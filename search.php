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

    06/03/06    Added escape/unescape calls
    05/12/09    Edit Parts over Popup (k.jacobs)
*/
    include('lib.php');
    partdb_init();

    // set action
    $action = isset( $_REQUEST['action']) ? $_REQUEST['action'] : 'default';

    // catch variables
    $pid        = isset( $_REQUEST['pid'])        ? $_REQUEST['pid'] : '';
    $keyword    = isset( $_REQUEST['keyword'])    ? $_REQUEST['keyword'] : '';
    $search_nam = isset( $_REQUEST['search_nam']) ? $_REQUEST['search_nam'] == 'true' : false;
    $search_com = isset( $_REQUEST['search_com']) ? $_REQUEST['search_com'] == 'true' : false;
    $search_sup = isset( $_REQUEST['search_sup']) ? $_REQUEST['search_sup'] == 'true' : false;
    $search_snr = isset( $_REQUEST['search_snr']) ? $_REQUEST['search_snr'] == 'true' : false;
    $search_loc = isset( $_REQUEST['search_loc']) ? $_REQUEST['search_loc'] == 'true' : false;
    $search_fpr = isset( $_REQUEST['search_fpr']) ? $_REQUEST['search_fpr'] == 'true' : false;

    
    // remove one part
    if ( $action == 'dec')
    {
        parts_stock_decrease( $pid);
    }

    // add one part
    if ( $action == 'inc')
    {
        parts_stock_increase( $pid);
    }
   

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
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

<div class="outer">
    <h2>Suchergebnis</h2>
    <div class="inner">
        Sie suchten nach &quot;<?PHP print $keyword; ?>&quot;
        
        <div style="float: right; display: inline;">
            <form action="export.php" method="post" style="display: inline;">
                <select name="format" size="1">
                    <option>XML</option>
                    <option>CSV</option>
                    <option>DokuWIKI</option>
                    <option>DymoCSV</option>
                </select>
                <?php
                    print "<input type='hidden' name='keyword' value='". $keyword ."'>\n";
                    if ( $search_nam) { print "<input type='hidden' name='search_nam' value='true'>\n"; }
                    if ( $search_com) { print "<input type='hidden' name='search_com' value='true'>\n"; } 
                    if ( $search_sup) { print "<input type='hidden' name='search_sup' value='true'>\n"; } 
                    if ( $search_snr) { print "<input type='hidden' name='search_snr' value='true'>\n"; } 
                    if ( $search_loc) { print "<input type='hidden' name='search_loc' value='true'>\n"; } 
                    if ( $search_fpr) { print "<input type='hidden' name='search_fpr' value='true'>\n"; } 
                 ?>
                <input type="submit" name="action" value="Export">
            </form>
        </div>
        <div class="clear"></div>
    </div>
</div>



<div class="outer">
    <div class="inner">
        <table>
        <?php
            // execute the SQL query
            $keyword_esc = smart_escape_for_search( $keyword);

            // build search strings
            $query_nam = ( $search_nam) ? " OR (parts.name LIKE ".           $keyword_esc.")" : "";
            $query_com = ( $search_com) ? " OR (parts.comment LIKE ".        $keyword_esc.")" : ""; 
            $query_sup = ( $search_sup) ? " OR (suppliers.name LIKE ".       $keyword_esc.")" : ""; 
            $query_snr = ( $search_snr) ? " OR (parts.supplierpartnr LIKE ". $keyword_esc.")" : ""; 
            $query_loc = ( $search_loc) ? " OR (storeloc.name LIKE ".        $keyword_esc.")" : ""; 
            $query_fpr = ( $search_fpr) ? " OR (footprints.name LIKE ".      $keyword_esc.")" : ""; 
            $search = $query_nam. $query_com. $query_sup. $query_snr. $query_loc. $query_fpr;
            $query = 
                "SELECT ".
                "parts.id,".
                "parts.name,".
                "parts.instock,".
                "parts.mininstock,".
                "footprints.name AS 'footprint',".
                "storeloc.name   AS 'location',".
                "parts.comment,".
                "parts.id_category, ".
                "parts.supplierpartnr ".
                "FROM parts ".
                "LEFT JOIN footprints ON parts.id_footprint=footprints.id ".
                "LEFT JOIN storeloc   ON parts.id_storeloc=storeloc.id ".
                "LEFT JOIN suppliers  ON parts.id_supplier=suppliers.id ".
                "WHERE FALSE ". $search.
                " ORDER BY parts.id_category, parts.name ASC;";
            $result = mysql_query( $query) or die( mysql_error());
        
            $rowcount = 0;  // $rowcount is used for the alternating bg colors
            $prevcat = -1;  // $prevcat remembers the previous category. -1 is
                            // an invalid category id.

            while ( $data_array = mysql_fetch_assoc( $result))
            {
                /* print new header, 
                   if a diffrent category is started */
                if ( $prevcat != $data_array['id_category'])
                {
                    // add one empty row for small spacing
                    print "<tr><td></td></tr>\n";
                    print "<tr>".
                        "<td class=\"tdtop\" colspan=\"9\">Treffer in der Kategorie ". show_bt( $data_array['id_category']) ."</td>".
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
                    $prevcat = $data_array['id_category'];
                    $rowcount = 0;
                }

                $rowcount++;
                print_table_row( $rowcount, $data_array);
            }
        ?>
        </table>
    </div>
</div>

</body>
</html>
