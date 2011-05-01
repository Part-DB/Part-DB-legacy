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
			<tr class="trlist1">
			<td>
			<b>CSP</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/CSP","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
				$title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist2">
			<td>
			<b>DIP</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/DIP","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>			</td>
			</tr>

			<tr class="trlist1">
			<td>
			<b>DIP Wide</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/DIPW","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist2">
			<td>
			<b>FBGA</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/FBGA","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist1">
			<td>
			<b>LBGA</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/LBGA","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist2">
			<td>
			<b>LQFP</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/LQFP","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist1">
			<td>
			<b>LQFP EXP PAD</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/LQFPE","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist2">
			<td>
			<b>MLF</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/MLF","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist1">
			<td>
			<b>Micro Array</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/MA","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist2">
			<td>
			<b>MSOIC</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/MSOIC","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist1">
			<td>
			<b>PLCC</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/PLCC","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist2">
			<td>
			<b>PQFP</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/PQFP","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist1">
			<td>
			<b>SC-70</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/SC70","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist2">
			<td>
			<b>SOIC</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/SOIC","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist1">
			<td>
			<b>SOICW</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/SOICW","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist2">
			<td>
			<b>SOT223</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/SOT223","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist1">
			<td>
			<b>SOT23</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/SOT23","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist2">
			<td>
			<b>SSOP</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/SSOP","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist1">
			<td>
			<b>SSOP EIAJ</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/SSOPEIAJ","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist2">
			<td>
			<b>TCSP</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/TCSP","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist1">
			<td>
			<b>TEPBGA</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/TEPBGA","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist2">
			<td>
			<b>TO3</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/TO3","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist1">
			<td>
			<b>TO46</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/TO46","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist2">
			<td>
			<b>TO92</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/TO92","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist1">
			<td>
			<b>TO99</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/TO99","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>


			<tr class="trlist2">
			<td>
			<b>TO100</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/TO100","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist1">
			<td>
			<b>TO220</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/TO220","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist2">
			<td>
			<b>TO247</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/TO247","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist1">
			<td>
			<b>TO252</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/TO252","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist2">
			<td>
			<b>TO263</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/TO263","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist1">
			<td>
			<b>TO263 Thin</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/TO263T","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist2">
			<td>
			<b>TQFP</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/TQFP","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist1">
			<td>
			<b>TQFP EXP PAD</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/TQFPE","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist2">
			<td>
			<b>TSOT</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/TSOT","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist1">
			<td>
			<b>TSSOP</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/TSSOP","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist2">
			<td>
			<b>TSSOP EXP PAD</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/TSSOPE","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist1">
			<td>
			<b>UCSP</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/UCSP","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist2">
			<td>
			<b>UFBGA</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/UFBGA","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    echo "<td><img src=".$file." height=\"70\"></img></td>";
			    }
			?>
			</td>
			</tr>

			<tr class="trlist1">
			<td>
			<b>Crystall</b>
			</td>
			<td>
			<?PHP
			$pic = listPicture("footprints/HC","png"); 
			  for($x=0;$x<count($pic);$x++) {
			    $file    =    $pic[$x]["file"]; 
			    $title 	 = 	  $pic[$x]["title"];
				echo "<img src=".$file." title=".$title." height=\"70\"></img>";
			    }
			?>
			</td>
			</tr>
		</table>
		</td>
	</tr>
  </table>

  </head>
 </body>
</html>

<?PHP
function listPicture($dir="",$type="png") {
    $x = 0;
    foreach (glob($dir."*.".$type) as $filename)    {
		$path_parts = pathinfo($filename);
        
		$picture[$x]["file"]	= $path_parts['dirname']."/".$path_parts['basename'];
		$picture[$x]["title"]	= $path_parts['filename'];
        $x++;
    }
    return $picture;
}
?>

