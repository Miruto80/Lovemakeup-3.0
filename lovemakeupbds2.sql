-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-06-2026 a las 03:53:41
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
CREATE DATABASE IF NOT EXISTS `lovemakeupbds2` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci;
USE `lovemakeupbds2`;

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
(1, 10200300, 'Acceso al sistema', 'Id_persona: 10200300 | Accion: Acceso al sistema | Descripcion: Entro al panel administrativo el usuario: V - 10200300, Jefe Lovemakeup [Login]', '2026-06-04 09:10:57'),
(2, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Producto [Producto]', '2026-06-04 09:11:10'),
(3, 10200300, 'ACCESO A MÓDULO', 'Usuario accedió al módulo de Bitácora [Bitácora]', '2026-06-04 09:11:13'),
(4, 10200300, 'Acceso a Usuario', 'Id_persona: 10200300 | Accion: Acceso a Usuario | Descripcion: Entro al módulo de Usuario [Usuario]', '2026-06-04 09:11:18'),
(5, 10200300, 'Acceso al sistema', 'Id_persona: 10200300 | Accion: Acceso al sistema | Descripcion: Entro al panel administrativo el usuario: V - 10200300, Jefe Lovemakeup [Login]', '2026-06-04 09:12:53'),
(6, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Producto [Producto]', '2026-06-04 09:13:43'),
(7, 10200300, 'Registro de producto', 'Id_persona: 10200300 | Accion: Registro de producto | Descripcion: Se registró el producto: polvo [Producto]', '2026-06-04 09:14:17'),
(8, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Producto [Producto]', '2026-06-04 09:14:18'),
(9, 10200300, 'Registro de compra', 'Id_persona: 10200300 | Accion: Registro de compra | Descripcion: Se registró la compra ID: 1 [Entrada]', '2026-06-04 09:14:42'),
(10, 10200300, 'Generó Reporte de Productos', 'Descripcion: Usuario (Administrador) ejecutó Generó Reporte de Productos [Reporte]', '2026-06-04 09:14:57'),
(11, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Ventas [Salida]', '2026-06-04 09:15:23'),
(12, 27349943, 'Registro de venta', 'Se registró una nueva venta con ID: 1', '2026-06-04 09:15:59'),
(13, 10200300, 'Registro de venta', 'Id_persona: 10200300 | Accion: Registro de venta | Descripcion: Se registró la venta ID: 1 [Salida]', '2026-06-04 09:15:59'),
(14, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Ventas [Salida]', '2026-06-04 09:16:00'),
(15, 10200300, 'Acceso al sistema', 'Id_persona: 10200300 | Accion: Acceso al sistema | Descripcion: Entro al panel administrativo el usuario: V - 10200300, Jefe Lovemakeup [Login]', '2026-06-04 21:15:12'),
(16, 10200300, 'ACCESO A MÓDULO', 'Usuario accedió al módulo de Bitácora [Bitácora]', '2026-06-04 21:15:20'),
(17, 10200300, 'Acceso a Usuario', 'Id_persona: 10200300 | Accion: Acceso a Usuario | Descripcion: Entro al módulo de Usuario [Usuario]', '2026-06-04 21:15:23'),
(18, 10200300, 'Acceso a Módulo ROL', 'Id_persona: 10200300 | Accion: Acceso a Módulo ROL | Descripcion: Entro al módulo de Tipo usuario [Tipo usuario (ROL)]', '2026-06-04 21:15:26'),
(19, 10200300, 'Acceso al sistema', 'Id_persona: 10200300 | Accion: Acceso al sistema | Descripcion: Entro al panel administrativo el usuario: V - 10200300, Jefe Lovemakeup [Login]', '2026-06-04 21:21:39'),
(20, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Producto [Producto]', '2026-06-04 21:21:47'),
(21, 10200300, 'Registro de producto', 'Id_persona: 10200300 | Accion: Registro de producto | Descripcion: Se registró el producto: Lip glaze [Producto]', '2026-06-04 21:26:41'),
(22, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Producto [Producto]', '2026-06-04 21:26:42'),
(23, 10200300, 'Acceso a Categorías', 'Id_persona: 10200300 | Accion: Acceso a Categorías | Descripcion: Administrador accedió al módulo Categoría [Categoria]', '2026-06-04 21:27:11'),
(24, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Categoria [Categoria]', '2026-06-04 21:27:11'),
(25, 10200300, 'Incluir Categoría', 'Id_persona: 10200300 | Accion: Incluir Categoría | Descripcion: Registró categoría \"Labial\" [Categoria]', '2026-06-04 21:27:30'),
(26, 10200300, 'Acceso a Categorías', 'Id_persona: 10200300 | Accion: Acceso a Categorías | Descripcion: Administrador accedió al módulo Categoría [Categoria]', '2026-06-04 21:27:32'),
(27, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Categoria [Categoria]', '2026-06-04 21:27:32'),
(28, 10200300, 'Incluir Categoría', 'Id_persona: 10200300 | Accion: Incluir Categoría | Descripcion: Registró categoría \"Sombras\" [Categoria]', '2026-06-04 21:28:34'),
(29, 10200300, 'Acceso a Categorías', 'Id_persona: 10200300 | Accion: Acceso a Categorías | Descripcion: Administrador accedió al módulo Categoría [Categoria]', '2026-06-04 21:28:35'),
(30, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Categoria [Categoria]', '2026-06-04 21:28:35'),
(31, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Producto [Producto]', '2026-06-04 21:32:40'),
(32, 10200300, 'Modificación de producto', 'Id_persona: 10200300 | Accion: Modificación de producto | Descripcion: Se modificó el producto: Lip glaze [Producto]', '2026-06-04 21:33:03'),
(33, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Producto [Producto]', '2026-06-04 21:33:04'),
(34, 10200300, 'Registro de producto', 'Id_persona: 10200300 | Accion: Registro de producto | Descripcion: Se registró el producto: Lipgloss dije tacon [Producto]', '2026-06-04 21:38:32'),
(35, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Producto [Producto]', '2026-06-04 21:38:33'),
(36, 10200300, 'Acceso a Marcas', 'Id_persona: 10200300 | Accion: Acceso a Marcas | Descripcion: Usuario accedió al módulo Marca [Marca]', '2026-06-04 21:42:11'),
(37, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Marca [Marca]', '2026-06-04 21:42:11'),
(38, 10200300, 'Incluir Marca', 'Id_persona: 10200300 | Accion: Incluir Marca | Descripcion: Registró marca “katire” [Marca]', '2026-06-04 21:42:26'),
(39, 10200300, 'Acceso a Marcas', 'Id_persona: 10200300 | Accion: Acceso a Marcas | Descripcion: Usuario accedió al módulo Marca [Marca]', '2026-06-04 21:42:27'),
(40, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Marca [Marca]', '2026-06-04 21:42:27'),
(41, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Producto [Producto]', '2026-06-04 21:42:40'),
(42, 10200300, 'Registro de producto', 'Id_persona: 10200300 | Accion: Registro de producto | Descripcion: Se registró el producto: Lip oil rosado [Producto]', '2026-06-04 21:44:44'),
(43, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Producto [Producto]', '2026-06-04 21:44:45'),
(44, 10200300, 'Registro de compra', 'Id_persona: 10200300 | Accion: Registro de compra | Descripcion: Se registró la compra ID: 2 [Entrada]', '2026-06-04 21:47:50'),
(45, 10200300, 'Acceso a Módulo Cliente', 'Id_persona: 10200300 | Accion: Acceso a Módulo Cliente | Descripcion: Entro al módulo de Cliente [Cliente]', '2026-06-04 21:48:01'),
(46, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Ventas [Salida]', '2026-06-04 21:48:19'),
(47, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Producto [Producto]', '2026-06-04 21:48:40'),
(48, 10200300, 'Modificación de producto', 'Id_persona: 10200300 | Accion: Modificación de producto | Descripcion: Se modificó el producto: Lip oil rosado [Producto]', '2026-06-04 21:48:56'),
(49, 10200300, 'Acceso a Módulo', 'Id_persona: 10200300 | Accion: Acceso a Módulo | Descripcion: módulo de Producto [Producto]', '2026-06-04 21:48:57');

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
(142, 4, 18, 5, 1);

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
(10090080, 'Soporte', 'Dev', 'corre@gmail.com', '0424-1243265', 'V'),
(10200300, 'Jefe', 'Lovemakeup', 'correo@gmail.com', '0424-0000000', 'V'),
(15200300, 'Cajera', 'Makeup', 'cajera@gmail.com', '0414-0000000', 'V'),
(27349943, 'Jose', 'Sanchez', 'josse9823@gmail.com', '0424-5603257', 'V');

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
(4, 'Administrador', 3, 1);

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
(4, 27349943, 'CQAA+TqTnJbrv9o5bkX/1lI2Vm1ldGluNmE4UjhOZzRBNzROY3c9PQ==', 1, 2);

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
  MODIFY `id_bitacora` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

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
  MODIFY `id_permiso_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD CONSTRAINT `bitacora_ibfk_1` FOREIGN KEY (`cedula`) REFERENCES `persona` (`cedula`) ON DELETE CASCADE;

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
