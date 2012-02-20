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
        // quote it
        $value = "'". mysql_real_escape_string( $value) ."'";
        return( $value);
    }
    
    function smart_escape_for_search( $value)
    {
        $value = str_replace( '*', '%', $value);
        $value = "'%". mysql_real_escape_string( $value) ."%'";
        return( $value);
    }


    /* at the moment this function is _very_ smart :) */
    function smart_unescape( $value)
    {
        return stripslashes( $value);
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
            if ( mysql_num_rows( $result) == 0)
            {
                return( "Error");
            }
            
            $d = mysql_fetch_row( $result);

            $cntr++;
            $visited_category_ids[$cntr] = $d[0];
        }

        /* We've been to all parent categories, so now build up a
         * string of those categories' names seperated by colons.
         */
        for ($i = $cntr-1; $i > 0; $i--)
            $bt .= "&quot;<b>".category_get_name($visited_category_ids[$i])."</b>&quot; : ";
            
        $bt .= "&quot;<b>".category_get_name($visited_category_ids[0])."</b>&quot;";

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
     * check if a given number is odd 
     */
    function is_odd( $number) 
    {
        //return $number & 1; // 0 = even, 1 = odd
        return ($number & 1) ? true : false; // false = even, true = odd
    }


    
    /*
     * no comment
     */
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
    

    /* 
        This function prints one complete table line.
        Its used from showparts.php and search.php

        $data is an associate array, following entrys are used:
            'id'
            'name'
            'footprint'
            'supplierpartnr'
            'comment'
            'instock'
            'mininstock'
            'location'

    */
    function print_table_row( $rowcount, $data) 
    
    {
        // the alternating background colors are created here
        print "<tr class=\"".( is_odd( $rowcount) ? 'trlist_odd': 'trlist_even')."\">\n";
        
        // Pictures
        print "<td class=\"tdrow0\">";
        print_table_image( $data['id'], $data['name'], $data['footprint']);
        print "</td>\n";

        // comment
        print "<td class=\"tdrow1\"><a title=\"Kommentar: " . htmlspecialchars( smart_unescape( $data['comment'])) . "\"";
        print "href=\"javascript:popUp('partinfo.php?pid=". smart_unescape( $data['id']) ."');\">". smart_unescape( $data['name']) ."</a></td>\n";
        // instock/ mininstock
        print "<td class=\"tdrow2\">". smart_unescape( $data['instock']) ."/". smart_unescape( $data['mininstock']) ."</td>\n";
        // footprint
        print "<td class=\"tdrow3\">". smart_unescape( $data['footprint']) ."</td>\n";
        // store location
        print "<td class=\"tdrow4\">". smart_unescape( $data['location']) . "</td>\n";
        // id
		print "<td class=\"tdrow4 idclass\">". smart_unescape( $data['id']) . "</td>\n";
        // datasheet links
        print "<td class=\"tdrow5\">";
        // with icons 
        print "<a title=\"alldatasheet.com\" href=\"http://www.alldatasheet.com/view.jsp?Searchword=". urlencode( smart_unescape( $data['name'])) ."\" target=\"_blank\">".
            "<img class=\"companypic\" src=\"img/partdb/ads.png\" alt=\"logo\">".
            "</a>\n";

        $searchfor = ( strlen( $data['supplierpartnr']) > 0) ? $data['supplierpartnr'] : $data['name'];
        print "<a title=\"Reichelt.de\" href=\"http://www.reichelt.de/?ACTION=4;START=0;SHOW=1;SEARCH=". urlencode( smart_unescape( $searchfor)) ."\" target=\"_blank\">".
            "<img class=\"companypic\" src=\"img/partdb/reichelt.png\" alt=\"logo\">".
            "</a>\n";
        // without icons
        print "<a href=\"http://search.datasheetcatalog.net/key/". urlencode( smart_unescape( $data['name'])) ."\" target=\"_blank\">DC </a>\n";
        // show local datasheet if availible
        $ds_query = "SELECT ".
            "datasheeturl ".
            "FROM datasheets ".
            "WHERE part_id=". smart_escape( $data['id']) 
            ." ORDER BY datasheeturl ASC;";
        $ds_result    = mysql_query( $ds_query) or die( mysql_error());
        $ds_data      = mysql_fetch_assoc( $ds_result);
        if( !empty( $ds_data['datasheeturl']) )
        {
            print "<a href=\"". smart_unescape( $ds_data['datasheeturl']) ."\">Datenblatt</a>\n";
        }
        print "</td>\n";
        
        //build the "-" button, only if more then 0 parts on stock
        print "<td class=\"tdrow6\"><form action=\"\" method=\"post\">";
        print "<input type=\"hidden\" name=\"pid\" value=\"".smart_unescape( $data['id'])."\"/>";
        print "<input type=\"hidden\" name=\"action\"  value=\"r\"/>";
        print "<input type=\"submit\" value=\"-\"";
        if ( $data['instock'] <= 0)
        {
            print " disabled=\"disabled\" ";
        }
        print "/></form></td>\n";
            
        //build the "+" button
        print "<td class=\"tdrow7\"><form action=\"\" method=\"post\">";
        print "<input type=\"hidden\" name=\"pid\" value=\"".smart_unescape( $data['id'])."\"/>";
        print "<input type=\"hidden\" name=\"action\"  value=\"a\"/>";
        print "<input type=\"submit\" value=\"+\"/></form></td>\n";

        print "</tr>\n";
    }

    
    /*
     * no comment
     */
    function GetFormatStrings()
    {
        $aRetVal = array("CSV","CSV Reichelt","CSV Farnell");
        return $aRetVal;
    }
    
    
    /*
     * no comment
     */
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
    
    
    /*
     * no comment
     */
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
    
    /*
     * no comment
     */
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

    

    /* ***************************************************
     * footprint querys
     */
    function footprint_build_tree( $id, $level, $select = -1)
    {
        $query  = "SELECT id, name FROM footprints".
            " WHERE parentnode=". smart_escape( $id).
            " ORDER BY name ASC;";
        $result = mysql_query( $query) or die( mysql_error());
        while ( $data = mysql_fetch_assoc( $result))
        {
            $selected = ($select == $data['id']) ? 'selected': '';
            print "<option ". $selected ." value=\"". smart_unescape( $data['id']) . "\">";
            for ( $i = 0; $i < $level; $i++) 
                print "&nbsp;&nbsp;&nbsp;";
            print smart_unescape( $data['name']).
                "</option>\n";

            // do the same for the next level.
            footprint_build_tree( $data['id'], $level + 1, $select);
        }
    }
    
    function footprint_add( $new, $parent_node = 0)
    {
        $query = "INSERT INTO footprints (name, parentnode) VALUES (".
            smart_escape( $new) .",".
            smart_escape( $parent_node) .");";
        mysql_query( $query) or die( mysql_error());
    }

    function footprint_del( $old)
    {
        // TODO: lock database
        // catch actual parent node
        $query  = "SELECT parentnode FROM footprints".
            " WHERE id=". smart_escape( $old) .";";
        $result = mysql_query( $query) or die( mysql_error());
        $data   = mysql_fetch_assoc( $result);
        $parent = $data['parentnode'];

        // delete footprint
        $query = "DELETE FROM footprints".
            " WHERE id=". smart_escape( $old).
            " LIMIT 1;";
        mysql_query( $query) or die( mysql_error());

        // resort all child footprints to parent node
        $query = "UPDATE footprints SET parentnode=". $parent ." WHERE parentnode=". smart_escape( $old) ." ;";
        mysql_query( $query) or die( mysql_error());
    }
    
    function footprint_rename( $id, $new_name)
    {
        $query = "UPDATE footprints SET name=". smart_escape( $new_name) ." WHERE id=". smart_escape( $id) ." LIMIT 1";  
        mysql_query( $query) or die( mysql_error());
    }

    /*
     * find all nodes below and given node
     */
    function footprint_find_child_nodes( $id)
    {
        $ret_val = array();
        $query   = "SELECT id FROM footprints WHERE parentnode=". smart_escape( $id) .";";
        $result  = mysql_query( $query) or die( mysql_error());
        while ( $data = mysql_fetch_assoc( $result))
        {
            // do the same for the next level.
            $ret_val[] = $data['id'];
            $ret_val   = array_merge( $ret_val, footprint_find_child_nodes( $data['id']));
        }
        return( $ret_val);
    }

    function footprint_new_parent( $id, $new_parent)
    {
        // check if new parent is not anywhere in a child node
        if ( !(in_array( $new_parent, footprint_find_child_nodes( $id))))
        {
            /* do transaction */
            $query = "UPDATE footprints SET parentnode=". smart_escape( $new_parent) ." WHERE id=". smart_escape( $id) ." LIMIT 1;";
            mysql_query( $query) or die( mysql_error());
        }
    }

    function footprint_count()
    {
        $query  = "SELECT count(*) as count FROM footprints;";
        $result = mysql_query( $query) or die( mysql_error());
        $data   = mysql_fetch_array( $result);
        return( $data['count']);
    }
    
    /*
     * parameter: string to search
     * result: id from database
     */
    function footprint_get_id( $footprint)
    {
        $query  = "SELECT id FROM footprints WHERE name=". smart_escape( $footprint) ." LIMIT 1;";
        $result = mysql_query( $query) or die( mysql_error());
        $data   = mysql_fetch_array( $result); 
        return( $data['id']);
    }
    

    /*
     * parameter: string to search
     * result: true  if exists in database
     *         flase if not exist
     */
    function footprint_exists( $footprint)
    {
        $query = "SELECT name FROM footprints WHERE name=". smart_escape( $footprint) .";";
        $res   = mysql_query( $query);
        $data  = mysql_num_rows( $res);

        return( ($data == 1) ? true : false );
    }


    /* ***************************************************
     * supplier querys
     */
    function supplier_add( $new)
    {
        $query = "INSERT INTO suppliers (name) VALUES (". smart_escape( $new) .");";
        mysql_query( $query) or die( mysql_error());
    }

    function supplier_delete( $supplier)
    {
        $query = "DELETE FROM suppliers WHERE id=". smart_escape( $supplier) ." LIMIT 1;";
        mysql_query( $query) or die( mysql_error());
    }
    
    function supplier_rename( $supplier, $new_name)
    {
        $query = "UPDATE suppliers SET name=". smart_escape( $new_name) ." WHERE id=". smart_escape( $supplier) ." LIMIT 1";  
        mysql_query( $query) or die( mysql_error());
    }
                            
    function suppliers_build_list( $select = -1)
    {
        $query  = "SELECT id, name FROM suppliers ORDER BY name ASC;";
        $result = mysql_query( $query);

        while ( $data = mysql_fetch_assoc( $result))
        {
            $selected = ($select == $data['id']) ? 'selected': '';
            print "<option ". $selected ." value=\"". smart_unescape( $data['id']) ."\">". smart_unescape( $data['name']) ."</option>\n";
        }
    }

    function suppliers_count()
    {
        $query  = "SELECT count(*) as count FROM suppliers;";
        $result = mysql_query( $query) or die( mysql_error());
        $data   = mysql_fetch_array( $result);
        return( $data['count']);
    }

    function supplier_get_id( $supplier)
    {
        $query = "SELECT id FROM suppliers WHERE name=". smart_escape( $supplier)." LIMIT 1;";
        $result = mysql_query( $query);
        $result_array = mysql_fetch_array( $result); 
        return( $result_array['id']);
    }

    function supplier_exists( $supplier)
    {
        $query = "SELECT name FROM suppliers WHERE name=". smart_escape( $supplier).";";
        $res   = mysql_query( $query);
        $data  = mysql_num_rows( $res);

        return( ($data == 1) ? true : false );
    }
    
    
    
    /* ***************************************************
     * location querys
     */
    
    /*
     * The buildtree function creates a tree for <select> tags.
     * It recurses trough all locations (and sublocations) and
     * creates the tree. Deeper levels have more spaces in front.
     * As the top-most location (it doesn't exist!) has the ID 0,
     * you have to supply id=0 at the very beginning.
     */
    function location_tree_build( $id = 0, $level = 1, $select = -1, $show_all = true)
    {
        $query_all = ( $show_all) ? '' : ' AND is_full=0';
        $query = "SELECT id, name, is_full FROM storeloc".
            " WHERE parentnode=". smart_escape( $id).
            $query_all.
            " ORDER BY name ASC;";
        $r = mysql_query( $query) or die( mysql_error());
        while ( $data = mysql_fetch_assoc( $r) )
        {
            $selected = ($select == $data['id']) ? 'selected': '';
            print "<option ". $selected ." value=\"". smart_unescape( $data['id']) . "\">";
            for ( $i = 0; $i < $level; $i++) 
                print "&nbsp;&nbsp;&nbsp;";
            print smart_unescape( $data['name']).
                ( $data['is_full'] ? ' [voll]' : '').
                "</option>\n";

            // do the same for the next level.
            location_tree_build( $data['id'], $level + 1, $select, $show_all);
        }
    }


    function location_add( $new_location, $parent_node)
    {
        $query = "INSERT INTO storeloc (name, parentnode) VALUES (".
            smart_escape( $new_location) .",".
            smart_escape( $parent_node)  .");";
        mysql_query( $query) or die( mysql_error());
    }

    function location_count()
    {
        $query  = "SELECT count(*) as count FROM storeloc;";
        $result = mysql_query( $query) or die( mysql_error());
        $data   = mysql_fetch_array( $result);
        return( $data['count']);
    }
    
    function location_get_id( $storeloc)
    {
        $query = "SELECT id FROM storeloc WHERE name=". smart_escape( $storeloc)." LIMIT 1;";
        $result = mysql_query( $query) or die( mysql_error());
        $result_array = mysql_fetch_array( $result); 
        return( $result_array['id']);
    }
    
    function location_get_name( $id)
    {
        $query  = "SELECT name FROM storeloc WHERE id=". smart_escape( $id) .";";
        $result = mysql_query( $query) or die( mysql_error());
        $data   = mysql_fetch_assoc( $result);
        return( $data['name']);
    }

    function location_exists( $storeloc)
    {
        $query  = "SELECT name FROM storeloc WHERE name=". smart_escape( $storeloc).";";
        $result = mysql_query( $query) or die( mysql_error());
        $data   = mysql_num_rows( $result);

        return( ($data == 1) ? true : false );
    }
    
    
    
    /* ***************************************************
     * categorie querys
     */
    
    /*
     * The buildtree function creates a tree for <select> tags.
     * It recurses trough all categories (and subcategories) and
     * creates the tree. Deeper levels have more spaces in front.
     * As the top-most category (it doesn't exist!) has the ID 0,
     * you have to supply cid=0 at the very beginning.
     */
    function categories_build_tree( $cid = 0, $level = 1, $select = -1)
    {
        $query = "SELECT id, name FROM categories".
            " WHERE parentnode=". smart_escape( $cid).
            " ORDER BY name ASC;";
        $result = mysql_query( $query) or die( mysql_error());
        while ( $data = mysql_fetch_assoc( $result))
        {
            $selected = ($select == $data['id']) ? 'selected': '';
            print "<option ". $selected ." value=\"". smart_unescape( $data['id']) . "\">";
            for ($i = 0; $i < $level; $i++)
                print "&nbsp;&nbsp;&nbsp;";
            print smart_unescape( $data['name']) ."</option>\n";
            // do the same for the next level.
            categories_build_tree( $data['id'], $level + 1, $select);
        }
    }
    
    /* This recursive procedure builds the tree of categories.
       There's nothing special about it, so no more comments.
       Warning: Infinite recursion can occur when the DB is
       corrupted! But normally everything should be fine. */
    function categories_build_navtree( $pid = 0)
    {
        $query  = "SELECT id, name FROM categories".
            " WHERE parentnode=". smart_escape( $pid).
            " ORDER BY categories.name ASC;";
        if ( $result = mysql_query( $query))
        {
            while ( $data = mysql_fetch_assoc( $result))
            {
                print "cat_tree.add(". smart_unescape( $data['id']) .",".
                    smart_unescape( $pid) .",'".
                    smart_unescape( $data['name']).
                    "','showparts.php?cid=". 
                    smart_unescape( $data['id']).
                    "&type=index\"','','content_frame');\n";
                categories_build_navtree( $data['id']);
            }
        }
    }

    function category_del( $old)
    {
        // TODO: lock database
        // catch actual parent node
        $query  = "SELECT parentnode FROM categories".
            " WHERE id=". smart_escape( $old) .";";
        $result = mysql_query( $query) or die( mysql_error());
        $data   = mysql_fetch_assoc( $result);
        $parent = $data['parentnode'];

        // delete footprint
        $query = "DELETE FROM categories".
            " WHERE id=". smart_escape( $old).
            " LIMIT 1;";
        mysql_query( $query) or die( mysql_error());

        // resort all child footprints to parent node
        $query = "UPDATE categories SET parentnode=". $parent ." WHERE parentnode=". smart_escape( $old) ." ;";
        mysql_query( $query) or die( mysql_error());
    }
    
    /*
     * find all nodes below and given node
     */
    function categories_find_child_nodes( $id)
    {
        $ret_val = array();
        $query   = "SELECT id FROM categories WHERE parentnode=". smart_escape( $id) .";";
        $result  = mysql_query( $query) or die( mysql_error());
        while ( $data = mysql_fetch_assoc( $result))
        {
            // do the same for the next level.
            $ret_val[] = $data['id'];
            $ret_val   = array_merge( $ret_val, categories_find_child_nodes( $data['id']));
        }
        return( $ret_val);
    }

    function categories_or_child_nodes( $cid, $with_subcategories = true)
    {
        $ret_val = "id_category=". smart_escape( $cid);
        if ($with_subcategories)
        {
            $query   = "SELECT id FROM categories WHERE parentnode=". smart_escape( $cid) .";";
            $result  = mysql_query( $query);
            while ( $data = mysql_fetch_assoc( $result))
            {
                $ret_val .= " OR ". categories_or_child_nodes( smart_unescape( $data['id']));
            }
        }
        return( $ret_val);
    }

    function categories_count()
    {
        $query  = "SELECT count(*) as count FROM categories;";
        $result = mysql_query( $query) or die( mysql_error());
        $data   = mysql_fetch_array( $result);
        return( $data['count']);
    }

    function category_get_id( $categorie)
    {
        $query = "SELECT id FROM categories WHERE name=". smart_escape( $categorie)." LIMIT 1;";
        $result = mysql_query( $query) or die( mysql_error());
        $result_array = mysql_fetch_array( $result); 
        return( $result_array['id']);
    }

    /*
     * Given the category id this helper-function does a lookup
     * and returns the name of the category. At the moment we
     * assume that the category id is valid. FIXME
     */
    function category_get_name( $id)
    {
        $query  = "SELECT name FROM categories WHERE id=". smart_escape($id) .";";
        $result = mysql_query( $query) or die( mysql_error());
        $data   = mysql_fetch_assoc( $result);

        return( ( $id == 0) ? "Alles" : smart_unescape( $data['name'] ));
    }
    
    function category_exists( $categorie)
    {
        $query  = "SELECT name FROM categories WHERE name=". smart_escape( $categorie) .";";
        $result = mysql_query( $query) or die( mysql_error());
        $data   = mysql_num_rows( $result);

        return( ($data == 1) ? true : false );
    }
    
    
    
    /* ***************************************************
     * part querys
     */
    
    function parts_count()
    {
        $query  = "SELECT count(*) as count FROM parts;";
        $result = mysql_query( $query) or die( mysql_error());
        $data   = mysql_fetch_array( $result);
        return( $data['count']);
    }

    // determine category
    function part_get_category_id( $part_id)
    {
        $cat    = 0;
        $query  = "SELECT id_category FROM parts WHERE id=". smart_escape( $part_id) .";";
        var_dump( $query);
        $result = mysql_query( $query) or die( mysql_error());
        if (mysql_num_rows( $result) > 0)
        {
            $data = mysql_fetch_assoc( $result);
            $cat  = $data['id_category'];
        }
        return( $cat);
    }


    /* ***************************************************
     * devices querys
     */
    
    function devices_count()
    {
        $query  = "SELECT count(*) as count FROM devices;";
        $result = mysql_query( $query) or die( mysql_error());
        $data   = mysql_fetch_array( $result);
        return( $data['count']);
    }
    
?>
