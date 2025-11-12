# 🔧 CNP News - Command Reference Card

**Copy-paste ready commands for local development**

---

## ⚡ Quick Commands

### Start Everything
```bash
cd /Users/surajgiri/Downloads/docs/wordpress-project && docker compose up -d
```

### Stop Everything
```bash
cd /Users/surajgiri/Downloads/docs/wordpress-project && docker compose down
```

### Check Status
```bash
cd /Users/surajgiri/Downloads/docs/wordpress-project && docker compose ps
```

### View Logs
```bash
cd /Users/surajgiri/Downloads/docs/wordpress-project && docker compose logs -f
```

---

## 🌐 Browser Access

| Service | URL | Port |
|---------|-----|------|
| WordPress | http://localhost | 80 |
| Admin | http://localhost/wp-admin | 80 |
| phpMyAdmin | http://localhost:8080 | 8080 |
| Adminer | http://localhost:8081 | 8081 |

---

## 🗄️ Database

### phpMyAdmin Web Access
```
URL: http://localhost:8080
User: cnpnews
Pass: cnpnews_password
```

### Command Line Access
```bash
docker exec -it cnp-mysql mysql -u cnpnews -p cnpnews_wp
# Password: cnpnews_password
```

### Export Database
```bash
docker exec cnp-mysql mysqldump -u cnpnews -pcnpnews_password cnpnews_wp > backup.sql
```

### Import Database
```bash
docker exec -i cnp-mysql mysql -u cnpnews -pcnpnews_password cnpnews_wp < backup.sql
```

---

## 📱 WordPress

### Access WordPress Shell
```bash
docker exec -it cnp-wordpress bash
```

### WP-CLI Commands (from inside container)
```bash
# List posts
wp post list

# Create post
wp post create --post_title="My Post" --post_status=publish

# Create category
wp term create category "Technology"

# List users
wp user list

# Create user
wp user create newuser email@example.com --user_pass=password123

# Flush cache
wp cache flush

# Health check
wp site health
```

### Quick WP-CLI Commands (from Mac)
```bash
docker exec -it cnp-wordpress wp post list
docker exec -it cnp-wordpress wp plugin list
docker exec -it cnp-wordpress wp theme list
docker exec -it cnp-wordpress wp user list
```

---

## 🎨 Theme Development

### Edit Theme Files
```
📁 /Users/surajgiri/Downloads/docs/wordpress-project/theme/cnp-news-theme/

Files:
- style.css              (Main styles)
- theme.json            (Design system)
- functions.php         (PHP functions)
- templates/            (Page templates)
- patterns/             (Block patterns)
- parts/                (Header, footer, etc)
- assets/js/            (JavaScript)
- assets/css/           (Additional styles)
- inc/                  (Includes - helpers, analytics, etc)
```

### After Editing Theme Files
1. Save file
2. Go to http://localhost
3. Hard refresh: **Cmd+Shift+R** (Mac) or **Ctrl+Shift+R** (Windows/Linux)
4. Changes appear!

---

## 🐳 Docker Commands

### View Container Status
```bash
cd /Users/surajgiri/Downloads/docs/wordpress-project
docker compose ps
```

### View All Logs
```bash
docker compose logs -f
```

### View Specific Service Logs
```bash
# WordPress
docker compose logs -f wordpress

# MySQL
docker compose logs -f mysql

# phpMyAdmin
docker compose logs -f phpmyadmin
```

### Restart Services
```bash
# All services
docker compose restart

# Specific service
docker compose restart wordpress
docker compose restart mysql
```

### Stop Services (Keep Data)
```bash
docker compose down
```

### Stop and Remove Everything (Delete Data)
```bash
docker compose down -v
```

### Rebuild Containers
```bash
docker compose up -d --build
```

---

## 🧹 Cleanup & Fresh Start

### Stop Everything
```bash
docker compose down
```

### Stop and Delete All Data
```bash
docker compose down -v
```

### Start Fresh
```bash
docker compose down -v
docker compose up -d
sleep 60
# Visit http://localhost
```

### Remove Unused Images
```bash
docker image prune -a
```

### Remove All Docker Resources (WARNING!)
```bash
docker system prune -a
```

---

## 📊 Performance & Debugging

### Docker Resource Usage
```bash
docker stats
```

### WordPress Error Log
```bash
docker exec cnp-wordpress tail -f /var/www/html/wp-content/debug.log
```

### MySQL Query Log
```bash
docker exec cnp-mysql tail -f /var/log/mysql/error.log
```

### PHP Error Log
```bash
docker exec cnp-wordpress tail -f /var/log/apache2/error.log
```

---

## 🔍 Troubleshooting Commands

### Check if Ports are in Use
```bash
# macOS
lsof -i :80
lsof -i :3306
lsof -i :8080
```

### Kill Process Using Port
```bash
# macOS
kill -9 <PID>

# Or via port
sudo lsof -ti:80 | xargs kill -9
```

### Clear Docker Cache
```bash
docker system prune
docker builder prune
```

### Verify Theme Files Exist
```bash
ls -la /Users/surajgiri/Downloads/docs/wordpress-project/theme/cnp-news-theme/
```

### Check WordPress Core Files
```bash
docker exec cnp-wordpress ls -la /var/www/html/wp-admin
```

---

## 📝 Useful One-Liners

### Full Setup (Start to Access in One Command)
```bash
cd /Users/surajgiri/Downloads/docs/wordpress-project && docker compose up -d && echo "Starting..." && sleep 60 && echo "✅ Visit http://localhost"
```

### Create Test Post
```bash
docker exec cnp-wordpress wp post create --post_title="Test Post" --post_content="Test content" --post_status=publish
```

### Create Test Categories
```bash
docker exec cnp-wordpress wp term create category "AI"
docker exec cnp-wordpress wp term create category "Business"
docker exec cnp-wordpress wp term create category "Startups"
```

### List All Posts
```bash
docker exec cnp-wordpress wp post list --format=table
```

### Get WordPress URL
```bash
docker exec cnp-wordpress wp option get siteurl
```

### Restart Everything
```bash
cd /Users/surajgiri/Downloads/docs/wordpress-project && docker compose down && docker compose up -d && sleep 30 && echo "✅ Ready"
```

### View WordPress Versions
```bash
docker exec cnp-wordpress wp core version
```

---

## 🔐 Credentials Reference

### WordPress Admin
```
URL: http://localhost/wp-admin
User: admin
Pass: (set during installation)
```

### Database
```
Host: mysql (or localhost)
Database: cnpnews_wp
User: cnpnews
Pass: cnpnews_password
MySQL Root: rootpassword
```

### phpMyAdmin
```
Server: mysql
User: cnpnews
Pass: cnpnews_password
```

---

## 🎯 Development Checklist

- [ ] Docker installed and running
- [ ] `docker compose up -d` executed
- [ ] Waited 60 seconds
- [ ] http://localhost loads
- [ ] WordPress installation complete
- [ ] Theme activated
- [ ] Can edit theme files
- [ ] Changes visible after refresh
- [ ] Test content created
- [ ] All services running (`docker compose ps`)

---

## 📚 Documentation Files

```
📖 QUICK-START-LOCAL.md      ← Start here!
📖 LOCAL_SETUP.md            ← Detailed guide
📖 LOCAL-DEVELOPMENT-STARTED.md ← What just happened
📖 COMMAND-REFERENCE.md      ← This file
📖 docker-compose.yml        ← Docker config
📖 README.md                 ← Project overview
```

---

## 🚀 Common Workflows

### First Time Setup
```bash
# 1. Start
cd /Users/surajgiri/Downloads/docs/wordpress-project
docker compose up -d
sleep 60

# 2. Install
# Visit http://localhost
# Complete WordPress setup

# 3. Activate theme
# Appearance → Themes → Activate CNP News Theme
```

### Daily Development
```bash
# 1. Start work
docker compose up -d

# 2. Edit theme
# theme/cnp-news-theme/style.css (or other files)

# 3. Test
# http://localhost (Cmd+Shift+R to refresh)

# 4. When done
docker compose down
```

### Debugging
```bash
# 1. Check status
docker compose ps

# 2. View logs
docker compose logs -f wordpress

# 3. Restart
docker compose restart wordpress

# 4. Test again
# http://localhost
```

### Database Work
```bash
# 1. Open phpMyAdmin
# http://localhost:8080

# 2. Or command line
docker exec -it cnp-mysql mysql -u cnpnews -p cnpnews_wp

# 3. Run queries
SELECT * FROM wp_posts;
SELECT * FROM wp_postmeta;
```

---

## 💡 Tips & Tricks

1. **Always hard refresh** after editing CSS: Cmd+Shift+R
2. **Check logs first** when something breaks: `docker compose logs -f`
3. **Use phpMyAdmin** for easy database browsing
4. **Save database backups**: `docker exec cnp-mysql mysqldump ...`
5. **Test mobile view** in DevTools: F12 → Device toolbar
6. **Enable Lighthouse**: F12 → Lighthouse tab (test performance)
7. **Use WP-CLI** for batch operations: `wp post list`
8. **Create test users** for role testing: `wp user create ...`

---

## 🔗 Quick Links

- WordPress: http://localhost
- Admin: http://localhost/wp-admin
- phpMyAdmin: http://localhost:8080
- Adminer: http://localhost:8081
- Docker Dashboard: Docker Desktop app

---

**Print this page or bookmark it for quick reference!**

Generated: November 11, 2025
