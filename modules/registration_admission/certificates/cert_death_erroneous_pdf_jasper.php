<?php

require_once('roots.php');
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

$report = 'ErrorDeath';
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

require_once($root_path.'include/care_api_classes/class_cert_death.php');
$obj_DeathCert = new DeathCertificate($pid);

require_once($root_path.'include/care_api_classes/class_address.php');
$address_country = new Address('country');
$address_brgy = new Address('barangay');

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
    //print($brgy_nr);
    //GET BARANGAY INFO
    $brgy_info = $address_brgy->getAddressInfo($brgy_nr,TRUE);
    //print ($brgy_info);
    if($brgy_info){
        $brgy_row = $brgy_info->FetchRow();
    }
    //print ($brgy_row['brgy_name'] . $brgy_row['mun_name']);
    //exit();
}

$deathCertInfo = $obj_DeathCert->getDeathCertRecord($pid);

//modified by jasper 01/09/2013

if ($deathCertInfo){
    //echo "jasper";
    extract($deathCertInfo);
    //$delivery_method_tmp= substr(trim($DeathCertInfo['delivery_method']),0,1);
    //$delivery_method_info = substr(trim($DeathCertInfo['delivery_method']),4);
    //$death_manner_tmp = substr(trim($DeathCertInfo['death_manner']),0,1);
    //$death_manner_accident = substr(trim($DeathCertInfo['death_manner']),4);
    //$death_manner_info = substr(trim($DeathCertInfo['death_manner']),4);
    //$attendant_type_tmp = substr(trim($DeathCertInfo['attendant_type']),0,1);
    //$attendant_type_others = substr(trim($DeathCertInfo['attendant_type']),4);
    //$corpse_disposal_tmp= substr(trim($DeathCertInfo['corpse_disposal']),0,1);
    //$corpse_disposal_others = substr(trim($DeathCertInfo['corpse_disposal']),4);
    //$is_autopsy = substr(trim($DeathCertInfo['is_autopsy']),0,1);
    //$tmp_death_cause = unserialize($DeathCertInfo['death_cause']);
    var_dump($name_patient);

    $data[0]['name'] = "";
    $data[0]['name1'] = "";
    $data[0]['hrn'] = "";
    $data[0]['age'] = "";
    $data[0]['civilstatus'] = "";
    $data[0]['sex'] = "";
    $data[0]['informantname'] = "";
    $data[0]['witnessname'] = "";
    $data[0]['datetime'] = "";
    $data[0]['relationship'] = "";
    $data[0]['date'] = "";

    $baseurl = sprintf(
        "%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_ADDR'],
        substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
    );

    $data[0]['dmc'] = $baseurl . "gui/img/logos/dmc_logo.jpg";
    $data[0]['doh'] = $baseurl . "img/doh.png";

    $name_patient = stripslashes(strtoupper($name_last)).', '.stripslashes(strtoupper($name_first)).' '.stripslashes(strtoupper($name_middle));
    $data[0]['name'] = $name_patient;
    $data[0]['name1'] = $name_patient;

    //HRN
    $data[0]['hrn'] = $pid;

    //DATE
    $data[0]['date'] = date("F d, Y");
    $data[0]['datetime'] = date("m/d/Y")."  ".date("h:i A");

    //AGE
    if($age_at_death)
        list($ageYear,$ageMonth,$ageDay) = explode(":",$age_at_death);

    if ($ageYear != 0){
        if ($ageYear>1)
            $labelyear = "years";
        else
            $labelyear = "year";

        $age = floor($ageYear)." ".$labelyear;
    }elseif ($ageMonth != 0){
        if ($ageMonth>1)
            $labelmonth = "months";
        else
            $labelmonth = "month";

        $age = floor($ageMonth)." ".$labelmonth;

    }elseif ($ageDay != 0){
        
        if ($ageDay>1)
            $label = "days";
        else
            $label = "day";

        $age = floor($ageDay).' '.$label;

    }else{
        if($death_hour){
            if ($death_hour>1)
                $label = "hours";
            else
                $label = "hour";

            $age = floor($death_hour).' '.$label;
        }elseif ($death_min) {
            if ($death_min>1)
                $label = "minutes";
            else
                $label = "minute";

            $age = floor($death_min).' '.$label;
        }elseif ($death_sec) {
            if ($death_sec>1)
                $label = "seconds";
            else
                $label = "second";

            $age = floor($death_sec).' '.$label;
        }
    }
    // if (stristr($age,'years')){
    //     $age = substr($age,0,-5);
    //     if ($age>1)
    //         $labelyear = "years";
    //     else
    //         $labelyear = "year";

    //     $age = floor($age)." ".$labelyear;
    //     }elseif (stristr($age,'year')){
    //         $age = substr($age,0,-4);
    //         if ($age>1)
    //             $labelyear = "years";
    //         else
    //             $labelyear = "year";

    //         $age = floor($age)." ".$labelyear;

    //     }elseif (stristr($age,'months')){
    //         $age = substr($age,0,-6);
    //         if ($age>1)
    //             $labelmonth = "months";
    //         else
    //             $labelmonth = "month";

    //         $age = floor($age)." ".$labelmonth;

    //     }elseif (stristr($age,'month')){
    //         $age = substr($age,0,-5);

    //         if ($age>1)
    //             $labelmonth = "months";
    //         else
    //             $labelmonth = "month";

    //         $age = floor($age)." ".$labelmonth;

    //     }elseif (stristr($age,'days')){
    //         $age = substr($age,0,-4);

    //         if ($age>30){
    //             $age = $age/30;
    //             if ($age>1)
    //                 $label = "months";
    //             else
    //                 $label = "month";

    //         }else{
    //             if ($age>1)
    //                 $label = "days";
    //             else
    //                 $label = "day";
    //         }

    //         $age = floor($age).' '.$label;

    //     }elseif (stristr($age,'day')){
    //         $age = substr($age,0,-3);

    //         if ($age>1)
    //             $labelday = "days";
    //         else
    //             $labelday = "day";

    //         $age = floor($age)." ".$labelday;
    //     }else{
    //         if ($age){
    //             if ($age>1)
    //                 $labelyear = "years";
    //             else
    //                 $labelyear = "year";

    //             $age = $age." ".$labelyear;
    //             }else
    //                 $age = "0 day";
    //     }

        $data[0]['age'] = $age . " old";

        //SEX
        if ($sex=='f')
            $gender = "FEMALE";
        elseif ($sex='M')
            $gender = "MALE";

        $data[0]['sex'] = stripslashes(strtoupper($gender));

        //CIVIL STATUS
        $data[0]['civilstatus'] = ucwords(strtolower($civil_status));

        //INFORMANT NAME
        $data[0]['informantname'] = mb_strtoupper($informant_name);

        //INFORMANT RELATIONSHIP
        $data[0]['relationship'] = mb_strtoupper($informant_relation);

        //WITNESS NAME
        $data[0]['witnessname'] = mb_strtoupper($encoder_name);
}
else{
    $data[0]['name'] = "NO DEATH CERTIFICATE RECORD YET";
    $data[0]['date'] = date("F d, Y");

    $baseurl = sprintf(
        "%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_ADDR'],
        substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
    );

    $data[0]['dmc'] = $baseurl . "gui/img/logos/dmc_logo.jpg";
    $data[0]['doh'] = $baseurl . "img/doh.png";
}

//modified by jasper 01/09/2013

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
#header('Content-Transfer-Encoding: binary');
#header('Content-Disposition: attachment; filename="DeathCertificate.pdf"');
readfile($outputPath);

unlink($outputPath);
?>
