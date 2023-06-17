<?php

require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/biometric/class_person_fingerprint.php');

/**
 * Description of Biometric
 *
 * @author Bong
 */
class Biometric extends Core {

    private static $fingers = array(
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

    public static function uniqidReal($length = 16) {
        // uniqid gives 13 chars, but you could adjust it to your needs.
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($length / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($length / 2));
        } else {
            throw new Exception("no cryptographically secure random function available");
        }
        return substr(bin2hex($bytes), 0, $length);
    }    
    
    /***
     * 
     */
    public static function saveFingerprint($pid, $fptemplate) {
        global $db;
//        $pid = json_decode($_POST["pid"]); 
        $fptemplate = preg_replace( '/\\\+/', '\\', $fptemplate);
        $template = json_decode(stripcslashes($fptemplate), true);
        
        $objPerson = new Person();
        $person = $objPerson->getPersonInfo($pid);        
        if (empty($person)) {
            throw new Exception('No person with ID '. $pid);
        }        
        
        $db->BeginTrans();
        try {            
            $objFingerprint = new PersonFingerprint();
            $fpbiom = $objFingerprint->getPersonFingerprint($pid);
            $bNewRecord = empty($fpbiom);

            $fpdata = array();
            $fpdata['pid'] = $person['pid'];
            $fpdata['lastName'] = $person['name_last'];
            $fpdata['birthYear'] = date("Y", strtotime($person['date_birth']));
            $fpdata['birthMonth'] = date("n", strtotime($person['date_birth']));
            $fpdata['gender'] = strtoupper($person['sex']);
            
            foreach(self::$fingers as $key => $value) {
                $fptmpValue = base64_decode($template[$key]);                
                $binarray = !empty($fptmpValue) ? unpack('C*', $fptmpValue) : null;
                $binstr = !empty($binarray) ? call_user_func_array("pack", array_merge(array("C*"), $binarray)) : null;
                                
                $fpdata[$value] = $binstr;
            }
            
            foreach ($fpbiom as $key => $value){
                if ($fpdata[$key] == ""){
                     $fpdata[$key] = $fpbiom[$key];
                }
            }

//            $pocOrder->modify_id = $_SESSION['sess_temp_userid'];
//            $pocOrder->create_id = $_SESSION['sess_temp_userid'];
//            $pocOrder->modify_dt = new CDbExpression('NOW()');
//            $pocOrder->create_dt = new CDbExpression('NOW()');            
            
            $bsuccess = $objFingerprint->savePersonFingerprint($fpdata, $bNewRecord);
			
            if ($bsuccess) {
                $db->CommitTrans();
                return TRUE;
//                echo CJSON::encode(true);
            }
            else {
                $db->RollbackTrans();
//                $errors = $pocOrder->getErrors();
//                $errmsg = implode("|",$errors);                
                $errmsg = "Error in saving the fingerprint of patient '".$person['pid']."'\n\nSQL = ".$objFingerprint->sql;
                throw new Exception($errmsg);
            }
            
        } catch (Exception $e) {
            
            $db->RollbackTrans();            
            throw new Exception('Unable to save the fingerprint of patient:  ' . $e->getMessage());
            
        }        
    }    
}
