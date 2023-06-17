<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use SegHEIRS\modules\integrations\events\CbgOrderedEvent;
use SegHis\modules\laboratory\models\LaboratoryRequest;
//use Yii;

/**
 * Description of RequestController
 *
 * @author Bong
 */
class RequestController extends \Controller {
    //put your code here

    public function filters()
    {
        return array(
            array('bootstrap.filters.BootstrapFilter')
        );
    }    
    
    /**
     *
     */
    public function actionServe()
    {
        // trigger event
        /** @var Emitter $emitter */
        $emitter = \Yii::app()->emitter;
        
        $ref_no = '2018004533';
        
        $labH = LaboratoryRequest::model()->findByPk($ref_no);        
        $confirmed = $labH->getLabDetails($ref_no);        
        
        if ($labH && $confirmed) {
            // trigger event
            /** @var Emitter $emitter */                        
            $emitter = \Yii::app()->emitter;
            $emitter->emit(new CbgOrderedEvent($labH, $confirmed));
        }
    }    
}
