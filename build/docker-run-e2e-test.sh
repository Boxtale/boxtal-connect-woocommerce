#!/usr/bin/env bash

MULTISITE=${1-0}

docker exec -u docker boxtal_woocommerce /home/docker/test/bin/run-e2e-test.sh false $MULTISITE