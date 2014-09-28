-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 15-12-2010 a las 11:17:06
-- Versión del servidor: 5.1.44
-- Versión de PHP: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `spora_real`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `backupdireccions`
--

CREATE TABLE `backupdireccions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `barri` varchar(255) NOT NULL,
  `via` varchar(5) NOT NULL,
  `carrer` varchar(255) NOT NULL,
  `numero` int(4) NOT NULL,
  `ordrePis` int(11) NOT NULL,
  `text` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `backupdireccions`
--

