<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_person.php');
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

$thisfile = 'seg-splab-ecg-result.php';

require_once $root_path.'gui/smarty_template/smarty_care.class.php';
$smarty = new smarty_care('common');

ob_start();
?>

<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css">

<!-- added by: syboy 03/28/2016 : meow  -->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<!-- ended syboy -->

<script type="text/javascript" src="<?= $root_path ?>js/jscalendar/calendar.js" ></script>
<script type="text/javascript" src="<?= $root_path ?>js/jscalendar/lang/calendar-en.js" ></script>
<script type="text/javascript" src="<?= $root_path ?>js/jscalendar/calendar-setup_3.js" ></script>
<script type="text/javascript" src="<?= $root_path ?>js/jscalendar/calendar-setup_3.js" ></script>
<script type="text/javascript" src="<?= $root_path ?>js/jquery/jquery-1.8.2.js"></script>

<script type="text/javascript">
    // added by: syboy 03/28/2016 : meow 
    var $J = jQuery.noConflict();
    $J(function(){
        $J('#ecgabbre').on('change', function(){
            var optionSelected = $J("option:selected", this);
            var valueSelected = this.value;
            var ecgDescrip = $J('#ecgAbb_'+valueSelected).val() + '' ;
            var imp = $J('#impression');
            if (valueSelected != 0) {
                
                if (imp.val()=='\n'){
                    imp.val('');
                }
                imp.val(imp.val() + ecgDescrip + '\n');
                    
            };
            
        });
    });
    // ended syboy

    function printEcgResult(refno){
        window.open("<?=$root_path?>modules/reports/reports/SPLAB_ECG_Result.php?refno="+refno);
    }

    // added by: syboy 03/28/2016 : meow 
    function mouserOverEcgAbb(tagId, id){
        
        var elTarget = $J(tagId);
            if(elTarget){
                desc = $J("#ecgAbb_"+id).val();
                return overlib( desc, CAPTION,"Impression Code Description",
                            TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, 'oltxt', CAPTIONFONTCLASS, 'olcap',
                            WIDTH, 300,FGCLASS,'olfgjustify',FGCOLOR, '#bbddff',FIXX, 240,FIXY, 100);
            }
    }
    // ended syboy
</script>

<?php
$enc_obj = new Encounter;
$person_obj = new Person;
$srv_obj = new SegLab;
$pers_obj = new Personell;
$spl_obj = new SegSpecialLab;

$refno = $_GET['refno'];
$result = $spl_obj->getAllInfoEcgResult($refno);

if(isset($_POST['Submit'])){
    $data = array(
        'refno' => $refno,
        'rhythm' => $_POST['rhythm'],
        'axis' => $_POST['axis'],
        'atrial' => $_POST['atrial'],
        'ventricular' => $_POST['ventri'],
        'interval' => $_POST['interval'],
        'qrs' => $_POST['qrs'],
        'qt' => $_POST['qt'],
        'position' => $_POST['position'],
        'input_1' => $_POST['input1'],
        'input_2' => $_POST['input2'],
        'input_3' => $_POST['input3'],
        'impression_id' => $_POST['ecgabbre'],
        'impression' => $_POST['impression'],
        'prepared_by' => $_POST['prepared'],
        'result_date' => $_POST['ecg_date'],
        'create_dt'=>date('Y-m-d H:i:s'),
        'create_id'=>$HTTP_SESSION_VARS['sess_user_name'],
        'history'=>"Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n",
    );

    if($spl_obj->saveEcgResultFromArray($data)){
        $id = $db->Insert_ID();
        $spl_obj->ServedLabRequest($refno, 'ECG', 1, date("Y-m-d H:i:s"));
        header("Location: seg-splab-ecg-result.php?refno=".$refno."&status=save");
    }
}
else if(isset($_POST['Update'])){
    $data = array(
        'id' => $result['id'],
        'refno' => $refno,
        'rhythm' => $_POST['rhythm'],
        'axis' => $_POST['axis'],
        'atrial' => $_POST['atrial'],
        'ventricular' => $_POST['ventri'],
        'interval' => $_POST['interval'],
        'qrs' => $_POST['qrs'],
        'qt' => $_POST['qt'],
        'position' => $_POST['position'],
        'input_1' => $_POST['input1'],
        'input_2' => $_POST['input2'],
        'input_3' => $_POST['input3'],
        'impression_id' => $_POST['ecgabbre'],
        'impression' => $_POST['impression'],
        'prepared_by' => $_POST['prepared'],
        'result_date' => date('Y-m-d H:i:s', strtotime($_POST['ecg_date'])),
        'modify_dt'=>date('Y-m-d H:i:s'),
        'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
    );

    if($spl_obj->updateEcgResultFromArray($data)){
        header("Location: seg-splab-ecg-result.php?refno=".$refno."&status=update");
        //var_dump($_POST);
    }
}

$ref_info = $srv_obj->getAllLabInfoByRefNo($refno, 'SPL')->FetchRow();
if($ref_info['encounter_nr']){
	$encInfo = $enc_obj->getEncounterInfo($ref_info['encounter_nr']);
}
else{
	$encInfo = $person_obj->getAllInfoObject($ref_info['pid'])->FetchRow();
}

$name = stripslashes(strtoupper($encInfo['name_first'])).' '.stripslashes(strtoupper($encInfo['name_middle'])).' '.stripslashes(strtoupper($encInfo['name_last']));
$sex = ($encInfo['sex'] == 'f' ? 'Female' : 'Male');
$age = floor((time() - strtotime($encInfo['date_birth']))/31556926).' years old';
$clinic = $db->GetOne('SELECT name_formal FROM care_department WHERE nr="'. $encInfo['current_dept_nr'] .'"');

if (trim($encInfo['street_name'])){
    if (trim($encInfo["brgy_name"])!="NOT PROVIDED")
        $street_name = trim($encInfo['street_name']).", ";
    else
        $street_name = trim($encInfo['street_name']).", ";
}else{
    $street_name = " ";
}

if ((!(trim($encInfo["brgy_name"]))) || (trim($encInfo["brgy_name"])=="NOT PROVIDED"))
    $brgy_name = " ";
else
    $brgy_name  = trim($encInfo["brgy_name"]).", ";

if ((!(trim($encInfo["mun_name"]))) || (trim($encInfo["mun_name"])=="NOT PROVIDED"))
    $mun_name = " ";
else{
    if ($brgy_name)
        $mun_name = trim($encInfo["mun_name"]);
    else
        $mun_name = trim($encInfo["mun_name"]);
}

if ((!(trim($encInfo["prov_name"]))) || (trim($encInfo["prov_name"])=="NOT PROVIDED"))
    $prov_name = " ";
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
        $prov_name = " ";
    }
}else
    $prov_name = " ";

$address = trim($street_name).trim($brgy_name).trim($mun_name).trim($prov_name);

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
$listDoctors[0] = "-Select Doctor-";
if (is_object($doctors)){
    while($drInfo=$doctors->FetchRow()){
        #print_r($drInfo);
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

        $name_doctor = trim($drInfo["name_last"]).", ".trim($drInfo["name_first"])." ".$middleInitial; #substr(trim($drInfo["name_middle"]),0,1).$dot;
        $name_doctor = ucwords(strtolower($name_doctor)).", MD";

        $listDoctors[$drInfo["personell_nr"]]=$name_doctor;

        #var_dump($listDoctors);
    }
}

$month_reader = $db->GetOne("SELECT prepared_by FROM seg_lab_ecg_result ORDER BY create_dt DESC");

$listDoctors = array_unique($listDoctors);
$sel_doctor = "<select name='prepared'>";
foreach($listDoctors as $key => $value){
    if($result['prepared_by']) {
    	if($result['prepared_by'] == $key)
        	$sel_doctor .= "<option value='".$key."' selected=\"selected\">".$value."</option>\n";
        else
        	$sel_doctor .= "<option value='".$key."'>".$value."</option>\n";
    }
    else{
    	if($month_reader == $key)
        	$sel_doctor .= "<option value='".$key."' selected=\"selected\">".$value."</option>\n";
        else
        	$sel_doctor .= "<option value='".$key."'>".$value."</option>\n";
	}
}
$sel_doctor .= "</select>";

if(!$result || empty($result)){
    $result_date = date('Y-m-d');
    $rhythm['sinus'] = "checked = 'checked'";
}
else {
    if ($result['result_date'] == null)
        $result_date = date('Y-m-d');
    else
        $result_date = $result['result_date'];

    if($result['rhythm'] == 'SINUS')
        $rhythm['sinus'] = "checked = 'checked'";
    else if($result['rhythm'] == 'NON-SINUS')
        $rhythm['nonsinus'] = "checked = 'checked'";
}
# added by: syboy 03/28/2016 : meow 
$ecgAbbre = $spl_obj->getECGAbbreviations();
$sel_ecgAbb = "<select name='ecgabbre' id='ecgabbre'>";
$sel_ecgAbb .= "<option value='0'>-Select Impression-</option>";
foreach ($ecgAbbre as $value) {
    $hidden_ecgAbbre .= "<input type='hidden' id='ecgAbb_".$value['id']."' name='ecgAbb_".$value['id']."' value='".$value['description']."'>";
    if ($result['impression_id'] == $value['id']) {
        $sel_ecgAbb .= "<option id='ecg_".$value['id']."' value='".$value['id']."' selected='selected' onMouseover='mouserOverEcgAbb(this, ".$value['id'].");' onMouseout='return nd();'>".$value['codename']."</option>";
    }else{
        $sel_ecgAbb .= "<option id='ecg_".$value['id']."' value='".$value['id']."' onMouseover='mouserOverEcgAbb(this, ".$value['id'].");' onMouseout='return nd();'>".$value['codename']."</option>";
    }
}
$sel_ecgAbb .= "</select>";
# ended syboy

$rhythm = '<input type="radio" id="rhythm1" name="rhythm" value="SINUS" ' .$rhythm['sinus']. '>SINUS
           <input type="radio" id="rhythm2" name="rhythm" value="NON-SINUS" '.$rhythm['nonsinus'].'>NON-SINUS';

$smarty->assign('sFormStart', '<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND.'&refno='.$refno.'" method="POST" id="ecg_result" name="ecg_result">');
$smarty->assign('sFormEnd', '</form>');
$smarty->assign('sName', $name);
$smarty->assign('sAge', $age);
$smarty->assign('sSex', $sex);
$smarty->assign('sAddress', $address);
$smarty->assign('sClinic', ($clinic) ? $clinic : 'N/A');
$smarty->assign('sDate', $result_date);
$smarty->assign('sRhythm', $rhythm);
$smarty->assign('sAxis', '<input type="text" name="axis" size="5" value="'.$result['axis'].'"/>');
$smarty->assign('sAtrial', '<input type="text" name="atrial" size="5" value="'.$result['atrial'].'"/>');
$smarty->assign('sVentri', '<input type="text" name="ventri" size="5" value="'.$result['ventricular'].'"/>');
$smarty->assign('sInterval', '<input type="text" name="interval" size="5" value="'.$result['interval'].'"/>');
$smarty->assign('sQrs', '<input type="text" name="qrs" size="5" value="'.$result['qrs'].'"/>');
$smarty->assign('sQt', '<input type="text" name="qt" size="5" value="'.$result['qt'].'"/>');
$smarty->assign('sPosition', '<input type="text" name="position" value="'.$result['position'].'"/>');
$smarty->assign('sImpression', '<textarea name="impression" id="impression" rows="3" style="width: 100%">'.$result['impression'].'</textarea>');
$smarty->assign('sEcgAbbre', $sel_ecgAbb);
$smarty->assign('sHiddenEcgAbbre', $hidden_ecgAbbre);
$smarty->assign('sImgCalendar', $root_path.'gui/img/common/default/show-calendar.gif');
$smarty->assign('sPreparedBy', $sel_doctor);

if(!$result || empty($result))
    $btn = '<input type="submit" name="Submit" value="Save">';
else
    $btn = '<input type="submit" name="Update" value="Update"/>&nbsp;&nbsp;<input type="button" value="Print" onclick="printEcgResult('.$refno.')"/>';

$smarty->assign('sButtons', $btn);
ob_start();
$sTemp='';
?>

<?php
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->assign('sHiddenInputs',$sTemp);

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','special_lab/splab-ecg-result.tpl');
$smarty->display('common/mainframe2.tpl');