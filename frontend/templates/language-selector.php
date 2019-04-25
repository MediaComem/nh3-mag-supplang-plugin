<?php if( $wrapper ): ?>
<div id="supplang-selector-wrapper">
<?php endif; ?>

  <select name="supplang-uil" id="supplang-selector-select">
  <?php foreach ( supplang_languages() as $language ) : ?>
    <option value="<?php echo esc_html( $language['slug'] ); ?>" <?php echo get_locale() === $language['locale'] ? 'selected' : ''; ?>><?php echo esc_html( $language['name'] ); ?></option>
  <?php endforeach ?>
  </select>

<?php if ( $wrapper ): ?>
</div>
<?php endif; ?>
