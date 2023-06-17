<?php
/*
 * @package care_api
 */

require_once($root_path.'include/care_api_classes/class_core.php');
 
class ConsultationReferral extends Core{
    /*
     * Database table for the medical certificate info.
     * @var string
     */
    var $tb_seg_consultation_referral='seg_consultation_referral';

    /*
     * Fieldnames of fld_seg_consultation_referral table. "encounter_nr".
     * @var array
     */

    var $fld_seg_consultation_referral=array(
                'encounter_nr',
                'is_emergency',
                'is_routine',
                'agency_to',
                'others',
                'agency_from',
                'brief_hist',
                'work_up',
                'impression',
                'reason_referral',
                'agency_remarks',
                'create_id',
                'create_dt',
                'modify_id',
                'modify_dt',
                'DATE__'
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
    function ConReferral($encounter_nr=''){
        $this->encounter_nr=$encounter_nr;
        $this->setTable($this->tb_seg_consultation_referral);
        $this->setRefArray($this->fld_seg_consultation_referral);
    }

    /*
     * Sets the core object point to seg_cert_med and corresponding field names.
     * @access private
     */
    function _useConReferral(){
        $this->coretable= $this->tb_seg_consultation_referral;
        $this->ref_array= $this->fld_seg_consultation_referral;
    }
    
    function getConReferral(){
        global $db;
        
        $encounter_nr=$this->encounter_nr;
        if(empty($encounter_nr) || (!$encounter_nr))
            return FALSE;
      
        $this->sql="SELECT * FROM $this->tb_seg_consultation_referral WHERE encounter_nr=".$db->qstr($encounter_nr)." ORDER BY create_dt,modify_dt DESC";

        if ($buf=$db->Execute($this->sql)){
            if($buf->RecordCount()) {
                return $buf->FetchRow();
            }else { return FALSE; }
        }else { return FALSE; }
    }

   /*
    * Insert new Consultation Referral info into table seg_consultation_referral
    * @param Array Data to by reference
    * @return boolean
    */  
    function saveConReferralInfoFromArray(&$data){
        $this->_useConReferral();
        $this->data_array=$data;

        return $this->insertDataFromInternalArray();
    }


   /*
    * Update medical certificate info in table seg_consultation_referral
    * @param Array Data to by reference
    * @return boolean
    */
    function updateConReferralInfoFromArray(&$data){

        $this->_useConReferral();
        $this->data_array=$data;
        if(isset($this->data_array['encounter_nr'])) 
            unset($this->data_array['encounter_nr']);
     
        $this->where="encounter_nr='".$data['encounter_nr']."'";
        return $this->updateDataFromInternalArray($data['encounter_nr'],FALSE);
    }# end function updateConReferralInfoFromArray
  
} # end class ConReferral

?>