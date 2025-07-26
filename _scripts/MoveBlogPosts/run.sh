#!/usr/bin/env bash

# Exit if an uninitialised variable is used
set -o nounset

# Exit if a command exits with a non-zero status
set -o errexit

docker compose run --rm php -f _scripts/MoveBlogPosts/moveFamilieBlogEntry.php

#./_scripts/takeItLive.sh 'Move familie blog post'