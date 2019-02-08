<input
  type="text"
  id="<?= esc_attr( $args['label_for'] ) ?>"
  name="nh3_nls_ui_languages"
  value="<?= isset( $options ) ? $options : '' ?>">
<p class="description"><?= __('Separate locales with a comma. Ex: "fr_FR,it_IT"', 'nh3-nls') ?></p>
