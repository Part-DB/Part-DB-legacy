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

    $Id: config_system.php 511 2012-08-05 weinbauer73@gmail.com $
*/

require_once ('lib.php');
include_once ('db_update.php');

$action = ( isset( $_REQUEST["action"]) ? $_REQUEST["action"] : 'default');

$html = new HTML;
$html -> set_html_meta ( array('title'=>$title,'menu'=>true) );
$html -> print_html_header();

$charsets = array();
foreach (array('iso8859-1','iso8859-15','utf-8') as $charset) $charsets[] = array('charsets'=>$charset,'checked'=>(($http_charset==$charset)?1:0));

$array = array(
	'version'		=>	'SVN '.get_svn_revision(),
	'head_charset'		=>	$charsets,
	'head_css'		=>	$css,
	'currency'		=>	$currency,
	'db_server'		=>	$db['mysql_server'],
	'db_database'		=>	$db['database'],
	'db_version_1'		=>	getDBVersion(),
	'db_version_2'		=>	(getDBVersion()!==getSollDBVersion()),
	'datasheet_path'	=>	str_replace(BASE.'/','./',$datasheet_path),
	'use_datasheet_path'	=>	(($use_datasheet_path===true)?1:0),
	'disable_update_list'	=>	(($disable_update_list===true)?1:0),
	'disable_devices'	=>	(($disable_devices===true)?1:0),
	'hide_id'		=>	(($hide_id===true)?1:0),
	'disable_help'		=>	(($disable_help===true)?1:0),
	'use_modal_dialog'	=>	$use_modal_dialog,
	'dialog_width'		=>	$dialog_width,
	'dialog_height'		=>	$dialog_height
);

$html -> parse_html_template( 'config_system', $array );

$html -> print_html_footer();

?>
