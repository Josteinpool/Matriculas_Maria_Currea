-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-01-2026 a las 22:24:24
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistema_matriculas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_acudiente`
--

CREATE TABLE `datos_acudiente` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nombres` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `celular` varchar(20) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text NOT NULL,
  `ciudad` varchar(100) NOT NULL,
  `tipo_documento` enum('CC','CE','TI','RC','PAS') NOT NULL,
  `numero_documento` varchar(50) NOT NULL,
  `lugar_expedicion` varchar(255) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `lugar_nacimiento` varchar(255) NOT NULL,
  `genero` enum('Masculino','Femenino','Otro','Prefiero no decir') NOT NULL,
  `correo` varchar(255) NOT NULL,
  `nivel_estudios` enum('Primaria','Bachillerato','Técnico','Tecnólogo','Pregrado','Especialización','Maestría','Doctorado','Ninguno') NOT NULL,
  `ocupacion` varchar(255) NOT NULL,
  `es_acudiente` enum('si','no') NOT NULL,
  `asiste_reuniones` enum('si','no') NOT NULL,
  `convive_estudiante` enum('si','no') NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `datos_acudiente`
--

INSERT INTO `datos_acudiente` (`id`, `usuario_id`, `nombres`, `apellidos`, `celular`, `telefono`, `direccion`, `ciudad`, `tipo_documento`, `numero_documento`, `lugar_expedicion`, `fecha_nacimiento`, `lugar_nacimiento`, `genero`, `correo`, `nivel_estudios`, `ocupacion`, `es_acudiente`, `asiste_reuniones`, `convive_estudiante`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 1, 'jenni', 'gonzalez', '302568945', '', 'carrera 38 # 34', 'soacha', 'CC', '1001297866', 'soacha', '2000-12-01', 'bogota', 'Femenino', 'jennifer@gmail.com', 'Técnico', 'fono', 'si', 'no', 'si', '2025-11-14 14:26:00', '2025-11-20 15:01:08'),
(4, 3, 'amanda', 'niño', '3062589732', '', 'carrera 25 # 23 48', 'Bogota', 'CC', '125698746', 'Bogota', '1979-02-14', 'Bogota', 'Femenino', 'Amanda@hotmail.com', 'Bachillerato', 'Independiente', 'si', 'si', 'si', '2025-12-24 14:25:53', '2025-12-24 14:25:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_madre`
--

CREATE TABLE `datos_madre` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nombres` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `celular` varchar(20) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text NOT NULL,
  `ciudad` varchar(100) NOT NULL,
  `tipo_documento` enum('CC','CE','TI','RC','PAS') NOT NULL,
  `numero_documento` varchar(50) NOT NULL,
  `lugar_expedicion` varchar(255) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `lugar_nacimiento` varchar(255) NOT NULL,
  `genero` enum('Femenino','Masculino','Otro','Prefiero no decir') NOT NULL,
  `correo` varchar(255) NOT NULL,
  `nivel_estudios` enum('Primaria','Bachillerato','Técnico','Tecnólogo','Pregrado','Especialización','Maestría','Doctorado','Ninguno') NOT NULL,
  `ocupacion` varchar(255) NOT NULL,
  `es_acudiente` enum('si','no') NOT NULL,
  `asiste_reuniones` enum('si','no') NOT NULL,
  `cabeza_familia` enum('si','no') NOT NULL,
  `convive_estudiante` enum('si','no') NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `datos_madre`
--

INSERT INTO `datos_madre` (`id`, `usuario_id`, `nombres`, `apellidos`, `celular`, `telefono`, `direccion`, `ciudad`, `tipo_documento`, `numero_documento`, `lugar_expedicion`, `fecha_nacimiento`, `lugar_nacimiento`, `genero`, `correo`, `nivel_estudios`, `ocupacion`, `es_acudiente`, `asiste_reuniones`, `cabeza_familia`, `convive_estudiante`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(4, 1, 'lu', 'mar', '3016824598', '', 'carrera 38 # 34', 'soacha', 'CC', '51752684', 'soacha', '1999-10-12', 'bogota', 'Femenino', 'luz@gmail.com', 'Primaria', 'ama de casa', 'no', 'si', 'si', 'si', '2025-11-20 14:48:50', '2025-11-20 14:52:13'),
(9, 3, 'amanda', 'niño', '3062589732', '', 'carrera 25 # 23 48', 'Bogota', 'CC', '125698746', 'Bogota', '1979-02-15', 'Bogota', 'Femenino', 'Amanda@hotmail.com', 'Bachillerato', 'Independiente', 'si', 'si', 'si', 'si', '2025-12-24 14:15:58', '2025-12-24 14:15:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_padre`
--

CREATE TABLE `datos_padre` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nombres` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `celular` varchar(20) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text NOT NULL,
  `ciudad` varchar(100) NOT NULL,
  `tipo_documento` enum('CC','CE','TI','RC','PAS') NOT NULL,
  `numero_documento` varchar(50) NOT NULL,
  `lugar_expedicion` varchar(255) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `lugar_nacimiento` varchar(255) NOT NULL,
  `genero` enum('Masculino','Femenino','Otro','Prefiero no decir') NOT NULL,
  `correo` varchar(255) NOT NULL,
  `nivel_estudios` enum('Primaria','Bachillerato','Técnico','Tecnólogo','Pregrado','Especialización','Maestría','Doctorado','Ninguno') NOT NULL,
  `ocupacion` varchar(255) NOT NULL,
  `es_acudiente` enum('si','no') NOT NULL,
  `asiste_reuniones` enum('si','no') NOT NULL,
  `cabeza_familia` enum('si','no') NOT NULL,
  `convive_estudiante` enum('si','no') NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `datos_padre`
--

INSERT INTO `datos_padre` (`id`, `usuario_id`, `nombres`, `apellidos`, `celular`, `telefono`, `direccion`, `ciudad`, `tipo_documento`, `numero_documento`, `lugar_expedicion`, `fecha_nacimiento`, `lugar_nacimiento`, `genero`, `correo`, `nivel_estudios`, `ocupacion`, `es_acudiente`, `asiste_reuniones`, `cabeza_familia`, `convive_estudiante`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 1, 'alfred', 'gonzalez', '3026845978', '', 'carrera 38 # 34', 'soacha', 'CC', '1115487955', 'soacha', '2000-02-14', 'soacha', 'Femenino', 'alfredo@gmail.com', 'Bachillerato', 'operario', 'no', 'no', 'si', 'si', '2025-11-14 14:13:15', '2025-11-20 15:01:04'),
(5, 3, 'Mario', 'Bros', '1547896324', '', 'carrera 23 # 23 45', 'Bogota', 'CC', '1526398765', 'Bogota', '1958-06-01', 'Madrid', 'Masculino', 'bros@gmail.com', 'Especialización', 'Maestro de artes marciales', 'si', 'si', 'si', 'si', '2025-12-24 14:23:01', '2025-12-24 14:23:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_salud`
--

CREATE TABLE `datos_salud` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tratamiento_medico` enum('si','no') NOT NULL,
  `alergia_medicamentos` enum('si','no') NOT NULL,
  `alergia_especificar` text DEFAULT NULL,
  `enfermedad_diagnosticada` enum('si','no') NOT NULL,
  `enfermedad_especificar` text DEFAULT NULL,
  `peso` decimal(4,1) NOT NULL,
  `estatura` decimal(4,1) NOT NULL,
  `observaciones_fisicas` text DEFAULT NULL,
  `medicamentos_permanentes` text DEFAULT NULL,
  `informacion_salud_adicional` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `datos_salud`
--

INSERT INTO `datos_salud` (`id`, `usuario_id`, `tratamiento_medico`, `alergia_medicamentos`, `alergia_especificar`, `enfermedad_diagnosticada`, `enfermedad_especificar`, `peso`, `estatura`, `observaciones_fisicas`, `medicamentos_permanentes`, `informacion_salud_adicional`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(3, 1, 'si', 'no', '', 'no', '', 65.0, 181.0, '', '', '', '2025-11-18 14:05:34', '2025-11-20 14:55:10'),
(20, 3, 'no', 'no', '', 'no', '', 25.0, 120.0, 'todo', 'todo', 'todo', '2025-12-24 14:28:08', '2025-12-24 14:46:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_vivienda`
--

CREATE TABLE `datos_vivienda` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `numero_personas` int(11) NOT NULL,
  `tipo_vivienda` enum('propia','familiar','arriendo','comodato') NOT NULL,
  `servicio_energia` enum('si','no') NOT NULL,
  `servicio_agua` enum('si','no') NOT NULL,
  `servicio_alcantarillado` enum('si','no') NOT NULL,
  `servicio_gas` enum('si','no') NOT NULL,
  `servicio_telefono` enum('si','no') NOT NULL,
  `servicio_internet` enum('si','no') NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `datos_vivienda`
--

INSERT INTO `datos_vivienda` (`id`, `usuario_id`, `numero_personas`, `tipo_vivienda`, `servicio_energia`, `servicio_agua`, `servicio_alcantarillado`, `servicio_gas`, `servicio_telefono`, `servicio_internet`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(5, 1, 2, 'familiar', 'no', 'si', 'si', 'si', 'si', 'si', '2025-11-18 13:54:35', '2025-11-20 14:46:04'),
(13, 3, 5, 'propia', 'si', 'si', 'si', 'si', 'si', 'si', '2025-12-24 14:26:54', '2025-12-24 14:26:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos_estudiante`
--

CREATE TABLE `documentos_estudiante` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo_documento` varchar(50) NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `ruta_archivo` varchar(500) NOT NULL,
  `fecha_subida` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(20) DEFAULT 'pendiente' COMMENT 'pendiente, aprobado, rechazado',
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `documentos_estudiante`
--

INSERT INTO `documentos_estudiante` (`id`, `usuario_id`, `tipo_documento`, `nombre_archivo`, `ruta_archivo`, `fecha_subida`, `estado`, `observaciones`) VALUES
(1, 1, 'registro_civil', 'formatoImpresionAUT (24).pdf', 'assets/uploads/estudiantes/1/registro_civil_1765554630_693c39c61a42f.pdf', '2025-12-12 15:50:30', 'aprobado', ''),
(2, 3, 'hoja_matricula_firmada', 'Hoja_Matricula_jennifer_2026-01-08.pdf', 'assets/uploads/estudiantes/3/hoja_matricula_firmada_1767887525_695fd2a5ec437.pdf', '2026-01-08 15:52:05', 'pendiente', NULL),
(3, 1, 'hoja_matricula_firmada', 'Modelo Plan de Formación.pdf', 'assets/uploads/estudiantes/1/hoja_matricula_firmada_1767888747_695fd76b785d5.pdf', '2026-01-08 16:12:27', 'pendiente', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes`
--

CREATE TABLE `estudiantes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `grado_matricular` varchar(50) DEFAULT NULL,
  `grado_actual` varchar(50) DEFAULT NULL,
  `sede` varchar(50) DEFAULT 'ÚNICA',
  `tipo_estudiante` enum('Nuevo','Antiguo') NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `lugar_nacimiento` varchar(100) NOT NULL,
  `edad` int(11) NOT NULL,
  `tipo_sangre` varchar(5) NOT NULL,
  `numero_documento` varchar(20) NOT NULL,
  `lugar_expedicion` varchar(100) NOT NULL,
  `tipo_documento` enum('TI','RC','CC') NOT NULL,
  `genero` enum('Masculino','Femenino','Otro') NOT NULL,
  `celular` varchar(15) NOT NULL,
  `telefono_residencia` varchar(15) NOT NULL,
  `correo_institucional` varchar(100) DEFAULT NULL,
  `fecha_matricula` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estudiantes`
--

INSERT INTO `estudiantes` (`id`, `usuario_id`, `nombres`, `apellidos`, `grado_matricular`, `grado_actual`, `sede`, `tipo_estudiante`, `fecha_nacimiento`, `lugar_nacimiento`, `edad`, `tipo_sangre`, `numero_documento`, `lugar_expedicion`, `tipo_documento`, `genero`, `celular`, `telefono_residencia`, `correo_institucional`, `fecha_matricula`, `created_at`, `updated_at`) VALUES
(1, 1, 'jostein', 'gonzalez', 'Por asignar', 'Por completar', 'ÚNICA', 'Antiguo', '2010-12-10', 'soacha', 15, 'O+', '1001264862', 'soacha', 'TI', 'Masculino', '3012564785', '2547263', '', '2025-10-28', '2025-10-28 17:13:44', '2025-12-20 00:34:44'),
(21, 3, 'jennifer', 'niño', 'Por asignar', 'Por completar', 'ÚNICA', 'Nuevo', '2018-12-21', 'Bogota', 7, 'O+', '987654321', 'Bogota', 'TI', 'Femenino', '3026589633', '', '', '2025-12-24', '2025-12-24 14:05:01', '2025-12-24 14:37:16'),
(24, 5, 'walter', 'flores', 'Por asignar', 'Por completar', 'ÚNICA', 'Antiguo', '2010-10-02', 'soacha', 15, 'A-', '1234567899', 'soacha', 'TI', 'Masculino', '3012564785', '', '', '2026-01-13', '2026-01-13 15:01:30', '2026-01-13 15:01:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes_info_general`
--

CREATE TABLE `estudiantes_info_general` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `barrio` varchar(100) NOT NULL,
  `madre_cabeza_familia` enum('si','no') NOT NULL,
  `estrato` int(11) NOT NULL,
  `municipio` varchar(100) NOT NULL,
  `sisben` varchar(20) NOT NULL,
  `eps` varchar(100) NOT NULL,
  `etnia` varchar(50) NOT NULL,
  `desplazado` enum('si','no') NOT NULL,
  `discapacidad_diagnostico` enum('si','no') NOT NULL,
  `tipo_discapacidad` varchar(50) DEFAULT NULL,
  `certificado_discapacidad` varchar(255) DEFAULT NULL,
  `numero_hermanos` enum('0','1','2','3','4','5','6','7') NOT NULL,
  `lugar_entre_hermanos` enum('no aplica','1','2','3','4','5','6','7') NOT NULL,
  `hermanos_en_colegio` enum('si','no') NOT NULL,
  `lateralidad` enum('zurdo','diestro','ambidiestro') NOT NULL,
  `historial_2025_ano` varchar(10) DEFAULT NULL,
  `historial_2025_colegio` varchar(255) DEFAULT NULL,
  `historial_2025_ciudad` varchar(100) DEFAULT NULL,
  `historial_2025_grado` varchar(50) DEFAULT NULL,
  `historial_2024_ano` varchar(10) DEFAULT NULL,
  `historial_2024_colegio` varchar(255) DEFAULT NULL,
  `historial_2024_ciudad` varchar(100) DEFAULT NULL,
  `historial_2024_grado` varchar(50) DEFAULT NULL,
  `historial_2023_ano` varchar(10) DEFAULT NULL,
  `historial_2023_colegio` varchar(255) DEFAULT NULL,
  `historial_2023_ciudad` varchar(100) DEFAULT NULL,
  `historial_2023_grado` varchar(50) DEFAULT NULL,
  `historial_2022_ano` varchar(10) DEFAULT NULL,
  `historial_2022_colegio` varchar(255) DEFAULT NULL,
  `historial_2022_ciudad` varchar(100) DEFAULT NULL,
  `historial_2022_grado` varchar(50) DEFAULT NULL,
  `historial_2021_ano` varchar(10) DEFAULT NULL,
  `historial_2021_colegio` varchar(255) DEFAULT NULL,
  `historial_2021_ciudad` varchar(100) DEFAULT NULL,
  `historial_2021_grado` varchar(50) DEFAULT NULL,
  `historial_2020_ano` varchar(10) DEFAULT NULL,
  `historial_2020_colegio` varchar(255) DEFAULT NULL,
  `historial_2020_ciudad` varchar(100) DEFAULT NULL,
  `historial_2020_grado` varchar(50) DEFAULT NULL,
  `historial_2019_ano` varchar(10) DEFAULT NULL,
  `historial_2019_colegio` varchar(255) DEFAULT NULL,
  `historial_2019_ciudad` varchar(100) DEFAULT NULL,
  `historial_2019_grado` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estudiantes_info_general`
--

INSERT INTO `estudiantes_info_general` (`id`, `usuario_id`, `direccion`, `barrio`, `madre_cabeza_familia`, `estrato`, `municipio`, `sisben`, `eps`, `etnia`, `desplazado`, `discapacidad_diagnostico`, `tipo_discapacidad`, `certificado_discapacidad`, `numero_hermanos`, `lugar_entre_hermanos`, `hermanos_en_colegio`, `lateralidad`, `historial_2025_ano`, `historial_2025_colegio`, `historial_2025_ciudad`, `historial_2025_grado`, `historial_2024_ano`, `historial_2024_colegio`, `historial_2024_ciudad`, `historial_2024_grado`, `historial_2023_ano`, `historial_2023_colegio`, `historial_2023_ciudad`, `historial_2023_grado`, `historial_2022_ano`, `historial_2022_colegio`, `historial_2022_ciudad`, `historial_2022_grado`, `historial_2021_ano`, `historial_2021_colegio`, `historial_2021_ciudad`, `historial_2021_grado`, `historial_2020_ano`, `historial_2020_colegio`, `historial_2020_ciudad`, `historial_2020_grado`, `historial_2019_ano`, `historial_2019_colegio`, `historial_2019_ciudad`, `historial_2019_grado`) VALUES
(12, 1, 'carrera 38 # 34', 'ciudad verde', 'no', 2, 'soacha', 'no aplica', 'famisanar', 'NINGUNA', 'no', 'no', '', '', '1', 'no aplica', 'si', 'diestro', '2025', 'maria currea', 'bogota', '11', '2024', 'maria currea', 'bogota', '10', '2023', 'maria currea', 'bogota', '9', '2022', 'maria currea', 'bogota', '8', '2021', 'benedicto', 'soacha', '7', '2020', 'benedicto ', 'socha ', '6', '2019', 'benedicto', 'soacha', '5'),
(22, 3, 'TR 25 A sur # 48 32', 'Madelena', 'no', 6, 'CUNDINAMARCA', 'no aplica', 'Falk', 'NINGUNA', 'no', 'no', '', '', '6', '4', 'si', 'diestro', '2025', 'manuela beltran', 'Bogota', '5', '2024', 'manuela beltran', 'Bogota', '4', '2023', 'manuela beltran', 'Bogota', '3', '2022', 'manuela beltran', 'Bogota', '2', '2021', 'manuela beltran', 'Bogota', '1', '2020', 'manuela beltran', 'Bogota', 'prescolar', '2019', 'manuela beltran', 'Bogota', 'jardin');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tokens_recuperacion`
--

CREATE TABLE `tokens_recuperacion` (
  `id` int(11) NOT NULL,
  `token` varchar(100) DEFAULT NULL,
  `correo` varchar(255) DEFAULT NULL,
  `expiracion` datetime DEFAULT NULL,
  `usado` tinyint(1) DEFAULT 0,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `documento` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','usuario') DEFAULT 'usuario',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `documento`, `password`, `rol`, `fecha_creacion`) VALUES
(1, '123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2025-10-28 16:35:57'),
(3, '987654321', '$2y$10$gQab9uZjzJPhhZ6LKerKfuI0BksshFdEy4GVrIFkJWZBW3FGQVOJG', 'usuario', '2025-12-24 14:02:45'),
(5, '1234567899', '$2y$10$m9/Sv/D4JKPVa.jem.0TnOyxnkGJUi9nkAlYBlm/7bLzY3ZoSZrhy', 'usuario', '2026-01-13 15:00:18');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `datos_acudiente`
--
ALTER TABLE `datos_acudiente`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_usuario` (`usuario_id`);

--
-- Indices de la tabla `datos_madre`
--
ALTER TABLE `datos_madre`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_madre_usuario` (`usuario_id`);

--
-- Indices de la tabla `datos_padre`
--
ALTER TABLE `datos_padre`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_usuario` (`usuario_id`);

--
-- Indices de la tabla `datos_salud`
--
ALTER TABLE `datos_salud`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_usuario` (`usuario_id`);

--
-- Indices de la tabla `datos_vivienda`
--
ALTER TABLE `datos_vivienda`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_usuario` (`usuario_id`);

--
-- Indices de la tabla `documentos_estudiante`
--
ALTER TABLE `documentos_estudiante`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_usuario_tipo` (`usuario_id`,`tipo_documento`);

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_usuario` (`usuario_id`);

--
-- Indices de la tabla `estudiantes_info_general`
--
ALTER TABLE `estudiantes_info_general`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_usuario` (`usuario_id`);

--
-- Indices de la tabla `tokens_recuperacion`
--
ALTER TABLE `tokens_recuperacion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `documento` (`documento`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `datos_acudiente`
--
ALTER TABLE `datos_acudiente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `datos_madre`
--
ALTER TABLE `datos_madre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `datos_padre`
--
ALTER TABLE `datos_padre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `datos_salud`
--
ALTER TABLE `datos_salud`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `datos_vivienda`
--
ALTER TABLE `datos_vivienda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `documentos_estudiante`
--
ALTER TABLE `documentos_estudiante`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `estudiantes_info_general`
--
ALTER TABLE `estudiantes_info_general`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `tokens_recuperacion`
--
ALTER TABLE `tokens_recuperacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `datos_acudiente`
--
ALTER TABLE `datos_acudiente`
  ADD CONSTRAINT `datos_acudiente_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `datos_madre`
--
ALTER TABLE `datos_madre`
  ADD CONSTRAINT `datos_madre_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `datos_padre`
--
ALTER TABLE `datos_padre`
  ADD CONSTRAINT `datos_padre_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `datos_salud`
--
ALTER TABLE `datos_salud`
  ADD CONSTRAINT `datos_salud_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `datos_vivienda`
--
ALTER TABLE `datos_vivienda`
  ADD CONSTRAINT `datos_vivienda_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `documentos_estudiante`
--
ALTER TABLE `documentos_estudiante`
  ADD CONSTRAINT `documentos_estudiante_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD CONSTRAINT `estudiantes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `estudiantes_info_general`
--
ALTER TABLE `estudiantes_info_general`
  ADD CONSTRAINT `estudiantes_info_general_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
