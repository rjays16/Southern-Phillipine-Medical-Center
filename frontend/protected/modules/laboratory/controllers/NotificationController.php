<?php
use SegHis\modules\laboratory\models\LaboratoryRequest;

class NotificationController extends Controller
{

    public function filters()
    {
        return array(
            array('bootstrap.filters.BootstrapFilter')
        );
    }    
    
    

    public function actionIsPrint($refno){
        if($refno){
         $labH = LaboratoryRequest::model()->findByPk($refno);    
       
        $data = array('data' => $labH);
        echo CJSON::encode($data);
         }
    }
   

}
