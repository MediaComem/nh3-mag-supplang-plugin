<?php
/**
 * Renders an HTML select list that displays the available user interface languages.
 * Loads a script that allows the user to actually change the language.
 */
function supplang_switcher() {
  wp_enqueue_script( 'supplang-language-switcher', plugin_dir_url( __FILE__ ).'js/language-switcher.js', array('jquery'), null, true );
	include 'templates/language-selector.php';
}

/**
 * Get an array of available languages.
 * The returned languages are the one that have been checked using the plugin setting in **Settings > Site Languages** page.
 * Each item is an array with the following properties:
 * * `name` - The name of the language, in the language itself
 * * `locale` - The name of the WordPress locale for this language
 * @return Array
 */
function supplang_languages() {
  $languages = array(
    'filtered' => array(),
    // The option array uses the language locale as a key to indicate its availability
    'available' => get_option( SUPPLANG_AVAILABLE_UIL )
  );

  foreach (SUPPLANG_LANGUAGES as $lang) {
    if (array_key_exists( $lang['locale'], $languages['available'] ) ) {
      // Add filtered language
      array_push( $languages['filtered'], array_filter( $lang, function($key) {
        // Do not send the description key
        return $key !== 'description';
      }, ARRAY_FILTER_USE_KEY ) );
    }
  }

  return $languages['filtered'];
}

/**
 * Append the supplang user interface language query param to the home url.
 * @param String path A path that will be appended to the home url
 * @return String the resulting home url
 */
function supplang_home_url($path = '') {
  $home_url = home_url( $path );
  return $home_url . (strpos($home_url, '?') ? '&' : '?') . SUPPLANG_GET_PARAM . '=' . supplang_slug_from_locale();
}

/**
 * Get the supplang language slug corresponding to the currently set site locale.
 * **Note:** The locale will be searched for among the **available** languages as set in the **Settings > Site Languages** page.
 * @return String The language slug, composed of the first two characters of the defined locale.
 */
function supplang_slug_from_locale() {
  $slug_from_locale = array_column( supplang_languages(), 'slug', 'locale' );
  return array_key_exists(get_locale(), $slug_from_locale) ? $slug_from_locale[ get_locale() ] : 'de';
}

/**
 * Get the supplang language locale corresponding to the given $slug.
 * **Note:** The slug will be searched for among the **available** languages as set in the **Settings > Site Languages** page.
 * @param String $slug The language slug
 * @return Mixed The language locale for the given slug, or null if no corresponding locale found.
 */
function supplang_locale_from_slug($slug) {
  $locale_from_slug = array_column( supplang_languages(), 'locale', 'slug' );
  return array_key_exists($slug, $locale_from_slug) ? $locale_from_slug[ $slug ] : null;
}
