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

    // 2. CRITICAL FIX: Repair the Private Key
    // Render often escapes newlines like "\\n" instead of "\n". This fixes it.
    if (isset($config['private_key'])) {
        // Replace literal \n with actual newlines
        $config['private_key'] = str_replace('\\n', "\n", $config['private_key']);
        // Ensure standard formatting
        $config['private_key'] = str_replace("PRIVATE KEY-----\n", "PRIVATE KEY-----\n", $config['private_key']);
    }

    try {
        $client = new Google_Client();
        $client->setAuthConfig($config);
        $client->setScopes(['https://www.googleapis.com/auth/spreadsheets']);

        $service = new Google_Service_Sheets($client);

        // 3. Spreadsheet Configuration
        // MAKE SURE THIS ID MATCHES YOUR NEW SHEET
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
