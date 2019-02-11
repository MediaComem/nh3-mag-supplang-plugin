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

As of now, the only way to change the UI language is to add an `uil` query parameter to the UI and access it. The value of the `uil` parameter should be one of the value defined in the plugin setting. Any `uil` value that is not one of those value will have no effect at all on the front-end UI.

> **Adding the `uil` query parameter is only required when _changing_ the language.** Any subsequent clik on link or typing of URL will remember the currently selected language.

**Example:**

Suppose your on the `http://example.com/article/welcome-to-the-jungle` article and you want to see the same article but with a UI in French, you could do so by accessing this URL: `http://example.com/article/welcome-to-the-jungle?uil=fr_FR`.
> This will only work if you added the `fr_FR` locale to your list of available languages in the settings.