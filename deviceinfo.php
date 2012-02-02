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
    $showsearchedparts = 0;
    $notallinstock = 0;
    $bookstate = 0;
    $bookerrorstring = "";
	$refreshnav = 0;
	
    if ( strcmp ($_REQUEST["action"], "assignbytext") == 0 )
    {

        $query = "SELECT id FROM parts WHERE name=". smart_escape($_REQUEST["newpartname"]) .";";
        debug_print ($query);
        $result = mysql_query ($query);
        $nParts = mysql_num_rows($result);
        if( $nParts == 1)
        {
            //Check if part is allready assigned
            $partid = mysql_fetch_row ($result);
            $query = "SELECT * FROM part_device WHERE id_part=". smart_escape($partid[0]) ." AND id_device=".smart_escape($_REQUEST["deviceid"]).";";
            debug_print ($query);
            $result = mysql_query ($query);
            $nDevices = mysql_num_rows($result);
            if( $nDevices == 0)
            {
                //now add a part to the device          
                $query = "INSERT INTO part_device (id_part,id_device,quantity) VALUES (". smart_escape($partid[0]) .",". smart_escape($_REQUEST["deviceid"]) .",1);";
                debug_print ($query);
                mysql_query ($query);
            }
            else
            {
                //Increment the part quantity
                $query = "UPDATE part_device SET quantity=quantity+1 WHERE id_part=" . smart_escape($partid[0]) . " AND id_device=".smart_escape($_REQUEST["deviceid"]).";";
                debug_print($query);
                mysql_query($query);
            }
        }
        else
        {
            $showsearchedparts = 1;
        }
    }
    else if ( strcmp ($_REQUEST["action"], "assignbyselected") == 0 )
    {
        $rowcount = $_REQUEST["selections"];
        while($rowcount)
        {
            if($_REQUEST["selectedid".$rowcount] && $_REQUEST["selectedquantity".$rowcount])
            {
                $query = "INSERT INTO part_device (id_part,id_device,quantity,mountname) VALUES (". smart_escape($_REQUEST["selectedid".$rowcount]) .",". smart_escape($_REQUEST["deviceid"]) .",".smart_escape($_REQUEST["selectedquantity".$rowcount]).",".smart_escape($_REQUEST["mounttext".$rowcount]).");";
                debug_print ($query);
                mysql_query ($query);
            }
            $rowcount--;
        }
    }
    else if ( strcmp ($_REQUEST["action"], "assign") == 0 )
    {
        //Increment the part quantity
        $query = "UPDATE part_device SET quantity=quantity+1 WHERE id_part=" . smart_escape($_REQUEST["partid"]) . " AND id_device=".smart_escape($_REQUEST["deviceid"]).";";
        debug_print($query);
        mysql_query($query);
    }
    else if ( strcmp ($_REQUEST["action"], "deassign") == 0 )
    {
        $query = "UPDATE part_device SET quantity=quantity-1 WHERE id_part=" . smart_escape($_REQUEST["partid"]) . " AND id_device=".smart_escape($_REQUEST["deviceid"]).";";
        debug_print($query);
        mysql_query($query);
    }
    else if ( strcmp ($_REQUEST["action"], "remove") == 0 )
    {
        $query = "DELETE FROM part_device WHERE id_part=" . smart_escape($_REQUEST["partid"]) . " AND id_device=".smart_escape($_REQUEST["deviceid"]).";";
        debug_print($query);
        mysql_query($query);
    }
    else if ( strcmp ($_REQUEST["action"], "bookparts") == 0 )
    {
        //First check if enough parts are in stock
        $query = "SELECT parts.instock, part_device.quantity, parts.name FROM parts JOIN part_device ON part_device.id_part = parts.id WHERE part_device.id_device = ".$_REQUEST["deviceid"].";";
        debug_print ($query);
        $result = mysql_query ($query);
        debug_print ($result);
        
        $enoughinstock = 0;
        $bookstate = 2; //no parts in device
        if(mysql_num_rows($result)>0)
            $enoughinstock = 1;
            
        while ( $d = mysql_fetch_row ($result) )
        {
            $needed = $d[1]*$_REQUEST["bookmultiplikator"];
            if($d[0] < $needed)
            {
                $enoughinstock = 0;
                $bookstate = 3; //not enough parts in stock
                $bookerrorstring = $bookerrorstring.$d[2]." Benötigt: ".$needed." Im Lager: ".$d[0]."<br>";
            }
        }       
        if($enoughinstock)
        {
            $query = "UPDATE parts JOIN part_device ON part_device.id_part = parts.id SET parts.instock = parts.instock - (part_device.quantity*".
            $_REQUEST["bookmultiplikator"].") ".
            "WHERE part_device.id_device = ".$_REQUEST["deviceid"].";";
            debug_print ($query);
            $result = mysql_query ($query);
            if($result)
                $bookstate = 1; //success
            else
                $bookstate = 4; //querry error
            debug_print ($result);
        }
    }
    else if( strcmp ($_REQUEST["action"], "setmountname") == 0 )
    {
        //Increment the part quantity
        $query = "UPDATE part_device SET mountname=".smart_escape($_REQUEST["newmountname"])." WHERE id_part=" . smart_escape($_REQUEST["partid"]) . " AND id_device=".smart_escape($_REQUEST["deviceid"]).";";
        debug_print($query);
        mysql_query($query);
    }
	else if( strcmp ($_REQUEST["action"], "renamedevice") == 0 )
	{
		$query = "UPDATE devices SET name=".smart_escape($_REQUEST["newdevname"])." WHERE id=" . smart_escape($_REQUEST["deviceid"]).";";
		debug_print($query);
        mysql_query($query);
		$refreshnav = 1;
	}
	else if( strcmp ($_REQUEST["action"], "copydevice") == 0 )
	{
		//Create a new device and get the ID
		$query = "INSERT INTO devices (name) VALUES (". smart_escape($_REQUEST["newcopydevname"]) .");";
        debug_print ($query);
        $r = mysql_query ($query);
		$newid = mysql_insert_id();
			
		//Get the parts
		$query = "SELECT part_device.id_part, part_device.quantity, part_device.mountname ".
        "FROM part_device ".
        "WHERE id_device = ".smart_escape($_REQUEST["deviceid"]).";";
		debug_print ($query);
        $r = mysql_query ($query);
		
		//Insert the parts
		while ( $d = mysql_fetch_row ($r) )
		{
			$query = "INSERT INTO part_device (id_part,quantity,mountname,id_device) VALUES (".smart_escape($d[0]).",".smart_escape($d[1]).",".smart_escape($d[2]).",".smart_escape($newid).");";
			debug_print ($query);
			mysql_query ($query);
		}
		$refreshnav = 1;
	}
	else if( strcmp ($_REQUEST["action"], "import") == 0 )
	{
		if (isset($_REQUEST["import_data"])) {
			$lines = preg_split("/\r\n/", $_REQUEST["import_data"]);
			foreach ($lines as $key => $value){
			  $rows = $lines = preg_split("/;/", $value);
			  $rowvalid = 1;
			  $addquery = "INSERT INTO part_device (id_part,quantity,mountname,id_device) VALUES (";
			  foreach ($rows as $keyrow => $rowvalue){
			  if($keyrow == 0)	//ID
			  {
					if(!is_numeric($rowvalue))
					{
						$rowvalid = 0;
					}
					$addquery = $addquery.smart_escape($rowvalue).",";
				}
				else if($keyrow == 1)	//Quantity
				{
					if(!is_numeric($rowvalue))
					{
						$rowvalid = 0;
					}
					$addquery = $addquery.smart_escape($rowvalue).",";
				}
				else if($keyrow == 2)	//mounting text
				{
					$addquery = $addquery.smart_escape($rowvalue).",";
				}
			  }
			  $addquery = $addquery.smart_escape($_REQUEST["deviceid"]).");";
			  if($rowvalid == 1)
			  {
				debug_print ($addquery);
				mysql_query ($addquery);
			  }
			}
		}
	}
 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Deviceinfo</title>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
</head>
<body class="body">

<table class="table">
    
    <tr>
        <td class="tdtop">
        Teile per Name zuordnen
        </td>
    </tr>
    <tr>
        <td class="tdtext">
            <?PHP
            print "<form method=\"post\" action=\"\">";
            print "<input type=\"text\" name=\"newpartname\"/>";
            print "<input type=\"hidden\" name=\"action\"  value=\"assignbytext\"/>";
            print "<input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
            print "<input type=\"submit\" value=\"Hinzufügen\"/></form>";
            
            print "<form method=\"post\" action=\"\">";
            print "<input type=\"hidden\" name=\"action\"  value=\"refresh\"/>";
            print "<input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
            print "<input type=\"submit\" value=\"Aktualisieren\"/></form>";
            ?>
        </td>
        </tr>
        
    <?PHP
    if($showsearchedparts == 1)
    {   
        print "<tr>";
        print "<td class=\"tdtext\">";
        print "<form method=\"post\" action=\"\">";
        print "<input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
        print "<input type=\"hidden\" name=\"action\"  value=\"assignbyselected\"/>";
        print "<table>";
        $kw = '\'%'. mysql_escape_string($_REQUEST['newpartname']) .'%\'';
        $query = "SELECT parts.name, parts.comment, parts.id, footprints.name, parts.instock FROM ".
        "parts JOIN footprints ON (footprints.id = parts.id_footprint) ".
        "WHERE parts.name LIKE ".$kw.
        " AND parts.id NOT IN(SELECT part_device.id_part FROM part_device WHERE part_device.id_device=".$_REQUEST["deviceid"].");";
        debug_print ($query);
        $result = mysql_query ($query);
        $nParts = mysql_num_rows($result);
        $rowcount = 0;
        print "<tr class=\"trcat\"><td></td><td>Anzahl</td><td>Bestückungs<br>Daten</td><td>Teil</td><td>Footprint</td><td>Lagernd</td>\n";
        while ( $d = mysql_fetch_row ($result) )
        {
        $q = mysql_fetch_row ($quantity);
        $rowcount++;
        if ( ($rowcount & 1) == 0 )
            print "<tr class=\"trlist1\">";
        else
            print "<tr class=\"trlist2\">";
        
        if (has_image($d[2]))
        {
            print "<td class=\"tdrow0\"><a href=\"javascript:popUp('getimage.php?pid=". smart_unescape($d[2]) . "')\"><img class=\"catbild\" src=\"getimage.php?pid=". smart_unescape($d[2]) . "\" alt=\"". smart_unescape($d[0]) ."\"></a></td>";
        }
        else
        {
            //Footprintbilder
            if(is_file("tools/footprints/" . smart_unescape($d[3]) . ".png"))
            {
            print "<td class=\"tdrow0\"><a href=\"javascript:popUp('tools/footprints/". smart_unescape($d[3]) . ".png')\"><img class=\"catbild\" src=\"tools/footprints/". smart_unescape($d[3]) .".png\" alt=\"\"></a></td>";
            }
            else
            {
            print "<td class=\"tdrow0\"><img class=\"catbild\" src=\"img/partdb/dummytn.png\" alt=\"\"></td>";
            }
        }
            
        print "<td class=\"tdrow1\" >";
        print "<input type=\"hidden\" name=\"selectedid".$rowcount."\" value=\"" . smart_unescape($d[2]). "\"/>";
        print "<input type=\"text\" size=\"3\" onkeypress=\"validateNumber(event)\" name=\"selectedquantity".$rowcount."\" value=\"0\"/>";
        
        print "</td>";
        print "<td class=\"tdrow1\" >";
        print "<input type=\"text\" size=\"9\" name=\"mounttext".$rowcount."\" value=\"\"/>";
        
        print "</td>";
        print "<td class=\"tdrow1\"><a title=\"";
        print "Kommentar: " . smart_unescape($d[1]);
        print "\" href=\"javascript:popUp('partinfo.php?pid=". smart_unescape($d[2]) ."');\">". smart_unescape($d[0]) ."</a></td>";
            
        print "<td class=\"tdrow1\">".smart_unescape($d[3])."</td>";
        print "<td class=\"tdrow1\">".smart_unescape($d[4])."</td>";
    
        print "</tr>\n";
        }
        
        
        print "</td></tr></table>";
        print "<input type=\"hidden\" name=\"selections\"  value=\"".$rowcount."\"/>";
        print "<input type=\"submit\" value=\"Hinzufügen\"/>";
        print "</table>";
        print "</form>";
        print "</td>";
        print "</tr>";
    }
    ?>
</table>

<br>

<table class="table">
    <tr>
        <td class="tdtop">
        Zugeordnete Teile zu &quot;<?PHP print lookup_device_name ($_REQUEST["deviceid"]); ?>&quot;
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
        
        function validateNumber(evt) 
        {
          var theEvent = evt || window.event;
          var key = theEvent.keyCode || theEvent.which;
          key = String.fromCharCode( key );
          var regex = /[0-9]|\./;
          if( !regex.test(key) ) {
            theEvent.returnValue = false;
            if(theEvent.preventDefault) theEvent.preventDefault();
          }
        }
        // -->
		
		<?PHP
		if($refreshnav == 1)
		{
			$refreshnav = 0;
			print "parent.frames._nav_frame.location.reload();";		
		}
		?>
        </script>
        
        <table>
        <?PHP
        $rowcount = 0;  
        print "<tr class=\"trcat\"><td></td><td>Teil</td><td>Bestückungs<br>Daten</td><td>Footprint</td><td>Anzahl</td><td>Lagernd</td><td>Lagerort</td><td>Lieferant</td><td>Einzelpreis</td><td>Gesamtpreis</td><td>Entfernen</td><td>-</td><td>+</td></tr>\n";
                
        $query = "SELECT parts.name, parts.comment, parts.id, footprints.name, part_device.quantity, parts.instock, storeloc.name, suppliers.name, preise.preis, part_device.mountname ".
        "FROM parts ".
        "JOIN (part_device) ".
        "ON (parts.id = part_device.id_part) ".
        "LEFT JOIN preise ON (preise.part_id = parts.id) ".
        "LEFT JOIN footprints ON (footprints.id = parts.id_footprint) ".
		"LEFT JOIN storeloc ON (storeloc.id = parts.id_storeloc) ".
		"LEFT JOIN suppliers ON (suppliers.id = parts.id_supplier) ".
		"WHERE id_device = ".$_REQUEST["deviceid"]." ORDER BY parts.id_category,parts.name ASC;";
        debug_print($query);
        $result = mysql_query ($query);
        $sumprice = 0;
        while ( $d = mysql_fetch_row ($result) )
        {
        
        $rowcount++;
        if ( ($rowcount & 1) == 0 )
            print "<tr class=\"trlist1\">";
        else
            print "<tr class=\"trlist2\">";
        
        if (has_image($d[2]))
        {
            print "<td class=\"tdrow0\"><a href=\"javascript:popUp('getimage.php?pid=". smart_unescape($d[2]) . "')\"><img class=\"catbild\" src=\"getimage.php?pid=". smart_unescape($d[2]) . "\" alt=\"". smart_unescape($d[0]) ."\"></a></td>";
        }
        else
        {
            //Footprintbilder
            if(is_file("tools/footprints/" . smart_unescape($d[3]) . ".png"))
            {
            print "<td class=\"tdrow0\"><a href=\"javascript:popUp('tools/footprints/". smart_unescape($d[3]) . ".png')\"><img class=\"catbild\" src=\"tools/footprints/". smart_unescape($d[3]) .".png\" alt=\"\"></a></td>";
            }
            else
            {
            print "<td class=\"tdrow0\"><img class=\"catbild\" src=\"img/partdb/dummytn.png\" alt=\"\"></td>";
            }
        }
        
        print "<td class=\"tdrow1\"><a title=\"";
        print "Kommentar: " . smart_unescape($d[1]);
        print "\" href=\"javascript:popUp('partinfo.php?pid=". smart_unescape($d[2]) ."');\">". smart_unescape($d[0]) ."</a></td>";

        print "<td class=\"tdrow1\"><form method=\"post\" action=\"\">";
        print "<input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
        print "<input type=\"hidden\" name=\"partid\" value=\"".smart_unescape($d[2])."\"/>";
        print "<input type=\"hidden\" name=\"action\"  value=\"setmountname\"/>";
        print "<input type=\"text\" size=\"5\"name=\"newmountname\"  value=\"".smart_unescape($d[9])."\"/>";
        print "<input type=\"submit\" value=\"Ok\"/></form></td>";
        
        
        print "<td class=\"tdrow1\">".smart_unescape($d[3])."</td>";
        print "<td class=\"tdrow1\">".smart_unescape($d[4])."</td>";
        
        print "<td ";
        if($d[4] <= $d[5])
        {
            print "class=\"tdrow1\"";
        }
        else
        {
            $notallinstock = 1;
            print "class=\"tdrowred\"";
        }
        print ">".smart_unescape($d[5])."</td>";
        print "<td class=\"tdrow1\">".smart_unescape($d[6])."</td>";
        print "<td class=\"tdrow1\">".smart_unescape($d[7])."</td>";
        print "<td class=\"tdrow1\">";
        if($d[8])
            print smart_unescape($d[8]);
        else
            print "-.-";
        print "€</td>";
        print "<td class=\"tdrow1\">";
        if($d[8])
            print smart_unescape($d[8]*$d[4]);
        else
            print "-.-";
        print "€</td>";
        //Build the sum
        $sumprice += $d[8]*$d[4];
        print "<td class=\"tdrow1\"><form method=\"post\" action=\"\">";
        print "<input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
        print "<input type=\"hidden\" name=\"partid\" value=\"".smart_unescape($d[2])."\"/>";
        print "<input type=\"hidden\" name=\"action\"  value=\"remove\"/>";
        print "<input type=\"submit\" value=\"Entfernen\"/></form></td>";
        
        print "<td class=\"tdrow1\"><form method=\"post\" action=\"\">";
        print "<input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
        print "<input type=\"hidden\" name=\"partid\" value=\"".smart_unescape($d[2])."\"/>";
        print "<input type=\"hidden\" name=\"action\"  value=\"deassign\"/>";
        print "<input type=\"submit\" value=\"-\"/";
        if($d[4] <= 0)
        {
            print "disabled=\"disabled\"";
        }
        print "></form></td>";
        
        print "<td class=\"tdrow1\"><form method=\"post\" action=\"\">";
        print "<input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
        print "<input type=\"hidden\" name=\"partid\" value=\"".smart_unescape($d[2])."\"/>";
        print "<input type=\"hidden\" name=\"action\"  value=\"assign\"/>";
        print "<input type=\"submit\" value=\"+\"/></form></td>";
        print "</tr>\n";
        }
        
        $rowcount++;
        if ( ($rowcount & 1) == 0 )
            print "<tr class=\"trlist1\">";
        else
            print "<tr class=\"trlist2\">";
        print "<td class=\"tdrow1\" colspan=\"9\"></td><td class=\"tdrow0\">Gesamtpreis:<br>".$sumprice."€</td><td class=\"tdrow1\" colspan=\"3\"></td>";
        print "</tr>";
        
        $query = "SELECT parts.name, parts.comment, parts.id, footprints.name, part_device.quantity, parts.instock, storeloc.name, suppliers.name, preise.preis ".
        "FROM parts ".
        "JOIN (part_device, footprints, storeloc, suppliers) ".
        "ON (parts.id = part_device.id_part AND footprints.id = parts.id_footprint AND storeloc.id = parts.id_storeloc AND suppliers.id = parts.id_supplier) ".
        "LEFT JOIN preise ON (preise.part_id = parts.id)".
        "WHERE id_device = ".$_REQUEST["deviceid"]." ORDER BY parts.id_category,parts.name ASC;";
        debug_print($query);
        $result = mysql_query ($query);
        
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
                
                print "Lieferant:</td><td><select name=\"sup_id\">";
                if (! isset($_REQUEST["sup_id"]) )
                    print "<option selected value=\"0\">Alle</option>";
                else
                    print "<option value=\"0\">Alle</option>";
                
                $query = "SELECT id,name FROM suppliers ORDER BY name ASC;";
                $r = mysql_query ($query);
                
                $ncol = mysql_num_rows ($r);
                $lieferanten;
                while ( ($d = mysql_fetch_row($r)) )
                {
                $lieferanten = $lieferanten . smart_unescape($d[0]);
                if ($d[0] == $_REQUEST["sup_id"])
                    print "<option selected value=\"". smart_unescape($d[0]) ."\">". smart_unescape($d[1]) ."</option>\n";
                else
                    print "<option value=\"". smart_unescape($d[0]) ."\">". smart_unescape($d[1]) ."</option>\n";
                }
                print "</select>";
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
                
                print "<tr class=\"trcat\"><td>Multiplikator:</td><td><input type=\"text\" name=\"multiplikator\" size=\"3\" onkeypress=\"validateNumber(event)\" value=\"";
                if ( strcmp ($_REQUEST["action"], "createbom"))
                    print "1";
                else
                    print $_REQUEST["multiplikator"];
                print "\"/></tr>";
                
                
                print "</td></tr>";
                print "<tr class=\"trcat\"><td>Nur fehlendes Material<br>exportieren:</td><td><input type=\"checkbox\" name=\"onlyneeded\" ";
                if ( strcmp ($_REQUEST["action"], "createbom"))
                {
                    print "checked=\"checked\"";
                }
                else
                {
                    if(isset($_REQUEST["onlyneeded"]))
                        print "checked=\"checked\"";
                }
                print "\"></tr></td>";
                print "<tr><td><input type=\"submit\" value=\"Ausführen\"/></tr></td>";
                
                print "<tr><td colspan=\"4\">";
                
                if ( strcmp ($_REQUEST["action"], "createbom") == 0 )
                {
                    
                    $query = "SELECT parts.supplierpartnr, part_device.quantity, storeloc.name, suppliers.name, parts.name, parts.instock, preise.preis ".
                    "FROM parts ".
                    "JOIN (part_device, footprints, storeloc, suppliers) ".
                    "ON (parts.id = part_device.id_part AND footprints.id = parts.id_footprint AND storeloc.id = parts.id_storeloc AND suppliers.id = parts.id_supplier) ".
                    "LEFT JOIN preise ON (preise.part_id = parts.id) ".
                    "WHERE id_device = ".$_REQUEST["deviceid"];
                    if($_REQUEST["sup_id"]!=0)
                    {
                        $query = $query . " AND parts.id_supplier = ".$_REQUEST["sup_id"];
                    }
                    $query = $query . " ORDER BY parts.id_category,parts.name ASC;";
                    
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
                        $order = 1;
                        $orderstring = "";
                        //print partnr.
                        $orderstring = $orderstring.smart_unescape($d[0]);
                        //print spacer
                        $orderstring = $orderstring.$_REQUEST["spacer"];
                        //print quantity
                        $quant = (smart_unescape($d[1])*$_REQUEST["multiplikator"]);
                        if(isset($_REQUEST["onlyneeded"]))
                        {
                            if( $quant > $d[5]) //Check if instock is greater
                            {
                                $quant = ($quant-$d[5]);
                            }
                            else
                            {
                                $order = 0;
                            }
                        }
                        
                        if($order)
                            print GenerateBOMResult($_REQUEST["format"],$_REQUEST["spacer"],$d[4],$d[0],$d[3],$quant,$d[5],$d[6]);
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
  
<br>

<table class="table">
    <tr>
        <td class="tdtop">
        Benötigte Teile abfassen
        </td>
    </tr>
    <tr>
        <td class="tdtext">
            <form method="post" action="">
                <table>
                    
                    <?PHP
                    print "<tr class=\"trcat\"><td>Multiplikator:</td><td><input type=\"text\" name=\"bookmultiplikator\" size=\"3\" onkeypress=\"validateNumber(event)\" value=\"";
                    if ( strcmp ($_REQUEST["action"], "bookparts"))
                        print "1";
                    else
                        print $_REQUEST["bookmultiplikator"];
                    print "\"/><td></tr>";
                    print "<tr><td><input type=\"submit\" value=\"Ausführen\"";
                    if($notallinstock)
                    {
                        print "disabled=\"disabled\"";
                    }
                    print "/>";
                    print "<input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
                    print "<input type=\"hidden\" name=\"action\"  value=\"bookparts\"/>";
                    print "</td>";
                    if($bookstate > 1)  //success
                    {
                    print "<td class=\"tdtextsmall\">";
                    if($bookstate == 2) //no parts in device
                        print "Keine Teile zum Gerät zugeordnet.";
                    else if($bookstate == 3)    //not enough parts in stock
                        print "<b>Nicht genug Teile verfügbar.<br>Teil/e:</b>" . $bookerrorstring;
                    else if($bookstate == 4)    //querry error
                        print "Fehler.";
                    print "</td>";
                    }
                    print "</tr>";
                    ?>
                </table>
            </form>
        </td>
    </tr>
  </table>
    <br>
  <table class="table">
    <tr>
        <td class="tdtop">
        Bauteile Importieren
        </td>
    </tr>
    <tr>
        <td class="tdtext">
            <form method="post" action="">
                <table>
                    
                    <?PHP
                    print "<tr class=\"trcat\"><td>";
					print "<textarea name=\"import_data\" rows=\"".$nrows."\" cols=\"40\" dir=\"ltr\">";
					print "</textarea>";
                    print "<td></tr>";
					print "<tr><td >";
					print "Format: ID;Anzahl;Bestückungsdaten;";
					print "</textarea>";
                    print "<td></tr>";
					print "<tr><td><input type=\"submit\" value=\"Ausführen\"/>";                    
                    print "<input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
                    print "<input type=\"hidden\" name=\"action\"  value=\"import\"/>";
                    print "</td>";
                    print "</tr>";
                    ?>
                </table>
            </form>
        </td>
    </tr>
  </table>
  
  <br>
  <table class="table">
    <tr>
        <td class="tdtop">
        Baugruppe verwalten
        </td>
    </tr>
    <tr>
        <td class="tdtext">
            <form method="post" action="">
                <table>
                    
                    <?PHP
                    print "<tr class=\"trcat\"><td>Umbenennen:</td><td><input type=\"text\" name=\"newdevname\" size=\"10\" maxlength=\"50\" value=\"";
					print lookup_device_name ($_REQUEST["deviceid"]);
                    print "\"/><td></tr>";
                    print "<tr><td><input type=\"submit\" value=\"Ausführen\"/>";
                    
                    print "<input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
                    print "<input type=\"hidden\" name=\"action\"  value=\"renamedevice\"/>";
                    print "</td>";
                    print "</tr>";
                    ?>
                </table>
            </form>
        </td>
    </tr>
	<tr>
        <td class="tdtext">
            <form method="post" action="">
                <table>
                    
                    <?PHP
                    print "<tr class=\"trcat\"><td>Kopieren:</td><td><input type=\"text\" name=\"newcopydevname\" size=\"10\" maxlength=\"50\" value=\"";
					print "KopieVon".lookup_device_name ($_REQUEST["deviceid"]);
                    print "\"/><td></tr>";
                    print "<tr><td><input type=\"submit\" value=\"Ausführen\"/>";
                    
                    print "<input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
                    print "<input type=\"hidden\" name=\"action\"  value=\"copydevice\"/>";
                    print "</td>";
                    print "</tr>";
                    ?>
                </table>
            </form>
        </td>
    </tr>
  </table>
  
</body>
</html>
