<?php
#Created By: Borj 2014-09-11
#Overall Summary of Bills Rendered
require_once('roots.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'include/care_api_classes/billing/class_billing_new.php';
$objBill = new Billing();

include 'parameters.php';
global $db;

$params->put('hosp_country',$hosp_country);
$params->put('hosp_agency',$hosp_agency);
$params->put('hosp_name',$hosp_name);
$params->put('hosp_addr1',$hosp_addr1);
$params->put('date_span',"From " . date('M d, Y',$from_date) . " to " . date('M d, Y',$to_date));
$params->put('delete_type',$header_dtype);

$query = "SELECT sbe.`bill_dte`,
                  sbe.`bill_nr`,
                  fn_get_person_name_first_mi_last(ce.`pid`) as patient_name,
                  (SELECT NAME FROM care_users WHERE login_id = (IF(sbe.`modify_id`, sbe.`modify_id`, sbe.`create_id`))) AS prep_by,
                  ce.`encounter_nr`,
                  DATE_FORMAT(ce.`encounter_date`, '%b %d %Y %h:%i %p') AS admission_date,
                  (SELECT
                      rduTransaction.transaction_date
                    FROM
                      seg_dialysis_request AS rduRequest
                      INNER JOIN seg_dialysis_prebill AS rduPreBill
                      ON rduRequest.encounter_nr = rduPreBill.encounter_nr
                      INNER JOIN seg_dialysis_transaction AS rduTransaction
                      ON rduPreBill.bill_nr = rduTransaction.transaction_nr
                    WHERE rduRequest.encounter_nr = ce.encounter_nr
                    ORDER BY rduTransaction.transaction_date
                    LIMIT 1) AS ADMISSION_DATE2,
                  (SELECT 
                      rduTransaction.`datetime_out` 
                    FROM
                      seg_dialysis_request AS rduRequest 
                      INNER JOIN seg_dialysis_prebill AS rduPreBill 
                        ON rduRequest.encounter_nr = rduPreBill.encounter_nr 
                        AND rduPreBill.bill_type IN ('PH','NPH')  
                      INNER JOIN seg_dialysis_transaction AS rduTransaction 
                        ON rduPreBill.bill_nr = rduTransaction.transaction_nr 
                    WHERE rduRequest.encounter_nr = ce.encounter_nr 
                    ORDER BY rduTransaction.datetime_out DESC 
                    LIMIT 1 )AS DISCHARGE_DATE2,
                  IF(ce.`is_discharged` = 1, DATE_FORMAT(ce.`discharge_date`, '%b %d %Y'), '') AS discharge_date,
                  IF(ce.`is_discharged` = 1, DATE_FORMAT(ce.`discharge_time`, '%h:%i %p'), '') AS discharge_time,
                  IFNULL(sbe.`total_acc_charge`, 0) AS total_acc_charge,
                  IFNULL(sbe.`total_srv_charge`, 0) AS total_srv_charge,
                  IFNULL(sbe.`total_ops_charge`, 0) AS total_ops_charge,
                  IFNULL(sbe.`total_msc_charge`, 0) AS total_msc_charge,
                  IFNULL(sbe.`total_med_charge`, 0) AS total_med_charge,
                  IFNULL(sbe.`total_doc_charge`, 0) AS total_doc_charge,
                  IFNULL(sbc.`total_services_coverage`, 0) AS total_services_coverage,
                  IFNULL(sbc.`total_acc_coverage`, 0) AS total_acc_coverage,
                  IFNULL(sbc.`total_med_coverage`, 0) AS total_med_coverage,
                  IFNULL(sbc.`total_sup_coverage`, 0) AS total_sup_coverage,
                  IFNULL(sbc.`total_srv_coverage`, 0) AS total_srv_coverage,
                  IFNULL(sbc.`total_ops_coverage`, 0) AS total_ops_coverage,
                  IFNULL(sbc.`total_d1_coverage`, 0) AS total_d1_coverage,
                  IFNULL(sbc.`total_d2_coverage`, 0) AS total_d2_coverage,
                  IFNULL(sbc.`total_d3_coverage`, 0) AS total_d3_coverage,
                  IFNULL(sbc.`total_d4_coverage`, 0) AS total_d4_coverage,
                  IFNULL(sbcd.`total_acc_discount`, 0) AS total_acc_discount,
                  IFNULL(sbcd.`total_med_discount`, 0) AS total_med_discount,
                  IFNULL(sbcd.`total_msc_discount`, 0) AS total_msc_discount,
                  IFNULL(sbcd.`total_ops_discount`, 0) AS total_ops_discount,
                  IFNULL(sbcd.`total_srv_discount`, 0) AS total_srv_discount,
                  IFNULL(sbcd.`total_sup_discount`, 0) AS total_sup_discount,
                  IFNULL(sbcd.`total_d1_discount`, 0) AS total_d1_discount, 
                  IFNULL(sbcd.`total_d2_discount`, 0) AS total_d2_discount,
                  IFNULL(sbcd.`total_d3_discount`, 0) AS total_d3_discount,
                  IFNULL(sbcd.`total_d4_discount`, 0) AS total_d4_discount,
                  IFNULL(sbcd.`hospital_income_discount`, 0) AS hospital_income_discount,
                  sbe.`accommodation_type`,
                  ce.`is_medico`
            FROM seg_billing_encounter `sbe`
            INNER JOIN care_encounter `ce`
            ON ce.`encounter_nr` = sbe.`encounter_nr`
            LEFT JOIN seg_billing_coverage `sbc`
            ON sbc.`bill_nr` = sbe.`bill_nr`
            LEFT JOIN seg_billingcomputed_discount `sbcd`
            ON sbcd.`bill_nr` = sbe.`bill_nr`
            WHERE sbe.`is_final` = '1'
            AND (sbe.`is_deleted` IS NULL  OR sbe.`is_deleted` = '0')
            AND DATE(sbe.`bill_dte`) BETWEEN ".$db->qstr(DATE('Y-m-d',$from_date))."AND ".$db->qstr(DATE('Y-m-d',$to_date))." 
             ORDER BY sbe.bill_dte ASC";
$BillingResult = $db->GetAll($query);
for ($i=0; $i < count($BillingResult); $i++) { 

//dischargeDate contact date and time
$dischargeDate = $BillingResult[$i]['DISCHARGE_DATE2']?$BillingResult[$i]['DISCHARGE_DATE2']:($BillingResult[$i]['discharge_date']." ".$BillingResult[$i]['discharge_time']);

//meds from costcenters FS and nnormal
$CostCenterMedsReg = 0;
$CostCenterMedsFS = 0;
$CostCenterMedsSql = "SELECT SUM(IF(sppm.`artikelname` LIKE '%(FS)%' OR sppm.`generic` LIKE '%(FS)%', '0', (spoi.`pricecharge` * spoi.`quantity`))) AS price_noremal,
                        SUM(IF(sppm.`artikelname` != '' OR sppm.`generic` != '',
                                IF(sppm.`artikelname` LIKE '%(FS)%' OR sppm.`generic` LIKE '%(FS)%',
                                  (spoi.`pricecharge` * spoi.`quantity`),
                                  0),
                                0) ) AS price_FS,
                        SUM(IF(sppm.`artikelname` != '' OR sppm.`generic` != '',
                                IF(sppm.`artikelname` LIKE '%(FS)%' OR sppm.`generic` LIKE '%(FS)%',
                                  0,
                                  (spoi.`pricecharge` * spoi.`quantity`)),
                                0) ) AS price_noremal
                FROM seg_pharma_order_items `spoi`
                INNER JOIN seg_pharma_orders  `spo`
                ON spo.`refno` = spoi.`refno`
                INNER JOIN care_pharma_products_main `sppm`
                ON sppm.`bestellnum` = spoi.`bestellnum`
                WHERE spo.`encounter_nr` = ".$db->qstr($BillingResult[$i]['encounter_nr'])." 
                AND spo.`is_cash` = '0'
                AND sppm.`prod_class` = 'M'";
 if($MedsFromCostCenterResult = $db->GetRow($CostCenterMedsSql)){
      $CostCenterMedsReg = $MedsFromCostCenterResult['price_noremal'];
      $CostCenterMedsFS = $MedsFromCostCenterResult['price_FS'];
 }

 
 //meds from billing FS AND Normal
 $BillingMedsReg = 0;
 $BillingMedsFS = 0;
 $BillingMedsSql = "SELECT SUM(IF(sppm.`artikelname` != '' OR sppm.`generic` != '',
                                    IF(sppm.`artikelname` LIKE '%(FS)%' OR sppm.`generic` LIKE '%(FS)%',
                                    (smpd.`quantity` * smpd.`unit_price`),
                                    0),
                               0)) AS price_FS,
                            SUM(IF(sppm.`artikelname` != '' OR sppm.`generic` != '',
                                    IF(sppm.`artikelname` LIKE '%(FS)%' OR sppm.`generic` LIKE '%(FS)%',
                                    0,
                                    (smpd.`quantity` * smpd.`unit_price`))
                                ,0)) AS meds_reg
                    FROM seg_more_phorder `smp` 
                    INNER JOIN seg_more_phorder_details `smpd` 
                      ON smp.`refno` = smpd.`refno` 
                    INNER JOIN care_pharma_products_main `sppm` 
                      ON smpd.`bestellnum` = sppm.`bestellnum` 
                    WHERE smp.`encounter_nr` =". $db->qstr($BillingResult[$i]['encounter_nr'])."
                    AND sppm.`prod_class` = 'M'";
if($BillingMedResult = $db->GetRow($BillingMedsSql)){
      $BillingMedsReg = $BillingMedResult['meds_reg'];
      $BillingMedsFS = $BillingMedResult['price_FS'];
}

$TotalMedsReg = $BillingMedsReg + $CostCenterMedsReg;
$TotalMedsFs = $BillingMedsFS + $CostCenterMedsFS;

//total of all charges
$totalCharges = $BillingResult[$i]['total_acc_charge'] + 
                $TotalMedsFs +
                $TotalMedsReg + 
                $BillingResult[$i]['total_srv_charge'] +
                $BillingResult[$i]['total_ops_charge'] +
                $BillingResult[$i]['total_msc_charge'] +
                $BillingResult[$i]['total_doc_charge'];

//total coverage covered 
$CoveredHCI = 0;
$CoveredPf = 0;
$CoveredAmount = 0;

if($BillingResult[$i]['total_services_coverage']){
  $CoveredHCI = $BillingResult[$i]['total_services_coverage'];
}else{
  $CoveredHCI = ($BillingResult[$i]['total_acc_coverage'] +
                $BillingResult[$i]['total_med_coverage'] +
                $BillingResult[$i]['total_sup_coverage'] +
                $BillingResult[$i]['total_srv_coverage'] +
                $BillingResult[$i]['total_ops_coverage']);
}

$CoveredPf = $BillingResult[$i]['total_d1_coverage'] +
            $BillingResult[$i]['total_d2_coverage'] +
            $BillingResult[$i]['total_d3_coverage'] +
            $BillingResult[$i]['total_d4_coverage'];

$CoveredAmount = $CoveredHCI + $CoveredPf;

//deposit 
$deposit = 0;
$depositSql = "SELECT spr.`amount_due`
                  FROM seg_pay `sp`
                  INNER JOIN seg_pay_request `spr`
                  ON sp.`or_no` = spr.`or_no`
                  WHERE cancel_date IS NULL 
                  AND sp.`encounter_nr` = ". $db->qstr($BillingResult[$i]['encounter_nr']);
$depositResult = $db->GetAll($depositSql);
foreach ($depositResult as  $depositResultvalue) {
  $deposit += $depositResultvalue['amount_due'];
}

//discounts 
 $discounts = ($BillingResult[$i]['total_acc_discount'] +
                  $BillingResult[$i]['total_med_discount'] +
                  $BillingResult[$i]['total_msc_discount'] +
                  $BillingResult[$i]['total_ops_discount'] +
                  $BillingResult[$i]['total_srv_discount'] +
                  $BillingResult[$i]['total_sup_discount'] +
                  $BillingResult[$i]['total_d1_discount'] +
                  $BillingResult[$i]['total_d2_discount'] +
                  $BillingResult[$i]['total_d3_discount'] +
                  $BillingResult[$i]['total_d4_discount'] +
                  $BillingResult[$i]['hospital_income_discount']);

 //for other discount
 //for dependent and infirmary
$dependent = $objBill->isInfirmaryOrDependent($BillingResult[$i]['encounter_nr']);

//for member category
$MemcategorySql = "SELECT sem.`memcategory_id`
                  FROM seg_encounter_memcategory `sem`
                  WHERE sem.`encounter_nr` = ".$db->qstr($BillingResult[$i]['encounter_nr']);
$Memcategory = $db->GetOne($MemcategorySql);

$otherdiscounts = 0;

//for NBB
if($Memcategory == '5' && $BillingResult[$i]['is_medico'] == '0' && $BillingResult[$i]['accommodation_type'] == '1'){
$otherdiscounts  = $totalCharges - ($CoveredAmount + $deposit + $discounts);
$discounts = $discounts + $otherdiscounts;
//for HSM
}else if ($Memcategory == '9' && $BillingResult[$i]['is_medico'] == '0' && $BillingResult[$i]['accommodation_type'] == '1'){
$otherdiscounts  = $totalCharges - ($CoveredAmount + $deposit + $discounts);
$discounts = $discounts + $otherdiscounts;
//for infirmary
}else if ($dependent == "infirmary"){
$otherdiscounts  = $totalCharges - ($CoveredAmount + $deposit + $discounts);
$discounts = $discounts + $otherdiscounts;
//for dependent 
}else if ($dependent == "dependent"){
$otherdiscounts  = $totalCharges - ($CoveredPf + $deposit + $discounts);
$discounts = $discounts + $otherdiscounts;
}

//case rate packages amount
$FistCaseAmount = 0;
$SecondCaseAmount = 0;
$CaseTotalAmount = 0;

$CaseRatePackagesSql = "SELECT sbc.`amount`,
                            sbc.`hci_amount`,
                            sbc.`pf_amount`,
                            sbc.`rate_type`
                      FROM seg_billing_caserate `sbc`
                      WHERE sbc.`bill_nr` = ".$db->qstr($BillingResult[$i]['bill_nr']);
$CaseRatePackagesResult = $db->GetAll($CaseRatePackagesSql);
foreach ($CaseRatePackagesResult as $CaseRatePack) {
  if($CaseRatePack['rate_type'] == '1'){
    $FistCaseAmount = $CaseRatePack['amount'];
  }else{
    $SecondCaseAmount = $CaseRatePack['amount'];
  }
}

$CaseTotalAmount = $FistCaseAmount + $SecondCaseAmount;

//excess
$excess = $totalCharges - ($CoveredAmount + $discounts + $deposit);
// number_format("1000000",2);
$data[$i] = array('bill_date'=>date('Y-m-d h:i A', strtotime($BillingResult[$i]['bill_dte'])),
                  'bill_no'=>$BillingResult[$i]['bill_nr'],
                  'patient_name'=>utf8_decode(trim($BillingResult[$i]['patient_name'])),
                  'prepared_by'=>utf8_decode(trim($BillingResult[$i]['prep_by'])),
                  'case_number'=>$BillingResult[$i]['encounter_nr'],
                  'admission'=>$BillingResult[$i]['ADMISSION_DATE2']?$BillingResult[$i]['ADMISSION_DATE2']:$BillingResult[$i]['admission_date'],
                  'discharged'=>$dischargeDate,
                  'room_board'=>number_format($BillingResult[$i]['total_acc_charge'], 2),
                  'drugs_meds_reg'=>number_format($TotalMedsReg, 2),
                  'drugs_meds_fs'=>number_format($TotalMedsFs, 2),
                  'xlo'=>number_format($BillingResult[$i]['total_srv_charge'], 2),
                  'or_dr'=>number_format($BillingResult[$i]['total_ops_charge'], 2),
                  'miscellaneous'=>number_format($BillingResult[$i]['total_msc_charge'], 2),
                  'pf'=>number_format($BillingResult[$i]['total_doc_charge'], 2),
                  'total'=>number_format($totalCharges, 2),
                  'hci'=>number_format($CoveredHCI, 2),
                  'pf_2'=>number_format($CoveredPf, 2),
                  'total_2'=>number_format($CoveredAmount, 2),
                  'deposit'=>number_format($deposit, 2),
                  'discounts'=>number_format($discounts, 2),
                  'first_case'=>number_format($FistCaseAmount, 2),
                  'second_case'=>number_format($SecondCaseAmount, 2),
                  'total_3'=>number_format($CaseTotalAmount, 2),
                  'excess'=>number_format($excess, 2),
);

}
                    
                   

?>