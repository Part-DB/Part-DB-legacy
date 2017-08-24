# Part-DB

**Achtung: Nach dem Update auf die aktuelle Version, muss im Part-DB Homeverzeichniss der Befehl
`php composer.phar install` ausgeführt werden, damit Part-DB funktioniert!
Sollte dies nicht möglich sein, dann muss der Ordner `vendor/` aus einem mit composer eingerichtetem
Part-DB kopiert werden.**

### Beschreibung

Part-DB ist eine webbasierte Datenbank zum Verwalten von Elektronischen Bauteilen. Da der Zugriff über den Webbrowser erfolgt, muss Part-DB auf einem Webserver installiert werden. Danach kann die Software mit jedem gängigen Browser und Betriebssystem ohne Installation von Zusatzsoftware benutzt werden.

### Funktionen

 * Angabe von Lagerorten, Footprints, Kategorien, Lieferanten, Datenblattern, Preise, Bestellnummern, ...
 * Baugruppenverwaltung
 * Upload von Bauteil Bildern
 * Automatische Anzeige von Footprintbildern
 * Statistik über das gesamte Lager
 * Auflistung von: "Zu bestellende Teile", "Teile ohne Preis" und "nicht mehr erhältliche Teile"
 * Liste von Hersteller-Logos
 * Informationen zu SMD-Beschriftungen von Widerstände, Kondensatoren und Spulen
 * Widerstandsrechner
 * Barcodegenerator und Scanfunktion für Barcodes
 * Verschiedene mitgelieferte Themes
 * 3D Footprints
 * Unterstützung von BBCode, in den Bauteilen
 * nutzt HTML5, mobile Ansicht

### Anforderungen

 * Webserver mit ca. 10MB Platz (ohne Footprints)
 * PHP >= 5.3.0
 * MySQL/MariaDB Datenbank

### Lizenz

Mit Ausnahme des JavaScript-Menü stehen alle Module unter der GPL. Das JavaScript-Menü ist Bierware. Somit ist eine kommerzielle Nutzung nur möglich, wenn es vom Ersteller des Menüs genehmigt wird.

### Installationsanleitung & Dokumentation

Die gesamte Dokumentation inkl. Installationsanleitung gibts hier:
<https://github.com/do9jhb/Part-DB/wiki>

### Online-Demo zum Ausprobieren

Eine Test-Datenbank ist unter <http://part-db.bplaced.net/> zu finden.

