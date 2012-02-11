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

    $Id: search.php,v 1.3 2006/03/09 15:08:09 cl Exp $

    06/03/06    Added escape/unescape calls
    05/12/09    Edit Parts over Popup (k.jacobs)
*/
    include('lib.php');
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
    
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Suchergebnisse</title>
    <?php print_http_charset(); ?>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
    <?php
        require( 'config.php');
        if ($hide_id)
        {
            print '<style type="text/css">.idclass { display: none; } </style>';
        } 
    ?>
    <script type="text/javascript" src="popup.php"></script>
</head>
<body class="body">

<table class="table">
    <tr>
        <td class="tdtop" colspan="9">
        Suchergebnis - Sie suchten nach &quot;<?PHP print $_REQUEST['keyword']; ?>&quot;
        </td>
    </tr>

    <tr><td></td></tr>

    <?PHP
        // execute the SQL query (DON'T USE smart_escape HERE, because
        // it breaks the query)
        $kw = '\'%'. mysql_real_escape_string($_REQUEST['keyword']) .'%\'';
        // build search strings
        if ( $_REQUEST['search_nam'] == "true") { $query_nam = " OR (parts.name LIKE ".$kw.")";           } 
        if ( $_REQUEST['search_com'] == "true") { $query_com = " OR (parts.comment LIKE ".$kw.")";        }
        if ( $_REQUEST['search_sup'] == "true") { $query_sup = " OR (suppliers.name LIKE ".$kw.")";       }
        if ( $_REQUEST['search_snr'] == "true") { $query_snr = " OR (parts.supplierpartnr LIKE ".$kw.")"; }
        if ( $_REQUEST['search_loc'] == "true") { $query_loc = " OR (storeloc.name LIKE ".$kw.")";        }
        if ( $_REQUEST['search_fpr'] == "true") { $query_fpr = " OR (footprints.name LIKE ".$kw.")";      }
        $query = 
            "SELECT ".
            "parts.id,".
            "parts.name,".
            "parts.instock,".
            "parts.mininstock,".
            "footprints.name AS 'footprint',".
            "storeloc.name   AS 'loc',".
            "parts.comment,".
            "parts.id_category, ".
            "parts.supplierpartnr ".
            "FROM parts ".
            "LEFT JOIN footprints ON parts.id_footprint=footprints.id ".
            "LEFT JOIN storeloc   ON parts.id_storeloc=storeloc.id ".
            "LEFT JOIN suppliers  ON parts.id_supplier=suppliers.id ".
            "WHERE FALSE ".$query_nam.$query_com.$query_sup.$query_snr.$query_loc.$query_fpr.
            " ORDER BY parts.id_category,parts.name ASC;";
        debug_print ($query."<br>");
        $result = mysql_query ($query);
    
        $rowcount = 0;  // $rowcount is used for the alternating bg colors
        $prevcat = -1;  // $prevcat remembers the previous category. -1 is
            // an invalid category id.
        while ( $d = mysql_fetch_row ($result) )
        {
            // use speaking names for results
            $id             = $d[0];
            $name           = $d[1];
            $instock        = $d[2];
            $mininstock     = $d[3];
            $footprint      = $d[4];
            $location       = $d[5];
            $comment        = $d[6];
            $id_category    = $d[7];
            $supplierpartnr = $d[8];

            /* print new header, if a diffrent category is started */
            if ($prevcat != $id_category)
            {
                // add one empty row for small spacing
                print "<tr><td></td></tr>\n";
                print "<tr>".
                    "<td class=\"tdtop\" colspan=\"9\">Treffer in der Kategorie ". show_bt($id_category) ."</td>".
                    "</tr>\n";
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
                $prevcat = $id_category;
                $rowcount = 0;
            }
            $rowcount++;
            print_table_row( $rowcount, $id, $name, $footprint, $supplierpartnr, $comment, $instock, $mininstock, $location);
        }
    ?>
</table>

</body>
</html>
