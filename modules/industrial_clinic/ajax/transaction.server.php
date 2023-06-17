<?php
function saveTransaction($data,$action,$dataEmp,$dateEmpTypeNew){
	global $db;
        // $db->debug = true;

	$objResponse = new xajaxResponse();
	$tr_obj = new SegICTransaction();


	if($data['refno']==""){
		$new_id = $tr_obj->getNewId();
		$refno=$new_id;
		$data['refno']=$refno;
	}

	$data['trxn_date']=date('Y-m-d H:i:s',strtotime($data['trxn_date']));
	$data['create_id'] = $_SESSION['sess_temp_userid'];
	$data['modify_id'] = $_SESSION['sess_temp_userid'];
	$data['create_dt'] = date('Y-m-d H:i:s');
	$data['modify_dt'] = date('Y-m-d H:i:s');
	$data['history'] = "Create ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]";
	if(empty($data['agency_id']))
		unset($data['agency_id']);

	$dataEncounter=
					array(
					 'encounter_nr',
					 'pid',
					 'encounter_type',
					 'encounter_date'
					 );
				
				
				$pid = $data['pid'];
				/**search if is discharged = active or Not
				/* Added by Marvin Cortes 05/16/2016
				**/
				$rs = $tr_obj->getHsscRecord($pid);
				$casenumber = $rs['casenumber'];
				$counts = $rs['counts'];
					 if($action=="add"){
					 		if($counts < 1 ){
									 		$strMsg="Transaction successfully save!";
											$dataEncounter=$tr_obj->getNew_Encounter_nr();
											$data['encounter_nr']= $dataEncounter['encounter_nr'];
											$dataEncounter['pid']=$data['pid'];
											$dataEncounter['encounter_date']= date('Y-m-d H:i:s');
							                $dataEncounter['smoker_history']=$data['smoker_history'];
							                $dataEncounter['drinker_history']=$data['drinker_history'];
											$tr_obj->insertNew_Encounter_nr($dataEncounter);
											$tr_obj->update_Encounter_tracker($data['encounter_nr']);
											/*added by art 05/18/2014*/
											if ($data['age'] >= 60) { 
												$tr_obj->isSeniorCitizen($data['pid']);
											}
											/*end art*/
											$db->BeginTrans();
									 			$saveok = $tr_obj->saveTransaction($data);
												$saveOthers= $tr_obj->saveExamPurposeOthers($data['refno'],$data['purpose_exam_other'],'add'); #added by art 04/19/2014
											
											if($dateEmpTypeNew==1){
												$emp=new SegAgencyManager();
												$emp->useCompEmployee();
												$dataEmp['create_id'] = $_SESSION['sess_temp_userid'];
												$dataEmp['modify_id'] = $_SESSION['sess_temp_userid'];
												$dataEmp['create_dt'] = date('Y-m-d H:i:s');
												$dataEmp['modify_dt'] = date('Y-m-d H:i:s');
												if(!$emp->isEmployeeExisting($dataEmp['pid'],$dataEmp['company_id'])){
														$emp->assignCompanyEmployee($dataEmp);
												}
												#commented by VAN 03-02-2011
												/*else{
														$objResponse->call('outputResponse', 'ERROR(SAVE):'.$emp->getErrorMsg().'\nSQL:'.$emp->sql);
														return $objResponse;
												}*/

											}
							}else{
								return $objResponse->alert("This patient had a previous consultation/admission with the same date and clinics.\n The case # is ".$casenumber.". Please check the said case #. Thank you.");
							}	#End MC	
	 				}elseif($action=="update"){
                        
                        $dataEncounter['encounter_nr'] = $data['encounter_nr'];
                        $dataEncounter['smoker_history'] = $data['smoker_history'];
                        $dataEncounter['drinker_history'] = $data['drinker_history'];
                        $tr_obj->update_encounter($dataEncounter);
                        
						$saveok = $tr_obj->updateTransaction($data,$data['refno']);
						$saveOthers= $tr_obj->saveExamPurposeOthers($data['refno'],$data['purpose_exam_other'],'update'); #added by art 04/19/2014
						$strMsg="Transaction successfully updated!";
							#added by art 06/23/2014
							if($dateEmpTypeNew==1){
								$emp=new SegAgencyManager();
								$emp->useCompEmployee();
								$dataEmp['create_id'] = $_SESSION['sess_temp_userid'];
								$dataEmp['modify_id'] = $_SESSION['sess_temp_userid'];
								$dataEmp['create_dt'] = date('Y-m-d H:i:s');
								$dataEmp['modify_dt'] = date('Y-m-d H:i:s');
								if(!$emp->isEmployeeExisting($dataEmp['pid'],$dataEmp['company_id'])){
										$emp->assignCompanyEmployee($dataEmp);
								}
							}
							#end art
					 }
					 # Added by James 4/28/2014
					 elseif($action=="view"){
					 		$objResponse->alert("Sorry, unable to update this transaction. This transaction is already discharged!");
					 		return $objResponse;
					 }
	if ($saveok && $saveOthers) {
		$db->CommitTrans();
	}else{
		$db->RollbackTrans();
	}

	 if($saveok!==FALSE) {
		$outputResponse='<dl id="system-message">
						<dt>Information</dt>
						<dd>'.$strMsg.'
						</dd>
					</dl>';
		$objResponse->redirect('./seg-ic-transaction-form.php?refno='.$data['refno'].'&process='.$action);


	} else {
		$objResponse->call('outputResponse', 'ERROR(Update):'.$tr_obj->getErrorMsg().'\nSQL:'.$tr_obj->sql);
	}
	return $objResponse;
}

function populateTransaction($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
	$objResponse = new xajaxResponse();
	$transaction=new SegICTransaction();

	$keyword = $args[0];

	$result=$transaction->fetchTransaction($keyword);
	#$objResponse->alert($transaction->sql);

	if($result) {
		$found_rows = $transaction->FoundRows();
		$last_page = ceil($found_rows/$max_rows)-1;
		if ($page_num > $last_page) $page_num=$last_page;

		if($data_size=$result->RecordCount()) {
			$temp=0;
			$i=0;
			$objResponse->contextAssign('currentPage', $page_num);
			$objResponse->contextAssign('lastPage', $last_page);
			$objResponse->contextAssign('maxRows', $max_rows);
			$objResponse->contextAssign('listSize', $found_rows);

			$DATA = array();
			while($row = $result->FetchRow()) {

				$DATA[$i]['patient_id'] = $row['patient_id'];
				$DATA[$i]['case_no'] = $row['case_no'];
				$DATA[$i]['full_name'] = $row['full_name'];
				$DATA[$i]['refno'] = $row['refno'];
				$DATA[$i]['FLAG'] = 1;
				$i++;
			} #end while
			#$objResponse->alert(print_r($DATA,true));
			$objResponse->contextAssign('dataSize', $data_size);
			$objResponse->contextAssign('listData', $DATA);
			#$objResponse->alert(print_r($DATA,true));
		}
		else {
			$objResponse->contextAssign('dataSize', 0);
			$objResponse->contextAssign('listData', NULL);
		}

	} else {

		$objResponse->contextAssign('dataSize', -1);
		$objResponse->contextAssign('listData', NULL);
	}

	$objResponse->script('this.fetchDone()');
	return $objResponse;
}
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/industrial_clinic/ajax/transaction.common.php');
require_once($root_path.'include/care_api_classes/industrial_clinic/class_ic_transactions.php');
require_once($root_path.'include/care_api_classes/industrial_clinic/class_agency_mgr.php');

$xajax->processRequest();