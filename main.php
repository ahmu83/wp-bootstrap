<?php
/**
 * Status of the bootstrap application.
 * If false, all `wpb_inc` functions are disabled.
 */
define( 'WPB_ACTIVE', true );
define( 'WPB_DIR_NAME', basename(WPB_DIR) );

/**
 * Define the WordPress directory path.
 */
define('WPB_WP_DIR', wpb_wp_dir());

/**
 * Get the WordPress directory path.
 *
 * @param string $file Optional file to append to the path.
 * @return string The WordPress directory path.
 */
function wpb_wp_dir($file = '') {

  return wpb_root_dir($file, 1);

}

/**
 * Generate a URL based on the current URL, optionally adding a file and navigating levels.
 *
 * @param string $file  File or path to append to the resulting URL. Defaults to an empty string.
 * @param int    $level Levels to include from the current URL. Positive adds `$file` to the start,
 *                      negative adds `$file` to the end. Defaults to 0.
 * @return string       The generated URL.
 */
function wpb_wp_url($file = '', $level = 0) {

  // Determine the protocol
  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';

  // Get the host
  $host = $_SERVER['HTTP_HOST'] ?? '';

  // Get the current request URI and trim slashes
  $request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

  // Break the URI into parts
  $path_parts = explode('/', $request_uri);

  // Positive level: Add $file to the start, include specified levels
  if ($level > 0) {

    $included_parts = array_slice($path_parts, 0, $level);
    $base_path = implode('/', $included_parts);
    $url = $protocol . $host . '/' . ltrim($base_path, '/') . '/' . ltrim($file, '/') . '/';


  }

  // Negative level: Add $file to the end, include remaining levels
  elseif ($level < 0) {

    $included_parts = array_slice($path_parts, 0, count($path_parts) + $level);
    $base_path = implode('/', $included_parts);
    $url = $protocol . $host . '/' . ltrim($file, '/') . '/' . ltrim($base_path, '/') . '/';

  }

  // Level = 0: Add $file to the root URL
  else {

    $url = $protocol . $host . '/' . ltrim($file, '/') . '/';

  }

  return rtrim($url, '/');

}




/**
 * Get the root directory path.
 *
 * @param string $file Optional file to append to the path.
 * @param int $level   Levels to go back from the current directory.
 * @return string      The root directory path.
 */
function wpb_root_dir($file = '', $level = 0) {

  $path = wpb_trailingslashit(WPB_DIR);

  // Navigate up directory levels
  for ($i = 0; $i < $level; $i++) {

    $path = dirname($path);

  }

  $dir = wpb_trailingslashit($path) . ltrim($file, '/');

  return rtrim($dir, '/');

}

/**
 * Include a file with optional environment handling.
 *
 * @param string $file The filename to include.
 */
function wpb_inc($file) {

  if (WPB_ACTIVE !== true) {

    return;

  }

  $base_path = wpb_trailingslashit(wpb_root_dir());
  $file_path = $base_path . ltrim($file, '/');
  $inc_path = wpb_trailingslashit(WPB_DIR . '/inc');

  // Handle environment-specific files
  if (strpos($file_path, $inc_path) !== false) {

    $envs = wpb_envs();
    $current_host = $_SERVER['HTTP_HOST'] ?? '';
    $environment_key = null;

    foreach ($envs as $key => $hosts) {

      foreach ($hosts as $host) {

        $parsed_host = parse_url($host, PHP_URL_HOST) ?? $host;

        if ($current_host === $parsed_host) {

          $environment_key = $key;
          break 2;

        }

      }

    }

    if ($environment_key && $environment_key !== 'production') {

      $env_specific_file = $inc_path . pathinfo($file, PATHINFO_FILENAME) . ".{$environment_key}.php";

      if (file_exists($env_specific_file)) {

        $file_path = $env_specific_file;

      }

    }

  }

  file_exists($file_path)
    ? require_once $file_path
    : error_log("Error: WP Bootstrap File {$file_path} Not Found.");

}

/**
 * Get environment details from `envs.php` or `envs.local.php`.
 * If `envs.local.php` exists, it takes precedence.
 *
 * @param string|null $name Environment name (e.g., 'production', 'staging', 'local').
 * @return array|null All environments if $name is null, or details of the specified environment.
 */
function wpb_envs($name = null) {

  $envs_file = wpb_root_dir('envs.php');
  $envs_file_local = wpb_root_dir('envs.local.php');
  $envs = file_exists($envs_file_local) ? include $envs_file_local : include $envs_file;

  return $name === null ? $envs : ($envs[$name] ?? null);

}

/**
 * Get the current environment based on the host name.
 *
 * @return string|null The environment key (e.g., 'production', 'staging', 'local') or null if not found.
 */
function wpb_env() {

  $envs = wpb_envs();
  $current_host = $_SERVER['HTTP_HOST'] ?? '';

  foreach ($envs as $key => $hosts) {

    if (in_array("http://$current_host", $hosts) || in_array("https://$current_host", $hosts)) {

      return $key;

    }

  }

  return null;

}

/**
 * Ensure a path has a single trailing slash.
 *
 * @param string $path The path to normalize.
 * @return string The normalized path.
 */
function wpb_trailingslashit($path) {

  return rtrim($path, '/\\') . '/';

}

/**
 * Print an array for debugging.
 *
 * @param array $arr The array to print.
 * @param string|null $title Optional title for the output.
 */
function wpb_printr($arr, $title = null) {

  echo '<pre>';
  echo '<h3>' . $title . '</h3>';
  echo print_r($arr, true);
  echo '</pre>';

}

/**
 * Enable PHP error reporting.
 */
function wpb_enable_errors() {

  if (WPB_ACTIVE !== true) {

    return;

  }

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

}

/**
 * Disable PHP error reporting.
 */
function wpb_disable_errors() {

  if (WPB_ACTIVE !== true) {

    return;

  }

  ini_set('display_errors', 0);
  ini_set('display_startup_errors', 0);
  error_reporting(0);

}

// wpb_enable_errors();
// wpb_disable_errors();

