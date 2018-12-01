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

use PartDB\Database;
use PartDB\Device;
use PartDB\DevicePart;
use PartDB\HTML;
use PartDB\Log;
use PartDB\Part;
use PartDB\Permissions\CPartAttributePermission;
use PartDB\Permissions\DevicePartPermission;
use PartDB\Permissions\PartAttributePermission;
use PartDB\Permissions\PartPermission;
use PartDB\Permissions\PermissionManager;
use PartDB\User;

$messages = array();
$fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

/********************************************************************************
 *
 *   Evaluate $_REQUEST
 *
 *********************************************************************************/

$part_id            = isset($_REQUEST['pid'])               ? (integer)$_REQUEST['pid']             : 0;
$n_less             = isset($_POST['n_less'])            ? (integer)$_POST['n_less']          : 0;
$n_more             = isset($_POST['n_more'])            ? (integer)$_POST['n_more']          : 0;
$order_quantity     = isset($_POST['order_quantity'])    ? (integer)$_POST['order_quantity']  : 0;
$instock_change_comment = isset($_POST['instock_change_comment']) ? (string)$_POST['instock_change_comment'] : "";

//When adding to a device
$device_id          = isset($_POST['device_id_new'])     ? (integer)$_POST['device_id_new']   : 0;
$device_qty         = isset($_POST['device_quantity_new']) ? (integer)$_POST['device_quantity_new'] : 0;
$device_name        = isset($_POST['device_name_new'])   ? (string)$_POST['device_name_new'] : "";

//Pagination for history
$page               = isset($_REQUEST['page'])              ? (integer)$_REQUEST['page']            : 1;
$limit              = isset($_REQUEST['limit'])             ? (integer)$_REQUEST['limit']           : 10;


//Parse Label scan
if (isset($_REQUEST['barcode'])) {
    $barcode = $_REQUEST['barcode'];
    if (is_numeric($barcode) && (mb_strlen($barcode) == 7 || mb_strlen($barcode) == 8)) {
        if (mb_strlen($barcode) == 8) {
            //Remove parity
            $barcode = substr($barcode, 0, -1);
        }
        $part_id = (integer) $barcode;
    } else {
        $messages[] = $messages[] = array('text' => nl2br(_("Label input is not valid!")), 'strong' => true, 'color' => 'red');
        $fatal_error = true;
    }
}

$action = 'default';
if (isset($_POST["dec"])) {
    $action = 'dec';
}
if (isset($_POST["inc"])) {
    $action = 'inc';
}
if (isset($_POST["mark_to_order"])) {
    $action = 'mark_to_order';
}
if (isset($_POST["remove_mark_to_order"])) {
    $action = 'remove_mark_to_order';
}
if (isset($_POST['device_add'])) {
    $action = "device_add";
}
if (isset($_POST['toggle_favorite'])) {
    $action = "toggle_favorite";
}

/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $user_config['theme'], _('Detailinfo'));


try {
    $database           = new Database();
    $log                = new Log($database);
    $current_user       = User::getLoggedInUser($database, $log);
    //Check permission
    $current_user->tryDo(PermissionManager::PARTS, PartPermission::READ);

    $part               = new Part($database, $current_user, $log, $part_id);
    $footprint          = $part->getFootprint();
    $storelocation      = $part->getStorelocation();
    $manufacturer       = $part->getManufacturer();
    $category           = $part->getCategory();
    $all_orderdetails   = $part->getOrderdetails();
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

    //If no comment was set, than use the default one from the user profile.
    if ($instock_change_comment == "") {
        $instock_change_comment = null;
    }

    switch ($action) {
        case 'dec': // remove some parts
            try {
                //$part->setInstock($part->getInstock() - abs($n_less));
                $part->withdrawalParts($n_less, $instock_change_comment);

                // reload the site without $_REQUEST['action'] to avoid multiple actions by manual refreshing
                header('Location: show_part_info.php?pid='.$part_id);
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        case 'inc': // add some parts
            try {
                //$part->setInstock($part->getInstock() + abs($n_more));
                $part->addParts($n_more, $instock_change_comment);

                // reload the site without $_REQUEST['action'] to avoid multiple actions by manual refreshing
                header('Location: show_part_info.php?pid='.$part_id);
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        case 'mark_to_order':
            try {
                $part->setManualOrder(true, $order_quantity);
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        case 'remove_mark_to_order':
            try {
                $part->setManualOrder(false);
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;
        case "device_add":
            try {
                if ($device_id > 0 && $device_qty > 0) {
                    $devicepart = DevicePart::add($database, $current_user, $log, $device_id, $part_id, $device_qty, $device_name);
                    $devicepart->getID();
                } else {
                    throw new Exception(_("Ungültige Eingabedaten!"));
                }
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;
        case "toggle_favorite":
            try {
                $part->setFavorite(!$part->getFavorite());
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;
    }
}

/********************************************************************************
 *
 *   Set the rest of the HTML variables
 *
 *********************************************************************************/

if (! $fatal_error) {
    try {
        $properties = $part->getPropertiesLoop();
        $html->setLoop("properties_loop", $properties);

        // global settings
        $html->setVariable('use_modal_popup', $config['popup']['modal'], 'boolean');
        $html->setVariable('popup_width', $config['popup']['width'], 'integer');
        $html->setVariable('popup_height', $config['popup']['height'], 'integer');


        //Set title
        $title = _('Detailinfo') . ': ' . $part->getName() . '';
        $html->setTitle($title);

        // part attributes
        $html->setVariable('pid', $part->getID(), 'integer');
        $html->setVariable('name', $part->getName(), 'string');
        $html->setVariable('manufacturer_product_url', $part->getManufacturerProductUrl(), 'string');
        $html->setVariable('description', $part->getDescription(), 'string');
        $html->setLoop('category_path', $category->buildBreadcrumbLoop("show_category_parts.php", "cid", false, null, true));
        $html->setVariable('category_id', $part->getCategory()->getID(), 'string');
        $html->setVariable('instock', $part->getInstock(true), 'string');
        $html->setVariable('instock_unknown', $part->isInstockUnknown(), 'boolean');
        $html->setVariable('mininstock', $part->getMinInstock(), 'integer');
        $html->setVariable('visible', $part->getVisible(), 'boolean');
        $html->setVariable('comment', nl2br($part->getComment()), 'string');
        $html->setLoop('footprint_path', (is_object($footprint) ? $footprint->buildBreadcrumbLoop("show_footprint_parts.php", "fid", false, null, true) : null));
        $html->setVariable('footprint_id', (is_object($footprint) ? $footprint->getID() : 0), 'integer');
        $html->setVariable('footprint_filename', (is_object($footprint) ? str_replace(BASE, BASE_RELATIVE, $footprint->getFilename()) : ''), 'string');
        $html->setVariable('footprint_valid', (is_object($footprint) ? $footprint->isFilenameValid() : false), 'boolean');
        $html->setLoop('storelocation_path', (is_object($storelocation) ? $storelocation->buildBreadcrumbLoop("show_location_parts.php", "lid", false, null, true) : null));
        $html->setVariable('storelocation_id', (is_object($storelocation) ? $storelocation->getID() : '0'), 'integer');
        $html->setVariable('storelocation_is_full', (is_object($storelocation) ? $storelocation->getIsFull() : false), 'boolean');
        $html->setLoop('manufacturer_path', (is_object($manufacturer) ? $manufacturer->buildBreadcrumbLoop("show_manufacturer_parts.php", "mid", false, null, true) : null));
        $html->setVariable('manufacturer_id', (is_object($manufacturer) ? $manufacturer->getID() : 0), 'integer');
        $html->setVariable('auto_order_exists', ($part->getAutoOrder()), 'boolean');
        $html->setVariable('manual_order_exists', ($part->getManualOrder() && ($part->getInstock() >= $part->getMinInstock())), 'boolean');

        $html->setVariable('last_modified', $part->getLastModified(), 'string');
        $html->setVariable('datetime_added', $part->getDatetimeAdded(), 'string');
        $last_modified_user = $part->getLastModifiedUser();
        $creation_user = $part->getCreationUser();
        if ($last_modified_user != null) {
            $html->setVariable('last_modified_user', $last_modified_user->getFullName(true), "string");
            $html->setVariable('last_modified_user_id', $last_modified_user->getID(), "int");
        }
        if ($creation_user != null) {
            $html->setVariable('creation_user', $creation_user->getFullName(true), "string");
            $html->setVariable('creation_user_id', $creation_user->getID(), "int");
        }

        //Default withdrawal/Add comment
        $html->setVariable('default_instock_change_comment_w', $current_user->getDefaultInstockChangeComment(true));
        $html->setVariable('default_instock_change_comment_a', $current_user->getDefaultInstockChangeComment(false));

        $html->setVariable('is_favorite', $part->getFavorite(), 'bool');

        //Infos about 3d footprint view
        $html->setVariable('foot3d_show_stats', $config['foot3d']['show_info'], 'boolean');
        $html->setVariable('foot3d_active', $config['foot3d']['active'], 'boolean');
        $html->setVariable('foot3d_filename', (is_object($footprint) ? str_replace(BASE, BASE_RELATIVE, $footprint->get3dFilename()) : ''), 'string');
        $html->setVariable('foot3d_valid', (is_object($footprint) ? $footprint->is3dFilenameValid() : false), 'boolean');

        // build orderdetails loop
        $orderdetails_loop = array();
        $row_odd = true;
        foreach ($all_orderdetails as $orderdetails) {
            $pricedetails_loop = array();
            foreach ($orderdetails->getPricedetails() as $pricedetails) {
                $pricedetails_loop[] = array(   'min_discount_quantity'     => $pricedetails->getMinDiscountQuantity(),
                    'price'                     => $pricedetails->getPrice(true, $pricedetails->getPriceRelatedQuantity()),
                    'price_related_quantity'    => $pricedetails->getPriceRelatedQuantity(),
                    'single_price'              => $pricedetails->getPrice(true, 1));
            }

            $orderdetails_loop[] = array(   'row_odd'                   => $row_odd,
                'supplier_full_path'        => $orderdetails->getSupplier()->getFullPath(),
                'supplier_id'               => $orderdetails->getSupplier()->getID(),
                'supplierpartnr'            => $orderdetails->getSupplierPartNr(),
                'supplier_product_url'      => $orderdetails->getSupplierProductUrl(),
                'obsolete'                  => $orderdetails->getObsolete(),
                'pricedetails'              => (count($pricedetails_loop) > 0) ? $pricedetails_loop : null);
            $row_odd = ! $row_odd;
        }

        $html->setLoop('orderdetails', $orderdetails_loop);

        if ($part->getAveragePrice(false, 1) > 0) {
            $html->setVariable('average_price', $part->getAveragePrice(true, 1), 'string');
        }

        // attachements
        $attachement_types = $part->getAttachementTypes();
        $attachement_types_loop = array();
        foreach ($attachement_types as $attachement_type) {
            /** @var $attachement_type \PartDB\AttachementType */
            /** @var $attachements \PartDB\Attachement[] */
            $attachements = $part->getAttachements($attachement_type->getID());
            $attachements_loop = array();
            foreach ($attachements as $attachement) {
                /** @var $attachement \PartDB\Attachement */
                $attachements_loop[] = array(   'attachement_name'  => $attachement->getName(),
                    'filename'          => str_replace(BASE, BASE_RELATIVE, $attachement->getFilename()),
                    'is_picture'        => $attachement->isPicture(),
                    'file_existing'     => $attachement->isFileExisting());
            }

            if (count($attachements) > 0) {
                $attachement_types_loop[] = array(  'attachement_type'  => $attachement_type->getFullPath(),
                    'attachements_loop' => $attachements_loop);
            }
        }

        if (count($attachement_types) == 0) {
            $attachements_empty = true;
        }

        if (count($attachement_types_loop) > 0) {
            $html->setLoop('attachement_types_loop', $attachement_types_loop);
        }

        //Auto datasheets
        $datasheet_loop = $config['auto_datasheets']['entries'];

        foreach ($datasheet_loop as $key => $entry) {
            $datasheet_loop[$key]['url'] = str_replace('%%PARTNAME%%', urlencode($part->getName()), $entry['url']);
        }

        if ($config['appearance']['use_old_datasheet_icons'] == true) {
            foreach ($datasheet_loop as &$sheet) {
                if (isset($sheet['old_image'])) {
                    $sheet['image'] = $sheet['old_image'];
                }
            }
        }
        $html->setLoop("datasheet_loop", $datasheet_loop);

        //Devices
        $devices = $part->getDevices();
        $devices_loop = array();
        foreach ($devices as $device) {
            $device_part = \PartDB\DevicePart::getDevicePart($database, $current_user, $log, $device->getID(), $part->getID());
            $devices_loop[] = array("name" => $device->getName(),
                "id" => $device->getID(),
                "fullpath" => $device->getFullPath(),
                "mount_quantity" => $device_part->getMountQuantity(),
                "mount_name" => $device_part->getMountNames());
        }

        $root_device = new Device($database, $current_user, $log, 0);
        $html->setVariable("devices_list", $root_device->buildHtmlTree(Device::getPrimaryDevice(), true, false), "string");

        if (count($devices_loop) > 0) {
            $html->setLoop('devices_loop', $devices_loop);
        }

        // global/category stuff
        $html->setVariable('disable_footprints', ($config['footprints']['disable'] || $category->getDisableFootprints(true)), 'boolean');
        $html->setVariable('disable_manufacturers', ($config['manufacturers']['disable'] || $category->getDisableManufacturers(true)), 'boolean');

        //Barcode stuff
        $html->setLoop("barcode_profiles", buildLabelProfilesDropdown("part"));

        $count = 0;
        if ($current_user->canDo(PermissionManager::PARTS, PartPermission::SHOW_HISTORY)) {
            $history = Log::getHistoryForPart($database, $current_user, $log, $part, $limit, $page);
            $html->setVariable("graph_history", Log::historyToGraph($history));
            $html->setLoop("history", $history);
            $count = Log::getHistoryForPartCount($database, $current_user, $log, $part);
        }
        $html->setLoop("pagination", generatePagination("show_location_parts.php?pid=$part_id", $page, $limit, $count));
        $html->setVariable("page", $page);
        $html->setVariable('limit', $limit);
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        $fatal_error = true;
    }
}
try {
    $html->setVariable("can_delete", $current_user->canDo(PermissionManager::PARTS, PartPermission::DELETE), "bool");
    $html->setVariable("can_edit", $current_user->canDo(PermissionManager::PARTS, PartPermission::EDIT), "bool");
    $html->setVariable("can_create", $current_user->canDo(PermissionManager::PARTS, PartPermission::CREATE), "bool");
    $html->setVariable("can_move", $current_user->canDo(PermissionManager::PARTS, PartPermission::MOVE), "bool");
    $html->setVariable("can_read", $current_user->canDo(PermissionManager::PARTS, PartPermission::READ), "bool");
    $html->setVariable("can_instock", $current_user->canDo(PermissionManager::PARTS_INSTOCK, PartAttributePermission::EDIT), "bool");
    $html->setVariable("can_favorite", $current_user->canDo(PermissionManager::PARTS, PartPermission::CHANGE_FAVORITE));

    $html->setVariable('can_orderdetails_create', $current_user->canDo(PermissionManager::PARTS_ORDERDETAILS, CPartAttributePermission::CREATE), "bool");
    $html->setVariable('can_attachement_create', $current_user->canDo(PermissionManager::PARTS_ATTACHEMENTS, CPartAttributePermission::CREATE), "bool");
    $html->setVariable('can_order_edit', $current_user->canDo(PermissionManager::PARTS_ORDER, PartAttributePermission::EDIT), "bool");
    $html->setVariable('can_order_read', $current_user->canDo(PermissionManager::PARTS_ORDER, PartAttributePermission::READ), "bool");
    $html->setVariable('can_devicepart_create', $current_user->canDo(PermissionManager::DEVICE_PARTS, DevicePartPermission::CREATE));
    $html->setVariable('can_generate_barcode', $current_user->canDo(PermissionManager::LABELS, \PartDB\Permissions\LabelPermission::CREATE_LABELS));

    $html->setVariable('can_visit_user', $current_user->canDo(PermissionManager::USERS, \PartDB\Permissions\UserPermission::READ));
} catch (Exception $e) {
    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
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

$reload_link = $fatal_error ? 'show_part_info.php?pid='.$part_id : '';  // an empty string means that the...
$html->printHeader($messages, $reload_link);                           // ...reload-button won't be visible

if (! $fatal_error) {
    $html->printTemplate('main');
    $html->printTemplate('properties');
    if (!($config['suppliers']['disable'] || ($config['part_info']['hide_empty_orderdetails'] && count($all_orderdetails) == 0))
        && $current_user->canDo(PermissionManager::PARTS_ORDERDETAILS, CPartAttributePermission::READ)) {
        $html->printTemplate('orderdetails');
    }
    if (!($config['part_info']['hide_empty_attachements'] && isset($attachements_empty))
        && $current_user->canDo(PermissionManager::PARTS_ATTACHEMENTS, CPartAttributePermission::READ)) {
        $html->printTemplate('attachements');
    }

    if ($current_user->canDo(PermissionManager::PARTS, PartPermission::SHOW_HISTORY)) {
        $html->printTemplate('history');
    }

    if ($current_user->canDo(PermissionManager::DEVICE_PARTS, DevicePartPermission::READ)
        && !$config['devices']['disable']) {
        $html->printTemplate('devices');
    }


    if (!$config['part_info']['hide_actions']) {
        $html->printTemplate('actions');
    }

    $html->printTemplate('modal');
}

$html->printFooter();
