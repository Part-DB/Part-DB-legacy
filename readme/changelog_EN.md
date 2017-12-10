# Part-DB changelog

## Part-DB 0.4.2

#### New functions:
* Automatic data sheet links can now also be accessed via a button on the part overview page
* You can now set how long an unused user session should remain open, 
    without the user being logged out (default: 90min)
* Show the lifetime of session cookies in server information

#### Bugfixes:
* german spelling error corrected ("obsolete" -> "obsolete")
* open the help and open links in the new tab, now also works in Firefox
* Minor visual problems fixed.

#### Other:
* Improved support of browsers with Javascript disabled

### Internal improvements
* Various PSR-2 violations corrected


## Part-DB 0.4.1

#### Bugfixes:
* If the description is empty, an - is displayed on the overview page.
* fixed problem with PHPDebugBar
* reset_admin_pw. php now works correctly
* added missing black radio button in the 2nd ring of the resistance calculator

#### Other:
* Links on microcontroller. net thread updated
* composer. lock updated

## Part-DB 4.0.0.0

### New functions:
* New (responsive) design with Bootstrap 3 and JQuery. Mobile device support
* Support of SVG files as image preview
* Support for translations with gettext. Translation into English complete
* Attachments are now also previewed in the part editing view
* Support for 3D models on footprints
* Sorting by table column possible
* Support of regular expressions in the search. Live search and highlighting of search results
* User system with group support and a finely granulated permission system
* Possibility to store all components at a certain storage location, with a certain footprint, with a certain manufacturer, etc.
* Possibility to move or delete many parts at once
* Pagination of the component result to prevent long loading times when there are many components in a category
* Different themes for a different look of Part-DB (Bootswatch)
* Possibility to set the stock to unknown
* Display of "Created" and "Last edited" data, for components and data structures, such as storage locations or footprints
* Support of file attachments and comments for assemblies. Various improvements for the handling of assemblies
* BBCode support for part description and comments
* Parts can be marked as favorites: highlighting in tables
* Possibility to download attachments via the server
* Google and Octopart added as link for automatic data sheet links
* Grouping for search results adjustable
* Data sheets can be stored in folder hierarchy, similar to the category structure
* Automatic generation of a table with part properties from the description and comment of a part
* A specific part name format within a category can be enforced (with RegEx)
* Many new settings to better adjust Part-DB
* Possibility to generate simple barcodes for components. Search field can be used as input field for a barcode scanner.
* Various other minor improvements

### Internal changes:
* Use of namespaces
* Change of template engine from vLib to smarty
* simple API for querying different data
* PHPdoc is now used for documentation
* Use of Composer to manage external libraries and autoloading
* Bcrypt is used to store the admin-pw
* Improved security by setting specific HTTP header in. htaccess
* Change from Dokuwiki to GitHub Wiki
* Improved debugging tools (PHPDebugbar and Whoopsie)
* Migration to SnakeCase (recommended for PHP) and PSR-1/PSR-2 coding guides
* Use of TypeScript for Javascript
* Fixing PHP strict standard errors.

### Other:
* Support of Google Analytics
* Improved compatibility with PHP 7.0
* Improved security by filtering inputs: No XSS attacks possible.

## Part DB 0.3.1

### Bugfixes:
* Issue #25: Too many erroneous footprints under Edit->Footprints could cause
          lead to an empty or not working properly page
* Errors when reading file permissions have made the installation on some PHP installations
          impassable
* Fixed display error in IE
* Error when deleting purchasing information or parts fixed

### Other:
* Conversion from SVN to Git (RSS-Feed adapted to start page, various links adapted)
* Compatibility with PHP 5.5 improved

## Part-DB 0.3.0

### New minimum requirements:
* PHP 5.3.0 or higher is required!
* PDO (PHP Data Objects) incl. MySQL Plugin is required!
* The MySQL engine "InnoDB" is required!

### Internal changes:
* Very extensive changes due to conversion to object-oriented programming
* Using the template system "vlib"
* Use of foreign keys and transactions in the database for more data security
* New debugging options
* Source code documentation with Doxygen

### New / updated functions:
* Added installer which can also create the database structure
* DokuWiki for documentation/help
* Components can have multiple suppliers, order numbers and prices
* Component prices that refer to a specific order quantity
* Individual components and complete assemblies can be marked for ordering
* Manufacturer management added
* More extensive configuration options

#### Bugfixes:
* Failed database updates no longer automatically cause another failure
* Improved compatibility with IE and FF browsers

#### Other:
* Many other new features, changes and bugfixes


## Part-DB 0.2.2.2
* There is no changelog up to this version.
