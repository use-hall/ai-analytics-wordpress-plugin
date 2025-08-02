<?php
/**
 * WordPress settings functionality
 *
 * @package AI Analytics
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Registration

function ai_analytics_register_settings() {
    // Register access token setting with validation callback
    register_setting(
        AI_ANALYTICS_SETTINGS_GROUP, 
        AI_ANALYTICS_ACCESS_TOKEN, 
        array(
            'type' => 'string',
            'sanitize_callback' => 'ai_analytics_sanitize_access_token',
            'default' => '',
            'capability' => 'manage_options'
        )
    );
    
    // Register analytics enabled setting with validation callback
    register_setting(
        AI_ANALYTICS_SETTINGS_GROUP, 
        AI_ANALYTICS_ENABLED, 
        array(
            'type' => 'string',
            'sanitize_callback' => 'ai_analytics_sanitize_checkbox',
            'default' => '1',
            'capability' => 'manage_options'
        )
    );
}

/**
 * Sanitize access token input
 *
 * @param string $input The input to sanitize
 * @return string Sanitized access token
 */
function ai_analytics_sanitize_access_token($input) {
    // Remove any whitespace
    $input = trim($input);
    
    // Only allow alphanumeric characters, hyphens, and underscores
    $input = preg_replace('/[^a-zA-Z0-9_-]/', '', $input);
    
    // Limit length to 255 characters
    if (strlen($input) > 255) {
        $input = substr($input, 0, 255);
    }
    
    return $input;
}

/**
 * Sanitize checkbox input
 *
 * @param string $input The input to sanitize
 * @return string Sanitized checkbox value
 */
function ai_analytics_sanitize_checkbox($input) {
    return ($input === '1') ? '1' : '0';
}

add_action('admin_init', 'ai_analytics_register_settings');

// Menu Item

function ai_analytics_menu() {
    add_options_page(
        'AI Analytics',    // Page title
        'AI Analytics',    // Menu title  
        'manage_options',    // Capability
        'ai_analytics_settings',    // Menu slug
        'ai_analytics_page' // Callback function
    );
}

add_action('admin_menu', 'ai_analytics_menu');

// Enqueue admin scripts and styles
function ai_analytics_admin_enqueue_scripts($hook) {
    // Only load on our settings page
    if ('settings_page_ai_analytics_settings' !== $hook) {
        return;
    }
    
    // Enqueue admin styles
    wp_add_inline_style('wp-admin', '
        .settings_page_ai_analytics_settings #wpcontent,
        .settings_page_ai_analytics_settings.auto-fold #wpcontent {
            padding-left: 0;
        }
        .header {
            background-color: #fff;
            padding: 40px 40px 0 60px;
            border-bottom: 1px solid #dcdcde;
            display: flex;
            flex-direction: row;
            height: 400px;
            overflow: hidden;
            flex-shrink: 1;
        }
        .header .text {
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex-shrink: 1;
            padding-right: 50px;
            gap: 8px;
        }
        .header .logo {
            width: 84px;
            height: 28px;
            display: block;
        }
        .header .headline {
            margin-top: 20px;
            font-size: 32px;
            line-height: 1.1em;
            font-weight: 500;
            color: #1d2327;
        }
        .header .lead {
            font-size: 16px;
            color: #666;
        }
        .header .screenshot {
            max-width: 600px;
            width: auto;
            height: auto;
            margin-bottom: -20px;
            flex-shrink: 0;
            object-fit: contain;
        }
        .header .button-primary, .header .button {
            display: inline-flex;
            flex-direction: row;
            align-items: center;
            gap: 8px;
            width: fit-content;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        @media (max-width: 1200px) {
            .header {
                padding: 40px;
                height: auto;
            }
            .header .screenshot {
                max-width: 400px;
                width: auto;
                height: auto;
                margin-bottom: -80px;
            }
            .header .headline {
                font-size: 24px;
            }
            .header .lead {
                font-size: 14px;
            }
        } 
        @media (max-width: 768px) {

            .header .screenshot {
                display: none;
            }
        }
        .page-heading {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-items: center;
            justify-content: space-between;
            gap: 8px;
            padding-top: 40px;
        }
        .page-heading h1 {
            margin: 0;
            padding: 0;
        }
        .page-heading .button {
            display: inline-flex;
            flex-direction: row;
            align-items: center;
            gap: 8px;
        }
        .step-section {
            padding: 5px 0;
        }
        .password-input-container {
            width: 100%;
            display: flex;
            align-items: center;
        }
        .password-input-container input {
            flex: 1;
            margin-right: 8px;
        }
        .password-input-container .button {
            margin-left: 8px;
            flex-shrink: 1;
            display: inline-flex;
            flex-direction: row;
            align-items: center;
            margin: 0;
            gap: 8px;
        }
        .checkbox-input-container {
            display: inline-flex;
            flex-direction: row;
            align-items: center;
            gap: 8px;
            padding-top: 5px;
        }
        .checkbox-input-container input {
            margin: 0;
        }
    ');
    
    // Enqueue admin JavaScript with jQuery dependency
    wp_add_inline_script('jquery', '
        jQuery(document).ready(function($) {
            window.togglePassword = function() {
                var input = $("#' . esc_js(AI_ANALYTICS_ACCESS_TOKEN) . '");
                var toggleText = $("#toggle-text");
                var toggleIcon = $(".dashicons", "#toggle-password-btn");
                
                if (input.attr("type") === "password") {
                    input.attr("type", "text");
                    toggleText.text("Hide");
                    toggleIcon.removeClass("dashicons-visibility").addClass("dashicons-hidden");
                } else {
                    input.attr("type", "password");
                    toggleText.text("Show");
                    toggleIcon.removeClass("dashicons-hidden").addClass("dashicons-visibility");
                }
            };
        });
    ');
}

add_action('admin_enqueue_scripts', 'ai_analytics_admin_enqueue_scripts');

// Settings Page

function ai_analytics_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'hall-ai-analytics'));
    }
    
    // Handle form submission
    if (isset($_POST['submit'])) {
        // Verify nonce
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'ai_analytics_settings_nonce')) {
            wp_die(esc_html__('Security check failed. Please try again.', 'hall-ai-analytics'));
        }
        
        // Check user capabilities again
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to save settings.', 'hall-ai-analytics'));
        }
        
        // Sanitize and save access token
        if (isset($_POST[AI_ANALYTICS_ACCESS_TOKEN])) {
            $access_token = ai_analytics_sanitize_access_token(sanitize_text_field(wp_unslash($_POST[AI_ANALYTICS_ACCESS_TOKEN])));
            // Only update if a new token is provided (not empty)
            if (!empty($access_token)) {
                update_option(AI_ANALYTICS_ACCESS_TOKEN, $access_token);
            }
        }
        
        // Sanitize and save analytics enabled setting
        $analytics_enabled = isset($_POST[AI_ANALYTICS_ENABLED]) ? '1' : '0';
        update_option(AI_ANALYTICS_ENABLED, $analytics_enabled);
        
        // Show success message
        add_settings_error(
            'ai_analytics_messages',
            'ai_analytics_message',
            esc_html__('Settings saved successfully.', 'hall-ai-analytics'),
            'updated'
        );
    }
    
    // Show any settings errors
    settings_errors('ai_analytics_messages');
    
    ?>
    <div class="header">
        <div class="text">
            <a href="https://usehall.com/?utm_source=wordpress_plugin" target="_blank" rel="noopener noreferrer" style="width: fit-content;">
                <img src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'hall-logo.svg'); ?>" alt="<?php echo esc_attr__('Hall logo', 'hall-ai-analytics'); ?>" class="logo" width="84" height="28" />
            </a>
            <div class="headline"><?php echo esc_html__('Track agent activity and referrals from AI', 'hall-ai-analytics'); ?></div>
            <p class="lead"><?php echo esc_html__('Measure and understand how AI agents and assistants are accessing your WordPress site. Track referrals and clicks from conversational AI platforms like ChatGPT.', 'hall-ai-analytics'); ?></p>
            <div style="display: flex; flex-direction: row; align-items: center; gap: 16px;">
                <a href="https://usehall.com/ai-agent-analytics?utm_source=wordpress_plugin" target="_blank" rel="noopener noreferrer" class="button button-primary">
                   <?php echo esc_html__('See how it works', 'hall-ai-analytics'); ?>
                </a>
                <a href="https://app.usehall.com/" target="_blank" rel="noopener noreferrer" class="button">
                    <span><?php echo esc_html__('Log in to analytics dashboard', 'hall-ai-analytics'); ?></span>
                    <span class="dashicons dashicons-external"></span>
                </a>
            </div>
        </div>
        <img src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'plugin-ai-analytics.png'); ?>" alt="<?php echo esc_attr__('AI analytics screenshot', 'hall-ai-analytics'); ?>" class="screenshot" />
    </div>
    <div class="container wrap">
        <div class="page-heading">
            <h1><?php echo esc_html__('AI Analytics Settings', 'hall-ai-analytics'); ?></h1>
            <a href="https://docs.usehall.com/?utm_source=wordpress_plugin" target="_blank" rel="noopener noreferrer" class="button">
                <span><?php echo esc_html__('View the documentation', 'hall-ai-analytics'); ?></span>
                <span class="dashicons dashicons-external"></span>
            </a>
        </div>
        <form method="post" action="" class="ai-analytics-form">
            <?php wp_nonce_field('ai_analytics_settings_nonce'); ?>
            <div class="step-section">
                <h2><?php echo esc_html__('Step 1: Create an account and set up your domain', 'hall-ai-analytics'); ?></h2>
                <p><a href="https://usehall.com/ai-agent-analytics?utm_source=wordpress_plugin" target="_blank" rel="noopener noreferrer"><?php echo esc_html__('Sign up for a free account', 'hall-ai-analytics'); ?></a> <?php echo esc_html__('to get started.', 'hall-ai-analytics'); ?>
                <?php echo esc_html__('Then, navigate to your domain or click', 'hall-ai-analytics'); ?> <strong><?php echo esc_html__('New domain', 'hall-ai-analytics'); ?></strong> <?php echo esc_html__('in the navigation sidebar and add your domain.', 'hall-ai-analytics'); ?></p> 
            </div>
            <div class="step-section">
                <h2><?php echo esc_html__('Step 2: Create your API key', 'hall-ai-analytics'); ?></h2>
                <p><?php echo esc_html__('Then click', 'hall-ai-analytics'); ?> <strong><?php echo esc_html__('Domain settings', 'hall-ai-analytics'); ?></strong> <?php echo esc_html__('and follow the set up instructions to create an API key for your domain.', 'hall-ai-analytics'); ?></p>
                <p><?php echo esc_html__('Copy and paste your API key for your domain below.', 'hall-ai-analytics'); ?></p>
                <div class="password-input-container">
                    <?php 
                    $existing_token = get_option(AI_ANALYTICS_ACCESS_TOKEN, '');
                    $has_existing_token = !empty($existing_token);
                    ?>
                    <input type="password"
                        placeholder="<?php echo $has_existing_token ? esc_attr__('API key is set (leave blank to keep current)', 'hall-ai-analytics') : esc_attr__('Paste your API key here', 'hall-ai-analytics'); ?>"
                        id="<?php echo esc_attr(AI_ANALYTICS_ACCESS_TOKEN); ?>" 
                        name="<?php echo esc_attr(AI_ANALYTICS_ACCESS_TOKEN); ?>" 
                        value=""
                        maxlength="255"
                        pattern="[a-zA-Z0-9_-]*"
                        title="<?php echo esc_attr__('Only letters, numbers, hyphens, and underscores are allowed', 'hall-ai-analytics'); ?>"
                    />
                    <button type="button" class="button" id="toggle-password-btn" onclick="togglePassword()">
                        <span class="dashicons dashicons-visibility"></span>
                        <span id="toggle-text">Show</span>
                    </button>
                </div>
            </div>
            <div class="step-section">
                <h2><?php echo esc_html__('Step 3: Enable analytics', 'hall-ai-analytics'); ?></h2>
                <p><?php echo esc_html__('Enable tracking for your WordPress site. Data from this plugin will start to appear in your', 'hall-ai-analytics'); ?> <a href="https://app.usehall.com/" target="_blank" rel="noopener noreferrer"><?php echo esc_html__('domain dashboard', 'hall-ai-analytics'); ?></a> <?php echo esc_html__('within your Hall account after enabling.', 'hall-ai-analytics'); ?></p>
                    
                    
                    
                <div class="checkbox-input-container">
                    <input
                        type="checkbox"
                        id="<?php echo esc_attr(AI_ANALYTICS_ENABLED); ?>"
                        name="<?php echo esc_attr(AI_ANALYTICS_ENABLED); ?>"
                        <?php checked(get_option(AI_ANALYTICS_ENABLED, '1') == '1'); ?>
                        value="1"
                    />
                    <label for="<?php echo esc_attr(AI_ANALYTICS_ENABLED); ?>"><?php echo esc_html__('Enable Analytics', 'hall-ai-analytics'); ?></label><br>
                </div>
            </div>
            <?php submit_button(esc_html__('Save Changes', 'hall-ai-analytics')); ?>
        </form>
    </div>
    <?php
}
