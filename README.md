# AI Analytics WordPress Plugin

## 🚀 Quick Start

1. **Install from WordPress.org**: Search "AI Analytics" in your WordPress admin
2. **Get your API key**: [Sign up for Hall](https://usehall.com/ai-agent-analytics) (free account)
3. **Configure**: Add your API key in Settings → AI Analytics
4. **Track**: View insights in your [Hall dashboard](https://app.usehall.com)

## 🎯 What It Does

- **Detects AI agents** visiting your WordPress site
- **Tracks referrals** from ChatGPT and other AI platforms  
- **Monitors behavior** of AI assistants and crawlers
- **Provides insights** through Hall Analytics dashboard

### Automated Deployment

This repository uses GitHub Actions to automatically deploy to WordPress.org SVN:

1. **Create a release** on GitHub (e.g., `v1.0.1`)
2. **GitHub Action runs** and deploys to WordPress.org
3. **Plugin updates** are available to users within hours

### Local Development

```bash
# Clone the repository
git clone https://github.com/use-hall/ai-analytics-wordpress-plugin.git

# Symlink to your WordPress plugins directory
ln -s /path/to/ai-analytics-wordpress-plugin /path/to/wordpress/wp-content/plugins/hall-ai-analytics
```

### Environment Variables

Set these secrets in your GitHub repository for automated deployment:

- `SVN_USERNAME`: Your WordPress.org username
- `SVN_PASSWORD`: Your WordPress.org SVN password

## 📊 Features

- **🤖 AI Agent Detection**: Identifies visits from AI platforms
- **📈 Traffic Analytics**: Measures referral patterns
- **🔒 Privacy Focused**: Server-side tracking, no cookies
- **⚡ Performance**: Asynchronous API calls, zero impact
- **🎨 Clean UI**: WordPress-native admin interface

## 🌐 External services

This plugin integrates with:

- **Hall Analytics API** (`https://analytics.usehall.com`)
- View [API documentation](https://docs.usehall.com/api-reference/visit)
- Review [privacy policy](https://usehall.com/legal/privacy-policy)

---

Made with ❤️ by [Hall](https://usehall.com)