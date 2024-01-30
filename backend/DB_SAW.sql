-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Gen 30, 2024 alle 00:26
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `saw`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `blog`
--

CREATE TABLE `blog` (
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `blog`
--

INSERT INTO `blog` (`post_id`, `user_id`, `content`, `created_at`) VALUES
(105, 2, '<h1 style=\"text-align: center;\">First post</h1>', '2024-01-29 21:13:43'),
(106, 24, '<p>Sono appena diventato un moderatore.<br><em>Ciao!</em><br><br></p>', '2024-01-29 21:16:52'),
(107, 2, '<h1 style=\"text-align: center;\">Let\'s public</h1>\r\n<p>Any post</p>', '2024-01-29 22:51:51'),
(108, 24, '<h1 style=\"text-align: center;\"><em><span style=\"text-decoration: underline;\">Search bar</span></em></h1>\r\n<p style=\"text-align: center;\">Implemented</p>', '2024-01-29 23:50:28'),
(109, 24, '<p>Mancherebbe solamente la navbar personalizzata a seconda dell\'utente</p>', '2024-01-29 23:51:20'),
(110, 24, '<h1 style=\"text-align: center;\">Consiglio:</h1>\r\n<p>guardare film et</p>\r\n<p>&nbsp;</p>', '2024-01-29 23:52:39'),
(111, 24, '<p>et</p>', '2024-01-29 23:52:56'),
(112, 24, '<h1 style=\"text-align: center;\">Public your ideas</h1>\r\n<p style=\"text-align: center;\">etelefono</p>', '2024-01-29 23:53:13'),
(113, 24, '<h1 style=\"text-align: center;\">et elefonando io</h1>\r\n<p style=\"text-align: center;\">Using this form</p>', '2024-01-29 23:54:04');

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `firstname` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Moderator','Viewer','Blocked') NOT NULL DEFAULT 'Viewer',
  `instagram` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`user_id`, `firstname`, `lastname`, `email`, `password`, `role`, `instagram`) VALUES
(2, 'Andreo', 'Cattarinich', 'andy.cattarinich@gmail.com', '$2y$10$U7EV48dAY4qyinoKQZvZkuLt2tRgYRSei5EV9RmE03q4U6sKEllmi', 'Admin', 'andre.slashino'),
(24, 'Nome', 'Cognome', 'nome@cognome.com', '$2y$10$YKkXPt.oAuy7wJorVwqqXOmpphd0heJR16LzInzVsmmk2NKG5LEz.', 'Moderator', 'my_instagram'),
(28, 'Mario', 'Rossi', 'mario@rossi.com', '$2y$10$4lXBpSuBmkMPe3Iq4aMcZu6aT0IYY/I6y7JFx63SiO57iKxz/k8Qq', 'Moderator', 'null'),
(30, 'Giuseppe', 'Laurenti', 'giuseppe@gmail.com', '$2y$10$0yWUovhjl3xi.MLcoMgYs.EU4Vp8hJk7I0K7urx7wNvWxKQlE2RsW', 'Viewer', 'null');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`post_id`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `Email` (`email`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `blog`
--
ALTER TABLE `blog`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
