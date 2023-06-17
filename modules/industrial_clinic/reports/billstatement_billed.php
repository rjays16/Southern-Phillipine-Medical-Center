<?php
/*added by art 06/10/2014*/
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/inc_environment_global.php';
require_once($root_path."include/care_api_classes/industrial_clinic/class_ic_transactions.php");
require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_department.php');

global $db;
global $HTTP_SESSION_VARS;
#--------------------------------------------------------------------------------------

$objIC = new SegICTransaction();
$pers_obj=new Personell;
$dept_obj=new Department;

$agency_id = $_GET['comp_id'];
$enclist = array();

$cutoff = $_GET['cutoff'];
$maxdte = $objIC->getMaxTrxnDte($agency_id, 1, DATE("Y-m-d", strtotime($cutoff)));
$bill_nr = $_GET['bill_nr'];
$getDisc = $objIC->getDiscount($bill_nr, $agency_id);
$disc = $getDisc->FetchRow();
$disc_percent = $disc['discount_percentage'];
$sub_total = '';
$disc_amount ='';

$enc = $objIC->getEncBilled($bill_nr);
if($enc){
  if ($enc->RecordCount()>0) {
    while($row = $enc->FetchRow()){
      array_push($enclist, $row['encounter_nr']);      
    }
  }
}

$seg_user_nr = $HTTP_SESSION_VARS['sess_temp_personell_nr'];
$prepared = $HTTP_SESSION_VARS["sess_user_name"];
 if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
    $seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
  else
    $seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];

 $dept_belong = $dept_obj->getUserDeptInfo($seg_user_name);

$personell = $pers_obj->get_Personell_info($seg_user_nr);
$job_pos = $personell['job_position'];
$license_nr = $personell['license_nr'] != NULL ? 'License No.'. $personell['license_nr'] : NULL;


$person_details = $pers_obj->getPersonellInfo($seg_user_name);

if (stristr($personell['job_function_title'],'doctor')===FALSE)
    $is_doctor = 0;
else
    $is_doctor = 1;


foreach ($enclist as $id) {
  $enc_nr = $objIC->getEnc($id);
  $enc = $enc_nr['encounter_nr'];
  $name = $objIC->getName($id);
  $getTotal = $objIC->getPatientExaminationsTotals($enc);
  $res = $getTotal->FetchRow();
  $total =  $res['lab_total_charge']+
            $res['splab_total_charge']+
            $res['bb_total_charge']+
            $res['radio_total_charge']+
            #$res['ip_total_charge']+
            #$res['mg_total_charge']+
            $res['pharma_total_charge']+
            $res['misc_total_charge'];
  $sub_total += $total;



  #get LB
  $lab = $objIC->getLabExams($enc,$disc_percent,'LB');
  if($lab){
    $labReq = '';
    $labReqPrice = '';
    if ($lab->RecordCount()>0) {
      while($row = $lab->FetchRow()){
        $socialized = $row['is_socialized'] == 1? '*':'';
        $qty = $row['quantity'];
        $labReq .= $socialized.$row['service_code'].' ('.$qty.')'.'<br>';
        $labReqPrice .= number_format($qty*$row['price_cash'],2).'<br>';
        $disc_amount += $qty*$row['discount'];
      }
    }
  }

  #get SPL
  #$spl = $objIC->getLabExams($enc,$disc_percent,'SPL');
  $spl = $objIC->getOtherLabs($enc,$disc_percent);
  if($spl){
    $splReq = '';
    $splReqPrice = '';
    if ($spl->RecordCount()>0) {
      while($row2 = $spl->FetchRow()){
        $socialized = $row2['is_socialized'] == 1? '*':'';
        $qty = $row2['quantity'];
        $splReq .= $socialized.$row2['service_code'].' ('.$qty.')'.'<br>';
        $splReqPrice .= number_format($qty*$row2['price_cash'],2).'<br>';
        $disc_amount += $qty*$row2['discount'];
      }
    }
  }

  #get radio
  $radio = $objIC->getRadio($enc,$disc_percent);
  if($radio){
    $radioReq = '';
    $radioReqPrice = '';
    if ($radio->RecordCount()>0) {
      while($row3 = $radio->FetchRow()){
        $socialized = $row3['is_socialized'] == 1? '*':'';
        $radioReq .= $socialized.$row3['service_code'].'<br>';
        $radioReqPrice .= number_format($row3['price_cash'],2).'<br>';
        $disc_amount += $row3['discount'];
      }
    }
  }

  #get meds
  $meds = $objIC->getPharmacy($enc,$disc_percent);
  if($meds){
    $medsReq = '';
    $medsReqPrice = '';
    if ($meds->RecordCount()>0) {
      while($row4 = $meds->FetchRow()){
        $socialized = $row4['is_socialized'] == 1? '*':'';
        $qty = $row4['quantity'];
        $medsReq .= $socialized.$row4['bestellnum'].' ('.$qty.')'.'<br>';
        $medsReqPrice .= number_format($qty*$row4['price_cash'],2).'<br>';
        $disc_amount += $qty*$row4['discount'];
      }
    }
  }

  #get misc
  $misc = $objIC->getMisc($enc,$disc_percent);
  if($misc){
    $miscReq = '';
    $miscReqPrice = '';
    if ($misc->RecordCount()>0) {
      while($row5 = $misc->FetchRow()){
        $qty = $row5['quantity'];
        $miscReq .= $row5['name_short'].' ('.$qty.')'.'<br>';
        $miscReqPrice .= number_format($qty*$row5['chrg_amnt'],2).'<br>';
        $disc_amount += $qty*$row5['discount'];
      }
    }
  }

$total = number_format((float)$total, 2, '.', '');

  $data[] = array(
                  'DATE'        => DATE("m-d-Y", strtotime($enc_nr['trxn_date'])),
                  'NAME'        => $name,
                  'LB'          => trim($labReq, "<br>"), 
                  'LBp'         => trim($labReqPrice, "<br>"), 
                  'SPL'         => trim($splReq, "<br>"), 
                  'SPLp'        => trim($splReqPrice, "<br>"), 
                  'RAD'         => trim($radioReq, "<br>"), 
                  'RADp'        => trim($radioReqPrice, "<br>"), 
                  'MED'         => trim($medsReq, "<br>"), 
                  'MEDp'        => trim($medsReqPrice, "<br>"),
                  'MISC'        => trim($miscReq, "<br>"), 
                  'MISCp'       => trim($miscReqPrice, "<br>"),
                  'TOTALAMOUNT' => $total
                  );

}
$objInfo = new Hospital_Admin();
if ($row = $objInfo->getAllHospitalInfo()) {
  $row['hosp_agency'] = strtoupper($row['hosp_agency']);
  $row['hosp_name']   = strtoupper($row['hosp_name']);
}
else {
  $row['hosp_country'] = "Republic of the Philippines";
  $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
  $row['hosp_name']    = "DAVAO MEDICAL CENTER";
  $row['hosp_addr1']   = "JICA Bldg., JP Laurel Avenue, Davao City";
}
$ic = 'INDUSTRIAL CLINIC BILLING STATEMENT';


$total_payable = number_format((float) ($sub_total - $disc_amount), 2, '.', '');
/*$amount_in_words = $objWords->number_word($total_payable, "Peso/s and", "Cent/s only");*/
$amount_in_words = ucwords($objIC->getMoneyInWords((double)$total_payable));
$company_name = ucwords(strtoupper($db->GetOne("SELECT c.name FROM seg_industrial_company AS c WHERE c.company_id = ".$db->qstr($agency_id))));
$params = array("hosp_country"=>$row['hosp_country'],
              "hosp_agency"=>$row['hosp_agency'],
              "hosp_name"=>$row['hosp_name'],
              "hosp_addr1"=>$row['hosp_addr1'],
              "ic" => $ic,
              "sub_total" => number_format((float)$sub_total, 2, '.', ''),
              "disc" => number_format((float)$disc_amount, 2, '.', ''),
              "total_payable" => $total_payable,
              "amount_in_words" => str_replace(',', '', $amount_in_words),
              "company" => $company_name,
              "prepared" => $prepared,
              "job_position" => $job_pos,
              "license_nr" => $license_nr
             );

#print_r($enclist);
showReport('ic-billingstatement',$params,$data,'PDF');
?>