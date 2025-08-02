<?php

namespace AIAnalytics\Tests\Integration;

use PHPUnit\Framework\TestCase;
use AIAnalytics\Tests\TestUtils;

/**
 * Simplified WordPress.org standards compliance test
 * Combines all WordPress.org requirements into focused tests
 */
class WordPressStandardsTest extends TestCase
{
    public function test_plugin_structure_is_valid()
    {
        // Core files exist
        $this->assertFileExists('ai-analytics.php', 'Main plugin file required');
        $this->assertFileExists('readme.txt', 'readme.txt required for WordPress.org');
        $this->assertDirectoryExists('includes', 'includes directory should exist');
    }

    public function test_plugin_headers_are_complete()
    {
        $requiredHeaders = [
            'Plugin Name' => 'AI Analytics',
            'Version' => '1.0.0',
            'Text Domain' => 'hall-ai-analytics',
            'License' => 'GPLv3'
        ];

        foreach ($requiredHeaders as $header => $expectedValue) {
            $actualValue = TestUtils::getPluginHeader($header);
            $this->assertNotNull($actualValue, "{$header} header is required");
            
            if ($expectedValue) {
                $this->assertEquals($expectedValue, $actualValue, 
                    "{$header} should be '{$expectedValue}'");
            }
        }
    }

    public function test_readme_format_is_valid()
    {
        $content = TestUtils::getFileContent('readme.txt');
        
        // Required sections
        $requiredSections = [
            '=== AI Analytics ===',
            '== Description ==',
            '== Installation ==', 
            '== Changelog =='
        ];

        foreach ($requiredSections as $section) {
            $this->assertStringContainsString($section, $content, 
                "readme.txt missing required section: {$section}");
        }

        // Version consistency
        $pluginVersion = TestUtils::getPluginHeader('Version');
        $stableTag = TestUtils::getReadmeField('Stable tag');
        $this->assertEquals($pluginVersion, $stableTag, 
            'Plugin version and readme stable tag must match');
    }

    public function test_security_requirements_are_met()
    {
        $files = TestUtils::getPluginFiles();

        foreach ($files as $file) {
            // Direct access prevention
            $content = TestUtils::getFileContent($file);
            $this->assertStringContainsString('ABSPATH', $content, 
                "File {$file} must prevent direct access");

            // No dangerous functions
            $dangerousFunctions = TestUtils::getDangerousFunctions();
            foreach ($dangerousFunctions as $func) {
                $this->assertStringNotContainsString($func, $content,
                    "File {$file} should not use dangerous function: {$func}");
            }
        }
    }

    public function test_input_sanitization_is_present()
    {
        $files = TestUtils::getPluginFiles();
        $sanitizationFunctions = TestUtils::getSanitizationFunctions();

        foreach ($files as $file) {
            $content = TestUtils::getFileContent($file);
            
            // If file uses superglobals, should have sanitization
            if (preg_match('/\$_(POST|GET|REQUEST|COOKIE)/', $content)) {
                $hasSanitization = TestUtils::fileContainsAny($file, $sanitizationFunctions);
                $this->assertTrue($hasSanitization, 
                    "File {$file} uses superglobals but lacks sanitization functions");
            }
        }
    }

    public function test_external_service_disclosure()
    {
        $content = TestUtils::getFileContent('readme.txt');
        
        // Must disclose external service usage
        $this->assertStringContainsString('External services', $content,
            'readme.txt must have External services section');
        
        $this->assertStringContainsString('analytics.usehall.com', $content,
            'Must disclose Hall Analytics API usage');
            
        $this->assertStringContainsString('Privacy Policy', $content,
            'Must link to privacy policy');
    }

    public function test_text_domain_consistency()
    {
        $textDomain = TestUtils::getPluginHeader('Text Domain');
        $this->assertEquals('hall-ai-analytics', $textDomain, 
            'Text domain should match plugin directory name');

        // Check translation functions use correct domain
        $files = TestUtils::getPluginFiles();
        foreach ($files as $file) {
            $content = TestUtils::getFileContent($file);
            
            if (preg_match_all('/esc_html__\([^,]+,\s*[\'"]([^\'"]+)[\'"]/', $content, $matches)) {
                foreach ($matches[1] as $domain) {
                    $this->assertEquals('hall-ai-analytics', $domain,
                        "Translation function in {$file} uses wrong text domain");
                }
            }
        }
    }

    public function test_nonce_verification_exists()
    {
        $files = TestUtils::getPluginFiles();

        foreach ($files as $file) {
            $content = TestUtils::getFileContent($file);
            
            // If processing forms, should verify nonces
            if (strpos($content, '$_POST') !== false && strpos($content, 'submit') !== false) {
                $hasNonceCheck = TestUtils::fileContainsAny($file, ['wp_verify_nonce', 'check_admin_referer']);
                $this->assertTrue($hasNonceCheck, "Form processing in {$file} should verify nonces");
            }
        }
    }

    public function test_capability_checks_exist()
    {
        $files = TestUtils::getPluginFiles();

        foreach ($files as $file) {
            $content = TestUtils::getFileContent($file);
            
            // Admin functions should check capabilities
            if (strpos($content, 'update_option') !== false || strpos($content, 'admin_') !== false) {
                $hasCapCheck = TestUtils::fileContainsAny($file, ['current_user_can', 'user_can']);
                $this->assertTrue($hasCapCheck, "Admin functionality in {$file} should check capabilities");
            }
        }
    }
}