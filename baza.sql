-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 24, 2025 at 05:59 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `schola`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `repertoire` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `repertoire`, `created_at`) VALUES
(5, 'Święto św. Stanisława Kostki, zakonnika', 'msza', '2025-09-18', NULL, '2025-08-23 23:39:45'),
(6, 'Dwudziesta Trzecia Niedziela zwykła', 'msza', '2025-09-07', NULL, '2025-08-24 00:52:29');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `event_parts`
--

CREATE TABLE `event_parts` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `part_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `event_parts`
--

INSERT INTO `event_parts` (`id`, `event_id`, `part_name`) VALUES
(2, 5, 'Wejście'),
(3, 5, 'Kyrie'),
(4, 5, 'Przygotowanie darów'),
(5, 5, 'Sanctus'),
(6, 5, 'Agnus Dei'),
(7, 5, 'Komunia'),
(8, 5, 'Dziękczynienie'),
(9, 5, 'Rozesłanie'),
(10, 6, 'Wejście'),
(11, 6, 'Przygotowanie darów'),
(12, 6, 'Dziękczynienie'),
(13, 6, 'Rozesłanie');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `event_songs`
--

CREATE TABLE `event_songs` (
  `id` int(11) NOT NULL,
  `part_id` int(11) NOT NULL,
  `tytul` varchar(255) NOT NULL,
  `hymn_number` varchar(50) DEFAULT NULL,
  `pdf` varchar(255) DEFAULT NULL,
  `audio` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `event_songs`
--

INSERT INTO `event_songs` (`id`, `part_id`, `tytul`, `hymn_number`, `pdf`, `audio`) VALUES
(2, 2, 'Przed wielu laty - o św. Stanisławie Kostce', '25', '', 'null'),
(3, 4, 'Aniele ziemski bez winy', '', '', 'null'),
(4, 5, 'Święty, Święty, Święty', '5', '', 'null');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `piesni`
--

CREATE TABLE `piesni` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `hymn_number` varchar(50) DEFAULT NULL,
  `pdf` varchar(255) DEFAULT NULL,
  `audio` varchar(255) DEFAULT NULL,
  `tytul` varchar(255) DEFAULT NULL,
  `okres` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `notatki` text DEFAULT NULL,
  `link_audio` varchar(255) DEFAULT NULL,
  `tekst` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `piesni`
--

INSERT INTO `piesni` (`id`, `title`, `hymn_number`, `pdf`, `audio`, `tytul`, `okres`, `status`, `notatki`, `link_audio`, `tekst`) VALUES
(1, '', '22', '', NULL, 'A wczora z wieczora', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(2, '', '22', '', NULL, 'A wczora z wieczora', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(3, '', '27', '', NULL, 'Abba Ojcze', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(4, '', '32', '', NULL, 'Amen, amen; jeden tylko jest Panem!', 'Wielkanoc (W)', 'Zapomniane', '', '', ''),
(5, '', '11', '', NULL, 'Aniele Boży', 'Okres zwykły (R)', 'Niewdrożone', 'm.: Paweł Bębenek', '', ''),
(6, '', '3', '', NULL, 'Archanioł Boży Gabryjel', 'Adwent (AB)', 'Niewdrożone', '', '', ''),
(7, '', '14', '', NULL, 'Ballada o zmartwychwstaniu', 'Wielkanoc (W)', 'Zapomniane', '', '', ''),
(8, '', '26', '', NULL, 'Barka', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(9, '', '7', '', NULL, 'Bądź pozdrowiona', 'Maryjne (M)', 'Wdrożone', '', '', ''),
(10, '', '19, 1', '', NULL, 'Będę Pana czcił', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(11, '', '27', '', NULL, 'Biada', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(12, '', '15', '', NULL, 'Błogosławieni miłosierni', 'Okres zwykły (R)', 'Niewdrożone', 'Hymn Światowych Dni Młodzieży Kraków 2016', '', ''),
(13, '', '49', '', NULL, 'Błogosławieni miłosierni', 'Okres zwykły (R)', 'Niewdrożone', 'Hymn Światowych Dni Młodzieży Kraków 2016', '', ''),
(14, '', '43', '', NULL, 'Bo jak śmierć', 'Okres zwykły (R)', 'Wdrożone', 'm.: Jacek Gałuszka\r\nLednica 2000', '', ''),
(15, '', '31', '', NULL, 'Bo jak śmierć', 'Wielkanoc (W)', 'Wdrożone', 'm.: Jacek Gałuszka\r\nLednica 2000', '', ''),
(16, '', '12', '', NULL, 'Bo miłosierny jest Pan', 'Wielkanoc (W)', 'Wdrożone', '', '', ''),
(17, '', '19, 2', '', NULL, 'Bo miłosierny jest Pan', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(18, '', '20', '', NULL, 'Bogu Ojcu chwała', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(19, '', '8, 11', '', NULL, 'Boże mój, Boże', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(20, '', '14', '', NULL, 'Bóg się narodził', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(21, '', '6', '', NULL, 'Bóg się rodzi', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(22, '', '13', '', NULL, 'Bóg tak umiłował świat', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(23, '', '8', '', NULL, 'Cały jestem Twój, Maryjo', 'Maryjne (M)', 'Niewdrożone', '', '', ''),
(24, '', '30', '', NULL, 'Chcę widzieć Cię', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(25, '', '47', '', NULL, 'Chlebie najcichszy', 'Okres zwykły (R)', 'Wdrożone', 'Wspólnota Miłości Ukrzyżowanej 2001, album \"Przybądź Płomieniu\"', '', ''),
(26, '', '17', '', NULL, 'Chlebie żywy', 'Wielkanoc (W)', 'Niewdrożone', '', '', ''),
(27, '', '42', '', NULL, 'Chodźcie, chodźcie uwielbiajmy Go', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(28, '', '30', '', NULL, 'Chrystus Pan, Boży Syn', 'Wielkanoc (W)', 'Niewdrożone', '', '', ''),
(29, '', '31', '', NULL, 'Chwalę Ciebie, Panie', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(30, '', '3', '', NULL, 'Chwała na wysokości Bogu', 'Części stałe mszy świętej (R)', 'Zapomniane', '', '', ''),
(31, '', '5', '', NULL, 'Cicha noc', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(32, '', '12', '', NULL, 'Cuda dzieją się', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(33, '', '9', '', NULL, 'Czarna Madonna', 'Maryjne (M)', 'Wdrożone', '', '', ''),
(34, '', '1', '', NULL, 'Czekam na Ciebie, Jezu mój mały', 'Adwent (AB)', 'Niewdrożone', '', '', ''),
(35, '', '31', '', NULL, 'Czy może niewiasta', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(36, '', '15', '', NULL, 'Do Serca Jezusowego', 'Wielkanoc (W)', 'Zapomniane', '', '', ''),
(37, '', '11', '', NULL, 'Do szopy, hej pasterze', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(38, '', '8', '', NULL, 'Dokąd idę', 'Maryjne (M)', 'Niewdrożone', '', '', ''),
(39, '', '19, 3', '', NULL, 'Dotknij, Panie, moich oczu', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(40, '', '30', '', NULL, 'Droga do Betlejem', 'Boże Narodzenie (AB)', 'Zapomniane', '', '', ''),
(41, '', '10', '', NULL, 'Duchu Pocieszycielu', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(42, '', '41', '', NULL, 'Duchu Święty Stworzycielu', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(43, '', '41', '', NULL, 'Duchu Święty, napełnij serce', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(44, '', '30', '', NULL, 'Duchu Święty, przyjdź i rozpal nas', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(45, '', '38', '', NULL, 'Duchu Święty, przyjdź, niech wiara', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(46, '', '10', '', NULL, 'Duchu Święty, Tchnienie Ojca', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(47, '', '7', '', NULL, 'Duszo ma Pana chwal', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(48, '', '12', '', NULL, 'Dzielmy się wiarą jak chlebem', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(49, '', '10', '', NULL, 'Dzięki Ci, Boże mój', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(50, '', '5', '', NULL, 'Dzięki Ci, Panie', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(51, '', '22', '', NULL, 'Dzięki Ci, Panie', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(52, '', '5', '', NULL, 'Dzięki Jezu', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(53, '', '7', '', NULL, 'Dzięki za Twój krzyż', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(54, '', '19, 4', '', NULL, 'Dzięki za Twój krzyż', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(55, '', '23', '', NULL, 'Dziękuję Ci', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(56, '', '19, 5', '', NULL, 'Dziękujmy Jezusowi', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(57, '', '17', '', NULL, 'Dzisiaj w Betlejem', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(58, '', '6', '', NULL, 'Dziś uwielbiam Twoje rany (droga krzyżowa)', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(59, '', '51', '', NULL, 'Dziś wesoły jest dzień', 'Okres zwykły (R)', 'Zapomniane', '', '', ''),
(60, '', '46', '', NULL, 'Gdy Pan odmienił los Syjonu (psalm 126)', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(61, '', '19', '', NULL, 'Gdy się Chrystus rodzi', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(62, '', '5', '', NULL, 'Gdy śliczna Panna', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(63, '', '11', '', NULL, 'Gdy śliczna Panna', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(64, '', '6', '', NULL, 'Gdy wpatruję się w Twą świętą twarz', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(65, '', '7', '', NULL, 'Gdybym przechodził', 'Okres zwykły (R)', 'Zapomniane', '', '', ''),
(66, '', '53', '', NULL, 'Godzien, o godzien', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(67, '', '10', '', NULL, 'Golgota', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(68, '', '36', '', NULL, 'Golgota', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(69, '', '16', '', NULL, 'Gore gwiazda', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(70, '', '28', '', NULL, 'Gore gwiazda', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(71, '', '8', '', NULL, 'Grudniowe noce', 'Boże Narodzenie (AB)', 'Zapomniane', '', '', ''),
(72, '', '28', '', NULL, 'Hosanna Synowi Dawida', 'Wielkanoc (W)', 'Wdrożone', '', '', ''),
(73, '', '16', '', NULL, 'Hymn o krzyżu', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(74, '', '29', '', NULL, 'Ile czekałem na tą chwilę', 'Wielki Post (W)', 'Zapomniane', '', '', ''),
(75, '', '8, 9', '', NULL, 'Jak drogocenna jest Twoja krew', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(76, '', '27', '', NULL, 'Jak w Betlejem', 'Boże Narodzenie (AB)', 'Zapomniane', '', '', ''),
(77, '', '57', '', NULL, 'Jeden jest tylko Pan', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(78, '', '3', '', NULL, 'Jest na świecie miłość', 'Maryjne (M)', 'Zapomniane', '', '', ''),
(79, '', '18', '', NULL, 'Jesteś cały miłosierdziem', 'Wielkanoc (W)', 'Wdrożone', '', '', ''),
(80, '', '19, 6', '', NULL, 'Jesteś cały miłosierdziem', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(81, '', '58', '', NULL, 'Jesteś radością mojego życia', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(82, '', '4', '', NULL, 'Jesteśmy ludem króla chwał', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(83, '', '42', '', NULL, 'Jesteśmy Piękni', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(84, '', '20, 12', '', NULL, 'Jezu, przemień mnie w Siebie', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(85, '', '42', '', NULL, 'Jezu, Tyś jest światłością mej duszy', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(86, '', '57', '', NULL, 'Jezu, Tyś jest światłością mej duszy', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(87, '', '8, 2', '', NULL, 'Jezu, Tyś jest światłością mej duszy', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(88, '', '20, 10', '', NULL, 'Jezu, ufam Tobie, Jezu, kocham Cię!', 'Wielki Post (W)', 'Zapomniane', '', '', ''),
(89, '', '13', '', NULL, 'Jezus - najwyższe Imię', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(90, '', '20, 9', '', NULL, 'Jezus daje nam zbawienie', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(91, '', '7', '', NULL, 'Jezus dziś przyszedł', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(92, '', '2', '', NULL, 'Jezus kocha Ciebie dziś', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(93, '', '55', '', NULL, 'Jezus zwyciężył', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(94, '', '20', '', NULL, 'Jezusa narodzonego', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(95, '', '30', '', NULL, 'Jezusa narodzonego', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(96, '', '5', '', NULL, 'Józefie, stajenki nie szukaj', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(97, '', '8, 3', '', NULL, 'Już się nie lękaj', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(98, '', '39', '', NULL, 'Karmisz mnie do syta', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(99, '', '29', '', NULL, 'Każdy spragniony', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(100, '', '41', '', NULL, 'Każdy spragniony', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(101, '', '21, 13', '', NULL, 'Kocham Ciebie Jezu', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(102, '', '23', '', NULL, 'Kolęda dla nieobecnych', 'Boże Narodzenie (AB)', 'Zapomniane', '', '', ''),
(103, '', '25', '', NULL, 'Kościół to nie tylko dom', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(104, '', '8', '', NULL, 'Krzyż nadzieją moją jest', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(105, '', '10', '', NULL, 'Krzyż, krzyż', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(106, '', '20, 11', '', NULL, 'Krzyż, krzyż', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(107, '', '58', '', NULL, 'Kto spożywa Moje Ciało', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(108, '', '1', '', NULL, 'Litania jesienna', 'Maryjne (M)', 'Wdrożone', '', '', ''),
(109, '', '2', '', NULL, 'Litania wiosenna', 'Maryjne (M)', 'Wdrożone', '', '', ''),
(110, '', '9', '', NULL, 'Lulajże, Jezuniu', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(111, '', '21, 14', '', NULL, 'Łaską jesteśmy zbawieni', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(112, '', '5', '', NULL, 'Magnificat', 'Maryjne (M)', 'Wdrożone', '', '', ''),
(113, '', '8', '', NULL, 'Maleńka miłość', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(114, '', '24', '', NULL, 'Maleńka miłość', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(115, '', '1', '', NULL, 'Marana tha! Przyjdź, Jezu Panie', 'Adwent (AB)', 'Zapomniane', '', '', ''),
(116, '', '54', '', NULL, 'Mario, czy już wiesz?', 'Okres zwykły (R)', 'Zapomniane', '', '', ''),
(117, '', '26', '', NULL, 'Mario, czy już wiesz?', 'Boże Narodzenie (AB)', 'Zapomniane', '', '', ''),
(118, '', '27', '', NULL, 'Mario, proszę, spraw', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(119, '', '42', '', NULL, 'Mario, proszę, spraw', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(120, '', '21, 15', '', NULL, 'Mario, proszę, spraw', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(121, '', '5', '', NULL, 'Maryjo, Matko mojego wezwania', 'Maryjne (M)', 'Zapomniane', '', '', ''),
(122, '', '9', '', NULL, 'Matka pod krzyżem', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(123, '', '19, 7', '', NULL, 'Matka pod krzyżem', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(124, '', '21, 16', '', NULL, 'Matko, która nas znasz', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(125, '', '1', '', NULL, 'Matko, która nas znasz (droga krzyżowa)', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(126, '', '8/9', '', NULL, 'Matko, która nas znasz (droga krzyżowa)', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(127, '', '48', '', NULL, 'Mądrość stół zastawiła obficie', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(128, '', '57', '', NULL, 'Memu Bogu, Królowi', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(129, '', '21, 21', '', NULL, 'Miłosierdzie Boże, wylewaj się na nas', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(130, '', '38', '', NULL, 'Miłości Król', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(131, '', '43', '', NULL, 'Miłość cierpliwa jest', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(132, '', '42', '', NULL, 'Miłość, którą jest Bóg w nas', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(133, '', '18', '', NULL, 'Mizerna, cicha', 'Boże Narodzenie (AB)', 'Zapomniane', '', '', ''),
(134, '', '8', '', NULL, 'Modlitwa o miłość', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(135, '', '21, 17', '', NULL, 'Może daleko jesteś od Niego', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(136, '', '13', '', NULL, 'Mój Zbawiciel', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(137, '', '50', '', NULL, 'Mów do mnie, Panie', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(138, '', '10', '', NULL, 'Myślę o Tobie', 'Boże Narodzenie (AB)', 'Zapomniane', '', '', ''),
(139, '', '7', '', NULL, 'Na drodze', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(140, '', '21, 18', '', NULL, 'Na drodze', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(141, '', '1', '', NULL, 'Na drugi brzeg', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(142, '', '34', '', NULL, 'Na ostatniej wieczerzy', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(143, '', '12', '', NULL, 'Na wieki żyje Król', 'Wielkanoc (W)', 'Niewdrożone', '', '', ''),
(144, '', '9', '', NULL, 'Nad Betlejem', 'Boże Narodzenie (AB)', 'Zapomniane', '', '', ''),
(145, '', '4', '', NULL, 'Nad Jordanem', 'Adwent (AB)', 'Zapomniane', '', '', ''),
(146, '', '35', '', NULL, 'Najcichsza Obecności', 'Okres zwykły (R)', 'Zapomniane', '', '', ''),
(147, '', '39', '', NULL, 'Nasz Bóg jest wielki', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(148, '', '1', '', NULL, 'Nasz Pan', 'Adwent (AB)', 'Wdrożone', '', '', ''),
(149, '', '55', '', NULL, 'Nic nie musisz mówić', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(150, '', '21', '', NULL, 'Nie bój się', 'Okres zwykły (R)', 'Zapomniane', '', '', ''),
(151, '', '27', '', NULL, 'Nie bój się, wypłyń', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(152, '', '8, 6', '', NULL, 'Nie bój się, wypłyń', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(153, '', '41', '', NULL, 'Nie bójcie się żyć dla miłości', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(154, '', '8, 7', '', NULL, 'Nie bójcie się żyć dla miłości', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(155, '', '18', '', NULL, 'Nie bójcie się żyć dla miłości', 'Wielkanoc (W)', 'Niewdrożone', '', '', ''),
(156, '', '28', '', NULL, 'Nie lękajcie się', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(157, '', '38', '', NULL, 'Nie ma innego jak Jezus', 'Okres zwykły (R)', 'Zapomniane', '', '', ''),
(158, '', '17', '', NULL, 'Nie mądrość świata tego (Marana tha!)', 'Wielkanoc (W)', 'Zapomniane', '', '', ''),
(159, '', '', '', NULL, 'Nie nam, Panie', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(160, '', '53', '', NULL, 'Nie zastąpi ciebie nikt', 'Okres zwykły (R)', 'Zapomniane', '', '', ''),
(161, '', '7', '', NULL, 'Nie zdejmę krzyża', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(162, '', '21, 19', '', NULL, 'Nie zdejmę krzyża', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(163, '', '2', '', NULL, 'Niebo jest w sercu mym', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(164, '', '55', '', NULL, 'Niech będzie chwała i cześć', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(165, '', '45', '', NULL, 'Niech miłość Twa', 'Okres zwykły (R)', 'Zapomniane', '', '', ''),
(166, '', '30', '', NULL, 'Niech miłość Twa', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(167, '', '42', '', NULL, 'Niech miłość Twoja, Panie', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(168, '', '8, 10', '', NULL, 'Niech miłość Twoja, Panie', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(169, '', '41', '', NULL, 'Niech nas ogarnie łaska, Panie, Twa', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(170, '', '21, 20', '', NULL, 'Niech uwielbiony będzie Bóg', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(171, '', '3', '', NULL, 'Niepokalana', 'Maryjne (M)', 'Niewdrożone', '', '', ''),
(172, '', '22, 23', '', NULL, 'Niepokalana, Ty zawsze mnie rozumiesz', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(173, '', '35', '', NULL, 'Nocą ogród oliwny', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(174, '', '8, 4', '', NULL, 'O Jezu, cichy i pokorny', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(175, '', '20, 8', '', NULL, 'O Jezu, cichy i pokorny', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(176, '', '6', '', NULL, 'O Maryjo, Niepokalana Dziewico', 'Maryjne (M)', 'Zapomniane', '', '', ''),
(177, '', '34', '', NULL, 'O Stworzycielu Duchu przyjdź', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(178, '', '29', '', NULL, 'Odnów mnie', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(179, '', '1', '', NULL, 'Odrzućcie uczynki nocy', 'Adwent (AB)', 'Zapomniane', '', '', ''),
(180, '', '4', '', NULL, 'Odszedł Pasterz od nas', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(181, '', '25', '', NULL, 'Ofiaruję Tobie', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(182, '', '8', '', NULL, 'Ogrody', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(183, '', '3', '', NULL, 'Oto idzie mój Bóg', 'Adwent (AB)', 'Wdrożone', '', '', ''),
(184, '', '18', '', NULL, 'Oto ja, poślij mnie', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(185, '', '27', '', NULL, 'Oto jest dzień', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(186, '', '21', '', NULL, 'Oto o północy', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(187, '', '2', '', NULL, 'Oto Pan Bóg przyjdzie', 'Adwent (AB)', 'Niewdrożone', '', '', ''),
(188, '', '13', '', NULL, 'Oto są baranki młode', 'Wielkanoc (W)', 'Wdrożone', '', '', ''),
(189, '', '2', '', NULL, 'Pan blisko jest', 'Adwent (AB)', 'Wdrożone', '', '', ''),
(190, '', '8, 1', '', NULL, 'Pan jest mocą', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(191, '', '22, 24', '', NULL, 'Pan jest mocą', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(192, '', '35', '', NULL, 'Pan mym Pasterzem', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(193, '', '16', '', NULL, 'Pan wywyższony', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(194, '', '56', '', NULL, 'Panie mój, przychodzę dziś', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(195, '', '31', '', NULL, 'Panie mój, wiesz, że Cię kocham', '', '', '\"Pieśń miłości\" m.: Jacek Sykulski', '', ''),
(196, '', '22, 25', '', NULL, 'Panie pragnę kochać Cię', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(197, '', '31', '', NULL, 'Panie, gdy tonę', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(198, '', '33', '', NULL, 'Pańska jest ziemia (Hymn III Tysiąclecia)', 'Wielkanoc (W)', 'Zapomniane', '', '', 'Psalm XXIV\r\nm.: Jacek Sykulski\r\ns.: Franciszek Karpiński'),
(199, '', '17', '', NULL, 'Pasterzem moim jest Pan (Psalm 23)', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(200, '', '29', '', NULL, 'Pastorałka od serca do ucha', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(201, '', '7', '', NULL, 'Pewnej nocy', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(202, '', '37', '', NULL, 'Pewnej nocy', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(203, '', '25', '', NULL, 'Pierwsza gwazda', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(204, '', '21', '', NULL, 'Pochylasz się nade mną', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(205, '', '22, 26', '', NULL, 'Pod cieniem skrzydeł swoich', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(206, '', '41', '', NULL, 'Podnieś mnie, Jezu', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(207, '', '30', '', NULL, 'Podnieś mnie, Jezu', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(208, '', '44', '', NULL, 'Pokładam w Panu ufność mą', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(209, '', '19', '', NULL, 'Powołanie', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(210, '', '15', '', NULL, 'Powstań i żyj', 'Wielkanoc (W)', 'Wdrożone', '', '', ''),
(211, '', '23, 29', '', NULL, 'Pozwól mi przyjść do Ciebie', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(212, '', '6', '', NULL, 'Pójdźmy wszyscy do stajenki', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(213, '', '30', '', NULL, 'Prowadź mnie, światło', 'Wielki Post (W)', 'Wdrożone', 'Lednica 2000', '', ''),
(214, '', '36', '', NULL, 'Przed Twój tron', 'Okres zwykły (R)', 'Zapomniane', '', '', ''),
(215, '', '25', '', NULL, 'Przed wielu laty - o św. Stanisławie Kostce', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(216, '', '58', '', NULL, 'Przemień serce me', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(217, '', '2', '', NULL, 'Przepraszam Cię', 'Wielki Post (W)', 'Zapomniane', '', '', ''),
(218, '', '40', '', NULL, 'Przybądź, Święty Niepojęty Duchu', 'Okres zwykły (R)', 'Zapomniane', '', '', ''),
(219, '', '7', '', NULL, 'Przybieżeli do Betlejem', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(220, '', '11', '', NULL, 'Przychodzisz, Panie', 'Wielkanoc (W)', 'Zapomniane', '', '', ''),
(221, '', '33', '', NULL, 'Przyjdź, Duchu Kościoła', 'Okres zwykły (R)', 'Zapomniane', '', '', ''),
(222, '', '3', '', NULL, 'Przyjdź, Jezu, przyjdź', 'Adwent (AB)', 'Wdrożone', '', '', ''),
(223, '', '21, 22', '', NULL, 'Przytul mnie, Jezu', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(224, '', '2', '', NULL, 'Radością serca jest Pan', 'Adwent (AB)', 'Niewdrożone', '', '', ''),
(225, '', '28', '', NULL, 'Radość dziś nastała', 'Boże Narodzenie (AB)', 'Niewdrożone', '', '', ''),
(226, '', '16', '', NULL, 'Raduje się dusza ma', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(227, '', '10', '', NULL, 'Rozpięty na ramionach', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(228, '', '23, 30', '', NULL, 'Rozpięty na ramionach', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(229, '', '10', '', NULL, 'Rysuję krzyż', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(230, '', '23, 31', '', NULL, 'Rysuję krzyż', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(231, '', '22, 27', '', NULL, 'Rzekłeś do Ojca', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(232, '', '22', '', NULL, 'Sandały', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(233, '', '32', '', NULL, 'Santo Subito', 'Okres zwykły (R)', 'Zapomniane', '', '', ''),
(234, '', '56', '', NULL, 'Schowaj mnie', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(235, '', '19', '', NULL, 'Serce Jezusa', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(236, '', '5', '', NULL, 'Składamy Ci, Ojcze', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(237, '', '45', '', NULL, 'Składamy dziś', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(238, '', '24', '', NULL, 'Skosztujcie i zobacznie (psalm 34)', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(239, '', '3', '', NULL, 'Sługa - Król', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(240, '', '', '', NULL, 'Stała Matka bolejąca', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(241, '', '26', '', NULL, 'Stary, szorstki krzyż', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(242, '', '17', '', NULL, 'Stoję dziś', 'Wielkanoc (W)', 'Wdrożone', '', '', ''),
(243, '', '20', '', NULL, 'Szedłem kiedyś', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(244, '', '25', '', NULL, 'Szymonie, cyrenejczyku', 'Wielki Post (W)', 'Zapomniane', '', '', ''),
(245, '', '6', '', NULL, 'Światłem i zbawieniem mym', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(246, '', '10', '', NULL, 'Świeć, gwiazdeczko', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(247, '', '16', '', NULL, 'Świeć, Jezu, świeć', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(248, '', '5', '', NULL, 'Święty, Święty, Święty', 'Części stałe mszy świętej (R)', 'Zapomniane', '', '', ''),
(249, '', '23, 32', '', NULL, 'Ta krew', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(250, '', '13', '', NULL, 'Tak mnie skrusz', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(251, '', '23, 33', '', NULL, 'Tak mnie skrusz', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(252, '', '52', '', NULL, 'Taki duży, taki mały', 'Okres zwykły (R)', 'Zapomniane', '', '', ''),
(253, '', '31', '', NULL, 'Tłumy serc', 'Okres zwykły (R)', 'Zapomniane', '', '', ''),
(254, '', '24, 39', '', NULL, 'To ludzie', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(255, '', '9', '', NULL, 'To mój Pan', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(256, '', '24, 40', '', NULL, 'To nie gwoździe (\"Golgota\" - refren)', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(257, '', '11', '', NULL, 'Tobie chór aniołów', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(258, '', '', '', NULL, 'Trójco Święta', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(259, '', '29', '', NULL, 'Tryumfy Króla niebieskiego', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(260, '', '8', '', NULL, 'Ty światłość dnia', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(261, '', '24, 37', '', NULL, 'Ty światłość dnia', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(262, '', '38', '', NULL, 'Ty tylko mnie poprowadź', 'Wielkanoc (W)', 'Niewdrożone', '', '', ''),
(263, '', '14', '', NULL, 'Tyle dobrego', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(264, '', '24, 34', '', NULL, 'Tyle dobrego', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(265, '', '24, 35', '', NULL, 'U Pana dziś zostawiam troski swe', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(266, '', '42', '', NULL, 'Ubi caritas', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(267, '', '48', '', NULL, 'Ubi caritas', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(268, '', '8, 8', '', NULL, 'Ubi caritas', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(269, '', '22, 28', '', NULL, 'Ubi caritas', 'Wielki Post (W)', 'Wdrożone', '', '', ''),
(270, '', '57', '', NULL, 'Ukaż mi, Panie, swą twarz', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(271, '', '32', '', NULL, 'Ukaż mi, Panie, swą twarz', 'Wielkanoc (W)', 'Wdrożone', '', '', ''),
(272, '', '8', '', NULL, 'Uwielbiajcie Pana', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(273, '', '11', '', NULL, 'Uwielbiam Cię', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(274, '', '23', '', NULL, 'Uwielbiam Cię, Trójco', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(275, '', '4', '', NULL, 'Uwielbiam Twoje Imię', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(276, '', '29', '', NULL, 'Uwielbiamy Cię', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(277, '', '39', '', NULL, 'Uwielbiamy Cię', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(278, '', '41', '', NULL, 'Uwielbiamy Cię', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(279, '', '8, 5', '', NULL, 'W lekkim powiewie', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(280, '', '41', '', NULL, 'W swoim wielkim miłosierdziu', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(281, '', '42', '', NULL, 'W Tobie jest światło', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(282, '', '15', '', NULL, 'W żłobie leży', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(283, '', '36', '', NULL, 'Wiele jest serc', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(284, '', '37', '', NULL, 'Wierzę w Ciebie, Panie', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(285, '', '4', '', NULL, 'Wino i chleb', 'Okres zwykły (R)', 'Zapomniane', '', '', ''),
(286, '', '18', '', NULL, 'Witaj Pokarmie', 'Okres zwykły (R)', 'Zapomniane', 'm.: Paweł Bębenek', '', ''),
(287, '', '4', '', NULL, 'Witaj, Gwiazdo morza', 'Maryjne (M)', 'Niewdrożone', '', '', ''),
(288, '', '10', '', NULL, 'Wspaniała Matka', 'Maryjne (M)', 'Wdrożone', '', '', ''),
(289, '', '7', '', NULL, 'Wśród nocnej ciszy', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(290, '', '13', '', NULL, 'Wśród nocnej ciszy', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(291, '', '13', '', NULL, 'Wśród nocnej ciszy', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(292, '', '24,38', '', NULL, 'Wywyższony', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(293, '', '17', '', NULL, 'Wzywam Cię, Duchu, przyjdź', 'Okres zwykły (R)', 'Zapomniane', '', '', ''),
(294, '', '42', '', NULL, 'Z Boga jestem', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(295, '', '20', '', NULL, 'Z narodzenia Pana', 'Boże Narodzenie (AB)', 'Wdrożone', '', '', ''),
(296, '', '24, 36', '', NULL, 'Za Twą miłość, Panie mój', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(297, '', '28', '', NULL, 'Zapada zmrok', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(298, '', '9', '', NULL, 'Zaufaj Maryi', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(299, '', '43', '', NULL, 'Zaufaj Panu', 'Okres zwykły (R)', 'Zapomniane', '', '', ''),
(300, '', '18', '', NULL, 'Zbawiciel, On porusza porusza góry', 'Wielkanoc (W)', 'Niewdrożone', '', '', ''),
(301, '', '2', '', NULL, 'Zbawienie przyszło przez krzyż', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(302, '', '7', '', NULL, 'Zbawienie przyszło przez krzyż', 'Wielki Post (W)', 'Niewdrożone', '', '', ''),
(303, '', '2', '', NULL, 'Zdrowaś bądź, Maryja', 'Adwent (AB)', 'Niewdrożone', '', '', ''),
(304, '', '12', '', NULL, 'Zmartwychwstał Chrystus Król (Regnavit)', 'Wielkanoc (W)', 'Niewdrożone', '', '', ''),
(305, '', '11', '', NULL, 'Zmartwychwstał Pan', 'Wielkanoc (W)', 'Wdrożone', '', '', ''),
(306, '', '32', '', NULL, 'Znowu Cię spotykam', 'Okres zwykły (R)', 'Niewdrożone', '', '', ''),
(307, '', '9', '', NULL, 'Życzenia', 'Okres zwykły (R)', 'Wdrożone', '', '', ''),
(308, '', '', '', NULL, 'Aniele ziemski bez winy', 'Okres zwykły (R)', 'Niewdrożone', 't.: T. Klonowski, \"Szczeble do nieba\", Poznań (1856 - 1867)\r\nm.: ks. J. Mazurowski, \"Melodie do Zbioru ks. Kellera\", Pelplin 1871.', '', '');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `event_parts`
--
ALTER TABLE `event_parts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indeksy dla tabeli `event_songs`
--
ALTER TABLE `event_songs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `part_id` (`part_id`);

--
-- Indeksy dla tabeli `piesni`
--
ALTER TABLE `piesni`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `event_parts`
--
ALTER TABLE `event_parts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `event_songs`
--
ALTER TABLE `event_songs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `piesni`
--
ALTER TABLE `piesni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=309;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event_parts`
--
ALTER TABLE `event_parts`
  ADD CONSTRAINT `event_parts_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `event_songs`
--
ALTER TABLE `event_songs`
  ADD CONSTRAINT `event_songs_ibfk_1` FOREIGN KEY (`part_id`) REFERENCES `event_parts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;