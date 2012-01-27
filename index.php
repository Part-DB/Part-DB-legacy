<!-- $Id: index.html,v 1.3 2005/08/05 15:11:28 cl Exp $-->
<?php
    include "config.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"                                                                                                                        
    "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
    <title><?php print $title ?></title>
    <link rel="icon" href="img/partdb/favicon.ico" type="image/x-icon">
</head>
<frameset cols="300,*" frameborder="0" framespacing="0">
  <frame name="_nav_frame" src="nav.php">
  <frame name="_content_frame" src="startup.php">
</frameset>
</html> 
