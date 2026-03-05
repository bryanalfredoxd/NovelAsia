<?php
define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Silenciamos la carga del .env, si falla lo anota en un log de emergencia
if (file_exists(BASE_PATH . '/.env')) {
    $dotenv = Dotenv::createImmutable(BASE_PATH);
    $dotenv->load();
} else {
    $logDir = BASE_PATH . '/logs';
    if (!is_dir($logDir)) mkdir($logDir, 0755, true);
    file_put_contents($logDir . '/emergencia.log', "[" . date('Y-m-d H:i:s') . "] ❌ FATAL: No se encuentra el archivo .env\n", FILE_APPEND);
    die();
}

error_reporting(E_ALL);
ini_set('display_errors', 0); // Desactivar salida de errores en pantalla
ini_set('log_errors', 1);
ini_set('error_log', BASE_PATH . '/logs/php_errores_internos.log'); // Redirigido a la nueva carpeta
set_time_limit(0); // Sin límite de tiempo

class MultiApiTraductorJob {
    
    private $pdo;
    private $apisDisponibles = [];
    private $apiActual = null;
    private $logFile;
    private $startTime;
    
    // Seguimiento de uso por API
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
    
    // Límites Dinámicos
    const UMBRAL_SEGURIDAD_PORCENTAJE = 95; 
    const TIEMPO_VENTANA_SEGUNDOS = 60; 
    const TIEMPO_ESPERA_ENTRE_INTENTOS = 5; 
    const COSTO_POR_CAPITULO = 0.01; 
    
    const PROMPT_BASE = <<<EOT
    Actúa como un traductor experto de novelas web chinas al español latinoamericano. Tu única tarea es traducir el CONTENIDO de este capítulo de forma completa, sin omitir ni resumir absolutamente nada, sin importar lo largo que sea.

    Aplica ESTRICTAMENTE las siguientes reglas en tu traducción:
    1. Estilo: Usa cursiva exclusivamente para los pensamientos internos de los personajes. Mantén una estructura de párrafos limpia y espaciada. NO INCLUYAS NINGÚN TÍTULO, comienza directamente con el texto.
    2. Niveles de Cultivo y Conceptos: Evita traducciones directas del pinyin sin contexto. Traduce o adapta el nombre para que tenga sentido épico usando el formato: [Nombre en español latino] (ej. [Reino de la Forja Ósea]).
    3. Juegos de Palabras y Modismos: Adapta modismos pensando en un lector de América Latina. Si un chiste pierde su sentido, usa el formato: [juego de palabras traducido] (significado real).
    4. Nombres de Equipos/Facciones: Evita traducciones literales torpes. Adapta los nombres para que suenen naturales y épicos, o mantenlos en Pinyin estilizado.
    5. Unidades de Medida y Tiempo (¡MUY IMPORTANTE!): Convierte SIEMPRE las unidades tradicionales chinas al sistema métrico decimal y medidas de tiempo estándar de manera invisible para el lector. (Ej: "Li" a metros/kilómetros, "Jin" a kilogramos, "tiempo de una varilla de incienso" a minutos).
    6. Limpieza de Spam: IGNORA y ELIMINA por completo cualquier texto de spam, anuncios o URLs insertadas (ej. "GOOGLE搜索TWKAN").
    7. Traducción Completa: DEBES traducir el texto desde la primera palabra hasta la última. NO resumas, NO omitas párrafos, NO cortes el final. Es vital que el capítulo esté 100% completo.
    8. Notas del Autor: Si el autor deja un mensaje al final, tradúcelo separándolo visualmente así:
    ---
    **Mensaje del Autor:** [Traducción del mensaje]

    Texto original a traducir (SOLO CONTENIDO):
    {CONTENIDO}

    IMPORTANTE: Responde ÚNICAMENTE con el texto traducido del contenido. NO incluyas saludos, NO agregues el título del capítulo, NO uses etiquetas <think>.
    EOT;

    public function __construct() {
        $this->startTime = microtime(true);
        // Nueva ruta y formato de nombre de archivo
        $this->logFile = BASE_PATH . '/logs/Traducciones_capitulo_' . date('Y-m-d') . '.log';
        
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
        
        $this->log(str_repeat("=", 80));
        $this->log("🚀 INICIO JOB TRADUCCIÓN CONTENIDO (SILENCIOSO & FAILOVER)");
        $this->log("Fecha de ejecución: " . date('Y-m-d H:i:s'));
        $this->log(str_repeat("=", 80));
    }
    
    private function conectarDB() {
        try {
            $this->log("⏳ Conectando a la base de datos...");
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
            
            $this->log("✓ Conexión BD exitosa.");
            
        } catch (PDOException $e) {
            $this->log("✗ Error BD: " . $e->getMessage(), 'ERROR');
            die();
        }
    }
    
    private function cargarAPIsDisponibles() {
        try {
            $this->log("⏳ Cargando configuración de APIs (tipo_api = 'traduccion')...");
            $sql = "
                SELECT * FROM apis_traduccion 
                WHERE esta_activa = TRUE
                  AND tipo_api = 'traduccion'
                ORDER BY modo_reserva ASC, prioridad ASC, tasa_error ASC
            ";
            
            $stmt = $this->pdo->query($sql);
            $apis = $stmt->fetchAll();
            
            if (empty($apis)) {
                $this->log("⚠️ No hay APIs activas configuradas. Terminando ejecución.", "WARNING");
                return;
            }
            
            foreach ($apis as $api) {
                $api['api_key'] = $this->desencriptarAPIKey($api['api_key_encriptada']);
                unset($api['api_key_encriptada']);
                $api['config'] = json_decode($api['configuracion_chino'] ?? '{}', true) ?: [];
                $this->verificarResetAPI($api);
                
                $this->apisDisponibles[$api['id_api']] = $api;
                $this->stats['tokens_por_api'][$api['id_api']] = 0;
            }
            
            $this->log("✓ APIs cargadas exitosamente: " . count($this->apisDisponibles));
            
        } catch (Exception $e) {
            $this->log("✗ Error cargando APIs: " . $e->getMessage(), 'ERROR');
            die();
        }
    }
    
    private function verificarLimiteDiario($api) {
        $tokensUsadosHoy = $api['tokens_usados_hoy'] ?? 0;
        $tokensPorDia = $api['tokens_por_dia'] ?? 0;
        $requestsHoy = $api['requests_hoy'] ?? 0;
        $requestsPorDia = $api['requests_por_dia'] ?? 0;
        
        if ($tokensPorDia > 0) {
            $limiteTokensDiario = ($tokensPorDia * self::UMBRAL_SEGURIDAD_PORCENTAJE) / 100;
            if ($tokensUsadosHoy >= $limiteTokensDiario) return false;
        }
        
        if ($requestsPorDia > 0) {
            $limiteRequestsDiario = floor(($requestsPorDia * self::UMBRAL_SEGURIDAD_PORCENTAJE) / 100);
            if ($requestsHoy >= $limiteRequestsDiario) return false;
        }
        
        return true;
    }
    
    private function inicializarSeguimientoUso() {
        $this->log("⏳ Inicializando seguimiento de Rate Limits...");
        foreach ($this->apisDisponibles as $id => $api) {
            $this->usoAPIs[$id] = [
                'tokens_minuto' => 0,
                'requests_minuto' => 0,
                'ultimo_uso' => time(),
                'en_espera_hasta' => null
            ];
            
            if (!$this->verificarLimiteDiario($api)) {
                $this->apisAgotadasHoy[$id] = true;
                $this->log(" ⚠️ API {$api['nombre_api']} inicia AGOTADA por límite diario (95%).");
            }
        }
    }
    
    private function verificarResetAPI(&$api) {
        try {
            $stmt = $this->pdo->prepare("SELECT ultimo_reset, DATE(ultimo_reset) as fecha_reset FROM apis_traduccion WHERE id_api = ?");
            $stmt->execute([$api['id_api']]);
            $reset = $stmt->fetch();
            
            if ($reset && $reset['fecha_reset'] < date('Y-m-d')) {
                $stmt = $this->pdo->prepare("
                    UPDATE apis_traduccion 
                    SET tokens_usados_hoy = 0, requests_hoy = 0, ultimo_reset = NOW()
                    WHERE id_api = ?
                ");
                $stmt->execute([$api['id_api']]);
                
                $api['tokens_usados_hoy'] = 0;
                $api['requests_hoy'] = 0;
                $this->log(" ↻ Reset diario aplicado para API {$api['nombre_api']}");
            }
        } catch (PDOException $e) { }
    }
    
    private function buscarMejorAPI() {
        $ahora = time();
        $apisDisponiblesAhora = [];
        
        foreach ($this->apisEnEspera as $apiId => $esperaHasta) {
            if ($ahora >= $esperaHasta) {
                unset($this->apisEnEspera[$apiId]);
                $this->usoAPIs[$apiId]['en_espera_hasta'] = null;
                $this->usoAPIs[$apiId]['tokens_minuto'] = 0; 
                $this->usoAPIs[$apiId]['requests_minuto'] = 0;
                $this->log(" 🔄 API {$this->apisDisponibles[$apiId]['nombre_api']} sale de espera y está disponible.");
            }
        }
        
        foreach ($this->apisDisponibles as $apiId => $api) {
            if (isset($this->apisAgotadasHoy[$apiId]) || isset($this->apisEnEspera[$apiId])) continue;
            
            $uso = $this->usoAPIs[$apiId];
            
            $tokensRestantes = $api['tokens_por_minuto'] - $uso['tokens_minuto'];
            $limiteSeguroMinuto = $api['tokens_por_minuto'] * 0.50; 
            
            $maxReqMinuto = floor(($api['requests_por_minuto'] * self::UMBRAL_SEGURIDAD_PORCENTAJE) / 100);
            
            if ($tokensRestantes >= $limiteSeguroMinuto && $uso['requests_minuto'] < max(1, $maxReqMinuto)) {
                $apisDisponiblesAhora[$apiId] = $uso['requests_minuto'];
            }
        }
        
        if (!empty($apisDisponiblesAhora)) {
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
        
        $tokensRestantes = $api['tokens_por_minuto'] - $this->usoAPIs[$apiId]['tokens_minuto'];
        $limiteSeguroMinuto = $api['tokens_por_minuto'] * 0.50;
        $maxReqMinuto = floor(($api['requests_por_minuto'] * self::UMBRAL_SEGURIDAD_PORCENTAJE) / 100);
        
        if ($tokensRestantes < $limiteSeguroMinuto || $this->usoAPIs[$apiId]['requests_minuto'] >= max(1, $maxReqMinuto)) {
            $this->apisEnEspera[$apiId] = $ahora + self::TIEMPO_VENTANA_SEGUNDOS;
            return true;
        }
        return false;
    }
    
    public function ejecutar() {
        if (empty($this->apisDisponibles)) return;
        
        $capitulos = $this->obtenerCapitulosPendientes();
        $totalCapitulos = count($capitulos);
        
        if ($totalCapitulos == 0) {
            $this->log("✓ No hay capítulos pendientes por traducir. Finalizando.");
            return;
        }
        
        $this->log("📚 Total de capítulos en cola: " . $totalCapitulos);
        $capitulosProcesados = 0;
        $intentosSinAPI = 0;
        
        while ($capitulosProcesados < $totalCapitulos) {
            
            if (count($this->apisAgotadasHoy) === count($this->apisDisponibles)) {
                $this->log("🛑 TODAS LAS APIs ALCANZARON SU LÍMITE DIARIO. Deteniendo job hasta mañana.", "CRITICAL");
                break;
            }

            if (!$this->buscarMejorAPI()) {
                $intentosSinAPI++;
                $tiempoEspera = min(self::TIEMPO_ESPERA_ENTRE_INTENTOS * $intentosSinAPI, 30);
                $this->log("⏳ APIs en cooldown. Esperando {$tiempoEspera} segundos...");
                $this->stats['tiempo_espera_total'] += $tiempoEspera;
                sleep($tiempoEspera);
                continue;
            }
            
            $intentosSinAPI = 0;
            $capitulo = $capitulos[$capitulosProcesados];
            
            $this->log(str_repeat("-", 50));
            $this->log("📖 Procesando " . ($capitulosProcesados + 1) . "/{$totalCapitulos} | Novela: {$capitulo['titulo_novela_es']} | Cap: {$capitulo['numero_capitulo']}");
            $this->log("🤖 Asignado a: {$this->apiActual['nombre_api']}");
            
            $resultado = $this->traducirCapitulo($capitulo);
            
            if ($resultado['exito']) {
                $this->stats['procesados']++;
                $capitulosProcesados++;
                $this->stats['exitosos']++;
                $this->stats['tokens_totales'] += $resultado['tokens_usados'];
                $this->stats['costo_total'] += self::COSTO_POR_CAPITULO;
                
                $apiId = $this->apiActual['id_api'];
                
                $this->registrarUsoAPI($apiId, $resultado['tokens_usados']);
                $this->apisDisponibles[$apiId]['tokens_usados_hoy'] += $resultado['tokens_usados'];
                $this->apisDisponibles[$apiId]['requests_hoy'] += 1;
                
                $this->stats['api_usada'][$apiId] = ($this->stats['api_usada'][$apiId] ?? 0) + 1;
                $this->stats['tokens_por_api'][$apiId] += $resultado['tokens_usados'];
                
                $this->log("✅ ÉXITO | Tokens: {$resultado['tokens_usados']} | Costo: \$" . self::COSTO_POR_CAPITULO);
                
                if (!$this->verificarLimiteDiario($this->apisDisponibles[$apiId])) {
                    $this->apisAgotadasHoy[$apiId] = true;
                    $this->log("🛑 API {$this->apiActual['nombre_api']} superó el 95% diario. Apagada por hoy.", "WARNING");
                }
                
                usleep(500000); 
                
            } else {
                if (!empty($resultado['saturada'])) {
                    $this->log("⚠️ Saturación/Timeout (HTTP {$resultado['mensaje']}). Penalizando IA por 60s...", "WARNING");
                    $apiId = $this->apiActual['id_api'];
                    $this->apisEnEspera[$apiId] = time() + 60; 
                    $this->stats['cambios_api']++;
                } else {
                    $this->stats['procesados']++;
                    $capitulosProcesados++;
                    $this->stats['fallidos']++;
                    $this->incrementarTasaError($this->apiActual['id_api']);
                    $this->log("❌ ERROR CRÍTICO: " . $resultado['mensaje'], 'ERROR');
                }
            }
        }
        
        $this->mostrarResumen();
    }
    
    private function obtenerCapitulosPendientes() {
        try {
            $this->log("⏳ Consultando base de datos por capítulos...");
            $sql = "
                SELECT 
                    c.id_capitulo,
                    c.id_novela,
                    c.numero_capitulo,
                    c.titulo_original,
                    c.contenido_original,
                    c.palabras_original,
                    n.titulo_original as titulo_novela,
                    COALESCE(nte.titulo_traducido, n.titulo_original) as titulo_novela_es,
                    cte.id_traduccion_capitulo_es,
                    cte.estado_traduccion,
                    n.total_vistas,
                    nte.id_traduccion_novela_es
                FROM capitulos c
                JOIN novelas n ON c.id_novela = n.id_novela
                LEFT JOIN novelas_traduccion_espanol nte ON n.id_novela = nte.id_novela
                LEFT JOIN capitulos_traduccion_espanol cte ON c.id_capitulo = cte.id_capitulo 
                    AND cte.version_traduccion = (
                        SELECT MAX(version_traduccion) 
                        FROM capitulos_traduccion_espanol 
                        WHERE id_capitulo = c.id_capitulo
                    )
                WHERE 
                    c.estado_capitulo = 'disponible'
                    AND c.contenido_original IS NOT NULL
                    AND LENGTH(TRIM(c.contenido_original)) > 0
                    AND (
                        cte.id_traduccion_capitulo_es IS NULL 
                        OR cte.estado_traduccion IN ('pendiente', 'error')
                        OR cte.contenido_traducido IS NULL 
                        OR TRIM(cte.contenido_traducido) = ''
                    )
                ORDER BY 
                    n.total_vistas DESC,
                    c.id_novela ASC,
                    c.orden_capitulo ASC
            ";
            
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            $this->log("✗ Error SQL obteniendo capítulos: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }
    
    private function traducirCapitulo($capitulo) {
        $resultado = [
            'exito' => false,
            'tokens_usados' => 0,
            'costo' => 0,
            'mensaje' => '',
            'saturada' => false
        ];
        
        try {
            $prompt = str_replace('{CONTENIDO}', $capitulo['contenido_original'], self::PROMPT_BASE);
            $this->log(" 📡 Iniciando solicitud CURL a Groq...");
            $respuesta = $this->llamarGROQ($prompt);
            
            if (!$respuesta['exito']) {
                $resultado['saturada'] = $respuesta['saturada'] ?? false;
                throw new Exception($respuesta['error']);
            }
            
            $this->log(" 🧹 Limpiando y formateando respuesta de la IA...");
            $contenidoTraducido = preg_replace('/<think>.*?<\/think>\s*/is', '', $respuesta['traduccion']);
            $contenidoTraducido = trim($contenidoTraducido);
            
            if (empty($contenidoTraducido)) {
                throw new Exception("La IA devolvió un texto vacío tras la limpieza.");
            }
            
            $this->log(" 💾 Ejecutando guardado en Base de Datos...");
            $this->guardarTraduccion($capitulo, $contenidoTraducido, $respuesta['tokens_usados']);
            $this->marcarCapituloTraducido($capitulo['id_capitulo']);
            $this->actualizarEstadisticasNovela($capitulo['id_novela']);
            
            $costo = self::COSTO_POR_CAPITULO;
            $this->registrarLogAPI($capitulo, $respuesta, $costo);
            
            $resultado['exito'] = true;
            $resultado['tokens_usados'] = $respuesta['tokens_usados'];
            $resultado['costo'] = $costo;
            
        } catch (Exception $e) {
            if (!$resultado['saturada']) {
                $this->registrarLogAPI($capitulo, ['exito' => false], 0, $e->getMessage());
                $this->marcarErrorCapitulo($capitulo);
            }
            $resultado['mensaje'] = $e->getMessage();
        }
        
        return $resultado;
    }
    
    private function llamarGROQ($prompt) {
        $url = $this->apiActual['endpoint_url'] ?: 'https://api.groq.com/openai/v1/chat/completions';
        
        $data = [
            'model' => $this->apiActual['modelo'],
            'messages' => [
                ['role' => 'system', 'content' => 'Eres un traductor profesional de novelas web chinas. Tu única tarea es traducir el texto al español latino aplicando reglas de formato y conversión de medidas. NUNCA converses, NUNCA devuelvas el título del capítulo, NO uses etiquetas <think>. Responde EXCLUSIVAMENTE con la novela traducida completa sin resumir.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => $this->apiActual['config']['temperatura'] ?? 0.2,
            'max_tokens' => $this->apiActual['config']['max_tokens_completion'] ?? 8000,
            'top_p' => $this->apiActual['config']['top_p'] ?? 0.9
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiActual['api_key']
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300); // 5 MINUTOS DE TIMEOUT
        
        $timeStart = microtime(true);
        $response = curl_exec($ch);
        $timeEnd = microtime(true);
        $duration = round($timeEnd - $timeStart, 2);
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        $this->log(" 🏁 Respuesta de Groq recibida en {$duration} segundos (HTTP {$httpCode})");
        
        if ($httpCode === 503 || $httpCode === 429 || $httpCode >= 500 || $curlError == 'Operation timed out') {
            return [
                'exito' => false, 
                'saturada' => true, 
                'error' => "HTTP {$httpCode} - Modelo saturado o timeout de red. ({$curlError})"
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
            'tokens_usados' => $result['usage']['total_tokens'] ?? 0,
            'tokens_input' => $result['usage']['prompt_tokens'] ?? 0,
            'tokens_output' => $result['usage']['completion_tokens'] ?? 0
        ];
    }
    
    private function guardarTraduccion($capitulo, $contenidoTraducido, $tokens) {
        try {
            $this->pdo->beginTransaction();
            
            if (empty($capitulo['id_traduccion_novela_es'])) {
                $stmt = $this->pdo->prepare("INSERT INTO novelas_traduccion_espanol (id_novela, estado_traduccion, tokens) VALUES (?, 'en_progreso', 0)");
                $stmt->execute([$capitulo['id_novela']]);
                $idTradNovela = $this->pdo->lastInsertId();
            } else {
                $idTradNovela = $capitulo['id_traduccion_novela_es'];
            }
            
            $palabras = str_word_count($contenidoTraducido, 0, 'áéíóúñü');
            $hash = hash('sha256', $contenidoTraducido);
            $tiempo = (int)(microtime(true) - $this->startTime);
            
            $stmt = $this->pdo->prepare("
                SELECT id_traduccion_capitulo_es, version_traduccion 
                FROM capitulos_traduccion_espanol 
                WHERE id_capitulo = ? ORDER BY version_traduccion DESC LIMIT 1
            ");
            $stmt->execute([$capitulo['id_capitulo']]);
            $existente = $stmt->fetch();
            
            if ($existente) {
                $stmt = $this->pdo->prepare("
                    UPDATE capitulos_traduccion_espanol SET
                        contenido_traducido = ?, estado_traduccion = 'completado', traductor_ia = ?,
                        calidad_estimada = 'alta', palabras_traducidas = ?, tiempo_traduccion_segundos = ?,
                        costo_traduccion = ?, tokens = tokens + ?, hash_traduccion = ?, fecha_traduccion = NOW(),
                        version_traduccion = version_traduccion + 1
                    WHERE id_traduccion_capitulo_es = ?
                ");
                $stmt->execute([
                    $contenidoTraducido, $this->apiActual['nombre_api'], $palabras, $tiempo,
                    self::COSTO_POR_CAPITULO, $tokens, $hash, $existente['id_traduccion_capitulo_es']
                ]);
            } else {
                $stmt = $this->pdo->prepare("
                    INSERT INTO capitulos_traduccion_espanol (
                        id_capitulo, id_traduccion_novela_es, contenido_traducido, estado_traduccion, 
                        traductor_ia, calidad_estimada, palabras_traducidas, tiempo_traduccion_segundos,
                        costo_traduccion, tokens, hash_traduccion, fecha_traduccion
                    ) VALUES (?, ?, ?, 'completado', ?, 'alta', ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $capitulo['id_capitulo'], $idTradNovela, $contenidoTraducido, $this->apiActual['nombre_api'],
                    $palabras, $tiempo, self::COSTO_POR_CAPITULO, $tokens, $hash
                ]);
            }
            
            $this->pdo->commit();
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
    
    private function marcarCapituloTraducido($idCapitulo) {
        try {
            $stmt = $this->pdo->prepare("UPDATE capitulos SET enviado_traduccion = TRUE WHERE id_capitulo = ?");
            $stmt->execute([$idCapitulo]);
        } catch (Exception $e) { }
    }
    
    private function actualizarEstadisticasNovela($idNovela) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as total_capitulos, SUM(palabras_traducidas) as total_palabras,
                       SUM(tiempo_traduccion_segundos) as total_tiempo, SUM(costo_traduccion) as total_costo,
                       SUM(tokens) as total_tokens
                FROM capitulos_traduccion_espanol cte
                JOIN capitulos c ON cte.id_capitulo = c.id_capitulo
                WHERE c.id_novela = ? AND cte.estado_traduccion = 'completado'
            ");
            $stmt->execute([$idNovela]);
            $totales = $stmt->fetch();
            
            $stmt = $this->pdo->prepare("
                UPDATE novelas_traduccion_espanol 
                SET palabras_traducidas = ?, tiempo_traduccion_segundos = ?, costo_traduccion = ?, tokens = ?,
                    estado_traduccion = CASE 
                        WHEN ? >= (SELECT total_capitulos_originales FROM novelas WHERE id_novela = ?) THEN 'completado' 
                        ELSE 'en_progreso' 
                    END
                WHERE id_novela = ?
            ");
            
            $stmt->execute([
                $totales['total_palabras'] ?? 0, $totales['total_tiempo'] ?? 0, $totales['total_costo'] ?? 0,
                $totales['total_tokens'] ?? 0, $totales['total_capitulos'] ?? 0, $idNovela, $idNovela
            ]);
            
        } catch (Exception $e) { }
    }
    
    private function registrarLogAPI($capitulo, $respuesta, $costo, $error = null) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO logs_api_traduccion (
                    id_api, id_capitulo, id_novela, tipo_operacion, idioma_origen, idioma_destino, 
                    tokens_usados, tokens_prompt, tokens_completion, duracion_ms, costo_estimado, estado, mensaje_error
                ) VALUES (?, ?, ?, 'traducir_capitulo', 'zh', 'es', ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $duracion = (int)((microtime(true) - $this->startTime) * 1000);
            
            $stmt->execute([
                $this->apiActual['id_api'], $capitulo['id_capitulo'], $capitulo['id_novela'],
                $respuesta['tokens_usados'] ?? 0, $respuesta['tokens_input'] ?? 0, $respuesta['tokens_output'] ?? 0,
                $duracion, $costo, $respuesta['exito'] ? 'exito' : 'error', $error ? substr($error, 0, 255) : null
            ]);
            
        } catch (Exception $e) { }
    }
    
    private function incrementarTasaError($idApi) {
        try {
            $stmt = $this->pdo->prepare("UPDATE apis_traduccion SET tasa_error = tasa_error + 0.5 WHERE id_api = ?");
            $stmt->execute([$idApi]);
        } catch (Exception $e) { }
    }
    
    private function marcarErrorCapitulo($capitulo) {
        try {
            if (!empty($capitulo['id_traduccion_capitulo_es'])) {
                $stmt = $this->pdo->prepare("
                    UPDATE capitulos_traduccion_espanol 
                    SET estado_traduccion = 'error', errores_reportados = errores_reportados + 1
                    WHERE id_traduccion_capitulo_es = ?
                ");
                $stmt->execute([$capitulo['id_traduccion_capitulo_es']]);
            }
        } catch (Exception $e) { }
    }
    
    private function mostrarResumen() {
        $tiempoTotal = round(microtime(true) - $this->startTime, 2);
        $tiempoMinutos = round($tiempoTotal / 60, 2);
        
        $this->log(str_repeat("=", 80));
        $this->log("📊 RESUMEN FINAL DE EJECUCIÓN");
        $this->log(str_repeat("=", 80));
        $this->log("📈 Procesados: {$this->stats['procesados']}");
        $this->log("✅ Exitosos: {$this->stats['exitosos']}");
        $this->log("❌ Fallidos: {$this->stats['fallidos']}");
        $this->log("💰 Costo estimado: \$" . round($this->stats['costo_total'], 2));
        $this->log("🔄 Failovers/Saturaciones: {$this->stats['cambios_api']}");
        $this->log("⏱️ Tiempo de ejecución: {$tiempoTotal}s ({$tiempoMinutos} min)");
        $this->log("📊 Tokens consumidos: {$this->stats['tokens_totales']}");
        $this->log("🛑 FIN DEL PROCESO");
    }
    
    private function desencriptarAPIKey($keyEncriptada) {
        if (empty($keyEncriptada)) return '';
        if (preg_match('/^\{\{(.+)\}\}$/', $keyEncriptada, $matches)) return $_ENV[$matches[1]] ?? '';
        $decoded = base64_decode($keyEncriptada, true);
        return $decoded !== false ? $decoded : $keyEncriptada;
    }
    
    private function log($mensaje, $nivel = 'INFO') {
        // Cálculo preciso con milisegundos para una línea de tiempo exacta
        $t = microtime(true);
        $micro = sprintf("%03d", ($t - floor($t)) * 1000);
        $timestamp = date('Y-m-d H:i:s') . '.' . $micro;
        
        $linea = "[{$timestamp}] [{$nivel}] {$mensaje}\n";
        
        // Escribe directamente al archivo sin emitir nada por consola (Silent mode)
        file_put_contents($this->logFile, $linea, FILE_APPEND);
    }
}

try {
    $job = new MultiApiTraductorJob();
    $job->ejecutar();
} catch (Exception $e) {
    file_put_contents(
        BASE_PATH . '/logs/fatal_error_contenido.log',
        "[" . date('Y-m-d H:i:s') . "] ❌ ERROR CRÍTICO FATAL: " . $e->getMessage() . "\n",
        FILE_APPEND
    );
    exit(1);
}
exit(0);