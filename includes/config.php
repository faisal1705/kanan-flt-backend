<?php
// includes/config.php

// 1. TiDB Cloud Credentials (Extracted from your link)
define('DB_HOST', 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com');
define('DB_PORT', '4000');
define('DB_NAME', 'kananFLT'); 
define('DB_USER', '2dn5mH27LHWdAG1.root');
define('DB_PASS', 'IP2hRzIhmFcvjZs1');

// 2. Base URL (Auto-detects Render URL)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
define('BASE_URL', getenv('RENDER_EXTERNAL_URL') ?: $protocol . "://" . $_SERVER['HTTP_HOST']);

// 3. Timezone & Settings
date_default_timezone_set('Asia/Kolkata');
define('REG_OPEN_DAYS', [2,3,4]); // Tue, Wed, Thu

// 4. Admin Credentials (CHANGE THESE!)
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123'); 

// 5. CORS Headers (Allows InfinityFree to talk to Render)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>
