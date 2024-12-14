<?php
// Load environment variables
function loadEnv($path = __DIR__) {
    $envFile = $path . '/.env';
    if (!file_exists($envFile)) {
        die('.env file not found. Please copy .env.example to .env and configure your settings.');
    }

    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue; // Skip comments
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        // Remove quotes if present
        if (strpos($value, '"') === 0 || strpos($value, "'") === 0) {
            $value = substr($value, 1, -1);
        }
        
        putenv("$name=$value");
        $_ENV[$name] = $value;
    }
}

// Load environment variables
loadEnv();

// PayPal Configuration
define('PAYPAL_CLIENT_ID', getenv('PAYPAL_CLIENT_ID'));
define('PAYPAL_CLIENT_SECRET', getenv('PAYPAL_CLIENT_SECRET'));
define('PAYPAL_CURRENCY', getenv('PAYPAL_CURRENCY'));
define('PAYPAL_MODE', getenv('PAYPAL_MODE'));
define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST']);

define('SITE_NAME', getenv('SITE_NAME'));
define('ITEMS_PER_PAGE', (int)getenv('ITEMS_PER_PAGE'));
?> 