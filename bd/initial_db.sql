-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-12-2025 a las 05:03:29
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
-- Base de datos: `occu`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_niveles_usuario`
--

CREATE TABLE `admin_niveles_usuario` (
  `id` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `estado` int(11) NOT NULL,
  `id_alta` int(11) NOT NULL,
  `fecha_alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `admin_niveles_usuario`
--

INSERT INTO `admin_niveles_usuario` (`id`, `nombre`, `estado`, `id_alta`, `fecha_alta`) VALUES
(1, 'Administrador', 0, 1, '2024-09-30 02:33:46'),
(2, 'Propietario', 0, 1, '2024-09-30 02:33:46'),
(3, 'Barista', 0, 1, '2024-10-12 06:55:24'),
(4, 'Cliente', 0, 1, '2024-10-12 06:55:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_niveles_usuario_modulos`
--

CREATE TABLE `admin_niveles_usuario_modulos` (
  `id` int(11) NOT NULL,
  `id_nivel` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `admin_niveles_usuario_modulos`
--

INSERT INTO `admin_niveles_usuario_modulos` (`id`, `id_nivel`, `id_modulo`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 1, 7),
(8, 1, 8),
(9, 2, 9),
(10, 2, 7),
(11, 2, 8),
(12, 1, 10),
(13, 3, 7),
(14, 3, 8),
(15, 4, 7),
(16, 4, 8),
(17, 1, 10),
(18, 1, 11),
(19, 1, 12),
(20, 2, 13),
(21, 2, 14),
(22, 2, 15),
(23, 2, 16),
(24, 1, 17),
(25, 1, 18);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_usuarios`
--

CREATE TABLE `admin_usuarios` (
  `id` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `apellido` text NOT NULL,
  `id_ciudad` int(11) NOT NULL,
  `correo_electronico` text NOT NULL,
  `contrasena` text NOT NULL,
  `nivel` text NOT NULL,
  `telefono` text NOT NULL,
  `imagen` text NOT NULL,
  `token_sesion` text NOT NULL,
  `pin` text NOT NULL,
  `verificado` text NOT NULL,
  `estado` int(11) NOT NULL,
  `id_alta` int(11) NOT NULL,
  `fecha_alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `admin_usuarios`
--

INSERT INTO `admin_usuarios` (`id`, `nombre`, `apellido`, `id_ciudad`, `correo_electronico`, `contrasena`, `nivel`, `telefono`, `imagen`, `token_sesion`, `pin`, `verificado`, `estado`, `id_alta`, `fecha_alta`) VALUES
(1, 'Omar', 'Rios', 1, 'admin@admin.com', '$2a$07$asxx54ahjppf45sd87a5auXBm1Vr2M1NV5t/zNQtGHGpS5fFirrbG', 'Administrador', '6865706609', 'views/assets/img/admin_usuarios/664_imagen_usuario_1.webp', '693672962fce7', '', 'Si', 0, 1, '2022-07-21 00:28:03'),
(4, 'Usuario', 'Prueba1', 1, 'user@user.com', '$2a$07$asxx54ahjppf45sd87a5augtYQ5l0YJxtJ.sls/VjJvJD4Oq/Jqk2', 'Propietario', '7412589636', 'views/assets/img/admin_usuarios/725_imagen_usuario_4.webp', '69323cf95ef52', '', 'Si', 0, 1, '2024-08-16 21:21:43'),
(5, 'Usuario', 'Prueba1', 1, 'user2@user.com', '$2a$07$asxx54ahjppf45sd87a5augtYQ5l0YJxtJ.sls/VjJvJD4Oq/Jqk2', 'Propietario', '7412589636', 'views/assets/img/admin_usuarios/166_imagen_usuario_5.webp', '6881bed36554f', '', 'Si', 0, 1, '2024-08-16 21:23:12'),
(6, 'barista', 'numero1', 0, 'barista@bar.com', '$2a$07$asxx54ahjppf45sd87a5auGZEtGHuyZwm.Ur.FJvWLCql3nmsMbXy', 'Barista', 'undefined', 'views/assets/img/usuario_default.png', '670aabb699594', '', 'Si', 0, 1, '2024-10-11 21:44:44'),
(7, 'Cliente', 'numero1', 0, 'cliente@cliente.com', '$2a$07$asxx54ahjppf45sd87a5augtYQ5l0YJxtJ.sls/VjJvJD4Oq/Jqk2', 'Cliente', '', 'views/assets/img/usuario_default.png', '69325087904ad', '', 'Si', 0, 0, '2024-10-11 21:58:25'),
(8, 'Usuario', 'numero1', 0, 'f', '$2a$07$asxx54ahjppf45sd87a5auGZEtGHuyZwm.Ur.FJvWLCql3nmsMbXy', 'Cliente', '', 'views/assets/img/usuario_default.png', '', '330885', 'Si', 0, 0, '2024-10-24 00:49:59'),
(9, 'Usuario', 'Prueba1', 0, '', '$2a$07$asxx54ahjppf45sd87a5auXBm1Vr2M1NV5t/zNQtGHGpS5fFirrbG', 'Barista', '', 'views/assets/img/usuario_default.png', '671c6c5c3d36d', '955416', '', 0, 0, '2024-10-24 00:50:43'),
(10, 'Jose', 'Rios', 0, 'sdf', '$2a$07$asxx54ahjppf45sd87a5auGZEtGHuyZwm.Ur.FJvWLCql3nmsMbXy', 'Cliente', '', 'views/assets/img/usuario_default.png', '', '905657', '', 0, 0, '2024-11-08 21:26:54'),
(13, 'Omar', 'Rios', 0, 'omarrios299@gmail.com', '$2a$07$asxx54ahjppf45sd87a5auGZEtGHuyZwm.Ur.FJvWLCql3nmsMbXy', 'Cliente', '', 'views/assets/img/usuario_default.png', '', '316229', '', 0, 0, '2025-04-30 22:20:13'),
(14, 'Omar', 'Rios', 1, 'a1173344@uabc.edu.mxd', '$2a$07$asxx54ahjppf45sd87a5auXBm1Vr2M1NV5t/zNQtGHGpS5fFirrbG', 'Propietario', '', 'views/assets/img/usuario_default.png', '681fb358cc126', '338946', 'Si', 0, 0, '2025-05-01 15:07:12'),
(15, 'Cecy', 'Rios', 1, '', '$2a$07$asxx54ahjppf45sd87a5auXBm1Vr2M1NV5t/zNQtGHGpS5fFirrbG', 'Cliente', '', 'views/assets/img/usuario_default.png', '682150ddd183a', '982651', 'Si', 0, 0, '2025-05-11 17:15:07'),
(16, 'Cecy', 'Rios', 1, 'a1173344@uabc.edu.mx', '$2a$07$asxx54ahjppf45sd87a5auXBm1Vr2M1NV5t/zNQtGHGpS5fFirrbG', 'Cliente', '', 'views/assets/img/usuario_default.png', '6833b62ccf674', '955729', 'Si', 0, 0, '2025-05-11 18:42:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cafeterias`
--

CREATE TABLE `cafeterias` (
  `id` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `imagen` text NOT NULL,
  `descripcion` text NOT NULL,
  `id_ciudad` int(11) NOT NULL,
  `direccion` text NOT NULL,
  `telefono` text NOT NULL,
  `correo_electronico` text NOT NULL,
  `latitud` text NOT NULL,
  `longitud` text NOT NULL,
  `horario_apertura` text DEFAULT NULL,
  `horario_cierre` text DEFAULT NULL,
  `horario_diferente` text NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `estado` int(11) NOT NULL,
  `id_alta` int(11) NOT NULL,
  `fecha_alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `cafeterias`
--

INSERT INTO `cafeterias` (`id`, `nombre`, `imagen`, `descripcion`, `id_ciudad`, `direccion`, `telefono`, `correo_electronico`, `latitud`, `longitud`, `horario_apertura`, `horario_cierre`, `horario_diferente`, `id_usuario`, `estado`, `id_alta`, `fecha_alta`) VALUES
(1, 'cafetaFCA', 'views/assets/img/cafeterias/905_imagen_cafeteria_1.webp', 'rf', 1, 'Av. Morelia 21', '', '', '', '', '18:44', '18:46', 'NO', 4, 0, 4, '2025-05-02 03:41:03'),
(2, 'cafetaFCA1', 'views/assets/img/cafeteria_default.png', '', 1, 'Av. Morelia 21', '', '', '', '', NULL, NULL, 'SI', 14, 0, 21, '2025-05-03 23:31:10'),
(3, 'cafetaFCA1215', 'views/assets/img/cafeteria_default.png', '', 1, 'Av. Morelia 21', '', '', '', '', '05:31', '19:31', 'NO', 14, 0, 21, '2025-05-03 23:32:06'),
(4, 'cafeta', 'views/assets/img/cafeteria_default.png', '', 1, 'Av. Morelia 21', '', '', '', '', '16:44', '16:46', 'NO', 21, 0, 21, '2025-05-04 01:40:21'),
(5, 'cafe chico', 'views/assets/img/cafeteria_default.png', '', 1, 'Av. Morelia 21', '', '', '', '', '', '', 'NO', 14, 2, 14, '2025-05-04 01:56:07'),
(6, 'cafe chico2', 'views/assets/img/cafeteria_default.png', '', 1, 'Av. Morelia 21', '', '', '', '', '', '', 'NO', 14, 2, 14, '2025-05-04 02:01:43'),
(7, 'cafetaFCA18', 'views/assets/img/cafeteria_default.png', '', 1, 'Av. Morelia 21', '', '', '', '', '', '', 'NO', 14, 2, 14, '2025-05-04 02:02:10'),
(8, 'Café punta del cielo', 'views/assets/img/cafeteria_default.png', '', 1, 'Av. Morelia 21', '', '', '', '', '', '', 'NO', 14, 2, 14, '2025-05-04 02:02:41'),
(9, 'CAfe 090', 'views/assets/img/cafeteria_default.png', 'cdcdcdcdc', 1, 'Av. Morelia 21', '6666666666', 'ca212feta@gmail.com', '', '', '08:07', '22:07', 'NO', 14, 2, 14, '2025-05-04 02:05:09'),
(10, 'CAfe 2', 'views/assets/img/cafeterias/431_imagen_cafeteria_10.webp', '', 1, '.', '', '', '', '', '06:45', '23:01', 'NO', 4, 0, 4, '2025-07-27 12:28:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cafeterias_comentarios`
--

CREATE TABLE `cafeterias_comentarios` (
  `id` int(11) NOT NULL,
  `comentario` text NOT NULL,
  `estrellas` text NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_cafeteria` int(11) NOT NULL,
  `estado` int(11) NOT NULL,
  `fecha_alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `cafeterias_comentarios`
--

INSERT INTO `cafeterias_comentarios` (`id`, `comentario`, `estrellas`, `id_usuario`, `id_cafeteria`, `estado`, `fecha_alta`) VALUES
(1, 'Buen sabor en el cafe', '', 16, 1, 0, '2025-05-17 13:01:42'),
(2, 'Comentario', '', 4, 1, 0, '2025-12-04 19:01:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cafeterias_imagenes`
--

CREATE TABLE `cafeterias_imagenes` (
  `id` int(11) NOT NULL,
  `id_cafeteria` int(11) NOT NULL,
  `imagen` text NOT NULL,
  `descripcion` text NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `cafeterias_imagenes`
--

INSERT INTO `cafeterias_imagenes` (`id`, `id_cafeteria`, `imagen`, `descripcion`, `estado`) VALUES
(1, 9, 'views/assets/img/cafeterias_imagenes/103_cafeteria_9_2.webp', '', 0),
(2, 1, 'views/assets/img/cafeterias_imagenes/159_cafeteria_1_2.webp', '', 0),
(3, 1, 'views/assets/img/cafeterias_imagenes/616_cafeteria_1_2.webp', '', 0),
(4, 1, 'views/assets/img/cafeterias_imagenes/255_cafeteria_1_2.webp', '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cafeterias_servicios`
--

CREATE TABLE `cafeterias_servicios` (
  `id` int(11) NOT NULL,
  `id_servicio` int(11) NOT NULL,
  `id_cafeteria` int(11) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `cafeterias_servicios`
--

INSERT INTO `cafeterias_servicios` (`id`, `id_servicio`, `id_cafeteria`, `estado`) VALUES
(1, 1, 1, 2),
(2, 2, 1, 2),
(3, 3, 1, 2),
(4, 4, 1, 2),
(5, 5, 1, 2),
(6, 6, 1, 2),
(7, 1, 1, 2),
(8, 2, 1, 2),
(9, 3, 1, 2),
(10, 4, 1, 2),
(11, 5, 1, 2),
(12, 1, 1, 0),
(13, 2, 1, 0),
(14, 3, 1, 0),
(15, 4, 1, 0),
(16, 5, 1, 0),
(17, 6, 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cafeteria_horarios`
--

CREATE TABLE `cafeteria_horarios` (
  `id` int(11) NOT NULL,
  `id_cafeteria` int(11) NOT NULL,
  `dia` text NOT NULL,
  `hora_apertura` text DEFAULT NULL,
  `hora_cierre` text DEFAULT NULL,
  `cerrado` text DEFAULT NULL,
  `fin_de_semana` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `cafeteria_horarios`
--

INSERT INTO `cafeteria_horarios` (`id`, `id_cafeteria`, `dia`, `hora_apertura`, `hora_cierre`, `cerrado`, `fin_de_semana`) VALUES
(43, 2, 'Lunes', '07:00', '20:00', 'NO', NULL),
(44, 2, 'Martes', '07:00', '19:00', 'NO', NULL),
(45, 2, 'Miércoles', '07:00', '19:00', 'NO', NULL),
(46, 2, 'Jueves', '07:00', '19:00', 'NO', NULL),
(47, 2, 'Viernes', '07:00', '21:00', 'NO', NULL),
(48, 2, 'Sábado', NULL, NULL, 'SI', NULL),
(49, 2, 'Domingo', NULL, NULL, 'SI', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciudades`
--

CREATE TABLE `ciudades` (
  `id` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `id_entidad_federativa` int(11) NOT NULL,
  `coordenadas` text NOT NULL,
  `estado` int(11) NOT NULL,
  `id_alta` int(11) NOT NULL,
  `fecha_alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `ciudades`
--

INSERT INTO `ciudades` (`id`, `nombre`, `id_entidad_federativa`, `coordenadas`, `estado`, `id_alta`, `fecha_alta`) VALUES
(1, 'Mexicali', 3, '[{\"lat\":32.526260901600644,\"lng\":-115.5140749636051},{\"lat\":32.54362747638307,\"lng\":-115.3218142214176},{\"lat\":32.59223601581936,\"lng\":-115.24079005149572},{\"lat\":32.67897142443699,\"lng\":-115.31838099387853},{\"lat\":32.65729545625588,\"lng\":-115.65603892234533}]', 0, 1, '2024-08-12 02:29:58'),
(2, 'San Luis Rio Colorado', 27, '[{\"lat\":32.4946090377715,\"lng\":-114.82095950479955},{\"lat\":32.427982640405325,\"lng\":-114.85529178019017},{\"lat\":32.36246698265542,\"lng\":-114.7522949540183},{\"lat\":32.45116262909821,\"lng\":-114.68363040323705}]', 0, 1, '2024-09-01 23:40:55'),
(3, 'Tijuana', 3, '[{\"lat\":32.536620338912634,\"lng\":-117.12210809628586},{\"lat\":32.56961015303892,\"lng\":-116.75955926816086},{\"lat\":32.446270455928776,\"lng\":-116.7403331939421},{\"lat\":32.377288743066465,\"lng\":-117.02323114316086},{\"lat\":32.45727946001347,\"lng\":-117.1028820220671}]', 0, 1, '2024-09-01 23:44:35'),
(4, 'San Felipe', 3, '[{\"lat\":31.039516470919132,\"lng\":-114.82546183210351},{\"lat\":31.043193437584716,\"lng\":-114.86271235090234},{\"lat\":31.027896322884168,\"lng\":-114.86545893293359},{\"lat\":31.008771474325695,\"lng\":-114.85052439313867},{\"lat\":30.992438758445452,\"lng\":-114.8438295994375},{\"lat\":30.987729708279232,\"lng\":-114.8328432713125},{\"lat\":30.995087497005734,\"lng\":-114.8276934300039},{\"lat\":31.013111680487757,\"lng\":-114.8354181919668}]', 0, 1, '2024-09-02 23:05:51'),
(5, 'San Luis Rio Colorado', 27, '[{\"lat\":32.485720960329566,\"lng\":-114.79102826094538},{\"lat\":32.4752947046791,\"lng\":-114.80682110762507},{\"lat\":32.4732672369393,\"lng\":-114.85488629317194},{\"lat\":32.44487789497818,\"lng\":-114.84630322432429},{\"lat\":32.38923203330696,\"lng\":-114.79343152022273},{\"lat\":32.390101763789076,\"lng\":-114.71378064131648},{\"lat\":32.458204618681336,\"lng\":-114.69524121260554}]', 2, 1, '2024-09-03 17:05:47'),
(6, 'Chihuahua', 7, '[{\"lat\":29.404598958279564,\"lng\":-107.14847005021986},{\"lat\":29.2789019394246,\"lng\":-107.19104207170423},{\"lat\":29.254942085841197,\"lng\":-106.83535969865736}]', 0, 1, '2024-09-28 13:20:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entidades_federativas`
--

CREATE TABLE `entidades_federativas` (
  `id` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `id_pais` int(11) NOT NULL,
  `estado` int(11) NOT NULL,
  `id_alta` int(11) NOT NULL,
  `fecha_alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `entidades_federativas`
--

INSERT INTO `entidades_federativas` (`id`, `nombre`, `id_pais`, `estado`, `id_alta`, `fecha_alta`) VALUES
(1, 'Estado de Prueba', 1, 0, 1, '2024-08-12 02:32:06'),
(2, 'Aguascalientes', 1, 0, 1, '2024-08-12 00:00:00'),
(3, 'Baja California', 1, 0, 1, '2024-08-12 00:00:00'),
(4, 'Baja California Sur', 1, 0, 1, '2024-08-12 00:00:00'),
(5, 'Campeche', 1, 0, 1, '2024-08-12 00:00:00'),
(6, 'Chiapas', 1, 0, 1, '2024-08-12 00:00:00'),
(7, 'Chihuahua', 1, 0, 1, '2024-08-12 00:00:00'),
(8, 'Coahuila', 1, 0, 1, '2024-08-12 00:00:00'),
(9, 'Colima', 1, 0, 1, '2024-08-12 00:00:00'),
(10, 'Durango', 1, 0, 1, '2024-08-12 00:00:00'),
(11, 'Guanajuato', 1, 0, 1, '2024-08-12 00:00:00'),
(12, 'Guerrero', 1, 0, 1, '2024-08-12 00:00:00'),
(13, 'Hidalgo', 1, 0, 1, '2024-08-12 00:00:00'),
(14, 'Jalisco', 1, 0, 1, '2024-08-12 00:00:00'),
(15, 'Mexico', 1, 0, 1, '2024-08-12 00:00:00'),
(16, 'Mexico City', 1, 0, 1, '2024-08-12 00:00:00'),
(17, 'Michoacán', 1, 0, 1, '2024-08-12 00:00:00'),
(18, 'Morelos', 1, 0, 1, '2024-08-12 00:00:00'),
(19, 'Nayarit', 1, 0, 1, '2024-08-12 00:00:00'),
(20, 'Nuevo León', 1, 0, 1, '2024-08-12 00:00:00'),
(21, 'Oaxaca', 1, 0, 1, '2024-08-12 00:00:00'),
(22, 'Puebla', 1, 0, 1, '2024-08-12 00:00:00'),
(23, 'Querétaro', 1, 0, 1, '2024-08-12 00:00:00'),
(24, 'Quintana Roo', 1, 0, 1, '2024-08-12 00:00:00'),
(25, 'San Luis Potosí', 1, 0, 1, '2024-08-12 00:00:00'),
(26, 'Sinaloa', 1, 0, 1, '2024-08-12 00:00:00'),
(27, 'Sonora', 1, 0, 1, '2024-08-12 00:00:00'),
(28, 'Tabasco', 1, 0, 1, '2024-08-12 00:00:00'),
(29, 'Tamaulipas', 1, 0, 1, '2024-08-12 00:00:00'),
(30, 'Tlaxcala', 1, 0, 1, '2024-08-12 00:00:00'),
(31, 'Veracruz', 1, 0, 1, '2024-08-12 00:00:00'),
(32, 'Yucatán', 1, 0, 1, '2024-08-12 00:00:00'),
(33, 'Zacatecas', 1, 0, 1, '2024-08-12 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu_categorias`
--

CREATE TABLE `menu_categorias` (
  `id` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `imagen` text NOT NULL,
  `estado` int(11) NOT NULL,
  `es_bebida` text NOT NULL,
  `id_alta` int(11) NOT NULL,
  `fecha_alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `menu_categorias`
--

INSERT INTO `menu_categorias` (`id`, `nombre`, `imagen`, `estado`, `es_bebida`, `id_alta`, `fecha_alta`) VALUES
(1, 'Bebidas', 'views/assets/img/menu_categorias/1_imagen.webp', 0, 'Si', 1, '2025-11-26 22:45:01'),
(2, 'Comida', 'views/assets/img/menu_categorias/2_imagen.webp', 0, 'No', 1, '2025-11-26 22:45:02'),
(3, 'Panadería & Postres', 'views/assets/img/menu_categorias/3_imagen.webp', 0, 'No', 1, '2025-11-26 22:45:03'),
(4, 'Snacks', 'views/assets/img/menu_categorias/4_imagen.webp', 0, 'No', 1, '2025-11-26 22:45:04'),
(5, 'Tienda / Café en Grano / Merch', 'views/assets/img/menu_categorias/5_imagen.webp', 0, 'No', 1, '2025-11-26 22:45:05'),
(6, 'Temporada / Promociones', 'views/assets/img/menu_categorias/6_imagen.webp', 0, 'No', 1, '2025-11-26 22:45:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu_ingredientes`
--

CREATE TABLE `menu_ingredientes` (
  `id` int(11) NOT NULL,
  `id_ingrediente_categoria` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `registro_occu` int(11) NOT NULL COMMENT '	1=occu, 2=propietario',
  `estado` int(11) NOT NULL,
  `id_alta` int(11) NOT NULL COMMENT 'id_propietario',
  `fecha_alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `menu_ingredientes`
--

INSERT INTO `menu_ingredientes` (`id`, `id_ingrediente_categoria`, `nombre`, `registro_occu`, `estado`, `id_alta`, `fecha_alta`) VALUES
(1, 1, 'Espresso', 1, 0, 1, '2025-11-26 23:13:58'),
(2, 1, 'Americano', 1, 0, 1, '2025-11-26 23:13:58'),
(3, 1, 'Cold Brew', 1, 0, 1, '2025-11-26 23:13:58'),
(4, 1, 'Café de Olla', 1, 0, 1, '2025-11-26 23:13:58'),
(5, 1, 'Descafeinado', 1, 0, 1, '2025-11-26 23:13:58'),
(6, 2, 'Leche Entera', 1, 0, 1, '2025-11-26 23:13:58'),
(7, 2, 'Leche Light', 1, 0, 1, '2025-11-26 23:13:58'),
(8, 2, 'Leche Deslactosada', 1, 0, 1, '2025-11-26 23:13:58'),
(9, 2, 'Leche de Almendra', 1, 0, 1, '2025-11-26 23:13:58'),
(10, 2, 'Leche de Coco', 1, 0, 1, '2025-11-26 23:13:58'),
(11, 2, 'Leche de Soya', 1, 0, 1, '2025-11-26 23:13:58'),
(12, 2, 'Leche de Avena', 1, 0, 1, '2025-11-26 23:13:58'),
(13, 3, 'Azúcar Blanca', 1, 0, 1, '2025-11-26 23:13:58'),
(14, 3, 'Azúcar Morena', 1, 0, 1, '2025-11-26 23:13:58'),
(15, 3, 'Miel', 1, 0, 1, '2025-11-26 23:13:58'),
(16, 3, 'Jarabe Simple', 1, 0, 1, '2025-11-26 23:13:58'),
(17, 3, 'Stevia', 1, 0, 1, '2025-11-26 23:13:58'),
(18, 4, 'Vainilla', 1, 0, 1, '2025-11-26 23:13:58'),
(19, 4, 'Avellana', 1, 0, 1, '2025-11-26 23:13:58'),
(20, 4, 'Caramelo', 1, 0, 1, '2025-11-26 23:13:58'),
(21, 4, 'Chocolate Blanco', 1, 0, 1, '2025-11-26 23:13:58'),
(22, 4, 'Menta', 1, 0, 1, '2025-11-26 23:13:58'),
(23, 4, 'Chai', 1, 0, 1, '2025-11-26 23:13:58'),
(24, 4, 'Matcha', 1, 0, 1, '2025-11-26 23:13:58'),
(25, 4, 'Almendra Dulce', 1, 0, 1, '2025-11-26 23:13:58'),
(26, 5, 'Crema Batida', 1, 0, 1, '2025-11-26 23:13:58'),
(27, 5, 'Canela', 1, 0, 1, '2025-11-26 23:13:58'),
(28, 5, 'Cocoa en Polvo', 1, 0, 1, '2025-11-26 23:13:58'),
(29, 5, 'Chispas de Chocolate', 1, 0, 1, '2025-11-26 23:13:58'),
(30, 5, 'Caramelo Líquido', 1, 0, 1, '2025-11-26 23:13:58'),
(31, 5, 'Chocolate Líquido', 1, 0, 1, '2025-11-26 23:13:58'),
(32, 9, 'Caliente', 1, 2, 1, '2025-11-26 23:13:58'),
(33, 9, 'Frío', 1, 2, 1, '2025-11-26 23:13:58'),
(34, 9, 'Frappe', 1, 2, 1, '2025-11-26 23:13:58'),
(35, 7, 'Té Negro', 1, 0, 1, '2025-11-26 23:13:58'),
(36, 7, 'Té Verde', 1, 0, 1, '2025-11-26 23:13:58'),
(37, 7, 'Té Blanco', 1, 0, 1, '2025-11-26 23:13:58'),
(38, 7, 'Té Chai', 1, 0, 1, '2025-11-26 23:13:58'),
(39, 7, 'Infusión Frutal', 1, 0, 1, '2025-11-26 23:13:58'),
(40, 8, 'Chocolate Oscuro', 1, 0, 1, '2025-11-26 23:13:58'),
(41, 8, 'Chocolate Blanco', 1, 0, 1, '2025-11-26 23:13:58'),
(42, 8, 'Cocoa Natural', 1, 0, 1, '2025-11-26 23:13:58'),
(43, 8, 'Cocoa Especial Premium', 1, 0, 1, '2025-11-26 23:13:58'),
(44, 6, 'Base de Café', 1, 2, 1, '2025-11-26 23:13:58'),
(45, 6, 'Base de Vainilla', 1, 2, 1, '2025-11-26 23:13:58'),
(46, 6, 'Base de Chocolate', 1, 2, 1, '2025-11-26 23:13:58'),
(47, 6, 'Base Natural Smoothie', 1, 2, 1, '2025-11-26 23:13:58'),
(48, 10, 'Baguette', 1, 0, 1, '2025-11-26 23:13:58'),
(49, 10, 'Pan Integral', 1, 0, 1, '2025-11-26 23:13:58'),
(50, 10, 'Bolillo', 1, 0, 1, '2025-11-26 23:13:58'),
(51, 10, 'Croissant', 1, 0, 1, '2025-11-26 23:13:58'),
(52, 11, 'Pollo', 1, 0, 1, '2025-11-26 23:13:58'),
(53, 11, 'Pavo', 1, 0, 1, '2025-11-26 23:13:58'),
(54, 11, 'Jamón', 1, 0, 1, '2025-11-26 23:13:58'),
(55, 11, 'Atún', 1, 0, 1, '2025-11-26 23:13:58'),
(56, 11, 'Huevo', 1, 0, 1, '2025-11-26 23:13:58'),
(57, 12, 'Lechuga', 1, 0, 1, '2025-11-26 23:13:58'),
(58, 12, 'Tomate', 1, 0, 1, '2025-11-26 23:13:58'),
(59, 12, 'Cebolla', 1, 0, 1, '2025-11-26 23:13:58'),
(60, 12, 'Espinaca', 1, 0, 1, '2025-11-26 23:13:58'),
(61, 12, 'Pepino', 1, 0, 1, '2025-11-26 23:13:58'),
(62, 13, 'Mayonesa', 1, 0, 1, '2025-11-26 23:13:58'),
(63, 13, 'Aderezo Ranch', 1, 0, 1, '2025-11-26 23:13:58'),
(64, 13, 'Aderezo Chipotle', 1, 0, 1, '2025-11-26 23:13:58'),
(65, 13, 'Mostaza', 1, 0, 1, '2025-11-26 23:13:58'),
(66, 14, 'Queso Cheddar', 1, 0, 1, '2025-11-26 23:13:58'),
(67, 14, 'Queso Panela', 1, 0, 1, '2025-11-26 23:13:58'),
(68, 14, 'Queso Manchego', 1, 0, 1, '2025-11-26 23:13:58'),
(69, 14, 'Queso Mozzarella', 1, 0, 1, '2025-11-26 23:13:58'),
(70, 15, 'Pesto', 1, 0, 1, '2025-11-26 23:13:58'),
(71, 15, 'Aceitunas', 1, 0, 1, '2025-11-26 23:13:58'),
(72, 15, 'Pepperoni', 1, 0, 1, '2025-11-26 23:13:58'),
(73, 15, 'Aguacate', 1, 0, 1, '2025-11-26 23:13:58'),
(74, 16, 'Masa de Hojaldre', 1, 0, 1, '2025-11-26 23:13:58'),
(75, 16, 'Masa de Pan Dulce', 1, 0, 1, '2025-11-26 23:13:58'),
(76, 16, 'Masa de Galleta', 1, 0, 1, '2025-11-26 23:13:58'),
(77, 17, 'Chocolate', 1, 0, 1, '2025-11-26 23:13:58'),
(78, 17, 'Queso Crema', 1, 0, 1, '2025-11-26 23:13:58'),
(79, 17, 'Cajeta', 1, 0, 1, '2025-11-26 23:13:58'),
(80, 17, 'Fresa', 1, 0, 1, '2025-11-26 23:13:58'),
(81, 18, 'Azúcar Glass', 1, 0, 1, '2025-11-26 23:13:58'),
(82, 18, 'Glaseado', 1, 0, 1, '2025-11-26 23:13:58'),
(83, 18, 'Chispas de Colores', 1, 0, 1, '2025-11-26 23:13:58'),
(84, 19, 'Proteína Vegana', 1, 0, 1, '2025-11-26 23:13:58'),
(85, 19, 'Crema Vegana', 1, 0, 1, '2025-11-26 23:13:58'),
(86, 19, 'Mayonesa Vegana', 1, 0, 1, '2025-11-26 23:13:58'),
(87, 20, 'Jarabe Sin Azúcar', 1, 0, 1, '2025-11-26 23:13:58'),
(88, 20, 'Chocolate Sin Azúcar', 1, 0, 1, '2025-11-26 23:13:58'),
(89, 20, 'Vainilla Sin Azúcar', 1, 0, 1, '2025-11-26 23:13:58'),
(90, 21, 'Queso Sin Lactosa', 1, 0, 1, '2025-11-26 23:13:58'),
(91, 21, 'Crema Sin Lactosa', 1, 0, 1, '2025-11-26 23:13:58'),
(92, 22, 'Pan Gluten Free', 1, 0, 1, '2025-11-26 23:13:58'),
(93, 22, 'Galleta Gluten Free', 1, 0, 1, '2025-11-26 23:13:58'),
(94, 23, 'Piloncillo', 1, 0, 1, '2025-11-26 23:13:58'),
(95, 23, 'Canela Mexicana', 1, 0, 1, '2025-11-26 23:13:58'),
(96, 23, 'Atole Base', 1, 0, 1, '2025-11-26 23:13:58'),
(97, 23, 'Vainilla de Papantla', 1, 0, 1, '2025-11-26 23:13:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu_ingredientes_categorias`
--

CREATE TABLE `menu_ingredientes_categorias` (
  `id` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `para_bebidas` text NOT NULL,
  `para_alimentos` text NOT NULL,
  `estado` int(11) NOT NULL,
  `id_alta` int(11) NOT NULL,
  `fecha_alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `menu_ingredientes_categorias`
--

INSERT INTO `menu_ingredientes_categorias` (`id`, `nombre`, `para_bebidas`, `para_alimentos`, `estado`, `id_alta`, `fecha_alta`) VALUES
(1, 'Tipo de Grano / Café', 'Si', '', 0, 1, '2025-11-26 23:11:23'),
(2, 'Leche', 'Si', '', 0, 1, '2025-11-26 23:11:23'),
(3, 'Endulzantes', 'Si', '', 0, 1, '2025-11-26 23:11:23'),
(4, 'Saborizantes /Jarabes', 'Si', '', 0, 1, '2025-11-26 23:11:23'),
(5, 'Toppings', 'Si', '', 0, 1, '2025-11-26 23:11:23'),
(6, 'Bases para Frappé / Smoothie', '', '', 2, 1, '2025-11-26 23:11:23'),
(7, 'Tipo de Té', 'Si', '', 0, 1, '2025-11-26 23:11:23'),
(8, 'Tipo de Chocolate / Cocoa', 'Si', '', 0, 1, '2025-11-26 23:11:23'),
(10, 'Pan / Base del Platillo', '', 'Si', 0, 1, '2025-11-26 23:11:23'),
(11, 'Proteínas', '', 'Si', 0, 1, '2025-11-26 23:11:23'),
(12, 'Vegetales', '', 'Si', 0, 1, '2025-11-26 23:11:23'),
(13, 'Salsas y Aderezos', '', 'Si', 0, 1, '2025-11-26 23:11:23'),
(14, 'Quesos', '', 'Si', 0, 1, '2025-11-26 23:11:23'),
(15, 'Extras Gourmet', '', 'Si', 0, 1, '2025-11-26 23:11:23'),
(16, 'Tipo de Masa / Base', '', 'Si', 0, 1, '2025-11-26 23:11:23'),
(17, 'Rellenos', '', 'Si', 0, 1, '2025-11-26 23:11:23'),
(18, 'Coberturas de Repostería', '', 'Si', 0, 1, '2025-11-26 23:11:23'),
(19, 'Opciones Veganas', '', 'Si', 0, 1, '2025-11-26 23:11:23'),
(20, 'Opciones Sin Azúcar', '', 'Si', 0, 1, '2025-11-26 23:11:23'),
(21, 'Opciones Sin Lactosa', '', 'Si', 0, 1, '2025-11-26 23:11:23'),
(22, 'Opciones Gluten Free', '', 'Si', 0, 1, '2025-11-26 23:11:23'),
(23, 'Especialidades Regionales', '', 'Si', 0, 1, '2025-11-26 23:11:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu_productos`
--

CREATE TABLE `menu_productos` (
  `id` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `id_subcategoria` int(11) NOT NULL,
  `imagen` text NOT NULL,
  `registro_occu` int(11) NOT NULL COMMENT '1=occu, 2=propietario',
  `estado` int(11) NOT NULL,
  `id_alta` int(11) NOT NULL COMMENT 'id_propietario',
  `fecha_alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `menu_productos`
--

INSERT INTO `menu_productos` (`id`, `nombre`, `id_subcategoria`, `imagen`, `registro_occu`, `estado`, `id_alta`, `fecha_alta`) VALUES
(1, 'Espresso', 1, 'views/assets/img/menu_productos/865_imagen_producto_1.webp', 1, 0, 1, '2025-11-26 23:20:01'),
(2, 'Americano', 1, 'views/assets/img/menu_productos/927_imagen_producto_2.webp', 1, 0, 1, '2025-11-26 23:20:02'),
(3, 'Latte', 1, 'views/assets/img/menu_productos/744_imagen_producto_3.webp', 1, 0, 1, '2025-11-26 23:20:03'),
(4, 'Cappuccino', 1, 'views/assets/img/menu_productos/910_imagen_producto_4.webp', 1, 0, 1, '2025-11-26 23:20:04'),
(5, 'Mocha', 1, 'views/assets/img/menu_productos/486_imagen_producto_5.webp', 1, 0, 1, '2025-11-26 23:20:05'),
(6, 'Matcha Latte Caliente', 1, 'views/assets/img/menu_productos/835_imagen_producto_6.webp', 1, 0, 1, '2025-11-26 23:20:06'),
(7, 'Chai Latte Caliente', 1, 'views/assets/img/menu_productos/594_imagen_producto_7.webp', 1, 0, 1, '2025-11-26 23:20:07'),
(8, 'Chocolate Caliente', 1, 'views/assets/img/menu_productos/327_imagen_producto_8.webp', 1, 0, 1, '2025-11-26 23:20:08'),
(9, 'Iced Latte', 2, 'views/assets/img/menu_productos/762_imagen_producto_9.webp', 1, 0, 1, '2025-11-26 23:20:09'),
(10, 'Iced Mocha', 2, 'views/assets/img/menu_productos/10_imagen.webp', 1, 0, 1, '2025-11-26 23:20:10'),
(11, 'Iced Chai Latte', 2, 'views/assets/img/menu_productos/11_imagen.webp', 1, 0, 1, '2025-11-26 23:20:11'),
(12, 'Iced Matcha Latte', 2, 'views/assets/img/menu_productos/12_imagen.webp', 1, 0, 1, '2025-11-26 23:20:12'),
(13, 'Té Negro Frío', 2, 'views/assets/img/menu_productos/13_imagen.webp', 1, 0, 1, '2025-11-26 23:20:13'),
(14, 'Frappe de Café', 3, 'views/assets/img/menu_productos/14_imagen.webp', 1, 0, 1, '2025-11-26 23:20:14'),
(15, 'Frappe de Oreo', 3, 'views/assets/img/menu_productos/15_imagen.webp', 1, 0, 1, '2025-11-26 23:20:15'),
(16, 'Frappe de Matcha', 3, 'views/assets/img/menu_productos/16_imagen.webp', 1, 0, 1, '2025-11-26 23:20:16'),
(17, 'Cold Brew', 4, 'views/assets/img/menu_productos/17_imagen.webp', 1, 0, 1, '2025-11-26 23:20:17'),
(18, 'Nitro Cold Brew', 4, 'views/assets/img/menu_productos/18_imagen.webp', 1, 0, 1, '2025-11-26 23:20:18'),
(19, 'Smoothie de Fresa', 5, 'views/assets/img/menu_productos/19_imagen.webp', 1, 0, 1, '2025-11-26 23:20:19'),
(20, 'Jugó Verde Detox', 5, 'views/assets/img/menu_productos/20_imagen.webp', 1, 0, 1, '2025-11-26 23:20:20'),
(21, 'Limonada Natural', 6, 'views/assets/img/menu_productos/21_imagen.webp', 1, 0, 1, '2025-11-26 23:20:21'),
(22, 'Naranjada', 6, 'views/assets/img/menu_productos/22_imagen.webp', 1, 0, 1, '2025-11-26 23:20:22'),
(23, 'Huevos al Gusto', 7, 'views/assets/img/menu_productos/23_imagen.webp', 1, 0, 1, '2025-11-26 23:20:23'),
(24, 'Hot Cakes', 7, 'views/assets/img/menu_productos/24_imagen.webp', 1, 0, 1, '2025-11-26 23:20:24'),
(25, 'Sandwich de Pavo', 8, 'views/assets/img/menu_productos/25_imagen.webp', 1, 0, 1, '2025-11-26 23:20:25'),
(26, 'Sandwich de Jamón y Queso', 8, 'views/assets/img/menu_productos/26_imagen.webp', 1, 0, 1, '2025-11-26 23:20:26'),
(27, 'Panini Caprese', 9, 'views/assets/img/menu_productos/27_imagen.webp', 1, 0, 1, '2025-11-26 23:20:27'),
(28, 'Wrap de Pollo', 10, 'views/assets/img/menu_productos/28_imagen.webp', 1, 0, 1, '2025-11-26 23:20:28'),
(29, 'Ensalada César', 11, 'views/assets/img/menu_productos/29_imagen.webp', 1, 0, 1, '2025-11-26 23:20:29'),
(30, 'Pastel de Chocolate', 16, 'views/assets/img/menu_productos/30_imagen.webp', 1, 0, 1, '2025-11-26 23:20:30'),
(31, 'Cheesecake', 16, 'views/assets/img/menu_productos/31_imagen.webp', 1, 0, 1, '2025-11-26 23:20:31'),
(32, 'Galleta Chocochip', 18, 'views/assets/img/menu_productos/32_imagen.webp', 1, 0, 1, '2025-11-26 23:20:32'),
(33, 'Brownie Tradicional', 20, 'views/assets/img/menu_productos/33_imagen.webp', 1, 0, 1, '2025-11-26 23:20:33'),
(34, 'Papas Fritas', 24, 'views/assets/img/menu_productos/34_imagen.webp', 1, 0, 1, '2025-11-26 23:20:34'),
(35, 'Mix de Nueces', 23, 'views/assets/img/menu_productos/35_imagen.webp', 1, 0, 1, '2025-11-26 23:20:35'),
(36, 'Extra Queso', 25, 'views/assets/img/menu_productos/36_imagen.webp', 1, 0, 1, '2025-11-26 23:20:36'),
(37, 'Café en Grano House Blend', 26, 'views/assets/img/menu_productos/37_imagen.webp', 1, 0, 1, '2025-11-26 23:20:37'),
(38, 'Té Verde en Hoja', 27, 'views/assets/img/menu_productos/38_imagen.webp', 1, 0, 1, '2025-11-26 23:20:38'),
(39, 'Pumpkin Spice Latte', 29, 'views/assets/img/menu_productos/39_imagen.webp', 1, 0, 1, '2025-11-26 23:20:39'),
(40, 'Pan de Muerto', 30, 'views/assets/img/menu_productos/40_imagen.webp', 1, 0, 1, '2025-11-26 23:20:40'),
(41, 'Bowl de Pollo y Quinoa', 12, 'views/assets/img/menu_productos/41_imagen.webp', 1, 0, 1, '2025-11-26 22:56:49'),
(42, 'Bowl Verde Detox', 12, 'views/assets/img/menu_productos/42_imagen.webp', 1, 0, 1, '2025-11-26 22:56:49'),
(43, 'Bowl Mediterráneo', 12, 'views/assets/img/menu_productos/43_imagen.webp', 1, 0, 1, '2025-11-26 22:56:49'),
(44, 'Hamburguesa de Pavo Ligera', 13, 'views/assets/img/menu_productos/44_imagen.webp', 1, 0, 1, '2025-11-26 22:56:49'),
(45, 'Hamburguesa de Pollo a la Parrilla', 13, 'views/assets/img/menu_productos/45_imagen.webp', 1, 0, 1, '2025-11-26 22:56:49'),
(46, 'Hamburguesa Veggie', 13, 'views/assets/img/menu_productos/46_imagen.webp', 1, 0, 1, '2025-11-26 22:56:49'),
(47, 'Tiramisú Clásico', 17, 'views/assets/img/menu_productos/47_imagen.webp', 1, 0, 1, '2025-11-26 22:56:49'),
(48, 'Milhojas', 17, 'views/assets/img/menu_productos/48_imagen.webp', 1, 0, 1, '2025-11-26 22:56:49'),
(49, 'Flan Napolitano', 17, 'views/assets/img/menu_productos/49_imagen.webp', 1, 0, 1, '2025-11-26 22:56:49'),
(50, 'Helado de Vainilla', 21, 'views/assets/img/menu_productos/50_imagen.webp', 1, 0, 1, '2025-11-26 22:56:49'),
(51, 'Helado de Chocolate', 21, 'views/assets/img/menu_productos/51_imagen.webp', 1, 0, 1, '2025-11-26 22:56:49'),
(52, 'Sundae de Caramelo', 21, 'views/assets/img/menu_productos/52_imagen.webp', 1, 0, 1, '2025-11-26 22:56:49'),
(53, 'Termo de Acero Inoxidable', 28, 'views/assets/img/menu_productos/53_imagen.webp', 1, 0, 1, '2025-11-26 22:56:49'),
(54, 'Vaso Reutilizable de Plástico', 28, 'views/assets/img/menu_productos/54_imagen.webp', 1, 0, 1, '2025-11-26 22:56:49'),
(55, 'Taza Cerámica Edición Especial', 28, 'views/assets/img/menu_productos/55_imagen.webp', 1, 0, 1, '2025-11-26 22:56:49'),
(56, 'Combo Café + Pan Dulce', 31, 'views/assets/img/menu_productos/56_imagen.webp', 1, 0, 1, '2025-11-26 22:56:49'),
(57, 'Combo Desayuno', 31, 'views/assets/img/menu_productos/57_imagen.webp', 1, 0, 1, '2025-11-26 22:56:49'),
(58, 'Descuento 2x1 en Frappé', 31, 'views/assets/img/menu_productos/58_imagen.webp', 1, 0, 1, '2025-11-26 22:56:49'),
(59, 'Croissant Salado', 15, 'views/assets/img/menu_productos/59_imagen.webp', 1, 0, 1, '2025-11-26 23:01:24'),
(60, 'Baguette Individual', 15, 'views/assets/img/menu_productos/60_imagen.webp', 1, 0, 1, '2025-11-26 23:01:24'),
(61, 'Pan Ciabatta', 15, 'views/assets/img/menu_productos/61_imagen.webp', 1, 0, 1, '2025-11-26 23:01:24'),
(62, 'Pretzels Salados', 22, 'views/assets/img/menu_productos/62_imagen.webp', 1, 0, 1, '2025-11-26 23:01:24'),
(63, 'Palomitas Mantequilla', 22, 'views/assets/img/menu_productos/63_imagen.webp', 1, 0, 1, '2025-11-26 23:01:24'),
(64, 'Barra de Granola', 22, 'views/assets/img/menu_productos/64_imagen.webp', 1, 0, 1, '2025-11-26 23:01:24'),
(65, 'Muffin de Blueberry', 19, 'views/assets/img/menu_productos/65_imagen.webp', 1, 0, 1, '2025-11-26 23:01:24'),
(66, 'Muffin de Chocolate', 19, 'views/assets/img/menu_productos/66_imagen.webp', 1, 0, 1, '2025-11-26 23:01:24'),
(67, 'Cupcake de Vainilla', 19, 'views/assets/img/menu_productos/67_imagen.webp', 1, 0, 1, '2025-11-26 23:01:24'),
(68, 'Concha Tradicional', 14, 'views/assets/img/menu_productos/68_imagen.webp', 1, 0, 1, '2025-11-26 23:01:46'),
(69, 'Rol de Canela', 14, 'views/assets/img/menu_productos/69_imagen.webp', 1, 0, 1, '2025-11-26 23:01:46'),
(70, 'Cuernito Dulce', 14, 'views/assets/img/menu_productos/70_imagen.webp', 1, 0, 1, '2025-11-26 23:01:46'),
(71, 'Cafe lco', 2, 'views/assets/img/cafeteria_default.png', 2, 0, 4, '2025-12-07 13:09:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu_productos_bases`
--

CREATE TABLE `menu_productos_bases` (
  `id` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_ingrediente_categoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `menu_productos_bases`
--

INSERT INTO `menu_productos_bases` (`id`, `id_producto`, `id_ingrediente_categoria`) VALUES
(1, 3, 2),
(2, 3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu_productos_tamanos`
--

CREATE TABLE `menu_productos_tamanos` (
  `id` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `unidad_medida` text NOT NULL,
  `medida` text NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `menu_productos_tamanos`
--

INSERT INTO `menu_productos_tamanos` (`id`, `nombre`, `unidad_medida`, `medida`, `estado`) VALUES
(1, '4', 'Oz', '4', 0),
(2, '6', 'Oz', '6', 0),
(3, '12', 'Oz', '12', 0),
(4, '16', 'Oz', '16', 0),
(5, '20', 'Oz', '20', 0),
(6, '24', 'Oz', '24', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu_subcategorias`
--

CREATE TABLE `menu_subcategorias` (
  `id` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `estado` int(11) NOT NULL,
  `registro_occu` int(11) NOT NULL COMMENT '1=occu, 2=propietarios',
  `id_propietario` int(11) NOT NULL,
  `fecha_alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `menu_subcategorias`
--

INSERT INTO `menu_subcategorias` (`id`, `nombre`, `id_categoria`, `estado`, `registro_occu`, `id_propietario`, `fecha_alta`) VALUES
(1, 'Bebidas Calientes', 1, 0, 1, 0, '2025-11-26 23:00:01'),
(2, 'Bebidas Frías', 1, 0, 1, 0, '2025-11-26 23:00:02'),
(3, 'Frappés / Frozen', 1, 0, 1, 0, '2025-11-26 23:00:03'),
(4, 'Cold Brew', 1, 0, 1, 0, '2025-11-26 23:00:04'),
(5, 'Jugos & Smoothies', 1, 0, 1, 0, '2025-11-26 23:00:05'),
(6, 'Aguas Frescas & Limonadas', 1, 0, 1, 0, '2025-11-26 23:00:06'),
(7, 'Desayunos', 2, 0, 1, 0, '2025-11-26 23:00:07'),
(8, 'Sandwiches', 2, 0, 1, 0, '2025-11-26 23:00:08'),
(9, 'Paninis', 2, 0, 1, 0, '2025-11-26 23:00:09'),
(10, 'Wraps & Bagels', 2, 0, 1, 0, '2025-11-26 23:00:10'),
(11, 'Ensaladas', 2, 0, 1, 0, '2025-11-26 23:00:11'),
(12, 'Bowls / Comida Ligera', 2, 0, 1, 0, '2025-11-26 23:00:12'),
(13, 'Hamburguesas Ligeras', 2, 0, 1, 0, '2025-11-26 23:00:13'),
(14, 'Pan Dulce', 3, 0, 1, 0, '2025-11-26 23:00:14'),
(15, 'Pan Salado', 3, 0, 1, 0, '2025-11-26 23:00:15'),
(16, 'Pasteles', 3, 0, 1, 0, '2025-11-26 23:00:16'),
(17, 'Repostería Fina', 3, 0, 1, 0, '2025-11-26 23:00:17'),
(18, 'Galletas', 3, 0, 1, 0, '2025-11-26 23:00:18'),
(19, 'Muffins & Cupcakes', 3, 0, 1, 0, '2025-11-26 23:00:19'),
(20, 'Brownies', 3, 0, 1, 0, '2025-11-26 23:00:20'),
(21, 'Helados / Postres Fríos', 3, 0, 1, 0, '2025-11-26 23:00:21'),
(22, 'Botanas', 4, 0, 1, 0, '2025-11-26 23:00:22'),
(23, 'Fruta & Mix de Nueces', 4, 0, 1, 0, '2025-11-26 23:00:23'),
(24, 'Papas & Nachos', 4, 0, 1, 0, '2025-11-26 23:00:24'),
(25, 'Extras & Toppings', 4, 0, 1, 0, '2025-11-26 23:00:25'),
(26, 'Café en Grano', 5, 0, 1, 0, '2025-11-26 23:00:26'),
(27, 'Té en Hoja', 5, 0, 1, 0, '2025-11-26 23:00:27'),
(28, 'Merchandising / Accesorios', 5, 0, 1, 0, '2025-11-26 23:00:28'),
(29, 'Bebidas de Temporada', 6, 0, 1, 0, '2025-11-26 23:00:29'),
(30, 'Comida de Temporada', 6, 0, 1, 0, '2025-11-26 23:00:30'),
(31, 'Promociones Especiales', 6, 0, 1, 0, '2025-11-26 23:00:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagina_inicial`
--

CREATE TABLE `pagina_inicial` (
  `id` int(11) NOT NULL,
  `area` text NOT NULL,
  `titulo` text NOT NULL,
  `imagen` text NOT NULL,
  `descripcion` text NOT NULL,
  `enlace` text NOT NULL,
  `nombre_enlace` text NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `pagina_inicial`
--

INSERT INTO `pagina_inicial` (`id`, `area`, `titulo`, `imagen`, `descripcion`, `enlace`, `nombre_enlace`, `estado`) VALUES
(1, 'carousel', 'Una frase de ejemplo.', 'views/assets/img/imagen1.jpg', 'Si deseas encontrar el mejor lugar para tomar un rico café.', '', 'registrame', 0),
(2, 'card', 'Espresso', 'views/assets/img/cafe.jpg', 'Encuentra las mejores cafeterías de la ciudad', 'cafeterias_mapa', 'registrame', 0),
(3, 'carousel', 'Titulo de prueba 2', 'views/assets/img/admin_usuarios/881_imagen_usuario_3.webp', 'asdad', 'cafeterias_lista', 'cafeterias', 2),
(4, 'card', 'Titulo de prueba card', 'views/assets/img/admin_usuarios/107_imagen_usuario_4.webp', 'descri', 'cafeterias_lista', 'card', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paises`
--

CREATE TABLE `paises` (
  `id` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `estado` int(11) NOT NULL,
  `id_alta` int(11) NOT NULL,
  `fecha_alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `paises`
--

INSERT INTO `paises` (`id`, `nombre`, `estado`, `id_alta`, `fecha_alta`) VALUES
(1, 'México', 0, 1, '2024-08-12 02:32:33'),
(2, 'Chile', 0, 1, '2024-08-30 23:26:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_areas`
--

CREATE TABLE `permisos_areas` (
  `id` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `icono` text NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos_areas`
--

INSERT INTO `permisos_areas` (`id`, `nombre`, `icono`, `estado`) VALUES
(0, 'Sin área', '', 0),
(1, 'Administración', '', 0),
(2, 'Menú', '', 0),
(3, 'Cafeterías', '', 0),
(5, 'Para propietarios', '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_modulos`
--

CREATE TABLE `permisos_modulos` (
  `id` int(11) NOT NULL,
  `id_area` int(11) NOT NULL,
  `id_subarea` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `ruta` text NOT NULL,
  `icono` text NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos_modulos`
--

INSERT INTO `permisos_modulos` (`id`, `id_area`, `id_subarea`, `nombre`, `ruta`, `icono`, `estado`) VALUES
(1, 1, 0, 'Usuarios', 'admin_usuarios', '', 0),
(2, 1, 0, 'Cafeterías', 'cafeterias', '', 0),
(3, 1, 0, 'Servicios de cafetería', 'cafeterias_servicios', '', 0),
(4, 1, 0, 'Países', 'admin_paises', '', 0),
(5, 2, 0, 'Menú', 'menu', '', 0),
(6, 2, 0, 'Productos', 'menu_productos', '', 0),
(7, 3, 0, 'Ubicaciones', 'cafeterias_mapa', '', 0),
(8, 3, 0, 'Lista de cafeterías', 'cafeterias_lista', '', 0),
(9, 0, 0, 'Cafeterías', 'cafeterias', '', 0),
(10, 1, 0, 'Editar página inicial', 'admin_pagina_inicial', '', 0),
(11, 2, 0, 'Subcategorías de productos', 'menu_subcategorias', '', 0),
(12, 2, 0, 'Cetegorías de productos', 'menu_categorias', '', 0),
(13, 2, 0, 'Mi menú', 'propietarios_menu', '', 0),
(14, 2, 0, 'Categorías', 'propietarios_menu_categorias', '', 0),
(15, 2, 0, 'Productos OCCU', 'propietarios_menu_productos', '', 0),
(16, 2, 0, 'QR de menú', 'propietarios_menu_qr', '', 2),
(17, 2, 0, 'Ingredientes', 'menu_ingredientes', '', 0),
(18, 2, 0, 'Categorías de ingredientes', 'menu_ingredientes_categorias', '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propietarios_ingredientes`
--

CREATE TABLE `propietarios_ingredientes` (
  `id` int(11) NOT NULL,
  `id_propietario_producto` int(11) NOT NULL,
  `id_ingrediente` int(11) NOT NULL,
  `costo_extra` text NOT NULL DEFAULT 'No',
  `cantidad_gratis` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propietarios_menu_subcategorias`
--

CREATE TABLE `propietarios_menu_subcategorias` (
  `id` int(11) NOT NULL,
  `id_subcategoria` int(11) NOT NULL,
  `id_propietario` int(11) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `propietarios_menu_subcategorias`
--

INSERT INTO `propietarios_menu_subcategorias` (`id`, `id_subcategoria`, `id_propietario`, `estado`) VALUES
(1, 2, 14, 1),
(2, 4, 14, 1),
(3, 10, 14, 1),
(4, 5, 14, 1),
(5, 2, 4, 1),
(6, 4, 4, 1),
(7, 10, 4, 1),
(8, 8, 4, 1),
(9, 5, 4, 1),
(10, 6, 4, 1),
(11, 7, 4, 1),
(12, 9, 4, 0),
(13, 17, 4, 1),
(14, 3, 4, 1),
(15, 19, 4, 1),
(19, 2, 5, 1),
(20, 4, 5, 1),
(21, 10, 5, 1),
(22, 22, 4, 1),
(23, 24, 4, 1),
(24, 16, 4, 1),
(25, 18, 4, 1),
(26, 20, 4, 1),
(27, 29, 4, 1),
(28, 1, 4, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propietarios_productos`
--

CREATE TABLE `propietarios_productos` (
  `id` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_propietario` int(11) NOT NULL,
  `id_cafeteria` int(11) NOT NULL,
  `precio_base` text NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propietarios_productos_tamanos`
--

CREATE TABLE `propietarios_productos_tamanos` (
  `id` int(11) NOT NULL,
  `id_propietario_producto` int(11) NOT NULL,
  `id_tamano` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `imagen` text NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 0,
  `id_alta` int(11) NOT NULL,
  `fecha_alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id`, `nombre`, `imagen`, `estado`, `id_alta`, `fecha_alta`) VALUES
(1, 'Pago con tarjeta', 'views/assets/img/servicios/289_imagen_servicio_1.webp', 0, 1, '2024-09-19 08:48:28'),
(2, 'Pet friendly', 'views/assets/img/servicios/677_imagen_servicio_2.webp', 0, 1, '2024-09-19 08:48:54'),
(3, 'Desayunos', 'views/assets/img/servicios/474_imagen_servicio_3.webp', 0, 1, '2024-09-19 08:50:44'),
(4, 'Drive thru', 'views/assets/img/servicios/123_imagen_servicio_4.webp', 0, 1, '2024-09-22 01:43:32'),
(5, 'A domicilio', 'views/assets/img/servicios/848_imagen_servicio_5.webp', 0, 1, '2024-09-27 08:33:09'),
(6, 'extra rapido', 'views/assets/img/servicios/260_imagen_servicio_6.webp', 0, 1, '2024-09-28 22:17:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `id_cafeteria` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `monto_total` text NOT NULL,
  `estado_pedido` int(11) NOT NULL,
  `estado` int(11) NOT NULL,
  `id_alta` int(11) NOT NULL,
  `fecha_alta` datetime NOT NULL,
  `id_aceptado` int(11) NOT NULL,
  `fecha_aceptado` datetime NOT NULL,
  `id_rechazo` int(11) NOT NULL,
  `fecha_rechazo` datetime NOT NULL,
  `motivo_rechazo` text NOT NULL,
  `id_entregado` int(11) NOT NULL,
  `fecha_entragado` datetime NOT NULL,
  `id_cancelado` int(11) NOT NULL,
  `fecha_cancelado` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `id_cafeteria`, `id_cliente`, `monto_total`, `estado_pedido`, `estado`, `id_alta`, `fecha_alta`, `id_aceptado`, `fecha_aceptado`, `id_rechazo`, `fecha_rechazo`, `motivo_rechazo`, `id_entregado`, `fecha_entragado`, `id_cancelado`, `fecha_cancelado`) VALUES
(1, 1, 7, '130', 4, 0, 7, '2025-11-25 21:17:25', 4, '2025-11-25 22:07:25', 0, '0000-00-00 00:00:00', '', 4, '2025-11-25 22:08:01', 0, '0000-00-00 00:00:00'),
(2, 1, 7, '80', 4, 0, 7, '2025-11-26 17:56:34', 4, '2025-11-26 17:57:20', 0, '0000-00-00 00:00:00', '', 4, '2025-11-26 17:57:51', 0, '0000-00-00 00:00:00'),
(3, 1, 7, '215', 4, 0, 7, '2025-12-03 19:43:26', 4, '2025-12-03 19:44:20', 0, '0000-00-00 00:00:00', '', 4, '2025-12-03 19:44:34', 0, '0000-00-00 00:00:00'),
(4, 1, 7, '110', 3, 0, 7, '2025-12-03 19:55:07', 0, '0000-00-00 00:00:00', 4, '2025-12-03 20:22:06', 'sjofgso\'', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas_carrito`
--

CREATE TABLE `ventas_carrito` (
  `id` int(11) NOT NULL,
  `id_cafeteria` int(11) NOT NULL,
  `estado` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `ventas_carrito`
--

INSERT INTO `ventas_carrito` (`id`, `id_cafeteria`, `estado`, `id_usuario`, `fecha_alta`) VALUES
(1, 1, 2, 7, '2025-11-23 00:08:11'),
(2, 2, 2, 7, '2025-11-23 21:11:53'),
(3, 1, 2, 7, '2025-11-23 21:15:08'),
(4, 2, 2, 7, '2025-11-23 21:16:56'),
(5, 1, 2, 7, '2025-11-23 21:17:11'),
(6, 2, 2, 7, '2025-11-23 21:25:11'),
(7, 1, 2, 7, '2025-11-23 21:25:53'),
(8, 2, 2, 7, '2025-11-23 21:39:17'),
(9, 1, 2, 7, '2025-11-24 17:44:41'),
(10, 2, 2, 7, '2025-11-24 18:01:41'),
(11, 1, 2, 7, '2025-11-24 18:59:21'),
(12, 1, 2, 7, '2025-11-25 22:46:08'),
(13, 1, 2, 7, '2025-11-28 21:08:00'),
(14, 1, 2, 7, '2025-12-03 19:54:16'),
(15, 1, 0, 1, '2025-12-07 22:40:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas_carrito_items`
--

CREATE TABLE `ventas_carrito_items` (
  `id` int(11) NOT NULL,
  `id_carrito` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_tamano` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `estado` int(11) NOT NULL,
  `fecha_alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `ventas_carrito_items`
--

INSERT INTO `ventas_carrito_items` (`id`, `id_carrito`, `id_producto`, `id_tamano`, `cantidad`, `estado`, `fecha_alta`) VALUES
(1, 1, 1, 5, 1, 2, '2025-11-23 00:08:11'),
(2, 1, 5, 3, 1, 2, '2025-11-23 16:33:11'),
(3, 1, 9, 3, 1, 2, '2025-11-23 16:49:55'),
(4, 1, 1, 1, 1, 2, '2025-11-23 20:59:38'),
(5, 1, 1, 1, 1, 2, '2025-11-23 21:00:22'),
(6, 1, 1, 5, 1, 2, '2025-11-23 21:10:30'),
(7, 1, 1, 5, 1, 0, '2025-11-23 21:11:53'),
(8, 2, 1, 5, 1, 2, '2025-11-23 21:13:13'),
(9, 3, 1, 1, 1, 2, '2025-11-23 21:15:08'),
(10, 4, 1, 1, 1, 2, '2025-11-23 21:16:56'),
(11, 5, 1, 3, 1, 2, '2025-11-23 21:17:11'),
(12, 6, 1, 5, 1, 2, '2025-11-23 21:25:11'),
(13, 7, 1, 3, 1, 2, '2025-11-23 21:25:53'),
(14, 8, 1, 5, 1, 2, '2025-11-23 21:39:17'),
(15, 9, 1, 5, 3, 2, '2025-11-24 17:44:41'),
(16, 10, 15, 0, 1, 2, '2025-11-24 18:01:41'),
(17, 11, 1, 5, 1, 2, '2025-11-24 18:59:21'),
(18, 11, 4, 0, 1, 2, '2025-11-24 18:59:26'),
(19, 11, 1, 3, 2, 2, '2025-11-24 19:40:04'),
(20, 11, 5, 5, 1, 2, '2025-11-24 20:24:36'),
(21, 11, 1, 3, 1, 2, '2025-11-24 20:24:54'),
(22, 11, 9, 5, 1, 2, '2025-11-24 21:00:13'),
(23, 11, 19, 0, 1, 2, '2025-11-24 21:00:46'),
(24, 11, 1, 1, 1, 2, '2025-11-24 21:29:06'),
(25, 11, 1, 1, 1, 2, '2025-11-24 21:30:33'),
(26, 11, 1, 1, 1, 2, '2025-11-24 21:32:09'),
(27, 11, 1, 1, 1, 2, '2025-11-24 21:35:36'),
(28, 11, 1, 1, 1, 2, '2025-11-24 21:53:41'),
(29, 11, 1, 1, 1, 2, '2025-11-24 21:55:32'),
(30, 11, 1, 1, 1, 2, '2025-11-24 21:58:18'),
(31, 11, 1, 1, 3, 2, '2025-11-24 22:39:42'),
(32, 11, 1, 1, 1, 2, '2025-11-24 22:45:54'),
(33, 12, 1, 3, 1, 2, '2025-11-25 22:46:08'),
(34, 12, 1, 3, 1, 2, '2025-11-26 17:56:27'),
(35, 13, 9, 1, 1, 2, '2025-11-28 21:08:00'),
(36, 13, 9, 3, 1, 2, '2025-12-02 19:18:50'),
(37, 13, 9, 3, 3, 2, '2025-12-03 19:42:45'),
(38, 13, 1, 3, 1, 2, '2025-12-03 19:43:01'),
(39, 14, 9, 3, 2, 2, '2025-12-03 19:54:16'),
(40, 15, 9, 0, 1, 0, '2025-12-07 22:40:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas_carrito_items_ingredientes`
--

CREATE TABLE `ventas_carrito_items_ingredientes` (
  `id` int(11) NOT NULL,
  `id_carrito_item` int(11) NOT NULL,
  `id_ingrediente` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `estado` int(11) NOT NULL,
  `fecha_alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `ventas_carrito_items_ingredientes`
--

INSERT INTO `ventas_carrito_items_ingredientes` (`id`, `id_carrito_item`, `id_ingrediente`, `cantidad`, `estado`, `fecha_alta`) VALUES
(1, 1, 2, 1, 2, '2025-11-23 00:08:11'),
(2, 1, 5, 1, 2, '2025-11-23 00:08:11'),
(3, 1, 1, 1, 2, '2025-11-23 00:08:11'),
(4, 1, 16, 1, 2, '2025-11-23 00:08:11'),
(5, 2, 1, 1, 2, '2025-11-23 16:33:11'),
(6, 2, 4, 1, 2, '2025-11-23 16:33:11'),
(7, 2, 6, 1, 2, '2025-11-23 16:33:11'),
(8, 2, 11, 1, 2, '2025-11-23 16:33:11'),
(9, 6, 3, 1, 2, '2025-11-23 21:10:30'),
(10, 6, 5, 1, 2, '2025-11-23 21:10:30'),
(11, 6, 8, 1, 2, '2025-11-23 21:10:30'),
(12, 6, 10, 1, 2, '2025-11-23 21:10:30'),
(13, 6, 1, 1, 2, '2025-11-23 21:10:30'),
(14, 6, 2, 1, 2, '2025-11-23 21:10:30'),
(15, 6, 9, 1, 2, '2025-11-23 21:10:30'),
(16, 15, 3, 1, 2, '2025-11-24 17:44:41'),
(17, 15, 1, 1, 2, '2025-11-24 17:44:41'),
(18, 17, 4, 1, 2, '2025-11-24 18:59:21'),
(19, 17, 5, 1, 2, '2025-11-24 18:59:21'),
(20, 17, 7, 1, 2, '2025-11-24 18:59:21'),
(21, 17, 1, 1, 2, '2025-11-24 18:59:21'),
(22, 17, 2, 1, 2, '2025-11-24 18:59:21'),
(23, 17, 9, 1, 2, '2025-11-24 18:59:21'),
(24, 17, 12, 1, 2, '2025-11-24 18:59:21'),
(25, 19, 4, 1, 2, '2025-11-24 19:40:04'),
(26, 19, 6, 1, 2, '2025-11-24 19:40:04'),
(27, 19, 1, 1, 2, '2025-11-24 19:40:04'),
(28, 20, 1, 1, 2, '2025-11-24 20:24:36'),
(29, 20, 3, 1, 2, '2025-11-24 20:24:36'),
(30, 21, 3, 1, 2, '2025-11-24 20:24:54'),
(31, 21, 5, 1, 2, '2025-11-24 20:24:54'),
(32, 21, 8, 1, 2, '2025-11-24 20:24:54'),
(33, 21, 10, 1, 2, '2025-11-24 20:24:54'),
(34, 21, 1, 1, 2, '2025-11-24 20:24:54'),
(35, 23, 14, 1, 2, '2025-11-24 21:00:46'),
(36, 23, 1, 1, 2, '2025-11-24 21:00:46'),
(37, 23, 2, 1, 2, '2025-11-24 21:00:46'),
(38, 24, 4, 1, 2, '2025-11-24 21:29:06'),
(39, 24, 5, 1, 2, '2025-11-24 21:29:06'),
(40, 24, 1, 1, 2, '2025-11-24 21:29:06'),
(41, 25, 4, 1, 2, '2025-11-24 21:30:33'),
(42, 25, 6, 1, 2, '2025-11-24 21:30:33'),
(43, 25, 1, 1, 2, '2025-11-24 21:30:33'),
(44, 26, 4, 1, 2, '2025-11-24 21:32:09'),
(45, 26, 6, 1, 2, '2025-11-24 21:32:09'),
(46, 26, 1, 1, 2, '2025-11-24 21:32:09'),
(47, 27, 4, 1, 2, '2025-11-24 21:35:36'),
(48, 27, 6, 1, 2, '2025-11-24 21:35:36'),
(49, 27, 1, 1, 2, '2025-11-24 21:35:36'),
(50, 28, 4, 1, 2, '2025-11-24 21:53:41'),
(51, 28, 6, 1, 2, '2025-11-24 21:53:41'),
(52, 28, 1, 1, 2, '2025-11-24 21:53:41'),
(53, 29, 4, 1, 2, '2025-11-24 21:55:32'),
(54, 29, 6, 1, 2, '2025-11-24 21:55:32'),
(55, 29, 1, 1, 2, '2025-11-24 21:55:32'),
(56, 30, 4, 1, 2, '2025-11-24 21:58:18'),
(57, 30, 6, 1, 2, '2025-11-24 21:58:18'),
(58, 30, 1, 1, 2, '2025-11-24 21:58:18'),
(59, 31, 4, 1, 2, '2025-11-24 22:39:42'),
(60, 31, 6, 1, 2, '2025-11-24 22:39:42'),
(61, 31, 1, 1, 2, '2025-11-24 22:39:42'),
(62, 32, 4, 1, 2, '2025-11-24 22:45:54'),
(63, 32, 5, 1, 2, '2025-11-24 22:45:54'),
(64, 32, 1, 1, 2, '2025-11-24 22:45:54'),
(65, 33, 1, 1, 2, '2025-11-25 22:46:08'),
(66, 33, 3, 1, 2, '2025-11-25 22:46:08'),
(67, 33, 5, 1, 2, '2025-11-25 22:46:08'),
(68, 33, 6, 1, 2, '2025-11-25 22:46:08'),
(69, 34, 1, 1, 2, '2025-11-26 17:56:27'),
(70, 34, 4, 1, 2, '2025-11-26 17:56:27'),
(71, 34, 5, 3, 2, '2025-11-26 17:56:27'),
(72, 34, 6, 1, 2, '2025-11-26 17:56:27'),
(73, 34, 9, 1, 2, '2025-11-26 17:56:27'),
(74, 34, 12, 1, 2, '2025-11-26 17:56:27'),
(75, 34, 11, 1, 2, '2025-11-26 17:56:27'),
(76, 35, 1, 1, 2, '2025-11-28 21:08:00'),
(77, 35, 9, 1, 2, '2025-11-28 21:08:00'),
(78, 36, 1, 1, 2, '2025-12-02 19:18:50'),
(79, 36, 8, 1, 2, '2025-12-02 19:18:50'),
(80, 36, 23, 1, 2, '2025-12-02 19:18:50'),
(81, 37, 2, 1, 2, '2025-12-03 19:42:45'),
(82, 37, 9, 1, 2, '2025-12-03 19:42:45'),
(83, 37, 23, 1, 2, '2025-12-03 19:42:45'),
(84, 38, 3, 1, 2, '2025-12-03 19:43:01'),
(85, 39, 3, 1, 2, '2025-12-03 19:54:16'),
(86, 39, 6, 1, 2, '2025-12-03 19:54:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas_items`
--

CREATE TABLE `ventas_items` (
  `id` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_tamano` int(11) NOT NULL,
  `monto_unitario` decimal(10,2) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `monto_subtotal` decimal(10,2) NOT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  `estado` int(11) NOT NULL,
  `id_alta` int(11) NOT NULL,
  `fecha_alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `ventas_items`
--

INSERT INTO `ventas_items` (`id`, `id_venta`, `id_producto`, `id_tamano`, `monto_unitario`, `cantidad`, `monto_subtotal`, `monto_total`, `estado`, `id_alta`, `fecha_alta`) VALUES
(1, 1, 1, 3, 50.00, 2, 100.00, 100.00, 0, 7, '2025-11-25 21:17:25'),
(2, 1, 1, 1, 10.00, 3, 30.00, 30.00, 0, 7, '2025-11-25 21:17:25'),
(3, 2, 1, 3, 50.00, 1, 50.00, 80.00, 0, 7, '2025-11-26 17:56:34'),
(4, 3, 1, 3, 50.00, 1, 50.00, 50.00, 0, 7, '2025-12-03 19:43:26'),
(5, 3, 9, 3, 55.00, 3, 165.00, 165.00, 0, 7, '2025-12-03 19:43:26'),
(6, 4, 9, 3, 55.00, 2, 110.00, 110.00, 0, 7, '2025-12-03 19:55:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas_items_ingredientes`
--

CREATE TABLE `ventas_items_ingredientes` (
  `id` int(11) NOT NULL,
  `id_venta_item` int(11) NOT NULL,
  `id_ingrediente` int(11) NOT NULL,
  `costo_extra` decimal(10,2) NOT NULL,
  `cantidad_gratis` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  `estado` int(11) NOT NULL,
  `id_alta` int(11) NOT NULL,
  `fecha_alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `ventas_items_ingredientes`
--

INSERT INTO `ventas_items_ingredientes` (`id`, `id_venta_item`, `id_ingrediente`, `costo_extra`, `cantidad_gratis`, `cantidad`, `precio`, `monto_total`, `estado`, `id_alta`, `fecha_alta`) VALUES
(1, 1, 1, 0.00, 0, 1, 15.00, 0.00, 0, 7, '2025-11-25 21:17:25'),
(2, 1, 4, 0.00, 0, 1, 0.00, 0.00, 0, 7, '2025-11-25 21:17:25'),
(3, 1, 6, 1.00, 0, 1, 0.00, 0.00, 0, 7, '2025-11-25 21:17:25'),
(4, 2, 1, 0.00, 0, 1, 15.00, 0.00, 0, 7, '2025-11-25 21:17:25'),
(5, 2, 4, 0.00, 0, 1, 0.00, 0.00, 0, 7, '2025-11-25 21:17:25'),
(6, 2, 6, 1.00, 0, 1, 0.00, 0.00, 0, 7, '2025-11-25 21:17:25'),
(7, 3, 1, 0.00, 0, 1, 15.00, 0.00, 0, 7, '2025-11-26 17:56:34'),
(8, 3, 4, 0.00, 0, 1, 0.00, 0.00, 0, 7, '2025-11-26 17:56:34'),
(9, 3, 5, 1.00, 0, 3, 0.00, 0.00, 0, 7, '2025-11-26 17:56:34'),
(10, 3, 6, 1.00, 0, 1, 0.00, 0.00, 0, 7, '2025-11-26 17:56:34'),
(11, 3, 9, 1.00, 0, 1, 10.00, 10.00, 0, 7, '2025-11-26 17:56:34'),
(12, 3, 12, 1.00, 0, 1, 10.00, 10.00, 0, 7, '2025-11-26 17:56:34'),
(13, 3, 11, 1.00, 0, 1, 10.00, 10.00, 0, 7, '2025-11-26 17:56:34'),
(14, 4, 3, 0.00, 0, 1, 12.00, 0.00, 0, 7, '2025-12-03 19:43:26'),
(15, 5, 2, 0.00, 0, 1, 10.00, 0.00, 0, 7, '2025-12-03 19:43:26'),
(16, 5, 23, 0.00, 0, 1, 0.00, 0.00, 0, 7, '2025-12-03 19:43:26'),
(17, 5, 9, 0.00, 0, 1, 10.00, 0.00, 0, 7, '2025-12-03 19:43:26'),
(18, 6, 3, 0.00, 0, 1, 12.00, 0.00, 0, 7, '2025-12-03 19:55:07'),
(19, 6, 6, 0.00, 0, 1, 0.00, 0.00, 0, 7, '2025-12-03 19:55:07');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admin_niveles_usuario`
--
ALTER TABLE `admin_niveles_usuario`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `admin_niveles_usuario_modulos`
--
ALTER TABLE `admin_niveles_usuario_modulos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `admin_usuarios`
--
ALTER TABLE `admin_usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cafeterias`
--
ALTER TABLE `cafeterias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cafeterias_comentarios`
--
ALTER TABLE `cafeterias_comentarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cafeterias_imagenes`
--
ALTER TABLE `cafeterias_imagenes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cafeterias_servicios`
--
ALTER TABLE `cafeterias_servicios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cafeteria_horarios`
--
ALTER TABLE `cafeteria_horarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ciudades`
--
ALTER TABLE `ciudades`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `entidades_federativas`
--
ALTER TABLE `entidades_federativas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `menu_categorias`
--
ALTER TABLE `menu_categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `menu_ingredientes`
--
ALTER TABLE `menu_ingredientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `menu_ingredientes_categorias`
--
ALTER TABLE `menu_ingredientes_categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `menu_productos`
--
ALTER TABLE `menu_productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `menu_productos_bases`
--
ALTER TABLE `menu_productos_bases`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `menu_productos_tamanos`
--
ALTER TABLE `menu_productos_tamanos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `menu_subcategorias`
--
ALTER TABLE `menu_subcategorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pagina_inicial`
--
ALTER TABLE `pagina_inicial`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `paises`
--
ALTER TABLE `paises`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `permisos_areas`
--
ALTER TABLE `permisos_areas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `permisos_modulos`
--
ALTER TABLE `permisos_modulos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `propietarios_ingredientes`
--
ALTER TABLE `propietarios_ingredientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `propietarios_menu_subcategorias`
--
ALTER TABLE `propietarios_menu_subcategorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `propietarios_productos`
--
ALTER TABLE `propietarios_productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `propietarios_productos_tamanos`
--
ALTER TABLE `propietarios_productos_tamanos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ventas_carrito`
--
ALTER TABLE `ventas_carrito`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ventas_carrito_items`
--
ALTER TABLE `ventas_carrito_items`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ventas_carrito_items_ingredientes`
--
ALTER TABLE `ventas_carrito_items_ingredientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ventas_items`
--
ALTER TABLE `ventas_items`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ventas_items_ingredientes`
--
ALTER TABLE `ventas_items_ingredientes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admin_niveles_usuario`
--
ALTER TABLE `admin_niveles_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `admin_niveles_usuario_modulos`
--
ALTER TABLE `admin_niveles_usuario_modulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `admin_usuarios`
--
ALTER TABLE `admin_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `cafeterias`
--
ALTER TABLE `cafeterias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `cafeterias_comentarios`
--
ALTER TABLE `cafeterias_comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `cafeterias_imagenes`
--
ALTER TABLE `cafeterias_imagenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `cafeterias_servicios`
--
ALTER TABLE `cafeterias_servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `cafeteria_horarios`
--
ALTER TABLE `cafeteria_horarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de la tabla `ciudades`
--
ALTER TABLE `ciudades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `entidades_federativas`
--
ALTER TABLE `entidades_federativas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `menu_categorias`
--
ALTER TABLE `menu_categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `menu_ingredientes`
--
ALTER TABLE `menu_ingredientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT de la tabla `menu_ingredientes_categorias`
--
ALTER TABLE `menu_ingredientes_categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `menu_productos`
--
ALTER TABLE `menu_productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT de la tabla `menu_productos_bases`
--
ALTER TABLE `menu_productos_bases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `menu_productos_tamanos`
--
ALTER TABLE `menu_productos_tamanos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `menu_subcategorias`
--
ALTER TABLE `menu_subcategorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `pagina_inicial`
--
ALTER TABLE `pagina_inicial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `paises`
--
ALTER TABLE `paises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `permisos_areas`
--
ALTER TABLE `permisos_areas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `permisos_modulos`
--
ALTER TABLE `permisos_modulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT de la tabla `propietarios_ingredientes`
--
ALTER TABLE `propietarios_ingredientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `propietarios_menu_subcategorias`
--
ALTER TABLE `propietarios_menu_subcategorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `propietarios_productos`
--
ALTER TABLE `propietarios_productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `propietarios_productos_tamanos`
--
ALTER TABLE `propietarios_productos_tamanos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `ventas_carrito`
--
ALTER TABLE `ventas_carrito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `ventas_carrito_items`
--
ALTER TABLE `ventas_carrito_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `ventas_carrito_items_ingredientes`
--
ALTER TABLE `ventas_carrito_items_ingredientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT de la tabla `ventas_items`
--
ALTER TABLE `ventas_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `ventas_items_ingredientes`
--
ALTER TABLE `ventas_items_ingredientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
