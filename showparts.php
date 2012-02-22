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
    include ("lib.php");
    partdb_init();

    $cid    = ( isset( $_REQUEST['cid']))    ? $_REQUEST['cid']    : '';
    $pid    = ( isset( $_REQUEST['pid']))    ? $_REQUEST['pid']    : '';
    $action = ( isset( $_REQUEST['action'])) ? $_REQUEST['action'] : 'default';

    
    if ( $action == 'dec')  //remove one part
    {
        parts_stock_decrease( $pid);
    }

    if ( $action == 'inc')  //add one part
    {
        parts_stock_increase( $pid);
    }
    

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
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

<div class="outer">
    <h2>Sonstiges</h2>
    <div class="inner">
        <?php
        print "<form action=\"\" method=\"post\">";
        print "<input type=\"hidden\" name=\"cid\" value=\"". $cid ."\">";
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
        <a href="newpart.php?cid=<?php print $cid; ?>" onclick="return popUp('newpart.php?cid=<?php print $cid; ?>');">Neues Teil in dieser Kategorie</a>
    </div>
</div>


<div class="outer">
    <h2>Anzeige der Kategorie &quot;<?PHP print category_get_name( $cid); ?>&quot;</h2>
    <div class="inner">
        <table>
        <?PHP
        
        // check if with or without subcategories
        $catclause = categories_or_child_nodes( $cid, (! isset( $_REQUEST['nosubcat'])));

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

            $query = "SELECT".
                " parts.id,".
                " parts.name,".
                " parts.instock,".
                " parts.mininstock,".
                " footprints.name AS 'footprint',".
                " storeloc.name AS 'location',".
                " parts.comment,".
                " parts.supplierpartnr".
                " FROM parts".
                " LEFT JOIN footprints ON parts.id_footprint=footprints.id".
                " LEFT JOIN storeloc   ON parts.id_storeloc=storeloc.id".
                " WHERE (". $catclause .")".
                " ORDER BY name ASC;";
            $result = mysql_query( $query) or die( mysql_error());

            $rowcount = 0;
            while ( $data_array = mysql_fetch_assoc( $result))
            {
                $rowcount++;
                print_table_row( $rowcount, $data_array);
            }
        }
        ?>
        </table>
    </div>
</div>

</body>
</html>
