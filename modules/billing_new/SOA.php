<?php
//created by Nick 08-08-2014
define('PHIC_ID', 18);
define('CHARITY_ACCOMMODATION_TYPE', '1');
define('PAYWARD_ACCOMMODATION_TYPE', '2');
define('FIRST_CASE_NUM', '1');
define('SECOND_CASE_NUM', '2');
define('FIRST_SECOND_CASE_NUM', '3');
#added by art 03/05/2015
$define_nbs = new Define_Config('NBS_PACKAGE_ID');
$define_nbs->get_value();
define('NBS_PACKAGE_ID', $define_nbs->get_value());
#end art

/**
 * Class SOA
 * @property Biling $Billing
 */
class SOA extends FPDF {

    var $encounter_nr,
        $bill_nr,
        $pid,
        $isDetailed,
        $isPaywardSettlement,
        $isDialysis,
        $tempbill_dte,
        $finalDiagnosis,
        $otherDiagnosis,
        $TOTAL_FIRST_CASE_PF,
        $TOTAL_SECOND_CASE_PF,
        $CLAIMED_FIRST_CASE_PF,
        $CLAIMED_SECOND_CASE_PF,
        $TOTAL_DISCOUNT_HCI,
        $TOTAL_FIRST_CASE_HCI,
        $TOTAL_SECOND_CASE_HCI,
        $CLAIMED_DISCOUNT_HCI,
        $CLAIMED_FIRST_CASE_HCI,
        $CLAIMED_SECOND_CASE_HCI,
        $GROUP_CASERATE_TOTAL_DISCOUNT,
        $GROUP_CASERATE_TOTAL_FIRST,
        $GROUP_CASERATE_TOTAL_SECOND,
        $Bill,
        $Billing,
        $oae = false,
        $widths = array(65, 25, 25, 50, 25);

    function __construct(){
        $this->encounter_nr = $_GET['encounter_nr'];
        $this->bill_nr = $_GET['nr'];
        $this->isDetailed = $_GET['IsDetailed'];
        $this->isPaywardSettlement = $_GET['isPaywardSettlement'];
        $this->hasBloodBorrowed = $_GET['has_blood_borrowed'];//added by arc 05/12/2016

        $this->getBillInfo();

        $this->isDialysis = $this->Billing->isDialysisPatient($this->encounter_nr);
        $this->finalDiagnosis = stripcslashes(strtoupper($this->Billing->getFinalDiagnosis($this->encounter_nr)));
        $this->otherDiagnosis = stripcslashes(strtoupper($this->Billing->getOtherDiagnosis($this->encounter_nr)));
        $this->dialysisDiagnosis = stripcslashes(strtoupper($this->Billing->getDialysisDiagnosis($this->encounter_nr)));
        $this->CLAIMED_FIRST_CASE_PF=0;
        $this->CLAIMED_SECOND_CASE_PF=0;
        $this->CLAIMED_DISCOUNT_HCI=0;
        $this->CLAIMED_FIRST_CASE_HCI=0;
        $this->CLAIMED_SECOND_CASE_HCI=0;
        $this->GROUP_CASERATE_TOTAL_DISCOUNT=0;
        $this->GROUP_CASERATE_TOTAL_FIRST=0;
        $this->GROUP_CASERATE_TOTAL_SECOND=0;
        $pg_size = array($this->toMillimeter(8.5), $this->toMillimeter(13));
        $this->FPDF("P", "mm", $pg_size);
        $this->AliasNbPages();
    }

    function Header(){
        $objInfo = new Hospital_Admin();
        if ($row = $objInfo->getAllHospitalInfo()) {
            $row['hosp_agency'] = strtoupper($row['hosp_agency']);
            $row['hosp_name'] = strtoupper($row['hosp_name']);
        }
        else {
            $row['hosp_country'] = "Republic of the Philippines";
            $row['hosp_agency'] = "DEPARTMENT OF HEALTH";
            $row['hosp_name'] = "DAVAO MEDICAL CENTER";
            $row['hosp_addr1'] = "JICA Bldg., JP Laurel Avenue, Davao City";
        }

        $this->Image('../../gui/img/logos/dmc_logo.jpg', 20, 10, 20, 20);
        $this->SetFont("Times", "B", "10");
        $this->Cell(0, 4, $row['hosp_country'], 0, 1, "C");
        $this->Cell(0, 4, $row['hosp_agency'], 0, 1, "C");
        $this->Cell(0, 4, $row['hosp_name'], 0, 1, "C");
        $this->SetFont("Times", "", "10");
        $this->Cell(0, 4, $row['hosp_addr1'], 0, 1, "C");
        $accommodation = $this->Bill->billInfo['accommodationDesc'];
        if ($this->isDetailed) {
            $reportTitle = 'DETAILED ';
        }
        $caseType = $this->Billing->getCaseTypeDesc($this->encounter_nr, $this->Bill->billInfo['bill_dte'], $this->Billing->prev_encounter);
        $reportTitle .= 'STATEMENT OF ACCOUNT';
        //updated by janken 11/14/2014
        if ($this->Bill->billInfo['accommodation_type']) {
            $fromRdu = ($this->isDialysis) ? ' (RDU)' : '';
            if ($this->Bill->billInfo['opd_type'] == '0')
                if($this->isDialysis&&$caseType=='PRIVATE CASE'){
                    $reportTitle .= " - Payward" . $fromRdu;
                }else{
                    $reportTitle .= ($this->Bill->billInfo['accommodation_type'] == 1) ? " - Service Ward" . $fromRdu : " - Payward" . $fromRdu;
                }
            else
                $reportTitle .= (($this->Bill->billInfo['opd_type']== 8) ? " - ONCO PAY" : (($this->Bill->billInfo['opd_type']== 7) ? " - ONCO SERVICE" : (($this->Bill->billInfo['opd_type']==6) ? " - HI SERVICE" : (($this->Bill->billInfo['opd_type']==5) ? " - ASU SERVICE" : (($this->Bill->billInfo['opd_type']==4) ? " - OPD PAY" : (($this->Bill->billInfo['opd_type']==3) ? " - HI PAY" : (($this->Bill->billInfo['opd_type']==2) ? " - ASU PAY" : " - OPD SERVICE")))))));
        } else {
            $reportTitle .= ' - NO ACCOMMODATION';
        }

        $this->SetFont("Times", "B", "10");
        $this->Ln();
        $this->Cell(0, 4, $reportTitle, 0, 1, "C");
        $this->Ln();
        $this->SetFont("Times", "", "10");
        $this->addWaterMark();
    }

    function Footer(){
        $this->SetY(-15);
        $this->SetFont('Arial', 'B', 8);
        $this->setX(10);
        $this->Cell(1, 4, "SPMC-F-BIL-11", "", 0, 'L');
        $this->setX(70);
        $this->Cell(1, 4, "Effectivity: January 15, 2018", "", 0, 'L');
        $this->setX(130);
        $this->Cell(1, 4, "Rev. 2", "", 0, 'L');
        $this->SetFont('Arial', 'I', 8);
        $this->setXY(100, -18);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . ' of {nb}', 0, 0, 'R');
    }

    function addBillInfo($caption,$value,$isLabel=true,$w=array(18,50),$b=false){
        $this->Cell($w[0], 4, $caption, 0, 0, 'L');
        $colon = ($isLabel) ? ": " : "  ";
        if ($b) $this->SetFont("Times", "B", "10");
        $this->Cell($w[1], 4, $colon . $value, 0, 0, 'L');
        $this->Cell(65, 10, "", 0, 0, 'L');
        $this->SetFont("Times", "", "10");
    }

    function setBillInfo(){
        $this->SetY(-1);
        $this->addBillInfo('Case #', $this->encounter_nr);
        $this->addBillInfo('Bill Ref. #', $this->bill_nr);
        $this->Ln(4);
        $this->addBillInfo('HRN', $this->Bill->encounterInfo['pid']);
        $this->addBillInfo('Date', date('M d, Y', strtotime($this->Bill->billInfo['bill_dte'])));
                $this->Ln(4);
        $patientName = $this->Bill->encounterInfo['name_last'] . ', ' . $this->Bill->encounterInfo['name_first'] . ' ' . $this->Bill->encounterInfo['name_middle'];
        $this->addBillInfo('Name', strtoupper($patientName));
        $this->addBillInfo('Dept', $this->Bill->encounterInfo['dept_name']);
        $this->Ln(4);
        $baranggay = trim($this->Bill->encounterInfo['brgy_name']);
        $municipality = trim($this->Bill->encounterInfo['mun_name']);
        $zipcode = trim($this->Bill->encounterInfo['zipcode']);
        $province = trim($this->Bill->encounterInfo['prov_name']);
        $this->addBillInfo('Address', strtoupper($baranggay), 'L');
        if(!$this->isDialysis)
        $this->addBillInfo('Admitted', date('M d, Y h:i a', strtotime($this->Bill->encounterInfo['encounter_date'])));
        $this->Ln(4);
        $this->addBillInfo('', strtoupper("$municipality $zipcode, $province"));
        $this->SetFont("Times", "B", "10");
        $this->addBillInfo('', $this->Bill->encounterInfo['isphic'] ? 'PHIC' : "No PHIC", false);
        $this->SetFont("Times", "", "10");
        $this->Ln(4);
        $room = $this->getRoom();
        $hasHighFlux = $this->hasHighFlux();
        $highFluxText = ($hasHighFlux) ? ' (HIGH FLUX) ' : ' ';
        $this->addBillInfo('Room #', $room['room_nr'] . ' ' . $room['ward_name'] . $highFluxText);
        if ($this->Bill->encounterInfo['isMedicoLegal']) {
            $this->SetFont("Times", "B", "10");
            $this->addBillInfo('', 'Medico Legal', false);
            $this->SetFont("Times", "", "10");
        }
        $this->Ln(4);
        if($this->isDialysis){
            $dialysis_obj = new SegDialysis();
            
            $this->Cell(22, 4, 'Session Date/s',0,0,'L');
            $colon = ": ";
            $rs = $dialysis_obj->getSessionDates($this->encounter_nr);
            $dates = '';
            $datecount = 0;
            $linecount = 0;
            while($row = $rs->FetchRow()){
                if($datecount == 0){
                    $dates = date('M d, Y', strtotime($row['transaction_date'])) . ' ';
                    $datecount++;
                }else if($datecount == 3){
                    $dates .= ', '.date('M d, Y', strtotime($row['transaction_date']));
                    $datecount = 0;

                    if($linecount > 0)
                        $this->Cell(24, 4, '',0,0,'L');

                    $this->Cell(50, 4, $colon . $dates,0,0,'L');
                    $this->SetFont("Times", "", "10");
                    $this->Ln(4);
                    $colon = "";
                    $dates = '';
                    $linecount++;
                }else{
                    $dates .= ', '.date('M d, Y ', strtotime($row['transaction_date']));
                    $datecount++;
                }
            }
            if($datecount > 0){
                if($linecount > 0)
                    $this->Cell(24, 4, '',0,0,'L');

                $this->Cell(50, 4, $colon . $dates,0,0,'L');
                $this->SetFont("Times", "", "10");
                $this->Ln(4);
            }
            
        }
        if ($this->Bill->encounterInfo['deathdate']) {
            $this->SetFont("Times", "B", "10");
            $this->addBillInfo('Death Date', date('M d, Y h:i a', strtotime($this->Bill->encounterInfo['deathdate'])));
            $this->SetFont("Times", "", "10");
            $this->Ln(4);
        }
        $series_nr = $this->Billing->getSeriesNumber($this->encounter_nr);
        if ($series_nr) {
            $this->addBillInfo('Series #', $series_nr);
            $this->Ln(4);
        }
        // if ($this->Bill->encounterInfo['isphic']) {
            if ($this->Bill->encounterInfo['first']) {
                $this->addBillInfo('First Case Rate',$this->Bill->encounterInfo['first'],true,array(30,-45),true); 
                if(!$this->Bill->encounterInfo['second']) $this->Ln(4);
            }
            if ($this->Bill->encounterInfo['second']) {
                $this->addBillInfo('Second Case Rate',$this->Bill->encounterInfo['second'],true,array(30,50),true); $this->Ln(4);
            }
            if ($this->finalDiagnosis) {
                $this->SetFont("Times", "", "10");
                $this->Cell(30, 4, "Final Diagnosis", 0, 0, 'L');
                $this->SetFont("Times", "B", "8");
                $this->Multicell(150, 4,": ".$this->finalDiagnosis, 0, 'L');
                $this->SetFont("Times", "", "10");
            }
            if ($this->otherDiagnosis) {
                $this->SetFont("Times", "", "10");
                $this->Cell(30, 4, "Other Diagnosis", 0, 0, 'L');
                $this->SetFont("Times", "B", "8");
                $this->Multicell(150, 4,": ".$this->otherDiagnosis, 0, 'L');
                $this->SetFont("Times", "", "10");  
            }
            if ($this->isDialysis) {
                $this->SetFont("Times", "", "10");
                $this->Cell(30, 4, "Final Diagnosis", 0, 0, 'L');
                $this->SetFont("Times", "B", "10");
                $this->Multicell(150, 4,": ".$this->dialysisDiagnosis, 0, 'L');
                $this->SetFont("Times", "", "10");
            }
            if ($this->oae) {
                $this->addBillInfo('Type of Hearing Test','Otoacoustic Emission (OAE)',true,array(30,50),true); $this->Ln(4);   
            }
        // }
        $this->Ln(4);
        
        $this->addDetailHeader();
    }//end function

    function addWaterMark(){
        /*if (!$this->Bill->billInfo['is_final']) {
            $this->Image('../../gui/img/logos/tentativebill.jpg',30, 50, 150,150);
            return true;
        }*/
        if ($this->isPaywardSettlement) {
            $this->Image('../../gui/img/logos/payward_settlement.jpg', 20, 60, 180, 180);
        } elseif (!$this->Bill->billInfo['is_final']) {
            $this->Image('../../gui/img/logos/tentativebill.jpg', 30, 50, 150, 150);
        }
    }

    function addDetailHeader(){
        $this->SetFont("Times", "", "11");
        $this->Cell($this->widths[0], 4, "Particulars", 'TB', 0, 'C');
        $this->Cell(2, 4, "", 0, 0, 'C');
        $this->Cell($this->widths[1], 4, "Actual Charges", 'TB', 0, 'C');
        $this->Cell(2, 4, "", 0, 0, 'C');
        $this->Cell($this->widths[2], 4, "Discount", 'TB', 0, 'C');
        $this->Cell(2, 4, "", 0, 0, 'C');
        $this->Cell($this->widths[3], 4, "Insurance/PHIC", 'TB', 0, 'C');
        $this->Cell(2, 4, "", 0, 0, 'C');
        $this->Cell($this->widths[4], 4, "Excess", 'TB', 1, 'C');
        $this->Ln(0);
        $this->Cell($this->widths[0]+$this->widths[1]+$this->widths[2]+6, 4, "", 0, 0, 'C');
        $this->Cell($this->widths[3]/2, 4, "1st Caserate", 'TB', 0, 'C');
        $this->Cell($this->widths[3]/2, 4, "2nd Caserate", 'TB', 0, 'C');
        // $this->Cell(, 4, "2nd Caserate", 'TB', 0, 'C');
        $this->Ln(4);
        $this->SetFont("Times", "", "10");
        $this->showBillDetails();
    }

    function showBillDetails(){
        $this->showAccommodation();
        $this->showXlo();
        $this->showMeds();
        $this->showOps();
        $this->showMisc();
    }

    function addGroup($title,$value=null){
        $DISCOUNT=null;
        $OUTPUT_1=null;
        $OUTPUT_2=null;
        if(isset($value)){
            $DISCOUNT=0;
            $OUTPUT_1=0;
            $OUTPUT_2=0;
            // if($this->CLAIMED_DISCOUNT_HCI!=$this->TOTAL_DISCOUNT_HCI){
                // if(($this->CLAIMED_DISCOUNT_HCI+$value)<=$this->TOTAL_DISCOUNT_HCI){
                //     $DISCOUNT=$value;
                //     $this->CLAIMED_DISCOUNT_HCI+=$value;
                // }else{
                //     $DISCOUNT=$this->TOTAL_DISCOUNT_HCI-$this->CLAIMED_DISCOUNT_HCI;
                //     $this->CLAIMED_DISCOUNT_HCI=$this->TOTAL_DISCOUNT_HCI;
                // }
            // }
            if($accommodation_type==CHARITY_ACCOMMODATION_TYPE&&!is_null($accommodation_mandatory_excess)){
                $DISCOUNT=$value-($accommodation_mandatory_excess*$accommodation_days);
            }else{
                $DISCOUNT=$value*$this->Bill->encounterInfo['applied_discount'];
            }
            $afterDiscount=$value-$DISCOUNT;
            if(($this->CLAIMED_FIRST_CASE_HCI+$afterDiscount)<=$this->TOTAL_FIRST_CASE_HCI){
                $OUTPUT_1=$afterDiscount;
                $this->CLAIMED_FIRST_CASE_HCI+=$afterDiscount;
            }else{
                $OUTPUT_1=$this->TOTAL_FIRST_CASE_HCI-$this->CLAIMED_FIRST_CASE_HCI;
                $this->CLAIMED_FIRST_CASE_HCI=$this->TOTAL_FIRST_CASE_HCI;
            }
            if($this->TOTAL_FIRST_CASE_HCI==$this->CLAIMED_FIRST_CASE_HCI){
                if(($this->CLAIMED_SECOND_CASE_HCI+($afterDiscount-$OUTPUT_1))<=$this->TOTAL_SECOND_CASE_HCI){
                    $OUTPUT_2=($afterDiscount-$OUTPUT_1);
                    $this->CLAIMED_SECOND_CASE_HCI+=($afterDiscount-$OUTPUT_1);
                }else{
                    $OUTPUT_2=$this->TOTAL_SECOND_CASE_HCI-$this->CLAIMED_SECOND_CASE_HCI;
                    $this->CLAIMED_SECOND_CASE_HCI=$this->TOTAL_SECOND_CASE_HCI;
                }
            }

            // if(($this->CLAIMED_FIRST_CASE_HCI+$value)<=$this->TOTAL_FIRST_CASE_HCI){
            //     $OUTPUT_1=$value;
            //     $OUTPUT_2=0;
            //     $this->CLAIMED_FIRST_CASE_HCI+=$value;
            // }else{
            //     if($this->CLAIMED_FIRST_CASE_HCI<$this->TOTAL_FIRST_CASE_HCI){
            //         $OUTPUT_1=$this->TOTAL_FIRST_CASE_HCI-$this->CLAIMED_FIRST_CASE_HCI;
            //         if(($value-$OUTPUT_1)>$this->TOTAL_SECOND_CASE_HCI){
            //             $OUTPUT_2=$this->TOTAL_SECOND_CASE_HCI;
            //             $this->CLAIMED_FIRST_CASE_HCI=$this->TOTAL_FIRST_CASE_HCI;
            //             $this->CLAIMED_SECOND_CASE_HCI=$this->TOTAL_SECOND_CASE_HCI;
            //         }else{
            //             $OUTPUT_2=($this->CLAIMED_FIRST_CASE_HCI+$value)-$this->TOTAL_FIRST_CASE_HCI;
            //             $this->CLAIMED_FIRST_CASE_HCI=$this->TOTAL_FIRST_CASE_HCI;
            //             $this->CLAIMED_SECOND_CASE_HCI+=$OUTPUT_2;
            //         }
            //     }else{
            //         if(($this->CLAIMED_SECOND_CASE_HCI+$value)<=$this->TOTAL_SECOND_CASE_HCI){
            //             $OUTPUT_1=0;
            //             $OUTPUT_2=$value;
            //             $this->CLAIMED_SECOND_CASE_HCI+=$value;
            //         }else{
            //             $OUTPUT_1=0;
            //             $OUTPUT_2=0;
            //         }
            //     }
            // }
        }
        $this->Cell($this->widths[0]+2, 4, $title, 0, 0, 'L');
        $this->Cell($this->widths[1]+2, 4, isset($value) ? number_format($value, 2) : '', 0, 0, 'R');
        $this->Cell($this->widths[2]+2, 4, isset($DISCOUNT) ? number_format($DISCOUNT, 2) : '', 0, 0, 'R');
        $this->Cell($this->widths[3]/2, 4, isset($OUTPUT_1) ? number_format($OUTPUT_1, 2) : '', 0, 0, 'R');
        $this->Cell($this->widths[3]/2, 4, isset($OUTPUT_2) ? number_format($OUTPUT_2, 2) : '', 0, 0, 'R');
        $this->Cell($this->widths[4], 4, isset($value) ? number_format(($value-($OUTPUT_1+$OUTPUT_2+$DISCOUNT)), 2) : '', 0, 1, 'R');
    }

    function addDetail($title,$value=null,$b=false,$wrap=false,$indent=0,$titleAlign='L'){
        $space = 0;
        for ($i = 0; $i < $indent; $i++) {
            $this->Cell(2, 4, '', 0, 0, $titleAlign);
            $space += 2;
        }
        $DISCOUNT=null;
        $OUTPUT_1=null;
        $OUTPUT_2=null;
        if(isset($value)){
            $DISCOUNT=0;
            $OUTPUT_1=0;
            $OUTPUT_2=0;
            if(!is_string($title)){
                $accommodation_type=$title[1];
                $accommodation_mandatory_excess=$title[2];
                $accommodation_days=$title[3];
                $title=$title[0];
            }
            // if($this->CLAIMED_DISCOUNT_HCI!=$this->TOTAL_DISCOUNT_HCI){
                // if(($this->CLAIMED_DISCOUNT_HCI+$value)<=$this->TOTAL_DISCOUNT_HCI){
                //     $DISCOUNT=$value;
                //     $this->CLAIMED_DISCOUNT_HCI+=$value;
                // }else{
                //     $DISCOUNT=$this->TOTAL_DISCOUNT_HCI-$this->CLAIMED_DISCOUNT_HCI;
                //     $this->CLAIMED_DISCOUNT_HCI=$this->TOTAL_DISCOUNT_HCI;
                // }
            // }
            if($accommodation_type==CHARITY_ACCOMMODATION_TYPE&&(!is_null($accommodation_mandatory_excess) && (!$this->Bill->encounterInfo['isphic']))){
                $DISCOUNT=$value-($accommodation_mandatory_excess*$accommodation_days);
            }else{
                $DISCOUNT=$value*$this->Bill->encounterInfo['applied_discount'];
            }
            $this->CLAIMED_DISCOUNT_HCI+=$DISCOUNT;
            $afterDiscount=$value-$DISCOUNT;
            if(($this->CLAIMED_FIRST_CASE_HCI+$afterDiscount)<=$this->TOTAL_FIRST_CASE_HCI){
                $OUTPUT_1=$afterDiscount;
                $this->CLAIMED_FIRST_CASE_HCI+=$afterDiscount;
            }else{
                $OUTPUT_1=$this->TOTAL_FIRST_CASE_HCI-$this->CLAIMED_FIRST_CASE_HCI;
                $this->CLAIMED_FIRST_CASE_HCI=$this->TOTAL_FIRST_CASE_HCI;
            }
            if($this->TOTAL_FIRST_CASE_HCI==$this->CLAIMED_FIRST_CASE_HCI){
                if(($this->CLAIMED_SECOND_CASE_HCI+($afterDiscount-$OUTPUT_1))<=$this->TOTAL_SECOND_CASE_HCI){
                    $OUTPUT_2=($afterDiscount-$OUTPUT_1);
                    $this->CLAIMED_SECOND_CASE_HCI+=($afterDiscount-$OUTPUT_1);
                }else{
                    $OUTPUT_2=$this->TOTAL_SECOND_CASE_HCI-$this->CLAIMED_SECOND_CASE_HCI;
                    $this->CLAIMED_SECOND_CASE_HCI=$this->TOTAL_SECOND_CASE_HCI;
                }
            }
            // if(($this->CLAIMED_FIRST_CASE_HCI+$value)<=$this->TOTAL_FIRST_CASE_HCI){
            //     $OUTPUT_1=$value;
            //     $OUTPUT_2=0;
            //     $this->CLAIMED_FIRST_CASE_HCI+=$value;
            // }else{
            //     if($this->CLAIMED_FIRST_CASE_HCI<$this->TOTAL_FIRST_CASE_HCI){
            //         $OUTPUT_1=$this->TOTAL_FIRST_CASE_HCI-$this->CLAIMED_FIRST_CASE_HCI;
            //         if(($value-$OUTPUT_1)>$this->TOTAL_SECOND_CASE_HCI){
            //             $OUTPUT_2=$this->TOTAL_SECOND_CASE_HCI;
            //             $this->CLAIMED_FIRST_CASE_HCI=$this->TOTAL_FIRST_CASE_HCI;
            //             $this->CLAIMED_SECOND_CASE_HCI=$this->TOTAL_SECOND_CASE_HCI;
            //         }else{
            //             $OUTPUT_2=($this->CLAIMED_FIRST_CASE_HCI+$value)-$this->TOTAL_FIRST_CASE_HCI;
            //             $this->CLAIMED_FIRST_CASE_HCI=$this->TOTAL_FIRST_CASE_HCI;
            //             $this->CLAIMED_SECOND_CASE_HCI+=$OUTPUT_2;
            //         }
            //     }else{
            //         if(($this->CLAIMED_SECOND_CASE_HCI+$value)<=$this->TOTAL_SECOND_CASE_HCI){
            //             $OUTPUT_1=0;
            //             $OUTPUT_2=$value;
            //             $this->CLAIMED_SECOND_CASE_HCI+=$value;
            //         }else{
            //             $OUTPUT_2=($this->CLAIMED_FIRST_CASE_HCI+$value)-$this->TOTAL_FIRST_CASE_HCI;
            //             $this->CLAIMED_FIRST_CASE_HCI=$this->TOTAL_FIRST_CASE_HCI;
            //             $this->CLAIMED_SECOND_CASE_HCI+=$OUTPUT_2;
            //         }
            //     }
            // }
        }
        $this->GROUP_CASERATE_TOTAL_DISCOUNT+=$DISCOUNT;
        $this->GROUP_CASERATE_TOTAL_FIRST+=$OUTPUT_1;
        $this->GROUP_CASERATE_TOTAL_SECOND+=$OUTPUT_2;
        if ($b)
            $this->SetFont("Times", "B", "10");
        if (!$wrap) {
            $this->Cell(3, 4, '', 0, 0, 'C');
            $this->Cell($this->widths[0] - ($space + 3)+2, 4, $title, 0, 0, $titleAlign);
            $this->SetFont("Times", "", "10");
            $this->Cell($this->widths[1]+2, 4, isset($value) ? number_format($value, 2) : '', 0, 0, 'R');
            $this->Cell($this->widths[2]+2, 4, isset($DISCOUNT) ? number_format($DISCOUNT, 2) : '', 0, 0, 'R');
            $this->Cell($this->widths[3]/2, 4, isset($OUTPUT_1) ? number_format($OUTPUT_1, 2) : '', 0, 0, 'R');
            $this->Cell($this->widths[3]/2, 4, isset($OUTPUT_2) ? number_format($OUTPUT_2, 2) : '', 0, 0, 'R');
            $this->Cell($this->widths[4], 4, isset($value) ? number_format(($value-($OUTPUT_1+$OUTPUT_2+$DISCOUNT)), 2) : '', 0, 1, 'R');
            // $this->Cell($this->widths[4], 4, '', 0, 1, 'R');
        } else {
            $index = 0;
            $limit = 40;
            $words = explode(' ', $title);
            $sentences = array();
            $sentence = "";
            foreach ($words as $key => $word) {
                if ((strlen($sentence) + strlen($word) + 1) < $limit) {
                    $sentence .= ' ' . $word;
                } else {
                    $sentences[] = $sentence;
                    $sentence = $word;
                }
                if ((count($words) - 1) <= $index) {
                    $sentences[] = $sentence;
                }
                $index++;
            }
            $index = 0;
            foreach ($sentences as $key => $sentence) {
                $ln = ((count($sentences) - 1) <= $index) ? 0 : 1;
                $this->Cell(3, 4, '', 0, 0, 'C');
                $this->Cell($this->widths[0] - ($space + 3)+2, 4, trim($sentence), 0, $ln, $titleAlign);
                $index++;
            }
            $this->SetFont("Times", "", "10");
            $this->Cell($this->widths[1]+2, 4, isset($value) ? number_format($value, 2) : '', 0, 0, 'R');
            $this->Cell($this->widths[2]+2, 4, isset($DISCOUNT) ? number_format($DISCOUNT, 2) : '', 0, 0, 'R');
            $this->Cell($this->widths[3]/2, 4, isset($OUTPUT_1) ? number_format($OUTPUT_1, 2) : '', 0, 0, 'R');
            $this->Cell($this->widths[3]/2, 4, isset($OUTPUT_2) ? number_format($OUTPUT_2, 2) : '', 0, 0, 'R');
            $this->Cell($this->widths[4], 4, isset($value) ? number_format(($value-($OUTPUT_1+$OUTPUT_2+$DISCOUNT)), 2) : '', 0, 1, 'R');
            // $this->Cell($this->widths[3]/2, 4, isset($totalCoverageFirst) ? number_format($totalCoverageFirst, 2) : '', $border, 0, 'R');
        }
    }

    function addGroupTotal($title,$totalCharge=null,$totalDiscount=null,$totalCoverageFirst=null,$totalCoverageSecond=null,$totalExcess=null,$border='R'){
        $this->Cell($this->widths[0], 4, '', 0, 0, 'R');
        $this->Cell($this->widths[1], 4, str_repeat("-", $this->widths[1] - 3), 0, 0, 'L');
        $this->Cell(5, 4, "", 0, 1, 'C');;

        $this->Cell(5, 4, '', 0, 0, 'C');
        $this->Cell($this->widths[0] - 5+2, 4, $title, 0, 0, 'R');
        $this->Cell($this->widths[1]+2, 4, isset($totalCharge) ? number_format($totalCharge, 2) : '', 0, 0, $border);
        $this->Cell($this->widths[2]+2, 4, isset($totalDiscount) ? number_format($totalDiscount, 2) : '', 0, 0, $border);
        $this->Cell($this->widths[3]/2, 4, isset($totalCoverageFirst) ? number_format($totalCoverageFirst, 2) : '', 0, 0, $border);
        $this->Cell($this->widths[3]/2, 4, isset($totalCoverageSecond) ? number_format($totalCoverageSecond, 2) : '', 0, 0, $border);
        $this->Cell($this->widths[4], 4, isset($totalExcess) ? number_format($totalExcess, 2) : '', 0, 1, $border);
    }

    function getSentences($text){

    }

    function addSubTotal($title,$totalCharge=null,$totalDiscount=null,$totalCoverageFirst=null,$totalCoverageSecond=null,$totalExcess=null,$border='T',$titleAlign='R',$indent=0,$b=array(0,0,0,0,0),$wrap=false){
        $space = 0;

        if ($b[0]) $this->SetFont("Times", "B", "10"); else $this->SetFont("Times", "", "10");

        if(strlen($title) > 27) $this->SetFont("Times", "", "9");

        if ($wrap) {
            $limit = 30;
            $words = explode(" ", $title);
            $sentences = array();
            $sentence = "";
            $index = 0;
            foreach ($words as $key => $word) {
                if (strlen("$sentence $word") < $limit) {
                    $sentence .= " $word";
                } else {
                    $sentences[] = $sentence;
                    $sentence = $word;
                }
                if ((count($words) - 1) <= $index) {
                    $sentences[] = $sentence;
                }
                $index++;
            }
            $index = 0;
            foreach ($sentences as $key => $sentence) {
                $space = 0;
                for ($i = 0; $i < $indent; $i++) {
                    $this->Cell(2, 4, '', 0, 0, $titleAlign);
                    $space += 2;
                }
                $ln = ((count($sentences) - 1) <= $index) ? 0 : 1;
                $this->Cell($this->widths[0] - $space, 4, $sentence, 0, $ln, $titleAlign);
                $index++;
            }
        } else {
            for ($i = 0; $i < $indent; $i++) {
                $this->Cell(2, 4, '', 0, 0, $titleAlign);
                $space += 2;
            }
            $this->Cell($this->widths[0] - $space, 4, $title, 0, 0, $titleAlign);
        }

        $this->Cell(2, 4, "", 0, 0, 'C');
        if ($b[1]) $this->SetFont("Times", "B", "10"); else $this->SetFont("Times", "", "10");
        $this->Cell($this->widths[1], 4, isset($totalCharge) ? number_format($totalCharge, 2) : '', $border, 0, 'R');
        $this->Cell(2, 4, "", 0, 0, 'C');
        if ($b[2]) $this->SetFont("Times", "B", "10"); else $this->SetFont("Times", "", "10");
        $this->Cell($this->widths[2], 4, isset($totalDiscount) ? number_format($totalDiscount, 2) : '', $border, 0, 'R');
        $this->Cell(2, 4, "", 0, 0, 'C');
        if ($b[3]) $this->SetFont("Times", "B", "10"); else $this->SetFont("Times", "", "10");
        $this->Cell($this->widths[3]/2, 4, isset($totalCoverageFirst) ? number_format($totalCoverageFirst, 2) : '', $border, 0, 'R');
        $this->Cell($this->widths[3]/2, 4, isset($totalCoverageSecond) ? number_format($totalCoverageSecond, 2) : '', $border, 0, 'R');
        if ($b[4]) $this->SetFont("Times", "B", "10"); else $this->SetFont("Times", "", "10");
        $this->Cell($this->widths[4], 4, isset($totalExcess) ? number_format($totalExcess, 2) : '', $border, 1, 'R');
    }

    function showAccommodation(){
        $this->addGroup('Accommodation');
        foreach ($this->Bill->accommodation as $key => $acc) {
            $this->addDetail($acc->type_desc);
            //updated by gelie 10-21-2015
            if ($acc->n_hrs > 0) {
                $andHrs = " & $acc->n_hrs hrs ";
            }
            else{
                $andHrs = " ";
            }
            $days_label = "day";
            if ($acc->n_days > 1) {
                $days_label .= "s";
            }
            $accom_date = "";
            $adm_date = strftime("%m/%d/%Y", strtotime($acc->admission_dtetime));
            $dis_date = strftime("%m/%d/%Y", strtotime($acc->discharge_dtetime));
            if ($adm_date != '01/01/1970' && $dis_date != '01/01/1970') {
                $accom_date = "(" . $adm_date . " to " . $dis_date . ") ";
            }
            $accommodation_details = array(
                $acc->n_days . " " . $days_label . $andHrs
                . $accom_date
                . "@ "
                . number_format($acc->room_rate, 2),
                $acc->accomodation_type,
                $acc->mandatory_excess,
                $acc->n_days
                );
            $this->addDetail($accommodation_details, $acc->n_days * $acc->room_rate);
            //end gelie
        }
        if ($this->isDetailed) {
            $this->addGroupTotal('Sub-Total(Accommodation)', $this->Bill->billInfo['total_acc_charge'],$this->GROUP_CASERATE_TOTAL_DISCOUNT,$this->GROUP_CASERATE_TOTAL_FIRST,$this->GROUP_CASERATE_TOTAL_SECOND,($this->Bill->billInfo['total_acc_charge']-($this->GROUP_CASERATE_TOTAL_FIRST+$this->GROUP_CASERATE_TOTAL_SECOND+$this->GROUP_CASERATE_TOTAL_DISCOUNT)));
        }
        $this->GROUP_CASERATE_TOTAL_DISCOUNT=0;
        $this->GROUP_CASERATE_TOTAL_FIRST=0;
        $this->GROUP_CASERATE_TOTAL_SECOND=0;
    }

    function showXlo(){
        if ($this->isDetailed) {
            $this->addGroup('X-Ray, Lab, & Others');
            if (count($this->Bill->xlo['laboratories'])) {
                $this->addDetail('Laboratories', null, true);
                foreach ($this->Bill->xlo['laboratories'] as $key => $xlo) {
                    $this->addDetail($xlo['service_desc'], null, false, true);
                    $price = number_format($xlo['serv_charge'], 2);
                    $this->addDetail($xlo['qty'] . " @ " . $price, $xlo['qty'] * $xlo['serv_charge']);
                }
            }
            if (count($this->Bill->xlo['radiologies'])) {
                $this->addDetail('Radiology', null, true);
                foreach ($this->Bill->xlo['radiologies'] as $key => $xlo) {
                    $this->addDetail($xlo['service_desc'], null, false, true);
                    $price = number_format($xlo['serv_charge'], 2);
                    $this->addDetail($xlo['qty'] . " @ " . $price, $xlo['qty'] * $xlo['serv_charge']);
                }
            }
            if (count($this->Bill->xlo['supplies'])) {
                $this->addDetail('Supplies', null, true);
                foreach ($this->Bill->xlo['supplies'] as $key => $xlo) {
                    $this->addDetail($xlo['service_desc'], null, false, true);
                    $price = number_format($xlo['serv_charge'], 2);
                    $this->addDetail($xlo['qty'] . " @ " . $price, $xlo['qty'] * $xlo['serv_charge']);
                }
            }
            if (count($this->Bill->xlo['others'])) {
                $this->addDetail('Others', null, true);
                foreach ($this->Bill->xlo['others'] as $key => $xlo) {
                    $this->addDetail($xlo['service_desc'], null, false, true);
                    $price = number_format($xlo['serv_charge'], 2);
                    $this->addDetail($xlo['qty'] . " @ " . $price, $xlo['qty'] * $xlo['serv_charge']);
                }
            }
            $this->addGroupTotal('Sub-Total(X-Ray, Lab, & Others)', $this->Bill->billInfo['total_sup_charge'] + $this->Bill->billInfo['total_srv_charge'],$this->GROUP_CASERATE_TOTAL_DISCOUNT,$this->GROUP_CASERATE_TOTAL_FIRST,$this->GROUP_CASERATE_TOTAL_SECOND,(($this->Bill->billInfo['total_sup_charge'] + $this->Bill->billInfo['total_srv_charge'])-($this->GROUP_CASERATE_TOTAL_FIRST+$this->GROUP_CASERATE_TOTAL_SECOND+$this->GROUP_CASERATE_TOTAL_DISCOUNT)));
        } else {
            $this->addGroup('X-Ray, Lab, & Others', $this->Bill->billInfo['total_sup_charge'] + $this->Bill->billInfo['total_srv_charge']);
        }
        $this->GROUP_CASERATE_TOTAL_DISCOUNT=0;
        $this->GROUP_CASERATE_TOTAL_FIRST=0;
        $this->GROUP_CASERATE_TOTAL_SECOND=0;
    }

    function showMeds(){
        if ($this->isDetailed) {
            $this->addGroup('Drugs & Medicines');
            foreach ($this->Bill->meds as $key => $med) {
                $this->addDetail($med['artikelname']);
                $this->addDetail($med['qty'] . " @ " . $med['price'], $med['qty'] * $med['price']);
            }
            $this->addGroupTotal('Drugs & Medicines', $this->Bill->billInfo['total_med_charge'],$this->GROUP_CASERATE_TOTAL_DISCOUNT,$this->GROUP_CASERATE_TOTAL_FIRST,$this->GROUP_CASERATE_TOTAL_SECOND,($this->Bill->billInfo['total_med_charge']-($this->GROUP_CASERATE_TOTAL_FIRST+$this->GROUP_CASERATE_TOTAL_SECOND+$this->GROUP_CASERATE_TOTAL_DISCOUNT)));
        } else {
            $this->addGroup('Drugs & Medicines', $this->Bill->billInfo['total_med_charge']);
        }
        $this->GROUP_CASERATE_TOTAL_DISCOUNT=0;
        $this->GROUP_CASERATE_TOTAL_FIRST=0;
        $this->GROUP_CASERATE_TOTAL_SECOND=0;
    }

    function showOps(){
        if ($this->isDetailed) {
            $this->addGroup('Operating/Delivery Room');
            foreach ($this->Bill->or as $key => $or) {
                $this->addDetail($or->op_desc, $or->op_charge);
            }
            $this->addGroupTotal('Sub-Total(Operating/Delivery Room)', $this->Bill->billInfo['total_ops_charge'],$this->GROUP_CASERATE_TOTAL_DISCOUNT,$this->GROUP_CASERATE_TOTAL_FIRST,$this->GROUP_CASERATE_TOTAL_SECOND,($this->Bill->billInfo['total_ops_charge']-($this->GROUP_CASERATE_TOTAL_FIRST+$this->GROUP_CASERATE_TOTAL_SECOND+$this->GROUP_CASERATE_TOTAL_DISCOUNT)));
        } else {
            $this->addGroup('Operating/Delivery Room', $this->Bill->billInfo['total_ops_charge']);
        }
        $this->GROUP_CASERATE_TOTAL_DISCOUNT=0;
        $this->GROUP_CASERATE_TOTAL_FIRST=0;
        $this->GROUP_CASERATE_TOTAL_SECOND=0;
    }

    function showMisc(){
        if ($this->isDetailed) {
            $this->addGroup('Miscellaneous');
            foreach ($this->Bill->misc as $key => $misc) {
                $this->addDetail($misc['name'], $misc['total_chrg']);
            }
            $this->addGroupTotal('Sub-Total(Miscellaneous)', $this->Bill->billInfo['total_msc_charge'],$this->GROUP_CASERATE_TOTAL_DISCOUNT,$this->GROUP_CASERATE_TOTAL_FIRST,$this->GROUP_CASERATE_TOTAL_SECOND,($this->Bill->billInfo['total_msc_charge']-($this->GROUP_CASERATE_TOTAL_FIRST+$this->GROUP_CASERATE_TOTAL_SECOND+$this->GROUP_CASERATE_TOTAL_DISCOUNT)));
        } else {
            $this->addGroup('Miscellaneous', $this->Bill->billInfo['total_msc_charge']);
        }
        $this->GROUP_CASERATE_TOTAL_DISCOUNT=0;
        $this->GROUP_CASERATE_TOTAL_FIRST=0;
        $this->GROUP_CASERATE_TOTAL_SECOND=0;

        

        $this->Ln(4);
        $this->addSubTotal('Sub-Total',
            $this->Bill->billInfo['totalHciCharge'],
            $this->Bill->billInfo['hospital_income_discount'],
            $this->CLAIMED_FIRST_CASE_HCI,
            $this->CLAIMED_SECOND_CASE_HCI,
            $this->Bill->billInfo['totalHciExcess']);

        $this->showDoctors();
    }

    function addDoctorRow($doctor,&$roleTotal){
        #$discount = ($doctor['accommodation_type'] == 1 && $this->Bill->billInfo['accommodation_type'] == 1 && !$this->Bill->encounterInfo['isphic'] && !$this->Bill->encounterInfo['isMedicoLegal']) ? $doctor['dr_charge'] : 0;
        $discount = 0;
        $OUTPUT_1 = 0;
        $OUTPUT_2 = 0;
        // if($this->Bill->encounterInfo['encounter_type'] != ER_PATIENT && $this->Bill->encounterInfo['encounter_type'] != OUT_PATIENT){
            if ($doctor['accommodation_type'] == 1 && $this->Bill->billInfo['accommodation_type'] == 1 && !$this->Bill->encounterInfo['isphic'] && !$this->Bill->encounterInfo['isMedicoLegal']) {
                $discount = $doctor['dr_charge'];

            }elseif(!$this->Bill->encounterInfo['isphic'] && $this->Bill->billInfo['accommodation_type'] == 1 && $this->Bill->encounterInfo['isMedicoLegal'] && $this->Bill->encounterInfo['applied_discount']){
                $discount = $doctor['dr_charge'] * $this->Bill->encounterInfo['applied_discount'];

            }elseif(!$this->Bill->encounterInfo['isphic'] && $this->Bill->billInfo['accommodation_type'] == 2 && $this->Bill->encounterInfo['applied_discount']){
                $discount = $doctor['dr_charge'] * $this->Bill->encounterInfo['applied_discount'];

            } elseif ($this->Bill->encounterInfo['isphic'] && $this->Bill->encounterInfo['applied_discount']) {
                $discount = $doctor['dr_charge'] * $this->Bill->encounterInfo['applied_discount'];
            }
        // }
        $doctor['coverage'] = ($doctor['accommodation_type'] == 1 && $this->Bill->billInfo['accommodation_type'] == 1 && !$this->Bill->encounterInfo['isphic'] && !$this->Bill->encounterInfo['isMedicoLegal']) ? 0 : ($doctor['coverage']);
        $excess = $doctor['dr_charge'] - ($discount + ($doctor['coverage']));
        $roleTotal['totalCharge'] += $doctor['dr_charge'];
        $roleTotal['totalDiscount'] += $discount;
        $roleTotal['totalCoverage'] += $doctor['coverage'];
        $roleTotal['totalExcess'] += $excess;
        // die($this->TOTAL_FIRST_CASE_PF);
        // var_dump($doctor['has_coverage']);die();
        if($doctor['has_coverage']==$this->Bill->billInfo['bill_nr']){
            $OUTPUT_1=$doctor['first_claim'];
            $this->CLAIMED_FIRST_CASE_PF+=$doctor['first_claim'];
            $OUTPUT_2=$doctor['second_claim'];
            $this->CLAIMED_SECOND_CASE_PF+=$doctor['second_claim'];
        }else{
        if($doctor['caserate']==FIRST_CASE_NUM){
            if(($this->CLAIMED_FIRST_CASE_PF+$doctor['coverage'])<=$this->TOTAL_FIRST_CASE_PF){
                $OUTPUT_1=$doctor['coverage'];
                $this->CLAIMED_FIRST_CASE_PF+=$doctor['coverage'];
            }else{
                $OUTPUT_1=$this->TOTAL_FIRST_CASE_PF-$this->CLAIMED_FIRST_CASE_PF;
                $this->CLAIMED_FIRST_CASE_PF=$this->TOTAL_FIRST_CASE_PF;
            }
            if($this->TOTAL_FIRST_CASE_PF==$this->CLAIMED_FIRST_CASE_PF){
                if(($this->CLAIMED_SECOND_CASE_PF+($doctor['coverage']-$OUTPUT_1))<=$this->TOTAL_SECOND_CASE_PF){
                    $OUTPUT_2=($doctor['coverage']-$OUTPUT_1);
                    $this->CLAIMED_SECOND_CASE_PF+=($doctor['coverage']-$OUTPUT_1);
                }else{
                    $OUTPUT_2=$this->TOTAL_SECOND_CASE_PF-$this->CLAIMED_SECOND_CASE_PF;
                    $this->CLAIMED_SECOND_CASE_PF=$this->TOTAL_SECOND_CASE_PF;
                }
            }
        }
        else if($doctor['caserate']==SECOND_CASE_NUM){
            if(($this->CLAIMED_SECOND_CASE_PF+($doctor['coverage']-$OUTPUT_1))<=$this->TOTAL_SECOND_CASE_PF){
                $OUTPUT_2=($doctor['coverage']-$OUTPUT_1);
                $this->CLAIMED_SECOND_CASE_PF+=($doctor['coverage']-$OUTPUT_1);
            }else{
                $OUTPUT_2=$this->TOTAL_SECOND_CASE_PF-$this->CLAIMED_SECOND_CASE_PF;
                $this->CLAIMED_SECOND_CASE_PF=$this->TOTAL_SECOND_CASE_PF;
            }
            if($this->TOTAL_SECOND_CASE_PF==$this->CLAIMED_SECOND_CASE_PF){
                    if(($this->CLAIMED_FIRST_CASE_PF+$doctor['coverage'])<=$this->TOTAL_FIRST_CASE_PF){
                    $OUTPUT_1=$doctor['coverage'];
                    $this->CLAIMED_FIRST_CASE_PF+=$doctor['coverage'];
                }else{
                    $OUTPUT_1=$this->TOTAL_FIRST_CASE_PF-$this->CLAIMED_FIRST_CASE_PF;
                    $this->CLAIMED_FIRST_CASE_PF=$this->TOTAL_FIRST_CASE_PF;
                }
            }
        }
        else if($doctor['caserate']==FIRST_SECOND_CASE_NUM){
            if($doctor['coverage']==$doctor['supposed_first_claim']){
                if(($this->CLAIMED_FIRST_CASE_PF+$doctor['coverage'])<=$this->TOTAL_FIRST_CASE_PF){
                    $OUTPUT_1=$doctor['coverage'];
                    $this->CLAIMED_FIRST_CASE_PF+=$doctor['coverage'];
                }else{
                    $OUTPUT_1=$this->TOTAL_FIRST_CASE_PF-$this->CLAIMED_FIRST_CASE_PF;
                    $this->CLAIMED_FIRST_CASE_PF=$this->TOTAL_FIRST_CASE_PF;
                }
                if($this->TOTAL_FIRST_CASE_PF==$this->CLAIMED_FIRST_CASE_PF){
                    if(($this->CLAIMED_SECOND_CASE_PF+($doctor['coverage']-$OUTPUT_1))<=$this->TOTAL_SECOND_CASE_PF){
                        $OUTPUT_2=($doctor['coverage']-$OUTPUT_1);
                        $this->CLAIMED_SECOND_CASE_PF+=($doctor['coverage']-$OUTPUT_1);
                    }else{
                        $OUTPUT_2=$this->TOTAL_SECOND_CASE_PF-$this->CLAIMED_SECOND_CASE_PF;
                        $this->CLAIMED_SECOND_CASE_PF=$this->TOTAL_SECOND_CASE_PF;
                    }
                }
            }else if($doctor['coverage']==$doctor['supposed_second_claim']){
                if(($this->CLAIMED_SECOND_CASE_PF+($doctor['coverage']-$OUTPUT_1))<=$this->TOTAL_SECOND_CASE_PF){
                    $OUTPUT_2=($doctor['coverage']-$OUTPUT_1);
                    $this->CLAIMED_SECOND_CASE_PF+=($doctor['coverage']-$OUTPUT_1);
                }else{
                    $OUTPUT_2=$this->TOTAL_SECOND_CASE_PF-$this->CLAIMED_SECOND_CASE_PF;
                    $this->CLAIMED_SECOND_CASE_PF=$this->TOTAL_SECOND_CASE_PF;
                }
                if($this->TOTAL_SECOND_CASE_PF==$this->CLAIMED_SECOND_CASE_PF){
                        if(($this->CLAIMED_FIRST_CASE_PF+$doctor['coverage'])<=$this->TOTAL_FIRST_CASE_PF){
                        $OUTPUT_1=$doctor['coverage'];
                        $this->CLAIMED_FIRST_CASE_PF+=$doctor['coverage'];
                    }else{
                        $OUTPUT_1=$this->TOTAL_FIRST_CASE_PF-$this->CLAIMED_FIRST_CASE_PF;
                        $this->CLAIMED_FIRST_CASE_PF=$this->TOTAL_FIRST_CASE_PF;
                    }
                }
            }
            else{
                if(($this->CLAIMED_FIRST_CASE_PF+$doctor['first_claim'])<=$this->TOTAL_FIRST_CASE_PF){
                    $OUTPUT_1=$doctor['first_claim'];
                    $this->CLAIMED_FIRST_CASE_PF+=$doctor['first_claim'];
                }else{
                    $OUTPUT_1=$this->TOTAL_FIRST_CASE_PF-$this->CLAIMED_FIRST_CASE_PF;
                    $this->CLAIMED_FIRST_CASE_PF=$this->TOTAL_FIRST_CASE_PF;
                }
                if(($this->CLAIMED_SECOND_CASE_PF+($doctor['second_claim']))<=$this->TOTAL_SECOND_CASE_PF){
                    $OUTPUT_2=($doctor['second_claim']);
                    $this->CLAIMED_SECOND_CASE_PF+=($doctor['second_claim']);
                }else{
                    $OUTPUT_2=$this->TOTAL_SECOND_CASE_PF-$this->CLAIMED_SECOND_CASE_PF;
                    $this->CLAIMED_SECOND_CASE_PF=$this->TOTAL_SECOND_CASE_PF;
                }
            }
        }
        else{
            if(($this->CLAIMED_FIRST_CASE_PF+$doctor['coverage'])<=$this->TOTAL_FIRST_CASE_PF){
                $OUTPUT_1=$doctor['coverage'];
                $this->CLAIMED_FIRST_CASE_PF+=$doctor['coverage'];
            }else{
                $OUTPUT_1=$this->TOTAL_FIRST_CASE_PF-$this->CLAIMED_FIRST_CASE_PF;
                $this->CLAIMED_FIRST_CASE_PF=$this->TOTAL_FIRST_CASE_PF;
            }
            if($this->TOTAL_FIRST_CASE_PF==$this->CLAIMED_FIRST_CASE_PF){
                if(($this->CLAIMED_SECOND_CASE_PF+($doctor['coverage']-$OUTPUT_1))<=$this->TOTAL_SECOND_CASE_PF){
                    $OUTPUT_2=($doctor['coverage']-$OUTPUT_1);
                    $this->CLAIMED_SECOND_CASE_PF+=($doctor['coverage']-$OUTPUT_1);
                }else{
                    $OUTPUT_2=$this->TOTAL_SECOND_CASE_PF-$this->CLAIMED_SECOND_CASE_PF;
                    $this->CLAIMED_SECOND_CASE_PF=$this->TOTAL_SECOND_CASE_PF;
                }
            }
        }
        }
        $OUTPUT_1 = $OUTPUT_1?$OUTPUT_1:0;
        $OUTPUT_2 = $OUTPUT_2?$OUTPUT_2:0;
        // if(($this->CLAIMED_FIRST_CASE_PF+$doctor['coverage'])<=$this->TOTAL_FIRST_CASE_PF){
        //     $OUTPUT_1=$doctor['coverage'];
        //     $this->CLAIMED_FIRST_CASE_PF+=$doctor['coverage'];
        // }else{
        //     $OUTPUT_1=$this->TOTAL_FIRST_CASE_PF-$this->CLAIMED_FIRST_CASE_PF;
        //     $this->CLAIMED_FIRST_CASE_PF=$this->TOTAL_FIRST_CASE_PF;
        // }
        // if($this->TOTAL_FIRST_CASE_PF==$this->CLAIMED_FIRST_CASE_PF){
        //     if(($this->CLAIMED_SECOND_CASE_PF+($doctor['coverage']-$OUTPUT_1))<=$this->TOTAL_SECOND_CASE_PF){
        //         $OUTPUT_2=($doctor['coverage']-$OUTPUT_1);
        //         $this->CLAIMED_SECOND_CASE_PF+=($doctor['coverage']-$OUTPUT_1);
        //     }else{
        //         $OUTPUT_2=$this->TOTAL_SECOND_CASE_PF-$this->CLAIMED_SECOND_CASE_PF;
        //         $this->CLAIMED_SECOND_CASE_PF=$this->TOTAL_SECOND_CASE_PF;
        //     }
        // }

        // if(($this->CLAIMED_FIRST_CASE_PF+$doctor['coverage'])<=$this->TOTAL_FIRST_CASE_PF){
        //     $OUTPUT_1=$doctor['coverage'];
        //     $OUTPUT_2=0;
        //     $this->CLAIMED_FIRST_CASE_PF+=$doctor['coverage'];
        // }else{
        //     if($this->CLAIMED_FIRST_CASE_PF<$this->TOTAL_FIRST_CASE_PF){
        //         $OUTPUT_1=$this->TOTAL_FIRST_CASE_PF-$this->CLAIMED_FIRST_CASE_PF;
        //         if(($doctor['coverage']-$OUTPUT_1)>$this->TOTAL_SECOND_CASE_PF){
        //             $OUTPUT_2=$this->TOTAL_SECOND_CASE_PF;
        //             $this->CLAIMED_FIRST_CASE_PF=$this->TOTAL_FIRST_CASE_PF;
        //             $this->CLAIMED_SECOND_CASE_PF=$this->TOTAL_SECOND_CASE_PF;
        //         }else{
        //             $OUTPUT_2=($this->CLAIMED_FIRST_CASE_PF+$doctor['coverage'])-$this->TOTAL_FIRST_CASE_PF;
        //             $this->CLAIMED_FIRST_CASE_PF=$this->TOTAL_FIRST_CASE_PF;
        //             $this->CLAIMED_SECOND_CASE_PF+=$OUTPUT_2;
        //         }
        //     }else{
        //         if(($this->CLAIMED_SECOND_CASE_PF+$doctor['coverage'])<=$this->TOTAL_SECOND_CASE_PF){
        //             $OUTPUT_1=0;
        //             $OUTPUT_2=$doctor['coverage'];
        //             $this->CLAIMED_SECOND_CASE_PF+=$doctor['coverage'];
        //         }else{
        //             $OUTPUT_1=0;
        //             $OUTPUT_2=0;
        //         }
        //     }
        // }
        $this->GROUP_CASERATE_TOTAL_DISCOUNT+=$discount;
        $this->GROUP_CASERATE_TOTAL_FIRST+=$OUTPUT_1;
        $this->GROUP_CASERATE_TOTAL_SECOND+=$OUTPUT_2;
        $this->addSubTotal($doctor['name'], $doctor['dr_charge'], $discount, $OUTPUT_1, $OUTPUT_2, $excess, '', 'L', 5, array(0, 0, 0, 0, 0), true);
    }

    function showDoctors(){
        $this->addGroup('ADD:');
        $this->addDetail('Professional Fees');
        if (count($this->Bill->doctors['admitting'])) {
            $roleTotal = array();
            $this->addDetail('Admitting', null, false, false, 2);
            foreach ($this->Bill->doctors['admitting'] as $key => $doctor) {
                $this->addDoctorRow($doctor, $roleTotal);
            }
            $this->Bill->billInfo['totalDoctorsCharge'] += $roleTotal['totalCharge'];
            if ($this->isDetailed)
                $this->addSubTotal('Sub Total(Admitting)', $roleTotal['totalCharge'], $this->Bill->billInfo['total_d1_discount'], $this->GROUP_CASERATE_TOTAL_FIRST, $this->GROUP_CASERATE_TOTAL_SECOND, $roleTotal['totalExcess'], 'T');
        }
        $this->GROUP_CASERATE_TOTAL_DISCOUNT=0;
        $this->GROUP_CASERATE_TOTAL_FIRST=0;
        $this->GROUP_CASERATE_TOTAL_SECOND=0;
        if (count($this->Bill->doctors['consulting'])) {
            $roleTotal = array();
            $this->addDetail('Consulting', null, false, false, 2);
            foreach ($this->Bill->doctors['consulting'] as $key => $doctor) {
                $this->addDoctorRow($doctor, $roleTotal);
            }
            $this->Bill->billInfo['totalDoctorsCharge'] += $roleTotal['totalCharge'];
            if ($this->isDetailed)
                $this->addSubTotal('Sub Total(Consulting)', $roleTotal['totalCharge'], $this->Bill->billInfo['total_d2_discount'], $this->GROUP_CASERATE_TOTAL_FIRST, $this->GROUP_CASERATE_TOTAL_SECOND, $roleTotal['totalExcess'], 'T');
        }
        $this->GROUP_CASERATE_TOTAL_DISCOUNT=0;
        $this->GROUP_CASERATE_TOTAL_FIRST=0;
        $this->GROUP_CASERATE_TOTAL_SECOND=0;
        if (count($this->Bill->doctors['surgeon'])) {
            $roleTotal = array();
            $this->addDetail('Surgeon', null, false, false, 2);
            foreach ($this->Bill->doctors['surgeon'] as $key => $doctor) {
                $this->addDoctorRow($doctor, $roleTotal);
            }
            $this->Bill->billInfo['totalDoctorsCharge'] += $roleTotal['totalCharge'];
            if ($this->isDetailed)
                $this->addSubTotal('Sub Total(Surgeon)', $roleTotal['totalCharge'], $this->Bill->billInfo['total_d3_discount'], $this->GROUP_CASERATE_TOTAL_FIRST, $this->GROUP_CASERATE_TOTAL_SECOND, $roleTotal['totalExcess'], 'T');
        }
        $this->GROUP_CASERATE_TOTAL_DISCOUNT=0;
        $this->GROUP_CASERATE_TOTAL_FIRST=0;
        $this->GROUP_CASERATE_TOTAL_SECOND=0;
        if (count($this->Bill->doctors['anesthesiologist'])) {
            $roleTotal = array();
            $this->addDetail('Anesthesiologist', null, false, false, 2);
            foreach ($this->Bill->doctors['anesthesiologist'] as $key => $doctor) {
                $this->addDoctorRow($doctor, $roleTotal);
            }
            $this->Bill->billInfo['totalDoctorsCharge'] += $roleTotal['totalCharge'];
            if ($this->isDetailed)
                $this->addSubTotal('Sub Total(Anesthesiologist)', $roleTotal['totalCharge'], $this->Bill->billInfo['total_d4_discount'], $this->GROUP_CASERATE_TOTAL_FIRST, $this->GROUP_CASERATE_TOTAL_SECOND, $roleTotal['totalExcess'], 'T');
        }
        $this->GROUP_CASERATE_TOTAL_DISCOUNT=0;
        $this->GROUP_CASERATE_TOTAL_FIRST=0;
        $this->GROUP_CASERATE_TOTAL_SECOND=0;
        $this->Bill->billInfo['totalDoctorsDiscounts'] = $this->Bill->billInfo['total_d1_discount'] + $this->Bill->billInfo['total_d2_discount'] +
            $this->Bill->billInfo['total_d3_discount'] + $this->Bill->billInfo['total_d4_discount'];

        if ($this->Bill->encounterInfo['isphic']) {
            $this->Bill->billInfo['totalDoctorsCoverage'] = $this->Bill->billInfo['total_d1_coverage'] + $this->Bill->billInfo['total_d2_coverage'] +
                $this->Bill->billInfo['total_d3_coverage'] + $this->Bill->billInfo['total_d4_coverage'];
        }

        $this->Bill->billInfo['totalDoctorsExcess'] = $this->Bill->billInfo['totalDoctorsCharge'] - ($this->Bill->billInfo['totalDoctorsDiscounts'] + $this->Bill->billInfo['totalDoctorsCoverage']);

        $this->Ln(4);
        $this->addSubTotal('Sub Total',
            $this->Bill->billInfo['totalDoctorsCharge'],
            $this->Bill->billInfo['totalDoctorsDiscounts'],
            $this->CLAIMED_FIRST_CASE_PF, $this->CLAIMED_SECOND_CASE_PF,
            $this->Bill->billInfo['totalDoctorsExcess'], 'T');

        $this->Ln(4);
        $this->SetFont("Times", "B", "10");
        $this->addSubTotal('TOTAL',
            $this->Bill->billInfo['totalHciCharge'] + $this->Bill->billInfo['totalDoctorsCharge'],
            $this->Bill->billInfo['totalHciDiscount'] + $this->Bill->billInfo['totalDoctorsDiscounts'],
            $this->CLAIMED_FIRST_CASE_HCI + $this->CLAIMED_FIRST_CASE_PF,
            $this->CLAIMED_SECOND_CASE_HCI + $this->CLAIMED_SECOND_CASE_PF,
            $this->Bill->billInfo['totalHciExcess'] + $this->Bill->billInfo['totalDoctorsExcess'], 'T', 'L', 0, array(1, 1, 1, 1, 1));

        $this->showLess();
    }//end function

    function addLessRow($title,$value){
        $this->SetFont("Times", "B", "10");
        $this->addSubTotal($title, null, null, null, null, $value, '');
        $this->SetFont("Times", "", "10");
    }

    function showLess(){
//        $creditCollectionObj = new CreditCollection();
//        $subtractor = 0;

        $this->Ln(4);
        $this->SetFont("Times", "B", "10");
        $this->addGroup('LESS:');

        $deposit = 0;

        $totalHospitalCharge = $this->Bill->billInfo['totalHciCharge'];
        $totalHospitalDiscount = $this->Bill->billInfo['totalHciDiscount'];
        $totalHospitalCoverage = $this->Bill->billInfo['totalHciCoverage'];
        $hospitalExcess = (float)str_replace(',', '', number_format($totalHospitalCharge, 2)) - (float)str_replace(',', '', number_format($totalHospitalDiscount, 2)) - (float)str_replace(',', '', number_format($totalHospitalCoverage, 2));

        $totalDoctorsCharge = $this->Bill->billInfo['totalDoctorsCharge'];
        $totalDoctorsDiscount = $this->Bill->billInfo['totalDoctorsDiscounts'];
        $totalDoctorsCoverage = $this->Bill->billInfo['totalDoctorsCoverage'];
        $doctorsExcess = (float)str_replace(',', '', number_format($totalDoctorsCharge, 2)) - (float)str_replace(',', '', number_format($totalDoctorsDiscount, 2)) - (float)str_replace(',', '', number_format($totalDoctorsCoverage, 2));

        $totalExcess = (float)$hospitalExcess + (float)$doctorsExcess;

        if (!empty($this->Bill->less['deposit'])) {
            $this->SetFont("Times", "B", "10");
            $this->addDetail('Previous Payment (PARTIAL PAYMENT)');
            $this->SetFont("Times", "", "10");
            foreach ($this->Bill->less['previousPayments'] as $key => $payment) {
                $this->addSubTotal("OR #: " . $payment->or_no, null, null, null, null, $payment->amount_paid, '', 'L', 5);
                $deposit += $payment->amount_paid;
            }
        }

        $amountDue = (float)$totalExcess;

        if ($this->Billing->isNbb()) {
            // $nbbInfo = $this->Billing->getMemberCategoryInfo();
            // $title = $nbbInfo['memcategory_desc'];

            // $amountDue -= $deposit;
            // if ($amountDue > 0) {
            //     $discount = $amountDue;
            //     $amountDue = 0;
            // } else
            //     $discount = 0;

            // $this->addSubTotal($title, null, null, null, $discount, '', 'L', 2, array(1));
        } 
        // Commented by Gervie 04/27/2016
        /*else if (mb_strtoupper($this->Bill->less['isInfirmaryOrDependent']) == 'INFIRMARY' || mb_strtoupper($this->Bill->less['isInfirmaryOrDependent']) == 'DEPENDENT') {
            switch (mb_strtoupper($this->Bill->less['isInfirmaryOrDependent'])) {
                case 'INFIRMARY':
                    $title = "INFIRMARY DISCOUNT";

                    if ($amountDue > 0) {
                        $discount = $amountDue;
                        $amountDue = -$deposit;
                    } else
                        $discount = 0;

                    $this->addSubTotal($title, null, null, null, $discount, '', 'L', 2, array(1));
                    break;
                case 'DEPENDENT':
                    $title = "INFIRMARY DISCOUNT (DEPENDENT)";

                    $amountDue = $doctorsExcess - $deposit;
                    $discount = $hospitalExcess;

                    $this->addSubTotal($title, null, null, null, $discount, '', 'L', 2, array(1));
                    break;
            }
        }*/ else {
            $amountDue -= (float)$deposit;
        }

        $this->showCreditCollectionSettlements();
        //var_dump(number_format($amountDue, 2) - number_format($this->Bill->less['totalCreditCollection'], 2)); die;

        if($this->Bill->less['totalCreditCollection']){
            $amountDue = (float)str_replace(',', '', number_format($amountDue, 2)) - (float)str_replace(',', '', number_format($this->Bill->less['totalCreditCollection'], 2)) ;
        }

        // if($this->isDialysis){
        //     $this->showTransactionPrebills();
        //     if($this->Bill->less['totalTransactionPrebills']){
        //         $amountDue = (float)str_replace(',', '', number_format($amountDue, 2)) - (float)str_replace(',', '', number_format($this->Bill->less['totalTransactionPrebills'], 2)) ;
        //     }
        // }
        

        $totalExcess = $this->Bill->billInfo['totalHciExcess'] + $this->Bill->billInfo['totalDoctorsExcess'];
        $this->Bill->billInfo['totalExcess'] = $totalExcess;
        $this->Bill->billInfo['totalNetAmount'] = $amountDue;

        $this->SetFont("Times", "B", "10");

        $this->SetFont("Times", "B", "10");
        $this->Cell(array_sum(array_slice($this->widths, 0, 4)) + 8, 4, '', 0, 0);
        $this->Cell($this->widths[4], 4, str_repeat('-', $this->widths[4] - 5), '', 1, 'R');

        $this->addSubTotal("AMOUNT DUE", null, null, null,null, $amountDue, '', 'L', 5, array(1, 0, 0, 0, 1));

        $this->Cell(array_sum(array_slice($this->widths, 0, 4)) + 8, 4, '', 0, 0);
        $this->Cell($this->widths[4], 4, str_repeat('=', ($this->widths[4] - 15)), '', 1, 'R');

        $this->showSignatories();
    }//end function


    /***************************************************/
    // added by Joy Rivera @ 05/23/2016
    // start

    function showPhicMemberInfo() {
        $this->Ln();
        $this->SetFont("Times", "B", "8");
        $this->Cell(50,4,'MEMBERSHIP CATEGORY:',0,0);
        $this->SetFont("Times", "", "8");

        $this->Cell(55,4,'',0,0);
        $this->Cell(50,4,'Prepared by:',0,1);
        $memberCategory = $this->Billing->getMemCategoryDesc();
        $clerk = $this->getBillingClerk();
        $memberName = $this->Billing->getInsuranceMemberName($this->Bill->encounterInfo['encounter_nr'],PHIC_ID);
        

        $this->Cell(50,4,$memberCategory ? $memberCategory : 'Not specified','B',0);
        $this->Cell(55,4,'',0,0);
        $this->Cell(77,4,$clerk ? mb_strtoupper($clerk) : 'NO NAME CAN BE DISPLAYED, NOT A BILLING CLERK','B',1,'C');
        $this->Cell(105,4,'',0,0);
        $this->Cell(77,4,'Billing clerk','',0,'C');
          /*added By Mark 2016-08-31*/
          $this->Ln();
            $this->SetFont("Times", "B", "8");
            $this->Cell(50,4,'MEMBER\'S NAME',0,1);
            
        if ($_GET['isPatientCopy'] =='0') {
            $this->SetFont("Times", "", "8");
            $this->Cell(50,4,$memberName ? mb_strtoupper($memberName) : 'Not specified','B',0);
            $this->Cell(55,4,'',0,0);
            $this->Cell(50,4,'Conformed by:',0,0);
        }elseif($_GET['isPatientCopy'] =='1'){
            $this->SetFont("Times", "", "8");
            $this->Cell(50,4,$memberName ? mb_strtoupper($memberName) : 'Not specified','B',0);
        }
        $this->Ln(5);
        // end
        $this->SetFont("Times", "B", "8");
        $this->Cell(50,4,'INSURANCE NO.',0,0);
        
        if ($_GET['isPatientCopy'] =='0') {
            $this->Cell(55,4,'',0,0);
            $this->Cell(77,4,'','B',1,'C');
        }else{
            $this->Ln();
        }
        $this->SetFont("Times", "", "8");
        $this->Cell(50,4,$this->Bill->encounterInfo['insurance']['insurance_nr'] ? mb_strtoupper($this->Bill->encounterInfo['insurance']['insurance_nr']) : 'Not specified','B',0);
        
        if ($_GET['isPatientCopy'] =='0') {
            $this->Cell(55,4,'',0,0);
            $this->Cell(77,4,'Signature over Printed Name/Relationship/Tel.#','',1,'C');
        }else{
            $this->Ln();
        }

        $this->Ln(1);
        $this->SetFont("Times", "B", "8");
        $this->Cell(50,4,'RELATIONSHIP',0,0);

        $this->Ln();
        $this->SetFont("Times", "", "8");
        $this->Cell(50,4,$this->Bill->encounterInfo['insurance']['relation']  ? mb_strtoupper($this->Bill->encounterInfo['insurance']['relation']) : 'Not specified','B',0);
        $this->Cell(55,4,'',0,0);

        $LastInsuranceModifier = $this->Billing->getLastInsuranceModifier($this->Bill->encounterInfo['encounter_nr']);
        
        if($LastInsuranceModifier['insurance_nr']!='TEMP'){
            $this->Ln(5);
            $this->SetFont("Times", "B", "8");
            $this->Cell(50,4,'RECEIVED BY:',0,0);

            $this->Ln();
            $this->SetFont("Times", "", "8");
            $this->Cell(50,4, $LastInsuranceModifier['name'] ? mb_strtoupper($LastInsuranceModifier['name']) : 'Not specified','B',0);
            $this->Cell(55,4,'',0,0);

            $this->Ln();
            $this->SetFont("Times", "", "8");
            $this->Cell(50,4, strftime("%m/%d/%Y", strtotime($LastInsuranceModifier['modify_date'])),0,0);
            }
    }
     // end
     /***************************************************/


    public function showCreditCollectionSettlements()
    {
        
        
        if(!empty($this->Bill->less['creditCollections'])) {
            $totalCredit = 0;
            foreach ($this->Bill->less['creditCollections'] as $less) {
                $less['alt_name'] = strtoupper($less['alt_name']);
                $less['control_nr'] = strtoupper($less['control_nr']);
                $totalCredit += $less['amount'];
                $this->addSubTotal("{$less['alt_name']} ({$less['control_nr']})", null, null, null, null, $less['amount'], '', 'L', 2, array(1));
            }
            $this->Bill->less['totalCreditCollection'] = $totalCredit;
        }
    }

    // public function showTransactionPrebills()
    // {
    //     if(!empty($this->Bill->less['transactionPrebills'])) {
    //         $totalCredit = 0;
    //         foreach ($this->Bill->less['transactionPrebills'] as $less) {
    //             $less['pay_type'] = strtoupper($less['pay_type']);
    //             $less['ref_no'] = strtoupper($less['ref_no']);
    //             $totalCredit += $less['amount'];
    //             $this->addSubTotal("{$less['pay_type']} ({$less['ref_no']})", null, null, null, null, $less['amount'], '', 'L', 2, array(1));
    //         }
    //         $this->Bill->less['totalTransactionPrebills'] = $totalCredit;
    //     }
    // }

    function showSignatories()
    {
        //commented by Joy 06-03-16
        /*$this->Ln();
        $this->SetFont("Times", "B", "10");
        $this->Cell(50, 4, 'MEMBERSHIP CATEGORY:', 0, 0);
        $this->SetFont("Times", "", "10");
        $this->Cell(55, 4, '', 0, 0);
        $this->Cell(50, 4, 'Prepared by:', 0, 1);

        $memberCategory = $this->Billing->getMemCategoryDesc();
        $clerk = $this->getBillingClerk();
        $memberName = $this->Billing->getInsuranceMemberName($this->Bill->encounterInfo['encounter_nr'],PHIC_ID);

        $this->Cell(50, 4, $memberCategory ? $memberCategory : 'Not specified', 'B', 0);
        $this->Cell(55, 4, '', 0, 0);
        $this->Cell(77, 4, $clerk ? mb_strtoupper($clerk) : 'NO NAME CAN BE DISPLAYED, NOT A BILLING CLERK', 'B', 1, 'C');
        $this->Cell(105, 4, '', 0, 0);
        $this->Cell(77, 4, 'Billing clerk', '', 0, 'C');

        $this->Ln();
        $this->Ln();
        $this->SetFont("Times", "B", "10");
        $this->Cell(50, 4, 'MEMBER\'S NAME', 0, 0);
        $this->SetFont("Times", "", "10");
        $this->Cell(55, 4, '', 0, 0);
        $this->Cell(50, 4, 'Conformed by:', 0, 1);

        $this->Cell(50, 4, $memberName ? mb_strtoupper($memberName) : 'Not specified', 'B', 0);
        $this->Cell(55, 4, '', 0, 0);
        $this->Cell(77, 4, '', 'B', 1, 'C');
        $this->Cell(105, 4, '', 0, 0);
        $this->Cell(77, 4, 'Signature over Printed Name/Relationship/Tel.#', '', 1, 'C');*/
        //end comment by Joy

        $this->addPendingBloodBankNote(); //added by arc 05/11/2016

        //Added by EJ 11/10/2014
        /*$insurance = $this->Insurance->getInsurance($this->Bill->encounterInfo['pid']);
        while($row = $insurance->FetchRow()) {
            $insuranceNo = $row['insurance_nr'];
        }

        $is_pbef = $this->Billing->checkIfPbef($insuranceNo,$this->Bill->encounterInfo['pid']); //Added by EJ 11/10/2014
        $pbefRelation = $this->Billing->getPbefRelation($insuranceNo, $this->Bill->encounterInfo['pid']); //Added by EJ 11/10/2014
        */

        //Added by EJ 11/10/2014
        /*if ($is_pbef) {
            $this->Ln();
            $this->SetFont("Times", "B", "10");
            $this->Cell(50,4,'INSURANCE NO.',0,0);
            $this->Ln();
            $this->SetFont("Times", "", "10");
            $this->Cell(50,4,$insuranceNo ? mb_strtoupper($insuranceNo) : 'Not specified','B',0);
            $this->Cell(55,4,'',0,0);

            $this->Ln(10);
            $this->SetFont("Times", "B", "10");
            $this->Cell(50,4,'PBEF-RELATION',0,0);
            $this->Ln();
            $this->SetFont("Times", "", "10");
            $this->Cell(50,4,$pbefRelation ? mb_strtoupper($pbefRelation) : 'Not specified','B',0);
            $this->Cell(55,4,'',0,0);
        }*/

        if ($this->Bill->encounterInfo['isphic']) {
            // $this->Ln();
            // $this->SetFont("Times", "B", "10");
            // $this->Cell(50,4,'INSURANCE NO.',0,0);

            // $this->Ln();
            // $this->SetFont("Times", "", "10");
            // $this->Cell(50,4,$this->Bill->encounterInfo['insurance']['insurance_nr'] ? mb_strtoupper($this->Bill->encounterInfo['insurance']['insurance_nr']) : 'Not specified','B',0);
            // $this->Cell(55,4,'',0,0);

            // $this->Ln(10);
            // $this->SetFont("Times", "B", "10");
            // $this->Cell(50,4,'RELATIONSHIP',0,0);

            // $this->Ln();
            // $this->SetFont("Times", "", "10");
            // $this->Cell(50,4,$this->Bill->encounterInfo['insurance']['relation']  ? mb_strtoupper($this->Bill->encounterInfo['insurance']['relation']) : 'Not specified','B',0);
            // $this->Cell(55,4,'',0,0);

            // Added by Joy Rivera @ 05/11/2016
            //start
            $memberID = $this->Bill->encounterInfo['insurance']['employer_no'];
            //end
            if ($memberID == "") {
                $this->showPhicMemberInfo(); // added by Joy Rivera @ 05/23/2016

            } else {
                $this->showPhicMemberInfo();

                // Added by Joy Rivera @ 05/11/2016
                // start
                $this->Ln(5);
            $this->SetFont("Times", "B", "8");
                $this->Cell(50,4,'PEN:',0,0);

            $this->Ln();
            $this->SetFont("Times", "", "8");
                $this->Cell(50,4,$this->Bill->encounterInfo['insurance']['employer_no']  ? mb_strtoupper($this->Bill->encounterInfo['insurance']['employer_no']) : 'Not specified','B',0);
            $this->Cell(55, 4, '', 0, 0);

            $this->Ln(5);
            $this->SetFont("Times", "B", "8");
                $this->Cell(50,4,'EMPLOYER\'S NAME',0,0);

            $this->Ln();
            $this->SetFont("Times", "", "8");
                $this->Cell(50,4,$this->Bill->encounterInfo['insurance']['employer_name']  ? mb_strtoupper($this->Bill->encounterInfo['insurance']['employer_name']) : 'Not specified','B',0);
            $this->Cell(55, 4, '', 0, 0);
                // end
        }

        } else {

            $this->Ln();
            $this->SetFont("Times", "", "8");

            $this->Cell(105,4,'',0,0);
            $this->Cell(50,4,'Prepared by:',0,1);

            $memberCategory = $this->Billing->getMemCategoryDesc();

            $clerk = $this->getBillingClerk();
            $memberName = $this->Billing->getInsuranceMemberName($this->Bill->encounterInfo['pid'],PHIC_ID);

            $this->Cell(105,4,'',0,0);
            $this->Cell(77,4,$clerk ? mb_strtoupper($clerk) : 'NO NAME CAN BE DISPLAYED, NOT A BILLING CLERK','B',1,'C');
            $this->Cell(105,4,'',0,0);
            $this->Cell(77,4,'Billing clerk','',0,'C');

                /*added By Mark 2016-08-31*/
                        $this->Ln();
                        $this->Ln();
                        $this->SetFont("Times", "", "8");
                        $this->Cell(105,4,'',0,0);
                        $this->Cell(50,4,' ',0,1);

        }
 

        if ($this->GetY() > 275) {
            $this->AddPage();
        }
        $this->SetY(275);

        $this->SetFont("Arial", $this->fontStyle, 8);
        $this->Ln();
        $this->Cell(0, 4, mb_strtoupper($this->Bill->billInfo['accommodationDesc']) . ' PATIENT CLEARANCE', 'T', 1, 'C');
        $this->Ln();
        $this->Cell($this->w / 2 - $this->toMillimeter(0.5), 4, 'CASE #:' . $this->encounter_nr, 0, 0, 'C');
        $this->Cell($this->w / 2 - $this->toMillimeter(0.5), 4, 'PATIENT : ' . mb_strtoupper($this->Bill->encounterInfo['patient_name']), 0, 1, 'C');

        $this->Ln(4);
        // $this->Cell(30, 4, 'LINEN: ', 0, 0, 'L');
        $this->Cell(80, 4, str_repeat('', 50), 0, 0, 'L');

        if ($this->Bill->billInfo['totalNetAmount'] > 0) {
            $this->Cell(10, 4, '', 0, 0, 'L');
            $this->Cell(10, 4, '', 0, 0, 'L');
            $this->Cell(30, 4, 'CASHIER: ', 0, 0, 'L');
            $this->Cell(60, 4, str_repeat('_', 35), 0, 1, 'L');
        } else {
            $this->Ln(4);
        }

        $this->Ln(4);
        $this->Cell(30, 4, 'NURSE ON DUTY: ', 0, 0, 'L');
        $this->Cell(60, 4, str_repeat('_', 30), 0, 0, 'L');

        if ($this->Bill->billInfo['accommodation_type'] == 2) {
            $this->Cell(9, 4, '', 0, 0, 'L');
            $this->Cell(1, 4, '', 0, 0, 'L');
            $this->Cell(30, 4, 'BILLING: ', 0, 0, 'L');
            $this->Cell(60, 4, str_repeat('_', 35), 0, 1, 'L');
        } else {
            $this->Ln(4);
        }
    }

    //added by arc 05/12/2016
    function addPendingBloodBankNote(){
        if($this->hasBloodBorrowed == 1){
            $whys=$this->GetY();
            if($whys>265){$this->Ln();$this->Ln();$this->Ln();$this->Ln();$this->Ln();$this->Ln();$this->Ln();}
            $this->Ln();
            $this->Ln();
            $this->Ln();
            $this->SetFont("Arial", "B", "18");
            $this->Cell(60,4,'',0,0); 
            $this->Ln();
            $this->Cell(60,4,'',0,0);
            $this->Cell(60,6,'NOTE:',0,1);
            $this->SetFont("Arial", "", "14");
            $this->Cell(60,4,'',0,0);
            $this->Cell(60,6,'Please settle pending',0,1);
            $this->SetFont("Arial", "", "14");
            $this->Cell(60,4,'',0,0);
            $this->Cell(60,6,'Blood Transaction at',0,1);
            $this->SetFont("Arial", "", "14");
            $this->Cell(60,4,'',0,0);
            $this->Cell(60,6,'Blood Bank.',0,1);
            $this->Cell(60,4,'',0,0);
            $why=$this->GetY();
            $this->Rect(65,$why-28, 65,30);
        } 
    }
    //end function

    /***************************************************/

    function Generate()
    {
        $this->setBillInfo();
        $this->Output();
    }

    /***************************************************/

    function getBillInfo()
    {
        $this->Bill = new Bill();
        $this->Insurance = new PersonInsurance();
        $this->Bill->billInfo = $this->getBillCharges();

        if (isset($_GET['from_dt']) && $_GET['from_dt'])
            $frm_dte = strftime("%Y-%m-%d %H:%M:%S", $_GET['from_dt']);
        else
            $frm_dte = "0000-00-00 00:00:00";

        $this->Billing = new Billing();
        $this->Billing->setBillArgs(
            $this->encounter_nr,
            $this->Bill->billInfo['bill_dte'],
            $frm_dte,
            $this->Bill->encounterInfo['deathdate'],
            $this->Bill->billInfo['bill_nr']
        );

        $this->TOTAL_FIRST_CASE_PF = $this->Billing->getFirstCaseratePF($_GET['nr']);
        $this->TOTAL_SECOND_CASE_PF = $this->Billing->getSecondCaseratePF($_GET['nr']);
        $this->TOTAL_FIRST_CASE_HCI = $this->Billing->getFirstCaserateHCI($_GET['nr']);
        $this->TOTAL_SECOND_CASE_HCI = $this->Billing->getSecondCaserateHCI($_GET['nr']);

        $this->Billing->getAccommodationType();
        $this->Billing->getMemCategoryDesc();

        $this->Bill->encounterInfo = array_merge(
            $this->getEncounterInfo(),
            $this->getCaseRates()
        );

        $this->Bill->encounterInfo['deathdate'] = $_GET['deathdate'];
        $this->Bill->encounterInfo['applied_discount'] = $this->Billing->getTotalAppliedDiscounts($this->encounter_nr);

        if ($this->Bill->encounterInfo['deathdate'] != '') {
            $this->tempbill_dte = $this->Bill->encounterInfo['deathdate'];
        } elseif (strcmp($this->Bill->billInfo['bill_dte'], "0000-00-00 00:00:00") != 0) {
            $this->tempbill_dte = $this->Bill->billInfo['bill_dte'];
        } else {
            $this->tempbill_dte = strftime("%Y-%m-%d %H:%M:%S");
        }

        $this->Bill->accommodation = $this->getAccommodation();
        $this->Bill->doctors = $this->getDoctors();
        $this->Bill->xlo = $this->getXlo();
        $this->Bill->meds = $this->getMeds();
        $this->Bill->or = $this->getOps();
        $this->Bill->misc = $this->getMisc();
        $this->Bill->less = $this->getLess();

        $totalHciCharge = $this->Bill->billInfo['total_acc_charge'] + $this->Bill->billInfo['total_med_charge'] +
            $this->Bill->billInfo['total_sup_charge'] + $this->Bill->billInfo['total_srv_charge'] +
            $this->Bill->billInfo['total_ops_charge'] + $this->Bill->billInfo['total_msc_charge'];

        $totalHciDiscount = $this->Bill->billInfo['hospital_income_discount'];
        $totalHciCoverage1 = ($this->Bill->encounterInfo['isphic']) ? $this->TOTAL_FIRST_CASE_HCI : 0;
        $totalHciCoverage2 = ($this->Bill->encounterInfo['isphic']) ? $this->TOTAL_SECOND_CASE_HCI : 0;
        $totalHciCoverage = ($this->Bill->encounterInfo['isphic']) ? $this->Bill->billInfo['total_services_coverage'] : 0;

        $this->Bill->billInfo['accommodationDesc'] = $this->Billing->getAccomodationDesc();
        $this->Bill->billInfo['totalHciCharge'] = $totalHciCharge;
        $this->Bill->billInfo['totalHciDiscount'] = $totalHciDiscount;
        $this->Bill->billInfo['totalHciCoverage1'] = $totalHciCoverage1;
        $this->Bill->billInfo['totalHciCoverage2'] = $totalHciCoverage2;
        $this->Bill->billInfo['totalHciCoverage'] = $totalHciCoverage;
        $this->Bill->billInfo['total_services_coverage1']=$this->TOTAL_FIRST_CASE_HCI;
        $this->Bill->billInfo['total_services_coverage2']=$this->TOTAL_SECOND_CASE_HCI;
        $this->TOTAL_DISCOUNT_HCI=$this->Bill->billInfo['hospital_income_discount'];
        #$this->Bill->billInfo['totalHciExcess'] = number_format($totalHciCharge,2)-(number_format($totalHciDiscount,2)+number_format($totalHciCoverage,2));#commented by art 09/19/2014
        $this->Bill->billInfo['totalHciExcess'] = (string)$totalHciCharge - ((string)$totalHciDiscount + (string)$totalHciCoverage);#added by art 09/19/2014
        //echo "<pre>".print_r($this->Bill,true)."</pre>";
    }//end function

    /***************************************************/

    function getRoom()
    {
        if ($this->Bill->encounterInfo['room_no'] == 0) {
            $accomodation = $this->Bill->accommodation[count($this->Bill->accommodation) - 1];
            if (!empty($accomodation)) {
                $room_nr = $accomodation->room_nr;
                $ward_name = $accomodation->type_desc;
            } else {
                $room_nr = 'None';
                $ward_name = "No Accommodation";
            }
            if ($this->Bill->encounterInfo['ishousecase']) {
                $ward_name = preg_replace("/pay[\s]*ward/i", "Ward", $ward_name);
            }
        } else {
            $room_nr = $this->Bill->encounterInfo['room_no'];
            $ward_name = $this->Bill->encounterInfo['ward_name'];

            if ($this->ishousecase) {
                $ward_name = preg_replace("/pay[\s]*ward/i", "Ward", $ward_name);
            }
        }
        $caseType = $this->Billing->getCaseTypeDesc($this->encounter_nr, $this->Bill->billInfo['bill_dte'], $this->Billing->prev_encounter);
        if ($caseType) {
            $ward_name .= ' - ' . $caseType;
        }
        return array('room_nr' => $room_nr, 'ward_name' => $ward_name);
    }

    function getActualAdmissionDte()
    {
        global $db;

        $admit_dte = "0000-00-00 00:00:00";
        $filter = '';

        if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";

        $sql = $db->Prepare("SELECT
                                  admission_dt
                                FROM
                                  care_encounter
                                WHERE (encounter_nr = ? $filter)
                                  AND admission_dt IS NOT NULL
                                ORDER BY encounter_date
                                LIMIT 1 ;");

        if ($result = $db->Execute($sql, $this->encounter_nr)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow())
                    $admit_dte = strftime("%Y-%m-%d %H:%M", strtotime($row['admission_dt'])) . ":00";
            }
        }

        return ($admit_dte);
    }

    function getAccommodation()
    {
        $hosp_obj = new Hospital_Admin();
        $cutoff_hrs = $hosp_obj->getCutoff_Hrs();

    if ($death_date!=''){
        $tempbill_dte = $death_date;
    }elseif(strcmp($bill_dte, "0000-00-00 00:00:00") != 0){
        $tempbill_dte = $objBill->bill_dte;
    }else{
        $tempbill_dte = strftime("%Y-%m-%d %H:%M:%S");
    }

        $accommodationList = array();
        $accommodations = $this->Billing->getAccomodationList()->GetRows();
        $getDeath = $this->getDeathDate();
        $flag = 0;
        foreach ($accommodations as $i => $accommodation) {
            $Acc = new Accommodation;
            if ($accommodation['source'] == 'AD') {
                if ($flag == 0) {
                    $tmpadmit_dte = $this->getActualAdmissionDte();
                    $tmpref_dte = $this->Bill->billInfo['bill_frmdte'];
                    if (strtotime($tmpadmit_dte) < strtotime($tmpref_dte))
                        $tmpadmit_dte = $tmpref_dte;
                    $tmpdate_from = strftime("%Y-%m-%d", strtotime($tmpadmit_dte));
                    $tmptime_from = strftime("%H:%M:%S", strtotime($tmpadmit_dte));
                    $flag++;
                } else {
                    $tmpdate_from = $accommodation['date_from'];
                    $tmptime_from = $accommodation['time_from'];
                }
                $Acc->setAdmissionDteTime($tmpdate_from, $tmptime_from);
                $tmpdate_to = $accommodation['date_to'];
                if (strcmp($tmpdate_to, "0000-00-00") == 0 ) {
                    if ($getDeath == " ") {
                        $tmpdate_to = strftime("%Y-%m-%d", strtotime($this->tempbill_dte + 1));
                        $tmptime_to = strftime("%H:%M:%S", strtotime($this->tempbill_dte));
                    }else{
                         $tmpdate_to = strftime("%Y-%m-%d", strtotime($this->tempbill_dte));
                         $tmptime_to = strftime("%H:%M:%S", strtotime($this->tempbill_dte));
                    }
                    
                } else {
                    $tmptime_to = $accommodation['time_to'];
                    $tmpref_dte = strftime("%Y-%m-%d", strtotime($tmpdate_to)) . ' ' . strftime("%H:%M:%S", strtotime($tmptime_to));

                    if (strtotime($tmpref_dte) > strtotime($this->tempbill_dte)) {
                        $tmpdate_to = strftime("%Y-%m-%d", strtotime($this->tempbill_dte));
                        $tmptime_to = strftime("%H:%M:%S", strtotime($this->tempbill_dte));
                    }
                
                }
                $Acc->setDischargeDteTime($tmpdate_to, $tmptime_to);
                #$Acc->setActualDays(round((strtotime($Acc->discharge_dtetime) - strtotime($Acc->admission_dtetime)) / 86400));
                $dis = strftime("%Y-%m-%d", strtotime($Acc->discharge_dtetime)) . '00:00:00';
                $adm = strftime("%Y-%m-%d", strtotime($Acc->admission_dtetime)) . '00:00:00';
                $Acc->setActualDays(round((strtotime($dis) - strtotime($adm)) / 86400));

                if ($Acc->n_days <= 0) {
                    $Acc->setActualDays(1);
                }
                $Acc->setExcessHrs(0);
            } else {
                if ($accommodation['hrs_stay'] > $cutoff_hrs)
                    $Acc->setActualDays($accommodation['days_stay'] + 1);
                else
                    $Acc->setActualDays($accommodation['days_stay']);
                $Acc->setExcessHrs(0);
                $Acc->setAdmissionDteTime($accommodation['date_from'], $accommodation['time_from']);    //added by gelie
                $Acc->setDischargeDteTime($accommodation['date_to'], $accommodation['time_to']);        //10-23-2015
            }

            $Acc->setRoomNr($accommodation['location_nr']);
            $Acc->setTypeNr($accommodation['type_nr']);
            $Acc->setTypeDesc($accommodation['name']);


//commented out by Nick 7-24-2015, removed these considerations
//            $confinement_type = $this->Billing->getConfinementType();
//            if ($this->Bill->billInfo['isphic']) {
//                $room_rate = $accommodation['rm_rate'];
//                $room_rate = $this->getRoomRateByCaseType($confinement_type, $accommodation['name']);
//                if ($room_rate > 0) {
//                } else {
//                    $room_rate = $accommodation['rm_rate'];
//                }
//            } else {
//                if($this->Bill->encounterInfo['deathdate']){
//                    if($this->Billing->isCharity() && !$this->Billing->isMedicoLegal()){
//                        $room_rate = $this->Billing->getdeathroomrate($accommodation['name']);
//                        if($room_rate==0){
//                            $room_rate = $accommodation['rm_rate'];
//                        }
//                    }else{
//                        $room_rate = $accommodation['rm_rate'];
//                    }
//                }else{
//                    $room_rate = $accommodation['rm_rate'];
//                }
//            }

            $room_rate = $accommodation['rm_rate'];//added by Nick 7-24-2015

            $Acc->setRoomRate($room_rate);
            $Acc->setSource($accommodation['source']);
            $Acc->setExcess($accommodation['mandatory_excess']);
            $Acc->setAccomodationType($accommodation['accomodation_type']);
            $accommodationList[] = $Acc;
        }//end for each

        return $accommodationList;
    }//end function

    function getXlo()
    {
        $xlos = $this->Billing->getXLOList()->GetRows();
        $laboratories = array();
        $radiologies = array();
        $supplies = array();
        $others = array();
        foreach ($xlos as $i => $xlo) {
            switch ($xlo['source']) {
                case 'LB':
                case 'POC':
                    $laboratories[] = $xlo;
                    break;
                case 'RD':
                    $radiologies[] = $xlo;
                    break;
                case ($xlo['source'] == 'SU' || $xlo['source'] == 'MS'):
                    $supplies[] = $xlo;
                    break;
                case 'OA':
                    $others[] = $xlo;
                    break;
            }
        }
        $result = array(
            'laboratories' => $laboratories,
            'radiologies' => $radiologies,
            'supplies' => $supplies,
            'others' => $others
        );

        return $result;
    }

    function getMeds()
    {
        $meds = $this->Billing->getMedsList()->GetRows();
        return $meds;
    }

    function getOps()
    {
        $this->Billing->getOpBenefits();
        $benefits = $this->Billing->hsp_ops_benefits;
        return $benefits;
    }

    function getMisc()
    {
        $misc = $this->Billing->getMiscList()->GetRows();
        return $misc;
    }

    function getPfCoverage($dr_nr, $bill_nr, $area)
    {
        global $db;
        $pf_limiter = Config::model()->findByPk('pf_distribution_limit');
        $pf_abovelimit = explode(",",Config::model()->findByPk('pf_distribution_abovelimit'));
        $pf_belowlimit = explode(",",Config::model()->findByPk('pf_distribution_belowlimit'));
        // var_dump($pf_limiter);
        // var_dump($pf_abovelimit[0]);//71
        // var_dump($pf_abovelimit[1]);//29
        // var_dump($pf_belowlimit[0]);//64
        // var_dump($pf_belowlimit[1]);//36
        // die;
        $sql = "SELECT 
                  sbpf.dr_claim,
                  sepdr.caserate,
                  IF(
                    sbpf.role_area = 'D3',
                    IF(
                      sepdr.caserate = '1',
                      IF(
                        sorp.rvu >= ".$pf_limiter.",
                        (scrp.pf * ".$pf_abovelimit[0]."),
                        (scrp.pf *".$pf_belowlimit[0].")
                      ),
                      IF(
                        sepdr.caserate = '2',
                        IF(
                          sorp.rvu >= ".$pf_limiter.",
                          (scrp.spf * ".$pf_abovelimit[0]."),
                          (scrp.spf *".$pf_belowlimit[0].")
                        ),
                        (SELECT 
                          SUM(
                            IF(
                              sbc2.rate_type = '1',
                              IF(
                                sorp2.rvu >= ".$pf_limiter.",
                                (scrp2.pf * ".$pf_abovelimit[0]."),
                                (scrp2.pf *".$pf_belowlimit[0].")
                              ),
                              IF(
                                sorp2.rvu >= ".$pf_limiter.",
                                (scrp2.spf * ".$pf_abovelimit[0]."),
                                (scrp2.spf *".$pf_belowlimit[0].")
                              )
                            )
                          ) AS total_claim 
                        FROM
                          seg_billing_caserate sbc2 
                          LEFT JOIN seg_case_rate_packages scrp2 
                            ON sbc2.package_id = scrp2.code 
                            AND scrp2.date_to > DATE(NOW()) 
                          LEFT JOIN seg_ops_rvu_phic sorp2 
                            ON scrp2.code = sorp2.code 
                        WHERE sbc2.bill_nr = sbpf.bill_nr)
                      )
                    ),
                    IF(
                      sepdr.caserate = '1',
                      IF(
                        sorp.rvu >= ".$pf_limiter.",
                        (scrp.pf * ".$pf_abovelimit[1]."),
                        (scrp.pf * ".$pf_belowlimit[1].")
                      ),
                      IF(
                        sepdr.caserate = '2',
                        IF(
                          sorp.rvu >= ".$pf_limiter.",
                          (scrp.spf * ".$pf_abovelimit[1]."),
                          (scrp.spf * ".$pf_belowlimit[1].")
                        ),
                        (SELECT 
                          SUM(
                            IF(
                              sbc2.rate_type = '1',
                              IF(
                                sorp2.rvu >= ".$pf_limiter.",
                                (scrp2.pf * ".$pf_abovelimit[1]."),
                                (scrp2.pf * ".$pf_belowlimit[1].")
                              ),
                              IF(
                                sorp2.rvu >= ".$pf_limiter.",
                                (scrp2.spf * ".$pf_abovelimit[1]."),
                                (scrp2.spf * ".$pf_belowlimit[1].")
                              )
                            )
                          ) AS total_claim 
                        FROM
                          seg_billing_caserate sbc2 
                          LEFT JOIN seg_case_rate_packages scrp2 
                            ON sbc2.package_id = scrp2.code
                            AND scrp2.date_to > DATE(NOW()) 
                          LEFT JOIN seg_ops_rvu_phic sorp2 
                            ON scrp2.code = sorp2.code 
                        WHERE sbc2.bill_nr = sbpf.bill_nr)
                      )
                    )
                  ) AS calculated_claim,
                IFNULL(
                    sbpfb.first_claim,
                    IF(
                    sbpf.role_area = 'D3',
                    IF(
                      sepdr.caserate = '1',
                      IF(
                        sorp.rvu >= ".$pf_limiter.",
                        (scrp.pf * ".$pf_abovelimit[0]."),
                        (scrp.pf *".$pf_belowlimit[0].")
                      ),
                      IF(
                        sepdr.caserate = '2',
                        0,
                        (SELECT 
                          SUM(
                            IF(
                              sorp2.rvu >= ".$pf_limiter.",
                              (scrp2.pf * ".$pf_abovelimit[0]."),
                              (scrp2.pf *".$pf_belowlimit[0].")
                            )
                          ) AS total_claim 
                        FROM
                          seg_billing_caserate sbc2 
                          LEFT JOIN seg_case_rate_packages scrp2 
                            ON sbc2.package_id = scrp2.code 
                            AND scrp2.date_to > DATE(NOW()) 
                          LEFT JOIN seg_ops_rvu_phic sorp2 
                            ON scrp2.code = sorp2.code 
                        WHERE sbc2.bill_nr = sbpf.bill_nr 
                          AND sbc2.rate_type = '1')
                      )
                    ),
                    IF(
                      sepdr.caserate = '1',
                      IF(
                        sorp.rvu >= ".$pf_limiter.",
                        (scrp.pf * ".$pf_abovelimit[1]."),
                        (scrp.pf * ".$pf_belowlimit[1].")
                      ),
                      IF(
                        sepdr.caserate = '2',
                        0,
                        (SELECT 
                          SUM(
                            IF(
                              sorp2.rvu >= ".$pf_limiter.",
                              (scrp2.pf * ".$pf_abovelimit[1]."),
                              (scrp2.pf * ".$pf_belowlimit[1].")
                            )
                          ) AS total_claim 
                        FROM
                          seg_billing_caserate sbc2 
                          LEFT JOIN seg_case_rate_packages scrp2 
                            ON sbc2.package_id = scrp2.code 
                            AND scrp2.date_to > DATE(NOW()) 
                          LEFT JOIN seg_ops_rvu_phic sorp2 
                            ON scrp2.code = sorp2.code 
                        WHERE sbc2.bill_nr = sbpf.bill_nr 
                          AND sbc2.rate_type = '1')
                      )
                    )
                  )) AS first_claim,
                IFNULL(
                    sbpfb.second_claim,
                    IF(
                    sbpf.role_area = 'D3',
                    IF(
                      sepdr.caserate = '1',
                      0,
                      IF(
                        sepdr.caserate = '2',
                        IF(
                          sorp.rvu >= ".$pf_limiter.",
                          (scrp.spf * ".$pf_abovelimit[0]."),
                          (scrp.spf *".$pf_belowlimit[0].")
                        ),
                        (SELECT 
                          SUM(
                            IF(
                              sorp2.rvu >= ".$pf_limiter.",
                              (scrp2.pf * ".$pf_abovelimit[0]."),
                              (scrp2.pf *".$pf_belowlimit[0].")
                            )
                          ) AS total_claim 
                        FROM
                          seg_billing_caserate sbc2 
                          LEFT JOIN seg_case_rate_packages scrp2 
                            ON sbc2.package_id = scrp2.code 
                            AND scrp2.date_to > DATE(NOW()) 
                          LEFT JOIN seg_ops_rvu_phic sorp2 
                            ON scrp2.code = sorp2.code 
                        WHERE sbc2.bill_nr = sbpf.bill_nr 
                          AND sbc2.rate_type = '2')
                      )
                    ),
                    IF(
                      sepdr.caserate = '1',
                      0,
                      IF(
                        sepdr.caserate = '2',
                        IF(
                          sorp.rvu >= ".$pf_limiter.",
                          (scrp.spf * ".$pf_abovelimit[1]."),
                          (scrp.spf * ".$pf_belowlimit[1].")
                        ),
                        (SELECT 
                          SUM(
                            IF(
                              sorp2.rvu >= ".$pf_limiter.",
                              (scrp2.spf * ".$pf_abovelimit[1]."),
                              (scrp2.spf * ".$pf_belowlimit[1].")
                            )
                          ) AS total_claim 
                        FROM
                          seg_billing_caserate sbc2 
                          LEFT JOIN seg_case_rate_packages scrp2 
                            ON sbc2.package_id = scrp2.code 
                            AND scrp2.date_to > DATE(NOW()) 
                          LEFT JOIN seg_ops_rvu_phic sorp2 
                            ON scrp2.code = sorp2.code 
                        WHERE sbc2.bill_nr = sbpf.bill_nr 
                          AND sbc2.rate_type = '2')
                      )
                    )
                  )) AS second_claim,
                  (SELECT 
                    scrp2.pf 
                  FROM
                    seg_billing_caserate sbc2 
                    LEFT JOIN seg_case_rate_packages scrp2 
                      ON sbc2.`package_id` = scrp2.`code` 
                      AND scrp2.date_to > DATE(NOW()) 
                  WHERE sbc2.`rate_type` = '2' 
                    AND sbc2.`bill_nr` = sbpf.bill_nr) AS supposed_first_claim,
                  (SELECT 
                    scrp2.pf 
                  FROM
                    seg_billing_caserate sbc2 
                    LEFT JOIN seg_case_rate_packages scrp2 
                      ON sbc2.`package_id` = scrp2.`code` 
                      AND scrp2.date_to > DATE(NOW()) 
                  WHERE sbc2.`rate_type` = '2' 
                    AND sbc2.`bill_nr` = sbpf.bill_nr) AS supposed_second_claim,
                    sbpfb.bill_nr as has_coverage
                FROM
                  seg_billing_pf sbpf 
                  LEFT JOIN seg_billing_pf_breakdown sbpfb 
                    ON sbpf.bill_nr = sbpfb.bill_nr 
                    AND sbpf.hcare_id = sbpfb.hcare_id 
                    AND sbpf.dr_nr = sbpfb.dr_nr 
                    AND sbpf.role_area = sbpfb.role_area 
                  LEFT JOIN seg_billing_encounter sbe 
                    ON sbpf.bill_nr = sbe.bill_nr 
                    AND sbe.is_deleted IS NULL 
                    AND sbe.is_final = '1' 
                  LEFT JOIN seg_encounter_privy_dr sepdr 
                    ON sbe.encounter_nr = sepdr.encounter_nr 
                    AND sbpf.dr_nr = sepdr.dr_nr 
                    AND sepdr.is_deleted = '0' 
                  LEFT JOIN seg_billing_caserate sbc 
                    ON sbc.bill_nr = sbe.bill_nr 
                    AND sepdr.caserate = sbc.rate_type
                  LEFT JOIN seg_case_rate_packages scrp 
                    ON sbc.package_id = scrp.code 
                    AND scrp.date_to > DATE(NOW()) 
                  LEFT JOIN seg_ops_rvu_phic sorp 
                    ON sbc.package_id = sorp.code 
                WHERE sbpf.dr_nr = ? AND sbpf.bill_nr = ? AND sbpf.role_area = ?";
                // die($sql);
        $rs = $db->Execute($sql, array(
            $dr_nr,
            $bill_nr,
            $area
        ));

        if ($rs) {
            if ($rs->RecordCount() > 0) {
                $row = $rs->FetchRow();
                if($row['caserate']==FIRST_CASE_NUM && $row['has_coverage']==NULL){
                    if($row['dr_claim']!=$row['first_claim']){
                        $row['first_claim']=$row['dr_claim'];
                        $row['caserate']=NULL;
                    }
                }
                elseif($row['caserate']==SECOND_CASE_NUM && $row['has_coverage']==NULL){
                    if($row['dr_claim']!=$row['second_claim']){
                        $row['second_claim']=$row['dr_claim'];
                        $row['caserate']=NULL;
                    } 
                }
                elseif($row['caserate']==FIRST_SECOND_CASE_NUM && $row['has_coverage']==NULL){
                    if($row['dr_claim']!=$row['calculated_claim']){
                        $row['calculated_claim']=$row['dr_claim'];
                        $row['caserate']=NULL;
                    }
                }
                return array($row['dr_claim'],$row['caserate'],$row['calculated_claim'],$row['first_claim'],$row['second_claim'],$row['has_coverage'],$row['supposed_first_claim'],$row['supposed_second_claim']);
            }
        }
        return 0;
    }

    function getDoctors()
    {
        $this->Billing->getProfFeesList();
        $this->Billing->getProfFeesBenefits();
        $benefits = $this->Billing->getPFBenefits();
        $doctors = $this->Billing->proffees_list;
        foreach ($benefits as $x => $benefit) {
            foreach ($doctors as $y => $doctor) {
                if ($benefit->dr_nr == $doctor->dr_nr && $benefit->role_area == $doctor->role_area) {
                    $doctorsList[] = array(
                        'dr_nr' => $doctor->dr_nr,
                        'name' => strtoupper("$doctor->dr_first $doctor->dr_mid $doctor->dr_last"),
                        'role_nr' => $doctor->role_nr,
                        'role_area' => $doctor->role_area,
                        'role_level' => $doctor->role_level,
                        'days_attend' => $doctor->days_attend,
                        'dr_dailyrate' => $doctor->dr_dailyrate,
                        'dr_charge' => $doctor->dr_charge,
                        'rvu' => $doctor->ops_rvu,
                        'multiplier' => $doctor->ops_multiplier,
                        'chrg_for_coverage' => $doctor->chrg_for_coverage,
                        'accommodation_type' => $doctor->accommodation_type
                    );
                }
            }//end for each 2
        }//end for each 1

        $admitting = array();
        $consulting = array();
        $surgeon = array();
        $anesthesiologist = array();

        foreach ($doctorsList as $x => $doctor) {
            $coverage = $this->getPfCoverage($doctor['dr_nr'], $this->Bill->billInfo['bill_nr'], $doctor['role_area']);
            switch ($doctor['role_area']) {
                case 'D1':
                    $admitting[] = array_merge(
                        $doctor,
                        array('coverage' => $coverage[0],'caserate' => $coverage[1],'calculated_claim' => $coverage[2],'first_claim' => $coverage[3],'second_claim' => $coverage[4],'has_coverage' => $coverage[5],'supposed_first_claim' => $coverage[6],'supposed_second_claim' => $coverage[7])
                    );
                    break;
                case 'D2':
                    $consulting[] = array_merge(
                        $doctor,
                        array('coverage' => $coverage[0],'caserate' => $coverage[1],'calculated_claim' => $coverage[2],'first_claim' => $coverage[3],'second_claim' => $coverage[4],'has_coverage' => $coverage[5],'supposed_first_claim' => $coverage[6],'supposed_second_claim' => $coverage[7])
                    );
                    break;
                case 'D3':
                    $surgeon[] = array_merge(
                        $doctor,
                        array('coverage' => $coverage[0],'caserate' => $coverage[1],'calculated_claim' => $coverage[2],'first_claim' => $coverage[3],'second_claim' => $coverage[4],'has_coverage' => $coverage[5],'supposed_first_claim' => $coverage[6],'supposed_second_claim' => $coverage[7])
                    );
                    break;
                case 'D4':
                    $anesthesiologist[] = array_merge(
                        $doctor,
                        array('coverage' => $coverage[0],'caserate' => $coverage[1],'calculated_claim' => $coverage[2],'first_claim' => $coverage[3],'second_claim' => $coverage[4],'has_coverage' => $coverage[5],'supposed_first_claim' => $coverage[6],'supposed_second_claim' => $coverage[7])
                    );
                    break;
            }
        }

        $result = array(
            'admitting' => $admitting,
            'consulting' => $consulting,
            'surgeon' => $surgeon,
            'anesthesiologist' => $anesthesiologist,
        );

        return $result;
    }//end function

    function getLess()
    {
        return array(
            'deposit' => $this->Billing->getPreviousPayments(),
            'previousPayments' => $this->Billing->prev_payments,
            'isInfirmaryOrDependent' => $this->Billing->isInfirmaryOrDependent($this->encounter_nr),
            'isPhs' => $this->Billing->checkIfPHS($this->encounter_nr),
            'creditCollections' => $this->Billing->getCreditCollectionSettlements()
            // ,'transactionPrebills' => $this->Billing->getTransactionPrebills($this->encounter_nr)
        );
    }

    function toMillimeter($inches)
    {
        return $inches * 25.4;
    }

    function getEncounterInfo()
    {
        global $db;
        $encounter = new Encounter();
        // Modified by Joy 06-14-16
        // added line "LEFT JOIN seg_encounter_name AS sn ON ce.encounter_nr = sn.encounter_nr"
        $sql = "SELECT
                    fn_get_person_lastname_first(ce.pid) AS patient_name, ce.encounter_type,
                    ce.encounter_nr, ce.pid, ce.encounter_date, ce.consulting_dr_nr,
                    sn.name_first, sn.name_middle, sn.name_last, cp.date_birth,
                    sb.brgy_name, cp.street_name, sm.mun_name, sm.zipcode, sp.prov_name,
                    sr.region_name, sr.region_desc, cd.id, cd.name_formal AS dept_name,
                    ce.current_room_nr AS room_no, cw.ward_id, cw.name AS ward_name
                FROM (care_encounter AS ce
                    INNER JOIN care_person AS cp ON ce.pid = cp.pid)
                    LEFT JOIN seg_encounter_name AS sn ON ce.encounter_nr = sn.encounter_nr
                    LEFT JOIN seg_barangays AS sb ON cp.brgy_nr = sb.brgy_nr
                    LEFT JOIN seg_municity AS sm ON cp.mun_nr = sm.mun_nr
                    LEFT JOIN seg_provinces AS sp ON sm.prov_nr = sp.prov_nr
                    LEFT JOIN seg_regions AS sr ON sp.region_nr = sr.region_nr
                    LEFT JOIN care_department AS cd ON cd.nr = ce.current_dept_nr
                    LEFT JOIN care_ward AS cw ON ce.current_ward_nr = cw.nr
                WHERE ce.encounter_nr =  ?";
        if ($this->personData = $db->Execute($sql, $this->encounter_nr)) {
            if ($this->personData->RecordCount()) {
                $row = $this->personData->FetchRow();
                $row['isphic'] = $this->Billing->isPHIC($this->encounter_nr);
                $row['isHouseCase'] = $encounter->isHouseCase($this->encounter_nr);
                $row['isMedicoLegal'] = $this->Billing->isMedicoLegal($this->encounter_nr);
                $row['insurance'] = $this->getInsuranceMemberInfo();
                return $row;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }// end of getPersonInfo

    function getCaseRates()
    {
        global $db;
        $sql = $db->Prepare("SELECT package_id,rate_type FROM seg_billing_caserate WHERE bill_nr = ?");
        $rs = $db->Execute($sql, $this->bill_nr);
        if ($rs) {
            if ($rs->RecordCount()) {
                $caserates = array();
                while ($row = $rs->FetchRow()) {
                    if ($row['rate_type'] == 1) {
                        $caserates['first'] = $row['package_id'];
                        if ($this->isNBS($row['package_id'])) {
                            $this->oae = true;
                        }
                    }
                    if ($row['rate_type'] == 2) {
                        $caserates['second'] = $row['package_id'];
                        if ($this->isNBS($row['package_id'])) {
                            $this->oae = true;
                        }
                    }
                }
                return $caserates;
            }
        }
        return array();
    }

    /*
    **added by art 11/22/14
    **check if package id is nbs
    ** return bool
    */
    function isNBS($id)
    {
        global $db;
        if ($id == NBS_PACKAGE_ID) {
            $rs = $db->GetOne('SELECT is_availed FROM `seg_caserate_hearing_test` a WHERE a.`encounter_nr` =' . $db->qstr($this->encounter_nr));
            if ($rs) {
                return true;
            }
        }
        return false;
    }

    #end art

    function getBillCharges()
    {
        global $db;
        $sql = $db->Prepare("SELECT
                              sbe.bill_nr, sbe.bill_dte, sbe.bill_frmdte,
                              sbe.accommodation_type, sbe.total_acc_charge,
                              sbe.total_med_charge, sbe.total_sup_charge,
                              sbe.total_srv_charge, sbe.total_ops_charge,
                              sbe.total_doc_charge, sbe.total_msc_charge,
                              sbe.total_prevpayments, sbe.is_final, 
                              sbcd.*, sbc.*, sbe.create_id, sbe.opd_type
                            FROM
                              seg_billing_encounter AS sbe
                              LEFT JOIN seg_billingcomputed_discount AS sbcd
                                ON sbe.bill_nr = sbcd.bill_nr
                              LEFT JOIN seg_billing_coverage AS sbc
                                ON sbe.bill_nr = sbc.bill_nr
                            WHERE
                              sbe.bill_nr = ?
                              AND
                              sbe.is_deleted IS NULL
                            LIMIT 1");
        $rs = $db->Execute($sql, $this->bill_nr);
        if ($rs) {
            if ($rs->RecordCount()) {
                return $rs->FetchRow();
            }
        }
        return array();
    }

    //Modified by EJ 12/07/2014
    function getBillingClerk(){
        global $db;
        $bill_nr =  $db->qstr($this->bill_nr);
        //$is_final = $db->GetOne("SELECT is_final FROM seg_billing_encounter WHERE bill_nr ='$bill_nr'");

        //if ($is_final >= 1) {
            $biller_name = $db->GetOne("SELECT fn_get_personell_name(personell_nr) AS name FROM care_users WHERE login_id = (SELECT modify_id FROM seg_billing_encounter WHERE bill_nr = $bill_nr)");
            return $biller_name;
        //}
        // else {

        //     // $sql = $db->Prepare("SELECT
        //     //                       fn_get_personell_firstname_last(cpl.nr) AS name
        //     //                     FROM
        //     //                       care_users AS cu
        //     //                       INNER JOIN care_personell AS cpl
        //     //                         ON cpl.nr = cu.personell_nr
        //     //                       INNER JOIN care_personell_assignment AS cpa
        //     //                         ON cpa.personell_nr = cpl.nr
        //     //                     WHERE cu.login_id = ? 
        //     //                     LIMIT 1");

        //     //Modified by Meg May 12, 2016
        //      $sql = $db->Prepare("SELECT
        //                           fn_get_personell_firstname_last(cpl.nr) AS name
        //                         FROM
        //                           care_users AS cu
        //                           INNER JOIN care_personell AS cpl
        //                             ON cpl.nr = cu.personell_nr
        //                           INNER JOIN care_personell_assignment AS cpa
        //                             ON cpa.personell_nr = cpl.nr
        //                         WHERE cu.login_id = (SELECT modify_id FROM seg_billing_encounter WHERE bill_nr = '$bill_nr')
        //                         LIMIT 1");


        //     $rs = $db->Execute($sql,$this->Bill->billInfo['create _id']);
        //     if($rs){
        //         $row = $rs->FetchRow();
        //         return $row['name'];
        //     }else{
        //         return null;
        //     }
        // }
    }
       private function getDeathDate()
    {
        global $db;
        $strSQL = $db->Prepare("SELECT CONCAT(p.death_date,' ',p.death_time) as deathdate 
                                FROM care_person p
                                WHERE death_encounter_nr = ".$db->qstr($this->encounter_nr)."");

        if($result=$db->Execute($strSQL)) {
             $row = $result->FetchRow();
                return $row['deathdate'];
        } else { return false; }
    }

    private function getInsuranceMemberInfo()
    {
        global $db;
        $insurance = $db->GetRow("SELECT
                                    seim.hcare_id,
                                    IF(sei.`remarks` = '1' OR sei.`remarks` IS NULL, seim.`insurance_nr`, siro.`title`) AS insurance_nr,
                                    seim.employer_no,  /* added by Joy Rivera */
                                    seim.employer_name, /* @ 05/10/2016 */
                                    IF(seim.relation='M','Member',sr.relation_desc) AS relation
                                  FROM
                                    seg_encounter_insurance_memberinfo AS seim
                                  LEFT JOIN seg_relationtomember AS sr ON seim.relation = sr.relation_code
                                  INNER JOIN seg_encounter_insurance sei
                                  ON sei.`encounter_nr` = seim.`encounter_nr`
                                  LEFT JOIN seg_insurance_remarks_options siro
                                  ON sei.`remarks` = siro.`id`
                                  WHERE
                                    seim.encounter_nr = {$db->qstr($this->encounter_nr)}
                                  AND
                                    seim.hcare_id = 18");
        return $insurance;
    }

    /**
     * Added by Gervie 03-19-2017
     * Query if dialysis encounter has used high flux machine.
     */
    private function hasHighFlux() {
        $billing = new Billing();

        return $billing->hasHighFlux($this->encounter_nr);
    }

    // private function MssApplied() {
    //      $credit_collection = new CreditCollection;
    //      return $credit_collection->IsDeleted($this->encounter_nr);
    
    // }

}//end class

class Bill
{
    var $encounterInfo,
        $billInfo,
        $accommodation,
        $meds,
        $xlo,
        $or,
        $misc,
        $doctors,
        $less;
}//end class

$soa = new SOA;
$soa->Generate();