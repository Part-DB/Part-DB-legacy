<div class="panel panel-default">
    <div class="panel-heading">
        <h4>Hilfe</h4>
    </div>
    
    <div class="panel-body">
    <dl class="help">
        <dt>Footprint Bilder</dt>
        <dd>In <em>../tools/footprints/</em> können Bilder für die Footprints abgelegt werden. Wenn man z.B. ein Footprint mit dem Namen DIP40W anlegt und ein Bild DIP40W.png (Gross-/Kleinschreibung beachten) in den Ordner speicher, wird dieses automatisch angezeigt, wenn kein anderes Bild gesetzt wurde. ICs und einige Standard-Bauteile sind schon vorhanden. Die Bezeichnung dafür findet man anhand der Bildnamen in "Tools->Footprints".</dd>

        <dt>Sicherheitseinstellungen (.htaccess/.htpasswd)</dt>
        <dd>Um Part-DB vor unerlaubten Zugriff zu schützen, sollte man unbedingt serverseitig mit .htaccess/.htpasswd arbeiten Eine Anleitung dafür findet man unter folgendem <a href="http://www.grammiweb.de/anleitungen/ka_htaccess.shtml">Link</a>. Eine Benutzerverwaltung ist in Planung.</dd>

        <dt>Thema ändern</dt>
        <dd>Momentan sind zwei Themen für die Part-DB vorhanden: <em>standard</em> und <em>Greenway</em>. Will man auf ein anderes Thema wechslen, so ändert man in der config.php die Variable <tt>$conf['html']['theme']</tt> auf das entsprechende Thema. Möchte man einzelne Elemente anders formatieren, so legt man eine CSS-Datei mit dem Themennamen im Verzeichnis css an und setzt in der Konfiguration die Variable <tt>$conf['html']['css']</tt> auf <em>true</em>.</dd>
    </dl>
</div>
