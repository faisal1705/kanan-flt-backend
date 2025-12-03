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
        // --- THE MAGIC FIX ---
        // Force PHP to resolve the IPv4 address of Gmail
        // This fixes "Network is unreachable" on Render
        $mail->Host = gethostbyname('smtp.gmail.com');
        
        $mail->isSMTP();
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpUser;
        $mail->Password   = $smtpPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Must use TLS
        $mail->Port       = 587;                            // Must use Port 587

        // Relax SSL rules (Prevents certificate errors)
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
