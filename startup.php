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

	$Id: startup.php,v 1.4 2006/05/28 10:28:57 cl Exp $

	28/05/06
		If all requirements regarding (locs, footprints, categories
		and suppliers) are met (at least one of each), hide the
		warning. Only if something's wrong the warning pops up, now
		the text color has been changed to red. Some people suggested
		this long ago ...
*/
	include("lib.php");
	partdb_init();

	/*
	 * This variable determines wheater the user is reminded to add
	 * add least one loc, one footprint, one category and one supplier.
	 */
	$display_warning = 0;

	$q = "SELECT id FROM storeloc LIMIT 1;";
	//debug_print($q);
	$r = mysql_query($q);
	if (! mysql_num_rows($r))
	{
		$display_warning |= 1;
	}

	$q = "SELECT id FROM footprints LIMIT 1;";
	debug_print($q);
	$r = mysql_query($q);
	if (! mysql_num_rows($r))
	{
		$display_warning |= 1;
	}

	$q = "SELECT id FROM categories LIMIT 1;";
	debug_print($q);
	$r = mysql_query($q);
	if (! mysql_num_rows($r))
	{
		$display_warning |= 1;
	}

	$q = "SELECT id FROM suppliers LIMIT 1;";
	debug_print($q);
	$r = mysql_query($q);
	if (! mysql_num_rows($r))
	{
		$display_warning |= 1;
	}
?>
<html>
 <body class="body">
  <head>
   <link rel="StyleSheet" href="css/partdb.css" type="text/css" />

<table class="table">
	<tr>
		<td class="tdstartup">
		  <img src="img/partdb/partdb.png"></img><b>Part-DB V0.1.5</b><img src="img/partdb/partdb.png"></img>
		</td>
	</tr>
</table>

<table class="tablenone">
</br>
</table>


<?PHP	// Display Warning 
	if ($display_warning)
	{
?>
<table class="table">
	 <tr>
		  <td class="tdtop">
		    Warning !!!
		  </td>
	</tr>
	<tr>
		 <td class="tdtext">
		    Beachten Sie bitte, dass Sie vor der Verwendung der jeweils mindestens</br>
		    einen Lagerort</br>
		    ein Footprint</br>
		    eine Kategorie</br>
		    und einen Lieferanten</br>
		    hinzuf&uuml;gen m&uuml;ssen. Die Tools hierf&uuml;r finden Sie links.</br>
		 </td>
	</tr>
</table>
<?PHP
	}
?>

<table class="table">
	<tr>
		<td class="tdtop">
		Lizenz
		</td>
	</tr>
		<td class="tdtext">
		  Part-DB, Copyright (C) 2005 of Christoph Lechner. Part-DB is published under the GPL, so it comes with ABSOLUTELY NO WARRANTY, click <a href="readme/gpl.txt">here</a> for details. This is free software, and you are welcome to redistribute it under certain conditions. Click <a href="readme/gpl.txt">here</a> for details.</br>
		  </br> 
		  The first Author's Homepage <a href="http://www.cl-projects.de/">http://www.cl-projects.de/</a></br>
		  </br>
		  Author since 2009 by K.Jacobs - <a href="http://www.grautier.com/">http://grautier.com</a></br>
		  ajfrenzel		 	Committer</br>
		  tgrziwa 			Committer</br>
		  d.lipschinski 		Committer</br>
		  </br>
		  <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		  <input type="hidden" name="cmd" value="_donations">
		  <input type="hidden" name="business" value="theborg@grautier.com">
		  <input type="hidden" name="lc" value="DE">
		  <input type="hidden" name="item_name" value="Part-DB">
		  <input type="hidden" name="no_note" value="0">
		  <input type="hidden" name="currency_code" value="EUR">
		  <input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHostedGuest">
		  <input type="image" src="https://www.paypal.com/de_DE/DE/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal.">
		  <img alt="" border="0" src="https://www.paypal.com/de_DE/i/scr/pixel.gif" width="1" height="1"></br>
</form>

		  </td>
	</tr>
</table>

<table class="tablenone">
</br>
</table> 

<table class="table">
	<tr>
		<td class="tdtop">
		Updates
		</td>
	</tr>
	<tr>
		<td class="tdtextsmall">
			</br>
			<?PHP
			$rss_file = join ( ' ', file ("http://code.google.com/feeds/p/part-db/downloads/basic"));
			$rss_zeilen = array ( "title", "updated", "id" );
			$rss_array = explode ( "<entry>", $rss_file );
			foreach ( $rss_array as $string ) {
			foreach ( $rss_zeilen as $zeile ) {
			preg_match_all ( "|<$zeile>(.*)</$zeile>|Usim", $string, $preg_match );
			$$zeile = $preg_match [1] [0];
			#if ($zeile = "id") 
			 #{
			 #echo "<a href=\"" . $$zeile . "\">" . $$zeile . "</a></br>";
			 #}
			 #else
			 #{
			 echo "" . $$zeile . "</br>";
			 #}
			} 
			echo "</br>";
			}
			?>
			</br>
		</td>
	</tr>
</table>

<table class="tablenone">
</br>
</table>

  </head>
 </body>
</html>


