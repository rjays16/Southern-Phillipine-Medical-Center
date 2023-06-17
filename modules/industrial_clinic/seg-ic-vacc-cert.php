<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/industrial_clinic/class_ic_med_cert.php');
require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_department.php');

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','lab.php');

$local_user='ck_ic_transaction_user';
require_once $root_path.'include/inc_front_chain_lang.php';

# Create products object
$GLOBAL_CONFIG=array();

# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

if(!isset($pid)) $pid=0;
if(!isset($encounter_nr)) $encounter_nr='';

$thisfile = 'seg-ic-vacc-cert.php';

global $HTTP_SESSION_VARS;
global $db;

require_once $root_path.'gui/smarty_template/smarty_care.class.php';
$smarty = new smarty_care('common');

ob_start();
?>

    <link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css">
    <link rel="stylesheet" type="text/css" href="css/vacc-cert.css"/>
    <script type="text/javascript" src="<?= $root_path ?>js/jscalendar/calendar.js" ></script>
    <script type="text/javascript" src="<?= $root_path ?>js/jscalendar/lang/calendar-en.js" ></script>
    <script type="text/javascript" src="<?= $root_path ?>js/jscalendar/calendar-setup_3.js" ></script>
    <script type="text/javascript" src="<?= $root_path ?>js/jscalendar/calendar-setup_3.js" ></script>
    <script type="text/javascript" src="<?= $root_path ?>js/jquery/jquery-1.8.2.js"></script>
    <script type="text/javascript" src="js/vacc-cert2.js"></script>

    <script type="text/javascript">
        function printVaccineCert(pid, encounter_nr, ref_no){
            var noted_by = $('#in-charge').val();
            window.open("<?=$root_path?>modules/reports/reports/IC_Vaccination_Certificate.php?pid="+pid+"&encounter_nr="+encounter_nr+"&refno="+ref_no+"&noted_by="+noted_by);
        }

        function checkForm(){
            var d = document.vacc_cert;

            if(d.first_tetanus.value == '' || d.first_hepatitis.value == ''){
                return false;
            }
        }
    </script>

<?php
$enc_obj =new Encounter;
$obj_medCert = new SegICCertMed;
$dept_obj =new Department;
$pers_obj =new Personell;

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$encounter_nr = ($_GET['encounter_nr'] !='' ? $_GET['encounter_nr'] : $_POST['encounter_nr']);
$refno = ($_GET['refno'] !='' ? $_GET['refno'] : $_POST['refno']);

$vaccine = $obj_medCert->getAllinfoCertVaccine($refno);
$encInfo=$enc_obj->getEncounterInfo($encounter_nr);

if(isset($_POST['mode']))
{
    switch($_POST['mode'])
    {
        case 'save':

            $data = array(
                'refno' => $refno,
                'first_tetanus' => $_POST['first_tetanus'],
                'first_tetanus_deltoid' => $_POST['tetanus_deltoid1'],
                'tetanus_dose' => '1',
                'first_hepatitis' => $_POST['first_hepatitis'],
                'first_hepatitis_deltoid' => $_POST['hepatitis_deltoid1'],
                'hepatitis_dose' => '1',
                'create_dt'=>date('Y-m-d H:i:s'),
                'create_id'=>($_POST['vacc_cert2'].",".$_POST['vacc_cert']),
                'history'=>"Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n",
            );

            if($obj_medCert->saveCertVaccineFromArray($data)){
                $cert_nr = $db->Insert_ID();
                header("Location: seg-ic-vacc-cert.php?pid=".$encInfo['pid']."&encounter_nr=".$encounter_nr."&refno=".$refno);
                $msg = "<span style='font-weight: bold'><center>Saved Successfully!</center></span>";
            }
            else{
                $msg = "<span style='color: #a00000'><center><strong>Failed to save data!</strong></center></span>";
            }
            break;
        case 'update':
            $cert_nr = $vaccine['cert_nr'];
            if($_POST['dose_tetanus'] == '1'){
                $data = array(
                    'cert_nr' => $cert_nr,
                    'refno' => $refno,
                    'first_tetanus' => $_POST['first_tetanus'],
                    'first_tetanus_deltoid' => $_POST['tetanus_deltoid1'],
                    'tetanus_dose' => $_POST['dose_tetanus'],
                    'modify_dt'=>date('Y-m-d H:i:s'),
                    'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
                );
            }
            elseif($_POST['dose_tetanus'] == '2'){
                $data = array(
                    'cert_nr' => $cert_nr,
                    'refno' => $refno,
                    'second_tetanus' => $_POST['second_tetanus'],
                    'second_tetanus_deltoid' => $_POST['tetanus_deltoid2'],
                    'tetanus_dose' => $_POST['dose_tetanus'],
                    'modify_dt'=>date('Y-m-d H:i:s'),
                    'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
                );
            }
            elseif($_POST['dose_tetanus'] == '3'){
                $data = array(
                    'cert_nr' => $cert_nr,
                    'refno' => $refno,
                    'third_tetanus' => $_POST['third_tetanus'],
                    'third_tetanus_deltoid' => $_POST['tetanus_deltoid3'],
                    'tetanus_dose' => $_POST['dose_tetanus'],
                    'modify_dt'=>date('Y-m-d H:i:s'),
                    'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
                );
            }

            if($_POST['dose_hepatitis'] == '1'){
                $data1 = array(
                    'cert_nr' => $cert_nr,
                    'refno' => $refno,
                    'first_hepatitis' => $_POST['first_hepatitis'],
                    'first_hepatitis_deltoid' => $_POST['hepatitis_deltoid1'],
                    'hepatitis_dose' => $_POST['dose_hepatitis'],
                    'modify_dt'=>date('Y-m-d H:i:s'),
                    'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
                );
            }
            elseif($_POST['dose_hepatitis'] == '2'){
                $data1 = array(
                    'cert_nr' => $cert_nr,
                    'refno' => $refno,
                    'second_hepatitis' => $_POST['second_hepatitis'],
                    'second_hepatitis_deltoid' => $_POST['hepatitis_deltoid2'],
                    'hepatitis_dose' => $_POST['dose_hepatitis'],
                    'modify_dt'=>date('Y-m-d H:i:s'),
                    'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
                );
            }
            elseif($_POST['dose_hepatitis'] == '3'){
                $data1 = array(
                    'cert_nr' => $cert_nr,
                    'refno' => $refno,
                    'third_hepatitis' => $_POST['third_hepatitis'],
                    'third_hepatitis_deltoid' => $_POST['hepatitis_deltoid3'],
                    'hepatitis_dose' => $_POST['dose_hepatitis'],
                    'modify_dt'=>date('Y-m-d H:i:s'),
                    'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
                );
            }

            if($obj_medCert->updateCertVaccineFromArray($data) && $obj_medCert->updateCertVaccineFromArray($data1)){
                $msg = "<span style='font-weight: bold'><center>Saved Successfully!</center></span>";
                header("Location: seg-ic-vacc-cert.php?pid=".$encInfo['pid']."&encounter_nr=".$encounter_nr."&refno=".$refno);
            }
            else{
                $msg = "<span style='color: #a00000'><center><strong>Failed to save data!</strong></center></span>";
            }
            break;
    }
}

if($encounter_nr){
    if(!($encInfo)){
        echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
        exit();
    }
}else{
    echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Case Number!</em>';
    exit();
}

// Patient Information
$name = stripslashes(strtoupper($encInfo['name_first'])).' '.stripslashes(strtoupper($encInfo['name_middle'])).' '.stripslashes(strtoupper($encInfo['name_last']));
$sex = ($encInfo['sex'] == 'f' ? 'Female' : 'Male');
$case = $encInfo['encounter_nr'];
$age = floor((time() - strtotime($encInfo['date_birth']))/31556926).' years old';
$hrn = $encInfo['pid'];
$status = $encInfo['civil_status'];
$nationality = $encInfo['citizenship'];

if (trim($encInfo['street_name'])){
    if (trim($encInfo["brgy_name"])==NULL)
        $street_name = trim($encInfo['street_name'])." ";
    else
        $street_name = trim($encInfo['street_name']).", ";
}else{
    $street_name = "";
}

if ((!(trim($encInfo["brgy_name"]))) || (trim($encInfo["brgy_name"])=="NOT PROVIDED"))
    $brgy_name = "";
else
    $brgy_name  = trim($encInfo["brgy_name"]).", ";

if ((!(trim($encInfo["mun_name"]))) || (trim($encInfo["mun_name"])=="NOT PROVIDED"))
    $mun_name = "";
else{
    if ($brgy_name)
        $mun_name = trim($encInfo["mun_name"]);
    else
        $mun_name = trim($encInfo["mun_name"]);
}

if ((!(trim($encInfo["prov_name"]))) || (trim($encInfo["prov_name"])=="NOT PROVIDED"))
    $prov_name = "";
else
    $prov_name = trim($encInfo["prov_name"]);

if(stristr(trim($encInfo["mun_name"]), 'city') === FALSE){
    if ((!empty($encInfo["mun_name"]))&&(!empty($encInfo["prov_name"]))){
        if ($prov_name!="NOT PROVIDED")
            $prov_name = ", ".trim($prov_name);
        else
            $prov_name = trim($prov_name);
    }else{
        #$province = trim($prov_name);
        $prov_name = "";
    }
}else
    $prov_name = "";

$address = trim($street_name).trim($brgy_name).trim($mun_name).trim($prov_name);

if($vaccine['tetanus_dose'] == '1')
    $tetanus['first'] = 'checked';
elseif($vaccine['tetanus_dose'] == '2')
    $tetanus['second'] = 'checked';
elseif($vaccine['tetanus_dose'] == '3')
    $tetanus['third'] = 'checked';

if($vaccine['hepatitis_dose'] == '1')
    $hepatitis['first'] = 'checked';
elseif($vaccine['hepatitis_dose'] == '2')
    $hepatitis['second'] = 'checked';
elseif($vaccine['hepatitis_dose'] == '3')
    $hepatitis['third'] = 'checked';

if($vaccine['first_tetanus_deltoid'] == 'left')
    $tOption1['left'] = 'selected';
elseif($vaccine['first_tetanus_deltoid'] == 'right')
    $tOption1['right'] = 'selected';

if($vaccine['second_tetanus_deltoid'] == 'left')
    $tOption2['left'] = 'selected';
elseif($vaccine['second_tetanus_deltoid'] == 'right')
    $tOption2['right'] = 'selected';

if($vaccine['third_tetanus_deltoid'] == 'left')
    $tOption3['left'] = 'selected';
elseif($vaccine['third_tetanus_deltoid'] == 'right')
    $tOption3['right'] = 'selected';

if($vaccine['first_hepatitis_deltoid'] == 'left')
    $hOption1['left'] = 'selected';
elseif($vaccine['first_hepatitis_deltoid'] == 'right')
    $hOption1['right'] = 'selected';

if($vaccine['second_hepatitis_deltoid'] == 'left')
    $hOption2['left'] = 'selected';
elseif($vaccine['second_hepatitis_deltoid'] == 'right')
    $hOption2['right'] = 'selected';

if($vaccine['third_hepatitis_deltoid'] == 'left')
    $hOption3['left'] = 'selected';
elseif($vaccine['third_hepatitis_deltoid'] == 'right')
    $hOption3['right'] = 'selected';

$smarty->assign('sName', $name);
$smarty->assign('sHrn', $hrn);
$smarty->assign('sCase', $case);
$smarty->assign('sAge', $age);
$smarty->assign('sSex', $sex);
$smarty->assign('sStatus', ucfirst($status));
$smarty->assign('sNationality', $nationality);
$smarty->assign('sAddress', rtrim($address, ','));
$smarty->assign('sFormStart', '<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND.'" method="POST" id="vacc_cert" name="vacc_cert">');
$smarty->assign('sFormEnd', '</form>');
$smarty->assign('tDose1', '<input type="radio" name="dose_tetanus" id="rad_tetanus1"
                                         value="1" ' . $tetanus['first'] . '/>');
$smarty->assign('tDose2', '<input type="radio" name="dose_tetanus" id="rad_tetanus2"
                                         value="2" ' . $tetanus['second'] . '/>');
$smarty->assign('tDose3', '<input type="radio" name="dose_tetanus" id="rad_tetanus3"
                                         value="3" ' . $tetanus['third'] . '/>');
$smarty->assign('hDose1', '<input type="radio" name="dose_hepatitis" id="rad_hepatitis1"
                                         value="1" ' . $hepatitis['first'] . '/>');
$smarty->assign('hDose2', '<input type="radio" name="dose_hepatitis" id="rad_hepatitis2"
                                         value="2" ' . $hepatitis['second'] . '/>');
$smarty->assign('hDose3', '<input type="radio" name="dose_hepatitis" id="rad_hepatitis3"
                                         value="3" ' . $hepatitis['third'] . '/>');
$smarty->assign('tOption1', '<option value="">--</option>
                             <option value="left" ' . $tOption1['left'] . '>Left Deltoid</option>
                             <option value="right" ' . $tOption1['right'] .'>Right Deltoid</option>');
$smarty->assign('tOption2', '<option value="">--</option>
                             <option value="left" ' . $tOption2['left'] . '>Left Deltoid</option>
                             <option value="right" ' . $tOption2['right'] .'>Right Deltoid</option>');
$smarty->assign('tOption3', '<option value="">--</option>
                             <option value="left" ' . $tOption3['left'] . '>Left Deltoid</option>
                             <option value="right" ' . $tOption3['right'] .'>Right Deltoid</option>');
$smarty->assign('hOption1', '<option value="">--</option>
                             <option value="left" ' . $hOption1['left'] . '>Left Deltoid</option>
                             <option value="right" ' . $hOption1['right'] .'>Right Deltoid</option>');
$smarty->assign('hOption2', '<option value="">--</option>
                             <option value="left" ' . $hOption2['left'] . '>Left Deltoid</option>
                             <option value="right" ' . $hOption2['right'] .'>Right Deltoid</option>');
$smarty->assign('hOption3', '<option value="">--</option>
                             <option value="left" ' . $hOption3['left'] . '>Left Deltoid</option>
                             <option value="right" ' . $hOption3['right'] .'>Right Deltoid</option>');
$smarty->assign('val1', $vaccine['first_tetanus']);
$smarty->assign('val2', $vaccine['second_tetanus']);
$smarty->assign('val3', $vaccine['third_tetanus']);
$smarty->assign('val4', $vaccine['first_hepatitis']);
$smarty->assign('val5', $vaccine['second_hepatitis']);
$smarty->assign('val6', $vaccine['third_hepatitis']);
$smarty->assign('sImgCalendar', $root_path.'gui/img/common/default/show-calendar.gif');

if(!$vaccine || empty($vaccine)){
    $vacc2 = $pers_obj->get_Signatory('vacc_cert2');
    while ($signatory1=$vacc2->FetchRow()){
        if ($signatory1['title'] == null){
            $title1 = '';
        }else{
            $title1 = ", ".$signatory1['title'];
        }
        $option_all1 .= "<option value=\"".strtoupper($signatory1['personell_nr'])."\">".strtoupper($signatory1['name']).$title1."</option>\n";
    }

    $vacc = $pers_obj->get_Signatory('vacc_cert');
    while ($signatory=$vacc->FetchRow()){
        if ($signatory['title'] == null){
            $title = '';
        }else{
            $title = ", ".$signatory['title'];
        }
        $option_all .= "<option value=\"".strtoupper($signatory['personell_nr'])."\">".strtoupper($signatory['name']).$title."</option>\n";
    }
}else{
    $cert = explode(",", $vaccine['create_id']);
    $signatory1 = $pers_obj->get_specific_signatory($cert[0]);
    if ($signatory1['title'] == null){
            $title1 = '';
    }else{
        $title1 = ", ".$signatory1['title'];
    }
    $option_all1 .= "<option value=\"".strtoupper($signatory1['personell_nr'])."\" selected>".strtoupper($signatory1['name']).$title1."</option>\n";

    $signatory = $pers_obj->get_specific_signatory($cert[1]);
    if ($signatory['title'] == null){
            $title = '';
    }else{
        $title = ", ".$signatory['title'];
    }
    $option_all .= "<option value=\"".strtoupper($signatory['personell_nr'])."\" selected>".strtoupper($signatory['name']).$title."</option>\n";
}
$smarty->assign('vacc_cert2', $option_all1);
$smarty->assign('vacc_cert', $option_all);


/*if(!$vaccine || empty($vaccine))
    $btn = '<input type="hidden" name="mode" id="mode" value="save">' . "\n" .
           '<input type="submit" name="Submit" value="Save">';
else
    $btn = '<input type="hidden" name="mode" id="mode" value="update">' . "\n" .
           '<input type="button" name="Print" value="Print"onclick="printVaccineCert('.$hrn.','.$encounter_nr.','.$refno.')">' . "\n&nbsp;&nbsp;" .
           '<input type="submit" name="Submit" value="Update">';*/
/*$btn = '<a href="#" class="myButton" id="save">Save</a>' .
       '<a href="#" class="myButton" id="update">Update</a>' .
       '<a href="#" class="myButton" id="print">Print</a>';*/
if(!$vaccine || empty($vaccine)){
    $modes = '<input type="hidden" name="mode" id="mode" value="save">';
    $btn = '<input type="submit" name="Submit" value="Save" id="save">';
}
else{
    $modes = '<input type="hidden" name="mode" id="mode" value="update">';
    $btn = '<input type="button" name="Print" value="Print"onclick="printVaccineCert('.$hrn.','.$encounter_nr.','.$refno.')">' . "\n&nbsp;&nbsp;" .
        '<input type="submit" name="Submit" value="Update">';
}

$hiddenInputs = '<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'">' . "\n" .
                '<input type="hidden" name="refno" id="refno" value="'.$refno.'">';

$smarty->assign('sButtons', $btn);
$smarty->assign('sEncRef', $hiddenInputs);
$smarty->assign('sMode', $modes);
$smarty->assign('sMsg', $msg);
ob_start();
$sTemp='';
?>

<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sHiddenInputs',$sTemp);

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','industrial_clinic/seg-ic-cert-vaccination.tpl');
$smarty->display('common/mainframe2.tpl');