# Part-DB

### Neue Version von Part-DB
*Die Version, die Sie in diesem Repository finden, ist veraltet und wird keine neuen Funktionen mehr erhalten.
Es gibt eine neuere und stark verbesserte Version von Part-DB (1.0.0+), die Sie [in diesem Repository](https://github.com/Part-DB/Part-DB-server) finden können.
Wenn Sie mit Part-DB beginnen wollen, verwenden Sie bitte die neue Version. Für bestehende Installationen von Part-DB 0.5.\* wird ein Update auf die Version 1.0 empfohlen.
Weitere Details zu den neuen Funktionen finden Sie [hier](https://github.com/Part-DB/Part-DB-legacy/discussions/118)
und die Upgrade-Anleitung [hier](https://docs.part-db.de/upgrade_legacy.html).*

---

**This file is available in English, too: [README](README.md)**

**Part-DB 0.6 benötigt PHP7 (PHP7.0 oder höher). Benutzen Sie Part-DB 0.5, wenn sie PHP5.6 benutzen wollen!**

### Beschreibung

Part-DB ist eine webbasierte Datenbank zum Verwalten von Elektronischen Bauteilen. Da der Zugriff über den Webbrowser erfolgt, muss Part-DB auf einem Webserver installiert werden. Danach kann die Software mit jedem gängigen Browser und Betriebssystem ohne Installation von Zusatzsoftware benutzt werden.

### Funktionen

 * Angabe von Lagerorten, Footprints, Kategorien, Lieferanten, Datenblattern, Preise, Bestellnummern, ...
 * Baugruppenverwaltung
 * Upload von Bauteil Bildern und Dateianhängen
 * Automatische Anzeige von Footprintbildern
 * Statistik über das gesamte Lager
 * Auflistung von: "Zu bestellende Teile", "Teile ohne Preis" und "nicht mehr erhältliche Teile"
 * Liste von Hersteller-Logos
 * Informationen zu SMD-Beschriftungen von Widerstände, Kondensatoren und Spulen
 * Widerstandsrechner
 * Barcodegenerator für Bauteile und Lagerorte und Scanfunktion für Barcodes
 * Verschiedene mitgelieferte Themes
 * 3D Footprints
 * Unterstützung von BBCode, in den Bauteilebeschreibungen und Kommentaren
 * Suche mittels regulärer Ausdrücke
 * Auflistung von Teilen in einem Lagerort, mit einem bestimmten Footprint oder einem bestimmten Hersteller
 * automatische Erzeugung einer Tabelle mit Bauteileigenschaften aus dem Beschreibungsfeld.
 * nutzt HTML5, mobile Ansicht (responsive Design)
 * Benutzersystem mit Unterstützung von Gruppen und feingranularem Berechtigungssystem
 * Statistikfunktion mit Graphenfunktion

### Anforderungen

 * Webserver mit ca. 20MB Platz (ohne Footprints und 3D Modelle)
 * PHP >= 5.4.0, mit PDO, mbstring und gettext (intl und curl empfohlen)
 * MySQL/MariaDB Datenbank mit Speicherengine InnoDB
 * Webbrowser mit Unterstützung von HTML5 und Javascript

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
dann kann dies [hier](https://crowdin.com/project/part-db) tun.
