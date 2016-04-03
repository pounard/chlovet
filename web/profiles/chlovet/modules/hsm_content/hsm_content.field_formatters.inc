<?php

/**
 * @file
 * Declares composite formatters.
 *
 * For composite lists, we mainly need to store some settings to each
 * view mode, so we use the power of formatters for that.
 *
 * * List by type:
 *    - options_list of types and simple widget
 *    - custom formatter that stores the view mode of listed nodes
 *
 * * List by tree:
 *    - nothing to store, virtual field
 *    - custom formatter that stores the view mode of listed nodes
 */

/**
 * Implements hook_field_formatter_info().
 */
function hsm_content_field_formatter_info() {
  return [
    'list_type' => [
      'label'       => t('List by content type'),
      'field types' => ['list_text'],
      'settings'    => [
        'view_mode' => 'default',
      ],
    ],
  ];
}

/**
 * View mode to render this field.
 *
 * @return array
 */
function _hsm_content_view_mode_options() {
  $view_modes = [];
  $entity_info = entity_get_info('node');
  foreach ($entity_info['view modes'] as $view_mode => $view_mode_info) {
    $view_modes[$view_mode] = $view_mode_info['label'];
  }
  return $view_modes;
}


/**
 * Implements hook_field_formatter_settings_form().
 */
function hsm_content_field_formatter_settings_form($field, $instance, $view_mode, $form, &$form_state) {
  $display = $instance['display'][$view_mode];
  $settings = $display['settings'];

  $element['view_mode'] = [
    '#title'         => t('View mode'),
    '#type'          => 'select',
    '#options'       => _hsm_content_view_mode_options(),
    '#default_value' => $settings['view_mode'],
    '#required'      => TRUE,
  ];

  return $element;
}

/**
 * Implements hook_field_formatter_settings_summary().
 */
function hsm_content_field_formatter_settings_summary($field, $instance, $view_mode) {
  $display = $instance['display'][$view_mode];
  $settings = $display['settings'];

  return t('View mode: %view_mode', ['%view_mode' => $settings['view_mode']]);
}

/**
 * Implements hook_field_formatter_view().
 */
function hsm_content_field_formatter_view($entity_type, $entity, $field, $instance, $langcode, $items, $display) {
  $element = [];
  $settings = $display['settings'];

  switch ($display['type']) {
    case 'list_type':
      // This should not really be used with multiple values, and should always
      // be required...
      $node_type = $items[0]['value'];

      $siteManager = ucms_site_manager();

      $query = db_select('node', 'n');
      // Specify this in case we are admin
      $query->join('ucms_site_node', 'un', 'un.nid = n.nid');
      $nids = $query
        ->fields('n', ['nid'])
        ->condition('n.type', $node_type)
        ->condition('n.status', NODE_PUBLISHED)
        ->condition('un.site_id', $siteManager->getContext()->getId())
        ->addTag('node_access')
        ->extend('PagerDefault')
        ->limit(10)
        ->execute()
        ->fetchCol();

      if (!empty($nids)) {
        $nodes = node_load_multiple($nids);
        $element = node_view_multiple($nodes, $settings['view_mode']);
        $element['pager']['#theme'] = 'pager';
      }
      else {
        $element['#markup'] = 'Pas de contenu pour le moment';
      }
      break;
  }

  return $element;
}
