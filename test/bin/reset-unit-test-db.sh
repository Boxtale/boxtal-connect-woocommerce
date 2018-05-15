#!/usr/bin/env bash

DB_NAME=boxtal_woocommerce_test
DB_USER=dbadmin
DB_PASS=dbpass
DB_HOST=localhost

# parse DB_HOST for port or socket references
PARTS=(${DB_HOST//\:/ })
DB_HOSTNAME=${PARTS[0]};
DB_SOCK_OR_PORT=${PARTS[1]};
EXTRA=""

if ! [ -z $DB_HOSTNAME ] ; then
    if [ $(echo $DB_SOCK_OR_PORT | grep -e '^[0-9]\{1,\}$') ]; then
        EXTRA=" --host=$DB_HOSTNAME --port=$DB_SOCK_OR_PORT --protocol=tcp"
    elif ! [ -z $DB_SOCK_OR_PORT ] ; then
        EXTRA=" --socket=$DB_SOCK_OR_PORT"
    elif ! [ -z $DB_HOSTNAME ] ; then
        EXTRA=" --host=$DB_HOSTNAME --protocol=tcp"
    fi
fi

# drop database
mysqladmin drop -bf $DB_NAME --user="$DB_USER" --password="$DB_PASS"$EXTRA

# create database
mysqladmin create $DB_NAME --user="$DB_USER" --password="$DB_PASS"$EXTRA