<?php
require_once __DIR__ . '/vendor/autoload.php'; // composer autoload

use MongoDB\Client;

function getMongoClient() {
    static $client = null;
    if ($client === null) {
        // prefer an env var on Render: MONGO_URI
        $uri = getenv('MONGO_URI') ?: 'mongodb+srv://kanan_db_user:faisal0902@kananflt.ujcfrsr.mongodb.net/?appName=kananFLT';
        $client = new Client($uri);
    }
    return $client;
}

function getMongoDB($dbName = null) {
    $client = getMongoClient();
    if ($dbName === null) $dbName = getenv('MONGO_DB') ?: 'kananflt';
    return $client->selectDatabase($dbName);
}
