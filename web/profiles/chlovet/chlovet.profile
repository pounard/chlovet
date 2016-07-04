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

/**
 * Alter the Security Kit settings.
 *
 * @param array $options
 *   Array of runtime options.
 *
 * @see _seckit_get_options().
*/
function chlovet_seckit_options_alter(&$options) {
  // HSTS : SSL mode enforced, stored in browser, browser will reject attempts at http:// for this domain
  // after first visits.
  $options['seckit_ssl']['hsts'] = variable_get('chlovet_security_SSL_for_all_websites', False);
  $options['seckit_ssl']['hsts_max_age']= 31536000;

  // Clickjacking protections, prevent usages in Frames
  $options['seckit_clickjacking']['js_css_noscript'] = 0;
  // We allow it for preview mode
  $options['seckit_clickjacking']['x_frame'] = 1; //SECKIT_X_FRAME_SAMEORIGIN;
  //$options['seckit_clickjacking']['x_frame'] = 2; //SECKIT_X_FRAME_DENY;

  // csrf protections
  $options['seckit_csrf']['origin'] = 1;
  // In case we need to allow cross-site POST of things, one day
  //$hsm_websites = array('http://foo.com', 'http://bar.com');
  //$options['seckit_csrf']['origin_whitelist'] = implode(',', $hsm_websites);

  // enable browser reflected xss protection, be careful, without block it may be used
  // to generates issues by blocking specific js in the page
  $options['seckit_xss']['x_xss'] = 2; // 2 means "1; mode=block"

  $master = variable_get('ucms_site_master_hostname');
  $options['seckit_xss']['csp']['checkbox'] = variable_get('chlovet_security_enable_CSP', True);
  $options['seckit_xss']['csp']['report-only'] = variable_get('chlovet_security_debug_CSP', True);
  $options['seckit_xss']['csp']['default-src'] = variable_get('chlovet_security_CSP_default', "'self'")
      . ' ' . variable_get('ucms_site_cdn_uri')
      ;
  $options['seckit_xss']['csp']['font-src'] = variable_get('chlovet_security_CSP_fonts', "'self'")
      . ' data: '
      . variable_get('ucms_site_cdn_uri')
      ;
  $options['seckit_xss']['csp']['script-src'] = variable_get('chlovet_security_CSP_script', "'self'")
      . " 'unsafe-inline'"
      . ' '
      . variable_get('ucms_site_cdn_uri')
      ;
  $options['seckit_xss']['csp']['style-src'] = variable_get('chlovet_security_CSP_style', "'self'")
      . " 'unsafe-inline'"
      . ' '
      . variable_get('ucms_site_cdn_uri')
      ;
  // AJAX & websockets
  $options['seckit_xss']['csp']['connect-src'] = variable_get('chlovet_security_CSP_connect', "'self'")
      . ' http://' . $master // required for SSO <--- remove this one one day
      . ' https://' . $master // required for SSO
      ;
  // img
  $options['seckit_xss']['csp']['img-src'] = '* data:';
  // media - audio and video
  $options['seckit_xss']['csp']['media-src'] = '*';

  // Frames
  $options['seckit_xss']['csp']['frame-src'] =  variable_get('chlovet_security_CSP_frame', 'none');
}
