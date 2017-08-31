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

use PartDB\HTML;

$errors = array();

if (isset($_REQUEST["add"])) {
    try {
        debug($_REQUEST['new_type'], $_REQUEST['new_text'], __FILE__, __LINE__, __METHOD__, false);
    } catch (Exception $exception) {
        $errors[] = $exception->getMessage();
    }
} elseif (isset($_REQUEST["clear"])) {
    try {
        createDebugLogFile(); // override the existing debug log with a new, empty debug log
    } catch (Exception $exception) {
        $errors[] = $exception->getMessage();
    }
} elseif (isset($_REQUEST["download"])) {
    if (is_readable(DEBUG_LOG_FILENAME)) {
        sendFile(DEBUG_LOG_FILENAME);
        // TODO: how can we re-activate the autorefresh now?!
    } else {
        $errors[] = _('Die Log-Datei kann nicht gelesen werden!');
    }
} elseif (isset($_REQUEST["enable"])) {
    try {
        setDebugEnable(true, $_REQUEST['admin_password']);
        header('Location: system_debug.php');
    } catch (Exception $exception) {
        $errors[] = $exception->getMessage();
    }
} elseif (isset($_REQUEST["disable"]) || isset($_REQUEST["disable_and_delete"])) {
    try {
        setDebugEnable(false);

        if (isset($_REQUEST["disable_and_delete"])) {
            deleteDebugLogFile();
        }

        header('Location: system_debug.php');
    } catch (Exception $exception) {
        $errors[] = $exception->getMessage();
    }
}

if ($config['debug']['enable'] && (count($errors) == 0) && (! isset($_REQUEST['stop_autorefresh']))
    && ((! isset($_REQUEST['autorefresh_disabled'])) || (isset($_REQUEST['start_autorefresh'])))) {
    $autorefresh = true;
} else {
    $autorefresh = false;
}


//Fill template with values
$html = new HTML($config['html']['theme'], $config['html']['custom_css'], "Debugging");

$html->setVariable("debug_enable", $config['debug']['enable'], "boolean");
$html->setVariable("autorefresh", $autorefresh, "boolean");
//$html->set_variable("errors_count", count($errors), "integer");
$html->setLoop("errors", $errors);
$html->setLoop("logs", getDebugLogElements());

// Print template
$html->printHeader();
$html->printTemplate("system_debug");
$html->printFooter();
