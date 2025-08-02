<?php

namespace AIAnalytics\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Actions;
use Brain\Monkey\Functions;

/**
 * Test suite for WordPress hooks and actions
 */
class WordPressHooksTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
        
        // Mock essential WordPress functions
        Functions\when('get_option')->justReturn('test-token');
        Functions\when('add_option')->justReturn(true);
        Functions\when('update_option')->justReturn(true);
        Functions\when('delete_option')->justReturn(true);
        Functions\when('flush_rewrite_rules')->justReturn(null);
        Functions\when('current_user_can')->justReturn(true);
        Functions\when('wp_verify_nonce')->justReturn(true);
        Functions\when('sanitize_text_field')->returnArg();
        Functions\when('wp_unslash')->returnArg();
        Functions\when('esc_html__')->returnArg();
        Functions\when('add_settings_error')->justReturn(null);
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function test_plugin_activation_hook_sets_default_options()
    {
        // Mock WordPress functions for activation
        Functions\when('get_option')->justReturn(false);
        Functions\when('add_option')->justReturn(true);
        Functions\when('flush_rewrite_rules')->justReturn(null);

        // Load and test activation function
        require_once __DIR__ . '/../../ai-analytics.php';
        
        // This should not throw errors
        $this->assertNull(ai_analytics_activate());
    }

    public function test_plugin_deactivation_hook_works()
    {
        Functions\when('flush_rewrite_rules')->justReturn(null);

        // Load the main plugin file
        require_once __DIR__ . '/../../ai-analytics.php';
        
        // This should not throw errors
        $this->assertNull(ai_analytics_deactivate());
    }

    public function test_admin_init_hook_registers_settings()
    {
        Functions\when('register_setting')->justReturn(true);

        // Load settings file
        require_once __DIR__ . '/../../includes/settings.php';
        
        // Test that function exists and can be called
        $this->assertTrue(function_exists('ai_analytics_register_settings'));
        
        // This should not throw errors
        $this->assertNull(ai_analytics_register_settings());
    }

    public function test_admin_menu_hook_adds_settings_page()
    {
        Functions\when('add_options_page')->justReturn('settings_page_hook');

        // Load settings file
        require_once __DIR__ . '/../../includes/settings.php';
        
        // Test that function exists and can be called
        $this->assertTrue(function_exists('ai_analytics_menu'));
        
        // This should not throw errors
        $this->assertNull(ai_analytics_menu());
    }

    public function test_wp_loaded_hook_function_exists()
    {
        // Load analytics file
        require_once __DIR__ . '/../../includes/analytics.php';
        
        // Test that the function exists
        $this->assertTrue(function_exists('ai_analytics_send_visit_request'));
    }

    public function test_analytics_functions_exist()
    {
        // Load analytics file
        require_once __DIR__ . '/../../includes/analytics.php';
        
        // Test that key functions exist
        $this->assertTrue(function_exists('ai_analytics_get_client_ip'));
        $this->assertTrue(function_exists('ai_analytics_system_request'));
        $this->assertTrue(function_exists('ai_analytics_get_request_headers'));
    }

    public function test_admin_enqueue_scripts_function_exists()
    {
        // Load settings file
        require_once __DIR__ . '/../../includes/settings.php';
        
        $this->assertTrue(function_exists('ai_analytics_admin_enqueue_scripts'));
    }

    public function test_settings_functions_exist()
    {
        // Load settings file
        require_once __DIR__ . '/../../includes/settings.php';
        
        // Test that key functions exist
        $this->assertTrue(function_exists('ai_analytics_sanitize_access_token'));
        $this->assertTrue(function_exists('ai_analytics_sanitize_checkbox'));
        $this->assertTrue(function_exists('ai_analytics_page'));
    }
}