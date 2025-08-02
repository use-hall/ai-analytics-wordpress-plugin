# WordPress Plugin Deployment Setup

This repository is configured to automatically deploy to WordPress.org SVN repository using GitHub Actions.

## Required GitHub Secrets

You need to add the following secrets to your GitHub repository:

1. Go to your GitHub repository
2. Click on Settings → Secrets and variables → Actions
3. Add these repository secrets:

### SVN_USERNAME
Your WordPress.org username (the one you use to login to wordpress.org)

### SVN_PASSWORD
Your WordPress.org password (the one you use to login to wordpress.org)

## Deployment Process

1. **Development**: Make your changes and commit to the main branch
2. **Release**: Create a new release on GitHub with a version tag (e.g., `v1.0.1`)
3. **Automatic Deployment**: The GitHub Action will automatically:
   - Run the build process
   - Deploy to WordPress.org SVN repository
   - Generate a downloadable ZIP file

## Plugin Slug

The plugin slug is set to: `hall-ai-analytics`

Make sure this matches your WordPress.org plugin directory name.

## Build Process

The workflow runs:
- `npm install`
- `npm run build`

Make sure these commands work in your local environment before releasing.

## File Structure

The deployment includes all files except:
- `.git/`
- `.github/`
- `node_modules/`
- Development files (as configured in `.distignore`)

## Troubleshooting

- Ensure your WordPress.org credentials are correct
- Check that the plugin slug matches your WordPress.org plugin directory
- Verify the version number in your main plugin file matches the release tag
- Review the GitHub Actions logs for any deployment errors