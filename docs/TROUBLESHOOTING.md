# 🔧 Troubleshooting Guide

## Common Issues and Solutions

---

## 📋 Table of Contents

1. [Health Check Issues](#health-check-issues)
2. [Container Issues](#container-issues)
3. [Database Issues](#database-issues)
4. [Permission Issues](#permission-issues)
5. [Performance Issues](#performance-issues)
6. [SSL/HTTPS Issues](#sslhttps-issues)
7. [Mobile App Issues](#mobile-app-issues)
8. [Branding Issues](#branding-issues)

---

## Health Check Issues

### Health Check Failing in Coolify

**Symptoms:**

- Container keeps restarting
- Coolify shows "unhealthy" status
- Deployment fails

**Solutions:**

1. **Increase Start Period**

   Moodle takes time to initialize. In Coolify:

   - Set start period to `180s` or higher
   - Set interval to `30s`
   - Set retries to `3`

2. **Check Container Logs**

   ```bash
   docker logs moodle_app --tail 100
   ```

3. **Test Health Check Manually**

   ```bash
   docker exec moodle_app curl -f http://localhost/healthcheck.php
   ```

4. **Check Database Connection**
   ```bash
   docker exec moodle_db pg_isready -U moodleuser -d moodle
   ```

### Health Check Returns 500

**Cause:** Database connection failed

**Solutions:**

1. **Verify database is running:**

   ```bash
   docker-compose ps db
   ```

2. **Check database credentials match:**

   - In `.env`
   - In docker-compose.yml
   - Both should have same values

3. **Wait for database initialization:**
   - First startup can take 60+ seconds
   - Check logs: `docker-compose logs db`

---

## Container Issues

### Container Won't Start

**Check:**

```bash
docker-compose ps
docker-compose logs app
```

**Common causes:**

1. **Port conflict:**

   ```bash
   # Check if port is in use
   netstat -tulpn | grep 8080

   # Change port in .env
   APP_PORT=8081
   ```

2. **Volume permission issues:**

   ```bash
   docker-compose down
   docker volume rm moodle_data
   docker-compose up -d
   ```

3. **Memory issues:**

   ```bash
   # Check available memory
   free -h

   # Reduce PHP memory in .env
   PHP_MEMORY_LIMIT=256M
   ```

### Container Restarts Continuously

**Check:**

```bash
docker-compose logs --tail 50 app
```

**Common causes:**

1. **Failed health check** - See above
2. **Missing config** - Check `.env` file exists
3. **Database not ready** - Wait longer or restart

**Solution:**

```bash
docker-compose down
docker-compose up -d
# Wait 2-3 minutes
docker-compose logs -f app
```

---

## Database Issues

### Database Connection Refused

**Symptoms:**

- "could not connect to server"
- Health check failing

**Solutions:**

1. **Check database container:**

   ```bash
   docker-compose ps db
   docker-compose logs db
   ```

2. **Restart database:**

   ```bash
   docker-compose restart db
   # Wait 30 seconds
   docker-compose restart app
   ```

3. **Verify credentials:**
   ```bash
   # Test connection
   docker exec moodle_db psql -U moodleuser -d moodle -c "SELECT 1"
   ```

### Database Tables Missing

**Symptoms:**

- "Table mdl_config doesn't exist"
- Installation appears incomplete

**Solutions:**

1. **Run Moodle installation:**

   ```bash
   docker exec moodle_app php /var/www/html/public/admin/cli/install.php \
     --wwwroot=$MOODLE_WWWROOT \
     --dataroot=/var/www/moodledata \
     --dbtype=pgsql \
     --dbhost=db \
     --dbname=moodle \
     --dbuser=moodleuser \
     --dbpass=your_password \
     --fullname="Your Site" \
     --shortname="LMS" \
     --adminuser=admin \
     --adminpass=AdminPass123! \
     --adminemail=admin@example.com \
     --non-interactive \
     --agree-license
   ```

2. **Or reset and reinstall:**
   ```bash
   docker-compose down -v  # WARNING: Deletes all data
   docker-compose up -d
   ```

### Database Slow

**Solutions:**

1. **Check PostgreSQL logs:**

   ```bash
   docker-compose logs db | grep -i slow
   ```

2. **Increase shared buffers** in docker-compose.yml:
   ```yaml
   command:
     - "-c"
     - "shared_buffers=512MB"
   ```

---

## Permission Issues

### Moodledata Permission Denied

**Symptoms:**

- "Cannot create directory"
- "Failed to write file"

**Solutions:**

1. **Fix permissions in container:**

   ```bash
   docker exec moodle_app chown -R www-data:www-data /var/www/moodledata
   docker exec moodle_app chmod -R 775 /var/www/moodledata
   ```

2. **Fix application permissions:**
   ```bash
   docker exec moodle_app chown -R www-data:www-data /var/www/html
   ```

### Config File Permission Denied

**Solution:**

```bash
docker exec moodle_app chown www-data:www-data /var/www/html/config.php
docker exec moodle_app chmod 640 /var/www/html/config.php
```

---

## Performance Issues

### Site is Slow

**Solutions:**

1. **Clear Moodle caches:**

   ```bash
   docker exec moodle_app php /var/www/html/public/admin/cli/purge_caches.php
   ```

2. **Check Redis is working:**

   ```bash
   docker exec moodle_redis redis-cli ping
   # Should return PONG
   ```

3. **Increase PHP memory:**

   ```env
   PHP_MEMORY_LIMIT=1G
   ```

4. **Check cron is running:**
   ```bash
   docker logs moodle_cron --tail 20
   ```

### Out of Memory Errors

**Solutions:**

1. **Increase container memory** in .env:

   ```env
   PHP_MEMORY_LIMIT=1G
   ```

2. **Check server memory:**

   ```bash
   free -h
   ```

3. **Reduce concurrent users** or upgrade server

---

## SSL/HTTPS Issues

### Mixed Content Warnings

**Cause:** `MOODLE_SSL_PROXY` not set

**Solution:**

```env
MOODLE_SSL_PROXY=true
MOODLE_WWWROOT=https://your-domain.com
```

### SSL Certificate Not Working

**In Coolify:**

1. Check domain DNS is correct
2. Verify domain is added in Coolify
3. Enable "Generate SSL Certificate"
4. Wait 5 minutes for propagation

**Manual Certbot:**

```bash
sudo certbot --nginx -d your-domain.com
```

### Redirect Loop

**Cause:** SSL proxy misconfigured

**Solution:**

1. Ensure `MOODLE_SSL_PROXY=true`
2. Ensure `MOODLE_WWWROOT` starts with `https://`
3. Restart containers:
   ```bash
   docker-compose down
   docker-compose up -d
   ```

---

## Mobile App Issues

### App Can't Connect

**Solutions:**

1. **Enable web services:**

   - Login as admin
   - Site Administration > Plugins > Web services > Mobile
   - Enable "Enable web services for mobile devices"

2. **Check site URL:**

   - Must be HTTPS in production
   - Must be publicly accessible

3. **Test web service:**
   ```bash
   curl https://your-domain.com/login/token.php
   ```

### Token Authentication Failed

**Solutions:**

1. **Check external services enabled:**

   - Site Administration > Advanced features
   - Enable "Web services"

2. **Create test token:**
   - Site Admin > Plugins > Web services > Manage tokens

---

## Branding Issues

### Logo Not Showing

**Solutions:**

1. **Check file exists:**

   ```bash
   ls -la public/theme/masbrand/pix/logo.png
   ```

2. **Check file permissions:**

   ```bash
   chmod 644 public/theme/masbrand/pix/logo.png
   ```

3. **Clear theme caches:**

   ```bash
   docker exec moodle_app php /var/www/html/public/admin/cli/purge_caches.php
   ```

4. **Force browser refresh:** Ctrl+Shift+R

### Colors Not Applying

**Solutions:**

1. **Verify JSON syntax:**

   ```bash
   cat whitelabel-config.json | python -m json.tool
   ```

2. **Re-run apply script:**

   ```bash
   php apply-whitelabel.php
   ```

3. **Rebuild:**

   ```bash
   docker-compose up -d --build
   ```

4. **Clear caches:**
   ```bash
   docker exec moodle_app php /var/www/html/public/admin/cli/purge_caches.php
   ```

### Moodle Text Still Visible

**Solutions:**

1. **Add more string replacements** in whitelabel-config.json
2. **Check renderer was generated:**
   ```bash
   cat public/theme/masbrand/classes/output/core_renderer.php
   ```
3. **Some strings require language pack edits**

---

## Diagnostic Commands

### Quick Health Check

```bash
# Container status
docker-compose ps

# All logs
docker-compose logs --tail 50

# Test health endpoint
curl http://localhost:8080/healthcheck.php

# Database status
docker exec moodle_db pg_isready

# Redis status
docker exec moodle_redis redis-cli ping

# PHP info
docker exec moodle_app php -v

# Disk space
df -h
```

### Detailed Diagnostics

```bash
# Full container logs
docker-compose logs > debug.log 2>&1

# Check Moodle configuration
docker exec moodle_app cat /var/www/html/config.php

# List Moodle data contents
docker exec moodle_app ls -la /var/www/moodledata

# Check PHP configuration
docker exec moodle_app php -i | grep memory_limit
```

---

## Getting Help

If issues persist:

1. Check container logs: `docker-compose logs`
2. Review this troubleshooting guide
3. Check DEPLOYMENT.md for setup issues
4. Open a GitHub issue with:
   - Error messages
   - Container logs
   - Environment (Coolify, VPS, EC2)

---

**Don't give up! 💪**
