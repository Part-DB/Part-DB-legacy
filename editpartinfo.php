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

	$Id: editpartinfo.php,v 1.5 2006/03/09 15:08:09 cl Exp $

	28/02/06
		Some changes concerning escape/unescape stuff.
		Added some comments, too.
	
	04/03/06
		Added some code for img managing.
	
	05/03/06
		Added security questions: "Do you REALLY want to ..."
*/
	include('lib.php');
	partdb_init();

	/*
	 * If there's a confirmation question or if the part has been
	 * deleted, don't output the normal dialog but something else.
	 * special_dialog == 0: output normal stuff
	 * special_dialog != 0: don't output normal stuff
	 */
	$special_dialog = 0;

	if ( strcmp ($_REQUEST["action"], "edit") == 0 )
	{
		$query = "UPDATE parts SET name=". smart_escape($_REQUEST["p_name"]) .",instock=". smart_escape($_REQUEST["p_instock"]) .", mininstock=". smart_escape($_REQUEST["p_mininstock"]) .", id_footprint=". smart_escape($_REQUEST["p_footprint"]) .", id_storeloc=". smart_escape($_REQUEST["p_storeloc"]) .", id_supplier=". smart_escape($_REQUEST["p_supplier"]) .", supplierpartnr=". smart_escape($_REQUEST["p_supplierpartnr"]) .", comment=". smart_escape($_REQUEST["p_comment"]) ." WHERE id=". smart_escape($_REQUEST["pid"]) ." LIMIT 1;";
		debug_print ($query);
		mysql_query ($query);
		print "<script>window.close();</script>";
	}
    else if ( strcmp ($_REQUEST["action"], "edit_category") == 0 )
    {
		$query = "UPDATE parts SET id_category=". smart_escape($_REQUEST["p_category"]) ." WHERE id=". smart_escape($_REQUEST["pid"]) ." LIMIT 1;";
        debug_print ($query);
        mysql_query ($query);
		print "<script>window.close();</script>";
    }
	else if ( strcmp ($_REQUEST["action"], "ds_add") == 0 )
	{
		$query = "INSERT INTO datasheets (part_id,datasheeturl) VALUES (". smart_escape($_REQUEST["pid"]) .",". smart_escape($_REQUEST["ds_url"]) .");";
		debug_print ($query);
		mysql_query ($query);
	}
	else if ( strcmp ($_REQUEST["action"], "ds_del") == 0 )
	{
		$query = "DELETE FROM datasheets WHERE id=". smart_escape($_REQUEST["ds_id"]) ." LIMIT 1;";
		debug_print ($query);
		mysql_query ($query);
	}
	else if (strcmp ($_REQUEST["action"], "part_del") == 0)
	{
		if ((! isset($_REQUEST["del_ok"])) && (! isset($_REQUEST["del_nok"])) )
		{
			/* display the confirmation text */
			$special_dialog = 1;
			print "<html><body class=\"body\"><link rel=\"StyleSheet\" href=\"css/partdb.css\" type=\"text/css\" /><div style=\"text-align:center;\">";
			print "<table class=\"table\">";
			print "<tr><td class=\"tdtop\"><div style=\"color:red;\">M&ouml;chten Sie das Bauteil &quot;". lookup_part_name ($_REQUEST["pid"]) ."&quot; wirklich l&ouml;schen? </td></tr>";
			print "<tr><td class=\"tdtext\"><table><tr><td></div>Der L&ouml;schvorgang ist irreversibel!</td></tr>";
			print "<tr><td><form action=\"editpartinfo.php\" method=\"post\"><input type=\"hidden\" name=\"pid\" value=\"". $_REQUEST["pid"] ."\"></td></tr>";
			print "<tr><td><input type=\"hidden\" name=\"action\" value=\"part_del\"><input type=\"submit\" name=\"del_nok\" value=\"Nicht L&ouml;schen!\"><input type=\"submit\" name=\"del_ok\" value=\"L&ouml;schen!\"></td></tr>";
			print "</table></td></tr></table></form></div></body></html>";
		}
		else if (isset($_REQUEST["del_ok"]))
		{
			/* the user said it's OK to delete the part ... */
			// no LIMIT here because every part can have multiple datasheets
			$query = "DELETE FROM datasheets WHERE part_id=". smart_escape($_REQUEST["pid"]) .";";
			debug_print ($query);
			mysql_query ($query);
			$query = "DELETE FROM parts WHERE id=". smart_escape($_REQUEST["pid"]). " LIMIT 1";
			debug_print ($query);
			mysql_query ($query);
			$special_dialog = 1;
			print "<script>window.close();</script>";
		}
	}
	else if ( strcmp ($_REQUEST["action"], "img_mgr") == 0 )
	{
		/*
		 * Set the default ("master") picture.
		 * The master picture is the picture whose thumbnail
		 * is shown in the part list.
		 */
		if (isset($_REQUEST["default_img"]))
		{
			$query = "UPDATE pictures SET pict_masterpict=0 WHERE part_id=". smart_escape($_REQUEST["pid"]) .";";
			debug_print ($query);
			mysql_query ($query);
			$query = "UPDATE pictures SET pict_masterpict=1 WHERE id=". smart_escape($_REQUEST["default_img"]) .";";
			debug_print ($query);
			mysql_query ($query);
		}	
		/* check if the user wants to delete an image */
		if (isset($_REQUEST["del_img"]))
		{
			$img_del_id_array = $_REQUEST["del_img"];
			if ((! isset($_REQUEST["del_ok"])) && (! isset($_REQUEST["del_nok"])) )
			{
				/* print the confirmation text */
				$special_dialog = 1;
				print "<html><body class=\"body\"><link rel=\"StyleSheet\" href=\"css/partdb.css\" type=\"text/css\"/>";
				print "<table class=\"table\">";
				print "<tr><td class=\"tdtop\"><div style=\"color:red\">M&ouml;chten Sie das ausgew&auml;hlte Bild/die ausgew&auml;hlen Bilder wirklich l&ouml;schen?</div></td></tr>";
				print "<tr><td class=\"tdtext\"><table><tr><td>Der L&ouml;schvorgang ist irreversibel!</td></tr>";
				print "<tr><td><form action=\"editpartinfo.php\" method=\"post\"><input type=\"hidden\" name=\"pid\" value=\"". $_REQUEST["pid"] ."\"><input type=\"hidden\" name=\"action\" value=\"img_mgr\">";
				for ($i = 0; $i < count($img_del_id_array); $i++)
				{
					print "<input type=\"hidden\" name=\"del_img[]\" value=\"". $img_del_id_array[$i] ."\">";
					print "<input type=\"submit\" name=\"del_nok\" value=\"Nicht L&ouml;schen!\"><input type=\"submit\" name=\"del_ok\" value=\"L&ouml;schen!\"></form></div></body></html>";
				}
				print "</form></td></tr></table></td></tr></table>";
			}
			else if (isset($_REQUEST["del_ok"]))
			{
				/* user OK'd the action ...*/
				for ($i = 0; $i < count($img_del_id_array); $i++)
				{
					// delete only the images, the thumbsnails will expire automatically
					$query = "DELETE FROM pictures WHERE id=". smart_escape($img_del_id_array[$i]) ." LIMIT 1;";
					debug_print ($query);
					mysql_query ($query);
				}
			}
		}
	}
	else if ( strcmp ($_REQUEST["action"], "img_add") == 0 )
	{
		if (is_uploaded_file($_FILES['uploaded_img']['tmp_name']))
		{
			/*
			 * split the file name into its parts and create
			 * a unique filename.
			 */
			$a = explode(".",$_FILES['uploaded_img']['name']);
			$fname = "img_";
			$fname .= md5_file($_FILES['uploaded_img']['tmp_name']);
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
			move_uploaded_file($_FILES['uploaded_img']['tmp_name'], "img/".$fname);
			chmod ("img/" .$fname, 0664);
			$query = "INSERT INTO pictures (part_id,pict_fname) VALUES (". smart_escape($_REQUEST["pid"]) .",". smart_escape($fname) .")";
			debug_print($query);
			mysql_query($query);
		}
	}
	else if ( strcmp ($_REQUEST["action"], "price_del") == 0 )
	{
		/*
		 * If everythink is OK (DB consistency, no bugs in the
		 * software, ...) every part only has one price "tag".
		 * So we add LIMIT 1 to protect from run-away queries.
		 */
		$query = "DELETE FROM preise WHERE part_id=". smart_escape($_REQUEST["pid"]) ." LIMIT 1;";
		debug_print($query);
		mysql_query($query);
	}
	else if ( strcmp ($_REQUEST["action"], "price_add") == 0 )
	{
		/*
		 * See if the price is a valid (floating point) number ...
		 * Actually this code snippet only checks for the beginning,
		 * not the entire text. I'm no RegEx expert, so maybe someone
		 * could replace this with a better one!
		 * (http://www.regular-expressions.info/floatingpoint.html)
		 */
		if (ereg("^[-+]?[0-9]*\.?[0-9]+", $_REQUEST["price"]) == true)
		{
			$_REQUEST["price"] = str_replace(',', '.', $_REQUEST["price"]);
			/* Before adding the new price, delete the old one! */
			$query = "DELETE FROM preise WHERE part_id=". smart_escape($_REQUEST["pid"]) ." LIMIT 1;";
			debug_print($query);
			mysql_query($query);
			$query = "INSERT INTO preise (part_id,ma,preis,t) VALUES (". smart_escape($_REQUEST["pid"]) .", 1, ". smart_escape($_REQUEST["price"]) .", NOW());";
			debug_print($query);
			mysql_query($query);
		}
	}
	if ($special_dialog == 0)
	{	
	?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">

<html>
  <head>
   <link rel="StyleSheet" href="css/partdb.css" type="text/css" />
  </head>

 <body class="body">

<table class="table">
	<tr>
		<td class="tdtop">
		&Auml;ndere Detailinfos von &quot;<?PHP print lookup_part_name ($_REQUEST["pid"]); ?>&quot;
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
				eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=1, scrollbars=1, location=1, 		statusbar=1, menubar=1, resizable=1, width=600, height=400');");
			}
			// -->
			</script>
			<form action="editpartinfo.php" method="get">
			<input type="hidden" name="pid" value="<?PHP print $_REQUEST["pid"]; ?>">
			<table>
				<?PHP  
				$query = "SELECT parts.id,parts.name,parts.instock,parts.mininstock,parts.id_footprint,parts.id_storeloc,parts.id_supplier AS 'supplier',parts.supplierpartnr,parts.comment FROM parts WHERE parts.id=". smart_escape($_REQUEST["pid"]) ." LIMIT 1;";
				debug_print ($query);
				$r = mysql_query ($query);
			while ( ($d = mysql_fetch_row ($r)) )
			{
			print "<tr><td><b>Name:</b></td><td><input name='p_name' value='". smart_unescape($d[1]) ."'></td></tr>";
			print "<tr><td><b>Vorhanden:</b></td><td><input name='p_instock' value='". smart_unescape($d[2]) ."'></td></tr>";
			print "<tr><td><b>Min. Bestand:</b></td><td><input name='p_mininstock' value='". smart_unescape($d[3]) ."'></td></tr>";
			print "<tr><td><b>Footprint:</b></td><td><select name='p_footprint'>";
			// warning: hax0r style below!
			$query = "SELECT id,name FROM footprints ORDER BY name ASC";
			debug_print($query);
			$r_fp = mysql_query($query);
			$ncol = mysql_num_rows($r_fp);
			for ($i = 0; $i < $ncol; $i++)
			{
				$d_fp = mysql_fetch_row($r_fp);
				// the current footprint is the default value!
				if ($d_fp[0] == $d[4])
				print "<option selected value=\"". smart_unescape($d_fp[0]) ."\">". smart_unescape($d_fp[1]) ."</option>\n";
			else
				print "<option value=\"". smart_unescape($d_fp[0]) ."\">". smart_unescape($d_fp[1]) ."</option>\n";
			}
			print "</select></td></tr>";
			print "<tr><td><b>Lagerort:</b></td><td><select name='p_storeloc'>";
			// warning: hax0r style below!
			$query = "SELECT id,name FROM storeloc ORDER BY name ASC";
			debug_print($query);
			$r_loc = mysql_query($query);
			$ncol = mysql_num_rows($r_loc);
			for ($i = 0; $i < $ncol; $i++)
			{
				$d_loc = mysql_fetch_row($r_loc);
				// the current storage location is the default one!
				if ($d_loc[0] == $d[5])
				print "<option selected value=\"". smart_unescape($d_loc[0]) ."\">". smart_unescape($d_loc[1]) ."</option>\n";
			else
				print "<option value=\"". smart_unescape($d_loc[0])."\">". smart_unescape($d_loc[1]) ."</option>\n";
			}
			print "</select></td></tr>";
			print "<tr><td><b>Lieferant:</b></td><td><select name='p_supplier'>";
			// warning: hax0r style below!
			$query = "SELECT id,name FROM suppliers ORDER BY name ASC";
			debug_print($query);
			$r_sup = mysql_query($query);
			$ncol = mysql_num_rows($r_sup);
			for ($i = 0; $i < $ncol; $i++)
			{
				$d_sup = mysql_fetch_row($r_sup);
				if ($d_sup[0] == $d[6])
				print "<option selected value=\"". smart_unescape($d_sup[0])."\">". smart_unescape($d_sup[1]) ."</option>\n";
			else
				print "<option value=\"". smart_unescape($d_sup[0]) ."\">". smart_unescape($d_sup[1]) ."</option>\n";
			}
			print "</select></td></tr>";
			print "<tr><td><b>Bestell-Nr.:</b></td><td><input name='p_supplierpartnr' value='". smart_unescape($d[7]) ."'></td></tr>";
			print "<tr><td valign='top'><b>Kommentar:</b></td><td><textarea name='p_comment'>". smart_unescape($d[8]) ."</textarea></td></tr>";
			}
			?>
			<tr><td><input type="hidden" name="action" value="edit"><input type="submit" value="&Auml;ndern!"></td></tr>
			</form>
			<?PHP
			function buildtree ($cid, $level, $select)
			{
				$query = "SELECT id,name FROM categories WHERE parentnode=". smart_escape($cid) .";";
				$r = mysql_query ($query);
			while ( $d = mysql_fetch_row ($r) )
			{
			if ($select == $d[0])
				print "<option selected value=\"". smart_unescape($d[0]) . "\">";
			else
				print "<option value=\"". smart_unescape($d[0]) . "\">";
			for ($i = 0; $i < $level; $i++) print "&nbsp;&nbsp;&nbsp;";
			print smart_unescape($d[1]) ."</option>";
			// do the same for the next level.
			buildtree ($d[0], $level + 1, $select);
			}
			}
			// determine category
			$query = "SELECT id_category FROM parts WHERE id=". $_REQUEST["pid"] .";";
			$r = mysql_query($query);
			if (mysql_num_rows($r) > 0)
			{
				$d = mysql_fetch_row($r);
				$cat = $d[0];
			}
			else
			$cat = 0;
			?>
			<form  action="editpartinfo.php" method="get">
			<tr><td></br>
			<a><b>Kategorie:</b></a></br></br>
	        	<input type="hidden" name="pid" value="<? print $_REQUEST["pid"]; ?>">
			<td><select name='p_category'>
			<option value="0">root node</option>
			<? buildtree(0, 1, $cat); ?>
			</td></tr>
			<tr><td><input type="hidden" name="action" value="edit_category"><input type="submit" value="&Auml;ndern!"></td></tr>
			</table>
			</form>
			<tr><td>
		</td>
	</tr>
</table>

<table class="tablenone">
</br>
</table>

<table class="table">
	<tr>
		<td class="tdtop">
		Preisinfos
		</td>
	</tr>
	<tr>
		<td class="tdtext">
			<?PHP
			$q = "SELECT id,preis,ma FROM preise WHERE part_id=". smart_escape($_REQUEST["pid"]) ." ORDER BY ma DESC;";
			debug_print($q);
			$r = mysql_query($q);
			if (mysql_num_rows($r) > 0)
			{
			/*
			* There's some information in the table ...
			* Because we assume that only one entry is possible,
			* we display the manual entry, if there's one manual
			* and one automatically added entry entry.
			*/
			$d = mysql_fetch_row($r);
			$d[1] = str_replace('.', ',', $d[1]);
			print "<b>Preis:</b> ". smart_unescape($d[1]);
			if ($d[2] == 1)
			{
			//print " (manuell)";
			print " &euro;";
			}
			}
			else
			{
			print "Keine Preisinfos vorhanden!";
			}
			?>
			<p><form action="editpartinfo.php" method="get">
			<input type="hidden" name="pid" value="<?PHP print $_REQUEST["pid"]; ?>">
			<input type="hidden" name="action" value="price_del">
			<input type="submit" value="L&ouml;sche Preisinfo!">
			</form>
			<form action="editpartinfo.php" method="get">
			<input type="hidden" name="pid" value="<?PHP print $_REQUEST["pid"]; ?>">
			<input type="hidden" name="action" value="price_add">
			<b>Preis:</b> <input type="edit" name="price" size="8"></br></br>
			<input type="submit" value="Preiseingabe!">
			</form>
			</td></tr>
		</td>
	</tr>
</table>

<table class="tablenone">
</br>
</table>

<table class="table">
	<tr>
		<td class="tdtop">
		Bilder
		</td>
	</tr>
	<tr>
		<td class="tdtext">
			<table>
			<tr><td><b>Bilder</b></td></tr>
			<tr><td>
			<?PHP
			if (has_image($_REQUEST["pid"]))
			{
				// there's at least one picture
				?>
				<form action="editpartinfo.php" method="get">
				<input type="hidden" name="pid" value="<?PHP print $_REQUEST["pid"]; ?>">
				<input type="hidden" name="action" value="img_mgr">
				<tr>
				<td>&nbsp;</td><td>&quot;Master-Bild&quot;</td><td>L&ouml;schen</td>
				</tr>
				<?PHP
				$query = "SELECT id FROM pictures WHERE ((pictures.pict_type='P') AND (pictures.part_id=". smart_escape($_REQUEST["pid"]) .")) ORDER BY pictures.pict_masterpict DESC, pictures.id ASC;";
				debug_print($query);
				$r_img = mysql_query($query);
				$ncol = mysql_num_rows($r_img);
				for ($i = 0; $i < $ncol; $i++)
			{
				$d_img = mysql_fetch_row($r_img);
				print "<tr>";
				print "<td><a href=\"javascript:popUp('getimage.php?pict_id=". smart_unescape($d_img[0]). "');\"><img src=\"getimage.php?pict_id=". smart_unescape($d_img[0]). "&maxx=200&maxy=150\"></a></td>";
				print "<td><input type=\"radio\" name=\"default_img\" value=\"". smart_unescape($d_img[0]) ."\"></td>";
				print "<td><input type=\"checkbox\" name=\"del_img[]\" value=\"". smart_unescape($d_img[0]). "\"></td>";
				print "</tr>";
			}
			?>
			<tr><td><input type="submit" value="F&uuml;hre &Auml;nderungen durch!"></td></tr>
			</form>
			<?PHP
			}
			else
			{
				// no pictures for this part
				print "Kein Bild vorhanden!";
			}
			?>
			</td></tr>
			<tr><td>
			Hier k&ouml;nnen Sie Bilder hochladen. Im Moment werden nur JPG Dateien unterst&uuml;tzt.
			<form enctype="multipart/form-data" action="editpartinfo.php" method="post">
			<input type="hidden" name="pid" value="<?PHP print $_REQUEST["pid"]; ?>">
			<input type="hidden" name="action" value="img_add">
			<input type="file" name="uploaded_img">
			<input type="submit" value="Lade Bild hoch!">
			</form>
			</td></tr>
			</table>
		</td>
	</tr>
</table>

<table class="tablenone">
</br>
</table>

<table class="table">
	<tr>
		<td class="tdtop">
		Datenbl&auml;tter
		</td>
	</tr>
	<tr>
		<td class="tdtext">
			<table>
			<tr><td>
			<form action="editpartinfo.php" method="get">
			<select name="ds_id" size="5">
			<?PHP
			$query = "SELECT id,datasheeturl FROM datasheets WHERE part_id=". smart_escape($_REQUEST["pid"]) .";";
			debug_print($query);
			$r = mysql_query($query);
			$ncol = mysql_num_rows($r);
			for ($i = 0; $i < $ncol; $i++)
			{
				$d = mysql_fetch_row ($r);
				if ($i == 0)
				print "<option selected value=\"". smart_unescape($d[0]) ."\">". smart_unescape($d[1]) ."</option>";
			else
				print "<option value=\"". smart_unescape($d[0]) ."\">". smart_unescape($d[1]) ."</option>";
			}
			?>
			</select>
			<input type="hidden" name="pid" value="<?PHP print $_REQUEST["pid"]; ?>">
			<input type="hidden" name="action" value="ds_del">&nbsp;&nbsp;&nbsp;
			<input type="submit" value="Ausgew&auml;hltes l&ouml;schen!">
			</form>
			</td></tr>
			<tr><td>
			<form action="editpartinfo.php" method="get">
			URL des hinzuf&uuml;genden Datenblattes:
			<input type="edit" name="ds_url" value="" width="40">
			<input type="hidden" name="pid" value="<?PHP print $_REQUEST["pid"]; ?>">
			<input type="hidden" name="action" value="ds_add">&nbsp;&nbsp;&nbsp;
			<input type="submit" value="Hinzuf&uuml;gen!">
			</form>
			Hinweis:<br>
			Wenn das Datenblatt unter C:\datasheets\foo.pdf zu finden ist, geben Sie als URL file:///C:/datasheets/foo.pdf an! Dies scheint allerdings nicht mit allen Browser-Versionen und Acrobat-Reader-Versionen zu funktionieren.</br>
			</td></tr>
			</table>
		</td>
	</tr>
</table>

<table class="tablenone">
</br>
</table>

<table class="table">
	<tr>
		<td class="tdtop">
		Bauteiel L&ouml;schen
		</td>
	</tr>
	<tr>
		<td class="tdtext">
			<table>
			<tr><td>
			<form action="editpartinfo.php" method="get">
			<input type="hidden" name="pid" value="<?PHP print $_REQUEST["pid"]; ?>">
			<input type="hidden" name="action" value="part_del">
			<tr>
			<td>
			Der L&ouml;schvorgang ist nicht r&uuml;ckg&auml;ngig zu machen!
			<p><input type="submit" value="L&ouml;sche Teil!">
			</td>
			</tr>
			</form>
			</table>
		</td>
	</tr>

  </table>
 </body>
</html>

<?PHP
	}
?>
