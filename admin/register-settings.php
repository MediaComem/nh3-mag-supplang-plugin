<?php

if ( ! function_exists( 'supplang_settings_init' ) ) {

	/**
	 * Register the settings page on the admin panel.
	 */
	function supplang_settings_init() {
		register_setting(
			SL_OPTION_GROUP,
			'supplang_uil_list',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'supplang_uil_list_sanitize',
			)
		);

		add_settings_section(
			'supplang_ui_languages',
			'User Interface Languages',
			'supplang_ui_languages_cb',
			SL_OPTION_GROUP
		);

		add_settings_field(
			'supplang_uil_list',
			__( 'Languages', 'supplang' ),
			'supplang_uil_list_field_cb',
			SL_OPTION_GROUP,
			'supplang_ui_languages',
			array(
				'label_for' => 'supplang_uil_list',
				'class'     => 'supplang_row',
			)
		);
	}
}

add_action( 'admin_init', 'supplang_settings_init' );

/**
 * Sanitize the user-input for the list of available languages by removing all whitespace.
 */
function supplang_uil_list_sanitize( $input ) {
	return str_replace( ' ', '', $input );
}

/**
 * Renders the HTML for the UI Languages section.
 */
function supplang_ui_languages_cb( $args ) {
	include 'templates/sections/user-interface-languages.php';
}

/**
 * Renders the HTML for the UI Language field.
 */
function supplang_uil_list_field_cb( $args ) {
	$options = get_option( 'supplang_uil_list' );

	include 'templates/fields/ui-available-languages.php';
}

if ( ! function_exists( 'supplang_settings_page_cb' ) ) {
	/**
	 * Renders the HTML for the plugin settings page.
	 */
	function supplang_settings_page_cb() {
		include 'templates/settings-page.php';
	}
}

if ( ! function_exists( 'supplang_options_page' ) ) {
	/**
	 * Defines that the plugin's setting page will be located under the Top-Level menu Settings
	 */
	function supplang_options_page() {
		add_options_page(
			__( 'Site Languages', 'supplang' ),
			__( 'Site Languages', 'supplang' ),
			'manage_options',
			SL_OPTION_GROUP,
			'supplang_settings_page_cb'
		);
	}
}

add_action( 'admin_menu', 'supplang_options_page' );
