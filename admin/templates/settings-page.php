<?php
/**
 * Template used to generate the settings page in the WordPress admin
 */
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<form action="options.php" method="post">
	<?php
		// output security fields for the registered setting "nh3_nls_settings"
		settings_fields( SUPPLANG_OPTION_GROUP );
		// output setting sections and their fields
		// (sections are registered for "nh3_nls", each field is registered to a specific section)
		do_settings_sections( SUPPLANG_ADMIN_PAGE_NAME );
		// output save settings button
		submit_button( esc_html__( 'Save Settings', 'supplang' ) );
	?>
	</form>
</div>
