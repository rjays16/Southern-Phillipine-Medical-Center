<?php

/**
 * IntegrationsModule,.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016,
 */

namespace SegHis\modules\integrations;

/**
 * Description of IntegrationsModule,
 */

class IntegrationsModule extends \WebModule
{
    /**
     * @var string
     */
    public $controllerNamespace = 'SegHEIRS\modules\integrations\controllers';
    /**
     * @var string $title
     */
    public $title = 'Integrations';

    /**
     * @var string
     */
    public $defaultController='default';
    
    /**
     * @var string|null
     */    
    public $listeningIPAddress = null;
    
    /**
     * @var string|null
     */    
    public $listeningIPPort = null;    

    /**
     * @param \Controller $controller
     * @param \CAction $action
     * @return bool
     */
//    public function beforeControllerAction($controller, $action)
//    {
//        $before = parent::beforeControllerAction($controller, $action);
//        if (!$before) return false;
//
//        /** @var \CUrlManager $urlManager */
//        $urlManager = \Yii::app()->getUrlManager();
//        $controller->breadcrumbs['Appointments'] = $urlManager->createUrl('/appointments');
//
//        return true;
//    }

}
