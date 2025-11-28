-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-11-2025 a las 05:58:28
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
(1, 'Omar', 'Rios', 1, 'admin@admin.com', '$2a$07$asxx54ahjppf45sd87a5auXBm1Vr2M1NV5t/zNQtGHGpS5fFirrbG', 'Administrador', '6865706609', 'views/assets/img/admin_usuarios/664_imagen_usuario_1.webp', '69223c3543a18', '', 'Si', 0, 1, '2022-07-21 00:28:03'),
(4, 'Usuario', 'Prueba1', 1, 'user@user.com', '$2a$07$asxx54ahjppf45sd87a5augtYQ5l0YJxtJ.sls/VjJvJD4Oq/Jqk2', 'Propietario', '7412589636', 'views/assets/img/admin_usuarios/725_imagen_usuario_4.webp', '6923a6d6ca5ae', '', 'Si', 0, 1, '2024-08-16 21:21:43'),
(5, 'Usuario', 'Prueba1', 1, 'user2@user.com', '$2a$07$asxx54ahjppf45sd87a5augtYQ5l0YJxtJ.sls/VjJvJD4Oq/Jqk2', 'Propietario', '7412589636', 'views/assets/img/admin_usuarios/166_imagen_usuario_5.webp', '6881bed36554f', '', 'Si', 0, 1, '2024-08-16 21:23:12'),
(6, 'barista', 'numero1', 0, 'barista@bar.com', '$2a$07$asxx54ahjppf45sd87a5auGZEtGHuyZwm.Ur.FJvWLCql3nmsMbXy', 'Barista', 'undefined', 'views/assets/img/usuario_default.png', '670aabb699594', '', 'Si', 0, 1, '2024-10-11 21:44:44'),
(7, 'Cliente', 'numero1', 0, 'cliente@cliente.com', '$2a$07$asxx54ahjppf45sd87a5augtYQ5l0YJxtJ.sls/VjJvJD4Oq/Jqk2', 'Cliente', '', 'views/assets/img/usuario_default.png', '692527573d097', '', 'Si', 0, 0, '2024-10-11 21:58:25'),
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
(9, 'CAfe 090', 'views/assets/img/cafeteria_default.png', 'cdcdcdcdc', 1, 'Av. Morelia 21', '6666666666', 'ca212feta@gmail.com', '', '', '08:07', '22:07', 'NO', 14, 2, 14, '2025-05-04 02:05:09'),
(10, 'CAfe 2', 'views/assets/img/cafeteria_default.png', '', 1, '.', '', '', '', '', '', '', 'NO', 4, 0, 4, '2025-07-27 12:28:03');

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
(1, 'Bebidas', 'views/assets/img/menu_categorias/150_imagen_categoria_1.webp', 0, 'Si', 1, '2024-08-17 22:22:07'),
(2, 'Tés y tisanas', 'views/assets/img/cafeteria_default.png', 2, 'Si', 1, '2024-08-17 22:22:07'),
(3, 'Infusiones', 'views/assets/img/cafeteria_default.png', 0, 'Si', 1, '2024-08-17 22:22:07'),
(4, 'Bebidas refrescantes', 'views/assets/img/cafeteria_default.png', 0, 'Si', 1, '2024-08-17 22:28:10'),
(5, 'Postres', 'views/assets/img/cafeteria_default.png', 0, '', 1, '2024-08-17 22:26:54'),
(6, 'Alimentos', 'views/assets/img/cafeteria_default.png', 0, '', 1, '2024-08-17 22:26:54'),
(8, 'cat1', 'views/assets/img/cafeteria_default.png', 2, '', 1, '2024-09-19 03:28:00'),
(9, 'cat2', 'views/assets/img/cafeteria_default.png', 2, '', 1, '2024-09-19 03:29:17'),
(10, 'cat3', 'views/assets/img/menu_categorias/453_imagen_categoria_10.webp', 2, '', 1, '2024-09-19 03:30:31'),
(11, 'Cafés frios2', 'views/assets/img/cafeteria_default.png', 2, '', 1, '2024-09-19 07:49:47'),
(12, 'Pruebw', 'views/assets/img/cafeteria_default.png', 2, 'Si', 1, '2025-11-20 19:41:09');

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

INSERT INTO `menu_ingredientes` (`id`, `id_ingrediente_categoria`, `nombre`, `registro_occu`, `id_alta`, `fecha_alta`) VALUES
(1, 1, 'Regularr', 0, 0, 1, '2025-06-14 20:58:02'),
(2, 1, 'Descafeinado', 0, 0, 1, '2025-06-14 20:58:02'),
(3, 2, 'Entera', 0, 0, 1, '2025-06-14 20:59:16'),
(4, 2, 'Deslactosada', 0, 0, 1, '2025-06-14 20:59:16'),
(5, 3, 'Regular', 0, 0, 1, '2025-06-14 20:59:45'),
(6, 3, 'Splenda', 0, 0, 1, '2025-06-14 20:59:45'),
(7, 4, 'Mocha', 0, 0, 1, '2025-06-14 21:00:19'),
(8, 4, 'Matcha', 0, 0, 1, '2025-06-14 21:00:19'),
(9, 5, 'Vainilla', 0, 0, 1, '2025-06-14 21:00:56'),
(10, 5, 'Caramelo', 0, 0, 1, '2025-06-14 21:00:56'),
(11, 6, 'Crema batida', 0, 0, 1, '2025-06-14 21:01:46'),
(12, 6, 'Expreso', 0, 0, 1, '2025-06-14 21:01:46'),
(13, 3, 'nms', 0, 2, 0, '0000-00-00 00:00:00'),
(14, 1, 'coca', 2, 2, 4, '2025-07-21 21:52:03'),
(15, 1, 'coca2', 2, 2, 4, '2025-07-21 21:55:44'),
(16, 2, 'coca3', 2, 2, 4, '2025-07-21 21:58:49'),
(17, 2, 'coca4', 2, 2, 4, '2025-07-21 21:59:20'),
(18, 1, 'coca4', 2, 2, 4, '2025-07-21 22:01:48'),
(19, 1, 'coca4', 2, 2, 4, '2025-07-21 22:01:54'),
(20, 1, '123', 2, 2, 4, '2025-07-21 22:07:24'),
(21, 2, '789', 2, 2, 4, '2025-07-21 22:08:23'),
(22, 1, 'poiiyu', 2, 2, 4, '2025-07-27 14:21:20'),
(23, 2, 'Almendra', 2, 0, 4, '2025-11-24 23:04:20'),
(24, 2, 'cocc', 2, 2, 4, '2025-11-24 23:04:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu_ingredientes_categorias`
--

CREATE TABLE `menu_ingredientes_categorias` (
  `id` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `obligatoria` text NOT NULL,
  `estado` int(11) NOT NULL,
  `id_alta` int(11) NOT NULL,
  `fecha_alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `menu_ingredientes_categorias`
--

INSERT INTO `menu_ingredientes_categorias` (`id`, `nombre`, `id_alta`, `fecha_alta`) VALUES
(1, 'Tipo de grano', 'Si', 0, 1, '2024-08-17 22:30:11'),
(2, 'Leches', 'Si', 0, 1, '2024-08-17 22:30:11'),
(3, 'Azúcar', '', 0, 1, '2024-08-17 22:30:11'),
(4, 'Sabores', '', 0, 1, '2024-08-17 22:30:11'),
(5, 'Esencias', '', 0, 1, '2024-08-17 22:30:11'),
(6, 'Extras', '', 0, 1, '2024-08-17 22:30:11'),
(7, 'w', '', 0, 1, '2024-08-17 22:30:11'),
(8, 'w', '', 0, 1, '2024-08-17 22:30:11'),
(9, 'Cereales', '', 2, 0, '0000-00-00 00:00:00');


INSERT INTO `menu_ingredientes_categorias` (`id`, `nombre`, `id_alta`, `fecha_alta`) VALUES
(1, 'Tipo de Grano / Café', 1, NOW()),
(2, 'Leche', 1, NOW()),
(3, 'Endulzantes', 1, NOW()),
(4, 'Saborizantes (Jarabes / Shots)', 1, NOW()),
(5, 'Toppings para Bebidas', 1, NOW()),
(6, 'Temperatura y Estilo', 1, NOW()),
(7, 'Tipo de Té', 1, NOW()),
(8, 'Tipo de Chocolate / Cocoa', 1, NOW()),
(9, 'Bases para Frappé / Smoothie', 1, NOW()),

(10, 'Pan / Base del Platillo', 1, NOW()),
(11, 'Proteínas', 1, NOW()),
(12, 'Vegetales', 1, NOW()),
(13, 'Salsas y Aderezos', 1, NOW()),
(14, 'Quesos', 1, NOW()),
(15, 'Extras Gourmet', 1, NOW()),

(16, 'Tipo de Masa / Base', 1, NOW()),
(17, 'Rellenos', 1, NOW()),
(18, 'Coberturas de Repostería', 1, NOW()),

(19, 'Opciones Veganas', 1, NOW()),
(20, 'Opciones Sin Azúcar', 1, NOW()),
(21, 'Opciones Sin Lactosa', 1, NOW()),
(22, 'Opciones Gluten Free', 1, NOW()),
(23, 'Especialidades Regionales', 1, NOW());

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

INSERT INTO `menu_productos` (`id`, `nombre`, `id_subcategoria`, `imagen`, `registro_occu`, `id_alta`, `fecha_alta`) VALUES
(1, 'Latte', 2, 'views/assets/img/menu_productos/474_imagen_producto_1.webp', 1, 0, 1, '2024-08-17 22:36:24'),
(2, 'Bora bora', 3, 'views/assets/img/menu_productos/289_imagen_producto_2.webp', 1, 0, 1, '2024-09-20 04:46:57'),
(3, 'Red velvet', 6, 'views/assets/img/menu_productos/366_imagen_producto_3.webp', 1, 0, 1, '2024-09-20 04:50:14'),
(4, 'Galleta de chispas de chocolate', 5, 'views/assets/img/cafeteria_default.png', 1, 0, 1, '2024-11-08 06:23:03'),
(5, 'Caramelo', 4, 'views/assets/img/cafeteria_default.png', 1, 0, 1, '2024-11-08 06:41:21'),
(6, 'Coca cola', 8, 'views/assets/img/cafeteria_default.png', 1, 0, 1, '2024-11-08 06:41:51'),
(7, 'Ensalada de pollo', 7, 'views/assets/img/cafeteria_default.png', 1, 0, 1, '2024-11-08 06:42:22'),
(8, 'Mineral', 9, 'views/assets/img/cafeteria_default.png', 1, 0, 1, '2024-11-08 06:43:13'),
(9, 'Mocha', 4, 'views/assets/img/cafeteria_default.png', 1, 0, 1, '2024-11-09 23:36:28'),
(10, 'Iced latte', 10, 'views/assets/img/cafeteria_default.png', 1, 0, 1, '2024-11-10 07:17:34'),
(11, 'Iced mocha', 10, 'views/assets/img/cafeteria_default.png', 1, 0, 1, '2024-11-10 07:18:37'),
(12, 'Chai en las rocas', 10, 'views/assets/img/cafeteria_default.png', 1, 0, 1, '2024-11-10 07:19:03'),
(13, 'Sprite', 8, 'views/assets/img/cafeteria_default.png', 1, 0, 1, '2024-12-11 02:51:16'),
(14, 'Coca cola cero', 8, 'views/assets/img/cafeteria_default.png', 1, 0, 1, '2024-12-11 02:51:47'),
(15, 'prod1', 2, 'views/assets/img/cafeteria_default.png', 1, 0, 1, '2025-05-04 22:39:18'),
(16, 'qweqwe', 2, 'views/assets/img/cafeteria_default.png', 2, 0, 4, '0000-00-00 00:00:00'),
(17, 'Frappe fresa', 4, 'views/assets/img/cafeteria_default.png', 2, 0, 4, '0000-00-00 00:00:00'),
(18, 'qqqqq', 2, 'views/assets/img/cafeteria_default.png', 1, 2, 1, '2025-07-08 06:28:53'),
(19, 'Prodcatee', 19, 'views/assets/img/cafeteria_default.png', 2, 0, 4, '0000-00-00 00:00:00'),
(20, '', 0, 'views/assets/img/cafeteria_default.png', 2, 0, 4, '0000-00-00 00:00:00'),
(21, 'fddfdfd', 2, 'views/assets/img/cafeteria_default.png', 2, 0, 4, '0000-00-00 00:00:00');

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
(1, 'Frias', 1, 2, 1, 0, '0000-00-00 00:00:00'),
(2, 'Calientes', 1, 0, 1, 0, '0000-00-00 00:00:00'),
(3, 'Tisanas', 3, 0, 1, 0, '0000-00-00 00:00:00'),
(4, 'Frappes', 1, 0, 1, 0, '0000-00-00 00:00:00'),
(5, 'Galletas', 5, 0, 1, 0, '0000-00-00 00:00:00'),
(6, 'Pasteles', 5, 0, 1, 0, '0000-00-00 00:00:00'),
(7, 'Ensaladas', 6, 0, 1, 0, '0000-00-00 00:00:00'),
(8, 'Sodas', 4, 0, 1, 0, '0000-00-00 00:00:00'),
(9, 'Limonadas', 4, 0, 1, 0, '0000-00-00 00:00:00'),
(10, 'En las rocas', 1, 0, 1, 0, '0000-00-00 00:00:00'),
(11, 'Tés', 3, 2, 1, 0, '0000-00-00 00:00:00'),
(12, 'Jamaica', 3, 2, 1, 0, '0000-00-00 00:00:00'),
(13, 'Pepsi', 4, 2, 1, 0, '0000-00-00 00:00:00'),
(14, 'Agua', 4, 2, 1, 0, '0000-00-00 00:00:00'),
(15, 'Gelatinas', 5, 2, 1, 0, '0000-00-00 00:00:00'),
(16, 'Dulces', 5, 2, 1, 0, '0000-00-00 00:00:00'),
(17, 'Merancia', 1, 2, 2, 4, '0000-00-00 00:00:00'),
(18, 'Merancia', 6, 2, 2, 4, '0000-00-00 00:00:00'),
(19, 'Catee', 1, 0, 2, 4, '0000-00-00 00:00:00');

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
-- Estructura de tabla para la tabla `propietarios_menu_cafeterias`
--

CREATE TABLE `propietarios_menu_cafeterias` (
  `id` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_tamano` int(11) NOT NULL,
  `id_cafeteria` int(11) NOT NULL,
  `precio` text NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `propietarios_menu_cafeterias`
--

INSERT INTO `propietarios_menu_cafeterias` (`id`, `id_producto`, `id_tamano`, `id_cafeteria`, `precio`, `estado`) VALUES
(25, 1, 0, 2, '', 1),
(26, 15, 0, 2, '', 1),
(27, 5, 0, 2, '', 1),
(28, 10, 0, 2, '', 1),
(29, 0, 0, 2, '', 1),
(30, 0, 0, 2, '', 1),
(31, 0, 1, 2, '40', 0),
(32, 0, 3, 2, '45', 1),
(33, 0, 5, 2, '50', 1),
(34, 1, 1, 2, '60', 1),
(35, 1, 5, 2, '80', 1),
(36, 1, 3, 2, '70', 1),
(37, 0, 2, 2, '55', 1),
(38, 1, 0, 3, '', 1),
(39, 15, 0, 3, '', 1),
(40, 5, 0, 3, '', 1),
(41, 10, 0, 3, '', 1),
(42, 0, 0, 3, '', 1),
(43, 0, 0, 3, '', 1),
(44, 0, 1, 3, '40', 0),
(45, 0, 3, 3, '45', 1),
(46, 0, 5, 3, '50', 1),
(47, 1, 1, 3, '60', 1),
(48, 1, 5, 3, '80', 1),
(49, 1, 3, 3, '70', 1),
(50, 0, 2, 3, '55', 1),
(1967, 1, 0, 1, '', 1),
(1968, 15, 0, 1, '', 0),
(1969, 5, 0, 1, '', 1),
(1970, 9, 0, 1, '', 1),
(1971, 10, 0, 1, '', 1),
(1972, 11, 0, 1, '', 1),
(1973, 1, 5, 1, '70', 1),
(1974, 1, 3, 1, '50', 1),
(1975, 1, 1, 1, '10', 0),
(1976, 15, 5, 1, '25', 1),
(1977, 15, 3, 1, '20', 1),
(1978, 15, 1, 1, '15', 1),
(1979, 5, 5, 1, '60', 1),
(1980, 5, 3, 1, '55', 1),
(1981, 5, 1, 1, '50', 1),
(1982, 9, 5, 1, '60', 1),
(1983, 9, 3, 1, '55', 1),
(1984, 9, 1, 1, '50', 1),
(1985, 10, 5, 1, '65', 1),
(1986, 10, 3, 1, '66', 1),
(1987, 10, 1, 1, '55', 1),
(1988, 11, 1, 1, '55', 1),
(1989, 11, 3, 1, '60', 1),
(1990, 11, 5, 1, '65', 1),
(1991, 0, 0, 1, '', 1),
(1992, 0, 1, 1, '60', 1),
(1993, 0, 3, 1, '70', 1),
(1994, 0, 5, 1, '75', 1),
(1995, 0, 0, 1, '', 0),
(1996, 0, 5, 1, '30', 1),
(1997, 0, 3, 1, '20', 1),
(1998, 0, 1, 1, '100', 1),
(1999, 0, 0, 1, '', 1),
(2000, 0, 2, 1, '100', 0),
(2001, 6, 0, 1, '', 1),
(2002, 13, 0, 1, '', 1),
(2003, 14, 0, 1, '', 1),
(2004, 0, 0, 1, '', 1),
(2005, 0, 0, 1, '', 1),
(2006, 15, 2, 1, '0', 1),
(2007, 16, 0, 1, '', 0),
(2008, 16, 5, 1, '20', 1),
(2009, 16, 3, 1, '20', 1),
(2010, 16, 1, 1, '20', 1),
(2011, 17, 0, 1, '', 1),
(2012, 19, 0, 1, '', 1),
(2013, 16, 2, 1, '0', 1),
(2014, 16, 4, 1, '0', 1),
(2015, 20, 0, 1, '', 1),
(2016, 21, 0, 1, '', 0),
(2017, 4, 0, 1, '', 1),
(2018, 3, 0, 1, '', 1),
(2019, 7, 0, 1, '', 1),
(2020, 1, 4, 1, '60', 1),
(2021, 1, 0, 10, '', 1),
(2022, 15, 0, 10, '', 0),
(2023, 5, 0, 10, '', 1),
(2024, 9, 0, 10, '', 1),
(2025, 10, 0, 10, '', 1),
(2026, 11, 0, 10, '', 1),
(2027, 1, 5, 10, '70', 1),
(2028, 1, 3, 10, '50', 1),
(2029, 1, 1, 10, '10', 0),
(2030, 15, 5, 10, '25', 1),
(2031, 15, 3, 10, '20', 1),
(2032, 15, 1, 10, '15', 1),
(2033, 5, 5, 10, '60', 1),
(2034, 5, 3, 10, '55', 1),
(2035, 5, 1, 10, '50', 1),
(2036, 9, 5, 10, '60', 1),
(2037, 9, 3, 10, '55', 1),
(2038, 9, 1, 10, '50', 1),
(2039, 10, 5, 10, '65', 1),
(2040, 10, 3, 10, '66', 1),
(2041, 10, 1, 10, '55', 1),
(2042, 11, 1, 10, '55', 1),
(2043, 11, 3, 10, '60', 1),
(2044, 11, 5, 10, '65', 1),
(2045, 0, 0, 10, '', 1),
(2046, 0, 1, 10, '60', 1),
(2047, 0, 3, 10, '70', 1),
(2048, 0, 5, 10, '75', 1),
(2049, 0, 0, 10, '', 0),
(2050, 0, 5, 10, '30', 1),
(2051, 0, 3, 10, '20', 1),
(2052, 0, 1, 10, '100', 1),
(2053, 0, 0, 10, '', 1),
(2054, 0, 2, 10, '100', 0),
(2055, 6, 0, 10, '', 1),
(2056, 13, 0, 10, '', 1),
(2057, 14, 0, 10, '', 1),
(2058, 0, 0, 10, '', 1),
(2059, 0, 0, 10, '', 1),
(2060, 15, 2, 10, '0', 1),
(2061, 16, 0, 10, '', 0),
(2062, 16, 5, 10, '20', 1),
(2063, 16, 3, 10, '20', 1),
(2064, 16, 1, 10, '20', 1),
(2065, 17, 0, 10, '', 1),
(2066, 19, 0, 10, '', 1),
(2067, 16, 2, 10, '0', 1),
(2068, 16, 4, 10, '0', 1),
(2069, 20, 0, 10, '', 1),
(2070, 21, 0, 10, '', 0),
(2071, 4, 0, 10, '', 1),
(2072, 3, 0, 10, '', 1),
(2073, 7, 0, 10, '', 1),
(2074, 1, 4, 10, '60', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propietarios_menu_ingredientes`
--

CREATE TABLE `propietarios_menu_ingredientes` (
  `id` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_ingrediente` int(11) NOT NULL,
  `costo_extra` text NOT NULL DEFAULT 'No',
  `cantidad_gratis` int(11) NOT NULL,
  `precio` text NOT NULL DEFAULT '0',
  `id_cafeteria` int(11) NOT NULL,
  `id_propietario` int(11) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `propietarios_menu_ingredientes`
--

INSERT INTO `propietarios_menu_ingredientes` (`id`, `id_producto`, `id_ingrediente`, `costo_extra`, `cantidad_gratis`, `precio`, `id_cafeteria`, `id_propietario`, `estado`) VALUES
(1, 16, 1, 'No', 1, '15', 0, 4, 1),
(2, 16, 3, 'Si', 2, '12', 0, 4, 1),
(3, 16, 5, 'Si', 0, '0', 0, 4, 0),
(4, 17, 12, 'Si', 0, '10', 0, 4, 1),
(8, 16, 2, 'No', 0, '0', 0, 4, 0),
(9, 16, 11, 'Si', 0, '10', 0, 4, 1),
(10, 16, 12, 'No', 0, '0', 0, 4, 1),
(11, 16, 9, 'No', 0, '0', 0, 4, 1),
(12, 16, 10, 'No', 0, '0', 0, 4, 1),
(13, 1, 2, 'Si', 10, '10', 0, 5, 1),
(14, 15, 1, 'Si', 0, '0', 0, 5, 1),
(15, 1, 1, 'Si', 1, '12', 0, 5, 1),
(16, 15, 2, 'Si', 0, '10', 0, 5, 1),
(17, 15, 3, 'Si', 1, '13', 0, 5, 1),
(18, 1, 3, 'Si', 0, '13', 0, 5, 1),
(19, 1, 1, 'No', 0, '15', 0, 4, 1),
(20, 1, 2, 'No', 0, '10', 0, 4, 1),
(21, 1, 14, 'Si', 1, '10', 0, 4, 0),
(22, 1, 16, 'No', 1, '10', 0, 4, 0),
(101, 16, 14, 'Si', 0, '10', 0, 4, 1),
(200, 21, 1, 'No', 1, '15', 0, 4, 1),
(201, 21, 2, 'No', 0, '10', 0, 4, 1),
(202, 19, 1, 'Si', 2, '15', 0, 4, 1),
(203, 19, 2, 'Si', 0, '10', 0, 4, 1),
(204, 19, 14, 'No', 0, '10', 0, 4, 1),
(205, 1, 3, 'No', 0, '12', 0, 4, 1),
(206, 1, 4, 'No', 0, '0', 0, 4, 1),
(207, 1, 5, 'Si', 0, '0', 0, 4, 1),
(208, 1, 6, 'Si', 0, '0', 0, 4, 1),
(209, 1, 7, 'No', 0, '0', 0, 4, 0),
(210, 1, 8, 'No', 0, '0', 0, 4, 0),
(211, 1, 9, 'Si', 0, '10', 0, 4, 1),
(212, 1, 10, 'Si', 0, '10', 0, 4, 1),
(213, 7, 10, 'No', 0, '0', 0, 4, 0),
(214, 7, 9, 'No', 0, '0', 0, 4, 0),
(215, 7, 12, 'No', 0, '0', 0, 4, 0),
(216, 7, 11, 'No', 0, '10', 0, 4, 0),
(230, 6, 1, 'Si', 0, '15', 0, 4, 0),
(232, 5, 1, 'No', 0, '15', 0, 4, 1),
(233, 5, 2, 'No', 0, '10', 0, 4, 1),
(234, 5, 3, 'No', 0, '12', 0, 4, 1),
(235, 5, 4, 'No', 0, '0', 0, 4, 1),
(236, 5, 5, 'Si', 0, '0', 0, 4, 1),
(237, 5, 6, 'Si', 0, '0', 0, 4, 1),
(238, 5, 11, 'Si', 0, '10', 0, 4, 1),
(239, 5, 12, 'Si', 0, '10', 0, 4, 1),
(240, 5, 9, 'No', 0, '0', 0, 4, 0),
(323, 1, 12, 'Si', 0, '10', 0, 4, 1),
(492, 1, 23, 'No', 0, '0', 0, 4, 1),
(493, 1, 11, 'Si', 0, '10', 0, 4, 1),
(758, 5, 23, 'No', 0, '0', 0, 4, 1),
(759, 9, 1, 'No', 0, '15', 0, 4, 1),
(760, 9, 2, 'No', 0, '10', 0, 4, 1),
(761, 9, 3, 'No', 0, '12', 0, 4, 1),
(762, 9, 4, 'No', 0, '0', 0, 4, 1),
(763, 9, 23, 'No', 0, '0', 0, 4, 1),
(764, 9, 5, 'Si', 0, '0', 0, 4, 1),
(765, 9, 6, 'Si', 0, '0', 0, 4, 1),
(766, 9, 9, 'No', 0, '10', 0, 4, 1),
(767, 9, 10, 'No', 0, '10', 0, 4, 1),
(768, 9, 11, 'Si', 0, '10', 0, 4, 1),
(769, 9, 12, 'Si', 0, '10', 0, 4, 1),
(770, 17, 1, 'No', 0, '15', 0, 4, 1),
(771, 17, 2, 'No', 0, '10', 0, 4, 1),
(772, 17, 3, 'No', 0, '12', 0, 4, 1),
(773, 17, 4, 'No', 0, '0', 0, 4, 1),
(774, 17, 23, 'No', 0, '0', 0, 4, 1),
(775, 17, 5, 'Si', 0, '0', 0, 4, 1),
(776, 17, 6, 'Si', 0, '0', 0, 4, 1),
(777, 17, 11, 'Si', 0, '10', 0, 4, 1),
(778, 10, 1, 'No', 0, '15', 0, 4, 1),
(779, 10, 2, 'No', 0, '10', 0, 4, 1),
(780, 10, 3, 'No', 0, '12', 0, 4, 1),
(781, 10, 4, 'No', 0, '0', 0, 4, 1),
(782, 10, 23, 'No', 0, '0', 0, 4, 1),
(783, 10, 5, 'No', 0, '0', 0, 4, 1),
(784, 10, 6, 'No', 0, '0', 0, 4, 1),
(785, 10, 11, 'Si', 0, '10', 0, 4, 1),
(786, 10, 12, 'Si', 0, '10', 0, 4, 1),
(787, 10, 9, 'Si', 0, '10', 0, 4, 1),
(788, 10, 10, 'Si', 0, '10', 0, 4, 1),
(789, 10, 7, 'No', 0, '0', 0, 4, 1),
(790, 10, 8, 'No', 0, '0', 0, 4, 1),
(791, 4, 11, 'Si', 0, '10', 0, 4, 1),
(792, 4, 12, 'Si', 0, '10', 0, 4, 1),
(793, 3, 11, 'Si', 0, '10', 0, 4, 1),
(794, 3, 12, 'Si', 0, '10', 0, 4, 1),
(795, 16, 1, 'No', 1, '15', 1, 0, 1),
(796, 16, 3, 'Si', 2, '12', 1, 0, 1),
(797, 16, 5, 'Si', 0, '0', 1, 0, 0),
(798, 17, 12, 'Si', 0, '10', 1, 0, 1),
(799, 16, 2, 'No', 0, '0', 1, 0, 0),
(800, 16, 11, 'Si', 0, '10', 1, 0, 1),
(801, 16, 12, 'No', 0, '0', 1, 0, 1),
(802, 16, 9, 'No', 0, '0', 1, 0, 1),
(803, 16, 10, 'No', 0, '0', 1, 0, 1),
(804, 1, 1, 'No', 0, '15', 1, 0, 1),
(805, 1, 2, 'No', 0, '10', 1, 0, 1),
(806, 1, 14, 'Si', 1, '10', 1, 0, 0),
(807, 1, 16, 'No', 1, '10', 1, 0, 0),
(808, 16, 14, 'Si', 0, '10', 1, 0, 1),
(809, 21, 1, 'No', 1, '15', 1, 0, 1),
(810, 21, 2, 'No', 0, '10', 1, 0, 1),
(811, 19, 1, 'Si', 2, '15', 1, 0, 1),
(812, 19, 2, 'Si', 0, '10', 1, 0, 1),
(813, 19, 14, 'No', 0, '10', 1, 0, 1),
(814, 1, 3, 'No', 0, '12', 1, 0, 1),
(815, 1, 4, 'No', 0, '0', 1, 0, 1),
(816, 1, 5, 'Si', 0, '0', 1, 0, 1),
(817, 1, 6, 'Si', 0, '0', 1, 0, 1),
(818, 1, 7, 'No', 0, '0', 1, 0, 0),
(819, 1, 8, 'No', 0, '0', 1, 0, 0),
(820, 1, 9, 'Si', 0, '10', 1, 0, 1),
(821, 1, 10, 'Si', 0, '10', 1, 0, 1),
(822, 7, 10, 'No', 0, '0', 1, 0, 0),
(823, 7, 9, 'No', 0, '0', 1, 0, 0),
(824, 7, 12, 'No', 0, '0', 1, 0, 0),
(825, 7, 11, 'No', 0, '10', 1, 0, 0),
(826, 6, 1, 'Si', 0, '15', 1, 0, 0),
(827, 5, 1, 'No', 0, '15', 1, 0, 1),
(828, 5, 2, 'No', 0, '10', 1, 0, 1),
(829, 5, 3, 'No', 0, '12', 1, 0, 1),
(830, 5, 4, 'No', 0, '0', 1, 0, 1),
(831, 5, 5, 'Si', 0, '0', 1, 0, 1),
(832, 5, 6, 'Si', 0, '0', 1, 0, 1),
(833, 5, 11, 'Si', 0, '10', 1, 0, 1),
(834, 5, 12, 'Si', 0, '10', 1, 0, 1),
(835, 5, 9, 'No', 0, '0', 1, 0, 0),
(836, 1, 12, 'Si', 0, '10', 1, 0, 1),
(837, 1, 23, 'No', 0, '0', 1, 0, 1),
(838, 1, 11, 'Si', 0, '10', 1, 0, 1),
(839, 5, 23, 'No', 0, '0', 1, 0, 1),
(840, 9, 1, 'No', 0, '15', 1, 0, 1),
(841, 9, 2, 'No', 0, '10', 1, 0, 1),
(842, 9, 3, 'No', 0, '12', 1, 0, 1),
(843, 9, 4, 'No', 0, '0', 1, 0, 1),
(844, 9, 23, 'No', 0, '0', 1, 0, 1),
(845, 9, 5, 'Si', 0, '0', 1, 0, 1),
(846, 9, 6, 'Si', 0, '0', 1, 0, 1),
(847, 9, 9, 'No', 0, '10', 1, 0, 1),
(848, 9, 10, 'No', 0, '10', 1, 0, 1),
(849, 9, 11, 'Si', 0, '10', 1, 0, 1),
(850, 9, 12, 'Si', 0, '10', 1, 0, 1),
(851, 17, 1, 'No', 0, '15', 1, 0, 1),
(852, 17, 2, 'No', 0, '10', 1, 0, 1),
(853, 17, 3, 'No', 0, '12', 1, 0, 1),
(854, 17, 4, 'No', 0, '0', 1, 0, 1),
(855, 17, 23, 'No', 0, '0', 1, 0, 1),
(856, 17, 5, 'Si', 0, '0', 1, 0, 1),
(857, 17, 6, 'Si', 0, '0', 1, 0, 1),
(858, 17, 11, 'Si', 0, '10', 1, 0, 1),
(859, 10, 1, 'No', 0, '15', 1, 0, 1),
(860, 10, 2, 'No', 0, '10', 1, 0, 1),
(861, 10, 3, 'No', 0, '12', 1, 0, 1),
(862, 10, 4, 'No', 0, '0', 1, 0, 1),
(863, 10, 23, 'No', 0, '0', 1, 0, 1),
(864, 10, 5, 'No', 0, '0', 1, 0, 1),
(865, 10, 6, 'No', 0, '0', 1, 0, 1),
(866, 10, 11, 'Si', 0, '10', 1, 0, 1),
(867, 10, 12, 'Si', 0, '10', 1, 0, 1),
(868, 10, 9, 'Si', 0, '10', 1, 0, 1),
(869, 10, 10, 'Si', 0, '10', 1, 0, 1),
(870, 10, 7, 'No', 0, '0', 1, 0, 1),
(871, 10, 8, 'No', 0, '0', 1, 0, 1),
(872, 4, 11, 'Si', 0, '10', 1, 0, 1),
(873, 4, 12, 'Si', 0, '10', 1, 0, 1),
(874, 3, 11, 'Si', 0, '10', 1, 0, 1),
(875, 3, 12, 'Si', 0, '10', 1, 0, 1),
(876, 16, 1, 'No', 1, '15', 10, 0, 1),
(877, 16, 3, 'Si', 2, '12', 10, 0, 1),
(878, 16, 5, 'Si', 0, '0', 10, 0, 0),
(879, 17, 12, 'Si', 0, '10', 10, 0, 1),
(880, 16, 2, 'No', 0, '0', 10, 0, 0),
(881, 16, 11, 'Si', 0, '10', 10, 0, 1),
(882, 16, 12, 'No', 0, '0', 10, 0, 1),
(883, 16, 9, 'No', 0, '0', 10, 0, 1),
(884, 16, 10, 'No', 0, '0', 10, 0, 1),
(885, 1, 1, 'No', 0, '15', 10, 0, 1),
(886, 1, 2, 'No', 0, '10', 10, 0, 1),
(887, 1, 14, 'Si', 1, '10', 10, 0, 0),
(888, 1, 16, 'No', 1, '10', 10, 0, 0),
(889, 16, 14, 'Si', 0, '10', 10, 0, 1),
(890, 21, 1, 'No', 1, '15', 10, 0, 1),
(891, 21, 2, 'No', 0, '10', 10, 0, 1),
(892, 19, 1, 'Si', 2, '15', 10, 0, 1),
(893, 19, 2, 'Si', 0, '10', 10, 0, 1),
(894, 19, 14, 'No', 0, '10', 10, 0, 1),
(895, 1, 3, 'No', 0, '12', 10, 0, 1),
(896, 1, 4, 'No', 0, '0', 10, 0, 1),
(897, 1, 5, 'Si', 0, '0', 10, 0, 1),
(898, 1, 6, 'Si', 0, '0', 10, 0, 1),
(899, 1, 7, 'No', 0, '0', 10, 0, 0),
(900, 1, 8, 'No', 0, '0', 10, 0, 0),
(901, 1, 9, 'Si', 0, '10', 10, 0, 1),
(902, 1, 10, 'Si', 0, '10', 10, 0, 1),
(903, 7, 10, 'No', 0, '0', 10, 0, 0),
(904, 7, 9, 'No', 0, '0', 10, 0, 0),
(905, 7, 12, 'No', 0, '0', 10, 0, 0),
(906, 7, 11, 'No', 0, '10', 10, 0, 0),
(907, 6, 1, 'Si', 0, '15', 10, 0, 0),
(908, 5, 1, 'No', 0, '15', 10, 0, 1),
(909, 5, 2, 'No', 0, '10', 10, 0, 1),
(910, 5, 3, 'No', 0, '12', 10, 0, 1),
(911, 5, 4, 'No', 0, '0', 10, 0, 1),
(912, 5, 5, 'Si', 0, '0', 10, 0, 1),
(913, 5, 6, 'Si', 0, '0', 10, 0, 1),
(914, 5, 11, 'Si', 0, '10', 10, 0, 1),
(915, 5, 12, 'Si', 0, '10', 10, 0, 1),
(916, 5, 9, 'No', 0, '0', 10, 0, 0),
(917, 1, 12, 'Si', 0, '10', 10, 0, 1),
(918, 1, 23, 'No', 0, '0', 10, 0, 1),
(919, 1, 11, 'Si', 0, '10', 10, 0, 1),
(920, 5, 23, 'No', 0, '0', 10, 0, 1),
(921, 9, 1, 'No', 0, '15', 10, 0, 1),
(922, 9, 2, 'No', 0, '10', 10, 0, 1),
(923, 9, 3, 'No', 0, '12', 10, 0, 1),
(924, 9, 4, 'No', 0, '0', 10, 0, 1),
(925, 9, 23, 'No', 0, '0', 10, 0, 1),
(926, 9, 5, 'Si', 0, '0', 10, 0, 1),
(927, 9, 6, 'Si', 0, '0', 10, 0, 1),
(928, 9, 9, 'No', 0, '10', 10, 0, 1),
(929, 9, 10, 'No', 0, '10', 10, 0, 1),
(930, 9, 11, 'Si', 0, '10', 10, 0, 1),
(931, 9, 12, 'Si', 0, '10', 10, 0, 1),
(932, 17, 1, 'No', 0, '15', 10, 0, 1),
(933, 17, 2, 'No', 0, '10', 10, 0, 1),
(934, 17, 3, 'No', 0, '12', 10, 0, 1),
(935, 17, 4, 'No', 0, '0', 10, 0, 1),
(936, 17, 23, 'No', 0, '0', 10, 0, 1),
(937, 17, 5, 'Si', 0, '0', 10, 0, 1),
(938, 17, 6, 'Si', 0, '0', 10, 0, 1),
(939, 17, 11, 'Si', 0, '10', 10, 0, 1),
(940, 10, 1, 'No', 0, '15', 10, 0, 1),
(941, 10, 2, 'No', 0, '10', 10, 0, 1),
(942, 10, 3, 'No', 0, '12', 10, 0, 1),
(943, 10, 4, 'No', 0, '0', 10, 0, 1),
(944, 10, 23, 'No', 0, '0', 10, 0, 1),
(945, 10, 5, 'No', 0, '0', 10, 0, 1),
(946, 10, 6, 'No', 0, '0', 10, 0, 1),
(947, 10, 11, 'Si', 0, '10', 10, 0, 1),
(948, 10, 12, 'Si', 0, '10', 10, 0, 1),
(949, 10, 9, 'Si', 0, '10', 10, 0, 1),
(950, 10, 10, 'Si', 0, '10', 10, 0, 1),
(951, 10, 7, 'No', 0, '0', 10, 0, 1),
(952, 10, 8, 'No', 0, '0', 10, 0, 1),
(953, 4, 11, 'Si', 0, '10', 10, 0, 1),
(954, 4, 12, 'Si', 0, '10', 10, 0, 1),
(955, 3, 11, 'Si', 0, '10', 10, 0, 1),
(956, 3, 12, 'Si', 0, '10', 10, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propietarios_menu_productos`
--

CREATE TABLE `propietarios_menu_productos` (
  `id` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_tamano` int(11) NOT NULL,
  `id_propietario` int(11) NOT NULL,
  `precio` text NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `propietarios_menu_productos`
--

INSERT INTO `propietarios_menu_productos` (`id`, `id_producto`, `id_tamano`, `id_propietario`, `precio`, `estado`) VALUES
(1, 1, 0, 14, '', 1),
(2, 15, 0, 14, '', 1),
(3, 5, 0, 14, '', 1),
(4, 10, 0, 14, '', 1),
(5, 0, 0, 14, '', 0),
(6, 0, 0, 14, '', 1),
(7, 0, 1, 14, '40', 0),
(8, 0, 3, 14, '45', 1),
(9, 0, 5, 14, '50', 1),
(10, 1, 1, 14, '60', 1),
(11, 1, 5, 14, '80', 1),
(12, 1, 3, 14, '70', 1),
(13, 0, 2, 14, '55', 1),
(14, 4, 0, 14, '', 1),
(15, 1, 0, 4, '', 1),
(16, 15, 0, 4, '', 0),
(17, 5, 0, 4, '', 1),
(18, 9, 0, 4, '', 1),
(19, 10, 0, 4, '', 1),
(20, 11, 0, 4, '', 1),
(21, 1, 5, 4, '70', 1),
(22, 1, 3, 4, '50', 1),
(23, 1, 1, 4, '10', 0),
(24, 15, 5, 4, '25', 1),
(25, 15, 3, 4, '20', 1),
(26, 15, 1, 4, '15', 1),
(27, 5, 5, 4, '60', 1),
(28, 5, 3, 4, '55', 1),
(29, 5, 1, 4, '50', 1),
(30, 9, 5, 4, '60', 1),
(31, 9, 3, 4, '55', 1),
(32, 9, 1, 4, '50', 1),
(33, 10, 5, 4, '65', 1),
(34, 10, 3, 4, '66', 1),
(35, 10, 1, 4, '55', 1),
(36, 11, 1, 4, '55', 1),
(37, 11, 3, 4, '60', 1),
(38, 11, 5, 4, '65', 1),
(39, 0, 0, 4, '', 1),
(40, 0, 1, 4, '60', 1),
(41, 0, 3, 4, '70', 1),
(42, 0, 5, 4, '75', 1),
(43, 0, 0, 4, '', 0),
(44, 0, 5, 4, '30', 1),
(45, 0, 3, 4, '20', 1),
(46, 0, 1, 4, '100', 1),
(47, 0, 0, 4, '', 1),
(48, 0, 2, 4, '100', 0),
(49, 6, 0, 4, '', 1),
(50, 13, 0, 4, '', 1),
(51, 14, 0, 4, '', 1),
(52, 0, 0, 4, '', 1),
(53, 0, 0, 4, '', 1),
(54, 15, 2, 4, '0', 1),
(55, 16, 0, 4, '', 0),
(56, 16, 5, 4, '20', 1),
(57, 16, 3, 4, '20', 1),
(58, 16, 1, 4, '20', 1),
(59, 17, 0, 4, '', 1),
(60, 19, 0, 4, '', 1),
(61, 16, 2, 4, '0', 1),
(62, 16, 4, 4, '0', 1),
(63, 20, 0, 4, '', 1),
(64, 21, 0, 4, '', 0),
(65, 1, 0, 5, '', 1),
(66, 15, 0, 5, '', 1),
(67, 4, 0, 4, '', 1),
(68, 3, 0, 4, '', 1),
(69, 7, 0, 4, '', 1),
(70, 1, 4, 4, '60', 1);

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
(13, 17, 4, 0),
(14, 3, 4, 0),
(15, 19, 4, 1),
(19, 2, 5, 1),
(20, 4, 5, 1),
(21, 10, 5, 1);

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
(11, 1, 0, 7, '2025-11-24 18:59:21');

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
(19, 11, 1, 3, 2, 0, '2025-11-24 19:40:04'),
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
(31, 11, 1, 1, 3, 0, '2025-11-24 22:39:42'),
(32, 11, 1, 1, 1, 2, '2025-11-24 22:45:54');

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
(18, 17, 4, 1, 0, '2025-11-24 18:59:21'),
(19, 17, 5, 1, 0, '2025-11-24 18:59:21'),
(20, 17, 7, 1, 0, '2025-11-24 18:59:21'),
(21, 17, 1, 1, 0, '2025-11-24 18:59:21'),
(22, 17, 2, 1, 0, '2025-11-24 18:59:21'),
(23, 17, 9, 1, 0, '2025-11-24 18:59:21'),
(24, 17, 12, 1, 0, '2025-11-24 18:59:21'),
(25, 19, 4, 1, 0, '2025-11-24 19:40:04'),
(26, 19, 6, 1, 0, '2025-11-24 19:40:04'),
(27, 19, 1, 1, 0, '2025-11-24 19:40:04'),
(28, 20, 1, 1, 0, '2025-11-24 20:24:36'),
(29, 20, 3, 1, 0, '2025-11-24 20:24:36'),
(30, 21, 3, 1, 0, '2025-11-24 20:24:54'),
(31, 21, 5, 1, 0, '2025-11-24 20:24:54'),
(32, 21, 8, 1, 0, '2025-11-24 20:24:54'),
(33, 21, 10, 1, 0, '2025-11-24 20:24:54'),
(34, 21, 1, 1, 0, '2025-11-24 20:24:54'),
(35, 23, 14, 1, 0, '2025-11-24 21:00:46'),
(36, 23, 1, 1, 0, '2025-11-24 21:00:46'),
(37, 23, 2, 1, 0, '2025-11-24 21:00:46'),
(38, 24, 4, 1, 0, '2025-11-24 21:29:06'),
(39, 24, 5, 1, 0, '2025-11-24 21:29:06'),
(40, 24, 1, 1, 0, '2025-11-24 21:29:06'),
(41, 25, 4, 1, 0, '2025-11-24 21:30:33'),
(42, 25, 6, 1, 0, '2025-11-24 21:30:33'),
(43, 25, 1, 1, 0, '2025-11-24 21:30:33'),
(44, 26, 4, 1, 0, '2025-11-24 21:32:09'),
(45, 26, 6, 1, 0, '2025-11-24 21:32:09'),
(46, 26, 1, 1, 0, '2025-11-24 21:32:09'),
(47, 27, 4, 1, 0, '2025-11-24 21:35:36'),
(48, 27, 6, 1, 0, '2025-11-24 21:35:36'),
(49, 27, 1, 1, 0, '2025-11-24 21:35:36'),
(50, 28, 4, 1, 0, '2025-11-24 21:53:41'),
(51, 28, 6, 1, 0, '2025-11-24 21:53:41'),
(52, 28, 1, 1, 0, '2025-11-24 21:53:41'),
(53, 29, 4, 1, 0, '2025-11-24 21:55:32'),
(54, 29, 6, 1, 0, '2025-11-24 21:55:32'),
(55, 29, 1, 1, 0, '2025-11-24 21:55:32'),
(56, 30, 4, 1, 0, '2025-11-24 21:58:18'),
(57, 30, 6, 1, 0, '2025-11-24 21:58:18'),
(58, 30, 1, 1, 0, '2025-11-24 21:58:18'),
(59, 31, 4, 1, 0, '2025-11-24 22:39:42'),
(60, 31, 6, 1, 0, '2025-11-24 22:39:42'),
(61, 31, 1, 1, 0, '2025-11-24 22:39:42'),
(62, 32, 4, 1, 0, '2025-11-24 22:45:54'),
(63, 32, 5, 1, 0, '2025-11-24 22:45:54'),
(64, 32, 1, 1, 0, '2025-11-24 22:45:54');

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
-- Indices de la tabla `propietarios_menu_ingredientes`
--
ALTER TABLE `propietarios_menu_ingredientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `propietarios_menu_productos`
--
ALTER TABLE `propietarios_menu_productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `propietarios_menu_subcategorias`
--
ALTER TABLE `propietarios_menu_subcategorias`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `menu_ingredientes`
--
ALTER TABLE `menu_ingredientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `menu_ingredientes_categorias`
--
ALTER TABLE `menu_ingredientes_categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `menu_productos`
--
ALTER TABLE `menu_productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `menu_productos_tamanos`
--
ALTER TABLE `menu_productos_tamanos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `menu_subcategorias`
--
ALTER TABLE `menu_subcategorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
-- AUTO_INCREMENT de la tabla `propietarios_menu_cafeterias`
--
ALTER TABLE `propietarios_menu_cafeterias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2075;

--
-- AUTO_INCREMENT de la tabla `propietarios_menu_ingredientes`
--
ALTER TABLE `propietarios_menu_ingredientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=957;

--
-- AUTO_INCREMENT de la tabla `propietarios_menu_productos`
--
ALTER TABLE `propietarios_menu_productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de la tabla `propietarios_menu_subcategorias`
--
ALTER TABLE `propietarios_menu_subcategorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ventas_carrito`
--
ALTER TABLE `ventas_carrito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `ventas_carrito_items`
--
ALTER TABLE `ventas_carrito_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `ventas_carrito_items_ingredientes`
--
ALTER TABLE `ventas_carrito_items_ingredientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de la tabla `ventas_items`
--
ALTER TABLE `ventas_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ventas_items_ingredientes`
--
ALTER TABLE `ventas_items_ingredientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
