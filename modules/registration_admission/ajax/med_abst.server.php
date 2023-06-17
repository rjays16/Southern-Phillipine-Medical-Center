<?php

	//added by raisa 01-21-09
	#edited by VAN 07-12-2010
		function populateMedAbstEncRef($pid,$sElem,$page,$searchkey,$med_abst,$isIPBM=0) {
				define('IPBMIPD_enc', 13);
				define('IPBMOPD_enc', 14);
				global $db;
				$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
				$glob_obj->getConfig('pagin_patient_search_max_block_rows');
				$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
				$objResponse = new xajaxResponse();
				$enc=new Encounter;
				$offset = $page * $maxRows;
				$searchkey = utf8_decode($searchkey);
				if(strpos($searchkey,"/"))
				{
						list($m,$d,$y) = explode("/",$searchkey);
						$searchkey = $y."-".$m."-".$d;
				}

				
				$ergebnis = $enc->SearchEncRefMedAbstList($pid, $searchkey, $med_abst, $maxRows, $offset,0,$isIPBM);
				$total = $enc->FoundRows();
				// var_dump($ergebnis->FetchRow());die;

				$lastPage = floor($total/$maxRows);

				if ((floor($total%10))==0)
						$lastPage = $lastPage-1;

				if ($page > $lastPage) $page=$lastPage;
			
				$rows=0;
				$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
				$objResponse->addScriptCall("clearList","product-list");
				
				if ($ergebnis) {
						
						$rows=$ergebnis->RecordCount();
						while($result=$ergebnis->FetchRow()) {
								if($med_abst=="true")
								{
										
										$date_prepared=substr($result["create_dt"],0,10);

										if (($date_prepared!='0000-00-00')&&($date_prepared!=""))
											$date_prepared = date('m/d/Y',strtotime($date_prepared));
										if (($result["date_admit"]!='0000-00-00')&&($result["date_admit"]!=""))
											$date_admit = date('m/d/Y',strtotime($result["date_admit"]));

										
										if ($result["custom_middle_initial"] != "") {
											$dr = explode(" ", $result["dr"]);
											$dr = $dr[0] .' '. $result["custom_middle_initial"].'. '. $dr[2];
										}else {
											$dr = $result["dr"];
										}
								
										$objResponse->addScriptCall("addProductToList","product-list",$pid,$result["encounter_nr"]."-".$result["referral_nr"],$date_prepared,$result["encounter_nr"],$result["referral_nr"],$date_admit,$result["dept"], $result["prepared_by"], $result["abst_nr"], $dr);
								}
								else
								{
										
										$date_admit=substr($result["admit_date"],0,10);
										if($result["encounter_type"]==1)
												$enc_type="ER";
										else if($result["encounter_type"]==2 || $result["encounter_type"]==IPBMOPD_enc)
												$enc_type="OPD";
										else if($result["encounter_type"]==3 || $result["encounter_type"]==4 || $result["encounter_type"]==IPBMIPD_enc)
												$enc_type="IPD";

										$objResponse->addScriptCall("addProductToList","product-list",$result["encounter_nr"]."-".$result["referral_nr"],$result["encounter_nr"],$result["referral_nr"],$result["dept"],$enc_type,$date_admit,$dr);
								}
						}#end of while
				} #end of if

				if (!$rows) $objResponse->addScriptCall("addProductToList","product-list",NULL);
				if ($sElem) {
						$objResponse->addScriptCall("endAJAXSearch",$sElem);
				}
				return $objResponse;
		}


		

		# ----------------------------- -----------added by shandy 08/28/2013------------------- -------------------------- 
		# add certifacate of confiment
	        # in medical records
			function populateConfiCertEncRefHistory($pid,$sElem,$page,$searchkey,$med_cert) {
				global $db;
				$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
				$glob_obj->getConfig('pagin_patient_search_max_block_rows');
				$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
				$objResponse = new xajaxResponse();
				$enc=new Encounter;
				$offset = $page * $maxRows;
				$searchkey = utf8_decode($searchkey);
				if(strpos($searchkey,"/"))
				{
						list($m,$d,$y) = explode("/",$searchkey);
						$searchkey = $y."-".$m."-".$d;
				}
				$ergebnis = $enc->SearchEncRefConfCertListHist($pid, $searchkey, $med_cert, $maxRows, $offset,0);
				$total = $enc->FoundRows();


				$lastPage = floor($total/$maxRows);

				if ((floor($total%10))==0)
						$lastPage = $lastPage-1;

				if ($page > $lastPage) $page=$lastPage;		
			
				$rows=0;
				$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
				$objResponse->addScriptCall("clearList","product-list");
				if ($ergebnis) {
					
						$rows=$ergebnis->RecordCount();
						while($result=$ergebnis->FetchRow()) {
								if($med_cert=="true")
								{
										$date_prepared=substr($result["create_dt"],0,10);

										if (($date_prepared!='0000-00-00')&&($date_prepared!=""))
											$date_prepared = date('m/d/Y',strtotime($date_prepared));
										if (($result["date_admit"]!='0000-00-00')&&($result["date_admit"]!=""))
											$date_admit = date('m/d/Y',strtotime($result["date_admit"]));

										$objResponse->addScriptCall("addProductToList","product-list",$pid,$result["encounter_nr"]."-".$result["referral_nr"],$date_prepared,$result["encounter_nr"],$result["referral_nr"],$date_admit,$result["requested_by"], $result["create_id"], $result["cert_nr"], $result["attending_doctor"]);
								}                                                                                                       
								else
								{
										$date_admit=substr($result["admit_date"],0,10);
										if($result["encounter_type"]==1)
												$enc_type="ER";
										else if($result["encounter_type"]==2)
												$enc_type="OPD";
										else if($result["encounter_type"]==3 || $result["encounter_type"]==4)
												$enc_type="IPD";
										$objResponse->addScriptCall("addProductToList","product-list",$result["encounter_nr"]."-".$result["referral_nr"],$result["encounter_nr"],$result["referral_nr"],$result["requested_by"],$enc_type,$date_admit);
								}
						}
				} 

				if (!$rows) $objResponse->addScriptCall("addProductToList","product-list",NULL);
				if ($sElem) {
						$objResponse->addScriptCall("endAJAXSearch",$sElem);
				}
				return $objResponse;
		}

		#---------------------------------  -------------- end added---------------- -------------------------------------------

		function deleteAbstract($encounter_nr, $abst_nr, $pid){

				global $db;
				$enc=new Encounter;
				$objResponse = new xajaxResponse();

				$status=$enc->deleteMedAbst($encounter_nr, $abst_nr);

				if ($status) {
					
					$objResponse->addScriptCall("startAJAXSearch","search", 0, $pid);
					$objResponse->addAlert("The medical abstract is successfully deleted.");
				}else{
					
					$objResponse->addScriptCall("Deleting the medical abstract failed.");
				}

			return $objResponse;
		}
                #added by shandy 08/28/2013 for delete function cert of conf......
		// function deleteCertificateConf($encounter_nr, $pid){
		// 		global $db;
		// 		$enc=new Encounter;
		// 		$objResponse = new xajaxResponse();

		// 		$status=$enc->deleteConfCert($encounter_nr);

		// 		if ($status) {
		// 			$objResponse->addScriptCall("startAJAXSearch","search", 0, $pid);
		// 			$objResponse->addAlert("The Confiment Certificate is successfully deleted.");
		// 		}else{
		// 			$objResponse->addScriptCall("Deleting the Confiment Certificate failed.");
		// 		}

		// 	return $objResponse;
		// }
		#added by VAN 01-22-09
		require('./roots.php');
		require($root_path.'include/inc_environment_global.php');
		require($root_path."modules/registration_admission/ajax/med_cert.common.php");
		require($root_path."modules/registration_admission/ajax/med_abst.common.php");

		#added by VAN 04-17-08
		require_once($root_path.'include/care_api_classes/class_globalconfig.php');
		require($root_path.'include/care_api_classes/class_encounter.php');

		$xajax->processRequests();
		#-------------
?>