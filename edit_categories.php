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

use PartDB\Category;
use PartDB\Database;
use PartDB\HTML;
use PartDB\Log;
use PartDB\PartProperty\PartNameRegEx;
use PartDB\User;

$messages = array();
$fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

/********************************************************************************
 *
 *   Evaluate $_REQUEST
 *
 *   Notes:
 *       - "$selected_id == 0" means that we will show the form for creating a new category
 *       - the $new_* variables contains the new values after editing an existing
 *           or creating a new category
 *
 *********************************************************************************/

$selected_id                = isset($_REQUEST['selected_id'])   ? (integer)$_REQUEST['selected_id'] : 0;
$new_name                   = isset($_REQUEST['name'])          ? (string)$_REQUEST['name']         : '';
$new_parent_id              = isset($_REQUEST['parent_id'])     ? (integer)$_REQUEST['parent_id']   : 0;
$new_disable_footprints     = isset($_REQUEST['disable_footprints']);
$new_disable_manufacturers  = isset($_REQUEST['disable_manufacturers']);
$new_disable_autodatasheets = isset($_REQUEST['disable_autodatasheets']);
$new_disable_properties     = isset($_REQUEST['disable_properties']);
$add_more                   = isset($_REQUEST['add_more']);
$new_default_description    = isset($_REQUEST['default_description'])  ? (string)$_REQUEST['default_description']  : '';
$new_default_comment        = isset($_REQUEST['default_comment'])  ? (string)$_REQUEST['default_comment']  : '';

$new_partname_regex         = isset($_REQUEST['partname_regex'])   ? (string)$_REQUEST['partname_regex']    : '';
$new_partname_hint          = isset($_REQUEST['partname_hint'])   ? (string)$_REQUEST['partname_hint']    : '';

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

$html = new HTML($config['html']['theme'], $config['html']['custom_css'], _('Kategorien'));

try {
    $database           = new Database();
    $log                = new Log($database);
    $current_user       = new User($database, $current_user, $log, 1); // admin
    $root_category      = new Category($database, $current_user, $log, 0);

    if ($selected_id > 0) {
        $selected_category = new Category($database, $current_user, $log, $selected_id);
    } else {
        $selected_category = null;
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
                $new_category = Category::add(
                    $database,
                    $current_user,
                    $log,
                    $new_name,
                    $new_parent_id,
                    $new_disable_footprints,
                    $new_disable_manufacturers,
                    $new_disable_autodatasheets,
                    $new_disable_properties,
                    $new_default_description,
                    $new_default_comment
                );

                $new_category->setPartnameRegex($new_partname_regex);
                $new_category->setPartnameHint($new_partname_hint);

                $html->setVariable('refresh_navigation_frame', true, 'boolean');

                if (! $add_more) {
                    $selected_category = $new_category;
                    $selected_id = $selected_category->getID();
                }
            } catch (Exception $e) {
                $messages[] = array('text' => _('Die neue Kategorie konnte nicht angelegt werden!'), 'strong' => true, 'color' => 'red');
                $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
            }
            break;

        case 'delete':
            try {
                if (! is_object($selected_category)) {
                    throw new Exception(_('Es ist keine Kategorie markiert oder es trat ein Fehler auf!'));
                }

                $parts = $selected_category->getParts();
                $count = count($parts);

                if ($count > 0) {
                    $messages[] = array('text' => sprintf(_('Es gibt noch %d Bauteile in dieser Kategorie, '.
                        'daher kann die Kategorie nicht gelöscht werden.'), $count), 'strong' => true, 'color' => 'red');
                } else {
                    $messages[] = array('text' => sprintf(_('Soll die Kategorie "%s'.
                        '" wirklich unwiederruflich gelöscht werden?'), $selected_category->getFullPath()), 'strong' => true, 'color' => 'red');
                    $messages[] = array('text' => '<br>'._('Hinweise:'), 'strong' => true);
                    $messages[] = array('text' => '&nbsp;&nbsp;&bull; '._('Es gibt keine Bauteile in dieser Kategorie.'));
                    $messages[] = array('text' => '&nbsp;&nbsp;&bull; '._('Beinhaltet diese Kategorie noch Unterkategorien, dann werden diese eine Ebene nach oben verschoben.'));
                    $messages[] = array('html' => '<input type="hidden" name="selected_id" value="'.$selected_category->getID().'">');
                    $messages[] = array('html' => '<input class="btn btn-default" type="submit" name="" value="'._('Nein, nicht löschen').'">', 'no_linebreak' => true);
                    $messages[] = array('html' => '<input class="btn btn-danger" type="submit" name="delete_confirmed" value="'._('Ja, Kategorie löschen').'">');
                }
            } catch (Exception $e) {
                $messages[] = array('text' => _('Es trat ein Fehler auf!'), 'strong' => true, 'color' => 'red');
                $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
            }
            break;

        case 'delete_confirmed':
            try {
                if (! is_object($selected_category)) {
                    throw new Exception(_('Es ist keine Kategorie markiert oder es trat ein Fehler auf!'));
                }

                $selected_category->delete();
                $selected_category = null;

                $html->setVariable('refresh_navigation_frame', true, 'boolean');
            } catch (Exception $e) {
                $messages[] = array('text' => _('Die Kategorie konnte nicht gelöscht werden!'), 'strong' => true, 'color' => 'red');
                $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
            }
            break;

        case 'apply':
            try {
                if (! is_object($selected_category)) {
                    throw new Exception(_('Es ist keine Kategorie markiert oder es trat ein Fehler auf!'));
                }

                $selected_category->setAttributes(array('name'                     => $new_name,
                    'parent_id'                => $new_parent_id,
                    'disable_footprints'       => $new_disable_footprints,
                    'disable_manufacturers'    => $new_disable_manufacturers,
                    'disable_autodatasheets'   => $new_disable_autodatasheets,
                    'disable_properties'       => $new_disable_properties,
                    'default_description'      => $new_default_description,
                    'default_comment'          => $new_default_comment,
                    'partname_regex'           => $new_partname_regex,
                    'partname_hint'            => $new_partname_hint));

                $html->setVariable('refresh_navigation_frame', true, 'boolean');
            } catch (Exception $e) {
                $messages[] = array('text' => _('Die neuen Werte konnten nicht gespeichert werden!'), 'strong' => true, 'color' => 'red');
                $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
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
        if (is_object($selected_category)) {
            $parent_id = $selected_category->getParentID();
            $html->setVariable('id', $selected_category->getID(), 'integer');
            $name = $selected_category->getName();

            //Default description/comment fields
            $default_description = $selected_category->getDefaultDescription(false);
            $default_comment = $selected_category->getDefaultComment(false);

            $default_description_parent = $selected_category->getDefaultDescription(true);
            $default_comment_parent = $selected_category->getDefaultComment(true);
            $html->setVariable('default_description_parent', $default_description_parent, 'string');
            $html->setVariable('default_comment_parent', $default_comment_parent, 'string');

            //Partname fields
            $partname_regex = $selected_category->getPartnameRegexRaw(false, true);
            $partname_hint = $selected_category->getPartnameHint(false);

            $partname_regex_parent = $selected_category->getPartnameRegexRaw(true);
            $partname_hint_parent = $selected_category->getPartnameHint(true);
            $html->setVariable('partname_regex_parent', $partname_regex_parent, 'string');
            $html->setVariable('partname_hint_parent', $partname_hint_parent, 'string');

            //Disable fields
            $disable_footprints = $selected_category->getDisableFootprints(true);
            $disable_manufacturers = $selected_category->getDisableManufacturers(true);
            $disable_autodatasheets = $selected_category->getDisableAutodatasheets(true);
            $disable_properties = $selected_category->getDisableProperties(true);

            $html->setVariable('parent_disable_footprints', ($selected_category->getDisableFootprints(true)
                && (! $selected_category->getDisableFootprints(false))), 'boolean');
            $html->setVariable('parent_disable_manufacturers', ($selected_category->getDisableManufacturers(true)
                && (! $selected_category->getDisableManufacturers(false))), 'boolean');
            $html->setVariable('parent_disable_autodatasheets', ($selected_category->getDisableAutodatasheets(true)
                && (! $selected_category->getDisableAutodatasheets(false))), 'boolean');

            $html->setVariable('parent_disable_properties', ($selected_category->getDisableProperties(true)
                && (! $selected_category->getDisableProperties(false))), 'boolean');
        } elseif ($action == 'add') {
            $parent_id = $new_parent_id;
            $name = $new_name;
            $disable_footprints = $new_disable_footprints;
            $disable_manufacturers = $new_disable_manufacturers;
            $disable_autodatasheets = $new_disable_autodatasheets;
            $disable_properties = $new_disable_properties;
            $default_description = $new_default_description;
            $default_comment = $new_default_comment;
            $partname_regex = $new_partname_regex;
            $partname_hint = $new_partname_hint;
        } else {
            $parent_id = 0;
            $name = '';
            $disable_footprints = false;
            $disable_manufacturers = false;
            $disable_autodatasheets = false;
            $disable_properties = false;
            $default_description = "";
            $default_comment = "";
            $partname_hint = "";
            $partname_regex = "";
        }

        $html->setVariable('name', $name, 'string');
        $html->setVariable('disable_footprints', $disable_footprints, 'boolean');
        $html->setVariable('disable_manufacturers', $disable_manufacturers, 'boolean');
        $html->setVariable('disable_autodatasheets', $disable_autodatasheets, 'boolean');
        $html->setVariable('disable_properties', $disable_properties, 'boolean');

        $html->setVariable('default_description', $default_description, 'string');
        $html->setVariable('default_comment', $default_comment, 'string');

        $html->setVariable('partname_regex', $partname_regex, 'string');
        $html->setVariable('partname_hint', $partname_hint, 'string');
        $html->setVariable('partname_input_pattern', PartNameRegEx::getPattern(true), 'string');

        $category_list = $root_category->buildHtmlTree($selected_id, true, false);
        $html->setVariable('category_list', $category_list, 'string');

        $parent_category_list = $root_category->buildHtmlTree($parent_id, true, true);
        $html->setVariable('parent_category_list', $parent_category_list, 'string');
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

$reload_link = $fatal_error ? 'edit_categories.php' : '';    // an empty string means that the...
$html->printHeader($messages, $reload_link);                // ...reload-button won't be visible

if (! $fatal_error) {
    $html->printTemplate('edit_categories');
}

$html->printFooter();
