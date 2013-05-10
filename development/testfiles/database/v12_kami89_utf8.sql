-- Status:12:1371:MP_0:part-db-kami89:php:1.24.4:reparierte version 12:5.5.24-0ubuntu0.12.04.1:1:::utf8:EXTINFO
--
-- TABLE-INFO
-- TABLE|categories|131|11452|2012-10-10 21:02:58|MyISAM
-- TABLE|datasheets|13|4196|2012-10-10 21:02:58|MyISAM
-- TABLE|devices|10|2376|2012-10-10 21:02:58|MyISAM
-- TABLE|footprints|98|8592|2012-10-10 21:02:58|MyISAM
-- TABLE|internal|2|2290|2012-10-10 21:02:58|MyISAM
-- TABLE|part_device|124|4208|2012-10-10 21:02:58|MyISAM
-- TABLE|parts|527|63192|2012-10-10 21:02:58|MyISAM
-- TABLE|pending_orders|0|1024|2012-10-10 21:02:58|MyISAM
-- TABLE|pictures|0|1024|2012-10-10 21:02:58|MyISAM
-- TABLE|preise|450|29232|2012-10-10 21:02:58|MyISAM
-- TABLE|storeloc|11|2356|2012-10-10 21:02:58|MyISAM
-- TABLE|suppliers|5|2156|2012-10-10 21:02:58|MyISAM
-- EOF TABLE-INFO
--
-- Dump by MySQLDumper 1.24.4 (http://mysqldumper.net)
/*!40101 SET NAMES 'utf8' */;
SET FOREIGN_KEY_CHECKS=0;
-- Dump created: 2012-10-10 21:03

--
-- Create Table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` mediumtext NOT NULL,
  `parentnode` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `parentnode` (`parentnode`)
) ENGINE=MyISAM AUTO_INCREMENT=134 DEFAULT CHARSET=utf8;

--
-- Data for Table `categories`
--

/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('1','Aktive Bauelemente','0');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('2','Passive Bauelemente','0');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('3','Schalter / Taster','0');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('4','Steck- / Schraubverbinder','0');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('5','Widerstände','2');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('6','SMD 0,26 - 2W','5');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('7','Dioden','1');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('8','Transistoren Bipolar','1');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('9','ICs','1');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('10','Mikrocontroller','1');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('11','Quarze und Resonatoren','1');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('12','Kondensatoren','2');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('13','Elektrolytkondensatoren','12');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('14','MKS Folienkondensatoren','12');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('15','Keramikkondensatoren','12');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('16','Potentiometer','2');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('17','Spulen / Drosseln','2');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('18','Platinensteck- /schraubverbinder','4');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('19','Schraubklemmen','18');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('20','Buchsenleisten','18');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('21','Stiftleisten','18');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('22','Print-Taster','3');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('23','Print-Schiebeschalter','3');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('24','Print-Codierschalter','3');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('25','Print-Druckschalter','3');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('26','Print-Kippschalter/-taster','3');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('27','USB','18');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('28','Power-Jacks','18');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('29','Audio-Steckverbinder','18');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('30','Elektromechanische Bauteile','0');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('31','Sicherungen','30');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('32','Sicherungshalter Printmontage','31');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('33','Jumper','30');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('34','Leiterplatten und Zubehör','30');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('35','Leiterplattenbefestigungen','34');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('36','Molex THT','18');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('37','USB-Stecker / Buchsen','4');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('38','Sub-D Stecker / Buchsen','4');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('39','Gehäuse + Zubehör','30');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('40','Steckergehäuse','39');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('41','Handgehäuse','39');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('42','Standard-Dioden','7');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('43','Zener-Dioden','7');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('44','Schottky-Dioden','7');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('45','Optoelektronische Bauteile','0');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('46','LEDs','45');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('47','3mm','46');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('48','5mm','46');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('49','Zubehör','46');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('50','SMD','46');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('51','Stromversorgung','1');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('52','Festspannungsregler 78xx','51');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('53','Einstellbare Spannungsregler','51');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('54','Platinen','34');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('55','IC-Sockel','30');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('56','Audio-Steckverbinder','4');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('57','Sub-D Printstecker/-buchsen','18');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('58','Schneidklemm-Buchsen','4');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('59','Optokoppler / Optotriac','45');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('60','Relais','30');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('61','Batteriehalter','30');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('62','SMD <= 0,25W','5');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('63','Gleichrichter','1');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('64','SMD > 2W','5');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('65','IR-Empfänger','45');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('66','Werkstatt / Verbrauchsmaterial','0');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('67','Ethernet','18');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('68','LDO-Spannungsregler','51');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('69','Steckernetzteile','30');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('70','Lautsprecher und Signalgeber','30');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('71','Anzeigen und Zubehör','45');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('72','Zubehör','71');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('73','LCDs','71');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('74','PC-Peripherie','9');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('75','Digitale Potentiometer','9');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('76','Leistungsverstärker','9');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('77','I2C-Bausteine','9');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('78','Knäpfe und Knüppel','30');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('79','Inkrementalgeber','3');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('81','Fotowiderstände/Transistoren','45');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('82','10mm','46');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('83','Kartenslots','30');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('84','Batterieklemmen','4');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('85','Decoder/Encoder','9');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('86','8mm','46');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('87','Bipolarkondensatoren','12');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('88','Diac / Triac','9');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('89','Mosfet / Hexfet','9');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('90','Kabeldurchführungen','30');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('91','Schaltregler','51');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('92','Netzteile und Trafos','51');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('93','MKP Folienkondensatoren','12');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('114','Sonstiges','2');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('95','Gehäusezubehör','39');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('96','Distanzbolzen','30');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('97','Tischgehäuse','39');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('98','Spezialwiderstände','5');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('99','Motorsteuer-ICs','9');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('100','Sonstige','9');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('101','Motoren','30');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('102','Transformatoren','2');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('103','SD-Kartenslots','18');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('104','Kühlkörper und Lüfter','30');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('105','Schutzdioden','7');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('106','Operationsverstärker','9');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('107','Vielschichtkondensatoren','12');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('108','Lampen und Fassungen','45');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('109','Kabel','30');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('110','Sonstiges','34');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('111','Bedrahtet <= 0,25W','5');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('112','Bedrahtet 0,26 - 2W','5');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('113','Bedrahtet > 2W','5');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('115','Tantal-Kondensatoren','12');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('116','Molex SMD','18');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('117','Geräte / Bausteine / Bausätze','0');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('118','Bestellte Platinen','34');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('119','SMD Dioden','7');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('120','Chemikalien / Sprays','66');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('121','Lötzubehör','66');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('122','CNC-Zubehör','66');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('123','Schrumpfschlauch','66');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('124','Sonstige Sicherungshalter','31');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('125','Experimentiersysteme','66');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('126','Litzen','66');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('127','Werkstattausstattung','66');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('128','7-Segment','71');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('129','Miniaturtaster mit Hebel','3');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('130','Kaltgerätebuchsen/-stecker','4');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('131','Laborbuchsen/-stecker','4');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('132','Quetschverbinder','4');
INSERT INTO `categories` (`id`,`name`,`parentnode`) VALUES ('133','Wippschalter','3');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;


--
-- Create Table `datasheets`
--

DROP TABLE IF EXISTS `datasheets`;
CREATE TABLE `datasheets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id` int(11) NOT NULL DEFAULT '0',
  `datasheeturl` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `part_id` (`part_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

--
-- Data for Table `datasheets`
--

/*!40000 ALTER TABLE `datasheets` DISABLE KEYS */;
INSERT INTO `datasheets` (`id`,`part_id`,`datasheeturl`) VALUES ('1','136','http://ww1.microchip.com/downloads/en/devicedoc/39662a.pdf');
INSERT INTO `datasheets` (`id`,`part_id`,`datasheeturl`) VALUES ('3','199','http://kitsrus.com/projects/tda7052.pdf');
INSERT INTO `datasheets` (`id`,`part_id`,`datasheeturl`) VALUES ('4','185','http://www.produktinfo.conrad.com/datenblaetter/125000-149999/147028-da-01-en-LD1117V33_Spannungswandler_Datenbaltt.pdf');
INSERT INTO `datasheets` (`id`,`part_id`,`datasheeturl`) VALUES ('5','200','http://www.biltek.tubitak.gov.tr/gelisim/elektronik/dosyalar/6/LM386.pdf');
INSERT INTO `datasheets` (`id`,`part_id`,`datasheeturl`) VALUES ('6','261','https://www.distrelec.ch/ishop/Datasheets/ivAVAGO-4N25_data_e.pdf');
INSERT INTO `datasheets` (`id`,`part_id`,`datasheeturl`) VALUES ('7','258','https://www.distrelec.ch/ishop/Datasheets/ogMOC305xM_data_e.pdf');
INSERT INTO `datasheets` (`id`,`part_id`,`datasheeturl`) VALUES ('9','128','http://www.nxp.com/documents/data_sheet/BT138_SERIES.pdf');
INSERT INTO `datasheets` (`id`,`part_id`,`datasheeturl`) VALUES ('10','265','https://www.distrelec.ch/ishop/Datasheets/ahTSR1_data_de.pdf');
INSERT INTO `datasheets` (`id`,`part_id`,`datasheeturl`) VALUES ('11','218','http://www.produktinfo.conrad.com/datenblaetter/175000-199999/181670-da-01-en-LCD_MODUL_STN_POSITIV_LED_WEISS_16X4.pdf');
INSERT INTO `datasheets` (`id`,`part_id`,`datasheeturl`) VALUES ('12','319','javascript:picV(\'video\',%20800,%20480,%20\'/ishop/Datasheets/07004542.pdf\');');
INSERT INTO `datasheets` (`id`,`part_id`,`datasheeturl`) VALUES ('13','294','http://www.conrad.ch/goto.php?artikel=163071');
INSERT INTO `datasheets` (`id`,`part_id`,`datasheeturl`) VALUES ('15','508','http://www.us.kingbright.com/images/catalog/SPEC/SA10-21EWA.pdf');
INSERT INTO `datasheets` (`id`,`part_id`,`datasheeturl`) VALUES ('16','505','http://www.produktinfo.conrad.com/datenblaetter/150000-174999/160032-da-01-en-7_SEGMENTANZEIGE_25MM_A.pdf');
/*!40000 ALTER TABLE `datasheets` ENABLE KEYS */;


--
-- Create Table `devices`
--

DROP TABLE IF EXISTS `devices`;
CREATE TABLE `devices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` mediumtext NOT NULL,
  `parentnode` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;

--
-- Data for Table `devices`
--

/*!40000 ALTER TABLE `devices` DISABLE KEYS */;
INSERT INTO `devices` (`id`,`name`,`parentnode`) VALUES ('1','Vollbestückung','40');
INSERT INTO `devices` (`id`,`name`,`parentnode`) VALUES ('22','Minimalbestückung','40');
INSERT INTO `devices` (`id`,`name`,`parentnode`) VALUES ('24','MotionControl to LPT Adapter v1','0');
INSERT INTO `devices` (`id`,`name`,`parentnode`) VALUES ('25','Micro-SD-2-DIL Adapter v1','0');
INSERT INTO `devices` (`id`,`name`,`parentnode`) VALUES ('26','PowerJack-2-DIL Adapter v1','0');
INSERT INTO `devices` (`id`,`name`,`parentnode`) VALUES ('27','SPI-2-DIL Adapter v1','0');
INSERT INTO `devices` (`id`,`name`,`parentnode`) VALUES ('28','UART-2-DIL Adapter v1','0');
INSERT INTO `devices` (`id`,`name`,`parentnode`) VALUES ('29','ENC28J60-Modul v2','0');
INSERT INTO `devices` (`id`,`name`,`parentnode`) VALUES ('39','Brushless-Regler','0');
INSERT INTO `devices` (`id`,`name`,`parentnode`) VALUES ('40','v14','39');
/*!40000 ALTER TABLE `devices` ENABLE KEYS */;


--
-- Create Table `footprints`
--

DROP TABLE IF EXISTS `footprints`;
CREATE TABLE `footprints` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` mediumtext NOT NULL,
  `filename` mediumtext,
  `parentnode` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=137 DEFAULT CHARSET=utf8;

--
-- Data for Table `footprints`
--

/*!40000 ALTER TABLE `footprints` DISABLE KEYS */;
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('1','Sonstige','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('2','DIP4','tools/footprints/Aktiv/ICs/DIP/IC_DIP04.png','92');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('3','DIP6','tools/footprints/Aktiv/ICs/DIP/IC_DIP06.png','92');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('4','DIP8','tools/footprints/Aktiv/ICs/DIP/IC_DIP08.png','92');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('5','DIP10','','92');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('6','DIP12','','92');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('7','DIP14','tools/footprints/Aktiv/ICs/DIP/IC_DIP14.png','92');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('8','DIP16','tools/footprints/Aktiv/ICs/DIP/IC_DIP16.png','92');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('9','DIP18','tools/footprints/Aktiv/ICs/DIP/IC_DIP18.png','92');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('10','DIP20','tools/footprints/Aktiv/ICs/DIP/IC_DIP20.png','92');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('11','DIP24','tools/footprints/Aktiv/ICs/DIP/IC_DIP24.png','92');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('12','DIP28','tools/footprints/Aktiv/ICs/DIP/IC_DIP28.png','92');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('13','DIP40','tools/footprints/Aktiv/ICs/DIP/IC_DIP40W.png','92');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('67','MULTIWATT11','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('19','DIP22','tools/footprints/Aktiv/ICs/DIP/IC_DIP22.png','92');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('30','SSOP28','tools/footprints/Aktiv/ICs/SO/SOP/SSOP/IC_SSOP28.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('18','RES-0207','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('20','MULTIWATT15','tools/footprints/Aktiv/ICs/IC_MULTIWATT15.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('21','SO20','tools/footprints/Aktiv/ICs/SO/SO/IC_SO20W.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('22','RES-0805','tools/footprints/Passiv/Widerstaende/SMD/WIDERSTAND-SMD_0805.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('58','RM5.08mm','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('24','PT10','tools/footprints/Passiv/Widerstaende/Trimmer/TRIMMER_PT10-H.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('25','DIP32','tools/footprints/Aktiv/ICs/DIP/IC_DIP32-3.png','92');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('31','DO35','tools/footprints/Aktiv/Dioden/Bedrahtet/DIODE_DO35.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('28','TO220-3','tools/footprints/Aktiv/ICs/TO/IC_TO220.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('29','TO92','tools/footprints/Aktiv/ICs/TO/IC_TO92.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('34','PV32','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('35','DIP48','tools/footprints/Aktiv/ICs/DIP/IC_DIP48W.png','92');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('36','HC49U','tools/footprints/Aktiv/Oszillatoren/Quarze_bedrahtet/QUARZ_HC49-4H.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('37','HC49S','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('38','SMD','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('39','SOIC28','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('40','SSOP16','tools/footprints/Aktiv/ICs/SO/SOP/SSOP/IC_SSOP16.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('41','SO28','tools/footprints/Aktiv/ICs/SO/SO/IC_SO28W.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('42','DPAK','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('43','SO24','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('44','RES-1206','tools/footprints/Passiv/Widerstaende/SMD/WIDERSTAND-SMD_1206.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('45','RES-2512','tools/footprints/Passiv/Widerstaende/SMD/WIDERSTAND-SMD_2512.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('46','SOIC8','tools/footprints/Aktiv/ICs/SO/SO/IC_SO08.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('47','SOT23','tools/footprints/Aktiv/ICs/SO/SOT/IC_SOT23.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('48','SOT89','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('69','TQFP32','tools/footprints/Aktiv/ICs/QFP/TQFP/IC_TQFP32.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('50','RES-0603','tools/footprints/Passiv/Widerstaende/SMD/WIDERSTAND-SMD_0603.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('51','TO218','tools/footprints/Aktiv/ICs/TO/IC_TO218.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('52','RES-0414','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('53','RES-7343','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('54','KEMET-B','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('55','RM1.25mm','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('56','DO214AA','tools/footprints/Aktiv/Dioden/SMD/DIODE_DO214AA.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('57','DO15','tools/footprints/Aktiv/Dioden/Bedrahtet/DIODE_DO15.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('59','RM2.54mm','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('63','DO214AC','tools/footprints/Aktiv/Dioden/SMD/DIODE_DO214AC.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('61','DO201','tools/footprints/Aktiv/Dioden/Bedrahtet/DIODE_DO201.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('62','THT','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('64','DO41','tools/footprints/Aktiv/Dioden/Bedrahtet/DIODE_DO41.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('65','QUAD28.6','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('66','RB1A','tools/footprints/Aktiv/Gleichrichter/GLEICHRICHTER_RB1A.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('68','TO218-5','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('70','CAP-0805','tools/footprints/Passiv/Kondensatoren/Keramik/SMD/KERKO-SMD_0805.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('71','CAP-0603','tools/footprints/Passiv/Kondensatoren/Keramik/SMD/KERKO-SMD_0603.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('72','RM7.5mm','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('73','DSUB-F25','tools/footprints/Elektromechanik/Verbinder/Sub-D/Weiblich_Platinenmontage/SUB-D-PLATINENMONTAGE_W-25.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('74','DSUB-F9','tools/footprints/Elektromechanik/Verbinder/Sub-D/Weiblich_Platinenmontage/SUB-D-PLATINENMONTAGE_W-09.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('75','DSUB-M9','tools/footprints/Elektromechanik/Verbinder/Sub-D/Maennlich_Platinenmontage/SUB-D-PLATINENMONTAGE_M-09.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('76','DSUB-M25','tools/footprints/Elektromechanik/Verbinder/Sub-D/Maennlich_Platinenmontage/SUB-D-PLATINENMONTAGE_M-25.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('77','WS10G','tools/footprints/Elektromechanik/Verbinder/Stiftleisten/2-Reihig_mit_Rahmen_gerade/STIFTLEISTE-GERADE-RAHMEN_2X05.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('78','WS6G','tools/footprints/Elektromechanik/Verbinder/Stiftleisten/2-Reihig_mit_Rahmen_gerade/STIFTLEISTE-GERADE-RAHMEN_2X03.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('79','WS10W','tools/footprints/Elektromechanik/Verbinder/Stiftleisten/2-Reihig_mit_Rahmen_abgewinkelt/STIFTLEISTE-ABGEWINKELT-RAHMEN_2X05.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('80','WS16W','tools/footprints/Elektromechanik/Verbinder/Stiftleisten/2-Reihig_mit_Rahmen_abgewinkelt/STIFTLEISTE-ABGEWINKELT-RAHMEN_2X08.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('81','WS16G','tools/footprints/Elektromechanik/Verbinder/Stiftleisten/2-Reihig_mit_Rahmen_gerade/STIFTLEISTE-GERADE-RAHMEN_2X08.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('82','SO4','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('83','SOT223','tools/footprints/Aktiv/ICs/SO/SOT/IC_SOT223.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('84','KEMET-A','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('85','SOD80','tools/footprints/Aktiv/Dioden/SMD/DIODE_SOD80.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('86','KEMET-C','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('87','RM3.5mm','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('88','RM1.5mm','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('89','Lötanschluss','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('90','LED-3','tools/footprints/Optik/LEDs/Bedrahtet/LED-ROT_3MM.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('91','LED-5','tools/footprints/Optik/LEDs/Bedrahtet/LED-ROT_5MM.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('92','DIP','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('93','Taster','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('94','LSH50','tools/footprints/Elektromechanik/Schalter_Taster/Drucktaster/TASTER_LSH50.png','93');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('95','CAP-0605','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('96','CAP-0405','','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('97','MOLEX-PSL2G','tools/footprints/Elektromechanik/Verbinder/Stiftleisten/Molex/PSL_gerade/STIFTLEISTE-MOLEX-GERADE_PSL-02.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('98','MOLEX-PSL3G','tools/footprints/Elektromechanik/Verbinder/Stiftleisten/Molex/PSL_gerade/STIFTLEISTE-MOLEX-GERADE_PSL-03.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('99','MOLEX-PSL4G','tools/footprints/Elektromechanik/Verbinder/Stiftleisten/Molex/PSL_gerade/STIFTLEISTE-MOLEX-GERADE_PSL-04.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('100','BL1X5','tools/footprints/Elektromechanik/Verbinder/Buchsenleisten/1-Reihig_gerade/BUCHSENLEISTE-GERADE_1X05.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('101','BLW1X5','tools/footprints/Elektromechanik/Verbinder/Buchsenleisten/1-Reihig_abgewinkelt/BUCHSENLEISTE-ABGEWINKELT_1X05.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('102','BUxx','tools/footprints/Elektromechanik/Verbinder/Power-Jacks/BUCHSE_DCPOWERCONNECTOR.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('103','LED-0805','tools/footprints/Optik/LEDs/SMD/LED-ROT_0805.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('104','MELF','tools/footprints/Aktiv/Dioden/SMD/DIODE_MELF.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('105','MINIMELF','tools/footprints/Aktiv/Dioden/SMD/DIODE_MINIMELF.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('106','MICROMELF','tools/footprints/Aktiv/Dioden/SMD/DIODE_MICROMELF.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('107','MOLEX-PSL3W','tools/footprints/Elektromechanik/Verbinder/Stiftleisten/Molex/PSL_abgewinkelt/STIFTLEISTE-MOLEX-ABGEWINKELT_PSL-03.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('108','1214','tools/footprints/Passiv/Kondensatoren/Elektrolyt/SMD/ELKO_SMD_1214.png','0');
INSERT INTO `footprints` (`id`,`name`,`filename`,`parentnode`) VALUES ('109','7-Segment','tools/footprints/Optik/7_Segment/7-SEGMENT_1-20CM.png','0');
/*!40000 ALTER TABLE `footprints` ENABLE KEYS */;


--
-- Create Table `internal`
--

DROP TABLE IF EXISTS `internal`;
CREATE TABLE `internal` (
  `keyName` char(30) CHARACTER SET ascii NOT NULL,
  `keyValue` char(30) DEFAULT NULL,
  UNIQUE KEY `keyName` (`keyName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Data for Table `internal`
--

/*!40000 ALTER TABLE `internal` DISABLE KEYS */;
INSERT INTO `internal` (`keyName`,`keyValue`) VALUES ('dbVersion','12');
INSERT INTO `internal` (`keyName`,`keyValue`) VALUES ('dbAutoUpdate','1');
/*!40000 ALTER TABLE `internal` ENABLE KEYS */;


--
-- Create Table `part_device`
--

DROP TABLE IF EXISTS `part_device`;
CREATE TABLE `part_device` (
  `id_part` int(11) NOT NULL DEFAULT '0',
  `id_device` int(11) NOT NULL DEFAULT '0',
  `quantity` int(11) NOT NULL DEFAULT '0',
  `mountname` mediumtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Data for Table `part_device`
--

/*!40000 ALTER TABLE `part_device` DISABLE KEYS */;
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('381','1','1','IC2');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('458','1','1','D4');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('455','1','1','C7');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('388','1','14','C8,C5,C9,C4,C3,C2,C20,C12,C13,C14,C15,C19,C17,C1');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('437','1','1','C10');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('379','1','1','IC3');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('367','1','1','R1');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('370','1','1','C21');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('459','1','1','R2');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('407','1','1','Q2');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('460','1','4','R19,R28,R25,R26');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('358','1','1','LED1');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('357','1','1','LED2');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('446','1','1','PPM');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('442','1','1','ISP');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('439','1','1','I2C/UART');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('461','1','1','R24');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('438','1','1','R31');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('454','1','1','OK1');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('472','1','1','R29');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('427','1','3','R4,R6,R7');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('466','1','3','R8,R9,R10');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('465','1','3','R3,R12,R13');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('467','1','1','R27');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('420','1','1','R18');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('468','1','1','R21');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('469','1','1','C6');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('368','1','4','R20,R4,R11,R32');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('348','1','3','D1,D2,D3');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('382','1','3','U1,U2,U3');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('314','1','3','C11,C16,C18');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('470','1','6','R14,R15,R16,R17,R22,R23');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('347','1','6','T1,T2,T3,T4,T5,T6');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('471','1','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('468','22','1','R21');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('14','24','3','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('465','22','3','R3,R12,R13');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('466','22','3','R8,R9,R10');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('427','22','3','R4,R6,R7');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('472','22','1','R29');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('486','25','3','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('480','25','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('323','25','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('142','24','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('357','22','1','LED2');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('358','22','1','LED1');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('460','22','4','R19,R28,R25,R26');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('407','22','1','Q2');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('459','22','1','R2');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('487','25','3','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('367','22','1','R1');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('379','22','1','IC3');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('437','22','1','C10');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('388','22','13','C8,C5,C9,C4,C3,C2,C20,C12,C13,C14,C19,C17,C1');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('455','22','1','C7');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('368','25','2','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('381','22','1','IC2');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('473','1','1','R30');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('397','24','9','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('368','22','3','R20,R4,R11');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('348','22','3','D1,D2,D3');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('382','22','3','U1,U2,U3');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('314','22','3','C11,C16,C18');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('470','22','6','R14,R15,R16,R17,R22,R23');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('347','22','6','T1,T2,T3,T4,T5,T6');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('471','22','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('473','22','1','R30');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('479','25','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('476','25','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('388','25','2','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('10','26','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('485','26','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('10','26','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('481','27','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('14','27','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('14','27','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('484','28','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('24','28','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('24','28','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('482','29','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('477','29','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('184','29','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('388','29','8','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('490','29','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('488','29','4','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('491','29','2','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('492','29','2','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('493','29','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('489','29','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('478','29','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('367','29','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('368','29','2','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('495','29','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('494','29','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('479','29','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('476','29','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('476','29','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('476','29','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('476','29','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('163','34','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('468','41','1','R21');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('465','41','3','R3,R12,R13');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('466','41','3','R8,R9,R10');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('427','41','3','R4,R6,R7');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('472','41','1','R29');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('357','41','1','LED2');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('358','41','1','LED1');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('460','41','4','R19,R28,R25,R26');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('407','41','1','Q2');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('459','41','1','R2');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('367','41','1','R1');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('379','41','1','IC3');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('437','41','1','C10');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('388','41','13','C8,C5,C9,C4,C3,C2,C20,C12,C13,C14,C19,C17,C1');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('455','41','1','C7');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('381','41','1','IC2');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('368','41','3','R20,R4,R11');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('348','41','3','D1,D2,D3');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('382','41','3','U1,U2,U3');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('314','41','3','C11,C16,C18');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('470','41','6','R14,R15,R16,R17,R22,R23');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('347','41','6','T1,T2,T3,T4,T5,T6');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('471','41','1','');
INSERT INTO `part_device` (`id_part`,`id_device`,`quantity`,`mountname`) VALUES ('473','41','1','R30');
/*!40000 ALTER TABLE `part_device` ENABLE KEYS */;


--
-- Create Table `parts`
--

DROP TABLE IF EXISTS `parts`;
CREATE TABLE `parts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_category` int(11) NOT NULL DEFAULT '0',
  `name` mediumtext NOT NULL,
  `description` mediumtext,
  `instock` int(11) NOT NULL DEFAULT '0',
  `mininstock` int(11) NOT NULL DEFAULT '0',
  `comment` mediumtext NOT NULL,
  `obsolete` tinyint(1) NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL,
  `id_footprint` int(11) NOT NULL DEFAULT '0',
  `id_storeloc` int(11) NOT NULL DEFAULT '0',
  `id_supplier` int(11) NOT NULL DEFAULT '0',
  `supplierpartnr` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_storeloc` (`id_storeloc`),
  KEY `id_category` (`id_category`)
) ENGINE=MyISAM AUTO_INCREMENT=546 DEFAULT CHARSET=utf8;

--
-- Data for Table `parts`
--

/*!40000 ALTER TABLE `parts` DISABLE KEYS */;
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('1','19','Schraubklemme 2Pol RM5.08 Schwarz',NULL,'21','5','','0','0','1','8','1','731877');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('2','19','Schraubklemme 3Pol RM5.08 Schwarz',NULL,'15','5','','0','0','1','8','1','731891');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('3','19','Schraubklemme 3Pol RM5.08 Blau',NULL,'1','0','','0','0','1','8','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('4','22','Printtaster langer Stössel RM5.08','','5','3','10x10mm, Stössel 7,5mm','0','0','1','8','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('5','22','Printtaster gross eckig rot RM5.08',NULL,'1','1','10x10mm','0','0','1','8','1','703508');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('6','22','Printtaster gross eckig schwarz RM5.08',NULL,'1','1','10x10mm','0','0','1','8','1','703524');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('7','22','Printtaster miniatur mit Stössel 2Anschl.','','1','0','15mm','0','0','62','8','1','707724');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('8','22','Printtaster miniatur',NULL,'27','4','Standard, ohne Stössel','0','0','94','8','1','700336');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('9','27','USB-Printbuchse miniatur',NULL,'1','1','','0','0','1','8','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('10','28','Power-Jack Buchse Printmontage 5.7/1.95mm',NULL,'3','2','','0','0','102','8','1','733260');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('11','29','3,5mm Klinkenbuchse Print Stereo',NULL,'3','1','','0','0','1','8','1','730294');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('12','24','Print-Codierschalter 2er',NULL,'2','1','','0','0','2','8','2','210342');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('13','24','Print-Codierschalter 4er',NULL,'2','1','','0','0','4','8','2','210346');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('14','21','Stiftleiste 2x5Pol mit Rahmen',NULL,'5','2','RM2,54mm\r\nFür 10P-ISP Flachbandkabel','0','0','77','8','2','127088');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('15','21','Stiftleiste 2x8Pol mit Rahmen',NULL,'3','2','RM 2,54mm\r\nFür Display 16P Flachband','0','0','81','8','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('16','21','Stiftleiste 2x3Pol mit Rahmen',NULL,'4','2','RM 2,54mm\r\nFür 6P-ISP\r\n\r\nConrad 741435','0','0','78','8','2','121631');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('17','26','Print-Kippschalter on-on 1P',NULL,'3','2','1A11-NF1PCAE\r\nRastend 2 Stellungen','0','0','1','8','2','202165');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('18','32','Print-Sicherungshalter 5x20mm',NULL,'3','1','Schutzhaube erforderlich!','0','0','1','8','2','288332');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('19','33','Jumper 2,54mm div. Farben',NULL,'7','4','','0','0','1','8','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('20','23','Print-Schiebeschalter 2Pos',NULL,'3','1','','0','0','1','8','2','202372');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('21','35','Platinenbefestigungen 90° Print','','4','2','Metall, M3','0','0','1','8','2','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('22','36','Molex Stiftleiste 2P 2,54mm',NULL,'6','2','','0','0','97','8','1','734166');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('23','36','Molex Stiftleiste 3P 2,54mm',NULL,'11','2','','0','0','98','8','1','734190');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('24','36','Molex Stiftleiste 4P 2,54mm',NULL,'5','2','','0','0','99','8','1','734208');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('25','36','Molex Buchse 2P 2,54mm',NULL,'10','2','','0','0','0','8','1','734021');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('26','36','Molex Buchse 3P 2,54mm',NULL,'5','2','','0','0','0','8','1','734036');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('27','36','Molex Buchse 4P 2,54mm',NULL,'3','2','','0','0','0','8','1','734054');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('28','36','Molex Crimpkontakte 20Stk',NULL,'2','1','','0','0','0','8','1','733884');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('29','37','USB-Stecker Kabelmontage',NULL,'2','1','Passende Gehäuse erforderlich und Verfügbar','0','0','1','8','1','747013');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('30','40','USB-Steckergehäuse für Kabel Transparent','','2','1','Passend zu verfügbaren Stecker. Nur für Kabel-Montage!!!','0','0','1','8','1','747037');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('31','29','Cinch-Buchse doppelt R/L',NULL,'1','1','Ausgelötet, nicht gekauft!','0','0','1','8','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('32','43','Zener-Diode 2,7V 500mW','Zener-Diode 2,7V 500mW','10','2','','0','0','31','5','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('33','43','Zener-Diode 3,3V 500mW','Zener-Diode 3,3V 500mW','10','2','','0','0','31','5','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('34','43','Zener-Diode 3,9V 500mW','Zener-Diode 3,9V 500mW','10','2','','0','0','31','5','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('35','43','Zener-Diode 4,7V 500mW','Zener-Diode 4,7V 500mW','10','2','','0','0','31','5','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('36','43','Zener-Diode 5,1V 500mW','Zener-Diode 5,1V 500mW','10','2','','0','0','31','5','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('37','43','Zener-Diode 5,6V 500mW','Zener-Diode 5,6V 500mW','10','2','','0','0','31','5','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('38','43','Zener-Diode 6,2V 500mW','Zener-Diode 6,2V 500mW','10','2','','0','0','31','5','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('39','43','Zener-Diode 6,8V 500mW','Zener-Diode 6,8V 500mW','10','2','','0','0','31','5','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('40','43','Zener-Diode 7,5V 500mW','Zener-Diode 7,5V 500mW','10','2','','0','0','31','5','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('41','43','Zener-Diode 8,2V 500mW','Zener-Diode 8,2V 500mW','10','2','','0','0','31','5','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('42','43','Zener-Diode 10V 500mW','Zener-Diode 10V 500mW','10','2','','0','0','31','5','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('43','43','Zener-Diode 12V 500mW','Zener-Diode 12V 500mW','10','2','','0','0','31','5','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('44','43','Zener-Diode 15V 500mW','Zener-Diode 15V 500mW','10','2','','0','0','31','5','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('45','43','Zener-Diode 18V 500mW','Zener-Diode 18V 500mW','10','2','','0','0','31','5','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('46','43','Zener-Diode 20V 500mW','Zener-Diode 20V 500mW','10','2','','0','0','31','5','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('47','43','Zener-Diode 22V 500mW','Zener-Diode 22V 500mW','10','2','','0','0','31','5','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('48','43','Zener-Diode 24V 500mW','Zener-Diode 24V 500mW','10','2','','0','0','31','5','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('49','43','Zener-Diode 27V 500mW','Zener-Diode 27V 500mW','10','2','','0','0','31','5','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('50','43','Zener-Diode 30V 500mW','Zener-Diode 30V 500mW','10','2','','0','0','31','5','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('51','43','Zener-Diode 33V 500mW','Zener-Diode 33V 500mW','10','2','','0','0','31','5','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('52','47','LED Rot 3mm Transparent',NULL,'7','3','30° / 80mcd / 20mA / 2V','0','0','90','2','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('53','47','LED Grün 3mm','','9','3','','0','0','90','2','2','254478');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('54','47','LED Gelb 3mm',NULL,'10','3','35° / 30mcd / 20mA / 2.1V','0','0','90','2','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('55','47','LED Blau 3mm Transparent',NULL,'10','3','20° / 2000mcd / 20mA / 3.2V','0','0','90','2','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('56','47','LED Weiss 3mm',NULL,'10','3','25° / 1200mcd / 20mA / 3.2V','0','0','90','2','1','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('57','48','LED Rot 5mm',NULL,'10','3','35° / 80mcd / 30mA / 2V','0','0','91','2','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('58','48','LED Grün 5mm','','10','3','35ï¿½ / 80mcd / 30mA / 2.2V','0','0','91','2','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('59','48','LED Gelb 5mm',NULL,'10','3','35° / 70mcd / 30mA / 2.1V','0','0','91','2','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('60','48','LED Blau 5mm Transparent',NULL,'10','3','12° / 1000mcd / 20mA / 3.2V','0','0','91','2','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('61','48','LED Weiss 5mm',NULL,'10','3','12° / 3000mcd / 20mA / 3.2V','0','0','91','2','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('62','48','LED UV 5mm',NULL,'10','3','20° / 3000mcd / 3.2V - 3.6V / Wellenlänge 400-405','0','0','91','2','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('63','48','LED Rot 5mm Ultrahell Transparent',NULL,'6','1','20° / 9800mcd / 1.9V - 2V / 20mA','0','0','91','2','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('64','48','LED Infrarot 5mm',NULL,'2','2','AN304 / 950nm','0','0','91','2','2','631016');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('65','50','SMD-LED Farbe unbekannt',NULL,'18','2','keine Angaben','0','0','1','2','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('66','49','LED-Fassung 3mm Schwarz Kunststoff',NULL,'20','3','Zum Einpressen','0','0','1','2','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('67','49','LED-Fassung 5mm Schwarz Kunststoff',NULL,'20','3','Zum Einpressen','0','0','1','2','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('68','49','LED-Fassung 5mm Metall',NULL,'1','1','Schraubmontage, Bohrung 8mm','0','0','1','2','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('69','8','BC327','Transistor PNP','9','3','','0','0','29','7','1','155829');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('70','8','BC337','Transistor NPN','15','5','','0','0','29','7','1','155918');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('71','8','BC517','Transistor NPN','16','4','','0','0','0','7','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('72','8','BC548B','Transistor NPN','12','4','','0','0','0','7','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('73','8','BC557B','Transistor PNP','9','3','','0','0','0','7','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('74','11','Quarz 3,8684Mhz hoch',NULL,'3','1','','0','0','1','7','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('75','11','Quarz 8Mhz tief',NULL,'2','2','HC49/4H','0','0','36','7','2','666944');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('76','11','Quarz 10Mhz tief',NULL,'2','2','HC49/4H','0','0','36','7','2','666956');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('77','11','Quarz 9.8304Mhz hoch',NULL,'1','1','','0','0','1','7','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('78','11','Quarz 8.192Mhz tief',NULL,'2','1','HC49/4H','0','0','36','7','2','666945');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('79','11','Quarz 20Mhz tief',NULL,'3','1','','0','0','36','7','2','666953');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('80','11','Quarz 16Mhz tief',NULL,'2','2','HC49/4H','0','0','36','7','2','666952');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('81','11','Quarz 25Mhz SMD',NULL,'2','1','HC49/4H SMX','0','0','37','7','2','666982');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('82','11','Quarz 32.768kHz',NULL,'2','1','','0','0','1','7','1','168467');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('83','52','L7805','Spannungsregler 5V','8','3','','0','0','28','7','1','179205');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('84','52','L7806','Spannungsregler 6V','4','1','','0','0','28','7','1','179213');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('85','52','L7809','Spannungsregler 9V','2','1','','0','0','28','7','1','179191');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('86','52','L7812','Spannungsregler 12V','8','3','','0','0','28','7','1','179230');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('87','53','LM317','Einstellbarer Spannungsregler','4','2','','0','0','28','7','1','176001');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('472','62','Widerstand 24k 0,1W',NULL,'10','4','','0','0','50','6','2','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('89','14','MKS Folienkondensator 330nF gross',NULL,'8','2','','0','0','1','7','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('90','14','Folienkondensator MKS2 10nF 63V',NULL,'9','4','Conrad 459960','0','0','58','7','2','820590');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('91','14','Folienkondensator MKS2 100nF 63V',NULL,'51','5','Conrad 459830','0','0','58','7','2','820596');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('92','15','Kerko 22pF 500V',NULL,'35','8','','0','0','1','7','2','838431');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('93','111','Widerstand 330R',NULL,'101','1','100er Pack','0','0','18','6','1','403989');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('94','42','1N4007','Diode 1000V 1A','100','15','','0','0','64','4','1','162272');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('95','42','1N4148','Diode 75V 0,15A','100','15','','0','0','31','4','1','162280');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('96','21','Stiftleiste 1-Reihig 20Pol',NULL,'15','2','','0','0','1','4','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('97','21','Stiftleiste 1-Reihig 20Pol gewinkelt RM2.54',NULL,'3','1','Distrelec 36Pol 127252','0','0','1','4','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('98','21','Stiftleiste 2-Reihig 32Pol RM2.54',NULL,'11','2','','0','0','1','4','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('99','20','Buchsenleiste 1-Reihig 20Pol RM2.54',NULL,'3','3','Distrelec 50Pol 121562','0','0','100','4','1','738335');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('100','20','Präzisionsbuchsenleiste 1-Reihig 20Pol RM2.54','','8','1','Nicht mehr bestellen','0','0','62','4','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('101','20','Buchsenleiste 1-Reihig 20Pol RM2.54 gewinkelt',NULL,'4','3','Distrelec 50Pol 121599','0','0','101','4','1','737314');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('102','20','Buchsenleiste 2x5Pol SMD',NULL,'4','1','','0','0','38','4','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('103','54','Hartpapier-Platine 50x100mm Einseitig Cu',NULL,'5','4','','0','0','0','4','1','528200');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('104','55','IC-Sockel 8Pol',NULL,'3','2','','0','0','4','4','2','651924');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('105','55','IC-Sockel 10Pol',NULL,'1','1','','0','0','5','4','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('106','55','IC-Sockel 16Pol',NULL,'7','3','','0','0','8','4','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('107','55','IC-Sockel 18Pol',NULL,'2','2','','0','0','9','4','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('108','55','IC-Sockel 20Pol',NULL,'13','2','','0','0','10','4','1','189839');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('109','55','IC-Sockel 22Pol',NULL,'1','1','','0','0','19','4','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('110','55','IC-Sockel 28Pol',NULL,'34','3','Distrelec 650560\r\n17Stk für 8.70Fr.: Distrelec 652647','0','0','12','4','1','179994');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('111','55','IC-Sockel 28Pol Breit',NULL,'6','1','','0','0','12','4','2','651931');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('112','55','IC-Sockel 22Pol Breit',NULL,'2','1','','0','0','19','4','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('113','55','IC-Sockel 48Pol',NULL,'1','1','','0','0','35','4','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('114','76','LM386N-1','Verstärker','2','1','','0','0','4','4','2','662226');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('115','100','74HCT373','Octal Transparent Latch','2','1','','0','0','10','4','2','649751');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('116','99','L297','Schrittmotorchopper','0','1','','0','0','10','4','1','156129');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('117','99','L298','Schrittmotortreiber','2','1','','0','0','20','4','1','156128');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('118','100','NE555','Timer','5','1','','0','0','4','4','1','177113');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('119','74','MAX232N','RS232 Treiber','2','1','','0','0','8','4','1','152281');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('120','77','PCF8574P','I2C Portexpander','1','1','','0','0','8','4','2','648999');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('121','75','DS1809-100+','IC','1','1','100kOhm Digitalpotentiometer','0','0','4','4','2','641802');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('122','10','ATMega8535-16PU','Mikrocontroller','1','1','','0','0','13','4','1','154257');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('123','100','TDA4605-3','Schaltnetzteil-IC','1','1','','0','0','4','4','1','081735');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('124','100','7490N','IC','1','1','','0','0','7','4','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('125','100','7442N','IC','1','1','','0','0','8','4','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('126','100','74HC244D','IC','3','1','','0','0','21','4','2','649548');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('127','89','IRFZ46N','N-Mosfet 55V 46A','4','2','','0','0','28','4','1','162752');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('128','88','BT138F-800','Triac','2','1','','0','0','28','4','2','620071');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('129','10','ATTiny2313-20PU','Mikrocontroller','2','2','Distrelec 645062 3.90Fr','0','0','10','4','1','154166');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('130','10','ATMega8-16PU','Mikrocontroller','3','2','Conrad 154054 11.95Fr.','0','0','12','4','2','642775');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('131','10','ATTiny24V-10PU','Mikrocontroller','3','2','','0','0','7','4','2','666018');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('132','10','ATMega16-16PU','Mikrocontroller','2','2','Distrelec 642777 11.10Fr\r\n','0','0','13','4','1','154242');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('133','89','IRF1010NPBF','N-Mosfet 55V 85A','2','2','','0','0','28','4','2','612155');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('134','89','IRLZ 34NPBF','N-Mosfet 55V 30A','2','2','','0','0','28','4','2','605379');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('135','75','DS1804-010+','IC','1','1','10kOhm Digitalpoti','0','0','4','4','1','170007');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('136','74','ENC28J60/SP ','Ethernet-Controller','2','1','','0','0','12','4','2','662876');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('137','56','Mono-Klinkenstecker 3.5mm',NULL,'1','1','','0','0','1','4','1','731471');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('138','38','Sub-D Buchse 25Pol Lötanschlüsse','','1','1','','0','0','1','4','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('139','38','Sub-D Stecker 25Pol Lötanschlüsse','','1','1','','0','0','1','4','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('140','38','Sub-D Stecker 15Pol Lötanschlüsse','','3','1','','0','0','1','4','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('141','40','Sub-D Steckergehäuse 15Pol','','3','1','','0','0','1','4','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('142','57','Sub-D Buchse Print 25Pol 90°','','0','2','','0','0','73','4','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('143','57','Sub-D Stecker Print 25Pol 90°','','2','1','','0','0','76','4','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('144','57','Sub-D Stecker Print 9Pol 90°','','5','2','','0','0','75','4','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('145','57','Sub-D Buchse Print 9Pol 90°','','1','1','','0','0','74','4','1','742613');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('146','58','Schneidklemm-Buchse 2x5Pol',NULL,'8','4','','0','0','1','4','2','127114');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('147','58','Schneidklemm-Buchse 2x8Pol',NULL,'4','2','','0','0','1','4','2','127116');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('148','58','Schneidklemm-Buchse 2x3Pol',NULL,'6','4','','0','0','1','4','1','742324');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('149','59','Optokoppler CNY17-3',NULL,'4','1','','0','0','3','4','1','153396');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('150','60','Print-Relais 5V / 10A 250V Weiss',NULL,'5','2','Finder, 1 Wechsler','0','0','1','4','1','503243');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('151','60','Print-Relais 5V',NULL,'3','1','2 Wechsler','0','0','1','4','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('152','61','Batteriehalter 4xAA Weiss',NULL,'1','1','','0','0','0','3','1','522557');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('153','15','Kerko 100nF 50V X7R',NULL,'18','4','','0','0','70','4','1','445210');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('154','15','Kerko 22pF 50V',NULL,'10','2','','0','0','70','4','1','445432');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('155','62','Widerstand 10k 0,1W',NULL,'20','4','','0','0','22','4','1','406376');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('156','13','Elko 2200uF 63V',NULL,'5','1','','0','0','72','4','1','472670');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('157','16','Poti mit Achse 10k linear',NULL,'1','1','Kunststoff','0','0','62','4','1','445536');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('158','16','Poti mit Achse 220k linear',NULL,'1','1','Kunststoff','0','0','62','4','1','445703');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('159','16','Trimmer 100k weiss',NULL,'1','1','','0','0','34','4','1','422388');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('160','16','Trimmer 10k weiss',NULL,'3','1','','0','0','34','4','1','447321');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('161','16','Trimmer 10k stehend linear',NULL,'1','1','','0','0','0','4','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('162','16','Trimmer 100k liegend linear',NULL,'3','1','','0','0','0','4','1','431397');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('163','16','Mini-Trimmer 1k liegend',NULL,'4','1','linear','0','0','24','4','1','430838');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('164','16','Mini-Trimmer 10k liegend',NULL,'5','5','linear','0','0','24','4','1','430862');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('165','16','Mini-Trimmer 100k liegend',NULL,'3','1','linear','0','0','24','4','1','430897');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('166','16','Mini-Trimmer 100k stehend',NULL,'2','1','linear','0','0','24','4','1','430765');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('167','16','Mini-Trimmer 10k stehend',NULL,'4','1','linear','0','0','24','4','1','430730');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('168','55','IC-Sockel 40Pol',NULL,'3','2','Distrelec 651933 breit 1.10Fr','0','0','13','4','1','189677');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('169','55','IC-Sockel 32Pol',NULL,'3','2','','0','0','25','4','1','189871');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('170','63','B40R','Brückengleichrichter','3','1','','0','0','66','4','1','501433');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('171','113','Drahtwiderstand 0.47Ohm 5W',NULL,'2','2','','0','0','1','4','1','428037');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('172','65','TSOP1738',NULL,'1','1','','0','0','1','4','1','0');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('173','65','TSOP1736',NULL,'2','1','','0','0','1','4','1','171069');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('174','17','Induktivität axial 0.1mH 1.62A','','2','1','','0','0','1','4','2','353620');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('175','15','Kerko 100nF 50V/100V',NULL,'10','8','Conrad 453099 50V\r\nDistrelec 833111 100V','0','0','59','7','1','453099');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('176','125','Laborkabel mit Krokodilklemmen beidseitig',NULL,'10','1','','0','0','0','9','2','100264');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('177','32','Schutzhaube zu Sicherungshalter 5x20mm',NULL,'3','1','','0','0','0','8','2','288333');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('178','74','FT232RL','USB-Seriell Wandler','1','2','','0','0','30','4','2','662109');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('179','121','Lötzinn Bleifrei 1.0mm Sn99/Cu1 100g','','1','1','','0','0','0','9','2','954638');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('180','120','Lötlack SK10 400ml','','1','1','','0','0','0','9','2','956367');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('181','120','Schutzlack Urethan 200ml',NULL,'1','1','','0','0','0','9','2','950109');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('182','17','Induktivität axial 0.01mH 1.4A','','1','0','','0','0','1','4','2','353600');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('183','125','Experimentierplatte',NULL,'1','1','','0','0','0','9','2','458073');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('184','67','Ethernet Buchse m. Übertrager','','1','1','','0','0','1','8','2','662422');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('185','68','LDO-Spannungsregler 3.3V',NULL,'2','2','LD1117V33\r\nOder besser 645487','0','0','28','7','2','662724');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('186','11','Resonator 10MHz',NULL,'3','1','noch nicht inventiert!','0','0','1','4','2','667567');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('187','11','Resonator 16MHz',NULL,'5','3','noch nicht inventiert!','0','0','62','4','2','667589');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('188','121','Lötzinn-Absauger 200mm','','1','1','','0','0','0','9','2','950680');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('189','121','Aktivator für Lötspitzen Bleifrei','','1','1','','0','0','0','9','2','953693');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('190','121','Lötzinn Bleifrei 100g 0.5mm','','1','1','Sn95.5/Ag3.8/Cu0.7','0','0','0','9','2','954615');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('191','121','Lötspitze Punktform für WHS-M','','1','1','','0','0','0','9','2','956877');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('192','125','Jumperkabel für Steckbrett KS-30','','1','1','1 Pack = 75 Stk = 7.20Fr','0','0','0','9','2','458099');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('193','122','Hartmetall Speerbohrer 0.8mm',NULL,'2','1','','0','0','0','9','1','814549');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('194','69','Steckernetzteil 5V 1200mA',NULL,'2','1','514221','0','0','0','9','1','512683');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('195','41','Handgehäuse Plastik 66x66x28 Schwarz','','1','1','','0','0','0','4','1','522667');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('196','70','Lautsprecher 5cm 8Ohm',NULL,'0','0','','0','0','0','4','1','710690');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('197','72','LCD-Rahmen für 4x16 Display','','1','0','','0','0','0','4','1','142042');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('198','15','Kerko 470pF 100V',NULL,'3','1','','0','0','58','4','1','500825');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('199','76','TDA7052','NF-Verstärker','1','1','','0','0','4','4','1','181544');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('200','76','LM386','Verstärker','1','0','','0','0','46','4','1','142336');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('201','127','Weisse Schublade 24 Fächer','','5','0','','0','0','0','9','1','419168');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('202','78','Drehknopf mit seitl. Befestigung',NULL,'0','0','','0','0','0','4','1','717581');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('203','54','Hartpapier-Platine 100x100mm Einseitig Cu',NULL,'7','3','','0','0','0','4','1','528226');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('204','79','Inkrementalgeber Vertikal',NULL,'0','0','','0','0','1','4','1','705594');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('205','117','DCF77 Baustein','','0','0','','0','0','0','4','1','641138');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('206','127','Isolierband 15mm 10m Schwarz',NULL,'3','1','','0','0','0','9','1','541659');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('207','40','USB-Gehäuse Transparent','','1','1','','0','0','0','4','1','531276');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('208','27','USB-Stecker Print Typ A Flach',NULL,'1','1','','0','0','1','8','1','747085');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('209','54','Epoxyd-Platine 75x100mm Einseitig Cu',NULL,'0','0','','0','0','0','4','1','528315');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('210','55','IC-Sockel 14Pol',NULL,'7','2','','0','0','7','4','1','189618');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('211','123','Schrumpfschlauch 2.4',NULL,'1','1','','0','0','0','9','1','530891');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('212','123','Schrumpfschlauch 1.6',NULL,'1','1','','0','0','0','9','1','531243');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('213','123','Schrumpfschlauch 3.2',NULL,'1','1','','0','0','0','9','1','531251');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('214','123','Schrumpfschlauch 4.7',NULL,'1','1','','0','0','0','9','1','530905');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('215','126','Litze 0.14mm2 Schwarz',NULL,'1','1','','0','0','0','9','1','605816');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('216','126','Litze 0.14mm2 Rot',NULL,'1','1','','0','0','0','9','1','605808');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('217','54','Platine Punktraster 160x100mm',NULL,'1','1','Distrelec 450124 5.50Fr','0','0','0','4','1','527769');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('253','90','Kabeldurchführung mit Knickschutz 5,8mm soft','','3','2','','0','0','0','4','2','505649');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('218','73','LCD Positiv LED Weiss 16x4',NULL,'0','0','','0','0','0','4','1','181670');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('219','79','Inkrementalgeber STEC12E08',NULL,'0','0','','0','0','1','4','1','700708');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('220','81','Fotowiderstand A9060',NULL,'1','1','','0','0','1','4','1','145475');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('221','82','LED 10mm Weiss',NULL,'3','0','','0','0','1','4','1','181154');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('222','78','Drehknopf mit seitl. Befestigung Nr2',NULL,'0','0','6mm','0','0','0','4','1','717529');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('223','120','Schutzlack Plastik 70 200ml',NULL,'0','1','','0','0','0','9','1','55265');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('224','120','Reinigungsspray Kontakt LR 200ml',NULL,'0','0','','0','0','0','9','1','55247');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('225','121','Entlötsauglitze','','1','1','Reichelt ENTLÖTLITZE AB','0','0','0','9','1','812066');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('226','83','Kartenslot MMC12 SMD',NULL,'1','1','','0','0','38','8','3','CONNECTOR MMC 12');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('227','11','Quarz 12.2889MHz',NULL,'4','1','','0','0','36','7','3','12.2880-HC49U-S');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('228','76','TDA7053','Audio-Verstärker','3','1','','0','0','8','4','3','TDA 7053');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('229','84','Batterieclip 9V Block Robust',NULL,'2','0','','0','0','0','4','3','CLIP HQ9V');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('230','84','Batterieclip 9V Block',NULL,'4','1','','0','0','0','4','3','CLIP 9V');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('231','84','Batterieclip 9V Block T-Form',NULL,'4','1','','0','0','0','4','3','CLIP 9V-T');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('232','85','VS1011E-S','MP3-Decoder','2','1','','0','0','39','4','3','VS 1011E-S');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('233','61','Batteriehalter 9V Block',NULL,'2','1','','0','0','0','3','3','HALTER 9V');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('234','61','Batteriehalter 4xAA Schwarz',NULL,'3','1','','0','0','0','3','3','HALTER 4XUM3-1DK');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('235','61','Batteriehalter 9V oder 2xAA Einbau Schwarz',NULL,'1','0','','0','0','0','3','3','HALTER 1X9V');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('236','115','Tantal Kondensator 0,22uF 35V',NULL,'5','1','','0','0','59','4','1','481637');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('237','15','Kerko 27pF 100V',NULL,'12','0','','0','0','59','4','1','457175');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('238','15','Kerko 220pF 100V',NULL,'4','1','','0','0','59','4','1','457272');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('239','15','Kerko 33pF ',NULL,'10','2','','0','0','1','4','1','500897');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('240','86','LED 8mm Gelb-Orange 360°','','1','1','','0','0','1','2','1','184473');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('241','86','LED 8mm Weiss 360°','','2','2','','0','0','1','2','1','184421');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('242','36','Molex Stiftleiste 3Pol 90°','','5','1','','0','0','107','8','1','733777');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('243','21','Stiftleiste 2x5Pol abgewinkelt mit Rahmen',NULL,'2','2','','0','0','79','8','1','736950');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('245','17','Drossel 1uH',NULL,'2','1','','0','0','18','4','1','535591');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('246','17','Drossel 100uH',NULL,'2','1','','0','0','18','4','1','535788');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('247','87','Bipolarer Kondensator 10uF',NULL,'2','1','','0','0','1','4','1','472743');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('248','87','Bipolarer Kondensator',NULL,'2','1','','0','0','1','4','1','472700');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('249','87','Bipolarer Kondensator 100uF',NULL,'2','1','','0','0','1','4','1','472808');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('250','14','Folienkondensator MKS4 470nF 63V',NULL,'5','1','','0','0','72','4','1','459935');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('251','14','Folienkondensator MKS4 47nF 100V',NULL,'5','1','','0','0','72','4','1','459817');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('252','17','Drossel 10uH 410mA',NULL,'2','1','','0','0','18','4','1','535729');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('254','90','Zugentlastungsklemme',NULL,'2','1','','0','0','0','4','2','505650');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('255','70','Lauttsprecher 8cm 15W 4Ohm',NULL,'1','0','','0','0','0','9','2','153946');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('256','70','Lauttsprecher 8cm 50W 4Ohm',NULL,'1','0','','0','0','0','9','2','150250');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('257','92','Printnetzteil Traco Power 12VDC 833mA',NULL,'1','0','TML 10112 / 230VAC /\r\nBauteil in Eagle vorhanden','0','0','1','9','2','361002');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('258','59','Optotriac MOC3052M',NULL,'3','1','','0','0','3','4','2','630638');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('259','90','Knickschutztülle 6.5mm','','4','1','','0','0','0','4','2','500334');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('260','56','Stereo-Klinkenstecker 3,5mm',NULL,'3','1','','0','0','0','4','2','159794');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('261','59','Optokoppler 4N25',NULL,'5','1','','0','0','3','4','2','630144');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('262','90','Knickschutztülle 5,5mm','','4','1','','0','0','0','4','2','500333');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('263','90','Zugentlastungsklemme',NULL,'3','1','','0','0','0','4','2','505650');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('264','90','Knickschutztülle 3,5mm','','4','1','','0','0','0','4','2','500335');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('265','91','Schaltregler Traco Power 5VDC 1A',NULL,'2','0','TSR 1-2450','0','0','62','4','2','361662');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('266','14','Folienkondensator MKS4 1uF 100V',NULL,'4','1','100VDC/63VAC','0','0','72','4','2','823791');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('267','93','Folienkondensator MKP X2 10nF 275VAC',NULL,'5','1','Für Sniffer','0','0','1','4','2','824116');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('268','112','Metalloxydschicht-Widerstand 18 Ohm 1W',NULL,'6','1','5%','0','0','18','3','2','712565');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('269','112','Metalloxydschicht-Widerstand 100 kOhm 1W',NULL,'4','1','','0','0','18','3','2','712610');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('270','112','Metalloxydschicht-Widerstand 47 kOhm 1W',NULL,'4','1','','0','0','18','3','2','712606');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('271','112','Metalloxydschicht-Widerstand 15 Ohm 1W',NULL,'6','1','','0','0','18','3','2','712564');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('272','38','Sub-D Stecker 9Pol',NULL,'5','2','Anschlüsse sind nicht beschriftet','0','0','89','4','1','742066');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('273','40','Sub-D Steckergehäuse 9Pol','','5','2','','0','0','0','4','1','711764');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('274','38','Sub-D Buchse 9Pol',NULL,'5','2','Anschlüsse sind nicht beschriftet','0','0','89','4','1','742082');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('275','95','Gehäusefüsse PU klar selbstklebend','','20','4','','0','0','0','3','1','540474');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('276','96','Distanzhülse d=3mm l=10mm','','20','4','','0','0','0','3','1','526363');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('277','96','Distanzhülse d=3mm l=25mm','','20','4','','0','0','0','3','1','526398');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('278','96','Distanzhülse d=3mm l=30mm','','20','4','','0','0','0','3','1','526401');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('279','55','IC-Sockel 6Pol',NULL,'4','2','','0','0','3','4','1','184864');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('280','97','Tischgehäuse Kunststoff 135x95x45 grau','','2','0','','0','0','0','4','1','523132');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('281','55','Quarz-Sockel',NULL,'2','1','','0','0','36','7','1','168777');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('282','98','Druckabhängiger Widerstand','','2','1','','0','0','0','9','1','503369');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('283','114','Joystick-Poti mit Taster',NULL,'1','1','','0','0','62','9','1','425637');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('284','114','Joystick-Poti ohne Taster',NULL,'1','1','','0','0','62','9','1','425609');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('285','78','Joystick-Knüppel','','2','2','','0','0','0','9','1','710047');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('286','96','Gewindebolzen für Sub-D mit Muttern','','5','2','','0','0','0','9','1','741370');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('287','96','Gewindebolzen Sub-D',NULL,'4','2','','0','0','0','9','1','742342');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('288','19','Printklemme 2,5mm2 2pol',NULL,'4','4','Distrelec 129503','0','0','58','9','1','744153');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('289','19','Printklemme 2,5mm2 3pol',NULL,'8','4','','0','0','58','9','1','744167');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('290','99','TMC428-I','IC Trinamic','1','1','','0','0','40','9','1','198672');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('291','101','60MM SCHRITTMOTOR, 1.8°, 2.8A, 2.1 NM ','','1','1','','0','0','0','9','1','197969');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('292','99','TMC249A-SA','STEPPER TREIBER 4 A SG','1','1','','0','0','41','9','1','198775');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('293','89','BSS83P','P-Mosfet Kleinleistung 60V 0,33A','4','2','','0','0','47','9','1','153047');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('294','89','IR2101S','Mosfet-Treiber','2','1','','0','0','46','9','1','163071');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('295','100','ICM7555','Taktgeber','2','1','','0','0','4','4','1','182877');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('296','15','Kerko Sortiment',NULL,'1','1','','0','0','71','9','1','420266');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('297','14','Folienkondensator MKS2 220nF 100V',NULL,'10','5','','0','0','58','9','1','459918');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('298','14','FolienkondensatorMKS4 1uF 63V',NULL,'5','4','','0','0','72','9','1','459964');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('299','64','Widerstand 0,1R 3W',NULL,'9','2','','0','0','38','9','1','428804');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('300','113','Widerstand 0,1 Ohm 5W',NULL,'4','2','','0','0','62','9','1','401668');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('301','44','MBRS1100','Schottky-Diode 1A 100V','10','4','','0','0','56','9','1','163746');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('302','44','SB3100','Schottky-Diode 3A 100V','10','5','','0','0','61','9','1','160222');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('303','44','SB150','Schottky-Diode 1A 50V','10','5','','0','0','57','9','1','160211');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('304','44','SB190','Schottky-Diode 1A 90V','10','5','','0','0','57','9','1','160213');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('305','111','Null-Ohm Widerstand 0207',NULL,'20','5','','0','0','18','9','1','403709');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('306','127','Widerstands-Uhr',NULL,'1','1','','0','0','0','9','1','400009');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('307','62','Widerstandssortiment',NULL,'1','1','','0','0','50','9','1','419646');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('308','89','IRLR120N','N-Mosfet 100V 10A','5','2','','0','0','42','4','1','162845');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('309','13','Elko 1000uF 63V',NULL,'5','2','RM7.5 / D=16\r\nConrad 442156','0','0','72','4','2','818581');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('310','99','TMC428-PI24','Stepper Controller ','0','0','','0','0','43','4','1','198685');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('311','42','MUR860G','Diode 600V 8A','2','2','Gleichrichterdiode','0','0','28','4','2','601250');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('312','89','IRF9520NPBF','P-Mosfet 55V 6.8A','3','2','','0','0','28','4','2','611837');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('313','13','SMD Elko 10uF 16V',NULL,'10','3','Conrad 445124','0','0','96','4','2','818272');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('314','15','Kerko 220nF 25V',NULL,'20','5','','0','0','71','4','2','823501');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('315','13','Elko 100uF 16V SMD',NULL,'10','4','','0','0','95','4','1','445111');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('316','14','Folienkondensator MKS2 22nF 250V',NULL,'10','4','Distrelec 828022','0','0','58','4','1','449990');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('317','6','SMD-Widerstand 10 Ohm 2W',NULL,'8','4','','0','0','38','4','2','719634');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('318','64','Widerstand 0,15R 3W',NULL,'8','2','','0','0','45','4','2','715217');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('319','63','GBPC 2510W','Brückengleichrichter 700V 25A','1','1','','0','0','65','4','2','605540');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('320','102','Ringkerntrafo 300VA 230V/2x15V',NULL,'1','1','','0','0','0','9','2','354196');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('321','103','SD-Kartenslot SMD Distrelec_1',NULL,'2','1','ALPS','0','0','38','4','2','129448');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('322','103','SD-Kartenslot SMD Distrelec_2',NULL,'2','1','3M','0','0','38','4','2','125442');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('323','103','MicroSD-Kartenslot',NULL,'2','1','','0','0','38','4','2','129888');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('324','99','L6203','Motortreiber 4A','3','4','','0','0','67','4','2','646988');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('325','19','Stecker 2Pol RM5.08',NULL,'4','2','','0','0','58','4','2','140934');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('326','19','Steckerbuchse 2Pol RM5.08',NULL,'4','2','','0','0','58','4','2','142258');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('327','70','Lautsprecherabdeckung FRS8',NULL,'1','0','','0','0','0','9','2','150001');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('328','19','Stecker 4pol RM5.08',NULL,'3','1','','0','0','58','4','2','140938');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('329','19','Steckerbuchse 4pol RM5.08',NULL,'3','1','','0','0','58','4','2','142260');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('330','104','Kühlkörper Multiwatt zum klammern','','2','0','5.6K/W\r\n37.5mm lang','0','0','0','4','2','650027');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('331','104','Aufsteckkühlkörper TO220','','4','1','','0','0','0','4','2','652411');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('332','104','Aufsteckkühlkörper TO220 Breit','','4','1','','0','0','0','4','2','652405');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('333','127','Wärmeleitpaste 10ml Spritze','','1','1','','0','0','0','9','2','650181');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('334','99','L6506','Schrittmotor-Chopper','4','1','','0','0','9','4','2','649042');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('335','27','USB-2.0 Buchse Typ B',NULL,'5','1','','0','0','1','8','2','129099');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('336','70','Lauttsprecher 8cm 15W 8Ohm',NULL,'0','0','','0','0','0','9','2','153947');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('337','59','Optokoppler Schnell 6N138',NULL,'8','2','z.B. für UART optisch trennen','0','0','4','4','2','630080');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('338','104','Wärmeleitfolie 70/50 TO220 0,25mm','','6','1','','0','0','0','4','1','189066');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('339','104','Wärmeleitfolie 70/50 TO247 0,25mm','','6','1','','0','0','0','4','1','180346');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('340','105','P6KE15CA','Suppressordiode 12V Bidirektional','4','1','','0','0','31','4','1','167991');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('341','105','P6KE6V8CA','Suppressordiode  5V Bidirektional','7','1','P6KE6V8CA','0','0','31','4','1','167967');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('342','105','P6KE68CA','Suppressordiode  68V Bidirektional','4','1','','0','0','31','4','1','162063');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('343','44','SB850','Schottky-Gleichrichterdiode 8A 50V','8','2','D5,4mm\r\nL7,5mm','0','0','62','4','1','161032');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('344','42','P2000G','Diode 400V 20A','8','2','Semikron','0','0','1','4','1','160484');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('345','42','P1000D','Diode 10A 200V','10','2','Semikron','0','0','62','4','1','160074');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('346','113','Widerstand Draht 0,1 Ohm, 50W, 5%',NULL,'1','0','','0','0','1','4','1','421421');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('347','89','IRLR7843','N-Mosfet 30V 113A','10','2','Conrad 164398','0','0','42','4','2','605328');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('348','42','BAS21','Diode 200V 0,25A','20','2','','0','0','47','4','1','153167');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('349','106','LM-393N','OPV','5','1','','0','0','4','4','1','174858');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('350','89','IR2102','Mosfet-Treiber','1','1','','0','0','4','4','1','163073');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('351','89','IR2103','Mosfet-Treiber','1','1','','1','0','4','4','1','163076');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('352','106','LM358','OPV 2-fach ','5','1','','0','0','46','4','1','142506');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('353','124','Sicherungshalter mit Kabel',NULL,'3','1','','0','0','0','4','1','501262');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('354','8','BC817','Transistor','10','2','','0','0','47','4','1','155961');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('355','32','Sicherungshalter freiliegend Printmontage',NULL,'10','2','','0','0','1','8','1','533920');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('356','50','LED SMD0805 Gelb',NULL,'15','2','','0','0','103','2','1','180107');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('357','50','LED SMD0805 Rot',NULL,'15','2','','0','0','103','2','1','180102');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('358','50','LED SMD0805 Grün','','15','2','','0','0','103','2','1','180104');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('359','70','Piepser SMD',NULL,'2','1','','0','0','38','4','1','710421');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('360','11','Quarz 16MHz SMD',NULL,'2','1','','0','0','37','7','1','156006');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('361','52','KIA78M05F','Spannungsregler 5V','6','2','','0','0','42','7','1','156945');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('362','11','Quarz 16MHz HC-49/US-SMD',NULL,'4','1','','0','0','37','7','1','445194');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('363','13','Elko Rad. 105°C 1uF 100V','','20','2','','0','0','59','4','1','445563');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('364','13','Elko Rad. 105°C 0,1uF 50V','','20','2','','0','0','88','4','1','445670');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('365','13','Elko Rad. 105°C 10uF 100V','','20','2','','0','0','59','4','1','445503');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('366','10','ATMega168-20AU','Mikrocontroller','2','1','Distrelec 645165','0','0','69','4','1','154885');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('367','62','Widerstand 10k 0,1W',NULL,'50','5','7.30Fr/100','0','0','50','4','2','715612');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('368','62','Widerstand 1k 0,1W',NULL,'50','5','7.30Fr/100','0','0','50','4','2','715588');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('369','52','L79L05ACZ','Spannungsregler -5V','2','1','0.85Fr','0','0','29','7','2','649203');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('370','107','VS-Kondensator 1uF X7R',NULL,'20','3','','0','0','70','4','1','450825');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('371','78','Drehknopf Pressbefestigung blau 12/4mm',NULL,'1','0','','0','0','0','4','1','717525');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('372','22','Printtaster SMD viereckiger Stössel','','1','1','','0','0','38','8','1','183387');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('373','22','Printtaster SMD rund langer Stössel','','1','1','','0','0','38','8','1','707570');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('374','22','Printtaster mit Stössel rund','','1','1','','0','0','1','8','1','700479');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('375','10','ATTiny45-20PU','Mikrocontroller','6','1','','0','0','4','4','2','645161');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('376','108','Lampenfassung E10 Lötstifte RM10','','4','1','','0','0','1','3','2','250608');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('377','108','Glühlampe E10 12V 200mA Weiss','','4','1','','0','0','0','3','2','253749');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('378','10','ATMega32-16PU','Mikrocontroller','1','1','','0','0','13','4','2','645110');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('379','10','ATMega88-20AU','Mikrocontroller','3','1','Distrelec 645163','0','0','69','4','1','154684');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('380','10','ATMega88-20PU','Mikrocontroller','3','1','','0','0','12','4','2','645164');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('381','68','LDO-Spannungsregler 5V 200mA SOT89',NULL,'5','2','','0','0','48','7','2','645489');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('382','89','IR2104SPBF','Mosfet-Treiber ','4','1','','0','0','46','4','2','646976');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('383','52','L7905CV','Spannungsregler -5V','3','1','','0','0','28','4','2','649206');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('384','52','TS78L05CT','Spannungsregler 5V','6','1','','0','0','29','7','2','662485');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('385','68','LD1117DT25','LDO-Spannungsregler 2,5V','3','1','','0','0','42','4','2','662705');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('386','16','Cermet-Poti 10k lin SMC-10-V mit Achse, stehend',NULL,'3','1','','0','0','24','4','2','740842');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('387','13','Elko radial 470uF 16V',NULL,'8','1','','0','0','87','1','2','801797');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('388','15','Kerko 100nF 50V',NULL,'50','10','Conrad 445544','0','0','71','4','2','823499');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('389','35','Linsenkopfschraube M3x10',NULL,'100','10','','0','0','0','4','1','815357');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('390','35','Linsenkopfschraube M3x6',NULL,'100','10','','0','0','0','4','1','815322');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('391','19','Printklemme 10Pol',NULL,'3','1','','0','0','87','8','2','140336');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('392','109','Flachbandkabel 16Pol',NULL,'3','1','','0','0','0','4','2','510816');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('393','47','LED Rot 3mm',NULL,'20','5','','0','0','90','2','2','632644');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('394','73','LCD 4x20 Grün/Gelb mit LED','','1','1','','0','0','0','4','2','661484');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('395','73','LCD 2x16 Grün/Gelb mit LED','','1','1','','0','0','0','4','2','661561');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('396','106','TS912IN','OPV','2','1','','0','0','4','4','2','663219');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('397','111','Widerstand 10k 0207',NULL,'82','10','','0','0','18','6','2','700037');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('398','13','Elko 100uF 16V',NULL,'20','2','','0','0','59','1','2','818511');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('399','100','KMZ-51','Magnetfeldsensor','1','1','','0','0','46','4','1','182826');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('400','106','LM324N','OPV','4','1','','0','0','7','4','1','151814');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('401','110','Nieten für Durchkontaktierungen 0.6mm','','1000','10','','0','0','0','9','1','551678');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('402','98','Heissleiter NTC 10R',NULL,'1','1','','0','0','62','4','1','500394');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('403','98','Heissleiter NTC 5R',NULL,'1','1','','0','0','62','4','1','500418');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('404','98','Heissleiter NTC 2.5R',NULL,'1','1','','0','0','62','4','1','500741');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('405','98','Heissleiter NTC 22R',NULL,'1','1','','0','0','62','4','1','468053');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('406','121','Lötzinn Bleihaltig 1mm 100g','','1','1','','0','0','0','9','1','812811');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('407','11','Resonator 16MHz KR16.00MCB5T',NULL,'5','1','','0','0','38','7','2','667593');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('408','22','Printtaster miniatur mit viereckigem Stössel','','5','1','','0','0','62','8','2','200865');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('409','26','Print-Kippschalter liegend on-on 1P',NULL,'4','1','','0','0','62','8','2','202149');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('410','26','Tastkappe 4x4mm',NULL,'5','1','','0','0','0','8','2','209255');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('411','109','Litze Silikongummi 1mm2 schwarz',NULL,'2','1','','0','0','0','9','2','514485');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('412','109','Litze Silikongummi 1mm2 rot',NULL,'2','1','','0','0','0','9','2','514486');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('413','109','Litze Silikongummi 1mm2 blau',NULL,'2','1','','0','0','0','9','2','514487');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('414','70','Piezo-Signalgeber KPEG-242',NULL,'2','1','','0','0','0','4','2','560212');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('415','70','Piezo-Signalgeber KPEG-260',NULL,'1','1','','0','0','0','4','2','560222');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('416','70','Summer KXG1205C',NULL,'2','1','','0','0','0','4','2','568047');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('417','100','BTS555','Leistungsschalter','2','1','','0','0','68','4','2','644945');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('418','10','ATTiny84V-10PU','Mikrocontroller','2','1','','0','0','7','4','2','666012');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('419','11','Resonator 16MHz CSTCE16',NULL,'5','1','','0','0','38','7','2','667649');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('420','64','Präzisionswiderstand 0,02R 5W','','3','2','Isabellenhütte SMT-R020-1.0','0','0','38','4','2','715201');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('421','64','Präzisionswiderstand 0,05R 5W','','5','1','Isabellenhütte SMT-R050-1.0','0','0','38','4','2','715204');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('422','13','Elko 330uF 25V',NULL,'8','1','','0','0','87','1','2','818566');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('423','127','Pinzette mit Umkehrfunktion',NULL,'1','1','','0','0','0','9','2','963616');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('424','91','Schaltregler Traco Power 3,3VDC 1A',NULL,'1','1','','0','0','28','7','2','361661');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('425','62','Widerstand 15R 0,1W',NULL,'50','10','','0','0','50','6','2','715544');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('427','62','Widerstand 4,7k 0,1W',NULL,'50','5','','0','0','50','6','1','446314');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('428','121','Lötzinn Bleihaltig 0.5mm 100g','','1','1','','0','0','0','9','1','812803');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('429','112','Metallschichtwiderstand 10R 1W',NULL,'10','2','','0','0','52','4','1','419320');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('430','14','Folienkondensator MSK2 15nF 100VDC',NULL,'20','2','','0','0','58','4','1','459856');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('431','16','Mini-Trimmer 1k stehend',NULL,'4','2','','0','0','24','4','1','430706');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('432','109','Flachbandkabel 10Pol',NULL,'2','1','','0','0','0','4','1','601922');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('433','122','Hartmetall Speerbohrer 1,3mm',NULL,'1','1','','0','0','0','9','1','814573');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('434','122','Hartmetall Speerbohrer 1,0mm',NULL,'1','1','','0','0','0','9','1','814561');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('435','127','Sortimentsbox 24 Fächer klar','','5','0','','0','0','0','9','1','523805');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('437','115','Kondensator Tantal 47uF 10VDC',NULL,'7','4','','0','0','54','4','2','818138');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('438','62','Widerstand 0R 0,1W',NULL,'30','5','','0','0','50','4','2','728141');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('439','116','Stiftleiste Molex SMD 6Pol 90°','','4','2','PicoBlade','0','0','55','10','2','116865');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('440','116','Stiftleiste Molex SMD 2Pol 90°','','2','2','PicoBlade','0','0','55','10','2','116861');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('441','116','Stiftleiste Molex SMD 3Pol 90°','','2','2','PicoBlade','0','0','55','10','2','116862');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('442','116','Stiftleiste Molex SMD 5Pol 90°','','2','2','PicoBlade','0','0','55','10','2','116864');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('443','116','Stiftleiste Molex SMD 5Pol',NULL,'2','2','PicoBlade','0','0','55','10','2','116836');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('444','116','Stiftleiste Molex SMD 4Pol',NULL,'2','2','PicoBlade','0','0','55','10','2','116835');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('445','116','Stiftleiste Molex SMD 3Pol',NULL,'4','2','PicoBlade','0','0','55','10','2','116834');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('446','116','Stiftleiste Molex SMD 2Pol',NULL,'4','2','PicoBlade','0','0','55','10','2','116833');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('447','116','Buchse Molex SMD 2Pol',NULL,'6','2','PicoBlade','0','0','0','10','2','116875');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('448','116','Buchse Molex SMD 3Pol',NULL,'6','2','PicoBlade','0','0','0','10','2','116876');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('449','116','Buchse Molex SMD 4Pol',NULL,'2','2','PicoBlade','0','0','0','10','2','116877');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('450','116','Buchse Molex SMD 5Pol',NULL,'4','2','PicoBlade','0','0','0','10','2','116878');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('451','116','Crimpkontakt Molex SMD',NULL,'30','5','PicoBlade','0','0','0','10','2','116904');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('452','105','P6SMBJ33A','Suppressordiode 33V 600W Unidirektional','5','1','','0','0','56','4','2','603810');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('453','59','Optokoppler 6N137',NULL,'4','1','','0','0','4','4','2','630083');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('454','59','Optokoppler HCPL-0600',NULL,'2','1','SMD-Variante von 6N137\r\n\r\nConrad 140053, gleicher Preis','0','0','46','4','2','633523');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('455','13','Elko Rad. 105°C 680uF 25VDC LowESR','','5','1','Conrad 442611 D13\r\nDistrelec 801847 D10,5','0','0','58','4','1','442611');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('456','117','TV TrickleSaver',NULL,'1','0','','0','0','0','0','2','841006');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('457','59','Optokoppler HCPL-0500',NULL,'4','1','','0','0','46','4','2','633522');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('458','105','SMAJ24A','Suppressordiode 24V 400W Unidirektional','4','1','','0','0','63','4','2','600159');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('459','62','Widerstand 10R 0,1W',NULL,'10','4','','0','0','50','6','2','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('460','62','Widerstand 180R 0,1W',NULL,'10','2','','0','0','50','6','2','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('461','62','Widerstand 390R 0,1W',NULL,'10','4','','0','0','50','6','2','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('462','62','Widerstand 220R 0,1W',NULL,'10','4','','0','0','50','6','2','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('463','62','Widerstand 15k 0,1W',NULL,'10','4','','0','0','50','6','2','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('464','62','Widerstand 2,2k 0,1W',NULL,'10','4','','0','0','50','6','2','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('465','62','Widerstand 47k 0,1W',NULL,'10','4','','0','0','50','6','2','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('466','62','Widerstand 18k',NULL,'10','4','','0','0','50','6','2','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('467','62','Widerstand 680R 0,1W',NULL,'10','4','','0','0','50','6','2','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('468','62','Widerstand 120R',NULL,'10','4','','0','0','50','6','2','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('469','15','Kerko 1uF 50V',NULL,'5','2','','0','0','70','4','2','838361');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('470','62','Widerstand 47R 0,1W',NULL,'10','4','','0','0','50','6','2','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('471','118','Platine für Brushless-Controller v14','','0','0','Preis geschätzt','0','0','0','4','5','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('473','62','Widerstand 2,8k 0,1X',NULL,'10','4','','0','0','50','0','2','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('474','35','Mutter M3 Stahl mit Sicherungsring',NULL,'100','5','','0','0','0','3','1','812808');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('475','59','Optokoppler HCPL-181',NULL,'5','2','','0','0','82','4','2','631697');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('476','68','LM1117','LDO-Spannungsregler 3,3V 0,8A','4','2','LM1117MP-3.3/NOPB','0','0','83','4','2','662670');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('477','74','ENC28J60/SO','Ethernet-Controller','2','1','','0','0','39','4','2','644353');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('478','115','Kondensator Tantal 10uF 6,3VDC',NULL,'6','2','','0','0','84','4','2','810770');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('479','119','LL4148','Diode 75V 150mA','10','2','','0','0','85','4','2','601496');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('480','118','Platine für Micro-SD-2-DIL Adapter v1','','4','1','Preis geschätzt','0','0','0','4','5','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('481','118','Platine für SPI-2-DIL Adapter v1','','0','1','Preis geschätzt','0','0','0','4','5','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('482','118','Platine für ENC28J60-Modul v2','','4','1','Preis geschätzt','0','0','0','4','5','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('483','118','Platine für SO8-2-DIL Adapter v1','','4','1','','0','0','0','4','5','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('484','118','Platine für UART-2-DIL Adapter v1','','4','1','Preis geschätzt','0','0','0','4','5','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('485','118','Platine für PowerJack-2-DIL Adapter v1','','4','1','Preis geschätzt','0','0','0','4','5','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('486','62','Widerstand 3,3k 0,1W',NULL,'10','2','','0','0','50','6','2','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('487','62','Widerstand 1,8k 0,1W',NULL,'10','2','','0','0','50','6','2','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('488','62','Widerstand 51R 0,2W',NULL,'10','2','','0','0','50','4','2','722941');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('489','62','Widerstand 2,4k 0,2W',NULL,'10','2','','0','0','50','4','2','722981');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('490','17','Induktivität SMCC 22uH 560mA','','3','1','','0','0','18','4','2','353578');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('491','62','Widerstand 150R 0,1W',NULL,'10','2','','0','0','50','6','2','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('492','15','Kerko 22pF 50V',NULL,'10','2','','0','0','71','12','2','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('493','11','Quarz 25MHz',NULL,'0','1','','0','0','37','3','0','');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('494','115','Kondensator Tantal 100uF 10VDC',NULL,'6','2','','0','0','86','4','2','818146');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('495','115','Kondensator Tantal 100uF 6,3V',NULL,'5','2','','0','0','54','4','2','812121');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('496','117','Evaluationsboard STM32 Value Line Discovery',NULL,'1','1','','0','0','0','0','2','667910');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('497','78','Drehknopf ohne Strich schwarz 20mm',NULL,'1','0','','0','0','0','4','2','268091');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('498','78','Deckel ohne Strich schwarz 20mm',NULL,'1','0','','0','0','0','4','2','268106');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('499','16','Cermet-Poti 1k lin SMC-10-V mit Achse, stehend',NULL,'1','1','','0','0','24','4','2','740836');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('500','16','Cermet-Poti 100k lin SMC-10-V mit Achse, stehend',NULL,'1','1','','0','0','24','4','2','740848');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('501','16','Cermet-Poti 5k lin SMC-10-V mit Achse, stehene',NULL,'1','1','','0','0','24','4','2','740840');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('502','104','Lüfter 12VDC 80mm','','1','0','','0','0','0','0','1','871001');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('503','120','Reinigungsspray Kontakt LR 400ml',NULL,'1','1','','0','0','0','0','1','055248');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('504','116','Buchse Molex SMD 6Pol',NULL,'4','2','','0','0','0','10','2','116879');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('505','128','7-Segnemt Anzeige 25mm CA Rot schwach',NULL,'6','1','Farbe ist sehr schwach! Besser nicht mehr kaufen.','0','0','62','4','1','160032');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('506','129','Miniaturtaster mit Hebel',NULL,'2','0','','0','0','62','4','1','704655');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('507','69','Steckernetzteil 5V 1A',NULL,'1','0','','0','0','0','0','1','514221');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('508','128','7-Segment Anzeige 25mm Superrot',NULL,'4','1','','0','0','62','4','2','661216');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('509','111','56R','Widerstand','50','5','','0','0','18','6','2','700010');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('510','92','Netzteil Traco +5/+15/-15VDC 30W',NULL,'1','1','TMP 30515C','0','0','0','0','2','363639');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('511','130','Kaltgerätebuchse mit Sicherung','','1','1','Anschlüsse: Faston 4.8 x 0.8 mm','0','0','0','4','2','110266');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('512','131','Laborbuchsen rot/schwarz 4mm',NULL,'1','1','','0','0','0','4','2','102901');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('513','97','Tischgehäuse 108x178x198','','1','1','','0','0','0','0','2','300284');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('514','132','Flachsteckhülse isoliert 4,8x0,8 1,5q','','10','4','','0','0','0','3','2','505134');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('515','132','Flachsteckhülse isoliert 6.3x0,8 1,5q','','10','4','','0','0','0','3','2','505135');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('516','132','Ringkabelschuh M4 isoliert 1,5mm2',NULL,'10','4','','0','0','0','3','2','505147');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('517','132','Ringkabelschuh M3 isoliert 1,5mm3',NULL,'10','4','','0','0','0','3','0','505146');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('518','132','Ringkabelschuh M5 isoliert 1,5mm4',NULL,'10','4','','0','0','0','3','0','505148');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('520','133','Wippschalter 250VAC, mit Lampe',NULL,'1','1','','0','0','0','4','2','202214');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('521','106','LM6172','OPV 2fach schnell','2','1','LM6172IN/NOPB\r\n100 MHz\r\n3000 V/us','0','0','4','0','0','641253');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('522','25','Print-Druckschalter 1P',NULL,'1','1','Kappe auch mitbestellen! 208404','0','0','62','4','2','208403');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('523','11','Resonator 20MHz',NULL,'4','1','','0','0','62','7','2','667553');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('524','16','Kohle-Potentiometer lin 50k klein',NULL,'1','1','','0','0','62','4','2','748172');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('525','16','Kohle-Potentiometer lin 10k kleio',NULL,'1','1','','0','0','62','4','2','748327');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('526','16','Kohle-Potentiometer lin 1k kleip',NULL,'1','1','','0','0','62','4','2','748325');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('530','48','L-59EGW','Bi-Color LED 5mm rot/grün','5','1','','0','0','91','2','2','253257');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('531','132','MPNYD 1-156','Rundsteckverbinder rot Stecker','30','5','','0','0','0','4','2','505219');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('532','132','FRFNYD 1-157','Rundsteckverbinder rot Hülse','30','5','','0','0','0','4','2','505224');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('533','105','P6KE20CA','Schutzdiode 20V 600W bidirektional','5','1','','0','0','57','4','2','604684');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('534','52','L78L09ACZ','Spannungsregler 9V','3','1','','0','0','29','3','2','649931');
INSERT INTO `parts` (`id`,`id_category`,`name`,`description`,`instock`,`mininstock`,`comment`,`obsolete`,`visible`,`id_footprint`,`id_storeloc`,`id_supplier`,`supplierpartnr`) VALUES ('535','128','SA39-11EWA','7-Segment LED 9,9mm superrot','5','1','','0','0','109','4','2','661192');
/*!40000 ALTER TABLE `parts` ENABLE KEYS */;


--
-- Create Table `pending_orders`
--

DROP TABLE IF EXISTS `pending_orders`;
CREATE TABLE `pending_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id` int(11) NOT NULL DEFAULT '0',
  `quantity` int(11) NOT NULL DEFAULT '0',
  `t` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `part_id` (`part_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Data for Table `pending_orders`
--

/*!40000 ALTER TABLE `pending_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `pending_orders` ENABLE KEYS */;


--
-- Create Table `pictures`
--

DROP TABLE IF EXISTS `pictures`;
CREATE TABLE `pictures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id` int(11) NOT NULL DEFAULT '0',
  `pict_fname` varchar(255) NOT NULL DEFAULT '',
  `pict_width` int(11) NOT NULL DEFAULT '0',
  `pict_height` int(11) NOT NULL DEFAULT '0',
  `pict_type` enum('P','T') NOT NULL DEFAULT 'P',
  `tn_obsolete` smallint(6) NOT NULL DEFAULT '0',
  `tn_t` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tn_pictid` int(11) NOT NULL DEFAULT '0',
  `pict_masterpict` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pict_type` (`pict_type`)
) ENGINE=MyISAM AUTO_INCREMENT=74 DEFAULT CHARSET=utf8;

--
-- Data for Table `pictures`
--

/*!40000 ALTER TABLE `pictures` DISABLE KEYS */;
/*!40000 ALTER TABLE `pictures` ENABLE KEYS */;


--
-- Create Table `preise`
--

DROP TABLE IF EXISTS `preise`;
CREATE TABLE `preise` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id` int(11) NOT NULL DEFAULT '0',
  `id_supplier` int(11) NOT NULL DEFAULT '0',
  `supplierpartnr` mediumtext NOT NULL,
  `manual_input` tinyint(1) NOT NULL DEFAULT '0',
  `price` decimal(6,2) NOT NULL DEFAULT '0.00',
  `last_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `part_id` (`part_id`),
  KEY `ma` (`manual_input`)
) ENGINE=MyISAM AUTO_INCREMENT=487 DEFAULT CHARSET=utf8;

--
-- Data for Table `preise`
--

/*!40000 ALTER TABLE `preise` DISABLE KEYS */;
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('1','132','0','','1','11.95','2011-01-02 23:14:06');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('40','130','0','','1','7.10','2011-01-04 00:11:14');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('3','122','0','','1','14.95','2011-01-02 23:16:08');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('4','129','0','','1','4.35','2011-01-02 23:17:06');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('5','152','0','','1','9.95','2011-01-02 23:19:02');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('206','170','0','','1','0.60','2012-02-03 12:56:45');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('7','94','0','','1','0.20','2011-01-02 23:20:09');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('8','95','0','','1','0.10','2011-01-02 23:20:40');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('9','87','0','','1','2.15','2011-01-02 23:21:19');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('10','116','0','','1','20.95','2011-01-02 23:24:24');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('11','117','0','','1','16.95','2011-01-02 23:24:53');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('12','110','0','','1','1.70','2011-01-03 23:09:57');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('13','175','0','','1','0.55','2011-01-03 23:33:29');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('14','25','0','','1','0.15','2011-01-03 23:37:07');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('15','22','0','','1','0.30','2011-01-03 23:37:53');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('16','24','0','','1','0.35','2011-01-03 23:38:18');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('17','176','0','','1','9.30','2011-01-03 23:47:45');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('18','17','0','','1','2.50','2011-01-03 23:51:29');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('19','12','0','','1','2.10','2011-01-03 23:52:05');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('20','13','0','','1','2.40','2011-01-03 23:52:24');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('21','18','0','','1','0.70','2011-01-03 23:54:07');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('22','177','0','','1','0.50','2011-01-03 23:54:59');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('23','120','0','','1','3.80','2011-01-03 23:56:13');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('24','178','0','','1','7.20','2011-01-03 23:57:30');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('25','179','0','','1','10.80','2011-01-03 23:58:50');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('26','180','0','','1','21.80','2011-01-03 23:59:32');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('27','181','0','','1','17.20','2011-01-04 00:00:22');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('28','75','0','','1','0.95','2011-01-04 00:01:22');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('29','80','0','','1','0.90','2011-01-04 00:01:47');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('30','78','0','','1','0.90','2011-01-04 00:02:36');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('31','16','0','','1','2.40','2011-01-04 00:04:24');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('32','14','0','','1','1.50','2011-01-04 00:05:19');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('33','20','0','','1','1.00','2011-01-04 00:05:53');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('34','174','0','','1','1.80','2011-01-04 00:06:27');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('35','182','0','','1','1.40','2011-01-04 00:07:22');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('36','183','0','','1','29.70','2011-01-04 00:08:00');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('37','134','0','','1','2.10','2011-01-04 00:08:43');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('38','133','0','','1','2.50','2011-01-04 00:09:09');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('39','121','0','','1','5.70','2011-01-04 00:09:43');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('41','184','0','','1','7.00','2011-01-04 00:14:20');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('42','185','0','','1','1.20','2011-01-04 00:16:07');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('43','136','0','','1','5.20','2011-01-04 00:16:29');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('44','131','0','','1','3.10','2011-01-04 00:16:55');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('45','79','0','','1','0.90','2011-01-04 00:17:22');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('46','76','0','','1','0.95','2011-01-04 00:17:47');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('47','81','0','','1','1.10','2011-01-04 00:18:28');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('48','186','0','','1','0.65','2011-01-04 00:20:06');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('49','187','0','','1','0.75','2011-01-04 00:20:48');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('50','90','0','','1','0.65','2011-01-04 00:22:11');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('51','91','0','','1','0.65','2011-01-04 00:22:47');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('52','92','0','','1','0.15','2011-01-04 00:23:23');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('53','188','0','','1','30.20','2011-01-04 00:24:11');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('54','189','0','','1','16.10','2011-01-04 00:24:51');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('55','190','0','','1','21.60','2011-01-04 00:26:29');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('56','191','0','','1','60.20','2011-01-04 00:27:17');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('57','192','0','','1','7.20','2011-01-04 00:28:53');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('58','193','0','','1','11.95','2011-01-04 00:30:46');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('59','194','0','','1','13.95','2011-01-04 00:32:42');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('60','10','0','','1','1.95','2011-01-04 00:33:56');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('61','135','0','','1','6.45','2011-01-04 00:34:34');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('62','8','0','','1','0.45','2011-01-04 00:35:20');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('63','195','0','','1','6.45','2011-01-04 00:36:27');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('64','200','0','','1','4.25','2011-01-04 12:38:08');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('65','148','0','','1','1.15','2011-01-04 12:39:06');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('66','1','0','','1','0.50','2011-01-04 12:41:08');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('67','2','0','','1','0.75','2011-01-04 12:41:46');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('68','150','0','','1','2.45','2011-01-04 12:43:21');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('69','201','0','','1','10.95','2011-01-04 12:45:04');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('70','5','0','','1','3.25','2011-01-04 12:45:36');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('71','6','0','','1','3.25','2011-01-04 12:45:43');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('72','7','0','','1','1.45','2011-01-04 12:46:23');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('73','84','0','','1','1.15','2011-01-04 12:48:14');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('74','83','0','','1','1.15','2011-01-04 12:48:56');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('75','86','0','','1','1.15','2011-01-04 12:49:16');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('76','85','0','','1','1.15','2011-01-04 12:49:45');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('77','202','0','','1','8.95','2011-01-04 12:52:07');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('78','103','0','','1','1.50','2011-01-04 12:54:01');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('79','203','0','','1','1.85','2011-01-04 12:54:50');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('80','204','0','','1','16.95','2011-01-04 12:56:36');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('81','205','0','','1','20.95','2011-01-04 12:57:51');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('82','206','0','','1','1.20','2011-01-04 12:58:42');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('83','156','0','','1','3.45','2011-01-04 12:59:13');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('84','153','0','','1','0.15','2011-01-04 12:59:39');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('85','154','0','','1','0.15','2011-01-04 13:00:01');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('86','155','0','','1','0.20','2011-01-04 13:00:21');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('87','163','0','','1','0.95','2011-01-04 13:01:29');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('88','164','0','','1','0.95','2011-01-04 13:01:57');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('89','165','0','','1','0.95','2011-01-04 13:02:21');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('90','167','0','','1','0.95','2011-01-04 13:03:05');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('91','166','0','','1','0.95','2011-01-04 13:03:28');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('92','159','0','','1','1.15','2011-01-04 13:04:26');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('93','160','0','','1','1.65','2011-01-04 13:05:46');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('94','158','0','','1','3.15','2011-01-04 13:06:15');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('95','157','0','','1','3.75','2011-01-04 13:06:44');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('96','162','0','','1','1.05','2011-01-04 13:07:17');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('97','26','0','','1','0.25','2011-01-04 13:07:51');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('98','23','0','','1','0.30','2011-01-04 13:08:11');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('99','28','0','','1','1.95','2011-01-04 13:08:57');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('100','207','0','','1','2.85','2011-01-04 13:09:50');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('101','29','0','','1','1.45','2011-01-04 13:10:16');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('102','30','0','','1','0.45','2011-01-04 13:10:47');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('103','208','0','','1','1.45','2011-01-04 13:11:49');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('104','209','0','','1','1.95','2011-01-04 13:13:43');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('105','210','0','','1','0.85','2011-01-04 13:15:30');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('106','108','0','','1','1.15','2011-01-04 13:15:56');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('107','169','0','','1','1.95','2011-01-04 13:16:16');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('108','168','0','','1','2.15','2011-01-04 13:16:35');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('109','173','0','','1','3.15','2011-01-04 13:17:04');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('110','149','0','','1','0.70','2011-01-04 13:17:29');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('111','212','0','','1','2.65','2011-01-04 13:19:01');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('112','211','0','','1','2.55','2011-01-04 13:19:11');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('113','213','0','','1','2.75','2011-01-04 13:19:21');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('114','214','0','','1','2.75','2011-01-04 13:20:04');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('115','216','0','','1','2.45','2011-01-04 13:21:39');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('116','215','0','','1','2.45','2011-01-04 13:21:47');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('117','101','0','','1','3.95','2011-01-04 13:22:33');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('118','70','0','','1','0.30','2011-01-04 13:23:11');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('119','217','0','','1','5.75','2011-01-04 13:24:14');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('120','137','0','','1','1.25','2011-01-04 13:24:41');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('121','11','0','','1','1.35','2011-01-04 13:25:05');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('122','218','0','','1','59.95','2011-01-04 13:26:05');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('123','219','0','','1','6.75','2011-01-04 13:27:39');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('124','220','0','','1','2.15','2011-01-04 13:29:07');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('125','221','0','','1','4.05','2011-01-04 13:30:16');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('126','27','0','','1','0.25','2011-01-04 13:30:56');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('127','222','0','','1','8.95','2011-01-04 13:31:55');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('128','223','0','','1','12.95','2011-01-04 13:33:25');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('129','224','0','','1','12.95','2011-01-04 13:34:14');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('130','225','0','','1','1.20','2011-01-04 15:10:09');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('131','226','0','','1','9.00','2011-01-04 15:13:16');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('132','227','0','','1','0.20','2011-01-04 15:14:54');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('133','228','0','','1','0.90','2011-01-04 15:16:14');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('134','229','0','','1','0.40','2011-01-04 15:18:13');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('135','230','0','','1','0.20','2011-01-04 15:37:02');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('136','231','0','','1','0.20','2011-01-04 15:37:09');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('137','232','0','','1','15.00','2011-01-04 15:41:29');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('138','233','0','','1','0.60','2011-01-04 15:48:44');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('139','234','0','','1','0.40','2011-01-04 15:52:10');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('140','235','0','','1','4.50','2011-01-04 15:56:13');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('141','236','0','','1','1.15','2011-01-04 17:09:22');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('142','237','0','','1','0.25','2011-01-04 17:10:33');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('143','238','0','','1','0.25','2011-01-04 17:11:36');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('144','239','0','','1','0.20','2011-01-05 13:23:20');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('145','240','0','','1','4.95','2011-01-06 11:11:07');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('146','241','0','','1','9.45','2011-01-06 11:11:52');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('147','242','0','','1','0.35','2011-01-06 11:14:09');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('148','243','0','','1','3.75','2011-01-06 11:28:07');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('149','93','0','','1','3.65','2011-01-06 13:25:42');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('150','145','0','','1','4.95','2011-01-06 13:29:03');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('151','257','0','','1','53.00','2011-05-08 21:28:45');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('152','265','0','','1','13.00','2011-05-08 21:30:55');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('153','336','0','','1','14.00','2011-05-08 21:32:03');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('154','280','0','','1','7.25','2011-05-08 21:33:27');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('155','273','0','','1','2.50','2011-05-08 21:34:52');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('194','379','0','','1','6.45','2012-02-02 22:24:51');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('157','407','0','','1','0.92','2012-02-02 14:39:14');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('195','455','0','','1','0.65','2012-02-02 22:33:35');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('159','388','0','','1','0.11','2012-02-02 14:40:04');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('160','469','0','','1','0.54','2012-02-02 14:40:28');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('161','314','0','','1','0.15','2012-02-02 14:40:51');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('162','348','0','','1','0.10','2012-02-02 14:41:16');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('163','358','0','','1','0.25','2012-02-02 14:41:32');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('164','357','0','','1','0.25','2012-02-02 14:41:40');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('165','454','0','','1','3.35','2012-02-02 14:41:58');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('166','438','0','','1','0.08','2012-02-02 14:42:23');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('167','367','0','','1','0.08','2012-02-02 14:42:54');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('168','425','0','','1','0.08','2012-02-02 14:43:29');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('169','468','0','','1','0.08','2012-02-02 14:44:16');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('170','459','0','','1','0.08','2012-02-02 14:44:29');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('171','463','0','','1','0.08','2012-02-02 14:44:37');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('172','460','0','','1','0.08','2012-02-02 14:44:44');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('173','464','0','','1','0.08','2012-02-02 14:44:52');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('174','466','0','','1','0.08','2012-02-02 14:45:02');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('175','368','0','','1','0.08','2012-02-02 14:45:11');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('176','461','0','','1','0.08','2012-02-02 14:45:18');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('177','427','0','','1','0.08','2012-02-02 14:45:29');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('178','465','0','','1','0.08','2012-02-02 14:45:36');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('179','470','0','','1','0.08','2012-02-02 14:45:45');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('180','467','0','','1','0.08','2012-02-02 14:45:56');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('181','420','0','','1','4.54','2012-02-02 14:46:13');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('182','381','0','','1','1.19','2012-02-02 14:46:29');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('183','347','0','','1','1.84','2012-02-02 14:47:10');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('184','382','0','','1','3.13','2012-02-02 14:47:41');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('185','370','0','','1','0.15','2012-02-02 14:48:04');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('186','437','0','','1','0.97','2012-02-02 14:48:19');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('187','446','0','','1','1.40','2012-02-02 14:48:32');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('188','442','0','','1','2.27','2012-02-02 14:48:46');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('189','439','0','','1','2.70','2012-02-02 14:48:59');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('190','471','0','','1','6.00','2012-02-02 14:49:19');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('191','452','0','','1','0.81','2012-02-02 14:49:51');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('192','340','0','','1','1.35','2012-02-02 14:50:04');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('201','458','0','','1','0.70','2012-02-03 12:36:56');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('196','301','0','','1','0.50','2012-02-03 12:24:47');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('197','303','0','','1','0.30','2012-02-03 12:24:59');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('198','304','0','','1','0.30','2012-02-03 12:25:17');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('199','302','0','','1','0.55','2012-02-03 12:25:32');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('200','343','0','','1','0.95','2012-02-03 12:26:49');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('202','311','0','','1','2.16','2012-02-03 12:41:16');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('203','345','0','','1','0.95','2012-02-03 12:42:18');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('204','344','0','','1','1.30','2012-02-03 12:44:24');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('205','319','0','','1','4.86','2012-02-03 12:47:44');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('207','308','0','','1','1.40','2012-02-03 13:06:18');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('208','127','0','','1','1.45','2012-02-03 13:09:53');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('209','312','0','','1','1.40','2012-02-03 13:11:26');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('210','293','0','','1','0.55','2012-02-03 13:11:43');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('211','294','0','','1','4.20','2012-02-03 13:12:27');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('212','334','0','','1','6.48','2012-02-03 13:16:29');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('213','324','0','','1','18.90','2012-02-03 13:16:54');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('214','310','0','','1','18.95','2012-02-03 13:18:59');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('215','292','0','','1','16.95','2012-02-03 13:19:30');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('216','290','0','','1','17.95','2012-02-03 13:19:43');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('217','352','0','','1','0.65','2012-02-03 13:20:04');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('218','349','0','','1','0.90','2012-02-03 13:20:18');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('219','400','0','','1','0.55','2012-02-03 13:20:32');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('220','396','0','','1','2.38','2012-02-03 13:21:17');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('221','119','0','','1','2.55','2012-02-03 13:22:16');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('222','126','0','','1','0.86','2012-02-03 13:23:50');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('223','118','0','','1','0.50','2012-02-03 13:25:30');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('224','295','0','','1','1.85','2012-02-03 13:25:50');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('225','417','0','','1','14.58','2012-02-03 13:26:36');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('226','399','0','','1','10.95','2012-02-03 13:28:15');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('227','366','0','','1','8.45','2012-02-03 13:29:44');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('228','378','0','','1','12.96','2012-02-03 13:31:01');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('229','375','0','','1','4.10','2012-02-03 13:32:00');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('230','418','0','','1','5.51','2012-02-03 13:32:13');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('231','362','0','','1','0.60','2012-02-03 13:32:51');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('232','291','0','','1','129.95','2012-02-03 13:33:53');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('233','380','0','','1','6.59','2012-02-03 13:34:11');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('234','331','0','','1','3.35','2012-02-03 13:34:29');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('235','332','0','','1','2.70','2012-02-03 13:34:45');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('236','248','0','','1','1.25','2012-02-03 13:35:02');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('237','249','0','','1','3.95','2012-02-03 13:35:19');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('238','247','0','','1','1.65','2012-02-03 13:35:33');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('239','447','0','','1','0.74','2012-02-03 13:35:53');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('240','448','0','','1','1.26','2012-02-03 13:36:12');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('241','449','0','','1','0.98','2012-02-03 13:36:28');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('242','450','0','','1','1.05','2012-02-03 13:36:42');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('243','99','0','','1','0.12','2012-02-03 13:37:17');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('244','386','0','','1','3.48','2012-02-03 13:37:35');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('245','276','0','','1','0.15','2012-02-03 13:37:51');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('246','277','0','','1','0.20','2012-02-03 13:38:01');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('247','278','0','','1','0.25','2012-02-03 13:38:16');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('248','451','0','','1','0.51','2012-02-03 13:38:42');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('249','171','0','','1','1.00','2012-02-03 13:39:05');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('250','371','0','','1','1.05','2012-02-03 13:39:17');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('251','246','0','','1','0.85','2012-02-03 13:40:09');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('252','252','0','','1','0.85','2012-02-03 13:40:20');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('253','245','0','','1','0.85','2012-02-03 13:40:32');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('254','282','0','','1','14.95','2012-02-03 13:40:46');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('255','309','0','','1','1.62','2012-02-03 13:41:10');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('256','398','0','','1','0.19','2012-02-03 13:41:36');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('257','315','0','','1','0.30','2012-02-03 13:41:51');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('258','422','0','','1','0.49','2012-02-03 13:42:06');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('259','364','0','','1','0.10','2012-02-03 13:42:21');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('260','365','0','','1','0.10','2012-02-03 13:42:36');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('261','363','0','','1','0.10','2012-02-03 13:42:49');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('262','387','0','','1','0.92','2012-02-03 13:43:04');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('263','432','0','','1','2.45','2012-02-03 13:43:21');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('264','392','0','','1','2.05','2012-02-03 13:43:36');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('265','267','0','','1','1.03','2012-02-03 13:43:48');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('266','296','0','','1','57.95','2012-02-03 13:53:06');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('267','436','0','','1','1.62','2012-02-03 13:59:46');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('268','472','0','','1','0.08','2012-02-06 13:10:56');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('269','473','0','','1','0.08','2012-02-06 13:11:07');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('271','475','0','','1','0.81','2012-02-08 20:13:20');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('272','486','0','','1','0.08','2012-02-10 21:09:19');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('273','487','0','','1','0.08','2012-02-10 21:10:02');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('274','476','0','','1','1.62','2012-02-10 21:18:21');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('275','323','0','','1','3.24','2012-02-10 21:19:08');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('276','480','0','','1','2.00','2012-02-10 21:19:38');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('277','479','0','','1','0.19','2012-02-10 21:20:15');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('278','485','0','','1','1.50','2012-02-10 21:22:00');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('279','481','0','','1','2.00','2012-02-10 21:24:03');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('280','484','0','','1','1.50','2012-02-10 21:25:14');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('281','488','0','','1','0.37','2012-02-10 21:33:19');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('282','489','0','','1','0.37','2012-02-10 21:35:10');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('283','490','0','','1','1.30','2012-02-10 21:41:09');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('284','491','0','','1','0.08','2012-02-10 21:47:30');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('285','494','0','','1','1.40','2012-02-10 22:15:22');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('286','495','0','','1','1.30','2012-02-10 22:18:31');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('287','477','0','','1','7.67','2012-02-10 22:24:08');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('288','478','0','','1','0.59','2012-02-10 22:24:27');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('289','482','0','','1','5.00','2012-02-10 22:24:44');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('290','496','0','','1','21.60','2012-02-11 13:54:16');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('291','285','0','','1','1.05','2012-02-11 14:15:20');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('292','497','0','','1','3.35','2012-02-11 14:16:56');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('293','498','0','','1','1.40','2012-02-11 14:18:08');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('294','499','0','','1','3.10','2012-02-11 14:24:13');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('295','500','0','','1','3.10','2012-02-11 14:24:46');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('296','501','0','','1','3.10','2012-02-11 14:25:12');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('297','435','0','','1','8.35','2012-02-11 14:33:17');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('298','502','0','','1','3.25','2012-02-11 14:34:51');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('299','503','0','','1','18.95','2012-02-11 14:36:28');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('300','69','0','','1','0.35','2012-02-11 14:42:52');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('302','419','0','','1','1.30','2012-02-11 14:44:02');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('303','325','0','','1','1.46','2012-02-11 14:48:37');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('304','328','0','','1','2.59','2012-02-11 14:49:04');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('305','326','0','','1','0.97','2012-02-11 14:49:22');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('306','329','0','','1','1.03','2012-02-11 14:49:39');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('307','408','0','','1','0.81','2012-02-11 14:50:09');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('308','409','0','','1','2.92','2012-02-11 14:50:41');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('309','410','0','','1','1.03','2012-02-11 14:51:18');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('310','353','0','','1','1.20','2012-02-11 14:51:33');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('311','389','0','','1','0.04','2012-02-11 14:52:32');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('312','474','0','','1','0.06','2012-02-11 14:54:27');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('313','274','0','','1','0.90','2012-02-11 14:55:32');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('314','272','0','','1','0.90','2012-02-11 14:55:52');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('315','300','0','','1','0.90','2012-02-11 15:08:03');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('316','402','0','','1','1.15','2012-02-11 15:09:07');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('317','404','0','','1','1.15','2012-02-11 15:09:34');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('318','405','0','','1','1.75','2012-02-11 15:09:50');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('319','403','0','','1','1.05','2012-02-11 15:10:07');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('320','283','0','','1','2.65','2012-02-11 15:12:14');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('321','284','0','','1','2.65','2012-02-11 15:12:47');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('322','297','0','','1','0.35','2012-02-11 15:15:36');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('323','316','0','','1','0.30','2012-02-11 15:15:48');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('324','266','0','','1','1.30','2012-02-11 15:16:01');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('325','250','0','','1','0.45','2012-02-11 15:16:13');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('326','251','0','','1','0.30','2012-02-11 15:16:24');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('327','430','0','','1','0.20','2012-02-11 15:16:36');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('328','298','0','','1','0.70','2012-02-11 15:16:46');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('329','275','0','','1','0.15','2012-02-11 15:17:03');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('330','286','0','','1','1.05','2012-02-11 15:17:18');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('331','377','0','','1','1.73','2012-02-11 15:17:37');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('332','434','0','','1','9.95','2012-02-11 15:17:51');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('333','433','0','','1','9.95','2012-02-11 15:18:05');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('334','115','0','','1','1.30','2012-02-11 15:19:13');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('335','128','0','','1','1.94','2012-02-11 15:20:16');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('336','114','0','','1','2.38','2012-02-11 15:21:39');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('337','123','0','','1','2.40','2012-02-11 15:22:24');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('338','199','0','','1','2.65','2012-02-11 15:23:02');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('339','111','0','','1','0.90','2012-02-11 15:23:31');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('340','279','0','','1','0.85','2012-02-11 15:23:58');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('341','104','0','','1','0.35','2012-02-11 15:24:16');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('342','253','0','','1','1.62','2012-02-11 15:24:39');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('343','198','0','','1','0.20','2012-02-11 15:24:59');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('344','264','0','','1','0.46','2012-02-11 15:25:21');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('345','262','0','','1','0.71','2012-02-11 15:25:37');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('346','259','0','','1','0.73','2012-02-11 15:25:49');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('347','330','0','','1','4.97','2012-02-11 15:26:03');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('348','376','0','','1','1.30','2012-02-11 15:26:27');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('349','196','0','','1','4.25','2012-02-11 15:26:42');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('350','504','0','','1','1.00','2012-02-15 14:31:38');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('351','385','0','','1','1.40','2012-02-17 00:14:49');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('352','197','0','','1','11.95','2012-02-17 00:14:49');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('353','394','0','','1','30.24','2012-02-17 00:14:49');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('354','395','0','','1','14.15','2012-02-17 00:14:49');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('355','256','0','','1','17.01','2012-02-17 00:14:49');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('356','255','0','','1','13.99','2012-02-17 00:14:49');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('357','327','0','','1','6.80','2012-02-17 00:14:49');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('358','287','0','','1','0.60','2012-02-17 00:14:49');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('373','431','0','','1','0.80','2012-02-17 00:22:50');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('374','429','0','','1','0.30','2012-02-17 00:22:50');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('375','270','0','','1','0.65','2012-02-17 00:22:50');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('376','268','0','','1','0.49','2012-02-17 00:22:50');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('377','271','0','','1','0.49','2012-02-17 00:22:50');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('378','269','0','','1','0.49','2012-02-17 00:22:50');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('379','406','0','','1','13.95','2012-02-17 00:22:50');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('380','428','0','','1','16.95','2012-02-17 00:22:50');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('381','411','0','','1','3.02','2012-02-17 00:22:50');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('382','412','0','','1','2.92','2012-02-17 00:22:50');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('383','413','0','','1','3.02','2012-02-17 00:22:50');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('384','356','0','','1','0.25','2012-02-17 00:22:50');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('385','393','0','','1','0.15','2012-02-17 00:22:50');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('386','53','0','','1','0.32','2012-02-17 00:22:50');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('387','391','0','','1','3.08','2012-02-17 00:25:27');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('388','421','0','','1','4.54','2012-02-17 00:25:27');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('389','423','0','','1','7.45','2012-02-17 00:25:27');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('390','415','0','','1','3.56','2012-02-17 00:25:27');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('391','414','0','','1','2.92','2012-02-17 00:25:27');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('392','359','0','','1','3.15','2012-02-17 00:25:27');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('393','258','0','','1','1.62','2012-02-17 00:25:27');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('394','337','0','','1','2.70','2012-02-17 00:25:27');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('395','457','0','','1','1.73','2012-02-17 00:25:27');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('396','453','0','','1','1.84','2012-02-17 00:25:27');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('397','261','0','','1','0.97','2012-02-17 00:25:27');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('398','305','0','','1','0.15','2012-02-17 00:25:27');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('399','350','0','','1','4.95','2012-02-17 00:25:27');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('400','64','0','','1','1.08','2012-02-17 00:25:27');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('401','361','0','','1','0.95','2012-02-17 00:28:31');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('402','317','0','','1','1.51','2012-02-17 00:28:31');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('403','313','0','','1','0.49','2012-02-17 00:28:31');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('404','355','0','','1','0.20','2012-02-17 00:28:31');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('405','322','0','','1','3.46','2012-02-17 00:28:31');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('406','147','0','','1','1.84','2012-02-17 00:28:31');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('407','146','0','','1','1.94','2012-02-17 00:28:31');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('408','424','0','','1','9.72','2012-02-17 00:28:31');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('409','320','0','','1','92.77','2012-02-17 00:28:31');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('410','281','0','','1','1.65','2012-02-17 00:28:31');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('411','82','0','','1','1.20','2012-02-17 00:28:31');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('412','360','0','','1','1.45','2012-02-17 00:28:31');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('413','372','0','','1','0.70','2012-02-17 00:28:31');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('414','373','0','','1','0.35','2012-02-17 00:28:31');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('415','374','0','','1','0.25','2012-02-17 00:28:31');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('416','289','0','','1','0.75','2012-02-17 00:28:31');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('417','288','0','','1','0.50','2012-02-17 00:28:31');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('486','456','0','','1','39.00','2012-09-19 19:47:01');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('419','354','0','','1','0.20','2012-02-17 00:30:46');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('420','342','0','','1','1.35','2012-02-17 00:30:46');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('421','341','0','','1','1.35','2012-02-17 00:30:46');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('422','416','0','','1','3.56','2012-02-17 00:30:46');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('423','443','0','','1','1.94','2012-02-17 00:30:46');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('424','444','0','','1','2.05','2012-02-17 00:30:46');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('425','441','0','','1','1.94','2012-02-17 00:30:46');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('426','445','0','','1','1.73','2012-02-17 00:30:46');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('427','440','0','','1','1.40','2012-02-17 00:30:46');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('428','260','0','','1','2.38','2012-02-17 00:30:46');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('429','384','0','','1','0.70','2012-02-17 00:30:46');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('430','369','0','','1','0.92','2012-02-17 00:30:46');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('431','383','0','','1','0.97','2012-02-17 00:30:46');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('444','263','0','','1','1.73','2012-02-17 00:32:55');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('445','254','0','','1','1.73','2012-02-17 00:32:55');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('446','307','0','','1','149.95','2012-02-17 00:32:55');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('447','306','0','','1','2.75','2012-02-17 00:32:55');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('448','346','0','','1','8.95','2012-02-17 00:32:55');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('449','397','0','','1','6.70','2012-02-17 00:32:55');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('450','299','0','','1','0.60','2012-02-17 00:32:55');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('451','318','0','','1','4.21','2012-02-17 00:32:55');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('452','333','0','','1','5.94','2012-02-17 00:32:55');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('453','339','0','','1','0.50','2012-02-17 00:32:55');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('454','338','0','','1','0.30','2012-02-17 00:32:55');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('455','335','0','','1','1.84','2012-02-17 00:32:55');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('456','390','0','','1','0.04','2012-02-18 12:13:20');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('457','401','0','','1','0.03','2012-02-18 12:14:24');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('458','505','0','','1','2.25','2012-03-28 13:02:41');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('459','506','0','','1','3.05','2012-03-28 13:05:55');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('460','507','0','','1','9.95','2012-03-28 13:06:51');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('461','508','0','','1','2.70','2012-03-28 15:49:56');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('462','510','0','','1','83.16','2012-04-15 21:12:05');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('463','511','0','','1','3.78','2012-04-15 21:15:17');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('464','512','0','','1','6.70','2012-04-15 21:18:53');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('465','513','0','','1','34.13','2012-04-15 21:27:57');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('466','514','0','','1','0.32','2012-04-15 21:51:45');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('467','515','0','','1','0.32','2012-04-15 21:52:51');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('468','516','0','','1','0.17','2012-04-15 21:55:30');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('469','517','0','','1','0.18','2012-04-15 21:56:06');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('470','518','0','','1','0.17','2012-04-15 21:56:52');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('472','520','0','','1','7.45','2012-04-15 22:14:26');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('473','521','0','','1','8.75','2012-04-15 22:17:08');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('474','522','0','','1','1.70','2012-04-17 19:57:01');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('475','523','0','','1','0.65','2012-04-17 20:00:55');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('476','524','0','','1','1.70','2012-04-17 20:02:27');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('477','525','0','','1','1.90','2012-04-17 20:03:00');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('478','526','0','','1','1.90','2012-04-17 20:03:20');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('479','530','0','','1','0.60','2012-07-06 15:32:46');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('480','531','0','','1','0.22','2012-07-06 15:35:36');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('481','532','0','','1','0.23','2012-07-06 15:36:31');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('482','533','0','','1','0.75','2012-07-06 15:37:55');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('483','534','0','','1','0.75','2012-07-06 15:40:01');
INSERT INTO `preise` (`id`,`part_id`,`id_supplier`,`supplierpartnr`,`manual_input`,`price`,`last_update`) VALUES ('484','535','0','','1','1.60','2012-07-06 15:43:31');
/*!40000 ALTER TABLE `preise` ENABLE KEYS */;


--
-- Create Table `storeloc`
--

DROP TABLE IF EXISTS `storeloc`;
CREATE TABLE `storeloc` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `parentnode` int(11) NOT NULL DEFAULT '0',
  `is_full` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

--
-- Data for Table `storeloc`
--

/*!40000 ALTER TABLE `storeloc` DISABLE KEYS */;
INSERT INTO `storeloc` (`id`,`name`,`parentnode`,`is_full`) VALUES ('1','Elkosortiment','0','0');
INSERT INTO `storeloc` (`id`,`name`,`parentnode`,`is_full`) VALUES ('2','LED-Sortiment','0','0');
INSERT INTO `storeloc` (`id`,`name`,`parentnode`,`is_full`) VALUES ('3','Schubladen Plastik','0','0');
INSERT INTO `storeloc` (`id`,`name`,`parentnode`,`is_full`) VALUES ('4','Schubladen Stahl','0','0');
INSERT INTO `storeloc` (`id`,`name`,`parentnode`,`is_full`) VALUES ('5','Zenerdioden-Sortiment','0','0');
INSERT INTO `storeloc` (`id`,`name`,`parentnode`,`is_full`) VALUES ('6','Widerstands-Sortiment','0','0');
INSERT INTO `storeloc` (`id`,`name`,`parentnode`,`is_full`) VALUES ('7','Weisse Schublade oben','0','0');
INSERT INTO `storeloc` (`id`,`name`,`parentnode`,`is_full`) VALUES ('8','Leiterplatten-Kleinteile','0','0');
INSERT INTO `storeloc` (`id`,`name`,`parentnode`,`is_full`) VALUES ('9','Werkstatt','0','0');
INSERT INTO `storeloc` (`id`,`name`,`parentnode`,`is_full`) VALUES ('10','Stiftleisten SMD','0','0');
INSERT INTO `storeloc` (`id`,`name`,`parentnode`,`is_full`) VALUES ('12','Kerko-Sortiment','0','0');
/*!40000 ALTER TABLE `storeloc` ENABLE KEYS */;


--
-- Create Table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

--
-- Data for Table `suppliers`
--

/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` (`id`,`name`) VALUES ('1','Conrad');
INSERT INTO `suppliers` (`id`,`name`) VALUES ('2','Distrelec');
INSERT INTO `suppliers` (`id`,`name`) VALUES ('3','Reichelt');
INSERT INTO `suppliers` (`id`,`name`) VALUES ('4','PCB-Pool');
INSERT INTO `suppliers` (`id`,`name`) VALUES ('5','Platinen-Sammler');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;

SET FOREIGN_KEY_CHECKS=1;
-- EOB

