<?php

/**
 * Loads the environment bootstrap in the console application context
 */

require 'bootstrap.php';

$common = __DIR__ . '/protected/config/common.php';
$console = __DIR__ . '/protected/config/console.php';
$bindings = __DIR__ . '/protected/config/bindings.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG', env('APP_DEBUG'));
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', env('APP_TRACE_LEVEL'));

defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));

// change the following paths if necessary
$yii = __DIR__ . '/protected/vendor/yiisoft/yii/framework/yii.php';

require_once($yii);

require_once 'ConsoleApplication.php';

$app = Yii::createApplication('ConsoleApplication',
    CMap::mergeArray(
        include($common),
        include($console),
        array('bindings' => include($bindings))
    )
);

$app->commandRunner->addCommands(YII_PATH . '/cli/commands');

return $app;
