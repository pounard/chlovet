<?php

/**
 * @file
 * Declares composite widgets.
 */

/**
 * Implements hook_field_widget_info().
 */
function hsm_content_field_widget_info() {
  //  return [
  //    'hidden' => [
  //      'label'       => t('Hidden (Automatic)'),
  //      'field types' => ['list_tree'],
  //    ],
  //  ];
}

/**
 * Return 'List by types' allowed values for types
 *
 * @param $field
 * @param $instance
 * @param $entity_type
 * @param $entity
 * @param $cacheable
 * @return array
 */
function hsm_content_allowed_contrib_types($field, $instance, $entity_type, $entity, $cacheable) {
  $existing_types = [];

  // If in a context of a site, prevent creating other list of same type.
  $siteManager = ucms_site_manager();
  $site = $siteManager->getContext();
  if ($site) {
    $query = db_select('node', 'n');
    $query->innerJoin('field_data_list_type', 'lt', "lt.entity_type = 'node' AND n.nid = lt.entity_id");
    $existing_types = $query
      ->fields('lt', ['list_type_value'])
      ->condition('n.type', $entity->type)
      ->addTag('node_access')
      ->execute()
      ->fetchCol();
  }

  $typeHandler = ucms_contrib_type_handler_get();
  $types = $typeHandler->getEditorialContentTypes();
  if ($existing_types) {
    $types = array_diff($types, $existing_types);
  }

  return $typeHandler->getTypesAsHumanReadableList($types);
}
