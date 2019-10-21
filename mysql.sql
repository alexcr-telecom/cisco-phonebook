-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Окт 21 2019 г., 15:00
-- Версия сервера: 5.7.27-0ubuntu0.16.04.1
-- Версия PHP: 7.0.33-0ubuntu0.16.04.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `cisco`
--

-- --------------------------------------------------------

--
-- Структура таблицы `contact`
--

CREATE TABLE `contact` (
  `id` mediumint(9) NOT NULL,
  `firstname` varchar(20) DEFAULT NULL,
  `middlename` varchar(7) DEFAULT NULL,
  `lastname` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `contact`
--

INSERT INTO `contact` (`id`, `firstname`, `middlename`, `lastname`) VALUES
(1, 'Alex', 'Ababii', 'Ababii'),
(2, 'Baba', 'gaga', 'gaga');

-- --------------------------------------------------------

--
-- Структура таблицы `contactinfo`
--

CREATE TABLE `contactinfo` (
  `contact_id` mediumint(9) NOT NULL,
  `type` enum('HomePhone','WorkPhone','MobilePhone','OtherPhone','Email','Other') DEFAULT 'HomePhone',
  `info` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `contactinfo`
--

INSERT INTO `contactinfo` (`contact_id`, `type`, `info`) VALUES
(1, 'MobilePhone', '123123123'),
(2, 'HomePhone', '456745674567');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`),
  ADD KEY `firstname` (`firstname`),
  ADD KEY `lastname` (`lastname`);

--
-- Индексы таблицы `contactinfo`
--
ALTER TABLE `contactinfo`
  ADD PRIMARY KEY (`contact_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `contact`
--
ALTER TABLE `contact`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `contactinfo`
--
ALTER TABLE `contactinfo`
  ADD CONSTRAINT `contactinfo_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

