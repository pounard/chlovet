<?php
/**
 * @file
 * Drupal install speed.
 */

/**
 * Yes, this is important.
 */
set_time_limit(0);

/**
 * PHP <5.3.6 compat.
 */
if (!defined('DEBUG_BACKTRACE_IGNORE_ARGS')) {
  define('DEBUG_BACKTRACE_IGNORE_ARGS', false);
}

/******************************************************************************
 *
 * null path.inc replacement
 *
 ******************************************************************************/

function drupal_path_initialize() {}

function drupal_lookup_path($action, $path = '', $path_language = NULL) {
  if ($action === 'alias') {
    return $path;
  }
  return false;
}

function drupal_cache_system_paths() {}

function drupal_get_path_alias($path = NULL, $path_language = NULL) {
  return drupal_lookup_path('alias', $path, $path_language);
}

function drupal_get_normal_path($path, $path_language = NULL) {
  return drupal_lookup_path('source', $path, $path_language);
}

function drupal_is_front_page() {
  return false;
}

function drupal_match_path($path, $patterns) {
  $regexps = &drupal_static(__FUNCTION__);
  if (!isset($regexps[$patterns])) {
    // Convert path settings to a regular expression.
    // Therefore replace newlines with a logical or, /* with asterisks and the <front> with the frontpage.
    $to_replace = array(
        '/(\r\n?|\n)/', // newlines
        '/\\\\\*/',     // asterisks
        '/(^|\|)\\\\<front\\\\>($|\|)/' // <front>
    );
    $replacements = array(
        '|',
        '.*',
        '\1' . preg_quote(variable_get('site_frontpage', 'node'), '/') . '\2'
    );
    $patterns_quoted = preg_quote($patterns, '/');
    $regexps[$patterns] = '/^(' . preg_replace($to_replace, $replacements, $patterns_quoted) . ')$/';
  }
  return (bool)preg_match($regexps[$patterns], $path);
}

function current_path() {
  return 'install';
}

function drupal_path_alias_whitelist_rebuild($source = NULL) {}

function path_load($conditions) {}

function path_save(&$path) {}

function path_delete($criteria) {}

function path_is_admin($path) {
  $path_map = &drupal_static(__FUNCTION__);
  if (!isset($path_map['admin'][$path])) {
    $patterns = path_get_admin_paths();
    $path_map['admin'][$path] = drupal_match_path($path, $patterns['admin']);
    $path_map['non_admin'][$path] = drupal_match_path($path, $patterns['non_admin']);
  }
  return $path_map['admin'][$path] && !$path_map['non_admin'][$path];
}

function path_get_admin_paths() {
  $patterns = &drupal_static(__FUNCTION__);
  if (!isset($patterns)) {
    $paths = module_invoke_all('admin_paths');
    drupal_alter('admin_paths', $paths);
    // Combine all admin paths into one array, and likewise for non-admin paths,
    // for easier handling.
    $patterns = array();
    $patterns['admin'] = array();
    $patterns['non_admin'] = array();
    foreach ($paths as $path => $enabled) {
      if ($enabled) {
        $patterns['admin'][] = $path;
      }
      else {
        $patterns['non_admin'][] = $path;
      }
    }
    $patterns['admin'] = implode("\n", $patterns['admin']);
    $patterns['non_admin'] = implode("\n", $patterns['non_admin']);
  }
  return $patterns;
}

function drupal_valid_path($path, $dynamic_allowed = FALSE) {
  global $menu_admin;
  // We indicate that a menu administrator is running the menu access check.
  $menu_admin = TRUE;
  if ($path == '<front>' || url_is_external($path)) {
    $item = array('access' => TRUE);
  }
  elseif ($dynamic_allowed && preg_match('/\/\%/', $path)) {
    // Path is dynamic (ie 'user/%'), so check directly against menu_router table.
    if ($item = db_query("SELECT * FROM {menu_router} where path = :path", array(':path' => $path))->fetchAssoc()) {
      $item['link_path']  = $item['path'];
      $item['link_title'] = $item['title'];
      $item['external']   = FALSE;
      $item['options'] = '';
      _menu_link_translate($item);
    }
  }
  else {
    $item = menu_get_item($path);
  }
  $menu_admin = FALSE;
  return $item && $item['access'];
}

function drupal_clear_path_cache($source = NULL) {}

/******************************************************************************
 *
 * API methods
 *
 ******************************************************************************/

/**
 * Overrides the Drupal error and exception handler.
 */
function speed_install_set_error_handler() {

  set_error_handler(function ($error_level, $message, $filename, $line, $context) {
    $reporting = error_reporting();
    // Error reporting is set to 0 when the call was prefixed by @
    if ($reporting !== 0 && $error_level & $reporting) {
      $i = 0;
      $dtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
      $strace = [];
      //array_shift($dtrace); // This closure.
      foreach ($dtrace as $caller) {
        if (isset($caller['file'])) {
          $strace[$i] = "#$i {$caller['file']}({$caller['line']}): {$caller['function']}()";
        } else {
          $strace[$i] = "#$i [internal function]: {$caller['function']}()";
        }
        ++$i;
      }
      print $filename . '(' . $line . '): ' . $message;
      print implode("\n", $strace) . "\n";
      flush();
    }
  });

  set_exception_handler(function (Exception $e) {
    print $e->getMessage() . "\n";
    print $e->getTraceAsString() . "\n";
    flush();
  });
}

/**
 * Enable and install modules more quickly than core alternative.
 */
function speed_module_enable($module) {

  // Only process modules that are not already enabled.
  $existing = db_query("SELECT status, info, schema_version FROM {system} WHERE name = :name", [':name' => $module])->fetch();

  if (!$existing) {
    throw new \Exception(sprintf("%s %s: system table is not complete during install", $module, $type));
  }

  $enabled = (bool)$existing->status;
  $info = (bool)unserialize($existing->info);
  $schema_version = $existing->schema_version;

  if ($enabled) {
    return;
  }

  // Load module code.
  drupal_load('module', $module);
  module_load_install($module);
  db_query("UPDATE {system} SET status = 1 WHERE name = :name", [':name' => $module]);

  _system_update_bootstrap_status();
  drupal_static_reset();
  module_list(true);

  // Update the registry only if the module defines files.
  if (!empty($info['files'])) {
    registry_update();
  }

  // Refresh the schema to include it, we cannot really make it conditional
  // because of hook_schema_alter() that could alter either this module schema
  // or be implemented by this module too.
  drupal_get_schema(null, true);

  // Now install the module if necessary.
  if (SCHEMA_UNINSTALLED == $schema_version) {
    drupal_install_schema($module);

    // Set the schema version to the number of the last update provided
    // by the module.
    $versions = drupal_get_schema_versions($module);
    $version = $versions ? max($versions) : SCHEMA_INSTALLED;

    // If the module has no current updates, but has some that were
    // previously removed, set the version to the value of
    // hook_update_last_removed().
    if ($last_removed = module_invoke($module, 'update_last_removed')) {
      $version = max($version, $last_removed);
    }
    drupal_set_installed_schema_version($module, $version);
    // Allow the module to perform install tasks.
    module_invoke($module, 'install');
  }

  module_invoke($module, 'enable');
}

/**
 * Prepare environment.
 */
function speed_install_pre_bootstrap() {

  $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
  $_SERVER['REQUEST_METHOD'] = 'POST';

  define('DRUPAL_ROOT', getcwd());
  define('MAINTENANCE_MODE', 'install');

  // Bootstrap database configuration.
  require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
  drupal_bootstrap(DRUPAL_BOOTSTRAP_CONFIGURATION);

  require_once DRUPAL_ROOT . '/includes/cache.inc';
  require_once DRUPAL_ROOT . '/includes/cache-install.inc';
  require_once DRUPAL_ROOT . '/includes/database/database.inc';
  require_once DRUPAL_ROOT . '/includes/common.inc';
  require_once DRUPAL_ROOT . '/includes/entity.inc';
  require_once DRUPAL_ROOT . '/includes/file.inc';
  require_once DRUPAL_ROOT . '/includes/install.inc';
  require_once DRUPAL_ROOT . '/includes/language.inc';
  require_once DRUPAL_ROOT . '/includes/module.inc';
  require_once DRUPAL_ROOT . '/includes/theme.inc';
  require_once DRUPAL_ROOT . '/includes/unicode.inc';
  require_once DRUPAL_ROOT . '/modules/system/system.install';
  require_once DRUPAL_ROOT . '/modules/system/system.module';
  require_once DRUPAL_ROOT . '/modules/user/user.install';
  require_once DRUPAL_ROOT . '/modules/user/user.module';
}

// An now, install
speed_install_pre_bootstrap();
speed_install_set_error_handler();

/**
 * Null cache backend for install only.
 */
class NullCacheBackend implements DrupalCacheInterface {
  function get($cid) { return false; }
  function getMultiple(&$cids) {}
  function set($cid, $data, $expire = CACHE_PERMANENT) {}
  function clear($cid = null, $wildcard = false) {}
  function isEmpty() { return true; }
}

/******************************************************************************
 *
 * Real installation
 *
 ******************************************************************************/

if (db_table_exists('system')) {
  throw new Exception("Drupal already installed");
}

$module_list = [];
$module_list['system']['filename'] = 'modules/system/system.module';
$module_list['user']['filename'] = 'modules/user/user.module';
module_list(true, false, false, $module_list);

// We will need this to do a few overrides.
global $conf;
$conf['path_inc'] = substr(__FILE__, strlen(DRUPAL_ROOT) + 1);
$conf['cache_default_class'] = 'NullCacheBackend';

// @todo fixme
$profile = 'chlovet';
$profile_file = DRUPAL_ROOT . "/profiles/$profile/$profile.profile";
if (!isset($profile) || !file_exists($profile_file)) {
  throw new Exception(sprintf("%s: profile not found", $profile));
}
$info_file = DRUPAL_ROOT . "/profiles/$profile/$profile.info";
if (!file_exists($info_file)) {
  throw new Exception(sprintf("%s: profile not found", $profile));
}
$info = drupal_parse_info_file($info_file);
echo "Profile ", $profile, " found\n";

// We cant do better than this function, since that system module bootstraps
// itself without any core subsystem ready yet, but then rebuilds the module
// data, which we do need too, before enabling the user and other modules.
drupal_install_system();
echo "System module installed\n";

speed_module_enable('user');
echo "User module installed\n";

// This must happend after syste module has been installed to ensure that the
// variable table exists.
variable_set('install_profile', $profile);

// We also need to rebuild all caches and let the system_rebuild_module_data()
// to ensure that the profile will be in the system list.
drupal_static_reset();
module_list(true);

// Rebuild the initial registry to ensures that classes in Drupal .inc files
// are correctly loaded once installed.
registry_update();

$module_list = $info['dependencies'];
// Node is a required module, most modules don't set the explicit dependency
// upon it, which makes profiles that don't set node in their module list to
// fail at install.
if (!in_array('node', $module_list)) {
  array_unshift($module_list, 'node');
}
// Same goes for the filter module.
if (!in_array('filter', $module_list)) {
  array_unshift($module_list, 'filter');
}

drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
echo "Drupal fully bootstrapped\n";

// Rebuild the modules dependency tree.
$module_data = system_rebuild_module_data();
$module_list = array_flip(array_values($module_list));
while (list($module) = each($module_list)) {
  if (!isset($module_data[$module])) {
    throw new Exception(sprintf("%s: module is missing from filesystem", $module));
  }
  $module_list[$module] = $module_data[$module]->sort;
  foreach (array_keys($module_data[$module]->requires) as $dependency) {
    if (!isset($module_list[$dependency])) {
      $module_list[$dependency] = 0;
    }
  }
}
arsort($module_list);
$module_list = array_keys($module_list);

// Install modules, really.
foreach ($module_list as $module) {
  speed_module_enable($module);
  echo "Module ", $module, " installed\n";
}

// Enable the profile
speed_module_enable($profile);
echo "Profile ", $profile, " installed\n";

// Delay all hook_module_installed().
module_invoke_all('modules_installed', $module_list);
module_invoke_all('modules_enabled', $module_list);

// Rebuild the theme registry, since we removed it from module_enable().
drupal_theme_rebuild();
