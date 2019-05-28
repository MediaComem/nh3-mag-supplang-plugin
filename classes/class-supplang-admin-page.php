<?php

if ( ! class_exists( 'Supplang_Admin_Page' ) ) {

  /**
   * This class manages the settings admin page of this plugin.
   * This means registering the custom option, and setting up the custom setting page.
   */
	class Supplang_Admin_Page {

		const SECTION_NAME  = 'supplang_uil_list';
		const TEMPLATES_DIR = '../admin/templates';

		public $option_values;
		public $languages;

		public function __construct() {
			add_action( 'admin_menu', array( $this, 'register_admin_page' ) );
			add_action( 'admin_init', array( $this, 'register_admin_content' ) );
			$option = get_option( SUPPLANG_AVAILABLE_UIL );
			// Test if setting exists
			$this->option_values = $option ? $option : array();
			$this->languages     = supplang_registered_languages();
		}

    /**
     * Registers the settings page component and custom option
     */
		public function register_admin_content() {
			register_setting( SUPPLANG_OPTION_GROUP, SUPPLANG_AVAILABLE_UIL );
			$this->register_setting_section();
			$this->register_setting_field();
		}

    /**
     * Registers the admin setting page
     */
		public function register_admin_page() {
			add_options_page(
				__( 'Site Languages Settings', 'supplang' ),
				__( 'Site Languages', 'supplang' ),
				'manage_options',
				SUPPLANG_ADMIN_PAGE_NAME,
				$this->load_template( 'settings-page' )
			);
		}

    /**
     * Registers the admin setting section, that will be included in the admin setting page
     */
		public function register_setting_section() {
			add_settings_section(
				self::SECTION_NAME,
				__( 'User Interface Languages', 'supplang' ),
				$this->load_template( 'settings-section' ),
				SUPPLANG_ADMIN_PAGE_NAME
			);
		}

    /**
     * Registers the settings field, that is the list of available languages and their respective checkbox
     */
		public function register_setting_field() {
			add_settings_field(
				'supplang_available_languages',
				__( 'Users can display the site in...', 'supplang' ),
				$this->load_template( 'settings-languages-fieldset' ),
				SUPPLANG_ADMIN_PAGE_NAME,
				self::SECTION_NAME,
				array(
					'class'     => 'supplang_uil_row',
					'languages' => $this->languages,
				)
			);
		}

    /**
     * Factory function that returns a closure to load the appropriate template.
     * The given $template_name should be the name of the tempate file to load, without the `.php` extension.
     * This file MUST exists in the ./admin/templates/ directory.
     * @param string $template_name The name of the template.
     * @return function A function that when called, will load the template
     */
		public function load_template( $template_name ) {
			$option_values = $this->option_values;
			// N.B.: The $args param of the anonymous function is used in the templates
			// and therefor must be declared in the function's param.
			return function( $args ) use ( $template_name, $option_values ) {
				include __DIR__ . "/../admin/templates/$template_name.php";
			};
    }

  }

}
