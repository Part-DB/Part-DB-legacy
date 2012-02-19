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

        $Id: newpart.php,v 1.5 2006/03/06 23:05:14 cl Exp $

        04/03/06:
                Added escape/unescape calls
*/
        include ("lib.php");
        partdb_init();
       
        $NewStorage     = "";
        $NewDistributor = "";
        $NewFootprint   = "";
       
        //global params to
        $Footprint   = $_REQUEST["p_footprint"];
        $Storage     = $_REQUEST["p_storeloc"];
        $Distributor = $_REQUEST["p_supplier"];
        $p_name      = $_REQUEST["p_name"];


        if(isset($_REQUEST["AddPart"]))
        {
                /* some sanity checks */
				//Removed check for testing
                /*if ( (strcmp ($_REQUEST["p_footprint"], "X") == 0) || (strcmp ($_REQUEST["p_storeloc"], "X") == 0) || (strcmp ($_REQUEST["p_supplier"], "X") == 0) )
                {
                    print "<h2>\nFehler:</h2>\n";
                        if (strcmp ($_REQUEST["p_footprint"], "X") == 0) { print "kein Footprint<br>"; }
                        if (strcmp ($_REQUEST["p_storeloc"],  "X") == 0) { print "kein Lagerort<br>"; }
                        if (strcmp ($_REQUEST["p_supplier"],  "X") == 0) { print "kein Lieferant<br>"; }
                    print "<br>\n";

                }
                else*/
                {
                    $query = 
                        "INSERT INTO parts (".
                        "id_category,".
                        "name,".
                        "instock,".
                        "mininstock,".
                        "comment,".
                        "id_footprint,".
                        "id_storeloc,".
                        "id_supplier,".
                        "supplierpartnr) ".
                        "VALUES (". 
                        smart_escape($_REQUEST["cid"]) .",".
                        smart_escape($_REQUEST["p_name"]) .",".
                        smart_escape($_REQUEST["p_instock"]) .",".
                        smart_escape($_REQUEST["p_mininstock"]) .",".
                        smart_escape($_REQUEST["p_comment"]) .",".
                        smart_escape($_REQUEST["p_footprint"]) .",".
                        smart_escape($_REQUEST["p_storeloc"]) .",".
                        smart_escape($_REQUEST["p_supplier"]) .",".
                        smart_escape($_REQUEST["p_supplierpartnr"]) .
                        ");";
                       
                    debug_print ($query);
                    $r = mysql_query ($query);
                    $id = mysql_insert_id();
                    if(strlen($_REQUEST["URLDatasheet"])!=0)
                    {
                        $query = "INSERT INTO datasheets (part_id,datasheeturl) VALUES (".smart_escape($id).",".smart_escape($_REQUEST["URLDatasheet"]).");";
                        mysql_query($query);
                    }
                    if(strlen($_FILES["AddImage"]["tmp_name"]))
                    {
                        if (is_uploaded_file($_FILES["AddImage"]["tmp_name"]))
                        {
                                /*
                                 * split the file name into its parts and create
                                 * a unique filename.
                                 */
                                $a = explode(".",$_FILES['AddImage']['name']);
                                $fname = "img_";
                                $fname .= md5_file($_FILES['AddImage']['tmp_name']);
                                if (($a[count($a)-1] == "jpg") || ($a[count($a)-1] == "jpg"))
                                {
                                        $fname .= ".jpg";
                                }
                                else if ($a[count($a)-1] == "gif")
                                {
                                        $fname .= ".gif";
                                }
                                else if ($a[count($a)-1] == "png")
                                {
                                        $fname .= ".png";
                                }
                                // FIXME: Some error handling required (for example:
                                // unknown file type etc. pp.
                                move_uploaded_file($_FILES['AddImage']['tmp_name'], "img/".$fname);
                                chmod ("img/" .$fname, 0775);
                                $query = "INSERT INTO pictures (part_id,pict_fname) VALUES (". smart_escape($id) .",". smart_escape($fname) .")";
                                debug_print($query);
                                mysql_query($query);
                        }
                    }
					if(strlen($_REQUEST["p_price"])!=0)
					{
						if (preg_match("/^[-+]?[0-9]*\.?[0-9]+/", $_REQUEST["p_price"]) == true)
						{
							$_REQUEST["price"] = str_replace(',', '.', $_REQUEST["price"]);
							/* Before adding the new price, delete the old one! */
							$query = "DELETE FROM preise WHERE part_id=". smart_escape($_REQUEST["pid"]) ." LIMIT 1;";
							debug_print($query);
							mysql_query($query);
							$query = "INSERT INTO preise (part_id,ma,preis,t) VALUES (". $id .", 1, ". smart_escape($_REQUEST["p_price"]) .", NOW());";
							debug_print($query);
							mysql_query($query);
						}
					}
                    // close the window on success
                    // but only if we don't want another part
                    if ( !($_REQUEST['addmoreparts'] == "true"))
                    {
                        print "<script>window.close();</script>";
                    }
                    // autoincrement name
                    $p_name = ++$_REQUEST["p_name"];
                }
        }

        //add a new storage if it not exists, and save the name in global var to select while creating drop downbox
        if(isset($_REQUEST["AddStorage"]))
        {
                if(strcmp($_REQUEST["NewStorage"],"Direkteingabe/Neu")!=0)
                {
                        $NewStorage = $_REQUEST["NewStorage"];
                        $query = "SELECT name FROM storeloc WHERE name = '" . $_REQUEST["NewStorage"] . "';";
                        $r = mysql_query ($query);
                        $ncol = mysql_num_rows ($r);
                        if($ncol == 0)
                        {
                                $query = "INSERT INTO storeloc (name) VALUES (". smart_escape($_REQUEST["NewStorage"]) .");";
                                debug_print ($query);
                                mysql_query ($query);
                        }
                }
        }

        //add a new distributor if it not exists, and save the name in global var to select while creating drop downbox
        if(isset($_REQUEST["AddDistributor"]))
        {
                if(strcmp($_REQUEST["NewDistributor"],"Direkteingabe/Neu")!=0)
                {
                        $NewDistributor = $_REQUEST["NewDistributor"];
                        $query = "SELECT name FROM suppliers WHERE name = '" . $_REQUEST["NewDistributor"] . "';";
                        $r = mysql_query ($query);
                        $ncol = mysql_num_rows ($r);
                        if($ncol == 0)
                        {
                                $query = "INSERT INTO suppliers (name) VALUES (". smart_escape($_REQUEST["NewDistributor"]) .");";
                                debug_print ($query);
                                mysql_query ($query);
                        }
                }
        }

        //add a new footprint if it not exists, and save the name in global var to select while creating drop downbox
        if(isset($_REQUEST["AddFootprint"]))
        {
                if(strcmp($_REQUEST["NewFootprint"],"Direkteingabe/Neu")!=0)
                {
                        $NewFootprint = $_REQUEST["NewFootprint"];
                        $query = "SELECT name FROM footprints WHERE name = '" . $_REQUEST["NewFootprint"] . "';";
                        $r = mysql_query ($query);
                        $ncol = mysql_num_rows ($r);
                        if($ncol == 0)
                        {
                                $query = "INSERT INTO footprints (name) VALUES (". smart_escape($_REQUEST["NewFootprint"]) .");";
                                debug_print ($query);
                                mysql_query ($query);
                        }
                }
        }


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Neues Teil</title>
    <?php print_http_charset(); ?>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
    <script type="text/javascript" src="util-functions.js"></script>
    <script type="text/javascript" src="clear-default-text.js"></script>       
	<script language="JavaScript" type="text/javascript">
	<!--
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
	</script> 
</head>
<body class="body">

<div class="outer">
    <h2>Neues Teil in der Kategorie &quot;<?PHP print lookup_category_name ($_REQUEST["cid"]); ?>&quot;</h2>
    <div class="inner">
        <form enctype="multipart/form-data" action="" method="post">
            <input type="hidden" name="cid" value="<?PHP print $_REQUEST["cid"]; ?>"/>
            <table width="100">
            <tr>
            <td>Name:</td>
            <td>
            <input type="text" name="p_name" value="<?PHP print $p_name ?>" tabindex=\"1\"/>
            </td>
            </tr>
            
            <tr>
            <td>Lagerbestand:</td>
            <td><input type="text" name="p_instock" onkeypress="validateNumber(event)" value="<?PHP print $_REQUEST["p_instock"] ?>"/></td>
            </tr>
            
            <tr>
            <td>Min. Bestand:</td>
            <td><input type="text" name="p_mininstock" onkeypress="validateNumber(event)" value="<?PHP print $_REQUEST["p_mininstock"] ?>"/></td>
            </tr>
            
            <tr>
            <td>Footprint:</td>
            <td>
            <select name="p_footprint">
            <option value="X"></option>
            <?php build_footprint_tree( 0, 1, $NewFootprint); ?>
            </select>
            </td>
            <td>
            <input type="hidden" name="a" value="AddFootprint"/>
            <input type="text" name="NewFootprint" value="Direkteingabe/Neu" class="cleardefault"/>
            <input type="submit" name="AddFootprint" value="Search/Add"/>
            </td>
            </tr>
            
            <tr>
            <td>Lagerort:</td>
            <td>
            <select name="p_storeloc">
            <option value=""></option>;
            <?php build_location_tree(0, 1, 0, false); ?>
            </select>
            </td>
            <td>
            <input type="hidden" name="a" value="AddStorage"/>
            <input type="text" name="NewStorage" value="Direkteingabe/Neu" class="cleardefault"/>
            <input type="submit" name="AddStorage" value="Search/Add"/>
            </td>
            </tr>
            
            <tr>
            <td>Lieferant:</td>
            <td>
            <select name="p_supplier">
            <option value="X"></option>
            <?PHP
            $query = "SELECT id,name FROM suppliers ORDER BY name ASC;";
            $r = mysql_query ($query);
            $ncol = mysql_num_rows ($r);
            for ($i = 0; $i < $ncol; $i++)
            {
                    $d = mysql_fetch_row ($r);
                    print "<option value=\"". smart_unescape($d[0])."\"";
                    //check if a new distributor should be selected
                    if(     (strlen($NewDistributor)>0 && strcmp($NewDistributor,smart_unescape($d[1]))==0) ||
                            (strlen($NewDistributor)==0 && $Distributor == smart_unescape($d[0])))
                    {
                            print " selected ";
                    }
                    print  ">". smart_unescape($d[1]) ."</option>\n";
            }
            ?>
            </select>
            </td>
            <td>
            <input type="hidden" name="a" value="AddDistributor"/>
            <input type="text" name="NewDistributor" value="Direkteingabe/Neu" class="cleardefault"/>
            <input type="submit" name="AddDistributor" value="Search/Add"/>
            </td>
            </tr>
            
            <tr>
            <td>Bestell-Nr.:</td>
            <td><input type="text" name="p_supplierpartnr" value="<?PHP print $_REQUEST["p_supplierpartnr"] ?>"></td>
            </tr>
            
            <tr>
            <td>Preis:</td>
            <td><input type="text" name="p_price" onkeypress="validateNumber(event)" value="<?PHP print $_REQUEST["p_price"] ?>"></td>
            </tr>
            
            <tr>
            <td valign="top">Kommentar:</td>
            <td colspan="2"><textarea name="p_comment" rows=2 cols=40><?PHP print $_REQUEST["p_comment"] ?></textarea></td>
            </tr>
            <tr><td>Bild:</td>
            <td><input type="file" name="AddImage"/></td>
            </tr>

            <tr>
            <td>Datenblatt (URL):</td>
            <td><input type="text" name="URLDatasheet" value="<?PHP print $_REQUEST["URLDatasheets"] ?>"/></td>
            </tr>
            <tr><td colspan="2"><input type="submit" name="AddPart" value="Teil hinzuf&uuml;gen"></td></tr>

            <tr>
            <td colspan="2">Weitere Bauteile erfassen:
            <input type="checkbox" name="addmoreparts" value="true" <?PHP if($_REQUEST["addmoreparts"]) print "checked = \"checked\""; ?>></td>
            </tr>
            </table>
        </form>
    </div>
</div>


</body>
</html>

