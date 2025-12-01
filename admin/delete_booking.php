<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();

if (!isset($_GET['id'])) {
    die("Invalid request");
}

$id = (int) $_GET['id'];

$pdo = getPDO();
$stmt = $pdo->prepare("DELETE FROM flt_bookings WHERE id = ?");
$stmt->execute([$id]);

header("Location: bookings.php?deleted=1");
exit;
