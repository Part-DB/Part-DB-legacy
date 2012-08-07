<?PHP
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

    $Id: stats.php 511 2012-08-05 weinbauer73@gmail.com $
*/

require_once ('lib.php');

function count_dir_entries($dir)
{
	$count = 0;
	$handle = opendir($dir) ;
	while ($entry = readdir($handle))
	{
		if ($entry != "." && $entry != ".." && $entry != ".svn")
		{
			if (is_dir( $dir.$entry))
			{
				$count += count_dir_entries($dir.$entry.'/');
			}
			else
			{
				$count++;
			}
		}
	}
	closedir( $handle );
	return( $count );
}

function count_files($dir)
{
	$count=0;
	$dh  = opendir($dir);
	while (false !== ($filename = readdir($dh))) if (!is_dir($filename)) $count++;
	closedir($dh);
	return $count;
}

$html = new HTML;
$html -> set_html_meta ( array('title'=>'Statistik') );
$html -> print_html_header();

$array = array (
	'parts_count_sum_value' => parts_count_sum_value(),
	'currency'		=> $currency,
	'parts_count_with_prices'=> parts_count_with_prices(),
	'parts_count'=> parts_count(),
	'parts_count_sum_instock'=> parts_count_sum_instock(),
	'categories_count'=> categories_count(),
	'footprint_count'=> footprint_count(),
	'location_count'=> location_count(),
	'suppliers_count'=> suppliers_count(),
	'devices_count'=> ((!$disable_devices)?devices_count():false),
	'images_count'=>$countimages,
	'count_footprints'=>count_dir_entries('tools/footprints/'),
	'count_images'=>count_files('img/'),
	'count_iclogos'=>count_files('tools/iclogos/')
);

$html -> parse_html_template( 'stats', $array );

$html -> print_html_footer();
?>