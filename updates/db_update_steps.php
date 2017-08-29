<?php
/*
    part-db version 0.1
    Copyright (C) 2005 Christoph Lechner
    http://www.cl-projects.de/

    part-db version 0.2+
    Copyright (C) 2009 K. Jacobs and others (see authors.php)
    http://code.google.com/p/part-db/

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
*/

include_once(BASE.'/updates/db_migration_functions.php');

/*
 * DATABASE UPDATE STEPS:
 *
 * This file contains all steps to update the database step by step to the latest version.
 *
 * To add a new step, you have to:
 *      - increment the constant "LATEST_DB_VERSION" by one
 *      - add a new "case" element at the end of the function below.
 *          -> this new "case" must have the number "LATEST_DB_VERSION - 1"!
 */

define('LATEST_DB_VERSION', 19);  // <-- increment here

/*
 * Get update steps
 *
 * This function will be executed one time for every update step until we have the latest version.
 *
 * Arguments:
 *      $current_version:       the current version
 *
 * Return:
 *      an array of SQL queries which we have to execute
 */
function get_db_update_steps($current_version)
{
    $updateSteps = array();

    switch($current_version)
    {
        case 0:
            // there are no tables (empty database), so we will create them.
            // Please note: We will directly create the database version 13, not the first version!

            $updateSteps[] = "CREATE TABLE IF NOT EXISTS `internal` (
                `keyName` char(30) CHARACTER SET ascii NOT NULL,
                `keyValue` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
                UNIQUE KEY `keyName` (`keyName`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

            // insert internal records
            $updateSteps[] = "INSERT INTO internal (keyName, keyValue) VALUES ('dbVersion', '13');"; // <-- We will create the version 13

            $updateSteps[] = "CREATE TABLE IF NOT EXISTS `attachements` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  `class_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  `element_id` int(11) NOT NULL,
                  `type_id` int(11) NOT NULL,
                  `filename` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  `show_in_table` tinyint(1) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`),
                  KEY `attachements_class_name_k` (`class_name`),
                  KEY `attachements_element_id_k` (`element_id`),
                  KEY `attachements_type_id_fk` (`type_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

            $updateSteps[] = "CREATE TABLE IF NOT EXISTS `attachement_types` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  `parent_id` int(11) DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  KEY `attachement_types_parent_id_k` (`parent_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

            // create attachement types "Bilder" and "Datenblätter"
            $updateSteps[] = "INSERT INTO `attachement_types` (name, parent_id) VALUES ('Bilder', NULL)";
            $updateSteps[] = "INSERT INTO `attachement_types` (name, parent_id) VALUES ('Datenblätter', NULL)";

            $updateSteps[] = "CREATE TABLE IF NOT EXISTS `categories` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  `parent_id` int(11) DEFAULT NULL,
                  `disable_footprints` tinyint(1) NOT NULL DEFAULT '0',
                  `disable_manufacturers` tinyint(1) NOT NULL DEFAULT '0',
                  `disable_autodatasheets` tinyint(1) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`),
                  KEY `categories_parent_id_k` (`parent_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

            $updateSteps[] = "CREATE TABLE IF NOT EXISTS `devices` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  `parent_id` int(11) DEFAULT NULL,
                  `order_quantity` int(11) NOT NULL DEFAULT '0',
                  `order_only_missing_parts` tinyint(1) NOT NULL DEFAULT '0',
                  `datetime_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `devices_parent_id_k` (`parent_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

            $updateSteps[] = "CREATE TABLE IF NOT EXISTS `device_parts` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `id_part` int(11) NOT NULL DEFAULT '0',
                  `id_device` int(11) NOT NULL DEFAULT '0',
                  `quantity` int(11) NOT NULL DEFAULT '0',
                  `mountnames` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `device_parts_combination_uk` (`id_part`,`id_device`),
                  KEY `device_parts_id_part_k` (`id_part`),
                  KEY `device_parts_id_device_k` (`id_device`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

            $updateSteps[] = "CREATE TABLE IF NOT EXISTS `footprints` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  `filename` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  `parent_id` int(11) DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  KEY `footprints_parent_id_k` (`parent_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

            $updateSteps[] = "CREATE TABLE IF NOT EXISTS `manufacturers` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  `parent_id` int(11) DEFAULT NULL,
                  `address` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  `phone_number` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  `fax_number` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  `email_address` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  `website` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  `datetime_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `manufacturers_parent_id_k` (`parent_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

            $updateSteps[] = "CREATE TABLE IF NOT EXISTS `orderdetails` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `part_id` int(11) NOT NULL,
                  `id_supplier` int(11) NOT NULL DEFAULT '0',
                  `supplierpartnr` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  `obsolete` tinyint(1) DEFAULT '0',
                  `datetime_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `orderdetails_part_id_k` (`part_id`),
                  KEY `orderdetails_id_supplier_k` (`id_supplier`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

            $updateSteps[] = "CREATE TABLE IF NOT EXISTS `parts` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `id_category` int(11) NOT NULL DEFAULT '0',
                  `name` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  `description` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  `instock` int(11) NOT NULL DEFAULT '0',
                  `mininstock` int(11) NOT NULL DEFAULT '0',
                  `comment` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
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
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

            $updateSteps[] = "CREATE TABLE IF NOT EXISTS `pricedetails` (
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
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

            $updateSteps[] = "CREATE TABLE IF NOT EXISTS `storelocations` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  `parent_id` int(11) DEFAULT NULL,
                  `is_full` tinyint(1) NOT NULL DEFAULT '0',
                  `datetime_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `storelocations_parent_id_k` (`parent_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

            $updateSteps[] = "CREATE TABLE IF NOT EXISTS `suppliers` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  `parent_id` int(11) DEFAULT NULL,
                  `address` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  `phone_number` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  `fax_number` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  `email_address` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  `website` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                  `datetime_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `suppliers_parent_id_k` (`parent_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";


            $updateSteps[] = "ALTER TABLE `attachements`
                  ADD CONSTRAINT `attachements_type_id_fk` FOREIGN KEY (`type_id`) REFERENCES `attachement_types` (`id`)";

            $updateSteps[] = "ALTER TABLE `attachement_types`
                  ADD CONSTRAINT `attachement_types_parent_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `attachement_types` (`id`)";

            $updateSteps[] = "ALTER TABLE `categories`
                  ADD CONSTRAINT `categories_parent_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`)";

            $updateSteps[] = "ALTER TABLE `devices`
                  ADD CONSTRAINT `devices_parent_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `devices` (`id`)";

            $updateSteps[] = "ALTER TABLE `footprints`
                  ADD CONSTRAINT `footprints_parent_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `footprints` (`id`)";

            $updateSteps[] = "ALTER TABLE `manufacturers`
                  ADD CONSTRAINT `manufacturers_parent_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `manufacturers` (`id`)";

            $updateSteps[] = "ALTER TABLE `parts`
                  ADD CONSTRAINT `parts_id_footprint_fk` FOREIGN KEY (`id_footprint`) REFERENCES `footprints` (`id`),
                  ADD CONSTRAINT `parts_id_manufacturer_fk` FOREIGN KEY (`id_manufacturer`) REFERENCES `manufacturers` (`id`),
                  ADD CONSTRAINT `parts_id_storelocation_fk` FOREIGN KEY (`id_storelocation`) REFERENCES `storelocations` (`id`),
                  ADD CONSTRAINT `parts_order_orderdetails_id_fk` FOREIGN KEY (`order_orderdetails_id`) REFERENCES `orderdetails` (`id`)";

            $updateSteps[] = "ALTER TABLE `storelocations`
                  ADD CONSTRAINT `storelocations_parent_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `storelocations` (`id`)";

            $updateSteps[] = "ALTER TABLE `suppliers`
                  ADD CONSTRAINT `suppliers_parent_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `suppliers` (`id`)";

            break;

        case 1:
            $updateSteps[] = NULL; // nothing to do (steps removed)
            break;

        case 2:
            $updateSteps[] = "ALTER TABLE  `part_device` ADD  `mountname` mediumtext NOT NULL AFTER  `quantity` ;";
            break;

        case 3:
            $updateSteps[] = "ALTER TABLE  `storeloc` ADD  `parentnode` int(11) NOT NULL default '0' AFTER  `name` ;";
            $updateSteps[] = "ALTER TABLE  `storeloc` ADD  `is_full` boolean NOT NULL default false AFTER `parentnode` ;";
            break;

        case 4:
            $updateSteps[] = "ALTER TABLE  `part_device` DROP PRIMARY KEY;";
            break;

        case 5:
            $updateSteps[] = "ALTER TABLE  `devices` ADD  `parentnode` int(11) NOT NULL default '0' AFTER  `name` ;";
            break;

        case 6:
            $updateSteps[] = "ALTER TABLE  footprints ADD  parentnode INT(11) NOT NULL default '0' AFTER name;";
            break;

        case 7:
            $updateSteps[] = "ALTER TABLE  parts  ADD  obsolete boolean NOT NULL default false AFTER comment;";
            break;

        case 8:
            footprint_migration_8($updateSteps);
            break;

        case 9:
            $updateSteps[] = "ALTER TABLE `parts` ADD `description` mediumtext AFTER `name`;";
            $updateSteps[] = "ALTER TABLE `parts` ADD `visible`     boolean NOT NULL AFTER `obsolete`;";
            break;

        case 10:
            $updateSteps[] = "ALTER TABLE `preise` CHANGE COLUMN `t` `last_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';";
            $updateSteps[] = "ALTER TABLE `preise` CHANGE COLUMN `ma` `manual_input` tinyint(1) NOT NULL DEFAULT '0';";
            $updateSteps[] = "ALTER TABLE `preise` CHANGE COLUMN `preis` `price` decimal(6,2) NOT NULL DEFAULT '0.00';";
            $updateSteps[] = "ALTER TABLE `preise` ADD `id_supplier` int(11) NOT NULL DEFAULT '0' AFTER `part_id`;";
            $updateSteps[] = "ALTER TABLE `preise` ADD `supplierpartnr` mediumtext NOT NULL AFTER `id_supplier`;";
            break;

        case 11:
            $updateSteps[] = "ALTER TABLE `footprints` ADD `filename` mediumtext AFTER `name`;";
            $updateSteps[] = "UPDATE footprints SET filename = name;";
            footprint_migration_11($updateSteps);
            break;

        case 12:

            /*****************************************************************************************
             **                                                                                      **
             ** Update to Database version 13 (for Part-DB Verison 0.3.0):                           **
             **      - Change to the MySQL Engine "InnoDB" because of the support for transactions   **
             **      - Make a lot of changes for the new object-oriented design of Part-DB           **
             **      - Add new keys                                                                  **
             **      - Make all existing keys unique over the whole database (change names)          **
             **      - Use now foreign keys                                                          **
             **      - Remove unused tables/columns                                                  **
             **                                                                                      **
             ** ATTENTION: IT IS STRONGLY RECOMMENDED TO MAKE A DATABASE BACKUP BEFORE UPDATING!!!!  **
             **                                                                                      **
             ** Please note:                                                                         **
             ** This is a huge update, so the risk of a failure is higher than usual.                **
             ** Because of this, automatic database updates are temporary disabled for this one.     **
             ** The user will get a warning and a recommendation to make a database backup.          **
             **                                                                                      **
             ** January 2013, kami89                                                                 **
             **                                                                                      **
             ******************************************************************************************/

            // drop table "pending_orders" (until now, that table is not used - we will create a new one later when we need it)
            $updateSteps[] = "DROP TABLE `pending_orders`";

            // Change all tables to InnoDB (for the support of transactions) and UTF-8
            $updateSteps[] = "ALTER TABLE `categories` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";
            $updateSteps[] = "ALTER TABLE `datasheets` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";
            $updateSteps[] = "ALTER TABLE `devices` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";
            $updateSteps[] = "ALTER TABLE `footprints` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";
            $updateSteps[] = "ALTER TABLE `internal` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
            $updateSteps[] = "ALTER TABLE `parts` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";
            $updateSteps[] = "ALTER TABLE `part_device` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";
            $updateSteps[] = "ALTER TABLE `pictures` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";
            $updateSteps[] = "ALTER TABLE `preise` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";
            $updateSteps[] = "ALTER TABLE `storeloc` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";
            $updateSteps[] = "ALTER TABLE `suppliers` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";

            // Fix a charset bug of an older version of Part-DB (see german post on uC.net: http://www.mikrocontroller.net/topic/269289#3147877)
            $charset_search_replace = array('Ã¤' => 'ä', 'Ã„' => 'Ä', 'Ã¶' => 'ö', 'Ã–' => 'Ö', 'Ã¼' => 'ü', 'Ãœ' => 'Ü',
                'Â°' => '°', 'Âµ' => 'µ', 'â‚¬' => '€', 'â€°' => '‰', 'Ã¨' => 'è', 'Ãˆ' => 'È',
                'Ã©' => 'é', 'Ã‰' => 'É', 'Ã' => 'à', 'Ã€' => 'À', 'Â£' => '£', 'Ã¸' => 'ø');
            foreach ($charset_search_replace as $search => $replace)
            {
                $updateSteps[] = "UPDATE `categories` SET name = REPLACE(name, '".$search."', '".$replace."')";
                $updateSteps[] = "UPDATE `datasheets` SET datasheeturl = REPLACE(datasheeturl, '".$search."', '".$replace."')";
                $updateSteps[] = "UPDATE `devices` SET name = REPLACE(name, '".$search."', '".$replace."')";
                $updateSteps[] = "UPDATE `footprints` SET name = REPLACE(name, '".$search."', '".$replace."')";
                $updateSteps[] = "UPDATE `footprints` SET filename = REPLACE(filename, '".$search."', '".$replace."')";
                $updateSteps[] = "UPDATE `parts` SET name = REPLACE(name, '".$search."', '".$replace."')";
                $updateSteps[] = "UPDATE `parts` SET description = REPLACE(description, '".$search."', '".$replace."')";
                $updateSteps[] = "UPDATE `parts` SET comment = REPLACE(comment, '".$search."', '".$replace."')";
                $updateSteps[] = "UPDATE `parts` SET supplierpartnr = REPLACE(supplierpartnr, '".$search."', '".$replace."')";
                $updateSteps[] = "UPDATE `part_device` SET mountname = REPLACE(mountname, '".$search."', '".$replace."')";
                $updateSteps[] = "UPDATE `pictures` SET pict_fname = REPLACE(pict_fname, '".$search."', '".$replace."')";
                $updateSteps[] = "UPDATE `storeloc` SET name = REPLACE(name, '".$search."', '".$replace."')";
                $updateSteps[] = "UPDATE `suppliers` SET name = REPLACE(name, '".$search."', '".$replace."')";
            }

            // Fix broken foreign keys if there are some (this is quite important, because later we use
            // the foreign keys of MySQL and if there are broken records in the table, the update will fail!
            $updateSteps[] = "UPDATE `parts` SET id_footprint = '0' WHERE id_footprint NOT IN (SELECT id FROM `footprints`)";
            $updateSteps[] = "UPDATE `parts` SET id_storeloc = '0' WHERE id_storeloc NOT IN (SELECT id FROM `storeloc`)";
            $updateSteps[] = "UPDATE `parts` SET id_supplier = '0' WHERE id_supplier NOT IN (SELECT id FROM `suppliers`)";
            $updateSteps[] = "INSERT IGNORE INTO `categories` (name) VALUES ('Unsortiert')";
            $updateSteps[] = "UPDATE `parts` SET id_category = (SELECT `id` FROM `categories` WHERE name='Unsortiert') ".
                "WHERE id_category NOT IN (SELECT id FROM `categories`)";
            $updateSteps[] = "DELETE FROM categories WHERE id NOT IN (SELECT id_category FROM parts) AND name='Unsortiert'";
            $updateSteps[] = "UPDATE `categories` ".
                "LEFT JOIN `categories` AS categories2 ON categories2.id = categories.parentnode ".
                "SET categories.parentnode = 0 WHERE categories2.id IS NULL";
            $updateSteps[] = "UPDATE `devices` ".
                "LEFT JOIN `devices` AS devices2 ON devices2.id = devices.parentnode ".
                "SET devices.parentnode = 0 WHERE devices2.id IS NULL";
            $updateSteps[] = "UPDATE `footprints` ".
                "LEFT JOIN `footprints` AS footprints2 ON footprints2.id = footprints.parentnode ".
                "SET footprints.parentnode = 0 WHERE footprints2.id IS NULL";
            $updateSteps[] = "UPDATE `storeloc` ".
                "LEFT JOIN `storeloc` AS storeloc2 ON storeloc2.id = storeloc.parentnode ".
                "SET storeloc.parentnode = 0 WHERE storeloc2.id IS NULL";
            $updateSteps[] = "DELETE FROM `datasheets` WHERE part_id NOT IN (SELECT id FROM `parts`)";
            $updateSteps[] = "DELETE FROM `preise` WHERE part_id NOT IN (SELECT id FROM `parts`)";
            $updateSteps[] = "DELETE FROM `pictures` WHERE part_id NOT IN (SELECT id FROM `parts`)";
            $updateSteps[] = "DELETE FROM `part_device` WHERE id_part NOT IN (SELECT id FROM `parts`)";
            $updateSteps[] = "DELETE FROM `part_device` WHERE id_device NOT IN (SELECT id FROM `devices`)";

            // table "suppliers"
            $updateSteps[] = "ALTER TABLE `suppliers` MODIFY `name` TINYTEXT NOT NULL";
            $updateSteps[] = "ALTER TABLE `suppliers` ADD `parent_id` int(11) DEFAULT NULL AFTER `name`";
            $updateSteps[] = "CREATE INDEX suppliers_parent_id_k ON suppliers(parent_id)";
            $updateSteps[] = "ALTER TABLE `suppliers` ADD `address` MEDIUMTEXT NOT NULL";
            $updateSteps[] = "ALTER TABLE `suppliers` ADD `phone_number` TINYTEXT NOT NULL";
            $updateSteps[] = "ALTER TABLE `suppliers` ADD `fax_number` TINYTEXT NOT NULL";
            $updateSteps[] = "ALTER TABLE `suppliers` ADD `email_address` TINYTEXT NOT NULL";
            $updateSteps[] = "ALTER TABLE `suppliers` ADD `website` TINYTEXT NOT NULL";
            $updateSteps[] = "ALTER TABLE `suppliers` ADD `datetime_added` TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
            $updateSteps[] = "UPDATE `suppliers` SET datetime_added=CURRENT_TIMESTAMP";
            // In the new table "orderdetails", it's not allowed to have orderdetails without a supplier!
            // But until now, it was allowed to have parts without a supplier. So maybe there are now parts
            // with a price or supplierpartnumber, but without a supplier. For these parts we create a dummy
            // supplier and change the supplier of these illegal parts to this new dummy supplier.
            $updateSteps[] = "INSERT IGNORE INTO `suppliers` (name, parent_id) VALUES ('Unbekannt', NULL)";
            $updateSteps[] = "UPDATE `parts` SET supplierpartnr='' WHERE supplierpartnr='0'"; // "0" is not a supplier part-nr! ;-)
            $updateSteps[] = "UPDATE `parts` LEFT JOIN `preise` ON parts.id = preise.part_id ".
                "SET parts.id_supplier = (SELECT `id` FROM `suppliers` WHERE name='Unbekannt') ".
                "WHERE (parts.id_supplier = 0) AND ((preise.price > 0) OR (parts.supplierpartnr != ''))";
            $updateSteps[] = "DELETE FROM suppliers WHERE id NOT IN (SELECT id_supplier FROM parts) AND name='Unbekannt'";

            // table "categories"
            $updateSteps[] = "ALTER TABLE `categories` MODIFY `name` TINYTEXT NOT NULL";
            $updateSteps[] = "ALTER TABLE `categories` DROP INDEX `parentnode`";
            $updateSteps[] = "ALTER TABLE `categories` CHANGE `parentnode` `parent_id` int(11) DEFAULT NULL";
            $updateSteps[] = "CREATE INDEX categories_parent_id_k ON categories(parent_id)";
            $updateSteps[] = "ALTER TABLE `categories` ADD `disable_footprints` BOOLEAN NOT NULL DEFAULT FALSE";
            $updateSteps[] = "ALTER TABLE `categories` ADD `disable_manufacturers` BOOLEAN NOT NULL DEFAULT FALSE";
            $updateSteps[] = "ALTER TABLE `categories` ADD `disable_autodatasheets` BOOLEAN NOT NULL DEFAULT FALSE";
            $updateSteps[] = "UPDATE `categories` SET parent_id=NULL WHERE parent_id=0";

            // table "devices"
            $updateSteps[] = "ALTER TABLE `devices` MODIFY `name` TINYTEXT NOT NULL";
            $updateSteps[] = "ALTER TABLE `devices` CHANGE `parentnode` `parent_id` int(11) DEFAULT NULL";
            $updateSteps[] = "CREATE INDEX devices_parent_id_k ON devices(parent_id)";
            $updateSteps[] = "ALTER TABLE `devices` ADD `order_quantity` INT(11) NOT NULL DEFAULT '0'";
            $updateSteps[] = "ALTER TABLE `devices` ADD `order_only_missing_parts` BOOLEAN NOT NULL DEFAULT false";
            $updateSteps[] = "ALTER TABLE `devices` ADD `datetime_added` TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
            $updateSteps[] = "UPDATE `devices` SET datetime_added=CURRENT_TIMESTAMP";
            $updateSteps[] = "UPDATE `devices` SET parent_id=NULL WHERE parent_id=0";

            // table "footprints"
            $updateSteps[] = "ALTER TABLE `footprints` MODIFY `name` TINYTEXT NOT NULL";
            $updateSteps[] = "ALTER TABLE `footprints` CHANGE `parentnode` `parent_id` int(11) DEFAULT NULL";
            $updateSteps[] = "CREATE INDEX footprints_parent_id_k ON footprints(parent_id)";
            $updateSteps[] = "ALTER TABLE `footprints` MODIFY `filename` mediumtext NOT NULL";
            $updateSteps[] = "UPDATE `footprints` SET `filename` = replace(`filename`,'tools/footprints/','%BASE%/img/footprints/')";
            $updateSteps[] = "UPDATE `footprints` SET parent_id=NULL WHERE parent_id=0";

            // table "storeloc" will now be renamed to "storelocations"
            $updateSteps[] = "ALTER TABLE `storeloc` RENAME `storelocations`";
            $updateSteps[] = "ALTER TABLE `storelocations` MODIFY `id` int(11) AUTO_INCREMENT NOT NULL";
            $updateSteps[] = "ALTER TABLE `storelocations` MODIFY `name` TINYTEXT NOT NULL";
            $updateSteps[] = "ALTER TABLE `storelocations` CHANGE `parentnode` `parent_id` int(11) DEFAULT NULL";
            $updateSteps[] = "CREATE INDEX storelocations_parent_id_k ON storelocations(parent_id)";
            $updateSteps[] = "ALTER TABLE `storelocations` ADD `datetime_added` TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
            $updateSteps[] = "UPDATE `storelocations` SET datetime_added=CURRENT_TIMESTAMP";
            $updateSteps[] = "UPDATE `storelocations` SET parent_id=NULL WHERE parent_id=0";

            // table "preise" will now be renamed to "orderdetails"
            $updateSteps[] = "ALTER TABLE `preise` RENAME `orderdetails`";
            $updateSteps[] = "DROP INDEX `ma` ON `orderdetails`";
            $updateSteps[] = "DROP INDEX `part_id` ON `orderdetails`";
            $updateSteps[] = "ALTER TABLE `orderdetails` MODIFY `supplierpartnr` TINYTEXT NOT NULL";
            $updateSteps[] = "ALTER TABLE `orderdetails` MODIFY `price` DECIMAL(6,2) DEFAULT NULL";
            $updateSteps[] = "ALTER TABLE `orderdetails` MODIFY `part_id` INT(11) NOT NULL";
            $updateSteps[] = "ALTER TABLE `orderdetails` ADD `obsolete` BOOL DEFAULT false";
            $updateSteps[] = "ALTER TABLE `orderdetails` ADD `datetime_added` TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
            $updateSteps[] = "CREATE INDEX orderdetails_part_id_k ON orderdetails(part_id)";
            $updateSteps[] = "CREATE INDEX orderdetails_id_supplier_k ON orderdetails(id_supplier)";
            $updateSteps[] = "UPDATE `orderdetails` ".
                "LEFT JOIN `parts` ON parts.id = orderdetails.part_id ".
                "SET orderdetails.id_supplier=parts.id_supplier, ".
                "orderdetails.supplierpartnr=parts.supplierpartnr, ".
                "orderdetails.obsolete=parts.obsolete ".
                "WHERE parts.id IS NOT NULL";
            $updateSteps[] = "INSERT IGNORE INTO `orderdetails` ".
                "(`part_id`, `id_supplier`, `supplierpartnr`, `last_update`, `manual_input`, `obsolete`) ".
                "SELECT `id`, `id_supplier`, `supplierpartnr`, now(), '1', `obsolete` FROM `parts` ".
                "WHERE (id_supplier > '0') AND (parts.id NOT IN (SELECT part_id FROM orderdetails))";
            $updateSteps[] = "UPDATE `orderdetails` SET datetime_added=CURRENT_TIMESTAMP";

            // create table "pricedetails"
            $updateSteps[] = "CREATE TABLE `pricedetails` (".
                "`id` int(11) NOT NULL AUTO_INCREMENT,".
                "`orderdetails_id` INT(11) NOT NULL,".
                "`price` DECIMAL(6,2) NOT NULL,".
                "`price_related_quantity` INT(11) NOT NULL DEFAULT 1,".
                "`min_discount_quantity`INT(11) NOT NULL DEFAULT 1,".
                "`manual_input` BOOL NOT NULL DEFAULT true,".
                "`last_modified` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,".
                " PRIMARY KEY  (`id`),".
                " UNIQUE KEY pricedetails_combination_uk (`orderdetails_id`, `min_discount_quantity`)".
                ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;";
            $updateSteps[] = "CREATE INDEX pricedetails_orderdetails_id_k ON pricedetails(orderdetails_id)";
            $updateSteps[] = "INSERT INTO `pricedetails` ".
                "(`orderdetails_id`, `price`, `price_related_quantity`, ".
                "`min_discount_quantity`, `manual_input`) ".
                "SELECT `id`, `price`, '1', '1', `manual_input` FROM `orderdetails` ".
                "WHERE (price > 0)";

            // clean up table "orderdetails"
            $updateSteps[] = "ALTER TABLE `orderdetails` DROP COLUMN `manual_input`";
            $updateSteps[] = "ALTER TABLE `orderdetails` DROP COLUMN `price`";
            $updateSteps[] = "ALTER TABLE `orderdetails` DROP COLUMN `last_update`";

            // table "parts"
            $updateSteps[] = "ALTER TABLE `parts` DROP INDEX `id_storeloc`";
            $updateSteps[] = "ALTER TABLE `parts` DROP INDEX `id_category`";
            $updateSteps[] = "ALTER TABLE `parts` ADD `order_orderdetails_id` INT(11) DEFAULT NULL";
            $updateSteps[] = "ALTER TABLE `parts` ADD `order_quantity` INT(11) NOT NULL DEFAULT '1'";
            $updateSteps[] = "ALTER TABLE `parts` ADD `manual_order` BOOLEAN NOT NULL DEFAULT false";
            $updateSteps[] = "ALTER TABLE `parts` CHANGE `id_storeloc` `id_storelocation` int(11) DEFAULT NULL";
            $updateSteps[] = "ALTER TABLE `parts` MODIFY `id_footprint` INT(11) DEFAULT NULL";
            $updateSteps[] = "ALTER TABLE `parts` DROP COLUMN `id_supplier`";
            $updateSteps[] = "ALTER TABLE `parts` DROP COLUMN `supplierpartnr`";
            $updateSteps[] = "ALTER TABLE `parts` DROP COLUMN `obsolete`";
            $updateSteps[] = "ALTER TABLE `parts` ADD `id_manufacturer` INT(11) DEFAULT NULL";
            $updateSteps[] = "ALTER TABLE `parts` ADD `id_master_picture_attachement` INT(11) DEFAULT NULL";
            $updateSteps[] = "ALTER TABLE `parts` MODIFY `name` mediumtext NOT NULL";
            $updateSteps[] = "ALTER TABLE `parts` MODIFY `comment` mediumtext NOT NULL";
            $updateSteps[] = "ALTER TABLE `parts` MODIFY `description` mediumtext NOT NULL";
            $updateSteps[] = "ALTER TABLE `parts` ADD `datetime_added` TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
            $updateSteps[] = "ALTER TABLE `parts` ADD `last_modified` TIMESTAMP NOT NULL";
            $updateSteps[] = "CREATE INDEX parts_id_category_k ON parts(id_category)";
            $updateSteps[] = "CREATE INDEX parts_id_footprint_k ON parts(id_footprint)";
            $updateSteps[] = "CREATE INDEX parts_id_storelocation_k ON parts(id_storelocation)";
            $updateSteps[] = "CREATE INDEX parts_order_orderdetails_id_k ON parts(order_orderdetails_id)";
            $updateSteps[] = "CREATE INDEX parts_id_manufacturer_k ON parts(id_manufacturer)";
            $updateSteps[] = "UPDATE `parts`, `pictures` SET ".
                "parts.id_master_picture_attachement=pictures.id ".
                "WHERE (pictures.part_id=parts.id) AND (pictures.pict_masterpict=TRUE)";
            $updateSteps[] = "UPDATE `parts` SET datetime_added=CURRENT_TIMESTAMP";
            $updateSteps[] = "UPDATE `parts` SET last_modified=CURRENT_TIMESTAMP";
            $updateSteps[] = "UPDATE `parts` SET id_footprint=NULL WHERE id_footprint=0";
            $updateSteps[] = "UPDATE `parts` SET id_storelocation=NULL WHERE id_storelocation=0";
            $updateSteps[] = "UPDATE `parts` SET id_manufacturer=NULL WHERE id_manufacturer=0";

            // table "part_device" will now be renamed to "device_parts"
            // (we will create a new table and copy all records to the new table,
            // but we will group device parts with the same device + part
            // because multiple device parts with the same part are no longer allowed)
            $updateSteps[] = "CREATE TABLE IF NOT EXISTS `device_parts` (
                `id` INT(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
                `id_part` INT(11) NOT NULL DEFAULT '0',
                `id_device` INT(11) NOT NULL DEFAULT '0',
                `quantity` INT(11) NOT NULL DEFAULT '0',
                `mountnames` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                UNIQUE KEY device_parts_combination_uk (`id_part`, `id_device`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
            $updateSteps[] = "CREATE INDEX device_parts_id_part_k ON device_parts(id_part)";
            $updateSteps[] = "CREATE INDEX device_parts_id_device_k ON device_parts(id_device)";
            $updateSteps[] = "INSERT INTO `device_parts` ".
                "(`id_part`, `id_device`, `quantity`, `mountnames`) ".
                "SELECT `id_part`, `id_device`, SUM(`quantity`), GROUP_CONCAT(`mountname`) FROM `part_device` ".
                "GROUP BY id_part, id_device ";
            $updateSteps[] = "DROP TABLE `part_device`";

            // table "internal"
            $updateSteps[] = "ALTER TABLE `internal` MODIFY `keyValue` VARCHAR(255)"; // Maybe we need more space for some values...
            $updateSteps[] = "DELETE FROM `internal` WHERE keyName='dbAutoUpdate'"; // this is now in config.php

            // create table "attachement_types"
            $updateSteps[] = "CREATE TABLE `attachement_types` (".
                "`id` INT(11) NOT NULL AUTO_INCREMENT,".
                "`name` TINYTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,".
                "`parent_id` INT(11) DEFAULT NULL,".
                " PRIMARY KEY  (`id`)".
                ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
            $updateSteps[] = "CREATE INDEX attachement_types_parent_id_k ON attachement_types(parent_id)";

            // create attachement types "Bilder" and "Datenblätter"
            $updateSteps[] = "INSERT INTO attachement_types (name, parent_id) VALUES ('Bilder', NULL)";
            $updateSteps[] = "INSERT INTO attachement_types (name, parent_id) VALUES ('Datenblätter', NULL)";

            // table "pictures" will now be changed to "attachements"
            $updateSteps[] = "ALTER TABLE `pictures` RENAME `attachements`";
            $updateSteps[] = "ALTER TABLE `attachements` CHANGE `part_id` `element_id` INT(11) NOT NULL";
            $updateSteps[] = "DROP INDEX `pict_type` ON `attachements`";
            $updateSteps[] = "ALTER TABLE `attachements` CHANGE `pict_fname` `filename` MEDIUMTEXT NOT NULL";
            $updateSteps[] = "ALTER TABLE `attachements` DROP COLUMN `pict_width`";
            $updateSteps[] = "ALTER TABLE `attachements` DROP COLUMN `pict_height`";
            $updateSteps[] = "ALTER TABLE `attachements` DROP COLUMN `pict_type`";
            $updateSteps[] = "ALTER TABLE `attachements` DROP COLUMN `tn_obsolete`";
            $updateSteps[] = "ALTER TABLE `attachements` DROP COLUMN `tn_t`";
            $updateSteps[] = "ALTER TABLE `attachements` DROP COLUMN `tn_pictid`";
            $updateSteps[] = "ALTER TABLE `attachements` CHANGE `pict_masterpict` `show_in_table` BOOLEAN NOT NULL DEFAULT FALSE";
            $updateSteps[] = "ALTER TABLE `attachements` ADD `name` TINYTEXT NOT NULL AFTER `id`";
            $updateSteps[] = "ALTER TABLE `attachements` ADD `class_name` VARCHAR(255) NOT NULL AFTER `name`";
            $updateSteps[] = "ALTER TABLE `attachements` ADD `type_id` INT(11) NOT NULL AFTER `element_id`";
            $updateSteps[] = "CREATE INDEX attachements_class_name_k ON attachements(class_name)";
            $updateSteps[] = "CREATE INDEX attachements_element_id_k ON attachements(element_id)";
            $updateSteps[] = "UPDATE `attachements` SET name='Bild'";
            $updateSteps[] = "UPDATE `attachements` SET type_id=(SELECT `id` FROM `attachement_types` WHERE name = 'Bilder')";
            $updateSteps[] = "UPDATE `attachements` SET class_name='Part'";
            $updateSteps[] = "UPDATE `attachements` SET show_in_table=FALSE";
            $updateSteps[] = "UPDATE `attachements` set `filename` = REPLACE(`filename`,'img_','%BASE%/media/img_')";

            // table "datasheets" will now be added to "attachements", and then "datasheets" will be deleted
            $updateSteps[] = "INSERT INTO `attachements` ".
                "(`element_id`, `name`, `filename`, `class_name`, `type_id`, `show_in_table`) ".
                "SELECT `part_id` as `element_id`, 'Datenblatt' as `name`, `datasheeturl` as `filename`, ".
                "'Part', (SELECT `id` FROM `attachement_types` WHERE name = 'Datenblätter'), TRUE FROM `datasheets`";
            $updateSteps[] = "DROP TABLE `datasheets`";

            // create table "manufacturers"
            $updateSteps[] = "CREATE TABLE `manufacturers` (".
                "`id` INT(11) NOT NULL AUTO_INCREMENT,".
                "`name` TINYTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,".
                "`parent_id` INT(11) DEFAULT NULL,".
                "`address` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,".
                "`phone_number` TINYTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,".
                "`fax_number` TINYTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,".
                "`email_address` TINYTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,".
                "`website` TINYTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,".
                "`datetime_added` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,".
                " PRIMARY KEY  (`id`)".
                ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
            $updateSteps[] = "CREATE INDEX manufacturers_parent_id_k ON manufacturers(parent_id)";

            // add foreign keys
            $updateSteps[] = "ALTER TABLE `categories` ADD CONSTRAINT categories_parent_id_fk FOREIGN KEY (parent_id) REFERENCES categories(id)";
            $updateSteps[] = "ALTER TABLE `devices` ADD CONSTRAINT devices_parent_id_fk FOREIGN KEY (parent_id) REFERENCES devices(id)";
            $updateSteps[] = "ALTER TABLE `attachement_types` ADD CONSTRAINT attachement_types_parent_id_fk FOREIGN KEY (parent_id) REFERENCES attachement_types(id)";
            $updateSteps[] = "ALTER TABLE `footprints` ADD CONSTRAINT footprints_parent_id_fk FOREIGN KEY (parent_id) REFERENCES footprints(id)";
            $updateSteps[] = "ALTER TABLE `manufacturers` ADD CONSTRAINT manufacturers_parent_id_fk FOREIGN KEY (parent_id) REFERENCES manufacturers(id)";
            $updateSteps[] = "ALTER TABLE `parts` ADD CONSTRAINT parts_id_footprint_fk FOREIGN KEY (id_footprint) REFERENCES footprints(id)";
            $updateSteps[] = "ALTER TABLE `parts` ADD CONSTRAINT parts_id_storelocation_fk FOREIGN KEY (id_storelocation) REFERENCES storelocations(id)";
            $updateSteps[] = "ALTER TABLE `parts` ADD CONSTRAINT parts_order_orderdetails_id_fk FOREIGN KEY (order_orderdetails_id) REFERENCES orderdetails(id)";
            $updateSteps[] = "ALTER TABLE `parts` ADD CONSTRAINT parts_id_manufacturer_fk FOREIGN KEY (id_manufacturer) REFERENCES manufacturers(id)";
            $updateSteps[] = "ALTER TABLE `storelocations` ADD CONSTRAINT storelocations_parent_id_fk FOREIGN KEY (parent_id) REFERENCES storelocations(id)";
            $updateSteps[] = "ALTER TABLE `suppliers` ADD CONSTRAINT suppliers_parent_id_fk FOREIGN KEY (parent_id) REFERENCES suppliers(id)";
            $updateSteps[] = "ALTER TABLE `attachements` ADD CONSTRAINT attachements_type_id_fk FOREIGN KEY (type_id) REFERENCES attachement_types(id)";
            break;

        case 13:
            // we have created the new directory "data", now we have to rename all filenames
            $updateSteps[] = "UPDATE `attachements` set `filename` = REPLACE(`filename`,'%BASE%/media/','%BASE%/data/media/')";
            break;

        case 14:
            // if a part has no master picture attachement, but it has picture attachements, set one of them as the master picture attachement
            $updateSteps[] = "UPDATE `parts` ".
                "INNER JOIN `attachements` ".
                "ON (attachements.element_id=parts.id) ".
                "AND (attachements.class_name='Part') ".
                "AND ((LOCATE('.jpg', LOWER(attachements.filename)) > 0) ".
                "OR (LOCATE('.jpeg', LOWER(attachements.filename)) > 0) ".
                "OR (LOCATE('.png', LOWER(attachements.filename)) > 0) ".
                "OR (LOCATE('.gif', LOWER(attachements.filename)) > 0) ".
                "OR (LOCATE('.bmp', LOWER(attachements.filename)) > 0)) ".
                "SET parts.id_master_picture_attachement=attachements.id ".
                "WHERE (parts.id_master_picture_attachement IS NULL)";
            break;

        case 15:
            // add new columns to the suppliers/manufacturers table for the new automatic links to the parts on the company's website
            $updateSteps[] = "ALTER TABLE `suppliers` ADD `auto_product_url` TINYTEXT NOT NULL AFTER `website`";
            $updateSteps[] = "ALTER TABLE `manufacturers` ADD `auto_product_url` TINYTEXT NOT NULL AFTER `website`";
            // add some additional columns for manual links, maybe this will be fully supported in future...
            $updateSteps[] = "ALTER TABLE `parts` ADD `manufacturer_product_url` TINYTEXT NOT NULL AFTER `id_master_picture_attachement`";
            $updateSteps[] = "ALTER TABLE `orderdetails` ADD `supplier_product_url` TINYTEXT NOT NULL AFTER `obsolete`";
            break;

        case 16:
            //Price is now stored with 5 decimal places.
            $updateSteps[] = "ALTER TABLE `pricedetails` MODIFY `price` DECIMAL(9,5)";
            break;

        case 17:
            $updateSteps[] = "ALTER TABLE `footprints` ADD `filename_3d` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `filename`";
            $updateSteps[]  = "UPDATE footprints SET filename_3d = '';";
            break;

        case 18:
            //Disable properties option
            $updateSteps[] = "ALTER TABLE `categories` ADD `disable_properties` TINYINT(1) NOT NULL DEFAULT '0' AFTER `disable_autodatasheets`;";
            $updateSteps[] = "UPDATE categories SET disable_properties = 0";
            //Name Regex
            $updateSteps[] = "ALTER TABLE `categories` ADD `partname_regex` TEXT NOT NULL DEFAULT '' AFTER `disable_properties`";
            $updateSteps[] = "UPDATE categories SET partname_regex = ''";
            //Name Filter hint
            $updateSteps[] = "ALTER TABLE `categories` ADD `partname_hint` TEXT NOT NULL DEFAULT '' AFTER `partname_regex`";
            $updateSteps[] = "UPDATE categories SET partname_hint = ''";
            //Default Description
            $updateSteps[] = "ALTER TABLE `categories` ADD `default_description` TEXT NOT NULL DEFAULT '' AFTER `partname_hint`";
            $updateSteps[] = "UPDATE categories SET default_description = ''";
            //Default Comment
            $updateSteps[] = "ALTER TABLE `categories` ADD `default_comment` TEXT NOT NULL DEFAULT '' AFTER `default_description`";
            $updateSteps[] = "UPDATE categories SET default_comment = ''";
            break;

        /*case 17:
          // create table "users"
          $updateSteps[] = "CREATE TABLE `users` (".
              // Benutzerinformationen
              "`id` INT(11) NOT NULL AUTO_INCREMENT,".            // Benutzer-ID
              "`name` VARCHAR(30) NOT NULL,".                     // Anmeldename
              "`password` VARCHAR(32),".                          // MD5-Hash des Passworts
              "`first_name` TINYTEXT,".                           // Vorname
              "`last_name` TINYTEXT,".                            // Nachname
              "`department` TINYTEXT,".                           // Abteilung
              "`email` TINYTEXT,".                                // E-Mail Adresse
              // Einstellungen und Gruppenzugehörigkeit
              "`auto_login` BOOLEAN NOT NULL DEFAULT '0',".       // Automatische Anmeldung ein/aus
              "`session_id` VARCHAR(32),".                        // Session-ID für die Identifizierung
              "`group_id` INT(11) NOT NULL,".                     // Gruppen-ID von der Gruppe des Users
              // System-Rechte
              "`perms_system` INT(3) NOT NULL,".                  // Allgemeine Rechte ("Kleinkram")
              "`perms_system_group_childs` INT(3) NOT NULL,".     // Gruppenverwaltung (nur Untergruppen der eigenen Gruppe)
              "`perms_system_group_others` INT(3) NOT NULL,".     // Gruppenverwaltung (alle, ausser die Untergruppen)
              "`perms_system_users_subgroups` INT(3) NOT NULL,".  // Benutzerverwaltung (Benutzer aller untergeortneten Gruppen)
              "`perms_system_users_others` INT(3) NOT NULL,".     // Benutzerverwaltung (alle, ausser von untergeordneten Gruppen)
              "`perms_system_dbupdate` INT(3) NOT NULL,".         // Datenbankaktualisierung bzw. dessen Einstellungen
              "`perms_system_dbbackup` INT(3) NOT NULL,".         // Datenbank-Backup bzw. dessen Einstellungen
              // Bauteil-Rechte
              "`perms_parts` INT(3) NOT NULL,".                   // Betrachten/Erstellen/Löschen/Verschieben
              "`perms_parts_name` INT(3) NOT NULL,".              // Name
              "`perms_parts_description` INT(3) NOT NULL,".       // Beschreibung
              "`perms_parts_instock` INT(3) NOT NULL,".           // Menge (an Lager)
              "`perms_parts_mininstock` INT(3) NOT NULL,".        // Mindestmenge
              "`perms_parts_footprint` INT(3) NOT NULL,".         // Footprint
              "`perms_parts_storelocation` INT(3) NOT NULL,".     // Lagerort
              "`perms_parts_obsolete` INT(3) NOT NULL,".          // Obsolet
              "`perms_parts_comment` INT(3) NOT NULL,".           // Kommentar
              "`perms_parts_orderdetails` INT(3) NOT NULL,".      // Bestellinformationen (Lieferanten, Bestellnummern)
              "`perms_parts_prices` INT(3) NOT NULL,".            // Preisinformationen
              "`perms_parts_attachements` INT(3) NOT NULL,".      // Dateianhänge (Bilder, Datenblätter, ...)
              // Baugruppen-Rechte
              "`perms_devices` INT(3) NOT NULL,".                 // Betrachten/Bearbeiten/Erstellen/Löschen/Verschieben
              "`perms_devices_parts` INT(3) NOT NULL,".           // Bauteile betrachten/bearbeiten
              // Lagerorte-Rechte
              "`perms_storelocations` INT(3) NOT NULL,".          // Betrachten/Bearbeiten/Erstellen/Löschen/Verschieben
              "`perms_storelocations_isfull` INT(3) NOT NULL,".   // Eigenschaft, ob Lagerort voll ist
              // Footprints-Rechte
              "`perms_footprints` INT(3) NOT NULL,".              // Betrachten/Bearbeiten/Erstellen/Löschen/Verschieben
              // Kategorien-Rechte
              "`perms_categories` INT(3) NOT NULL,".              // Betrachten/Bearbeiten/Erstellen/Löschen/Verschieben
              // Lieferanten-Rechte
              "`perms_suppliers` INT(3) NOT NULL,".               // Betrachten/Bearbeiten/Erstellen/Löschen/Verschieben
              // Hersteller-Rechte
              "`perms_manufacturers` INT(3) NOT NULL,".           // Betrachten/Bearbeiten/Erstellen/Löschen/Verschieben
              // Attribute
              " PRIMARY KEY  (`id`),".
              " UNIQUE KEY `name` (`name`)".
              ") ENGINE=InnoDB;";

          // create table "groups"
          $updateSteps[] = "CREATE TABLE `groups` (".
              // Gruppeninformationen
              "`id` INT(11) NOT NULL AUTO_INCREMENT,".            // Gruppen-ID
              "`name` TINYTEXT NOT NULL,".                        // Gruppenname
              "`parent_id` INT(11) NOT NULL,".                    // ID der übergeordneten Gruppe ('-1' bei root)
              "`comment` MEDIUMTEXT,".                            // Kommentar (optional)
              // System-Rechte
              "`perms_system` INT(3) NOT NULL,".                  // Allgemeine Rechte ("Kleinkram")
              "`perms_system_group_childs` INT(3) NOT NULL,".     // Gruppenverwaltung (nur Untergruppen der eigenen Gruppe)
              "`perms_system_group_others` INT(3) NOT NULL,".     // Gruppenverwaltung (alle, ausser die Untergruppen)
              "`perms_system_users_subgroups` INT(3) NOT NULL,".  // Benutzerverwaltung (Benutzer aller untergeortneten Gruppen)
              "`perms_system_users_others` INT(3) NOT NULL,".     // Benutzerverwaltung (alle, ausser von untergeordneten Gruppen)
              "`perms_system_dbupdate` INT(3) NOT NULL,".         // Datenbankaktualisierung bzw. dessen Einstellungen
              "`perms_system_dbbackup` INT(3) NOT NULL,".         // Datenbank-Backup bzw. dessen Einstellungen
              // Bauteil-Rechte
              "`perms_parts` INT(3) NOT NULL,".                   // Betrachten/Erstellen/Löschen/Verschieben
              "`perms_parts_name` INT(3) NOT NULL,".              // Name
              "`perms_parts_description` INT(3) NOT NULL,".       // Beschreibung
              "`perms_parts_instock` INT(3) NOT NULL,".           // Menge (an Lager)
              "`perms_parts_mininstock` INT(3) NOT NULL,".        // Mindestmenge
              "`perms_parts_footprint` INT(3) NOT NULL,".         // Footprint
              "`perms_parts_storelocation` INT(3) NOT NULL,".     // Lagerort
              "`perms_parts_obsolete` INT(3) NOT NULL,".          // Obsolet
              "`perms_parts_comment` INT(3) NOT NULL,".           // Kommentar
              "`perms_parts_orderdetails` INT(3) NOT NULL,".      // Bestellinformationen (Lieferanten, Bestellnummern)
              "`perms_parts_prices` INT(3) NOT NULL,".            // Preisinformationen
              "`perms_parts_attachements` INT(3) NOT NULL,".      // Dateianhänge (Bilder, Datenblätter, ...)
              // Baugruppen-Rechte
              "`perms_devices` INT(3) NOT NULL,".                 // Betrachten/Bearbeiten/Erstellen/Löschen/Verschieben
              "`perms_devices_parts` INT(3) NOT NULL,".           // Bauteile betrachten/bearbeiten
              // Lagerorte-Rechte
              "`perms_storelocations` INT(3) NOT NULL,".          // Betrachten/Bearbeiten/Erstellen/Löschen/Verschieben
              "`perms_storelocations_isfull` INT(3) NOT NULL,".   // Eigenschaft, ob Lagerort voll ist
              // Footprints-Rechte
              "`perms_footprints` INT(3) NOT NULL,".              // Betrachten/Bearbeiten/Erstellen/Löschen/Verschieben
              // Kategorien-Rechte
              "`perms_categories` INT(3) NOT NULL,".              // Betrachten/Bearbeiten/Erstellen/Löschen/Verschieben
              // Lieferanten-Rechte
              "`perms_suppliers` INT(3) NOT NULL,".               // Betrachten/Bearbeiten/Erstellen/Löschen/Verschieben
              // Hersteller-Rechte
              "`perms_manufacturers` INT(3) NOT NULL,".           // Betrachten/Bearbeiten/Erstellen/Löschen/Verschieben
              // Attribute
              " PRIMARY KEY  (`id`)".
              ") ENGINE=InnoDB;";

          // create user "admin"
          $updateSteps[] = "INSERT INTO users SET ".
              "name='admin',".
              "password='81dc9bdb52d04dc20036dbd8313ed055',". // Passwort = "1234"
              "first_name='',".
              "last_name='',".
              "department='',".
              "email='',".
              "auto_login='0',".
              "session_id='',".
              "group_id='0',". // Gruppe 0 existiert nicht in der Datenbank, dies ist nur für den Benutzer "admin" erlaubt
              "perms_system='127',".
              "perms_system_group_childs='127',".
              "perms_system_group_others='127',".
              "perms_system_users_subgroups='127',".
              "perms_system_users_others='127',".
              "perms_system_dbupdate='127',".
              "perms_system_dbbackup='127',".
              "perms_parts='127',".
              "perms_parts_name='127',".
              "perms_parts_description='127',".
              "perms_parts_instock='127',".
              "perms_parts_mininstock='127',".
              "perms_parts_footprint='127',".
              "perms_parts_storelocation='127',".
              "perms_parts_obsolete='127',".
              "perms_parts_comment='127',".
              "perms_parts_orderdetails='127',".
              "perms_parts_prices='127',".
              "perms_parts_attachements='127',".
              "perms_devices='127',".
              "perms_devices_parts='127',".
              "perms_storelocations='127',".
              "perms_storelocations_isfull='127',".
              "perms_footprints='127',".
              "perms_categories='127',".
              "perms_suppliers='127',".
              "perms_manufacturers='127'";
          break;*/

        /*
                Templates:

                  case 14:
                    $updateSteps[] = "INSERT INTO internal (keyName, keyValue) VALUES ('test', 'muh')";
                    break;
                  case 15:
                    $updateSteps[] = "DELETE FROM internal WHERE keyName='test2'";
                    break;
        */
        default:
            throw new Exception("Unbekannte Datenbankversion \"$current_version\"!");
            break;
    }

    return $updateSteps;
}
