<?php
/**
 * override wp-content, plugins & themes directory
 * 
 * https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 * 
 */

define( 'WP_CONTENT_DIR', wpb_wp_dir('core/content') );
define( 'WP_CONTENT_URL', wpb_wp_url('core/content', 0) );

define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
define( 'WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins' );
define( 'PLUGINDIR', WP_CONTENT_DIR . '/plugins' );

define( 'UPLOADS', 'media' );

$debug_output = [
  'WP_CONTENT_DIR' => WP_CONTENT_DIR,
  'WP_CONTENT_URL' => WP_CONTENT_URL,
  'WP_PLUGIN_DIR' => WP_PLUGIN_DIR,
  'WP_PLUGIN_URL' => WP_PLUGIN_URL,
  'PLUGINDIR' => PLUGINDIR,
  'UPLOADS' => UPLOADS,
];

// wpb_printr($debug_output);exit;


