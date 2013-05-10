<?php
   /**
    * @page styleguide          Code-Styleguide
    * @tableofcontents
    *
    *
    * Wie alle größeren Projekte haben wir auch ein paar Richtlinien für die Programmierung aufgestellt.
    *
    * @section language         Sprache
    * @li Alle Kommentare sind in Englisch zu verfassen.
    * @li Deutsche Kommentare sind entsprechend zu kennzeichnen und möglichst rasch ins Englische zu übersetzen.
    * @li Kommentare sind nicht überflüssig, sondern notwendig!
    * @li Kommentare sollen nicht offensichtliche Dinge beschreiben, sondern zum allgemeinen Verständnis des Codes beitragen.
    *
    *
    * @section development      Entwicklung
    * @li PHP 4 ist veraltet und wird nicht mehr unterstützt. Es muss daher keine Rücksicht genommen werden auf Versionen vor PHP 5.3.
    * @li Alle neuen Funktionen werden in den Branches zuerst getestet und dann in die offizielle Version übernommen.
    * @li Kennzeichnung von stable, unstable und non public (oder ähnlich)-Versionen. Diese Kennzeichnung wird nur für die gepackten Archive genommen und nicht für die Entwicklerversionen im SVN.
    *
    *
    * @section documentation    Dokumentation
    * @li Einleitende Kommentare für die Funktionsbeschreibung sollten nicht vergessen werden.
    * @li In den Klassen und Libs <b>jede</b> Funktion mit Doxygen-Kommentaren versehen (müssen mit /** beginnen!).
    * @li Die wichtigsten Doxygen-Befehle findet man in der "Doxygen Quick Reference".
    * @li Nach Änderungen am System muss das Wiki ggf. angepasst werden, oder zumindest als "ToDo" vermerken dass es noch gemacht werden muss.
    * @li Für Dinge, die noch zu tun sind, immer den Befehl @@todo benutzen!
    *
    *
    * @section files            Aufbau PHP-Dateien
    * @li Einleitung mit Lizenz
    * @li darunter Auflistung der Änderungen im folgenden Format:
    *   - Datum
    *   - Nickname oder E-Mail
    *   - Leerzeichen und "-"
    *   - Kurze Beschreibung der Änderung (pro Änderung eine Zeile verwenden)
    * @li Ausgabe von HTML nicht im PHP Script vornehmen, dazu Templates (vlibTemplate) nutzen!
    *
    *
    * @section indentation      Einrückungen
    * @li Die Einrückung wird mit 4 Spaces vorgenommen. Keine Tabs verwenden (ev. eigenen Editor entsprechend konfigurieren)!
    * @li Öffnende und schliessende geschweifte Klammern in einer eigenen Zeile.
    * @li Jeder Block innerhalb geschweifter Klammern einrücken.
    * @li Ausnahmen für einzeilige Blöcke. Auf Klammern sollte der Lesbarkeit wegen nicht verzichtet werden.
    * @li IF-THEN-ELSE als ternärer Operator ((...) ? ... : ...) ist erlaubt, sollte möglichst aber nur bei der Zuweisung von Werten verwendet werden.
    * @li Arrays oder längere Strings so formatieren, das eine gute Lesbarkeit gewährleistet ist (ev. nur ein Element pro Zeile, schön untereinander).
    *
    *
    * @section naming           Benennungen
    * @li Funktionen werden klein geschrieben und müssen selbst beschreibende Namen tragen.
    * @li Trennung der einzelnen Wörter erfolgt mit dem Unterstrich.
    * @li Für Variablennamen gilt das selbe wie für Funktionen.
    * @li Klassennamen werden Gross geschrieben (CamelCase).
    *
    *
    * @section classes          Klassen und Funktionen
    * @li Sich wiederholende Funktionen bitte in Klassen auslagern.
    * @li Namespaces sind möglich.
    * @li Klassen mit dem Konstruktur construct() für die Prüfung auf Abhängigkeiten der Klasse und eventuell fehlenden Funktionen versehen.
    * @li Includes am Anfang der Dateien bitte durch include_once() oder require_once() ersetzen. Ist meistens aber nicht notwendig wegen der Autoload-Funktion von PHP.
    * @li Debug-Meldungen einfügen für eine einfachere Fehlersuche. Dazu gibt es einen Debug-Mechanismus in lib.debug.php.
    * @li Exceptions verwenden (try...catch)! Das ermöglicht eine saubere Trennung zwischen Normalfall und Ausnahmefall.
    *
    *
    */
?>
