<?php
/**
 * Integration with the Hall Analytics API
 * https://docs.usehall.com/api-reference/visit
 *
 * @package AI Analytics
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Visit tracking

function ai_analytics_send_visit_request() {
    $access_token = get_option(AI_ANALYTICS_ACCESS_TOKEN);
    $enabled = get_option(AI_ANALYTICS_ENABLED) === '1';

    $request_path = isset($_SERVER['REQUEST_URI']) ? sanitize_url(wp_unslash($_SERVER['REQUEST_URI'])) : false;
    $request_method = isset($_SERVER['REQUEST_METHOD']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_METHOD'])) : false;
    $request_headers = ai_analytics_get_request_headers();

    // Track the visit

    if ($enabled && $access_token && $request_path && $request_method && !ai_analytics_system_request($request_path)) {
        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $access_token
        );

        $body = array(
            'request_path' => $request_path,
            'request_method' => $request_method,
            'request_headers' => $request_headers,
            'request_ip' => ai_analytics_get_client_ip(),
            'request_timestamp' => time()
        );

        wp_remote_post('https://analytics.usehall.com/visit', array(
            'headers' => $headers,
            'body' => wp_json_encode($body),
            'blocking' => false
        ));
    }

}

add_action('wp_loaded', 'ai_analytics_send_visit_request');


// Utility functions

function ai_analytics_get_request_headers() {
    $header_names = [
        'Host',
        'User-Agent',
        'Referer',
    ];

    $request_headers = [];

    foreach ($header_names as $header_name) {
        $header_value = ai_analytics_get_request_header_value($header_name);

        if ($header_value) {
            $request_headers[$header_name] = $header_value;
        }
    }
    
    return $request_headers;
}

function ai_analytics_get_request_header_value($header_name) {
    $server_key = strtoupper(str_replace('-', '_', $header_name));
    $server_key_with_http_prefix = 'HTTP_' . $server_key;

    if (isset($_SERVER[$server_key])) {
        return sanitize_text_field(wp_unslash($_SERVER[$server_key]));
    } else if (isset($_SERVER[$server_key_with_http_prefix])) {
        return sanitize_text_field(wp_unslash($_SERVER[$server_key_with_http_prefix]));
    } else if (function_exists('getallheaders')) {
        $headers_with_lowercase_keys = array_change_key_case(getallheaders(), CASE_LOWER);
        $lowercased_header_name = strtolower($header_name);

        if (isset($headers_with_lowercase_keys[$lowercased_header_name])) {
            return $headers_with_lowercase_keys[$lowercased_header_name];
        } else {
            return null;
        }
    } else {
        return null;
    }
}

function ai_analytics_system_request($request_path) {
    return (
        stripos($request_path, '/wp-admin') === 0 ||
        stripos($request_path, '/wp-login') === 0 ||
        stripos($request_path, '/wp-cron') === 0 ||
        stripos($request_path, '/wp-json') === 0 ||
        stripos($request_path, '/wp-includes') === 0 ||
        stripos($request_path, '/wp-content') === 0
    );
}


function ai_analytics_get_client_ip() {
    $ip = false;
    
    if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_CF_CONNECTING_IP']));
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR']));
    } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
        $ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_REAL_IP']));
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
    }
    
    return $ip ? $ip : null;
}


