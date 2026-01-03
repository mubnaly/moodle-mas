<?php
// Simple healthcheck that bypasses full Moodle bootstrap for speed and reliability in container orchestration
define('CLI_SCRIPT', true);
define('ABORT_AFTER_CONFIG', true);
define('CACHE_DISABLE_ALL', true); // Disable caches to avoid engaging Redis/etc if valid

// Load only essential Moodle configuration
// We need to know where config.php is.
// If this file is in /public/healthcheck.php, and config is in /public/config.php or /config.php
// The repo structure shows config.php is usually in public/ or root. 

$configfile = __DIR__ . '/config.php';
if (!file_exists($configfile)) {
    // If config doesn't exist, we might be in installation mode.
    // Return 200 so the container doesn't restart during install.
    header("HTTP/1.1 200 OK");
    echo "OK: No config";
    exit;
}

require_once($configfile);

// Check Database Connection
try {
    $db = $DB->get_dbh();
    // Simple query to verify DB is alive
    $DB->get_records_sql('SELECT 1', null, 0, 1);
    header("HTTP/1.1 200 OK");
    echo "OK";
} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Error: Database connection failed";
    exit(1);
}
