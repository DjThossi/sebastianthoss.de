#!/usr/bin/env bash

# Exit if an uninitialised variable is used
set -o nounset

# Exit if a command exits with a non-zero status
set -o errexit

# generate prod
vendor/bin/sculpin generate --env=prod --clean --no-interaction

git add .
git status

# collect message
echo -n "Commit message [ENTER]: "
read message

# git commit and push
git commit -am "${message}"
git push