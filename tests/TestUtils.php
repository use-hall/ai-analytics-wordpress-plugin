<?php

namespace AIAnalytics\Tests;

/**
 * Shared utilities for all tests
 */
class TestUtils
{
    /**
     * Get all PHP files in the plugin
     */
    public static function getPluginFiles()
    {
        $files = ['ai-analytics.php'];
        
        if (is_dir('includes')) {
            $includeFiles = glob('includes/*.php');
            $files = array_merge($files, $includeFiles);
        }
        
        return $files;
    }

    /**
     * Get file content safely
     */
    public static function getFileContent($filepath)
    {
        if (!file_exists($filepath)) {
            return '';
        }
        
        return file_get_contents($filepath);
    }

    /**
     * Check if file contains any of the given patterns
     */
    public static function fileContainsAny($filepath, array $patterns)
    {
        $content = self::getFileContent($filepath);
        
        foreach ($patterns as $pattern) {
            if (strpos($content, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Extract plugin header value
     */
    public static function getPluginHeader($header)
    {
        $content = self::getFileContent('ai-analytics.php');
        
        if (preg_match("/{$header}:\s*([^\n\r]+)/", $content, $matches)) {
            return trim($matches[1]);
        }
        
        return null;
    }

    /**
     * Extract readme.txt field value
     */
    public static function getReadmeField($field)
    {
        $content = self::getFileContent('readme.txt');
        
        if (preg_match("/{$field}:\s*([^\n\r]+)/", $content, $matches)) {
            return trim($matches[1]);
        }
        
        return null;
    }

    /**
     * Common dangerous functions to check for
     */
    public static function getDangerousFunctions()
    {
        return [
            'eval(',
            'exec(',
            'system(',
            'shell_exec(',
            'passthru(',
            'file_get_contents($_',
            'include $_',
            'require $_'
        ];
    }

    /**
     * Common sanitization functions
     */
    public static function getSanitizationFunctions()
    {
        return [
            'sanitize_text_field',
            'sanitize_email',
            'sanitize_url',
            'wp_unslash',
            'intval',
            'absint'
        ];
    }

    /**
     * Common escaping functions
     */
    public static function getEscapingFunctions()
    {
        return [
            'esc_html',
            'esc_attr',
            'esc_url',
            'esc_js',
            'wp_kses'
        ];
    }
}