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

    $Id: $
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
        $query = "INSERT INTO devices (name, parentnode) VALUES (". smart_escape($_REQUEST["new_device"]) .",". smart_escape($_REQUEST["parent_node"]) .");";
        debug_print($query);
        mysql_query ($query);
		$refreshnav = true;
    }
    
    
    if ( $action == 'delete')
    {
        /*
         * Delete a device.
         */
        if ((! isset($_REQUEST["del_ok"])) && (! isset($_REQUEST["del_nok"])) )
        {
            $special_dialog = true;
                print "<html><body>".
                    "<div style=\"text-align:center;\">".
                    "<div style=\"color:red;font-size:x-large;\">M&ouml;chten Sie die Baugruppe &quot;". lookup_device_name($_REQUEST["devsel"]) ."&quot; wirklich l&ouml;schen?</div>".
                    "Der L&ouml;schvorgang ist irreversibel!".
                    "<form action=\"\" method=\"post\">".
                    "<input type=\"hidden\" name=\"devsel\"  value=\"". $_REQUEST["devsel"] ."\">".
                    "<input type=\"hidden\" name=\"delete\"  value=\"x\">".
                    "<input type=\"submit\" name=\"del_nok\" value=\"Nicht L&ouml;schen\">".
                    "<input type=\"submit\" name=\"del_ok\"  value=\"L&ouml;schen\">".
                    "</form></div>".
                    "</body></html>";
        }
        else if (isset($_REQUEST["del_ok"]))
        {
            // the user said it's OK to delete the device
            $query = "DELETE FROM devices WHERE id=". smart_escape($_REQUEST["devsel"]) ." LIMIT 1;";
            debug_print ($query);
            mysql_query ($query);
            // resort all child devices to root node
            $query = "UPDATE devices SET parentnode=0 WHERE parentnode=". smart_escape($_REQUEST["devsel"]) ." ;";
            debug_print ($query);
            mysql_query ($query);
			$refreshnav = true;
        }
    }
   

    if ( $action == 'rename')
    {
        /* rename */
        $query = "UPDATE devices SET name=". smart_escape($_REQUEST["new_name"]) ." WHERE id=". smart_escape($_REQUEST["devsel"]) ." LIMIT 1";
        debug_print($query);
        mysql_query($query);
		$refreshnav = true;
    }
   

    if ( $action == 'new_parent')
    {
        /* resort */
        // check if new parent is anywhere in a child node
        if ( ! (in_array( $_REQUEST["parent_node"], find_child_nodes( $_REQUEST["devsel"]))))
        {
            /* do transaction */
            $query = "UPDATE devices SET parentnode=". smart_escape($_REQUEST["parent_node"]) ." WHERE id=". smart_escape($_REQUEST["devsel"]) ." LIMIT 1";
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
    function find_child_nodes($devid)
    {
        $result = array();
        $query = "SELECT id FROM devices WHERE parentnode=". smart_escape($devid) .";";
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
     * It recurses trough all devices (and subdevices) and
     * creates the tree. Deeper levels have more spaces in front.
     * As the top-most device (it doesn't exist!) has the ID 0,
     * you have to supply devid=0 at the very beginning.
     */
    function buildtree ($devid, $level)
    {
        $query = "SELECT id,name FROM devices WHERE parentnode=". smart_escape($devid) .";";
        $r = mysql_query ($query);
        while ( $d = mysql_fetch_row ($r) )
        {
            print "<option value=\"". smart_unescape($d[0]) . "\">";
            for ($i = 0; $i < $level; $i++) print "&nbsp;&nbsp;&nbsp;";
            print smart_unescape($d[1]) ."</option>\n";

            // do the same for the next level.
            buildtree ($d[0], $level + 1);
        }
    }

    if ($special_dialog == false)
    {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Baugruppen</title>
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


<table class="table">
    <tr>
        <td class="tdtop">
        Baugruppe anlegen
        </td>
    </tr>
    <tr>
        <td class="tdtext">
            <form action="" method="post">
            <table>
                <tr>
                    <td>&Uuml;bergeordnete Baugruppe ausw&auml;hlen:</td>
                    <td>
                        <select name="parent_node">
                        <option value="0">root node</option>
                        <?PHP buildtree(0, 1); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Name der neuen Baugruppe</td>
                    <td>
                        <input type="text" name="new_device">
                        <input type="submit" name="add" value="Anlegen">
                    </td>
                </tr>
            </table>
            </form>
        </td>
    </tr>
</table>

<br>

<table class="table">
    <tr>
        <td class="tdtop">
        Baugruppe umbennenen/l&ouml;schen/sortieren
        </td>
    </tr>
    <tr>
        <td class="tdtext">
            <form action="" method="post">
            <table>
                <tr>
                    <td>
                        W&auml;hlen Sie die zu bearbeitende Baugruppe:<br>
                    </td>
                    <td>
                        Was soll mit der ausgew&auml;hlten Baugruppe geschehen?
                    </td>
                </tr>
                <tr>
                    <td rowspan="3">
                        <select name="devsel" size="15">
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
                        Neue &Uuml;berbaugruppe:<br>
                        <select name="parent_node">
                        <option value="0">root node</option>
                        <?PHP buildtree(0, 1); ?>
                        </select>
                        <input type="submit" name="new_parent" value="Umsortieren">
                    </td>
                </tr>
            </table>
            </form>
        </td>
    </tr>
</table>

</body>
</html>

<?PHP
    }
?>
