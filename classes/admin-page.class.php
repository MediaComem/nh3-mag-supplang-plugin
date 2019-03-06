<?php

if ( ! class_exists( 'Supplang_Admin_Page' ) ) {

  class Supplang_Admin_Page {

    const SECTION_NAME = 'supplang_uil_list';
    const TEMPLATES_DIR = '../admin/templates';

    public $option_values;
    public $languages;

    public function __construct() {
      add_action( 'admin_menu', array( $this, 'register_admin_page'));
      add_action( 'admin_init', array($this, 'register_admin_content'));
      $option = get_option( SUPPLANG_AVAILABLE_UIL );
      // Test if setting exists
      $this->option_values = $option ? $option : array();
      $this->languages = $this->extend_available_languages();
    }

    public function register_admin_content() {
      register_setting( SUPPLANG_OPTION_GROUP, SUPPLANG_AVAILABLE_UIL );
      $this->register_setting_section();
      $this->register_setting_field();
    }

    public function register_admin_page() {
      add_options_page(
        __('Site Languages Settings', 'supplang'),
        __('Site Languages', 'supplang'),
        'manage_options',
        SUPPLANG_ADMIN_PAGE_NAME,
        $this->load_template('settings-page')
      );
    }

    public function register_setting_section() {
      add_settings_section(
        self::SECTION_NAME,
        __('User Interface Languages', 'supplang'),
        $this->load_template('settings-section'),
        SUPPLANG_ADMIN_PAGE_NAME
      );
    }

    public function register_setting_field() {
      add_settings_field(
        'supplang_available_languages',
        __('Users can display the site in...', 'supplang'),
        $this->load_template('settings-languages-fieldset'),
        SUPPLANG_ADMIN_PAGE_NAME,
        self::SECTION_NAME,
        [
          'class' => 'supplang_uil_row',
          'languages' => $this->languages
        ]
      );
    }

    public function load_template( $template_name ) {
      $option_values = $this->option_values;
      // N.B.: The $args param of the anonymous function is ued in the templates
      // and therefor must be declared in the function's param.
      return function( $args ) use ( $template_name, $option_values ) {
        include __DIR__."/../admin/templates/$template_name.php";
      };
    }

    private function extend_available_languages() {
      return array_map(
        function( $language ) {
          // TODO check if po file exists
          // TODO if po file -> link to edit translation
          // TODO if no po file -> link to create po file ?
          $path = urlencode("themes/nh3-mag/languages/{$language['locale']}.po");
          $language['loco_link'] = get_site_url() . "/wp-admin/admin.php?path=$path&bundle=nh3-mag&domain=nh3-mag&page=loco-theme&action=file-edit";
          return $language;
        }, SUPPLANG_LANGUAGES
      );
    }

  }
}
