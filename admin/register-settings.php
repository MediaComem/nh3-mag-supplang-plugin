<?php

define(OPTION_GROUP, 'nh3_nls');

if ( !function_exists('nh3_nls_settings_init') ) {
  /**
   * Register the settings page on the admin panel.
   */
  function nh3_nls_settings_init() {
    register_setting( OPTION_GROUP, 'nh3_nls_settings' );

    add_settings_section(
      'nh3_nls_ui_languages',
      'User Interface Languages',
      'nh3_nls_ui_languages_cb',
      OPTION_GROUP
    );

    add_settings_field(
      'ui_languages_field',
      __( 'Languages', 'nh3-nls' ),
      'nh3_nls_ui_languages_field_cb',
      OPTION_GROUP,
      'nh3_nls_ui_languages',
      [
        'label_for' => 'ui_languages_field',
        'class' => 'nh3_nls_row'
      ]
    );
  }
}

add_action( 'admin_init', 'nh3_nls_settings_init' );

/**
 * Renders the HTML for the UI Languages section.
 */
function nh3_nls_ui_languages_cb( $args ) {
  require_once 'templates/sections/user-interface-languages.php';
}

/**
 * Renders the HTML for the UI Language field.
 */
function nh3_nls_ui_languages_field_cb( $args ) {
  $options = get_option( 'nh3_nls_settings' );

  require_once 'templates/fields/ui-available-languages.php';
}

if ( !function_exists('nh3_nls_options_page_cb') ) {
  /**
   * Renders the HTML for the plugin settings page.
   */
  function nh3_nls_options_page_cb() {
    require_once 'templates/settings-page.php';
  }
}

if ( !function_exists( 'nh3_nls_options_page' ) ) {
  /**
   * Defines that the plugin's setting page will be located under the Top-Level menu Settings
   */
  function nh3_nls_options_page() {
    add_options_page(
      __('Site Languages', 'nh3-nls'),
      __('Site Languages', 'nh3-nls'),
      'manage_options',
      OPTION_GROUP,
      'nh3_nls_options_page_cb'
    );
  }
}

add_action('admin_menu', 'nh3_nls_options_page');
