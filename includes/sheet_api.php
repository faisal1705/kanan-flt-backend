<?php
/**
 * Google Sheets API helper (Render-compatible version)
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

require_once __DIR__ . '/../vendor/autoload.php';

function appendBookingToSheet(array $row)
{
    // Load Google Service Account JSON from Render environment
    $serviceJson = getenv('GOOGLE_SERVICE_JSON');

    if (!$serviceJson) {
        error_log("Google service account JSON not found.");
        return;
    }

    $config = json_decode($serviceJson, true);

    if (!$config) {
        error_log("Invalid GOOGLE_SERVICE_JSON format.");
        return;
    }

    $client = new Google_Client();
    $client->setAuthConfig($config);
    $client->setScopes(['https://www.googleapis.com/auth/spreadsheets']);

    $service = new Google_Service_Sheets($client);

    // Your FLT Booking Sheet ID
    $spreadsheetId = '1ySw_eks9muyPgH3cblOnmtNSn9IuHI8XyykO334UeoQ';
    $range = 'FLT Booking!A:Z';

    $body = new Google_Service_Sheets_ValueRange([
        'values' => [ $row ]
    ]);

    $params = ['valueInputOption' => 'USER_ENTERED'];

    try {
        $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
    } catch (Exception $e) {
        error_log('Sheets append error: ' . $e->getMessage());
    }
}
