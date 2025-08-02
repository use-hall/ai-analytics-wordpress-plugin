<?php

namespace AIAnalytics\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery;

/**
 * Test suite for API calls and external service integration
 */
class ApiCallsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function test_visit_request_sends_correct_data_structure()
    {
        // Set up server environment
        $_SERVER['REQUEST_URI'] = '/blog/test-post';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0';
        $_SERVER['HTTP_REFERER'] = 'https://google.com';
        $_SERVER['REMOTE_ADDR'] = '192.168.1.1';
        
        // Mock WordPress functions
        Functions\when('get_option')
            ->alias(function($option) {
                if ($option === AI_ANALYTICS_ACCESS_TOKEN) return 'test-api-token-123';
                if ($option === AI_ANALYTICS_ENABLED) return '1';
                return null;
            });
            
        Functions\when('sanitize_url')->returnArg();
        Functions\when('sanitize_text_field')->returnArg();
        Functions\when('wp_unslash')->returnArg();
        Functions\when('wp_json_encode')->alias('json_encode');
        
        // Mock the API call and capture the data being sent
        $capturedData = null;
        Functions\expect('wp_remote_post')
            ->once()
            ->with('https://analytics.usehall.com/visit', Mockery::on(function($args) use (&$capturedData) {
                $capturedData = $args;
                return true;
            }));

        // Load analytics and trigger visit request
        require_once __DIR__ . '/../../includes/analytics.php';
        ai_analytics_send_visit_request();
        
        // Verify API call structure
        $this->assertNotNull($capturedData);
        $this->assertArrayHasKey('headers', $capturedData);
        $this->assertArrayHasKey('body', $capturedData);
        $this->assertArrayHasKey('blocking', $capturedData);
        
        // Verify headers
        $headers = $capturedData['headers'];
        $this->assertEquals('application/json', $headers['Content-Type']);
        $this->assertEquals('Bearer test-api-token-123', $headers['Authorization']);
        
        // Verify request is non-blocking
        $this->assertFalse($capturedData['blocking']);
        
        // Verify body structure
        $body = json_decode($capturedData['body'], true);
        $this->assertArrayHasKey('request_path', $body);
        $this->assertArrayHasKey('request_method', $body);
        $this->assertArrayHasKey('request_headers', $body);
        $this->assertArrayHasKey('request_ip', $body);
        $this->assertArrayHasKey('request_timestamp', $body);
        
        // Verify body content
        $this->assertEquals('/blog/test-post', $body['request_path']);
        $this->assertEquals('GET', $body['request_method']);
        $this->assertEquals('192.168.1.1', $body['request_ip']);
        $this->assertIsInt($body['request_timestamp']);
        
        // Verify headers are captured
        $requestHeaders = $body['request_headers'];
        $this->assertEquals('example.com', $requestHeaders['Host']);
        $this->assertEquals('Mozilla/5.0', $requestHeaders['User-Agent']);
        $this->assertEquals('https://google.com', $requestHeaders['Referer']);
        
        // Clean up
        unset($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $_SERVER['HTTP_HOST'], 
              $_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_REFERER'], $_SERVER['REMOTE_ADDR']);
    }

    public function test_visit_request_skips_when_analytics_disabled()
    {
        $_SERVER['REQUEST_URI'] = '/test-page';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        Functions\when('get_option')
            ->alias(function($option) {
                if ($option === AI_ANALYTICS_ACCESS_TOKEN) return 'test-token';
                if ($option === AI_ANALYTICS_ENABLED) return '0'; // Disabled
                return null;
            });
            
        Functions\when('sanitize_url')->returnArg();
        Functions\when('sanitize_text_field')->returnArg();
        Functions\when('wp_unslash')->returnArg();
        
        // API should NOT be called when disabled
        Functions\expect('wp_remote_post')->never();

        require_once __DIR__ . '/../../includes/analytics.php';
        ai_analytics_send_visit_request();
        
        unset($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
    }

    public function test_visit_request_skips_when_no_access_token()
    {
        $_SERVER['REQUEST_URI'] = '/test-page';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        Functions\when('get_option')
            ->alias(function($option) {
                if ($option === AI_ANALYTICS_ACCESS_TOKEN) return ''; // No token
                if ($option === AI_ANALYTICS_ENABLED) return '1';
                return null;
            });
            
        Functions\when('sanitize_url')->returnArg();
        Functions\when('sanitize_text_field')->returnArg();
        Functions\when('wp_unslash')->returnArg();
        
        // API should NOT be called without token
        Functions\expect('wp_remote_post')->never();

        require_once __DIR__ . '/../../includes/analytics.php';
        ai_analytics_send_visit_request();
        
        unset($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
    }

    public function test_visit_request_handles_missing_server_variables()
    {
        // Don't set REQUEST_URI or REQUEST_METHOD
        
        Functions\when('get_option')
            ->alias(function($option) {
                if ($option === AI_ANALYTICS_ACCESS_TOKEN) return 'test-token';
                if ($option === AI_ANALYTICS_ENABLED) return '1';
                return null;
            });
        
        // API should NOT be called with missing server variables
        Functions\expect('wp_remote_post')->never();

        require_once __DIR__ . '/../../includes/analytics.php';
        ai_analytics_send_visit_request();
    }

    public function test_api_call_uses_correct_endpoint()
    {
        $_SERVER['REQUEST_URI'] = '/test';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        
        Functions\when('get_option')
            ->alias(function($option) {
                if ($option === AI_ANALYTICS_ACCESS_TOKEN) return 'token';
                if ($option === AI_ANALYTICS_ENABLED) return '1';
                return null;
            });
            
        Functions\when('sanitize_url')->returnArg();
        Functions\when('sanitize_text_field')->returnArg();
        Functions\when('wp_unslash')->returnArg();
        Functions\when('wp_json_encode')->alias('json_encode');
        
        Functions\expect('wp_remote_post')
            ->once()
            ->with('https://analytics.usehall.com/visit', Mockery::type('array'));

        require_once __DIR__ . '/../../includes/analytics.php';
        ai_analytics_send_visit_request();
        
        unset($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $_SERVER['REMOTE_ADDR']);
    }

    public function test_api_call_timestamp_is_recent()
    {
        $_SERVER['REQUEST_URI'] = '/test';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        
        Functions\when('get_option')
            ->alias(function($option) {
                if ($option === AI_ANALYTICS_ACCESS_TOKEN) return 'token';
                if ($option === AI_ANALYTICS_ENABLED) return '1';
                return null;
            });
            
        Functions\when('sanitize_url')->returnArg();
        Functions\when('sanitize_text_field')->returnArg();
        Functions\when('wp_unslash')->returnArg();
        Functions\when('wp_json_encode')->alias('json_encode');
        
        $capturedTimestamp = null;
        Functions\expect('wp_remote_post')
            ->once()
            ->with('https://analytics.usehall.com/visit', Mockery::on(function($args) use (&$capturedTimestamp) {
                $body = json_decode($args['body'], true);
                $capturedTimestamp = $body['request_timestamp'];
                return true;
            }));

        $beforeCall = time();
        require_once __DIR__ . '/../../includes/analytics.php';
        ai_analytics_send_visit_request();
        $afterCall = time();
        
        // Timestamp should be within the call timeframe
        $this->assertGreaterThanOrEqual($beforeCall, $capturedTimestamp);
        $this->assertLessThanOrEqual($afterCall, $capturedTimestamp);
        
        unset($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $_SERVER['REMOTE_ADDR']);
    }
}