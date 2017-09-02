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

/**
 * @file lib.import.php
 * @brief Miscellaneous Functions for Data Import
 *
 * Steps for import data:
 *  1.  Convert the import string (CSV, XML, ...) into an array with "import_text_to_array()".
 *      For each key (column in CSV) in the import string, there will be a key in the returned array.
 *  2.  Generate a template loop to print out the import data with the table template: "build_import_template_loop()".
 *  3.  Check all values in the data array (verify it) with "import_*($check_only = true)".
 *      You will get an exception if the data is not valid.
 *  4.  Now you can import the data with "import_*($check_only = false)".
 *
 * @note    The "import data array" is an abstraction of data to import. It is independent of the import format (CSV, XML, ...).
 *          So we can work now very easy with that data.
 *
 * @author kami89
 */

use PartDB\Category;
use PartDB\Database;
use PartDB\DevicePart;
use PartDB\Footprint;
use PartDB\Log;
use PartDB\Manufacturer;
use PartDB\Orderdetails;
use PartDB\Part;
use PartDB\Pricedetails;
use PartDB\Storelocation;
use PartDB\Supplier;
use PartDB\User;

$import_data_columns = array(   // for import parts
    'part_name', 'part_description', 'part_instock', 'part_mininstock', 'part_comment',
    'part_category_name', 'part_footprint_name', 'part_storelocation_name', 'part_manufacturer_name',
    'part_supplier_name', 'part_supplierpartnr', 'part_price',
    // for import device parts (add existing parts to a device)
    'devicepart_part_id', 'devicepart_part_name', 'devicepart_mount_quantity', 'devicepart_mount_names'
);

$import_data_column_datatypes = array(  // for import parts
    'part_name'                 => 'string',
    'part_description'          => 'string',
    'part_instock'              => 'integer',
    'part_mininstock'           => 'integer',
    'part_comment'              => 'string',
    'part_category_name'        => 'string',
    'part_footprint_name'       => 'string',
    'part_storelocation_name'   => 'string',
    'part_manufacturer_name'    => 'string',
    'part_supplier_name'        => 'string',
    'part_supplierpartnr'       => 'string',
    'part_price'                => 'float',
    // for import device parts (add existing parts to a device)
    'devicepart_part_id'        => 'integer',
    'devicepart_part_name'      => 'string',
    'devicepart_mount_quantity' => 'integer',
    'devicepart_mount_names'    => 'string',
);

$import_data_default_values = array(    // for import parts
    'part_name'                 => '',
    'part_description'          => '',
    'part_instock'              => 0,
    'part_mininstock'           => 0,
    'part_comment'              => '',
    'part_category_name'        => 'Import',
    'part_footprint_name'       => '',
    'part_storelocation_name'   => '',
    'part_manufacturer_name'    => '',
    'part_supplier_name'        => '',
    'part_supplierpartnr'       => '',
    'part_price'                => 0.0,
    // for import device parts (add existing parts to a device)
    'devicepart_part_id'        => 0,
    'devicepart_part_name'      => '',
    'devicepart_mount_quantity' => 0,
    'devicepart_mount_names'    => '',
);

$import_data_translations = array(  // translations for import parts
    'Name'                  => 'part_name',
    'Beschreibung'          => 'part_description',
    'Bestand'               => 'part_instock',
    'Mindestbestand'        => 'part_mininstock',
    'Kommentar'             => 'part_comment',
    'Kategorie'             => 'part_category_name',
    'Footprint'             => 'part_footprint_name',
    'Lagerort'              => 'part_storelocation_name',
    'Hersteller'            => 'part_manufacturer_name',
    'Lieferant'             => 'part_supplier_name',
    'Bestellnummer'         => 'part_supplierpartnr',
    'Preis'                 => 'part_price',
    // translations for import device parts
    'Bauteile-ID'           => 'devicepart_part_id',
    'Bauteil-Name'          => 'devicepart_part_name',
    'Anzahl'                => 'devicepart_mount_quantity',
    'Bestückungsdaten'      => 'devicepart_mount_names');

/**
 * Get the import data array from the variable $_REQUEST
 *
 * If the user clicks "check data" under an import table, you can use this function to build the import data array.
 *
 * @return array     An associative array with the column-names as keys and the field contents as values (exactly the same like "import_text_to_array()")
 *
 * @throws Exception if there was an error
 */
function extractImportDataFromRequest($table_rowcount)
{
    global $import_data_columns;
    global $import_data_default_values;
    global $import_data_column_datatypes;
    $data = array();

    for ($i=0; $i<$table_rowcount; $i++) {
        $data_row = array();
        foreach ($import_data_columns as $column) {
            switch ($column) {
                // import parts
                case 'part_name':
                    $value = isset($_REQUEST['name_'.$i]) ? $_REQUEST['name_'.$i] : $import_data_default_values[$column];
                    break;
                case 'part_description':
                    $value = isset($_REQUEST['description_'.$i]) ? $_REQUEST['description_'.$i] : $import_data_default_values[$column];
                    break;
                case 'part_instock':
                    $value = isset($_REQUEST['instock_'.$i]) ? $_REQUEST['instock_'.$i] : $import_data_default_values[$column];
                    break;
                case 'part_mininstock':
                    $value = isset($_REQUEST['mininstock_'.$i]) ? $_REQUEST['mininstock_'.$i] : $import_data_default_values[$column];
                    break;
                case 'part_comment':
                    $value = isset($_REQUEST['comment_'.$i]) ? $_REQUEST['comment_'.$i] : $import_data_default_values[$column];
                    break;
                case 'part_category_name':
                    $value = isset($_REQUEST['category_'.$i]) ? $_REQUEST['category_'.$i] : $import_data_default_values[$column];
                    break;
                case 'part_footprint_name':
                    $value = isset($_REQUEST['footprint_'.$i]) ? $_REQUEST['footprint_'.$i] : $import_data_default_values[$column];
                    break;
                case 'part_storelocation_name':
                    $value = isset($_REQUEST['storelocation_'.$i]) ? $_REQUEST['storelocation_'.$i] : $import_data_default_values[$column];
                    break;
                case 'part_manufacturer_name':
                    $value = isset($_REQUEST['manufacturer_'.$i]) ? $_REQUEST['manufacturer_'.$i] : $import_data_default_values[$column];
                    break;
                case 'part_supplier_name':
                    $value = isset($_REQUEST['supplier_'.$i]) ? $_REQUEST['supplier_'.$i] : $import_data_default_values[$column];
                    break;
                case 'part_supplierpartnr':
                    $value = isset($_REQUEST['supplier_partnr_'.$i]) ? $_REQUEST['supplier_partnr_'.$i] : $import_data_default_values[$column];
                    break;
                case 'part_price':
                    $value = isset($_REQUEST['price_'.$i]) ? $_REQUEST['price_'.$i] : $import_data_default_values[$column];
                    $value = str_replace(',', '.', $value); // TODO: use the PHP class "NumberFormatter"
                    break;

                // import device parts
                case 'devicepart_part_id':
                    $value = isset($_REQUEST['id_'.$i]) ? $_REQUEST['id_'.$i] : $import_data_default_values[$column];
                    break;
                case 'devicepart_part_name':
                    $value = isset($_REQUEST['name_'.$i]) ? $_REQUEST['name_'.$i] : $import_data_default_values[$column];
                    break;
                case 'devicepart_mount_quantity':
                    $value = isset($_REQUEST['quantity_'.$i]) ? $_REQUEST['quantity_'.$i] : $import_data_default_values[$column];
                    break;
                case 'devicepart_mount_names':
                    $value = isset($_REQUEST['mountnames_'.$i]) ? $_REQUEST['mountnames_'.$i] : $import_data_default_values[$column];
                    break;

                // undefined column
                default:
                    throw new Exception('Unbekannte Spalte: '.$column);
            }

            settype($value, $import_data_column_datatypes[$column]);
            if (is_string($value)) {
                $value = trim($value);
            }

            $data_row[$column] = $value;
        }
        $data[] = $data_row;
    }

    return $data;
}

/**
 * Convert an import text string (like XML or CSV) to an associative array
 *
 * What this function does:
 *  * Write all values from the import string (CSV, XML) to an assocuative array
 *  * If the column names in CSV or XML are in german, they will be replaced with internal column names (like "Name" --> "part_name")
 *  * Set the datatypes of each array element correct (numbers to integer, texts to string)
 *  * Fill in default values for all attributes which are not defined in the import string
 *
 * @note After calling this function for device parts, you have to call the function "match_devicepart_names_to_ids()"!
 *
 * @param string $text          The text which should be converted to an array
 * @param string $format        @li The file format
 *                              @li Supported formats: "CSV", "XML"
 * @param string $separator     For CSV we need to know the separator string
 *
 * @return array                An associative array with the column-names as keys and the field contents as values
 *
 * @throws Exception if there was an error (maybe the file structure is not correct)
 */
function importTextToArray($text, $format, $separator = ';')
{
    global $config;
    global $import_data_columns;
    global $import_data_column_datatypes;
    global $import_data_default_values;
    global $import_data_translations;

    $format = strtoupper($format);
    $accepted_codings = array('UTF-8', 'ISO-8859-1', 'ISO-8859-15', 'ASCII');
    $text_converted = mb_convert_encoding($text, $config['html']['http_charset'], mb_detect_encoding($text, $accepted_codings, true));
    $data = array();

    switch ($format) {
        case 'CSV':
            $file_array = array_filter(array_map('trim', explode("\n", $text_converted)));

            if (count($file_array) < 2) {
                throw new Exception('Die Datei muss mindestens eine Header-Zeile und eine Daten-Zeile haben!');
            }

            if (mb_substr_count($file_array[0], '#') == 0) {
                throw new Exception("Es wurde kein Header gefunden (das erste Zeichen der ersten Zeile muss '#' sein)!");
            } else {
                $file_array[0] = trim(str_replace('#', '', $file_array[0]));
            }

            $csv_columns = array_map('trim', explode($separator, $file_array[0]));

            foreach ($csv_columns as $key => $value) {
                if (isset($import_data_translations[$value])) {
                    $csv_columns[$key] = $import_data_translations[$value];
                } elseif (in_array($value, $import_data_translations)) {
                    $csv_columns[$key] = $value;
                } else {
                    throw new Exception('Ungültige Spalte: "'.$value.'"');
                }
            }

            unset($file_array[0]); // remove header line
            $row_number = 2;
            foreach ($file_array as $line) {
                $data_row = array();
                $values = array_map('trim', str_getcsv($line, $separator));

                if (count($csv_columns) != count($values)) {
                    throw new Exception('Es haben nicht alle Zeilen die selbe Anzahl an Attributen (Zeile '.$row_number.')!');
                }

                foreach ($import_data_columns as $column) {
                    if (in_array($column, $csv_columns)) {
                        $data_row[$column] = $values[array_search($column, $csv_columns)];
                    } else {
                        $data_row[$column] = $import_data_default_values[$column];
                    }

                    settype($data_row[$column], $import_data_column_datatypes[$column]);
                }

                $data[] = $data_row;
                $row_number++;
            }

            break;

        case 'XML':
            $dom = new DOMDocument('1.0', 'utf-8');
            $success = $dom->loadXml($text_converted, LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_NOBLANKS);

            if (! $success) {
                throw new Exception('Das DOMDocument-Objekt konnte nicht erstellt werden!');
            }

            $elements = $dom->getElementsByTagName('part');
            $column_prefix = 'part_';
            if ($elements->length == 0) {
                $elements = $dom->getElementsByTagName('devicepart');
                $column_prefix = 'devicepart_';
            }
            if ($elements->length == 0) {
                throw new Exception('Es wurden keine Einträge gefunden!');
            }

            foreach ($elements as $element) {
                $data_row = array();
                $columns = array();

                foreach ($element->childNodes as $node) {
                    $columns[$column_prefix.$node->nodeName] = $node->nodeValue;
                }

                foreach ($import_data_columns as $column) {
                    if (isset($columns[$column])) {
                        $data_row[$column] = $columns[$column];
                    } else {
                        $data_row[$column] = $import_data_default_values[$column];
                    }

                    settype($data_row[$column], $import_data_column_datatypes[$column]);
                }

                $data[] = $data_row;
            }
            break;

        default:
            throw new Exception('Nicht unterstütztes Dateiformat: "'.$format.'"');
    }

    return $data;
}

/**
 * Fill in DevicePart IDs of an associative array (from "import_text_to_array()") where only the part names are used (instead of the IDs)
 *
 * If the user has used part names ("devicepart_part_name") instead of part IDs ("devicepart_part_id"),
 * this funtion will search for these parts and fill in the IDs in "devicepart_part_id".
 *
 * If no unique part is found, this function will ignore this device part ("devicepart_part_id == 0").
 * After trying to import such parts, you will get an exception.
 *
 * @note    You should ALWAYS call this function after using "import_text_to_array()" with device parts!
 *          But it's not necessary to call it after building the data array with "extract_import_data_from_request()".
 *
 * @param Database  &$database          reference to the database object
 * @param User      &$current_user      reference to the user which is logged in
 * @param Log       &$log               reference to the Log-object
 * @param array     &$data              reference to the data array from the function "import_text_to_array()"
 *
 * @throws Exception if there was an error (but NOT if the search for parts was not successful)
 */
function matchDevicepartNamesToIds(&$database, &$current_user, &$log, &$data)
{
    foreach ($data as $key => $row) {
        if (($row['devicepart_part_id'] <= 0) && (strlen($row['devicepart_part_name']) > 0)) {
            // we have only the name of the part, not the ID
            // --> try to find the ID by the name

            $parts = Part::searchParts($database, $current_user, $log, $row['devicepart_part_name'], '', true, false);

            foreach ($parts as $partkey => $part) {
                if ($part->getName() != $row['devicepart_part_name']) {
                    unset($parts[$partkey]);
                }
            }

            if (count($parts) == 1) {
                $data[$key]['devicepart_part_id'] = $parts[0]->getID();
            }
        }
    }
}

/**
 * Convert an associative array (from "import_text_to_array()") to an template loop for creating a table
 *
 * @param Database  &$database          reference to the database object
 * @param User      &$current_user      reference to the user which is logged in
 * @param Log       &$log               reference to the Log-object
 * @param array     $data               The data array from the function "import_text_to_array()"
 *
 * @return array                A template loop for printing a table with "vlib_table.tmpl"
 *
 * @throws Exception if there was an error
 */
function buildPartsImportTemplateLoop(&$database, &$current_user, &$log, $data)
{
    $loop = array();

    // table columns
    $columns = array(   'row', 'name_edit', 'description_edit', 'instock_edit', 'mininstock_edit', 'comment_edit',
        'category_edit', 'footprint_edit', 'storelocation_edit', 'manufacturer_edit', 'supplier_edit',
        'supplier_partnr_edit', 'price_edit');

    $column_loop = array();
    foreach ($columns as $column) {
        $column_loop[] = array('caption' => $column);
    }
    $loop[] = array('print_header' => true, 'columns' => $column_loop); // print the table header

    $row_index = 0;
    foreach ($data as $row) {
        $table_row = array();
        $table_row['row_odd']       = isOdd($row_index);
        $table_row['row_index']     = $row_index;
        $table_row['row_fields']    = array();

        foreach ($columns as $column) {
            $row_field = array();
            $row_field['row_index']         = $row_index;
            $row_field['caption']           = $column;

            switch ($column) {
                case 'row':
                    $row_field['row'] = $row_index + 1;
                    break;
                case 'name_edit':
                    $row_field['name'] = $row['part_name'];
                    break;
                case 'description_edit':
                    $row_field['description'] = $row['part_description'];
                    break;
                case 'instock_edit':
                    $row_field['instock'] = $row['part_instock'];
                    break;
                case 'mininstock_edit':
                    $row_field['mininstock'] = $row['part_mininstock'];
                    break;
                case 'comment_edit':
                    $row_field['comment'] = $row['part_comment'];
                    break;
                case 'category_edit':
                    $row_field['category_name'] = $row['part_category_name'];
                    break;
                case 'footprint_edit':
                    $row_field['footprint_name'] = $row['part_footprint_name'];
                    break;
                case 'storelocation_edit':
                    $row_field['storelocation_name'] = $row['part_storelocation_name'];
                    break;
                case 'manufacturer_edit':
                    $row_field['manufacturer_name'] = $row['part_manufacturer_name'];
                    break;
                case 'supplier_edit':
                    $row_field['supplier_name'] = $row['part_supplier_name'];
                    break;
                case 'supplier_partnr_edit':
                    $row_field['supplier_partnr'] = $row['part_supplierpartnr'];
                    break;
                case 'price_edit':
                    $row_field['price'] = $row['part_price'];
                    break;
                default:
                    throw new Exception('Unbekannte Spalte: '.$column);
            }
            $table_row['row_fields'][] = $row_field;
        }
        $loop[] = $table_row;
        $row_index++;
    }

    return $loop;
}

/**
 * Convert an associative array (from "import_text_to_array()") to an template loop for creating a table
 *
 * @param Database  &$database          reference to the database object
 * @param User      &$current_user      reference to the user which is logged in
 * @param Log       &$log               reference to the Log-object
 * @param array     $data               The data array from the function "import_text_to_array()"
 *
 * @return array                A template loop for printing a table with "vlib_table.tmpl"
 *
 * @throws Exception if there was an error
 */
function buildDevicepartsImportTemplateLoop(&$database, &$current_user, &$log, $data)
{
    $loop = array();

    // table columns
    $columns = array('hover_picture', 'row', 'id', 'name', 'description', 'footprint_name', 'quantity_edit', 'mountnames_edit');

    $column_loop = array();
    foreach ($columns as $column) {
        $column_loop[] = array('caption' => $column);
    }
    $loop[] = array('print_header' => true, 'columns' => $column_loop); // print the table header

    $row_index = 0;
    foreach ($data as $row) {
        try {
            $part = new Part($database, $current_user, $log, $row['devicepart_part_id']);
        } catch (Exception $e) {
            $part = null; // To avoid warnings like "undefined variable $part"
        }

        $table_row = array();
        $table_row['row_odd']           = isOdd($row_index);
        $table_row['row_index']         = $row_index;
        $table_row['id']                = $row['devicepart_part_id'];
        $table_row['row_fields']        = array();
        $table_row['part_not_found']    = ($part == null);

        foreach ($columns as $column) {
            $row_field = array();
            $row_field['row_index']         = $row_index;
            $row_field['caption']           = $column;
            $row_field['id']                = $row['devicepart_part_id'];
            $row_field['part_not_found']    = ($part == null);

            switch ($column) {
                case 'id':
                    $row_field['id'] = $row['devicepart_part_id'];
                    break;
                case 'row':
                    $row_field['row'] = $row_index + 1;
                    break;
                case 'hover_picture':
                    $picture_filename = is_object($part) ? str_replace(BASE, BASE_RELATIVE, $part->getMasterPictureFilename(true)) : '';
                    $row_field['picture_name']  = strlen($picture_filename) ? basename($picture_filename) : '';
                    $row_field['small_picture'] = strlen($picture_filename) ? $picture_filename : '';
                    $row_field['hover_picture'] = strlen($picture_filename) ? $picture_filename : '';
                    break;
                case 'name':
                    $row_field['name'] = is_object($part) ? $part->getName() : $row['devicepart_part_name'];
                    break;
                case 'description':
                    $row_field['description'] = is_object($part) ? $part->getDescription() : 'BAUTEIL NICHT GEFUNDEN!';
                    break;
                case 'footprint_name':
                    $row_field['footprint_name'] = (is_object($part) && is_object($part->getFootprint())) ? $part->getFootprint()->getName() : '';
                    break;
                case 'quantity_edit':
                    $row_field['quantity'] = $row['devicepart_mount_quantity'];
                    break;
                case 'mountnames_edit':
                    $row_field['mountnames'] = $row['devicepart_mount_names'];
                    break;
                default:
                    throw new Exception('Unbekannte Spalte: '.$column);
            }
            $table_row['row_fields'][] = $row_field;
        }
        $loop[] = $table_row;
        $row_index++;
    }

    return $loop;
}

/**
 * Import Parts (create Parts, and if neccessary, Categories, Footprints and so on)
 *
 * @note    This function uses database transactions. If an error occurs, all changes will be rolled back.
 *
 * @param Database  &$database          reference to the database object
 * @param User      &$current_user      reference to the user which is logged in
 * @param Log       &$log               reference to the Log-object
 * @param array     $data               The import data array from "extract_import_data_from_request()"
 * @param boolean   $only_check_data    If true, this function will only check if all values in "$data" are valid.
 *                                      In this case, no parts will be imported!
 *
 * @return array    All new Part objects (only if "$only_check_data == false")
 *
 * @throws Exception    if there was an error (maybe the passed data is not valid)
 */
function importParts(&$database, &$current_user, &$log, $data, $only_check_data = false)
{
    $parts = array();

    try {
        $transaction_id = $database->beginTransaction(); // start transaction

        // Get the category, footprint, storelocation, ... which are named "Import", or create them.
        // We need this elements as parent for new elements, which will be created while import parts.
        $import_categories = Category::search($database, $current_user, $log, 'Import', true);
        if (count($import_categories) > 0) {
            $import_category = $import_categories[0];
            $import_category_created = false;
        } else {
            $import_category = Category::add($database, $current_user, $log, 'Import', null);
            $import_category_created = true; // we can delete it later if we didn't need it
        }

        $import_storelocations = Storelocation::search($database, $current_user, $log, 'Import', true);
        if (count($import_storelocations) > 0) {
            $import_storelocation = $import_storelocations[0];
            $import_storelocation_created = false;
        } else {
            $import_storelocation = Storelocation::add($database, $current_user, $log, 'Import', null);
            $import_storelocation_created = true; // we can delete it later if we didn't need it
        }

        $import_footprints = Footprint::search($database, $current_user, $log, 'Import', true);
        if (count($import_footprints) > 0) {
            $import_footprint = $import_footprints[0];
            $import_footprint_created = false;
        } else {
            $import_footprint = Footprint::add($database, $current_user, $log, 'Import', null);
            $import_footprint_created = true; // we can delete it later if we didn't need it
        }

        $import_suppliers = Supplier::search($database, $current_user, $log, 'Import', true);
        if (count($import_suppliers) > 0) {
            $import_supplier = $import_suppliers[0];
            $import_supplier_created = false;
        } else {
            $import_supplier = Supplier::add($database, $current_user, $log, 'Import', null);
            $import_supplier_created = true; // we can delete it later if we didn't need it
        }

        $import_manufacturers = Manufacturer::search($database, $current_user, $log, 'Import', true);
        if (count($import_manufacturers) > 0) {
            $import_manufacturer = $import_manufacturers[0];
            $import_manufacturer_created = false;
        } else {
            $import_manufacturer = Manufacturer::add($database, $current_user, $log, 'Import', null);
            $import_manufacturer_created = true; // we can delete it later if we didn't need it
        }
        $import_category_used = false;
        $import_storelocation_used = false;
        $import_footprint_used = false;
        $import_supplier_used = false;
        $import_manufacturer_used = false;

        // start import
        $row_index = 0;
        foreach ($data as $row) {
            $name               = $row['part_name'];
            $description        = $row['part_description'];
            $instock            = $row['part_instock'];
            $mininstock         = $row['part_mininstock'];
            $comment            = $row['part_comment'];
            $category_name      = $row['part_category_name'];
            $footprint_name     = $row['part_footprint_name'];
            $storelocation_name = $row['part_storelocation_name'];
            $manufacturer_name  = $row['part_manufacturer_name'];
            $supplier_name      = $row['part_supplier_name'];
            $supplierpartnr     = $row['part_supplierpartnr'];
            $price              = $row['part_price'];

            // search elements / create them if they don't exist already

            if (strlen($category_name) > 0) {
                $categories = Category::search($database, $current_user, $log, $category_name, true);

                if (count($categories) > 0) {
                    $category = $categories[0];
                } else {
                    $category = Category::add($database, $current_user, $log, $category_name, $import_category->getID());
                    $import_category_used = true;
                }
            } else {
                throw new Exception('Jedes Bauteil muss eine Kategorie haben!');
            }

            if (strlen($storelocation_name) > 0) {
                $storelocations = Storelocation::search($database, $current_user, $log, $storelocation_name, true);

                if (count($storelocations) > 0) {
                    $storelocation = $storelocations[0];
                } else {
                    $storelocation = Storelocation::add($database, $current_user, $log, $storelocation_name, $import_storelocation->getID());
                    $import_storelocation_used = true;
                }
            }

            if (strlen($manufacturer_name) > 0) {
                $manufacturers = Manufacturer::search($database, $current_user, $log, $manufacturer_name, true);

                if (count($manufacturers) > 0) {
                    $manufacturer = $manufacturers[0];
                } else {
                    $manufacturer = Manufacturer::add($database, $current_user, $log, $manufacturer_name, $import_manufacturer->getID());
                    $import_manufacturer_used = true;
                }
            }

            if (strlen($footprint_name) > 0) {
                $footprints = Footprint::search($database, $current_user, $log, $footprint_name, true);

                if (count($footprints) > 0) {
                    $footprint = $footprints[0];
                } else {
                    $footprint = Footprint::add($database, $current_user, $log, $footprint_name, $import_footprint->getID());
                    $import_footprint_used = true;
                }
            }

            if (strlen($supplier_name) > 0) {
                $suppliers = Supplier::search($database, $current_user, $log, $supplier_name, true);

                if (count($suppliers) > 0) {
                    $supplier = $suppliers[0];
                } else {
                    $supplier = Supplier::add($database, $current_user, $log, $supplier_name, $import_supplier->getID());
                    $import_supplier_used = true;
                }
            } else {
                if ((strlen($supplierpartnr) > 0) || ($price > 0)) {
                    throw new Exception('Ist eine Bestellnummer oder ein Preis angegeben, so muss auch ein Lieferant angegeben werden!');
                }
            }

            $new_part = Part::add(
                $database,
                $current_user,
                $log,
                $name,
                $category->getID(),
                $description,
                $instock,
                $mininstock,
                (isset($storelocation) ? $storelocation->getID() : null),
                (isset($manufacturer) ? $manufacturer->getID() : null),
                (isset($footprint) ? $footprint->getID() : null),
                $comment
            );

            if (isset($supplier)) {
                $new_orderdetails = Orderdetails::add($database, $current_user, $log, $new_part->getID(), $supplier->getID(), $supplierpartnr);

                if ($price > 0) {
                    $new_pricedetails = Pricedetails::add($database, $current_user, $log, $new_orderdetails->getID(), $price);
                }
            }

            if (! $only_check_data) {
                $parts[] = $new_part;
            }

            $row_index++;
        }

        // delete all elements which were created in this function, but were not used
        if (($import_category_created) && (! $import_category_used)) {
            $import_category->delete();
        }
        if (($import_storelocation_created) && (! $import_storelocation_used)) {
            $import_storelocation->delete();
        }
        if (($import_footprint_created) && (! $import_footprint_used)) {
            $import_footprint->delete();
        }
        if (($import_supplier_created) && (! $import_supplier_used)) {
            $import_supplier->delete();
        }
        if (($import_manufacturer_created) && (! $import_manufacturer_used)) {
            $import_manufacturer->delete();
        }

        if ($only_check_data) {
            $database->rollback();
        } // rollback transaction
        else {
            $database->commit($transaction_id);
        } // commit transaction
    } catch (Exception $e) {
        $database->rollback(); // rollback transaction
        throw new Exception((isset($row_index) ? 'Nr. '.($row_index + 1).': ' : '').$e->getMessage());
    }

    return $parts;
}

/**
 * Import DeviceParts in a Device
 *
 * @note    This function uses database transactions. If an error occurs, all changes will be rolled back.
 *
 * @note    If there are already parts in the device, which are also in the import data array,
 *          the quantity and the mountnames of the part to import will be added to the already existing device-part.
 *
 * @param Database  &$database          reference to the database object
 * @param User      &$current_user      reference to the user which is logged in
 * @param Log       &$log               reference to the Log-object
 * @param integer   $device_id          The ID of the device where the parts should be imported
 * @param array     $data               The import data array from "extract_import_data_from_request()"
 * @param boolean   $only_check_data    If true, this function will only check if all values in "$data" are valid.
 *                                      In this case, no parts will be imported!
 *
 * @throws Exception    if there was an error (maybe the passed data is not valid)
 */
function importDeviceParts(&$database, &$current_user, &$log, $device_id, $data, $only_check_data = false)
{
    try {
        $transaction_id = $database->beginTransaction(); // start transaction

        foreach ($data as $row) {
            $part_id = $row['devicepart_part_id'];
            $quantity = $row['devicepart_mount_quantity'];
            $mountnames = $row['devicepart_mount_names'];

            if ($quantity > 0) {
                $new_devicepart = DevicePart::add($database, $current_user, $log, $device_id, $part_id, $quantity, $mountnames, true);
            }
        }

        if ($only_check_data) {
            $database->rollback();
        } // rollback transaction
        else {
            $database->commit($transaction_id);
        } // commit transaction
    } catch (Exception $e) {
        $database->rollback(); // rollback transaction
        throw new Exception($e->getMessage());
    }
}
