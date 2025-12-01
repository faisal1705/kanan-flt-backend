<?php
require_once __DIR__ . '/config.php';

function getPDO()
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        
        // Point to the file we just uploaded
        $sslPath = __DIR__ . '/cacert.pem';

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            
            // 1. Point to OUR certificate file
            PDO::MYSQL_ATTR_SSL_CA       => $sslPath,
            
            // 2. Disable strict verification (Helps prevent errors on some hosts)
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        ];

        // Debug check: Verify the file actually exists before connecting
        if (!file_exists($sslPath)) {
            die("<h1>Error: Certificate Missing</h1><p>Could not find the file at: $sslPath</p><p>Please upload cacert.pem to the includes folder.</p>");
        }

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("<h1>Database Connection Failed</h1><br>Error: " . $e->getMessage());
        }
    }
    return $pdo;
}
?>
