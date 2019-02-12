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
define( SL_OPTION_GROUP, 'supplang' ); // Option group name for plugin settings
define( SL_UIL_NAME, 'supplang-uil' ); // Name used for managing the selected ui languages (POST param and cookie)

/**
 * --- LOAD PLUGIN FILES ---
 */

// Load the admin setting page
if ( is_admin() ) require_once 'admin/register-settings.php';

// Load the define locale mechanism
require_once 'includes/define-locale.php';

// Load the frontend api
if ( !is_admin() ) require_once 'frontend/api.php';

// TODO synchronize the list of available languages with the defined language taxonomies ?