<?php

/**
 * Implements hook_field_formatter_info().
 */
function chlovet_field_formatter_info() {
  return [
    'list_type' => [
      'label'       => t('Content list'),
      'field types' => ['list_text'],
      'settings'    => [
        'view_mode'   => 'default',
        'limit'       => 3,
        'order_field' => 'created',
        'order'       => 'desc',
        'pager'       => false,
      ],
    ],
  ];
}

/**
 * View mode to render this field.
 */
function _chlovet_field_formatter_view_mode_options() {
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
function chlovet_field_formatter_field_formatter_settings_form($field, $instance, $view_mode, $form, &$form_state) {
  $element = [];
  $display = $instance['display'][$view_mode];
  $settings = $display['settings'];

  if (!empty($settings['view_mode'])) {
    $element['view_mode'] = [
      '#title'          => t('View mode'),
      '#type'           => 'select',
      '#options'        => _chlovet_field_formatter_view_mode_options(),
      '#default_value'  => $settings['view_mode'],
      '#required'       => true,
    ];
    $element['order_field'] = [
      '#title'          => t('Order on'),
      '#type'           => 'select',
      '#options'        => ['created' => t("Creation date"), 'updated' => t("Last update date")],
      '#default_value'  => $settings['order_field'],
      '#required'       => true,
    ];
    $element['order'] = [
      '#title'          => t('Order'),
      '#type'           => 'select',
      '#options'        => ['asc' => t("Ascending"), 'desc' => t("Descending")],
      '#default_value'  => $settings['order'],
      '#required'       => true,
    ];
    $element['limit'] = [
      '#title'          => t('Number of items to display'),
      '#type'           => 'select',
      '#options'        => drupal_map_assoc(range(1, 50)),
      '#default_value'  => $settings['limit'],
      '#required'       => true,
    ];
    $element['pager'] = [
      '#title'          => t('Display pager'),
      '#type'           => 'checkbox',
      '#default_value'  => $settings['pager'],
    ];
  }

  return $element;
}

/**
 * Implements hook_field_formatter_view().
 */
function chlovet_field_formatter_view($entity_type, $entity, $field, $instance, $langcode, $items, $display) {
  $function = '_chlovet_formatter_' . $display['type'];
  if (function_exists($function)) {
    return $function($entity_type, $entity, $field, $instance, $langcode, $items, $display);
  }
  return [];
}

/**
 * List type formatter.
 */
function _chlovet_formatter_list_type($entity_type, $entity, $field, $instance, $langcode, $items, $display) {
  $element = [];
  $siteManager = ucms_site_manager();

  if ($siteManager->getContext()) {

    $typeList   = [];
    $exclude    = [];
    $viewMode   = $display['settings']['view_mode'] ?? 'default';
    $limit      = $display['settings']['limit'] ?? 3;
    $orderField = $display['settings']['order_field'] ?? 'created';
    $order      = $display['settings']['order'] ?? 'desc';
    $usePager   = $display['settings']['pager'] ?? false;

    foreach ($items as $delta => $item) {
      $type = $item['value'];

      // Exclude current node whenever it matches the conditions
      if (($current = menu_get_object()) && $current->bundle() === $type) {
        $exclude[] = $current->id();
      }

      $typeList[] = $type;
    }

    $query = db_select('node', 'n');
    $query->join('ucms_site_node', 'un', 'un.nid = n.nid');

    if ($exclude) {
      $query->condition('n.nid', $exclude, 'NOT IN');
    }

    $query
      ->fields('n', ['nid'])
      ->condition('n.type', $typeList)
      ->condition('n.status', NODE_PUBLISHED)
      ->condition('un.site_id', $siteManager->getContext()->getId())
      ->orderBy('n.' . $orderField, $order)
      ->addMetaData('entity', $entity)
      ->addMetaData('type_listed', $type)
      ->addTag('node_access')
    ;

    // We CANNOT get view_mode of full node with hook_field_formatter_view().
    // That's why we do this ugly check to avoid rewriting all everything...
    // Hope (!) it will do the tricks before YOU apply a real fix ;-)
    if ($usePager) {
      $query = $query->extend('PagerDefault'); // You get a clone. NEEDED!!
      $query->limit($limit);
    } else {
      $query->range(0, $limit);
    }

    $nids = $query->execute()->fetchCol();
    $nodes = [];
    if (!empty($nids)) {
      $nodes = node_load_multiple($nids);
      $element[$delta]['results'] = [
        '#theme'      => 'chlovet_list_type',
        '#nodes'      => $nodes,
        '#type'       => $typeList,
        '#view_mode'  => $viewMode,
        '#pager'      => $usePager ? ['#theme' => 'pager', '#element' => PagerDefault::$maxElement - 1] : null,
        '#limit'      => $limit,
        '#count'      => count($nodes),
      ];
    } else {
      $element[$delta]['noresult']['#markup'] = '<p class="col-xs-12 text-center">Pas de contenu pour le moment</p>';
    }
  }

  return $element;
}
