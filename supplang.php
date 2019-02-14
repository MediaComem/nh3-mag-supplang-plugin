<?php
/**
 * Plugin Name: Supplang
 * Description: Flexible language manager that allows for a complete separation between UI language and content language.
 * Version:     0.1.0
 * Author:      Mathias Oberson - MEI
 * Author URI:  https://heig-vd.ch/rad/instituts/mei/
 * Text Domain: supplang
 * Domain Path: /languages
 */

/**
 * --- DEFINE PLUGIN CONSTANTS ---
 */
// Name of the POST param used for switching languages
define( SL_UIL_POST_PARAM, 'supplang-uil' );
// Name of the cookie used to save the user langauge choice
define( SL_UIL_COOKIE_NAME, 'supplang-uil' );
// ID for the custom taxonomy
define( SL_LANG_TAX_ID, 'supplang_lang' );
// Prefix that should be used by all plugin classes
define( SL_CLASS_PREFIX, 'Supplang');
// List of available languages
// Add a new array to add a new language
define(SL_LANGUAGES, [
  [
    "name" => "Français",
    "locale" => "fr_FR"
  ],
  [
    "name" => "Italiano",
    "locale" => "it_IT"
  ],
  [
    "name" => "Rumansch",
    "locale" => "rm_CH"
  ]
]);

/**
 * --- REGISTER AUTOLOADER ---
 */
spl_autoload_register( function( $class_name ) {
  $class_name_parts = explode( '_', $class_name);
  if ( $class_name_parts[0] === SL_CLASS_PREFIX ) {
    array_shift( $class_name_parts );
    $classes_dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR;
    $class_file = strtolower( implode( '-', $class_name_parts ) . '.class.php' );
    require_once $classes_dir . $class_file;
  }
} );

/**
 * --- LOAD ACTIVATION/DEACTIVATION HOOKS ---
 */

register_activation_hook( __FILE__, function() {
  $supplang_lang_tax = new Supplang_Language_Taxonomy();
  $supplang_lang_tax->activate();

  // Create table supplang_uil_lang
} );

/**
 * --- LOAD PLUGIN FILES ---
 */

// Register custom taxonomy
$supplang_lang_tax = new Supplang_Language_Taxonomy();
add_action( 'init', array( $supplang_lang_tax, 'register_taxonomy' ) );
add_action( 'restrict_manage_posts', array( $supplang_lang_tax, 'add_admin_filter_dropdown' ) );
add_filter( 'parse_query', array( $supplang_lang_tax, 'admin_filter_posts' ) );

// Load the admin setting page
// if ( is_admin() ) require_once 'admin/register-settings.php';

// Load the define locale mechanism
require_once 'includes/define-locale.php';

// Load the frontend api
if ( !is_admin() ) require_once 'frontend/api.php';
