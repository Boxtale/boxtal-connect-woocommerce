#!/usr/bin/env bash

MULTISITE=${1-0}

docker exec boxtal_woocommerce /home/docker/test/bin/reset-unit-test-db.sh

if [[ $MULTISITE = "1" ]]; then
    docker exec boxtal_woocommerce /home/docker/vendor/bin/phpunit -c phpunit-multisite.xml
else
    docker exec boxtal_woocommerce /home/docker/vendor/bin/phpunit
fi
