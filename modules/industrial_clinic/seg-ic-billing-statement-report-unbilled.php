<?php

# ====================================================
# Created by James 4/1/2014
# ====================================================
# IC billing statement report
# Same with Laboratory Income Report
# ====================================================
# Code copied from seg-ic-billing-statement-report.php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/billing/class_billing.php');
require_once($root_path.'include/care_api_classes/industrial_clinic/class_ic_transactions.php');

class IC_Billing_Statement_Report extends RepGen {

	var $colored = FALSE;        
	var $comp_id;
	var $encounter_nr;
	var $cutoff;				
	var $SUB_TOTAL;
	var $DISCOUNT_AMOUNT;								
	var $GRAND_TOTAL;

	var $total_width=0;

	function IC_Billing_Statement_Report($comp_id, $encounter_nr, $cutoff) {
		global $db;                                  
		$this->RepGen("INDUSTRIAL CLINIC OFFICE BILLING","L", Legal);
		$this->ColumnWidth = array(18,47,47,47,47,47,47,28);
		$this->RowHeight = 5.5;
		$this->LEFTMARGIN = 15;
		$this->Alignment = array('C','L','R','R','R','R','R','R');       
		$this->total_width = array_sum($this->ColumnWidth);
		$this->comp_id = $comp_id;
		$this->encounter_nr = $encounter_nr;
		$this->cutoff = $cutoff;																		

		$this->SetFillColor(0xFF);
		if ($this->colored) $this->SetDrawColor(0xDD);
	}

	function Header() {
		global $root_path, $db;
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

		$this->SetFont("Arial","I","9");
		$total_w = 230;
		$this->Cell(45,4);
		$this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
		$this->Cell(45,4);
		$this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
		$this->Ln(2);
		$this->SetFont("Arial","B","10");
		$this->Cell(45,4);
		$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell(45,4);
		$this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
		$this->Ln(4);

		$this->SetFont("Arial","B","8");
		$this->Cell(45,4);
		$this->Cell($total_w,4,'INDUSTRIAL CLINIC BILLING STATEMENT',$border2,1,'C');
		$this->Ln(2);
		
		$company_name = ucwords(strtoupper($db->GetOne("SELECT c.name FROM seg_industrial_company AS c WHERE c.company_id = ".$db->qstr($this->comp_id))));
								
		$this->SetFont("Arial","B","8");
		$this->Cell(45,3);
		$this->Cell($total_w,4,'OFFICE: '.$company_name, $border2,1,'C');   
		$this->Ln(2);          

	}

	function Footer()
	{
		$this->SetY(-23);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
	}

	function BeforeRow()
	{
		$this->FONTSIZE = 8;
		if ($this->colored) {
			if (($this->ROWNUM%2)>0)
				$this->FILLCOLOR=array(0xee, 0xef, 0xf4);
			else
				$this->FILLCOLOR=array(255,255,255);
			$this->DRAWCOLOR = array(0xDD,0xDD,0xDD);
		}
	}

	function BeforeData()
	{
		if ($this->colored) {
			$this->DrawColor = array(0xDD,0xDD,0xDD);
		}
		# Print table header
		$this->SetFont('Arial','B',8);
		if ($this->colored) $this->SetFillColor(0xED);
		$this->SetTextColor(0);
		$row=7;
		$this->Cell($this->ColumnWidth[0],$row,'DATE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[1],$row,'EMPLOYEE',1,0,'C',1);      
		$this->Cell($this->ColumnWidth[2],$row,'LABORATORY',1,0,'C',1);
		$this->Cell($this->ColumnWidth[3],$row,'SP. LABORATORY',1,0,'C',1);      
		$this->Cell($this->ColumnWidth[4],$row,'RADIOLOGY',1,0,'C',1);           
		$this->Cell($this->ColumnWidth[5],$row,'MEDICINES',1,0,'C',1);                     
		$this->Cell($this->ColumnWidth[6],$row,'OTHERS',1,0,'C',1);
		$this->Cell($this->ColumnWidth[7],$row,'TOTAL',1,0,'C',1);           
		$this->Ln();
	}

	function BeforeCellRender() {
		$this->FONTSIZE = 8;
		if ($this->colored) {
			if (($this->RENDERPAGEROWNUM%2)>0)
				$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
			else
				$this->RENDERCELL->FillColor=array(255,255,255);
		}
	}

	function AfterData()
	{
		global $db; 

		if($this->encounter_nr == "")
			$encounters = 0;
		else
			$encounters = explode(',', trim($this->encounter_nr,",")); 

		if ($encounters == 0) {
				$this->SetFont('Arial','B',9);
				$this->SetFillColor(255);
				$this->SetTextColor(0);
				$this->Cell($this->total_width, $this->RowHeight, "No records found for this report...", 1, 1, 'C', 1);
		}

		if($this->DISCOUNT_AMOUNT != 0)
		{
			if($encounters == 0)
			{
				$this->Cell(($this->total_width-$this->ColumnWidth[6]),$this->RowHeight,"TOTAL AMOUNT PAYABLE",1,0,'L',1);
				$this->Cell($this->ColumnWidth[6],$this->RowHeight,"P ".number_format($this->SUB_TOTAL,2),1,0,'R',1);
				$this->Ln(10); 
			}
			else
			{
				$this->Cell(($this->total_width-$this->ColumnWidth[6]),$this->RowHeight,"SUB TOTAL",1,0,'L',1);
				$this->Cell($this->ColumnWidth[6],$this->RowHeight,"P ".number_format($this->SUB_TOTAL,2),1,0,'R',1);
				$this->Ln();
				$this->Cell(($this->total_width-$this->ColumnWidth[6]),$this->RowHeight,"DISCOUNT",1,0,'L',1);
				$this->Cell($this->ColumnWidth[6],$this->RowHeight,"P ".number_format($this->DISCOUNT_AMOUNT,2),1,0,'R',1);
				$this->Ln();
				$this->Cell(($this->total_width-$this->ColumnWidth[6]),$this->RowHeight,"TOTAL AMOUNT PAYABLE",1,0,'L',1);
				$this->Cell($this->ColumnWidth[6],$this->RowHeight,"P ".number_format($this->GRAND_TOTAL,2),1,0,'R',1);
				$this->Ln(10);  
			}
		}
		else
		{
			$this->Cell(($this->total_width-$this->ColumnWidth[6]),$this->RowHeight,"TOTAL AMOUNT PAYABLE",1,0,'L',1);
			$this->Cell($this->ColumnWidth[6],$this->RowHeight,"P ".number_format($this->SUB_TOTAL,2),1,0,'R',1);
			$this->Ln(10); 
		}
                               
		$this->Cell(320, $this->RowHeight, "Physician - in - charge", 0, 1, 'C', 1);             
		$cols = array();
	}

																		 

	function FetchData()
	{
		global $db; 

		$objIC = new SegICTransaction();

		$sub_total = 0;
		$discount_amount = 0;
		$grand_total = 0;
		
		if($this->encounter_nr == "")
			$encounters = 0;
		else
			$encounters = explode(',', trim($this->encounter_nr,","));

		if(count($encounters) != 0)
		{
			foreach ($encounters as $employee_id) {
				$sql = "SELECT encounter_nr FROM seg_industrial_transaction WHERE refno=".$db->qstr($employee_id);

				$result = $db->Execute($sql);
				$row = $result->FetchRow();
				$enc_nr = $row['encounter_nr'];

				/* Commendted by Nick - Query Transfered to class */
				// $sql = "SELECT
				// (SELECT SUM(ld.price_cash*ld.quantity)
				// 	FROM seg_lab_servdetails AS ld
				// 	INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
				// 	WHERE ld.is_served = 1 AND l.encounter_nr = '$enc_nr' AND l.is_cash=0 AND l.ref_source='LB' AND l.status<>'deleted') AS `lab_total_charge`,
				// (SELECT SUM(ld.price_cash*ld.quantity)
				// 	FROM seg_lab_servdetails AS ld
				// 	INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
				// 	WHERE ld.is_served = 1 AND l.encounter_nr = '$enc_nr' AND l.is_cash=0 AND l.ref_source='IC' AND l.status<>'deleted') AS `iclab_total_charge`,
				// (SELECT SUM(ld.price_cash*ld.quantity)
				// 	FROM seg_lab_servdetails AS ld
				// 	INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
				// 	WHERE ld.is_served = 1 AND l.encounter_nr = '$enc_nr' AND l.is_cash=0 AND l.ref_source='BB' AND l.status<>'deleted') AS `bb_total_charge`,
				// -- (SELECT SUM(ld.price_cash*ld.quantity)
				// -- 	FROM seg_lab_servdetails AS ld
				// -- 	INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
				// -- 	WHERE l.encounter_nr = '$enc_nr' AND l.is_cash=0 AND l.ref_source='SPL' AND l.status<>'deleted') AS `splab_total_charge`,
				// (SELECT SUM(ld.price_cash*ld.quantity)
				// 	FROM seg_lab_servdetails AS ld
				// 	INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
				// 	WHERE ld.is_served = 1 AND l.encounter_nr = '$enc_nr' AND l.is_cash=0 /*AND l.ref_source='IC' commented by art 05/13/14*/ AND l.status<>'deleted') AS `splab_total_charge`,
				// (SELECT SUM(rd.price_cash)
				// 	FROM care_test_request_radio AS rd
				// 	INNER JOIN seg_radio_serv AS r ON rd.refno=r.refno
				// 	WHERE rd.is_served = 1 AND r.encounter_nr = '$enc_nr' AND r.is_cash=0 AND r.status<>'deleted') AS `radio_total_charge`,
				// (SELECT SUM(ph.pricecash*ph.quantity)
				// 	FROM seg_pharma_order_items AS ph
				// 	INNER JOIN seg_pharma_orders AS p ON ph.refno=p.refno
				// 	WHERE ph.serve_status = 'S' AND p.encounter_nr = '$enc_nr' AND p.is_cash=0 AND p.pharma_area='IP') AS `ip_total_charge`,
				// (SELECT SUM(ph.pricecash*ph.quantity)
				// 	FROM seg_pharma_order_items AS ph
				// 	INNER JOIN seg_pharma_orders AS p ON ph.refno=p.refno
				// 	WHERE ph.serve_status = 'S' AND p.encounter_nr = '$enc_nr' AND p.is_cash=0 AND p.pharma_area='MG' ) AS `mg_total_charge`,
				// (SELECT SUM(md.chrg_amnt*md.quantity)
				// 	FROM seg_misc_service_details AS md
				// 	INNER JOIN seg_misc_service AS m ON m.refno=md.refno
				// 	WHERE m.encounter_nr='$enc_nr' AND m.is_cash=0 ) AS `misc_total_charge`";

				$result = $objIC->getPatientExaminationsTotals($enc_nr);//added by Nick 05-19-2014
				$data = $result->FetchRow();

				$LAB 	= parseFloatEx($data["lab_total_charge"]+$data["bb_total_charge"]+$data["iclab_total_charge"]);
				$SPLAB 	= parseFloatEx($data["splab_total_charge"]);
				$RAD 	= parseFloatEx($data["radio_total_charge"]);
				$MED 	= parseFloatEx($data["ip_total_charge"]);
				$MISC 	= parseFloatEx($data["misc_total_charge"]+$data["mg_total_charge"]);
				$patient_total = $LAB + $SPLAB + $RAD + $MED + $MISC;

				$sql = "SELECT fn_get_person_name(pid) AS fullname 
								FROM seg_industrial_transaction
							   WHERE refno=".$db->qstr($employee_id);

				$result = $db->GetOne($sql);
				if($result)
					$patient_name = $result;
				else
				{
					print_r($sql);
					print_r($db->ErrorMsg());
					exit;
					# Error
				}

				$sub_total += $patient_total;
				//display all encounters of employees with charges
				$this->Data[]=array(

				DATE("m-d-Y", strtotime($this->cutoff)),strtoupper($patient_name),         
				number_format($LAB,2), number_format($SPLAB,2),
				number_format($RAD,2), number_format($MED,2),
				number_format($MISC,2), number_format($patient_total,2));		                  
			}	
		}                                                     
				
			# Get the billing no. to use
			$maxdte = $objIC->getMaxTrxnDte($this->comp_id, 1, DATE("Y-m-d", strtotime($this->cutoff)));
			$strSQL = "SELECT fn_get_ic_billing_nr_new(DATE('$maxdte')) AS bill_nr";
			$bill_nr = '';
			if ($res = $db->Execute($strSQL)) {
				if ($row = $res->FetchRow()) {
					$bill_nr = $row["bill_nr"];
				}
			}

			if(!$bill_nr == '')
			{
				$result = $objIC->checkDiscountBillNr($bill_nr, $this->comp_id);

				if($result != FALSE)
				{
					$row = $result->FetchRow();
					$count = $row['count'];

					if($count != 0)
					{
						$result = $objIC->getDiscount($bill_nr, $this->comp_id);

						if($result != FALSE)
						{
							$row = $result->FetchRow();
							$discount_amount = $row['discount_amount'];

							if($discount_amount != 0)
								$this->DISCOUNT_AMOUNT = $discount_amount;
							else
								$this->DISCOUNT_AMOUNT = 0;
						}
						else
							$this->DISCOUNT_AMOUNT = 0;

					}
					else
						$this->DISCOUNT_AMOUNT = 0;
				}
				else
					$this->DISCOUNT_AMOUNT = 0;
			}
			else
				print_r("Error generating Bill No.");

			$this->SUB_TOTAL = $sub_total;
			$grand_total = $this->SUB_TOTAL - $this->DISCOUNT_AMOUNT;

			if($grand_total < 0)
				$this->GRAND_TOTAL = 0;
			else          		        
				$this->GRAND_TOTAL = $grand_total;
	}
}

$rep = new IC_Billing_Statement_Report($comp_id, $encounter_nr, $cutoff);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>
