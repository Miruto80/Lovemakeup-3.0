-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-08-2026 a las 21:27:10
-- Versión del servidor: 10.4.32-MariaDB-log
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `lovemakeupbds2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora`
--

CREATE TABLE `bitacora` (
  `id_bitacora` int(11) NOT NULL,
  `cedula` int(11) NOT NULL,
  `accion` varchar(100) NOT NULL,
  `descripcion` varchar(250) DEFAULT NULL,
  `fecha_hora` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `bitacora`
--

INSERT INTO `bitacora` (`id_bitacora`, `cedula`, `accion`, `descripcion`, `fecha_hora`) VALUES
(377, 10200300, 'ACCESO A MÓDULO', 'Usuario accedió al módulo de Bitácora [Bitácora]', '2026-08-26 21:30:23'),
(378, 10200300, 'Acceso a Módulo Cliente', 'Id_persona: 10200300 | Accion: Acceso a Módulo Cliente | Descripcion: Entro al módulo de Cliente [Cliente]', '2026-08-26 21:35:48'),
(379, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Ventas [Salida]', '2026-08-26 21:35:51'),
(380, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Producto [Producto]', '2026-08-26 21:36:03'),
(381, 10200300, 'Acceso a Marcas', 'Id_persona: 10200300 | Accion: Acceso a Marcas | Descripcion: Usuario accedió al módulo Marca [Marca]', '2026-08-26 21:36:05'),
(382, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Marca [Marca]', '2026-08-26 21:36:05'),
(383, 10200300, 'Acceso a Categorías', 'Id_persona: 10200300 | Accion: Acceso a Categorías | Descripcion: Administrador accedió al módulo Categoría [Categoria]', '2026-08-26 21:36:06'),
(384, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Categoria [Categoria]', '2026-08-26 21:36:07'),
(385, 10200300, 'Acceso a Proveedores', 'Id_persona: 10200300 | Accion: Acceso a Proveedores | Descripcion: Administrador accedió al módulo Proveedores [Proveedor]', '2026-08-26 21:36:07'),
(386, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Proveedor [Proveedor]', '2026-08-26 21:36:07'),
(387, 10200300, 'Acceso a Módulo Cliente', 'Id_persona: 10200300 | Accion: Acceso a Módulo Cliente | Descripcion: Entro al módulo de Cliente [Cliente]', '2026-08-26 21:36:16'),
(388, 10200300, 'Acceso a Delivery', 'Id_persona: 10200300 | Accion: Acceso a Delivery | Descripcion: Administrador accedió al módulo Delivery [Delivery]', '2026-08-26 21:36:20'),
(389, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Delivery [Delivery]', '2026-08-26 21:36:20'),
(390, 10200300, 'Acceso a Módulo Tasa Cambio', 'Id_persona: 10200300 | Accion: Acceso a Módulo Tasa Cambio | Descripcion: Entro al módulo de Tasa Cambio [Tasa de Cambio]', '2026-08-26 21:36:26'),
(391, 10200300, 'ACCESO A MÓDULO', 'Usuario accedió al módulo de Bitácora [Bitácora]', '2026-08-26 21:36:34'),
(392, 10200300, 'Acceso a Usuario', 'Id_persona: 10200300 | Accion: Acceso a Usuario | Descripcion: Entro al módulo de Usuario [Usuario]', '2026-08-26 21:36:38'),
(393, 10200300, 'Acceso a Módulo Cliente', 'Id_persona: 10200300 | Accion: Acceso a Módulo Cliente | Descripcion: Entro al módulo de Cliente [Cliente]', '2026-08-26 21:37:01'),
(394, 10200300, 'Acceso a Usuario', 'Id_persona: 10200300 | Accion: Acceso a Usuario | Descripcion: Entro al módulo de Usuario [Usuario]', '2026-08-26 21:37:05'),
(395, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Producto [Producto]', '2026-08-26 21:37:09'),
(396, 10200300, 'Modificación de producto', 'Id_persona: 10200300 | Accion: Modificación de producto | Descripcion: Se modificó el producto: pestana [Producto]', '2026-08-26 21:37:27'),
(397, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Producto [Producto]', '2026-08-26 21:37:28'),
(398, 10200300, 'Modificación de producto', 'Id_persona: 10200300 | Accion: Modificación de producto | Descripcion: Se modificó el producto: Lip glaze [Producto]', '2026-08-26 21:37:43'),
(399, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Producto [Producto]', '2026-08-26 21:37:44'),
(400, 10200300, 'Modificación de producto', 'Id_persona: 10200300 | Accion: Modificación de producto | Descripcion: Se modificó el producto: Lip oil rosado [Producto]', '2026-08-26 21:37:49'),
(401, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Producto [Producto]', '2026-08-26 21:37:50'),
(402, 10200300, 'Modificación de producto', 'Id_persona: 10200300 | Accion: Modificación de producto | Descripcion: Se modificó el producto: Lipgloss dije tacon [Producto]', '2026-08-26 21:37:56'),
(403, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Producto [Producto]', '2026-08-26 21:37:58'),
(404, 10200300, 'Modificación de producto', 'Id_persona: 10200300 | Accion: Modificación de producto | Descripcion: Se modificó el producto: Mascara big eyes [Producto]', '2026-08-26 21:38:02'),
(405, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Producto [Producto]', '2026-08-26 21:38:04'),
(406, 10200300, 'Modificación de producto', 'Id_persona: 10200300 | Accion: Modificación de producto | Descripcion: Se modificó el producto: Rizador de pestaña [Producto]', '2026-08-26 21:38:21'),
(407, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Producto [Producto]', '2026-08-26 21:38:22'),
(408, 10200300, 'Modificación de producto', 'Id_persona: 10200300 | Accion: Modificación de producto | Descripcion: Se modificó el producto: Máscara de pestañas [Producto]', '2026-08-26 21:38:33'),
(409, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Producto [Producto]', '2026-08-26 21:38:35'),
(410, 10200300, 'Registro de compra', 'Id_persona: 10200300 | Accion: Registro de compra | Descripcion: Se registró la compra ID: 6 [Entrada]', '2026-08-26 21:39:02'),
(411, 10200300, 'Acceso al sistema', 'Id_persona: 10200300 | Accion: Acceso al sistema | Descripcion: Entro al panel administrativo el usuario: V - 10200300, Jefe Lovemakeup [Login]', '2026-08-26 22:45:49'),
(412, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Ventas [Salida]', '2026-08-26 22:45:54'),
(413, 10200300, 'Acceso al sistema', 'Id_persona: 10200300 | Accion: Acceso al sistema | Descripcion: Entro al panel administrativo el usuario: V - 10200300, Jefe Lovemakeup [Login]', '2026-08-28 11:16:55'),
(414, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Ventas [Salida]', '2026-08-28 11:16:59'),
(415, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Producto [Producto]', '2026-08-28 11:17:01'),
(416, 10200300, 'Registro de producto', 'Id_persona: 10200300 | Accion: Registro de producto | Descripcion: Se registró el producto: gel [Producto]', '2026-08-28 11:17:22'),
(417, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Producto [Producto]', '2026-08-28 11:17:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulo`
--

CREATE TABLE `modulo` (
  `id_modulo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `modulo`
--

INSERT INTO `modulo` (`id_modulo`, `nombre`) VALUES
(1, 'Reporte'),
(2, 'Compra'),
(3, 'Venta'),
(4, 'Reserva'),
(5, 'Pedido Web'),
(6, 'Producto'),
(7, 'Marca'),
(8, 'Categoria'),
(9, 'Proveedor'),
(10, 'Cliente'),
(11, 'Delivery'),
(12, 'Metodo Entrega'),
(13, 'Metodo Pago'),
(14, 'Tasa de Cambio'),
(15, 'Bitacora'),
(16, 'Usuario'),
(17, 'Tipo Usuario'),
(18, 'Notificaciones'),
(19, 'Lista de Deseos'),
(20, 'Ver Mis Datos'),
(21, 'Ver Mis Pedidos'),
(22, 'Ver Carrito'),
(23, 'Pedido Entrega'),
(24, 'Pedido Pago');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permiso`
--

CREATE TABLE `permiso` (
  `id_permiso` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `permiso`
--

INSERT INTO `permiso` (`id_permiso`, `nombre`) VALUES
(1, 'Ver'),
(2, 'Registrar'),
(3, 'Modificar'),
(4, 'Eliminar'),
(5, 'Especial');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permiso_rol`
--

CREATE TABLE `permiso_rol` (
  `id_permiso_rol` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `id_permiso` int(11) NOT NULL,
  `estado` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `permiso_rol`
--

INSERT INTO `permiso_rol` (`id_permiso_rol`, `id_rol`, `id_modulo`, `id_permiso`, `estado`) VALUES
(1, 1, 1, 1, 1),
(2, 1, 2, 1, 1),
(3, 1, 2, 2, 1),
(4, 1, 2, 3, 1),
(5, 1, 3, 1, 1),
(6, 1, 3, 2, 1),
(7, 1, 4, 1, 1),
(8, 1, 4, 5, 1),
(9, 1, 5, 1, 1),
(10, 1, 5, 5, 1),
(11, 1, 6, 1, 1),
(12, 1, 6, 2, 1),
(13, 1, 6, 3, 1),
(14, 1, 6, 4, 1),
(15, 1, 6, 5, 1),
(16, 1, 7, 1, 1),
(17, 1, 7, 2, 1),
(18, 1, 7, 3, 1),
(19, 1, 7, 4, 1),
(20, 1, 8, 1, 1),
(21, 1, 8, 2, 1),
(22, 1, 8, 3, 1),
(23, 1, 8, 4, 1),
(24, 1, 9, 1, 1),
(25, 1, 9, 2, 1),
(26, 1, 9, 3, 1),
(27, 1, 9, 4, 1),
(28, 1, 10, 1, 1),
(29, 1, 10, 3, 1),
(30, 1, 11, 1, 1),
(31, 1, 11, 2, 1),
(32, 1, 11, 3, 1),
(33, 1, 11, 4, 1),
(34, 1, 12, 1, 1),
(35, 1, 12, 2, 1),
(36, 1, 12, 3, 1),
(37, 1, 12, 4, 1),
(38, 1, 13, 1, 1),
(39, 1, 13, 2, 1),
(40, 1, 13, 3, 1),
(41, 1, 13, 4, 1),
(42, 1, 14, 1, 1),
(43, 1, 14, 3, 1),
(44, 1, 15, 1, 1),
(45, 1, 15, 4, 1),
(46, 1, 16, 1, 1),
(47, 1, 16, 2, 1),
(48, 1, 16, 3, 1),
(49, 1, 16, 4, 1),
(50, 1, 17, 1, 1),
(51, 1, 17, 2, 1),
(52, 1, 17, 3, 1),
(53, 1, 17, 4, 1),
(54, 1, 17, 5, 1),
(55, 1, 18, 1, 1),
(56, 1, 18, 5, 1),
(57, 2, 19, 1, 1),
(58, 2, 19, 2, 1),
(59, 2, 19, 3, 1),
(60, 2, 19, 4, 1),
(61, 2, 20, 1, 1),
(62, 2, 20, 3, 1),
(63, 2, 20, 4, 1),
(64, 2, 21, 1, 1),
(65, 2, 22, 1, 1),
(66, 2, 22, 2, 1),
(67, 2, 23, 1, 1),
(68, 2, 23, 2, 1),
(69, 2, 24, 1, 1),
(70, 2, 24, 2, 1),
(71, 2, 24, 1, 1),
(72, 3, 1, 1, 1),
(73, 3, 3, 1, 1),
(74, 3, 3, 2, 1),
(75, 3, 4, 1, 1),
(76, 3, 4, 5, 1),
(77, 3, 5, 1, 1),
(78, 3, 5, 5, 1),
(79, 3, 6, 1, 1),
(80, 3, 6, 5, 1),
(81, 3, 10, 1, 1),
(82, 3, 10, 3, 1),
(83, 3, 14, 1, 1),
(84, 3, 14, 3, 1),
(85, 3, 18, 1, 1),
(86, 3, 18, 5, 1),
(87, 4, 1, 1, 1),
(88, 4, 2, 1, 1),
(89, 4, 2, 2, 1),
(90, 4, 2, 3, 1),
(91, 4, 3, 1, 1),
(92, 4, 3, 2, 1),
(93, 4, 4, 1, 1),
(94, 4, 4, 5, 1),
(95, 4, 5, 1, 1),
(96, 4, 5, 5, 1),
(97, 4, 6, 1, 1),
(98, 4, 6, 2, 1),
(99, 4, 6, 3, 1),
(100, 4, 6, 4, 1),
(101, 4, 6, 5, 1),
(102, 4, 7, 1, 1),
(103, 4, 7, 2, 1),
(104, 4, 7, 3, 1),
(105, 4, 7, 4, 1),
(106, 4, 8, 1, 1),
(107, 4, 8, 2, 1),
(108, 4, 8, 3, 1),
(109, 4, 8, 4, 1),
(110, 4, 9, 1, 1),
(111, 4, 9, 2, 1),
(112, 4, 9, 3, 1),
(113, 4, 9, 4, 1),
(114, 4, 10, 1, 1),
(115, 4, 10, 3, 1),
(116, 4, 11, 1, 1),
(117, 4, 11, 2, 1),
(118, 4, 11, 3, 1),
(119, 4, 11, 4, 1),
(120, 4, 12, 1, 1),
(121, 4, 12, 2, 1),
(122, 4, 12, 3, 1),
(123, 4, 12, 4, 1),
(124, 4, 13, 1, 1),
(125, 4, 13, 2, 1),
(126, 4, 13, 3, 1),
(127, 4, 13, 4, 1),
(128, 4, 14, 1, 1),
(129, 4, 14, 3, 1),
(130, 4, 15, 1, 1),
(131, 4, 15, 4, 1),
(132, 4, 16, 1, 1),
(133, 4, 16, 2, 1),
(134, 4, 16, 3, 1),
(135, 4, 16, 4, 1),
(136, 4, 17, 1, 1),
(137, 4, 17, 2, 1),
(138, 4, 17, 3, 1),
(139, 4, 17, 4, 1),
(140, 4, 17, 5, 1),
(141, 4, 18, 1, 1),
(142, 4, 18, 5, 1),
(143, 5, 1, 1, 1),
(144, 5, 2, 1, 1),
(145, 5, 2, 2, 1),
(146, 5, 2, 3, 1),
(147, 5, 3, 1, 1),
(148, 5, 3, 2, 1),
(149, 5, 4, 1, 1),
(150, 5, 4, 5, 1),
(151, 5, 5, 1, 1),
(152, 5, 5, 5, 1),
(153, 5, 6, 1, 1),
(154, 5, 6, 2, 1),
(155, 5, 6, 3, 1),
(156, 5, 6, 4, 1),
(157, 5, 6, 5, 1),
(158, 5, 7, 1, 1),
(159, 5, 7, 2, 1),
(160, 5, 7, 3, 1),
(161, 5, 7, 4, 1),
(162, 5, 8, 1, 1),
(163, 5, 8, 2, 1),
(164, 5, 8, 3, 1),
(165, 5, 8, 4, 1),
(166, 5, 9, 1, 1),
(167, 5, 9, 2, 1),
(168, 5, 9, 3, 1),
(169, 5, 9, 4, 1),
(170, 5, 10, 1, 1),
(171, 5, 10, 3, 1),
(172, 5, 11, 1, 1),
(173, 5, 11, 2, 1),
(174, 5, 11, 3, 1),
(175, 5, 11, 4, 1),
(176, 5, 12, 1, 0),
(177, 5, 12, 2, 0),
(178, 5, 12, 3, 0),
(179, 5, 12, 4, 0),
(180, 5, 13, 1, 0),
(181, 5, 13, 2, 0),
(182, 5, 13, 3, 0),
(183, 5, 13, 4, 0),
(184, 5, 14, 1, 1),
(185, 5, 14, 3, 1),
(186, 5, 15, 1, 0),
(187, 5, 15, 4, 0),
(188, 5, 16, 1, 1),
(189, 5, 16, 2, 1),
(190, 5, 16, 3, 1),
(191, 5, 16, 4, 1),
(192, 5, 17, 1, 0),
(193, 5, 17, 2, 0),
(194, 5, 17, 3, 0),
(195, 5, 17, 4, 0),
(196, 5, 17, 5, 0),
(197, 5, 18, 1, 1),
(198, 5, 18, 5, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE `persona` (
  `cedula` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `tipo_documento` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `persona`
--

INSERT INTO `persona` (`cedula`, `nombre`, `apellido`, `correo`, `telefono`, `tipo_documento`) VALUES
(7448073, 'Maribel', 'Codero', 'cmcorderomaribel@gmail.com', '0424-5712502', 'V'),
(10090080, 'Soporte', 'Dev', 'corre@gmail.com', '0424-1243265', 'V'),
(10200300, 'Jefe', 'Lovemakeup', 'correo@gmail.com', '0424-0000000', 'V'),
(11697944, 'Henry', 'Sanchez', 'henryjosesanchez12@gmail.com', '0426-0356037', 'V'),
(12701387, 'Fidel', 'Aguilar', 'fidelaguilar3000@gmail.com', '0424-5574258', 'V'),
(15200300, 'Laura', 'Martinez', 'lauramaria546col@gmail.com', '0414-4567666', 'V'),
(15350450, 'Pedro', 'Colmenarez', 'pedrocolmenarez20001@gmail.com', '0412-5666999', 'V'),
(20150599, 'Susana', 'Castillo', 'susancastillo9805@gmail.com', '0414-9735566', 'V'),
(26779660, 'Daniel', 'Rojas', 'danielrojas660@gmail.com', '0424-2123456', 'V'),
(27349943, 'Jose', 'Sanchez', 'josse9823@gmail.com', '0424-5603257', 'V'),
(28493943, 'Yoselyn', 'Montana', 'sebasyoset04@gmail.com', '0412-0524985', 'V'),
(28500131, 'Valentina', 'Perez', 'valentina2perez200@gmail.com', '0424-5600655', 'V'),
(28516209, 'Manuela', 'Mujica', 'manuelaalejandra.mujica@gmail.com', '0426-5507191', 'V'),
(28539535, 'Hennymar', 'Penaloza', 'hennypeba@gmail.com', '0416-7068686', 'V'),
(28653567, 'Katherin', 'Sanchez', 'katherinsanchez5254@gmail.com', '0426-5656943', 'V'),
(28653577, 'Adriana', 'Sanchez', 'sanchezari12@gmail.com', '0416-5258888', 'V'),
(29506932, 'Moises', 'Torrealba', 'moisestorrealba200@gmail.com', '0412-0554646', 'V'),
(29517871, 'Leonardo', 'Perez', 'leonardomediana22@gmail.com', '0412-6565645', 'V'),
(29531465, 'Yonathan', 'Medino', 'yonathan20255@gmail.com', '0424-5656569', 'V'),
(29997994, 'Yessica', 'Valentina', 'yessica@gmail.com', '0424-5555555', 'V'),
(30621801, 'Mairelys', 'Marin', 'alejandramarinyanez@gmail.com', '0414-5354236', 'V'),
(30754263, 'Carlyannys', 'Mora', '06abril05@gmail.com', '0424-5753257', 'V'),
(30803034, 'Raymari', 'Romero', 'raymaripaolaromero@gmail.com', '0424-5635573', 'V'),
(31765947, 'Roxi', 'Quintero', 'roxiquintero@gmail.com', '0424-5390678', 'V'),
(31770828, 'Yanna', 'Alvarez', 'alvarezyanna180@gmail.com', '0412-0524985', 'V'),
(32063438, 'Aron', 'Sanchez', 'aaronjosue098@gmail.com', '0416-1225722', 'V'),
(32121168, 'Esthefany', 'Perdomo', 'esthefanyperdono@gmail.com', '0424-5168720', 'V');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `nivel` int(1) DEFAULT NULL,
  `estatus` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `nombre`, `nivel`, `estatus`) VALUES
(1, 'Desarrollador', 3, 1),
(2, 'Cliente', 1, 1),
(3, 'Asesora de Venta', 2, 1),
(4, 'Administrador', 3, 1),
(5, 'Encargado', 3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `cedula` int(11) NOT NULL,
  `clave` varchar(512) NOT NULL,
  `estatus` int(1) DEFAULT NULL,
  `id_rol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `cedula`, `clave`, `estatus`, `id_rol`) VALUES
(1, 10090080, 'c42t7J2RXdedsSUKKahzaE15eGhEcE1rbWs5ZWoxNU5vVEQrRUE9PQ==', 1, 1),
(2, 10200300, 'GJ2LRyGX9XUpCmkwhg+ibVFaV0dZQkNnSldQcVRaY281dStrWHc9PQ==', 1, 4),
(3, 15200300, 'UFx0aI+aFhDxgi0ZrYwEAXBtaHBwbFR2WHJvdUo4V3pIOGplbXc9PQ==', 1, 3),
(4, 27349943, 'CQAA+TqTnJbrv9o5bkX/1lI2Vm1ldGluNmE4UjhOZzRBNzROY3c9PQ==', 1, 2),
(5, 20150599, '+jX20dNnSVIwf70frrWAz3IzY3JXUVYvckFzNXQwbTZ6NUVldXc9PQ==', 1, 3),
(6, 15350450, '2jHeLjyrJ8YK8HuEcsBY1XlyUFRXajQ5djNuNmZWcDN5d1BaMmc9PQ==', 1, 5),
(7, 32063438, 'TFo7VOArQKVx9j4hXxPvpG5uLytIYmFoNmI2MWYrTUZKRVl2a3c9PQ==', 1, 2),
(8, 28493943, 'T1pgqKzIP/HQxS72UoNj2UZjNm1iRXVSUDA3bitIS3UybDNWRVE9PQ==', 1, 2),
(9, 31770828, 'XDExdHIiBI9AbBMg9hrmKkF2ekUvNDZNZFA1dVZ2NTYrSityVHc9PQ==', 1, 2),
(10, 30803034, '4xO4gthPCwL4I/Ev+l6tVTMyOWpXR3BMeTBDbWYrOXAvdjNjYmc9PQ==', 1, 2),
(11, 7448073, 'KEV0LnkaTt7n+5LOU3nbA1hpSW1iR1JZbnN0aGRnbUtrOVB0UEE9PQ==', 1, 2),
(12, 28516209, 'g8y2XZ6MvhuIDOEM5GWC0mMrYnFCMTdTVko0NVNWbXoyM3RPTHc9PQ==', 1, 2),
(13, 30621801, 'fR+RGWircvXUBoAf4qX8o0FyckR5aVEyYTBQSmhBT0g4MmVTcFE9PQ==', 1, 2),
(14, 29997994, '3eAoh2RngT4q/w1PLKRvKjAxVlc1elN6dlhOZGZHOGRoTGRtcVE9PQ==', 1, 2),
(15, 29517871, 'A0hi6efv+64BvER9aZx4VW9iVmtVaWJySU1BQVA5N1IvU2FtYVE9PQ==', 1, 2),
(16, 29531465, '0IB572fi30/vm1H6D46e2zdqcFJxZjN5R0plcnhxT28yU1pSbFE9PQ==', 1, 2),
(17, 29506932, 'KGh87B04FNy5Nssbv2w6C1VCWUgyOCtTcm4rOXE1VEVxbjd1eHc9PQ==', 1, 2),
(18, 31765947, 'aK+TkZpXBwWw0iuuQJf83nlTTDE3bFFzdjhTZnhQZU1yREMwSEE9PQ==', 1, 2),
(19, 26779660, 'u9lzhGGojtNNbnPxwftHMTR4WkpDSThPS1dBUzZIT2VadTdWc2c9PQ==', 1, 2),
(20, 12701387, '2YWSxFjfGbAJUMnDFyT1PWVLTVNWR2hvNGluRlYva0wzbG9jQWc9PQ==', 1, 2),
(21, 28539535, 'ba7c9rU1uqPPtYAPZ+TC+0VYbmg5VnkySnRqZXZ3VzFOeWRoZWc9PQ==', 1, 2),
(22, 32121168, '+M6p0KJS+ATZ7/oDaTO2am0yeC9ZL1BRb2dWY3drZnFOS1UrRFE9PQ==', 1, 2),
(23, 28653577, 'b6e3+inFvm4j6aBq7wrQRDROWkNVckdUeE44SlVqamtqcVVzMEE9PQ==', 1, 2),
(24, 28653567, 'IzgKBWtPuMtjxJmveE5rQlBjd00yUXI0K2puekZrY1BCTnNiUnc9PQ==', 1, 2),
(25, 11697944, 'vhiKC1kY0SaQIjUh2TYiEGVWNEdBWm9iL1dqL0VqM0JxQTlEOEE9PQ==', 1, 2),
(26, 28500131, 'yoZbYL6gHI2PMwwngiafnUIvYlhRWTQzVmdZdFUxaVpwVlFVTlE9PQ==', 1, 3),
(27, 30754263, 'y7/67+3YpU+BuBFPPLtTqk92ckltdzZIcStGYVFLS2ZLMVp1YUE9PQ==', 1, 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD PRIMARY KEY (`id_bitacora`),
  ADD KEY `cedula` (`cedula`);

--
-- Indices de la tabla `modulo`
--
ALTER TABLE `modulo`
  ADD PRIMARY KEY (`id_modulo`);

--
-- Indices de la tabla `permiso`
--
ALTER TABLE `permiso`
  ADD PRIMARY KEY (`id_permiso`);

--
-- Indices de la tabla `permiso_rol`
--
ALTER TABLE `permiso_rol`
  ADD PRIMARY KEY (`id_permiso_rol`),
  ADD KEY `id_rol` (`id_rol`),
  ADD KEY `id_modulo` (`id_modulo`),
  ADD KEY `id_permiso` (`id_permiso`);

--
-- Indices de la tabla `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`cedula`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `cedula` (`cedula`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `id_bitacora` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=418;

--
-- AUTO_INCREMENT de la tabla `modulo`
--
ALTER TABLE `modulo`
  MODIFY `id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `permiso`
--
ALTER TABLE `permiso`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `permiso_rol`
--
ALTER TABLE `permiso_rol`
  MODIFY `id_permiso_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD CONSTRAINT `bitacora_ibfk_1` FOREIGN KEY (`cedula`) REFERENCES `persona` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `permiso_rol`
--
ALTER TABLE `permiso_rol`
  ADD CONSTRAINT `permiso_rol_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`) ON DELETE CASCADE,
  ADD CONSTRAINT `permiso_rol_ibfk_2` FOREIGN KEY (`id_modulo`) REFERENCES `modulo` (`id_modulo`),
  ADD CONSTRAINT `permiso_rol_ibfk_3` FOREIGN KEY (`id_permiso`) REFERENCES `permiso` (`id_permiso`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`cedula`) REFERENCES `persona` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usuario_ibfk_2` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
