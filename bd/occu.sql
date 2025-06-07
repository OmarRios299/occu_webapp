-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-06-2025 a las 15:59:11
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
(23, 2, 16);

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
(1, 'Omar', 'Rios', 1, 'admin@admin.com', '$2a$07$asxx54ahjppf45sd87a5auXBm1Vr2M1NV5t/zNQtGHGpS5fFirrbG', 'Administrador', '6865706609', 'views/assets/img/admin_usuarios/664_imagen_usuario_1.webp', '6833b77889f3c', '', 'Si', 0, 1, '2022-07-21 00:28:03'),
(4, 'Usuario', 'Prueba1', 1, 'user@user.com', '$2a$07$asxx54ahjppf45sd87a5augtYQ5l0YJxtJ.sls/VjJvJD4Oq/Jqk2', 'Propietario', '7412589636', 'views/assets/img/admin_usuarios/725_imagen_usuario_4.webp', '68444527c68ca', '', 'Si', 0, 1, '2024-08-16 21:21:43'),
(5, 'Usuario', 'Prueba1', 1, 'user@user.com', '$2a$07$asxx54ahjppf45sd87a5auGZEtGHuyZwm.Ur.FJvWLCql3nmsMbXy', 'Propietario', '7412589636', 'views/assets/img/admin_usuarios/166_imagen_usuario_5.webp', '', '', 'Si', 2, 1, '2024-08-16 21:23:12'),
(6, 'barista', 'numero1', 0, 'barista@bar.com', '$2a$07$asxx54ahjppf45sd87a5auGZEtGHuyZwm.Ur.FJvWLCql3nmsMbXy', 'Barista', 'undefined', 'views/assets/img/usuario_default.png', '670aabb699594', '', 'Si', 0, 1, '2024-10-11 21:44:44'),
(7, 'Cliente', 'numero1', 0, 'cliente@cli.com', '$2a$07$asxx54ahjppf45sd87a5auGZEtGHuyZwm.Ur.FJvWLCql3nmsMbXy', 'Cliente', '', 'views/assets/img/usuario_default.png', '670a885530158', '', 'Si', 0, 0, '2024-10-11 21:58:25'),
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
(1, 'cafetaFCA', 'views/assets/img/cafeteria_default.png', 'rf', 1, 'Av. Morelia 21', '', '', '', '', '18:44', '18:46', 'NO', 4, 0, 4, '2025-05-02 03:41:03'),
(2, 'cafetaFCA1', 'views/assets/img/cafeteria_default.png', '', 1, 'Av. Morelia 21', '', '', '', '', NULL, NULL, 'SI', 14, 0, 21, '2025-05-03 23:31:10'),
(3, 'cafetaFCA1215', 'views/assets/img/cafeteria_default.png', '', 1, 'Av. Morelia 21', '', '', '', '', '05:31', '19:31', 'NO', 14, 0, 21, '2025-05-03 23:32:06'),
(4, 'cafeta', 'views/assets/img/cafeteria_default.png', '', 1, 'Av. Morelia 21', '', '', '', '', '16:44', '16:46', 'NO', 21, 0, 21, '2025-05-04 01:40:21'),
(5, 'cafe chico', 'views/assets/img/cafeteria_default.png', '', 1, 'Av. Morelia 21', '', '', '', '', '', '', 'NO', 14, 2, 14, '2025-05-04 01:56:07'),
(6, 'cafe chico2', 'views/assets/img/cafeteria_default.png', '', 1, 'Av. Morelia 21', '', '', '', '', '', '', 'NO', 14, 2, 14, '2025-05-04 02:01:43'),
(7, 'cafetaFCA18', 'views/assets/img/cafeteria_default.png', '', 1, 'Av. Morelia 21', '', '', '', '', '', '', 'NO', 14, 2, 14, '2025-05-04 02:02:10'),
(8, 'Café punta del cielo', 'views/assets/img/cafeteria_default.png', '', 1, 'Av. Morelia 21', '', '', '', '', '', '', 'NO', 14, 2, 14, '2025-05-04 02:02:41'),
(9, 'CAfe 090', 'views/assets/img/cafeteria_default.png', 'cdcdcdcdc', 1, 'Av. Morelia 21', '6666666666', 'ca212feta@gmail.com', '', '', '08:07', '22:07', 'NO', 14, 2, 14, '2025-05-04 02:05:09');

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
(1, 'Buen sabor en el cafe', '', 16, 1, 0, '2025-05-17 13:01:42');

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
(2, 1, 'views/assets/img/cafeterias_imagenes/159_cafeteria_1_2.webp', '', 0);

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
(1, 1, 1, 0),
(2, 2, 1, 0),
(3, 3, 1, 0),
(4, 4, 1, 0),
(5, 5, 1, 0),
(6, 6, 1, 0);

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
  `id_alta` int(11) NOT NULL,
  `fecha_alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `menu_categorias`
--

INSERT INTO `menu_categorias` (`id`, `nombre`, `imagen`, `estado`, `id_alta`, `fecha_alta`) VALUES
(1, 'Bebidas', 'views/assets/img/menu_categorias/150_imagen_categoria_1.webp', 0, 1, '2024-08-17 22:22:07'),
(2, 'Tés y tisanas', 'views/assets/img/cafeteria_default.png', 2, 1, '2024-08-17 22:22:07'),
(3, 'Infusiones', 'views/assets/img/cafeteria_default.png', 0, 1, '2024-08-17 22:22:07'),
(4, 'Bebidas refrescantes', 'views/assets/img/cafeteria_default.png', 0, 1, '2024-08-17 22:28:10'),
(5, 'Postres', 'views/assets/img/cafeteria_default.png', 0, 1, '2024-08-17 22:26:54'),
(6, 'Alimentos', 'views/assets/img/cafeteria_default.png', 0, 1, '2024-08-17 22:26:54'),
(8, 'cat1', 'views/assets/img/cafeteria_default.png', 2, 1, '2024-09-19 03:28:00'),
(9, 'cat2', 'views/assets/img/cafeteria_default.png', 2, 1, '2024-09-19 03:29:17'),
(10, 'cat3', 'views/assets/img/menu_categorias/453_imagen_categoria_10.webp', 2, 1, '2024-09-19 03:30:31'),
(11, 'Cafés frios2', 'views/assets/img/cafeteria_default.png', 2, 1, '2024-09-19 07:49:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu_productos`
--

CREATE TABLE `menu_productos` (
  `id` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `id_subcategoria` int(11) NOT NULL,
  `imagen` text NOT NULL,
  `estado` int(11) NOT NULL,
  `id_alta` int(11) NOT NULL,
  `fecha_alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `menu_productos`
--

INSERT INTO `menu_productos` (`id`, `nombre`, `id_subcategoria`, `imagen`, `estado`, `id_alta`, `fecha_alta`) VALUES
(1, 'Latte', 2, 'views/assets/img/menu_productos/474_imagen_producto_1.webp', 0, 1, '2024-08-17 22:36:24'),
(2, 'Bora bora', 3, 'views/assets/img/menu_productos/289_imagen_producto_2.webp', 0, 1, '2024-09-20 04:46:57'),
(3, 'Red velvet', 6, 'views/assets/img/menu_productos/366_imagen_producto_3.webp', 0, 1, '2024-09-20 04:50:14'),
(4, 'Galleta de chispas de chocolate', 5, 'views/assets/img/cafeteria_default.png', 0, 1, '2024-11-08 06:23:03'),
(5, 'Caramelo', 4, 'views/assets/img/cafeteria_default.png', 0, 1, '2024-11-08 06:41:21'),
(6, 'Coca cola', 8, 'views/assets/img/cafeteria_default.png', 0, 1, '2024-11-08 06:41:51'),
(7, 'Ensalada de pollo', 7, 'views/assets/img/cafeteria_default.png', 0, 1, '2024-11-08 06:42:22'),
(8, 'Mineral', 9, 'views/assets/img/cafeteria_default.png', 0, 1, '2024-11-08 06:43:13'),
(9, 'Mocha', 4, 'views/assets/img/cafeteria_default.png', 0, 1, '2024-11-09 23:36:28'),
(10, 'Iced latte', 10, 'views/assets/img/cafeteria_default.png', 0, 1, '2024-11-10 07:17:34'),
(11, 'Iced mocha', 10, 'views/assets/img/cafeteria_default.png', 0, 1, '2024-11-10 07:18:37'),
(12, 'Chai en las rocas', 10, 'views/assets/img/cafeteria_default.png', 0, 1, '2024-11-10 07:19:03'),
(13, 'Sprite', 8, 'views/assets/img/cafeteria_default.png', 0, 1, '2024-12-11 02:51:16'),
(14, 'Coca cola cero', 8, 'views/assets/img/cafeteria_default.png', 0, 1, '2024-12-11 02:51:47'),
(15, 'prod1', 2, 'views/assets/img/cafeteria_default.png', 0, 1, '2025-05-04 22:39:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu_productos_ingredientes`
--

CREATE TABLE `menu_productos_ingredientes` (
  `id` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `tipo` text NOT NULL,
  `estado` int(11) NOT NULL,
  `id_alta` int(11) NOT NULL,
  `fecha_alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `menu_productos_ingredientes`
--

INSERT INTO `menu_productos_ingredientes` (`id`, `nombre`, `tipo`, `estado`, `id_alta`, `fecha_alta`) VALUES
(1, 'Espresso', 'Bebida', 0, 1, '2024-08-17 22:30:11'),
(2, 'Leche entera', 'Bebida', 0, 1, '2024-08-17 22:30:11'),
(3, 'Leche deslactosada', 'Bebida', 0, 1, '2024-08-17 22:30:11'),
(4, 'Leche de almendras', 'Bebida', 0, 1, '2024-08-17 22:30:11'),
(5, 'Esencia', 'Bebida', 0, 1, '2024-08-17 22:30:11'),
(6, 'Splenda', 'Bebida', 0, 1, '2024-08-17 22:30:11'),
(7, 'Fresas', 'Alimento', 0, 1, '2024-08-17 22:30:11'),
(8, 'Platano', 'Alimento', 0, 1, '2024-08-17 22:30:11');

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
(1, 'Chico', 'Oz', '4', 0),
(2, 'Mediano', 'Oz', '6', 0),
(3, 'Grande', 'Oz', '12', 0),
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
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `menu_subcategorias`
--

INSERT INTO `menu_subcategorias` (`id`, `nombre`, `id_categoria`, `estado`) VALUES
(1, 'Frias', 1, 2),
(2, 'Calientes', 1, 0),
(3, 'Tisanas', 3, 0),
(4, 'Frappes', 1, 0),
(5, 'Galletas', 5, 0),
(6, 'Pasteles', 5, 0),
(7, 'Ensaladas', 6, 0),
(8, 'Sodas', 4, 0),
(9, 'Limonadas', 4, 0),
(10, 'En las rocas', 1, 0),
(11, 'Tés', 3, 2),
(12, 'Jamaica', 3, 2),
(13, 'Pepsi', 4, 2),
(14, 'Agua', 4, 2),
(15, 'Gelatinas', 5, 2),
(16, 'Dulces', 5, 2);

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
(16, 2, 0, 'QR de menú', 'propietarios_menu_qr', '', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propietarios_menu_cafeterias`
--

CREATE TABLE `propietarios_menu_cafeterias` (
  `id` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_producto_extra` int(11) NOT NULL,
  `id_tamano` int(11) NOT NULL,
  `id_cafeteria` int(11) NOT NULL,
  `precio` text NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `propietarios_menu_cafeterias`
--

INSERT INTO `propietarios_menu_cafeterias` (`id`, `id_producto`, `id_producto_extra`, `id_tamano`, `id_cafeteria`, `precio`, `estado`) VALUES
(25, 1, 0, 0, 2, '', 1),
(26, 15, 0, 0, 2, '', 1),
(27, 5, 0, 0, 2, '', 1),
(28, 10, 0, 0, 2, '', 1),
(29, 0, 1, 0, 2, '', 1),
(30, 0, 2, 0, 2, '', 1),
(31, 0, 1, 1, 2, '40', 0),
(32, 0, 1, 3, 2, '45', 1),
(33, 0, 1, 5, 2, '50', 1),
(34, 1, 0, 1, 2, '60', 1),
(35, 1, 0, 5, 2, '80', 1),
(36, 1, 0, 3, 2, '70', 1),
(37, 0, 1, 2, 2, '55', 1),
(38, 1, 0, 0, 3, '', 1),
(39, 15, 0, 0, 3, '', 1),
(40, 5, 0, 0, 3, '', 1),
(41, 10, 0, 0, 3, '', 1),
(42, 0, 1, 0, 3, '', 1),
(43, 0, 2, 0, 3, '', 1),
(44, 0, 1, 1, 3, '40', 0),
(45, 0, 1, 3, 3, '45', 1),
(46, 0, 1, 5, 3, '50', 1),
(47, 1, 0, 1, 3, '60', 1),
(48, 1, 0, 5, 3, '80', 1),
(49, 1, 0, 3, 3, '70', 1),
(50, 0, 1, 2, 3, '55', 1),
(140, 1, 0, 0, 1, '', 0),
(141, 15, 0, 0, 1, '', 1),
(142, 5, 0, 0, 1, '', 1),
(143, 9, 0, 0, 1, '', 1),
(144, 10, 0, 0, 1, '', 1),
(145, 11, 0, 0, 1, '', 1),
(146, 1, 0, 5, 1, '30', 1),
(147, 1, 0, 3, 1, '20', 1),
(148, 1, 0, 1, 1, '10', 1),
(149, 15, 0, 5, 1, '25', 1),
(150, 15, 0, 3, 1, '20', 1),
(151, 15, 0, 1, 1, '15', 1),
(152, 5, 0, 5, 1, '60', 1),
(153, 5, 0, 3, 1, '55', 1),
(154, 5, 0, 1, 1, '50', 1),
(155, 9, 0, 5, 1, '60', 1),
(156, 9, 0, 3, 1, '55', 1),
(157, 9, 0, 1, 1, '50', 1),
(158, 10, 0, 5, 1, '65', 1),
(159, 10, 0, 3, 1, '60', 1),
(160, 10, 0, 1, 1, '55', 1),
(161, 11, 0, 1, 1, '55', 1),
(162, 11, 0, 3, 1, '60', 1),
(163, 11, 0, 5, 1, '65', 1),
(164, 0, 3, 0, 1, '', 1),
(165, 0, 3, 1, 1, '60', 1),
(166, 0, 3, 3, 1, '70', 1),
(167, 0, 3, 5, 1, '75', 1),
(168, 0, 4, 0, 1, '', 0),
(169, 0, 4, 5, 1, '30', 1),
(170, 0, 4, 3, 1, '20', 1),
(171, 0, 4, 1, 1, '100', 1),
(172, 0, 5, 0, 1, '', 1),
(173, 0, 4, 2, 1, '100', 0),
(174, 6, 0, 0, 1, '', 1),
(175, 13, 0, 0, 1, '', 1),
(176, 14, 0, 0, 1, '', 1),
(177, 0, 6, 0, 1, '', 1),
(178, 0, 7, 0, 1, '', 1),
(179, 15, 0, 2, 1, '0', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propietarios_menu_productos`
--

CREATE TABLE `propietarios_menu_productos` (
  `id` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_producto_extra` int(11) NOT NULL,
  `id_tamano` int(11) NOT NULL,
  `id_propietario` int(11) NOT NULL,
  `precio` text NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `propietarios_menu_productos`
--

INSERT INTO `propietarios_menu_productos` (`id`, `id_producto`, `id_producto_extra`, `id_tamano`, `id_propietario`, `precio`, `estado`) VALUES
(1, 1, 0, 0, 14, '', 1),
(2, 15, 0, 0, 14, '', 1),
(3, 5, 0, 0, 14, '', 1),
(4, 10, 0, 0, 14, '', 1),
(5, 0, 1, 0, 14, '', 0),
(6, 0, 2, 0, 14, '', 1),
(7, 0, 1, 1, 14, '40', 0),
(8, 0, 1, 3, 14, '45', 1),
(9, 0, 1, 5, 14, '50', 1),
(10, 1, 0, 1, 14, '60', 1),
(11, 1, 0, 5, 14, '80', 1),
(12, 1, 0, 3, 14, '70', 1),
(13, 0, 1, 2, 14, '55', 1),
(14, 4, 0, 0, 14, '', 1),
(15, 1, 0, 0, 4, '', 0),
(16, 15, 0, 0, 4, '', 1),
(17, 5, 0, 0, 4, '', 1),
(18, 9, 0, 0, 4, '', 1),
(19, 10, 0, 0, 4, '', 1),
(20, 11, 0, 0, 4, '', 1),
(21, 1, 0, 5, 4, '30', 1),
(22, 1, 0, 3, 4, '20', 1),
(23, 1, 0, 1, 4, '10', 1),
(24, 15, 0, 5, 4, '25', 1),
(25, 15, 0, 3, 4, '20', 1),
(26, 15, 0, 1, 4, '15', 1),
(27, 5, 0, 5, 4, '60', 1),
(28, 5, 0, 3, 4, '55', 1),
(29, 5, 0, 1, 4, '50', 1),
(30, 9, 0, 5, 4, '60', 1),
(31, 9, 0, 3, 4, '55', 1),
(32, 9, 0, 1, 4, '50', 1),
(33, 10, 0, 5, 4, '65', 1),
(34, 10, 0, 3, 4, '60', 1),
(35, 10, 0, 1, 4, '55', 1),
(36, 11, 0, 1, 4, '55', 1),
(37, 11, 0, 3, 4, '60', 1),
(38, 11, 0, 5, 4, '65', 1),
(39, 0, 3, 0, 4, '', 1),
(40, 0, 3, 1, 4, '60', 1),
(41, 0, 3, 3, 4, '70', 1),
(42, 0, 3, 5, 4, '75', 1),
(43, 0, 4, 0, 4, '', 0),
(44, 0, 4, 5, 4, '30', 1),
(45, 0, 4, 3, 4, '20', 1),
(46, 0, 4, 1, 4, '100', 1),
(47, 0, 5, 0, 4, '', 1),
(48, 0, 4, 2, 4, '100', 0),
(49, 6, 0, 0, 4, '', 1),
(50, 13, 0, 0, 4, '', 1),
(51, 14, 0, 0, 4, '', 1),
(52, 0, 6, 0, 4, '', 1),
(53, 0, 7, 0, 4, '', 1),
(54, 15, 0, 2, 4, '0', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propietarios_menu_productos_extra`
--

CREATE TABLE `propietarios_menu_productos_extra` (
  `id` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `id_subcategoria` int(11) NOT NULL,
  `id_subcategoria_extra` int(11) NOT NULL,
  `id_propietario` int(11) NOT NULL,
  `imagen` text NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `propietarios_menu_productos_extra`
--

INSERT INTO `propietarios_menu_productos_extra` (`id`, `nombre`, `id_subcategoria`, `id_subcategoria_extra`, `id_propietario`, `imagen`, `estado`) VALUES
(1, 'Té de frambuesa', 0, 1, 14, 'views/assets/img/cafeteria_default.png', 0),
(2, 'Galleta de chispas', 5, 0, 14, 'views/assets/img/cafeteria_default.png', 0),
(3, 'Latte especial', 10, 0, 4, 'views/assets/img/cafeteria_default.png', 0),
(4, 'Jamaica', 0, 2, 4, 'views/assets/img/cafeteria_default.png', 0),
(5, 'prod2', 2, 0, 4, 'views/assets/img/cafeteria_default.png', 0),
(6, 'Frappe fresa', 4, 0, 4, 'views/assets/img/cafeteria_default.png', 0),
(7, 'Galleta', 6, 0, 4, 'views/assets/img/cafeteria_default.png', 0);

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
(11, 7, 4, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propietarios_menu_subcategorias_extra`
--

CREATE TABLE `propietarios_menu_subcategorias_extra` (
  `id` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `id_propietario` int(11) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `propietarios_menu_subcategorias_extra`
--

INSERT INTO `propietarios_menu_subcategorias_extra` (`id`, `nombre`, `id_categoria`, `id_propietario`, `estado`) VALUES
(1, 'Tés', 3, 14, 1),
(2, 'Tés', 3, 4, 0),
(3, 'Merancia', 6, 4, 2);

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
-- Indices de la tabla `menu_productos`
--
ALTER TABLE `menu_productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `menu_productos_ingredientes`
--
ALTER TABLE `menu_productos_ingredientes`
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
-- Indices de la tabla `propietarios_menu_cafeterias`
--
ALTER TABLE `propietarios_menu_cafeterias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `propietarios_menu_productos`
--
ALTER TABLE `propietarios_menu_productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `propietarios_menu_productos_extra`
--
ALTER TABLE `propietarios_menu_productos_extra`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `propietarios_menu_subcategorias`
--
ALTER TABLE `propietarios_menu_subcategorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `propietarios_menu_subcategorias_extra`
--
ALTER TABLE `propietarios_menu_subcategorias_extra`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `admin_usuarios`
--
ALTER TABLE `admin_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `cafeterias`
--
ALTER TABLE `cafeterias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `cafeterias_comentarios`
--
ALTER TABLE `cafeterias_comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cafeterias_imagenes`
--
ALTER TABLE `cafeterias_imagenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `cafeterias_servicios`
--
ALTER TABLE `cafeterias_servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `menu_productos`
--
ALTER TABLE `menu_productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `menu_productos_ingredientes`
--
ALTER TABLE `menu_productos_ingredientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `menu_productos_tamanos`
--
ALTER TABLE `menu_productos_tamanos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `menu_subcategorias`
--
ALTER TABLE `menu_subcategorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT de la tabla `propietarios_menu_cafeterias`
--
ALTER TABLE `propietarios_menu_cafeterias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=180;

--
-- AUTO_INCREMENT de la tabla `propietarios_menu_productos`
--
ALTER TABLE `propietarios_menu_productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de la tabla `propietarios_menu_productos_extra`
--
ALTER TABLE `propietarios_menu_productos_extra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `propietarios_menu_subcategorias`
--
ALTER TABLE `propietarios_menu_subcategorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `propietarios_menu_subcategorias_extra`
--
ALTER TABLE `propietarios_menu_subcategorias_extra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
