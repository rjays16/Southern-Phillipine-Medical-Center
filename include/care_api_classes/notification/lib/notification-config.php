<?php
include __DIR__ . "/../../../inc_init_main.php";
return array(
    'HOST' => $notification_host,
    'Notification_TOKEN' => $notification_token,
    'URS' => '/api/authService/urs',

    'HIS_PRIVATE_KEY' => $notification_token,
    'Notification_PATH' => '/include/care_api_classes/notification',


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