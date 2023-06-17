<?php
/**
 * JnjModule,.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016,
 */
namespace SegHEIRS\modules\integrations\modules\jnj;

use SegHEIRS\modules\integrations\modules\jnj\events\ListenerProvider;

/**
 * Description of JnjModule,
 *
 */
class JnjModule extends \CWebModule
{
    /**
     * @var string
     */
    public $controllerNamespace = 'SegHEIRS\modules\integrations\modules\jnj\controllers';
    /**
     * @var string $title
     */
    public $title = 'JnJ HL7';

    /**
     * @var string
     */
    public $defaultController='default';

    /**
     * @var bool
     */
    public $enableIntegration = false;

    /**
     * @var int
     */
    public $heartbeat = 60;

    /**
     * @var string
     */
    public $defaultUser;

    /**
     * @var string
     */
    public $transferMode = 'socket';

    /**
     * @var string|null
     */
    public $receivingApplication = null;

    /**
     * @var string|null
     */
    public $receivingFacility = null;    

    /**
     * @var string|null
     */    
    public $receivingIPAddress = null;
    
    /**
     * @var string|null
     */    
    public $receivingIPPort = null;
    
    public $receiveAckTimeOut = 3;
        
    public function init()
    {
            // this method is called when the module is being created
            // you may place code here to customize the module or the application

            // import the module-level models and components
//		$this->setImport(array(
//			'cashier.models.*',
//			'cashier.components.*',
//		));
    }

    public function beforeControllerAction($controller, $action)
    {
        if (parent::beforeControllerAction($controller, $action)) {
            // this method is called before any module controller action is performed
            // you may place customized code here
            return true;
        } else {
            return false;
        }
    }
    
    /**
     *
     * @return array
     */
    public static function getListeners()
    {
        return array(
            new ListenerProvider()
        );
    }    
}
