<?php

namespace CliScripts;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;
use \CurlFile;
use \ZipArchive;

include_once 'utils.php';

class Releases {

  const COMMANDS = ['delete', 'make'];
  const VERSION_SYNTAX = '/v(\d\.){2}\d/';
  const WHITELIST = ['major', 'minor', 'patch'];

  public static function route(Event $event) {
    $args = $event->getArguments();
    // Incorrect number of arguments
    if ( sizeof($args) === 0 || sizeof($args) > 2 ) {
      write("ERROR --- You provided ".sizeof($args)." argument".(sizeof($args) === 0 ? '' : 's').".");
      self::writeHelp();
      exit();
    }
    $action = $args[0];
    if ($action === self::COMMANDS[0]) { // delete
      write("You wish to delete a release");
      $version = isset($args[1]) ? $args[1] : null;
      if (preg_match(self::VERSION_SYNTAX, $version)) {
        write("You wish to delete the $version release");
        self::delete($version);
      } else {
        write([
          "Bad version argument",
          self::writeHelp()
        ]);
      }
    } else if ($action === self::COMMANDS[1]) { // make
      write('You wish to make a release');
      $type = isset($args[1]) ? $args[1] : null;
      if (null !== $type && in_array($type, self::WHITELIST)) {
        self::make($type);
      } else {
        write([
          "Bad type argument",
          self::writeHelp()
        ]);
      }
    } else if (in_array($action, self::WHITELIST)) {
      self::make($action);
    } else {
      write(self::writeHelp());
    }
  }

  public static function writeHelp() {
    self::writeMakeHelp();
    write();
    self::writeDeleteHelp();
  }

  private static function writeMakeHelp() {
    write([
      '---------------------------------------',
      'Make and deploy a new release to GitLab',
      '',
      'Usage:',
      ' composer release [make] <major|minor|patch>',
      '',
      'Arguments:',
      '  major ---- will increment the first number of the release version.',
      '             example: getting from a v1.2.3 to a v2.0.0',
      '  minor ---- will increment the second number of the release version.',
      '             example: getting from a v.1.2.3 to a v.1.3.0',
      '  patch ---- will increment the last number of the release version.',
      '             example: getting from a v.1.2.3 to a v.1.2.4',
      'Help:',
      '  Update the plugin.json file.',
      '  Regenerate a new supplang.php file.',
      '  Create a zipfile containing the necessary plugin files.',
      '  Upload the zipfile to GitLab, using the settings in the .release.conf file.',
      '  Finally, create a new release on GitLab, attaching the uploaded zipfile.'
      ]);
    }

  private static function writeDeleteHelp() {
    write([
      '-----------------------------',
      'Delete a release from GitLab',
      '',
      'Usage:',
      '  composer release delete <version>',
      '',
      'Arguments:',
      '  <version> - the version number of the release to delete.',
      '              Must respect the format "v(\d\.){2}\d\."',
      'Examples:',
      '  composer release delete v1.2.3',
      '  composer release delete v0.0.1',
      '',
      'Help:',
    ]);
  }


  /**
   * Main process of creating a new Release, using semver notation.
   * This function should be called via a Composer script.
   * It accepts one argument, which is the type of the release, that must be one of the following values:
   * * `major` - A major release up the major number of your semver, and reset the minor and patch numbers, i.e. going from v0.1.3 to v1.0.0
   * * `minor` - A minor release up the minor number of your semver, and reset the patch number, i.e. going from v0.1.3 to v0.2.0
   * * `patch` - A patch release up the patch number of your semver, i.e. going from v0.1.3 to v0.1.4
   * At the end of the process, you'll find a new zip file in the `releases` folder with the name `supplang_release_vX.X.X.zip`, `vX.X.X` matchin the new version number.
   */
	private static function make($type) {
    write();
    // Making release
    if (self::checkGitStatus()) {
      // Get the new version based on given argument (major, minor, patch)
      $versions = self::bumpVersionNumberTo($type);
      write([
        "INFO ---- Last version found was ".$versions['last'],
        "INFO ---- Release type \"$type\" bumped the version to ".$versions['current']
      ]);
      // Update the plugin config
      self::updatePluginConfigVersion($versions['current']);
      // Regenerate the plugin header file
      exec('composer plugin-header');
      // Make new commit
      exec('git add .');
      exec('git commit -m "Release new '.$type.' version - '.$versions['current'].'"');
      write('INFO ---- New commit for the release.');
      // Zip folder
      $releaseZip = self::makeZipFolder($versions['current']);
      // Add new tag with the new version
      exec("git tag ".$versions['current']);
      write('INFO ---- New git tag "'.$versions['current'].'" created.');
      // Publish release
      if ($releaseZip) {
        $release_conf = self::getReleaseConfig();
        // Push changes to remote
        exec('git push && git push --tag');
        write('SUCCESS - Release commit and tag pushed to remote branch');
        // Upload zip file
        $zipUpload = self::uploadReleaseZip($release_conf, $releaseZip);
        if ($zipUpload) {
          self::createNewRelease($release_conf, $versions['current'], $zipUpload);
        }
      }
      write();
    }
  }

  private static function delete($version) {
    write("DELETED -- $version release");
  }

  /**
   * Upload the given zip file at $zipPath to GitLab as a project file.
   * @return Mixed The GitLab project relative path if file uploaded, or false if something bad happened.
   */
  private static function uploadReleaseZip($config, $zipPath) {
    [$zipFolder, $zipName] = explode('/', $zipPath);
    // Prepare the request
    $ch = curl_init();
    $options = [
      CURLOPT_URL => "{$config['gitlab']['api_endpoint']}/projects/{$config['gitlab']['project_id']}/uploads",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => true,
      CURLOPT_HTTPHEADER => [
        'Content-Type: multipart/form-data',
        "PRIVATE-TOKEN: {$config['gitlab']['private_token']}",
        "Accept: application/json"
      ],
      CURLOPT_POSTFIELDS => [
        'file' => new CurlFile($zipPath, 'application/zip', $zipName)
      ],
    ];
    curl_setopt_array($ch, $options);
    // Execute the request
    $response = curl_exec($ch);

    // TODO better error handling
    if (!$response) {
      write([
        "ERROR --- Error while uploading the release zip file \"$zipName\" to GitLab...",
        "INFO ---- Error n°".curl_errno($ch).": ".curl_error($ch)
      ]);
    } else {
      write("SUCCESS - Release zip file \"$zipName\" has been uploaded to GitLab.");
      $response = json_decode($response, JSON_UNESCAPED_SLASHES)['url'];
    }
    curl_close($ch);
    return $response;
  }

  /**
   * Create a new release for the specified $version, with the specified $uploadedZipPath asset.
   * @return Boolean True if creation successfull, False otherwise.
   */
  private static function createNewRelease($config, $version, $uploadedZipPath) {
    // Prepare the request
    $ch = curl_init();
    // Prepare the request payload
    $post_data = json_encode([
      'name' => "Release of $version",
      'tag_name' => $version,
      // TODO Better release note process (use external file ?)
      'description' => "New release of Supplang $version",
      'assets' => [
        'links' => [
          [
            'name' => 'WordPress Plugin Zipfile',
            'url' => "{$config['gitlab']['project_url']}$uploadedZipPath"
          ]
        ]
      ]
    ], JSON_UNESCAPED_SLASHES);

    $options = [
      CURLOPT_URL => "{$config['gitlab']['api_endpoint']}/projects/{$config['gitlab']['project_id']}/releases",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        "PRIVATE-TOKEN: {$config['gitlab']['private_token']}",
        "Accept: application/json",
      ],
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => $post_data,
    ];
    curl_setopt_array($ch, $options);
    // Execute the request
    $response = curl_exec($ch);

    if (!$response) {
      write([
        "ERROR --- Error while creating the $version release on GitLab...",
        "INFO ---- Error n°".curl_errno($ch).": ".curl_error($ch)
      ]);
    } else {
      write([
        "SUCCESS - New $version release created on GitLab.",
        "INFO ---- See it on {$config['gitlab']['project_url']}/releases"
      ]);
    }
    curl_close($ch);
    return $response;
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
   * Creates the zip folder for the release.
   * The resulting zip will be named after the plugin and the version number.
   * It will contain a single directory which will contain all the plugin files.
   * @return Mixed The relative path to the release zip, as a String, or false if something bad happened.
   */
  private static function makeZipFolder(string $version) {
    $pluginName = strtolower(loadConfigFile()->pluginName);
    $config = self::getReleaseConfig();
    $zipName = "{$pluginName}_{$version}.zip";
    $zipPath = "releases/$zipName";
    try {
      $zip = new ZipArchive();
      if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
        throw new Exception("cannot open <$zipPath>\n");
      }
      foreach ($config['zip_content']['files'] as $glob) {
        $zip->addGlob($glob, GLOB_BRACE);
      }
      write("INFO ---- $zip->numFiles file(s) added to the zip.");
      $zip->close();
      write("SUCCESS - New release zip file created at $zipPath");
    } catch (\Exception $e) {
      write("ERROR --- Error while creating the zipfile.");
      $zipPath = false;
    }
    return $zipPath;
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

  /**
   * Returns the content of the release .conf file as an associative array.
   */
  private static function getReleaseConfig() {
    return parse_ini_file('.release.conf', true);
  }

  /**
   * Performs status checks on the repository.
   * Ensure that there is no unstaged changes and no unpushed commits.
   * @return Boolean True if all checks passed, False otherwise.
   */
  private static function checkGitStatus() {
    exec('git status --porcelain', $status);
    if (sizeof($status) !== 0 ) {
      write([
        'ERROR --- You have unstaged changes in your repository...',
        'INFO ---- Please commit or stash them and retry.',
      ]);
      return false;
    }
    exec('git log @{u}..', $commits);
    if (sizeof($commits) !== 0 ) {
      write([
        'ERROR --- You have local commits that are not pushed to remote branch...',
        'INFO ---- Please push your local commits and retry.'
      ]);
      return false;
    }
    return true;
  }

}
