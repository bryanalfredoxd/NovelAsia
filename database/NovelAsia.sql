-- ============================================
-- BASE DE DATOS COMPLETA - PLATAFORMA DE NOVELAS
-- Sistema de scraping, traducción y lectura de novelas chinas
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- TABLA 1: USUARIOS
-- ============================================
DROP TABLE IF EXISTS usuarios;
CREATE TABLE usuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    nombre VARCHAR(100),
    pais VARCHAR(50),
    idioma_preferido VARCHAR(10) DEFAULT 'es' COMMENT 'Español Default',
    avatar_url VARCHAR(255),
    rol ENUM('lector', 'traductor', 'moderador', 'admin') DEFAULT 'lector',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_login TIMESTAMP NULL DEFAULT NULL,
    esta_activo BOOLEAN DEFAULT TRUE,
    -- Campos APP
    token_notificaciones VARCHAR(255) COMMENT 'Token para push notifications',
    configuracion_app JSON COMMENT 'Preferencias de tema, modo lectura, etc',
    dispositivo_registro VARCHAR(50) COMMENT 'Tipo de dispositivo (iOS/Android)',
    -- Sistema de puntos
    puntos_canje INT DEFAULT 0 COMMENT 'Puntos acumulados para canjear',
    puntos_totales_historico INT DEFAULT 0 COMMENT 'Total de puntos ganados históricamente',
    -- Sistema de referidos
    codigo_referido VARCHAR(20) UNIQUE COMMENT 'Código único para referir usuarios',
    referido_por INT NULL COMMENT 'ID del usuario que lo refirió',
    total_referidos INT DEFAULT 0 COMMENT 'Cantidad de usuarios referidos',
    -- Sistema de logros y títulos
    titulo_activo INT NULL COMMENT 'ID del título equipado',
    insignia_activa INT NULL COMMENT 'ID de la insignia equipada',
    FOREIGN KEY (referido_por) REFERENCES usuarios(id_usuario) ON DELETE SET NULL,
    INDEX idx_codigo_referido (codigo_referido),
    INDEX idx_puntos (puntos_canje DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 2: AUTORES
-- ============================================
DROP TABLE IF EXISTS autores_novelas;
CREATE TABLE autores_novelas (
    id_autor INT PRIMARY KEY AUTO_INCREMENT,
    nombre_autor VARCHAR(100) NOT NULL,
    biografia TEXT,
    otras_obras JSON COMMENT 'IDs de otras novelas del autor',
    seguidores INT DEFAULT 0,
    INDEX idx_nombre (nombre_autor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 3: GÉNEROS
-- ============================================
DROP TABLE IF EXISTS generos;
CREATE TABLE generos (
    id_genero INT PRIMARY KEY AUTO_INCREMENT,
    nombre_genero VARCHAR(50) UNIQUE NOT NULL,
    nombre_genero_es VARCHAR(50) COMMENT 'Nombre traducido al español',
    descripcion TEXT,
    icono_url VARCHAR(255),
    color_hex VARCHAR(7) COMMENT 'Color representativo del género',
    esta_activo BOOLEAN DEFAULT TRUE,
    INDEX idx_nombre (nombre_genero)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 4: ETIQUETAS
-- ============================================
DROP TABLE IF EXISTS etiquetas;
CREATE TABLE etiquetas (
    id_etiqueta INT PRIMARY KEY AUTO_INCREMENT,
    nombre_etiqueta VARCHAR(50) UNIQUE NOT NULL,
    nombre_etiqueta_es VARCHAR(50) COMMENT 'Nombre traducido al español',
    descripcion TEXT,
    categoria ENUM('trama', 'personaje', 'ambientacion', 'otro') DEFAULT 'otro',
    esta_activa BOOLEAN DEFAULT TRUE,
    INDEX idx_nombre (nombre_etiqueta),
    INDEX idx_categoria (categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 5: NOVELAS (Información original en chino)
-- ============================================
DROP TABLE IF EXISTS novelas;
CREATE TABLE novelas (
    id_novela INT PRIMARY KEY AUTO_INCREMENT,
    titulo_original VARCHAR(200) NOT NULL,
    id_autor INT NULL,
    autor_original VARCHAR(100) COMMENT 'Nombre del autor en chino',
    descripcion_original TEXT,
    url_original_qidian VARCHAR(255),
    estado_original ENUM('en_progreso', 'completado', 'pausado', 'cancelado') DEFAULT 'en_progreso',
    fecha_publicacion_original DATE,
    portada_url VARCHAR(255),
    total_capitulos_originales INT DEFAULT 0 COMMENT 'Total de capítulos en la fuente',
    -- Metadatos de scraping
    fuente_scraping VARCHAR(100) COMMENT 'URL/sitio de donde se obtiene',
    ultimo_scraping TIMESTAMP NULL DEFAULT NULL,
    es_verificado BOOLEAN DEFAULT FALSE,
    hash_metadata VARCHAR(64) COMMENT 'Hash para detectar cambios en metadata',
    -- Estadísticas
    promedio_calificacion DECIMAL(3,2) DEFAULT 0,
    total_calificaciones INT DEFAULT 0,
    total_vistas BIGINT DEFAULT 0,
    total_favoritos INT DEFAULT 0,
    total_comentarios INT DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_autor) REFERENCES autores_novelas(id_autor) ON DELETE SET NULL,
    INDEX idx_novelas_popularidad (promedio_calificacion DESC, total_vistas DESC),
    INDEX idx_autor (id_autor),
    INDEX idx_estado (estado_original)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 6: NOVELAS_GÉNEROS (Relación N:N)
-- ============================================
DROP TABLE IF EXISTS novelas_generos;
CREATE TABLE novelas_generos (
    id_novela INT NOT NULL,
    id_genero INT NOT NULL,
    PRIMARY KEY (id_novela, id_genero),
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE CASCADE,
    FOREIGN KEY (id_genero) REFERENCES generos(id_genero) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 7: NOVELAS_ETIQUETAS (Relación N:N)
-- ============================================
DROP TABLE IF EXISTS novelas_etiquetas;
CREATE TABLE novelas_etiquetas (
    id_novela INT NOT NULL,
    id_etiqueta INT NOT NULL,
    PRIMARY KEY (id_novela, id_etiqueta),
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE CASCADE,
    FOREIGN KEY (id_etiqueta) REFERENCES etiquetas(id_etiqueta) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 8: CAPÍTULOS (Contenido original en chino)
-- ============================================
DROP TABLE IF EXISTS capitulos;
CREATE TABLE capitulos (
    id_capitulo INT PRIMARY KEY AUTO_INCREMENT,
    id_novela INT NOT NULL,
    numero_capitulo INT NOT NULL,
    orden_capitulo INT NOT NULL COMMENT 'Orden de visualización del capítulo',
    titulo_original VARCHAR(200) COMMENT 'En Chino',
    contenido_original MEDIUMTEXT COMMENT 'Texto del Capitulo - En Chino',
    fecha_publicacion_original TIMESTAMP NULL,
    fuente_url VARCHAR(255),
    palabras_original INT COMMENT 'Cantidad de caracteres Chinos por Capitulo',
    -- Estados y control
    estado_capitulo ENUM('disponible', 'borrador', 'oculto', 'en_revision') DEFAULT 'disponible',
    enviado_traduccion BOOLEAN DEFAULT FALSE COMMENT 'Enviado a traducción IA',
    prioridad_traduccion INT DEFAULT 1 COMMENT 'Prioridad (1-10)',
    -- Scraping
    hash_contenido VARCHAR(64) COMMENT 'Hash SHA256 para detectar cambios',
    scrapeado_en TIMESTAMP NULL DEFAULT NULL,
    intentos_scraping INT DEFAULT 0,
    -- Auditoría
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE CASCADE,
    UNIQUE KEY uk_novela_capitulo (id_novela, numero_capitulo),
    UNIQUE KEY uk_novela_orden (id_novela, orden_capitulo),
    INDEX idx_capitulos_novela_num (id_novela, numero_capitulo),
    INDEX idx_estado_prioridad (estado_capitulo, prioridad_traduccion),
    INDEX idx_enviado_traduccion (enviado_traduccion, id_novela),
    INDEX idx_orden (id_novela, orden_capitulo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 9: NOVELAS_TRADUCCION_ESPAÑOL
-- ============================================
DROP TABLE IF EXISTS novelas_traduccion_espanol;
CREATE TABLE novelas_traduccion_espanol (
    id_traduccion_novela_es INT PRIMARY KEY AUTO_INCREMENT,
    id_novela INT NOT NULL,
    titulo_traducido VARCHAR(200),
    descripcion_traducida TEXT,
    autor_traducido VARCHAR(100) COMMENT 'Nombre del autor en español',
    estado_traduccion ENUM('pendiente', 'en_progreso', 'completado', 'pausado', 'error') DEFAULT 'pendiente',
    fecha_inicio_traduccion TIMESTAMP NULL DEFAULT NULL,
    fecha_fin_traduccion TIMESTAMP NULL DEFAULT NULL,
    -- IA y calidad
    traductor_ia VARCHAR(50) COMMENT 'API/IA usada: DeepL, GPT, Claude, etc',
    version_traduccion INT DEFAULT 1,
    calidad_estimada ENUM('baja', 'media', 'alta', 'excelente') DEFAULT 'media',
    configuracion_ia JSON COMMENT 'Parámetros de traducción utilizados',
    -- Metadata de traducción
    palabras_traducidas INT,
    tiempo_traduccion_segundos INT,
    costo_traduccion DECIMAL(10,4) DEFAULT 0,
    revisado_manualmente BOOLEAN DEFAULT FALSE,
    fecha_revision TIMESTAMP NULL DEFAULT NULL,
    -- Cache
    hash_traduccion VARCHAR(64),
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE CASCADE,
    UNIQUE KEY uk_novela_version (id_novela, version_traduccion),
    INDEX idx_estado (estado_traduccion),
    INDEX idx_calidad (calidad_estimada)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 10: CAPITULOS_TRADUCCION_ESPAÑOL
-- ============================================
DROP TABLE IF EXISTS capitulos_traduccion_espanol;
CREATE TABLE capitulos_traduccion_espanol (
    id_traduccion_capitulo_es INT PRIMARY KEY AUTO_INCREMENT,
    id_capitulo INT NOT NULL,
    id_traduccion_novela_es INT NOT NULL COMMENT 'Referencia a traducción de novela',
    titulo_traducido VARCHAR(200),
    contenido_traducido MEDIUMTEXT COMMENT 'Texto del Capitulo - En Español',
    estado_traduccion ENUM('pendiente', 'en_progreso', 'completado', 'pausado', 'error') DEFAULT 'pendiente',
    fecha_traduccion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- IA y calidad
    traductor_ia VARCHAR(50),
    version_traduccion INT DEFAULT 1,
    calidad_estimada ENUM('baja', 'media', 'alta', 'excelente') DEFAULT 'media',
    palabras_traducidas INT COMMENT 'Cantidad de Palabras por capitulo en Español',
    -- Optimización
    contenido_comprimido MEDIUMBLOB COMMENT 'Contenido comprimido',
    hash_traduccion VARCHAR(64),
    -- Metadata
    tiempo_traduccion_segundos INT,
    costo_traduccion DECIMAL(10,4) DEFAULT 0,
    revisado_manualmente BOOLEAN DEFAULT FALSE,
    errores_reportados INT DEFAULT 0,
    FOREIGN KEY (id_capitulo) REFERENCES capitulos(id_capitulo) ON DELETE CASCADE,
    FOREIGN KEY (id_traduccion_novela_es) REFERENCES novelas_traduccion_espanol(id_traduccion_novela_es) ON DELETE CASCADE,
    UNIQUE KEY uk_capitulo_version (id_capitulo, version_traduccion),
    INDEX idx_estado (estado_traduccion),
    INDEX idx_novela_traduccion (id_traduccion_novela_es)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 11: IDIOMAS_DISPONIBLES
-- ============================================
DROP TABLE IF EXISTS idiomas_disponibles;
CREATE TABLE idiomas_disponibles (
    id_idioma INT PRIMARY KEY AUTO_INCREMENT,
    codigo_idioma VARCHAR(10) UNIQUE NOT NULL COMMENT 'en, pt, fr, etc',
    nombre_idioma VARCHAR(50) NOT NULL,
    nombre_nativo VARCHAR(50) COMMENT 'English, Português',
    esta_activo BOOLEAN DEFAULT TRUE,
    prioridad INT DEFAULT 1 COMMENT 'Orden de prioridad para traducciones',
    usa_traductor_automatico BOOLEAN DEFAULT TRUE COMMENT 'Usar Google Translate u otro',
    INDEX idx_codigo (codigo_idioma),
    INDEX idx_activo_prioridad (esta_activo, prioridad)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 12: NOVELAS_TRADUCCION_IDIOMAS
-- ============================================
DROP TABLE IF EXISTS novelas_traduccion_idiomas;
CREATE TABLE novelas_traduccion_idiomas (
    id_traduccion_novela_idioma INT PRIMARY KEY AUTO_INCREMENT,
    id_traduccion_novela_es INT NOT NULL COMMENT 'Traducción base en español',
    id_idioma INT NOT NULL,
    titulo_traducido VARCHAR(200) COMMENT 'Segun el Idioma',
    descripcion_traducida TEXT COMMENT 'Segun el Idioma',
    autor_traducido VARCHAR(100) COMMENT 'Segun el Idioma',
    estado_traduccion ENUM('pendiente', 'en_progreso', 'completado', 'pausado', 'error') DEFAULT 'pendiente',
    fecha_traduccion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Traductor
    servicio_traduccion VARCHAR(50) COMMENT 'Google Translate, DeepL, etc',
    version_traduccion INT DEFAULT 1,
    calidad_estimada ENUM('baja', 'media', 'alta') DEFAULT 'media',
    -- Metadata
    hash_traduccion VARCHAR(64),
    FOREIGN KEY (id_traduccion_novela_es) REFERENCES novelas_traduccion_espanol(id_traduccion_novela_es) ON DELETE CASCADE,
    FOREIGN KEY (id_idioma) REFERENCES idiomas_disponibles(id_idioma) ON DELETE CASCADE,
    UNIQUE KEY uk_novela_idioma_version (id_traduccion_novela_es, id_idioma, version_traduccion),
    INDEX idx_idioma_estado (id_idioma, estado_traduccion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 13: CAPITULOS_TRADUCCION_IDIOMAS
-- ============================================
DROP TABLE IF EXISTS capitulos_traduccion_idiomas;
CREATE TABLE capitulos_traduccion_idiomas (
    id_traduccion_capitulo_idioma INT PRIMARY KEY AUTO_INCREMENT,
    id_traduccion_capitulo_es INT NOT NULL COMMENT 'Traducción base en español',
    id_traduccion_novela_idioma INT NOT NULL COMMENT 'Novela en el mismo idioma',
    titulo_traducido VARCHAR(200),
    contenido_traducido MEDIUMTEXT,
    estado_traduccion ENUM('pendiente', 'en_progreso', 'completado', 'pausado', 'error') DEFAULT 'pendiente',
    fecha_traduccion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Traductor
    servicio_traduccion VARCHAR(50),
    version_traduccion INT DEFAULT 1,
    calidad_estimada ENUM('baja', 'media', 'alta') DEFAULT 'media',
    -- Optimización
    contenido_comprimido MEDIUMBLOB,
    hash_traduccion VARCHAR(64),
    FOREIGN KEY (id_traduccion_capitulo_es) REFERENCES capitulos_traduccion_espanol(id_traduccion_capitulo_es) ON DELETE CASCADE,
    FOREIGN KEY (id_traduccion_novela_idioma) REFERENCES novelas_traduccion_idiomas(id_traduccion_novela_idioma) ON DELETE CASCADE,
    UNIQUE KEY uk_capitulo_idioma_version (id_traduccion_capitulo_es, id_traduccion_novela_idioma, version_traduccion),
    INDEX idx_novela_idioma (id_traduccion_novela_idioma)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 14: LECTURAS_USUARIO
-- ============================================
DROP TABLE IF EXISTS lecturas_usuario;
CREATE TABLE lecturas_usuario (
    id_lectura INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_novela INT NOT NULL,
    id_capitulo_ultimo INT COMMENT 'Último capítulo leído',
    idioma_lectura VARCHAR(10) DEFAULT 'es',
    fecha_ultima_lectura TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    progreso DECIMAL(5,2) DEFAULT 0 COMMENT 'Porcentaje leído',
    capitulos_leidos INT DEFAULT 0,
    tiempo_lectura_total_minutos INT DEFAULT 0,
    -- APP
    marcador_posicion INT COMMENT 'Posición de scroll',
    velocidad_lectura_ppm INT COMMENT 'Palabras por minuto',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE CASCADE,
    FOREIGN KEY (id_capitulo_ultimo) REFERENCES capitulos(id_capitulo) ON DELETE SET NULL,
    UNIQUE KEY uk_usuario_novela (id_usuario, id_novela),
    INDEX idx_lecturas_usuario_fecha (id_usuario, fecha_ultima_lectura DESC),
    INDEX idx_progreso (id_usuario, progreso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 15: BIBLIOTECA_USUARIO
-- ============================================
DROP TABLE IF EXISTS biblioteca_usuario;
CREATE TABLE biblioteca_usuario (
    id_biblioteca INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_novela INT NOT NULL,
    tipo ENUM('favoritos', 'leyendo', 'pendientes', 'leido', 'abandonado') DEFAULT 'leyendo',
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizado TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- APP
    notificar_nuevos_caps BOOLEAN DEFAULT TRUE,
    orden_personalizado INT COMMENT 'Orden en biblioteca',
    notas_personales TEXT COMMENT 'Notas del usuario sobre la novela',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE CASCADE,
    UNIQUE KEY uk_usuario_novela_tipo (id_usuario, id_novela, tipo),
    INDEX idx_tipo_fecha (tipo, fecha_actualizado DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 16: FUENTES_SCRAPING
-- ============================================
DROP TABLE IF EXISTS fuentes_scraping;
CREATE TABLE fuentes_scraping (
    id_fuente INT PRIMARY KEY AUTO_INCREMENT,
    nombre_fuente VARCHAR(100) NOT NULL,
    url_base VARCHAR(255) NOT NULL,
    tipo_fuente ENUM('qidian', 'webnovel', 'ranobes', 'otro') DEFAULT 'otro',
    pais_origen VARCHAR(50) DEFAULT 'China',
    idioma_contenido VARCHAR(10) DEFAULT 'zh',
    estado ENUM('activa', 'inactiva', 'bloqueada', 'en_prueba') DEFAULT 'activa',
    intervalo_scraping_min INT DEFAULT 1440 COMMENT 'Minutos entre scrapings',
    ultimo_check TIMESTAMP NULL DEFAULT NULL,
    configuracion_scraper JSON COMMENT 'Selectores CSS, XPath, headers, etc',
    tasa_exito DECIMAL(5,2) DEFAULT 100 COMMENT '% de requests exitosos',
    prioridad INT DEFAULT 1 COMMENT 'Prioridad de scraping',
    limite_requests_hora INT DEFAULT 60,
    requiere_vpn BOOLEAN DEFAULT FALSE,
    requiere_login BOOLEAN DEFAULT FALSE,
    credenciales_json JSON COMMENT 'Credenciales encriptadas si requiere login',
    INDEX idx_estado_prioridad (estado, prioridad)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 17: LOGS_SCRAPING
-- ============================================
DROP TABLE IF EXISTS logs_scraping;
CREATE TABLE logs_scraping (
    id_log INT PRIMARY KEY AUTO_INCREMENT,
    id_fuente INT,
    tipo_operacion VARCHAR(50) COMMENT 'scrape_novelas, scrape_capitulos, verificacion',
    estado ENUM('exito', 'error', 'parcial', 'cancelado') DEFAULT 'exito',
    detalles TEXT,
    novelas_obtenidas INT DEFAULT 0,
    capitulos_obtenidos INT DEFAULT 0,
    capitulos_actualizados INT DEFAULT 0,
    errores_encontrados INT DEFAULT 0,
    duracion_segundos INT,
    ip_utilizada VARCHAR(45),
    user_agent VARCHAR(255),
    fecha_ejecucion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_fuente) REFERENCES fuentes_scraping(id_fuente) ON DELETE SET NULL,
    INDEX idx_scraping_fuente (id_fuente, fecha_ejecucion DESC),
    INDEX idx_fecha_estado (fecha_ejecucion DESC, estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 18: COMENTARIOS_CAPITULO
-- ============================================
DROP TABLE IF EXISTS comentarios_capitulo;
CREATE TABLE comentarios_capitulo (
    id_comentario INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_capitulo INT NOT NULL,
    contenido TEXT NOT NULL,
    idioma_comentario VARCHAR(10) DEFAULT 'es',
    fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_editado TIMESTAMP NULL DEFAULT NULL,
    likes INT DEFAULT 0,
    dislikes INT DEFAULT 0,
    -- Moderación
    es_spam BOOLEAN DEFAULT FALSE,
    esta_oculto BOOLEAN DEFAULT FALSE,
    editado BOOLEAN DEFAULT FALSE,
    numero_ediciones INT DEFAULT 0,
    reportes INT DEFAULT 0,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_capitulo) REFERENCES capitulos(id_capitulo) ON DELETE CASCADE,
    INDEX idx_comentarios_capitulo (id_capitulo, fecha_publicacion DESC),
    INDEX idx_usuario (id_usuario, fecha_publicacion DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 19: COMENTARIOS_NOVELA
-- ============================================
DROP TABLE IF EXISTS comentarios_novela;
CREATE TABLE comentarios_novela (
    id_comentario_novela INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_novela INT NOT NULL,
    id_calificacion INT NULL COMMENT 'Vinculado a la calificación',
    contenido TEXT NOT NULL,
    idioma_comentario VARCHAR(10) DEFAULT 'es',
    fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_editado TIMESTAMP NULL DEFAULT NULL,
    likes INT DEFAULT 0,
    dislikes INT DEFAULT 0,
    -- Control de ediciones
    editado BOOLEAN DEFAULT FALSE,
    numero_ediciones INT DEFAULT 0 COMMENT 'Máximo 2 ediciones',
    -- Moderación
    es_spam BOOLEAN DEFAULT FALSE,
    esta_oculto BOOLEAN DEFAULT FALSE,
    reportes INT DEFAULT 0,
    -- Verificación de lectura
    progreso_lectura_momento DECIMAL(5,2) COMMENT 'Progreso cuando comentó',
    capitulos_leidos_momento INT COMMENT 'Capítulos leídos cuando comentó',
    puede_comentar BOOLEAN DEFAULT FALSE COMMENT 'TRUE si leyó mínimo 35%',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE CASCADE,
    UNIQUE KEY uk_usuario_novela (id_usuario, id_novela) COMMENT 'Solo 1 comentario por novela',
    INDEX idx_novela_fecha (id_novela, fecha_publicacion DESC),
    INDEX idx_usuario (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 20: CALIFICACIONES_NOVELA
-- ============================================
DROP TABLE IF EXISTS calificaciones_novela;
CREATE TABLE calificaciones_novela (
    id_calificacion INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_novela INT NOT NULL,
    id_comentario_novela INT NULL COMMENT 'Vinculado al comentario',
    calificacion INT CHECK (calificacion BETWEEN 1 AND 5),
    fecha_calificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizada TIMESTAMP NULL DEFAULT NULL,
    -- Verificación
    progreso_lectura_momento DECIMAL(5,2),
    puede_calificar BOOLEAN DEFAULT FALSE COMMENT 'TRUE si leyó mínimo 35%',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE CASCADE,
    FOREIGN KEY (id_comentario_novela) REFERENCES comentarios_novela(id_comentario_novela) ON DELETE SET NULL,
    UNIQUE KEY uk_usuario_novela (id_usuario, id_novela),
    INDEX idx_novela_calificacion (id_novela, calificacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 21: SESIONES_APP
-- ============================================
DROP TABLE IF EXISTS sesiones_app;
CREATE TABLE sesiones_app (
    id_sesion INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    dispositivo_id VARCHAR(255) COMMENT 'ID único del dispositivo',
    sistema_operativo VARCHAR(20),
    version_app VARCHAR(20),
    token_sesion VARCHAR(255) UNIQUE,
    fecha_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_ultima_actividad TIMESTAMP NULL DEFAULT NULL,
    esta_activa BOOLEAN DEFAULT TRUE,
    ip_conexion VARCHAR(45),
    ubicacion_aprox VARCHAR(100) COMMENT 'Ciudad, País',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    INDEX idx_sesiones_activas (esta_activa, fecha_ultima_actividad DESC),
    INDEX idx_usuario_activas (id_usuario, esta_activa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 22: DESCARGAS_OFFLINE
-- ============================================
DROP TABLE IF EXISTS descargas_offline;
CREATE TABLE descargas_offline (
    id_descarga INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_capitulo INT NOT NULL,
    idioma_descargado VARCHAR(10) DEFAULT 'es',
    fecha_descarga TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tamaño_bytes INT,
    ruta_almacenamiento VARCHAR(500) COMMENT 'Ruta local en dispositivo',
    expira_en TIMESTAMP NULL DEFAULT NULL COMMENT 'Gestión de caché',
    es_usuario_premium BOOLEAN DEFAULT FALSE COMMENT 'Solo para usuarios de pago',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_capitulo) REFERENCES capitulos(id_capitulo) ON DELETE CASCADE,
    UNIQUE KEY uk_usuario_capitulo_idioma (id_usuario, id_capitulo, idioma_descargado),
    INDEX idx_usuario_fecha (id_usuario, fecha_descarga DESC),
    INDEX idx_expiracion (expira_en)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 23: ESTADISTICAS_DIARIAS
-- ============================================
DROP TABLE IF EXISTS estadisticas_diarias;
CREATE TABLE estadisticas_diarias (
    id_estadistica INT PRIMARY KEY AUTO_INCREMENT,
    fecha DATE NOT NULL,
    -- Usuarios
    nuevos_usuarios INT DEFAULT 0,
    usuarios_activos INT DEFAULT 0,
    usuarios_premium INT DEFAULT 0,
    sesiones_totales INT DEFAULT 0,
    -- Contenido
    novelas_agregadas INT DEFAULT 0,
    capitulos_agregados INT DEFAULT 0,
    capitulos_traducidos_es INT DEFAULT 0,
    capitulos_traducidos_otros INT DEFAULT 0,
    -- Lectura
    capitulos_leidos INT DEFAULT 0,
    paginas_leidas BIGINT DEFAULT 0,
    tiempo_lectura_total_min BIGINT DEFAULT 0,
    -- Scraping
    scrapings_exitosos INT DEFAULT 0,
    scrapings_fallidos INT DEFAULT 0,
    -- Interacción
    comentarios_nuevos INT DEFAULT 0,
    calificaciones_nuevas INT DEFAULT 0,
    -- Financiero
    ingresos_anuncios DECIMAL(10,2) DEFAULT 0,
    ingresos_suscripciones DECIMAL(10,2) DEFAULT 0,
    suscripciones_nuevas INT DEFAULT 0,
    suscripciones_canceladas INT DEFAULT 0,
    -- Sistema de puntos
    puntos_canjeados INT DEFAULT 0,
    puntos_otorgados INT DEFAULT 0,
    UNIQUE KEY uk_fecha (fecha),
    INDEX idx_fecha_estadisticas (fecha DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 24: ESTADISTICAS_NOVELAS_HORA
-- ============================================
DROP TABLE IF EXISTS estadisticas_novelas_hora;
CREATE TABLE estadisticas_novelas_hora (
    id_estadistica_hora INT PRIMARY KEY AUTO_INCREMENT,
    id_novela INT NOT NULL,
    fecha_hora DATETIME NOT NULL,
    vistas INT DEFAULT 0,
    lecturas_iniciadas INT DEFAULT 0,
    lecturas_completas INT DEFAULT 0,
    favoritos_agregados INT DEFAULT 0,
    comentarios_nuevos INT DEFAULT 0,
    calificaciones_nuevas INT DEFAULT 0,
    tiempo_lectura_minutos INT DEFAULT 0,
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE CASCADE,
    UNIQUE KEY uk_novela_hora (id_novela, fecha_hora),
    INDEX idx_fecha_hora (fecha_hora),
    INDEX idx_novela_hora (id_novela, fecha_hora DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 25: METRICAS_USUARIO
-- ============================================
DROP TABLE IF EXISTS metricas_usuario;
CREATE TABLE metricas_usuario (
    id_metrica INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    fecha DATE NOT NULL,
    -- Actividad
    capitulos_leidos INT DEFAULT 0,
    tiempo_lectura_min INT DEFAULT 0,
    comentarios_realizados INT DEFAULT 0,
    dias_consecutivos INT DEFAULT 0 COMMENT 'Racha de días activos',
    -- Interacción
    novelas_favoritas_agregadas INT DEFAULT 0,
    calificaciones_dadas INT DEFAULT 0,
    -- APP
    sesiones_app INT DEFAULT 0,
    notificaciones_recibidas INT DEFAULT 0,
    -- Sistema de puntos
    puntos_ganados INT DEFAULT 0,
    puntos_gastados INT DEFAULT 0,
    anuncios_vistos INT DEFAULT 0,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    UNIQUE KEY uk_usuario_fecha (id_usuario, fecha),
    INDEX idx_usuario_metricas (id_usuario, fecha DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 26: APIS_TRADUCCION
-- ============================================
DROP TABLE IF EXISTS apis_traduccion;
CREATE TABLE apis_traduccion (
    id_api INT PRIMARY KEY AUTO_INCREMENT,
    nombre_api VARCHAR(50) NOT NULL COMMENT 'DeepL, GPT-4, Claude, etc',
    tipo_api VARCHAR(20) COMMENT 'traduccion, resumen, correccion',
    proveedor VARCHAR(50) COMMENT 'OpenAI, Anthropic, DeepL',
    endpoint_url VARCHAR(255),
    api_key_encriptada VARCHAR(500) COMMENT 'API Key encriptada',
    modelo VARCHAR(50) COMMENT 'gpt-4, claude-3.5-sonnet, etc',
    prioridad INT DEFAULT 1 COMMENT '1=mayor prioridad',
    -- Límites
    tokens_por_minuto INT DEFAULT 1000,
    tokens_por_dia INT DEFAULT 100000,
    requests_por_minuto INT DEFAULT 10,
    requests_por_dia INT DEFAULT 1000,
    costo_por_millon_tokens DECIMAL(10,4) DEFAULT 0,
    -- Uso actual
    tokens_usados_hoy INT DEFAULT 0,
    requests_hoy INT DEFAULT 0,
    ultimo_uso TIMESTAMP NULL DEFAULT NULL,
    ultimo_reset TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Especialización
    especialidad_chino_es BOOLEAN DEFAULT TRUE,
    configuracion_chino JSON COMMENT 'Parámetros específicos',
    idiomas_soportados JSON COMMENT 'Lista de códigos de idioma',
    -- Estado
    esta_activa BOOLEAN DEFAULT TRUE,
    modo_reserva BOOLEAN DEFAULT FALSE COMMENT 'Solo usar si otras fallan',
    tasa_error DECIMAL(5,2) DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_prioridad_activa (esta_activa, prioridad, modo_reserva)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 27: LOGS_API_TRADUCCION
-- ============================================
DROP TABLE IF EXISTS logs_api_traduccion;
CREATE TABLE logs_api_traduccion (
    id_log INT PRIMARY KEY AUTO_INCREMENT,
    id_api INT NOT NULL,
    id_capitulo INT NULL,
    id_novela INT NULL,
    tipo_operacion VARCHAR(50) COMMENT 'traducir_capitulo, traducir_metadata',
    idioma_origen VARCHAR(10),
    idioma_destino VARCHAR(10),
    tokens_usados INT,
    tokens_prompt INT,
    tokens_completion INT,
    duracion_ms INT,
    costo_estimado DECIMAL(10,6),
    estado ENUM('exito', 'error', 'limite_excedido', 'timeout') DEFAULT 'exito',
    codigo_error VARCHAR(50),
    mensaje_error TEXT,
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_api) REFERENCES apis_traduccion(id_api) ON DELETE CASCADE,
    FOREIGN KEY (id_capitulo) REFERENCES capitulos(id_capitulo) ON DELETE SET NULL,
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE SET NULL,
    INDEX idx_fecha_api (fecha_solicitud DESC, id_api),
    INDEX idx_estado (estado, fecha_solicitud DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 28: ANUNCIANTES
-- ============================================
DROP TABLE IF EXISTS anunciantes;
CREATE TABLE anunciantes (
    id_anunciante INT PRIMARY KEY AUTO_INCREMENT,
    nombre_empresa VARCHAR(100) NOT NULL,
    contacto_nombre VARCHAR(100),
    contacto_email VARCHAR(100),
    telefono VARCHAR(20),
    pais VARCHAR(50),
    sitio_web VARCHAR(255),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    esta_activo BOOLEAN DEFAULT TRUE,
    presupuesto_mensual DECIMAL(10,2) DEFAULT 0,
    gasto_total DECIMAL(10,2) DEFAULT 0,
    INDEX idx_anunciantes_activos (esta_activo, fecha_registro DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 29: CAMPAÑAS_ANUNCIOS
-- ============================================
DROP TABLE IF EXISTS campañas_anuncios;
CREATE TABLE campañas_anuncios (
    id_campaña INT PRIMARY KEY AUTO_INCREMENT,
    id_anunciante INT NOT NULL,
    nombre_campaña VARCHAR(100) NOT NULL,
    -- Ubicaciones
    ubicaciones JSON NOT NULL COMMENT '["inicio_capitulo", "entre_capitulos", "sidebar"]',
    tipo_anuncio ENUM('banner', 'intersticial', 'video', 'gif', 'imagen', 'native') DEFAULT 'banner',
    -- Targeting
    targeting_idioma VARCHAR(10) DEFAULT 'es',
    targeting_paises JSON COMMENT '["MX", "ES", "AR", "CO"]',
    targeting_generos JSON COMMENT 'IDs de géneros',
    targeting_edad_min INT,
    targeting_edad_max INT,
    solo_usuarios_free BOOLEAN DEFAULT TRUE COMMENT 'Solo mostrar a usuarios sin suscripción',
    -- Presupuesto
    presupuesto_total DECIMAL(10,2),
    presupuesto_diario DECIMAL(10,2),
    gasto_actual DECIMAL(10,2) DEFAULT 0,
    fecha_inicio DATE,
    fecha_fin DATE,
    -- Contenido
    titulo_anuncio VARCHAR(200),
    descripcion_anuncio TEXT,
    imagen_url VARCHAR(255),
    video_url VARCHAR(255),
    url_destino VARCHAR(255),
    texto_boton VARCHAR(50),
    duracion_segundos INT COMMENT 'Para videos/gifs',
    skippable BOOLEAN DEFAULT FALSE COMMENT 'Si el usuario puede saltar',
    skip_despues_segundos INT COMMENT 'Segundos antes de poder saltar',
    -- Métricas
    impresiones_maximas INT,
    clics_maximos INT,
    impresiones_actuales INT DEFAULT 0,
    clics_actuales INT DEFAULT 0,
    ctr DECIMAL(5,4) DEFAULT 0 COMMENT 'Click-through rate',
    -- Estado
    estado ENUM('activa', 'pausada', 'finalizada', 'pendiente', 'rechazada') DEFAULT 'pendiente',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_anunciante) REFERENCES anunciantes(id_anunciante) ON DELETE CASCADE,
    INDEX idx_estado_fechas (estado, fecha_inicio, fecha_fin),
    INDEX idx_activas (estado, fecha_inicio, fecha_fin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 30: ANUNCIOS_MOSTRADOS
-- ============================================
DROP TABLE IF EXISTS anuncios_mostrados;
CREATE TABLE anuncios_mostrados (
    id_mostrado INT PRIMARY KEY AUTO_INCREMENT,
    id_campaña INT NOT NULL,
    id_usuario INT NULL,
    id_novela INT NULL,
    id_capitulo INT NULL,
    tipo_evento ENUM('impresion', 'clic', 'vista_completa', 'skip', 'conversion') DEFAULT 'impresion',
    ubicacion VARCHAR(50),
    idioma_usuario VARCHAR(10),
    -- Contexto
    dispositivo ENUM('web', 'android', 'ios') DEFAULT 'web',
    pais_usuario VARCHAR(10),
    tiempo_visualizacion_segundos INT COMMENT 'Tiempo que vio el anuncio',
    completado BOOLEAN DEFAULT FALSE COMMENT 'Si vio el anuncio completo',
    -- Monetización
    ingreso_generado DECIMAL(8,4) DEFAULT 0,
    costo_anunciante DECIMAL(8,4) DEFAULT 0,
    fecha_evento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_campaña) REFERENCES campañas_anuncios(id_campaña) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL,
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE SET NULL,
    FOREIGN KEY (id_capitulo) REFERENCES capitulos(id_capitulo) ON DELETE SET NULL,
    INDEX idx_fecha_campaña (fecha_evento DESC, id_campaña),
    INDEX idx_usuario_fecha (id_usuario, fecha_evento DESC),
    INDEX idx_tipo_evento (tipo_evento, fecha_evento DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 31: CONTROL_ANUNCIOS_USUARIO
-- ============================================
DROP TABLE IF EXISTS control_anuncios_usuario;
CREATE TABLE control_anuncios_usuario (
    id_control INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_capitulo INT NOT NULL,
    fecha_visualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    anuncio_visto BOOLEAN DEFAULT FALSE,
    tipo_anuncio_visto VARCHAR(50),
    puede_ver_capitulo BOOLEAN DEFAULT FALSE COMMENT 'TRUE después de ver anuncio',
    intentos_saltar INT DEFAULT 0 COMMENT 'Intentos de ver sin anuncio',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_capitulo) REFERENCES capitulos(id_capitulo) ON DELETE CASCADE,
    UNIQUE KEY uk_usuario_capitulo (id_usuario, id_capitulo),
    INDEX idx_usuario_fecha (id_usuario, fecha_visualizacion DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 32: PLANES_SUSCRIPCION
-- ============================================
DROP TABLE IF EXISTS planes_suscripcion;
CREATE TABLE planes_suscripcion (
    id_plan INT PRIMARY KEY AUTO_INCREMENT,
    nombre_plan VARCHAR(50) NOT NULL,
    codigo_plan VARCHAR(20) UNIQUE COMMENT 'free, premium, vip',
    descripcion TEXT,
    -- Beneficios
    sin_anuncios BOOLEAN DEFAULT FALSE,
    lectura_modo_offline BOOLEAN DEFAULT FALSE,
    capitulos_avanzados BOOLEAN DEFAULT FALSE,
    acceso_exclusivo BOOLEAN DEFAULT FALSE,
    limite_descargas_dia INT DEFAULT 0,
    velocidad_traduccion_prioritaria BOOLEAN DEFAULT FALSE,
    insignia_exclusiva BOOLEAN DEFAULT FALSE,
    multiplicador_puntos DECIMAL(3,2) DEFAULT 1.00 COMMENT '1.5 = 50% más puntos',
    -- Precios
    precio_mensual DECIMAL(6,2),
    precio_trimestral DECIMAL(8,2),
    precio_anual DECIMAL(8,2),
    moneda VARCHAR(3) DEFAULT 'USD',
    descuento_anual_porcentaje INT COMMENT 'Porcentaje de descuento',
    -- Estado
    esta_activo BOOLEAN DEFAULT TRUE,
    orden_visual INT DEFAULT 1,
    color_distintivo VARCHAR(7) COMMENT 'Color hex del plan',
    icono_url VARCHAR(255),
    es_destacado BOOLEAN DEFAULT FALSE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_planes_activos (esta_activo, orden_visual)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 33: SUSCRIPCIONES_USUARIO
-- ============================================
DROP TABLE IF EXISTS suscripciones_usuario;
CREATE TABLE suscripciones_usuario (
    id_suscripcion INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_plan INT NOT NULL,
    -- Estado
    estado ENUM('activa', 'cancelada', 'expirada', 'pendiente_pago', 'suspendida') DEFAULT 'pendiente_pago',
    metodo_pago ENUM('paypal', 'stripe', 'mercado_pago', 'tarjeta', 'puntos_canje') DEFAULT 'paypal',
    tipo_periodo ENUM('mensual', 'trimestral', 'anual') DEFAULT 'mensual',
    -- Período
    fecha_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_vencimiento TIMESTAMP NULL,
    fecha_cancelacion TIMESTAMP NULL,
    razon_cancelacion TEXT,
    -- Pago
    id_pago_externo VARCHAR(100) COMMENT 'ID en pasarela de pago',
    monto_pagado DECIMAL(8,2),
    moneda VARCHAR(3) DEFAULT 'USD',
    proximo_pago_estimado TIMESTAMP NULL,
    renovacion_automatica BOOLEAN DEFAULT TRUE,
    -- Descuentos y promociones
    codigo_descuento VARCHAR(50),
    descuento_aplicado DECIMAL(6,2),
    es_trial BOOLEAN DEFAULT FALSE COMMENT 'Si es período de prueba',
    dias_trial INT COMMENT 'Días de prueba',
    -- APP
    dispositivo_compra VARCHAR(50),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_plan) REFERENCES planes_suscripcion(id_plan),
    INDEX idx_estado_vencimiento (estado, fecha_vencimiento),
    INDEX idx_usuario_activa (id_usuario, estado),
    INDEX idx_renovacion (renovacion_automatica, fecha_vencimiento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 34: HISTORIAL_PAGOS
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
    referencia_externa VARCHAR(100),
    tipo_pago ENUM('suscripcion', 'renovacion', 'upgrade', 'reembolso') DEFAULT 'suscripcion',
    estado_pago ENUM('completado', 'pendiente', 'fallido', 'reembolsado', 'cancelado') DEFAULT 'pendiente',
    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_confirmacion TIMESTAMP NULL,
    fecha_reembolso TIMESTAMP NULL,
    motivo_fallo TEXT,
    datos_pago JSON COMMENT 'Respuesta de la pasarela',
    comision_plataforma DECIMAL(6,2),
    monto_neto DECIMAL(8,2),
    FOREIGN KEY (id_suscripcion) REFERENCES suscripciones_usuario(id_suscripcion) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    INDEX idx_fecha_estado (fecha_pago DESC, estado_pago),
    INDEX idx_usuario (id_usuario, fecha_pago DESC),
    INDEX idx_transaccion (id_transaccion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 35: SISTEMA_REFERIDOS
-- ============================================
DROP TABLE IF EXISTS sistema_referidos;
CREATE TABLE sistema_referidos (
    id_referido INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario_referente INT NOT NULL COMMENT 'Quien refiere',
    id_usuario_referido INT NOT NULL COMMENT 'Quien fue referido',
    codigo_utilizado VARCHAR(20),
    fecha_registro_referido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'validado', 'completado', 'invalido') DEFAULT 'pendiente',
    -- Recompensas
    puntos_otorgados INT DEFAULT 0,
    bono_referente DECIMAL(6,2) DEFAULT 0,
    bono_referido DECIMAL(6,2) DEFAULT 0,
    fecha_recompensa TIMESTAMP NULL,
    -- Condiciones de validación
    referido_activo BOOLEAN DEFAULT FALSE,
    referido_hizo_compra BOOLEAN DEFAULT FALSE,
    dias_activo INT DEFAULT 0,
    FOREIGN KEY (id_usuario_referente) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario_referido) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    UNIQUE KEY uk_referido (id_usuario_referido),
    INDEX idx_referente (id_usuario_referente, estado),
    INDEX idx_estado (estado, fecha_registro_referido)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 36: CATEGORIAS_LOGROS
-- ============================================
DROP TABLE IF EXISTS categorias_logros;
CREATE TABLE categorias_logros (
    id_categoria_logro INT PRIMARY KEY AUTO_INCREMENT,
    nombre_categoria VARCHAR(50) NOT NULL,
    descripcion TEXT,
    icono_url VARCHAR(255),
    color_hex VARCHAR(7),
    orden_visual INT DEFAULT 1,
    INDEX idx_orden (orden_visual)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 37: LOGROS
-- ============================================
DROP TABLE IF EXISTS logros;
CREATE TABLE logros (
    id_logro INT PRIMARY KEY AUTO_INCREMENT,
    id_categoria INT NOT NULL,
    nombre_logro VARCHAR(100) NOT NULL,
    descripcion TEXT,
    rango ENUM('comun', 'raro', 'unico', 'epico', 'legendario') DEFAULT 'comun',
    icono_url VARCHAR(255),
    -- Condiciones
    tipo_condicion ENUM('referidos', 'novelas_completadas', 'comentarios', 'calificaciones', 'tiempo_lectura', 'dias_consecutivos', 'otro') NOT NULL,
    valor_requerido INT COMMENT 'Cantidad necesaria para desbloquear',
    -- Recompensas
    puntos_recompensa INT DEFAULT 0,
    otorga_titulo BOOLEAN DEFAULT FALSE,
    id_titulo_otorgado INT NULL,
    -- Metadata
    es_oculto BOOLEAN DEFAULT FALSE COMMENT 'Logro secreto',
    es_repetible BOOLEAN DEFAULT FALSE,
    esta_activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categoria) REFERENCES categorias_logros(id_categoria) ON DELETE CASCADE,
    INDEX idx_rango (rango),
    INDEX idx_tipo (tipo_condicion),
    INDEX idx_activo (esta_activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 38: LOGROS_USUARIO
-- ============================================
DROP TABLE IF EXISTS logros_usuario;
CREATE TABLE logros_usuario (
    id_logro_usuario INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_logro INT NOT NULL,
    fecha_obtencion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    progreso_actual INT DEFAULT 0 COMMENT 'Para logros en progreso',
    completado BOOLEAN DEFAULT FALSE,
    puntos_obtenidos INT DEFAULT 0,
    veces_completado INT DEFAULT 1 COMMENT 'Para logros repetibles',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_logro) REFERENCES logros(id_logro) ON DELETE CASCADE,
    UNIQUE KEY uk_usuario_logro (id_usuario, id_logro),
    INDEX idx_usuario_completado (id_usuario, completado),
    INDEX idx_fecha (fecha_obtencion DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 39: TITULOS
-- ============================================
DROP TABLE IF EXISTS titulos;
CREATE TABLE titulos (
    id_titulo INT PRIMARY KEY AUTO_INCREMENT,
    nombre_titulo VARCHAR(100) NOT NULL,
    descripcion TEXT,
    rango ENUM('comun', 'raro', 'unico', 'epico', 'legendario', 'divino') DEFAULT 'comun',
    color_texto VARCHAR(7) COMMENT 'Color hex del título',
    efecto_especial VARCHAR(50) COMMENT 'brillo, animacion, etc',
    -- Obtención
    tipo_obtencion ENUM('logro', 'puntos', 'evento', 'compra', 'regalo') NOT NULL,
    puntos_requeridos INT COMMENT 'Si se obtiene por puntos',
    id_logro_requerido INT NULL COMMENT 'Si se obtiene por logro',
    precio_puntos INT COMMENT 'Si se compra con puntos',
    es_exclusivo BOOLEAN DEFAULT FALSE,
    es_temporal BOOLEAN DEFAULT FALSE,
    duracion_dias INT COMMENT 'Si es temporal',
    esta_activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_logro_requerido) REFERENCES logros(id_logro) ON DELETE SET NULL,
    INDEX idx_rango (rango),
    INDEX idx_tipo (tipo_obtencion),
    INDEX idx_activo (esta_activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 40: TITULOS_USUARIO
-- ============================================
DROP TABLE IF EXISTS titulos_usuario;
CREATE TABLE titulos_usuario (
    id_titulo_usuario INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_titulo INT NOT NULL,
    fecha_obtencion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_expiracion TIMESTAMP NULL COMMENT 'Para títulos temporales',
    esta_equipado BOOLEAN DEFAULT FALSE,
    veces_obtenido INT DEFAULT 1,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_titulo) REFERENCES titulos(id_titulo) ON DELETE CASCADE,
    UNIQUE KEY uk_usuario_titulo (id_usuario, id_titulo),
    INDEX idx_usuario_equipado (id_usuario, esta_equipado),
    INDEX idx_expiracion (fecha_expiracion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 41: CATEGORIAS_RANKING
-- ============================================
DROP TABLE IF EXISTS categorias_ranking;
CREATE TABLE categorias_ranking (
    id_categoria_ranking INT PRIMARY KEY AUTO_INCREMENT,
    nombre_categoria VARCHAR(50) NOT NULL,
    descripcion TEXT,
    tipo_metrica ENUM('lecturas', 'tiempo', 'comentarios', 'referidos', 'racha_dias', 'novelas_completadas') NOT NULL,
    icono_url VARCHAR(255),
    limite_top INT DEFAULT 10 COMMENT 'Top 5, Top 10, etc',
    periodo_actualizacion ENUM('diario', 'semanal', 'mensual', 'general') DEFAULT 'semanal',
    esta_activo BOOLEAN DEFAULT TRUE,
    INDEX idx_tipo (tipo_metrica),
    INDEX idx_activo (esta_activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 42: RANKINGS_USUARIOS
-- ============================================
DROP TABLE IF EXISTS rankings_usuarios;
CREATE TABLE rankings_usuarios (
    id_ranking INT PRIMARY KEY AUTO_INCREMENT,
    id_categoria_ranking INT NOT NULL,
    id_usuario INT NOT NULL,
    posicion INT NOT NULL,
    valor_metrica DECIMAL(10,2) COMMENT 'Valor que determina el ranking',
    periodo_inicio DATE,
    periodo_fin DATE,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- Recompensas
    puntos_recompensa INT DEFAULT 0,
    insignia_otorgada VARCHAR(100),
    titulo_temporal_otorgado INT NULL,
    FOREIGN KEY (id_categoria_ranking) REFERENCES categorias_ranking(id_categoria_ranking) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    UNIQUE KEY uk_categoria_usuario_periodo (id_categoria_ranking, id_usuario, periodo_inicio, periodo_fin),
    INDEX idx_categoria_posicion (id_categoria_ranking, posicion),
    INDEX idx_periodo (periodo_inicio, periodo_fin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 43: ANUNCIOS_FARMEO_PUNTOS
-- ============================================
DROP TABLE IF EXISTS anuncios_farmeo_puntos;
CREATE TABLE anuncios_farmeo_puntos (
    id_farmeo INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_campaña INT NOT NULL,
    fecha_visualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tipo_anuncio VARCHAR(50),
    duracion_segundos INT,
    completado BOOLEAN DEFAULT FALSE,
    puntos_ganados INT DEFAULT 0,
    -- Control de límites
    es_farmeo_manual BOOLEAN DEFAULT TRUE COMMENT 'Usuario entró a ver anuncios manualmente',
    total_anuncios_dia INT COMMENT 'Contador del día',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_campaña) REFERENCES campañas_anuncios(id_campaña) ON DELETE CASCADE,
    INDEX idx_usuario_fecha (id_usuario, fecha_visualizacion DESC),
    INDEX idx_usuario_dia (id_usuario, DATE(fecha_visualizacion))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 44: CONFIGURACION_FARMEO_PUNTOS
-- ============================================
DROP TABLE IF EXISTS configuracion_farmeo_puntos;
CREATE TABLE configuracion_farmeo_puntos (
    id_config_farmeo INT PRIMARY KEY AUTO_INCREMENT,
    tipo_anuncio VARCHAR(50) NOT NULL,
    puntos_por_anuncio INT DEFAULT 1,
    duracion_minima_segundos INT DEFAULT 5,
    maximo_anuncios_dia INT DEFAULT 50 COMMENT 'Límite diario',
    tiempo_entre_anuncios_segundos INT DEFAULT 10,
    esta_activo BOOLEAN DEFAULT TRUE,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 45: CATALOGO_CANJE_PUNTOS
-- ============================================
DROP TABLE IF EXISTS catalogo_canje_puntos;
CREATE TABLE catalogo_canje_puntos (
    id_item_canje INT PRIMARY KEY AUTO_INCREMENT,
    nombre_item VARCHAR(100) NOT NULL,
    descripcion TEXT,
    tipo_item ENUM('vip_temporal', 'titulo', 'logro', 'insignia', 'descuento', 'otro') NOT NULL,
    -- Costos
    puntos_necesarios INT NOT NULL,
    stock_disponible INT COMMENT 'NULL = ilimitado',
    stock_actual INT,
    limite_por_usuario INT COMMENT 'Máximo que puede canjear cada usuario',
    -- Recompensa
    id_titulo_otorgado INT NULL,
    id_logro_otorgado INT NULL,
    dias_vip INT COMMENT 'Si es VIP temporal',
    descuento_porcentaje INT COMMENT 'Si es descuento',
    -- Estado
    es_destacado BOOLEAN DEFAULT FALSE,
    esta_activo BOOLEAN DEFAULT TRUE,
    fecha_inicio_disponibilidad DATE,
    fecha_fin_disponibilidad DATE,
    imagen_url VARCHAR(255),
    orden_visual INT DEFAULT 1,
    FOREIGN KEY (id_titulo_otorgado) REFERENCES titulos(id_titulo) ON DELETE SET NULL,
    FOREIGN KEY (id_logro_otorgado) REFERENCES logros(id_logro) ON DELETE SET NULL,
    INDEX idx_tipo_activo (tipo_item, esta_activo),
    INDEX idx_puntos (puntos_necesarios)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 46: HISTORIAL_CANJES
-- ============================================
DROP TABLE IF EXISTS historial_canjes;
CREATE TABLE historial_canjes (
    id_canje INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_item_canje INT NOT NULL,
    puntos_gastados INT NOT NULL,
    fecha_canje TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('completado', 'pendiente', 'cancelado', 'reembolsado') DEFAULT 'completado',
    fecha_expiracion TIMESTAMP NULL COMMENT 'Para items temporales',
    notas TEXT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_item_canje) REFERENCES catalogo_canje_puntos(id_item_canje) ON DELETE CASCADE,
    INDEX idx_usuario_fecha (id_usuario, fecha_canje DESC),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 47: HISTORIAL_PUNTOS
-- ============================================
DROP TABLE IF EXISTS historial_puntos;
CREATE TABLE historial_puntos (
    id_historial_punto INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    tipo_movimiento ENUM('ganancia', 'gasto', 'bono', 'reembolso', 'expiracion') NOT NULL,
    puntos INT NOT NULL COMMENT 'Positivo para ganancias, negativo para gastos',
    saldo_anterior INT,
    saldo_nuevo INT,
    concepto VARCHAR(255) NOT NULL,
    referencia_id INT COMMENT 'ID del registro relacionado',
    referencia_tipo VARCHAR(50) COMMENT 'anuncio, canje, referido, logro, etc',
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    INDEX idx_usuario_fecha (id_usuario, fecha_movimiento DESC),
    INDEX idx_tipo (tipo_movimiento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 48: RECOMENDACIONES_USUARIOS
-- ============================================
DROP TABLE IF EXISTS recomendaciones_usuarios;
CREATE TABLE recomendaciones_usuarios (
    id_recomendacion INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    titulo_novela VARCHAR(200) NOT NULL,
    autor_novela VARCHAR(100),
    url_referencia VARCHAR(500) COMMENT 'Enlace a Qidian u otro',
    generos_sugeridos JSON,
    etiquetas_sugeridas JSON,
    descripcion TEXT,
    motivo_recomendacion TEXT,
    -- Estado
    estado ENUM('pendiente', 'revisando', 'aprobada', 'rechazada', 'ya_existe') DEFAULT 'pendiente',
    id_novela_asignada INT NULL COMMENT 'Si ya existe',
    fecha_recomendacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_revision TIMESTAMP NULL,
    revisado_por INT NULL,
    comentarios_revisor TEXT,
    -- Recompensa
    puntos_otorgados INT DEFAULT 0,
    recompensa_dada BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_novela_asignada) REFERENCES novelas(id_novela) ON DELETE SET NULL,
    FOREIGN KEY (revisado_por) REFERENCES usuarios(id_usuario) ON DELETE SET NULL,
    INDEX idx_estado_fecha (estado, fecha_recomendacion),
    INDEX idx_usuario (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 49: REPORTES_ERRORES
-- ============================================
DROP TABLE IF EXISTS reportes_errores;
CREATE TABLE reportes_errores (
    id_reporte INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    tipo_error ENUM('traduccion', 'contenido', 'formato', 'scraping', 'tecnico', 'otro') DEFAULT 'traduccion',
    id_novela INT NULL,
    id_capitulo INT NULL,
    idioma_capitulo VARCHAR(10) DEFAULT 'es',
    -- Detalles
    titulo_reporte VARCHAR(200),
    descripcion_error TEXT NOT NULL,
    texto_problematico TEXT,
    sugerencia_correccion TEXT,
    captura_pantalla_url VARCHAR(255),
    -- Estado
    estado ENUM('pendiente', 'revisando', 'corregido', 'duplicado', 'invalido', 'no_procede') DEFAULT 'pendiente',
    prioridad ENUM('baja', 'media', 'alta', 'critica') DEFAULT 'media',
    fecha_reporte TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_revision TIMESTAMP NULL,
    revisado_por INT NULL,
    acciones_tomadas TEXT,
    correccion_aplicada BOOLEAN DEFAULT FALSE,
    fecha_correccion TIMESTAMP NULL,
    -- Recompensa
    puntos_otorgados INT DEFAULT 0 COMMENT 'Recompensa por reportar error válido',
    fuente_scraping VARCHAR(100),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE SET NULL,
    FOREIGN KEY (id_capitulo) REFERENCES capitulos(id_capitulo) ON DELETE SET NULL,
    FOREIGN KEY (revisado_por) REFERENCES usuarios(id_usuario) ON DELETE SET NULL,
    INDEX idx_estado_prioridad (estado, prioridad),
    INDEX idx_fecha_tipo (fecha_reporte DESC, tipo_error),
    INDEX idx_usuario (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 50: COLECCIONES_NOVELAS
-- ============================================
DROP TABLE IF EXISTS colecciones_novelas;
CREATE TABLE colecciones_novelas (
    id_coleccion INT PRIMARY KEY AUTO_INCREMENT,
    nombre_coleccion VARCHAR(100) NOT NULL,
    descripcion TEXT,
    creador_id INT NOT NULL,
    es_publica BOOLEAN DEFAULT TRUE,
    es_oficial BOOLEAN DEFAULT FALSE COMMENT 'Colecciones creadas por admin',
    portada_url VARCHAR(255),
    total_seguidores INT DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultima_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (creador_id) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    INDEX idx_publicas (es_publica, fecha_creacion DESC),
    INDEX idx_oficiales (es_oficial, total_seguidores DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 51: COLECCIONES_CONTENIDO
-- ============================================
DROP TABLE IF EXISTS colecciones_contenido;
CREATE TABLE colecciones_contenido (
    id_contenido INT PRIMARY KEY AUTO_INCREMENT,
    id_coleccion INT NOT NULL,
    id_novela INT NOT NULL,
    orden INT DEFAULT 1,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    comentario TEXT,
    agregado_por INT COMMENT 'Usuario que agregó (si es colección colaborativa)',
    FOREIGN KEY (id_coleccion) REFERENCES colecciones_novelas(id_coleccion) ON DELETE CASCADE,
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE CASCADE,
    FOREIGN KEY (agregado_por) REFERENCES usuarios(id_usuario) ON DELETE SET NULL,
    UNIQUE KEY uk_coleccion_novela (id_coleccion, id_novela),
    INDEX idx_coleccion_orden (id_coleccion, orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 52: NOTIFICACIONES_SISTEMA
-- ============================================
DROP TABLE IF EXISTS notificaciones_sistema;
CREATE TABLE notificaciones_sistema (
    id_notificacion INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    tipo_notificacion ENUM('nuevo_capitulo', 'suscripcion', 'sistema', 'social', 'logro', 'ranking', 'puntos') DEFAULT 'sistema',
    titulo VARCHAR(200),
    contenido TEXT,
    url_accion VARCHAR(255),
    icono_url VARCHAR(255),
    -- Contexto
    id_novela INT NULL,
    id_capitulo INT NULL,
    id_logro INT NULL,
    id_referencia INT COMMENT 'ID genérico de referencia',
    tipo_referencia VARCHAR(50) COMMENT 'Tipo de referencia',
    -- Estado
    leida BOOLEAN DEFAULT FALSE,
    enviada_push BOOLEAN DEFAULT FALSE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_lectura TIMESTAMP NULL,
    fecha_envio_push TIMESTAMP NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_novela) REFERENCES novelas(id_novela) ON DELETE SET NULL,
    FOREIGN KEY (id_capitulo) REFERENCES capitulos(id_capitulo) ON DELETE SET NULL,
    FOREIGN KEY (id_logro) REFERENCES logros(id_logro) ON DELETE SET NULL,
    INDEX idx_usuario_leida (id_usuario, leida, fecha_creacion DESC),
    INDEX idx_tipo (tipo_notificacion, fecha_creacion DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA 53: CONFIGURACION_SISTEMA
-- ============================================
DROP TABLE IF EXISTS configuracion_sistema;
CREATE TABLE configuracion_sistema (
    id_config INT PRIMARY KEY AUTO_INCREMENT,
    clave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT,
    tipo ENUM('string', 'integer', 'boolean', 'json', 'decimal') DEFAULT 'string',
    categoria VARCHAR(50) COMMENT 'general, scraping, traduccion, anuncios, puntos, gamificacion',
    descripcion TEXT,
    editable BOOLEAN DEFAULT TRUE,
    valor_por_defecto TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_config_categoria (categoria, clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- TRIGGERS
-- ============================================

DELIMITER //

-- Trigger 1: Generar código de referido al crear usuario
CREATE TRIGGER trg_generar_codigo_referido
BEFORE INSERT ON usuarios
FOR EACH ROW
BEGIN
    IF NEW.codigo_referido IS NULL THEN
        SET NEW.codigo_referido = CONCAT('REF', LPAD(NEW.id_usuario, 6, '0'), SUBSTRING(MD5(RAND()), 1, 4));
    END IF;
END//

-- Trigger 2: Actualizar estadísticas de novelas al agregar favoritos
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

-- Trigger 3: Actualizar calificación promedio de novela
CREATE TRIGGER trg_actualizar_calificacion_novela
AFTER INSERT ON calificaciones_novela
FOR EACH ROW
BEGIN
    DECLARE avg_cal DECIMAL(3,2);
    DECLARE total_cal INT;
    
    SELECT AVG(calificacion), COUNT(*) INTO avg_cal, total_cal
    FROM calificaciones_novela
    WHERE id_novela = NEW.id_novela;
    
    UPDATE novelas 
    SET promedio_calificacion = COALESCE(avg_cal, 0),
        total_calificaciones = total_cal
    WHERE id_novela = NEW.id_novela;
END//

-- Trigger 4: Vincular comentario con calificación
CREATE TRIGGER trg_vincular_comentario_calificacion
AFTER INSERT ON comentarios_novela
FOR EACH ROW
BEGIN
    UPDATE calificaciones_novela
    SET id_comentario_novela = NEW.id_comentario_novela
    WHERE id_usuario = NEW.id_usuario AND id_novela = NEW.id_novela;
END//

-- Trigger 5: Crear traducción al español automáticamente para capítulos
CREATE TRIGGER trg_crear_traduccion_espanol_capitulo
AFTER INSERT ON capitulos
FOR EACH ROW
BEGIN
    DECLARE id_trad_novela INT;
    
    -- Obtener ID de traducción de novela
    SELECT id_traduccion_novela_es INTO id_trad_novela
    FROM novelas_traduccion_espanol
    WHERE id_novela = NEW.id_novela
    LIMIT 1;
    
    IF id_trad_novela IS NOT NULL THEN
        INSERT INTO capitulos_traduccion_espanol 
        (id_capitulo, id_traduccion_novela_es, estado_traduccion)
        VALUES (NEW.id_capitulo, id_trad_novela, 'pendiente')
        ON DUPLICATE KEY UPDATE estado_traduccion = 'pendiente';
    END IF;
END//

-- Trigger 6: Crear traducción al español automáticamente para novelas
CREATE TRIGGER trg_crear_traduccion_espanol_novela
AFTER INSERT ON novelas
FOR EACH ROW
BEGIN
    INSERT INTO novelas_traduccion_espanol 
    (id_novela, estado_traduccion)
    VALUES (NEW.id_novela, 'pendiente')
    ON DUPLICATE KEY UPDATE estado_traduccion = 'pendiente';
END//

-- Trigger 7: Actualizar contadores de API
CREATE TRIGGER trg_actualizar_contador_api
AFTER INSERT ON logs_api_traduccion
FOR EACH ROW
BEGIN
    UPDATE apis_traduccion 
    SET 
        tokens_usados_hoy = tokens_usados_hoy + COALESCE(NEW.tokens_usados, 0),
        requests_hoy = requests_hoy + 1,
        ultimo_uso = NEW.fecha_solicitud
    WHERE id_api = NEW.id_api;
END//

-- Trigger 8: Notificar nuevo capítulo traducido
CREATE TRIGGER trg_notificar_nuevo_capitulo
AFTER UPDATE ON capitulos_traduccion_espanol
FOR EACH ROW
BEGIN
    DECLARE novel_title VARCHAR(200);
    DECLARE chapter_num INT;
    DECLARE novel_id INT;
    
    IF NEW.estado_traduccion = 'completado' AND OLD.estado_traduccion != 'completado' THEN
        SELECT nt.titulo_traducido, c.numero_capitulo, c.id_novela 
        INTO novel_title, chapter_num, novel_id
        FROM capitulos c
        JOIN novelas_traduccion_espanol nt ON c.id_novela = nt.id_novela
        WHERE c.id_capitulo = NEW.id_capitulo;
        
        INSERT INTO notificaciones_sistema 
        (id_usuario, tipo_notificacion, titulo, contenido, id_novela, id_capitulo)
        SELECT 
            bu.id_usuario,
            'nuevo_capitulo',
            CONCAT('Nuevo capítulo: ', novel_title),
            CONCAT('Capítulo ', chapter_num, ' disponible'),
            novel_id,
            NEW.id_capitulo
        FROM biblioteca_usuario bu
        WHERE bu.id_novela = novel_id
        AND bu.tipo IN ('favoritos', 'leyendo')
        AND bu.notificar_nuevos_caps = TRUE;
    END IF;
END//

-- Trigger 9: Registrar vista de novela
CREATE TRIGGER trg_registrar_vista_novela
AFTER INSERT ON lecturas_usuario
FOR EACH ROW
BEGIN
    UPDATE novelas 
    SET total_vistas = total_vistas + 1
    WHERE id_novela = NEW.id_novela;
END//

-- Trigger 10: Actualizar última actividad de sesión
CREATE TRIGGER trg_actualizar_actividad_sesion
AFTER UPDATE ON lecturas_usuario
FOR EACH ROW
BEGIN
    UPDATE sesiones_app 
    SET fecha_ultima_actividad = NOW()
    WHERE id_usuario = NEW.id_usuario 
    AND esta_activa = TRUE
    ORDER BY fecha_inicio DESC
    LIMIT 1;
END//

-- Trigger 11: Registrar movimiento de puntos
CREATE TRIGGER trg_registrar_movimiento_puntos
AFTER UPDATE ON usuarios
FOR EACH ROW
BEGIN
    IF NEW.puntos_canje != OLD.puntos_canje THEN
        INSERT INTO historial_puntos
        (id_usuario, tipo_movimiento, puntos, saldo_anterior, saldo_nuevo, concepto)
        VALUES (
            NEW.id_usuario,
            IF(NEW.puntos_canje > OLD.puntos_canje, 'ganancia', 'gasto'),
            NEW.puntos_canje - OLD.puntos_canje,
            OLD.puntos_canje,
            NEW.puntos_canje,
            'Actualización automática'
        );
    END IF;
END//

-- Trigger 12: Validar límite de ediciones de comentarios novela
CREATE TRIGGER trg_validar_ediciones_comentario_novela
BEFORE UPDATE ON comentarios_novela
FOR EACH ROW
BEGIN
    IF NEW.editado = TRUE AND NEW.numero_ediciones >= 2 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Límite de ediciones alcanzado (máximo 2)';
    END IF;
    
    IF NEW.contenido != OLD.contenido THEN
        SET NEW.editado = TRUE;
        SET NEW.numero_ediciones = OLD.numero_ediciones + 1;
        SET NEW.fecha_editado = NOW();
    END IF;
END//

-- Trigger 13: Validar progreso mínimo para comentar
CREATE TRIGGER trg_validar_progreso_comentario
BEFORE INSERT ON comentarios_novela
FOR EACH ROW
BEGIN
    DECLARE progreso_actual DECIMAL(5,2);
    
    SELECT progreso INTO progreso_actual
    FROM lecturas_usuario
    WHERE id_usuario = NEW.id_usuario AND id_novela = NEW.id_novela;
    
    IF progreso_actual < 35.00 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Debe leer al menos el 35% de la novela para comentar';
    END IF;
    
    SET NEW.progreso_lectura_momento = progreso_actual;
    SET NEW.puede_comentar = TRUE;
END//

-- Trigger 14: Validar progreso mínimo para calificar
CREATE TRIGGER trg_validar_progreso_calificacion
BEFORE INSERT ON calificaciones_novela
FOR EACH ROW
BEGIN
    DECLARE progreso_actual DECIMAL(5,2);
    
    SELECT progreso INTO progreso_actual
    FROM lecturas_usuario
    WHERE id_usuario = NEW.id_usuario AND id_novela = NEW.id_novela;
    
    IF progreso_actual < 35.00 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Debe leer al menos el 35% de la novela para calificar';
    END IF;
    
    SET NEW.progreso_lectura_momento = progreso_actual;
    SET NEW.puede_calificar = TRUE;
END//

-- Trigger 15: Actualizar contador de referidos
CREATE TRIGGER trg_actualizar_contador_referidos
AFTER INSERT ON sistema_referidos
FOR EACH ROW
BEGIN
    UPDATE usuarios
    SET total_referidos = total_referidos + 1
    WHERE id_usuario = NEW.id_usuario_referente;
END//

-- Trigger 16: Otorgar puntos por referido validado
CREATE TRIGGER trg_otorgar_puntos_referido
AFTER UPDATE ON sistema_referidos
FOR EACH ROW
BEGIN
    IF NEW.estado = 'validado' AND OLD.estado != 'validado' AND NEW.puntos_otorgados > 0 THEN
        UPDATE usuarios
        SET puntos_canje = puntos_canje + NEW.puntos_otorgados
        WHERE id_usuario = NEW.id_usuario_referente;
    END IF;
END//

-- Trigger 17: Actualizar total de comentarios en novela
CREATE TRIGGER trg_actualizar_total_comentarios_novela
AFTER INSERT ON comentarios_novela
FOR EACH ROW
BEGIN
    UPDATE novelas
    SET total_comentarios = total_comentarios + 1
    WHERE id_novela = NEW.id_novela;
END//

-- Trigger 18: Verificar orden de capítulo automáticamente
CREATE TRIGGER trg_verificar_orden_capitulo
BEFORE INSERT ON capitulos
FOR EACH ROW
BEGIN
    IF NEW.orden_capitulo IS NULL OR NEW.orden_capitulo = 0 THEN
        SET NEW.orden_capitulo = NEW.numero_capitulo;
    END IF;
END//

DELIMITER ;

-- ============================================
-- EVENTOS PROGRAMADOS
-- ============================================

DELIMITER //

-- Evento 1: Resetear contadores diarios de APIs
CREATE EVENT IF NOT EXISTS reset_contadores_api_diarios
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_DATE + INTERVAL 1 DAY + INTERVAL 3 HOUR
DO
BEGIN
    UPDATE apis_traduccion 
    SET tokens_usados_hoy = 0, 
        requests_hoy = 0,
        ultimo_reset = NOW()
    WHERE DATE(ultimo_uso) < CURDATE();
END//

-- Evento 2: Desactivar sesiones inactivas
CREATE EVENT IF NOT EXISTS desactivar_sesiones_inactivas
ON SCHEDULE EVERY 1 HOUR
DO
BEGIN
    UPDATE sesiones_app 
    SET esta_activa = FALSE
    WHERE esta_activa = TRUE
    AND fecha_ultima_actividad < NOW() - INTERVAL 7 DAY;
END//

-- Evento 3: Actualizar estadísticas diarias
CREATE EVENT IF NOT EXISTS actualizar_estadisticas_diarias
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_DATE + INTERVAL 1 DAY + INTERVAL 4 HOUR
DO
BEGIN
    INSERT INTO estadisticas_diarias (
        fecha, 
        nuevos_usuarios, 
        usuarios_activos,
        usuarios_premium
    )
    SELECT 
        CURDATE() - INTERVAL 1 DAY,
        COUNT(CASE WHEN DATE(fecha_registro) = CURDATE() - INTERVAL 1 DAY THEN 1 END),
        COUNT(DISTINCT CASE WHEN DATE(ultimo_login) = CURDATE() - INTERVAL 1 DAY THEN id_usuario END),
        COUNT(DISTINCT CASE WHEN EXISTS (
            SELECT 1 FROM suscripciones_usuario su
            WHERE su.id_usuario = usuarios.id_usuario
            AND su.estado = 'activa'
            AND su.fecha_vencimiento > NOW()
        ) THEN id_usuario END)
    FROM usuarios 
    WHERE esta_activo = TRUE;
END//

-- Evento 4: Expirar suscripciones vencidas
CREATE EVENT IF NOT EXISTS expirar_suscripciones
ON SCHEDULE EVERY 1 HOUR
DO
BEGIN
    UPDATE suscripciones_usuario
    SET estado = 'expirada'
    WHERE estado = 'activa'
    AND fecha_vencimiento < NOW();
END//

-- Evento 5: Limpiar descargas offline expiradas
CREATE EVENT IF NOT EXISTS limpiar_descargas_expiradas
ON SCHEDULE EVERY 1 DAY
DO
BEGIN
    DELETE FROM descargas_offline
    WHERE expira_en IS NOT NULL
    AND expira_en < NOW();
END//

-- Evento 6: Actualizar rankings semanales
CREATE EVENT IF NOT EXISTS actualizar_rankings_semanales
ON SCHEDULE EVERY 1 WEEK
STARTS CURRENT_DATE + INTERVAL 1 WEEK
DO
BEGIN
    -- Top lectores por capítulos
    INSERT INTO rankings_usuarios (
        id_categoria_ranking,
        id_usuario,
        posicion,
        valor_metrica,
        periodo_inicio,
        periodo_fin
    )
    SELECT 
        1, -- ID categoría "más lecturas"
        id_usuario,
        ROW_NUMBER() OVER (ORDER BY total_capitulos DESC),
        total_capitulos,
        CURDATE() - INTERVAL 1 WEEK,
        CURDATE()
    FROM (
        SELECT id_usuario, SUM(capitulos_leidos) as total_capitulos
        FROM metricas_usuario
        WHERE fecha >= CURDATE() - INTERVAL 1 WEEK
        GROUP BY id_usuario
        ORDER BY total_capitulos DESC
        LIMIT 10
    ) as top_usuarios;
END//

DELIMITER ;

-- ============================================
-- VISTAS ÚTILES
-- ============================================

-- Vista 1: Capítulos pendientes de traducción al español
CREATE OR REPLACE VIEW vista_capitulos_pendientes_es AS
SELECT 
    c.id_capitulo,
    c.id_novela,
    n.titulo_original,
    c.numero_capitulo,
    c.orden_capitulo,
    c.titulo_original as titulo_capitulo,
    c.palabras_original,
    c.estado_capitulo,
    c.prioridad_traduccion,
    COALESCE(cte.estado_traduccion, 'sin_iniciar') as estado_traduccion
FROM capitulos c
JOIN novelas n ON c.id_novela = n.id_novela
LEFT JOIN capitulos_traduccion_espanol cte ON c.id_capitulo = cte.id_capitulo 
WHERE (cte.estado_traduccion IS NULL OR cte.estado_traduccion IN ('pendiente', 'error'))
AND c.estado_capitulo = 'disponible'
ORDER BY c.prioridad_traduccion DESC, n.total_vistas DESC, c.orden_capitulo ASC;

-- Vista 2: Novelas más populares en español
CREATE OR REPLACE VIEW vista_novelas_populares_es AS
SELECT 
    n.id_novela,
    n.titulo_original,
    nte.titulo_traducido,
    n.promedio_calificacion,
    n.total_vistas,
    n.total_favoritos,
    n.total_comentarios,
    n.total_calificaciones,
    COUNT(DISTINCT cte.id_traduccion_capitulo_es) as capitulos_traducidos,
    n.total_capitulos_originales,
    ROUND((COUNT(DISTINCT cte.id_traduccion_capitulo_es) / NULLIF(n.total_capitulos_originales, 0)) * 100, 2) as porcentaje_traducido
FROM novelas n
LEFT JOIN novelas_traduccion_espanol nte ON n.id_novela = nte.id_novela
LEFT JOIN capitulos c ON n.id_novela = c.id_novela
LEFT JOIN capitulos_traduccion_espanol cte ON c.id_capitulo = cte.id_capitulo 
    AND cte.estado_traduccion = 'completado'
GROUP BY n.id_novela
ORDER BY n.total_vistas DESC, n.promedio_calificacion DESC;

-- Vista 3: Usuarios premium activos
CREATE OR REPLACE VIEW vista_usuarios_premium AS
SELECT 
    u.id_usuario,
    u.username,
    u.email,
    s.id_plan,
    p.nombre_plan,
    p.codigo_plan,
    s.fecha_inicio,
    s.fecha_vencimiento,
    DATEDIFF(s.fecha_vencimiento, CURDATE()) as dias_restantes,
    s.renovacion_automatica
FROM usuarios u
JOIN suscripciones_usuario s ON u.id_usuario = s.id_usuario
JOIN planes_suscripcion p ON s.id_plan = p.id_plan
WHERE s.estado = 'activa'
AND s.fecha_vencimiento > NOW();

-- Vista 4: APIs disponibles para chino-español
CREATE OR REPLACE VIEW vista_apis_chino_es AS
SELECT 
    id_api,
    nombre_api,
    proveedor,
    modelo,
    prioridad,
    tokens_por_minuto - tokens_usados_hoy as tokens_disponibles,
    requests_por_minuto - requests_hoy as requests_disponibles,
    especialidad_chino_es,
    esta_activa,
    modo_reserva,
    tasa_error
FROM apis_traduccion
WHERE especialidad_chino_es = TRUE
AND esta_activa = TRUE
ORDER BY modo_reserva ASC, prioridad ASC, tasa_error ASC;

-- Vista 5: Top usuarios por puntos
CREATE OR REPLACE VIEW vista_top_usuarios_puntos AS
SELECT 
    u.id_usuario,
    u.username,
    u.puntos_canje,
    u.puntos_totales_historico,
    u.total_referidos,
    COUNT(DISTINCT lu.id_logro) as total_logros,
    COUNT(DISTINCT tu.id_titulo) as total_titulos
FROM usuarios u
LEFT JOIN logros_usuario lu ON u.id_usuario = lu.id_usuario AND lu.completado = TRUE
LEFT JOIN titulos_usuario tu ON u.id_usuario = tu.id_usuario
WHERE u.esta_activo = TRUE
GROUP BY u.id_usuario
ORDER BY u.puntos_canje DESC
LIMIT 100;

-- ============================================
-- DATOS INICIALES
-- ============================================

-- Idiomas disponibles
INSERT INTO idiomas_disponibles (codigo_idioma, nombre_idioma, nombre_nativo, esta_activo, prioridad, usa_traductor_automatico) VALUES
('es', 'Español', 'Español', TRUE, 1, FALSE),
('en', 'Inglés', 'English', TRUE, 2, TRUE),
('pt', 'Portugués', 'Português', TRUE, 3, TRUE),
('fr', 'Francés', 'Français', TRUE, 4, TRUE),
('de', 'Alemán', 'Deutsch', TRUE, 5, TRUE),
('it', 'Italiano', 'Italiano', TRUE, 6, TRUE),
('ru', 'Ruso', 'Русский', TRUE, 7, TRUE),
('ja', 'Japonés', '日本語', TRUE, 8, TRUE),
('ko', 'Coreano', '한국어', TRUE, 9, TRUE);

-- Configuración del sistema
INSERT INTO configuracion_sistema (clave, valor, tipo, categoria, descripcion) VALUES
('idioma_principal', 'es', 'string', 'general', 'Idioma principal de la plataforma'),
('traduccion_automatica_es', 'true', 'boolean', 'traduccion', 'Traducir automáticamente al español'),
('prioridad_espanol', '1', 'integer', 'traduccion', 'Prioridad para traducciones al español'),
('limite_capitulos_diarios_es', '100', 'integer', 'scraping', 'Máximo de capítulos a traducir al español por día'),
('scraping_automatico', 'true', 'boolean', 'scraping', 'Activar scraping automático'),
('intervalo_scraping_minutos', '360', 'integer', 'scraping', 'Intervalo entre scrapings'),
('modo_mantenimiento', 'false', 'boolean', 'general', 'Modo mantenimiento'),
('anuncios_activos', 'true', 'boolean', 'anuncios', 'Mostrar anuncios'),
('registro_abierto', 'true', 'boolean', 'general', 'Registro abierto'),
-- Sistema de puntos
('puntos_por_referido', '100', 'integer', 'puntos', 'Puntos por referir usuario'),
('puntos_por_comentario', '5', 'integer', 'puntos', 'Puntos por comentar'),
('puntos_por_calificacion', '3', 'integer', 'puntos', 'Puntos por calificar novela'),
('puntos_por_reporte_valido', '20', 'integer', 'puntos', 'Puntos por reportar error válido'),
('limite_anuncios_farmeo_dia', '50', 'integer', 'puntos', 'Máximo de anuncios para farmear por día'),
('puntos_por_anuncio', '2', 'integer', 'puntos', 'Puntos por ver anuncio completo'),
-- Gamificación
('porcentaje_minimo_comentar', '35', 'integer', 'gamificacion', 'Porcentaje mínimo de lectura para comentar'),
('dias_racha_bono', '7', 'integer', 'gamificacion', 'Días consecutivos para bono'),
('multiplicador_racha', '1.5', 'decimal', 'gamificacion', 'Multiplicador de puntos por racha');

-- Planes de suscripción
INSERT INTO planes_suscripcion (
    nombre_plan, codigo_plan, descripcion, 
    sin_anuncios, lectura_modo_offline, limite_descargas_dia,
    multiplicador_puntos, precio_mensual, precio_anual, 
    descuento_anual_porcentaje, orden_visual, esta_activo
) VALUES
('Gratuito', 'free', 'Acceso básico con anuncios', 
    FALSE, FALSE, 0, 1.00, 0.00, 0.00, 0, 1, TRUE),
('Premium', 'premium', 'Sin anuncios y descargas offline', 
    TRUE, TRUE, 50, 1.25, 4.99, 49.99, 17, 2, TRUE),
('VIP', 'vip', 'Todo Premium + acceso prioritario', 
    TRUE, TRUE, 200, 1.50, 9.99, 99.99, 17, 3, TRUE);

-- Categorías de logros
INSERT INTO categorias_logros (nombre_categoria, descripcion, orden_visual) VALUES
('Lectura', 'Logros relacionados con la lectura de novelas', 1),
('Social', 'Logros por interacción social', 2),
('Referidos', 'Logros por referir usuarios', 3),
('Exploración', 'Logros por explorar la plataforma', 4),
('Especiales', 'Logros únicos y especiales', 5);

-- Algunos logros de ejemplo
INSERT INTO logros (id_categoria, nombre_logro, descripcion, rango, tipo_condicion, valor_requerido, puntos_recompensa, esta_activo) VALUES
(1, 'Primer Paso', 'Lee tu primera novela completa', 'comun', 'novelas_completadas', 1, 50, TRUE),
(1, 'Devorador de Historias', 'Completa 10 novelas', 'raro', 'novelas_completadas', 10, 200, TRUE),
(1, 'Maestro Lector', 'Completa 50 novelas', 'epico', 'novelas_completadas', 50, 1000, TRUE),
(1, 'Leyenda Viviente', 'Completa 100 novelas', 'legendario', 'novelas_completadas', 100, 5000, TRUE),
(2, 'Comunicador', 'Deja tu primer comentario', 'comun', 'comentarios', 1, 10, TRUE),
(2, 'Crítico Activo', 'Deja 50 comentarios', 'raro', 'comentarios', 50, 150, TRUE),
(3, 'Reclutador', 'Refiere a 5 usuarios', 'raro', 'referidos', 5, 100, TRUE),
(3, 'Evangelista', 'Refiere a 20 usuarios', 'epico', 'referidos', 20, 500, TRUE),
(1, 'Maratonista', 'Lee durante 24 horas seguidas', 'epico', 'tiempo_lectura', 1440, 300, TRUE),
(1, 'Constante', 'Mantén una racha de 30 días', 'epico', 'dias_consecutivos', 30, 400, TRUE);

-- Categorías de ranking
INSERT INTO categorias_ranking (nombre_categoria, descripcion, tipo_metrica, limite_top, periodo_actualizacion, esta_activo) VALUES
('Top Lectores Semanales', 'Usuarios que más capítulos leyeron esta semana', 'lecturas', 10, 'semanal', TRUE),
('Top Tiempo de Lectura', 'Usuarios con más tiempo de lectura', 'tiempo', 10, 'mensual', TRUE),
('Top Comentaristas', 'Usuarios más activos comentando', 'comentarios', 10, 'mensual', TRUE),
('Top Referidores', 'Usuarios que más han referido', 'referidos', 10, 'general', TRUE),
('Racha Más Larga', 'Usuarios con la racha de días consecutivos más larga', 'racha_dias', 5, 'general', TRUE);

-- Configuración de farmeo de puntos por anuncios
INSERT INTO configuracion_farmeo_puntos (tipo_anuncio, puntos_por_anuncio, duracion_minima_segundos, maximo_anuncios_dia, tiempo_entre_anuncios_segundos, esta_activo) VALUES
('video', 5, 30, 20, 15, TRUE),
('banner', 1, 5, 50, 5, TRUE),
('intersticial', 3, 10, 30, 10, TRUE),
('gif', 2, 8, 40, 8, TRUE);

-- Catálogo de canje de puntos
INSERT INTO catalogo_canje_puntos (nombre_item, descripcion, tipo_item, puntos_necesarios, dias_vip, esta_activo, orden_visual) VALUES
('VIP 7 días', 'Suscripción VIP por 7 días', 'vip_temporal', 500, 7, TRUE, 1),
('VIP 30 días', 'Suscripción VIP por 30 días', 'vip_temporal', 1800, 30, TRUE, 2),
('Descuento 10%', 'Cupón de 10% de descuento en suscripción', 'descuento', 300, NULL, TRUE, 3),
('Descuento 25%', 'Cupón de 25% de descuento en suscripción', 'descuento', 800, NULL, TRUE, 4);

-- ============================================
-- FIN DEL SCRIPT
-- ============================================