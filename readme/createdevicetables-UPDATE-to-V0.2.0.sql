-- --------------------------------------------------------

-- 
-- Table structure for table `devices`
-- 

CREATE TABLE `devices` (
  `id` int(11) NOT NULL auto_increment,
  `name` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `part_device`
-- 

CREATE TABLE `part_device` (
  `id_part` int(11) NOT NULL default '0',
  `id_device` int(11) NOT NULL default '0',
  `quantity` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_part`)
) ENGINE=MyISAM;