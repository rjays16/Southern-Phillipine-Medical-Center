<?php
#EDITED BY VANESSA A. SAREN 02-13-08
#EDITED BY LST 06-29-2008, 08-13-2008, 12-03-2008 - Removed getSuppliesData function
require('./roots.php');
require_once($root_path."classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/billing/class_bill_info.php'); //added by jasper 04/08/2013
require_once($root_path.'include/care_api_classes/billing/class_billing_new.php');
require_once($root_path.'include/care_api_classes/billing/class_billareas.php');

require_once($root_path.'include/care_api_classes/billing/class_ops_new.php');
require_once($root_path.'include/care_api_classes/dialysis/class_dialysis.php');
require_once($root_path.'include/care_api_classes/class_ward.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path."include/care_api_classes/class_caserate_icd_icp.php");

include_once($root_path.'include/care_api_classes/class_personell.php');
include_once($root_path.'include/care_api_classes/class_department.php');

#added by VAN 04-24-2009
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');

define('GEN_COL01', 4);		 	// in mm.
define('GEN_COL02', 2.8); 	 	// in inches.
define('GEN_COL02_D', 8.25);	 	// in mm.
define('GEN_COL02_D2', 11.75);	// in mm.
define('GEN_COL02_D3', GEN_COL02_D2 + 3.5);	// in mm.

define('COL_MID', 2);

define('COL03_WIDTH', 33.5);
define('COL04_WIDTH', 25.972);
define('COL05_WIDTH', 27.972);
define('COL06_WIDTH', 27.972);

define('FOOTER_COL01', 84);
define('FOOTER_COL02', 84);

define('NAME_LEN', 52);
define('DEPT_LEN', 24);
define('HCARE_ID', 18);

class BillPDF extends FPDF {
	var $encounter_nr;
    var $bill_ref_nr; //added by jasper 01/04/13
    var $prev_bill_amt; //added by jasper 04/08/2013
	var $ishousecase;
	var $isphic;
	var $death_date; //Added by Jarel 05/24/13

	var $DEFAULT_FONTSIZE;
	var $DEFAULT_FONTTYPE;
	var $DEFAULT_FONTSTYLE;

	var $WBORDER;
	var $ALIGNMENT;
	var $NEWLINE;

	var $reportTitle="";

	var $billType;

	var $Data;
	var $pfDaTa;

	var $totalCharge = array(0, 0, 0, 0, 0, 0, 0, 0, 0);
	var $totalDiscount = array(0, 0, 0, 0, 0, 0, 0, 0, 0);
	var $totalCoverage = array(0, 0, 0, 0, 0, 0, 0, 0, 0);
	var $totalExcess = array(0, 0, 0, 0, 0, 0, 0, 0, 0);

	var $personData = array();

	var $objBill; //Billing object

	var $IsDetailed;
	var $bill_date;

	var $head_name;
	var $head_position;

	var $clerk_name;
    var $clerk_italized;

	var $b_acchist_gathered = FALSE;

    var $brecalc = false;
    var $excess;
    var $pid;

    var $bill_nr;
    var $hdata;
    var $accomodation_type;
	/*
	 * Constructor
	 * @param string encounter_nr
	 */

	function BillPDF($enc='', $bill_dt = "0000-00-00 00:00:00", $bill_frmdt = "0000-00-00 00:00:00", $old_bill_nr = '', $bcomp=false, $deathdate) {
		 if(!empty($enc)){
			$this->encounter_nr = $enc;
		 }
         //added by jasper 01/04/13
         if (!empty($old_bill_nr)) {
            $this->bill_ref_nr = $old_bill_nr;
         }
		#added by VAN 02-14-08
		 $this->IsDetailed = $_GET['IsDetailed'];
         $this->brecalc = $bcomp;

		 $pg_size = array($this->in2mm(8.5), $this->in2mm(13));                 // Default to long bond paper --- modified by LST - 04.13.2009
		 $this->FPDF("P","mm", $pg_size);
		 $this->AliasNbPages();
		 $this->AddPage("P");
//		 $this->SetTopMargin(1);

		 $this->DEFAULT_FONTTYPE = "Times";
		 $this->DEFAULT_FONTSIZE = 11;
		 $this->DEFAULT_FONTSTYLE = '';
		 $this->NEWLINE = 1;
		 $this->death_date = $deathdate;

		 //Instantiate billing object
         if ($this->brecalc) {
            $this->objBill = new Billing();
            $this->objBill->setBillArgs($this->encounter_nr, $bill_dt, $bill_frmdt,$deathdate,$old_bill_nr);
            //$this->objBill->applyDiscounts(); //TODO1
         }
         else{
            $this->objBill = new Billing();
		 	$this->objBill->setBillArgs($this->encounter_nr, $bill_dt, $bill_frmdt,$deathdate,$old_bill_nr);
		 }
		 $this->bill_date = $bill_dt;

		 //get first the confinement type
		 $this->objBill->getConfinementType();

         //added by jasper 03/18/2013
         if (!($this->objBill->isFinal())) {
            $this->Image('../../gui/img/logos/tentativebill.jpg',30, 50, 150,150);
         }
         //added by jasper 03/18/2013
         $data['encounter'] = $this->encounter_nr;
         $this->bill_nr = $this->objBill->getbillnr($data);

        $this->hdata = $this->getTotals();//added by Nick, 1/4/2014
		// echo json_encode($this->hdata);
		// exit();
	}// end of Bill_Pdf

	//Page Header
	#commented by VAN 03-15-08

	function Header() {
		//Display Page title
#----------------------- LST - 06-21-2008
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

		$this->Image('../../gui/img/logos/dmc_logo.jpg',20,10,20,20);

//		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',20,10,20,20);
		$this->SetFont("Times", "B", "10");
		$this->Cell(0, 4, $row['hosp_country'], 0, 1,"C");
		$this->Cell(0, 4, $row['hosp_agency'], 0, 1 , "C");
		$this->Cell(0, 4, $row['hosp_name'], 0, 1, "C");

		$this->SetFont("Times", "", "10");
		$this->Cell(0, 4, $row['hosp_addr1'], 0, 1, "C");
#---------------------- LST  - 06-21-2008

	}// end of Bill_Header function

	//Page footer
	function Footer() {
		//Go to 1.5 cm from bottom
		$this->SetY(-15);
		
		$this->SetFont('Arial','B',8);
		$this->setX(10);
		$this->Cell(1, 4, "SPMC-F-BIL-11", "", 0, 'L');
		$this->setX(70);
		$this->Cell(1, 4, "Effectivity: October 1, 2013", "", 0, 'L');
		$this->setX(130);
		$this->Cell(1, 4, "Rev. 0", "", 0, 'L');
		$this->SetFont('Arial','I',8);
		$this->setXY(100,-18);
		$this->Cell(0, 10, 'Page '.$this->PageNo().' of {nb}',0,0,'R');
	}

	function ReportFooter() {
		#added by VAN 04-24-2009
		global $db;
		$labObj = new SegLab();
		$personObj = new Person();


		$this->getBillingHead();
		$this->getBillingClerk($_SESSION['sess_temp_userid'],$_GET['encounter_nr'],$_GET['nr']);

        #italized if no save bill yet
        if ($this->clerk_italized)
            $this->SetFont('Arial','I',8);
        else
		    $this->SetFont($this->fontType, $this->fontStyle, $this->fontSize);

		$y = $this->GetY();
		// Signatories ...
		$this->Ln(4);
		$this->Cell(4, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL01, 4, "", "", 0, '');
		$this->Cell(20, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL02, 4, "Prepared by:", "", 1, '');
		$this->Ln(4);
		$this->Cell(4, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL01, 4, '', "", 0, 'C');
		$this->Cell(20, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL02, 4, $this->clerk_name, "", 1, 'C');

		$this->Cell(4, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL01, 4, '', "", 0, 'C');
		$this->Cell(20, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL02, 4, "Billing Clerk", "T", 1, 'C');

		// Confirmation ...
		//added by pol 07/24/2013
		//fix for bug # 308
		$this->Ln(4);
		$this->Cell(4, 4, "", "", 0, '');
		//end by pol
		$this->Ln(4);
		$this->Cell(4, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL01, 4, "", "", 0, '');
		$this->Cell(20, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL02, 4, "Confirmed by:", "", 1, '');



		$this->Ln(5);
		$this->Cell(4, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL01, 4, "", "", 0, '');
		$this->Cell(20, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL02, 4, "Signature over Printed Name/Relationship/Tel.#", "T", 1, '');

		if($this->isphic){
			$this->SetFont("Arial", $this->fontStyle, 8);
			$this->setXY(10,$y+7);
			//$this->Ln(8);
			$sMembership = $this->objBill->getMemCategoryDesc();
			$this->Cell(1, 4, "MEMBERSHIP CATEGORY:", "", 1, 'L');
			$this->SetFont("Arial", "B", 8);
			$this->Cell(50, 4, ($sMembership == '' ? "Not Specified" : $sMembership), "B", 1, 'L');

			$this->setX(10);
			$this->Ln(8);
			
			$member_name = $this->objBill->getInsuranceMemberName($this->pid,HCARE_ID);
			
			$this->SetFont("Arial", $this->fontStyle, 8);
			$this->Cell(1, 4, "MEMBER'S NAME:", "", 1, 'L');
			$this->Ln(3);
			$this->SetFont("Arial", "B", 8);
			$this->Cell(50, 4,  strtoupper($member_name), "B", 1, 'C');
			$this->SetFont("Arial", $this->fontStyle, 8);
		}


		$saccom = (!$this->ishousecase) ? strtoupper($this->objBill->getAccomodationDesc() /*TODO9*/) : "";

		$nypos = $this->GetY();
		//edited by VAN 02-14-2013
        /*if (!(strpos($saccom, "PAYWARD") === false)){
			$this->SetY(-1 * $this->in2mm(2.2));
		}else
			$this->SetY(-1 * $this->in2mm(2));*/
        $this->SetY(-1 * $this->in2mm(1.85));
		$ntmp = $this->GetY();
		if ($nypos >= $ntmp) $this->AddPage("P");

		/*if (!(strpos($saccom, "PAYWARD") === false))
			$this->SetY(-1 * $this->in2mm(2.2));
		else
			$this->SetY(-1 * $this->in2mm(2));*/
        $this->SetY(-1 * $this->in2mm(1.85));

		$this->Cell(0, 1, "", "T", 1, 'C');
		$this->Cell(0, 4, $saccom." PATIENT CLEARANCE", "", 1, 'C');
		$this->Ln(1);

		$this->Cell(4, 2, "", "", 0, '');
		$this->Cell(FOOTER_COL01, 4, "CASE #: ".$this->encounter_nr, "", 0, 'C');
		$this->Cell(20, 2, "", "", 0, '');

//		$row = $this->personData->FetchRow();
		$row = $this->personData;
		$name = strtoupper($row['name_last'].",  ".$row['name_first']." ".$row['name_middle']);
		$this->Cell(FOOTER_COL02, 4, "PATIENT: ".$name, "", 1, 'C');

		$this->Ln(2);

		#edited by VAN 04-24-2009
        #change this that not only with borrowed blood but also all patients with blood request will
        #ask for blood bank clearance
        	$hasbloodborrowed = 0;
			$labObj->hasBloodRequest($this->encounter_nr);
			if ($labObj->count)
			    $hasbloodborrowed = 1;

			if ($hasbloodborrowed){
				if($this->excess > 0){ 
					$this->Cell(4, 4, "", "", 0, '');
			    $this->Cell(FOOTER_COL01*0.325, 4, "BLOOD BANK: ", "", 0, '');
			    $this->Cell(4, 4, "", "", 0, '');
			    $this->Cell(FOOTER_COL01*0.675, 4, str_repeat('_', 30), "", 0, '');
				}
			    

				$pharmaXval =  8;
				$pharmaNextLine =  1;
				$cashierXval =  4;
				$cashierNextLine =  0;
				$nurseXval =  8;
				$nurseNextLine =  1;
				$billingXval = 4;

	        }else{
				$pharmaXval =  4;
				$pharmaNextLine =  0;
				$cashierXval =  8;
				$cashierNextLine =  1;
				$nurseXval =  4;
				$nurseNextLine =  0;
				$billingXval = 8;
			}
       
		
		#$this->Cell($this->accomodation_type; 8, 4, "", "", 0, '');
		$this->Cell($pharmaXval, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL02*0.325, 4,  "LINEN: ", "", 0, '');
		$this->Cell(4, 4, "", "", 0, '');
		#$this->Cell(FOOTER_COL01*0.675, 4, str_repeat('_', 30), "", 1, '');
		$this->Cell(FOOTER_COL01*0.675, 4, str_repeat('_', 30), "", $pharmaNextLine, '');

		if ($hasbloodborrowed)
				$this->Ln(2);

		#$this->Cell(4, 4, "", "", 0, '');
		if($this->excess > 0){
			$this->Cell($cashierXval, 4, "", "", 0, '');
			$this->Cell(FOOTER_COL01*0.325, 4, "CASHIER: ", "", 0, '');
			$this->Cell(4, 4, "", "", 0, '');
			#$this->Cell(FOOTER_COL01*0.675, 4, str_repeat('_', 30), "", 0, '');
			$this->Cell(FOOTER_COL01*0.675, 4, str_repeat('_', 30), "", $cashierNextLine, '');
		}	
		

		if (!$hasbloodborrowed)
		    $this->Ln(6);

		#$this->Cell(8, 4, "", "", 0, '');
		$this->Cell($nurseXval, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL02*0.325, 4, "NURSE ON DUTY: ", "", 0, '');
		$this->Cell(4, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL01*0.675, 4, str_repeat('_', 30), "", $nurseNextLine, '');
	 #----------
		if ($this->accomodation_type == 2) {
			if ($hasbloodborrowed)
								$this->Ln(2);

			$this->Cell($billingXval, 4, "", "", 0, '');
			$this->Cell(FOOTER_COL01*0.325, 4, "BILLING: ", "", 0, '');
			$this->Cell(4, 4, "", "", 0, '');
			$this->Cell(FOOTER_COL01*0.675, 4, str_repeat('_', 30), "", 1, '');
         
		}
		
	}

	function ReportTitle() {
		$this->Ln(2);
		$this->SetFont($this->fontType, "B", "10");
		$this->Cell(0, 4, $this->reportTitle, 0 , 1, "C");
	}

	function PersonInfo() {
		global $date_format;

		$rowArray = $this->getPersonInfo($this->encounter_nr);
		if (!is_bool($rowArray)) {
			$row = $rowArray->FetchRow();

			$this->personData = $row;

			$name = strtoupper($row['name_last'].",  ".$row['name_first']." ".$row['name_middle']);

			$saddr1 = '';
			$saddr2 = '';
			$saddr3 = '';
			$this->trimAddress($row['street_name'], $row['brgy_name'], $row['mun_name'], $row['prov_name'], $row['zipcode'], $saddr1, $saddr2, $saddr3);
//			$admission_dte = @formatDate2Local($row['admission_dt'], $date_format);

			//$billdte       = strftime("%b %d, %Y %I:%M %p", strtotime($this->bill_date));
            //edited by jasper 06/10/2013 REMOVE TIME FROM BILL DATE
            $billdte       = strftime("%b %d, %Y", strtotime($this->bill_date));
			if (is_null($row['admission_dt']))
				$admission_dte = strftime("%b %d, %Y %I:%M %p", strtotime($row['encounter_date']));
			else
				$admission_dte = strftime("%b %d, %Y %I:%M %p", strtotime($row['admission_dt']));

// --- Changes made by LST - $this->in2mm(4.8) to $this->in2mm(4.5)

			$this->Ln(4);
			$this->SetFont($this->fontType, $this->fontStyle, $this->fontSize);

            //added by jasper 01/04/13
            //Encounter number
            $this->Cell(20, 4, "Case #", "", 0, 'L');
            $this->Cell(1, 4, ":", "", 0, 'R');
            $this->Cell($this->in2mm(4.4), 4, $this->encounter_nr, "", 0, '');


            //Bill Reference number
            $this->Cell(22.6, 4, "Bill Ref. # ", "", 0, 'L');
            $this->Cell(1, 4, ":", "", 0, 'R');
            $this->Cell(12, 4, $this->bill_ref_nr, "", 1, '');
            //added by jasper 01/04/13

			//HRN
			$this->Cell(20, 4, "HRN ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->Cell($this->in2mm(4.4), 4, $row['pid'], "", 0, '');
			$this->pid = $row['pid'];
			//Date
			$this->Cell(22.6, 4, "Date ", "", 0, '');
			$this->Cell(1, 4, ":", "", 0, 'R');
//			$this->Cell(12, 4, date('m/d/Y'), "", 1, '');
			$this->Cell(12, 4, $billdte, "", 1, '');

			//patient name
			$this->Cell(20, 4, "Name ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->Cell($this->in2mm(4.4),  4, substr($name, 0, NAME_LEN), "", 0, '');

			//Department
			$this->Cell(22.6, 4, "Dept. ", "", 0, '');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->Cell(12, 4, substr($row['dept_name'],0,DEPT_LEN), "", 1, '');

			//Address (line 1)
			$this->Cell(20, 4, "Address ", "", 0, '');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->Cell($this->in2mm(4.4), 4, strtoupper($saddr1), "", 0, '');

			//Admitted
			$this->Cell(22.6, 4, "Admitted", "", 0, '');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->Cell(35, 4, $admission_dte, "", 1, '');

            //Address (line 2)
			$this->Cell(20, 4, "", "", 0, '');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->Cell($this->in2mm(4.4), 4, strtoupper($saddr2), "", 0, '');

			//Classification
//			$sClassification = $this->objBill->getClassificationDesc();
			//$sMembership = $this->objBill->getMemCategoryDesc();
            //added by jasper 04/24/2013
            $classification = $this->objBill->getClassificationDesc($this->encounter_nr, $this->bill_date); //TODO4


//			$this->Cell(22.75, 4, "Classification", "", 0, '');
            //edited by jasper 04/24/2013
			$this->Cell(22.75, 4,(!$classification ? " " : "Classification") , "", 0, '');
			$this->Cell(1, 4, (!$classification ? " " : ":"), "", 0, 'R');
//			$this->Cell(30, 4, ($sClassification == '' ? "No Classification" : $sClassification), "", 1, '');
			if($this->isphic && ($sMembership!='' || $classification1)){
				if(count_chars($sMembership)>15 || count_chars($classification1)>15)
					$this->SetFont("Times", "B", "6");
			}
			$this->Cell(30, 4, ($this->isphic ? ($sMembership == '' ? " " : $sMembership) : ($classification ? $classification : "No PHIC")), "", 1, '');
			$this->SetFont("Times", "", "10");


			//Address (line 3)
			if ($saddr3 != '') {
				$this->Cell(20, 4, "", "", 0, '');
				$this->Cell(1, 4, ":", "", 0, 'R');
				$this->Cell($this->in2mm(4.4), 4, strtoupper($saddr3), "", 1, '');
			}

			//Room #
			
			if ($row['room_no'] == 0) {
                if ($this->brecalc) {
                    $acchist = $this->objBill->getAccomodationList;/*getAccommodationHist();*/ // set AccommodationHist
                    /*$this->objBill->getRoomTypeBenefits(); // set Room type Benefits
                    $this->objBill->getConfineBenefits('AC');*/
                }
                else {
                    $ac = new ACBill();
                    if (!($ac instanceof ACBill)) {
                            $var_dump("No accommodation object retrieved!");
                    }
                    $ac->assignBillObject($this->objBill);
                }

				$accArray   = $this->objBill->getAccHist($accHist);
				if (!empty($accArray)) {
					$sroom_no   = $accArray[count($accArray)-1]->getRoomNr();
					$sward_name = $accArray[count($accArray)-1]->getTypeDesc();

                    if ($this->ishousecase) {
                        $sward_name = preg_replace("/pay[\s]*ward/i", "Ward", $sward_name);
                    }
				}
				else {
					$sroom_no   = 'None';
					$sward_name = "No Accommodation";
				}

				$this->b_acchist_gathered = TRUE;
			}
			else {
				$sroom_no   = $row['room_no'];
				$sward_name = $row['ward_name'];

                if ($this->ishousecase) { 
                    $sward_name = preg_replace("/pay[\s]*ward/i", "Ward", $sward_name);
                }
			}

			$sCaseType = $this->objBill->getCaseTypeDesc($this->encounter_nr, $this->bill_date); //TODO5

			$this->Cell(20, 4, "Room #", "", 0, '');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->Cell(10, 4, $sroom_no, "", 0, '');
//			$this->Cell($this->in2mm(4), 4, "( ".$sward_name." )".($sCaseType == '' ? '' : " - ".$sCaseType), "", 0 ,'');

			#Last billing ...
			$lastbilldte = $this->objBill->getActualLastBillDte(); //TODO6
			if ( ($lastbilldte == "0000-00-00 00:00:00") && !$this->objBill->getIsCoveredByPkg() )
				$this->Cell($this->in2mm(4), 4, "( ".$sward_name." )".($sCaseType == '' ? '' : " - ".$sCaseType), "", 0 ,'');
			else {
				$this->Cell($this->in2mm(4), 4, "( ".$sward_name." )".($sCaseType == '' ? '' : " - ".$sCaseType), "", 0 ,'');

                if ( $this->objBill->getIsCoveredByPkg() ) {
                    $this->Cell(23, 4, "Package ", "", 0, '');
                    $this->Cell(1, 4, ":", "", 0, 'R');
                    $this->Cell(35, 4, $this->objBill->getPackageName(), "", 1, '');
                }
                else {
                    $this->Cell(22.6, 4, "  ", "", 0, '');
                    $this->Cell(1, 4, ":", "", 0, 'R');
                    $this->Cell(12, 4, strftime("%b %d, %Y %I:%M%p", strtotime($lastbilldte)), "", 1, '');
                }
			}

			if($this->objBill->isMedicoLegal($this->encounter_nr) /*edited by nick,1/5/2014 3:43PM*/){
				$this->SetFont("Times", "B", "11");
				$this->Cell(50, 4,"Medico Legal", "", 1, 'R');
				$this->SetFont("Times", "", "11");
			}/*else{
				$this->Cell(50, 4,"1111", "", 1, 'R');
			}*/

			#Added by Jarel	06/12/2013
			if($this->death_date != ''){
				#Updated by Jane 10/17/2013
				$this->SetFont("Times", "B", "11");
				$this->Cell(20, 4, "Death Date", "", 0, 'R');
				$this->Cell(4, 4, ":", "", 0, 'R');
				$this->Cell(35, 4, strftime("%b %d, %Y %I:%M%p", strtotime($this->death_date)), "", 1, '');
				$this->SetFont("Times", "", "11");
			}/*else{
				$this->Cell(50, 4,"2222", "", 1, 'R');
			}*/

			#added by Nick, 1/5/2014 6:23 PM
			$this->Ln();
            $this->showSeriesNr();//added by nick 06-27-2014
			if ($this->isphic && $this->IsDetailed) {
				$icds = $this->getIcdCodes($this->encounter_nr);
				$str = "";
				$index=1;
				if (!empty($icds)){
					foreach ($icds as $key => $value) {
						$str .= $value['code'].",";
					}
					$str = trim($str,',');
					$this->Cell(50, 4,"ICD: ".$str, "", 1, 'L');
				}
				
				$icps = $this->getIcpCodes($this->encounter_nr);
				$str = "";
				$index=1;
				if (!empty($icps)){
					foreach ($icps as $key => $value) {
						$str .= $value['code'].",";
					}
					$str = trim($str,',');
					$this->Cell(50, 4,"ICP: ".$str, "", 1, 'L');
				}
			} elseif ($this->isphic) {
				$result = $this->getSavePackages($this->bill_nr);
				if($result) {
					while($row=$result->FetchRow()){
						if($row['rate_type']==1){
                            $this->AddLabel("First Case Rate",$row['package_id']);
						}else {
                            $this->AddLabel("Second Case Rate",$row['package_id']);
						}
					}	
				}
				$this->SetFont("Times", "", "11");
			}
			#end Nick
			//$this->Ln();
		}
	}//end of PersonInfo

    //added by Nick 06-26-2014
    function showSeriesNr(){
        $series_nr = $this->objBill->getSeriesNumber($this->encounter_nr);
        if($series_nr){
            $this->AddLabel("Series #",$series_nr,array(20,35));
        }
    }

    //added by Nick 06-26-2014
    function AddLabel($lbl,$val,$w=array(35,35)){
        $this->SetFont("Times", "B", "11");
        $this->Cell($w[0], 4, $lbl, "", 0, '');
        $this->Cell(1, 4, ":", "", 0, 'R');
        $this->Cell($w[1], 4, $val, "", 1, '');
        $this->SetFont("Times", "", "11");
    }

	function TitleHeader($billType){
		switch($billType){
			case 'summary':
				$this->Ln(3);
//				$this->Cell(GEN_COL01-4, 4, "#", "TB", 0, 'C');
				$this->Cell(0, 4, " ", "", 1, '');//added by art 01/11/2014
				$this->Cell($this->in2mm(GEN_COL02) , 4, "Particulars", "TB", 0, 'C');
				$this->Cell(COL_MID, 4, " ", "", 0, '');
				$this->Cell(COL03_WIDTH, 4, "Actual Charges", "TB", 0, 'C');
				$this->Cell(COL_MID, 4, " ", "", 0, '');
				$this->Cell(COL04_WIDTH, 4, "Discount", "TB", 0, 'C');
				$this->Cell(COL_MID, 4, " ", "", 0, '');
				$this->Cell(COL05_WIDTH, 4, "Insurance/PHIC", "TB", 0, 'C');
				$this->Cell(COL_MID, 4, " ", "", 0, '');
				$this->Cell(COL06_WIDTH, 4, "Excess", "TB", 0, 'C');
				break;
			case 'detailed':
//				$this->Ln(3);
//				$this->Cell(8, 4, "#", "TB", 0, 'C');
//				$this->Cell(4, 4, " ", "", 0, '');
//				$this->Cell($this->in2mm(1.2) , 4, "Date Requested", "TB", 0, 'C');
//				$this->Cell(4, 4, " ", "", 0, '');
//				$this->Cell($this->in2mm(3.4) , 4, "Particulars", "TB", 0, 'C');
//				$this->Cell(4, 4, " ", "", 0, '');
//				$this->Cell(10 , 4, "Qty", "TB", 0, 'C');
//				$this->Cell(4, 4, " ", "", 0, '');
//				$this->Cell($this->in2mm(1.2) , 4, "Amount", "TB", 0, 'C');
			break;
		}
	} //end of function TitleHeader

	function PrintData(){
		$this->Ln(5);

		// Accommodation
		if (!$this->objBill->isERPatient($this->encounter_nr)) $this->getAccommodationData();
		$this->getHospitalServiceData();   // Hospital services ( Laboratory & radiology)
		$this->getMedicinesData();         // Medicines
//		$this->getSuppliesData();          // Supplies
		$this->getOpsCharges();			   // Operation/Procedures
		$this->getMiscellaneousCharges();  // Miscellaneous Charges

	}// end of function PrintData

	function getPFDiscount($pfarea, $npf, $nclaim) {
		global $db;

		$n_discount = 0.00;
		$n_prevdiscount = 0.00;

		$area_array = array('AC', 'D1', 'D2', 'D3', 'D4');
        //edited by jasper 04/16/2013    -CONDITION SHOULD BE THE SAME WITH FUNCTION getBillAreaDiscount IN class_billing.php
		//if ($this->objBill->isCharity() && (in_array($pfarea, $area_array))) {
          if ($this->objBill->isCharity() && !$this->objBill->isMedicoLegal($this->encounter_nr/*edited by nick,1/5/2014 3:43PM*/) && !$this->objBill->isPHIC() && (in_array($pfarea, $area_array))) {
			switch ($pfarea) {
				case 'D1':
				case 'D2':
				case 'D3':
				case 'D4':
					$n_discount = $npf - $nclaim;
                    break;
			}
		}
		else {
			$strSQL = "select fn_get_bill_discount('". $this->encounter_nr. "', '". $pfarea ."', '".$this->bill_date."') as discount";
			if ($result = $db->Execute($strSQL)) {
				if ($result->RecordCount()) {
					$row = $result->FetchRow();
					if (!is_null($row['discount'])) {
						$n_discount = $row['discount'];
					}
				}
			}

			// .... get discount rate applied to bill area of encounter while at ER, if there is one.
			if ($this->objBill->prev_encounter_nr != '') {
				$strSQL = "select fn_get_bill_discount('". $this->objBill->prev_encounter_nr. "', '". $pfarea ."', '".$this->bill_date."') as discount";
				if ($result = $db->Execute($strSQL)) {
					if ($result->RecordCount()) {
						$row = $result->FetchRow();
						if (!is_null($row['discount'])) {
							$n_prevdiscount = $row['discount'];
						}
					}
				}
			}

			$n_discount = ($n_discount > $n_prevdiscount) ? $n_discount : $n_prevdiscount;      // Return the highest discount applied.
			switch ($pfarea) {
				case 'D1':
				case 'D2':
				case 'D3':
				case 'D4':
					$n_discount *= $npf;
					break;
			}
		}
		return round($n_discount, 2);
	}

	#added by Nick, 1/5/2014
	function getIcdCodes($encounter_nr){
		global $db;
		$data = array();
		$index = 0;
		$sql = "SELECT code FROM seg_encounter_diagnosis WHERE encounter_nr = ".$db->qstr($encounter_nr)." AND is_deleted = 0;";
		$rs = $db->Execute($sql);
		 if($rs){
		 	if($rs->RecordCount()>0){
		 		while($row = $rs->FetchRow()){
		 			$data[$index] = array("code" => $row['code']);
		 			$index++;
		 		}
		 		return $data;
		 	}else{
		 		return false;
		 	}
		 }else{
		 	return false;
		 }
	}

	function getIcpCodes($encounter_nr){
		global $db;
		$data = array();
		$index = 0;
		$sql = "SELECT b.ops_code FROM seg_misc_ops AS a
				INNER JOIN seg_misc_ops_details  AS b ON a.`refno` = b.`refno`
				WHERE a.`encounter_nr` = ".$db->qstr($encounter_nr)." ORDER BY op_date DESC;";
		$rs = $db->Execute($sql);
		 if($rs){
		 	if($rs->RecordCount()>0){
		 		while($row = $rs->FetchRow()){
		 			$data[$index] = array("code" => $row['ops_code']);
		 			$index++;
		 		}
		 		return $data;
		 	}else{
		 		return false;
		 	}
		 }else{
		 	return false;
		 }
	}
#end nick


	function getSavePackages($bill_nr)
    {
        global $db;
        
        $this->sql = "SELECT * FROM seg_billing_caserate WHERE bill_nr =".$db->qstr($bill_nr)." ORDER BY rate_type";
        if ($result = $db->Execute($this->sql)) {
            if ($result->RecordCount()) {
                return $result;
            } else {
                return false;
            }
        }
    }

#added by Nick, 1/4/2014
/*function getMiscData($enc){

	global $db;

	$sql = "SELECT  (a.`total_msc_charge` * SUM(b.`discount`)) AS misc_discount,
					a.`total_msc_charge` - (a.`total_msc_charge` * SUM(b.`discount`)) AS misc_excess
			FROM seg_billing_encounter AS a
			RIGHT JOIN seg_billingapplied_discount AS b ON a.`encounter_nr` = b.`encounter_nr`
			WHERE a.encounter_nr = ".$db->qstr($enc)." AND is_deleted IS NULL;";

	$rs = $db->Execute($sql);
	if($rs){
		if($rs->RecordCount()>0){
			$row = $rs->FetchRow();
			return array("misc_discount" => $row['misc_discount'],
				         "misc_excess" => $row['misc_excess']
				        );
		}else{
			return array("misc_discount" => 0,
				         "misc_excess" => 0
				        );
		}
	}else{
		return array("misc_discount" => 0,
				         "misc_excess" => 0
				        );
	}

}*/
#end nick
#added by Nick, 1/4/2014
	function getCoverage_PF_Serv($bill_nr){
		global $db;
		$sql = "SELECT  (total_d1_coverage+total_d2_coverage+total_d3_coverage+total_d4_coverage) AS total_pf_coverage,
						total_services_coverage AS total_serv_coverage
				FROM seg_billing_coverage WHERE bill_nr = ".$db->qstr($bill_nr);
		$rs = $db->Execute($sql);
		if($rs){
			if($rs->RecordCount()>0){
				return $rs->FetchRow();
			}else{
				return array("total_pf_coverage"=>0,"total_serv_coverage"=>0);
			}
		}else{
			return array("total_pf_coverage"=>0,"total_serv_coverage"=>0);
		}
	}

	function getCoverage_PF_PerArea($dr_nr,$bill_nr,$area){
		global $db;
		$sql = '';
		$sql = "SELECT dr_claim FROM seg_billing_pf WHERE dr_nr = ".$db->qstr($dr_nr)." AND ".
				"bill_nr = ".$db->qstr($bill_nr)." AND role_area = ".$db->qstr($area);
		$rs = $db->Execute($sql);
	
		if($rs){
			if($rs->RecordCount()>0){
				return $rs->FetchRow();
			}else{
				return array("dr_claim"=>0);
			}
		}else{
			return array("dr_claim"=>0);
		}
	}

	function getDiscount_PF_PerArea($bill_nr){
		global $db;
		$sql = "SELECT total_d1_discount, total_d2_discount,
						total_d3_discount, total_d4_discount
				FROM seg_billingcomputed_discount WHERE bill_nr = ".$db->qstr($bill_nr);
		$rs = $db->Execute($sql);
		if($rs){
			if($rs->RecordCount()>0){
				return $rs->FetchRow();
			}else{
				return array("total_d1_discount"=>0,
						     "total_d2_discount"=>0,
						     "total_d3_discount"=>0,
						     "total_d4_discount"=>0);
			}
		}else{
			return array("total_d1_discount"=>0,
					     "total_d2_discount"=>0,
					     "total_d3_discount"=>0,
					     "total_d4_discount"=>0);
		}
	}

	function getDiscount_PF_Serv($bill_nr){
		global $db;
		$sql = "SELECT  hospital_income_discount AS total_serv_discount,
						(total_d1_discount + total_d2_discount +
						total_d3_discount + total_d4_discount + professional_income_discount) AS total_pf_discount
				FROM seg_billingcomputed_discount WHERE bill_nr = ".$db->qstr($bill_nr);
		$rs = $db->Execute($sql);
		if($rs){
			if($rs->RecordCount()>0){
				return $rs->FetchRow();
			}else{
				return array("total_serv_discount"=>0,"total_pf_discount"=>0);
			}
		}else{
			return array("total_serv_discount"=>0,"total_pf_discount"=>0);
		}
	}

	function getTotal_PF_Serv($bill_nr){
		global $db;

		$sql = "SELECT  (total_acc_charge+total_med_charge+total_sup_charge+total_srv_charge+total_ops_charge+total_msc_charge) AS total_serv,
						total_doc_charge AS total_pf
				FROM seg_billing_encounter WHERE bill_nr = ".$db->qstr($bill_nr);
		$rs = $db->Execute($sql);
		if($rs){
			if($rs->RecordCount()>0){
				return $rs->FetchRow();
			}else{
				return array("total_serv"=>0,"total_pf"=>0);
			}
		}else{
			return array("total_serv"=>0,"total_pf"=>0);
		}
	}

	function getTotals(){

		//$miscData = $this->getMiscData($this->encounter_nr);

		$coverages = $this->getCoverage_PF_Serv($this->bill_nr);
		$discounts = $this->getDiscount_PF_Serv($this->bill_nr);
		$totals = $this->getTotal_PF_Serv($this->bill_nr);

		// echo json_encode($discounts);
		// echo json_encode($coverages);
		// echo json_encode($totals);

		$serv_ex = ($totals['total_serv'] - ($coverages['total_serv_coverage'] + $discounts['total_serv_discount'])) /*- $miscData['misc_discount']*/;
		$pf_ex = $totals['total_pf'] - ($coverages['total_pf_coverage'] + $discounts['total_pf_discount']);

		if($pf_ex < 0){
			$coverages['total_pf_coverage'] += $pf_ex;
		}

		$pf_ex = $totals['total_pf'] - ($coverages['total_pf_coverage'] + $discounts['total_pf_discount']);

		$total_charge = $totals['total_serv'] + $totals['total_pf'];
		$total_discount = $discounts['total_pf_discount'] + ($discounts['total_serv_discount'] /*+ $miscData['misc_discount']*/);
		$total_coverage = $coverages['total_pf_coverage'] + $coverages['total_serv_coverage'];
		$total_excess = $serv_ex + $pf_ex;

		$output = array("total_charge"=>$total_charge,
			            "total_discount"=>$total_discount,
						"total_coverage"=>$total_coverage,
						"total_excess"=>$total_excess,
						"serv_charge"=>$totals['total_serv'],
						"serv_discount"=>$discounts['total_serv_discount'] /* + $miscData['misc_discount']*/,
						"serv_coverage"=>$coverages['total_serv_coverage'],
						"serv_excess"=>$serv_ex,
						"pf_charge"=>$totals['total_pf'],
						"pf_discount"=>$discounts['total_pf_discount'],
						"pf_coverage"=>$coverages['total_pf_coverage'],
						"pf_excess"=>$pf_ex
			           );
		// echo json_encode($output);
		return $output;
	}
#end nick

	//added by Nick,1/4/2014
	//edited by Nick, 1/5/2014 3:44 PM
	function Professional_Fee() {
		$this->Ln(8);
		$this->Cell(GEN_COL01, 4, "ADD:", "", 1, 'C');

		$data = array();
		$index = 0;

		$this->objBill->getProfFeesList();
		$this->objBill->getProfFeesBenefits();
		$hsp_pfs_benefits = $this->objBill->getPFBenefits();
		$proffees_list = $this->objBill->proffees_list;

		foreach($hsp_pfs_benefits as $key=> $value) {
			if ($value->role_area == $prevrole_area) continue;
			$prevrole_area = $value->role_area;
			reset($proffees_list);
			$this->objBill->initProfFeesCoverage($value->role_area);
			$totalCharge = number_format($this->objBill->getTotalPFCharge($value->role_area), 2);
			$coverage    = number_format($this->objBill->pfs_confine_coverage[$value->role_area], 2, '.', ',');
			$tr ='';
			foreach($proffees_list as $key=>$profValue){
				if($value->role_area == $profValue->role_area) {
					$opcodes = $profValue->getOpCodes();
					if ($opcodes != '') {
						$opcodes = explode(";", $opcodes);
					}
					if (is_array($opcodes)) {
						foreach($opcodes as $v) {
							$i = strpos($v, '-');
							if (!($i === false)) {
								$code = substr($v, 0, $i);
	                              if ($this->objBill->getIsCoveredByPkg()) break;
							}#if
						}#foreach
					}#if

					$drName = $profValue->dr_first." ".$profValue->dr_mid.(substr($profValue->dr_mid, strlen($profValue->dr_mid)-1,1) == '.' ? " " : ". ").$profValue->dr_last;
					$drCharge = number_format($profValue->dr_charge, 2, '.', ',');
					$totalPF += $profValue->dr_charge;

					$data[$index] = array("dr_charge"=>$profValue->dr_charge,
						                  "role_area"=>$value->role_area,
								          "role_desc"=>$value->role_desc,
								          "total_charge"=>$this->objBill->getTotalPFCharge($value->role_area),
								          "coverage"=>number_format($this->objBill->pfs_confine_coverage[$value->role_area], 2, '.', ','),
								          "drName"=>$drName,
								          "dr_nr" => $profValue->dr_nr,
								          "accommodation_type" => $profValue->accommodation_type,
							  	         );
					$index++;
				}#if
			}#foreach
		}#foreach
//echo "<pre>".print_r($data,true)."</pre>";
		$total = 0;

		foreach ($data as $data_key => $data_value) {		
			$total+=$data_value['total_charge'];
		}

		// if($this->IsDetailed){
		if(count($data)>0){
			$this->Cell($this->in2mm(GEN_COL02), 4,"Professional Fees", "",1, '');

			//counts
			$d1_count = 0;
			$d2_count = 0;
			$d3_count = 0;
			$d4_count = 0;

			foreach ($data as $data_key => $data_value) {
				if($data_value['role_area'] == "D1")
					$d1_count++;
				if($data_value['role_area'] == "D2")
					$d2_count++;
				if($data_value['role_area'] == "D3")
					$d3_count++;
				if($data_value['role_area'] == "D4")
					$d4_count++;
			}
			
			$applied_discount = $this->objBill->getTotalAppliedDiscounts($this->encounter_nr);
#Admitting -- Admitting -- Admitting
			if($d1_count>0){
				$d1_totalPF = 0;
				$this->Cell(GEN_COL01, 4, "", "", 0, '');
				$this->Cell($this->in2mm(GEN_COL02)-8, 4, "Admitting", "", ($d1_count>0)?1:0, '');
				
				foreach ($data as $data_key => $data_value) {
					$dr_coverage = $this->getCoverage_PF_PerArea($data_value['dr_nr'],$this->bill_nr,'D1');
					if($data_value['role_area'] == "D1"){
						$this->Cell(GEN_COL01 + 3.5, 4, "", "", 0, '');
						$this->Cell($this->in2mm(GEN_COL02)-10, 4, strtoupper($data_value['drName']), "", 0, '');
						$this->Cell(COL_MID - 1.75, 4, "", "", 0, '');
						$this->Cell(COL03_WIDTH + 4, 4, number_format($data_value['dr_charge'],2,'.',','), "", 0, 'R');
						if($data_value['accommodation_type'] == 1 && !$this->isphic && $this->objBill->isCharity() && !$this->objBill->isMedicoLegal($this->encounter_nr)){
							$discount = $data_value['dr_charge'];
						}else{
							$discount = $data_value['dr_charge'] * $applied_discount;
						}
						$this->Cell(COL03_WIDTH - 5, 4, number_format($discount,2,'.',','), "",0 , 'R');
						$this->Cell(COL03_WIDTH - 4, 4, number_format($dr_coverage['dr_claim'],2,'.',','), "",0 , 'R');
						$this->Cell(COL03_WIDTH - 4, 4, number_format($data_value['dr_charge']-$discount-$dr_coverage['dr_claim'],2,'.',','), "", 1, 'R');
						$d1_totalPF+=$data_value['dr_charge'];
						$d1_totalClaim+=$dr_coverage['dr_claim'];
						$d1_totalDiscount +=$discount;
						$d1_totalExcess+=$data_value['dr_charge']-$discount-$dr_coverage['dr_claim'];
					}
				}
				
				if($d1_count>0  && $this->IsDetailed)
					$this->Pf_Sub_Total($d1_totalPF,$d1_totalClaim,$d1_totalExcess,$d1_totalDiscount,"Admitting");
			}
#Consulting -- Consulting -- Consulting
			if($d2_count>0){
				$d2_totalPF = 0;
				$this->Cell(GEN_COL01, 4, "", "", 0, '');
				$this->Cell($this->in2mm(GEN_COL02)-8, 4, "Consulting", "", ($d2_count>0)?1:0, '');
				foreach ($data as $data_key => $data_value) {
					$dr_coverage = $this->getCoverage_PF_PerArea($data_value['dr_nr'],$this->bill_nr,'D2');
					if($data_value['role_area'] == "D2"){
						$this->Cell(GEN_COL01 + 3.5, 4, "", "", 0, '');
						$this->Cell($this->in2mm(GEN_COL02)-10, 4, strtoupper($data_value['drName']), "", 0, '');
						$this->Cell(COL_MID - 1.75, 4, "", "", 0, '');
						$this->Cell(COL03_WIDTH + 4, 4, number_format($data_value['dr_charge'],2,'.',','), "", 0, 'R');
						if($data_value['accommodation_type'] == 1 && !$this->isphic && $this->objBill->isCharity() && !$this->objBill->isMedicoLegal($this->encounter_nr)){
							$discount = $data_value['dr_charge'];
						}else{
							$discount = $data_value['dr_charge'] * $applied_discount;
						}
						$this->Cell(COL03_WIDTH - 5, 4, number_format($discount,2,'.',','), "",0 , 'R');
						$this->Cell(COL03_WIDTH -4, 4, number_format($dr_coverage['dr_claim'],2,'.',','), "",0 , 'R');
						$this->Cell(COL03_WIDTH - 4, 4, number_format($data_value['dr_charge']-$discount-$dr_coverage['dr_claim'],2,'.',','), "", 1, 'R');
						$d2_totalPF+=$data_value['dr_charge'];
						$d2_totalClaim+=$dr_coverage['dr_claim'];
						$d2_totalDiscount +=$discount;
						$d2_totalExcess+=$data_value['dr_charge']-$discount-$dr_coverage['dr_claim'];
					}
				}
				if($d2_count>0 && $this->IsDetailed)
					$this->Pf_Sub_Total($d2_totalPF,$d2_totalClaim,$d2_totalExcess,$d2_totalDiscount,"Consulting");
			}
#Surgeon -- Surgeon -- Surgeon
			if($d3_count>0){
				$d3_totalPF = 0;
				$this->Cell(GEN_COL01, 4, "", "", 0, '');
				$this->Cell($this->in2mm(GEN_COL02)-8, 4, "Surgeon", "", ($d3_count>0)?1:0, '');
				foreach ($data as $data_key => $data_value) {
					$dr_coverage = $this->getCoverage_PF_PerArea($data_value['dr_nr'],$this->bill_nr,'D3');
					if($data_value['role_area'] == "D3"){
						$this->Cell(GEN_COL01 + 3.5, 4, "", "", 0, '');
						$this->Cell($this->in2mm(GEN_COL02)-10, 4, strtoupper($data_value['drName']), "", 0, '');
						$this->Cell(COL_MID - 1.75, 4, "", "", 0, '');
						$this->Cell(COL03_WIDTH + 4, 4, number_format($data_value['dr_charge'],2,'.',','), "", 0, 'R');
						if($data_value['accommodation_type'] == 1 && !$this->isphic && $this->objBill->isCharity() && !$this->objBill->isMedicoLegal($this->encounter_nr)){
							$discount = $data_value['dr_charge'];
						}else{
							$discount = $data_value['dr_charge'] * $applied_discount;
						}
						$this->Cell(COL03_WIDTH - 5, 4, number_format($discount,2,'.',','), "",0 , 'R');
						$this->Cell(COL03_WIDTH - 4, 4, number_format($dr_coverage['dr_claim'],2,'.',','), "",0 , 'R');
						$this->Cell(COL03_WIDTH - 4, 4, number_format($data_value['dr_charge']-$discount-$dr_coverage['dr_claim'],2,'.',','), "", 1, 'R');
						$d3_totalPF+=$data_value['dr_charge'];
						$d3_totalClaim+=$dr_coverage['dr_claim'];
						$d3_totalDiscount +=$discount;
						$d3_totalExcess+=$data_value['dr_charge']-$discount-$dr_coverage['dr_claim'];
					}
				}
				if($d3_count>0 && $this->IsDetailed)
					$this->Pf_Sub_Total($d3_totalPF,$d3_totalClaim,$d3_totalExcess,$d3_totalDiscount,"Surgeon");
			}
#Surgeon -- Surgeon -- Surgeon
			if($d4_count>0){
				$d4_totalPF = 0;
				$this->Cell(GEN_COL01, 4, "", "", 0, '');
				$this->Cell($this->in2mm(GEN_COL02)-8, 4, "Anesthesiologist", "", ($d4_count>0)?1:0, '');
				foreach ($data as $data_key => $data_value) {
					$dr_coverage = $this->getCoverage_PF_PerArea($data_value['dr_nr'],$this->bill_nr,'D4');
					if($data_value['role_area'] == "D4"){
						$this->Cell(GEN_COL01 + 3.5, 4, "", "", 0, '');
						$this->Cell($this->in2mm(GEN_COL02)-10, 4, strtoupper($data_value['drName']), "", 0, '');
						$this->Cell(COL_MID - 1.75, 4, "", "", 0, '');
						$this->Cell(COL03_WIDTH + 4, 4, number_format($data_value['dr_charge'],2,'.',','), "", 0, 'R');
						if($data_value['accommodation_type'] == 1 && !$this->isphic && $this->objBill->isCharity() && !$this->objBill->isMedicoLegal($this->encounter_nr)){
							$discount = $data_value['dr_charge'];
						}else{
							$discount = $data_value['dr_charge'] * $applied_discount;
						}
						$this->Cell(COL03_WIDTH - 5, 4, number_format($discount,2,'.',','), "",0 , 'R');
						$this->Cell(COL03_WIDTH - 4, 4, number_format($dr_coverage['dr_claim'],2,'.',','), "",0 , 'R');
						$this->Cell(COL03_WIDTH - 4, 4, number_format($data_value['dr_charge']-$discount-$dr_coverage['dr_claim'],2,'.',','), "", 1, 'R');
						$d4_totalPF+=$data_value['dr_charge'];
						$d4_totalClaim+=$dr_coverage['dr_claim'];
						$d4_totalDiscount +=$discount;
						$d4_totalExcess+=$data_value['dr_charge']-$discount-$dr_coverage['dr_claim'];
					}
				}
				if($d4_count>0 && $this->IsDetailed)
					$this->Pf_Sub_Total($d4_totalPF,$d4_totalClaim,$d4_totalExcess,$d4_totalDiscount,"Anesthesiologist");
			}
				
			$this->Ln();
			$this->hdata['pf_coverage'] = $d1_totalClaim + $d2_totalClaim + $d3_totalClaim + $d4_totalClaim;
			$this->Pf_Totals();

		}else{
			$this->Cell($this->in2mm(GEN_COL02), 4,"Professional Fees", "",0, '');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL03_WIDTH, 4, "0.00", "", 1, 'R');
		}
		// }else{
		// 	$this->Cell($this->in2mm(GEN_COL02), 4,"Professional Fees", "",0, '');
		// 	$this->Cell(COL_MID, 4, "", "", 0, '');
		// 	$this->Cell(COL03_WIDTH, 4, number_format($total,2,'.',','), "", 1, 'R');
		// }

	}#end of function Professional_Fee
	//end by Nick

	function getDrClaim($dr_nr, $role_area, $drclaims) {
		$claim = 0;
		foreach($drclaims as $k=>$v) {
			if (($v->getDrNr() == $dr_nr) && ($v->getRoleArea() == $role_area)) {
				$claim = $v->getDrClaim();
			}
		}
		return $claim;
	}

#edited by Nick, 1/4/2014
var $serv_total = 0;
	function Sub_Total(){

		$this->serv_total = $this->total_accomodation +
					  $this->total_xlo +
					  $this->total_meds +
					  $this->total_ops +
					  $this->total_misc;

		$this->Ln(2);
		$this->Cell($this->in2mm(GEN_COL02), 4, "Sub-Total" , "", 0, 'R');

		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL03_WIDTH, 4, number_format($this->serv_total/*$this->hdata['serv_charge']*/,2,'.',','), "T", 0, 'R');

		$t_discount = 0;
		foreach($this->totalDiscount as $key=>$v) {
			if ($key != PF_AREA)
				$t_discount += $v;
		}
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL04_WIDTH, 4, number_format($this->hdata['serv_discount']/*-$this->mandatory_excess*/,2,'.',','), "T", 0, 'R');

		//Medicare Coverage
		#accomodation + hospital services + medicines + supplies + others
		$totalcoverage = 0;
		foreach($this->totalCoverage as $key=>$v) {
			if ($key != PF_AREA)
				$totalcoverage += $v;
		}
#		$this->subTotal_Medicare = $this->ACSubTotal_Medicare + $this->HSSubTotal_Medicare + $this->MDSubTotal_Medicare + $this->SPSubTotal_Medicare;
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL05_WIDTH, 4, number_format($this->hdata['serv_coverage'],2,'.',','), "T", 0, 'R');

		//Excess
		#accomodation + hospital services + medicines + supplies + others
		$totalexcess = 0;
		foreach($this->totalExcess as $key=>$v) {
			if ($key != PF_AREA)
				$totalexcess += $v;
		}
#		$this->subTotal_Excess = $this->ACSubTotal_Excess + $this->HSSubTotal_Excess + $this->MDSubTotal_Excess + $this->SPSubTotal_Excess;

		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL06_WIDTH, 4, number_format($this->hdata['serv_excess']/*+$this->mandatory_excess*/,2,'.',','), "T", 0, 'R');

	}//end of function Sub_Total

#edited by Nick, 1/4/2014
	function Pf_Sub_Total($total,$claim,$excess,$discount,$role){

//		$this->Cell(GEN_COL01, 4, " ", "", 0, '');
		$this->Cell($this->in2mm(GEN_COL02), 4, "Sub-Total(".$role.")", "", 0, 'R');

		//Actual charges
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL03_WIDTH, 4,number_format($total, 2, '.', ','), "T", 0, 'R');

		//Discount
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL04_WIDTH, 4, number_format($discount, 2, '.', ','), "T", 0, 'R');

		//Insurance Coverage
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL05_WIDTH, 4, number_format($claim, 2, '.', ','), "T", 0, 'R');

		//Excess
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL06_WIDTH, 4, number_format($excess, 2, '.', ','), "T", 1, 'R');
	}// end of function Pf_Sub_Total()

	function  Pf_Totals(){

		$this->Cell($this->in2mm(GEN_COL02), 4, "Sub-Total", "", 0, 'R');
		$this->hdata['pf_excess'] = $this->hdata['pf_charge'] - ($this->hdata['pf_discount'] + $this->hdata['pf_coverage']);
		//Actual charges
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL03_WIDTH, 4,number_format($this->hdata['pf_charge'], 2, '.', ','), "T", 0, 'R');

		//Discount
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL04_WIDTH, 4, number_format($this->hdata['pf_discount'], 2, '.', ','), "T", 0, 'R');

		//Insurance Coverage
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL05_WIDTH, 4, number_format($this->hdata['pf_coverage'], 2, '.', ','), "T", 0, 'R');

		//Excess
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL06_WIDTH, 4, number_format($this->hdata['pf_excess'], 2, '.', ','), "T", 1, 'R');
	}

#edited by Nick, 1/4/2014
	function Totals(){
		$this->Ln(4);
//		$this->Cell(GEN_COL01, 4, " ", "", 0, '');
		$this->Cell($this->in2mm(GEN_COL02), 4, "T O T A L", "", 0, '');

//		$totalActualCharge = $this->subTotal_ActualCharge + $this->pfSubTotal_ActualCharge;
//		$totalMedicare = $this->subTotal_Medicare + $this->pfSubTotal_Medicare;
//		$totalExcess = $this->subTotal_Excess + $this->pfSubTotal_Excess;

		$totalActualCharge = 0;
		$t_discount        = 0;
		$totalMedicare     = 0;
		$totalExcess       = 0;

/*		foreach($this->totalCharge as $v)
			$totalActualCharge += round($v, 2);

		foreach($this->totalDiscount as $v)
			$t_discount += round($v, 2);

		foreach($this->totalCoverage as $v)
			$totalMedicare += round($v, 2);*/

//		foreach($this->totalExcess as $v)
//			$totalExcess += round($v, 0);
		//$totalExcess = $totalActualCharge - $t_discount - $totalMedicare;

		$this->SetFont("Times", "B", "11");

		$total_coverage = $this->hdata['pf_coverage'] +  $this->hdata['serv_coverage']; 
		$total_serv = $this->serv_total + $this->hdata['pf_charge'];
		$totalexcess = $total_serv - ($this->hdata['total_discount'] + $total_coverage);
		//Actual charges
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL03_WIDTH, 4,number_format($total_serv, 2, '.', ','), "T", 0, 'R');

		//Discount
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL04_WIDTH, 4, number_format($this->hdata['total_discount'], 2, '.', ','), "T", 0, 'R');

		//Insurance Coverage
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL05_WIDTH, 4, number_format($total_coverage, 2, '.', ','), "T", 0, 'R');

		//Excess
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL06_WIDTH, 4, number_format($totalexcess /*($this->serv_total + $this->hdata['pf_charge']) - $this->hdata['total_coverage']*/, 2, '.', ','), "T", 1, 'R');

//		$this->SetFont("Times", "", "10");
        //added by jasper 04/08/2013
        $prevbill_amt = $this->PreviousBill($this->encounter_nr, $this->bill_ref_nr);
		$this->Less($hdata['pfEX']);

		
		//"SPMC-F-BIL-13"
	}//end of function Totals()

    //added by jasper 04/08/2013
    function PreviousBill ($enc_nr, $bill_nr) {
        //echo $enc_nr . "//" . $bill_nr;
        $objbillinfo = new BillInfo();
        $tot_prevbill_amt = 0;
        $result = $objbillinfo->getPreviousBillAmt($enc_nr, $bill_nr);
        //echo $result;
        if ($result) {
            while ($row = $result->FetchRow()) {
                $n_bill = 0;
                if (!empty($row["total_charge"])) $n_bill = $row["total_charge"];
                if (!empty($row["total_coverage"])) $n_bill -= $row["total_coverage"];
                if (!empty($row["total_computed_discount"])) $n_bill -= $row["total_computed_discount"];
                if (!empty($row["total_discount"]) && ($n_bill > 0)) $n_bill -= ($n_bill * $row["total_discount"]);
                $tot_prevbill_amt += $n_bill;
            }
        }
        //echo $enc_nr . "//" . $bill_nr . "//" . $tot_prevbill_amt;
        $this->prev_bill_amt = $tot_prevbill_amt;

        if ($tot_prevbill_amt>0) {
            $this->SetFont("Times", "B", "11");
            $this->Ln(2);
    //        $this->Cell(GEN_COL01, 4, "", "", 0, '');
            $this->Cell($this->in2mm(GEN_COL02), 4, "Add :","", 0, '');

            $this->Ln(4);
            $this->SetFont("Times", "", "11");
    //        $this->Cell(GEN_COL02_D, 4, "", "", 0, '');
            $this->Cell(GEN_COL01, 4, "", "", 0, '');
            $this->Cell($this->in2mm(GEN_COL02)-6, 4, "Previous Bill Amount","", 0, '');

            $this->SetFont("Times", "B", "11");

            $this->Cell(COL_MID + COL03_WIDTH + COL_MID + COL04_WIDTH + COL_MID + COL05_WIDTH, 4, " ", "", 0, 'R');

            $this->Cell(COL_MID+2, 4, " ", "", 0, '');
            $this->Cell(COL06_WIDTH, 4, number_format(round($tot_prevbill_amt), 2, '.', ','), "T", 1, 'R');
        }
    }

#edited by Nick, 1/4/2014
	function Less($totalExcess){
		$this->SetFont("Times", "B", "11");
		$this->Ln(2);
		$this->Cell($this->in2mm(GEN_COL02), 4, "Less :","", 0, '');

		$deposit = $this->objBill->getPreviousPayments();
		$this->Ln(4);
        if (!is_null($deposit) && $deposit > 0) {
			$this->SetFont("Times", "", "11");
			$this->Cell(GEN_COL01, 4, "", "", 0, '');
			$this->Cell($this->in2mm(GEN_COL02)-6, 4, "Previous Payment (DEPOSIT)","", 0, '');
			$this->Cell(COL_MID + COL03_WIDTH + COL_MID + COL04_WIDTH + COL_MID + COL05_WIDTH + 1.5, 4, "", "", 0, 'R');
			$this->SetFont("Times", "", "11");
	        $this->Cell(COL_MID, 4, " ", "", 1, '');
        }

        foreach ($this->objBill->prev_payments as $val) {
            $this->Cell(GEN_COL01, 4, "", "", 0, '');
            $this->Cell($this->in2mm(GEN_COL02)-6, 4, "    OR#: " .$val->getORNo(),"", 0, '');
            $this->Cell(COL_MID + COL03_WIDTH + COL_MID + COL04_WIDTH + COL_MID + COL05_WIDTH + 1.5, 4, "", "", 0, 'R');
            $this->Cell(COL_MID, 4, " ", "", 0, '');
            $this->Cell(COL06_WIDTH, 4, number_format($val->getAmountPaid(), 2, '.', ','), "", 1, 'R');
        }

        #Commented By Jarel 02/17/2014
/*        $totalOBpayments = $this->objBill->getOBAnnexPayment(); //TODO8
        $deposit += $totalOBpayments;
        if (!is_null($totalOBpayments)) {
            $this->SetFont("Times", "", "11");
            $this->Cell(GEN_COL01, 4, "", "", 0, '');                                          
            $this->Cell($this->in2mm(GEN_COL02)-6, 4, "Previous Payment (Co-Payment)","", 1, '');
            foreach ($this->objBill->ob_payments as $val) {
                $this->Cell(GEN_COL01, 4, "", "", 0, '');
                $this->Cell($this->in2mm(GEN_COL02)-6, 4, "    OR#: " .$val->getORNo(), "", 0, '');
                $this->Cell(COL_MID + COL03_WIDTH + COL_MID + COL04_WIDTH + COL_MID + COL05_WIDTH + 1.5, 4, "", "", 0, 'R');
				$this->Cell(COL_MID, 4, " ", "", 0, '');
                $this->Cell(COL06_WIDTH, 4, number_format($val->getAmountPaid(),2,'.',',') ,"", 1, 'R');
            }
        }*/

        $isInfirmaryOrDependent = $this->objBill->isInfirmaryOrDependent($this->encounter_nr); // added by Nick, 4/8/2014

        $totalDiscount = $this->hdata['total_discount'];
        if ($this->objBill->isSponsoredMember() || $this->objBill->checkIfPHS($this->encounter_nr) || $this->objBill->isHSM() || $isInfirmaryOrDependent) {  // edited by Nick, 4/8/2014
        	if($this->objBill->isSponsoredMember()){
        		$label = "SPONSORED - NO BALANCE BILLING";
        	}elseif ($this->objBill->isHSM()) {
        		$label = "HOSPITAL SPONSORED MEMBER";
        	}else{
        		$label = "INFIRMARY DISCOUNT";
        	}

	       	// added by Nick, 4/8/2014s
        	switch (mb_strtoupper($isInfirmaryOrDependent)) {
	        	case 'INFIRMARY':
	        			$label = "INFIRMARY DISCOUNT";
	        			$temp_discount = $this->hdata['pf_excess'] + $this->hdata['serv_excess'];
	        		break;
	        	case 'DEPENDENT':
	        			$label = "INFIRMARY DISCOUNT (DEPENDENT)";
	        			$temp_discount = $this->hdata['serv_excess'];
	        		break;
	        }

        	$this->SetFont("Times", "B", "11");
            $this->Ln(4);
            $this->Cell(GEN_COL01, 4, "", "", 0, '');
            $this->Cell($this->in2mm(GEN_COL02)-6, 4, $label, "", 0, '');
            $this->Cell(COL_MID + COL03_WIDTH + COL_MID + COL04_WIDTH + COL_MID + COL05_WIDTH + 1.5, 4, "", "", 0, 'R');
            $this->SetFont("Times", "B", "11");
            $this->Cell(COL_MID, 4, " ", "", 0, '');
            $total_coverage = $this->hdata['pf_coverage'] +  $this->hdata['serv_coverage'];
            
            if($isInfirmaryOrDependent){
            	$netExcess = $temp_discount;
	            $this->Cell(COL06_WIDTH, 4, number_format($netExcess, 2, '.', ','), "", 0, 'R');
	            $netcharges = ($this->hdata['pf_excess'] + $this->hdata['serv_excess']) - $temp_discount - $deposit ;
            }else{
            	$netExcess = ($this->hdata['total_excess'] + $this->prev_bill_amt) - (round($deposit, 0) + round($totalDiscount, 0) + round($this->prev_bill_amt, 0));
	            $this->Cell(COL06_WIDTH, 4, number_format($netExcess + $totalDiscount + $this->prev_bill_amt, 2, '.', ','), "", 0, 'R');
	            $netcharges  = 0.00;
            }
            //end nick
        } else {
        	//$total_coverage = $this->hdata['pf_coverage'] +  $this->hdata['serv_coverage'];
            //$netcharges = ((($this->serv_total + $this->hdata['pf_charge']) - $total_coverage) + $this->prev_bill_amt) - (round($deposit, 0) + round($totalDiscount, 0));
            $netcharges = ($this->hdata['pf_excess'] + $this->hdata['serv_excess']) - $deposit;
  		}
        
        $this->excess= $netcharges;

		$this->SetFont("Times", "B", "11");
		$this->Ln(6);
		$this->Cell($this->in2mm(GEN_COL02), 4, "AMOUNT DUE :","", 0, '');
		$this->SetFont("Times", "B", "13");
		$this->Cell(COL_MID + COL03_WIDTH + COL_MID + COL04_WIDTH + COL_MID + COL05_WIDTH, 4, " ", "", 0, 'R');
        $this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL06_WIDTH, 4, number_format($netcharges, 2, '.', ','), "T", 1, 'R');
		$this->Cell($this->in2mm(GEN_COL02), 4, "","", 0, '');
		$this->Cell(COL_MID + COL03_WIDTH + COL_MID + COL04_WIDTH + COL_MID + COL05_WIDTH, 4, " ", "", 0, 'R');
		$this->Cell(COL_MID, 4, "", "", 0, '');
		$this->Cell(COL06_WIDTH + 1, 4, str_repeat("=", 14), "", 1, 'R');
        $this->SetFont("Times", "B", "13");
		$this->ReportFooter();
	}

	function getBillingClerk($slogin_id,$enc,$bill_nr) {
		global $db;

		$sname = '';

        #edited by VAN 02-22-2013
        $with_bill = 0;
        $this->clerk_italized = 0;
        #$strSQL1 = "Select create_id from seg_billing_encounter where encounter_nr ='".$enc."' and is_final = 1";
        $strSQL1 = "Select create_id from seg_billing_encounter where encounter_nr ='".$enc."' AND bill_nr='".$bill_nr."'";
        if ($result1 = $db->Execute($strSQL1)){
            if($result1->RecordCount()){
                $row1= $result1->FetchRow();
                $log_id = $row1['create_id'];
                $with_bill = 1;
            }else{
                $log_id = $slogin_id;
            }
        }

      #edited by VAN 02-22-2013
      #add that the billing clerk must be in billing dept when there is no SAVED BILL yet else the billing clerk is "NO FINAL BILL YET"
      $strSQL = "select pa.location_nr, cp.name_last, cp.name_first, cp.name_middle ".
						"   from care_person as cp inner join (care_users as cu inner join care_personell as cper ".
						"      on cu.personell_nr = cper.nr) on cper.pid = cp.pid ".
                        " INNER JOIN care_personell_assignment pa ON pa.personell_nr=cper.nr ".
						"   where login_id = '".$log_id."'".
                        "  AND cper.STATUS NOT IN ('deleted','hidden','inactive','void')  ";

        if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				$row = $result->FetchRow();
//				$objb = new BillInfo();
//				$sname = $objb->concatname($row["name_last"], $row["name_first"], $row["name_middle"]);
                #added by VAN 02-22-2013
                #display only clerk from billing dept
				if ($row['location_nr']=='152'){
                    $sname = strtoupper($row["name_first"] . (is_null($row["name_middle"]) || ($row["name_middle"] == '') ? " " : " ".substr($row["name_middle"],0,1).". ").$row["name_last"]);
                }else{
                    $this->clerk_italized = 1;
                    if ($with_bill)
                        $sname = "NO NAME BE DISPLAYED, NOT A BILLING CLERK";
                    else
                        $sname = "NOT A BILLING CLERK and NO SAVE BILL YET";
                }
			}
		}

		$this->clerk_name = $sname;
	}

	function getBillingHead() {
		global $db;

		$shname = '';
		$shpos  = '';

        //added by VAN 02-14-2013
        //add AND cper.status NOT IN ('void','hidden','deleted','inactive')
		$strSQL = "select cp.name_last, cp.name_first, cp.name_middle, cper.job_position, cper.other_title ".
						"   from care_person as cp inner join (((care_personell as cper inner join care_personell_assignment as cpa ".
									"      on cper.nr = cpa.personell_nr) inner join care_department as cd on cpa.location_nr = cd.nr) ".
									"      inner join care_role_person as crp on cpa.role_nr = crp.nr) on cp.pid = cper.pid ".
									"   where upper(crp.role) regexp 'HEAD' and upper(cd.id) regexp 'BILLING' ".
                                    " AND cper.status NOT IN ('void','hidden','deleted','inactive') ".
									"   limit 1";
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				$row = $result->FetchRow();

//				$objb = new BillInfo();
//				$shname = $objb->concatname($row["name_last"], $row["name_first"], $row["name_middle"]);
                $row["other_title"] = trim($row["other_title"]);
                $shname = strtoupper($row["name_first"] . (is_null($row["name_middle"]) || ($row["name_middle"] == '') ? " " : " ".substr($row["name_middle"],0,1).". ").$row["name_last"]). ( ( ($row["other_title"] != '') && !is_null($row["other_title"]) ) ? ", ".$row["other_title"] : "" );

				$shpos  = $row["job_position"];
			}
		}

		$this->head_name = $shname;
		$this->head_position = $shpos;
	}

//	function getSuppliesData(){
//		$this->objBill->getSuppliesList(); // gathered all supplies consumed
//		$this->objBill->getSupplyBenefits();
//		$this->objBill->getConfineBenefits('MS', 'S');
//
//		$totalSupConfineCoverage = $this->objBill->getSupConfineCoverage();
//		$supBenefitsArray = $this->objBill->getSupConfineBenefits();
//		$ndiscount        = $this->objBill->getBillAreaDiscount('MS','S');
//
//		$this->Ln(2);
//		$this->Cell(GEN_COL01, 4, "", "", 0, 'C');
//		$this->Cell($this->in2mm(GEN_COL02), 4,"Supplies", "", ($this->IsDetailed && (count($supBenefitsArray) > 0)) ? 1 : 0, '');
//
//		if(is_array($supBenefitsArray)){
//			foreach($supBenefitsArray  as $key=>$value){
//				$acPrice = number_format($value->item_charge, 2, '.', ',');
//				$price   = number_format($value->item_price, 2, '.', ',');
//
//				if ($this->IsDetailed){
//					$this->Cell(GEN_COL02_D, 4, "", "", 0, '');
//					$this->Cell(GEN_COL01, 4, "", "", 0, '');
//					$stmp = ($value->getItemQty() > 1 ? "s" : "");
//
//					$this->Cell($this->in2mm(GEN_COL02)-6, 4, $value->artikelname." ".$value->getItemQty()." pc".$stmp." @ ".number_format($price, 2, '.', ','), "", 0, '');
//					$this->Cell(COL_MID, 4, "", "", 0, '');
//					$this->Cell(COL03_WIDTH, 4, number_format($acPrice, 2, '.', ','), "", 1, 'R');
//				}
//			}
//		}

//		$TotalSupCharge = $this->objBill->getTotalSupCharge();
//		$totalExcess = $TotalSupCharge - $ndiscount - $totalSupConfineCoverage;
//
//		if ($this->IsDetailed && (count($supBenefitsArray) > 0)){
//			$this->Cell(22, 4, "", "", 0, '');
//			$this->Cell(GEN_COL01 + $this->in2mm(GEN_COL02), 4, "", "", 0, 'R');
//			$this->Cell($this->in2mm(GEN_COL02), 4, "", "", 0, 'R');
//			$this->Cell(COL_MID, 4, "", "", 0, '');
//			$this->Cell(COL03_WIDTH, 4, str_repeat("-", 25), "", 0, 'R');
//			$this->Cell(COL_MID, 4, "", "", 0, '');
//			$this->Cell(COL04_WIDTH, 4, str_repeat("-", 20), "", 0, 'R');
//			$this->Cell(COL_MID, 4, "", "", 0, '');
//			$this->Cell(COL05_WIDTH, 4, str_repeat("-", 23), "", 0, 'R');
//			$this->Cell(COL_MID, 4, "", "", 0, '');
//			$this->Cell(COL06_WIDTH, 4, str_repeat("-", 23), "", 1, 'R');
//
//			$this->Cell(22, 4, "", "", 0, '');
//			$this->Cell(GEN_COL01 + $this->in2mm(GEN_COL02), 4, "Sub-Total (Supplies)", "", 0, 'R');
//			$this->Cell($this->in2mm(GEN_COL02), 4, "Sub-Total (Supplies)", "", 0, 'R');
//		}
//
//		$this->Cell(COL_MID, 4, "", "", 0, '');
//		$this->Cell(COL03_WIDTH, 4, number_format($TotalSupCharge, 2, '.', ','), "", 0, 'R');
//		$this->Cell(COL_MID, 4, "", "", 0, '');
//		$this->Cell(COL04_WIDTH, 4, number_format($ndiscount, 2, '.', ','), "", 0, 'R');
//		$this->Cell(COL_MID, 4, "", "", 0, '');
//		$this->Cell(COL05_WIDTH, 4, number_format($totalSupConfineCoverage, 2, '.', ','), "", 0, 'R');
//		$this->Cell(COL_MID, 4, "", "", 0, '');
//		$this->Cell(COL06_WIDTH, 4, number_format($totalExcess, 2, '.', ','), "", 0, 'R');
//		$this->Ln(4);
//
//		$this->totalCharge[SP_AREA] = $TotalSupCharge;
//		$this->totalDiscount[SP_AREA] = $ndiscount;
//		$this->totalCoverage[SP_AREA] = $totalSupConfineCoverage;
//		$this->totalExcess[SP_AREA] = $totalExcess;
//
//	}// end of function getSuppliesData

#added by Nick, 1/3/2014
#edited by Nick, 1/4/2014
	var $total_meds=0;
	function getMedicinesData(){
		$data = array();
		$index = 0;

		$result = $this->objBill->getMedsList();
		if($result){
			while($row=$result->FetchRow()){
				$data[$index] = array("refno"=>$row['refno'],
						      "bestellnum"=>$row['bestellnum'],
						      "artikelname"=>$row['artikelname'],
						      "flag"=>$row['flag'],
						      "qty"=>$row['qty'],
						      "srv_price"=>number_format($row['price'], 2, '.', ','),
						      "itemcharge"=>number_format($row['itemcharge'], 2, '.', ','),
						      "source"=>$row['source'],
						      "total"=>$row['qty']*$row['price'],
						     );
				$index++;
			}
		}
		// echo $this->objBill->sql;exit();
		// echo json_encode($data);

		$total = 0;

		foreach ($data as $data_key => $data_value) {
			$total += $data_value['total'];
		}
		$this->total_meds = $total;

		if($this->IsDetailed){
			if(count($data)>0){
				$this->Cell($this->in2mm(GEN_COL02), 4,"Drugs & Medicines", "",1, '');
				foreach ($data as $data_key => $data_value) {
					$this->Cell(GEN_COL01, 4, "", "", 0, '');
					$this->Cell($this->in2mm(GEN_COL02)-8, 4, $data_value['artikelname'], "", 1, '');
					$this->Cell(GEN_COL01 + 3.5, 4, "", "", 0, '');
					$this->Cell($this->in2mm(GEN_COL02)-10, 4, $data_value['qty']." @ ".$data_value['srv_price']."  (".$data_value['group_desc'].")", "", 0, '');
					$this->Cell(COL_MID - 1.75, 4, "", "", 0, '');
					$this->Cell(COL03_WIDTH + 4, 4, number_format($data_value['total'], 2, '.', ','), "", 1, 'R');
				}
				$this->Cell($this->in2mm(GEN_COL02), 4, " ", "", 0, 'R');
				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL03_WIDTH, 4, str_repeat("-", 25), "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL04_WIDTH, 4, ""/*str_repeat("-", 20)*/, "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL05_WIDTH, 4, ""/*str_repeat("-", 23)*/, "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL06_WIDTH, 4, ""/*str_repeat("-", 23)*/, "", 1, 'R');

				$this->Cell($this->in2mm(GEN_COL02), 4, "Sub-Total (Drugs & Medicines)", "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL03_WIDTH, 4, number_format($total, 2, '.', ','), "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL04_WIDTH, 4, ""/*number_format($ndiscount, 2, '.', ',')*/, "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL05_WIDTH, 4, ""/*number_format($total_confine_coverage, 2, '.', ',')*/, "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL06_WIDTH, 4, ""/*number_format($excess, 2, '.', ',')*/, "", 1, 'R');
				$this->Cell(COL_MID, 4, "", "", 1, '');
			}else{
				$this->Cell($this->in2mm(GEN_COL02), 4,"Drugs & Medicines", "",0, '');
				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL03_WIDTH, 4, "0.00", "", 1, 'R');
			}
		}else{
			$this->Cell($this->in2mm(GEN_COL02), 4,"Drugs & Medicines", "",0, '');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL03_WIDTH, 4, number_format($total,2,'.',','), "", 1, 'R');
		}
	}//end of function getMedicinesData

	//added by Nick, 12/31/2013 3:43 AM
	//edited by Nick, 1/4/2014
	var $total_xlo=0;
	function getHospitalServiceData() {
		$data = array();
		$index = 0;

		$result = $this->objBill->getXLOList();
		if($result){
			while($row=$result->FetchRow()){		
				$data[$index] = array("srv_desc"=>$row['service_desc'],
											"group_code"=>$row['group_code'],
											"group_desc"=>$row['group_desc'],
											"srv_price"=>number_format($row['serv_charge'], 2, '.', ','),
											"source"=>$row['source'],
											"qty"=>$row['qty'],
											"total"=>$row['qty'] * $row['serv_charge']
						                   );
				$index++;
			}
		}
// echo json_encode($data);

		$lab_count=0;
		$rad_count=0;
		$sup_count=0;
		$oth_count=0;
		$xlo_count=0;
		foreach ($data as $data_key => $data_value) {//foreach
			$total += $data_value['total'];

			if($data_value['source']=='LB' || $data_value['source']=='POC')
				$lab_count++;
			if($data_value['source']=='RD')
				$rad_count++;
			if($data_value['source']=='MS' || $data_value['source']=='SU')
				$sup_count++;
			if($data_value['source']=='OA')
				$oth_count++;
		}//foreach

		$this->total_xlo = $total;
		$xlo_count=$lab_count + $rad_count + $sup_count + $oth_count;
		$this->Cell($this->in2mm(GEN_COL02), 4,"X-Ray, Lab, & Others", "", ($xlo_count>0 && $this->IsDetailed)?1:0, '');
		if($xlo_count > 0){#if1
			if($this->IsDetailed){
				#laboratory -- laboratory -- laboratory
				if($lab_count>0){
					$this->Cell(GEN_COL01, 4, "", "", 0, '');
					$this->SetFont("Times", "B", "10");
					$this->Cell($this->in2mm(GEN_COL02)-6, 4, "Laboratory", "", ($lab_count>0)?1:0, '');
					$this->SetFont("Times","", "10");
					if($lab_count>0){
						foreach ($data as $data_key => $data_value) {
							if($data_value['source']=='LB'){
								$this->Cell(GEN_COL01, 4, "", "", 0, '');
								$this->Cell($this->in2mm(GEN_COL02)-8, 4, $data_value['srv_desc'], "", 1, '');
								$this->Cell(GEN_COL01 + 3.5, 4, "", "", 0, '');
								$this->Cell($this->in2mm(GEN_COL02)-10, 4, $data_value['qty']." @ ".$data_value['srv_price']."  (".$data_value['group_desc'].")", "", 0, '');
								$this->Cell(COL_MID - 1.75, 4, "", "", 0, '');
								$this->Cell(COL03_WIDTH + 4, 4, number_format($data_value['total'],2,'.',','), "", 1, 'R');
							}
						}
						$this->Cell(COL_MID, 4, "", "", 1, '');
					}else{
						$this->Cell(COL_MID+2, 4, "", "", 0, '');
						$this->Cell(COL03_WIDTH, 4, "0.00", "", 1, 'R');
						$this->Cell(COL_MID, 4, "", "", 1, '');
					}
				}
				#Radiology -- Radiology -- Radiology
				if($rad_count>0){
					$this->Cell(GEN_COL01, 4, "", "", 0, '');
					$this->SetFont("Times", "B", "10");
					$this->Cell($this->in2mm(GEN_COL02)-6, 4, "Radiology", "", ($rad_count>0)?1:0, '');
					$this->SetFont("Times","", "10");
					if($rad_count>0){
						foreach ($data as $data_key => $data_value) {
							if($data_value['source']=='RD'){
								$this->Cell(GEN_COL01, 4, "", "", 0, '');
								$this->Cell($this->in2mm(GEN_COL02)-8, 4, $data_value['srv_desc'], "", 1, '');
								$this->Cell(GEN_COL01 + 3.5, 4, "", "", 0, '');
								$this->Cell($this->in2mm(GEN_COL02)-10, 4, $data_value['qty']." @ ".$data_value['srv_price']."  (".$data_value['group_desc'].")", "", 0, '');
								$this->Cell(COL_MID - 1.75, 4, "", "", 0, '');
								$this->Cell(COL03_WIDTH + 4, 4, number_format($data_value['total'],2,'.',','), "", 1, 'R');
							}
						}
						$this->Cell(COL_MID, 4, "", "", 1, '');
					}else{
						$this->Cell(COL_MID+2, 4, "", "", 0, '');
						$this->Cell(COL03_WIDTH, 4, "0.00", "", 1, 'R');
						$this->Cell(COL_MID, 4, "", "", 1, '');
					}
				}
				#Supplies -- Supplies -- Supplies
				if($sup_count>0){
					$this->Cell(GEN_COL01, 4, "", "", 0, '');
					$this->SetFont("Times", "B", "10");
					$this->Cell($this->in2mm(GEN_COL02)-6, 4, "Supplies", "", ($sup_count>0)?1:0, '');
					$this->SetFont("Times", "", "10");
					if($sup_count>0){
						foreach ($data as $data_key => $data_value) {
							if($data_value['source']=='SU' || $data_value['source']=='MS'){
								$this->Cell(GEN_COL01, 4, "", "", 0, '');
								$this->Cell($this->in2mm(GEN_COL02)-8, 4, $data_value['srv_desc'], "", 1, '');
								$this->Cell(GEN_COL01 + 3.5, 4, "", "", 0, '');
								$this->Cell($this->in2mm(GEN_COL02)-10, 4, $data_value['qty']." @ ".$data_value['srv_price']."  (".$data_value['group_desc'].")", "", 0, '');
								$this->Cell(COL_MID - 1.75, 4, "", "", 0, '');
								$this->Cell(COL03_WIDTH + 4, 4, number_format($data_value['total'],2,'.',','), "", 1, 'R');
							}
						}
						$this->Cell(COL_MID, 4, "", "", 1, '');
					}else{
						$this->Cell(COL_MID+2, 4, "", "", 0, '');
						$this->Cell(COL03_WIDTH, 4, "0.00", "", 1, 'R');
						$this->Cell(COL_MID, 4, "", "", 1, '');
					}
				}
				#Others -- Others -- Others
				if($oth_count>0){
					$this->Cell(GEN_COL01, 4, "", "", 0, '');
					$this->SetFont("Times", "B", "10");
					$this->Cell($this->in2mm(GEN_COL02)-6, 4, "Others", "", ($oth_count>0)?1:0, '');
					$this->SetFont("Times", "", "10");
					if($oth_count>0){
						foreach ($data as $data_key => $data_value) {
							if($data_value['source']=='OA'){
								$this->Cell(GEN_COL01, 4, "", "", 0, '');
								$this->Cell($this->in2mm(GEN_COL02)-8, 4, $data_value['srv_desc'], "", 1, '');
								$this->Cell(GEN_COL01 + 3.5, 4, "", "", 0, '');
								$this->Cell($this->in2mm(GEN_COL02)-10, 4, $data_value['qty']." @ ".$data_value['srv_price']."  (".$data_value['group_desc'].")", "", 0, '');
								$this->Cell(COL_MID - 1.75, 4, "", "", 0, '');
								$this->Cell(COL03_WIDTH + 4, 4, number_format($data_value['total'],2,'.',','), "", 1, 'R');
							}
						}
						$this->Cell(COL_MID, 4, "", "", 1, '');
					}else{
						$this->Cell(COL_MID+2, 4, "", "", 0, '');
						$this->Cell(COL03_WIDTH, 4, "0.00", "", 1, 'R');
						$this->Cell(COL_MID, 4, "", "", 1, '');
					}
				}
				#--------------------------------------------
				$this->Cell($this->in2mm(GEN_COL02), 4, " ", "", 0, 'R');
				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL03_WIDTH, 4, str_repeat("-", 25), "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL04_WIDTH, 4, ""/*str_repeat("-", 20)*/, "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL05_WIDTH, 4, ""/*str_repeat("-", 23)*/, "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL06_WIDTH, 4, ""/*str_repeat("-", 23)*/, "", 1, 'R');

				$this->Cell($this->in2mm(GEN_COL02), 4, "Sub-Total (X-Ray, Lab, & Others)", "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL03_WIDTH, 4, number_format($total, 2, '.', ','), "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL04_WIDTH, 4, ""/*number_format($ndiscount, 2, '.', ',')*/, "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL05_WIDTH, 4, ""/*number_format($total_confine_coverage, 2, '.', ',')*/, "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL06_WIDTH, 4, ""/*number_format($excess, 2, '.', ',')*/, "", 1, 'R');
				$this->Cell(COL_MID, 4, "", "", 1, '');
			}else{
				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL03_WIDTH, 4, number_format($total, 2, '.', ','), "", 1, 'R');
			}
		}else{
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL03_WIDTH, 4, "0.00", "", 1, 'R');
		}
	}// end of function LaboratoryData
	//end by Nick

	#added by VAN 02-13-08

	function getRoomTypeAttachedInfo($type_nr, $src, $accHistArray, &$typeDesc, &$sRooms) {
		$sDesc  = '';
		$sRooms = '';
		foreach ($accHistArray as $key => $accHist) {
			if (($accHist->type_nr == $type_nr) && ($accHist->getSource() == $src)) {
				if ($sDesc == '') $sDesc = $accHist->getTypeDesc();
				$pos = strpos($sRooms, $accHist->getRoomNr());
				if ($pos === false) {
					if ($sRooms != '') $sRooms .= ', ';
					$sRooms .= $accHist->getRoomNr();
				}
			}
		}
		$typeDesc = $sDesc;
	}

function getDayDiff($isFirstAd,$to,$from,$bill_date){
	$tmpTo = strtotime($to);
	$tmpFrom = strtotime($from);
	$tmpBill = strtotime($bill_date);

	$tmpTo = strtotime(date('Y-m-d',$tmpTo));
	$tmpFrom = strtotime(date('Y-m-d',$tmpFrom));
	$tmpBill = strtotime(date('Y-m-d',$tmpBill));

	if ($tmpTo != $tmpFrom){
		if($tmpTo <=0){
			$output = round(($tmpBill - $tmpFrom) / 86400);
			if($output == 0)
				return 1;
			else if($output < 0)
				return 0;
			else
				return $output;
		}
		$output = round(($tmpTo - $tmpFrom) / 86400);
		if($output == 0)
			return 1;
		else if($output < 0)
			return 0;
		else
			return $output;
	}else{
		return 0;
	}
}

#added by Nick, 1/3/2014
#edited by Nick, 1/5/2014
	var $total_accomodation=0;
	var $mandatory_excess=0;
	function getAccommodationData() {
        //added by Nick,12/31/2013
        $data = array();
        $index = 0;
        
        if($this->death_date != ''){
        	$todate = $this->death_date;
        }else{
        	$todate = $this->bill_date;
        }

        if($this->objBill->isERPatient($this->encounter_nr) == 1)
        	return;

        $total = 0;
        $result = $this->objBill->getAccomodationList();
        $arr_accomodations = array();

        if($result->RecordCount() > 0)
        	$this->Cell($this->in2mm(GEN_COL02)+2, 4,"Accommodation", "",1, '');
        else{
        	$this->Cell($this->in2mm(GEN_COL02)+2, 4,"Accommodation", "",0, '');
        	$this->Cell(COL03_WIDTH, 4, "0.00", "", 1, 'R');
        }

        if($result->RecordCount() > 1){
        	while($row = $result->FetchRow()){
				array_push($arr_accomodations, $row);
			}
			
			$is_first_admission_day = true;
			$index = 0;
			foreach ($arr_accomodations as $key => $row){
				$this->accomodation_type = $row['accomodation_type'];
				if($row['source']=='AD'){
					if($is_first_admission_day){
						$date_to = $row['date_to'] . ' ' . $row['time_to'];
						$tmp_datetime = strtotime($this->objBill->getCaseDate($this->encounter_nr));

						if($tmp_datetime <= 0){
							$ahead_to = $arr_accomodations[$index+1]['date_from'] . ' ' . $arr_accomodations[$index+1]['time_from'];
							if(!isset($ahead_to)){
								$ahead_to = $bill_dte;
							}
						}

						$date_from = date('Y-m-d h:i:s', $tmp_datetime);
						$diff = $this->getDayDiff($is_first_admission_day,$date_to,$date_from,$todate);
						$row['days_stay'] = (($diff==0)? 1 : $diff);
				 		$is_first_admission_day = false;
					}else{
						$ahead_to = $arr_accomodations[$index+1]['date_from'] . ' ' . $arr_accomodations[$index+1]['time_from'];
						if(!isset($ahead_to)){
							$ahead_to = $bill_dte;
						}
						$date_from = $row['date_from'] . ' ' . $row['time_from'];
						$diff = $this->getDayDiff($is_first_admission_day,$date_to,$date_from,$todate);
						$row['days_stay'] = (($diff==0)? 1 : $diff);
					}
					if($row['days_stay'] > 0){
						$charges = $row['days_stay'] * $row['rm_rate'];
						$this->Cell(GEN_COL01, 4, "", "", 0, '');
						$this->Cell($this->in2mm(GEN_COL02)-6, 4, $row['name'], "", 1, '');
						$this->Cell(GEN_COL01+2, 4, "", "", 0, '');
						$this->Cell($this->in2mm(GEN_COL02)-8, 4,  $row['days_stay']." days & ".$row['hrs_stay']." hrs @ ".number_format($row['rm_rate'], 2, '.', ','), "", 0, '');
						$this->Cell(GEN_COL01, 4, "", "", 0, '');
						$this->Cell(COL03_WIDTH, 4, number_format($charges, 2, '.', ','), "", 1, 'R');
						$this->mandatory_excess += $row['days_stay'] * $row['mandatory_excess'];
						$total += $charges;
					}
				}else{
					if($row['days_stay'] > 0){
						$charges = $row['days_stay'] * $row['rm_rate'];
						$this->Cell(GEN_COL01, 4, "", "", 0, '');
						$this->Cell($this->in2mm(GEN_COL02)-6, 4, $row['name'], "", 1, '');
						$this->Cell(GEN_COL01+2, 4, "", "", 0, '');
						$this->Cell($this->in2mm(GEN_COL02)-8, 4,  $row['days_stay']." days & ".$row['hrs_stay']." hrs @ ".number_format($row['rm_rate'], 2, '.', ','), "", 0, '');
						$this->Cell(GEN_COL01, 4, "", "", 0, '');
						$this->Cell(COL03_WIDTH, 4, number_format($charges, 2, '.', ','), "", 1, 'R');
						$this->mandatory_excess += $row['days_stay'] * $row['mandatory_excess'];
						$total += $charges;
					}
				}
				$index++;
			}
        }else{
        	$is_first_admission_day = true;
        	while($row = $result->FetchRow()){
        		$this->accomodation_type = $row['accomodation_type'];
        		if($row['source']=='AD'){
        			$date_to = $row['date_to'] . ' ' . $row['time_to'];
				 	if($is_first_admission_day){
				 		$tmp_datetime = strtotime($this->objBill->getCaseDate($this->encounter_nr));
						$date_from = date('Y-m-d h:i:s', $tmp_datetime);
						$diff = $this->getDayDiff($is_first_admission_day,$date_to,$date_from,$todate);
						$row['days_stay'] = (($diff==0)? 1 : $diff);
				 		$is_first_admission_day = false;
	        		}else{
	        			$tmp_datetime = strtotime($row['date_from'] . ' ' . $row['time_from']);
	        			$date_from = date('Y-m-d h:i:s', $tmp_datetime);
	        			$diff = $this->getDayDiff($is_first_admission_day,$date_to,$date_from,$todate);
	 					$row['days_stay'] = (($diff==0)? 1 : $diff);
	        		}
	        		if($row['days_stay'] > 0){
						$charges = $row['days_stay'] * $row['rm_rate'];
						$this->Cell(GEN_COL01, 4, "", "", 0, '');
						$this->Cell($this->in2mm(GEN_COL02)-6, 4, $row['name'], "", 1, '');
						$this->Cell(GEN_COL01+2, 4, "", "", 0, '');
						$this->Cell($this->in2mm(GEN_COL02)-8, 4,  $row['days_stay']." days & ".$row['hrs_stay']." hrs @ ".number_format($row['rm_rate'], 2, '.', ','), "", 0, '');
						$this->Cell(GEN_COL01, 4, "", "", 0, '');
						$this->Cell(COL03_WIDTH, 4, number_format($charges, 2, '.', ','), "", 1, 'R');
						$this->mandatory_excess += $row['days_stay'] * $row['mandatory_excess'];
						$total += $charges;
					}
        		}else{
        			if($row['days_stay'] > 0){
						$charges = $row['days_stay'] * $row['rm_rate'];
						$this->Cell(GEN_COL01, 4, "", "", 0, '');
						$this->Cell($this->in2mm(GEN_COL02)-6, 4, $row['name'], "", 1, '');
						$this->Cell(GEN_COL01+2, 4, "", "", 0, '');
						$this->Cell($this->in2mm(GEN_COL02)-8, 4,  $row['days_stay']." days & ".$row['hrs_stay']." hrs @ ".number_format($row['rm_rate'], 2, '.', ','), "", 0, '');
						$this->Cell(GEN_COL01, 4, "", "", 0, '');
						$this->Cell(COL03_WIDTH, 4, number_format($charges, 2, '.', ','), "", 1, 'R');
						$this->mandatory_excess += $row['days_stay'] * $row['mandatory_excess'];
						$total += $charges;
					}
        		}
        	}
        	
        }

        $this->total_accomodation = $total;

        if($this->IsDetailed && $result->RecordCount() > 0){
			$this->Cell($this->in2mm(GEN_COL02), 4, " ", "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL03_WIDTH, 4, str_repeat("-", 25), "", 0, 'R');

			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL04_WIDTH, 4, ""/*str_repeat("-", 20)*/, "", 0, 'R');

			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL05_WIDTH, 4, ""/*str_repeat("-", 23)*/, "", 0, 'R');

			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL06_WIDTH, 4, ""/*str_repeat("-", 23)*/, "", 1, 'R');

			$this->Cell($this->in2mm(GEN_COL02), 4, "Sub-Total (Accomodation)", "", 0, 'R');

			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL03_WIDTH, 4, number_format($total, 2, '.', ','), "", 0, 'R');

			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL04_WIDTH, 4, ""/*number_format($ndiscount, 2, '.', ',')*/, "", 0, 'R');

			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL05_WIDTH, 4, ""/*number_format($total_confine_coverage, 2, '.', ',')*/, "", 0, 'R');

			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL06_WIDTH, 4, ""/*number_format($excess, 2, '.', ',')*/, "", 1, 'R');
			$this->Cell(COL_MID, 4, "", "", 1, '');
		}

	}// end of function getAccommodationData

	//added by Nick, 12/31/2013 4:40 AM
	//edited by Nick, 1/4/2014
	var $total_ops=0;
	function getOpsCharges(){
		$data = array();
		$index = 0;

		$this->objBill->getOpBenefits();
		$opsBenefitsArray = $this->objBill->hsp_ops_benefits;
		foreach ($opsBenefitsArray as $key=>$value) {
			$data[$index] = array("desc"=>$value->op_desc,
						          "rvu"=>$value->op_rvu,
						          "multiplier"=>$value->op_multiplier,
						          "total"=>$value->getOpCharge()
					             );
			$index++;
		}
// echo json_encode($data);exit();

		$total = 0;

		foreach ($data as $data_key => $data_value) {
			$total += $data_value['total'];
		}
		$this->total_ops = $total;

		if(count($data)>0){
			if($this->IsDetailed){
				$this->Cell($this->in2mm(GEN_COL02), 4,"Operating/Delivery Room", "",1, '');
				foreach ($data as $data_key => $data_value) {
					$this->Cell(GEN_COL01, 4, "", "", 0, '');
					$this->Cell($this->in2mm(GEN_COL02)-6, 4, $data_value['desc'], "", 0, '');
					$this->Cell(COL_MID+1.8, 4, "", "", 0, '');
					$this->Cell(COL03_WIDTH, 4, number_format($data_value['total'], 2, '.', ','), "", 1, 'R');
				}

				$this->Cell($this->in2mm(GEN_COL02), 4, " ", "", 0, 'R');
				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL03_WIDTH, 4, str_repeat("-", 25), "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL04_WIDTH, 4, ""/*str_repeat("-", 20)*/, "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL05_WIDTH, 4, ""/*str_repeat("-", 23)*/, "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL06_WIDTH, 4, ""/*str_repeat("-", 23)*/, "", 1, 'R');

				$this->Cell($this->in2mm(GEN_COL02), 4, "Sub-Total (Operating/Delivery Room)", "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL03_WIDTH, 4, number_format($total, 2, '.', ','), "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL04_WIDTH, 4, ""/*number_format($ndiscount, 2, '.', ',')*/, "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL05_WIDTH, 4, ""/*number_format($total_confine_coverage, 2, '.', ',')*/, "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL06_WIDTH, 4, ""/*number_format($excess, 2, '.', ',')*/, "", 1, 'R');
				$this->Cell(COL_MID, 4, "", "", 1, '');
			}else{
				$this->Cell($this->in2mm(GEN_COL02), 4,"Operating/Delivery Room", "",0, '');
				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL03_WIDTH, 4, number_format($total, 2, '.', ','), "", 1, 'R');
			}
		}else{
			$this->Cell($this->in2mm(GEN_COL02), 4,"Operating/Delivery Room", "",0, '');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL03_WIDTH, 4, number_format("0.00", 2, '.', ','), "", 1, 'R');
		}
	}
	//end by Nick

	//added by Nick, 12/31/2013 5:09 AM
	//edited by Nick, 1/4/2014
	var $total_misc = 0;
	function getMiscellaneousCharges() {
        $data = array();
		$index=0;
		$result = $this->objBill->getMiscList();
		if($result){
			while($row=$result->FetchRow()){		
				$data[$index] = array("name"=>$row['name'],
					                  "desc"=>$row['description'],
							          "qty"=>$row['qty'],
							          "chrg"=>($row['avg_chrg']*$row['qty'])
							         );
				$index++;
			}
		}
// echo json_encode($data);exit();

		$total = 0;

		foreach ($data as $data_key => $data_value) {
			$total += $data_value['chrg'];
		}
		$this->total_misc = $total;

		if(count($data)>0){
			if($this->IsDetailed){
				$this->Cell($this->in2mm(GEN_COL02), 4,"Miscellaneous", "",1, '');
				foreach ($data as $data_key => $data_value) {
					$this->Cell(GEN_COL01, 4, "", "", 0, '');
					$this->Cell($this->in2mm(GEN_COL02)-6, 4, $data_value['name'], "", 0, '');
					$this->Cell(COL_MID+1.8, 4, "", "", 0, '');
					$this->Cell(COL03_WIDTH, 4, number_format($data_value['chrg'], 2, '.', ','), "", 1, 'R');
				}

				$this->Cell($this->in2mm(GEN_COL02), 4, " ", "", 0, 'R');
				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL03_WIDTH, 4, str_repeat("-", 25), "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL04_WIDTH, 4, ""/*str_repeat("-", 20)*/, "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL05_WIDTH, 4, ""/*str_repeat("-", 23)*/, "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL06_WIDTH, 4, ""/*str_repeat("-", 23)*/, "", 1, 'R');

				$this->Cell($this->in2mm(GEN_COL02), 4, "Sub-Total (Miscellaneous)", "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL03_WIDTH, 4, number_format($total, 2, '.', ','), "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL04_WIDTH, 4, ""/*number_format($ndiscount, 2, '.', ',')*/, "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL05_WIDTH, 4, ""/*number_format($total_confine_coverage, 2, '.', ',')*/, "", 0, 'R');

				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL06_WIDTH, 4, ""/*number_format($excess, 2, '.', ',')*/, "", 1, 'R');
			}else{
				$this->Cell($this->in2mm(GEN_COL02), 4,"Miscellaneous", "",0, '');
				$this->Cell(COL_MID, 4, "", "", 0, '');
				$this->Cell(COL03_WIDTH, 4, number_format($total, 2, '.', ','), "", 1, 'R');
			}
		}else{
			$this->Cell($this->in2mm(GEN_COL02), 4,"Miscellaneous", "",0, '');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL03_WIDTH, 4, number_format("0.00", 2, '.', ','), "", 1, 'R');
		}
	}
	//end by Nick

	function getPersonInfo($encounter=''){
		global $db;

		if(!empty($encounter)){
			$this->encounter_nr = $encounter;
		}

// ---- Commented out by LST - 03102008 ---------------
//		$sql = "SELECT ce.*, cp.name_first, cp.name_middle, cp.name_last,
//       				cp.date_birth,
//						sb.brgy_name, sm.mun_name, sm.zipcode,
//						sp.prov_name, sr.region_name, sr.region_desc,  cd.id, cd.name_formal as dept_name,
//						ce.current_room_nr as room_no,cw.ward_id, cw.name as ward_name
//					FROM care_encounter AS ce
//						INNER JOIN care_person AS cp ON ce.pid = cp.pid
//							INNER JOIN seg_barangays AS sb ON cp.brgy_nr = sb.brgy_nr
//							INNER JOIN seg_municity AS sm ON sb.mun_nr = sm.mun_nr
//								  INNER JOIN seg_provinces AS sp ON sm.prov_nr = sp.prov_nr
//								  INNER JOIN seg_regions AS sr ON sp.region_nr = sr.region_nr
//							INNER JOIN care_department AS cd ON cd.nr = ce.consulting_dept_nr
//							INNER JOIN care_ward AS cw ON ce.current_ward_nr = cw.nr
//					WHERE ce.encounter_nr ='".$this->encounter_nr."'";

		$sql = "SELECT ce.*, cp.name_first, cp.name_middle, cp.name_last,
							cp.date_birth,
						sb.brgy_name, cp.street_name, sm.mun_name, sm.zipcode,
						sp.prov_name, sr.region_name, sr.region_desc,  cd.id, cd.name_formal as dept_name,
						ce.current_room_nr as room_no,cw.ward_id, cw.name as ward_name
					FROM (care_encounter AS ce
						INNER JOIN care_person AS cp ON ce.pid = cp.pid)
							left JOIN seg_barangays AS sb ON cp.brgy_nr = sb.brgy_nr
							left JOIN seg_municity AS sm ON cp.mun_nr = sm.mun_nr
									left JOIN seg_provinces AS sp ON sm.prov_nr = sp.prov_nr
									left JOIN seg_regions AS sr ON sp.region_nr = sr.region_nr
							left JOIN care_department AS cd ON cd.nr = ce.current_dept_nr
							left JOIN care_ward AS cw ON ce.current_ward_nr = cw.nr
					WHERE ce.encounter_nr ='".$this->encounter_nr."'";

		if($this->personData = $db->Execute($sql)){
			if($this->personData->RecordCount()){
				return $this->personData;
			}else{
				return FALSE;
			}
		}else{
			return false;// echo 'SQL - '.$sql;
		}
	}// end of getPersonInfo

	function ReportOut(){
		$this->Output();
	}

	function trimAddress($street, $brgy, $mun, $prov, $zipcode, &$s_addr1, &$s_addr2, &$s_addr3){
		$address = trim($street);
		$address1 = (!empty($address) && !empty($brgy)) ?  trim($address.", ".$brgy) : trim($address." ".$brgy);
		$s_addr1 = $address1;

//		$address2 =  (!empty($address1) && !empty($mun)) ? trim($address1.", ".$mun) : trim($address1." ".$mun);
		$address2 = trim($mun);
		$address3 =  (!empty($address2) && !empty($zipcode))? trim($address2." ".$zipcode) : $address2." ";

		$address4 = (!empty($address3) && !empty($prov))? trim($address3.", ".$prov) : trim($address3." ".$prov);
		$s_addr2  = $address4;
		$s_addr3  = '';

//		return $address4;
	}// end of  function trimAddress

	function setEncounter_nr($encounter){
		$this->encounter_nr = $encounter;
	}

	/*function setObjBill(){
		$this->objBill = new Billing($this->encounter_nr);
	}*/

	function setFontSize($size){
		$this->DEFAULT_FONTSIZE = $size;
	}

	function setFontType($type){
		$this->DEFAULT_FONTTYPE = $type;
	}

	function setFontStyle($style){
		$this->DEFAULT_FONTSTYLE = $style;
	}

	function setBorder($border){
		$this->WBORDER = $border;
	}

	function setAlignment($alignment){
		$this->ALIGNMENT = $alignment;
	}

	function setNewLine($newline){
		$this->NEWLINE = $newline;
	}

	function setReportTitle($title){
		$this->reportTitle = $title;
	}

	function in2mm($inches){
//		return $inches * (0.35/(1/72));
				return $inches * 25.4;
	}

}//end of class Bill_Pdf

# ----------------------------------------------------------------------------------------

if(isset($_GET['pid']) && $_GET['pid']) $pid = $_GET['pid'];
if(isset($_GET['encounter_nr']) && $_GET['encounter_nr']) $encounter_nr = $_GET['encounter_nr'];

# --- Added by LST 03102008 -- to make bill date consistent with bill date in browser window ...
if (isset($_GET['from_dt']) && $_GET['from_dt'])
	$frm_dte = strftime("%Y-%m-%d %H:%M:%S", $_GET['from_dt']);
else
	$frm_dte = "0000-00-00 00:00:00";

if (isset($_GET['bill_dt']) && $_GET['bill_dt'])
//	$bill_dte = $_GET['bill_dt'];
	$bill_dte = strftime("%Y-%m-%d %H:%M:%S", $_GET['bill_dt']);
else
	$bill_dte = "0000-00-00 00:00:00";

if (isset($_GET['nr']))
		$old_bill_nr = $_GET['nr'];
else
		$old_bill_nr = '';

//Instantiate BillPDF class
$pdfBill =  new BillPDF($encounter_nr, $bill_dte, $frm_dte, $old_bill_nr, true/*(isset($_GET['rcalc']) && ($_GET['rcalc'] == '1'))*/, $_GET['deathdate']);

$encobj = new Encounter();
$pdfBill->isphic = $encobj->isPHIC($encounter_nr);
$pdfBill->ishousecase = $encobj->isHouseCase($encounter_nr);

$pdfBill->objBill->getAccommodationType();

$s_accommodation = $pdfBill->objBill->getAccomodationDesc();/*strtoupper($pdfBill->objBill->getAccommodationDesc());*/ //TODO2
$pdfBill->setReportTitle(($pdfBill->IsDetailed ? "DETAILED " : "")."STATEMENT OF ACCOUNT".($s_accommodation == '' ? " - NO ACCOMMODATION" : ($pdfBill->ishousecase ? "" : " - ".$s_accommodation)));
$pdfBill->ReportTitle();

//print patient informatin
$pdfBill->PersonInfo();
//print title bar
$pdfBill->TitleHeader('summary');
#$pdfBill->TitleHeader('detailed');

//print data
$pdfBill->PrintData();
$pdfBill->Sub_Total();
$pdfBill->Professional_Fee();

$pdfBill->Totals();
//print to pdf format
$pdfBill->ReportOut();
?>
