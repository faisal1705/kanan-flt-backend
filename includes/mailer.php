<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function sendEmail($to, $subject, $htmlBody) {
    $mail = new PHPMailer(true);
    
    $smtpUser = getenv('MAIL_USER'); 
    $smtpPass = getenv('MAIL_PASS');

    if (!$smtpUser || !$smtpPass) {
        error_log("EMAIL ERROR: Credentials missing.");
        return false;
    }

    try {
        // 1. Force IPv4 (Fixes 'Network is unreachable' on Render)
        $mail->Host = gethostbyname('smtp.gmail.com'); 
        
        // 2. Server Settings
        $mail->isSMTP();
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpUser;
        $mail->Password   = $smtpPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use TLS
        $mail->Port       = 587;                            // Use Port 587

        // 3. Relax SSL Options (Prevents connection drops)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true
            )
        );

        // 4. Content
        $mail->setFrom($smtpUser, 'Kanan FLT System');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;

        $mail->send();
        error_log("Email sent successfully to: " . $to);
        return true;

    } catch (Exception $e) {
        error_log("EMAIL FAILED: " . $mail->ErrorInfo);
        return false;
    }
}
?>
