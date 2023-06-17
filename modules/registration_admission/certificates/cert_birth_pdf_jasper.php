<?php

require_once('roots.php');
require_once($root_path.'include/inc_environment_global.php');


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

function seg_ucwords($str, $capitalizeAfterdash=0) {
    if($capitalizeAfterdash)
        $words = preg_split("/([\s,.-]+)/", mb_strtolower($str), -1, PREG_SPLIT_DELIM_CAPTURE);
    else
        $words = preg_split("/([\s(]+)/", mb_strtolower($str), -1, PREG_SPLIT_DELIM_CAPTURE);

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

function convertAddress($re,$address,$compaddress){
    $comma = '';
    if(!empty($compaddress))
        $comma = ', ';

    $count = 0;
    preg_match_all($re, strtoupper(trim($address)), $matches, PREG_OFFSET_CAPTURE);

    if(count($matches[0])){
        foreach($matches[0] as $match){
            if(preg_match('/\s/',substr(trim($address), $match[1]-1, 1))){
                $temp = seg_ucwords(trim($address));
                $roman_address = substr_replace($temp,$match[0],$match[1],strlen($match[0]));
                $compaddress = $compaddress.$comma.$roman_address;
                $count = 1;
            }
        }
    }else{
        if(preg_match('#[0-9]#',trim($address))){
            $compaddress = $compaddress.", ".seg_ucwords(trim($address),1);
        }else $compaddress = $compaddress.$comma.seg_ucwords(trim($address));
    }

    return $compaddress;
}

 
$x = checkJavaExtension();
$BackGround ="";
if (isset($_GET['pidJS'])) {
    $BackGround =$_GET['pidJS'];
}
if ($BackGround =="") {
    $report = 'BirthCertificate_WithOutTemplate';
}else{
    $report = 'BirthCertificate_WithTemplate';
    $data[0] = array();
    $baseurl = sprintf(
        "%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_ADDR'],
       # $_SERVER['HTTP_HOST'],
        substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
    );
    $data[0]['image_01'] = $baseurl . "reports/birth001.jpg";
    $data[0]['image_02'] = $baseurl . "reports/birth002.jpg";

}

// $report = 'BirthCertificate';
$compileManager = new JavaClass("net.sf.jasperreports.engine.JasperCompileManager");
$report = $compileManager->compileReport(realpath(java_resource.$report.'.jrxml'));
java_set_file_encoding("ISO-8859-1");
$fillManager = new JavaClass("net.sf.jasperreports.engine.JasperFillManager");

$params = new Java("java.util.HashMap");

$start = microtime(true);

$db->SetFetchMode(ADODB_FETCH_ASSOC);

$pid = $_GET['pid'];

require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
$objInfo = new Hospital_Admin();

require_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person($pid);

require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

require_once($root_path.'include/care_api_classes/class_cert_birth.php');
$obj_birthCert = new BirthCertificate($pid);

require_once($root_path.'include/care_api_classes/class_address.php');
$address_country = new Address();

if ($row = $objInfo->getAllHospitalInfo()) {
        $row['hosp_agency'] = mb_strtoupper($row['hosp_agency']);
        $row['hosp_name']   = mb_strtoupper($row['hosp_name']);
}
else {
        $row['hosp_country'] = "Republic of the Philippines";
        $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
        $row['hosp_name']    = "BUKIDNON PROVINCIAL HOSPITAL - MALAYBALAY";
        $row['hosp_addr1']   = "Malaybalay, Bukidnon";
        $row['mun_name']     = "Malaybalay";
        $row['prov_name']    = "Bukidnon";
        $row['region_name']  = "Region X";
}

$doctor_address = $row['hosp_name']." - ".$row['mun_name'];

if ($pid){
    if (!($basicInfo=$person_obj->getAllInfoArray($pid))){
        echo '<em class="warn"> Sorry, the page cannot be displayed!</em>';
        exit();
    }
    extract($basicInfo);
}

$birthCertInfo = $obj_birthCert->getBirthCertRecord($pid);



if ($birthCertInfo){
    extract($birthCertInfo);
    
    #PAGE 1
    #data
    if(stristr($row['mun_name'], 'city') === FALSE){
        $data[0]['birth_place_prov'] = trim($row['prov_name']);
    }    
    $data[0]['birth_place_mun'] = trim($row['mun_name']);
    $data[0]['registry_nr'] = $register_nr;
    
    #NAME
    $name_first = str_replace(" ","  ",trim($name_first));
    $data[0]['name_first'] = mb_strtoupper(stripslashes($name_first));
    
    $name_middle = str_replace(" ","  ",trim($name_middle));
    $data[0]['name_middle'] = mb_strtoupper(stripslashes($name_middle));
    
    $name_last = str_replace(" ","  ",trim($name_last));
    $data[0]['name_last'] = mb_strtoupper(stripslashes($name_last));
    
    #SEX
    if ($sex=='m')
        $gender = "Male";
    elseif ($sex=='f')
        $gender = "Female";    
        
    $data[0]['sex'] = $gender;
    
    #DATE OF BIRTH
    $arrayMonth = array ("","January","February","March","April","May","June","July","August","September","October","November","December");
    
    $birthDay = date("d",strtotime($date_birth));
    $birthMonth = date("F",strtotime($date_birth));
    $birthYear = date("Y",strtotime($date_birth));
    
    $data[0]['birthDay'] = $birthDay;
    $data[0]['birthMonth'] = $birthMonth;
    $data[0]['birthYear'] = $birthYear;
    
    #PLACE OF BIRTH
    if ($birth_place_basic)
        $birth_place = mb_strtoupper($birth_place_basic).", ";
    else
        $birth_place = "";
    
    $birthplace = mb_strtoupper($birth_place)." ".mb_strtoupper($birth_place_mun);
    $data[0]['birth_place'] = $birthplace;
    
    #TYPE OF BIRTH
    #delete
    #$birth_rank = 'second';
    $data[0]['birth_rank'] = seg_ucwords($birth_rank);
    
    if ($birth_type=="1"){
        $birthtype = "Single";
        $data[0]['birth_rank'] = "n/a";
    }elseif ($birth_type=="2"){
        $birthtype = "Twin";
    }
    elseif ($birth_type=="3"){
        $birthtype = "Triplet";
    }
    elseif ($birth_type=="4"){
        $birthtype = $birth_type_others;
    }
        
    $data[0]['birth_type'] = $birthtype;
    
    #BIRTH ORDER
    $data[0]['birth_order'] = seg_ucwords($birth_order);
    
    #5d. WEIGHT AT BIRTH
    $data[0]['birth_weight'] = $birth_weight;
    
    #mother
    #MAIDEN NAME
    if((strlen(stripslashes(trim($m_name_first))) > 24) || (strlen(stripslashes(trim($m_name_middle))) > 20) || (strlen(stripslashes(trim($m_name_last))) > 22)){
        $data[0]['m_name_first_short'] = mb_strtoupper(stripslashes(trim($m_name_first)));
        $data[0]['m_name_middle_short'] = mb_strtoupper(stripslashes(trim($m_name_middle)));
        $data[0]['m_name_last_short'] = mb_strtoupper(stripslashes(trim($m_name_last)));
    }else{
        $data[0]['m_name_first'] = mb_strtoupper(stripslashes(trim($m_name_first)));
        $data[0]['m_name_middle'] = mb_strtoupper(stripslashes(trim($m_name_middle)));
        $data[0]['m_name_last'] = mb_strtoupper(stripslashes(trim($m_name_last)));
    } 

    #CITIZENSHIP
    $data[0]['m_citizenship'] = seg_ucwords(trim($m_citizenship));
    #RELIGION
    if (($m_religion_name=="Not Applicable")||($m_religion_name=="Not Indicated"))
        $m_religion_name="n/a";
    $data[0]['m_religion'] = $m_religion_name;
    
    $data[0]['m_total_alive'] = $m_total_alive;
    $data[0]['m_still_living'] = $m_still_living;
    $data[0]['m_now_dead'] = $m_now_dead;
    #OCCUPATION (MOTHER)   == 'Other'? $m_occupation_other : $m_occupation_name
    # added by: syboy 09/18/2015
    if ($m_occupation_name == 'Other') {
        $m_occupation_name_mother = $m_occupation_other;
    }else{
        $m_occupation_name_mother = $m_occupation_name;
    }
    #encoder_date_sign
    $data[0]['m_occupation'] = $m_occupation_name_mother;
    #AGE AT THE TIME OF THIS BIRTH (MOTHER)
    $data[0]['m_age'] = $m_age;
    
    #RESIDENCE
    $m_address = trim($m_residence_basic);
    if ($m_residence_brgy)
        $brgy = $address_country->getMunicityByBrgy($m_residence_brgy);
    if ($m_residence_mun)    
        $mun = $address_country->getProvinceByBrgy($m_residence_mun);
    if ($m_residence_prov)
        $prov = $address_country->getProvinceInfo($m_residence_prov);
    if ($m_residence_country)
        $country = $address_country->getCountryInfo($m_residence_country);

   $re = "/\b(?:X?L?(?:X{0,3}(?:IX|IV|V|V?I{1,3})|IX|X{1,3})|XL|L)\b/";
    
    if (!empty($m_address) && !empty($m_residence_brgy)){
        if($brgy['brgy_name'] != ''){
            $convertedAddress = convertAddress($re, $brgy['brgy_name'], $m_address);
            $m_address = $convertedAddress;
        }
    }else{
        if($brgy['brgy_name'] != ''){
            $convertedAddress = convertAddress($re, $brgy['brgy_name'], $m_address);
            $m_address = $convertedAddress;
        }
    }

    if (!empty($m_address) && !empty($m_residence_mun)){
        $convertedAddress = convertAddress($re, $mun['mun_name'], $m_address);
        $m_address = $convertedAddress;
    }else{
        $convertedAddress = convertAddress($re, $mun['mun_name'], $m_address);
        $m_address = $convertedAddress;
    }

    if(stristr($mun['mun_name'], 'city') === FALSE){
        if (!empty($m_address)){
            if(preg_match('#[0-9]#',trim($prov['prov_name'])))
                $m_address = $m_address.", ".seg_ucwords(trim($prov['prov_name']),1);
            else $m_address = $m_address.", ".seg_ucwords(trim($prov['prov_name']));
        }else{
            if(preg_match('#[0-9]#',trim($prov['prov_name'])))
                $m_address = $m_address." ".seg_ucwords(trim($prov['prov_name']),1);
            else $m_address = $m_address." ".seg_ucwords(trim($prov['prov_name']));
        }
    }   

    if (trim($country['country_name']))
        $m_country = ", ".seg_ucwords(trim($country['country_name']));
    
    #edited by VAN 01-24-2013
    if ($m_residence_country=='PH'){
        $data[0]['m_residence_place'] = $m_address.$m_country;
    }
    else{
        #barangay, municipality and province is BLANK
        $m_address2 = trim($m_residence_basic);
        
        if (($m_address2)&&(trim($country['country_name'])))
            $m_country2 = ", ".seg_ucwords(trim($country['country_name'])); 
        else
            $m_country2 = seg_ucwords(trim($country['country_name']));     
                 
        $data[0]['m_residence_place'] = $m_address2.$m_country2;
    }    
    
    #father
    #FATHER'S NAME
    if ((($f_name_first=="N/A") || ($f_name_first=="n/a"))&&(($f_name_middle=="N/A") 
        || ($f_name_middle=="n/a"))&&(($f_name_last=="N/A") || ($f_name_last=="n/a"))){
        $f_name_first  = "n/a";
        $f_name_middle  = "";
        $f_name_last  = "";
    }else{
        if ((stristr($f_name_first,'JR')) || (stristr($f_name_first,'SR'))){
            $father_name = trim($f_name_first);
        }else{
            $father_name = str_replace(",","",trim($f_name_first));
        }

        $f_name_first  = mb_strtoupper(stripslashes(trim($father_name)));
        $f_name_middle = mb_strtoupper(stripslashes(trim($f_name_middle)));
        $f_name_last  =  mb_strtoupper(stripslashes(trim($f_name_last)));
        
    }
    
    $data[0]['f_name_first'] = $f_name_first;
    $data[0]['f_name_middle'] = $f_name_middle;
    $data[0]['f_name_last'] = $f_name_last;
    #CITIZENSHIP (FATHER)
    if (($f_citizenship=="n/a")||($f_citizenship=="N/A"))
        $f_citizenship = "";
        
    $data[0]['f_citizenship'] = seg_ucwords($f_citizenship);
    #RELIGION
    if (($f_religion_name=="Not Applicable")||($f_religion_name=="Not Indicated"))
        $f_religion_name="";
    $data[0]['f_religion'] = $f_religion_name;
    #OCCUPATION (FATHER) 
    // if (($f_occupation_name=="Not Applicable")||($f_occupation_name=="Not Indicated"))
    # edited by: syboy 09/18/2015
    if ($f_occupation_name == 'Other') {
        $f_occupation_name_father = $f_occupation_other;
    }else {
        $f_occupation_name_father = $f_occupation_name;
    }
    $data[0]['f_occupation'] = $f_occupation_name_father;
    // var_dump($f_occupation_name_father); die();
    # ended
    #AGE AT THE TIME OF THIS BIRTH (FATHER)
    if ($f_age==0)
        $f_age = "";
    $data[0]['f_age'] = $f_age;
    
   #RESIDENCE
   $f_address = trim($f_residence_basic);
   
   if (($f_name_first=="N/A") || ($f_name_first=="n/a")){
       $data[0]['f_residence_place'] = ' ';     
   }else{         
       if ($f_address){
        if ($f_residence_brgy)
            $brgy = $address_country->getMunicityByBrgy($f_residence_brgy);
        if ($f_residence_mun)    
            $mun = $address_country->getProvinceByBrgy($f_residence_mun);
        if ($f_residence_prov)
            $prov = $address_country->getProvinceInfo($f_residence_prov);
        if ($f_residence_country)
            $country = $address_country->getCountryInfo($f_residence_country);
        
        if (!empty($f_address) && !empty($f_residence_brgy)){
            if($brgy['brgy_name'] != ''){
                $convertedAddress = convertAddress($re, $brgy['brgy_name'], $f_address);
                $f_address = $convertedAddress;
            }
        }else{
            if($brgy['brgy_name'] != ''){
                $convertedAddress = convertAddress($re, $brgy['brgy_name'], $f_address);
                $f_address = $convertedAddress;
            }
        }
        
        if (!empty($f_address) && !empty($f_residence_mun)){
            $convertedAddress = convertAddress($re, $mun['mun_name'], $f_address);
            $f_address = $convertedAddress;
        }else{
            $convertedAddress = convertAddress($re, $mun['mun_name'], $f_address);
            $f_address = $convertedAddress;
        }
        if(stristr($mun['mun_name'], 'city') === FALSE){
            if (!empty($f_address)){
                if(preg_match('#[0-9]#',trim($prov['prov_name'])))
                    $f_address = $f_address.", ".seg_ucwords(trim($prov['prov_name']),1);
                else $f_address = $f_address.", ".seg_ucwords(trim($prov['prov_name']));
            }else{
                if(preg_match('#[0-9]#',trim($prov['prov_name'])))
                    $f_address = $f_address." ".seg_ucwords(trim($prov['prov_name']),1);
                else $f_address = $f_address." ".seg_ucwords(trim($prov['prov_name']));
            }
        }   
        
        if (trim($country['country_name']))
            $f_country = ", ".seg_ucwords(trim($country['country_name']));
        
        #edited by VAN 01-24-2013
        if ($f_residence_country=='PH')
            $data[0]['f_residence_place'] = $f_address.$f_country;
        else{
            #barangay, municipality and province is BLANK
            $f_address2 = trim($f_residence_basic);
            
            if (($f_address2)&&(trim($country['country_name'])))
                $f_country2 = ", ".seg_ucwords(trim($country['country_name'])); 
            else
                $f_country2 = seg_ucwords(trim($country['country_name']));     
                     
            $data[0]['f_residence_place'] = $f_address2.$f_country2;
        }
        
       }else
          $data[0]['f_residence_place'] = ' ';
   }      
    
    #delete this
    #$parent_marriage_date = "1982-12-12";
    #$parent_marriage_place = 'temp place';
    #$is_married = 1;
    
    if ($is_married=='1'){
        if ($is_tribalwed==0){
            $parent_marriage_day = date("d",strtotime($parent_marriage_date));
            $parent_marriage_month = date("F",strtotime($parent_marriage_date));
            $parent_marriage_year = date("Y",strtotime($parent_marriage_date));
        }else{    
            if(($parent_marriage_date!='0000-00-00')||($parent_marriage_place)){
                if ($parent_marriage_date!='0000-00-00'){
                    $parent_marriage_day = date("d",strtotime($parent_marriage_date));
                    $parent_marriage_month = date("F",strtotime($parent_marriage_date));
                    $parent_marriage_year = date("Y",strtotime($parent_marriage_date));   
                    #if ($parent_marriage_place)
                    #    $parent_marriage_place = "Tribal Wedding - ".$parent_marriage_place;
                    #else
                        #$parent_marriage_place = "Tribal Wedding";    
                }else{
                    $parent_marriage_month = "";    
                    $parent_marriage_day = "";
                    $parent_marriage_year = "";
                    #$parent_marriage_place = "Tribal Wedding";
                }

            }else{
                $parent_marriage_month = "";    
                $parent_marriage_day = "";
                $parent_marriage_year = "";
                #$parent_marriage_place = "Tribal Wedding";
            }
            
            if ($parent_marriage_place)
                $parent_marriage_place = "Tribal Wedding - ".$parent_marriage_place;
            else
                $parent_marriage_place = "Tribal Wedding";
        }        
    }else{
        $parent_marriage_month = "";    
        $parent_marriage_day = "n/a";
        $parent_marriage_year = "";
        $parent_marriage_place = "n/a";    
    }    
    $data[0]['parent_marriage_month'] = $parent_marriage_month;
    $data[0]['parent_marriage_day'] = $parent_marriage_day;
    $data[0]['parent_marriage_year'] = $parent_marriage_year;
    $data[0]['parent_marriage_place'] = $parent_marriage_place;
    
    #ATTENDANT
    $attendant_type = substr(trim($birthCertInfo['attendant_type']),0,1);
    $attendant_type_others = substr(trim($birthCertInfo['attendant_type']),4);
    #$attendant_type_others = "test";
    #uncommented
    if ($attendant_type=='1')
        $data[0]['is_physician'] = "x";
    if ($attendant_type=='2')
        $data[0]['is_nurse'] = "x";
    if ($attendant_type=='3')
        $data[0]['is_midwife'] = "x";
    if ($attendant_type=='4')
        $data[0]['is_hilot'] = "x";
    if ($attendant_type=='5'){
        $data[0]['is_other'] = "x";
        #delete
        #$attendant_type_others = 'other';
        $data[0]['is_other_name'] = $attendant_type_others;
    }
    #CERTIFICATION OF BIRTH
    if ($birth_time!=""){
        $birthtime = date("h:i A",strtotime($birth_time));
        $birth_time_meridian = date("a",strtotime($birth_time));
        #uncomment this
        if ($birth_time_meridian=='am')
            #$birth_time_am = "___";
            $birth_time = ''; 
        else    
            #$birth_time_pm = "___";
            $birth_time_am = '';
    }else{
        $birth_time = '';
        $birth_time_am = '';
    }    
    $data[0]['birth_time'] = $birthtime;
    
    $data[0]['birth_time_am'] = $birth_time_am;
    $data[0]['birth_time_pm'] = $birth_time_pm;
    
    #attendant
    $data[0]['non_resident_status'] = seg_ucwords($non_resident_status);
    if (is_numeric($attendant_name)){
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
        $doctor_name = mb_strtoupper($doctor_name).", MD";
    }else{
        $doctor_name = mb_strtoupper($attendant_name).", MD";
    }

    if(strlen($doctor_name) > 38)
        $data[0]['attendant_name_short'] = stripslashes($doctor_name);
    else
        $data[0]['attendant_name'] = stripslashes($doctor_name);

    $data[0]['attendant_title'] = $attendant_title;
    
    $attendant_address = substr_replace(trim($attendant_address)," ",20,1);
    $attendant_address = seg_ucwords($attendant_address);
    $data[0]['attendant_address'] = $attendant_address;
    
    if (($attendant_date_sign!='0000-00-00') && ($attendant_date_sign!="")){
        $tempYear = date("Y",strtotime($attendant_date_sign));
        $tempMonth = date("F",strtotime($attendant_date_sign));
        $tempDay = date("d",strtotime($attendant_date_sign));

        $attendant_date_sign =$tempDay." ".$tempMonth." ".$tempYear;
    }else{
        $attendant_date_sign = '';
    }
    
    $data[0]['attendant_date_sign'] = $attendant_date_sign;
    
    #INFORMANT
    if(strlen(mb_strtoupper(stripslashes($informant_name))) > 32)
        $data[0]['informant_name_short'] = mb_strtoupper(stripslashes($informant_name));
    else
        $data[0]['informant_name'] = mb_strtoupper(stripslashes($informant_name));

    $data[0]['informant_relation'] = $informant_relation;

    if(strlen($informant_address) > 48)
        $data[0]['informant_address_short'] = trim($informant_address);
    else
        $data[0]['informant_address'] = trim($informant_address);
    
    if (($informant_date_sign!='0000-00-00') && ($informant_date_sign!="")){
        $tempYear = date("Y",strtotime($informant_date_sign));
        $tempMonth = date("F",strtotime($informant_date_sign));
        $tempDay = date("d",strtotime($informant_date_sign));

        $informant_date_sign =$tempDay." ".$tempMonth." ".$tempYear;
    }else{
        $informant_date_sign = '';
    }
    
    $data[0]['informant_date_sign'] = $informant_date_sign;
    
    #PREPARED BY
    $data[0]['encoder_name'] = mb_strtoupper(stripslashes($encoder_name));
    $data[0]['encoder_title'] = $encoder_title;
    
    if (($encoder_date_sign!='0000-00-00') && ($encoder_date_sign!="")){
        $tempYear = date("Y",strtotime($encoder_date_sign));
        $tempMonth = date("F",strtotime($encoder_date_sign));
        $tempDay = date("d",strtotime($encoder_date_sign));

        $encoder_date_sign =$tempDay." ".$tempMonth." ".$tempYear;
    }else{
        $encoder_date_sign = '';
    }
    
    $data[0]['encoder_date_sign'] = $encoder_date_sign;
    
    #RECEIVED BY
    $data[0]['receiver_name'] = mb_strtoupper(stripslashes($receiver_name));
    $data[0]['receiver_title'] = $receiver_title;
    
    if (($receiver_date_sign!='0000-00-00') && ($receiver_date_sign!="")){
        $tempYear = date("Y",strtotime($receiver_date_sign));
        $tempMonth = date("F",strtotime($receiver_date_sign));
        $tempDay = date("d",strtotime($receiver_date_sign));

        $receiver_date_sign =$tempDay." ".$tempMonth." ".$tempYear;
    }else{
        $receiver_date_sign = '';
    }
    
    $data[0]['receiver_date_sign'] = $receiver_date_sign;  
    
    #PAGE2
    #AFFIDAVIT OF ACKNOWLEDGMENT/ADMISSION OF PATERNITY
    #delete this
    #$is_married = 0;
    if ($is_married!=1){
        if (($f_name_first=="n/a")&&($f_name_first=="n/a")&&($f_name_first=="n/a")){
            $father = "";
            $nofather = 1;
        }else{
            if (stristr($f_name_first,",")){
                $f_name_first_new = explode(",",$f_name_first);
                $fname1 = $f_name_first_new[0];
                $fname2 = $f_name_first_new[1];
            }else{
                $fname1 = $f_name_first;
                $fname2 = "";
            }

            if ((stristr($fname2,'JR')) || (stristr($fname2,'SR')))
                $comma = ", ";
            else
                $comma = " ";

            if ($fname2)
                $fname2 = $comma.$fname2;

            if ($f_name_middle)
                $f_name_middle = substr($f_name_middle,0,1).". ";

            if (empty($f_fullname)) {
                $father = $fname1."".$fname2." ".$f_name_middle.$f_name_last;
                if($f_name_middle == "-")
                    $father = $fname1."".$fname2." ".$f_name_last;  #added by Christian 03-10-20
            }else
                $father = $f_fullname;

            $nofather = 0;
        }

        if ($father_mname=="-")
            $father= $father_fname." ".$father_lname;  #added by Christian 03-10-20

        $data[0]['ack_f_fullname'] =mb_strtoupper($father);
        
        $middleInitial = "";
        if (trim($m_name_middle)!=""){
            $thisMI=split(" ",$m_name_middle);
            foreach($thisMI as $value){
                if (!trim($value)=="")
                $middleInitial .= $value[0];
            }
            if (trim($middleInitial)!="")
            $middleInitial .= ". ";
        }
        //this is the orginal code ~~Commented by Christian 11-01-19
        // #$mother = $m_name_first." ".$middleInitial.$m_name_last; 
    
        // $mother = '';
        // $data[0]['ack_m_fullname'] = $nofather ? mb_strtoupper($mother) : '';
        //this is the orginal code end here

        //added by Christian 11-01-19
        $m_name_middle_exist = $m_name_middle == '-' ? '': $m_name_middle;
        $mother = $m_name_first." ".$m_name_middle_exist." ".$m_name_last;
        $mother = preg_replace('/\s+/', ' ',$mother);
        $data[0]['ack_m_fullname'] =mb_strtoupper($mother);
        if (($f_name_first=="n/a")&&($f_name_first=="n/a")&&($f_name_first=="n/a"))
            $data[0]['ack_m_fullname_sworn'] = "";
        else{
            $data[0]['ack_m_fullname_sworn'] =mb_strtoupper($mother);
        }
        //added by Christian 11-01-19 end here
        
        $middleInitial = "";
        if (trim($name_middle)!=""){
            $middleInitial .= $name_middle[0].". ";
        }
        // added by lenar
        else {
            $middleInitial .= "     ";
        }
        // end
        #$baby_name = mb_strtoupper($name_first)." ".mb_strtoupper($name_middle)." ".mb_strtoupper($name_last);
        $baby_name = $name_first." ".$middleInitial.$name_last;    
         
        $data[0]['ack_fullname'] = mb_strtoupper($baby_name);
        $data[0]['ack_date_birth'] = $birthMonth." ".$birthDay.", ".$birthYear;
        $data[0]['ack_birth_place'] = $birthplace;
        
        if ($officer_date_sign!="0000-00-00"){
            $officerYear = date("Y",strtotime($officer_date_sign));
            $officerMonthName = date("F",strtotime($officer_date_sign));
            $officerDay = date("d",strtotime($officer_date_sign));
        }else{
            $officerYear = "";
            $officerMonthName = "";
            $officerDay = "";
        }
        if ($officerDay==0)
            $officerDay = "";

        if ($officerYear==0)
            $officerYear = "";
    
        $data[0]['ack_date_day'] = $officerDay;
        $data[0]['ack_date_month'] = $officerMonthName;
        $data[0]['ack_date_year'] = $officerYear;
        
        
        $data[0]['ack_f_com_tax_nr'] = $f_com_tax_nr;
        
        if (($f_com_tax_date!='0000-00-00') && ($f_com_tax_date!="")){
            $tempYear = date("Y",strtotime($f_com_tax_date));
            $tempMonth = date("F",strtotime($f_com_tax_date));
            $tempDay = date("d",strtotime($f_com_tax_date));

            $f_com_tax_date =$tempDay." ".$tempMonth." ".$tempYear;
        }else{
            $f_com_tax_date = '';
        }
        $data[0]['ack_f_com_tax_date'] = $f_com_tax_date;
        #$data[0]['ack_f_com_tax_place'] = $officer_place_sign;#commented by art 01/28/2014
        $data[0]['ack_f_com_tax_place'] = $late_officer_place_sign;#added by art 01/28/2014
        $data[0]['ack_officer_name'] = mb_strtoupper($officer_name);
        $data[0]['ack_officer_title'] = $officer_title;
        $data[0]['ack_officer_address'] = $officer_address;    

    // commented by Christian 11-04-19 ~reason why not 2nd does not display when no father
    //     if ($nofather == 1) {
    //         $data[0]['ack_f_fullname'] = '';
    //         $data[0]['ack_m_fullname'] = '';
    //         $data[0]['ack_date_birth'] = '';
    //         $data[0]['ack_fullname'] = '';
    //         $data[0]['ack_birth_place'] = '';
    //         $data[0]['ack_date_day'] = '';
    //         $data[0]['ack_date_month'] = '';
    //         $data[0]['ack_f_com_tax_nr'] = '';
    //         $data[0]['ack_f_com_tax_date'] = '';
    //         $data[0]['ack_officer_name'] = '';
    //         $data[0]['ack_officer_title'] = '';
    //         $data[0]['ack_f_com_tax_place'] = '';
    //         $data[0]['ack_officer_address'] = '';

    //     }
    // } 
    // end of commented by Christian 11-04-19 ~reason why not 2nd does not display when no father
     
        //added by Christian 11-04-19
            if ($nofather == 1) {
            $data[0]['ack_f_fullname'] = 'n/a';
        }
        //added by Christian 11-04-19 end
    }    
    
    
    
    #AFFIDAVIT OF LATE/DELAYED REGISTRATION
    if ($is_late_reg=='1'){
        
        $data[0]['late_affiant_name'] = mb_strtoupper($late_affiant_name);
        $data[0]['late_affiant_address'] = $late_affiant_address;
        
        $bdate = $birthDay." ".$birthMonth." ".$birthYear;
        $baby_name = mb_strtoupper($name_first)." ".mb_strtoupper($name_middle)." ".mb_strtoupper($name_last);
        #echo "s = ".$is_subject_person;
        #uncommented
        if ($is_subject_person){
            $data[0]['late_1'] = "x";
            $data[0]['late_2'] = "";
            $data[0]['late_fullname_1'] = $baby_name;
            $data[0]['late_date_birth_1'] = $bdate;
        }else{
            $data[0]['late_2'] = "x";
            $data[0]['late_1'] = "";
            $data[0]['late_fullname_2'] = $baby_name;
            
            if (strlen($birthplace) > 15){
                $pos = 20;
                $data[0]['late_birth_place_1'] = substr($birthplace, 0, $pos);
                $data[0]['late_birth_place_2'] = substr($birthplace, $pos); 
            }else    
                $data[0]['late_birth_place_2'] = $birthplace; 
            
            $data[0]['late_date_birth_2'] = $bdate;
        }    
        
        $data[0]['late_attendant'] = $doctor_name;
        $data[0]['late_attendant_address'] = $doctor_address;
        
        $country = $address_country->getCountryInfo($late_baby_citizenship);
        $data[0]['late_baby_citizenship'] = $country['country_name'];
        #uncommented
        if ($is_married!=1){
            $data[0]['late_not_married'] = 'x'; 
            $data[0]['late_married'] = '';
            $data[0]['late_f_fullname'] = mb_strtoupper($father); 
        }else{    
            $data[0]['late_married'] = 'x';
            $data[0]['late_not_married'] = ''; 
            $data[0]['late_parent_marriage_place'] = $parent_marriage_place;
            $data[0]['late_parent_marriage_date'] = date("F d, Y",strtotime($parent_marriage_date));
        }    
        
        $data[0]['late_reason'] = mb_strtoupper($late_reason);
        #delete
        #$late_husband = 'name here';
        $data[0]['late_husband'] = mb_strtoupper($late_husband);
        $data[0]['late_relationship'] = mb_strtoupper($late_relationship);
        
        if (($affiant_com_tax_date2!='0000-00-00') && ($affiant_com_tax_date2!="")){
            $late_affiant_date_ack_day = date("d",strtotime($affiant_com_tax_date2));
            $late_affiant_date_ack_monthyr = date("F, Y",strtotime($affiant_com_tax_date2));
        }else{
            $affiant_com_tax_date2 = '';
        }
            
        $data[0]['late_affiant_date_ack_day'] = $late_affiant_date_ack_day;
        $data[0]['late_affiant_date_ack_monthyr'] = $late_affiant_date_ack_monthyr;
        $data[0]['late_affiant_place2'] = $affiant_com_tax_place2;
        $data[0]['late_affiant_name'] = mb_strtoupper($late_affiant_name);
        
        $data[0]['late_officer_place_sign'] = $late_officer_place_sign;
        $data[0]['late_affiant_com_tax_nr'] = $affiant_com_tax_nr;
        
        if (($affiant_com_tax_date!='0000-00-00') && ($affiant_com_tax_date!="")){
            $affiant_com_tax_date = date("d M Y", strtotime($affiant_com_tax_date));
        }else{
            $affiant_com_tax_date = '';
        }
        
        if (($late_officer_date_sign!='0000-00-00') && ($late_officer_date_sign!="")){
            $late_officer_date_sign_day = date("d",strtotime($late_officer_date_sign));
            $late_officer_date_sign_monthyr = date("F, Y",strtotime($late_officer_date_sign));
        }else{
            $late_officer_date_sign = '';
        }
        $data[0]['late_officer_date_sign_day'] = $late_officer_date_sign_day;
        $data[0]['late_officer_date_sign_monthyr'] = $late_officer_date_sign_monthyr;
        $data[0]['late_affiant_com_tax_date'] = $affiant_com_tax_date;
        $data[0]['late_affiant_place'] = $affiant_com_tax_place;
        $data[0]['late_affiant_com_tax_place'] = $affiant_com_tax_place;
        $data[0]['late_officer_name'] = mb_strtoupper($late_officer_name);
        $data[0]['late_officer_title'] = $late_officer_title;
        $data[0]['late_officer_address'] = $late_officer_address;
        
        #images
        #$data[0]['image_01'] = java_classpath."images/birth_cert01.jpg";
        #$data[0]['image_02'] = java_classpath."images/birth_cert02.jpg";
        #$data[0]['image_01'] = "http://localhost/hisdmc/modules/registration_admission/certificates/images/birth_cert01.jpg";
        #$data[0]['image_02'] = "http://localhost/hisdmc/modules/registration_admission/certificates/images/birth_cert02.jpg";
        /*$data[0]['image_01'] = "http://localhost/hisdmc/modules/registration_admission/certificates/images/blank.jpg";
        $data[0]['image_02'] = "http://localhost/hisdmc/modules/registration_admission/certificates/images/blank.jpg";*/
    }    
    #echo "data = ".$data[0]['image_01'];
    #exit();
    #print_r($data);
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

    /*    $exportManager = new java("net.sf.jasperreports.engine.export.JRHtmlExporter");
        $exportManager->setParameter(java("net.sf.jasperreports.engine.JRExporterParameter")->JASPER_PRINT, $jasperPrint);
        $exportManager->setParameter(java("net.sf.jasperreports.engine.export.JRHtmlExporterParameter")->IS_USING_IMAGES_TO_ALIGN, false);
        $exportManager->setParameter(java("net.sf.jasperreports.engine.JRExporterParameter")->OUTPUT_FILE_NAME, $outputPath);
        $exportManager->exportReport();
        header("Content-type: text/html");*/
$exportManager->exportReportToPdfFile($jasperPrint, $outputPath);


header("Content-type: application/pdf;");
#header('Content-Transfer-Encoding: binary');
#header('Content-Disposition: attachment; filename="BirthCertificate.pdf"');
readfile($outputPath);

unlink($outputPath);
