<label for="lang_list"><?php esc_html_e( 'Choose a language', 'polylang' ); ?></label>
<select name="lang_list" id="lang_list">
  <option value=""></option>
	<?php
	foreach ( $this->get_predefined_languages() as $lg ) {
		printf(
			'<option value="%1$s:%2$s:%3$s:%4$s">%5$s - %2$s</option>' . "\n",
			esc_attr( $lg['code'] ),
			esc_attr( $lg['locale'] ),
			'rtl' == $lg['dir'] ? '1' : '0',
			esc_attr( $lg['flag'] ),
			esc_html( $lg['name'] )
		);
	}
	?>
</select>
<p><?php esc_html_e( 'You can choose a language in the list or directly edit it below.', 'polylang' ); ?></p>
