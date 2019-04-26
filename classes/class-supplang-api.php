<?php
if ( ! class_exists( 'Supplang_Api' ) ) {

	/**
	 * Add language feature to API request on posts.
	 */
	class Supplang_Api {

		public function __construct() {
			add_action(
				'rest_api_init', function () {
					add_filter( 'rest_post_query', array( $this, 'add_lang_param_in_search_query' ), 10, 2 );
					add_filter( 'rest_post_dispatch', array( $this, 'add_translated_category_name_to_posts' ), 10, 3 );
				}
			);
		}

		/**
		 * Updates the REST post query to filter them by languages.
		 * This can be done by adding a `uil` param to the GET request, and setting it to one of the available language slug.
		 * An invalid or empty `uil` param will be discarded.
		 *
		 * @param Array $query_args The current query arguments
		 * @param WP_REST_Request $request The request object
		 * @return Array A potentially updated query args array
		 */
		public function add_lang_param_in_search_query( $query_args, $request ) {
			if ( ! empty( $request[ SUPPLANG_GET_PARAM ] ) && supplang_locale_from_slug( $request[ SUPPLANG_GET_PARAM ] ) !== null ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => SUPPLANG_LANG_TAX_ID,
					'field'    => 'slug',
					'terms'    => $request[ SUPPLANG_GET_PARAM ],
				);
			}
			return $query_args;
		}

		/**
		 * If the REST GET request has a valid `uil` param, then a new item `localized_category_name`
		 * is added to each of the resulting posts, which contains the translated posts's category name.
		 * If there is no translation available for this category name, then the original name is returned instead.
		 *
		 * @param Object $result The result from the query
		 * @param Object $server The WP API Rest Server
		 * @param WP_REST_Request $request The original REST request
		 * @return Object A potentially updated result object
		 */
		public function add_translated_category_name_to_posts( $result, $server, $request ) {
			if ( ! empty( $request[ SUPPLANG_GET_PARAM ] ) && supplang_locale_from_slug( $request[ SUPPLANG_GET_PARAM ] ) !== null ) {
				$mo = $this->get_language_mo( $request[ SUPPLANG_GET_PARAM ] );
				foreach ( $result->data as $key => $post ) {
					$category = get_category( $post['categories'][0], OBJECT );
					// phpcs:disable
					$localized_cat_name                              = null === $mo ? $category->name : $mo->translate( $category->name );
					// phpcs:enable
					$result->data[ $key ]['localized_category_name'] = $localized_cat_name;
					$result->data[ $key ]['content']['unrendered']   = strip_tags( $post['content']['rendered'] );
				}
			}
			return $result;
		}

		/**
		 * Return an MO object for the provided $lang_slug.
		 * If the $lang_slug does not match an available supplang language,
		 * or there is no MO file for the matchin locale, then NULL is returned.
		 *
		 * @param String $lang_slug The language slug
		 * @return MO|Null The MO file for the language, or NULL
		 */
		private function get_language_mo( $lang_slug ) {
			$locale = supplang_locale_from_slug( $lang_slug );
			$mofile = get_template_directory() . "/languages/$locale.mo";
			if ( null !== $locale && file_exists( $mofile ) ) {
				$mo = new MO();
				$mo->import_from_file( $mofile );
			}
			return isset( $mo ) ? $mo : null;
		}

	}

}
