#!/usr/bin/env bash

if [[ ${RUN_CODE_COVERAGE} != 1 ]]; then
    phpenv config-rm xdebug.ini
fi
