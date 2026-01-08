# 🎓 Moodle Backend - White-Label LMS

<p align="center">
  <img src="logo.png" alt="Brand Logo" width="200">
</p>

**A production-ready, white-label Moodle LMS backend designed for mobile app integration and easy deployment on Coolify, VPS, or EC2.**

---

## ✨ Features

- 🐳 **Docker-Ready** - Fully containerized with optimized Dockerfile
- 🎨 **White-Label Support** - Complete branding configuration via JSON
- 📱 **Mobile App Integration** - Pre-configured for Moodle mobile app
- 🔒 **Production Security** - SSL, Redis sessions, secure headers
- 🏥 **Health Checks** - Robust health endpoint for container orchestration
- 🚀 **Performance Optimized** - OPcache, Redis, PostgreSQL tuning
- ☁️ **Coolify Compatible** - Works perfectly with Coolify deployment

---

## 🚀 Quick Start

### 1. Clone the Repository

```bash
git clone https://github.com/your-repo/moodle-backend.git
cd moodle-backend
```

### 2. Configure Environment

```bash
cp .env.example .env
nano .env  # Edit with your settings
```

### 3. Deploy with Docker

```bash
docker-compose up -d --build
```

### 4. Access Your LMS

Open `http://localhost:8080` (or your configured domain)

---

## 📁 Project Structure

```
moodle-backend/
├── Dockerfile              # Production-optimized Docker image
├── docker-compose.yml      # Complete stack with DB, Redis, Cron
├── docker-entrypoint.sh    # Auto-configuration script
├── .env.example            # Environment template
├── whitelabel-config.json  # Brand customization file
├── apply-whitelabel.php    # White-label application script
├── DEPLOYMENT.md           # Comprehensive deployment guide
├── public/                 # Moodle web root
│   ├── healthcheck.php     # Health check endpoint
│   └── theme/masbrand/     # Custom white-label theme
└── ...
```

---

## 🎨 White-Labeling

### Step 1: Edit Configuration

Customize `whitelabel-config.json`:

```json
{
  "brand": {
    "name": "YourBrand",
    "display_name": "YourBrand Learning Platform"
  },
  "colors": {
    "primary": { "main": "#2563EB" }
  }
}
```

### Step 2: Add Your Logo

Place logos in `public/theme/masbrand/pix/`:

- `logo.png` - Main logo (200x50px)
- `favicon.ico` - Browser icon (32x32px)

### Step 3: Apply Branding

```bash
php apply-whitelabel.php
docker-compose up -d --build
```

---

## 📋 Configuration

### Essential Environment Variables

| Variable                | Description       | Default                 |
| ----------------------- | ----------------- | ----------------------- |
| `DOMAIN`                | Your domain name  | `localhost`             |
| `MOODLE_WWWROOT`        | Full site URL     | `http://localhost:8080` |
| `MOODLE_DBPASSWORD`     | Database password | ⚠️ **Change this!**     |
| `MOODLE_ADMIN_PASSWORD` | Admin password    | ⚠️ **Change this!**     |
| `MOODLE_SSL_PROXY`      | Behind SSL proxy  | `false`                 |

See `.env.example` for all options.

---

## 🏥 Health Checks

The `/healthcheck.php` endpoint provides:

- ✅ PHP runtime check
- ✅ Database connectivity
- ✅ Configuration validation

```bash
curl http://localhost:8080/healthcheck.php
```

Response:

```json
{
  "status": "healthy",
  "checks": {
    "php": "ok",
    "config": "ok",
    "database": "ok"
  }
}
```

---

## 📱 Mobile App Setup

Your backend is pre-configured for the Moodle mobile app:

1. Login as admin
2. Go to Site Administration > Plugins > Web services > Mobile
3. Enable mobile web services
4. Configure your mobile app with your site URL

---

## 📚 Documentation

- **[DEPLOYMENT.md](./DEPLOYMENT.md)** - Complete deployment guide
- **[whitelabel-config.json](./whitelabel-config.json)** - Branding options reference

---

## 🔧 Common Commands

```bash
# Start all services
docker-compose up -d

# View logs
docker-compose logs -f app

# Restart services
docker-compose restart

# Clear Moodle caches
docker exec moodle_app php /var/www/html/public/admin/cli/purge_caches.php

# Run cron manually
docker exec moodle_app php /var/www/html/public/admin/cli/cron.php
```

---

## 🛡️ Security Checklist

Before production:

- [ ] Changed `MOODLE_DBPASSWORD`
- [ ] Changed `MOODLE_ADMIN_PASSWORD`
- [ ] Set `MOODLE_ADMIN_EMAIL`
- [ ] Enabled HTTPS (`MOODLE_SSL_PROXY=true`)
- [ ] Configured proper domain
- [ ] Set up backups

---

## 📄 License

This is a fork of [Moodle](https://moodle.org), which is provided freely as open source software under the GNU General Public License v3.

---

## 🤝 Support

For deployment issues, check:

1. [DEPLOYMENT.md](./DEPLOYMENT.md) troubleshooting section
2. Container logs: `docker-compose logs`

---

**Made with ❤️ for modern education**
