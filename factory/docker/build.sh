#!/usr/bin/env bash

PHP_VERSION=${1-5.6}
WP_VERSION=${2-latest}
WC_VERSION=${3-3.3.5}

docker build . -t 890731937511.dkr.ecr.eu-west-1.amazonaws.com/boxtal-woocommerce:$PHP_VERSION-$WP_VERSION-$WC_VERSION --build-arg PHP_VERSION=$PHP_VERSION --build-arg WP_VERSION=$WP_VERSION --build-arg WC_VERSION=$WC_VERSION