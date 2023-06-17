<?php
/**
* API class for Nursing Notes and Documentation.
*  Core 
*   |_ Notes
*         |_ NursingNotes
* @package care_api
*/

/**
*/
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/care_api_classes/class_notes.php');

/**
*  Nursing Notes and Documentation methods.
*  Note this class should be instantiated only after a "$db" adodb  connector object  has been established by an adodb instance.
* @author Elpidio Latorilla
* @version beta 2.0.1
* @copyright 2002,2003,2004,2005,2005 Elpidio Latorilla
* @package care_api
*/
class NursingNotes extends Notes {
	/**
	* Constructor
	*/			
	function NursingNotes(){
		$this->Notes();
	}
	/**
	* Checks if nursing report record exists in the database.
	* @param int Encounter number
	* @return boolean
	*/			
	function Exists($enr){
		if($this->_RecordExists("type_nr=15 AND encounter_nr=$enr")){
			return true;
		}else{return false;}
	}
	/**
	* Checks if nursing effectivity report record exists in the database.
	* @param int Encounter number
	* @return boolean
	*/			
	function EffectivityExists($enr){
		if($this->_RecordExists("type_nr=17 AND encounter_nr=$enr")){
			return true;
		}else{return false;}
	}	
	/**
	* Checks if daily ward notes record exists in the database.
	* @param int Encounter number
	* @return mixed integer = record number if exists, FALSE=boolean if not exists
	*/			
	function DailyWardNotesExists($enr){
		$buf;
		if($this->_RecordExists("type_nr=6 AND encounter_nr=$enr")){
			$buf=$this->result->FetchRow();
			return $buf['nr'];
		}else{return false;}
	}
	/**
	* Gets a nursing report based on the encounter_nr key.
	* @param int Encounter number
	* @return mixed adodby record object if exists, FALSE=boolean if not exists
	*/			
	function getNursingReport($enr){
		if($this->_getNotes(" type_nr=15 AND encounter_nr=$enr","ORDER BY date,time")){
			return $this->result;
		}else{
			return false;
		}
	}
	/**
	* Gets a nursing effectivity report based on the encounter_nr key.
	* @param int Encounter number
	* @return mixed adodby record object if exists, FALSE=boolean if not exists
	*/			
	function getEffectivityReport($enr){
		if($this->_getNotes(" type_nr=17 AND encounter_nr=$enr","ORDER BY date,time")){
			return $this->result;
		}else{
			return false;
		}
	}
	/**
	* Gets both nursing report and effectivity report based on the encounter_nr key.
	* @param int Encounter number
	* @return mixed adodby record object if exists, FALSE=boolean if not exists
	*/			
	function getNursingAndEffectivityReport($enr){
		global $db;
		$this->sql="SELECT n.*,
						e.nr AS eff_nr,
						e.notes AS eff_notes,
						e.aux_notes AS eff_aux_notes,
						e.date AS eff_date,
						e.time AS eff_time,
						e.personell_name AS eff_personell_name
					FROM $this->tb_notes AS n LEFT JOIN $this->tb_notes AS e ON n.nr=e.ref_notes_nr AND e.encounter_nr=$enr
					WHERE (n.type_nr=15 AND n.encounter_nr=$enr)
						OR (n.type_nr=17 AND n.encounter_nr=$enr)
					ORDER BY date,time";
		if($this->result=$db->Execute($this->sql)){
			if($this->result->RecordCount()){
				return $this->result;
			}else{return false;}
		}else{ 
			return false;
		}
	}
	/**
	* Saves a nursing report.
	*
	* The data must be contained in an associative array and passed by reference.
	* @param array Nursing data in associative array. Reference pass.
	* @return boolean
	*/			
	function saveNursingReport(&$data){
		if(empty($data)){
			return false;
		}else{
			$this->data_array['encounter_nr']=$data['pn'];
			$this->data_array['notes']=$data['berichtput'];
			$this->data_array['date']=$data['dateput'];
			$this->data_array['time']=$data['timeput'];
			$this->data_array['personell_name']=$data['author'];
			$this->data_array['aux_notes']=$data['warn'];
		}
		return $this->_insertNotesFromInternalArray(15);
	}
	/**
	* Saves a nursing effectivity report.
	*
	* The data must be contained in an associative array and passed by reference.
	* @param array Nursing effectivity data in associative array. Reference pass.
	* @return boolean
	*/			
	function saveEffectivityReport(&$data){
		if(empty($data)){
			return false;
		}else{
			$this->data_array['encounter_nr']=$data['pn'];
			$this->data_array['notes']=$data['berichtput2'];
			$this->data_array['date']=$data['dateput2'];
			$this->data_array['time']=$data['timeput'];
			$this->data_array['personell_name']=$data['author2'];
			$this->data_array['aux_notes']=$data['warn2'];
			$this->data_array['ref_notes_nr']=$data['ref_notes_nr'];
		}
		return $this->_insertNotesFromInternalArray(17);
	}
	/**
	* Gets the date range of a nursing report.
	* @param int Encounter number
	* @return mixed 1 dimensional array or boolean
	*/			
	function getNursingReportDateRange($enr){
		if($this->_getNotesDateRange($enr,0,"encounter_nr=$enr AND (type_nr=15 OR type_nr=17)")){
			return $this->result->FetchRow();
		}else{return false;}
	}
	/**
	* Gets all daily notes data of an encounter number.
	* @param int Encounter number
	* @return mixed adodb record object or boolean
	*/			
	function getDailyWardNotes($enr){
		global $db;       
		if($this->_getNotes("type_nr=6 AND encounter_nr=".$db->qstr($enr)."","ORDER BY date,time DESC LIMIT 1")){
			return $this->result;
		}else{
			return false;
		}
	}
	/**
	* Saves a ward notes of a day.
	* @param string Ward notes. Reference pass.
	* @return boolean
	*/			
	function saveDailyWardNotes(&$data){
/*		$buf;	*/
		global $db;    


		if(empty($data)){
			return false;
		}else{
			$encounter_nr = $db->qstr($data['pn']);
			$type_nr = 6;
			$notes = $db->qstr($data['impression']);

			// Added by Gerald 10/08/2020
			$services = $db->qstr($data['services']);
			$other = $db->qstr($data['other']);
			$diagnostic = $db->qstr($data['diagnostic']);
			$special = $db->qstr($data['special']);
			$additional = $db->qstr($data['additional']);
			$vs = $db->qstr($data['vs']);
			//End

			$modify_id = $db->qstr($_SESSION['sess_user_name']);
			$location_id = $db->qstr($data['station']);
			$dDate= $db->qstr(date('Y-m-d'));
			$tTime =$db->qstr(date('H:i:s'));
			$diet = $db->qstr($data['nDiet']);
			$remarks = $db->qstr($data['remarks']);
			$ivf = $db->qstr($data['ivf']);
			$height = number_format($data['height'],2);
			$weight = number_format($data['weight'],2);
			$avail_meds = $db->qstr($data['avail_meds']);
			$gadgets = $db->qstr($data['gadgets']);
			$problems = $db->qstr($data['problems']);
			$actions = $db->qstr($data['actions']);
			$created_id = $db->qstr($_SESSION['sess_user_name']);
			$create_time=$db->qstr(date('Y-m-d H:i:s'));	
			$modify_time=$db->qstr(date('Y-m-d H:i:s'));

 			$metric = ( number_format($data['weight'],2) / (number_format($data['height'],2) *number_format($data['height'],2)) * 10000 );
 			$bmi = round($metric,2);
			$nBmi = $db->qstr($bmi);

			$sql = "SELECT encounter_nr FROM care_encounter_notes WHERE encounter_nr = ".$encounter_nr." AND is_deleted = 0";
			$insert = 1;


			$db->StartTrans();
			$result = $db->Execute($sql);
						
			if($result->RecordCount() > 1){
				$history = $this->ConcatHistory("\nUpdated: ".date('Y-m-d H:i:s')." ".$_SESSION['sess_user_name']);
				$fields = array(
							'encounter_nr' 	=> $encounter_nr,
							'is_deleted' 	=> 1,
							'modify_time'	=> $modify_time,
							'modify_id'		=> $modify_id,
							'history'		=> $history
						  );

				$pk = array('encounter_nr');

				$ok = $db->Replace('care_encounter_notes', $fields, $pk);
				
				if($ok)
					$db->CompleteTrans();
				else
					$db->FailTrans();

			}elseif($result->RecordCount() == 1){
				$history = $this->ConcatHistory("\nUpdated: ".date('Y-m-d H:i:s')." by ".$_SESSION['sess_user_name']);
				$fields = array(
							'encounter_nr' 		=> $encounter_nr,
							'type_nr' 			=> $type_nr,
							'notes'				=> $notes,
							'personell_name'	=> $modify_id,
							'location_id'		=> $location_id,
							'date'				=> $dDate,
							'time'				=> $tTime,
							'nDiet'				=> $diet,
							'nRemarks'			=> $remarks,
							'nIVF'				=> $ivf,
							'nHeight'			=> $height,
							'nWeight'			=> $weight,
							'modify_id'			=> $modify_id,
							'modify_time' 		=> $modify_time,
							'is_deleted' 		=> $db->qstr('0'),
							'history'			=> $history,
							'nBmi' 				=> $nBmi,
							'avail_meds' 		=> $avail_meds,
							'gadgets' 			=> $gadgets,
							'problems' 			=> $problems,
							'actions' 			=> $actions,
							// Added by Gerald 10/08/2020
							'services'			=> $services,
							'other'				=> $other,
							'diagnostic'		=> $diagnostic,
							'special'			=> $special,
							'additional'		=> $additional,
							'vs'				=> $vs
							//End
						  );
						  					 
				$pk = array('encounter_nr','is_deleted');

				$success = $db->Replace('care_encounter_notes', $fields, $pk);
				$insert = 0;
				
				if($success)	
					$db->CompleteTrans();
					
				else
					$db->FailTrans();
			}
			if($insert){
				$history=$db->qstr("Create: ".date('Y-m-d H:i:s')." ".$_SESSION['sess_user_name']."\n\r");
				$this->sql = "INSERT INTO care_encounter_notes(
		 					`encounter_nr`,
		 					`type_nr`,
		 					`notes`,
		 					`personell_name`,
		 					`location_id`,
		 					`date`,
		 					`time`,
		 					`nDiet`,
		 					`nRemarks`,
		 					`nIVF`,
		 					`nHeight`,
		 					`nWeight`,
		 					`create_id`,
		 					`modify_id`,
		 					`create_time`,
		 					`modify_time`,
		 					`history`,
		 					`nBmi`,
		 					`avail_meds`,
		 					`gadgets`,
		 					`problems`,
		 					`actions`,
		 					`services`,
		 					`other`,
		 					`diagnostic`,
		 					`special`,
		 					`additional`,
		 					`vs`)
	                        VALUES(
	                       	$encounter_nr, $type_nr, $notes, $modify_id, $location_id, $dDate, $tTime, $diet, $remarks, $ivf, $height, $weight, $created_id, $modify_id, $create_time, $modify_time, $history,$nBmi,$avail_meds,$gadgets,$problems,$actions,$services,$other,$diagnostic,$special,$additional,$vs
	                        )";		

		        if($db->Execute($this->sql))
		            $db->CompleteTrans();
		        else
		        	$db->FailTrans();
			}
		}
	}

	function saveDietOrder($data){
		global $db;
		$db->BeginTrans();
		$updated_time=$db->qstr(date('Y-m-d H:i:s'));
		$view_sql= "SELECT encounter_nr FROM seg_diet_order where encounter_nr= ".$db->qstr($data['pn']);
		$result = $db->Execute($view_sql);
		
		$sqlTime = "SELECT TIME_FORMAT(CURTIME(), '%H:%i') AS CURTIME ";
		$exeTime = $db->GetRow($sqlTime);
		$time = $exeTime['CURTIME'];
		
		if($time >= $data['cutoff_time_lunch_from'] && $time <= $data['cutoff_time_lunch_to']) {
			$cutOffTime = 'Lunch';

		}else if($time >= $data['cutoff_time_dinner_from'] && $time <= $data['cutoff_time_dinner_to']) {
			$cutOffTime = 'Dinner';

		}else {
			$cutOffTime = 'BreakFast';
		}

		if($cutOffTime  == 'Lunch'){
				$mealCategory = ",l,d,";
				$mealData = ",".$db->qstr($data['diet']).",".$db->qstr($data['diet']).",";
		}elseif($cutOffTime  == 'Dinner'){
				$mealCategory = ",d,";
				$mealData =",".$db->qstr($data['diet']).",";
		}else{
				$mealCategory = ",b,l,d,";
				$mealData = ",".$db->qstr($data['diet']).",".$db->qstr($data['diet']).",".$db->qstr($data['diet']).",";

		}



		// die($cutOffTime);
		
		if($result->RecordCount()<1){
				$insert_order_sql = "INSERT seg_diet_order(encounter_nr,created_by,selected_type) VALUES (".$db->qstr($data['pn']).",".$db->qstr($_SESSION['sess_login_userid']).",".$db->qstr($cutOffTime).")";
				// var_dump($insert_order_sql);
				$ok = $db->Execute($insert_order_sql);
				if($ok){
					$row_order =$db->GetRow("SELECT refno from seg_diet_order WHERE encounter_nr=".$db->qstr($data['pn']));
					
					$insert_order_list = "INSERT seg_diet_order_item(refno".$mealCategory."created_at) VALUES(".$db->qstr($row_order['refno']).$mealData.$db->qstr($updated_time).")";
					// var_dump($insert_order_list);exit();
					$ok = $db->Execute($insert_order_list);

				}
		}
		else{

			$sql = "UPDATE seg_diet_order SET
								selected_type=".$db->qstr($cutOffTime).",
								updated_by=".$db->qstr($data['loginid']).",
								updated_at=".$updated_time."
								WHERE encounter_nr = ".$db->qstr($data['pn']);

			$ok = $db->Execute($sql);

			if($ok){
					$get_selected_type = "SELECT sdo.`selected_type`,sdoi.`id` FROM `seg_diet_order` AS sdo INNER JOIN `seg_diet_order_item` AS sdoi 
	   											 ON sdo.`refno` = sdoi.`refno` WHERE sdo.`encounter_nr` = ".$db->qstr($data['pn'])." ORDER BY sdoi.`id` DESC 
								LIMIT 1  ";
//								 var_dump($get_selected_type);exit();
					$row= $db->GetRow($get_selected_type);
					

					switch ($row['selected_type']) {
						case 'breakfast':
							$when_update = "b=".$db->qstr($data['diet']).",";
							$when_update.= "l=".$db->qstr($data['diet']).",";
							$when_update.= "d=".$db->qstr($data['diet']).",";
							break;
						case 'lunch':
							$when_update= "l=".$db->qstr($data['diet']).",";
							$when_update.= "d=".$db->qstr($data['diet']).",";
							break;
						case 'dinner':
						$when_update= "d=".$db->qstr($data['diet']).",";
							break;
					}
					$list_sql = "UPDATE seg_diet_order_item SET
								 		$when_update
								updated_at= ".$updated_time."
								WHERE id = ".$db->qstr($row['id']);
//								 var_dump($list_sql);exit();
				$ok = $db->Execute($list_sql);

			}
		}

		if($ok){
			 $db->CommitTrans();
  			 $success = TRUE;
		}
		else{
  			 $db->RollbackTrans();
  			 $success = FALSE;
  		}
		return $success;
			
}

function UpdateDietList($enc,$skd){
	global $db;
	
		$db->BeginTrans();

		if($skd=='BreakFast'){
			$view_sql= "SELECT sdo.`selected_type`,sdoi.`id`,sdoi.b,sdoi.l,sdoi.d FROM `seg_diet_order` AS sdo INNER JOIN `seg_diet_order_item` AS sdoi 
	   											 ON sdo.`refno` = sdoi.`refno` WHERE sdo.`encounter_nr` = ".$db->qstr($enc)." ORDER BY sdoi.`id` DESC 
								LIMIT 1 ";
			$result = $db->Execute($view_sql);
			if($result->RecordCount()>0){
				 while($row=$result->FetchRow()){
				 		switch ($row['selected_type']) {
						case 'breakfast':
							$when_update= "l=".$db->qstr($row['b']).",d=".$db->qstr($row['b']);
							break;
						case 'lunch':
							$when_update= "b=".$db->qstr($row['l']).",d=".$db->qstr($row['l']);
							break;
						case 'dinner':
							$when_update= "b=".$db->qstr($row['d']).",l=".$db->qstr($row['d']);
							break;
					}
						$list_sql = "UPDATE seg_diet_order_item SET
								 		$when_update
								WHERE id = ".$db->qstr($row['id']);
						$ok = $db->Execute($list_sql);
				 }
			}
		}



	return $ok;

}

function saveDietOrderCutOff($data){
	global $db;
	$db->BeginTrans();
	$updated_time=$db->qstr(date('Y-m-d H:i:s'));
	// print_r($data['pn']);
	$view_sql= "SELECT encounter_nr FROM seg_diet_order_cut_off where encounter_nr= ".$db->qstr($data['pn']);
	$result = $db->Execute($view_sql);
	
	$sqlTime = "SELECT TIME_FORMAT(CURTIME(), '%H:%i') AS CURTIME , NOW() as now ";
	$exeTime = $db->GetRow($sqlTime);
	$time = $exeTime['CURTIME'];
	$now  =  $exeTime['now'];
	// die($now);
	
	if($time >=  $data['cutoff_time_lunch_from'] && $time <=  $data['cutoff_time_lunch_to']) {
		$cutOffTime = 'Lunch';

	}else if($time >= $data['cutoff_time_dinner_from'] && $time <= $data['cutoff_time_dinner_to']) {
		$cutOffTime = 'Dinner';
	}

	$sqlDiet = "SELECT sdoi.`b` , sdoi.`l` , sdoi.`d` from `seg_diet_order` as sdo INNER JOIN `seg_diet_order_item` as sdoi ON sdo.`refno` = sdoi.`refno` WHERE sdo.`encounter_nr` = ".$db->qstr($data['pn']);
	$exeDiet = $db->GetRow($sqlDiet);



	if($result->RecordCount()<1){
		// die("x");
			$insert_order_sql = "INSERT seg_diet_order_cut_off(encounter_nr,created_by,selected_type) VALUES (".$db->qstr($data['pn']).",".$db->qstr($_SESSION['sess_login_userid']).",".$db->qstr($cutOffTime).")";
			// var_dump($insert_order_sql);
			$ok = $db->Execute($insert_order_sql);
			if($ok){
				$row_order =$db->GetRow("SELECT refno from seg_diet_order_cut_off WHERE encounter_nr=".$db->qstr($data['pn']));
				$insert_order_list = "INSERT seg_diet_order_item_cut_off(refno,b,l,d,created_at) VALUES(".$db->qstr($row_order['refno']).",".$db->qstr($exeDiet['b']).",".$db->qstr($exeDiet['l']).",".$db->qstr($exeDiet['d']).",".$db->qstr($now).")";
				// var_dump($insert_order_list);exit();
				$ok = $db->Execute($insert_order_list);

			}
	}
	else{
		// die("xx");

		$sql = "UPDATE seg_diet_order_cut_off SET
							selected_type=".$db->qstr($cutOffTime).",
							updated_at=".$db->qstr($now).",
							updated_by=".$db->qstr($data['loginid'])." 
							WHERE encounter_nr = ".$db->qstr($data['pn']);
					// print_r($sql);
		$ok = $db->Execute($sql);

		if($ok){
				$get_selected_type = "SELECT sdo.`selected_type`,sdoi.`refno` as id FROM `seg_diet_order_cut_off` AS sdo INNER JOIN `seg_diet_order_item_cut_off` AS sdoi 
												ON sdo.`refno` = sdoi.`refno` WHERE sdo.`encounter_nr` = ".$db->qstr($data['pn'])." ORDER BY sdoi.`id` DESC 
							LIMIT 1  ";
				$row= $db->GetRow($get_selected_type);
				// print_r($get_selected_type);


			$sqlDiet = "SELECT sdoi.`b` , sdoi.`l` , sdoi.`d` from `seg_diet_order` as sdo INNER JOIN `seg_diet_order_item` as sdoi ON sdo.`refno` = sdoi.`refno` WHERE sdo.`encounter_nr` = ".$db->qstr($data['pn']);
				// print_r($sqlDiet);
			$exeDiet = $db->GetRow($sqlDiet);

				switch ($row['selected_type']) {
					case 'breakfast':
						$when_update= "b=".$db->qstr($exeDiet['b']).",";
						break;
					case 'lunch':
						$when_update= "l=".$db->qstr($exeDiet['l']).",";
						break;
					case 'dinner':
					$when_update= "d=".$db->qstr($exeDiet['d']).",";
						break;
					
					
				}
				$list_sql = "UPDATE seg_diet_order_item_cut_off SET
									 $when_update
							updated_at= ".$db->qstr($now)."
							WHERE id = ".$db->qstr($row['id']);
							// var_dump($list_sql);die();
			$ok = $db->Execute($list_sql);

		}
	}

	if($ok){
		 $db->CommitTrans();
		   $success = TRUE;
	}
	else{
		   $db->RollbackTrans();
		   $success = FALSE;
	  }
	return $success;
		
}

	
}
?>
