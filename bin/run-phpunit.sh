#!/usr/bin/env bash

pushd "`dirname $0`/.." > /dev/null
SCRIPTPATH=`pwd -P`
popd > /dev/null

PHPUNIT=${SCRIPTPATH}/vendor/phpunit/phpunit/phpunit
AUTOLOAD=${SCRIPTPATH}/vendor/autoload.php

#PROJECTS="${SCRIPTPATH}/web/sites/all/modules/composer/drupal-sf-dic
#${SCRIPTPATH}/web/sites/all/modules/composer/drupal-ucms
#${SCRIPTPATH}/web/sites/all/modules/composer/drupal-umenu
#${SCRIPTPATH}/vendor/makinacorpus/redis-bundle"
PROJECTS="${SCRIPTPATH}/web/sites/all/modules/composer/drupal-ucms"
#${SCRIPTPATH}/web/sites/all/modules/composer/drupal-umenu"

for project in $PROJECTS; do
    cd $project
    DRUPAL_PATH="${SCRIPTPATH}/web" $PHPUNIT --bootstrap=$AUTOLOAD "$@"
    cd - > /dev/null
done
