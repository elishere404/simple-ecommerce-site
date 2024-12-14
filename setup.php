<?php
// Directory paths
$projectRoot = realpath(__DIR__);

// Create json directory if it doesn't exist
if (!file_exists(__DIR__ . '/json')) {
    mkdir(__DIR__ . '/json', 0777, true);
    echo "Created json directory\n";
}

// Create .htaccess
$htaccess = <<<EOT
<IfModule mod_php.c>
    php_value error_reporting E_ALL
    php_flag display_errors on
</IfModule>

# Protect .env file
<Files .env>
    Order allow,deny
    Deny from all
</Files>
EOT;

file_put_contents('.htaccess', $htaccess);
echo "Created .htaccess file\n";

// Check if .env exists, if not copy from example
if (!file_exists('.env') && file_exists('.env.example')) {
    copy('.env.example', '.env');
    echo "Created .env file from .env.example\n";
    echo "Please update your .env file with your actual configuration values\n";
}

echo "\nSetup completed successfully!\n";
echo "Please make sure:\n";
echo "1. Your products.json file is in the json directory\n";
echo "2. Your .env file is configured with correct values\n";
  