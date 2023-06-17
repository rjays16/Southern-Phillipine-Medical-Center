<?php
/**
* @package care_api
*/
/**
*/
require('roots.php');
require_once($root_path.'include/care_api_classes/class_core.php');
include_once($root_path.'include/care_api_classes/class_department.php');   # burn added: July 19, 2007
include_once($root_path.'include/care_api_classes/class_globalconfig.php');
/**
*  Personnel methods.
*  Note this class should be instantiated only after a "$db" adodb  connector object  has been established by an adodb instance
* @author Elpidio Latorilla
* @version beta 2.0.1
* @copyright 2002,2003,2004,2005,2005 Elpidio Latorilla
* @package care_api
*/
class ProgressNotes extends Core {
		/**#@+
		* @access private
		*/
        function getPersonalInfo($pid,$enc_nr){
            global $db;

            $personalinfo = "SELECT *,IF(cp.name_middle=NULL,CONCAT(cp.name_last,',',cp.name_first,' ',UPPER(SUBSTRING(name_middle,1,1)),'.'),CONCAT(cp.name_last,',',cp.name_first)) AS fullname,
					cw.name as ward_name,
                    IF(sex='m','Male','Female') as gender,
                    SUBSTRING(ce.admission_dt,1,10) AS admitdate,
					ce.encounter_date as consoldate
                    FROM care_encounter ce 
                    LEFT JOIN seg_pdpu_progress_notes sp ON ce.encounter_nr=sp.encounter_nr
                    LEFT JOIN care_person cp ON ce.pid=cp.pid
                    LEFT JOIN care_ward cw ON ce.current_ward_nr=cw.nr
                    LEFT JOIN seg_barangays sb ON cp.brgy_nr=sb.brgy_nr
                    LEFT JOIN seg_soa_diagnosis sd ON ce.encounter_nr=sd.encounter_nr
                    LEFT JOIN seg_municity sm ON sm.mun_nr=cp.mun_nr
                    WHERE sp.pid=".$db->qstr($pid)." and sp.encounter_nr=".$db->qstr($enc_nr)."";
                    
            $this->info= $db->Execute($personalinfo)->FetchRow();
                  
            return $this->info;
		}

		
        function getAttendingPhysician($pid,$enc_nr){
            global $db;

            $att_ph="SELECT CONCAT(TRIM(`fn_get_personell_lastname_first` (
                            IF(
                              ce.current_att_dr_nr <> 0,
                              ce.current_att_dr_nr,
                              ce.consulting_dr_nr
                            )
                          )),', M.D.') AS AttDrName
                    FROM seg_pdpu_progress_notes sp
                    JOIN care_encounter ce ON ce.encounter_nr=sp.encounter_nr
                    WHERE ce.pid=".$db->qstr($pid)." AND ce.encounter_nr=".$db->qstr($enc_nr)."";

            $this->att = $db->Execute($att_ph)->FetchRow();
            return $this->att;
                    
        }


}