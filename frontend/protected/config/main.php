<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
require dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR.
    'include'.DIRECTORY_SEPARATOR.
    'inc_init_main.php';

$protectedDir = dirname(dirname(__FILE__));
require_once( dirname(__FILE__) . '/../components/helpers.php');

if (!env('APP_ENV')) {
    $env = new Dotenv\Dotenv(__DIR__, '.env');
    $env->load();    
}

$env = new Dotenv\Dotenv(__DIR__, '.env.'.env('APP_ENV', 'local'));
$env->load(); 

return array(
	'basePath'=>dirname(dirname(__FILE__)),
	'name'=>'Embedded eClaims',
    
    'aliases' => array(
        'bootstrap' => dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'extensions'.DIRECTORY_SEPARATOR.'yiibooster',
        'SegHEIRS' => $protectedDir,
        'SegHis' => dirname(dirname(__FILE__)),
        'Segworks' => dirname(dirname(__FILE__)).'/vendors/Segworks',
        'SegHisVendor' => dirname(dirname(__FILE__)).'/vendor'        
    ),

	// preloading 'log' component
	'preload'=>array('log', 'eventManager', 'emitter'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
                'application.events.*',
                'application.vendors.*',
                'application.components.event.*'
	),

	'modules'=>array(
            'billing' => array(
               'class' => 'SegHis\modules\billing\BillingModule', 
            ),
            'biometric' => array(
               'class' => 'SegHis\modules\biometric\BiometricModule', 
            ),
            'phic' => array(
               'class' => 'SegHis\modules\phic\PhicModule',                         
            ),
            'eclaims' => array(
               'class' => 'SegHis\modules\eclaims\EclaimsModule',
            ),
            'or_' => array(
                'class' => 'SegHis\modules\or_\Or_Module',
            ),
            'collections' => array(
                'class' => 'SegHis\modules\collections\CollectionsModule',
            ),
            'creditCollection' => array(
                'class' => 'SegHis\modules\creditCollection\CreditCollectionModule',
            ),
            'grantAccount' => array(
                'class' => 'SegHis\modules\grantAccount\GrantAccountModule',
            ),
            'packageManager' => array(
                'class' => 'SegHis\modules\packageManager\PackageManagerModule',
            ),
            'article' => array(
                'class' => 'SegHis\modules\article\ArticleModule',
            ),
            'dialysis' => array(
                'class' => 'SegHis\modules\dialysis\DialysisModule',
            ),
            'cashier' => array(
                'class' => 'SegHis\modules\cashier\CashierModule',                
            ),
            'person' => array(
                'class' => 'SegHis\modules\person\PersonModule',
            ),
            'socialService' => array(
                'class' => 'SegHis\modules\socialService\SocialServiceModule',
            ),
            'personnel' => array(
                'class' => 'SegHis\modules\personnel\PersonnelModule',
            ),
            'admission' => array(
                'class' => 'SegHis\modules\admission\AdmissionModule',
            ),
            'industrialClinic' => array(
                'class' => 'SegHis\modules\industrialClinic\IndustrialClinicModule',
            ),
            'pdpu' => array(
                'class' => 'SegHis\modules\pdpu\PdpuModule',
            ),
            'costCenter' => array(
                'class' => 'SegHis\modules\costCenter\CostCenterModule',
            ),
            'laboratory' => array(
                'class' => 'SegHis\modules\laboratory\LaboratoryModule',
            ),
            'radiology' => array(
                'class' => 'SegHis\modules\radiology\RadiologyModule',
            ),
            'pharmacy' => array(
                'class' => 'SegHis\modules\pharmacy\PharmacyModule',
            ),
            'poc' => array(
                'class' => 'SegHis\modules\poc\PocModule',
            ),
            'InventoryAPIServices'=> array(
                 'class' => 'SegHis\modules\InventoryAPIServices\InventoryAPIServicesModule',           
            ),/*added by MARK GOCELA */
                    
            'integrations' => array(
                'class' => 'SegHis\modules\integrations\IntegrationsModule',
                'listeningIPAddress' => env('LISTENING_IP_ADDRESS'),
                'listeningIPPort' => env('LISTENING_IP_PORT'),                
                'modules' => array(
                    'jnj' => array(
                        'class' => 'SegHEIRS\modules\integrations\modules\jnj\JnjModule',
                    'enableIntegration' => true,
                    'receivingApplication' => env('INTEGRATION_JNJ_HL7_RECEIVING_APPLICATION'),
                    'receivingFacility' => env('INTEGRATION_JNJ_HL7_RECEIVING_FACILITY'),
                    'receivingIPAddress' => env('RECEIVING_IP_ADDRESS'),
                    'receivingIPPort' => env('RECEIVING_IP_PORT'),
                    'receiveAckTimeOut' => env('RECEIVE_ACK_TIMEOUT')
                    )
                ),
        ),
        'onlineConsult' => array(
            'class' => 'SegHis\modules\onlineConsult\OnlineConsultModule',
        ),
        'medRec' => array(
            'class' => 'SegHis\modules\medRec\OnlineConsultModule',
        ),

            // uncomment the following to enable the Gii tool
    //		'gii'=>array(
    //			'class'=>'system.gii.GiiModule',
    //			'password'=>false,
    //			// If removed, Gii defaults to localhost only. Edit carefully to taste.
    //			'ipFilters'=>array('127.0.0.1','::1'),
    //		),
		
	),

	// application components
	'components'=>array(
		'messagequeu' => array(
           'class' => '\SegHis\extensions\MessageQueu\MessageQueu',
           'ip' => '127.0.0.1',
           'port' => '5555',
        ),
        'bootstrap' => array(
            'class' => 'bootstrap.components.Bootstrap',
            'fontAwesomeCss' => true
        ),
                'emitter' => array(
                    'class' => 'SegHEIRS\components\event\Emitter',
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
//		'urlManager'=>array(
//			'urlFormat'=>'path',
//			'rules'=>array(
//				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
//				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
//				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
//			),
//		),
		'db'=>array(
			'connectionString' => 'mysql:host='.$dbhost.';dbname='.$dbname,
			'emulatePrepare' => true,
			'username' => $dbusername,
			'password' => $dbpassword,
			'charset' => 'utf8',
			'enableParamLogging' => true,
			// 'charset' => 'latin1',
		),
        'ehrDb'        => array(
            'class'            => 'CDbConnection',
            'connectionString' => 'mysql:host=' . $dbhost . ';dbname=' . env('EHR_DB_NAME'),
            'emulatePrepare'   => true,
            'username'         => env('EHR_DB_USERNAME'),
            'password'         => env('EHR_DB_PASSWORD'),
            'charset'          => 'utf8',
        ),
        'errorHandler' => array(
			// use 'site/error' action to display errors
            'errorAction' => 'site/error',
		),
            'eventManager' => array(
                'class' => 'SegHis\components\eventManager\EventManager',
            ),
            'log'=>array(
                'class'=>'CLogRouter',
                'routes'=>array(
                    array(
                        'class'=>'CFileLogRoute',
//                        'levels'=>'error, warning',
                        'levels'=>'trace,log',
                        'categories' => 'system.db.CDbCommand',
                        'logFile' => 'db.log',                                    
                    ),
// uncomment the following to show log messages on web pages
//				array(
//					'class'=>'CWebLogRoute',
//				),
                ),
            ),            
        'session' => array(
            'class' => 'CareHttpSession'
        )
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
    'params' => include(dirname(__FILE__) . '/params.php'),
);