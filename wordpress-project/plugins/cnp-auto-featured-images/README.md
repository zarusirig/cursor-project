# CNP Auto Featured Images

Automatically generate featured images for WordPress posts and pages using FAL AI's powerful image generation models.

## Features

✨ **Automatic Generation** - Generate featured images automatically when publishing posts
🎨 **Multiple AI Models** - Support for FLUX Schnell, FLUX Dev, FLUX Pro, and Stable Diffusion 3
📏 **Flexible Sizes** - Various aspect ratios including 16:9, 4:3, square, portrait, and landscape
🎭 **Custom Styles** - Professional news photos, illustrations, minimalist, photorealistic, and more
📝 **Prompt Templates** - Customizable templates with placeholders for title, excerpt, and categories
🔄 **Bulk Generation** - Generate images for multiple posts at once with progress tracking
📊 **Generation Logs** - Track all generation attempts with detailed success/error logs
⚡ **Fast Processing** - Batch processing with rate limiting to avoid API throttling
🔒 **Secure** - API keys stored securely, all inputs sanitized

## Requirements

- WordPress 6.0 or higher
- PHP 8.0 or higher
- FAL AI API key ([Get one here](https://fal.ai/dashboard/keys))

## Installation

### Manual Installation

1. Download the plugin files
2. Upload to `/wp-content/plugins/cnp-auto-featured-images/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to **Featured Images AI** → **Settings**
5. Enter your FAL AI API key
6. Click **Validate** to verify your key
7. Configure your preferred settings
8. Click **Save Settings**

### Via WordPress Admin

1. Go to **Plugins** → **Add New**
2. Click **Upload Plugin**
3. Choose the plugin ZIP file
4. Click **Install Now**
5. Activate the plugin
6. Follow steps 4-8 above

## Configuration

### API Key

1. Sign up for a FAL AI account at https://fal.ai
2. Navigate to https://fal.ai/dashboard/keys
3. Create a new API key
4. Copy the key (starts with `fal_`)
5. Paste it in the plugin settings
6. Click **Validate** to test the connection

### Image Model

Choose from available models:

- **FLUX Schnell (Fast)** - 4 steps, fast generation, good quality (Recommended)
- **FLUX Dev (Balanced)** - 12 steps, balanced speed and quality
- **FLUX Pro (Best Quality)** - 28 steps, highest quality, slower
- **Stable Diffusion 3 Medium** - Fast generation, good quality

### Image Size

Select the aspect ratio for generated images:

- **Landscape 16:9** (1024×576) - Recommended for featured images
- **Landscape 4:3** (1024×768)
- **Portrait 16:9** (576×1024)
- **Portrait 4:3** (768×1024)
- **Square HD** (1024×1024)
- **Square** (512×512)

### Image Style

Choose the visual style:

- Professional News Photo (default)
- Modern Digital Illustration
- Minimalist Design
- Photorealistic
- Abstract Art
- Editorial Photography
- Tech Concept Art
- Business Professional

### Prompt Template

Customize how prompts are generated using placeholders:

- `{title}` - Post title
- `{excerpt}` - Post excerpt or first 150 characters
- `{categories}` - Post categories (comma-separated)
- `{style}` - Selected image style

**Default template:**
```
A professional editorial image representing: {title}. {style}
```

**Example output:**
```
Safe for work, professional editorial: A professional editorial image representing: 10 Ways AI is Transforming Healthcare. professional news photo
```

### Auto-generate on Publish

Enable this option to automatically generate a featured image when:
- A post or page is published
- The post doesn't already have a featured image
- The API key is valid

## Usage

### Bulk Generate

1. Go to **Featured Images AI** → **Bulk Generate**
2. Select post type (Posts or Pages)
3. Click **Load Posts**
4. Select posts you want to generate images for
5. Click **Generate Featured Images for X Posts**
6. Wait for processing to complete
7. Check the progress log for results

### Single Post Generation

**Option 1: On Publish** (if auto-generate is enabled)
- Simply publish a post without a featured image
- The plugin will automatically generate one

**Option 2: From Bulk Generate**
- Load posts without featured images
- Select individual posts
- Generate

### View Generation Logs

1. Go to **Featured Images AI** → **Logs**
2. View history of all generation attempts
3. See success/failure status
4. Check error messages for failed attempts
5. View the prompt used for each generation

## API Costs

FAL AI charges per image generated. Pricing varies by model:

- **FLUX Schnell**: ~$0.003 per image (300 images per $1)
- **FLUX Dev**: ~$0.01 per image (100 images per $1)
- **FLUX Pro**: ~$0.05 per image (20 images per $1)
- **SD3 Medium**: ~$0.003 per image (300 images per $1)

Check current pricing at: https://fal.ai/pricing

## Troubleshooting

### API Key Invalid

**Problem:** Validation fails or shows "Invalid API key"

**Solutions:**
1. Verify the key is copied correctly (no extra spaces)
2. Ensure the key starts with `fal_`
3. Check your FAL AI account is active
4. Verify you have credits in your account

### Generation Fails

**Problem:** Images fail to generate

**Solutions:**
1. Check generation logs for specific error messages
2. Verify API key is still valid
3. Ensure you have sufficient credits
4. Check if prompt is too long (max ~500 characters)
5. Try a different model (FLUX Schnell is most reliable)

### Timeout Errors

**Problem:** Requests timeout before completion

**Solutions:**
1. Increase timeout in settings (default: 60 seconds)
2. Use faster models (FLUX Schnell instead of Pro)
3. Process fewer posts per batch in bulk generation

### Rate Limiting

**Problem:** "Rate limit exceeded" errors

**Solutions:**
1. Wait a few minutes before trying again
2. Reduce batch size in bulk generation
3. The plugin automatically adds 0.5s delay between requests
4. Upgrade your FAL AI plan for higher limits

### Images Not Appearing

**Problem:** Generation succeeds but image doesn't show

**Solutions:**
1. Check if image was uploaded to Media Library
2. Verify WordPress upload directory is writable
3. Check for PHP memory limits (increase if needed)
4. Look for errors in WordPress debug log

## Filters & Hooks

### Filters

Customize plugin behavior using WordPress filters:

```php
// Modify prompt before generation
add_filter('cnp_afi_generate_prompt', function($prompt, $post) {
    // Custom prompt logic
    return $prompt;
}, 10, 2);

// Modify image options
add_filter('cnp_afi_image_options', function($options, $post) {
    // Change model, size, etc.
    return $options;
}, 10, 2);

// Modify downloaded filename
add_filter('cnp_afi_image_filename', function($filename, $post_id) {
    return 'custom-' . $filename;
}, 10, 2);
```

### Actions

Hook into plugin events:

```php
// After successful generation
add_action('cnp_afi_image_generated', function($post_id, $attachment_id, $result) {
    // Custom logic after image is generated
}, 10, 3);

// After failed generation
add_action('cnp_afi_generation_failed', function($post_id, $error) {
    // Custom error handling
}, 10, 2);

// Before bulk generation starts
add_action('cnp_afi_bulk_generation_start', function($post_ids) {
    // Pre-processing logic
}, 10, 1);

// After bulk generation completes
add_action('cnp_afi_bulk_generation_complete', function($results) {
    // Post-processing logic
}, 10, 1);
```

## Database

The plugin creates one database table:

### `wp_cnp_afi_logs`

Stores generation attempt logs:

- `id` - Log entry ID
- `post_id` - WordPress post ID
- `post_type` - Post type (post, page, etc.)
- `prompt` - The prompt used for generation
- `status` - success, failed, or processing
- `image_url` - FAL AI image URL (if successful)
- `error_message` - Error details (if failed)
- `created_at` - Timestamp

## Performance Considerations

### Bulk Generation

- Processes 5 posts per batch by default
- 0.5 second delay between individual generations
- 1 second delay between batches
- Prevents timeout and rate limiting issues

### Caching

- No caching is performed (all generation is on-demand)
- Images are downloaded once and stored in Media Library
- Logs are stored in database for historical tracking

### Optimization Tips

1. **Use FLUX Schnell** for fastest generation
2. **Lower image sizes** generate faster (576×1024 vs 1024×1024)
3. **Shorter prompts** process faster
4. **Batch wisely** - Don't bulk generate 100+ posts at once
5. **Monitor credits** - Set up alerts in FAL AI dashboard

## Uninstallation

### Keep Data

If you want to keep generated images:
1. Deactivate plugin only
2. Images remain in Media Library
3. Settings and logs remain in database

### Clean Uninstall

To remove everything:
1. Go to **Plugins** → **Installed Plugins**
2. Deactivate **CNP Auto Featured Images**
3. Click **Delete**
4. Plugin files, settings, and logs will be removed
5. Generated images remain in Media Library (delete manually if needed)

## Support

For issues, questions, or feature requests:

- **Documentation:** Check this README and inline help text
- **FAL AI Docs:** https://fal.ai/models
- **WordPress Support:** Standard WordPress plugin support practices

## Credits

- **Developed by:** CNP News Development Team
- **Powered by:** FAL AI (https://fal.ai)
- **AI Models:** FLUX (Black Forest Labs), Stable Diffusion (Stability AI)

## License

GPL v2 or later

## Changelog

### 1.0.0 - 2025-11-13
- Initial release
- FLUX Schnell, Dev, Pro models
- Stable Diffusion 3 Medium
- Bulk generation with progress tracking
- Auto-generate on publish
- Generation logs
- Customizable prompts and styles
- Multiple image sizes and aspect ratios

## Privacy & Data

- **API Keys:** Stored in WordPress database, transmitted securely to FAL AI
- **Prompts:** Post titles and excerpts sent to FAL AI for image generation
- **Generated Images:** Hosted by FAL AI temporarily, then downloaded to your server
- **Logs:** Stored locally in WordPress database
- **No External Tracking:** Plugin doesn't send usage data anywhere except FAL AI API calls

## Roadmap

Planned features for future releases:

- [ ] Support for more AI models
- [ ] Image editing and regeneration from admin
- [ ] Scheduled generation via WP-Cron
- [ ] Integration with popular page builders
- [ ] Custom post type support configuration
- [ ] Advanced prompt engineering options
- [ ] A/B testing for image styles
- [ ] Analytics for image performance
- [ ] WebP format support
- [ ] Image compression options

---

**Version:** 1.0.0
**Last Updated:** November 13, 2025
**Tested up to:** WordPress 6.4
**Requires PHP:** 8.0+
