<?PHP
/*
    part-db version 0.1
    Copyright (C) 2005 Christoph Lechner
    http://www.cl-projects.de/

    part-db version 0.2+
    Copyright (C) 2009 K. Jacobs and others (see authors.php)
    http://code.google.com/p/part-db/

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

    $Id: statistics.php 616 2013-04-19 15:24:55Z kami89@gmx.ch $

    Changelog (sorted by date):
        [DATE]      [NICKNAME]          [CHANGES]
        2012-05-06  theborg             - Int.
*/
?>

<body onload="document.barform.barcode.focus();">

<br>
<form action="barcode.php" method="POST" name="barform">
  CODE128-Input:<input name="barcode" type="text" size="50" maxlength="30" value=""></input>
  <input type="submit" value="Start" name="start" size="100">
</form>
<br> 
<table>
  <tr>
    <td><img src="./img/barcodes/barcode-AB.png" height="60">--</img></td>
    <td><img src="./img/barcodes/barcode-EB.png" height="60">--</img></td>
    <td><img src="./img/barcodes/barcode-LO.png" height="60">--</img></td>
  </tr>
  <tr>
    <td><img src="./img/barcodes/barcode-ED.png" height="60">--</img></td>
    <td><img src="./img/barcodes/barcode-ER.png" height="60">--</img></td>
  </tr>
</table>
<br>
<?PHP $scan =  $_POST['barcode'];?>

<b>CODE128-Input:</b> <?PHP print $scan; ?> <br>

<?PHP
// Barcode zerlegen in Text und PID
$scan = explode("-",$scan);
$name = $scan[0];
$pid =  $scan[1];
?>

<b>CODE128-PID-Output:</b> <?PHP print $pid; ?><br>
<b>CODE128-Name-Output:</b> <?PHP print $name; ?><br>
<b>CODE128-PID-Options:</b>

<?PHP
if ($pid == "0") {
  print "Options-Mode $name";
} 
elseif ($pid == " ") {
  print "PID/Bauteiel nicht Gefunden";
}
else {
  print "None<br>";
?>
<iframe src="../partdb/show_part_info.php?pid=<?PHP print $pid; ?>" width="90%" height="400" name="SELFHTML_in_a_box"></iframe>
<?PHP
}
?>
