<?php
// change the following paths if necessary
//$yiic=dirname(__FILE__).'/../../classes/yii/yiic.php';
//$config=dirname(__FILE__).'/config/console.php';
//
//require_once($yiic);

// change the following paths if necessary
$app = require(dirname(__DIR__).'/bootstrap-socket-server.php');
$app->run();