<?php
/**
 * Google Sheets API helper
 *
 * IMPORTANT:
 * - Upload your service_account.json file into the includes/ folder.
 * - Install Google API PHP Client library in /vendor via Composer:
 *      composer require google/apiclient:^2.15
 * - Set your FLT Booking sheet ID below.
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

require_once __DIR__ . '/../vendor/autoload.php';

function appendBookingToSheet(array $row)
{
    $serviceAccountPath = __DIR__ . '/service_account.json';
    if (!file_exists($serviceAccountPath)) {
        // If service account not present, just skip silently
        return;
    }

    $client = new Google_Client();
    $client->setAuthConfig($serviceAccountPath);
    $client->setScopes(['https://www.googleapis.com/auth/spreadsheets']);

    $service = new Google_Service_Sheets($client);

    // TODO: put your real FLT Booking spreadsheet ID
    $spreadsheetId = '1eYFFikCUwzfEMoBmoh6727amuOikhKrAdh3O8k3tIyo';
    $range = 'FLT Booking!A:Z';

    $body = new Google_Service_Sheets_ValueRange([
        'values' => [ $row ]
    ]);

    $params = ['valueInputOption' => 'USER_ENTERED'];

    try {
        $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
    } catch (Exception $e) {
        // You might log errors to a file instead
        error_log('Sheets append error: ' . $e->getMessage());
    }
}
