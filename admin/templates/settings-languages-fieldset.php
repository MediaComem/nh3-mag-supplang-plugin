<fieldset>
	<?php foreach ( $args['languages'] as $lang ) : ?>
      <label for="<?php echo esc_html( "supplang_lang_{$lang['locale']}" ); ?>">
      <input
        type="checkbox"
        id="<?php echo esc_html( "supplang_lang_{$lang['locale']}" ); ?>"
        name="<?php echo esc_html( SUPPLANG_AVAILABLE_UIL ); ?>[<?php echo esc_html( $lang['locale'] ); ?>]"
        <?php echo array_key_exists( $lang['locale'], $option_values ) ? 'checked' : ''; ?>
        value="1">
      <?php echo esc_html( $lang['name'] ); ?> (<?php echo esc_html($lang['slug']); ?>)
    </label>
      <?php if ( isset( $lang['loco_link'] ) ) : ?>
      <a href="<?php echo esc_html( $lang['loco_link'] ); ?>"><?php echo esc_html__( 'See translation state', 'supplang' ); ?></a>
    <?php endif; ?>
    <br>
	<?php endforeach; ?>
	<p class="description">(<?php esc_html_e( 'Use the checkbox before each language to enable or disable it for your users.', 'supplang' ); ?>)</p>
</fieldset>
