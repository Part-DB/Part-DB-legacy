INSERT INTO `part-db`.`internal` (`keyName`, `keyValue`) VALUES ('dbSubversion', '0');
INSERT INTO `part-db`.`internal` (`keyName`, `keyValue`) VALUES ('dbRevision', '0');
UPDATE `part-db`.`internal` SET `keyValue` = '1' WHERE `keyName` = 'dbSubversion';
