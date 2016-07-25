<?php

/**
 * Implements hook_field_widget_info().
 */
function chlovetfield_formatter_info() {
  return [
    'chlovet_leaflet' => [
      'label'       => t("Leaflet map"),
      'field types' => ['latlon'],
      'settings'    => [],
    ],
  ];
}

/**
 * Implements hook_field_formatter_settings_form().
 */
function chlovetfield_formatter_settings_form($field, $instance, $view_mode, $form, &$form_state) {
  $element = [];

  /*
  $display  = $instance['display'];
  $settings = $display['settings'];

  switch ($display['type']) {

    case 'chlovet_leaflet':
      break;
  }
   */

  return $element;
}

/**
 * Implements hook_field_formatter_settings_summary().
 */
function chlovetfield_formatter_settings_summary($field, $instance, $view_mode) {
  return "&hellip;";
}

/**
 * Implements hook_field_formatter_view().
 */
function chlovetfield_formatter_view($entity_type, $entity, $field, $instance, $langcode, $items, $display) {
  $element = [];

  // $settings = $display['settings'];

  switch ($display['type']) {

    case 'chlovet_leaflet':
      break;
  }

  return $element;
}
