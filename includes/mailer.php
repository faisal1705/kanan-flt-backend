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
        // --- CRITICAL FIX START ---
        // Force IPv4 Address resolution
        // This fixes "Network unreachable" on Render/Docker
        $mail->Host = gethostbyname('smtp.gmail.com');
        // --- CRITICAL FIX END ---

        $mail->isSMTP();
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpUser;
        $mail->Password   = $smtpPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $mail->Port       = 587;

        // Relax SSL verification
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->setFrom($smtpUser, 'Kanan FLT System');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;

        $mail->send();
        error_log("EMAIL SUCCESS: Sent to " . $to);
        return true;

    } catch (Exception $e) {
        error_log("EMAIL FAILED: " . $mail->ErrorInfo);
        return false;
    }
}
?>
