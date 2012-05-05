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
       
       
        // global params to
        $NewFootprint   = ( isset( $_REQUEST['AddFootprint']))   ? $_REQUEST['NewFootprint']   : '';
        $NewStorage     = ( isset( $_REQUEST['AddStorage']))     ? $_REQUEST['NewStorage']     : '';
        $NewDistributor = ( isset( $_REQUEST['AddDistributor'])) ? $_REQUEST['NewDistributor'] : '';

        $p_name         = ( isset( $_REQUEST['p_name']))         ? $_REQUEST['p_name']         : '';
        $p_description  = ( isset( $_REQUEST['p_description']))  ? $_REQUEST['p_description']  : '';
        $p_instock      = ( isset( $_REQUEST['p_instock']))      ? $_REQUEST['p_instock']      : '';
        $p_mininstock   = ( isset( $_REQUEST['p_mininstock']))   ? $_REQUEST['p_mininstock']   : '';
        $p_comment      = ( isset( $_REQUEST['p_comment']))      ? $_REQUEST['p_comment']      : '';
        $p_price        = ( isset( $_REQUEST['p_price']))        ? $_REQUEST['p_price']        : '';
        $p_obsolete     = ( isset( $_REQUEST["p_obsolete"])      ? (bool)$_REQUEST["p_obsolete"] : false);
        $p_footprint    = ( isset( $_REQUEST['p_footprint']))    ? $_REQUEST['p_footprint']    : '';
        $p_storeloc     = ( isset( $_REQUEST['p_storeloc']))     ? $_REQUEST['p_storeloc']     : '';
        $p_supplier     = ( isset( $_REQUEST['p_supplier']))     ? $_REQUEST['p_supplier']     : '';
        $p_supplierpartnr = ( isset( $_REQUEST['p_supplierpartnr'])) ? $_REQUEST['p_supplierpartnr'] : '';
        $URLDatasheets  = ( isset( $_REQUEST['URLDatasheets']))  ? $_REQUEST['URLDatasheets']  : '';
        $addmoreparts   = ( isset( $_REQUEST['addmoreparts']))   ? (bool)$_REQUEST['addmoreparts'] : false;

        $Footprint      = ( isset( $_REQUEST['NewFootprint']))   ? $_REQUEST['NewFootprint']   : $p_footprint;
        $Storage        = ( isset( $_REQUEST['NewStorage']))     ? $_REQUEST['NewStorage']     : $p_storeloc;
        $Distributor    = ( isset( $_REQUEST['NewDistributor'])) ? $_REQUEST['NewDistributor'] : $p_supplier;


        if ( isset( $_REQUEST["AddPart"]))
        {
                /* some sanity checks */
				//Removed check for testing
                /*if ( (strcmp ($p_footprint, "X") == 0) || (strcmp ($p_storeloc, "X") == 0) || (strcmp ($p_supplier, "X") == 0) )
                {
                    print "<h2>\nFehler:</h2>\n";
                        if (strcmp ($p_footprint, "X") == 0) { print "kein Footprint<br>"; }
                        if (strcmp ($p_storeloc,  "X") == 0) { print "kein Lagerort<br>"; }
                        if (strcmp ($p_supplier,  "X") == 0) { print "kein Lieferant<br>"; }
                    print "<br>\n";

                }
                else*/
                {
                       
                    $id = part_add( $_REQUEST["cid"], $p_name, $p_description, $p_instock, $p_mininstock, $p_comment, $p_obsolete, $p_footprint, $p_storeloc, $p_supplier, $p_supplierpartnr);
                    if ( strlen($_REQUEST["URLDatasheet"]) != 0)
                    {
                        datasheet_add( $id, $_REQUEST["URLDatasheet"]);
                    }
                    if ( strlen( $_FILES["AddImage"]["tmp_name"]))
                    {
                        if ( is_uploaded_file( $_FILES["AddImage"]["tmp_name"]))
                        {
                                /*
                                 * split the file name into its parts and create
                                 * a unique filename.
                                 */
                                $a = explode( ".", $_FILES['AddImage']['name']);
                                $fname = "img_";
                                $fname .= md5_file( $_FILES['AddImage']['tmp_name']);
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
                                move_uploaded_file( $_FILES['AddImage']['tmp_name'], "img/".$fname);
                                chmod( "img/" .$fname, 0775);
                                picture_add( $id, $fname);
                        }
                    }
					if ( strlen( $_REQUEST["p_price"]) != 0)
					{
						if (preg_match("/^[-+]?[0-9]*\.?[0-9]+/", $_REQUEST["p_price"]) == true)
						{
							$_REQUEST["price"] = str_replace(',', '.', $_REQUEST["price"]);
                            price_add( $id, $_REQUEST["p_price"]);
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
        if ( isset( $_REQUEST["AddStorage"]))
        {
            if (( $NewStorage != "Direkteingabe/Neu") && (! location_exists( $NewStorage)))
            {
                location_add( $_REQUEST["NewStorage"], 0);
            }
        }

        //add a new distributor if it not exists, and save the name in global var to select while creating drop downbox
        if ( isset( $_REQUEST["AddDistributor"]))
        {
            if (( $NewDistributor != "Direkteingabe/Neu") && (! supplier_exists( $NewDistributor)))
            {
                supplier_add( $NewDistributor);
            }
        }

        //add a new footprint if it not exists, and save the name in global var to select while creating drop downbox
        if ( isset($_REQUEST["AddFootprint"]))
        {
            if (( $NewFootprint != "Direkteingabe/Neu") && (! footprint_exists( $NewFootprint)))
            {
                footprint_add( $NewFootprint);
            }
        }


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Neues Teil</title>
    <?php print_http_charset(); ?>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
    <script type="text/javascript" src="util-functions.js"></script>
    <script type="text/javascript" src="clear-default-text.js"></script>       
    <script type="text/javascript" src="validatenumber.js"></script>       
</head>
<body class="body">

<div class="outer">
    <h2>Neues Teil in der Kategorie &quot;<?php print category_get_name( $_REQUEST["cid"]); ?>&quot;</h2>
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
            <td>Beschreibung:</td>
            <td>
            <input type="text" name="p_desciption" value="<?PHP print $p_description ?>">
            </td>
            </tr>
            
            <tr>
            <td>Lagerbestand:</td>
            <td><input type="text" name="p_instock" onkeypress="validateNumber(event)" value="<?PHP print $p_instock ?>"/></td>
            </tr>
            
            <tr>
            <td>Min. Bestand:</td>
            <td><input type="text" name="p_mininstock" onkeypress="validateNumber(event)" value="<?PHP print $p_mininstock ?>"/></td>
            </tr>
            
            <tr>
            <td>Footprint:</td>
            <td>
            <select name="p_footprint">
            <option value="X"></option>
            <?php footprint_build_tree( 0, 1, footprint_get_id( $NewFootprint)); ?>
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
            <?php location_tree_build(0, 1, location_get_id( $NewStorage), false); ?>
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
            <?php suppliers_build_list( supplier_get_id( $NewDistributor)); ?>
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
            <td><input type="text" name="p_supplierpartnr" value="<?PHP print $p_supplierpartnr ?>"></td>
            </tr>
                    
            <tr>
            <td><b>obsolet:</b></td>
            <td><input type="checkbox" name="p_obsolete" value="true"<?php print $p_obsolete ? 'checked' : ''; ?>></td>
            </tr>
            
            <tr>
            <td>Preis:</td>
            <td><input type="text" name="p_price" onkeypress="validateNumber(event)" value="<?php print $p_price ?>"></td>
            </tr>
            
            <tr>
            <td valign="top">Kommentar:</td>
            <td colspan="2"><textarea name="p_comment" rows=2 cols=40><?PHP print $p_comment ?></textarea></td>
            </tr>
            <tr><td>Bild:</td>
            <td><input type="file" name="AddImage"/></td>
            </tr>

            <tr>
            <td>Datenblatt (URL):</td>
            <td><input type="text" name="URLDatasheet" value="<?php print $URLDatasheets ?>"/></td>
            </tr>
            <tr><td colspan="2"><input type="submit" name="AddPart" value="Teil hinzuf&uuml;gen"></td></tr>

            <tr>
            <td colspan="2">Weitere Bauteile erfassen:
            <input type="checkbox" name="addmoreparts" value="true"<?php print $addmoreparts ? 'checked' : ''; ?>></td>
            </tr>
            </table>
        </form>
    </div>
</div>


</body>
</html>

