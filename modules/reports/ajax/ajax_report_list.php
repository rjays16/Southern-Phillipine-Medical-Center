<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require "{$root_path}include/inc_environment_global.php";
require "{$root_path}classes/json/json.php";
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_repgen.php');
$repgen_obj=new RepGen;
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj = new Department;

global $db;

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json; charset=ISO-8859-1");
define("SPECIAL_FUNCTION", '214');
define("IPBM_DEPT", '182');
define("OPD_DEPT", '150');
define("PHS_DEPT", 133);
define("BB_DEPT", 190);
define(OB,209);
define(RAD,158);


$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$rep_category = $_REQUEST['rep_category'];
$search = $_REQUEST['search'];
$dept_nr = $_REQUEST['dept_nr'];
$from_doctordashboard = $_GET['from'];

#add rnel

$area = $_REQUEST['area'];
#end rnel


#echo "cat, search, dept_nr = ".$rep_category." == ".$search." == ".$dept_nr;

$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'rep_group' => 'rep_group',
    'rep_name' => 'rep_name',
);

$sortName = $_REQUEST['sort'];
#echo "<br><br>sortn = ".$sortName."<br><br>";

if (!$sortName || !array_key_exists($sortName, $sortMap))
	$sortName = 'rep_group';

$filters = array(
	'sort' => $sortMap[$sortName]." ".$sortDir
);

$data = array();


if(is_array($filters))
{
	foreach ($filters as $i=>$v) {
		switch (strtolower($i)) {
			case 'sort': $sort_sql = $v; break;
		}
	}
}

		//added by Mary ~ June 2, 2016
	 	$session = $_SESSION['sess_login_personell_nr'];
	 	$strSQL = "select permission,login_id from care_users WHERE personell_nr=".$db->qstr($session);

		$permission = array();
		$login_id = "";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()){
                	$permission[] = $row['permission'];
                	$login_id = $row['login_id'];
                }
            }
        }

        require_once($root_path . 'include/care_api_classes/class_acl.php');
		$objAcl = new Acl($login_id);

		#for doctor report luncher rnel
		$admin_access = $objAcl->checkPermissionRaw('_a_0_all');
		$all_doctor_report = $objAcl->checkPermissionRaw('_a_1_doctorsreportlauncher');
		$admission_log_book = $objAcl->checkPermissionRaw('_a_2_Admission_Logbook_For_Docs');
		$ehr_monitoring = $objAcl->checkPermissionRaw('_a_2_EHR_User_Log_Monitoring');

		$book_registry_report = $objAcl->checkPermissionRaw('_a_2_MR_Book_Report');
		$medrec_report_launcher_only = ($objAcl->checkPermissionRaw('_a_1_medocs_report_launcher') && !$book_registry_report);

		// $refferal_monitoring_sheet = $objAcl->checkPermissionRaw('_a_2_Referral_Monitoring_Sheet');
		$emergency_daily_transaction_doctor = $objAcl->checkPermissionRaw('_a_2_ER_Daily_Transactions_for_docs');
		$pediatrics_report = $objAcl->checkPermissionRaw('_a_2_MR_Pediatrics_Reports');
		#end rnel

		$parent_doctor_report_launcher = ($all_doctor_report && !($ehr_monitoring || $admission_log_book /*|| $refferal_monitoring_sheet*/ || $emergency_daily_transaction_doctor || $pediatrics_report));

        $all_reports = "SELECT SQL_CALC_FOUND_ROWS r.* FROM seg_rep_templates_registry r
			 			 		WHERE is_active=1 AND rep_dept_nr=".$db->qstr($dept_nr) ;

        //end ~ mary

#edited by VAS 11/27/2017
if ($dept_nr!=''){

	if ($from_doctordashboard != 'doctor') {
		$cond_dept = "AND (rep_dept_nr = ".$db->qstr($dept_nr)."
		  			OR d.dept_nr = ".$db->qstr($dept_nr).")";
	}else{
		$cond_dept = "AND (rep_dept_nr = ".$db->qstr($dept_nr)." OR rep_dept_nr ='0'
		  			OR d.dept_nr = ".$db->qstr($dept_nr)." OR d.dept_nr ='0')";
	} 
    
}		 

$all_reports = "SELECT DISTINCT
		  SQL_CALC_FOUND_ROWS r.*,d.dept_nr
		FROM
		  seg_rep_templates_registry r 
		LEFT JOIN seg_rep_templates_dept d ON d.report_id=r.report_id  
		WHERE is_active = 1 ".$cond_dept;

 
//modified by Nick 3-27-2015
require_once($root_path . 'include/care_api_classes/class_personell.php');
require_once($root_path . '/frontend/bootstrap.php');

$personnel = new Personell;

$arrayCheck=array();

$sessionUserObj = $personnel->get_Dept_name($_SESSION['sess_login_personell_nr']);
$sessionUserNr = $sessionUserObj['location_nr'];

#added and modify rnel
#

$_a_3_sassgin_dept_only = $objAcl->checkPermissionRaw('_a_3_sassgin_dept_only');
if(($personnel->isDoctor($_SESSION['sess_login_personell_nr']) && $from_doctordashboard=='doctor') || !$personnel->isDoctor($_SESSION['sess_login_personell_nr']) && $area == 'doctor'){

	if($admin_access || $all_doctor_report || $ehr_monitoring || $admission_log_book || $emergency_daily_transaction_doctor || $pediatrics_report) {
	$personnelAssignment = $personnel->get_Dept_name($_SESSION['sess_login_personell_nr']);

		$sql_doctor = "SELECT SQL_CALC_FOUND_ROWS r.*,r.rep_dept_nr
			FROM seg_rep_templates_registry r
			INNER JOIN seg_rep_templates_clinic AS c ON c.report_id = r.report_id
		 	WHERE is_active=1 AND c.dept_nr=0 OR r.rep_dept_nr=".$db->qstr($personnelAssignment['location_nr']);
		$temp_total = 0;

		if($parent_doctor_report_launcher || $ehr_monitoring || $admission_log_book || /*$refferal_monitoring_sheet ||*/ $emergency_daily_transaction_doctor || $pediatrics_report) {

			$has_permission_on_report = '';

			if($parent_doctor_report_launcher || $admin_access){
				$has_permission_on_report = " _a_2_Admission_Logbook_For_Docs _a_2_EHR_User_Log_Monitoring _a_2_Referral_Monitoring_Sheet _a_2_ER_Daily_Transactions_for_docs _a_2_MR_Pediatrics_Reports ";
			}

			foreach($permission as $permitted) {

				$has_permission_on_report .= $permitted;
				$permit = explode(' ', $has_permission_on_report);
				$extractList = '';
				foreach($permit as $value) {
					$access = substr((string) $value, 5);
					$extractList .= "'". $access . "',";
					if ($access == 'all') {
						$sql = $sql_doctor;
						break;
					}

					if($admin_access){
						$sql = "SELECT SQL_CALC_FOUND_ROWS r.*
							FROM seg_rep_templates_registry r
							INNER JOIN seg_rep_templates_clinic AS c ON c.report_id = r.report_id
							WHERE is_active=1 AND c.dept_nr=0 OR c.dept_nr=119";

							break;
					}

					
					
					$sql = "SELECT SQL_CALC_FOUND_ROWS r.*
							FROM seg_rep_templates_registry r
							INNER JOIN seg_rep_templates_clinic AS c ON c.report_id = r.report_id
							WHERE is_active=1 AND r.report_id=".$db->qstr($access)." AND (r.rep_dept_nr=0 OR c.dept_nr=0 OR c.dept_nr=".$db->qstr($personnelAssignment['location_nr'])." OR r.rep_dept_nr=".$db->qstr($personnelAssignment['location_nr']).")";

					if($rep_category) {
					    $sql.=" AND rep_category = ".$db->qstr($rep_category);
					}

					
 //var_dump($sql);
					// echo $access . '<br>'; 

					if ($result = $db->Execute($sql)) {
						if ($result->RecordCount()) {

							while ($row = $result->FetchRow()) {
							if(in_array($row['report_id'], $arrayCheck) == FALSE){
								array_push($arrayCheck, $row['report_id']);
							}
							else{
								break;
							}

							$sTemp = '';
							//var_dump($row['report_id']);
							$param_ids=&$repgen_obj->getReportParamById($row['report_id']);
							$inDeptList = false;
							if(is_object($param_ids)){

								while($row_param_ids=$param_ids->FetchRow()) {

									$parameter=&$repgen_obj->getReportParameter2($row_param_ids['param_id']);
									if(is_object($parameter)){
										while($row_param=$parameter->FetchRow()) {
											
											$sTemp = $sTemp.'<span id="'.$row_param['param_id'].'_1"><b>'.$row_param['parameter'].'</b></span>';
											switch ($row_param['param_type']){
												case 'option' :  
																$option_arr = explode(",", $row_param['choices']);
																if($row_param["param_id"] == "OPDdeptt"){
									                            	$row_param['parameter'] = "All";
									                            }else{
									                            	$row_param['parameter'] = "Select " . $row_param['parameter'];
									                            }
																$options="<option value=''>-Select ".$row_param['parameter']."-</option>";
																if (count($option_arr)){
																	$inChildDeptList = true;
																	while (list($key,$val) = each($option_arr))  {
																		$val = substr(trim($val),0,strlen(trim($val))-1);
																		$val = substr(trim($val),1);
																		$val_arr = explode("-", $val);

																		if($row_param["param_id"] == "OPDdeptt" && $dept_nr == "150"){
																			
											                                while (list($key,$val) = each($option_arr))  {
											                                    $val = substr(trim($val),0,strlen(trim($val))-1);
											                                    $val = substr(trim($val),1);
											                                    $val_arr = explode("-", $val);

											                                    if($sessionUserNr == $val_arr[0])
											                                    	$inDeptList = true;

																				if($_a_3_sassgin_dept_only && !$admin_access){
																					if($pDeptRes = $dept_obj->searchParentDept($sessionUserNr)){
																						
																						if($inChildDeptList){
																							$inChildDeptList = false;
																							$options.='<option value="'.$pDeptRes["id"].'">'.$pDeptRes["name"].'</option>';
																							$deptChildList = $dept_obj->getChildDeptList($sessionUserNr);
																							if($deptChildList){
																								while ($cdeptRes = $deptChildList->FetchRow()) {
																									$options.='<option value="'.$cdeptRes["id"].'">'.$cdeptRes["name"].'</option>';
																								}
																							}
																						}
																						
																					}else{
																						if($val_arr[0] == $sessionUserNr){
																							$options.='<option value="'.$val_arr[0].'">'.$val_arr[1].'</option>';
																						}
																					}
																		 		}else{
																		 			$options.='<option value="'.$val_arr[0].'">'.$val_arr[1].'</option>';
																		 		}	
																				
											                                }
											                               
																		}else{
																			$options.='<option value="'.$val_arr[0].'">'.$val_arr[1].'</option>';
																		}
																		
																	}
																}
																if($row_param['param_id']=="billing_insurance"){ $param = '<br/><span id="'.$row_param['param_id'].'"><select onchange="billing_insurance_control()" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" style="width: 300px" class="segInput">'.$options.'</select></span>';}
																else{ $param = '<br/><span id="'.$row_param['param_id'].'"><select name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" style="width: 300px" class="segInput">'.$options.'</select></span>';}
																break;
												case 'time' :    
																$jav =  '<script type="text/javascript">
																			jQuery(function($){
																				$J("#'.$row['rep_script'].'_param_'.$row_param['param_id'].'_from").mask("99:99");
																			});
																			jQuery(function($){
																				$J("#'.$row['rep_script'].'_param_'.$row_param['param_id'].'_to").mask("99:99");
																			});
																		</script>';
																$param = $jav.'<span id="'.$row_param['param_id'].'">
																					<input class="segInput" maxlength="5" size="2" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_from" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_from" type="text" value="">
																					<select class="segInput" name = "'.$row['rep_script'].'_param_'.$row_param['param_id'].'_meridian_from" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_meridian_from">
																						<option value = "AM">AM</option>
																						<option value = "PM">PM</option>
																					</select>
																					To
																					<input class="segInput" maxlength="5" size="2" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_to" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_to" type="text" value="">
																					<select class="segInput" name = "'.$row['rep_script'].'_param_'.$row_param['param_id'].'_meridian_to" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_meridian_to">
																						<option value = "AM">AM</option>
																						<option value = "PM">PM</option>
																					</select>
																				</span>';
																break; 
												case 'date' :    
																$param = '<span id="'.$row_param['param_id'].'"><input class="segInput" maxlength="10" size="8" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="text" value=""></span>';
																break; 
												
												case 'boolean' : 
																$param = '<span id="'.$row_param['param_id'].'"><input class="segInput" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="checkbox" value="1"></span>';
																break;
												case 'radio' :   
																$param = '<span id="'.$row_param['param_id'].'"><input class="segInput" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="radio" value="1"></span>';;
																break;                                                                     
												case 'sql' :     
																$option_sql=$db->Execute($row_param['choices']);
																$options="<option value=''>-Select a ".$row_param['parameter']."-</option>";
																if (is_object($option_sql)){
																	while ($row_option=$option_sql->FetchRow()) {
																		$options.='<option value="'.$row_option['id'].'">'.$row_option['id'].'-'.$row_option['namedesc'].'</option>';
																	}
																}
																
																$param = '<br/><span id="'.$row_param['param_id'].'"><select name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" style="width: 300px" class="segInput"></span><br/> 
																			'.$options.'</select>';
																break;
												case 'text' :   

																$param = '<span id="'.$row_param['param_id'].'"><br/>Search by code&nbsp<input name="'.$row['rep_script'].'_paramCheck_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_paramCheck_'.$row_param['param_id'].'" type="checkbox" value="">
																			<br/>
																			<input class="segInput" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="hidden" style="width: 300px" value="">
																			<input class="segInput" name="'.$row['rep_script'].'_param1_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param1_'.$row_param['param_id'].'" type="text" style="width: 300px" value="">
																			</span>';
																break;
												case 'autocomplete' :
																	
																$param = '<br/><span id="'.$row_param['param_id'].'">
																			<input class="segInput" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="hidden" style="width: 300px" value="">
																			<input class="segInput" name="'.$row['rep_script'].'_param1_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param1_'.$row_param['param_id'].'" type="text" onblur="clearNr(this.id);" style="width: 300px" value="">
																			</span>';
																break;                 
												case 'checkbox' :     
																$option_sql=$db->Execute($row_param['choices']);
																$options="";
																if (is_object($option_sql)){
																	while ($row_option=$option_sql->FetchRow()) {
																		$options.='<input class="segInput" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_id_'.$row_option['id'].'" type="checkbox" value="'.$row_option['id'].'"/>'.$row_option['namedesc'].'<br/>';
																		// $options.='<option value="'.$row_option['id'].'">'.$row_option['id'].'-'.$row_option['namedesc'].'</option>';
																	}
																		if($row_param['param_id']=='mem_category'){
																         $options.= '<button id="segInput" cursor: pointer;" onclick="getICPICDP();" title="Load">
														                        	 Load
														                    	   </button>';
														                    	   }
																}
																
																$param = '<br/><span id="'.$row_param['param_id'].'">'.$options.'</span><br/>';
																break;
																// $param = '<span id="'.$row_param['param_id'].'"><input name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="checkbox" value=""></span>';
																// break;
																
												case 'textbox' :    
																$param = '<br/><span id="'.$row_param['param_id'].'">
																			<input class="segInput" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="text" style="width: 300px" value="">
																			</span>';
																break;
																																														
												default :        break;                   
											}
											
											$sTemp = $sTemp.$param.'<br/>';
											
										}
									}	
								}
							}

 		                             		if(!$row['with_template'] && $row['dept_nr'] == $dept_nr){
									        	$row['with_template'] = 1;
									        }
											// if($_a_3_sassgin_dept_only && !$admin_access && $inDeptList && $row["report_id"] == "Outpatient_Dept_Daily_Transaction")	 		                       
												// continue;
									        	if (!$search) {
									        		$data[] = array(
														'report_id' => trim($row['report_id']),
														'rep_script' => trim($row['rep_script']),
														'rep_group' => trim($row['rep_group']),
														'rep_name' => trim($row['rep_name']),
														'with_template' => trim($row['with_template']),
														'query_in_jasper' => trim($row['query_in_jasper']),
														'parameter' => $sTemp,
														'is_have_param' => ($sTemp!=null) ? 1 : 0,
													);
									        	}
													
											
									$temp_total++;
									
									
								} #end while.
							}

						}

					


				}
				if($sql != $sql_doctor && $search){
				  	if ($dept_nr!=''){
						$cond_dept = " AND (r.rep_dept_nr = ".$db->qstr($dept_nr)."
	  						OR c.dept_nr = ".$db->qstr($dept_nr)." OR r.rep_dept_nr = '0' OR c.dept_nr = '0')";
					}

					$cond_rep = "";
					if($extractList){
						$cond_rep = " AND r.report_id IN (".rtrim($extractList,",") . ")";
					}

					$sql = "SELECT SQL_CALC_FOUND_ROWS r.*
					FROM seg_rep_templates_registry r
					INNER JOIN seg_rep_templates_clinic AS c ON c.report_id = r.report_id
					WHERE is_active=1 $cond_dept $cond_rep";
					
				}

			}
		}
//die();
	} #end rnel.


}else{
		// $ipbm_admission_logbook_permission = "_a_2_PSY_Admission_Logbook_For_Docs";
		$outpatient_dept_daily_transaction = "_a_2_sOutpatient_Dept_Daily_Transaction";
		$admission_logbook_rep_name = 'Admission Logbook For Doctors';
		$icd_encoded_name = 'Number of Health Records Encoded';
		$ave_daily_census_admitted_name = 'Average Daily Census of Admitted Patients';
		$ipd_demog_name ="Inpatient's Demographic Data";
		// Added and Modified by Mary ~ June 2, 2016
		// Limit access permission in billing reports
		$dept = Config::get('limit_access_permission_billing');
		// Added by Matsuu 12012017 : Limit access for IPBM Reports
		$ipbm = Config::get('limit_access_permission_ipbm');
		// var_dump($_REQUEST['dept_nr']); die();
 		$medocs = Config::get('limit_access_permission_medocs');
 		// added by devon
 		$blood_bank = Config::get('limit_access_permission_bb');
        $himd = Config::get('limit_access_permission_himd');

        $obaccess = Config::get('limit_access_permission_obgyne');

		if ($dept_nr == $dept->value || $dept_nr == SPECIAL_FUNCTION ||  $dept_nr == $ipbm->value  || $dept_nr == $medocs->value || $dept_nr == $blood_bank->value || ($dept_nr == $himd->value && (!$medrec_report_launcher_only))  || $dept_nr == $obaccess->value) {
			require_once($root_path . 'include/care_api_classes/class_acl.php');
			$objAcl = new Acl($login_id);
			$user_to_all_reports = $objAcl->checkPermissionRaw('_a_1_billreports');

			$bill_overall_sum_rendered = $objAcl->checkPermissionRaw('_a_2_Billing_Overall_Summary_Rendered');
			$bill_acr = $objAcl->checkPermissionRaw('_a_2_Billing_ACR');
			$bill_admin_log = $objAcl->checkPermissionRaw('_a_2_Billing_Admission_Logbook');
			$bill_cf1_summary = $objAcl->checkPermissionRaw('_a_2_Billing_Received_CF1_Summary_Report');
			$bill_rendered = $objAcl->checkPermissionRaw('_a_2_Billing_Bills_Rendered');
			$bill_RVS_ICD = $objAcl->checkPermissionRaw('_a_2_Billing_Top15_RVS_ICD');
			$bill_PHIC_claims = $objAcl->checkPermissionRaw('_a_2_Billing_PHIC_Claims_Transmitted');
            $bill_PHIC_claims_category = $objAcl->checkPermissionRaw('_a_2_Billing_Transmittal_Based_On_PHIC_Category');
			$bill_grants_monthly = $objAcl->checkPermissionRaw('_a_2_Billing_Grants_Monthly');
			$bill_detailed_sum = $objAcl->checkPermissionRaw('_a_2_Billing_Detailed_Summary_Bills_Rendered');
			$bill_mmhr = $objAcl->checkPermissionRaw('_a_2_Billing_MMHR');
			$bill_transc_soa = $objAcl->checkPermissionRaw('_a_2_Billing_Transaction_Billed_SOA');
			$bill_Meds_XLO = $objAcl->checkPermissionRaw('_a_2_Transact_Meds_XLO');
			$bill_temp_PHIC = $objAcl->checkPermissionRaw('_a_2_Billing_Transaction_Temp_PHIC');
			$bill_payward_set = $objAcl->checkPermissionRaw('_a_2_Billing_Transaction_Payward_Settlement');
			$bill_tentative_rebill = $objAcl->checkPermissionRaw('_a_2_Transact_Tentative_Rebill');
			$bill_transmittal_history = $objAcl->checkPermissionRaw('_a_2_Billing_Transmittal_History');
			$bill_phic_accreditation = $objAcl->checkPermissionRaw('_a_2_Billing_PHIC_Accreditation');
			$bill_HSM = $objAcl->checkPermissionRaw('_a_2_Billing_HSM');
			$_a_2_Employee_Audit_Trail_Deactivated = $objAcl->checkPermissionRaw('_a_2_Employee_Audit_Trail_Deactivated');
			$_a_2_Employee_Audit_Trail_Modified = $objAcl->checkPermissionRaw('_a_2_Employee_Audit_Trail_Modified');
			$_a_2_Employee_Audit_Trail = $objAcl->checkPermissionRaw('_a_2_Employee_Audit_Trail');
			#Added by Matsuu  02142018
			//IPBM Reports
			$all_report_ipbm = $objAcl->checkPermissionRaw('_a_0_all');
			$_a_1_ipbm_report_launcher = $objAcl->checkPermissionRaw('_a_1_ipbm_report_launcher');
			$_a_2_PSY_Admission_Logbook_For_Docs = $objAcl->checkPermissionRaw('_a_2_PSY_Admission_Logbook_For_Docs');
			$_a_2_PSY_Causes_Confinement = $objAcl->checkPermissionRaw('_a_2_causes_confinement');
			$_a_2_PSY_OPD_daily_trans = $objAcl->checkPermissionRaw('_a_2_PSY_OPD_daily_trans');
			$_a_2_opd_summary = $objAcl->checkPermissionRaw('_a_2_opd_summary');
			$_a_2_report_discharges =$objAcl->checkPermissionRaw('_a_2_report_discharges');
			$_a_2_report_referral = $objAcl->checkPermissionRaw('_a_2_report_referral');
			$_a_2_report_icd_encoded = $objAcl->checkPermissionRaw('_a_2_report_icd_encoded');
			$_a_2_death= $objAcl->checkPermissionRaw('_a_2_death');
			$_a_2_top_10 = $objAcl->checkPermissionRaw('_a_2_top_10');
			$_a_2_ave_daily_census_admitted=$objAcl->checkPermissionRaw('_a_2_ave_daily_census_admitted');
			$_a_2_PSY_Research_Query=$objAcl->checkPermissionRaw('_a_2_PSY_Research_Query');
			$_a_2_psy_opd_rendered=$objAcl->checkPermissionRaw('_a_2_psy_opd_rendered');
            $_a_2_smoking = $objAcl->checkPermissionRaw('_a_2_smoking');
            $_a_2_leading_discharges= $objAcl->checkPermissionRaw('_a_2_leading_discharges');
            $_a_2_Discharges_7days_Admission= $objAcl->checkPermissionRaw('_a_2_Discharges_7days_Admission');
			$_a_2_discharge_treatment = $objAcl->checkPermissionRaw('_a_2_discharge_treatment');
            $_a_2_leading_morbidity_oveall = $objAcl->checkPermissionRaw('_a_2_leading_morbidity_oveall');
            $_a_2_discharges_served = $objAcl->checkPermissionRaw('_a_2_discharges_served');
            $_a_2_PSY_Unregistered_Death_Certificate = $objAcl->checkPermissionRaw('_a_2_PSY_Unregistered_Death_Certificate');
            $_a_2_icd_encoded_stat = $objAcl->checkPermissionRaw('_a_2_icd_encoded_stat');
            $_a_2_ipd_demog = $objAcl->checkPermissionRaw('_a_2_ipd_demog');
        	$_a_2_PSY_leading_mortality = $objAcl->checkPermissionRaw('_a_2_PSY_leading_mortality');
            $_a_2_summary_patient = $objAcl->checkPermissionRaw('_a_2_summary_patient');
        	$_a_2_TOP15_ICP_ICD_Detailed =$objAcl->checkPermissionRaw('_a_2_TOP15_ICP_ICD_Detailed');
            $_a_2_causes_mortality = $objAcl->checkPermissionRaw('_a_2_causes_mortality');
            $_a_2_notifiable = $objAcl->checkPermissionRaw('_a_2_notifiable');
            $_a_2_PSY_bor = $objAcl->checkPermissionRaw('_a_2_PSY_bor');

#Ended here..

			#OPD
			$_a_1_opd_report_launcher = $objAcl->checkPermissionRaw('_a_1_opd_report_launcher');
			$_a_2_sdental_procedure = $objAcl->checkPermissionRaw('_a_2_sdental_procedure');
			$_a_2_sOPD_Animal_Bite = $objAcl->checkPermissionRaw('_a_2_sOPD_Animal_Bite');
			$_a_2_sOPD_Patients_Census = $objAcl->checkPermissionRaw('_a_2_sOPD_Patients_Census');
			$_a_2_sopd_daily_trans = $objAcl->checkPermissionRaw('_a_2_sopd_daily_trans');
			$_a_2_sOutpatient_Dept_Daily_Transaction = $objAcl->checkPermissionRaw('_a_2_sOutpatient_Dept_Daily_Transaction');
			$_a_2_sopd_preventive_care_center_daily_transaction = $objAcl->checkPermissionRaw('_a_2_sopd_preventive_care_center_daily_transaction');
			$_a_2_sRecord_Availment_Form = $objAcl->checkPermissionRaw('_a_2_sRecord_Availment_Form');
			$_a_2_sSenior_Citizen_Utilization = $objAcl->checkPermissionRaw('_a_2_sSenior_Citizen_Utilization');
			

			#Blood Bank devon 
			$_a_1_blood_report_launcher = $objAcl->checkPermissionRaw('_a_1_blood_report_launcher');
			$_a_2_bb_revised_utilization_ward_btype = $objAcl->checkPermissionRaw('_a_2_bb_revised_utilization_ward_btype');
			$_a_2_bb_daily_transac_monitoring = $objAcl->checkPermissionRaw('_a_2_bb_daily_transac_monitoring');
			$_a_2_bb_income_rep = $objAcl->checkPermissionRaw('_a_2_bb_income_rep');
			$_a_2_bb_redcell_stat_summary = $objAcl->checkPermissionRaw('_a_2_bb_redcell_stat_summary');
			$_a_2_bb_turn_around_time = $objAcl->checkPermissionRaw('_a_2_bb_turn_around_time');
			$_a_2_bb_stat_used_rep = $objAcl->checkPermissionRaw('_a_2_bb_stat_used_rep');
			$_a_2_bb_stat_unit_payment = $objAcl->checkPermissionRaw('_a_2_bb_stat_unit_payment');
			$_a_2_bb_stat_unit_rep = $objAcl->checkPermissionRaw('_a_2_bb_stat_unit_rep');
			$_a_2_bb_stat_unit_rep_daily = $objAcl->checkPermissionRaw('_a_2_bb_stat_unit_rep_daily');
			$_a_2_bb_stat_agesex_services = $objAcl->checkPermissionRaw('_a_2_bb_stat_agesex_services');
			$_a_2_bb_utilization_component_source_ward = $objAcl->checkPermissionRaw('_a_2_bb_utilization_component_source_ward');
			$_a_2_bb_daily_summary_report_con = $objAcl->checkPermissionRaw('_a_2_bb_daily_summary_report_con');
			$_a_2_bb_daily_summary_report = $objAcl->checkPermissionRaw('_a_2_bb_daily_summary_report');
			$_a_2_bb_grand_total_source_type_compo = $objAcl->checkPermissionRaw('_a_2_bb_grand_total_source_type_compo');
			$_a_2_bb_processing_report = $objAcl->checkPermissionRaw('_a_2_bb_processing_report');
			$_a_2_BB_Stat = $objAcl->checkPermissionRaw('_a_2_BB_Stat');
			$_a_2_bb_stat_agesex_products = $objAcl->checkPermissionRaw('_a_2_bb_stat_agesex_products');
			$_a_2_bb_daily_report_deposited = $objAcl->checkPermissionRaw('_a_2_bb_daily_report_deposited');
			#OBGYNE -Matsuu
			$_a_1_obgye_report_launcher = $objAcl->checkPermissionRaw('_a_1_OBGyne_report_launcher');
			$_a_2_OBGyne_professional_fee_report = $objAcl->checkPermissionRaw('_a_2_OBGyne_professional_fee_report');

			$GYNEReportLauncherParentOnly = ($_a_1_obgye_report_launcher && !($_a_2_OBGyne_professional_fee_report));

	$IPBMReportLauncherParentOnly = ($_a_1_ipbm_report_launcher && !($_a_2_opd_summary || $_a_2_PSY_OPD_daily_trans || $_a_2_PSY_Admission_Logbook_For_Docs || $_a_2_report_discharges || $_a_2_PSY_Causes_Confinement || $_a_2_report_icd_encoded|| $_a_2_death || $_a_2_top_10 || $_a_2_report_referral || $_a_2_ave_daily_census_admitted || $_a_2_PSY_Research_Query || $_a_2_psy_opd_rendered || $_a_2_smoking || $_a_2_Discharges_7days_Admission || $_a_2_discharge_treatment|| $_a_2_leading_morbidity_oveall || $_a_2_discharges_served || $_a_2_PSY_Unregistered_Death_Certificate || $_a_2_icd_encoded_stat || $_a_2_ipd_demog || $_a_2_PSY_leading_mortality || $_a_2_summary_patient || $_a_2_causes_mortality || $_a_2_notifiable || $_a_2_PSY_bor));



	$OPDReportLauncherParentOnly = ($_a_1_opd_report_launcher && !($_a_2_sdental_procedure ||
																		 $_a_2_sOPD_Animal_Bite ||
																		 $_a_2_sOPD_Patients_Census ||
																		 $_a_2_sopd_daily_trans ||
																		 $_a_2_sOutpatient_Dept_Daily_Transaction ||
																		 $_a_2_sopd_preventive_care_center_daily_transaction ||
																		 $_a_2_sRecord_Availment_Form ||
																		 $_a_2_sSenior_Citizen_Utilization ||
																		 $_a_3_sassgin_dept_only
																	)
									);
		#BloodBank
	$BBReportLauncherParentOnly  = ($_a_1_blood_report_launcher && !($_a_2_bb_revised_utilization_ward_btype ||
																		$_a_2_bb_income_rep ||
																		$_a_2_bb_redcell_stat_summary ||
																		$_a_2_bb_turn_around_time ||
																		$_a_2_bb_stat_used_rep ||
																		$_a_2_bb_stat_unit_payment ||
																		$_a_2_bb_stat_unit_rep ||
																		$_a_2_bb_stat_unit_rep_daily ||
																		$_a_2_bb_stat_agesex_services ||
																		$_a_2_bb_utilization_component_source_ward ||
																		$_a_2_bb_daily_summary_report_con ||
																		$_a_2_bb_daily_summary_report ||
																		$_a_2_bb_grand_total_source_type_compo ||
																		$_a_2_bb_processing_report ||
																		$_a_2_BB_Stat ||
																		$_a_2_bb_stat_agesex_products ||
																		$_a_2_bb_daily_report_deposited ||
																		$_a_2_bb_daily_transac_monitoring
																	)
									);
			$temp_total = 0;

			#add bloodbank
			if ($_a_2_Employee_Audit_Trail ||$_a_2_Employee_Audit_Trail_Deactivated ||$_a_2_Employee_Audit_Trail_Modified||$bill_overall_sum_rendered || $bill_acr || $bill_admin_log  || $bill_rendered || $bill_RVS_ICD || $bill_grants_monthly || $bill_detailed_sum || $bill_mmhr || $bill_transc_soa || $bill_Meds_XLO || $bill_temp_PHIC || $bill_payward_set || $bill_tentative_rebill || $bill_transmittal_history|| $bill_PHIC_claims || $bill_PHIC_claims_category || $bill_HSM || $bill_cf1_summary || $_a_1_ipbm_report_launcher|| $_a_2_PSY_OPD_daily_trans || $_a_2_PSY_Admission_Logbook_For_Docs|| $_a_2_opd_summary|| $_a_2_report_discharges|| $_a_2_PSY_Causes_Confinement || $_a_2_report_referral || $_a_2_report_icd_encoded || $_a_2_death || $_a_2_top_10 || $_a_2_ave_daily_census_admitted || $_a_2_smoking ||
				 $_a_2_PSY_Research_Query ||
				 $_a_1_opd_report_launcher || 
				 $_a_2_sdental_procedure ||
				 $_a_2_sOPD_Animal_Bite ||
                $_a_2_sOPD_Patients_Census ||
                $_a_2_sopd_daily_trans ||
                $_a_2_sOutpatient_Dept_Daily_Transaction ||
                $_a_2_sopd_preventive_care_center_daily_transaction ||
                $_a_2_sRecord_Availment_Form ||
                $_a_2_sSenior_Citizen_Utilization ||
                $_a_3_sassgin_dept_only ||
                $_a_2_psy_opd_rendered ||
                $_a_2_Discharges_7days_Admission ||
                $_a_2_leading_discharges ||
                $_a_2_leading_morbidity_oveall ||
                $_a_2_discharge_treatment ||
                $_a_2_discharges_served ||
                $_a_2_PSY_Unregistered_Death_Certificate ||
                $_a_2_icd_encoded_stat ||
                $_a_2_ipd_demog ||
                $_a_2_PSY_leading_mortality ||
                $_a_1_blood_report_launcher ||
                $_a_2_bb_revised_utilization_ward_btype ||
                $_a_2_bb_income_rep ||
                $_a_2_bb_redcell_stat_summary ||
                $_a_2_bb_turn_around_time ||
                $_a_2_bb_stat_used_rep ||
                $_a_2_bb_stat_unit_payment ||
                $_a_2_bb_stat_unit_rep ||
                $_a_2_bb_stat_unit_rep_daily ||
                $_a_2_bb_stat_agesex_services ||
                $_a_2_bb_utilization_component_source_ward ||
                $_a_2_bb_daily_summary_report_con ||
                $_a_2_bb_daily_summary_report ||
                $_a_2_bb_grand_total_source_type_compo ||
                $_a_2_bb_processing_report ||
                $_a_2_BB_Stat ||
                $_a_2_bb_stat_agesex_products ||
                $_a_2_bb_daily_report_deposited ||
                $_a_2_bb_daily_transac_monitoring ||
                $_a_2_summary_patient ||
				 $_a_2_TOP15_ICP_ICD_Detailed || 
                                 $book_registry_report || 
                                 $_a_2_causes_mortality  || 
                 $_a_2_notifiable  ||
                $_a_2_PSY_bor ||
				 $_a_1_obgye_report_launcher ||
				 $_a_2_OBGyne_professional_fee_report
			
				) {
				
						$extractList = "";
					 	$fff = "";
					 	
		        foreach ($permission as $value) {
			         $fff.=$value;
			         
					 $exvalue = explode(" ", $fff);
					// var_dump($value);
					 foreach ($exvalue as $value) {
					 	
					 	# updated by carriane 02/14/18
					 	//g adjust nako ni kay naay bug sa saving permission
					 	
					 	// if($value == $ipbm_admission_logbook_permission)

					 	// 	$extracted_value = substr((string)$value, 6);
					 	// else{


					 		$opdReportLauncherItemList = array(
						 	  								"_a_2_sdental_procedure",
						 	  								"_a_2_sOPD_Animal_Bite",
						 	  								"_a_2_sOPD_Patients_Census",
						 	  								"_a_2_sopd_daily_trans",
						 	  								"_a_2_sOutpatient_Dept_Daily_Transaction",
						 	  								"_a_2_sopd_preventive_care_center_daily_transaction",
						 	  								"_a_2_sRecord_Availment_Form",
						 	  								"_a_2_sSenior_Citizen_Utilization",
						 	  								"_a_3_sassgin_dept_only"
						 	  							);

						 	  $is_opdReportLauncherItemList = in_array($value, $opdReportLauncherItemList);

						 	  if($is_opdReportLauncherItemList){
						
						 	  	
						 	  	if($value=="_a_3_sassgin_dept_only"){
						 	  		$value = !$_a_2_sOutpatient_Dept_Daily_Transaction ? "_a_2_sOutpatient_Dept_Daily_Transaction" : $value;
						 	  	}

						 	  	$extracted_value = substr((string)$value, 6);

						 	  }else{
						 	  	$extracted_value = substr((string)$value, 5);
						 	  }
						 	  // var_dump($extracted_value)
					 	// }
					 	
					 	  	if($dept_nr == $himd->value && (!$medrec_report_launcher_only)){
					 	  		$himd_reports_req_permission = array('_a_2_MR_Book_Report');

					 	  		if(in_array($value, $himd_reports_req_permission) || $value == $admin_access){
						 	  // var_dump($dept_nr);
					 	  			$extracted_value = substr((string)$value, 5);
					 	  		}else{
					 	  			$extracted_value = '';
					 	  		}
						 	}
					 	
					 	
						$extractList .= "'". $extracted_value . "',";

					 	// ended here..
						// var_dump($extracted_value);
						# updated by carriane 02/14/18
						// if ($extracted_value == "all"  || ($extracted_value=='ipbm_report_launcher' && $IPBMReportLauncherParentOnly) || ($extracted_value=='opd_report_launcher' && $OPDReportLauncherParentOnly)) {
						//   	$sql = $all_reports;
						// 	break;
						// }
						
						# updated by fritz 12/02/18
						# updated by devon
						if ($extracted_value == "all"  || ($extracted_value=='ipbm_report_launcher' && $IPBMReportLauncherParentOnly && $dept_nr == IPBM_DEPT) || ($extracted_value=='opd_report_launcher' && $OPDReportLauncherParentOnly && $dept_nr == OPD_DEPT) || ($extracted_value=='blood_report_launcher' && $BBReportLauncherParentOnly && $dept_nr ==BB_DEPT)  || ($extracted_value =='OBGyne_report_launcher' && $GYNEReportLauncherParentOnly && $dept_nr==OB)) {
						  	$sql = $all_reports;
							break;

						}

						if ($dept_nr!=''){
							$cond_dept = " AND (r.rep_dept_nr = ".$db->qstr($dept_nr)."
		  						OR d.dept_nr = ".$db->qstr($dept_nr).")";
						}

	
	$sql = "SELECT SQL_CALC_FOUND_ROWS r.*,d.dept_nr FROM seg_rep_templates_registry r LEFT JOIN seg_rep_templates_dept d ON d.report_id=r.report_id  
								 		WHERE r.is_active=1 ".$cond_dept . " AND r.report_id=".$db->qstr($extracted_value);
	if($rep_category) {
	    $sql.=" AND rep_category = ".$db->qstr($rep_category);
	}							 		
	// $sql = "SELECT SQL_CALC_FOUND_ROWS r.* FROM seg_rep_templates_registry r
	// 							 		WHERE is_active=1 AND rep_dept_nr=".$db->qstr($dept_nr)."AND report_id=".$db->qstr($extracted_value) ;
								
										 	if ($result = $db->Execute($sql)) {
								            if ($result->RecordCount()) {
								    //             if ($row = $result->FetchRow()){

								    //             	$data[] = array(
										  //           'report_id' => trim($row['report_id']),
										  //           'rep_script' => trim($row['rep_script']),
												// 	'rep_group' => trim($row['rep_group']),
												// 	'rep_name' => trim($row['rep_name']),
										  //           'with_template' => trim($row['with_template']),
										  //           'query_in_jasper' => trim($row['query_in_jasper']),
										  //           'parameter' => $sTemp,
										  //           'is_have_param' => ($sTemp!=null) ? 1 : 0,
												// );

								    //             	$temp_total++;

								    //             }
								while ($row = $result->FetchRow()) {
						 		$sTemp = '';
								// $total = $db->GetOne("SELECT FOUND_ROWS()");
								if($dept_nr == IPBM_DEPT){
									$param_ids=&$repgen_obj->getReportParamExistById($row['report_id']);
								}
								else{
									$param_ids=&$repgen_obj->getReportParamById($row['report_id']);
								}
						 		$inDeptList = false;
							 	if(is_object($param_ids)){
							 		while($row_param_ids=$param_ids->FetchRow()) {
							 			
							 			// var_dump($sessionUserNr);die;
							 			$parameter=&$repgen_obj->getReportParameter2($row_param_ids['param_id']);

								 		if(is_object($parameter)){
										    while($row_param=$parameter->FetchRow()) {
										    	
										        $sTemp = $sTemp.'<span id="'.$row_param['param_id'].'_1"><b>'.$row_param['parameter'].'</b></span>';
										        switch ($row_param['param_type']){
										           case 'option' :  

										                            $option_arr = explode(",", $row_param['choices']);
										                            if($row_param["param_id"] == "OPDdeptt"){
										                            	$row_param['parameter'] = "All";
										                            }else{
										                            	$row_param['parameter'] = "Select " . $row_param['parameter'];
										                            }

										                            $options="<option value=''>-".$row_param['parameter']."-</option>";
										                            if (count($option_arr)){
										                            	
										                            	
																		$inChildDeptList = true;
										                                while (list($key,$val) = each($option_arr))  {

										                                    $val = substr(trim($val),0,strlen(trim($val))-1);
										                                    $val = substr(trim($val),1);
										                                    $val_arr = explode("-", $val);

										                                    if($row_param["param_id"] == "OPDdeptt"){
										                                    	if($sessionUserNr == $val_arr[0])
											                                    	$inDeptList = true;
											                                    
																				if($_a_3_sassgin_dept_only && !$admin_access){

																					if($pDeptRes = $dept_obj->searchParentDept($sessionUserNr)){
																						
																						if($inChildDeptList){

																							$inChildDeptList = false;
																							$options.='<option value="'.$pDeptRes["id"].'">'.$pDeptRes["name"].'</option>';
																							$deptChildList = $dept_obj->getChildDeptList($sessionUserNr);
																							if($deptChildList){
																								while ($cdeptRes = $deptChildList->FetchRow()) {
																									$options.='<option value="'.$cdeptRes["id"].'">'.$cdeptRes["name"].'</option>';
																								}
																							}
																						}
																						
																					}else{
																						if($val_arr[0] == $sessionUserNr){
																							$options.='<option value="'.$val_arr[0].'">'.$val_arr[1].'</option>';
																						}
																					}
																		 		}else{
																		 			$options.='<option value="'.$val_arr[0].'">'.$val_arr[1].'</option>';
																		 		}	
										                                    }else{
										                                    	$options.='<option value="'.$val_arr[0].'">'.$val_arr[1].'</option>';
										                                    }
										                                    
																			
										                                }

										                                
										                            }
										                            if($row_param['param_id']=="billing_insurance"){ $param = '<br/><span id="'.$row_param['param_id'].'"><select onchange="billing_insurance_control()" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" style="width: 300px" class="segInput">'.$options.'</select></span>';}
										                            else{ $param = '<br/><span id="'.$row_param['param_id'].'"><select name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" style="width: 300px" class="segInput">'.$options.'</select></span>';}
										                            break;
										           case 'time' :    
										                            $jav =  '<script type="text/javascript">
										                                        jQuery(function($){
										                                            $J("#'.$row['rep_script'].'_param_'.$row_param['param_id'].'_from").mask("99:99");
										                                        });
										                                        jQuery(function($){
										                                            $J("#'.$row['rep_script'].'_param_'.$row_param['param_id'].'_to").mask("99:99");
										                                        });
										                                    </script>';
										                            $param = $jav.'<span id="'.$row_param['param_id'].'">
										                                                <input class="segInput" maxlength="5" size="2" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_from" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_from" type="text" value="">
										                                                <select class="segInput" name = "'.$row['rep_script'].'_param_'.$row_param['param_id'].'_meridian_from" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_meridian_from">
										                                                    <option value = "AM">AM</option>
										                                                    <option value = "PM">PM</option>
										                                                </select>
										                                                To
										                                                <input class="segInput" maxlength="5" size="2" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_to" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_to" type="text" value="">
										                                                <select class="segInput" name = "'.$row['rep_script'].'_param_'.$row_param['param_id'].'_meridian_to" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_meridian_to">
										                                                    <option value = "AM">AM</option>
										                                                    <option value = "PM">PM</option>
										                                                </select>
										                                           </span>';
										                            break; 
										           case 'date' :    
										                            $param = '<span id="'.$row_param['param_id'].'"><input class="segInput" maxlength="10" size="8" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="text" value=""></span>';
										                            break; 
										           
										           case 'boolean' : 
										                            $param = '<span id="'.$row_param['param_id'].'"><input class="segInput" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="checkbox" value="1"></span>';
										                            break;
										           case 'radio' :   
										                            $param = '<span id="'.$row_param['param_id'].'"><input class="segInput" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="radio" value="1"></span>';;
										                            break;                                                                     
										           case 'sql' :     
										           if ($row_param['param_id']=='billing_icd' || $row_param['param_id']=='billing_icp') {
										           		$fromDate =date('Y-m-d',strtotime($_GET['dateFROM']));
							           					$toDate = date('Y-m-d',strtotime($_GET['dateTO']));
							                           	$var = $row_param['choices'];
							                            $date = array("fromDate", "toDate");
														$changeDate   = array($fromDate,$toDate);
														$newphrase = str_replace($date, $changeDate,$var);
														$option_sql=$db->Execute($newphrase);
										           	}else{
										                            $option_sql=$db->Execute($row_param['choices']);
										                            }
										                            $options="<option value=''>-Select a ".$row_param['parameter']."-</option>";
										                            if (is_object($option_sql)){
										                                while ($row_option=$option_sql->FetchRow()) {
										                                	if ($row_param['param_id'] == "PSY_mr_encoder") {
										                                		$options.='<option value="'.$row_option['id'].'">'.$row_option['namedesc'].'</option>';
										                                	}else{
										                                    $options.='<option value="'.$row_option['id'].'">'.$row_option['id'].'-'.$row_option['namedesc'].'</option>';
										                                }
										                                    
										                                }
										                            }
										                            
										                            $param = '<br/><span id="'.$row_param['param_id'].'"><select name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" style="width: 300px" class="segInput"></span><br/> 
										                                     '.$options.'</select>';
										                            break;
										                                      /*$option_sql=$db->Execute($row_param['choices']);
										                            $options="<option value=''>-Select a ".$row_param['parameter']."-</option>";
										                            if (is_object($option_sql)){
										                                while ($row_option=$option_sql->FetchRow()) {
										                                    $options.='<option value="'.$row_option['id'].'">'.$row_option['id'].'-'.$row_option['namedesc'].'</option>';
										                                }
										                            }
										                            
										                            $param = '<br/><span id="'.$row_param['param_id'].'"><select name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" style="width: 300px" class="segInput"></span><br/> 
										                                     '.$options.'</select>';
										                            break;*/
										           case 'text' :   

										                            $param = '<span id="'.$row_param['param_id'].'"><br/>Search by code&nbsp<input name="'.$row['rep_script'].'_paramCheck_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_paramCheck_'.$row_param['param_id'].'" type="checkbox" value="">
										                                      <br/>
										                                        <input class="segInput" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="hidden" style="width: 300px" value="">
										                                        <input class="segInput" name="'.$row['rep_script'].'_param1_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param1_'.$row_param['param_id'].'" type="text" style="width: 300px" value="">
										                                      </span>';
										                            break;
										           case 'autocomplete' :
										           					    
										                            $param = '<br/><span id="'.$row_param['param_id'].'">
										                                        <input class="segInput" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="hidden" style="width: 300px" value="">
										                                        <input class="segInput" name="'.$row['rep_script'].'_param1_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param1_'.$row_param['param_id'].'" type="text" onblur="clearNr(this.id);" style="width: 300px" value="">
										                                      </span>';
										                            break;                 
										           case 'checkbox' :     
										           					$option_sql=$db->Execute($row_param['choices']);
										                            $options="";
										                            if (is_object($option_sql)){
										                                while ($row_option=$option_sql->FetchRow()) {
										                                	$options.='<input class="segInput" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_id_'.$row_option['id'].'" type="checkbox" value="'.$row_option['id'].'"/>'.$row_option['namedesc'].'<br/>';
										                                    // $options.='<option value="'.$row_option['id'].'">'.$row_option['id'].'-'.$row_option['namedesc'].'</option>';
										                                }
										                                	if($row_param['param_id']=='mem_category'){
																	         $options.= '<button id="segInput" cursor: pointer;" onclick="getICPICDP();" title="Load">
															                        	 Load
															                    	   </button>';
															                    	   }
										                            }
										                            
										                            $param = '<br/><span id="'.$row_param['param_id'].'">'.$options.'</span><br/>';
										                            break;
										                            // $param = '<span id="'.$row_param['param_id'].'"><input name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="checkbox" value=""></span>';
										                            // break;
										                            
										           case 'textbox' :    
										                            $param = '<br/><span id="'.$row_param['param_id'].'">
										                                        <input class="segInput" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="text" style="width: 300px" value="">
										                                      </span>';
										                            break;
										                                                                                                                                                    
										           default :        break;                   
										        }
										        
										        $sTemp = $sTemp.$param.'<br/>';
										        
										    }
										}	
							 		}
							 	}

							 			# added by carriane 02/14/18
							 			if($row['rep_name'] == $admission_logbook_rep_name and $dept_nr==IPBM_DEPT)
							 				$row['rep_name'] = "Admission Logbook";
										if($row['rep_name']==$icd_encoded_name && $dept_nr==IPBM_DEPT)
	 		                                 $row['rep_name'] = 'Number of Patients encoded with ICD 10';
	 		                             if($row['rep_name']==$ave_daily_census_admitted_name && $dept_nr == IPBM_DEPT)
	 		                             	$row['rep_name']= 'Average Daily Census of Admitted Patients';
	 		                            if($row['rep_name']==$ipd_demog_name && $dept_nr == IPBM_DEPT)
											 $row['rep_name'] = "Patient's Demographic Data";			
	 		                             			// if($_a_3_sassgin_dept_only && !$admin_access && $inDeptList && $row["report_id"] == "Outpatient_Dept_Daily_Transaction")	 		                       continue;

											 	if(!$row['with_template'] && $row['dept_nr'] == $dept_nr){
			 		                            	$row['with_template'] = 1;
			 		                            }

	 		                             			if(!$search)
	 		                             				$data[] = array(
												            'report_id' => trim($row['report_id']),
												            'rep_script' => trim($row['rep_script']),
															'rep_group' => trim($row['rep_group']),
															'rep_name' => trim($row['rep_name']),
												            'with_template' => trim($row['with_template']),
												            'query_in_jasper' => trim($row['query_in_jasper']),
												            'parameter' => $sTemp,
												            'is_have_param' => ($sTemp!=null) ? 1 : 0,
														);
					                                	
					                    
								                	$temp_total++;

								                }
								            }

								        }
						  }	
						
					}

					if($sql != $all_reports && $search){
					  	if ($dept_nr!=''){
							$cond_dept = " AND (r.rep_dept_nr = ".$db->qstr($dept_nr)."
		  						OR d.dept_nr = ".$db->qstr($dept_nr).")";
						}

						$cond_rep = "";
						if($extractList){
							$cond_rep = " AND r.report_id IN (".rtrim($extractList,",") . ")";
						}
						
						$sql = "SELECT SQL_CALC_FOUND_ROWS r.*,d.dept_nr FROM seg_rep_templates_registry r LEFT JOIN seg_rep_templates_dept d ON d.report_id=r.report_id WHERE r.is_active=1 ". $cond_dept . $cond_rep;
					  
					}
		     	}else if ($user_to_all_reports ) {
		     		$sql = $all_reports;
		     	}

		}else{

			$sql = $all_reports;
		}
		
		$total = $temp_total;
		
}


if($search) {
    $sql.=" AND rep_name LIKE '%".$search."%'";
}

if($rep_category) {
    $sql.=" AND rep_category = ".$db->qstr($rep_category);
}

if($sort_sql) {
	#$sql.=" ORDER BY {$sort_sql} ";
    $sql.=" ORDER BY rep_group, rep_name";
}
if($maxRows) {
	$sql.=" LIMIT $offset, $maxRows";
}


$result = $db->Execute($sql);
// echo "ss = ".$sql;

if ($result !== FALSE) {
	$total = $db->GetOne("SELECT FOUND_ROWS()");
 	while ($row = $result->FetchRow()) {

 		$sTemp = '';
 		if($dept_nr == IPBM_DEPT){

									$param_ids=&$repgen_obj->getReportParamExistById($row['report_id']);
								}
								else{
									$param_ids=&$repgen_obj->getReportParamById($row['report_id']);
								}
		$inDeptList = false;						
	 	if(is_object($param_ids)){
	 		while($row_param_ids=$param_ids->FetchRow()) {
	 			$parameter=&$repgen_obj->getReportParameter2($row_param_ids['param_id']);

		 		if(is_object($parameter)){
				    while($row_param=$parameter->FetchRow()) {
				    	
				        $sTemp = $sTemp.'<span id="'.$row_param['param_id'].'_1"><b>'.$row_param['parameter'].'</b></span>';
				        switch ($row_param['param_type']){
				           case 'option' :  
				                            $option_arr = explode(",", $row_param['choices']);

				                            if($row_param["param_id"] == "OPDdeptt"){
				                            	$row_param['parameter'] = "All";
				                            }else{
				                            	$row_param['parameter'] = "Select " . $row_param['parameter'];
				                            }
				                            $options="<option value=''>-".$row_param['parameter']."-</option>";
				                            if (count($option_arr)){
				                            	$inChildDeptList = true;
				                                while (list($key,$val) = each($option_arr))  {
				                                    $val = substr(trim($val),0,strlen(trim($val))-1);
				                                    $val = substr(trim($val),1);
				                                    $val_arr = explode("-", $val);
				                                    if($row_param["param_id"] == "OPDdeptt" && $dept_nr == "150"){
					                                    	if($sessionUserNr == $val_arr[0])
						                                    
						                                    $inDeptList = true;

															if($_a_3_sassgin_dept_only && !$admin_access){

																if($pDeptRes = $dept_obj->searchParentDept($sessionUserNr)){
																						
																	if($inChildDeptList){
																		$inChildDeptList = false;
																		$options.='<option value="'.$pDeptRes["id"].'">'.$pDeptRes["name"].'</option>';
																		$deptChildList = $dept_obj->getChildDeptList($sessionUserNr);
																		if($deptChildList){
																			while ($cdeptRes = $deptChildList->FetchRow()) {
																				$options.='<option value="'.$cdeptRes["id"].'">'.$cdeptRes["name"].'</option>';
																			}
																		}
																	}
																	
																}else{
																	if($val_arr[0] == $sessionUserNr){
																		$options.='<option value="'.$val_arr[0].'">'.$val_arr[1].'</option>';
																	}
																}
													 		}else{
													 			$options.='<option value="'.$val_arr[0].'">'.$val_arr[1].'</option>';
													 		}	
					                                    
														}else{
															$options.='<option value="'.$val_arr[0].'">'.$val_arr[1].'</option>';
														}
				                                    // $options.='<option value="'.$val_arr[0].'">'.$val_arr[1].'</option>';
				                                }
				                            }
				                            if($row_param['param_id']=="billing_insurance"){ $param = '<br/><span id="'.$row_param['param_id'].'"><select onchange="billing_insurance_control()" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" style="width: 300px" class="segInput">'.$options.'</select></span>';}
				                            else{ $param = '<br/><span id="'.$row_param['param_id'].'"><select name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" style="width: 300px" class="segInput">'.$options.'</select></span>';}
				                            break;
				           case 'time' :    
				                            $jav =  '<script type="text/javascript">
				                                        jQuery(function($){
				                                            $J("#'.$row['rep_script'].'_param_'.$row_param['param_id'].'_from").mask("99:99");
				                                            $J("#'.$row['rep_script'].'_param_'.$row_param['param_id'].'_to").mask("99:99");
				                                            $J("#'.$row['rep_script'].'_param_'.$row_param['param_id'].'_from" ).blur(function() {
																if((parseInt($( "#param_time_from" ).val().substr(0, 3))>12) || (parseInt($( "#param_time_from" ).val().substr(3, 6))>59)){ alert("Something is wrong with the time format in From:"); $("#param_time_from").val("")}
															});
															$J("#'.$row['rep_script'].'_param_'.$row_param['param_id'].'_to" ).blur(function() {
																if((parseInt($( "#param_time_to" ).val().substr(0, 3))>12) || (parseInt($( "#param_time_to" ).val().substr(3, 6))>59)){ alert("Something is wrong with the time format in To:"); $("#param_time_to").val("")}
				                                        });
				                                        });
				                                    </script>';
				                            $param = $jav.'<span id="'.$row_param['param_id'].'">
				                                                <input class="segInput" maxlength="5" size="2" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_from" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_from" type="text" value="">
				                                                <select class="segInput" name = "'.$row['rep_script'].'_param_'.$row_param['param_id'].'_meridian_from" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_meridian_from">
				                                                    <option value = "AM">AM</option>
				                                                    <option value = "PM">PM</option>
				                                                </select>
				                                                To
				                                                <input class="segInput" maxlength="5" size="2" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_to" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_to" type="text" value="">
				                                                <select class="segInput" name = "'.$row['rep_script'].'_param_'.$row_param['param_id'].'_meridian_to" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_meridian_to">
				                                                    <option value = "AM">AM</option>
				                                                    <option value = "PM">PM</option>
				                                                </select>
				                                           </span>';
				                            break; 
				           case 'date' :    
				                            $param = '<span id="'.$row_param['param_id'].'"><input class="segInput" maxlength="10" size="8" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="text" value=""></span>';
				                            if($dept_nr == BB_DEPT){
				                            	$date_format3 = '%m/%d/%Y';
				                            	$jav =  '<script type="text/javascript">
				                                        now = new Date();
									                    Calendar.setup ({
									                            inputField: "'.$row['rep_script'].'_param_'.$row_param['param_id'].'",
									                            dateFormat: "'.$date_format3.'",
									                            trigger: "expirydate-trigger",
									                            showTime: false,
									                            fdow: 0,
									                            onSelect: function() { this.hide() }
									                    });
				                                    </script>';
				                            	$param = $jav.'<span id="'.$row_param['param_id'].'">
				                            				<input class="segInput" maxlength="10" size="8" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="text" value="" style="margin-top:8px; margin-left: 4px">
				                            			  </span>
				                            			  <button id="expirydate-trigger" style="margin-top:8px; margin-left: 4px; cursor: pointer;" onclick="return false" title="Select Expiry Date">
								                        	  <span class="icon calendar"></span>Select
								                    	   </button>';
				                            }
				                            break; 
				           
				           case 'boolean' : 
				                            $param = '<span id="'.$row_param['param_id'].'"><input class="segInput" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="checkbox" value="1"></span>';
				                            break;
				           case 'radio' :   
				                            $param = '<span id="'.$row_param['param_id'].'"><input class="segInput" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="radio" value="1"></span>';;
				                            break;                                                                     
				           case 'sql' :  
				           					if ($row_param['param_id']=='phar_encoder' || $row_param['param_id'] == 'ihomp_orientation') {	
				           						$option_sql=$db->Execute($row_param['choices']);
				           						$options="<option value=''>-Select a ".$row_param['parameter']."-</option>";
					                            if (is_object($option_sql)){
					                                while ($row_option=$option_sql->FetchRow()) {
					                                    $options.='<option value="'.$row_option['id'].'">'.$row_option['namedesc'].'</option>';
					                                }
					                            }
					                            
					                            $param = '<br/><span id="'.$row_param['param_id'].'"><select name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" style="width: 300px" class="segInput"></span><br/> 
					                                     '.$options.'</select>';
											}elseif($row_param['param_id']=='billing_icd' || $row_param['param_id']=='billing_icp'){
								           		$fromDate =date('Y-m-d',strtotime($_GET['dateFROM']));
					           					$toDate = date('Y-m-d',strtotime($_GET['dateTO']));
					                           	$var = $row_param['choices'];
					                            $date = array("fromDate", "toDate");
												$changeDate   = array($fromDate,$toDate);
												$newphrase = str_replace($date, $changeDate,$var);
												$option_sql=$db->Execute($newphrase);
  											$options="<option value=''>-Select a ".$row_param['parameter']."-</option>";
				                            if (is_object($option_sql)){
				                                while ($row_option=$option_sql->FetchRow()) {
				                                	if($dept_nr==PHS_DEPT){
				                                		 $options.='<option value="'.$row_option['id'].'">'.$row_option['namedesc'].'</option>';
				                                	}
				                                	else{
				                                		 $options.='<option value="'.$row_option['id'].'">'.$row_option['id'].'-'.$row_option['namedesc'].'</option>';
				                                	}
				                                }
				                            }
				                            
				                            $param = '<br/><span id="'.$row_param['param_id'].'"><select name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" style="width: 300px" class="segInput"></span><br/> 
				                                     '.$options.'</select>';
											}else{
					                            $option_sql=$db->Execute($row_param['choices']);
					                            $options="<option value=''>-Select a ".$row_param['parameter']."-</option>";
					                            if (is_object($option_sql)){
					                                while ($row_option=$option_sql->FetchRow()) {
					                                	if($dept_nr==PHS_DEPT || $dept_nr == BB_DEPT || $dept_nr==IPBM_DEPT){
					                                		 $options.='<option value="'.$row_option['id'].'">'.$row_option['namedesc'].'</option>';
					                                	}
					                                	else{
					                                		 $options.='<option value="'.$row_option['id'].'">'.$row_option['id'].'-'.$row_option['namedesc'].'</option>';
					                                	}
					                                }
					                            }
				                            
					                            $param = '<br/><span id="'.$row_param['param_id'].'"><select name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" style="width: 300px" class="segInput"></span><br/> 
					                                     '.$options.'</select>';
			                                }
				                            break;
				           case 'text' :   

				                            $param = '<span id="'.$row_param['param_id'].'"><br/>Search by code&nbsp<input name="'.$row['rep_script'].'_paramCheck_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_paramCheck_'.$row_param['param_id'].'" type="checkbox" value="">
				                                      <br/>
				                                        <input class="segInput" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="hidden" style="width: 300px" value="">
				                                        <input class="segInput" name="'.$row['rep_script'].'_param1_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param1_'.$row_param['param_id'].'" type="text" style="width: 300px" value="">
				                                      </span>';
				                            break;
				           case 'autocomplete' :
				           					    
				                            $param = '<br/><span id="'.$row_param['param_id'].'">
				                                        <input class="segInput" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="hidden" style="width: 300px" value="">
				                                        <input class="segInput" name="'.$row['rep_script'].'_param1_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param1_'.$row_param['param_id'].'" type="text" onblur="clearNr(this.id);" style="width: 300px" value="">
				                                      </span>';
				                            break;                 
				           case 'checkbox' :     
				           					$option_sql=$db->Execute($row_param['choices']);
										    $options="";
										    if (is_object($option_sql)){
										        while ($row_option=$option_sql->FetchRow()) {
										            $options.='<input class="segInput" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_id_'.$row_option['id'].'" type="checkbox" value="'.$row_option['id'].'"/>'.$row_option['namedesc'].'<br/>';
										        }
										       	if($row_param['param_id']=='mem_category'){
										         $options.= '<button id="segInput" cursor: pointer;" onclick="getICPICDP();" title="Load">
								                        	 Load
								                    	   </button>';
								                    	   }
										    }                 
										    $param = '<br/><span id="'.$row_param['param_id'].'">'.$options.'</span>';
										    break;
				                            // $param = '<span id="'.$row_param['param_id'].'"><input name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="checkbox" value=""></span>';
				                            // break;
				                            
				           case 'textbox' :    
				                            $param = '<br/><span id="'.$row_param['param_id'].'">
				                                        <input class="segInput" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="text" style="width: 300px" value="">
				                                      </span>';
				                            break;
				                                                                                                                                                    
				           default :        break;                   
				        }
				        
				        $sTemp = $sTemp.$param.'<br/>';
				    }
				}	
	 		}
	 	}

	 	# updated by carriane 02/14/18
 	if($row['rep_name']==$admission_logbook_rep_name && $dept_nr==IPBM_DEPT)
	 		$row['rep_name'] = 'Admission Logbook';
	 	if($row['rep_name']==$icd_encoded_name && $dept_nr==IPBM_DEPT)
	 		$row['rep_name'] = 'Number of Patients encoded with ICD 10';
	if($row['rep_name']==$ave_daily_census_admitted_name && $dept_nr == IPBM_DEPT)
			$row['rep_name']= 'Average Daily Census';
	if($row['rep_name']==$ipd_demog_name && $dept_nr == IPBM_DEPT)
			$row['rep_name'] = "Patient's Demographic Data";
	 	// if($_a_3_sassgin_dept_only && !$admin_access && $inDeptList && $row["report_id"] == "Outpatient_Dept_Daily_Transaction")	 		                       continue;

	 		// if($from)
	 	
	 	if(!$row['with_template'] && $row['dept_nr'] == $dept_nr){
        	$row['with_template'] = 1;
        }
	 		$data[] = array(
	            'report_id' => trim($row['report_id']),
	            'rep_script' => trim($row['rep_script']),
				'rep_group' => trim($row['rep_group']),
				'rep_name' => trim($row['rep_name']),
	            'with_template' => trim($row['with_template']),
	            'query_in_jasper' => trim($row['query_in_jasper']),
	            'parameter' => $sTemp,
	            'is_have_param' => ($sTemp!=null) ? 1 : 0,
			);
	}
}

$response = array(
	'currentPage'=>$page,
	'total'=>($total != 0) ? $total : $temp_total,
	'data'=>$data
 );

$json = new Services_JSON;
print $json->encode($response);