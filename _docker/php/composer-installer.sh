#!/bin/sh

php -r "copy('https://composer.github.io/installer.sig', 'composer-setup.sig');"
EXPECTED_SIGNATURE="$(cat composer-setup.sig)"

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_SIGNATURE="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]
then
    >&2 echo 'ERROR: Invalid installer signature'
    rm composer-setup.php
    rm composer-setup.sig
    exit 1
fi

php composer-setup.php --install-dir=/usr/local/bin --filename=composer --quiet
RESULT=$?
rm composer-setup.php
rm composer-setup.sig
exit $RESULT