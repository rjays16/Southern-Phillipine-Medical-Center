<?php
/*------begin------ This protection code was suggested by Luki R. luki@karet.org ---- */
if (eregi('save_admission_data.inc.php',$PHP_SELF)) 
	die('<meta http-equiv="refresh" content="0; url=../">');	

$obj->setDataArray($HTTP_POST_VARS);

	
	$dischargeData = array(
    	'encounter_nr'	=> $encounter_nr,
    	'discharged_dt'	=>	$HTTP_POST_VARS['date'],
    	'status'	=> "discharged"
    );
	// var_dump($mode); 
	// var_dump($dischargeData); 
	// die();
switch($mode)
{	
	case 'create': 
					require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
					$ehr = Ehr::instance();
					$patient = $ehr->postDischargedPatient($dischargeData);
					$asd = $ehr->getResponseData();
					$EHRstatus = $patient->status;
					if(!$EHRstatus){
						// echo "<pre>";
						// var_dump($patient->status);
						// var_dump($patient->msg);
						// var_dump($patient->asd);
						// die();
					}
					if($obj->insertDataFromInternalArray() ) {
						if(isset($redirect)&&$redirect){
							#header("location:".$thisfile.URL_REDIRECT_APPEND."&target=$target&mode=details&encounter_nr=".$HTTP_SESSION_VARS['sess_en']."&nr=".$HTTP_POST_VARS['ref_notes_nr']);
							//<a href=show_medocs.php'.URL_APPEND.'&from=such&pid='.$zeile['pid'].'&encounter_nr='.$zeile['encounter_nr'].'&target=entry&tabs='.$tabs.'>
							//show_medocs.php?sid=a94c5f9b17e1fe69a214159fb6e71978&lang=en&pid=10000000&encounter_nr=2007500007&target=entry&mode=show&type_nr=1&encounter_class_nr = 2">
							//<a href="'.$thisfile.URL_APPEND.'&pid='.$HTTP_SESSION_VARS['sess_pid'].'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&target='.$target.'&mode=show&type_nr='.$type_nr.'&encounter_class_nr = '.$encounter_class_nr.'">
							
							header("location:".$thisfile.URL_APPEND."&pid=".$HTTP_SESSION_VARS['sess_pid']."&encounter_nr=".$HTTP_SESSION_VARS['sess_en']."&target=entry&mode=show&type_nr=".$type_nr."&encounter_class_nr=".$encounter_class_nr.$IPBMextend);
							#header("location:".$thisfile.URL_REDIRECT_APPEND."&from=such&pid=".$_POST['sess_pid']."&encounter_nr=".$encounter_nr."&target=entry");
								
							exit;
						}
					
					} else echo "$obj->sql<br>$LDDbNoSave";
					break;
								
	case 'update': 
					require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
					$ehr = Ehr::instance();
					$patient = $ehr->postDischargedPatient($dischargeData);
					$asd = $ehr->getResponseData();
					$EHRstatus = $patient->status;
					if(!$EHRstatus){
						// echo "<pre>";
						// var_dump($patient->status);
						// var_dump($patient->msg);
						// var_dump($patient->asd);
						// die();
					}
					$obj->where=' nr='.$nr;
					if($obj->updateDataFromInternalArray($nr)) {
						if($redirect){
							header("location:".$thisfile.URL_REDIRECT_APPEND."&target=$target&encounter_nr=".$HTTP_SESSION_VARS['sess_en'].$IPBMextend);
							echo "$obj->sql<br>$LDDbNoUpdate";
							exit;
						}
					} else echo "$obj->sql<br>$LDDbNoUpdate";
					break;
								
}// end of switch

?>
