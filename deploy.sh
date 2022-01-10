#!/bin/sh -eu
echo "Deploying...";
rm -f db/db.sqlite
sqlite3 db/db.sqlite < sql/create_table.sql
sqlite3 db/db.sqlite < sql/insert_seed.sql
chmod -R 777 db/
chmod -R 777 images/
