-- phpMyAdmin SQL Dump
-- version 2.11.1.1
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Erstellungszeit: 11. Juli 2012 um 16:02
-- Server Version: 5.1.36
-- PHP-Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Datenbank: `part-db`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `parentnode` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `parentnode` (`parentnode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `categories`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `datasheets`
--

DROP TABLE IF EXISTS `datasheets`;
CREATE TABLE IF NOT EXISTS `datasheets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id` int(11) NOT NULL DEFAULT '0',
  `datasheeturl` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `part_id` (`part_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `datasheets`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `devices`
--

DROP TABLE IF EXISTS `devices`;
CREATE TABLE IF NOT EXISTS `devices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `parentnode` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `devices`
--

INSERT INTO `devices` (`id`, `name`, `parentnode`) VALUES
(1, 'IC', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `footprints`
--

DROP TABLE IF EXISTS `footprints`;
CREATE TABLE IF NOT EXISTS `footprints` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `filename` mediumtext COLLATE utf8_unicode_ci,
  `parentnode` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `footprints`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `internal`
--

DROP TABLE IF EXISTS `internal`;
CREATE TABLE IF NOT EXISTS `internal` (
  `keyName` char(30) CHARACTER SET ascii NOT NULL,
  `keyValue` char(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  UNIQUE KEY `keyName` (`keyName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `internal`
--

INSERT INTO `internal` (`keyName`, `keyValue`) VALUES
('dbVersion', '12'),
('dbAutoUpdate', '1');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `parts`
--

DROP TABLE IF EXISTS `parts`;
CREATE TABLE IF NOT EXISTS `parts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_category` int(11) NOT NULL DEFAULT '0',
  `name` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8_unicode_ci,
  `instock` int(11) NOT NULL DEFAULT '0',
  `mininstock` int(11) NOT NULL DEFAULT '0',
  `comment` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `obsolete` tinyint(1) NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL,
  `id_footprint` int(11) NOT NULL DEFAULT '0',
  `id_storeloc` int(11) NOT NULL DEFAULT '0',
  `id_supplier` int(11) NOT NULL DEFAULT '0',
  `supplierpartnr` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_storeloc` (`id_storeloc`),
  KEY `id_category` (`id_category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `parts`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `part_device`
--

DROP TABLE IF EXISTS `part_device`;
CREATE TABLE IF NOT EXISTS `part_device` (
  `id_part` int(11) NOT NULL DEFAULT '0',
  `id_device` int(11) NOT NULL DEFAULT '0',
  `quantity` int(11) NOT NULL DEFAULT '0',
  `mountname` mediumtext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `part_device`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pending_orders`
--

DROP TABLE IF EXISTS `pending_orders`;
CREATE TABLE IF NOT EXISTS `pending_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id` int(11) NOT NULL DEFAULT '0',
  `quantity` int(11) NOT NULL DEFAULT '0',
  `t` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `part_id` (`part_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `pending_orders`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pictures`
--

DROP TABLE IF EXISTS `pictures`;
CREATE TABLE IF NOT EXISTS `pictures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id` int(11) NOT NULL DEFAULT '0',
  `pict_fname` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `pict_width` int(11) NOT NULL DEFAULT '0',
  `pict_height` int(11) NOT NULL DEFAULT '0',
  `pict_type` enum('P','T') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'P',
  `tn_obsolete` smallint(6) NOT NULL DEFAULT '0',
  `tn_t` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tn_pictid` int(11) NOT NULL DEFAULT '0',
  `pict_masterpict` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pict_type` (`pict_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `pictures`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `preise`
--

DROP TABLE IF EXISTS `preise`;
CREATE TABLE IF NOT EXISTS `preise` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id` int(11) NOT NULL DEFAULT '0',
  `id_supplier` int(11) NOT NULL DEFAULT '0',
  `supplierpartnr` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `manual_input` tinyint(1) NOT NULL DEFAULT '0',
  `price` decimal(6,2) NOT NULL DEFAULT '0.00',
  `last_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `part_id` (`part_id`),
  KEY `ma` (`manual_input`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `preise`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `storeloc`
--

DROP TABLE IF EXISTS `storeloc`;
CREATE TABLE IF NOT EXISTS `storeloc` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `parentnode` int(11) NOT NULL DEFAULT '0',
  `is_full` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `storeloc`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `suppliers`
--

