<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
//define('LANG_FILE','doctors.php');
if($HTTP_SESSION_VARS['sess_user_origin']=='personell_admin'){
	$local_user='aufnahme_user';
}else{
	$local_user='ck_op_dienstplan_user';
}
require_once($root_path.'include/inc_front_chain_lang.php');

require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;
$pers_obj->useAssignmentTable();
$data=array();

//$db->debug=true;

if($mode!='delete'){
   
	$data['personell_nr']=$nr;
	
	#-----------add 02-24-07-----------
	$role_nr = $pers_obj->getRole_type($nr, $job_fxn);
	$loc_type = $pers_obj->getDeptInfo($dept_nr);
	#----------------------------------
	
	$data['role_nr'] = $role_nr['nr'];      //16; // 16 = nurse (role person)
	$data['location_type_nr'] = $loc_type['type'];  // 1 = dept (location type)  --- edited 02-24-07
	$data['location_nr']=$dept_nr;
	$data['date_start']=date('Y-m-d');
	
}

$data['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];

	
    $assignPersonnel = array(
        "personnel_nr"   =>  $data['personell_nr'],
        "groupName"     =>  $groupName,
        "dept_id" =>  $dept_nr,
        "role_id"   => $data['role_nr']
    );

    // var_dump($assignPersonnel); die();

switch($mode){
	case 'save':
						$data['history']="Add: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n";
						$data['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
						$data['create_time']=date('YmdHis');
						$pers_obj->setDataArray($data);

		                $assignNurse = array(
		                    "personnel_nr"   =>  $data['personell_nr'],
		                    "groupName"     =>  $groupName,
		                    "dept_id" =>  $dept_nr,
		                    "role_id"   => $data['role_nr']
		                );

		                require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
		                $ehr = Ehr::instance();
		                $patient = $ehr->doctor_postCreatePersonnelAssignment($assignNurse);
		                $response = $ehr->getResponseData();
		                $EHRstatus = $patient->status;
		                if(!$EHRstatus){
		                    // echo "<pre>";
		                    // var_dump($patient->status);
		                    // var_dump($assignDoctor);
		                    // var_dump($patient->asd);
		                    // die();
		                }

						if(!$pers_obj->insertDataFromInternalArray())  echo "$obj->sql<br>$LDDbNoSave";
						break;
	case 'update':
					$data['history']=$pers_obj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");
					#$pers_obj->setDataArray($data);
					$data['modfiy_id']=$HTTP_SESSION_VARS['sess_user_name'];
					$data['modify_time']=date('YmdHis');
					#-------------------
					$data['status']=" ";
					$personell_nr = $pers_obj->get_Person_name($nr);
					$assign_nr = $personell_nr['nr'];
					#-------------------
					$pers_obj->setDataArray($data);

		                $assignNurse = array(
		                    "personnel_nr"   =>  $data['personell_nr'],
		                    "groupName"     =>  $groupName,
		                    "dept_id" =>  $dept_nr,
		                    "role_id"   => $data['role_nr']
		                );

		                require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
		                $ehr = Ehr::instance();
		                $patient = $ehr->doctor_postCreatePersonnelAssignment($assignNurse);
		                $response = $ehr->getResponseData();
		                $EHRstatus = $patient->status;
		                if(!$EHRstatus){
		                    // echo "<pre>";
		                    // var_dump($patient->status);
		                    // var_dump($assignDoctor);
		                    // var_dump($patient->asd);
		                    // die();
		                }

					if(!$pers_obj->updateDataFromInternalArray($assign_nr))  echo "$obj->sql<br>$LDDbNoUpdate";
					#if(!$pers_obj->updateDataFromInternalArray($item_nr))  echo "$obj->sql<br>$LDDbNoUpdate";
					break;
	case 'delete':
					$data['status']='deleted';
					$data['date_end']=date('Y-m-d');
					$data['history']=$pers_obj->ConcatHistory("Deleted: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");
					$data['modfiy_id']=$HTTP_SESSION_VARS['sess_user_name'];
					$data['modify_time']=date('YmdHis');
					$pers_obj->setDataArray($data);
					if(!$pers_obj->updateDataFromInternalArray($item_nr))  echo "$obj->sql<br>$LDDbNoUpdate";
}

#header("location:nursing-or-dienst-personalliste.php".URL_REDIRECT_APPEND."&saved=1&retpath=$retpath&ipath=$ipath&dept_nr=$dept_nr&nr=$nr");
header("location:nursing-or-dienst-personalliste.php".URL_REDIRECT_APPEND."&saved=1&retpath=$retpath&ipath=$ipath&dept_nr=$dept_nr&nr=$nr&item_nr=$item_nr");
exit;
?>
