<?php
//created by CELSY, Sept. 6, 2010
//IC billing statement report
//same with Laboratory Income report
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

class IC_Billing_Statement_Report extends RepGen {

	var $colored = FALSE;        
	var $comp_id;				
	var $is_comp;								
	var $pat_type;
	var $enctype;      
	var $servgroup;
	var $group_cond;
	var $GRAND_TOTAL;

	var $total_width=0;

	function IC_Billing_Statement_Report($comp_id, $is_comp) {
		global $db;                                  
		$this->RepGen("INDUSTRIAL CLINIC OFFICE BILLING","L", Legal);
		$this->ColumnWidth = array(18,57,15,15,15,15,15,15,15,15,15,15,15,15,15,15,15,25);
		$this->RowHeight = 5.5;
		$this->LEFTMARGIN = 15;
		$this->Alignment = array('C','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R');       
		$this->total_width = array_sum($this->ColumnWidth);
		$this->comp_id = $comp_id;	
		$this->is_comp = $is_comp;																		

		$this->pat_type=6;
		$this->enctype = " ";//AND (encounter_type IN (5) OR encounter_type IS NULL AND is_rdu = 0) "; 

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
		
		if($this->is_comp==1){
			$company_name = ucwords(strtoupper($db->GetOne("SELECT c.name FROM seg_industrial_company AS c WHERE c.company_id = ".$db->qstr($this->comp_id))));
		}else {
			$sql = 'SELECT fn_get_person_name('.$db->qstr($this->comp_id).')';
			$company_name = ucwords(strtoupper($db->GetOne($sql)));
		}
		
											
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
		$row=6;
		$this->Cell($this->ColumnWidth[0],$row,'DATE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[1],$row,'EMPLOYEE',1,0,'C',1);      
		$this->Cell($this->ColumnWidth[2],$row,'CBC',1,0,'C',1);           
		$this->Cell($this->ColumnWidth[3],$row,'UA',1,0,'C',1);           
		$this->Cell($this->ColumnWidth[4],$row,'FA',1,0,'C',1);           
		$this->Cell($this->ColumnWidth[5],$row,'FBS',1,0,'C',1);           
		$this->Cell($this->ColumnWidth[6],$row,'CREA',1,0,'C',1);           
		$this->Cell($this->ColumnWidth[7],$row,'SUA',1,0,'C',1);           
		$this->Cell($this->ColumnWidth[8],$row,'LP',1,0,'C',1);           
		$this->Cell($this->ColumnWidth[9],$row,'HP',1,0,'C',1);           
		$this->Cell($this->ColumnWidth[10],$row,'IZ',1,0,'C',1);           
		$this->Cell($this->ColumnWidth[11],$row,'ECG',1,0,'C',1);           
		$this->Cell($this->ColumnWidth[12],$row,'XR',1,0,'C',1);           
		$this->Cell($this->ColumnWidth[13],$row,'USD',1,0,'C',1);           
		$this->Cell($this->ColumnWidth[14],$row,'OTHERS',1,0,'C',1);           
		$this->Cell($this->ColumnWidth[15],$row,'DS',1,0,'C',1);           
		$this->Cell($this->ColumnWidth[16],$row,'PE',1,0,'C',1);           
		$this->Cell($this->ColumnWidth[17],$row,'TOTAL',1,0,'C',1);           
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
		}  
		$this->Cell(($this->total_width-$this->ColumnWidth[17]),$this->RowHeight,"TOTAL AMOUNT PAYABLE",1,0,'L',1);
		$this->Cell($this->ColumnWidth[17],$this->RowHeight,"P ".number_format($this->GRAND_TOTAL,2),1,0,'R',1);//;
		$this->Ln(10);                                 
		$this->Cell(320, $this->RowHeight, "Physician - in - charge", 0, 1, 'C', 1);             
		$cols = array();
	}

																		 

	function FetchData()
	{
		global $db; 
		$grand_total = 0;
		$GRAND_TOTAL = 0;  
		$cutoff_date = '';
		
		if($this->is_comp==1){ 
			$sql2 = "SELECT bh.cutoff_date FROM seg_industrial_bill_h AS bh 
							WHERE bh.company_id=".$db->qstr($this->comp_id)."
							ORDER BY bh.cutoff_date DESC;";    
		}else{
			$sql2 = "SELECT bh.cutoff_date FROM seg_industrial_bill_h AS bh 
		-- // 					WHERE bh.pid=".$db->qstr($this->comp_id)."
							ORDER BY bh.cutoff_date DESC;";     
		}
			$result2 = $db->Execute($sql2);       
		if ($result2) {   
			$row_cut=$result2->FetchRow();
			$cutoff_date = $row_cut['cutoff_date'];
		}
		if($cutoff_date=='') {
			//$cutoff_date=date("Y-m-d"); 
			$cutoff_date = mktime(0,0,0,date("m"),date("d")+1,date("Y"));
			$cutoff_date = date("Y-m-d", $cutoff_date);
			//echo '$$$$'.$cutoff_date;  
		}                                  
		//echo $sql2.'<br>'.$cutoff_date."//".date();
																 
		//$this_date=date("Y-m-d");   
		$from_date = mktime(0,0,0,date("m"),date("d"),date("Y")-3);
		$from_date = date("Y-m-d", $from_date);                           
		$time_span =" BETWEEN ".$db->qstr($from_date)." AND ".$db->qstr($cutoff_date);
		//echo $time_span;
									 
		if($this->is_comp=1){
			// fetch all encounters charged to the agency                                                                    
			$sql_patient ="SELECT it.pid, it.encounter_nr, it.refno, it.trxn_date, 
											fn_get_person_name(it.pid) AS fullname, it.agency_id 
											FROM seg_industrial_transaction AS it 
											INNER JOIN seg_industrial_company AS ic ON it.agency_id=ic.company_id 
											INNER JOIN seg_industrial_comp_emp AS cpe ON cpe.company_id=it.agency_id 
											LEFT JOIN care_person AS cp ON cp.pid=it.pid 
											WHERE it.agency_charged=1 AND ic.company_id=".$db->qstr($this->comp_id)."  
											AND DATE(it.trxn_date) $time_span 
											GROUP BY refno ORDER BY it.trxn_date"; 
//				 echo $sql_patient;
		
	//		SELECT cp.pid, it.refno,it.encounter_nr, it.trxn_date,
//											CONCAT(IFNULL(name_last,''),', ',IFNULL(name_first,''),' ',IFNULL(name_middle,'')) AS fullname
//											FROM care_person AS cp 
//											INNER JOIN seg_industrial_comp_emp AS ice 
//											ON cp.pid=ice.pid 
//											INNER JOIN seg_industrial_transaction AS it 
//											ON it.pid=cp.pid 
//											WHERE ice.company_id=".$db->qstr($this->comp_id)."         
//											AND it.agency_charged IN (1) 
//											ORDER BY it.trxn_date";
		}
		else{								
			$sql_patient = "SELECT cp.pid, it.refno, it.encounter_nr, it.trxn_date,
											CONCAT(IFNULL(name_last,''),', ',IFNULL(name_first,''),' ',IFNULL(name_middle,'')) AS fullname
											FROM care_person AS cp 
											INNER JOIN seg_industrial_transaction AS it 
											ON it.pid=cp.pid 
											WHERE it.pid=".$db->qstr($this->comp_id)."  
											ORDER BY it.trxn_date";
		}
										
		$result_patient = $db->Execute($sql_patient);       
		if ($result_patient) {   
			$this->_count = $result_patient->RecordCount();
			$this->Data=array();    
			while ($row=$result_patient->FetchRow())
			{ 
				$encounter_nr = $row['encounter_nr'];   
				$txn_date =substr($row['trxn_date'],0,-9);
				$txn_date2 = date('m/d/Y',strtotime($txn_date));
				$patient_total = 0;
				//-------------------fetch all unpaid radiology services availed by the employee
				$sql_radio = "SELECT rs.refno,rs.encounter_nr,rs.pid,rs.request_date,rs.source_req, rs.is_cash,
								sd.request_flag,rsg.department_nr, sd.service_code,sd.price_cash
								FROM seg_radio_serv AS rs 
								LEFT JOIN care_test_request_radio AS sd ON rs.refno = sd.refno    
								LEFT JOIN seg_radio_services AS r ON r.service_code = sd.service_code 
								LEFT JOIN seg_radio_service_groups AS rsg ON rsg.group_code = r.group_code 
								WHERE rs.source_req ='IC' 
								AND rs.status NOT IN ('deleted','hidden','inactive','void') 
								AND sd.status NOT IN ('deleted','hidden','inactive','void')
								AND (sd.request_flag IS NULL OR sd.request_flag NOT IN ('paid'))
								AND DATE(rs.request_date) $time_span 
								AND rs.encounter_nr=".$db->qstr($encounter_nr);   
				$result_radio = $db->Execute($sql_radio); 
				if ($result_radio) {     
					$XR = 0;
					$USD = 0;
					$SP = 0; 
					$CT = 0;
					while ($row2=$result_radio->FetchRow()){
						if($row2['department_nr']==164)
							$XR+= $row2['price_cash'];   						
						if($row2['department_nr']==165)
							$USD+= $row2['price_cash'];     					
						if($row2['department_nr']==166)
							$SP+= $row2['price_cash']; 					
						if($row2['department_nr']==167)
							$CT+= $row2['price_cash'];   
						$patient_total+=$row2['price_cash'];   	
					}
				}
				//----------------fetch all unpaid laboratory services availed by the employee
				$sql_lab = "SELECT rs.refno,rs.encounter_nr,rs.pid,rs.serv_dt,rs.source_req, rs.is_cash,
											r.group_code, sd.service_code,sd.price_cash
											FROM seg_lab_serv AS rs 
											LEFT JOIN seg_lab_servdetails AS sd ON rs.refno = sd.refno    
											LEFT JOIN seg_lab_services AS r ON r.service_code = sd.service_code 
											LEFT JOIN seg_lab_service_groups AS rsg ON rsg.group_code = r.group_code 
											WHERE rs.source_req ='IC' 
											AND rs.is_cash IN (0)  
											AND (sd.request_flag IS NULL OR sd.request_flag NOT IN ('paid')) 
											AND rs.status NOT IN ('deleted','hidden','inactive','void') 
											AND sd.status NOT IN ('deleted','hidden','inactive','void')
											AND DATE(rs.serv_dt) $time_span 
											AND rs.encounter_nr = ".$db->qstr($encounter_nr);   
				$result_lab = $db->Execute($sql_lab); 
				// items with # are temporary values
				if ($result_lab) {     
					$CBC = 0;
					$UA = 0;  
					$FA = 0;
					$FBS = 0; 
					$CREA = 0;
					$SUA = 0; 
					$LP = 0;    
					$HP = 0; 
					$IZ = 0;
					$ECG=0; 
					$DS = 0; 
					$PE = 0;  # physical examination charges
					$other_lab=0;
					while ($row3=$result_lab->FetchRow()){
						if($row3['service_code']=='CBC')
							$CBC+= $row3['price_cash'];   		    
						else if($row3['service_code']=='ICURIN' || $row3['service_code']=='URINE')
							$UA+= $row3['price_cash'];   
						else if($row3['service_code']=='FECAL')   #
							$FA+= $row3['price_cash'];   
						else if($row3['service_code']=='FBS')
							$FBS+= $row3['price_cash'];   
						else if($row3['service_code']=='CREA')
							$CREA+= $row3['price_cash'];
						else if($row3['service_code']=='LIPID')  #   lipid profile
							$LP+= $row3['price_cash'];   
						else if($row3['service_code']=='SUA')
							$SUA+= $row3['price_cash'];          
						else if($row3['service_code']=='HEPA')  # hepa profile
							$HP+= $row3['price_cash'];    
						else if($row3['group_code']=='IZ')  #  Immunization ~no code for this service yet, also it is very rarely used in the IC
							$IZ+= $row3['price_cash'];    
						else if($row3['service_code']=='ECG')
							$ECG+= $row3['price_cash'];                      
						else 
							$other_lab+=$row3['price_cash'];  
						$patient_total+=$row3['price_cash'];
					}
				}
				//----------------fetch all unpaid phramacy items availed by the employee
				$sql_pharma = "SELECT SUM((CASE WHEN ((ls.is_cash=0))                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
												THEN (ld.pricecharge*ld.quantity) ELSE 0.00 END)) AS price_charge									                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
												FROM seg_pharma_orders AS ls                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   
												INNER JOIN seg_pharma_order_items AS ld ON ld.refno=ls.refno                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             
												WHERE ls.request_source = 'IC' 
												AND DATE(ls.orderdate) $time_span 
												AND ls.encounter_nr = ".$db->qstr($encounter_nr);		
				$pharma_total = $db->GetOne($sql_pharma);		
				if($pharma_total)										
					$patient_total += $pharma_total;  
					
				//----------------fetch all unpaid misc items availed by the employee	
				$sql_misc = "SELECT SUM((CASE WHEN ((ls.is_cash=0))                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 
											THEN (ld.chrg_amnt*ld.quantity) ELSE 0.00 END)) AS price_charge									                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   
											FROM seg_misc_service AS ls                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 
											INNER JOIN seg_misc_service_details AS ld ON ld.refno=ls.refno                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              
											INNER JOIN seg_other_services AS os ON os.alt_service_code=ld.service_code
											INNER JOIN seg_cashier_account_subtypes AS cas ON cas.type_id=os.account_type 
											WHERE ls.request_source = 'IC' 
											AND (cas.type_id NOT IN (50) OR cas.name_short NOT IN ('Dental'))
											AND DATE(ls.chrge_dte) $time_span 
											AND ls.encounter_nr =".$db->qstr($encounter_nr);	
				$misc_total = $db->GetOne($sql_misc);
				if($misc_total) 					
					$patient_total += $misc_total; 
					
				//----------------fetch all unpaid dental services availed by the employee
				$sql_dental = "SELECT SUM((CASE WHEN ((ls.is_cash=0))                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 
												THEN (ld.chrg_amnt*ld.quantity) ELSE 0.00 END)) AS price_charge									                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   
												FROM seg_misc_service AS ls                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 
												INNER JOIN seg_misc_service_details AS ld ON ld.refno=ls.refno    
												INNER JOIN seg_other_services AS os ON os.alt_service_code=ld.service_code
												INNER JOIN seg_cashier_account_subtypes AS cas ON cas.type_id=os.account_type 
												WHERE ls.request_source = 'IC'   
												AND (cas.type_id IN (50) OR cas.name_short IN ('Dental')) 
												AND DATE(ls.chrge_dte) $time_span 
												AND ls.encounter_nr =".$db->qstr($encounter_nr);
				$DS = $db->GetOne($sql_dental); 		
				if($DS)
					$patient_total += $DS;						
					
					//----------------fetch all unpaid physical service fees charged to the employee
					//----------------service name for PE charge is still not set
				$sql_pe = "SELECT SUM((CASE WHEN ((ls.is_cash=0))                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 
												THEN (ld.chrg_amnt*ld.quantity) ELSE 0.00 END)) AS price_charge									                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   
												FROM seg_misc_service AS ls                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 
												INNER JOIN seg_misc_service_details AS ld ON ld.refno=ls.refno    
												INNER JOIN seg_other_services AS os ON os.alt_service_code=ld.service_code
												INNER JOIN seg_cashier_account_subtypes AS cas ON cas.type_id=os.account_type 
												WHERE ls.request_source = 'IC'                               
												AND os.name IN ('Physical Examination', 'Physical Examination Fee', 'PE Fee')
												AND DATE(ls.chrge_dte) $time_span 
												AND ls.encounter_nr =".$db->qstr($encounter_nr);
				$PE = $db->GetOne($sql_pe); 		
				if($PE)
					$patient_total += $PE;					                                                     
				
				
				
				$other_charges = $other_lab + $SP + $CT + $pharma_total + $misc_total;
				$grand_total += $patient_total;
				//display all encounters of employees with charges
				if($patient_total>0){
					$this->Data[]=array( 					
						$txn_date2,strtoupper($row['fullname']),         
						number_format($CBC,2), number_format($UA,2),
						number_format($FA,2), number_format($FBS,2),  
						number_format($CREA,2), number_format($SUA,2),
						number_format($LP,2), number_format($HP,2),
						number_format($IZ,2), number_format($ECG,2),
						number_format($XR,2), number_format($USD,2),
						number_format($other_charges,2),number_format($DS,2),
						number_format($PE,2), number_format($patient_total,2),                               
					);	
				}		                  
			}
			$this->GRAND_TOTAL = $grand_total;
		}
		else {
			 print_r($sql);
			 print_r($db->ErrorMsg());
			 exit;
			 # Error
		}          		
		//$this->_count=false;          
	}
}

$rep = new IC_Billing_Statement_Report($comp_id);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>
