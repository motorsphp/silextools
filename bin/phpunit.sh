#!/usr/bin/env bash

SCRIPT=$(readlink -f "$0")
SCRIPT_DIR=$(dirname "$SCRIPT")
ROOT_DIR=$(dirname "${SCRIPT_DIR}")

PHPUNIT=${ROOT_DIR}/vendor/bin/phpunit
${PHPUNIT} $@