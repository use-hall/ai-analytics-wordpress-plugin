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

// Load plugin constants
require_once __DIR__ . '/../includes/constants.php';

// Set up test environment cleanup
register_shutdown_function(function() {
    \Brain\Monkey\tearDown();
});