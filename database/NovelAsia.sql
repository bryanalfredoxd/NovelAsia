-- ============================================
-- BASE DE DATOS COMPLETA - NOVELASIA (CORREGIDA)
-- Sistema de novelas chinas traducidas por IA
-- Español como idioma principal
-- MySQL compatible
-- ============================================

-- Desactivar restricciones temporalmente para creación limpia
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- TABLA 1: USUARIOS (Sistema de usuarios)
-- ============================================
DROP TABLE IF EXISTS usuarios;
CREATE TABLE usuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    nombre VARCHAR(100),
    pais VARCHAR(50),
    idioma_preferido VARCHAR(10) DEFAULT 'es',
    avatar_url VARCHAR(255),
    rol ENUM('lector', 'traductor', 'moderador', 'admin') DEFAULT 'lector',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_login TIMESTAMP NULL DEFAULT NULL,
    esta_activo BOOLEAN DEFAULT TRUE,
    -- Campos específicos para APP --
    token_notificaciones VARCHAR(255) COMMENT 'APP: Token para push notifications',
    configuracion_app JSON COMMENT 'APP: Preferencias de tema, modo lectura, etc',
    dispositivo_registro VARCHAR(50) COMMENT 'APP: Tipo de dispositivo (iOS/Android)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 2: AUTORES (Información de autores)
-- ============================================
DROP TABLE IF EXISTS autores_novelas;
CREATE TABLE autores_novelas (
    id_autor INT PRIMARY KEY AUTO_INCREMENT,
    nombre_autor VARCHAR(100) NOT NULL,
    biografia TEXT,
    pais_origen VARCHAR(50) DEFAULT 'China',
    otras_obras JSON COMMENT 'IDs de otras novelas',
    seguidores INT DEFAULT 0,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    esta_verificado BOOLEAN DEFAULT FALSE,
    INDEX idx_nombre (nombre_autor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 3: NOVELAS (Catálogo principal)
-- ============================================
DROP TABLE IF EXISTS novelas;
CREATE TABLE novelas (
    id_novela INT PRIMARY KEY AUTO_INCREMENT,
    titulo_original VARCHAR(200) NOT NULL,
    titulo_ingles VARCHAR(200),
    autor_original VARCHAR(100),
    descripcion_original TEXT,
    url_original_qidian VARCHAR(255),
    generos JSON,
    etiquetas JSON,
    estado_original ENUM('en_progreso', 'completado', 'pausado', 'cancelado'),
    fecha_publicacion_original DATE,
    portada_url VARCHAR(255),
    -- Metadatos de scraping --
    fuente_scraping VARCHAR(100) COMMENT 'URL/sitio de donde se obtiene',
    ultimo_scraping TIMESTAMP NULL DEFAULT NULL,
    es_verificado BOOLEAN DEFAULT FALSE,
    -- Estadísticas --
    promedio_calificacion DECIMAL(3,2) DEFAULT 0,
    total_vistas BIGINT DEFAULT 0,
    total_favoritos INT DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_novelas_popularidad (promedio_calificacion DESC, total_vistas DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 4: CAPÍTULOS (Contenido de novelas)
-- ============================================
DROP TABLE IF EXISTS capitulos;
CREATE TABLE capitulos (
    id_capitulo INT PRIMARY KEY AUTO_INCREMENT,
    id_novela INT NOT NULL,
    numero_capitulo INT NOT NULL,
    titulo_original VARCHAR(200),
    contenido_original MEDIUMTEXT,
    fecha_publicacion_original TIMESTAMP NULL,
    fuente_url VARCHAR(255),
    orden_lectura INT,
    palabras_original INT,
    -- Estados y control --
    estado_capitulo ENUM('disponible', 'borrador', 'oculto', 'en_revision') DEFAULT 'disponible',
    enviado_traduccion BOOLEAN DEFAULT FALSE COMMENT 'Marcar si ya fue enviado a traducción IA',
    prioridad_traduccion INT DEFAULT 1 COMMENT 'Prioridad para traducción (1-10)',
    -- Para scraping --
    hash_contenido VARCHAR(64) COMMENT 'Hash SHA256 para detectar cambios',
    scrapeado_en TIMESTAMP NULL DEFAULT NULL,
    intentos_scraping INT DEFAULT 0 COMMENT 'Número de intentos de scraping',
    -- Auditoría --
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE CASCADE,
    UNIQUE KEY uk_novela_capitulo (id_novela, numero_capitulo),
    INDEX idx_capitulos_novela_num (id_novela, numero_capitulo),
    INDEX idx_estado_prioridad (estado_capitulo, prioridad_traduccion),
    INDEX idx_enviado_traduccion (enviado_traduccion, id_novela)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 5: TRADUCCIONES DE NOVELAS
-- ============================================
DROP TABLE IF EXISTS traducciones_novela;
CREATE TABLE traducciones_novela (
    id_traduccion_novela INT PRIMARY KEY AUTO_INCREMENT,
    id_novela INT NOT NULL,
    idioma VARCHAR(10) NOT NULL,
    titulo_traducido VARCHAR(200),
    descripcion_traducida TEXT,
    estado_traduccion ENUM('pendiente', 'en_progreso', 'completado', 'pausado') DEFAULT 'pendiente',
    fecha_inicio_traduccion TIMESTAMP NULL DEFAULT NULL,
    fecha_fin_traduccion TIMESTAMP NULL DEFAULT NULL,
    traductor_ia VARCHAR(50) COMMENT 'API/IA usada: DeepL, GPT, etc',
    configuracion_ia JSON COMMENT 'Parámetros de traducción',
    -- Campos para priorizar español --
    prioridad_idioma INT DEFAULT 1,
    es_principal BOOLEAN DEFAULT FALSE COMMENT 'TRUE para español como idioma principal',
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE CASCADE,
    UNIQUE KEY uk_novela_idioma (id_novela, idioma),
    INDEX idx_prioridad_espanol (es_principal, prioridad_idioma)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 6: TRADUCCIONES DE CAPÍTULOS
-- ============================================
DROP TABLE IF EXISTS traducciones_capitulo;
CREATE TABLE traducciones_capitulo (
    id_traduccion_capitulo INT PRIMARY KEY AUTO_INCREMENT,
    id_capitulo INT NOT NULL,
    idioma VARCHAR(10) NOT NULL,
    titulo_traducido VARCHAR(200),
    contenido_traducido TEXT,
    estado_traduccion ENUM('pendiente', 'en_progreso', 'completado', 'pausado') DEFAULT 'pendiente',
    fecha_traduccion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    traductor_ia VARCHAR(50),
    version_traduccion INT DEFAULT 1,
    calidad_estimada ENUM('baja', 'media', 'alta') DEFAULT 'media',
    palabras_traducidas INT,
    -- Para caché/optimización --
    contenido_comprimido MEDIUMBLOB COMMENT 'Contenido comprimido para optimizar almacenamiento',
    hash_traduccion VARCHAR(64),
    FOREIGN KEY (id_capitulo) REFERENCES capitulos(id_capitulo) ON DELETE CASCADE,
    UNIQUE KEY uk_capitulo_idioma_version (id_capitulo, idioma, version_traduccion),
    INDEX idx_capitulos_traducciones_es (idioma, estado_traduccion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 7: LECTURAS DE USUARIO
-- ============================================
DROP TABLE IF EXISTS lecturas_usuario;
CREATE TABLE lecturas_usuario (
    id_lectura INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_novela INT NOT NULL,
    id_capitulo_ultimo INT COMMENT 'Último capítulo leído',
    idioma_lectura VARCHAR(10),
    fecha_ultima_lectura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    progreso DECIMAL(5,2) DEFAULT 0 COMMENT 'Porcentaje leído',
    -- Campos específicos para APP --
    marcador_posicion INT COMMENT 'APP: Posición de scroll/lectura',
    velocidad_lectura INT COMMENT 'APP: Palabras por minuto estimadas',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE CASCADE,
    FOREIGN KEY (id_capitulo_ultimo) REFERENCES capitulos(id_capitulo) ON DELETE SET NULL,
    UNIQUE KEY uk_usuario_novela (id_usuario, id_novela),
    INDEX idx_lecturas_usuario_fecha (id_usuario, fecha_ultima_lectura DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 8: BIBLIOTECA DE USUARIO
-- ============================================
DROP TABLE IF EXISTS biblioteca_usuario;
CREATE TABLE biblioteca_usuario (
    id_biblioteca INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_novela INT NOT NULL,
    tipo ENUM('favoritos', 'leyendo', 'pendientes', 'leido') DEFAULT 'leyendo',
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Campos APP --
    notificar_nuevos_caps BOOLEAN DEFAULT TRUE COMMENT 'APP: Push notifications',
    orden_personalizado INT COMMENT 'APP: Orden en biblioteca personal',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE CASCADE,
    UNIQUE KEY uk_usuario_novela_tipo (id_usuario, id_novela, tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 9: FUENTES DE SCRAPING
-- ============================================
DROP TABLE IF EXISTS fuentes_scraping;
CREATE TABLE fuentes_scraping (
    id_fuente INT PRIMARY KEY AUTO_INCREMENT,
    nombre_fuente VARCHAR(100),
    url_base VARCHAR(255),
    tipo_fuente ENUM('qidian', 'pirata', 'oficial') DEFAULT 'pirata',
    estado ENUM('activa', 'inactiva', 'bloqueada') DEFAULT 'activa',
    intervalo_scraping_min INT DEFAULT 1440 COMMENT 'Minutos entre scrapings',
    ultimo_check TIMESTAMP NULL DEFAULT NULL,
    configuracion_scraper JSON COMMENT 'Selectores CSS, XPath, etc',
    tasa_exito DECIMAL(5,2) DEFAULT 100 COMMENT '% de requests exitosos'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 10: LOGS DE SCRAPING
-- ============================================
DROP TABLE IF EXISTS logs_scraping;
CREATE TABLE logs_scraping (
    id_log INT PRIMARY KEY AUTO_INCREMENT,
    id_fuente INT,
    tipo_operacion VARCHAR(50),
    estado ENUM('exito', 'error', 'parcial') DEFAULT 'exito',
    detalles TEXT,
    novelas_obtenidas INT DEFAULT 0,
    capitulos_obtenidos INT DEFAULT 0,
    duracion_segundos INT,
    fecha_ejecucion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_fuente) REFERENCES fuentes_scraping(id_fuente),
    INDEX idx_scraping_fuente (id_fuente, fecha_ejecucion DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 11: COMENTARIOS DE CAPÍTULOS
-- ============================================
DROP TABLE IF EXISTS comentarios_capitulo;
CREATE TABLE comentarios_capitulo (
    id_comentario INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_capitulo INT NOT NULL,
    contenido TEXT NOT NULL,
    idioma_comentario VARCHAR(10),
    fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    likes INT DEFAULT 0,
    -- Para moderación --
    es_spam BOOLEAN DEFAULT FALSE,
    editado BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_capitulo) REFERENCES capitulos(id_capitulo) ON DELETE CASCADE,
    INDEX idx_comentarios_capitulo (id_capitulo, fecha_publicacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 12: CALIFICACIONES DE NOVELAS
-- ============================================
DROP TABLE IF EXISTS calificaciones_novela;
CREATE TABLE calificaciones_novela (
    id_calificacion INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_novela INT NOT NULL,
    calificacion INT CHECK (calificacion BETWEEN 1 AND 5),
    fecha_calificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE CASCADE,
    UNIQUE KEY uk_usuario_novela (id_usuario, id_novela)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 13: SESIONES DE APP
-- ============================================
DROP TABLE IF EXISTS sesiones_app;
CREATE TABLE sesiones_app (
    id_sesion INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    dispositivo_id VARCHAR(255) COMMENT 'APP: ID único del dispositivo',
    sistema_operativo VARCHAR(20),
    version_app VARCHAR(20),
    token_sesion VARCHAR(255) UNIQUE,
    fecha_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_ultima_actividad TIMESTAMP NULL DEFAULT NULL,
    esta_activa BOOLEAN DEFAULT TRUE,
    ip_conexion VARCHAR(45),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    INDEX idx_sesiones_activas (esta_activa, fecha_ultima_actividad DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 14: DESCARGAS OFFLINE (APP)
-- ============================================
DROP TABLE IF EXISTS descargas_offline;
CREATE TABLE descargas_offline (
    id_descarga INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_capitulo INT NOT NULL,
    idioma_descargado VARCHAR(10),
    fecha_descarga TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tamaño_bytes INT,
    ruta_almacenamiento VARCHAR(500) COMMENT 'APP: Ruta local en dispositivo',
    expira_en TIMESTAMP NULL DEFAULT NULL COMMENT 'APP: Para gestión de caché',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_capitulo) REFERENCES capitulos(id_capitulo) ON DELETE CASCADE,
    UNIQUE KEY uk_usuario_capitulo_idioma (id_usuario, id_capitulo, idioma_descargado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 15: ESTADÍSTICAS DIARIAS
-- ============================================
DROP TABLE IF EXISTS estadisticas_diarias;
CREATE TABLE estadisticas_diarias (
    id_estadistica INT PRIMARY KEY AUTO_INCREMENT,
    fecha DATE NOT NULL,
    -- Métricas de usuarios --
    nuevos_usuarios INT DEFAULT 0,
    usuarios_activos INT DEFAULT 0,
    sesiones_totales INT DEFAULT 0,
    -- Métricas de contenido --
    novelas_agregadas INT DEFAULT 0,
    capitulos_traducidos INT DEFAULT 0,
    traducciones_espanol INT DEFAULT 0 COMMENT 'Traducciones específicas al español',
    -- Métricas de lectura --
    paginas_leidas BIGINT DEFAULT 0,
    tiempo_lectura_total_min INT DEFAULT 0,
    -- Métricas de scraping --
    scrapings_exitosos INT DEFAULT 0,
    scrapings_fallidos INT DEFAULT 0,
    -- Métricas financieras (si aplica) --
    ingresos_anuncios DECIMAL(10,2) DEFAULT 0,
    suscripciones_nuevas INT DEFAULT 0,
    UNIQUE KEY uk_fecha (fecha),
    INDEX idx_fecha_estadisticas (fecha DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 16: ESTADÍSTICAS POR HORA DE NOVELAS
-- ============================================
DROP TABLE IF EXISTS estadisticas_novelas_hora;
CREATE TABLE estadisticas_novelas_hora (
    id_estadistica_hora INT PRIMARY KEY AUTO_INCREMENT,
    id_novela INT NOT NULL,
    fecha_hora DATETIME NOT NULL,
    vistas INT DEFAULT 0,
    lecturas_completas INT DEFAULT 0,
    favoritos_agregados INT DEFAULT 0,
    comentarios_nuevos INT DEFAULT 0,
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE CASCADE,
    INDEX idx_fecha_hora (fecha_hora),
    INDEX idx_novela_hora (id_novela, fecha_hora)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 17: MÉTRICAS DE USUARIO
-- ============================================
DROP TABLE IF EXISTS metricas_usuario;
CREATE TABLE metricas_usuario (
    id_metrica INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    fecha DATE NOT NULL,
    -- Métricas de actividad --
    capitulos_leidos INT DEFAULT 0,
    tiempo_lectura_min INT DEFAULT 0,
    comentarios_realizados INT DEFAULT 0,
    -- Métricas de interacción --
    novelas_favoritas_agregadas INT DEFAULT 0,
    calificaciones_dadas INT DEFAULT 0,
    -- Métricas APP específicas --
    sesiones_app INT DEFAULT 0 COMMENT 'APP: Número de inicios de sesión',
    notificaciones_recibidas INT DEFAULT 0 COMMENT 'APP: Push notifications',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    UNIQUE KEY uk_usuario_fecha (id_usuario, fecha),
    INDEX idx_usuario_metricas (id_usuario, fecha DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 18: APIS DE TRADUCCIÓN
-- ============================================
DROP TABLE IF EXISTS apis_traduccion;
CREATE TABLE apis_traduccion (
    id_api INT PRIMARY KEY AUTO_INCREMENT,
    nombre_api VARCHAR(50) NOT NULL COMMENT 'DeepL, GPT-4, Claude, etc',
    tipo_api VARCHAR(20) COMMENT 'traduccion, resumen, correccion',
    endpoint_url VARCHAR(255),
    api_key VARCHAR(255) COMMENT 'Encriptada en producción',
    modelo VARCHAR(50) COMMENT 'gpt-4, claude-3, etc',
    prioridad INT DEFAULT 1 COMMENT '1=mayor prioridad, 10=menor',
    -- Límites de uso --
    tokens_por_minuto INT DEFAULT 1000,
    tokens_por_dia INT DEFAULT 100000,
    requests_por_minuto INT DEFAULT 10,
    costo_por_millon_tokens DECIMAL(10,4) DEFAULT 0,
    -- Estadísticas de uso --
    tokens_usados_hoy INT DEFAULT 0,
    requests_hoy INT DEFAULT 0,
    ultimo_uso TIMESTAMP NULL DEFAULT NULL,
    -- Configuración específica para chino->español --
    especialidad_chino_es BOOLEAN DEFAULT TRUE,
    configuracion_chino JSON COMMENT 'Parámetros específicos para chino',
    -- Estado --
    esta_activa BOOLEAN DEFAULT TRUE,
    modo_reserva BOOLEAN DEFAULT FALSE COMMENT 'Solo usar si otras fallan',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_prioridad_activa (prioridad, esta_activa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 19: LOGS DE API DE TRADUCCIÓN
-- ============================================
DROP TABLE IF EXISTS logs_api_traduccion;
CREATE TABLE logs_api_traduccion (
    id_log INT PRIMARY KEY AUTO_INCREMENT,
    id_api INT NOT NULL,
    id_capitulo INT NULL,
    tipo_operacion VARCHAR(50),
    tokens_usados INT,
    duracion_ms INT,
    costo_estimado DECIMAL(10,6),
    estado ENUM('exito', 'error', 'limite_excedido') DEFAULT 'exito',
    codigo_error VARCHAR(50),
    mensaje_error TEXT,
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_api) REFERENCES apis_traduccion(id_api),
    FOREIGN KEY (id_capitulo) REFERENCES capitulos(id_capitulo) ON DELETE SET NULL,
    INDEX idx_fecha_api (fecha_solicitud, id_api)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 20: ANUNCIANTES
-- ============================================
DROP TABLE IF EXISTS anunciantes;
CREATE TABLE anunciantes (
    id_anunciante INT PRIMARY KEY AUTO_INCREMENT,
    nombre_empresa VARCHAR(100),
    contacto_email VARCHAR(100),
    telefono VARCHAR(20),
    pais VARCHAR(50),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    esta_activo BOOLEAN DEFAULT TRUE,
    presupuesto_mensual DECIMAL(10,2) DEFAULT 0,
    INDEX idx_anunciantes_activos (esta_activo, fecha_registro DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 21: CAMPAÑAS DE ANUNCIOS (CORREGIDA)
-- ============================================
DROP TABLE IF EXISTS campañas_anuncios;
CREATE TABLE campañas_anuncios (
    id_campaña INT PRIMARY KEY AUTO_INCREMENT,
    id_anunciante INT NOT NULL,
    nombre_campaña VARCHAR(100),
    -- Ubicaciones de anuncios --
    ubicaciones JSON NOT NULL COMMENT '["inicio_capitulo", "sidebar", "entre_capitulos"]',
    tipo_anuncio ENUM('banner', 'intersticial', 'video', 'native') DEFAULT 'banner',
    -- Configuración de targeting --
    targeting_idioma VARCHAR(10) DEFAULT 'es',
    targeting_paises JSON COMMENT '["MX", "ES", "AR"]',
    targeting_generos JSON COMMENT '["xianxia", "wuxia"]',
    -- Presupuesto y programación --
    presupuesto_total DECIMAL(10,2),
    presupuesto_diario DECIMAL(10,2),
    fecha_inicio DATE,
    fecha_fin DATE,
    -- Contenido del anuncio --
    titulo_anuncio VARCHAR(200),
    imagen_url VARCHAR(255),
    url_destino VARCHAR(255),
    texto_boton VARCHAR(50),
    -- Métricas --
    impresiones_maximas INT,
    clics_maximos INT,
    estado ENUM('activa', 'pausada', 'finalizada', 'pendiente') DEFAULT 'pendiente',
    FOREIGN KEY (id_anunciante) REFERENCES anunciantes(id_anunciante),
    INDEX idx_estado_fechas (estado, fecha_inicio, fecha_fin),
    INDEX idx_anuncios_activos (estado, fecha_inicio, fecha_fin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 22: ANUNCIOS MOSTRADOS
-- ============================================
DROP TABLE IF EXISTS anuncios_mostrados;
CREATE TABLE anuncios_mostrados (
    id_mostrado INT PRIMARY KEY AUTO_INCREMENT,
    id_campaña INT NOT NULL,
    id_usuario INT NULL,
    id_novela INT NULL,
    id_capitulo INT NULL,
    tipo_evento ENUM('impresion', 'clic', 'conversion') DEFAULT 'impresion',
    ubicacion VARCHAR(50),
    idioma_usuario VARCHAR(10),
    -- Datos contextuales --
    dispositivo ENUM('web', 'android', 'ios') DEFAULT 'web',
    pais_usuario VARCHAR(10),
    -- Monetización --
    ingreso_generado DECIMAL(8,4) DEFAULT 0,
    fecha_evento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_campaña) REFERENCES campañas_anuncios(id_campaña),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL,
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE SET NULL,
    FOREIGN KEY (id_capitulo) REFERENCES capitulos(id_capitulo) ON DELETE SET NULL,
    INDEX idx_fecha_campaña (fecha_evento, id_campaña),
    INDEX idx_usuario_fecha (id_usuario, fecha_evento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 23: PLANES DE SUSCRIPCIÓN
-- ============================================
DROP TABLE IF EXISTS planes_suscripcion;
CREATE TABLE planes_suscripcion (
    id_plan INT PRIMARY KEY AUTO_INCREMENT,
    nombre_plan VARCHAR(50) NOT NULL,
    descripcion TEXT,
    -- Beneficios --
    sin_anuncios BOOLEAN DEFAULT FALSE,
    lectura_modo_offline BOOLEAN DEFAULT FALSE COMMENT 'APP: Descargar capítulos',
    capitulos_avanzados BOOLEAN DEFAULT FALSE COMMENT 'Acceso a capítulos antes',
    limite_descargas INT DEFAULT 0 COMMENT 'APP: Capítulos para offline',
    -- Precios --
    precio_mensual DECIMAL(6,2),
    precio_anual DECIMAL(8,2),
    moneda VARCHAR(3) DEFAULT 'USD',
    -- Estado --
    esta_activo BOOLEAN DEFAULT TRUE,
    orden_visual INT DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_planes_activos (esta_activo, orden_visual)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 24: SUSCRIPCIONES DE USUARIO (CORREGIDA)
-- ============================================
DROP TABLE IF EXISTS suscripciones_usuario;
CREATE TABLE suscripciones_usuario (
    id_suscripcion INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_plan INT NOT NULL,
    -- Estado de suscripción --
    estado ENUM('activa', 'cancelada', 'expirada', 'pendiente_pago') DEFAULT 'pendiente_pago',
    metodo_pago ENUM('paypal', 'stripe', 'mercado_pago', 'tarjeta') DEFAULT 'paypal',
    -- Período de suscripción --
    fecha_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_vencimiento TIMESTAMP NULL,
    fecha_cancelacion TIMESTAMP NULL,
    -- Información de pago --
    id_pago_externo VARCHAR(100) COMMENT 'ID de transacción en pasarela',
    monto_pagado DECIMAL(8,2),
    proximo_pago_estimado TIMESTAMP NULL,
    renovacion_automatica BOOLEAN DEFAULT TRUE,
    -- Campos APP específicos --
    dispositivo_compra VARCHAR(50) COMMENT 'APP: Dispositivo donde se suscribió',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_plan) REFERENCES planes_suscripcion(id_plan),
    INDEX idx_estado_vencimiento (estado, fecha_vencimiento),
    INDEX idx_usuario_activa (id_usuario, estado),
    INDEX idx_suscripciones_activas (estado, fecha_vencimiento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 25: HISTORIAL DE PAGOS
-- ============================================
DROP TABLE IF EXISTS historial_pagos;
CREATE TABLE historial_pagos (
    id_pago INT PRIMARY KEY AUTO_INCREMENT,
    id_suscripcion INT NOT NULL,
    id_usuario INT NOT NULL,
    monto DECIMAL(8,2),
    moneda VARCHAR(3) DEFAULT 'USD',
    metodo_pago VARCHAR(50),
    id_transaccion VARCHAR(100),
    estado_pago ENUM('completado', 'pendiente', 'fallido', 'reembolsado') DEFAULT 'pendiente',
    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_confirmacion TIMESTAMP NULL,
    datos_pago JSON COMMENT 'Respuesta completa de la pasarela',
    FOREIGN KEY (id_suscripcion) REFERENCES suscripciones_usuario(id_suscripcion),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    INDEX idx_fecha_estado (fecha_pago, estado_pago)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 26: RECOMENDACIONES DE USUARIOS (CORREGIDA)
-- ============================================
DROP TABLE IF EXISTS recomendaciones_usuarios;
CREATE TABLE recomendaciones_usuarios (
    id_recomendacion INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    -- Información de la novela recomendada --
    titulo_novela VARCHAR(200) NOT NULL,
    autor_novela VARCHAR(100),
    url_referencia VARCHAR(500) COMMENT 'Enlace a Qidian o sitio original',
    generos_sugeridos JSON,
    descripcion TEXT,
    motivo_recomendacion TEXT,
    -- Estado de la recomendación --
    estado ENUM('pendiente', 'revisando', 'aprobada', 'rechazada', 'ya_existe') DEFAULT 'pendiente',
    id_novela_asignada INT NULL COMMENT 'Si ya existe, se vincula',
    -- Metadatos --
    fecha_recomendacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_revision TIMESTAMP NULL,
    revisado_por INT NULL COMMENT 'ID del moderador/admin',
    comentarios_revisor TEXT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_novela_asignada) REFERENCES novelas(id_novela) ON DELETE SET NULL,
    FOREIGN KEY (revisado_por) REFERENCES usuarios(id_usuario) ON DELETE SET NULL,
    INDEX idx_recomendaciones_pendientes (estado, fecha_recomendacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 27: REPORTES DE ERRORES (CORREGIDA)
-- ============================================
DROP TABLE IF EXISTS reportes_errores;
CREATE TABLE reportes_errores (
    id_reporte INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    -- Contexto del error --
    tipo_error ENUM('traduccion', 'contenido', 'formato', 'scraping', 'otro') DEFAULT 'traduccion',
    id_novela INT NULL,
    id_capitulo INT NULL,
    idioma_capitulo VARCHAR(10) DEFAULT 'es',
    -- Detalles del error --
    titulo_reporte VARCHAR(200),
    descripcion_error TEXT NOT NULL,
    texto_problematico TEXT COMMENT 'Texto específico con error',
    sugerencia_correccion TEXT,
    -- Estado del reporte --
    estado ENUM('pendiente', 'revisando', 'corregido', 'duplicado', 'invalidado') DEFAULT 'pendiente',
    prioridad ENUM('baja', 'media', 'alta', 'critica') DEFAULT 'media',
    -- Seguimiento --
    fecha_reporte TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_revision TIMESTAMP NULL,
    revisado_por INT NULL,
    acciones_tomadas TEXT,
    correccion_aplicada BOOLEAN DEFAULT FALSE,
    fecha_correccion TIMESTAMP NULL,
    -- Para errores de scraping --
    fuente_scraping VARCHAR(100),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE SET NULL,
    FOREIGN KEY (id_capitulo) REFERENCES capitulos(id_capitulo) ON DELETE SET NULL,
    FOREIGN KEY (revisado_por) REFERENCES usuarios(id_usuario) ON DELETE SET NULL,
    INDEX idx_estado_prioridad (estado, prioridad),
    INDEX idx_fecha_tipo (fecha_reporte, tipo_error),
    INDEX idx_reportes_pendientes (estado, prioridad, fecha_reporte)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 28: COLECCIONES DE NOVELAS
-- ============================================
DROP TABLE IF EXISTS colecciones_novelas;
CREATE TABLE colecciones_novelas (
    id_coleccion INT PRIMARY KEY AUTO_INCREMENT,
    nombre_coleccion VARCHAR(100) NOT NULL,
    descripcion TEXT,
    creador_id INT NOT NULL,
    es_publica BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultima_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (creador_id) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    INDEX idx_colecciones_publicas (es_publica, fecha_creacion DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 29: CONTENIDO DE COLECCIONES
-- ============================================
DROP TABLE IF EXISTS colecciones_contenido;
CREATE TABLE colecciones_contenido (
    id_coleccion INT NOT NULL,
    id_novela INT NOT NULL,
    orden INT DEFAULT 1,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    comentario TEXT,
    PRIMARY KEY (id_coleccion, id_novela),
    FOREIGN KEY (id_coleccion) REFERENCES colecciones_novelas(id_coleccion) ON DELETE CASCADE,
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 30: NOTIFICACIONES DEL SISTEMA
-- ============================================
DROP TABLE IF EXISTS notificaciones_sistema;
CREATE TABLE notificaciones_sistema (
    id_notificacion INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    tipo_notificacion ENUM('nuevo_capitulo', 'suscripcion', 'sistema', 'social') DEFAULT 'sistema',
    titulo VARCHAR(200),
    contenido TEXT,
    url_accion VARCHAR(255),
    -- Para notificaciones de capítulos --
    id_novela INT NULL,
    id_capitulo INT NULL,
    -- Estado --
    leida BOOLEAN DEFAULT FALSE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_lectura TIMESTAMP NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE SET NULL,
    FOREIGN KEY (id_capitulo) REFERENCES capitulos(id_capitulo) ON DELETE SET NULL,
    INDEX idx_usuario_leida (id_usuario, leida, fecha_creacion DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 31: CONFIGURACIÓN DEL SISTEMA
-- ============================================
DROP TABLE IF EXISTS configuracion_sistema;
CREATE TABLE configuracion_sistema (
    id_config INT PRIMARY KEY AUTO_INCREMENT,
    clave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT,
    tipo ENUM('string', 'integer', 'boolean', 'json', 'array') DEFAULT 'string',
    categoria VARCHAR(50) COMMENT 'general, scraping, traduccion, anuncios',
    descripcion TEXT,
    editable BOOLEAN DEFAULT TRUE,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_config_categoria (categoria, clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TRIGGERS PARA LA BASE DE DATOS
-- ============================================

-- Trigger 1: Actualizar estadísticas de novelas al agregar favoritos
DELIMITER //
CREATE TRIGGER trg_actualizar_favoritos_novela
AFTER INSERT ON biblioteca_usuario
FOR EACH ROW
BEGIN
    IF NEW.tipo = 'favoritos' THEN
        UPDATE novelas 
        SET total_favoritos = total_favoritos + 1
        WHERE id_novela = NEW.id_novela;
    END IF;
END//
DELIMITER ;

-- Trigger 2: Actualizar calificación promedio de novela
DELIMITER //
CREATE TRIGGER trg_actualizar_calificacion_novela
AFTER INSERT ON calificaciones_novela
FOR EACH ROW
BEGIN
    DECLARE avg_cal DECIMAL(3,2);
    
    SELECT AVG(calificacion) INTO avg_cal
    FROM calificaciones_novela
    WHERE id_novela = NEW.id_novela;
    
    UPDATE novelas 
    SET promedio_calificacion = COALESCE(avg_cal, 0)
    WHERE id_novela = NEW.id_novela;
END//
DELIMITER ;

-- Trigger 3: Crear traducción al español automáticamente para nuevos capítulos
DELIMITER //
CREATE TRIGGER trg_crear_traduccion_espanol_capitulo
AFTER INSERT ON capitulos
FOR EACH ROW
BEGIN
    -- Marcar capítulo para traducción al español
    INSERT INTO traducciones_capitulo (id_capitulo, idioma, estado_traduccion)
    VALUES (NEW.id_capitulo, 'es', 'pendiente')
    ON DUPLICATE KEY UPDATE estado_traduccion = 'pendiente';
END//
DELIMITER ;

-- Trigger 4: Crear traducción al español automáticamente para nuevas novelas
DELIMITER //
CREATE TRIGGER trg_crear_traduccion_espanol_novela
AFTER INSERT ON novelas
FOR EACH ROW
BEGIN
    -- Crear entrada de traducción al español automáticamente
    INSERT INTO traducciones_novela (id_novela, idioma, estado_traduccion, es_principal, prioridad_idioma)
    VALUES (NEW.id_novela, 'es', 'pendiente', TRUE, 1)
    ON DUPLICATE KEY UPDATE estado_traduccion = 'pendiente';
END//
DELIMITER ;

-- Trigger 5: Controlar límites de API automáticamente
DELIMITER //
CREATE TRIGGER trg_actualizar_contador_api
AFTER INSERT ON logs_api_traduccion
FOR EACH ROW
BEGIN
    -- Actualizar contadores de uso de API
    UPDATE apis_traduccion 
    SET 
        tokens_usados_hoy = tokens_usados_hoy + COALESCE(NEW.tokens_usados, 0),
        requests_hoy = requests_hoy + 1,
        ultimo_uso = NEW.fecha_solicitud
    WHERE id_api = NEW.id_api;
END//
DELIMITER ;

-- Trigger 6: Notificar nuevo capítulo traducido a usuarios suscritos
DELIMITER //
CREATE TRIGGER trg_notificar_nuevo_capitulo
AFTER UPDATE ON traducciones_capitulo
FOR EACH ROW
BEGIN
    DECLARE novel_title VARCHAR(200);
    DECLARE chapter_num INT;
    DECLARE novel_id INT;
    
    -- Solo notificar cuando se completa una traducción al español
    IF NEW.estado_traduccion = 'completado' AND NEW.idioma = 'es' AND OLD.estado_traduccion != 'completado' THEN
        -- Obtener información del capítulo y novela
        SELECT n.titulo_original, c.numero_capitulo, c.id_novela INTO novel_title, chapter_num, novel_id
        FROM capitulos c
        JOIN novelas n ON c.id_novela = n.id_novela
        WHERE c.id_capitulo = NEW.id_capitulo;
        
        -- Notificar a usuarios que tienen esta novela en favoritos o leyendo
        INSERT INTO notificaciones_sistema (id_usuario, tipo_notificacion, titulo, contenido, id_novela, id_capitulo)
        SELECT 
            bu.id_usuario,
            'nuevo_capitulo',
            CONCAT('Nuevo capítulo disponible: ', novel_title),
            CONCAT('Capítulo ', chapter_num, ' traducido al español y listo para leer'),
            novel_id,
            NEW.id_capitulo
        FROM biblioteca_usuario bu
        WHERE bu.id_novela = novel_id
        AND bu.tipo IN ('favoritos', 'leyendo')
        AND bu.notificar_nuevos_caps = TRUE
        AND bu.id_usuario IN (
            SELECT id_usuario FROM usuarios WHERE esta_activo = TRUE
        );
    END IF;
END//
DELIMITER ;

-- Trigger 7: Controlar suscripciones activas vs anuncios
DELIMITER //
CREATE TRIGGER trg_verificar_suscripcion_activa
BEFORE INSERT ON anuncios_mostrados
FOR EACH ROW
BEGIN
    DECLARE user_has_active_subscription BOOLEAN;
    
    -- Verificar si el usuario tiene suscripción activa sin anuncios
    IF NEW.id_usuario IS NOT NULL THEN
        SELECT COUNT(*) > 0 INTO user_has_active_subscription
        FROM suscripciones_usuario su
        JOIN planes_suscripcion ps ON su.id_plan = ps.id_plan
        WHERE su.id_usuario = NEW.id_usuario
        AND su.estado = 'activa'
        AND su.fecha_vencimiento > NOW()
        AND ps.sin_anuncios = TRUE;
        
        -- Si tiene suscripción activa sin anuncios, cancelar la inserción del anuncio
        IF user_has_active_subscription THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Usuario con suscripción activa sin anuncios';
        END IF;
    END IF;
END//
DELIMITER ;

-- Trigger 8: Registrar vista de novela automáticamente
DELIMITER //
CREATE TRIGGER trg_registrar_vista_novela
AFTER INSERT ON lecturas_usuario
FOR EACH ROW
BEGIN
    UPDATE novelas 
    SET total_vistas = total_vistas + 1
    WHERE id_novela = NEW.id_novela;
END//
DELIMITER ;

-- Trigger 9: Actualizar última actividad de sesión
DELIMITER //
CREATE TRIGGER trg_actualizar_actividad_sesion
AFTER INSERT ON lecturas_usuario
FOR EACH ROW
BEGIN
    UPDATE sesiones_app 
    SET fecha_ultima_actividad = NOW()
    WHERE id_usuario = NEW.id_usuario 
    AND esta_activa = TRUE
    ORDER BY fecha_inicio DESC
    LIMIT 1;
END//
DELIMITER ;

-- ============================================
-- EVENTOS PROGRAMADOS
-- ============================================

-- Evento 1: Resetear contadores diarios de APIs
DELIMITER //
CREATE EVENT IF NOT EXISTS reset_contadores_api_diarios
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_DATE + INTERVAL 1 DAY + INTERVAL 3 HOUR
DO
BEGIN
    UPDATE apis_traduccion 
    SET tokens_usados_hoy = 0, requests_hoy = 0
    WHERE DATE(ultimo_uso) < CURDATE();
END//
DELIMITER ;

-- Evento 2: Desactivar sesiones inactivas (APP)
DELIMITER //
CREATE EVENT IF NOT EXISTS desactivar_sesiones_inactivas
ON SCHEDULE EVERY 1 HOUR
STARTS CURRENT_TIMESTAMP
DO
BEGIN
    UPDATE sesiones_app 
    SET esta_activa = FALSE
    WHERE esta_activa = TRUE
    AND fecha_ultima_actividad < NOW() - INTERVAL 7 DAY;
END//
DELIMITER ;

-- Evento 3: Actualizar estadísticas diarias
DELIMITER //
CREATE EVENT IF NOT EXISTS actualizar_estadisticas_diarias
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_DATE + INTERVAL 1 DAY + INTERVAL 4 HOUR
DO
BEGIN
    -- Insertar estadísticas del día anterior
    INSERT INTO estadisticas_diarias (fecha, nuevos_usuarios, usuarios_activos)
    SELECT 
        CURDATE() - INTERVAL 1 DAY,
        COUNT(CASE WHEN DATE(fecha_registro) = CURDATE() - INTERVAL 1 DAY THEN 1 END),
        COUNT(DISTINCT id_usuario)
    FROM usuarios 
    WHERE DATE(ultimo_login) = CURDATE() - INTERVAL 1 DAY
    AND esta_activo = TRUE;
END//
DELIMITER ;

-- ============================================
-- VISTAS ÚTILES PARA EL SISTEMA
-- ============================================

-- Vista 1: Capítulos pendientes de traducción al español
CREATE OR REPLACE VIEW vista_capitulos_pendientes_es AS
SELECT 
    c.id_capitulo,
    c.id_novela,
    n.titulo_original,
    c.numero_capitulo,
    c.titulo_original as titulo_capitulo,
    c.palabras_original,
    c.fecha_publicacion_original,
    c.scrapeado_en
FROM capitulos c
JOIN novelas n ON c.id_novela = n.id_novela
LEFT JOIN traducciones_capitulo tc ON c.id_capitulo = tc.id_capitulo 
    AND tc.idioma = 'es' 
    AND tc.estado_traduccion = 'completado'
WHERE tc.id_traduccion_capitulo IS NULL
ORDER BY n.total_vistas DESC, c.numero_capitulo ASC;

-- Vista 2: Estadísticas de traducción por idioma
CREATE OR REPLACE VIEW vista_estadisticas_traduccion AS
SELECT 
    idioma,
    COUNT(*) as total_capitulos,
    SUM(CASE WHEN estado_traduccion = 'completado' THEN 1 ELSE 0 END) as completados,
    SUM(CASE WHEN estado_traduccion = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
    SUM(CASE WHEN estado_traduccion = 'en_progreso' THEN 1 ELSE 0 END) as en_progreso,
    SUM(palabras_traducidas) as total_palabras
FROM traducciones_capitulo
GROUP BY idioma
ORDER BY 
    CASE WHEN idioma = 'es' THEN 1 ELSE 2 END,
    completados DESC;

-- Vista 3: Usuarios premium activos
CREATE OR REPLACE VIEW vista_usuarios_premium AS
SELECT 
    u.id_usuario,
    u.username,
    u.email,
    s.id_plan,
    p.nombre_plan,
    s.fecha_inicio,
    s.fecha_vencimiento,
    DATEDIFF(s.fecha_vencimiento, CURDATE()) as dias_restantes
FROM usuarios u
JOIN suscripciones_usuario s ON u.id_usuario = s.id_usuario
JOIN planes_suscripcion p ON s.id_plan = p.id_plan
WHERE s.estado = 'activa'
AND s.fecha_vencimiento > NOW()
AND p.sin_anuncios = TRUE;

-- Vista 4: Novelas más populares (español)
CREATE OR REPLACE VIEW vista_novelas_populares_es AS
SELECT 
    n.id_novela,
    n.titulo_original,
    tn.titulo_traducido,
    n.autor_original,
    n.generos,
    n.estado_original,
    n.promedio_calificacion,
    n.total_vistas,
    n.total_favoritos,
    COUNT(DISTINCT tc.id_capitulo) as capitulos_traducidos_es
FROM novelas n
LEFT JOIN traducciones_novela tn ON n.id_novela = tn.id_novela AND tn.idioma = 'es'
LEFT JOIN capitulos c ON n.id_novela = c.id_novela
LEFT JOIN traducciones_capitulo tc ON c.id_capitulo = tc.id_capitulo 
    AND tc.idioma = 'es' 
    AND tc.estado_traduccion = 'completado'
WHERE tn.idioma = 'es'
GROUP BY n.id_novela
ORDER BY n.total_vistas DESC, n.promedio_calificacion DESC;

-- Vista 5: APIs disponibles para chino-español
CREATE OR REPLACE VIEW vista_apis_chino_es AS
SELECT 
    id_api,
    nombre_api,
    modelo,
    prioridad,
    tokens_por_minuto - tokens_usados_hoy as tokens_disponibles_minuto,
    tokens_por_dia - tokens_usados_hoy as tokens_disponibles_dia,
    especialidad_chino_es,
    esta_activa
FROM apis_traduccion
WHERE especialidad_chino_es = TRUE
AND esta_activa = TRUE
ORDER BY prioridad ASC;

-- ============================================
-- CONFIGURACIÓN INICIAL DEL SISTEMA
-- ============================================

-- Insertar configuración por defecto priorizando español
INSERT INTO configuracion_sistema (clave, valor, tipo, categoria, descripcion) VALUES
('idioma_principal', 'es', 'string', 'general', 'Idioma principal de la plataforma'),
('traduccion_automatica_es', 'true', 'boolean', 'traduccion', 'Traducir automáticamente al español'),
('prioridad_espanol', '1', 'integer', 'traduccion', 'Prioridad para traducciones al español (1-10)'),
('limite_capitulos_diarios_es', '50', 'integer', 'scraping', 'Máximo de capítulos a traducir al español por día'),
('api_principal_chino_es', '1', 'integer', 'traduccion', 'ID de API principal para chino-español'),
('scraping_automatico', 'true', 'boolean', 'scraping', 'Activar scraping automático'),
('intervalo_scraping_minutos', '360', 'integer', 'scraping', 'Intervalo entre scrapings en minutos'),
('modo_mantenimiento', 'false', 'boolean', 'general', 'Modo mantenimiento del sitio'),
('anuncios_activos', 'true', 'boolean', 'anuncios', 'Mostrar anuncios en el sitio'),
('registro_abierto', 'true', 'boolean', 'general', 'Registro de nuevos usuarios abierto');

-- Insertar planes de suscripción por defecto
INSERT INTO planes_suscripcion (nombre_plan, descripcion, sin_anuncios, lectura_modo_offline, limite_descargas, precio_mensual, precio_anual, orden_visual) VALUES
('Gratuito', 'Acceso básico a novelas con anuncios', FALSE, FALSE, 0, 0.00, 0.00, 1),
('Premium', 'Sin anuncios y descargas offline', TRUE, TRUE, 50, 4.99, 49.99, 2),
('VIP', 'Todo en Premium más acceso anticipado', TRUE, TRUE, 200, 9.99, 99.99, 3);

-- Reactivar verificaciones de claves foráneas
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- FIN DEL SCRIPT DE CREACIÓN DE BASE DE DATOS
-- ============================================