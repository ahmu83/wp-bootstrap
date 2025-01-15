<?php
/**
 * WP_DEBUG toggle using URL parameter "debug"
 * 
 * Usage: 
 *  Include this file in wp-config.php ( include 'WpDebug.class.php'; )
 *  Comment out WP_DEBUG, WP_DEBUG_LOG, WP_DEBUG_DISPLAY, SCRIPT_DEBUG in wp-config.php
 *  ?debug=1 to enable debugging, ?debug=0 to disable.
 */

namespace WpBootstrap;

class WpDebug {

  /**
   * Holds class instance
   *
   * @access protected
   */

  protected static $_instance;
  
  protected static $debug_status;
  
  function __construct() {

    if ( defined('WP_DEBUG') || defined('SCRIPT_DEBUG') || defined('WP_DEBUG_LOG') || defined('WP_DEBUG_DISPLAY') ) {

      return;

    }

    $this->init_debug();
    $this->define_constants(true); // always log errors
    $this->init_whoops(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR);

  }

  private function define_constants($always_log = false) {

    define( 'WP_DEBUG', $always_log ? true : self::$debug_status );
    define( 'WP_DEBUG_LOG', $always_log ? true : self::$debug_status );

    define( 'WP_DEBUG_DISPLAY', self::$debug_status );
    @ini_set( 'display_errors', self::$debug_status ? '1' : '0' );
    define( 'SCRIPT_DEBUG', self::$debug_status );

  }

  private function is_secure() {

    return
      (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
      || $_SERVER['SERVER_PORT'] == 443;

  }

  private function maybe_start_session() {

    $cookie_secure = $this->is_secure();
    // $cookie_secure = true;

    if ( session_status() === PHP_SESSION_NONE ) {

      session_start(['cookie_secure' => $cookie_secure, 'cookie_httponly' => true]);

    }

  }

  private function init_debug() {

    $this->maybe_start_session();

    $debug_toggle = filter_input( INPUT_GET, 'debug', FILTER_VALIDATE_INT );

    // print_r($debug_toggle);exit;

    if ( $debug_toggle !== null ) {

      $_SESSION['debug'] = $debug_toggle === 1;

    }

    $debug_status = !empty($_SESSION['debug']);

    self::$debug_status = $debug_status;

    // return $debug_status;

  }

  /**
   * Explanation of Error Types:
   * 
   *  E_ERROR: Fatal runtime errors.
   *  E_WARNING: Run-time warnings.
   *  E_PARSE: Compile-time parse errors.
   *  E_NOTICE: Run-time notices.
   *  E_CORE_ERROR, E_CORE_WARNING: Errors/warnings specific to PHP's core.
   *  E_COMPILE_ERROR, E_COMPILE_WARNING: Errors/warnings during script compilation.
   *  E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE: Errors/warnings/notices triggered by trigger_error.
   *  E_RECOVERABLE_ERROR: Catchable fatal errors.
   *  E_DEPRECATED, E_USER_DEPRECATED: Deprecated code warnings.
   *  
   * @param  [type] $error_type
   * @return void
   */
  private function init_whoops(
    $error_type = E_ERROR | E_WARNING | E_PARSE | E_NOTICE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE | E_RECOVERABLE_ERROR | E_DEPRECATED | E_USER_DEPRECATED
  ) {

    // Return if debugging is not enabled
    if ( defined('WP_DEBUG') && !WP_DEBUG ) {

      return;

    }

    // Initialize Whoops instance
    $whoops = new \Whoops\Run;
    $handler = new \Whoops\Handler\PrettyPageHandler();

    $handler->setPageTitle("An error occurred!");

    $whoops->pushHandler($handler);
    $whoops->register();

    // Custom error handler for selected error types
    $error_handler = function ($severity, $message, $file, $line) use ($error_type) {

      if ( !(error_reporting() & $severity) || !($severity & $error_type) ) {

        // Skip handling if the severity isn't reported or not in specified error types
        return false;

      }

      throw new \ErrorException($message, 0, $severity, $file, $line);

    };

    set_error_handler($error_handler);

    // Register a shutdown function to catch fatal errors
    register_shutdown_function(function () use ($whoops, $error_type) {

      $error = error_get_last();

      if ( $error && ($error['type'] & $error_type) ) {

        // Handle fatal errors with Whoops
        $whoops->handleException( new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']) );

      }

    });

  }

  /**
   * Get the instance class through the singleton pattern
   *
   * @access public
   */
  public static function get_instance() {

    if ( empty(self::$_instance) ) {

      self::$_instance = new self();

    }

    return self::$_instance;

  }

}

// WpDebug::get_instance();

// throw new Exception("This is a test exception to trigger Whoops."); // for testing

// echo $undefinedVariable; // This should trigger a notice


