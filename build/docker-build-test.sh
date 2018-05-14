#!/usr/bin/env bash

if [ $# -lt 3 ]; then
	echo "usage: $0 <db-name> <db-user> <db-pass> [db-host] [wp-version] [wc-version]"
	exit 1
fi

DB_NAME=$1
DB_USER=$2
DB_PASS=$3
WP_VERSION=${5-latest}
WC_VERSION=${6-master}

if [[ $(docker inspect -f {{.State.Running}} boxtal_woocommerce) = "false" ]]; then
    echo "boxtal_woocommerce docker container is not running"
    exit
fi

docker exec -u root boxtal_woocommerce chmod 777 /home/docker/test/bin/build-test.sh
docker exec -u root boxtal_woocommerce /home/docker/test/bin/build-test.sh $DB_NAME $DB_USER $DB_PASS localhost $WP_VERSION $WC_VERSION false