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
    cd "www/sites/all/modules/composer/$module"
    echo " * www/sites/all/modules/composer/$module"
    git pull --rebase
    cd $current
done

for module in $drupal_themes; do
    cd "www/sites/all/themes/composer/$module"
    echo " * www/sites/all/themes/composer/$module"
    git pull --rebase
    cd $current
done

for library in $vendor_libraries; do
    cd "lib/vendor/$library"
    echo " * lib/vendor/$library"
    git pull --rebase
    cd $current
done

