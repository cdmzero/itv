-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 10-03-2020 a las 18:43:57
-- Versión del servidor: 10.4.6-MariaDB
-- Versión de PHP: 7.1.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `itv`
--
CREATE DATABASE IF NOT EXISTS `itv` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `itv`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `coches`
--

DROP TABLE IF EXISTS `coches`;
CREATE TABLE `coches` (
  `id` int(11) NOT NULL,
  `matricula` text COLLATE latin1_spanish_ci NOT NULL,
  `marca` text COLLATE latin1_spanish_ci NOT NULL,
  `modelo` text COLLATE latin1_spanish_ci NOT NULL,
  `combustible` text COLLATE latin1_spanish_ci NOT NULL,
  `titular` text COLLATE latin1_spanish_ci NOT NULL,
  `dni` text COLLATE latin1_spanish_ci NOT NULL,
  `telefono` text COLLATE latin1_spanish_ci NOT NULL,
  `fecha_cita` text COLLATE latin1_spanish_ci NOT NULL,
  `imagen` text COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `coches`
--

INSERT INTO `coches` (`id`, `matricula`, `marca`, `modelo`, `combustible`, `titular`, `dni`, `telefono`, `fecha_cita`, `imagen`) VALUES
(11, '1234ABC', 'Mercedes', 'Exclusive', 'Gas', 'Jose Funez', '05937585C', '626909589', '09/04/2020', '941658972ca56100fa2d36e436c2bc01.jpeg'),
(13, '1233ABC', 'Mercedes', 'C220', 'Gas', 'Jose Funez', '05937585C', '626909589', '11/03/2020', '8a603298383b4b8db8572c3880fe3851.jpeg'),
(14, '1234ABC', 'Mercedes', 'E21', 'Gas', 'Jose Funez', '05937585C', '626909589', '11/03/2020', '95025d242df25136be00fac55bce37ae.jpeg'),
(15, '1233ABC', 'Mercedes', 'e 121', 'Gasolina', 'Jose Funez', '05937585C', '656267123', '12/03/2020', '70286fafafb72923cef2ee977dff9e08.jpeg'),
(16, '1234ABC', 'Seat', 'Panda', 'Propano', 'Jose Funez', '05937585C', '626789045', '01/01/2101', 'ba609cf354f6d53f19c31c9da30b3b82.jpeg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `email` text COLLATE utf8_spanish_ci NOT NULL,
  `password` text COLLATE utf8_spanish_ci NOT NULL,
  `tipo` text COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `email`, `password`, `tipo`) VALUES
(1, 'admin@admin.com', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', 'admin'),
(2, 'pepe@pepe.com', '7c9e7c1494b2684ab7c19d6aff737e460fa9e98d5a234da1310c97ddf5691834', 'normal');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `coches`
--
ALTER TABLE `coches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `coches`
--
ALTER TABLE `coches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
