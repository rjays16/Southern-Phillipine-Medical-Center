<?php
include __DIR__ . "/../../../inc_init_main.php";
return array(
    'HOST' => $telemed_host,
    'TELEMED_TOKEN' => $telemed_token,
    'URS' => '/api/authService/urs',

    'HIS_PRIVATE_KEY' => $telemed_token,
    'TELEMED_PATH' => '/include/care_api_classes/telemed',


    // HIS DB Config <optional>
    // 'DB_HOST' => '192.168.1.111',
    // 'DB_NAME' => 'hisdb',
    // 'DB_USER' => 'hisdbuser',
    // 'DB_PASS' => 'hisDB',
    'DB_HOST' => $dbhost,
    'DB_NAME' => $dbname,
    'DB_USER' => $dbusername,
    'DB_PASS' => $dbpassword,
);