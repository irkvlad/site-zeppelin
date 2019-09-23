-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Мар 23 2016 г., 02:17
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
-- Структура таблицы `jos_zepp_designworked_cat`
--

CREATE TABLE IF NOT EXISTS `jos_zepp_designworked_cat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(65) DEFAULT NULL,
  `comment` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45 ;

--
-- Дамп данных таблицы `jos_zepp_designworked_cat`
--

INSERT INTO `jos_zepp_designworked_cat` (`id`, `name`, `comment`) VALUES
(1, 'наклейка', ''),
(3, 'распечатка,(листовки, буклеты)', ''),
(4, 'полноцвет, включая UF', ''),
(5, 'номерки', ''),
(6, 'таблички', ''),
(7, 'штендеры', ''),
(8, 'стенды', ''),
(9, 'световые короба', ''),
(10, 'вывески', ''),
(11, 'стелы, указатели', ''),
(12, 'входные группы', ''),
(13, 'неон', ''),
(14, 'сувенирка-фигуры-нестандарт', ''),
(15, 'столярка', ''),
(16, 'композитные фасады', ''),
(17, 'маркизы+летние кафе', ''),
(18, 'киоски разные', ''),
(19, 'мебель ЛДСП', ''),
(20, 'искуственный камень', ''),
(21, 'подсветка зданий', ''),
(22, 'стеклопластик', ''),
(23, 'пенопласт всякий', ''),
(24, 'экраны, строки', ''),
(25, 'тросовые системы', ''),
(26, 'vista', ''),
(27, 'оцинковка', ''),
(28, 'теплицы-парники', ''),
(29, 'формовка', ''),
(30, 'праздничное и иное оформление среды', ''),
(31, 'оклейка окраска поверхностей', ''),
(32, 'проекты СРО. Согласования ( расчеты)', ''),
(33, 'монтажи межгород', ''),
(34, 'фрезер-лазер', ''),
(35, 'продажа диодов, air sistem', ''),
(36, 'ремонт вывесок', ''),
(37, 'отделка, освещение интерьеров', ''),
(38, 'креатив, консультирование', ''),
(39, 'вышка, доставка', ''),
(40, 'фонтаны *(водопады)', ''),
(41, 'роспись стен', ''),
(42, 'бани+сваи', ''),
(43, 'зашита вывесок', ''),
(44, 'эскизы', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;