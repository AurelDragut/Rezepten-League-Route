-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 19, 2020 at 10:40 AM
-- Server version: 10.5.6-MariaDB-log
-- PHP Version: 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rezepten`
--

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `nr` int(10) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`nr`, `name`) VALUES
(5, 'Bio-Zitrone'),
(6, 'Butter'),
(7, 'Eier (Gr. M)'),
(8, 'Salz'),
(16, 'Zwiebel'),
(17, 'Knoblauchzehe'),
(18, 'Thymian'),
(19, 'Risotto-Reis'),
(20, 'cremiger Gorgonzola Käse'),
(21, 'Hokkaido-Kürbis'),
(22, 'Gemüsebrühe'),
(23, 'Weißwein'),
(24, 'Pfeffer'),
(25, 'Olivenöl'),
(26, 'Haselnussblättchen'),
(27, 'Alufolie'),
(28, 'Öl'),
(29, 'Kakaokekse mit Cremefüllung (176 g; z. B. „Oreo“)'),
(30, 'Himbeeren,Heidel­beeren,Brombeeren und Rote Johannisbeeren'),
(32, 'Doppelrahmfrischkäse'),
(33, 'Zucker'),
(34, 'Schlagsahne'),
(35, 'Sahnefestiger'),
(36, 'Beerenkonfitüre (kernlos; z. B. von Schwartau)'),
(37, 'Gefrierbeutel'),
(38, 'Backpapier'),
(45, 'Sonnenblumenöl'),
(46, 'Petersilie'),
(50, 'kleine Spaghettikürbisse (à ca. 1000 g)'),
(51, 'Putenbrust'),
(52, 'Stange Porree'),
(53, 'körniger Frischkäse'),
(54, 'Parmesankäse'),
(55, 'Oregano'),
(57, 'Langkornreis'),
(58, 'Zucchini (z. B. grüne und gelbe)'),
(59, 'Mais'),
(60, 'Zwiebeln'),
(61, 'Toastbrot'),
(62, 'Bergkäse (Stück)'),
(63, 'Crème fraîche'),
(64, 'Muskat'),
(65, 'Fett für die Form'),
(66, 'weiche Butter'),
(73, 'vegane Margarine'),
(74, 'Mehl'),
(75, 'Tofu'),
(76, 'Sojamilch'),
(77, 'Estragon'),
(78, 'Schnittlauch'),
(79, 'Champignons'),
(80, 'Schalotten'),
(81, 'Kirschtomaten'),
(82, 'Fett'),
(83, 'Eier'),
(84, 'gewürfelter Katenschinken'),
(85, 'geraspelter Goudakäse'),
(94, 'Gemüsebrühe'),
(97, 'Öl (z.B. Rapsöl oder Sonnenblumenöl)'),
(98, 'Tomatenmark'),
(99, '40 g Mehl'),
(100, 'Knoblauchzehen'),
(101, 'Sojasoße');

-- --------------------------------------------------------

--
-- Table structure for table `ingredients_recipes`
--

CREATE TABLE `ingredients_recipes` (
  `nr` int(10) NOT NULL,
  `recipe_nr` int(10) NOT NULL,
  `ingredient_nr` int(10) NOT NULL,
  `amount` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ingredients_recipes`
--

INSERT INTO `ingredients_recipes` (`nr`, `recipe_nr`, `ingredient_nr`, `amount`) VALUES
(132, 94, 60, ''),
(133, 94, 67, '4 EL'),
(134, 94, 68, '2 EL'),
(135, 94, 69, ''),
(136, 94, 70, '2'),
(137, 94, 22, '800 ml'),
(138, 94, 71, '2 EL'),
(139, 94, 24, ''),
(140, 133, 72, ''),
(141, 138, 22, '800 ml'),
(142, 138, 24, ''),
(143, 138, 60, ''),
(144, 138, 67, '4 EL'),
(145, 138, 68, '2 EL'),
(146, 138, 69, ''),
(147, 138, 70, '2'),
(148, 138, 71, '2 EL'),
(149, 139, 22, '800 ml'),
(150, 139, 24, ''),
(151, 139, 60, ''),
(152, 139, 67, '4 EL'),
(153, 139, 68, '2 EL'),
(154, 139, 69, ''),
(155, 139, 70, '2'),
(156, 139, 71, '2 EL'),
(173, 141, 73, '125 g'),
(174, 141, 74, ''),
(175, 141, 75, '300 g'),
(176, 141, 76, '150 ml'),
(177, 141, 77, '6 Stiel(e)'),
(178, 141, 78, '0,5 Bund'),
(179, 141, 8, ''),
(180, 141, 24, ''),
(181, 141, 79, '100 g'),
(182, 141, 80, '5'),
(183, 141, 45, '2 EL'),
(184, 141, 81, '200 g'),
(185, 141, 82, ''),
(187, 93, 7, '3'),
(190, 93, 28, '2 EL'),
(191, 93, 57, '150 g'),
(192, 93, 58, '1,2 kg'),
(193, 93, 59, '1 Dose (425 ml)'),
(194, 93, 60, '3'),
(195, 93, 61, '6 Scheiben'),
(196, 93, 62, '100 g'),
(197, 93, 63, '200 g'),
(200, 93, 66, '50 g'),
(236, 92, 8, ''),
(238, 92, 28, '4 EL'),
(239, 92, 34, '200 g'),
(241, 92, 50, '2'),
(242, 92, 51, '600 g'),
(243, 92, 52, '1'),
(244, 92, 53, '200 g'),
(245, 92, 54, '25 g'),
(246, 92, 55, '4 Stiele'),
(247, 82, 6, '2 EL'),
(248, 82, 8, ''),
(249, 82, 22, '150 ml'),
(250, 82, 24, ''),
(251, 82, 25, '5 EL'),
(252, 82, 39, '400 g'),
(253, 82, 40, '1'),
(254, 82, 41, '2'),
(255, 82, 42, '500 g'),
(256, 82, 43, '700 g'),
(257, 82, 44, '400 g'),
(258, 82, 45, '2 EL'),
(259, 82, 46, '1 Bund'),
(260, 82, 47, '200 g'),
(261, 82, 48, '300 g'),
(262, 82, 49, '2 EL'),
(271, 140, 94, '800 ml'),
(274, 140, 97, '4 EL'),
(275, 140, 98, '2 EL'),
(277, 140, 100, '2'),
(278, 140, 101, '2 EL'),
(296, 35, 6, '50 g'),
(297, 35, 29, '1 Packung'),
(299, 35, 5, '1'),
(300, 35, 32, '400 g'),
(301, 35, 33, '75 g'),
(302, 35, 34, '200 g'),
(303, 35, 35, '2 Päckchen'),
(304, 35, 36, '100 g'),
(347, 142, 46, '0,5 Bund'),
(348, 142, 60, '1 kg'),
(349, 142, 66, '125 g'),
(350, 142, 74, '150 g'),
(352, 142, 83, '6'),
(353, 142, 84, '250 g'),
(354, 142, 85, '500 g'),
(367, 142, 82, ''),
(371, 143, 0, '100 ml'),
(372, 143, 8, ''),
(373, 143, 60, '3'),
(377, 143, 24, ''),
(378, 143, 33, ''),
(379, 143, 63, '200 g'),
(380, 143, 18, '10 Stiele'),
(381, 143, 25, '4 EL'),
(382, 144, 110, '1'),
(383, 144, 8, ''),
(384, 144, 60, '3'),
(385, 144, 111, '1'),
(386, 144, 112, '1 Packung (125 g)'),
(387, 144, 113, '100 ml'),
(388, 144, 24, ''),
(389, 144, 33, ''),
(390, 144, 63, '200 g'),
(391, 144, 18, '10 Stiele'),
(392, 144, 25, '4 EL'),
(415, 145, 8, ''),
(416, 145, 18, '10 Stiele'),
(417, 145, 24, ''),
(418, 145, 25, '4 EL'),
(419, 145, 33, ''),
(420, 145, 60, '3'),
(421, 145, 63, '200 g'),
(422, 145, 110, '1'),
(423, 145, 111, '1'),
(424, 145, 112, '1 Packung (125 g)'),
(425, 145, 113, '100 ml'),
(480, 32, 8, ''),
(481, 32, 16, '1'),
(482, 32, 17, '1'),
(483, 32, 18, '6 Stiele'),
(484, 32, 19, '200 g'),
(485, 32, 20, '150 g'),
(486, 32, 21, '500 g'),
(487, 32, 22, '500 ml'),
(488, 32, 23, '125 ml'),
(490, 32, 25, '2 EL'),
(491, 32, 26, '50 g'),
(727, 140, 22, '800 ml'),
(798, 35, 28, ''),
(800, 35, 30, '125 g'),
(806, 35, 37, ''),
(807, 35, 38, ''),
(809, 92, 24, ''),
(812, 92, 38, ''),
(820, 93, 8, ''),
(821, 93, 24, ''),
(830, 93, 64, ''),
(831, 93, 65, ''),
(833, 146, 128, ''),
(834, 147, 130, ''),
(835, 148, 130, ''),
(836, 149, 130, ''),
(863, 150, 131, ''),
(878, 140, 24, ''),
(879, 140, 60, ''),
(883, 140, 99, ''),
(908, 32, 24, ''),
(911, 32, 27, '');

-- --------------------------------------------------------

--
-- Table structure for table `links`
--

CREATE TABLE `links` (
  `nr` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `parent` int(3) NOT NULL,
  `order_nr` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `links`
--

INSERT INTO `links` (`nr`, `name`, `link`, `parent`, `order_nr`) VALUES
(1, 'Startseite', '/', 0, 1),
(2, 'Rezepten', '#', 0, 2),
(3, 'Artikel hinzufügen', '/admin/recipes/create', 2, 1),
(4, 'Einträge auflisten', '/admin/recipes/index', 2, 2),
(5, 'Menü-Links', '#', 0, 3),
(6, 'Artikel hinzufügen', '/admin/links/create', 5, 1),
(7, 'Einträge auflisten', '/admin/links/index', 5, 2),
(8, 'Benutzer', '#', 0, 4),
(9, 'Artikel hinzufügen', '/admin/users/create', 8, 1),
(10, 'Einträge auflisten', '/admin/users/index', 8, 2),
(19, 'Zutaten', '#', 0, 2),
(20, 'Artikel hinzufügen', '/admin/ingredients/create', 19, 1),
(21, 'Einträge auflisten', '/admin/ingredients/index', 19, 2);

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `nr` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `schnecke` varchar(255) NOT NULL,
  `bild` varchar(255) DEFAULT NULL,
  `portionsnummern` tinyint(3) UNSIGNED NOT NULL,
  `vorbereitungszeit` varchar(255) NOT NULL,
  `vorbereitung_schwierigkeit` varchar(255) NOT NULL,
  `vorbereitung_anweisungen` text NOT NULL,
  `kcal` int(10) UNSIGNED NOT NULL,
  `kj` int(10) UNSIGNED NOT NULL,
  `prot` int(10) UNSIGNED NOT NULL,
  `fett` int(10) UNSIGNED NOT NULL,
  `kh` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`nr`, `name`, `schnecke`, `bild`, `portionsnummern`, `vorbereitungszeit`, `vorbereitung_schwierigkeit`, `vorbereitung_anweisungen`, `kcal`, `kj`, `prot`, `fett`, `kh`) VALUES
(32, 'Risotto-Kürbis-Auflauf', 'risotto-krbis-auflauf', '/img/uploads/risotto-kurbis-auflauf.jpg', 4, '75 Minuten', 'leicht', '1.\r\nZwiebel und Knoblauch schälen, klein würfeln. Thymian waschen, trocken schütteln. Blättchen abstreifen und, bis auf etwas zum Garnieren, fein hacken. Zusammen mit Reis in einer Auflaufform (ca. 1,75 Liter Inhalt) mischen. Käse klein würfeln, ca. 50 g beiseite stellen, Rest in die Form geben. Kürbis putzen, Kerne herausschaben und waschen. In dünne Scheiben hobeln.\r\n2.\r\nBrühe und Wein erhitzen, in die Form gießen. Kürbis auf dem Reis kreisförmig überlappend verteilen. Mit Salz und Pfeffer würzen und mit Öl beträufeln. Zugedeckt im vorgeheizten Backofen (E-Herd: 200 °C/ Umluft: 175 °C/ Gas: s. Hersteller) ca. 40 Minuten garen. Folie entfernen. Restlichen Käse in der Form am Rand verteilen. Haselnussblättchen darauf streuen. Offen bei gleicher Temperatur ca. 10 Minuten backen. Mit beiseite gelegtem Thymian garniert servieren.', 570, 123, 16, 27, 57),
(35, '„Summer Jam“- Cheesecake', 'summer-jam--cheesecake', '/img/uploads/summer-jam--cheesecake.jpg', 10, '35 Minuten ( + 120 Minuten Wartezeit )', 'ganz einfach', '1.\r\nEine Tortenplatte mit etwas Öl einstreichen. Den Formrand einer Springform (20 cm Ø) ­daraufsetzen. Butter in einem Topf schmelzen. Die Kekse in einen Gefrierbeutel ­geben, ­verschließen und mit einer Teigrolle grob ­zer­bröseln. Keksbrösel und Butter mischen, in den Formrand füllen und gleichmäßig als ­Boden andrücken. Kalt stellen.\r\n2.\r\nBeeren verlesen, evtl. waschen. Johannis­beeren von den Rispen streifen. Zitrone heiß waschen, abtrocknen und die Schale fein abreiben. Zitrone halbieren, eine Hälfte aus­pressen. Frischkäse, Zucker, Zitronenschale und -saft verrühren. Sahne steif schlagen, dabei Sahnefestiger einrieseln lassen. Sahne unter die Frischkäsemasse heben.\r\n3.\r\nEtwa die Hälfte der Beeren auf dem Keks­boden ­verteilen. Creme vorsichtig darauf­geben, sodass die Beeren nicht verrutschen. Restliche Beeren auf die Creme streuen und diese leicht in die Creme drücken. Kuchen mindestens 2 Stunden kalt stellen.\r\n4.\r\nKonfitüre bei kleiner Hitze leicht erwärmen. Kuchen aus dem Formrand lösen und die Konfitüre darüberträufeln.', 330, 123, 6, 24, 20),
(92, 'Gefüllter Spaghettikürbis mit Putengeschnetzeltem', 'gefllter-spaghettikrbis-mit-putengeschnetzeltem', '/img/uploads/gefuellter-spaghettikuerbis-mit-putengeschnetzeltem.jpg', 4, '60 Minuten', 'ganz einfach', '1.\r\nKürbisse waschen, trocken reiben, halbieren und Kerne mit einem Löffel herauskratzen. Auf ein mit Backpapier ausgelegtes Backblech legen, mit ca. 2 EL Öl bepinseln und mit Salz würzen. Im vorgeheizten Backofen (E-Herd: 175 °C/Umluft: 150 °C/Gas: s. Hersteller) 45–50 Minuten garen.\r\n2.\r\nFleisch trocken tupfen und in Streifen schneiden. Porree putzen, waschen, trocken reiben und in Ringe schneiden. 2 EL Öl in einer Pfanne erhitzen, Fleisch darin 2–3 Minuten unter Wenden goldbraun anbraten. Porree zufügen, weitere 1–2 Minuten braten und mit Salz und Pfeffer würzen. Frischkäse und Sahne zufügen einrühren. Parmesan fein reiben, unterrühren. Geschnetzeltes mit Salz und Pfeffer abschmecken.\r\n3.\r\nOregano waschen, trocken schütteln und Blättchen von den Stielen zupfen. Kürbis-Hälften aus dem Ofen nehmen und Spaghetti mit Hilfe einer Gabel herauskratzen. Spaghetti auf eine Seite der Kürbishälften schieben, andere Seiten mit Putengeschnetzeltem befüllen. Mit Oregano garnieren und servieren.', 660, 2770, 49, 34, 39),
(93, 'Goldener Zucchini-Reis-Auflauf', 'goldener-zucchini-reis-auflauf', '/img/uploads/goldener-zucchini-reis-auflauf.jpg', 6, '60 Minuten', 'leicht', '1.\r\nReis in kochendem Salzwasser nach Packungsanweisung garen. Zucchini waschen und, bis auf ein ca. 4 cm langes Stück, in ca. 1 cm dicke Scheiben schneiden. Zucchinistück längs halbieren und mit einem Herzausstecher zwei Herzen ausstechen. Zucchinischeiben und Herzen in kochendem Salzwasser ca. 2 Minuten blanchieren, abschrecken und abtropfen lassen. Herzen beiseitelegen.\r\n2.\r\nMais abtropfen lassen. Zwiebeln schälen, fein würfeln. Öl in einem Topf erhitzen. Zwiebeln darin glasig dünsten. Toast im Universalzerkleinerer fein hacken. Käse grob raspeln.\r\n3.\r\nReis abgießen. Crème fraîche und Eier verquirlen. Mit Salz, Pfeffer und Muskat kräftig würzen. Eiermix, Reis, Zucchini, Mais, Zwiebeln, ⅔ der Toastbrösel und Käse mischen. In eine gefettete Auflaufform (ca. 18 x 25 cm) füllen. Zucchiniherzen darauflegen. Mit übrigen Toastbröseln bestreuen. Butter in Stückchen auf dem Auflauf verteilen. Im vorgeheizten Backofen (E-Herd: 180 °C/Umluft: 160 °C/Gas: s. Hersteller) 25–30 Minuten goldbraun backen.', 540, 1000, 18, 29, 48),
(140, 'Vegane Bratensoße', 'vegane-bratensoe', '/img/uploads/vegane-bratensoe.jpg', 6, '40 Minuten', 'ganz einfach', '1.\r\nZwiebeln schälen und fein würfeln. Knoblauchzehen pressen.\r\n2.\r\nÖl in einer beschichteten Pfanne erhitzen und Zwiebelwürfel darin 5 Minuten anbraten. Tomatenmark und Mehl hinzugeben und bei mittlerer Hitze und gelegentlichem Umrühren 10 Minuten braten. Knoblauch hinzufügen und kurz weiterbraten.\r\n3.\r\nMit 800 ml Gemüsebrühe ablöschen, alles bei geringer Hitze 10 Minuten köcheln lassen. Mit Sojasoße und etwas Pfeffer würzen.\r\n4.\r\nAlles in einen Topf oder eine große Schüssel geben und mit dem Stabmixer sehr fein pürieren.', 137, 150, 3, 9, 11),
(141, 'Vegane Quiche mit Champignons und Tomaten', 'vegane-quiche-mit-champignons-und-tomaten', '/img/uploads/vegane-quiche-mit-champignons-und-tomaten.jpg', 4, '75 Minuten', 'ganz einfach', '1.\r\nFür den veganen Mürbeteig Margarine, Mehl und 4 EL Wasser zu einem glatten Teig verkneten. Den Teig auf bemehlter Arbeitsfläche rund (ca. 25 cm Ø) ausrollen. Eine Tarteform fetten und mit Mehl ausstäuben. Den Teig für die in die Form geben und 30 Minuten kalt stellen.\r\n2.\r\nFür den Belag: Tofu in Würfel schneiden, in kochendem Wasser ca. 5 Minuten kochen. Tofu in ein Sieb gießen, abtropfen lassen und abkühlen lassen.\r\n3.\r\nTofu mit 150 ml Sojamilch fein pürieren. Kräuter waschen und trocken schütteln. Estragonblättchen abzupfen und fein hacken. Schnittlauch in Röllchen schneiden. Tofumasse mit Salz und Pfeffer würzen und Estragon und Schnittlauch unterrühren.\r\n4.\r\nChampignons putzen. Schalotten schälen und in Spalten schneiden. 2 EL Öl in einer Pfanne erhitzen, Pilze und Schalotten nacheinander je 3 Minuten anbraten. Mit Salz und Pfeffer würzen. Kirschtomaten waschen und halbieren. Tofumasse auf den Teig geben und glatt streichen. Das Gemüse darauf verteilen. Die vegane Quiche im vorgeheizten Backofen (E-Herd: 175 °C/Umluft: 150 °C) ca. 40 Minuten garen.', 610, 700, 19, 37, 50),
(142, 'Zwiebelkuchen ohne Boden', 'zwiebelkuchen-ohne-boden', '/img/uploads/zwiebelkuchen-ohne-boden.jpg', 12, '70 Minuten ( + 30 Minuten Wartezeit )', 'ganz einfach', '1.\r\nZwiebeln schälen und würfeln. Butter mit den Schneebesen des Handrührgerätes cremig rühren. Die Hälfte der Eier nacheinander unterrühren. Erst Mehl, dann die restlichen Eier unterrühren. Schinken, Käse und Zwiebeln mit dem Teig vermengen.\r\n2.\r\nTeig in eine gefettete Springform (28-30 cm Ø) geben und glatt streichen. Im vorgeheizten Backofen (E-Herd: 200 °C/Umluft: 175 °C) ca. 40 Minuten goldbraun backen. Aus dem Ofen nehmen, lauwarm abkühlen lassen.\r\n3.\r\nPetersilie waschen, trocken schütteln, Blätter abzupfen und, bis auf etwas zum Garnieren, fein hacken. Zwiebelkuchen ohne Boden in Stücke schneiden, anrichten und mit Petersilie bestreuen.', 370, 780, 21, 26, 13);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `nr` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`nr`, `name`, `username`, `email`, `password`, `created_at`, `updated_at`) VALUES
(7, 'Aurel Dragut', 'aureldragut', 'aurel.dragut@gmail.com', '$2y$10$/5NLqb3HZtrF96W59d5yg.8MMlDmVv6FzEeVAoKIzsB17ReZrvhXe', '2020-11-03 13:17:07', '2020-11-03 13:17:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`nr`);

--
-- Indexes for table `ingredients_recipes`
--
ALTER TABLE `ingredients_recipes`
  ADD PRIMARY KEY (`nr`),
  ADD UNIQUE KEY `recipeNr` (`recipe_nr`,`ingredient_nr`);

--
-- Indexes for table `links`
--
ALTER TABLE `links`
  ADD PRIMARY KEY (`nr`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`nr`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`nr`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `nr` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT for table `ingredients_recipes`
--
ALTER TABLE `ingredients_recipes`
  MODIFY `nr` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=912;

--
-- AUTO_INCREMENT for table `links`
--
ALTER TABLE `links`
  MODIFY `nr` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `nr` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `nr` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
