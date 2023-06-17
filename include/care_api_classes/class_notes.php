<?php
/**
* @package care_api
*/
/**
*/
require_once($root_path.'include/care_api_classes/class_core.php');

/**
*  Notes methods.
*  Note: this class should be instantiated only after a "$db" adodb  connector object  has been established by an adodb instance.
* @author Elpidio Latorilla
* @version beta 2.0.1
* @copyright 2002,2003,2004,2005,2005 Elpidio Latorilla
* @package care_api
*/
class Notes extends Core {
	/**
	* Database table for the encounter notes data.
	* @var string
	* @access private
	*/
	var $tb_notes='care_encounter_notes';
	/**
	* Database table for the notes types.
	* @var string
	* @access private
	*/
	var $tb_types='care_type_notes';
	/**
	* Database table for the encounter data.
	* @var string
	* @access private
	*/
	var $tb_enc='care_encounter';
	/**
	* Holder for sql query results.
	* @var object adodb record object
	* @access private
	*/
	var $result;
	/**
	* Holder for preloaded department data.
	* @var object adodb record object
	* @access private
	*/
	var $preload_dept;
	/**
	* Preloaded flag
	* @var boolean
	* @access private
	*/
	var $is_preloaded=false;
	/**
	* Field names of care_encounter_notes table
	* @var array
	* @access private
	*/
	var $fld_notes=array('nr',
									'encounter_nr',
									'type_nr',
									'notes',
									'code',
									'short_notes',
									'aux_notes',
									'ref_notes_nr',
									'personell_nr',
									'personell_name',
									'send_to_pid',
									'send_to_name',
									'date',
									'time',
									'location_type',
									'location_type_nr',
									'location_nr',
									'location_id',
									'ack_short_id',
									'date_ack',
									'date_checked',
									'date_printed',
									'send_by_mail',
									'send_by_email',
									'send_by_fax',
									'status',
									'history',
									'modify_id',
									'modify_time',
									'create_id',
									'create_time');
	/**
	* Constructor
	*/			
	function Notes(){
		$this->setTable($this->tb_notes);
		$this->setRefArray($this->fld_notes);
	}
	/**
	* Checks if a certain notes record of a certain type exists in the database.
	* @access private
	* @param int Encounter number
	* @param int Notes type number
	* @return boolean
	*/			
	function _Exists($enr,$type_nr){
		if($this->_RecordExists("type_nr=$type_nr AND encounter_nr=$enr")){
			return true;
		}else{return false;}
	}
	/**
	* Gets all types of notes record. Sorted result.
	* @access public
	* @param string Sort item
	* @return mixed 2 dimensional array or boolean
	*/			
	function getAllTypesSort($sort=''){
	    global $db;
	
		if(empty($sort)) $sort=" ORDER BY nr";
			else $sort=" ORDER BY $sort";
	    if ($this->result=$db->Execute("SELECT nr,type,name,LD_var AS \"LD_var\" FROM $this->tb_types  $sort")) {
		    if ($this->result->RecordCount()) {
		        return $this->result->GetArray();
			} else {
				return false;
			}
		}
		else {
		    return false;
		}
	}
	/**
	* Gets all types of notes record. Unsorted result.
	* @access public
	* @param string Sort item
	* @return mixed 2 dimensional array or boolean
	*/			
	function getAllTypes(){
		return $this->getAllTypesSort();
	}
	/**
	* Gets notes type information based on the type number (nr key).
	*
	* The returned array has 4 elements:
	* - nr  = The type number (integer).
	* - type  = The optional type id (alphanumeric).
	* - name = The name of the notes type.
	* - LD_var  = The name of the language dependent variable containing the foreign name of the notes type.
	*
	* @access public
	* @return mixed 1 dimensional array or boolean
	*/			
	function getType($nr=1){
	    global $db;

	    if ($this->res['gt']=$db->Execute("SELECT nr,type,name,LD_var AS \"LD_var\" FROM $this->tb_types WHERE nr=$nr")) {
		    if ($this->res['gt']->RecordCount()) {
		        return $this->res['gt']->FetchRow();
			} else {
				return false;
			}
		}
		else {
		    return false;
		}
	}
	/**
	* Gets a notes record data based on a passed condition.
	* @access private
	* @param string Condition for the WHERE sql part. Query constraint.
	* @param string Sort directive in complete syntax e.g. "ORDER BY date DESC"
	* @return mixed adodb record object or boolean
	*/			
	function _getNotes($cond,$order='ORDER BY date,time DESC'){
	    global $db;
		$this->sql="SELECT * FROM $this->tb_notes WHERE $cond $order";
		//echo $this->sql;
	    if ($this->result=$db->Execute($this->sql)) {
		    if ($this->result->RecordCount()) {
		        //return true;
		        return $this->result;
			}else{return false;}
		}else{return false;}
	}
	/**
	* Save a notes data of a given type number.
	*
	* The data to be saved comes from an internal buffer array that is populated by other methods.
	* @access private
	* @param string Type number of the notes data to be saved.
	* @return boolean
	*/
	function _insertNotesFromInternalArray($type_nr=''){
		global $HTTP_SESSION_VARS;
		if(empty($type_nr)) return false;
		if(empty($this->data_array['date'])) $this->data_array['date']=date('Y-m-d');
		if(empty($this->data_array['time'])) $this->data_array['time']=date('H:i:s');
		$this->data_array['type_nr']=$type_nr;
		//$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
		$this->data_array['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
		$this->data_array['create_time']=date('YmdHis');
		$this->data_array['history']="Create: ".date('Y-m-d H-i-s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n\r";	
        	return $this->insertDataFromInternalArray();
	}
	/**
	* Updates a notes data record based on the primary record key "nr".
	*
	* The data to be saved comes from an internal buffer array that is populated by other methods.
	* @access private
	* @param int Record number of the notes record to be updated.
	* @return boolean
	*/			
	function _updateNotesFromInternalArray($nr){
		global $HTTP_SESSION_VARS;
		$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
		$this->data_array['modify_time']=date('YmdHis');
		$this->data_array['history']=$this->ConcatHistory("Update: ".date('Y-m-d H-i-s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n\r");
		return $this->updateDataFromInternalArray($nr);
		/*
		if($this->updateDataFromInternalArray($nr)){
			return true;
		}else{ return false; }
		*/
	}
	/**
	* Gets the date range of a certain notes type that fits to a given condition.
	*
	* The resulting adodb record object is stored in the internal buffer $result.
	* @access private
	* @param int Encounter number
	* @param int Notes type number
	* @param string Condition string. Query constraint.
	* @return boolean
	*/			
	function _getNotesDateRange($enr='',$type_nr=0,$cond=''){
		global $db;
		if(empty($enr)){
			return false;
		}else{
			if(empty($cond)&&$type_nr){
				$cond="encounter_nr=$enr AND type_nr=$type_nr";
			}
			$this->sql="SELECT MIN(date) AS fe_date, MAX(date) AS le_date FROM $this->tb_notes WHERE $cond";
			if($this->result=$db->Execute($this->sql)){
				if($this->result->RecordCount()){
					return true;
				}else{return false;}
			}else{return false;}
		}
	}
	/**
	*Gets all notes of a given record number.
	* @access public
	* @param int Record number
	* @return mixed adodb record object or boolean
	*/
	function getEncounterNotes($nr){
		return $this->_getNotes("nr=$nr AND status NOT IN ($this->dead_stat)",'');
	}

	/**
	 * Insert new recordset
	 * @access public
	 * @param encounter, type_nr, notes
	 * @return mixed adodb record object or boolean
	 */
	
	function AddNotes($encounter,$type_nr,$desc,$create_id,$target){
		$this->sql= "INSERT INTO $tb_notes (encounter_nr,type_nr,notes,create_id) VALUES($encounter,$type_nr,$desc,$create_id)";
		if($this->Transact()){
			return TRUE;
		}else{return FALSE;}
	}
	
	function getNotes($nr,$type_nr){
		global $db;
		if(empty($nr)) return FALSE;
		
	}
	
	//========== added by mark March 9, 2007============
/*	
	function _useResult(){
			$this->coretable = $this->tb_seg_encounter_result;
			$this->ref_array = $this->fld_seg_encounter_result;
			$this->fld_primary_key = 'encounter_nr';
			$this->tb_foreign = 'seg_results';
			$this->fld_foreign_key = 'result_code'; 
		}
		
		function _useDisposition(){
			$this->coretable = $this->tb_seg_encounter_disposition;
			$this->ref_array = $this->fld_seg_encounter_disposition;
			$this->fld_primary_key = 'encounter_nr';
			$this->tb_foreign = 'seg_dispositions';
			$this->fld_foreign_key = 'disp_code';	
		}
	
	
	function _getResultx(){
			global $db;
			//$this->_useResult();
			$this->sql = "SELECT * FROM $this->tb_foreign WHERE area_used='A' OR area_used='NULL'";
			if($this->res2['re']=$db->Execute($this->sql)){
				if($this->rec1=$['rc']->res2['re']->RecordCount()){
					return $this->res2['re'];
				}else { return FALSE;}
			}else {return  FALSE;}
	}
	
	
	*/
	function getDataNotes($enc){
		 global $db;

        $this->sql = "SELECT cen.encounter_nr as enc,
			ce.er_opd_diagnosis AS impression,
			/* added by Gerald */
			services,
			dept_nr,
			(SELECT cw.name FROM care_ward AS cw WHERE  cw.nr=ce.`current_ward_nr`)  AS station,
			(SELECT cw.ward_id FROM care_ward AS cw WHERE  cw.nr=ce.`current_ward_nr`)  AS station_id,
			dr_nr,
			other,
			diagnostic,
			special,
			additional,
			vs,
			/* end */
			notes,
			nRemarks,
			nDiet,
			nIVF,
			nHeight,
			nWeight,
			cen.modify_id,
			cen.date, 
			(
			CASE
				WHEN fn_get_ageyr (
				ce.`admission_dt`,
				cp.date_birth
				) > 0 
				THEN 
				CASE
				WHEN fn_calculate_age (
					ce.`admission_dt`,
					cp.date_birth
				) IS NOT NULL 
				THEN fn_get_age (
					NOW(),
					cp.date_birth
				) 
				ELSE '' 
				END 
				ELSE 
				CASE
				WHEN fn_calculate_age (NOW(), cp.date_birth) IS NOT NULL 
				THEN fn_get_age (NOW(), cp.date_birth) 
				ELSE '' 
				END 
			END
			  ) AS age,cen.time,avail_meds,gadgets,problems,actions
				FROM care_encounter AS ce
					INNER JOIN care_person AS cp
					ON cp.pid = ce.pid
				LEFT JOIN care_encounter_notes AS cen
				ON ce.encounter_nr = cen.encounter_nr
				AND cen.`is_deleted` = 0
				WHERE ce.encounter_nr = ".$db->qstr($enc)." 
				ORDER BY cen.date DESC,cen.time DESC LIMIT 1";
        			//  die($this->sql);
        $row = $db->GetRow($this->sql);

        return $row;
	}
	function getDietName($enc,$last){
		global $db;

		        $this->sql = "SELECT cen.`nDiet` AS diet_name,sd.diet_code
        			FROM seg_diet AS sd
        			INNER JOIN care_encounter_notes AS cen 
        			ON cen.nDiet = sd.diet_name OR cen.`nDiet` = sd.`diet_code` 
        			WHERE cen.encounter_nr = ".$db->qstr($enc)." ORDER BY cen.date,cen.time DESC LIMIT 1 ";
        $row = $db->GetRow($this->sql);

        return $row;

	}

	function getListDiet($enc,$last){
		global $db;

		        $this->sql = "SELECT 
							  CASE
							    sdo.selected_type 
							    WHEN 'breakfast' 
							    THEN sdoi.b 
							    WHEN 'lunch' 
							    THEN sdoi.l 
							    WHEN 'dinner' 
							    THEN sdoi.d 
							  	END AS diet_name 
									FROM
									  seg_diet_order AS sdo 
									  LEFT JOIN `seg_diet_order_item` AS sdoi 
									    ON sdo.refno = sdoi.`refno` 
        					WHERE sdo.encounter_nr = ".$db->qstr($enc)." ORDER BY sdo.refno LIMIT 1 ";
        $row = $db->GetRow($this->sql);

        return $row;

	}

	function getNameDr($dr_nr){
		global $db;
				$this->sql = "SELECT diet_name,sd.diet_code as diet_code
								FROM seg_diet as sd WHERE sd.diet_code= ".$db->qstr($dr_nr);
		        // $this->$sql = "SELECT *, fn_get_personellname_lastfirstmi (cp.nr) AS doc_name 
				// 				FROM `care_personell` AS cp 
				// 				INNER JOIN `care_personell_assignment` AS cpa 
				// 				ON cp.`nr` = cpa.`personell_nr`
				// 				WHERE (cp.short_id LIKE 'D%') AND cpa.`location_nr` IN (".$db->qstr($dept_id).")
				// 				AND cp.status NOT IN ('deleted')";
				$ok = $db->Execute($sql);
        $row = $db->GetRow($this->sql);

        return $row;

	}

	function getNameDiet($code){
		global $db;

		        $this->sql = "SELECT diet_name,sd.diet_code as diet_code
							  FROM seg_diet as sd WHERE sd.diet_code= ".$db->qstr($code);
        $row = $db->GetRow($this->sql);

        return $row;

	}

	function getLastUpdate($enc){
		global $db;

		        $this->sql = "SELECT sdo.`selected_type` FROM `seg_diet_order` AS sdo WHERE sdo.`encounter_nr`= ".$db->qstr($enc);
        $row = $db->GetRow($this->sql);

        return $row;

	}

		function getBMI($bmi){
		global $db;

		        $this->sql = "SELECT bmi.`bmi_category` AS cat FROM seg_bmi_category bmi 
WHERE bmi.`bmi` >= ".$db->qstr($bmi)." LIMIT  1 ";
        $row = $db->GetRow($this->sql);

        return $row['cat'];

	}

			function getBMI2($bmi){
		global $db;

		        $this->sql = "SELECT bmi.`bmi_category` AS cat FROM seg_bmi_category bmi 
ORDER by bmi.bmi DESC LIMIT  1 ";
        $row = $db->GetRow($this->sql);

        return $row['cat'];

	}

	function getOralDietList()
	{
			global $db;
 		$this->sql = "SELECT * from seg_diet as sd WHERE sd.is_oral='1' AND sd.status='active'";
 		if($this->result=$db->Execute($this->sql)) {
			$this->count = $this->result->RecordCount();
			return $this->result;
		}
	}
	function getTubeFeedingList()
	{
			global $db;
 		$this->sql = "SELECT * from seg_diet as sd WHERE sd.is_tubefeeding='1' AND sd.status='active'";
 		if($this->result=$db->Execute($this->sql)) {
			$this->count = $this->result->RecordCount();
			return $this->result;
		}
	}


	#Added by Matsu 12272018
	function getNourishpatient(){
    	global $db;

    
    	$this->sql = "SELECT 
						enc.encounter_nr,
						p.pid,
						p.date_birth,
						p.sex,
						p.name_first,
						p.name_last,
						IF(fn_calculate_age(NOW(),p.date_birth),
					       fn_get_age(NOW(),p.date_birth),'') AS age,
						fn_get_person_lastname_first(p.`pid`) AS uname,
						(SELECT 
					    	cen.notes 
					  	FROM
					    	care_encounter_notes cen 
					  	WHERE cen.encounter_nr = enc.encounter_nr 
					  	ORDER BY cen.date DESC,cen.time DESC LIMIT 1) AS notes,
					  	(SELECT 
					    	cen.nDiet 
					  	FROM
					    	care_encounter_notes cen 
					  	WHERE cen.encounter_nr = enc.encounter_nr 
					  	ORDER BY cen.date DESC,cen.time DESC LIMIT 1) AS diet,
					  	(SELECT 
					    	cen.nRemarks 
					  	FROM
					    	care_encounter_notes cen 
					  	WHERE cen.encounter_nr = enc.encounter_nr 
					  	ORDER BY cen.date DESC,
					    		cen.time DESC 
					  	LIMIT 1) AS nRemarks,
					   	(SELECT 
					    	cen.nIVF 
					  	FROM
					    	care_encounter_notes cen 
					  	WHERE cen.encounter_nr = enc.encounter_nr 
					  	ORDER BY cen.date DESC,cen.time DESC LIMIT 1) AS IVF,
						(SELECT 
							cen.nHeight 
						FROM
							care_encounter_notes cen 
						WHERE cen.encounter_nr = enc.encounter_nr 
						ORDER BY cen.date DESC,
								cen.time DESC 
						LIMIT 1) AS height,
						(SELECT 
							cen.nWeight 
						FROM
							care_encounter_notes cen 
						WHERE cen.encounter_nr = enc.encounter_nr 
						ORDER BY cen.date DESC,
								cen.time DESC 
						LIMIT 1) AS weight,
						(SELECT 
							  CASE
							    sdo.selected_type 
							    WHEN 'breakfast' 
							    THEN sdoi.b 
							    WHEN 'lunch' 
							    THEN sdoi.l 
							    WHEN 'dinner' 
							    THEN sdoi.d 
							  END AS diet_list 
						FROM
						  `seg_diet_order_item` AS sdoi 
						  INNER JOIN `seg_diet_order` AS sdo 
						    ON sdoi.refno = sdo.refno 
						WHERE sdo.encounter_nr = enc.`encounter_nr` ORDER BY sdoi.id DESC) AS diet_list ,
						(SELECT 
							cen.nBmi 
						FROM
							care_encounter_notes cen 
						WHERE cen.encounter_nr = enc.encounter_nr 
						ORDER BY cen.date DESC,
								cen.time DESC 
						LIMIT 1) AS nBmi,
		  			  (SELECT 
					    sba.bmi_category 
					  FROM
					    `seg_bmi_category` AS sba 
					  WHERE sba.bmi >= nBmi 
					  LIMIT 1) AS nBmi_name,
					 (SELECT 
							 sdoi.b 
						FROM
						  `seg_diet_order_item` AS sdoi 
						  INNER JOIN `seg_diet_order` AS sdo 
						    ON sdoi.refno = sdo.refno 
						WHERE sdo.encounter_nr = enc.`encounter_nr` ORDER BY sdoi.id DESC) AS b,
					 (SELECT 
							 sdoi.l
						FROM
						  `seg_diet_order_item` AS sdoi 
						  INNER JOIN `seg_diet_order` AS sdo 
						    ON sdoi.refno = sdo.refno 
						WHERE sdo.encounter_nr = enc.`encounter_nr` ORDER BY sdoi.id DESC) AS l,
					   (SELECT 
							 sdoi.d
						FROM
						  `seg_diet_order_item` AS sdoi 
						  INNER JOIN `seg_diet_order` AS sdo 
						    ON sdoi.refno = sdo.refno 
						WHERE sdo.encounter_nr = enc.`encounter_nr` ORDER BY sdoi.id DESC) AS d
					FROM
						$this->tb_enc AS enc 
					  	LEFT JOIN care_person AS p 
					    	ON enc.pid = p.pid 
					  	LEFT JOIN care_ward AS w 
					    	ON enc.current_ward_nr = w.nr 
					    LEFT JOIN seg_nourishment as sn
					    	ON sn.encounter_nr = enc.encounter_nr
					WHERE sn.is_nourish='1' AND enc.current_ward_nr NOT IN ('445','21','205','425','66','68','119')";

					// die($this->sql);
		if($this->result=$db->Execute($this->sql)) {
			$this->count = $this->result->RecordCount();
			return $this->result;
		} else { return false; }
    }

    #all diet id must be update.
    function getDietNames(){
    	global $db;


    		$this->sql = "SELECT sd.`diet_code` , sd.`diet_name`,sd.`alt_code` FROM seg_diet AS sd WHERE sd.`diet_code` IN ('HA','JeJu','LC','SP','LD','LFLS','F100','F75','HD','MF','ReD','MF')";
		if($this->result=$db->Execute($this->sql)) {
			$this->count = $this->result->RecordCount();
			return $this->result;
		} else { return false; }


    }

    function getAltNameDiet($code){
		global $db;

		        $this->sql = "SELECT diet_name,IFNULL(sd.alt_code,sd.diet_code) as diet_code
							  FROM seg_diet as sd WHERE IFNULL(sd.alt_code,sd.diet_code)= ".$db->qstr($code);
        $row = $db->GetRow($this->sql);

        return $row;

	}

	function getVitalDetails($enc)
	{
		global $db;

		$this->sql = "SELECT encounter_nr,height,weight
        				FROM seg_encounter_vital_sign_bmi 
        				WHERE encounter_nr = ".$db->qstr($enc)." 
        					AND is_deleted = 0
        				ORDER BY create_dt DESC
						LIMIT 1";

		$row = $db->GetRow($this->sql);

		return $row;
	}

	public function getBMICategory($pid, $height, $weight)
	{
		global $db;
		$dbDate = $db->GetOne("SELECT CURDATE()");

		$birthDate = $db->GetOne("SELECT t.date_birth FROM care_person t WHERE t.pid=".$db->qstr($pid));

		$birthDate = strtotime($birthDate);
		$currentDate = strtotime($dbDate);

		$diff = $currentDate - $birthDate;
		$months = floor(floatval($diff) / (60 * 60 * 24 * 365 / 12));

		$height = number_format($height,2);
		$weight = number_format($weight,2);
		$metric = ( $weight / ($height * $height) * 10000 );
		$val = round($metric,2);

		$eighteen = 216;
		$seventeen = 215;
		$five = 60;

		if ($months >= $eighteen) {
			$bmi = $val;
			$category = $this->getBMI($val);
			$category = $bmi. ' - '.$category;
		} elseif ($months >= $five && $months <= $seventeen) {
			$category = $val;
		} else {
			$category = 'No results';
		}

		return $category;
	}

	public function getAutoBMI($enc, $vHeight, $vWeight)
	{
		global $db;

		$height = number_format($vHeight, 2);
		$weight = number_format($vWeight, 2);
		$metric = ($weight / ($height * $height) * 10000);
		$val = round($metric, 2);

		$enc = $db->GetOne(
			'SELECT encounter_nr FROM care_encounter_notes WHERE encounter_nr ='
			. $db->qstr($enc) . ' AND is_deleted  = 0'
		);

		if ($enc) {
			$sql = 'UPDATE care_encounter_notes SET nWeight=' . $db->qstr(
					$vWeight
				) . ', nHeight=' . $db->qstr($vHeight) . ', nBmi=' . $db->qstr(
					$val
				) . ' WHERE encounter_nr =' . $db->qstr($enc);

			if ($result = $db->Execute($sql)) {
				if ($result->RecordCount()) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		return true;
	}

	function checkBMI($enc)
	{
		global $db;

		return $db->GetOne("SELECT encounter_nr
        				FROM seg_encounter_vital_sign_bmi 
        				WHERE encounter_nr = ".$db->qstr($enc)." 
        					AND is_deleted = 0");
	}

}
?>
