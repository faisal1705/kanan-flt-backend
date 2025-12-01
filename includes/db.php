<?php
require_once __DIR__ . '/config.php';

function getPDO()
{
    static $pdo = null;
    if ($pdo === null) {
        // We use the credentials from config.php
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            // CRITICAL: This line is required for TiDB Cloud!
            PDO::MYSQL_ATTR_SSL_CA       => '/etc/ssl/certs/ca-certificates.crt',
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // If connection fails, show the error message
            die('Database Connection Failed: ' . $e->getMessage());
        }
    }
    return $pdo;
}
?>
