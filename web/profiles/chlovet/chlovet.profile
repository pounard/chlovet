<?php
/**
 * Placeholder file.
 */

/**
 * Implements hook_page_delivery_callback_alter().
 *
 * @see http://makina-corpus.com/blog/metier/2015/gerer-arborescence-menu-et-fil-ariane-drupal
 */
function chlovet_page_delivery_callback_alter(&$callback) {
  $current_path = current_path();
  // Only work on normal pages, not ajax nor admin pages.
  if ($callback != 'drupal_deliver_html_page' || path_is_admin($current_path)) {
    return;
  }
  $parent_path = NULL;

  // Do nothing if the node is actually in the menu tree
  $preferred_link = menu_link_get_preferred($current_path);
  if ($preferred_link['customized']) {
    return;
  }

  // Set the nearest parent given certain conditions.
  $siteManager = ucms_site_manager();
  $item = menu_get_item();
  if ($item['path'] == 'node/%' && $siteManager->getContext()) {
    /* WHAT
    // Get all list_type content for this site
    $query = db_select('node', 'n');
    $query->join('ucms_site_node', 'un', 'un.nid = n.nid'); // Specify this in case we are admin
    $query->innerJoin('field_data_list_type', 'lt', "lt.entity_type = 'node' AND n.nid = lt.entity_id");
    $lists = $query
      ->fields('lt', ['list_type_value'])
      ->fields('n', ['nid'])
      ->condition('n.status', NODE_PUBLISHED)
      ->condition('un.site_id', $siteManager->getContext()->getId())
      ->addTag('node_access')
      ->execute()
      ->fetchAllKeyed();

    $node = $item['map'][1];
    if (in_array($node->type, array_keys($lists))) {
      $parent_path = 'node/' . $lists[$node->type];
    }
     */
  }

  // If parent found, set it for menu tree trail future build.
  if ($parent_path) {
    $parent_preferred_link = menu_link_get_preferred($parent_path);
    menu_tree_set_path($parent_preferred_link['menu_name'], $parent_path);
    // Fool drupal so that it take the correct link to build the breadcrumb.
    $preferred_links = &drupal_static('menu_link_get_preferred');
    $preferred_links[$current_path] = $preferred_links[$parent_path];
  }
}
