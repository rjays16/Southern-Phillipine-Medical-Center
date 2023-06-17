<?php
/*
 * @package care_api
 */

require_once($root_path.'include/care_api_classes/class_core.php');
 
class MedAbstract extends Core{

	var $tb_seg_med_abstract='seg_med_abstract';
	
	
	/*
	 * Fieldnames of seg_med_abstract table. Primary key "encounter_nr".
	 * @var array
	 */
	var $fld_seg_med_abstract=array(
				'encounter_nr',
				'brief_hist',
				'mental_status',
				'diagnosis',
				'remarks',
				'dr_nr',
				'history',
				'modify_id',
				'modify_dt',
				'create_id',
				'create_dt',
				'abst_nr',
				'civil_status',
				'age'	
			);			
				
	/*
	 * Sets the core object point to seg_cert_abstract and corresponding field names.
	 * @access private
	 */
	function _useMedAbstract(){
		$this->coretable= $this->tb_seg_med_abstract;
		$this->ref_array= $this->fld_seg_med_abstract;
	}
	
   /*
	* Insert new medical Abstract info into table seg_cert_med
	* @param Array Data to by reference
	* @return boolean
	*/	
	function saveMedAbstractInfoFromArray(&$data){

		$this->_useMedAbstract();
		$this->data_array=$data;
		return $this->insertDataFromInternalArray();

	}# end function saveMedCertificateInfoFromArray


   /*
	* Update medical Abstract info in table seg_cert_abstract
	* @param Array Data to by reference
	* @return boolean
	*/
	function updateMedAbstractInfoFromArray(&$data){

		$this->_useMedAbstract();
		$this->data_array=$data;
		if(isset($this->data_array['encounter_nr'])) 
			unset($this->data_array['encounter_nr']);
	
		$this->where="encounter_nr='".$data['encounter_nr']."'";
		return $this->updateDataFromInternalArray($data['encounter_nr'],FALSE);
	
	
	}# end function updateMedAbstractInfoFromArray

	function getMedAbsRecord($nr=''){
		global $db;
		
		if(empty($nr) || (!$nr)){
			$nr=$this->nr;
			if(empty($nr) || (!$nr))
				return FALSE;
		}
		$this->sql="SELECT * FROM $this->tb_seg_med_abstract WHERE encounter_nr='$nr'";
		if ($buf=$db->Execute($this->sql)){
			if($buf->RecordCount()) {
				return $buf->FetchRow();
			}else { return FALSE; }
		}else { return FALSE; }
	}//end fucntion getMedAbsRecord

} # end class MedAbstract

?>