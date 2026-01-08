<?php
// ==============================================================================
// Moodle Health Check Endpoint
// ==============================================================================
// This file provides a simple, fast health check for container orchestration
// (Docker, Kubernetes, Coolify, etc.)
//
// It performs minimal checks to verify the application is responsive:
// 1. PHP is running
// 2. Database connection is available
// 3. Core Moodle configuration is loaded
//
// Returns HTTP 200 OK if healthy, HTTP 500 if unhealthy
// ==============================================================================

// Bypass Moodle bootstrap for speed - we only need config
define('NO_MOODLE_COOKIES', true);
define('NO_OUTPUT_BUFFERING', true);

// Prevent any session handling
define('ABORT_AFTER_CONFIG', true);

// Disable caches to avoid Redis/Memcache dependency issues during health check
define('CACHE_DISABLE_ALL', true);

// Set headers for health check response
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Response data structure
$response = [
    'status' => 'healthy',
    'timestamp' => date('c'),
    'checks' => [
        'php' => 'ok',
        'config' => 'pending',
        'database' => 'pending'
    ]
];

// ==============================================================================
// Check 1: Configuration File Exists
// ==============================================================================
$configFile = __DIR__ . '/config.php';
$parentConfig = dirname(__DIR__) . '/config.php';

if (file_exists($configFile)) {
    $configPath = $configFile;
} elseif (file_exists($parentConfig)) {
    $configPath = $parentConfig;
} else {
    // No config file - installation mode
    // Return 200 so container doesn't restart during initial setup
    $response['status'] = 'installing';
    $response['checks']['config'] = 'not_found';
    $response['checks']['database'] = 'skipped';
    $response['message'] = 'Moodle configuration not found - installation pending';
    
    http_response_code(200);
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

// ==============================================================================
// Check 2: Load Configuration
// ==============================================================================
try {
    // We need to load config carefully without the full Moodle bootstrap
    // First, check if the config file is parseable
    $configContent = file_get_contents($configPath);
    
    if (empty($configContent)) {
        throw new Exception('Configuration file is empty');
    }
    
    $response['checks']['config'] = 'ok';
} catch (Exception $e) {
    $response['status'] = 'unhealthy';
    $response['checks']['config'] = 'error';
    $response['error'] = 'Configuration error: ' . $e->getMessage();
    
    http_response_code(500);
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

// ==============================================================================
// Check 3: Database Connection
// ==============================================================================
try {
    // Parse the config file to extract database credentials
    // We use a safe approach without eval
    
    // Get database settings from environment (Docker approach)
    $dbType = getenv('MOODLE_DBTYPE') ?: 'pgsql';
    $dbHost = getenv('MOODLE_DBHOST') ?: 'localhost';
    $dbName = getenv('MOODLE_DBNAME') ?: 'moodle';
    $dbUser = getenv('MOODLE_DBUSER') ?: 'moodleuser';
    $dbPass = getenv('MOODLE_DBPASSWORD') ?: '';
    $dbPort = getenv('MOODLE_DBPORT') ?: '5432';
    
    // Construct DSN based on database type
    if ($dbType === 'pgsql') {
        $dsn = "pgsql:host={$dbHost};port={$dbPort};dbname={$dbName}";
    } elseif ($dbType === 'mysqli' || $dbType === 'mariadb') {
        $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
        $dbPort = getenv('MOODLE_DBPORT') ?: '3306';
    } else {
        throw new Exception("Unsupported database type: {$dbType}");
    }
    
    // Attempt database connection
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5
    ]);
    
    // Simple query to verify database is responsive
    $stmt = $pdo->query('SELECT 1 as test');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result && $result['test'] == 1) {
        $response['checks']['database'] = 'ok';
    } else {
        throw new Exception('Database query returned unexpected result');
    }
    
    // Close connection
    $pdo = null;
    
} catch (PDOException $e) {
    $response['status'] = 'unhealthy';
    $response['checks']['database'] = 'error';
    $response['error'] = 'Database connection failed';
    // Don't expose detailed error in production
    if (getenv('MOODLE_DEBUG') === 'true') {
        $response['debug'] = $e->getMessage();
    }
    
    http_response_code(500);
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
} catch (Exception $e) {
    $response['status'] = 'unhealthy';
    $response['checks']['database'] = 'error';
    $response['error'] = $e->getMessage();
    
    http_response_code(500);
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

// ==============================================================================
// All Checks Passed
// ==============================================================================
$response['status'] = 'healthy';
$response['message'] = 'All systems operational';

http_response_code(200);
echo json_encode($response, JSON_PRETTY_PRINT);
exit;
