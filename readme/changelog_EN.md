# Part-DB changelog

## Part-DB 0.5.5
### New functions
* Maximum price is now 999.999,99999€ (instead of 9.999,99999 before). Database update required.
* Now for each purchase information an additional link to the order website can be given.
* A link to a manufacturer's website can now be provided for each component
* The current time on the server is displayed in the settings.
* Improved appendix and ordering information sections on part machining side

### Bugsizes
* When exporting large tables to PDF, no more columns are truncated (font size reduced)
* If no name is specified for a file attachment, it is automatically determined from the file name again.
* Component link to manufacturer is now correctly opened in a new tab
* Fixed several visual bugs

### Miscellaneous
* Composer dependencies updated

## Part-DB 0.5.4
### New functions
* Reset button to reset the filter to system_log.php
* Maximum number of parts that can be added/removed/ordered via show_part_info.php increased to 999999
* A hint is displayed when the user tries to exceed the part limit (2^32).
* Added a button to cancel the creation of a part
* Number of existing parts are displayed on show_device_parts.php when adding parts
* In the "Add Parts" panel on show_device_parts.php, a link to create a part has been added (search term is used as part name)

### Bugfixes
* **Caution**: The upload and execution (under Apache2) of PHP files will now be prevented.
* URLs in file attachments are not regarded as faulty
* Improved naming of some labels
* Element numbers in page selection are now correct
* If the next page is called in the system log, the filter is now taken into account.
* If, after creating a component, the page is reloaded in the browser, the edit dialog is displayed correctly.
* Fixed a bug in the English translation
* Fixed visual errors on show_device_parts.php

### Miscellaneous
* Composer dependencies updated

## Part-DB 0.5.3
### Bugfixes
* Fixed problem where category was not applied when creating a part from a category
* Fixed problems with incorrectly styled checkboxes
* Fixed problem where a part could not be created when the page was reloaded in the browser
* Fixed incorrect value for "old version" in database upgrade log
* Fixed several error messages in debug mode

## Part-DB 0.5.2
## New functions
* It is displayed in the part overview if the file linked to a part does not exist.

## Bugfixes
* Problems with the display of wide images (as attachments) in the part editing dialog
* Missing attachment images are no longer displayed in part lists (led to display errors)
* Problem that page "empty" was reloaded, if you click with the button from the edit_show_part_info.php dialog into the
    jumped to the last parts list
* Forward button in browser works now

## Other
* Fontawesome updated to version 5.4

## Part-DB 0.5.1
This version has been withdrawn.

## Part-DB 0.5.0
### New functions
* Added viewer for existing 3D footprints
* Bootstrap tooltips are used for title attributes
* Special characters can be inserted in text fields using Alt key combinations
* Improved label generator with freely definable content and formatting
* Support of labels for storage locations
* Data source of TreeViews in the sidebar can be selected via dropdown menu (e.g. storage locations or footprints)
* After logging in, the user is redirected to the website on which he was before logging in.
* After deleting a part, the user is redirected to the last visited part list.
* Added button in part edit dialog to return to last visited part list
* Possibility to display the last edited and last created components
* Eventlog system to log changes to the database
* Possibility to display the last user to be edited
* Graphs in statistics for the most used categories, footprints, etc.
* Ability to use a Unix socket for database access
* Ability to set port for database access
* Possibility to display a cookie-consent dialog
* Size of the database is shown in system_database.php

### Miscellaneous
* Improved performance of the authorization system
* Improved security against CSFR attacks
* Fontawesome 5 instead of Fontawesome 4
* More dependencies are now managed via Composer
* Javascript and CSS files are delivered in reduced size

## Part-DB 0.4.6
### Bugfixes
* Problem with missing part markings in part tables fixed
* Login now also works correctly when calling the login page with the "?logout' option
* Pre-order function for module components now works correctly
* Links on the start page and component comments now work
* Line breaks in part comments are now correctly interpreted as part property separators.

### Miscellaneous
* Composer dependencies updated

## Part-DB 0.4.5
### Bugfixes
* Problem that categories could not be deleted if a sub-element contains components
 fixed.
* The download of exported order lists now works.

### Miscellaneous
* Composer dependencies updated.

## Part-DB 0.4.4

#### New functions:
* It is possible to disable the hint dialog for missing data structures, such as manufacturer or supplier in the settings.
* If components of an assembly are exported and the output is displayed, the panel remains open.

#### Bugfixes:
* Problems with missing composer dependencies fixed
* POSIX locale removed as this could cause problems
* Problems with displaying and downloading components of assemblies fixed

#### Other:
* Composer dependencies updated

## Part-DB 0.4.3

#### New functions:
* A note is displayed in the Comment Panel if an assembly does not yet have a comment

#### Bugfixes:
* Button for scanning barcodes in Mobile View works again
* File attachment panel on the assembly overview can now be collapsed again
* Disable the comment fields for data structures if a user has no editing rights
* For the IC-Logos tool the correct permission is checked (and no longer the IMPORT permission)


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
