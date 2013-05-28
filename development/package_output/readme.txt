To make a package for a release, run this command in /development/

./tools.sh -p

This will make a *.tar.gz in /development/package_output/






IMPORTANT: Checklist for publishing a new release
=================================================

    1. check the system version in "config_defaults.php", it must be higher than the last release!
    2. run "./tools.sh -p"
    3. check if the files in the archive have the right owner and group "www-data:www-data"
    4. check if the files in the archive have the right permissions:
        - 555 for all directories (with exceptions)
        - 444 for all files (with exceptions)
        - 755 for data/
        - 755 for data/backup/
        - 755 for data/log/
        - 755 for data/media/
    5. check if there are NO PERSONAL FILES in the archive:
        - data/config.php
        - data/ENABLE-DOKUWIKI-WRITE-PERMS.txt
        - data/backup/* (except index.html)
        - data/log/* (except index.html)
        - data/media/* (except .htaccess)
    6. check if there directories are NOT in the archive:
        - .svn/
        - development/
        - documentation/dokuwiki/data/cache/
        - documentation/dokuwiki/data/tmp/
    7. check if .htaccess exists in the main directory and in data/
