<?php
ini_set('memory_limit', '2048M');

/*
#created by Darryl 2017/03-09/


*/
require_once('roots.php');
require_once $root_path . 'include/inc_environment_global.php';
include 'parameters.php';

global $db;
$where = array();
$type_code = array();
$ENCOUNTER_TYPE_DIALYSIS = DIALYSIS_PATIENT;
define(Charity, 1);
define(Payward, 2);
define(PrivateCase, 1);
define(HouseCase, 2);
define(Service_Confinement, 1);
define(Payward_Confinement, 2);
define(Payward_HouseCase, 3);
define(Payward_PrivateCase, 4);
define(DIALYSIS,5);
define(ISNBB, 1);


// $condition2 = explode(',', $_GET['param']);
// $condition3_ICD_code = explode('--', $condition2[2]);
// $condition4_ICP_code = explode('--', $condition2[1]);
$where_HAVING ="";
// $style_font_ICD=$condition3_ICD_code[1];
// $style_font_ICP =$condition4_ICP_code[1];

// if (count($condition2) ==1) {
// 	$condition2 = explode(',', $_GET['param']);
// 	$condition3_NEW = explode('--', $condition2[0]);
// 		if ($condition3_NEW[0]=='param_top_15_icd'){
// 			$style_font_ICP ="No selected code";
// 			$style_font_ICD=$condition3_NEW[1];

// 		}
// 		else if ($condition3_NEW[0]=='param_top_15_icp'){
// 			$style_font_ICD ="No selected code";
// 			$style_font_ICP=$condition3_NEW[1];
// 		}
// }

if($icd_value ==null || $icp_value == null ){
	if($icd_value !=null || $icp_value != null){
    	$where_HAVING  = "HAVING first_code IN(".$db->qstr($icd_value).",".$db->qstr($icp_value).")";
	}
	if($icd_value == null){
		$icd_value = 'All';
	}
	if($icp_value == null){
		$icp_value = 'All';

	}
	$type ="ICD code:".$icd_value." AND ICP code: ".$icp_value.", Filtered both code, 1st Case Rate Code OR 2nd CaseRate";
	
}
// var_dump($type);exit;
// e: ".$style_font_ICP." Filtered both code, 1st Case Rate
// Code OR 2nd Case
// Rate";
// } 

//date
$where[] = "DATE(fb.bill_dte)
               BETWEEN
                    DATE(" . $db->qstr(date('Y-m-d', $from_date)) . ")
               AND
                    DATE(" . $db->qstr(date('Y-m-d', $to_date)) . ") ";

if (!isset($mem_cats) || $mem_cats == 'all') {
} else {
    $where[] = "sm.memcategory_code in (" . $mem_cats .")";
}



$condition = implode(') AND (', $where);
$params->put('hosp_country', $hosp_country);
$params->put('hosp_agency', $hosp_agency);
$params->put('hosp_name', $hosp_name);
$params->put('hosp_addr1', $hosp_addr1);
$params->put('date_span', "From " . date('M d, Y', $from_date) . " to " . date('M d, Y', $to_date));
$params->put('delete_type', $type);
$params->put('title',$report_title);

//modified by EJ 12/31/2014
$query = "SELECT DISTINCT
			e.encounter_type,
			  fb.bill_nr,
			  fb.bill_dte,
			  fu.name prepared_by,
			   CONCAT(TRIM(cp.name_last), ', ',TRIM(cp.name_first),' ', 
			IF(
				TRIM(cp.name_middle)<>'',
				CONCAT(
					LEFT(TRIM(cp.name_middle),1),
					'.'
					),
				''),
			'') AS patient,
			  e.encounter_nr,
				(SELECT 
			    rduTransaction.transaction_date 
			  FROM
			    seg_dialysis_prebill AS rduPreBill 
			    INNER JOIN seg_dialysis_transaction AS rduTransaction 
			      ON rduPreBill.bill_nr = rduTransaction.transaction_nr 
			  WHERE rduPreBill.encounter_nr = e.encounter_nr 
			  ORDER BY rduTransaction.transaction_date 
			  LIMIT 1) AS ADMISSION_DATE2,
			  (SELECT 
			    rduTransaction.`datetime_out` 
			  FROM
			    seg_dialysis_prebill AS rduPreBill 
			    INNER JOIN seg_dialysis_transaction AS rduTransaction 
			      ON rduPreBill.bill_nr = rduTransaction.transaction_nr 
			  WHERE rduPreBill.encounter_nr = e.encounter_nr 
			  AND rduPreBill.bill_type IN ('PH', 'NPH') 
			  ORDER BY rduTransaction.datetime_out DESC 
			  LIMIT 1) AS DISCHARGE_DATE3,
			  department.id department,
			  fb.total_acc_charge AS acc_charge,
			  fb.total_med_charge AS med_charge,
			  fb.total_srv_charge AS srv_charge,
			  fb.total_ops_charge AS ops_charge,
			  fb.total_doc_charge AS doc_charge,
			  fb.total_msc_charge AS msc_charge,
			  dsc.hospital_income_discount AS part_discount,
			  CONCAT(
			    IF (
			      e.encounter_type IN (3, 4),
			      IF(
			        fb.accommodation_type = 1,
			        'Service',
			        'Pay'
			      ),
			      et.type
			    ),
			    IF(
			      (
			        IFNULL(cov.total_acc_coverage, 0) + IFNULL(cov.total_med_coverage, 0) + IFNULL(cov.total_srv_coverage, 0) + IFNULL(cov.total_ops_coverage, 0) + IFNULL(cov.total_msc_coverage, 0) + IFNULL(cov.total_d1_coverage, 0) + IFNULL(cov.total_d2_coverage, 0) + IFNULL(cov.total_d3_coverage, 0) + IFNULL(cov.total_d4_coverage, 0) + IFNULL(cov.total_services_coverage, 0)
			      ) > 0,
			      '/PHIC',
			      ''
			    )
			  ) TYPE,
			  (
			    fb.total_acc_charge + fb.total_med_charge + fb.total_srv_charge + fb.total_ops_charge + fb.total_doc_charge + fb.total_msc_charge
			  ) total_charge,
			  (
			    (
			      IFNULL(dsc.total_acc_discount, 0) + IFNULL(dsc.total_med_discount, 0) + IFNULL(dsc.total_ops_discount, 0) + IFNULL(dsc.total_srv_discount, 0) + IFNULL(dsc.total_msc_discount, 0) + IFNULL(dsc.total_d1_discount, 0) + IFNULL(dsc.total_d2_discount, 0) + IFNULL(dsc.total_d3_discount, 0) + IFNULL(dsc.total_d4_discount, 0)
			    ) + IFNULL(dsc.hospital_income_discount, 0)
			  ) total_discount,
			  (
			    IFNULL(cov.total_acc_coverage, 0) + IFNULL(cov.total_med_coverage, 0) + IFNULL(cov.total_srv_coverage, 0) + IFNULL(cov.total_ops_coverage, 0) + IFNULL(cov.total_msc_coverage, 0) + IFNULL(cov.total_services_coverage, 0)
			  ) hci,
			  (
			    IFNULL(cov.total_d1_coverage, 0) + IFNULL(cov.total_d2_coverage, 0) + IFNULL(cov.total_d3_coverage, 0) + IFNULL(cov.total_d4_coverage, 0)
			  ) doc_pf,
			  fb.total_prevpayments previous_payment,
			  IFNULL(
			    (SELECT 
			      sbc.amount 
			    FROM
			      seg_billing_caserate sbc 
			    WHERE sbc.rate_type = '1' 
			      AND sbc.bill_nr = fb.bill_nr  LIMIT 1),
			    0
			  ) AS cs_first,
			  IFNULL(
			    (SELECT 
			      sbc.amount 
			    FROM
			      seg_billing_caserate sbc 
			    WHERE sbc.rate_type = '2' 
			      AND sbc.bill_nr = fb.bill_nr  LIMIT 1),
			    0
			  ) AS cs_second,
			  IFNULL(
			    (SELECT 
			      sbc.package_id 
			    FROM
			      seg_billing_caserate sbc 
			    WHERE sbc.rate_type = '1' 
			      AND sbc.bill_nr = fb.bill_nr  LIMIT 1),
			    ' '
			  ) AS first_code,
			  IFNULL(
			    (SELECT 
			      sbc.package_id 
			    FROM
			      seg_billing_caserate sbc 
			    WHERE sbc.rate_type = '2' 
			      AND sbc.bill_nr = fb.bill_nr LIMIT 1),
			    ' '
			  ) AS second_code,
			  /*fn_billing_compute_gross_amount (fb.bill_nr) excess, g comment nako kay slow kaayo.*/
			   (SELECT
	   IFNULL(b.total_msc_charge,0)-IFNULL(c.total_msc_coverage,0)-IFNULL(d.total_msc_discount,0) FROM seg_billing_encounter AS b
			LEFT JOIN seg_billing_coverage AS c ON b.bill_nr=c.bill_nr AND c.hcare_id = '18'
			LEFT JOIN seg_billingcomputed_discount AS d ON b.bill_nr=d.bill_nr
			LEFT JOIN seg_billing_discount bd ON b.bill_nr=bd.bill_nr AND bd.is_deleted <>'1'
		WHERE b.bill_nr=fb.bill_nr AND b.is_deleted IS NULL ) AS msc_gross,
		(SELECT
	   IFNULL(b.total_acc_charge,0) + IFNULL(b.total_med_charge,0) + 
			IFNULL(b.total_srv_charge,0) + IFNULL(b.total_ops_charge,0) -
                    	IFNULL(c.total_services_coverage,0) - IFNULL(d.hospital_income_discount,0) +
                    	 IFNULL(b.total_doc_charge,0)-IFNULL(c.total_d1_coverage,0)-IFNULL(d.total_d1_discount,0)
			-IFNULL(c.total_d2_coverage,0)-IFNULL(d.total_d2_discount,0)
			-IFNULL(c.total_d3_coverage,0)-IFNULL(d.total_d3_discount,0)
			-IFNULL(c.total_d4_coverage,0)-IFNULL(d.total_d4_discount,0) - IFNULL(d.professional_income_discount,0)
			- IFNULL(b.total_prevpayments,0) FROM seg_billing_encounter AS b
			LEFT JOIN seg_billing_coverage AS c ON b.bill_nr=c.bill_nr AND c.hcare_id = '18'
			LEFT JOIN seg_billingcomputed_discount AS d ON b.bill_nr=d.bill_nr
			LEFT JOIN seg_billing_discount bd ON b.bill_nr=bd.bill_nr AND bd.is_deleted <>'1'
		WHERE b.bill_nr=fb.bill_nr AND b.is_deleted IS NULL ) AS hci_gross,
			  IF(i.encounter_nr,sm.memcategory_desc,'') AS phic_category,
			  IF(e.is_medico = '1', 'YES', 'NO') AS medico_legal,
			  bd.discountid AS discountID,
			  IF(e.encounter_type=$ENCOUNTER_TYPE_DIALYSIS,
				  (SELECT
					  rduTransaction.transaction_date
					FROM
					  seg_dialysis_request AS rduRequest
					  INNER JOIN seg_dialysis_prebill AS rduPreBill
						ON rduRequest.encounter_nr = rduPreBill.encounter_nr
						AND rduPreBill.bill_type = 'PH'
					  INNER JOIN seg_dialysis_transaction AS rduTransaction
						ON rduPreBill.bill_nr = rduTransaction.transaction_nr
					WHERE rduRequest.encounter_nr = fb.encounter_nr
					ORDER BY rduTransaction.transaction_date ASC
					LIMIT 1),
			  IFNULL(
			    e.admission_dt,
			    e.encounter_date
				  )
			  ) AS ADMISSION_DATE,
			  e.mgh_setdte AS DISCHARGE_DATE,
			  IF(e.encounter_type=$ENCOUNTER_TYPE_DIALYSIS,
			  	(SELECT 
				  COUNT(transactions.transaction_nr) AS cnt
				FROM
				  seg_dialysis_prebill AS prebill
				  INNER JOIN seg_dialysis_transaction AS transactions
					ON prebill.bill_nr = transactions.transaction_nr
				WHERE prebill.`encounter_nr` = fb.encounter_nr
				AND prebill.bill_type = 'PH')
			  	,
			  DATEDIFF(
			    DATE(fb.bill_dte),
			    DATE(
			      IFNULL(
			        e.admission_dt,
			        e.encounter_date
			      )
			    )
			  	)  
			  ) AS NUMBER_OF_DAYS,
			  fb.is_deleted,
			  fb.is_final,
			  fb.discount_type AS discount,
			  (SELECT
			    discountid
			  FROM seg_charity_grants_pid
			  WHERE pid = e.pid AND discountid = 'PHS'
			  LIMIT 1) discountid,
			  sm.memcategory_id,
			  e.pid,
			  st.transmit_no as transmit_no,
			  DATE(st.transmit_dte) as transmit_date,
			  IF(IF(st.transmit_no IS NULL, IF(cp.death_date='0000-00-00',e.`mgh_setdte`,CONCAT(cp.death_date,' ',cp.death_time)), e.`mgh_setdte`)='0000-00-00 00:00:00',fb.bill_dte,IF(st.transmit_no IS NULL, IF(cp.death_date='0000-00-00',e.`mgh_setdte`,CONCAT(cp.death_date,' ',cp.death_time)), e.`mgh_setdte`)) AS DISCHARGE_DATE2,
			  
			  CONCAT(cp.death_date,' ',cp.death_time) DEATH_DATE,
			  
			  e.`mgh_setdte` MGH_DATE,
			   sbd.discountid AS SC_ID,  
  			  sbd.discount AS SC_DISC,  
  			  sm.isnbb As  isnbb  
			FROM
			  seg_billing_encounter fb 
			  INNER JOIN care_encounter e 
			    ON e.encounter_nr = fb.encounter_nr 
				 LEFT JOIN seg_encounter_case AS sec
			  ON e.encounter_nr = sec.encounter_nr
			  AND sec.is_deleted NOT IN ('1')
			  LEFT JOIN care_ward AS cw 
			  ON cw.nr = e.current_ward_nr
			  LEFT JOIN seg_billingapplied_discount  AS sbd  
    			ON  sbd.encounter_nr = e.encounter_nr 
			  LEFT JOIN care_department department 
			    ON department.nr = e.current_dept_nr 
			  INNER JOIN seg_encounter_insurance i 
			    ON i.encounter_nr = e.encounter_nr 
			    AND hcare_id = '18' 
			  LEFT JOIN care_type_encounter et 
			    ON e.encounter_type = et.type_nr 
			  LEFT JOIN seg_billing_coverage cov 
			    ON cov.bill_nr = fb.bill_nr 
			  LEFT JOIN seg_billingcomputed_discount dsc 
			    ON dsc.bill_nr = fb.bill_nr 
			  LEFT JOIN seg_billing_discount bd 
			    ON bd.bill_nr = fb.bill_nr 
			  LEFT JOIN seg_discount dd 
			    ON dd.discountid = bd.discountid 
			  LEFT JOIN care_users fu 
			    ON fb.modify_id = fu.login_id 
			  LEFT JOIN seg_encounter_memcategory sem 
			    ON sem.encounter_nr = e.encounter_nr 
			  LEFT JOIN seg_memcategory sm 
			    ON sm.memcategory_id = sem.memcategory_id
				LEFT JOIN seg_transmittal_details stdls
			    ON e.encounter_nr = stdls.encounter_nr
			  LEFT JOIN seg_transmittal st
			    ON stdls.transmit_no = st.transmit_no
			  LEFT JOIN care_person cp
			    ON e.pid = cp.pid
				WHERE  fb.is_final ='1'  AND fb.is_deleted IS NULL AND ($condition)".$where_HAVING;
#echo $query;die();
$rs = $db->Execute($query);
if ($rs) {
    if ($rs->RecordCount() > 0) {
        $i = 0;
        while ($row = $rs->FetchRow()) {

            $status = array();
            if ($row['is_deleted'] == '1') {
                $status[] = '<strong style="color:#FF0000;">Cancelled</strong>';
            }
            if ($row['is_final'] == '1') {
                $status[] = 'Final';
            } else {
                $status[] = '<strong style="color:#FF0000;">Not Final</strong>';
            }
            $status = implode('/', $status);

            if (($row['classification'] == 'Infirmary') || ($row['classification'] == 'Senior Citizen')) {
                $ClassificationShow = $row['classification'];
                $totalDiscount = (float)$row['discount_fixed'] ?
                    $row['discount_fixed'] :
                    ((float)$row['msc_gross'] + (float)$row['hci_gross']) * $row['discount_pct'];
                $AmountDueShow = (float)$row['msc_gross'] + (float)$row['hci_gross'] - $totalDiscount;
                $OrNumberShow = $row['or'];
                $OrDateShow = $row['or_date'] ? date('Y-m-d h:i A', strtotime($row['or_date'])) : '-';
                $AmountPayableShow = $row['or_amount'];
                $ClerkShow = $row['or_clerk'];

            } else {
                $ClassificationShow = '';
                $totalDiscount = '';
                $AmountDueShow = '';
                $OrNumberShow = '';
                $OrDateShow = '';
                $AmountPayableShow = '';
                $ClerkShow = '';
            }

            // if ($row['DISCHARGE_DATE'] == '0000-00-00 00:00:00' || is_null($row['DISCHARGE_DATE'])) {
            //     $discharge_date = '';
            //     $num_days = '';
            // } else {
            //     $discharge_date = $row['DISCHARGE_DATE'];
            //     $num_days = $row['NUMBER_OF_DAYS'];
            // }

            //exclude NBB,HSM,PHS,PS
            if($row['discount'] && ($row['memcategory_id'] != '5' && $row['memcategory_id'] != '9' && $row['discountid'] != 'PHS')){
            	$serve_charge = $row['acc_charge'] + $row['med_charge'] +
                            $row['srv_charge'] + $row['ops_charge'] + $row['msc_charge'];
	            $pf_charge = $row['doc_charge'];
	            //$serve_excess = $serve_charge - ($row['part_discount'] + $row['hci']);
	            //$pf_excess = $pf_charge - $row['doc_pf'];
                $dependent = $serve_charge - ($row['part_discount'] + $row['hci']);
                $infirmary = ($serve_charge + $pf_charge) - ($row['total_discount'] + ($row['hci'] + $row['doc_pf']));
                $discount_desc = $row['discount'];
	            if($row['discount'] == 'infirmary'){
								$discount_amount = $infirmary;
								$infirmary_data = $infirmary;
	            }else if($row['discount'] == 'dependent'){
					$discount_amount = $dependent;
						$infirmary_dependent = $infirmary;
	            }
                else{
                    $discount_desc = null;
	            	$discount_amount = null;
	            }
            }
            // else if($row['discountID'] && ($row['memcategory_id'] == '5' || $row['memcategory_id'] == '6' || $row['memcategory_id'] == '9' ||
            //         $row['memcategory_id'] == '10' || $row['memcategory_id'] == '11')){
			 else if($row['discountID'] && ($row['isnbb']==ISNBB)){
                $serve_charge = $row['acc_charge'] + $row['med_charge'] +
                    $row['srv_charge'] + $row['ops_charge'] + $row['msc_charge'];
                $pf_charge = $row['doc_charge'];
                $nbb = ($serve_charge + $pf_charge) - ($row['total_discount'] + ($row['hci'] + $row['doc_pf']));

                $discount_desc = 'NBB';
				if($row['previous_payment'] >= $nbb)
					$discount_amount = 0;
				else
					$discount_amount = $nbb - $row['previous_payment'];
            }
            else{
                $discount_desc = null;
            	$discount_amount = null;
						}
						
            $AmountDueShowSC =0;
            $AmountDueShowDesc ="";
            if ($row['SC_ID'] == 'SC' || $row['SC_ID'] !="") {
            		 $AmountDueShowSC =  ((float)$row['total_charge']) * $row['SC_DISC'];
            		 $AmountDueShowDesc ="Senior Citizen";
            }
            $patientEncounterNameSql = "SELECT name_first, name_middle, name_last
                							FROM seg_encounter_name `sen`
                							WHERE sen.`encounter_nr` = ".$db->qstr($row['encounter_nr'])."
                							AND sen.`pid` = ".$db->qstr($row['pid']);
            $patientEncounterNameResult = $db->GetRow($patientEncounterNameSql);

            if($patientEncounterNameResult){
            	$patient_name = $patientEncounterNameResult['name_last'].", ".$patientEncounterNameResult['name_first']." ".$patientEncounterNameResult['name_middle'].".";
            }else{
            	$patient_name = $row['patient'];
            }

			if($row['encounter_type'] == DIALYSIS_PATIENT) {
				$dischargeDate = date('m-d-Y h:i A', strtotime($row['DISCHARGE_DATE3']?$row['DISCHARGE_DATE3']:$row['DISCHARGE_DATE']));
			} else {
				$dischargeDate = date('m-d-Y h:i A', strtotime($row['bill_dte']));
			}
		
			$dependent_row = $db->GetOne("SELECT amount FROM seg_credit_collection_ledger AS ledger
                             	LEFT JOIN seg_grant_account_type AS accountType
                              	ON ledger.pay_type = accountType.type_name WHERE is_deleted = 0
                              	AND pay_type = 'dependent'
                             	AND bill_nr =".$db->qstr($row['bill_nr']));
			$dependent_amount = $dependent_row;

			$infirmary_row = $db->GetOne("SELECT amount FROM seg_credit_collection_ledger AS ledger
                             	LEFT JOIN seg_grant_account_type AS accountType
                              	ON ledger.pay_type = accountType.type_name WHERE is_deleted = 0
                              	AND pay_type = 'infirmary'
                             	AND bill_nr =".$db->qstr($row['bill_nr']));	
			$infirmary_amount = $infirmary_row;

           $data[$i] = array(
                'bill_ref' => $row['bill_nr'] . (($row['is_deleted']) ? ' - D' : ''),
                'bill_date' => date('m-d-Y h:i A', strtotime($row['bill_dte'])),
                'prepared_by' => $row['prepared_by'],
                'patient_name' => utf8_decode(trim(ucwords($patient_name))),
                'case_no' => $row['encounter_nr'],
                'department' => $row['department'],
                'type' => $row['TYPE'],
                'actual_charges' => (double)$row['total_charge'],
                'discount' => (double)$row['total_discount'],
                'phic_coverage' => (double)$row['hci'] + (double)$row['doc_pf'],
                'deposit' => (double)$row['previous_payment'],
                'cs_first' => (double)$row['cs_first'],
                'cs_second' => (double)$row['cs_second'],
                'total_package' => (double)$row['cs_first'] + (double)$row['cs_second'],
                'hci' => (double)$row['hci'],
                'doc_pf' => (double)$row['doc_pf'],
                'excess' => (($row['discountid'] == 'NBB' || $row['discountid'] == 'HSM') ? (double)0.00 : (double)$row['msc_gross'] + (double)$row['hci_gross']),
                'phic_category' => $row['phic_category'],
                'medico_legal' => $row['medico_legal'],
                'first_code' => $row['first_code'],
                'second_code' => $row['second_code'],
                'nbb_excess' => (($row['discountid'] == 'NBB' || $row['discountid'] == 'HSM') ? (double)$row['msc_gross'] + (double)$row['hci_gross'] : (double)0.00),
                'admission_date' => date('m-d-Y h:i A',strtotime($row['ADMISSION_DATE2']?$row['ADMISSION_DATE2']:$row['ADMISSION_DATE'])),
                'discharge_date' => $dischargeDate,
                'num_days' => $num_days,
                'status' => $status,
                'acc_charge' => (double)$row['acc_charge'],
                'med_charge' => (double)$row['med_charge'],
                'ops_charge' => (double)($row['ops_charge'] + $row['srv_charge']),
                'doc_charge' => (double)$row['doc_charge'],
                'msc_charge' => (double)$row['msc_charge'],
                'discount_name' => ucwords($discount_desc) == "" ? $AmountDueShowDesc : ucwords($discount_desc),
                /*'discount_amount' => $discount_amount,*/
                'transmit_no' => $row['transmit_no'],
                'transmit_date' => $row['transmit_date'],
                'discharge_date2' => date('m-d-Y h:i A', strtotime($row['DISCHARGE_DATE3']?$row['DISCHARGE_DATE3']:$row['DISCHARGE_DATE2'])),
                'disc_senior' => (double)$AmountDueShowSC == 0.00 ? null: (double)$AmountDueShowSC,
                'disc_infirmary' => ((ucwords($discount_desc) == 'Infirmary') ? (double)$infirmary_amount : null),
                'disc_infirmary_dept' => ((ucwords($discount_desc) == 'Dependent') ? (double)$dependent_amount: null),
                'disc_nbb' => ((ucwords($discount_desc) == 'NBB') ? (double)$discount_amount : null),
                'num_days' => $row['NUMBER_OF_DAYS']
            );
            $i++;
        }
    } else {
        $data['bill_ref'][0] = "No data";
    }
} else {
    $data['bill_ref'][0] = "No data";
}
//exit;
