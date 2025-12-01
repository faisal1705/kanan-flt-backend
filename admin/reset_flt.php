<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();

$pdo = getPDO();

// delete all bookings
$pdo->query("DELETE FROM flt_bookings");

header("Location: dashboard.php?reset=success");
exit;
