# Part-DB

**This file is available in English, too: [README](README_EN.md)**

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
 * Suche mittels regulärer Ausdrücke
 * Auflistung von Teilen in einem Lagerort, mit einem bestimmten Footprint oder einem bestimmten Hersteller
 * automatische Erzeugung einer Tabelle mit Bauteileigenschaften aus dem Beschreibungsfeld.
 * nutzt HTML5, mobile Ansicht

### Anforderungen

 * Webserver mit ca. 10MB Platz (ohne Footprints)
 * PHP >= 5.4.0, mit PDO und mbstring
 * MySQL/MariaDB Datenbank

### Lizenz
Part-DB steht unter der [General Public License Version 2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.de.html).
Zusätzlich verwendet Part-DB einige Bibliotheken, die andere Lizenzen verwenden. 
Genaue infos hierzu liefert [EXTERNAL_LIBS](readme/EXTERNAL_LIBS.md)

### Installationsanleitung & Dokumentation

Die gesamte Dokumentation inkl. Installationsanleitung gibts hier:
<https://github.com/do9jhb/Part-DB/wiki>

### Online-Demo zum Ausprobieren

Eine Test-Datenbank ist unter <http://part-db.bplaced.net/> zu finden.

### Übersetung
Part-DB ist auch in Englisch verfügbar: Hierfür muss die Einstellung Sprache in den Einstellungen oder während der 
Installation auf _[en_US] English (United States)_ gestellt werden. 

Möchte man sich an der Übersetzung beteiligen (insbesondere bei anderen Sprachen als Englisch), 
dann kann dies [hier](https://translate.zanata.org/iteration/view/part-db/0.4.0/) tun.