<?php
/**
 * Template used to display the language selector in the theme
 * Called in the supplang_switcher() function
 */
?>

<?php if ( $options['wrapper'] ) : ?>
<div id="supplang-selector-wrapper">
<?php endif; ?>

	<select name="supplang-uil" id="supplang-selector-select">
	<?php foreach ( supplang_languages() as $language ) : ?>
	<option value="<?php echo esc_html( $language['slug'] ); ?>" <?php echo get_locale() === $language['locale'] ? 'selected' : ''; ?>>
    <?php printf( $options['template'], esc_html( $language['name'] ) ); ?>
  </option>
	<?php endforeach ?>
	</select>

<?php if ( $options['wrapper'] ) : ?>
</div>
<?php endif; ?>
