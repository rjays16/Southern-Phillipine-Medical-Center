<?php

require_once($root_path.'include/care_api_classes/class_core.php');
/**
 * Description of class_person_fingerprint
 *
 * @author Bong
 */
class PersonFingerprint extends Core {    
        
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_person_fingerprint';
    }    
    
    public function getPersonFingerprint($key){
        global $db;

        $this->sql="SELECT * from {$this->tableName()}
                    WHERE pid = '{$key}'";

        if ($this->result=$db->Execute($this->sql)) {
            if ($this->count = $this->result->RecordCount()) {
                $db->setFetchMode(DB_FETCHMODE_OBJECT);
                return $this->result->FetchRow();
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
    
    public function savePersonFingerprint($data, $bNewRecord = true) {
        global $db;
                
        if ($bNewRecord) {
            $fields = array();
            $values = array();
            foreach($data as $k => $v) {
                $fields[] = $k;            
                $values[] = (($k != 'birthYear') && ($k != 'birthMonth')) ? $db->qstr($v) : $v;
            }            
            
            $this->sql = "INSERT INTO {$this->tableName()} (".implode(",", $fields).") \n".
                         "VALUES (".implode(",", $values).")";
        }
        else {
            $strFldVal = '';
            foreach($data as $k => $v) {
                if ($k != "pid") {
                    if (!empty($strFldVal)) {
                        $strFldVal .= ", ";
                    }
                    $strFldVal .= $k;
                    $strFldVal .= " = ";
                    $strFldVal .= (($k != 'birthYear') && ($k != 'birthMonth')) ? $db->qstr($v) : $v;
                }                
            }        
                                    
            $this->sql = "UPDATE {$this->tableName()} SET ".$strFldVal." \n".
                         "WHERE pid = '".$data['pid']."'";
        }

        if ($this->result = $db->Execute($this->sql)) {
            return true;
        } else {
            return false;
        }
    }

    static function getPersonFingerprintOnly($pid) {
        global $db;
        $db->setFetchMode(ADODB_FETCH_ASSOC);
        $result=$db->GetRow("SELECT 
                              leftPinky,
                              leftRing,
                              leftMiddle,
                              leftIndex,
                              leftThumb,
                              rightPinky,
                              rightRing,
                              rightMiddle,
                              rightIndex,
                              rightThumb 
                            FROM
                              `seg_person_fingerprint` 
                            WHERE pid = '".$pid."'");
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }            
    
}
