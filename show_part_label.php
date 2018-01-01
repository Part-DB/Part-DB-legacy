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

// We save every setting in this array, so we can serialize and deserialize it later easily.
$profile = array();

$element_id            = isset($_REQUEST['id'])                 ? (integer)$_REQUEST['id']             : 0;
$profile['generator_type']        = isset($_REQUEST['generator'])          ? (string)$_REQUEST['generator']       : "part";
$profile['label_size']            = isset($_REQUEST['size'])               ? (string)$_REQUEST['size']            : "";
$profile['label_preset']          = isset($_REQUEST['preset'])             ? (string)$_REQUEST['preset']          : "";
$profile['label_type']            = isset($_REQUEST['type'])               ? (integer)$_REQUEST['type']           : 2;


//Advanced settings
$profile['text_bold']             = isset($_REQUEST['text_bold']);
$profile['text_italic']           = isset($_REQUEST['text_italic']);
$profile['text_underline']        = isset($_REQUEST['text_underline']);
$profile['text_size']             = isset($_REQUEST['text_size']) ? (int) $_REQUEST['text_size']                  : 8;

$profile['output_mode']           = isset($_REQUEST['radio_output']) ? (string)$_REQUEST['radio_output'] : "html";
$profile['barcode_alignment']     = isset($_REQUEST['barcode_alignment']) ? (string)$_REQUEST['barcode_alignment'] : "center";
$profile['custom_rows']           = isset($_REQUEST['custom_rows']) ? (string)$_REQUEST['custom_rows'] : "";



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

    switch ($profile['generator_type']) {
        case "part":
            /* @var $generator_class BaseLabel */
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

try {
    //Build config array
    $options = array();

    $options['text_bold'] = $profile['text_bold'];
    $options['text_italic'] = $profile['text_italic'];
    $options['text_underline'] = $profile['text_underline'];
    $options['text_size'] = $profile['text_size'];

    if ($profile['output_mode'] == "text") {
        $options['force_text_output'] = true;
    }
    $options['barcode_alignment'] = $profile['barcode_alignment'];
    $options['custom_rows'] = $profile['custom_rows'];

    //If selected preset is not "custom", than show the preset lines in custom_rows
    if ($profile['label_preset'] != "custom") {
        foreach ($generator_class::getLinePresets() as $preset) {
            if ($preset["name"] == $preset['label_preset']) {
                $custom_rows = implode("\n", $preset["lines"]);
            }
        }
    }


    switch ($action) {
        case "generate":
            $html->setVariable("preview_src", "show_part_label.php?" . http_build_query($_REQUEST) . "&view", "string");
            $html->setVariable("download_link", "show_part_label.php?" . http_build_query($_REQUEST) . "&download", "string");
            break;
        case "view":
            /* @var BaseLabel $generator */
            if (isset($element)) {
                $generator = new $generator_class($element, $profile['label_type'], $profile['label_size'], $profile['label_preset'], $options);

                $generator->generate();
            }
            break;
        case "download":
            /* @var BaseLabel $generator */
            if (isset($element)) {
                $generator = new $generator_class($element, $profile['label_type'], $profile['label_size'], $profile['label_preset'], $options);

                $generator->download();
            }
            break;
    }
} catch (Exception $e) {
    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
    $fatal_error = true;
}

/********************************************************************************
 *
 *   Set the rest of the HTML variables
 *
 *********************************************************************************/

if (! $fatal_error) {
    try {
        $html->setVariable("id", $element_id, "integer");
        $html->setVariable("selected_size", $profile['label_size'], "string");
        $html->setVariable("selected_preset", $profile['label_preset'], "string");
        $html->setVariable('type', $profile['label_type'], "integer");

        //Show which label sizes are supported.
        $html->setLoop("supported_sizes", $generator_class::getSupportedSizes());
        $html->setLoop("supported_types", $generator_class::getSupportedTypes());
        $html->setLoop("available_presets", $generator_class::getLinePresets());

        //Advanced settings
        $html->setVariable("text_bold", $profile['text_bold'], "bool");
        $html->setVariable("text_italic", $profile['text_italic'], "bool");
        $html->setVariable("text_underline", $profile['text_underline'], "bool");
        $html->setVariable("text_size", $profile['text_size'], "int");
        $html->setVariable("radio_output", $profile['output_mode'], "string");
        $html->setVariable('barcode_alignment', $profile['barcode_alignment'], "string");
        $html->setVariable('custom_rows', $profile['custom_rows'], "string");

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
