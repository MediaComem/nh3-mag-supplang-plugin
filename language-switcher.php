<?php
/**
 * Plugin Name: NH3 - Language Switcher
 * Description: Add a language switcher to allow NH3 users to switch the frontend language dynamically.
 * Text Domain: nh3-nls
 */

require_once 'admin/register-settings.php';

// TODO synchronize the list of available languages with the defined language taxonomies ?

if ( !function_exists( 'nh3_nls_define_locale' ) ) {

  /**
   * Defines the locale to use for the frontend UI.
   * The locale to use is extracted from the the `nls` query parameter, if present, and compared to
   * a whitelist of available languages, extracted from the plugin settings and defined in the admin panel.
   * The locale is then stored in the cookie for future reference, then returned.
   * **Note**: The locale defined in the URL has always precedence over the one in the cookie, provided that
   * it's an available locale.
   *
   * @author Mathias Oberson
   * @return `null` if no locale could be found or it was not an available locale ; otherwise returne the locale value.
   */
  function nh3_nls_define_locale() {

    if ( is_admin() ) return;

    define( NLS_COOKIE_NAME, 'nh3-selected-language' );
    define( NLS_GET_PARAM, 'nls' );

    $localeWhitelist = explode(',', get_option( 'nh3_nls_settings' )['ui_languages_field'] );

    $localeUrl = in_array( $_GET[NLS_GET_PARAM], $localeWhitelist ) ? $_GET[NLS_GET_PARAM] : null;
    $localeCookie = in_array( $_COOKIE[NLS_COOKIE_NAME], $localeWhitelist ) ? $_COOKIE[NLS_COOKIE_NAME] : null;

    $locale = $localeUrl ? $localeUrl : $localeCookie;

    if ( $locale && ( !$localeCookie || $locale !== $localeCookie ) ) {
      setcookie( NLS_COOKIE_NAME, $locale, time() + DAY_IN_SECONDS * 30, COOKIEPATH, COOKIE_DOMAIN );
      // We need to force set the cookie in the $_COOKIE array because the hook that triggers this function
      // is called executed several time when WordPress constructs the page. This is to prevent sending the same
      // cookie multiple time.
      $_COOKIE[NLS_COOKIE_NAME] = $locale;
    }

    return $locale;
  }
}

add_filter( 'locale', 'nh3_nls_define_locale' );

// TODO create an utility function that display a language selector on the template ?
