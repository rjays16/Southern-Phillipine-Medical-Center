<?php 
/*
* @author : art 02/03/15
*/
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/industrial_clinic/class_ic_transactions.php');
include('parameters.php');
$objIC = new SegICTransaction();

#hospital details -------------------------------------------------
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
#end hospital details --------------------------------------------

$from = date('Y-m-d',$_GET['from_date']);
$to = date('Y-m-d',$_GET['to_date']);

#title -----------------------------------------------------------
$title = strtoupper('Income Report');
$title_department = strtoupper('HEALTH SERVICES AND SPECIALTY CLINIC (HSSC)');
$params->put("hosp_country", $row['hosp_country']);
$params->put("hosp_agency",  $row['hosp_agency']);
$params->put("hosp_name",    $row['hosp_name']);
$params->put("hosp_addr1",   $row['hosp_addr1']);
$params->put("title",        $title);
$params->put("range",      	 'From : '.$from.'  To: '.$to);
$params->put("title_department", $title_department);
#end title -------------------------------------------------------

$rs = $objIC->getICtrxns($from,$to);

if($rs){
	foreach ($rs as $key => $value) {
		$lcash = '';
		$rcash = '';
		$pcash = '';
		$mcash = '';
		$lcharge = '';
		$rcharge = '';
		$pcharge = '';
		$mcharge = '';
		$lcash_total = 0;
		$rcash_total = 0;
		$pcash_total = 0;
		$mcash_total = 0;
		$lcharge_total = 0;
		$rcharge_total = 0;
		$pcharge_total = 0;
		$mcharge_total = 0;
		$subtotal = 0;
		#get cash----------------------------
		$cash = $objIC->getICincomeCash($value['encounter_nr']);
		if ($cash) {
			foreach ($cash as $cashrow) {
				#lab
				$qty = number_format($cashrow['qty']);
				if ($cashrow['ref_source'] == 'LD') {
					$lcash .= $cashrow['service_code'].'('.$qty.')<br>&nbsp;&nbsp;&nbsp;-->'.$cashrow['amount_due']*$qty.'<br>';
					$lcash_total += $cashrow['amount_due']*$qty;
					$subtotal += $cashrow['amount_due']*$qty;
				}elseif ($cashrow['ref_source'] == 'RD') {
					$rcash .= $cashrow['service_code'].'('.$qty.')<br>&nbsp;&nbsp;&nbsp;-->'.$cashrow['amount_due']*$qty.'<br>';
					$rcash_total += $cashrow['amount_due']*$qty;
					$subtotal += $cashrow['amount_due']*$qty;
				}elseif ($cashrow['ref_source'] == 'PH') {
					$pcash .= $cashrow['service_code'].'('.$qty.')<br>&nbsp;&nbsp;&nbsp;-->'.$cashrow['amount_due']*$qty.'<br>';
					$pcash_total += $cashrow['amount_due']*$qty;
					$subtotal += $cashrow['amount_due']*$qty;
				}elseif ($cashrow['ref_source'] == 'MISC') {
					$mcash .= $cashrow['service_code'].'('.$qty.')<br>&nbsp;&nbsp;&nbsp;-->'.$cashrow['amount_due']*$qty.'<br>';
					$mcash_total += $cashrow['amount_due']*$qty;
					$subtotal += $cashrow['amount_due']*$qty;
				}
			}
		}
		#end cash----------------------------

		$chargeLab = $objIC->getChargedLab($value['encounter_nr']);
		if ($chargeLab) {
			foreach ($chargeLab as $labrow) {
				$lcharge .= $labrow['service_code'].'('.$labrow['qty'].')<br>&nbsp;&nbsp;&nbsp;-->'.$labrow['total'].'<br>';
				$lcharge_total += $labrow['total'];
				$subtotal += $labrow['total'];
			}
		}

		$chargeRadio = $objIC->getChargedRadio($value['encounter_nr']);
		if ($chargeRadio) {
			foreach ($chargeRadio as $radrow) {
				$rcharge .= $radrow['service_code'].'('.$radrow['qty'].')<br>&nbsp;&nbsp;&nbsp;-->'.$radrow['total'].'<br>';
				$rcharge_total += $radrow['total'];
				$subtotal += $radrow['total'];
			}
		}

		$chargePh = $objIC->getChargedPh($value['encounter_nr']);
		if ($chargePh) {
			foreach ($chargePh as $phrow) {
				$pcharge .= $phrow['service_code'].'('.$phrow['qty'].')<br>&nbsp;&nbsp;&nbsp;-->'.number_format($phrow['total'], 2, '.', '').'<br>';
				$pcharge_total += $phrow['total'];
				$subtotal += $phrow['total'];
			}
		}

		$chargeMisc = $objIC->getChargedMisc($value['encounter_nr']);
		if ($chargeMisc) {
			foreach ($chargeMisc as $miscrow) {
				$mcharge .= $miscrow['service_code'].'('.$miscrow['qty'].')<br>&nbsp;&nbsp;&nbsp;-->'.$miscrow['total'].'<br>';
				$mcharge_total += $miscrow['total'];
				$subtotal += $miscrow['total'];
			}
		}
		$data[$key] = array(
			                'date'			=> date('Y-m-d',strtotime($value['trxn_date'])),
							'pname'			=> utf8_decode(trim(strtoupper($value['person_name']))),
							'lcash'			=> $lcash,
							'lcashtotal'	=> $lcash_total,
							'lcharge'		=> $lcharge,
							'lchargetotal'	=> $lcharge_total,	
							'rcash'			=> $rcash,
							'rcashtotal'	=> $rcash_total,
							'rcharge'		=> $rcharge,
							'rchargetotal'	=> $rcharge_total,
							'pcash'			=> $pcash,
							'pcashtotal'	=> $pcash_total,
							'pcharge'		=> $pcharge,
							'pchargetotal'	=> $pcharge_total,
							'mcash'			=> $mcash,
							'mcashtotal'	=> $mcash_total,
							'mcharge'		=> $mcharge,
							'mchargetotal'	=> $mcharge_total,
							'subtotal'		=> number_format($subtotal, 2, '.', ''),
			                 );
	}
}else{
	$data[0]['pname'] = 'No records';
}
?>