#!/bin/sh

./tsmarty2c.php -o ../templates/nextgen/locale/partdb.pot ../templates/nextgen

#Generate locales from PHP
echo "Start generate locales from php"
find .. -type f -iname "*.php" | xargs xgettext --from-code=UTF-8 -k_e -k_x -k__ -o ../locale/php.pot
echo "Complete!"