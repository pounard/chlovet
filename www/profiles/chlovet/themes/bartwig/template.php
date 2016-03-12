<?php
/**
 * @file
 * Where the magic happens.
 */

/**
 * Implements hook_page_build().
 */
function bartwig_preprocess_page(&$page) {
  drupal_add_css('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css', ['type' => 'external', 'weight' => 1000, 'group' => CSS_THEME]);
  drupal_add_js('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js', ['type' => 'external', 'weight' => 1000]);
}
