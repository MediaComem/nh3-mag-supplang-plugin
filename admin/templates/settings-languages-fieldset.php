<?php
/**
 * Template used to render the list of registered languages in the admin settings page
 */
?>

<fieldset>
	<?php foreach ( $args['languages'] as $lang ) : ?>
		<input type="checkbox" id="<?php echo esc_html( "supplang_lang_{$lang['locale']}" ); ?>" name="<?php echo esc_html( SUPPLANG_AVAILABLE_UIL ); ?>[<?php echo esc_html( $lang['locale'] ); ?>]" <?php echo array_key_exists( $lang['locale'], $option_values ) ? 'checked' : ''; ?> value="1">
		<label for="<?php echo esc_html( "supplang_lang_{$lang['locale']}" ); ?>">
			<?php echo esc_html( $lang['name'] ); ?> (<?php echo esc_html( $lang['slug'] ); ?>)
			<?php if ( isset( $lang['loco_link'] ) ) : ?>
				<a href="<?php echo esc_html( $lang['loco_link'] ); ?>"><?php echo esc_html__( 'See translation state', 'supplang' ); ?></a>
			<?php endif; ?>
		</label>
		<br>
	<?php endforeach; ?>
	<p class="description">(<?php esc_html_e( 'Use the checkbox before each language to enable or disable it for your users.', 'supplang' ); ?>)</p>
</fieldset>
