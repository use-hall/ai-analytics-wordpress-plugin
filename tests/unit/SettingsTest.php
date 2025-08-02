<?php

namespace AIAnalytics\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;

/**
 * Test suite for settings functionality
 */
class SettingsTest extends TestCase
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

    public function test_ai_analytics_sanitize_access_token_removes_whitespace()
    {
        require_once __DIR__ . '/../../includes/settings.php';
        
        $result = ai_analytics_sanitize_access_token('  test-token-123  ');
        $this->assertEquals('test-token-123', $result);
    }

    public function test_ai_analytics_sanitize_access_token_removes_invalid_characters()
    {
        require_once __DIR__ . '/../../includes/settings.php';
        
        $result = ai_analytics_sanitize_access_token('test@token#with$invalid%chars');
        $this->assertEquals('testtokenwithinvalidchars', $result);
    }

    public function test_ai_analytics_sanitize_access_token_allows_valid_characters()
    {
        require_once __DIR__ . '/../../includes/settings.php';
        
        $result = ai_analytics_sanitize_access_token('valid-token_123');
        $this->assertEquals('valid-token_123', $result);
    }

    public function test_ai_analytics_sanitize_access_token_limits_length()
    {
        require_once __DIR__ . '/../../includes/settings.php';
        
        $longToken = str_repeat('a', 300);
        $result = ai_analytics_sanitize_access_token($longToken);
        $this->assertEquals(255, strlen($result));
    }

    public function test_ai_analytics_sanitize_checkbox_returns_1_for_valid_input()
    {
        require_once __DIR__ . '/../../includes/settings.php';
        
        $result = ai_analytics_sanitize_checkbox('1');
        $this->assertEquals('1', $result);
    }

    public function test_ai_analytics_sanitize_checkbox_returns_0_for_invalid_input()
    {
        require_once __DIR__ . '/../../includes/settings.php';
        
        $this->assertEquals('0', ai_analytics_sanitize_checkbox('0'));
        $this->assertEquals('0', ai_analytics_sanitize_checkbox('invalid'));
        $this->assertEquals('0', ai_analytics_sanitize_checkbox(''));
        $this->assertEquals('0', ai_analytics_sanitize_checkbox(null));
    }

    public function test_sanitize_access_token_handles_empty_string()
    {
        require_once __DIR__ . '/../../includes/settings.php';
        
        $result = ai_analytics_sanitize_access_token('');
        $this->assertEquals('', $result);
    }

    public function test_sanitize_access_token_handles_null()
    {
        require_once __DIR__ . '/../../includes/settings.php';
        
        $result = ai_analytics_sanitize_access_token(null);
        $this->assertEquals('', $result);
    }
}