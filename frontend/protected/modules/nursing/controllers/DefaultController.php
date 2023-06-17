<?php 

Yii::import('application.models.SegDietList');

class DefaultController extends Controller
{

 public function actionIndex()
 {
 	$dietlist = new SegDietList();
 	$dietlist->encounter_nr = $_POST['encounter_nr'];
 	$dietlist->diet_name =  $_POST['dietname'];
 	$dietlist->create_id = $_POST['login'];
 	$dietlist->create_dt = date('Y-m-d H:i:s');
 	$dietlist->modify_id =  $_POST['login'];
 	
 		if($dietlist->save()){
            echo CJSON::encode(array("message"=>'Successfully Save.'));
        }
        else{
             echo CJSON::encode(array("message"=>'Failed to Save.'));
        }
 }


}





?>