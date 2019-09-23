-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Июл 05 2016 г., 14:45
-- Версия сервера: 5.5.25
-- Версия PHP: 5.2.12

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `zepp`
--

-- --------------------------------------------------------

--
-- Структура таблицы `jos_zepp_polnocvet`
--

CREATE TABLE IF NOT EXISTS `jos_zepp_polnocvet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_load` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `link` text NOT NULL,
  `name_file` varchar(160) NOT NULL,
  `file` varchar(160) NOT NULL,
  `manager_id` int(11) NOT NULL,
  `set_date` date DEFAULT NULL,
  `teh_admin` int(11) DEFAULT NULL,
  `realis_date` date DEFAULT NULL,
  `set_status` date DEFAULT NULL,
  `brack_text` text,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0-не выполнен;1-выполнен;2-брак',
  `project_id` int(11) DEFAULT NULL,
  `ploschad` decimal(10,2) DEFAULT NULL,
  `stanok` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
