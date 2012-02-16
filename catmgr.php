<?PHP
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

    $Id: catmgr.php,v 1.5 2006/05/08 19:03:15 cl Exp $

    ChangeLog

    04/03/2006
        Added some comments and some escape/unescape stuff. Tested!
    
    02/05/2006
        Some people told me it would be useful to add functionality
        for deleting and renaming categories.
        Security stuff is there too, so you cannot delete categories
        which aren't empty.
*/
    include('lib.php');
    partdb_init();
    
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


    if ( $action == 'add')
    {
        $query = "INSERT INTO categories (name, parentnode) VALUES (". smart_escape($_REQUEST["new_category"]) .",". smart_escape($_REQUEST["parent_node"]) .");";
        debug_print($query);
        mysql_query ($query);
		$refreshnav = true;
    }
    
    
    if ( $action == 'delete')
    {
        /*
         * Delete a category.
         * Includes confirmation questions. Don't delete the
         * category when there are parts in it.
         */
        if ((! isset($_REQUEST["del_ok"])) && (! isset($_REQUEST["del_nok"])) )
        {
            $special_dialog = true;
            $query = "SELECT COUNT(*) FROM parts WHERE id_category=". smart_escape($_REQUEST["catsel"]) .";";
            debug_print($query);
            $r = mysql_query($query);
            $d = mysql_fetch_row($r); // COUNT(*) queries always give a result!
            if ($d[0] != 0)
            {
                print "<html><body>".
                    "<div style=\"text-align:center;\">".
                    "<div style=\"color:red;font-size:x-large;\">Kategorie kann nicht gel&ouml;scht werden!</div>".
                    "Es gibt noch Teile, die in dieser Kategorie eingetragen sind.".
                    "<form method=\"get\" action=\"\">".
                    "<input type=\"submit\" value=\"OK\">".
                    "</form></div>".
                    "</body></html>";
            }
            else
            {
                print "<html><body>".
                    "<div style=\"text-align:center;\">".
                    "<div style=\"color:red;font-size:x-large;\">M&ouml;chten Sie die Kategorie &quot;". lookup_category_name($_REQUEST["catsel"]) ."&quot; wirklich l&ouml;schen?</div>".
                    "Der L&ouml;schvorgang ist irreversibel!".
                    "<form action=\"\" method=\"post\">".
                    "<input type=\"hidden\" name=\"catsel\"  value=\"". $_REQUEST["catsel"] ."\">".
                    "<input type=\"hidden\" name=\"delete\"  value=\"x\">".
                    "<input type=\"submit\" name=\"del_nok\" value=\"Nicht L&ouml;schen\">".
                    "<input type=\"submit\" name=\"del_ok\"  value=\"L&ouml;schen\">".
                    "</form></div>".
                    "</body></html>";
            }
        }
        else if (isset($_REQUEST["del_ok"]))
        {
            // the user said it's OK to delete the category
            $query = "DELETE FROM categories WHERE id=". smart_escape($_REQUEST["catsel"]) ." LIMIT 1;";
            debug_print ($query);
            mysql_query ($query);
            // resort all child categories to root node
            $query = "UPDATE categories SET parentnode=0 WHERE parentnode=". smart_escape($_REQUEST["catsel"]) ." ;";
            debug_print ($query);
            mysql_query ($query);
			$refreshnav = true;
        }
    }
   

    if ( $action == 'rename')
    {
        /* rename */
        $query = "UPDATE categories SET name=". smart_escape($_REQUEST["new_name"]) ." WHERE id=". smart_escape($_REQUEST["catsel"]) ." LIMIT 1";
        debug_print($query);
        mysql_query($query);
		$refreshnav = true;
    }
   

    if ( $action == 'new_parent')
    {
        /* resort */
        // check if new parent is anywhere in a child node
        if ( ! (in_array( $_REQUEST["parent_node"], find_child_nodes( $_REQUEST["catsel"]))))
        {
            /* do transaction */
            $query = "UPDATE categories SET parentnode=". smart_escape($_REQUEST["parent_node"]) ." WHERE id=". smart_escape($_REQUEST["catsel"]) ." LIMIT 1";
            debug_print($query);
            mysql_query($query);
			$refreshnav = true;
        }
        else
        {
            /* transaction not allowed, would destroy tree structure */
        }
    }

    /*
     * find all nodes below and given node
     */
    function find_child_nodes($cid)
    {
        $result = array();
        $query = "SELECT id FROM categories".
            " WHERE parentnode=". smart_escape($cid) .";";
        $r = mysql_query ($query);
        while ( $d = mysql_fetch_row ($r) )
        {
            // do the same for the next level.
            $result[] = $d[0];
            $result = array_merge( $result, find_child_nodes( $d[0]));
        }
        return( $result);
    }

    /*
     * The buildtree function creates a tree for <select> tags.
     * It recurses trough all categories (and subcategories) and
     * creates the tree. Deeper levels have more spaces in front.
     * As the top-most category (it doesn't exist!) has the ID 0,
     * you have to supply cid=0 at the very beginning.
     */
    function buildtree ($cid, $level)
    {
        $query = "SELECT id,name FROM categories".
            " WHERE parentnode=". smart_escape( $cid).
            " ORDER BY name ASC;";
        $result = mysql_query( $query) or die( mysql_error());
        while ( $d = mysql_fetch_assoc( $result))
        {
            print "<option value=\"". smart_unescape( $d['id']) . "\">";
            for ($i = 0; $i < $level; $i++) print "&nbsp;&nbsp;&nbsp;";
            print smart_unescape( $d['name']) ."</option>\n";

            // do the same for the next level.
            buildtree( $d['id'], $level + 1);
        }
    }

    if ($special_dialog == false)
    {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Kategorien</title>
    <?php print_http_charset(); ?>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
</head>
<body class="body">

<script language="JavaScript" type="text/javascript">
	<?PHP
	if ($refreshnav)
	{
		$refreshnav = false;
		print "parent.frames._nav_frame.location.reload();";		
	}
	?>
</script>


<div class="outer">
    <h2>Kategorie anlegen</h2>
    <div class="inner">
        <form action="" method="post">
            <table>
                <tr>
                    <td>&Uuml;bergeordnete Kategorie ausw&auml;hlen:</td>
                    <td>
                        <select name="parent_node">
                        <option value="0">root node</option>
                        <?PHP buildtree(0, 1); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Name der neuen Kategorie</td>
                    <td>
                        <input type="text" name="new_category">
                        <input type="submit" name="add" value="Anlegen">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>


<div class="outer">
    <h2>Kategorie umbennenen/l&ouml;schen/sortieren</h2>
    <div class="inner">
        <form action="" method="post">
            <table>
                <tr>
                    <td>
                        W&auml;hlen Sie die zu bearbeitende Kategorie:<br>
                    </td>
                    <td>
                        Was soll mit der ausgew&auml;hlten Kategorie geschehen?
                    </td>
                </tr>
                <tr>
                    <td rowspan="3">
                        <select name="catsel" size="15">
                        <?PHP buildtree(0, 1); ?>
                        </select>
                    </td>
                    <td>
                        <input type="submit" name="delete" value="L&ouml;schen">
                    </td>
                </tr>
                <tr>
                    <td>
                        Neuer Name:<br>
                        <input type="text" name="new_name">
                        <input type="submit" name="rename" value="Umbenennen">
                    </td>
                </tr>
                <tr>
                    <td>
                        Neue &Uuml;berkategorie:<br>
                        <select name="parent_node">
                        <option value="0">root node</option>
                        <?PHP buildtree(0, 1); ?>
                        </select>
                        <input type="submit" name="new_parent" value="Umsortieren">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

</body>
</html>

<?PHP
    }
?>
