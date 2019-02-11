<input
  type="text"
  id="<?= esc_attr( $args['label_for'] ) ?>"
  name="supplang_uil_list"
  value="<?= isset( $options ) ? $options : '' ?>">
<p class="description"><?= __('Separate locales with a comma. Ex: "fr_FR,it_IT"', 'supplang') ?></p>
