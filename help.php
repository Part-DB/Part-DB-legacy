<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Hilfe</title>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
</head>
<body class="body">

<div class="outer">
    <h2>Hilfe</h2>
    <div class="inner">
        <h3>Footprint Bilder</h3>
        <p>
        In ../tools/footprints/ k&ouml;nnen Bilder f&uuml;r die Footprints abgelegt werden.
        Wenn man z.B. ein Footprint mit dem Namen DIP40W anlegt und ein Bild DIP40W.png (Gross-/Kleinschreibung beachten) in den Ordner schmeisst wird dieses automatisch angezeigt wenn kein anderes Bild gesetzt wurde.
        ICs und einige Standard-Bauteile sind schon vorhanden die Bezeichnung daf&uuml;r findet man anhand der Bildnamen in "Tools->Footprints" raus.
        </p>
        
        <h3>Sicherheitseinstellungen (.htaccess/.htpasswd)</h3>
        <p>
        Um die Datenbank vor unerlaubten Zugriff zu sch&uuml;tzen sollte man unbedingt das Verzeichnis mit einem Passwort sch&uuml;tzen, am sinnvollsten ist es, dies serverseitig mit .htaccess zu machen. Eine Anleitung daf&uuml;r findet man unter folgendem <a href="http://www.grammiweb.de/anleitungen/ka_htaccess.shtml">Link</a>.
        </p>
        
        <h3>Template &auml;ndern</h3>
        <p>
        Momentan sind zwei Templates f&uuml;r die Part-DB vorhanden. Zum Wechseln einfach die gew&uuml;nschte Dateie in ../css/partdb.css umbenennen.
        </p>
        
        <h3>&Ouml;ffentliche Liste</h3>
        <p>
        Am einfachsten geht es, wenn ihr auf dem Server einen extra Ordner erstellt. In diesen kopiert ihr die openlist.php, stats.php, config.php, lib.php und den CSS Ordner.
        Alternativ k&ouml;nnt Ihr auch den Ordner ../partdb/openlist/ mit .htaccess wieder freigeben dieses klappt aber nur wenn der Webserver dieses erlaubt. 
        </p>
    </div>
</div>

</body>
</html>
