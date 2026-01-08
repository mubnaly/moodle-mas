# 📚 Moodle Backend Documentation

This folder contains all documentation, configuration templates, and guides for the Moodle Backend project.

---

## 📁 Contents

| File                                                   | Description                                      |
| ------------------------------------------------------ | ------------------------------------------------ |
| **[DEPLOYMENT.md](./DEPLOYMENT.md)**                   | Complete deployment guide for Coolify, VPS, EC2  |
| **[WHITELABEL.md](./WHITELABEL.md)**                   | How to apply white-labeling to create your brand |
| **[CONFIGURATION.md](./CONFIGURATION.md)**             | All configuration options explained              |
| **[TROUBLESHOOTING.md](./TROUBLESHOOTING.md)**         | Common issues and solutions                      |
| **[MOBILE_APP.md](./MOBILE_APP.md)**                   | Mobile app integration guide                     |
| **[whitelabel-config.json](./whitelabel-config.json)** | Brand customization template                     |
| **[env-template.txt](./env-template.txt)**             | Environment variables template                   |

---

## 🚀 Quick Links

- **First Time Setup?** → Start with [DEPLOYMENT.md](./DEPLOYMENT.md)
- **Want to Brand?** → Read [WHITELABEL.md](./WHITELABEL.md)
- **Having Issues?** → Check [TROUBLESHOOTING.md](./TROUBLESHOOTING.md)
- **Mobile App?** → See [MOBILE_APP.md](./MOBILE_APP.md)

---

## 📋 Quick Reference

### Essential Commands

```bash
# Start all services
docker-compose up -d

# View logs
docker-compose logs -f app

# Rebuild after changes
docker-compose up -d --build

# Clear Moodle caches
docker exec moodle_app php /var/www/html/public/admin/cli/purge_caches.php

# Run cron manually
docker exec moodle_app php /var/www/html/public/admin/cli/cron.php

# Check health
curl http://localhost:8080/healthcheck.php
```

### Environment Variables (Minimum Required)

```env
DOMAIN=lms.yourdomain.com
MOODLE_WWWROOT=https://lms.yourdomain.com
MOODLE_DBPASSWORD=your_secure_password
MOODLE_ADMIN_PASSWORD=YourSecureAdminPass123!
MOODLE_SSL_PROXY=true
```

---

## 📞 Support

For issues:

1. Check [TROUBLESHOOTING.md](./TROUBLESHOOTING.md)
2. Review container logs
3. Open a GitHub issue
