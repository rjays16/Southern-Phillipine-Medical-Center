<?php

require './roots.php';
require_once $root_path.'include/inc_environment_global.php';

// change the following paths if necessary
$yii= $root_path.'classes/yii/yii.php';
$config= $root_path.'frontend/protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require $root_path.'frontend/protected/vendor/autoload.php';    
require_once($yii);

require_once $root_path.'frontend/SegHis.php';
$application = new SegHis($config);    
return $application;