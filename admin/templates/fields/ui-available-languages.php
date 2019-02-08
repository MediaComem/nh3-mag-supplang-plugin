<input
  type="text"
  id="<?= esc_attr( $args['label_for'] ) ?>"
  name="nh3_nls_settings[<?= esc_attr( $args['label_for'] ) ?>]"
  value="<?= isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : '' ?>">
<p class="description"><?= __('List each langauge separated by a comma. Ex: "fr,it,de"', 'nh3-nls') ?></p>