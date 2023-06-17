<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

require($root_path.'modules/medocs/ajax/medocs_common.php'); //add by mark 
require($root_path.'include/inc_environment_global.php');

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
$thisfile=basename(__FILE__);

if(!isset($type_nr)||!$type_nr) $type_nr=1; //* 1 = history physical notes

require_once($root_path.'include/care_api_classes/class_notes.php');
$obj=new Notes;
$types=$obj->getAllTypesSort('name');
$this_type=$obj->getType($type_nr);

//$db->debug=1;

require($root_path.'include/care_api_classes/class_medocs.php');
$objResDisp = new Medocs;

require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_department.php');

$dept_obj=new Department;
$pers_obj=new Personell;



if(!isset($mode)){
	$mode='show';
		
//} elseif(($mode=='create'||$mode=='update')
//				&&!empty($HTTP_POST_VARS['text_diagnosis'])
//				&&!empty($HTTP_POST_VARS['text_therapy'])) {
// If mode=='crete or update
// Save new diagnosis and procedure to care_notes and care_encounter_diagnosis  
} elseif(($mode=='create'||$mode=='update')) {
	# Prepare the posted data for saving in databank
	include_once($root_path.'include/inc_date_format_functions.php');
	
	# If date is empty,default to today
	if(empty($HTTP_POST_VARS['date'])){
		$HTTP_POST_VARS['date']=date('Y-m-d');
	}else{
		$HTTP_POST_VARS['date']=@formatDate2STD($HTTP_POST_VARS['date'],$date_format);
	}
	
	$HTTP_POST_VARS['aux_notes']=substr($HTTP_POST_VARS['aux_notes'],0,255);
	$HTTP_POST_VARS['history']='Entry: '.date('Y-m-d H:i:s').' '.$HTTP_SESSION_VARS['sess_user_name'];
	$HTTP_POST_VARS['time']=date('H:i:s');
	$HTTP_POST_VARS['type_nr']=12; // 12 = text_diagnosis
	
	//$enc_obj->saveEncounterCondition(&$data) - save condition
	//$enc_obj->updateEncounterCondition($item_nr='',$code) -update condition
	//$enc_obj-> saveEncounterDisposition(&$data) - save Disposition
	//$enc_obj->updateEncounterDisposition($item_nr='',$code) - update disposition
	//$enc_obj->saveEncounterResults(&$data) - save result
	//$enc_obj->updateEncounterResults($item_nr='', $code) - update result
	
	
	//save codition from er
	/*if($encounter_type==1){
		
		if(isset($_POST_VARS['con'])){
			$condition['cond_code']=
			$condition
		}
	}
	
	*/
	
	
	if($enc_Info['encounter_type']=='3' || $enc_Info['encounter_type']=='4'){
		
		
		if($objResDisp->saveResultDispFromArray($HTTP_POST_VARS['res'],0)){
			if($objResDisp->saveResultDispFromArray($HTTP_POST_VARS['disp'],1)){
					
				$start=FALSE;
				foreach ($_POST['icdCodeID'] as $i=>$v) {	
					$HTTP_POST_VARS['code']=$v;
					$HTTP_POST_VARS['notes']=$_POST['icdCodeDesc'][$i];
					
					$redirect=false;
					include('./include/save_admission_data.inc.php');
					
					if (!$start){
						$insid=$db->Insert_ID();
						$HTTP_POST_VARS['ref_notes_nr']=$obj->LastInsertPK('nr',$insid);
						$start=TRUE;
					}
				}
				$HTTP_POST_VARS['type_nr']=13; // 12 = text_diagnosis
				$g=0;
				$k=count($_POST[icpCodeID]);
				foreach ($_POST[icpCodeID] as $i=>$v) {
					$HTTP_POST_VARS['code']=$v;
					$HTTP_POST_VARS['notes']=$_POST['icpCodeDesc'][$i];
					
					if ($k==$g+1)$redirect=TRUE;
					
					include('./include/save_admission_data.inc.php');
					$g++;
				}
			}
		}
	}else{
		$start=FALSE;
		foreach ($_POST['icdCodeID'] as $i=>$v) {	
			$HTTP_POST_VARS['code']=$v;
			$HTTP_POST_VARS['notes']=$_POST['icdCodeDesc'][$i];
			
			$redirect=false;
			include('./include/save_admission_data.inc.php');
			
			if (!$start){
				$insid=$db->Insert_ID();
				$HTTP_POST_VARS['ref_notes_nr']=$obj->LastInsertPK('nr',$insid);
				$start=TRUE;
			}
		}
		$HTTP_POST_VARS['type_nr']=13; // 12 = text_diagnosis
		$g=0;
		$k=count($_POST[icpCodeID]);
		foreach ($_POST[icpCodeID] as $i=>$v) {
			$HTTP_POST_VARS['code']=$v;
			$HTTP_POST_VARS['notes']=$_POST['icpCodeDesc'][$i];
			
			if ($k==$g+1)$redirect=TRUE;
			
			include('./include/save_admission_data.inc.php');
			$g++;
		}
		
	}

}// End of (if mode='create' || mode='update')

require('./include/init_show.php');

$page_title=$LDMedocs;

# Load the entire encounter data
require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter($encounter_nr);
$enc_obj->loadEncounterData();
# Get encounter class
$enc_class=$enc_obj->EncounterClass();
/*if($enc_class==2)  $HTTP_SESSION_VARS['sess_full_en']=$GLOBAL_CONFIG['patient_outpatient_nr_adder']+$encounter_nr;
	else $HTTP_SESSION_VARS['sess_full_en']=$GLOBAL_CONFIG['patient_inpatient_nr_adder']+$encounter_nr;
*/
$HTTP_SESSION_VARS['sess_full_en']=$encounter_nr;
	
if(empty($encounter_nr)&&!empty($HTTP_SESSION_VARS['sess_en'])){
	$encounter_nr=$HTTP_SESSION_VARS['sess_en'];
}elseif($encounter_nr) {
	$HTTP_SESSION_VARS['sess_en']=$encounter_nr;
}

//$patient_enc = $encounter_obj->getPatientEncounter($row['encounter_nr']); echo $patient_enc['encounter_type'];

$enc_Info=$enc_obj->getEncounterInfo($encounter_nr);

$patient_enc = $enc_obj->getPatientEncounter($encounter_nr);
//echo "objResDisp->".$objResDisp;

//echo "mode = ".$mode."<br> \n";
//echo "encounter_nr = ".$encounter_nr."<br> \n";
//echo "<br>glob->obj->".$glob_obj."<br>";
if (($encounter_class_nr==2)&&($encounter_type==2)){
	# Load all  doctors in OPD
	$doctor_dept=$pers_obj->getDoctors(0);
	$all_meds=&$dept_obj->getAllOPDMedicalObject(0);
}else{
	# Load all  doctors in IPD
	$doctor_dept=$pers_obj->getDoctors(1);
	$all_meds=&$dept_obj->getAllOPDMedicalObject(1);
}

require_once($root_path.'/include/care_api_classes/class_drg.php');

$objDRG= new DRG;


//It show list of diagnosis and procedures
if($mode=='show'){
	
	// show other diagnosis list and procedure
	if($tabs==0){
	
		//$rcode=$objDRG->getDiagnosisCodes($enc_nr=0);
		//if($HTTP_SESSION_VARS['sess_en']!=$encounter_nr) $HTTP_SESSION_VARS['sess_en']=$encounter_nr;
		
	/*	$a_sql="SELECT c.code AS code, d.description as diagnosis, c.create_id, date(c.create_time) AS date, c.diagnosing_dept_nr ".
			"\n FROM care_encounter_diagnosis c ".
			"\n LEFT JOIN care_icd10_en d ON c.code = d.diagnosis_code ".
			"\n WHERE c.encounter_nr ='$encounter_nr'";
		
		$b_sql = "SELECT p.code, o.description AS therapy, p.create_id, date(p.create_time) AS date, p.responsible_dept_nr ".
			"\n FROM care_encounter_procedure p ".
			"\n LEFT JOIN care_ops301_en o ON p.code=o.code ".
			"\n WHERE p.encounter_nr='$encounter_nr'";
	*/

	$a_sql= "SELECT 1 as type, c.code AS code, d.description as diagnosis, c.create_id, date(c.create_time) AS date, ".
			"\n c.diagnosing_dept_nr as dept_nr FROM care_encounter_diagnosis c LEFT JOIN care_icd10_en d ON c.code = d.diagnosis_code ".
			"\n WHERE c.encounter_nr ='$encounter_nr' ".
			"\n union ".
			"\n SELECT 2 as type, p.code as code, o.description AS diagnosis, p.create_id, date(p.create_time) AS date, ".
			"\n p.responsible_dept_nr as dept_nr FROM care_encounter_procedure p LEFT JOIN care_ops301_en o ON p.code=o.code ".
			"\n WHERE p.encounter_nr='$encounter_nr'";
	
	  $d_sql= "select distinct (d.diagnosing_dept_nr) as seg_dept  FROM care_encounter_diagnosis as d ".
	  		"\n where d.encounter_nr ='$encounter_nr' ".
			"\n UNION ". 
			"\n select distinct (p.responsible_dept_nr) as seg_dept  FROM care_encounter_procedure as p ".
			"\n where p.encounter_nr ='$encounter_nr'";
			
			
	}else{ //show principal diagnosis and procedure tabs=1
		
		//Query for diagnosis
		$a_sql ="SELECT c.diagnosis_code AS code, d.description as diagnosis, c.create_id,date(c.create_time) as date". 
			 "\n FROM seg_encounter_icd c ". 
			 "\n LEFT JOIN care_icd10_en d on c.diagnosis_code = d.diagnosis_code".
			 "\n  WHERE c.encounter_nr ='$encounter_nr'";
		
		 //Query for procedures
		 $b_sql= "SELECT c.procedure_code AS code, d.description as therapy, c.create_id,date(c.create_time) as date".
			 "\n FROM seg_encounter_icp c".
			 "\n LEFT JOIN seg_icpm d on c.procedure_code = d.procedure_code".
			 "\n WHERE c.encounter_nr='$encounter_nr'";

	}//End of if(tabs) # mark added: March 24, 2007
	$result=NULL;
	
	//Diagnosis
	if($result=$db->Execute($a_sql)){	
		if($rows=$result->RecordCount()){
			if($HTTP_SESSION_VARS['sess_en']!=$encounter_nr) $HTTP_SESSION_VARS['sess_en']=$encounter_nr;
		}
	}else{
		echo "$LDDbNoRead<p>$a_sql";
	}
	
	//$result_icp=NULL;
	
	//Procedure
	/*
	if($pro=$db->Execute($b_sql)){
		if($rowsB=$pro->RecordCount()){
			if($HTTP_SESSION_VARS['sess_en']!=$encounter_nr) $HTTP_SESSION_VARS['sess_en']=$encounter_nr;
		}
	}else{
		echo "$LDDbNoRead<p>$b_sql";
	}
	*/
	echo "<br>tabs->".$tabs;
	echo "<br>d_sql".$d_sql;
	if($tabs==0){
		if($segdept=$db->Execute($d_sql)){
			if($segdept->RecordCount()){
				//if($HTTP_SESSION_VARS['sess_en']!=$encounter_nr) $HTTP_SESSION_VARS['sess_en']=$encounter_nr;
			}
		}else{
			echo "$LDDbNoRead<p>$b_sql";
		}
	}
	//$rows = $rowsA + $rowsB;
		/*
		if($t1=$db->Execute($sql)){	
			if($rows=$t1->RecordCount()){
				$temp = $t1;
				$result=$temp->FetchRow();
				$result['code']='';
				$result['diagnosis']='';
				while($t2=$t1->FetchRow()){
					$result['diagnosis'].= $t2['code']." : ".$t2['diagnosis']." <br> \n";
				}
			}else{
				//echo "$LDDbNoRead<p>$sql";
			}
		}else{
			//echo $sql;
		} */
	
	
/*
 	if($result=$db->Execute($sql)){
		if($rows=$result->RecordCount()){
			# Resync the encounter_nr
			if($HTTP_SESSION_VARS['sess_en']!=$encounter_nr) $HTTP_SESSION_VARS['sess_en']=$encounter_nr;
			if($rows==1){
				$row=$result->FetchRow();
				if($row['is_discharged']) $edit=0;

				header("location:".$thisfile.URL_REDIRECT_APPEND."&target=$target&mode=details&nolist=1&pid=$pid&encounter_nr=&encounter_nr&nr=".$row['nr']."&edit=$edit&is_discharged=".$row['is_discharged']);
				exit;
			}
		}
	}else{
		echo "$LDDbNoRead<p>$sql";
		}
  
 */	

//Show the detailed description of diagnosis and procedures
#}elseif(($mode=='details')&&!empty($nr)){
}elseif(($mode=='details')&&!empty($encounter_nr)){
	$sql ="SELECT c.diagnosis_code AS code, d.description as diagnosis, c.create_id,date(c.create_time) as date". 
		 "\n FROM seg_encounter_icd c ". 
		 "\n LEFT JOIN care_icd10_en d on c.diagnosis_code = d.diagnosis_code".
		 "\n  WHERE c.encounter_nr ='$encounter_nr'";
	
	echo "sql= ".$sql;
	$result=NULL;
	if($t1=$db->Execute($sql)){	
		if($rows=$t1->RecordCount()){
			$temp = $t1;
			$result=$temp->FetchRow();
			$result['code']='';
			$result['diagnosis']='';
			while($t2=$t1->FetchRow()){
				$result['diagnosis'].= $t2['code']." : ".$t2['diagnosis']."\n";
			}
		}else{
			//echo "$LDDbNoRead<p>$sql";
		}
	}else{
		//echo $sql;
	}


	$sql= "SELECT c.procedure_code AS code, d.description as therapy, c.create_id,date(c.create_time) as date".
	"\n FROM seg_encounter_icp c".
	"\n LEFT JOIN seg_icpm d on c.procedure_code = d.procedure_code".
	"\n WHERE c.encounter_nr='$encounter_nr'";

	echo "sql= ".$sql;
	$result_icp=NULL;
	if($t1=$db->Execute($sql)){	
		if($rows=$t1->RecordCount()){
			$result_icp['therapy']='';
			while($t2=$t1->FetchRow()){
				$result_icp['therapy'].= $t2['code']." : ".$t2['therapy']."\n";
			}
		}else{
			//echo "$LDDbNoRead<p>$sql";
		}
	}else{
		//echo $sql;
	}
	
	

	//for result
	$sql ="SELECT r.result_desc as description FROM seg_encounter_result e LEFT JOIN seg_results r ON ".
			"\n e.result_code = r.result_code WHERE e.encounter_nr='$encounter_nr' ".
			"\n AND r.area_used='A'";
	//echo "<br><br>";
	//echo "result -sql->".$sql;
	$rResult=NULL;
	if($r=$db->Execute($sql)){
		if($rows=$r->RecordCount()){
			//print_r($r);
			$rResult['description']='';
			while($h=$r->FetchRow()){
				$rResult['description'].="- ". $h['description']."<br> \n";
			}
		}else{
			//echo "$LDDbNoRead<p>$sql<br>";
		}
	}else{
		//echo "<br>".$sql;
	}

	
	$sql = "SELECT d.disp_desc as descrip FROM seg_encounter_disposition e LEFT JOIN seg_dispositions d ON ".
			"\n e.disp_code = d.disp_code WHERE e.encounter_nr='$encounter_nr' ".
			"\n AND d.area_used='A'";
	//echo "<br><br>";
	//echo "disposition -sql->".$sql;
	$rDisp=NULL;
	if($d=$db->Execute($sql)){
		if($rows=$d->RecordCount()){
			$rDisp['descrip']='';
			while($s=$d->FetchRow()){
				$rDisp['descrip'].="- ". $s['descrip']."<br> \n";
			}
		}else{
			//echo "$LDDbNoRead<p>$sql<br>";
		}
	}else{
	//	echo "<br>".$sql;
	}
	
//	echo "<br><br>";
//	echo "result -sql->".$sql;
//	echo "<br><br>";
//	print_r($rDisp);
	
	
	echo "<br><br>(details) rows = $rows <br>result_icp in show_medocs =";
//	print_r($result_icp);
//	echo "<br><br> \n";

}


$subtitle=$LDMedocs;
	
$buffer=str_replace('~tag~',$title.' '.$name_last,$LDNoRecordFor);
$norecordyet=str_replace('~obj~',strtolower($subtitle),$buffer); 
$HTTP_SESSION_VARS['sess_file_return']=$thisfile;

# Set break file
require('include/inc_breakfile.php');
if($mode=='show') $glob_obj->getConfig('medocs_%');

/* Load GUI page */
require('./gui_bridge/default/gui_show_medocs.php');

?>