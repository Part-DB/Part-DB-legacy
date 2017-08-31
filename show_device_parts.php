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

include_once('start_session.php');
/** @noinspection PhpIncludeInspection */
include_once(BASE.'/inc/lib.export.php');
include_once(BASE.'/inc/lib.import.php');

use PartDB\Database;
use PartDB\Device;
use PartDB\DevicePart;
use PartDB\HTML;
use PartDB\Log;
use PartDB\Part;
use PartDB\User;

$messages = array();
$fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

/********************************************************************************
 *
 *   Evaluate $_REQUEST
 *
 *********************************************************************************/

// for all sections
$device_id                = isset($_REQUEST['device_id'])               ? (integer)$_REQUEST['device_id']               : 0;

// sections "search parts" and "parts table"
$new_part_name            = isset($_REQUEST['new_part_name'])           ? (string)$_REQUEST['new_part_name']            : '';
$searched_parts_rowcount  = isset($_REQUEST['searched_parts_rowcount']) ? (integer)$_REQUEST['searched_parts_rowcount'] : 0;
$device_parts_rowcount    = isset($_REQUEST['device_parts_rowcount'])   ? (integer)$_REQUEST['device_parts_rowcount']   : 0;

// section "export"
$export_multiplier        = isset($_REQUEST['export_multiplier'])       ? abs((integer)$_REQUEST['export_multiplier'])  : 0;
$export_multiplier_original = $export_multiplier; // for HTML->set_variable(), because $export_multiplier will be edited in this script
$export_format_id         = isset($_REQUEST['export_format'])           ? (integer)$_REQUEST['export_format']           : 0;
$export_only_missing      = isset($_REQUEST['only_missing_material']);

// section "import"
$import_file_content      = isset($_REQUEST['import_file_content'])     ? (string)$_REQUEST['import_file_content']      : '';
$import_format            = isset($_REQUEST['import_format'])           ? (string)$_REQUEST['import_format']            : 'CSV';
$import_separator         = isset($_REQUEST['import_separator'])        ? trim((string)$_REQUEST['import_separator'])   : ';';
$import_rowcount          = isset($_REQUEST['import_rowcount'])         ? (integer)$_REQUEST['import_rowcount']         : 0;

// section "copy device"
$copy_new_name            = isset($_REQUEST['copy_new_name'])           ? (string)$_REQUEST['copy_new_name']            : '';
$copy_new_parent_id       = isset($_REQUEST['copy_new_parent_id'])      ? (integer)$_REQUEST['copy_new_parent_id']      : 0;
$copy_recursive           = isset($_REQUEST['copy_recursive']);


$action = 'default';
if (isset($_REQUEST['show_searched_parts'])) {
    $action = 'show_searched_parts';
}
if (isset($_REQUEST['assign_by_selected'])) {
    $action = 'assign_by_selected';
}
if (isset($_REQUEST['device_parts_apply'])) {
    $action = 'device_parts_apply';
}
if (isset($_REQUEST['book_parts'])) {
    $action = 'book_parts';
}
if (isset($_REQUEST['book_parts_in'])) {
    $action = 'book_parts';
    $export_multiplier *= -1;
}
if (isset($_REQUEST['add_order'])) {
    $action = 'add_order';
}
if (isset($_REQUEST['add_order_only_missing'])) {
    $action = 'add_order';
}
if (isset($_REQUEST['remove_order'])) {
    $action = 'add_order';
    $export_multiplier = 0;
}
if (isset($_REQUEST['copy_device'])) {
    $action = 'copy_device';
}
if (isset($_REQUEST['export_show'])) {
    $action = 'export';
}
if (isset($_REQUEST['export_download'])) {
    $action = 'export';
}
if (isset($_REQUEST['import_readtext'])) {
    $action = 'import_readtext';
}
if (isset($_REQUEST['check_import_data'])) {
    $action = 'import';
}
if (isset($_REQUEST['import_data'])) {
    $action = 'import';
}

/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $config['html']['custom_css'], 'Baugruppe');

try {
    $database           = new Database();
    $log                = new Log($database);
    $current_user       = new User($database, $current_user, $log, 1); // admin
    $root_device        = new Device($database, $current_user, $log, 0);
    $device             = new Device($database, $current_user, $log, $device_id);
    $subdevices         = $device->getSubelements(false);
} catch (Exception $e) {
    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
    $fatal_error = true;
}

/********************************************************************************
 *
 *   Execute actions
 *
 *********************************************************************************/

if (! $fatal_error) {
    switch ($action) {
        case 'show_searched_parts': // show the search results for adding parts to this device
            try {
                // search parts by name and description
                $searched_parts = Part::searchParts(
                    $database,
                    $current_user,
                    $log,
                    $new_part_name,
                    '',
                    true,
                    true,
                    false,
                    false,
                    false,
                    false,
                    false,
                    false
                );

                $searched_parts_loop = Part::buildTemplateTableArray($searched_parts, 'searched_device_parts');
                $html->setVariable('searched_parts_rowcount', count($searched_parts), 'integer');
                $html->setVariable('no_searched_parts_found', (count($searched_parts) == 0), 'integer');
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        case 'assign_by_selected': // add some parts (which were listed by part search) to this device
            for ($i=0; $i<$searched_parts_rowcount; $i++) {
                $part_id    = isset($_REQUEST['id_'.$i])           ? (integer)$_REQUEST['id_'.$i]              : 0;
                $quantity   = isset($_REQUEST['quantity_'.$i])     ? abs((integer)$_REQUEST['quantity_'.$i])   : 0;
                $mountname  = isset($_REQUEST['mountnames_'.$i])   ? trim((string)$_REQUEST['mountnames_'.$i]) : '';

                if ($quantity > 0) {
                    try {
                        // if there is already such Part in this Device, the quantity will be increased
                        $device_part = DevicePart::add(
                            $database,
                            $current_user,
                            $log,
                            $device_id,
                            $part_id,
                            $quantity,
                            $mountname,
                            true
                        );
                    } catch (Exception $e) {
                        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                    }
                }
            }

            if (count($messages) == 0) {
                $reload_site = true;
            }
            break;

        case 'device_parts_apply': // apply new quantities and new mountnames, or remove parts from this device
            for ($i=0; $i<$device_parts_rowcount; $i++) {
                $part_id    = isset($_REQUEST['id_'.$i])           ? (integer)$_REQUEST['id_'.$i]              : 0;
                $quantity   = isset($_REQUEST['quantity_'.$i])     ? abs((integer)$_REQUEST['quantity_'.$i])   : 0;
                $mountname  = isset($_REQUEST['mountnames_'.$i])   ? trim((string)$_REQUEST['mountnames_'.$i]) : '';

                try {
                    $device_part = new DevicePart($database, $current_user, $log, $part_id);

                    if ($quantity > 0) {
                        $device_part->setAttributes(array('quantity' => $quantity, 'mountnames' => $mountname));
                    } else {
                        $device_part->delete();
                    } // remove the part from this device
                } catch (Exception $e) {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
            }

            if (count($messages) == 0) {
                $reload_site = true;
            }
            break;

        case 'book_parts': // book parts from this device (decrease "instock" of all parts in this device)
            try {
                $device->bookParts($export_multiplier);
                $reload_site = true;
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        case 'add_order': // mark this device as "to order" (then the parts of this device will be shown in "parts to order")
            try {
                $device->setOrderQuantity($export_multiplier);
                $device->setOrderOnlyMissingParts(isset($_REQUEST['add_order_only_missing']));
                $reload_site = true;
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        case 'copy_device': // make a copy of this device (including all parts)
            try {
                $device->copy($copy_new_name, $copy_new_parent_id, $copy_recursive);
                $html->setVariable('refresh_navigation_frame', true, 'boolean');
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        case 'export':
            try {
                $device_parts = $device->getParts();

                if ($export_only_missing) {
                    foreach ($device_parts as $key => $devicepart) {
                        /** @var DevicePart $devicepart */
                        $needed = $devicepart->getMountQuantity() * $export_multiplier;
                        $instock = $devicepart->getPart()->getInstock();
                        $mininstock = $devicepart->getPart()->getMinInstock();

                        if ($instock - $needed >= $mininstock) {
                            unset($device_parts[$key]);
                        }
                    }
                }

                $download = isset($_REQUEST['export_download']);
                $export_string = exportParts(
                    $device_parts,
                    'deviceparts',
                    $export_format_id,
                    $download,
                    'deviceparts_'.$device->getName(),
                    array('export_quantity' => $export_multiplier)
                );
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        case 'import_readtext':
            try {
                $import_data = importTextToArray($import_file_content, $import_format, $import_separator);
                matchDevicepartNamesToIds($database, $current_user, $log, $import_data);
                $import_loop = buildDevicepartsImportTemplateLoop($database, $current_user, $log, $import_data);
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        case 'import':
            $only_check_data = isset($_REQUEST['check_import_data']);
            try {
                $import_data = extractImportDataFromRequest($import_rowcount);
                $import_loop = buildDevicepartsImportTemplateLoop($database, $current_user, $log, $import_data);

                importDeviceParts($database, $current_user, $log, $device->getID(), $import_data, $only_check_data);
                $import_data_is_valid = true; // no exception in "import_device_parts()", so the data is valid

                if (! $only_check_data) {
                    // clear import variables, so the import table is no longer visible in the HTML output
                    $import_file_content = '';
                    unset($import_data);
                    unset($import_loop);
                }
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;
    }
}

if (isset($reload_site) && $reload_site) {
    // reload the site to avoid multiple actions by manual refreshing
    header('Location: show_device_parts.php?device_id='.$device_id.'&export_multiplier='.$export_multiplier_original);
}

/********************************************************************************
 *
 *   Generate Subdevices Table
 *
 *********************************************************************************/

if (! $fatal_error) {
    try {
        $subdevices_loop = array();
        $row_odd = true;
        foreach ($subdevices as $subdevice) {
            $subdevices_loop[] = array(
                'row_odd'               => $row_odd,
                'id'                    => $subdevice->getID(),
                'name'                  => $subdevice->getName(),
                'parts_count'           => $subdevice->getPartsCount(),
                'parts_sum_count'       => $subdevice->getPartsSumCount(),
                'sum_price'             => $subdevice->getTotalPrice(true, false)
            );

            $row_odd = ! $row_odd;
        }
        $html->setLoop('subdevices', $subdevices_loop);
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        $fatal_error = true;
    }
}

/********************************************************************************
 *
 *   Generate DeviceParts Table
 *
 *********************************************************************************/

if (! $fatal_error) {
    try {
        $device_parts = $device->getParts();
        // don't forget: $device_parts contains "DevicePart"-objects, not "Part"-objects!!
        $device_parts_loop = DevicePart::buildTemplateTableArray($device_parts, 'device_parts');

        $html->setVariable('device_parts_rowcount', count($device_parts), 'integer');
        $html->setVariable('sum_price', $device->getTotalPrice(true, false), 'string');
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        $fatal_error = true;
    }
}

/********************************************************************************
 *
 *   Set the rest of the HTML variables
 *
 *********************************************************************************/
if (! $fatal_error) {
    // global stuff
    $html->setVariable('disable_footprints', $config['footprints']['disable'], 'boolean');
    $html->setVariable('disable_manufacturers', $config['manufacturers']['disable'], 'boolean');
    $html->setVariable('disable_auto_datasheets', $config['auto_datasheets']['disable'], 'boolean');

    $html->setVariable('use_modal_popup', $config['popup']['modal'], 'boolean');
    $html->setVariable('popup_width', $config['popup']['width'], 'integer');
    $html->setVariable('popup_height', $config['popup']['height'], 'integer');

    // device stuff
    $html->setVariable('device_id', $device->getID(), 'integer');
    $html->setVariable('device_name', $device->getName(), 'string');

    $parent_device_list = $root_device->buildHtmlTree($device->getParentID(), true, true);
    $html->setVariable('parent_device_list', $parent_device_list, 'string');

    // export stuff
    $html->setVariable('export_multiplier', $export_multiplier_original, 'integer');
    $html->setVariable('order_quantity', $device->getOrderQuantity(), 'integer');
    $html->setVariable('order_only_missing_parts', $device->getOrderOnlyMissingParts(), 'boolean');
    $html->setVariable('export_only_missing', $export_only_missing, 'boolean');
    $html->setLoop('export_formats', buildExportFormatsLoop('deviceparts', $export_format_id));
    if (isset($export_string)) {
        $html->setVariable('export_result', str_replace("\n", '<br>', str_replace("\n  ", '<br>&nbsp;&nbsp;',   // yes, this is quite ugly,
            str_replace("\n    ", '<br>&nbsp;&nbsp;&nbsp;&nbsp;',               // but the result is pretty ;-)
                htmlspecialchars($export_string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')))), 'string');
    }

    // import stuff
    $html->setVariable('import_rowcount', (isset($import_data) ? count($import_data) : 0), 'integer');
    $html->setVariable('import_file_content', $import_file_content, 'string');
    $html->setVariable('import_format', $import_format, 'string');
    $html->setVariable('import_separator', $import_separator, 'string');
    //$html->set_variable('import_data_is_valid',     (isset($import_data_is_valid) && ($import_data_is_valid)), 'boolean');
}

/********************************************************************************
 *
 *   Generate HTML Output
 *
 *********************************************************************************/


//If a ajax version is requested, say this the template engine.
if (isset($_REQUEST["ajax"])) {
    $html->setVariable("ajax_request", true);
}

$reload_link = $fatal_error ? 'show_device_parts.php?devid='.$device_id : ''; // an empty string means that the...
$html->printHeader($messages, $reload_link);                                 // ...reload-button won't be visible

if (! $fatal_error) {
    if ((count($subdevices_loop) > 0) || ($device_id == 0)) {
        $html->printTemplate('subdevices');
    }

    if ($device_id > 0) {
        $html->setLoop('table', (isset($searched_parts_loop) ? $searched_parts_loop : array()));
        $html->printTemplate('add_parts');

        $html->setLoop('table', $device_parts_loop);
        $html->printTemplate('device_parts');

        $html->printTemplate('export');

        $html->setLoop('table', (isset($import_loop) ? $import_loop : array()));
        $html->printTemplate('import');

        $html->printTemplate('copy_device');
    }
}

$html->printFooter();
