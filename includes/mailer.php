<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function sendEmail($to, $subject, $htmlBody) {
    $mail = new PHPMailer(true);
    
    // 1. Grab credentials from Render Environment Variables
    $smtpUser = getenv('MAIL_USER'); 
    $smtpPass = getenv('MAIL_PASS');

    // 2. Security Check: Stop if keys are missing
    if (!$smtpUser || !$smtpPass) {
        error_log("EMAIL ERROR: MAIL_USER or MAIL_PASS variables are missing in Render.");
        return false;
    }

    try {
        // 3. Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpUser;
        $mail->Password   = $smtpPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Standard TLS
        $mail->Port       = 587;

        // 4. Sender & Recipient
        $mail->setFrom($smtpUser, 'Kanan FLT System');
        $mail->addAddress($to);

        // 5. Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;

        $mail->send();
        
        // Log success to Render console
        error_log("Email sent successfully to: " . $to);
        return true;

    } catch (Exception $e) {
        // Log the exact error from Gmail
        error_log("EMAIL FAILED: " . $mail->ErrorInfo);
        return false;
    }
}
?>
