<?php
/*
#created by ngel, august 24, 2010
#Consolidated Print out of Employee by individual and list of employees in the company
*/
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/repgen/repgen.inc.php');

class ICTransaction_Daily_Report extends RepGen {

	var $date_from;
	var $date_to;
	var $total_width=0;
	var $sql;
	var $employee;
	var $pid;
	var $company_id;
	var $print_type; 	# 1=per company ; 2=individual
	var $list_employee;


	function ICTransaction_Daily_Report($pid, $company_id, $dateFrm, $dateTo)
	{
		global $db;
		$this->RepGen("INDUSTRIAL CLINIC DAILY TRANSACTION REPORT");
		$this->Headers = array(
				'',
				'Patient ID',
				'Fullname',
				'Time',
				'Age',
				'Gender',
				'Address',
				'Purpose',
				'Company'
			);

		$this->Emp=array(
					'name',
					'lab',
					'radio',
					'pharma',
					'misc',
					'total_cash',
					'total_charge'
				);


		$this->colored = TRUE;
		$this->ColumnWidth = array(5,30,50,20,20,20,50,30,50);
		$this->RowHeight = 6;
		$this->Alignment = array('L','C','L','C','C','C','L','L','L');
		$this->PageOrientation = "";
		$this->SetMargins(1,1,1);
		$this->total_width = array_sum($this->ColumnWidth);
		$this->NoWrap=FALSE;

		$this->pid=$pid;
		$this->company_id=$company_id;
		if(empty($this->pid))
			$this->print_type=1;
		else
			$this->print_type=2;

		$this->date_from = date('M-d-Y',strtotime($dateFrm));
		$this->date_to = date('M-d-Y', strtotime($dateTo));
		if (empty($dateTo))
			$this->date_to=date('M-d-Y');

		if (empty($dateFrm))
			$this->date_from=date('M-d-Y');


		if ($this->colored) $this->SetDrawColor(0xDD);
	}

	#function Header
	function Header()
	{
		global $db;
		$this->Ln();
		$this->SetTextColor(0);
		$row=5;


		$this->SetY(1);
		$this->Image('../../gui/img/logos/dmc_logo.jpg',70,5,15);
		$this->SetFont("Arial","I","9");
		$total_w = 0;
		$this->Cell(0,4,'Republic of the Philippines',0,1,'C');
		$this->Cell(0,4,'DEPARTMENT OF HEALTH',0,1,'C');
		$this->Ln(2);
		$this->SetFont("Arial","B","10");
		$this->Cell(0,4,'DAVAO MEDICAL CENTER',0,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell(0,4,'Bajada, Davao City',0,1,'C');
		$this->SetFont('Arial','B',12);
		$this->Cell(0,15,strtoupper('Consolidated Print-out'),0,1,'C');
		$this->SetFont('Arial','',9);
		$this->date_from = date('M-d-Y',strtotime($this->date_from ));
		$this->date_to = date('M-d-Y', strtotime($this->date_to));
		$this->Cell(0,4,"From: ".$this->date_from."    To: ".$this->date_to,0,1,'C');


		$this->Ln();

		$sql="select * from seg_industrial_company as sic
									where sic.company_id='".$this->company_id."'";
					$result=$db->Execute($sql);
					if($row=$result->FetchRow()){
						$company_name=$row['name'];
					}
					$this->SetFont('Arial','B',10);
					$this->SetXY(20,47);
					$this->Cell(0, 4,"COMPANY :  ", 0, 0, 'L');
					$this->SetXY(43,47);
					$this->SetFont('Arial','',10);
					$this->Cell(0, 4,strtoupper($company_name), 0, 0, 'L');


		$this->Ln();
	}#end


	function BeforeData()
	{
		$this->FONTSIZE = 9;
		if ($this->colored) {
				$this->DrawColor = array(255,255,255);
		}
	}

	#for displaying the data
	function AfterData()
	{

			if (!$this->_count) {
					$this->SetFont('Arial','B',9);
					$this->SetFillColor(255);
					$this->SetTextColor(0);
					$this->SetXY(20,55);
					$this->Cell(0, 10, "No records found for this report...", 0, 1, 'L', 1);
			}
			else{
					$this->SetFont('Arial','B',9);
					$i=0;
					$space=25;


					#for single employee
					$startY=55;

					if($this->print_type==2){
							$this->SetXY(20,$startY);
							$this->Cell(0, 4,"EMPLOYEE:  ", 0, 0, 'L', 1);
							$this->SetXY(43,$startY);
							$this->SetFont('Arial','',9);
							$this->Cell(0, 4,strtoupper($this->employee[$i]["name"]), 0, 0, 'L', 1);
							$this->SetFont('Arial','',9);
							$sum_total_cash=0;
							$sum_total_charge=0;
							while($i<$this->_count){

								$this->Ln();
								$y=60;
								$x1=100;
								$x2=150;
								switch($this->employee[$i]["attrib"]){

											case "LAB":
																	$this->SetXY(30,$y+($i*4));
																	$this->SetFont('Arial','B',9);
																	$this->Cell(0, 4,"LAB: ", 0, 0, 'L', 1);
																	$this->SetFont('Arial','',9);
																	$this->SetX($x1);
																	$this->Cell(10, 4,"Cash: ", 0, 0, 'L', 1);
																	$this->SetX($x1+$space);
																	$this->Cell(10, 4,number_format($this->employee[$i]["total_cash"],2,".", ","), 0, 0, 'R', 1);
																	$sum_total_cash=$sum_total_cash+$this->employee[$i]["total_cash"];
																	$this->SetX($x2);
																	$this->Cell(10,4,"Charge: ", 0, 0, 'L', 1);
																	$this->SetX($x2+$space);
																	$this->Cell(10, 4,number_format($this->employee[$i]["total_charge"],2,".", ","), 0, 0, 'R', 1);
																	$sum_total_charge=$sum_total_charge+$this->employee[$i]["total_charge"];
																	break;
																case "RADIO":
																	$this->SetXY(30,$y+($i*4));
																	$this->SetFont('Arial','B',9);
																	$this->Cell(0, 4,"RADIO: ", 0, 0, 'L', 1);
																	$this->SetFont('Arial','',9);
																	$this->SetX($x1);
																	$this->Cell(10, 4,"Cash: ", 0, 0, 'L', 1);
																	$this->SetX($x1+$space);
																	$this->Cell(10, 4,number_format($this->employee[$i]["total_cash"],2,".", ","), 0, 0, 'R', 1);
																	$sum_total_cash=$sum_total_cash+$this->employee[$i]["total_cash"];
																	$this->SetX($x2);
																	$this->Cell(10,4,"Charge: ", 0, 0, 'L', 1);
																	$this->SetX($x2+$space);
																	$this->Cell(10, 4,number_format($this->employee[$i]["total_charge"],2,".", ","), 0, 0, 'R', 1);
																	$sum_total_charge=$sum_total_charge+$this->employee[$i]["total_charge"];
																	break;
																case "PHARMA":
																	$this->SetXY(30,$y+($i*4));
																	$this->SetFont('Arial','B',9);
																	$this->Cell(0, 4,"PHARMA: ", 0, 0, 'L', 1);
																	$this->SetFont('Arial','',9);
																	$this->SetX($x1);
																	$this->Cell(10, 4,"Cash: ", 0, 0, 'L', 1);
																	$this->SetX($x1+$space);
																	$this->Cell(10, 4,number_format($this->employee[$i]["total_cash"],2,".", ","), 0, 0, 'R', 1);
																	$sum_total_cash=$sum_total_cash+$this->employee[$i]["total_cash"];
																	$this->SetX($x2);
																	$this->Cell(10,4,"Charge: ", 0, 0, 'L', 1);
																	$this->SetX($x2+$space);
																	$this->Cell(10, 4,number_format($this->employee[$i]["total_charge"],2,".", ","), 0, 0, 'R', 1);
																	$sum_total_charge=$sum_total_charge+$this->employee[$i]["total_charge"];
																	$this->Ln();
																	break;
																case "MISC":
																	$this->SetXY(30,$y+($i*4));
																	$this->SetFont('Arial','B',9);
																	$this->Cell(0, 3,"MISC: ", 0, 0, 'L', 1);
																	$this->SetFont('Arial','',9);
																	$this->SetX($x1);
																	$this->Cell(10, 4,"Cash: ", 0, 0, 'L', 1);
																	$this->SetX($x1+$space);
																	$this->Cell(10, 4,number_format($this->employee[$i]["total_cash"],2,".", ","), 0, 0, 'R', 1);
																	$sum_total_cash=$sum_total_cash+$this->employee[$i]["total_cash"];
																	$this->SetX($x2);
																	$this->Cell(10,4,"Charge: ", 0, 0, 'L', 1);
																	$this->SetX($x2+$space);
																	$this->Cell(10, 4,number_format($this->employee[$i]["total_charge"],2,".", ","), 0, 0, 'R', 1);
																	$sum_total_charge=$sum_total_charge+$this->employee[$i]["total_charge"];
																	$this->Ln();
																	break;
																default:
																	break;

								}
								$i++;
							}
							$this->SetXY($x1,$y+15);
							$this->Cell(10,3,"Total: ", 0, 0, 'L', 1);
							$this->SetXY($x1+$space,$y+15);
							$this->Cell(10,3,number_format($sum_total_cash,2,".", ","), 0, 0, 'R', 1);
							$this->SetXY($x2,$y+15);
							$this->Cell(10, 3,"Total: ", 0, 0, 'L', 1);
							$this->SetXY($x2+$space,$y+15);
							$this->Cell(10, 3,number_format($sum_total_charge,2,".", ","), 0, 0, 'R', 1);

					}#end for single employee

					#for employees in the company
					elseif($this->print_type==1){
							$index=0;
							$iRow=0;
							$final_total_cash=0;
							$final_total_charge=0;
							$emp_page_counter=0;
							$emp_page_counter_max=7;
							$samp=0;
							$index=0;
							while($index<count($this->list_employee)){
											$emp_page_counter++;
											if($emp_page_counter>$emp_page_counter_max){
												$emp_page_counter=0;
												$iRow=0;
												$this->AddPage();
											}
											$i=0;
											$this->employee=$this->list_employee[$index];
											if(!empty($this->list_employee[$index][$i]["name"])){

											if($emp_page_counter==0){
												$startY=55+($iRow*4)+($iRow*4);
											}
											else{
												$startY=55+($iRow*4)+($iRow * 20);
											}

											$this->SetXY(20,$startY);
											$this->SetFont('Arial','B',9);
											$this->Cell(0, 4,"EMPLOYEE:  ", 0, 0, 'L', 1);
											$this->SetXY(43,$startY);
											$this->SetFont('Arial','',9);
											$this->Cell(0, 4,$this->list_employee[$index][$i]["name"], 0, 0, 'L', 1);
											$this->SetFont('Arial','',9);
											$sum_total_cash=0;
											$sum_total_charge=0;
											while($i<count($this->employee)){
													$this->Ln();
													$y=$startY+5;
													$x1=100;
													$x2=150;
													switch($this->employee[$i]["attrib"]){
																case "LAB":
																	$this->SetXY(30,$y+($i*4));
																	$this->SetFont('Arial','B',9);
																	$this->Cell(0, 4,"LAB: ", 0, 0, 'L', 1);
																	$this->SetFont('Arial','',9);
																	$this->SetX($x1);
																	$this->Cell(10, 4,"Cash: ", 0, 0, 'L', 1);
																	$this->SetX($x1+$space);
																	$this->Cell(10, 4,number_format($this->employee[$i]["total_cash"],2,".", ","), 0, 0, 'R', 1);
																	$sum_total_cash=$sum_total_cash+$this->employee[$i]["total_cash"];
																	$this->SetX($x2);
																	$this->Cell(10,4,"Charge: ", 0, 0, 'L', 1);
																	$this->SetX($x2+$space);
																	$this->Cell(10, 4,number_format($this->employee[$i]["total_charge"],2,".", ","), 0, 0, 'R', 1);
																	$sum_total_charge=$sum_total_charge+$this->employee[$i]["total_charge"];
																	break;
																case "RADIO":
																	$this->SetXY(30,$y+($i*4));
																	$this->SetFont('Arial','B',9);
																	$this->Cell(0, 4,"RADIO: ", 0, 0, 'L', 1);
																	$this->SetFont('Arial','',9);
																	$this->SetX($x1);
																	$this->Cell(10, 4,"Cash: ", 0, 0, 'L', 1);
																	$this->SetX($x1+$space);
																	$this->Cell(10, 4,number_format($this->employee[$i]["total_cash"],2,".", ","), 0, 0, 'R', 1);
																	$sum_total_cash=$sum_total_cash+$this->employee[$i]["total_cash"];
																	$this->SetX($x2);
																	$this->Cell(10,4,"Charge: ", 0, 0, 'L', 1);
																	$this->SetX($x2+$space);
																	$this->Cell(10, 4,number_format($this->employee[$i]["total_charge"],2,".", ","), 0, 0, 'R', 1);
																	$sum_total_charge=$sum_total_charge+$this->employee[$i]["total_charge"];
																	break;
																case "PHARMA":
																	$this->SetXY(30,$y+($i*4));
																	$this->SetFont('Arial','B',9);
																	$this->Cell(0, 4,"PHARMA: ", 0, 0, 'L', 1);
																	$this->SetFont('Arial','',9);
																	$this->SetX($x1);
																	$this->Cell(10, 4,"Cash: ", 0, 0, 'L', 1);
																	$this->SetX($x1+$space);
																	$this->Cell(10, 4,number_format($this->employee[$i]["total_cash"],2,".", ","), 0, 0, 'R', 1);
																	$sum_total_cash=$sum_total_cash+$this->employee[$i]["total_cash"];
																	$this->SetX($x2);
																	$this->Cell(10,4,"Charge: ", 0, 0, 'L', 1);
																	$this->SetX($x2+$space);
																	$this->Cell(10, 4,number_format($this->employee[$i]["total_charge"],2,".", ","), 0, 0, 'R', 1);
																	$sum_total_charge=$sum_total_charge+$this->employee[$i]["total_charge"];
																	$this->Ln();
																	break;
																case "MISC":
																	$this->SetXY(30,$y+($i*4));
																	$this->SetFont('Arial','B',9);
																	$this->Cell(0, 3,"MISC: ", 0, 0, 'L', 1);
																	$this->SetFont('Arial','',9);
																	$this->SetX($x1);
																	$this->Cell(10, 4,"Cash: ", 0, 0, 'L', 1);
																	$this->SetX($x1+$space);
																	$this->Cell(10, 4,number_format($this->employee[$i]["total_cash"],2,".", ","), 0, 0, 'R', 1);
																	$sum_total_cash=$sum_total_cash+$this->employee[$i]["total_cash"];
																	$this->SetX($x2);
																	$this->Cell(10,4,"Charge: ", 0, 0, 'L', 1);
																	$this->SetX($x2+$space);
																	$this->Cell(10, 4,number_format($this->employee[$i]["total_charge"],2,".", ","), 0, 0, 'R', 1);
																	$sum_total_charge=$sum_total_charge+$this->employee[$i]["total_charge"];
																	$this->Ln();
																	break;
																default:
																	break;

													}
													$i++;
												}
												$this->SetXY($x1,$y+15);
												$this->Cell(10, 3,"Total: ", 0, 0, 'L', 1);
												$this->SetXY($x1+$space,$y+15);
												$this->Cell(10, 3,number_format($sum_total_cash,2,".", ","), 0, 0, 'R', 1);
												$final_total_cash=$final_total_cash+$sum_total_cash;
												$this->SetXY($x2,$y+15);
												$this->Cell(10, 3,"Total: ", 0, 0, 'L', 1);
												$this->SetXY($x2+$space,$y+15);
												$this->Cell(10, 3,number_format($sum_total_charge,2,".", ","), 0, 0, 'R', 1);
												$final_total_charge=$final_total_charge+$sum_total_charge;

												$iRow++;
											}
											$index++;


							}

							$totalY=255;
							$this->SetXY($x1-50,$totalY);
							$this->Cell(0, 3,"Final : ", 0, 0, 'L', 1);

							$this->SetXY($x1-10,$totalY);
							$this->Cell(10, 3,"Total Cash:   ".number_format($final_total_cash,2,".", ","), 0, 0, 'L', 1);
							$this->SetXY($x2-10,$totalY);
							$this->Cell(10, 3,"Total Charge :   ".number_format($final_total_charge,2,".", ","), 0, 0, 'L', 1);
					}

			}

			$cols = array();
	}#end

	#Fetch Data Employee given id
	function FetchData($pid)
	{
		global $db;

		 $this->date_from = date('Y-m-d',strtotime($this->date_from ));
		 $this->date_to = date('Y-m-d', strtotime($this->date_to));
		 $this->pid=$pid;
					$this->sql="
											select sice.pid,
													sice.company_id,
													sice.employee_id,
													sice.position,
													sice.job_status,
													emp.*
													from seg_industrial_comp_emp as sice
											inner join
												(

														select
															sls.pid,
															coalesce(fn_get_person_name(cp.pid),'') as person_name,
															'LAB' as attrib,
															SUM(CASE WHEN (sls.is_cash=1) THEN (slsd.price_cash*slsd.quantity) ELSE 0 END) AS total_cash ,
															SUM(CASE WHEN (sls.is_cash=0) THEN (slsd.price_cash*slsd.quantity) ELSE 0 END) AS total_charge,
															coalesce(sum(slsd.price_cash*slsd.quantity),'') as totalcost
														 from care_person as cp
																	 inner join seg_lab_serv sls on sls.pid=cp.pid
																	 inner join seg_lab_servdetails slsd  on slsd.refno=sls.refno
														where cp.pid='".$this->pid."'
														and sls.serv_dt BETWEEN '".$this->date_from."' AND '".$this->date_to."'
														GROUP BY sls.pid
														union all

														select
															srs.pid,
															coalesce(fn_get_person_name(cp.pid),'') as person_name,
															'RADIO' as attrib,
															SUM(CASE WHEN (srs.is_cash=1) THEN (cttr.price_cash) ELSE 0 END) AS total_cash ,
															SUM(CASE WHEN (srs.is_cash=0) THEN (cttr.price_cash) ELSE 0 END) AS total_charge ,
															coalesce(sum(cttr.price_cash),'') as totalcost
														 from care_person as cp
															inner join seg_radio_serv as srs on srs.pid=cp.pid
															inner join care_test_request_radio as cttr on cttr.refno=srs.refno
														where cp.pid='".$this->pid."' and
														srs.request_date BETWEEN '".$this->date_from."' AND '".$this->date_to."'
														GROUP BY srs.pid

														union all

														select
															spo.pid,
															coalesce(fn_get_person_name(cp.pid),'') as person_name,
															'PHARMA' as attrib,
															SUM(CASE WHEN (spo.is_cash=1) THEN (spoi.pricecash * spoi.quantity) ELSE 0 END) AS total_cash ,
															SUM(CASE WHEN (spo.is_cash=0) THEN (spoi.pricecash * spoi.quantity) ELSE 0 END) AS total_charge ,
															coalesce(sum(spoi.pricecash * spoi.quantity),'') as totalcost
														 from care_person as cp
															inner join seg_pharma_orders as spo on spo.pid=cp.pid
															inner join seg_pharma_order_items as spoi on spoi.refno=spo.refno
														where cp.pid='".$this->pid."'
														and spo.orderdate BETWEEN '".$this->date_from."' AND '".$this->date_to."'
														GROUP BY spo.pid
														union all


														select
															sit.pid,
															coalesce(fn_get_person_name(sit.pid),'') as person_name,
															'MISC' as attrib,
															SUM(CASE WHEN (sms.is_cash=1) THEN (smsd.chrg_amnt*smsd.quantity) ELSE 0 END) AS total_cash ,
															SUM(CASE WHEN (sms.is_cash=0) THEN (smsd.chrg_amnt*smsd.quantity) ELSE 0 END) AS total_charge ,
															coalesce(sum(smsd.chrg_amnt*smsd.quantity),'') as totalcost
															from seg_misc_service as sms
															inner join seg_industrial_transaction as sit on sit.encounter_nr=sms.encounter_nr
															inner join seg_misc_service_details as smsd on smsd.refno=sms.refno
														where sit.pid='".$this->pid."'
														and sms.chrge_dte BETWEEN '".$this->date_from."' AND '".$this->date_to."'
														group by  sit.pid
												) as emp on emp.pid=sice.pid and sice.company_id='".$this->company_id."' ;
							 ";

				 $result=$db->Execute($this->sql);
				 if($result){
					 $this->_count=$result->RecordCount();
					 $i=0;
					 while($row=$result->FetchRow()){
						 $employee[$i]=array(
																	'name'=>$row['person_name'],
																	'attrib'=>$row['attrib'],
																	'total_cash'=>$row['total_cash'],
																	'total_charge'=>$row['total_charge']
																	);
							$i++;
					 }
					 return $employee;
				 }else return false;
	}#end

	function PrepareEmployee($employee){
		$this->employee=$employee;
	}#end

	#list of employees
	function PrepareListEmployee($list_employee){
		$this->list_employee=$list_employee;
		$this->_count=count($list_employee);
	}#end

	#Get Employees in the Company
	function GetEmployees(){
		global $db;
		$this->sql="
						select sice.pid,
									fn_get_person_lastname_first(sice.pid) as full_name
									from seg_industrial_company as sic
									inner join seg_industrial_comp_emp as sice on sic.company_id=sice.company_id
								where sic.company_id='".$this->company_id."' and sice.status!='deleted' ";
		$result=$db->Execute($this->sql);
		$rst=array();
		$i=0;
		if($result){
			while($row=$result->FetchRow()){
				$rst[$i]=$row;
				$i++;
			}
			return $rst;
		}else return false;
	}#end


	#Page footer
	function Footer()
	{
			$this->SetY(-15);
			$this->SetFont('Arial','I',8);
			$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}#end




}


$rep = new ICTransaction_Daily_Report($_GET['pid'], $_GET['company_id'], $_GET["date_from"], $_GET["date_to"]);
$rep->AliasNbPages();
#if single employee
if(!empty($_GET['pid'])){
	$employee=$rep->FetchData($_GET['pid']);
	$rep->PrepareEmployee($employee);
}
#for list of employees
else{

	$employee=$rep->FetchData();
	$rst=$rep->GetEmployees();
	$i=0;
$list_employee=array();
	while($i<count($rst)){
		$pid=$rst[$i]["pid"];
		$arr=$rep->FetchData($pid);
		if(!is_null($arr))
		$list_employee[$i]=$arr;
		$i++;
	}
	$rep->PrepareListEmployee($list_employee);
}

$rep->Report();
?>
