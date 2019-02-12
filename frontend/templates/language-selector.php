<script>
  jQuery( document ).ready(function( $ ) {
    $('#supplang-selector-select').on('change', function() {
      $('#supplang-selector').submit();
    });
  });
</script>
<div id="supplang-selector-wrapper">
  <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" id="supplang-selector">
    <select name="supplang-uil" id="supplang-selector-select">
    <?php foreach ($availableLanguages as $availableLanguage) { ?>
      <option value="<?= $availableLanguage ?>" <?= get_locale() == $availableLanguage ? 'selected' : '' ?>><?= $availableLanguage ?></option>
    <?php } ?>
    </select>
  </form>
</div>