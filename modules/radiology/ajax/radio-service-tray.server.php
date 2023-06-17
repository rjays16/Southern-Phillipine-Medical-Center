<?php

function populateRadioServiceList($area='',$area_type='',$is_cash=1,$discountid='',$discount=0,$is_senior=0,$is_walkin=1,$dept_nr,$source_req='RD',$isStat=0,$is_charge2comp=0,$compID='',$sElem,$searchkey,$page,$group_code) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

		$objResponse = new xajaxResponse();
		$radio_obj = new SegRadio;
		$objSS = new SocialService;
		$offset = $page * $maxRows;

		#$group_code = $ref_source;
		// $objResponse->alert("searcElement: ".$sElem." searchkey: ".$searchkey." group_code: ".$group_code);
		if (!$discount)
			$discount = 0;
		$ssInfo = $objSS->getSSClassInfo($discountid);

		if (($discountid=='SC')&& ($is_senior))
			$is_senior = 1;
		else
			$is_senior = 0;

		if (($discountid!='SC') && ($discountid !='A-PWD') && ($discountid !='B-PWD') && ($discountid !='C1-PWD') && ($discountid !='C2-PWD') && ($discountid !='C3-PWD') && ($discountid !='PWD')){
		if ($ssInfo['parentid'])
			$discountid = $ssInfo['parentid'];
		}

		$sc_walkin_discount = 0;
		#if ((($is_senior) && ($is_walkin)) || ((($is_senior)&&($is_cash==0)))){
		if (($is_senior) && ($is_walkin)){
			$discountid='SC';

			//$sql_sc = "SELECT * FROM seg_default_value WHERE name='senior discount' AND source='SS'";
			$sql_sc = "SELECT non_social_discount FROM seg_discount WHERE discountid = 'SC'";
			$rs_sc = $db->Execute($sql_sc);
			$row_sc = $rs_sc->FetchRow();

			if ($row_sc['non_social_discount'])
				$sc_walkin_discount = $row_sc['non_social_discount'];
		}
		else if($is_senior){
			$sql_sc = "SELECT non_social_discount FROM seg_discount WHERE discountid = 'SC'";
			$rs_sc = $db->Execute($sql_sc);
			$row_sc = $rs_sc->FetchRow();

			if ($row_sc['non_social_discount'])
				$sc_walkin_discount = $row_sc['non_social_discount'];
		}

		$non_social_discount = 0;
		if($discountid == 'PHSDep'){
			$sql_dep = "SELECT discount, non_social_discount FROM seg_discount WHERE discountid=".$db->qstr($discountid);
			$rs_dep = $db->Execute($sql_dep);
			$row_dep = $rs_dep->FetchRow();

			if ($row_dep['non_social_discount']){
				$non_social_discount = $row_dep['non_social_discount'];
				$discount = $row_dep['discount'];
			}
		}

		if($discountid == 'PHS') {
			$sql_phs = "SELECT non_social_discount FROM seg_discount WHERE discountid=".$db->qstr($discountid);
			$rs_phs = $db->Execute($sql_phs);
			$row_phs = $rs_phs->FetchRow();

			if($row_phs['non_social_discount'])
				$non_social_discount = $row_phs['non_social_discount'];
		}

       $ExistNonSocial = array("B-PWD","A-PWD","C1-PWD","C2-PWD","C3-PWD");
        if(in_array($discountid, $ExistNonSocial)) {
            $sql_pwd = "SELECT non_social_discount FROM seg_discount WHERE discountid=".$db->qstr($discountid);
            $rs_pwd = $db->Execute($sql_pwd);
            $row_pwd = $rs_pwd->FetchRow();
            if($row_pwd['non_social_discount'])
                $non_social_discount = $row_pwd['non_social_discount'];
        }

		if ($isStat){
			$sql_stat = "SELECT * FROM seg_default_value WHERE name='stat charge' AND source IS NULL";
			$rs_stat = $db->Execute($sql_stat);
			$row_stat = $rs_stat->FetchRow();

			if ($row_stat['value'])
				$stat_additional = $row_stat['value'];
		}

		#$objResponse->alert('van = '.$sc_walkin_discount);
		#--------
		if (stristr($searchkey,",")){
			$keyword_multiple = explode(",",$searchkey);
			#$objResponse->alert($keyword_multiple[0]);
			$codenum = 0;
			if (is_numeric($keyword_multiple[0]))
					$codenum = 1;

			for ($i=0;$i<sizeof($keyword_multiple);$i++){
				$keyword .= "'".trim($keyword_multiple[$i])."',";
			}
			#$objResponse->addAlert('keyword1 = '.$keyword);
			$word = trim($keyword);
			#$objResponse->addAlert('word = '.$word);
			$searchkey = substr($word,0,strlen($word)-1);
			#$objResponse->addAlert('keyword = '.$keyword);
			$multiple = 1;
		}else{
			$multiple = 0;
		}
		#----------------
		#$objResponse->alert($area);
		$ergebnis=$radio_obj->SearchService2($source_req, $is_charge2comp, $compID, $dept_nr,$is_cash,$discountid,$discount, $is_senior, $is_walkin, $sc_walkin_discount, $non_social_discount, $codenum,$searchkey,$multiple,$maxRows,$offset,$area,$group_code);
		$total = $radio_obj->FoundRows();
		#$objResponse->alert($radio_obj->sql);

		$lastPage = floor($total/$maxRows);

		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;

		if ($page > $lastPage) $page=$lastPage;
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","request-list");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
                #Added by Matsuu
                $q_serv_discount  ="SELECT ssd.`price` AS net_price FROM `seg_service_discounts` AS ssd WHERE ssd.`discountid` = ".$db->qstr($discountid)." AND ssd.`service_code` = ".$db->qstr($result['service_code'])." AND ssd.`service_area`='RD'";
                $get_discount = $db->GetRow($q_serv_discount);
                if(empty($get_discount['net_price'])){
                    $query_parent_discount = "SELECT sd.`parentid` FROM seg_discount AS sd WHERE sd.`discountid` = ".$db->qstr($discountid);
                    $get_parent_discount = $db->GetRow($query_parent_discount);
                    $query_parent_serv_discount = "SELECT ssd.`price` AS net_price FROM `seg_service_discounts` AS ssd WHERE ssd.`discountid` = ".$db->qstr($get_parent_discount['parentid'])." AND ssd.`service_code` = ".$db->qstr($result['service_code'])." AND ssd.`service_area`='RD'";
                    $get_parent_serv_discount = $db->GetRow($query_parent_serv_discount);
                    if(!empty($get_parent_serv_discount['net_price']) && $result['is_socialized']==1&&$is_cash){
                        $result['net_price']= $get_parent_serv_discount['net_price'];
                    }
                    else{
                        $result['net_price']= $result['net_price'];
                    }
                }
                else{
                    $result['net_price']=$result['net_price'];
                }

                #Ended here...
				$name = $result["name"];
				if (strlen($name)>40)
					$name = substr($result["name"],0,40)."...";

				if ($result['status']=='unavailable')
						$available = 0;
				else
						$available = 1;

				#added by VAN 07-14-2010
				if ($area_type){
						$query4 = "SELECT IF($is_cash,p.price_cash,p.price_charge) AS net_price,
												p.price_cash, p.price_charge
												FROM seg_service_pricelist AS p
												WHERE p.service_code=".$db->qstr($result["service_code"])."
												AND p.ref_source='RD' AND p.area_code='$area_type'";
						#$objResponse->alert($query4);
						$radio_serv2 = $db->GetRow($query4);
						if ($radio_serv2){
							$result["price_cash"] = $radio_serv2["price_cash"];
							$result["price_charge"] = $radio_serv2["price_charge"];
							$result["net_price"] = $radio_serv2["net_price"] - ($radio_serv2['net_price'] * $discount);
						}

						#add additional charges
						if ($area_type!='pw'){
							if ($isStat){
									$result["price_cash"] = $result["price_cash"] + ($result["price_cash"] * $stat_additional);
									$result["price_charge"] = $result["price_charge"] + ($result["price_charge"] * $stat_additional);
									$result["net_price"] = $result["net_price"] + ($result["net_price"] * $stat_additional);
							}
						}
			 }else{
				 #add additional charges
				 if ($isStat){
						$result["price_cash"] = $result["price_cash"] + ($result["price_cash"] * $stat_additional);
						$result["price_charge"] = $result["price_charge"] + ($result["price_charge"] * $stat_additional);
						$result["net_price"] = $result["net_price"] + ($result["net_price"] * $stat_additional);
				 }
			 }

				$objResponse->addScriptCall("addProductToList","request-list",$result["service_code"],
														$name,$result["group_code"],$result["dept_name"],number_format($result["price_cash"], 2, '.', ''),
														number_format($result["price_charge"], 2, '.', ''), $result['is_socialized'],
														$result['in_pacs'], $result['pacs_code'],
														number_format($result['net_price'], 2, '.', ''),number_format($result['pf'], 2, '.', ''),number_format($result['net_pf'], 2, '.', ''),$available);
			}#end of while
		} #end of if

		if (!$rows) $objResponse->addScriptCall("addProductToList","request-list",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}

		return $objResponse;
	}
#---------------------------------------------------
	// updated by carriane 10/24/17; limit the displaying of department in IPBM module
	function setALLDepartment($dept_nr=0,$isipbm=0){
		$dept_obj=new Department;

		$objResponse = new xajaxResponse();

		$rs=$dept_obj->getAllMedicalObject($isipbm);
		$objResponse->addScriptCall("ajxClearDocDeptOptions",1);
		if ($rs) {
			if(!$isipbm)
				$objResponse->addScriptCall("ajxAddDocDeptOption",1,"-Select a Department-",0);
			while ($result=$rs->FetchRow()) {
				 $objResponse->addScriptCall("ajxAddDocDeptOption",1,$result["name_formal"],$result["nr"]);
			}
		if($dept_nr)
				$list='';
				$objResponse->addScriptCall("ajxSetDepartment", $dept_nr, $list); # set the department
		}
		else {
			$objResponse->addAlert("setALLDepartment : Error retrieving Department information...");
		}
		return $objResponse;
	}

	function setDepartmentOfDoc($personell_nr=0) {
		$dept_obj=new Department;

		$objResponse = new xajaxResponse();
			if ($personell_nr!=0){
			$result=$dept_obj->getDeptofDoctor($personell_nr);
			if ($result){
				$list = $dept_obj->getAncestorChildrenDept($result["nr"]);   # burn added : July 19, 2007
				if (trim($list)!="")
					$list .= ",".$result["nr"];
				else
					$list .= $result["nr"];
				$objResponse->addScriptCall("ajxSetDepartment",$result["nr"],$list); # set the department
			}
			if($personell_nr)
				$objResponse->addScriptCall("ajxSetDoctor",$personell_nr); # set the doctor

		}else{
			$objResponse->addAlert("setDepartmentOfDoc : Error retrieving Department information of a doctor...");
		}
		return $objResponse;
	}

	function setDoctors($dept_nr=0, $personell_nr=0) {
		$objResponse = new xajaxResponse();

		$pers_obj=new Personell;
		if ($dept_nr)
			$rs=$pers_obj->getDoctorsOfDept($dept_nr);
		else
			$rs=$pers_obj->getDoctors(2);	# argument, $admit_patient NOT IN (0,1), BOTH Inpatient/ER & Outpatient

		$objResponse->addScriptCall("ajxClearDocDeptOptions",0);
		if ($rs) {
			$objResponse->addScriptCall("ajxAddDocDeptOption",0,"-Select a Doctor-",0);

			while ($result=$rs->FetchRow()) {
				if (trim($result["name_middle"]))
					$dot  = ".";

				$doctor_name = trim($result["name_last"]).", ".trim($result["name_first"])." ".substr(trim($result["name_middle"]),0,1).$dot;
				$doctor_name = ucwords(strtolower($doctor_name)).", MD";

				$doctor_name = htmlspecialchars($doctor_name);
				$objResponse->addScriptCall("ajxAddDocDeptOption",0,$doctor_name,$result["personell_nr"]);
			}
			if($personell_nr)
				$objResponse->addScriptCall("ajxSetDoctor", $personell_nr); # set the doctor
			if($dept_nr)
				$objResponse->addScriptCall("ajxSetDepartment", $dept_nr); # set the department
			$objResponse->addScriptCall("request_doc_handler"); # set the 'request_doctor_out' textbox
		}
		else {
			$objResponse->addScriptCall("ajxAddDocDeptOption",0,"-No Doctor Available-",0);
		}
		return $objResponse;
	}

	function getDeptDocValues($encounter_nr){
		global $db;
		$objResponse = new xajaxResponse();

		$enc_obj=new Encounter;

		$patient = $enc_obj->getPatientEncounter($encounter_nr);
		#$objResponse->alert($enc_obj->sql);
		if (($patient['encounter_type']==1)|| ($patient['encounter_type']==2)){
			$dept_nr = $patient['current_dept_nr'];
			$doc_nr = $patient['current_att_dr_nr'];
		}elseif (($patient['encounter_type']==3)|| ($patient['encounter_type']==4)){
			$dept_nr = $patient['consulting_dept_nr'];
			$doc_nr = $patient['consulting_dr_nr'];
		}else{
			$dept_nr = 0;
			$doc_nr = 0;
		}

		$objResponse->addScriptCall("setDeptDocValues",$dept_nr, $doc_nr);

		return $objResponse;
	}

	#added by VAN 05-09-2011
	function validateImpression($service_code, $impression){
		 global $db;
		 #$db->debug = true;
		 $objResponse = new xajaxResponse();
		 #$objResponse->alert($impression);
		 $allowed = 0;
		 if ($impression !=''){
				$numrows = 0;
				$sql_na = "SELECT SQL_CALC_FOUND_ROWS s.* FROM seg_not_allowed_comment s WHERE comment='".addslashes(trim($impression))."' LIMIT 1";
				$result_na = $db->Execute($sql_na);
				#$row_na = $result_na->FetchRow();
				/*if (is_object($result_na))
				$numrows = $result_na->RecordCount();*/
				if (!$result_na->RecordCount()) {
					 $objResponse->addScriptCall("prepareAdd",$service_code);
					 
				}else{
					$objResponse->addScriptCall("unchecked",$service_code);
					$objResponse->alert('Please input a decent clinical impression.');
				}

				/*if ($numrows)
					$allowed = 0;
				else
					$allowed = 1;*/
		 }else{
		 	$objResponse->alert('Please indicate the clinical information.');
		 }

		 // if ($allowed){
			
		 // }else
			// 	$objResponse->addScriptCall("promptalert",$service_code);

		 return $objResponse;
	}

	function getAllServiceOfPackage($service_code, $is_cash=1, $discountid='',$discount=0,$is_senior=0,$is_walkin=1, $isStat=0, $impression=''){
				global $db;
				$objResponse = new xajaxResponse();
				$radio_obj = new SegRadio;
				$objSS = new SocialService;

				#added by VAN 04-28-2011
				#add some filter here that impression with 'not applicable'' related comment is not allowed
				$allowed = 0;
				if ($impression){
					$numrows = 0;
					$sql_na = "SELECT SQL_CALC_FOUND_ROWS s.* FROM seg_not_allowed_comment s WHERE COMMENT='".addslashes(trim($impression))."' LIMIT 1";
					$result_na = $db->Execute($sql_na);
					#$row_na = $result_na->FetchRow();
					if (is_object($result_na))
					$numrows = $result_na->RecordCount();

					if ($numrows)
						$allowed = 0;
					else
						$allowed = 1;
				}

				// if ($allowed){

				$ssInfo = $objSS->getSSClassInfo($discountid);

					/*if ($ssInfo['parentid'])
						$discountid = $ssInfo['parentid'];*/
//					if ($discountid!='SC'){
//				if ($ssInfo['parentid'])
//					$discountid = $ssInfo['parentid'];
//					}

				$sc_walkin_discount = 0;
				if (($is_senior) && ($is_walkin)){
					$discountid='SC';

					$sql_sc = "SELECT * FROM seg_default_value WHERE name='senior discount' AND source='SS'";
					$rs_sc = $db->Execute($sql_sc);
					$row_sc = $rs_sc->FetchRow();

					if ($row_sc['value'])
						$sc_walkin_discount = $row_sc['value'];
				}

				if ($isStat){
					$sql_stat = "SELECT * FROM seg_default_value WHERE name='stat charge' AND source IS NULL";
					$rs_stat = $db->Execute($sql_stat);
					$row_stat = $rs_stat->FetchRow();

					if ($row_stat['value'])
						$stat_additional = $row_stat['value'];
				}

                                #for CTSCAN
					$for_ctscan = 0;
					$for_mri = 0;
					if ($dept=='CT-SCAN')
						$for_ctscan = 1;
					#for MRI
					if ($dept=='MRI')
						$for_mri = 1;
				#$objResponse->alert($is_cash." - ".$discountid." - ".$discount);
				$rs_group = $radio_obj->isServiceAPackage($service_code);
				#$objResponse->alert($radio_obj->sql);
				$rs_count = $radio_obj->count;
				if ($rs_count){
					if (!$discount)
						$discount = 0;
					
					$rs_group_inc = $radio_obj->getAllServiceOfPackage($service_code, $is_cash, $discountid, $discount, $is_senior, $is_walkin, $sc_walkin_discount);
					// $objResponse->alert('pkg = '.$radio_obj->sql);
					#lab exam request that is a package
					while ($row=$rs_group_inc->FetchRow()){
							if ($isStat){
									$row['price_cash'] = $row['price_cash'] + ($row['price_cash'] * $stat_additional);
									$row['price_charge'] = $row['price_charge'] + ($row['price_charge'] * $stat_additional);
									$row['net_price'] = $row['net_price'] + ($row['net_price'] * $stat_additional);
							}

								$objResponse->addScriptCall("prepareAdd_Package",$row['service_code'],$row['name'],
																number_format($row['price_cash'], 2, '.', ''),
																number_format($row['price_charge'], 2, '.', ''),
																$row['is_socialized'],$row['in_pacs'],$row['pacs_code'],
																$row['group_code'],number_format($row['net_price'], 2, '.', ''),
																$for_ctscan,$for_mri);
					}

				} else{
					 #lab exam request that is not a package
						 $objResponse->addScriptCall("prepareAdd_NotPackage",$service_code,$for_ctscan,$for_mri);
					 #$objResponse->alert('not pkg = '.$radio_obj->sql);
				}
				// }else{
					//$objResponse->alert("Please input a decent clinical impression.");
					// $objResponse->addScriptCall("promptalert");
				// }

				return $objResponse;
		}
		 #-----------------

	//added by cha, june 8, 2010
	function populateRadioSections($dept_nr)
		{
			global $db;
			$objResponse = new xajaxResponse();

			$objResponse->addAssign("checklist-div", "innerHTML", "");
			if($dept_nr=='0')
			{
				 $objResponse->assign("radio_section_row", "style.display", "none");
				 return $objResponse;
			}
			$sql = "SELECT group_code, name FROM seg_radio_service_groups WHERE department_nr=".$db->qstr($dept_nr)."AND fromdept='RD' AND status NOT IN ('deleted') ORDER BY name ASC";
			$result = $db->Execute($sql);
			$options = '<option value="0">-Select Section-</option>';
			while($row=$result->FetchRow())
			{
				$options.='<option value="'.$row['group_code'].'">'.$row['name'].'</option>';
			}
			$objResponse->assign("radio_section", "innerHTML", $options);
			$objResponse->assign("radio_section_row", "style.display", "");
			return $objResponse;
		}
	#end CHA---------------------

	function populate_radio_checklist($section, $area_type='',$searchkey='', $area='',$is_cash=1,$discountid='',$discount=0,$is_senior=0,$is_walkin=1,$source_req='RD',$isStat=0,$is_charge2comp=0,$compID='')
	{
		global $db;
		$objSS = new SocialService;
		$objResponse = new xajaxResponse();
		#$objResponse->alert($section);
		$objResponse->addAssign("checklist-div", "innerHTML", "");


			$fromdept = $db->GetOne("SELECT srsg.fromdept FROM seg_radio_service_groups as srsg WHERE srsg.group_code=".$db->qstr($section));

			if($fromdept!='OB'){
					if ($area=='ER')
						$area_cond = " AND is_ER=1 ";
					else if($source_req=='IC') // Added by Gervie 03/06/2016
						$area_cond = " AND is_IC=1 ";
					else
						$area_cond = "";
			}
		
		// end Gervie

		if (!$discount)
			$discount = 0;

		$ssInfo = $objSS->getSSClassInfo($discountid);

		if ($discountid=='SC')
			$is_senior = 1;
		else
			$is_senior = 0;

		/*if ($ssInfo['parentid'])
			$discountid = $ssInfo['parentid'];*/

		// if ($discountid!='SC'){
		// if ($ssInfo['parentid'])
		// 	$discountid = $ssInfo['parentid'];
		// }

		$sc_walkin_discount = 0;
		if (($is_senior && $is_walkin) || ($source_req=='IC' && $discountid == 'SC')){
			$discountid='SC';

			$sql_sc = "SELECT * FROM seg_default_value WHERE name='senior discount' AND source='SS'";
			$rs_sc = $db->Execute($sql_sc);
			$row_sc = $rs_sc->FetchRow();

			if ($row_sc['value'])
				$sc_walkin_discount = $row_sc['value'];
		}

		$non_social_discount = 0;
		if($discountid == 'PHSDep'){
			$sql_dep = "SELECT discount, non_social_discount FROM seg_discount WHERE discountid=".$db->qstr($discountid);
			$rs_dep = $db->Execute($sql_dep);
			$row_dep = $rs_dep->FetchRow();

			if ($row_dep['non_social_discount']){
				$non_social_discount = $row_dep['non_social_discount'];
				$discount = $row_dep['discount'];
			}
		}

		if($discountid == 'PHS') {
			$sql_phs = "SELECT non_social_discount FROM seg_discount WHERE discountid=".$db->qstr($discountid);
			$rs_phs = $db->Execute($sql_phs);
			$row_phs = $rs_phs->FetchRow();

			if($row_phs['non_social_discount'])
				$non_social_discount = $row_phs['non_social_discount'];
		}

        $ExistNonSocial = array("B-PWD","A-PWD","C1-PWD","C2-PWD","C3-PWD", "PWD");
        if(in_array($discountid, $ExistNonSocial)) {
            $sql_pwd = "SELECT non_social_discount FROM seg_discount WHERE discountid=".$db->qstr($discountid);
            $rs_pwd = $db->Execute($sql_pwd);
            $row_pwd = $rs_pwd->FetchRow();
            if($row_pwd['non_social_discount'])
                $non_social_discount = $row_pwd['non_social_discount'];
        }else{
            $discount_social = $discount;
            $non_social="l.in_phs=1 AND '$discountid'='PHS' ";
        }
        // var_dump($non_social_discount);die;

		if ($isStat){
			$sql_stat = "SELECT * FROM seg_default_value WHERE name='stat charge' AND source IS NULL";
			$rs_stat = $db->Execute($sql_stat);
			$row_stat = $rs_stat->FetchRow();

			if ($row_stat['value'])
				$stat_additional = $row_stat['value'];
		}

		if ($discountid  && $source_req!='IC'){
            $discount_pwd = substr($discountid,-3,3);
			$with_disc_query = " IF(l.is_socialized=0,
														 IF($is_cash,IF('$discountid'='PHSDep' OR '$discountid'='PHS' OR '$discount_pwd'='PWD' OR '$discount_pwd'='SC' , l.price_cash * (1-$non_social_discount), l.price_cash),l.price_charge),
														 IF($is_cash,
																	 IF($is_senior,IF($is_cash,IF($is_walkin,(l.price_cash*(1-$sc_walkin_discount)),
																	 IF(sd.price,sd.price,(l.price_cash*(1-$discount)))),l.price_charge),
																	 IF($is_cash,
																			 IF(sd.price,sd.price,
																				 IF($is_cash,
																							(l.price_cash*(1-$discount)),
																							(l.price_charge*(1-$discount))
																				 )
																			 ),
																			 l.price_charge
																		)
															),
															l.price_charge)
													) AS net_price ,
									IF(l.is_socialized=0,l.pf,IF('$discountid'='PHSDep' OR '$discountid'='PHS', l.pf*(1-$discount),l.pf)
													) AS net_pf,l.pf,";

				$with_disc_join = "LEFT JOIN seg_service_discounts AS sd ON sd.service_code=l.service_code
																AND sd.service_area='RD' AND sd.discountid='$discountid'";

		} else{
			if ($source_req=='IC'){
				if ($is_charge2comp){
					//$with_disc_query = " IF(ics.price,ics.price,IF($is_cash,l.price_cash,l.price_charge)) AS net_price, ";
                    //added by Nick 06-24-2014
                    $with_disc_query = " (IF($is_cash,
                                            IF('$discountid'='SC',
                                                IF(ics.price,
                                                    ics.price - (ics.price * $sc_walkin_discount),
                                                    l.price_cash - (l.price_cash * $sc_walkin_discount)
                                                )
                                                ,
                                                IF(ics.price,
                                                    ics.price,
                                                    l.price_cash
                                                )
                                            ),
                                            IF('$discountid'='SC',
                                                IF(ics.price,
                                                    ics.price - (ics.price * $sc_walkin_discount),
                                                    l.price_charge - (l.price_charge * $sc_walkin_discount)
                                                )
                                                ,
                                                IF(ics.price,
                                                    ics.price,
                                                    l.price_charge
                                                )
                                            )
                                          )) AS  net_price,l.pf,l.pf as net_pf,";
					$with_disc_join = " LEFT JOIN seg_industrial_comp_price AS ics ON ics.service_code=l.service_code
															AND ics.company_id='".$compID."' AND ics.service_area='RD'";
				}else{
					$with_disc_query = " IF($is_cash,l.price_cash,l.price_charge) AS net_price, ";
					$with_disc_join = " ";
				}

			}else{
				$with_disc_query = "  IF($is_cash,l.price_cash,l.price_charge) AS net_price,l.pf as pf,l.pf as net_pf, ";

				$with_disc_join = "";
			}
		}

		#-------------------
	
		// $objResponse->alert($fromdept);
		if($fromdept=='OB'){
				$fromdept = 'OB';
				$is_ob = 1;
		}else{
				$fromdept = 'RD';
				$is_ob = 0;
		}

		$query = "SELECT gm.* FROM seg_gui_mgr AS gm WHERE gm.ref_source=".$db->qstr($fromdept)." AND gm.section=".$db->qstr($section);
		$result = $db->Execute($query);
		#$objResponse->alert($query);
		if($result->RecordCount()>0) {
			while($row=$result->FetchRow())
			{

				#edited by VAN 07-29-2010
				$query2 = "SELECT gmd.*, l.name, l.status, IF($is_ob,'UCW',d.name_short) AS dept_name, \n".
										$with_disc_query.
									"l.price_cash as`cash`, l.price_charge as `charge`,g.fromdept, \n".
									"l.group_code,l.is_socialized FROM seg_gui_mgr_details AS gmd \n".
									"LEFT JOIN  seg_radio_services AS l ON gmd.service_code=l.service_code \n".
									"LEFT JOIN seg_radio_service_groups AS g ON g.group_code=l.group_code \n".
									"LEFT JOIN care_department AS d ON d.nr=g.department_nr	\n".
									$with_disc_join." WHERE gmd.nr=".$db->qstr($row["nr"])." ".$area_cond;
				#$objResponse->alert($query2);
				$if_exists = true;

				if($searchkey!="") {
					$search_sql = "SELECT IF(EXISTS(SELECT l.service_code FROM seg_radio_services AS l WHERE l.service_code=".
						"gmd.service_code),1,0) AS `is_existing` \n".
						"FROM seg_gui_mgr_details AS gmd LEFT JOIN seg_radio_services AS l ON l.service_code=gmd.service_code \n".
						"WHERE (gmd.service_code LIKE '%".$searchkey."%' OR l.name LIKE '%".$searchkey."%') AND gmd.nr='".$row["nr"]."'";
					$if_exists = $db->GetOne($search_sql);
					if(!empty($if_exists))
					{
						 $query2.= "AND ((l.service_code like '%".$searchkey."%' OR l.name like '%".$searchkey."%')".
											" OR gmd.name_type='H')";
					}
				}

				if($if_exists)
				{
					$query2.=" ORDER BY gmd.row_order_no, gmd.col_order_no ASC";
					// $objResponse->alert($query2);
					$guiRes = $db->Execute($query2);
					if (is_object($guiRes)){
						while($guiDetails=$guiRes->FetchRow())
						{
                            #Added by Matsuu
                            $q_serv_discount  ="SELECT ssd.`price` AS net_price FROM `seg_service_discounts` AS ssd WHERE ssd.`discountid` = ".$db->qstr($discountid)." AND ssd.`service_code` = ".$db->qstr($guiDetails['service_code'])." AND ssd.`service_area`='RD'";
                            $get_discount = $db->GetRow($q_serv_discount);
                            if(empty($get_discount['net_price'])){
                                $query_parent_discount = "SELECT sd.`parentid` FROM seg_discount AS sd WHERE sd.`discountid` = ".$db->qstr($discountid);
                                $get_parent_discount = $db->GetRow($query_parent_discount);
                                $query_parent_serv_discount = "SELECT ssd.`price` AS net_price FROM `seg_service_discounts` AS ssd WHERE ssd.`discountid` = ".$db->qstr($get_parent_discount['parentid'])." AND ssd.`service_code` = ".$db->qstr($guiDetails['service_code'])." AND ssd.`service_area`='RD'";
                                $get_parent_serv_discount = $db->GetRow($query_parent_serv_discount);
                                if(!empty($get_parent_serv_discount['net_price'])){
                                    $guiDetails['net_price']= $get_parent_serv_discount['net_price'];
                                }else{
                                    $guiDetails['net_price']= $guiDetails['net_price'];
                                }
                            }else{
                                $guiDetails['net_price']=$guiDetails['net_price'];
                            }
                            #Ended here..
							//$objResponse->alert("row-".print_r($guiDetails,true));
							if($guiDetails["name_type"]=="D")
							 {
								 #added by VAN 06-26-2010
								 if ($guiDetails['status']=='unavailable')
									$available = 0;
								 else
									$available = 1;

								 #added by VAN 07-15-2010
									if ($area_type){
											if ($discountid){
												$with_disc_query2 = " IF(l.is_socialized=0,
																							 IF($is_cash,IF('$discountid'='PHSDep',p.price_cash * (1-$dependent_discount), p.price_cash),p.price_charge),
																							 IF($is_cash,
																										 IF($is_senior,IF($is_cash,IF($is_walkin,(p.price_cash*(1-$sc_walkin_discount)),
																										 IF(sd.price,sd.price,(p.price_cash*(1-$discount)))),p.price_charge),
																										 IF($is_cash,
																												 IF(sd.price,sd.price,
																													 IF($is_cash,
																																(p.price_cash*(1-$discount)),
																																(p.price_charge*(1-$discount))
																													 )
																												 ),
																												 p.price_charge
																											)
																								),
																								p.price_charge)
																						) AS net_price , ";

													$with_disc_join2 = "
																							 LEFT JOIN seg_service_discounts AS sd ON sd.service_code=l.service_code
																									AND sd.service_area='RD' AND sd.discountid='$discountid'";

											} else{
												$with_disc_query2 = "  IF($is_cash,p.price_cash,p.price_charge) AS net_price, ";

												$with_disc_join2 = "";
											}

											$query4 = "SELECT $with_disc_query2 p.service_code, p.price_cash as `cash`, p.price_charge as `charge`
																	FROM seg_service_pricelist AS p
																	INNER JOIN seg_radio_services AS l ON l.service_code=p.service_code
																	$with_disc_join2
																	WHERE p.service_code=".$db->qstr($guiDetails["service_code"])."
																	AND p.ref_source='RD' AND p.area_code='$area_type'";
											#$objResponse->alert($query4);
											$radio_serv2 = $db->GetRow($query4);
									}

									if ($radio_serv2){
										$guiDetails["cash"] = $radio_serv2["cash"];
										$guiDetails["charge"] = $radio_serv2["charge"];
										$guiDetails["net_price"] = $radio_serv2["net_price"];
									}

									if ($area_type!='pw'){
										if ($isStat){
											$guiDetails["cash"] = $guiDetails["cash"] + ($guiDetails["cash"] * $stat_additional);
											$guiDetails["charge"] = $guiDetails["charge"] + ($guiDetails["charge"] * $stat_additional);
											$guiDetails["net_price"] = $guiDetails["net_price"] + ($guiDetails["net_price"] * $stat_additional);
										}
									}

								 	/*if($discountid == 'PHSDep'){
										if($is_cash){
											if($guiDetails["is_socialized"] != '1'){
												$guiDetails["net_price"] = $guiDetails["net_price"] - ($guiDetails["net_price"] * $dependent_discount);
											}
										}
									}*/


								#edited by VAN 07-30-2010
								 $service_details[] = array(
										"type"=>$guiDetails["name_type"],
										"col_nr"=>$guiDetails["col_order_no"],
										"row_nr"=>$guiDetails["row_order_no"],
										"service_code"=>$guiDetails["service_code"],
										"service_name"=>$guiDetails["name"],
										"service_cash"=>$guiDetails["cash"],
										"service_charge"=>$guiDetails["charge"],
										"service_net_price"=>$guiDetails["net_price"],
										"dept_name"=>$guiDetails["dept_name"],
										"group_code"=>$guiDetails["group_code"],
										"sservice"=>$guiDetails["is_socialized"],
										"available"=>$available,
										"pf" => $guiDetails["pf"],
										"fromdept" => $guiDetails["fromdept"],
										"net_pf" => $guiDetails["net_pf"]
									);
							 }
							 else	if($guiDetails["name_type"]=="H") {
								 $service_details[] = array(
									"type"=>$guiDetails["name_type"],
									"col_nr"=>$guiDetails["col_order_no"],
									"row_nr"=>$guiDetails["row_order_no"],
									"header"=>$guiDetails["header_data"]);
							 }
						}
						//$objResponse->alert("final-".print_r($service_details,true));
						$objResponse->addScriptCall("print_checklist", $service_details, $row["nr"]);
					}else{
						 $objResponse->addScriptCall("print_checklist_message", "NO CHECKLIST AVAILABLE FOR THIS SECTION..");
					}
				}
				$service_details = array();
			}
			if(!$if_exists){
					$objResponse->addScriptCall("print_checklist_message", "SERVICE NOT FOUND..");
			}
		}
		else {
				$objResponse->addScriptCall("print_checklist_message", "NO CHECKLIST AVAILABLE FOR THIS SECTION..");
		}

		return $objResponse;
	}
	//added by Matsuu, 10292018
	function populateobgyneSections($dept_nr)
		{
			global $db;
			$objResponse = new xajaxResponse();

			$objResponse->addAssign("checklist-div", "innerHTML", "");
			if($dept_nr=='0')
			{
				 $objResponse->assign("radio_section_row", "style.display", "none");
				 return $objResponse;
			}
			$sql = "SELECT group_code, name FROM seg_radio_service_groups WHERE department_nr=".$db->qstr($dept_nr)." AND fromdept='OB' AND status NOT IN ('deleted') ORDER BY name ASC";
			$result = $db->Execute($sql);
			$options = '<option value="0">-Select Section-</option>';
			while($row=$result->FetchRow())
			{
				$options.='<option value="'.$row['group_code'].'">'.$row['name'].'</option>';
			}
			$objResponse->assign("radio_section", "innerHTML", $options);
			$objResponse->assign("radio_section_row", "style.display", "");
			return $objResponse;
		}
	require('./roots.php');

	require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/care_api_classes/class_department.php');
	require_once($root_path.'include/care_api_classes/class_personell.php');
	require($root_path."modules/radiology/ajax/radio-service-tray.common.php");
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require_once($root_path.'include/care_api_classes/class_radiology.php');
	require_once($root_path.'include/care_api_classes/class_encounter.php');
	require_once($root_path.'include/care_api_classes/class_social_service.php');
	$xajax->processRequests();
?>