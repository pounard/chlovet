<?php

/**
 * Forms alteration.
 */
require_once __DIR__ . '/templates/form/template.php';

/**
 * Toolbar alterations.
 */
require_once __DIR__ . '/templates/toolbar/template.php';

/**
 * µCMS alterations.
 */
require_once __DIR__ . '/templates/ucms/template.php';

/**
 * Implements hook_theme_registry_alter().
 */
function vetbase_theme_registry_alter(&$theme_registry) {
  // Add an empty_list variable
  $theme_registry['item_list']['variables']['empty_list'] = '';
}

/**
 * Implements hook_preprocess().
 */
function vetbase_preprocess(&$vars) {
  $vars['logged_in'] = \Drupal::currentUser()->isAuthenticated();
}

/**
 * Implements hook_preprocess_HOOK().
 */
function vetbase_preprocess_ucms_layout_item(&$vars) {
  // For now, we override the view_mode and replace it by region name.
  // This enables subtheme to override given certain condition, and may be
  // replaced later by a user setting from ucms_layout table.
  $vars['view_mode'] = $vars['region']->getName();
}

/**
 * Append current page menus to template variables.
 */
function _vatbase_add_menus(&$variables) {

  $siteManager  = ucms_site_manager();
  $hasContext   = $siteManager->hasContext();
  $siteContext  = $hasContext ? $siteManager->getContext() : null;

  if ($siteContext) {
    $menuTree = umenu_get_manager()->buildTree('site-main-' . $siteContext->getId());

    // Main menu
    if ($menuTree) {
      $variables['menu'] = [
        '#tree'  => $menuTree,
        '#theme' => 'umenu__main_menu',
      ];
    }

    if ($menuTree && ($node = menu_get_object())) {
      $menuCurrent = $menuTree->getMostRevelantItemForNode($node->nid);

      // Child entries
      if ($menuCurrent && $menuCurrent->hasChildren()) {
        $variables['menu_children'] = [
          '#tree'  => $menuCurrent,
          '#theme' => 'umenu__children',
        ];
      }

      // Create siblings
      if ($menuCurrent && $menuCurrent->getParentId()) {
        $variables['menu_siblings'] = [
          '#tree'     => $menuTree->getItemById($menuCurrent->getParentId()),
          '#current'  => $menuCurrent->getNodeId(),
          '#theme'    => 'umenu__siblings',
        ];
      }

      if ($menuCurrent) {
        $variables['menu_navigation'] = [
          '#tree'     => $menuTree,
          '#current'  => $menuCurrent->getNodeId(),
          '#theme'    => 'umenu__navigation',
        ];
      }
    }
  }
}

/**
 * Implements hook_preprocess_HOOK().
 */
function vetbase_preprocess_page(&$variables) {

  $variables['isFrontPage'] = drupal_is_front_page();

  $siteManager  = ucms_site_manager();
  $hasContext   = $siteManager->hasContext();
  $siteContext  = $hasContext ? $siteManager->getContext() : null;

  // Force some JS inclusion.
  $opts = ['preprocess' => true, 'every_page' => true];
  $path = drupal_get_path('theme', 'vetbase') . '/../resources';
  if (theme_get_setting('gulpifier_single_js')) {
    drupal_add_js($path . '/bootstrap/js/alert.js', $opts);
    drupal_add_js($path . '/bootstrap/js/button.js', $opts);
    drupal_add_js($path . '/bootstrap/js/collapse.js', $opts);
    drupal_add_js($path . '/bootstrap/js/dropdown.js', $opts);
    drupal_add_js($path . '/bootstrap/js/modal.js', $opts);
    drupal_add_js($path . '/bootstrap/js/tab.js', $opts);
    drupal_add_js($path . '/bootstrap/js/tooltip.js', $opts);
    drupal_add_js($path . '/bootstrap/js/popover.js', $opts);
  } else {
    drupal_add_js($path . '/bootstrap/dist/js/bootstrap.min.js', $opts);
  }

  // FIXME find a better way, this is already loaded during site:init event
  if (\Drupal::getContainer()->has('umenu.storage') && \Drupal::getContainer()->has('ucms_site.manager')) {
    /* @var $storage MakinaCorpus\Umenu\MenuStorageInterface */
    $storage = \Drupal::service('umenu.storage');
    /* @var $manager MakinaCorpus\Ucms\Site\SiteManager */
    $manager = \Drupal::service('ucms_site.manager');
    if ($manager->hasContext()) {
      $menuList = $storage->loadWithConditions(['site_id' => $manager->getContext()->getId()]);
      if ($menuList) {
        // Take the first one, hop... Ni vu ni connnu je t'embrouille.
        $variables['page']['menu'][] = menu_tree(reset($menuList)['name']);
      }
    }
  }

  if ($siteContext) {
    // Append, whenever possible, the search form, for that we need a
    // search component, take the first available
    $searchPageNid = db_select('node', 'n')
      ->fields('n', ['nid'])
      ->condition('n.type', 'search')
      ->condition('n.status', 1)
      ->addTag('node_access')
      ->addTag('ucms_site_access')
      ->range(0, 1)
      ->execute()
      ->fetchField()
    ;
    if ($searchPageNid) {
      $variables['search_url'] = url('node/' . $searchPageNid);
    }
  }

  _vatbase_add_menus($variables);
}

/**
 * Implements hook_preprocess_HOOK().
 */
function vetbase_preprocess_html(&$variables) {
  foreach (['toolbar', 'toolbar-drawer'] as $class) {
    if (false !== ($index = array_search($class, $variables['classes_array']))) {
      unset($variables['classes_array'][$index]);
    }
  }

  // Media is stupid.
  if (!empty($variables['page']['#theme']) && 'media_dialog_page' === $variables['page']['#theme']) {
    $variables['classes_array'][] = 'fix-iframe';
  }
}

/**
 * Implements hook_page_alter().
 */
function vetbase_page_alter(&$page) {
  // @see system_page_alter().
  // This stupid Drupal code puts the 'region' in '#theme_wrappers' instead
  // instead of using '#theme', it makes me very angry because I can't get
  // back my blocks in the template and dispose of them as I want...
  // The '#theme_wrappers' callbacks are run after rendering children, while
  // the '#theme' callback is run before rendering children, allowing me to
  // proceed with render() calls in my region template.
  $regions = system_region_list($GLOBALS['theme']);
  foreach (array_keys($regions) as $region) {
    unset($page[$region]['#theme_wrappers']);
    if (!empty($page[$region])) {
      $page[$region]['#theme'] = 'region';
    }
  }
}

/**
 * Implements hook_preprocess_HOOK().
 */
function vetbase_preprocess_block(&$variables) {
  // Add some region/page specifics.
  $region = $variables['block']->region;
  if (($node = menu_get_object()) && !arg(2)) {
    $variables['theme_hook_suggestions'][] = 'block__' . $region . '__' . $node->type;
  }
}

/**
 * Implements hook_preprocess_HOOK().
 */
function vetbase_preprocess_region(&$variables) {

  switch ($variables['region']) {

    case 'header2':
      $variables['attributes_array']['class'][] = 'col-md-12';
      break;

    case 'front1':
    case 'front2':
    case 'front3':
    case 'front4':
      $variables['attributes_array']['class'][] = 'row';
      break;
  }

  unset($variables['theme_hook_suggestions']);
  $variables['theme_hook_suggestions'][] = 'region';
  $variables['theme_hook_suggestions'][] = 'region__' . $variables['region'];
  if (($node = menu_get_object()) && !arg(2)) {
    $variables['theme_hook_suggestions'][] = 'region__' . $variables['region'] . '__' . $node->type;
  }

  // Now that we are a #theme and not a #theme_wrapper, move all renderable
  // children into the content array instead of leaving them as-is. Per default
  // the 'render element' for the 'region' theme hook is 'elements'.
  foreach (element_children($variables['elements']) as $key) {
    $variables['content'][$key] = &$variables['elements'][$key];
    $variables['content'][$key]['#sorted'] = true;
  }
}

/**
 * Implements hook_preprorcess_HOOK().
 */
function vetbase_preprocess_node(&$variables) {
  $node = $variables['node'];
  $view_mode = $variables['view_mode'];

  if ($variables['page']) {
    _vatbase_add_menus($variables);
  }

  if (in_array($view_mode, ['front1', 'front2', 'front3', 'front4'])) {
    switch ($node->type) {

      case 'list': // List will expand 100%
        break;

      case 'contact':
        $variables['classes_array'][] = 'col-md-8';
        break;

      default: // Default is to be 3rd or 4th of div width
        $variables['classes_array'][] = 'col-md-4';
        break;
    }
  }

  switch ($node->type) {

    case 'page':
    case 'news':
      $variables['display_authoring'] = ('full' === $view_mode);
      break;

    default:
      $variables['display_authoring'] = false;
  }

  // @todo I would have preferred to do this in the template directly
  if ($variables['display_authoring']) {
    if ($items = field_get_items('node', $node, 'content_author')) {
      $variables['content_author'] = check_plain($items[0]['value']);
    } else {
      $variables['content_author'] = format_username(user_load($node->uid));
    }
    if ($items = field_get_items('node', $node, 'content_date')) {
      $variables['content_date'] = $items[0]['date'];
    } else {
      $variables['content_date'] = $node->changed;
    }
  }

  // Add smart template suggestions.
  $variables['theme_hook_suggestions'][] = 'node__' . $view_mode;
  $variables['theme_hook_suggestions'][] = 'node__' . $node->type . '__' . $view_mode;
}

/**
 * Implements hook_preprocess_HOOK().
 */
function vetbase_preprocess_field(&$variables, $hook) {
  $element = $variables['element'];

  // Add some other smart suggestions.
  $variables['theme_hook_suggestions'] = array(
    'field__' . $element['#field_type'],
    'field__' . $element['#field_name'],
    'field__' . $element['#bundle'],
    'field__' . $element['#field_name'] . '__' . $element['#bundle'],
  );
}

/**
 * Overrides theme_menu_local_task().
 */
function vetbase_menu_local_task($variables) {
  $link = $variables['element']['#link'];
  $link_text = $link['title'];
  if (!empty($variables['element']['#active'])) {
    $active = '<span class="sr-only">' . t('(active tab)') . '</span>';
    if (empty($link['localized_options']['html'])) {
      $link['title'] = check_plain($link['title']);
    }
    $link['localized_options']['html'] = TRUE;
    $link_text = t('!local-task-title!active', ['!local-task-title' => $link['title'], '!active' => $active]);
  }
  return '<li role="presentation"' . (!empty($variables['element']['#active']) ? ' class="active"' : '') . '>' . l($link_text, $link['href'], $link['localized_options']) . "</li>\n";
}

/**
 * Overrides theme_menu_local_tasks().
 */
function vetbase_menu_local_tasks(&$variables) {
  $output = '';
  if (!empty($variables['primary'])) {
    $variables['primary']['#prefix'] = '<ul class="nav nav-tabs">';
    $variables['primary']['#suffix'] = '</ul>';
    $output .= drupal_render($variables['primary']);
  }
  if (!empty($variables['secondary'])) {
    $variables['secondary']['#prefix'] = '<ul class="nav nav-tabs">';
    $variables['secondary']['#suffix'] = '</ul>';
    $output .= drupal_render($variables['secondary']);
  }
  return $output;
}

/**
 * Overrides theme_menu_local_action().
 */
function vetbase_menu_local_action($variables) {
  $link = $variables['element']['#link'];
  // $output = '<li role="presentation">';
  if (!empty($link['localized_options']['html'])) {
    $title = '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> ' . $link['title'];
  } else {
    $title = '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> ' . check_plain($link['title']);
  }
  $link['localized_options']['html'] = true;
  if (isset($link['href'])) {
    $link['localized_options']['attributes']['class'][] = 'btn';
    $link['localized_options']['attributes']['class'][] = 'btn-success';
    $output = l($title, $link['href'], $link['localized_options'] + $link);
  } else {
    $output = $title;
  }
  // $output .= "</li>";
  return $output;
}

/**
 * Overrides theme_breadcrumb().
 */
function vetbase_breadcrumb($variables) {
  $breadcrumb = $variables['breadcrumb'];
  if (!empty($breadcrumb)) {
    $output = '<h2 class="sr-only">' . t('You are here') . '</h2>';
    if (!empty($breadcrumb)) {
      $links = '<li>' . implode('</li><li>', $breadcrumb) . '</li>';
    } else {
      $links = '';
    }

    if (($item = menu_get_item()) && ($item['type'] == MENU_LOCAL_TASK)) {
      _menu_translate($item, $item['original_map']);
      $current = $item['title'];
    } else {
      $current = drupal_get_title();
    }

    $output .= '<ol class="breadcrumb">' . $links  . '<li class="active">' . $current . '</li></ol>';
    return $output;
  }
}

/**
 * Overrides theme_status_messages().
 */
function vetbase_status_messages($variables) {
  $display = $variables['display'];
  $output = '';
  foreach (drupal_get_messages($display) as $type => $messages) {
    switch ($type) {
      case 'error':
        $class = 'alert-danger alert-dismissible';
        $icon = '<span class="glyphicon glyphicon-exclamation-sign"></span> '; // Space is important.
        break;
      case 'warning':
        $class = 'alert-warning alert-dismissible';
        $icon = '<span class="glyphicon glyphicon-warning-sign"></span> '; // Space is important.
        break;
      default:
        $class = 'alert-success alert-dismissible';
        $icon = '<span class="glyphicon glyphicon-ok"></span> '; // Space is important.
        break;
    }
    foreach ($messages as $message) {
      $output .= '<div class="alert ' . $class . '" role="alert">' . $icon;
      $output .= $message;
      $output .= '<button type="button" class="close" data-dismiss="alert" aria-label="' . t("Close") . '"><span aria-hidden="true">&times;</span></button>';
      $output .= '</div>';
    }
  }
  return $output;
}

/**
 * Overrides theme_admin_block().
 */
function vetbase_admin_block($variables) {
  $block = $variables['block'];
  $output = '';
  if (empty($block['show'])) {
    return $output;
  }
  $output = '<div class="panel panel-default admin-block">';
  if (!empty($block['title'])) {
    $output .= '<div class="panel-heading"><h3 class="panel-title">' . $block['title'] . '</h3></div>';
  }
  $output .= '<div class="panel-body">';
  if (!empty($block['content'])) {
    $output .= $block['content'];
  }
  else {
    $output .= '<p class="description">' . $block['description'] . '</p>';
  }
  return $output . '</div></div>';
}

/**
 * Overrides theme_admin_block_content().
 */
function vetbase_admin_block_content($variables) {
  $content = $variables['content'];
  $output = '';
  if (!empty($content)) {
    $compact = system_admin_compact_mode();
    $output .= '<dl>';
    foreach ($content as $item) {
      $output .= '<dt>' . l($item['title'], $item['href'], $item['localized_options']) . '</dt>';
      if (!$compact && isset($item['description'])) {
        $output .= '<dd>' . filter_xss_admin($item['description']) . '</dd>';
      }
    }
    $output .= '</dl>';
  }
  return $output;
}

/**
 * Overrides theme_admin_page().
 */
function vetbase_admin_page($variables) {
  $blocks = $variables['blocks'];

  $stripe = 0;
  $output = '';
  $container = [];

  foreach ($blocks as $block) {
    if ($block_output = theme('admin_block', ['block' => $block])) {
      if (empty($block['position'])) {
        // perform automatic striping.
        $block['position'] = ++$stripe % 2 ? 'left' : 'right';
      }
      if (!isset($container[$block['position']])) {
        $container[$block['position']] = '';
      }
      $container[$block['position']] .= $block_output;
    }
  }

  //$output .= theme('system_compact_link');
  foreach ($container as $data) {
    $output .= '<div class="col-md-6">';
    $output .= $data;
    $output .= '</div>';
  }

  return '<div class="row">' . $output . '</div>';
}

/**
 * Overrides theme_system_admin_index().
 */
function vetbase_system_admin_index($variables) {
  $menu_items = $variables['menu_items'];

  $container = ['left' => '', 'right' => ''];
  $flip = ['left' => 'right', 'right' => 'left'];
  $position = 'left';

  // Iterate over all modules.
  foreach ($menu_items as $module => $block) {
    list($description, $items) = $block;

    // Output links.
    if (count($items)) {
      $block = [
        'title'       => $module,
        'content'     => theme('admin_block_content', ['content' => $items]),
        'description' => t($description),
        'show'        => true,
      ];
      if ($block_output = theme('admin_block', ['block' => $block])) {
        if (!isset($block['position'])) {
          // Perform automatic striping.
          $block['position'] = $position;
          $position = $flip[$position];
        }
        $container[$block['position']] .= $block_output;
      }
    }
  }

  $output = theme('system_compact_link');
  foreach ($container as $id => $data) {
    $output .= '<div class="' . $id . ' clearfix">';
    $output .= $data;
    $output .= '</div>';
  }

  return $output;
}

/**
 * Overrides theme_status_report().
 */
function vetbase_status_report($variables) {
  $requirements = $variables['requirements'];
  $severities = [
    REQUIREMENT_INFO => [
      'title' => t('Info'),
      'class' => 'info',
    ],
    REQUIREMENT_OK => [
      'title' => t('OK'),
      'class' => 'success',
    ],
    REQUIREMENT_WARNING => [
      'title' => t('Warning'),
      'class' => 'warning',
    ],
    REQUIREMENT_ERROR => [
      'title' => t('Error'),
      'class' => 'danger',
    ],
  ];
  $output = '<table class="table table-condensed">';

  foreach ($requirements as $requirement) {
    if (empty($requirement['#type'])) {
      $severity = $severities[isset($requirement['severity']) ? (int) $requirement['severity'] : REQUIREMENT_OK];
      // Output table row(s)
      if (!empty($requirement['description'])) {
        $output .= '<tr class="' . $severity['class'] . ' merge-down"><td class="status-title">' . $requirement['title'] . '</td><td class="status-value">' . $requirement['value'] . '</td></tr>';
        $output .= '<tr class="' . $severity['class'] . ' merge-up"><td colspan="2" class="status-description">' . $requirement['description'] . '</td></tr>';
      }
      else {
        $output .= '<tr class="' . $severity['class'] . '"><td class="status-title">' . $requirement['title'] . '</td><td class="status-value">' . $requirement['value'] . '</td></tr>';
      }
    }
  }

  $output .= '</table>';
  return $output;
}

/**
 * Overrides theme_menu_link__backoffice().
 */
function vetbase_menu_link__backoffice($variables) {
  $element = $variables['element'];
  if ($element['#below']) {
    return '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">' . filter_xss($element['#title']) . ' <span class="caret"></span></a>' . drupal_render($element['#below']) . '</li>';
  } else {
    return '<li>' . l($element['#title'], $element['#href'], $element['#localized_options']) . '</li>';
  }
}

/**
 * Overrides theme_links().
 */
function vetbase_links($variables) {
  $links = $variables['links'];
  $heading = $variables['heading'];
  global $language_url;
  $output = '';

  if (count($links) > 0) {
    // Treat the heading first if it is present to prepend it to the
    // list of links.
    if (!empty($heading)) {
      if (is_string($heading)) {
        // Prepare the array that will be used when the passed heading
        // is a string.
        $heading = array(
          'text' => $heading,
          // Set the default level of the heading.
          'level' => 'h2',
        );
      }
      $output .= '<' . $heading['level'];
      if (!empty($heading['class'])) {
        $output .= drupal_attributes(array('class' => $heading['class']));
      }
      $output .= '>' . check_plain($heading['text']) . '</' . $heading['level'] . '>';
    }

    $variables['attributes']['class'][] = "btn-group";
    $output .= '<div' . drupal_attributes($variables['attributes']) . '>';

    foreach ($links as $key => $link) {
      $link['attributes']['class'][] = $key;
      $link['attributes']['class'][] = 'btn';
      $link['attributes']['class'][] = 'btn-default';

      if (isset($link['href']) && ($link['href'] == $_GET['q'] || ($link['href'] == '<front>' && drupal_is_front_page()))
          && (empty($link['language']) || $link['language']->language == $language_url->language))
        {
          $link['attributes']['class'][] = 'active';
        }

        if (isset($link['href'])) {
          // Pass in $link as $options, they share the same keys.
          $output .= l($link['title'], $link['href'], $link);
        }
        elseif (!empty($link['title'])) {
          // Some links are actually not links, but we wrap these in <span> for adding title and class attributes.
          if (empty($link['html'])) {
            $link['title'] = check_plain($link['title']);
          }
          $span_attributes = '';
          if (isset($link['attributes'])) {
            $span_attributes = drupal_attributes($link['attributes']);
          }
          $output .= '<span' . $span_attributes . '>' . $link['title'] . '</span>';
        }
    }

    $output .= '</div>';
  }

  return $output;
}

/**
 * Implements hook_preprocess_HOOK().
 */
function vetbase_preprocess_table(&$vars) {

  // Make it bootstrap yeah!
  $vars['attributes']['class'][] = 'table';
  if (empty($vars['attributes']['no_strip'])) {
    $vars['attributes']['class'][] = 'table-striped';
  }
  $vars['attributes']['class'][] = 'table-condensed';

  // Count header for later, better here than over there.
  $header_count = 0;
  if (!empty($vars['header'])) {
    foreach ($vars['header'] as &$header_cell) {
      if (is_array($header_cell)) {
        $header_count += isset($header_cell['colspan']) ? $header_cell['colspan'] : 1;
      } else {
        $header_count++;
      }
    }
  }

  // Need this in template.
  $vars['header_count'] = $header_count;

  if ($header_count) {
    $vars['sortheader'] = tablesort_init($vars['header']);
  } else {
    $vars['sortheader'] = [];
  }

  if (!empty($vars['header'])) {
    foreach ($vars['header'] as &$header_cell) {
      $header_cell = tablesort_header($header_cell, $vars['header'], $vars['sortheader']);
    }
  }
  if (!empty($vars['rows'])) {
    foreach ($vars['rows'] as &$row) {
      $i = 0;
      foreach ($row as &$cell) {
        $cell = tablesort_cell($cell, $vars['header'], $vars['sortheader'], $i);
        $i++;
      }
    }
  }

  // Add the 'empty' row message if available.
  if ($vars['empty'] && empty($vars['rows'])) {
    $vars['rows'][] = [['data' => $vars['empty'], 'colspan' => $header_count, 'class' => ['empty', 'message']]];
  }

  // Manage main attributes
  if (isset($vars['colgroups'])) {
    _vetbase_extract_data_from_attributes($vars, 'colgroups');
  }
  if (isset($vars['header'])) {
    _vetbase_extract_data_from_attributes($vars, 'header');
  }
  if (isset($vars['rows'])) {
    _vetbase_extract_data_from_attributes($vars, 'rows');
  }

  // Manage cells attributes, because they are deeper.
  $vars['cells_attributes'] = [];
  foreach ($vars['rows'] as $row_index => &$row) {
    if (is_array($row)) {
      foreach ($row as $cell_index => &$cell) {
        $vars['cells_attributes'][$row_index][$cell_index] = [];
        if (isset($cell['data']) || (is_array($cell) && count(element_properties($cell)) == 0)) {
          $temp_real_value = '';
          foreach ($cell as $key => $value) {
            if ($key == 'data') {
              $temp_real_value = $value;
            } else {
              $vars['cells_attributes'][$row_index][$cell_index][$key] = $value;
            }
          }
          $cell = $temp_real_value;
        }
      }
    }
  }

  $vars['theme_hook_suggestions'][] = 'table';
}

/**
 * Extract 'data' key and create another variable with other attributes.
 *
 * @param $vars
 * @param $name
 */
function _vetbase_extract_data_from_attributes(&$vars, $name) {
  $vars[$name . '_attributes'] = [];
  foreach ($vars[$name] as $index => &$item) {
    $vars[$name . '_attributes'][$index] = [];
    if (is_array($item) && (isset($item['data']) || ($name == 'header' && count(element_properties($item)) == 0))) {
      $temp_real_value = '';
      foreach ($item as $key => $value) {
        if ($key === 'data') {
          $temp_real_value = $value;
        }
        else {
          $vars[$name . '_attributes'][$index][$key] = $value;
        }
      }
      $item = $temp_real_value;
    }
  }
}

/**
 * Overrides theme_nice_menus_build().
 */
function vetbase_nice_menus_build($variables) {
  $menu = $variables['menu'];
  $depth = $variables['depth'];
  $trail = $variables['trail'];
  $output = '';
  // Prepare to count the links so we can mark first, last, odd and even.
  $index = 0;
  $count = 0;
  foreach ($menu as $menu_count) {
    if ($menu_count['link']['hidden'] == 0) {
      $count++;
    }
  }
  // Get to building the menu.
  foreach ($menu as $menu_item) {
    $mlid = $menu_item['link']['mlid'];
    // Check to see if it is a visible menu item.
    if (!isset($menu_item['link']['hidden']) || $menu_item['link']['hidden'] == 0) {
      // Check our count and build first, last, odd/even classes.
      $index++;
      $first_class = $index == 1 ? ' first ' : '';
      $oddeven_class = $index % 2 == 0 ? ' even ' : ' odd ';
      $last_class = $index == $count ? ' last ' : '';
      // Build class name based on menu path
      // e.g. to give each menu item individual style.
      // Strip funny symbols.
      $clean_path = str_replace(array('http://', 'www', '<', '>', '&', '=', '?', ':', '.'), '', $menu_item['link']['href']);
      // Convert slashes to dashes.
      $clean_path = str_replace('/', '-', $clean_path);
      $class = 'menu-path-' . $clean_path;
      if ($trail && in_array($mlid, $trail)) {
        $class .= ' active-trail';
      }
      // If it has children build a nice little tree under it.
      if ((!empty($menu_item['link']['has_children'])) && (!empty($menu_item['below'])) && $depth != 0) {
        // Keep passing children into the function 'til we get them all.
        if ($menu_item['link']['depth'] <= $depth || $depth == -1) {
          $children = array(
            '#theme' => 'nice_menus_build',
            '#prefix' => '<ul>',
            '#suffix' => '</ul>',
            '#menu' => $menu_item['below'],
            '#depth' => $depth,
            '#trail' => $trail,
          );
        }
        else {
          $children = '';
        }
        // Set the class to parent only of children are displayed.
        $parent_class = ($children && ($menu_item['link']['depth'] <= $depth || $depth == -1)) ? 'menuparent ' : '';
         $element = array(
          '#below' => $children,
          '#title' => $menu_item['link']['link_title'],
          '#href' =>  $menu_item['link']['href'],
          '#localized_options' => $menu_item['link']['localized_options'],
          '#attributes' => array(
            'class' => array('menu-' . $mlid, $parent_class, $class, $first_class, $oddeven_class, $last_class),
          ),
        );
        $variables['element'] = $element;
        $output .= theme('menu_link', $variables);
      }
      else {
        $element = array(
          '#below' => '',
          '#title' => $menu_item['link']['link_title'],
          '#href' =>  $menu_item['link']['href'],
          '#localized_options' => $menu_item['link']['localized_options'],
          '#attributes' => array(
            'class' => array('menu-' . $mlid, $class, $first_class, $oddeven_class, $last_class),
          ),
        );
        $variables['element'] = $element;
        $output .= theme('menu_link', $variables);
      }
    }
  }
  return $output;
}

/**
 * Drupal is so stupid, JS progress bars cannot be themed...
 */
function vetbase_progress_bar($variables) {
  $percent = $variables['percent'];
  return <<<EOT
<div class="progress">
  <div class="progress-bar" role="progressbar" aria-valuenow="{$percent}" aria-valuemin="0" aria-valuemax="100" style="width: {$percent}%;">
    {$percent}%
  </div>
</div>
EOT;
/*
    <<<EOT
<div id="progress" class="progress">
  <div class="bar">
    <div class="filled" style="width: {$variables['percent']}%"></div>
  </div>
  <div class="percentage">{$variables['percent']}%</div>
  <div class="message">{$variables['message']}</div>
</div>
EOT;
*/
}

/**
 * Overrides theme_image().
 *
 * Why the F*** would they not add classes to images??
 *
 * @param $variables
 * @return string
 */
function vetbase_image($variables) {
  $attributes = $variables['attributes'];
  $attributes['src'] = file_create_url($variables['path']);

  foreach (array('width', 'height', 'alt', 'title', 'class') as $key) {

    if (isset($variables[$key])) {
      $attributes[$key] = $variables[$key];
    }
  }

  return '<img' . drupal_attributes($attributes) . ' />';
}

/**
 * Implements hook_preprocess_HOOK().
 */
function vetbase_preprocess_item_list(&$vars) {
  if (empty($vars['items']) && $vars['empty_list']) {
    // Force class
    if (!isset($vars['attributes']['class']) || array_search('list-unstyled', $vars['attributes']['class']) === FALSE) {
      $vars['attributes']['class'][] = 'list-unstyled';
    }
    $vars['items'][] = $vars['empty_list'];
  }
}
