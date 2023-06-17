<?php 
	error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
	require_once('./roots.php');
	require_once($root_path.'include/inc_jasperReporting.php');
	require_once($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/care_api_classes/class_affidavit_father_surename.php');
	require_once($root_path.'include/care_api_classes/class_address.php');
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	require_once($root_path.'include/care_api_classes/class_personell.php');
	global $db;

	if(isset($_GET['pid'])) {
		$pid = $_GET['pid'];
	} else {
		exit(0);
	}

	$personell = new Personell;
	$addressObj = new Address;
	$affidavit = new AffidavitFatherSurename($pid);
	$hospitalObj = new Hospital_Admin;
    
	$addressDataArr = array();
	$index = (int) 0;
	$info = $affidavit->getRecentData($pid);
	extract($info);
	
	#personell data
    $per_sql = "SELECT p.*, upper(fn_get_personell_name2({$db->qstr($administer_personell)})) AS personell_name
    			FROM seg_signatory AS p WHERE p.personell_nr =".$db->qstr($administer_personell);
    $per_result = $db->Execute($per_sql);
    $per_res = $per_result->FetchRow();
    #end

    #person data
    $Psql = "SELECT cp.`name_first`,cp.`name_middle`,cp.`name_last` FROM `care_person` AS cp WHERE cp.`pid` =".$db->qstr($pid);
    $person_result = $db->Execute($Psql);
    $person_res = $person_result->FetchRow();
    #end

	#relation
	$rel_sql = "SELECT name FROM seg_social_relationships WHERE id =".$db->qstr($child_relationship);
	$result = $db->Execute($rel_sql);
	$rel = $result->FetchRow();
	#end

	$cert_sql = "SELECT f_name_last FROM seg_cert_birth WHERE pid =".$db->qstr($pid);
	$cert_result = $db->Execute($cert_sql);
	$cert_res = $cert_result->FetchRow();

	$address = explode(',', $affiant_address);

	#hospital info
	$row = $hospitalObj->getAllHospitalInfo();
	if($row) {
		$hospital_name = $row['hosp_name'];
	}
	#end

	$addressDataArr = array(
		'brg_nr' => $address[0],
		'mun_nr' => $address[1],
		'prov_nr' => $address[2],
		'country_code' => $address[3]
		);

	// ADDED BY JEFF 07-15-17 and MODIFIED @ 07-24-17
	#For fetching of specific address
	$rInfo = $affidavit->getRecentData($pid);
	$aInfo = explode(',', $rInfo['affiant_address']);

	$addressInfo_strt =  $rInfo['aff_street'];
	$addressInfo_brgy =  $rInfo['aff_brgy'];
	$addressInfo_city =  $rInfo['aff_city'];
	
	if (!$rInfo) {
		$sql_brgy_mun="SELECT sm.`mun_name`,
							  sb.`brgy_name`,
							  cb.`m_residence_basic`
					   FROM
					   `care_person` AS cp 
					   LEFT JOIN `seg_cert_birth` AS cb 
	    			   ON cp.`pid` = cb.`pid`
					   LEFT JOIN `seg_barangays` AS sb 
					   ON cp.`brgy_nr` = sb.`brgy_nr` 
					   LEFT JOIN `seg_municity` AS sm 
					   ON cp.`mun_nr` = sm.`mun_nr` WHERE cp.`pid` =".$db->qstr($pid);

	    $res_brgy_mun = $db->Execute($sql_brgy_mun);

		if ($res_brgy_mun)
		{
		    while($row = $res_brgy_mun->FetchRow())
		    {
		    	$res_brgy = $row['brgy_name'];
		    	$res_mun  = $row['mun_name'];
		    	$res_basic = $row['m_residence_basic'];
		   	}
		}
	}
	else {
		$res_basic = $addressInfo_strt;
		$res_brgy = $addressInfo_brgy;
    	$res_mun  = $addressInfo_city;
	}
		$np = "NOT PROVIDED";
		if ($res_brgy == $np) 
			{
				$res_brgy = "";
			}
			else
			{
				$res_brgy = $res_brgy .", ";
			}

		if ($res_basic == $np) 
			{
				$res_basic = "";
			}
			else
			{
				$res_basic = $res_basic .", ";
			}

	$blnk = " ";
	$d_administer_place = "DAVAO CITY";
	$dd_administer_place = "DAVAO CITY, PHILIPPINES";
    // END JEFF 07-15-17

	#jamen
	// $brg_sql = "SELECT brgy_name FROM seg_barangays WHERE brgy_nr=".$addressDataArr = array('brg'=>$address[0]);
	// $brg_result = $db->Execute($brg_sql);
	// $brg_res = $brg_result->FetchRow();
 	
	//$addressInfo = $addressObj->getAddressInfos($addressDataArr);
	//$address = explode(',', $addressInfo['address']);
	//$address2 = trim($address[0]).', '.trim($address[1]);


	$brg_r = $addressObj->getBarangayInfo($address[0]);
	$mun_r = $addressObj->getMunCityInfo($address[1]);
	$address2 = $brg_r['brgy_name'].', '.$mun_r['mun_name'];

	#child place birth info
	$country = $addressObj->getCountryInfo($child_birth_country);
	$country = $country['country_name'];
	$province = $addressObj->getProvinceInfo($child_birth_pro);
	$province = $province['prov_name'];
	$muncity = $addressObj->getMunCityInfo($child_birth_mun_cty);
	$muncity = $muncity['mun_name'];
	#end

	#filling place default to current place
	$fcountry = $addressObj->getCountryInfo($country_lcro_cert);
	$fcountry = $fcountry['country_name'];
	$fprovince = $addressObj->getProvinceInfo($province_lcro_cert);
	$fprovince = $fprovince['prov_name'];
	$fmuncity = $addressObj->getMunCityInfo($city_mun_lcro_cert);
	$fmuncity = $fmuncity['mun_name'];

	#Added by Christian 03-05-20
	$affiant_mname_exist = $affiant_mname == '-' ? '' : $affiant_mname;
	$affiant_fullName = $affiant_fname." ".$affiant_mname_exist." ".$affiant_lname;
	$affiant_fullName = preg_replace('/\s+/', ' ',$affiant_fullName);
	#end Christian 03-05-20

	$data[$index]['affiant_fname'] = "";
	$data[$index]['affiant_lname'] = "";
	// $data[$index]['affiant_mname'] = $affiant_fname." ".$affiant_mname." ".$affiant_lname; Commented by Christian 03-05-20
	$data[$index]['affiant_mname'] = $affiant_fullName; #Added by Christian 03-05-20
	$data[$index]['affiant_status'] = $affiant_status;
	$data[$index]['affiant_age'] = $affiant_age;
	$data[$index]['affiant_citizenship'] = strtoupper($affiant_citizenship);
	// $data[$index]['father_surename'] = $father_surename;
	$data[$index]['father_surename'] = strtoupper($cert_res['f_name_last']);
	// $data[$index]['child_birth_date'] = date('m-d-Y',strtotime($child_birth_date));
	$data[$index]['child_birth_date'] = date('d-M-Y',strtotime($child_birth_date));
	$data[$index]['affiant_address'] = $res_basic."".$res_mun; #modified by jeff for responding to NOT PROVIDED #removed brgy by carriane 05/08/18 
	$data[$index]['child_birth_country'] = trim(strtoupper($country));
	$data[$index]['child_birth_mun_cty'] = trim($muncity);
	// $data[$index]['child_birth_pro'] = trim($province);
	$data[$index]['child_birth_pro'] = $blnk;
	// $data[$index]['child_birth_reg_num'] = (!empty($child_birth_reg_num)) ? $child_birth_reg_num : 'N/A';
	// $data[$index]['child_birth_reg_date'] =  date('m-d-Y',strtotime($child_birth_reg_date));
	// $data[$index]['child_birth_reg_date'] =  date('M d, Y',strtotime($child_birth_reg_date));
	// $data[$index]['paternity_reg_num'] = (!empty($paternity_reg_num)) ? $paternity_reg_num : 'N/A';
	$data[$index]['child_birth_reg_num'] = $blnk; 	#modified by jeff for request of user
	$data[$index]['child_birth_reg_date'] =  $blnk; 	#modified by jeff for request of user
	$data[$index]['paternity_reg_num'] = $blnk; 		#modified by jeff for request of user
	// $data[$index]['paternity_reg_date'] = date('m-d-Y',strtotime($paternity_reg_date));
	$data[$index]['paternity_reg_date'] = $blnk; 		#modified by jeff for request of user
	$data[$index]['year_suffex'] = substr(date('Y'), 2);
	$data[$index]['month_suffex'] = date('M');
	$data[$index]['day_suffex'] = date('dS');
	// $data[$index]['month_suffex'] = date('M',strtotime($administer_date));
	// $data[$index]['month_suffex'] = date('M',strtotime($administer_date));
	// $data[$index]['day_suffex'] = date('dS',strtotime($administer_date));
	$data[$index]['month_suffex_two'] = $blnk;		#modified by jeff for request of user
	$data[$index]['day_suffex_two'] = $paternity_reg_num;  #modified by jeff for request of user | use as ID number
	// $data[$index]['month_suffex'] = " ";
	// $data[$index]['day_suffex'] = " ";
	/*$data[$index]['place_suffex'] = $hospital_name;*/
	// $data[$index]['administer_place'] = $administer_place;
	$data[$index]['administer_place'] = $d_administer_place;
	// $data[$index]['administer_place'] = " ";administer_place2
	$data[$index]['administer_place_two'] = $administer_place;	#modified by jeff for request of user
	// $data[$index]['administer_date'] = date('m-d-Y',strtotime($administer_date));
	// $data[$index]['administer_date'] = date('M d, Y',strtotime($administer_date));
	$data[$index]['administer_date'] = $blnk;			#modified by jeff for request of user
	$data[$index]['country_lcro_cert'] = trim(strtoupper($fcountry));
	// $data[$index]['province_lcro_cert'] = trim($fprovince);
	$data[$index]['province_lcro_cert'] = $blnk;		#modified by jeff for requesrt of user
	$data[$index]['city_mun_lcro_cert'] = trim($fmuncity);
	// $data[$index]['place_ausf_cert'] = trim($place_ausf_cert);
	$data[$index]['place_ausf_cert'] = $dd_administer_place;
	$affiant_printed_name = $affiant_fname.' '.strtoupper(substr($affiant_mname, 0, 1)).'.'.' '.$affiant_lname;
	$affiant_printed_name = $affiant_mname == '-' ? $affiant_fullName : $affiant_printed_name;  #Added by Christian 03-05-20
	$data[$index]['affiant_signature'] = $affiant_printed_name;
	// $data[$index]['paternity_reg_place'] = $paternity_reg_place;
	// $data[$index]['personell_signature'] = $per_res['personell_name'];
	// $data[$index]['personell_title'] = $per_res['signatory_position'];
	$data[$index]['paternity_reg_place'] = $blnk;		#modified by jeff for request of user
	$data[$index]['personell_signature'] = $blnk;		#modified by jeff for request of user
	$data[$index]['personell_title'] = $blnk;			#modified by jeff for request of user
	
	$is_other = $info['is_other'];
	$is_self = $info['is_self'];

	$new_child_fullname = $person_res['name_first']." ".$person_res['name_middle']." ".$person_res['name_last'];

	if($is_other != 0) {
		$params = array('is_other' => '/');
		$params['is_self'] = '';
		// $data[$index]['child_fullname'] = $child_fullname;
		$data[$index]['child_fullname'] = strtoupper($new_child_fullname);
		$data[$index]['child_relationship'] = strtoupper($rel['name']);
	}else if($is_self != 0) {
		$params = array('is_self' => '/');
		$params['is_other'] = '';
		$data[$index]['child_fullname'] = 'N/A';
		$data[$index]['child_relationship'] = 'N/A';
	}
	
	showReport('affidavit_father_username', $params, $data,'PDF');

 ?>