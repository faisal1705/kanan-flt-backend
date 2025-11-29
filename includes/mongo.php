<?php

require __DIR__ . '/../vendor/autoload.php';

function getMongo()
{
    static $client = null;

    if ($client === null) {
        $uri = getenv("MONGO_URI"); // from Render Environment Variable
        $client = new MongoDB\Client($uri);
    }

    return $client->selectDatabase("kanan_flt"); // db name
}
