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

class pack
{

	function __construct ()
	{
		/* check for loaded classes */
		$e =& new _exception;

		$classes = array('ZipArchive');
		$text = array();
		foreach ($classes as $class) if (class_exists($class)===false) $text[]=$class;
		if (count($text)>0) $e -> throw_class_error($text,basename(__FILE__));
	}

	function __destruct()
	{
		foreach ($this as $key => $value) {
			unset($this->$key);
		}
	}

	function zip ( $file = '', $files = array() )
	{

		if ( strlen($file) == 0 || count ($files) == 0 ) return 2; // Errorcode 2: File(s) not found

		$zip = new ZipArchive();
		
		if ($zip->open($file, ZIPARCHIVE::CREATE)!==TRUE) return 4; // Errorcode 4: File not createable

		foreach ( $files as $file2zip )
		{
			$zip->addFile($file2zip);
		}
		$array = array( 'count' => $zip->numFiles, 'status' => $zip->status );
		$zip->close();
		return $array;
	}

	function unzip ( $file = '', $path = '' )
	{

		if ( strlen($file) == 0 || !is_readable($file) || !is_dir($path) ) return 2; // Errorcode 2: File or directory not found

		$zip = new ZipArchive();
		$zip->open($file);
		$zip->extractTo($path);
		$zip->close();
	}

}

?>