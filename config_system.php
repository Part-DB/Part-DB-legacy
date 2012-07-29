<?php
/*
    $Id: nav.php 377 2012-02-27 23:21:10Z bubbles.red@gmail.com $
*/

/** edit: 20120711 Udo Neist **/

require_once ('lib.php');
include_once ('db_update.php');

/** end: 20120711 Udo Neist **/

$action = ( isset( $_REQUEST["action"]) ? $_REQUEST["action"] : 'default');

/** edit: 20120729 Udo Neist **/

$html = new HTML;
$html -> set_html_meta ( array('title'=>$title,'http_charset'=>$http_charset,'theme'=>$theme,'css'=>$css,'menu'=>true) );
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

/** end: 20120729 Udo Neist **/

?>
