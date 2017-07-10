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
     * @brief Build a template loop for a <select> list of export formats
     *
     * @param string            $export_type        An export type which is defined in config_defaults.php ($config['export'][_HERE_IS_THE_EXPORT_TYPE_][0])
     * @param integer|string    $selected_index     The ID of the selected export type (from config_defaults.php): $config['export']['searchparts'][_HERE_IS_THE_FORMAT_ID_]
     *
     * @retval array    The template loop
     */
    function build_export_formats_loop($export_type, $selected_index = 0)
    {
        global $config;

        if ( ! ctype_alpha($selected_index))
            settype($selected_index, 'integer');

        $loop = array();
        foreach ($config['export'][$export_type] as $key => $value)
            $loop[] = array('value' => $key, 'text' => '['.$value['format'].'] '.$value['name'], 'selected' => ($key === $selected_index));

        return $loop;
    }

    /**
     * @brief Export Part or DevicePart Objects
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
     * @retval string           The export string (if $send_file is "false")
     *
     * @note    If $send_file is "true", the script will be stopped by "exit;" !
     *
     * @throws Exception if there was an error
     */
    function export_parts(&$objects, $export_type, $format_id, $send_file = false, $filename = '', $additional_params = array())
    {
        global $config;

        if (( ! isset($config['export'][$export_type])) || ( ! isset($config['export'][$export_type][$format_id])))
            throw new Exception('Es gibt kein Exportformat in dieser Variable: $config[\'export\'][\''.$export_type.'\'][\''.$format_id.'\']');

        $format     = strtoupper($config['export'][$export_type][$format_id]['format']);
        $columns    = explode(';', $config['export'][$export_type][$format_id]['columns']);

        // prepare stuff
        switch ($format)
        {
            case 'CSV':
                $separator = $config['export'][$export_type][$format_id]['separator']; // the separator is needed for CSV export!
                $items_separator = (($separator == ';') ? ',' : ';');
                $show_header = $config['export'][$export_type][$format_id]['header']; // the header is needed for CSV export!
                if ($show_header)
                    $output = '#'.implode($separator, $columns)."\n"; // header
                else
                    $output = '';
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

        foreach ($objects as $object)
        {
            // prepare stuff for the next object
            switch ($format)
            {
                case 'CSV':
                    $column_output = '';
                    break;

                case 'XML':
                    $object_node = $dom->createElement(strtolower(get_class($object)));
                    break;
            }

            switch (get_class($object))
            {
                case 'Part':
                    $part = $object;
                    break;
                case 'DevicePart':
                    $part = $object->get_part();
                    $devicepart = $object;
                    break;
                default:
                    throw new Exception('Klasse "'.get_class($object).'" kann nicht exportiert werden (wird nicht unterstützt)!');
                    break;
            }


            for ($i=0; $i<count($columns); $i++)
            {
                $column = $columns[$i];

                switch ($column)
                {
                    // sometimes empty columns are needed...
                    case '':
                        $value = '';
                        break;

                    // general parts stuff
                    case 'id':
                        $value = $part->get_id();
                        break;
                    case 'name':
                        $value = $part->get_name();
                        break;
                    case 'description':
                        $value = $part->get_description();
                        break;
                    case 'instock':
                        $value = $part->get_instock();
                        break;
                    case 'mininstock':
                        $value = $part->get_mininstock();
                        break;
                    case 'footprint':
                        if (is_object($part->get_footprint()))
                            $value = $part->get_footprint()->get_name();
                        else
                            $value = '';
                        break;
                    case 'footprint_fullpath':
                        if (is_object($part->get_footprint()))
                            $value = $part->get_footprint()->get_full_path();
                        else
                            $value = '';
                        break;
                    case 'manufacturer':
                        if (is_object($part->get_manufacturer()))
                            $value = $part->get_manufacturer()->get_name();
                        else
                            $value = '';
                        break;
                    case 'manufacturer_fullpath':
                        if (is_object($part->get_manufacturer()))
                            $value = $part->get_manufacturer()->get_full_path();
                        else
                            $value = '';
                        break;
                    case 'storelocation':
                        if (is_object($part->get_storelocation()))
                            $value = $part->get_storelocation()->get_name();
                        else
                            $value = '';
                        break;
                    case 'storelocation_fullpath':
                        if (is_object($part->get_storelocation()))
                            $value = $part->get_storelocation()->get_full_path();
                        else
                            $value = '';
                        break;
                    case 'suppliers':
                        $value = $part->get_suppliers(false, $items_separator, false, true);
                        break;
                    case 'suppliers_fullpath':
                        $value = $part->get_suppliers(false, $items_separator, true, true);
                        break;
                    case 'supplierpartnrs':
                        $value = $part->get_supplierpartnrs($items_separator, true);
                        break;
                    case 'average_single_price':
                        $value = $part->get_average_price(true);
                        break;
                    case 'single_prices':
                        $value = $part->get_prices(false, $items_separator, 1, NULL, true);
                        break;

                    // order parts stuff
                    case 'order_supplier':
                        if (is_object($part->get_order_orderdetails()))
                            $value = $part->get_order_orderdetails()->get_supplier()->get_name();
                        else
                            $value = '';
                        break;
                    case 'order_supplierpartnr':
                        if (is_object($part->get_order_orderdetails()))
                            $value = $part->get_order_orderdetails()->get_supplierpartnr();
                        else
                            $value = '';
                        break;
                    case 'order_quantity':
                        $value = $part->get_order_quantity();
                        break;
                    case 'order_single_price': // the single price of the selected orderdetails
                        if (is_object($part->get_order_orderdetails()))
                            $value = $part->get_order_orderdetails()->get_price(true);
                        else
                            $value = '';
                        break;
                    case 'order_total_price': // the total price of the selected orderdetails
                        if (is_object($part->get_order_orderdetails()))
                            $value = $part->get_order_orderdetails()->get_price(true, $object->get_order_quantity());
                        else
                            $value = '';
                        break;
                    case 'order_total_prices':
                        $value = $part->get_prices(false, $items_separator, $part->get_order_quantity(), NULL, true);
                        break;

                    // device parts stuff
                    case 'mount_quantity':
                        $value = $devicepart->get_mount_quantity();
                        break;
                    case 'total_mount_quantity':
                        if ( ! isset($additional_params['export_quantity']))
                            throw new Exception('$additional_params[\'export_quantity\'] ist nicht gesetzt!');
                        $value = $devicepart->get_mount_quantity() * (integer)$additional_params['export_quantity'];
                        break;
                    case 'mount_names':
                        $value = $devicepart->get_mount_names();
                        break;
                    case 'total_prices':
                        if ( ! isset($additional_params['export_quantity']))
                            throw new Exception('$additional_params[\'export_quantity\'] ist nicht gesetzt!');
                        $value = $part->get_prices(false, $items_separator, $devicepart->get_mount_quantity() * (integer)$additional_params['export_quantity'], NULL, true);
                        break;

                    // unknown column
                    default:
                        throw new Exception('Nicht unterstützte Spalte: "'.$column.'"');
                        break;
                }

                // finish stuff for that column
                switch ($format)
                {
                    case 'CSV':
                        $column_output .= str_replace($separator, ' ', $value);
                        if ($i < (count($columns) - 1))
                            $column_output .= $separator; // Add the separator if this is not the last column
                        break;

                    case 'XML':
                        $column_node = $dom->createElement($column, $value);
                        $object_node->appendChild($column_node); // append the new column child to the object node
                        break;
                }
            }

            // finish stuff for that object (only if that object has to be exported!)
            if ((( ! in_array('order_supplier', $columns)) && ( ! in_array('order_supplierpartnr', $columns))
                && ( ! in_array('order_single_price', $columns)) && ( ! in_array('order_total_price', $columns)))
                || (isset($part) && is_object($part->get_order_orderdetails())))
            {
                switch ($format)
                {
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
        switch ($format)
        {
            case 'CSV':
                // nothing to do, the CSV is already in $output
                break;

            case 'XML':
                $output = $dom->saveXML($dom, LIBXML_NOEMPTYTAG); // write the XML from the DOMDocument to $output
                break;
        }

        // send file or return string
        if ($send_file)
        {
            $mimetype = $config['export'][$export_type][$format_id]['mimetype'];
            $filename .= '.'.substr($mimetype, strpos($mimetype, '/') + 1);
            send_string($output, $filename, $mimetype); // in this function is an "exit;" !
            return ""; //Useless but its suppresses warnings.
        }
        else
            return $output;
    }
