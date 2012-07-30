<?php
/*
    part-db version 0.1
    Copyright (C) 2005 Christoph Lechner
    http://www.cl-projects.de/

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

    $Id: startup.php 446 2012-06-16 06:21:08Z bubbles.red@gmail.com $

*/

require_once ('db_update.php');
require_once ('authors.php');
require_once ('lib.php');

$html = new HTML;
$html -> set_html_meta ( array(
		'title'		=>	$title,
		'http_charset'	=>	$http_charset,
		'theme'		=>	$theme,
		'css'		=>	$css,
		'menu'		=>	true
	)
);
$html -> print_html_header();
$html -> load_html_template('startup');
/*
* This variable determines wheater the user is reminded to add
* add least one loc, one footprint, one category and one supplier.
*/
$display_warning = false;
// predefines
$good = "&#x2714; ";
$bad  = "&#x2718; ";

$html -> set_html_variable('startup_title', $startup_title, 'string');
$html -> set_html_variable('get_svn_revision', get_svn_revision(), 'string');

// catch output to do fine formating later
if ( checkDBUpdateNeeded())
{
	$html -> set_html_variable('db_version', getDBVersion(), 'integer');
	if ( getDBAutomaticUpdateActive())
	{
		doDBUpdate();
	}
	else
	{
		$html -> set_html_variable('disabled_autoupdate', true, 'boolean');
	}
}

if (categories_count() == 0 || location_count() == 0 || footprint_count() == 0 || suppliers_count() == 0)
{
	$html -> set_html_variable('display_warning', true,'boolean');
	$html -> set_html_variable('missing_category', ((categories_count()==0)?$bad:$good),'string');
	$html -> set_html_variable('missing_storeloc', ((location_count()==0)?$bad:$good),'string');
	$html -> set_html_variable('missing_footprint', ((footprint_count()==0)?$bad:$good),'string');
	$html -> set_html_variable('missing_supplier', ((suppliers_count()==0)?$bad:$good),'string');
}

$html -> set_html_variable('database_update', $database_update, 'boolean');
$html -> set_html_variable('banner', $banner, 'string');

if (! $disable_update_list)
{
	$rss_file   = join ( ' ', file ("http://code.google.com/feeds/p/part-db/downloads/basic"));
	$rss_zeilen = array ( "title", "updated", "id" );
	$rss_array  = explode ( "<entry>", $rss_file );
	
	// show only the last actual versions
	$count = 4;
	$rss_text = array();
	foreach ( $rss_array as $string )
	{
		// show all lines from rss feed
		foreach ( $rss_zeilen as $zeile )
		{
			// find tags
			preg_match_all( "|<$zeile>(.*)</$zeile>|Usim", $string, $preg_match);
			$$zeile = $preg_match [1] [0];
			// make clickable if http url
			$$zeile = preg_replace('`((?:http)://\S+[[:alnum:]]/?)`si', '<a href="\\1">\\1</a>', $$zeile);
			$rss_text[]['zeile'] = $$zeile;
		}
		if (!(--$count)) break;
	}
	$html -> set_html_loop('update_list', $rss_text);
}

$html -> set_html_loop('authors', $authors);
$html -> print_html_template();
$html -> print_html_footer();

?>
