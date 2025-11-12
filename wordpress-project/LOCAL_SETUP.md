# CNP News - Local Development Setup

**Quick start guide for running the WordPress site locally using Docker.**

---

## Prerequisites

Before starting, ensure you have these installed:

### macOS Requirements
```bash
# Install Docker Desktop (includes Docker and Docker Compose)
# Download from: https://www.docker.com/products/docker-desktop

# Verify installation
docker --version
docker-compose --version
```

If you don't have Docker Desktop:
```bash
# Install via Homebrew
brew install --cask docker

# Or with Docker CE:
brew install docker docker-compose
```

---

## Quick Start (5 Minutes)

### Step 1: Clone/Navigate to Project

```bash
cd /Users/surajgiri/Downloads/docs/wordpress-project
```

### Step 2: Start Docker Containers

```bash
# Start all services in background
docker-compose up -d

# Wait 30-60 seconds for MySQL to be ready
echo "Waiting for MySQL to be ready..."
sleep 60
```

### Step 3: Access WordPress

Open in your browser:
```
http://localhost
```

**WordPress Admin:**
```
http://localhost/wp-admin

Username: admin
Password: (set during first-run wizard)
```

### Step 4: Database Tools

**phpMyAdmin:**
```
http://localhost:8080

Server: mysql
Username: cnpnews
Password: cnpnews_password
```

**Adminer:**
```
http://localhost:8081

Server: mysql
Username: cnpnews
Password: cnpnews_password
Database: cnpnews_wp
```

---

## Full Setup with Theme

### Step 1: Verify Theme Location

Theme should be at:
```
wordpress-project/theme/cnp-news-theme/
```

### Step 2: Start Services

```bash
cd /Users/surajgiri/Downloads/docs/wordpress-project
docker-compose up -d
```

### Step 3: Wait for WordPress

```bash
# Check service status
docker-compose ps

# Wait for "healthy" status
docker-compose logs -f wordpress
```

Once you see:
```
WordPress setup complete!
```

### Step 4: Complete WordPress Installation

1. Open `http://localhost` in browser
2. Complete installation wizard:
   - Site Title: `CNP News`
   - Username: `admin`
   - Password: (create strong password)
   - Email: `admin@cnpnews.local`
3. Click "Install WordPress"

### Step 5: Activate Theme

1. Go to **Appearance > Themes**
2. Look for "CNP News Theme"
3. Click "Activate"

### Step 6: Install Plugins

```bash
# SSH into WordPress container
docker exec -it cnp-wordpress bash

# Install plugins via WP-CLI
wp plugin install wordfence --activate
wp plugin install wp-optimize --activate
wp plugin install seo-by-rankmath --activate
wp plugin install litespeed-cache --activate
wp plugin install webp-express --activate
wp plugin install host-analyticsjs-local --activate

# Set up WP-CLI
wp config get DB_NAME
wp core verify-checksums
```

### Step 7: Configure WordPress Settings

```bash
# From inside container
wp option update blogname "CNP News"
wp option update blogdescription "Clarity in Tech. Confidence in Business."
wp option update permalink_structure "/%postname%/"
wp option update timezone_string "Asia/Kathmandu"
wp option update date_format "F j, Y"
wp option update time_format "g:i a"
```

---

## Common Commands

### Start Services
```bash
docker-compose up -d
```

### Stop Services
```bash
docker-compose down
```

### View Logs
```bash
# WordPress logs
docker-compose logs -f wordpress

# MySQL logs
docker-compose logs -f mysql

# All logs
docker-compose logs -f
```

### Access WordPress Container Shell
```bash
docker exec -it cnp-wordpress bash
```

### Access MySQL Command Line
```bash
docker exec -it cnp-mysql mysql -u cnpnews -p cnpnews_wp
```

### Restart Services
```bash
docker-compose restart
```

### Remove Everything (Clean Slate)
```bash
# Stop containers and remove volumes (WARNING: deletes all data!)
docker-compose down -v

# Then start fresh
docker-compose up -d
```

---

## WP-CLI Commands

Once inside the WordPress container, you can use WP-CLI:

```bash
# Docker into WordPress
docker exec -it cnp-wordpress bash

# Then run WP-CLI commands:
wp post list
wp user list
wp plugin list
wp theme list
wp db export backup.sql
wp db import backup.sql
wp cache flush
```

---

## Creating Test Content

```bash
# From inside WordPress container
docker exec -it cnp-wordpress bash

# Create sample posts
wp post create \
  --post_type=post \
  --post_title="Sample Article" \
  --post_content="This is sample content" \
  --post_status=publish

# Create categories
wp term create category "Strategy & Analysis"
wp term create category "Artificial Intelligence"
wp term create category "Startups & Capital"

# Create test user
wp user create testuser test@cnpnews.local --user_pass=testpass123
```

---

## File Locations

Inside Docker:
```
/var/www/html/                          # WordPress root
/var/www/html/wp-content/themes/cnp-news-theme/  # Theme
/var/www/html/wp-content/plugins/       # Plugins
/var/www/html/wp-content/debug.log      # Debug log
```

On your Mac:
```
~/Downloads/docs/wordpress-project/     # Project root
~/Downloads/docs/wordpress-project/theme/cnp-news-theme/  # Theme
```

---

## Troubleshooting

### MySQL Connection Refused
```bash
# Wait longer for MySQL
sleep 30
docker-compose up -d

# Or manually restart
docker-compose restart mysql
```

### WordPress Not Loading
```bash
# Check logs
docker-compose logs wordpress

# Restart WordPress
docker-compose restart wordpress
```

### Database Already Exists
```bash
# Remove volumes and start fresh
docker-compose down -v
docker-compose up -d
```

### Port Already in Use
If port 80, 3306, 8080, or 8081 is in use:

Edit `docker-compose.yml`:
```yaml
# Change from:
ports:
  - "80:80"

# To:
ports:
  - "8000:80"  # Use 8000 instead
```

Then access at: `http://localhost:8000`

### Theme Not Showing Up
1. Ensure theme is at `wordpress-project/theme/cnp-news-theme/`
2. Refresh theme list in WordPress
3. Check file permissions: `chmod -R 755 theme/cnp-news-theme/`

---

## Performance Tips

### Improve Local Performance
```bash
# Enable WP caching
docker exec -it cnp-wordpress bash
wp option update wp_object_cache true

# Flush cache when needed
wp cache flush
```

### Monitor Resource Usage
```bash
# View Docker stats
docker stats
```

### Increase Memory Limit
If WordPress runs slowly, edit `docker-compose.yml`:

```yaml
wordpress:
  # ... other config ...
  environment:
    # Add this line
    WP_MEMORY_LIMIT: 512M
```

---

## Database Backup & Restore

### Export Database
```bash
# From container
docker exec cnp-mysql mysqldump -u cnpnews -pcnpnews_password cnpnews_wp > backup.sql

# From Mac terminal
docker exec cnp-mysql mysqldump -u cnpnews -pcnpnews_password cnpnews_wp > ~/Downloads/cnp-backup.sql
```

### Import Database
```bash
# From Mac terminal
docker exec -i cnp-mysql mysql -u cnpnews -pcnpnews_password cnpnews_wp < ~/Downloads/cnp-backup.sql
```

---

## Development Workflow

### 1. Start Work Day
```bash
cd ~/Downloads/docs/wordpress-project
docker-compose up -d
```

### 2. Edit Theme Files
Edit files in `theme/cnp-news-theme/` - they're synced live to WordPress.

### 3. View Changes
- Edit theme files on your Mac
- Hard refresh browser (Cmd+Shift+R)
- Changes appear instantly

### 4. Test Features
- Create test posts
- Test different page types
- Check admin functionality
- Test plugins

### 5. End Work Day
```bash
# Stop containers (keeps data)
docker-compose down

# Or remove everything
docker-compose down -v
```

---

## Next Steps

1. ✅ Run `docker-compose up -d`
2. ✅ Complete WordPress setup wizard
3. ✅ Activate CNP News theme
4. ✅ Install plugins
5. ✅ Create sample content
6. ✅ Test theme features
7. ✅ Configure settings
8. ✅ Run Lighthouse audit
9. ✅ Check analytics integration
10. ✅ Review in browser

---

## Support

For Docker issues:
- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Reference](https://docs.docker.com/compose/compose-file/)

For WordPress issues:
- [WordPress Codex](https://codex.wordpress.org/)
- [WP-CLI Documentation](https://developer.wordpress.org/cli/commands/)

---

**Happy Local Development! 🚀**
