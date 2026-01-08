# ⚙️ Configuration Reference

## All Configuration Options Explained

---

## 📋 Table of Contents

1. [Environment Variables](#environment-variables)
2. [Docker Configuration](#docker-configuration)
3. [PHP Configuration](#php-configuration)
4. [Database Configuration](#database-configuration)
5. [Redis Configuration](#redis-configuration)
6. [Security Settings](#security-settings)

---

## Environment Variables

### General Settings

| Variable               | Description           | Default          | Required |
| ---------------------- | --------------------- | ---------------- | -------- |
| `COMPOSE_PROJECT_NAME` | Docker project prefix | `moodle`         | No       |
| `DOCKER_IMAGE_NAME`    | Docker image name     | `moodle-backend` | No       |
| `DOCKER_IMAGE_TAG`     | Docker image tag      | `latest`         | No       |
| `TIMEZONE`             | Server timezone       | `UTC`            | No       |

### Application Settings

| Variable   | Description      | Default     | Required |
| ---------- | ---------------- | ----------- | -------- |
| `APP_PORT` | Port to expose   | `8080`      | No       |
| `DOMAIN`   | Your domain name | `localhost` | **Yes**  |

### Database Settings

| Variable            | Description       | Default      | Required   |
| ------------------- | ----------------- | ------------ | ---------- |
| `MOODLE_DBTYPE`     | Database type     | `pgsql`      | No         |
| `MOODLE_DBHOST`     | Database host     | `db`         | No         |
| `MOODLE_DBNAME`     | Database name     | `moodle`     | No         |
| `MOODLE_DBUSER`     | Database username | `moodleuser` | No         |
| `MOODLE_DBPASSWORD` | Database password | `moodlepass` | **Yes** ⚠️ |
| `MOODLE_DBPORT`     | Database port     | `5432`       | No         |
| `MOODLE_DBPREFIX`   | Table prefix      | `mdl_`       | No         |

### Moodle Site Settings

| Variable           | Description     | Default                 | Required |
| ------------------ | --------------- | ----------------------- | -------- |
| `MOODLE_WWWROOT`   | Full site URL   | `http://localhost:8080` | **Yes**  |
| `MOODLEDATA_PATH`  | Data directory  | `/var/www/moodledata`   | No       |
| `MOODLE_FULLNAME`  | Site full name  | `My Learning Platform`  | No       |
| `MOODLE_SHORTNAME` | Site short name | `LMS`                   | No       |

### Admin Settings

| Variable                | Description    | Default             | Required   |
| ----------------------- | -------------- | ------------------- | ---------- |
| `MOODLE_ADMIN`          | Admin username | `admin`             | No         |
| `MOODLE_ADMIN_PASSWORD` | Admin password | `Admin123!`         | **Yes** ⚠️ |
| `MOODLE_ADMIN_EMAIL`    | Admin email    | `admin@example.com` | **Yes**    |

### Redis Settings

| Variable          | Description    | Default   | Required |
| ----------------- | -------------- | --------- | -------- |
| `REDIS_HOST`      | Redis hostname | `redis`   | No       |
| `REDIS_PORT`      | Redis port     | `6379`    | No       |
| `REDIS_PASSWORD`  | Redis password | _(empty)_ | No       |
| `REDIS_MAXMEMORY` | Max memory     | `256mb`   | No       |

### SSL/Proxy Settings

| Variable           | Description      | Default | Required         |
| ------------------ | ---------------- | ------- | ---------------- |
| `MOODLE_SSL_PROXY` | Behind SSL proxy | `false` | **Yes** if HTTPS |

### PHP Settings

| Variable                  | Description         | Default | Required |
| ------------------------- | ------------------- | ------- | -------- |
| `PHP_MEMORY_LIMIT`        | PHP memory limit    | `512M`  | No       |
| `PHP_MAX_EXECUTION_TIME`  | Max script time     | `300`   | No       |
| `PHP_MAX_INPUT_VARS`      | Max input variables | `5000`  | No       |
| `PHP_UPLOAD_MAX_FILESIZE` | Max upload size     | `512M`  | No       |
| `PHP_POST_MAX_SIZE`       | Max POST size       | `512M`  | No       |

### Branding

| Variable     | Description | Default   | Required |
| ------------ | ----------- | --------- | -------- |
| `BRAND_NAME` | Brand name  | `MyBrand` | No       |

---

## Docker Configuration

### docker-compose.yml Services

| Service | Description         | Port                     |
| ------- | ------------------- | ------------------------ |
| `app`   | Moodle application  | 80 (internal) → APP_PORT |
| `db`    | PostgreSQL database | 5432 (internal)          |
| `redis` | Redis cache         | 6379 (internal)          |
| `cron`  | Background tasks    | None                     |

### Volumes

| Volume          | Purpose           | Path                       |
| --------------- | ----------------- | -------------------------- |
| `moodle_data`   | Moodle files      | `/var/www/moodledata`      |
| `moodle_logs`   | Log files         | `/var/log/moodle`          |
| `postgres_data` | Database          | `/var/lib/postgresql/data` |
| `redis_data`    | Redis persistence | `/data`                    |

### Health Checks

| Service | Check              | Interval | Timeout | Start Period |
| ------- | ------------------ | -------- | ------- | ------------ |
| `app`   | `/healthcheck.php` | 30s      | 10s     | 120s         |
| `db`    | `pg_isready`       | 10s      | 5s      | 30s          |
| `redis` | `redis-cli ping`   | 10s      | 5s      | 15s          |

---

## PHP Configuration

### Dockerfile Settings

The Dockerfile configures PHP with:

```ini
# OPcache
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=10000
opcache.revalidate_freq=0

# Uploads
upload_max_filesize=512M
post_max_size=512M
max_execution_time=300
max_input_vars=5000

# Memory
memory_limit=512M

# Security
display_errors=Off
expose_php=Off
session.cookie_secure=1
session.cookie_httponly=1
```

### Custom PHP Configuration

Edit `php-custom.ini` for additional settings:

```ini
; Override memory limit
memory_limit = 1G

; Custom error log
error_log = /var/log/moodle/php_errors.log
```

---

## Database Configuration

### PostgreSQL Settings

The docker-compose.yml includes optimized PostgreSQL settings:

| Setting                | Value | Purpose                |
| ---------------------- | ----- | ---------------------- |
| `max_connections`      | 200   | Maximum connections    |
| `shared_buffers`       | 256MB | Shared memory          |
| `effective_cache_size` | 768MB | Query planner hint     |
| `work_mem`             | 4MB   | Per-operation memory   |
| `maintenance_work_mem` | 128MB | Maintenance operations |

### Scaling Database

For larger installations, edit docker-compose.yml:

```yaml
db:
  command:
    - "postgres"
    - "-c"
    - "shared_buffers=512MB"
    - "-c"
    - "effective_cache_size=2GB"
    - "-c"
    - "max_connections=500"
```

---

## Redis Configuration

### Default Settings

```yaml
redis:
  command:
    - "--maxmemory"
    - "256mb"
    - "--maxmemory-policy"
    - "allkeys-lru"
```

### Scaling Redis

For larger installations:

```yaml
redis:
  command:
    - "--maxmemory"
    - "512mb"
    - "--maxmemory-policy"
    - "allkeys-lru"
```

---

## Security Settings

### Required for Production

| Setting                 | Value           | Reason            |
| ----------------------- | --------------- | ----------------- |
| `MOODLE_DBPASSWORD`     | Strong password | Database security |
| `MOODLE_ADMIN_PASSWORD` | Strong password | Admin access      |
| `MOODLE_SSL_PROXY`      | `true`          | HTTPS enforcement |

### Password Requirements

Moodle enforces (configurable):

- Minimum 8 characters
- At least 1 uppercase letter
- At least 1 lowercase letter
- At least 1 number
- At least 1 special character

### Security Headers

Apache is configured with:

- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: SAMEORIGIN`
- `X-XSS-Protection: 1; mode=block`
- `Referrer-Policy: strict-origin-when-cross-origin`

---

## Example Configurations

### Development

```env
COMPOSE_PROJECT_NAME=moodle-dev
DOMAIN=localhost
APP_PORT=8080
MOODLE_WWWROOT=http://localhost:8080
MOODLE_DBPASSWORD=devpassword
MOODLE_ADMIN_PASSWORD=Admin123!
MOODLE_SSL_PROXY=false
```

### Production (Coolify)

```env
COMPOSE_PROJECT_NAME=mybrand
DOMAIN=lms.mybrand.com
APP_PORT=8080
MOODLE_WWWROOT=https://lms.mybrand.com
MOODLE_DBPASSWORD=super_secure_password_here
MOODLE_ADMIN_PASSWORD=MySecureAdminPass123!
MOODLE_ADMIN_EMAIL=admin@mybrand.com
MOODLE_SSL_PROXY=true
PHP_MEMORY_LIMIT=1G
REDIS_MAXMEMORY=512mb
```

### High Traffic

```env
COMPOSE_PROJECT_NAME=enterprise
DOMAIN=lms.enterprise.com
MOODLE_WWWROOT=https://lms.enterprise.com
MOODLE_DBPASSWORD=enterprise_secure_password
MOODLE_ADMIN_PASSWORD=EnterpriseAdmin123!
MOODLE_SSL_PROXY=true
PHP_MEMORY_LIMIT=2G
REDIS_MAXMEMORY=1gb
```

---

**Configure wisely! ⚡**
