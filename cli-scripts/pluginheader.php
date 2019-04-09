<?php

namespace CliScripts;

include_once 'utils.php';

Class PluginHeader {

  /**
   * Generates a plugin WordPress php file, using the content of the plugin.json file.
   * The file will be saved in the root directory, using the lowercase `pluginName` value.
   */
  public static function generate() {
    write();
    self::makeFile(loadConfigFile());
  }

  /**
   * Generates the plugin endpoint file based on the content of the `plugin.json` file.
   * The resulting file will be stored at the root of your plugin folder,
   * and be named with the lowercase value of the `pluginName` attribute.
   */
  private static function makeFile($config) {
    $filename = normalize_name("$config->pluginName.php");
    $lines = [
      '<?php',
      '/**',
      " * Plugin Name: $config->pluginName",
      " * Description: $config->description",
      " * Version:     $config->version",
      " * Author:      $config->author",
      " * Author URI:  $config->authorUri",
      " * Text Domain: $config->textDomain",
      " * Domain Path: $config->domainPath",
      ' */',
      '',
      '// THIS FILE IS AUTOMATICALLY GENERATED !',
      '// DO NOT ALTER ITS CONTENT',
      '',
      '// Main plugin file path',
      'define(\''.normalize_name($config->pluginName, '_', true).'_MAIN_FILE\', __FILE__);',
      '',
      '// Bootstrap the plugin.',
      "require_once '$config->bootstrapFilePath';"
    ];

    if (!$handle = fopen($filename, 'w')) {
      write("ERROR: Unable to open file \"$filename\"...");
      exit;
    }

    foreach ($lines as $line) {
      $line .= PHP_EOL;
      if (!fwrite($handle, $line)) {
        write("ERROR: Unable to write to file \"$filename\"...");
        exit;
      }
    }

    write("SUCCESS: The plugin header file \"$filename\" has been generated.");

    fclose($handle);
  }

}
