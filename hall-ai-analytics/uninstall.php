<?php
/**
 * Uninstall AI Analytics
 *
 * This file runs when the plugin is deleted from the WordPress admin.
 * It removes all plugin data from the database.
 *
  * @package AI Analytics
 *
 */

// Prevent direct access
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Define plugin constants if not already defined
if (!defined('AI_ANALYTICS_ACCESS_TOKEN')) {
    define('AI_ANALYTICS_ACCESS_TOKEN', 'ai_analytics_access_token');
}
if (!defined('AI_ANALYTICS_ENABLED')) {
    define('AI_ANALYTICS_ENABLED', 'ai_analytics_enabled');
}

// Remove all plugin options
delete_option(AI_ANALYTICS_ACCESS_TOKEN);
delete_option(AI_ANALYTICS_ENABLED);

// For multisite installations, remove options from all sites
if (is_multisite()) {
    $sites = get_sites();
    foreach ($sites as $site) {
        switch_to_blog($site->blog_id);
        
        delete_option(AI_ANALYTICS_ACCESS_TOKEN);
        delete_option(AI_ANALYTICS_ENABLED);
        
        restore_current_blog();
    }
}

// Clear any cached data
wp_cache_flush();