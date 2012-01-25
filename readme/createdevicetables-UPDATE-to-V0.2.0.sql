-- --------------------------------------------------------

-- 
-- Table structure for table `devices`
-- 
CREATE TABLE `devices` (
  `id` int(11) NOT NULL auto_increment,
  `name` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `part_device`
-- 

CREATE TABLE `part_device` (
  `id_part` int(11) NOT NULL default '0',
  `id_device` int(11) NOT NULL default '0',
  `quantity` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;