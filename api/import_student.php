<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

require_once __DIR__ . '/includes/db.php';

// This endpoint receives JSON array from Apps Script and upserts into students table
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
    http_response_code(400);
    echo 'Invalid JSON';
    exit;
}

$pdo = getPDO();
foreach ($data as $row) {
    // Adjust headers exactly as in your Consolidated Sheet
    $phone   = trim($row['Phone Number'] ?? '');
    if ($phone === '') continue;

    $name    = $row['Name'] ?? '';
    $email   = $row['Email'] ?? '';
    $alt     = $row['Alt. No.'] ?? '';
    $enMonth = $row['Enrollment Month'] ?? '';
    $enDate  = $row['Enrollment Date'] ?? '';
    $stdCode = $row['Student Code'] ?? '';
    $crmCode = $row['CRM Code'] ?? '';
    $batch   = $row['Batch'] ?? '';
    $fac     = $row['Faculty'] ?? '';
    $rm      = $row['RM'] ?? '';
    $status  = $row['Status 1'] ?? '';

    $dateSql = null;
    if (!empty($enDate)) {
        $ts = strtotime($enDate);
        if ($ts) {
            $dateSql = date('Y-m-d', $ts);
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO students
        (phone, name, email, alt_phone, enrollment_month, enrollment_date,
         student_code, crm_code, batch, faculty_name, rm, status)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
        ON DUPLICATE KEY UPDATE
           name = VALUES(name),
           email = VALUES(email),
           alt_phone = VALUES(alt_phone),
           enrollment_month = VALUES(enrollment_month),
           enrollment_date = VALUES(enrollment_date),
           student_code = VALUES(student_code),
           crm_code = VALUES(crm_code),
           batch = VALUES(batch),
           faculty_name = VALUES(faculty_name),
           rm = VALUES(rm),
           status = VALUES(status)
    ");

    $stmt->execute([
        $phone,
        $name,
        $email,
        $alt,
        $enMonth,
        $dateSql,
        $stdCode,
        $crmCode,
        $batch,
        $fac,
        $rm,
        $status
    ]);
}

echo 'OK';
