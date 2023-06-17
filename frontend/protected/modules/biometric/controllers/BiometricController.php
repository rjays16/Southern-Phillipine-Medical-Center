<?php
use SegHis\modules\biometric\models\PersonFingerprint;
use \Person;

/**
 * Description of Biometric Controller
 *
 * @author Bong
 */
class BiometricOntroller extends \Controller {
    
    const fingers = array(
    	"left little"  => "leftPinky",
        "left ring"    => "leftRing",
    	"left middle"  => "leftMiddle",        
    	"left index"   => "leftIndex",
    	"left thumb"   => "leftThumb",    	
    	"right little" => "rightPinky",
    	"right ring"   => "rightRing",
    	"right middle" => "rightMiddle",
    	"right index"  => "rightIndex",
    	"right thumb"  => "rightThumb"
    );
    
    /***
     * 
     */
    public function actionSaveFingerprint() {        
        $pid = json_decode($_POST["pid"]);        
        $template = json_decode($_POST["template"], true);
        
        $person = Person::model()->findByPk($pid);        
        if (empty($person)) {
            throw new CHttpException(500, 'No person with ID '. $pid);
        }
        
        $transaction = Yii::app()->getDb()->beginTransaction();           
        try {
            $fpbiom = PersonFingerprint::model()->findByPk($pid);
            if (empty($fpbiom)) {
                $fpbiom = new PersonFingerprint();
                $fpbiom->pid = $person->pid;
            }                        
            $fpbiom->lastName = $person->name_last;
            $fpbiom->birthYear = date("Y", strtotime($person->date_birth));
            $fpbiom->birthMonth = date("n", strtotime($person->date_birth));
            $fpbiom->gender = strtoupper($person->sex);
            
            foreach($template as $key => $value) {
                $binstr = !empty($value) ? call_user_func_array("pack", array_merge(array("C*"), $value)) : null;
                $fpbiom->fingers[$key] = $binstr;
            }
                        
//            $pocOrder->modify_id = $_SESSION['sess_temp_userid'];
//            $pocOrder->create_id = $_SESSION['sess_temp_userid'];
//            $pocOrder->modify_dt = new CDbExpression('NOW()');
//            $pocOrder->create_dt = new CDbExpression('NOW()');                                               
            
            $bsuccess = $fpbiom->isNewRecord ? $fpbiom->save() : $fpbiom->update();
            if ($bsuccess) {                                
                $transaction->commit();
                echo CJSON::encode(true);
            }
            else {
                $errors = $fpbiom->getErrors();
                $errmsg = implode("|",$errors);                
                
                $transaction->rollback();
                throw new Exception($errmsg);
            }
            
        } catch (Exception $e) {
            
            if ($transaction->active) {
                $transaction->rollback();
            }
            
            throw new CHttpException(500, 'Unable to save the fingerprint of patient:  ' . $e->getMessage());
        }        
    }
}