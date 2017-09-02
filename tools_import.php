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
include_once(BASE.'/inc/lib.import.php');

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

// section "upload file"
$file_format    = isset($_REQUEST['file_format'])       ? (string)$_REQUEST['file_format']      : 'CSV';
$separator      = isset($_REQUEST['separator'])         ? (string)$_REQUEST['separator']        : ';';

// section "check data"
$table_rowcount = isset($_REQUEST['table_rowcount'])    ? (integer)$_REQUEST['table_rowcount']  : 0;
$file_content   = isset($_REQUEST['file_content'])      ? (string)$_REQUEST['file_content']     : '';

$new_part_ids   = isset($_REQUEST['new_part_ids'])      ? (string)$_REQUEST['new_part_ids']     : '';

$action = 'default';
if (isset($_REQUEST["show_imported_parts"])) {
    $action = 'show_imported_parts';
}
if (isset($_REQUEST["upload_file"])) {
    $action = 'upload_file';
}
if (isset($_REQUEST["check_data"])) {
    $action = 'check_data';
}
if (isset($_REQUEST["import_data"])) {
    $action = 'import_data';
}

/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $config['html']['custom_css'], _('Teile importieren'));

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
 *   Execute actions
 *
 *********************************************************************************/

if (! $fatal_error) {
    switch ($action) {
        case 'upload_file':
            try {
                if (! is_uploaded_file($_FILES['uploaded_file']['tmp_name'])) {
                    throw new Exception(_('Datei konnte nicht hochgeladen werden!'));
                }

                $file_content = file_get_contents($_FILES['uploaded_file']['tmp_name']);

                $import_data = importTextToArray($file_content, $file_format, $separator);
                $table_loop = buildPartsImportTemplateLoop($database, $current_user, $log, $import_data);
            } catch (Exception $e) {
                $messages[] = array('text' => _('Es gab ein Fehler!'), 'strong' => true, 'color' => 'red');
                $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
            }
            break;

        case 'check_data':
            try {
                $import_data = extractImportDataFromRequest($table_rowcount);
                $table_loop = buildPartsImportTemplateLoop($database, $current_user, $log, $import_data);
                importParts($database, $current_user, $log, $import_data, true);

                $html->setVariable('data_is_valid', true, 'boolean'); // now the "import" button will be visible
                $messages[] = array('text' => _('Die Daten sind gültig!'), 'strong' => true, 'color' => 'darkgreen');
            } catch (Exception $e) {
                $messages[] = array('text' => _('Die Daten sind nicht gültig!'), 'strong' => true, 'color' => 'red');
                $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
            }
            break;

        case 'import_data':
            try {
                $import_data = extractImportDataFromRequest($table_rowcount);
                $table_loop = buildPartsImportTemplateLoop($database, $current_user, $log, $import_data);
                $new_parts = importParts($database, $current_user, $log, $import_data, false);

                $html->setVariable('refresh_navigation_frame', true, 'boolean');
                $messages[] = array('text' => _('Die Daten wurden erfolgreich importiert!'), 'strong' => true, 'color' => 'darkgreen');
                unset($import_data);
                unset($table_loop);
                $file_content = '';

                $new_part_ids = '';
                foreach ($new_parts as $part) {
                    /** @var Part $part */
                    $new_part_ids .= $part->getID().';';
                }

                // reload the site to avoid multiple actions by manual refreshing
                header('Location: tools_import.php?show_imported_parts=1&new_part_ids='.$new_part_ids);
            } catch (Exception $e) {
                $messages[] = array('text' => _('Es gab ein Fehler beim Importieren!'), 'strong' => true, 'color' => 'red');
                $messages[] = array('text' => _('Fehlermeldung: ').nl2br($e->getMessage()), 'color' => 'red');
            }
            break;

        case 'show_imported_parts':
            try {
                $html->setVariable('refresh_navigation_frame', true, 'boolean');

                $ids = explode(';', $new_part_ids);
                $new_parts = array();
                foreach ($ids as $id) {
                    if ($id > 0) {
                        $new_parts[] = new Part($database, $current_user, $log, $id);
                    }
                }
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;
    }
}

/********************************************************************************
 *
 *   Set HTML Variables
 *
 *********************************************************************************/


if (! $fatal_error) {
    // global settings
    $html->setVariable('disable_footprints', $config['footprints']['disable'], 'boolean');
    $html->setVariable('disable_manufacturers', $config['manufacturers']['disable'], 'boolean');
    $html->setVariable('disable_auto_datasheets', $config['auto_datasheets']['disable'], 'boolean');

    $html->setVariable('use_modal_popup', $config['popup']['modal'], 'boolean');
    $html->setVariable('popup_width', $config['popup']['width'], 'integer');
    $html->setVariable('popup_height', $config['popup']['height'], 'integer');

    // import stuff
    $html->setVariable('file_format', $file_format, 'string');
    $html->setVariable('separator', $separator, 'string');

    $csv_file_example = file_get_contents(BASE.'/documentation/examples/import_parts/import_parts.csv');
    $html->setVariable('csv_file_example', $csv_file_example, 'string');

    $xml_file_example = file_get_contents(BASE.'/documentation/examples/import_parts/import_parts.xml');
    $html->setVariable('xml_file_example', $xml_file_example, 'string');

    $html->setVariable('file_content', $file_content, 'string');

    try {
        if (isset($new_parts)) {
            $new_parts_loop = Part::buildTemplateTableArray($new_parts, 'imported_parts');
        }
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
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

$html->printHeader($messages);

if (! $fatal_error) {
    $html->printTemplate('upload');

    if (isset($import_data)) {
        $html->setLoop('table', $table_loop);
        $html->setVariable('table_rowcount', count($import_data), 'integer');
        $html->printTemplate('check_data');
    }

    if (strlen($file_content) > 0) {
        $html->printTemplate('file_content');
    }

    if (isset($new_parts_loop)) {
        $html->setLoop('table', $new_parts_loop);
        $html->printTemplate('new_parts');
    }

    if ($action == 'default') {
        $html->printTemplate('file_examples');
    }
}

$html->printFooter();
