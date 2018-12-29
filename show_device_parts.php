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
/** @noinspection PhpIncludeInspection */
include_once(BASE.'/inc/lib.import.php');

use PartDB\Attachment;
use PartDB\Database;
use PartDB\Device;
use PartDB\DevicePart;
use PartDB\HTML;
use PartDB\Log;
use PartDB\Part;
use PartDB\Permissions\DevicePartPermission;
use PartDB\Permissions\PartAttributePermission;
use PartDB\Permissions\PermissionManager;
use PartDB\Permissions\StructuralPermission;
use PartDB\User;

$messages = array();
$fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

/********************************************************************************
 *
 *   Evaluate $_REQUEST
 *
 *********************************************************************************/

// for all sections
$device_id                = isset($_REQUEST['device_id'])               ? (int)$_REQUEST['device_id']               : 0;

// sections "search parts" and "parts table"
$new_part_name            = isset($_POST['new_part_name'])           ? (string)$_POST['new_part_name']            : '';
$searched_parts_rowcount  = isset($_POST['searched_parts_rowcount']) ? (int)$_POST['searched_parts_rowcount'] : 0;
$device_parts_rowcount    = isset($_POST['device_parts_rowcount'])   ? (int)$_POST['device_parts_rowcount']   : 0;

// section "export"
$export_multiplier        = isset($_POST['export_multiplier'])       ? abs((int)$_POST['export_multiplier'])  : 0;
$export_multiplier_original = $export_multiplier; // for HTML->set_variable(), because $export_multiplier will be edited in this script
$export_format_id         = isset($_POST['export_format'])           ? (int)$_POST['export_format']           : 0;
$export_only_missing      = isset($_POST['only_missing_material']);

// section "import"
$import_file_content      = isset($_POST['import_file_content'])     ? (string)$_POST['import_file_content']      : '';
$import_format            = isset($_POST['import_format'])           ? (string)$_POST['import_format']            : 'CSV';
$import_separator         = isset($_POST['import_separator'])        ? trim((string)$_POST['import_separator'])   : ';';
$import_rowcount          = isset($_POST['import_rowcount'])         ? (int)$_POST['import_rowcount']         : 0;

// section "copy device"
$copy_new_name            = isset($_POST['copy_new_name'])           ? (string)$_POST['copy_new_name']            : '';
$copy_new_parent_id       = isset($_POST['copy_new_parent_id'])      ? (int)$_POST['copy_new_parent_id']      : 0;
$copy_recursive           = isset($_POST['copy_recursive']);

// section: attachements
$new_show_in_table          = isset($_POST['show_in_table']);
$attachement_id             = isset($_POST['attachement_id'])            ? (int)$_POST['attachement_id']           : 0;
$new_attachement_type_id    = isset($_POST['attachement_type_id'])       ? (int)$_POST['attachement_type_id']      : 0;
$new_name                   = isset($_POST['name'])                      ? (string)$_POST['name']                      : '';
$new_filename               = isset($_POST['attachement_filename'])      ? toUnixPath(trim((string)$_POST['attachement_filename'])) : '';
$download_file              = isset($_POST['download_file']);


$action = 'default';
if (isset($_POST['show_searched_parts'])) {
    $action = 'show_searched_parts';
}
if (isset($_POST['assign_by_selected'])) {
    $action = 'assign_by_selected';
}
if (isset($_POST['device_parts_apply'])) {
    $action = 'device_parts_apply';
}
if (isset($_POST['book_parts'])) {
    $action = 'book_parts';
}
if (isset($_POST['book_parts_in'])) {
    $action = 'book_parts';
    $export_multiplier *= -1;
}
if (isset($_POST['add_order'])) {
    $action = 'add_order';
}
if (isset($_POST['add_order_only_missing'])) {
    $action = 'add_order';
}
if (isset($_POST['remove_order'])) {
    $action = 'add_order';
    $export_multiplier = 0;
}
if (isset($_POST['copy_device'])) {
    $action = 'copy_device';
}
if (isset($_POST['export_show'])) {
    $action = 'export';
}
if (isset($_POST['export_download'])) {
    $action = 'export';
}
if (isset($_POST['import_readtext'])) {
    $action = 'import_readtext';
}
if (isset($_POST['check_import_data'])) {
    $action = 'import';
}
if (isset($_POST['import_data'])) {
    $action = 'import';
}
if (isset($_POST['primary_device'])) {
    $action = "primary_device";
}

if (isset($_POST["attachement_add"])) {
    $action = 'attachement_add';
}
if (isset($_POST["attachement_apply"])) {
    $action = 'attachement_apply';
}
if (isset($_POST["attachement_delete"])) {
    $action = 'attachement_delete';
}

/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $user_config['theme'], _('Baugruppe'));

try {
    $database           = new Database();
    $log                = new Log($database);
    $current_user       = User::getLoggedInUser($database, $log);
    $root_device        = Device::getInstance($database, $current_user, $log, 0);
    $device             = Device::getInstance($database, $current_user, $log, $device_id);
    $subdevices         = $device->getSubelements(false);

    $root_attachement_type   = \PartDB\AttachmentType::getInstance($database, $current_user, $log, 0);

    //Check for Device parts read permission, when on Device detail page.
    if ($device_id > 0) {
        $current_user->tryDo(PermissionManager::DEVICE_PARTS, DevicePartPermission::READ);

        $html->setTitle(_("Baugruppe"). ": " . $device->getName());
    }

    if ($attachement_id > 0) {
        $attachement = Attachment::getInstance($database, $current_user, $log, $attachement_id);
    } else {
        $attachement = null;
    }

    //Remember what page user visited, so user can return there, when he deletes a part.
    session_start();
    $_SESSION["part_delete_last_link"] = $_SERVER['REQUEST_URI'];
    session_write_close();
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
                    true,
                    true,
                    true,
                    true,
                    false,
                    true
                );

                $searched_parts_loop = Part::buildTemplateTableArray($searched_parts, 'searched_device_parts');
                $html->setVariable('searched_parts_rowcount', count($searched_parts), 'integer');
                $html->setVariable('no_searched_parts_found', (count($searched_parts) == 0), 'integer');
                $html->setVariable("part_name_search", $new_part_name);
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        case 'assign_by_selected': // add some parts (which were listed by part search) to this device
            for ($i=0; $i<$searched_parts_rowcount; $i++) {
                $part_id    = isset($_POST['id_'.$i])           ? (int)$_POST['id_'.$i]              : 0;
                $quantity   = isset($_POST['quantity_'.$i])     ? abs((int)$_POST['quantity_'.$i])   : 0;
                $mountname  = isset($_POST['mountnames_'.$i])   ? trim((string)$_POST['mountnames_'.$i]) : '';

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
                $part_id    = isset($_POST['id_'.$i])           ? (int)$_POST['id_'.$i]              : 0;
                $quantity   = isset($_POST['quantity_'.$i])     ? abs((int)$_POST['quantity_'.$i])   : 0;
                $mountname  = isset($_POST['mountnames_'.$i])   ? trim((string)$_POST['mountnames_'.$i]) : '';

                try {
                    $device_part = DevicePart::getInstance($database, $current_user, $log, $part_id);

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
                $device->setOrderOnlyMissingParts(isset($_POST['add_order_only_missing']));
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

                $download = isset($_POST['export_download']);
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
            $only_check_data = isset($_POST['check_import_data']);
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
        case 'primary_device':
            $id = (int) $_POST['primary_device'];
            if ($id > 0) {
                Device::setPrimaryDevice($id);
            }
            break;
        case 'attachement_add':
            try {
                if (empty($new_filename) && (isset($_FILES['attachement_file']) && strlen($_FILES['attachement_file']['name']) == 0)) {
                    throw new Exception(_('Sie müssen entweder ein Dateiname angeben, oder eine Datei zum Hochladen wählen!'));
                }

                $filepath = $config['attachements']['folder_structure'] ? generateAttachementPath(BASE."/data/media/devices/", $device) : BASE.'/data/media/';

                if (isset($_FILES['attachement_file']) && strlen($_FILES['attachement_file']['name']) > 0) {
                    $new_filename = uploadFile($_FILES['attachement_file'], $filepath);
                } else { //If no file was uploaded, check if the download Flag was set and the filename is a valid URL.
                    if (isURL($new_filename) && $download_file) {
                        $downloaded_file_name =  downloadFile($new_filename, $filepath);
                        if ($downloaded_file_name !== "") {
                            $new_filename = $downloaded_file_name;
                        } else {
                            $messages[] = array('text' => _("Die Datei konnte nicht heruntergeladen werden!"), 'strong' => true, 'color' => 'red');
                        }
                    }
                }

                $new_attachement = Attachment::add(
                    $database,
                    $current_user,
                    $log,
                    $device,
                    $new_attachement_type_id,
                    $new_filename,
                    $new_name,
                    $new_show_in_table
                );
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        case 'attachement_apply':
            try {
                if (! is_object($attachement)) {
                    throw new Exception(_('Es ist kein Dateianhang ausgewählt!'));
                }

                $filepath = $config['attachements']['folder_structure'] ? generateAttachementPath(BASE."/data/media/devices/", $device) : BASE.'/data/media/';

                if (isset($_FILES['attachement_file']) && strlen($_FILES['attachement_file']['name']) > 0) {
                    $new_filename = uploadFile($_FILES['attachement_file'], $filepath);
                } else { //If no file was uploaded, check if the download Flag was set and the filename is a valid URL.
                    if (isURL($new_filename) && $download_file) {
                        $downloaded_file_name =  downloadFile($new_filename, $filepath);
                        if ($downloaded_file_name !== "") {
                            $new_filename = $downloaded_file_name;
                        } else {
                            $messages[] = array('text' => _("Die Datei konnte nicht heruntergeladen werden!"), 'strong' => true, 'color' => 'red');
                        }
                    }
                }

                $attachement->setAttributes(array( 'type_id'           => $new_attachement_type_id,
                    'name'              => $new_name,
                    'filename'          => $new_filename));
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        case 'attachement_delete':
            try {
                if (! is_object($attachement)) {
                    throw new Exception(_('Es ist kein Dateianhang ausgewählt!'));
                }
                $attachement->delete(true); // the file will be deleted only if there are no other attachements with the same filename
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
                'sum_price'             => $subdevice->getTotalPrice(true, false),
                'is_primary'            => $subdevice->getID() == Device::getPrimaryDevice()
            );

            $row_odd = ! $row_odd;
        }
        $html->setVariable('subdevices', $subdevices_loop);
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

//Generate the parts list, only if a device id was set.
if (! $fatal_error && $device_id > 0) {
    try {
        $device_parts = $device->getParts();
        // don't forget: $device_parts contains "DevicePart"-objects, not "Part"-objects!!
        $device_parts_loop = DevicePart::buildTemplateTableArray($device_parts, 'device_parts');

        $comment = $device->getComment(true);

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
    try {
        $html->setVariable('disable_footprints', $config['footprints']['disable'], 'boolean');
        $html->setVariable('disable_manufacturers', $config['manufacturers']['disable'], 'boolean');
        $html->setVariable('disable_auto_datasheets', $config['auto_datasheets']['disable'], 'boolean');

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
        $html->setVariable('export_formats', buildExportFormatsLoop('deviceparts', $export_format_id));
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

        $attachements_loop = array();
        $all_attachements = $device->getAttachments();
        $row_odd = true;
        foreach ($all_attachements as $attachement) {
            /** @var  $attachement Attachment */
            $attachement_types_list = $root_attachement_type->buildHtmlTree($attachement->getType()->getID(), true, false);
            $attachements_loop[] = array('row_odd' => $row_odd,
                'id' => $attachement->getID(),
                'attachement_types_list' => $attachement_types_list,
                'name' => $attachement->getName(),
                'show_in_table' => $attachement->getShowInTable(),
                'is_picture' => $attachement->isPicture(),
                'filename' => str_replace(BASE, BASE_RELATIVE, $attachement->getFilename()),
                'filename_base_relative' => str_replace(BASE . '/', '', $attachement->getFilename()),
                'picture_filename' => ($attachement->isPicture() ? str_replace(BASE, BASE_RELATIVE, $attachement->getFilename()) : ''),
                'download_file' => $config['attachements']['download_default'] && isURL($attachement->getFilename()));
            $row_odd = !$row_odd;
        }

        // add one additional row -> with this row you can add more files
        $attachement_types_list = $root_attachement_type->buildHtmlTree(0, true, false);
        $attachements_loop[] = array('row_odd' => $row_odd,
            'id' => 'new',
            'attachement_types_list' => $attachement_types_list,
            'name' => '',
            'is_picture' => true,
            'show_in_table' => false,
            'is_master_picture' => false,
            'filename' => '',
            'filename_base_relative' => '',
            'picture_filename' => '',
            'download_file' => $config['attachements']['download_default']);

        $html->setVariable('attachements_loop', $attachements_loop);
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red', );
        $fatal_error = true;
    }
}

try {
    $html->setVariable("can_part_create", $current_user->canDo(PermissionManager::DEVICE_PARTS, DevicePartPermission::CREATE));
    $html->setVariable("can_part_edit", $current_user->canDo(PermissionManager::DEVICE_PARTS, DevicePartPermission::EDIT));
    $html->setVariable("can_part_delete", $current_user->canDo(PermissionManager::DEVICE_PARTS, DevicePartPermission::DELETE));

    $html->setVariable('can_part_instock', $current_user->canDo(PermissionManager::PARTS_INSTOCK, PartAttributePermission::EDIT));
    $html->setVariable('can_part_order', $current_user->canDo(PermissionManager::PARTS_ORDER, PartAttributePermission::EDIT));
    $html->setVariable('can_devices_add', $current_user->canDo(PermissionManager::DEVICES, StructuralPermission::CREATE));

    $html->setVariable('can_attachement_edit', $current_user->canDo(PermissionManager::DEVICES, StructuralPermission::EDIT));
    $html->setVariable('max_upload_filesize', ini_get('upload_max_filesize'), 'string');
    $html->setVariable('downloads_enable', $config['allow_server_downloads'], "boolean");
} catch (Exception $e) {
    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red', );
    $fatal_error = true;
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
        $html->setVariable('table', (isset($searched_parts_loop) ? $searched_parts_loop : array()));
        $html->printTemplate('add_parts');

        $html->setVariable('table', $device_parts_loop);
        $html->printTemplate('device_parts');

        if (isset($comment)) {
            $html->setVariable('comment', $comment);
            $html->printTemplate('comment');
        }

        $html->printTemplate('attachements');

        $html->printTemplate('export');

        //Import deviceparts does not work at all! To fix this we need a reworked import/export system... [TODO]
        //$html->setVariable('table', (isset($import_loop) ? $import_loop : array()));
        //$html->printTemplate('import');

        $html->printTemplate('copy_device');
    }
}

$html->printFooter();
