<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/sheet_api.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/mailer.php'; // <--- ADDED THIS
require_once __DIR__ . '/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

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

// Weekly registration window: Tue-Thu
if (!in_array($phpDay, REG_OPEN_DAYS, true)) {
    if ($email !== '') {
        $subject = 'FLT Booking Not Open';
        $msg = "Hello,\n\nRegistration opens every Tuesday and closes on Thursday.\nPlease try again during that period.";
        // UPDATED TO PHPMAILER
        sendEmail($email, $subject, nl2br($msg)); 
    }
    die('Registration is currently closed.');
}

// Find student
$student = findStudentByPhone($phone);
if (!$student) {
    if ($email !== '') {
        $subject = 'FLT Booking Not Successful';
        $msg = "Hello,\n\nYour number is not registered in our system. Please contact the administrator.";
        // UPDATED TO PHPMAILER
        sendEmail($email, $subject, nl2br($msg));
    }
    die('Number not registered.');
}
if ($email === '' && !empty($student['email'])) {
    $email = $student['email'];
}

// Settings
$saturdayStatus = strtoupper(getSetting('Saturday', 'ON'));
$sundayStatus   = strtoupper(getSetting('Sunday', 'ON'));
$adminEmail     = getSetting('AdminEmail', '');

// Holiday validation
if (
    (strpos($fltDay, 'Saturday') !== false && $saturdayStatus === 'OFF') ||
    (strpos($fltDay, 'Sunday')   !== false && $sundayStatus   === 'OFF')
) {
    if ($email !== '') {
        $subject = 'FLT Booking Closed - Holiday';
        $msg = "Hello {$student['name']},\n\nFLT is not conducted on {$fltDay}. Please check the updated schedule or contact the administrator.";
        // UPDATED TO PHPMAILER
        sendEmail($email, $subject, nl2br($msg));
    }
    die('FLT not conducted on selected day.');
}

// Duplicate booking
if (hasExistingBooking($phone)) {
    if ($email !== '') {
        $subject = 'Duplicate FLT Submission';
        $msg = "Hello {$student['name']},\n\nIt seems you have already booked your FLT. Duplicate submission removed.";
        // UPDATED TO PHPMAILER
        sendEmail($email, $subject, nl2br($msg));
    }
    die('You have already booked FLT.');
}

// Validate PB/CBT
if (
    ($fltDay === 'Saturday- Slot 1' || $fltDay === 'Saturday- Slot 2') &&
    $fltType === 'CBT'
) {
    if ($email !== '') {
        $subject = 'FLT Booking Failed - Invalid Type';
        $msg = "Hello {$student['name']},\n\nCBT option is only available on Sunday. Please select \"Pen Paper\" for Saturday slots.";
        // UPDATED TO PHPMAILER
        sendEmail($email, $subject, nl2br($msg));
    }
    die('CBT only allowed on Sunday.');
}

// Slot ranges
$minNo = $maxNo = null;
if ($fltDay === 'Saturday- Slot 1') {
    $minNo = 1;  $maxNo = 62;
} elseif ($fltDay === 'Saturday- Slot 2') {
    $minNo = 63; $maxNo = 100;
} elseif ($fltDay === 'Sunday') {
    $minNo = 101; $maxNo = 150;
} else {
    if ($email !== '') {
        // UPDATED TO PHPMAILER
        sendEmail($email, 'FLT Booking Failed - Invalid Slot', "Hello {$student['name']}<br><br>The selected slot \"{$fltDay}\" is invalid.");
    }
    die('Invalid slot selected.');
}

// FLT date
if (strpos($fltDay, 'Saturday') !== false) {
    $fltDate = getNextDayOfWeek($today, 6);
} else {
    $fltDate = getNextDayOfWeek($today, 0);
}

// Candidate number
$usedNos = getUsedCandidateNumbersInRange($minNo, $maxNo);
$newFltNo = null;
if (empty($usedNos)) {
    $newFltNo = $minNo;
} else {
    for ($i = $minNo; $i <= $maxNo; $i++) {
        if (!in_array($i, $usedNos, true)) {
            $newFltNo = $i;
            break;
        }
    }
}

if ($newFltNo === null) {
    if ($email !== '') {
        // UPDATED TO PHPMAILER
        sendEmail($email, 'FLT Booking Failed - Slot Full', "Hello {$student['name']}<br><br>The selected slot ({$fltDay}) is full. Please try another.");
    }
    die('Selected slot is full.');
}

$pdo = getPDO();
$venue = 'Kanan.co Bharuch';

if ($fltDay === 'Saturday- Slot 1') {
    $speakingTime = '09:30:00';
    $writingTime  = '10:30:00';
} elseif ($fltDay === 'Saturday- Slot 2') {
    $speakingTime = '10:30:00';
    $writingTime  = '11:30:00';
} else {
    $speakingTime = '09:30:00';
    $writingTime  = '10:30:00';
}

// Insert booking
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

// Send to Google Sheet
$fltDateStr = $fltDate->format('d-m-Y');
appendBookingToSheet([
    $newFltNo,
    $student['name'],
    $student['phone'],
    $email,
    $fltDay,
    $fltType,
    $fltDateStr,
    $speakingTime,
    $writingTime,
    $venue,
    date('d-m-Y H:i:s')
]);

// Confirmation email (HTML)
$fltDateDisplay = $fltDate->format('d/m/Y');
$htmlBody = "
<div style=\"font-family: Arial, sans-serif; color: #333;\">
  <div style=\"background-color:#f0f7ff; padding:20px; border:1px solid #bcd4ff; border-radius:8px; max-width:600px; margin:auto;\">
    <h2 style=\"color:#0b5394; text-align:center;\">Test Booking Confirmation</h2>
    <p>Hello <b>{$student['name']}</b>,</p>
    <p>Your FLT has been <b>successfully booked</b>. Please find the details below:</p>
    <table style=\"border-collapse: collapse; width:100%; margin-top:10px;\">
      <tr><td style=\"padding:8px; border-bottom:1px solid #ddd;\"><b>Candidate No:</b></td><td style=\"padding:8px; border-bottom:1px solid #ddd;\">{$newFltNo}</td></tr>
      <tr><td style=\"padding:8px; border-bottom:1px solid #ddd;\"><b>FLT Date:</b></td><td style=\"padding:8px; border-bottom:1px solid #ddd;\">{$fltDateDisplay}</td></tr>
      <tr><td style=\"padding:8px; border-bottom:1px solid #ddd;\"><b>FLT Day:</b></td><td style=\"padding:8px; border-bottom:1px solid #ddd;\">{$fltDay}</td></tr>
      <tr><td style=\"padding:8px; border-bottom:1px solid #ddd;\"><b>Mode:</b></td><td style=\"padding:8px; border-bottom:1px solid #ddd;\">{$fltType}</td></tr>
      <tr><td style=\"padding:8px; border-bottom:1px solid #ddd;\"><b>Venue:</b></td><td style=\"padding:8px; border-bottom:1px solid #ddd;\">{$venue}</td></tr>
      <tr><td style=\"padding:8px; border-bottom:1px solid #ddd;\"><b>Speaking Starts At:</b></td><td style=\"padding:8px; border-bottom:1px solid #ddd;\">" . date('h:i A', strtotime($speakingTime)) . "</td></tr>
      <tr><td style=\"padding:8px; border-bottom:1px solid #ddd;\"><b>Writing Starts At:</b></td><td style=\"padding:8px; border-bottom:1px solid #ddd;\">" . date('h:i A', strtotime($writingTime)) . "</td></tr>
    </table>
    <p style=\"margin-top:20px;\">Please arrive <b>15 minutes early</b> to complete initial formalities.</p>
    <p style=\"font-size:13px; color:#666;\">In case of any issues, contact the admin at
      <a href=\"mailto:{$adminEmail}\" style=\"color:#0b5394;\">{$adminEmail}</a>.
    </p>
    <p style=\"margin-top:25px; text-align:center; font-weight:bold;\">Best of luck for your Test!</p>
  </div>
  <p style=\"text-align:center; font-size:12px; color:#777; margin-top:15px;\">
    — This is an automated email from <b>Kanan FLT System</b> —
  </p>
</div>
";

if ($email !== '') {
    // UPDATED TO PHPMAILER - No need for manual headers anymore
    $subject = 'Your FLT Booking Confirmation';
    sendEmail($email, $subject, $htmlBody);
}

header('Location: success.php?candidate=' . urlencode($newFltNo));
exit;
?>
