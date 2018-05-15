#!/usr/bin/env bash

# Start xvfb to run the tests
export BASE_URL="http://localhost:80"
xvfb-run npm test

