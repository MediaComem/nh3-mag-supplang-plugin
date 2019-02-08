<div class="wrap">
  <h1><?= esc_html(get_admin_page_title()); ?></h1>
  <p><?= __('List the languages in which your site is available to your users.', 'nh3-nls'); ?></p>
  <p><strong><?= __('Note: this only applies the user interface, not the content language', 'nh3-nls'); ?></strong></p>
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