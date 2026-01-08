---
description: How to deploy Moodle to Coolify
---

# Deploying Moodle Backend to Coolify

## Prerequisites

- Coolify installed on your server
- Domain configured and pointing to your server
- Git repository access configured in Coolify

## Steps

### 1. Create Environment File

Copy the example environment file and edit it:

```bash
cp .env.example .env
```

// turbo

### 2. Edit the .env file

Open `.env` and set these critical values:

- `DOMAIN` - Your domain name (e.g., lms.yourdomain.com)
- `MOODLE_WWWROOT` - Full URL with https://
- `MOODLE_DBPASSWORD` - A secure database password
- `MOODLE_ADMIN_PASSWORD` - A secure admin password
- `MOODLE_SSL_PROXY=true` - Enable this for HTTPS

### 3. In Coolify Dashboard

1. Go to Projects > Add New Project
2. Select "Docker Compose"
3. Connect your GitHub/GitLab repository
4. Select the moodle-backend repository

### 4. Configure Environment Variables in Coolify

Add all variables from your `.env` file to Coolify's environment section.

### 5. Configure Health Check in Coolify

Set these health check parameters:

- **Path:** `/healthcheck.php`
- **Interval:** `30s`
- **Timeout:** `10s`
- **Start Period:** `180s` (important - Moodle takes time to initialize)
- **Retries:** `3`

### 6. Configure Domain & SSL

1. Add your domain in Coolify
2. Enable "Generate SSL Certificate"
3. Coolify will auto-configure Let's Encrypt

### 7. Deploy

Click the Deploy button and wait for the build (5-10 minutes for first deployment).

### 8. Verify Deployment

- Check `/healthcheck.php` returns `{"status": "healthy"}`
- Access your site at `https://your-domain.com`
- Login with admin credentials set in environment

## Troubleshooting

### Health check failing

1. Increase start period to 180s or more
2. Check container logs: `docker logs container_name`
3. Verify database connection

### Database connection errors

1. Check database container is running
2. Verify credentials match in all services
3. Wait for database to fully initialize (30-60 seconds)

### SSL issues

1. Ensure domain DNS is properly configured
2. Check Coolify SSL certificate status
3. Verify `MOODLE_SSL_PROXY=true` is set
