<?php
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '5432');
define('DB_NAME', getenv('DB_NAME') ?: 'camagru');
define('DB_USER', getenv('DB_USER') ?: 'sqluser');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: 'sqlpass');

try {
    $pdo = new PDO(
        "pgsql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME,
        DB_USER,
        DB_PASSWORD
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>