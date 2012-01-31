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

    01/03/06
        Added escape stuff.
*/
    include('lib.php');
    partdb_init();
    
    $action = 'default';
    if ( isset( $_REQUEST["add"]))    { $action = 'add';}
    if ( isset( $_REQUEST["delete"])) { $action = 'delete';}
    if ( isset( $_REQUEST["rename"])) { $action = 'rename';}
    
    if ( $action == 'add')
    {
        $query = "INSERT INTO footprints (name) VALUES (". smart_escape($_REQUEST["new_footprint"]) .");";
        debug_print($query);
        mysql_query($query);
    }
    
    if ( $action == 'delete')
    {
        // limit protects from runaway queries
        $query = "DELETE FROM footprints WHERE id=". smart_escape($_REQUEST["footprint_sel"]) ." LIMIT 1;";
        debug_print($query);
        mysql_query($query);
    }

    if ( $action == 'rename')
    {
        $query = "UPDATE footprints SET name=". smart_escape($_REQUEST["new_name"]) ." WHERE id=". smart_escape($_REQUEST["footprint_sel"]) ." LIMIT 1;";
        debug_print ($query);
        mysql_query ($query);
    }

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Footprints</title>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
</head>
<body class="body">

<table class="table">
    <tr>
        <td class="tdtop">
        Footprint anlegen 
        </td>
    </tr>
    <tr>
        <td class="tdtext">
            <form action="" method="post">
            Neuer Footprint:
            <input type="text" name="new_footprint">
            <input type="submit" name="add" value="Anlegen">
            </form>
        </td>
    </tr>
</table>

<br>

<table class="table">
    <tr>
        <td class="tdtop">
        Footprint umbenennen/l&ouml;schen
        </td>
    </tr>
    <tr>
        <td class="tdtext">
            <form action="" method="post">
            <table>
                <tr>
                    <td rowspan="2">
                        <select name="footprint_sel" size="15">
                        <?PHP
                            $query = "SELECT id,name FROM footprints ORDER BY name ASC;";
                            $r = mysql_query ($query);
                    
                            $ncol = mysql_num_rows ($r);
                            for ($i = 0; $i < $ncol; $i++)
                            {
                                $d = mysql_fetch_row ($r);
                                print "<option value=\"". smart_unescape($d[0])."\">". smart_unescape($d[1]) ."</option>\n";
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
                        <input type="text"   name="new_name">
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
