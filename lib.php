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

		// if it's no integer, quote it
		if (!is_numeric($value)) {
			$value = "'". mysql_escape_string($value) ."'";
		}

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
?>
