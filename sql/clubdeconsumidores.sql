-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 15-01-2019 a las 03:42:24
-- Versión del servidor: 5.7.17-log
-- Versión de PHP: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `clubdeconsumidores`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cupones`
--

CREATE TABLE `cupones` (
  `id` bigint(20) NOT NULL,
  `cupon` varchar(20) NOT NULL,
  `id_proveedor` bigint(20) NOT NULL,
  `id_socio` bigint(20) NOT NULL,
  `status` varchar(8) NOT NULL,
  `factura` varchar(20) NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `tipopremio` varchar(10) NOT NULL,
  `premio` decimal(12,2) NOT NULL,
  `socio` tinyint(1) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `cupones`
--

INSERT INTO `cupones` (`id`, `cupon`, `id_proveedor`, `id_socio`, `status`, `factura`, `monto`, `tipopremio`, `premio`, `socio`, `email`, `telefono`, `nombres`, `apellidos`) VALUES
(1, 'CUPON', 1, 2, 'Generado', '45645', '45455.00', 'Descuento', '10.00', 0, '', '', '', ''),
(2, 'CUPON', 1, 3, 'Generado', 'A123456', '12345.00', 'Descuento', '10.00', 0, '', '', '', ''),
(3, 'CUPON', 1, 3, 'Generado', '456125', '654321.00', 'Descuento', '10.00', 0, '', '', '', ''),
(4, 'CUPON', 1, 5, 'Generado', '45652', '123.00', 'Descuento', '10.00', 0, 'lore0303@gmail.com', '04545', 'asdadas', 'qweqweqw'),
(5, 'CUPON', 1, 5, 'Generado', '45652', '123.00', 'Descuento', '10.00', 0, 'lore0303@gmail.com', '04545', 'asdadas', 'qweqweqw'),
(6, 'CUPON', 1, 5, 'Generado', '45652', '123.00', 'Descuento', '10.00', 0, 'lore0303@gmail.com', '04545', 'asdadas', 'qweqweqw'),
(7, 'CUPON', 1, 5, 'Generado', '45652', '123.00', 'Descuento', '10.00', 0, 'lore0303@gmail.com', '04545', 'asdadas', 'qweqweqw'),
(8, 'CUPON', 1, 5, 'Generado', '45652', '123.00', 'Descuento', '10.00', 0, 'lore0303@gmail.com', '04545', 'asdadas', 'qweqweqw'),
(9, 'CUPON', 1, 5, 'Generado', '45652', '123.00', 'Descuento', '10.00', 0, 'lore0303@gmail.com', '04545', 'asdadas', 'qweqweqw'),
(10, 'CUPON', 1, 5, 'Generado', '45652', '123.00', 'Descuento', '10.00', 1, 'lore0303@gmail.com', '04545', 'asdadas', 'qweqweqw'),
(11, 'CUPON', 1, 5, 'Generado', '45652', '123.00', 'Descuento', '10.00', 0, 'lore0303@gmail.com', '04545', 'asdadas', 'qweqweqw'),
(12, 'CUPON', 1, 6, 'Generado', '12345', '123456.00', 'Descuento', '10.00', 1, 'dasdasd@wewe.ee', '45645', 'sdsdasd', 'weqweqw'),
(13, 'CUPON', 1, 6, 'Generado', '1234', '123456.00', 'Descuento', '10.00', 1, 'dasdasd@wewe.ee', '45645', 'sdsdasd', 'weqweqw'),
(14, 'CUPON', 1, 6, 'Generado', '123', '12345.00', 'Descuento', '10.00', 1, 'dasdasd@wewe.ee', '4564', 'wewe', 'wweqwe'),
(15, 'CUPON', 1, 8, 'Generado', '555', '123.00', 'Descuento', '10.00', 0, 'erer@wewe.ww', '0424', 'dasdas', 'qweqwe'),
(16, 'CUPON', 1, 8, 'Generado', '555', '123.00', 'Descuento', '10.00', 0, 'erer@wewe.ww', '0424', 'dasdas', 'qweqwe'),
(17, 'CUPON', 1, 8, 'Generado', '5553', '123.00', 'Descuento', '10.00', 0, 'erer@wewe.ww', '0424', 'dasdas', 'qweqwe');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` bigint(20) NOT NULL,
  `nombre` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `logo` varchar(100) NOT NULL,
  `direccion` varchar(250) NOT NULL,
  `rif` varchar(10) NOT NULL,
  `contacto` varchar(250) NOT NULL,
  `telefono` varchar(250) NOT NULL,
  `categoria` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id`, `nombre`, `email`, `logo`, `direccion`, `rif`, `contacto`, `telefono`, `categoria`) VALUES
(1, 'Mr. Falafel', 'sgcvzla@gmail.com', 'mrf.jpg', 'Concepto La Viña', 'J123456789', 'Piter Ayrout', '04244071820', 'Alimentos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `socios`
--

CREATE TABLE `socios` (
  `id` bigint(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `socios`
--

INSERT INTO `socios` (`id`, `email`, `telefono`, `nombres`, `apellidos`) VALUES
(1, 'hasjdhkjads@ddd.com', '22323223232323', 'bbbCCCCCCCCCCC', 'apellidos'),
(2, 'asas@dsds.fff', '4564654', 'asdasd', 'dadad'),
(3, 'soluciones2000@gmail.com', '04144802725', 'Luis Antonio', 'Rodríguez Estrada'),
(4, '', '', '', ''),
(5, 'lore0303@gmail.com', '04545', 'asdadas', 'qweqweqw'),
(6, 'dasdasd@wewe.ee', '45645', 'sdsdasd', 'weqweqw'),
(7, 'qwqee@www.dd', '0414', 'weqeqw', 'asdadsa'),
(8, 'erer@wewe.ww', '0424', 'dasdas', 'qweqwe'),
(9, 'ererer@dfdfd.dd', '0424', 'aasda', 'kkjlkjl');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `_categoria`
--

CREATE TABLE `_categoria` (
  `decripcion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `_categoria`
--

INSERT INTO `_categoria` (`decripcion`) VALUES
('Alimentos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `_parametros`
--

CREATE TABLE `_parametros` (
  `empresa` varchar(100) NOT NULL,
  `nombresistema` varchar(100) NOT NULL,
  `logosistema` varchar(100) NOT NULL,
  `rif` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `_parametros`
--

INSERT INTO `_parametros` (`empresa`, `nombresistema`, `logosistema`, `rif`) VALUES
('SGC Consultores C.A.', 'Club de Consumidores', 'sgc.jpg', 'J402424418');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `_statuscupon`
--

CREATE TABLE `_statuscupon` (
  `status` varchar(9) NOT NULL,
  `descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `_statuscupon`
--

INSERT INTO `_statuscupon` (`status`, `descripcion`) VALUES
('generado', 'El socio registró la factura y generó el cupón, falta que vaya al punto de venta a usarlo'),
('usado', 'El socio fue al punto de venta y usó el cupón para reclamar su premio'),
('anulado', 'El cupón fue anulado, no es válido');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `_tipopremio`
--

CREATE TABLE `_tipopremio` (
  `tipopremio` varchar(10) NOT NULL,
  `descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `_tipopremio`
--

INSERT INTO `_tipopremio` (`tipopremio`, `descripcion`) VALUES
('porcentaje', 'Porcentaje de descuento en la compra donde se usa el cupón'),
('monto', 'Monto fijo de descuento en la compra donde se usa el cupón'),
('producto', 'Se entrega un producto específico cuando se usa el cupón');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cupones`
--
ALTER TABLE `cupones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `socios`
--
ALTER TABLE `socios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cupones`
--
ALTER TABLE `cupones`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `socios`
--
ALTER TABLE `socios`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
