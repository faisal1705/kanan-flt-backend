<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();

$pdo = getPDO();
$q = strtolower(trim($_GET['q'] ?? ''));

$query = "
    SELECT *
    FROM students
    WHERE 
        LOWER(name) LIKE ? OR 
        LOWER(phone) LIKE ? OR
        LOWER(email) LIKE ? OR
        LOWER(student_code) LIKE ? OR
        LOWER(batch) LIKE ?
    ORDER BY updated_at DESC
    LIMIT 200
";

$like = "%$q%";
$stmt = $pdo->prepare($query);
$stmt->execute([$like, $like, $like, $like, $like]);

echo json_encode($stmt->fetchAll());
