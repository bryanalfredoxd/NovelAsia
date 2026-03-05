<?php
define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Mpdf\Mpdf;

// Aumentamos los límites porque generar PDFs de miles de páginas consume mucha memoria y tiempo
ini_set('memory_limit', '2048M'); 
set_time_limit(0); 

if (file_exists(BASE_PATH . '/.env')) {
    $dotenv = Dotenv::createImmutable(BASE_PATH);
    $dotenv->load();
} else {
    die("❌ No se encuentra el archivo .env\n");
}

class GeneradorPDFNovelas {
    private $pdo;
    private $pdfDir;
    private $parsedown;

    public function __construct() {
        $this->pdfDir = BASE_PATH . '/storage/pdfs';
        if (!is_dir($this->pdfDir)) {
            mkdir($this->pdfDir, 0755, true);
        }
        
        $this->conectarDB();
        $this->parsedown = new Parsedown();
    }

    private function conectarDB() {
        try {
            $host = $_ENV['DB_HOST'] ?? 'localhost';
            $database = $_ENV['DB_DATABASE'] ?? $_ENV['DB_NAME'] ?? 'novelas_db';
            $username = $_ENV['DB_USERNAME'] ?? $_ENV['DB_USER'] ?? 'root';
            $password = $_ENV['DB_PASSWORD'] ?? $_ENV['DB_PASS'] ?? '';
            $port = $_ENV['DB_PORT'] ?? '3306';
            
            $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
            
            $this->pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            echo "✓ Conexión BD exitosa.\n";
        } catch (PDOException $e) {
            die("❌ Error BD: " . $e->getMessage() . "\n");
        }
    }

    public function ejecutar() {
        echo "=== INICIANDO GENERADOR DE PDFs ===\n";
        
        // 1. Obtener todas las novelas que tengan al menos un capítulo traducido
        $sqlNovelas = "
            SELECT DISTINCT 
                n.id_novela, 
                n.titulo_original,
                nte.titulo_traducido
            FROM novelas n
            JOIN capitulos c ON n.id_novela = c.id_novela
            JOIN capitulos_traduccion_espanol cte ON c.id_capitulo = cte.id_capitulo
            LEFT JOIN novelas_traduccion_espanol nte ON n.id_novela = nte.id_novela
            WHERE cte.estado_traduccion = 'completado'
        ";
        
        $stmt = $this->pdo->query($sqlNovelas);
        $novelas = $stmt->fetchAll();
        
        if (empty($novelas)) {
            echo "No hay novelas con capítulos traducidos para generar.\n";
            return;
        }
        
        echo "📚 Encontradas " . count($novelas) . " novelas para procesar.\n\n";

        foreach ($novelas as $novela) {
            $this->generarPDFNovela($novela);
        }
        
        echo "\n=== PROCESO FINALIZADO ===\n";
    }

    private function generarPDFNovela($novela) {
        $idNovela = $novela['id_novela'];
        
        // Determinar qué título usar (Priorizar Español)
        if (!empty($novela['titulo_traducido'])) {
            $tituloMostrar = $novela['titulo_traducido'];
            echo "📄 Generando PDF para: {$tituloMostrar}\n";
        } else {
            $tituloMostrar = $novela['titulo_original'];
            echo "📄 Generando PDF para: {$tituloMostrar} (⚠️ Título en español no encontrado en DB)\n";
        }

        $nombreArchivo = $this->limpiarNombreArchivo($tituloMostrar);
        
        // 2. Obtener los capítulos en orden
        $sqlCapitulos = "
            SELECT 
                c.numero_capitulo,
                cte.titulo_traducido,
                cte.contenido_traducido
            FROM capitulos c
            JOIN capitulos_traduccion_espanol cte ON c.id_capitulo = cte.id_capitulo
            WHERE c.id_novela = ? AND cte.estado_traduccion = 'completado'
            ORDER BY c.orden_capitulo ASC, c.numero_capitulo ASC
        ";
        
        $stmt = $this->pdo->prepare($sqlCapitulos);
        $stmt->execute([$idNovela]);
        $capitulos = $stmt->fetchAll();

        if (empty($capitulos)) {
            echo "   ⚠️ Sin capítulos válidos. Saltando...\n";
            return;
        }

        // 3. Configurar mPDF con Times New Roman y márgenes
        $mpdf = new Mpdf([
            'default_font' => 'times',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 20,
            'margin_bottom' => 20,
        ]);

        // 4. Inyectar el CSS
        $css = "
            body { font-family: 'times', serif; }
            .capitulo-titulo { 
                font-size: 18pt; 
                font-weight: bold; 
                text-align: center; 
                margin-bottom: 30px; 
            }
            .capitulo-contenido { 
                font-size: 14pt; 
                text-align: justify; 
                line-height: 1.5; 
            }
            .capitulo-contenido p { margin-bottom: 15px; }
        ";
        $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);

        // 5. Portada
        $htmlPortada = "
            <div style='text-align: center; margin-top: 200px;'>
                <h1 style='font-size: 36pt;'>{$tituloMostrar}</h1>
                <p style='font-size: 16pt;'>Generado automáticamente</p>
                <p style='font-size: 14pt;'>Total de capítulos traducidos: " . count($capitulos) . "</p>
            </div>
        ";
        $mpdf->WriteHTML($htmlPortada, \Mpdf\HTMLParserMode::HTML_BODY);

        // 6. Recorrer e insertar cada capítulo
        foreach ($capitulos as $index => $cap) {
            // Salto de página para cada capítulo
            $mpdf->AddPage();
            
            // Título: Si no tiene título traducido, usa el genérico
            $tituloCapitulo = $cap['titulo_traducido'] ?: "Capítulo " . $cap['numero_capitulo'];
            
            // Contenido: Convertimos el Markdown a HTML
            $contenidoHtml = $this->parsedown->text($cap['contenido_traducido']);

            // Estructura HTML del capítulo
            $htmlCapitulo = "
                <div class='capitulo-titulo'>{$tituloCapitulo}</div>
                <div class='capitulo-contenido'>{$contenidoHtml}</div>
            ";
            
            $mpdf->WriteHTML($htmlCapitulo, \Mpdf\HTMLParserMode::HTML_BODY);
        }

        // 7. Guardar el archivo en disco
        $rutaArchivo = $this->pdfDir . '/' . $nombreArchivo . '.pdf';
        try {
            $mpdf->Output($rutaArchivo, \Mpdf\Output\Destination::FILE);
            echo "   ✅ ¡PDF guardado con éxito! (" . count($capitulos) . " capítulos)\n";
            echo "   📂 Ruta: {$rutaArchivo}\n\n";
        } catch (Exception $e) {
            echo "   ❌ Error al guardar PDF: " . $e->getMessage() . "\n";
        }
    }

    // Limpia caracteres extraños con el guion correctamente escapado (\-)
    private function limpiarNombreArchivo($cadena) {
        // Se agregó la barra invertida antes del guion: \-
        $cadena = preg_replace('/[^\p{L}\p{N}\s\-_]/u', '', $cadena);
        $cadena = preg_replace('/[\s\-]+/', '_', $cadena);
        $cadena = trim($cadena, '_');
        
        // Seguro adicional: Si la cadena quedó vacía tras la limpieza, se le da un nombre único
        if (empty($cadena)) {
            $cadena = 'Novela_Generada_' . time();
        }
        
        return $cadena;
    }
}

// Iniciar
$generador = new GeneradorPDFNovelas();
$generador->ejecutar();