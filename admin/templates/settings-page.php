<div class="wrap">
  <h1><?= esc_html(get_admin_page_title()); ?></h1>
  <form action="options.php" method="post">
    <?php
      // output security fields for the registered setting "supplang_settings"
      settings_fields('supplang');
      // output setting sections and their fields
      // (sections are registered for "supplang", each field is registered to a specific section)
      do_settings_sections('supplang');
      // output save settings button
      submit_button(__('Save Settings', 'supplang'));
    ?>
  </form>
</div>
