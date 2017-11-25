# Part-DB Changelog

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
