<?php
/**
 * Class to manage custom folder structures for WordPress core directories.
 * 
 * This class allows the redefinition of `wp-content`, `plugins`, `themes`, 
 * and `uploads` directories for custom folder structures.
 *
 * Usage:
 * - TODO
 */

namespace WpBootstrap;

class MoveFolders {

  /**
   * Custom folder names for core directories.
   *
   * @var array
   */
  private static $folder_names = array(
    'wp-content' => 'app',
    'plugins'    => 'addons',
    'themes'     => 'themes',
    'uploads'    => 'media',
  );

  private static $wp_content_dir;
  private static $wp_content_url;

  private static $plugins_dir;
  private static $plugins_url;

  private static $themes_dir;
  private static $themes_url;

  /**
   * Constructor to initialize and set custom folder paths.
   *
   * @param bool $set_themes_dir Whether to only set the themes directory. Default is false.
   */
  public function __construct($set_themes_dir = false) {

    self::$wp_content_dir = wpb_wp_dir(self::$folder_names['wp-content']);
    self::$wp_content_url = wpb_wp_url(self::$folder_names['wp-content'], 0);

    self::$plugins_dir = self::$wp_content_dir . '/' . self::$folder_names['plugins'];
    self::$plugins_url = self::$wp_content_url . '/' . self::$folder_names['plugins'];

    self::$themes_dir = self::$wp_content_dir . '/' . self::$folder_names['themes'];
    self::$themes_url = self::$wp_content_url . '/' . self::$folder_names['themes'];

    if ($set_themes_dir === false) {

      $this->wpContent();
      $this->plugins();
      $this->uploads();

    } else {

      $this->themes();

    }

  }

  /**
   * Define custom `wp-content` directory and URL.
   */
  private function wpContent() {

    self::$wp_content_dir = wpb_wp_dir(self::$folder_names['wp-content']);
    self::$wp_content_url = wpb_wp_url(self::$folder_names['wp-content'], 0);

    define('WP_CONTENT_DIR', self::$wp_content_dir);
    define('WP_CONTENT_URL', self::$wp_content_url);

  }

  /**
   * Define custom plugins directory and URL.
   */
  private function plugins() {

    self::$plugins_dir = self::$wp_content_dir . '/' . self::$folder_names['plugins'];
    self::$plugins_url = self::$wp_content_url . '/' . self::$folder_names['plugins'];

    define('PLUGINDIR', self::$plugins_dir);
    define('WP_PLUGIN_DIR', self::$plugins_dir);
    define('WP_PLUGIN_URL', self::$plugins_url);

  }

  /**
   * Define custom uploads directory.
   */
  private function uploads() {

    define('UPLOADS', self::$folder_names['uploads']);

  }

  /**
   * Register custom themes directory.
   */
  private function themes() {

    add_action('setup_theme', function () {

      register_theme_directory(self::$themes_dir);

    });

  }

}

