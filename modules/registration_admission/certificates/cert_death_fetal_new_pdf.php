<?php

include("roots.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');

interface ReportDataSource {
    public function toArray();
}


class ReportGenerator {

    protected $_dataSource;

    public function __construct(&$dataSource) {
        $this->_dataSource = $dataSource;
    }

}

/**
 * see if the java extension was loaded.
 */
function checkJavaExtension()
{
    if(!extension_loaded('java'))
    {
        $sapi_type = php_sapi_name();
        $port = (isset($_SERVER['SERVER_PORT']) && (($_SERVER['SERVER_PORT'])>1024)) ? $_SERVER['SERVER_PORT'] : '8080';
        if ($sapi_type == "cgi" || $sapi_type == "cgi-fcgi" || $sapi_type == "cli")
        {
            require_once(java_include);
            return true;
        }
        else
        {
            if(!(@require_once(java_include)))
            {
                require_once(java_include);
            }
        }
    }
    if(!function_exists("java_get_server_name"))
    {
        return "The loaded java extension is not the PHP/Java Bridge";
    }

    return true;
}

# created by pet for fetal death certificate, patterned from birth & death certificates; june 11, 2008
function seg_ucwords($str) {
	$words = preg_split("/([\s,.-]+)/", mb_strtolower($str), -1, PREG_SPLIT_DELIM_CAPTURE);
	$words = @array_map('ucwords',$words);
	return implode($words);
}

/**
 * convert a php value to a java one...
 * @param string $value
 * @param string $className
 * @returns boolean success
 */
function convertValue($value, $className){
    // if we are a string, just use the normal conversion
    // methods from the java extension...
    try{
        if ($className == 'java.lang.String'){
            $temp = new Java('java.lang.String', $value);
            return $temp;
        }else if ($className == 'java.lang.Boolean' ||
                    $className == 'java.lang.Integer' ||
                    $className == 'java.lang.Long' ||
                    $className == 'java.lang.Short' ||
                    $className == 'java.lang.Double' ||
                    $className == 'java.math.BigDecimal')
        {
            $temp = new Java($className, $value);
            return $temp;
        }else if ($className == 'java.sql.Timestamp' ||
            $className == 'java.sql.Time')
        {
            $temp = new Java($className);
            $javaObject = $temp->valueOf($value);
            return $javaObject;
        }else if ($className == "java.util.Date"){
            #$temp = new Java('java.text.DateFormat');
            $temp = new Java('java.text.SimpleDateFormat("MM/dd/yyyy")');
            $javaObject = $temp->parse($value);
            return $javaObject;
        }
    }catch (Exception $err){
        echo (  'unable to convert value, ' . $value .
                ' could not be converted to ' . $className);
        return false;
    }

    echo (  'unable to convert value, class name '.$className.
    ' not recognised');
    return false;
}

$x = checkJavaExtension();

$report = 'FetalDeathCertificateNew';
$compileManager = new JavaClass("net.sf.jasperreports.engine.JasperCompileManager");
$report = $compileManager->compileReport(realpath(java_resource.$report.'.jrxml'));
java_set_file_encoding("ISO-8859-1");
$fillManager = new JavaClass("net.sf.jasperreports.engine.JasperFillManager");

$params = new Java("java.util.HashMap");

$start = microtime(true);

$db->SetFetchMode(ADODB_FETCH_ASSOC);

if (isset($_GET['id']) && $_GET['id']){
	$pid = $_GET['id'];
}

include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person($pid);

require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

include_once($root_path.'include/care_api_classes/class_address.php');
$address_country = new Address('country');
$address_brgy = new Address('barangay');

require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
$objInfo = new Hospital_Admin();

if ($row = $objInfo->getAllHospitalInfo()) {
		$row['hosp_agency'] = strtoupper($row['hosp_agency']);
		$row['hosp_name']   = strtoupper($row['hosp_name']);
}
else {
		$row['hosp_country'] = "Republic of the Philippines";
		$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
		$row['hosp_name']    = "BUKIDNON PROVINCIAL HOSPITAL - MALAYBALAY";
		$row['hosp_addr1']   = "Malaybalay, Bukidnon";
		$row['mun_name']     = "Malaybalay";
		$row['prov_name']     = "Bukidnon";
		$row['region_name']     = "Region X";
}

if ($pid){
	if (!($basicInfo=$person_obj->BasicDataArray($pid))){
		echo '<em class="warn"> Sorry, the page cannot be displayed!</em>';
		exit();
	}
	extract($basicInfo);
	$brgy_info = $address_brgy->getAddressInfo($brgy_nr,TRUE);
	if($brgy_info){
		$brgy_row = $brgy_info->FetchRow();
	}
}else{
	echo '<em class="warn">Sorry, the page cannot be displayed! <br> Invalid PID!</em>';
	exit();
}

$birthYear = date("Y",strtotime($date_birth));
$birthMonth = date("F",strtotime($date_birth));
$birthDay = date("d",strtotime($date_birth));

include_once($root_path.'include/care_api_classes/class_cert_death_fetal.php');
$obj_fetalDeathCert = new FetalDeathCertificate($pid);

$fetalDeathCertInfo = $obj_fetalDeathCert->getFetalDeathCertRecord($pid);

if ($fetalDeathCertInfo){
	extract($fetalDeathCertInfo);
	$delivery_method_tmp= substr(trim($fetalDeathCertInfo['delivery_method']),0,1);
	$delivery_method_info = substr(trim($fetalDeathCertInfo['delivery_method']),4);
	$attendant_type_tmp = substr(trim($fetalDeathCertInfo['attendant_type']),0,1);
	$attendant_type_others = substr(trim($fetalDeathCertInfo['attendant_type']),4);
	$death_occurrence = substr(trim($fetalDeathCertInfo['death_occurrence']),0,1);
	$corpse_disposal_tmp= substr(trim($fetalDeathCertInfo['corpse_disposal']),0,1);
	$corpse_disposal_others = substr(trim($fetalDeathCertInfo['corpse_disposal']),4);
	$is_autopsy = substr(trim($fetalDeathCertInfo['is_autopsy']),0,1);
	$tmp_death_cause = unserialize($fetalDeathCertInfo['death_cause']);

	$data[0]['death_place_mun'] = "";
	$data[0]['registry_nr'] = "";
	$data[0]['name_first'] = "";
	$data[0]['name_middle'] = "";
	$data[0]['name_last'] = "";
	$data[0]['sex_m'] = "";
	$data[0]['sex_f'] = "";
	$data[0]['sex_u'] = "";
	$data[0]['delivery_date'] = "";
	$data[0]['birth_place'] = "";
	$data[0]['birth_type_s'] = "";
	$data[0]['birth_type_twin'] = "";
	$data[0]['birth_type_tr'] = "";
	$data[0]['birth_rank_f'] = "";
	$data[0]['birth_rank_s'] = "";
	$data[0]['birth_rank_o'] = "";
	$data[0]['birth_rank_others'] = "";
	$data[0]['delivery_method_n'] = "";
	$data[0]['delivery_method_o'] = "";
	$data[0]['delivery_method_info'] = "";
	$data[0]['birth_order'] = "";
	$data[0]['birth_weight'] = "";
	$data[0]['m_name_first'] = "";
	$data[0]['m_name_middle'] = "";
	$data[0]['m_name_last'] = "";
	$data[0]['m_citizenship'] = "";
	$data[0]['m_religion'] = "";
	$data[0]['m_occupation'] = "";
	$data[0]['m_age'] = "";
	$data[0]['m_total_alive'] = "";
	$data[0]['m_still_living'] = "";
	$data[0]['m_now_dead'] = "";
	$data[0]['m_address_long'] = "";
	$data[0]['m_address_shorter'] = "";
	$data[0]['no_f_name'] = "";
	$data[0]['f_name_first'] = "";
	$data[0]['f_name_middle'] = "";
	$data[0]['f_name_last'] = "";
	$data[0]['f_citizenship'] = "";
	$data[0]['f_religion'] = "";
	$data[0]['f_occupation'] = "";
	$data[0]['f_age'] = "";
	$data[0]['parent_marriage_date'] = "";
	$data[0]['parent_marriage_place'] = "";
	$data[0]['marriage_date'] = "";
	$data[0]['marriage_municity'] = "";
	$data[0]['marriage_prov'] = "";
	$data[0]['marriage_country'] = "";
	$data[0]['death_occurrence_b'] = "";
	$data[0]['death_occurrence_d'] = "";
	$data[0]['death_occurrence_u'] = "";
	$data[0]['pregnancy_length'] = "";
	$data[0]['attendant_type_1'] = "";
	$data[0]['attendant_type_2'] = "";
	$data[0]['attendant_type_3'] = "";
	$data[0]['attendant_type_4'] = "";
	$data[0]['attendant_type_5'] = "";
	$data[0]['attendant_type_6'] = "";
	$data[0]['attendant_type_others'] = "";
	$data[0]['death_time'] = "";
	$data[0]['have_attend'] = "";
	$data[0]['havenot_attend'] = "";
	$data[0]['doctor_name'] = "";
	$data[0]['attendant_title'] = "";
	$data[0]['attendant_address'] = "";
	$data[0]['attendant_date_sign'] = "";
	$data[0]['burial_permit'] = "";
	$data[0]['burial_date_issued'] = "";
	$data[0]['corpse_disposal_tmp1'] = "";
	$data[0]['corpse_disposal_tmp2'] = "";
	$data[0]['corpse_disposal_tmp3'] = "";
	$data[0]['corpse_disposal_others'] = "";
	$data[0]['is_autopsy_yes'] = "";
	$data[0]['is_autopsy_no'] = "";
	$data[0]['informant_name'] = "";
	$data[0]['informant_relation'] = "";
	$data[0]['informant_address'] = "";
	$data[0]['informant_date_sign'] = "";
	$data[0]['encoder_name'] = "";
	$data[0]['encoder_title'] = "";
	$data[0]['encoder_date_sign'] = "";

	### H E A D E R ###
	$data[0]['death_place_mun'] = $row['mun_name'];
	$data[0]['registry_nr'] = $registry_nr;

	### F E T U S ###

	# 1. NAME OF FETUS
	$data[0]['name_first'] = mb_strtoupper($name_first);
	$data[0]['name_middle'] = mb_strtoupper($name_middle);
	$data[0]['name_last'] = mb_strtoupper($name_last);

	# 2. SEX
	if ($sex=='m')
		$data[0]['sex_m'] = "MALE";
	if ($sex=='f')
		$data[0]['sex_f'] = "FEMALE";
	if ($sex=='u')
		$data[0]['sex_u'] = "UNDETERMINED";

	# 3. DATE OF DELIVERY
	$data[0]['delivery_date'] = $birthDay."          ".mb_strtoupper($birthMonth)."     ".$birthYear;

	# 4. PLACE OF DELIVERY
	if ($birth_place_basic)
		$birth_place = mb_strtoupper(trim($birth_place_basic)).", ";
	else
		$birth_place = trim($row['hosp_name']);

	$data[0]['birth_place'] = $birth_place." ".mb_strtoupper(trim($birth_place_mun));

	# 5a. TYPE OF DELIVERY
	if ($birth_type=="1")
		$data[0]['birth_type_s'] = "SINGLE";
	if ($birth_type=="2")
		$data[0]['birth_type_twin'] = "TWIN";
	if (($birth_type!="")&&($birth_type!="1")&&($birth_type!="2"))
		$data[0]['birth_type_tr'] = "TRIPLET";

	# b. IF MULTIPLE DELIVERY, FETUS WAS
	if ($birth_rank == 'first')
		$data[0]['birth_rank_f'] = "FIRST";
	if ($birth_rank == 'second')
		$data[0]['birth_rank_s'] = "SECOND";
	else{
		$data[0]['birth_rank_o'] = "";

		$data[0]['birth_rank_others'] = mb_strtoupper($birth_rank);
	}

	### M O T H E R ###

	# c. METHOD OF DELIVERY
	if ($delivery_method == 1)
		$data[0]['delivery_method_n'] = "NORMAL SPONTANEOUS VERTEX ";
	else{
		$data[0]['delivery_method_o'] = "";

		$data[0]['delivery_method_info'] = mb_strtoupper($delivery_method_info);
	}

	# d. BIRTH ORDER
	$data[0]['birth_order'] = mb_strtoupper($birth_order);

	# e. WEIGHT OF FETUS
	$data[0]['birth_weight'] = $birth_weight;

	# 6. MAIDEN NAME
	$data[0]['m_name_first'] = mb_strtoupper($m_name_first);
	$data[0]['m_name_middle'] = mb_strtoupper($m_name_middle);
	$data[0]['m_name_last'] = mb_strtoupper($m_name_last);

	# 7. CITIZENSHIP
	if ($m_citizenship=='PH')
		$m_citizenship = "FILIPINO";

	$data[0]['m_citizenship'] = strtoupper($m_citizenship);

	# 8. RELIGION
	$religion_obj = $obj_fetalDeathCert->getMReligion($m_religion);

	if ($religion_obj['religion_name']=="Not Applicable")
		$religion_obj['religion_name'] = "N/A";

	$data[0]['m_religion'] = mb_strtoupper($religion_obj['religion_name']);

	# 9. OCCUPATION
	$occupation_obj = $obj_fetalDeathCert->getMOccupation($m_occupation);
	
	if ($occupation_obj['occupation_name']=="Not Applicable")
		$occupation_obj['occupation_name'] = "N/A";

	$data[0]['m_occupation'] = mb_strtoupper($occupation_obj['occupation_name']);

	# 10. Age at the time of delivery
	$data[0]['m_age'] = $m_age;

	# 11a. Total number of children born alive
	$data[0]['m_total_alive'] = $m_total_alive;

	# b. No. of children still living
	$data[0]['m_still_living'] = $m_still_living;

	# c. No. of children born alive but are now dead
	$data[0]['m_now_dead'] = $m_now_dead;

	# 12. RESIDENCE
	$m_address = $m_residence_basic;

	$brgy = $address_country->getMunicityByBrgy($m_residence_brgy);
	$mun = $address_country->getProvinceByBrgy($m_residence_mun);
	$prov = $address_country->getProvinceInfo($m_residence_prov);
	//$country = $address_country->getCountryInfo($m_residence_country);

	if ($m_address){
		if ($brgy_name!="NOT PROVIDED")
			$street_name = trim($m_address).", ";
		else
			$street_name = trim($m_address).", ";
	}else
		$street_name = "";


	if ((!($brgy['brgy_name'])) || ($brgy['brgy_name']=="NOT PROVIDED"))
		$brgy_name = "";
	else
		$brgy_name  = trim($brgy['brgy_name']).", ";

	if ((!($mun['mun_name'])) || ($mun['mun_name']=="NOT PROVIDED"))
		$mun_name = "";
	else{
		if ($brgy_name)
			$mun_name = trim($mun['mun_name']);
	}

	if ((!($prov['prov_name'])) || ($prov['prov_name']=="NOT PROVIDED"))
		$prov_name = "";
	else
		$prov_name = trim($prov['prov_name']);

	if(stristr(trim($mun_name), 'city') === FALSE){
		if ((!empty($mun_name))&&(!empty($prov_name))){
			if ($prov_name!="NOT PROVIDED")
				$prov_name = ", ".trim($prov_name);
			else
				$prov_name = "";
		}else{
			$prov_name = "";
		}
	}else
		$prov_name = " ";

	#COUNTRY
	
	if($m_residence_country) {
		$country_name = ", ". $m_residence_country;
	}else{
		$country_name = ", PHILIPPINES";
	}

	$m_address = $street_name.$brgy_name.$mun_name.$prov_name.$country_name;

	
	if(strlen($m_address) >= 55)
		$data[0]['m_address_shorter'] = mb_strtoupper($m_address);
	else
		$data[0]['m_address_long'] = mb_strtoupper($m_address);
	

	### F A T H E R ###

	# 13. NAME
	if ((($f_name_first=="N/A") || ($f_name_first=="n/a"))&&(($f_name_middle=="N/A") || ($f_name_middle=="n/a"))&&(($f_name_last=="N/A") || ($f_name_last=="n/a"))){
		$data[0]['no_f_name'] = "N/A";
	}else{
		$data[0]['f_name_first'] = mb_strtoupper($f_name_first);
		$data[0]['f_name_middle'] = mb_strtoupper($f_name_middle);
		$data[0]['f_name_last'] = mb_strtoupper($f_name_last);
	}

	# 14. CITIZENSHIP
	if ($f_citizenship=='PH')
		$f_citizenship = "FILIPINO";

	if (($f_citizenship=="n/a")||($f_citizenship=="N/A")||((($f_name_first=="N/A") || ($f_name_first=="n/a"))&&(($f_name_middle=="N/A") || ($f_name_middle=="n/a"))&&(($f_name_last=="N/A") || ($f_name_last=="n/a"))))
		$f_citizenship = "";

	$data[0]['f_citizenship'] = strtoupper($f_citizenship);

	# 15. RELIGION
	$religion_obj = $obj_fetalDeathCert->getFReligion($f_religion);
	
	if ($religion_obj['religion_name']=="Not Applicable")
		$religion_obj['religion_name'] = "N/A";

	$data[0]['f_religion'] = mb_strtoupper($religion_obj['religion_name']);

	# 16. OCCUPATION
	$occupation_obj = $obj_fetalDeathCert->getFOccupation($f_occupation);
	if ($occupation_obj['occupation_name']=="Not Applicable")
		$occupation_obj['occupation_name'] = "N/A";

	$data[0]['f_occupation'] = mb_strtoupper($occupation_obj['occupation_name']);

	# 17. Age at the time of this delivery
	if ($f_age==0)
		$f_age = "";

	$data[0]['f_age'] = $f_age;

	# 18. DATE AND PLACE OF MARRIAGE OF PARENTS
	
	$mun = $address_country->getProvinceByBrgy($p_residence_mun);
	$prov = $address_country->getProvinceInfo($p_residence_prov);
	//$country = $address_country->getCountryInfo($p_residence_country);

	if (($parent_marriage_date!='0000-00-00') && (!empty($parent_marriage_date))){
		#if ($parent_marriage_date){
		if (($parent_marriage_place)||($parent_marriage_place!='N/A')){
			$data[0]['marriage_date'] = mb_strtoupper(date("F d, Y",strtotime($parent_marriage_date)));
	
			if($p_residence_mun == '' && $p_residence_prov == '') {
				if(strlen($parent_marriage_place) > 30) {
					$data[0]['marriage_municity_short'] = mb_strtoupper($parent_marriage_place);
				}else {
					$data[0]['marriage_municity'] = mb_strtoupper($parent_marriage_place);
				}
				$data[0]['marriage_country'] = 'PHILIPPINES';

			}else {
				$data[0]['marriage_municity'] = mb_strtoupper($mun['mun_name']);
				$data[0]['marriage_prov'] = mb_strtoupper($prov['prov_name']);
				$data[0]['marriage_country'] = mb_strtoupper($p_residence_country);
			}
			
		}
		else
			$data[0]['marriage_date'] = date("F d, Y",strtotime($parent_marriage_date));

	}else{
		$data[0]['marriage_date'] = " ";
		$data[0]['marriage_municity'] = " ";
		$data[0]['marriage_prov'] = " ";
		$data[0]['marriage_country'] = " ";
	}

	

	# 20. FETUS DIEAD
	if ($death_occurrence=="1")
		$data[0]['death_occurrence_b'] = "X";
	if ($death_occurrence=="2")
		$data[0]['death_occurrence_d'] = "X";
	if ($death_occurrence=="3")
		$data[0]['death_occurrence_u'] = "X";

	# 21. LENGTH OF PREGNANCY
	$data[0]['pregnancy_length'] = $pregnancy_length;

	# 22a. ATTENDANT
	if ($attendant_type==1)
		$data[0]['attendant_type_1'] = "PHYSICIAN";
	else if ($attendant_type==2)
		$data[0]['attendant_type_2'] = "NURSE";
	else if ($attendant_type==3)
		$data[0]['attendant_type_3'] = "MIDWIFE";
	else if ($attendant_type==4)
		$data[0]['attendant_type_4'] = "HILOT (TRADITIONAL MIDWIFE)";
	else if ($attendant_type==5){
		$data[0]['attendant_type_5'] = "";
		//$attendant_type_others = "other";
		$data[0]['attendant_type_others'] = mb_strtoupper($attendant_type_others);
	}
 	else if ($attendant_type==6)
		$data[0]['attendant_type_6'] = "NONE";

	# 22b. CERTIFICATION
	
	if ($attend == 1) {
		$data[0]['have_attend'] = 'X';
	}else if ($attend == 2){
		$data[0]['havenot_attend'] = 'X';
	} 

	if ($death_time!="")
		$death_time = convert24HourTo12HourLocal($death_time);
	else
		$death_time = '';
	if (($attendant_date_sign!='0000-00-00') && ($attendant_date_sign!="")){
		$tempYear = date("Y",strtotime($attendant_date_sign));
		$tempMonth = date("F",strtotime($attendant_date_sign));
		$tempDay = date("d",strtotime($attendant_date_sign));

		$attendant_date_sign =$tempDay." ".$tempMonth." ".$tempYear;
	}else{
		$attendant_date_sign = '';
	}

	$data[0]['death_time'] = $death_time;

	$doctor = $pers_obj->get_Person_name($attendant_name);

	$middleInitial = "";
	if (trim($doctor['name_middle'])!=""){
		$thisMI=split(" ",$doctor['name_middle']);
		foreach($thisMI as $value){
			if (!trim($value)=="")
			$middleInitial .= $value[0];
		}
		if (trim($middleInitial)!="")
		$middleInitial .= ". ";
	}
	$doctor_name = $doctor["name_first"]." ".$doctor["name_2"]." ".$middleInitial.$doctor["name_last"];

	if (!empty($attendant_name))
		$doctor_name = mb_strtoupper($doctor_name).", MD";

	if(strlen($doctor_name) > 28 && strlen($doctor_name) < 31)
		$data[0]['doctor_name_short'] = $doctor_name;
	elseif(strlen($doctor_name) > 30)
		$data[0]['doctor_name_short_2'] = $doctor_name;
	else
		$data[0]['doctor_name'] = $doctor_name;

	$data[0]['attendant_title'] = mb_strtoupper($attendant_title);

	$attendant_address = substr_replace(trim($attendant_address)," ",20,1);
	$data[0]['attendant_address'] = mb_strtoupper(seg_ucwords($attendant_address));

	$data[0]['attendant_date_sign'] = mb_strtoupper($attendant_date_sign);

	# 23. CORPSE DISPOSAL
	if ($corpse_disposal_tmp=='1')
		$data[0]['corpse_disposal_tmp1'] = "BURIAL";
	if ($corpse_disposal_tmp=='2')
		$data[0]['corpse_disposal_tmp2'] = "CREMATION";
	if ($corpse_disposal_tmp=='3'){
		$data[0]['corpse_disposal_tmp3'] = "";

		//$corpse_disposal_others = "0";
		$data[0]['corpse_disposal_others'] = mb_strtoupper($corpse_disposal_others);
	}

	# 24 BURIAL/CREMATION PERMIT
	$data[0]['burial_permit'] = $burial_permit;
	if ($burial_date_issued == "0000-00-00")
		$burial_date_issued = "";
	$data[0]['burial_date_issued'] = $burial_date_issued;

	# 25. AUTOPSY
	if ($is_autopsy=='1')
		$data[0]['is_autopsy_yes'] = "YES";
	else if ($is_autopsy=='2')
		$data[0]['is_autopsy_no'] = "NO";

	if($cemetery_name_address != NULL){
		$data[0]['cemetery_address'] = mb_strtoupper($cemetery_name_address);
	} else {
		$data[0]['cemetery_address'] = '';
	}
	
	# 27. INFORMANT
	if (($informant_date_sign!='0000-00-00') && ($informant_date_sign!="")){
		$tempYear = date("Y",strtotime($informant_date_sign));
		$tempMonth = date("F",strtotime($informant_date_sign));
		$tempDay = date("d",strtotime($informant_date_sign));

		$informant_date_sign =$tempDay." ".$tempMonth." ".$tempYear;
	}else{
		$informant_date_sign = '';
	}

	$data[0]['informant_name'] = mb_strtoupper($informant_name);
	$data[0]['informant_relation'] = mb_strtoupper($informant_relation);

	if(strlen($informant_address) > 60 && strlen($informant_address) < 80)
		$data[0]['informant_address_short'] = mb_strtoupper($informant_address);
	elseif(strlen($informant_address) > 79)
		$data[0]['informant_address_shorter'] = mb_strtoupper($informant_address);
	else $data[0]['informant_address'] = mb_strtoupper($informant_address);

	$data[0]['informant_date_sign'] = mb_strtoupper($informant_date_sign);

	# 28. PREPARED BY
	if (($encoder_date_sign!='0000-00-00') && ($encoder_date_sign!="")){
		$tempYear = date("Y",strtotime($encoder_date_sign));
		$tempMonth = date("F",strtotime($encoder_date_sign));
		$tempDay = date("d",strtotime($encoder_date_sign));

		$encoder_date_sign =$tempDay." ".$tempMonth." ".$tempYear;
	}else{
		$encoder_date_sign = '';
	}

	$data[0]['encoder_name'] =  mb_strtoupper($encoder_name);
	$data[0]['encoder_title'] = mb_strtoupper($encoder_title);
	$data[0]['encoder_date_sign'] = mb_strtoupper($encoder_date_sign);
}

$jCollection = new Java("java.util.ArrayList");
foreach ($data as $i => $row) {
    $jMap = new Java('java.util.HashMap');
    foreach ( $row as $field => $value ) {
        $jMap->put($field, $value);
    }
    $jCollection->add($jMap);
}

$jMapCollectionDataSource = new Java("net.sf.jasperreports.engine.data.JRMapCollectionDataSource", $jCollection);
$jasperPrint = $fillManager->fillReport($report, $params, $jMapCollectionDataSource);

$end = microtime(true);

$outputPath  = tempnam(java_tmp, '');
chmod($outputPath, 0777);

$exportManager = new JavaClass("net.sf.jasperreports.engine.JasperExportManager");
$exportManager->exportReportToPdfFile($jasperPrint, $outputPath);


header("Content-type: application/pdf;");
readfile($outputPath);

unlink($outputPath);

?>