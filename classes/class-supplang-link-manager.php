<?php

class Supplang_Link_Manager {

	const LINK_HOOKS = array(
		'post',
		'author',
		'category',
	);

	public function __construct() {
		foreach ( self::LINK_HOOKS as $hook ) {
			add_filter( "{$hook}_link", array( $this, 'append_locale_to_links' ) );
		}
	}

	/**
	 * Append the supplang get param to the given $permalink, setting its value to the currently defined locale slug.
	 * **Note:** This is only done if the function is called from a non admin page.
	 * @param String $permalink The link url to update
	 * @return String The updated link url
	 */
	public function append_locale_to_links( $permalink ) {
		if ( ! is_admin() ) {
			$lang_slug = supplang_slug_from_locale();
			$permalink = $permalink . ( strpos( $permalink, '?' ) ? '&' : '?' ) . SUPPLANG_GET_PARAM . "=$lang_slug";
		}
		return $permalink;
	}

}
