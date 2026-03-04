<?php
define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/vendor/autoload.php';

use Dotenv\Dotenv;

if (file_exists(BASE_PATH . '/.env')) {
    $dotenv = Dotenv::createImmutable(BASE_PATH);
    $dotenv->load();
    echo "✓ .env cargado desde: " . BASE_PATH . "/.env\n";
} else {
    die("❌ No se encuentra el archivo .env en: " . BASE_PATH . "/.env\n");
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', BASE_PATH . '/storage/logs/multi_api_titulos_error.log');
set_time_limit(0); 

class MultiApiTraductorTitulosJob {
    
    private $pdo;
    private $apisDisponibles = [];
    private $apiActual = null;
    private $logFile;
    private $startTime;
    
    private $usoAPIs = [];
    private $apisEnEspera = []; 
    private $apisAgotadasHoy = []; 
    
    private $stats = [
        'procesados' => 0,
        'exitosos' => 0,
        'fallidos' => 0,
        'costo_total' => 0,
        'tokens_totales' => 0,
        'api_usada' => [],
        'tokens_por_api' => [],
        'cambios_api' => 0,
        'tiempo_espera_total' => 0
    ];
    
    // Limite de seguridad dinámico: Siempre usaremos máximo el 95% de la capacidad de la BD
    const UMBRAL_SEGURIDAD_PORCENTAJE = 95; 
    const TIEMPO_VENTANA_SEGUNDOS = 60;
    const TIEMPO_ESPERA_ENTRE_INTENTOS = 5;
    const COSTO_POR_TITULO = 0.001; 
    
    const PROMPT_TITULOS = <<<EOT
    Actúa como un traductor experto de novelas web chinas al español latinoamericano. Tu única tarea es traducir el TITULO de un capítulo.

    Aplica ESTRICTAMENTE las siguientes reglas:
    1. Formato: El título DEBE mantener el formato "Capítulo X: [Título traducido]".
    2. Localización: Adapta chistes, modismos, juegos de palabras o términos de cultivo al español latinoamericano para que suene épico o natural.
    3. NO agregues comillas extras, notas, explicaciones ni saludos. Responde ÚNICAMENTE con el título traducido.

    Título original a traducir:
    {TITULO}
    EOT;

    public function __construct() {
        $this->startTime = microtime(true);
        $this->logFile = BASE_PATH . '/storage/logs/traductor_titulos_' . date('Y-m-d') . '.log';
        
        $this->inicializarLog();
        $this->conectarDB();
        $this->cargarAPIsDisponibles();
        $this->inicializarSeguimientoUso();
    }
    
    private function inicializarLog() {
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $this->log("=== INICIO JOB TRADUCCIÓN TÍTULOS (LÍMITES DINÁMICOS AL " . self::UMBRAL_SEGURIDAD_PORCENTAJE . "%) ===");
        $this->log("Fecha: " . date('Y-m-d H:i:s'));
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
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
            
            $this->log("✓ Conexión BD exitosa");
            
        } catch (PDOException $e) {
            $this->log("✗ Error BD: " . $e->getMessage(), 'ERROR');
            die("Error BD: " . $e->getMessage() . "\n");
        }
    }
    
    private function cargarAPIsDisponibles() {
        try {
            $sql = "
                SELECT * FROM apis_traduccion 
                WHERE esta_activa = TRUE 
                  AND tipo_api = 'titulos' 
                ORDER BY modo_reserva ASC, prioridad ASC, tasa_error ASC
            ";
            
            $stmt = $this->pdo->query($sql);
            $apis = $stmt->fetchAll();
            
            if (empty($apis)) {
                $this->log("⚠️ No hay APIs activas configuradas con tipo_api = 'titulos'");
                return;
            }
            
            foreach ($apis as $api) {
                $api['api_key'] = $this->desencriptarAPIKey($api['api_key_encriptada'] ?? '');
                $api['config'] = json_decode($api['configuracion_chino'] ?? '{}', true) ?: [];
                $this->apisDisponibles[$api['id_api']] = $api;
                $this->stats['tokens_por_api'][$api['id_api']] = 0;
            }
            
            $this->log("✓ APIs de TÍTULOS cargadas: " . count($this->apisDisponibles));
            
        } catch (Exception $e) {
            $this->log("✗ Error cargando APIs: " . $e->getMessage(), 'ERROR');
            die("Error cargando APIs\n");
        }
    }
    
    // Calcula los límites diarios (Tokens y Requests) aplicando el 95%
    private function verificarLimiteDiario($api) {
        $tokensUsadosHoy = $api['tokens_usados_hoy'] ?? 0;
        $tokensPorDia = $api['tokens_por_dia'] ?? 0;
        $requestsHoy = $api['requests_hoy'] ?? 0;
        $requestsPorDia = $api['requests_por_dia'] ?? 0;
        
        // Verificar Tokens Diarios
        if ($tokensPorDia > 0) {
            $limiteTokensDiario = ($tokensPorDia * self::UMBRAL_SEGURIDAD_PORCENTAJE) / 100;
            if ($tokensUsadosHoy >= $limiteTokensDiario) {
                return false;
            }
        }
        
        // Verificar Requests Diarias
        if ($requestsPorDia > 0) {
            $limiteRequestsDiario = floor(($requestsPorDia * self::UMBRAL_SEGURIDAD_PORCENTAJE) / 100);
            if ($requestsHoy >= $limiteRequestsDiario) {
                return false;
            }
        }
        
        return true;
    }
    
    private function inicializarSeguimientoUso() {
        foreach ($this->apisDisponibles as $id => $api) {
            $this->usoAPIs[$id] = [
                'tokens_minuto' => 0,
                'requests_minuto' => 0,
                'ultimo_uso' => time(),
                'en_espera_hasta' => null
            ];
            
            if (!$this->verificarLimiteDiario($api)) {
                $this->apisAgotadasHoy[$id] = true;
                $this->log(" ⚠️ API {$api['nombre_api']} inicia AGOTADA por alcanzar el 95% de límite diario (Tokens o Requests).");
            }
        }
    }
    
    private function buscarMejorAPI() {
        $ahora = time();
        $apisDisponiblesAhora = [];
        
        // Liberar APIs cuyo tiempo de espera de 1 minuto haya expirado
        foreach ($this->apisEnEspera as $apiId => $esperaHasta) {
            if ($ahora >= $esperaHasta) {
                unset($this->apisEnEspera[$apiId]);
                $this->usoAPIs[$apiId]['tokens_minuto'] = 0; 
                $this->usoAPIs[$apiId]['requests_minuto'] = 0;
            }
        }
        
        foreach ($this->apisDisponibles as $apiId => $api) {
            if (isset($this->apisAgotadasHoy[$apiId]) || isset($this->apisEnEspera[$apiId])) continue;
            
            $uso = $this->usoAPIs[$apiId];
            
            // Límites por MINUTO calculados dinámicamente al 95%
            $maxTokensMinuto = ($api['tokens_por_minuto'] * self::UMBRAL_SEGURIDAD_PORCENTAJE) / 100;
            $maxReqMinuto = floor(($api['requests_por_minuto'] * self::UMBRAL_SEGURIDAD_PORCENTAJE) / 100);
            
            // Dejamos un margen extra de 150 tokens para no chocar justo en el límite en esta petición
            if ($uso['tokens_minuto'] < ($maxTokensMinuto - 150) && $uso['requests_minuto'] < max(1, $maxReqMinuto)) {
                $apisDisponiblesAhora[$apiId] = $uso['requests_minuto'];
            }
        }
        
        if (!empty($apisDisponiblesAhora)) {
            // Prioriza la API que menos requests haya hecho en este minuto
            asort($apisDisponiblesAhora);
            $mejorAPIId = array_key_first($apisDisponiblesAhora);
            $this->apiActual = $this->apisDisponibles[$mejorAPIId];
            return true;
        }
        
        return false;
    }
    
    private function registrarUsoAPI($apiId, $tokensUsados) {
        $ahora = time();
        $this->usoAPIs[$apiId]['tokens_minuto'] += $tokensUsados;
        $this->usoAPIs[$apiId]['requests_minuto']++;
        $this->usoAPIs[$apiId]['ultimo_uso'] = $ahora;
        
        $api = $this->apisDisponibles[$apiId];
        $maxTokensMinuto = ($api['tokens_por_minuto'] * self::UMBRAL_SEGURIDAD_PORCENTAJE) / 100;
        $maxReqMinuto = floor(($api['requests_por_minuto'] * self::UMBRAL_SEGURIDAD_PORCENTAJE) / 100);
        
        if ($this->usoAPIs[$apiId]['tokens_minuto'] >= ($maxTokensMinuto - 150) || 
            $this->usoAPIs[$apiId]['requests_minuto'] >= max(1, $maxReqMinuto)) {
            
            $this->apisEnEspera[$apiId] = $ahora + self::TIEMPO_VENTANA_SEGUNDOS;
            return true; // Entra en cooldown de 1 minuto
        }
        return false;
    }
    
    public function ejecutar() {
        $this->log("\n=== INICIANDO PROCESAMIENTO DE TÍTULOS ===");
        
        if (empty($this->apisDisponibles)) {
            $this->log("✗ No se encontraron APIs. El script se detendrá.");
            return;
        }
        
        $titulosPendientes = $this->obtenerTitulosPendientes();
        $totalTitulos = count($titulosPendientes);
        
        if ($totalTitulos == 0) {
            $this->log("✓ No hay títulos pendientes por traducir.");
            return;
        }
        
        $this->log("📚 Títulos a procesar: " . $totalTitulos);
        $titulosProcesados = 0;
        $intentosSinAPI = 0;
        
        while ($titulosProcesados < $totalTitulos) {
            
            // Verificamos si TODAS las APIs están agotadas DIARIAMENTE
            if (count($this->apisAgotadasHoy) === count($this->apisDisponibles)) {
                $this->log("\n⚠️ TODAS LAS APIs ALCANZARON SU LÍMITE DIARIO (95%). El job se detiene hasta mañana.");
                break;
            }

            if (!$this->buscarMejorAPI()) {
                $intentosSinAPI++;
                $tiempoEspera = min(self::TIEMPO_ESPERA_ENTRE_INTENTOS * $intentosSinAPI, 30);
                $this->log("⏳ APIs en espera por límite de minuto/saturación. Esperando {$tiempoEspera}s...");
                sleep($tiempoEspera);
                continue;
            }
            
            $intentosSinAPI = 0;
            $capitulo = $titulosPendientes[$titulosProcesados];
            
            $this->log("\n--- Título " . ($titulosProcesados + 1) . "/{$totalTitulos} ---");
            $this->log(" Original: {$capitulo['titulo_original']}");
            $this->log(" IA Asignada: {$this->apiActual['nombre_api']}");
            
            $resultado = $this->traducirTitulo($capitulo);
            
            if ($resultado['exito']) {
                $this->stats['procesados']++;
                $titulosProcesados++; 
                $this->stats['exitosos']++;
                $this->stats['tokens_totales'] += $resultado['tokens_usados'];
                $this->stats['costo_total'] += self::COSTO_POR_TITULO;
                
                $apiId = $this->apiActual['id_api'];
                
                // Actualizar uso en memoria (Minuto y Diario)
                $this->registrarUsoAPI($apiId, $resultado['tokens_usados']);
                $this->apisDisponibles[$apiId]['tokens_usados_hoy'] += $resultado['tokens_usados'];
                $this->apisDisponibles[$apiId]['requests_hoy'] += 1;
                
                $this->stats['api_usada'][$apiId] = ($this->stats['api_usada'][$apiId] ?? 0) + 1;
                $this->stats['tokens_por_api'][$apiId] += $resultado['tokens_usados'];
                
                $this->log(" ✅ Traducido: " . $resultado['titulo_traducido']);
                
                // Verificación de seguridad: ¿Llegó al 95% del límite diario tras esta petición?
                if (!$this->verificarLimiteDiario($this->apisDisponibles[$apiId])) {
                    $this->apisAgotadasHoy[$apiId] = true;
                    $this->log(" 🛑 API {$this->apiActual['nombre_api']} acaba de alcanzar el 95% de su límite DIARIO. Desactivada por hoy.");
                }
                
                usleep(300000); 
                
            } else {
                if (!empty($resultado['saturada'])) {
                    $this->log(" ⚠️ IA Saturada ({$resultado['mensaje']}). Pasando a la siguiente IA...");
                    $apiId = $this->apiActual['id_api'];
                    $this->apisEnEspera[$apiId] = time() + 60; // Penalización por 60 segundos
                    $this->stats['cambios_api']++;
                } else {
                    $this->stats['procesados']++;
                    $titulosProcesados++; 
                    $this->stats['fallidos']++;
                    $this->log(" ❌ Error crítico: " . $resultado['mensaje'], 'ERROR');
                }
            }
        }
        
        $this->mostrarResumen();
    }
    
    private function obtenerTitulosPendientes() {
        try {
            $sql = "
                SELECT 
                    c.id_capitulo,
                    c.id_novela,
                    c.numero_capitulo,
                    c.titulo_original,
                    cte.id_traduccion_capitulo_es,
                    cte.titulo_traducido,
                    nte.id_traduccion_novela_es
                FROM capitulos c
                LEFT JOIN novelas_traduccion_espanol nte ON c.id_novela = nte.id_novela
                LEFT JOIN capitulos_traduccion_espanol cte ON c.id_capitulo = cte.id_capitulo 
                    AND cte.version_traduccion = (
                        SELECT MAX(version_traduccion) 
                        FROM capitulos_traduccion_espanol 
                        WHERE id_capitulo = c.id_capitulo
                    )
                WHERE 
                    c.titulo_original IS NOT NULL 
                    AND TRIM(c.titulo_original) != ''
                    AND (cte.titulo_traducido IS NULL OR TRIM(cte.titulo_traducido) = '')
                ORDER BY 
                    c.id_novela ASC, 
                    c.numero_capitulo ASC
            ";
            
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            $this->log("✗ Error obteniendo títulos pendientes: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }
    
    private function traducirTitulo($capitulo) {
        $resultado = [
            'exito' => false,
            'titulo_traducido' => '',
            'tokens_usados' => 0,
            'mensaje' => '',
            'saturada' => false
        ];
        
        try {
            $prompt = str_replace('{TITULO}', $capitulo['titulo_original'], self::PROMPT_TITULOS);
            $respuesta = $this->llamarGROQ($prompt);
            
            if (!$respuesta['exito']) {
                $resultado['saturada'] = $respuesta['saturada'] ?? false;
                $resultado['mensaje'] = $respuesta['error'];
                return $resultado;
            }
            
            // Mantenemos la lógica por si en un futuro decides meter un modelo Reasoning (Qwen/DeepSeek)
            $tituloTraducido = preg_replace('/<think>.*?<\/think>\s*/is', '', $respuesta['traduccion']);
            $tituloTraducido = trim($tituloTraducido);
            
            $this->guardarTituloTraducido($capitulo, $tituloTraducido, $respuesta['tokens_usados']);
            
            $resultado['exito'] = true;
            $resultado['titulo_traducido'] = $tituloTraducido;
            $resultado['tokens_usados'] = $respuesta['tokens_usados'];
            
        } catch (Exception $e) {
            $resultado['mensaje'] = "Excepción: " . $e->getMessage();
        }
        
        return $resultado;
    }
    
    private function llamarGROQ($prompt) {
        $url = $this->apiActual['endpoint_url'] ?: 'https://api.groq.com/openai/v1/chat/completions';
        
        $data = [
            'model' => $this->apiActual['modelo'],
            'messages' => [
                ['role' => 'system', 'content' => 'Eres un traductor experto en novelas web chinas. Tu única tarea es traducir títulos de capítulos al español latino. NUNCA converses, NUNCA agregues notas, NO uses etiquetas <think>. Responde EXCLUSIVAMENTE con la línea del título traducido.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => $this->apiActual['config']['temperatura'] ?? 0.2,
            'max_tokens' => 150, // Lo volvemos a bajar a 150 ya que vas a usar Llama
            'top_p' => 0.9
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiActual['api_key']
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($httpCode === 503 || $httpCode === 429 || $httpCode >= 500) {
            return [
                'exito' => false, 
                'saturada' => true, 
                'error' => "HTTP {$httpCode} - Modelo saturado, rate limit o caído."
            ];
        }
        
        if ($httpCode !== 200) {
            return ['exito' => false, 'saturada' => false, 'error' => "HTTP {$httpCode} - " . ($curlError ?: $response)];
        }
        
        $result = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['exito' => false, 'saturada' => false, 'error' => 'Error decodificando JSON'];
        }

        return [
            'exito' => true,
            'traduccion' => $result['choices'][0]['message']['content'] ?? '',
            'tokens_usados' => $result['usage']['total_tokens'] ?? 0
        ];
    }
    
    private function guardarTituloTraducido($capitulo, $tituloTraducido, $tokens) {
        try {
            $this->pdo->beginTransaction();
            
            if (empty($capitulo['id_traduccion_novela_es'])) {
                $stmt = $this->pdo->prepare("INSERT INTO novelas_traduccion_espanol (id_novela, estado_traduccion, tokens) VALUES (?, 'en_progreso', 0)");
                $stmt->execute([$capitulo['id_novela']]);
                $idTradNovela = $this->pdo->lastInsertId();
            } else {
                $idTradNovela = $capitulo['id_traduccion_novela_es'];
            }
            
            if (!empty($capitulo['id_traduccion_capitulo_es'])) {
                $stmt = $this->pdo->prepare("
                    UPDATE capitulos_traduccion_espanol 
                    SET titulo_traducido = ?, tokens = tokens + ?
                    WHERE id_traduccion_capitulo_es = ?
                ");
                $stmt->execute([$tituloTraducido, $tokens, $capitulo['id_traduccion_capitulo_es']]);
            } else {
                $stmt = $this->pdo->prepare("
                    INSERT INTO capitulos_traduccion_espanol (
                        id_capitulo, id_traduccion_novela_es, titulo_traducido, 
                        estado_traduccion, fecha_traduccion, tokens, traductor_ia
                    ) VALUES (?, ?, ?, 'pendiente', NOW(), ?, ?)
                ");
                $stmt->execute([
                    $capitulo['id_capitulo'], 
                    $idTradNovela, 
                    $tituloTraducido, 
                    $tokens, 
                    $this->apiActual['nombre_api']
                ]);
            }
            
            $stmt = $this->pdo->prepare("
                UPDATE apis_traduccion 
                SET tokens_usados_hoy = tokens_usados_hoy + ?, requests_hoy = requests_hoy + 1, ultimo_uso = NOW()
                WHERE id_api = ?
            ");
            $stmt->execute([$tokens, $this->apiActual['id_api']]);
            
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
    
    private function mostrarResumen() {
        $tiempoTotal = round(microtime(true) - $this->startTime, 2);
        
        $this->log("\n" . str_repeat("=", 60));
        $this->log("📊 RESUMEN FINAL TRADUCCIÓN TÍTULOS");
        $this->log(str_repeat("=", 60));
        $this->log("📈 Procesados: {$this->stats['procesados']}");
        $this->log("✅ Exitosos: {$this->stats['exitosos']}");
        $this->log("❌ Fallidos: {$this->stats['fallidos']}");
        $this->log("🔄 Cambios por Saturación: {$this->stats['cambios_api']}");
        $this->log("⏱️ Tiempo total: {$tiempoTotal}s");
    }
    
    private function desencriptarAPIKey($keyEncriptada) {
        if (empty($keyEncriptada)) return '';
        if (preg_match('/^\{\{(.+)\}\}$/', $keyEncriptada, $matches)) return $_ENV[$matches[1]] ?? '';
        $decoded = base64_decode($keyEncriptada, true);
        return $decoded !== false ? $decoded : $keyEncriptada;
    }
    
    private function log($mensaje, $nivel = 'INFO') {
        $timestamp = date('Y-m-d H:i:s');
        $linea = "[{$timestamp}] [{$nivel}] {$mensaje}\n";
        echo $mensaje . "\n";
        file_put_contents($this->logFile, $linea, FILE_APPEND);
    }
}

try {
    echo "\n🚀 Iniciando traductor EXCLUSIVO DE TÍTULOS...\n\n";
    $job = new MultiApiTraductorTitulosJob();
    $job->ejecutar();
    
} catch (Exception $e) {
    echo "❌ Error fatal: " . $e->getMessage() . "\n";
    file_put_contents(
        BASE_PATH . '/storage/logs/fatal_error_titulos.log',
        "[" . date('Y-m-d H:i:s') . "] " . $e->getMessage() . "\n",
        FILE_APPEND
    );
    exit(1);
}
exit(0);