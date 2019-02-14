# NH3 Language Switcher

This is a WordPress plugin that allows switching the language of a theme without medeling with the language of the content.

## Settings

This plugin comes with one setting, which is a list of languages available to the user for them to apply to the front-end UI.
The setting is located under the **Settings > Site Languages** menu from the admin panel.
The value of the field should be a list of [WordPress locales](https://translate.wordpress.org/), separated by a comma.

**Examples of valid values:**
* `fr_FR` - End users could choose a French UI... but having a single value is utterly useless.
* `fr_FR,it_IT` - End users could choose between a French or Italian UI.
* `fr_FR,en_EN,de_CH` - End users could choose between a French, English or Italian UI.
* ...

> Adding a locale to the list **DOES NOT** download the corresponding `.mo` and `.po` files! You'll need to do this yourself.

## Changing the front-end UI language

To change the language, you can send a POST request on the current URL, sending a POST param named `supplang-uil` whose value should be one of the locale defined in the admin panel.

You can also use a special function in your templates that displays a select list of the available languages and send the POST request for you. To do so, use this function somewhere in your templates:

```php
SUPPLANG_LANGUAGES_selector();
```
