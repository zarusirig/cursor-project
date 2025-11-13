# CNP Auto Featured Images Plugin - Setup Guide

**Plugin:** CNP Auto Featured Images
**Version:** 1.0.0
**Created:** November 13, 2025
**Purpose:** Automatically generate featured images for posts/pages using FAL AI

---

## 🚀 Quick Start

### Step 1: Get FAL AI API Key

1. Visit https://fal.ai
2. Sign up for an account (free tier available)
3. Navigate to https://fal.ai/dashboard/keys
4. Click **Create New Key**
5. Copy the API key (starts with `fal_`)

### Step 2: Install Plugin

The plugin is already in your WordPress project at:
```
/wordpress-project/plugins/cnp-auto-featured-images/
```

**To activate:**
1. Copy plugin folder to your WordPress installation:
   ```bash
   cp -r wordpress-project/plugins/cnp-auto-featured-images /path/to/wordpress/wp-content/plugins/
   ```

2. In WordPress admin:
   - Go to **Plugins** → **Installed Plugins**
   - Find **CNP Auto Featured Images**
   - Click **Activate**

### Step 3: Configure Plugin

1. Go to **Featured Images AI** → **Settings** (in WordPress admin sidebar)
2. Paste your FAL AI API key
3. Click **Validate** button
4. Wait for green checkmark ✓
5. Configure preferences (or use defaults):
   - **Image Model:** FLUX Schnell (recommended)
   - **Image Size:** Landscape 16:9 (recommended)
   - **Image Style:** Professional News Photo
   - **Prompt Template:** (leave default)
   - **Auto-generate on Publish:** Check if you want automatic generation
6. Click **Save Settings**

---

## 📋 Features Overview

### 1. Dashboard/Settings Page

**Location:** Featured Images AI → Settings

**What you can do:**
- Enter and validate FAL AI API key
- Choose AI model (speed vs quality tradeoff)
- Select image size/aspect ratio
- Pick visual style
- Customize prompt template
- Enable auto-generation on publish
- View statistics (total generated, last generated time)

### 2. Bulk Generate Page

**Location:** Featured Images AI → Bulk Generate

**What you can do:**
- Load all posts without featured images
- Select post type (Posts or Pages)
- Choose which posts to generate for
- Select all / deselect all
- Start bulk generation process
- Monitor real-time progress
- View detailed log of successes/failures

**Process:**
1. Select post type
2. Click "Load Posts"
3. Review posts without images
4. Check boxes for posts you want
5. Click "Generate Featured Images for X Posts"
6. Watch progress bar and log
7. Wait for completion
8. Reload page to see results

### 3. Generation Logs

**Location:** Featured Images AI → Logs

**What you can see:**
- History of all generation attempts
- Post title and link
- Status (success, failed, processing)
- Prompt used
- Error messages (if failed)
- Timestamp
- Pagination for many logs

---

## 🎨 Configuration Options Explained

### Image Models

| Model | Speed | Quality | Cost/Image | Use Case |
|-------|-------|---------|-----------|----------|
| FLUX Schnell | ⚡⚡⚡ Fast | ⭐⭐⭐ Good | $0.003 | **Recommended** - Best balance |
| FLUX Dev | ⚡⚡ Medium | ⭐⭐⭐⭐ Better | $0.01 | Higher quality needed |
| FLUX Pro | ⚡ Slow | ⭐⭐⭐⭐⭐ Best | $0.05 | Premium content |
| SD3 Medium | ⚡⚡⚡ Fast | ⭐⭐⭐ Good | $0.003 | Alternative to Schnell |

**Recommendation:** Use **FLUX Schnell** for news sites - fast, cheap, good quality

### Image Sizes

| Size | Dimensions | Best For |
|------|------------|----------|
| Landscape 16:9 | 1024×576 | **Featured images** ✓ |
| Landscape 4:3 | 1024×768 | Traditional layouts |
| Portrait 16:9 | 576×1024 | Mobile-first designs |
| Portrait 4:3 | 768×1024 | Sidebar features |
| Square HD | 1024×1024 | Social media |
| Square | 512×512 | Thumbnails |

**Recommendation:** Use **Landscape 16:9** for WordPress featured images

### Image Styles

| Style | Description | Best For |
|-------|-------------|----------|
| Professional News Photo | Clean, editorial style | **News sites** ✓ |
| Modern Digital Illustration | Contemporary graphics | Tech blogs |
| Minimalist Design | Simple, clean | Minimalist sites |
| Photorealistic | Real-looking photos | Lifestyle blogs |
| Abstract Art | Artistic, conceptual | Creative sites |
| Editorial Photography | Magazine quality | Publishing |
| Tech Concept Art | Futuristic, tech | Tech news |
| Business Professional | Corporate aesthetic | Business sites |

**Recommendation:** Use **Professional News Photo** for CNP News

### Prompt Template

**Default:**
```
A professional editorial image representing: {title}. {style}
```

**Available Placeholders:**
- `{title}` - Post title
- `{excerpt}` - Post excerpt or first 150 characters
- `{categories}` - Post categories (comma-separated)
- `{style}` - Your selected image style

**Example Custom Templates:**

For tech news:
```
Modern tech illustration showing: {title}. {style}. Categories: {categories}
```

For business news:
```
Professional business image: {title}. Context: {excerpt}. {style}
```

For minimalist style:
```
Clean minimal design representing: {title}
```

**Note:** The plugin automatically prepends "Safe for work, professional editorial:" to all prompts for content safety.

---

## 💡 Usage Examples

### Example 1: Bulk Generate for Existing Posts

**Scenario:** You have 50 posts without featured images

**Steps:**
1. Go to **Featured Images AI** → **Bulk Generate**
2. Select **Posts** from dropdown
3. Click **Load Posts**
4. You see: "Found 50 posts without featured images"
5. Click **Select All** (or manually check desired posts)
6. Click **Generate Featured Images for 50 Posts**
7. Confirm the popup
8. Watch progress: "Processed 5 of 50 posts..."
9. Wait ~5 minutes (50 posts × ~5 seconds each + delays)
10. See completion: "✓ Successfully generated 48 images, 2 failed"
11. Click **Reload Page**
12. Check your posts - featured images are now set!

**Time estimate:** ~5-10 minutes for 50 posts

### Example 2: Auto-Generate on New Posts

**Scenario:** You want every new post to get an image automatically

**Setup:**
1. Go to **Featured Images AI** → **Settings**
2. Check **Auto-generate on Publish**
3. Click **Save Settings**

**Usage:**
1. Write a new post
2. Don't set featured image
3. Click **Publish**
4. Wait ~5 seconds
5. Featured image appears automatically!
6. If generation fails, you'll see an error in logs

### Example 3: Generate for Single Post

**Scenario:** You want to generate for just one specific post

**Steps:**
1. Go to **Featured Images AI** → **Bulk Generate**
2. Click **Load Posts**
3. Find your post in the list
4. Check ONLY that post's checkbox
5. Click **Generate Featured Images for 1 Posts**
6. Wait for completion
7. Done!

### Example 4: Regenerate Failed Images

**Scenario:** Some images failed, you want to retry

**Steps:**
1. Go to **Featured Images AI** → **Logs**
2. Find posts with "Failed" status
3. Note the post IDs or titles
4. Go to **Bulk Generate**
5. Load posts and select the failed ones
6. Generate again
7. Check logs to verify success

---

## 🔧 Troubleshooting

### Issue: "Invalid API key"

**Causes:**
- Key copied incorrectly (extra spaces)
- Key doesn't start with `fal_`
- FAL AI account inactive
- No credits in account

**Solutions:**
1. Copy key again carefully
2. Verify it starts with `fal_`
3. Check FAL AI dashboard for account status
4. Add credits if balance is $0

### Issue: Bulk generation fails or times out

**Causes:**
- Too many posts at once
- Slow model (FLUX Pro)
- Network issues

**Solutions:**
1. Process fewer posts (25 at a time instead of 100)
2. Switch to FLUX Schnell
3. Increase timeout in settings (try 90 seconds)
4. Check internet connection

### Issue: Images not appearing

**Causes:**
- WordPress upload directory not writable
- PHP memory limit too low
- Generation succeeded but attachment failed

**Solutions:**
1. Check file permissions on `/wp-content/uploads/`
2. Increase PHP memory limit in wp-config.php:
   ```php
   define('WP_MEMORY_LIMIT', '256M');
   ```
3. Check logs for specific error message
4. Verify image exists in Media Library

### Issue: "Rate limit exceeded"

**Causes:**
- Too many requests too quickly
- FAL AI account limits reached

**Solutions:**
1. Wait 5-10 minutes
2. Reduce batch size
3. Upgrade FAL AI plan for higher limits
4. Plugin adds 0.5s delay automatically, but you can manually slow down

### Issue: Generation successful but poor quality

**Causes:**
- Prompt too generic
- Wrong model
- Wrong style

**Solutions:**
1. Customize prompt template with more detail
2. Try different model (FLUX Dev or Pro)
3. Experiment with different styles
4. Use {excerpt} placeholder for more context

---

## 💰 Cost Management

### Estimated Costs

**FLUX Schnell (Recommended):**
- $0.003 per image
- 100 posts = $0.30
- 1,000 posts = $3.00
- 10,000 posts = $30.00

**FLUX Dev:**
- $0.01 per image
- 100 posts = $1.00
- 1,000 posts = $10.00

**FLUX Pro:**
- $0.05 per image
- 100 posts = $5.00
- 1,000 posts = $50.00

### Budget Tips

1. **Use Schnell for bulk** - 10x cheaper than Pro
2. **Generate selectively** - Only for important posts
3. **Set up alerts** in FAL AI dashboard
4. **Monitor logs** - Avoid regenerating same posts
5. **Test prompts first** - Generate 1-2 images before bulk

### Free Tier

FAL AI offers free credits to start:
- Usually $1-5 free credits
- Enough for 300-1,500 images (with Schnell)
- Perfect for testing and small sites

---

## 📊 Best Practices

### For News Sites (like CNP News)

**Recommended Settings:**
```
Model: FLUX Schnell
Size: Landscape 16:9 (1024×576)
Style: Professional News Photo
Auto-generate: Enabled
Prompt: A professional editorial image representing: {title}. {style}
```

**Workflow:**
1. Write and publish posts as normal
2. Featured images generate automatically
3. Review logs weekly for failures
4. Regenerate any failures
5. Monitor FAL AI credit usage

### For Blog Sites

**Recommended Settings:**
```
Model: FLUX Schnell or Dev
Size: Landscape 16:9 or Square HD
Style: Modern Digital Illustration or Photorealistic
Auto-generate: Enabled or Manual
Prompt: Custom based on your niche
```

### For E-commerce/Business

**Recommended Settings:**
```
Model: FLUX Dev or Pro
Size: Square HD (1024×1024)
Style: Business Professional or Minimalist Design
Auto-generate: Manual (more control)
Prompt: Professional {style} image for: {title}. Context: {excerpt}
```

---

## 🔐 Security & Privacy

### What Data is Sent to FAL AI?

- Post title
- Post excerpt (if used in prompt)
- Category names (if used in prompt)
- Your prompt template text

### What Data is NOT Sent?

- Full post content
- Author information
- User data
- Site credentials
- Other post metadata

### API Key Security

- Stored in WordPress database (wp_options)
- Not exposed in frontend code
- Transmitted over HTTPS only
- Can be deleted by deactivating plugin

### Generated Images

- Initially hosted by FAL AI (temporarily)
- Downloaded to your WordPress server
- Stored in /wp-content/uploads/
- You own the generated images

---

## 🎓 Advanced Usage

### Custom Hooks for Developers

**Modify prompts programmatically:**
```php
add_filter('cnp_afi_generate_prompt', function($prompt, $post) {
    // Add custom prefix for specific categories
    $categories = get_the_category($post->ID);
    if (!empty($categories)) {
        $cat_name = $categories[0]->name;
        $prompt = "Category: {$cat_name}. " . $prompt;
    }
    return $prompt;
}, 10, 2);
```

**Change model based on post:**
```php
add_filter('cnp_afi_image_options', function($options, $post) {
    // Use Pro model for featured posts
    if (has_tag('featured', $post)) {
        $options['model'] = 'fal-ai/flux-pro';
    }
    return $options;
}, 10, 2);
```

**Track generations in Google Analytics:**
```php
add_action('cnp_afi_image_generated', function($post_id, $attachment_id, $result) {
    // Send event to GA4
    // Your tracking code here
}, 10, 3);
```

### Database Queries

**Find posts without featured images:**
```sql
SELECT p.ID, p.post_title
FROM wp_posts p
LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
WHERE p.post_type = 'post'
AND p.post_status = 'publish'
AND pm.meta_id IS NULL
LIMIT 100;
```

**Check generation success rate:**
```sql
SELECT
    status,
    COUNT(*) as count,
    ROUND(COUNT(*) * 100.0 / SUM(COUNT(*)) OVER(), 2) as percentage
FROM wp_cnp_afi_logs
GROUP BY status;
```

---

## 📦 Plugin Files Structure

```
cnp-auto-featured-images/
├── cnp-auto-featured-images.php      # Main plugin file
├── README.md                           # Full documentation
├── includes/
│   ├── class-fal-ai-api.php           # FAL AI API integration
│   ├── class-image-generator.php      # Image generation logic
│   ├── class-admin-dashboard.php      # Admin UI rendering
│   └── class-ajax-handlers.php        # AJAX endpoint handlers
└── assets/
    ├── css/
    │   └── admin.css                   # Admin styling
    └── js/
        └── admin.js                    # Admin functionality
```

**Total:** 8 files, ~2,500 lines of code

---

## 🚀 Next Steps

### 1. Immediate (Right Now)

- [ ] Get FAL AI API key
- [ ] Activate plugin
- [ ] Validate API key
- [ ] Configure settings
- [ ] Test with 1-2 posts

### 2. Short Term (This Week)

- [ ] Bulk generate for existing posts without images
- [ ] Review generation logs
- [ ] Adjust prompt template if needed
- [ ] Monitor FAL AI credit usage
- [ ] Enable auto-generate if desired

### 3. Long Term (Ongoing)

- [ ] Review logs weekly
- [ ] Regenerate failures
- [ ] Monitor costs
- [ ] Optimize prompts for better results
- [ ] Consider upgrading FAL AI plan if needed

---

## ✅ Pre-Launch Checklist

Before using in production:

- [ ] FAL AI API key validated
- [ ] Settings configured (model, size, style)
- [ ] Tested on 1-2 posts successfully
- [ ] Reviewed generated image quality
- [ ] Checked FAL AI credit balance
- [ ] Set up billing alerts (if needed)
- [ ] Understood cost per image
- [ ] Reviewed logs functionality
- [ ] Tested bulk generation with small batch (5-10 posts)
- [ ] Confirmed images appear in Media Library
- [ ] Verified images display correctly on frontend

---

## 📞 Support Resources

- **Plugin README:** `/wordpress-project/plugins/cnp-auto-featured-images/README.md`
- **FAL AI Docs:** https://fal.ai/models
- **FAL AI Dashboard:** https://fal.ai/dashboard
- **FAL AI Pricing:** https://fal.ai/pricing
- **FAL AI Support:** https://fal.ai/support
- **WordPress Codex:** https://codex.wordpress.org/

---

**Happy generating! 🎨**

**Plugin Version:** 1.0.0
**Guide Created:** November 13, 2025
**Last Updated:** November 13, 2025
