<?php
//created by celsy, august 28, 2010
//income report for industrial clinic transaction
//same with Laboratory Income report
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

class ICTransaction_Income_Report extends RepGen {

	var $colored = FALSE;
	var $fromdate;
	var $todate;
	var $SUM_AMT_BILLED;
	var $SUM_AMT_PAID;
	var $SUM_LAB;
	var $SUM_BLOOD; 
	var $SUM_OTHER;
	var $SUM_SPLAB;
	var $SUM_ICLAB;
	
					 
	var $per_shift_billed;
	var $per_shift_paid;  
	var $per_shift_lab;
	var $per_shift_iclab;
	var $per_shift_splab;
	var $per_shift_blood;

	var $no_of_services;
	var $no_of_patients;
	var $no_of_requests;

	var $pat_type;
	var $enctype;
	var $patient_type;
	var $servgroup;
	var $group_cond;     

	var $total_width=0;

	function ICTransaction_Income_Report($fromdate, $todate) {
		global $db;
		$this->RepGen("INCOME REPORT: TRANSACTION");
		$this->ColumnWidth = array(12,30,18,16,16,16,16,16,16,16,16);
		$this->RowHeight = 5.5;
		$this->LEFTMARGIN = 15;
		$this->Alignment = array('C','C','R','R','R','R','R','R','R','R','R');
		$this->PageOrientation = "P";
		$this->total_width = array_sum($this->ColumnWidth);

		if ($fromdate) $this->fromdate=date("Y-m-d",strtotime($fromdate));
		if ($todate) $this->todate=date("Y-m-d",strtotime($todate));

		$this->pat_type=5;
		$this->enctype = " AND (encounter_type IN (6))";// OR en.counter_type IS NULL AND is_rdu = 0) ";
		$this->patient_type = "IC TRANSACTION PATIENT";

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
		$total_w = 50;
		$this->Cell(65,4);
		$this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
		$this->Cell(65,4);
		$this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
		$this->Ln(2);
		$this->SetFont("Arial","B","10");
		$this->Cell(65,4);
		$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell(65,4);
		$this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
		$this->Ln(4);

		$this->SetFont("Arial","B","9");
		$this->Cell(65,4);
		$this->Cell($total_w,4,'INDUSTRIAL CLINIC DEPARTMENT',$border2,1,'C');
		$this->Ln(2);

		$this->Cell(65,5);
		$this->Cell($total_w,4,$this->patient_type.' INCOME REPORT',$border2,1,'C');
		$this->SetFont('Arial','B',9);
		$this->Cell(65,5);


		if ($this->fromdate==$this->todate)
			$text = "For ".date("F j, Y",strtotime($this->fromdate));
		else
			$text = "From ".date("F j, Y",strtotime($this->fromdate))." To ".date("F j, Y",strtotime($this->todate));

		$this->Cell($total_w,4,$text,$border2,1,'C');
		$this->Ln(5);

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
		$row=6;
		$this->Cell($this->ColumnWidth[0],$row,'DATE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[1],$row,'SHIFT',1,0,'C',1);
		$this->Cell($this->ColumnWidth[2],$row,'AMT PAID',1,0,'C',1);
		$this->Cell($this->ColumnWidth[3],$row,'LAB',1,0,'C',1);
		$this->Cell($this->ColumnWidth[4],$row,'SP LAB',1,0,'C',1);
		$this->Cell($this->ColumnWidth[5],$row,'IC LAB',1,0,'C',1);   
		$this->Cell($this->ColumnWidth[6],$row,'BLOOD',1,0,'C',1);
		$this->Cell($this->ColumnWidth[7],$row,'RADIO',1,0,'C',1);     
		$this->Cell($this->ColumnWidth[8],$row,'MG',1,0,'C',1);
		$this->Cell($this->ColumnWidth[9],$row,'IP',1,0,'C',1);
		$this->Cell($this->ColumnWidth[10],$row,'MISC',1,0,'C',1);
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

		if (!$this->_count) {
				$this->SetFont('Arial','B',9);
				$this->SetFillColor(255);
				$this->SetTextColor(0);
				$this->Cell($this->total_width, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		}else{
				$this->SetFont('Arial','B',12);
				$this->Ln(4);                             
				
				$this->Cell(80, $this->RowHeight, 'AMOUNT BILLED', 0, 0, 'L', 1);
				$this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, number_format($this->SUM_AMT_BILLED,2,'.',','), 0, 1, 'R', 1);    
				
				$this->Cell(80, $this->RowHeight, 'LABORATORY', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, number_format($this->SUM_AMT_LAB,2,'.',','), 0, 1, 'R', 1);
				$this->Cell(80, $this->RowHeight, 'SPECIAL LABORATORY', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, number_format($this->SUM_AMT_SPLAB,2,'.',','), 0, 1, 'R', 1);  
				$this->Cell(80, $this->RowHeight, 'IC LABORATORY', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, number_format($this->SUM_AMT_ICLAB,2,'.',','), 0, 1, 'R', 1);
				$this->Cell(80, $this->RowHeight, 'BLOOD BANK', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, number_format($this->SUM_AMT_BLOOD ,2,'.',','), 0, 1, 'R', 1);
				$this->Cell(80, $this->RowHeight, 'RADIOLOGY', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, number_format($this->SUM_AMT_RADIO ,2,'.',','), 0, 1, 'R', 1);
				$this->Cell(80, $this->RowHeight, 'MURANG GAMOT', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, number_format($this->SUM_AMT_MG,2,'.',','), 0, 1, 'R', 1);
				$this->Cell(80, $this->RowHeight, 'INPATIENT PHARMACY', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, number_format($this->SUM_AMT_IP,2,'.',','), 0, 1, 'R', 1);
				$this->Cell(80, $this->RowHeight, 'MISCELLANEOUS CHARGES', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, number_format($this->SUM_AMT_MISC,2,'.',','), 0, 1, 'R', 1);

				$this->Ln(5);
				$this->Cell(80, $this->RowHeight, 'NUMBER OF PATIENTS SERVED', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '=', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, $this->no_of_patients, 0, 1, 'R', 1);

				$this->Cell(80, $this->RowHeight, 'NUMBER OF REQUESTS', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '=', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, $this->no_of_requests, 0, 1, 'R', 1);

//				$this->Cell(80, $this->RowHeight, 'NUMBER OF SERVICES REQUESTED', 0, 0, 'L', 0);
//				$this->Cell(15, $this->RowHeight, '=', 0, 0, 'L', 1);
//				$this->Cell(30, $this->RowHeight, $this->no_of_services, 0, 1, 'R', 1);

	}

		$cols = array();
	}
	
	
	function getTotalAmount($fromtime, $totime, $date)
	{
		global $db;

		unset($result2);
		unset($row2);

		$sql2 = "SELECT SQL_CALC_FOUND_ROWS
							DATE_,
							SHIFT,
							SUM(CASE WHEN (LAB) then LAB else 0 end) AS LAB,   
							SUM(CASE WHEN (SPL) then SPL else 0 end) AS SPL,
							SUM(CASE WHEN (ICLAB) then ICLAB else 0 end) AS ICLAB,
							SUM(CASE WHEN (BLOOD) then BLOOD else 0 end) AS BLOOD,    
							SUM(CASE WHEN (RADIO) then RADIO else 0 end) AS RADIO,
							SUM(CASE WHEN (PHARMA_MG) then PHARMA_MG else 0 end) AS PHARMA_MG,
							SUM(CASE WHEN (PHARMA_IP) then PHARMA_IP else 0 end) AS PHARMA_IP,
							SUM(CASE WHEN (MISC) then MISC else 0 end) AS MISC
							FROM seg_rep_ic_income_tbl WHERE DATE_='".$date."'
							AND SHIFT BETWEEN '".$fromtime."' AND '".$totime."'
						 ".$this->group_cond."
						 ".$this->enctype;

		$result2=$db->Execute($sql2);
		$row2 = $result2->FetchRow();
		$row2['AMT_BILLED']  = $row2['LAB']  + $row2['SPL'] + $row2['ICLAB'] + $row2['BLOOD'] + $row2['RADIO']  + $row2['PHARMA_MG'] + $row2['PHARMA_IP'] + $row2['MISC'];
				
		return $row2; 
	}                  			   
	
	


	function FetchData()
	{
		global $db;           
		//$db->Execute("CALL sp_populate_ic_income('".$this->fromdate."','".$this->todate."')");

		$sql = "SELECT SQL_CALC_FOUND_ROWS
							DATE_,
							SHIFT,                                                            
							SUM(CASE WHEN (LAB) then LAB else 0 end) AS LAB,   
							SUM(CASE WHEN (SPL) then SPL else 0 end) AS SPL,
							SUM(CASE WHEN (ICLAB) then ICLAB else 0 end) AS ICLAB,
							SUM(CASE WHEN (BLOOD) then BLOOD else 0 end) AS BLOOD,    
							SUM(CASE WHEN (RADIO) then RADIO else 0 end) AS RADIO,
							SUM(CASE WHEN (PHARMA_MG) then PHARMA_MG else 0 end) AS PHARMA_MG,
							SUM(CASE WHEN (PHARMA_IP) then PHARMA_IP else 0 end) AS PHARMA_IP,
							SUM(CASE WHEN (MISC) then MISC else 0 end) AS MISC

						FROM seg_rep_ic_income_tbl
						WHERE DATE_ BETWEEN '".$this->fromdate."' AND '".$this->todate."'
						 ".$this->group_cond."
						 ".$this->enctype.
						"GROUP BY DATE_, SHIFT";
		$result=$db->Execute($sql);

		$sql_serv = "SELECT SQL_CALC_FOUND_ROWS
								count(service_code) AS no_of_services
								FROM seg_rep_ic_income_tbl
								WHERE DATE_ BETWEEN '".$this->fromdate."' AND '".$this->todate."'
								".$this->group_cond."
								".$this->enctype.
								"GROUP BY service_code";

		$result_serv = $db->Execute($sql_serv);

		if (is_object($result_serv))
			$this->no_of_services = $result_serv->RecordCount();
		if (!$this->no_of_services)
			$this->no_of_services = 0;

		$sql_pat = "SELECT SQL_CALC_FOUND_ROWS
								count(pid) AS no_of_services
								FROM seg_rep_ic_income_tbl
								WHERE DATE_ BETWEEN '".$this->fromdate."' AND '".$this->todate."'
								".$this->group_cond."
								".$this->enctype.
								"GROUP BY pid";

		$result_pat=$db->Execute($sql_pat);
		if (is_object($result_pat))
			$this->no_of_patients = $result_pat->RecordCount();

		if (!$this->no_of_patients)
			$this->no_of_patients = 0;

		$sql_ref = "SELECT SQL_CALC_FOUND_ROWS
								count(refno) AS no_of_services
								FROM seg_rep_ic_income_tbl
								WHERE DATE_ BETWEEN '".$this->fromdate."' AND '".$this->todate."'
								".$this->group_cond."
								".$this->enctype.
								"GROUP BY refno";

		$result_ref=$db->Execute($sql_ref);
		if (is_object($result_ref))
			$this->no_of_requests = $result_ref->RecordCount();

		if (!$this->no_of_requests)
			$this->no_of_requests = 0;

		$SUM_AMT_BILLED = 0;  
		$SUM_AMT_LAB = 0;
		$SUM_AMT_SPLAB = 0;    
		$SUM_AMT_ICLAB = 0;
		$SUM_AMT_BLOOD = 0;  
		$SUM_AMT_RADIO = 0;
		$SUM_AMT_MG = 0;
		$SUM_AMT_IP = 0;
		$SUM_AMT_MISC = 0;    
																		
		
		$total_amt_billed = 0;    
		$total_amt_lab = 0;                 
		$total_amt_iclab = 0;  
		$total_amt_splab = 0;
		$total_amt_blood = 0;
		$total_amt_radio = 0;       
		$total_amt_mg = 0;  
		$total_amt_ip = 0;  
		$total_amt_misc = 0;

		$prev_date = "";
		$first = TRUE;


		if ($result) {
			$this->_count = $result->RecordCount();
			$this->Data=array();

			while ($row=$result->FetchRow())
			{
					$timeframe = $row['SHIFT'];
					$DATE = date("m/d",strtotime($row['DATE_']));       
					

					if(($timeframe >= '00:00:00') && ($timeframe <= '00:59:59')){
							$shift = "12:00 AM - 01:00 AM";
							$upper = '00:00:00'; $lower = '00:59:59';

					}elseif(($timeframe >= '01:00:00') && ($timeframe <= '01:59:59')){
							$shift = "01:00 AM - 02:00 AM";
							$upper ='01:00:00'; $lower = '01:59:59';

					}elseif(($timeframe >= '02:00:00') && ($timeframe <= '02:59:59')){
							$shift = "02:00 AM - 03:00 AM";
							$upper ='02:00:00'; $lower = '02:59:59';

					}elseif(($timeframe >= '03:00:00') && ($timeframe <= '03:59:59')){
							$shift = "03:00 AM - 04:00 AM";
							$upper ='03:00:00'; $lower = '03:59:59';

					}elseif(($timeframe >= '04:00:00') && ($timeframe <= '04:59:59')){
							$shift = "04:00 AM - 05:00 AM";
							$upper ='04:00:00'; $lower = '04:59:59';

					}elseif(($timeframe >= '05:00:00') && ($timeframe <= '05:59:59')){
							$shift = "05:00 AM - 06:00 AM";
							$upper ='05:00:00'; $lower = '05:59:59';

					}elseif(($timeframe >= '06:00:00') && ($timeframe <= '06:59:59')){
							$shift = "06:00 AM - 07:00 AM";
							$upper ='06:00:00'; $lower = '06:59:59';

					}elseif(($timeframe >= '07:00:00') && ($timeframe <= '07:59:59')){
							$shift = "07:00 AM - 08:00 AM";
							$upper ='07:00:00'; $lower = '07:59:59';

					}elseif(($timeframe >= '08:00:00') && ($timeframe <= '08:59:59')){
							$shift = "08:00 AM - 09:00 AM";
							$upper ='08:00:00'; $lower = '08:59:59';

					}elseif(($timeframe >= '09:00:00') && ($timeframe <= '09:59:59')){
							$shift = "09:00 AM - 10:00 AM";
							$upper ='09:00:00'; $lower = '09:59:59';

					}elseif(($timeframe >= '10:00:00') && ($timeframe <= '10:59:59')){
							$shift = "10:00 AM - 11:00 AM";
							$upper ='10:00:00'; $lower = '10:59:59';

					}elseif(($timeframe >= '11:00:00') && ($timeframe <= '11:59:59')){
							$shift = "11:00 AM - 12:00 PM";
							$upper ='11:00:00'; $lower = '11:59:59';

					}elseif(($timeframe >= '12:00:00') && ($timeframe <= '12:59:59')){
							$shift = "12:00 PM - 01:00 PM";
							$upper ='12:00:00'; $lower =  '12:59:59';

					}elseif(($timeframe >= '13:00:00') && ($timeframe <= '13:59:59')){
							$shift = "01:00 PM - 02:00 PM";
							$upper ='13:00:00'; $lower = '13:59:59';

					}elseif(($timeframe >= '14:00:00') && ($timeframe <= '14:59:59')){
							$shift = "02:00 PM - 03:00 PM";
							$upper ='14:00:00'; $lower =  '14:59:59';

					}elseif(($timeframe >= '15:00:00') && ($timeframe <= '15:59:59')){
							$shift = "03:00 PM - 04:00 PM";
							$upper ='15:00:00'; $lower = '15:59:59';

					}elseif(($timeframe >= '16:00:00') && ($timeframe <= '16:59:59')){
							$shift = "04:00 PM - 05:00 PM";
							$upper ='16:00:00'; $lower = '16:59:59';

					}elseif(($timeframe >= '17:00:00') && ($timeframe <= '17:59:59')){
							$shift = "05:00 PM - 06:00 PM";
							$upper ='17:00:00'; $lower = '17:59:59';

					}elseif(($timeframe >= '18:00:00') && ($timeframe <= '18:59:59')){
							$shift = "06:00 PM - 07:00 PM";
							$upper ='18:00:00'; $lower =  '19:59:59';

					}elseif(($timeframe >= '19:00:00') && ($timeframe <= '19:59:59')){
							$shift = "07:00 PM - 08:00 PM";
							$upper ='19:00:00'; $lower = '19:59:59';

					}elseif(($timeframe >= '20:00:00') && ($timeframe <= '20:59:59')){
							$shift = "08:00 PM - 09:00 PM";
							$upper ='20:00:00'; $lower = '20:59:59';

					}elseif(($timeframe >= '21:00:00') && ($timeframe <= '21:59:59')){
							$shift = "09:00 PM - 10:00 PM";
							$upper ='21:00:00'; $lower = '21:59:59';

					}elseif(($timeframe >= '22:00:00') && ($timeframe <= '22:59:59')){
							$shift = "10:00 PM - 11:00 PM";
							$upper ='22:00:00'; $lower = '22:59:59';

					}elseif(($timeframe >= '23:00:00') && ($timeframe <= '23:59:59')){
							$shift = "11:00 PM - 12:00 AM";
							$upper ='23:00:00'; $lower = '23:59:59';
					}
					if($old_shift!=$shift || ($old_shift==$shift && $prev_date!=$DATE)){
						$row2 = $this->getTotalAmount($upper, $lower, $row['DATE_']);
											$total_amt_billed += $row2['AMT_BILLED'];   
					$total_amt_lab += $row2['LAB'];                       
					$total_amt_splab += $row2['SPL'];  
					$total_amt_iclab += $row2['ICLAB'];  
					$total_amt_blood += $row2['BLOOD'];   
					$total_amt_radio += $row2['RADIO'];       
					$total_amt_mg += $row2['PHARMA_MG'];  
					$total_amt_ip += $row2['PHARMA_IP'];  
					$total_amt_misc += $row2['MISC'];  
					}


					if($shift!=$old_shift){
								 $this->Data[]=array(
										$DATE,
										$shift,
										number_format($row2['AMT_BILLED'],2,'.',','),   
										number_format($row2['LAB'],2,'.',','),
										number_format($row2['SPL'],2,'.',','),  
										number_format($row2['ICLAB'],2,'.',','),
										number_format($row2['BLOOD'],2,'.',','),  
										number_format($row2['RADIO'],2,'.',','),
										number_format($row2['PHARMA_MG'],2,'.',','),  
										number_format($row2['PHARMA_IP'],2,'.',','),
										number_format($row2['MISC'],2,'.',',')
									);    
					}               

					 $old_shift = $shift; 			
					
					$prev_date = $DATE;			

			}

				$this->SUM_AMT_BILLED = $total_amt_billed;
				$this->SUM_AMT_LAB = $total_amt_lab;   
				$this->SUM_AMT_SPLAB = $total_amt_splab;
				$this->SUM_AMT_ICLAB = $total_amt_iclab;
				$this->SUM_AMT_BLOOD = $total_amt_blood;
				$this->SUM_AMT_RADIO = $total_amt_radio;
				$this->SUM_AMT_MG = $total_amt_mg;   
				$this->SUM_AMT_IP = $total_amt_ip;   
				$this->SUM_AMT_MISC = $total_amt_misc;   

		}
		else {
			 print_r($sql);
			 print_r($db->ErrorMsg());
			 exit;
			 # Error
		}
	}
}

$rep = new ICTransaction_Income_Report($_GET['date_from'], $_GET["date_to"]);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>
