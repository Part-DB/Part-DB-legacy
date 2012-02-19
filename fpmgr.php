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

    $Id: fpmgr.php,v 1.5 2006/03/06 23:05:14 cl Exp $

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
   

    if ( $action == 'add')
    {
        $query = "INSERT INTO footprints (name, parentnode) VALUES (".
            smart_escape($_REQUEST["new_footprint"]) .",".
            smart_escape($_REQUEST["parent_node"])   .");";
        mysql_query( $query) or die( mysql_error());
    }
   

    if ( $action == 'delete')
    {
        /*
         * Delete a footprint
         * Don't delete when there are parts use this footprin.
         */
        $query  = "SELECT (name) FROM parts WHERE id_footprint=". smart_escape($_REQUEST["footprint_sel"]) .";";
        $result = mysql_query( $query);
        $ncol   = mysql_num_rows( $result);
        if ($ncol > 0)
        {
            $special_dialog = true;

            // catch up to three examples, where footprint is in use
            for ($i = 0; ($i < $ncol) and ($i < 3); $i++)
            {
                $d         = mysql_fetch_assoc( $result);
                $example[] = $d['name'];
            }
            $example = implode( ', ', $example);

            // give warning message
            print "<html><body>".
                "<div style=\"text-align:center;\">".
                "<div style=\"color:red;font-size:x-large;\">Footprint kann nicht gel&ouml;scht werden!</div>";
            if ($ncol == 1)
                print "Es gibt noch 1 Teil, das diesen Footprint verwendet: ". $example;
            else
                print "Es gibt noch ". $ncol ." Teile, die diesen Footprint verwenden (z.B.: ". $example. ").";
            print "<form method=\"get\" action=\"\">".
                "<input type=\"submit\" value=\"OK\">".
                "</form></div>".
                "</body></html>";
        }
        else
        {
            // delete footprint
            $query = "DELETE FROM footprints".
                " WHERE id=". smart_escape($_REQUEST["footprint_sel"]).
                " LIMIT 1;";
            mysql_query( $query);
            // resort all child footprints to root node
            $query = "UPDATE footprints SET parentnode=0 WHERE parentnode=". smart_escape($_REQUEST["footprint_sel"]) ." ;";
            mysql_query ($query);
        }
    }


    if ( $action == 'rename')
    {
        $query = "UPDATE footprints SET name=". smart_escape($_REQUEST["new_name"]) ." WHERE id=". smart_escape($_REQUEST["footprint_sel"]) ." LIMIT 1;";
        mysql_query ($query);
    }
   

    if ( $action == 'new_parent')
    {
        /* resort */
        // check if new parent is anywhere in a child node
        if ( !(in_array( $_REQUEST["parent_node"], find_child_nodes( $_REQUEST["footprint_sel"]))))
        {
            /* do transaction */
            $query = "UPDATE footprints SET parentnode=". smart_escape($_REQUEST["parent_node"]) ." WHERE id=". smart_escape($_REQUEST["footprint_sel"]) ." LIMIT 1";
            mysql_query($query);
        }
        else
        {
            /* transaction not allowed, would destroy tree structure */
        }
    }

    /*
     * find all nodes below and given node
     */
    function find_child_nodes( $id)
    {
        $result = array();
        $query = "SELECT id FROM footprints WHERE parentnode=". smart_escape( $id) .";";
        $r = mysql_query( $query);
        while ( $data = mysql_fetch_assoc( $r))
        {
            // do the same for the next level.
            $result[] = $data['id'];
            $result = array_merge( $result, find_child_nodes( $data['id']));
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
        $query  = "SELECT id, name FROM footprints".
            " WHERE parentnode=". smart_escape( $id).
            " ORDER BY name ASC;";
        $result = mysql_query( $query) or die( mysql_error());
        while ( $data = mysql_fetch_assoc( $result))
        {
            print "<option value=\"". smart_unescape( $data['id']) . "\">";
            for ( $i = 0; $i < $level; $i++) 
                print "&nbsp;&nbsp;&nbsp;";
            print smart_unescape( $data['name']).
                "</option>\n";

            // do the same for the next level.
            buildtree( $data['id'], $level + 1);
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
    <title>Footprints</title>
    <?php print_http_charset(); ?>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
</head>
<body class="body">

<div class="outer">
    <h2>Footprint anlegen</h2> 
    <div class="inner">
        <form action="" method="post">
            <table>
                <tr>
                    <td>&Uuml;bergeordnete Footprinthierarchie ausw&auml;hlen:</td>
                    <td>
                        <select name="parent_node">
                        <option value="0">root node</option>
                        <?PHP buildtree(0, 1); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Neuer Footprint:</td>
                    <td>
                        <input type="text"     name="new_footprint">
                        <input type="submit"   name="add" value="Anlegen">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>


<div class="outer">
    <h2>Footprints umbenennen/l&ouml;schen/sortieren</h2>
    <div class="inner">
        <form action="" method="post">
            <table>
                <tr>
                    <td rowspan="3">
                        Zu bearbeitenden Footprint w&auml;hlen:<br>
                        <select name="footprint_sel" size="15">
                        <?php buildtree(0, 1); ?>
                        </select>
                    </td>
                    <td>
                        <input type="submit" name="delete" value="L&ouml;schen">
                    </td>
                </tr>
                <tr>
                    <td>
                        Neuer Name:<br>
                        <input type="text"   name="new_name">
                        <input type="submit" name="rename" value="Umbenennen">
                    </td>
                </tr>
                <tr>
                    <td>
                        Neuer &uuml;bergeordneter Footprint:<br>
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
<?PHP } ?>
