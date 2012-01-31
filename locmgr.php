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
    if ( isset( $_REQUEST["add"]))    { $action = 'add';}
    if ( isset( $_REQUEST["delete"])) { $action = 'delete';}
    if ( isset( $_REQUEST["rename"])) { $action = 'rename';}


    if ( $action == 'add')
    {
        if ($_REQUEST["series"] == "true")
        {
            // add location series
            $query = "INSERT INTO storeloc (name) VALUES ";
            $start = (int)$_REQUEST["series_start"];
            $end   = (int)$_REQUEST["series_end"];
            for ($index = $start; $index <= $end; $index++) {
                $query = $query ."(". smart_escape( $_REQUEST["new_location"]. $index) ."), ";
            }
            // complete query
            $query = substr($query, 0, -2).";";
        }
        else
        {
            // add a location
            $query = "INSERT INTO storeloc (name) VALUES (". smart_escape($_REQUEST["new_location"]) .");";
        }
        debug_print ($query);
        mysql_query ($query);
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
            debug_print ($query);
            mysql_query ($query);
        }
    }
    
    if ( $action == 'rename')
    {
        $query = "UPDATE storeloc SET name=". smart_escape($_REQUEST["new_name"]) ." WHERE id=". smart_escape($_REQUEST["location_sel"]) ." LIMIT 1;";
        debug_print ($query);
        mysql_query ($query);
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
    <title>Navigation</title>
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
                    <td>
                        Neuer Lagerort:
                        <input type="text"     name="new_location">
                        <input type="submit"   name="add" value="Anlegen">
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="checkbox" name="series" value="true" onclick="switch_series()">Serie erzeugen&nbsp;&nbsp;
                        von <input type="text" name="series_start" size="5" value="1" disabled>
                        bis <input type="text" name="series_end"   size="5" value="9" disabled>
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
        Lagerorte umbenennen/l&ouml;schen
        </td>
    </tr>
    <tr>
        <td class="tdtext">
            <form action="" method="post">
            <table>
                <tr>
                    <td rowspan="2">
                        Zu bearbeitenden Lagerort w&auml;hlen:<br>
                        <select name="location_sel" size="10">
                        <?PHP
                            $query = "SELECT id,name FROM storeloc ORDER BY name ASC;";
                            debug_print($query);
                            $r = mysql_query ($query);
                    
                            $ncol = mysql_num_rows ($r);
                            for ($i = 0; $i < $ncol; $i++)
                            {
                                $d = mysql_fetch_row ($r);
                                print "<option value=\"". smart_unescape($d[0]) ."\">". smart_unescape($d[1]) ."</option>\n";
                            }
                        ?>
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
            </table>
            </form>
        </td>
    </tr>
</table>

</body>
</html>
<?PHP } ?>
