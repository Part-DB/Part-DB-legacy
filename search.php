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

    $Id: search.php 510 2012-08-03 weinbauer73@gmail.com $
*/

require_once ('lib.php');

// set action
$action = isset( $_REQUEST['action']) ? $_REQUEST['action'] : 'default';

// catch variables
$pid        = isset( $_REQUEST['pid'])        ? $_REQUEST['pid'] : '';
$keyword    = isset( $_REQUEST['keyword'])    ? $_REQUEST['keyword'] : '';
$search_nam = isset( $_REQUEST['search_nam']) ? $_REQUEST['search_nam'] == 'true' : false;
$search_cat = isset( $_REQUEST['search_cat']) ? $_REQUEST['search_cat'] == 'true' : false;
$search_des = isset( $_REQUEST['search_des']) ? $_REQUEST['search_des'] == 'true' : false;
$search_com = isset( $_REQUEST['search_com']) ? $_REQUEST['search_com'] == 'true' : false;
$search_sup = isset( $_REQUEST['search_sup']) ? $_REQUEST['search_sup'] == 'true' : false;
$search_snr = isset( $_REQUEST['search_snr']) ? $_REQUEST['search_snr'] == 'true' : false;
$search_loc = isset( $_REQUEST['search_loc']) ? $_REQUEST['search_loc'] == 'true' : false;
$search_fpr = isset( $_REQUEST['search_fpr']) ? $_REQUEST['search_fpr'] == 'true' : false;

// remove one part
if ( $action == 'dec')
{
	parts_stock_decrease( $pid);
}

// add one part
if ( $action == 'inc')
{
	parts_stock_increase( $pid);
}

$html = new HTML;
$html -> set_html_meta ( array('title'=>'Suchergebnisse','menu'=>true,'popup'=>true,'hide_id'=>$hide_id) );
$html -> print_html_header();

$keyword_esc = smart_escape_for_search( $keyword);
$result      = parts_select_search( $keyword_esc, $search_nam, $search_cat, $search_des, $search_com, $search_sup, $search_snr, $search_loc, $search_fpr);

$row_odd = true; // $row_odd is used for the alternating bg colors
$prevcat = -1;   // $prevcat remembers the previous category. -1 is
		// an invalid category id.

$table = array();
while ( $data = mysql_fetch_assoc( $result))
{

	/* print new header, if a diffrent category is started */
	if ( $prevcat != $data['id_category'])
	{
		$table[] = array('new_category'=>true);
		$prevcat = $data['id_category'];
		$row_odd = true;
	}
	$table[] = print_table_row( $row_odd, $data, $hide_mininstock);
	$row_odd = ! $row_odd;
}

$array = array (
	'keyword'	=>	$keyword,
	'search_nam'	=>	$search_nam,
	'search_cat'	=>	$search_cat,
	'search_des'	=>	$search_des,
	'search_com'	=>	$search_com,
	'search_sup'	=>	$search_sup,
	'search_snr'	=>	$search_snr,
	'search_loc'	=>	$search_loc,
	'search_fpr'	=>	$search_fpr,
	'table'		=>	$table
);

$html -> parse_html_template( 'table', $array );

$html -> print_html_footer();
?>
