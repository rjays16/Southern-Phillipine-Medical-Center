<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
require dirname(dirname(dirname(dirname(__FILE___)))).DIRECTORY_SEPARATOR.
    'include'.DIRECTORY_SEPARATOR.
    'inc_init_main.php';

return array(
    'basePath'=>dirname(dirname(__FILE__)),
    'name'=>'Embedded eClaims',

    'aliases' => array(
        'bootstrap' => dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.
            'extensions'.DIRECTORY_SEPARATOR.
            'yiibooster'
    ),

    // preloading 'log' component
    'preload'=>array('log'),

    // autoloading model and component classes
    'import'=>array(
        'application.models.*',
        'application.components.*',
    ),

    'modules'=>array(
        'billing' => array(),
        'phic' => array(),
        'eclaims' => array(),
        // uncomment the following to enable the Gii tool

        'gii'=>array(
            'class'=>'system.gii.GiiModule',
            'password'=>false,
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters'=>array('127.0.0.1','::1'),
        ),

    ),

    // application components
    'components'=>array(
        'bootstrap' => array(
            'class' => 'bootstrap.components.Bootstrap',
            'fontAwesomeCss' => true
        ),
        'format' => array(
            'class' => 'application.components.Formatter'
        ),
        'user'=>array(
            // enable cookie-based authentication
            'allowAutoLogin'=>false,
            'class' => 'WebUser',
            'loginUrl' => 'main/login.php',
            'autoUpdateFlash' => false, // disable the flash counter
        ),
        // uncomment the following to enable URLs in path-format
//      'urlManager'=>array(
//          'urlFormat'=>'path',
//          'rules'=>array(
//              '<controller:\w+>/<id:\d+>'=>'<controller>/view',
//              '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
//              '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
//          ),
//      ),
        'db'=>array(
            'connectionString' => 'mysql:host='.$dbhost.';dbname='.$dbname,
            'emulatePrepare' => true,
            'enableParamLogging' => true,
            'enableProfiling' => true,
            'username' => $dbusername,
            'password' => $dbpassword,
            'charset' => 'utf8',
        ),
        'errorHandler'=>array(
            // use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error, warning',
                ),
                // uncomment the following to show log messages on web pages
                // array(
                //  'class'=>'CWebLogRoute',
                // ),
            ),
        ),
        'session' => array(
            'class' => 'CareHttpSession'
        )
    ),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params'=>array(
        // this is used in contact page
        'adminEmail'=>'webmaster@example.com',
    ),
);