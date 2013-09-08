Please Note:

To use our DokuWiki in a read-only mode, we patched the original DokuWiki file "inc/auth.php".
If we update our DokuWiki to a newer version, we have to apply the patch again:

> patch -p0 -i auth.php.diff

That patch enables a read-only mode, which is enabled by default.
It can be disabled by creating an empty file in the Part-DB data-folder, 
which is called "ENABLE-DOKUWIKI-WRITE-PERMS.txt".
This file can easily be created with the Part-DB configuration page.
