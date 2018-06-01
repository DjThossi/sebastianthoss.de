#!/usr/bin/env bash

# Exit if an uninitialised variable is used
set -o nounset

# Exit if a command exits with a non-zero status
set -o errexit

# generate prod
vendor/bin/sculpin generate --env=prod --clean --no-interaction

gitStatus=$(git status)
echo "$gitStatus"

if echo "$gitStatus" | grep -q "nothing to commit";then
    exit 0;
fi

git add .

# collect message
echo -n "Commit message [ENTER]: "
read message

# git commit and push
git commit -am "${message}"
git push