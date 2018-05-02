#!/usr/bin/env bash

WP_SITE_URL="http://localhost:8080"

# Start xvfb to run the tests
export BASE_URL="$WP_SITE_URL"
export DISPLAY=:99.0
sh -e /etc/init.d/xvfb start
sleep 3

# Run the tests
    ls -l
npm test
