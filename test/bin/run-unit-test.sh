#!/usr/bin/env bash

docker exec boxtal_woocommerce /home/docker/test/bin/reset-unit-test-db.sh

if [[ ${RUN_CODE_COVERAGE} == 1 ]]; then
	./vendor/bin/phpunit -c phpunit.xml --coverage-clover=coverage.xml
else
	docker exec boxtal_woocommerce /home/docker/vendor/bin/phpunit -c phpunit.xml
fi
