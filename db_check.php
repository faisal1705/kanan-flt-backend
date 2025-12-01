<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

try {
    $pdo = getPDO(); // secure SSL connection

    echo "Connected to DB host: " . htmlspecialchars(DB_HOST) . "<br>";

    // Count flt_bookings
    $c1 = $pdo->query("SELECT COUNT(*) AS c FROM flt_bookings")
             ->fetch(PDO::FETCH_ASSOC)['c'] ?? 'N/A';

    // Count students
    $c2 = $pdo->query("SELECT COUNT(*) AS c FROM students")
             ->fetch(PDO::FETCH_ASSOC)['c'] ?? 'N/A';

    echo "flt_bookings rows: " . $c1 . "<br>";
    echo "students rows: " . $c2 . "<br>";

} catch (Exception $e) {
    echo "DB connect error: " . $e->getMessage();
}
?>
