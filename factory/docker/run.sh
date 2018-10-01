#!/usr/bin/env bash

PHP_VERSION=${1-5.6}
WP_VERSION=${2-latest}
WC_VERSION=${3-3.3.5}

docker run -di -p 80:80 -e APIURL=$APIURL -e ONBOARDINGURL=$ONBOARDINGURL --name "boxtal_woocommerce" 890731937511.dkr.ecr.eu-west-1.amazonaws.com/boxtal-woocommerce:$PHP_VERSION-$WP_VERSION-$WC_VERSION