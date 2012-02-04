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

    ChangeLog
    
    07/03/06:
        Added escape/unescape stuff
*/
    include ("lib.php");
    partdb_init();
    $SearchQuerry = "";
    if(strcmp($_REQUEST["action"], "an") == 0) //add number of parts
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
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
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

        print "<form method=\"get\">";
        print "<input type=\"hidden\" name=\"cid\" value=\"0\">";
        print "<input type=\"hidden\" name=\"type\" value=\"toless\">\nLieferant(en):<select name=\"sup_id\">";

        if (! isset($_REQUEST["sup_id"]) )
            print "<option selected value=\"0\">Alle</option>";
        else
            print "<option value=\"0\">Alle</option>";
            
        $query = "SELECT id,name FROM suppliers ORDER BY name ASC;";
        $r = mysql_query ($query);
        
        $ncol = mysql_num_rows ($r);
        while ( ($d = mysql_fetch_row($r)) )
        {
        if ($d[0] == $_REQUEST["sup_id"])
        print "<option selected value=\"". smart_unescape($d[0]) ."\">". smart_unescape($d[1]) ."</option>\n";
        else
        print "<option value=\"". smart_unescape($d[0]) ."\">". smart_unescape($d[1]) ."</option>\n";
        }
        print "</select><input type=\"submit\" value=\"W&auml;hle Lieferanten!\"></form>\n";
        ?>
        </td>
    </tr>
</table>

<br>

<table class="table">
    <tr>
        <td class="tdtop">
        Zu bestellende Teile &quot;<?PHP print lookup_category_name ($_REQUEST["cid"]); ?>&quot;
        </td>
    </tr>
    <tr>
        <td class="tdtext">
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

        debug_print ($query);
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
        debug_print ($query);
        $result = mysql_query ($query);

        $rowcount = 0;
        while ( $d = mysql_fetch_row ($result) )
        {
            $rowcount++;
            print "<tr class=\"".( is_odd( $rowcount) ? 'trlist_odd': 'trlist_even')."\">";
            
            print "<td class=\"tdrow1\"><a href=\"javascript:popUp('partinfo.php?pid=". smart_unescape($d[0]) ."');\">". smart_unescape($d[1]) ."</a></td><td class=\"tdrow3\">". smart_unescape($d[2]) ."</td><td class=\"tdrow4\">". smart_unescape($d[3]) ."</td><td class=\"tdrow1\">". smart_unescape($d[4]) ."</td><td class=\"tdrow1\">". smart_unescape($d[5]) . "</td><td class=\"tdrow1\">". smart_unescape($d[8]) . "</td>";
            //show text box with number to add and the add button
            print "<td class=\"tdrow2\"><form method=\"post\">";
            print "<input type=\"hidden\" name=\"pid\" value=\"".smart_unescape($d[0])."\"/>";
            print "<input type=\"hidden\" name=\"action\"  value=\"an\"/>";
            print "<input type=\"text\" style=\"width:25px;\" name=\"toadd\" value=\"" . smart_unescape($d[3]) . "\"/>";
            print "<input type=\"submit\" value=\"Add\"/></form></td>";
            
            print "</tr>\n";
        }
        ?>
        </table>
        </td>
    </tr>
</table>
<br>

<table class="table">
<tr>
    <td class="tdtop">
    Bauteile Export
    </td>
</tr>
<tr>
    <td class="tdtext">
        <form method="post" action="">
            <table>
            <?PHP
            print "<tr class=\"trcat\"><td><input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
            print "<input type=\"hidden\" name=\"action\"  value=\"createbom\"/>";
            
            
            print "<tr class=\"trcat\"><td>";
            print "Format:</td><td><select name=\"format\">";
            PrintsFormats("format");
            print "</select>";
            print "</td></tr><tr class=\"trcat\"><td>";
            print "Trennzeichen:</td><td><input type=\"text\" name=\"spacer\" size=\"3\" value=\"";
            if ( strcmp ($_REQUEST["action"], "createbom"))
                print ";";
            else
                print $_REQUEST["spacer"];
            print "\"/></td></tr>";
            
            print "</td></tr>";
            
            print "<tr><td><input type=\"submit\" value=\"Ausführen\"/></tr></td>";
            
            print "<tr><td colspan=\"4\">";
            
            if ( strcmp ($_REQUEST["action"], "createbom") == 0 )
            {
                
                $query = $SearchQuerry;
                
                $result = mysql_query ($query);
                $nrows = mysql_num_rows($result)+6;
                
                print "<textarea name=\"sql_query\" rows=\"".$nrows."\" cols=\"40\" dir=\"ltr\" >";
                debug_print($query);
                print "______________________________\r\n";
                print "Bestell-Liste:\r\n";
                print GenerateBOMHeadline($_REQUEST["format"],$_REQUEST["spacer"]);
                while ( $d = mysql_fetch_row ($result) )
                {
                    $q = mysql_fetch_row ($quantity);
                    
                    //function GenerateBOMResult($Format,$Spacer,$PartName,$SupNr,$SupName,$Quantity,$Instock,$Price)
                    print GenerateBOMResult($_REQUEST["format"],    //$Format
                                            $_REQUEST["spacer"],    //$Spacer
                                            $d[2],                  //$PartName
                                            $d[5],                  //$SupNr
                                            $d[4],                  //$SupName
                                            $d[3],                  //$Quantity
                                            $d[6],                  //$Instock
                                            $d[7]);                 //$Price
                }
                print "</textarea>";
            }
            print "</td></tr>";
            ?>
            </table>
        </form>
    </td>
</tr>
</table>
  

 </body>
</html>
