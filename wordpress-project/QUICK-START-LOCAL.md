# 🚀 CNP News - Local WordPress (Quick Start)

**Get the website running locally in 2 minutes!**

---

## One-Line Setup

Copy and paste this into your terminal:

```bash
cd /Users/surajgiri/Downloads/docs/wordpress-project && docker-compose up -d && echo "Starting in 60 seconds..." && sleep 60 && echo "✅ Visit http://localhost"
```

---

## Manual Setup (Step by Step)

### 1. Navigate to Project
```bash
cd /Users/surajgiri/Downloads/docs/wordpress-project
```

### 2. Start Docker Containers
```bash
docker-compose up -d
```

Output:
```
[+] Running 5/5
 ✓ Network cnp-network  Created
 ✓ Container cnp-mysql  Started
 ✓ Container cnp-wordpress  Started
 ✓ Container cnp-phpmyadmin  Started
 ✓ Container cnp-adminer  Started
```

### 3. Wait 60 Seconds for Services to Start
```bash
sleep 60
```

### 4. Open WordPress
```
http://localhost
```

---

## Complete WordPress Installation

### Step 1: WordPress Setup Wizard

Open `http://localhost` in your browser

1. Select Language: **English**
2. Click **Continue**
3. Fill in:
   - **Site Title:** CNP News
   - **Username:** admin
   - **Password:** (create one)
   - **Email:** admin@cnpnews.local
4. Click **Install WordPress**

### Step 2: Log In

```
http://localhost/wp-admin

Username: admin
Password: (what you created above)
```

### Step 3: Activate Theme

1. Go to **Appearance → Themes**
2. Find **CNP News Theme**
3. Click **Activate**

### Step 4: View Website

Visit `http://localhost` to see your site with the CNP News theme!

---

## Quick Access URLs

| Service | URL | Login |
|---------|-----|-------|
| **WordPress** | http://localhost | N/A |
| **WordPress Admin** | http://localhost/wp-admin | admin / (your password) |
| **phpMyAdmin** | http://localhost:8080 | cnpnews / cnpnews_password |
| **Adminer** | http://localhost:8081 | MySQL credentials above |

---

## Database Credentials

```
Host:     localhost (or mysql if inside container)
Database: cnpnews_wp
User:     cnpnews
Password: cnpnews_password
```

---

## Common Commands

### Check Status
```bash
docker-compose ps
```

### View Logs
```bash
docker-compose logs -f wordpress
```

### Stop All Services
```bash
docker-compose down
```

### Access WordPress Shell
```bash
docker exec -it cnp-wordpress bash
```

### Restart Everything
```bash
docker-compose restart
```

### Fresh Start (Warning: Deletes All Data)
```bash
docker-compose down -v
docker-compose up -d
sleep 60
# Then access http://localhost
```

---

## Creating Test Content

Once WordPress is set up, create some test posts:

```bash
# Enter WordPress container
docker exec -it cnp-wordpress bash

# Create a post
wp post create \
  --post_title="My First Article" \
  --post_content="This is test content for CNP News." \
  --post_status=publish \
  --post_author=1

# Create categories
wp term create category "AI & Startups"
wp term create category "Business News"
wp term create category "Tech Reviews"

# List posts
wp post list

# Exit container
exit
```

---

## Editing Theme Files

The theme files are automatically synced:

```
On Your Mac:
📁 /Users/surajgiri/Downloads/docs/wordpress-project/theme/cnp-news-theme/

In WordPress (Docker):
📁 /var/www/html/wp-content/themes/cnp-news-theme/
```

**Edit any file in the theme folder and refresh your browser to see changes!**

---

## Performance Monitoring

### View Docker Resource Usage
```bash
docker stats
```

### Check WordPress Error Log
```bash
docker exec cnp-wordpress tail -f /var/www/html/wp-content/debug.log
```

### Test Performance with Lighthouse

1. Open `http://localhost` in Chrome
2. Press `F12` (Dev Tools)
3. Go to **Lighthouse tab**
4. Click **Analyze**
5. Wait for report

---

## Troubleshooting

### WordPress Shows Blank Page
```bash
# Restart WordPress container
docker-compose restart wordpress

# Wait 10 seconds
sleep 10

# Try again in browser
```

### MySQL Connection Error
```bash
# Restart MySQL
docker-compose restart mysql

# Wait 30 seconds
sleep 30

# Restart WordPress
docker-compose restart wordpress
```

### Port 80 Already in Use
Edit `docker-compose.yml` and change:
```yaml
# FROM:
ports:
  - "80:80"

# TO:
ports:
  - "8000:80"

# Then access: http://localhost:8000
```

### Can't Find Theme
1. Theme should be at: `wordpress-project/theme/cnp-news-theme/`
2. Verify `style.css` exists in that folder
3. Refresh WordPress Themes page
4. If still missing, restart: `docker-compose restart wordpress`

---

## Workflow: Edit → Test → Repeat

```bash
# 1. Open terminal
cd /Users/surajgiri/Downloads/docs/wordpress-project

# 2. Start services
docker-compose up -d

# 3. Open browser
# http://localhost

# 4. Edit theme files (in VS Code, etc)
# ~/Downloads/docs/wordpress-project/theme/cnp-news-theme/

# 5. Hard refresh browser (Cmd+Shift+R)

# 6. See changes instantly!

# 7. Repeat steps 4-6

# 8. When done:
docker-compose down
```

---

## Next Steps

✅ Run setup  
✅ Visit http://localhost  
✅ Complete installation  
✅ Activate theme  
✅ Create test posts  
✅ Edit theme files  
✅ Test in browser  
✅ Check admin panel  
✅ Install plugins  
✅ Configure settings  

---

## Full Documentation

For detailed information, see:
- `LOCAL_SETUP.md` - Complete setup guide
- `docker-compose.yml` - Docker configuration
- `theme/cnp-news-theme/` - Theme files
- `plugins/plugin-list.md` - Plugin recommendations

---

## Support

**Docker issues?**
```bash
docker-compose logs -f
```

**WordPress issues?**
```bash
docker exec -it cnp-wordpress bash
wp site health
```

**Still stuck?**
See `LOCAL_SETUP.md` for troubleshooting section.

---

**Ready? Run this now:**

```bash
cd /Users/surajgiri/Downloads/docs/wordpress-project && docker-compose up -d && sleep 60 && echo "✅ Visit http://localhost"
```

🚀 **Happy coding!**
