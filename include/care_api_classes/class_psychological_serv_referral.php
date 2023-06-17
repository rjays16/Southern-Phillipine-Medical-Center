<?php
/*
 * @package care_api
 */

require_once($root_path.'include/care_api_classes/class_core.php');
 
class PsychologicalServReferral extends Core{
    /*
     * Database table for the medical certificate info.
     * @var string
     */
    var $tb_seg_psychological_serv_referral='seg_psychological_serv_referral';

    /*
     * Fieldnames of fld_psychological_serv_referral table. "encounter_nr".
     * @var array
     */

    var $fld_psychological_serv_referral=array(
                'encounter_nr',
                'reason_referral',
                'psy_comment',
                'is_opd',
                'is_ciu',
                'is_fw',
                'is_mw',
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
    function PsychologicalServReferral($encounter_nr=''){
        $this->encounter_nr=$encounter_nr;
        $this->setTable($this->tb_seg_psychological_serv_referral);
        $this->setRefArray($this->fld_psychological_serv_referral);
    }

    /*
     * Sets the core object point to seg_cert_med and corresponding field names.
     * @access private
     */
    function _usePsychologicalServReferral(){
        $this->coretable= $this->tb_seg_psychological_serv_referral;
        $this->ref_array= $this->fld_psychological_serv_referral;
    }
    
    function getPsychologicalServReferral(){
        global $db;
        
        $encounter_nr=$this->encounter_nr;
        if(empty($encounter_nr) || (!$encounter_nr))
            return FALSE;
      
        $this->sql="SELECT * FROM $this->tb_seg_psychological_serv_referral WHERE encounter_nr=".$db->qstr($encounter_nr)." ORDER BY create_dt,modify_dt DESC";

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
    function savePsychologicalServReferralInfoFromArray(&$data){
        $this->_usePsychologicalServReferral();
        $this->data_array=$data;

        return $this->insertDataFromInternalArray();
    }


   /*
    * Update medical certificate info in table seg_consultation_referral
    * @param Array Data to by reference
    * @return boolean
    */
    function updatePsychologicalServReferralInfoFromArray(&$data){

        $this->_usePsychologicalServReferral();
        $this->data_array=$data;
        if(isset($this->data_array['encounter_nr'])) 
            unset($this->data_array['encounter_nr']);
     
        $this->where="encounter_nr='".$data['encounter_nr']."'";
        return $this->updateDataFromInternalArray($data['encounter_nr'],FALSE);
    }# end function updateConReferralInfoFromArray
  
} # end class ConReferral

?>