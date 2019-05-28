# Supplang WordPress Plugin

This is a WordPress plugin that allows switching the language of a theme without interfering with the language of the content.

It also register a new custom taxonomy, called `supplang_lang` that can be applied to posts to indicates that it has been written in a specific language.

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

# Updates

## Manual update

To manually update the plugin, you'll need to download the new [release][2], uncompress it somwhere, and replace all the files in your `wp-content/plugins/supplang` folder by the one contained in the compressed downloaded file.

## Automatic update

To automatically push the new updates on the server, configure a webhook on the `push` events. Then, on your server, detect changes on the `master` branch and execute a script that moves the required files in the `wp-content/wp-plugins/supplang` folder.

> You'll find a list of the required files blobs in the `.release.conf` file.

# Settings

This plugin comes with one setting, which is a list of languages that can be made available to the end-user, for them to select as their front-end UI.

The setting is located under the **Settings > Site Languages** menu from the admin panel.

Simply toggle a language checkbox to render this language either available (when checked) or unavailable (when unchecked) to you users.

> **Checking a langauge does not magically render your site translated in this language!** You **still need** to have a translation file available in your theme. We suggest using the Loco Translate plugin to manage your theme translation, if it does not natively provide one.

# Usage

The switching of the language occurs when the requested URL contains a GET parameter named `uil` whose value is one of the language's locale defined in the admin panel:

| Language | `uil` value |
| :------- | :---------- |
| French   | `fr`        |
| Italian  | `it`        |
| German   | `de`        |
| Rumansh  | `rm`        |
| English  | `en`        |

> See [the supported languages](./bootstrap.php#L23)

## Automatic detection

Some minimal language detection is done if the URL does not contain the `uil` GET param, and no cookie exists for this user.

This is done by parsing the HTTP_ACCEPT_LANGUAGE header, and checking if the first item somewhat matches one of the available (and checked) languages.

> See [the implementation](./classes/class-supplang-locale-manager.php#L50)

## Utility function

You can also use a special function in your templates that displays a `<select>` list of the available languages, and manages the GET param and it's value for you (enqueuing a JS script). To do so, use this function somewhere in your templates:

```php
supplang_switcher();
```
> You do not need to make an `echo` of this function ; just call it as is.

Note that for this switcher to properly function, some JavaScript is involved in the front-end, and a Cookie is placed on your user's browser to save the currently selected language, in case the `uil` param is missing in subsequent URL requests.

## Rendering

The `<select>` element (and its `<option>`) will be wrapped around a `<div id="supplang-selector-wrapper">`. Suppose you checked French and English in your admin setting ; a call to `supplang_switcher()` would render the following HTML markup:

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
> `supplang_switcher` accepts several other options.
> See [the implementation](./frontend/api.php#L2)

# API

The Supplang plugin provides several utilities function, in addition to `supplang_switcher()`

## `supplang_languages()`

> See [the implementation](./frontend/api.php#L36)

**Returns an array of available languages.**

The returned languages are the one that have been checked using the plugin setting in **Settings > Site Languages** page.

Each item is an array with the following properties:
 * `name` - The name of the language, in the language itself (i.e. "Deutsch" for the german language, or "Français" for the french)
 * `locale` - The name of the WordPress locale for this language (see [Usage](#usage))

## `supplang_home_url( $path = '' )`

> See [the implementation](./frontend/api.php#L68)

**Append the supplang `uil` query param to the home url, and returns it.**

> This is a wrapper around [the wordpress `home_url()` function][1].

_Params:_
* `$path` _(string)_ - A path that will be appended to the home url, before the `uil` param.

## `supplang_slug_from_locale()`

> See [the implementation](./frontend/api.php#L80)

**Get the supplang language slug corresponding to the current site locale.**

> **Note:** The locale will be searched among the **available** languages as set in the **Settings > Site Languages** page.

_Returns:_
 * _(string)_ - The language slug, composed of the first two characters of the defined locale.

## `supplang_locale_from_slug( $slug )`

> See [the implementation](./frontend/api.php#L90)

**Get the supplang language locale corresponding to the given `$slug`.**

> **Note:** The slug will be searched among the **available** languages as set in the **Settings > Site Languages** page.

_Params:_
 * `$slug` _(string)_ - The language slug (i.e. `fr`, `en`, etc).

_Returns:_
 * _(string)_ - The language locale for the given slug, or `null` if no corresponding locale found.

# Filters

The plugin provide one filter, `supplang_register_languages` that allows you to add new supported languages to the plugin.

> See [the implementation](./bootstrap.php#L60)

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

# REST API

The plugin updates the WordPress REST API results and provide a new request param.

> See [the implementation](./classes/class-supplang-api.php)

# Development

To contribute to this plugin, you'll need to have:

* A local PHP Server (like MAMP), with a PHP version at least equals to the one supported by WordPress 5.1.1
* A WordPress 5.1.1 (or higher) installed on your local server
* [Composer] installed (and added to your `PATH` so that you can execute `composer` from your terminal)

1. Clone this repository on your machine (I advise cloning it directly in the right folder, that is `wp-content/wp-plugins` of your WordPress instance)
2. Install the composer dependencies:
  ```
  $> composer install
  ```
3. Go to your local WordPress admin, and to the **Plugins > Installed Plugins** to activate the **Supplang** plugin.

## Project structure

Here's a rapid description of the project's structure:

* `admin` - Contains the `php` template used to display the plugin settings in the WordPress admin
* `classes` - Contains the classes used in the plugin (this is where most of the plugin's logic takes place)
* `cli-scripts` - Contains the scripts file used by the [composer scripts](#composer-scripts)
* `frontend` - Contains files related to the theme frontend features (the public API, the templates, the JS)
* `languages` - Contains the `.pot` file for translating the plugin (generate it with i.e. [POEdit])
* `bootstrap.php` - Contains the code that bootstrap the plugin
* `plugin.json` - Plugin metadata, used to generate the plugin entry file.
* `supplang.php` - Plugin entry file. Automatically generated by a script (see below). DO NOT MANUALLY UDPATE!

# Composer scripts

The [`composer.json` file](.composer.json) provides some scripts:

| Command                  | Description                                                                                                                                                           |
| :----------------------- | :-------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `composer lint`          | Execute PHPCS linting using the WordPress linting rules (it is advised to follow them, even if some are... questionnable)                                             |
| `composer lint:fix`      | Executes PHPCS linting and fixes all the fixable errors                                                                                                               |
| `composer release major` | Create a new semver major release for the plugin (See [the complete description](./cli-scripts/releases.php#L89))                                                     |
| `composer release minor` | Create a new semver minor release for the plugin (See [the complete description](./cli-scripts/releases.php#L89))                                                     |
| `composer release patch` | Create a new semver patch release for the plugin (See [the complete description](./cli-scripts/releases.php#L89))                                                     |
| `composer plugin-header` | Automatically generates the `supplang.php` file using the `plugin.json` values (used by the `composer release <type>` script. You shouldn't have to call it manually) |

[1]: https://developer.wordpress.org/reference/functions/home_url/
[2]: https://github.com/Fonsart/nh3-mag-supplang-plugin/releases
[3]: https://translate.wordpress.org/
[composer]: https://getcomposer.org/
[poedit]: https://poedit.net/
