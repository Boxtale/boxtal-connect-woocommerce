#!/usr/bin/env bash

DB_NAME=boxtal_woocommerce_test
DB_USER=dbadmin
DB_PASS=dbpass

# drop database
#mysqladmin drop -bf $DB_NAME --user="$DB_USER" --password="$DB_PASS"
mysql --user="$DB_USER" --password="$DB_PASS" -Bse "DROP DATABASE IF EXISTS $DB_NAME"

# create database
mysqladmin create $DB_NAME --user="$DB_USER" --password="$DB_PASS"