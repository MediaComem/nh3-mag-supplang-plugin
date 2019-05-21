/**
 * This script makes the language switcher select list reload the page
 * while setting the new locale to the selected language.
 */
(function($) {
  console.log('Supplang Switcher Loaded');
  $('#supplang-selector-select').on('change', function() {
    console.log('Supplang Switcher Changed');
    var newUrl;
    var currentUrl = window.location.href;
    // Case when the supplang get param is already set in the current URL
    if (currentUrl.match(/[\?|&]uil=[^&]*/)) {
      newUrl = currentUrl.replace(/([\?|&]uil=)[^&]*/, '$1' + this.value);
    // Case when there already is some get param in the url
    } else if (currentUrl.indexOf('?') !== -1) {
      newUrl = currentUrl + "&uil=" + this.value;
    // Case when there is no param in the current url
    } else {
      newUrl = currentUrl + "?uil=" + this.value;
    }
    window.location.href = newUrl;
  });
})(jQuery)
