<?php
	#include("roots.php");
	require('./roots.php');
	
	#include_once($root_path."/classes/fpdf/fpdf.php");
	include_once($root_path."/classes/fpdf/pdf.class.php");
	require_once($root_path.'include/inc_environment_global.php');
	include_once($root_path.'include/inc_date_format_functions.php');
	
	require_once($root_path.'include/care_api_classes/class_radiology.php');
	$srvObj=new SegRadio();
	require_once($root_path.'include/care_api_classes/class_department.php');
	$dept_obj=new Department;
	require_once($root_path.'include/care_api_classes/class_person.php');
	$person_obj=new Person;
	require_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;
	require_once($root_path.'include/care_api_classes/class_personell.php');
	$pers_obj=new Personell;
	require_once($root_path.'include/care_api_classes/class_ward.php');
	$ward_obj=new Ward;
	
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	$objInfo = new Hospital_Admin();
	
	require($root_path.'classes/adodb/adodb.inc.php');
	
	global $db;
	
	$pdf = new PDF("L",'mm','Legal');
	$pdf->AliasNbPages();   #--added
	$pdf->AddPage("L");
		
	$borderYes="1";
	$borderNo="0";
	$newLineYes="1";
	$newLineNo="0";
	$space=2;
	
	$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',20,10,20,20);
	
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
		
	$pdf->SetFont("Times","B","10");
  	#$pdf->Cell(0,4,'Republic of the Philippines',$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,$row['hosp_country'],$borderNo,$newLineYes,'C');
	$pdf->Ln(1);
	#$pdf->Cell(0,4,'DEPARTMENT OF HEALTH', $border_0,1,'C');
	$pdf->Cell(0,4,$row['hosp_agency'], $border_0,1,'C');
	$pdf->Ln(2);
	#$pdf->Cell(0,4,'DAVAO MEDICAL CENTER',$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,$row['hosp_name'],$borderNo,$newLineYes,'C');
	#$pdf->Cell(0,4,'OUTPATIENT and PREVENTIVE CARE CENTER',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	$pdf->SetFont("Times","B","8");
   #$pdf->Cell(0,4,'JICA Bldg., JP Laurel Avenue, Davao City',$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,$row['hosp_addr1'],$borderNo,$newLineYes,'C');
   $pdf->Ln(2);
	$pdf->SetFont("Times","B","10");
   $pdf->Cell(0,4,'DEPARTMENT OF RADIOLOGICAL & IMAGING SCIENCES',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	$pdf->SetFont("Times","I","10");
	$pdf->Cell(0,4,'ROENTGENOLOGICAL CLASSIFICATION CASES\' REPORT',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	
	$rpt_cases = $_GET['rpt_cases'];
	$datefrom = $_GET['fromdate'];
	$dateto = $_GET['todate'];
	
	$tpl = $_GET['tpl'];
	$sc = $_GET['sc'];
	$not = $_GET['not'];
	
	#$user = $_GET['user'];
	
	
	$pdf->SetFont("Times","B","10");
	if ($grp_kind == 'all'){
		$pdf->Cell(0,4,'ALL RADIOLOGICAL REQUESTS',$borderNo,$newLineYes,'C');
		$pdf->Ln(4);
	}elseif ($grp_kind == 'wo_result'){
		$pdf->Cell(0,4,'RADIOLOGICAL REQUEST WITHOUT FINDINGS',$borderNo,$newLineYes,'C');
		$pdf->Ln(4);
	}elseif ($grp_kind == 'w_result'){
		$pdf->Cell(0,4,'RADIOLOGICAL REQUEST WITH FINDINGS',$borderNo,$newLineYes,'C');
		$pdf->Ln(4);
	}
	
	$fromtime = '00:00:00';
	$totime = '00:00:00';
	
	$report_info = $srvObj->getListRadioClassification($rpt_cases, $tpl, $sc, $datefrom, $dateto, $not);
	#echo $srvObj->sql;
	$totalcount = $srvObj->count;
	
	$pdf->SetFont("Times","","10");
	/*
	$pdf->Cell(35,4,'Prepared By',"",0,'L');
	$pdf->Cell(5,4,':',"",0,'L');
	$pdf->Cell(235,4,strtoupper($user),"",0,'L');
	$pdf->Ln($space*2);
	*/
	if ($rpt_cases==$tpl)
		$cases = "TPL";	
	elseif ($rpt_cases==$sc)	
		$cases = "SENIOR CITIZEN";	
	elseif ($rpt_cases==$not)	
		$cases = "NOT CLASSIFIED";	
	else{
		$info = $srvObj->getChargeTypeInfo($rpt_cases);
		$cases = mb_strtoupper($info['charge_name']);	
	}
	
	if ($rpt_cases!='all'){
		#$pdf->Cell(270,4,'Laboratory Section : '.$service_info['name'],"",0,'L');
		$pdf->Cell(35,4,'Classification',"",0,'L');
		$pdf->Cell(5,4,':',"",0,'L');
		$pdf->Cell(235,4,$cases,"",0,'L');
	}else{
		#$pdf->Cell(270,4,'Laboratory Section : ALL SECTION',"R",0,'L');
		$pdf->Cell(35,4,'Classification',"",0,'L');
		$pdf->Cell(5,4,':',"",0,'L');
		$pdf->Cell(235,4,'ALL CASES',"",0,'L');
	}
	#$pdf->Cell(60,4,'Date : '.date("M. d, Y "),"",0,'L');
	$pdf->Cell(10,4,'Date',"",0,'L');
	$pdf->Cell(5,4,':',"",0,'L');
	$pdf->Cell(45,4,date("M. d, Y "),"",0,'L');
	
	if (($datefrom)&&($dateto)){
		$pdf->Ln($space*2);
		$pdf->Cell(35,4,'Start Date',"",0,'L');
		$pdf->Cell(5,4,':',"",0,'L');
		$pdf->Cell(235,4,date("F d, Y ", strtotime($datefrom)),"",0,'L');
		$pdf->Ln($space*2);
		$pdf->Cell(35,4,'End Date',"",0,'L');
		$pdf->Cell(5,4,':',"",0,'L');
		$pdf->Cell(235,4,date("F d, Y ", strtotime($dateto)),"",0,'L');
	}
	
	$pdf->Ln($space*2);
	#$pdf->Cell(270,4,'Number of Records : '.$totalcount,"",0,'L');
	$pdf->Cell(35,4,'Number of Records',"",0,'L');
	$pdf->Cell(5,4,':',"",0,'L');
	$pdf->Cell(235,4,$totalcount,"",0,'L');
	
	#$pdf->Cell(60,4,'Time : '.date("h:i:s A"),"",0,'L');
	$pdf->Cell(10,4,'Time',"",0,'L');
	$pdf->Cell(5,4,':',"",0,'L');
	$pdf->Cell(45,4,date("h:i:s A"),"",0,'L');
	
	$pdf->Ln($space*2);
	#$pdf->Cell(270,4,'Currency : Philippine Peso (Php)',"",0,'L');
	$pdf->Cell(35,4,'Currency',"",0,'L');
	$pdf->Cell(5,4,':',"",0,'L');
	$pdf->Cell(35,4,'Philippine Peso (Php)',"",0,'L');
	
	#$pdf->Cell(60,4,'Page : '.$pdf->PageNo().' / {nb}',"",0,'L');
	
	$pdf->Ln($space*4);
	$pdf->SetFont('Arial','B',8);
	$pdf->Cell(10,4,"","",0,'L');	
	$pdf->Cell(20,4,'PATIENT ID',"TB",0,'L');
	$pdf->Cell(20,4,'BATCH NO.',"TB",0,'L');
	$pdf->Cell(30,4,'PATIENT NAME',"TB",0,'L');
	$pdf->Cell(35,4,'ORDER DATE & TIME',"TB",0,'L');
	
	#$pdf->Cell(20,4,'TIME',"TB",0,'L');
	/*
	if ($grpview==0){
		$pdf->Cell(35,4,'TEST',"TB",0,'L');
	
		if ($grp_code=='all'){
			$pdf->Cell(20,4,'SECTION',"TB",0,'L');
		}
	}	
	
	if ($discountID=='all'){
		$pdf->Cell(30,4,'CLASSIFICATION',"TB",0,'L');
	}
	*/
	#$pdf->Cell(25,4,'P. TYPE',"TB",0,'L');
	$pdf->Cell(23,4,'PATIENT TYPE',"TB",0,'L');
	$pdf->Cell(25,4,'CLASS',"TB",0,'L');
	$pdf->Cell(42,4,'DEPT/LOCATION',"TB",0,'L');
	$pdf->Cell(35,4,'REQUEST',"TB",0,'L');
	$pdf->Cell(20,4,'SECTION',"TB",0,'L');
	$pdf->Cell(30,4,'GROSS AMOUNT',"TB",0,'R');
	$pdf->Cell(2,4,'',"TB",0,'R');
	$pdf->Cell(25,4,'AMOUNT PAID',"TB",0,'R');
	$pdf->Cell(2,4,'',"TB",0,'R');
	$pdf->Cell(25,4,'AMOUNT BAL.',"TB",1,'R');
	
	#$pdf->Cell(50,4,'',"TB",1,'L');
	if ($totalcount){
			$i=1;
			$all_total_amount = 0;
			$total_paid = 0;
			$total_amount_bal = 0;
			$pdf->SetFont('Times','',8);	
			while ($row=$report_info->FetchRow()){
				$pdf->Cell(10,4,$i.".)","",0,'L');
				$pdf->Cell(20,4,$row['patientID'],"",0,'L');
				$pdf->Cell(20,4,$row['refno'],"",0,'L');
				
				#$pdf->Cell(30,4,ucwords(strtolower($row['ordername'])),"",0,'L');
				$x = $pdf->GetX();
 				$y = $pdf->GetY();
				$pdf->SetXY($x, $y);
				$pdf->MultiCell(30, 5, ucwords(strtolower($row['ordername'])), '', 'L','');
				$pdf->SetXY($x+30, $y);
				$pdf->Cell(35,4,$row['request_date'].'   '.date("h:i:s A",strtotime($row['request_time'])),"",0,'L');
				
				#$pdf->Cell(17,4,$row['serv_tm'],"",0,'C');
				#$pdf->Cell(20,4,date("h:i:s A",strtotime($row['serv_tm'])),"",0,'L');
				/*
				if ($grpview==0){
					$pdf->Cell(35,4,$row['service_name'],"",0,'L');
		
					if ($grp_code=='all'){
						$pdf->Cell(20,4,$row['dept_name'],"",0,'L');
					}
				}
				
				if ($discountID=='all'){
					if ((empty($row['classID']))|| ($row['classID']==" ") || ($row['classID']==""))
						$classify = "NONE";
					else
						$classify = $row['classID'];		
					
					$pdf->Cell(30,4,$classify,"",0,'L');
				}
				*/
				if ($row['encounter_type']==1){
					$patient_type = "ERPx";
					#$patient_type = "ER";
					$location = "ER";
				}elseif ($row['encounter_type']==2){
					$patient_type = "OPDPx";
					#$patient_type = "OPD";
					$dept = $dept_obj->getDeptAllInfo($row['current_dept_nr']);
					$location = $dept['id'];
				}elseif (($row['encounter_type']==3)||($row['encounter_type']==4)){
					if ($row['encounter_type']==3)
						$wer = "(ER)";
					elseif ($row['encounter_type']==4)	
						$wer = "(OPD)";
						
					$patient_type = "IPDPx ".$wer;
					
					#$patient_type = "IPD";
					$ward = $ward_obj->getWardInfo($row['current_ward_nr']);
					#$location = $ward['name'];
					$location = $ward['ward_id']." : Rm.#".$row['current_room_nr'];
				}else{
					$patient_type = "Walkin";
					$location = '';
				}
				#$pdf->SetXY($x+35, $y);
				$pdf->Cell(25,4,$patient_type,"",0,'L');
				/*
				#TPL
				#if (($row['is_cash']) && ($row['is_urgent']))
				if ($row['is_tpl'])
					$paidstatus = 'TPL';
				//elseif (!($row['is_cash']) &&($row['is_urgent']))
				elseif (!($row['is_cash']))
					$paidstatus = 'Charge';
				elseif (($row['is_cash']) && !($row['is_urgent']))
					$paidstatus = 'Cash';
					
				$pdf->Cell(15,4,$paidstatus,"",0,'L');
				*/
				if ($row['senior_ID'])
					$cases2 = "Senior Citizen";
				elseif ($row['is_tpl'])	
					$cases2 = "TPL";
				else{
					$info2 = $srvObj->getChargeTypeInfo($row['type_charge']);
					$cases2 = mb_strtoupper($info2['charge_name']);
				}	
				
				if (empty($cases2))
					$cases2 = "None";
				
				$pdf->Cell(25,4,$cases2,"",0,'L');
				$pdf->Cell(35,4,$location,"",0,'L');
				
				$report_info_details = $srvObj->getPatientListDetails($row['refno']);
				#echo "<br>".$srvObj->sql;
				$totalcount = $srvObj->count;
				#$i=1;
				// if ($totalcount){
					// while ($row2=$report_info_details->FetchRow()){
						$x = $pdf->GetX();
						$y = $pdf->GetY();
						$pdf->SetXY($x+5, $y);
						#$pdf->Cell(35,4,mb_strtoupper($row2['service_name']),"",0,'L');
						#$pdf->Cell(20,4,$row2['dept_name_short'],"",0,'L');
						$pdf->MultiCell(35, 5, mb_strtoupper($row['service_name']), '', 'L','');
						$x = $pdf->GetX();
						$y = $pdf->GetY();
						$pdf->SetXY($x+240, $y-5);
						#$pdf->SetY($y);
						$pdf->MultiCell(20, 5, $row['dept_name'], '', 'C','');
						$pdf->Cell(200,5,"","",0,'L');
					// }
				// }	
				
				$price = $srvObj->getSumPerTransaction($row['refno']);
				#echo "<br>".$srvObj->sql;
				if ($row['is_cash'])
					$total_amount = $price['price_cash'];
				else	
					$total_amount = $price['price_charge'];
				
				#$pdf->Cell(7,4,'Php',"",0,'L');
				$x = $pdf->GetX();
				$y = $pdf->GetY();
				$pdf->SetXY($x+60, $y-5);
				
				$all_total_amount = $all_total_amount + $total_amount;
				$pdf->Cell(30,4,number_format($total_amount,2),"",0,'R');
				
				$paid = $srvObj->getSumPaidPerTransaction($row['refno'],$row['patientID']);
				#echo "<br>".$srvObj->sql;
				$pdf->Cell(2,4,'',"",0,'R');
				$total_paid = $total_paid + $paid['amount_paid'];
				
				$pdf->Cell(25,4,number_format($paid['amount_paid'],2),"",0,'R');
				$pdf->Cell(2,4,'',"",0,'R');
				
				$amount_bal = $total_amount - $paid['amount_paid'];
				
				$total_amount_bal = $total_amount_bal + $amount_bal;
				#money_format('%(#10n', $number)
				$pdf->Cell(25,4,number_format($amount_bal,2),"",1,'R');
				#$amount_bal = number_format($amount_bal,2);
				#$pdf->Cell(30,4,money_format('%(n', $amount_bal),"",1,'R');
				//$pdf->Cell(30,4,'',"",1,'L');
				$i++;
			}	
		$pdf->Cell(30,4,'',"",1,'L');
		$pdf->Cell(10,4,"","",0,'L');	
		$pdf->Cell(320,4,'',"T",1,'L');
		
		$pdf->SetFont('Times','B',10);
		$pdf->Ln($space*2);	

		$pdf->Cell(10,4,"","",0,'L');	
		$pdf->Cell(55,4,"TOTAL GROSS AMOUNT","",0,'L');	
		$pdf->Cell(10,4,": Php","",0,'L');	
		$pdf->Cell(2,4,"","",0,'L');	
		$pdf->Cell(20,4,number_format($all_total_amount, 2),"",1,'R');	
		
		$pdf->Cell(10,4,"","",0,'L');	
		$pdf->Cell(55,4,"TOTAL AMOUNT PAID","",0,'L');	
		$pdf->Cell(10,4,": Php","",0,'L');	
		$pdf->Cell(2,4,"","",0,'L');	
		$pdf->Cell(20,4,number_format($total_paid, 2),"",1,'R');	
		
		$pdf->Cell(10,4,"","",0,'L');	
		$pdf->Cell(55,4,"TOTAL AMOUNT BALANCE","",0,'L');	
		$pdf->Cell(10,4,": Php","",0,'L');	
		$pdf->Cell(2,4,"","",0,'L');	
		$pdf->Cell(20,4,number_format($total_amount_bal, 2),"",0,'R');	
		
	}else{
		$pdf->SetFont('Times','',10);	
		$pdf->Ln($space*4);
		$pdf->Cell(337,4,'No query results available at this time...',"",0,'C');
	}
	
	$pdf->Output();	
?>