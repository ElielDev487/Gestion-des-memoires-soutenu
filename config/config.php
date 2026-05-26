<?php
// Base de donnees
define('DB_HOST',    'localhost');
define('DB_NAME',    'memoires_db');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

// Application
define('APP_NAME', 'Plateforme Memoires');
define('BASE_URL', 'http://localhost/memoires_platform/public');
define('ROOT_PATH', dirname(__DIR__));

// Stockage PDF (hors public)
define('STORAGE_PATH', ROOT_PATH . '/storage/memoires/');

// Upload
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10 Mo
define('ALLOWED_MIME',    'application/pdf');
