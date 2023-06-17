<?php
/**
 * TODO Make use of smarty to generate drop-downs and check-boxes - Nick 7-11-2015
 */
require_once("roots.php");
require_once($root_path . 'include/inc_environment_global.php');
#require_once($root_path.'include/inc_date_format_functions.php');
include_once($root_path . 'include/care_api_classes/industrial_clinic/class_ic_med_cert.php');
require_once($root_path . 'include/care_api_classes/class_personell.php');
require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');
include_once($root_path . 'include/care_api_classes/class_encounter.php');


define('NO_2LEVEL_CHK',1);
define('LANG_FILE','lab.php');

$local_user='ck_ic_transaction_user';
require_once $root_path.'include/inc_front_chain_lang.php';

# Create products object
$GLOBAL_CONFIG=array();

# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

require_once $root_path.'gui/smarty_template/smarty_care.class.php';
$smarty = new smarty_care('common');

$obj_medCert = new SegICCertMed;

global $db;

if (isset($_GET['pid']) && $_GET['pid']) {
    $pid = $_GET['pid'];
}
if (isset($_POST['pid']) && $_POST['pid']) {
    $pid = $_POST['pid'];
}
if (isset($_GET['refno']) && $_GET['refno']) {
    $refno = $_GET['refno'];
}
if (isset($_POST['refno']) && $_POST['refno']) {
    $refno = $_POST['refno'];
}
if (isset($_GET['encounter_nr']) && $_GET['encounter_nr']) {
    $encounter_nr = $_GET['encounter_nr'];
}
if (get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}
$exam_nr = '';

$errors = array();

if (isset($_POST['mode'])) {


    //added by Nick 7-29-2015
    $db->StartTrans();

    /* Other Clinical Findings */
    foreach($_POST['exam'] as $key => $finding){
        $status = SegICCertMed::saveOtherClinicalFindings($key,$_POST['encounter_nr'],$finding['result'],
            $finding['remark'],$finding['left_remark'],$finding['right_remark'],$finding['glass_right_remark'],
            $finding['glass_left_remark'],$finding['physician_nr']);
        if(!$status){
            $errors[] = 'Error in saving Other Clinical Findings.<br>'.$db->ErrorMsg();
            $db->FailTrans();
        }
    }

    /* Conditions */
    foreach($_POST['cond'] as $key => $value){
        $status = $db->Replace('seg_industrial_med_chart_conditions',array(
            'encounter_nr' => $db->qstr($_POST['encounter_nr']),
            'condition_nr' => $db->qstr($key),
            'is_selected' => $db->qstr($value),
        ),array('encounter_nr','condition_nr'));
        if(!$status){
            $errors[] = 'Error in saving patient\'s condition.<br>'.$db->ErrorMsg();
            $db->FailTrans();
        }
    }

    /* Other Routine Mecidal Examinations */
    foreach($_POST['content'] as $key => $value){
        $status = $db->Replace('seg_industrial_med_chart_content',array(
            'encounter_nr' => $db->qstr($_POST['encounter_nr']),
            'content_nr' => $db->qstr($key),
            'content' => $db->qstr($value),
        ),array('encounter_nr','content_nr'));
        if(!$status){
            $errors[] = 'Error in saving patient\'s Other Routine Medical Examinations.<br>'.$db->ErrorMsg();
            $db->FailTrans();
        }
    }

    /* Diagnostic Report */
    foreach($_POST['diag'] as $key => $value){
        $status = $db->Replace('seg_industrial_med_chart_diagnostic',array(
            'encounter_nr' => $db->qstr($_POST['encounter_nr']),
            'diag_nr' => $db->qstr($key),
            'is_selected' => $db->qstr($value['yn']),
            'remarks' => $db->qstr($value['remark']),
        ),array('encounter_nr','diag_nr'));
        if(!$status){
            $errors[] = 'Error in saving patient\'s diagnositc report.<br>'.$db->ErrorMsg();
            $db->FailTrans();
        }
    }

    /* Physical Examination */
    foreach($_POST['phy'] as $key => $value){
        $status = $db->Replace('seg_industrial_med_chart_physical',array(
            'encounter_nr' => $db->qstr($_POST['encounter_nr']),
            'phy_nr' => $db->qstr($key),
            'is_selected' => $db->qstr($value['yn']),
            'remarks' => $db->qstr($value['remark']),
        ),array('encounter_nr','phy_nr'));
        if(!$status){
            $errors[] = 'Error in saving patient\'s Physical Examination.<br>'.$db->ErrorMsg();
            $db->FailTrans();
        }
    }

    /* Vital Signs */
    $vitalSigns = array(
        'exam_nr'        => $db->qstr($exam_nr),
        'encounter_nr'   => $db->qstr($_POST['encounter_nr']),
        'blood_pressure' => $db->qstr($_POST['bp']),
        'pulse_rate'     => $db->qstr($_POST['pr']),
        'resp_rate'      => $db->qstr($_POST['rr']),
        'temperature'    => $db->qstr($_POST['temp']),
        'weight'         => $db->qstr($_POST['weight']),
        'height'         => $db->qstr($_POST['height']),
        'bmi'            => $db->qstr($_POST['bodybuilt']),
        'visual_acuity'  => $db->qstr($_POST['visual']),
        'ishihara'       => $db->qstr($_POST['ishihara']),
        'hearing'        => $db->qstr($_POST['hearing']),
        'speech'         => $db->qstr($_POST['speech']),
    );
    $status = $db->Replace('seg_industrial_vitalsigns',$vitalSigns,array('encounter_nr'));
    if(!$status){
        $errors[] = 'Error in saving patient\'s Vital Signs.<br>'.$db->ErrorMsg();
        $db->FailTrans();
    }

    /* Main Medical Chart */
    $data = array(
        //'exam_nr'        => $db->qstr($exam_nr),
        'refno'          => $db->qstr($refno),
        'pid'            => $db->qstr($_POST['pid']),
        'encounter_nr'   => $db->qstr($_POST['encounter_nr']),
        'diagnosis'      => $db->qstr($_POST['final_diagnosis']),
        'remarks'      => $db->qstr($_POST['remarks_final']), # added by: syboy 10/26/2015 : meow
        'physician_nr'   => $db->qstr($_POST['physician_nr']),
        'recommendation' => $db->qstr($_POST['recommendation']),
        'treatment'      => $db->qstr($_POST['treatment']),
    );

    if($mode=='save'){
        $history = $db->qstr("Created by " . date('Y-m-d H:i:s') . " " . $_SERVER['sess_user_name'] . " \n");
        $data = array_merge($data,array(
            'history' => $history,
            'create_id' => $_SERVER['sess_user_name'],
            'create_dt' => 'NOW()',
        ));
    }else if($mode=='update'){
        $history = "CONCAT(history,".$db->qstr("Updated by " . date('Y-m-d H:i:s') . " " . $_SERVER['sess_user_name'] . "\n").")";
        $data = array_merge($data,array(
            'history' => $history,
            'modify_id' => $_SERVER['sess_user_name'],
            'modify_dt' => 'NOW()',
        ));
    }

    $status = $db->Replace('seg_industrial_med_chart',$data,array('refno'));
    if(!$status){
        $errors[] = 'Error in saving patient\'s Medical Chart.<br>'.$db->ErrorMsg();
        $db->FailTrans();
    }

    if(empty($errors)){
        $db->CompleteTrans();
        $smarty->assign('message','<div class="alert"><strong><i>&check;</i> success:</strong> <span>Data Updated</span></div>');
    }else{
        $smarty->assign('errors',$errors);
    }
    //end Nick

//    commented out by Nick 7-24-2015
//    switch ($_POST['mode']) {
//        case 'save':

//            $con = $_POST['cond'];
//            foreach ($con as $key => $value) {
//                if (!empty($value)) {
//                    $val = array($encounter_nr, $key, $value);
//                    $sql = $db->Prepare('INSERT INTO seg_industrial_med_chart_conditions (encounter_nr,condition_nr,is_selected) VALUES (?,?,?)');
//                    $db->Execute($sql, $val);
//                }
//            }

//            $content = $_POST['content'];
//            foreach ($content as $key => $value) {
//                if (!empty($value)) {
//                    $contval = array($encounter_nr, $key, $value);
//                    $contsql = $db->Prepare('INSERT INTO seg_industrial_med_chart_content (encounter_nr,content_nr,content) VALUES(?,?,?)');
//                    $db->Execute($contsql, $contval);
//                }
//            }

//            $diag = $_POST['diag'];
//            foreach ($diag as $key => $diag) {
//                if ($diag['yn'] != '') {
//                    $diagval = array($encounter_nr, $key, $diag['yn'], $diag['remark']);
//                    $diagsql = $db->Prepare('INSERT INTO seg_industrial_med_chart_diagnostic (encounter_nr,diag_nr,is_selected,remarks) VALUES(?,?,?,?)');
//                    $db->Execute($diagsql, $diagval);
//                }
//            }

//            $phy = $_POST['phy'];
//            foreach ($phy as $key => $value) {
//                if ($value['yn'] != '') {
//                    $phyval = array($encounter_nr, $key, $value['yn'], $value['remark']);
//                    $physql = $db->Prepare('INSERT INTO seg_industrial_med_chart_physical (encounter_nr,phy_nr,is_selected,remarks) VALUES(?,?,?,?)');
//                    #echo 'INSERT INTO TB (encounter_nr,phy_nr,is_selected,remarks) VALUES ('.$encounter_nr.','.$key.','.$value['yn'].','.$value['remark'].')<br>';
//                    $db->Execute($physql, $phyval);
//                }
//
//            }

//            $vitalsigns = array('exam_nr' => $exam_nr,
//                // 'systole',
//                // 'diastole',
//                // 'cardiac_rate',
//                'encounter_nr' => $encounter_nr,
//                'blood_pressure' => $_POST['bp'],
//                'pulse_rate' => $_POST['pr'],
//                'resp_rate' => $_POST['rr'],
//                'temperature' => $_POST['temp'],
//                'weight' => $_POST['weight'],
//                'height' => $_POST['height'],
//                'bmi' => $_POST['bodybuilt'],
//                'visual_acuity' => $_POST['visual'],
//                'ishihara' => $_POST['ishihara'],
//                'hearing' => $_POST['hearing'],
//                'speech' => $_POST['speech'],
//            );
//
//            if ($obj_medCert->saveVitalsignsFromArray($vitalsigns)) {
//                # $cert_nr = $db->Insert_ID();
//                //if($save2){
//                $errorMsg = '<div class="alert"><i>&check;</i> <strong>success:</strong> <span>Data Saved</span></div>';
//                $smarty->assign('message',$errorMsg);
//            } else {
//                echo $obj_medCert->sql;
//                # $errorMsg='<font style="color:#FF0000">'."Failed to save data".'</font>';
//                #echo $errorMsg;
//            }

//            $data = array(
//                'exam_nr' => $exam_nr,
//                'refno' => $refno,
//                'pid' => $_POST['pid'],
//                'encounter_nr' => $_POST['encounter_nr'],
//                'diagnosis' => $_POST['final_diagnosis'],
//                'physician_nr' => $_POST['physician_nr'],
//                'recommendation' => $_POST['recommendation'],
//                'history' => "Create " . date('Y-m-d H:i:s') . " " . $HTTP_SESSION_VARS['sess_user_name'] . " \n",
//                'create_id' => $HTTP_SESSION_VARS['sess_user_name'],
//                'create_dt' => date('Y-m-d H:i:s'),
//                'modify_id' => $HTTP_SESSION_VARS['sess_user_name'],
//                'modify_dt' => date('Y-m-d H:i:s'),
//                'treatment' => $_POST['treatment'],
//            );
//
//            if ($obj_medCert->saveMedChartFromArray($data)) {
//                # $cert_nr = $db->Insert_ID();
//                //if($save2){
//                #$errorMsg='<font style="color:#FF0000;font-style:italic">'."Saved sucessfully!".'</font>';
//                #echo $errorMsg;
//            } else {
//                echo $obj_medCert->sql;
//                #$errorMsg='<font style="color:#FF0000">'."Failed to save data".'</font>';
//                #echo $errorMsg;
//            }

//            break;
//
//        case 'update':
            /*update conditions*/
//            commented out by Nick 7-24-2015
//            $con = $_POST['cond'];
//            foreach ($con as $key => $value) {
//                $param = array($key, $encounter_nr);
//                $check = $db->Prepare('SELECT is_selected FROM seg_industrial_med_chart_conditions WHERE condition_nr = ? AND encounter_nr = ?');
//                $result = $db->Execute($check, $param);
//                if ($result->RecordCount()) {
//                    $update = $db->Prepare('UPDATE seg_industrial_med_chart_conditions SET is_selected= ? WHERE condition_nr=? AND encounter_nr= ?');
//                } else {
//                    if (!empty($value)) {
//                        $update = $db->Prepare('INSERT INTO seg_industrial_med_chart_conditions (is_selected,condition_nr,encounter_nr) VALUES (?,?,?)');
//                    }
//                }
//
//                $val = array($value, $key, $encounter_nr);
//                $db->Execute($update, $val);
//            }

            /*update content*/
//            $content = $_POST['content'];
//            foreach ($content as $key => $value) {
//                $param = array($key, $encounter_nr);
//                $check = $db->Prepare('SELECT content FROM seg_industrial_med_chart_content WHERE content_nr = ? AND encounter_nr=?');
//                $result = $db->Execute($check, $param);
//                if ($result->RecordCount()) {
//                    $update = $db->Prepare('UPDATE seg_industrial_med_chart_content SET content = ? WHERE content_nr = ? AND encounter_nr = ?');
//                } else {
//                    if (!empty($value)) {
//                        $update = $db->Prepare('INSERT INTO seg_industrial_med_chart_content (content,content_nr,encounter_nr) VALUES (?,?,?)');
//                    }
//                }
//                $val = array($value, $key, $encounter_nr);
//                $db->Execute($update, $val);
//            }

            /*update diag*/
//            $diag = $_POST['diag'];
//            foreach ($diag as $key => $diag) {
//                $param = array($key, $encounter_nr);
//                $check = $db->Prepare('SELECT is_selected FROM seg_industrial_med_chart_diagnostic WHERE diag_nr = ? AND encounter_nr = ?');
//                $result = $db->Execute($check, $param);
//                if ($result->RecordCount()) {
//                    $update = $db->Prepare('UPDATE seg_industrial_med_chart_diagnostic SET is_selected = ? , remarks = ? WHERE diag_nr = ? AND encounter_nr = ?');
//                } else {
//                    if (!empty($diag)) {
//                        $update = $db->Prepare('INSERT INTO seg_industrial_med_chart_diagnostic (is_selected,remarks,diag_nr,encounter_nr) VALUES (?,?,?,?)');
//                    }
//                }
//                $val = array($diag['yn'], $diag['remark'], $key, $encounter_nr);
//                $db->Execute($update, $val);
//            }

            /*update physical*/
//            $phy = $_POST['phy'];
//            foreach ($phy as $key => $value) {
//                $param = array($key, $encounter_nr);
//                $check = $db->Prepare('SELECT is_selected FROM seg_industrial_med_chart_physical WHERE phy_nr = ? AND encounter_nr = ?');
//                $result = $db->Execute($check, $param);
//                if ($result->RecordCount()) {
//                    $update = $db->Prepare('UPDATE seg_industrial_med_chart_physical SET is_selected = ? ,remarks = ? WHERE phy_nr = ? AND encounter_nr = ?');
//                } else {
//                    if (!empty($value)) {
//                        $update = $db->Prepare('INSERT INTO seg_industrial_med_chart_physical (is_selected,remarks,phy_nr,encounter_nr) VALUES (?,?,?,?)');
//                    }
//                }
//
//                $val = array($value['yn'], $value['remark'], $key, $encounter_nr);
//                $db->Execute($update, $val);
//            }

//            $vitalsigns = array('exam_nr' => $exam_nr,
//                // 'systole',
//                // 'diastole',
//                // 'cardiac_rate',
//                'encounter_nr' => $encounter_nr,
//                'blood_pressure' => $_POST['bp'],
//                'pulse_rate' => $_POST['pr'],
//                'resp_rate' => $_POST['rr'],
//                'temperature' => $_POST['temp'],
//                'weight' => $_POST['weight'],
//                'height' => $_POST['height'],
//                'bmi' => $_POST['bodybuilt'],
//                'visual_acuity' => $_POST['visual'],
//                'ishihara' => $_POST['ishihara'],
//                'hearing' => $_POST['hearing'],
//                'speech' => $_POST['speech'],
//            );
//
//            if ($obj_medCert->updateVitalsignsFromArray($vitalsigns)) {
//                // $errorMsg='<div class="alert-box success"><span>success: </span>Write your success message here.</div>';
//                // echo $errorMsg;
//            } else {
//                // echo $obj_medCert->sql;
//                //   $errorMsg='<font style="color:#FF0000">'."Failed to save data".'</font>';
//                //    echo $errorMsg;
//            }
//
//            $data = array(
//                'exam_nr' => $medinfo['exam_nr'],
//                'refno' => $refno,
//                'pid' => $_POST['pid'],
//                'encounter_nr' => $_POST['encounter_nr'],
//                'diagnosis' => $_POST['final_diagnosis'],
//                'physician_nr' => $_POST['physician_nr'],
//                'recommendation' => $_POST['recommendation'],
//                'history' => "Create " . date('Y-m-d H:i:s') . " " . $HTTP_SESSION_VARS['sess_user_name'] . " \n",
//                'create_id' => $HTTP_SESSION_VARS['sess_user_name'],
//                'create_dt' => date('Y-m-d H:i:s'),
//                'modify_id' => $HTTP_SESSION_VARS['sess_user_name'],
//                'modify_dt' => date('Y-m-d H:i:s'),
//                'treatment' => $_POST['treatment'],
//            );
//
//            if ($obj_medCert->updateMedChartFromArray($data)) {
//                # $cert_nr = $db->Insert_ID();
//                //if($save2){
//                $errorMsg = '<div class="alert"><strong><i>&check;</i> success:</strong> <span>Data Updated</span></div>';
//                $smarty->assign('message',$errorMsg);
//            } else {
//                echo $obj_medCert->sql;
//                // $errorMsg='<font style="color:#FF0000">'."Failed to save data".'</font>';
//                //  echo $errorMsg;
//            }

//            break;
//
//        default:
//            # code...
//            break;
//    }

}//if (isset($_POST['mode']))

$enc_obj = new Encounter;

if ($encounter_nr) {
    if (!($encInfo = $enc_obj->getEncounterInfo($encounter_nr))) {
        echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
        exit();
    }
} else {
    echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Case Number!</em>';
    exit();
}

/*patient name*/
$last_name = $encInfo['name_last'];
$first_name = $encInfo['name_first'];
$middle_name = $encInfo['name_middle'];
$position = $encInfo['occupation'];
/*end patient name*/

function hosp()
{
    $objInfo = new Hospital_Admin();
    if ($row = $objInfo->getAllHospitalInfo()) {
        $row['hosp_agency'] = strtoupper($row['hosp_agency']);
        $row['hosp_name'] = strtoupper($row['hosp_name']);
    } else {
        $row['hosp_country'] = "Republic of the Philippines";
        $row['hosp_agency'] = "DEPARTMENT OF HEALTH";
        $row['hosp_name'] = "SOUTHERN PHILIPPINES MEDICAL CENTER";
        $row['hosp_addr1'] = "JICA Bldg., JP Laurel Avenue, Davao City";
    }
    return $row;
}

/* conditions */
function conditions()
{

    $html = "";

    $ic_obj = new SegICCertMed;
    $cond = $ic_obj->getDriverCondList();
    if (is_object($cond)) {
        while ($row = $cond->FetchRow()) {
            $rowCondList[$row['id']] = $row['id'] . ". " . $row['cond_name'];
        }
    }

    $nrows = $cond->RecordCount();
    $ncols = 3;
    $rem = $nrows % $ncols;
    $itemPerCol = $nrows / $ncols;
    $jump1 = 10;
    $jump2 = 20;
    $encounter_nr = (!empty($_GET['encounter_nr']) ? $_GET['encounter_nr'] : $_POST['encounter_nr']);
    $pid = (!empty($_GET['pid']) ? $_GET['pid'] : $_POST['pid']); # added by: syboy 10/26/2015 : meow
    for ($i = 1; $i <= $itemPerCol; $i++) {
        $sel = $ic_obj->getConditionsMedchart($pid, $encounter_nr, $i);
        $strCond = '<tr>
              <td class="text">' . $rowCondList[$i] . '</td>
              <td>
                <select name="cond[' . $i . ']" id="cond[' . $i . ']" class="textbox">
                  <option value="">---</option>
                  <option value="1" ' . $yes = ($sel == 1 ? 'selected' : '') . '>Yes</option>
                  <option value="2" ' . $no = ($sel == 2 ? 'selected' : '') . '>No</option>
                </select>
              </td>';

        $sel = $ic_obj->getConditionsMedchart($pid, $encounter_nr, $i + $jump1);
        $strCond .= '<td class="text">' . $rowCondList[$i + $jump1] . '</td>
              <td>
                <select name="cond[' . $tmp = $i + $jump1 . ']" id="' . $tmp = $i + $jump1 . '" class="textbox">
                  <option value="">---</option>
                  <option value="1" ' . $yes = ($sel == 1 ? 'selected' : '') . '>Yes</option>
                  <option value="2" ' . $no = ($sel == 2 ? 'selected' : '') . '>No</option>
                </select>
              </td>';
        $sel = $ic_obj->getConditionsMedchart($pid, $encounter_nr, $i + $jump2);
        $strCond .= '<td class="text">' . $rowCondList[$i + $jump2] . '</td>
              <td>
                <select name="cond[' . $tmp = $i + $jump2 . ']" id="' . $tmp = $i + $jump2 . '" class="textbox">
                  <option value="">---</option>
                  <option value="1" ' . $yes = ($sel == 1 ? 'selected' : '') . '>Yes</option>
                  <option value="2" ' . $no = ($sel == 2 ? 'selected' : '') . '>No</option>
                </select>
              </td>
              </tr>';
        $html .= $strCond;
    }

    return $html;
}

/* end conditions */

/* physical exam */
function physical()
{
    $html = "";

    $ic_obj = new SegICCertMed;
    $phys_list = $ic_obj->getDriverPhysicalExamList();

    $ncols = 2;
    $rem = $nrows % $ncols;
    $itemPerCol = 10;
    $jump1 = 10;
    $encounter_nr = (!empty($_GET['encounter_nr']) ? $_GET['encounter_nr'] : $_POST['encounter_nr']);
    $pid = (!empty($_GET['pid']) ? $_GET['pid'] : $_POST['pid']); # added by: syboy 10/28/2015 : meow
    if (is_object($phys_list)) {
        while ($row = $phys_list->FetchRow()) {
            $rowPhysList[$row['id']] = $row['id'] . ". " . $row['label_name'];
        }
    }

    for ($i = 1; $i <= $itemPerCol; $i++) {
        $physical = $ic_obj->getPhysicalMedchart($pid, $encounter_nr, $i);

        $strPhy = '<tr>
          <td width="20%" class="text">' . $rowPhysList[$i] . '</td>
          <td width="10%" align="center">
            <select name="phy[' . $i . '][yn]" id="" class="textbox">
              <option value="">---</option>
              <option value="1" ' . $yes = ($physical['is_selected'] == 1 ? 'selected' : '') . '>Yes</option>
              <option value="2" ' . $no = ($physical['is_selected'] == 2 ? 'selected' : '') . '>No</option>
            </select>
          </td>
          <td><textarea name="phy[' . $i . '][remark]" id="" align="center" cols="30" rows="3" placeholder="Remarks..." class="textbox">' . $physical["remarks"] . '</textarea></td>';
        if ($i < 9) {
            $physical = $ic_obj->getPhysicalMedchart($pid, $encounter_nr, $i + $jump1);
            $strPhy .= '<td width="20%" class="text">' . $rowPhysList[$i + $jump1] . '</td>
                        <td width="10%" align="center">
                          <select name="phy[' . $tmp = $i + $jump1 . '][yn]" id="" class="textbox">
                            <option value="">---</option>
                            <option value="1" ' . $yes = ($physical['is_selected'] == 1 ? 'selected' : '') . '>Yes</option>
                            <option value="2" ' . $no = ($physical['is_selected'] == 2 ? 'selected' : '') . '>No</option>
                          </select>
                        </td>
                        <td><textarea name="phy[' . $tmp = $i + $jump1 . '][remark]" id="" align="center" cols="30" rows="3" placeholder="Remarks..." class="textbox">' . $physical["remarks"] . '</textarea></td>
                        </tr>';
        } elseif ($i == 9) {
            /*$physical = $ic_obj->getPhysicalMedchart($encounter_nr,$i+$jump1); 
            $strPhy .= '<td width="20%" class="text">Others: </td>
                        <td width="10%" align="center"><input type="hidden" name="phy['.$tmp=$i+$jump1.'][yn]" id="othersyn" value="0"></td>
                        <td><textarea name="phy['.$tmp=$i+$jump1.'][remark]" id="others" align="center" cols="30" rows="3" class="textbox">'.$physical["remarks"].'</textarea></td>
                        </tr>';*/
        }


        $html .= $strPhy;
    }
    return $html;
}

/* end physical exam */

/* diagnostic */
function diagnostic()
{
    $html = "";
    $ic_obj = new SegICCertMed;
    $diag = $ic_obj->getDriverDiagnosisList();

    $nrows = $diag->RecordCount();
    $ncols = 2;
    $rem = $nrows % $ncols;
    $itemPerCol = $nrows / $ncols;
    $jump1 = $nrows / $ncols;
    $encounter_nr = (!empty($_GET['encounter_nr']) ? $_GET['encounter_nr'] : $_POST['encounter_nr']);
    $pid = (!empty($_GET['pid']) ? $_GET['pid'] : $_POST['pid']); # added by: syboy 10/28/2015 : meow
    if (is_object($diag)) {
        while ($row = $diag->FetchRow()) {
            $rowDiagList[$row['id']] = $row['id'] . ". " . $row['label_name'];
        }
    }

    for ($i = 1; $i <= $itemPerCol; $i++) {
        $diagnostic = $ic_obj->getDiagnosticMedchart($pid, $encounter_nr, $i);
        if ($i == 1) {

            $strDiag = '<tr>
                  <td width="20%" class="text">' . $rowDiagList[$i] . '</td>
                  <td width="10%" align="center">
                    <select name="diag[' . $i . '][yn]" id="diag[' . $i . '][yn]" class="textbox">
                      <option value="">---</option>
                      <option value="1" ' . $yes = ($diagnostic['is_selected'] == 1 ? 'selected' : '') . '>Yes</option>
                      <option value="2" ' . $no = ($diagnostic['is_selected'] == 2 ? 'selected' : '') . '>No</option>
                    </select>
                  </td>
                  
                  <td class="text">Hbg:<input type="text" name="diag[' . $i . '][remark]" value="' . $diagnostic['remarks'] . '" class="textbox">gms.</td>';
        } elseif ($i == 3) {
            $strDiag = '<tr>
                  <td width="20%" class="text">' . $rowDiagList[$i] . '</td>
                  <td width="10%" align="center">
                    <select name="diag[' . $i . '][yn]" id="diag[' . $i . ']" class="textbox">
                        <option value="">---</option>
                        <option value="1" ' . $yes = ($diagnostic['is_selected'] == 1 ? 'selected' : '') . '>Yes</option>
                        <option value="2" ' . $no = ($diagnostic['is_selected'] == 2 ? 'selected' : '') . '>No</option>
                    </select>
                  </td>';
            $diag_albumin = $ic_obj->getDiagnosticMedchart($pid, $encounter_nr, 13);
            $diag_sugar = $ic_obj->getDiagnosticMedchart($pid, $encounter_nr, 14);
            $diag_pus = $ic_obj->getDiagnosticMedchart($pid, $encounter_nr, 15);
            $diag_rbc = $ic_obj->getDiagnosticMedchart($pid, $encounter_nr, 16);
            $strDiag .= '<td class="text">Albumin:<input type="hidden" name="diag[13][yn]" value="" id="diag_albumin_yn">
                                           <input type="text" name="diag[13][remark]" value="' . $diag_albumin['remarks'] . '" id="diag_albumin" class="textbox"><br>
                      Sugar:<input type="hidden" name="diag[14][yn]" id="diag_sugar_yn" value="">
                            <input type="text" name="diag[14][remark]" value="' . $diag_sugar['remarks'] . '" id="diag_sugar" class="textbox"><br>
                      Pus Cells:<input type="hidden" name="diag[15][yn]" id="diag_pus_yn" value="">
                                <input type="text" name="diag[15][remark]" id="diag_pus" value="' . $diag_pus['remarks'] . '" class="textbox"><br>
                      RBC:<input type="hidden" name="diag[16][yn]" id="diag_rbc_yn" value="">
                          <input type="text" name="diag[16][remark]" id="diag_rbc" value="' . $diag_rbc['remarks'] . '" class="textbox">
                  </td>';
        } else {
            $strDiag = '<tr>
                <td width="20%" class="text">' . $rowDiagList[$i] . '</td>
                <td width="10%" align="center">
                  <select name="diag[' . $i . '][yn]" id="" class="textbox">
                      <option value="">---</option>
                      <option value="1" ' . $yes = ($diagnostic['is_selected'] == 1 ? 'selected' : '') . '>Yes</option>
                      <option value="2" ' . $no = ($diagnostic['is_selected'] == 2 ? 'selected' : '') . '>No</option>
                  </select>
                </td>
                <td><textarea name="diag[' . $i . '][remark]" id="diag[' . $i . ']" align="center" cols="30" rows="3" placeholder="Remarks..." class="textbox">' . $diagnostic['remarks'] . '</textarea></td>';
        }
        $diagnostic = $ic_obj->getDiagnosticMedchart($pid, $encounter_nr, $i + $jump1);
        if ($i == 5) {
            $strDiag .= '<td width="20%" class="text">' . $rowDiagList[$i + $jump1] . '<br>
                  Film no :</td>
                  <td width="10%" align="center">
                    <select name="diag[' . $temp = $i + $jump1 . '][yn]" id="diag[' . $i . ']" class="textbox">
                        <option value="">---</option>
                        <option value="1" ' . $yes = ($diagnostic['is_selected'] == 1 ? 'selected' : '') . '>Yes</option>
                        <option value="2" ' . $no = ($diagnostic['is_selected'] == 2 ? 'selected' : '') . '>No</option>
                    </select>
                  </td>
                  
                  <td><textarea name="diag[' . $temp = $i + $jump1 . '][remark]" id="diag[' . $i . ']" align="center" cols="30" rows="3" placeholder="Remarks..." class="textbox">' . $diagnostic['remarks'] . '</textarea></td>
                  </tr>';
        } elseif ($i == 6) {
            $diag_fbs = $ic_obj->getDiagnosticMedchart($pid, $encounter_nr, 17); 
            $diag_lipid = $ic_obj->getDiagnosticMedchart($pid, $encounter_nr, 18);
            $diag_trigly = $ic_obj->getDiagnosticMedchart($pid, $encounter_nr, 19);
            $diag_hdl = $ic_obj->getDiagnosticMedchart($pid, $encounter_nr, 20);
            $diag_ldl = $ic_obj->getDiagnosticMedchart($pid, $encounter_nr, 21);
            $diag_creatinine = $ic_obj->getDiagnosticMedchart($pid, $encounter_nr, 22);
            $diag_sua = $ic_obj->getDiagnosticMedchart($pid, $encounter_nr, 23);
            $diag_sgpt = $ic_obj->getDiagnosticMedchart($pid, $encounter_nr, 24); 


            $strDiag .= '<td width="20%" class="text">' . $rowDiagList[$i + $jump1] . '<br>
                  &nbsp;&nbsp;&nbsp;
                  FBS :<input type="hidden" name="diag[17][yn]" value="" id="diag_fbs_yn">
                       <input type="text" name="diag[17][remark]" value="' . $diag_fbs['remarks'] . '" class="textbox" style="width: 60px;" id="diag_fbs"> 
                       <br>
                  &nbsp;&nbsp;&nbsp;
                  Total Cholesterol/<br>
                  &nbsp;&nbsp;&nbsp;
                  Lipid Profile :<input type="hidden" name="diag[18][yn]" value="" id="diag_lipid_yn">
                                 <input type="text" name="diag[18][remark]" value="' . $diag_lipid['remarks'] . '" class="textbox" style="width: 60px;" id="diag_lipid">
                                 <br>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  Trigly :<input type="hidden" name="diag[19][yn]" value="" id="diag_trigly_yn">
                          <input type="text" name="diag[19][remark]" value="' . $diag_trigly['remarks'] . '" class="textbox" style="width: 60px;" id="diag_trigly">
                          <br>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  HDL :<input type="hidden" name="diag[20][yn]" value="" id="diag_hdl_yn">
                      <input type="text" name="diag[20][remark]" value="' . $diag_hdl['remarks'] . '" class="textbox" style="width: 60px;" id="diag_hdl">
                      <br>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  LDL/VLDL :<input type="hidden" name="diag[21][yn]" value="" id="diag_ldl_yn">
                           <input type="text" name="diag[21][remark]" value="' . $diag_ldl['remarks'] . '" class="textbox" style="width: 60px;"id="diag_ldl">
                           <br>
                  &nbsp;&nbsp;&nbsp;
                  Creatinine :<input type="hidden" name="diag[22][yn]" value="" id="diag_creatinine_yn">
                              <input type="text" name="diag[22][remark]" value="' . $diag_creatinine['remarks'] . '" class="textbox" style="width: 60px;" id="diag_creatinine">
                              <br>
                  &nbsp;&nbsp;&nbsp;
                  SUA :<input type="hidden" name="diag[23][yn]" value="" id="diag_sua_yn">
                       <input type="text" name="diag[23][remark]" value="' . $diag_sua['remarks'] . '" class="textbox" style="width: 60px;" id="diag_sua">
                       <br>
                  &nbsp;&nbsp;&nbsp;
                  SGPT :<input type="hidden" name="diag[24][yn]" value="" id="diag_sgpt_yn">
                        <input type="text" name="diag[24][remark]" value="' . $diag_sgpt['remarks'] . '" class="textbox"  style="width: 60px;" id="diag_sgpt">
                        <br>
                  </td>
                 
                  <td width="10%" align="center">
                    <select name="diag[' . $temp = $i + $jump1 . '][yn]" id="diag[' . $i . ']" class="textbox">
                        <option value="">---</option>
                        <option value="1" ' . $yes = ($diagnostic['is_selected'] == 1 ? 'selected' : '') . '>Yes</option>
                        <option value="2" ' . $no = ($diagnostic['is_selected'] == 2 ? 'selected' : '') . '>No</option>
                    </select>
                  </td>
                 
                  
                  <td><textarea name="diag[' . $temp = $i + $jump1 . '][remark]" id="diag[' . $i . ']" align="center" cols="30" rows="3" placeholder="Remarks..." class="textbox">' . $diagnostic['remarks'] . '</textarea></td>
                  </tr>';
        } else {
            $strDiag .= '<td width="20%" class="text">' . $rowDiagList[$i + $jump1] . '</td>
                  <td width="10%" align="center">
                    <select name="diag[' . $temp = $i + $jump1 . '][yn]" id="" class="textbox">
                      <option value="">---</option>
                      <option value="1" ' . $yes = ($diagnostic['is_selected'] == 1 ? 'selected' : '') . '>Yes</option>
                      <option value="2" ' . $no = ($diagnostic['is_selected'] == 2 ? 'selected' : '') . '>No</option>
                    </select>
                  </td>
                  <td><textarea name="diag[' . $temp = $i + $jump1 . '][remark]" id="diag[' . $i . ']" align="center" cols="30" rows="3" placeholder="Remarks..." class="textbox">' . $diagnostic['remarks'] . '</textarea></td>
                  </tr>';
        }
        $html .= $strDiag;
    }

    return $html;
}

/* end diagnostic */

function medicalOfficer()
{
    $pers_obj = new Personell;
    $ic_obj = new SegICCertMed;
    $listDoctors = array();
    $doctors = $pers_obj->getDoctors(1);

    $medchartInfo = $ic_obj->getMedChartInfo($_GET['refno']);

    if (is_object($doctors)) {
        while ($drInfo = $doctors->FetchRow()) {

            $middleInitial = "";
            if (trim($drInfo['name_middle']) != "") {
                $thisMI = split(" ", $drInfo['name_middle']);
                foreach ($thisMI as $value) {
                    if (!trim($value) == "")
                        $middleInitial .= $value[0];
                }
                if (trim($middleInitial) != "")
                    $middleInitial .= ". ";
            }
            $name_doctor = trim($drInfo["name_last"]) . ", " . trim($drInfo["name_first"]) . " " . $middleInitial;
            $name_doctor = ucwords(strtolower($name_doctor)) . ", MD";

            $listDoctors[$drInfo["personell_nr"]] = $name_doctor;
        }
    }
    $strMed = '<select class="combo-box" name="physician_nr" id="physician_nr">
            <option value="0">-Select a doctor-</option>';
    $listDoctors = array_unique($listDoctors);
    if (empty($medchartInfo['physician_nr']))
        $medchartInfo['physician_nr'] = 0;
    foreach ($listDoctors as $key => $value) {
        if ($medchartInfo['physician_nr'] == $key) {
            $strMed .= "        <option value='" . $key . "' selected=\"selected\">" . $value . "</option> \n";
        } else {
            $strMed .= "        <option value='" . $key . "'>" . $value . "</option> \n";
        }
    }
    $strMed .= '</select>';
    return $strMed;
}


$vital = $obj_medCert->getVitalSignsMedchart($pid, $encounter_nr);
if ($vital['visual_acuity'] == 1) {
    $visual1 = 'checked';
    #$visual1 = 'checked';
} elseif ($vital['visual_acuity'] == 2) {
    $visual2 = 'checked';
    #$visual2 = 'checked';
} elseif ($vital['visual_acuity'] == 3) {
    $visual3 = 'checked';
    #$visual3 = 'checked';
} elseif ($vital['visual_acuity'] == 4) {
    $visual4 = 'checked';
    #$visual4 = 'checked';
} else {
    $visual1 = '';
    $visual2 = '';
    $visual3 = '';
    $visual4 = '';
}

if ($vital['ishihara'] == 1) {
    $ishi1 = 'checked';
} elseif ($vital['ishihara'] == 2) {
    $ishi2 = 'checked';
} else {
    $ishi1 = '';
    $ishi2 = '';
}

if ($vital['hearing'] == 1) {
    $hear1 = 'checked';
} elseif ($vital['hearing'] == 2) {
    $hear2 = 'checked';
} else {
    $hear1 = '';
    $hear2 = '';
}

if ($vital['speech'] == 1) {
    $speech1 = 'checked';
} elseif ($vital['speech'] == 2) {
    $speech2 = 'checked';
} else {
    $speech1 = '';
    $speech2 = '';
}
/* end vital*/

function getDoctors(){
    $pers_obj = new Personell;
    $doctors = $pers_obj->getDoctors(1);
    $result = array(''=>'-SELECT PHYSICIAN-');#added by: syboy 09/07/2015
    foreach($doctors->GetRows() as $doctor){
        $result[$doctor['personell_nr']] = htmlentities(strtoupper($doctor['doctor_name2']));
    }
    return $result;
}

$doctorOptions = getDoctors();

$medinfo = $obj_medCert->getMedChartInfo($pid, $refno);

$recommendation = $medinfo[0]['recommendation'];
if ($medinfo[0]['recommendation'] == 1) {
    $a_checked = 'checked';
}else{
    $a_checked = '';
}
# added by: syboy 10/28/2015 : meow
if ($medinfo[0]['refno'] != $refno) {
    $hasSaved = false;
}else{
    $hasSaved = true;
}
# ended syboy

//added by Nick 7-11-2015
$smarty->assign('baseUrl',$root_path);
$smarty->assign('jquery_enabled',true);
$smarty->assign('jquery_ui_enabled',true);
$smarty->assign('footer_enabled',true);
$smarty->assign('title','MEDICAL EXAMINATION CHART');
$smarty->assign('hospitalInfo',hosp());
$smarty->assign('firstName',htmlentities($first_name));     //updated by gelie 09/17/2015
$smarty->assign('lastName',htmlentities($last_name));       
$smarty->assign('middleName',htmlentities($middle_name));   
$smarty->assign('position',$position);
$smarty->assign('middleName',htmlentities($middle_name));   
$smarty->assign('conditions',conditions());
$smarty->assign('personalHistory',$obj_medCert->getContentMedchart($pid, $encounter_nr, 1));
$smarty->assign('familyHistory',$obj_medCert->getContentMedchart($pid, $encounter_nr, 2));
$smarty->assign('immunizationHistory',$obj_medCert->getContentMedchart($pid, $encounter_nr, 3));
$smarty->assign('historyPresentIllness',$obj_medCert->getContentMedchart($pid, $encounter_nr, 7));
$smarty->assign('height',htmlentities($vital['height']));
$smarty->assign('weight',htmlentities($vital['weight']));
$smarty->assign('bloodPressure',htmlentities($vital['blood_pressure']));
$smarty->assign('pulseRate',htmlentities($vital['pulse_rate']));
$smarty->assign('respiratoryRate',htmlentities($vital['resp_rate']));
$smarty->assign('bmi',htmlentities($vital['bmi']));
$smarty->assign('visual1',$visual1);
$smarty->assign('visual2',$visual2);
$smarty->assign('visual3',$visual3);
$smarty->assign('visual4',$visual4);
$smarty->assign('ishi1',$ishi1);
$smarty->assign('ishi2',$ishi2);
$smarty->assign('hear1',$hear1);
$smarty->assign('hear2',$hear2);
$smarty->assign('speech1',$speech1);
$smarty->assign('speech2',$speech2);
$smarty->assign('physical',physical());
$smarty->assign('diagnostic',diagnostic());
$smarty->assign('aDental',$obj_medCert->getContentMedchart($pid, $encounter_nr, 4));
$smarty->assign('bOptha',$obj_medCert->getContentMedchart($pid, $encounter_nr, 5));
$smarty->assign('cEnt',$obj_medCert->getContentMedchart($pid, $encounter_nr, 6));
$smarty->assign('finalDiagnosis',$medinfo[0]['diagnosis']);
$smarty->assign('treatment',$medinfo[0]['treatment']);
$smarty->assign('a_checked',$a_checked);
$smarty->assign('bRecommendationChecked',$recommendation == 2 ? 'checked' : '');
$smarty->assign('cRecommendationChecked',$recommendation == 3 ? 'checked' : '');
$smarty->assign('dRecommendationChecked',$recommendation == 4 ? 'checked' : '');
$smarty->assign('eRecommendationChecked',$recommendation == 5 ? 'checked' : '');
// $smarty->assign('medicalOfficer',$doctorOptions);
$smarty->assign('remarks',$medinfo[0]['remarks']); # added by: syboy 01/11/2016 : meow
$smarty->assign('medicalOfficerNr',$medinfo[0]['physician_nr']);
$smarty->assign('pid',$pid);
$smarty->assign('encounter_nr',$encounter_nr);
$smarty->assign('refno',$refno );
$smarty->assign('hasSavedInfo',$hasSaved);
$smarty->assign('otherClinicalFindings',SegICCertMed::getOtherClinicalFindings());
$smarty->assign('otherClinicalFindingsResultOptions',array(
    1 => 'Examination not done',
    2 => 'No Abnormality noted',
    3 => 'Abnormality noted',
));

$smarty->assign('physicianOptions',$doctorOptions);
$smarty->assign('personOtherClinicalFindings',SegICCertMed::getPersonOtherClinicalFindings($pid, $encounter_nr));
$smarty->assign('headTags',array(
    '<script type="text/javascript" src="js/med-chart.js"></script>',
    "<script type='text/javascript' src='{$root_path}js/jquery/select2-3.5.3/select2.js'></script>",
    '<link rel="stylesheet" href="css/medchart.css">',
    "<link rel='stylesheet' href='{$root_path}js/jquery/select2-3.5.3/select2.css'>",
));

$smarty->assign('contentFile','industrial_clinic/seg-ic-cert-med-exam-interface1.tpl');
$smarty->display('common/layout.tpl');
//end Nick