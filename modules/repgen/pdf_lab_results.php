<?php
		error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
		require('./roots.php');
		require($root_path.'include/inc_environment_global.php');
		require($root_path.'/modules/repgen/repgen.inc.php');
		require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
		require_once($root_path.'include/care_api_classes/class_lab_results.php');
		require_once($root_path.'include/care_api_classes/class_department.php');
		require_once($root_path.'include/care_api_classes/class_ward.php');
/**
* SegHIS - Hospital Information System (BPH Deployment)
* Enhanced by Segworks Technologies Corporation
*/

	class RepGen_LabResults extends RepGen {
	var $pid, $refno, $group_id, $gender, $done, $service_code;
	var $colored = TRUE;

	function RepGen_LabResults () {

				$this->pid = $_GET["pid"];
				$this->refno = $_GET["refno"];
				if(isset($_GET["group_id"]) && $_GET["group_id"]!='')
						$this->group_id = $_GET["group_id"];
				else
						$this->group_id = '0';
				if(isset($_GET["service_code"]) && $_GET["service_code"]!='')
						$this->service_code = $_GET["service_code"];
				else
						$this->service_code = '0';

				$lab_results = new Lab_Results();

				$this->SetMargins(1,1,1);
				$this->PageOrientation = "L";
				//$this->FPDF('P','mm','halfshort');
				$this->FPDF('L','mm','letter');
				if ($this->colored) $this->SetDrawColor(0xDD);
	}

	function Header() {

				$lab_results = new Lab_Results();
				$dept_obj=new Department;
				$ward_obj = new Ward;

				$borderYes="1";
				$borderNo="0";
				$newLineYes="1";
				$newLineNo="0";
				$space=2;

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

				$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',10,8,10);
				$this->SetXY(0,5);
				$this->SetFont("Arial","I","8");
				$width = 0;
				$height = 4;
				$this->SetXY(23,5);
				$this->Cell($width,$height,$row['hosp_country'],$borderNo,$newLineYes);
				$this->SetXY(23,8);
				$this->Cell($width,$height,$row['hosp_agency'], $borderNo,$newLineYes);
				$this->Ln(2);
				$this->SetFont("Arial","B","9");
				$this->SetXY(23,13);
				$this->Cell($width,$height,$row['hosp_name'],$borderNo,$newLineYes);
				$this->SetFont("Arial","","9");
				$this->SetXY(23,16);
				$this->Cell($width,$height,$row['hosp_addr1'],$borderNo,$newLineYes);
				$this->Ln(4);
				$this->SetFont("Arial","B","9");

				$patient = $lab_results->get_patient_data($this->refno, $this->group_id);
				if($patient!=NULL)
						extract($patient);
				else{
					 $sql = "SELECT * from seg_walkin WHERE pid='$this->pid'";
					 $rs = $db->Execute($sql);
					 if($rs && $pt = $rs->FetchRow()){
							 extract($pt);
					 }
					 $ordername = mb_strtoupper($name_last).", ".mb_strtoupper($name_first)." ".mb_strtoupper($name_middle).".";
				}
				//$sql = "select service_date FROM seg_lab_resultdata WHERE refno='$this->refno' AND group_id='$this->group_id' AND service_code='$this->service_code'  AND (ISNULL(`status`) OR `status`!='deleted');";
				$sql = "select service_date FROM seg_lab_resultdata WHERE refno='$this->refno' AND (group_id='$this->group_id' OR service_code='$this->service_code')  AND (ISNULL(`status`) OR `status`!='deleted');";
				$result = $lab_results->exec_query($sql);
				if($result)
				{
						if($resdata = $result->FetchRow())
								$date =  date("m/d/Y",strtotime(substr($resdata["service_date"], 0, -9)));
				}
				#$this->Rect(12,32,186,24, 5);
				$this->SetDrawColor(0,0,0);

				$this->SetXY(10,24);
				$this->SetFont('Arial','B',8);
				$this->Cell(15,5,"PATIENT: ",0,0,'L');
				$this->SetFont('Arial','',7);
				$this->MultiCell(51,4,$ordername,"0","L","0");

				$this->SetXY(70,24);
				$this->SetFont('Arial','B',8);
				$this->Cell(10,5,"DATE: ",0,0,'L');
				$this->SetFont('Arial','',7);
				$this->Cell(30,5,$date,0,0,'L');

				if($sex=="m" || $sex=="M")
				{
						$sex = "Male";
						$this->gender = "is_male=1";
				}
				else
				{
						$sex = "Female";
						$this->gender = "is_female=1";
				}

				if($age)
						$age = $age;
				else
						$age = "";

				$this->SetXY(10,27);
				$this->SetFont('Arial','B',8);
				$this->Cell(10,5,"AGE: ",0,0,'L');
				$this->SetFont('Arial','',7);
				$this->Cell(30,5,$age,0,0,'L');

				$this->SetXY(40,27);
				$this->SetFont('Arial','B',8);
				$this->Cell(10,5,"SEX: ",0,0,'L');
				$this->SetFont('Arial','',7);
				$this->Cell(30,5,$sex,0,0,'L');

				$encounter_type = $encounter_type;

				if ($encounter_type==1){
						$ward = "EMERGENCY ROOM";
				}elseif ($encounter_type==2){
						$dept = $dept_obj->getDeptAllInfo($current_dept_nr);
						$ward = strtoupper(strtolower(stripslashes($dept['name_formal'])));
				}elseif (($encounter_type==3)||($encounter_type==4)){
						$loc = $ward_obj->getWardInfo($current_ward_nr);
						$ward = strtoupper(strtolower(stripslashes($loc['name'])))." Rm # : ".$current_room_nr;
				}else{
					 $ward = 'WALK-IN';
				}

				$this->SetXY(70,27);
				$this->SetFont('Arial','B',8);
				$this->Cell(55,5,"WARD: ",0,0,'L');
				$this->SetXY(82,27);
				$this->SetFont('Arial','',7);
				$this->MultiCell(40,4,$ward,"0","L","0");

				$sql = "SELECT CONCAT(IF(ISNULL(name_first), '', CONCAT(name_first, ' ')), IF(ISNULL(name_middle), '', CONCAT(name_middle, ' ')), IF(ISNULL(name_last), '', name_last)) as name FROM care_person LEFT JOIN care_personell ON care_personell.pid=care_person.pid WHERE nr='".$request_doctor."'";
				$result = $lab_results->exec_query($sql);
				if($result!=NULL && $resdata = $result->FetchRow())
				{
						$physician = $resdata["name"];
						$this->SetXY(10,30);
						$this->SetFont('Arial','B',8);
						$this->Cell(18,5,"PHYSICIAN: ",0,0,'L');
						$this->SetFont('Arial','',7);
						$this->Cell(42,5,"Dr. ".$physician,0,0,'L');
						$this->SetXY(10,33);
						$this->SetFont('Arial','B',8);
						$this->Cell(10, 5, 'HRN:', 0, 0, 'L');
						$this->SetFont('Arial','',7);
						$this->Cell(15, 5, $this->pid,0,1, 'L');
				}
				else{
						$this->SetXY(10,30);
						$this->SetFont('Arial','B',8);
						$this->Cell(60,5,"PHYSICIAN:",0,0,'L');
						$this->SetXY(10,33);
						$this->SetFont('Arial','B',8);
						$this->Cell(10, 5, 'HRN:', 0, 0, 'L');
						$this->SetFont('Arial', '', 7);
						$this->Cell(15, 5, $this->pid, 0,0, 'L');
				}

				$this->LabResult_body();
	}

	function LabResult_body(){
				global $db;

				$lab_results=new Lab_Results();

				$tb_x = 10;
				$tb_y = 40;
				$tb_height = 120;

				if($this->group_id) {
						$service_name = strtoupper($lab_results->get_group_name($this->group_id));
						$sql = "SELECT is_served FROM seg_lab_servdetails AS sd
								LEFT JOIN seg_lab_result_groupparams AS gp ON gp.service_code = sd.service_code
								WHERE gp.group_id='".$this->group_id."' AND sd.refno='".$this->refno."' AND(ISNULL(status) OR status!='deleted')";
				}
				if($this->service_code) {
						$service_name = strtoupper($lab_results->get_service_name($this->service_code));
						$sql = "SELECT is_served FROM seg_lab_servdetails AS sd
								WHERE sd.service_code='".$this->service_code."' AND sd.refno='".$this->refno."' AND(ISNULL(status) OR status!='deleted')";
				}
				$result = $db->Execute($sql);
				if($result!=NULL && $val = $result->FetchRow())
				{
						if($val["is_served"]=="1")
								$service_name .=" (OFFICIAL READING)";
						else
								$service_name .=" (INITIAL READING)";
				}

				$this->SetFont('Arial','B',8);
				$this->SetXY(10,40);
				$this->Cell(100,5,$service_name,0,0);

				$with_normal = FALSE;
				if($this->group_id)
						$sql = "SELECT * FROM seg_lab_result_params WHERE group_id=$this->group_id
										AND (NOT (ISNULL(SI_lo_normal) AND ISNULL(SI_hi_normal)))";
				if($this->service_code)
						$sql = "SELECT * FROM seg_lab_result_params WHERE service_code=$this->service_code
										AND (NOT (ISNULL(SI_lo_normal) AND ISNULL(SI_hi_normal)))";

				$result = $lab_results->exec_query($sql);
				if($result!=NULL){
						$with_normal = TRUE;
				}
				$this->SetFont('Arial','B',8);

				$this->SetFont('Arial','',7);

				/*if($this->service_code){
						$sql = "SELECT p.*, r.result_value, r.unit, s.name as group_name
										FROM seg_lab_result_params AS p
										LEFT JOIN seg_lab_services AS s ON s.service_code = p.service_code
										LEFT JOIN seg_lab_result AS r ON r.param_id = p.param_id AND r.refno='$this->refno' AND (ISNULL(r.status) OR r.status!='deleted')
										WHERE $this->gender AND s.service_code='$this->service_code' ORDER BY p.order_nr ASC";
				}
				if($this->group_id){
						$sql = "SELECT p.*, r.result_value, r.unit, pg.name as group_name, gp.order_nr as order2, IF(ISNULL(d.refno), 0, 1) AS enabled
										FROM seg_lab_result_groupparams as gp
										LEFT JOIN seg_lab_result_params as p ON p.service_code = gp.service_code
										LEFT JOIN seg_lab_result as r ON p.param_id = r.param_id AND r.refno='$this->refno' AND (ISNULL(r.status) OR r.status!='deleted')
										LEFT JOIN seg_lab_result_paramgroups as pg ON pg.param_group_id = p.param_group_id
										LEFT JOIN seg_lab_servdetails AS d ON d.service_code=p.service_code AND d.refno='$this->refno'
										WHERE gp.group_id=$this->group_id AND $this->gender AND (ISNULL(p.status) OR p.status NOT IN ('deleted'))
										UNION SELECT p.*, r.result_value, r.unit, pg.name as group_name, gp.order_nr as order2, IF(ISNULL(d.refno), 0, 1) AS enabled
										FROM seg_lab_result_groupparams as gp
										LEFT JOIN seg_lab_result_group as g ON g.service_code = gp.service_code
										LEFT JOIN seg_lab_result_params as p ON p.service_code = g.service_code_child
										LEFT JOIN seg_lab_result as r ON p.param_id = r.param_id AND r.refno='$this->refno' AND (ISNULL(r.status) OR r.status!='deleted')
										LEFT JOIN seg_lab_result_paramgroups as pg ON pg.param_group_id = p.param_group_id
										LEFT JOIN seg_lab_servdetails AS d ON (d.service_code=g.service_code OR d.service_code=p.service_code) AND d.refno='$this->refno'
										WHERE gp.group_id=$this->group_id AND $this->gender AND (ISNULL(p.status) OR p.status NOT IN ('deleted'))
										ORDER BY order2, order_nr ASC";
				}*/
				$sql = "SELECT * FROM \n".
									"(SELECT pa.param_id,gp.order_nr AS `group_order`, pa.order_nr AS `param_order`, \n".
									"p.name, p.param_group_id,pg.name AS `group_name`, p.is_numeric, p.is_boolean, p.is_longtext, p.SI_unit, p.SI_lo_normal, \n".
									"p.SI_hi_normal, p.CU_unit, p.CU_lo_normal, p.CU_hi_normal, p.is_female, p.is_male, p.is_time, p.is_multiple_choice, p.is_table, \n".
									"r.result_value, r.unit \n".
									"FROM seg_lab_result_params AS p \n".
									"LEFT JOIN seg_lab_result_param_assignment AS pa ON p.param_id=pa.param_id \n".
									"LEFT JOIN seg_lab_result_groupparams AS gp ON p.group_id=gp.group_id AND pa.service_code=gp.service_code \n".
									"LEFT JOIN seg_lab_result_paramgroups AS pg ON pg.param_group_id=p.param_group_id \n".
									"LEFT JOIN seg_lab_servdetails AS d ON d.service_code=pa.service_code AND d.refno='$this->refno'\n".
									"LEFT JOIN seg_lab_result AS r ON d.refno=r.refno AND r.param_id=pa.param_id AND r.status <> 'deleted' \n".
									"WHERE p.status <> 'deleted' AND p.group_id='$this->group_id' \n".
									" ORDER BY gp.order_nr, pa.order_nr) a \n".
							"GROUP BY a.param_id, a.param_group_id \n".
							"ORDER BY group_order,param_order";
				$result = $lab_results->exec_query($sql);
				if($result!=NULL)
				{
						$numrecs = $result->RecordCount();
						if($this->group_id)
								$sql = "SELECT COUNT(DISTINCT p.param_group_id) as numrecs
										FROM seg_lab_result_groupparams as gp
										LEFT JOIN seg_lab_result_params as p ON p.service_code = gp.service_code
										WHERE gp.group_id=$this->group_id AND $this->gender";
						if($this->service_code)
								$sql = "SELECT COUNT(DISTINCT p.param_id) as numrecs
										FROM seg_lab_result_params as p
										WHERE p.service_code=$this->service_code AND $this->gender";
						$rs = $lab_results->exec_query($sql);
						if($rs!=NULL && $val = $rs->FetchRow())
								$numrecs = $numrecs + $val["numrecs"];

						$tb_y = $tb_y + 5;
						$tb_height = $tb_height - 7;
						$tb_rowheight = $tb_height / $numrecs;
						if($numrecs<=5)
						$tb_rowheight = $tb_rowheight /4;
						else if($numrecs<=10)
						$tb_rowheight = $tb_rowheight /2;
						$tb_x = $tb_x + 3;
						//echo "x=".$tb_x."y=".$tb_y;
						while($result!=NULL && $value = $result->FetchRow()){
								$level = "";
								$str = "";
								$td = "";
								$tx = 0;
								$fld_value = $value["result_value"];
								$unit = $value["unit"];
								if($value["group_name"]!="" && $group_name != $value["group_name"]){
										$group_name = $value["group_name"];
										$this->SetXY($tb_x,$tb_y);
										$this->Cell(50,4,strtoupper($value["group_name"]),0,0,'L');
										$tb_y = $tb_y + $tb_rowheight;
								}
								if($value["group_name"]!=""){
										$td = "     ";
										$tx = 4.5;
								}
								if($value["is_boolean"]=="1"){
										if($fld_value=="on")
										$this->Image("../../images/check2.png",$tb_x-0.5+$tx,$tb_y-1,6,5);
										$this->Rect($tb_x+0.5+$tx,$tb_y+0.5,3,3, 5);
										$this->SetXY($tb_x+4,$tb_y);
										$this->MultiCell(40,3,$td .$value["name"],"0","L","0");
								}
								elseif($value["is_numeric"]=="1"){
										$this->SetXY($tb_x+$str,$tb_y);
										$this->MultiCell(45,3,$td .$value["name"],"0","L","0");
										if($with_normal){
												$this->SetDrawColor(0,0,0);
												$this->SetXY($tb_x+44,$tb_y);
												if($value["SI_lo_normal"]!=""){
														if($value["SI_hi_normal"]!="")
																$str = $value["SI_lo_normal"]."-".$value["SI_hi_normal"]." ".$value["SI_unit"];
														else
																$str = " >=".$value["SI_lo_normal"]." ".$value["SI_unit"];
														$this->Cell(20,5,$str,0,0,'L');
												}
												elseif($value["SI_hi_normal"]!=""){
														$str = " <".$value["SI_hi_normal"]." ".$value["SI_unit"];
														$this->Cell(20,5,$str,0,0,'L');
												}
										}

										if($fld_value!="")
										{
												if($unit==""){
														$this->SetXY($tb_x+75,$tb_y);
														$this->Cell(25,5,$fld_value,0,0,'C');
												}
												else{
														$this->SetXY($tb_x+75,$tb_y);
														$this->Cell(25,5,$fld_value,0,0,'C');
												}
										}
										$this->Line($tb_x+78, $tb_y+3.8, $tb_x+98, $tb_y+3.8);
								}
								else{
										$this->SetXY($tb_x+$str,$tb_y);
										$this->MultiCell(45,3,$td .$value["name"],"0","L","0");
										if($fld_value!=""){
												$this->SetXY($tb_x+50,$tb_y);
												$this->Cell(45,5,$fld_value,0,0,'C');
										}
										$this->Line($tb_x+50, $tb_y+3.8, $tb_x+98, $tb_y+3.8);
								}

								$tb_y = $tb_y + $tb_rowheight;
						}
						if($this->group_id==13){
								$this->Ln(5);
								$x = $this->getX();
								$y = $this->getY();
								$this->Line($x+23, $y-1, $x+113, $y-1);
								$this->Line($x+23, $y-1, $x+23, $y+19);
								$this->Line($x+53, $y-1, $x+53, $y+19);
								$this->Line($x+113, $y-1, $x+113, $y+19);
								$this->Cell(23, 2, "", "0", 0, "C");
								$this->SetFont('Arial','B',8);
								$this->Cell(30, 2, "SCORE", "0", 0, "C");
								$this->Cell(60, 2, "INTERPRETATION", "0", 0, "C");
								$this->SetFont('Arial','',7);
								$this->Ln(4);
								$x = $this->getX();
								$y = $this->getY();
								$this->Line($x+23, $y-1, $x+113, $y-1);
								$this->Cell(23, 2, "", "0", 0, "C");
								$this->Cell(30, 2, "< 2", "0", 0, "C");
								$this->Cell(60, 2, "NEGATIVE", "0", 0, "C");
								$this->Ln(4);
								$x = $this->getX();
								$y = $this->getY();
								$this->Line($x+23, $y-1, $x+113, $y-1);
								$this->Cell(23, 2, "", "0", 0, "C");
								$this->Cell(30, 2, "3", "0", 0, "C");
								$this->Cell(60, 2, "BORDERLINE / INCONCLUSIVE", "0", 0, "C");
								$this->Ln(4);
								$x = $this->getX();
								$y = $this->getY();
								$this->Line($x+23, $y-1, $x+113, $y-1);
								$this->Cell(23, 2, "", "0", 0, "C");
								$this->Cell(30, 2, "4", "0", 0, "C");
								$this->Cell(60, 2, "WEAK POSITIVE", "0", 0, "C");
								$this->Ln(4);
								$x = $this->getX();
								$y = $this->getY();
								$this->Line($x+23, $y-1, $x+113, $y-1);
								$this->Cell(23, 2, "", "0", 0, "C");
								$this->Cell(30, 2, "6-10", "0", 0, "C");
								$this->Cell(60, 2, "POSITIVE", "0", 0, "C");
								$this->Ln(4);
								$x = $this->getX();
								$y = $this->getY();
								$this->Line($x+23, $y-1, $x+113, $y-1);
						}
				}
		}

		function Footer()
		{
				$lab_results = new Lab_Results();
				$sql = "Select create_id, create_dt, service_date, med_tech_pid, pathologist_pid FROM seg_lab_resultdata WHERE refno='$this->refno' AND group_id='$this->group_id'  AND (ISNULL(`status`) OR `status`!='deleted');";
				$result = $lab_results->exec_query($sql);
				if($result)
				{
					 if($resdata = $result->FetchRow())
					 {
							 $encoder = $resdata["create_id"];
							 $encode_date = date("m/d/Y",strtotime($resdata["create_dt"]));
							 $pathologist = $resdata["pathologist_pid"];
							 $med_tech_pid = $resdata["med_tech_pid"];
							 $sql = "SELECT CONCAT(IF(ISNULL(name_first), '', CONCAT(name_first, ' ')), IF(ISNULL(name_middle), '', name_middle), '. ', IF(ISNULL(name_last), '', name_last), ', ', IF(ISNULL(title), '', title)) as name from care_person WHERE care_person.pid = '".$pathologist."'";
							 $result = $lab_results->exec_query($sql);
							 if($result!=NULL && $x = $result->FetchRow())
									 $pathologist=$x["name"];
							 else
										$pathologist="";
							 $sql = "SELECT fn_get_pid_name('$med_tech_pid') as name";
							 $result = $lab_results->exec_query($sql);
							 if($result!=NULL && $resdata = $result->FetchRow())
									 $med_tech = $resdata["name"];
							 else
									 $med_tech = "";
					 }
				}
				$this->SetDrawColor(0,0,0);
				$this->SetFont('Arial','',8.5);

				if($this->group_id=="14"){
						$this->SetXY(38,200);
						$this->MultiCell(60,3,strtoupper($med_tech),"B","C","0");
						#$this->Line(35,201,99,201);
						$this->SetXY(35,203);
						$this->Cell(60,5,strtoupper("Microscopist"),0,0,'C');
				}
				else{
						$this->SetXY(5,200);
						$this->MultiCell(47,3,strtoupper($med_tech),"B","C","0");
						#$this->Line(15,201,59,201);
						$this->SetXY(5,203);
						$this->Cell(47,5,strtoupper("Medical Technologist"),0,0,"C");

						$this->SetXY(57,200);
						$this->MultiCell(65,3,strtoupper($pathologist),"B","C","0");
						#$this->Line(67,201,123,201);
						$this->SetXY(57,203);
						$this->Cell(65,5,strtoupper("Pathologist"),0,0,"C");
				}

				$this->SetFont("Arial", "", "6");
				$this->SetXY(80,210);
				$this->Cell(50,5,"Encoded by: ".$encoder,0,0,'L');
				$this->SetXY(80,215);
				$this->Cell(50,5,"Encoded on: ".$encode_date,0,0,'L');
		}
}
$report = new RepGen_LabResults();
$report->AliasNbPages();
$report->Report();
?>
