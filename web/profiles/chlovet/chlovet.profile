<?php
/**
 * Placeholder file.
 */

/**
 * We need this.
 */
require_once __DIR__ . '/chlovet.field.inc';

/**
 * Implements hook_boot().
 */
function chlovet_boot() {
  // Prepolute some Drupal caches to avoid useless SQL queris.
  drupal_static('system_date_format_locale', [
    'fr' => [
      'medium' => false,
    ]
  ]);
  drupal_static('system_get_date_types', [
    'long' => [
      'module' => 'system',
      'type' => 'long',
      'title' => 'Long',
      'locked' => 1,
      'is_new' => false,
    ],
    'medium' =>[
      'module' => 'system',
      'type' => 'medium',
      'title' => 'Medium',
      'locked' => 1,
      'is_new' => false,
    ],
    'short' => [
      'module' => 'system',
      'type' => 'short',
      'title' => 'Short',
      'locked' => 1,
      'is_new' => false,
    ],
  ]);
  drupal_static('language_list', [
    'language' => [
      'en' => (object)[
        'language' => 'en',
        'name' => 'English',
        'native' => 'English',
        'direction' => '0',
        'enabled' => '0',
        'plurals' => '0',
        'formula' => '',
        'domain' => '',
        'prefix' => '',
        'weight' => '0',
        'javascript' => '',
      ],
      'fr' =>  (object)[
        'language' => 'fr',
        'name' => 'French',
        'native' => 'Français',
        'direction' => '0',
        'enabled' => '1',
        'plurals' => '0',
        'formula' => '',
        'domain' => '',
        'prefix' => 'fr',
        'weight' => '0',
        'javascript' => '',
      ],
    ],
    'enabled' => [[
      'en' =>  (object)[
        'language' => 'en',
        'name' => 'English',
        'native' => 'English',
        'direction' => '0',
        'enabled' => '0',
        'plurals' => '0',
        'formula' => '',
        'domain' => '',
        'prefix' => '',
        'weight' => '0',
        'javascript' => '',
      ],
      ], [
      'fr' =>  (object)[
        'language' => 'fr',
        'name' => 'French',
        'native' => 'Français',
        'direction' => '0',
        'enabled' => '1',
        'plurals' => '0',
        'formula' => '',
        'domain' => '',
        'prefix' => 'fr',
        'weight' => '0',
        'javascript' => '',
      ],
    ]],
  ]);
}

/**
 * Implements hook_html_head_alter().
 */
function chlovet_html_head_alter(&$head_elements) {
  foreach ($head_elements as &$element) {
    if (isset($element['#attributes']['rel']) && 'shortcut icon' === $element['#attributes']['rel']) {
      $element['#attributes']['href'] = url(drupal_get_path('profile', 'chlovet') . '/favicon.png');
    }
  }
}

/**
 * Implements hook_js_alter().
 */
function chlovet_js_alter(&$js) {
  foreach ($js as $index => $info) {
    if ('file' === $info['type'] && false === strpos($info['data'], 'ckeditor')) {
      unset($js[$index]);
    }
  }
  $js['profiles/chlovet/dist/public.min.js'] = [
    'type' => 'file',
    'group' => -100,
    'weight' => 2.007,
    'version' => null,
    'type' => "file",
    'every_page' => false,
    'requires_jquery' => false,
    'scope' => "header",
    'cache' => true,
    'defer' => false,
    'preprocess' => false,
    'data' => 'profiles/chlovet/dist/public.min.js',
  ];
  if (user_is_logged_in()) {
    $js['profiles/chlovet/dist/admin.min.js'] = [
      'type' => 'file',
      'group' => -100,
      'weight' => 2.007,
      'version' => null,
      'type' => "file",
      'every_page' => false,
      'requires_jquery' => false,
      'scope' => "header",
      'cache' => true,
      'defer' => false,
      'preprocess' => false,
      'data' => 'profiles/chlovet/dist/admin.min.js',
    ];
  }
}

function _chlovet_realpath_recursion(array $pieces, int $start): string {
  for ($i = $start; $i < count($pieces); ++$i) {
    if ('.' === $pieces[$i]) {
      unset($pieces[$i]);
      return _chlovet_realpath_recursion(array_values($pieces), $i);
    } else if ('..' === $pieces[$i] && 0 !== $i) {
      unset($pieces[$i], $pieces[$i - 1]);
      return _chlovet_realpath_recursion(array_values($pieces), $i - 1);
    }
  }
  return implode('/', $pieces);
}

function _chlovet_realpath(string $path): string {
  return _chlovet_realpath_recursion(explode('/', $path), 0);
}

/**
 * Implements hook_js_alter().
 */
function chlovet_css_alter(&$css) {
  foreach ($css as $name => $settings) {
    if ($settings['group'] === CSS_THEME) {
      // Keep theme files
      if (isset($settings['data']) && 'file' === $settings['type']) {
        $css[$name]['data'] = _chlovet_realpath($settings['data']);
      }
      $css[$name]['preprocess'] = false;
    } elseif ($settings['type'] === 'inline') {
      // This will disable @import commands, thus fewer requests.
      $css[$name]['preprocess'] = false;
    } elseif ('external' !== $settings['type']) {
      // Exclude all others
      unset($css[$name]);
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
