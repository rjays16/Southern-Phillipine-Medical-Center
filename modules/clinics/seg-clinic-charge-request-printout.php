<?php 
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'classes/fpdf/fpdf.php');
require($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once $root_path.'include/care_api_classes/class_encounter.php';

class Request_Slip extends FPDF{


var $hosp_name = '';  
var $fontFamily = 'Times';
var $fontSizeSmall = 8;
var $fontSizeMedium = 10;
var $fontSizeLarge = 12;
var $PageSize = array(216,279);
var $PageTitle = 'Charged Request Slip';
var $encounter_nr;
var $total_payment = 0;
	function Request_Slip($encounter_nr){
	  $this->FPDF('P','mm',$this->PageSize);
	  $this->SetTitle($this->PageTitle, true);
	  if($encounter_nr)
			$this->encounter_nr=$encounter_nr;
	}


	function Header(){
		global $db;
		$objInfo = new Hospital_Admin();
		if ($row = $objInfo->getAllHospitalInfo()){
		  $hosp_country = $row['hosp_country'];
		  $hosp_agency = $row['hosp_agency'];
		  $hosp_name = $row['hosp_name'];
		  $hosp_addr1 = $row['hosp_addr1'];
		}
		#hosp
		$this->Image('../../gui/img/logos/dmc_logo.jpg',30,10,30,30);
		$this->SetFont($this->fontFamily,'I', $this->fontSizeLarge);
		$this->Cell(0,5,$hosp_country, 0, 1,'C');
		$this->Cell(0,5,$hosp_agency, 0, 1,'C');
		$this->SetFont($this->fontFamily,'B', $this->fontSizeLarge);
		$this->Cell(0,5,$hosp_name, 0, 1,'C');
		$this->SetFont($this->fontFamily,'', $this->fontSizeLarge);
		$this->Cell(0,5,$hosp_addr1, 0, 1,'C');
		$this->Ln(3);
		#title
		$this->SetFont($this->fontFamily,'B', $this->fontSizeLarge);
		$this->Cell(0,5,strtoupper($this->PageTitle), 0, 1,'C');
		#patient
		$person = new Encounter();
		$person_info = $person->getEncounterInfo($this->encounter_nr,0);
		$this->Ln(5);
		$this->SetFont($this->fontFamily,'', $this->fontSizeMedium);
		$this->Cell(50,5,'PID: '.$person_info['pid'], 0, 1,'L');
		$this->Ln(3);
		$this->Cell(150,5,'Patient Name: '.strtoupper($person_info['name_last'].", ".$person_info['name_first']." ".$person_info['name_middle']), 0, '','L');
		$this->Cell(0,5,'Date: '.date("F d, Y"), 0, 1,'R');
		$this->Ln(3);
	}

	function printRequests()
	{
		global $db;

		$this->Ln(2);
		$sql = "SELECT l.refno FROM seg_lab_serv AS l INNER JOIN seg_lab_servdetails AS ld ON l.refno=ld.refno\n".
						"WHERE l.is_cash=0 AND l.ref_source='LB' AND l.status<>'deleted' \n".
						"AND (ld.request_flag IS NULL OR ld.request_flag!='paid')\n".
						"AND l.encounter_nr=".$db->qstr($this->encounter_nr)." AND DATE(l.serv_dt)=DATE(NOW())";
		$has_lab = $db->GetOne($sql);
		if($has_lab!==FALSE) {
			$this->SetFont("Times","B", 10);
			$this->Cell(30, 4, "Laboratory :", "", 1, 'L');
			$this->printLabRequests();
			$this->Ln(2);
		}

		$sql = "SELECT l.refno FROM seg_lab_serv AS l INNER JOIN seg_lab_servdetails AS ld ON l.refno=ld.refno\n".
						"WHERE l.is_cash=0 AND l.ref_source='BB' AND l.status<>'deleted' \n".
						"AND (ld.request_flag IS NULL OR ld.request_flag!='paid')\n".
						"AND l.encounter_nr=".$db->qstr($this->encounter_nr)." AND DATE(l.serv_dt)=DATE(NOW())";
		$has_blood = $db->GetOne($sql);
		if($has_blood!==FALSE) {
			$this->SetFont("Times","B", 10);
			$this->Cell(30, 4, "Blood Bank :", "", 1, 'L');
			$this->printBloodRequests();
			$this->Ln(2);
		}

		$sql = "SELECT l.refno FROM seg_lab_serv AS l INNER JOIN seg_lab_servdetails AS ld ON l.refno=ld.refno\n".
						"WHERE l.is_cash=0 AND l.ref_source='SPL' AND l.status<>'deleted' \n".
						"AND (ld.request_flag IS NULL OR ld.request_flag!='paid')\n".
						"AND l.encounter_nr=".$db->qstr($this->encounter_nr)." AND DATE(l.serv_dt)=DATE(NOW())";
		$has_splab = $db->GetOne($sql);
		if($has_splab!==FALSE) {
			$this->SetFont("Times","B", 10);
			$this->Cell(30, 4, "Special Lab :", "", 1, 'L');
			$this->printSplabRequests();
			$this->Ln(2);
		}

		$sql = "SELECT l.refno FROM seg_lab_serv AS l INNER JOIN seg_lab_servdetails AS ld ON l.refno=ld.refno\n".
						"WHERE l.is_cash=0 AND l.ref_source='IC' AND l.status<>'deleted' \n".
						"AND (ld.request_flag IS NULL OR ld.request_flag!='paid')\n".
						"AND l.encounter_nr=".$db->qstr($this->encounter_nr)." AND DATE(l.serv_dt)=DATE(NOW())";
		$has_iclab = $db->GetOne($sql);
		if($has_iclab!==FALSE) {
			$this->SetFont("Times","B", 10);
			$this->Cell(30, 4, "Industrial Clinic Lab :", "", 1, 'L');
			$this->printICLabRequests();
			$this->Ln(2);
		}

		$sql = "SELECT r.refno FROM seg_radio_serv AS r INNER JOIN care_test_request_radio AS rd ON r.refno=rd.refno\n".
						"WHERE r.is_cash=0 AND r.status<>'deleted' \n".
						"AND r.fromdept='RD'\n".
						"AND (rd.request_flag IS NULL OR rd.request_flag!='paid')\n".
						"AND r.encounter_nr=".$db->qstr($this->encounter_nr)." AND DATE(r.request_date)=DATE(NOW())";
		$has_radio = $db->GetOne($sql);
		if($has_radio!==FALSE) {
			$this->SetFont("Times","B", 10);
			$this->Cell(30, 4, "Radiology :", "", 1, 'L');
			$this->printRadioRequests();
			$this->Ln(2);
		}

		$sql = "SELECT r.refno FROM seg_radio_serv AS r INNER JOIN care_test_request_radio AS rd ON r.refno=rd.refno\n".
						"WHERE r.is_cash=0 AND r.status<>'deleted' \n".
						"AND r.fromdept='OBGUSD'\n".
						"AND (rd.request_flag IS NULL OR rd.request_flag!='paid')\n".
						"AND r.encounter_nr=".$db->qstr($this->encounter_nr)." AND DATE(r.request_date)=DATE(NOW())";
		$has_radio = $db->GetOne($sql);
		if($has_radio!==FALSE) {
			$this->SetFont("Times","B", 10);
			$this->Cell(30, 4, "Ultrasound-OB-Gyne :", "", 1, 'L');
			$this->printOBGUSDRequests();
			$this->Ln(2);
		}

		$sql = "SELECT p.refno FROM seg_pharma_orders AS p INNER JOIN seg_pharma_order_items AS ph ON p.refno=ph.refno \n".
						"WHERE p.is_cash=0 AND p.pharma_area='IP' \n".
						"AND (ph.request_flag IS NULL OR ph.request_flag!='paid')\n".
						"AND p.encounter_nr=".$db->qstr($this->encounter_nr)." AND DATE(p.orderdate)=DATE(NOW())";
		$has_ip = $db->GetOne($sql);
		if($has_ip!==FALSE) {
			$this->SetFont("Times","B", 10);
			$this->Cell(30, 4, "Inpatient Pharmacy :", "", 1, 'L');
			$this->printIpRequests();
			$this->Ln(2);
		}

		$sql = "SELECT p.refno FROM seg_pharma_orders AS p INNER JOIN seg_pharma_order_items AS ph ON p.refno=ph.refno \n".
						"WHERE p.is_cash=0 AND p.pharma_area='MG' \n".
						"AND (ph.request_flag IS NULL OR ph.request_flag!='paid')\n".
						"AND p.encounter_nr=".$db->qstr($this->encounter_nr)." AND DATE(p.orderdate)=DATE(NOW())";
		$has_mg = $db->GetOne($sql);
		if($has_mg!==FALSE) {
			$this->SetFont("Times","B", 10);
			$this->Cell(30, 4, "Murang Gamot :", "", 1, 'L');
			$this->printMgRequests();
			$this->Ln(2);
		}

		$sql = "SELECT m.refno FROM seg_misc_service AS m INNER JOIN seg_misc_service_details AS md ON m.refno=md.refno \n".
						"WHERE m.is_cash=0 \n".
						"AND (md.request_flag IS NULL OR md.request_flag!='paid')\n".
						"AND m.encounter_nr=".$db->qstr($this->encounter_nr)." AND DATE(m.chrge_dte)=DATE(NOW())";
		$has_misc = $db->GetOne($sql);
		if($has_misc!==FALSE) {
			$this->SetFont("Times","B", 10);
			$this->Cell(30, 4, "Miscellaneous :", "", 1, 'L');
			$this->printMiscRequests();
		}
	}
	function printTableHeader()
	{
		$this->SetFont("Times", "B", 10);
		$this->Cell(30,8, "Reference No.", 1, 0, 'C');
		$this->Cell(95,8, "Item Name", 1, 0, 'C');
		$this->Cell(25,8, "Quantity", 1, 0, 'C');
		$this->Cell(25,8, "Unit Price", 1, 0, 'C');
		$this->Cell(25,8, "Total", 1, 1, 'C');
		$this->SetFont("Times", "", 10);

	}


	function printLabRequests()
	{
		global $db;
		$this->printTableHeader();
		$this->NoWrap=TRUE;
		$sql = "SELECT l.refno, CONCAT(l.serv_dt,' ', l.serv_tm) AS `date`, ls.name, ld.quantity, ld.price_cash, ld.request_flag \n".
					"FROM seg_lab_servdetails AS ld \n".
					"INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno \n".
					"LEFT JOIN seg_lab_services AS ls ON ld.service_code=ls.service_code \n".
					"WHERE l.encounter_nr=".$db->qstr($this->encounter_nr)." AND l.status<>'deleted' AND l.is_cash=0 \n".
					"AND l.ref_source='LB' AND (ld.request_flag IS NULL OR ld.request_flag!='paid') AND DATE(l.serv_dt)=DATE(NOW()) ORDER BY date DESC ";
		$result = $db->Execute($sql);
		if($result!==FALSE) {
			if($result->RecordCount()>0){
				$total_lab = 0;
				while($row=$result->FetchRow())
				{
					$total=parseFloatEx($row['quantity']*$row['price_cash']);
					$total_lab+=parseFloatEx($total);
					$this->Cell(30, 6, $row["refno"], 1, 0, "C");
					if($this->GetStringWidth($row["name"])>32) {
						$temp_name = ($this->GetStringWidth($row["name"]) > 95 ? substr($row["name"], 0, 50).'...' : $row["name"]);
						$this->SetFont('Times', '', 8);
						$this->Cell(95, 6, ucfirst($temp_name), 1, 0, "L");
						$this->SetFont('Times', '', 10);
					} else {
						$this->SetFont('Times', '', 10);
						$this->Cell(95, 6, ucfirst($row["name"]), 1, 0, "L");
					}

					$this->Cell(25, 6, $row["quantity"], 1, 0, "C");
					$this->Cell(25, 6, number_format($row["price_cash"],2), 1, 0, "R");
					$total_label = ((isset($row['request_flag']) && strtolower($row["request_flag"])!='paid') ? strtoupper($row["request_flag"]) : number_format($total,2) );
					$this->Cell(25, 6, $total_label, 1, 1, "R");
				}
				$this->Cell(180, 6, "TOTAL CHARGE: ", 0, 0, "L");
				$this->Cell(20, 6, number_format($total_lab,2), 0, 1, "R");
				$this->total_payment+=$total_lab;
			} else {
				$this->Cell(95, 6, "No charged items for this center.", 1, 1, "L");
			}
		}else {
			echo $db->ErrorMsg();
			echo "<br>LAST QUERY: ".$sql;
			exit();
		}
	}

	function printBloodRequests()
	{
		global $db;
		$this->printTableHeader();
		$this->NoWrap=TRUE;
		$sql = "SELECT l.refno, CONCAT(l.serv_dt,' ', l.serv_tm) AS `date`, ls.name, ld.quantity, ld.price_cash, ld.request_flag \n".
					"FROM seg_lab_servdetails AS ld \n".
					"INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno \n".
					"LEFT JOIN seg_lab_services AS ls ON ld.service_code=ls.service_code \n".
					"WHERE l.encounter_nr=".$db->qstr($this->encounter_nr)." AND l.status<>'deleted' AND l.is_cash=0 \n".
					"AND l.ref_source='BB' AND (ld.request_flag IS NULL OR ld.request_flag!='paid') AND DATE(l.serv_dt)=DATE(NOW()) ORDER BY date DESC ";
		$result = $db->Execute($sql);
		if($result!==FALSE) {
			if($result->RecordCount()>0){
				$total_blood = 0;
				while($row=$result->FetchRow())
				{
					$total=parseFloatEx($row['quantity']*$row['price_cash']);
					$total_blood+=parseFloatEx($total);
					$this->Cell(30, 6, $row["refno"], 1, 0, "C");
					if($this->GetStringWidth($row["name"])>32) {
						$temp_name = ($this->GetStringWidth($row["name"]) > 95 ? substr($row["name"], 0, 50).'...' : $row["name"]);
						$this->SetFont('Times', '', 8);
						$this->Cell(95, 6, ucfirst($temp_name), 1, 0, "L");
						$this->SetFont('Times', '', 10);
					} else {
						$this->SetFont('Times', '', 10);
						$this->Cell(95, 6, ucfirst($row["name"]), 1, 0, "L");
					}

					$this->Cell(25, 6, $row["quantity"], 1, 0, "C");
					$this->Cell(25, 6, number_format($row["price_cash"],2), 1, 0, "R");
					$total_label = ((isset($row['request_flag']) && strtolower($row["request_flag"])!='paid') ? strtoupper($row["request_flag"]) : number_format($total,2) );
					$this->Cell(25, 6, $total_label, 1, 1, "R");
				}
				$this->Cell(180, 6, "TOTAL CHARGE: ", 0, 0, "L");
				$this->Cell(20, 6, number_format($total_blood,2), 0, 1, "R");
				$this->total_payment+=$total_blood;
			} else {
				$this->Cell(95, 6, "No cash items for this center.", 1, 1, "L");
			}
		}else {
			echo $db->ErrorMsg();
			echo "<br>LAST QUERY: ".$sql;
			exit();
		}
	}

	function printSpLabRequests()
	{
		global $db;
		$this->printTableHeader();
		$this->NoWrap=TRUE;
		$sql = "SELECT l.refno, CONCAT(l.serv_dt,' ', l.serv_tm) AS `date`, ls.name, ld.quantity, ld.price_cash, ld.request_flag \n".
					"FROM seg_lab_servdetails AS ld \n".
					"INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno \n".
					"LEFT JOIN seg_lab_services AS ls ON ld.service_code=ls.service_code \n".
					"WHERE l.encounter_nr=".$db->qstr($this->encounter_nr)." AND l.status<>'deleted' AND l.is_cash=0 \n".
					"AND l.ref_source='SPL' AND (ld.request_flag IS NULL OR ld.request_flag!='paid') AND DATE(l.serv_dt)=DATE(NOW()) ORDER BY date DESC ";
		$result = $db->Execute($sql);
		if($result!==FALSE) {
			if($result->RecordCount()>0){
				$total_lab = 0;
				while($row=$result->FetchRow())
				{
					$total=parseFloatEx($row['quantity']*$row['price_cash']);
					$total_lab+=parseFloatEx($total);
					$this->Cell(30, 6, $row["refno"], 1, 0, "C");
					if($this->GetStringWidth($row["name"])>32) {
						$temp_name = ($this->GetStringWidth($row["name"]) > 95 ? substr($row["name"], 0, 50).'...' : $row["name"]);
						$this->SetFont('Times', '', 8);
						$this->Cell(95, 6, ucfirst($temp_name), 1, 0, "L");
						$this->SetFont('Times', '', 10);
					} else {
						$this->SetFont('Times', '', 10);
						$this->Cell(95, 6, ucfirst($row["name"]), 1, 0, "L");
					}

					$this->Cell(25, 6, $row["quantity"], 1, 0, "C");
					$this->Cell(25, 6, number_format($row["price_cash"],2), 1, 0, "R");
					$total_label = ((isset($row['request_flag']) && strtolower($row["request_flag"])!='paid') ? strtoupper($row["request_flag"]) : number_format($total,2) );
					$this->Cell(25, 6, $total_label, 1, 1, "R");
				}
				$this->Cell(180, 6, "TOTAL CHARGE: ", 0, 0, "L");
				$this->Cell(20, 6, number_format($total_lab,2), 0, 1, "R");
				$this->total_payment+=$total_lab;
			} else {
				$this->Cell(95, 6, "No charged items for this center.", 1, 1, "L");
			}
		}else {
			echo $db->ErrorMsg();
			echo "<br>LAST QUERY: ".$sql;
			exit();
		}
	}

	function printICLabRequests()
	{
		global $db;
		$this->printTableHeader();
		$this->NoWrap=TRUE;
		$sql = "SELECT l.refno, CONCAT(l.serv_dt,' ', l.serv_tm) AS `date`, ls.name, ld.quantity, ld.price_cash, ld.request_flag \n".
					"FROM seg_lab_servdetails AS ld \n".
					"INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno \n".
					"LEFT JOIN seg_lab_services AS ls ON ld.service_code=ls.service_code \n".
					"WHERE l.encounter_nr=".$db->qstr($this->encounter_nr)." AND l.status<>'deleted' AND l.is_cash=0 \n".
					"AND l.ref_source='IC' AND (ld.request_flag IS NULL OR ld.request_flag!='paid') AND DATE(l.serv_dt)=DATE(NOW()) ORDER BY date DESC ";
		$result = $db->Execute($sql);
		if($result!==FALSE) {
			if($result->RecordCount()>0){
				$total_lab = 0;
				while($row=$result->FetchRow())
				{
					$total=parseFloatEx($row['quantity']*$row['price_cash']);
					$total_lab+=parseFloatEx($total);
					$this->Cell(30, 6, $row["refno"], 1, 0, "C");
					if($this->GetStringWidth($row["name"])>32) {
						$temp_name = ($this->GetStringWidth($row["name"]) > 95 ? substr($row["name"], 0, 50).'...' : $row["name"]);
						$this->SetFont('Times', '', 8);
						$this->Cell(95, 6, ucfirst($temp_name), 1, 0, "L");
						$this->SetFont('Times', '', 10);
					} else {
						$this->SetFont('Times', '', 10);
						$this->Cell(95, 6, ucfirst($row["name"]), 1, 0, "L");
					}

					$this->Cell(25, 6, $row["quantity"], 1, 0, "C");
					$this->Cell(25, 6, number_format($row["price_cash"],2), 1, 0, "R");
					$total_label = ((isset($row['request_flag']) && strtolower($row["request_flag"])!='paid') ? strtoupper($row["request_flag"]) : number_format($total,2) );
					$this->Cell(25, 6, $total_label, 1, 1, "R");
				}
				$this->Cell(180, 6, "TOTAL CHARGE: ", 0, 0, "L");
				$this->Cell(20, 6, number_format($total_lab,2), 0, 1, "R");
				$this->total_payment+=$total_lab;
			} else {
				$this->Cell(95, 6, "No charged items for this center.", 1, 1, "L");
			}
		}else {
			echo $db->ErrorMsg();
			echo "<br>LAST QUERY: ".$sql;
			exit();
		}
	}

	function printRadioRequests()
	{
		global $db;
		$this->printTableHeader();
		$this->NoWrap=TRUE;
		$sql = "SELECT r.refno, CONCAT(r.request_date,' ', r.request_time) AS `date`, rs.name, 1 AS `quantity`, rd.price_cash, rd.request_flag \n".
					"FROM care_test_request_radio AS rd \n".
					"INNER JOIN seg_radio_serv AS r ON rd.refno=r.refno \n".
					"LEFT JOIN seg_radio_services AS rs ON rd.service_code=rs.service_code \n".
					"WHERE r.encounter_nr=".$db->qstr($this->encounter_nr)." AND r.status<>'deleted' AND r.fromdept='RD' AND r.is_cash=0 \n".
					"AND (rd.request_flag IS NULL OR rd.request_flag!='paid') AND DATE(r.request_date)=DATE(NOW()) ORDER BY date DESC ";
		$result = $db->Execute($sql);
		if($result!==FALSE) {
			if($result->RecordCount()>0){
				$total_radio = 0;
				while($row=$result->FetchRow())
				{
					$total=parseFloatEx($row['quantity']*$row['price_cash']);
					$total_radio+=parseFloatEx($total);
					$this->Cell(30, 6, $row["refno"], 1, 0, "C");
					if($this->GetStringWidth($row["name"])>32) {
						$temp_name = ($this->GetStringWidth($row["name"]) > 95 ? substr($row["name"], 0, 50).'...' : $row["name"]);
						$this->SetFont('Times', '', 8);
						$this->Cell(95, 6, ucfirst($temp_name), 1, 0, "L");
						$this->SetFont('Times', '', 10);
					} else {
						$this->SetFont('Times', '', 10);
						$this->Cell(95, 6, ucfirst($row["name"]), 1, 0, "L");
					}

					$this->Cell(25, 6, $row["quantity"], 1, 0, "C");
					$this->Cell(25, 6, number_format($row["price_cash"],2), 1, 0, "R");
					$total_label = ((isset($row['request_flag']) && strtolower($row["request_flag"])!='paid') ? strtoupper($row["request_flag"]) : number_format($total,2) );
					$this->Cell(25, 6, $total_label, 1, 1, "R");
				}
				$this->Cell(180, 6, "TOTAL CHARGE: ", 0, 0, "L");
				$this->Cell(20, 6, number_format($total_radio,2), 0, 1, "R");
				$this->total_payment+=$total_radio;
			} else {
				$this->Cell(95, 6, "No charged items for this center.", 1, 1, "L");
			}
		}else {
			echo $db->ErrorMsg();
			echo "<br>LAST QUERY: ".$sql;
			exit();
		}
	}

	function printOBGUSDRequests()
	{
		global $db;
		$this->printTableHeader();
		$this->NoWrap=TRUE;
		$sql = "SELECT r.refno, CONCAT(r.request_date,' ', r.request_time) AS `date`, rs.name, 1 AS `quantity`, rd.price_cash, rd.request_flag \n".
					"FROM care_test_request_radio AS rd \n".
					"INNER JOIN seg_radio_serv AS r ON rd.refno=r.refno \n".
					"LEFT JOIN seg_radio_services AS rs ON rd.service_code=rs.service_code \n".
					"WHERE r.encounter_nr=".$db->qstr($this->encounter_nr)." AND r.status<>'deleted' AND r.fromdept='OBGUSD' AND r.is_cash=0 \n".
					"AND (rd.request_flag IS NULL OR rd.request_flag!='paid') AND DATE(r.request_date)=DATE(NOW()) ORDER BY date DESC ";
		$result = $db->Execute($sql);
		if($result!==FALSE) {
			if($result->RecordCount()>0){
				$total_radio = 0;
				while($row=$result->FetchRow())
				{
					$total=parseFloatEx($row['quantity']*$row['price_cash']);
					$total_radio+=parseFloatEx($total);
					$this->Cell(30, 6, $row["refno"], 1, 0, "C");
					if($this->GetStringWidth($row["name"])>32) {
						$temp_name = ($this->GetStringWidth($row["name"]) > 95 ? substr($row["name"], 0, 50).'...' : $row["name"]);
						$this->SetFont('Times', '', 8);
						$this->Cell(95, 6, ucfirst($temp_name), 1, 0, "L");
						$this->SetFont('Times', '', 10);
					} else {
						$this->SetFont('Times', '', 10);
						$this->Cell(95, 6, ucfirst($row["name"]), 1, 0, "L");
					}

					$this->Cell(25, 6, $row["quantity"], 1, 0, "C");
					$this->Cell(25, 6, number_format($row["price_cash"],2), 1, 0, "R");
					$total_label = ((isset($row['request_flag']) && strtolower($row["request_flag"])!='paid') ? strtoupper($row["request_flag"]) : number_format($total,2) );
					$this->Cell(25, 6, $total_label, 1, 1, "R");
				}
				$this->Cell(180, 6, "TOTAL CHARGE: ", 0, 0, "L");
				$this->Cell(20, 6, number_format($total_radio,2), 0, 1, "R");
				$this->total_payment+=$total_radio;
			} else {
				$this->Cell(95, 6, "No charged items for this center.", 1, 1, "L");
			}
		}else {
			echo $db->ErrorMsg();
			echo "<br>LAST QUERY: ".$sql;
			exit();
		}
	}

	function printIpRequests()
	{
		global $db;
		$this->printTableHeader();
		$this->NoWrap=TRUE;
		$sql = "SELECT p.refno, p.orderdate, ph.artikelname, pi.quantity, pi.pricecash, pi.request_flag \n".
					"FROM seg_pharma_order_items AS pi \n".
					"INNER JOIN seg_pharma_orders AS p ON pi.refno=p.refno \n".
					"INNER JOIN care_pharma_products_main AS ph ON pi.bestellnum=ph.bestellnum \n".
					"WHERE p.encounter_nr=".$db->qstr($this->encounter_nr)." AND p.is_cash=0 AND p.pharma_area='IP' \n".
					"AND (pi.request_flag IS NULL OR pi.request_flag!='paid') AND DATE(p.orderdate)=DATE(NOW()) ORDER BY orderdate DESC ";
		$result = $db->Execute($sql);
		if($result!==FALSE) {
			if($result->RecordCount()>0){
				$total_ip = 0;
				while($row=$result->FetchRow())
				{
					$total=parseFloatEx($row['quantity']*$row['pricecash']);
					$total_ip+=parseFloatEx($total);
					$this->Cell(30, 6, $row["refno"], 1, 0, "C");
					if($this->GetStringWidth($row["artikelname"])>32) {
						$temp_name = ($this->GetStringWidth($row["artikelname"]) > 95 ? substr($row["artikelname"], 0, 50).'...' : $row["artikelname"]);
						$this->SetFont('Times', '', 8);
						$this->Cell(95, 6, ucfirst($temp_name), 1, 0, "L");
						$this->SetFont('Times', '', 10);
					} else {
						$this->SetFont('Times', '', 10);
						$this->Cell(95, 6, ucfirst($row["artikelname"]), 1, 0, "L");
					}

					$this->Cell(25, 6, $row["quantity"], 1, 0, "C");
					$this->Cell(25, 6, number_format($row["pricecash"],2), 1, 0, "R");
					$total_label = ((isset($row['request_flag']) && strtolower($row["request_flag"])!='paid') ? strtoupper($row["request_flag"]) : number_format($total,2) );
					$this->Cell(25, 6, $total_label, 1, 1, "R");
				}
				$this->Cell(180, 6, "TOTAL CHARGE: ", 0, 0, "L");
				$this->Cell(20, 6, number_format($total_ip,2), 0, 1, "R");
				$this->total_payment+=$total_ip;
			} else {
				$this->Cell(95, 6, "No charged items for this center.", 1, 1, "L");
			}
		}else {
			echo $db->ErrorMsg();
			echo "<br>LAST QUERY: ".$sql;
			exit();
		}
	}

	function printMgRequests()
	{
		global $db;
		$this->printTableHeader();
		$this->NoWrap=TRUE;
		$sql = "SELECT p.refno, p.orderdate, ph.artikelname, pi.quantity, pi.pricecash, pi.request_flag \n".
					"FROM seg_pharma_order_items AS pi \n".
					"INNER JOIN seg_pharma_orders AS p ON pi.refno=p.refno \n".
					"INNER JOIN care_pharma_products_main AS ph ON pi.bestellnum=ph.bestellnum \n".
					"WHERE p.encounter_nr=".$db->qstr($this->encounter_nr)." AND p.is_cash=0 AND p.pharma_area='MG' \n".
					"AND (pi.request_flag IS NULL OR pi.request_flag!='paid') AND DATE(p.orderdate)=DATE(NOW()) ORDER BY orderdate DESC ";
		$result = $db->Execute($sql);
		if($result!==FALSE) {
			if($result->RecordCount()>0){
				$total_mg = 0;
				while($row=$result->FetchRow())
				{
					$total=parseFloatEx($row['quantity']*$row['pricecash']);
					$total_mg+=parseFloatEx($total);
					$this->Cell(30, 6, $row["refno"], 1, 0, "C");

					if($this->GetStringWidth($row["artikelname"])>32) {
						$temp_name = ($this->GetStringWidth($row["artikelname"]) > 95 ? substr($row["artikelname"], 0, 50).'...' : $row["artikelname"]);
						$this->SetFont('Times', '', 8);
						$this->Cell(95, 6, ucfirst($temp_name), 1, 0, "L");
						$this->SetFont('Times', '', 10);
					} else {
						$this->SetFont('Times', '', 10);
						$this->Cell(95, 6, ucfirst($row["artikelname"]), 1, 0, "L");
					}

					$this->Cell(25, 6, $row["quantity"], 1, 0, "C");
					$this->Cell(25, 6, number_format($row["pricecash"],2), 1, 0, "R");

					$total_label = ((isset($row['request_flag']) && strtolower($row["request_flag"])!='paid') ? strtoupper($row["request_flag"]) : number_format($total,2) );
					$this->Cell(25, 6, $total_label, 1, 1, "R");
				}
				$this->Cell(180, 6, "TOTAL CHARGE: ", 0, 0, "L");
				$this->Cell(20, 6, number_format($total_mg,2), 0, 1, "R");
				$this->total_payment+=$total_mg;
			} else {
				$this->Cell(95, 6, "No charged items for this center.", 1, 1, "L");
			}
		}else {
			echo $db->ErrorMsg();
			echo "<br>LAST QUERY: ".$sql;
			exit();
		}
	}

	function printMiscRequests()
	{
		global $db;
		$this->printTableHeader();
		$this->NoWrap=TRUE;
		$sql = "SELECT m.refno, m.chrge_dte, ms.name, md.quantity, md.chrg_amnt AS `price`, md.request_flag \n".
					"FROM seg_misc_service_details AS md \n".
					"INNER JOIN seg_misc_service AS m ON md.refno=m.refno \n".
					"INNER JOIN seg_other_services AS ms ON md.service_code=ms.alt_service_code \n".
					"WHERE m.encounter_nr=".$db->qstr($this->encounter_nr)." AND m.is_cash=0 \n".
					"AND (md.request_flag IS NULL OR md.request_flag!='paid') AND DATE(m.chrge_dte)=DATE(NOW()) ORDER BY chrge_dte DESC";
		$result = $db->Execute($sql);
		if($result!==FALSE) {
			if($result->RecordCount()>0){
				$total_misc = 0;
				while($row=$result->FetchRow())
				{
					$total=parseFloatEx($row['quantity']*$row['price']);
					$total_misc+=parseFloatEx($total);
					$this->Cell(30, 6, $row["refno"], 1, 0, "C");

					if($this->GetStringWidth($row["name"])>32) {
						$temp_name = ($this->GetStringWidth($row["name"]) > 95 ? substr($row["name"], 0, 50).'...' : $row["name"]);
						$this->SetFont('Times', '', 8);
						$this->Cell(95, 6, ucfirst($temp_name), 1, 0, "L");
						$this->SetFont('Times', '', 10);
					} else {
						$this->SetFont('Times', '', 10);
						$this->Cell(95, 6, ucfirst($row["name"]), 1, 0, "L");
					}

					$this->Cell(25, 6, $row["quantity"], 1, 0, "C");
					$this->Cell(25, 6, number_format($row["price"],2), 1, 0, "R");
					$total_label = ((isset($row['request_flag']) && strtolower($row["request_flag"])!='paid') ? strtoupper($row["request_flag"]) : number_format($total,2) );
					$this->Cell(25, 6, $total_label, 1, 1, "R");
				}
				$this->Cell(180, 6, "TOTAL CHARGE: ", 0, 0, "L");
				$this->Cell(20, 6, number_format($total_misc,2), 0, 1, "R");
				$this->total_payment+=$total_misc;
			} else {
				$this->Cell(95, 6, "No charged items for this center.", 1, 1, "L");
			}
		}else {
			echo $db->ErrorMsg();
			echo "<br>LAST QUERY: ".$sql;
			exit();
		}
	}
	function AfterData()
	{
		$this->Line($this->GetX()-1,$this->GetY()+5,$this->GetX()+200,$this->GetY()+5);
		$this->Ln(5);
		$this->SetFont("Times", "B", 12);
		$this->Cell(200, 8, "Total Amount Charged: ".number_format($this->total_payment, 2), 0, 1, "R");
		$this->Line($this->GetX()-1,$this->GetY(),$this->GetX()+200,$this->GetY());

	}
}

$objPdf = new Request_Slip($_GET['encounter_nr']);
$objPdf->AliasNbPages();
$objPdf->AddPage();
$objPdf->printRequests();
$objPdf->AfterData();
$objPdf->Output();
 ?>