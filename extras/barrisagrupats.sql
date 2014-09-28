-- phpMyAdmin SQL Dump
-- version 3.3.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 29, 2010 at 11:06 PM
-- Server version: 5.1.52
-- PHP Version: 5.3.3-pl1-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `spora`
--

-- --------------------------------------------------------

--
-- Table structure for table `barrisagrupats`
--

CREATE TABLE IF NOT EXISTS `barrisagrupats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grup` int(11) NOT NULL,
  `barri` int(11) NOT NULL,
  `actiu` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `barrisagrupats`
--

INSERT INTO `barrisagrupats` (`id`, `grup`, `barri`, `actiu`) VALUES
(1, 1, 9, 1),
(2, 1, 10, 1),
(3, 1, 18, 1);
