-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Creato il: Set 03, 2025 alle 14:47
-- Versione del server: 8.0.36
-- Versione PHP: 8.0.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `my_olivettiopenday`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `Configurazione`
--

CREATE TABLE `Configurazione` (
  `chiave` varchar(50) NOT NULL,
  `valore` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `Configurazione`
--

INSERT INTO `Configurazione` (`chiave`, `valore`) VALUES
('gioco_avviato', '0'),
('inizio_gioco', ''),
('durata_gioco', ''),
('mostra_risultati', '0'),
('mostra_spiegazione', '0'),
('livello_spiegazione', '1'),
('fine_generale', '0');

-- --------------------------------------------------------

--
-- Struttura della tabella `Livelli`
--

CREATE TABLE `Livelli` (
  `id` int NOT NULL,
  `titolo` varchar(100) NOT NULL,
  `descrizione` text NOT NULL,
  `file_nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `punteggioL` int NOT NULL,
  `flag` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `Livelli`
--

INSERT INTO `Livelli` (`id`, `titolo`, `descrizione`, `file_nome`, `punteggioL`, `flag`) VALUES
(1, 'The Legend of the Hidden Code', 'Nel mondo di Hyrule non tutto è come appare. Alcuni segreti si nascondono dietro la facciata proprio come un cuore segreto dietro una parete di mattoni. Non limitarti a guardare; scava nel profondo per trovare la verità.', 'the_legend_of_hidden_code.png', 100, 'flag{7h3_m4573r_5w0rd}'),
(3, 'There Is No Spoon', 'Neo ha ricevuto un messaggio segreto dal mondo reale ma gli Agenti lo hanno nascosto nel codice della simulazione. Solo chi sa dove guardare potrà trovarlo.', 'there_is_no_spoon.html', 250, 'flag{00_73_173_1}'),
(2, 'Gruppi Hacker', 'Un attacco informatico senza precedenti ha compromesso le nostre difese digitali. Un nuovo gruppo hacker è apparso improvvisamente sulla scena, sferrando un colpo potente e lasciando poche tracce dietro di sé. I nostri analisti hanno cercato di identificare gli autori, ma invano: ogni pista si è dissolta rapidamente. Siamo riusciti ad estrarre i nomi di tutti i gruppi hacker che hanno effettuato attacchi negli ultimi anni, sicuramente al suo interno c\'è anche il gruppo che stiamo cercando. La vostra missione è cruciale: analizzate il documento e scoprite chi si nasconde dietro questo nuovo pericolo digitale prima che colpisca di nuovo. \nLa flag è così composta: flag{Nome_Gruppo_Hacker}', 'gruppi_hacker.csv', 150, 'flag{Omega_Group}'),
(4, 'CSV Trap', 'Abbiamo trovato un file di cui non si capisce il contenuto, riesci a capirlo?\n\n', 'flag.csv', 350, 'flag{c5v_h1dd3n}'),
(5, 'Key to the Boss', 'Sei riuscito a trovare quella che ritieni essere la lista delle password aziendali di una piccola impresa. Il tuo obiettivo è quello di capire quale potrebbe essere quella del CEO dell’azienda, il signor Rossi. Hai mandato un tuo collega (B) a parlare con il segretario dell’azienda per trovare delle informazioni che potrebbero esserti utili, e questo è quello che ti riporta.', 'Dialogo_con_il_guardiano_del_CEO.pdf,Password_nascoste.pdf', 450, 'flag{Holly_&_Benji_1}'),
(6, 'Mystery Code', 'Un partecipante di Squid Game ha trovato un piccolo biglietto nascosto sotto il suo letto scritto in un codice incomprensibile. Il biglietto sembra contenere un indizio segreto sul prossimo gioco che lui e gli altri partecipanti dovranno affrontare ma nessuno sa come leggerlo.\n\nIl messaggio nel biglietto è: synt{7u3_134f7_p4a_j4gpu_7u3_s10j_j4ea3!}\nRiuscirai a ricostruire ciò che è stato alterato?', '', 700, 'flag{7h3_134s7_c4n_w4tch_7h3_f10w_w4rn3!}');

-- --------------------------------------------------------

--
-- Struttura della tabella `LivelliCompletati`
--

CREATE TABLE `LivelliCompletati` (
  `id` int NOT NULL,
  `squadra_nome` varchar(100) NOT NULL,
  `livello_id` int NOT NULL,
  `completato_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `Squadra`
--

CREATE TABLE `Squadra` (
  `id` int NOT NULL,
  `nome` varchar(20) NOT NULL,
  `passwd` varchar(8) NOT NULL,
  `punti` int NOT NULL DEFAULT '0',
  `test_finito` tinyint(1) NOT NULL DEFAULT '0',
  `tempo_fine` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `Squadra`
--

INSERT INTO `Squadra` (`id`, `nome`, `passwd`, `punti`, `test_finito`, `tempo_fine`) VALUES
(1, 'admin', '123', 0, 0, NULL),
(28, 'RansomCrew', '8fG2dWqL', 0, 0, NULL),
(30, 'PacketSniffers', 'u1QmE6zB', 0, 0, NULL),
(32, 'MalwareMunchers', 'J7bL3tQw', 0, 0, NULL),
(34, 'BruteForceBar', 'dE3zKw1Y', 0, 0, NULL),
(35, 'SocialHackers', 'Wq9Lm3eT', 0, 0, NULL);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `Configurazione`
--
ALTER TABLE `Configurazione`
  ADD PRIMARY KEY (`chiave`);

--
-- Indici per le tabelle `Livelli`
--
ALTER TABLE `Livelli`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `LivelliCompletati`
--
ALTER TABLE `LivelliCompletati`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `squadra_nome` (`squadra_nome`,`livello_id`);

--
-- Indici per le tabelle `Squadra`
--
ALTER TABLE `Squadra`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `Livelli`
--
ALTER TABLE `Livelli`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT per la tabella `LivelliCompletati`
--
ALTER TABLE `LivelliCompletati`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT per la tabella `Squadra`
--
ALTER TABLE `Squadra`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
