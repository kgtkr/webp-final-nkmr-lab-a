#!/bin/sh -eu
echo "Deploying...";

sqlite3 db/db.sqlite < sql/create_table.sql
sqlite3 db/db.sqlite < sql/insert_seed.sql
