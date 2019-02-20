<script>
	jQuery( document ).ready(function( $ ) {
    $('#supplang-selector-select').on('change', function() {
      $('#supplang-selector').submit();
    });
	});
</script>
<div id="supplang-selector-wrapper">
	<form action="<?php echo esc_html( $_SERVER['REQUEST_URI'] ); ?>" method="post" id="supplang-selector">
	<select name="supplang-uil" id="supplang-selector-select">
	<?php foreach ( SUPPLANG_LANGUAGES as $language ) : ?>
		<option value="<?php echo esc_html( $language['locale'] ); ?>" <?php echo get_locale() === $language['locale'] ? 'selected' : ''; ?>><?php echo esc_html( $language['name'] ); ?></option>
	<?php endforeach ?>
	</select>
	</form>
</div>
