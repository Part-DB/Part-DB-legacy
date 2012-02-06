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

    $Id: lib.php,v 1.7 2006/03/06 23:05:14 cl Exp $

    ChangeLog
    
    25/02/2006
        Some major changes concerning the escaping of arguments
        supplied in SQL queries. Added some comments, too.
*/
    

    /*
     * debug_print is used for printing the SQL queries
     * before submitting the queries to the DB. The
     * $partdb_debug var is used to turn debugging off
     * during normal "bug-free" usage.
     */
    function debug_print($t)
    {
        $partdb_debug = 0;
        if ($partdb_debug == 1)
            print $t;
    }

    /*@@@ some helper functions down below @@@*/


    function partdb_init()
    {
        include("config.php");
        
        /* Enter your MySQL username and password in config.php */
        $link = mysql_connect ($mysql_server, $db_user, $db_password);
        if ($link)
            mysql_select_db ($database);
        else
        {
            echo "connect to DB failed", mysql_errno(), "<br>", mysql_error(), "<br>";
        }
    }


    /* stolen from the PHP docs */
    function smart_escape($value)
    {
        // use stripslashes if necessary
        // is there somebody using this mode???
        if (get_magic_quotes_gpc()) {
            $value = stripslashes($value);
        }

        // quote it
        $value = "'". mysql_escape_string($value) ."'";

        return ($value);
    }

    /* at the moment this function is _very_ smart :) */
    function smart_unescape($value)
    {
        return stripslashes($value);
    }

    /*
     * Given the category id this helper-function does a lookup
     * and returns the name of the category. At the moment we
     * assume that the category id is valid. FIXME
     */
    function lookup_category_name ($id)
    {
        $query = "SELECT name FROM categories WHERE id=". smart_escape($id) .";";
        debug_print($query);
        $r = mysql_query ($query);
        $d = mysql_fetch_row ($r);

        return (($id == 0) ? "Alles" : smart_unescape($d[0]));
    }

    function lookup_part_name ($id)
    {
        $query = "SELECT name FROM parts WHERE id=". smart_escape($id) .";";
        debug_print($query);
        $r = mysql_query ($query);
        $d = mysql_fetch_row ($r);

        return (smart_unescape($d[0]));
    }
    
    function lookup_device_name ($id)
    {
        $query = "SELECT name FROM devices WHERE id=". smart_escape($id) .";";
        debug_print($query);
        $r = mysql_query ($query);
        $d = mysql_fetch_row ($r);

        return (smart_unescape($d[0]));
    }
    
    function lookup_location_name ($id)
    {
        $query = "SELECT name FROM storeloc WHERE id=". smart_escape($id) .";";
        debug_print($query);
        $r = mysql_query ($query);
        $d = mysql_fetch_row ($r);

        return (smart_unescape($d[0]));
    }

    /*
     * This function is very special. The $visited_category_ids array
     * holds the ids of all categories we've been to. This is used to
     * avoid infinite recursion. Nevertheless, error handling if recursion
     * happens is still missing.
     * Afterwards a backtrace is created, i.e. the branch of the category
     * tree the part is in.
     */
    function show_bt($cat_id)
    {
        $bt = "";
        $cntr = 0;
        $visited_category_ids = array();

        $visited_category_ids[$cntr] = $cat_id;
        while ($visited_category_ids[$cntr])
        {
            $w = "(1 ";
            for ($i = 0; $i < $cntr; $i++)
                $w = $w . "AND (id!='".$visited_category_ids[$i]."') ";
            $w .= ')';
                
            $query = "SELECT parentnode FROM categories WHERE (id='".$visited_category_ids[$cntr]."') AND ".$w.";";
            debug_print ($query."<br>");
            $result = mysql_query($query);
            if (mysql_num_rows($result) == 0)
            {
                return("Error");
            }
            
            $d = mysql_fetch_row($result);

            $cntr++;
            $visited_category_ids[$cntr] = $d[0];
        }

        /* We've been to all parent categories, so now build up a
         * string of those categories' names seperated by colons.
         */
        for ($i = $cntr-1; $i > 0; $i--)
            $bt .= "&quot;<b>".lookup_category_name($visited_category_ids[$i])."</b>&quot; : ";
            
        $bt .= "&quot;<b>".lookup_category_name($visited_category_ids[0])."</b>&quot;";

        return ($bt);
    }

    /*
     * When listing all parts of a category, part-db wants to know
     * if an item has got a thumbnail. This procedure does the job.
     * It returns 1 if there's a picture and 0 if not.
     */
    function has_image($pid)
    {
        $pict_query = "SELECT pictures.pict_fname FROM pictures WHERE pictures.part_id=". smart_escape($pid). ";";
        debug_print ($pict_query);
        $r = mysql_query ($pict_query); 
        if (mysql_num_rows($r))
        {
            mysql_free_result($r);
            return(1);
        }
        mysql_free_result($r);
        return(0);
    }


    /*
     * parameter: string to search
     * result: id from database
     */
    function get_category_id( $categorie)
    {
        $query = "SELECT id FROM categories WHERE name=". smart_escape( $categorie)." LIMIT 1;";
        $result = mysql_query( $query);
        $result_array = mysql_fetch_array( $result); 
        return( $result_array['id']);
    }
    
    function get_footprint_id( $footprint)
    {
        $query = "SELECT id FROM footprints WHERE name=". smart_escape( $footprint)." LIMIT 1;";
        $result = mysql_query( $query);
        $result_array = mysql_fetch_array( $result); 
        return( $result_array['id']);
    }
    
    function get_storeloc_id( $storeloc)
    {
        $query = "SELECT id FROM storeloc WHERE name=". smart_escape( $storeloc)." LIMIT 1;";
        $result = mysql_query( $query);
        $result_array = mysql_fetch_array( $result); 
        return( $result_array['id']);
    }

    function get_supplier_id( $supplier)
    {
        $query = "SELECT id FROM suppliers WHERE name=". smart_escape( $supplier)." LIMIT 1;";
        $result = mysql_query( $query);
        $result_array = mysql_fetch_array( $result); 
        return( $result_array['id']);
    }
    
    
    /*
     * parameter: string to search
     * result: true  if exists in database
     *         flase if not exist
     */
    function check_categories( $categorie)
    {
        $query = "SELECT name FROM categories WHERE name=". smart_escape( $categorie).";";
        $res   = mysql_query( $query);
        $data  = mysql_num_rows( $res);

        return( ($data == 1) ? true : false );
    }

    function check_footprint( $footprint)
    {
        $query = "SELECT name FROM footprints WHERE name=". smart_escape( $footprint).";";
        $res   = mysql_query( $query);
        $data  = mysql_num_rows( $res);

        return( ($data == 1) ? true : false );
    }

    function check_storeloc( $storeloc)
    {
        $query = "SELECT name FROM storeloc WHERE name=". smart_escape( $storeloc).";";
        $res   = mysql_query( $query);
        $data  = mysql_num_rows( $res);

        return( ($data == 1) ? true : false );
    }

    function check_supplier( $supplier)
    {
        $query = "SELECT name FROM suppliers WHERE name=". smart_escape( $supplier).";";
        $res   = mysql_query( $query);
        $data  = mysql_num_rows( $res);

        return( ($data == 1) ? true : false );
    }


    function is_odd( $number) 
    {
        //return $number & 1; // 0 = even, 1 = odd
        return ($number & 1) ? true : false; // false = even, true = odd
    }


    function print_table_image( $id, $name, $footprint)
    {
        if ( has_image( $id))
        {
            $link = "getimage.php?pid=". smart_unescape( $id);
            print "<a href=\"javascript:popUp('". $link ."')\">".
                "<img class=\"hoverpic\" src=\"". $link . "\" alt=\"". smart_unescape( $name) ."\">".
                "</a>";
        }
        else
        {
            $link = "tools/footprints/". smart_unescape( $footprint) .".png";
            if ( is_file( $link))
            {
                // footprint
                print "<a href=\"javascript:popUp('". $link ."')\">".
                    "<img class=\"hoverpic\" src=\"". $link ."\" alt=\"\">".
                    "</a>";
            }
            else
            {
                // dummy
                print '<img src="img/partdb/dummytn.png" alt="">';
            }
        }
    } // end function
    

    function print_table_row( $rowcount, $id, $name, $footprint, $supplierpartnr, $comment, $instock, $mininstock, $location)
    {
        // the alternating background colors are created here
        print "<tr class=\"".( is_odd( $rowcount) ? 'trlist_odd': 'trlist_even')."\">\n";
        
        // Pictures
        print "<td class=\"tdrow0\">";
        print_table_image( $id, $name, $footprint);
        print "</td>\n";

        // comment
        print "<td class=\"tdrow1\"><a title=\"Kommentar: " . htmlspecialchars( smart_unescape( $comment)) . "\"";
        print "href=\"javascript:popUp('partinfo.php?pid=". smart_unescape( $id) ."');\">". smart_unescape( $name) ."</a></td>\n";
        // instock/ mininstock
        print "<td class=\"tdrow2\">". smart_unescape( $instock) ."/". smart_unescape( $mininstock) ."</td>\n";
        // footprint
        print "<td class=\"tdrow3\">". smart_unescape( $footprint) ."</td>\n";
        // store location
        print "<td class=\"tdrow4\">". smart_unescape( $location) . "</td>\n";
        // id
		print "<td class=\"tdrow4 idclass\">". smart_unescape( $id) . "</td>\n";
        // datasheet links
        print "<td class=\"tdrow5\">";
        // with icons 
        print "<a title=\"alldatasheet.com\" href=\"http://www.alldatasheet.com/view.jsp?Searchword=". urlencode( smart_unescape( $name)) ."\" target=\"_blank\">".
            "<img class=\"companypic\" src=\"img/partdb/ads.png\" alt=\"logo\">".
            "</a>\n";

        $searchfor = ( strlen( $supplierpartnr) > 0) ? $supplierpartnr : $name;
        print "<a title=\"Reichelt.de\" href=\"http://www.reichelt.de/?ACTION=4;START=0;SHOW=1;SEARCH=". urlencode( smart_unescape( $searchfor)) ."\" target=\"_blank\">".
            "<img class=\"companypic\" src=\"img/partdb/reichelt.png\" alt=\"logo\">".
            "</a>\n";
        // without icons
        print "<a href=\"http://search.datasheetcatalog.net/key/". urlencode( smart_unescape( $name)) ."\" target=\"_blank\">DC </a>\n";
        // show local datasheet if availible
        $query = "SELECT ".
            "datasheeturl ".
            "FROM datasheets ".
            "WHERE part_id=". smart_escape( $id) 
            ." ORDER BY datasheeturl ASC;";
        $result_ds = mysql_query($query);
        $d = mysql_fetch_row ($result_ds);
        $datasheeturl = $d[0];
        if( !empty( $datasheeturl) )
        {
            print "<a href=\"". smart_unescape( $datasheeturl) ."\">Datenblatt</a>\n";
        }
        print "</td>\n";
        
        //build the "-" button, only if more then 0 parts on stock
        print "<td class=\"tdrow6\"><form action=\"\" method=\"post\">";
        print "<input type=\"hidden\" name=\"pid\" value=\"".smart_unescape( $id)."\"/>";
        print "<input type=\"hidden\" name=\"action\"  value=\"r\"/>";
        print "<input type=\"submit\" value=\"-\"";
        if ( $instock <= 0)
        {
            print " disabled=\"disabled\" ";
        }
        print "/></form></td>\n";
            
        //build the "+" button
        print "<td class=\"tdrow7\"><form action=\"\" method=\"post\">";
        print "<input type=\"hidden\" name=\"pid\" value=\"".smart_unescape( $id)."\"/>";
        print "<input type=\"hidden\" name=\"action\"  value=\"a\"/>";
        print "<input type=\"submit\" value=\"+\"/></form></td>\n";

        print "</tr>\n";
    }

    function GetFormatStrings()
    {
        $aRetVal = array("CSV","CSV Reichelt","CSV Farnell");
        return $aRetVal;
    }
    
    function PrintsFormats($Request)
    {
        $Formats = GetFormatStrings();
        $NrOfFormats = count($Formats);
        for ($i = 0; $i < $NrOfFormats; $i++) 
        {
            if (($i==0 && isset($_REQUEST[$Request])==0 ) ||
                (isset($_REQUEST[$Request]) && $i == $_REQUEST[$Request]))
                print "<option selected value=\"".smart_unescape($i)."\">".$Formats[$i]."</option>";
            else
                print "<option value=\"".smart_unescape($i)."\">".$Formats[$i]."</option>";
        }
    }
    
    function GenerateBOMHeadline($Format,$Spacer)
    {
        if( $Format == 0 ) //CSV
        {
            $strRetVal = "\r\n";
            $strRetVal = $strRetVal."Name:";
            $strRetVal = $strRetVal.smart_unescape($Spacer);
            $strRetVal = $strRetVal."Anzahl:";
            $strRetVal = $strRetVal.smart_unescape($Spacer);
            $strRetVal = $strRetVal."Lieferant:";
            $strRetVal = $strRetVal.smart_unescape($Spacer);
            $strRetVal = $strRetVal."Bestellnummer:";
            $strRetVal = $strRetVal.smart_unescape($Spacer);
            $strRetVal = $strRetVal."Preis:";
            $strRetVal = $strRetVal.smart_unescape($Spacer);
            $strRetVal = $strRetVal."Lagernd:";
            $strRetVal = $strRetVal.smart_unescape($Spacer);
            $strRetVal = $strRetVal."\r\n";
            return $strRetVal;
        }
        else if( $Format == 1 ) //CSV Reichelt
        {
            return "\r\nBestellnummer:;Anzahl:\r\n";
        }
        else if( $Format == 2 ) //CSV Farnell
        {
            return "\r\nBestellnummer:,Anzahl:\r\n";
        }
        else
        {
            return "Unbekanntes export Format.";
        }
    }
    
    function GenerateBOMResult($Format,$Spacer,$PartName,$SupNr,$SupName,$Quantity,$Instock,$Price)
    {
        if( $Format == 0 ) //CSV
        {
            $strRetVal = "\r\n";
            $strRetVal = $strRetVal.smart_unescape($PartName);
            $strRetVal = $strRetVal.smart_unescape($Spacer);
            $strRetVal = $strRetVal.smart_unescape($Quantity);
            $strRetVal = $strRetVal.smart_unescape($Spacer);
            $strRetVal = $strRetVal.smart_unescape($SupName);
            $strRetVal = $strRetVal.smart_unescape($Spacer);
            $strRetVal = $strRetVal.smart_unescape($SupNr);
            $strRetVal = $strRetVal.smart_unescape($Spacer);
            $strRetVal = $strRetVal.smart_unescape($Price);
            $strRetVal = $strRetVal.smart_unescape($Spacer);
            $strRetVal = $strRetVal.smart_unescape($Instock);
            
            return $strRetVal;
        }
        else if( $Format == 1 ) //CSV Reichelt
        {
            return "\r\n".smart_unescape($SupNr).";".smart_unescape($Quantity);
        }
        else if( $Format == 2 ) //CSV Farnell
        {
            return "\r\n".smart_unescape($SupNr).",".smart_unescape($Quantity);
        }
        else
        {
            return "";
        }
    }


    /* generate http header line */
    function print_http_charset()
    {
        require( 'config.php');
        if ( strlen( $http_charset) > 0 )
        {
            print "<meta http-equiv=\"content-type\" content=\"text/html; charset=". $http_charset ."\">\n";
        }
    }
?>
