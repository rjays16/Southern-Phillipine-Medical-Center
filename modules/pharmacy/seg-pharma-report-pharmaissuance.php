<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_product.php');
include($root_path."include/care_api_classes/class_order.php");

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

		class RepGen_Pharma_DailyIssuance extends RepGen {

		 var $area;
		 var $date_from;
		 var $date_to;

		function RepGen_Pharma_DailyIssuance ($area, $datefrom, $dateto)
		{
				global $db;
				$this->RepGen("PHARMACY ISSUANCE REPORT");
				$this->Headers = array(
						'Serve Date', 'HRN', 'Patient Name',
						'Medicine Name', 'Qty', 'Location'
				);
				$this->colored = TRUE;
				$this->ColumnWidth = array(32,14,50,40,10,55);
				#$this->ColumnWidth = array(10,10,10,10,10);
				$this->RowHeight = 4;
				$this->Alignment = array('L','L','L','L','C','L');
				$this->PageOrientation = "P";
				if ($datefrom) $this->date_from=date("Y-m-d",strtotime($datefrom));
				else $this->date_from=date("Y-m-d");
				if ($dateto) $this->date_to=date("Y-m-d",strtotime($dateto));
				else $this->date_to=date("Y-m-d");
				$this->area=$area;
				if ($this->colored)    $this->SetDrawColor(0xDD);
		}

		function Header()
		{
				global $root_path, $db;
				$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',70,6,15);
				$this->SetFont("Arial","I","9");
				$total_w = 165;
				$this->Cell(17,4);
				$this->Cell($total_w,4,'Republic of the Philippines',$border2,1,'C');
				$this->Cell(17,4);
				$this->Cell($total_w,4,'DEPARTMENT OF HEALTH',$border2,1,'C');
				$this->Ln(2);
				$this->SetFont("Arial","B","10");
				$this->Cell(17,4);
				$this->Cell($total_w,4,'DAVAO MEDICAL CENTER',$border2,1,'C');
				$this->SetFont("Arial","","9");
				$this->Cell(17,4);
				$this->Cell($total_w,4,'Bajada, Davao City',$border2,1,'C');
				$this->Ln(6);
				$this->SetFont('Arial','B',12);
				$this->Cell(17,5);
				$this->Cell($total_w,4,'PHARMACY ISSUANCE REPORT',$border2,1,'C');
				$this->SetFont('Arial','B',9);
				$this->Cell(17,4);
				if($this->area)
				{
						$prod_obj=new Product;
						$prod=$prod_obj->getAllPharmaAreas();
						while($row=$prod->FetchRow())
						{
								if($row['area_code']==$this->area)
								{
										$area_name = $row['area_name'];
								}
						}
				}
				else
				{
						$area_name = "All areas";
				}

				$this->Ln(2);
				$this->Cell(17,4);
				$this->Cell($total_w,4,$area_name,$border2,1,'C');
				$this->Cell(17,4);
				if($this->date_from && $this->date_to)
				{
						$this->Cell($total_w,4,date("F j, Y",strtotime($this->date_from))." to ".date("F j, Y",strtotime($this->date_to)),$border2,1,'C');
				}
				$this->Ln();
				$this->SetTextColor(0);
				$row=6;
				$this->SetFont('Arial','B',9);
				$this->Cell($this->ColumnWidth[0],$this->RowHeight,$this->Headers[0],1,0,'C',1);
				$this->Cell($this->ColumnWidth[1],$this->RowHeight,$this->Headers[1],1,0,'C',1);
				$this->Cell($this->ColumnWidth[2],$this->RowHeight,$this->Headers[2],1,0,'C',1);
				$this->Cell($this->ColumnWidth[3],$this->RowHeight,$this->Headers[3],1,0,'C',1);
				$this->Cell($this->ColumnWidth[4],$this->RowHeight,$this->Headers[4],1,0,'C',1);
				$this->Cell($this->ColumnWidth[5],$this->RowHeight,$this->Headers[5],1,0,'C',1);
				$this->Cell($this->ColumnWidth[6],$this->RowHeight,$this->Headers[6],1,0,'C',1);
			
				$this->Ln();

		}

		function Footer()
		{
				$this->SetY(-23);
				$this->SetFont('Arial','I',8);
				$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
		}

		function BeforeData()
		{
				if ($this->colored) {
						$this->DrawColor = array(0xDD,0xDD,0xDD);
				}
				$this->ColumnFontSize = 7;
		}

		function BeforeCellRender()
		{
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
						$this->Cell(201, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
				}

				$cols = array();
		}

		function FetchData()
		{
			$order = new SegOrder('pharma');
				$this->SetFont('Arial','',7);
				global $db;
				$where_area="";
				$where_date="";
				if($this->area)
				{
						$where_area="and o.pharma_area='".$this->area."' ";
				}
				if($this->date_from)
				{
						$where_date="and (i.serve_dt between '".$this->date_from." 00:00:00' and '".$this->date_to." 00:00:00') ";
				}
				$sql="select i.serve_dt, o.pid,CONCAT(IF (trim(p.name_last) IS NULL,'',trim(p.name_last)),', ',IF(trim(p.name_first) IS NULL ,'',trim(p.name_first)),' ', ".
				"IF(trim(p.name_middle) IS NULL,'',trim(p.name_middle))) as PatientName, c.artikelname, i.quantity,ce.er_location AS erloc, ce.er_location_lobby AS erloclob,ce.current_dept_nr,ce.encounter_type AS enctype,  ce.`current_ward_nr` AS current_ward,
  				ce.`current_room_nr`".
				"from seg_pharma_orders as o join seg_pharma_order_items as i on o.refno=i.refno inner join care_person as p on o.pid=p.pid INNER JOIN care_encounter AS ce
   				 ON ce.`encounter_nr` = o.`encounter_nr`".
				"inner join care_pharma_products_main as c on c.bestellnum=i.bestellnum and i.serve_status='S' ".
				$where_date.
				$where_area.
				"order by p.name_last, p.name_first, p.name_middle";
				//echo "query: ".$sql;
				$result = $db->Execute($sql);
				if($result)
				{
		
						$this->_count = $result->RecordCount();
						$this->Data=array();
						while($row=$result->FetchRow())
						 {
						 			if ($row['enctype']==1){
			
				$erLoc = $order->getERLocation($row['erloc'], $row['erloclob']);
		
				if($erLoc['area_location'] != '')
   				$location = "ER - " . $erLoc['area_location'] . " (" . $erLoc['lobby_name'] . ")";
    			else
   				$location = "EMERGENCY ROOM";
			}elseif ($row['enctype']==2){
				$dept = $order->getDeptAllInfo($row['current_dept_nr']);

				$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));

			}
			elseif(($row['enctype']==4)|| ($row['enctype']==3)){

				$dward = $order->getWardInfo($row['current_ward']);

				$location = strtoupper(strtolower(stripslashes($dward['ward_id'])))." Rm # :" .$row['current_room_nr'];


			}
			elseif ($row['enctype']==6){			
				$location = "Industrial clinic";
			}else{
				$location = 'WALK-IN';
			}
								$this->Data[]=array(
										$row['serve_dt'],$row['pid'],$row['PatientName'],
										$row['artikelname'], $row['quantity'], $location
								);
						 }
				}
				else
				{
						print_r($sql);
						print_r($db->ErrorMsg());
						exit;
						# Error
				}
		}

}
$rep =& new RepGen_Pharma_DailyIssuance($_GET['area'], $_GET['datefrom'], $_GET['dateto']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();

?>