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

/*
 * Please note:
 *  The files "edit_categories.php", "edit_footprints.php", "edit_manufacturers.php",
 *  "edit_suppliers.php", "edit_devices.php", "edit_storelocations.php" and "edit_filetypes.php"
 *  are quite similar.
 *  If you make changes in one of them, please check if you should change the other files too.
 */

include_once('start_session.php');

use PartDB\Database;
use PartDB\HTML;
use PartDB\Log;
use PartDB\Storelocation;
use PartDB\User;

$messages = array();
$fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

/********************************************************************************
 *
 *   Evaluate $_REQUEST
 *
 *   Notes:
 *       - "$selected_id == 0" means that we will show the form for creating a new storelocation
 *       - the $new_* variables contains the new values after editing an existing
 *           or creating a new storelocation
 *
 *********************************************************************************/

$selected_id        = isset($_REQUEST['selected_id'])   ? (integer)$_REQUEST['selected_id'] : 0;
$new_name           = isset($_REQUEST['name'])          ? (string)$_REQUEST['name']         : '';
$new_parent_id      = isset($_REQUEST['parent_id'])     ? (integer)$_REQUEST['parent_id']   : 0;
$new_is_full        = isset($_REQUEST['is_full']);
$create_series      = isset($_REQUEST['series']);
$series_from        = isset($_REQUEST['series_from'])   ? $_REQUEST['series_from'] : 1;
$series_to          = isset($_REQUEST['series_to'])     ? $_REQUEST['series_to']   : 1;
$add_more           = isset($_REQUEST['add_more']);

$action = 'default';
if (isset($_REQUEST["add"])) {
    $action = 'add';
}
if (isset($_REQUEST["delete"])) {
    $action = 'delete';
}
if (isset($_REQUEST["delete_confirmed"])) {
    $action = 'delete_confirmed';
}
if (isset($_REQUEST["apply"])) {
    $action = 'apply';
}

/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $config['html']['custom_css'], _('Lagerorte'));

try {
    $database           = new Database();
    $log                = new Log($database);
    $current_user       = new User($database, $current_user, $log, 1); // admin
    $root_storelocation = new Storelocation($database, $current_user, $log, 0);

    if ($selected_id > 0) {
        $selected_storelocation = new Storelocation($database, $current_user, $log, $selected_id);
    } else {
        $selected_storelocation = null;
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

if (! $fatal_error) {
    switch ($action) {
        case 'add':
            try {
                if ($create_series) {
                    $width  = mb_strlen((string) $series_to); // determine the width of second argument
                    $format = "%0". (int)$width ."s";

                    foreach (range($series_from, $series_to) as $index) {
                        $new_storelocation_name = $new_name . sprintf($format, $index);
                        $new_storelocation = Storelocation::add(
                            $database,
                            $current_user,
                            $log,
                            $new_storelocation_name,
                            $new_parent_id,
                            $new_is_full
                        );
                    }
                } else {
                    $new_storelocation = Storelocation::add(
                        $database,
                        $current_user,
                        $log,
                        $new_name,
                        $new_parent_id,
                        $new_is_full
                    );
                }

                if (! $add_more) {
                    $selected_storelocation = $new_storelocation;
                    $selected_id = $selected_storelocation->getID();
                }
            } catch (Exception $e) {
                $messages[] = array('text' => _('Der neue Lagerort konnte nicht angelegt werden!'), 'strong' => true, 'color' => 'red');
                $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
            }
            break;

        case 'delete':
            if (is_object($selected_storelocation)) {
                try {
                    $parts = $selected_storelocation->getParts();
                    $count = count($parts);

                    if ($count > 0) {
                        $messages[] = array('text' => sprintf(_('Es gibt noch %d Bauteile an diesem Lagerort, '.
                            'daher kann der Lagerort nicht gelöscht werden.'), $count), 'strong' => true, 'color' => 'red');
                    } else {
                        $messages[] = array('text' => sprintf(_('Soll der Lagerort "%s'.
                            '" wirklich unwiederruflich gelöscht werden?'), $selected_storelocation->getFullPath()), 'strong' => true, 'color' => 'red');
                        $messages[] = array('text' => _('<br>Hinweise:'), 'strong' => true);
                        $messages[] = array('text' => _('&nbsp;&nbsp;&bull; Es gibt keine Bauteile an diesem Lagerort.'));
                        $messages[] = array('text' => _('&nbsp;&nbsp;&bull; Beinhaltet dieser Lagerort noch Unterlagerorte, dann werden diese eine Ebene nach oben verschoben.'));
                        $messages[] = array('html' => '<input type="hidden" name="selected_id" value="'.$selected_storelocation->getID().'">');
                        $messages[] = array('html' => '<input type="submit" class="btn btn-default" name="" value="'._('Nein, nicht löschen').'">', 'no_linebreak' => true);
                        $messages[] = array('html' => '<input type="submit" class="btn btn-danger" name="delete_confirmed" value="'._('Ja, Lagerort löschen').'">');
                    }
                } catch (Exception $e) {
                    $messages[] = array('text' => _('Es trat ein Fehler auf!'), 'strong' => true, 'color' => 'red');
                    $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
                }
            } else {
                $messages[] = array('text' => _('Es ist kein Lagerort markiert oder es trat ein Fehler auf!'),
                    'strong' => true, 'color' => 'red');
            }
            break;

        case 'delete_confirmed':
            if (is_object($selected_storelocation)) {
                try {
                    $selected_storelocation->delete();
                    $selected_storelocation = null;
                } catch (Exception $e) {
                    $messages[] = array('text' => _('Der Lagerort konnte nicht gelöscht werden!'), 'strong' => true, 'color' => 'red');
                    $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
                }
            } else {
                $messages[] = array('text' => _('Es ist kein Lagerort markiert oder es trat ein Fehler auf!'),
                    'strong' => true, 'color' => 'red');
            }
            break;

        case 'apply':
            if (is_object($selected_storelocation)) {
                try {
                    $selected_storelocation->setAttributes(array(  'name'       => $new_name,
                        'parent_id'  => $new_parent_id,
                        'is_full'    => $new_is_full));
                } catch (Exception $e) {
                    $messages[] = array('text' => _('Die neuen Werte konnten nicht gespeichert werden!'), 'strong' => true, 'color' => 'red');
                    $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
                }
            } else {
                $messages[] = array('text' => _('Es ist kein Lagerort markiert oder es trat ein Fehler auf!'),
                    'strong' => true, 'color' => 'red');
            }
            break;
    }
}

/********************************************************************************
 *
 *   Set the rest of the HTML variables
 *
 *********************************************************************************/

$html->setVariable('add_more', $add_more, 'boolean');

if (! $fatal_error) {
    try {
        if (is_object($selected_storelocation)) {
            $parent_id = $selected_storelocation->getParentID();
            $html->setVariable('id', $selected_storelocation->getID(), 'integer');
            $name = $selected_storelocation->getName();
            $is_full = $selected_storelocation->getIsFull();
        } elseif ($action == 'add') {
            $parent_id = $new_parent_id;
            $name = $new_name;
            $is_full = $new_is_full;
        } else {
            $parent_id = 0;
            $name = '';
            $is_full = false;
        }

        $html->setVariable('name', $name, 'string');
        $html->setVariable('is_full', $is_full, 'boolean');

        $storelocation_list = $root_storelocation->buildHtmlTree($selected_id, true, false);
        $html->setVariable('storelocation_list', $storelocation_list, 'string');

        $parent_storelocation_list = $root_storelocation->buildHtmlTree($parent_id, true, true);
        $html->setVariable('parent_storelocation_list', $parent_storelocation_list, 'string');
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red', );
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

$reload_link = $fatal_error ? 'edit_storelocations.php' : '';   // an empty string means that the...
$html->printHeader($messages, $reload_link);                   // ...reload-button won't be visible

if (! $fatal_error) {
    $html->printTemplate('edit_storelocations');
}

$html->printFooter();
