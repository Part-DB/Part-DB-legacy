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

    $Id$

    Changelog (sorted by date):
        [DATE]      [NICKNAME]          [CHANGES]
        2012-??-??  weinbauer73         - changed to templates
        2012-09-09  kami89              - changed to OOP
*/

    include_once('start_session.php');

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

    //Parse Barcode scan
    if(isset($_REQUEST['barcode']))
    {
        $barcode = $_REQUEST['barcode'];
        if(is_numeric($barcode) && (mb_strlen($barcode) == 7 || mb_strlen($barcode) == 8))
        {
            if(mb_strlen($barcode) == 8)
            {
                //Remove parity
                $barcode = substr($barcode, 0, -1);
            }
            $part_id = (integer) $barcode;
        }
        else
        {
            $messages[] = $messages[] = array('text' => nl2br(_("Barcode input is not valid!")), 'strong' => true, 'color' => 'red');
            $fatal_error = true;
        }
    }

    $action = 'default';
    if (isset($_REQUEST["dec"]))                    {$action = 'dec';}
    if (isset($_REQUEST["inc"]))                    {$action = 'inc';}
    if (isset($_REQUEST["mark_to_order"]))          {$action = 'mark_to_order';}
    if (isset($_REQUEST["remove_mark_to_order"]))   {$action = 'remove_mark_to_order';}

    /********************************************************************************
    *
    *   Initialize Objects
    *
    *********************************************************************************/

    $html = new HTML($config['html']['theme'], $config['html']['custom_css'], _('Detailinfo'));


    try
    {
        $database           = new Database();
        $log                = new Log($database);
        $current_user       = new User($database, $current_user, $log, 1); // admin
        $part               = new Part($database, $current_user, $log, $part_id);
        $footprint          = $part->get_footprint();
        $storelocation      = $part->get_storelocation();
        $manufacturer       = $part->get_manufacturer();
        $category           = $part->get_category();
        $all_orderdetails   = $part->get_orderdetails();
    }
    catch (Exception $e)
    {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        $fatal_error = true;
    }

    /********************************************************************************
    *
    *   Execute actions
    *
    *********************************************************************************/

    if ( ! $fatal_error)
    {
        switch ($action)
        {
            case 'dec': // remove some parts
                try
                {
                    $part->set_instock($part->get_instock() - abs($n_less));

                    // reload the site without $_REQUEST['action'] to avoid multiple actions by manual refreshing
                    header('Location: show_part_info.php?pid='.$part_id);
                }
                catch (Exception $e)
                {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
                break;

            case 'inc': // add some parts
                try
                {
                    $part->set_instock($part->get_instock() + abs($n_more));

                    // reload the site without $_REQUEST['action'] to avoid multiple actions by manual refreshing
                    header('Location: show_part_info.php?pid='.$part_id);
                }
                catch (Exception $e)
                {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
                break;

            case 'mark_to_order':
                try
                {
                    $part->set_manual_order(true, $order_quantity);
                }
                catch (Exception $e)
                {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
                break;

            case 'remove_mark_to_order':
                try
                {
                    $part->set_manual_order(false);
                }
                catch (Exception $e)
                {
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

    $html->use_javascript(array('validatenumber', 'popup'));

    // global settings
    $html->set_variable('use_modal_popup',          $config['popup']['modal'], 'boolean');
    $html->set_variable('popup_width',              $config['popup']['width'], 'integer');
    $html->set_variable('popup_height',             $config['popup']['height'], 'integer');

    if (! $fatal_error)
    {
        try
        {
            //Set title
            $title = _('Detailinfo') . ': ' . $part->get_name() . '';
            $html->set_title($title);

            // part attributes
            $html->set_variable('pid',                      $part->get_id(), 'integer');
            $html->set_variable('name',                     $part->get_name(), 'string');
            $html->set_variable('manufacturer_product_url', $part->get_manufacturer_product_url(), 'string');
            $html->set_variable('description',              $part->get_description(), 'string');
            $html->set_variable('category_full_path',       $part->get_category()->get_full_path(), 'string');
            $html->set_variable('instock',                  $part->get_instock(), 'integer');
            $html->set_variable('mininstock',               $part->get_mininstock(), 'integer');
            $html->set_variable('visible',                  $part->get_visible(), 'boolean');
            $html->set_variable('comment',                  nl2br($part->get_comment()), 'string');
            $html->set_variable('footprint_full_path',      (is_object($footprint) ? $footprint->get_full_path() : '-'), 'string');
            $html->set_variable('footprint_filename',       (is_object($footprint) ? str_replace(BASE, BASE_RELATIVE, $footprint->get_filename()) : ''), 'string');
            $html->set_variable('footprint_valid',          (is_object($footprint) ? $footprint->is_filename_valid() : false), 'boolean');
            $html->set_variable('storelocation_full_path',  (is_object($storelocation) ? $storelocation->get_full_path() : '-'), 'string');
            $html->set_variable('storelocation_is_full',    (is_object($storelocation) ? $storelocation->get_is_full() : false), 'boolean');
            $html->set_variable('manufacturer_full_path',   (is_object($manufacturer) ? $manufacturer->get_full_path() : '-'), 'string');
            $html->set_variable('category_full_path',       (is_object($category) ? $category->get_full_path() : '-'), 'string');
            $html->set_variable('auto_order_exists',        ($part->get_instock() < $part->get_mininstock()), 'boolean');
            $html->set_variable('manual_order_exists',      ($part->get_manual_order() && ($part->get_instock() >= $part->get_mininstock())), 'boolean');

            $html->set_variable('last_modified',            $part->get_last_modified(), 'string');
            $html->set_variable('datetime_added',           $part->get_datetime_added(), 'string');

            //Infos about 3d footprint view
            $html->set_variable('foot3d_show_stats',        $config['foot3d']['show_info'], 'boolean');
            $html->set_variable('foot3d_active',            $config['foot3d']['active'], 'boolean');
            $html->set_variable('foot3d_filename',          (is_object($footprint) ? str_replace(BASE, BASE_RELATIVE, $footprint->get_3d_filename()) : ''), 'string');
            $html->set_variable('foot3d_valid',             (is_object($footprint) ? $footprint->is_3d_filename_valid() : false), 'boolean');

            // build orderdetails loop
            $orderdetails_loop = array();
            $row_odd = true;
            foreach ($all_orderdetails as $orderdetails)
            {
                $pricedetails_loop = array();
                foreach($orderdetails->get_pricedetails() as $pricedetails)
                {
                    $pricedetails_loop[] = array(   'min_discount_quantity'     => $pricedetails->get_min_discount_quantity(),
                                                    'price'                     => $pricedetails->get_price(true, $pricedetails->get_price_related_quantity()),
                                                    'price_related_quantity'    => $pricedetails->get_price_related_quantity(),
                                                    'single_price'              => $pricedetails->get_price(true, 1));
                }

                $orderdetails_loop[] = array(   'row_odd'                   => $row_odd,
                                                'supplier_full_path'        => $orderdetails->get_supplier()->get_full_path(),
                                                'supplierpartnr'            => $orderdetails->get_supplierpartnr(),
                                                'supplier_product_url'      => $orderdetails->get_supplier_product_url(),
                                                'obsolete'                  => $orderdetails->get_obsolete(),
                                                'pricedetails'              => (count($pricedetails_loop) > 0) ? $pricedetails_loop : NULL);
                $row_odd = ! $row_odd;
            }

            $html->set_loop('orderdetails', $orderdetails_loop);

            if ($part->get_average_price(false, 1) > 0)
                $html->set_variable('average_price', $part->get_average_price(true, 1), 'string');

            // attachements
            $attachement_types = $part->get_attachement_types();
            $attachement_types_loop = array();
            foreach ($attachement_types as $attachement_type)
            {
                $attachements = $part->get_attachements($attachement_type->get_id());
                $attachements_loop = array();
                foreach ($attachements as $attachement)
                {
                    $attachements_loop[] = array(   'attachement_name'  => $attachement->get_name(),
                                                    'filename'          => str_replace(BASE, BASE_RELATIVE, $attachement->get_filename()),
                                                    'is_picture'        => $attachement->is_picture());
                }

                if (count($attachements) > 0)
                    $attachement_types_loop[] = array(  'attachement_type'  => $attachement_type->get_full_path(),
                                                        'attachements_loop' => $attachements_loop);
            }

            if (count($attachement_types_loop) > 0)
                $html->set_loop('attachement_types_loop', $attachement_types_loop);

            // global/category stuff
            $html->set_variable('disable_footprints',       ($config['footprints']['disable'] || $category->get_disable_footprints(true)), 'boolean');
            $html->set_variable('disable_manufacturers',    ($config['manufacturers']['disable'] || $category->get_disable_manufacturers(true)), 'boolean');
        }
        catch (Exception $e)
        {
            $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            $fatal_error = true;
        }
    }

    /********************************************************************************
    *
    *   Generate HTML Output
    *
    *********************************************************************************/

    $reload_link = $fatal_error ? 'show_part_info.php?pid='.$part_id : '';  // an empty string means that the...
    $html->print_header($messages, $reload_link);                           // ...reload-button won't be visible

    if (! $fatal_error)
        $html->print_template('show_part_info');

    $html->print_footer();

