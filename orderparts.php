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

    $Id: $

*/
    include ("lib.php");
    partdb_init();
    
    
    // set action to default, if not exists
    $action       = ( isset( $_REQUEST['action'])    ? $_REQUEST['action']   : 'default');
    $cid          = ( isset( $_REQUEST['cid'])       ? $_REQUEST['cid']      : '');
    $sup_id       = ( isset( $_REQUEST['sup_id'])    ? $_REQUEST['sup_id']   : '');
    $deviceid     = ( isset( $_REQUEST['deviceid)']) ? $_REQUEST['deviceid'] : '');
    $SearchQuerry = "";

    if(strcmp( $action, "an") == 0) //add number of parts
    {
        $query = "UPDATE parts SET instock=instock+". smart_escape($_REQUEST["toadd"]) ." WHERE id=". smart_escape($_REQUEST["pid"]) ." LIMIT 1;";
        debug_print($query);
        mysql_query($query);
    }

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Teileansicht</title>
    <?php print_http_charset(); ?>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
    <script type="text/javascript" src="dtree.js"></script>
    <script type="text/javascript" src="popup.php"></script>
</head>
<body class="body">

<div class="outer">
    <h2>Sonstiges</h2>
    <div class="inner">
        <form method="get" action="">
            <input type="hidden" name="cid" value="0">
            <input type="hidden" name="type" value="toless">
            Lieferant(en):
            <select name="sup_id">
            <?php
                $selected = (! isset($_REQUEST["sup_id"])) ? 'selected': '';
                print "<option ". $selected ." value=\"0\">Alle</option>";
                suppliers_build_list( $sup_id);
            ?>
            </select>
            <input type="submit" value="W&auml;hle Lieferanten!">
        </form>
    </div>
</div>


<div class="outer">
    <h2>Zu bestellende Teile &quot;<?PHP print category_get_name( $cid); ?>&quot;</h2>
    <div class="inner">
        <table>
        <?PHP
        
        /*
         * All supplier IDs are positive integers, thus 0 (which
         * stands for "all suppliers") is no valid supplier ID!
         * Show the entire list.
         */
        if ( (! isset($_REQUEST["sup_id"]) ) || ($_REQUEST["sup_id"] == "0") )
        {
            $query = "SELECT SUM((parts.mininstock-parts.instock)*preise.preis) FROM parts LEFT JOIN preise ON parts.id=preise.part_id WHERE (parts.instock < parts.mininstock);";
        }
        else
        {
            $query = "SELECT SUM((parts.mininstock-parts.instock)*preise.preis) FROM parts LEFT JOIN preise ON parts.id=preise.part_id WHERE (parts.instock < parts.mininstock) AND (parts.id_supplier=". smart_escape($_REQUEST["sup_id"]) .");";
        }

        $result = mysql_query ($query);
        $d = mysql_fetch_row ($result);
        include("config.php");
        print "<tr><td colspan=\"4\">Wert der zu bestellenden Artikel: ".$d[0]." ".$currency."</td></tr>";

        /****/
        print "<tr class=\"trcat\"><td>Name</td><td>Footprint</td><td>Bestellmenge</td><td>Lieferant</td><td>Bestell-Nr.</td><td>Lagerort</td><td>Hinzuf&uuml;gen</td></tr>";
        if ( (! isset($_REQUEST["sup_id"]) ) || ($_REQUEST["sup_id"] == "0") )
        {
            $query = 
                "SELECT ".
                "parts.id,".
                "parts.name,".
                "footprints.name AS 'footprint',".
                "parts.mininstock-parts.instock AS 'diff',".
                "suppliers.name AS 'supplier',".
                "parts.supplierpartnr,".
                "parts.instock,parts.mininstock,".
                "storeloc.name AS 'loc',".
                "preise.preis".
                " FROM parts ".
                " LEFT JOIN footprints ON parts.id_footprint=footprints.id".
                " LEFT JOIN suppliers ON parts.id_supplier=suppliers.id".
                " LEFT JOIN pending_orders ON parts.id=pending_orders.part_id".
                " LEFT JOIN storeloc ON parts.id_storeloc=storeloc.id".
                " LEFT JOIN preise ON (preise.part_id = parts.id)".
                " WHERE (pending_orders.id IS NULL) AND (parts.instock < parts.mininstock) ".
                "UNION ".
                "SELECT ".
                "parts.id,".
                "parts.name,".
                "footprints.name AS 'footprint',".
                "parts.mininstock-parts.instock-SUM(pending_orders.quantity),".
                "suppliers.name AS 'supplier',".
                "parts.supplierpartnr,".
                "parts.instock,parts.mininstock,".
                "storeloc.name AS 'loc',".
                "preise.preis".
                " FROM parts ".
                " INNER JOIN pending_orders ON (parts.id=pending_orders.part_id)".
                " LEFT JOIN footprints ON parts.id_footprint=footprints.id".
                " LEFT JOIN suppliers ON parts.id_supplier=suppliers.id".
                " LEFT JOIN storeloc ON parts.id_storeloc=storeloc.id".
                " LEFT JOIN preise ON (preise.part_id = parts.id)".
                "GROUP BY (pending_orders.part_id) ".
                "HAVING (parts.instock + SUM(pending_orders.quantity)  < parts.mininstock) ".
                "ORDER BY name ASC ";
        }
        else
        {
            $query = "SELECT ".
            "parts.id,".
            "parts.name,".
            "footprints.name AS 'footprint',".
            "parts.mininstock-parts.instock AS 'diff',".
            "suppliers.name AS 'supplier',".
            "parts.supplierpartnr,".
            "parts.instock,".
            "parts.mininstock,".
            "storeloc.name AS 'loc',".
            "preise.preis".
            " FROM parts".
            " LEFT JOIN footprints ON parts.id_footprint=footprints.id".
            " LEFT JOIN suppliers ON parts.id_supplier=suppliers.id".
            " LEFT JOIN pending_orders ON parts.id = pending_orders.part_id".
            " LEFT JOIN storeloc ON parts.id_storeloc=storeloc.id".
            " LEFT JOIN preise ON (preise.part_id = parts.id)".
            " WHERE (pending_orders.id IS NULL) AND (parts.instock < parts.mininstock) AND (parts.id_supplier = ". smart_escape($_REQUEST["sup_id"]) .")". 
            " UNION".
            " SELECT ".
            "parts.id,".
            "parts.name,". 
            "footprints.name AS 'footprint',". 
            "parts.mininstock - parts.instock - SUM( pending_orders.quantity ) ,". 
            "suppliers.name AS 'supplier',". 
            "parts.supplierpartnr," .
            "parts.instock,". 
            "parts.mininstock,".
            "storeloc.name AS 'loc',".
            "preise.preis".
            " FROM parts ".
            " INNER JOIN pending_orders ON ( parts.id = pending_orders.part_id ) ". 
            " LEFT JOIN footprints ON parts.id_footprint = footprints.id ".
            " LEFT JOIN suppliers ON parts.id_supplier = suppliers.id ".
            " LEFT JOIN storeloc ON parts.id_storeloc=storeloc.id".
            " LEFT JOIN preise ON (preise.part_id = parts.id)".
            " WHERE (parts.id_supplier = ". smart_escape($_REQUEST["sup_id"]) .") GROUP BY (pending_orders.part_id) HAVING (parts.instock + SUM( pending_orders.quantity ) < parts.mininstock) ORDER BY name ASC;";
        }
        $SearchQuerry = $query;
        $result = mysql_query ($query);

        $rowcount = 0;
        while ( $d = mysql_fetch_row ($result) )
        {
            $rowcount++;
            print "<tr class=\"".( is_odd( $rowcount) ? 'trlist_odd': 'trlist_even')."\">";
            
            print "<td class=\"tdrow1\"><a href=\"javascript:popUp('partinfo.php?pid=". smart_unescape($d[0]) ."');\">". smart_unescape($d[1]) ."</a></td><td class=\"tdrow3\">". smart_unescape($d[2]) ."</td><td class=\"tdrow4\">". smart_unescape($d[3]) ."</td><td class=\"tdrow1\">". smart_unescape($d[4]) ."</td><td class=\"tdrow1\">". smart_unescape($d[5]) . "</td><td class=\"tdrow1\">". smart_unescape($d[8]) . "</td>\n";
            //show text box with number to add and the add button
            print "<td class=\"tdrow2\"><form method=\"post\" action=\"\">\n";
            print "<input type=\"hidden\" name=\"pid\" value=\"".smart_unescape($d[0])."\"/>\n";
            print "<input type=\"hidden\" name=\"action\"  value=\"an\"/>\n";
            print "<input type=\"text\" style=\"width:25px;\" name=\"toadd\" value=\"" . smart_unescape($d[3]) . "\"/>\n";
            print "<input type=\"submit\" value=\"Add\"/></form></td>\n";
            
            print "</tr>\n";
        }
        ?>
        </table>
    </div>
</div>


<div class="outer">
    <h2>Bauteile Export</h2>
    <div class="inner">
        <form method="post" action="">
            <table>
            <?PHP
            print "<tr class=\"trcat\">\n".
                "<td>".
                "<input type=\"hidden\" name=\"deviceid\" value=\"" .$deviceid. "\">".
                "<input type=\"hidden\" name=\"action\"  value=\"createbom\">";
            
            print "Format:".
                "</td>\n".
                "<td><select name=\"format\">";
                PrintsFormats("format");
            print "</select>".
                "</td>\n".
                "</tr>\n";

            print "<tr class=\"trcat\">".
                "<td>Trennzeichen:</td>\n".
                "<td><input type=\"text\" name=\"spacer\" size=\"3\" value=\"";
                if ( strcmp ($action, "createbom"))
                    print ";";
                else
                    print $_REQUEST["spacer"];
            print "\"\n>".
                "</td>\n</tr>\n";
            
            print "<tr>\n".
                "<td><input type=\"submit\" value=\"Ausführen\"/></td>\n".
                "</tr>\n";
            
            print "<tr>\n".
                "<td colspan=\"4\">";
            
            if ( strcmp ($action, "createbom") == 0 )
            {
                
                $query  = $SearchQuerry;
                
                $result = mysql_query( $query);
                $nrows  = mysql_num_rows( $result) + 6;
                
                print "<textarea name=\"sql_query\" rows=\"". $nrows ."\" cols=\"40\" dir=\"ltr\" >";
                print "______________________________".PHP_EOL;
                print "Bestell-Liste:".PHP_EOL;
                print GenerateBOMHeadline( $_REQUEST["format"], $_REQUEST["spacer"]);
                while ( $data = mysql_fetch_row( $result))
                {
                    //function GenerateBOMResult($Format,$Spacer,$PartName,$SupNr,$SupName,$Quantity,$Instock,$Price)
                    print GenerateBOMResult($_REQUEST["format"],    //$Format
                                            $_REQUEST["spacer"],    //$Spacer
                                            $data[1],               //$PartName
                                            $data[5],               //$SupNr
                                            $data[4],               //$SupName
                                            $data[3],               //$Quantity
                                            $data[6],               //$Instock
                                            $data[7]);              //$Price
                }
                print "</textarea>";
            }
            print "</td>\n".
                "</tr>\n";
            ?>
            </table>
        </form>
    </div>
</div>
  

</body>
</html>
