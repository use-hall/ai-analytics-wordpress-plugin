=== AI Analytics - Track AI Bots & Referrals ===
Contributors: usehall
Tags: answer engine optimization, ai optimization, ai search, cralwers, bots
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.0
Stable tag: 1.0.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Track AI agents and assistants accessing your WordPress site. Monitor referrals and clicks from ChatGPT and conversational AI platforms.

== Description ==

AI Analytics enables you to measure AI assistant, AI agents, and AI crawler activity on your website, as well as referral traffic from popular AI platforms like ChatGPT, Perplexity, Gemini, Claude, and more.

After setting up the plugin, you'll be able to understand:

- **How AI crawlers access your website** to train and improve their foundational models like GPT-4 or Claude Sonnet
- **How AI assistants and AI agents are using content from your website** to display to users in response to questions asked on platforms like ChatGPT
- **How search crawlers are browsing pages on your website** to build and improve search engine indexes for both AI and traditional search
- **Which AI platforms users are clicking from** and being referred to your website

[Hall](https://usehall.com/ai-agent-analytics/?utm_source=wordpress_plugin_directory) provides analytics dashboards that display data such as:

- **Pages viewed by bot and crawler type**, such as AI assistants, AI training, or search crawlers
- **Analysis of the AI companies accessing your website**, such as OpenAI, Anthropic, or Google
- **The most popular pages on your site** that are visited by AI

This plugin connects your WordPress website to [Hall](https://usehall.com/ai-agent-analytics/?utm_source=wordpress_plugin_directory), and integrates to forward requests from your WordPress website to the [Hall Analytics API](https://docs.usehall.com/api-reference/visit).

== Installation ==

### Automatic install

1. Log in to your WordPress admin panel
2. Navigate to **Plugins**, then **Add New**
3. Search for **AI Analytics**
4. Click **Install Now** and then **Activate**

### Manual download

1. Download the plugin zip file
2. Log in to your WordPress admin panel
3. Navigate to **Plugins**, then **Add New**, then **Upload Plugin**
4. Choose the downloaded zip file and click **Install Now**
5. Click **Activate Plugin**

### Get your API key

1. [Sign up for a free account](https://usehall.com/?utm_source=wordpress_plugin_directory) with Hall
2. Navigate to your domain or click **New domain** in the navigation sidebar and add your domain
3. Then click **Domain settings**, and follow the set up instructions to create an API key for your domain
4. Copy your API key
5. Navigate to **Settings**, then **AI Analytics** in your WordPress site
6. Paste your API key, and **Enable Analytics**
7. Finally, click **Save Settings** to finish set up

Data from this plugin will start to appear in your domain dashboard within your Hall account after enabling.

== Frequently Asked Questions ==

= How does the plugin work? =

This plugin connects to the Hall Analytics API endpoint located at `https://analytics.usehall.com/visit` to provide AI analytics insights for your website.

The plugin works by forwarding each request to your WordPress site to the API endpoint, including:

- Page URL and request path
- HTTP request method (GET, POST, etc.)
- Basic request headers (Host, User-Agent, Referer)
- Visitor IP address
- Timestamp of the visit

Request to WordPress admin pages, login pages, and system requests are not sent, and data is forwarded asynchronously and does not affect website performance. No requests are forwarded if analytics is disabled in the plugin settings.

You can review the API documentation at [https://docs.usehall.com/api-reference/visit](https://docs.usehall.com/api-reference/visit?utm_source=wordpress_plugin_directory). 

This information is then processed and analyzed to provide analytics dashboards of AI activity and referrals to your website.

You can review the [Terms of Service](https://usehall.com/legal/customer-subscription-agreement?utm_source=wordpress_plugin_directory) and [Privacy Policy](https://usehall.com/legal/privacy-policy?utm_source=wordpress_plugin_directory) for Hall.

= Do I need a Hall account to use this plugin? =

Yes. Creating an account is free and takes just a few seconds. You can [sign up here for free](https://auth.usehall.com/sign-up). Once integrated, you will view your analytics dashboard from [app.usehall.com](https://app.usehall.com).

= What is Hall? = 

[Hall](https://usehall.com/?utm_source=wordpress_plugin_directory) helps understand how businesses and websites appear in conversations across AI platforms like ChatGPT, Perplexity, and Gemini. With Hall, you can monitor the visibility of your brand, business, or website in AI conversations and understand how AI platforms are accessing and surfacing content from your website in AI conversations.


= Will this slow down my website? =

No, the plugin is designed to be lightweight, unintrusive, and sends data asynchronously on the server-side without impacting your site performance.

== Changelog ==

= 1.0.0 =
* Initial release