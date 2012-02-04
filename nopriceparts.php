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
    
    if(strcmp($_REQUEST["action"], "newprice") == 0)  //Set new price
    {
        $rowcount = $_REQUEST["selections"];
        while($rowcount)
        {
            if($_REQUEST["selectedpid".$rowcount] && $_REQUEST["newprice".$rowcount])
            {           
                $_REQUEST["newprice".$rowcount] = str_replace(',', '.', $_REQUEST["newprice".$rowcount]);
                /* Before adding the new price, delete the old one! */
                $query = "DELETE FROM preise WHERE part_id=". smart_escape($_REQUEST["selectedpid".$rowcount]) ." LIMIT 1;";
                debug_print($query);
                mysql_query($query);
                $query = "INSERT INTO preise (part_id,ma,preis,t) VALUES (". smart_escape($_REQUEST["selectedpid".$rowcount]) .", 1, ". smart_escape($_REQUEST["newprice".$rowcount]) .", NOW());";
                debug_print($query);
                mysql_query($query);
            }
            $rowcount--;
        }
    }
    

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Teile ohne Preis</title>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
    <script type="text/javascript" src="dtree.js"></script>
</head>
<body class="body">


<table class="table">
    <tr>
        <td class="tdtop">
        Teile ohne Preis
        </td>
    </tr>
    <tr>
        <td class="tdtext">
        <script language="JavaScript" type="text/javascript">
        <!--
        function popUp(URL)
        {
        d = new Date();
        id = d.getTime();
        eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=1, scrollbars=1, location=1, statusbar=1, menubar=1, resizable=1, width=600, height=400');");
        }
        
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
        // -->
        </script>
        
        <form method="post" action="">
        <input type="hidden" name="action"  value="newprice"/>
        <table>
        <?PHP
        

        print "<tr class=\"trcat\"><td></td> <td>Name</td> <td>Vorh./<br>Min.Best.</td> <td>Footprint</td> <td>Lagerort</td> <td align=\"center\">Preis</td></tr>\n";

        $query = 
        "SELECT ".
        "parts.id,".
        "parts.name,".
        "parts.instock,".
        "parts.mininstock,".
        "footprints.name AS 'footprint',".
        "storeloc.name AS 'loc',".
        "parts.comment ".
        "FROM parts ".
        "LEFT JOIN footprints ON parts.id_footprint=footprints.id ".
        "LEFT JOIN storeloc ON parts.id_storeloc=storeloc.id ".
        "LEFT JOIN preise ON parts.id=preise.part_id ".
        "WHERE (preise.id IS NULL) ".
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
                print "<td class=\"tdrow0\"><a href=\"javascript:popUp('getimage.php?pid=". smart_unescape($d[0]) . "')\"><img class=\"catbild\" src=\"getimage.php?pid=". smart_unescape($d[0]) . "\" alt=\"". smart_unescape($d[1]) ."\"></a></td>";
            }
            else
            {
                //Footprintbilder
                if(is_file("tools/footprints/" . smart_unescape($d[4]) . ".png"))
                {
                print "<td class=\"tdrow0\"><a href=\"javascript:popUp('tools/footprints/". smart_unescape($d[4]) . ".png')\"><img class=\"catbild\" src=\"tools/footprints/". smart_unescape($d[4]) .".png\" alt=\"\"></a></td>";
                }
                else
                {
                print "<td class=\"tdrow0\"><img class=\"catbild\" src=\"img/partdb/dummytn.png\" alt=\"\"></td>";
                }
            }
            print "<td class=\"tdrow1\"><a title=\"Kommentar: " . htmlspecialchars( smart_unescape($d[6]));
            print "\" href=\"javascript:popUp('partinfo.php?pid=". smart_unescape($d[0]) ."');\">". smart_unescape($d[1]) ."</a></td>";
            print "<td class=\"tdrow1\">". smart_unescape($d[2]) ."/". smart_unescape($d[3]) ."</td>";
            print "<td class=\"tdrow1\">". smart_unescape($d[4]) ."</td>";
            print "<td class=\"tdrow1\">". smart_unescape($d[5]) . "</td>";
            
            
            //Show a text box to add new price
            print "<td class=\"tdrow1\">";
            print "<input type=\"hidden\" name=\"selectedpid".$rowcount."\" value=\"" . smart_unescape($d[0]). "\"/>";
            print "<input type=\"text\" size=\"3\" onkeypress=\"validateFloat(event)\" name=\"newprice".$rowcount."\" value=\"0\"/>";
            print "</td>";
            print "</tr>\n";
        }
        
        print "<input type=\"hidden\" name=\"selections\"  value=\"".$rowcount."\"/>";
        ?>
        </table>
        <input type="submit" value="Hinzufügen"/>
        </form>
        </td>
    </tr>
</table>

 </body>
</html>
