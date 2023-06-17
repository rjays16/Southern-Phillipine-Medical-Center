<?php
require('./roots.php');
require($root_path."/classes/fpdf/pdf.class.php");
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_personell.php');

class MedCertDriver_IC extends FPDF{
	var $encounter_nr;

	function MedCertDriver_IC($encounter_nr){
		global $db;
		$this->ColumnWidth = array(22,56,32,15,12,80,33,25);
		$this->SetTopMargin(3);
		$this->Alignment = array('C','L','C','C','C','L','C','C');
		$this->FPDF("P", 'mm', 'Legal');
		$this->encounter_nr = $encounter_nr;
		//if ($from) $this->from=date("Y-m-d",strtotime($from));
		//if ($to) $this->to=date("Y-m-d",strtotime($to));
	}

	function Header() {
		global $root_path, $db;
		$objInfo = new Hospital_Admin();
		$rowheight = 5;

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

		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',35,8,15); //for ic logo
		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',165,8,15); //spmc logo
		$this->SetFont('Arial', '', 11);
		$this->Cell(0, $rowheight, $row['hosp_country'],0,1,'C');
		$this->SetFont('Arial', '', 12);
		$this->Cell(0, $rowheight, strtoupper($row['hosp_agency']), 0,1,'C');
		$this->SetFont('Arial', '', 11);
		$this->Cell(0, $rowheight, "Center for Health Development - Davao Region", 0,1,'C');
		$this->Cell(0, $rowheight, ucwords($row['hosp_name'])." - Industrial Clinic", 0,1 ,'C');
		$this->Cell(0, $rowheight, "Davao City Tel No. 227-2731",0,1,'C');
		$this->Ln($rowheight);
			$x = $this->GetX();
			$y = $this->GetY();
		$this->Line($x, $y-1, $x+190, $y-1);

		$this->SetFont('Arial', 'B', 16);
		$this->Cell(0, $rowheight, "MEDICAL CERTIFICATE", 0,1,'C');
		$this->Ln();
	}

	function GetData(){
		global $root_path, $db;
		$rowheight = 5;
		$pers_obj=new Personell;

		//put query here
		$sql = "SELECT c.*, p.name_last, p.name_first, p.name_middle,
		fn_calculate_age(p.date_birth, NOW()) AS patient_age, p.civil_status, UPPER(p.sex) AS patient_sex,
		p.street_name,  sb.brgy_name, sm.mun_name, sm.zipcode, sp.prov_name, sc.citizenship, p.civil_status
		FROM seg_industrial_cert_med_driver AS c
		INNER JOIN seg_industrial_transaction AS t ON t.refno = c.refno
		INNER JOIN care_person AS p ON p.pid = t.pid
		LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
		LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr
		LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
		LEFT JOIN seg_country AS sc ON sc.country_code=p.citizenship
		WHERE t.encounter_nr = '".$this->encounter_nr."';";
		$result = $db->Execute($sql);
		$row = $result->FetchRow();

		$h_unit = $row['height_unit'];
		$w_unit = $row['weight_unit'];

		$sql2 = "SELECT sev.unit_name FROM seg_encounter_vitalsigns_unit AS sev
		LEFT JOIN seg_industrial_cert_med_driver AS c ON c.height_unit = sev.unit_id
		WHERE sev.unit_id = '".$h_unit."';";
		$result2 = $db->Execute($sql2);
		$row2 = $result2->FetchRow();

		$sql3="SELECT sev.unit_name FROM seg_encounter_vitalsigns_unit AS sev
		LEFT JOIN seg_industrial_cert_med_driver AS c ON c.weight_unit = sev.unit_id
		WHERE sev.unit_id = '".$w_unit."';";
		$result3 = $db->Execute($sql3);
		$row3 = $result3->FetchRow();

		if (trim($row['street_name'])){
					if (trim($row["brgy_name"])!="NOT PROVIDED")
						$street_name = trim($row['street_name']).", ";
					else
						$street_name = trim($row['street_name']).", ";
			}else{
					$street_name = "";
			}

			if ((!(trim($row["brgy_name"]))) || (trim($row["brgy_name"])=="NOT PROVIDED"))
				$brgy_name = "";
			else
				$brgy_name  = trim($row["brgy_name"]).", ";

			if ((!(trim($row["mun_name"]))) || (trim($row["mun_name"])=="NOT PROVIDED"))
				$mun_name = "";
			else{
				if ($brgy_name)
					$mun_name = trim($row["mun_name"]);
				else
					$mun_name = trim($row["mun_name"]);
			}

			if ((!(trim($row["prov_name"]))) || (trim($row["prov_name"])=="NOT PROVIDED"))
				$prov_name = "";
			else
				$prov_name = trim($row["prov_name"]);

			if(stristr(trim($row["mun_name"]), 'city') === FALSE){
				if ((!empty($row["mun_name"]))&&(!empty($row["prov_name"]))){
					if ($prov_name!="NOT PROVIDED")
						$prov_name = ", ".trim($prov_name);
					else
						$prov_name = trim($prov_name);
				}else{
					#$province = trim($prov_name);
					$prov_name = "";
				}
			}else
				$prov_name = "";

			$address = trim($street_name).trim($brgy_name).trim($mun_name).trim($prov_name);


		//temporary variables
		$firstname = strtoupper($row['name_first']);
		$middlename = strtoupper($row['name_middle']);
		$surname = strtoupper($row['name_last']);
		//$address = "Brgy. 6A Kamya Alley, San Pedro Extension, Davao City";
		$age = floor($row['patient_age']);
		$nationality = $row['citizenship'];

			if($row['patient_sex']=='M'){
				$male = "X";
				$female = "";
			}
			else{
				$male = "";
				$female = "X";
			}

			if($row['civil_status']=="single"){
				$single = "X";
				$married = "";
				$separated = "";
				$widow = "";
				$annulled = "";
			}else if($row['civil_status']=='married'){
				$single = "";
				$married = "X";
				$separated = "";
				$widow = "";
				$annulled = "";
			}else if($row['civil_status']=='separated'){
				$single = "";
				$married = "";
				$separated = "X";
				$widow = "";
				$annulled = "";
			}else if($row['civil_status']=='widow' || $row['civil_status']=='widower'){
				$single = "";
				$married = "";
				$separated = "";
				$widow = "X";
				$annulled = "";
			}else if($row['civil_status']=='annulled'){
				$single = "";
				$married = "";
				$separated = "";
				$widow = "";
				$annulled = "X";
			}

		//$single = "X";
		//$married = "";
		//$separated = "";
		//$widow = "";
		//$annulled = "";

		if($row['employ_nr']=="1"){
			$private = "X";
			$government = "";
		}else if($row['employ_nr']=="2"){
			$private = "";
			$government = "X";
		}

		switch($row['educ_attain_nr']){
			case 1:
					$no_formal_schooling = "X";
					$elementary_graduate = "";
					$elementary_undergraduate = "";
					$highschool_graduate = "";
					$highschool_undergraduate = "";
					$college_graduate = "";
					$college_undergraduate = "";
					$vocational = "";
					$post_graduate = "";
					break;
			case 2:
					$no_formal_schooling = "";
					$elementary_graduate = "X";
					$elementary_undergraduate = "";
					$highschool_graduate = "";
					$highschool_undergraduate = "";
					$college_graduate = "";
					$college_undergraduate = "";
					$vocational = "";
					$post_graduate = "";
					break;
			case 3:
					$no_formal_schooling = "";
					$elementary_graduate = "";
					$elementary_undergraduate = "X";
					$highschool_graduate = "";
					$highschool_undergraduate = "";
					$college_graduate = "";
					$college_undergraduate = "";
					$vocational = "";
					$post_graduate = "";
					break;
			case 4:
					$no_formal_schooling = "";
					$elementary_graduate = "";
					$elementary_undergraduate = "";
					$highschool_graduate = "X";
					$highschool_undergraduate = "";
					$college_graduate = "";
					$college_undergraduate = "";
					$vocational = "";
					$post_graduate = "";
					break;
			case 5:
					$no_formal_schooling = "";
					$elementary_graduate = "";
					$elementary_undergraduate = "";
					$highschool_graduate = "";
					$highschool_undergraduate = "X";
					$college_graduate = "";
					$college_undergraduate = "";
					$vocational = "";
					$post_graduate = "";
					break;
			case 6:
					$no_formal_schooling = "";
					$elementary_graduate = "";
					$elementary_undergraduate = "";
					$highschool_graduate = "";
					$highschool_undergraduate = "";
					$college_graduate = "";
					$college_undergraduate = "";
					$vocational = "X";
					$post_graduate = "";
					break;
			case 7:
					$no_formal_schooling = "";
					$elementary_graduate = "";
					$elementary_undergraduate = "";
					$highschool_graduate = "";
					$highschool_undergraduate = "";
					$college_graduate = "X";
					$college_undergraduate = "";
					$vocational = "";
					$post_graduate = "";
					break;
			case 8:
					$no_formal_schooling = "";
					$elementary_graduate = "";
					$elementary_undergraduate = "";
					$highschool_graduate = "";
					$highschool_undergraduate = "";
					$college_graduate = "";
					$college_undergraduate = "X";
					$vocational = "";
					$post_graduate = "";
					break;
			case 9:
					$no_formal_schooling = "";
					$elementary_graduate = "";
					$elementary_undergraduate = "";
					$highschool_graduate = "";
					$highschool_undergraduate = "";
					$college_graduate = "";
					$college_undergraduate = "";
					$vocational = "";
					$post_graduate = "X";
					break;
			case 10:
					$no_formal_schooling = "";
					$elementary_graduate = "";
					$elementary_undergraduate = "";
					$highschool_graduate = "";
					$highschool_undergraduate = "";
					$college_graduate = "";
					$college_undergraduate = "";
					$vocational = "";
					$post_graduate = "X";
					break;
			case 11:
					$no_formal_schooling = "";
					$elementary_graduate = "";
					$elementary_undergraduate = "";
					$highschool_graduate = "";
					$highschool_undergraduate = "";
					$college_graduate = "";
					$college_undergraduate = "";
					$vocational = "";
					$post_graduate = "X";
					break;
			case 12:
					$no_formal_schooling = "";
					$elementary_graduate = "";
					$elementary_undergraduate = "";
					$highschool_graduate = "";
					$highschool_undergraduate = "";
					$college_graduate = "";
					$college_undergraduate = "";
					$vocational = "";
					$post_graduate = "X";
					break;
		}

		switch($row['work_status_nr']){
			case 1:
					$unemployed = "X";
					$self_employed = "";
					$employed = "";
					$student = "";
					$retired = "";
					$overseas = "";
					break;
			case 2:
					$unemployed = "";
					$self_employed = "X";
					$employed = "";
					$student = "";
					$retired = "";
					$overseas = "";
					break;
			case 3:
					$unemployed = "";
					$self_employed = "";
					$employed = "X";
					$student = "";
					$retired = "";
					$overseas = "";
					break;
			case 4:
					$unemployed = "";
					$self_employed = "";
					$employed = "";
					$student = "X";
					$retired = "";
					$overseas = "";
					break;
			case 5:
					$unemployed = "";
					$self_employed = "";
					$employed = "";
					$student = "";
					$retired = "X";
					$overseas = "";
			case 6:
					$unemployed = "";
					$self_employed = "";
					$employed = "";
					$student = "";
					$retired = "";
					$overseas = "X";
					break;
		}

		if($row['vehicle_nr']=="1"){
			$private_vehicle = "X";
			$for_hire_vehicle = "";
		}else if($row['vehicle_nr']=="2"){
			$private_vehicle = "";
			$for_hire_vehicle = "X";
		}
		//end variables

		$this->SetFont('Arial', '', 12);
		$this->Cell(18, $rowheight, "NAME:",0,0,'L');
		$this->SetFont('Arial', 'B',16);
		$this->Cell(40, $rowheight, strtoupper($surname),0,0,'C'); //put surname here
		$this->Cell(70, $rowheight, strtoupper($firstname),0,0,'C'); //put firstname here
		$this->Cell(40, $rowheight, strtoupper($middlename),0,1,'C');

		$this->SetFont('Arial', '', 12);
		$this->Cell(18, $rowheight);
		$this->Cell(40, $rowheight, "SURNAME", 0,0,'C');
		$this->Cell(70, $rowheight, "FIRST NAME", 0,0,'C');
		$this->Cell(40, $rowheight, "MIDDLE NAME",0,1,'C');

		$this->Cell(23, $rowheight, "ADDRESS:",0,0,'L');
		$this->SetFont('Arial', 'U', 12);
		$this->Cell(167, $rowheight, $address, 0,1,'L'); //put address here

		$this->SetFont('Arial', '', 12);
		$this->Cell(12, $rowheight, "AGE:",0,0,'L');
		$this->SetFont('Arial', 'U', 12);
		$this->Cell(45, $rowheight, $age, 0,0,'L'); //put age here
		$this->SetFont('Arial', '', 12);
		$this->Cell(30, $rowheight, "NATIONALITY:",0,0,'L');
		$this->SetFont('Arial', 'U', 12);
		$this->Cell(40, $rowheight, strtoupper($nationality),0,1,'L'); //put nationality here

		$this->SetFont('Arial', '', 12);
		$this->Cell(50, $rowheight, "GENDER:", 0,0,'L');
		$this->Cell(80, $rowheight, "EDUCATIONAL ATTAINMENT:",0,0,'L');
		$this->Cell(50, $rowheight, "WORK STATUS:",0,1,'L');

		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $male, 1,0,'C'); //gender - male
		$this->Cell(35, $rowheight, "Male",0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $no_formal_schooling, 1,0,'C'); //educational attainment - no formal schooling
		$this->Cell(65, $rowheight, "No Formal Schooling",0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $unemployed, 1,0,'C'); //work status - unemployed
		$this->Cell(35, $rowheight, "Unemployed",0,1,'L');

		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $female,1,0,'C'); //gender - female
		$this->Cell(35, $rowheight, "Female", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $elementary_graduate, 1,0,'C'); //educational attainment - elementary graduate
		$this->Cell(65, $rowheight, "Elementary Graduate", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $self_employed, 1,0,'C'); //work status - self employed
		$this->Cell(35, $rowheight, "Self Employed",0,1,'L');

		$this->Cell(50, $rowheight, "MARITAL STATUS:", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $elementary_undergraduate, 1,0,'C'); //educational attainment - elementary undergraduate
		$this->Cell(65, $rowheight, "Elementary Undergraduate",0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $employed, 1,0,'C'); //work status - employed
		$this->Cell(35, $rowheight, "Employed",0,1,'L');

		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $single,1,0,'C'); //marital status - single
		$this->Cell(35, $rowheight, "Single", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $highschool_graduate, 1,0,'C'); //educational attainment - high school graduate
		$this->Cell(65, $rowheight, "High School Graduate", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $student, 1,0,'C'); //work status - student
		$this->Cell(35, $rowheight, "Student",0,1,'L');

		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $married,1,0,'C'); //marital status - married
		$this->Cell(35, $rowheight, "Married", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $highschool_undergraduate, 1,0,'C'); //educational attainment - high school undergraduate
		$this->Cell(65, $rowheight, "High School Undergraduate", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $retired, 1,0,'C'); //work status - retired
		$this->Cell(35, $rowheight, "Retired",0,1,'L');

		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $separated,1,0,'C'); //marital status - separated
		$this->Cell(35, $rowheight, "Separated", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $college_graduate, 1,0,'C'); //educational attainment - college graduate
		$this->Cell(65, $rowheight, "College Graduate", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $overseas, 1,0,'C'); //work status - overseas
		$this->Cell(35, $rowheight, "Overseas",0,1,'L');

		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $widow,1,0,'C'); //marital status - widow/widower
		$this->Cell(35, $rowheight, "Widow / Widower", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $college_undergraduate, 1,0,'C'); //educational attainment - college undergraduate
		$this->Cell(65, $rowheight, "College Undergraduate", 0,0,'L');
		$this->Cell(50, $rowheight, "MOTOR VEHICLE DRIVEN:", 0,1,'L');

		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $annulled,1,0,'C'); //marital status - annulled
		$this->Cell(35, $rowheight, "Annulled", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $vocational, 1,0,'C'); //educational attainment - vocational
		$this->Cell(65, $rowheight, "Vocational", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $private_vehicle, 1,0,'C'); //vehicle - private
		$this->Cell(35, $rowheight, "Private",0,1,'L');

		$this->Cell(50, $rowheight, "KIND OF EMPLOYMENT:", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $post_graduate,1,0,'C'); //educational attainment - post graduate
		$this->Cell(65, $rowheight, "Post Graduate", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $for_hire_vehicle, 1,0,'C'); //vehicle - for hire
		$this->Cell(35, $rowheight, "For Hire", 0,1,'L');

		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $private, 1, 0, 'C'); //employment - private
		$this->Cell(35, $rowheight, "Private", 0,1,'L');

		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $government, 1, 0, 'C'); //employment - government
		$this->Cell(35, $rowheight, "Government", 0,1,'L');

		$this->Ln();

		$this->SetFont('Arial', '', 12);
		$this->Cell(0, $rowheight, "PHYSICAL EXAMINATION", 0,1,'C');

		//-------------physical examination temporary data-------
		$height = $row['height'];
		$height_unit = $row2['unit_name'].".";
		$weight = $row['weight'];
		$weight_unit = $row3['unit_name'].".";
		$blood_pressure = $row['systole']."/".$row['diastole'];

		if($row['general_physique']=="Normal"){
			$normal_physique = "X";
			$abnormal_physique = "";
		}else if($row['general_physique']=="Abnormal"){
			$normal_physique = "";
			$abnormal_physique = "X";
		}

		if($row['abnormality']){
			$abnormality_desc = $row['abnormality'];
		}else if(!$row['abnormality']){
			$abnormality_desc = "";
		}

		if($row['with_disease']=="0"){
			$no_disease = "X";
			$with_disease = "";
		}else if($row['with_disease']=="1"){
			$no_disease = "";
			$with_disease = "X";
		}

		if($row['contagious_diseases']){
			$disease = $row['contagious_diseases'];
		}else if(!$row['contagious_diseases']){
			$disease = "";
		}

		//$employment_driver  = "X";
		//$employment_government = "";
		$left_eye_snellen = $row['left_eye_snellen'];
		$right_eye_snellen = $row['right_eye_snellen'];
		$left_visual_acuity = $row['left_eye_acuity'];
		$right_visual_acuity = $row['right_eye_acuity'];

		if($row['left_eye_other']=="1"){
			$left_corrective_lenses = "X";
			$left_color_blind = "";
		}else if($row['left_eye_other']=="2"){
			$left_corrective_lenses = "";
			$left_color_blind = "X";
		}

		if($row['right_eye_other']=="1"){
			$right_corrective_lenses = "X";
			$right_color_blind = "";
		}else if($row['right_eye_other']=="2"){
			$right_corrective_lenses = "";
			$right_color_blind = "X";
		}

		switch($row['left_ear']){
			case 1:
					$left_ear_normal = "X";
					$left_ear_reduced = "";
					$left_ear_zero = "";
					$left_ear_hearing_aid = "";
					break;
			case 2:
					$left_ear_normal = "";
					$left_ear_reduced = "X";
					$left_ear_zero = "";
					$left_ear_hearing_aid = "";
					break;
			case 3:
					$left_ear_normal = "";
					$left_ear_reduced = "";
					$left_ear_zero = "X";
					$left_ear_hearing_aid = "";
					break;
			case 4:
					$left_ear_normal = "";
					$left_ear_reduced = "";
					$left_ear_zero = "";
					$left_ear_hearing_aid = "X";
					break;
		}

		switch($row['right_ear']){
			case 1:
					$right_ear_normal = "X";
					$right_ear_reduced ="";
					$right_ear_zero = "";
					$right_ear_hearing_aid = "";
					break;
			case 2:
					$right_ear_normal = "";
					$right_ear_reduced ="X";
					$right_ear_zero = "";
					$right_ear_hearing_aid = "";
					break;
			case 3:
					$right_ear_normal = "";
					$right_ear_reduced ="";
					$right_ear_zero = "X";
					$right_ear_hearing_aid = "";
					break;
			case 4:
					$right_ear_normal = "";
					$right_ear_reduced ="";
					$right_ear_zero = "";
					$right_ear_hearing_aid = "X";
					break;
		}

		switch($row['left_upper_extremities']){
			case 1:
					$left_upper_extremities_normal = "X";
					$left_upper_extremities_defective = "";
					$left_upper_extremities_equipment = "";
					break;
			case 2:
					$left_upper_extremities_normal = "";
					$left_upper_extremities_defective = "X";
					$left_upper_extremities_equipment = "";
					break;
			case 3:
					$left_upper_extremities_normal = "";
					$left_upper_extremities_defective = "";
					$left_upper_extremities_equipment = "X";
					break;
		}

		switch($row['right_upper_extremities']){
			case 1:
					$right_upper_extremities_normal = "X";
					$right_upper_extremities_defective = "";
					$right_upper_extremities_equipment = "";
					break;
			case 2:
					$right_upper_extremities_normal = "";
					$right_upper_extremities_defective = "X";
					$right_upper_extremities_equipment = "";
					break;
			case 3:
					$right_upper_extremities_normal = "";
					$right_upper_extremities_defective = "";
					$right_upper_extremities_equipment = "X";
					break;
		}

		switch($row['left_lower_extremities']){
			case 1:
					$left_lower_extremities_normal = "X";
					$left_lower_extremities_defective = "";
					$left_lower_extremities_equipment = "";
					break;
			case 2:
					$left_lower_extremities_normal = "";
					$left_lower_extremities_defective = "X";
					$left_lower_extremities_equipment = "";
					break;
			case 3:
					$left_lower_extremities_normal = "";
					$left_lower_extremities_defective = "";
					$left_lower_extremities_equipment = "X";
					break;
		}

		switch($row['right_lower_extremities']){
			case 1:
					$right_lower_extremities_normal = "X";
					$right_lower_extremities_defective = "";
					$right_lower_extremities_equipment = "";
					break;
			case 2:
					$right_lower_extremities_normal = "";
					$right_lower_extremities_defective = "X";
					$right_lower_extremities_equipment = "";
					break;
			case 3:
					$right_lower_extremities_normal = "";
					$right_lower_extremities_defective = "";
					$right_lower_extremities_equipment = "X";
					break;
		}

		switch($row['comment_drive']){
			case 1:
					$fit_to_drive = "X";
					$without_condition = "";
					$with_condition = "";
					break;
			case 2:
					$fit_to_drive = "";
					$without_condition = "X";
					$with_condition = "";
					break;
			case 3:
					$fit_to_drive = "";
					$without_condition = "";
					$with_condition = "X";
					break;
		}

		if($row['conditions']){

			switch($row['conditions']){
				case 1:
					$with_condition_a = "X";
					$with_condition_b = "";
					$with_condition_c = "";
					$with_condition_d = "";
					$with_condition_e = "";
					break;
				case 2:
					$with_condition_a = "";
					$with_condition_b = "X";
					$with_condition_c = "";
					$with_condition_d = "";
					$with_condition_e = "";
					break;
				case 3:
					$with_condition_a = "";
					$with_condition_b = "";
					$with_condition_c = "X";
					$with_condition_d = "";
					$with_condition_e = "";
					break;
				case 4:
					$with_condition_a = "";
					$with_condition_b = "";
					$with_condition_c = "";
					$with_condition_d = "X";
					$with_condition_e = "";
					break;
				case 5:
					$with_condition_a = "";
					$with_condition_b = "";
					$with_condition_c = "";
					$with_condition_d = "";
					$with_condition_e = "X";
			}

		}else{
			$with_condition_a = "";
			$with_condition_b = "";
			$with_condition_c = "";
			$with_condition_d = "";
			$with_condition_e = "";
		}


		$remarks = $row['remarks'];
		//----end----

		$this->Cell(19, $rowheight, "HEIGHT:", 0,0,'L');
			$x = $this->GetX();
			$y = $this->GetY();
		$this->Line($x, $y+$rowheight, $x+12, $y+$rowheight);
		$this->Cell(12, $rowheight, $height,0,0,'C'); //put height here
		$this->Cell(15, $rowheight, $height_unit, 0,0,'L'); //put height unit here

		$this->Cell(20, $rowheight, "WEIGHT:", 0,0,'L');
			$x = $this->GetX();
			$y = $this->GetY();
		$this->Line($x, $y+$rowheight, $x+15, $y+$rowheight);
		$this->Cell(15, $rowheight, $weight,0,0,'C'); //put weight here
		$this->Cell(15, $rowheight, $weight_unit,0,0,'L'); //put weight unit here

		$this->Cell(42, $rowheight, "BLOOD PRESSURE:",0,0,'L');
			$x = $this->GetX();
			$y = $this->GetY();
		$this->Line($x, $y+$rowheight, $x+20, $y+$rowheight);
		$this->Cell(20, $rowheight, $blood_pressure, 0,1,'L'); //put blood pressure here

		$this->Cell(70, $rowheight, "GENERAL PHYSIQUE:",0,0,'L');
		$this->Cell(70, $rowheight, "CONTAGIOUS DISEASES:", 0,1,'L');

		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $normal_physique, 1, 0, 'C'); //general physique - normal
		$this->Cell(55, $rowheight, "Normal", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $no_disease,1,0,'C'); //contagious diseases - none
		$this->Cell(55, $rowheight, "None", 0,1,'L');

		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $abnormal_physique, 1, 0, 'C'); //general_physique - abnormal
		$this->Cell(20, $rowheight, "Abnormal", 0,0,'L');
		$this->Cell(35, $rowheight, $abnormality_desc, "B",0,'L'); //general physique - description

		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $with_disease, 1,0,'C'); //contagious diseases - with disease
		$this->Cell(55, $rowheight, $disease, "B",1,'L'); // disease

		$this->Cell(0, $rowheight, "VISUAL ACUITY:", 0,1, 'L');

		$this->Cell(45, $rowheight, "LEFT EYE : SNELLEN", 0, 0, 'L');
		$this->Cell(30, $rowheight, $left_eye_snellen, "B", 0,'L');
		$this->Cell(2, $rowheight);
		$this->Cell(48, $rowheight, "RIGHT EYE : SNELLEN", 0, 0, 'L');
		$this->Cell(22, $rowheight, $right_eye_snellen, "B", 1,'L');

		$this->Cell(50, $rowheight, "VISUAL ACUITY CHART:",0,0,'L');
		$this->Cell(25, $rowheight, $left_visual_acuity,"B",0,'L');
		$this->Cell(2, $rowheight);
		$this->Cell(50, $rowheight, "VISUAL ACUITY CHART:",0,0,'L');
		$this->Cell(25, $rowheight, $right_visual_acuity,"B",1,'L');

		$this->Cell(5, $rowheight);
		$this->Cell(5, $rowheight-1, $left_corrective_lenses,1,0,'C');
		$this->Cell(65, $rowheight, "With corrective lens or contact", 0,0,'L');
		$this->Cell(2, $rowheight);
		$this->Cell(5, $rowheight);
		$this->Cell(5, $rowheight-1, $right_corrective_lenses,1,0,'C');
		$this->Cell(65, $rowheight, "With corrective lens or contact", 0,1,'L');

		$this->Cell(5, $rowheight);
		$this->Cell(5, $rowheight-1, $left_color_blind, 1, 0, 'C');
		$this->Cell(65, $rowheight, "Color Blind", 0,0,'L');
		$this->Cell(2, $rowheight);
		$this->Cell(5, $rowheight);
		$this->Cell(5, $rowheight-1, $right_color_blind, 1, 0, 'C');
		$this->Cell(65, $rowheight, "Color Blind", 0,1,'L');

		$this->Cell(120, $rowheight, "HEARING:", 0,0,'L');
			$xsave = $this->GetX();
		$this->SetFont('Arial', 'B', 10);
		$xsave = $xsave + 2;
		$this->Cell(2, $rowheight);
		$this->Cell(68, $rowheight, "COMMENTS:","TLR",1,'L');

		$this->SetFont('Arial', '', 12);
		$this->Cell(60, $rowheight, "LEFT EAR:", 0, 0,'L');
		$this->Cell(60, $rowheight, "RIGHT EAR:",0,0,'L');
			$ysave = $this->GetY();
		$this->Ln();

		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $left_ear_normal, 1, 0, 'C');
		$this->Cell(45, $rowheight, "Normal", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $right_ear_normal, 1, 0, 'C');
		$this->Cell(45, $rowheight, "Normal", 0,1,'L');

		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $left_ear_reduced, 1, 0, 'C');
		$this->Cell(45, $rowheight, "Reduced", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $right_ear_reduced, 1, 0, 'C');
		$this->Cell(45, $rowheight, "Reduced", 0,1,'L');

		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $left_ear_zero, 1, 0, 'C');
		$this->Cell(45, $rowheight, "Zero", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $right_ear_zero, 1, 0, 'C');
		$this->Cell(45, $rowheight, "Zero", 0,1,'L');

		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $left_ear_hearing_aid, 1, 0, 'C');
		$this->Cell(45, $rowheight, "With Hearing Aid", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $right_ear_hearing_aid, 1, 0, 'C');
		$this->Cell(45, $rowheight, "With Hearing Aid", 0,1,'L');

		$this->Cell(120, $rowheight, "UPPER EXTREMITIES:", 0,1,'L');
		$this->Cell(60, $rowheight, "LEFT:",0,0,'L');
		$this->Cell(60, $rowheight, "RIGHT:",0,1,'L');

		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $left_upper_extremities_normal, 1,0,'C');
		$this->Cell(45, $rowheight, "Normal", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $right_upper_extremities_normal, 1,0,'C');
		$this->Cell(45, $rowheight, "Normal", 0,1,'L');

		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $left_upper_extremities_defective, 1,0,'C');
		$this->Cell(45, $rowheight, "Defective", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $right_upper_extremities_defective, 1,0,'C');
		$this->Cell(45, $rowheight, "Defective", 0,1,'L');

		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $left_upper_extremities_equipment, 1,0,'C');
		$this->Cell(45, $rowheight, "With Special Equipment", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $right_upper_extremities_equipment, 1,0,'C');
		$this->Cell(45, $rowheight, "With Special Equipment", 0,1,'L');

		$this->Cell(120, $rowheight, "LOWER EXTREMITIES:", 0,1,'L');
		$this->Cell(60, $rowheight, "LEFT:",0,0,'L');
		$this->Cell(60, $rowheight, "RIGHT:",0,1,'L');

		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $left_lower_extremities_normal, 1,0,'C');
		$this->Cell(45, $rowheight, "Normal", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $right_lower_extremities_normal, 1,0,'C');
		$this->Cell(45, $rowheight, "Normal", 0,1,'L');

		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $left_lower_extremities_defective, 1,0,'C');
		$this->Cell(45, $rowheight, "Defective", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $right_lower_extremities_defective, 1,0,'C');
		$this->Cell(45, $rowheight, "Defective", 0,1,'L');

		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $left_lower_extremities_equipment, 1,0,'C');
		$this->Cell(45, $rowheight, "With Special Equipment", 0,0,'L');
		$this->Cell(10, $rowheight);
		$this->Cell(5, $rowheight-1, $right_lower_extremities_equipment, 1,0,'C');
		$this->Cell(45, $rowheight, "With Special Equipment", 0,1,'L');
		$this->Ln();
		$xsave2 = $this->GetX();
		$ysave2 = $this->GetY();

		//comments
		$this->SetFont('Arial', '', 10);
		$this->SetXY($xsave, $ysave);
		$this->Cell(2, $rowheight, "", "L");
		$this->Cell(5, $rowheight-1, $fit_to_drive, 1, 0, 'C');
		$this->Cell(61, $rowheight, "Fit to Drive", "R",1,'L');
		$this->SetX($xsave);
		$this->Cell(2, $rowheight, "", "L");
		$this->Cell(5, $rowheight-1, $without_condition, 1, 0, 'C');
		$this->Cell(61, $rowheight, "Without Conditions", "R",1,'L');
		$this->SetX($xsave);
		$this->Cell(2, $rowheight, "", "L");
		$this->Cell(5, $rowheight-1, $with_condition, 1, 0, 'C');
		$this->Cell(61, $rowheight, "With Conditions", "R",1,'L');
		$this->SetX($xsave);
		$this->Cell(7, $rowheight, "", "L");
		$this->Cell(5, $rowheight, $with_condition_a, 1, 0, 'C');
		$this->Cell(56, $rowheight, "A. Wear Corrective Lenses", "R",1,'L');
		$this->SetX($xsave);
		$this->Cell(7, $rowheight, "", "L");
		$this->Cell(5, $rowheight, $with_condition_b, 1, 0, 'C');
		$this->Cell(56, $rowheight, "B. Drive Only With Special", "R",1,'L');
		$this->SetX($xsave);
		$this->Cell(7, $rowheight, "", "L");
		$this->Cell(5, $rowheight, "", 0, 0, 'C');
		$this->Cell(56, $rowheight, "Equipment For Upper Limbs", "R",1,'L');
		$this->SetX($xsave);
		$this->Cell(7, $rowheight, "", "L");
		$this->Cell(5, $rowheight, $with_condition_c, 1, 0, 'C');
		$this->Cell(56, $rowheight, "C. Drive Only With Special", "R",1,'L');
		$this->SetX($xsave);
		$this->Cell(7, $rowheight, "", "L");
		$this->Cell(5, $rowheight, "", 0, 0, 'C');
		$this->Cell(56, $rowheight, "Equipment For Lower Limbs", "R",1,'L');
		$this->SetX($xsave);
		$this->Cell(7, $rowheight, "", "L");
		$this->Cell(5, $rowheight, $with_condition_d, 1, 0, 'C');
		$this->Cell(56, $rowheight, "D. Daylight Driving Only", "R",1,'L');
		$this->SetX($xsave);
		$this->Cell(7, $rowheight, "", "L");
		$this->Cell(5, $rowheight, $with_condition_e, 1, 0, 'C');
		$this->Cell(56, $rowheight, "E. Must be Accompanied by a", "R",1,'L');
		$this->SetX($xsave);
		$this->Cell(7, $rowheight, "", "L");
		$this->Cell(5, $rowheight, "", 0, 0, 'C');
		$this->Cell(56, $rowheight, "Person With Normal Hearing", "R",1,'L');
		$this->SetX($xsave);
		$this->Cell(68, $rowheight, "", "LR", 1);

		$this->SetX($xsave);
		$this->Cell(68, $rowheight, "REMARKS:", "LR", 1, 'L');
		$this->SetX($xsave);
		$this->MultiCell(68, $rowheight, $remarks, "LRB", 'L');

		$dr_nr = $row['exam_physician'];
		$physician = $pers_obj->get_Person_name($dr_nr);
		$exam_physician = $physician['dr_name'];
		$license = $physician['license_nr'];
		$control_no = $row['control_number'];

		$this->SetXY($xsave2, $ysave2);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(122, $rowheight, "I HEREBY CERTIFY TO THE MEDICAL EXAMINATION PERFORMED", 0, 1, 'L');
		$this->Cell(122, $rowheight, "SIGNATURE OF EXAMINING PHYSICIAN", 0, 1, 'L');
		$this->Ln();
		$this->Cell(30, $rowheight, "PRINTED NAME:", 0, 0, 'L');
		$this->SetFont('Arial', '', 10);
		$this->Cell(120, $rowheight, $exam_physician.", M.D.", "B", 1, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(45, $rowheight, "PRC LICENSE NUMBER:", 0,0,'L');
		$this->SetFont('Arial', '', 10);
		$this->Cell(25, $rowheight, $license, "B", 0,'L');
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(70, $rowheight, "MEDICAL CERTIFICATE CONTROL NO.:", 0, 0, 'L');
		$this->Cell(40, $rowheight, $control_no, "B", 1, 'L');
		$this->Cell(0, $rowheight, "VALID AT: METRO DAVAO LICENSING CENTER", 0,1,'L');
		$this->Cell(0, $rowheight, "VALID ONLY FOR ONE (1) YEAR from the DATE OF ISSUE", 0, 1,'L');
	}

	function Footer()
	{
		$this->SetY(-23);
		$this->SetFont('Arial','I',8);
		//$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
	}

	//-------------------------------------
	 function SetWidths($w)
	 {
			//Set the array of column widths
			$this->widths=$w;
	 }

	 function SetAligns($a)
	 {
			//Set the array of column alignments
			$this->aligns=$a;
	 }

	 function Row($data)
	 {
		$row = 4;
			//Calculate the height of the row
			$nb=0;
			for($i=0;$i<count($data);$i++)
					$nb=max($nb,$this->NbLines($this->ColumnWidth[$i],$data[$i]));
					$nb2=$this->NbLines($this->ColumnWidth[1],$data[1]);
					$nb3=$this->NbLines($this->ColumnWidth[5],$data[5]);
					if($nb2>$nb3){
						$nbdiff = $nb2 - $nb3;
						 $nbdiff = $nbdiff*$row;
						k == 1;
					}
					else if($nb3>$nb2){
						$nbdiff = $nb3 - $nb2;
						 $nbdiff = $nbdiff*$row;
						k==0;
					}
					else{
						$nbdiff = 0;
					}

					//$nb3=max($nb,$this->NbLines($this->widths[0],$data[0]));
					//print_r($nb2, $nb3);

					//$nb = $nb*2;
					//print_r($nb);
			$h=$row*$nb;
			//Issue a page break first if needed
			$this->CheckPageBreak($h);
			//Draw the cells of the row

			for($i=0;$i<count($data);$i++)
			{
					$w=$this->ColumnWidth[$i];
					$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
					//$a = isset($this->Alignment[$i]) ? $this->Alignment[$i] : 'L';
					//Save the current position

					$x=$this->GetX();
					$y=$this->GetY();
					//Draw the border

							$length = $this->GetStringWidth($data[$i]);
							if($length < $this->ColumnWidth[$i]){
								//$this->Cell($w, $h, $data[$i],1,0,'L');
								$this->Cell($w, $h, $data[$i], 1, 0, $this->Alignment[$i]);
							}
							else{
								$nbrow = 3;
								// $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
								//$this->MultiCell($w, $row,$data[$i],1,'L');
								$this->MultiCell($w, $row, $data[$i], 1,$this->Alignment[$i]);

								//$this->MultiCell($length, $row,$data[$i],1,'L');

							}

					//Put the position to the right of the cell
					$this->SetXY($x+$w,$y);
			}
			//Go to the next line
			$this->Ln($h);
		}

		function CheckPageBreak($h) {
				//If the height h would cause an overflow, add a new page immediately
				if($this->GetY()+$h>$this->PageBreakTrigger)
						$this->AddPage($this->CurOrientation);
		}

		function NbLines($w,$txt) {
				//Computes the number of lines a MultiCell of width w will take
				$cw=&$this->CurrentFont['cw'];
				if($w==0)
						$w=$this->w-$this->rMargin-$this->x;
				$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
				$s=str_replace("\r",'',$txt);
				$nb=strlen($s);
				if($nb>0 and $s[$nb-1]=="\n")
						$nb--;
				$sep=-1;
				$i=0;
				$j=0;
				$l=0;
				$nl=1;
				while($i<$nb)
				{
						$c=$s[$i];
						if($c=="\n")
						{
								$i++;
								$sep=-1;
								$j=$i;
								$l=0;
								$nl++;
								continue;
						}
						if($c==' ')
								$sep=$i;
						$l+=$cw[$c];
						if($l>$wmax)
						{
								if($sep==-1)
								{
										if($i==$j)
												$i++;
								}
								else
										$i=$sep+1;
								$sep=-1;
								$j=$i;
								$l=0;
								$nl++;
						}
						else
								$i++;
				}
				return $nl;
		}

}

//$from = $_GET['from'];
//$to = $_GET['to'];

$pdf = new MedCertDriver_IC($_GET['encounter_nr']);
$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->GetData();
$pdf->Output();
?>