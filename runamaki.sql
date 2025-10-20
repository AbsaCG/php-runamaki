-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-10-2025 a las 05:22:42
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `runamaki`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `icono` varchar(50) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `icono`, `color`, `activo`, `fecha_creacion`) VALUES
(1, 'Educación', 'Enseñanza y tutorías', 'BookOpen', '#C86F3C', 1, '2025-10-20 00:46:16'),
(2, 'Tecnología', 'Servicios tecnológicos y digitales', 'Laptop', '#5A8B4A', 1, '2025-10-20 00:46:16'),
(3, 'Manualidades', 'Trabajos manuales y artesanías', 'Scissors', '#D4A574', 1, '2025-10-20 00:46:16'),
(4, 'Idiomas', 'Clases de idiomas locales e internacionales', 'Languages', '#8B7355', 1, '2025-10-20 00:46:16'),
(5, 'Cocina', 'Preparación de alimentos y gastronomía', 'ChefHat', '#C86F3C', 1, '2025-10-20 00:46:16'),
(6, 'Reparaciones', 'Arreglos y mantenimiento', 'Wrench', '#5A8B4A', 1, '2025-10-20 00:46:16'),
(7, 'Arte', 'Expresiones artísticas', 'Palette', '#D4A574', 1, '2025-10-20 00:46:16'),
(8, 'Música', 'Enseñanza musical e instrumentos', 'Music', '#8B7355', 1, '2025-10-20 00:46:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` text NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('texto','numero','boolean') DEFAULT 'texto',
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `clave`, `valor`, `descripcion`, `tipo`, `fecha_actualizacion`) VALUES
(1, 'nombre_sitio', 'Runa Maki', 'Nombre de la plataforma', 'texto', '2025-10-20 00:46:16'),
(2, 'puntos_inicial', '100', 'Puntos Runa al registrarse', 'numero', '2025-10-20 00:46:16'),
(3, 'puntos_por_hora', '25', 'Puntos sugeridos por hora de servicio', 'numero', '2025-10-20 00:46:16'),
(4, 'moderacion_activa', 'false', 'Requiere aprobación de habilidades', 'boolean', '2025-10-20 00:46:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `denuncias`
--

CREATE TABLE `denuncias` (
  `id` int(11) NOT NULL,
  `denunciante_id` int(11) NOT NULL,
  `denunciado_id` int(11) NOT NULL,
  `tipo` enum('usuario','habilidad','trueque') NOT NULL,
  `referencia_id` int(11) NOT NULL,
  `motivo` text NOT NULL,
  `estado` enum('pendiente','en_revision','resuelto','rechazado') DEFAULT 'pendiente',
  `fecha_denuncia` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_resolucion` timestamp NULL DEFAULT NULL,
  `admin_comentario` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `habilidades`
--

CREATE TABLE `habilidades` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text NOT NULL,
  `horas_ofrecidas` int(11) NOT NULL,
  `puntos_sugeridos` int(11) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `estado` enum('pendiente','aprobado','rechazado') DEFAULT 'aprobado',
  `visitas` int(11) DEFAULT 0,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `habilidades`
--

INSERT INTO `habilidades` (`id`, `usuario_id`, `categoria_id`, `titulo`, `descripcion`, `horas_ofrecidas`, `puntos_sugeridos`, `imagen`, `estado`, `visitas`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 3, 4, 'Clases de Quechua para principiantes', 'Aprende el idioma ancestral de los Andes. Clases didácticas y culturales.', 2, 30, NULL, 'aprobado', 17, '2025-10-20 00:46:16', '2025-10-20 01:54:06'),
(2, 4, 2, 'Reparación de laptops y PCs', 'Diagnóstico y solución de problemas de hardware y software.', 1, 50, NULL, 'aprobado', 4, '2025-10-20 00:46:16', '2025-10-20 01:32:36'),
(3, 3, 5, 'Tejido tradicional andino', 'Enseñanza de técnicas ancestrales de tejido cusqueño.', 3, 40, NULL, 'aprobado', 7, '2025-10-20 00:46:16', '2025-10-20 01:57:07'),
(4, 2, 8, 'Clases de guitarra nivel básico', 'Aprende a tocar guitarra desde cero con métodos prácticos.', 2, 35, NULL, 'aprobado', 2, '2025-10-20 00:46:16', '2025-10-20 01:38:01'),
(8, 3, 2, 'phppppppppppp', 'hola mundo', 2, 50, NULL, 'aprobado', 3, '2025-10-20 01:27:01', '2025-10-20 01:54:21'),
(9, 6, 1, 'como arto', 'sssssssssssssssss', 2, 50, NULL, 'aprobado', 0, '2025-10-20 01:41:57', '2025-10-20 01:41:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logros`
--

CREATE TABLE `logros` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `icono` varchar(50) DEFAULT NULL,
  `requisito_tipo` enum('trueques','puntos','reputacion','especial') NOT NULL,
  `requisito_valor` int(11) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `logros`
--

INSERT INTO `logros` (`id`, `nombre`, `descripcion`, `icono`, `requisito_tipo`, `requisito_valor`, `activo`, `fecha_creacion`) VALUES
(1, 'Primer Trueque', 'Completaste tu primer intercambio', '🎉', 'trueques', 1, 1, '2025-10-20 00:46:16'),
(2, '10 Trueques', 'Has realizado 10 trueques exitosos', '🌟', 'trueques', 10, 1, '2025-10-20 00:46:16'),
(3, '50 Trueques', 'Experto en intercambios', '🏆', 'trueques', 50, 1, '2025-10-20 00:46:16'),
(4, '100 Trueques', 'Maestro del trueque', '💎', 'trueques', 100, 1, '2025-10-20 00:46:16'),
(5, '100 Puntos Runa', 'Acumulaste 100 puntos', '💰', 'puntos', 100, 1, '2025-10-20 00:46:16'),
(6, '500 Puntos Runa', 'Eres un acumulador', '💵', 'puntos', 500, 1, '2025-10-20 00:46:16'),
(7, 'Mentor Comunitario', 'Reputación excelente', '👨‍🏫', 'reputacion', 48, 1, '2025-10-20 00:46:16'),
(8, 'Experto Local', 'Referente en tu comunidad', '⭐', 'reputacion', 50, 1, '2025-10-20 00:46:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL,
  `trueque_id` int(11) NOT NULL,
  `remitente_id` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha_envio` timestamp NOT NULL DEFAULT current_timestamp(),
  `leido` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`id`, `trueque_id`, `remitente_id`, `mensaje`, `fecha_envio`, `leido`) VALUES
(1, 5, 6, 'dsdsds', '2025-10-20 01:42:10', 0),
(2, 5, 6, 'hola', '2025-10-20 01:43:47', 0),
(3, 5, 3, 'hola que tal', '2025-10-20 01:44:24', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transacciones_puntos`
--

CREATE TABLE `transacciones_puntos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo` enum('ganado','gastado','ajuste_admin') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `concepto` varchar(255) NOT NULL,
  `trueque_id` int(11) DEFAULT NULL,
  `fecha_transaccion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `transacciones_puntos`
--

INSERT INTO `transacciones_puntos` (`id`, `usuario_id`, `tipo`, `cantidad`, `concepto`, `trueque_id`, `fecha_transaccion`) VALUES
(1, 6, 'ganado', 50, 'Trueque completado', 5, '2025-10-20 01:44:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trueques`
--

CREATE TABLE `trueques` (
  `id` int(11) NOT NULL,
  `usuario_ofrece_id` int(11) NOT NULL,
  `usuario_recibe_id` int(11) NOT NULL,
  `habilidad_ofrece_id` int(11) NOT NULL,
  `habilidad_recibe_id` int(11) NOT NULL,
  `puntos_intercambio` int(11) NOT NULL,
  `estado` enum('pendiente','aceptado','completado','rechazado','cancelado') DEFAULT 'pendiente',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_aceptacion` timestamp NULL DEFAULT NULL,
  `fecha_completado` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `trueques`
--

INSERT INTO `trueques` (`id`, `usuario_ofrece_id`, `usuario_recibe_id`, `habilidad_ofrece_id`, `habilidad_recibe_id`, `puntos_intercambio`, `estado`, `fecha_creacion`, `fecha_aceptacion`, `fecha_completado`) VALUES
(5, 6, 3, 9, 8, 50, 'completado', '2025-10-20 01:42:10', '2025-10-20 01:44:27', '2025-10-20 01:44:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `puntos_runa` int(11) DEFAULT 100,
  `reputacion` decimal(3,2) DEFAULT 5.00,
  `nivel` varchar(50) DEFAULT 'Principiante',
  `estado` enum('activo','suspendido','inactivo') DEFAULT 'activo',
  `rol` enum('usuario','admin') DEFAULT 'usuario',
  `ubicacion` varchar(100) DEFAULT 'Cusco, Perú',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultima_conexion` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password_hash`, `avatar`, `puntos_runa`, `reputacion`, `nivel`, `estado`, `rol`, `ubicacion`, `fecha_registro`, `ultima_conexion`) VALUES
(1, 'Administrador', 'admin@runamaki.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 1000, 5.00, 'Principiante', 'activo', 'admin', 'Cusco, Perú', '2025-10-20 00:46:16', '2025-10-20 01:27:44'),
(2, 'Absalón', 'absalon@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 250, 4.80, 'Principiante', 'activo', 'usuario', 'Cusco, Perú', '2025-10-20 00:46:16', NULL),
(3, 'María Quispe', 'maria@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 180, 4.90, 'Principiante', 'activo', 'usuario', 'Cusco, Perú', '2025-10-20 00:46:16', '2025-10-20 01:44:08'),
(4, 'Carlos Mendoza', 'carlos@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 320, 5.00, 'Principiante', 'activo', 'usuario', 'Cusco, Perú', '2025-10-20 00:46:16', NULL),
(5, 'Ana Torres', 'ana@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 150, 4.70, 'Principiante', 'activo', 'usuario', 'Cusco, Perú', '2025-10-20 00:46:16', NULL),
(6, 'Absalón Cespedes Galiano', 'absa@gmail.com', '$2y$10$2KwUs76H5IyBf/pVeaOK2OoBIesrv153UNiPHZgIAmAt//edgw1b6', NULL, 150, 5.00, 'Principiante', 'activo', 'usuario', 'Cusco, Perú', '2025-10-20 01:08:38', '2025-10-20 01:44:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_logros`
--

CREATE TABLE `usuarios_logros` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `logro_id` int(11) NOT NULL,
  `fecha_obtencion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `valoraciones`
--

CREATE TABLE `valoraciones` (
  `id` int(11) NOT NULL,
  `trueque_id` int(11) NOT NULL,
  `evaluador_id` int(11) NOT NULL,
  `evaluado_id` int(11) NOT NULL,
  `puntuacion` int(11) NOT NULL CHECK (`puntuacion` between 1 and 5),
  `comentario` text DEFAULT NULL,
  `fecha_valoracion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`);

--
-- Indices de la tabla `denuncias`
--
ALTER TABLE `denuncias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `denunciante_id` (`denunciante_id`),
  ADD KEY `denunciado_id` (`denunciado_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_tipo` (`tipo`);

--
-- Indices de la tabla `habilidades`
--
ALTER TABLE `habilidades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_categoria` (`categoria_id`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indices de la tabla `logros`
--
ALTER TABLE `logros`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_trueque` (`trueque_id`),
  ADD KEY `idx_remitente` (`remitente_id`);

--
-- Indices de la tabla `transacciones_puntos`
--
ALTER TABLE `transacciones_puntos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trueque_id` (`trueque_id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_fecha` (`fecha_transaccion`);

--
-- Indices de la tabla `trueques`
--
ALTER TABLE `trueques`
  ADD PRIMARY KEY (`id`),
  ADD KEY `habilidad_ofrece_id` (`habilidad_ofrece_id`),
  ADD KEY `habilidad_recibe_id` (`habilidad_recibe_id`),
  ADD KEY `idx_usuario_ofrece` (`usuario_ofrece_id`),
  ADD KEY `idx_usuario_recibe` (`usuario_recibe_id`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indices de la tabla `usuarios_logros`
--
ALTER TABLE `usuarios_logros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_usuario_logro` (`usuario_id`,`logro_id`),
  ADD KEY `logro_id` (`logro_id`),
  ADD KEY `idx_usuario` (`usuario_id`);

--
-- Indices de la tabla `valoraciones`
--
ALTER TABLE `valoraciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_valoracion` (`trueque_id`,`evaluador_id`),
  ADD KEY `evaluador_id` (`evaluador_id`),
  ADD KEY `idx_evaluado` (`evaluado_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `denuncias`
--
ALTER TABLE `denuncias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `habilidades`
--
ALTER TABLE `habilidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `logros`
--
ALTER TABLE `logros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `transacciones_puntos`
--
ALTER TABLE `transacciones_puntos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `trueques`
--
ALTER TABLE `trueques`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios_logros`
--
ALTER TABLE `usuarios_logros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `valoraciones`
--
ALTER TABLE `valoraciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `denuncias`
--
ALTER TABLE `denuncias`
  ADD CONSTRAINT `denuncias_ibfk_1` FOREIGN KEY (`denunciante_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `denuncias_ibfk_2` FOREIGN KEY (`denunciado_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `habilidades`
--
ALTER TABLE `habilidades`
  ADD CONSTRAINT `habilidades_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `habilidades_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`trueque_id`) REFERENCES `trueques` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mensajes_ibfk_2` FOREIGN KEY (`remitente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `transacciones_puntos`
--
ALTER TABLE `transacciones_puntos`
  ADD CONSTRAINT `transacciones_puntos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transacciones_puntos_ibfk_2` FOREIGN KEY (`trueque_id`) REFERENCES `trueques` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `trueques`
--
ALTER TABLE `trueques`
  ADD CONSTRAINT `trueques_ibfk_1` FOREIGN KEY (`usuario_ofrece_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trueques_ibfk_2` FOREIGN KEY (`usuario_recibe_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trueques_ibfk_3` FOREIGN KEY (`habilidad_ofrece_id`) REFERENCES `habilidades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trueques_ibfk_4` FOREIGN KEY (`habilidad_recibe_id`) REFERENCES `habilidades` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios_logros`
--
ALTER TABLE `usuarios_logros`
  ADD CONSTRAINT `usuarios_logros_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuarios_logros_ibfk_2` FOREIGN KEY (`logro_id`) REFERENCES `logros` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `valoraciones`
--
ALTER TABLE `valoraciones`
  ADD CONSTRAINT `valoraciones_ibfk_1` FOREIGN KEY (`trueque_id`) REFERENCES `trueques` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `valoraciones_ibfk_2` FOREIGN KEY (`evaluador_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `valoraciones_ibfk_3` FOREIGN KEY (`evaluado_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
