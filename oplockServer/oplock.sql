-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Май 28 2023 г., 09:22
-- Версия сервера: 8.0.33
-- Версия PHP: 8.2.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `oplock`
--

-- --------------------------------------------------------

--
-- Структура таблицы `applications`
--

CREATE TABLE `applications` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `adress` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `latitude` float NOT NULL,
  `longitude` float NOT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `time` tinyint(1) NOT NULL,
  `dopInfo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `taken` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `applications`
--

INSERT INTO `applications` (`id`, `user_id`, `adress`, `latitude`, `longitude`, `date`, `time`, `dopInfo`, `status`, `taken`) VALUES
(1, 2, 'улица Борисова, 14А, Красноярск', 55.9932, 92.7917, '2023-05-19 19:48:42', 1, 'Такой-то подъезд, такая-то квартира.', 1, NULL),
(2, 2, 'улица Академика Киренского, 26к1, Красноярск', 55.9944, 92.7976, '2023-05-19 19:48:54', 2, 'Дополнительная информация Дополнительная информация Дополнительная информация Дополнительная информация Дополнительная информация Дополнительная информация Дополнительная информация Дополнительная  0', 2, 3),
(3, 2, 'adress', 11, 11, '2023-05-26 03:04:57', 1, '123141512313', 1, NULL),
(8, 59, 'test', 11, 11, '2023-05-26 12:23:46', 1, '1232123', 1, NULL),
(9, 2, 'test', 11, 11, '2023-05-26 13:46:29', 2, '123', 4, NULL),
(10, 2, 'test', 11, 11, '2023-05-26 13:46:55', 1, '123', 1, NULL),
(11, 2, 'ул. Академика Киренского, 26А, Красноярск, Красноярский край, Россия, 660074', 55.9967, 92.7977, '2023-05-28 15:52:44', 1, '76', 1, NULL),
(12, 2, 'ул. Академика Киренского, 26, Красноярск, Красноярский край, Россия, 660074', 55.9944, 92.7977, '2023-05-28 15:59:05', 1, 'аудитория 1 16', 4, NULL),
(13, 2, 'ул. Перенсона, 2, Красноярск, Красноярский край, Россия, 660049', 56.0087, 92.8685, '2023-05-28 16:10:38', 1, '', 4, NULL),
(14, 2, 'ул. Академика Киренского, 26, Красноярск, Красноярский край, Россия, 660074', 55.9956, 92.7978, '2023-05-28 16:13:00', 2, '', 4, NULL),
(15, 2, 'ул. Ленинградская, 11, 10, Красноярск, Красноярский край, Россия, 660074', 56.0002, 92.7801, '2023-05-28 16:13:31', 1, '', 1, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `complaints`
--

CREATE TABLE `complaints` (
  `id` int UNSIGNED NOT NULL,
  `userFrom` int UNSIGNED NOT NULL,
  `userTo` int UNSIGNED NOT NULL,
  `application` int UNSIGNED NOT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `text` varchar(500) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `complaints`
--

INSERT INTO `complaints` (`id`, `userFrom`, `userTo`, `application`, `date`, `text`) VALUES
(1, 2, 3, 1, '0000-00-00 00:00:00', 'Тестовая жалоба!'),
(2, 3, 4, 1, '2023-05-22 12:41:19', 'тестовая жалоба 2');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `username` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(12) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `registrationDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `complainsTo` int NOT NULL DEFAULT '0',
  `complainsFrom` int DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `hash` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ip` int UNSIGNED DEFAULT NULL,
  `banned` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `phone`, `registrationDate`, `complainsTo`, `complainsFrom`, `status`, `hash`, `ip`, `banned`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', '', NULL, '2023-05-10 11:07:45', 0, 0, 3, '4248d1424840ea2aafee93c63585e93d', 2130706433, 0),
(2, 'user', 'ee11cbb19052e40b07aac0ca060c23ee', NULL, '88005553535', '2023-05-10 11:25:11', 0, 0, 0, 'a95207bac49a042735b876983fa2d0ad', 3232235622, 0),
(3, 'worker', 'b61822e8357dcaff77eaaccf348d9134', NULL, NULL, '2023-05-10 11:25:45', 0, 0, 1, '1f6273814cf0865e53a7728b04e12c2c', 3232270677, 0),
(4, 'manager', '1d0258c2440a8d19e716292b231e3190', NULL, NULL, '2023-05-10 11:26:18', 0, 0, 2, '8b2858451f3587f2ea5e5bf745ae32bf', 2130706433, 0),
(53, 'test', '098f6bcd4621d373cade4e832627b4f6', NULL, '89995551213', '2023-05-12 14:41:34', 0, 0, 0, 'bf99410694fd852a198138a9b2ab45a2', 3232235786, 0),
(55, 'qqqqqq', '343b1c4a3ea721b2d640fc8700db0f36', 'r@r.r', '+79990001234', '2023-05-13 14:21:51', 0, 0, 0, 'eb12e63c62027245f9f66c2538a562c2', 3232235624, 0),
(56, 'meneger1', '112d9377e1d99b65c16a1c3dca35e918', NULL, NULL, '2023-05-18 13:05:06', 0, 0, 2, '23ada4ad421f2c02332b91da6bcd93de', 2130706433, 0),
(57, 'test111', '4061863caf7f28c0b0346719e764d561', 'test@mail.ru', NULL, '2023-05-20 14:23:42', 0, 0, 0, NULL, NULL, 0),
(58, 'test222', '4edefd1254ebf8bdb04bf7c208a1f347', 'test2@mail.com', '88005553535', '2023-05-20 14:27:51', 0, 0, 0, 'a65392ce14d4a6cd41d6e975c1b19719', 3232235786, 0),
(59, 'test333', '3aaa4ff6fa71d98282e0b2e0c49d4066', 'q@q.q', '89998887766', '2023-05-26 12:23:13', 0, 0, 0, '644fdea1f79070412094c3b9f5422f80', 3232270677, 0),
(60, 'testmaster', '1e3adbfeb1bd10f436bba4cad73e6ee4', NULL, NULL, '2023-05-26 12:28:57', 0, 0, 1, 'fa633ec6e81313107dc82057e96e1658', 3232270677, 0);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `taken` (`taken`);

--
-- Индексы таблицы `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userFrom` (`userFrom`),
  ADD KEY `userTo` (`userTo`),
  ADD KEY `application` (`application`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`taken`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`userFrom`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `complaints_ibfk_2` FOREIGN KEY (`userTo`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `complaints_ibfk_3` FOREIGN KEY (`application`) REFERENCES `applications` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
