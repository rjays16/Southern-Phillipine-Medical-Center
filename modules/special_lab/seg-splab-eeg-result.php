 <?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_special_lab.php');
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
require($root_path."modules/special_lab/ajax/splab-request-list.common.php");
$xajax->printJavascript($root_path.'classes/xajax');

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','lab.php');

$local_user='ck_prod_db_user';
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

global $HTTP_SESSION_VARS;
global $db;

$thisfile = 'seg-splab-eeg-result.php';

require_once $root_path.'gui/smarty_template/smarty_care.class.php';
$smarty = new smarty_care('common');

ob_start();
?>

<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="<?= $root_path ?>js/jscalendar/calendar.js" ></script>
<script type="text/javascript" src="<?= $root_path ?>js/jscalendar/lang/calendar-en.js" ></script>
<script type="text/javascript" src="<?= $root_path ?>js/jscalendar/calendar-setup_3.js" ></script>
<script type="text/javascript" src="<?= $root_path ?>js/jscalendar/calendar-setup_3.js" ></script>
<script type="text/javascript" src="<?= $root_path ?>js/jquery/jquery-1.8.2.js"></script>

<script type="text/javascript">
    function printEEGResult(refno, code){
        window.open("<?=$root_path?>modules/reports/reports/EEG_Result_Form.php?refno="+refno+"&service_code="+code);
        console.log(code);
    }
</script>

<?php
$srv_obj = new SegLab;
$pers_obj = new Personell;
$spl_obj = new SegSpecialLab;

$refno = $_GET['refno'];
$service_code = $_GET['service_code'];

$result = $spl_obj->getAllInfoEEGResult($refno, $service_code);

if(isset($_POST['Submit'])){
    $data = array(
        'refno' => $refno,
        'service_code' => $service_code,
        'medication' => $_POST['medication'],
        'perform_dt' => $_POST['perform_date'],
        'summary' => $_POST['summary'],
        'interpretation' => $_POST['interpret'],
        'consult_doctor' => $_POST['doctor'],
        'doctor_title' => $_POST['doctor_title'],
        'create_dt'=>date('Y-m-d H:i:s'),
        'create_id'=>$HTTP_SESSION_VARS['sess_user_name'],
        'history'=>"Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n",
    );

    if($spl_obj->saveEEGResultFromArray($data)){
        $id = $db->Insert_ID();
        $spl_obj->ServedLabRequest($refno, $service_code, 1, date("Y-m-d H:i:s"));
        header("Location: seg-splab-eeg-result.php?refno=".$refno."&service_code=".$service_code."&status=save");
    }
}
else if(isset($_POST['Update'])){
    $data = array(
        'id' => $result['id'],
        'refno' => $refno,
        'service_code' => $service_code,
        'medication' => $_POST['medication'],
        'perform_dt' => $_POST['perform_date'],
        'summary' => $_POST['summary'],
        'interpretation' => $_POST['interpret'],
        'consult_doctor' => $_POST['doctor'],
        'doctor_title' => $_POST['doctor_title'],
        'modify_dt'=>date('Y-m-d H:i:s'),
        'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
    );

    if($spl_obj->updateEEGResultFromArray($data)){
        header("Location: seg-splab-eeg-result.php?refno=".$refno."&service_code=".$service_code."&status=update");
    }
}

if($srv_obj->getLabItemInfo($refno, $service_code)){
    $ref_info = $srv_obj->getLabItemInfo($refno, $service_code)->FetchRow();
}

$pid = $ref_info['pid'];
$name = stripslashes(strtoupper($ref_info['name_first'])).' '.stripslashes(strtoupper($ref_info['name_middle'])).' '.stripslashes(strtoupper($ref_info['name_last']));
$sex = ($ref_info['sex'] == 'f' ? 'Female' : 'Male');
$age = floor((time() - strtotime($ref_info['date_birth']))/31556926).' years old';
$service_name = $ref_info['service'];

if($ref_info['manual_doctor']) {
    $request_physician = 'DR. ' . mb_strtoupper($ref_info['manual_doctor']);
}
else{
    $request_physician = 'DR. ' . mb_strtoupper($ref_info['request_doctor_name']);
}
$clinical_data = $ref_info['clinical_info'];

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

if(isset($_GET['status'])){
    if($_GET['status'] == 'save'){
        $smarty->assign('sMessage', "Saved Successfully!");
    }
    else if($_GET['status'] == 'update'){
        $smarty->assign('sMessage', "Updated Successfully!");
    }
}

$listDoctors = array();
$doctors = $pers_obj->getDoctors(1);
$listDoctors[0] = "-- Select Doctor --";

if (is_object($doctors)){
    while($drInfo=$doctors->FetchRow()){
        $middleInitial = "";
        if (trim($drInfo['name_middle'])!=""){
            $thisMI=split(" ",$drInfo['name_middle']);
            foreach($thisMI as $value){
                if (!trim($value)=="")
                    $middleInitial .= $value[0];
            }
            if (trim($middleInitial)!="")
                $middleInitial .= ". ";
        }
        $name_doctor = trim($drInfo["name_last"]).", ".trim($drInfo["name_first"])." ".$middleInitial; 
        $name_doctor = ucwords(strtolower($name_doctor)).", MD";

        $listDoctors[$drInfo["personell_nr"]]=$name_doctor;
    }
}

$listDoctors = array_unique($listDoctors);
$sel_doctor = "<select name='doctor'>";
foreach($listDoctors as $key => $value){
    if($result['consult_doctor'] == $key)
        $sel_doctor .= "<option value='".$key."' selected=\"selected\">".$value."</option>\n";
    else
        $sel_doctor .= "<option value='".$key."'>".$value."</option>\n";
}
$sel_doctor .= "</select>";

if(!$result || empty($result)){
    $perform_date = date('Y-m-d');
}
else {
    if ($result['perform_dt'] == null)
        $perform_date = date('Y-m-d');
    else
        $perform_date = $result['perform_dt'];
}

$doctor_title = "<select name='doctor_title'>";
if($result['doctor_title'] == 'FPNA')
    $doctor_title .= "<option value='FPPS'>FPPS</option>
                      <option value='FPNA' selected>FPNA</option>";
else
    $doctor_title .= "<option value='FPPS' selected>FPPS</option>
                      <option value='FPNA'>FPNA</option>";
$doctor_title .= "</select>";

$smarty->assign('sFormStart', '<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND.'&refno='.$refno.'&service_code='.$service_code.'" method="POST" id="eeg_result" name="eeg_result">');
$smarty->assign('sFormEnd', '</form>');
$smarty->assign('sHRN', $pid);
$smarty->assign('sName', $name);
$smarty->assign('sAge', $age);
$smarty->assign('sSex', $sex);
$smarty->assign('sService', $service_name);
$smarty->assign('sRequestDoc', $request_physician);
$smarty->assign('sDate', $perform_date);
$smarty->assign('sClinical', $clinical_data);
//$smarty->assign('sClinical', '<textarea name="clinical" rows="3" style="width: 300px">'.$clinical_data.'</textarea>');
$smarty->assign('sMedication', '<textarea name="medication" rows="3" style="width: 300px">'.$result['medication'].'</textarea>');
$smarty->assign('sSummary', '<textarea name="summary" rows="5" style="width: 300px">'.$result['summary'].'</textarea>');
$smarty->assign('sInterpret', '<textarea name="interpret" rows="5" style="width: 300px">'.$result['interpretation'].'</textarea>');
$smarty->assign('sImgCalendar', $root_path.'gui/img/common/default/show-calendar.gif');
$smarty->assign('sConsultDoc', $sel_doctor);
$smarty->assign('sDoctorTitle', $doctor_title);

$code = $service_code;
if(!$result || empty($result))
    $btn = '<input type="submit" name="Submit" value="Save">';
else
    $btn = '<input type="submit" name="Update" value="Update"/>&nbsp;&nbsp;<input type="button" value="Print" onclick="printEEGResult('.$refno.',\''.$service_code.'\')"/>';

$smarty->assign('sButtons', $btn);
ob_start();
$sTemp='';
?>

<?php
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->assign('sHiddenInputs',$sTemp);

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','special_lab/splab-eeg-result.tpl');
$smarty->display('common/mainframe2.tpl');