<?php
require('./roots.php');
require($root_path."/classes/fpdf/pdf.class.php");
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/industrial_clinic/class_ic_med_cert.php');
include_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_personell.php');

class ExamChart extends FPDF{
	var $fontfamily_label = "Arial";
	var $fontfamily_answer = "Arial";
	var $fontstyle_label = '';
	var $fontstyle_label2 = "B";
	var $fontstyle_label3 = "BU";
	var $fontstyle_answer = "B";
	var $fontsize_label = 12;
	var $fontsize_label2 = 16;
	var $fontsize_label3 = 9;
	var $fontsize_label4 = 8;
	var $fontsize_answer = 10;
	var $rowheight = 5;
	var $totwidth = 200;
	var $nowidth = 0;
	var $header_label = "Center for Health Development - Davao Region";
	var $withoutborder = 0;
	var $withborder = 1;
	var $continueline = 0;
	var $nextline = 1;
	var $lineAdjustment = 0.5;
	var $alignCenter = "C";
	var $alignLeft = "L";
	var $alignRight = "R";
	var $alignJustify = "J";
	var $dept_name = "Industrial Clinic";
	var $encounter_nr;
	var $refno;
	var $pid;
	var $city = "Davao City";
	var $title = "MEDICAL EXAMINATION CHART";
	var $label = array('Examination not done', 'No abnormality found', 'Abnormality noted', 'Remarks');

	function ExamChart($refno){
		global $db;
		$this->ColumnWidth = array(22,56,32,15,12,80,33,25);
		$this->SetTopMargin(3);
		$this->Alignment = array('C','L','C','C','C','L','C','C');
		$this->FPDF("P", 'mm', 'Legal');

		#$this->encounter_nr = $encounter_nr;
		$this->refno = $refno;
		#$this->pid = $pid;
	}

	function Header_() {
		global $root_path, $db;

		//$this->Ln($rowheight*4);
		$pers_obj=new Personell;

		$objInfo = new Hospital_Admin();

		if ($row = $objInfo->getAllHospitalInfo()) {
			$row['hosp_agency'] = strtoupper($row['hosp_agency']);
			$row['hosp_name']   = strtoupper($row['hosp_name']);
		}
		else {
			$row['hosp_country'] = "Republic of the Philippines";
			$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
			$row['hosp_name']    = "SOUTHERN PHILIPPINES MEDICAL CENTER";
			$row['hosp_addr1']   = "JICA Bldg., JP Laurel Avenue, Davao City";
		}


		 $this->Image($root_path.'gui/img/logos/dmc_logo.jpg',35,6,15);
		 $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		 $this->Cell($this->nowidth, $this->rowheight, $row['hosp_country'], $this->withoutborder, $this->nextline, $this->alignCenter);
		 $this->Cell($this->nowidth, $this->rowheight, ucwords(strtolower($row['hosp_agency'])), $this->withoutborder, $this->nextline, $this->alignCenter);
		 $this->Cell($this->nowidth, $this->rowheight, $this->header_label, $this->withoutborder, $this->nextline, $this->alignCenter);
		 $this->Cell($this->nowidth, $this->rowheight, $row['hosp_name']." - ".$this->dept_name, $this->withoutborder, $this->nextline, $this->alignCenter);
		 $this->Cell($this->nowidth, $this->rowheight, $this->city, $this->withoutborder, $this->nextline, $this->alignCenter);
		 $this->Ln();

		 $this->SetFont($this->fontfamily_label, $this->fontstyle_label2, $this->fontsize_label2);
		 $this->Cell($this->nowidth, $this->rowheight, strtoupper($this->title), $this->withoutborder, $this->nextline, $this->alignCenter);
		 $this->Ln();

		//put query here.....
				$sql = "SELECT c.*, CONCAT(IFNULL(p.name_last,''),IFNULL(CONCAT(', ', p.name_first),''),IFNULL(CONCAT(' ', p.name_middle),'')) AS patient_name,
		fn_calculate_age(p.date_birth, NOW()) AS patient_age, p.civil_status, UPPER(p.sex) AS patient_sex,
		p.street_name,  sb.brgy_name, sm.mun_name, sm.zipcode, sp.prov_name
		FROM seg_industrial_cert_med AS c
		INNER JOIN seg_industrial_transaction AS t ON t.refno = c.refno
		INNER JOIN care_person AS p ON p.pid = t.pid
		LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
		LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr
		LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
		WHERE t.encounter_nr = '".$this->encounter_nr."';";
		$result = $db->Execute($sql);
		$row = $result->FetchRow();

		//variables
			if($row['with_medical']=='1' && $row['with_dental']=='1')
				$title = "MEDICAL / DENTAL EXAMINATION CERTIFICATE";
			else if($row['with_medical']=='1' && $row['with_dental']!='1')
				$title = "MEDICAL EXAMINATION CERTIFICATE";
			else if($row['with_medical']!='1' && $row['with_dental']=='1')
				$title = "DENTAL EXAMINATION CERTIFICATE";

			$clinic_number = $row['clinic_num'];
			$this->cert_date = date("F j, Y", strtotime($row['medcert_date']));

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

			$this->address = trim($street_name).trim($brgy_name).trim($mun_name).trim($prov_name);

			if($row['patient_sex']=='M')
				$this->gender = "male";
			else
				$this->gender = "female";
	}

	function ChartData(){
		global $root_path, $db;
		$obj_medCert = new SegICCertMed;
		$enc_obj=new Encounter;
		$pers_obj=new Personell;

		//local variables
		$cnt_exam;
		$cnt_list;
		$cnt = 0;
		$physician = array('Dentist', 'Doctor');
		$ColumnWidth = array(40, 25, 4 ,70, 60);
		$ColumnWidth2 = array(25, 25,30, 4,111);
		$ColumnWidth3 = array(100, 100);

		//call class or do sql here...
		$medchartInfo = $obj_medCert->getMedChartInfo($this->refno);
		$encInfo=$enc_obj->getEncounterInfo($medchartInfo['encounter_nr']);
		#print_r($encInfo);

		$physician_num = $medchartInfo['physician_nr'];
		$physician = $pers_obj->get_Person_name($physician_num);
		$physician_name = $physician['dr_name'];
		$physician_license = $physician['license_nr'];

		$answer = array();
		$remarks_arr = array();
		$dr_arr = array();
		$license_arr = array();
		$cnt1 = 1;
		$cnt2 = 1;
		$det = $obj_medCert->getMedChartDetails($this->refno);
		while($details = $det->FetchRow()){
			if($details['exam_type_list']==1){
				$answer[$cnt1][$cnt2] = "X";
				$answer[$cnt1][$cnt2+1] = "";
				$answer[$cnt1][$cnt2+2] = "";
			}else if($details['exam_type_list']==2){
				$answer[$cnt1][$cnt2] = "";
				$answer[$cnt1][$cnt2+1] = "X";
				$answer[$cnt1][$cnt2+2] = "";
			}else if($details['exam_type_list']==3){
				$answer[$cnt1][$cnt2] = "";
				$answer[$cnt1][$cnt2+1] = "";
				$answer[$cnt1][$cnt2+2] = "X";
			}

			if($details['remarks'])
				$remarks_arr[$cnt1] = $details['remarks'];
			else
				$remarks_arr[$cnt1] = "";

			if($details['dr_nr']!=0 || $details['dr_nr']==''){
				$nr = $details['dr_nr'];
				$dr = $pers_obj->get_Person_name($nr);
				$dr_arr[$cnt1] = strtoupper($dr['dr_name']);
				$license_arr[$cnt1] = $dr['license_nr'];
			}else{
				$dr_arr[$cnt1] = "";
				$license_arr[$cnt1] = "";
			}
			$cnt1++;
		}

		//print_r($remarks_arr);
		//end class/sql

		#print_r($physician);

		$exam = $obj_medCert->getTypeExam();
		$num_exam = $exam->RecordCount();
		$list = $obj_medCert->getChartList();
		$num_list = $list->RecordCount();

		$diagnosis = $medchartInfo['diagnosis'];
		$recommendation = $medchartInfo['recommendation'];

		if(is_object($list)){
			while($row = $list->FetchRow()){
				$rowList[$row['list_id']] = $row['list_name'];
			}
		}

		//print_r($rowList);

		if(is_object($exam)){
			while($row2 = $exam->FetchRow()){
				$rowExam[$row2['id']][$cnt] = $row2['name'];
				$rowExam[$row2['id']][$cnt+1] = $row2['with_dr_sig'];
			}
		}

		//print_r($rowExam);

		//assign to variables...
		$patient_name = stripslashes(strtoupper($encInfo['name_first'])).' '.stripslashes(strtoupper($encInfo['name_middle'])).' '.stripslashes(strtoupper($encInfo['name_last']));
		$patient_age = $encInfo['age'];
		if($encInfo['sex']=='f')
			$patient_sex = 'Female';
		else
			$patient_sex = 'Male';
		if($encInfo['civil_status'])
			$patient_status = $encInfo['civil_status'];
		else
			$patient_status = "UNKNOWN";
		$patient_agency = "";
		$patient_address = stripslashes(strtoupper($encInfo['street_name']))." ".stripslashes(strtoupper($encInfo['brgy_name']))." ".stripslashes(strtoupper($encInfo['mun_name'])). ", ".stripslashes(strtoupper($encInfo['prov_name']));
		$patient_birthdate = date("F j, Y", strtotime($encInfo['date_birth']));
		$patient_birthplace = $encInfo['place_birth'];
		$clinic_number = $encInfo['encounter_nr'];
		$date_examined = date("F j, Y", strtotime($medchartInfo['create_dt']));

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
		$this->Cell($this->nowidth, $this->rowheight, "Clinic No. ".$clinic_number, $this->withoutborder, $this->nextline, $this->alignCenter);  //put clinic number here
		$this->Cell($this->nowidth, $this->rowheight, "Date Examined: ".$date_examined, $this->withoutborder, $this->nextline, $this->alignCenter);  //put date examined here

		$this->Ln($rowheight*2);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
		$this->Cell(12, $this->rowheight, "Name:", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell(88, $this->rowheight, $patient_name, $this->withoutborder, $this->continueline, $this->alignLeft);  //put name here
		$this->Cell(8, $this->rowheight, "Age:",$this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell(22, $this->rowheight, $patient_age, $this->withoutborder, $this->continueline, $this->alignLeft); //put age here
		$this->Cell(8, $this->rowheight, "Sex:", $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell(15, $this->rowheight, $patient_sex, $this->withoutborder, $this->continueline, $this->alignCenter); //put sex here
		$this->Cell(25, $this->rowheight, "Civil Status:", $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell(22, $this->rowheight, $patient_status, $this->withoutborder, $this->nextline, $this->alignLeft); //put civil status here

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
		$this->Cell(35, $this->rowheight, "Agency / Organization:", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($this->totwidth - 35, $this->rowheight, $patient_agency, $this->withoutborder, $this->nextline, $this->alignLeft);  //put agency here

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
		$this->Cell(15, $this->rowheight, "Address:", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($this->totwidth - 15, $this->rowheight, $patient_address, $this->withoutborder, $this->nextline, $this->alignLeft); //put address here

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
		$this->Cell(15, $this->rowheight, "Birthday:", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell(40, $this->rowheight, $patient_birthdate, $this->withoutborder, $this->continueline, $this->alignLeft); //put birthday here
		$this->Cell(18, $this->rowheight, "Birthplace:", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell(117, $this->rowheight, $patient_birthplace, $this->withoutborder, $this->nextline, $this->alignLeft); //put
		$this->Ln();

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label3, $this->fontsize_label);
		$this->Cell($this->nowidth, $this->rowheight, "Findings / Results", $this->withoutborder, $this->nextline, $this->alignCenter);

		for($cnt_exam = 1; $cnt_exam <= $num_exam; $cnt_exam++){

			if($cnt_exam==5){
				$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
				$this->Cell($ColumnWidth2[0], $this->rowheight, "LABORATORY EXAMS", $this->withoutborder, $this->nextline, $this->alignLeft);
				$this->Cell($ColumnWidth2[0], $this->rowheight);
				$this->Cell($ColumnWidth2[1], $this->rowheight, "ROUTINE LABS", $this->withoutborder, $this->nextline, $this->alignLeft);
				$this->Cell($ColumnWidth2[0], $this->rowheight);
				$this->Cell($ColumnWidth2[1], $this->rowheight);
				$this->Cell($ColumnWidth2[2], $this->rowheight, $rowExam[$cnt_exam][$cnt], $this->withoutborder, $this->nextline, $this->alignLeft);
			}elseif($cnt_exam < 5){
				$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
				$this->Cell($ColumnWidth[0], $this->rowheight, $rowExam[$cnt_exam][$cnt], $this->withoutborder, $this->nextline, $this->alignLeft);
			}else{
				$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
				$this->Cell($ColumnWidth2[0], $this->rowheight);
				$this->Cell($ColumnWidth2[1], $this->rowheight);
				$this->Cell($ColumnWidth2[2], $this->rowheight, $rowExam[$cnt_exam][$cnt], $this->withoutborder, $this->nextline, $this->alignLeft);
			}


			for($cnt_list = 1; $cnt_list <= $num_list; $cnt_list++){

				if($cnt_exam < 5){
					if($cnt_list < 4){
						$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
						$this->Cell($ColumnWidth[0]+$ColumnWidth[1], $this->rowheight);
						#echo "answer = ".$answer[$cnt_exam][$cnt_list]."<br>";
						#echo "cnt_exam= ".$cnt_exam."<br>";
						$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
						$this->Cell($ColumnWidth[2], $this->rowheight-2, $answer[$cnt_exam][$cnt_list], $this->withborder, $this->continueline, $this->alignLeft);
						$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
						$this->Cell($ColumnWidth[3], $this->rowheight, $rowList[$cnt_list], $this->withoutborder, $this->nextline, $this->alignLeft);

					}else{
						$xsave = $this->GetX();
						$ysave = $this->GetY();
						$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
						$this->Cell($ColumnWidth[0]+$ColumnWidth[1]+$ColumnWidth[2], $this->rowheight);
						$this->Cell(20, $this->rowheight, "Remarks:", $this->withoutborder, $this->continueline, $this->alignLeft);
						$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
						//put remarks here...
						$this->MultiCell(($ColumnWidth[3]-20)+$ColumnWidth[4], $this->rowheight, $remarks_arr[$cnt_exam], $this->withoutborder, $this->alignJustify);
						$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontstyle_label4);
						$this->Cell($ColumnWidth[0]+$ColumnWidth[1]+$ColumnWidth[2]+$ColumnWidth[3], $this->rowheight);
						$this->Cell(18, $this->rowheight, "Signature:", $this->withoutborder, $this->continueline, $this->alignLeft);
						$x = $this->GetX();
						$y = $this->GetY();
						$this->Line($x, $y+($this->rowheight - $this->lineAdjustment), $x+$ColumnWidth[4]-18, $y+($this->rowheight - $this->lineAdjustment));
						$this->Ln();
						$this->Cell($ColumnWidth[0]+$ColumnWidth[1]+$ColumnWidth[2]+$ColumnWidth[3], $this->rowheight);
						$this->Cell(20, $this->rowheight, "License No.", $this->withoutborder, $this->continueline, $this->alignLeft);
						$x = $this->GetX();
						$y = $this->GetY();
						$this->Line($x, $y+($this->rowheight - $this->lineAdjustment), $x+$ColumnWidth[4]-20, $y+($this->rowheight - $this->lineAdjustment));
						$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
						//put license number here...
						$this->Cell($ColumnWidth[4]-20, $this->rowheight, $license_arr[$cnt_exam], $this->withoutborder, $this->nextline, $this->alignLeft);
						if($cnt_exam == 4){
							$this->SetXY($xsave, $ysave);
							$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
							$this->Cell(10, $this->rowheight, "BP:", $this->withoutborder, $this->continueline, $this->alignLeft);
							$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
							//put bp here...
							$this->Cell(30, $this->rowheight, $medchartInfo['systole']."/".$medchartInfo['diastole']." mmHG", $this->withoutborder, $this->nextline, $this->alignLeft);

							$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
							$this->Cell(10, $this->rowheight, "CR:", $this->withoutborder, $this->continueline, $this->alignLeft);
							$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
							//put cr here...
							$this->Cell(30, $this->rowheight, $medchartInfo['cardiac_rate'], $this->withoutborder, $this->nextline, $this->alignLeft);

							$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
							$this->Cell(10, $this->rowheight, "RR:", $this->withoutborder, $this->continueline, $this->alignLeft);
							$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
							//put rr here...
							$this->Cell(30, $this->rowheight, $medchartInfo['resp_rate']." br/m", $this->withoutborder, $this->nextline, $this->alignLeft);

							$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
							$this->Cell(10, $this->rowheight, "T:", $this->withoutborder, $this->continueline, $this->alignLeft);
							$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
							//put Temperature here...
							$this->Cell(30, $this->rowheight, $medchartInfo['temperature']." C", $this->withoutborder, $this->nextline, $this->alignLeft);

							$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
							$this->Cell(10, $this->rowheight, "Wt:", $this->withoutborder, $this->continueline, $this->alignLeft);
							$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
							//put weight here...
							$this->Cell(30, $this->rowheight, $medchartInfo['weight']." kg", $this->withoutborder, $this->nextline, $this->alignLeft);

							$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
							$this->Cell(10, $this->rowheight, "Ht:", $this->withoutborder, $this->continueline, $this->alignLeft);
							$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
							//put height here...
							$this->Cell(30, $this->rowheight, $medchartInfo['height']." ft", $this->withoutborder, $this->nextline, $this->alignLeft);

							$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
							$this->Cell(10, $this->rowheight, "BMI:", $this->withoutborder, $this->continueline, $this->alignLeft);
							$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
							//put BMI here...
							$this->Cell(30, $this->rowheight, $medchartInfo['bmi'], $this->withoutborder, $this->nextline, $this->alignLeft);
							$this->Ln();
						}
					}

				}else{
					if($cnt_list < 4){
						$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
						$this->Cell($ColumnWidth2[0]+$ColumnWidth2[1]+$ColumnWidth2[2], $this->rowheight);
						$this->Cell($ColumnWidth2[3], $this->rowheight-2, $ans1, $this->withborder, $this->continueline, $this->alignLeft);
						$this->Cell($ColumnWidth2[4], $this->rowheight, $rowList[$cnt_list], $this->withoutborder, $this->nextline, $this->alignLeft);
					}else{
						$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
						$this->Cell($ColumnWidth2[0]+$ColumnWidth2[1]+$ColumnWidth2[2], $this->rowheight);
						$this->Cell(20, $this->rowheight, "Remarks:", $this->withoutborder, $this->continueline, $this->alignLeft);
						$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
						$this->MultiCell(($ColumnWidth2[3]-20)+$ColumnWidth2[4], $this->rowheight, $remarks[$cnt_exam], $this->withoutborder, $this->alignJustify);
					}
				}

			}
		}

		//Other Laboratory Exams
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
		$this->Cell($ColumnWidth3[0], $this->rowheight, "Other Laboratory Exams", $this->withoutborder, $this->nextline, $this->alignLeft);

		//query to get labs...
		$other_exams = array();
		$tmp = 0;
		$query = "SELECT srv.name, 'lab' AS dept FROM seg_lab_servdetails AS sls
									INNER JOIN seg_lab_services AS srv ON srv.service_code = sls.service_code
									WHERE sls.refno = '$this->refno'
									AND sls.status NOT IN('deleted')
									AND sls.service_code NOT IN('CBC', 'URINE', 'FECAL')
									UNION ALL
									select rad.name, 'radio' AS dept FROM care_test_request_radio AS ct
									INNER JOIN seg_radio_services AS rad ON rad.service_code = ct.service_code
									WHERE ct.refno = '$this->refno'
									AND ct.status NOT IN('deleted')
									AND ct.service_code NOT IN('XRAY-C');
									";
				$result = $db->Execute($query);
				#echo $query;
				$num = $result->RecordCount();
				if(is_object($result)){
					while($row = $result->FetchRow()){

						if($row['dept']=='lab')
							$other_exams[$tmp] = $row['name']." (LB)";
						else
							$other_exams[$tmp] = $row['name']." (RD)";
						$tmp++;
					}
				}

				//print_r($other_exams);

		if($num > 0){
			$cols = $num / 2;
			$cols = round($cols);
		}

		$cnt = 2;
		for($i = 0; $i <= $cols; $i = $i+2){

			$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
			$this->Cell($ColumnWidth3[0], $this->rowheight, ($i+1).".) ".$other_exams[$i], $this->withoutborder, $this->continueline, $this->alignLeft);
			if($cnt < $num){
				$this->Cell($ColumnWidth3[1], $this->rowheight, ($i+2).".) ".$other_exams[$i+1], $this->withoutborder, $this->continueline, $this->alignLeft);
				$cnt = $cnt + 2;
				$this->Ln();
			}

		}
		$this->Ln();
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
		$this->Cell(20, $this->rowheight, "Remarks :", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->MultiCell($this->totwidth-20, $this->rowheight, $medchartInfo['remarks_other'], $this->withoutborder, $this->alignJustify);
		$this->Ln($this->rowheight*2);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
		$this->Cell(20, $this->rowheight, "Diagnosis :", $this->withoutborder, $this->continueline, $this->alignLeft);
		//put diagnosis here
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->MultiCell($this->totwidth - 20, $this->rowheight, $diagnosis, $this->withoutborder, $this->alignJustify);
		$this->Ln();
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
		$this->Cell(35, $this->rowheight, "Recommendations :", $this->withoutborder, $this->continueline, $this->alignLeft);
		//put recommendations here
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->MultiCell($this->totwidth - 35, $this->rowheight, $recommendation, $this->withoutborder, $this->alignJustify);
		$this->Ln($this->rowheight * 3);

		$this->Cell($ColumnWidth2[0], $this->rowheight);
		//put physician-in-charge
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell(60, $this->rowheight, strtoupper($physician_name).", M.D.", $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Line($x, $y+($this->rowheight - $this->lineAdjustment), $x+60, $y+($this->rowheight - $this->lineAdjustment));
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
		$this->Cell($ColumnWidth2[0], $this->rowheight);
		$this->Cell(60, $this->rowheight, "PHYSICIAN-IN-CHARGE", $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Cell($ColumnWidth2[0], $this->rowheight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
		$this->Cell(20, $this->rowheight, "License No.", $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		//put physician - in-charge license number here
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell(40, $this->rowheight, $physician_license, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Line($x, $y+($this->rowheight - $this->lineAdjustment), $x+40, $y+($this->rowheight - $this->lineAdjustment));

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label3);
		$this->Cell(40, $this->rowheight);
		$this->Cell(35, $this->rowheight, "Date Accomplished:", $this->withoutborder, $this->continueline, $this->alignLeft);
		//put date accomplished here
		$this->Cell(30, $this->rowheight, date("F j, Y", strtotime($medchartInfo['create_dt'])), $this->withoutborder, $this->continueline, $this->alignLeft);

	}

	function Footer()
	{
		global $HTTP_SESSION_VARS;
		$this->SetY(-23);
		$this->SetFont('Arial','I',8);
		//$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
		//$this->Cell(0, 5, "Encoded by: ".$HTTP_SESSION_VARS['sess_user_name'],0,0,'L');
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

#$from = $_GET['from'];
#$to = $_GET['to'];
#$code = $_GET['icd'];
//$case_num = "2009560200";

$pdf = new ExamChart($_GET['refno']);
$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Header_();
#$pdf->MedCertData();
$pdf->ChartData();
$pdf->Output();
?>