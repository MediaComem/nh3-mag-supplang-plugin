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
    <?php foreach (SL_LANGUAGES as $language) : ?>
      <option value="<?= $language['locale'] ?>" <?= get_locale() == $language['locale'] ? 'selected' : '' ?>><?= $language['name'] ?></option>
    <?php endforeach ?>
    </select>
  </form>
</div>
