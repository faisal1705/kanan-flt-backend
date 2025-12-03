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

   // ... inside sendEmail function ...
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpUser;
    $mail->Password   = $smtpPass;
    
    // SWITCH TO SSL ON PORT 465 (More reliable on Render)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
    $mail->Port       = 465; 

    // ... rest of the code ...

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
