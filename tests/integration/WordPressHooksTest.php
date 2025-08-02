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
        Functions\expect('get_option')
            ->once()
            ->with(AI_ANALYTICS_ENABLED)
            ->andReturn(false);
            
        Functions\expect('add_option')
            ->once()
            ->with(AI_ANALYTICS_ENABLED, '1');
            
        Functions\expect('flush_rewrite_rules')
            ->once();

        // Load the main plugin file to register hooks
        require_once __DIR__ . '/../../ai-analytics.php';
        
        // Trigger activation
        ai_analytics_activate();
    }

    public function test_plugin_deactivation_hook_flushes_rewrite_rules()
    {
        Functions\expect('flush_rewrite_rules')
            ->once();

        // Load the main plugin file
        require_once __DIR__ . '/../../ai-analytics.php';
        
        // Trigger deactivation
        ai_analytics_deactivate();
    }

    public function test_admin_init_hook_registers_settings()
    {
        Functions\expect('register_setting')
            ->twice(); // Called for both settings

        // Load settings file
        require_once __DIR__ . '/../../includes/settings.php';
        
        // Trigger admin_init action
        Actions\expectDone('admin_init')
            ->whenHappen(function() {
                ai_analytics_register_settings();
            });
            
        do_action('admin_init');
    }

    public function test_admin_menu_hook_adds_settings_page()
    {
        Functions\expect('add_options_page')
            ->once()
            ->with(
                'AI Analytics',
                'AI Analytics',
                'manage_options',
                'ai_analytics_settings',
                'ai_analytics_page'
            );

        // Load settings file
        require_once __DIR__ . '/../../includes/settings.php';
        
        // Trigger admin_menu action
        Actions\expectDone('admin_menu')
            ->whenHappen(function() {
                ai_analytics_menu();
            });
            
        do_action('admin_menu');
    }

    public function test_wp_loaded_hook_sends_visit_request()
    {
        // Set up server variables for a valid request
        $_SERVER['REQUEST_URI'] = '/test-page';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        
        Functions\expect('get_option')
            ->with(AI_ANALYTICS_ACCESS_TOKEN)
            ->andReturn('test-token');
            
        Functions\expect('get_option')
            ->with(AI_ANALYTICS_ENABLED)
            ->andReturn('1');
            
        Functions\expect('sanitize_url')->returnArg();
        Functions\expect('sanitize_text_field')->returnArg();
        Functions\expect('wp_unslash')->returnArg();
        Functions\expect('wp_json_encode')->alias('json_encode');
        
        Functions\expect('wp_remote_post')
            ->once()
            ->with('https://analytics.usehall.com/visit', \Mockery::type('array'));

        // Load analytics file
        require_once __DIR__ . '/../../includes/analytics.php';
        
        // Trigger wp_loaded action
        Actions\expectDone('wp_loaded')
            ->whenHappen(function() {
                ai_analytics_send_visit_request();
            });
            
        do_action('wp_loaded');
        
        // Clean up server variables
        unset($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $_SERVER['HTTP_HOST'], $_SERVER['REMOTE_ADDR']);
    }

    public function test_wp_loaded_hook_skips_system_requests()
    {
        // Set up server variables for a system request
        $_SERVER['REQUEST_URI'] = '/wp-admin/admin.php';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        Functions\expect('get_option')
            ->with(AI_ANALYTICS_ACCESS_TOKEN)
            ->andReturn('test-token');
            
        Functions\expect('get_option')
            ->with(AI_ANALYTICS_ENABLED)
            ->andReturn('1');
            
        Functions\expect('sanitize_url')->returnArg();
        Functions\expect('sanitize_text_field')->returnArg();
        Functions\expect('wp_unslash')->returnArg();
        
        // wp_remote_post should NOT be called for system requests
        Functions\expect('wp_remote_post')
            ->never();

        // Load analytics file
        require_once __DIR__ . '/../../includes/analytics.php';
        
        // Trigger wp_loaded action
        ai_analytics_send_visit_request();
        
        // Clean up server variables
        unset($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
    }

    public function test_admin_enqueue_scripts_loads_styles_on_settings_page()
    {
        Functions\expect('wp_add_inline_style')
            ->once()
            ->with('wp-admin', \Mockery::type('string'));
            
        Functions\expect('wp_add_inline_script')
            ->once()
            ->with('jquery', \Mockery::type('string'));

        // Load settings file
        require_once __DIR__ . '/../../includes/settings.php';
        
        // Trigger admin_enqueue_scripts for our settings page
        ai_analytics_admin_enqueue_scripts('settings_page_ai_analytics_settings');
    }

    public function test_admin_enqueue_scripts_skips_other_pages()
    {
        Functions\expect('wp_add_inline_style')
            ->never();
            
        Functions\expect('wp_add_inline_script')
            ->never();

        // Load settings file
        require_once __DIR__ . '/../../includes/settings.php';
        
        // Trigger admin_enqueue_scripts for different page
        ai_analytics_admin_enqueue_scripts('edit.php');
    }
}