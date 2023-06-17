<?php
//created by ngel, august 19, 2010
//daily report for industrial clinic transaction
//same with OPD Daily Transaction Report
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_department.php');

#edited by VAN 09-01-2010
# change the hard-coded Header of the Report
# change the query of the report
# change the orientation and alignment of report
# change the date filter to Y-m-d

class ICTransaction_Daily_Report extends RepGen {

	var $date_from;
	var $date_to;
	var $time_from;
	var $time_to;
	var $total_width=0;

	function ICTransaction_Daily_Report($dateFrm, $dateTo, $timeFrm, $timeto)
	{
		global $db;
		$this->RepGen("INDUSTRIAL CLINIC DAILY TRANSACTION REPORT");
		$this->Headers = array(
				'',
				'HRN',
				'Fullname',
				'Date',
				'Age',
				'Sex',
				'Address',
				'Purpose',
				'Company'
			);
		$this->colored = TRUE;
		$this->ColumnWidth = array(10,20,30,18,10,10,45,30,40);
		$this->RowHeight = 5;
		$this->TextHeight = 5;
		$this->TextPadding = 0.2;
		$this->LEFTMARGIN = 2;
		$this->Alignment = array('L','C','L','L','C','C','L','L','L');

		$this->PageOrientation = "P";
		#$this->SetMargins(1,1,1);
		$this->total_width = array_sum($this->ColumnWidth);
		$this->NoWrap=FALSE;

		$this->date_from = date('Y-m-d',strtotime($dateFrm));
		$this->date_to = date('Y-m-d', strtotime($dateTo));
		$this->time_from = $timeFrm;
		$this->time_to = $timeto;

		if ($this->colored) $this->SetDrawColor(0xDD);
	}

	function Header()
	{
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
		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',40,8,20);
		$this->SetFont("Arial","I","9");
		$total_w = 100;
		$this->Cell(50,4);
		$this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
		$this->Cell(50,4);
		$this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
		$this->Ln(2);
		$this->SetFont("Arial","B","10");
		$this->Cell(50,4);
		$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell(50,4);
		$this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
		$this->Ln(4);
		$this->SetFont('Arial','B',12);
		$this->Cell(50,5);

		$this->Cell($total_w,4,strtoupper('Industrial Clinic Daily Transactions'),0,1,'C');
		$this->SetFont('Arial','B',9);
		$this->Ln(4);
		$this->Cell(50,5);
		#$this->Cell(0,10,date("M d, Y",strtotime($this->date_from))."  ".date("h:i A",strtotime($this->time_from))." - ".date("M d, Y",strtotime($this->date_to))."  ".date("h:i A",strtotime($this->time_to)),0,1,'C');
		if ($this->date_from==$this->date_to)
			$text = "For ".date("F j, Y",strtotime($this->date_from));
		else
			$text = "From ".date("F j, Y",strtotime($this->date_from))." To ".date("F j, Y",strtotime($this->date_to));

		$this->Cell($total_w,4,$text,0,1,'C');

		$this->Ln(4);


		$this->SetFont('Arial','B',11);
		$this->Cell($this->ColumnWidth[0],$this->RowHeight,$this->Headers[0],0,0,'L',1);
		$this->Cell($this->ColumnWidth[1],$this->RowHeight,$this->Headers[1],0,0,'L',1);
		$this->Cell($this->ColumnWidth[2],$this->RowHeight,$this->Headers[2],0,0,'L',1);
		$this->Cell($this->ColumnWidth[3],$this->RowHeight,$this->Headers[3],0,0,'L',1);
		$this->Cell($this->ColumnWidth[4],$this->RowHeight,$this->Headers[4],0,0,'L',1);
		$this->Cell($this->ColumnWidth[5],$this->RowHeight,$this->Headers[5],0,0,'L',1);
		$this->Cell($this->ColumnWidth[6],$this->RowHeight,$this->Headers[6],0,0,'L',1);
		$this->Cell($this->ColumnWidth[7],$this->RowHeight,$this->Headers[7],0,0,'L',1);
		$this->Cell($this->ColumnWidth[8],$this->RowHeight,$this->Headers[8],0,0,'L',1);
		$this->Ln();
	}

	function BeforeData()
	{
		$this->FONTSIZE = 9;
		if ($this->colored) {
				$this->DrawColor = array(255,255,255);
		}
	}

	/*function BeforeCellRender()
	{
			$this->FONTSIZE = 8;
			if ($this->colored) {
					if (($this->RENDERPAGEROWNUM%2)>0)
							$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
					else
							$this->RENDERCELL->FillColor=array(255,255,255);
			}
	}*/

	function AfterData()
	{
			global $db;
			if (!$this->_count) {
					$this->SetFont('Arial','B',9);
					$this->SetFillColor(255);
					$this->SetTextColor(0);
					$this->Cell($this->total_width, $this->RowHeight, "No records found for this report...", 0, 1, 'L', 1);
			}

			$cols = array();
	}

	function FetchData()
	{
		global $db;
		if (empty($this->date_to)) $end_date="NOW()";
		else $end_date=$this->date_to;
		if (empty($this->date_from)) $start_date="NOW()";
		else
		$start_date=$this->date_from;

		$grp_sql = " GROUP BY ce.pid ";
		$order_sql = " ORDER BY encounter_date ";
		#edited by VAN 09-01-2010
		$sql =
			"SELECT distinct cp.pid, cd.name_formal,
				CONCAT(IFNULL(name_last,''),', ',IFNULL(name_first,''),' ',IFNULL(name_middle,'')) AS fullname,
				CAST(encounter_date as DATE) as consult_date,
				CAST(encounter_date AS TIME) AS consult_time,
				fn_get_age(CAST(encounter_date AS date), CAST(date_birth AS DATE)) AS age,
				UPPER(sex) AS p_sex, addr_str, cd.id,
				cp.street_name,	sb.brgy_name, sm.mun_name, sm.zipcode, sp.prov_name, ce.encounter_nr,
				fn_get_icd_encounter(ce.encounter_nr) AS icd_code,
				fn_get_personell_name(fn_get_icd_dr_encounter(ce.encounter_nr)) AS diagnosing_clinician,
				sip.name as purpose_exam,
				sic.name as company_name

			FROM care_encounter AS ce
				INNER JOIN care_person AS cp ON ce.pid = cp.pid
				INNER JOIN seg_industrial_transaction as sit on sit.encounter_nr=ce.encounter_nr
				INNER JOIN seg_industrial_purpose as sip on sit.purpose_exam=sip.id
				LEFT JOIN seg_industrial_company as sic on sic.company_id=sit.agency_id
				LEFT JOIN care_department AS cd ON ce.current_dept_nr = cd.nr
				LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=cp.brgy_nr
				LEFT JOIN seg_municity AS sm ON sm.mun_nr=cp.mun_nr
				LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
				LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
			WHERE DATE(ce.encounter_date) BETWEEN '$start_date' AND '$end_date'
				AND ce.encounter_type IN (6)
				AND ce.status NOT IN ('deleted','hidden','inactive','void')";


		$sql .= " $grp_sql $order_sql";


		$result=$db->Execute($sql);
		$this->_count = $result->RecordCount();

		if ($result!==FALSE) {
			$this->Data=array();
			$i=1;

			 while ($row=$result->FetchRow())
			 {
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
						$prov_name = "";
					}
				}else
					$prov_name = "";

				$addr = trim($street_name).trim($brgy_name).trim($mun_name).trim($prov_name);

									if (stristr($row['age'],'years')){
											$age = substr($row['age'],0,-5);
											$age = floor($age).' y';
									}elseif (stristr($row['age'],'year')){
											$age = substr($row['age'],0,-4);
											$age = floor($age).' y';
									}elseif (stristr($row['age'],'months')){
											$age = substr($row['age'],0,-6);
											$age = floor($age).' m';
									}elseif (stristr($row['age'],'month')){
											$age = substr($row['age'],0,-5);
											$age = floor($age).' m';
									}elseif (stristr($row['age'],'days')){
											$age = substr($row['age'],0,-4);

											if ($age>30){
													$age = $age/30;
													$label = 'm';
											}else
													$label = 'd';

											$age = floor($age).' '.$label;
									}elseif (stristr($row['age'],'day')){
											$age = substr($row['age'],0,-3);
											$age = floor($age).' d';
									}

					$this->Data[]=array(
						$i,
						$row['pid'],
						strtoupper(trim($row['fullname'])),
						date("m/d/Y",strtotime($row['consult_date'])).' '.date("h:i A",strtotime($row['consult_time'])),
						$age,
						strtoupper($row['p_sex']),
						strtoupper(trim($addr)),
						strtoupper($row['purpose_exam']),
						strtoupper($row['company_name'])
					);
					$i++;
			}
		}
		else {
			echo "error:".$db->ErrorMsg();
		}
	}
}

$rep = new ICTransaction_Daily_Report($_GET["date_from"], $_GET["date_to"], $_GET["time_from"], $_GET["time_to"]);
$rep->SetAutoPageBreak(false);#added by art 03/18/2014
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>
