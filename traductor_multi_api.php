<?php
/**
 * JOB: Traductor Multi-API con Rate Limiting Inteligente - VERSIÓN FINAL
 * 
 * Características:
 * 1. ✅ Rate limiting por minuto (2 capítulos / 10000 tokens)
 * 2. ✅ Rate limiting por día (95% de tokens_por_dia)
 * 3. ✅ Bucle infinito hasta completar todos los capítulos
 * 4. ✅ Espera inteligente cuando todas las APIs están en cooldown
 */

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
ini_set('error_log', BASE_PATH . '/storage/logs/multi_api_error.log');
set_time_limit(0); // Sin límite de tiempo - el bucle puede durar horas

class MultiApiTraductorJob {
    
    private $pdo;
    private $apisDisponibles = [];
    private $apiActual = null;
    private $logFile;
    private $startTime;
    
    // Seguimiento de uso por API
    private $usoAPIs = [];
    private $apisEnEspera = []; // APIs en cooldown por minuto
    private $apisAgotadasHoy = []; // APIs que ya no pueden usarse hoy (95% límite diario)
    
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
    
    // Límites
    const TOKENS_MAXIMOS_POR_MINUTO = 10000; // 10K tokens por minuto
    const CAPITULOS_POR_API_POR_MINUTO = 2; // Máximo 2 capítulos por minuto por API
    const TIEMPO_VENTANA_SEGUNDOS = 60; // Ventana de 1 minuto
    const UMBRAL_DIARIO_PORCENTAJE = 95; // 95% del límite diario
    const TIEMPO_ESPERA_ENTRE_INTENTOS = 5; // Segundos entre intentos cuando no hay APIs
    
    // Costo fijo por capítulo
    const COSTO_POR_CAPITULO = 0.01; // $0.01 USD por capítulo
    
    // Prompt simplificado (solo contenido)
    const PROMPT_BASE = <<<EOT
Eres un traductor profesional especializado en novelas chinas (xianxia, wuxia, cultivation).
Traduce el siguiente texto del chino al español de forma natural, manteniendo términos técnicos.

Texto a traducir:
{CONTENIDO}

SOLO responde con el texto traducido, sin títulos, explicaciones ni comentarios adicionales.
EOT;

    public function __construct() {
        $this->startTime = microtime(true);
        $this->logFile = BASE_PATH . '/storage/logs/traductor_' . date('Y-m-d') . '.log';
        
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
        
        $this->log("=== INICIO JOB MULTI-API CON RATE LIMITING COMPLETO ===");
        $this->log("Fecha: " . date('Y-m-d H:i:s'));
        $this->log("Límite por minuto por API: " . self::TOKENS_MAXIMOS_POR_MINUTO . " tokens");
        $this->log("Capítulos máximos por minuto por API: " . self::CAPITULOS_POR_API_POR_MINUTO);
        $this->log("Límite diario: 95% de tokens_por_dia");
    }
    
    private function conectarDB() {
        try {
            $host = $_ENV['DB_HOST'] ?? 'localhost';
            $database = $_ENV['DB_DATABASE'] ?? $_ENV['DB_NAME'] ?? 'novelas_db';
            $username = $_ENV['DB_USERNAME'] ?? $_ENV['DB_USER'] ?? 'root';
            $password = $_ENV['DB_PASSWORD'] ?? $_ENV['DB_PASS'] ?? '';
            $port = $_ENV['DB_PORT'] ?? '3306';
            
            $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
            
            $this->pdo = new PDO(
                $dsn,
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            
            $this->log("✓ Conexión BD exitosa");
            
        } catch (PDOException $e) {
            $this->log("✗ Error BD: " . $e->getMessage(), 'ERROR');
            die("Error BD: " . $e->getMessage() . "\n");
        }
    }
    
    private function cargarAPIsDisponibles() {
        try {
            $sql = "
                SELECT 
                    id_api,
                    nombre_api,
                    proveedor,
                    modelo,
                    endpoint_url,
                    api_key_encriptada,
                    prioridad,
                    tokens_por_minuto,
                    tokens_por_dia,
                    requests_por_minuto,
                    requests_por_dia,
                    tokens_usados_hoy,
                    requests_hoy,
                    costo_por_millon_tokens,
                    configuracion_chino,
                    modo_reserva,
                    tasa_error
                FROM apis_traduccion 
                WHERE esta_activa = TRUE
                ORDER BY modo_reserva ASC, prioridad ASC, tasa_error ASC
            ";
            
            $stmt = $this->pdo->query($sql);
            $apis = $stmt->fetchAll();
            
            if (empty($apis)) {
                $this->log("⚠️ No hay APIs activas configuradas");
                return;
            }
            
            foreach ($apis as $api) {
                $api['api_key'] = $this->desencriptarAPIKey($api['api_key_encriptada']);
                unset($api['api_key_encriptada']);
                $api['config'] = json_decode($api['configuracion_chino'], true) ?: [];
                $this->verificarResetAPI($api);
                
                $this->apisDisponibles[$api['id_api']] = $api;
            }
            
            $this->log("✓ APIs cargadas: " . count($this->apisDisponibles));
            
            // Inicializar estadísticas
            foreach ($this->apisDisponibles as $id => $api) {
                $this->stats['tokens_por_api'][$id] = 0;
            }
            
        } catch (Exception $e) {
            $this->log("✗ Error cargando APIs: " . $e->getMessage(), 'ERROR');
            die("Error cargando APIs\n");
        }
    }
    
    /**
     * Verificar límite diario de API
     */
    private function verificarLimiteDiario($api) {
        $tokensUsadosHoy = $api['tokens_usados_hoy'];
        $tokensPorDia = $api['tokens_por_dia'];
        
        if ($tokensPorDia <= 0) return true; // Sin límite
        
        $porcentajeUsado = ($tokensUsadosHoy / $tokensPorDia) * 100;
        $limiteDiario = ($tokensPorDia * self::UMBRAL_DIARIO_PORCENTAJE) / 100;
        
        if ($tokensUsadosHoy >= $limiteDiario) {
            return false; // No puede usar más hoy
        }
        
        return true;
    }
    
    /**
     * Inicializar el seguimiento de uso
     */
    private function inicializarSeguimientoUso() {
        try {
            $ahora = time();
            $haceUnMinuto = date('Y-m-d H:i:s', $ahora - self::TIEMPO_VENTANA_SEGUNDOS);
            
            // Inicializar arrays de uso para cada API
            foreach ($this->apisDisponibles as $id => $api) {
                $this->usoAPIs[$id] = [
                    'tokens_minuto' => 0,
                    'capitulos_minuto' => 0,
                    'ultimo_uso' => null,
                    'en_espera_hasta' => null,
                    'tokens_usados_hoy' => $api['tokens_usados_hoy'],
                    'tokens_por_dia' => $api['tokens_por_dia']
                ];
                
                // Verificar límite diario
                if (!$this->verificarLimiteDiario($api)) {
                    $this->apisAgotadasHoy[$id] = true;
                    $porcentaje = round(($api['tokens_usados_hoy'] / $api['tokens_por_dia']) * 100, 2);
                    $this->log("  ⚠️ API {$api['nombre_api']} agotada por hoy: {$porcentaje}% usado");
                }
            }
            
            // Obtener traducciones del último minuto
            $stmt = $this->pdo->prepare("
                SELECT 
                    cte.traductor_ia,
                    cte.tokens,
                    cte.fecha_traduccion,
                    api.id_api
                FROM capitulos_traduccion_espanol cte
                JOIN apis_traduccion api ON cte.traductor_ia = api.nombre_api
                WHERE cte.fecha_traduccion > ?
                ORDER BY cte.fecha_traduccion ASC
            ");
            $stmt->execute([$haceUnMinuto]);
            $traduccionesRecientes = $stmt->fetchAll();
            
            foreach ($traduccionesRecientes as $trad) {
                if (isset($this->usoAPIs[$trad['id_api']])) {
                    $this->usoAPIs[$trad['id_api']]['tokens_minuto'] += $trad['tokens'];
                    $this->usoAPIs[$trad['id_api']]['capitulos_minuto']++;
                    $this->usoAPIs[$trad['id_api']]['ultimo_uso'] = strtotime($trad['fecha_traduccion']);
                }
            }
            
            // Determinar qué APIs están en espera por minuto
            foreach ($this->usoAPIs as $apiId => $uso) {
                if ($uso['tokens_minuto'] >= self::TOKENS_MAXIMOS_POR_MINUTO - 500) {
                    $tiempoEspera = self::TIEMPO_VENTANA_SEGUNDOS - ($ahora - $uso['ultimo_uso']);
                    if ($tiempoEspera > 0) {
                        $this->usoAPIs[$apiId]['en_espera_hasta'] = $ahora + $tiempoEspera;
                        $this->apisEnEspera[$apiId] = $ahora + $tiempoEspera;
                        $nombre = $this->apisDisponibles[$apiId]['nombre_api'];
                        $this->log("  ⏳ API {$nombre} en espera inicial por {$tiempoEspera}s");
                    }
                }
            }
            
        } catch (Exception $e) {
            $this->log("⚠️ Error inicializando seguimiento: " . $e->getMessage(), 'WARNING');
        }
    }
    
    private function verificarResetAPI(&$api) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT ultimo_reset, DATE(ultimo_reset) as fecha_reset
                FROM apis_traduccion 
                WHERE id_api = ?
            ");
            $stmt->execute([$api['id_api']]);
            $reset = $stmt->fetch();
            
            if ($reset && $reset['fecha_reset'] < date('Y-m-d')) {
                $stmt = $this->pdo->prepare("
                    UPDATE apis_traduccion 
                    SET tokens_usados_hoy = 0, 
                        requests_hoy = 0, 
                        ultimo_reset = NOW()
                    WHERE id_api = ?
                ");
                $stmt->execute([$api['id_api']]);
                
                $api['tokens_usados_hoy'] = 0;
                $api['requests_hoy'] = 0;
                
                $this->log("  ↻ Reset diario para API {$api['id_api']}");
            }
        } catch (PDOException $e) {
            // Silenciar error
        }
    }
    
    /**
     * Buscar la mejor API disponible
     */
    private function buscarMejorAPI() {
        $ahora = time();
        $apisDisponiblesAhora = [];
        
        // Limpiar APIs que ya cumplieron su tiempo de espera por minuto
        foreach ($this->apisEnEspera as $apiId => $esperaHasta) {
            if ($ahora >= $esperaHasta) {
                unset($this->apisEnEspera[$apiId]);
                if (isset($this->usoAPIs[$apiId])) {
                    $this->usoAPIs[$apiId]['en_espera_hasta'] = null;
                    $this->usoAPIs[$apiId]['tokens_minuto'] = 0; // Resetear tokens del minuto
                    $this->usoAPIs[$apiId]['capitulos_minuto'] = 0;
                    $nombre = $this->apisDisponibles[$apiId]['nombre_api'];
                    $this->log("  🔄 API {$nombre} disponible nuevamente después de espera");
                }
            }
        }
        
        // Evaluar cada API
        foreach ($this->apisDisponibles as $apiId => $api) {
            $nombre = $api['nombre_api'];
            
            // Verificar si está agotada por hoy
            if (isset($this->apisAgotadasHoy[$apiId])) {
                continue;
            }
            
            // Verificar límite diario
            if (!$this->verificarLimiteDiario($api)) {
                $this->apisAgotadasHoy[$apiId] = true;
                $porcentaje = round(($api['tokens_usados_hoy'] / $api['tokens_por_dia']) * 100, 2);
                $this->log("  ⚠️ API {$nombre} alcanzó límite diario ({$porcentaje}%)");
                continue;
            }
            
            // Verificar si está en espera por minuto
            if (isset($this->apisEnEspera[$apiId])) {
                $segundosRestantes = $this->apisEnEspera[$apiId] - $ahora;
                $this->log("  ⏳ API {$nombre} en espera por {$segundosRestantes}s");
                continue;
            }
            
            // Verificar límites básicos
            if (!$this->apiPuedeUsarse($api)) {
                continue;
            }
            
            // Obtener uso actual del minuto
            $uso = $this->usoAPIs[$apiId] ?? ['tokens_minuto' => 0, 'capitulos_minuto' => 0];
            
            // Calcular disponibilidad
            $tokensDisponibles = self::TOKENS_MAXIMOS_POR_MINUTO - $uso['tokens_minuto'];
            $capitulosDisponibles = self::CAPITULOS_POR_API_POR_MINUTO - $uso['capitulos_minuto'];
            
            // Si puede procesar al menos un capítulo
            if ($tokensDisponibles > 500 && $capitulosDisponibles > 0) { // Mínimo 500 tokens para intentar
                $apisDisponiblesAhora[$apiId] = [
                    'tokens_disponibles' => $tokensDisponibles,
                    'capitulos_disponibles' => $capitulosDisponibles,
                    'tokens_usados' => $uso['tokens_minuto'],
                    'capitulos_usados' => $uso['capitulos_minuto'],
                    'nombre' => $nombre
                ];
            }
        }
        
        // Si hay APIs disponibles, elegir la mejor
        if (!empty($apisDisponiblesAhora)) {
            // Ordenar por: menos capítulos usados, luego menos tokens usados
            uasort($apisDisponiblesAhora, function($a, $b) {
                if ($a['capitulos_usados'] == $b['capitulos_usados']) {
                    return $a['tokens_usados'] <=> $b['tokens_usados'];
                }
                return $a['capitulos_usados'] <=> $b['capitulos_usados'];
            });
            
            $mejorAPIId = array_key_first($apisDisponiblesAhora);
            $this->apiActual = $this->apisDisponibles[$mejorAPIId];
            $info = $apisDisponiblesAhora[$mejorAPIId];
            
            $this->log("🔁 Usando API: {$info['nombre']} (ID: {$mejorAPIId}) - ya usó {$info['capitulos_usados']} caps, {$info['tokens_usados']} tokens en este minuto");
            return true;
        }
        
        return false;
    }
    
    private function apiPuedeUsarse($api) {
        // Verificar límites diarios de requests
        if ($api['requests_hoy'] >= $api['requests_por_dia']) {
            return false;
        }
        if ($api['tasa_error'] > 30) {
            return false;
        }
        return true;
    }
    
    /**
     * Registrar uso de API después de una traducción exitosa
     */
    private function registrarUsoAPI($apiId, $tokensUsados) {
        $ahora = time();
        
        if (!isset($this->usoAPIs[$apiId])) {
            $this->usoAPIs[$apiId] = [
                'tokens_minuto' => 0,
                'capitulos_minuto' => 0,
                'ultimo_uso' => null,
                'en_espera_hasta' => null
            ];
        }
        
        $this->usoAPIs[$apiId]['tokens_minuto'] += $tokensUsados;
        $this->usoAPIs[$apiId]['capitulos_minuto']++;
        $this->usoAPIs[$apiId]['ultimo_uso'] = $ahora;
        
        $usoActual = $this->usoAPIs[$apiId];
        $this->log("  📊 API {$apiId}: ahora {$usoActual['capitulos_minuto']} caps, {$usoActual['tokens_minuto']} tokens en este minuto");
        
        // Verificar si después de este capítulo, la API debe entrar en espera por minuto
        if ($usoActual['tokens_minuto'] >= self::TOKENS_MAXIMOS_POR_MINUTO - 500 || 
            $usoActual['capitulos_minuto'] >= self::CAPITULOS_POR_API_POR_MINUTO) {
            
            $tiempoEspera = self::TIEMPO_VENTANA_SEGUNDOS;
            $this->usoAPIs[$apiId]['en_espera_hasta'] = $ahora + $tiempoEspera;
            $this->apisEnEspera[$apiId] = $ahora + $tiempoEspera;
            
            $nombre = $this->apisDisponibles[$apiId]['nombre_api'];
            $this->log("  ⏸️ API {$nombre} en espera por {$tiempoEspera}s (alcanzó límite: {$usoActual['capitulos_minuto']} caps, {$usoActual['tokens_minuto']} tokens)");
            
            return true; // Indica que la API entró en espera
        }
        
        return false;
    }
    
    public function ejecutar() {
        $this->log("\n=== INICIANDO PROCESAMIENTO CON RATE LIMITING COMPLETO ===");
        
        if (empty($this->apisDisponibles)) {
            $this->log("✗ No hay APIs configuradas");
            return;
        }
        
        // Verificar si hay APIs con límite diario disponible
        $apisConLimiteDiario = 0;
        foreach ($this->apisDisponibles as $apiId => $api) {
            if (!isset($this->apisAgotadasHoy[$apiId])) {
                $apisConLimiteDiario++;
            }
        }
        
        if ($apisConLimiteDiario == 0) {
            $this->log("✗ TODAS LAS APIs ALCANZARON SU LÍMITE DIARIO");
            $this->log("   El job se detiene hasta mañana.");
            return;
        }
        
        $capitulos = $this->obtenerCapitulosPendientes();
        $totalCapitulos = count($capitulos);
        
        if ($totalCapitulos == 0) {
            $this->log("✓ No hay capítulos pendientes");
            return;
        }
        
        $this->log("📚 Capítulos a procesar: " . $totalCapitulos);
        
        $capitulosProcesados = 0;
        $intentosSinAPI = 0;
        
        // BUCLE PRINCIPAL - NO TERMINA HASTA PROCESAR TODOS LOS CAPÍTULOS
        while ($capitulosProcesados < $totalCapitulos) {
            // Verificar tiempo total (opcional - para logs)
            $tiempoTranscurrido = microtime(true) - $this->startTime;
            if ($capitulosProcesados > 0 && $capitulosProcesados % 10 == 0) {
                $this->log("\n⏱️ Progreso: {$capitulosProcesados}/{$totalCapitulos} capítulos - Tiempo: " . round($tiempoTranscurrido / 60, 2) . " minutos");
            }
            
            // Buscar API disponible
            if (!$this->buscarMejorAPI()) {
                $intentosSinAPI++;
                $tiempoEspera = min(self::TIEMPO_ESPERA_ENTRE_INTENTOS * $intentosSinAPI, 30); // Espera progresiva máx 30s
                
                $this->log("⏳ No hay APIs disponibles. Esperando {$tiempoEspera} segundos... (intento {$intentosSinAPI})");
                $this->stats['tiempo_espera_total'] += $tiempoEspera;
                sleep($tiempoEspera);
                continue;
            }
            
            // Resetear contador de intentos cuando encontramos API
            $intentosSinAPI = 0;
            
            // Obtener el siguiente capítulo pendiente
            $capitulo = $capitulos[$capitulosProcesados];
            
            $this->stats['procesados']++;
            $capitulosProcesados++;
            
            $this->log("\n--- Capítulo {$capitulosProcesados}/{$totalCapitulos} ---");
            $this->log("  Novela: {$capitulo['titulo_novela_es']}");
            $this->log("  Capítulo: {$capitulo['numero_capitulo']} - {$capitulo['titulo_original']}");
            $this->log("  API actual: {$this->apiActual['nombre_api']}");
            
            $resultado = $this->traducirCapitulo($capitulo);
            
            if ($resultado['exito']) {
                $this->stats['exitosos']++;
                $this->stats['tokens_totales'] += $resultado['tokens_usados'];
                $this->stats['costo_total'] += self::COSTO_POR_CAPITULO;
                
                $apiId = $this->apiActual['id_api'];
                
                // Registrar uso para rate limiting
                $entroEnEspera = $this->registrarUsoAPI($apiId, $resultado['tokens_usados']);
                
                // Actualizar tokens usados hoy en la API
                $this->apisDisponibles[$apiId]['tokens_usados_hoy'] += $resultado['tokens_usados'];
                $this->usoAPIs[$apiId]['tokens_usados_hoy'] = $this->apisDisponibles[$apiId]['tokens_usados_hoy'];
                
                // Verificar límite diario después de actualizar
                if (!$this->verificarLimiteDiario($this->apisDisponibles[$apiId])) {
                    $this->apisAgotadasHoy[$apiId] = true;
                    $porcentaje = round(($this->apisDisponibles[$apiId]['tokens_usados_hoy'] / $this->apisDisponibles[$apiId]['tokens_por_dia']) * 100, 2);
                    $this->log("  ⚠️ API {$this->apiActual['nombre_api']} alcanzó límite diario ({$porcentaje}%)");
                }
                
                // Estadísticas
                if (!isset($this->stats['api_usada'][$apiId])) {
                    $this->stats['api_usada'][$apiId] = 0;
                }
                $this->stats['api_usada'][$apiId]++;
                $this->stats['tokens_por_api'][$apiId] += $resultado['tokens_usados'];
                
                // Actualizar contadores en BD
                $this->actualizarContadoresAPI(
                    $this->apiActual['id_api'], 
                    $resultado['tokens_usados']
                );
                
                $this->log("  ✅ Traducido - Tokens: {$resultado['tokens_usados']} - Costo: \$" . self::COSTO_POR_CAPITULO);
                
                // Si la API entró en espera por minuto, buscar otra inmediatamente
                if ($entroEnEspera) {
                    $this->stats['cambios_api']++;
                    $this->log("  🔄 Buscando siguiente API...");
                    // No buscar inmediatamente, dejar que el siguiente ciclo del while lo haga
                }
                
            } else {
                $this->stats['fallidos']++;
                $this->incrementarTasaError($this->apiActual['id_api']);
                $this->log("  ❌ Error: " . $resultado['mensaje'], 'ERROR');
                // No incrementamos capítulosProcesados? Debemos decidir si reintentar el mismo capítulo
                // Por ahora, lo saltamos (ya se incrementó arriba)
            }
            
            // Pequeña pausa entre requests
            usleep(500000); // 0.5 segundos
            
            // Verificar si todas las APIs están agotadas por hoy
            $apisActivasHoy = 0;
            foreach ($this->apisDisponibles as $apiId => $api) {
                if (!isset($this->apisAgotadasHoy[$apiId])) {
                    $apisActivasHoy++;
                }
            }
            
            if ($apisActivasHoy == 0) {
                $this->log("\n⚠️ TODAS LAS APIs ALCANZARON SU LÍMITE DIARIO");
                $this->log("   Procesados {$capitulosProcesados}/{$totalCapitulos} capítulos.");
                $this->log("   El job se detiene hasta mañana.");
                break;
            }
        }
        
        $this->mostrarResumen();
    }
    
    private function obtenerCapitulosPendientes() {
        try {
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
                    nte.id_traduccion_novela_es,
                    nte.tokens as tokens_novela
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
                    c.enviado_traduccion = FALSE
                    AND c.estado_capitulo = 'disponible'
                    AND c.contenido_original IS NOT NULL
                    AND LENGTH(c.contenido_original) > 0
                    AND (cte.id_traduccion_capitulo_es IS NULL 
                         OR cte.estado_traduccion IN ('pendiente', 'error'))
                ORDER BY 
                    n.total_vistas DESC,
                    c.prioridad_traduccion DESC,
                    c.orden_capitulo ASC
            ";
            
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            $this->log("✗ Error obteniendo capítulos: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }
    
    private function traducirCapitulo($capitulo) {
        $resultado = [
            'exito' => false,
            'tokens_usados' => 0,
            'costo' => 0,
            'mensaje' => ''
        ];
        
        try {
            if (empty($this->apiActual['api_key'])) {
                throw new Exception("API Key vacía");
            }
            
            $prompt = $this->prepararPrompt($capitulo);
            $respuesta = $this->llamarAPI($prompt);
            
            if (!$respuesta['exito']) {
                throw new Exception($respuesta['error']);
            }
            
            // Guardar traducción (solo contenido)
            $this->guardarTraduccion($capitulo, $respuesta['traduccion'], $respuesta['tokens_usados']);
            
            // Marcar capítulo como enviado a traducción
            $this->marcarCapituloTraducido($capitulo['id_capitulo']);
            
            // Actualizar estadísticas acumuladas en novelas_traduccion_espanol
            $this->actualizarEstadisticasNovela($capitulo['id_novela'], $respuesta['tokens_usados']);
            
            $costo = self::COSTO_POR_CAPITULO;
            
            $this->registrarLogAPI($capitulo, $respuesta, $costo);
            
            $resultado['exito'] = true;
            $resultado['tokens_usados'] = $respuesta['tokens_usados'];
            $resultado['costo'] = $costo;
            
        } catch (Exception $e) {
            $this->registrarLogAPI($capitulo, ['exito' => false], 0, $e->getMessage());
            $this->marcarErrorCapitulo($capitulo);
            $resultado['mensaje'] = $e->getMessage();
        }
        
        return $resultado;
    }
    
    private function prepararPrompt($capitulo) {
        return str_replace(
            '{CONTENIDO}',
            $capitulo['contenido_original'],
            self::PROMPT_BASE
        );
    }
    
    private function llamarAPI($prompt) {
        $proveedor = strtolower($this->apiActual['proveedor'] ?? '');
        
        switch ($proveedor) {
            case 'groq':
                return $this->llamarGROQ($prompt);
            default:
                throw new Exception("Proveedor no soportado: {$proveedor}");
        }
    }
    
    private function llamarGROQ($prompt) {
        $url = $this->apiActual['endpoint_url'] ?: 'https://api.groq.com/openai/v1/chat/completions';
        
        $data = [
            'model' => $this->apiActual['modelo'],
            'messages' => [
                ['role' => 'system', 'content' => 'Eres un traductor profesional. Responde SOLO con el texto traducido, sin títulos ni explicaciones.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => $this->apiActual['config']['temperatura'] ?? 0.2,
            'max_tokens' => $this->apiActual['config']['max_tokens_completion'] ?? 4000,
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
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            return [
                'exito' => false,
                'error' => 'Error CURL: ' . $curlError
            ];
        }
        
        if ($httpCode == 429) {
            return [
                'exito' => false,
                'error' => "HTTP 429 - Rate limit exceeded"
            ];
        }
        
        if ($httpCode !== 200) {
            return [
                'exito' => false,
                'error' => "HTTP {$httpCode}"
            ];
        }
        
        $result = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'exito' => false,
                'error' => 'Error decodificando JSON'
            ];
        }
        
        return [
            'exito' => true,
            'traduccion' => trim($result['choices'][0]['message']['content'] ?? ''),
            'tokens_usados' => $result['usage']['total_tokens'] ?? 0,
            'tokens_input' => $result['usage']['prompt_tokens'] ?? 0,
            'tokens_output' => $result['usage']['completion_tokens'] ?? 0
        ];
    }
    
    private function guardarTraduccion($capitulo, $contenidoTraducido, $tokens) {
        try {
            $this->pdo->beginTransaction();
            
            // Obtener o crear traducción de novela
            if (empty($capitulo['id_traduccion_novela_es'])) {
                $stmt = $this->pdo->prepare("
                    INSERT INTO novelas_traduccion_espanol (id_novela, estado_traduccion, tokens)
                    VALUES (?, 'en_progreso', 0)
                ");
                $stmt->execute([$capitulo['id_novela']]);
                $idTradNovela = $this->pdo->lastInsertId();
            } else {
                $idTradNovela = $capitulo['id_traduccion_novela_es'];
            }
            
            // Guardar traducción del capítulo
            $palabras = str_word_count($contenidoTraducido, 0, 'áéíóúñü');
            $hash = hash('sha256', $contenidoTraducido);
            $tiempo = (int)(microtime(true) - $this->startTime);
            
            // Verificar si ya existe una versión
            $stmt = $this->pdo->prepare("
                SELECT id_traduccion_capitulo_es, version_traduccion 
                FROM capitulos_traduccion_espanol 
                WHERE id_capitulo = ? 
                ORDER BY version_traduccion DESC 
                LIMIT 1
            ");
            $stmt->execute([$capitulo['id_capitulo']]);
            $existente = $stmt->fetch();
            
            if ($existente) {
                // Actualizar versión existente
                $stmt = $this->pdo->prepare("
                    UPDATE capitulos_traduccion_espanol SET
                        contenido_traducido = ?,
                        estado_traduccion = 'completado',
                        traductor_ia = ?,
                        calidad_estimada = 'alta',
                        palabras_traducidas = ?,
                        tiempo_traduccion_segundos = ?,
                        costo_traduccion = ?,
                        tokens = ?,
                        hash_traduccion = ?,
                        fecha_traduccion = NOW(),
                        version_traduccion = version_traduccion + 1
                    WHERE id_traduccion_capitulo_es = ?
                ");
                $stmt->execute([
                    $contenidoTraducido,
                    $this->apiActual['nombre_api'],
                    $palabras,
                    $tiempo,
                    self::COSTO_POR_CAPITULO,
                    $tokens,
                    $hash,
                    $existente['id_traduccion_capitulo_es']
                ]);
            } else {
                // Insertar nueva traducción
                $stmt = $this->pdo->prepare("
                    INSERT INTO capitulos_traduccion_espanol (
                        id_capitulo, id_traduccion_novela_es,
                        contenido_traducido, estado_traduccion, traductor_ia,
                        calidad_estimada, palabras_traducidas, tiempo_traduccion_segundos,
                        costo_traduccion, tokens, hash_traduccion,
                        fecha_traduccion
                    ) VALUES (
                        ?, ?,
                        ?, 'completado', ?,
                        'alta', ?, ?, ?, ?, ?,
                        NOW()
                    )
                ");
                $stmt->execute([
                    $capitulo['id_capitulo'],
                    $idTradNovela,
                    $contenidoTraducido,
                    $this->apiActual['nombre_api'],
                    $palabras,
                    $tiempo,
                    self::COSTO_POR_CAPITULO,
                    $tokens,
                    $hash
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
            $stmt = $this->pdo->prepare("
                UPDATE capitulos 
                SET enviado_traduccion = TRUE 
                WHERE id_capitulo = ?
            ");
            $stmt->execute([$idCapitulo]);
        } catch (Exception $e) {
            $this->log("  ⚠️ Error marcando capítulo: " . $e->getMessage(), 'WARNING');
        }
    }
    
    private function actualizarEstadisticasNovela($idNovela, $tokensCapitulo) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    COUNT(*) as total_capitulos,
                    SUM(palabras_traducidas) as total_palabras,
                    SUM(tiempo_traduccion_segundos) as total_tiempo,
                    SUM(costo_traduccion) as total_costo,
                    SUM(tokens) as total_tokens
                FROM capitulos_traduccion_espanol cte
                JOIN capitulos c ON cte.id_capitulo = c.id_capitulo
                WHERE c.id_novela = ? 
                AND cte.estado_traduccion = 'completado'
            ");
            $stmt->execute([$idNovela]);
            $totales = $stmt->fetch();
            
            $stmt = $this->pdo->prepare("
                UPDATE novelas_traduccion_espanol 
                SET 
                    palabras_traducidas = ?,
                    tiempo_traduccion_segundos = ?,
                    costo_traduccion = ?,
                    tokens = ?,
                    estado_traduccion = CASE 
                        WHEN ? >= (SELECT total_capitulos_originales FROM novelas WHERE id_novela = ?) 
                        THEN 'completado' 
                        ELSE 'en_progreso' 
                    END
                WHERE id_novela = ?
            ");
            
            $stmt->execute([
                $totales['total_palabras'] ?? 0,
                $totales['total_tiempo'] ?? 0,
                $totales['total_costo'] ?? 0,
                $totales['total_tokens'] ?? 0,
                $totales['total_capitulos'] ?? 0,
                $idNovela,
                $idNovela
            ]);
            
        } catch (Exception $e) {
            $this->log("  ⚠️ Error actualizando estadísticas: " . $e->getMessage(), 'WARNING');
        }
    }
    
    private function registrarLogAPI($capitulo, $respuesta, $costo, $error = null) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO logs_api_traduccion (
                    id_api, id_capitulo, id_novela, tipo_operacion,
                    idioma_origen, idioma_destino, tokens_usados,
                    tokens_prompt, tokens_completion, duracion_ms,
                    costo_estimado, estado, mensaje_error
                ) VALUES (
                    ?, ?, ?, 'traducir_capitulo',
                    'zh', 'es', ?,
                    ?, ?, ?,
                    ?, ?, ?
                )
            ");
            
            $duracion = (int)((microtime(true) - $this->startTime) * 1000);
            
            $stmt->execute([
                $this->apiActual['id_api'],
                $capitulo['id_capitulo'],
                $capitulo['id_novela'],
                $respuesta['tokens_usados'] ?? 0,
                $respuesta['tokens_input'] ?? 0,
                $respuesta['tokens_output'] ?? 0,
                $duracion,
                $costo,
                $respuesta['exito'] ? 'exito' : 'error',
                $error ? substr($error, 0, 255) : null
            ]);
            
        } catch (Exception $e) {
            // Silenciar error
        }
    }
    
    private function actualizarContadoresAPI($idApi, $tokens) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE apis_traduccion 
                SET tokens_usados_hoy = tokens_usados_hoy + ?,
                    requests_hoy = requests_hoy + 1,
                    ultimo_uso = NOW()
                WHERE id_api = ?
            ");
            $stmt->execute([$tokens, $idApi]);
            
        } catch (Exception $e) {
            // Silenciar error
        }
    }
    
    private function incrementarTasaError($idApi) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE apis_traduccion 
                SET tasa_error = tasa_error + 0.5
                WHERE id_api = ?
            ");
            $stmt->execute([$idApi]);
            
        } catch (Exception $e) {
            // Silenciar error
        }
    }
    
    private function marcarErrorCapitulo($capitulo) {
        try {
            if (!empty($capitulo['id_traduccion_capitulo_es'])) {
                $stmt = $this->pdo->prepare("
                    UPDATE capitulos_traduccion_espanol 
                    SET estado_traduccion = 'error',
                        errores_reportados = errores_reportados + 1
                    WHERE id_traduccion_capitulo_es = ?
                ");
                $stmt->execute([$capitulo['id_traduccion_capitulo_es']]);
            }
        } catch (Exception $e) {
            // Silenciar error
        }
    }
    
    private function mostrarResumen() {
        $tiempoTotal = round(microtime(true) - $this->startTime, 2);
        $tiempoMinutos = round($tiempoTotal / 60, 2);
        $tokensPorSegundo = $this->stats['tokens_totales'] / max(1, $tiempoTotal);
        
        $this->log("\n" . str_repeat("=", 60));
        $this->log("📊 RESUMEN FINAL");
        $this->log(str_repeat("=", 60));
        $this->log("📈 Procesados: {$this->stats['procesados']}");
        $this->log("✅ Exitosos: {$this->stats['exitosos']}");
        $this->log("❌ Fallidos: {$this->stats['fallidos']}");
        $this->log("💰 Costo total: \$" . round($this->stats['costo_total'], 2));
        $this->log("🔄 Cambios de API: {$this->stats['cambios_api']}");
        $this->log("⏱️ Tiempo total: {$tiempoTotal}s ({$tiempoMinutos} minutos)");
        $this->log("⏱️ Tiempo en espera: {$this->stats['tiempo_espera_total']}s");
        $this->log("📊 Tokens totales: {$this->stats['tokens_totales']}");
        $this->log("⚡ Tokens/segundo: " . round($tokensPorSegundo, 2));
        
        if (!empty($this->stats['api_usada'])) {
            $this->log("\n📊 Uso por API:");
            foreach ($this->stats['api_usada'] as $apiId => $cantidad) {
                $nombre = $this->apisDisponibles[$apiId]['nombre_api'] ?? "API {$apiId}";
                $tokensApi = $this->stats['tokens_por_api'][$apiId] ?? 0;
                $promedio = $cantidad > 0 ? round($tokensApi / $cantidad) : 0;
                $this->log("  • {$nombre}:");
                $this->log("    - Capítulos: {$cantidad}");
                $this->log("    - Tokens: {$tokensApi}");
                $this->log("    - Promedio: {$promedio} tokens/cap");
            }
        }
        
        // Mostrar APIs agotadas por hoy
        if (!empty($this->apisAgotadasHoy)) {
            $this->log("\n⚠️ APIs agotadas por hoy:");
            foreach ($this->apisAgotadasHoy as $apiId => $value) {
                $nombre = $this->apisDisponibles[$apiId]['nombre_api'] ?? "API {$apiId}";
                $porcentaje = round(($this->apisDisponibles[$apiId]['tokens_usados_hoy'] / $this->apisDisponibles[$apiId]['tokens_por_dia']) * 100, 2);
                $this->log("  • {$nombre}: {$porcentaje}% usado");
            }
        }
    }
    
    private function desencriptarAPIKey($keyEncriptada) {
        if (empty($keyEncriptada)) {
            return '';
        }
        
        if (preg_match('/^\{\{(.+)\}\}$/', $keyEncriptada, $matches)) {
            return $_ENV[$matches[1]] ?? '';
        }
        
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
    echo "\n🚀 Iniciando traductor con rate limiting completo (v4)...\n\n";
    $job = new MultiApiTraductorJob();
    $job->ejecutar();
    
} catch (Exception $e) {
    echo "❌ Error fatal: " . $e->getMessage() . "\n";
    file_put_contents(
        BASE_PATH . '/storage/logs/fatal_error.log',
        "[" . date('Y-m-d H:i:s') . "] " . $e->getMessage() . "\n",
        FILE_APPEND
    );
    exit(1);
}

exit(0);