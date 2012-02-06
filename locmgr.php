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

    $Id: locmgr.php,v 1.7 2006/04/12 12:27:29 cl Exp $

    28/02/06
        Added escape/unescape functions where required

    06/04/06
        Now it is possible to edit the name of the location.
        Some sanity checks are new, too! The base line is to
        avoid database corruption, therefore we refuse to
        delete locs with parts in them.
*/
    include('lib.php');
    partdb_init();

    /*
     * In some cases a confirmation question has to be displayed.
     */
    $special_dialog = false;

    /*
     * this is the dispatcher ...
     */
    $action = 'default';
    if ( isset( $_REQUEST["add"]))        { $action = 'add';}
    if ( isset( $_REQUEST["delete"]))     { $action = 'delete';}
    if ( isset( $_REQUEST["rename"]))     { $action = 'rename';}
    if ( isset( $_REQUEST["new_parent"])) { $action = 'new_parent';}
    if ( isset( $_REQUEST["update"]))     { $action = 'update';}


    if ( $action == 'add')
    {
        if ($_REQUEST["series"] == "true")
        {
            // add location series
            $start = (int)$_REQUEST["series_start"];
            $end   = (int)$_REQUEST["series_end"];
            for ($index = $start; $index <= $end; $index++) {
                $query = "INSERT INTO storeloc (name, parentnode) VALUES (".
                    smart_escape( $_REQUEST["new_location"].$index) .",".
                    smart_escape( $_REQUEST["parent_node"]) ."); ";
                mysql_query ($query);
            }
        }
        else
        {
            // add a location
            $query = "INSERT INTO storeloc (name, parentnode) VALUES (".
                smart_escape( $_REQUEST["new_location"]) .",".
                smart_escape( $_REQUEST["parent_node"]) .");";
            mysql_query ($query);
        }
    }

    if ( $action == 'delete')
    {
        /*
         * Delete a location.
         * Includes confirmation questions. Don't delete the
         * location when there are parts in this location.
         */
        if ((! isset($_REQUEST["del_ok"])) && (! isset($_REQUEST["del_nok"])) )
        {
            $special_dialog = true;
            $query = "SELECT COUNT(*) FROM parts WHERE id_storeloc=". smart_escape($_REQUEST["location_sel"]) .";";
            debug_print($query);
            $r = mysql_query($query);
            $d = mysql_fetch_row($r); // COUNT(*) queries always give a result!
            if ($d[0] != 0)
            {
                print "<html><body>".
                    "<div style=\"text-align:center;\">".
                    "<div style=\"color:red;font-size:x-large;\">Lagerort kann nicht gel&ouml;scht werden!</div>".
                    "Es gibt noch Teile, die diesen Lagerort als Ort eingetragen haben.".
                    "<form method=\"get\" action=\"\">".
                    "<input type=\"submit\" value=\"OK\">".
                    "</form></div>".
                    "</body></html>";
            }
            else
            {
                print "<html><body>".
                    "<div style=\"text-align:center;\">".
                    "<div style=\"color:red;font-size:x-large;\">M&ouml;chten Sie den Lagerort &quot;". lookup_location_name($_REQUEST["location_sel"]) ."&quot; wirklich l&ouml;schen?</div>".
                    "Der L&ouml;schvorgang ist irreversibel!".
                    "<form action=\"\" method=\"post\">".
                    "<input type=\"hidden\" name=\"location_sel\" value=\"". $_REQUEST["location_sel"] ."\">".
                    "<input type=\"hidden\" name=\"delete\"  value=\"x\">".
                    "<input type=\"submit\" name=\"del_nok\" value=\"Nicht L&ouml;schen\">".
                    "<input type=\"submit\" name=\"del_ok\"  value=\"L&ouml;schen\">".
                    "</form></div>".
                    "</body></html>";
            }
        }
        else if (isset($_REQUEST["del_ok"]))
        {
            // the user said it's OK to delete the location
            $query = "DELETE FROM storeloc WHERE id=". smart_escape($_REQUEST["location_sel"]) ." LIMIT 1;";
            mysql_query ($query);
            // resort all child categories to root node
            $query = "UPDATE storeloc SET parentnode=0 WHERE parentnode=". smart_escape($_REQUEST["location_sel"]) ." ;";
            mysql_query ($query);
        }
    }
    
    if ( $action == 'rename')
    {
        $query = "UPDATE storeloc SET name=". smart_escape($_REQUEST["new_name"]) ." WHERE id=". smart_escape($_REQUEST["location_sel"]) ." LIMIT 1;";
        debug_print ($query);
        mysql_query ($query);
    }
   

    if ( $action == 'new_parent')
    {
        /* resort */
        // check if new parent is anywhere in a child node
        if ( ! (in_array( $_REQUEST["parent_node"], find_child_nodes( $_REQUEST["location_sel"]))))
        {
            /* do transaction */
            $query = "UPDATE storeloc SET parentnode=". smart_escape($_REQUEST["parent_node"]) ." WHERE id=". smart_escape($_REQUEST["location_sel"]) ." LIMIT 1";
            mysql_query($query);
        }
        else
        {
            /* transaction not allowed, would destroy tree structure */
        }
    }
    
    if ( $action == 'update')
    {
        $value = isset( $_REQUEST["is_full"]) ? $_REQUEST["is_full"] : 'false';
        $value = ($value == 'true') ? '1' : '0';
        $query = "UPDATE storeloc SET is_full=". smart_escape( $value) ." WHERE id=". smart_escape($_REQUEST["location_sel"]) ." LIMIT 1;";
        mysql_query ($query);
    }

    /*
     * find all nodes below and given node
     */
    function find_child_nodes( $id)
    {
        $result = array();
        $query = "SELECT id FROM storeloc WHERE parentnode=". smart_escape( $id) .";";
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
     * It recurses trough all locations (and sublocations) and
     * creates the tree. Deeper levels have more spaces in front.
     * As the top-most location (it doesn't exist!) has the ID 0,
     * you have to supply id=0 at the very beginning.
     */
    function buildtree ($id, $level)
    {
        $query = "SELECT id, name, is_full FROM storeloc WHERE parentnode=". smart_escape( $id) .";";
        $r = mysql_query( $query);
        while ( $d = mysql_fetch_row( $r) )
        {
            print "<option value=\"". smart_unescape( $d[0]) . "\">";
            for ( $i = 0; $i < $level; $i++) 
                print "&nbsp;&nbsp;&nbsp;";
            print smart_unescape( $d[1]).
                ( $d[2] ? ' [voll]' : '').
                "</option>\n";

            // do the same for the next level.
            buildtree( $d[0], $level + 1);
        }
    }

    /*
     * Don't show the default text when there's a msg.
     */
    if ($special_dialog == false)
    {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Lagerorte</title>
    <?php print_http_charset(); ?>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
</head>
<body class="body">

<script type="text/javascript">
    function switch_series() 
    {
        if(document.create.series.checked)
        {
            document.create.series_start.disabled=false;
            document.create.series_end.disabled=false;
        }
        else
        {
            document.create.series_start.disabled=true;
            document.create.series_end.disabled=true;
        }
    }
</script> 


<table class="table">
    <tr>
        <td class="tdtop">
        Lagerorte anlegen 
        </td>
    </tr>
    <tr>
        <td class="tdtext">
            <form action="" method="post" name="create">
            <table>
                <tr>
                    <td>&Uuml;bergeordneten Lagerort ausw&auml;hlen:</td>
                    <td>
                        <select name="parent_node">
                        <option value="0">root node</option>
                        <?PHP buildtree(0, 1); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Neuer Lagerort:</td>
                    <td>
                        <input type="text"     name="new_location">
                        <input type="submit"   name="add" value="Anlegen">
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="checkbox" name="series" value="true" onclick="switch_series()">Serie erzeugen&nbsp;&nbsp;
                    </td>
                    <td>
                        von <input type="text" name="series_start" size="4" value="1" disabled>
                        bis <input type="text" name="series_end"   size="4" value="3" disabled>
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
        Lagerorte umbenennen/l&ouml;schen/sortieren
        </td>
    </tr>
    <tr>
        <td class="tdtext">
            <form action="" method="post">
            <table>
                <tr>
                    <td rowspan="4">
                        Zu bearbeitenden Lagerort w&auml;hlen:<br>
                        <select name="location_sel" size="15">
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
                        Neuer &uuml;bergeordneter Lagerort:<br>
                        <select name="parent_node">
                        <option value="0">root node</option>
                        <?PHP buildtree(0, 1); ?>
                        </select>
                        <input type="submit" name="new_parent" value="Umsortieren">
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="checkbox" name="is_full" value="true">voll<br>
                        <input type="checkbox" name="is_full" value="false">noch Platz<br>
                        <input type="submit"   name="update"  value="Markieren">
                    </td>
                </tr>
            </table>
            </form>
        </td>
    </tr>
</table>

</body>
</html>
<?PHP } ?>
