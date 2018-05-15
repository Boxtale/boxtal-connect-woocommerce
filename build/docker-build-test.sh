#!/usr/bin/env bash

WP_VERSION=${1-latest}
WC_VERSION=${2-master}

if [[ $(docker inspect -f {{.State.Running}} boxtal_woocommerce) = "false" ]]; then
    echo "boxtal_woocommerce docker container is not running"
    exit
fi

docker exec boxtal_woocommerce /home/docker/test/bin/build-test.sh $WP_VERSION $WC_VERSION false