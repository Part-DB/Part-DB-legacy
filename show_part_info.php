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

$part_id            = isset($_REQUEST['pid'])               ? (integer)$_REQUEST['pid']             : 0;
$n_less             = isset($_REQUEST['n_less'])            ? (integer)$_REQUEST['n_less']          : 0;
$n_more             = isset($_REQUEST['n_more'])            ? (integer)$_REQUEST['n_more']          : 0;
$order_quantity     = isset($_REQUEST['order_quantity'])    ? (integer)$_REQUEST['order_quantity']  : 0;

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
if (isset($_REQUEST["dec"])) {
    $action = 'dec';
}
if (isset($_REQUEST["inc"])) {
    $action = 'inc';
}
if (isset($_REQUEST["mark_to_order"])) {
    $action = 'mark_to_order';
}
if (isset($_REQUEST["remove_mark_to_order"])) {
    $action = 'remove_mark_to_order';
}

/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $config['html']['custom_css'], _('Detailinfo'));


try {
    $database           = new Database();
    $log                = new Log($database);
    $current_user       = new User($database, $current_user, $log, 1); // admin
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
    switch ($action) {
        case 'dec': // remove some parts
            try {
                $part->setInstock($part->getInstock() - abs($n_less));

                // reload the site without $_REQUEST['action'] to avoid multiple actions by manual refreshing
                header('Location: show_part_info.php?pid='.$part_id);
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        case 'inc': // add some parts
            try {
                $part->setInstock($part->getInstock() + abs($n_more));

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
    }
}

/********************************************************************************
 *
 *   Set the rest of the HTML variables
 *
 *********************************************************************************/


$properties = $part->getPropertiesLoop();
$html->setLoop("properties_loop", $properties);

// global settings
$html->setVariable('use_modal_popup', $config['popup']['modal'], 'boolean');
$html->setVariable('popup_width', $config['popup']['width'], 'integer');
$html->setVariable('popup_height', $config['popup']['height'], 'integer');

if (! $fatal_error) {
    try {
        //Set title
        $title = _('Detailinfo') . ': ' . $part->getName() . '';
        $html->setTitle($title);

        // part attributes
        $html->setVariable('pid', $part->getID(), 'integer');
        $html->setVariable('name', $part->getName(), 'string');
        $html->setVariable('manufacturer_product_url', $part->getManufacturerProductUrl(), 'string');
        $html->setVariable('description', $part->getDescription(), 'string');
        $html->setVariable('category_full_path', $part->getCategory()->getFullPath(), 'string');
        $html->setVariable('category_id', $part->getCategory()->getID(), 'string');
        $html->setVariable('instock', $part->getInstock(), 'integer');
        $html->setVariable('mininstock', $part->getMinInstock(), 'integer');
        $html->setVariable('visible', $part->getVisible(), 'boolean');
        $html->setVariable('comment', nl2br($part->getComment()), 'string');
        $html->setVariable('footprint_full_path', (is_object($footprint) ? $footprint->getFullPath() : '-'), 'string');
        $html->setVariable('footprint_id', (is_object($footprint) ? $footprint->getID() : 0), 'integer');
        $html->setVariable('footprint_filename', (is_object($footprint) ? str_replace(BASE, BASE_RELATIVE, $footprint->getFilename()) : ''), 'string');
        $html->setVariable('footprint_valid', (is_object($footprint) ? $footprint->isFilenameValid() : false), 'boolean');
        $html->setVariable('storelocation_full_path', (is_object($storelocation) ? $storelocation->getFullPath() : '-'), 'string');
        $html->setVariable('storelocation_id', (is_object($storelocation) ? $storelocation->getID() : '0'), 'integer');
        $html->setVariable('storelocation_is_full', (is_object($storelocation) ? $storelocation->getIsFull() : false), 'boolean');
        $html->setVariable('manufacturer_full_path', (is_object($manufacturer) ? $manufacturer->getFullPath() : '-'), 'string');
        $html->setVariable('manufacturer_id', (is_object($manufacturer) ? $manufacturer->getID() : 0), 'integer');
        $html->setVariable('category_full_path', (is_object($category) ? $category->getFullPath() : '-'), 'string');
        $html->setVariable('auto_order_exists', ($part->getInstock() < $part->getMinInstock()), 'boolean');
        $html->setVariable('manual_order_exists', ($part->getManualOrder() && ($part->getInstock() >= $part->getMinInstock())), 'boolean');

        $html->setVariable('last_modified', $part->getLastModified(), 'string');
        $html->setVariable('datetime_added', $part->getDatetimeAdded(), 'string');

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
            $attachements = $part->getAttachements($attachement_type->getID());
            $attachements_loop = array();
            foreach ($attachements as $attachement) {
                $attachements_loop[] = array(   'attachement_name'  => $attachement->getName(),
                    'filename'          => str_replace(BASE, BASE_RELATIVE, $attachement->getFilename()),
                    'is_picture'        => $attachement->isPicture());
            }

            if (count($attachements) > 0) {
                $attachement_types_loop[] = array(  'attachement_type'  => $attachement_type->getFullPath(),
                    'attachements_loop' => $attachements_loop);
            }
        }

        if (count($attachement_types_loop) > 0) {
            $html->setLoop('attachement_types_loop', $attachement_types_loop);
        }

        // global/category stuff
        $html->setVariable('disable_footprints', ($config['footprints']['disable'] || $category->getDisableFootprints(true)), 'boolean');
        $html->setVariable('disable_manufacturers', ($config['manufacturers']['disable'] || $category->getDisableManufacturers(true)), 'boolean');
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        $fatal_error = true;
    }
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
    $html->printTemplate('show_part_info');
}

$html->printFooter();
