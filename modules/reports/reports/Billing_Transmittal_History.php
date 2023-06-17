<?
#created by Nick, 1/30/2014
require_once('roots.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/inc_environment_global.php';
include 'parameters.php';

$year = $_GET['year'];
$month = $_GET['month'];

$start = strtotime($year . '-' . $month . '-01');
$end = strtotime('+1 month', $start);
$month_year = date('M',$start) . " " . date('Y',$start);

$start = date('Y-m-d',$start);
$end = date('Y-m-d',$end);


global $db;

$where = array();

$params->put('hosp_country',$hosp_country);
$params->put('hosp_agency',$hosp_agency);
$params->put('hosp_name',$hosp_name);
$params->put('hosp_addr1',$hosp_addr1);
$params->put('date_span',"From " . date('M d, Y',$from_date) . " to " . date('M d, Y',$to_date));

//date
$where[] = "DATE(h.transmit_dte)
               BETWEEN
                    DATE(".$db->qstr(date('Y-m-d',$from_date)).")
               AND
                    DATE(".$db->qstr(date('Y-m-d',$to_date)).")";

$condition = implode(') AND (',$where);

$sql = "SELECT 
		  h.transmit_dte AS TRANSMITTAL_DATE,
		  cpi.insurance_nr AS PHIC_NUMBER,
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
		  fn_get_person_name (cp.pid) AS NAME_PATIENT,
		  IF(
		    cpi.is_principal = '1',
		    fn_get_person_name (cp.pid),
		    CONCAT(
		      TRIM(member_lname),
		      IF(
		        TRIM(member_fname) <> '',
		        CONCAT(', ', TRIM(member_fname)),
		        ' '
		      ),
		      IF(
		        TRIM(member_mname) <> '',
		        CONCAT(' ', LEFT(TRIM(member_mname), 1), '.'),
		        ''
		      )
		    )
		  ) AS MEMBER_NAME,
		  memcategory_desc,
		  DATE_FORMAT(
		    (
		      CASE
		        WHEN admission_dt IS NULL 
		        OR admission_dt = '' 
		        THEN encounter_date 
		        ELSE admission_dt 
		      END
		    ),
		    '%b %e, %Y %l:%i%p'
		  ) AS date_admission,
		  DATE_FORMAT(
		    STR_TO_DATE(
		      ce.mgh_setdte,
		      '%Y-%m-%d %H:%i:%s'
		    ),
		    '%b %e, %Y %l:%i%p'
		  ) AS date_discharge,
		  acc_coverage,
		  med_coverage,
		  xlo_coverage,
		  or_fee,
		  pf_visit,
		  surgeon_coverage,
		  anesth_coverage,
		  patient_claim,
		  ce.encounter_nr,
		  ce.pid
		FROM
		  (
		    (
		      (
		        (
		          (
		            seg_transmittal AS h 
		            INNER JOIN seg_transmittal_details AS d 
		              ON h.transmit_no = d.transmit_no
		          ) 
		          INNER JOIN care_encounter AS ce 
		            ON d.encounter_nr = ce.encounter_nr
		        ) 
		        INNER JOIN care_person AS cp 
		          ON ce.pid = cp.pid
		      ) 
		      INNER JOIN care_person_insurance AS cpi 
		        ON cpi.pid = ce.pid 
		        AND cpi.hcare_id = h.hcare_id 
		      LEFT JOIN seg_insurance_member_info simi 
		        ON simi.pid = ce.pid AND simi.hcare_id = h.hcare_id
		    ) 
		    INNER JOIN 
		      (SELECT 
		        encounter_nr,
		        hcare_id,
		        SUM(total_acc_coverage) AS acc_coverage,
		        SUM(total_med_coverage) AS med_coverage,
		        SUM(
		          total_srv_coverage + total_msc_coverage
		        ) AS xlo_coverage,
		        SUM(total_ops_coverage) AS or_fee,
		        SUM(
		          total_d1_coverage + total_d2_coverage
		        ) AS pf_visit,
		        SUM(total_d3_coverage) AS surgeon_coverage,
		        SUM(total_d4_coverage) AS anesth_coverage 
		      FROM
		        seg_billing_coverage AS sbc 
		        INNER JOIN seg_billing_encounter AS sbe 
		          ON (
		            sbc.bill_nr = sbe.bill_nr 
		            AND sbe.is_deleted IS NULL
		          ) 
		      GROUP BY encounter_nr,
		        hcare_id) AS t 
		      ON t.encounter_nr = d.encounter_nr 
		      AND t.hcare_id = h.hcare_id
		  ) 
		  LEFT JOIN (
		      seg_encounter_memcategory AS sem 
		      INNER JOIN seg_memcategory AS sm 
		        ON sem.memcategory_id = sm.memcategory_id
		    ) 
		    ON sem.encounter_nr = d.encounter_nr 
		    -- Editado por by Matsuu 03082017
		WHERE ($condition)
		ORDER BY DATE(h.transmit_dte),
		  cp.name_last,
		  cp.name_first,
		  cp.name_middle";

// die($sql);

$i = 0;
$data = array();

$rs = $db->Execute($sql);
if($rs){
	if($rs->RecordCount()){
		while($row = $rs->FetchRow()){
			$patientEncounterNameSql = "SELECT name_first, name_middle, name_last
                							FROM seg_encounter_name `sen`
                							WHERE sen.`encounter_nr` = ".$db->qstr($row['encounter_nr'])."
                							AND sen.`pid` = ".$db->qstr($row['pid']);
            $patientEncounterNameResult = $db->GetRow($patientEncounterNameSql);

            if($patientEncounterNameResult){
            	$patient_name = $patientEncounterNameResult['name_last'].", ".$patientEncounterNameResult['name_first']." ".$patientEncounterNameResult['name_middle'].".";
            }else{
            	$patient_name = $row['NAME_PATIENT'];
            }
			$data[$i] = array('transmittal_date'=>date('M d,Y',strtotime($row['TRANSMITTAL_DATE'])),
							  'phic_number'=>$row['PHIC_NUMBER'],
							  'name_patient'=>utf8_decode(trim($patient_name)),
							  'member_name'=>utf8_decode(trim($row['MEMBER_NAME'])),
							  'memcategory_desc'=>$row['memcategory_desc'],
							  'date_admission'=>$row['ADMISSION_DATE2']?$row['ADMISSION_DATE2']:$row['date_admission'],
							  'date_discharge'=>$row['DISCHARGE_DATE2']?$row['DISCHARGE_DATE2']:$row['date_discharge'],
							  'acc_coverage'=>(double)$row['acc_coverage'],
							  'med_coverage'=>(double)$row['med_coverage'],
							  'xlo_coverage'=>(double)$row['xlo_coverage'],
							  'or_fee'=>(double)$row['or_fee'],
							  'pf_visit'=>(double)$row['pf_visit'],
							  'surgeon_coverage'=>(double)$row['surgeon_coverage'],
							  'anesth_coverage'=>(double)$row['anesth_coverage'],
							  'patient_claim'=>(double)$row['patient_claim']
				             );
			$i++;
		}
	}else{
		$data[0] = array('transmittal_date'=>'No Data');
	}
}else{
	$data[0] = array('transmittal_date'=>'No Data');
}

?>