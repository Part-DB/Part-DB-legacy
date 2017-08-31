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
include_once(BASE.'/inc/lib.export.php');

use PartDB\Database;
use PartDB\Device;
use PartDB\HTML;
use PartDB\Log;
use PartDB\Part;
use PartDB\Supplier;
use PartDB\User;

$messages = array();
$fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

/********************************************************************************
 *
 *   Evaluate $_REQUEST
 *
 *********************************************************************************/

// section "parts to order"
$table_rowcount             = isset($_REQUEST['table_rowcount'])        ? (integer)$_REQUEST['table_rowcount']          : 0;
$selected_supplier_id       = isset($_REQUEST['selected_supplier_id'])  ? (integer)$_REQUEST['selected_supplier_id']    : 0;

// section "devices to order"
$device_id                  = isset($_REQUEST['device_id'])             ? (integer)$_REQUEST['device_id']               : 0;

// section "export"
$export_format_id           = isset($_REQUEST['export_format'])         ? $_REQUEST['export_format']                    : 0;

$action = 'default';
if (isset($_REQUEST['apply_changes'])) {
    $action = 'apply_changes';
}
if (isset($_REQUEST['autoset_quantities'])) {
    $action = 'autoset_quantities';
}
if (isset($_REQUEST['remove_device'])) {
    $action = 'remove_device';
}
if (isset($_REQUEST['export_show'])) {
    $action = 'export';
}
if (isset($_REQUEST['export_download'])) {
    $action = 'export';
}

/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $config['html']['custom_css'], 'Zu bestellende Preise');

try {
    $database           = new Database();
    $log                = new Log($database);
    $current_user       = new User($database, $current_user, $log, 1); // admin
} catch (Exception $e) {
    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
    $fatal_error = true;
}

/********************************************************************************
 *
 *   Some special functions for this site
 *
 *********************************************************************************/

function get_suppliers_template_loop($suppliers, $selected_supplier_id)
{
    $loop = array();

    foreach ($suppliers as $supplier) {
        /** @var Supplier $supplier */
        $loop[] = array(    'id'                => $supplier->getID(),
            'full_path'         => ($supplier->getFullPath()),
            'selected'          => ($supplier->getID() == $selected_supplier_id),
            'count_of_parts'    => $supplier->getCountOfPartsToOrder());
    }

    return $loop;
}

/********************************************************************************
 *
 *   Execute actions
 *
 *********************************************************************************/

if (! $fatal_error) {
    switch ($action) {
        case 'apply_changes':   // save new "selected_supplier" + "order_quantity", and delete or change "instock"
            for ($i=0; $i<$table_rowcount; $i++) {
                $part_id                = isset($_REQUEST['id_'.$i])                ? (integer)$_REQUEST['id_'.$i]                      : 0;
                $order_orderdetails_id  = isset($_REQUEST['orderdetails_'.$i])      ? (integer)$_REQUEST['orderdetails_'.$i]            : 0;
                $order_quantity         = isset($_REQUEST['order_quantity_'.$i])    ? max(0, (integer)$_REQUEST['order_quantity_'.$i])  : 0;

                try {
                    $part = new Part($database, $current_user, $log, $part_id);

                    $part->setOrderOrderdetailsID($order_orderdetails_id);
                    $part->setOrderQuantity($order_quantity);

                    if (isset($_REQUEST['remove_'.$i]) && ($part->getManualOrder())) {
                        $part->setManualOrder(false);
                    }

                    if (isset($_REQUEST['tostock_'.$i])) {
                        $part->setInstock($part->getInstock() + $order_quantity);
                    }
                } catch (Exception $e) {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
            }

            $reload_site = true;
            break;

        case 'autoset_quantities':
            for ($i=0; $i<$table_rowcount; $i++) {
                $part_id                = isset($_REQUEST['id_'.$i])                ? (integer)$_REQUEST['id_'.$i]                      : 0;

                try {
                    $part = new Part($database, $current_user, $log, $part_id);
                    $part->setOrderQuantity($part->getMinOrderQuantity());
                } catch (Exception $e) {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
            }

            $reload_site = true;
            break;

        case 'remove_device':
            try {
                $device = new Device($database, $current_user, $log, $device_id);
                $device->setOrderQuantity(0);
                $reload_site = true;
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        case 'export':
            try {
                if ($selected_supplier_id > 0) {
                    $supplier = new Supplier($database, $current_user, $log, $selected_supplier_id);
                    $parts = Part::getOrderParts($database, $current_user, $log, array($selected_supplier_id)); // parts from ONE supplier
                    $filename = 'order_parts_'.$supplier->getName();
                } else {
                    $parts = Part::getOrderParts($database, $current_user, $log); // parts from ALL suppliers
                    $filename = 'order_parts';
                }

                $download = isset($_REQUEST['export_download']);
                $export_string = exportParts($parts, 'orderparts', $export_format_id, $download, $filename);
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;
    }
}

if (isset($reload_site) && $reload_site && (! $config['debug']['request_debugging_enable'])) {
    // reload the site to avoid multiple actions by manual refreshing
    header('Location: show_order_parts.php?selected_supplier_id='.$selected_supplier_id);
}

/********************************************************************************
 *
 *   Generate Supplier Dropdown-List
 *
 *********************************************************************************/

if (! $fatal_error) {
    try {
        $suppliers = Supplier::getOrderSuppliers($database, $current_user, $log);
        $supplier_loop = get_suppliers_template_loop($suppliers, $selected_supplier_id);
        $html->setLoop('suppliers', $supplier_loop);
        $html->setVariable('selected_supplier_id', $selected_supplier_id, 'integer');
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        $fatal_error = true;
    }
}

/********************************************************************************
 *
 *   Generate "Parts to order"-Table
 *
 *********************************************************************************/

if (! $fatal_error) {
    try {
        if ($selected_supplier_id > 0) {
            $parts = Part::getOrderParts($database, $current_user, $log, array($selected_supplier_id));
        } // parts from ONE supplier
        else {
            $parts = Part::getOrderParts($database, $current_user, $log);
        } // parts from ALL suppliers

        $sum_price = 0;
        foreach ($parts as $part) {
            /** @var Part $part */
            $orderdetails = $part->getOrderOrderdetails();
            if (is_object($orderdetails)) {
                $sum_price += $orderdetails->getPrice(false, $part->getOrderQuantity());
            }
        }

        $table_loop = Part::buildTemplateTableArray($parts, 'order_parts');
        $html->setLoop('table', $table_loop);
        $html->setVariable('table_rowcount', count($parts), 'integer');
        $html->setVariable('sum_price', floatToMoneyString($sum_price), 'string');
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        $fatal_error = true;
    }
}

/********************************************************************************
 *
 *   Generate "Devices to order"-Table
 *
 *********************************************************************************/

if (! $fatal_error) {
    try {
        $order_devices = Device::getOrderDevices($database, $current_user, $log);
        $order_devices_loop = array();
        $row_odd = true;
        foreach ($order_devices as $device) {
            /** @var Device $device */
            $too_less_parts = 0;
            foreach ($device->getParts() as $devicepart) {
                /** @var \PartDB\DevicePart  $devicepart */
                $needed = $devicepart->getMountQuantity() * $device->getOrderQuantity();
                $instock = $devicepart->getPart()->getInstock();
                $mininstock = $devicepart->getPart()->getMinInstock();

                if ($instock - $needed < $mininstock) {
                    $too_less_parts++;
                }
            }

            $order_devices_loop[] = array(
                'row_odd'               => $row_odd,
                'id'                    => $device->getID(),
                'name'                  => $device->getName(),
                'full_path'             => $device->getFullPath(),
                'order_quantity'        => $device->getOrderQuantity(),
                'only_missing_parts'    => $device->getOrderOnlyMissingParts(),
                'parts_count'           => $device->getPartsCount(),
                'parts_count_to_order'  => $too_less_parts
            );

            $row_odd = ! $row_odd;
        }
        $html->setLoop('order_devices_loop', $order_devices_loop);
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
    // export formats
    $html->setLoop('export_formats', buildExportFormatsLoop('orderparts', $export_format_id));

    if (isset($export_string)) {
        $html->setVariable('export_result', str_replace("\n", '<br>', str_replace("\n  ", '<br>&nbsp;&nbsp;',   // yes, this is quite ugly,
            str_replace("\n    ", '<br>&nbsp;&nbsp;&nbsp;&nbsp;',               // but the result is pretty ;-)
                htmlspecialchars($export_string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')))), 'string');
    }

    // global stuff
    $html->setVariable('disable_footprints', $config['footprints']['disable'], 'boolean');
    $html->setVariable('disable_manufacturers', $config['manufacturers']['disable'], 'boolean');
    $html->setVariable('disable_auto_datasheets', $config['auto_datasheets']['disable'], 'boolean');

    $html->setVariable('use_modal_popup', $config['popup']['modal'], 'boolean');
    $html->setVariable('popup_width', $config['popup']['width'], 'integer');
    $html->setVariable('popup_height', $config['popup']['height'], 'integer');
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

$html->printHeader($messages);

if (! $fatal_error) {
    $html->printTemplate('show_order_parts');
}

$html->printFooter();
