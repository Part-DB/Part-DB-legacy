<?php
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
    include ("config.php");
    partdb_init();
    
    
    // set action to default, if not exists
    $action       = ( isset( $_REQUEST['action'])    ? $_REQUEST['action']   : 'default');
    $cid          = ( isset( $_REQUEST['cid'])       ? $_REQUEST['cid']      : '');
    $sup_id       = ( isset( $_REQUEST['sup_id'])    ? $_REQUEST['sup_id']   : 0);
    $deviceid     = ( isset( $_REQUEST['deviceid)']) ? $_REQUEST['deviceid'] : '');

    if ( strcmp( $action, "an") == 0) //add number of parts
    {
        parts_stock_increase( $_REQUEST["pid"], $_REQUEST["toadd"]);
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
    <h2>Zu bestellende Teile &quot;<?php print category_get_name( $cid); ?>&quot;</h2>
    <div class="inner">
        <table>
        <?php
        
        /*
         * All supplier IDs are positive integers, thus 0 (which
         * stands for "all suppliers") is no valid supplier ID!
         * Show the entire list.
         */
        $order_value = parts_order_sum( $sup_id);
        include("config.php");
        print "<tr><td colspan=\"4\">Wert der zu bestellenden Artikel: ". $order_value ." ".$currency."</td></tr>". PHP_EOL;

        ?>
        <tr class="trcat">
            <td></td>
            <td>Name</td>
            <?php if (! $disable_footprints) { ?>
                <td>Footprint</td>
            <?php } ?>
            <td>Bestellmenge</td>
            <td>Lieferant</td>
            <td>Bestell-Nr.</td>
            <td>Lagerort</td>
            <td>Hinzuf&uuml;gen</td>
        </tr>
        <?php
        $result = parts_select_order( $sup_id); 

        $rowcount = 0;
        while ( $data = mysql_fetch_assoc( $result))
        {
            $rowcount++;
            ?>
            <tr class="<?php print is_odd( $rowcount) ? 'trlist_odd': 'trlist_even'; ?>">
            
            <td class="tdrow0"><?php print_table_image( $data['id'], $data['name'], $data['footprint_filename']); ?></td>
            <td class="tdrow1">
                <a href="javascript:popUp('partinfo.php?pid=<?php print smart_unescape( $data['id']); ?>');"><?php print smart_unescape( $data['name']); ?></a></td>
            <?php if (! $disable_footprints) { ?>
                <td class="tdrow3"><?php print smart_unescape( $data['footprint']); ?></td>
            <?php } ?>
            <td class="tdrow4"><?php print smart_unescape( $data['diff']); ?></td>
            <td class="tdrow1"><?php print smart_unescape( $data['supplier']); ?></td>
            <td class="tdrow1"><?php print smart_unescape( $data['supplierpartnr']); ?></td>
            <td class="tdrow1"><?php print smart_unescape( $data['loc']); ?></td>
            <?php //show text box with number to add and the add button ?>
            <td class="tdrow2"><form method="post" action="">
            <input type="hidden" name="pid" value="<?php print smart_unescape( $data['id']); ?>">
            <input type="hidden" name="action" value="an">
            <input type="text" style="width:25px;" name="toadd" value="<?php print smart_unescape( $data['diff']); ?>">
            <input type="submit" value="Add"></form></td>
            
            </tr>
            <?php
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
            <?php
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
                "<td><input type=\"submit\" value=\"Ausf&uuml;hren\"/></td>\n".
                "</tr>\n";
            
            print "<tr>\n".
                "<td colspan=\"4\">";
            
            if ( strcmp ($action, "createbom") == 0 )
            {
                
                $result = parts_select_order( $sup_id); 
                $nrows  = mysql_num_rows( $result) + 6;
                
                print "<textarea name=\"bom\" rows=\"". $nrows ."\" cols=\"50\" dir=\"ltr\" >". PHP_EOL;
                print "______________________________".PHP_EOL;
                print "Bestell-Liste:". PHP_EOL;
                print GenerateBOMHeadline( $_REQUEST["format"], $_REQUEST["spacer"]);
                while ( $data = mysql_fetch_assoc( $result))
                {
                    //function GenerateBOMResult($Format,$Spacer,$PartName,$SupNr,$SupName,$Quantity,$Instock,$Price)
                    print GenerateBOMResult($_REQUEST["format"],     //$Format
                                            $_REQUEST["spacer"],     //$Spacer
                                            $data['name'],           //$PartName
                                            $data['supplierpartnr'], //$SupNr
                                            $data['supplier'],       //$SupName
                                            $data['diff'],           //$Quantity
                                            $data['instock'],        //$Instock
                                            $data['price']);         //$Price
                }
                print "</textarea>". PHP_EOL;
            }
            print "</td>". PHP_EOL;
            print "</tr>". PHP_EOL;
            ?>
            </table>
        </form>
    </div>
</div>
  

</body>
</html>
