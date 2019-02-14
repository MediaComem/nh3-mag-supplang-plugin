<?php
/**
 * Renders an HTML select list that allow users to change the UI langauge.
 */
function sl_languages_selector() {
	$availableLanguages = explode( ',', get_option( 'supplang_uil_list' ) );
	include 'templates/language-selector.php';
}
