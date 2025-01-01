#!/usr/bin/env bash

# Exit if an uninitialised variable is used
set -o nounset

# Exit if a command exits with a non-zero status
set -o errexit

# generate prod
make generate-prod

gitStatus=$(git status)

if echo "$gitStatus" | grep -q "nothing to commit";then
    exit 0;
fi

git add .

if [ $# -eq 0 ]
  then
    # collect message
    echo "$gitStatus"
    echo -n "Commit message [ENTER]: "
    read message
  else
    message="$1"
fi

# git commit and push
git commit -am "${message}"
git push