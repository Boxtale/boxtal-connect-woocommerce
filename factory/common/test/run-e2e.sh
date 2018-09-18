#!/usr/bin/env bash

TRAVIS=${1-false}
MULTISITE=${2-0}


export BASE_URL="http://localhost:80"

# Run the tests
if [ ${TRAVIS} = "false" ]; then
    xvfb-run -a npm test
    if [ ${MULTISITE} = "1" ]; then
        export BASE_URL="http://localhost:80/alternate"
        xvfb-run -a npm test
    fi
else
    export DISPLAY=:99.0
    sh -e /etc/init.d/xvfb start
 	sleep 3
	npm test
	TEST1=$?
	if [ ${MULTISITE} = "1" ]; then
        export BASE_URL="http://localhost:80/alternate"
        npm test
        echo $?
        exit ${TEST1} && $?
    fi
    exit ${TEST1}
fi

