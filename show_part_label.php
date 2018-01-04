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
use PartDB\Permissions\LabelPermission;
use PartDB\Permissions\PermissionManager;
use PartDB\Tools\JSONStorage;
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

$element_id                       = isset($_REQUEST['id'])                 ? (integer)$_REQUEST['id']             : 0;
$generator_type                   = isset($_REQUEST['generator'])          ? (string)$_REQUEST['generator']       : "part";
$profile_name                     = !empty($_REQUEST['profile'])            ? (string)$_REQUEST['profile']         : "default";
$selected_profile                 = isset($_REQUEST['selected_profile'])   ? (string)$_REQUEST['selected_profile'] : "";

//Create JSON storage object, so we can load the profile the user has selected.
$json_storage = new JSONStorage(BASE_DATA . "/label_profiles.json");

//If Json storage default profile is empty, then create one.
if (!$json_storage->itemExists($generator_type . "@" . $profile_name)) {
    $profile = array("label_size" => "",
        "label_preset" => "",
        "label_type" => 2,
        "text_bold" => false,
        "text_italic" => false,
        "text_underline" => false,
        "text_size" => 8,
        "output_mode" => "html",
        "barcode_alignment" => "center",
        "custom_rows" => "",
        "custom_height" => "",
        "custom_width" => "",
        "text_alignment" => "left",
        "logo_path" => "",
        "use_footprint_image" => false);

    /*if ($profile_name == "default") {
        $json_storage->addItem($generator_type . "@default", $profile);
    }*/
} else {
    $profile = $json_storage->getItem($generator_type . "@" . $profile_name);
}

$profile['label_size']            = isset($_REQUEST['size'])               ? (string)$_REQUEST['size']            : $profile['label_size'];
$profile['label_preset']          = isset($_REQUEST['preset'])             ? (string)$_REQUEST['preset']          : $profile['label_preset'];
$profile['label_type']            = isset($_REQUEST['type'])               ? (integer)$_REQUEST['type']           : $profile['label_type'] ;


//Advanced settings
$profile['text_bold']             = isset($_REQUEST['text_bold']) ? true :  $profile['text_bold'] ;
$profile['text_italic']           = isset($_REQUEST['text_italic']) ? true : $profile['text_italic'];
$profile['text_underline']        = isset($_REQUEST['text_underline']) ? true : $profile['text_underline'];
$profile['text_size']             = isset($_REQUEST['text_size']) ? (int) $_REQUEST['text_size']                  : $profile['text_size']  ;
$profile['text_alignment']        = isset($_REQUEST['text_alignment']) ? (string)$_REQUEST['text_alignment'] : $profile['text_alignment'];
$profile['logo_path']             = isset($_REQUEST['logo_path']) ? (string)$_REQUEST['logo_path'] : $profile['logo_path'];

$profile['custom_width']          = isset($_REQUEST['custom_width']) ? (string) $_REQUEST['custom_width']         : $profile['custom_width']  ;
$profile['custom_height']          = isset($_REQUEST['custom_height']) ? (string) $_REQUEST['custom_height']         : $profile['custom_height']  ;

$profile['output_mode']           = isset($_REQUEST['radio_output']) ? (string)$_REQUEST['radio_output'] : $profile['output_mode'];
$profile['barcode_alignment']     = isset($_REQUEST['barcode_alignment']) ? (string)$_REQUEST['barcode_alignment'] : $profile['barcode_alignment'];
$profile['custom_rows']           = isset($_REQUEST['custom_rows']) ? (string)$_REQUEST['custom_rows'] : $profile['custom_rows'];
$profile['use_footprint_image']  = isset($_REQUEST['use_footprint_image']) ? true :  $profile['use_footprint_image'] ;



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
if (isset($_REQUEST['save_profile'])) {
    $action = 'save_profile';
}
if (isset($_REQUEST['load_profile'])) {
    $action = "load_profile";
}
if (isset($_REQUEST['delete_profile'])) {
    $action = "delete_profile";
}
if (isset($_REQUEST['delete_confirmed'])) {
    $action = "delete_confirmed";
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

    $current_user->tryDo(PermissionManager::LABELS, LabelPermission::CREATE_LABELS);

    switch ($generator_type) {
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
if (!$fatal_error) {
    try {
        //Check if a file was uploaded, then move it to correct place and use it as logo.
        $filepath = BASE . "/data/media/labels/";

        if (isset($_FILES['logo_file']) && strlen($_FILES['logo_file']['name']) > 0) {
            $uploaded_file = uploadFile($_FILES['logo_file'], $filepath);
            $profile['logo_path'] =  str_replace(BASE . "/", "", $uploaded_file);
        }

        //Build config array
        $options = array();

        $options['text_bold'] = $profile['text_bold'];
        $options['text_italic'] = $profile['text_italic'];
        $options['text_underline'] = $profile['text_underline'];
        $options['text_size'] = $profile['text_size'];
        $options['custom_width'] = $profile['custom_width'];
        $options['custom_height'] = $profile['custom_height'];
        $options['text_alignment'] = $profile['text_alignment'];
        $options['logo_path'] = $profile['logo_path'];

        //Override $options['logo_path'] if use_footprint_image option is set
        if ($profile['use_footprint_image'] && $generator_type == "part") {
            $options['logo_path'] = $element->getFootprint()->getFilename(false);
        }

        if ($profile['output_mode'] == "text") {
            $options['force_text_output'] = true;
        }
        $options['barcode_alignment'] = $profile['barcode_alignment'];
        $options['custom_rows'] = $profile['custom_rows'];

        //If selected preset is not "custom", than show the preset lines in custom_rows
        if ($profile['label_preset'] != "custom") {
            foreach ($generator_class::getLinePresets() as $preset) {
                if ($preset["name"] == $profile['label_preset']) {
                    $profile['custom_rows'] = implode("\n", $preset["lines"]);
                }
            }
        }

        //Show size preset in custom size inputs
        if ($profile['label_size'] != "custom") {
            $exploded = explode("x", $profile['label_size']);
            $profile['custom_width'] = $exploded[0];
            $profile['custom_height'] = $exploded[1];
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
            case "save_profile":
                $current_user->tryDo(PermissionManager::LABELS, LabelPermission::EDIT_PROFILES);
                $new_name = $_REQUEST['save_name'];
                if ($new_name == "") {
                    throw new Exception(_("Der Profilname darf nicht leer sein!"));
                }
                $json_storage->editItem($generator_type . "@" . $new_name, $profile, true, true);
                $messages[] = array("text" => _("Das Profil wurde erfolgreich gespeichert!"), "strong" => true, "color" => "green");
                break;
            case "load_profile":
                if ($selected_profile == "") {
                    throw new Exception(_("Sie müssen ein Profil zum Laden auswählen!"));
                }
                $new_request = $_GET;
                $new_request['profile'] = $selected_profile;
                $html->redirect("show_part_label.php?" . http_build_query($new_request));
                break;
            case "delete_profile":
                $current_user->tryDo(PermissionManager::LABELS, LabelPermission::DELETE_PROFILES);
                if ($selected_profile == "") {
                    throw new Exception(_("Sie müssen ein Profil zum Löschen auswählen!"));
                }

                $messages[] = array('text' => sprintf(_('Soll das Profil "%s' .
                    '" wirklich unwiederruflich gelöscht werden?'), $selected_profile), 'strong' => true, 'color' => 'red');
                $messages[] = array('html' => '<input type="hidden" name="generator" value="' . $generator_type . '">');
                $messages[] = array('html' => '<input type="hidden" name="selected_profile" value="' . $selected_profile . '">');
                $messages[] = array('html' => '<input type="submit" class="btn btn-default" name="" value="' . _('Nein, nicht löschen') . '">', 'no_linebreak' => true);
                $messages[] = array('html' => '<input type="submit" class="btn btn-danger" name="delete_confirmed" value="' . _('Ja, Profil löschen') . '">');
                break;
            case "delete_confirmed":
                $current_user->tryDo(PermissionManager::LABELS, LabelPermission::DELETE_PROFILES);
                if ($selected_profile == "") {
                    throw new Exception(_("Sie müssen ein Profil zum Löschen auswählen!"));
                }
                $messages[] = array("text" => _("Das Profil wurde erfolgreich gelöscht!"), "strong" => true, "color" => "green");
                $json_storage->deleteItem($generator_type . "@" . $selected_profile);
        }
    } catch (Exception $e) {
        $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
        //$fatal_error = true;
    }
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
        $html->setVariable("text_alignment", $profile['text_alignment'], "string");
        $html->setVariable('custom_rows', $profile['custom_rows'], "string");

        $html->setVariable("custom_width", $profile['custom_width'], "int");
        $html->setVariable("custom_height", $profile['custom_height'], "int");
        $html->setVariable("logo_path", $profile['logo_path'], "string");
        $html->setVariable('use_footprint_image', $profile['use_footprint_image'], "bool");

        //Profile tabs
        $html->setVariable("save_name", $profile_name != "default" ? $profile_name : "", "string");
        $html->setVariable("selected_profile", $profile_name, "string");
        $html->setLoop("profiles", buildLabelProfilesDropdown($generator_type, true));

        //Permission variables
        $html->setVariable("can_save_profile", $current_user->canDo(PermissionManager::LABELS, LabelPermission::EDIT_PROFILES));
        $html->setVariable("can_edit_option", $current_user->canDo(PermissionManager::LABELS, LabelPermission::EDIT_OPTIONS));
        $html->setVariable("can_delete_profile", $current_user->canDo(PermissionManager::LABELS, LabelPermission::DELETE_PROFILES));

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
