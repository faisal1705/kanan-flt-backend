<?php
/**
 * Google Sheets API helper (Fixed for Render/JWT Issues)
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

require_once __DIR__ . '/../vendor/autoload.php';

function appendBookingToSheet(array $row)
{
    // 1. Load Google Service Account JSON from Render environment
    $serviceJson = getenv('GOOGLE_SERVICE_JSON');

    if (!$serviceJson) {
        error_log("SHEET ERROR: GOOGLE_SERVICE_JSON variable is missing.");
        return;
    }

    $config = json_decode($serviceJson, true);

    if (!$config) {
        error_log("SHEET ERROR: Invalid JSON format in environment variable.");
        return;
    }

    // 2. CRITICAL FIX: Handle Newlines in Private Key
    // Render/Docker sometimes escapes '\n' as literal characters. This fixes it.
    if (isset($config['private_key'])) {
        $config['private_key'] = str_replace('\\n', "\n", $config['private_key']);
    }

    try {
        $client = new Google_Client();
        $client->setAuthConfig($config);
        $client->setScopes(['https://www.googleapis.com/auth/spreadsheets']);

        $service = new Google_Service_Sheets($client);

        // 3. Spreadsheet Configuration
        // Note: This is the NEW ID from your code. Make sure the Sheet is shared with the bot!
        $spreadsheetId = '1ySw_eks9muyPgH3cblOnmtNSn9IuHI8XyykO334UeoQ'; 
        $range = 'FLT Booking!A:Z';

        $body = new Google_Service_Sheets_ValueRange([
            'values' => [ $row ]
        ]);

        $params = ['valueInputOption' => 'USER_ENTERED'];

        // 4. Send Data
        $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
        error_log("SHEET SUCCESS: Data appended for row.");

    } catch (Exception $e) {
        // Log detailed error
        error_log('SHEET APPEND FAILED: ' . $e->getMessage());
    }
}
?>
