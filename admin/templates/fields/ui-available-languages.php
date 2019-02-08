<input
  type="text"
  id="<?= esc_attr( $args['label_for'] ) ?>"
  name="nh3_nls_settings[<?= esc_attr( $args['label_for'] ) ?>]"
  value="<?= isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : '' ?>">
<p class="description"><?= __('Separate locales with a comma. Ex: "fr_FR,it_IR"', 'nh3-nls') ?></p>
