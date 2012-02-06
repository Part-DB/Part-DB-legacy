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

    $Id: showparts.php,v 1.11 2006/05/23 21:47:14 cl Exp $

    ChangeLog
    
    07/03/06:
        Added escape/unescape stuff
*/
    include ("lib.php");
    partdb_init();
    
    if(strcmp($_REQUEST["action"], "r") == 0)  //remove one part
    {
        $query = "UPDATE parts SET instock=instock-1 WHERE id=" . smart_escape($_REQUEST["pid"]) . " AND instock >= 1 LIMIT 1;";
        debug_print($query);
        mysql_query($query);
    }
    else if(strcmp($_REQUEST["action"], "a") == 0)  //add one part
    {
        $query = "UPDATE parts SET instock=instock+1 WHERE id=" . smart_escape($_REQUEST["pid"]) . " LIMIT 1;";
        debug_print($query);
        mysql_query($query);
    }
    
    function findallsubcategories ($cid)
    {
        $rv = "id_category=". smart_escape($cid);
        
        $query = "SELECT id FROM categories WHERE parentnode=". smart_escape($cid) .";";
        debug_print ($query."<br>");
        $result = mysql_query ($query);
        while ( ( $d = mysql_fetch_row ($result) ) )
        {
            $rv = $rv . " OR " . findallsubcategories (smart_unescape($d[0]));
        }

        return ($rv);
    }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Teileansicht</title>
    <?php print_http_charset(); ?>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
    <?php
        require( 'config.php');
        if ($hide_id)
        {
            print '<style type="text/css">.idclass { display: none; } </style>';
        } 
    ?>
    <script type="text/javascript" src="dtree.js"></script>
    <script type="text/javascript" src="popup.php"></script>
</head>
<body class="body">

<table class="table">
    <tr>
        <td class="tdtop">
        Sonstiges
        </td>
    </tr>
    <tr>
        <td class="tdtext">
        <?PHP
        print "<form action=\"showparts.php\" method=\"post\">";
        print "<input type=\"hidden\" name=\"cid\" value=\"".$_REQUEST["cid"]."\">";
        print "<input type=\"hidden\" name=\"type\" value=\"index\">";
        if (! isset($_REQUEST["nosubcat"]) )
        {
        print "<input type=\"hidden\" name=\"nosubcat\" value=\"1\">";
        print "<input type=\"submit\" name=\"s\" value=\"Unterkategorien ausblenden\">";
        }
        else
        print "<input type=\"submit\" name=\"s\" value=\"Unterkategorien einblenden\">";
        print "</form>";

        ?>
        <a href="javascript:popUp('newpart.php?cid=<?PHP print $_REQUEST["cid"]; ?>');">Neues Teil in dieser Kategorie</a>
        </td>
    </tr>
</table>

<br>

<table class="table">
    <tr>
        <td class="tdtop">
        Anzeige der Kategorie &quot;<?PHP print lookup_category_name ($_REQUEST["cid"]); ?>&quot;
        </td>
    </tr>
    <tr>
        <td class="tdtext">
        <table>
        <?PHP
        
        // check if with or without subcategories
        if (! isset($_REQUEST["nosubcat"]) )
            $catclause = findallsubcategories ($_REQUEST["cid"]);
        else
            $catclause = "id_category=".$_REQUEST["cid"];

        if ( (strcmp ($_REQUEST["type"], "index") == 0))
        {
        print "<tr class=\"trcat\">".
            "<td></td>".
            "<td>Name</td>".
            "<td>Vorh./<br>Min.Best.</td>".
            "<td>Footprint</td>".
            "<td>Lagerort</td>".
            "<td class='idclass'>ID</td>".
            "<td>Datenbl&auml;tter</td>".
            "<td align=\"center\">-</td>".
            "<td align=\"center\">+</td>".
            "</tr>\n";

        $query = "SELECT ".
            "parts.id,".
            "parts.name,".
            "parts.instock,".
            "parts.mininstock,".
            "footprints.name AS 'footprint',".
            "storeloc.name AS 'loc',".
            "parts.comment, ".
            "parts.supplierpartnr ".
            " FROM parts".
            " LEFT JOIN footprints ON parts.id_footprint=footprints.id".
            " LEFT JOIN storeloc ON parts.id_storeloc=storeloc.id".
            " WHERE (". $catclause .")".
            " ORDER BY name ASC;";

        debug_print ($query);
        $result = mysql_query ($query);

        $rowcount = 0;
        while ( $d = mysql_fetch_row ($result) )
        {
            // just name the results
            $part_id        = $d[0];
            $part_name      = $d[1];
            $footprint_name = $d[4];
            $supplierpartnr = $d[7];

            $rowcount++;
            print_table_row( $rowcount, $part_id, $part_name, $footprint_name, $supplierpartnr, $d[6], $d[2], $d[3], $d[5]);
        }
        }
        // dead code below ?
        else if ( strcmp ($_REQUEST["type"], "showpending") == 0 )
        {
        print "<tr class=\"trcat\"><td></td><td>Name</td><td>Ausstehend</td><td>Vorhanden</td><td>Min. Bestand</td><td>Footprint</td><td>Lagerort</td><td>Datenbl&auml;tter</td></tr>\n";

        $query = 
            "SELECT ".
            "parts.id,".
            "parts.name,".
            "SUM(pending_orders.quantity),".
            "parts.instock,".
            "parts.mininstock,".
            "footprints.name AS 'footprint',".
            "storeloc.name AS 'loc' ".
            "FROM parts ".
            "LEFT JOIN footprints ON parts.id_footprint=footprints.id ".
            "LEFT JOIN storeloc ON parts.id_storeloc=storeloc.id ".
            "INNER JOIN pending_orders ON parts.id=pending_orders.part_id ".
            "WHERE (". $catclause .") ".
            "GROUP BY (pending_orders.part_id) ".
            "ORDER BY name ASC;";

        debug_print ($query);
        $result = mysql_query ($query);

        $rowcount = 0;
        while ( $d = mysql_fetch_row ($result) )
        {
            $rowcount++;
            print "<tr class=\"".( is_odd( $rowcount) ? 'trlist_odd': 'trlist_even')."\">";

            if (has_image($d[0]))
            {
                print "<td  class=\"tdrow0\"><a href=\"javascript:popUp('partinfo.php?pid=". smart_unescape($d[0]) ."');\"><img src=\"gettn.php?pid=". smart_unescape($d[0]) . "\" alt=\"". smart_unescape($d[1]) ."\"></a></td>";
            }
            else
            {
                print "<td class=\"tdrow0\"><img class=\"catbild\" src=\"img/dummytn.png\" alt=\"\"></td>";
            }
            print "<td class=\"tdrow1\"><a href=\"javascript:popUp('partinfo.php?pid=". smart_unescape($d[0]) ."');\">". smart_unescape($d[1]) ."</a></td><td class=\"tdrow2\">". smart_unescape($d[2]) ."</td><td class=\"tdrow3\">". smart_unescape($d[3]) ."</td><td class=\"tdrow4\">". smart_unescape($d[4]) ."</td><td class=\"tdrow5\">". smart_unescape($d[5]) . "</td><td class=\"tdrow6\">". smart_unescape($d[6]) . "</td>";
            print "<td  class=\"tdrow6\">";
            $query = "SELECT datasheeturl FROM datasheets WHERE part_id=". smart_escape($d[0]) ." ORDER BY datasheeturl ASC;";
            $result_ds = mysql_query($query);
            while ( $d_ds = mysql_fetch_row ($result_ds) )
            {
                print "<a href=\"". smart_unescape($d_ds[0]) ."\">Datenblatt</a> ";
            }
            print "</td>";
            print "<tr>\n";
        }
        }
        
        ?>
        </table>
        </td>
    </tr>
</table>

 </body>
</html>
