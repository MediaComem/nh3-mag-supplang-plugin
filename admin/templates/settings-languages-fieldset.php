<fieldset>
  <?php foreach ( $args['languages'] as $lang): ?>
  <label for="<?= "supplang_lang_{$lang['locale']}" ?>">
  <input
    type="checkbox"
    id="<?= "supplang_lang_{$lang['locale']}" ?>"
    name="<?php echo SUPPLANG_AVAILABLE_UIL; ?>[<?= $lang['locale'] ?>]"
    <?php echo array_key_exists( $lang['locale'], $option_values) ? 'checked' : '' ?>
    value="1"><?php echo $lang['name']; ?></label>
    <?php if ( isset( $lang['loco_link'] ) ): ?>
      <a href="<?= $lang['loco_link'] ?>"><?= __('See translation state', 'supplang') ?></a>
    <?php endif; ?>
    <br>
  <?php endforeach; ?>
  <p class="description">(<?php _e('Use the checkbox before each language to enable or disable it for your users.', 'supplang') ?>)</p>
</fieldset>
