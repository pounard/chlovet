<?php
/**
 * Placeholder file.
 */

/**
 * Implements hook_page_build().
 */
function chlovet_page_build() {
  // @todo For some unknown reason sometime it's missing
  drupal_add_library('system', 'ui.droppable');
}

/**
 * Implements hook_filter_info().
 */
function chlovet_filter_info() {
  return [
    'chlovet_htmlawed' => [
      'title'             => t('Limit allowed HTML tags'),
      'process callback'  => 'chlovet_filter_htmlawed',
      'settings callback' => 'chlovet_filter_htmlawed_settings',
      'default settings'  => [
        'allowed_html'    => chlovet_filter_htmlawed_default(),
        'filter_html_nofollow' => 0,
      ],
    ],
  ];
}

/**
 * Default tags allowed for stripped HTML.
 */
function chlovet_filter_htmlawed_default() {
  return 'a, em, strong, cite, blockquote, ol, ul, li, dl, dt, dd';
}

/**
 * Filter settings callback.
 */
function chlovet_filter_htmlawed_settings($form, &$form_state, $filter, $format, $defaults) {
  $filter->settings += $defaults;
  return [
    'allowed_html' => [
      '#type'           => 'textfield',
      '#title'          => t('Allowed HTML tags'),
      '#default_value'  => $filter->settings['allowed_html'],
      '#maxlength'      => 1024,
      '#description'    => t('A list of HTML tags that can be used. JavaScript event attributes, JavaScript URLs, and CSS are always stripped.'),
    ],
    'filter_html_nofollow' => [
      '#type'           => 'checkbox',
      '#title'          => t('Add rel="nofollow" to all links'),
      '#default_value'  => $filter->settings['filter_html_nofollow'],
    ],
  ];
}

/**
 * Filter process callback.
 */
function chlovet_filter_htmlawed($text, $filter) {

  if (empty($text)) {
    return $text;
  }

  # @todo allow image attributes, we need that for media udnd from ucms_contrib
  $spec   = '';
  $config = [
    'safe'            => 1,
    'keep_bad'        => 0,
    'elements'        => $filter->settings['allowed_html'] ?? chlovet_filter_htmlawed_default(),
    'deny_attribute'  => 'id, style'
  ];

  return htmLawed($text, $config, $spec);
}

/**
 * Implements hook_library_alter().
 */
function chlovet_library_alter(&$libraries, $module) {
  if ($module === 'system') {
    $path = drupal_get_path('module', 'chlovet') . '/js/jquery';

    // jQuery UI CSS
    $names = drupal_map_assoc([
      'ui.menu', 'ui.spinner', 'ui.tooltip',
      'ui.accordion', 'ui.autocomplete', 'ui.button', 'ui.datepicker', 'ui.dialog',
      'ui.progressbar', 'ui.resizable', 'ui.selectable', 'ui.slider', 'ui.tabs',
      'ui.menu', 'ui.spinner', 'ui.tooltip',
    ]);
    $names['ui'] = 'ui.core';
    foreach ($names as $name => $file) {
      $libraries[$name]['css']["misc/ui/jquery.$file.css"]['data'] = $path . '/ui/themes/base/minified/jquery.' . $file . '.min.css';
    }
    $libraries['ui']['css']['misc/ui/jquery.ui.theme.css']['data'] = $path . '/ui/themes/base/minified/jquery.theme.min.css';

    // jQuery UI JS
    $names = drupal_map_assoc([
      'ui.menu', 'ui.spinner', 'ui.tooltip',
      'ui.accordion', 'ui.autocomplete', 'ui.button', 'ui.datepicker', 'ui.dialog', 'ui.draggable',
      'ui.droppable', 'ui.mouse', 'ui.position', 'ui.progressbar', 'ui.resizable', 'ui.selectable',
      'ui.slider', 'ui.sortable', 'ui.tabs', 'ui.widget', 'ui.spinner', 'ui.menu', 'ui.tooltip',
    ]);
    $names['ui'] = 'ui.core';
    $names['effects'] = array('effects.core', 'ui.effect');
    $names = jquery_update_make_library_hook_to_file_name_segment_map_for_effects($names);
    foreach ($names as $name => $file) {
      list($file_core, $file_updated) = is_array($file) ? $file : array($file, $file);
      $corefile = 'misc/ui/jquery.' . $file_core . '.min.js';
      $libraries[$name]['js'][$corefile]['data'] = $path . '/ui/minified/jquery.' . $file_updated . '.min.js';
      $libraries[$name]['version'] = '1.10.4';
    }
  }
}

/**
 * Implements hook_preprocess_node().
 */
function chlovet_preprocess_node(&$variables) {
  /** @var \Drupal\node\NodeInterface $node */
  $node = $variables['node'];

  switch ($variables['view_mode']) {

    case 'full':
      $variables['search_total'] = 0;
      $variables['search_displayed'] = 0;
      $variables['search_results'] = [];

      // Do not wake everyone if unnecessary
      if (isset($_GET['s'])) {
        /** @var \MakinaCorpus\Ucms\Search\SearchFactory $factory */
        $factory = \Drupal::service('ucms_search.search_factory');
        $search = $factory->create('private');

        // @todo later add facet
        $search->getFilterQuery()->matchTerm('status', 1)->matchTermCollection('type', ['page']);
        $response = $search->setLimit(16)->doSearch($_GET);

        if ($response->isSuccessful()) {
          $nodeIdList = $response->getAllNodeIdentifiers();

          $variables['search_total'] = $response->getTotal();
          $variables['search_displayed'] = count($nodeIdList);

          foreach (node_load_multiple($nodeIdList) as $node) {
            $variables['search_results'][] = node_view($node, 'search_result');
          }
        }
      }
      break;
  }
}

/**
 * Implements hook_system_info_alter().
 */
function chlovet_system_info_alter(&$info, $file, $type) {
  // Inherit system theme settings from parent
  if ($type == 'theme' && isset($info['base theme'])) {
    $base = $info['base theme'];
    $parent_info = drupal_parse_info_file(
      drupal_get_path('theme', $base).'/'.$base.'.info'
    );
    if (isset($parent_info['settings'])) {
      if (isset($info['settings'])) {
        // FIXME may not merge gulpifier whitelists
        $info['settings'] = array_merge_recursive(
          $parent_info['settings'],
          $info['settings']
        );
      } else {
        $info['settings'] = $parent_info['settings'];
      }
    }
  }
}

/**
 * Implements hook_ckeditor_settings_alter().
 */
function chlovet_ckeditor_settings_alter(&$settings, $context) {
  $settings['stylesCombo_stylesSet'] = 'drupal:/' . drupal_get_path('module', 'chlovet') . '/js/ckeditor/styles.js';
  $settings['contentsCss'][] = url(drupal_get_path('module', 'chlovet') . '/js/ckeditor/styles.css');
}

/**
 * Implements hook_form_FORM_ID_alter()
 */
function chlovet_form_user_login_alter(&$form, &$form_state, $form_id) {
  array_unshift($form['#validate'], 'chlovet_user_login_replace_email_by_name');
}

/**
 * Validate callback handler to replace email by username if any.
 */
function chlovet_user_login_replace_email_by_name($form, &$form_state) {
  $login = $form_state['values']['name'];

  if (!empty($form_state['values']['name']) && valid_email_address($login)) {

    $name = db_select('users', 'u')
      ->fields('u', array('name'))
      ->condition('u.mail', $login)
      ->execute()
      ->fetchField()
    ;

    if ($name) {
      $form_state['values']['name'] = $name;
    }
  }
}
