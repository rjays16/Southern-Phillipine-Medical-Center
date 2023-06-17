<?php

namespace SegHis\modules\billing;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BillingModule
 *
 * @author Bong
 */
class BillingModule extends \CWebModule {
    
	public function init()
	{
            // this method is called when the module is being created
            // you may place code here to customize the module or the application

            // import the module-level models and components
            $this->setImport(array(
                    'billing.models.*',
                    'billing.components.*',
            ));
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
}
