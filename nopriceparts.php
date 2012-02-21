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
    $action = isset( $_REQUEST['action']) ? $_REQUEST['action'] : 'default';
    
    if ( strcmp( $action, "newprice") == 0)  //Set new price
    {
        $rowcount = $_REQUEST["selections"];
        while($rowcount)
        {
            if($_REQUEST["selectedpid".$rowcount] && $_REQUEST["newprice".$rowcount])
            {           
                $_REQUEST["newprice".$rowcount] = str_replace(',', '.', $_REQUEST["newprice".$rowcount]);
                /* Before adding the new price, delete the old one! */
                $query = "DELETE FROM preise WHERE part_id=". smart_escape($_REQUEST["selectedpid".$rowcount]) ." LIMIT 1;";
                mysql_query($query);
                $query = "INSERT INTO preise (part_id,ma,preis,t) VALUES (". smart_escape($_REQUEST["selectedpid".$rowcount]) .", 1, ". smart_escape($_REQUEST["newprice".$rowcount]) .", NOW());";
                mysql_query($query);
            }
            $rowcount--;
        }
    }
    

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Teile ohne Preis</title>
    <?php print_http_charset(); ?>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
    <script type="text/javascript" src="dtree.js"></script>
    <script type="text/javascript" src="popup.php"></script>
</head>
<body class="body">


<div class="outer">
    <h2>Teile ohne Preis</h2>
    <div class="inner">
        <script language="JavaScript" type="text/javascript">
        
        function validateFloat(evt) 
        {
          var theEvent = evt || window.event;
          var key = theEvent.keyCode || theEvent.which;
          key = String.fromCharCode( key );
          var regex = /[0-9]|\.|\,/;
          if( !regex.test(key) ) {
            theEvent.returnValue = false;
            if(theEvent.preventDefault) theEvent.preventDefault();
          }
        }
        </script>
        
        <form method="post" action="">
        <input type="hidden" name="action"  value="newprice"/>
        <table>
        <?PHP
        
        print "<tr class=\"trcat\">".
            "<td></td>".
            "<td>Name</td>".
            "<td>Vorh./<br>Min.Best.</td>".
            "<td>Footprint</td>".
            "<td>Lagerort</td>".
            "<td>Lieferant</td>".
            "<td>Bestell-Nr.</td>". 
            "<td align=\"center\">Preis</td>".
            "</tr>\n";

        $query = "SELECT ".
            " parts.id,".
            " parts.name,".
            " parts.instock,".
            " parts.mininstock,".
            " footprints.name AS 'footprint',".
            " storeloc.name AS 'location',".
            " suppliers.name AS 'supplier',". 
            " parts.supplierpartnr,". 
            " parts.comment".
            " FROM parts".
            " LEFT JOIN footprints ON parts.id_footprint=footprints.id".
            " LEFT JOIN storeloc ON parts.id_storeloc=storeloc.id".
            " LEFT JOIN suppliers ON parts.id_supplier=suppliers.id". 
            " LEFT JOIN preise ON parts.id=preise.part_id".
            " WHERE (preise.id IS NULL)".
            " ORDER BY name ASC;";

        $result = mysql_query( $query) or die( mysql_error());

        $rowcount = 0;
        while ( $data = mysql_fetch_assoc( $result))
        {
            $rowcount++;
            print "<tr class=\"".( is_odd( $rowcount) ? 'trlist_odd': 'trlist_even')."\">";
            
            // Pictures
            print "<td class=\"tdrow0\">";
            print_table_image( $data['id'], $data['name'], $data['footprint']);
            print "</td>\n";
            
            print "<td class=\"tdrow1\"><a title=\"Kommentar: " . htmlspecialchars( smart_unescape( $data['comment']));
            print "\" href=\"javascript:popUp('partinfo.php?pid=". smart_unescape( $data['id']) ."');\">". smart_unescape($data['name']) ."</a></td>";
            print "<td class=\"tdrow1\">". smart_unescape( $data['instock']) ."/". smart_unescape( $data['mininstock']) ."</td>";
            print "<td class=\"tdrow1\">". smart_unescape( $data['footprint']) ."</td>";
            print "<td class=\"tdrow1\">". smart_unescape( $data['location']) ."</td>";
            print "<td class=\"tdrow1\">". smart_unescape( $data['supplier']) ."</td>"; 
            print "<td class=\"tdrow1\">". smart_unescape( $data['supplierpartnr']) ."</td>"; 
            
            
            //Show a text box to add new price
            print "<td class=\"tdrow1\">";
            print "<input type=\"hidden\" name=\"selectedpid".$rowcount."\" value=\"" . smart_unescape( $data['id']). "\"/>";
            print "<input type=\"text\" size=\"3\" onkeypress=\"validateFloat(event)\" name=\"newprice".$rowcount."\" value=\"0\"/>";
            print "</td>";
            print "</tr>\n";
        }
        
        print "</table>";
        print "<input type=\"hidden\" name=\"selections\"  value=\"".$rowcount."\">";
        ?>
        <input type="submit" value="Hinzuf&uuml;gen">
        </form>
    </div>
</div>

</body>
</html>
