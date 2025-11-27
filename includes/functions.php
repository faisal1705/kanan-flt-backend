<?php
require_once __DIR__ . '/db.php';

function getSetting($key, $default = null) {
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? $row['setting_value'] : $default;
}

function setSetting($key, $value) {
    $pdo = getPDO();
    $stmt = $pdo->prepare(
        "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
    );
    $stmt->execute([$key, $value]);
}

// Get next occurrence of given weekday (0=Sunday .. 6=Saturday)
function getNextDayOfWeek(DateTime $start, int $dayOfWeek): DateTime {
    $date = clone $start;
    $currentDow = (int)$date->format('w'); // 0-6
    $diff = ($dayOfWeek + 7 - $currentDow) % 7;
    if ($diff === 0) $diff = 7;
    $date->modify("+$diff day");
    return $date;
}

function findStudentByPhone(string $phone) {
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT * FROM students WHERE phone = ?");
    $stmt->execute([$phone]);
    return $stmt->fetch();
}

function hasExistingBooking(string $phone): bool {
    $pdo = getPDO();
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) AS c
         FROM flt_bookings b
         JOIN students s ON b.student_id = s.id
         WHERE s.phone = ?"
    );
    $stmt->execute([$phone]);
    $row = $stmt->fetch();
    return $row && $row['c'] > 0;
}

function getUsedCandidateNumbersInRange(int $minNo, int $maxNo): array {
    $pdo = getPDO();
    $stmt = $pdo->prepare(
        "SELECT candidate_no FROM flt_bookings WHERE candidate_no BETWEEN ? AND ? ORDER BY candidate_no"
    );
    $stmt->execute([$minNo, $maxNo]);
    return array_column($stmt->fetchAll(), 'candidate_no');
}

function requireAdminLogin() {
    session_start();
    if (empty($_SESSION['admin_logged_in'])) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}
