<?php

/*
part-db version 0.4
Copyright (C) 2016 Jan B�hmer

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
use PartDB\Label\BaseLabel;
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
$element_id            = isset($_REQUEST['id'])                 ? (integer)$_REQUEST['id']             : 0;
$generator_type        = isset($_REQUEST['generator'])          ? (string)$_REQUEST['generator']       : "part";
$label_size            = isset($_REQUEST['size'])               ? (string)$_REQUEST['size']            : "";
$label_preset          = isset($_REQUEST['preset'])             ? (string)$_REQUEST['preset']          : "";

$action = 'default';
if (isset($_REQUEST["label_generate"])) {
    $action = 'generate';
}
if (isset($_REQUEST["download"])) {
    $action = 'download';
}
if (isset($_REQUEST["view"])) {
    $action = "view";
}


/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $user_config['theme'], _('Labels'));

try {

    $database           = new Database();
    $log                = new Log($database);
    $current_user       = User::getLoggedInUser($database, $log);

    switch ($generator_type) {
        case "part":
            /* @var BaseLabel */
            $generator_class = "\PartDB\Label\PartLabel";
            if ($element_id > 0) {
                $element = new Part($database, $current_user, $log, $element_id);
            }
    }

} catch (Exception $e) {
    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
    $fatal_error = true;
}


/********************************************************************************
 *
 *   Execute Actions
 *
 *********************************************************************************/

//try {
    switch ($action) {
        case "generate":
                $html->setVariable("preview_src","show_part_label.php?" . $_SERVER["QUERY_STRING"] . "&view", "string");
                $html->setVariable("download_link", "show_part_label.php?" . $_SERVER["QUERY_STRING"] . "&download", "string");
            break;
        case "view":
            /* @var BaseLabel $generator */
            if (isset($element)) {
                $generator = new $generator_class($element, BaseLabel::TYPE_BARCODE, $label_size, $label_preset);

                $generator->generate();
            }
            break;
        case "download":
            /* @var BaseLabel $generator */
            if (isset($element)) {
                $generator = new $generator_class($element, BaseLabel::TYPE_BARCODE, $label_size, $label_preset);

                $generator->download();
            }
            break;
    }
/*} catch (Exception $e) {
    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
    $fatal_error = true;
}*/

/********************************************************************************
 *
 *   Set the rest of the HTML variables
 *
 *********************************************************************************/

if (! $fatal_error) {
    try {
        $html->setVariable("id", $element_id, "integer");
        $html->setVariable("selected_size", $label_size, "string");
        $html->setVariable("selected_preset", $label_preset, "string");

        //Show which label sizes are supported.
        $html->setLoop("supported_sizes", $generator_class::getSupportedSizes());
        $html->setLoop("available_presets", $generator_class::getLinePresets());

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


$reload_link = $fatal_error ? 'show_part_label.php?pid='.$part_id : '';  // an empty string means that the...
$html->printHeader($messages, $reload_link);                           // ...reload-button won't be visible




if (! $fatal_error) {
    $html->printTemplate('show_part_label');
}


$html->printFooter();
