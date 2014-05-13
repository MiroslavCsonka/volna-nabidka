-- phpMyAdmin SQL Dump
-- version 3.5.0
-- http://www.phpmyadmin.net
--
-- Počítač: wm21.wedos.net:3306
-- Vygenerováno: Pát 26. dub 2013, 09:23
-- Verze MySQL: 5.5.23
-- Verze PHP: 5.4.11

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `d24950_db`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `Category`
--

CREATE TABLE IF NOT EXISTS `Category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_czech_ci NOT NULL COMMENT 'Název kategorie',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Tabulka kategorií' AUTO_INCREMENT=28 ;

--
-- Vypisuji data pro tabulku `Category`
--

INSERT INTO `Category` (`id`, `name`) VALUES
(3, 'Administrativa'),
(4, 'Auto – moto'),
(5, 'Cestovní ruch a ubytování'),
(6, 'Doprava a zásobování'),
(7, 'Finance a ekonomika'),
(8, 'Gastronomie a pohostinství'),
(9, 'Chemie a potravinářství'),
(10, 'Informatika'),
(11, 'Kultura, umění a tvůrčí práce'),
(12, 'Marketing, média a reklama'),
(13, 'Ostatní'),
(14, 'Ostraha a bezpečnost'),
(15, 'Personalistika a vzdělávání'),
(16, 'Prodej a obchod'),
(17, 'Řemeslné a manuální práce'),
(18, 'Služby'),
(19, 'Státní zaměstnanci'),
(20, 'Stavebnictví a reality'),
(21, 'Technika, elektrotechnika a energetika'),
(22, 'Telekomunikace'),
(23, 'Tisk, vydavatelství a polygrafie'),
(24, 'Výroba, průmysl a provoz'),
(25, 'Zákaznický servis'),
(26, 'Zdravotnictví, farmacie a sociální péče'),
(27, 'Zemědělství, lesnictví a ekologie');

-- --------------------------------------------------------

--
-- Struktura tabulky `City`
--

CREATE TABLE IF NOT EXISTS `City` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=107 ;

--
-- Vypisuji data pro tabulku `City`
--

INSERT INTO `City` (`id`, `name`) VALUES
(80, 'Benešov'),
(81, 'Beroun'),
(35, 'Blansko'),
(37, 'Brno'),
(58, 'Bruntál'),
(38, 'Břeclav'),
(53, 'Česká lípa'),
(27, 'České budějovice'),
(28, 'Český krumlov'),
(90, 'Děčín'),
(75, 'Domažlice'),
(60, 'Frýdek-místek'),
(97, 'Havlíčkův Brod'),
(39, 'Hodonín'),
(47, 'Hradec Králové'),
(43, 'Cheb'),
(91, 'Chomutov'),
(71, 'Chrudim'),
(54, 'Jablonec nad Nisou'),
(66, 'Jeseník'),
(48, 'Jičín'),
(98, 'Jihlava'),
(29, 'Jindřichův Hradec'),
(44, 'Karlovy Vary'),
(61, 'Karviná'),
(82, 'Kladno'),
(76, 'Klatovy'),
(83, 'Kolín'),
(102, 'Kroměříž'),
(84, 'Kutná Hora'),
(55, 'Liberec'),
(92, 'Litoměřice'),
(93, 'Louny'),
(85, 'Mělník'),
(86, 'Mladá Boleslav'),
(94, 'Most'),
(49, 'Náchod'),
(62, 'Nový Jičín'),
(87, 'Nymburk'),
(67, 'Olomouc'),
(63, 'Opava'),
(64, 'Ostrava'),
(72, 'Pardubice'),
(99, 'Pelhřimov'),
(30, 'Písek'),
(77, 'Plzeň'),
(25, 'Praha'),
(31, 'Prachatice'),
(68, 'Prostějov'),
(69, 'Přerov'),
(88, 'Příbram'),
(89, 'Rakovník'),
(78, 'Rokycany'),
(50, 'Rychnov nad Kněžnou'),
(56, 'Semily'),
(45, 'Sokolov'),
(32, 'Strakonice'),
(73, 'Svitavy'),
(70, 'Šumperk'),
(33, 'Tábor'),
(79, 'Tachov'),
(95, 'Teplice'),
(51, 'Trutnov'),
(100, 'Třebíč'),
(103, 'Uherské Hradiště'),
(96, 'Ústí nad Labem'),
(74, 'Ústí nad Orlicí'),
(104, 'Vsetín'),
(40, 'Vyškov'),
(105, 'Zlín'),
(41, 'Znojmo'),
(101, 'Žďár nad Sázavou');

-- --------------------------------------------------------

--
-- Struktura tabulky `Comment`
--

CREATE TABLE IF NOT EXISTS `Comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from` int(11) NOT NULL COMMENT 'User, který odeslal komentář k projektu',
  `project` int(11) NOT NULL COMMENT 'Project, kterému je zpráva určena',
  `value` longtext COLLATE utf8_czech_ci NOT NULL COMMENT 'Obsah komentáře',
  `date` datetime NOT NULL COMMENT 'Datum, kdy byl komentář přidán',
  PRIMARY KEY (`id`),
  KEY `from` (`from`),
  KEY `project` (`project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Tabulka komentářů k projektu' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `Education`
--

CREATE TABLE IF NOT EXISTS `Education` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `focus` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `end` year(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=18 ;

--
-- Vypisuji data pro tabulku `Education`
--

INSERT INTO `Education` (`id`, `user_id`, `name`, `focus`, `end`) VALUES
(17, 58, 'EDUCAnet', 'mezinárodní styky', 2020);

-- --------------------------------------------------------

--
-- Struktura tabulky `File`
--

CREATE TABLE IF NOT EXISTS `File` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `content` blob NOT NULL,
  `description` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `Group`
--

CREATE TABLE IF NOT EXISTS `Group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `thread_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_id` int(11) DEFAULT NULL,
  `lastRead_id` int(11) DEFAULT NULL,
  `subscribed` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `thread_id` (`thread_id`,`user_id`),
  KEY `last_id` (`last_id`),
  KEY `user_id` (`user_id`),
  KEY `lastRead_id` (`lastRead_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `Invitation`
--

CREATE TABLE IF NOT EXISTS `Invitation` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'pk',
  `inviter` int(11) NOT NULL COMMENT 'ID uživatele, který odesílá pozvánku',
  `reciever` int(11) NOT NULL COMMENT 'ID uživatele, který obdrží pozvánku',
  `project_id` int(11) NOT NULL COMMENT 'Project, na který je reciever pozván',
  PRIMARY KEY (`id`),
  KEY `inviter` (`inviter`),
  KEY `reciever` (`reciever`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Tabulka invitací' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `Language`
--

CREATE TABLE IF NOT EXISTS `Language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=3 ;

--
-- Vypisuji data pro tabulku `Language`
--

INSERT INTO `Language` (`id`, `name`) VALUES
(1, 'Angličtina'),
(2, 'Ruština');

-- --------------------------------------------------------

--
-- Struktura tabulky `Language_User`
--

CREATE TABLE IF NOT EXISTS `Language_User` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `level` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `language_id` (`language_id`,`user_id`),
  UNIQUE KEY `language_id_2` (`language_id`,`user_id`),
  KEY `asfssasd` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `Message`
--

CREATE TABLE IF NOT EXISTS `Message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `content` text COLLATE utf8_czech_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `Notification`
--

CREATE TABLE IF NOT EXISTS `Notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) NOT NULL COMMENT 'Cizí klíč do tabulky User',
  `message` text COLLATE utf8_czech_ci NOT NULL COMMENT 'Text notifikace',
  `date` datetime NOT NULL COMMENT 'Datum přidání notifikace',
  `status` enum('read','unread','deleted') COLLATE utf8_czech_ci NOT NULL DEFAULT 'unread' COMMENT 'status notifikace',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Tabulka notifikací' AUTO_INCREMENT=3 ;

--
-- Vypisuji data pro tabulku `Notification`
--

INSERT INTO `Notification` (`id`, `user_id`, `message`, `date`, `status`) VALUES
(1, 61, 'Byl jste vybrán jako pracovník projektu\r\n                           <a href=''/projekt/160.html''>Natření pokoje</a>. Gratulujeme!', '2013-04-25 02:07:29', 'read'),
(2, 61, 'Uživatel <a href=''/uzivatel/63''>JohnDoe</a>\r\n                          označil projekt <a href=''/projekt/160.html''>Natření pokoje</a>,\r\n                          na kterém jste pracoval, za dokončený.\r\n                          Ohodnocení najdete na vašem profilu v sekci reference.', '2013-04-25 02:13:21', 'unread');

-- --------------------------------------------------------

--
-- Struktura tabulky `OldProject`
--

CREATE TABLE IF NOT EXISTS `OldProject` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `forWho` varchar(255) COLLATE utf8_czech_ci NOT NULL COMMENT 'pro koho ten projekt dělal',
  `description` longtext COLLATE utf8_czech_ci NOT NULL COMMENT 'popisek',
  `user_id` int(11) NOT NULL COMMENT 'Uživatel ke kterému to patří',
  PRIMARY KEY (`id`),
  KEY `asasdasddf` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=2 ;

--
-- Vypisuji data pro tabulku `OldProject`
--

INSERT INTO `OldProject` (`id`, `forWho`, `description`, `user_id`) VALUES
(1, 'EDUCAnet', 'Nejlepší zaměstnavatel. ', 58);

-- --------------------------------------------------------

--
-- Struktura tabulky `Project`
--

CREATE TABLE IF NOT EXISTS `Project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `owner` int(11) NOT NULL COMMENT 'Owner projektu (User)',
  `status` enum('finished','live','locked') COLLATE utf8_czech_ci NOT NULL DEFAULT 'live',
  `description` longtext COLLATE utf8_czech_ci NOT NULL COMMENT 'Dodatečné informace o projektu',
  `deadline` date NOT NULL,
  `pricing` enum('perHour','perProject') COLLATE utf8_czech_ci NOT NULL,
  `reward` int(11) NOT NULL,
  `scale` enum('1','2','3') COLLATE utf8_czech_ci NOT NULL COMMENT 'Rozsah projektu',
  `location` int(11) DEFAULT NULL COMMENT 'Cizí klíč do tabulky měst',
  PRIMARY KEY (`id`),
  KEY `reference` (`owner`),
  KEY `location` (`location`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Tabulka projektů' AUTO_INCREMENT=161 ;

--
-- Vypisuji data pro tabulku `Project`
--

INSERT INTO `Project` (`id`, `name`, `owner`, `status`, `description`, `deadline`, `pricing`, `reward`, `scale`, `location`) VALUES
(155, 'Hledání duálních cyklických závislostí', 58, 'live', '<p>Dobr&yacute; den,</p>\r\n<p>pracuji na projektu s grafy (aka mapa). M&aacute;m probl&eacute;m, že když hled&aacute;m cestu z Prahy do Brna, vypoč&iacute;t&aacute; mi to nejkrat&scaron;&iacute; cestu za <strong>-3</strong> hodiny. Mysl&iacute;m si, že se v grafu vyskytuj&iacute; du&aacute;ln&iacute; cyklick&eacute; z&aacute;vislosti. D&aacute;m 700Kč na hodinu tomu, kdo je najde v&scaron;echny.</p>', '2013-08-01', 'perHour', 700, '3', 37),
(156, 'Ztráta brýlí', 58, 'live', '<p>Dobr&yacute; den,</p>\r\n<p>ztratil jsem u mě doma slunečn&iacute; br&yacute;le a potřeboval bych nějak&eacute;ho schopn&eacute;ho policistu, aby mi je na&scaron;el.</p>', '2013-06-12', 'perProject', 2000, '1', 27),
(157, 'Pomocník na hledání duálních cyklických závislostí', 59, 'live', '<p>Přečetl jsem si kn&iacute;žku o grafech a do&scaron;lo mi, že nějak&eacute; grafy obsahuj&iacute; du&aacute;ln&iacute; cyklick&eacute; z&aacute;vislosti. Potřeboval bych tento pojem bl&iacute;že vysvětlit a naučit se s těmito z&aacute;vislostmi pracovat.</p>', '2013-11-15', 'perHour', 800, '3', 37),
(158, '50 účastníků do Tautology Clubu', 59, 'live', '<p>Potřebuju sehnat 50 podpisů, abych mohl ofici&aacute;lně založit svůj tautology club. 50 podpisů je 50 podpisů. D&aacute;m 200 Kč za jeden podpis, tedy jeden podpis mě bude st&aacute;t 200kč. Kdo se chce přidat, ať se přid&aacute;.</p>', '2013-09-26', 'perProject', 200, '2', 25),
(159, 'Maturitní práce', 61, 'live', '<p>Dobr&yacute; den,</p>\r\n<p>m&aacute;m z&aacute;važn&yacute; probl&eacute;m, potřeboval bych někoho, kdo mi nap&iacute;&scaron;e maturitn&iacute; pr&aacute;ci na IT. Term&iacute;n už se bl&iacute;ž&iacute; a j&aacute; m&aacute;m naprgan&yacute; pouze login a registračku.</p>\r\n<p>Potřebuju doprogramovat tyto věci:</p>\r\n<ul>\r\n<li>N&aacute;kupn&iacute; ko&scaron;&iacute;k</li>\r\n<li>Odesl&aacute;n&iacute; objedn&aacute;vky</li>\r\n<li>Administračn&iacute; sekci</li>\r\n</ul>\r\n<p>D&aacute;le potřebuji cel&yacute; web zabezpečit proti SQL injekc&iacute;m a XSS.</p>\r\n<p>Nab&iacute;z&iacute;m, řekl bych, celkem přiměřen&eacute; finančn&iacute; ohodnocen&iacute;.</p>', '2013-09-01', 'perProject', 20000, '3', 25),
(160, 'Natření pokoje', 63, 'finished', '<p>Dobr&yacute; den,</p>\r\n<p>potřeboval bych natř&iacute;t pokoj na světle hnědou barvu&nbsp;<em style="font-weight: bold; font-style: normal; color: #444444; font-family: arial, sans-serif; font-size: small; line-height: 16px;">#</em><span style="color: #444444; font-family: arial, sans-serif; font-size: small; line-height: 16px;">e3e3e3.</span></p>', '2013-10-16', 'perHour', 250, '1', 25);

-- --------------------------------------------------------

--
-- Struktura tabulky `Project_SubCategory`
--

CREATE TABLE IF NOT EXISTS `Project_SubCategory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `subcategory_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `asd` (`subcategory_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=437 ;

--
-- Vypisuji data pro tabulku `Project_SubCategory`
--

INSERT INTO `Project_SubCategory` (`id`, `project_id`, `subcategory_id`) VALUES
(425, 155, 143),
(426, 155, 250),
(427, 156, 268),
(428, 157, 143),
(429, 157, 250),
(430, 158, 235),
(431, 158, 250),
(432, 159, 142),
(433, 159, 146),
(434, 159, 150),
(435, 159, 163),
(436, 160, 334);

-- --------------------------------------------------------

--
-- Struktura tabulky `Project_User`
--

CREATE TABLE IF NOT EXISTS `Project_User` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `status` enum('attended','revoked','workingOn','finished') COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uzivatel+project+unique` (`user_id`,`project_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=108 ;

--
-- Vypisuji data pro tabulku `Project_User`
--

INSERT INTO `Project_User` (`id`, `user_id`, `project_id`, `status`) VALUES
(105, 59, 155, 'attended'),
(106, 58, 159, 'attended'),
(107, 61, 160, 'finished');

-- --------------------------------------------------------

--
-- Struktura tabulky `Reference`
--

CREATE TABLE IF NOT EXISTS `Reference` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL COMMENT 'Id Projektu, který je hodnocen',
  `owner_id` int(11) NOT NULL COMMENT 'Id Usera, který hodnotí (tj. vlastník projektu)',
  `rated_id` int(11) NOT NULL COMMENT 'ID uživatele, který je hodnoce (tj. pracovník projektu)',
  `rating` int(1) NOT NULL COMMENT 'Hvězdičky, 0-5',
  `comment` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `owner_id` (`owner_id`),
  KEY `rated_id` (`rated_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Tabulka hodnocení projektu' AUTO_INCREMENT=2 ;

--
-- Vypisuji data pro tabulku `Reference`
--

INSERT INTO `Reference` (`id`, `project_id`, `owner_id`, `rated_id`, `rating`, `comment`) VALUES
(1, 160, 63, 61, 3, 'Malíř vybral nekvalitní barvu která nesplnila moje představy.');

-- --------------------------------------------------------

--
-- Struktura tabulky `SubCategory`
--

CREATE TABLE IF NOT EXISTS `SubCategory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL COMMENT 'Název podkategorie',
  `parent` int(11) NOT NULL COMMENT 'FK',
  PRIMARY KEY (`id`),
  KEY `sss` (`parent`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Tabulka podkategorií' AUTO_INCREMENT=588 ;

--
-- Vypisuji data pro tabulku `SubCategory`
--

INSERT INTO `SubCategory` (`id`, `name`, `parent`) VALUES
(5, 'Administrativní pracovník', 3),
(6, 'Asistentka', 3),
(7, 'Fakturantka', 3),
(8, 'Office Manager', 3),
(9, 'Pracovník back office', 3),
(10, 'Recepční', 3),
(11, 'Referent', 3),
(12, 'Tlumočník/překladatel', 3),
(13, 'Vkládání dat do počítače', 3),
(14, 'Autočalouník', 4),
(15, 'Autoelektrikář', 4),
(16, 'Autoklempíř', 4),
(17, 'Automechanik', 4),
(18, 'Fleet manager', 4),
(19, 'Garanční technik', 4),
(20, 'Karosář', 4),
(21, 'Lakýrník', 4),
(22, 'Pracovník pneuservisu', 4),
(23, 'Prodejce vozů', 4),
(24, 'Přijímací technik', 4),
(25, 'Servisní technik', 4),
(26, 'Vedoucí servisu', 4),
(27, 'Delegát cestovní kanceláře', 5),
(28, 'Domovník/správce objektu', 5),
(29, 'Hosteska', 5),
(30, 'Letuška/steward', 5),
(31, 'Pokojská', 5),
(32, 'Portýr/dveřník', 5),
(33, 'Pracovník call centra', 5),
(34, 'Prodejce zájezdů', 5),
(35, 'Průvodce', 5),
(36, 'Recepční', 5),
(37, 'Ředitel/manažer hotelu', 5),
(38, 'Tlumočník/překladatel', 5),
(39, 'Category Manager', 6),
(40, 'Celní deklarant', 6),
(41, 'Dispečer', 6),
(42, 'Disponent', 6),
(43, 'Kontrolor', 6),
(44, 'Kurýr', 6),
(45, 'Letuška/steward', 6),
(46, 'Logistik', 6),
(47, 'Manažer dopravy', 6),
(48, 'Mechanik', 6),
(49, 'Nákupčí', 6),
(50, 'Nákupčí pro prodejní řetězce', 6),
(51, 'Námořník', 6),
(52, 'Pilot', 6),
(53, 'Pracovník letového provozu', 6),
(54, 'Pracovník na železnici', 6),
(55, 'Pracovník poštovního provozu', 6),
(56, 'Ředitel/manažer logistiky', 6),
(57, 'Ředitel/manažer nákupu', 6),
(58, 'Řidič', 6),
(59, 'Řidič vysokozdvižného vozíku', 6),
(60, 'Skladník/manipulant', 6),
(61, 'Specialista logistiky', 6),
(62, 'Speditér', 6),
(63, 'Strojvedoucí', 6),
(64, 'Supply Chain Specialist', 6),
(65, 'Taxikář/osobní řidič', 6),
(66, 'Technik', 6),
(67, 'Vedoucí/manažer skladu', 6),
(68, 'Závozník', 6),
(69, 'Asistent v pojišťovnictví', 7),
(70, 'Auditor', 7),
(71, 'Bankovní poradce', 7),
(72, 'Bankovní specialista', 7),
(73, 'Daňový poradce/specialista', 7),
(74, 'Ekonom', 7),
(75, 'Fakturantka', 7),
(76, 'Financial Controller', 7),
(77, 'Finanční analytik', 7),
(78, 'Finanční poradce', 7),
(79, 'Finanční ředitel/manažer', 7),
(80, 'Finanční specialista', 7),
(81, 'Finanční účetní', 7),
(82, 'Firemní bankéř/Relationship Manager', 7),
(83, 'Hlavní účetní', 7),
(84, 'Konzultant', 7),
(85, 'Leasingový specialista', 7),
(86, 'Likvidátor pojistných událostí', 7),
(87, 'Makléř', 7),
(88, 'Metodik', 7),
(89, 'Mzdová účetní', 7),
(90, 'Odborný asistent', 7),
(91, 'Odhadce', 7),
(92, 'Osobní bankéř', 7),
(93, 'Pojistný matematik', 7),
(94, 'Pojistný zprostředkovatel', 7),
(95, 'Pojišťovací poradce', 7),
(96, 'Pokladní', 7),
(97, 'Pracovník call centra', 7),
(98, 'Pracovník na přepážce', 7),
(99, 'Project Manager', 7),
(100, 'Risk Manager', 7),
(101, 'Rozpočtář/kalkulant', 7),
(102, 'Specialista pohledávek', 7),
(103, 'Specialista splátkového prodeje', 7),
(104, 'Specialista treasury', 7),
(105, 'Statistik', 7),
(106, 'Účetní', 7),
(107, 'Účetní metodik', 7),
(108, 'Úvěrový specialista', 7),
(109, 'Barman', 8),
(110, 'Cukrář', 8),
(111, 'Číšník/servírka', 8),
(112, 'F&B Manager ', 8),
(113, 'Hosteska', 8),
(114, 'Kuchař', 8),
(115, 'Pekař', 8),
(116, 'Pomocná síla do kuchyně', 8),
(117, 'Someliér', 8),
(118, 'Šéfkuchař', 8),
(119, 'Vedoucí provozu', 8),
(120, 'Vedoucí/manažer restaurace', 8),
(121, 'Zaměstnanec provozu restaurace', 8),
(122, 'Cukrář', 9),
(123, 'Dělník', 9),
(124, 'Chemický inženýr', 9),
(125, 'Chemik', 9),
(126, 'Kontrolor', 9),
(127, 'Laborant', 9),
(128, 'Mistr', 9),
(129, 'Pekař', 9),
(130, 'Pracovník výzkumu a vývoje', 9),
(131, 'Řezník/uzenář', 9),
(132, 'Technik potravinářské výroby', 9),
(133, 'Technik v chemickém průmyslu', 9),
(134, 'Technolog potravinářské výroby', 9),
(135, 'Technolog v chemickém průmyslu', 9),
(136, 'Vedoucí/manažer laboratoře', 9),
(137, 'Výrobní ředitel/manažer', 9),
(138, 'Account Manager', 10),
(139, 'Animace 3D', 10),
(140, 'Architekt HW systémů', 10),
(141, 'Auditor', 10),
(142, 'Databázový specialista', 10),
(143, 'IT analytik', 10),
(144, 'IT konzultant', 10),
(145, 'IT ředitel/manažer', 10),
(146, 'IT Security Specialist', 10),
(147, 'Lektor/instruktor', 10),
(148, 'Operátor ', 10),
(149, 'Pracovník help desku', 10),
(150, 'Programátor', 10),
(151, 'Project Administrator', 10),
(152, 'Project Manager', 10),
(153, 'Quality Manager', 10),
(154, 'Specialista ERP', 10),
(155, 'Správce aplikačního SW', 10),
(156, 'Správce operačních systémů a sítí', 10),
(157, 'Support IS', 10),
(158, 'SW architekt', 10),
(159, 'Technical Writer', 10),
(160, 'Technik HW', 10),
(161, 'Tester', 10),
(162, 'Vedoucí vývoje/Team Leader', 10),
(163, 'Webdesigner', 10),
(164, 'Webmaster', 10),
(165, 'Animace 3D', 11),
(166, 'Aranžér/dekoratér', 11),
(167, 'Dabing', 11),
(168, 'Designer', 11),
(169, 'Fotograf', 11),
(170, 'Grafik', 11),
(171, 'Herec', 11),
(172, 'Kameraman', 11),
(173, 'Knihovník', 11),
(174, 'Kurátor', 11),
(175, 'Maskér', 11),
(176, 'Moderátor', 11),
(177, 'Osvětlovač', 11),
(178, 'Pracovník audiovize', 11),
(179, 'Produkční', 11),
(180, 'Promítač', 11),
(181, 'Průmyslový designer', 11),
(182, 'Rekvizitář', 11),
(183, 'Režisér', 11),
(184, 'Stavěč dekorací', 11),
(185, 'Střihač', 11),
(186, 'Tanečník', 11),
(188, 'Zahradní architekt', 11),
(189, 'Zpěvák', 11),
(190, 'Zvukař', 11),
(191, 'Account Manager', 12),
(192, 'Animace 3D', 12),
(193, 'Asistent v médiích, reklamě, PR', 12),
(194, 'Brand manager', 12),
(195, 'Category Manager', 12),
(196, 'Copywriter', 12),
(197, 'Designer', 12),
(198, 'Direct Marketing', 12),
(199, 'DTP operátor', 12),
(200, 'Editor', 12),
(201, 'Event Specialist', 12),
(202, 'Fotograf', 12),
(203, 'Grafik', 12),
(204, 'Konzultant v médiích, reklamě, PR', 12),
(205, 'Korektor', 12),
(206, 'Kreativec', 12),
(207, 'Marketingová komunikace', 12),
(208, 'Marketingová strategie', 12),
(209, 'Marketingový analytik', 12),
(210, 'Marketingový ředitel/manažer', 12),
(211, 'Marketingový výzkum', 12),
(212, 'Media Planner', 12),
(213, 'Merchandiser', 12),
(214, 'Novinář, publicista, redaktor', 12),
(215, 'Online Marketing', 12),
(216, 'Product Manager', 12),
(217, 'Product Specialist', 12),
(218, 'Produkční', 12),
(219, 'Project Manager', 12),
(220, 'Promotér', 12),
(221, 'Průmyslový designer', 12),
(222, 'Sociolog', 12),
(223, 'Specialista marketingu', 12),
(224, 'Specialista PR', 12),
(225, 'Šéfredaktor', 12),
(226, 'Tiskový mluvčí', 12),
(227, 'Trade Marketing', 12),
(229, 'Archeolog', 13),
(230, 'Astronom', 13),
(231, 'Biolog', 13),
(232, 'Country Director/Manager', 13),
(233, 'Ekolog', 13),
(234, 'Filolog', 13),
(235, 'Filozof', 13),
(236, 'Finanční ředitel/manažer', 13),
(237, 'Fyzik', 13),
(238, 'Generální ředitel', 13),
(239, 'Genetik', 13),
(240, 'Geolog', 13),
(241, 'Historik', 13),
(242, 'HR ředitel/manažer', 13),
(243, 'Hydrolog', 13),
(244, 'Chemik', 13),
(245, 'IT ředitel/manažer', 13),
(246, 'Jednatel', 13),
(247, 'Konstruktér', 13),
(248, 'Laborant', 13),
(249, 'Marketingový ředitel/manažer', 13),
(250, 'Matematik', 13),
(251, 'Meteorolog', 13),
(252, 'Obchodní ředitel/manažer', 13),
(253, 'Project Manager', 13),
(254, 'Provozní ředitel/manažer', 13),
(255, 'Přírodovědec', 13),
(256, 'Ředitel', 13),
(257, 'Ředitel/manažer kvality', 13),
(258, 'Ředitel/manažer logistiky', 13),
(259, 'Ředitel/manažer nákupu', 13),
(260, 'Sociolog', 13),
(261, 'Specialista vývojového týmu', 13),
(262, 'Technický ředitel/manažer', 13),
(263, 'Vědecko výzkumný pracovník', 13),
(264, 'Výkonný ředitel', 13),
(265, 'Výrobní ředitel/manažer', 13),
(266, 'Vývojový inženýr', 13),
(267, 'Vývojový technolog', 13),
(268, 'Policista', 14),
(269, 'Pracovník ostrahy', 14),
(270, 'Pracovník vězeňské služby', 14),
(271, 'Specialista zabezpečovacích zařízení', 14),
(272, 'Technik BOZP', 14),
(273, 'Technik požární ochrany', 14),
(274, 'Voják', 14),
(275, 'Vrátný', 14),
(276, 'HR Business Partner', 15),
(277, 'HR generalista', 15),
(278, 'HR konzultant', 15),
(279, 'HR ředitel/manažer', 15),
(280, 'HR specialista', 15),
(281, 'Inspektor', 15),
(282, 'Knihovník', 15),
(283, 'Kouč', 15),
(284, 'Lektor/instruktor', 15),
(285, 'Mzdová účetní', 15),
(286, 'Náborář', 15),
(287, 'Personalista', 15),
(288, 'Právník', 15),
(289, 'Project Manager', 15),
(290, 'Psycholog', 15),
(291, 'Ředitel', 15),
(292, 'Specialista HR marketingu', 15),
(293, 'Specialista pro odměňování a benefity', 15),
(294, 'Specialista pro rozvoj a vzdělávání', 15),
(295, 'Speciální pedagog', 15),
(296, 'Školník', 15),
(297, 'Trenér/školitel', 15),
(298, 'Učitel mateřské školy', 15),
(299, 'Učitel střední školy', 15),
(300, 'Učitel základní školy', 15),
(301, 'Vychovatel', 15),
(302, 'Vysokoškolský učitel', 15),
(303, 'Account Manager', 16),
(304, 'Analytik prodeje', 16),
(305, 'Business Development Manager', 16),
(306, 'Farmaceutický reprezentant', 16),
(307, 'Finanční poradce', 16),
(308, 'Key Account Manager', 16),
(309, 'Manažer partnerského prodeje', 16),
(310, 'Manažer prodejního týmu', 16),
(311, 'Merchandiser', 16),
(312, 'Obchodní asistent', 16),
(313, 'Obchodní referent', 16),
(314, 'Obchodní ředitel/manažer', 16),
(315, 'Obchodní zástupce', 16),
(316, 'Pojišťovací poradce', 16),
(317, 'Pokladní', 16),
(318, 'Pracovník call centra', 16),
(319, 'Prodavač', 16),
(320, 'Prodejce po telefonu/Telesales', 16),
(321, 'Project Manager', 16),
(322, 'Realitní makléř', 16),
(323, 'Regionalní obchodní manažer', 16),
(324, 'Trenér obchodních dovedností', 16),
(325, 'Vedoucí úseku prodejny', 16),
(326, 'Vedoucí/manažer prodejny', 16),
(327, 'Dělník', 17),
(328, 'Elektrikář', 17),
(329, 'Hodinář', 17),
(330, 'Hospodyně', 17),
(331, 'Instalatér/topenář', 17),
(332, 'Kovář', 17),
(333, 'Krejčí/švadlena', 17),
(334, 'Malíř/natěrač', 17),
(335, 'Mechanik', 17),
(336, 'Operátor CNC strojů', 17),
(337, 'Práce doma', 17),
(338, 'Truhlář', 17),
(339, 'Údržbář', 17),
(340, 'Uklízečka', 17),
(341, 'Zahradník', 17),
(342, 'Zámečník', 17),
(343, 'Zedník', 17),
(344, 'Zlatník/klenotník', 17),
(345, 'Advokát', 18),
(346, 'Advokátní a notářský koncipient', 18),
(347, 'Asistent soudce', 18),
(348, 'Domovník/správce objektu', 18),
(349, 'Exekutor', 18),
(350, 'Fotograf', 18),
(351, 'Hlídání dětí', 18),
(352, 'Hosteska', 18),
(353, 'Kadeřnice', 18),
(354, 'Kosmetička/vizážistka', 18),
(355, 'Manikérka/pedikérka', 18),
(356, 'Masér', 18),
(357, 'Notář', 18),
(358, 'Optik', 18),
(359, 'Pracovník pohřební služby', 18),
(360, 'Pracovník poštovního provozu', 18),
(361, 'Právník', 18),
(362, 'Soudce', 18),
(363, 'Soudní zapisovatel', 18),
(364, 'Tlumočník/překladatel', 18),
(365, 'Trenér/instruktor', 18),
(366, 'Archivář', 19),
(367, 'Asistent soudce', 19),
(368, 'Celník', 19),
(369, 'Hasič', 19),
(370, 'Knihovník', 19),
(371, 'Kurátor', 19),
(372, 'Náměstek', 19),
(373, 'Policista', 19),
(374, 'Pracovník charity', 19),
(375, 'Pracovník vězeňské služby', 19),
(376, 'Referent', 19),
(377, 'Ředitel instituce', 19),
(378, 'Soudce', 19),
(379, 'Specialista na fondy EU', 19),
(380, 'Správní rada', 19),
(381, 'Tajemník', 19),
(382, 'Úředník', 19),
(383, 'Vedoucí odboru/oddělení', 19),
(384, 'Voják', 19),
(385, 'Architekt', 20),
(386, 'Dělník', 20),
(387, 'Developer', 20),
(388, 'Facility Manager', 20),
(389, 'Geodet/zeměměřič', 20),
(390, 'Instalatér/topenář', 20),
(391, 'Jeřábník', 20),
(392, 'Klempíř', 20),
(393, 'Konstruktér', 20),
(394, 'Malíř/natěrač', 20),
(395, 'Mistr', 20),
(396, 'Obsluha stavebních strojů', 20),
(397, 'Odhadce', 20),
(398, 'Podlahář', 20),
(399, 'Pokrývač', 20),
(400, 'Project Manager', 20),
(401, 'Projektant', 20),
(402, 'Přípravář', 20),
(403, 'Realitní makléř', 20),
(404, 'Revizní technik', 20),
(405, 'Rozpočtář/kalkulant', 20),
(406, 'Statik', 20),
(407, 'Stavbyvedoucí', 20),
(408, 'Stavební dozor', 20),
(409, 'Svářeč', 20),
(410, 'Technik ve stavebnictví', 20),
(411, 'Technolog ve stavebnictví', 20),
(412, 'Tesař', 20),
(413, 'Zedník', 20),
(414, 'CAD/CAM specialista', 21),
(415, 'Dělník', 21),
(416, 'Elektrikář', 21),
(417, 'Elektroinženýr', 21),
(418, 'Elektromechanik', 21),
(419, 'Elektromontér', 21),
(420, 'Elektrotechnik', 21),
(421, 'Energetik', 21),
(422, 'Konstruktér', 21),
(423, 'Mistr', 21),
(424, 'Plynař', 21),
(425, 'Pracovník výzkumu a vývoje', 21),
(426, 'Procesní inženýr', 21),
(427, 'Project Manager', 21),
(428, 'Průmyslový designer', 21),
(429, 'Revizní technik', 21),
(430, 'Servisní technik', 21),
(431, 'Seřizovač/programátor CNC strojů', 21),
(432, 'Specialista zabezpečovacích zařízení', 21),
(433, 'Technický ředitel/manažer', 21),
(434, 'Technik', 21),
(435, 'Technik v energetice', 21),
(436, 'Technik v plynárenství', 21),
(437, 'Technolog', 21),
(438, 'Technolog v energetice', 21),
(439, 'Technolog v plynárenství', 21),
(440, 'Topič', 21),
(441, 'Výrobní ředitel/manažer', 21),
(442, 'Vývojový inženýr', 21),
(443, 'Vývojový technolog', 21),
(444, 'Zkušební technik', 21),
(445, 'Elektrikář', 22),
(446, 'Elektroinženýr', 22),
(447, 'Elektromechanik', 22),
(448, 'Elektromontér', 22),
(449, 'Elektrotechnik', 22),
(450, 'Konstruktér', 22),
(451, 'Pracovník call centra', 22),
(452, 'Project Manager', 22),
(453, 'Projektant', 22),
(454, 'Revizní technik', 22),
(455, 'Specialista mobilních technologií', 22),
(456, 'Spojový manipulant', 22),
(457, 'SW architekt', 22),
(458, 'System Engineer', 22),
(459, 'Technik', 22),
(460, 'Technolog v telekomunikacích', 22),
(461, 'Zkušební technik', 22),
(462, 'Animace 3D', 23),
(463, 'Dělník', 23),
(464, 'Designer', 23),
(465, 'DTP operátor', 23),
(466, 'Editor', 23),
(467, 'Grafik', 23),
(468, 'Korektor', 23),
(469, 'Mistr', 23),
(470, 'Novinář, publicista, redaktor', 23),
(471, 'Servisní technik', 23),
(472, 'Šéfredaktor', 23),
(473, 'Tiskař', 23),
(474, 'Výrobní ředitel/manažer', 23),
(475, 'Auditor', 24),
(476, 'Dělník', 24),
(477, 'Horník/těžební pracovník', 24),
(478, 'Hutník', 24),
(479, 'Keramik', 24),
(480, 'Konstruktér', 24),
(481, 'Kontrolor', 24),
(482, 'Kvalitář', 24),
(483, 'Lakýrník', 24),
(484, 'Mechanik', 24),
(485, 'Metrolog', 24),
(486, 'Mistr', 24),
(487, 'Nástrojař', 24),
(488, 'Obráběč kovů', 24),
(489, 'Operátor CNC strojů', 24),
(490, 'Operátor výroby', 24),
(491, 'Plánovač výroby', 24),
(492, 'Project Manager', 24),
(493, 'Projektant', 24),
(494, 'Průmyslový designer', 24),
(495, 'Revizní technik', 24),
(496, 'Ředitel/manažer kvality', 24),
(497, 'Servisní technik', 24),
(498, 'Seřizovač/programátor CNC strojů', 24),
(499, 'Sklář', 24),
(500, 'Specialista ISO', 24),
(501, 'Strojní inženýr', 24),
(502, 'Strojní zámečník', 24),
(503, 'Supervizor výroby', 24),
(504, 'Svářeč', 24),
(505, 'Šička', 24),
(506, 'Technický ředitel/manažer', 24),
(507, 'Technik ve strojírenství', 24),
(508, 'Technik výroby', 24),
(509, 'Technolog ve strojírenství', 24),
(510, 'Technolog výroby', 24),
(511, 'Vedoucí výroby', 24),
(512, 'Výrobní ředitel/manažer', 24),
(513, 'Pracovník back office', 25),
(514, 'Pracovník call centra', 25),
(515, 'Pracovník front office', 25),
(516, 'Pracovník help desku', 25),
(517, 'Pracovník reklamačního oddělení', 25),
(518, 'Prodejce po telefonu/Telesales', 25),
(519, 'Vedoucí/manažer týmu', 25),
(520, 'Alergolog/imunolog', 26),
(521, 'Anesteziolog', 26),
(522, 'Asistent klinických studií', 26),
(523, 'Dermatolog', 26),
(524, 'Diabetolog', 26),
(525, 'Farmaceutický reprezentant', 26),
(526, 'Fyzioterapeut', 26),
(527, 'Gynekolog/porodník', 26),
(528, 'Chirurg', 26),
(529, 'Internista', 26),
(530, 'Kardiolog', 26),
(531, 'Laborant', 26),
(532, 'Lékárenský laborant', 26),
(533, 'Lékárník', 26),
(534, 'Logoped', 26),
(535, 'Manažer klinických studií', 26),
(536, 'Medical Advisor', 26),
(537, 'Monitor klinických studií', 26),
(538, 'Neurolog', 26),
(539, 'Odborný asistent ve výzkumu a vývoji', 26),
(540, 'Oftalmolog', 26),
(541, 'Onkolog', 26),
(542, 'Optik', 26),
(543, 'Ortoped', 26),
(544, 'Ošetřovatel', 26),
(545, 'Otorinolaryngolog', 26),
(546, 'Patolog', 26),
(547, 'Pediatr', 26),
(548, 'Porodní asistentka', 26),
(549, 'Product Manager', 26),
(550, 'Psychiatr', 26),
(551, 'Psycholog', 26),
(552, 'Radiolog', 26),
(553, 'Regulatory Manager', 26),
(554, 'Sexuolog', 26),
(555, 'Sociální pracovník', 26),
(556, 'Sportovní lékař', 26),
(557, 'Stomatolog', 26),
(558, 'Vědecko výzkumný pracovník', 26),
(559, 'Vedoucí/manažer lékárny', 26),
(560, 'Veterinární lékař', 26),
(561, 'Všeobecný lékař', 26),
(562, 'Záchranář', 26),
(563, 'Zdravotní sestra', 26),
(576, 'Agronom', 27),
(577, 'Dělník', 27),
(578, 'Ekolog', 27),
(579, 'Lesník', 27),
(580, 'Mechanik', 27),
(581, 'Ošetřovatel/chovatel zvířat', 27),
(582, 'Referent životního prostředí', 27),
(583, 'Řidič', 27),
(584, 'Technik', 27),
(585, 'Veterinární lékař', 27),
(586, 'Vodohospodář', 27),
(587, 'Zootechnik', 27);

-- --------------------------------------------------------

--
-- Struktura tabulky `Thread`
--

CREATE TABLE IF NOT EXISTS `Thread` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `User`
--

CREATE TABLE IF NOT EXISTS `User` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fbuid` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `name` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `gender` enum('male','female','dunno') COLLATE utf8_czech_ci NOT NULL DEFAULT 'dunno',
  `mail` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `aboutMe` blob,
  `full_id` int(11) DEFAULT NULL,
  `thumbnail_id` int(11) DEFAULT NULL,
  `pass` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `activated` int(1) NOT NULL COMMENT '0 - ještě neaktivoval účet, 1 - účet je aktivní, -1 uživatel je zabanován',
  `role` enum('user','admin') COLLATE utf8_czech_ci NOT NULL DEFAULT 'user',
  `city` int(11) DEFAULT NULL,
  `icq` int(11) DEFAULT NULL,
  `skype` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `telephone` int(11) DEFAULT NULL,
  `resetCode` varchar(64) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'klíč pro změnu hesla',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `mail` (`mail`),
  UNIQUE KEY `fbuid` (`fbuid`),
  KEY `full_id` (`full_id`),
  KEY `thumbnail_id` (`thumbnail_id`),
  KEY `city` (`city`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Tabulka uživatelů' AUTO_INCREMENT=64 ;

--
-- Vypisuji data pro tabulku `User`
--

INSERT INTO `User` (`id`, `fbuid`, `name`, `gender`, `mail`, `aboutMe`, `full_id`, `thumbnail_id`, `pass`, `activated`, `role`, `city`, `icq`, `skype`, `telephone`, `resetCode`) VALUES
(58, NULL, 'MiroslavCsonka', 'male', 'miroslav.csonka@educanet.cz', 0x50726f6772616d756a692076206e656a6e6f76c49b6ac5a1c3ad6d206a617a796365204e6574746520322e302e31302e204f766cc3a164c3a16d2069206672616d65776f726b206a6176617363726970742e204a73656d206d696c6f766ec3ad6b207072c3a163652073206d79c5a1c3ad2c20616c6520692073206b6cc3a17665736e6963c3ad207369206f62c48d617320706f6872616a752e20, NULL, NULL, 'heslo', 1, 'admin', 37, 969696969, 'miluju.rad.microsoft', 0, NULL),
(59, NULL, 'DavidNosek', 'dunno', 'david.nosek@educanet.cz', NULL, NULL, NULL, 'heslo', 1, 'user', NULL, NULL, NULL, NULL, NULL),
(60, NULL, 'JiriHaase', 'male', 'jiri.haase@educanet.cz', NULL, NULL, NULL, 'heslo', 1, 'user', NULL, NULL, NULL, NULL, NULL),
(61, NULL, 'KristianJindra', 'female', 'kristian.jindra@educanet.cz', NULL, NULL, NULL, 'heslo', -1, 'user', NULL, NULL, NULL, NULL, NULL),
(63, NULL, 'JohnDoe', 'male', 'johndoe@volnanabidka.cz', NULL, NULL, NULL, 'heslo', 1, 'user', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `User_SubCategory`
--

CREATE TABLE IF NOT EXISTS `User_SubCategory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'ID uživatele',
  `subCategory_id` int(11) NOT NULL COMMENT 'ID kategorie',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `subCategory_id` (`subCategory_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=10 ;

--
-- Vypisuji data pro tabulku `User_SubCategory`
--

INSERT INTO `User_SubCategory` (`id`, `user_id`, `subCategory_id`) VALUES
(7, 58, 142),
(8, 58, 150);

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `Comment`
--
ALTER TABLE `Comment`
  ADD CONSTRAINT `Comment_ibfk_1` FOREIGN KEY (`from`) REFERENCES `User` (`id`),
  ADD CONSTRAINT `Comment_ibfk_2` FOREIGN KEY (`project`) REFERENCES `Project` (`id`);

--
-- Omezení pro tabulku `Education`
--
ALTER TABLE `Education`
  ADD CONSTRAINT `Education_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`);

--
-- Omezení pro tabulku `Group`
--
ALTER TABLE `Group`
  ADD CONSTRAINT `Group_ibfk_1` FOREIGN KEY (`thread_id`) REFERENCES `Thread` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Group_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`),
  ADD CONSTRAINT `Group_ibfk_5` FOREIGN KEY (`last_id`) REFERENCES `Message` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `Group_ibfk_6` FOREIGN KEY (`lastRead_id`) REFERENCES `Message` (`id`) ON DELETE SET NULL;

--
-- Omezení pro tabulku `Invitation`
--
ALTER TABLE `Invitation`
  ADD CONSTRAINT `Invitation_ibfk_1` FOREIGN KEY (`inviter`) REFERENCES `User` (`id`),
  ADD CONSTRAINT `Invitation_ibfk_2` FOREIGN KEY (`reciever`) REFERENCES `User` (`id`),
  ADD CONSTRAINT `Invitation_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `Project` (`id`);

--
-- Omezení pro tabulku `Language_User`
--
ALTER TABLE `Language_User`
  ADD CONSTRAINT `asfasd` FOREIGN KEY (`language_id`) REFERENCES `Language` (`id`),
  ADD CONSTRAINT `asfssasd` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`);

--
-- Omezení pro tabulku `Message`
--
ALTER TABLE `Message`
  ADD CONSTRAINT `Message_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `Group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `Notification`
--
ALTER TABLE `Notification`
  ADD CONSTRAINT `Notification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`);

--
-- Omezení pro tabulku `OldProject`
--
ALTER TABLE `OldProject`
  ADD CONSTRAINT `asasdasddf` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`);

--
-- Omezení pro tabulku `Project`
--
ALTER TABLE `Project`
  ADD CONSTRAINT `Project_ibfk_1` FOREIGN KEY (`location`) REFERENCES `City` (`id`),
  ADD CONSTRAINT `reference` FOREIGN KEY (`owner`) REFERENCES `User` (`id`);

--
-- Omezení pro tabulku `Project_SubCategory`
--
ALTER TABLE `Project_SubCategory`
  ADD CONSTRAINT `asd` FOREIGN KEY (`subcategory_id`) REFERENCES `SubCategory` (`id`),
  ADD CONSTRAINT `Project_SubCategory_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `Project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `Project_User`
--
ALTER TABLE `Project_User`
  ADD CONSTRAINT `Project_User_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Project_User_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `Project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `Reference`
--
ALTER TABLE `Reference`
  ADD CONSTRAINT `Reference_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `Project` (`id`),
  ADD CONSTRAINT `Reference_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `User` (`id`),
  ADD CONSTRAINT `Reference_ibfk_3` FOREIGN KEY (`rated_id`) REFERENCES `User` (`id`);

--
-- Omezení pro tabulku `SubCategory`
--
ALTER TABLE `SubCategory`
  ADD CONSTRAINT `sss` FOREIGN KEY (`parent`) REFERENCES `Category` (`id`);

--
-- Omezení pro tabulku `User`
--
ALTER TABLE `User`
  ADD CONSTRAINT `User_ibfk_1` FOREIGN KEY (`full_id`) REFERENCES `File` (`id`),
  ADD CONSTRAINT `User_ibfk_2` FOREIGN KEY (`thumbnail_id`) REFERENCES `File` (`id`),
  ADD CONSTRAINT `User_ibfk_3` FOREIGN KEY (`city`) REFERENCES `City` (`id`);

--
-- Omezení pro tabulku `User_SubCategory`
--
ALTER TABLE `User_SubCategory`
  ADD CONSTRAINT `User_SubCategory_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `User_SubCategory_ibfk_2` FOREIGN KEY (`subCategory_id`) REFERENCES `SubCategory` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
