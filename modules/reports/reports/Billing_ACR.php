<?php
ini_set('memory_limit', '2048M');

#created by Nick, 1/30/2014
require_once('roots.php');
require_once $root_path . 'include/inc_environment_global.php';
include 'parameters.php';

global $db;
define(Charity, 1);
define(Payward, 2);
define(PrivateCase, 1);
define(HouseCase, 2);
define(Service_Confinement, 1);
define(Payward_Confinement, 2);
define(Payward_HouseCase, 3);
define(Payward_PrivateCase, 4);
define(DIALYSIS,5);
$where = array();

$ENCOUNTER_TYPE_DIALYSIS = DIALYSIS_PATIENT;
define(ISNBB, 1);

//bill status
if (!isset($billing_status) || $billing_status == 'all') {
    $header_dtype = "All Bills";
} else if ($billing_status == 'deleted') {
    $header_dtype = "Deleted Bills";
    $where[] = "fb.is_deleted = '1'";
} else if ($billing_status == 'final') {
    $header_dtype = "Final Bills";
    $where[] = "fb.is_final = 1 AND fb.is_deleted IS NULL";
}
#ar_dump($casetype_confinement);die();

//conf
if (!isset($casetype_confinement)||$casetype_confinement=='all') {
	 $header_dtype = "All Bills";
#	 $where[] = "sec.is_deleted NOT IN ('1')";
}else{
	if($casetype_confinement==Service_Confinement){
		$header_dtype = "Service Confinement Type";
		//$where[] = "sec.casetype_id = ".$db->qstr($casetype_confinement)."";
		#$where[] = "fb.accommodation_type = ".$db->qstr($casetype_confinement)."";
		$where[] = "IF(e.encounter_type IN (".DIALYSIS."),sec.casetype_id NOT IN (".PrivateCase."),fb.accommodation_type NOT IN (".Payward."))"; //for patient with service confinement type and also for the patient w/o confinement type
	}else if($casetype_confinement==Payward_Confinement){
		$header_dtype = "All Payward Confinement Type";
		$where[] = "fb.accommodation_type=".$db->qstr($casetype_confinement)."  OR (e.encounter_type IN (".DIALYSIS.")  AND sec.casetype_id IN (".PrivateCase."))";

	}else if($casetype_confinement==Payward_HouseCase){
		$header_dtype = "Payward House Case Type";
		$where[] = " fb.accommodation_type IN (".Payward.") AND (sec.casetype_id NOT IN (".PrivateCase.") OR sec.casetype_id IS NULL)";
	}else if($casetype_confinement==Payward_PrivateCase){
		$header_dtype = "Payward Private Case Type";
		$where[] = "(fb.accommodation_type IN (".Payward.") AND sec.casetype_id IN (".PrivateCase.")) OR (e.encounter_type IN (".DIALYSIS.")  AND sec.casetype_id IN (".PrivateCase.")) ";
	}
}

//philhealth
if ($billing_insurance == 'ph') {
    $header_dtype .= " - PhilHealth";
    $where[] = "cov.hcare_id = '18'";
} else if ($billing_insurance == 'nph') {
    $header_dtype .= " - Non-PhilHealth";
    $where[] = "cov.hcare_id <> '18' OR cov.hcare_id IS NULL";
}

//date
$where[] = "DATE(fb.bill_dte)
               BETWEEN
                    DATE(" . $db->qstr(date('Y-m-d', $from_date)) . ")
               AND
                    DATE(" . $db->qstr(date('Y-m-d', $to_date)) . ")";

//encoder
if (!isset($billing_encoder) || $billing_encoder == 'all') {
} else {
    $where[] = "fu.personell_nr = " . $db->qstr($billing_encoder);
}

//encoder
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
$params->put('delete_type', $header_dtype);
$params->put('delete_type', $header_dtype);
$params->put('membership_type', "From ".$mem_cats_details. " category type(s)");

//modified by EJ 12/31/2014
//modified by Kenneth 05-18-2016
$query = "SELECT DISTINCT
			e.encounter_type,
			  fb.bill_nr,
			  fb.bill_dte,
			  fu.name prepared_by,
			  fn_get_person_name (e.pid) patient,
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
			      AND sbc.bill_nr = fb.bill_nr LIMIT 1),
			    0
			  ) AS cs_first,
			  IFNULL(
			    (SELECT 
			      sbc.amount 
			    FROM
			      seg_billing_caserate sbc 
			    WHERE sbc.rate_type = '2' 
			      AND sbc.bill_nr = fb.bill_nr LIMIT 1),
			    0
			  ) AS cs_second,
			  IFNULL(
			    (SELECT 
			      sbc.package_id 
			    FROM
			      seg_billing_caserate sbc 
			    WHERE sbc.rate_type = '1' 
			      AND sbc.bill_nr = fb.bill_nr LIMIT 1),
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
			  fn_billing_compute_gross_amount (fb.bill_nr) excess,
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
			   sbd.discountid AS SC_ID,  /*added by MARK 2016-10-15*/
  			  sbd.discount AS SC_DISC,   /*added by MARK 2016-10-15*/
  			  sm.isnbb As  isnbb    /* added by Matsuu 08032017 */
			FROM
			  seg_billing_encounter fb 
			  INNER JOIN care_encounter e 
			    ON e.encounter_nr = fb.encounter_nr 
			  LEFT JOIN seg_encounter_case AS sec
			  ON e.encounter_nr = sec.encounter_nr
			  AND sec.is_deleted NOT IN ('1')
			  LEFT JOIN care_ward AS cw 
			  ON cw.nr = e.current_ward_nr
			  LEFT JOIN seg_billingapplied_discount  AS sbd   /*added by MARK 2016-10-15*/
    			ON  sbd.encounter_nr = e.encounter_nr 
			  LEFT JOIN care_department department 
			    ON department.nr = e.current_dept_nr 
			  LEFT JOIN seg_encounter_insurance i 
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
			WHERE ($condition) ORDER BY fb.bill_dte ASC";
#var_dump($query);die();
			/*commented just sample*/
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
                    ((float)$row['excess']) * $row['discount_pct'];
                $AmountDueShow = (float)$row['excess'] - $totalDiscount;
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
            //     $dischargeDate = '';
            //     $num_days = '';
            // } else {
            //     $dischargeDate = $row['DISCHARGE_DATE'];
            //     $num_days = $row['NUMBER_OF_DAYS'];
            // }
            // if ($row['DISCHARGE_DATE2'] == '0000-00-00 00:00:00' || is_null($row['DISCHARGE_DATE2'])) {
            //     $dischargeDate2 = '';
            //     $num_days = '';
            // } else {
            //     $dischargeDate2 = $row['DISCHARGE_DATE2'];
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
	            	$infirmary_data = $infirmary; /*added by MARK 2016-10-15*/
	            }else if($row['discount'] == 'dependent'){
					$discount_amount = $dependent;
					$infirmary_dependent = $infirmary; /*added by MARK 2016-10-15*/
	            }else{
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
            /*added by MARK 2016-10-15*/
            $AmountDueShowSC =0;
            $AmountDueShowDesc ="";
            if ($row['SC_ID'] == 'SC' || $row['SC_ID'] !="") {
            		 $AmountDueShowSC =  ((float)$row['total_charge']) * $row['SC_DISC'];
            		 $AmountDueShowDesc ="Senior Citizen";
            }
            /*END added by MARK 2016-10-15*/
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
			/*Start added by Mark April 17,2017*/
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
			/*END added by MARK April 17,2017*/

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
                'excess' => (($row['discountid'] == 'NBB' || $row['discountid'] == 'HSM') ? (double)0.00 : (double)$row['excess']),
                'phic_category' => $row['phic_category'],
                'medico_legal' => $row['medico_legal'],
                'first_code' => $row['first_code'],
                'second_code' => $row['second_code'],
                'nbb_excess' => (($row['discountid'] == 'NBB' || $row['discountid'] == 'HSM') ? (double)$row['excess'] : (double)0.00),
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
            );
            $i++;
        }
    } else {
        $data['bill_ref'][0] = "No data";
    }
} else {
    $data['bill_ref'][0] = "No data";
}
// exit;
