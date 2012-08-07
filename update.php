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

    $Id: config_update.php 511 2012-08-04 weinbauer73@gmail.com $
*/

require_once ('lib.php');
require_once ('pack.php');

function updateDB ( $update = '' )
{
	if ( $update == '' || ! is_readable(BASE.'/update/part-db_update_rev'.$update.'.sql') ) return false;

	$sqlcontent = explode(';',file_get_contents(BASE.'/update/part-db_update_rev'.$update.'.sql'));
	$error = array();
	for ( $i=0; $i<count($sqlcontent)-1; $i++ )
	{
		if(!mysql_query(trim($sqlcontent[$i]))) $error[] = array('sql'=>trim($sqlcontent[$i]),'error'=>mysql_error());
	}
	return ((count($error)==0)?true:$error);
}

function getDBUpdateFile ()
{

	$query = "SELECT keyName,keyValue FROM internal WHERE keyName in ('dbVersion','dbSubversion','dbRevision')";

	$rows = array();
	$result = mysql_query ($query);
	while ( $row = mysql_fetch_assoc($result) ) $rows[$row['keyName']] = $row['keyValue'];

	$version = $rows['dbVersion'].(($version['dbSubversion'])?'-'.$version['dbSubversion']:'').(($version['dbRevision'])?'-'.$version['dbRevision']:'');
	$file = 'part-db_update_rev'.$version.'.zip';

	// TODO: Download aus dem Netz

	echo "loading $file<br>";

	$packer = new pack();
	$packer -> unzip (BASE.'/update/'.$file,BASE.'/update');

	if ( is_readable (BASE.'/update/config_update_rev'.$version.'.php') )
	{
		include (BASE.'/update/config_update_rev'.$version.'.php');
		if ( ! is_array($up2date) ) return false;
		if ( $version == $up2date['version'].(($up2date['subversion'])?'-'.$up2date['subversion']:'').(($up2date['revision'])?'-'.$up2date['revision']:'') )
		{
			return array_merge($up2date,array('script'=>$up2date['version'].(($up2date['subversion'])?'-'.$up2date['subversion']:'').(($up2date['revision'])?'-'.$up2date['revision']:'')));
			var_dump($up2date);
		}
	}
	else
	{
		return false;
	}
}

$up2date = getDBUpdateFile();
if ( is_array($up2date) )
{
	$status = updateDB($up2date['script']);
	if ( is_array($status) )
	{
		foreach ( $status as $sql ) echo $sql['sql'].' => '.$sql['error'].'<br>';
	}
	else
	{
		echo "done<br>";
	}
}

?>