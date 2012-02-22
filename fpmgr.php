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
        footprint_add( $_REQUEST["new_footprint"], $_REQUEST["parent_node"]);
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
            footprint_del( $_REQUEST["footprint_sel"]);
        }
    }


    if ( $action == 'rename')
    {
        footprint_rename( $_REQUEST["footprint_sel"], $_REQUEST["new_name"]);
    }
   

    if ( $action == 'new_parent')
    {
        footprint_new_parent( $_REQUEST["footprint_sel"], $_REQUEST["parent_node"]);
    }

    /*
     * Don't show the default text when there's a msg.
     */
    if ($special_dialog == false)
    {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
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
                        <?PHP footprint_build_tree(0, 1); ?>
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
                        <?php footprint_build_tree(0, 1); ?>
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
                        <?PHP footprint_build_tree(0, 1); ?>
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
