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
 * @file lib.export.php
 * @brief Miscellaneous Functions for Data Export
 * @author kami89
 */

/**
 * Build a template loop for a <select> list of export formats
 *
 * @param string            $export_type        An export type which is defined in config_defaults.php ($config['export'][_HERE_IS_THE_EXPORT_TYPE_][0])
 * @param integer|string    $selected_index     The ID of the selected export type (from config_defaults.php): $config['export']['searchparts'][_HERE_IS_THE_FORMAT_ID_]
 *
 * @return array    The template loop
 */
function buildExportFormatsLoop($export_type, $selected_index = 0)
{
    global $config;

    if (! ctype_alpha($selected_index)) {
        settype($selected_index, 'integer');
    }

    $loop = array();
    foreach ($config['export'][$export_type] as $key => $value) {
        $loop[] = array('value' => $key, 'text' => '['.$value['format'].'] '.$value['name'], 'selected' => ($key === $selected_index));
    }

    return $loop;
}

/**
 * Export Part or DevicePart Objects
 *
 * @param array             &$objects               Array of Objects (Supported: Part, DevicePart)
 * @param string            $export_type            An export type which is defined in config_defaults.php: $config['export'][_HERE_IS_THE_EXPORT_TYPE_][0]
 * @param integer|string    $format_id              The ID of the export type (from config_defaults.php): $config['export']['searchparts'][_HERE_IS_THE_FORMAT_ID_]
 * @param boolean           $send_file              @li If true, the export file will directly send to the user with lib.php::send_string()
 *                                                  @li If false, the export string will be returned
 * @param string            $filename               @li The filename which should be displayed in the download dialog (without extension!)
 *                                                  @li This is only needed if "$send_file == true"
 * @param array             $additional_params      For some things we need more parameters, like the export quantity of DevicePart objects for calculating the total price
 *
 * @return string           The export string (if $send_file is "false")
 *
 * @note    If $send_file is "true", the script will be stopped by "exit;" !
 *
 * @throws Exception if there was an error
 */
function exportParts(&$objects, $export_type, $format_id, $send_file = false, $filename = '', $additional_params = array())
{
    global $config;

    if ((! isset($config['export'][$export_type])) || (! isset($config['export'][$export_type][$format_id]))) {
        throw new Exception('Es gibt kein Exportformat in dieser Variable: $config[\'export\'][\''.$export_type.'\'][\''.$format_id.'\']');
    }

    $format     = strtoupper($config['export'][$export_type][$format_id]['format']);
    $columns    = explode(';', $config['export'][$export_type][$format_id]['columns']);

    // prepare stuff
    switch ($format) {
        case 'CSV':
            $separator = $config['export'][$export_type][$format_id]['separator']; // the separator is needed for CSV export!
            $items_separator = (($separator == ';') ? ',' : ';');
            $show_header = $config['export'][$export_type][$format_id]['header']; // the header is needed for CSV export!
            if ($show_header) {
                $output = '#'.implode($separator, $columns)."\n";
            } // header
            else {
                $output = '';
            }
            break;

        case 'XML':
            $items_separator = ',';
            $dom = new DOMDocument('1.0', 'utf-8');
            $dom->formatOutput = true;
            $root_node = $dom->createElement('parts');
            $dom->appendChild($root_node);
            break;

        default:
            throw new Exception('Nicht unterstütztes Exportformat: "'.$format.'"');
    }

    foreach ($objects as $object) {
        // prepare stuff for the next object
        switch ($format) {
            case 'CSV':
                $column_output = '';
                break;

            case 'XML':
                $object_node = $dom->createElement(strtolower(getClassShort($object)));
                break;
        }

        switch (getClassShort($object)) {
            case 'Part':
                $part = $object;
                break;
            case 'DevicePart':
                $part = $object->getPart();
                $devicepart = $object;
                break;
            default:
                throw new Exception('Klasse "'.getClassShort($object).'" kann nicht exportiert werden (wird nicht unterstützt)!');
                break;
        }

        /* @var \PartDB\Part $part */
        /* @var \PartDB\DevicePart $devicepart */

        for ($i=0; $i<count($columns); $i++) {
            $column = $columns[$i];

            switch ($column) {
                // sometimes empty columns are needed...
                case '':
                    $value = '';
                    break;

                // general parts stuff
                case 'id':
                    $value = $part->getID();
                    break;
                case 'name':
                    $value = $part->getID();
                    break;
                case 'description':
                    $value = $part->getID();
                    break;
                case 'instock':
                    $value = $part->getInstock();
                    break;
                case 'mininstock':
                    $value = $part->getMinInstock();
                    break;
                case 'footprint':
                    if (is_object($part->getFootprint())) {
                        $value = $part->getFootprint()->getName();
                    } else {
                        $value = '';
                    }
                    break;
                case 'footprint_fullpath':
                    if (is_object($part->getFootprint())) {
                        $value = $part->getFootprint()->getFullPath();
                    } else {
                        $value = '';
                    }
                    break;
                case 'manufacturer':
                    if (is_object($part->getManufacturer())) {
                        $value = $part->getManufacturer()->getName();
                    } else {
                        $value = '';
                    }
                    break;
                case 'manufacturer_fullpath':
                    if (is_object($part->getManufacturer())) {
                        $value = $part->getManufacturer()->getFullPath();
                    } else {
                        $value = '';
                    }
                    break;
                case 'storelocation':
                    if (is_object($part->getStorelocation())) {
                        $value = $part->getStorelocation()->getName();
                    } else {
                        $value = '';
                    }
                    break;
                case 'storelocation_fullpath':
                    if (is_object($part->getStorelocation())) {
                        $value = $part->getStorelocation()->getFullPath();
                    } else {
                        $value = '';
                    }
                    break;
                case 'suppliers':
                    $value = $part->getSuppliers(false, $items_separator, false, true);
                    break;
                case 'suppliers_fullpath':
                    $value = $part->getSuppliers(false, $items_separator, true, true);
                    break;
                case 'supplierpartnrs':
                    $value = $part->getSupplierpartnrs($items_separator, true);
                    break;
                case 'average_single_price':
                    $value = $part->getAveragePrice(true);
                    break;
                case 'single_prices':
                    $value = $part->getPrices(false, $items_separator, 1, null, true);
                    break;

                // order parts stuff
                case 'order_supplier':
                    if (is_object($part->getOrderOrderdetails())) {
                        $value = $part->getOrderOrderdetails()->getSupplier()->getName();
                    } else {
                        $value = '';
                    }
                    break;
                case 'order_supplierpartnr':
                    if (is_object($part->getOrderOrderdetails())) {
                        $value = $part->getOrderOrderdetails()->getSupplierPartNr();
                    } else {
                        $value = '';
                    }
                    break;
                case 'order_quantity':
                    $value = $part->getOrderQuantity();
                    break;
                case 'order_single_price': // the single price of the selected orderdetails
                    if (is_object($part->getOrderOrderdetails())) {
                        $value = $part->getOrderOrderdetails()->getPrice(true);
                    } else {
                        $value = '';
                    }
                    break;
                case 'order_total_price': // the total price of the selected orderdetails
                    if (is_object($part->getOrderOrderdetails())) {
                        $value = $part->getOrderOrderdetails()->getPrice(true, $object->getOrderQuantity());
                    } else {
                        $value = '';
                    }
                    break;
                case 'order_total_prices':
                    $value = $part->getPrices(false, $items_separator, $part->getOrderQuantity(), null, true);
                    break;

                    /** @var \PartDB\DevicePart $devicepart */

                // device parts stuff
                case 'mount_quantity':
                    $value = $devicepart->getMountQuantity();
                    break;
                case 'total_mount_quantity':
                    if (! isset($additional_params['export_quantity'])) {
                        throw new Exception('$additional_params[\'export_quantity\'] ist nicht gesetzt!');
                    }
                    $value = $devicepart->getMountQuantity() * (integer)$additional_params['export_quantity'];
                    break;
                case 'mount_names':
                    $value = $devicepart->getMountNames();
                    break;
                case 'total_prices':
                    if (! isset($additional_params['export_quantity'])) {
                        throw new Exception('$additional_params[\'export_quantity\'] ist nicht gesetzt!');
                    }
                    $value = $part->getPrices(false, $items_separator, $devicepart->getMountQuantity() * (integer)$additional_params['export_quantity'], null, true);
                    break;

                // unknown column
                default:
                    throw new Exception('Nicht unterstützte Spalte: "'.$column.'"');
                    break;
            }

            // finish stuff for that column
            switch ($format) {
                case 'CSV':
                    $column_output .= str_replace($separator, ' ', $value);
                    if ($i < (count($columns) - 1)) {
                        $column_output .= $separator;
                    } // Add the separator if this is not the last column
                    break;

                case 'XML':
                    $column_node = $dom->createElement($column, $value);
                    $object_node->appendChild($column_node); // append the new column child to the object node
                    break;
            }
        }

        // finish stuff for that object (only if that object has to be exported!)
        if (((! in_array('order_supplier', $columns)) && (! in_array('order_supplierpartnr', $columns))
                && (! in_array('order_single_price', $columns)) && (! in_array('order_total_price', $columns)))
            || (isset($part) && is_object($part->getOrderOrderdetails()))) {
            switch ($format) {
                case 'CSV':
                    $output .= $column_output."\n"; // Add a line break
                    break;

                case 'XML':
                    $root_node->appendChild($object_node); // append the new object node to the root node
                    break;
            }
        }
    }

    // finish stuff
    switch ($format) {
        case 'CSV':
            // nothing to do, the CSV is already in $output
            break;

        case 'XML':
            $output = $dom->saveXML($dom, LIBXML_NOEMPTYTAG); // write the XML from the DOMDocument to $output
            break;
    }

    // send file or return string
    if ($send_file) {
        $mimetype = $config['export'][$export_type][$format_id]['mimetype'];
        $filename .= '.'.substr($mimetype, strpos($mimetype, '/') + 1);
        sendString($output, $filename, $mimetype); // in this function is an "exit;" !
        return ""; //Useless but its suppresses warnings.
    } else {
        return $output;
    }
}
