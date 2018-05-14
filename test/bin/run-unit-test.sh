#!/usr/bin/env bash

DB_NAME=$1
DB_USER=$2
DB_PASS=$3

docker exec -u root boxtal_woocommerce chmod 777 /home/docker/test/bin/reset-unit-test-db.sh
docker exec -u root boxtal_woocommerce /home/docker/test/bin/reset-unit-test-db.sh $DB_NAME $DB_USER $DB_PASS localhost

if [[ ${RUN_CODE_COVERAGE} == 1 ]]; then
	./vendor/bin/phpunit -c phpunit.xml --coverage-clover=coverage.xml
else
	docker exec boxtal_woocommerce /home/docker/vendor/bin/phpunit -c phpunit.xml
fi
