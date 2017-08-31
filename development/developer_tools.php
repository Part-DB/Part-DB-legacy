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

include_once('../start_session.php');


use PartDB\HTML;

$messages = array();
$fatal_error = false; // if a fatal error occurs, only the $messages will be printed, but not the site content

/********************************************************************************
 *
 *   Some special functions for this site
 *
 *********************************************************************************/

function exec_output_to_tmpl_loop($exec_output, $trim)
{
    $output_loop = array();
    for ($i=0; $i<count($exec_output); $i++) {
        if (($i <= 20) || ($i > count($exec_output) - 20) || ( ! $trim)) {
            $output_loop[] = array('text' => $exec_output[$i]);
            if (($i == 20) && ($trim)) {
                $output_loop[] = array('text' => '');
                $output_loop[] = array('text' => '[...]');
                $output_loop[] = array('text' => '');
            }
        }
    }

    return $output_loop;
}

function build_doxygen($trim, &$output_loop)
{
    $output = array();
    $output[] = 'Befehl: ./tools.sh -d';
    $output[] = '';
    exec('./tools.sh -d 2>&1', $output, $return);
    $output[] = '';
    $output[] = 'Returncode: '.$return;

    $output_loop = exec_output_to_tmpl_loop($output, $trim);

    return ($return == 0) ? true : false;
}

function tab2spaces($trim, &$output_loop)
{
    $output = array();
    $output[] = 'Befehl: ./tools.sh -r';
    $output[] = '';
    exec('./tools.sh -r 2>&1', $output, $return);
    $output[] = '';
    $output[] = 'Returncode: '.$return;
    $output[] = '';

    $output2 = array();
    $output2[] = 'Befehl: ./tools.sh -t';
    $output2[] = '';
    exec('./tools.sh -t 2>&1', $output2, $return2);
    $output2[] = '';
    $output2[] = 'Returncode: '.$return2;

    $output_loop = exec_output_to_tmpl_loop(array_merge($output, $output2), $trim);

    return (($return == 0) && ($return2 == 0)) ? true : false;
}

function build_release_package($trim, &$output_loop)
{
    $output = array();
    $output[] = 'Befehl: ./tools.sh -p';
    $output[] = '';
    exec('./tools.sh -p 2>&1', $output, $return);
    $output[] = '';
    $output[] = 'Returncode: '.$return;

    $output_loop = exec_output_to_tmpl_loop($output, $trim);

    return ($return == 0) ? true : false;
}

/********************************************************************************
 *
 *   Evaluate $_REQUEST
 *
 *********************************************************************************/

$trim_exec_output = isset($_REQUEST["trim_exec_output"]);

$action = 'default';
if (isset($_REQUEST["build_doxygen"])) {
    $action = 'build_doxygen';
}
if (isset($_REQUEST["tab2spaces"])) {
    $action = 'tab2spaces';
}
if (isset($_REQUEST["build_release_package"]))  {
    $action = 'build_release_package';
}
if (isset($_REQUEST["delete_release_package"])) {
    $action = 'delete_release_package';
}

/********************************************************************************
 *
 *   Initialize Objects
 *
 *********************************************************************************/

$html = new HTML($config['html']['theme'], $config['html']['custom_css'], 'Entwicklerwerkzeuge');

try
{
    //$database           = new Database();
    //$log                = new Log($database);
    //$system             = new System($database, $log);
    //$current_user       = new User($database, $current_user, $log, 1); // admin
}
catch (Exception $e) {
    $messages[] = array('text' => nl2br($e->getMessage()), 'strong' => true, 'color' => 'red');
    $fatal_error = true;
}

/********************************************************************************
 *
 *   Execute actions
 *
 *********************************************************************************/

$release_package_filename = BASE.'/development/package_output/Part-DB_'.$config['system']['version'].'.tar.gz';

if ( ! $fatal_error) {
    switch ($action) {
        case 'build_doxygen':
            $doxygen_successful = build_doxygen($trim_exec_output, $doxygen_output_loop);
            break;

        case 'tab2spaces':
            $remove_spaces_successful = tab2spaces($trim_exec_output, $tab2spaces_output_loop);
            break;

        case 'build_release_package':
            $release_packing_successful = build_release_package($trim_exec_output, $release_packing_output_loop);
            break;

        case 'delete_release_package':
            unlink($release_package_filename);
            break;
    }
}

/********************************************************************************
 *
 *   Set all HTML variables
 *
 *********************************************************************************/

$html->setVariable('current_system_version', $config['system']['version'], 'string');

if (file_exists($release_package_filename)) {
    $html->setVariable('release_archive_link', str_replace(BASE, BASE_RELATIVE, $release_package_filename), 'string');
    $html->setVariable('release_archive_basename', basename($release_package_filename), 'string');
}

$html->setVariable('packing_checklist_link', BASE_RELATIVE.'/development/package_output/readme.txt', 'string');

if (isset($doxygen_output_loop)) {
    //$html->set_variable('exec_successful', $doxygen_successful, 'boolean');
    $html->setLoop('exec_output', $doxygen_output_loop);
}

if (isset($tab2spaces_output_loop)) {
    //$html->set_variable('exec_successful', $tab2spaces_successful, 'boolean');
    $html->setLoop('exec_output', $tab2spaces_output_loop);
}

if (isset($release_packing_output_loop)) {
    //$html->set_variable('exec_successful', $release_packing_successful, 'boolean');
    $html->setLoop('exec_output', $release_packing_output_loop);
}

/********************************************************************************
 *
 *   Generate HTML Output
 *
 *********************************************************************************/

$html->printHeader($messages);

if ( ! $fatal_error)
{
    $html->printTemplate('developer_tools');
}


$html->printFooter();
