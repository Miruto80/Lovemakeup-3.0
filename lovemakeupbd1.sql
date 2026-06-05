-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-06-2026 a las 03:52:20
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
-- Base de datos: `lovemakeupbd1`
--
CREATE DATABASE IF NOT EXISTS `lovemakeupbd1` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci;
USE `lovemakeupbd1`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `estatus` int(2) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `nombre`, `estatus`) VALUES
(1, 'Polvo', 1),
(2, 'Labial', 1),
(3, 'Sombras', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra`
--

CREATE TABLE `compra` (
  `id_compra` int(11) NOT NULL,
  `fecha_entrada` datetime DEFAULT NULL,
  `id_proveedor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `compra`
--

INSERT INTO `compra` (`id_compra`, `fecha_entrada`, `id_proveedor`) VALUES
(1, '2026-06-04 09:14:42', 1),
(2, '2026-06-04 21:47:50', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra_detalles`
--

CREATE TABLE `compra_detalles` (
  `id_detalle_compra` int(11) NOT NULL,
  `id_compra` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio_total` decimal(12,2) DEFAULT NULL,
  `precio_unitario` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `compra_detalles`
--

INSERT INTO `compra_detalles` (`id_detalle_compra`, `id_compra`, `id_producto`, `cantidad`, `precio_total`, `precio_unitario`) VALUES
(1, 1, 5, 100, 50.00, 0.50),
(2, 2, 6, 100, 50.00, 0.50),
(3, 2, 7, 100, 50.00, 0.50),
(4, 2, 8, 100, 50.00, 0.50);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comprobante_pago`
--

CREATE TABLE `comprobante_pago` (
  `id_comprobante` int(11) NOT NULL,
  `id_pago` int(11) DEFAULT NULL,
  `imagen` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `delivery`
--

CREATE TABLE `delivery` (
  `id_delivery` int(11) NOT NULL,
  `nombre` varchar(80) DEFAULT NULL,
  `tipo` varchar(80) DEFAULT NULL,
  `contacto` varchar(100) DEFAULT NULL,
  `estatus` int(2) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pago`
--

CREATE TABLE `detalle_pago` (
  `id_pago` int(11) NOT NULL,
  `monto` decimal(12,2) DEFAULT NULL,
  `monto_usd` decimal(12,2) DEFAULT NULL,
  `id_metodopago` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `detalle_pago`
--

INSERT INTO `detalle_pago` (`id_pago`, `monto`, `monto_usd`, `id_metodopago`) VALUES
(1, 0.00, 4.00, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direccion`
--

CREATE TABLE `direccion` (
  `id_direccion` int(11) NOT NULL,
  `cedula` varchar(15) DEFAULT NULL,
  `direccion_envio` text DEFAULT NULL,
  `sucursal_envio` varchar(100) DEFAULT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `id_metodoentrega` int(11) DEFAULT NULL,
  `id_delivery` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lista_deseo`
--

CREATE TABLE `lista_deseo` (
  `id_lista` int(11) NOT NULL,
  `cedula` varchar(15) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marca`
--

CREATE TABLE `marca` (
  `id_marca` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `estatus` int(2) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `marca`
--

INSERT INTO `marca` (`id_marca`, `nombre`, `estatus`) VALUES
(1, 'Sin Marca', 1),
(2, 'Ushas', 1),
(3, 'katire', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodo_entrega`
--

CREATE TABLE `metodo_entrega` (
  `id_entrega` int(11) NOT NULL,
  `nombre` varchar(200) DEFAULT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  `estatus` int(2) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `metodo_entrega`
--

INSERT INTO `metodo_entrega` (`id_entrega`, `nombre`, `descripcion`, `estatus`) VALUES
(1, 'Delivery', 'Barquisimeto', 1),
(2, 'MRW', 'Envió nacionales', 1),
(3, 'ZOOM', 'Envio nacionales', 1),
(4, 'Retiro en Tienda Fisica', 'Tienda Fisica', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodo_pago`
--

CREATE TABLE `metodo_pago` (
  `id_metodopago` int(11) NOT NULL,
  `nombre` varchar(200) DEFAULT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  `requiere_banco` int(2) DEFAULT NULL,
  `estatus` int(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `metodo_pago`
--

INSERT INTO `metodo_pago` (`id_metodopago`, `nombre`, `descripcion`, `requiere_banco`, `estatus`) VALUES
(1, 'Pago Movil', 'Pagos en moneda nacional Bs', 1, 1),
(2, 'Transferencia Bancaria', 'Pagos en moneda nacional Bs', 1, 1),
(3, 'Punto de Venta', 'Pagos en moneda nacional Bs Atravez con tarjeta Bancaria', 0, 1),
(4, 'Efectivo Bs', 'Pagos en moneda nacional Bs', 0, 1),
(5, 'Divisas (Dolares $)', 'Pagos en moneda extranjera Dolares $', 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id_notificacion` int(11) NOT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `mensaje` varchar(100) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `estado` varchar(20) DEFAULT NULL,
  `id_pedido` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `id_pedido` int(11) NOT NULL,
  `tipo` varchar(20) DEFAULT NULL,
  `canal_origen` varchar(20) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `estatus` varchar(20) DEFAULT NULL,
  `tracking` varchar(100) DEFAULT NULL,
  `precio_total_usd` decimal(12,2) DEFAULT NULL,
  `precio_total_bs` decimal(12,2) DEFAULT NULL,
  `cedula` int(11) DEFAULT NULL,
  `id_direccion` int(11) DEFAULT NULL,
  `id_pago` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `pedido`
--

INSERT INTO `pedido` (`id_pedido`, `tipo`, `canal_origen`, `fecha`, `estatus`, `tracking`, `precio_total_usd`, `precio_total_bs`, `cedula`, `id_direccion`, `id_pago`) VALUES
(1, '1', NULL, '2026-06-04 09:15:59', '2', NULL, 4.00, 2241.50, 27349943, NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_detalles`
--

CREATE TABLE `pedido_detalles` (
  `id_detalle` int(11) NOT NULL,
  `id_pedido` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio_unitario` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `pedido_detalles`
--

INSERT INTO `pedido_detalles` (`id_detalle`, `id_pedido`, `id_producto`, `cantidad`, `precio_unitario`) VALUES
(1, 1, 5, 2, 2.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  `cantidad_mayor` int(11) DEFAULT NULL,
  `precio_mayor` decimal(10,2) DEFAULT NULL,
  `precio_detal` decimal(10,2) DEFAULT NULL,
  `stock_disponible` int(11) DEFAULT NULL,
  `stock_minimo` int(11) DEFAULT NULL,
  `stock_maximo` int(11) DEFAULT NULL,
  `estatus` int(1) DEFAULT 1,
  `id_categoria` int(11) DEFAULT NULL,
  `id_marca` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id_producto`, `nombre`, `descripcion`, `cantidad_mayor`, `precio_mayor`, `precio_detal`, `stock_disponible`, `stock_minimo`, `stock_maximo`, `estatus`, `id_categoria`, `id_marca`) VALUES
(5, 'polvo', '100 porciento original', 12, 2.00, 2.00, 98, 10, 100, 1, 1, 1),
(6, 'Lip glaze', 'producto original', 10, 3.60, 4.70, 100, 10, 100, 1, 1, 2),
(7, 'Lipgloss dije tacon', 'Productos originales', 10, 2.70, 3.70, 100, 10, 100, 1, 2, 2),
(8, 'Lip oil rosado', 'producto original', 10, 1.75, 2.70, 100, 5, 100, 1, 1, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_imagen`
--

CREATE TABLE `producto_imagen` (
  `id_imagen` int(11) NOT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `url_imagen` text DEFAULT NULL,
  `tipo` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `producto_imagen`
--

INSERT INTO `producto_imagen` (`id_imagen`, `id_producto`, `url_imagen`, `tipo`) VALUES
(1, 6, 'assets/img/Imgproductos/img_6a22274f7b67a_01.png', 'principal'),
(2, 6, 'assets/img/Imgproductos/img_6a22274f7c139_0101.png', 'secundaria'),
(3, 6, 'assets/img/Imgproductos/img_6a22274f7c5c7_010101.png', 'secundaria'),
(4, 7, 'assets/img/Imgproductos/img_6a2228981100a_020202.png', 'principal'),
(5, 7, 'assets/img/Imgproductos/img_6a222898116bc_0202.png', 'secundaria'),
(6, 8, 'assets/img/Imgproductos/img_6a222b084b35f_030303.png', 'principal');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `id_proveedor` int(11) NOT NULL,
  `tipo_documento` varchar(10) DEFAULT NULL,
  `numero_documento` varchar(20) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `estatus` int(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`id_proveedor`, `tipo_documento`, `numero_documento`, `nombre`, `correo`, `telefono`, `direccion`, `estatus`) VALUES
(1, 'J', '900800700', 'Inveriones casa de maquijalle', 'inversionescasa@hotmail.com', '02518862233', 'av lara', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `referencia_pago`
--

CREATE TABLE `referencia_pago` (
  `id_referencia` int(11) NOT NULL,
  `id_pago` int(11) DEFAULT NULL,
  `banco_emisor` varchar(50) DEFAULT NULL,
  `banco_receptor` varchar(50) DEFAULT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `telefono_emisor` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reserva`
--

CREATE TABLE `reserva` (
  `id_reserva` int(11) NOT NULL,
  `id_pedido` int(11) DEFAULT NULL,
  `fecha_retiro` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tasa_dolar`
--

CREATE TABLE `tasa_dolar` (
  `fecha` date NOT NULL,
  `tasa_bs` decimal(10,2) DEFAULT NULL,
  `fuente` varchar(100) DEFAULT NULL,
  `estatus` int(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `tasa_dolar`
--

INSERT INTO `tasa_dolar` (`fecha`, `tasa_bs`, `fuente`, `estatus`) VALUES
('2026-06-04', 560.38, 'Automatico', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta`
--

CREATE TABLE `venta` (
  `id_venta` int(11) NOT NULL,
  `id_pedido` int(11) DEFAULT NULL,
  `fecha_confirmacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `venta`
--

INSERT INTO `venta` (`id_venta`, `id_pedido`, `fecha_confirmacion`) VALUES
(1, 1, '2026-06-04 09:15:59');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `compra`
--
ALTER TABLE `compra`
  ADD PRIMARY KEY (`id_compra`),
  ADD KEY `id_proveedor` (`id_proveedor`);

--
-- Indices de la tabla `compra_detalles`
--
ALTER TABLE `compra_detalles`
  ADD PRIMARY KEY (`id_detalle_compra`),
  ADD KEY `id_compra` (`id_compra`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `comprobante_pago`
--
ALTER TABLE `comprobante_pago`
  ADD PRIMARY KEY (`id_comprobante`),
  ADD KEY `id_pago` (`id_pago`);

--
-- Indices de la tabla `delivery`
--
ALTER TABLE `delivery`
  ADD PRIMARY KEY (`id_delivery`);

--
-- Indices de la tabla `detalle_pago`
--
ALTER TABLE `detalle_pago`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_metodopago` (`id_metodopago`);

--
-- Indices de la tabla `direccion`
--
ALTER TABLE `direccion`
  ADD PRIMARY KEY (`id_direccion`),
  ADD KEY `id_metodoentrega` (`id_metodoentrega`),
  ADD KEY `id_delivery` (`id_delivery`),
  ADD KEY `cedula` (`cedula`);

--
-- Indices de la tabla `lista_deseo`
--
ALTER TABLE `lista_deseo`
  ADD PRIMARY KEY (`id_lista`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `cedula` (`cedula`);

--
-- Indices de la tabla `marca`
--
ALTER TABLE `marca`
  ADD PRIMARY KEY (`id_marca`);

--
-- Indices de la tabla `metodo_entrega`
--
ALTER TABLE `metodo_entrega`
  ADD PRIMARY KEY (`id_entrega`);

--
-- Indices de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  ADD PRIMARY KEY (`id_metodopago`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id_notificacion`),
  ADD KEY `id_pedido` (`id_pedido`);

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_direccion` (`id_direccion`),
  ADD KEY `id_pago` (`id_pago`),
  ADD KEY `cedula` (`cedula`);

--
-- Indices de la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `id_categoria` (`id_categoria`),
  ADD KEY `id_marca` (`id_marca`),
  ADD KEY `nombre` (`nombre`),
  ADD KEY `stock_disponible` (`stock_disponible`);

--
-- Indices de la tabla `producto_imagen`
--
ALTER TABLE `producto_imagen`
  ADD PRIMARY KEY (`id_imagen`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`id_proveedor`),
  ADD KEY `numero_documento` (`numero_documento`);

--
-- Indices de la tabla `referencia_pago`
--
ALTER TABLE `referencia_pago`
  ADD PRIMARY KEY (`id_referencia`),
  ADD KEY `id_pago` (`id_pago`);

--
-- Indices de la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD PRIMARY KEY (`id_reserva`),
  ADD KEY `id_pedido` (`id_pedido`);

--
-- Indices de la tabla `tasa_dolar`
--
ALTER TABLE `tasa_dolar`
  ADD PRIMARY KEY (`fecha`);

--
-- Indices de la tabla `venta`
--
ALTER TABLE `venta`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `id_pedido` (`id_pedido`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `compra`
--
ALTER TABLE `compra`
  MODIFY `id_compra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `compra_detalles`
--
ALTER TABLE `compra_detalles`
  MODIFY `id_detalle_compra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `comprobante_pago`
--
ALTER TABLE `comprobante_pago`
  MODIFY `id_comprobante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `delivery`
--
ALTER TABLE `delivery`
  MODIFY `id_delivery` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_pago`
--
ALTER TABLE `detalle_pago`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `direccion`
--
ALTER TABLE `direccion`
  MODIFY `id_direccion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `lista_deseo`
--
ALTER TABLE `lista_deseo`
  MODIFY `id_lista` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `marca`
--
ALTER TABLE `marca`
  MODIFY `id_marca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `metodo_entrega`
--
ALTER TABLE `metodo_entrega`
  MODIFY `id_entrega` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  MODIFY `id_metodopago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `producto_imagen`
--
ALTER TABLE `producto_imagen`
  MODIFY `id_imagen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `id_proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `referencia_pago`
--
ALTER TABLE `referencia_pago`
  MODIFY `id_referencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reserva`
--
ALTER TABLE `reserva`
  MODIFY `id_reserva` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `venta`
--
ALTER TABLE `venta`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `compra`
--
ALTER TABLE `compra`
  ADD CONSTRAINT `compra_ibfk_1` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id_proveedor`);

--
-- Filtros para la tabla `compra_detalles`
--
ALTER TABLE `compra_detalles`
  ADD CONSTRAINT `compra_detalles_ibfk_1` FOREIGN KEY (`id_compra`) REFERENCES `compra` (`id_compra`),
  ADD CONSTRAINT `compra_detalles_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`);

--
-- Filtros para la tabla `comprobante_pago`
--
ALTER TABLE `comprobante_pago`
  ADD CONSTRAINT `comprobante_pago_ibfk_1` FOREIGN KEY (`id_pago`) REFERENCES `detalle_pago` (`id_pago`);

--
-- Filtros para la tabla `detalle_pago`
--
ALTER TABLE `detalle_pago`
  ADD CONSTRAINT `detalle_pago_ibfk_1` FOREIGN KEY (`id_metodopago`) REFERENCES `metodo_pago` (`id_metodopago`);

--
-- Filtros para la tabla `direccion`
--
ALTER TABLE `direccion`
  ADD CONSTRAINT `direccion_ibfk_1` FOREIGN KEY (`id_metodoentrega`) REFERENCES `metodo_entrega` (`id_entrega`),
  ADD CONSTRAINT `direccion_ibfk_2` FOREIGN KEY (`id_delivery`) REFERENCES `delivery` (`id_delivery`);

--
-- Filtros para la tabla `lista_deseo`
--
ALTER TABLE `lista_deseo`
  ADD CONSTRAINT `lista_deseo_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`);

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id_pedido`);

--
-- Filtros para la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `pedido_ibfk_1` FOREIGN KEY (`id_direccion`) REFERENCES `direccion` (`id_direccion`),
  ADD CONSTRAINT `pedido_ibfk_2` FOREIGN KEY (`id_pago`) REFERENCES `detalle_pago` (`id_pago`);

--
-- Filtros para la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  ADD CONSTRAINT `pedido_detalles_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id_pedido`),
  ADD CONSTRAINT `pedido_detalles_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`);

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`),
  ADD CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`id_marca`) REFERENCES `marca` (`id_marca`);

--
-- Filtros para la tabla `producto_imagen`
--
ALTER TABLE `producto_imagen`
  ADD CONSTRAINT `producto_imagen_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`);

--
-- Filtros para la tabla `referencia_pago`
--
ALTER TABLE `referencia_pago`
  ADD CONSTRAINT `referencia_pago_ibfk_1` FOREIGN KEY (`id_pago`) REFERENCES `detalle_pago` (`id_pago`);

--
-- Filtros para la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD CONSTRAINT `reserva_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id_pedido`);

--
-- Filtros para la tabla `venta`
--
ALTER TABLE `venta`
  ADD CONSTRAINT `venta_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id_pedido`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
