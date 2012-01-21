<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">

<html>
  <head>
   <link rel="StyleSheet" href="../css/partdb.css" type="text/css" />
  </head>

 <body class="body">

<table class="table">
	<tr>
		<td class="tdtop">
		Footprints
		</td>
	</tr>
	<tr>
		<td class="tdtext">
		  <?php
		    $pic = array();
		    $path = "footprints/";
		      $verzeichnis = @opendir($path);
		      if(!$verzeichnis) die("Kann Verzeichnis $path nicht öffnen");
			rewinddir($verzeichnis);
		      while($file = readdir($verzeichnis)) {
			if($file != "." and $file != ".." and $file != ".db" and $file != ".svn") {
			  array_push($pic, "$file");
			}
		      }
		      sort($pic);
		      for($x=0;$x<count($pic);$x++) {
			$file = $pic[$x]; 
			$title = $pic[$x];
			// Normal
			echo "<img class=\"footprintbild\" src=".$path. "" .$file." title=".$title."></img>";
			// With Java Popup
			#echo "<img src=\"" .$path. "" .$file. "\" height=\"70\" onClick=\"window.open('" .$path. "" .$file. "','320','240','menubar=no')\">";
		      }
		  ?>
		</td>
	</tr>
  </table>

 </body>
</html>
