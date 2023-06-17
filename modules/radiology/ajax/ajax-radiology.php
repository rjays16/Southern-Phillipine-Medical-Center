<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_radiology.php');

AjaxRadiology::call($_GET['request']);

class AjaxRadiology {
	public static function call($func){
		$function = self::getFunctionName($func);

		if (!method_exists(new AjaxRadiology, $function)) {
			die("Error method '$func' does not exit");
		}else{
			self::$function();
		}
	}

	public static function getFunctionName($func) {
		return 'get'.strtoupper($func[0]).substr($func, 1);
	}

	public function getSubLevel(){
		global $db;
		// $db->debug = true;
		// $q = ;
		$index_name_2 = $db->GetALL("SELECT index_name_2, id2 FROM seg_radio_index_level_02 WHERE id_level_01 =? ", array($_REQUEST['level1']));
		// var_dump($index_name_2);
		// die();
		echo json_encode($index_name_2);

	}

	public function getSubLevel2(){
		global $db;
		// $db->debug = true;
		// $q = ;
		$index_name_3 = $db->GetALL("SELECT index_name_3, id3 FROM seg_radio_index_level_03 WHERE fk_lvl_one =? ", array($_REQUEST['level2']));
		// var_dump($index_name_2);
		// die();
		echo json_encode($index_name_3);

	}

	public function getSubLevel3(){
		global $db;
		// $db->debug = true;
		// $q = ;
		$index_name_4 = $db->GetALL("SELECT index_name_4, id4 FROM seg_radio_index_level_04 WHERE index_id_3 =? ", array($_REQUEST['level3']));
		// var_dump($index_name_2);
		// die();
		echo json_encode($index_name_4);

	}

	public function getSaveRadioDiagnosis(){
		$radio_obj = new SegRadio;
		$is_save = $radio_obj->addRadioDiagnosis($_GET['refno'], $_GET['findings_nr'], $_GET['lv1'], $_GET['lv2'], $_GET['lv3'], $_GET['lv4'], $_GET['modified_id']);
		if ($is_save) {
			echo json_encode($radio_obj->getLatestDiagnosisId($_GET['refno'],$_GET['findings_nr']));
		} else {
			echo false;
		}
		// addRadioDiagnosis($refno, $level1, $level2, $level3, $level4)
	}

	public function getDeleteRadioDiagnosis(){
		$radio_obj = new SegRadio;
		echo $radio_obj->deleteRadioDiagnosis($_GET['id'],$_GET['modified_id']);
	}

	

	public function getViewRadioDiagnosis(){
		$radio_obj = new SegRadio;
		// echo $radio_obj->viewRadioDiagnosis($_GET['id']);
		echo json_encode($radio_obj->viewRadioDiagnosis($_GET['id']));

	}
}