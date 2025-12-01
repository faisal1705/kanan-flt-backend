<?php
require_once __DIR__ . '/config.php';

function getPDO() {

    static $pdo = null;
    if ($pdo) return $pdo;

    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";

    $sslPath = __DIR__ . '/cacert.pem';

    if (!file_exists($sslPath)) {
        die("<h1>Error: SSL Certificate Missing</h1>
             <p>Upload <b>cacert.pem</b> to: /includes folder.</p>");
    }

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,

        PDO::MYSQL_ATTR_SSL_CA => $sslPath,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false // important for TiDB Serverless
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        die("<h1>Database Connection Failed</h1>Error: " . $e->getMessage());
    }

    return $pdo;
}
?>
