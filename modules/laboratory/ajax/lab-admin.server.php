<?php

		#added by Raissa 05-27-09
		function deleteTestGroup($group_id){
				global $db;

				$objResponse = new xajaxResponse();
				$lab_obj=new Lab_Results();

				$sql = "SELECT group_id FROM seg_lab_resultdata WHERE group_id = '$group_id'
								UNION SELECT refno as group_id FROM seg_lab_servdetails AS sd WHERE sd.service_code IN (SELECT service_code FROM seg_lab_result_groupparams WHERE group_id='$group_id' AND (ISNULL(status) OR status!='deleted'))";
				 $res=$db->Execute($sql);
				if ($res && $rs=$res->FetchRow()){
						$objResponse->addAlert("The laboratory test group cannot be deleted. It has already been used.");
				}
				else{
						$status = $lab_obj->deleteGroup($group_id);
						if ($status) {
								$objResponse->addAlert("The laboratory service is successfully deleted.");
						}
						else
								$objResponse->addAlert("Error in deleting.");
				}
				$objResponse->addScriptCall("document.forms[0].submit");
				return $objResponse;
		}

		#added by Raissa 05-27-09
		function populateLabTestGroups($search,$sElem,$searchkey,$page) {
				global $db;
				$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
				$lab_obj = new Lab_Results();

				$glob_obj->getConfig('pagin_patient_search_max_block_rows');
				$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

				$objResponse = new xajaxResponse();
				$offset = $page * $maxRows;
				$searchkey = utf8_decode($searchkey);
				//$objResponse->addAlert('maxrows = '.$maxRows);
				$total_srv = $lab_obj->countSearchTestGroups($searchkey,$maxRows,$offset);
				#$objResponse->addAlert($lab_obj->sql);
				$total = $lab_obj->count;
				#$objResponse->addAlert('total = '.$total);

				$lastPage = floor($total/$maxRows);

				if ((floor($total%10))==0)
						$lastPage = $lastPage-1;

				if ($page > $lastPage) $page=$lastPage;
				$ergebnis=$lab_obj->SearchTestGroups($searchkey,$maxRows,$offset);
				#$objResponse->addAlert("sql = ".$lab_obj->sql);
				$rows=0;
				$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
				$objResponse->addScriptCall("clearList","ServiceList");
				if ($ergebnis) {
						$rows=$ergebnis->RecordCount();
						while($result=$ergebnis->FetchRow()) {
								//$objResponse->addAlert("values = ".$result["param_id"].",".$result["name"].",".$data_type.",".$result["order_nr"].",".$SI_range.",".$CU_range.",".$gender);
								$objResponse->addScriptCall("addProductToList","ServiceList",$result["group_id"],$result["name"]);
						}#end of while
				} #end of if

				if (!$rows) $objResponse->addScriptCall("addProductToList","ServiceList",NULL);
				if ($sElem) {
						$objResponse->addScriptCall("endAJAXSearch",$sElem);
				}

				return $objResponse;
		}

		#added by Raissa 02-04-09
		function populateLabTestParametersList($group_code,$sElem,$searchkey,$page) {
				global $db;
				$glob_obj = new GlobalConfig($GLOBAL_CONFIG);

				$glob_obj->getConfig('pagin_patient_search_max_block_rows');
				$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

				$objResponse = new xajaxResponse();
				$srvObj=new SegLab;
				$offset = $page * $maxRows;
				$searchkey = utf8_decode($searchkey);
				//$objResponse->addAlert('maxrows = '.$maxRows);
				$total_srv = $srvObj->countSearchParameters($group_code,$searchkey,0,$maxRows,$offset);
				#$objResponse->addAlert($srvObj->sql);
				$total = $srvObj->count;
				#$objResponse->addAlert('total = '.$total);

				$lastPage = floor($total/$maxRows);

				if ((floor($total%10))==0)
						$lastPage = $lastPage-1;

				if ($page > $lastPage) $page=$lastPage;
				$ergebnis=$srvObj->SearchParameters($group_code,$searchkey,0,$maxRows,$offset);
				#$objResponse->addAlert("sql = ".$srvObj->sql);
				$rows=0;
				$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
				$objResponse->addScriptCall("clearList","ServiceList");
				if ($ergebnis) {
						$rows=$ergebnis->RecordCount();
						while($result=$ergebnis->FetchRow()) {
								$SI_range="";
								$CU_range="";
								if($result["is_numeric"]=="1")
										$data_type = "numeric";
								else if($result["is_boolean"]=="1")
										$data_type = "boolean";
								else if($result["is_longtext"]=="1")
										$data_type = "long text";
								else
										$data_type = "text";
								if($result["SI_lo_normal"]!="")
								{
										if($result["SI_hi_normal"]!="")
												$SI_range = $result["SI_lo_normal"] ." - ". $result["SI_hi_normal"];
										else
												$SI_range = " >= ". $result["SI_lo_normal"];
								}
								else if($result["SI_hi_normal"]!="")
										$SI_range = " < ". $result["SI_hi_normal"];
								$SI_range = $SI_range ." ". $result["SI_unit"];
								if($result["CU_lo_normal"]!="")
								{
										if($result["CU_hi_normal"]!="")
												$CU_range = $result["CU_lo_normal"] ." - ". $result["CU_hi_normal"];
										else
												$CU_range = " >= ". $result["CU_lo_normal"];
								}
								else if($result["CU_hi_normal"]!="")
												$CU_range = " < ". $result["CU_hi_normal"];
								$CU_range = $CU_range ." ". $result["CU_unit"];
								if($result["is_male"]=="1")
								{
										if($result["is_female"]=="1")
												$gender = "both";
										else
												$gender = "male";
								}
								else
										$gender = "female";
								//$objResponse->addAlert("values = ".$result["param_id"].",".$result["name"].",".$data_type.",".$result["order_nr"].",".$SI_range.",".$CU_range.",".$gender);
								$objResponse->addScriptCall("addProductToList","ServiceList",$result["param_id"],$result["name"],$data_type,$result["order_nr"], $SI_range, $CU_range, $gender);
						}#end of while
				} #end of if

				if (!$rows) $objResponse->addScriptCall("addProductToList","ServiceList",NULL);
				if ($sElem) {
						$objResponse->addScriptCall("endAJAXSearch",$sElem);
				}

				return $objResponse;
		}

	/* popserv function (Populate Services)
	*		Fetches the database for available services associated with a specific service group
	*/
	function psrv($grp, $serv_code) {
		$srvObj=new SegLab;
		$objResponse = new xajaxResponse();

		#$objResponse->addAlert("psrv service code : $serv_code");
		#$objResponse->addAlert("psrv group code : $grp");

		#$ergebnis=$srvObj->getLabServices("group_code = '$grp'");

		if (($serv_code=="none")||($serv_code=="*")){
			#$objResponse->addAlert("true");
			#$ergebnis=$srvObj->getLabServices("ss.group_code = '$grp'","ss.service_code");
			$ergebnis=$srvObj->getLabServices(1,"ss.group_code = '$grp'");
		}else{
			#$objResponse->addAlert("false");
			#$ergebnis=$srvObj->getLabServices("ss.group_code = '$grp' AND ((ss.service_code LIKE '%$serv_code%') OR (ss.name LIKE '%$serv_code%'))","ss.service_code");
			$ergebnis=$srvObj->getLabServices(0,"ss.group_code = '$grp' AND ((ss.service_code LIKE '%$serv_code%') OR (ss.name LIKE '%$serv_code%'))");
		}
		#$objResponse->addAlert("psrv sql : $srvObj->sql");

		$objResponse->addScriptCall("crow");
		$recCount = $srvObj->count;
		#$objResponse->addAlert("psrv recCount : $recCount");
		$counter=0;
		if ($recCount>0) {
			while($result=$ergebnis->FetchRow()) {
				$counter++;
				$objResponse->addScriptCall("nrow",$grp,trim($result["service_code"]),trim($result["name"]),trim($result["price_cash"]),trim($result["price_charge"]));
				#$objResponse->addScriptCall("nrow",$grp,$result["service_code"],$result["name"],$result["price_cash"],$result["price_charge"], $result["c1_price"], $result["c2_price"], $result["c3_price"]);
			}
		}
		else {
			$objResponse->addScriptCall("nrow",$grp,NULL);
		}
		# $objResponse->addAlert(print_r($srvObj->sql,TRUE));
		return $objResponse;
	}

	function nsrv($code, $name, $cash, $charge, $grp) {
	#function nsrv($code, $name, $cash, $charge, $grp, $cashC1, $cashC2, $cashC3) {
		// Escape passed argument
		global $db;
		$srvObj=new SegLab;
		$objResponse = new xajaxResponse();

		#$status=$srvObj->createLabService($code, $name, $cash, $charge, '', $grp);
		//$code = strtoupper($code);

		#$status=$srvObj->createLabService($code, $name, $cash, $charge, '', $grp);
		$status=$srvObj->createLabService($code, addslashes($name), $cash, $charge, '', $grp);

		#$objResponse->addAlert("nsrv sql 1 : $srvObj->sql");
		#$cashC1, $cashC2, $cashC3
		#$status2=$srvObj->createLabService_discounts($code, $cashC1, $cashC2, $cashC3);
		#$objResponse->addAlert("nsrv sql 2 : $srvObj->sql");

		#if (($status)&&($status2)) {
		if ($status){
			$objResponse->addScriptCall("nrow", $grp, $code, $name, $cash, $charge, TRUE);
			#$objResponse->addScriptCall("nrow", $grp, $code, $name, $cash, $charge, $cashC1, $cashC2, $cashC3, TRUE);
			$objResponse->addScriptCall("clrForm");
		}
		else {
			$objResponse->addScriptCall("showme", $srvObj->sql);
			$objResponse->addAlert("ERROR:".$db->ErrorMsg());
		}
		return $objResponse;
	}

	function dsrv($rowno,$code) {
		global $db;
		$srvObj=new SegLab;
		$dept_obj=new Department;

		$objResponse = new xajaxResponse();

		$sql = "SELECT service_code FROM seg_lab_servdetails WHERE service_code = '$code'
					UNION
							SELECT code AS service_code FROM seg_hcare_srvops WHERE code='$code'";

		$res=$db->Execute($sql);
		$row=$res->RecordCount();

		if ($row==0){
			$status=$srvObj->deleteLabService($code);
			$dept = $dept_obj->getDepartmentInfo("name_formal like 'pathology'", "name_formal");
			$status2=$srvObj->deleteServiceDiscounts($code,$dept['nr']);
			#$objResponse->addAlert("sql:".$srvObj->sql);

			#if ($status) {
			if (($status)&&$status2) {
				$objResponse->addScriptCall("gui_delRow", "serviceTable","srvRow",$rowno);
				$objResponse->addScriptCall("colt", "serviceTable");

			}
			else
				$objResponse->addScriptCall("showme", $srvObj->sql);
		}else{
			$objResponse->addAlert("The laboratory service cannot be deleted. It is already been used.");
		}
		return $objResponse;
	}

	/*Added by VAS*/
	/*get the respective Group Laboratory services that belongs to a certain Department*/
	#function getServiceGroup($dept_nr, $group_id='') {
	function getServiceGroup($group_code='') {
		global $db;
		$srvObj=new SegLab;
		$objResponse = new xajaxResponse();
		
		$session = $_SESSION['sess_login_personell_nr'];
		$strSQL = "select permission,login_id from care_users WHERE personell_nr=".$db->qstr($session);
		$permission = array();
		$ss= array();
		$login_id = "";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()){
                	$permission[] = $row['permission'];
                	$login_id = $row['login_id'];
                }
            }
        }
        $rs=$srvObj->getLabServiceGroups2();

		#$objResponse->addAlert("xajax getServiceGroup: group_id = '$group_id'");
		#$objResponse->addAlert("xajax getServiceGroup: dept_nr = '$dept_nr'");

		#$rs=$srvObj->getLabServiceGroups2("department_nr=$dept_nr");
		
       
       
		$objAcl = new Acl($login_id);
     	
     	$view_lab_bloodbank = $objAcl->checkPermissionRaw('_a_2_zB');
     	$view_lab_CPS= $objAcl->checkPermissionRaw('_a_2_zSPC');
     	$view_lab_clinicalchemistry = $objAcl->checkPermissionRaw('_a_2_zC');
     	$view_lab_clinicalmicroscopy = $objAcl->checkPermissionRaw('_a_2_zU');
     	$view_lab_drugtest = $objAcl->checkPermissionRaw('_a_2_zDT');
     	$view_lab_hematology = $objAcl->checkPermissionRaw('_a_2_sH');
        $view_lab_histapathology = $objAcl->checkPermissionRaw('_a_2_zHP');
        $view_lab_hivLab = $objAcl->checkPermissionRaw('_a_2_zHIV');
        $view_lab_microbiology = $objAcl->checkPermissionRaw('_a_2_zMB');
        $view_lab_mycobacteriology = $objAcl->checkPermissionRaw('_a_2_zML');
        $view_lab_pcr= $objAcl->checkPermissionRaw('_a_2_zPCR');
        $view_lab_sia = $objAcl->checkPermissionRaw('_a_2_zI');
        $view_lab_speciallaboratory = $objAcl->checkPermissionRaw('_a_2_zSPL');
        $view_lab_admin = $objAcl->checkPermissionRaw('_a_1_labadmin');
        $view_lab_cathlab = $objAcl->checkPermissionRaw('_a_2_sCATH');
        $view_lab_2decho = $objAcl->checkPermissionRaw('_a_2_zECHO');
        $view_all= $objAcl->checkPermissionRaw('_a_0_all');
      
   
        if($view_lab_bloodbank || $view_lab_CPS || $view_lab_clinicalchemistry || $view_lab_clinicalmicroscopy || $view_lab_drugtest 
        	|| $view_lab_hematology || $view_lab_histapathology || $view_lab_hivLab || $view_lab_microbiology || $view_lab_mycobacteriology 
        	|| $view_lab_pcr || $view_lab_sia || $view_lab_speciallaboratory || $view_lab_cathlab||$view_lab_2decho){
        		$fff= "";
        	// var_dump($permission);
        	foreach ($permission as $value)
        	 {
        	 		$fff.=$value;
		        	$exvalue = explode(" ",$fff);
		        	foreach ($exvalue as $value) {


		        		$extracted_value =substr((string)$value,6);
		        		array_push($ss,$extracted_value);
		     		}
        	}
        	
        	
              if ($rs) {
			$objResponse->addScriptCall("ajxClearOptions");
			if ($srvObj->count > 0){
					$objResponse->addScriptCall("ajxAddOption","Select Service Section",0);
			}else{
				$objResponse->addScriptCall("ajxAddOption","No Service Section",0);
			}

			
				if($view_all){
					while ($result=$rs->FetchRow()) {
				$objResponse->addScriptCall("ajxAddOption",$result["name"],$result["group_code"]);
			}

				}
				else {
					while ($result=$rs->FetchRow()) {
				foreach ($ss as $value ) {
					if ($result["group_code"]==$value) 
						$objResponse->addScriptCall("ajxAddOption",$result["name"],$result["group_code"]);
					
					}
				}
			
				
			}
			

			$objResponse->addScriptCall("ajxSetServiceGroup",$group_code);

		}
		else {
			#$objResponse->addAlert("getServiceGroup : Error retrieving lab service groups information...");
			$objResponse->addScriptCall("ajxClearOptions");
			$objResponse->addScriptCall("ajxAddOption","No Service Section",0);
		}



        	
        }
        elseif ($view_lab_admin) {
        	if ($rs) {
			$objResponse->addScriptCall("ajxClearOptions");
				if ($srvObj->count > 0){
					$objResponse->addScriptCall("ajxAddOption","Select Service Section",0);
				}else{
				$objResponse->addScriptCall("ajxAddOption","No Service Section",0);
				}	

			while ($result=$rs->FetchRow()) {
				$objResponse->addScriptCall("ajxAddOption",$result["name"],$result["group_code"]);
			}

			$objResponse->addScriptCall("ajxSetServiceGroup",$group_code);

		}
			else {
			#$objResponse->addAlert("getServiceGroup : Error retrieving lab service groups information...");
			$objResponse->addScriptCall("ajxClearOptions");
			$objResponse->addScriptCall("ajxAddOption","No Service Section",0);
				}

        	
        }
        else
        {
        	if ($rs) {
			$objResponse->addScriptCall("ajxClearOptions");
			if ($srvObj->count > 0){
					$objResponse->addScriptCall("ajxAddOption","Select Service Section",0);
			}else{
				$objResponse->addScriptCall("ajxAddOption","No Service Section",0);
			}
			$objResponse->addScriptCall("ajxSetServiceGroup",$group_code);

		}
		else {
			#$objResponse->addAlert("getServiceGroup : Error retrieving lab service groups information...");
			$objResponse->addScriptCall("ajxClearOptions");
			$objResponse->addScriptCall("ajxAddOption","No Service Section",0);
		}

        }
		

		#$objResponse->addAlert("getServiceGroup sql : $srvObj->sql");

		// if ($rs) {
		// 	$objResponse->addScriptCall("ajxClearOptions");
		// 	if ($srvObj->count > 0){
		// 			$objResponse->addScriptCall("ajxAddOption","Select Service Section",0);
		// 	}else{
		// 		$objResponse->addScriptCall("ajxAddOption","No Service Section",0);
		// 	}

		// 	while ($result=$rs->FetchRow()) {
		// 		$objResponse->addScriptCall("ajxAddOption",$result["name"],$result["group_code"]);
		// 	}

		// 	$objResponse->addScriptCall("ajxSetServiceGroup",$group_code);

		// }
		// else {
		// 	#$objResponse->addAlert("getServiceGroup : Error retrieving lab service groups information...");
		// 	$objResponse->addScriptCall("ajxClearOptions");
		// 	$objResponse->addScriptCall("ajxAddOption","No Service Section",0);
		// }

		return $objResponse;

	}

	#added by VAN 03-12-08
	function populateLabServiceList($group_code,$sElem,$searchkey,$page) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);

		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

		$objResponse = new xajaxResponse();
		$srvObj=new SegLab;
		$offset = $page * $maxRows;
		$searchkey = utf8_decode($searchkey);
		$area = '';
		// $objResponse->addAlert($group_code);
		$total_srv = $srvObj->SearchService($group_code,$searchkey,0,$maxRows,$offset,$area, 0,1);
		// $objResponse->addAlert($srvObj->sql);
		$total = $srvObj->count;
		#$objResponse->addAlert('total = '.$total);

		$lastPage = floor($total/$maxRows);

		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;

		if ($page > $lastPage) $page=$lastPage;
		$ergebnis=$srvObj->SearchService($group_code,$searchkey,0,$maxRows,$offset,$area, 0,0);
		#$objResponse->addAlert("sql = ".$srvObj->sql);
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","ServiceList");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
				if ($result['status']=='unavailable')
					$available = 0;
				else
					$available = 1;

				#$objResponse->alert($result['status']);
				#$objResponse->addAlert("id, name, cash, charge, sservice = ".trim($result["service_code"])." , ".trim($result["name"])." , ".number_format(trim($result["price_cash"]),2,'.', '')." , ".number_format(trim($result["price_charge"]),2,'.', '')." , ".$result["is_socialized"]);
				$objResponse->addScriptCall("addProductToList","ServiceList",trim($result["service_code"]),trim($result["ipdservice_code"]),trim($result["oservice_code"]),trim($result["erservice_code"]),trim($result["icservice_code"]),trim($result["name"]),number_format(trim($result["price_cash"]),2,'.', ''),number_format(trim($result["price_charge"]),2,'.', ''), $result["is_socialized"], $result["code_num"], $available, $result["is_ER"], $result["in_lis"]);
			}#end of while
		} #end of if

		if (!$rows) $objResponse->addScriptCall("addProductToList","ServiceList",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}

		return $objResponse;
	}

	function deleteService($code){
		global $db;
		$srvObj=new SegLab;
		$dept_obj=new Department;
		$objResponse = new xajaxResponse();

		#$objResponse->addAlert("ajax deleteRequest code = $code");
		$sql = "SELECT service_code FROM seg_lab_servdetails WHERE service_code = '$code'
					UNION
							SELECT code AS service_code FROM seg_hcare_srvops WHERE code='$code' AND provider='LB'";

		 $res=$db->Execute($sql);
		 #$objResponse->addAlert("sql = ".$sql);
		 $row=$res->RecordCount();
		 #$objResponse->addAlert("row = ".$row);

		if ($row==0){

			$status=$srvObj->deleteLabService($code);
			#$dept = $dept_obj->getDepartmentInfo("name_formal like 'pathology'", "name_formal");
			#$status2=$srvObj->deleteServiceDiscounts($code,$dept['nr']);
			$status2=$srvObj->deleteServiceDiscounts($code,'LB');
			#$objResponse->addAlert('dept = '.$dept['nr']);
			#$objResponse->addAlert($srvObj->sql);

				if (($status)&&$status2) {
					$objResponse->addScriptCall("removeService",$code);
					$objResponse->addAlert("The laboratory service is successfully deleted.");
				}else
					$objResponse->addScriptCall("showme", $srvObj->sql);
		 }else{
				$objResponse->addAlert("The laboratory service cannot be deleted. It is already been used.");
		 }
		return $objResponse;
	}
	#----------------------

	#-----------added by VAN---
	function getLabListReq($encmode='') {
		$srvObj=new SegLab;
		$objResponse = new xajaxResponse();

		$ergebnis=$srvObj->getRequestorList($encmode);
		#$objResponse->addAlert("getLabListReq sql : $srvObj->sql");

		$objResponse->addScriptCall("crow2");
		$recCount = $srvObj->result->RecordCount();
		#$objResponse->addAlert("getLabListReq count : $recCount");
		$counter=0;
		if ($recCount>0) {
			#$objResponse->addAlert("getLabListReq while : $ergebnis");
			while($result=$ergebnis->FetchRow()) {
				#$objResponse->addAlert("getLabListReq while : ".$result["refno"]);
				$counter++;
				$name = trim($result["name_first"])." ".trim($result["name_middle"])." ".trim($result["name_last"]);
				/*
				if ($result["encounter_type"] == 5)
					$enctype = "Walkin";
				else
					$enctype = "Inpatient";
				*/
				if ($result["encounter_type"] == 1)
					$enctype = "ER Patient";
				elseif ($result["encounter_type"] == 2)
					$enctype = "Outpatient";
				elseif (($result["encounter_type"] == 3)||($result["encounter_type"] == 4)||($result["encounter_type"] == 5))
					$enctype = "Inpatient";
				elseif ($result["encounter_type"] == 5)
					$enctype = "PHS-Outpatient";
				else
					$enctype = "Walkin";

				#$objResponse->addScriptCall("nrow2",$result["pid"],$name,$result["serv_dt"],$enctype, TRUE);
				$objResponse->addScriptCall("nrow2",trim($result["refno"]),trim($result["pid"]),$name,date("m-d-Y",strtotime($result["serv_dt"])),$enctype, TRUE);
			}
		}
		else {
			#$objResponse->addAlert("getLabListReq sql : $srvObj->count");
			$objResponse->addScriptCall("nrow2",NULL);
		}
		# $objResponse->addAlert(print_r($srvObj->sql,TRUE));
		return $objResponse;
	}

	function drequestor($rowno,$refno, $pid) {
	#function drequestor($rowno,$refno) {
		$srvObj=new SegLab;
		$objResponse = new xajaxResponse();
		#$status=$srvObj->deleteRequestor($refno, $pid);
		$status=$srvObj->deleteRequestor($refno);
		#$objResponse->addAlert("drequestor sql : $srvObj->sql");

		if ($status) {
			$objResponse->addScriptCall("gui_delRow", "serviceTable","srvRow",$rowno);
			$objResponse->addScriptCall("colt", "serviceTable");
			$objResponse->addScriptCall("closechild");
		}
		else
			$objResponse->addScriptCall("showme", $srvObj->sql);
		return $objResponse;
	}

	#added by VAN 03-10-08
	function populateLabGroupList($sElem,$searchkey,$page) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

		$objResponse = new xajaxResponse();
		$srvObj=new SegLab;
		$offset = $page * $maxRows;
		#$objResponse->addAlert("searchkey = ".$searchkey);
		$searchkey = utf8_decode($searchkey);
		if ($searchkey==NULL)
			$searchkey = '*';
		$total_srv = $srvObj->countSearchGroup($searchkey,$maxRows,$offset);
		#$objResponse->addAlert("sql c1 = ".$srvObj->sql);
		$total = $srvObj->count;
		#$objResponse->addAlert("total = ".$total);
		$lastPage = floor($total/$maxRows);

		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;

		if ($page > $lastPage) $page=$lastPage;
		$ergebnis=$srvObj->SearchGroup($searchkey,$maxRows,$offset);
		#$objResponse->addAlert("sql c2 = ".$srvObj->sql);
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","labgrouplistTable");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
				#$objResponse->addAlert("sql c2 = ".$result["group_code"]." , ".$result["name"]." , ".$result["other_name"]);
				#$code = str_replace("^","'",stripslashes($labgroupInfo['group_code']));;
				 #$objResponse->addScriptCall("addLabGroup","labgrouplistTable",$code,stripslashes($result["name"]),strtoupper(stripslashes($result["other_name"])));
				 $objResponse->addScriptCall("addLabGroup","labgrouplistTable",stripslashes($result["group_code"]),stripslashes($result["name"]),strtoupper(stripslashes($result["other_name"])));
			}
		}
		#commented by VAN 03-17-08
		if (!$rows) $objResponse->addScriptCall("addLabGroup","labgrouplistTable",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}

		return $objResponse;
	}

	function deleteLabGroup($grp_id){
		global $db;
		$srvObj=new SegLab;
		$objResponse = new xajaxResponse();

		//$grp_id = stripslashes($grp_id);
		$grp_id = utf8_decode(addslashes($grp_id));
		#$objResponse->addAlert("grp id  = ".$grp_id);
		$sql = "SELECT * FROM seg_lab_services WHERE group_code='".$grp_id."'";
		#$objResponse->addAlert("sql = ".$sql);
		#$objResponse->addAlert("grp id  = ".$grp_id);
		$res=$db->Execute($sql);
		$row=$res->RecordCount();

		if ($row==0){
			$status=$srvObj->deleteServiceGroup($grp_id);

			if ($status) {
				$objResponse->addScriptCall("removeLabGroup",$grp_id);
				$objResponse->addAlert("The laboratory service group is successfully deleted.");
			}else{
				$objResponse->addScriptCall("showme", $srvObj->sql);
			}
		 }else{
				$objResponse->addAlert("The laboratory service group cannot be deleted. It is already been used.");
		 }
		return $objResponse;
	}
	/*
	function saveLabGroup($code, $gname, $goname, $mode){
		global $db;
		$srvObj=new SegLab;
		$objResponse = new xajaxResponse();

		$code = str_replace("'","",$code);
		$objResponse->addAlert("save");
		$srvObj->getServiceGroupInfo($gname, $code);
		if (($srvObj->count==0)&&($code!='none')){
			#$srvObj->saveLabServiceGroup(strtoupper($_POST['gname']), strtoupper($_POST['gcode']), $_POST['goname'], $status
			if ($srvObj->saveLabServiceGroup(strtoupper($gname), strtoupper($code), $goname, $mode)) {
				#$objResponse->addScriptCall("showMessage",$mode);
				if ($mode=='save')
					$objResponse->addAlert("Service Group ".strtoupper($gname)." is successfully created!");
				else
					$objResponse->addAlert("Service Group ".strtoupper($gname)." is successfully updated!");
				#$objResponse->addScriptCall("refreshWindow");
			}
		}else{
			$objResponse->addAlert("Service Group ".strtoupper($gname)." already exists or the code is not accepted!");
			#$objResponse->addScriptCall("showMessage","error");
		}

		return $objResponse;
	}
	*/
	#----------------------

		function populateLabReagentsList($sElem,$searchkey,$page) {
				global $db;
				$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
				$glob_obj->getConfig('pagin_patient_search_max_block_rows');
				$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

				$objResponse = new xajaxResponse();
				$srvObj=new SegLab;
				$offset = $page * $maxRows;
				#$objResponse->addAlert("searchkey = ".$searchkey);
				$searchkey = utf8_decode($searchkey);
				if ($searchkey==NULL)
						$searchkey = '*';
				$total_srv = $srvObj->countSearchReagents($searchkey,$maxRows,$offset);
				#$objResponse->addAlert("sql c1 = ".$srvObj->sql);
				$total = $srvObj->count;
				#$objResponse->addAlert("total = ".$total);
				$lastPage = floor($total/$maxRows);

				if ((floor($total%10))==0)
						$lastPage = $lastPage-1;

				if ($page > $lastPage) $page=$lastPage;
				$ergebnis=$srvObj->SearchReagents($searchkey,$maxRows,$offset);
				#$objResponse->addAlert("sql c2 = ".$srvObj->sql);
				$rows=0;

				$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
				$objResponse->addScriptCall("clearList","labreagentslistTable");
				if ($ergebnis) {
						$rows=$ergebnis->RecordCount();
						while($result=$ergebnis->FetchRow()) {
								#$objResponse->addScriptCall("addLabReagents","labreagentslistTable",stripslashes($result["reagent_code"]),stripslashes($result["reagent_name"]),strtoupper(stripslashes($result["other_name"])));
								$objResponse->addScriptCall("addLabReagents","labreagentslistTable",stripslashes($result["bestellnum"]),stripslashes($result["artikelname"]),strtoupper(stripslashes($result["generic"])));
						}
				}
				#commented by VAN 03-17-08
				if (!$rows) $objResponse->addScriptCall("addLabReagents","labreagentslistTable",NULL);
				if ($sElem) {
						$objResponse->addScriptCall("endAJAXSearch",$sElem);
				}

				return $objResponse;
		}

		function populateReagentList($sElem,$searchkey,$page) {
				global $db;
				$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
				$glob_obj->getConfig('pagin_patient_search_max_block_rows');
				$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

				$objResponse = new xajaxResponse();
				$srvObj=new SegLab;

				$offset = $page * $maxRows;
				#$objResponse->addAlert("searchkey = ".$searchkey);
				$searchkey = utf8_decode($searchkey);
				if ($searchkey==NULL)
						$searchkey = '*';
						/*
						$objResponse->addAlert("searchkey = ".$searchkey);
						$objResponse->addAlert("maxRows = ".$maxRows);
						$objResponse->addAlert("offset = ".$offset);
				*/
				$total_srv = $srvObj->countSearchReagents($searchkey,$maxRows,$offset);

				#$objResponse->addAlert("sql c1 = ".$srvObj->sql);
				$total = $srvObj->count;
				#$objResponse->addAlert("total = ".$total);
				$lastPage = floor($total/$maxRows);

				if ((floor($total%10))==0)
						$lastPage = $lastPage-1;

				if ($page > $lastPage) $page=$lastPage;
				$ergebnis=$srvObj->SearchReagents($searchkey,$maxRows,$offset);
				#$objResponse->addAlert("sql c2 = ".$srvObj->sql);
				$rows=0;

				$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
				$objResponse->addScriptCall("clearList","labreagentslistTable");
				$details = (object) 'details';
				if ($ergebnis) {
						$rows=$ergebnis->RecordCount();
						while($result=$ergebnis->FetchRow()) {

								$reagent_code = $result["reagent_code"];
								$details->reagent_code = $result["reagent_code"];
								$details->reagent_name = $result["reagent_name"];
								$details->other_name = $result["other_name"];


								#$objResponse->addAlert("sql c2 = ".$print_r($details,true));
								$objResponse->addScriptCall("addReagents","labreagentslistTable", $details);

						}
				}
				else {
						#$details->error = nl2br(htmlentities($srvObj->sql));
				}
				if (!$rows) $objResponse->addScriptCall("addReagents","labreagentslistTable",$details);
			 /*
				if ($rows==1 && $reagent_code) {
						$objResponse->addScriptCall("prepareSelect",$reagent_code);
				}
				*/

				if ($sElem) {
						$objResponse->addScriptCall("endAJAXSearch",$sElem);
				}
				return $objResponse;
		}


		function deleteLabReagent($reagent_code){
				global $db;
				$srvObj=new SegLab;
				$objResponse = new xajaxResponse();

				$reagent_code = utf8_decode(addslashes($reagent_code));
				$sql = "SELECT * FROM seg_lab_service_reagents WHERE reagent_code='".$reagent_code."'";
				$res=$db->Execute($sql);
				$row=$res->RecordCount();

				if ($row==0){
						$status=$srvObj->deleteReagent($reagent_code);

						if ($status) {
								$objResponse->addScriptCall("removeLabReagent",$reagent_code);
								$objResponse->addAlert("The laboratory service reagent is successfully deleted.");
						}else{
								$objResponse->addScriptCall("showme", $srvObj->sql);
						}
				 }else{
								 $objResponse->addAlert("The laboratory service reagent cannot be deleted. It is already been used.");
				 }
				return $objResponse;
		}

		function populateServiceReagentsList($service_code) {
				global $db;
				$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
				$glob_obj->getConfig('pagin_patient_search_max_block_rows');
				$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

				$objResponse = new xajaxResponse();

				$srvObj=new SegLab;

				$offset = $page * $maxRows;

				$ergebnis=$srvObj->getAllReagentsInService($service_code);
				#$objResponse->addAlert($srvObj->sql);
				$rows=0;

				if ($ergebnis) {
						$rows=$ergebnis->RecordCount();
						while($result=$ergebnis->FetchRow()) {
								$objResponse->addScriptCall("initialServiceReagentList",$result['reagent_code'], 
									$result['reagent_name'],$result['item_qty']);

																																	 ;
						}#end of while
				} #end of if

				if (!$rows) $objResponse->addScriptCall("initialServiceReagentList",NULL);

				return $objResponse;
		}

	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require($root_path.'include/care_api_classes/class_labservices_transaction.php');
	require($root_path.'include/care_api_classes/class_lab_results.php');

	#added by VAN 03-10-08
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require($root_path.'include/care_api_classes/class_department.php');
	require($root_path."modules/laboratory/ajax/lab-admin.common.php");
	 require_once($root_path . 'include/care_api_classes/class_acl.php');
	$xajax->processRequests();
?>
