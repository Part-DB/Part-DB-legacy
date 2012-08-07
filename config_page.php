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

    $Id: config_page.php 511 2012-08-04 weinbauer73@gmail.com $
*/

require_once ('lib.php');
include_once ('db_update.php');

function rGlob( $pattern = '*', $path = false )
{
	/* see also http://webpiraten.de/index.php/php/rekursive-php-glob-funktion-ordnerdateien-rekursiv-nach-pattern-filtern/ */

	if (!$path)
	{
		$path = dirname($pattern).DIRECTORY_SEPARATOR;
	}
 
	$pattern    = basename($pattern);
	$paths      = glob($path.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT);
	$files      = glob($path.$pattern);
	foreach ($paths as $path)
	{
		$files = array_merge($files, $path.DIRECTORY_SEPARATOR.rGlob($pattern, $path));
	}
	return $files;
}

function list_backup_files( $path )
{

	/* searching for SQL-files in $path, sorted in ascending order */
	$sqlfiles = rGlob("*.sql", $path);
	$files = array();
	foreach ( $sqlfiles as $file ) $files[]['db_backup_filename'] = smart_unescape(str_replace(__DIR__,'',$file));
	rsort($files);
	return $files;

}

$db['backup_path'] = str_replace('%Y%',date('Y'),$db['backup_path']); // Year
$db['backup_path'] = str_replace('%M%',date('m'),$db['backup_path']); // Month
$db['backup_path'] = str_replace('%W%',date('w'),$db['backup_path']); // Day of week
$db['backup_path'] = str_replace('%WY%',date('W'),$db['backup_path']); // Week of year
if ( !is_dir($db['backup_path']) ) mkdir($db['backup_path'],0775,true);
$backup_path = isset($db['backup_path']) ? $db['backup_path'] : "backup/";

if ( $_REQUEST['backup'] && is_writable( $backup_path ) )
{
	// backup mysql-database
	$backup_file = $db['database'].'_'. date("Y-m-d_H:i:s") . '.sql';
	$command = "mysqldump --opt -h ".$db['mysql_server']." -u".$db['user']." -p".$db['password']." ".$db['database']." > $backup_path$backup_file";
	exec($command);
}

if ( $_REQUEST['delete_file']  && $_REQUEST["selected_backup_file"] != "" )
{
	 // delete backupfile
	unlink (__DIR__.$_REQUEST["selected_backup_file"]);
}

if ( $_REQUEST['download_file'] && $_REQUEST["selected_backup_file"] != "" )
{
	// send backupfile to user
	$mtime = ($mtime = filemtime(__DIR__.$_REQUEST["selected_backup_file"])) ? $mtime : gmtime();
	if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE") != false)
	{
		header("Content-Disposition: attachment; filename=".urlencode(basename($_REQUEST["selected_backup_file"]))."; modification-date=".date('r', $mtime).";");
	}
	else
	{
		header("Content-Disposition: attachment; filename=\"".basename($_REQUEST["selected_backup_file"])."\"; modification-date=\"".date('r', $mtime)."\";");
	}
	header("Content-Type: text/x-sql");
	header("Content-Length:". filesize(__DIR__.$_REQUEST["selected_backup_file"]));

	if (in_array('mod_xsendfile',apache_get_modules()) )
	{
		header('X-Sendfile: '.__DIR__.$_REQUEST["selected_backup_file"]);
	}
	else
	{
		readfile(__DIR__.$_REQUEST["selected_backup_file"]);
	}
	exit;
}

$array = array();

if ( $_REQUEST['set_auto_update'] )
{
	// detect auto-update
	$array['db_version_autoupdate_set'] = setDBAutomaticUpdateActive($_REQUEST["active"]=='active');
}

$html = new HTML;
$html -> set_html_meta ( array('title'=>$title) );
$html -> print_html_header();

$array = array(
	'db_version_1'			=>	getDBVersion(),
	'db_version_2'			=>	getSollDBVersion(),
	'db_version_update'		=>	checkDBUpdateNeeded(),
	'db_version_update_log'		=>	(($_REQUEST['db_update'])?doDBUpdate():''),
	'db_version_autoupdate_check'	=>	((getDBAutomaticUpdateActive())?'checked':''),
	'db_list_backups'		=>	list_backup_files($backup_path)
);

$html -> parse_html_template( 'config_page', $array );

$html -> print_html_footer();

?>
