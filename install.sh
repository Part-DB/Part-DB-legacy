#!/bin/sh

# MySQL-user with rights to create a database
ADMIN_USER=root
ADMIN_PASS=

# Database
DB_HOST=localhost
DB_NAME=part-db
DB_USER=part-db
DB_PASS=part-db

# Install
SQL_INSTALL="readme/createtables-FOR-V0.2.1-rev12.sql"

sed "s/DB_NAME/$DB_NAME/g;s/DB_HOST/$DB_HOST/g;s/DB_USER/$DB_USER/g;s/DB_PASS/$DB_PASS/g" <$SQL_INSTALL | mysql -u$ADMIN_USER -p$ADMIN_PASS -h$DB_HOST
