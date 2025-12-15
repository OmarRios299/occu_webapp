-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-12-2025 a las 05:34:58
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
(25, 1, 18),
(26, 2, 19);

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
  `pin` text NOT NULL,
  `verificado` text NOT NULL,
  `token_sesion` text NOT NULL,
  `estado` int(11) NOT NULL,
  `id_alta` int(11) NOT NULL,
  `fecha_alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `admin_usuarios`
--

INSERT INTO `admin_usuarios` (`id`, `nombre`, `apellido`, `id_ciudad`, `correo_electronico`, `contrasena`, `nivel`, `telefono`, `imagen`, `pin`, `verificado`, `token_sesion`, `estado`, `id_alta`, `fecha_alta`) VALUES
(1, 'Omar', 'Rios', 1, 'admin@admin.com', '$2a$07$asxx54ahjppf45sd87a5auXBm1Vr2M1NV5t/zNQtGHGpS5fFirrbG', 'Administrador', '6865706609', 'views/assets/img/admin_usuarios/664_imagen_usuario_1.webp', '261857', 'Si', '693ceae606c72', 0, 0, '2022-07-21 00:28:03'),
(2, 'Omar', 'Rios', 0, 'user@user.com', '$2a$07$asxx54ahjppf45sd87a5auXBm1Vr2M1NV5t/zNQtGHGpS5fFirrbG', 'Propietario', '', 'views/assets/img/usuario_default.png', '', 'Si', '', 0, 0, '2024-10-19 12:43:37'),
(3, 'Ian', 'Arvizu', 1, 'frat@gmail.com', '$2a$07$asxx54ahjppf45sd87a5aufYGGivbsnLy93A5O7siTeN420NWHjKy', 'Administrador', '', 'views/assets/img/usuario_default.png', '809560', 'Si', '693108c220639', 0, 0, '2024-10-19 12:44:42'),
(4, 'IANMARCUS', 'ARVIZU', 0, 'IANA@gmail.com', '$2a$07$asxx54ahjppf45sd87a5aufYGGivbsnLy93A5O7siTeN420NWHjKy', 'Cliente', '', 'views/assets/img/usuario_default.png', '223139', 'Si', '68167213cb562', 0, 0, '2025-05-03 12:43:30'),
(5, 'Elieth', 'Viramontes', 0, 'eli@gmail.com', '$2a$07$asxx54ahjppf45sd87a5auI5z/cXoLaZ/jqxFYzg1/3kheSqyITm.', 'Cliente', '', 'views/assets/img/usuario_default.png', '862886', 'Si', '681672c6b45e8', 0, 0, '2025-05-03 12:46:49');

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
(1, 1, 'Descafeinado', 1, 0, 1, '2025-11-26 23:13:58'),
(2, 1, 'Regular', 1, 0, 1, '2025-11-26 23:13:58'),
(3, 1, 'Especialidad', 1, 0, 1, '2025-11-26 23:13:58'),
(4, 2, 'Leche Entera', 1, 0, 1, '2025-11-26 23:13:58'),
(5, 2, 'Leche Light', 1, 0, 1, '2025-11-26 23:13:58'),
(6, 2, 'Leche Deslactosada', 1, 0, 1, '2025-11-26 23:13:58'),
(7, 2, 'Leche de Almendra', 1, 0, 1, '2025-11-26 23:13:58'),
(8, 2, 'Leche de Coco', 1, 0, 1, '2025-11-26 23:13:58'),
(9, 2, 'Leche de Soya', 1, 0, 1, '2025-11-26 23:13:58'),
(10, 2, 'Leche de Avena', 1, 0, 1, '2025-11-26 23:13:58'),
(11, 3, 'Azúcar Blanca', 1, 0, 1, '2025-11-26 23:13:58'),
(12, 3, 'Azúcar Morena', 1, 0, 1, '2025-11-26 23:13:58'),
(13, 3, 'Miel', 1, 0, 1, '2025-11-26 23:13:58'),
(14, 3, 'Jarabe Simple', 1, 0, 1, '2025-11-26 23:13:58'),
(15, 3, 'Stevia', 1, 0, 1, '2025-11-26 23:13:58'),
(16, 4, 'Vainilla', 1, 0, 1, '2025-11-26 23:13:58'),
(17, 4, 'Avellana', 1, 0, 1, '2025-11-26 23:13:58'),
(18, 4, 'Caramelo', 1, 0, 1, '2025-11-26 23:13:58'),
(19, 4, 'Chocolate Blanco', 1, 0, 1, '2025-11-26 23:13:58'),
(20, 4, 'Menta', 1, 0, 1, '2025-11-26 23:13:58'),
(21, 4, 'Chai', 1, 0, 1, '2025-11-26 23:13:58'),
(22, 4, 'Matcha', 1, 0, 1, '2025-11-26 23:13:58'),
(23, 4, 'Almendra Dulce', 1, 0, 1, '2025-11-26 23:13:58'),
(24, 5, 'Crema Batida', 1, 0, 1, '2025-11-26 23:13:58'),
(25, 5, 'Canela', 1, 0, 1, '2025-11-26 23:13:58'),
(26, 5, 'Cocoa en Polvo', 1, 0, 1, '2025-11-26 23:13:58'),
(27, 5, 'Chispas de Chocolate', 1, 0, 1, '2025-11-26 23:13:58'),
(28, 5, 'Caramelo Líquido', 1, 0, 1, '2025-11-26 23:13:58'),
(29, 5, 'Chocolate Líquido', 1, 0, 1, '2025-11-26 23:13:58'),
(30, 6, 'Base de Café', 1, 2, 1, '2025-11-26 23:13:58'),
(31, 6, 'Base de Vainilla', 1, 2, 1, '2025-11-26 23:13:58'),
(32, 6, 'Base de Chocolate', 1, 2, 1, '2025-11-26 23:13:58'),
(33, 6, 'Base Natural Smoothie', 1, 2, 1, '2025-11-26 23:13:58'),
(34, 10, 'Baguette', 1, 0, 1, '2025-11-26 23:13:58'),
(35, 10, 'Pan Integral', 1, 0, 1, '2025-11-26 23:13:58'),
(36, 10, 'Bolillo', 1, 0, 1, '2025-11-26 23:13:58'),
(37, 10, 'Croissant', 1, 0, 1, '2025-11-26 23:13:58'),
(38, 11, 'Pollo', 1, 0, 1, '2025-11-26 23:13:58'),
(39, 11, 'Pavo', 1, 0, 1, '2025-11-26 23:13:58'),
(40, 11, 'Jamón', 1, 0, 1, '2025-11-26 23:13:58'),
(41, 11, 'Atún', 1, 0, 1, '2025-11-26 23:13:58'),
(42, 11, 'Huevo', 1, 0, 1, '2025-11-26 23:13:58'),
(43, 12, 'Lechuga', 1, 0, 1, '2025-11-26 23:13:58'),
(44, 12, 'Tomate', 1, 0, 1, '2025-11-26 23:13:58'),
(45, 12, 'Cebolla', 1, 0, 1, '2025-11-26 23:13:58'),
(46, 12, 'Espinaca', 1, 0, 1, '2025-11-26 23:13:58'),
(47, 12, 'Pepino', 1, 0, 1, '2025-11-26 23:13:58'),
(48, 13, 'Mayonesa', 1, 0, 1, '2025-11-26 23:13:58'),
(49, 13, 'Aderezo Ranch', 1, 0, 1, '2025-11-26 23:13:58'),
(50, 13, 'Aderezo Chipotle', 1, 0, 1, '2025-11-26 23:13:58'),
(51, 13, 'Mostaza', 1, 0, 1, '2025-11-26 23:13:58'),
(52, 14, 'Queso Cheddar', 1, 0, 1, '2025-11-26 23:13:58'),
(53, 14, 'Queso Panela', 1, 0, 1, '2025-11-26 23:13:58'),
(54, 14, 'Queso Manchego', 1, 0, 1, '2025-11-26 23:13:58'),
(55, 14, 'Queso Mozzarella', 1, 0, 1, '2025-11-26 23:13:58'),
(56, 15, 'Pesto', 1, 0, 1, '2025-11-26 23:13:58'),
(57, 15, 'Aceitunas', 1, 0, 1, '2025-11-26 23:13:58'),
(58, 15, 'Pepperoni', 1, 0, 1, '2025-11-26 23:13:58'),
(59, 15, 'Aguacate', 1, 0, 1, '2025-11-26 23:13:58'),
(60, 16, 'Masa de Hojaldre', 1, 0, 1, '2025-11-26 23:13:58'),
(61, 16, 'Masa de Pan Dulce', 1, 0, 1, '2025-11-26 23:13:58'),
(62, 16, 'Masa de Galleta', 1, 0, 1, '2025-11-26 23:13:58'),
(63, 17, 'Chocolate', 1, 0, 1, '2025-11-26 23:13:58'),
(64, 17, 'Queso Crema', 1, 0, 1, '2025-11-26 23:13:58'),
(65, 17, 'Cajeta', 1, 0, 1, '2025-11-26 23:13:58'),
(66, 17, 'Fresa', 1, 0, 1, '2025-11-26 23:13:58'),
(67, 18, 'Azúcar Glass', 1, 0, 1, '2025-11-26 23:13:58');

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
(23, 'Especialidades', '', 'Si', 0, 1, '2025-11-26 23:11:23');

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
(13, 'Té Negro Frío', 2, 'views/assets/img/menu_productos/13_imagen.webp', 1, 2, 1, '2025-11-26 23:20:13'),
(14, 'Frappe de Café', 3, 'views/assets/img/menu_productos/14_imagen.webp', 1, 0, 1, '2025-11-26 23:20:14'),
(15, 'Frappe de Oreo', 3, 'views/assets/img/menu_productos/15_imagen.webp', 1, 0, 1, '2025-11-26 23:20:15'),
(16, 'Frappe de Matcha', 3, 'views/assets/img/menu_productos/16_imagen.webp', 1, 0, 1, '2025-11-26 23:20:16'),
(17, 'Cold Brew', 2, 'views/assets/img/menu_productos/17_imagen.webp', 1, 0, 1, '2025-11-26 23:20:17'),
(18, 'Limonada Natural', 5, 'views/assets/img/menu_productos/21_imagen.webp', 1, 0, 1, '2025-11-26 23:20:21'),
(19, 'Naranjada', 5, 'views/assets/img/menu_productos/22_imagen.webp', 1, 0, 1, '2025-11-26 23:20:22'),
(20, 'Huevos al Gusto', 6, 'views/assets/img/menu_productos/23_imagen.webp', 1, 0, 1, '2025-11-26 23:20:23'),
(21, 'Hot Cakes', 6, 'views/assets/img/menu_productos/24_imagen.webp', 1, 0, 1, '2025-11-26 23:20:24'),
(22, 'Helado de Vainilla', 19, 'views/assets/img/menu_productos/50_imagen.webp', 1, 0, 1, '2025-11-26 22:56:49'),
(23, 'Helado de Chocolate', 19, 'views/assets/img/menu_productos/51_imagen.webp', 1, 0, 1, '2025-11-26 22:56:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu_productos_bases`
--

CREATE TABLE `menu_productos_bases` (
  `id` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_ingrediente_categoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

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
(1, 'Cafés Calientes', 1, 0, 1, 0, '2025-11-26 23:00:01'),
(2, 'Cafés Fríos', 1, 0, 1, 0, '2025-11-26 23:00:02'),
(3, 'Frappés', 1, 0, 1, 0, '2025-11-26 23:00:03'),
(4, 'Jugos & Smoothies', 1, 0, 1, 0, '2025-11-26 23:00:05'),
(5, 'Aguas Frescas & Limonadas', 1, 0, 1, 0, '2025-11-26 23:00:06'),
(6, 'Desayunos', 2, 0, 1, 0, '2025-11-26 23:00:07'),
(7, 'Sandwiches', 2, 0, 1, 0, '2025-11-26 23:00:08'),
(8, 'Paninis', 2, 0, 1, 0, '2025-11-26 23:00:09'),
(9, 'Wraps & Bagels', 2, 0, 1, 0, '2025-11-26 23:00:10'),
(10, 'Ensaladas', 2, 0, 1, 0, '2025-11-26 23:00:11'),
(11, 'Postres', 3, 0, 1, 0, '2025-11-26 23:00:18'),
(12, 'Pan', 3, 0, 1, 0, '2025-11-26 23:00:14'),
(13, 'Pan Dulce', 3, 0, 1, 0, '2025-11-26 23:00:15'),
(14, 'Pan Salado', 3, 0, 1, 0, '2025-11-26 23:00:16'),
(15, 'Pasteles', 3, 0, 1, 0, '2025-11-26 23:00:17'),
(16, 'Galletas', 3, 0, 1, 0, '2025-11-26 23:00:19'),
(17, 'Muffins & Cupcakes', 3, 0, 1, 0, '2025-11-26 23:00:20'),
(18, 'Brownies', 3, 0, 1, 0, '2025-11-26 23:00:21'),
(19, 'Helados / Postres Fríos', 3, 0, 1, 0, '2025-11-26 23:00:22'),
(20, 'Botanas', 4, 0, 1, 0, '2025-11-26 23:00:23'),
(21, 'Fruta & Mix de Nueces', 4, 0, 1, 0, '2025-11-26 23:00:24'),
(22, 'Papas & Nachos', 4, 0, 1, 0, '2025-11-26 23:00:25'),
(23, 'Extras & Toppings', 4, 0, 1, 0, '2025-11-26 23:00:26'),
(24, 'Café en Grano', 5, 0, 1, 0, '2025-11-26 23:00:27'),
(25, 'Té en Hoja', 5, 0, 1, 0, '2025-11-26 23:00:28'),
(26, 'Merchandising / Accesorios', 5, 0, 1, 0, '2025-11-26 23:00:29'),
(27, 'Bebidas de Temporada', 6, 0, 1, 0, '2025-11-26 23:00:30'),
(28, 'Comida de Temporada', 6, 0, 1, 0, '2025-11-26 23:00:31');

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
(1, 'carousel', ' ¡Comprar café nunca ha sido tan fácil! ', 'views/assets/img/admin_usuarios/218_imagen_usuario_1.webp', 'Encuentra la cafetería ideal para compartir', 'cafeterias_lista', 'Cafeterías', 0),
(2, 'card', '#Cafedegrano', 'views/assets/img/admin_usuarios/178_imagen_usuario_2.webp', 'Encuentra las mejores cafeterías de la ciudad', 'cafeterias_lista', '', 0),
(3, '', 'Café 100% méxicano', 'views/assets/img/admin_usuarios/830_imagen_usuario_3.webp', '#cafemexico', '', '', 0),
(4, 'card', '#cafemexico', 'views/assets/img/admin_usuarios/396_imagen_usuario_4.webp', 'Café 100% mexicano', '', '', 0),
(5, 'card', '#artelatte', 'views/assets/img/admin_usuarios/852_imagen_usuario_5.webp', 'El café como te gusta', '', '', 0),
(6, 'carousel', 'Donde encuentras a los mejores baristas', 'views/assets/img/admin_usuarios/505_imagen_usuario_6.webp', 'El café preparado por las mejores manos de méxico', 'cafeterias_mapa', 'Ver ubicaciones', 0);

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
(18, 2, 0, 'Categorías de ingredientes', 'menu_ingredientes_categorias', '', 0),
(19, 2, 0, 'Pedidos del día', 'cafeteria_pedidos', '', 0);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `admin_usuarios`
--
ALTER TABLE `admin_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `cafeterias`
--
ALTER TABLE `cafeterias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cafeterias_comentarios`
--
ALTER TABLE `cafeterias_comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cafeterias_imagenes`
--
ALTER TABLE `cafeterias_imagenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cafeterias_servicios`
--
ALTER TABLE `cafeterias_servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cafeteria_horarios`
--
ALTER TABLE `cafeteria_horarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT de la tabla `menu_ingredientes_categorias`
--
ALTER TABLE `menu_ingredientes_categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `menu_productos`
--
ALTER TABLE `menu_productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `menu_productos_bases`
--
ALTER TABLE `menu_productos_bases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `menu_productos_tamanos`
--
ALTER TABLE `menu_productos_tamanos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `menu_subcategorias`
--
ALTER TABLE `menu_subcategorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `pagina_inicial`
--
ALTER TABLE `pagina_inicial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ventas_carrito`
--
ALTER TABLE `ventas_carrito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ventas_carrito_items`
--
ALTER TABLE `ventas_carrito_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ventas_carrito_items_ingredientes`
--
ALTER TABLE `ventas_carrito_items_ingredientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
