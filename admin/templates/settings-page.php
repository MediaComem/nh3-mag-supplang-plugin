<div class="wrap">
  <h1><?= esc_html(get_admin_page_title()); ?></h1>
  <form action="options.php" method="post">
    <?php
      // output security fields for the registered setting "nh3_nls_settings"
      settings_fields('nh3_nls');
      // output setting sections and their fields
      // (sections are registered for "nh3_nls", each field is registered to a specific section)
      do_settings_sections('nh3_nls');
      // output save settings button
      submit_button(__('Save Settings', 'nh3-nls'));
    ?>
  </form>
</div>
