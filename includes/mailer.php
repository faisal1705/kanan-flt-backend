<?php
// includes/mailer.php
// Uses Resend API to send emails

function sendEmail($to, $subject, $html)
{
    $apiKey = getenv("RESEND_API_KEY"); // Store in Render Environment Variables

    if (!$apiKey) {
        error_log("RESEND_API_KEY is missing.");
        return false;
    }

    $payload = [
        "from" => "Kanan FLT System <noreply@kananflt.com>",
        "to" => [$to],
        "subject" => $subject,
        "html" => $html
    ];

    $ch = curl_init("https://api.resend.com/emails");

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer {$apiKey}",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        error_log("RESEND CURL ERROR: " . curl_error($ch));
        curl_close($ch);
        return false;
    }

    curl_close($ch);
    error_log("RESEND RESPONSE: " . $response);

    return true;
}
