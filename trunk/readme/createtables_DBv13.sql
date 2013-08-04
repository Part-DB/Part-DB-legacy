-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 04. Mai 2013 um 23:25
-- Server Version: 5.5.29
-- PHP-Version: 5.4.6-1ubuntu1.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `part-db`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `attachements`
--

CREATE TABLE IF NOT EXISTS `attachements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `class_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `element_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `filename` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `show_in_table` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `attachements_class_name_k` (`class_name`),
  KEY `attachements_element_id_k` (`element_id`),
  KEY `attachements_type_id_fk` (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `attachement_types`
--

CREATE TABLE IF NOT EXISTS `attachement_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attachement_types_parent_id_k` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `attachement_types`
--

INSERT INTO `attachement_types` (`id`, `name`, `parent_id`) VALUES
(1, 'Bilder', NULL),
(2, 'Datenblätter', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `disable_footprints` tinyint(1) NOT NULL DEFAULT '0',
  `disable_manufacturers` tinyint(1) NOT NULL DEFAULT '0',
  `disable_autodatasheets` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `categories_parent_id_k` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `devices`
--

CREATE TABLE IF NOT EXISTS `devices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `order_quantity` int(11) NOT NULL DEFAULT '0',
  `order_only_missing_parts` tinyint(1) NOT NULL DEFAULT '0',
  `datetime_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `devices_parent_id_k` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `device_parts`
--

CREATE TABLE IF NOT EXISTS `device_parts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_part` int(11) NOT NULL DEFAULT '0',
  `id_device` int(11) NOT NULL DEFAULT '0',
  `quantity` int(11) NOT NULL DEFAULT '0',
  `mountnames` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `device_parts_combination_uk` (`id_part`,`id_device`),
  KEY `device_parts_id_part_k` (`id_part`),
  KEY `device_parts_id_device_k` (`id_device`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `footprints`
--

CREATE TABLE IF NOT EXISTS `footprints` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `filename` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `footprints_parent_id_k` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `internal`
--

CREATE TABLE IF NOT EXISTS `internal` (
  `keyName` char(30) CHARACTER SET ascii NOT NULL,
  `keyValue` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  UNIQUE KEY `keyName` (`keyName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `internal`
--

INSERT INTO `internal` (`keyName`, `keyValue`) VALUES
('dbVersion', '13');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `manufacturers`
--

CREATE TABLE IF NOT EXISTS `manufacturers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `address` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `phone_number` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `fax_number` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `email_address` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `website` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `datetime_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `manufacturers_parent_id_k` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `orderdetails`
--

CREATE TABLE IF NOT EXISTS `orderdetails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id` int(11) NOT NULL,
  `id_supplier` int(11) NOT NULL DEFAULT '0',
  `supplierpartnr` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `obsolete` tinyint(1) DEFAULT '0',
  `datetime_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `orderdetails_part_id_k` (`part_id`),
  KEY `orderdetails_id_supplier_k` (`id_supplier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `parts`
--

CREATE TABLE IF NOT EXISTS `parts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_category` int(11) NOT NULL DEFAULT '0',
  `name` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `instock` int(11) NOT NULL DEFAULT '0',
  `mininstock` int(11) NOT NULL DEFAULT '0',
  `comment` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `id_footprint` int(11) DEFAULT NULL,
  `id_storelocation` int(11) DEFAULT NULL,
  `order_orderdetails_id` int(11) DEFAULT NULL,
  `order_quantity` int(11) NOT NULL DEFAULT '1',
  `manual_order` tinyint(1) NOT NULL DEFAULT '0',
  `id_manufacturer` int(11) DEFAULT NULL,
  `id_master_picture_attachement` int(11) DEFAULT NULL,
  `datetime_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `parts_id_category_k` (`id_category`),
  KEY `parts_id_footprint_k` (`id_footprint`),
  KEY `parts_id_storelocation_k` (`id_storelocation`),
  KEY `parts_order_orderdetails_id_k` (`order_orderdetails_id`),
  KEY `parts_id_manufacturer_k` (`id_manufacturer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pricedetails`
--

CREATE TABLE IF NOT EXISTS `pricedetails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderdetails_id` int(11) NOT NULL,
  `price` decimal(6,2) NOT NULL,
  `price_related_quantity` int(11) NOT NULL DEFAULT '1',
  `min_discount_quantity` int(11) NOT NULL DEFAULT '1',
  `manual_input` tinyint(1) NOT NULL DEFAULT '1',
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pricedetails_combination_uk` (`orderdetails_id`,`min_discount_quantity`),
  KEY `pricedetails_orderdetails_id_k` (`orderdetails_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `storelocations`
--

CREATE TABLE IF NOT EXISTS `storelocations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `is_full` tinyint(1) NOT NULL DEFAULT '0',
  `datetime_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `storelocations_parent_id_k` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `suppliers`
--

CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `address` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `phone_number` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `fax_number` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `email_address` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `website` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `datetime_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `suppliers_parent_id_k` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `attachements`
--
ALTER TABLE `attachements`
  ADD CONSTRAINT `attachements_type_id_fk` FOREIGN KEY (`type_id`) REFERENCES `attachement_types` (`id`);

--
-- Constraints der Tabelle `attachement_types`
--
ALTER TABLE `attachement_types`
  ADD CONSTRAINT `attachement_types_parent_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `attachement_types` (`id`);

--
-- Constraints der Tabelle `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_parent_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`);

--
-- Constraints der Tabelle `devices`
--
ALTER TABLE `devices`
  ADD CONSTRAINT `devices_parent_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `devices` (`id`);

--
-- Constraints der Tabelle `footprints`
--
ALTER TABLE `footprints`
  ADD CONSTRAINT `footprints_parent_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `footprints` (`id`);

--
-- Constraints der Tabelle `manufacturers`
--
ALTER TABLE `manufacturers`
  ADD CONSTRAINT `manufacturers_parent_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `manufacturers` (`id`);

--
-- Constraints der Tabelle `parts`
--
ALTER TABLE `parts`
  ADD CONSTRAINT `parts_id_footprint_fk` FOREIGN KEY (`id_footprint`) REFERENCES `footprints` (`id`),
  ADD CONSTRAINT `parts_id_manufacturer_fk` FOREIGN KEY (`id_manufacturer`) REFERENCES `manufacturers` (`id`),
  ADD CONSTRAINT `parts_id_storelocation_fk` FOREIGN KEY (`id_storelocation`) REFERENCES `storelocations` (`id`),
  ADD CONSTRAINT `parts_order_orderdetails_id_fk` FOREIGN KEY (`order_orderdetails_id`) REFERENCES `orderdetails` (`id`);

--
-- Constraints der Tabelle `storelocations`
--
ALTER TABLE `storelocations`
  ADD CONSTRAINT `storelocations_parent_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `storelocations` (`id`);

--
-- Constraints der Tabelle `suppliers`
--
ALTER TABLE `suppliers`
  ADD CONSTRAINT `suppliers_parent_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `suppliers` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
