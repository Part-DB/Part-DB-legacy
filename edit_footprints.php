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

include_once  __DIR__ . '/start_session.php';

use PartDB\Database;
use PartDB\Footprint;
use PartDB\HTML;
use PartDB\Log;
use PartDB\User;
use PartDB\Permissions\StructuralPermission;
use PartDB\Permissions\PermissionManager;

$messages = array();
$fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

/********************************************************************************
 *
 *   Evaluate $_REQUEST
 *
 *   Notes:
 *       - "$selected_id == 0" means that we will show the form for creating a new footprint
 *       - the $new_* variables contains the new values after editing an existing
 *           or creating a new footprint
 *
 *********************************************************************************/

$selected_id                    = isset($_REQUEST['selected_id'])                   ? (int)$_REQUEST['selected_id']                     : 0;
$new_name                       = isset($_POST['name'])                          ? trim((string)$_POST['name'])                       : '';
$new_parent_id                  = isset($_POST['parent_id'])                     ? (int)$_POST['parent_id']                       : 0;
$new_filename                   = isset($_POST['filename'])                      ? toUnixPath(trim((string)$_POST['filename']))     : '';
$new_3d_filename                = isset($_POST['filename_3d'])                   ? toUnixPath(trim((string)$_POST['filename_3d']))  : '';
$new_comment                    = isset($_POST['comment'])       ? (string)$_POST['comment']      : '';

if ((strlen($new_filename) > 0) && (! isPathabsoluteAndUnix($new_filename))) {
    if (!strcontains($new_filename, 'img/footprints/')) {
        $new_filename = 'img/footprints/' . $new_filename;
    }
    $new_filename = BASE.'/'.$new_filename;
} // switch from relative path (like "img/foo.png") to absolute path (like "/var/www/part-db/img/foo.png")

if ((strlen($new_3d_filename) > 0) && (! isPathabsoluteAndUnix($new_3d_filename))) {
    if (!strcontains($new_3d_filename, 'models/')) {
        $new_3d_filename = 'models/' . $new_3d_filename;
    }
    $new_3d_filename = BASE.'/'.$new_3d_filename;
} // switch from relative path (like "img/foo.png") to absolute path (like "/var/www/part-db/img/foo.png")

$add_more                       = isset($_POST['add_more']);

$broken_footprints_count        = isset($_POST['broken_footprints_count'])       ? (int)$_POST['broken_footprints_count']     : 0;
$save_all_proposed_filenames    = isset($_POST['save_all_proposed_filenames']);

$broken_3d_footprints_count     = isset($_POST['broken_3d_footprints_count'])       ? (int)$_POST['broken_3d_footprints_count']     : 0;
$save_all_proposed_3d_filenames = isset($_POST['save_all_proposed_3d_filenames']);

$action = 'default';
if (isset($_POST['add'])) {
    $action = 'add';
}
if (isset($_POST['delete'])) {
    $action = 'delete';
}
if (isset($_POST['delete_confirmed'])) {
    $action = 'delete_confirmed';
}
if (isset($_POST['apply'])) {
    $action = 'apply';
}
if (isset($_POST['save_proposed_filenames'])) {
    $action = 'save_proposed_filenames';
}
if (isset($_POST['save_all_proposed_filenames'])) {
    $action = 'save_proposed_filenames';
}
if (isset($_POST['save_proposed_3d_filenames'])) {
    $action = 'save_proposed_3d_filenames';
}
if (isset($_POST['save_all_proposed_3d_filenames'])) {
    $action = 'save_proposed_3d_filenames';
}


/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $user_config['theme'], _('Footprints'));

try {
    $database           = new Database();
    $log                = new Log($database);
    $current_user       = User::getLoggedInUser($database, $log);
    $root_footprint     = Footprint::getInstance($database, $current_user, $log, 0);

    $current_user->tryDo(PermissionManager::FOOTRPINTS, StructuralPermission::READ);

    if ($selected_id > 0) {
        $selected_footprint = Footprint::getInstance($database, $current_user, $log, $selected_id);
    } else {
        $selected_footprint = null;
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
                $filepath = BASE . '/data/media/footprints/';

                if (isset($_FILES['footprint_file']) && strlen($_FILES['footprint_file']['name']) > 0) {
                    $new_filename = uploadFile($_FILES['footprint_file'], $filepath);
                } else if (isURL($new_filename)) {
                    $downloaded_file_name =  downloadFile($new_filename, $filepath);
                    if ($downloaded_file_name !== '') {
                        $new_filename = $downloaded_file_name;
                    } else {
                        $messages[] = array('text' => _('Die Datei konnte nicht heruntergeladen werden!'), 'strong' => true, 'color' => 'red');
                    }
                }

                $new_footprint = Footprint::add(
                    $database,
                    $current_user,
                    $log,
                    $new_name,
                    $new_parent_id,
                    $new_filename,
                    $new_3d_filename,
                    $new_comment
                );

                if (! $add_more) {
                    $selected_footprint = $new_footprint;
                    $selected_id = $selected_footprint->getID();
                }
            } catch (Exception $e) {
                $messages[] = array('text' => _('Der neue Footprint konnte nicht angelegt werden!'), 'strong' => true, 'color' => 'red');
                $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
            }
            break;

        case 'delete':
            try {
                if (! is_object($selected_footprint)) {
                    throw new Exception(_('Es ist kein Footprint markiert oder es trat ein Fehler auf!'));
                }

                $parts = $selected_footprint->getParts();
                $count = count($parts);

                if ($count > 0) {
                    $messages[] = array('text' => sprintf(_('Es gibt noch %d Bauteile mit diesem Footprint, '.
                        'daher kann der Footprint nicht gelöscht werden.'), $count), 'strong' => true, 'color' => 'red');
                } else {
                    $messages[] = array('text' => sprintf(_('Soll der Footprint "%s'.
                        '" wirklich unwiederruflich gelöscht werden?'), $selected_footprint->getFullPath()), 'strong' => true, 'color' => 'red');
                    $messages[] = array('text' => _('<br>Hinweise:'), 'strong' => true);
                    $messages[] = array('text' => _('&nbsp;&nbsp;&bull; Es gibt keine Bauteile mit diesem Footprint.'));
                    $messages[] = array('text' => _('&nbsp;&nbsp;&bull; Beinhaltet dieser Footprint noch Unterfootprints, dann werden diese eine Ebene nach oben verschoben.'));
                    $messages[] = array('html' => '<input type="hidden" name="selected_id" value="'.$selected_footprint->getID().'">');
                    $messages[] = array('html' => '<input type="submit" class="btn btn-secondary" name="" value="'._('Nein, nicht löschen').'">', 'no_linebreak' => true);
                    $messages[] = array('html' => '<input type="submit" class="btn btn-danger" name="delete_confirmed" value="'._('Ja, Footprint löschen').'">');
                }
            } catch (Exception $e) {
                $messages[] = array('text' => _('Es trat ein Fehler auf!'), 'strong' => true, 'color' => 'red');
                $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
            }
            break;

        case 'delete_confirmed':
            try {
                if (! is_object($selected_footprint)) {
                    throw new Exception(_('Es ist kein Footprint markiert oder es trat ein Fehler auf!'));
                }

                $selected_footprint->delete();
                $selected_footprint = null;
            } catch (Exception $e) {
                $messages[] = array('text' => _('Der Footprint konnte nicht gelöscht werden!'), 'strong' => true, 'color' => 'red');
                $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
            }
            break;

        case 'apply':
            try {
                if (! is_object($selected_footprint)) {
                    throw new Exception(_('Es ist kein Footprint markiert oder es trat ein Fehler auf!'));
                }

                $filepath = BASE . '/data/media/footprints/';

                if (isset($_FILES['footprint_file']) && strlen($_FILES['footprint_file']['name']) > 0) {
                    $new_filename = uploadFile($_FILES['footprint_file'], $filepath);
                } else if (isURL($new_filename)) {
                    $downloaded_file_name =  downloadFile($new_filename, $filepath);
                    if ($downloaded_file_name !== '') {
                        $new_filename = $downloaded_file_name;
                    } else {
                        $messages[] = array('text' => _('Die Datei konnte nicht heruntergeladen werden!'), 'strong' => true, 'color' => 'red');
                    }
                }



                $selected_footprint->setAttributes(array(  'name'          => $new_name,
                    'parent_id'     => $new_parent_id,
                    'filename'      => $new_filename,
                    'filename_3d'   => $new_3d_filename,
                    'comment' => $new_comment));
            } catch (Exception $e) {
                $messages[] = array('text' => _('Die neuen Werte konnten nicht gespeichert werden!'), 'strong' => true, 'color' => 'red');
                $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
            }
            break;

        case 'save_proposed_filenames':
            $errors = array();
            for ($i=0; $i < $broken_footprints_count; $i++) {
                $spf_footprint_id   = $_POST['broken_footprint_id_' . $i] ?? -1; // -1 will produce an error
                $spf_new_filename   = isset($_POST['proposed_filename_'.$i])    ? toUnixPath($_POST['proposed_filename_'.$i])   : null;
                $spf_checked        = isset($_POST['filename_checkbox_'.$i])     || $save_all_proposed_filenames;

                if ((strlen($spf_new_filename) > 0) && (! isPathabsoluteAndUnix($spf_new_filename))) {
                    $spf_new_filename = BASE.'/'.$spf_new_filename;
                } // switch from relative path (like "img/foo.png") to absolute path (like "/var/www/part-db/img/foo.png")

                try {
                    if ($spf_checked) {
                        $spf_broken_footprint = Footprint::getInstance($database, $current_user, $log, $spf_footprint_id);
                        $spf_broken_footprint->setFilename($spf_new_filename);
                    }
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }

            foreach ($errors as $error) {
                $messages[] = array('text' => _('Fehlermeldung: ').$error, 'color' => 'red');
            }

            break;

        case 'save_proposed_3d_filenames':
            $errors = array();
            for ($i=0; $i < $broken_3d_footprints_count; $i++) {
                $spf_footprint_id   = isset($_POST['broken_3d_footprint_id_'.$i])  ? $_POST['broken_3d_footprint_id_'.$i] : -1; // -1 will produce an error
                $spf_new_filename   = isset($_POST['proposed_3d_filename_'.$i])    ? toUnixPath($_POST['proposed_3d_filename_'.$i])   : null;
                $spf_checked        = isset($_POST['filename_3d_checkbox_'.$i])     || $save_all_proposed_3d_filenames;

                if ((strlen($spf_new_filename) > 0) && (! isPathabsoluteAndUnix($spf_new_filename))) {
                    $spf_new_filename = BASE.'/'.$spf_new_filename;
                } // switch from relative path (like "img/foo.png") to absolute path (like "/var/www/part-db/img/foo.png")

                try {
                    if ($spf_checked) {
                        $spf_broken_footprint = Footprint::getInstance($database, $current_user, $log, $spf_footprint_id);
                        $spf_broken_footprint->set3dFilename($spf_new_filename);
                    }
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }

            foreach ($errors as $error) {
                $messages[] = array('text' => 'Fehlermeldung: '.$error, 'color' => 'red');
            }

            break;
    }
}

/********************************************************************************
 *
 *   List broken filename footprints
 *
 *********************************************************************************/

if (! $fatal_error) {
    try {
        $broken_filename_footprints = Footprint::getBrokenFilenameFootprints($database, $current_user, $log);
        $broken_filename_loop = array();

        if (count($broken_filename_footprints) > 0) {
            // get all available files for the proposed footprint images
            $available_proposed_files = array_merge(findAllFiles(BASE.'/img/', true), findAllFiles(BASE.'/data/media/', true));

            // read the PHP constant "max_input_vars"
            $max_input_vars = ((ini_get('max_input_vars') !== false) ? (int)ini_get('max_input_vars') : 999999);

            foreach ($broken_filename_footprints as $i => $iValue) {
                // avoid too many post variables
                if ($i*10 >= $max_input_vars) {
                    break;
                }

                // avoid too long execution time and a huge HTML table
                if ($i >= 100) {
                    break;
                }

                /** @var Footprint $footprint */
                $footprint = $iValue;
                $proposed_filenames_loop = array();
                $proposed_filenames = getProposedFilenames($footprint->getFilename(), $available_proposed_files);

                if ((count($proposed_filenames) > 0) && (pathinfo($proposed_filenames[0], PATHINFO_FILENAME) == pathinfo($footprint->getFilename(), PATHINFO_FILENAME))) {
                    $exact_match = true;
                } else {
                    $exact_match = false;
                }

                foreach ($proposed_filenames as $index => $filename) {
                    $filename = str_replace(BASE.'/', '', $filename);
                    $proposed_filenames_loop[] = array( 'selected' => ($index == 0) && $exact_match,
                        'proposed_filename' => $filename);
                }

                $broken_filename_loop[] = array(    'index'                     => $i,
                    'checked'                   => $exact_match,
                    'broken_id'                 => $footprint->getID(),
                    'broken_full_path'          => $footprint->getFullPath(),
                    'broken_filename'           => str_replace(BASE.'/', '', $footprint->getFilename()),
                    'proposed_filenames_count'  => count($proposed_filenames_loop),
                    'proposed_filenames'        => $proposed_filenames_loop);
            }

            $html->setVariable('broken_filename_footprints', $broken_filename_loop);
        }

        $html->setVariable('broken_footprints_count', count($broken_filename_loop), 'integer');
        $html->setVariable('broken_footprints_count_total', count($broken_filename_footprints), 'integer');
    } catch (Exception $e) {
        $messages[] = array('text' => 'Es konnten nicht alle Footprints mit defektem Dateinamen aufgelistet werden!',
            'strong' => true, 'color' => 'red');
        $messages[] = array('text' => 'Fehlermeldung: '.nl2br($e->getMessage()), 'color' => 'red');
    }
}

/********************************************************************************
 *
 *   List broken 3d filename footprints
 *
 *********************************************************************************/

if (! $fatal_error) {
    try {
        $broken_filename_footprints = Footprint::getBroken3dFilenameFootprints($database, $current_user, $log);
        $broken_filename_loop = array();

        if (count($broken_filename_footprints) > 0) {
            // get all available files for the proposed footprint images
            $available_proposed_files = array_merge(findAllFiles(BASE.'/models/', true));

            // read the PHP constant "max_input_vars"
            $max_input_vars = ((ini_get('max_input_vars') !== false) ? (int)ini_get('max_input_vars') : 999999);

            foreach ($broken_filename_footprints as $i => $iValue) {
                // avoid too many post variables
                if ($i*10 >= $max_input_vars) {
                    break;
                }

                // avoid too long execution time and a huge HTML table
                if ($i >= 100) {
                    break;
                }

                $footprint = $iValue;
                $proposed_filenames_loop = array();
                $proposed_filenames = getProposedFilenames($footprint->get3dFilename(), $available_proposed_files);

                if ((count($proposed_filenames) > 0) && (pathinfo($proposed_filenames[0], PATHINFO_FILENAME) == pathinfo($footprint->get3dFilename(), PATHINFO_FILENAME))) {
                    $exact_match = true;
                } else {
                    $exact_match = false;
                }

                foreach ($proposed_filenames as $index => $filename) {
                    $filename = str_replace(BASE.'/', '', $filename);
                    $proposed_filenames_loop[] = array( 'selected' => ($index == 0) && $exact_match,
                        'proposed_filename' => $filename);
                }

                $broken_filename_loop[] = array(    'index'                     => $i,
                    'checked'                   => $exact_match,
                    'broken_id'                 => $footprint->getID(),
                    'broken_full_path'          => $footprint->getFullPath(),
                    'broken_filename'           => str_replace(BASE.'/', '', $footprint->get3dFilename()),
                    'proposed_filenames_count'  => count($proposed_filenames_loop),
                    'proposed_filenames'        => $proposed_filenames_loop);
            }

            $html->setVariable('broken_3d_filename_footprints', $broken_filename_loop);
        }

        $html->setVariable('broken_3d_footprints_count', count($broken_filename_loop), 'integer');
        $html->setVariable('broken_3d_footprints_count_total', count($broken_filename_footprints), 'integer');
    } catch (Exception $e) {
        $messages[] = array('text' => 'Es konnten nicht alle Footprints mit defektem Dateinamen aufgelistet werden!',
            'strong' => true, 'color' => 'red');
        $messages[] = array('text' => 'Fehlermeldung: '.nl2br($e->getMessage()), 'color' => 'red');
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
        if (is_object($selected_footprint)) {
            $parent_id = $selected_footprint->getParentID();
            $html->setVariable('id', $selected_footprint->getID(), 'integer');
            $name = $selected_footprint->getName();
            $filename = $selected_footprint->getFilename();
            $filename_3d = $selected_footprint->get3dFilename();
            $comment = $selected_footprint->getComment(false);
            $html->setVariable('datetime_added', $selected_footprint->getDatetimeAdded(true));
            $html->setVariable('last_modified', $selected_footprint->getLastModified(true));
            $last_modified_user = $selected_footprint->getLastModifiedUser();
            $creation_user = $selected_footprint->getCreationUser();
            if ($last_modified_user != null) {
                $html->setVariable('last_modified_user', $last_modified_user->getFullName(true), 'string');
                $html->setVariable('last_modified_user_id', $last_modified_user->getID(), 'int');
            }
            if ($creation_user != null) {
                $html->setVariable('creation_user', $creation_user->getFullName(true), 'string');
                $html->setVariable('creation_user_id', $creation_user->getID(), 'int');
            }
        } elseif ($action == 'add') {
            $parent_id = $new_parent_id;
            $name = $new_name;
            $filename = $new_filename;
            $filename_3d = $new_3d_filename;
            $comment = $new_comment;
        } else {
            $parent_id = 0;
            $name = '';
            $filename = '';
            $filename_3d = '';
            $comment = '';
        }

        $html->setVariable('name', $name, 'string');
        $html->setVariable('filename', str_replace(BASE.'/', '', $filename), 'string');
        $html->setVariable('comment', $comment, 'string');

        $html->setVariable('filename_3d', str_replace(BASE.'/', '', $filename_3d), 'string');
        $html->setVariable('foot3d_active', $config['foot3d']['active'], 'boolean');

        //Say if file is valid (needed for preview in footprints)
        if (is_object($selected_footprint)) {
            $html->setVariable('filename_3d_valid', $selected_footprint->is3dFilenameValid(), 'boolean');
            $html->setVariable('filename_valid', $selected_footprint->isFilenameValid(), 'boolean');
        }

        $footprint_list = $root_footprint->buildHtmlTree($selected_id, true, false);
        $html->setVariable('footprint_list', $footprint_list, 'string');

        $parent_footprint_list = $root_footprint->buildHtmlTree($parent_id, true, true);
        $html->setVariable('parent_footprint_list', $parent_footprint_list, 'string');
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        $fatal_error = true;
    }
}

try {
    $html->setVariable('can_delete', $current_user->canDo(PermissionManager::FOOTRPINTS, StructuralPermission::DELETE));
    $html->setVariable('can_edit', $current_user->canDo(PermissionManager::FOOTRPINTS, StructuralPermission::EDIT));
    $html->setVariable('can_create', $current_user->canDo(PermissionManager::FOOTRPINTS, StructuralPermission::CREATE));
    $html->setVariable('can_move', $current_user->canDo(PermissionManager::FOOTRPINTS, StructuralPermission::MOVE));
    $html->setVariable('can_read', $current_user->canDo(PermissionManager::FOOTRPINTS, StructuralPermission::READ));
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
if (isset($_REQUEST['ajax'])) {
    $html->setVariable('ajax_request', true);
}

$reload_link = $fatal_error ? 'edit_footprints.php' : '';    // an empty string means that the...
$html->printHeader($messages, $reload_link);                // ...reload-button won't be visible

if (! $fatal_error) {
    $html->printTemplate('edit_footprints');
}

$html->printFooter();
