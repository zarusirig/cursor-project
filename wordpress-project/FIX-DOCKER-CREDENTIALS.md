# 🔧 Fix Docker Credentials Helper Issue on macOS

**Error:** `error getting credentials - err: exit status 1, out: One or more parameters passed to the function were not valid. (-50)`

---

## Quick Fix (Try This First)

### Option 1: Reset Docker Credentials

```bash
# Remove Docker config
rm -rf ~/.docker/config.json

# Restart Docker Desktop
# 1. Click Docker icon in menu bar
# 2. Click "Quit Docker Desktop"
# 3. Wait 10 seconds
# 4. Open Applications → Docker
```

Then try:
```bash
cd /Users/surajgiri/Downloads/docs/wordpress-project
docker compose up -d
```

---

### Option 2: Disable Credential Helper

Edit your Docker config:

```bash
# Open config
nano ~/.docker/config.json
```

Find this section:
```json
"credsStore": "osxkeychain"
```

Delete or comment it out, so the file looks like:
```json
{
  "auths": {},
  "plugins": {
    "buildx": "disabled",
    "compose": "enabled",
    "scout": "disabled"
  }
}
```

Save and restart Docker.

---

### Option 3: Use Pre-Pulled Images (Fastest)

Pre-pull the images so Docker doesn't need credentials:

```bash
docker pull mysql:8.0
docker pull wordpress:6.4-php8.2-apache
docker pull phpmyadmin:5.2

# Then try compose
cd /Users/surajgiri/Downloads/docs/wordpress-project
docker compose up -d
```

---

## If None of That Works

### Nuclear Option: Reset Everything

```bash
# 1. Quit Docker Desktop completely
# Applications → Docker → Quit

# 2. Clear all Docker data
rm -rf ~/.docker
rm -rf ~/Library/Group\ Containers/group.com.docker

# 3. Restart Mac
# Apple menu → Restart

# 4. Open Docker Desktop again
# Applications → Docker

# 5. Wait for it to be fully ready (whale icon in menu)

# 6. Try again
cd /Users/surajgiri/Downloads/docs/wordpress-project
docker compose up -d
```

---

## Alternative: Use Local Built Images

Create images locally (no credential issues):

```bash
# Instead of pulling from Docker Hub, build locally
docker build --tag mysql-local:8.0 -f - <<EOF
FROM mysql:8.0
EOF

# Then use in compose - but this is complicated
# Better to just fix the credential helper above
```

---

## What's Actually Wrong?

The Docker credentials helper (osxkeychain) is having trouble. This can happen because:

1. Docker Desktop is out of date
2. Keychain is corrupted
3. MacOS permissions issue
4. Old config file

**The fix:** Reset the credentials and let Docker re-authenticate.

---

## Step-by-Step Fix

### Step 1: Check Current Status
```bash
docker ps
```

If this works → Skip to Step 5
If it fails with credentials error → Continue

### Step 2: Quit Docker
```bash
# Click whale icon in top menu bar → Quit Docker Desktop
# Or via terminal:
pkill Docker
```

Wait 10 seconds.

### Step 3: Remove Config
```bash
rm ~/.docker/config.json
```

### Step 4: Restart Docker
```bash
# Open Applications → Docker
# Wait until whale icon appears in menu bar
# Should say "Docker is running"
```

### Step 5: Test Docker
```bash
docker ps
```

Should work now.

### Step 6: Try Compose
```bash
cd /Users/surajgiri/Downloads/docs/wordpress-project
docker compose up -d
```

---

## Verify It's Working

After applying the fix:

```bash
# Should show version
docker --version

# Should work without errors
docker ps

# Should work without errors
docker compose ps
```

---

## Next: Start WordPress

Once Docker is working:

```bash
cd /Users/surajgiri/Downloads/docs/wordpress-project

# Start containers
docker compose up -d

# Wait 60 seconds
sleep 60

# Check status
docker compose ps

# Should see all containers running:
# cnp-mysql      Running
# cnp-wordpress  Running  
# cnp-phpmyadmin Running
```

Then visit: **http://localhost**

---

## Still Not Working?

Try these in order:

1. **Restart Docker completely:**
   ```bash
   pkill Docker
   sleep 10
   open /Applications/Docker.app
   ```

2. **Check if port 80 is free:**
   ```bash
   lsof -i :80
   # If something shows, it's using port 80
   # Quit that app or change docker port to 8000
   ```

3. **Check Docker status:**
   ```bash
   docker system info
   # Should show lots of info about Docker
   ```

4. **Check network:**
   ```bash
   ping google.com
   # If fails, internet is down
   ```

5. **Rebuild everything:**
   ```bash
   docker system prune -a
   docker compose up -d
   ```

---

## Contact Docker Support

If the issue persists:

1. Download latest Docker Desktop: https://www.docker.com/products/docker-desktop
2. Reinstall fresh
3. Or submit issue: https://github.com/docker/for-mac/issues

---

**After fixing, run:**

```bash
cd /Users/surajgiri/Downloads/docs/wordpress-project && docker compose up -d && sleep 60 && echo "✅ Visit http://localhost"
```
