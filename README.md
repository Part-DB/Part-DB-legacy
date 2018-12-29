# Part-DB

**[Hier](README_DE.md) gibt es eine deutsche Version dieser README**

**If you are using Part-DB, then it would be helpful for further development, if you participate in the following anonymous survey:
https://docs.google.com/forms/d/e/1FAIpQLSdmhcNutwuG35m5JLpxF8xJpJqTCDhTpSupV5dnJrMOYISm9w/viewform?usp=sf_link**

**Part-DB 0.6 requires now PHP7 (PHP7.0 or higher)! Use Part-DB 0.5, if you need a version for PHP5.6.**

### Description

Part-DB is a web-based database for managing electronic components. Since access is via the Web browser, you must install Part-DB on a Web server. Afterwards, the software can be used with any standard browser and operating system without the need to install any additional software.

### Functions

* Indication of storage locations, footprints, categories, suppliers, data sheets, prices, order numbers,...
* Assembly management
* Upload of component images
* Automatic display of footprint images
* Statistics on the entire warehouse
* Parts to be ordered, parts without price and parts no longer available.
* List of manufacturer logos
* Information on SMD marking of resistors, capacitors and coils
* Resistance calculator
* Barcode generator for Part and storelocations and scan function for barcodes
* Various included themes
* 3D Footprints
* Support of BBCode, in the part's description and comment
* Search by regular expressions
* List of all parts in a storage location, with a specific footprint or manufacturer
* Automatic generation of a table with part properties from the description field.
* uses HTML5, mobile view
* User system with fine permission system
* Statistics system with graphes

### requirements

* Web server with approx. 20MB space (without footprint images or 3D models)
* PHP >= 5.4.0, with PDO, mbstring and gettext (intl and curl recommended)
* MySQL/MariaDB database

### License
Part-DB is available under the [General Public License Version 2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.de.html).
In addition, Part-DB uses some libraries that use other licenses. 
For detailed information see [EXTERNAL_LIBS](readme/EXTERNAL_LIBS.md)s

### Installation instructions & documentation

All documentation including installation instructions can be found here:
<https://github.com/do9jhb/Part-DB/wiki>

### Online demo

A test database can be found at <http://part-db.bplaced.net/part-en>.

### Translation
Part-DB is also available in German: For this purpose, the setting Language must be selected in the settings or during the 
Installation on _[de_DE] Deutsch (Deutschland)_. 

If you want to participate in the translation (especially for languages other than English), 
then this can be done [here](https://crowdin.com/project/part-db).
