<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

require_once __DIR__ . '/../vendor/autoload.php';

function appendBookingToSheet(array $row)
{
    $serviceJson = getenv('GOOGLE_SERVICE_JSON');

    if (!$serviceJson) {
        error_log("SHEET ERROR: GOOGLE_SERVICE_JSON variable is missing.");
        return;
    }

    $config = json_decode($serviceJson, true);

    if (!$config) {
        error_log("SHEET ERROR: Invalid JSON format.");
        return;
    }

    // --- CRITICAL FIX: Repair Private Key ---
    // Fixes "Invalid JWT Signature" error
    if (isset($config['private_key'])) {
        $config['private_key'] = str_replace('\\n', "\n", $config['private_key']);
    }

    try {
        $client = new Google_Client();
        $client->setAuthConfig($config);
        $client->setScopes(['https://www.googleapis.com/auth/spreadsheets']);

        $service = new Google_Service_Sheets($client);
        
        // Use your NEW Spreadsheet ID (from your code snippet)
        $spreadsheetId = '1ySw_eks9muyPgH3cblOnmtNSn9IuHI8XyykO334UeoQ'; 
        $range = 'FLT Booking!A:Z';

        $body = new Google_Service_Sheets_ValueRange([
            'values' => [ $row ]
        ]);
        $params = ['valueInputOption' => 'USER_ENTERED'];

        $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
        error_log("SHEET SUCCESS: Data appended.");

    } catch (Exception $e) {
        error_log('SHEET FAILED: ' . $e->getMessage());
    }
}
?>
