<?php

include_once('roots.php');
include_once($root_path.'include/inc_environment_global.php');

AjaxImageUpload::call($_GET['a'],$_POST);

class AjaxImageUpload {

	public static function call($functionName, $parameters){
		$me = new AjaxImageUpload;
		$functionName =  self::getFunctionName($functionName);
		if(method_exists($me,$functionName)){
			call_user_func_array(array($me,$functionName),$parameters);
		}else{

		}
	}

	private static function getFunctionName($functionName){
		return 'action'.strtoupper($functionName[0]).substr($functionName, 1);
	}

	private static function getPersonnelSignature($personnelId){
		global $db;
		return $db->GetOne("SELECT signature_filename FROM care_personell WHERE nr = ?",$personnelId);
	}

	public static function actionDeletePersonnelSignature($personnelId){
		if($personnelId){
			self::deletePersonnelSignature($personnelId);
			print json_encode(array('element'=>'alert','message'=>'Image deleted'));
		}else{
			print json_encode(array('element'=>'alert','message'=>'Error'));
		}
	}

	private static function deletePersonnelSignature($personnelId){
		global $db,$root_path;
		$signature = self::getPersonnelSignature($personnelId);
		if($signature){
			$target_dir = $root_path . "fotos/signatures/";
			if(file_exists($target_dir.$signature)){
				try {
					unlink($target_dir.$signature);
					$date = date('Y-m-d H:i:s');
					$userId = $_SESSION['sess_temp_userid'];
					$history = "Update: $date = $userId, delete signature\n";
					$rs = $db->Execute("UPDATE care_personell SET signature_filename=NULL,history=CONCAT(history,?) WHERE nr=?",array($history,$personnelId));
					if(!$rs)
						return false;
				} catch (Exception $e) {
					return false;
				}
			}
		}
		return true;
	}

	private static function actionSavePersonnelSignature(){
		global $db,$root_path;

		$personnelId = $_POST['personell_nr'];
		$file = $_FILES['upload-file'];
		$image = getimagesize($file["tmp_name"]);
		$resolution = explode(' ', $image[3]);

		$target_dir = $root_path . "fotos/signatures/";

		if(!is_dir($target_dir)){
			mkdir($target_dir,0777);
		}

		$type = pathinfo($target_dir . basename($file["name"]), PATHINFO_EXTENSION);
		$width = doubleval(trim(substr($resolution[0], strpos($resolution[0], '=')+1),'"'));
		$height = doubleval(trim(substr($resolution[1], strpos($resolution[1], '=')+1),'"'));
		$fileName = "{$personnelId}.{$type}";
		$target_file = $target_dir . $fileName;

		if ($type != "jpg" && $type != "jpeg"){
			print json_encode(array('element'=>'file-type','message'=>''));
			die;
		}

		if ($file["size"] > 2097152){//2mb
			print json_encode(array('element'=>'max-file-size','message'=>''));
			die;
		}

		if($image === false){
			print json_encode(array('element'=>'alert','message'=>'Not an image'));
			die;
		}

		if(!self::deletePersonnelSignature($personnelId)){
			print json_encode(array('element'=>'alert','message'=>'Error!'));
			die;
		}

		if (!move_uploaded_file($file["tmp_name"], $target_file)){
			print json_encode(array('element'=>'alert','message'=>'Error uploading file'));
			die;
		}else{
			$date = date('Y-m-d H:i:s');
			$userId = $_SESSION['sess_temp_userid'];
			$history = "Update: $date = $userId, upload signature\n";
			$rs = $db->Execute("UPDATE 
									care_personell 
								SET history=CONCAT(history,?),
								signature_filename=?
								WHERE nr = ?",array(
									$history,
									$fileName,
									$personnelId
								));

			if ($width > 200 || $height > 50){
				print json_encode(array('element'=>'alert','message'=>"Warning: Image was uploaded but it \nexceeds the recommended resolution"));
				die;
			}

			if($rs)
				print json_encode(array('element'=>'alert','message'=>'Image uploaded!'));
			else
				print json_encode(array('element'=>'alert','message'=>'Error!'));
			die;
		}
	}
	
}//end class