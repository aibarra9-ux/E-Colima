-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-05-2026 a las 08:07:30
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
-- Base de datos: `ecolima`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `slug`) VALUES
(1, 'Flora', 'flora'),
(2, 'Fauna', 'fauna'),
(3, 'Ecosistemas', 'ecosistemas'),
(4, 'Noticias', 'noticias'),
(5, 'Consejos', 'consejos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios`
--

CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL,
  `publicacion_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `contenido` text NOT NULL,
  `aprobado` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comentarios`
--

INSERT INTO `comentarios` (`id`, `publicacion_id`, `usuario_id`, `contenido`, `aprobado`, `fecha_creacion`) VALUES
(1, 13, 5, 'hola', 1, '2026-05-18 23:54:51'),
(2, 13, 5, 'buenas noches', 1, '2026-05-19 04:54:23'),
(3, 13, 5, 'tas hermosa baby', 1, '2026-05-19 04:54:43'),
(4, 15, 5, 'good', 1, '2026-05-19 05:12:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etiquetas`
--

CREATE TABLE `etiquetas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `publicacion_id` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `likes`
--

INSERT INTO `likes` (`id`, `usuario_id`, `publicacion_id`, `fecha`) VALUES
(1, 5, 7, '2026-05-17 02:14:18'),
(9, 24, 8, '2026-05-17 03:09:30'),
(10, 24, 7, '2026-05-17 03:09:32'),
(11, 24, 9, '2026-05-17 16:43:17'),
(12, 5, 13, '2026-05-17 19:04:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicaciones`
--

CREATE TABLE `publicaciones` (
  `id` int(11) NOT NULL,
  `autor_id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `subcategoria_id` int(11) DEFAULT NULL,
  `titulo` varchar(255) NOT NULL,
  `contenido` longtext NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `tipo_media` enum('imagen','video') NOT NULL DEFAULT 'imagen',
  `estado` enum('borrador','pendiente','publicado','rechazado') NOT NULL DEFAULT 'borrador',
  `motivo_rechazo` text DEFAULT NULL,
  `estado_interno` varchar(50) DEFAULT NULL,
  `observaciones_editor` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_publicacion` timestamp NULL DEFAULT NULL,
  `visitas` int(11) DEFAULT 0,
  `fecha_eliminacion` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `publicaciones`
--

INSERT INTO `publicaciones` (`id`, `autor_id`, `categoria_id`, `subcategoria_id`, `titulo`, `contenido`, `imagen`, `tipo_media`, `estado`, `motivo_rechazo`, `estado_interno`, `observaciones_editor`, `fecha_creacion`, `fecha_publicacion`, `visitas`, `fecha_eliminacion`) VALUES
(2, 5, 2, 13, 'putos', 'jajajaja', '1778971358_6a08f2dec77af.jpg', 'imagen', 'rechazado', 'por cabron', 'borrador', NULL, '2026-05-16 22:42:38', NULL, 0, NULL),
(6, 5, 1, 10, 'Avatares', 'Lo mas bacano', '1778981810_6a091bb21b8d1.jpg', 'imagen', 'publicado', NULL, NULL, NULL, '2026-05-17 01:36:50', NULL, 0, NULL),
(7, 5, 5, 36, 'consejo', 'te aconsejo el suicidio', '1778982816_6a091fa0a39e4.jpg', 'imagen', 'publicado', NULL, NULL, NULL, '2026-05-17 01:53:36', NULL, 0, NULL),
(8, 5, 5, 36, 'Consejo 2', 'te aconsejon el no suicidio', '1778982837_6a091fb5693f6.jpg', 'imagen', 'publicado', NULL, NULL, NULL, '2026-05-17 01:53:57', NULL, 0, NULL),
(9, 24, 5, 36, 'JOSH', 'Y los billetes??', '1779035958_6a09ef36ad9e2.jpg', 'imagen', 'publicado', NULL, NULL, NULL, '2026-05-17 16:39:18', NULL, 0, NULL),
(10, 24, 1, 10, 'Hola', 'HOLA SOY BUENO', '1779036064_6a09efa04733f.jpg', 'imagen', 'rechazado', 'NAH BRO NO ERES BUENO', 'borrador', NULL, '2026-05-17 16:41:04', NULL, 0, NULL),
(11, 5, 4, 32, 'HOLA', 'Buenas nocjhes', '1779043789_6a0a0dcd6d9cc.jpg', 'imagen', 'publicado', NULL, NULL, NULL, '2026-05-17 18:49:49', NULL, 0, NULL),
(12, 5, 5, 37, 'j jhxa', 'ajha hja', '1779043858_6a0a0e12c521e.jpg', 'imagen', 'publicado', NULL, NULL, NULL, '2026-05-17 18:50:58', NULL, 0, NULL),
(13, 5, 1, 9, 'holaaaa', 'hysacb hjcbhsac', '1779044644_6a0a112412863.jpg', 'imagen', 'publicado', NULL, NULL, NULL, '2026-05-17 19:04:04', NULL, 0, NULL),
(14, 24, 4, 32, 'RE ZERO', 'el anime mas bacano', '1779044813_6a0a11cda3860.jpg', 'imagen', 'rechazado', 'no', 'borrador', NULL, '2026-05-17 19:06:53', NULL, 0, NULL),
(15, 5, 5, 36, 'Probando video', 'solo probando poner un video', '1779166918_6a0beec6036da.mp4', 'video', 'publicado', NULL, NULL, NULL, '2026-05-19 05:01:58', NULL, 0, NULL),
(16, 5, 1, 7, 'preuba 1', 'prueba 1', '1779167974_6a0bf2e623f23.mp4', 'video', 'publicado', NULL, NULL, NULL, '2026-05-19 05:19:34', NULL, 0, NULL),
(17, 5, 2, 16, 'Prueba 2', 'prueba 2', '1779169208_6a0bf7b80b93f.mp4', 'video', 'publicado', NULL, NULL, NULL, '2026-05-19 05:40:08', NULL, 0, NULL),
(18, 5, 3, 18, 'prueba 3', 'kjaa', '1779169252_6a0bf7e40b09c.mp4', 'video', 'publicado', NULL, NULL, NULL, '2026-05-19 05:40:52', NULL, 0, NULL),
(19, 5, 4, 33, 'prueba 4', 'aaa', '1779169302_6a0bf816b02b6.mp4', 'video', 'publicado', NULL, NULL, NULL, '2026-05-19 05:41:42', NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicacion_etiquetas`
--

CREATE TABLE `publicacion_etiquetas` (
  `publicacion_id` int(11) NOT NULL,
  `etiqueta_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recursos_multimedia`
--

CREATE TABLE `recursos_multimedia` (
  `id` int(11) NOT NULL,
  `publicacion_id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `tipo` enum('imagen','video','pdf') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Administrador', 'Un usuario con los maximos permisos, le permite realizar cualquier acción posible para usuarios con otros roles'),
(2, 'Editor', 'Es un usuario que tiene los permisos para revisar y aprobar o rechazar la publicacion de publicaciones'),
(3, 'Autor', 'Es un usuario que tiene la capacidad de escribir nuevas publicaciones y publicarlas siempre y cuando sean aprobadas por el Editor'),
(4, 'Usuario', 'Es el rol básico que tieen un usuario al registrarse, le permite interactuar con las publicaciones');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_rol`
--

CREATE TABLE `solicitudes_rol` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `rol_solicitado` int(11) NOT NULL,
  `motivo` text NOT NULL,
  `estado` enum('pendiente','aprobado','rechazado') DEFAULT 'pendiente',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_respuesta` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes_rol`
--

INSERT INTO `solicitudes_rol` (`id`, `usuario_id`, `rol_solicitado`, `motivo`, `estado`, `fecha_creacion`, `fecha_respuesta`) VALUES
(1, 7, 1, 'Necesito permisos para subir reportes semanales de limpieza.', 'aprobado', '2026-05-07 15:45:55', '2026-05-07 16:16:57'),
(2, 5, 1, 'Me gustaría solicitar el cambio de rol para poder publicar reportes semanales sobre la calidad del agua en las costas de Manzanillo.', 'rechazado', '2026-05-10 11:51:08', '2026-05-10 11:59:10'),
(5, 24, 2, 'quiero Escribir', 'rechazado', '2026-05-16 20:40:18', '2026-05-16 20:41:33'),
(6, 24, 3, 'Quiero editar ser un editor no autor', 'aprobado', '2026-05-16 20:43:02', '2026-05-16 20:46:17'),
(7, 24, 2, 'Quiero ser Autor por fa, autor cabron', 'aprobado', '2026-05-16 20:44:31', '2026-05-16 20:46:37'),
(8, 24, 2, 'Quiero ser Autor', 'aprobado', '2026-05-16 20:49:36', '2026-05-16 20:51:42'),
(9, 24, 3, 'Ahora quiero ser Editor', 'aprobado', '2026-05-16 20:49:48', '2026-05-16 20:51:07'),
(10, 24, 2, 'Quiero ser editor', 'rechazado', '2026-05-16 20:53:17', '2026-05-16 20:53:50'),
(11, 24, 3, 'Quiero ser autor', 'rechazado', '2026-05-16 20:53:25', '2026-05-16 20:53:53'),
(12, 24, 3, 'Necesito revisar los cambios', 'aprobado', '2026-05-17 16:37:54', '2026-05-17 16:38:16'),
(13, 24, 2, 'por que quiero', 'aprobado', '2026-05-17 19:07:49', '2026-05-17 19:08:14'),
(14, 24, 3, 'hola', 'aprobado', '2026-05-18 00:41:19', '2026-05-18 00:42:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subcategorias`
--

CREATE TABLE `subcategorias` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `subcategorias`
--

INSERT INTO `subcategorias` (`id`, `categoria_id`, `nombre`, `slug`) VALUES
(7, 1, 'Árboles', 'arboles'),
(8, 1, 'Arbustos', 'arbustos'),
(9, 1, 'Cactáceas', 'cactaceas'),
(10, 1, 'Plantas Endémicas', 'plantas-endemicas'),
(11, 1, 'Plantas en Riesgo', 'plantas-en-riesgo'),
(12, 1, 'Reino Fungi', 'reino-fungi'),
(13, 2, 'Mamíferos', 'mamiferos'),
(14, 2, 'Aves', 'aves'),
(15, 2, 'Reptiles', 'reptiles'),
(16, 2, 'Anfibios', 'anfibios'),
(17, 2, 'Invertebrados', 'invertebrados'),
(18, 3, 'Armería', 'armeria'),
(19, 3, 'Colima', 'colima'),
(20, 3, 'Comala', 'comala'),
(21, 3, 'Coquimatlán', 'coquimatlan'),
(22, 3, 'Cuauhtémoc', 'cuauhtemoc'),
(23, 3, 'Ixtlahuacán', 'ixtlahuacan'),
(24, 3, 'Manzanillo', 'manzanillo'),
(25, 3, 'Minatitlán', 'minatitlan'),
(26, 3, 'Tecomán', 'tecoman'),
(27, 3, 'Villa de Álvarez', 'villa-de-alvarez'),
(28, 4, 'Biodiversidad', 'biodiversidad'),
(29, 4, 'Cambio Climático', 'cambio-climatico'),
(30, 4, 'Reforestación', 'reforestacion'),
(31, 4, 'Educación Ambiental', 'educacion-ambiental'),
(32, 4, 'Contaminación', 'contaminacion'),
(33, 4, 'Áreas Naturales Protegidas', 'areas-naturales-protegidas'),
(34, 5, 'Acciones Individuales', 'acciones-individuales'),
(35, 5, 'Acciones Escolares', 'acciones-escolares'),
(36, 5, 'Acciones Comunitarias', 'acciones-comunitarias'),
(37, 5, 'Consumo Responsable', 'consumo-responsable');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `biografia` text DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT 'default_avatar.png',
  `banner_perfil` varchar(255) DEFAULT 'default_banner.jpg',
  `password_hash` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) DEFAULT 1,
  `fecha_eliminacion` timestamp NULL DEFAULT NULL,
  `modo_oscuro` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `rol_id`, `username`, `email`, `biografia`, `foto_perfil`, `banner_perfil`, `password_hash`, `fecha_registro`, `activo`, `fecha_eliminacion`, `modo_oscuro`) VALUES
(5, 1, 'aibarra67', 'aibarra9@ucol.mx', NULL, 'perfil_5_1778957962.jpg', 'banner_5_1778957974.png', '$2y$10$reZGJ4wfPVqwYuq0ExtSe.TY0/3GhgYMBPmyju2upsnjDLE3VQ.yS', '2026-03-26 01:03:06', 1, NULL, 1),
(7, 1, 'Diego', 'anastacio@gmail.com', NULL, 'default_avatar.png', 'default_banner.jpg', '$2y$10$lLGqVlANRCQjLkTROS1dn.8vZM8K3k5/IuoedYIY8NN6Qqk0kOm9G', '2026-03-27 15:12:07', 1, NULL, 0),
(12, 1, 'navarro', 'navatro@gmail.com', NULL, 'default_avatar.png', 'default_banner.jpg', '$2y$10$mLlssIQfshHJODLKde2Bs.rOC/IeMJO9tH3GlLSVxHmFkogLKcxKG', '2026-05-10 22:15:50', 4, NULL, 0),
(13, 1, 'prueba', 'prueba@gmail.com', NULL, 'default_avatar.png', 'default_banner.jpg', '$2y$10$LQJ.mGL8d9IqBgLA2v6o.uWU3W1Z6iswOUlgJApSjFZp7X5ecOV3K', '2026-05-10 22:22:20', 4, NULL, 0),
(24, 3, 'alan', 'eibarra4@ucol.mx', '¡Hola! Estoy usando E-COLIMA', 'perfil_24_1779068011.jpg', 'banner_24_1779068018.jpg', '$2y$10$r8cM8zG309HcM3Mm3I1oSu5tcvV.Qls2uVlepEPggE9g19y9fCn3C', '2026-05-16 20:40:03', 1, NULL, 0),
(25, 4, 'alan2', 'alantapon26@gmail.com', NULL, 'default_avatar.png', 'default_banner.jpg', '$2y$10$kc.wUFvS6sRXM.itPaUkCeLij6qZAnJPQLrxn1rznsbZNo3YeMWrS', '2026-05-17 18:36:51', 1, NULL, 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indices de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `publicacion_id` (`publicacion_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `etiquetas`
--
ALTER TABLE `etiquetas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indices de la tabla `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`,`publicacion_id`),
  ADD KEY `publicacion_id` (`publicacion_id`);

--
-- Indices de la tabla `publicaciones`
--
ALTER TABLE `publicaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `autor_id` (`autor_id`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `fk_publicaciones_subcategoria` (`subcategoria_id`);

--
-- Indices de la tabla `publicacion_etiquetas`
--
ALTER TABLE `publicacion_etiquetas`
  ADD PRIMARY KEY (`publicacion_id`,`etiqueta_id`),
  ADD KEY `etiqueta_id` (`etiqueta_id`);

--
-- Indices de la tabla `recursos_multimedia`
--
ALTER TABLE `recursos_multimedia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `publicacion_id` (`publicacion_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `solicitudes_rol`
--
ALTER TABLE `solicitudes_rol`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `rol_solicitado` (`rol_solicitado`);

--
-- Indices de la tabla `subcategorias`
--
ALTER TABLE `subcategorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `rol_id` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `etiquetas`
--
ALTER TABLE `etiquetas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `publicaciones`
--
ALTER TABLE `publicaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `recursos_multimedia`
--
ALTER TABLE `recursos_multimedia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `solicitudes_rol`
--
ALTER TABLE `solicitudes_rol`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `subcategorias`
--
ALTER TABLE `subcategorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`publicacion_id`) REFERENCES `publicaciones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comentarios_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`publicacion_id`) REFERENCES `publicaciones` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `publicaciones`
--
ALTER TABLE `publicaciones`
  ADD CONSTRAINT `fk_publicaciones_subcategoria` FOREIGN KEY (`subcategoria_id`) REFERENCES `subcategorias` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `publicaciones_ibfk_1` FOREIGN KEY (`autor_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `publicaciones_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);

--
-- Filtros para la tabla `publicacion_etiquetas`
--
ALTER TABLE `publicacion_etiquetas`
  ADD CONSTRAINT `publicacion_etiquetas_ibfk_1` FOREIGN KEY (`publicacion_id`) REFERENCES `publicaciones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publicacion_etiquetas_ibfk_2` FOREIGN KEY (`etiqueta_id`) REFERENCES `etiquetas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `recursos_multimedia`
--
ALTER TABLE `recursos_multimedia`
  ADD CONSTRAINT `recursos_multimedia_ibfk_1` FOREIGN KEY (`publicacion_id`) REFERENCES `publicaciones` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `solicitudes_rol`
--
ALTER TABLE `solicitudes_rol`
  ADD CONSTRAINT `solicitudes_rol_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `solicitudes_rol_ibfk_2` FOREIGN KEY (`rol_solicitado`) REFERENCES `roles` (`id`);

--
-- Filtros para la tabla `subcategorias`
--
ALTER TABLE `subcategorias`
  ADD CONSTRAINT `subcategorias_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
