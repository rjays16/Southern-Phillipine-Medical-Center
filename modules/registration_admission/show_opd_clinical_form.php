<?php
	#edited by VAN 05-01-08
	include("roots.php");
	#commented by Art 01/16/2014
	//include_once($root_path."/classes/fpdf/fpdf.php");
	//include_once($root_path."/classes/fpdf/pdf.class.php"); 
	include_once($root_path."/classes/fpdf/cert-pdf-nocode.php"); #added by Art 01/16/2014
	require_once($root_path.'include/inc_environment_global.php');
	include_once($root_path.'include/inc_date_format_functions.php');

	require_once($root_path.'/include/care_api_classes/class_drg.php');
	$objDRG= new DRG;

	include_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;

	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	$objInfo = new Hospital_Admin();

	define('IPBMIPD_enc', 13);
	define('IPBMOPD_enc', 14);
	define('IPBMIPD_enc_STR', '13');
	define('IPBMOPD_enc_STR', '14');
	
	if ($_GET['encounter_nr']) {
		if (!($enc_info = $enc_obj->getEncounterInfo($_GET['encounter_nr']))){
			echo '<em class="warn">Sorry but the page cannot be displayed!</em>';
			exit();
		}
		#echo "enc_obj->sql = '".$enc_obj->sql."' <br> \n";		
		extract($enc_info);
	}else{
			echo '<em class="warn">Sorry but the page cannot be displayed! <br>Invalid Case Number! </em>';
			exit();	
	}
	
	$border="1";
	$border2="0";
	$space=2;
	#$fontSizeLabel=9;
	$fontSizeLabel=10;
	$fontSizeInput=12;
	$fontSizeHeading=13;

	//Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, int fill [, mixed link]]]]]]])
	//s$pdf = new FPDF();
	#$pdf = new PDF();
	$pdf  = new PDF("P","mm","Letter");//added by art 01/16/2014
	$pdf->AliasNbPages();   #--added
	#$pdf->SetAutoPageBreak('true','10');
	$pdf->AddPage("P");
	/* commented by art 01/18/2014
	$pdf->SetFont('Arial','',$fontSizeLabel+3);
	$pdf->Cell(150,4,'HRN : ',"$border2",0,'R');
	$pdf->SetFont('Arial','B',$fontSizeHeading+8);
	$pdf->Cell(0,4	,$pid,"$border2",0,'R');
	$pdf->Ln(1); */
/*
	$pdf->SetFont("Arial","","11");
	$pdf->Cell(0,4,'Republic of the Philippines',$border2,1,'C');
	#$pdf->SetFont("Arial","","11");
	$pdf->Cell(0,4,'DEPARTMENT OF HEALTH',$border2,1,'C');

	$pdf->Ln(1);
	$pdf->SetFont("Arial","B","12");
	$pdf->Cell(0,4,'DAVAO MEDICAL CENTER',$border2,1,'C');

	$pdf->SetFont("Arial","","11");
	$pdf->Cell(0,4,'Bajada, Davao City',$border2,1,'C');
*/
	if ($row = $objInfo->getAllHospitalInfo()) {			
		$row['hosp_agency'] = strtoupper($row['hosp_agency']);
		$row['hosp_name']   = strtoupper($row['hosp_name']);
	}
	else {
		$row['hosp_country'] = "Republic of the Philippines";
		$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
		$row['hosp_name']    = "Davao Medical Center";
        $row['hosp_addr1']   = "JICA Bldg. JP Laurel Bajada, Davao City";
	}

	$pdf->SetFont("Arial","","11");
	$pdf->Cell(0,4,$row['hosp_country'],$border2,1,'C');
	#$pdf->SetFont("Arial","","11");
	$pdf->Cell(0,4,$row['hosp_agency'],$border2,1,'C');

	$pdf->Ln(1);
	$pdf->SetFont("Arial","B","12");
	$pdf->Cell(0,4,$row['hosp_name'],$border2,1,'C');

	$pdf->SetFont("Arial","","11");
	$pdf->Cell(0,4,$row['hosp_addr1'],$border2,1,'C');
	
	$pdf->SetFont('Arial','B',"13");
	$pdf->Cell(0,5,'Outpatient Preventive Care Center Clinical Form',$border2,1,'C');

	//added by art 01/18/2014
	$pdf->Image('image/logo_doh.jpg',25,10,20,20);
	$pdf->Image('image/dmc_logo.jpg',170,10,20,20);
	//end art

	#$pdf->Ln($space*2);
	$pdf->Ln($space*6);
	
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(30,4,'Case No. : ',"$border2",0,'L');
	$pdf->SetFont('Arial','B',$fontSizeHeading+5);
	$pdf->Cell(50,4,$encounter_nr,"$border2",0,'L');
	#$pdf->SetFont('Arial','',$fontSizeLabel+3);
	#$pdf->Cell(50,4,'Patient ID : ',"$border2",0,'R');
	#$pdf->SetFont('Arial','B',$fontSizeHeading+5);
	#$pdf->Cell(30,4	,$pid,"$border2",0,'L');
	
	#$pdf->Ln($space*3);
	#$pdf->SetFont('Arial','',$fontSizeLabel+3);
	#$pdf->Cell(27,4,'Department : ',"",0,'R');
	#$pdf->SetFont('Arial','B',$fontSizeHeading+3);
	#$pdf->MultiCell(80, 4,mb_strtoupper($name_formal), '0', 'R','');
	//added by art art 01/22/2014
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(80,4,'HRN : ',"$border2",0,'R');
	$pdf->SetFont('Arial','B',$fontSizeHeading+8);
	#$pdf->MultiCell(0, 4,'Department : '.mb_strtoupper($name_formal), '0', 'R',''); commented by art 01/18/2014
	$pdf->MultiCell(0, 4,$pid, '0', 'R','');
	// if($encounter_type==14){
	// 	$pdf->Ln($space*1);
	// 	$pdf->SetFont('Arial','',$fontSizeLabel);
	// 	$pdf->Cell(167,4,'HOMIS ID: ',"$border2",0,'R');
	// 	$pdf->SetFont('Arial','B',$fontSizeHeading);
	// 	#$pdf->MultiCell(0, 4,'Department : '.mb_strtoupper($name_formal), '0', 'R',''); commented by art 01/18/2014
	// 	$pdf->MultiCell(0, 4,$homis_id, '0', 'R','');
	// }
	
	//end art 
	// $homis_id=123123;
	// $encounter_type=2;
	$pdf->Ln($space*1);
	
	$pdf->SetFont('Arial','',$fontSizeLabel+1);
	$pdf->Cell(50,4,'Consultation Date and Time : ',"",0,'L');
	$pdf->SetFont('Arial','B',$fontSizeLabel+1);
	$pdf->Cell(45,4,@formatDate2Local($er_opd_datetime,$date_format,1),"",0,'L');

	//added by art 01/18/2014
	if($encounter_type==IPBMOPD_enc){
		$pdf->SetFont('Arial','',$fontSizeLabel);
		$pdf->Cell(22,4,'Department : ',"",0,'L');
		$pdf->SetFont('Arial','B',$fontSizeHeading);
		$pdf->Cell(30,4	,mb_strtoupper($name_formal),"$border2",0,'L');
		$pdf->SetFont('Arial','',$fontSizeLabel);
		$pdf->Cell(18,4,'HOMIS ID: ',"$border2",0,'L');
		$pdf->SetFont('Arial','B',$fontSizeHeading);
		$pdf->Cell(0, 4,$homis_id,"$border2",0,'L');
	}else{
		$pdf->SetFont('Arial','',$fontSizeLabel+1);
		$pdf->Cell(25,4,'Department : ',"",0,'R');

		if(strlen($name_formal) > 21)
			$pdf->SetFont('Arial','B',$fontSizeHeading-2);
		else
			$pdf->SetFont('Arial','B',$fontSizeHeading+2);
		$pdf->Cell(55,4	,mb_strtoupper($name_formal),"$border2",0,'L');
	}
	//end
	

	$pdf->Ln($space*3);
	#$pdf->SetFont('Arial','',$fontSizeLabel+3);
	#$pdf->Cell(27,4,'Department : ',"",0,'L');
	#$pdf->SetFont('Arial','B',$fontSizeHeading+3);
	#$pdf->MultiCell(80, 4,mb_strtoupper($name_formal), '0', 'L','');
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(48,4,'Last Name',"TR",0,'L');
	$pdf->Cell(48,4,'First Name',"TLR",0,'L');
	$pdf->Cell(48,4,'Middle Name',"TLR",0,'L');
	$pdf->Cell(48,4,'Maiden Name',"TL",0,'L');
	
	$pdf->Ln();	
	
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	
	$pdf->SetFont('Arial','B',$fontSizeLabel+3);
	$pdf->Cell(48,12,'',"RB",0,'L');
	$pdf->Cell(48,12,'',"LRB",0,'L');
	$pdf->Cell(48,12,'',"LRB",0,'L');
	$pdf->Cell(48,12,'',"LB",0,'L');
	
	$pdf->SetXY($x, $y);
	$pdf->MultiCell(47, 4, mb_strtoupper($name_last), '', 'L','');
	
	$pdf->SetXY($x+48, $y);
	$pdf->MultiCell(47, 4, mb_strtoupper($name_first), '0', 'L','');
	
	$pdf->SetXY($x+96, $y);
	$pdf->MultiCell(47, 4, mb_strtoupper($name_middle), '0', 'L','');
	
	$pdf->SetXY($x+144, $y);
	$pdf->MultiCell(47, 4, mb_strtoupper($name_maiden), '0', 'L','');
	#$pdf->Ln($space*2);
	$pdf->SetY($y+12);
	#$pdf->Ln($space*2);
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(10,8,'Age : ',"",0,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	#if ($age)
	#	$age = $age." old ";
	
	if (stristr($age,'years')){
		$age = substr($age,0,-5);
		if ($age>1)
			$labelyear = "years";
		else
			$labelyear = "year";
				
		$age = floor($age)." ".$labelyear;
	}elseif (stristr($age,'year')){	
		$age = substr($age,0,-4);
		if ($age>1)
			$labelyear = "years";
		else
			$labelyear = "year";
			
		$age = floor($age)." ".$labelyear;
		
	}elseif (stristr($age,'months')){	
		$age = substr($age,0,-6);
		if ($age>1)
			$labelmonth = "months";
		else
			$labelmonth = "month";
			
		$age = floor($age)." ".$labelmonth;	
		
	}elseif (stristr($age,'month')){	
		$age = substr($age,0,-5);
		
		if ($age>1)
			$labelmonth = "months";
		else
			$labelmonth = "month";
			
		$age = floor($age)." ".$labelmonth;		
		
	}elseif (stristr($age,'days')){	
		$age = substr($age,0,-4);
					
		if ($age>30){
			$age = $age/30;
			if ($age>1)
				$label = "months";
			else
				$label = "month";
			
		}else{
			if ($age>1)
				$label = "days";
			else
				$label = "day";
		}
						
		$age = floor($age).' '.$label;	
			
	}elseif (stristr($age,'day')){	
		$age = substr($age,0,-3);
		
		if ($age>1)
			$labelday = "days";
		else
			$labelday = "day";
			
		$age = floor($age)." ".$labelday;		
	}else{
		if ($age){
			if ($age>1)
				$labelyear = "years";
			else
				$labelyear = "year";
			
			$age = $age." ".$labelyear;
		}else
			$age = "0 day";		
	}
	
	$pdf->Cell(38, 8, $age." old", '', 0,'L');
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(10,8,'Sex : ',"",0,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	
	if ($sex=='f')
		$gender = 'FEMALE';
	elseif ($sex=='m')
		$gender = 'MALE';	
	
	$pdf->Cell(38, 8, mb_strtoupper($gender), '', 0,'L');
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(22,8,'Civil Status : ',"",0,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(28, 8, mb_strtoupper($civil_status), '', 0,'L');
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(27,8,'Contact Number : ',"",0,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	
	$contact = $phone_1_nr;
	if (!isset($contact) || empty($contact)) $contact = $cellphone_1_nr;
	if (!isset($contact) || empty($contact)) $contact = $phone_2_nr;
	if (!isset($contact) || empty($contact)) $contact = $cellphone_2_nr;
	
	$pdf->Cell(22, 8, $contact, '', 1,'L');
	
	$pdf->SetY($y+17);
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(20,8,'Address : ',"",0,'L');
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->SetXY($x-2, $y+2);
	/*
	if ($street_name)
		$street_name = "$street_name, ";
	else
		$street_name = "";	
		
	if (!($brgy_name))
		$brgy_name = "NOT PROVIDED";
		
	if(stristr(trim($mun_name), 'city') === FALSE){
		if (!empty($mun_name)){
			$province = ", ".trim($prov_name);
		}else{
			$province = trim($prov_name);;
		}
	}	
	
	#$address = trim($street_name)." ".trim($brgy_name).", ".trim($mun_name)." ".trim($zipcode)." ".trim($prov_name);
	$address = trim($street_name)." ".trim($brgy_name).", ".trim($mun_name)." ".$province;
	*/
	
	if ($street_name){
		if ($brgy_name==NULL)
			$street_name = $street_name." ";
		else
			$street_name = $street_name.", ";	
	}#else
		#$street_name = "";	
				
				
		
	if ((!($brgy_name)) || ($brgy_name=="NOT PROVIDED"))
		$brgy_name = "";
	else 
		$brgy_name  = $brgy_name.", ";	
					
	if ((!($mun_name)) || ($mun_name=="NOT PROVIDED"))
		$mun_name = "";		
	else{	
		if ($brgy_name)
			$mun_name = $mun_name;	
		#else
			#$mun_name = $mun_name;		
	}			
	
	if ((!($prov_name)) || ($prov_name=="NOT PROVIDED"))
		$prov_name = "";		
	#else
	#	$prov_name = $prov_name;			
				
	if(stristr(trim($mun_name), 'city') === FALSE){
		if ((!empty($mun_name))&&(!empty($prov_name))){
			if ($prov_name!="NOT PROVIDED")	
				$prov_name = ", ".trim($prov_name);
			else
				$prov_name = "";	
		}else{
			#$province = trim($prov_name);
			$prov_name = "";
		}
	}else
		$prov_name = " ";	
				
	$address = $street_name.$brgy_name.$mun_name.$prov_name;
	
	// added by carriane 12/12/18;
	// adjust font size if length exceeds to 80 characters
	if(strlen($address) > 80 && strlen($address) < 91)
		$pdf->SetFont('Arial','', 9);
	elseif(strlen($address) > 90 && strlen($address) < 100 )
		$pdf->SetFont('Arial','', 8);
	elseif(strlen($address) > 100)
		$pdf->SetFont('Arial','', 7);
	// end carriane

	$pdf->MultiCell(170, 4,mb_strtoupper(rtrim($address, ', ')), '0', 'L','');
	
	$pdf->Ln($space);
	#$x = $pdf->GetX();
	#$y = $pdf->GetY();
	#$pdf->SetXY($x, $y);
	#$pdf->SetY($y+13);
	$pdf->SetY($y+5);
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(55,8,'Country of Nationality : ',"",0,'L');
	$pdf->Cell(55,8,'Religion : ',"",0,'L');
	$pdf->Cell(55,8,'Occupation : ',"",0,'L');
	$pdf->Ln($space*2);
	
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(55, 8, mb_strtoupper($citizenship), '', 0,'L');
	$pdf->Cell(55, 8, mb_strtoupper($religion), '', 0,'L');
	$pdf->Cell(55, 8, mb_strtoupper($occupation), '', 0,'L');
	
	$pdf->Ln($space);
	#$pdf->SetY($y+22);
	$pdf->SetY($y+13);
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(110,8,'Birth Place : ',"",0,'L');
	$pdf->Cell(55,8,'Birth Date : ',"",0,'L');
	#$pdf->Cell(55,8,'Department : ',"",0,'L');
	
	$pdf->Ln($space*3);
	
	$pdf->SetFont('Arial','',$fontSizeLabel);
	#$pdf->Cell(55, 4, mb_strtoupper($place_birth), '', 0,'L');
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	
	$pdf->SetXY($x, $y);
	
	$pdf->MultiCell(110, 4,mb_strtoupper($place_birth), '', 'L','');
	
	$pdf->SetXY($x+110, $y);
	if ($date_birth)
		#$pdf->Cell(55,4,@formatDate2Local($date_birth,$date_format),"",0,'L');
		$pdf->MultiCell(50, 4,@formatDate2Local($date_birth,$date_format), '0', 'L','');
	else
		#$pdf->Cell(55,4,'',"",0,'L');
		$pdf->MultiCell(50, 4,'', '', 'L','');
		
	#$pdf->SetXY($x+110, $y);	
	#$pdf->Cell(55, 4, mb_strtoupper($name_formal), '', 0,'L');
	#$pdf->MultiCell(80, 4,mb_strtoupper($name_formal), '0', 'L','');
	
	#$pdf->Ln($space*4);
	$pdf->Ln($space);
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(70,8,'Name of Father : ',"T",0,'L');
	$pdf->Cell(70,8,'Name of Mother : ',"T",0,'L');
	$pdf->Cell(52,8,'Name of Guardian :',"T",0,'L');
	
	$pdf->Ln($space*3);
	
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	
	$pdf->SetXY($x, $y);
	
	$pdf->SetFont('Arial','',$fontSizeLabel);
	
	$FmiddleInitial = "";
		if (trim($father_mname)!=""){
			$thisMI=split(" ",$father_mname);	
			foreach($thisMI as $value){
				if (!trim($value)=="")
					$FmiddleInitial .= $value[0];
			}
			if (trim($FmiddleInitial)!="")
				$FmiddleInitial = " ".$FmiddleInitial.".";
		}
	
	$father_name = $father_fname." ".$FmiddleInitial." ".$father_lname;
	
	$pdf->MultiCell(68, 4,mb_strtoupper($father_name), '', 'L','');
	
	$pdf->SetXY($x+70, $y);	
	#$pdf->Cell(55, 4, mb_strtoupper($name_formal), '', 0,'L');
	$MmiddleInitial = "";
		if (trim($mother_mname)!=""){
			$thisMI=split(" ",$mother_mname);	
			foreach($thisMI as $value){
				if (!trim($value)=="")
					$MmiddleInitial .= $value[0];
			}
			if (trim($MmiddleInitial)!="")
				$MmiddleInitial = " ".$MmiddleInitial.".";
		}
	
	$mother_name = $mother_fname." ".$MmiddleInitial." ".$mother_lname;
	$pdf->MultiCell(68, 4,mb_strtoupper($mother_name), '', 'L','');
	
	$pdf->SetXY($x+140, $y);	
	#$pdf->Cell(55, 4, mb_strtoupper($name_formal), '', 0,'L');
	$pdf->MultiCell(55, 4,mb_strtoupper($guardian_name), '', 'L','');
	
	$pdf->Ln($space);
	
	#$pdf->SetY($y+8);
	$pdf->SetY($y+3);
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(70,8,'Name of Spouse : ',"",0,'L');
	$pdf->Cell(70,8,'Employer : ',"",0,'L');
	
	if ($parent_mss_id)
		$mss_classification = $parent_mss_id." (".$mss_id.")";
	else
		$mss_classification = $mss_id;	
	$pdf->Cell(25,8,'MSS CLASS : ',"",0,'L');
	
	if ($encounter_status=='phs')
		$mss_classification = 'CLASS D';
	
	$pdf->SetFont('Arial','',$fontSizeLabel+2);
	$pdf->Cell(40,8,$mss_classification,"",0,'L');
	
	$pdf->Ln($space);
	
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	
	$pdf->SetXY($x, $y+4);
	
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->MultiCell(68, 4,mb_strtoupper($spouse_name), '', 'L','');
	
	$pdf->SetXY($x+70, $y+4);	
	#$pdf->Cell(55, 4, mb_strtoupper($name_formal), '', 0,'L');
	$pdf->MultiCell(68, 4,mb_strtoupper($employer), '', 'L','');
	
	$pdf->SetXY($x+145, $y+4);	
	#$pdf->Cell(55, 4, mb_strtoupper($name_formal), '', 0,'L');
	#$pdf->MultiCell(52, 4,$mss_id ." (".mb_strtoupper($mss_class).")", '', 'L','');
	$pdf->SetFont('Arial','',$fontSizeLabel-2);
	
	#$pdf->MultiCell(55, 4,'CLASS '.$mss_id, '', 'L','');
	if($mss_no)
		$pdf->MultiCell(52, 4,'MSS NO.: '.$mss_no, '', 'L','');
	else
		$pdf->MultiCell(52, 4,'', '', 'L','');	
	
	$y = $pdf->GetY();
	#$pdf->SetY($y+4);	
	$pdf->SetY($y);	
	
	$pdf->Cell(192,2,'',"T",1,'L');
		
	#$pdf->Ln($space);
	#echo "nd = ".$er_opd_datetime;
	$patient_OR = $enc_obj->getPatientOPDORNoforAnEncounter($pid, $er_opd_datetime);
	#echo "sql = ".$enc_obj->sql;
	#echo "count = ".$enc_obj->count;
	#print_r($patient_OR);
	#added by VAN 05-08-08 #Edited by Jarel 07/17/2013
	// if($official_receipt_nr && $official_receipt_nr != 'WCPU'){ -> commented to show WCPU

	// if($official_receipt_nr)
	// 	$ornum = $official_receipt_nr;
	// elseif ($enc_obj->count){
	// 	$ornum = trim($patient_OR['or_no']);
	// }else{
	// 	if ($senior_ID){
	// 		$ornum = "SENIOR CITIZEN";
	// 	}elseif($personnelID){
	// 		$ornum = "DMC PERSONNEL";
	// 	}elseif ($encounter_status=='phs')
	// 		$ornum = "PERSONNEL DEPENDENT";	
	// }
	#---------------------------

	# Added by JEFF 12-06-17 | fetching OR description instead of NUMBER
	if($official_receipt_nr){
		$enc_or_nr = $enc_obj->getOPDTempDesc($official_receipt_nr);
			if ($enc_or_nr) {
				$ornum = $enc_or_nr;
			}else{
				$ornum = $official_receipt_nr;
			}
	}
	elseif ($enc_obj->count){
		$ornum = trim($patient_OR['or_no']);
	}else{
		if ($senior_ID){
			$ornum = "SENIOR CITIZEN";
		}elseif($personnelID){
			$ornum = "DMC PERSONNEL";
		}elseif ($encounter_status=='phs')
			$ornum = "PERSONNEL DEPENDENT";	
	}
	
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(25,4,'OR Number : ',"",0,'L');
	$pdf->SetFont('Arial','B',$fontSizeLabel);
	$pdf->Cell(50,4,$ornum,"",0,'L');
	$pdf->Cell(25,4,'',"",0,'L');
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(25,4,'Amount Paid : ',"",0,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(10,4,'Php ',"",0,'L');
	$pdf->Cell(50,4,number_format($patient_OR['amount_paid'],2),"",1,'L');
     $pdf->Ln($space);     
    #added by VAN 10-12-2011
    $pdf->SetFont('Arial','I',$fontSizeLabel);
    $pdf->Cell(35,4,'History of Smoking : ',"",0,'L');
    $pdf->SetFont('Arial','B',$fontSizeLabel);
    
    if ($smoker_history=='yes'){
        $smoker_yes = '/';
        $smoker_no = '';
        $smoker_na = '';
    }elseif ($smoker_history=='no'){
        $smoker_yes = '';
        $smoker_no = '/';
        $smoker_na = '';
    }elseif ($smoker_history=='na'){
        $smoker_yes = '';
        $smoker_no = '';
        $smoker_na = '/';
    }else{
        $smoker_yes = '';
        $smoker_no = '';
        $smoker_na = '';
    }
            
    $pdf->Cell(5,4,$smoker_yes,1,"","C");
    $pdf->SetFont('Arial','',$fontSizeLabel);
    $pdf->Cell(10,4,"YES","","","L");
    $pdf->SetFont('Arial','B',$fontSizeLabel);
    $pdf->Cell(5,4,$smoker_no,1,"","C");
    $pdf->SetFont('Arial','',$fontSizeLabel);
    $pdf->Cell(10,4," NO","","","L");
    $pdf->SetFont('Arial','B',$fontSizeLabel);
    $pdf->Cell(5,4,$smoker_na,1,"","C");
    $pdf->SetFont('Arial','',$fontSizeLabel);
    $pdf->Cell(10,4,"N/A","",1,"L");
    $pdf->SetFont('Arial','I',$fontSizeLabel);
	
	$pdf->Ln($space);
	
    $pdf->Cell(35,4,'Alcohol Drinker : ',"",0,'L');
    $pdf->SetFont('Arial','B',$fontSizeLabel);
    
    if ($drinker_history=='yes'){
        $drinker_yes = '/';
        $drinker_no = '';
        $drinker_na = '';
    }elseif ($drinker_history=='no'){
        $drinker_yes = '';
        $drinker_no = '/';
        $drinker_na = '';
    }elseif ($drinker_history=='na'){
        $drinker_yes = '';
        $drinker_no = '';
        $drinker_na = '/';
    }else{
        $drinker_yes = '';
        $drinker_no = '';
        $drinker_na = '';
    }
            
    $pdf->Cell(5,4,$drinker_yes,1,"","C");
    $pdf->SetFont('Arial','',$fontSizeLabel);
    $pdf->Cell(10,4,"YES","","","L");
    $pdf->SetFont('Arial','B',$fontSizeLabel);
    $pdf->Cell(5,4,$drinker_no,1,"","C");
    $pdf->SetFont('Arial','',$fontSizeLabel);
    $pdf->Cell(10,4," NO","","","L");
    $pdf->SetFont('Arial','B',$fontSizeLabel);
    $pdf->Cell(5,4,$drinker_na,1,"","C");
    $pdf->SetFont('Arial','',$fontSizeLabel);
    $pdf->Cell(10,4,"N/A","",1,"L");
    #--------------
    
	$pdf->Ln($space*2);

	$pdf->SetFont('Arial','',8);
	$pdf->Cell(50,4,"Registered By : ".$registered_by,"",0,'L');
	/* commented out by art 01/16/2014
	$pdf->Ln(3);
	$pdf->Cell(50,4,"Encoded By : ".$encoded_by,"",0,'L');
	*/
	
	//added by art 01/18/2014
	$pdf->setY(-30);
	$pdf->SetFont('Arial','I',8);
	$pdf->Cell(50,4,"Encoded By : ".$encoded_by,"",0,'L');
	$pdf->setY(5);
	$pdf->SetFont('Arial','B',12);
	$pdf->setY(35);
	$pdf->Cell(0,4,"SPMC-F-MRO-01A", "",0, 'R');
	//end art

	#$pdf->Ln($space*60);
	#commented by VAN 09-19-08
	/*
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(25,5,'Principal Diagnosis:',"0",1,'L');
	
	
	$pdf->SetFont('Arial','B',$fontSizeLabel);
	$result_diagnosis = array();
	if (isset($is_discharged) && $is_discharged){
		if ($rs_diagnosis = $objDRG->getDiagnosisCodes($_GET['encounter_nr'])){
			$rowsDiagnosis = $rs_diagnosis->RecordCount();
			while($temp=$rs_diagnosis->FetchRow()){
				$temp_diagnosis = array();
				$temp_diagnosis['type'] = $temp['type'];
				$temp_diagnosis['code'] = $temp['code'];
				$temp_diagnosis['diagnosis'] = $temp['diagnosis'];
				array_push($result_diagnosis,$temp_diagnosis);
			}			
		}
	}
	
	if (isset($is_discharged) && ($rowsDiagnosis)){
		$count=0;
		foreach ($result_diagnosis as $value) {
			if ($value['type']==1){
				$pdf->Cell(10,5,"","0",0,'L');
				$pdf->Cell(71,5,$value['code']." : ".$value['diagnosis'],"0",1,'L');
				$count++;
			}
		}
		if (1-$count){
			$pdf->Ln($space*(1-$count));
		}
	}else{
		$pdf->Ln($space*26);
	}
	
	$pdf->Ln($space);
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(25,5,'Other Diagnosis:',"0",1,'L');
	
	
	$pdf->SetFont('Arial','B',$fontSizeLabel);
	
	if (isset($is_discharged) && ($rowsDiagnosis)){
		$count=0;
		foreach ($result_diagnosis as $value) {
			if ($value['type']==0){
				$pdf->Cell(10,5,"","0",0,'L');
				$pdf->Cell(71,5,$value['code']." : ".$value['diagnosis'],"0",1,'L');
				$count++;
			}
		}
		if (1-$count){
			$pdf->Ln($space*(1-$count));
		}
	}else{
		$pdf->Ln($space*26);
	}
	
	$pdf->Ln($space);
	
	#if ($is_discharged){
		$pdf->SetFont('Arial','I',$fontSizeLabel);
		$pdf->Cell(10,5,"","0",0,'L');
		$pdf->Cell(45,5,$attending_dr,"0",0,'C');
		$pdf->Ln(1);
		$pdf->Cell(13,3,"","0",0,'L');
		$pdf->Cell(90,3,"________________________","0",1,'L');
		
		$pdf->Cell(15,5,"","0",0,'L');
		$pdf->Cell(85,5,"ATTENDING PHYSICIAN","0",1,'L');

	#}
	*/
	$pdf->Output();	
?>