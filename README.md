# Supplang WordPress Plugin

This is a WordPress plugin that allows for switching the language of a theme without interfering with the language of the content.

It also register a new custom taxonomy, called `supplang_lang` that can be applied to posts, and indicates that it has been written in a specific language.

> **This plugin has been developed using a WordPress 5.1.1**

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->


- [Installation](#installation)
  - [Update](#update)
- [Settings](#settings)
- [Usage](#usage)
  - [Utility function](#utility-function)
  - [Rendering](#rendering)
- [API](#api)
  - [`supplang_languages()`](#supplang_languages)
  - [`supplang_home_url( $path = '' )`](#supplang_home_url-path---)
  - [`supplang_slug_from_locale()`](#supplang_slug_from_locale)
  - [`supplang_locale_from_slug( $slug )`](#supplang_locale_from_slug-slug-)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

# Installation

To install the Supplang plugin, download the latest [release][2], and uncompress it in your `wp-content/plugins` folder (or use the **Plugins > Add New** menu entry and click on the **Upload Plugin** button).

Then, go to your WordPress admin and navigate to **Plugins > Installed Plugins** and activate the **Supplang** plugin.

> Activating the plugin will register the new `supplang_lang` taxonomy and create the defaults value, one for each out-of-the-box registered languages (see [Usage](#usage)).

## Update

To update the plugin, you'll need to download the new [release][2], uncompress it somwhere, and replace all the files in your `wp-content/plugins/supplang` folder by the one contained in the compressed downloaded file.

# Settings

This plugin comes with one setting, which is a list of languages that can be made available to the user, for them to select as their front-end UI.
The setting is located under the **Settings > Site Languages** menu from the admin panel.

Simply toggle a language checkbox to render this language either available (when checked) or unavailable (when unchecked) to you users.

> **Checking a langauge does not magically render your site translated in this language!** You **still need** to have a translation file available in your theme.

# Usage

The switching of the language occurs when the requested URL contains a GET parameter named `uil` whose value is one of the language's locale defined in the admin panel:

| Language | `uil` value |
| :------- | :---------- |
| French   | `fr`        |
| Italian  | `it`        |
| German   | `de`        |
| Rumansh  | `rm`        |
| English  | `en`        |

## Utility function

You can also use a special function in your templates that displays a `<select>` list of the available languages, and manages the GET param and it's value for you. To do so, use this function somewhere in your templates:

```php
supplang_switcher();
```
> You do not need to make an `echo` of this function ; just call it as is.

Note that for this switcher to properly function, some JavaScript is involved in the front-end, and a Cookie is placed on your user's browser to save the currently selected language, in case the `uil` param is missing in subsequent URL requests.

## Rendering

The `<select>` element (and its `<option>`) will be wrapped around a `<div class="">`. Suppose you checked French and English in your admin setting ; a call to `supplang_switcher()` would render the following HTML markup:

```html
<div id="supplang-selector-wrapper">
  <select name="supplang-uil" id="supplang-selector-select">
    <option value="fr">Français</option>
    <option value="en">English</option>
  </select>
</div>
```

You can get rid of the wrapping `<div>` by passing an option array to the `supplang_switcher()` function with a `wrapper` item set to `false`, like this:

```php
supplang_switcher( array( 'wrapper' => false ) );
```
In this case, the `<select>` element will be directly inserted in place:

```html
<select name="supplang-uil" id="supplang-selector-select">
  <option value="fr">Français</option>
  <option value="en">English</option>
</select>
```
# API

The Supplang plugin provides several utilities function, in addition to `supplang_switcher()`

## `supplang_languages()`

**Returns an array of available languages.**

The returned languages are the one that have been checked using the plugin setting in **Settings > Site Languages** page.

Each item is an array with the following properties:
 * `name` - The name of the language, in the language itself (i.e. "Deutsch" for the german language, or "Français" for the french)
 * `locale` - The name of the WordPress locale for this language (see [Usage](#usage))

## `supplang_home_url( $path = '' )`

**Append the supplang `uil` query param to the home url, and returns it.**

> This is a wrapper around [the wordpress `home_url()` function][1].

_Params:_
* `$path` _(string)_ - A path that will be appended to the home url, before the `uil` param.

## `supplang_slug_from_locale()`

**Get the supplang language slug corresponding to the current site locale.**

> **Note:** The locale will be searched among the **available** languages as set in the **Settings > Site Languages** page.

_Returns:_
 * _(string)_ - The language slug, composed of the first two characters of the defined locale.

## `supplang_locale_from_slug( $slug )`

**Get the supplang language locale corresponding to the given `$slug`.**

> **Note:** The slug will be searched among the **available** languages as set in the **Settings > Site Languages** page.

_Params:_
 * `$slug` _(string)_ - The language slug (i.e. `fr`, `en`, etc).

_Returns:_
 * _(string)_ - The language locale for the given slug, or `null` if no corresponding locale found.

# Filters

The plugin provide one filter, `supplang_register_languages` that allows you to add new supported languages to the plugin.

To do this, call `add_filter` with a callback that accepts one parameter, which is an array of the currently registered languages. You then can add a new item (or several) to this array to register as much languages.

Each new item must be an array with the following items:
* `name` - The name of the language, preferably in the language itself (so not its english name)
* `locale` - Must be an official WordPress locale code. See [here][3] and search for your language's locale code (that's the small code in grey at the bottom of each language card).
* `slug` - Your language's slug. It's used throughout the plugin and as the value of `uil`. It's usually the first two characters of your locale.

## Example

```php
add_filter( 'supplang_register_languages', function($languages) {
  $languages[] = array(
    'name'   => 'Español',
    'locale' => 'es_ES',
    'slug'   => 'es',
  );
  return $languages;
} );
```
This will register the spanish languages as a new supported language by the plugin. You'll then be able to check it in the admin setting panel, and thus, selecting it as your site locale.

> I repeat: registering a new language and checking it in the settings **does not translates your site in this language!**

<!-- # Development

## Release

cURL SSL certificate error resolution:
* Download the `cacert.pem` file from https://curl.haxx.se/docs/caextract.html
* Place the file on your server (like `C:\MAMP\cacert.pem`)
* Update the `php.ini` file with_
  curl.cainfo="C:\MAMP\cacert.pem"
  openssl.cafile="C:\MAMP\cacert.pem"
* Restart server -->

[1]: https://developer.wordpress.org/reference/functions/home_url/
[2]: https://gitlab.com/mediacomem/nh3-mag-supplang-plugin/releases
[3]: https://translate.wordpress.org/
