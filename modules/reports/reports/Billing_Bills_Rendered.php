<?php
#created by Nick, 1/30/2014
require_once('roots.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/inc_environment_global.php';
include 'parameters.php';

global $db;

$where = array();

//bill status
if(!isset($billing_status) || $billing_status=='all'){
    $header_dtype = "All Bills";
}else if($billing_status=='deleted'){
    $header_dtype = "Deleted Bills";
    $where[] = "fb.is_deleted = '1'";
}else if($billing_status=='final'){
    $header_dtype = "Final Bills";
    $where[] = "fb.is_final = 1  AND fb.is_deleted IS NULL";
}

//date
$where[] = "DATE(fb.bill_dte)
               BETWEEN
                    DATE(".$db->qstr(date('Y-m-d',$from_date)).")
               AND
                    DATE(".$db->qstr(date('Y-m-d',$to_date)).")";

//encoder
if(!isset($billing_encoder) || $billing_encoder == 'all'){
}else{
    $where[] = "fu.personell_nr = " . $db->qstr($billing_encoder);
}

$params->put('hosp_country',$hosp_country);
$params->put('hosp_agency',$hosp_agency);
$params->put('hosp_name',$hosp_name);
$params->put('hosp_addr1',$hosp_addr1);
$params->put('date_span',"From " . date('M d, Y',$from_date) . " to " . date('M d, Y',$to_date));
$params->put('delete_type',$header_dtype);

$condition = implode(') AND (',$where);

$query = "SELECT
			fb.bill_nr, fb.bill_dte, 
			fu.name prepared_by, fn_get_person_name(e.pid) patient,
			e.encounter_nr, department.id department,
			CONCAT(
			    IF (e.encounter_type IN (3,4), IF(w.accomodation_type=1, 'Service', 'Pay'), et.type),
			        IF(
			            (
			            	IFNULL(cov.total_acc_coverage,0) + IFNULL(cov.total_med_coverage, 0) + 
			            	IFNULL(cov.total_srv_coverage, 0) + IFNULL(cov.total_ops_coverage, 0) + 
			            	IFNULL(cov.total_msc_coverage, 0) + IFNULL(cov.total_d1_coverage, 0) + 
			                IFNULL(cov.total_d2_coverage, 0) + IFNULL(cov.total_d3_coverage, 0) + 
			                IFNULL(cov.total_d4_coverage, 0) + IFNULL(cov.total_services_coverage, 0)
			            ) > 0,
			            '/PHIC', ''
			          )
			) type,

			(
				fb.total_acc_charge + fb.total_med_charge + 
				fb.total_srv_charge + fb.total_ops_charge + 
				fb.total_doc_charge + fb.total_msc_charge
			) total_charge,

			(
				IFNULL(dsc.total_acc_discount, 0) + IFNULL(dsc.total_med_discount, 0) +
				IFNULL(dsc.total_ops_discount, 0) + IFNULL(dsc.total_srv_discount, 0) + 
				IFNULL(dsc.total_msc_discount, 0) + IFNULL(dsc.total_d1_discount, 0) + 
			    IFNULL(dsc.total_d2_discount, 0) + IFNULL(dsc.total_d3_discount, 0) + 
			    IFNULL(dsc.total_d4_discount, 0)
			) total_discount,

			(
				IFNULL(cov.total_acc_coverage,0) + IFNULL(cov.total_med_coverage, 0) + 
				IFNULL(cov.total_srv_coverage, 0) + IFNULL(cov.total_ops_coverage, 0) + 
				IFNULL(cov.total_msc_coverage, 0) + IFNULL(cov.total_d1_coverage, 0) + 
			    IFNULL(cov.total_d2_coverage, 0) + IFNULL(cov.total_d3_coverage, 0) + 
			    IFNULL(cov.total_d4_coverage, 0) + IFNULL(cov.total_services_coverage, 0)
			) total_coverage,

			fb.total_prevpayments previous_payment,
			fn_billing_compute_gross_amount(fb.bill_nr) excess,

			IFNULL(dd.discountdesc,'-') classification,
			IFNULL(bd.discount,0) discount_pct,
			bd.discount_amnt discount_fixed,
			p.or_no or_no,
			p.or_date or_date,
			p.cancel_date,
			pr.ref_source, pr.service_code,
			pr.amount_due or_amount,
			fb.is_deleted,
			mu.name,
			(SELECT DATE_FORMAT(fb.modify_dt,'%b %d %Y %h:%i %p')) as modifydate,
			pu.name or_clerk,
			e.pid
			FROM seg_billing_encounter fb
			INNER JOIN care_encounter e ON e.encounter_nr = fb.encounter_nr
			INNER JOIN care_department department ON department.nr = e.current_dept_nr

			LEFT JOIN seg_encounter_insurance i ON i.encounter_nr=e.encounter_nr AND hcare_id='18'
			LEFT JOIN care_type_encounter et ON e.encounter_type=et.type_nr
			LEFT JOIN care_ward w ON w.nr = e.current_ward_nr
			LEFT JOIN seg_billing_coverage cov ON cov.bill_nr = fb.bill_nr
			LEFT JOIN seg_billingcomputed_discount dsc ON dsc.bill_nr = fb.bill_nr
			LEFT JOIN seg_billing_discount bd ON bd.bill_nr = fb.bill_nr
			LEFT JOIN seg_discount dd ON dd.discountid=bd.discountid
			LEFT JOIN seg_pay_request pr ON pr.ref_source='FB' AND pr.service_code = fb.bill_nr
			LEFT JOIN seg_pay p ON p.or_no=pr.or_no AND p.cancel_date IS NULL
			LEFT JOIN care_users pu ON p.create_id = pu.login_id
			LEFT JOIN care_users fu ON fb.create_id = fu.login_id
			LEFT JOIN care_users mu ON fb.modify_id = mu.login_id 

			WHERE ($condition)";

// echo $query; exit();

$rs = $db->Execute($query);
if($rs){
	if($rs->RecordCount()>0){
		$i = 0;
		while($row = $rs->FetchRow()){
			if ($row['is_deleted']) {
                $IsDeleted = 'Cancelled';
                $Deletedby = $row['name'];
                $deletedtime = $row['modifydate'];
            }
            else{
                $IsDeleted = '';
                $Deletedby = '';
                $deletedtime = '';
            }
			if (($row['classification'] == 'Infirmary') || ($row['classification'] == 'Senior Citizen') ) {
                $ClassificationShow = $row['classification'];
                $totalDiscount = (float) $row['discount_fixed'] ? 
                $row['discount_fixed'] : 
                ((float) $row['excess']) * $row['discount_pct'];
                $AmountDueShow = (float) $row['excess'] - $totalDiscount;
                $OrNumberShow = $row['or_no'];
                $OrDateShow = $row['or_date'] ? date('Y-m-d h:i A', strtotime($row['or_date'])) : '';
                $AmountPayableShow = $row['or_amount'];
                $ClerkShow = $row['or_clerk'];
            }else{
                $ClassificationShow = '';
                $totalDiscount = '';
                $AmountDueShow = '';
                $OrNumberShow = '';
                $OrDateShow = '';
                $AmountPayableShow = '';
                $ClerkShow = '';
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

			$data[$i] = array('bill_ref'=>$row['bill_nr'],
				              'bill_date'=>date('Y-m-d h:i A', strtotime($row['bill_dte'])),
				              'prepared_by'=>$row['prepared_by'],
				              'patient_name'=>utf8_decode(trim($patient_name)),
				              'case_no'=>$row['encounter_nr'],
				              'department'=>$row['department'],
				              'type'=>$row['type'],
				              'actual_charges'=>(double)$row['total_charge'],
				              'discount'=>(double)$row['total_discount'],
				              'phic_coverage'=>(double)$row['total_coverage'],
				              'deposit'=>(double)$row['previous_payment'],
				              'excess'=>(double)$row['excess'],
				              'classification'=>$ClassificationShow,
				              'classification_discount'=>$totalDiscount,
				              'or_no'=>$OrNumberShow,
				              'or_date'=>$OrDateShow,
				              'amount Paid'=>(double)$AmountPayableShow,
				              'clerk'=>$ClerkShow,
				              'cancelled'=>$IsDeleted,
				              'cancelled_by'=>utf8_decode(trim($Deletedby)),
				              'cancelled_date'=>$deletedtime
				             );
			$i++;
		}
	}else{
		$data['bill_ref'][0] = "No data";
	}
}else{
	$data['bill_ref'][0] = "No data";
}

?>