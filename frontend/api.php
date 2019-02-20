<?php
/**
 * Renders an HTML select list that allow users to change the UI langauge.
 */
function supplang_switcher() {
	include 'templates/language-selector.php';
}

/**
 * Returns an array of available languages. Each item is an array with the following properties:
 * * `name` - The name of the language, in the language itself
 * * `locale` - The name of the WordPress locale for this language
 */
function supplang_languages() {
	return SUPPLANG_LANGUAGES;
}
