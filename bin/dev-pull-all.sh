#!/bin/sh

current="$PWD"

drupal_modules="drupal-apubsub
drupal-filechunk
drupal-gulpifier
drupal-iresponsive
drupal-minidialog
drupal-sf-dic
drupal-ucms
drupal-udate
drupal-ulog
drupal-umenu
drupal-unoderef
drupal-usync"

drupal_themes="drupal-badm"

vendor_libraries="makinacorpus/apubsub
makinacorpus/drupal-tooling"

for module in $drupal_modules; do
    cd "web/sites/all/modules/composer/$module"
    echo " * websites/all/modules/composer/$module"
    git pull --rebase
    cd $current
done

for module in $drupal_themes; do
    cd "web/sites/all/themes/composer/$module"
    echo " * web/sites/all/themes/composer/$module"
    git pull --rebase
    cd $current
done

for library in $vendor_libraries; do
    cd "vendor/$library"
    echo " * vendor/$library"
    git pull --rebase
    cd $current
done

