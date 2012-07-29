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

    $Id: catmgr.php 392 2012-03-03 06:30:03Z bubbles.red@gmail.com $

*/

require_once ('lib.php');

$html = new HTML;

$refreshnav = false;

/*
	* In some cases a confirmation question has to be displayed.
*/
$special_dialog = false;

$action = 'default';
if ( isset( $_REQUEST["add"]))        { $action = 'add';}
if ( isset( $_REQUEST["delete"]))     { $action = 'delete';}
if ( isset( $_REQUEST["rename"]))     { $action = 'rename';}
if ( isset( $_REQUEST["new_parent"])) { $action = 'new_parent';}

$catsel      = isset( $_REQUEST["catsel"]) ? $_REQUEST["catsel"] : -1;
$parentnode  = isset( $_REQUEST["parentnode"])  ? $_REQUEST["parentnode"] : 0;

if ( $action == 'add')
{
	category_add( $_REQUEST["new_category"], $parentnode);
	$refreshnav = true;
}

if ( $action == 'delete')
{
	/*
	* Delete a category.
	* Includes confirmation questions. Don't delete the
	* category when there are parts in it.
	*/
	if ((! isset($_REQUEST["del_ok"])) && (! isset($_REQUEST["del_nok"])) && $catsel >= 0)
	{
		$special_dialog = true;
		if ( parts_count_on_category( $catsel) != 0)
		{
			$array = array('question_delete'=>true);
		}
		else
		{
			$array = array('nothing_delete'=>true,'category_name'=>category_get_name($catsel),'category_sel'=>$catsel);
		}
		$html -> parse_html_template( 'cat_delete', $array );
	}
	else if (isset($_REQUEST["del_ok"]))
	{
		// the user said it's OK to delete the category
		category_del( $catsel);
		$refreshnav = true;
	}
}

if ( $action == 'rename')
{
	/* rename */
	category_rename( $catsel, $_REQUEST["new_name"]);
	$refreshnav = true;
}

if ( $action == 'new_parent')
{
	/* resort */
	category_new_parent( $catsel, $parentnode);
	$refreshnav = true;
}

$data       = category_select( $catsel);
$name       = $data['name'];
$parentnode = $data['parentnode'];
$size       = min( categories_count(), 30);

if ($special_dialog == false)
{

	$html -> set_html_meta ( array('title'=>'Kategorien','http_charset'=>$http_charset,'theme'=>$theme,'css'=>$css,'menu'=>true) );
	$html -> print_html_header();

	$array = array (
		'categories_refreshnav'=>$refreshnav,
		'categories_build_size'=>$size,
		'categories_build_name'=>$name
	);

	ob_start();
	categories_build_tree( 0, 0, $parentnode);
	$array['categories_build_tree_1'] = ob_get_contents();
	ob_end_clean();
	ob_start();
	categories_build_tree( 0, 0, $catsel);
	$array['categories_build_tree_2'] = ob_get_contents();
	ob_end_clean();

	$tmpl = new vlibTemplate(BASE."/templates/$theme/catmgr.php/vlib_cat_edit.tmpl");
	$html -> parse_html_template( 'cat_edit', $array );

	$html -> print_html_footer();

}
?>
