<?php

require_once($root_path.'include/care_api_classes/class_core.php');

class DeathCertificate extends Core{
	
	var $tb_seg_cert_death = 'seg_cert_death';
	
	var $fld_seg_cert_death = array(
				'pid',
				'registry_nr',
				'death_place_basic',
				'death_place_mun',
				'death_place_prov',
				'death_date',
				'dcitizenship',
				'age_at_death',
				'm_age',
				'delivery_method',
				'pregnancy_length',
				'birth_type',
				'birth_rank',
				'death_cause',
				'death_manner',
				'place_occurence',
				'attendant_type',
				'attended_from_date',
				'attended_to_date',
				'death_cert_attended',
				'death_time',
				'attendant_name',
				'attendant_title',
				'attendant_address',
				'attendant_date_sign',
				'corpse_disposal',
				'burial_permit',
				'burial_date_issued',
				'is_autopsy',
				'cemetery_name_address',
				'informant_name',
				'informant_address',
				'informant_relation',
				'informant_date_sign',
				'encoder_name',
				'encoder_title',
				'encoder_date_sign',
				'history',
				'create_id',
				'create_time',
				'modify_id',
				'modify_time'
				);
				
	var $refCode;
	
	/*    
	 * Constructor
	 * @param string primary key refCode
	 */
	function DeathCertificate($refCode){
		if(!empty($refCode)) $this->refCode = $refCode;
		$this->setTable($this->tb_seg_cert_death);
		$this->setRefArray($this->fld_seg_cert_death);
	}
	
	/**
	 * Sets the core object point to seg_cert_death and corresponding field names.
	 * @access private 
	 */
	function _useDeathCertificate(){
		$this->coretable = $this->tb_seg_cert_death;
		$this->ref_array = $this->fld_seg_cert_death;
	}
	
	/**
	 * Check if death certificate info exists based on PID
	 * @param string ref_code - PID
	 * @return array of death certificate info else boolean
	 */
	function getDeathCertRecord($refCode=''){
		global $db;
		
		if(empty($refCode) || (!$refCode)){
			$refCode = $this->refCode;
			if(empty($refCode) || (!$refCode))
				return FALSE;		
		}
			# burn added : July 28, 2007
		if (intval($refCode)){
			$pid_format = " (pid='".$refCode."' OR pid=".$refCode.") ";
		}else{
			$pid_format = " pid='".$refCode."' ";
		}

		$this->sql = "SELECT * FROM $this->tb_seg_cert_death WHERE $pid_format";
		if($buf = $db->Execute($this->sql)){
			if($buf->RecordCount()){
				return $buf->FetchRow();
			}else { return FALSE; }
		}else { return FALSE; }
	} // end function getDeathCertRecord
		
	/**
	 * Insert new death certificate record into table seg_cert_death
	 * @param Array Data to by reference
	 * @return boolean
	 */
	function saveDeathCertificateInfoFromArray(&$data){
		$this->_useDeathCertificate();
		$this->data_array = $data;
		
		return $this->insertDataFromInternalArray();
	}// end function daveDeathCertificateInfoFromArray();
	
	/**
	 * Update death certificate info in table 'seg_cert_death'
	 * @param Array Data to by reference
	 * @return boolean
	 */
	function updateDeathCertificateInfoFromArray(&$data){
		global $HTTP_SESSION_VARS, $dbtype;

#	echo "updateDeathCertificateInfoFromArray : data = ";
#	print_r ($data);
#	echo " <br> \n";

		$this->_useDeathCertificate();
		$this->data_array=$data;
		// remove probable existing array data to avoid replacing the stored data
		unset($this->data_array['create_id']);
		unset($this->data_array['create_dt']);
		unset($this->data_array['modify_time']);
		$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
#		return $this->updateDataFromInternalArray($nr);

		if($dbtype=='postgres7'||$dbtype=='postgres') $concatfx='||';
			else $concatfx='concat';

			# burn added : July 28, 2007
		if (intval($data['pid'])){
			$pid_format = " (pid='".$data['pid']."' OR pid=".$data['pid'].") ";
		}else{
			$pid_format = " pid='".$data['pid']."' ";
		}

			#	Only the keys of data to be updated must be present in the passed array.
		$x='';
		$v='';
		while(list($x,$v)=each($this->ref_array)) {
#			if(isset($this->data_array[$v])&&(trim($this->data_array[$v])!='')) {
				$this->buffer_array[$v]=trim($this->data_array[$v]);
#			}
		}
#	echo "updateDeathCertificateInfoFromArray : this->data_array = '";
#	print_r ($this->data_array);
#	echo "' <br> \n";
#	echo "updateDeathCertificateInfoFromArray : this->buffer_array = '";
#	print_r ($this->buffer_array);
#	echo "' <br> \n";
		$elems='';
		while(list($x,$v)=each($this->buffer_array)) {
			# use backquoting for mysql and no-quoting for other dbs. 
			if ($dbtype=='mysql') $elems.="`$x`=";
				else $elems.="$x=";
			
			if(stristr($v,$concatfx)||stristr($v,'null')) $elems.=" $v,";
				else $elems.="'$v',";
		}
		# Bug fix. Reset array.
		reset($this->data_array);
		reset($this->buffer_array);
		$elems=substr_replace($elems,'',(strlen($elems))-1);
      $this->sql="UPDATE $this->coretable SET $elems, modify_time=NOW() WHERE $pid_format";
#	echo "updateDeathCertificateInfoFromArray : this->sql = '".$this->sql."' <br> \n";
#	exit();
		return $this->Transact();
	}// end function updateDeathCertificateInfoFromArray

	/**
	 * Update death certificate info in table 'seg_cert_death'
	 * @param Array Data to by reference
	 * @return boolean
	 */
	function updateDeathCertificateInfoFromArray2(&$data){
		$this->_useDeathCertificate();
		$this->data_array = $data;
		if(isset($this->data_array['pid']))
			unset($this->data_array['pid']);
		
		$this->where="pid='".$data['pid']."'";
		return $this->updateDataFromInternalArray($data['pid'],FALSE);
	}// end function updateDeathCertificateInfoFromArray
	
	function selCitizenship($country){
		global $db;
		
		$this->sql = "SELECT DISTINCT cp.*, c.* FROM care_person AS cp 
						  LEFT JOIN seg_country AS c ON cp.citizenship=c.country_code
						  WHERE c.country_code='$country'";			  
		
		if ($this->result=$db->Execute($this->sql)) {
         $this->count=$this->result->RecordCount();
         return $this->result->FetchRow();
      	} else{
         return FALSE;
      	}					  
	}// end function selCitizenship	
	
	function getCitizenship(){
	    global $db;
		$this->sql="SELECT * FROM seg_country ORDER BY citizenship ASC";
						 
	    if ($this->result=$db->Execute($this->sql)) {
		    if ($this->result->RecordCount()) {
		        return $this->result;
			} else {
				return FALSE;
			}
		} else {
		    return FALSE;
		}
	}// end function getCitizenship	
	
}// end class DeathCertificate

?>