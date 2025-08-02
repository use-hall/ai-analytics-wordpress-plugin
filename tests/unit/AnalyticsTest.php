<?php

namespace AIAnalytics\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;

/**
 * Test suite for analytics functionality
 */
class AnalyticsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
        
        // Mock WordPress functions
        Functions\when('get_option')->justReturn('test-token');
        Functions\when('sanitize_url')->returnArg();
        Functions\when('sanitize_text_field')->returnArg();
        Functions\when('wp_unslash')->returnArg();
        Functions\when('wp_json_encode')->alias('json_encode');
        Functions\when('wp_remote_post')->justReturn(['response' => ['code' => 200]]);
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function test_ai_analytics_get_client_ip_with_cloudflare()
    {
        $_SERVER['HTTP_CF_CONNECTING_IP'] = '1.2.3.4';
        $_SERVER['REMOTE_ADDR'] = '5.6.7.8';
        
        require_once __DIR__ . '/../../includes/analytics.php';
        
        $ip = ai_analytics_get_client_ip();
        $this->assertEquals('1.2.3.4', $ip);
        
        unset($_SERVER['HTTP_CF_CONNECTING_IP'], $_SERVER['REMOTE_ADDR']);
    }

    public function test_ai_analytics_get_client_ip_with_forwarded()
    {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '9.10.11.12';
        $_SERVER['REMOTE_ADDR'] = '5.6.7.8';
        
        require_once __DIR__ . '/../../includes/analytics.php';
        
        $ip = ai_analytics_get_client_ip();
        $this->assertEquals('9.10.11.12', $ip);
        
        unset($_SERVER['HTTP_X_FORWARDED_FOR'], $_SERVER['REMOTE_ADDR']);
    }

    public function test_ai_analytics_get_client_ip_with_remote_addr()
    {
        $_SERVER['REMOTE_ADDR'] = '13.14.15.16';
        
        require_once __DIR__ . '/../../includes/analytics.php';
        
        $ip = ai_analytics_get_client_ip();
        $this->assertEquals('13.14.15.16', $ip);
        
        unset($_SERVER['REMOTE_ADDR']);
    }

    public function test_ai_analytics_get_client_ip_returns_null_when_no_ip()
    {
        require_once __DIR__ . '/../../includes/analytics.php';
        
        $ip = ai_analytics_get_client_ip();
        $this->assertNull($ip);
    }

    public function test_ai_analytics_system_request_detects_admin_paths()
    {
        require_once __DIR__ . '/../../includes/analytics.php';
        
        $this->assertTrue(ai_analytics_system_request('/wp-admin/admin.php'));
        $this->assertTrue(ai_analytics_system_request('/wp-login.php'));
        $this->assertTrue(ai_analytics_system_request('/wp-cron.php'));
        $this->assertTrue(ai_analytics_system_request('/wp-json/wp/v2/posts'));
        $this->assertTrue(ai_analytics_system_request('/wp-includes/js/jquery.js'));
        $this->assertTrue(ai_analytics_system_request('/wp-content/themes/style.css'));
    }

    public function test_ai_analytics_system_request_allows_public_paths()
    {
        require_once __DIR__ . '/../../includes/analytics.php';
        
        $this->assertFalse(ai_analytics_system_request('/'));
        $this->assertFalse(ai_analytics_system_request('/about'));
        $this->assertFalse(ai_analytics_system_request('/blog/post-title'));
        $this->assertFalse(ai_analytics_system_request('/contact'));
    }

    public function test_ai_analytics_get_request_headers()
    {
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0';
        $_SERVER['HTTP_REFERER'] = 'https://google.com';
        
        require_once __DIR__ . '/../../includes/analytics.php';
        
        $headers = ai_analytics_get_request_headers();
        
        $this->assertEquals('example.com', $headers['Host']);
        $this->assertEquals('Mozilla/5.0', $headers['User-Agent']);
        $this->assertEquals('https://google.com', $headers['Referer']);
        
        unset($_SERVER['HTTP_HOST'], $_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_REFERER']);
    }

    public function test_ai_analytics_get_request_header_value()
    {
        require_once __DIR__ . '/../../includes/analytics.php';
        
        // Test with HTTP prefix
        $_SERVER['HTTP_USER_AGENT'] = 'Test Agent';
        $value = ai_analytics_get_request_header_value('User-Agent');
        $this->assertEquals('Test Agent', $value);
        unset($_SERVER['HTTP_USER_AGENT']);
        
        // Test without prefix
        $_SERVER['HOST'] = 'example.com';
        $value = ai_analytics_get_request_header_value('Host');
        $this->assertEquals('example.com', $value);
        unset($_SERVER['HOST']);
        
        // Test missing header
        $value = ai_analytics_get_request_header_value('NonExistent');
        $this->assertNull($value);
    }
}