<?php

require('./roots.php');
require($root_path.'include/inc_environment_global.php');

AjaxPersonnel::call($_GET['request']);

class AjaxPersonnel {
	public static function call($func) {
		$function = self::getFunctionName($func);

		if (!method_exists(new AjaxPersonnel, $function)) {
			die("Error method '$func' does not exitt ");
		} else {
			self::$function();
		}
	}

	private static function getFunctionName($func) {
		return 'get'.strtoupper($func[0]).substr($func, 1);
	}

	public function getSubCategory(){
		global $db;
		
		// $description = $db->GetOne("SELECT description FROM seg_phs_category WHERE id =? ", $_REQUEST['category']);
		$spcdescription = $db->GetOne("SELECT spc.description AS spcdescription
							FROM seg_phs_job_status AS spjs
							     INNER JOIN seg_phs_category AS spc
							      ON spjs.category = spc.id
							WHERE spjs.id =?", $_REQUEST['category']);
		echo $spcdescription;
		// echo "swesd";
	}

	public function getCheckDuplicateRisId(){
		global $db;

		$ris_id = $db->GetOne("SELECT ris_id FROM care_personell WHERE ris_id = " . $db->qstr($_REQUEST['risId']));

		echo $ris_id?true:false;
	}

	// Added by Matsuu 06182017
	public function getinserTINvArea(){
		global $db;
		$data ="";
		foreach ($_REQUEST['arae_value'] as $key => $value) {

			$personell_nr = $db->qstr($value["personell_nr"]);
			$area_code = $db->qstr("inv-".$value["area_code"]);
			$group_action = $db->qstr($value["group_action"]);
			$action_personell = $db->qstr($_SESSION['sess_temp_userid']);

			 $sql = "INSERT INTO seg_inv_ward_accr_trail SET
			 				personell_nr =$personell_nr,
			 				item =$area_code,
			 				action_personell=$action_personell,
			 				action_abbr='insert',
			 				group_action=$group_action";
			 $data = $db->Execute($sql);
		}
		echo json_encode($data);
		
	}
	public function getdeleteINvArea(){
		global $db;
		$data ="";
		foreach ($_REQUEST['arae_value'] as $key => $values) {

			$personell_nr = $db->qstr($values["personell_nr"]);
			$area_code = $db->qstr("inv-".$values["area_code"]);
			$group_action = $db->qstr($values["group_action"]);
			$action_personell = $db->qstr($_SESSION['sess_temp_userid']);

			 $sql = "INSERT INTO seg_inv_ward_accr_trail SET
			 				personell_nr =$personell_nr,
			 				item =$area_code,
			 				action_personell=$action_personell,
			 				action_abbr='delete',
			 				group_action=$group_action";

			 $data = $db->Execute($sql);
		}
		echo json_encode($data);
		
	}
	public function getInsertWardArea(){
		global $db;
		$data ="";
		foreach ($_REQUEST['ward_nr'] as $key => $value) {

			$personell_nr = $db->qstr($value["personell_nr"]);
			$area_code = $db->qstr("ward-".$value["ward_nr"]);
			$group_action = $db->qstr($value["group_action"]);
			$action_personell = $db->qstr($_SESSION['sess_temp_userid']);

			 $sql = "INSERT INTO seg_inv_ward_accr_trail SET
			 				personell_nr =$personell_nr,
			 				item =$area_code,
			 				action_personell=$action_personell,
			 				action_abbr='insert',
			 				group_action=$group_action";

			 $data = $db->Execute($sql);
		}
		echo json_encode($data);
		
	}
	public function getDeleteWardArea(){
		global $db;
		$data ="";
		foreach ($_REQUEST['ward_nr'] as $key => $value) {

			$personell_nr = $db->qstr($value["personell_nr"]);
			$area_code = $db->qstr("ward-".$value["ward_nr"]);
			$group_action = $db->qstr($value["group_action"]);
			$action_personell = $db->qstr($_SESSION['sess_temp_userid']);

			 $sql = "INSERT INTO seg_inv_ward_accr_trail SET
			 				personell_nr =$personell_nr,
			 				item =$area_code,
			 				action_personell=$action_personell,
			 				action_abbr='delete',
			 				group_action=$group_action";

			 $data = $db->Execute($sql);
		}
		echo json_encode($data);
		
	}
		public function getInsertAccreditationArea(){
		global $db;
		$data ="";
		// $arrayName = array();
		foreach($_REQUEST['accreditation_area'] as $key => $value) {

			// foreach ($value as $newkey => $newvalue) {
				$personell_nr = $db->qstr($value["personell_nr"]);
				$area_code = $db->qstr("accr-".$value["accreditation_area"]);
				$group_action = $db->qstr($value["group_action"]);
				$action_personell = $db->qstr($_SESSION['sess_temp_userid']);
			
			 $sql = "INSERT INTO seg_inv_ward_accr_trail SET
			 				personell_nr =$personell_nr,
			 				item =$area_code,
			 				action_personell=$action_personell,
			 				action_abbr='insert',
			 				group_action=$group_action";

			 $data = $db->Execute($sql);
			// }
			
		}
		echo json_encode($data);
		
	}

	public function getDeleteAccreditationArea(){
		global $db;
		$data ="";
		// $arrayName = array();
		foreach($_REQUEST['accreditation_area'] as $key => $value) {

			// foreach ($value as $newkey => $newvalue) {
				$personell_nr = $db->qstr($value["personell_nr"]);
				$area_code = $db->qstr("accr-".$value["accreditation_area"]);
				$group_action = $db->qstr($value["group_action"]);
				$action_personell = $db->qstr($_SESSION['sess_temp_userid']);
			
			 $sql = "INSERT INTO seg_inv_ward_accr_trail SET
			 				personell_nr =$personell_nr,
			 				item =$area_code,
			 				action_personell=$action_personell,
			 				action_abbr='delete',
			 				group_action=$group_action";

			 $data = $db->Execute($sql);
			// }
			
		}
		echo json_encode($data);
		
	}
	// Ended by Matsuu 

}