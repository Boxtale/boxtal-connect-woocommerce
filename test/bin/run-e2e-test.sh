#!/usr/bin/env bash

TRAVIS=${1-false}

# Start xvfb to run the tests
export BASE_URL="http://localhost:80"

# Run the tests
if [ ${TRAVIS} = "false" ]; then
    xvfb-run npm test
else
    export DISPLAY=:99.0
    sh -e /etc/init.d/xvfb start
 	sleep 3
	npm test
fi

