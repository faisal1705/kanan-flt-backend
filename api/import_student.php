<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

require_once __DIR__ . '/../includes/db.php';

// Receive JSON from Apps Script
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
    http_response_code(400);
    echo "Invalid JSON";
    exit;
}

$pdo = getPDO();

foreach ($data as $row) {

    // Ensure phone exists (acts as unique key)
    $phone = trim($row['Phone Number'] ?? '');
    if ($phone === '') continue;

    // Extract values
    $name       = $row['Name'] ?? '';
    $email      = $row['Email Address'] ?? '';
    $alt_phone  = $row['Alt. No.'] ?? '';
    $month      = $row['Enrollment Month'] ?? '';
    $date_raw   = $row['Enrollment Date'] ?? '';
    $std_code   = $row['Student Code'] ?? '';
    $crm_code   = $row['CRM Code'] ?? '';
    $batch      = $row['Batch'] ?? '';
    $faculty    = $row['Faculty'] ?? '';
    $rm         = $row['RM'] ?? '';
    $status     = $row['Status 1'] ?? '';

    // Convert to SQL date
    $dateSql = null;
    if (!empty($date_raw)) {
        $ts = strtotime($date_raw);
        if ($ts) $dateSql = date('Y-m-d', $ts);
    }

    // UPSERT QUERY
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
        $phone, $name, $email, $alt_phone, $month, $dateSql,
        $std_code, $crm_code, $batch, $faculty, $rm, $status
    ]);
}

echo "OK";
