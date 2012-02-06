<html>
<body>
<pre>
<?php
$datei = file('config.php');

foreach($datei AS $meine_datei)
{
    print htmlspecialchars( $meine_datei);
}
?>
</pre>
</body>
</html>
