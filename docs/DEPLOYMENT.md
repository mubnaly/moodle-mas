# 🚀 Moodle Backend Deployment Guide

## Complete Guide for Deploying on Coolify, VPS, and EC2

---

## 📋 Table of Contents

1. [Prerequisites](#prerequisites)
2. [Quick Start](#quick-start)
3. [Configuration](#configuration)
4. [Deployment Options](#deployment-options)
   - [Coolify Deployment](#option-1-coolify-deployment-recommended)
   - [Docker Compose on VPS](#option-2-docker-compose-on-vps)
   - [EC2 Deployment](#option-3-ec2-deployment)
5. [Health Check Configuration](#health-check-configuration)
6. [SSL/HTTPS Setup](#sslhttps-setup)
7. [Backup and Restore](#backup-and-restore)
8. [Updating](#updating)

---

## Prerequisites

Before you begin, ensure you have:

- [ ] A VPS or EC2 instance with at least:
  - 2 CPU cores (4 recommended)
  - 4GB RAM (8GB recommended)
  - 40GB storage (SSD recommended)
- [ ] Domain name pointed to your server
- [ ] Docker and Docker Compose installed (or Coolify)
- [ ] Git installed
- [ ] Your brand logo (PNG format, transparent background)

---

## Quick Start

### Step 1: Clone the Repository

```bash
git clone https://github.com/your-repo/moodle-backend.git
cd moodle-backend
```

### Step 2: Create Environment File

```bash
cp .env.example .env
```

### Step 3: Edit Configuration

Open `.env` and update these values:

```bash
nano .env
```

**Critical settings to change:**

```env
# Your domain
DOMAIN=lms.yourdomain.com
MOODLE_WWWROOT=https://lms.yourdomain.com

# CHANGE THESE PASSWORDS!
MOODLE_DBPASSWORD=your_secure_database_password
MOODLE_ADMIN_PASSWORD=Your_Secure_Admin_Password_123!
MOODLE_ADMIN_EMAIL=admin@yourdomain.com

# Site name
MOODLE_FULLNAME=Your Learning Platform
MOODLE_SHORTNAME=YLP

# SSL (set to true if using HTTPS)
MOODLE_SSL_PROXY=true
```

### Step 4: Deploy

```bash
docker-compose up -d --build
```

### Step 5: Access Your Site

Open `https://lms.yourdomain.com` in your browser.

---

## Configuration

### Environment Variables Reference

| Variable                | Description           | Default                 |
| ----------------------- | --------------------- | ----------------------- |
| `COMPOSE_PROJECT_NAME`  | Docker project name   | `moodle`                |
| `DOMAIN`                | Your domain name      | `localhost`             |
| `APP_PORT`              | Port to expose        | `8080`                  |
| `MOODLE_WWWROOT`        | Full URL of your site | `http://localhost:8080` |
| `MOODLE_DBPASSWORD`     | Database password     | `moodlepass`            |
| `MOODLE_ADMIN`          | Admin username        | `admin`                 |
| `MOODLE_ADMIN_PASSWORD` | Admin password        | `Admin123!`             |
| `MOODLE_ADMIN_EMAIL`    | Admin email           | `admin@example.com`     |
| `MOODLE_FULLNAME`       | Site full name        | `My Learning Platform`  |
| `MOODLE_SHORTNAME`      | Site short name       | `LMS`                   |
| `MOODLE_SSL_PROXY`      | Behind SSL proxy?     | `false`                 |
| `PHP_MEMORY_LIMIT`      | PHP memory limit      | `512M`                  |
| `REDIS_MAXMEMORY`       | Redis max memory      | `256mb`                 |

---

## Deployment Options

### Option 1: Coolify Deployment (Recommended)

Coolify is a self-hosted PaaS that makes deployment easy.

#### Prerequisites

- Coolify installed on your VPS
- Domain configured in Coolify

#### Steps

1. **Add New Project in Coolify**

   - Go to Coolify Dashboard
   - Click "New Project"
   - Select "Docker Compose"

2. **Connect Your Repository**

   - Connect to GitHub/GitLab
   - Select your moodle-backend repository

3. **Configure Environment Variables**

   In Coolify's environment settings, add:

   ```env
   COMPOSE_PROJECT_NAME=yourbrand
   DOMAIN=lms.yourbrand.com
   MOODLE_WWWROOT=https://lms.yourbrand.com
   MOODLE_DBPASSWORD=your_secure_password_here
   MOODLE_ADMIN=admin
   MOODLE_ADMIN_PASSWORD=YourSecureAdminPassword123!
   MOODLE_ADMIN_EMAIL=admin@yourbrand.com
   MOODLE_FULLNAME=Your Learning Platform
   MOODLE_SHORTNAME=YLP
   MOODLE_SSL_PROXY=true
   REDIS_HOST=redis
   REDIS_PORT=6379
   ```

4. **Configure Health Check**

   In Coolify's Health Check settings:

   - **Path:** `/healthcheck.php`
   - **Interval:** `30s`
   - **Timeout:** `10s`
   - **Start Period:** `180s` ⚠️ (important - Moodle takes time to initialize)
   - **Retries:** `3`

5. **Configure Domain & SSL**

   - Add your domain in Coolify
   - Enable "Generate SSL Certificate"
   - Coolify will auto-configure Let's Encrypt

6. **Deploy**
   - Click "Deploy"
   - Wait for build to complete (5-10 minutes first time)

#### Coolify Health Check Fix

If health check fails in Coolify:

1. **Increase Start Period**

   - Moodle takes time to initialize
   - Set start period to `180s` or higher

2. **Check the Health Check URL**

   ```bash
   # Test manually
   curl -f http://localhost:8080/healthcheck.php
   ```

3. **View Container Logs**
   ```bash
   docker logs moodle_app
   ```

### Option 2: Docker Compose on VPS

#### Server Setup

1. **Update Server**

   ```bash
   sudo apt update && sudo apt upgrade -y
   ```

2. **Install Docker**

   ```bash
   curl -fsSL https://get.docker.com | sh
   sudo usermod -aG docker $USER
   ```

3. **Install Docker Compose**

   ```bash
   sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
   sudo chmod +x /usr/local/bin/docker-compose
   ```

4. **Clone and Deploy**
   ```bash
   git clone https://github.com/your-repo/moodle-backend.git
   cd moodle-backend
   cp .env.example .env
   nano .env  # Edit configuration
   docker-compose up -d --build
   ```

#### Setting Up Nginx Reverse Proxy with SSL

1. **Install Nginx and Certbot**

   ```bash
   sudo apt install nginx certbot python3-certbot-nginx -y
   ```

2. **Create Nginx Configuration**

   ```bash
   sudo nano /etc/nginx/sites-available/moodle
   ```

   ```nginx
   server {
       listen 80;
       server_name lms.yourdomain.com;

       location / {
           proxy_pass http://localhost:8080;
           proxy_set_header Host $host;
           proxy_set_header X-Real-IP $remote_addr;
           proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
           proxy_set_header X-Forwarded-Proto $scheme;

           # WebSocket support
           proxy_http_version 1.1;
           proxy_set_header Upgrade $http_upgrade;
           proxy_set_header Connection "upgrade";

           # Timeouts
           proxy_connect_timeout 60s;
           proxy_send_timeout 300s;
           proxy_read_timeout 300s;

           # File upload size
           client_max_body_size 512M;
       }
   }
   ```

3. **Enable Site**

   ```bash
   sudo ln -s /etc/nginx/sites-available/moodle /etc/nginx/sites-enabled/
   sudo nginx -t
   sudo systemctl restart nginx
   ```

4. **Get SSL Certificate**
   ```bash
   sudo certbot --nginx -d lms.yourdomain.com
   ```

### Option 3: EC2 Deployment

#### Launch EC2 Instance

1. **Instance Type:** t3.medium or larger
2. **AMI:** Ubuntu 22.04 LTS
3. **Storage:** 40GB SSD minimum
4. **Security Groups:**
   - Port 22 (SSH)
   - Port 80 (HTTP)
   - Port 443 (HTTPS)

#### Setup

1. **SSH into Instance**

   ```bash
   ssh -i your-key.pem ubuntu@your-ec2-ip
   ```

2. **Follow VPS Setup Steps Above**

3. **Configure Elastic IP**

   - Go to EC2 Dashboard > Elastic IPs
   - Allocate new address
   - Associate with your instance

4. **Configure Route 53 (Optional)**
   - Create hosted zone for your domain
   - Add A record pointing to Elastic IP

---

## Health Check Configuration

The health check endpoint (`/healthcheck.php`) provides:

- PHP runtime check
- Database connectivity check
- Configuration validation

### Response Format

```json
{
  "status": "healthy",
  "timestamp": "2024-01-15T10:30:00+00:00",
  "checks": {
    "php": "ok",
    "config": "ok",
    "database": "ok"
  },
  "message": "All systems operational"
}
```

### Health Check States

| Status       | HTTP Code | Meaning                      |
| ------------ | --------- | ---------------------------- |
| `healthy`    | 200       | All systems operational      |
| `installing` | 200       | First-time setup in progress |
| `unhealthy`  | 500       | System failure - check logs  |

---

## SSL/HTTPS Setup

### Behind Coolify/Traefik (Automatic)

SSL is automatically handled by Coolify's Traefik integration.

Ensure your `.env` has:

```env
MOODLE_SSL_PROXY=true
MOODLE_WWWROOT=https://your-domain.com
```

### Manual SSL with Certbot

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Get Certificate
sudo certbot --nginx -d your-domain.com

# Auto-renewal
sudo certbot renew --dry-run
```

---

## Backup and Restore

### Automated Backup Script

Create `backup.sh`:

```bash
#!/bin/bash
BACKUP_DIR="/backups/moodle"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p $BACKUP_DIR

# Backup database
docker exec moodle_db pg_dump -U moodleuser moodle | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup moodledata
docker run --rm -v moodle_data:/data -v $BACKUP_DIR:/backup alpine tar czf /backup/data_$DATE.tar.gz -C /data .

# Keep only last 7 days
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup completed: $DATE"
```

### Restore

```bash
# Restore database
gunzip < backup.sql.gz | docker exec -i moodle_db psql -U moodleuser moodle

# Restore moodledata
docker run --rm -v moodle_data:/data -v /path/to/backup:/backup alpine tar xzf /backup/data_backup.tar.gz -C /data
```

---

## Updating

### Update Application

```bash
# Pull latest changes
git pull origin main

# Rebuild and restart
docker-compose up -d --build

# Run upgrades
docker exec moodle_app php /var/www/html/public/admin/cli/upgrade.php
```

### Update Docker Images

```bash
docker-compose pull
docker-compose up -d
```

---

## Security Checklist

Before going to production:

- [ ] Changed all default passwords
- [ ] Enabled HTTPS/SSL
- [ ] Configured backup system
- [ ] Set `MOODLE_SSL_PROXY=true` if behind proxy
- [ ] Reviewed firewall rules
- [ ] Disabled debug mode
- [ ] Set secure admin email
- [ ] Tested health checks

---

**Happy Deploying! 🎉**
