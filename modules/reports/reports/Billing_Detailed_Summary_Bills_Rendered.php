<?php
/**
 * @author Nick B. Alcala 5-25-2015
 */
include 'roots.php';
include 'parameters.php';
include_once $root_path.'include/inc_environment_global.php';

$params->put('hosp_agency',$hosp_agency);
$params->put('hosp_name',$hosp_name);
$params->put('hosp_addr1',$hosp_addr1);
$params->put('date_span',"From " . date('M d, Y',$from_date) . " to " . date('M d, Y',$to_date));

global $db;

$sql = <<<SQL
SELECT
	DATE_FORMAT(bill.bill_dte,'%m/%d/%Y') AS bill_date,
	bill.bill_nr AS bill_number,
	bill.encounter_nr AS encounter_number,		#added by gelie 09/12/2015
			  IF(encounter.encounter_type='5',DATE_FORMAT((SELECT
					  rduTransaction.transaction_date
					FROM
					  seg_dialysis_request AS rduRequest
					  INNER JOIN seg_dialysis_prebill AS rduPreBill
						ON rduRequest.encounter_nr = rduPreBill.encounter_nr
					  INNER JOIN seg_dialysis_transaction AS rduTransaction
						ON rduPreBill.bill_nr = rduTransaction.transaction_nr
					WHERE rduRequest.encounter_nr = encounter.encounter_nr
					ORDER BY rduTransaction.transaction_date
					LIMIT 1),'%m/%d/%Y'),DATE_FORMAT(encounter.encounter_date,'%m/%d/%Y')) AS admission_date,
			  IF(encounter.encounter_type='5',DATE_FORMAT((SELECT 
			      rduTransaction.`datetime_out` 
			    FROM
			      seg_dialysis_request AS rduRequest 
			      INNER JOIN seg_dialysis_prebill AS rduPreBill 
			        ON rduRequest.encounter_nr = rduPreBill.encounter_nr 
			        AND rduPreBill.bill_type IN ('PH','NPH')  
			      INNER JOIN seg_dialysis_transaction AS rduTransaction 
			        ON rduPreBill.bill_nr = rduTransaction.transaction_nr 
			    WHERE rduRequest.encounter_nr = encounter.encounter_nr 
			    ORDER BY rduTransaction.datetime_out DESC 
			    LIMIT 1 ),'%m/%d/%Y'),DATE_FORMAT(encounter.discharge_date,'%m/%d/%Y')) AS discharged_date,
	UPPER(fn_get_person_name(encounter.pid)) AS patient_name,
	(
		bill.total_acc_charge + bill.total_med_charge +
		bill.total_srv_charge + bill.total_msc_charge +
		bill.total_ops_charge + bill.total_doc_charge
	) AS actual_charges,
	(
		discount.total_acc_discount + discount.total_med_discount +
		discount.total_ops_discount + discount.total_srv_discount +
		discount.total_msc_discount + discount.total_d1_discount +
		discount.total_d2_discount + discount.total_d3_discount +
		discount.total_d4_discount + discount.hospital_income_discount
	) AS discounts,
	(
		coverage.total_acc_coverage +
		coverage.total_med_coverage +
		coverage.total_srv_coverage +
		coverage.total_ops_coverage +
		coverage.total_msc_coverage +
		coverage.total_services_coverage + 
		coverage.total_d1_coverage +
		coverage.total_d2_coverage +
		coverage.total_d3_coverage +
		coverage.total_d4_coverage
	) AS phic_coverage,
	fn_billing_compute_gross_amount(bill.bill_nr) AS excess,
	bill.total_prevpayments deposits,
	SUM(IF(creditCollection.pay_type LIKE '%lingap%',IF(creditCollection.entry_type = 'debit',creditCollection.amount,-(creditCollection.amount)),NULL)) AS lingap,
	SUM(IF(creditCollection.pay_type = 'fund_checks',IF(creditCollection.entry_type = 'debit',creditCollection.amount,-(creditCollection.amount)),NULL)) AS funding,
	SUM(IF(creditCollection.pay_type = 'map',IF(creditCollection.entry_type = 'debit',creditCollection.amount,-(creditCollection.amount)),NULL)) AS map,
	SUM(IF(creditCollection.pay_type NOT REGEXP 'lingap|map|fund_checks|ss|coh|partial|paid|nbb|infirmary|dependent' AND creditCollection.pay_type <> 'pn',IF(creditCollection.entry_type = 'debit',creditCollection.amount,-(creditCollection.amount)),NULL)) AS pcso,
	SUM(IF(creditCollection.pay_type LIKE 'ss',IF(creditCollection.entry_type = 'debit',creditCollection.amount,-(creditCollection.amount)),NULL)) AS qfs,
	SUM(IF(creditCollection.pay_type LIKE '%coh%',IF(creditCollection.entry_type = 'debit',creditCollection.amount,-(creditCollection.amount)),NULL)) AS coh,
	SUM(IF(creditCollection.pay_type = 'pn',IF(creditCollection.entry_type = 'debit',creditCollection.amount,-(creditCollection.amount)),NULL)) AS pn,
	SUM(IF(creditCollection.pay_type REGEXP 'partial|paid',IF(creditCollection.entry_type = 'debit',creditCollection.amount,-(creditCollection.amount)),NULL)) AS paid,
	SUM(IF(creditCollection.pay_type REGEXP 'nbb|infirmary|dependent',IF(creditCollection.entry_type = 'debit',creditCollection.amount,-(creditCollection.amount)),NULL)) AS billing_discount,
	fn_billing_compute_gross_amount(bill.bill_nr) - IFNULL(SUM(IF(creditCollection.entry_type = 'debit',creditCollection.amount,-(creditCollection.amount))),0) AS balance,
	or_request.or_no
FROM seg_billing_encounter AS bill
INNER JOIN care_encounter AS encounter
	ON bill.encounter_nr = encounter.encounter_nr
LEFT JOIN seg_billing_coverage AS coverage
	ON coverage.bill_nr = bill.bill_nr
LEFT JOIN seg_billingcomputed_discount AS discount
	ON discount.bill_nr = bill.bill_nr
LEFT JOIN seg_credit_collection_ledger AS creditCollection
	ON creditCollection.encounter_nr = bill.encounter_nr
	AND creditCollection.is_deleted = 0
LEFT JOIN seg_pay_request AS or_request 
    ON or_request.service_code = bill.bill_nr
WHERE
	STR_TO_DATE(bill.bill_dte,'%Y-%m-%d') >= STR_TO_DATE(?,'%Y-%m-%d')
	AND STR_TO_DATE(bill.bill_dte,'%Y-%m-%d') <= STR_TO_DATE(?,'%Y-%m-%d')
AND bill.is_deleted IS NULL
AND bill.is_final = 1
GROUP BY bill.bill_nr
ORDER BY bill.bill_dte DESC
SQL;
// die($sql);
$data = $db->GetAll($sql,array(date('Y-m-d',$from_date),date('Y-m-d',$to_date)));

if(empty($data))
	$data = array(array());