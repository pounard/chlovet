<?php
/**
 * A few custom formatters for text.
 */

/**
 * Implements hook_field_formatter_info().
 */
function chlovet_field_formatter_info() {
  return [
    'chlovet_text_summary' => [
      'label'       => t("Better (but uncached, sorry) text summary"),
      'field types' => ['text', 'text_long', 'text_with_summary'],
      'settings'    => ['trim_length' => 600],
    ],
  ];
}

/**
 * Implements hook_field_formatter_view().
 */
function chlovet_field_formatter_view($entity_type, $entity, $field, $instance, $langcode, $items, $display) {
  $element = [];
  $settings = $display['settings'];

  switch ($display['type']) {

    case 'chlovet_text_summary':
      foreach ($items as $delta => $item) {
        $output = check_markup($item['value'], 'partial_html', $langcode);
        $output = text_summary($output, 'partial_html', $settings['trim_length']);
        $element[$delta]['#markup'] = $output;
      }
      break;
  }

  return $element;
}
