<?php
/*
	$Id: config_page.php 479 2012-07-03 17:43:42Z kami89@gmx.ch $
*/

require_once ('lib.php');
include_once ('db_update.php');

function list_backup_files( $path)
{

	/* searching for SQL-files in $path, sorted in ascending order */
	foreach ( glob( $path."*.sql" ) as $file ) $options[]['db_backup_filename'] = smart_unescape(basename($file));
	rsort($options);
	return $options;

}

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
	unlink ($backup_path.$_REQUEST["selected_backup_file"]);
}

if ( $_REQUEST['download_file'] && $_REQUEST["selected_backup_file"] != "" )
{
	// send backupfile to user
	$mtime = ($mtime = filemtime($backup_path.$_REQUEST["selected_backup_file"])) ? $mtime : gmtime();
	if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE") != false)
	{
		header("Content-Disposition: attachment; filename=".urlencode($_REQUEST["selected_backup_file"])."; modification-date=".date('r', $mtime).";");
	}
	else
	{
		header("Content-Disposition: attachment; filename=\"".$_REQUEST["selected_backup_file"]."\"; modification-date=\"".date('r', $mtime)."\";");
	}
	header("Content-Type: text/x-sql");
	header("Content-Length:". filesize($backup_path.$_REQUEST["selected_backup_file"]));

	if (in_array('mod_xsendfile',apache_get_modules()) )
	{
		header('X-Sendfile: '.$backup_path.$_REQUEST["selected_backup_file"]);
	}
	else
	{
		readfile($backup_path.$_REQUEST["selected_backup_file"]);
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
$html -> set_html_meta ( array('title'=>$title,'http_charset'=>$http_charset,'theme'=>$theme,'css'=>$css,'menu'=>true) );
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
