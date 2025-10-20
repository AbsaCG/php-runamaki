-- ================================================
-- RUNA MAKI - Base de Datos SQL
-- Sistema de Trueque de Habilidades Locales
-- ================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS runamaki CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE runamaki;

-- ================================================
-- TABLA: usuarios
-- ================================================
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    puntos_runa INT DEFAULT 100,
    reputacion DECIMAL(3,2) DEFAULT 5.00,
    nivel VARCHAR(50) DEFAULT 'Principiante',
    estado ENUM('activo', 'suspendido', 'inactivo') DEFAULT 'activo',
    rol ENUM('usuario', 'admin') DEFAULT 'usuario',
    ubicacion VARCHAR(100) DEFAULT 'Cusco, Perú',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultima_conexion TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: categorias
-- ================================================
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    icono VARCHAR(50),
    color VARCHAR(20),
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: habilidades
-- ================================================
CREATE TABLE habilidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    categoria_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    horas_ofrecidas INT NOT NULL,
    puntos_sugeridos INT NOT NULL,
    imagen VARCHAR(255) DEFAULT NULL,
    estado ENUM('pendiente', 'aprobado', 'rechazado') DEFAULT 'aprobado',
    visitas INT DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_categoria (categoria_id),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: trueques
-- ================================================
CREATE TABLE trueques (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_ofrece_id INT NOT NULL,
    usuario_recibe_id INT NOT NULL,
    habilidad_ofrece_id INT NOT NULL,
    habilidad_recibe_id INT NOT NULL,
    puntos_intercambio INT NOT NULL,
    estado ENUM('pendiente', 'aceptado', 'completado', 'rechazado', 'cancelado') DEFAULT 'pendiente',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_aceptacion TIMESTAMP NULL,
    fecha_completado TIMESTAMP NULL,
    FOREIGN KEY (usuario_ofrece_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_recibe_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (habilidad_ofrece_id) REFERENCES habilidades(id) ON DELETE CASCADE,
    FOREIGN KEY (habilidad_recibe_id) REFERENCES habilidades(id) ON DELETE CASCADE,
    INDEX idx_usuario_ofrece (usuario_ofrece_id),
    INDEX idx_usuario_recibe (usuario_recibe_id),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: mensajes
-- ================================================
CREATE TABLE mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trueque_id INT NOT NULL,
    remitente_id INT NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    leido BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (trueque_id) REFERENCES trueques(id) ON DELETE CASCADE,
    FOREIGN KEY (remitente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_trueque (trueque_id),
    INDEX idx_remitente (remitente_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: valoraciones
-- ================================================
CREATE TABLE valoraciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trueque_id INT NOT NULL,
    evaluador_id INT NOT NULL,
    evaluado_id INT NOT NULL,
    puntuacion INT NOT NULL CHECK (puntuacion BETWEEN 1 AND 5),
    comentario TEXT,
    fecha_valoracion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trueque_id) REFERENCES trueques(id) ON DELETE CASCADE,
    FOREIGN KEY (evaluador_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (evaluado_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_valoracion (trueque_id, evaluador_id),
    INDEX idx_evaluado (evaluado_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: logros
-- ================================================
CREATE TABLE logros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    icono VARCHAR(50),
    requisito_tipo ENUM('trueques', 'puntos', 'reputacion', 'especial') NOT NULL,
    requisito_valor INT NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: usuarios_logros
-- ================================================
CREATE TABLE usuarios_logros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    logro_id INT NOT NULL,
    fecha_obtencion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (logro_id) REFERENCES logros(id) ON DELETE CASCADE,
    UNIQUE KEY unique_usuario_logro (usuario_id, logro_id),
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: denuncias
-- ================================================
CREATE TABLE denuncias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    denunciante_id INT NOT NULL,
    denunciado_id INT NOT NULL,
    tipo ENUM('usuario', 'habilidad', 'trueque') NOT NULL,
    referencia_id INT NOT NULL,
    motivo TEXT NOT NULL,
    estado ENUM('pendiente', 'en_revision', 'resuelto', 'rechazado') DEFAULT 'pendiente',
    fecha_denuncia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_resolucion TIMESTAMP NULL,
    admin_comentario TEXT,
    FOREIGN KEY (denunciante_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (denunciado_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_estado (estado),
    INDEX idx_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: transacciones_puntos
-- ================================================
CREATE TABLE transacciones_puntos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo ENUM('ganado', 'gastado', 'ajuste_admin') NOT NULL,
    cantidad INT NOT NULL,
    concepto VARCHAR(255) NOT NULL,
    trueque_id INT DEFAULT NULL,
    fecha_transaccion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (trueque_id) REFERENCES trueques(id) ON DELETE SET NULL,
    INDEX idx_usuario (usuario_id),
    INDEX idx_fecha (fecha_transaccion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: configuracion
-- ================================================
CREATE TABLE configuracion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) NOT NULL UNIQUE,
    valor TEXT NOT NULL,
    descripcion TEXT,
    tipo ENUM('texto', 'numero', 'boolean') DEFAULT 'texto',
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- DATOS INICIALES
-- ================================================

-- Insertar categorías
INSERT INTO categorias (nombre, descripcion, icono, color) VALUES
('Educación', 'Enseñanza y tutorías', 'BookOpen', '#C86F3C'),
('Tecnología', 'Servicios tecnológicos y digitales', 'Laptop', '#5A8B4A'),
('Manualidades', 'Trabajos manuales y artesanías', 'Scissors', '#D4A574'),
('Idiomas', 'Clases de idiomas locales e internacionales', 'Languages', '#8B7355'),
('Cocina', 'Preparación de alimentos y gastronomía', 'ChefHat', '#C86F3C'),
('Reparaciones', 'Arreglos y mantenimiento', 'Wrench', '#5A8B4A'),
('Arte', 'Expresiones artísticas', 'Palette', '#D4A574'),
('Música', 'Enseñanza musical e instrumentos', 'Music', '#8B7355');

-- Insertar logros
INSERT INTO logros (nombre, descripcion, icono, requisito_tipo, requisito_valor) VALUES
('Primer Trueque', 'Completaste tu primer intercambio', '🎉', 'trueques', 1),
('10 Trueques', 'Has realizado 10 trueques exitosos', '🌟', 'trueques', 10),
('50 Trueques', 'Experto en intercambios', '🏆', 'trueques', 50),
('100 Trueques', 'Maestro del trueque', '💎', 'trueques', 100),
('100 Puntos Runa', 'Acumulaste 100 puntos', '💰', 'puntos', 100),
('500 Puntos Runa', 'Eres un acumulador', '💵', 'puntos', 500),
('Mentor Comunitario', 'Reputación excelente', '👨‍🏫', 'reputacion', 48),
('Experto Local', 'Referente en tu comunidad', '⭐', 'reputacion', 50);

-- Insertar usuario administrador
INSERT INTO usuarios (nombre, email, password_hash, avatar, puntos_runa, reputacion, rol, estado) VALUES
('Administrador', 'admin@runamaki.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 1000, 5.00, 'admin', 'activo');
-- Contraseña: admin123

-- Insertar usuarios de ejemplo
INSERT INTO usuarios (nombre, email, password_hash, puntos_runa, reputacion) VALUES
('Absalón', 'absalon@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 250, 4.80),
('María Quispe', 'maria@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 180, 4.90),
('Carlos Mendoza', 'carlos@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 320, 5.00),
('Ana Torres', 'ana@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 150, 4.70);
-- Contraseña para todos: admin123

-- Insertar habilidades de ejemplo
INSERT INTO habilidades (usuario_id, categoria_id, titulo, descripcion, horas_ofrecidas, puntos_sugeridos, estado) VALUES
(3, 4, 'Clases de Quechua para principiantes', 'Aprende el idioma ancestral de los Andes. Clases didácticas y culturales.', 2, 30, 'aprobado'),
(4, 2, 'Reparación de laptops y PCs', 'Diagnóstico y solución de problemas de hardware y software.', 1, 50, 'aprobado'),
(3, 7, 'Tejido tradicional andino', 'Enseñanza de técnicas ancestrales de tejido cusqueño.', 3, 40, 'aprobado'),
(2, 8, 'Clases de guitarra nivel básico', 'Aprende a tocar guitarra desde cero con métodos prácticos.', 2, 35, 'aprobado');

-- Insertar configuración inicial
INSERT INTO configuracion (clave, valor, descripcion, tipo) VALUES
('nombre_sitio', 'Runa Maki', 'Nombre de la plataforma', 'texto'),
('puntos_inicial', '100', 'Puntos Runa al registrarse', 'numero'),
('puntos_por_hora', '25', 'Puntos sugeridos por hora de servicio', 'numero'),
('moderacion_activa', 'false', 'Requiere aprobación de habilidades', 'boolean');
