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

    $Id: showparts.php 510 2012-08-03 weinbauer73@gmail.com $
*/

require_once ('lib.php');

$cid    = isset( $_REQUEST['cid'])    ? $_REQUEST['cid']          : '';
$pid    = isset( $_REQUEST['pid'])    ? $_REQUEST['pid']          : '';
$subcat = isset( $_REQUEST['subcat']) ? (bool)$_REQUEST['subcat'] : true;
$action = isset( $_REQUEST['action']) ? $_REQUEST['action']       : 'default';

// logical inverted text
$subcat_text = $subcat ? 'ausblenden' : 'einblenden';

if ( $action == 'dec')
{
    // remove one part
    parts_stock_decrease($pid);
}

if ( $action == 'inc')
{
    // add one part
    parts_stock_increase($pid);
}

$html = new HTML;
$html -> set_html_meta ( array(
    'title'         =>  'Deviceinfo',
    'menu'          =>  true,
    'popup'         =>  true,
    'hide_id'       =>  $hide_id
    )
);
$html -> print_html_header();

$table = array(array('new_category'=>true));
$result   = parts_select_category( $cid, $subcat);
$row_odd = true;
while ( $data = mysql_fetch_assoc( $result))
{
    $table[] = print_table_row( $row_odd, $data, $hide_mininstock);
    $row_odd = ! $row_odd;
}

$array = array(
    'cid'           =>  $cid,
    'subcat'        =>  (! $subcat),
    'subcat_text'       =>  $subcat_text,
    'category_get_name' =>  category_get_name($cid),
    'hide_mininstock'   =>  $hide_mininstock,
    'table'         =>  $table
    );

$html -> parse_html_template( 'table', $array );
$html -> print_html_footer();

?>
