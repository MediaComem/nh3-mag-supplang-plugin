<?php

namespace CliScripts;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

include_once 'utils.php';

class Releases {

  const WHITELIST = ['major', 'minor', 'patch'];

  /**
   * Main process of creating a new Release, using semver notation.
   * This function should be called via a Composer script.
   * It accepts one argument, which is the type of the release, that must be one of the following values:
   * * `major` - A major release up the major number of your semver, and reset the minor and patch numbers, i.e. going from v0.1.3 to v1.0.0
   * * `minor` - A minor release up the minor number of your semver, and reset the patch number, i.e. going from v0.1.3 to v0.2.0
   * * `patch` - A patch release up the patch number of your semver, i.e. going from v0.1.3 to v0.1.4
   * At the end of the process, you'll find a new zip file in the `releases` folder with the name `supplang_release_vX.X.X.zip`, `vX.X.X` matchin the new version number.
   */
	public static function make(Event $event) {
    write();
    $args = $event->getArguments();
    $type = $args[0];
    // Incorrect number of arguments
    if ( sizeof($args) === 0 || sizeof($args) > 1 ) {
      write([
        "ERROR --- ".self::acceptedArgs(),
        "ERROR --- You provided ".sizeof($args)." argument".(sizeof($args) === 0 ? '' : 's')."."
      ]);
    // Invalid argument
    } elseif (!in_array($type, self::WHITELIST)) {
      write([
        "ERROR --- The provided argument, \"$type\", is not a valid argument.",
        "INFO ---- ".self::acceptedArgs()
      ]);
    // Making release
    } else {
      // Get the new version last tag and given argument (major, minor, patch)
      $versions = self::bumpVersionNumberTo($type);
      write([
        "INFO ---- Last version found was ".$versions['last'],
        "SUCCESS - Release type \"$type\" bumped the version to ".$versions['current']
      ]);
      // Update the plugin config
      self::updatePluginConfigVersion($versions['current']);
      // Zip folder
      $zipName = strtolower(loadConfigFile()->pluginName)."_".$versions['current'];
      self::makeZipFolder($zipName);
      // Add new tag with the new version
      exec("git tag ".$versions['current']);
      write('SUCCESS - New git tag "'.$versions['current'].'" created.');
      // Publish release ?
      write();
    }
  }

  /**
   * Generic message for the accepted arguments.
   * TODO: Generate the list from the WHITELIST constant
   */
  private static function acceptedArgs() {
    return 'The "release" script requires one argument among the following ones : "major", "minor" or "patch".';
  }

  /**
   * Retrieve the last version number from the git tags of the repository, and up it according to the $type argument.
   * @return Array An array with two item.
   *                `last` contains the last version number.
   *                `current` contains the new version number.
   */
  private static function bumpVersionNumberTo($type) {
    exec('git tag -l', $tags); // Get git tags
    $version['last'] = sizeof($tags) === 0 ? 'v0.0.0' : end($tags);
    $last_array = explode('.', str_replace('v', '', $version['last']));
    $current_array = [
      'major' => (int) $last_array[0],
      'minor' => (int) $last_array[1],
      'patch' => (int) $last_array[2]
    ];

    // Bump the version
    switch ($type) {
      case "major":
        $current_array['major']++;
        $current_array['minor'] = 0;
        $current_array['patch'] = 0;
        break;
      case "minor":
        $current_array['minor']++;
        $current_array['patch'] = 0;
        break;
      case "patch":
        $current_array['patch']++;
        break;
    }
    $version['current'] = 'v'.implode('.', $current_array);
    return $version;
  }

  /**
   * Creates the zip folder for the release
   */
  private static function makeZipFolder(string $zipName) {
    $zipFile = new \PhpZip\ZipFile();
    try {
      $zipFile
        ->addFile("supplang.php")
        ->addFile("bootstrap.php")
        ->addDirRecursive("frontend", "frontend")
        ->addDirRecursive("classes", "classes")
        ->addDirRecursive("includes", "includes")
        ->addDirRecursive("languages", "languages")
        ->saveAsFile("releases/$zipName.zip")
        ->close();
    } catch (\PhpZip\Exception\ZipException $e) {
      write("ERROR --- Error while creating the zipfile.");
    } finally {
      $zipFile->close();
      write("SUCCESS - New release created at releases/$zipName.zip");
    }
  }

  /**
   * Update the `plugin.config` file by changing the `version` value.
   */
  private static function updatePluginConfigVersion(string $current_version) {
    $config = loadConfigFile();
    $config->version = str_replace('v', '', $current_version);
    file_put_contents('plugin.json', json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    write("SUCCESS - Plugin config version has been updated to $current_version");
  }
}
