#!/usr/bin/env bash

pushd "`dirname $0`/.." > /dev/null
SCRIPTPATH=`pwd -P`
popd > /dev/null

PHPUNIT=${SCRIPTPATH}/lib/vendor/phpunit/phpunit/phpunit
AUTOLOAD=${SCRIPTPATH}/lib/vendor/autoload.php

PROJECTS="${SCRIPTPATH}/www/sites/all/modules/composer/drupal-sf-dic
${SCRIPTPATH}/www/sites/all/modules/composer/drupal-ucms
${SCRIPTPATH}/www/sites/all/modules/composer/drupal-umenu
${SCRIPTPATH}/lib/vendor/makinacorpus/redis-bundle"

for project in $PROJECTS; do
    cd $project
    DRUPAL_PATH="${SCRIPTPATH}/www" $PHPUNIT --bootstrap=$AUTOLOAD "$@"
    cd - > /dev/null
done
