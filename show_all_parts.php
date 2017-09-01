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
$starttime = microtime(true); // this is to measure the time while debugging is active

/********************************************************************************
 *
 *   Evaluate $_REQUEST
 *
 *********************************************************************************/


//$manufacturer_id        = isset($_REQUEST['mid'])               ? (integer)$_REQUEST['mid']             : 0;
//$with_submanufacturers = isset($_REQUEST['subman'])            ? (boolean)$_REQUEST['subman']          : true;
$table_rowcount     = isset($_REQUEST['table_rowcount'])    ? (integer)$_REQUEST['table_rowcount']  : 0;

$action = 'default';
//if (isset($_REQUEST['subman_button']))      {$action = 'change_subman_state';}
$selected_part_id = 0;
for ($i=0; $i<$table_rowcount; $i++) {
    $selected_part_id = isset($_REQUEST['id_'.$i]) ? (integer)$_REQUEST['id_'.$i] : 0;

    if (isset($_REQUEST['decrement_'.$i])) {
        $action = 'decrement';
        break;
    }

    if (isset($_REQUEST['increment_'.$i])) {
        $action = 'increment';
        break;
    }
}

/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $config['html']['custom_css'], _('Teileansicht'));

try {
    $database           = new Database();
    $log                = new Log($database);
    $current_user       = new User($database, $current_user, $log, 1); // admin

    if ($selected_part_id > 0) {
        $part = new Part($database, $current_user, $log, $selected_part_id);
    } else {
        $part = null;
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
        case 'decrement': // remove one part
            try {
                if (! is_object($part)) {
                    throw new Exception('Es wurde keine gültige Bauteil-ID übermittelt!');
                }

                $part->setInstock($part->getInstock() - 1);

                $reload_site = true;
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;

        case 'increment': // add one part
            try {
                if (! is_object($part)) {
                    throw new Exception(_('Es wurde keine gültige Bauteil-ID übermittelt!'));
                }

                $part->setInstock($part->getInstock() + 1);

                $reload_site = true;
            } catch (Exception $e) {
                $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
            }
            break;
    }
}

if (isset($reload_site) && $reload_site && (! $config['debug']['request_debugging_enable'])) {
    // reload the site to avoid multiple actions by manual refreshing
    header('Location: show_all_parts.php');
}

/********************************************************************************
 *
 *   Generate Table
 *
 *********************************************************************************/

if (! $fatal_error) {
    try {
        $parts = Part::getAllParts($database, $current_user, $log);
        $table_loop = Part::buildTemplateTableArray($parts, 'all_parts');
        $html->setVariable('table_rowcount', count($parts), 'integer');
        $html->setLoop('table', $table_loop);
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        $fatal_error = true;
    }
}

$php_endtime = microtime(true); // For Debug informations

/********************************************************************************
 *
 *   Set the rest of the HTML variables
 *
 *********************************************************************************/


//$html->set_variable('with_submanufacturers', $with_submanufacturers, 'boolean');

if (! $fatal_error) {
    $html->setVariable('disable_footprints', ($config['footprints']['disable']), 'boolean');
    $html->setVariable('disable_manufacturers', ($config['manufacturers']['disable']), 'boolean');
    $html->setVariable('disable_auto_datasheets', ($config['auto_datasheets']['disable']), 'boolean');

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



$reload_link = $fatal_error ? 'show_all_parts.php'  : ''; // an empty string means that the...
$html->printHeader($messages, $reload_link);                                   // ...reload-button won't be visible


if (! $fatal_error) {
    $html->printTemplate('show_all_parts');
}

// If debugging is enabled, print some debug informations
$debug_messages = array();
if ((! $fatal_error) && ($config['debug']['enable'])) {
    $endtime = microtime(true);
    $lifetime = (integer)(1000*($endtime - $starttime));
    $php_lifetime = (integer)(1000*($php_endtime - $starttime));
    $html_lifetime = (integer)(1000*($endtime - $php_endtime));
    $debug_messages[] = array('text' => 'Debug-Meldungen: ', 'strong' => true, 'color' => 'darkblue');
    $debug_messages[] = array('text' => 'Anzahl Teile: '.(count($parts)), 'color' => 'darkblue');
    $debug_messages[] = array('text' => 'Gesamte Laufzeit: '.$lifetime.'ms', 'color' => 'darkblue');
    $debug_messages[] = array('text' => 'PHP Laufzeit: '.$php_lifetime.'ms', 'color' => 'darkblue');
    $debug_messages[] = array('text' => 'HTML Laufzeit: '.$html_lifetime.'ms', 'color' => 'darkblue');
}

$html->printFooter($debug_messages);
