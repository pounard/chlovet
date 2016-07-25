<?php

/**
 * Implements hook_field_widget_info().
 */
function chlovet_field_formatter_info() {
  return [
    'chlovet_htmlawed' => [
      'label'       => t("Minimal, safe, purified, XSS-free, compliant HTML"),
      'field types' => ['text', 'text_long', 'text_with_summary'],
      'settings'    => [
        'config'  => [],
        'spec'    => '',
        'trim'    => 0,
      ],
    ],
  ];
}

/**
 * Implements hook_field_formatter_settings_form().
 */
function chlovet_field_formatter_settings_form($field, $instance, $view_mode, $form, &$form_state) {
  $element = [];

  $display  = $instance['display'][$view_mode];
  $settings = $display['settings'];

  switch ($display['type']) {

    case 'chlovet_htmlawed':
      $element['config'] = [
        '#type'           => 'textarea',
        '#rows'           => 3,
        '#title'          => t('Config.'),
        '#default_value'  => $settings['config'],
        '#description'    => 'Write something, or not',
        '#required'       => true,
      ];
      $element['spec'] = [
        '#type'           => 'textarea',
        '#rows'           => 3,
        '#title'          => t('Spec.'),
        '#default_value'  => $settings['spec'],
      ];
      break;
  }

  return $element;
}

/**
 * Implements hook_field_formatter_settings_summary().
 */
function chlovet_field_formatter_settings_summary($field, $instance, $view_mode) {
  return "&hellip;";
}

/**
 * Implements hook_field_formatter_view().
 */
function chlovet_field_formatter_view($entity_type, $entity, $field, $instance, $langcode, $items, $display) {
  $element = [];

  $settings = $display['settings'];

  switch ($display['type']) {

    case 'chlovet_htmlawed':

      $config = $settings['config'];
      $spec   = $settings['spec'];
      $trim   = (int)$settings['trim'];

      if (empty($config)) {
        $config = ['safe' => 1, 'keep_bad' => 0, 'elements' => 'a, em, strong, cite, blockquote, ol, ul, li, dl, dt, dd', 'deny_attribute' => 'id, style'];
      }

      foreach ($items as $delta => $item) {

        if (empty($item['value'])) {
          continue;
        }

        $text   = $item['value'];
        $format = $item['format'] ? $item['format'] : null;

        if ($format) {
          $text = check_markup($text, $format);
        }
        if ($trim) {
          $text = text_summary($text, $format, $trim);
        }

        $text = htmLawed($text, $config, $spec);
        $text = str_replace('<!--break -->', '<!--break-->', $text);

        $element[$delta]['#markup'] = $text;
      }

      break;
  }

  return $element;
}
