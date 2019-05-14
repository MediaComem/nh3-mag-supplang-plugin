<?php

if ( ! class_exists( 'Supplang_Locale_Manager' ) ) {

	class Supplang_Locale_Manager {

		const POST_PARAM  = 'supplang-uil';
		const COOKIE_NAME = 'supplang';

		public function __construct() {
			add_filter( 'locale', array( $this, 'supplang_define_locale' ) );
		}

		/**
		 * Defines the locale to use for the frontend UI.
		 * The locale to use is extracted from the the `uil` query parameter, if present, and compared to
		 * a whitelist of available languages, extracted from the plugin settings and defined in the admin panel.
		 * The locale is stored in the cookie for future reference, then returned.
     * If their is not GET param and no cookie, then the language is guessed based on the HTTP_ACCEPT_LANGUAGE header
     * If the language could not be guessed, it fall back to the default language, which is
		 * **Note**: The locale defined in the URL has always precedence over the one in the cookie, provided that
		 * it's an available locale.
		 *
		 * @return `null` if no locale could be found or it was not an available locale ; otherwise return the locale value.
		 */
		public function supplang_define_locale() {

			// Prevent PHP Warnings
			//phpcs:disable
			if ( ! isset( $_GET[ SUPPLANG_GET_PARAM ] ) ) {
				$_GET[ SUPPLANG_GET_PARAM ] = null;
			}
			if ( ! isset( $_COOKIE[ self::COOKIE_NAME ] ) ) {
				$_COOKIE[ self::COOKIE_NAME ] = null;
			}
			// phpcs:enable

			$locale_whitelist = array_map(
				function( $language ) {
					return $language['locale'];
				}, supplang_languages()
			);

			//phpcs:disable
			$locale_get    = supplang_locale_from_slug( $_GET[ SUPPLANG_GET_PARAM ] );
			// phpcs:enable
			$locale_get    = in_array( $locale_get, $locale_whitelist, true ) ? $locale_get : null;
      $locale_cookie = in_array( $_COOKIE[ self::COOKIE_NAME ], $locale_whitelist, true ) ? $_COOKIE[ self::COOKIE_NAME ] : null;

      // Try to guess the browser preferred language
      $locale_browser = filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE');
      if ( boolval( $locale_browser ) ) {
        // The idea is to try and get a locale from the first two letters of the first language in the header
        $locale_browser = supplang_locale_from_slug(substr(explode( ',', $locale_browser )[0], 0, 2 )) ;
        // and compare it to the activated languages for the site.
        $locale_browser = in_array( $locale_browser, $locale_whitelist, true) ? $locale_browser : null;
      }

      // Default language
      $locale = "fr_FR";
      // Define the locale based on the priority (default < browser < cookie < URL)
      if ( boolval( $locale_browser ) ) {
        $locale = $locale_browser;
      }
      if ( boolval( $locale_cookie ) ) {
        $locale = $locale_cookie;
      }
      if ( boolval ( $locale_get ) ) {
        $locale = $locale_get;
      }

			if ( $locale && ( ! $locale_cookie || $locale !== $locale_cookie ) ) {
				setcookie( self::COOKIE_NAME, $locale, time() + DAY_IN_SECONDS * 30, COOKIEPATH, COOKIE_DOMAIN );
				// Since this hook is executed several time during the process, manually setting the cookie in the $_COOKIE array
				// prevents sending it multiple time to the client.
				$_COOKIE[ self::COOKIE_NAME ] = $locale;
			}

			return $locale;
		}

	}

}
