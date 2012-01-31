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

  include ('db_update.php');
  // catch output to do fine formating later
  ob_start();
  if ( checkDBUpdateNeeded())
  {
    $ver = getDBVersion();
    print "DBVersion: ".$ver.", ben&ouml;tigt ein Update.<br><br>";
    doDBUpdate();
  }
  $database_update = ob_get_contents();
  ob_end_clean();

    include("config.php");
//    include("lib.php");
    partdb_init();

    /*
     * This variable determines wheater the user is reminded to add
     * add least one loc, one footprint, one category and one supplier.
     */
    $display_warning = false;
    // predefines
    $good = "&#x2714; ";
    $bad  = "&#x2718; ";
    // defaults
    $missing_storeloc  = $good;
    $missing_footprint = $good;
    $missing_category  = $good;
    $missing_supplier  = $good;

    $q = "SELECT id FROM storeloc LIMIT 1;";
    debug_print($q);
    $r = mysql_query($q) or die ("MySQL-Fehler: " . mysql_error());
    if (! mysql_num_rows($r))
    {
        $display_warning  = true;
        $missing_storeloc = $bad;
    }

    $q = "SELECT id FROM footprints LIMIT 1;";
    debug_print($q);
    $r = mysql_query($q);
    if (! mysql_num_rows($r))
    {
        $display_warning   = true;
        $missing_footprint = $bad;
    }

    $q = "SELECT id FROM categories LIMIT 1;";
    debug_print($q);
    $r = mysql_query($q);
    if (! mysql_num_rows($r))
    {
        $display_warning  = true;
        $missing_category = $bad;
    }

    $q = "SELECT id FROM suppliers LIMIT 1;";
    debug_print($q);
    $r = mysql_query($q);
    if (! mysql_num_rows($r))
    {
        $display_warning  = true;
        $missing_supplier = $bad;
    }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Startup</title>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
</head>
<body class="body">

<table class="table">
    <tr>
        <td class="tdstartup">
          <img src="img/partdb/partdb.png" alt="logo"><b><?php print $startup_title ?></b><img src="img/partdb/partdb.png" alt="logo">
        </td>
    </tr>
</table>

<?PHP   // Display Warning 
    if ($display_warning)
    {
?>
<br>

<table class="table">
    <tr>
          <td class="tdtop">
            Achtung!
          </td>
    </tr>
    <tr>
        <td class="tdtext">
          Bitte beachten Sie, dass vor der Verwendung der Datenbank mindestens<br>
          <blockquote><?php print $missing_storeloc  ?>ein     <a href="locmgr.php" target="_content_frame">Lagerort</a>  </blockquote>
          <blockquote><?php print $missing_footprint ?>ein     <a href="fpmgr.php"  target="_content_frame">Footprint</a> </blockquote>
          <blockquote><?php print $missing_category  ?>eine    <a href="catmgr.php" target="_content_frame">Kategorie</a> </blockquote>
          <blockquote><?php print $missing_supplier  ?>und ein <a href="supmgr.php" target="_content_frame">Lieferant</a> </blockquote>
          hinzuf&uuml;gt werden muss.
        </td>
    </tr>
</table>
<?PHP } ?>


<?PHP   // display database update 
    if ( strlen( $database_update) > 0)
    {
?>
<br>

<table class="table">
    <tr>
          <td class="tdtop">
            Datenbankupdate
          </td>
    </tr>
    <tr>
        <td class="tdrowred">
        <?php print $database_update; ?>
        </td>
    </tr>
</table>
<?PHP } ?>


<br>

<table class="table">
    <tr>
        <td class="tdtop">
        Lizenz
        </td>
    </tr>
    <tr>
        <td class="tdtext">
          <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
          <input type="hidden" name="cmd" value="_donations">
          <input type="hidden" name="business" value="theborg@grautier.com">
          <input type="hidden" name="lc" value="DE">
          <input type="hidden" name="item_name" value="Part-DB">
          <input type="hidden" name="no_note" value="0">
          <input type="hidden" name="currency_code" value="EUR">
          <input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHostedGuest">
          <input type="image" src="https://www.paypal.com/de_DE/DE/i/btn/btn_donateCC_LG.gif" name="submit" align="right" alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal.">
          <img alt="" border="0" src="https://www.paypal.com/de_DE/i/scr/pixel.gif" width="1" height="1" align="right">
          </form>
          Part-DB, Copyright (C) 2005 of <b>Christoph Lechner</b>. Part-DB is published under the <b>GPL</b>,
          so it comes with <b>ABSOLUTELY NO WARRANTY</b>, click <a href="readme/gpl.txt">here</a> for details.
          This is free software, and you are welcome to redistribute it under certain conditions.
          Click <a href="readme/gpl.txt">here</a> for details.<br>
          <br> 
          The first Author's Homepage <a href="http://www.cl-projects.de/">http://www.cl-projects.de/</a><br>
          Author since 2009 by <b>K.Jacobs</b> - <a href="http://www.grautier.com/">http://grautier.com</a><br>
          <br> 
          <b>ajfrenzel</b> - Committer/Bugfix<br>
          <b>tgrziwa</b> - Committer/Bugfix<br>
          <b>d.lipschinski</b> - Committer/Bugfix<br>
          <b>Michael Buesch</b> - Reichelt/Pollin Preissuch Script<br>
          <b>THX @ Matthias Wei&szlig;er</b> f&uuml;r EAGLE3D und dem gro&szlig;artigen Bauteile Renderscript (eagle3d.py) 
        </td>
    </tr>
</table>

<?php
    if (! $disable_update_list) {
?>

<br>

<table class="table">
    <tr>
        <td class="tdtop">
        Updates
        </td>
    </tr>
    <tr>
        <td class="tdtextsmall">
          <br>
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
            #echo "<a href=\"" . $$zeile . "\">" . $$zeile . "</a><br>";
          #}
            #else
          #{
            echo "" . $$zeile . "<br>";
          #}
          } 
            echo "<br>";
          }
          ?>
        </td>
    </tr>
</table>

<?php
    }
?>

</body>
</html>


