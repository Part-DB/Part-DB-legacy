<?php
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

    $Id: nopriceparts.php 364 2012-02-23 17:11:52Z bubbles.red@gmail.com $

*/

    require_once ('lib.php');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Nicht mehr erh&auml;ltliche Teile</title>
    <?php print_http_charset(); ?>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
    <script type="text/javascript" src="popup.php"></script>
</head>
<body class="body">


<div class="outer">
    <h2>Nicht mehr erh&auml;ltliche Teile</h2>
    <div class="inner">
        <table>
        
        <tr class="trcat">
            <td></td>
            <td>Name</td>
            <td>Beschreibung</td>
            <td>Vorh./<br>Min.Best.</td>
            <td>Footprint</td>
            <td>Lagerort</td>
            <td>Lieferant</td>
            <td>Bestell-Nr.</td>
            <td align="center">Preis</td>
        </tr>

        <?php
        $result = parts_select_obsolete();

        $rowcount = 0;
        while ( $data = mysql_fetch_assoc( $result))
        {
            $rowcount++;
            print "<tr class=\"".( is_odd( $rowcount) ? 'trlist_odd': 'trlist_even')."\">";
            
            // Pictures
            print "<td class=\"tdrow0\">";
            print_table_image( $data['id'], $data['name'], $data['footprint_filename']);
            print "</td>". PHP_EOL;
            
            print "<td class=\"tdrow1\"><a title=\"Kommentar: ". htmlspecialchars( smart_unescape( $data['comment']));
            print "\" href=\"javascript:popUp('partinfo.php?pid=". smart_unescape( $data['id']) ."');\">". smart_unescape( $data['name']) ."</a></td>". PHP_EOL;
            print "<td class=\"tdrow1\">". smart_unescape( $data['description']) ."</td>". PHP_EOL;
            print "<td class=\"tdrow1\">". smart_unescape( $data['instock']) ."/". smart_unescape( $data['mininstock']) ."</td>". PHP_EOL;
            print "<td class=\"tdrow1\">". smart_unescape( $data['footprint']) ."</td>". PHP_EOL;
            print "<td class=\"tdrow1\">". smart_unescape( $data['location']) ."</td>". PHP_EOL;
            print "<td class=\"tdrow1\">". smart_unescape( $data['supplier']) ."</td>". PHP_EOL; 
            print "<td class=\"tdrow1\">". smart_unescape( $data['supplierpartnr']) ."</td>". PHP_EOL; 
            print "<td class=\"tdrow1\">". smart_unescape( $data['price']) ."</td>". PHP_EOL; 
            print "</tr>". PHP_EOL;
        }
        ?>
        </table>
    </div>
</div>

</body>
</html>
