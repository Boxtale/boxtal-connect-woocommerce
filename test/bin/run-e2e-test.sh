#!/usr/bin/env bash

# Start xvfb to run the tests
export BASE_URL="http://localhost:80"
export DISPLAY=:99.0

# Run the tests
xvfb-run npm test
