=== AI Analytics - Track AI Bots & Referrals ===
Contributors: usehall
Tags: AI, Agents, ChatGPT, Analytics, Attribution
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.0
Stable tag: 1.0.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Track website visits and user behavior with Hall Analytics integration for WordPress.

== Description ==

AI Analytics enables you to easily quantify referral traffic from conversational AI platforms like ChatGPT, and understand the visiting behavior of AI assistants, AI agents, and AI crawlers on your website.

This plugin connects your website to [Hall](https://usehall.com/ai-agent-analytics/?utm_source=wordpress_plugin_directory). It uses the Hall Analytics API to integrate with your WordPress site.

The plugin works by integrating with the [Analytics API](https://docs.usehall.com/?utm_source=wordpress_plugin_directory) to provide website analytics functionality. Page requests from your WordPress site will be forwarded to the API for analysis and display within your Hall workspace, allowing you to monitor visitor behavior and provide insights about AI agent traffic to your website.

You can review the [Analytics API documentation](https://docs.usehall.com/?utm_source=wordpress_plugin_directory), [Privacy Policy](https://usehall.com/legal/privacy-policy?utm_source=wordpress_plugin_directory), and [Customer Subscription Agreement](https://usehall.com/legal/customer-subscription-agreement?utm_source=wordpress_plugin_directory).

== Installation ==

### Automatic install

1. Log in to your WordPress admin panel
2. Navigate to **Plugins**, then **Add New**
3. Search for **AI Analytics**
4. Click **Install Now** and then **Activate**

## Manual download

1. Download the plugin zip file
2. Log in to your WordPress admin panel
3. Navigate to **Plugins**, then **Add New**, then **Upload Plugin**
4. Choose the downloaded zip file and click **Install Now**
5. Click **Activate Plugin**

## Get your API key

1. [Sign up for a free account](https://usehall.com/?utm_source=wordpress_plugin_directory) with Hall
2. Navigate to your domain or click **New domain** in the navigation sidebar and add your domain
3. Then click **Domain settings**, and follow the set up instructions to create an API key for your domain
4. Copy your API key.
5. Navigate to **Settings**, then **AI Analytics** in your WordPress site
6. Paste your API key, and **Enable Analytics**
7. Finally, click **Save Settings** to finish set up

Data from this plugin will start to appear in your domain dashboard within your Hall account after enabling.

== Frequently Asked Questions ==

= Do I need a Hall account to use this plugin? =

Yes. Creating an account is free and takes just a few seconds. Once integrated, you will view your analytics dashboard from [app.usehall.com](https://app.usehall.com).


= What data does this plugin collect? =

The plugin tracks basic visitor information including page views, referrers, user agents, and IP addresses to provide analytics insights.

= Will this slow down my website? =

No, the plugin is designed to be lightweight, unintrusive, and sends data asynchronously on the server-side without impacting your site performance.

== External services ==

This plugin connects to the Hall Analytics API at https://analytics.usehall.com to provide AI analytics insights for your website.

**What the service is used for:**
The Hall Analytics API processes website visitor data to identify and analyze traffic from AI agents, assistants, and conversational platforms like ChatGPT.

**What data is sent and when:**
- Page URL and request path
- HTTP request method (GET, POST, etc.)
- Basic request headers (Host, User-Agent, Referer)
- Visitor IP address
- Timestamp of the visit

Data is sent for each page visit on your website (excluding WordPress admin pages, login pages, and system requests). Data transmission occurs asynchronously and does not affect website performance. No data is sent if analytics is disabled in the plugin settings.

**Service provider information:**
This service is provided by Hall. You can review their:
- Terms of Service: https://usehall.com/legal/customer-subscription-agreement
- Privacy Policy: https://usehall.com/legal/privacy-policy
- API Documentation: https://docs.usehall.com/api-reference/visit

This data transmission is essential for the plugin's core functionality of providing AI analytics insights. You can disable this service by deactivating the plugin or disabling analytics in the plugin settings.

= What external services does this plugin use and what data is shared? =

See the "External services" section above for complete details.

== Changelog ==

= 1.0.0 =
* Initial release