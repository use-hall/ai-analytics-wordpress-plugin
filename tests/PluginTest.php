<?php

use PHPUnit\Framework\TestCase;

/**
 * WordPress Plugin Tests
 * Tests core plugin functionality and WordPress.org compliance
 */
class PluginTest extends TestCase
{
    private static function getPluginFiles()
    {
        $files = ['hall-ai-analytics/ai-analytics.php'];
        if (is_dir('hall-ai-analytics/includes')) {
            $files = array_merge($files, glob('hall-ai-analytics/includes/*.php'));
        }
        return $files;
    }

    private static function getPluginHeader($header)
    {
        $content = file_get_contents('hall-ai-analytics/ai-analytics.php');
        if (preg_match("/{$header}:\s*([^\n\r]+)/", $content, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    public function test_plugin_structure()
    {
        $this->assertFileExists('hall-ai-analytics/ai-analytics.php', 'main plugin file required');
        $this->assertFileExists('hall-ai-analytics/readme.txt', 'readme.txt required for wordpress.org');
        $this->assertDirectoryExists('hall-ai-analytics/includes', 'includes directory required');
    }

    public function test_plugin_headers()
    {
        $required_headers = [
            'Plugin Name' => 'AI Analytics',
            'Text Domain' => 'hall-ai-analytics',
            'License' => 'GPLv3'
        ];

        foreach ($required_headers as $header => $expected) {
            $actual = self::getPluginHeader($header);
            $this->assertNotNull($actual, "{$header} header required");
            $this->assertEquals($expected, $actual, "{$header} should be '{$expected}'");
        }

        // Check that Version header exists without hardcoding the value
        $version = self::getPluginHeader('Version');
        $this->assertNotNull($version, "Version header required");
        $this->assertNotEmpty($version, "Version header should not be empty");
    }

    public function test_version_consistency()
    {
        $plugin_version = self::getPluginHeader('Version');
        
        $readme_content = file_get_contents('hall-ai-analytics/readme.txt');
        preg_match('/Stable tag:\s*([^\s\n\r]+)/', $readme_content, $matches);
        $readme_version = trim($matches[1]);
        
        $this->assertEquals($plugin_version, $readme_version, 
            'plugin version and readme stable tag must match');
    }

    public function test_security_compliance()
    {
        $files = self::getPluginFiles();
        $dangerous = ['eval(', 'exec(', 'system(', 'shell_exec('];
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            
            // direct access prevention
            $this->assertStringContainsString('ABSPATH', $content, 
                "file {$file} must prevent direct access");
            
            // no dangerous functions
            foreach ($dangerous as $func) {
                $this->assertStringNotContainsString($func, $content,
                    "file {$file} should not use dangerous function {$func}");
            }
        }
    }

    public function test_readme_compliance()
    {
        $content = file_get_contents('hall-ai-analytics/readme.txt');
        
        $required_sections = [
            '=== AI Analytics - Track AI Bots & Referrals ===',
            '== Description ==',
            '== Installation ==',
            '== Changelog =='
        ];

        foreach ($required_sections as $section) {
            $this->assertStringContainsString($section, $content, 
                "readme.txt missing section: {$section}");
        }
    }

    public function test_sanitization_present()
    {
        $files = self::getPluginFiles();
        $has_sanitization = false;
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            if (strpos($content, 'sanitize_') !== false || 
                strpos($content, 'wp_unslash') !== false ||
                strpos($content, 'esc_') !== false) {
                $has_sanitization = true;
                break;
            }
        }
        
        $this->assertTrue($has_sanitization, 
            'plugin should use wordpress sanitization functions');
    }
}