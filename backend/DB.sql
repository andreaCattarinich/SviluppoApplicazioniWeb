-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Gen 28, 2024 alle 21:35
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
  `user_email` varchar(30) NOT NULL,
  `content` longtext NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `blog`
--

INSERT INTO `blog` (`post_id`, `user_email`, `content`, `created_at`) VALUES
(97, 'andy.cattarinich@gmail.com', '<h1 style=\"text-align: center;\">First post</h1>', '2024-01-28 11:42:05'),
(98, 'andy.cattarinich@gmail.com', '<p>Adesso modificher&ograve; tutti i JSONResponse().<br><br><em>Ci vediamo dopo!</em></p>', '2024-01-28 12:40:12'),
(99, 'andy.cattarinich@gmail.com', '<h1 style=\"text-align: center;\">Devo mettere apposto i redirect...</h1>', '2024-01-28 20:17:00'),
(100, 'andy.cattarinich@gmail.com', '<p>Finalmente ho messo apposto i redirect</p>', '2024-01-28 21:16:27');

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
  `role` enum('Admin','Moderator','Editor','Blocked') NOT NULL DEFAULT 'Editor',
  `instagram` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`user_id`, `firstname`, `lastname`, `email`, `password`, `role`, `instagram`) VALUES
(1, 'Alessia', 'S', 'alessia@gmail.com', '$2y$10$FaElGm2/Pju/ILkDcMqIdeqAskcxP0iV0SfNoz.L0HWz5vFjWtaEi', 'Blocked', NULL),
(2, 'Andrea', 'Cattarinich', 'andy.cattarinich@gmail.com', '$2y$10$U7EV48dAY4qyinoKQZvZkuLt2tRgYRSei5EV9RmE03q4U6sKEllmi', 'Admin', 'andre.slashino'),
(3, 'Ygsmcunxe', 'Xtozwbseu', 'biezt@ibtamgpfnj.fsg', '$2y$10$gkMoyZ0w8oMFYkCY2xJxjezmFzfA3AAwTmuUc4XooO4MN3ZHDhZ2W', 'Editor', NULL),
(4, 'Prove Per', 'Cookie', 'jwt@gmail.com', '$2y$10$.n6DyfDE6QKskO2lqpda.eE.5E1.pIIIXfvzF1z/yZrFX62heqtfq', 'Editor', NULL),
(5, 'Rebeca Del Pilar', 'Puno', 'rebe@gmail.com', '$2y$10$ospSbktghTyRde.RIroiiuE9vZWRFo2JZfeXpIwGpG8XE2/Uacu3G', 'Moderator', NULL),
(12, 'Luca', 'Zappaterra', 'luca@zappa.it', '$2y$10$NqLdwSnxfhGhWuEIc3NUaeg4u4muSdYvkRypCvqr21zg5fs7ZEctm', 'Editor', NULL),
(15, 'Andrea', 'Cattarinich', 'andy.cattarinich2@gmail.com', '$2y$10$u96pwdLQfl65SFgsz/ZePuJXnoO0LHwAL4j0oMB84qDU62WW7djuy', 'Blocked', NULL),
(17, 'Andrea', 'Cattarinich', 'andy.cattarinich3@gmail.com', '$2y$10$OdkPuna2qeflAOh50pI5XO2yqmNk8jSYYWkT0EBh18doe1OGuITBO', 'Editor', NULL),
(18, 'Andrea', 'Cattarinich', 'andy.cattarinich4@gmail.com', '$2y$10$sAc6pw6TkPDRYjI36FqaqOZ3RKOkPqEQb1tmmvu0UW5JUeISD.rQW', 'Editor', NULL),
(19, 'Andrea', 'Cattarinich', 'andy.cattarinich5@gmail.com', '$2y$10$gGc6I2ulSi8.T9yLBcpC5.j8yeWt7ytUhFuu9QM8B2HM3PgTR0rRq', 'Blocked', NULL),
(20, 'Andrea', 'Cattarinich', 'andy.cattarinich6@gmail.com', '$2y$10$s13GY3N9PGLIsZF/.BNt2ejIpZ5PlB4/gqA.Kw1oYKcxqlgpL9Cjm', 'Blocked', NULL),
(21, 'Luca', 'Marchesano', 'luca@gmail.com', '$2y$10$kYTVbYTG5i0jBLo9w65laOTbMFbnUQKsZ5KVTA/mXLjiR4sQf0A4C', 'Editor', NULL),
(22, 'Lrafdzisv', 'Ildmpksce', 'euazs@yptczwjelh.zuf', '$2y$10$fyH9XlVqafgC7gxUaCdE..K8xGj3bO9VGAHAMyhLQMxIgHR4V57nW', 'Editor', NULL);

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
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
