# Server Setup Guide - CNP News

## 1. Server Requirements

### Minimum Specifications
- **OS:** Ubuntu 22.04 LTS or CentOS 8+
- **RAM:** 4GB minimum, 8GB recommended
- **Storage:** 50GB SSD minimum
- **CPU:** 2 cores minimum, 4 cores recommended
- **Bandwidth:** Unlimited or high allowance

### Software Stack
- **Web Server:** Nginx 1.18+ (preferred) or Apache 2.4+
- **PHP:** 8.1 or 8.2 (latest stable)
- **Database:** MySQL 8.0+ or MariaDB 10.6+
- **Cache:** Redis or Memcached
- **SSL:** Let's Encrypt or commercial certificate

## 2. Nginx Configuration

Create `/etc/nginx/sites-available/cnpnews.net`:

```nginx
# CNP News - High Performance Configuration
server {
    listen 80;
    server_name cnpnews.net www.cnpnews.net;
    return 301 https://cnpnews.net$request_uri;
}

server {
    listen 443 ssl http2;
    server_name www.cnpnews.net;
    return 301 https://cnpnews.net$request_uri;
}

server {
    listen 443 ssl http2;
    server_name cnpnews.net;
    
    root /var/www/cnpnews.net/public_html;
    index index.php index.html;
    
    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/cnpnews.net/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/cnpnews.net/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:ECDHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    
    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
    
    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private must-revalidate auth;
    gzip_types
        text/plain
        text/css
        text/xml
        text/javascript
        application/x-javascript
        application/xml+rss
        application/javascript
        application/json
        image/svg+xml;
    
    # Brotli Compression (if module available)
    brotli on;
    brotli_comp_level 6;
    brotli_types
        text/plain
        text/css
        application/json
        application/javascript
        text/xml
        application/xml
        application/xml+rss
        text/javascript;
    
    # WordPress Rules
    location / {
        try_files $uri $uri/ /index.php?$args;
    }
    
    # PHP Processing
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        
        # Security
        fastcgi_hide_header X-Powered-By;
        
        # Cache for static content
        location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
            expires 1y;
            add_header Cache-Control "public, immutable";
            access_log off;
        }
    }
    
    # WordPress Security
    location ~ /(\.|wp-config\.php|readme\.html|license\.txt) {
        deny all;
    }
    
    location ~ ^/(wp-admin|wp-includes)/ {
        location ~ \.php$ {
            include snippets/fastcgi-php.conf;
            fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        }
    }
    
    # WordPress uploads
    location ~* /wp-content/uploads/.*\.php$ {
        deny all;
    }
    
    # WordPress REST API rate limiting
    location ~ ^/wp-json/ {
        limit_req zone=api burst=10 nodelay;
    }
    
    # Sitemap optimization
    location = /sitemap_index.xml {
        try_files $uri $uri/ /index.php?$args;
        expires 1h;
        add_header Cache-Control "public";
    }
    
    location ~ /sitemap.*\.xml$ {
        try_files $uri $uri/ /index.php?$args;
        expires 1h;
        add_header Cache-Control "public";
    }
}

# Rate limiting zones
http {
    limit_req_zone $binary_remote_addr zone=api:10m rate=5r/m;
    limit_req_zone $binary_remote_addr zone=login:10m rate=1r/m;
}
```

## 3. PHP Configuration

Edit `/etc/php/8.2/fpm/php.ini`:

```ini
; Performance Settings
memory_limit = 512M
max_execution_time = 300
max_input_vars = 3000
post_max_size = 64M
upload_max_filesize = 64M

; Security Settings
expose_php = Off
display_errors = Off
log_errors = On
error_log = /var/log/php/error.log

; OPcache Settings
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
opcache.enable_cli=1
opcache.validate_timestamps=1
```

## 4. MySQL Configuration

Create optimized MySQL configuration in `/etc/mysql/conf.d/wordpress.cnf`:

```ini
[mysqld]
# WordPress Optimizations
max_connections = 200
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT

# Query Cache (if using MySQL 5.7)
query_cache_type = 1
query_cache_size = 128M
query_cache_limit = 4M

# General Performance
tmp_table_size = 128M
max_heap_table_size = 128M
key_buffer_size = 128M
table_open_cache = 4000
```

## 5. Cloudflare Integration

### DNS Configuration
1. Point your domain to Cloudflare nameservers
2. Set DNS records:
   ```
   Type: A, Name: @, Value: [Your Server IP]
   Type: A, Name: www, Value: [Your Server IP]
   Type: CNAME, Name: *, Value: cnpnews.net
   ```

### Cloudflare Settings
- **SSL/TLS:** Full (strict) mode
- **Always Use HTTPS:** On
- **HTTP Strict Transport Security:** Enabled
- **Minimum TLS Version:** 1.2
- **Automatic HTTPS Rewrites:** On
- **Brotli Compression:** On
- **Auto Minify:** CSS, JavaScript, HTML
- **APO (Automatic Platform Optimization):** Enabled for WordPress

### Page Rules
```
1. cnpnews.net/wp-admin/* - Security Level: High, Cache Level: Bypass
2. cnpnews.net/wp-login.php - Security Level: High, Cache Level: Bypass
3. cnpnews.net/sitemap*.xml - Cache Level: Standard, Edge Cache TTL: 1 hour
4. cnpnews.net/* - Cache Level: Standard, Browser Cache TTL: 1 year (for static)
```

## 6. Security Configuration

### Firewall (UFW)
```bash
sudo ufw enable
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw allow 3306 # MySQL
sudo ufw deny 80
```

### Fail2Ban
Install and configure Fail2Ban:
```bash
sudo apt install fail2ban
```

Create `/etc/fail2ban/jail.local`:
```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 3

[nginx-http-auth]
enabled = true

[nginx-noscript]
enabled = true

[nginx-badbots]
enabled = true

[nginx-noproxy]
enabled = true
```

## 7. SSL Certificate

### Let's Encrypt Setup
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d cnpnews.net -d www.cnpnews.net
sudo certbot renew --dry-run
```

### Auto-renewal Cron
```bash
0 12 * * * /usr/bin/certbot renew --quiet
```

## 8. Backup Configuration

### Database Backup Script
Create `/home/backup/mysql-backup.sh`:
```bash
#!/bin/bash
BACKUP_DIR="/home/backup/mysql"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="cnpnews_wp"
DB_USER="backup_user"
DB_PASS="secure_password"

mkdir -p $BACKUP_DIR
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/cnpnews_$DATE.sql.gz

# Keep only last 7 days
find $BACKUP_DIR -name "cnpnews_*.sql.gz" -mtime +7 -delete
```

### File Backup Script
Create `/home/backup/files-backup.sh`:
```bash
#!/bin/bash
BACKUP_DIR="/home/backup/files"
WP_DIR="/var/www/cnpnews.net/public_html"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR
tar -czf $BACKUP_DIR/cnpnews_files_$DATE.tar.gz -C $WP_DIR .

# Keep only last 7 days
find $BACKUP_DIR -name "cnpnews_files_*.tar.gz" -mtime +7 -delete
```

## 9. Monitoring Setup

### Log Rotation
Create `/etc/logrotate.d/cnpnews`:
```
/var/log/nginx/cnpnews.net*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
    postrotate
        systemctl reload nginx
    endscript
}
```

### System Monitoring
Install basic monitoring:
```bash
sudo apt install htop iotop nethogs
```

## 10. Performance Testing

After setup, test performance:
```bash
# Test Nginx configuration
sudo nginx -t

# Test PHP-FPM
sudo php-fpm8.2 -t

# Test MySQL connection
mysql -u root -p -e "SELECT VERSION();"

# Test SSL certificate
openssl s_client -connect cnpnews.net:443 -servername cnpnews.net
```

## Next Steps

1. ✅ Server configured
2. ⏳ Install WordPress (see `wordpress-install.md`)
3. ⏳ Configure theme and plugins
4. ⏳ Set up Cloudflare APO
5. ⏳ Performance optimization
6. ⏳ Security hardening

---

**Estimated Setup Time:** 2-3 hours  
**Prerequisites:** Root server access, domain control  
**Support:** Contact WordPress Developer for assistance
