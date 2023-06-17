<?php
	#edited by VAN 05-01-08
	include("roots.php");
	include_once($root_path."/classes/fpdf/fpdf.php");
	#include_once($root_path."/classes/fpdf/pdf.class.php"); commented by art 01/18/2014
	include_once($root_path."/classes/fpdf/cert-pdf-nocode.php"); #added by art 01/18/2014
	
	#added by VAN 02-12-08
	#include_once($root_path."/classes/fpdf/pdf.class.php");
	
	require_once($root_path.'include/inc_environment_global.php');
	include_once($root_path.'include/inc_date_format_functions.php');
	include_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;
	
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	$objInfo = new Hospital_Admin();
	
	#echo "enc = ".$_GET['encounter_nr'];
	if ($_GET['encounter_nr']) {
		if (!($enc_info = $enc_obj->getEncounterInfo($_GET['encounter_nr']))){
			echo '<em class="warn">Sorry but the page cannot be displayed!</em>';
			exit();
		}
		#echo "enc_obj = '".$enc_obj->sql."' <br> \n";		
		extract($enc_info);
	}else{
			echo '<em class="warn">Sorry but the page cannot be displayed! <br>Invalid Case Number! </em>';
			exit();	
	}
	#echo "enc_obj = '".$enc_obj->sql."' <br> \n";	
	
	$border="1";
	$border2="0";
	$space=2;
	#$fontSizeLabel=9;
	$fontSizeLabel=10;
	$fontSizeInput=12;
	$fontSizeHeading=13;

	//Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, int fill [, mixed link]]]]]]])
	#edited by VAN 02-12-08
	#$pdf = new FPDF();
	#$pdf = new PDF(); commented by art 01/18/2014
	$pdf  = new PDF("P","mm","Letter"); #added by art 01/18/2014
	$pdf->AliasNbPages();   #--added
	$pdf->AddPage("P");
	
	$pdf->setY(5); #added by art 01/18/2014
	#added by VAN 06-13-08
	$pdf->SetDisplayMode(real,'default');
	$pdf->SetAutoPageBreak('true','10');
	/* commented by art 01/18/2014
	$pdf->SetFont('Arial','',$fontSizeLabel+3);
	$pdf->Cell(150,4,'HRN : ',"$border2",0,'R');
	$pdf->SetFont('Arial','B',$fontSizeHeading+8);
	$pdf->Cell(0,4	,$pid,"$border2",0,'R');
	$pdf->Ln(1);*/
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

	//added by art 01/18/2014
	$pdf->Image('image/logo_doh.jpg',25,7,20,20);
	$pdf->Image('image/dmc_logo.jpg',170,7,20,20);
	//end art

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
	$pdf->Cell(0,5,'Emergency Room Clinical Form',$border2,1,'C');
	//added by art 01/18/2014
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$category_name = $enc_obj->getTriageCategoryInfo($category);
	$pdf->Cell(30,4,'Triage Category : ', "",0,'L');
	$pdf->SetFont('Arial','B',$fontSizeHeading);
	$pdf->Cell(30,4,"".mb_strtoupper($category_name['roman_id']), "$border2",0,'L');
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(0,6,  "SPMC-F-MRI-10A", "",0, 'R');
	//end art


	#$pdf->Ln($space*2);
	$pdf->Ln($space*3.5);
	#added by VAN 06-13-08
	// $pdf->SetFont('Arial','I',$fontSizeLabel);
	// $category_name = $enc_obj->getTriageCategoryInfo($category);
	// $pdf->Cell(30,4,'Triage Category : ', "",0,'L');
	// $pdf->SetFont('Arial','B',$fontSizeHeading);
	// $pdf->Cell(30,4,"".mb_strtoupper($category_name['roman_id']), "$border2",0,'L');
	// $pdf->SetFont('Arial','',$fontSizeLabel+3);
	// $pdf->Cell(50,4,'Department : ',"$border2",0,'R');
	// $pdf->SetFont('Arial','B',$fontSizeHeading+5);
	// $pdf->Cell(60,4	,mb_strtoupper($name_formal),"$border2",0,'L');
	// #--------------------
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(30,4,'Case No. : ',"$border2",0,'L');
	$pdf->SetFont('Arial','B',$fontSizeHeading+5);
	$pdf->Cell(50,4,$encounter_nr,"$border2",0,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(80,4,'HRN : ',"$border2",0,'R');
	$pdf->SetFont('Arial','B',$fontSizeHeading+8);
	$pdf->Cell(0,4	,$pid,"$border2",0,'R');
	#--------------------

	$pdf->Ln($space*3);
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(30,4,'Date/Time : ',"$border2",0,'L');
	$pdf->SetFont('Arial','B',$fontSizeHeading);
	$pdf->Cell(50,4	,@formatDate2Local($er_opd_datetime,$date_format,1),"$border2",0,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(50,4,'Department : ',"$border2",0,'R');

	if(strlen($name_formal) > 21)
		$pdf->SetFont('Arial','B',$fontSizeHeading-1);
	else
		$pdf->SetFont('Arial','B',$fontSizeHeading+3);
	$pdf->Cell(60,4	,mb_strtoupper($name_formal),"$border2",0,'L');
	
	// //added by art 01/18/2014
	// $pdf->SetFont('Arial','B',$fontSizeHeading);
	// $pdf->Cell(0,4	,'HRN : '.$pid,"$border2",0,'R');
	// //end

	$pdf->Ln($space*3);
	/*
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(52,4,'Admitted Date and Time at ER : ',"",0,'L');
	$pdf->SetFont('Arial','B',$fontSizeLabel);
	$pdf->Cell(48,4,@formatDate2Local($er_opd_datetime,$date_format,1),"",1,'L');
	*/
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(65,4,'Last Name',"TR",0,'L');
	$pdf->Cell(65,4,'First Name',"TLR",0,'L');
	$pdf->Cell(65,4,'Middle Name',"TLR",0,'L');
	//$pdf->Cell(48,4,'Maiden Name',"TL",0,'L');
	
	$pdf->Ln();	
	
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	
	$pdf->SetFont('Arial','B',$fontSizeLabel+5);
	$pdf->Cell(65,8,'',"RB",0,'L');
	$pdf->Cell(65,8,'',"LRB",0,'L');
	$pdf->Cell(65,8,'',"LRB",0,'L');
	// $pdf->Cell(48,8,'',"LB",0,'L');
	
	$pdf->SetXY($x, $y);
	$pdf->MultiCell(47, 4, mb_strtoupper($name_last), '', 'L','');
	
	$pdf->SetXY($x+65, $y);
	$pdf->MultiCell(47, 4, mb_strtoupper($name_first), '', 'L','');
	
	$pdf->SetXY($x+130, $y);
	$pdf->MultiCell(47, 4, mb_strtoupper($name_middle), '', 'L','');
	
	// $pdf->SetXY($x+144, $y);
	// $pdf->MultiCell(47, 4, mb_strtoupper($name_maiden), '', 'L','');
	
	$pdf->SetY($y+7);
	#$pdf->Ln($space*2);
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(10,8,'Age : ',"",0,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel+2);
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
	$pdf->SetFont('Arial','',$fontSizeLabel+2);
	
	if ($sex=='f')
		$gender = 'FEMALE';
	elseif ($sex=='m')
		$gender = 'MALE';	
	
	$pdf->Cell(38, 8, mb_strtoupper($gender), '', 0,'L');
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(20,8,'Civil Status : ',"",0,'L');
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
	
	$pdf->SetY($y+12);
	
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
	
	#$address = "$street_name $brgy_name, $mun_name $zipcode $prov_name";
	if (!($brgy_name))
		$brgy_name = "NOT PROVIDED";
	
	#$address = trim($street_name)." ".trim($brgy_name).", ".trim($mun_name)." ".trim($zipcode)." ".trim($prov_name);
	if(stristr(trim($mun_name), 'city') === FALSE){
		if (!empty($mun_name)){
			$province = ", ".trim($prov_name);
		}else{
			$province = trim($prov_name);;
		}
	}	
	
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
	
	if(stristr(trim($mun_name), 'city') == true){
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
	$pdf->SetY($y+13);
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(55,8,'Birth Place : ',"",0,'L');
	$pdf->Cell(55,8,'Birth Date : ',"",0,'L');
	#$pdf->Cell(55,8,'Department : ',"",0,'L');
	
	$pdf->Ln($space*3);
	
	$pdf->SetFont('Arial','',$fontSizeLabel);
	#$pdf->Cell(55, 4, mb_strtoupper($place_birth), '', 0,'L');
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	
	$pdf->SetXY($x, $y);
	
	$pdf->MultiCell(55, 4,mb_strtoupper($place_birth), '', 'L','');
	
	$pdf->SetXY($x+55, $y);
	if ($date_birth)
		#$pdf->Cell(55,4,@formatDate2Local($date_birth,$date_format),"",0,'L');
		$pdf->MultiCell(50, 4,@formatDate2Local($date_birth,$date_format), '', 'L','');
	else
		#$pdf->Cell(55,4,'',"",0,'L');
		$pdf->MultiCell(50, 4,'', '', 'L','');
		
	#$pdf->SetXY($x+110, $y);	
	#$pdf->Cell(55, 4, mb_strtoupper($name_formal), '', 0,'L');
	#$pdf->MultiCell(80, 4,mb_strtoupper($name_formal), '', 'L','');
	
	$pdf->Ln($space*2);
	
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
	$pdf->MultiCell(70, 4,mb_strtoupper($father_name), '', 'L','');
	
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
	
	$pdf->SetY($y+7);
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(70,8,'Name of Spouse : ',"",0,'L');
	//$pdf->Cell(70,8,'Employer : ',"",0,'L');
	//$pdf->Cell(52,8,'MSS CLASS : ',"",0,'L');
	
	$pdf->Ln($space);
	
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	
	$pdf->SetXY($x, $y+4);
	
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->MultiCell(68, 4,mb_strtoupper($spouse_name), '', 'L','');
	
	$pdf->SetXY($x+70, $y+4);	
	#$pdf->Cell(55, 4, mb_strtoupper($name_formal), '', 0,'L');
	//$pdf->MultiCell(68, 4,mb_strtoupper($employer), '', 'L','');
	
	$pdf->SetXY($x+140, $y+4);	
	#$pdf->Cell(55, 4, mb_strtoupper($name_formal), '', 0,'L');
	#$pdf->MultiCell(52, 4,$mss_id ." (".mb_strtoupper($mss_class).")", '', 'L','');
	$pdf->SetFont('Arial','',$fontSizeLabel+2);
	$pdf->MultiCell(55, 4,'     '.$mss_id, '', 'L','');
	
	$y = $pdf->GetY();
	$pdf->SetY($y+2);	
	
	$pdf->Cell(190,1,'',"T",0,'L');
		
	$pdf->Ln($space*0.5);
	
	#$y = $pdf->GetY();
	#$pdf->SetXY($y);
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(64,4,'Registration Clerk',"",0,'L');
	
	$pdf->Cell(63,4,'Name of Informant',"",0,'L');
	$pdf->Cell(63,4,'Relation to Patient',"",1,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(64,4,mb_strtoupper($admitting_clerk_er_opd),"",0,'L');
	$pdf->Cell(63,4,mb_strtoupper($informant_name),"",0,'L');
	$pdf->Cell(62,4,mb_strtoupper($relation_informant),"",1,'L'); //added by KENTOOT 09/15/2014
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	
	$pdf->Cell(45,6,'Date and Time Examined : ', "",0,'L');
	$pdf->SetFont('Arial','',$fontSizeInput);
	$pdf->Cell(50,6,'',"",0,'L');
	/*
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(52,6,'Admitted Date and Time at ER : ',"",0,'L');
	$pdf->SetFont('Arial','B',$fontSizeLabel);
	$pdf->Cell(43,6,@formatDate2Local($er_opd_datetime,$date_format,1),"R",1,'L');
	*/
	$pdf->Cell(95,6,"","",1,'L');
	
	#$pdf->Cell(135,20,'  S',1,0,'L');
	$pdf->Cell(0,22,'  S',"",1,'L');
	#added by VAN 06-13-08
	/*
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->SetXY($x,$y);
	$pdf->Cell(40,4,'Triage Category', "",1,'L');
	
	$list_category = $enc_obj->getTriageCategory();
	if(is_object($list_category)){
		#$categorylist = '';
		#if (empty($category))
		#	$category = 4;
		$i = 4;
		while($result=$list_category->FetchRow()) {
			if($category==$result['category_id']){
				$pdf->SetFont('Arial','B',$fontSizeLabel-1);
				$pdf->SetXY($x+1,$y+$i);
				$pdf->Cell(53.5,4,$result['roman_id']." - ".$result['category'], "1",1,'L');	
			}else{
				$pdf->SetFont('Arial','',$fontSizeLabel-1);
				$pdf->SetXY($x+1,$y+$i);
				$pdf->Cell(53.5,4,$result['roman_id']." - ".$result['category'], "",1,'L');	
			}
			$i += 4;		
		}
	}		
	
	$pdf->SetXY($x,$y);
	$pdf->Cell(55,20,'', "TR",1,'L');
	*/
	#----------------------
	#$pdf->SetFont('Arial','B',$fontSizeLabel);
	$pdf->Cell(0,22,'  O',"",1,'L');
	$pdf->Cell(0,22,'  A',"",1,'L');
	$pdf->Cell(0,22,'  P',"",1,'L');
	
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(150,4,'Diagnosis',"",0,'L');
	$pdf->Cell(40,4,'ICD Code',"",1,'L');
	$pdf->Cell(150,10,'',"",0,'L');
	$pdf->Cell(40,10,'',"",1,'L');
	$pdf->Ln(0.5);
	$pdf->Cell(50,4,'Condition at ER',"",0,'L');
	$pdf->Cell(50,4,'Disposition',"",0,'L');
	#$pdf->Cell(50,4,'Disposition',"TLR",1,'L');
	$pdf->Cell(0,4,'',"",1,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(7,4,'[   ]',"",0,'R');
	$pdf->Cell(43,4,'Conscious',"",0,'L');
	$pdf->Cell(7,4,'[   ]',"",0,'R');
	$pdf->Cell(43,4,'Admitted',"",0,'L');
	$pdf->Cell(0,4,'',"",1,'L');
	$pdf->Cell(7,4,'[   ]',"",0,'R');
	$pdf->Cell(43,4,'Semi-conscious',"",0,'L');
	$pdf->Cell(7,4,'[   ]',"",0,'R');
	$pdf->Cell(43,4,'Discharged',"",0,'L');
	$pdf->Cell(0,4,'',"",1,'L');
	$pdf->Cell(7,4,'',"",0,'R');
	$pdf->Cell(43,4,'',"",0,'L');
	$pdf->Cell(7,4,'[   ]',"",0,'R');
	$pdf->Cell(43,4,'Transferred',"",0,'L');
	$pdf->Cell(0,4,'',"",1,'L');
	$pdf->SetFont('Arial','I',$fontSizeLabel);
	$pdf->Cell(50,4,'Results',"",0,'L');
	$pdf->SetFont('Arial','',$fontSizeLabel);
	$pdf->Cell(7,4,'[   ]',"",0,'R');
	$pdf->Cell(43,4,'HAMA',"",0,'L');
	$pdf->Cell(0,4,'',"",1,'L');
	$pdf->Cell(7,4,'[   ]',"",0,'R');
	$pdf->Cell(43,4,'Recovered',"",0,'L');
	$pdf->Cell(7,4,'[   ]',"",0,'R');
	
	$pdf->Cell(43,4,'Absconded/PNF',"",0,'L');
	$pdf->Cell(0,4,'',"",1,'L');
	$pdf->Cell(7,4,'[   ]',"",0,'R');
	$pdf->Cell(43,4,'Improved',"",0,'L');
	$pdf->Cell(7,4,'[   ]',"",0,'R');
	$pdf->Cell(43,4,'For OPD',"",0,'L');
	$pdf->Cell(6,4,'',"",0,'');
	$pdf->Cell(78,4,mb_strtoupper($er_opd_admitting_physician_name),"",0,'C');
	$pdf->Cell(6,4,'',"",1,'');
	$pdf->Cell(7,4,'[   ]',"",0,'R');
	$pdf->Cell(43,4,'Unimproved',"",0,'L');
	$pdf->Cell(7,4,'',"",0,'R');
	$pdf->Cell(43,4,'',"",0,'L');
	$pdf->Cell(0,4,'Name & Signature of Attending Physician',"",1,'C');
	$pdf->Cell(7,4,'[   ]',"",0,'R');
	$pdf->Cell(43,4,'Died',"",0,'L');
	$pdf->Cell(7,4,'',"",0,'R');
	$pdf->Cell(43,4,'',"",0,'L');
	$pdf->Cell(0,4,'',"",1,'L');
	#$pdf->Cell(7,4,'',"LB",0,'R');
	#$pdf->Cell(43,4,'',"RB",0,'L');
	#$pdf->Cell(7,4,'',"LB",0,'R');
	#$pdf->Cell(43,4,'',"RB",0,'L');
	#$pdf->Cell(0,4,'',"RB",1,'L');
	
	$pdf->Output();	
?>