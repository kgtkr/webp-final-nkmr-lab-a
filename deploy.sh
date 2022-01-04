#!/bin/sh -eu
echo "Deploying...";

sqlite3 db/db.sqlite < sql/create_table.sql
