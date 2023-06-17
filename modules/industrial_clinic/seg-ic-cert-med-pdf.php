<?php
require('./roots.php');
require($root_path."/classes/fpdf/pdf.class.php");
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/industrial_clinic/class_ic_med_cert.php');

$font_nr = $_GET['font_size'];



class MedicalDentalCertificate extends FPDF
{
	var $from;
	var $to;
	var $count_rows;
	var $name;
	var $age;
	var $encounter_nr;
	var $gender;
	var $status;
	var $address;
	var $cert_date;
	var $med_findings;
	var $dental_findings;
	var $optha_findings;
	var $ent_findings;
	var $note;
	var $dentist;
	var $physician;
	var $optha;
	var $ent;
	var $dentist_license;
	var $physician_license;
	var $ent_license;
	var $optha_license;
	var $hosp;
	var $encoder;
	var $with_dental;
	var $with_medical;
	var $with_optha;
	var $with_ent;
	var $recommendation;
	var $rec_desc;


	function MedicalDentalCertificate($encounter_nr){
		global $db;
		$this->ColumnWidth = array(22,56,32,15,12,80,33,25);
		$this->SetTopMargin(3);
		$this->Alignment = array('C','L','C','C','C','L','C','C');
		$this->FPDF("P", 'mm', 'Letter');

		//if ($from) $this->from=date("Y-m-d",strtotime($from));
		//if ($to) $this->to=date("Y-m-d",strtotime($to));
		$this->encounter_nr = $encounter_nr;
	}

	function Header() {
		global $root_path, $db;
		$rowheight = 5;
		//$this->Ln($rowheight*4);
		
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

		$title_department = "HEALTH SERVICES AND SPECIALTY CLINIC (HSSC)";

		$this->hosp = $row['hosp_id'];
		#logo
		$this->Image('../registration_admission/image/logo_doh.jpg',25,10,20,20);
		$this->Image('../registration_admission/image/dmc_logo.jpg',170,10,20,20);

		#hospital info
		$this->SetFont('Arial', '', 11);
		$this->Cell(0, $rowheight-1, $row['hosp_country'], 0,1,'C');
		$this->Cell(0, $rowheight-1, $row['hosp_agency'], 0,1, 'C');
		$this->SetFont("Arial","B",12);
		$this->Cell(0, $rowheight-1, $row['hosp_name'], 0,1, 'C');
		$this->SetFont('Arial', '', 11);
		$this->Cell(0, $rowheight-1, $row['hosp_addr1'], 0,1, 'C');
		$this->setY(30);
		$this->SetFont('Arial', 'B', 11);
		$this->Cell(0, $rowheight-1, $title_department, 0,1, 'C');
		$this->setY(30);

		

		// $this->SetFont('Arial', 'B', 12);
		// $this->Cell(0, $rowheight, "Clinic No. ".$clinic_number, 0,1,'C');
		// $this->Cell(0, $rowheight, "Date: ".$this->cert_date, 0, 1, 'C');

		// $this->Ln($rowheight*2);
		//echo "session= ".$HTTP_SESSION_VARS['sess_user_name'];
	}

	function MedCertData(){
		global $root_path, $db;
		$rowheight = 5;
		$objPatient = new SegICCertMed;
		$pers_obj =new Personell;

		// $fontSizeTextUIDental = $_GET['fsizedental'];
		// $fontSizeTextUIOptha = $_GET['fsizeoptha'];
		// $fontSizeTextUIDent = $_GET['fsizedent'];
		// $fontSizeTextUIMed = $_GET['fsizemed'];
		$fontSizeTextUIRem = $_GET['fsizerem'];

		#get patient details
		$row =$objPatient->getPatientData($this->encounter_nr);
		#hrn
		$this->Ln(9); #edited by art 01/18/2014
		$this->SetFont('Arial', '', 11);
		$this->Cell(130, 3 , '', "",0,'');
		$this->Cell(25, 3 , 'HRN:', "",0,'');
		$this->SetFont('Arial', 'B', 12);
		$this->Cell(45, 3 , $row['pid'], "",0,'');
		#case
		$this->Ln(4);
		$this->SetFont('Arial', '', 11);
		$this->Cell(130, 3 , '', "",0,'');
		$this->Cell(25, 3 , 'CASE NO.:', "",0,'');
		$this->SetFont('Arial', 'B', 12);
		$this->Cell(45, 3 , $row['clinic_num'], "",0,'');
		#date
		$this->Ln(4);
		$this->SetFont('Arial', '', 11);
		$this->Cell(130, 3 , '', "",0,'');
		$this->Cell(25, 3 , 'DATE:', "",0,'');
		$this->SetFont('Arial', 'B', 12);
		$date_created = date("m/d/Y",strtotime($row['create_dt']));
		$this->Cell(45, 3 , ''.$date_created, "",0,'');
		$this->Ln(8);
		

		//variables
		/* commented by art
			if($row['with_medical']=='1' && $row['with_dental']=='1')
				$title = "MEDICAL / DENTAL EXAMINATION CERTIFICATE";
			else if($row['with_medical']=='1' && $row['with_dental']!='1')
				$title = "MEDICAL EXAMINATION CERTIFICATE";
			else if($row['with_medical']!='1' && $row['with_dental']=='1')
				$title = "DENTAL EXAMINATION CERTIFICATE";
		*/

		$title = 'MEDICAL CERTIFICATE';
		/* commented by art
		$clinic_number = $row['clinic_num'];
		*/
		//$this->cert_date = date("F j, Y", strtotime($row['medcert_date']));
		
		if (trim($row['street_name'])){
				if (trim($row["brgy_name"])!="NOT PROVIDED")
					$street_name = trim($row['street_name']);
				else
					$street_name = trim($row['street_name']);
		}else{
				$street_name = "";
		}

		if ((!(trim($row["brgy_name"]))) || (trim($row["brgy_name"])=="NOT PROVIDED"))
			$brgy_name = "";
		else
			$brgy_name  = ", ".trim($row["brgy_name"]);

		if ((!(trim($row["mun_name"]))) || (trim($row["mun_name"])=="NOT PROVIDED"))
			$mun_name = "";
		else{
			if ($brgy_name)
				$mun_name = ", ". trim($row["mun_name"]);
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

		$this->name = $row['patient_name'];
		$this->status = $row['civil_status'];
		$this->age = floor((time() - strtotime($row['date_birth']))/31556926);
		$this->med_findings = strtoupper($row['medical_findings']);
		$this->dental_findings = strtoupper($row['dental_findings']);
		$this->optha_findings = strtoupper($row['optha_findings']);
		$this->ent_findings = strtoupper($row['ent_findings']);
		$this->note = $row['remarks'];

		#med doctor
		$physician_num = $row['dr_nr_med'];
		$physician = $pers_obj->get_Person_name($physician_num);
		$this->physician = $physician['dr_name'];
		$this->physician_license = $physician['license_nr'];

		#dentist
		$dentist_num = $row['dr_nr_dental'];
		$dentist = $pers_obj->get_Person_name($dentist_num);
		$this->dentist = $dentist['dr_name'];
		$this->dentist_license = $dentist['license_nr'];

		#ent doctor
		$ent_num = $row['dr_nr_ent'];
		$ent = $pers_obj->get_Person_name($ent_num);
		$this->ent = $ent['dr_name'];
		$this->ent_license = $ent['license_nr'];

		#optha doctor
		$optha_num = $row['dr_nr_optha'];
		$optha = $pers_obj->get_Person_name($optha_num);
		$this->optha = $optha['dr_name'];
		$this->optha_license = $optha['license_nr'];

		#encoder
		$this->encoder = $row['modify_id'];

		$this->with_dental = $row['with_dental'];
		$this->with_medical = $row['with_medical'];
		$this->with_ent = $row['with_ent'];
		$this->with_optha = $row['with_optha'];
		$this->recommendation = $row['recommendation'];

		#title
		$this->SetFont('Arial', 'B', 14);
		$this->Cell(0, $rowheight, $title,0,1,'C');

		#Salutation
		$this->Ln(8);
		$this->SetFont('Arial', '', 12);
		$this->Cell(0,3, 'TO WHOM IT MAY CONCERN:', $border2,1,'L');

		#content
		$certifies = "       This certifies that ";
		$certifies .= strtoupper($this->name).', ';
		$certifies .= $this->age.' years old, ';
		$certifies .= ucwords(strtolower($this->gender)).', ';
		$certifies .= ucwords(strtolower($this->status)).' and a resident of ';
		$certifies .= ucwords(strtolower(rtrim($this->address, ','))).' was examined in this hospital on ';
		$date_created1 = date("F j, Y", strtotime($row['trxn_date'])); //added by Macoy August 01,2014
		$certifies .= $date_created1.' with the following findings:'; //edited by Macoy August 01,2014 
		$this->Ln(7);
		$this->SetFont('Arial', '', 11);
		$this->MultiCell(195, $rowheight,$certifies, 0, 'J');
		$this->Ln();

		#with dental
		#Added by Borj 2014-09-08 Font Size
		if($this->with_dental=='1'){
			$this->SetFont('Arial', 'B',$fontSizeTextUIRem);
			$this->MultiCell(195, $rowheight, strtoupper($this->dental_findings), 0, 'C');
			$this->Ln();
			$this->SetFont('Arial', 'BU', 11);
			$this->Cell(100, $rowheight);
			$this->Cell(95, $rowheight, "(sgd) ".strtoupper($this->dentist).", DMD", 0, 1, 'C');
			$this->SetFont('Arial', 'BI', 11);
			$this->Cell(100, $rowheight);
			$this->Cell(95, $rowheight, "DENTIST IN-CHARGE", 0,1,'C');
			$this->Cell(127, $rowheight);
			$this->Cell(24, $rowheight, "License No. ",0,0,'L');
			$this->SetFont('Arial', 'IU', 11);
			$this->Cell(41, $rowheight, $this->dentist_license, 0,1,'L');
			#$this->Ln($rowheight*2);
			$this->Ln();
		}

		#with optha
		#Added by Borj 2014-09-08 Font Size
		if($this->with_optha=='1'){
			$this->SetFont('Arial', 'B', $fontSizeTextUIRem);
			$this->MultiCell(195, $rowheight, strtoupper($this->optha_findings), 0, 'C');
			$this->Ln();
			$this->SetFont('Arial', 'BU', 11);
			$this->Cell(100, $rowheight);
			$this->Cell(95, $rowheight, strtoupper($this->optha).", MD", 0, 1, 'C');
			$this->SetFont('Arial', 'BI', 11);
			$this->Cell(100, $rowheight);
			$this->Cell(95, $rowheight, "(OPHTHALMOLOGIST) PHYSICIAN IN-CHARGE", 0,1,'C');
			$this->Cell(127, $rowheight);
			$this->Cell(24, $rowheight, "License No. ",0,0,'L');
			$this->SetFont('Arial', 'IU', 11);
			$this->Cell(41, $rowheight, $this->optha_license, 0,1,'L');
			#$this->Ln($rowheight*2);
			$this->Ln();
		}

		#with ent
		#Added by Borj 2014-09-08 Font Size
		if($this->with_ent=='1'){
			$this->SetFont('Arial', 'B', $fontSizeTextUIRem);
			$this->MultiCell(195, $rowheight, strtoupper($this->ent_findings), 0, 'C');
			$this->Ln();
			$this->SetFont('Arial', 'BU', 11);
			$this->Cell(100, $rowheight);
			$this->Cell(95, $rowheight,strtoupper($this->ent).", MD", 0, 1, 'C');
			$this->SetFont('Arial', 'BI', 11);
			$this->Cell(100, $rowheight);
			$this->Cell(95, $rowheight, "(ENT) IN-CHARGE", 0,1,'C');
			$this->Cell(127, $rowheight);
			$this->Cell(24, $rowheight, "License No. ",0,0,'L');
			$this->SetFont('Arial', 'IU', 11);
			$this->Cell(41, $rowheight, $this->ent_license, 0,1,'L');
			#$this->Ln($rowheight*2);
			$this->Ln();
		}

		#with medical
		#Added by Borj 2014-09-08 Font Size
		if($this->with_medical=='1'){
			$this->SetFont('Arial', 'B', $fontSizeTextUIRem);
			$this->MultiCell(195, $rowheight, strtoupper($this->med_findings),0,'C');
			$this->Ln();

			if (!empty($this->recommendation)) {

				switch ($this->recommendation) {
					case '1':
						$this->rec_desc="Class A - Physically fit for any work";
						break;
					case '2';
						$this->rec_desc="Class B - With Correctible defects";
						break;
					case '3';
						$this->rec_desc="Class C - Employable but owing to certain impairments or condition requires special placement 
						or limited duty requiring follow up treatment/periodic evaluation";
						break;
					case '4';
						$this->rec_desc="Class D - Unfit or unsafe for any type of employment";
						break;
					case '5';
						$this->rec_desc="Pending, for further evaluation";
						break;
				}
				$this->SetFont('Arial', '', $fontSizeTextUIRem);
				$this->MultiCell(183, $rowheight, $this->rec_desc,0,'');
			}
			$this->Ln(3);

			#note
			#Added by Borj 2014-09-08 Font Size
			if (!empty($this->note)) {
				$this->SetFont('Arial', 'B', 11);
				$this->Cell(55, $rowheight, "Remarks/Recommendation:",0,0,'L');
				$this->SetFont('Arial', '', $fontSizeTextUIRem);
				$this->MultiCell(183, $rowheight, $this->note,0,'J');
			}
			#$this->Ln($rowheight*2);
			$this->Ln(1);
			$this->SetFont('Arial', 'BU', 11);
			$this->Cell(100, $rowheight);
			$this->Cell(95, $rowheight, strtoupper($this->physician).", MD",0,1,'C');
			$this->SetFont('Arial', 'BI', 11);
			$this->Cell(100, $rowheight);
			$this->Cell(95, $rowheight, "PHYSICIAN IN-CHARGE",0,1,'C');
			$this->Cell(125, $rowheight);
			$this->Cell(24, $rowheight, "License No. ",0,0,'L');
			$this->SetFont('Arial', 'IU', 11);
			$this->Cell(41, $rowheight, $this->physician_license, 0,1,'L');
			#$this->Ln($rowheight*2);
			$this->Ln();
		}

		#valid
		$this->SetFont('Arial', 'B', 11);
		$this->Cell(0, $rowheight+1,"  Not Valid", 0,1,'L');
		$this->Cell(0, $rowheight+1,"Without ".$this->hosp." Seal",0,1,'L');
		
	}

	function Footer()
	{
		#global $HTTP_SESSION_VARS;
		$code = 'SPMC-F-HIM-14';
		$this->SetY(-20);
		$this->SetFont('Arial','I',8);
		$this->Cell(5, 3 , '', "", 0,'');
		#$this->Cell(0,10,'Encoded by : '.$HTTP_SESSION_VARS['sess_user_name'],0,1,'L');
		$this->Cell(0,10,'Encoded by : '.$this->encoder,0,1,'L');

		$this->AliasNbPages(); 
		$this->SetFont('Arial','B',12);
		$this->Cell(0,2,$code, "",1,'L');
		$this->SetFont('Arial','',8);
		$this->Cell(60,8,'Effectivity : October 1, 2013',0,0,'L');
		$this->Cell(80,8,'Revision : 0',0,0,'C');
		$this->Cell(50,8,'Page '.$this->PageNo().' of {nb}',0,0,'R');
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

$pdf = new MedicalDentalCertificate($_GET['encounter_nr']);
$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->MedCertData();
$pdf->Output();
?>