<html>
 <body class="body">
  <head>
   <link rel="StyleSheet" href="../css/partdb.css" type="text/css" />

<table class="table">
	<tr>
		<td class="tdtop">
		Footprints
		</td>
	</tr>
	<tr>
		<td class="tdtext">
		<table>
			</td>
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
			      echo "<img class=\"footprintbild\" src=".$path. "" .$file." title=".$title."></img>";
			    }
			  ?>
			</tr>
		</table>
		</td>
	</tr>
  </table>

  </head>
 </body>
</html>