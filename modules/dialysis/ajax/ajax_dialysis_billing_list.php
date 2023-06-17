<?php

error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require "./roots.php";
require "{$root_path}include/inc_environment_global.php";
require "{$root_path}classes/json/json.php";


global $db;

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: text/x-json");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page - 1) * $maxRows;
$total = 0;
#added by art 03/16/2015
require_once($root_path.'include/care_api_classes/class_acl.php');
$objAcl = new Acl($_SESSION['sess_temp_userid']);
$AssignMachine_permission = $objAcl->checkPermissionRaw('_a_1_dialysisassignmachine');
#end art
require_once $root_path . "include/care_api_classes/dialysis/class_dialysis.php";
require_once $root_path . "include/care_api_classes/class_encounter.php";
$dialysis_obj = new SegDialysis();

$pid = $_REQUEST["pid"];

#Added Jayson-OJT 2/11/2014
#Reason: for filtering the list by encounter
$encounter_nr = $_REQUEST["selected_encounter"];
if (empty($encounter_nr)) {
    $encounter_nr = 0;
}

#End Jayson-OJT

$sortDir = $_REQUEST['dir'] == '1' ? 'ASC' : 'DESC';
$sortMap = array(
    'trn_no' => 'transaction_nr',
);

$filters = array(
    'sort' => $sortMap['trn_no'] . " " . $sortDir
);
$data = array();
$phFilters = array();
if (is_array($filters)) {
    foreach ($filters as $i => $v) {
        switch (strtolower($i)) {
            case 'sort': $sort_sql = $v;
                break;
        }
    }
}


$transactions_result = $dialysis_obj->getDialysisTransactionList($encounter_nr);

foreach ($transactions_result as $i => $trans) {
    $total++;
    if (isset($trans['transaction_date'])) {
        $buttons =
                '<button class="segButton" disabled><img src="../../gui/img/common/default/monitor_add.png" style="opacity:0.5;"/>DONE</button>';
                // '<button class="segButton" onclick="openAssignMachineTray(\''.$row['bill_nr'].'\',\''.$encounter_nr.'\',\''.$pid.'\');return false;"><img src="../../gui/img/common/default/monitor_add.png"/>Machine</button>'.
                //'<button class="segButton" name="edit_dial" id="edit_button" onclick="return false;"><img src="../../gui/img/common/default/pencil.png" />Edit</button>';
    } else {

        if ($trans['is_discharged'] == '1') {
            $disabledButton = 'disabled="disabled"';
        }elseif ($AssignMachine_permission == 0) {
             $disabledButton = 'disabled="disabled" title="No permission"';
        } else {
            $disabledButton = '';
        }

        $buttons = '<button class="segButton" ' . $disabledButton . ' onclick="openAssignMachineTray(\'' . $trans['bill_nr'] . '\',\'' . $encounter_nr . '\',\'' . $pid . '\');return false;"><img src="../../gui/img/common/default/monitor_add.png"/>Machine</button>';
        //'<button class="segButton" ' . $disabledButton . ' name="edit_dial" id="edit_button" onclick="return false;"><img src="../../gui/img/common/default/pencil.png" />Edit</button>';
    }

    $check = '<input type="checkbox">';

    #added by KENTOOT 09-01-2014
    if($trans["bill_type"]=='PH')
        $bill_type="PHIC";
    elseif($trans["bill_type"]=='NPH')
        $bill_type="Non-PHIC";

    $totalAmount = ($trans["amount"] + $trans['hdf_amount']);

    $data[$i] = array(
        'check' => $check,
        'trn_no' => $trans["bill_nr"],
        'bill_type' => $bill_type,
        'status' => $status,
        'orNo' => '',
        'details' => '',
        // 'lingap' => '',
        // 'cmap' => '',
        'totalbill' => number_format($totalAmount, 2, '.', ','),
        'dateVisited' => isset($trans["transaction_date"]) ? date('m/d/Y h:i:s A', strtotime($trans["transaction_date"])) : '',
        'options' => $buttons,
    );

    switch ($trans['STATUS']) {
        case 'cmap':
            $data[$i]['status'] = '<img src="../../images/flag_cmap.gif">';
            $data[$i]['details'] = "<table id='".$trans["bill_nr"]."' class='no-border'><tr><td>MAP<td><td>".$trans['amount']."<td></tr></table>";
            break;
        case 'lingap':
            $data[$i]['status'] = '<img src="../../images/flag_lingap.gif">';
            $data[$i]['details'] = "<table id='".$trans["bill_nr"]."' class='no-border'><tr><td>LINGAP<td><td>".$trans['amount']."<td></tr></table>";
            break;
        case 'manual':
            $data[$i]['status'] = '<span style="border:2px solid #4E74C7; background-color:#ffffff; padding: 0 2px; color:#0C3691;font-size:11px;"><b>MANUAL</b></span>';
            $manual = $dialysis_obj->getPrebillPayments($trans["bill_nr"]);
            $table = "<table id='".$trans["bill_nr"]."'>";
            foreach ($manual as $key => $value) {
                if($value['pay_type']=='cash')
                    $type = ucfirst($value['pay_type']);
                else
                    $type = strtoupper($value['pay_type']);
                /*added by Mark 07-29-16*/
                $new_type = $type== "CMAP" ? "MAP" :$type;
                $table .= "<tr><td>".$new_type."</td><td>".$value['amount']."</td></tr>";
            }
            $table .= "</table>";
            $data[$i]['details'] = $table;
            break;
        default:
            $data[$i]['status'] = '<img src="../../images/flag_paid.gif">';
            $manual = $dialysis_obj->getPrebillPayments($trans["bill_nr"]);
            $paid = $dialysis_obj->getPaidInCashier($trans['or_no'], $trans['bill_nr']);
            $table = "<table id='".$trans["bill_nr"]."'>";
            $or_table = "<table id='".$trans['or_no']."'>";
            foreach ($manual as $key => $value) {
                if($value['pay_type']=='cash')
                    $type = ucfirst($value['pay_type']);
                else
                    $type = strtoupper($value['pay_type']);
                 /*added by Mark 07-29-16*/
                $new_type = $type== "CMAP" ? "MAP" :$type;
                $table .= "<tr><td>".$new_type."</td><td>".$value['amount']."</td></tr>";
                $or_table .= "<tr></tr>";
            }

            foreach ($paid as $key => $value) {
                $table .= "<tr><td>Cash</td><td>".$value['amount_due']."</td></tr>";
                $or_table .= "<tr><td>".$trans['or_no']."</td></tr>";
            }

            $table .= "</table>";
            $or_table .= "</table>";
            $data[$i]['details'] = $table;
            $data[$i]['orNo'] = $or_table;
            //$data[$i]['details'] = "<table id='".$trans["bill_nr"]."' class='no-border'><tr><td>Cash<td><td>".$trans['amount']."<td></tr></table>";
            break;
    }

}

$response = array(
    'currentPage' => $page,
    'total' => $total,
    'data' => $data
);
echo json_encode($response);