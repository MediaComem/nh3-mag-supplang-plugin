<script>
  jQuery( document ).ready(function( $ ) {
    $('#supplang-switcher-select').on('change', function() {
      $('#supplang-switcher').submit();
    });
  });
</script>
<div id="supplang-switcher-wrapper">
  <p><?= get_locale(  ) ?></p>
  <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" id="supplang-switcher">
    <select name="supplang-uil" id="supplang-switcher-select">
      <option value="fr_FR" <?= get_locale() == 'fr_FR' ? 'selected' : '' ?>>Français</option>
      <option value="it_IT" <?= get_locale() == 'it_IT' ? 'selected' : '' ?>>Italiano</option>
      <option value="de_CH" <?= get_locale() == 'de_CH' ? 'selected' : '' ?>>Deutsch</option>
    </select>
  </form>
</div>