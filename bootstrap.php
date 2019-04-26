<?php
/**
 * This file is where you should put the code that bootstraps your plugin
 */

/**
 * --- DEFINE PLUGIN CONSTANTS ---
 */
// Plugin name
define( 'SUPPLANG_NAME', 'supplang' );
// ID for the custom taxonomy
define( 'SUPPLANG_LANG_TAX_ID', 'supplang_lang' );
// Prefix that should be used by all plugin classes
define( 'SUPPLANG_CLASS_PREFIX', 'Supplang' );
// Name of the Supplang option group
define( 'SUPPLANG_OPTION_GROUP', 'supplang' );
// Name of the admin settings page
define( 'SUPPLANG_ADMIN_PAGE_NAME', 'supplang_admin_settings' );
// Name of the setting that list available languages for the site UI
define( 'SUPPLANG_AVAILABLE_UIL', 'supplang_available_uil' );
// Name of the GET param for changing the UI language
define( 'SUPPLANG_GET_PARAM', 'uil' );
// List of available languages
// Add a new array to add a new language
define(
	'SUPPLANG_LANGUAGES', array(
		array(
			'name'   => 'Français',
			'locale' => 'fr_FR',
			'desc'   => 'Apply this to french written articles',
			'slug'   => 'fr',
		),
		array(
			'name'   => 'Italiano',
			'locale' => 'it_IT',
			'desc'   => 'Apply this to italian written articles',
			'slug'   => 'it',
		),
		array(
			'name'   => 'Deutsch',
			'locale' => 'de_DE',
			'desc'   => 'Apply this to german written articles',
			'slug'   => 'de',
		),
		array(
			'name'   => 'Rumansch',
			'locale' => 'rm_CH',
			'desc'   => 'Apply this to romansch written articles',
			'slug'   => 'rm',
		),
		array(
			'name'   => 'English',
			'locale' => 'en_GB',
			'desc'   => 'Apply this to english written articles',
			'slug'   => 'en',
		),
	)
);

/**
 * --- REGISTER AUTOLOADER ---
 */
spl_autoload_register(
	function( $class_name ) {
		$class_name_parts = explode( '_', $class_name );
		if ( SUPPLANG_CLASS_PREFIX === $class_name_parts[0] ) {
			$classes_dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR;
			$class_file  = strtolower( 'class-' . implode( '-', $class_name_parts ) . '.php' );
			require_once $classes_dir . $class_file;
		}
	}
);

$supplang_lang_tax = new Supplang_Language_Taxonomy();

/**
 * --- LOAD ACTIVATION/DEACTIVATION HOOKS ---
 */

register_activation_hook( SUPPLANG_MAIN_FILE, array( $supplang_lang_tax, 'activate' ) );
register_deactivation_hook( SUPPLANG_MAIN_FILE, array( $supplang_lang_tax, 'deactivate' ) );

/**
 * --- REGISTER CUSTOM TAXONOMY ---
 */
add_action( 'init', array( $supplang_lang_tax, 'register_taxonomy' ) );
add_action( 'restrict_manage_posts', array( $supplang_lang_tax, 'add_admin_filter_dropdown' ) );
add_filter( 'parse_query', array( $supplang_lang_tax, 'admin_filter_posts' ) );

/**
 * --- LOAD PLUGIN FILES ---
 */

if ( is_admin() ) {
	// Load the admin features
	new Supplang_Admin_Page();
} else {
	// Load the frontend features
	require_once 'frontend/api.php';
	new Supplang_Link_Manager();
	new Supplang_Locale_Manager();
	new Supplang_Api();
}
