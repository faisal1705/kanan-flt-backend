<?php
// book.php - Final Fixed Version

// 1. CORS Headers (Allows your frontend to talk to this script)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

// 2. Load Dependencies
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/sheet_api.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/mailer.php';
require_once __DIR__ . '/vendor/autoload.php';

// Handle Pre-flight checks
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// 3. Get & Validate Inputs
$phone   = trim($_POST['phone'] ?? '');
$email   = trim($_POST['email'] ?? '');
$fltDay  = trim($_POST['flt_day'] ?? '');
$fltType = trim($_POST['flt_type'] ?? '');

if ($phone === '' || $fltDay === '' || $fltType === '') {
    die('Missing required fields.');
}

$today = new DateTime('now');
$phpDay = (int)$today->format('N'); // 1=Mon..7=Sun

if (!preg_match('/^\d{10}$/', $phone)) {
    die("Invalid phone number. Must be 10 digits.");
}

// 4. Registration Window Check
if (!in_array($phpDay, REG_OPEN_DAYS, true)) {
    if ($email !== '') {
        $subject = 'FLT Booking Not Open';
        $msg = "Hello,<br><br>Registration opens every Tuesday and closes on Thursday.<br>Please try again during that period.";
        try { sendEmail($email, $subject, $msg); } catch (Exception $e) {}
    }
    die('Registration is currently closed.');
}

// 5. Find Student in DB
$student = findStudentByPhone($phone);
if (!$student) {
    if ($email !== '') {
        $subject = 'FLT Booking Not Successful';
        $msg = "Hello,<br><br>Your number is not registered in our system. Please contact the administrator.";
        try { sendEmail($email, $subject, $msg); } catch (Exception $e) {}
    }
    die('Number not registered.');
}
// Use registered email if user input is empty
if ($email === '' && !empty($student['email'])) {
    $email = $student['email'];
}

// 6. Check Holidays
$saturdayStatus = strtoupper(getSetting('Saturday', 'ON'));
$sundayStatus   = strtoupper(getSetting('Sunday', 'ON'));

if (
    (strpos($fltDay, 'Saturday') !== false && $saturdayStatus === 'OFF') ||
    (strpos($fltDay, 'Sunday')   !== false && $sundayStatus   === 'OFF')
) {
    if ($email !== '') {
        $subject = 'FLT Booking Closed - Holiday';
        $msg = "Hello {$student['name']},<br><br>FLT is not conducted on {$fltDay}. Please check the updated schedule.";
        try { sendEmail($email, $subject, $msg); } catch (Exception $e) {}
    }
    die('FLT not conducted on selected day.');
}

// 7. Check Duplicate Booking
if (hasExistingBooking($phone)) {
    if ($email !== '') {
        $subject = 'Duplicate FLT Submission';
        $msg = "Hello {$student['name']},<br><br>It seems you have already booked your FLT. Duplicate submission removed.";
        try { sendEmail($email, $subject, $msg); } catch (Exception $e) {}
    }
    die('You have already booked FLT.');
}

// 8. Validate Type (CBT/Pen-Paper)
if (
    ($fltDay === 'Saturday- Slot 1' || $fltDay === 'Saturday- Slot 2') &&
    $fltType === 'CBT'
) {
    if ($email !== '') {
        $subject = 'FLT Booking Failed - Invalid Type';
        $msg = "Hello {$student['name']},<br><br>CBT option is only available on Sunday. Please select \"Pen Paper\" for Saturday slots.";
        try { sendEmail($email, $subject, $msg); } catch (Exception $e) {}
    }
    die('CBT only allowed on Sunday.');
}

// 9. Slot Calculation & Candidate No
$minNo = ($fltDay === 'Saturday- Slot 1') ? 1 : (($fltDay === 'Saturday- Slot 2') ? 63 : 101);
$maxNo = ($fltDay === 'Saturday- Slot 1') ? 62 : (($fltDay === 'Saturday- Slot 2') ? 100 : 150);

$usedNos = getUsedCandidateNumbersInRange($minNo, $maxNo);
$newFltNo = null;

for ($i = $minNo; $i <= $maxNo; $i++) {
    if (!in_array($i, $usedNos, true)) {
        $newFltNo = $i;
        break;
    }
}

if ($newFltNo === null) {
    if ($email !== '') {
        try { sendEmail($email, 'FLT Booking Failed - Slot Full', "Hello {$student['name']}<br><br>The selected slot ({$fltDay}) is full. Please try another."); } catch (Exception $e) {}
    }
    die('Selected slot is full.');
}

// 10. Date Logic
if (strpos($fltDay, 'Saturday') !== false) {
    $fltDate = getNextDayOfWeek($today, 6);
    $speakingTime = ($fltDay === 'Saturday- Slot 1') ? '09:30:00' : '10:30:00';
    $writingTime  = ($fltDay === 'Saturday- Slot 1') ? '10:30:00' : '11:30:00';
} else {
    $fltDate = getNextDayOfWeek($today, 0);
    $speakingTime = '09:30:00';
    $writingTime  = '10:30:00';
}
$venue = 'Kanan.co Bharuch';

// ---------------------------------------------------------
// EXECUTION PHASE (Try/Catch to prevent crashing)
// ---------------------------------------------------------

// A. Save to Database
$pdo = getPDO();
$stmt = $pdo->prepare(
    "INSERT INTO flt_bookings
    (student_id, candidate_no, flt_day, flt_type, flt_date, speaking_time, writing_time, venue)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);
$stmt->execute([
    $student['id'],
    $newFltNo,
    $fltDay,
    $fltType,
    $fltDate->format('Y-m-d'),
    $speakingTime,
    $writingTime,
    $venue
]);

// B. Send to Google Sheet (Updated Column Mapping)
try {
    $fltDateStr = $fltDate->format('d-m-Y');
    
    // Exact mapping based on your Google Sheet Screenshot
    $sheetRow = [
        $newFltNo,                          // Col A: Sr
        $fltDay,                            // Col B: Slot
        $student['enrollment_month'] ?? '', // Col C: Enrollment Month
        $student['enrollment_date'] ?? '',  // Col D: Enrollment Date
        $student['student_code'] ?? '',     // Col E: Student Code
        $student['crm_code'] ?? '',         // Col F: CRM Code
        $student['name'],                   // Col G: Name
        $student['phone'],                  // Col H: Phone Number
        $student['alt_phone'] ?? '',        // Col I: Alt. No.
        $email,                             // Col J: Email Address
        $student['batch'] ?? '',            // Col K: Batch
        $student['faculty_name'] ?? '',     // Col L: Faculty Name
        $student['rm'] ?? '',               // Col M: RM
        $student['status'] ?? '',           // Col N: Status 1
        $fltDay,                            // Col O: Day
        $fltType,                           // Col P: PB/CBT
        ''                                  // Col Q: Attendance (Empty)
    ];

    appendBookingToSheet($sheetRow);
} catch (Exception $e) {
    error_log("SHEET ERROR: " . $e->getMessage());
}

// C. Send Confirmation Email
try {
    if ($email !== '') {
        $subject = 'Your FLT Booking Confirmation';
        $fltDateDisplay = $fltDate->format('d/m/Y');
        
        $htmlBody = "
        <div style=\"font-family: Arial, sans-serif; color: #333;\">
          <div style=\"background-color:#f0f7ff; padding:20px; border:1px solid #bcd4ff; border-radius:8px; max-width:600px; margin:auto;\">
            <h2 style=\"color:#0b5394; text-align:center;\">Test Booking Confirmation</h2>
            <p>Hello <b>{$student['name']}</b>,</p>
            <p>Your FLT has been <b>successfully booked</b>.</p>
            <table style=\"border-collapse: collapse; width:100%; margin-top:10px;\">
              <tr><td style=\"padding:8px; border-bottom:1px solid #ddd;\"><b>Candidate No:</b></td><td style=\"padding:8px; border-bottom:1px solid #ddd;\">{$newFltNo}</td></tr>
              <tr><td style=\"padding:8px; border-bottom:1px solid #ddd;\"><b>Date:</b></td><td style=\"padding:8px; border-bottom:1px solid #ddd;\">{$fltDateDisplay}</td></tr>
              <tr><td style=\"padding:8px; border-bottom:1px solid #ddd;\"><b>Slot:</b></td><td style=\"padding:8px; border-bottom:1px solid #ddd;\">{$fltDay}</td></tr>
              <tr><td style=\"padding:8px; border-bottom:1px solid #ddd;\"><b>Venue:</b></td><td style=\"padding:8px; border-bottom:1px solid #ddd;\">{$venue}</td></tr>
            </table>
            <p style=\"margin-top:20px;\">Please arrive 15 minutes early.</p>
          </div>
        </div>";

        sendEmail($email, $subject, $htmlBody);
    }
} catch (Exception $e) {
    error_log("EMAIL ERROR: " . $e->getMessage());
}

// D. Redirect to Success Page
header('Location: success.php?candidate=' . urlencode($newFltNo));
exit;
?>
