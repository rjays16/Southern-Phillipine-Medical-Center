<?php
/*
 * @package care_api
 */

require_once($root_path.'include/care_api_classes/class_core.php');
 
class OccuTherapyReferral extends Core{
    /*
     * Database table for the medical certificate info.
     * @var string
     */
    var $tb_seg_occupational_therapy_referral='seg_occupational_therapy_referral';

    /*
     * Fieldnames of fld_seg_occupational_therapy_referral table. "encounter_nr".
     * @var array
     */

    var $fld_seg_occupational_therapy_referral=array(
                'encounter_nr',
                'contact_person',
                'relation_patient',
                'contact_no',
                'reason_referral',
                'diagnosis',
                'precautions',
                'is_physical_fit',
                'is_leisure_explo',
                'is_thera_gardening',
                'is_creative_express',
                'is_adl_giadl',
                'is_work_explo',
                'is_social_skill',
                'others',
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
    function OccuTherapyReferral($encounter_nr=''){
        $this->encounter_nr=$encounter_nr;
        $this->setTable($this->tb_seg_occupational_therapy_referral);
        $this->setRefArray($this->fld_seg_occupational_therapy_referral);
    }

   
    function _useOccuTherapyReferral(){
        $this->coretable= $this->tb_seg_occupational_therapy_referral;
        $this->ref_array= $this->fld_seg_occupational_therapy_referral;
    }
    
    function getOccuTherapyReferral(){
        global $db;
        
        $encounter_nr=$this->encounter_nr;
        if(empty($encounter_nr) || (!$encounter_nr))
            return FALSE;
      
        $this->sql="SELECT * FROM $this->tb_seg_occupational_therapy_referral WHERE encounter_nr=".$db->qstr($encounter_nr)." ORDER BY create_dt,modify_dt DESC";

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
    public function saveOccuTherapyReferralInfoFromArray(&$data){
        $this->_useOccuTherapyReferral();
        $this->data_array=$data;

        return $this->insertDataFromInternalArray();
    }


   /*
    * Update medical certificate info in table seg_occupational_therapy_referral
    * @param Array Data to by reference
    * @return boolean
    */
    function updateOccuTherapyReferralInfoFromArray(&$data){

        $this->_useOccuTherapyReferral();
        $this->data_array=$data;
        if(isset($this->data_array['encounter_nr'])) 
            unset($this->data_array['encounter_nr']);
     
        $this->where="encounter_nr='".$data['encounter_nr']."'";
        return $this->updateDataFromInternalArray($data['encounter_nr'],FALSE);
    }# end function updateOccuTherapyReferralInfoFromArray
  
} # end class OccuTherapyReferral

?>