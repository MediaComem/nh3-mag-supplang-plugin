<?php

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

    $localeWhitelist = explode(',', get_option( 'supplang_uil_list' ) );

    $localePost = in_array( $_POST[SL_UIL_NAME], $localeWhitelist ) ? $_POST[SL_UIL_NAME] : null;
    $localeCookie = in_array( $_COOKIE[SL_UIL_NAME], $localeWhitelist ) ? $_COOKIE[SL_UIL_NAME] : null;

    $locale = $localePost ? $localePost : $localeCookie;

    if ( $locale && ( !$localeCookie || $locale !== $localeCookie ) ) {
      setcookie( SL_UIL_NAME, $locale, time() + DAY_IN_SECONDS * 30, COOKIEPATH, COOKIE_DOMAIN );
      // We need to force set the cookie in the $_COOKIE array because the hook that triggers this function
      // is called executed several time when WordPress constructs the page. This is to prevent sending the same
      // cookie multiple time.
      $_COOKIE[SL_UIL_NAME] = $locale;
    }

    return $locale;
  }
}

add_filter( 'locale', 'supplang_define_locale' );
