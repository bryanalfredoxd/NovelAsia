-- ============================================
-- BASE DE DATOS COMPLETA - NOVELASIA
-- ============================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS NovelAsia;
USE NovelAsia;

-- ============================================
-- 1. TABLAS PRINCIPALES
-- ============================================

-- Tabla de usuarios
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
    ultimo_login TIMESTAMP NULL,
    esta_activo BOOLEAN DEFAULT TRUE,
    -- Campos específicos para APP --
    token_notificaciones VARCHAR(255) COMMENT 'APP: Token para push notifications',
    configuracion_app JSON COMMENT 'APP: Preferencias de tema, modo lectura, etc',
    dispositivo_registro VARCHAR(50) COMMENT 'APP: Tipo de dispositivo (iOS/Android)'
);

-- Tabla de novelas
CREATE TABLE novelas (
    id_novela INT PRIMARY KEY AUTO_INCREMENT,
    titulo_original VARCHAR(200) NOT NULL,
    titulo_ingles VARCHAR(200),
    autor_original VARCHAR(100),
    descripcion_original TEXT,
    url_original_qidian VARCHAR(255),
    generos JSON, -- ["fantasia", "xianxia", "romance"]
    etiquetas JSON, -- ["cultivacion", "reencarnacion", "harem"]
    estado_original ENUM('en_progreso', 'completado', 'pausado', 'cancelado'),
    fecha_publicacion_original DATE,
    portada_url VARCHAR(255),
    -- Metadatos de scraping --
    fuente_scraping VARCHAR(100) COMMENT 'URL/sitio de donde se obtiene',
    ultimo_scraping TIMESTAMP,
    es_verificado BOOLEAN DEFAULT FALSE,
    -- Estadísticas --
    promedio_calificacion DECIMAL(3,2) DEFAULT 0,
    total_vistas BIGINT DEFAULT 0,
    total_favoritos INT DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de capítulos
CREATE TABLE capitulos (
    id_capitulo INT PRIMARY KEY AUTO_INCREMENT,
    id_novela INT NOT NULL,
    numero_capitulo INT NOT NULL,
    titulo_original VARCHAR(200),
    contenido_original TEXT,
    fecha_publicacion_original TIMESTAMP NULL,
    fuente_url VARCHAR(255),
    orden_lectura INT,
    palabras_original INT,
    -- Para scraping --
    hash_contenido VARCHAR(64) COMMENT 'Hash para detectar cambios',
    scrapeado_en TIMESTAMP,
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE CASCADE,
    UNIQUE KEY (id_novela, numero_capitulo)
);

-- ============================================
-- 2. TABLAS DE TRADUCCIONES
-- ============================================

-- Tabla de traducciones de novelas
CREATE TABLE traducciones_novela (
    id_traduccion_novela INT PRIMARY KEY AUTO_INCREMENT,
    id_novela INT NOT NULL,
    idioma VARCHAR(10) NOT NULL, -- 'es', 'en', 'fr', etc
    titulo_traducido VARCHAR(200),
    descripcion_traducida TEXT,
    estado_traduccion ENUM('pendiente', 'en_progreso', 'completado', 'pausado') DEFAULT 'pendiente',
    prioridad_idioma INT DEFAULT 1,
    es_principal BOOLEAN DEFAULT FALSE COMMENT 'TRUE para español como idioma principal',
    fecha_inicio_traduccion TIMESTAMP NULL,
    fecha_fin_traduccion TIMESTAMP NULL,
    traductor_ia VARCHAR(50) COMMENT 'API/IA usada: DeepL, GPT, etc',
    configuracion_ia JSON COMMENT 'Parámetros de traducción',
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE CASCADE,
    UNIQUE KEY (id_novela, idioma)
);

-- Tabla de traducciones de capítulos
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
    UNIQUE KEY (id_capitulo, idioma, version_traduccion)
);

-- ============================================
-- 3. TABLAS DE LECTURA/USUARIO
-- ============================================

-- Tabla de lecturas de usuario
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
    UNIQUE KEY (id_usuario, id_novela)
);

-- Tabla de biblioteca de usuario
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
    UNIQUE KEY (id_usuario, id_novela, tipo)
);

-- ============================================
-- 4. TABLAS DE SCRAPING Y MONITOREO
-- ============================================

-- Tabla de fuentes de scraping
CREATE TABLE fuentes_scraping (
    id_fuente INT PRIMARY KEY AUTO_INCREMENT,
    nombre_fuente VARCHAR(100),
    url_base VARCHAR(255),
    tipo_fuente ENUM('qidian', 'pirata', 'oficial') DEFAULT 'pirata',
    estado ENUM('activa', 'inactiva', 'bloqueada') DEFAULT 'activa',
    intervalo_scraping_min INT DEFAULT 1440 COMMENT 'Minutos entre scrapings',
    ultimo_check TIMESTAMP,
    configuracion_scraper JSON COMMENT 'Selectores CSS, XPath, etc',
    tasa_exito DECIMAL(5,2) DEFAULT 100 COMMENT '% de requests exitosos'
);

-- Tabla de logs de scraping
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
    FOREIGN KEY (id_fuente) REFERENCES fuentes_scraping(id_fuente)
);

-- ============================================
-- 5. TABLAS DE INTERACCIONES
-- ============================================

-- Tabla de comentarios de capítulos
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
    INDEX (id_capitulo, fecha_publicacion)
);

-- Tabla de calificaciones de novelas
CREATE TABLE calificaciones_novela (
    id_calificacion INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_novela INT NOT NULL,
    calificacion INT CHECK (calificacion BETWEEN 1 AND 5),
    fecha_calificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE CASCADE,
    UNIQUE KEY (id_usuario, id_novela)
);

-- ============================================
-- 6. TABLAS PARA APP MÓVIL
-- ============================================

-- Tabla de sesiones de APP
CREATE TABLE sesiones_app (
    id_sesion INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    dispositivo_id VARCHAR(255) COMMENT 'APP: ID único del dispositivo',
    sistema_operativo VARCHAR(20),
    version_app VARCHAR(20),
    token_sesion VARCHAR(255) UNIQUE,
    fecha_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_ultima_actividad TIMESTAMP,
    esta_activa BOOLEAN DEFAULT TRUE,
    ip_conexion VARCHAR(45),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- Tabla de descargas offline
CREATE TABLE descargas_offline (
    id_descarga INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_capitulo INT NOT NULL,
    idioma_descargado VARCHAR(10),
    fecha_descarga TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tamaño_bytes INT,
    ruta_almacenamiento VARCHAR(500) COMMENT 'APP: Ruta local en dispositivo',
    expira_en TIMESTAMP COMMENT 'APP: Para gestión de caché',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_capitulo) REFERENCES capitulos(id_capitulo) ON DELETE CASCADE,
    UNIQUE KEY (id_usuario, id_capitulo, idioma_descargado)
);

-- ============================================
-- 7. TABLAS DE ESTADÍSTICAS Y MÉTRICAS
-- ============================================

-- Tabla de estadísticas diarias
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
    UNIQUE KEY (fecha)
);

-- Tabla de estadísticas por hora de novelas
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
);

-- Tabla de métricas de usuario
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
    UNIQUE KEY (id_usuario, fecha)
);

-- ============================================
-- 8. TABLAS DE GESTIÓN DE APIS
-- ============================================

-- Tabla de APIs de traducción
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
    ultimo_uso TIMESTAMP NULL,
    -- Configuración específica para chino->español --
    especialidad_chino_es BOOLEAN DEFAULT TRUE,
    configuracion_chino JSON COMMENT 'Parámetros específicos para chino',
    -- Estado --
    esta_activa BOOLEAN DEFAULT TRUE,
    modo_reserva BOOLEAN DEFAULT FALSE COMMENT 'Solo usar si otras fallan',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_prioridad_activa (prioridad, esta_activa)
);

-- Tabla de logs de APIs
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
);

-- ============================================
-- 9. TABLAS DE ANUNCIOS
-- ============================================

-- Tabla de anunciantes
CREATE TABLE anunciantes (
    id_anunciante INT PRIMARY KEY AUTO_INCREMENT,
    nombre_empresa VARCHAR(100),
    contacto_email VARCHAR(100),
    telefono VARCHAR(20),
    pais VARCHAR(50),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    esta_activo BOOLEAN DEFAULT TRUE,
    presupuesto_mensual DECIMAL(10,2) DEFAULT 0
);

-- Tabla de campañas de anuncios
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
    INDEX idx_estado_fechas (estado, fecha_inicio, fecha_fin)
);

-- Tabla de anuncios mostrados
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
);

-- ============================================
-- 10. TABLAS DE SUSCRIPCIONES
-- ============================================

-- Tabla de planes de suscripción
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
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de suscripciones de usuario
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
    INDEX idx_usuario_activa (id_usuario, estado)
);

-- Tabla de historial de pagos
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
);

-- ============================================
-- 11. TABLAS DE RECOMENDACIONES
-- ============================================

-- Tabla de recomendaciones de usuarios
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
    FOREIGN KEY (revisado_por) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
);

-- ============================================
-- 12. TABLAS DE REPORTES DE ERRORES
-- ============================================

-- Tabla de reportes de errores
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
    INDEX idx_fecha_tipo (fecha_reporte, tipo_error)
);

-- ============================================
-- 13. OTRAS TABLAS ESENCIALES
-- ============================================

-- Tabla de autores de novelas
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
);

-- Tabla de colecciones de novelas
CREATE TABLE colecciones_novelas (
    id_coleccion INT PRIMARY KEY AUTO_INCREMENT,
    nombre_coleccion VARCHAR(100) NOT NULL,
    descripcion TEXT,
    creador_id INT NOT NULL,
    es_publica BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultima_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (creador_id) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- Tabla de contenido de colecciones
CREATE TABLE colecciones_contenido (
    id_coleccion INT NOT NULL,
    id_novela INT NOT NULL,
    orden INT DEFAULT 1,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    comentario TEXT,
    PRIMARY KEY (id_coleccion, id_novela),
    FOREIGN KEY (id_coleccion) REFERENCES colecciones_novelas(id_coleccion) ON DELETE CASCADE,
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE CASCADE
);

-- Tabla de notificaciones del sistema
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
);

-- Tabla de configuración del sistema
CREATE TABLE configuracion_sistema (
    id_config INT PRIMARY KEY AUTO_INCREMENT,
    clave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT,
    tipo ENUM('string', 'integer', 'boolean', 'json', 'array') DEFAULT 'string',
    categoria VARCHAR(50) COMMENT 'general, scraping, traduccion, anuncios',
    descripcion TEXT,
    editable BOOLEAN DEFAULT TRUE,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- 14. ÍNDICES ADICIONALES
-- ============================================

-- Índices para optimización
CREATE INDEX idx_novelas_genero ON novelas((CAST(generos AS CHAR(50))));
CREATE INDEX idx_capitulos_novela_num ON capitulos(id_novela, numero_capitulo);
CREATE INDEX idx_traducciones_cap_idioma ON traducciones_capitulo(id_capitulo, idioma);
CREATE INDEX idx_lecturas_usuario_fecha ON lecturas_usuario(id_usuario, fecha_ultima_lectura DESC);
CREATE INDEX idx_scraping_fuente ON logs_scraping(id_fuente, fecha_ejecucion DESC);
CREATE INDEX idx_novelas_popularidad ON novelas(promedio_calificacion DESC, total_vistas DESC);
CREATE INDEX idx_capitulos_traducciones_es ON traducciones_capitulo(idioma, estado_traduccion);
CREATE INDEX idx_usuarios_activos ON usuarios(esta_activo, ultimo_login DESC);
CREATE INDEX idx_suscripciones_activas ON suscripciones_usuario(estado, fecha_vencimiento);
CREATE INDEX idx_anuncios_activos ON campañas_anuncios(estado, fecha_inicio, fecha_fin);
CREATE INDEX idx_reportes_pendientes ON reportes_errores(estado, prioridad, fecha_reporte);
CREATE INDEX idx_recomendaciones_pendientes ON recomendaciones_usuarios(estado, fecha_recomendacion);
CREATE INDEX idx_prioridad_espanol ON traducciones_novela(es_principal, prioridad_idioma);

-- ============================================
-- 15. TRIGGERS
-- ============================================

-- Trigger para actualizar estadísticas de novelas al agregar favoritos
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

-- Trigger para actualizar calificación promedio de novela
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
    SET promedio_calificacion = avg_cal
    WHERE id_novela = NEW.id_novela;
END//
DELIMITER ;

-- Trigger para crear traducción al español automáticamente para nueva novela
DELIMITER //
CREATE TRIGGER trg_crear_traduccion_novela_es
AFTER INSERT ON novelas
FOR EACH ROW
BEGIN
    -- Crear entrada de traducción al español automáticamente
    INSERT INTO traducciones_novela (id_novela, idioma, estado_traduccion, es_principal, prioridad_idioma)
    VALUES (NEW.id_novela, 'es', 'pendiente', TRUE, 1);
END//
DELIMITER ;

-- Trigger para crear traducción al español automáticamente para nuevo capítulo
DELIMITER //
CREATE TRIGGER trg_crear_traduccion_capitulo_es
AFTER INSERT ON capitulos
FOR EACH ROW
BEGIN
    -- Marcar capítulo para traducción al español con prioridad
    INSERT INTO traducciones_capitulo (id_capitulo, idioma, estado_traduccion)
    VALUES (NEW.id_capitulo, 'es', 'pendiente')
    ON DUPLICATE KEY UPDATE estado_traduccion = 'pendiente';
END//
DELIMITER ;

-- Trigger para controlar límites de API
DELIMITER //
CREATE TRIGGER trg_actualizar_contador_api
AFTER INSERT ON logs_api_traduccion
FOR EACH ROW
BEGIN
    -- Actualizar contadores de uso
    UPDATE apis_traduccion 
    SET 
        tokens_usados_hoy = tokens_usados_hoy + COALESCE(NEW.tokens_usados, 0),
        requests_hoy = requests_hoy + 1,
        ultimo_uso = NEW.fecha_solicitud
    WHERE id_api = NEW.id_api;
END//
DELIMITER ;

-- Trigger para notificar nuevo capítulo traducido al español
DELIMITER //
CREATE TRIGGER trg_notificar_nuevo_capitulo_es
AFTER UPDATE ON traducciones_capitulo
FOR EACH ROW
BEGIN
    DECLARE novel_title VARCHAR(200);
    DECLARE chapter_num INT;
    DECLARE novel_id INT;
    
    -- Solo notificar para español cuando se completa la traducción
    IF NEW.idioma = 'es' AND NEW.estado_traduccion = 'completado' AND OLD.estado_traduccion != 'completado' THEN
        -- Obtener información del capítulo y novela
        SELECT n.titulo_original, c.numero_capitulo, c.id_novela 
        INTO novel_title, chapter_num, novel_id
        FROM capitulos c
        JOIN novelas n ON c.id_novela = n.id_novela
        WHERE c.id_capitulo = NEW.id_capitulo;
        
        -- Notificar a usuarios que tienen esta novela en favoritos o leyendo
        INSERT INTO notificaciones_sistema (id_usuario, tipo_notificacion, titulo, contenido, id_novela, id_capitulo)
        SELECT 
            bu.id_usuario,
            'nuevo_capitulo',
            CONCAT('Nuevo capítulo disponible: ', LEFT(novel_title, 50)),
            CONCAT('Capítulo ', chapter_num, ' disponible para lectura'),
            novel_id,
            NEW.id_capitulo
        FROM biblioteca_usuario bu
        WHERE bu.id_novela = novel_id
        AND bu.tipo IN ('favoritos', 'leyendo')
        AND bu.notificar_nuevos_caps = TRUE
        AND bu.id_usuario IN (
            SELECT id_usuario FROM usuarios WHERE esta_activo = TRUE
        )
        ON DUPLICATE KEY UPDATE fecha_creacion = CURRENT_TIMESTAMP;
    END IF;
END//
DELIMITER ;

-- Trigger para verificar suscripción activa antes de mostrar anuncios
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
        IF user_has_active_subscription AND NEW.tipo_evento = 'impresion' THEN
            SET NEW.id_mostrado = NULL; -- Evitar inserción
        END IF;
    END IF;
END//
DELIMITER ;

-- Trigger para actualizar métricas de usuario al leer capítulo
DELIMITER //
CREATE TRIGGER trg_actualizar_metricas_usuario
AFTER INSERT ON lecturas_usuario
FOR EACH ROW
BEGIN
    DECLARE chapter_words INT;
    DECLARE reading_minutes INT;
    
    -- Obtener palabras del capítulo
    SELECT palabras_original INTO chapter_words 
    FROM capitulos 
    WHERE id_capitulo = NEW.id_capitulo_ultimo;
    
    -- Calcular minutos de lectura estimados (200 palabras/minuto)
    SET reading_minutes = COALESCE(chapter_words / 200, 5);
    
    -- Actualizar o insertar métricas diarias del usuario
    INSERT INTO metricas_usuario (id_usuario, fecha, capitulos_leidos, tiempo_lectura_min)
    VALUES (NEW.id_usuario, CURDATE(), 1, reading_minutes)
    ON DUPLICATE KEY UPDATE 
        capitulos_leidos = capitulos_leidos + 1,
        tiempo_lectura_min = tiempo_lectura_min + reading_minutes;
END//
DELIMITER ;

-- ============================================
-- 16. VISTAS
-- ============================================

-- Vista para capítulos pendientes de traducción al español
CREATE VIEW vista_capitulos_pendientes_es AS
SELECT 
    c.id_capitulo,
    c.id_novela,
    n.titulo_original,
    c.numero_capitulo,
    c.titulo_original as titulo_capitulo,
    c.palabras_original,
    c.fecha_publicacion_original,
    c.fuente_url
FROM capitulos c
JOIN novelas n ON c.id_novela = n.id_novela
LEFT JOIN traducciones_capitulo tc ON c.id_capitulo = tc.id_capitulo AND tc.idioma = 'es'
WHERE tc.id_traduccion_capitulo IS NULL
ORDER BY n.total_vistas DESC, c.numero_capitulo ASC;

-- Vista para estadísticas de traducción por idioma
CREATE VIEW vista_estadisticas_traduccion AS
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

-- Vista para usuarios premium activos
CREATE VIEW vista_usuarios_premium AS
SELECT 
    u.id_usuario,
    u.username,
    u.email,
    s.id_plan,
    p.nombre_plan,
    s.fecha_inicio,
    s.fecha_vencimiento,
    DATEDIFF(s.fecha_vencimiento, CURDATE()) as dias_restantes,
    p.sin_anuncios,
    p.lectura_modo_offline
FROM usuarios u
JOIN suscripciones_usuario s ON u.id_usuario = s.id_usuario
JOIN planes_suscripcion p ON s.id_plan = p.id_plan
WHERE s.estado = 'activa'
AND s.fecha_vencimiento > NOW();

-- Vista para novelas más populares
CREATE VIEW vista_novelas_populares AS
SELECT 
    n.id_novela,
    n.titulo_original,
    COALESCE(tn.titulo_traducido, n.titulo_original) as titulo_espanol,
    n.autor_original,
    n.generos,
    n.promedio_calificacion,
    n.total_vistas,
    n.total_favoritos,
    COUNT(DISTINCT c.id_capitulo) as total_capitulos,
    COUNT(DISTINCT tc.id_traduccion_capitulo) as capitulos_traducidos_es
FROM novelas n
LEFT JOIN capitulos c ON n.id_novela = c.id_novela
LEFT JOIN traducciones_novela tn ON n.id_novela = tn.id_novela AND tn.idioma = 'es'
LEFT JOIN traducciones_capitulo tc ON c.id_capitulo = tc.id_capitulo AND tc.idioma = 'es'
GROUP BY n.id_novela
ORDER BY n.total_vistas DESC, n.promedio_calificacion DESC
LIMIT 100;

-- Vista para reportes pendientes de revisión
CREATE VIEW vista_reportes_pendientes AS
SELECT 
    r.id_reporte,
    r.titulo_reporte,
    r.tipo_error,
    r.prioridad,
    r.fecha_reporte,
    u.username as usuario_reportero,
    n.titulo_original as novela,
    c.numero_capitulo
FROM reportes_errores r
JOIN usuarios u ON r.id_usuario = u.id_usuario
LEFT JOIN novelas n ON r.id_novela = n.id_novela
LEFT JOIN capitulos c ON r.id_capitulo = c.id_capitulo
WHERE r.estado = 'pendiente'
ORDER BY 
    CASE r.prioridad 
        WHEN 'critica' THEN 1
        WHEN 'alta' THEN 2
        WHEN 'media' THEN 3
        ELSE 4
    END,
    r.fecha_reporte ASC;

-- ============================================
-- 17. CONFIGURACIÓN INICIAL DEL SISTEMA
-- ============================================

-- Insertar configuración por defecto priorizando español
INSERT INTO configuracion_sistema (clave, valor, tipo, categoria, descripcion) VALUES
('idioma_principal', 'es', 'string', 'general', 'Idioma principal de la plataforma'),
('traduccion_automatica_es', 'true', 'boolean', 'traduccion', 'Traducir automáticamente al español'),
('prioridad_espanol', '1', 'integer', 'traduccion', 'Prioridad para traducciones al español (1-10)'),
('limite_capitulos_diarios_es', '20', 'integer', 'scraping', 'Máximo de capítulos a traducir al español por día'),
('api_principal_chino_es', '1', 'integer', 'traduccion', 'ID de API principal para chino-español'),
('scraping_intervalo_min', '360', 'integer', 'scraping', 'Intervalo entre scrapings en minutos'),
('max_capitulos_por_novela', '5000', 'integer', 'scraping', 'Máximo de capítulos por novela'),
('calidad_traduccion_minima', 'media', 'string', 'traduccion', 'Calidad mínima aceptable para traducciones'),
('notificaciones_nuevos_caps', 'true', 'boolean', 'general', 'Enviar notificaciones de nuevos capítulos'),
('modo_mantenimiento', 'false', 'boolean', 'general', 'Modo mantenimiento del sistema'),
('version_base_datos', '1.0.0', 'string', 'general', 'Versión del esquema de base de datos');

-- Insertar planes de suscripción por defecto
INSERT INTO planes_suscripcion (nombre_plan, descripcion, sin_anuncios, lectura_modo_offline, capitulos_avanzados, limite_descargas, precio_mensual, precio_anual, orden_visual) VALUES
('Gratis', 'Acceso básico a todas las novelas con anuncios', FALSE, FALSE, FALSE, 10, 0.00, 0.00, 1),
('Premium', 'Lectura sin anuncios y descarga offline', TRUE, TRUE, FALSE, 100, 4.99, 49.99, 2),
('VIP', 'Acceso anticipado a capítulos y beneficios exclusivos', TRUE, TRUE, TRUE, 500, 9.99, 99.99, 3);

-- Insertar APIs de traducción de ejemplo
INSERT INTO apis_traduccion (nombre_api, tipo_api, prioridad, tokens_por_minuto, tokens_por_dia, especialidad_chino_es, esta_activa) VALUES
('DeepL Pro', 'traduccion', 1, 500000, 5000000, TRUE, TRUE),
('GPT-4 Turbo', 'traduccion', 2, 10000, 100000, TRUE, TRUE),
('Claude 3', 'traduccion', 3, 10000, 100000, TRUE, TRUE),
('Google Translate API', 'traduccion', 4, 1000000, 10000000, FALSE, TRUE);

-- ============================================
-- 18. EVENTOS PROGRAMADOS
-- ============================================

-- Evento para resetear contadores diarios de APIs
DELIMITER //
CREATE EVENT reset_contadores_api_diarios
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP + INTERVAL 1 DAY
DO
BEGIN
    UPDATE apis_traduccion 
    SET tokens_usados_hoy = 0, requests_hoy = 0
    WHERE DATE(ultimo_uso) < CURDATE();
END//
DELIMITER ;

-- Evento para actualizar estadísticas diarias
DELIMITER //
CREATE EVENT actualizar_estadisticas_diarias
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_DATE + INTERVAL 1 DAY
DO
BEGIN
    -- Resumen de usuarios del día anterior
    INSERT INTO estadisticas_diarias (fecha, nuevos_usuarios, usuarios_activos)
    SELECT 
        CURDATE() - INTERVAL 1 DAY,
        COUNT(CASE WHEN DATE(fecha_registro) = CURDATE() - INTERVAL 1 DAY THEN 1 END),
        COUNT(DISTINCT id_usuario)
    FROM usuarios 
    WHERE ultimo_login >= CURDATE() - INTERVAL 1 DAY
    ON DUPLICATE KEY UPDATE
        nuevos_usuarios = VALUES(nuevos_usuarios),
        usuarios_activos = VALUES(usuarios_activos);
END//
DELIMITER ;

-- Evento para limpiar sesiones inactivas de APP
DELIMITER //
CREATE EVENT limpiar_sesiones_inactivas
ON SCHEDULE EVERY 1 HOUR
STARTS CURRENT_TIMESTAMP
DO
BEGIN
    UPDATE sesiones_app 
    SET esta_activa = FALSE 
    WHERE fecha_ultima_actividad < NOW() - INTERVAL 7 DAY;
    
    DELETE FROM sesiones_app 
    WHERE fecha_ultima_actividad < NOW() - INTERVAL 30 DAY;
END//
DELIMITER ;

-- Evento para verificar suscripciones vencidas
DELIMITER //
CREATE EVENT verificar_suscripciones_vencidas
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
BEGIN
    UPDATE suscripciones_usuario 
    SET estado = 'expirada'
    WHERE estado = 'activa'
    AND fecha_vencimiento < NOW();
END//
DELIMITER ;

-- ============================================
-- 19. PROCEDIMIENTOS ALMACENADOS ÚTILES
-- ============================================

-- Procedimiento para obtener API disponible para traducción
DELIMITER //
CREATE PROCEDURE obtener_api_traduccion(
    IN p_idioma_origen VARCHAR(10),
    IN p_idioma_destino VARCHAR(10)
)
BEGIN
    SELECT *
    FROM apis_traduccion
    WHERE esta_activa = TRUE
    AND (especialidad_chino_es = TRUE OR (p_idioma_origen = 'zh' AND p_idioma_destino = 'es'))
    AND tokens_usados_hoy < tokens_por_dia
    ORDER BY 
        CASE 
            WHEN p_idioma_origen = 'zh' AND p_idioma_destino = 'es' AND especialidad_chino_es = TRUE THEN prioridad
            ELSE prioridad + 10
        END,
        (tokens_usados_hoy / tokens_por_dia) ASC
    LIMIT 1;
END//
DELIMITER ;

-- Procedimiento para registrar nuevo capítulo traducido
DELIMITER //
CREATE PROCEDURE registrar_capitulo_traducido(
    IN p_id_capitulo INT,
    IN p_idioma VARCHAR(10),
    IN p_contenido TEXT,
    IN p_traductor_ia VARCHAR(50),
    IN p_calidad ENUM('baja', 'media', 'alta')
)
BEGIN
    DECLARE v_palabras INT;
    DECLARE v_id_novela INT;
    
    -- Obtener número de palabras y ID de novela
    SELECT palabras_original, id_novela INTO v_palabras, v_id_novela
    FROM capitulos WHERE id_capitulo = p_id_capitulo;
    
    -- Insertar o actualizar traducción
    INSERT INTO traducciones_capitulo (
        id_capitulo, 
        idioma, 
        contenido_traducido, 
        estado_traduccion, 
        traductor_ia, 
        calidad_estimada,
        palabras_traducidas,
        fecha_traduccion
    ) VALUES (
        p_id_capitulo,
        p_idioma,
        p_contenido,
        'completado',
        p_traductor_ia,
        p_calidad,
        v_palabras,
        CURRENT_TIMESTAMP
    ) ON DUPLICATE KEY UPDATE
        contenido_traducido = p_contenido,
        estado_traduccion = 'completado',
        traductor_ia = p_traductor_ia,
        calidad_estimada = p_calidad,
        palabras_traducidas = v_palabras,
        version_traduccion = version_traduccion + 1,
        fecha_traduccion = CURRENT_TIMESTAMP;
    
    -- Si es español, actualizar estadísticas
    IF p_idioma = 'es' THEN
        UPDATE novelas 
        SET total_vistas = total_vistas + 1
        WHERE id_novela = v_id_novela;
    END IF;
END//
DELIMITER ;

-- Procedimiento para obtener próximo capítulo a traducir
DELIMITER //
CREATE PROCEDURE obtener_proximo_capitulo_traducir(
    IN p_idioma VARCHAR(10),
    IN p_limite INT
)
BEGIN
    SELECT 
        c.id_capitulo,
        c.id_novela,
        c.numero_capitulo,
        c.titulo_original,
        c.contenido_original,
        c.palabras_original,
        n.titulo_original as titulo_novela,
        n.promedio_calificacion,
        n.total_vistas
    FROM capitulos c
    JOIN novelas n ON c.id_novela = n.id_novela
    LEFT JOIN traducciones_capitulo tc ON c.id_capitulo = tc.id_capitulo AND tc.idioma = p_idioma
    WHERE tc.id_traduccion_capitulo IS NULL
    ORDER BY 
        CASE WHEN p_idioma = 'es' THEN 
            CASE 
                WHEN n.total_vistas > 10000 THEN 1
                WHEN n.total_vistas > 1000 THEN 2
                ELSE 3
            END
        ELSE 4
        END,
        n.total_vistas DESC,
        c.numero_capitulo ASC
    LIMIT p_limite;
END//
DELIMITER ;

-- ============================================
-- 20. CONFIGURACIÓN DE PERMISOS (EJEMPLO)
-- ============================================

-- Nota: Estos son ejemplos, ajustar según tu entorno de producción

/*
-- Crear usuario para la aplicación web
CREATE USER 'novelasia_web'@'localhost' IDENTIFIED BY 'password_seguro';
GRANT SELECT, INSERT, UPDATE, DELETE ON NovelAsia.* TO 'novelasia_web'@'localhost';

-- Crear usuario para procesos de scraping/traducción
CREATE USER 'novelasia_worker'@'localhost' IDENTIFIED BY 'password_seguro_worker';
GRANT SELECT, INSERT, UPDATE ON NovelAsia.* TO 'novelasia_worker'@'localhost';
GRANT EXECUTE ON PROCEDURE NovelAsia.* TO 'novelasia_worker'@'localhost';

-- Crear usuario de solo lectura para reportes
CREATE USER 'novelasia_reportes'@'localhost' IDENTIFIED BY 'password_reportes';
GRANT SELECT ON NovelAsia.* TO 'novelasia_reportes'@'localhost';
GRANT SELECT ON NovelAsia.vista_* TO 'novelasia_reportes'@'localhost';

-- Aplicar cambios de permisos
FLUSH PRIVILEGES;
*/

-- ============================================
-- FIN DEL ESQUEMA DE BASE DE DATOS
-- ============================================

-- Mostrar resumen de tablas creadas
SELECT 
    TABLE_NAME as 'Tabla',
    TABLE_ROWS as 'Filas Estimadas',
    DATA_LENGTH as 'Tamaño Datos (KB)',
    INDEX_LENGTH as 'Tamaño Índices (KB)'
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = 'NovelAsia'
ORDER BY TABLE_NAME;