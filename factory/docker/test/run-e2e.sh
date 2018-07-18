#!/usr/bin/env bash

MULTISITE=${1-0}

docker exec -u docker boxtal_woocommerce /home/docker/factory/common/test/run-e2e.sh false $MULTISITE