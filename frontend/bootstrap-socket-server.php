<?php

/**
 * Loads the environment bootstrap in the console application context
 */
$config=dirname(__FILE__).'/protected/config/main.php';
$console = dirname(__FILE__).'/protected/config/console.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG', true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));

// change the following paths if necessary
$yii=dirname(__FILE__).'/../classes/yii/yii.php';

require (dirname(__DIR__)) . '/frontend/protected/vendor/autoload.php';
require_once($yii);
require_once 'ConsoleSocketApplication.php';

$app = Yii::createApplication('ConsoleSocketApplication', 
    CMap::mergeArray(
        include($config),
        include($console)
    )
);
$app->commandRunner->addCommands(YII_PATH . '/cli/commands');
return $app;
