<?php 

/**
 *
 * @author rnel <arnacmj@gmail.com>
 * credit to CORE programmer of SEGWORKS
 */


require_once($root_path.'include/care_api_classes/class_core.php');

class AffidavitFatherSurename extends Core {


	public $table = 'seg_affidavit_father_surename';

	public $field = array(
		'pid',
		'modify_id',
		'modify_dt',
		'create_id',
		'create_dt',
		'affiant_fname',
		'affiant_lname',
		'affiant_mname',
		'father_surename',
		'child_fullname',
		'child_relationship',
		'affiant_address',
		'affiant_age',
		'affiant_status',
		'affiant_citizenship',
		'child_birth_date',
		'child_birth_pro',
		'child_birth_mun_cty',
		'child_birth_country',
		'child_birth_reg_num',
		'child_birth_reg_date',
		'paternity_reg_num',
		'paternity_reg_date',
		'paternity_reg_place',
		'city_mun_lcro_cert',
		'province_lcro_cert',
		'country_lcro_cert',
		'date_ausf_cert',
		'place_ausf_cert',
		'administer_personell',
		'administer_date',
		'administer_place',
		'is_self',
		'is_other',
		'history',
		'aff_street',
		'aff_brgy',
		'aff_city',
		'aff_country'
		);

	public $ref_nr;

	public function __construct($ref_nr) {

		if(!empty($ref_nr)) {
			 $this->ref_nr = $ref_nr;
		}

		$this->setTable($this->table);
		$this->setRefArray($this->field);
	}

	public function _useAffidavitFatherSurename(){
		$this->coretable = $this->table;
		$this->ref_array = $this->field;
	}


	public function saveNewAffidavitFatherSurename($data, $user_session = '', $create_dt = '') {
		$this->_useAffidavitFatherSurename();
		$this->data_array =  $data;
		$this->data_array['pid'] = $this->ref_nr;
		$this->data_array['history'] = 'Create '.date('Y-m-d H:i:s').' '. $user_session."\n";
		$this->data_array['create_id'] = $user_session;
		$this->data_array['create_dt'] = $create_dt;
		$this->insertDataFromInternalArray();
	}

	public function getRecentData($pid) {
		global $db;

		if(!pid || empty($pid)) {
			$pid = $this->ref_nr;
		}

		$sql = "SELECT afs.*, p.pid
				FROM $this->table AS afs
				INNER JOIN care_person AS p ON p.pid = afs.pid WHERE afs.pid = ".$db->qstr($pid);
			/*	var_dump($sql);*/
		$result = $db->Execute($sql);
		
		if($result->RecordCount() > 0) {
			return $result->FetchRow();
		} else {
			return false;
		}
	}

	public function updateAffidavitFatherSurname($data, $user_session) {
		global $db;

		// $db->debug = true;

		$this->_useAffidavitFatherSurename();

		extract($data);

		$fieldData = array();

		$history = 'Updated: '.date('Y-m-d H:i:s').' '.$user_session."\n";

		$db->BeginTrans();

		$fieldData = array(
			$this->ref_array[0] => $this->ref_nr,
			$this->ref_array[1] => $user_session,
			$this->ref_array[2] => date('Y-m-d H:i:s'),
			$this->ref_array[5] => $affiant_fname,
			$this->ref_array[6] => $affiant_lname,
			$this->ref_array[7] => $affiant_mname,
			$this->ref_array[8] => $father_surename,
			$this->ref_array[9] => $child_fullname,
			$this->ref_array[10] => $child_relationship,
			$this->ref_array[11] => $affiant_address,
			$this->ref_array[12] => $affiant_age,
			$this->ref_array[13] => $affiant_status,
			$this->ref_array[14] => $affiant_citizenship,
			$this->ref_array[15] => $child_birth_date,
			$this->ref_array[16] => $child_birth_pro,
			$this->ref_array[17] => $child_birth_mun_cty,
			$this->ref_array[18] => $child_birth_country,
			$this->ref_array[19] => $child_birth_reg_num,
			$this->ref_array[20] => $child_birth_reg_date,
			$this->ref_array[21] => $paternity_reg_num,
			$this->ref_array[22] => $paternity_reg_date,
			$this->ref_array[23] => $paternity_reg_place,
			$this->ref_array[24] => $city_mun_lcro_cert,
			$this->ref_array[25] => $province_lcro_cert,
			$this->ref_array[26] => $country_lcro_cert,
			$this->ref_array[27] => $date_ausf_cert,
			$this->ref_array[28] => $place_ausf_cert,
			$this->ref_array[29] => $administer_personell,
			$this->ref_array[30] => $administer_date,
			$this->ref_array[31] => $administer_place,
			$this->ref_array[32] => $is_self,
			$this->ref_array[33] => $is_other,
			$this->ref_array[35] => $aff_street,
			$this->ref_array[36] => $aff_brgy,
			$this->ref_array[37] => $aff_city,
			$this->ref_array[38] => $aff_country,
			);
		  // echo "<pre>";
		  // var_dump($fieldData);
		  // die();
		  $update = $db->Replace($this->coretable, $fieldData, $this->ref_array[0], $autoquote = true);
	
		  if($update == 0) {

		  	$db->RollbackTrans();
		  	return false;

		  } else {
		  	$sql = "UPDATE $this->coretable 
		  				SET history = ".$this->ConcatHistory($history)."
	  					WHERE pid = $this->ref_nr";
	  		$db->Execute($sql);
		  	$db->CommitTrans();
		  	return true;
		  }

	}

}