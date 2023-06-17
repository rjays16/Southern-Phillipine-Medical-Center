<?php
/*
 * @package care_api
 */

require_once($root_path.'include/care_api_classes/class_core.php');
 
class PatientReferral extends Core{
    /*
     * Database table for the medical certificate info.
     * @var string
     */
    var $tb_seg_patient_referral='seg_patient_referral';

    /*
     * Fieldnames of fld_seg_patient_referral table. "encounter_nr".
     * @var array
     */

    var $fld_seg_patient_referral=array(
                'encounter_nr',
                'is_emergency',
                'is_urgent',
                'is_routine',
                'diagnosis',
                'referral_to',
                'is_evaluation',
                'is_comanage',
                'is_clearance',
                'is_transferserv',
                'others',
                'clinical_findings',
                'create_id',
                'create_dt',
                'modify_id',
                'modify_dt',
            );
    
    var $encounter_nr;

    /*
     * Class Constructor
     * @param string $encounter_nr
     */ 
    function __construct($encounter_nr=''){
        $this->encounter_nr=$encounter_nr;
    }

    /*
     * Core Constructor
     * @param string $encounter_nr
     */ 
    function PatientReferral($encounter_nr=''){
        $this->encounter_nr=$encounter_nr;
        $this->setTable($this->tb_seg_patient_referral);
        $this->setRefArray($this->fld_seg_patient_referral);
    }

   
    function _usePatientReferral(){
        $this->coretable= $this->tb_seg_patient_referral;
        $this->ref_array= $this->fld_seg_patient_referral;
    }
    
    function getPatientReferral(){
        global $db;
        
        $encounter_nr=$this->encounter_nr;
        if(empty($encounter_nr) || (!$encounter_nr))
            return FALSE;
      
        $this->sql="SELECT * FROM $this->tb_seg_patient_referral WHERE encounter_nr=".$db->qstr($encounter_nr)." ORDER BY create_dt,modify_dt DESC";

        if ($buf=$db->Execute($this->sql)){
            if($buf->RecordCount()) {
                return $buf->FetchRow();
            }else { return FALSE; }
        }else { return FALSE; }
    }

   /*
    * Insert new Consultation Referral info into table seg_occupational_therapy_referral
    * @param Array Data to by reference
    * @return boolean
    */  
    public function savePatientReferralInfoFromArray(&$data){
        $this->_usePatientReferral();
        $this->data_array=$data;

        return $this->insertDataFromInternalArray();
    }


   /*
    * Update medical certificate info in table seg_occupational_therapy_referral
    * @param Array Data to by reference
    * @return boolean
    */
    function updatePatientReferralInfoFromArray(&$data){

        $this->_usePatientReferral();
        $this->data_array=$data;
        if(isset($this->data_array['encounter_nr'])) 
            unset($this->data_array['encounter_nr']);
     
        $this->where="encounter_nr='".$data['encounter_nr']."'";
        return $this->updateDataFromInternalArray($data['encounter_nr'],FALSE);
    }# end function updatePatientReferralInfoFromArray
  
} # end class PatientReferral

?>