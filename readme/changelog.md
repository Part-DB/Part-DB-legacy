# Part-DB Changelog

## Part-DB 0.5.10
### Bugfixes
* Nun kompatibel mit PHP 8.*
* XSS Lücke in Install-Assistent behoben

## Part-DB 0.5.9
### Bugfixes
* Import funktioniert nun (danke an chrisnoisel)

## Part-DB 0.5.8
### Neue Funktionen
* Es sind nun englische Spaltennamen in CSV Bauteileimportdateien erlaubt 
(Siehe `documentation/examples/import_parts/import_parts_en.csv` für ein Beispiel).

## Part-DB 0.5.7
### Neue Funktionen
* Scrollbars in Seitenleiste werden nun auch unter Edge und alten Firefox Versionen versteckt

### Bugfixes
* Fehler bei der Verwendung von MySQL 8.0 behoben

### Sonstiges
* Composer Dependencies aktualisiert
* Mitgelieferte composer.phar aktualisiert

## Part-DB 0.5.6
### Neue Funktion
* Es können nun auch aus den Bauteilelisten für Footprints, Lagerorte und Hersteller, ein neues Bauteil angelegt werden
* Unter Verwaltung->Bearbeiten, gibt es jetzt einen Link um ein Bauteil anlegen zu können

### Bugfixes
* Fehlerhafte und nicht übersetzte Seitentitel korregiert
* Es wird jetzt eine Fehlermeldung angezeigt, wenn ein Benutzer keine Rechte hat die Konfigurationsseite anzuzeigen
* Überschreiben von Einstellungen mit $manual_config funktioniert nun korrekt
* Bauteile mit unbekanntem Lagerbestand werden nun korrekt vom Logsystem behandelt
* Verschieben mehrerer Bauteile auf einmal funktioniert nun wieder überall korrrekt
* Fehler mit "Label erzeugen" auf Lagerort Seite behoben.

### Sonstiges
* Composer Dependencies aktualisiert
* Größe der mitgelieferten Bilder verkleinert

## Part-DB 0.5.5
### Neue Funktionen
* Maximaler Preis liegt nun bei 999.999,99999€ (statt vorher 9.999,99999). Datenbankupdate erforderlich.
* Es kann nun für jede Einkaufsinformation zusätzlich ein Link zur Bestellwebsite angegeben werden
* Es kann nun für jedes Bauteil ein Link zu einer Herstellerwebsite angegeben werden
* In den Einstellungen wird die aktuelle Uhrzeit auf dem Server angezeigt
* Verbesserte Anhangs und Bestellinformations Abschnitte auf der Bauteilebearbeitungsseite

### Bugsizes
* Beim PDF-Export großer Tabellen werden keine Spalten mehr abgeschnitten (Schriftgröße verkleinert)
* Wenn kein Name für ein Dateianhang angegeben wird, wird er wieder automatisch aus dem Dateinamen bestimmt
* Bauteilelink zum Hersteller wird nun korrekt in einem neuen Tab geöffnet
* Diverse visuelle Fehler behoben

### Sonstiges
* Composer Abhängigkeiten aktualisiert
 
## Part-DB 0.5.4
### Neue Funktionen
* Reset Button zum Zurücksetzen des Filters auf system_log.php
* Maximale Zahl der Bauteile, die über show_part_info.php hinzugefügt/entnommen/bestellt werden können, auf 999999 erhöht
* Es wird ein Hinweis angezeigt, wenn der Benutzer versucht das Bauteilelimit (2^32) zu überschreiten versucht
* Ein Button hinzugefügt, um das Anlegen eines Bauteils abzubrechen
* Zahl der Vorhanden Bauteile werden beim Bauteile hinzufügen auf show_device_parts.php angezeigt
* Im "Bauteile hinzufügen" Panel auf show_device_parts.php, wurde ein Link zum Anlegen eines Bauteils hinzugefügt (Suchbegriff wird als Bauteilename übernommen)

### Bugfixes
* **Achtung**: Das Hochladen und die Ausführung (unter Apache2) von PHP Dateien wird jetzt verhindert
* URLs in Dateianhängen werden nicht als fehlerhaft angesehen
* Benennung einiger Labels verbessert
* Elementzahlen in Seitenauswahl sind nun korrekt
* Wenn im Systemlog die nächste Seite aufgerufen wird, dann wird jetzt der Filter berücksichtigt
* Wenn nach dem Anlegen eines Bauteils, die Seite im Browser neugeladen wird, wird korrekt der Bearbeitendialog angezeigt
* Fehler in der Englischen Übersetzung behoben
* Visuelle Fehler auf show_device_parts.php behoben

### Sonstiges
* Composer Abhängigkeiten aktualisiert

## Part-DB 0.5.3
### Bugfixes
* Problem behoben, dass die Kategorie nicht übernommen wurde, wenn man ein Bauteil aus einer Kategorie heraus erstellt
* Probleme mit nicht korrekt gestylten Checkboxes behoben
* Problem behoben, dass ein Bauteil nicht angelegt werden konnte, wenn die Seite im Browser neu geladen wurde
* Fehlerhafter Wert für "Alte Version" beim Datenbank Upgrade Log behoben
* Diverse Fehlermeldungen im Debugmodus behoben

## Part-DB 0.5.2
### Neue Funktionen
* Es wird in der Bauteileübersicht angezeigt, wenn die mit einem Bauteil verknüpfte Datei nicht vorhanden ist

### Bugfixes
* Probleme mit der Darstellung von breiten Bildern (als Anhang) im Bauteilebearbeitungsdialog
* Nicht vorhandene Anhangsbilder werden nicht mehr in Bauteilelisten angezeigt (führte zu Darstellungsfehlern)
* Problem das Seite "leer" nachgeladen wurde, wenn man mit dem Button aus dem edit_show_part_info.php Dialog in die
    letzte Bauteileliste gesprungen ist
* Vorwärts Button im Browser funktionier nun

### Sonstiges
* Fontawesome auf Version 5.4 geupdated

## Part-DB 0.5.1
Diese Version wurde zurückgezogen.

## Part-DB 0.5.0
### Neue Funktionen
* Viewer für die vorhanden 3D-Footprints hinzugefügt
* Bootstrap Tooltips werden für title-Attribute benutzt
* Einfügen von Sonderzeichen in Textfelder über Alt-Tastenkombinationen möglich
* Verbesserter Labelgenerator mit frei definierbaren Inhalt und Formatierung
* Unterstützung von Labeln für Lagerorte
* Datenquelle von TreeViews in der Seitenleiste kann über Dropdown-Menu gewählt werden (z.B. Lagerorte oder Footprints)
* Nach dem Login wird der Nutzer zu der Website zurückgeleitet, auf der er vor dem Login war
* Nach dem Löschen eines Bauteils wird der Benutzer auf die zuletzt besuchte Bauteileliste zurückgeleitet
* Button im Bauteilebearbeitendialog hinzugefügt, um zur letztbesuchten Bauteileliste zurückzukehren
* Möglichkeit sich die zuletzt bearbeiten und zuletzt angelegten Bauteile anzeigen lassen zu können
* Eventlog System, um Veränderungen an der Datenbank zu protokollieren
* Möglichkeit den zuletzt bearbeitenden Benutzer anzeigen zu lassen
* Graphen in Statistik, für die meistbenutzten Kategorien, Footprints, etc.
* Möglichkeit ein Unix-Socket für Datenbankzugriff benutzen zu können
* Möglichkeit Port für Datenbankzugriff festlegen zu können
* Möglichkeit einen Cookie-Consent Dialog anzeigen zu lassen
* Datenbankgröße wird unter system_database.php angezeigt


### Verschiedenes
* Verbesserte Performance des Berechtigungssystem
* Verbesserte Sicherheit gegen CSFR-Attacken
* Fontawesome 5 statt Fontawesome 4
* Mehr Abhängigkeiten werden jetzt über Composer gemanagt
* Javascript und CSS-Dateien werden verkleinert ausgeliefert


## Part-DB 0.4.6
### Bugfixes
* Problem, mit fehlender Markierung von Bauteilen in den Bauteiletabellen, behoben
* Login funktioniert nun auch korrekt, wenn die loginseite mit "?logout" Option aufgerufen wird
* Vorbestellfunktion von Baugruppenbauteilen funktioniert nun korrekt
* Links auf der Startseite und Bauteilekommentaren funktionieren nun
* Zeilenumbrüche in Bauteilekommentaren werden nun korrekt als Trenner von Bauteileeigenschaften interpretiert

### Sonstiges
* Composer Abhängigkeiten aktualisiert

## Part-DB 0.4.5
### Bugfixes
* Problem, dass Kategorien nicht gelöscht werden konnte, wenn ein Subelement Bauteile enthält
 behoben.
* Der Download von exportierten Bestelllisten funktioniert nun.

### Sonstiges
* Composer Abhängigkeiten aktualisiert.

## Part-DB 0.4.4

### Neue Funktionen:
* Es ist möglich den Hinweisdialog auf fehlende Datenstrukturen, wie Hersteller oder Lieferanten in den Einstellungen zu deaktivieren.
* Werden Bauteile einer Baugruppe exportiert und die Ausgabe angezeigt, dann bleibt das Panel offen.

### Bugfixes:
* Probleme mit fehlenden Composerabhängigkeiten behoben
* POSIX locale entfernt, da dies zu Problemen führen konnte
* Probleme mit Anzeige und Download von Bauteilen von Baugruppen behoben

### Sonstiges:
* Composer Abhängigkeiten aktualisiert

## Part-DB 0.4.3

### Neue Funktionen:
* Es wird ein Hinweis im Kommentarpanel angezeigt, wenn eine Baugruppe noch kein Kommentar hat

### Bugfixes:
* Button zum Scannen von Barcodes in der Mobile-Ansicht funktioniert wieder
* Dateianhangspanel auf der Baugruppenübersicht kann nun wieder eingeklappt werden
* Deaktiviere die Kommentarfelder für Datenstrukturen, wenn ein Benutzer keine Bearbeitungsrechte hat
* Für die IC-Logos wird nun die richtige Berechtigung abgefragt (und nicht mehr die IMPORT-Permission)

## Part-DB 0.4.2

### Neue Funktionen:
* Automatische Datenblattlinks können nun auch über einen Button auf der Bauteileübersichtsseite aufgerufen werden
* Es ist nun einstellbar, wie lange eine unbenutzte Benutzersession, geöffnet bleiben soll, 
    ohne dass der Nutzer ausgeloggt wird (Standard: 90min)
* Zeige in Serverinformationen die Lebensdauer der Sessioncookies

### Bugfixes:
* Rechtschreibfehler korriegiert ("obsolent" -> "obsolet")
* Aufruf der Hilfe und öffnen von Links im neuem Tab, funktioniert nun auch in Firefox
* Kleinere visuelle Probleme behoben.

### Sonstiges:
* Verbesserung der Unterstützung von Browsern mit deaktiviertem Javascript

### Interne Verbesserungen
* Diverse PSR-2 Violations korrigiert

## Part-DB 0.4.1

### Bugfixes:
* Bei einer leeren Beschreibung wird auf der Übersichtsseite ein - angezeigt
* Problem mit PHPDebugBar gefixt
* reset_admin_pw.php funktioniert jetzt korrekt
* Fehlender schwarzer Radiobutton im 2. Ring beim Widerstandsrechner hinzugefügt

### Sonstiges:
* Links auf mikrocontroller.net Thread aktualisiert
* composer.lock geupdated
 
## Part-DB 0.4.0

### Neue Funktionen:
* Neues (responsive) Design mit Bootstrap 3 und JQuery. Unterstützung von Mobilgeräten
* Unterstützung von SVG Dateien als Bildvorschau
* Unterstützung von Übersetungen mit gettext. Übersetung in Englische komplett
* Bildanhänge werden nun auch als Vorschau in der Ansicht zur Bauteilebearbeitung angezeigt
* Unterstützung für 3D Modelle bei Footprints
* Sortierung nach einer Tabellenspalte möglich
* Unterstützung von regulären Ausdrücken bei der Suche. Livesuche und Hervorhebung der Suchergebnisse
* Benutzersystem mit Unterstützung von Gruppen und einem fein granuliertem Berechtigungssystem
* Möglichkeit alle Bauteile an einem bestimmtem Lagerort, mit einem bestimmtem Footprint, mit bestimmtem Hersteller, etc
* Möglichkeit viele Bauteile auf einmal zu verschieben oder zu löschen
* Paginierung der Bauteilergebnis, um lange Ladezeiten zu verhindern, wenn in einer Kategorie viele Bauteile sind
* Verschiedene Themen für ein anderes Aussehen von Part-DB (Bootswatch)
* Möglichkeit den Lagerbestand auf unbekannt zu setzen
* Anzeige von "Angelegt" und "Zuletzt bearbeitet" Daten, für Bauteile und Datenstrukturen, wie z.B. Lagerorte oder Footprints
* Unterstützung von Dateianhängen und Kommentare für Baugruppen. Verschiedene verbesserungen für das Handling von Baugruppen
* BBCode Unterstützung für Bauteilebeschreibung und Kommentare
* Bauteile können als Favorit markiert werden: Farbige Hervorhebung in Tabellen
* Möglichkeit Dateianhänge über den Server herunterzuladen
* Google und Octopart als Link für automatische Datenblatt links hinzugefügt
* Gruppierung für Suchergebnisse einstellbar
* Datenblätter können in Ordnerhierachie, ähnlich der Kategorienstruktur gespeichert werden
* Automatische Generierung einer Tabelle mit Bauteileeigenschaften aus der Beschreibung und dem Kommentar eines Bauteils
* Ein bestimmtes Format für Bauteilenamen innerhalb einer Kategorie kann erzwungen werden (mit RegEx)
* Viele neue Einstellmöglichkeiten um Part-DB besser anpassen zu können
* Möglichkeit einfache Barcodes für Bauteile zu generien. Suchfeld kann als Eingabefeld für einen Barcodescanner benutzt werden.
* Verschieden weitere kleinere Verbesserungen

### Interne Veränderungen:
* Verwendung von Namespaces
* Wechsel der Templateengine von vLib auf smarty
* simple API zur Abfrage verschiedener Daten
* PHPdoc wird nun zur Dokumentation benutzts
* Verwendung von Composer zur Verwaltung von externen Bibliotheken und Autoloading
* Bcrypt wird zur Speicherung der Admin-PW benutzt
* Verbesserte Sicherheit durch das setzen bestimmter HTTP header in .htaccess
* Wechsel von Dokuwiki auf GitHub Wiki
* Verbesserte Debugtools (PHPDebugbar and Whoopsie)
* Migration auf SnakeCase (Empfohlen für PHP) und PSR-1/PSR-2 coding guides
* Verwendung von TypeScript für Javascript
* Behebung von PHP strict Standard fehlern.

### Sonstiges:
* Unterstützung von Google Analytics
* Verbesserte Kompatibilität mit PHP 7.0
* Sicherheitsverbesserung durch das Filtern von Eingaben: Keine XSS-Attacke möglich.

## Part-DB 0.3.1

### Bugfixes:
* Issue #25: Bei zu vielen fehlerhaften Footprints unter Bearbeiten->Footprints konnte es
          zu einer leeren oder nicht richtig funktionierenden Seite führen
* Fehler beim Lesen von Dateirechten haben die Installation auf einigen PHP Installationen
          verunmöglicht
* Darstellungsfehler im IE behoben
* Fehler beim Löschen von Einkaufsinformationen oder Bauteilen behoben

### Sonstiges:
* Umstellung von SVN auf Git (RSS-Feed auf Startseite angepasst, diverse Links angepasst)
* Kompatibilität mit PHP 5.5 verbessert

## Part-DB 0.3.0

### Neue Mindestanforderungen:
* Es wird mindestens PHP 5.3.0 vorausgesetzt!
* Es wird PDO (PHP Data Objects) inkl. MySQL Plugin vorausgesetzt!
* Die MySQL Engine "InnoDB" wird vorausgesetzt!

### Interne Veränderungen:
* Sehr umfangreiche Änderungen durch Umstellung auf objektorientierte Programmierung
* Verwendung des Template-Systems "vlib"
* Verwendung von Fremdschlüssel und Transaktionen in der Datenbank für mehr Datensicherheit
* Neue Debug-Möglichkeiten
* Quellcode-Dokumentation mit Doxygen

### Neue / aktualisierte Funktionen:
* Installer hinzugefügt, der auch die Datenbankstruktur erzeugen kann
* DokuWiki für die Dokumentation/Hilfe
* Bauteile können mehrere Lieferanten, Bestellnummern und Preise haben
* Bauteilpreise, die sich auf eine bestimmte Bestellmenge beziehen
* Einzelne Bauteile und ganze Baugruppen können zum Bestellen vorgemerkt werden
* Herstellerverwaltung hinzugefügt
* Umfangreichere Konfigurationsmöglichkeiten

### Bugfixes:
* Fehlgeschlagene Datenbankupdates führen nicht mehr automatisch zu einem weiteren Fehlschlag
* Verbesserung der Kompatibilität mit den Browsern IE und FF

### Sonstiges:
* Sehr viele weitere neue Funktionen, Veränderungen und Bugfixes


## Part-DB 0.2.2
* Bis zu dieser Version ist kein Changelog vorhanden.
