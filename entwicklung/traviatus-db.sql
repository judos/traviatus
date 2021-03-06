-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 01. Jun 2020 um 14:40
-- Server-Version: 10.4.11-MariaDB
-- PHP-Version: 7.4.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `traviatus`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr1_ally`
--

CREATE TABLE `tr1_ally` (
  `id` int(10) UNSIGNED NOT NULL,
  `tag` varchar(4) NOT NULL DEFAULT '',
  `name` varchar(16) NOT NULL DEFAULT '',
  `beschreibung` text NOT NULL,
  `beschreibung2` text NOT NULL,
  `einwohner` mediumint(9) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `tr1_ally`
--

INSERT INTO `tr1_ally` (`id`, `tag`, `name`, `beschreibung`, `beschreibung2`, `einwohner`) VALUES
(2, 'asdf', 'qwer', '', '', 758);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr1_ally_chat`
--

CREATE TABLE `tr1_ally_chat` (
  `ally_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `zeit` datetime NOT NULL,
  `text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `tr1_ally_chat`
--

INSERT INTO `tr1_ally_chat` (`ally_id`, `user_id`, `zeit`, `text`) VALUES
(2, 1, '2020-06-01 13:56:09', 'test');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr1_ally_einladungen`
--

CREATE TABLE `tr1_ally_einladungen` (
  `ally_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr1_ally_kampfe`
--

CREATE TABLE `tr1_ally_kampfe` (
  `keyid` int(10) UNSIGNED NOT NULL,
  `ally_id` int(10) UNSIGNED NOT NULL,
  `datetime` datetime NOT NULL,
  `betreff` text NOT NULL,
  `text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `tr1_ally_kampfe`
--

INSERT INTO `tr1_ally_kampfe` (`keyid`, `ally_id`, `datetime`, `betreff`, `text`) VALUES
(1, 2, '2012-12-04 00:23:39', 'Kolonie greift Neues Dorf an', '1:Angreifer:<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=1\">Kolonie (3|1)</a>\r3:3\r4:Einheiten:0:0:0:165:0:0:0:0:0:0\r4:Verluste:0:0:0:0:0:0:0:0:0:0\r4:'),
(2, 2, '2012-12-04 09:58:12', 'Kolonie greift Neues Dorf an', '1:Angreifer:<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=1\">Kolonie (3|1)</a>\r3:3\r4:Einheiten:0:0:0:165:0:0:0:0:0:0\r4:Verluste:0:0:0:0:0:0:0:0:0:0\r4:'),
(3, 2, '2012-12-04 14:48:06', 'Kolonie greift Neues Dorf an', '1:Angreifer:<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=1\">Kolonie (3|1)</a>\r3:3\r4:Einheiten:0:0:0:165:0:0:0:0:0:0\r4:Verluste:0:0:0:0:0:0:0:0:0:0\r4:'),
(4, 2, '2012-12-04 15:27:34', 'Kolonie greift Neues Dorf an', '1:Angreifer:<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=1\">Kolonie (3|1)</a>\r3:3\r4:Einheiten:0:0:0:165:0:0:0:0:0:0\r4:Verluste:0:0:0:0:0:0:0:0:0:0\r4:'),
(5, 2, '2012-12-04 15:31:33', 'Kolonie greift Neues Dorf an', '1:Angreifer:<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=1\">Kolonie (3|1)</a>\r3:3\r4:Einheiten:0:0:0:165:0:0:0:0:0:0\r4:Verluste:0:0:0:0:0:0:0:0:0:0\r4:'),
(6, 2, '2012-12-04 15:36:14', 'Kolonie greift Neues Dorf an', '1:Angreifer:<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=1\">Kolonie (3|1)</a>\r3:3\r4:Einheiten:0:0:0:165:0:0:0:0:0:0\r4:Verluste:0:0:0:0:0:0:0:0:0:0\r4:'),
(7, 2, '2012-12-04 20:44:33', 'Kolonie greift Neues Dorf an', '1::Angreifer::<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=1\">Kolonie (3|1)</a>\r3::3\r4::Einheiten::0::0::0::165::0::0::0::0::0::0::0\r4::Verluste::0::0::0::0::0::0::0::0::0::0::0\r4::'),
(8, 2, '2012-12-04 20:45:52', 'Kolonie greift Neues Dorf an', '1::Angreifer::<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=1\">Kolonie (3|1)</a>\r3::3\r4::Einheiten::0::0::0::165::0::0::0::0::0::0::0\r4::Verluste::0::0::0::0::0::0::0::0::0::0::0\r4::'),
(9, 2, '2012-12-04 20:55:57', 'Kolonie greift Neues Dorf an', '1::Angreifer::<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=2\">Neues Dorf (3|2)</a>\r3::3\r4::Einheiten::0::0::0::165::0::0::0::0::0::0::0\r4::Verluste::0::0::0::0::0::0::0::0::0::0::0\r4::'),
(10, 2, '2012-12-04 20:59:51', 'Kolonie greift Neues Dorf an', '1::Angreifer::<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=2\">Neues Dorf (3|2)</a>\r3::3\r4::Einheiten::0::0::0::165::0::0::0::0::0::0::0\r4::Verluste::0::0::0::0::0::0::0::0::0::0::0\r4::');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr1_ally_news`
--

CREATE TABLE `tr1_ally_news` (
  `ally_id` int(10) UNSIGNED NOT NULL,
  `news` text NOT NULL,
  `datum` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `tr1_ally_news`
--

INSERT INTO `tr1_ally_news` (`ally_id`, `news`, `datum`) VALUES
(2, '[spieler]1[/spieler] hat [spieler]5[/spieler] in die Allianz eingeladen.', '2012-10-25 15:26:02'),
(2, '[spieler]5[/spieler] hat die Einladung abgelehnt.', '2012-10-25 15:26:51'),
(2, '[spieler]1[/spieler] hat [spieler]5[/spieler] in die Allianz eingeladen.', '2012-10-25 15:26:54'),
(2, '[spieler]5[/spieler] ist in die Allianz eingetreten.', '2012-10-25 15:26:57'),
(2, '[spieler]5[/spieler] hat die Allianz verlassen.', '2012-10-25 15:35:35'),
(2, '[spieler]1[/spieler] hat [spieler]5[/spieler] in die Allianz eingeladen.', '2012-10-25 15:35:39'),
(2, '[spieler]5[/spieler] ist in die Allianz eingetreten.', '2012-10-25 15:35:43');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr1_ally_range`
--

CREATE TABLE `tr1_ally_range` (
  `ally_id` int(10) UNSIGNED NOT NULL,
  `rang_id` int(10) UNSIGNED NOT NULL,
  `rang_name` text NOT NULL,
  `user_einladen` tinyint(1) NOT NULL,
  `rang_vergeben` tinyint(1) NOT NULL,
  `user_entlassen` tinyint(1) NOT NULL,
  `beschreibung_andern` tinyint(1) NOT NULL,
  `rundmail` tinyint(1) NOT NULL,
  `ally_diplomatie` tinyint(1) NOT NULL,
  `ally_forum` tinyint(1) NOT NULL,
  `ally_auflosen` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `tr1_ally_range`
--

INSERT INTO `tr1_ally_range` (`ally_id`, `rang_id`, `rang_name`, `user_einladen`, `rang_vergeben`, `user_entlassen`, `beschreibung_andern`, `rundmail`, `ally_diplomatie`, `ally_forum`, `ally_auflosen`) VALUES
(2, 12, 'Gr', 1, 1, 1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr1_angebote`
--

CREATE TABLE `tr1_angebote` (
  `keyid` int(10) UNSIGNED NOT NULL,
  `user` varchar(16) NOT NULL DEFAULT '',
  `ursprung_x` int(11) NOT NULL,
  `ursprung_y` int(11) NOT NULL,
  `angebot_id` tinyint(4) NOT NULL,
  `angebot` int(11) NOT NULL,
  `nachfrage_id` tinyint(4) NOT NULL,
  `nachfrage` int(11) NOT NULL,
  `handler` tinyint(4) NOT NULL DEFAULT 0,
  `maxzeit` tinyint(4) NOT NULL DEFAULT 0,
  `ally` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `tr1_angebote`
--

INSERT INTO `tr1_angebote` (`keyid`, `user`, `ursprung_x`, `ursprung_y`, `angebot_id`, `angebot`, `nachfrage_id`, `nachfrage`, `handler`, `maxzeit`, `ally`) VALUES
(7, '1', 5, 1, 1, 200, 2, 200, 1, 2, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr1_bugs`
--

CREATE TABLE `tr1_bugs` (
  `id` int(10) UNSIGNED NOT NULL,
  `titel` varchar(24) NOT NULL DEFAULT '',
  `text` tinytext NOT NULL,
  `zeit` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr1_diverses`
--

CREATE TABLE `tr1_diverses` (
  `id` varchar(32) NOT NULL DEFAULT '',
  `value` tinytext NOT NULL,
  `comment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `tr1_diverses`
--

INSERT INTO `tr1_diverses` (`id`, `value`, `comment`) VALUES
('berichte_pro_seite', '20', 'Anzahl Berichte pro Seite aufgelistet'),
('db_version', '10', 'zahl\r\nDie versionsnummer wird benützt um backups einer geänderten Datenbank zu erstellen. Unbedingt ändern bei neuer Struktur in der Datenbank (z.B. neue Tabelle oder neue Felder)'),
('fallen', '10:20:30:40:50:60:70:80:90:100:110:120:130:140:150:160:170:180:190:200', 'zahl1:zahl2\r\nAnzahl Fallen die gebaut werden können in Abhängigkeit der Stufe des Fallenstellers'),
('fallen_bauzeit', '300:0.9', 'startZeit:faktor\r\nDie Zeit in Sekunden die für den Bau einer Falle benötigt wird. Mit jeder weiteren Stufe die der Fallensteller ausgebaut wird multipliziert sie sich mit dem Faktor.'),
('fallen_kosten', '20:30:10:20', 'holz:lehm:eisen:getreide\r\nRohstoffkosten einer Falle'),
('false_login', 'Zurzeit werden Arbeiten vorgenommen.', 'text\r\nEin kommentar im Feld value deaktiviert das login'),
('handler_pro_mp_stufe', '1', 'zahl\r\nAnzahl Händler die pro weitere Stufe des Marktplatzes verfügbar sind'),
('held_kosten', '3', 'faktor\r\nEin Held kostet das x-fache der normalen Einheit aus welcher er ausgebildet wird.'),
('held_trag_faktor', '5', 'Held kann x mal mehr als normale Einheit tragen'),
('held_wiederbeleben_exp', '1', ''),
('lager', '12:17:23:31:40:50:63:78:96:118:144:176:214:259:313:379:457:551:664:800', 'zahl1:zahl2:...\r\nLagerkapazität des Lagers in hundert Einheiten. Stufe1 umfasst so 1200, Stufe2 1700 etc. Einheiten.'),
('mauer_schutz_max', '80:50:65', 'schutzVolk1:schutzVolk2:schutzVolk3\r\nPronzentualer Schutz der mit Stufe 20 des Verteidigungsgebäudes erreicht werden kann pro Volk'),
('max_ausbau_nicht_hd', '10', 'zahl\r\nMaximale Ausbaustufe der Rohstofffelder, wenn das Dorf nicht das Hauptdorf ist.'),
('natur_last_update', '2020-06-01 14:20:32', 'datum zeit\r\nLetzte Aktualisierung der Natur (Tiere in Oasen regenerieren sich)'),
('neue_dorfer', '2:8:20:39:65:99:141:191:251:319:397:486:584:692:811', 'zahl1:zahl2:...\r\nKulturpunkte die benötigt werden für das nächste Dorf in tausend Einheiten'),
('produktion', '2:5:9:15:22:33:55:70:100:145:200:280:375:495:635:800:1000:1250:1600:2100:2800:3800:5400:7800:12000:18000:27500', 'zahl1:zahl2:...\r\nRohstoffproduktion in Einheiten pro Stunde begonnen bei Stufe 0'),
('register', '1', 'might be 1 or a text explaining why no register is possible anymore'),
('rundenstart', '2007-12-17 13:00:00', 'datum zeit\r\nUm welche Zeit die Runde gestartet wurde'),
('startdorftyp', '3', 'zahl\r\nTyp des Startdorfes (bestimmt Verteilung der Rohstofftypen)'),
('start_pos', '1-7:1-7', 'xmin-xmax:ymin:ymax\r\nkoordinaten der start dörfer wenn sich ein neuer Spieler registriert'),
('turnierplatz_ausdauer', '10', 'zahl\r\nAb welcher Entfernung erhöht der Turnierplatz die Geschwindigkeit der Truppen'),
('versteck', '100:150:230:340:500:750:1100:1700:2600:3800:5800:8600:13000:20000:30000', 'kapStufe1:kapStufe2:...\r\nLagerkapazität des Verstecks für jede Stufe einzeln'),
('volker', 'Römer:Germanen:Gallier:Natur', 'name1:name2:...\r\nDie Namen aller Völker, Reihenfolge wichtig\r\nVolk 1 sind Römer, dann Germanen, Gallier und Natur');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr1_dorfer`
--

CREATE TABLE `tr1_dorfer` (
  `x` tinyint(4) NOT NULL DEFAULT 0,
  `y` tinyint(4) NOT NULL DEFAULT 0,
  `name` varchar(16) NOT NULL DEFAULT 'Neues Dorf',
  `user` tinyint(4) NOT NULL DEFAULT 0,
  `einwohner` int(11) NOT NULL DEFAULT 2,
  `grosse` tinyint(4) NOT NULL DEFAULT 0,
  `zustimmung` tinyint(4) NOT NULL DEFAULT 100,
  `expansion` varchar(19) NOT NULL DEFAULT '0',
  `lager` tinytext NOT NULL,
  `geb1` tinytext NOT NULL,
  `geb2` tinytext NOT NULL,
  `geb2t` tinytext NOT NULL,
  `fallen` int(11) NOT NULL,
  `lastupdate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `tr1_dorfer`
--

INSERT INTO `tr1_dorfer` (`x`, `y`, `name`, `user`, `einwohner`, `grosse`, `zustimmung`, `expansion`, `lager`, `geb1`, `geb2`, `geb2t`, `fallen`, `lastupdate`) VALUES
(1, 2, 'ropeko', 5, 14, 1, 100, '0', '80000:80000:80000:80000', '20:25:25:25:25:25:25:25:25:25:25:25:25:25:25:25:25:25', '0:20:20:1:0:0:1:34:0:0:0:0:0:0:0:0:0:0:0:0:1:0', '0:10:11:5:0:0:18:15:0:0:0:0:0:0:0:0:0:0:0:0:16:0', 0, '2020-06-01 14:02:22'),
(2, 2, 'WW Dorf', 1, 7, 0, 100, '0', '9257.4911111102:6637.4911111102:3126.8683333336:65287.179722214', '0:0:0:0:0:0:0:0:0:0:0:0:0:0:0:0:0:0', '1:20:20:0:33:0:0:5:0:0:0:0:0:0:0:0:0:0:0:0:1:0', '38:11:10:0:15:0:0:40:0:0:0:0:0:0:0:0:0:0:0:0:16:0', 0, '2020-06-01 14:20:32'),
(3, 1, 'Kolonie', 1, 104, 0, 100, '0', '31300:31300:31300:31300', '0:10:4:0:0:1:1:6:8:0:0:1:1:0:0:0:0:0', '15:2:2:3:4:1:15:33:0:0:15:1:0:0:1:0:0:0:0:0:1:0', '11:17:37:19:22:8:10:15:0:0:23:25:0:0:23:0:0:0:0:0:16:0', 0, '2020-06-01 14:20:32'),
(3, 2, 'Neues Dorf', 6, 2, 1, 100, '0', '800:800:800:800', '0:0:0:0:0:0:0:0:0:0:0:0:0:0:0:0:0:0', '0:0:0:0:0:0:0:1:0:0:0:0:0:0:0:0:0:0:0:0:0:0', '0:0:0:0:0:0:0:15:0:0:0:0:0:0:0:0:0:0:0:0:0:0', 0, '2020-06-01 14:02:22'),
(4, 4, 'Neues Dorf', 4, 2, 1, 100, '0', '800:800:800:800', '0:0:0:0:0:0:0:0:0:0:0:0:0:0:0:0:0:0', '0:0:0:0:0:0:0:1:0:0:0:0:0:0:0:0:0:0:0:0:0:0', '0:0:0:0:0:0:0:15:0:0:0:0:0:0:0:0:0:0:0:0:0:0', 0, '2020-06-01 14:02:22'),
(5, 1, 'Hauptdorf', 1, 633, 1, 100, '2:3:1:2:2', '83600:83600:83600:83600', '20:20:20:20:20:20:20:20:20:20:20:20:20:20:20:20:20:20', '16:1:10:10:19:1:20:33:10:1:20:20:2:1:14:1:6:2:5:2:15:1:1', '26:38:24:22:19:37:10:15:20:21:11:17:18:39:36:14:28:29:34:35:16:33:17', 10, '2020-06-01 14:39:24');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr1_errorlog`
--

CREATE TABLE `tr1_errorlog` (
  `page` text NOT NULL,
  `msg` text NOT NULL,
  `time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr1_gebeude`
--

CREATE TABLE `tr1_gebeude` (
  `id` tinyint(4) NOT NULL DEFAULT 0,
  `name` varchar(25) NOT NULL DEFAULT '',
  `besch` mediumtext NOT NULL,
  `typ` tinyint(4) NOT NULL DEFAULT 0,
  `baukosten` tinytext NOT NULL,
  `arbeiter` tinyint(4) NOT NULL DEFAULT 0,
  `stufen` tinyint(4) NOT NULL DEFAULT 0,
  `rebuild` tinyint(4) NOT NULL DEFAULT 0,
  `bauzeit` tinytext NOT NULL,
  `needs` tinytext NOT NULL,
  `volksvorteile` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `tr1_gebeude`
--

INSERT INTO `tr1_gebeude` (`id`, `name`, `besch`, `typ`, `baukosten`, `arbeiter`, `stufen`, `rebuild`, `bauzeit`, `needs`, `volksvorteile`) VALUES
(1, 'Holzfäller', 'Der Holzfäller fällt Bäume, um daraus Holz zu gewinnen. Je weiter der Holzfäller ausgebaut wird, desto mehr Holz produziert er.', 1, '40:100:50:60:1.67', 2, 25, 0, '260:360:1.6', '-1', ''),
(2, 'Lehmgrube', 'Aus der Lehmgrube wird der Rohstoff Lehm gewonnen. Je höher die Lehmgrube ausgebaut ist desto mehr Lehm kann produziert werden.', 1, '80:40:80:50:1.67', 2, 25, 0, '220:330:1.6', '-1', ''),
(3, 'Eisenmine', 'Aus den Eisenminen gewinnen die Bergleute den wertvollen Rohstoff Eisen. Je weiter die Eisenmine ausgebaut ist, desto mehr Eisen wird jede Stunde produziert.', 1, '100:80:30:60:1.67', 2, 25, 0, '450:470:1.6', '-1', ''),
(4, 'Getreidefarm', 'In der Getreidefarm wird Nahrung zur Versorgung der Bevölkerung hergestellt. Je höher die Getreidefarm ausgebaut wird, desto mehr Getreide wird produziert.', 1, '70:90:70:20:1.67', 1, 25, 0, '150:290:1.6', '-1', ''),
(5, 'Sägewerk', 'Im Sägewerk wird das Holz der Holzfäller weiterverarbeitet. Je nach Ausbaustufe erhöht sich die Holzproduktion um bis zu 25 Prozent.', 1, '520:380:290:90:1.8', 2, 5, 0, '3000:2700:1.5', '1:1:10', ''),
(6, 'Lehmbrennerei', 'In der Lehmbrennerei wird Lehm zu Ziegelsteinen verarbeitet. Je nach Ausbaustufe erhöht sich die Lehmproduktion um bis zu 25 Prozent.', 1, '440:480:320:50:1.8', 2, 5, 0, '2240:2320:1.5', '1:2:10', ''),
(7, 'Eisengiesserei', 'In der Eisengießerei wird Eisen veredelt. Je nach Ausbaustufe erhöht sich die Eisenproduktion so um bis zu 25 Prozent.', 1, '200:450:510:120:1.8', 2, 5, 0, '4080:3240:1.5', '1:3:10', ''),
(8, 'Getreidemühle', 'In der Getreidemühle wird Getreide zu Mehl gemahlen. Je nach Ausbaustufe erhöht sich die Getreideproduktion um bis zu 25 Prozent.', 1, '500:440:380:1240:1.8', 1, 5, 0, '1840:2120:1.5', '1:4:5', ''),
(9, 'Bäckerei', 'In der Bäckerei wird das Mehl der Mühle zu Brot verbacken. Damit erhöht sich die Getreideproduktion zusammen mit der Mühle um bis zu 50 Prozent.', 1, '1200:1480:870:1600:1.8', 2, 5, 0, '3680:3040:1.5', '2:4:10:8:5', ''),
(10, 'Rohstofflager', 'Im Rohstofflager werden die Rohstoffe Holz, Lehm und Eisen gelagert. Je weiter das Rohstofflager ausgebaut wird, desto mehr Rohstoffe können eingelagert werden.', 1, '130:160:90:40:1.28', 1, 20, 15, '2000:620:1.16', '0', ''),
(11, 'Kornspeicher', 'Im Kornspeicher wird das Getreide der Getreidefarmen gelagert. Je weiter der Kornspeicher ausgebaut wird, desto mehr Getreide kann eingelagert werden.', 1, '80:100:70:20:1.28', 1, 20, 15, '1600:560:1.16', '0', ''),
(12, 'Waffenschmiede', 'In den Schmelzöfen der Waffenschmiede werden die Waffen der Krieger verbessert. Je weiter das Gebäude ausgebaut ist, desto besser können die Waffen geschmiedet werden.', 2, '170:200:380:130:1.28', 3, 20, 0, '2000:620:1.161', '1:22:3', ''),
(13, 'Rüstungsschmiede', 'In den Schmelzöfen der Rüstungsschmiede werden die Rüstungen der Krieger verbessert. Je weiter das Gebäude ausgebaut ist, desto bessere Rüstungen können geschmiedet werden.', 2, '130:210:410:130:1.28', 2, 20, 0, '2000:620:1.161', '1:22:1', ''),
(14, 'Turnierplatz', 'Am Turnierplatz können deine Truppen ihre Ausdauer trainieren. Je weiter dieses Gebäude ausgebaut ist, desto schneller sind deine Einheiten bei Entfernungen über 30 Feldern.', 2, '1750:2250:1530:240:1.28', 2, 20, 0, '3400:960:1.019', '1:16:15', ''),
(15, 'Hauptgebäude', 'Im Hauptgebäude wohnen die Baumeister des Dorfes. Je weiter das Hauptgebäude ausgebaut wird, desto schneller können andere Gebäude gebaut werden.', 3, '70:40:60:20:1.28', 2, 20, 0, '2000:620:1.16', '-1', ''),
(16, 'Versammlungsplatz', 'Am Versammlungsplatz treffen sich die Soldaten des Dorfes. Von dort aus können die Truppen zum Unterstützen, Plündern oder Erobern, zu einem anderen Dorf geschickt werden.', 2, '110:160:90:70:1.28', 1, 20, 0, '2000:620:1.161', '0', ''),
(17, 'Marktplatz', 'Am Marktplatz kann man mit anderen Spielern Rohstoffe tauschen. Je weiter der Marktplatz ausgebaut ist, desto mehr Händler stehen dir zur Verfügung.', 3, '80:70:120:70:1.28', 2, 20, 0, '1800:590:1.16', '3:15:3:10:1:11:1', ''),
(18, 'Botschaft', 'Die Botschaft ist ein Ort für Diplomaten. Je weiter eine Botschaft ausgebaut wird, desto mehr Möglichkeiten stehen dem König offen.', 3, '900:650:750:400:1.28', 2, 5, 0, '2000:620:1.161', '0', ''),
(19, 'Kaserne', 'In der Kaserne können alle Fußtruppen ausgebildet werden. Je weiter die Kaserne ausgebaut ist, desto schneller werden die Truppen ausgebildet.', 2, '210:140:260:120:1.28', 3, 20, 0, '2000:620:1.161', '2:15:3:16:1', ''),
(20, 'Stall', 'In dem Stall können alle berittenen Truppen ausgebildet werden. Je weiter der Stall ausgebaut ist, desto schneller werden die Truppen ausgebildet.', 2, '260:140:220:100:1.28', 3, 20, 0, '2200:650:1.158', '1:22:5:12:3', ''),
(21, 'Werkstatt', 'In der Werkstatt können Belagerungseinheiten wie Katapulte und Rammen gebaut werden. Je weiter die Werkstatt ausgebaut ist, desto schneller kann produziert werden.', 2, '460:510:600:320:1.28', 3, 20, 0, '3000:780:1.161', '2:22:10:15:5', ''),
(22, 'Akademie', 'In der Akademie können neue Einheitentypen erforscht werden. Je weiter die Akademie ausgebaut wird, desto fortschrittlichere Einheiten können erforscht werden.', 2, '220:160:90:40:1.28', 2, 20, 0, '2000:620:1.161', '2:15:3:19:3', ''),
(23, 'Versteck', 'Im Versteck kann im Falle eines Angriffs ein Teil der Rohstoffe aus den Lagern versteckt werden. Diese Rohstoffe können von angreifenden Plünderern nicht geraubt werden.', 2, '40:50:30:10:1.5', 0, 15, 10, '750:420:1.163', '0', ''),
(24, 'Rathaus', 'Im Rathaus kannst du für deine Bürger prunkvolle Feste veranstalten. Durch ein solches Fest erhöht sich die Anzahl deiner Kulturpunkte.', 3, '1250:1100:1260:600:1.28', 3, 20, 0, '12500:2300:1.159', '2:22:10:15:10', ''),
(25, 'Residenz', 'Die Residenz ist ein kleiner Palast, in dem der König weilt, wenn er gerade zu Besuch ist. Die Residenz schützt das Dorf u.a. vor einer feindlichen Übernahme.', 3, '580:460:350:180:1.28', 2, 20, 0, '2000:620:1.161', '1:15:5', ''),
(26, 'Palast', 'Im Palast wohnt der König oder die Königin eines Reiches. Der Palast dient dem Herrscher zur Bestimmung des Hauptdorfes, deswegen kann auch nur ein Palast im ganzen Herrschaftsgebiet errichtet werden.', 3, '550:800:750:250:1.28', 2, 20, 0, '5000:1100:1.158', '2:18:1:15:5', ''),
(27, 'Schatzkammer', 'In den vor kurzem entdeckten Dörfern der Nataren werden die Baupläne vermutet. In der Schatzkammer findet man eine Liste mit den aktuellen Aufenthaltsorten der einzelnen Baupläne.', 3, '2880:2740:2580:990:1.28', 3, 10, 0, '8000:1580:1.161', '-1', ''),
(28, 'Handelskontor', 'Im Handelskontor werden die Marktkarren verbessert und mit starken Pferden ausgestattet. Je weiter der Handelskontor ausgebaut ist, desto mehr können deine Händler transportieren.', 3, '1400:1330:1200:400:1.28', 2, 20, 0, '3000:780:1.161', '2:17:20:20:10', ''),
(29, 'Grosse Kaserne', 'Die große Kaserne ermöglicht die Ausbildung zusätzlicher Truppen. Dies schlägt allerdings mit den 3x Kosten zu Buche.', 2, '630:420:780:360:1.28', 3, 20, 0, '2000:620:1.161', '1:19:20', ''),
(30, 'Grosser Stall', 'Der große Stall ermöglicht die Ausbildung zusätzlicher berittener Truppen. Dies schlägt allerdings mit den 3x Kosten zu Buche.', 2, '780:420:660:300:1.28', 3, 20, 0, '2200:650:1.158', '1:20:20', ''),
(31, 'Stadtmauer', 'Die Stadtmauer schützt deine Siedlung vor Angriffen. Je weiter die Stadtmauer ausgebaut ist, desto leichter wird es deine Siedlung haben, sich erfolgreich gegen die plündernden Horden deiner Gegner zu verteidigen.', 2, '70:90:170:70:1.28', 0, 20, 0, '2000:620:1.161', '1:volk:1', ''),
(32, 'Erdwall', 'Der Erdwall schützt deine Siedlung vor Angriffen. Je weiter der Erdwall ausgebaut ist, desto leichter wird es deine Siedlung haben, sich erfolgreich gegen die plündernden Horden deiner Gegner zu verteidigen.', 2, '120:200:0:80:1.28', 0, 20, 0, '2000:620:1.161', '1:volk:2', ''),
(33, 'Palisade', 'Die Palisade schützt deine Siedlung vor Angriffen. Je weiter die Palisade ausgebaut ist, desto leichter wird es deine Siedlung haben, sich erfolgreich gegen die plündernden Horden deiner Gegner zu verteidigen.', 2, '160:100:80:60:1.28', 0, 20, 0, '2000:620:1.161', '1:volk:3', ''),
(34, 'Steinmetz', 'Der Steinmetz ist ein Experte in der Bearbeitung von Stein. Je höher das Gebäude ausgebaut wird, desto stabiler werden die Bauwerke dieses Dorfes.', 3, '155:130:125:70:1.28', 2, 20, 0, '2200:950:1.159', '2:15:5:26:3', ''),
(35, 'Brauerei', 'In der Brauerei werden für das Volk leckere Getränke gebraut. Das macht deine Einwohner zufriedener und Sie sind schwerer davon zu überzeugen zu einem anderen Herrscher überzulaufen. Zudem kann bei den Festen mehr getrunken werden, was dir mehr Kulturpunkte einbringt.', 3, '1200:1400:1050:2200:1.28', 4, 20, 0, '1800:590:1.16', '2:16:15:24:10', ''),
(36, 'Fallensteller', 'Mit gut versteckten Fallen schützt der Fallensteller dein Dorf. Unachtsame Angreifer können so in Gefangenschaft geraten und stellen für dein Dorf vorübergehend keine Gefahr mehr dar.', 2, '100:100:100:100:1.28', 3, 20, 0, '2000:320:1.16', '2:16:2:-1:3', ''),
(37, 'Heldenhof', 'Im Heldenhof kannst du dir einen Helden ausbilden und ab Gebäudestufe 10 mit dem Helden Rohstoffvorkommen aus deiner Umgebung annektieren.', 2, '700:670:700:240:1.33', 2, 20, 0, '2300:370:1.155', '2:15:3:16:1', ''),
(38, 'Grosses Rohstofflager', 'Im großen Rohstofflager werden die Rohstoffe Holz, Lehm und Eisen gelagert. Je weiter das große Rohstofflager ausgebaut wird, desto mehr Rohstoffe können eingelagert werden. Das große Rohstofflager fasst dreimal so viele Rohstoffe wie das normale Rohstofflager.', 1, '650:800:450:200:1.28', 2, 20, 10, '9000:1740:1.161', '1:10:20', ''),
(39, 'Grosser Kornspeicher', 'Im großen Kornspeicher wird das Getreide der Getreidefarmen gelagert. Je weiter der große Kornspeicher ausgebaut wird, desto mehr Getreide kann eingelagert werden. Der große Kornspeicher fasst dreimal so viel Getreide wie der normale Kornspeicher.', 1, '400:500:350:100:1.28', 2, 20, 10, '7000:1420:1.159', '1:11:20', ''),
(40, 'Weltwunder', 'Das Weltwunder stellt die Krönung einer Zivilisation dar. Nur die Mächtigsten und Reichsten sind in der Lage solch ein Meisterwerk der Baukunst zu errichten und vor neidischen Feinden zu schützen. ', 3, '66700:69050:72200:13200:1.0275', 0, 20, 0, '18000:850:1.016', '-1', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr1_handler`
--

CREATE TABLE `tr1_handler` (
  `keyid` int(10) UNSIGNED NOT NULL,
  `user` varchar(16) NOT NULL DEFAULT '',
  `ursprung_x` int(11) NOT NULL DEFAULT 0,
  `ursprung_y` int(11) NOT NULL DEFAULT 0,
  `von_x` int(11) NOT NULL DEFAULT 0,
  `von_y` int(11) NOT NULL,
  `nach_x` int(11) NOT NULL,
  `nach_y` int(11) NOT NULL,
  `start` datetime NOT NULL DEFAULT current_timestamp(),
  `ziel` datetime NOT NULL DEFAULT current_timestamp(),
  `handler` tinyint(4) NOT NULL DEFAULT 0,
  `speed` tinyint(4) NOT NULL,
  `ress` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr1_hero`
--

CREATE TABLE `tr1_hero` (
  `keyid` int(10) UNSIGNED NOT NULL,
  `user` int(10) UNSIGNED NOT NULL,
  `name` tinytext NOT NULL,
  `lebt` tinyint(1) NOT NULL,
  `troop_id` int(10) UNSIGNED NOT NULL,
  `erfahrung` int(10) UNSIGNED NOT NULL,
  `hp` int(10) UNSIGNED NOT NULL,
  `bonus` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `tr1_hero`
--

INSERT INTO `tr1_hero` (`keyid`, `user`, `name`, `lebt`, `troop_id`, `erfahrung`, `hp`, `bonus`) VALUES
(1, 1, 'Lord Julius der Dritte', 1, 21, 0, 100, '0:1:2:2:0');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr1_lander`
--

CREATE TABLE `tr1_lander` (
  `x` tinyint(4) NOT NULL DEFAULT 0,
  `y` tinyint(4) NOT NULL DEFAULT 0,
  `oase` tinyint(4) NOT NULL DEFAULT 0,
  `typ` tinyint(4) NOT NULL DEFAULT 0,
  `aussehen` tinyint(4) NOT NULL DEFAULT 0,
  `annektiert_user` int(10) UNSIGNED NOT NULL,
  `annektiert_x` int(10) UNSIGNED NOT NULL,
  `annektiert_y` int(10) UNSIGNED NOT NULL,
  `ww` tinyint(3) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `tr1_lander`
--

INSERT INTO `tr1_lander` (`x`, `y`, `oase`, `typ`, `aussehen`, `annektiert_user`, `annektiert_x`, `annektiert_y`, `ww`) VALUES
(1, 1, 1, 1, 0, 0, 0, 0, 0),
(1, 2, 0, 5, 9, 0, 0, 0, 0),
(1, 3, 0, 3, 7, 0, 0, 0, 0),
(1, 4, 0, 4, 5, 0, 0, 0, 0),
(1, 5, 0, 5, 3, 0, 0, 0, 0),
(1, 6, 0, 2, 6, 0, 0, 0, 0),
(1, 7, 0, 1, 1, 0, 0, 0, 0),
(2, 1, 0, 11, 0, 0, 0, 0, 0),
(2, 2, 0, 7, 6, 0, 0, 0, 1),
(2, 3, 0, 12, 2, 0, 0, 0, 0),
(2, 4, 0, 5, 8, 0, 0, 0, 0),
(2, 5, 0, 1, 1, 0, 0, 0, 0),
(2, 6, 0, 14, 5, 0, 0, 0, 0),
(2, 7, 0, 5, 5, 0, 0, 0, 0),
(3, 1, 0, 13, 3, 0, 0, 0, 0),
(3, 2, 0, 1, 2, 0, 0, 0, 0),
(3, 3, 0, 2, 9, 0, 0, 0, 0),
(3, 4, 0, 10, 9, 0, 0, 0, 0),
(3, 5, 0, 10, 0, 0, 0, 0, 0),
(3, 6, 0, 4, 3, 0, 0, 0, 0),
(3, 7, 0, 9, 8, 0, 0, 0, 0),
(4, 1, 0, 12, 9, 0, 0, 0, 0),
(4, 2, 0, 12, 5, 0, 0, 0, 0),
(4, 3, 1, 3, 0, 0, 0, 0, 0),
(4, 4, 0, 12, 1, 0, 0, 0, 0),
(4, 5, 0, 6, 4, 0, 0, 0, 0),
(4, 6, 0, 11, 0, 0, 0, 0, 0),
(4, 7, 0, 2, 3, 0, 0, 0, 0),
(5, 1, 0, 6, 4, 0, 0, 0, 0),
(5, 2, 0, 3, 9, 0, 0, 0, 0),
(5, 3, 0, 9, 1, 0, 0, 0, 0),
(5, 4, 0, 13, 6, 0, 0, 0, 0),
(5, 5, 0, 11, 2, 0, 0, 0, 0),
(5, 6, 0, 5, 6, 0, 0, 0, 0),
(5, 7, 0, 6, 4, 0, 0, 0, 0),
(6, 1, 0, 12, 9, 0, 0, 0, 0),
(6, 2, 0, 1, 3, 0, 0, 0, 0),
(6, 3, 0, 1, 6, 0, 0, 0, 0),
(6, 4, 0, 1, 1, 0, 0, 0, 0),
(6, 5, 0, 8, 8, 0, 0, 0, 0),
(6, 6, 0, 11, 6, 0, 0, 0, 0),
(6, 7, 0, 5, 6, 0, 0, 0, 0),
(7, 1, 0, 14, 6, 0, 0, 0, 0),
(7, 2, 0, 2, 7, 0, 0, 0, 0),
(7, 3, 0, 8, 1, 0, 0, 0, 0),
(7, 4, 0, 4, 0, 0, 0, 0, 0),
(7, 5, 0, 6, 4, 0, 0, 0, 0),
(7, 6, 0, 8, 6, 0, 0, 0, 0),
(7, 7, 0, 11, 3, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr1_land_typen`
--

CREATE TABLE `tr1_land_typen` (
  `typ` tinyint(4) NOT NULL DEFAULT 0,
  `geb` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `tr1_land_typen`
--

INSERT INTO `tr1_land_typen` (`typ`, `geb`) VALUES
(1, '4:4:1:4:4:2:3:4:4:3:3:4:4:1:4:2:1:2'),
(2, '3:4:1:3:2:2:3:4:4:3:3:4:4:1:4:2:1:2'),
(3, '1:4:1:3:2:2:3:4:4:3:3:4:4:1:4:2:1:2'),
(4, '1:4:1:2:2:2:3:4:4:3:3:4:4:1:4:2:1:2'),
(5, '1:4:1:3:1:2:3:4:4:3:3:4:4:1:4:2:1:2'),
(6, '4:4:1:3:4:4:4:4:4:4:4:4:4:4:4:2:4:4'),
(7, '1:4:4:1:2:2:3:4:4:3:3:4:4:1:4:2:1:2'),
(8, '3:4:4:1:2:2:3:4:4:3:3:4:4:1:4:2:1:2'),
(9, '3:4:4:1:1:2:3:4:4:3:3:4:4:1:4:2:1:2'),
(10, '3:4:1:2:2:2:3:4:4:3:3:4:4:1:4:2:1:2'),
(11, '3:1:1:3:1:4:4:3:3:2:2:3:1:4:4:2:4:4'),
(12, '1:4:1:1:2:2:3:4:4:3:3:4:4:1:4:2:1:2'),
(13, '1:4:1:3:2:2:3:2:4:3:3:1:3:1:4:2:1:2'),
(14, '1:1:1:3:2:2:3:2:4:3:3:1:3:1:2:2:1:2');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr1_msg`
--

CREATE TABLE `tr1_msg` (
  `keyid` int(10) UNSIGNED NOT NULL,
  `von` varchar(16) NOT NULL DEFAULT '',
  `an` varchar(16) NOT NULL DEFAULT '',
  `typ` tinyint(4) NOT NULL DEFAULT 0,
  `neu` tinyint(4) NOT NULL DEFAULT 1,
  `zeit` datetime NOT NULL,
  `betreff` varchar(36) NOT NULL DEFAULT '',
  `text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `tr1_msg`
--

INSERT INTO `tr1_msg` (`keyid`, `von`, `an`, `typ`, `neu`, `zeit`, `betreff`, `text`) VALUES
(63, '', 'bla', 3, 1, '2012-12-03 22:54:23', 'Kolonie greift Neues Dorf an', '1:Angreifer:<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=1\">Kolonie (3|1)</a>\r3:3\r4:Einheiten:0:0:0:165:0:0:0:0:0:0\r4:Verluste:0:0:0:0:0:0:0:0:0:0\r4:'),
(65, '', 'bla', 3, 1, '2012-12-03 23:01:30', 'Kolonie greift Neues Dorf an', '1:Angreifer:<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=1\">Kolonie (3|1)</a>\r3:3\r4:Einheiten:0:0:0:165:0:0:0:0:0:0\r4:Verluste:0:0:0:0:0:0:0:0:0:0\r4:'),
(67, '', 'bla', 3, 1, '2012-12-03 23:05:45', 'Kolonie greift Neues Dorf an', '1:Angreifer:<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=1\">Kolonie (3|1)</a>\r3:3\r4:Einheiten:0:0:0:165:0:0:0:0:0:0\r4:Verluste:0:0:0:0:0:0:0:0:0:0\r4:'),
(69, '', 'bla', 3, 1, '2012-12-03 23:08:32', 'Kolonie greift Neues Dorf an', '1:Angreifer:<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=1\">Kolonie (3|1)</a>\r3:3\r4:Einheiten:0:0:0:165:0:0:0:0:0:0\r4:Verluste:0:0:0:0:0:0:0:0:0:0\r4:'),
(71, '', 'bla', 3, 1, '2012-12-03 23:10:22', 'Kolonie greift Neues Dorf an', '1:Angreifer:<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=1\">Kolonie (3|1)</a>\r3:3\r4:Einheiten:0:0:0:165:0:0:0:0:0:0\r4:Verluste:0:0:0:0:0:0:0:0:0:0\r4:'),
(73, '', 'bla', 3, 1, '2012-12-04 00:23:39', 'Kolonie greift Neues Dorf an', '1:Angreifer:<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=1\">Kolonie (3|1)</a>\r3:3\r4:Einheiten:0:0:0:165:0:0:0:0:0:0\r4:Verluste:0:0:0:0:0:0:0:0:0:0\r4:'),
(75, '', 'bla', 3, 1, '2012-12-04 09:58:12', 'Kolonie greift Neues Dorf an', '1:Angreifer:<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=1\">Kolonie (3|1)</a>\r3:3\r4:Einheiten:0:0:0:165:0:0:0:0:0:0\r4:Verluste:0:0:0:0:0:0:0:0:0:0\r4:'),
(77, '', 'bla', 3, 1, '2012-12-04 14:48:06', 'Kolonie greift Neues Dorf an', '1:Angreifer:<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=1\">Kolonie (3|1)</a>\r3:3\r4:Einheiten:0:0:0:165:0:0:0:0:0:0\r4:Verluste:0:0:0:0:0:0:0:0:0:0\r4:'),
(79, '', 'bla', 3, 1, '2012-12-04 15:27:34', 'Kolonie greift Neues Dorf an', '1:Angreifer:<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=1\">Kolonie (3|1)</a>\r3:3\r4:Einheiten:0:0:0:165:0:0:0:0:0:0\r4:Verluste:0:0:0:0:0:0:0:0:0:0\r4:'),
(81, '', 'bla', 3, 1, '2012-12-04 15:31:33', 'Kolonie greift Neues Dorf an', '1:Angreifer:<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=1\">Kolonie (3|1)</a>\r3:3\r4:Einheiten:0:0:0:165:0:0:0:0:0:0\r4:Verluste:0:0:0:0:0:0:0:0:0:0\r4:'),
(83, '', 'bla', 3, 1, '2012-12-04 15:36:14', 'Kolonie greift Neues Dorf an', '1:Angreifer:<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=1\">Kolonie (3|1)</a>\r3:3\r4:Einheiten:0:0:0:165:0:0:0:0:0:0\r4:Verluste:0:0:0:0:0:0:0:0:0:0\r4:'),
(84, '', 'judos', 3, 0, '2012-12-04 20:44:33', 'Kolonie greift Neues Dorf an', '1::Angreifer::<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=1\">Kolonie (3|1)</a>\r3::3\r4::Einheiten::0::0::0::165::0::0::0::0::0::0::0\r4::Verluste::0::0::0::0::0::0::0::0::0::0::0\r4::'),
(85, '', 'bla', 3, 1, '2012-12-04 20:44:33', 'Kolonie greift Neues Dorf an', '1::Angreifer::<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=1\">Kolonie (3|1)</a>\r3::3\r4::Einheiten::0::0::0::165::0::0::0::0::0::0::0\r4::Verluste::0::0::0::0::0::0::0::0::0::0::0\r4::'),
(86, '', 'judos', 3, 0, '2012-12-04 20:45:52', 'Kolonie greift Neues Dorf an', '1::Angreifer::<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=1\">Kolonie (3|1)</a>\r3::3\r4::Einheiten::0::0::0::165::0::0::0::0::0::0::0\r4::Verluste::0::0::0::0::0::0::0::0::0::0::0\r4::'),
(87, '', 'bla', 3, 1, '2012-12-04 20:45:52', 'Kolonie greift Neues Dorf an', '1::Angreifer::<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=1\">Kolonie (3|1)</a>\r3::3\r4::Einheiten::0::0::0::165::0::0::0::0::0::0::0\r4::Verluste::0::0::0::0::0::0::0::0::0::0::0\r4::'),
(88, '', 'judos', 3, 0, '2012-12-04 20:55:57', 'Kolonie greift Neues Dorf an', '1::Angreifer::<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=2\">Neues Dorf (3|2)</a>\r3::3\r4::Einheiten::0::0::0::165::0::0::0::0::0::0::0\r4::Verluste::0::0::0::0::0::0::0::0::0::0::0\r4::'),
(89, '', 'bla', 3, 1, '2012-12-04 20:55:57', 'Kolonie greift Neues Dorf an', '1::Angreifer::<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=2\">Neues Dorf (3|2)</a>\r3::3\r4::Einheiten::0::0::0::165::0::0::0::0::0::0::0\r4::Verluste::0::0::0::0::0::0::0::0::0::0::0\r4::'),
(90, '', 'judos', 3, 0, '2012-12-04 20:59:51', 'Kolonie greift Neues Dorf an', '1::Angreifer::<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=2\">Neues Dorf (3|2)</a>\r3::3\r4::Einheiten::0::0::0::165::0::0::0::0::0::0::0\r4::Verluste::0::0::0::0::0::0::0::0::0::0::0\r4::'),
(91, '', 'bla', 3, 1, '2012-12-04 20:59:51', 'Kolonie greift Neues Dorf an', '1::Angreifer::<a href=\"?page=spieler&name=judos\">judos</a> aus Dorf <a href=\"?page=karte-show&x=3&y=2\">Neues Dorf (3|2)</a>\r3::3\r4::Einheiten::0::0::0::165::0::0::0::0::0::0::0\r4::Verluste::0::0::0::0::0::0::0::0::0::0::0\r4::');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr1_oasen`
--

CREATE TABLE `tr1_oasen` (
  `typ` int(10) UNSIGNED NOT NULL,
  `bonus` tinytext NOT NULL,
  `tier_grenze` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `tr1_oasen`
--

INSERT INTO `tr1_oasen` (`typ`, `bonus`, `tier_grenze`) VALUES
(1, '25:0:0:0', '0:0:0:0:29:24:19:0:0:0'),
(2, '25:0:0:0', '0:0:0:0:23:18:23:0:0:0'),
(3, '25:0:0:25', '0:0:0:0:50:31:38:0:13:0'),
(4, '0:25:0:0', '49:39:59:0:20:0:0:0:0:0'),
(5, '0:25:0:0', '78:58:16:0:35:0:0:0:0:0'),
(6, '0:25:0:25', '68:49:0:0:29:0:0:10:29:0'),
(7, '0:0:25:0', '51:21:36:41:0:5:0:0:0:0'),
(8, '0:0:25:0', '93:46:0:46:0:0:0:0:0:0'),
(9, '0:0:25:25', '206:124:0:81:0:0:0:0:0:0'),
(10, '0:0:0:25', '14:0:19:0:0:0:12:10:0:0'),
(11, '0:0:0:25', '29:0:26:0:0:0:13:0:11:0'),
(12, '0:0:0:50', '33:0:30:0:0:0:0:13:8:13');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr1_others`
--

CREATE TABLE `tr1_others` (
  `keyid` int(10) UNSIGNED NOT NULL,
  `x` tinyint(4) NOT NULL DEFAULT 0,
  `y` tinyint(4) NOT NULL DEFAULT 0,
  `user` tinyint(4) NOT NULL DEFAULT 0,
  `typ` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1=kaserne,2=stall,3=werkstatt,4=residenz/palast,5=akademie,6=waffen,7=rüstungen,8=rathaus,9=geb abreissen,10=gebbauen,11=fallen,12=hero,13=held wiederbeleben,14=grossekaserne,15=grosserstall',
  `id` tinyint(4) NOT NULL DEFAULT 0,
  `zeit` datetime NOT NULL DEFAULT current_timestamp(),
  `anzahl` smallint(6) NOT NULL DEFAULT 0,
  `dauer` mediumint(9) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr1_todo`
--

CREATE TABLE `tr1_todo` (
  `keyid` int(10) UNSIGNED NOT NULL,
  `text` text NOT NULL,
  `status` text NOT NULL,
  `erfasst` datetime NOT NULL,
  `fertig` datetime NOT NULL,
  `implemented_in` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `tr1_todo`
--

INSERT INTO `tr1_todo` (`keyid`, `text`, `status`, `erfasst`, `fertig`, `implemented_in`) VALUES
(1, 'Projekt neu gestartet', 'finished', '2012-10-15 00:39:23', '2012-10-15 00:39:30', 'v0.3'),
(2, 'Alle angezeigten Fehler beheben f', 'finished', '2012-10-15 00:41:33', '2012-10-15 00:42:58', 'v0.3'),
(4, 'Dorf Umgebung ohne Fehler', 'finished', '2012-10-15 00:43:28', '2012-10-15 00:54:21', 'v0.3'),
(5, 'Stadt ohne Fehler', 'finished', '2012-10-15 00:43:33', '2012-10-15 00:55:44', 'v0.3'),
(7, 'Statistik funktioniert', 'finished', '2012-10-15 00:43:47', '2012-10-15 00:57:29', 'v0.3'),
(8, 'Nachrichten funktionieren', 'finished', '2012-10-15 00:43:53', '2012-10-16 16:34:52', 'v0.3'),
(9, 'Berichte funktionieren', 'finished', '2012-10-15 00:43:59', '2012-10-18 15:40:50', 'v0.3'),
(10, 'Karte funktioniert', 'finished', '2012-10-15 00:44:48', '2012-10-15 01:44:25', 'v0.3'),
(11, 'Doku template', 'finished', '2012-10-15 00:45:41', '2012-10-19 14:32:27', 'v0.32'),
(12, 'Doku views schreiben', 'finished', '2012-10-15 00:45:48', '2012-10-25 14:53:38', 'v0.32'),
(14, 'ally angriffe', 'finished', '2012-10-16 23:35:46', '2012-12-04 00:39:07', 'v0.33'),
(15, 'allianz diplomatie', '', '2012-12-04 21:07:06', '0000-00-00 00:00:00', ''),
(17, 'meiste ally funktionen', 'finished', '2012-10-17 00:33:48', '2012-10-17 00:33:49', 'v0.3'),
(18, 'forum chat automatisch aktualisieren', '', '2012-12-04 21:07:09', '0000-00-00 00:00:00', ''),
(19, 'dorf ', 'finished', '2012-10-17 01:53:50', '2012-10-30 19:11:10', 'v0.32'),
(20, 'marktplatz funktioniert', 'finished', '2012-10-18 00:36:06', '2012-10-18 00:36:08', 'v0.3'),
(21, 'stadtmauer klickbereich verbessern', 'finished', '2012-10-18 01:10:14', '2012-10-25 14:31:29', 'v0.32'),
(22, 'automatically import new db and make backup if you change version nr in db', 'finished', '2012-10-19 03:17:56', '2012-10-19 03:17:59', 'v0.32'),
(23, 'javascript keyshortcuts', '', '2012-12-04 21:07:10', '0000-00-00 00:00:00', ''),
(24, 'rohstoffbilder falsch beim bauen', 'finished', '2012-10-23 17:31:33', '2012-10-23 17:59:14', 'v0.32'),
(25, 'rohstofffelder stufe 26 kein bild -> ge', 'finished', '2012-10-23 18:06:57', '2012-10-23 18:07:04', 'v0.32'),
(26, 'Speed faktor einführen', '', '2012-10-30 17:39:36', '0000-00-00 00:00:00', ''),
(28, 'Truppen essen auch unterwegs getreide', '', '2012-11-01 16:30:32', '0000-00-00 00:00:00', ''),
(29, 'Leere Dörfer sollen im Bericht mit user und Name erscheinen', 'finished', '2012-12-03 21:28:16', '2012-12-03 23:14:30', 'v0.33'),
(30, 'aktualisieren wenn ein gebäude fertig gebaut ist bleibt aus', 'finished', '2012-12-03 21:32:20', '2012-12-03 21:50:34', 'v0.33'),
(31, 'reset speed of theatus blitz to 19', '', '2012-12-03 22:46:55', '0000-00-00 00:00:00', ''),
(32, 'Umkehrende Truppen erscheinen mit falschem Absender', 'finished', '2012-12-03 23:15:18', '2012-12-04 20:37:55', 'beta v0.34'),
(33, 'online status von usern korrigiert', 'finished', '2012-12-03 23:28:34', '2012-12-03 23:28:36', 'v0.33'),
(34, 'rohstoffe erbeuten beim angreifen', 'finished', '2012-12-04 00:39:23', '2012-12-04 21:01:46', 'beta v0.34'),
(35, 'kampfberichte', 'finished', '2012-12-04 21:06:55', '2012-12-04 21:06:57', 'beta v0.34'),
(36, 'spähberichte', '', '2012-12-04 21:07:00', '0000-00-00 00:00:00', ''),
(37, 'angriffe auf oasen', '', '2013-11-26 16:27:07', '0000-00-00 00:00:00', ''),
(38, 'kampfsimulator', '', '2013-11-26 16:31:36', '0000-00-00 00:00:00', ''),
(39, 'diverse kleine Bugs behoben, DB aktualisiert', 'finished', '2013-11-26 16:36:24', '2013-11-26 16:36:26', 'beta v0.35');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr1_truppen`
--

CREATE TABLE `tr1_truppen` (
  `x` int(11) NOT NULL DEFAULT 0,
  `y` int(11) NOT NULL DEFAULT 0,
  `user` int(11) NOT NULL DEFAULT 0,
  `troops` tinytext NOT NULL,
  `gefangen` tinyint(1) NOT NULL,
  `ursprung_x` int(11) NOT NULL,
  `ursprung_y` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `tr1_truppen`
--

INSERT INTO `tr1_truppen` (`x`, `y`, `user`, `troops`, `gefangen`, `ursprung_x`, `ursprung_y`) VALUES
(1, 1, 0, '0:0:0:0:33.944302584599:29.243860810841:23.709062423153:0:0:0:0', 0, 1, 1),
(3, 4, 0, '17.026296523752:0:22.830393342547:0:0:0:14.944238197847:13.149408632919:0:0:0', 0, 3, 4),
(4, 3, 0, '0:0:0:0:57.241442174213:36.256231793126:43.641949527248:0:16.763169774444:0:0', 0, 4, 3),
(5, 2, 0, '20:0:29:0:0:0:17:13.642316458727:0:0:0', 0, 5, 2),
(5, 3, 0, '0:0:0:0:28.495741299708:22.94693230059:28.552417049698:0:0:0:0', 0, 5, 3),
(5, 4, 0, '0:0:0:0:35.125325836106:29.657584712167:24.079265202531:0:0:0:0', 0, 5, 4),
(5, 5, 0, '0:0:0:0:35.236980089244:29.680916994826:24.058111977934:0:0:0:0', 0, 5, 5),
(5, 6, 0, '35.141567962025:0:34:0:0:0:20:0:17:0:0', 0, 5, 6),
(5, 7, 0, '0:0:0:0:58.117956954602:37.445427798995:45.015350282273:0:17.179819905321:0:0', 0, 5, 7),
(6, 1, 0, '39.632474738335:0:36.370604923031:0:0:0:0:17.187895139038:13:17.18757726197:0', 0, 6, 1),
(6, 2, 0, '0:0:0:0:28.547201379811:22.955182918771:28.531734128048:0:0:0:0', 0, 6, 2),
(6, 3, 0, '22:0:27:0:0:0:20:17:0:0:0', 0, 6, 3),
(6, 4, 0, '0:0:0:0:57.963456933073:37.368954347347:45.130395193614:0:17.191523597833:0:0', 0, 6, 4),
(6, 5, 0, '19:0:26:0:0:0:20:13.6521142345:0:0:0', 0, 6, 5),
(6, 6, 0, '0:0:0:0:57.712171372244:37.384568742943:45.11213674506:0:17.207287486664:0:0', 0, 6, 6),
(7, 2, 0, '87.801711738042:66.580326484903:20.620279642013:0:41.822501284768:0:0:0:0:0:0', 0, 7, 2),
(7, 3, 0, '77.303166181995:56.990483443901:0:0:35.190375701902:0:0:13.651567904097:35.249227667012:0:0', 0, 7, 3),
(7, 4, 0, '0:0:0:0:57.91117188816:37.443990298405:45.131559012669:0:17.174742203172:0:0', 0, 7, 4),
(7, 7, 0, '59.141579298638:26.245043376621:42.878538905438:48.283347820404:0:7.532255510141:0:0:0:0:0', 0, 7, 7);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr1_truppen_move`
--

CREATE TABLE `tr1_truppen_move` (
  `keyid` int(10) UNSIGNED NOT NULL,
  `user` tinyint(4) NOT NULL DEFAULT 0,
  `start_x` tinyint(4) NOT NULL DEFAULT 0,
  `start_y` tinyint(4) NOT NULL DEFAULT 0,
  `ziel_x` tinyint(4) NOT NULL DEFAULT 0,
  `ziel_y` tinyint(4) NOT NULL DEFAULT 0,
  `start_zeit` datetime NOT NULL DEFAULT current_timestamp(),
  `ziel_zeit` datetime NOT NULL DEFAULT current_timestamp(),
  `aktion` tinyint(4) NOT NULL DEFAULT 0,
  `truppen` tinytext NOT NULL,
  `ress` tinytext NOT NULL,
  `msg` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr1_truppen_typen`
--

CREATE TABLE `tr1_truppen_typen` (
  `id` tinyint(4) NOT NULL DEFAULT 0,
  `name` varchar(24) NOT NULL DEFAULT '',
  `mehrzahl` varchar(24) NOT NULL DEFAULT '',
  `besch` text NOT NULL,
  `volk` tinyint(4) NOT NULL DEFAULT 0,
  `typ` tinyint(4) NOT NULL DEFAULT 0,
  `spio` tinyint(4) NOT NULL DEFAULT 0,
  `baukz` tinytext NOT NULL,
  `versorgung` float NOT NULL DEFAULT 0,
  `speed` smallint(6) NOT NULL,
  `tragen` smallint(6) NOT NULL DEFAULT 0,
  `werte` tinytext NOT NULL,
  `needs` tinytext NOT NULL,
  `reskost` tinytext NOT NULL,
  `forsch` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `tr1_truppen_typen`
--

INSERT INTO `tr1_truppen_typen` (`id`, `name`, `mehrzahl`, `besch`, `volk`, `typ`, `spio`, `baukz`, `versorgung`, `speed`, `tragen`, `werte`, `needs`, `reskost`, `forsch`) VALUES
(1, 'Legionär', 'Legionäre', 'Der Legionär ist eine einfache und universell einsetzbare Fußtruppe des römischen Imperiums. Vielseitig ausgebildet, eignet er sich sowohl zum Angriff, als auch sehr gut zur Verteidigung.', 1, 1, 0, '120:100:180:40:2000', 0.25, 6, 40, '40:35:50', '1:19:1', '', '940:800:1460:440:0.8:7800'),
(2, 'Prätorianer', '', 'Die Prätorianer wurden ursprünglich als Leibgarde des römischen Senats ausgebildet. Durch ihre langwierige Ausbildung in defensiven Kampftechniken hat sich diese Truppe einen legendären Ruf erarbeitet.', 1, 1, 0, '100:130:160:70:2200', 0.3, 5, 20, '30:65:35', '2:22:1:13:1', '700:620:1460:580:8400', '800:1010:1320:650:0.8:8400'),
(3, 'Imperianer', '', 'Der Imperianer bildet die ultimative Angriffstruppe des römischen Heeres. Schnell und angriffsstark ist er der Schrecken der Verteidiger.', 1, 1, 0, '150:160:210:80:2400', 0.4, 7, 50, '70:40:25', '2:22:5:12:1', '1000:740:1880:640:9000', '1150:1220:1670:720:0.8:8400'),
(4, 'Equites Legati', '', 'Die Equites Legati sind die Aufklärer der römischen Truppen. Sie sind äußerst schnell. Listig erkunden sie die gegnerischen Einheiten und Ressourcen oder die Verteidigungsanlagen.', 1, 2, 1, '140:160:20:40:1700', 0.7, 16, 0, '0:20:10', '2:20:1:22:5', '940:740:360:400:6900', '540:610:170:220:0.8:6300'),
(5, 'Equites Imperiatoris', '', 'Dies ist die Standard-Kavallerie der Römer. Nicht ganz so schnell, aber gut bewaffnet und gerüstet ist sie der Schrecken aller unvorbereiteten Feinde. Allerdings sollte man immer im Auge haben, dass die Versorgung von Pferd und Reiter nicht umsonst ist!', 1, 2, 0, '550:440:320:100:3300', 1.3, 14, 100, '120:65:50', '2:20:5:22:5', '3400:1860:2760:760:11700', '1320:1060:815:285:0.8:11700'),
(6, 'Equites Caesaris', '', 'Dies ist die schwere Reiterei. Sehr schwer gerüstet und auch gut bewaffnet ist sie langsamer als der Imperatoris und kann auch weniger Ressourcen tragen. Auch im Unterhalt sind sie teurer - Leistung hat seinen Preis.', 1, 2, 0, '550:640:800:180:4400', 1.6, 10, 70, '180:80:105', '2:20:10:22:5', '3400:2660:6600:1240:15000', '990:1145:1450:355:0.8:15000'),
(7, 'Rammbock', 'Rammböcke', 'Die Ramme ist eine schwere Waffe zur Unterstützung der Infantrie und Kavallerie. Ihre Aufgabe ist es, den feindlichen Schutzwall bzw. die Stadtmauer zu durchbrechen und den Angriff der eigenen Truppen zu erleichtern.', 1, 3, 0, '900:360:500:70:4600', 3, 4, 0, '60:30:75', '2:22:10:21:1', '5500:1540:4200:580:15600', '2135:875:1235:215:0.8:15600'),
(8, 'Feuerkatapult', 'Feuerkatapulte', 'Das Feuerkatapult ist eine exzellente Distanzwaffe zur Zerstörung von Gebäuden und Ressourcenfelder. Allerdings ist es selbst nahezu schutzlos! Daher muss immer Geleitschutz mitgeschickt werden.\r\n\r\nJe weiter der Vesammlungsplatz ausgebaut ist - und die Männer am Katapult damit ausgebildet sind - desto grösser ist die Zielgenauigkeit. Beim Versammlungsplatz Stufe 10 kann jedes Gebäude außer dem Versteck anvisiert werden.', 1, 3, 0, '950:1350:600:90:9000', 5, 3, 0, '75:60:10', '2:21:10:22:15', '5800:5500:5000:700:28800', '1125:1590:735:130:0.8:28800'),
(9, 'Senator', 'Senatoren', 'Der Senator ist ein ausgewählter Anführer des Volkes. Er ist ein guter Redner und weiß zu überzeugen. So gelingt es ihm, die Bewohner eines feindlichen Dorfes zu überreden, sich deinem Reich anzuschließen.\r\n\r\nJe häufiger er vor den Mauern der Stadt zu den Bewohnern spricht, desto geringer ist die Zustimmung für den gegnerischen Herrscher, bis das Dorf zu deinem Reich gehört.', 1, 4, 0, '30750:27200:45000:37500:90700', 1, 4, 0, '50:40:30', '2:16:10:22:20', '15880:13800:36400:22660:24475', '0'),
(10, 'Siedler', '', 'Siedler sind tapfere und wagemutige Bürger deiner Stadt, die sich nach langer Ausbildung auf den Weg machen, in der Fremde ein neues Dorf zu deinen Ehren zu gründen.\r\n\r\nDa die Reise und die Gründung eines Dorfes beschwerlich ist, tun sich immer drei Siedler zusammen, um ein Dorf zu gründen. Gleichzeitig benötigen sie einen Grundstock von je 750 Einheiten pro Ressource.', 1, 4, 0, '5800:5300:7200:5500:26900', 1, 5, 3000, '0:80:80', '-1', '0', '0'),
(11, 'Keulenschwinger', '', 'Der Keulenschwinger ist die preiswerteste Einheit in ganz Travian. Schnell auszubilden, ist er aber nur von mittlerer Angriffskraft und seine Rüstung ist nicht die stärkste. Gerade der Kavallerie kann er in der Verteidigung nichts entgegensetzen und wird förmlich niedergeritten.', 2, 1, 0, '95:75:40:40:900', 0.15, 7, 60, '40:20:5', '1:19:1', '0', '765:625:480:440:0.8:4500'),
(12, 'Speerkämpfer', '', 'Gerade in der Verteidigung ist der Speerkämpfer eine gute Waffe. Durch die große Reichweite seiner Speere ist er besonders gut gegen Kavallerie einzusetzen.', 2, 1, 0, '145:70:85:40:1400', 0.3, 7, 40, '10:35:60', '1:22:1', '970:380:880:400:6000', '1115:590:795:440:0.8:5700'),
(13, 'Axtkämpfer', '', 'Dies ist die stärkste Infantrie der Germanen. Mit guter stärke im Angriff und mittleren Werten in der Verteidigung ist sie allerdings etwas langsamer und etwas teurer als die anderen Einheiten.', 2, 1, 0, '130:120:170:70:1500', 0.5, 6, 50, '60:30:30', '2:22:3:12:1', '880:580:1560:580:6300', '1010:940:1390:650:6300'),
(14, 'Kundschafter', '', 'Der Kundschafter ist den germanischen Truppen oft weit voraus und erkundet die Dörfer des Feindes. Er ist zu Fuß unterwegs und deshalb nicht ganz so schnell. Vorsichtig späht er die gegnerischen Einheiten und Ressourcen oder die Verteidigungsanlagen aus.', 2, 1, 1, '160:100:50:50:1400', 0.4, 9, 0, '0:10:5', '2:15:5:22:1', '1060:500:600:460:6000', '1220:800:550:510:6000'),
(15, 'Paladin', 'Paladine', 'Mit starker Rüstung ist der Paladin eine gute Waffe in der Verteidigung. Gerade Infantrie vermag seine Schild kaum zu durchdringen.\r\n\r\nDafür ist seine Stärke beim Angriff eher klein und auch die Geschwindigkeit ist für Kavallerie durch die Rüstung nur Durchschnitt. Die Ausbildung dauert relativ lange und ist kostspielig.', 2, 2, 0, '370:270:290:75:3000', 1.2, 10, 110, '55:100:40', '2:22:5:20:3', '2320:1180:2520:610:10800', '1345:995:1115:345:0.8:10800'),
(16, 'Teutonen Reiter', '', 'Der Teutonen Reiter ist ein mächtiger Recke, der seine Gegner beim Angriff in Angst und Schrecken versetzt. In der Verteidigung ist er gerade gegen die feindliche Kavallerie ein guter Kämpfer. Die Kosten für Ausbildung und Unterhalt sind allerdings hoch.', 2, 2, 0, '450:515:480:80:3700', 1.7, 9, 80, '150:50:75', '2:22:15:20:10', '2800:2160:4040:640:12900', '1085:1235:1185:240:0.8:12900\r\n'),
(17, 'Ramme', 'Rammen', 'Die Ramme ist eine schwere Waffe zur Unterstützung der Infantrie und Kavallerie. Ihre Aufgabe ist es, den feindlichen Schutzwall bzw. die Stadtmauer zu durchbrechen und den Angriff der eigenen Truppen zu erleichtern. Selbst ohne Schutz, benötigt sie Geleitschutz um effektiv eingesetzt werden zu können.', 2, 3, 0, '1000:300:350:70:4200', 2.5, 4, 0, '65:30:80', '2:22:10:21:1', '6100:1300:3000:580:14400', '2365:735:885:215:0.8:14400\r\n'),
(18, 'Katapult', 'Katapulte', 'Das Katapult ist eine exzellente Distanzwaffe zur Zerstörung von Gebäuden und Ressourcenfelder. Allerdings ist es selbst nahezu schutzlos! Daher muss immer Geleitschutz mitgeschickt werden.\r\n\r\nJe weiter der Vesammlungsplatz ausgebaut ist - und die Männer am Katapult damit ausgebildet sind - desto grösser ist die Zielgenauigkeit. Beim Versammlungsplatz Stufe 10 kann jedes Gebäude außer dem Versteck anvisiert werden.', 2, 3, 0, '900:1200:600:60:9000', 4.5, 3, 0, '50:60:10', '2:21:10:22:15', '5500:4900:5000:520:28800', '1065:1415:735:95:28800'),
(19, 'Stammesführer', '', 'Aus ihrer Mitte wählen die Germanen den Stammesführer. Um gewählt zu werden, bedarf es nicht nur Tapferkeit und Kriegskunst, auch Redekunst ist notwendig. Diese verwendet der Stammesführer dann dafür, die Einwohner fremder Dörfer zu überzeugen den Stamm zu wechseln.\r\n\r\nJe öfter er vor den Mauern der Stadt zu den Bewohnern spricht, desto geringer ist die Zustimmung für den anderen Herrscher, bis das Dorf zu deinem Reich gehört.', 2, 4, 0, '35500:26600:25000:27200:70500', 1, 4, 0, '40:60:40', '2:16:5:22:20', '18250:13500:20400:16480:19425', '0'),
(20, 'Siedler', '', 'Siedler sind tapfere und wagemutige Bürger deiner Stadt die sich nach langer Ausbildung auf den Weg machen, in der Fremde ein neues Dorf zu deinen Ehren zu gründen.\r\n\r\nDa die Reise und die Gründung eines Dorfes beschwerlich ist, tun sich immer drei Siedler zusammen, um ein Dorf zu gründen. Gleichzeitig benötigen sie einen Grundstock von je 750 Einheiten pro Ressource.', 2, 4, 0, '7200:5500:5800:6500:31000', 1, 5, 3000, '10:80:80', '-1', '0', '0'),
(21, 'Phalanx', 'Phalanxe', 'Als einfache Fußtruppe ist die Phalanx relativ preiswert und schnell zu produzieren.\r\n\r\nIhr Angriffswert ist allerdings sehr klein. Erst in der Verteidigung zeigen sie ihre Stärken, sowohl gegen Infantrie als auch gegen Kavallerie.', 3, 1, 0, '100:130:55:30:1300', 0.25, 7, 30, '15:40:50', '1:19:1', '0', '800:1010:585:370:0.8:5700'),
(22, 'Schwertkämpfer', '', 'Die Schwertkämpfer sind teurer als die Phalanx, aber sie stellen bereits eine veritable Angriffstruppe dar.\r\n\r\nIn der Defensive sind sie allerdings relativ schwach, besonders gegen Kavallerie.', 3, 1, 0, '140:150:185:60:1800', 0.3, 6, 45, '65:35:20', '2:22:1:12:1', '940:700:1680:520:7200', '1080:1150:1495:580:0.8:7200'),
(23, 'Späher', '', 'Der Späher ist die Aufklärungstruppe der Gallier. Sie sind äußerst schnell. Vorsichtig nähern sie sich schnell und gedeckt und erkunden die gegnerischen Einheiten und Ressourcen oder die Verteidigungsanlagen.', 3, 2, 1, '170:150:20:40:1700', 1.3, 17, 0, '0:20:10', '2:22:5:20:1', '1120:700:360:400:3300', '645:575:170:220:0.8:6900'),
(24, 'Theutates Blitz', 'Theutates Blitze', 'Die Blitze sind eine äußerst schnelle und sehr schlagkräftige Angriffseinheit, die zudem auch noch eine große Menge an Rohstoffen tragen können.\r\n\r\nIn der Verteidigung sind ihre Leistungen dagegen eher mittelmässig.', 3, 2, 0, '350:450:230:60:3100', 1.5, 190, 75, '90:25:40', '2:22:5:20:3', '2200:1900:2040:520:11100', '1275:1625:905:290:0.8:11100'),
(25, 'Druidenreiter', '', 'Diese mittlere Kavallerieeinheit ist defensiv ausgerichtet. Der Schwerpunkt der Druidenreiter liegt dabei eindeutig in der Verteidigung gegen Infantrie. Sie ist aber in Bau und Unterhalt schon relativ teuer.', 3, 2, 0, '360:330:280:120:3900', 1.4, 16, 35, '45:115:55', '2:22:5:20:5', '2260:1420:2440:880:11400', '1310:1205:1080:500:0.8:11400'),
(26, 'Haeduaner', '', 'Die Haeduaner sind die ultimative Waffe im Angriff und in der Verteidigung gegen Kavallerie. Kaum jemand kann ihnen in diesen Punkten das Wasser reichen.\r\n\r\nAllerdings ist die Ausbildung und Ausrüstung der Haeduaner auch entsprechend teuer und mit 3 Einheiten Getreide/Stunde muss man sich schon genau überlegen, ob man bereit ist, diesen Preis zu bezahlen.', 3, 2, 0, '500:620:675:170:3900', 1.8, 13, 65, '140:50:165', '2:22:15:20:10', '3100:2580:5600:1180:13500', '1200:1480:1640:450:0.8:13500'),
(27, 'Rammholz', 'Rammhölzer', 'Das Rammholz ist eine schwere Waffe zur Unterstützung der Infantrie und Kavallerie. Seine Aufgabe ist es, den feindlichen Schutzwall bzw. die Stadtmauer zu durchbrechen und den Angriff der eigenen Truppen zu erleichtern.\r\n\r\nSelbst ohne Schutz, benötigt sie Geleitschutz um effektiv eingesetzt werden zu können.', 3, 3, 0, '950:555:330:75:5000', 2.8, 4, 0, '50:30:105', '2:22:10:21:1', '5800:2320:2840:610:16800', '2250:1330:835:230:0.8:16800'),
(28, 'Kriegskatapult', 'Kriegskatapulte', 'Das Katapult ist eine exzellente Distanzwaffe zur Zerstörung von Gebäuden und Ressourcenfelder. Allerdings ist es selbst nahezu schutzlos! Daher muss immer Geleitschutz mitgeschickt werden.\r\n\r\nJe weiter der Versammlungsplatz ausgebaut ist - und die Männer am Katapult damit ausgebildet sind - desto grösser ist die Zielgenauigkeit. Beim Versammlungsplatz Stufe 10 kann jedes Gebäude außer dem Versteck anvisiert werden.', 3, 3, 0, '960:1450:630:90:9000', 6, 3, 0, '70:45:10', '2:22:15:21:10', '5860:5900:5240:700:28800', '1135:1710:770:130:0.8:28800'),
(29, 'Häuptling', 'Häuptlinge', 'Jeder Stamm hat einen altehrwürdigen und erfahrenen Kämpfer dem es gelingt, die Bevölkerung des gegnerischen Dorfes zu überzeugen, dem Stamm beizutreten.\r\n\r\nJe häufiger er vor den Mauern der Stadt zu den Bewohnern spricht, desto geringer ist die Zustimmung für den gegnerischen Herrscher, bis das Dorf zu deinem Stamm gehört.', 3, 4, 0, '30750:45400:31000:37500:90700', 1, 5, 0, '40:50:50', '2:22:20:16:10\r\n', '15880:22900:25200:22600:24475', '0'),
(30, 'Siedler', '', 'Siedler sind tapfere und wagemutige Bürger deiner Stadt die sich nach langer Ausbildung auf den Weg machen, in der Fremde ein neues Dorf zu deinen Ehren zu gründen.\r\n\r\nDa die Reise und die Gründung eines Dorfes beschwerlich ist, tun sich immer drei Siedler zusammen, um ein Dorf zu gründen. Gleichzeitig benötigen sie einen Grundstock von je 750 Einheiten pro Ressource.', 3, 4, 0, '5500:7000:5300:4900:26900', 1, 5, 3000, '0:80:80', '-1', '0', '0'),
(31, 'Ratte', 'Ratten', '', 4, 1, 0, '', 1, 20, 0, '10:25:20', '', '', ''),
(32, 'Spinne', 'Spinnen', '', 4, 1, 0, '', 1, 20, 0, '20:35:40', '', '', ''),
(33, 'Schlange', 'Schlangen', '', 4, 1, 0, '', 1, 20, 0, '60:40:60', '', '', ''),
(34, 'Fledermaus', 'Fledermäuse', '', 4, 2, 1, '', 1, 40, 0, '80:66:50', '', '', ''),
(35, 'Wildschwein', 'Wildschweine', '', 4, 2, 0, '', 2, 20, 5, '50:70:33', '', '', ''),
(36, 'Wolf', 'Wölfe', '', 4, 2, 0, '', 2, 20, 20, '100:80:70', '', '', ''),
(37, 'Bär', 'Bären', '', 4, 3, 0, '', 3, 20, 60, '250:140:200', '', '', ''),
(38, 'Krokodil', 'Krokodile', '', 4, 3, 0, '', 3, 20, 60, '450:380:240', '', '', ''),
(39, 'Tiger', '', '', 4, 2, 1, '', 3, 20, 50, '200:170:250', '', '', ''),
(40, 'Elefant', 'Elefanten', '', 4, 3, 0, '', 5, 20, 150, '600:440:520', '', '', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr1_user`
--

CREATE TABLE `tr1_user` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(16) NOT NULL DEFAULT '',
  `pw` tinytext NOT NULL,
  `email` varchar(64) NOT NULL DEFAULT '',
  `einwohner` int(11) NOT NULL DEFAULT 0,
  `kps` float NOT NULL DEFAULT 0,
  `volk` tinyint(4) NOT NULL DEFAULT 0,
  `besch` tinytext NOT NULL,
  `research` tinytext NOT NULL COMMENT 'welche Einheiten wurden schon erforscht',
  `weapons` tinytext NOT NULL,
  `arms` tinytext NOT NULL,
  `ally` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `ally_rang` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `konfig` tinytext NOT NULL,
  `last_update` datetime NOT NULL DEFAULT current_timestamp(),
  `last_online` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `tr1_user`
--

INSERT INTO `tr1_user` (`id`, `name`, `pw`, `email`, `einwohner`, `kps`, `volk`, `besch`, `research`, `weapons`, `arms`, `ally`, `ally_rang`, `konfig`, `last_update`, `last_online`) VALUES
(1, 'judos', '81dc9bdb52d04dc20036dbd8313ed055', 'judos@gmx.ch', 744, 5128980, 3, 'Entwickler und oWn€r', '1:1:0:1:0:0:1:0:0:0', '1:0:0:0:0:0:0:0:0:0', '1:1:0:0:0:0:0:0:0:0', 2, 12, '1:1:1:1:1:1:1:1:1:1:1:1:1', '2020-06-01 14:20:32', '2020-06-01 14:39:24'),
(4, 'test', '098f6bcd4621d373', 'test@gmx.ch', 2, 2779.63, 2, '', '1:0:0:0:0:0:0:0:0:0', '0:0:0:0:0:0:0:0:0:0', '0:0:0:0:0:0:0:0:0:0', 0, 0, '1:1:1:1:1:1:1:1:1:1:1:1:1', '2020-06-01 14:20:32', '0000-00-00 00:00:00'),
(5, 'ropeko', '6137708dfbddf039', 'ropeko@yahoo.de', 14, 19442.3, 1, '', '1:0:0:0:0:0:0:0:0:0', '0:0:0:0:0:0:0:0:0:0', '0:0:0:0:0:0:0:0:0:0', 2, 0, ':0:1:1:1:1:1:1:1:1:1:1:1', '2020-06-01 14:20:32', '0000-00-00 00:00:00'),
(6, 'bla', '128ecf542a35ac52', 'bla', 2, 2777.83, 3, '', '1:0:0:0:0:0:0:0:0:0', '0:0:0:0:0:0:0:0:0:0', '0:0:0:0:0:0:0:0:0:0', 0, 0, '1:1:1:1:1:1:1:1:1:1:1:1:1', '2020-06-01 14:20:32', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tr_rounds`
--

CREATE TABLE `tr_rounds` (
  `name` varchar(16) NOT NULL DEFAULT '',
  `info` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `tr_rounds`
--

INSERT INTO `tr_rounds` (`name`, `info`) VALUES
('1', 'Die Test Runde von Traviatus.\r\nVersion 1');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `tr1_ally`
--
ALTER TABLE `tr1_ally`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `tr1_ally_chat`
--
ALTER TABLE `tr1_ally_chat`
  ADD PRIMARY KEY (`ally_id`,`user_id`,`zeit`);

--
-- Indizes für die Tabelle `tr1_ally_einladungen`
--
ALTER TABLE `tr1_ally_einladungen`
  ADD PRIMARY KEY (`ally_id`,`user_id`);

--
-- Indizes für die Tabelle `tr1_ally_kampfe`
--
ALTER TABLE `tr1_ally_kampfe`
  ADD PRIMARY KEY (`keyid`);

--
-- Indizes für die Tabelle `tr1_ally_news`
--
ALTER TABLE `tr1_ally_news`
  ADD PRIMARY KEY (`ally_id`,`datum`);

--
-- Indizes für die Tabelle `tr1_ally_range`
--
ALTER TABLE `tr1_ally_range`
  ADD PRIMARY KEY (`rang_id`);

--
-- Indizes für die Tabelle `tr1_angebote`
--
ALTER TABLE `tr1_angebote`
  ADD PRIMARY KEY (`keyid`);

--
-- Indizes für die Tabelle `tr1_bugs`
--
ALTER TABLE `tr1_bugs`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `tr1_diverses`
--
ALTER TABLE `tr1_diverses`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `tr1_dorfer`
--
ALTER TABLE `tr1_dorfer`
  ADD PRIMARY KEY (`x`,`y`);

--
-- Indizes für die Tabelle `tr1_gebeude`
--
ALTER TABLE `tr1_gebeude`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `tr1_handler`
--
ALTER TABLE `tr1_handler`
  ADD PRIMARY KEY (`keyid`);

--
-- Indizes für die Tabelle `tr1_hero`
--
ALTER TABLE `tr1_hero`
  ADD PRIMARY KEY (`keyid`);

--
-- Indizes für die Tabelle `tr1_lander`
--
ALTER TABLE `tr1_lander`
  ADD PRIMARY KEY (`x`,`y`);

--
-- Indizes für die Tabelle `tr1_land_typen`
--
ALTER TABLE `tr1_land_typen`
  ADD PRIMARY KEY (`typ`);

--
-- Indizes für die Tabelle `tr1_msg`
--
ALTER TABLE `tr1_msg`
  ADD PRIMARY KEY (`keyid`);

--
-- Indizes für die Tabelle `tr1_oasen`
--
ALTER TABLE `tr1_oasen`
  ADD PRIMARY KEY (`typ`);

--
-- Indizes für die Tabelle `tr1_others`
--
ALTER TABLE `tr1_others`
  ADD PRIMARY KEY (`keyid`);

--
-- Indizes für die Tabelle `tr1_todo`
--
ALTER TABLE `tr1_todo`
  ADD PRIMARY KEY (`keyid`);

--
-- Indizes für die Tabelle `tr1_truppen`
--
ALTER TABLE `tr1_truppen`
  ADD PRIMARY KEY (`x`,`y`,`user`);

--
-- Indizes für die Tabelle `tr1_truppen_move`
--
ALTER TABLE `tr1_truppen_move`
  ADD PRIMARY KEY (`keyid`);

--
-- Indizes für die Tabelle `tr1_truppen_typen`
--
ALTER TABLE `tr1_truppen_typen`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `tr1_user`
--
ALTER TABLE `tr1_user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `tr1_ally`
--
ALTER TABLE `tr1_ally`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `tr1_ally_kampfe`
--
ALTER TABLE `tr1_ally_kampfe`
  MODIFY `keyid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT für Tabelle `tr1_ally_range`
--
ALTER TABLE `tr1_ally_range`
  MODIFY `rang_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT für Tabelle `tr1_angebote`
--
ALTER TABLE `tr1_angebote`
  MODIFY `keyid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT für Tabelle `tr1_bugs`
--
ALTER TABLE `tr1_bugs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `tr1_handler`
--
ALTER TABLE `tr1_handler`
  MODIFY `keyid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `tr1_hero`
--
ALTER TABLE `tr1_hero`
  MODIFY `keyid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `tr1_msg`
--
ALTER TABLE `tr1_msg`
  MODIFY `keyid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT für Tabelle `tr1_others`
--
ALTER TABLE `tr1_others`
  MODIFY `keyid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT für Tabelle `tr1_todo`
--
ALTER TABLE `tr1_todo`
  MODIFY `keyid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT für Tabelle `tr1_truppen_move`
--
ALTER TABLE `tr1_truppen_move`
  MODIFY `keyid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `tr1_user`
--
ALTER TABLE `tr1_user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
