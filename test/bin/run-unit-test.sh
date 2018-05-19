#!/usr/bin/env bash

docker exec boxtal_woocommerce /home/docker/test/bin/reset-unit-test-db.sh
docker exec boxtal_woocommerce /home/docker/vendor/bin/phpunit -c phpunit.xml --coverage-clover /tmp/coverage.xml
