<?php
/**
 * @author : Syross P. Algabre
 * Created : 12/16/2015 : meow
 * Description : Add Remarks in Personnel Management; Independent
 * Bug Number : SPMC-205
 */

require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_seg_dependents.php');

AjaxDependent::call($_GET['request']);

class AjaxDependent {
	public static function call($func){
		$function = self::getFunctionName($func);

		if (!method_exists(new AjaxDependent, $function)) {
			die("Error method '$func' does not exit");
		}else{
			self::$function();
		}
	}

	public static function getFunctionName($func) {
		return 'get'.strtoupper($func[0]).substr($func, 1);
	}

	public function getSaveDependentRemarks(){
		$dependent_Obj = new SegDependents;
		$is_save = $dependent_Obj->addDependentRemarks($_GET['pid'], $_GET['remarks'], $_GET['sess_user']);
		if ($is_save) {
			echo json_encode($dependent_Obj->getLastestDependentRemarks($_GET['pid']));
		}else{
			echo false;
		}
	}

	public function getDeleteDepRemarks(){
		$dependent_Obj = new SegDependents;
		echo $dependent_Obj->deleteDependentRemarks($_GET['id'], $_GET['sess_user']);
	}

	public function getDataDepRemarks(){
		$dependent_Obj = new SegDependents;
		echo json_encode($dependent_Obj->DataDependentRemarks($_GET['id']));
	}

	public function getUpdateDepRemarks(){
		$dependent_Obj = new SegDependents;
		$is_update = $dependent_Obj->updateDependentRemarks($_GET['id'], $_GET['pid'], $_GET['remarks'], $_GET['sess_user']);
		if ($is_update) {
			echo json_encode($dependent_Obj->indexDependentsRemarks($_GET['pid']));
		}else{
			echo false;
		}
	}

	#added rnel
	public function getPersonellStatus() {

		$personellObj = new SegDependents;

		$status = $personellObj->personellStatus($_GET['pid']);
		
		if($status) {
			echo true;
		} else {
			echo false;
		}
		
	}

}