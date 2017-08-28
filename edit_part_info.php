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

use PartDB\Attachement;
use PartDB\AttachementType;
use PartDB\Category;
use PartDB\Database;
use PartDB\Footprint;
use PartDB\HTML;
use PartDB\Log;
use PartDB\Manufacturer;
use PartDB\Orderdetails;
use PartDB\Part;
use PartDB\Pricedetails;
use PartDB\Storelocation;
use PartDB\Supplier;
use PartDB\User;

/*
 * This site is used for editing an existing part.
 * But it is also used for creating new parts,
 * because this way we don't have to create another, quite similar site.
 */

    include_once('start_session.php');

    $messages = array();
    $fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

    /********************************************************************************
    *
    *   Evaluate $_REQUEST
    *
    *********************************************************************************/

    // special: do we want to create a new part?
    $is_new_part                = ((isset($_REQUEST['category_id'])) && (! isset($_REQUEST['pid'])));
    $add_one_more_part          = isset($_REQUEST['add_one_more_part']);

    // section: part attributes
    $part_id                    = isset($_REQUEST['pid'])                       ? (integer)$_REQUEST['pid']                      : -1;
    $new_name                   = isset($_REQUEST['name'])                      ? (string)$_REQUEST['name']                      : '';
    $new_description            = isset($_REQUEST['description'])               ? (string)$_REQUEST['description']               : '';
    $new_manufacturer_id        = isset($_REQUEST['manufacturer_id'])           ? (integer)$_REQUEST['manufacturer_id']          : 0;
    $new_instock                = isset($_REQUEST['instock'])                   ? (integer)$_REQUEST['instock']                  : 0;
    $new_mininstock             = isset($_REQUEST['mininstock'])                ? (integer)$_REQUEST['mininstock']               : 0;
    $new_category_id            = isset($_REQUEST['category_id'])               ? (integer)$_REQUEST['category_id']              : 0;
    $new_storelocation_id       = isset($_REQUEST['storelocation_id'])          ? (integer)$_REQUEST['storelocation_id']         : 0;
    $new_footprint_id           = isset($_REQUEST['footprint_id'])              ? (integer)$_REQUEST['footprint_id']             : 0;
    $new_visible                = isset($_REQUEST['visible']);
    $new_comment                = isset($_REQUEST['comment'])                   ? (string)$_REQUEST['comment']                   : '';

    // search/add
    $search_category_name       = isset($_REQUEST['search_category_name'])      ? (string)$_REQUEST['search_category_name']      : '';
    $search_footprint_name      = isset($_REQUEST['search_footprint_name'])     ? (string)$_REQUEST['search_footprint_name']     : '';
    $search_storelocation_name  = isset($_REQUEST['search_storelocation_name']) ? (string)$_REQUEST['search_storelocation_name'] : '';
    $search_manufacturer_name   = isset($_REQUEST['search_manufacturer_name'])  ? (string)$_REQUEST['search_manufacturer_name']  : '';

    // section: attachements
    $new_show_in_table          = isset($_REQUEST['show_in_table']);
    $new_is_master_picture      = isset($_REQUEST['is_master_picture']);
    $attachement_id             = isset($_REQUEST['attachement_id'])            ? (integer)$_REQUEST['attachement_id']           : 0;
    $new_attachement_type_id    = isset($_REQUEST['attachement_type_id'])       ? (integer)$_REQUEST['attachement_type_id']      : 0;
    $new_name                   = isset($_REQUEST['name'])                      ? (string)$_REQUEST['name']                      : '';
    $new_filename               = isset($_REQUEST['attachement_filename'])      ? to_unix_path(trim((string)$_REQUEST['attachement_filename'])) : '';

    $partname_invalid           = isset($_REQUEST['name_edit'])                 ? true                                           : false;

    if ((strlen($new_filename) > 0) && (! is_path_absolute_and_unix($new_filename))) {
        $new_filename = BASE.'/'.$new_filename;
    } // switch from relative path (like "img/foo.png") to absolute path (like "/var/www/part-db/img/foo.png")

    // section: delete part
    $delete_files_from_hdd      = isset($_REQUEST['delete_files_from_hdd']);

    $action = 'default';
    if (isset($_REQUEST['create_new_part'])) {
        $action = 'create_new_part';
    }

    if (isset($_REQUEST["apply_attributes"])) {
        $action = 'apply_attributes';
    }

    if (isset($_REQUEST["orderdetails_add"])) {
        $action = 'orderdetails_add';
        $orderdetails_id = $_REQUEST["orderdetails_add"];
    }
    if (isset($_REQUEST["orderdetails_apply"])) {
        $action = 'orderdetails_apply';
        $orderdetails_id = $_REQUEST["orderdetails_apply"];
    }
    if (isset($_REQUEST["orderdetails_delete"])) {
        $action = 'orderdetails_delete';
        $orderdetails_id = $_REQUEST["orderdetails_delete"];
    }

    if (isset($_REQUEST["pricedetails_add"])) {
        $action = 'pricedetails_add';
        $pricedetails_id = "new";
        $orderdetails_id = $_REQUEST["pricedetails_add"];
    }
    if (isset($_REQUEST["pricedetails_apply"])) {
        $action = 'pricedetails_apply';
        $pricedetails_id = $_REQUEST["pricedetails_apply"];
    }
    if (isset($_REQUEST["pricedetails_delete"])) {
        $action = 'pricedetails_delete';
        $pricedetails_id = $_REQUEST["pricedetails_delete"];
    }

    if (isset($_REQUEST["attachement_add"])) {
        $action = 'attachement_add';
    }
    if (isset($_REQUEST["attachement_apply"])) {
        $action = 'attachement_apply';
    }
    if (isset($_REQUEST["attachement_delete"])) {
        $action = 'attachement_delete';
    }

    if (isset($_REQUEST["delete_part"])) {
        $action = 'delete_part';
    }
    if (isset($_REQUEST["delete_part_confirmed"])) {
        $action = 'delete_part_confirmed';
    }

    if (isset($_REQUEST["search_category"])) {
        $action = 'search_category';
    }
    if (isset($_REQUEST["search_footprint"])) {
        $action = 'search_footprint';
    }
    if (isset($_REQUEST["search_storelocation"])) {
        $action = 'search_storelocation';
    }
    if (isset($_REQUEST["search_manufacturer"])) {
        $action = 'search_manufacturer';
    }

    if (isset($_REQUEST["apply_name_save"])) {
        $action = 'apply_name_confirmed';
    }
    if (isset($_REQUEST["create_name_save"])) {
        $action = 'create_new_part';
    }


    // section: orderdetails
    if (isset($orderdetails_id)) {
        $new_supplier_id = isset($_REQUEST['supplier_id_'.$orderdetails_id]) ? (integer)$_REQUEST['supplier_id_'.$orderdetails_id] : 0;
        $new_supplierpartnr = isset($_REQUEST['supplierpartnr_'.$orderdetails_id]) ? (string)$_REQUEST['supplierpartnr_'.$orderdetails_id] : '';
        $new_obsolete = isset($_REQUEST['obsolete_'.$orderdetails_id]);
    }
    // section: pricedetails
    if (isset($pricedetails_id)) {
        if (isset($orderdetails_id)) {
            $new_price = isset($_REQUEST['price_' . $orderdetails_id . "_" . $pricedetails_id]) ? (float)str_replace(',', '.', $_REQUEST['price_' . $orderdetails_id . "_" . $pricedetails_id]) : 0;
            $new_min_discount_quantity = isset($_REQUEST['min_discount_quantity_' . $orderdetails_id . "_" .  $pricedetails_id]) ? (integer)$_REQUEST['min_discount_quantity_' . $orderdetails_id . "_" .  $pricedetails_id] : 1;
            $new_price_related_quantity = isset($_REQUEST['price_related_quantity_' . $orderdetails_id . "_" . $pricedetails_id]) ? (integer)$_REQUEST['price_related_quantity_' . $orderdetails_id . "_" . $pricedetails_id] : 1;
        } else {
            $new_price = isset($_REQUEST['price_' . $pricedetails_id]) ? (float)str_replace(',', '.', $_REQUEST['price_'  . $pricedetails_id]) : 0;
            $new_min_discount_quantity = isset($_REQUEST['min_discount_quantity_' .  $pricedetails_id]) ? (integer)$_REQUEST['min_discount_quantity_'  .  $pricedetails_id] : 1;
            $new_price_related_quantity = isset($_REQUEST['price_related_quantity_'  . $pricedetails_id]) ? (integer)$_REQUEST['price_related_quantity_' . $pricedetails_id] : 1;
        }
    }

/********************************************************************************
    *
    *   Initialize Objects
    *
    *********************************************************************************/

    $html = new HTML($config['html']['theme'], $config['html']['custom_css'], _('Bauteil bearbeiten'));

    try {
        $database               = new Database();
        $log                    = new Log($database);
        $current_user           = new User($database, $current_user, $log, 1); // admin

        if (! $is_new_part) {
            $part               = new Part($database, $current_user, $log, $part_id);

            ///@todo: remove this line:
            $new_visible = $part->get_visible();
        }

        $root_storelocation     = new Storelocation($database, $current_user, $log, 0);
        $root_category          = new Category($database, $current_user, $log, 0);
        $root_manufacturer      = new Manufacturer($database, $current_user, $log, 0);
        $root_footprint         = new Footprint($database, $current_user, $log, 0);
        $root_supplier          = new Supplier($database, $current_user, $log, 0);
        $root_attachement_type  = new AttachementType($database, $current_user, $log, 0);

        if (isset($orderdetails_id) && $orderdetails_id > 0) {
            $orderdetails = new Orderdetails($database, $current_user, $log, $orderdetails_id);
        } else {
            $orderdetails = null;
        }

        if (isset($pricedetails_id) && $pricedetails_id > 0) {
            $pricedetails = new Pricedetails($database, $current_user, $log, $pricedetails_id);
        } else {
            $pricedetails = null;
        }

        if ($attachement_id > 0) {
            $attachement = new Attachement($database, $current_user, $log, $attachement_id);
        } else {
            $attachement = null;
        }
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        $fatal_error = true;
    }

    /********************************************************************************
    *
    *   Execute actions
    *
    *********************************************************************************/

    $print_unsaved_values = false;

    if (! $fatal_error) {
        switch ($action) {
            case 'create_new_part':
                try {
                    $existing_parts = Part::check_for_existing_part(
                        $database,
                        ,
                        ,
                        ,
                        $new_storelocation_id,
                        $new_category_id
                    );

                    $category = new Category($database, $current_user, $log, $new_category_id);

                    if (Part::is_valid_name($new_name, $category) || isset($_REQUEST['create_name_save'])) {
                        $part = Part::add(
                            $database,
                            $current_user,
                            $log,
                            $new_name,
                            $new_category_id,
                            $new_description,
                            $new_instock,
                            $new_mininstock,
                            $new_storelocation_id,
                            $new_manufacturer_id,
                            $new_footprint_id,
                            $new_comment,
                            $new_visible
                        );

                        $is_new_part = false;
                    } else {
                        if (empty($category->get_partname_hint(true, false))) {
                            $messages[] = array('text' => sprintf(_('Der Name "%s" entspricht nicht den Vorgaben!'), $new_name),
                                'strong' => true, 'color' => 'red');
                        } else {
                            $messages[] = array('html' => sprintf(
                                _('Der Name "%s" entspricht nicht den Vorgaben <b>(%s)</b>!'),
                                $new_name,
                                $category->get_partname_hint(true, false)
                            ));
                        }



                        $messages[] = array('text' => _('<br>Hinweis:'), 'strong' => true);
                        $messages[] = array('text' => _('Der Name muss folgendem Format entsprechen: ') . "<b>" . $category->get_partname_regex(true) . "</b>");
                        if (!$category->get_partname_regex_obj()->is_enforced()) {
                            $messages[] = array('html' => _('Möchten sie wirklich fortfahren?<br>'));
                            $messages[] = array('html' => generate_button("", _('Nein, Name überarbeiten')), 'no_linebreak' => true);
                            $messages[] = array('html' => generate_button_red("create_name_save", _('Ja, Name speichern')));
                        } else {
                            $messages[] = array('html' => _('Dies kann nicht ignoriert werden, da das Enforcement-Flag für diese Kategorie gesetzt ist!<br>'));
                            $messages[] = array('html' => '<button class="btn btn-default" type="submit" name="" >'._('Ok, Name überarbeiten').'</button>', 'no_linebreak' => true);
                        }

                        $messages[] = array('html' => generate_input_hidden("name", $new_name), 'no_linebreak' => true);
                        $messages[] = array('html' => generate_input_hidden("category_id", $new_category_id), 'no_linebreak' => true);
                        $messages[] = array('html' => generate_input_hidden("description", $new_description), 'no_linebreak' => true);
                        $messages[] = array('html' => generate_input_hidden("instock", $new_instock), 'no_linebreak' => true);
                        $messages[] = array('html' => generate_input_hidden("mininstock", $new_mininstock), 'no_linebreak' => true);
                        $messages[] = array('html' => generate_input_hidden("storelocation_id", $new_storelocation_id), 'no_linebreak' => true);
                        $messages[] = array('html' => generate_input_hidden("manufacturer_id", $new_manufacturer_id), 'no_linebreak' => true);
                        $messages[] = array('html' => generate_input_hidden("footprint_id", $new_footprint_id), 'no_linebreak' => true);
                        $messages[] = array('html' => generate_input_hidden("comment", $new_comment), 'no_linebreak' => 'true');
                        $messages[] = array('html' => generate_input_hidden("visible", $new_visible), 'no_linebreak' => 'true');


                        $partname_invalid = true;
                    }
                } catch (Exception $e) {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
                break;

            case 'apply_name_confirmed':
                try {
                    $part->set_name($new_name);
                } catch (Exception $e) {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
                break;
            case 'apply_attributes':
                try {
                    $new_attributes = array(
                        'description'       => $new_description,
                        'instock'           => $new_instock,
                        'mininstock'        => $new_mininstock,
                        'id_category'       => $new_category_id,
                        'id_storelocation'  => $new_storelocation_id,
                        'visible'           => $new_visible,
                        'comment'           => $new_comment);

                    // do not overwrite (remove!) the footprint or manufacturer if they are disabled (global or in the part's category)
                    if (isset($_REQUEST['footprint_id'])) {
                        $new_attributes['id_footprint']    = $new_footprint_id;
                    }
                    if (isset($_REQUEST['manufacturer_id'])) {
                        $new_attributes['id_manufacturer'] = $new_manufacturer_id;
                    }

                    $part->set_attributes($new_attributes);

                    if (Part::is_valid_name($new_name, $part->get_category())) {
                        $part->set_name($new_name);
                    } else {
                        if (empty($part->get_category()->get_partname_hint(true, false))) {
                            $messages[] = array('text' => sprintf(_('Der Name "%s" entspricht nicht den Vorgaben!'), $new_name),
                                'strong' => true, 'color' => 'red');
                        } else {
                            $messages[] = array('html' => sprintf(
                                _('Der Name "%s" entspricht nicht den Vorgaben <b>(%s)</b>!'),
                                $new_name,
                                $part->get_category()->get_partname_hint(true, false)
                            ));
                        }

                        $messages[] = array('text' => _('<br>Hinweis:'), 'strong' => true);
                        $messages[] = array('html' => _('Der Name muss folgendem Format entsprechen: ') . "<b>" . $part->get_category()->get_partname_regex(true) . "</b>");
                        if ($part->get_category()->get_partname_regex_obj()->is_enforced()) {
                            $messages[] = array('html' => _('Dies kann nicht ignoriert werden, da das Enforcement-Flag für diese Kategorie gesetzt ist!<br>'));
                            $messages[] = array('html' => '<button class="btn btn-default" type="submit" name="name_edit" >'._('Ok, Name überarbeiten').'</button>', 'no_linebreak' => true);
                        } else {
                            $messages[] = array('html' => _('Möchten sie wirklich fortfahren?<br>'));
                            $messages[] = array('html' => '<button class="btn btn-default" type="submit" name="name_edit" >'._('Nein, Name überarbeiten').'</button>', 'no_linebreak' => true);
                            $messages[] = array('html' => '<button class="btn btn-danger" type="submit" name="apply_name_save">'._('Ja, Name speichern').'</button>', 'no_linebreak' => true);
                        }
                        $messages[] = array('html' => '<input type="hidden" name="pid" value="'.$part_id.'">', 'no_linebreak' => true);
                        $messages[] = array('html' => '<input type="hidden" name="name" value="'.$new_name.'">', 'no_linebreak' => true);

                        $partname_invalid = true;
                    }
                } catch (Exception $e) {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
                break;

            case 'orderdetails_add':
                try {
                    $new_orderdetails = Orderdetails::add(
                        $database,
                        $current_user,
                        $log,
                        $part_id,
                                                            $new_supplier_id,
                        $new_supplierpartnr,
                        $new_obsolete
                    );
                } catch (Exception $e) {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
                break;

            case 'orderdetails_apply':
                try {
                    if (! is_object($orderdetails)) {
                        throw new Exception(_('Es ist keine Einkaufsinformation ausgewählt!'));
                    }

                    $orderdetails->set_attributes(array(    'id_supplier'               => $new_supplier_id,
                                                            'supplierpartnr'            => $new_supplierpartnr,
                                                            'obsolete'                  => $new_obsolete));
                } catch (Exception $e) {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
                break;

            case 'orderdetails_delete':
                try {
                    //$orderdetails = new Orderdetails($database, $current_user, $log, $_REQUEST['orderdetails_delete']);
                    if (! is_object($orderdetails)) {
                        throw new Exception(_('Es ist keine Einkaufsinformation ausgewählt!'));
                    }

                    $orderdetails->delete();
                } catch (Exception $e) {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
                break;

            case 'pricedetails_add':
                try {
                    $new_pricedetails = Pricedetails::add(
                        $database,
                        $current_user,
                        $log,
                        $orderdetails_id,
                                                            $new_price,
                        $new_price_related_quantity,
                                                            $new_min_discount_quantity
                    );
                } catch (Exception $e) {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
                break;

            case 'pricedetails_apply':
                try {
                    //$pricedetails_id = $_REQUEST['pricedetails_apply'];
                    //$pricedetails = new Pricedetails($database, $current_user, $log, $pricedetails_id);
                    if (! is_object($pricedetails)) {
                        throw new Exception(_('Es ist keine Preisinformation ausgewählt!'));
                    }

                    $pricedetails->set_attributes(array(    'price'                     => $new_price,
                                                            'price_related_quantity'    => $new_price_related_quantity,
                                                            'min_discount_quantity'     => $new_min_discount_quantity));
                } catch (Exception $e) {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
                break;

            case 'pricedetails_delete':
                try {
                    $pricedetails = new Pricedetails($database, $current_user, $log, $_REQUEST['pricedetails_delete']);
                    if (! is_object($pricedetails)) {
                        throw new Exception(_('Es ist keine Preisinformation ausgewählt!'));
                    }

                    $pricedetails->delete();
                } catch (Exception $e) {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
                break;

            case 'attachement_add':
                try {
                    if ((strlen($_FILES['attachement_file']['name']) == 0) == (strlen($new_filename) == 0)) {
                        throw new Exception(_('Sie müssen entweder ein Dateiname angeben, oder eine Datei zum Hochladen wählen!'));
                    }

                    if (strlen($_FILES['attachement_file']['name']) > 0) {
                        $new_filename = upload_file($_FILES['attachement_file'], BASE.'/data/media/');
                    }

                    $new_attachement = Attachement::add(
                        $database,
                        $current_user,
                        $log,
                        $part,
                        $new_attachement_type_id,
                                                        $new_filename,
                        $new_name,
                        $new_show_in_table
                    );

                    if ($new_is_master_picture && $new_attachement->is_picture()) {
                        $part->set_master_picture_attachement_id($new_attachement->get_id());
                    }
                } catch (Exception $e) {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
                break;

            case 'attachement_apply':
                try {
                    if (! is_object($attachement)) {
                        throw new Exception(_('Es ist kein Dateianhang ausgewählt!'));
                    }

                    if (strlen($_FILES['attachement_file']['name']) > 0) {
                        $new_filename = upload_file($_FILES['attachement_file'], BASE.'/data/media/');
                    }

                    $attachement->set_attributes(array( 'type_id'           => $new_attachement_type_id,
                                                        'name'              => $new_name,
                                                        'filename'          => $new_filename,
                                                        'show_in_table'     => $new_show_in_table));

                    if ($new_is_master_picture) {
                        $part->set_master_picture_attachement_id($attachement->get_id());
                    } else {
                        $master_picture = $part->get_master_picture_attachement();

                        if (is_object($master_picture) && ($master_picture->get_id() == $attachement->get_id())) {
                            $part->set_master_picture_attachement_id(null);
                        } // remove master picture
                    }
                } catch (Exception $e) {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
                break;

            case 'attachement_delete':
                try {
                    if (! is_object($attachement)) {
                        throw new Exception(_('Es ist kein Dateianhang ausgewählt!'));
                    }

                    // if this is the master picture, we have to remove that attribute
                    $master_picture = $part->get_master_picture_attachement();
                    if (is_object($master_picture) && ($master_picture->get_id() == $attachement->get_id())) {
                        $part->set_master_picture_attachement_id(null);
                    } // remove master picture

                    $attachement->delete(true); // the file will be deleted only if there are no other attachements with the same filename
                } catch (Exception $e) {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
                break;

            case 'delete_part':
                try {
                    // check if there are devices with this part. if there are some, the part can't be deleted!
                    $devices = $part->get_devices();
                    if (count($devices) > 0) {
                        $device_names = '';
                        foreach ($devices as $device) {
                            $device_names .= "\n&nbsp;&nbsp;&bull; ".$device->get_full_path();
                        }
                        throw new Exception(_('Das Bauteil kann nicht gelöscht werden, da es noch in den '.
                                            'folgenden Baugruppen verwendet wird:').$device_names);
                    }

                    $messages[] = array('text' => sprintf(_('Soll das Bauteil "%s" wirklich unwiederruflich gelöscht werden?'), $part->get_name()),
                                            'strong' => true, 'color' => 'red');
                    $messages[] = array('text' => _('<br>Hinweise:'), 'strong' => true);
                    $messages[] = array('text' => _('&nbsp;&nbsp;&bull; Es gibt keine Baugruppen die dieses Bauteil verwenden.'));
                    if ($delete_files_from_hdd) {
                        $messages[] = array('text' => _('&nbsp;&nbsp;&bull; Alle Dateien dieses Bauteiles, die nicht von anderen Bauteilen verwendet werden, werden von der Festplatte gelöscht!'), 'color' => 'red');
                    } else {
                        $messages[] = array('text' => _('&nbsp;&nbsp;&bull; Alle Dateien dieses Bauteiles bleiben weiterhin erhalten.'));
                    }

                    $messages[] = array('html' => '<input type="hidden" name="pid" value="'.$part_id.'">');
                    if ($delete_files_from_hdd) {
                        $messages[] = array('html' => '<input type="hidden" name="delete_files_from_hdd">');
                    }
                    $messages[] = array('html' => '<input class="btn btn-default" type="submit" value="'._('Nein, nicht löschen').'">', 'no_linebreak' => true);
                    $messages[] = array('html' => '<input class="btn btn-danger" type="submit" name="delete_part_confirmed" value="'._('Ja, Bauteil löschen').'">');
                } catch (Exception $e) {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
                break;

            case 'delete_part_confirmed':
                try {
                    $part->delete($delete_files_from_hdd);
                    $part = null;
                    $messages[] = array('text' => _('Das Bauteil wurde erfolgreich gelöscht!'), 'strong' => true, 'color' => 'darkgreen');
                    $messages[] = array('html' => '<br><a class="btn btn-primary" href="startup.php">'._('Fenster schliessen').'</a>');
                    $fatal_error = true; // there is no error, but we cannot display the part infos because the part exists no longer :-)
                } catch (Exception $e) {
                    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
                }
                break;

            case 'search_category':
                $classname = 'Category';
                $search_name = $search_category_name;
                break;
            case 'search_footprint':
                $classname = 'Footprint';
                $search_name = $search_footprint_name;
                break;
            case 'search_storelocation':
                $classname = 'Storelocation';
                $search_name = $search_storelocation_name;
                break;
            case 'search_manufacturer':
                $classname = 'Manufacturer';
                $search_name = $search_manufacturer_name;
                break;
        }

        if (isset($classname)) {
            $print_unsaved_values = true;
            $search_name = trim($search_name);

            try {
                if (strpos($search_name, '__ID__=') === 0) {
                    $searched_element = new $classname($database, $current_user, $log, (int)str_replace('__ID__=', '', $search_name));
                } else {
                    $elements = $classname::search($database, $current_user, $log, $search_name);

                    if (count($elements) > 0) {
                        $searched_element = $elements[0];
                    } else {
                        $searched_element = $classname::add($database, $current_user, $log, $search_name, 0);
                    }
                }
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
        }
    }

    /********************************************************************************
    *
    *   Set the rest of the HTML variables
    *
    *********************************************************************************/


    if (! $fatal_error) {
        try {
            // global settings
            $html->set_variable('use_modal_popup', $config['popup']['modal'], 'boolean');
            $html->set_variable('popup_width', $config['popup']['width'], 'integer');
            $html->set_variable('popup_height', $config['popup']['height'], 'integer');

            // special
            $html->set_variable('is_new_part', ($is_new_part || $add_one_more_part), 'boolean');

            // part attributes
            if (isset($part) && is_object($part)) {
                $html->set_variable('pid', $part->get_id(), 'integer');
                if ($partname_invalid) {
                    $html->set_variable('name', $new_name, 'string');
                } else {
                    $html->set_variable('name', $part->get_name(), 'string');
                }
                $html->set_variable('description', $part->get_description(false), 'string');
                $html->set_variable('instock', $part->get_instock(), 'integer');
                $html->set_variable('mininstock', $part->get_mininstock(), 'integer');
                $html->set_variable('visible', $part->get_visible(), 'boolean');
                $html->set_variable('comment', $part->get_comment(false), 'string');
                $html->set_variable('format_hint', $part->get_category()->get_partname_hint(true, false), 'string');

                // dropdown lists -> get IDs
                $category_id        = (is_object($part->get_category())         ?   $part->get_category()->get_id()      : 0);
                $footprint_id       = (is_object($part->get_footprint())        ?   $part->get_footprint()->get_id()     : 0);
                $storelocation_id   = (is_object($part->get_storelocation())    ?   $part->get_storelocation()->get_id() : 0);
                $manufacturer_id    = (is_object($part->get_manufacturer())     ?   $part->get_manufacturer()->get_id()  : 0);

                // build orderdetails loop
                $orderdetails_loop = array();
                $row_odd = true;
                foreach ($part->get_orderdetails() as $orderdetails) {
                    $supplier_list = $root_supplier->build_html_tree($orderdetails->get_supplier()->get_id(), true, false);
                    $pricedetails_loop = array();
                    foreach ($orderdetails->get_pricedetails() as $pricedetails) {
                        //HTML5 wants a float number with a dot as a decimal point. The browser should change its display correspondingly to HTML locale.
                        $price = str_replace(",", ".", $pricedetails->get_price(false, $pricedetails->get_price_related_quantity()));

                        $pricedetails_loop[] = array(   'row_odd'                   => ! $row_odd,
                                                        'orderdetails_id'           => $orderdetails->get_id(),
                                                        'pricedetails_id'           => $pricedetails->get_id(),
                                                        'min_discount_quantity'     => $pricedetails->get_min_discount_quantity(),
                                                        'price'                     => $price,
                                                        'price_related_quantity'    => $pricedetails->get_price_related_quantity());
                    }

                    if (count($pricedetails_loop) > 0) {
                        $next_min_discount_quantity = $pricedetails_loop[count($pricedetails_loop)-1]['min_discount_quantity'] * 10;
                    } else {
                        $next_min_discount_quantity = 1;
                    }

                    $pricedetails_loop[] = array(       'orderdetails_id'           => $orderdetails->get_id(),
                                                        'pricedetails_id'           => 'new',
                                                        'min_discount_quantity'     => $next_min_discount_quantity,
                                                        'price'                     => 0,
                                                        'price_related_quantity'    => 1);

                    $orderdetails_loop[] = array(       'row_odd'                   => $row_odd,
                                                        'orderdetails_id'           => $orderdetails->get_id(),
                                                        'supplier_list'             => $supplier_list,
                                                        'supplierpartnr'            => $orderdetails->get_supplierpartnr(),
                                                        'obsolete'                  => $orderdetails->get_obsolete(),
                                                        'pricedetails'              => $pricedetails_loop);
                    $row_odd = ! $row_odd;
                }

                // add one additional row -> with this row you can add more orderdetails
                $supplier_list = $root_supplier->build_html_tree(0, true, false);
                $orderdetails_loop[] = array(   'row_odd'                   => $row_odd,
                                                'orderdetails_id'           => 'new',
                                                'supplier_list'             => $supplier_list,
                                                'supplierpartnr'            => '',
                                                'obsolete'                  => false);

                $html->set_loop('orderdetails', $orderdetails_loop);

                // build attachements loop
                $master_picture_id = (is_object($part->get_master_picture_attachement()) ? $part->get_master_picture_attachement()->get_id() : null);
                $attachements_loop = array();
                $all_attachements = $part->get_attachements();
                $row_odd = true;
                foreach ($all_attachements as $attachement) {
                    $attachement_types_list = $root_attachement_type->build_html_tree($attachement->get_type()->get_id(), true, false);
                    $attachements_loop[] = array(   'row_odd'                   => $row_odd,
                                                    'id'                        => $attachement->get_id(),
                                                    'attachement_types_list'    => $attachement_types_list,
                                                    'name'                      => $attachement->get_name(),
                                                    'show_in_table'             => $attachement->get_show_in_table(),
                                                    'is_picture'                => $attachement->is_picture(),
                                                    'is_master_picture'         => ($attachement->get_id() == $master_picture_id),
                                                    'filename'                  => str_replace(BASE, BASE_RELATIVE, $attachement->get_filename()),
                                                    'filename_base_relative'    => str_replace(BASE.'/', '', $attachement->get_filename()),
                                                    'picture_filename'          => ($attachement->is_picture() ? str_replace(BASE, BASE_RELATIVE, $attachement->get_filename()) : ''));
                    $row_odd = ! $row_odd;
                }

                // add one additional row -> with this row you can add more files
                $attachement_types_list = $root_attachement_type->build_html_tree(0, true, false);
                $attachements_loop[] = array(   'row_odd'                   => $row_odd,
                                                'id'                        => 'new',
                                                'attachement_types_list'    => $attachement_types_list,
                                                'name'                      => '',
                                                'is_picture'                => true,
                                                'show_in_table'             => false,
                                                'is_master_picture'         => false,
                                                'filename'                  => '',
                                                'filename_base_relative'    => '',
                                                'picture_filename'          => '');

                $html->set_loop('attachements_loop', $attachements_loop);
            }

            if (($print_unsaved_values) || (! isset($part)) || (! is_object($part))) {
                if (isset($new_category_id)) {
                    $cat = new Category($database, $current_user, $log, $new_category_id);
                    if (empty($new_description)) {
                        $new_description = $cat->get_default_description(true, false);
                    }
                    if (empty($new_comment)) {
                        $new_comment = $cat->get_default_comment(true, false);
                    }
                    $new_comment = $cat->get_default_comment(true, false);
                }

                $html->set_variable('name', $new_name, 'string');
                $html->set_variable('description', $new_description, 'string');
                $html->set_variable('instock', $new_instock, 'integer');
                $html->set_variable('mininstock', $new_mininstock, 'integer');
                $html->set_variable('visible', $new_visible, 'boolean');
                $html->set_variable('comment', $new_comment, 'string');

                $category_id        = $new_category_id;
                $footprint_id       = $new_footprint_id;
                $storelocation_id   = $new_storelocation_id;
                $manufacturer_id    = $new_manufacturer_id;
            }

            if (isset($searched_element) && $searched_element instanceof Category) {
                $category_id = $searched_element->get_id();
            }

            if (isset($searched_element) && $searched_element instanceof Footprint) {
                $footprint_id = $searched_element->get_id();
            }

            if (isset($searched_element) && $searched_element instanceof Storelocation) {
                $storelocation_id = $searched_element->get_id();
            }

            if (isset($searched_element) && $searched_element instanceof Manufacturer) {
                $manufacturer_id = $searched_element->get_id();
            }


            // dropdown lists -> generate lists
            $manufacturer_list  = $root_manufacturer->build_html_tree($manufacturer_id, true, false);
            $category_list      = $root_category->build_html_tree($category_id, true, false);
            $storelocation_list = $root_storelocation->build_html_tree($storelocation_id, true, false);
            $footprint_list     = $root_footprint->build_html_tree($footprint_id, true, false);

            // the category ID is used for creating a new part (in *.tmpl file the latest DIV element)
            $html->set_variable('category_id', $category_id, 'integer');

            // dropdown lists -> set html variables
            $html->set_variable('manufacturer_list', $manufacturer_list, 'string');
            $html->set_variable('category_list', $category_list, 'string');
            $html->set_variable('storelocation_list', $storelocation_list, 'string');
            $html->set_variable('footprint_list', $footprint_list, 'string');

            // global/category stuff
            $category = new Category($database, $current_user, $log, $category_id);
            $html->set_variable('disable_footprints', ($config['footprints']['disable'] || $category->get_disable_footprints(true)), 'boolean');
            $html->set_variable('disable_manufacturers', ($config['manufacturers']['disable'] || $category->get_disable_manufacturers(true)), 'boolean');
            $html->set_variable('max_upload_filesize', ini_get('upload_max_filesize'), 'string');
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
        $html->set_variable("ajax_request", true);
    }

    // an empty string in "$reload_link" means that the reload-button won't be visible
    $reload_link = ($fatal_error && ($action != 'delete_part_confirmed')) ? 'edit_part_info.php?pid='.$part_id : '';
    $html->print_header($messages, $reload_link);

    if (! $fatal_error) {
        $html->print_template('part');

        if (! ($is_new_part || $add_one_more_part)) {
            $html->print_template('orderdetails');
            $html->print_template('attachements');
            $html->print_template('actions');
        }
    }

    $html->print_footer();
