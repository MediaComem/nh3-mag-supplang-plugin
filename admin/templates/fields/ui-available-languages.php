<input
  type="text"
  id="<?php echo esc_attr( $args['label_for'] ); ?>"
  name="supplang_uil_list"
  value="<?php echo isset( $options ) ? $options : ''; ?>">
<p class="description"><?php echo __( 'Separate locales with a comma. Ex: "fr_FR,it_IT"', 'supplang' ); ?></p>
