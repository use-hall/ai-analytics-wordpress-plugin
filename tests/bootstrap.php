<?php
/**
 * PHPUnit bootstrap file for AI Analytics WordPress Plugin tests
 */

// Load Composer autoloader
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Initialize Brain Monkey for WordPress function mocking
\Brain\Monkey\setUp();

// Define WordPress constants for testing
if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/wordpress/');
}

if (!defined('WP_DEBUG')) {
    define('WP_DEBUG', true);
}

// Mock common WordPress functions that are always needed
\Brain\Monkey\Functions\when('plugin_dir_path')->justReturn(__DIR__ . '/../');
\Brain\Monkey\Functions\when('plugin_dir_url')->justReturn('http://example.com/wp-content/plugins/ai-analytics/');
\Brain\Monkey\Functions\when('plugin_basename')->justReturn('ai-analytics/ai-analytics.php');
\Brain\Monkey\Functions\when('esc_js')->returnArg();
\Brain\Monkey\Functions\when('esc_html')->returnArg();
\Brain\Monkey\Functions\when('esc_attr')->returnArg();
\Brain\Monkey\Functions\when('esc_url')->returnArg();

// Load plugin constants
require_once __DIR__ . '/../includes/constants.php';

// Set up test environment cleanup
register_shutdown_function(function() {
    \Brain\Monkey\tearDown();
});