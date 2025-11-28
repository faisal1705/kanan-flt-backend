<?php
require_once __DIR__ . '/includes/config.php';
try {
    $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "Connected to DB host: " . htmlspecialchars(DB_HOST) . "<br>";
    $c = $pdo->query("SELECT COUNT(*) AS c FROM flt_bookings")->fetch(PDO::FETCH_ASSOC)['c'] ?? 'N/A';
    echo "flt_bookings rows: " . htmlspecialchars($c) . "<br>";
    $c2 = $pdo->query("SELECT COUNT(*) AS c FROM students")->fetch(PDO::FETCH_ASSOC)['c'] ?? 'N/A';
    echo "students rows: " . htmlspecialchars($c2) . "<br>";
} catch (Exception $e) {
    echo "DB connect error: " . htmlspecialchars($e->getMessage());
}
