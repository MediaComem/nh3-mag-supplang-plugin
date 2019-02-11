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

require_once 'admin/register-settings.php';

// TODO synchronize the list of available languages with the defined language taxonomies ?

if ( !function_exists( 'supplang_define_locale' ) ) {

  /**
   * Defines the locale to use for the frontend UI.
   * The locale to use is extracted from the the `uil` query parameter, if present, and compared to
   * a whitelist of available languages, extracted from the plugin settings and defined in the admin panel.
   * The locale is stored in the cookie for future reference, then returned.
   * **Note**: The locale defined in the URL has always precedence over the one in the cookie, provided that
   * it's an available locale.
   *
   * @return `null` if no locale could be found or it was not an available locale ; otherwise returne the locale value.
   */
  function supplang_define_locale() {

    if ( is_admin() ) return;

    define( SUPPLANG_UIL_NAME, 'supplang-uil' );

    $localeWhitelist = explode(',', get_option( 'supplang_uil_list' ) );

    $localePost = in_array( $_POST[SUPPLANG_UIL_NAME], $localeWhitelist ) ? $_POST[SUPPLANG_UIL_NAME] : null;
    $localeCookie = in_array( $_COOKIE[SUPPLANG_UIL_NAME], $localeWhitelist ) ? $_COOKIE[SUPPLANG_UIL_NAME] : null;

    $locale = $localePost ? $localePost : $localeCookie;

    if ( $locale && ( !$localeCookie || $locale !== $localeCookie ) ) {
      setcookie( SUPPLANG_UIL_NAME, $locale, time() + DAY_IN_SECONDS * 30, COOKIEPATH, COOKIE_DOMAIN );
      // We need to force set the cookie in the $_COOKIE array because the hook that triggers this function
      // is called executed several time when WordPress constructs the page. This is to prevent sending the same
      // cookie multiple time.
      $_COOKIE[SUPPLANG_UIL_NAME] = $locale;
    }

    return $locale;
  }
}

add_filter( 'locale', 'supplang_define_locale' );

// TODO create an utility function that display a language selector on the template ?
require_once 'frontend/language-switcher.php';
