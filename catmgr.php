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

    $Id$

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
        category_add( $_REQUEST["new_category"], $_REQUEST["parent_node"]);
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
            if ( parts_count_on_category( $_REQUEST["catsel"]) != 0)
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
                    "<div style=\"color:red;font-size:x-large;\">M&ouml;chten Sie die Kategorie &quot;". category_get_name($_REQUEST["catsel"]) ."&quot; wirklich l&ouml;schen?</div>".
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
            category_del( $_REQUEST["catsel"]);
			$refreshnav = true;
        }
    }
   

    if ( $action == 'rename')
    {
        /* rename */
        category_rename( $_REQUEST["catsel"], $_REQUEST["new_name"]);
		$refreshnav = true;
    }
   

    if ( $action == 'new_parent')
    {
        /* resort */
        category_new_parent( $_REQUEST["catsel"], $_REQUEST["parent_node"]);
        $refreshnav = true;
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
                        <?php categories_build_tree(); ?>
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
                        <?php categories_build_tree(); ?>
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
                        <?php categories_build_tree(); ?>
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
