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

use PartDB\Attachment;
use PartDB\AttachmentType;
use PartDB\Category;
use PartDB\Database;
use PartDB\Footprint;
use PartDB\HTML;
use PartDB\Log;
use PartDB\Manufacturer;
use PartDB\Orderdetails;
use PartDB\Part;
use PartDB\Permissions\CPartAttributePermission;
use PartDB\Permissions\PartPermission;
use PartDB\Permissions\PermissionManager;
use PartDB\Permissions\PartAttributePermission;
use PartDB\Pricedetails;
use PartDB\Storelocation;
use PartDB\Supplier;
use PartDB\User;

/*
 * This site is used for editing an existing part.
 * But it is also used for creating new parts,
 * because this way we don't have to create another, quite similar site.
 */

include_once __DIR__ . '/start_session.php';

$messages = array();
$fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

/********************************************************************************
 *
 *   Evaluate $_REQUEST
 *
 *********************************************************************************/

// special: do we want to create a new part?
$is_new_part                = !isset($_REQUEST['pid']);
$add_one_more_part          = isset($_REQUEST['add_one_more_part']);

// section: part attributes
$part_id                    = isset($_REQUEST['pid'])                       ? (int)$_REQUEST['pid']                      : -1;
$new_name                   = isset($_REQUEST['name'])                      ? (string)$_REQUEST['name']                      : '';
$new_description            = isset($_POST['description'])               ? (string)$_POST['description']               : '';
$new_instock                = isset($_POST['instock'])                   ? (int)$_POST['instock']                  : 0;
$new_instock                = isset($_POST['instock_unknown'])           ? Part::INSTOCK_UNKNOWN : $new_instock;
$new_mininstock             = isset($_POST['mininstock'])                ? (int)$_POST['mininstock']               : 0;
//The category ID is given as a GET param, so we need REQUEST here.
$new_category_id            = isset($_REQUEST['category_id'])               ? (int)$_REQUEST['category_id']        : 0;
$new_storelocation_id       = isset($_REQUEST['storelocation_id'])          ? (int)$_REQUEST['storelocation_id']   : 0;
$new_footprint_id           = isset($_REQUEST['footprint_id'])              ? (int)$_REQUEST['footprint_id']          : 0;
$new_manufacturer_id        = isset($_REQUEST['manufacturer_id'])           ? (int)$_REQUEST['manufacturer_id']       : 0;
$new_visible                = isset($_POST['visible']);
$new_comment                = isset($_POST['comment'])                   ? (string)$_POST['comment']                   : '';
$new_manufacturer_url       = isset($_POST['manufacturer_url'])          ? (string)$_POST['manufacturer_url']          : '';

$change_comment             = isset($_POST['change_comment'])    ? (string)$_POST['change_comment']   : null;

// search/add
$search_category_name       = isset($_POST['search_category_name'])      ? (string)$_POST['search_category_name']      : '';
$search_footprint_name      = isset($_POST['search_footprint_name'])     ? (string)$_POST['search_footprint_name']     : '';
$search_storelocation_name  = isset($_POST['search_storelocation_name']) ? (string)$_POST['search_storelocation_name'] : '';
$search_manufacturer_name   = isset($_POST['search_manufacturer_name'])  ? (string)$_POST['search_manufacturer_name']  : '';

// section: attachements
$new_show_in_table          = isset($_POST['show_in_table']);
$new_is_master_picture      = isset($_POST['is_master_picture']);
$attachement_id             = isset($_POST['attachement_id'])            ? (int)$_POST['attachement_id']           : 0;
$new_attachement_type_id    = isset($_POST['attachement_type_id'])       ? (int)$_POST['attachement_type_id']      : 0;
$new_name                   = isset($_REQUEST['name'])                      ? (string)$_REQUEST['name']                      : '';
$new_filename               = isset($_POST['attachement_filename'])      ? toUnixPath(trim((string)$_POST['attachement_filename'])) : '';
$download_file              = isset($_POST['download_file']);

$partname_invalid           = isset($_POST['name_edit'])                 ? true                                           : false;

$rightclicked               = isset($_POST['rightclicked']);

if ((strlen($new_filename) > 0) && (! isPathabsoluteAndUnix($new_filename))) {
    $new_filename = BASE.'/'.$new_filename;
} // switch from relative path (like "img/foo.png") to absolute path (like "/var/www/part-db/img/foo.png")

// section: delete part
$delete_files_from_hdd      = isset($_POST['delete_files_from_hdd']);

$action = 'default';
if (isset($_POST['create_new_part'])) {
    $action = 'create_new_part';
}

if (isset($_POST['apply_attributes'])) {
    $action = 'apply_attributes';
}

if (isset($_POST['orderdetails_add'])) {
    $action = 'orderdetails_add';
    $orderdetails_id = $_POST['orderdetails_add'];
}
if (isset($_POST['orderdetails_apply'])) {
    $action = 'orderdetails_apply';
    $orderdetails_id = $_POST['orderdetails_apply'];
}
if (isset($_POST['orderdetails_delete'])) {
    $action = 'orderdetails_delete';
    $orderdetails_id = $_POST['orderdetails_delete'];
}

if (isset($_POST['pricedetails_add'])) {
    $action = 'pricedetails_add';
    $pricedetails_id = 'new';
    $orderdetails_id = $_POST['pricedetails_add'];
}
if (isset($_POST['pricedetails_apply'])) {
    $action = 'pricedetails_apply';
    $pricedetails_id = $_POST['pricedetails_apply'];
}
if (isset($_POST['pricedetails_delete'])) {
    $action = 'pricedetails_delete';
    $pricedetails_id = $_POST['pricedetails_delete'];
}

if (isset($_POST['attachement_add'])) {
    $action = 'attachement_add';
}
if (isset($_POST['attachement_apply'])) {
    $action = 'attachement_apply';
}
if (isset($_POST['attachement_delete'])) {
    $action = 'attachement_delete';
}

if (isset($_POST['delete_part'])) {
    $action = 'delete_part';
}
if (isset($_POST['delete_part_confirmed'])) {
    $action = 'delete_part_confirmed';
}

if (isset($_REQUEST['search_category'])) {
    $action = 'search_category';
}
if (isset($_REQUEST['search_footprint'])) {
    $action = 'search_footprint';
}
if (isset($_REQUEST['search_storelocation'])) {
    $action = 'search_storelocation';
}
if (isset($_REQUEST['search_manufacturer'])) {
    $action = 'search_manufacturer';
}

if (isset($_POST['apply_name_save'])) {
    $action = 'apply_name_confirmed';
}
if (isset($_POST['create_name_save'])) {
    $action = 'create_new_part';
}


// section: orderdetails
if (isset($orderdetails_id)) {
    $new_supplier_id = isset($_POST['supplier_id_'.$orderdetails_id]) ? (int)$_POST['supplier_id_'.$orderdetails_id] : 0;
    $new_supplierpartnr = isset($_POST['supplierpartnr_'.$orderdetails_id]) ? (string)$_POST['supplierpartnr_'.$orderdetails_id] : '';
    $new_obsolete = isset($_POST['obsolete_'.$orderdetails_id]);
    $new_supplier_url = isset($_POST['supplierurl_' .$orderdetails_id]) ? (string)$_POST['supplierurl_'.$orderdetails_id] : '';
}
// section: pricedetails
if (isset($pricedetails_id)) {
    if (isset($orderdetails_id)) {
        $new_price = isset($_POST['price_' . $orderdetails_id . '_' . $pricedetails_id]) ? (float)str_replace(',', '.', $_POST['price_' . $orderdetails_id . '_' . $pricedetails_id]) : 0;
        $new_min_discount_quantity = isset($_POST['min_discount_quantity_' . $orderdetails_id . '_' .  $pricedetails_id]) ? (int)$_POST['min_discount_quantity_' . $orderdetails_id . '_' .  $pricedetails_id] : 1;
        $new_price_related_quantity = isset($_POST['price_related_quantity_' . $orderdetails_id . '_' . $pricedetails_id]) ? (int)$_POST['price_related_quantity_' . $orderdetails_id . '_' . $pricedetails_id] : 1;
    } else {
        $new_price = isset($_POST['price_' . $pricedetails_id]) ? (float)str_replace(',', '.', $_POST['price_'  . $pricedetails_id]) : 0;
        $new_min_discount_quantity = isset($_POST['min_discount_quantity_' .  $pricedetails_id]) ? (int)$_POST['min_discount_quantity_'  .  $pricedetails_id] : 1;
        $new_price_related_quantity = isset($_POST['price_related_quantity_'  . $pricedetails_id]) ? (int)$_POST['price_related_quantity_' . $pricedetails_id] : 1;
    }
}

/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $user_config['theme'], _('Bauteil bearbeiten'));

try {
    $database               = new Database();
    $log                    = new Log($database);
    $current_user           = User::getLoggedInUser($database, $log);

    if (! $is_new_part) {
        $part               = Part::getInstance($database, $current_user, $log, $part_id);

        ///@todo: remove this line:
        $new_visible = $part->getVisible();
    }

    $root_storelocation     = Storelocation::getInstance($database, $current_user, $log, 0);
    $root_category          = Category::getInstance($database, $current_user, $log, 0);
    $root_manufacturer      = Manufacturer::getInstance($database, $current_user, $log, 0);
    $root_footprint         = Footprint::getInstance($database, $current_user, $log, 0);
    $root_supplier          = Supplier::getInstance($database, $current_user, $log, 0);
    $root_attachement_type  = AttachmentType::getInstance($database, $current_user, $log, 0);

    if (isset($orderdetails_id) && intval($orderdetails_id) > 0) {
        $orderdetails = Orderdetails::getInstance($database, $current_user, $log, $orderdetails_id);
    } else {
        $orderdetails = null;
    }

    if (isset($pricedetails_id) && $pricedetails_id > 0) {
        $pricedetails = Pricedetails::getInstance($database, $current_user, $log, $pricedetails_id);
    } else {
        $pricedetails = null;
    }

    if ($attachement_id > 0) {
        $attachement = Attachment::getInstance($database, $current_user, $log, $attachement_id);
    } else {
        $attachement = null;
    }

    //Default text for instock changes via this page.
    $default_instock_comment = _('Bauteil bearbeitet');
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
                $category = Category::getInstance($database, $current_user, $log, $new_category_id);

                if (Part::isValidName($new_name, $category) || isset($_POST['create_name_save'])) {
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
                        $new_visible,
                        $new_manufacturer_url
                    );

                    $is_new_part = false;

                    global $config;
                    if ($config['edit_parts']['created_go_to_info'] xor $rightclicked) {
                        $html->redirect('show_part_info.php?pid=' . $part->getID(), true);
                    } else {
                        $html->redirect('edit_part_info.php?pid=' . $part->getID(), true);
                    }
                } else {
                    $partname_hint = $category->getPartnameHint(true, false);
                    if (empty($partname_hint)) {
                        $messages[] = array('text' => sprintf(_('Der Name "%s" entspricht nicht den Vorgaben!'), $new_name),
                            'strong' => true, 'color' => 'red');
                    } else {
                        $messages[] = array('html' => sprintf(
                            _('Der Name "%s" entspricht nicht den Vorgaben <b>(%s)</b>!'),
                            $new_name,
                            $partname_hint
                        ));
                    }



                    $messages[] = array('text' => _('<br>Hinweis:'), 'strong' => true);
                    $messages[] = array('text' => _('Der Name muss folgendem Format entsprechen: ') . '<b>' . $category->getPartnameRegex(true) . '</b>');
                    if (!$category->getPartnameRegexObj()->isEnforced()) {
                        $messages[] = array('html' => _('Möchten sie wirklich fortfahren?<br>'));
                        $messages[] = array('html' => generateButton('', _('Nein, Name überarbeiten')), 'no_linebreak' => true);
                        $messages[] = array('html' => generateButtonRed('create_name_save', _('Ja, Name speichern')));
                    } else {
                        $messages[] = array('html' => _('Dies kann nicht ignoriert werden, da das Enforcement-Flag für diese Kategorie gesetzt ist!<br>'));
                        $messages[] = array('html' => '<button class="btn btn-secondary" type="submit" name="" >'._('Ok, Name überarbeiten').'</button>', 'no_linebreak' => true);
                    }

                    $messages[] = array('html' => generateInputHidden('name', $new_name), 'no_linebreak' => true);
                    $messages[] = array('html' => generateInputHidden('category_id', $new_category_id), 'no_linebreak' => true);
                    $messages[] = array('html' => generateInputHidden('description', $new_description), 'no_linebreak' => true);
                    $messages[] = array('html' => generateInputHidden('instock', $new_instock), 'no_linebreak' => true);
                    $messages[] = array('html' => generateInputHidden('mininstock', $new_mininstock), 'no_linebreak' => true);
                    $messages[] = array('html' => generateInputHidden('storelocation_id', $new_storelocation_id), 'no_linebreak' => true);
                    $messages[] = array('html' => generateInputHidden('manufacturer_id', $new_manufacturer_id), 'no_linebreak' => true);
                    $messages[] = array('html' => generateInputHidden('footprint_id', $new_footprint_id), 'no_linebreak' => true);
                    $messages[] = array('html' => generateInputHidden('comment', $new_comment), 'no_linebreak' => 'true');
                    $messages[] = array('html' => generateInputHidden('visible', $new_visible), 'no_linebreak' => 'true');

                    $partname_invalid = true;
                }
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        case 'apply_name_confirmed':
            try {
                $part->setName($new_name);
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;
        case 'apply_attributes':
            try {
                $new_attributes = array(
                    'description'       => $new_description,
                    //'instock'           => $new_instock, //We set this later via setInstock() to create the needed InstockChangeEntry
                    'mininstock'        => $new_mininstock,
                    'id_category'       => $new_category_id,
                    'id_storelocation'  => $new_storelocation_id,
                    'visible'           => $new_visible,
                    'comment'           => $new_comment,
                    'manufacturer_product_url' => $new_manufacturer_url);

                $part->setInstock($new_instock, $change_comment == null ?  _('Bauteil bearbeitet') : $change_comment);

                // do not overwrite (remove!) the footprint or manufacturer if they are disabled (global or in the part's category)
                if (isset($_POST['footprint_id'])) {
                    $new_attributes['id_footprint']    = $new_footprint_id;
                }
                if (isset($_POST['manufacturer_id'])) {
                    $new_attributes['id_manufacturer'] = $new_manufacturer_id;
                }

                if (Part::isValidName($new_name, $part->getCategory())) {
                    $new_attributes['name'] = $new_name;
                    $part->setAttributes($new_attributes, $change_comment);
                    global $config;
                    if ($config['edit_parts']['saved_go_to_info'] xor $rightclicked) {
                        $html->redirect('show_part_info.php?pid=' . $part->getID(), true);
                    }
                } else {
                    $part->setAttributes($new_attributes, $change_comment);

                    $parname_hint = $part->getCategory()->getPartnameHint(true, false);
                    if (empty($parname_hint)) {
                        $messages[] = array('text' => sprintf(_('Der Name "%s" entspricht nicht den Vorgaben!'), $new_name),
                            'strong' => true, 'color' => 'red');
                    } else {
                        $messages[] = array('html' => sprintf(
                            _('Der Name "%s" entspricht nicht den Vorgaben <b>(%s)</b>!'),
                            $new_name,
                            $part->getCategory()->getPartnameHint(true, false)
                        ));
                    }

                    $messages[] = array('text' => _('<br>Hinweis:'), 'strong' => true);
                    $messages[] = array('html' => _('Der Name muss folgendem Format entsprechen: ') . '<b>' . $part->getCategory()->getPartnameRegex(true) . '</b>');
                    if ($part->getCategory()->getPartnameRegexObj()->isEnforced()) {
                        $messages[] = array('html' => _('Dies kann nicht ignoriert werden, da das Enforcement-Flag für diese Kategorie gesetzt ist!<br>'));
                        $messages[] = array('html' => '<button class="btn btn-secondary" type="submit" name="name_edit" >'._('Ok, Name überarbeiten').'</button>', 'no_linebreak' => true);
                    } else {
                        $messages[] = array('html' => _('Möchten sie wirklich fortfahren?<br>'));
                        $messages[] = array('html' => '<button class="btn btn-secondary" type="submit" name="name_edit" >'._('Nein, Name überarbeiten').'</button>', 'no_linebreak' => true);
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
                    $new_obsolete,
                    $new_supplier_url
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

                $orderdetails->setAttributes(array(    'id_supplier'               => $new_supplier_id,
                    'supplierpartnr'            => $new_supplierpartnr,
                    'obsolete'                  => $new_obsolete,
                    'supplier_product_url' => $new_supplier_url));
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        case 'orderdetails_delete':
            try {
                //$orderdetails = Orderdetails::getInstance($database, $current_user, $log, $_REQUEST['orderdetails_delete']);
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
                //$pricedetails = Pricedetails::getInstance($database, $current_user, $log, $pricedetails_id);
                if (! is_object($pricedetails)) {
                    throw new Exception(_('Es ist keine Preisinformation ausgewählt!'));
                }

                $pricedetails->setAttributes(array(    'price'                     => $new_price,
                    'price_related_quantity'    => $new_price_related_quantity,
                    'min_discount_quantity'     => $new_min_discount_quantity));
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        case 'pricedetails_delete':
            try {
                $pricedetails = Pricedetails::getInstance($database, $current_user, $log, $_POST['pricedetails_delete']);
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
                if (empty($new_filename) && (isset($_FILES['attachement_file']) && strlen($_FILES['attachement_file']['name']) == 0)) {
                    throw new Exception(_('Sie müssen entweder ein Dateiname angeben, oder eine Datei zum Hochladen wählen!'));
                }

                $filepath = $config['attachements']['folder_structure'] ? generateAttachementPath(BASE. '/data/media/', $part->getCategory()) : BASE.'/data/media/';

                if (isset($_FILES['attachement_file']) && strlen($_FILES['attachement_file']['name']) > 0) {
                    $new_filename = uploadFile($_FILES['attachement_file'], $filepath);
                } else if (isURL($new_filename) && $download_file) {
                    $downloaded_file_name =  downloadFile($new_filename, $filepath);
                    if ($downloaded_file_name !== '') {
                        $new_filename = $downloaded_file_name;
                    } else {
                        $messages[] = array('text' => _('Die Datei konnte nicht heruntergeladen werden!'), 'strong' => true, 'color' => 'red');
                    }
                }

                $new_attachement = Attachment::add(
                    $database,
                    $current_user,
                    $log,
                    $part,
                    $new_attachement_type_id,
                    $new_filename,
                    $new_name,
                    $new_show_in_table
                );

                if ($new_is_master_picture && $new_attachement->isPicture()) {
                    $part->setMasterPictureAttachementID($new_attachement->getID());
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

                $filepath = $config['attachements']['folder_structure'] ? generateAttachementPath(BASE. '/data/media/', $part->getCategory()) : BASE.'/data/media/';

                if (isset($_FILES['attachement_file']) && strlen($_FILES['attachement_file']['name']) > 0) {
                    $new_filename = uploadFile($_FILES['attachement_file'], $filepath);
                } else if (isURL($new_filename) && $download_file) {
                    $downloaded_file_name =  downloadFile($new_filename, $filepath);
                    if ($downloaded_file_name !== '') {
                        $new_filename = $downloaded_file_name;
                    } else {
                        $messages[] = array('text' => _('Die Datei konnte nicht heruntergeladen werden!'), 'strong' => true, 'color' => 'red');
                    }
                }

                $attachement->setAttributes(array( 'type_id'           => $new_attachement_type_id,
                    'name'              => $new_name,
                    'filename'          => $new_filename,
                    'show_in_table'     => $new_show_in_table));

                if ($new_is_master_picture) {
                    $part->setMasterPictureAttachementID($attachement->getID());
                } else {
                    $master_picture = $part->getMasterPictureAttachement();

                    if (is_object($master_picture) && ($master_picture->getID() == $attachement->getID())) {
                        $part->setMasterPictureAttachementID(null);
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
                $master_picture = $part->getMasterPictureAttachement();
                if (is_object($master_picture) && ($master_picture->getID() == $attachement->getID())) {
                    $part->setMasterPictureAttachementID(null);
                } // remove master picture

                $attachement->delete(true); // the file will be deleted only if there are no other attachements with the same filename
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        case 'delete_part':
            try {
                // check if there are devices with this part. if there are some, the part can't be deleted!
                $devices = $part->getDevices();
                if (count($devices) > 0) {
                    $device_names = '';
                    foreach ($devices as $device) {
                        $device_names .= "\n&nbsp;&nbsp;&bull; ".$device->getFullPath();
                    }
                    throw new Exception(_('Das Bauteil kann nicht gelöscht werden, da es noch in den '.
                            'folgenden Baugruppen verwendet wird:').$device_names);
                }

                $messages[] = array('text' => sprintf(_('Soll das Bauteil "%s" wirklich unwiederruflich gelöscht werden?'), $part->getName()),
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
                $messages[] = array('html' => '<input class="btn btn-secondary" type="submit" value="'._('Nein, nicht löschen').'">', 'no_linebreak' => true);
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
                $close_url = !empty($_SESSION['part_delete_last_link']) ? $_SESSION['part_delete_last_link'] : 'startup.php';
                $messages[] = array('html' => '<br><a class="btn btn-primary" href="' . $close_url .'">'._('Fenster schliessen').'</a>');
                $messages[] = array('html' => '<a class="btn link" href="startup.php">'._('Startseite aufrufen').'</a>');
                $fatal_error = true; // there is no error, but we cannot display the part infos because the part exists no longer :-)
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        case 'search_category':
            $classname = Category::class;
            $search_name = $search_category_name;
            break;
        case 'search_footprint':
            $classname = Footprint::class;
            $search_name = $search_footprint_name;
            break;
        case 'search_storelocation':
            $classname = Storelocation::class;
            $search_name = $search_storelocation_name;
            break;
        case 'search_manufacturer':
            $classname = Manufacturer::class;
            $search_name = $search_manufacturer_name;
            break;
    }

    if (isset($classname)) {
        $print_unsaved_values = true;
        $search_name = trim($search_name);

        try {
            if (strncmp($search_name, '__ID__=', 7) === 0) {
                $searched_element = $classname::getInstance($database, $current_user, $log, (int)str_replace('__ID__=', '', $search_name));
            } else {
                /** @var \PartDB\Interfaces\ISearchable $classname */
                $elements = $classname::search($database, $current_user, $log, $search_name, false);

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
        // special
        $html->setVariable('is_new_part', $is_new_part || $add_one_more_part, 'boolean');

        // part attributes
        if (isset($part) && is_object($part)) {
            $html->setVariable('pid', $part->getID(), 'integer');
            if ($partname_invalid) {
                $html->setVariable('name', $new_name, 'string');
            } else {
                $html->setVariable('name', $part->getName(), 'string');
            }
            $html->setVariable('description', $part->getDescription(false), 'string');
            $html->setVariable('instock', $part->getInstock(), 'integer');
            $html->setVariable('instock_unknown', $part->isInstockUnknown(), 'boolean');
            $html->setVariable('mininstock', $part->getMinInstock(), 'integer');
            $html->setVariable('visible', $part->getVisible(), 'boolean');
            $html->setVariable('comment', $part->getComment(false), 'string');
            $html->setVariable('format_hint', $part->getCategory()->getPartnameHint(true, false), 'string');

            $html->setVariable('default_change_comment', $default_instock_comment, 'string');

            $html->setVariable('manufacturer_url', $part->getManufacturerProductUrl(true), 'string');

            // dropdown lists -> get IDs
            $category_id        = (is_object($part->getCategory())         ?   $part->getCategory()->getID()      : 0);
            $footprint_id       = (is_object($part->getFootprint())        ?   $part->getFootprint()->getID()     : 0);
            $storelocation_id   = (is_object($part->getStorelocation())    ?   $part->getStorelocation()->getID() : 0);
            $manufacturer_id    = (is_object($part->getManufacturer())     ?   $part->getManufacturer()->getID()  : 0);

            // build orderdetails loop
            $orderdetails_loop = array();
            $row_odd = true;
            foreach ($part->getOrderdetails() as $orderdetails) {
                $supplier_list = $root_supplier->buildHtmlTree($orderdetails->getSupplier()->getID(), true, false);
                $pricedetails_loop = array();
                foreach ($orderdetails->getPricedetails() as $pricedetails) {
                    //HTML5 wants a float number with a dot as a decimal point. The browser should change its display correspondingly to HTML locale.
                    $price = str_replace(',', '.', $pricedetails->getPrice(false, $pricedetails->getPriceRelatedQuantity()));

                    $pricedetails_loop[] = array(   'row_odd'                   => ! $row_odd,
                        'orderdetails_id'           => $orderdetails->getID(),
                        'pricedetails_id'           => $pricedetails->getID(),
                        'min_discount_quantity'     => $pricedetails->getMinDiscountQuantity(),
                        'price'                     => $price,
                        'price_related_quantity'    => $pricedetails->getPriceRelatedQuantity());
                }

                if (count($pricedetails_loop) > 0) {
                    $next_min_discount_quantity = $pricedetails_loop[count($pricedetails_loop)-1]['min_discount_quantity'] * 10;
                } else {
                    $next_min_discount_quantity = 1;
                }

                $pricedetails_loop[] = array(       'orderdetails_id'           => $orderdetails->getID(),
                    'pricedetails_id'           => 'new',
                    'min_discount_quantity'     => $next_min_discount_quantity,
                    'price'                     => 0,
                    'price_related_quantity'    => 1);

                $orderdetails_loop[] = array(       'row_odd'                   => $row_odd,
                    'orderdetails_id'           => $orderdetails->getID(),
                    'supplier_list'             => $supplier_list,
                    'supplierpartnr'            => $orderdetails->getSupplierPartNr(),
                    'obsolete'                  => $orderdetails->getObsolete(),
                    'pricedetails'              => $pricedetails_loop,
                    'supplier_url' => $orderdetails->getSupplierProductUrl());
                $row_odd = ! $row_odd;
            }

            // add one additional row -> with this row you can add more orderdetails
            $supplier_list = $root_supplier->buildHtmlTree(0, true, false);
            $orderdetails_loop[] = array(   'row_odd'                   => $row_odd,
                'orderdetails_id'           => 'new',
                'supplier_list'             => $supplier_list,
                'supplierpartnr'            => '',
                'obsolete'                  => false);

            $html->setVariable('orderdetails', $orderdetails_loop);

            // build attachements loop
            $master_picture_id = (is_object($part->getMasterPictureAttachement()) ? $part->getMasterPictureAttachement()->getID() : null);
            $attachements_loop = array();
            $all_attachements = $part->getAttachments();
            $row_odd = true;
            foreach ($all_attachements as $attachement) {
                /** @var  $attachement Attachment */
                $attachement_types_list = $root_attachement_type->buildHtmlTree($attachement->getType()->getID(), true, false);
                $attachements_loop[] = array(   'row_odd'                   => $row_odd,
                    'id'                        => $attachement->getID(),
                    'attachement_types_list'    => $attachement_types_list,
                    'name'                      => $attachement->getName(),
                    'show_in_table'             => $attachement->getShowInTable(),
                    'is_picture'                => $attachement->isPicture(),
                    'is_master_picture'         => $attachement->getID() == $master_picture_id,
                    'filename'                  => str_replace(BASE, BASE_RELATIVE, $attachement->getFilename()),
                    'filename_base_relative'    => str_replace(BASE.'/', '', $attachement->getFilename()),
                    'picture_filename'          => $attachement->isPicture() ? str_replace(BASE, BASE_RELATIVE, $attachement->getFilename()) : '',
                    'download_file'             => $config['attachements']['download_default'] && isURL($attachement->getFilename()));
                $row_odd = ! $row_odd;
            }

            // add one additional row -> with this row you can add more files
            $attachement_types_list = $root_attachement_type->buildHtmlTree(0, true, false);
            $attachements_loop[] = array(   'row_odd'                   => $row_odd,
                'id'                        => 'new',
                'attachement_types_list'    => $attachement_types_list,
                'name'                      => '',
                'is_picture'                => true,
                'show_in_table'             => false,
                'is_master_picture'         => false,
                'filename'                  => '',
                'filename_base_relative'    => '',
                'picture_filename'          => '',
                'download_file'             => $config['attachements']['download_default']);

            $html->setVariable('attachements_loop', $attachements_loop);
        }

        if ($print_unsaved_values || ! isset($part) || (! is_object($part))) {
            if (isset($new_category_id)) {
                $cat = Category::getInstance($database, $current_user, $log, $new_category_id);
                if (empty($new_description)) {
                    $new_description = $cat->getDefaultDescription(true, false);
                }
                if (empty($new_comment)) {
                    $new_comment = $cat->getDefaultComment(true, false);
                }
            }

            $html->setVariable('name', $new_name, 'string');
            $html->setVariable('description', $new_description, 'string');
            $html->setVariable('instock', $new_instock, 'integer');
            $html->setVariable('mininstock', $new_mininstock, 'integer');
            $html->setVariable('visible', $new_visible, 'boolean');
            $html->setVariable('comment', $new_comment, 'string');
            $html->setVariable('manufacturer_url', $new_manufacturer_url, 'string');

            $category_id        = $new_category_id;
            $footprint_id       = $new_footprint_id;
            $storelocation_id   = $new_storelocation_id;
            $manufacturer_id    = $new_manufacturer_id;
        }

        if (isset($searched_element) && $searched_element instanceof Category) {
            $category_id = $searched_element->getID();
        }

        if (isset($searched_element) && $searched_element instanceof Footprint) {
            $footprint_id = $searched_element->getID();
        }

        if (isset($searched_element) && $searched_element instanceof Storelocation) {
            $storelocation_id = $searched_element->getID();
        }

        if (isset($searched_element) && $searched_element instanceof Manufacturer) {
            $manufacturer_id = $searched_element->getID();
        }


        // dropdown lists -> generate lists
        $manufacturer_list  = $root_manufacturer->buildHtmlTree($manufacturer_id, true, false);
        $category_list      = $root_category->buildHtmlTree($category_id, true, false);
        $storelocation_list = $root_storelocation->buildHtmlTree($storelocation_id, true, false);
        $footprint_list     = $root_footprint->buildHtmlTree($footprint_id, true, false);

        // the category ID is used for creating a new part (in *.tmpl file the latest DIV element)
        $html->setVariable('category_id', $category_id, 'integer');

        // dropdown lists -> set html variables
        $html->setVariable('manufacturer_list', $manufacturer_list, 'string');
        $html->setVariable('category_list', $category_list, 'string');
        $html->setVariable('storelocation_list', $storelocation_list, 'string');
        $html->setVariable('footprint_list', $footprint_list, 'string');

        // global/category stuff
        $category = Category::getInstance($database, $current_user, $log, $category_id);
        $html->setVariable('disable_footprints', $config['footprints']['disable'] || $category->getDisableFootprints(true), 'boolean');
        $html->setVariable('disable_manufacturers', $config['manufacturers']['disable'] || $category->getDisableManufacturers(true), 'boolean');
        $html->setVariable('max_upload_filesize', ini_get('upload_max_filesize'), 'string');
        $html->setVariable('downloads_enable', $config['allow_server_downloads'], 'boolean');

        $html->setVariable('currency_symbol', getCurrencySymbol(), 'string');

        $html->setVariable('back_link', $_SESSION['part_delete_last_link'], 'string');
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        $fatal_error = true;
    }
}

try {
    //Set permissions
    $html->setVariable('can_delete', $current_user->canDo(PermissionManager::PARTS, PartPermission::DELETE));
    $html->setVariable('can_edit', $current_user->canDo(PermissionManager::PARTS, PartPermission::EDIT));
    $html->setVariable('can_create', $current_user->canDo(PermissionManager::PARTS, PartPermission::CREATE));
    $html->setVariable('can_move', $current_user->canDo(PermissionManager::PARTS, PartPermission::MOVE));
    $html->setVariable('can_read', $current_user->canDo(PermissionManager::PARTS, PartPermission::READ));
    $html->setVariable('can_name', $current_user->canDo(PermissionManager::PARTS_NAME, PartAttributePermission::EDIT));
    $html->setVariable('can_description', $current_user->canDo(PermissionManager::PARTS_DESCRIPTION, PartAttributePermission::EDIT));
    $html->setVariable('can_comment', $current_user->canDo(PermissionManager::PARTS_COMMENT, PartAttributePermission::EDIT));
    $html->setVariable('can_instock', $current_user->canDo(PermissionManager::PARTS_INSTOCK, PartAttributePermission::EDIT));
    $html->setVariable('can_mininstock', $current_user->canDo(PermissionManager::PARTS_MININSTOCK, PartAttributePermission::EDIT));
    $html->setVariable('can_storelocation', $current_user->canDo(PermissionManager::PARTS_STORELOCATION, PartAttributePermission::EDIT));
    $html->setVariable('can_footprint', $current_user->canDo(PermissionManager::PARTS_FOOTPRINT, PartAttributePermission::EDIT));
    $html->setVariable('can_manufacturer', $current_user->canDo(PermissionManager::PARTS_MANUFACTURER, PartAttributePermission::EDIT));

    $html->setVariable('can_attachement_edit', $current_user->canDo(PermissionManager::PARTS_ATTACHEMENTS, CPartAttributePermission::EDIT));
    $html->setVariable('can_attachement_delete', $current_user->canDo(PermissionManager::PARTS_ATTACHEMENTS, CPartAttributePermission::DELETE));
    $html->setVariable('can_attachement_create', $current_user->canDo(PermissionManager::PARTS_ATTACHEMENTS, CPartAttributePermission::CREATE));

    $html->setVariable('can_orderdetails_edit', $current_user->canDo(PermissionManager::PARTS_ORDERDETAILS, CPartAttributePermission::EDIT));
    $html->setVariable('can_orderdetails_delete', $current_user->canDo(PermissionManager::PARTS_ORDERDETAILS, CPartAttributePermission::DELETE));
    $html->setVariable('can_orderdetails_create', $current_user->canDo(PermissionManager::PARTS_ORDERDETAILS, CPartAttributePermission::CREATE));

    $html->setVariable('can_prices_edit', $current_user->canDo(PermissionManager::PARTS_PRICES, CPartAttributePermission::EDIT));
    $html->setVariable('can_prices_delete', $current_user->canDo(PermissionManager::PARTS_PRICES, CPartAttributePermission::DELETE));
    $html->setVariable('can_prices_create', $current_user->canDo(PermissionManager::PARTS_PRICES, CPartAttributePermission::CREATE));
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
if (isset($_REQUEST['ajax'])) {
    $html->setVariable('ajax_request', true);
}

// an empty string in "$reload_link" means that the reload-button won't be visible
$reload_link = ($fatal_error && ($action != 'delete_part_confirmed')) ? 'edit_part_info.php?pid='.$part_id : '';
$html->printHeader($messages, $reload_link);


if (! $fatal_error) {
    $html->printTemplate('part');

    if (! ($is_new_part || $add_one_more_part)) {
        if (!$config['suppliers']['disable']) { //Dont print orderdetails template, when suppliers are disabled.
            $html->printTemplate('orderdetails');
        }
        $html->printTemplate('attachements');
        $html->printTemplate('actions');
    }
}

$html->printFooter();
