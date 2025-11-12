# 🐳 Docker Installation Guide for macOS

**Docker is not currently installed on your Mac. Here's how to install it.**

---

## 📊 Current Status

```
❌ Docker CLI: NOT FOUND
❌ Docker Daemon: NOT RUNNING
❌ Docker Compose: NOT INSTALLED

Action Required: Install Docker Desktop
```

---

## Installation Options

### Option 1: Docker Desktop (Recommended - Easiest)

**Download and Install:**

1. Go to: https://www.docker.com/products/docker-desktop
2. Click **"Download for Mac"**
3. Choose your Mac architecture:
   - **Apple Silicon (M1/M2/M3):** `Docker.dmg` for Apple Silicon
   - **Intel Mac:** `Docker.dmg` for Intel Chip
4. Download and run the installer
5. Follow the installation wizard
6. Authorize with your Mac password when prompted
7. Launch Docker Desktop from Applications

**Start Docker:**
- Open **Applications → Docker** 
- Wait for the menu bar icon to show (whale icon in top-right)
- You'll see "Docker is running"

**Verify Installation:**
```bash
docker --version
docker run hello-world
```

---

### Option 2: Homebrew (Alternative)

**Install via Homebrew:**

First, install Homebrew if you don't have it:
```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

Then install Docker:
```bash
brew install --cask docker
```

Start Docker:
```bash
# Either:
# 1. Open Docker from Applications (easier)
# 2. Or start the daemon manually (advanced)
```

---

### Option 3: Docker Homebrew Package (Advanced)

For experienced users who want just the CLI:
```bash
brew install docker docker-compose
brew install colima  # Container runtime for Mac
colima start        # Start the container runtime
```

---

## ✅ Verification Steps

### Step 1: Check Docker Version
```bash
docker --version
```

Expected output:
```
Docker version 24.0.0, build abcdef
```

### Step 2: Check Docker Daemon
```bash
docker ps
```

Expected output:
```
CONTAINER ID   IMAGE     COMMAND   CREATED   STATUS    PORTS     NAMES
```
(Should show empty list if no containers running)

### Step 3: Test Docker Works
```bash
docker run hello-world
```

Expected output:
```
Hello from Docker!
This message shows that your installation appears to be working correctly.
```

---

## 🔧 Troubleshooting

### Docker command not found

**Problem:** 
```
zsh: command not found: docker
```

**Solution 1:** Restart terminal
```bash
# Close and reopen terminal
# Then try: docker --version
```

**Solution 2:** Add to PATH manually
```bash
# Open ~/.zshrc
nano ~/.zshrc

# Add this line:
export PATH="/Applications/Docker.app/Contents/Resources/bin:$PATH"

# Save (Ctrl+O, Enter, Ctrl+X)
# Reload: source ~/.zshrc
```

**Solution 3:** Reinstall Docker Desktop
- Uninstall: **Applications → Docker → Delete**
- Reinstall from: https://www.docker.com/products/docker-desktop

### Docker daemon not running

**Problem:**
```
Cannot connect to the Docker daemon at unix:///var/run/docker.sock. 
Is the docker daemon running?
```

**Solution:**
1. Open **Applications → Docker**
2. Wait for whale icon to appear in top menu bar
3. Should say "Docker is running"
4. Try command again: `docker ps`

### M1/M2 Mac specific issues

**For Apple Silicon Macs:**
- Download: **"Docker Desktop for Apple Silicon"**
- NOT the Intel version
- Visit: https://www.docker.com/products/docker-desktop

---

## 🎯 Next Steps (After Installation)

Once Docker is installed and running:

### 1. Verify Installation
```bash
docker --version
docker ps
```

### 2. Navigate to Project
```bash
cd /Users/surajgiri/Downloads/docs/wordpress-project
```

### 3. Start WordPress
```bash
docker compose up -d
sleep 60
echo "✅ Visit http://localhost"
```

### 4. Open WordPress
```
http://localhost
```

### 5. Complete Setup
- Fill in WordPress installation form
- Create admin account
- Activate CNP News theme

---

## 📱 Docker Desktop Features

After installation, you get:

✅ Docker Engine (runs containers)  
✅ Docker Compose (manages multi-container apps)  
✅ Docker Desktop GUI (visual management)  
✅ Kubernetes (optional orchestration)  
✅ Automatic updates  

---

## 💾 System Requirements

**macOS Requirements:**
- macOS 11 (Big Sur) or newer
- Apple Silicon (M1+) or Intel processor
- 4GB RAM minimum (8GB+ recommended)
- 10GB free disk space

**Docker Desktop takes approximately:**
- 2-3 GB disk space
- 500MB-2GB RAM when running

---

## 🔑 Key Concepts

**Container:** Lightweight app package (like VM but faster)  
**Image:** Blueprint for creating containers  
**Docker Compose:** Tool to run multiple containers together  
**docker-compose.yml:** Config file (we created this for you)  

For CNP News, we have:
- **MySQL container** (database)
- **WordPress container** (website)
- **phpMyAdmin container** (database tool)

All defined in `docker-compose.yml`

---

## 📖 Resources

**Official Documentation:**
- Docker: https://docs.docker.com/
- Docker Desktop: https://docs.docker.com/desktop/
- Docker Compose: https://docs.docker.com/compose/

**Mac-Specific:**
- Apple Silicon: https://docs.docker.com/desktop/install/mac-install/
- Troubleshooting: https://docs.docker.com/desktop/troubleshoot/

---

## ⏱️ Installation Time

- Download: 2-5 minutes (depends on internet)
- Installation: 2-3 minutes
- First launch: 30-60 seconds
- **Total: 5-10 minutes**

---

## ✨ After Installation

Once Docker is installed:

```bash
# Navigate to project
cd /Users/surajgiri/Downloads/docs/wordpress-project

# Start your WordPress site
docker compose up -d

# Wait 60 seconds
sleep 60

# Visit in browser
open http://localhost
```

That's it! Your complete WordPress development environment will be running.

---

## 🆘 Still Having Issues?

### Check installation:
```bash
# Should show version
docker --version

# Should show docker desktop is running
docker ps

# Should work
docker run hello-world
```

### Common fixes:
```bash
# Restart Docker:
# Applications → Docker → Quit
# Then: Applications → Docker → Launch

# Restart Mac:
# Apple menu → Restart

# Reinstall Docker:
# Applications → Docker → Drag to Trash
# Download fresh from docker.com
```

---

## 🎉 Success Indicators

You'll know Docker is installed correctly when:

✅ `docker --version` shows version number  
✅ `docker ps` works without errors  
✅ `docker run hello-world` runs successfully  
✅ Whale icon appears in top menu bar  
✅ Applications → Docker exists  

---

**Once Docker is installed and running, you'll have a complete WordPress development environment ready to go!**

👉 **Next Step: Install Docker, then come back and run:**
```bash
cd /Users/surajgiri/Downloads/docs/wordpress-project && docker compose up -d
```

Happy developing! 🚀
