<?php
/**
 * SegHIS - Hospital Information System
 * Enhanced by Segworks Technologies Corporation
 * Transmittal Letter
 */

error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require('./roots.php');

include_once($root_path . "include/care_api_classes/class_hospital_admin.php");
include_once($root_path . "include/care_api_classes/class_insurance.php");
include_once($root_path.'include/care_api_classes/class_encounter.php');
require($root_path . 'include/inc_environment_global.php');
require_once "{$root_path}include/care_api_classes/billing/class_billing_new.php";//added by Nick 6-9-2015
//require($root_path.'/modules/repgen/repgen.inc.php');

require($root_path . '/modules/repgen/themes/dmc/dmc2.php');

define('DEFAULT_HCAREID', 18);
define('DEFAULT_NBPKG_RATE', 1750);
define('DEFAULT_NBPKG_NAME', 'NEW BORN');//Added By Jarel 12/09/2013
define('WELLBABY', 12);
define('InsuranceCutOff', 20140829);


class RegGen_TransmittalLetter extends DMCRepGen
{
    var $is_detailed = FALSE;
    var $transmit_no;
    var $transmit_date;
    var $classification;
    var $ColumnBorders;

    var $frmdte;
    var $todte;

    var $from_date;
    var $to_date;

    var $prev_date;

    var $PgTotals;
    var $GrTotals;
    var $bEndOfReport;

    function RegGen_TransmittalLetter($insurance_name, $bdetailed = false, $bSummaryRep = false)
    {
        global $db;

        $pg_size = array('215.9', '330.2');
        $this->DMCRepGen($insurance_name, "L", $pg_size, $db, TRUE);
        $this->Caption = "TRANSMITTAL LETTER";
        $this->is_detailed = $bdetailed;
        if ($this->is_detailed) {
            $this->ColumnWidth = array(22, 40, 40, 23, 23, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15);
            $this->Columns = 16;
            $this->ColumnLabels = array(
                'Philhealth No.',
                'Name of Patient',
                'Name of Member',
                'Admitted',
                'Discharged',
                'Rm & Brd',
                'Drgs/Meds',
                'X-Ray/L/O',
                'OR Fee',
                'Total',
                'PF Visit',
                'Surgeon',
                'Anesth.',
                'Total',
                'Gr. Total',
                'Refund'
            );
            $this->ColumnBorders = array(
                'LBR',
                'LBR',
                'LBR',
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                'LBR',
                'LBR'
            );
            $this->Alignment = array('C', 'L', 'L', 'C', 'C', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R');
            $this->PgTotals = array(0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);
            $this->GrTotals = array(0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);
        } else {
            if (!$bSummaryRep) {
                $this->ColumnWidth = array(30, 50, 50, 32, 32, 30, 30, 31, 30);
                $this->Columns = 9;
                $this->ColumnLabels = array(
                    'Philhealth No.',
                    'Name of Patient',
                    'Name of Member',
                    'Admitted',
                    'Discharged',
                    'Hosp. Charges',
                    'Prof. Fee',
                    'Grand Total',
                    'Patient\'s Refund'
                );
                $this->Alignment = array('C', 'L', 'L', 'C', 'C', 'R', 'R', 'R', 'R');
            } else {
                $this->ColumnWidth = array(19, 24, 47, 47, 31, 31, 29, 29, 29, 29);
                $this->Columns = 10;
                $this->ColumnLabels = array(
                    'Date',
                    'Philhealth No.',
                    'Name of Patient',
                    'Name of Member',
                    'Admitted',
                    'Discharged',
                    'Hosp. Charges',
                    'Prof. Fee',
                    'Grand Total',
                    'Patient\'s Refund'
                );
                $this->Alignment = array('C', 'C', 'L', 'L', 'C', 'C', 'R', 'R', 'R', 'R');
            }
            $this->PgTotals = array(0.00, 0.00, 0.00, 0.00);
            $this->GrTotals = array(0.00, 0.00, 0.00, 0.00);
        }

        $this->TotalWidth = array_sum($this->ColumnWidth);

        $this->RowHeight = 6;
        $this->TextHeight = 6;

        $this->SetDrawColor(0, 0, 0);

        $this->PageOrientation = "L";

        $this->NoWrap = FALSE;
    }

    //commented by Nick, 2/24/2014
    // function Footer() {
    // 	if ($this->bEndOfReport)
    // 		$this->PrintGrandTotals();
    // 	else
    // 		$this->PrintPageTotals();
    // }
    // end nick

		function AfterData() {
        $this->PrintPageTotals();
        $this->bEndOfReport = true;
        //added by Nick, 2/24/2014
        if ($this->bEndOfReport)
            $this->PrintGrandTotals();
        else
            $this->PrintPageTotals();
        //end nick
    }

		function BeforeData() {
        $cell_height = $this->RowHeight;
        $indention = 3;

        $this->prev_date = '0000-00-00';

        $this->SetFontSize(14);
        $this->Cell(0, $cell_height, $this->title, 0, 1, 'C');

        if ($this->transmit_no != '') {
            $this->Cell(0, $cell_height, "TRANSMITTAL LETTER", 0, 1, 'C');
            $this->Ln(2);
            $this->SetFontSize($this->DEFAULT_FONTSIZE);

            $this->SetFont($this->DEFAULT_FONTFAMILY, $this->DEFAULT_FONTSTYLE, 11);

            $this->SetFontSize(12);
            $sTmp = "Transmittal No: " . $this->transmit_no;
            $cwidth = ($this->rMargin - $this->lMargin) / 2;
            $this->Cell($cwidth, $cell_height, $sTmp, 0, 1, "L");

            $sTmp = "Classification: " . $this->getClassificationDesc();
            $cwidth = ($this->rMargin - $this->lMargin) / 2;
            $this->Cell($cwidth, $cell_height, $sTmp, 0, 0, "L");

            $sTmp = "Transmittal Date: " . $this->transmit_date;
            $this->Cell($cwidth, $cell_height, $sTmp, 0, 1, "R");


            if ($this->caseType) {
                $sTmp = "Case Type: " . $this->caseType . " Cases";
                $cwidth = ($this->rMargin - $this->lMargin) / 2;
                $this->Cell($cwidth, $cell_height, $sTmp, 0, 1, "L");
            }

            $this->SetFont($this->DEFAULT_FONTFAMILY, $this->DEFAULT_FONTSTYLE, $this->DEFAULT_FONTSIZE);

            if ($this->is_detailed)
                $colwidths = array($this->ColumnWidth[0] + $this->ColumnWidth[1], $this->ColumnWidth[2] + $this->ColumnWidth[3] + $this->ColumnWidth[4] + $this->ColumnWidth[5] + $this->ColumnWidth[6],
                    $this->ColumnWidth[7] + $this->ColumnWidth[8] + $this->ColumnWidth[9] + $this->ColumnWidth[10], $this->ColumnWidth[11] + $this->ColumnWidth[12] + $this->ColumnWidth[13] + $this->ColumnWidth[14] + $this->ColumnWidth[15]);
            else
                $colwidths = array($this->ColumnWidth[0] + $this->ColumnWidth[1], $this->ColumnWidth[2] + $this->ColumnWidth[3], $this->ColumnWidth[4] + $this->ColumnWidth[5], $this->ColumnWidth[6] + $this->ColumnWidth[7] + $this->ColumnWidth[8]);

            $x = $this->GetX();
            $y = $this->GetY();

            $this->SetX($x + $indention);
            $this->Cell($colwidths[0], $cell_height, "Hospital Name", 0, 0, "L");
            $this->Rect($x, $y, $colwidths[0], $cell_height + ($cell_height + 8));

            $x = $x + $colwidths[0];
            $this->SetX($x + $indention);
            $this->Cell($colwidths[1], $cell_height, "Address", 0, 0, "L");
            $this->Rect($x, $y, $colwidths[1], $cell_height + ($cell_height + 8));

            $x = $x + $colwidths[1];
            $this->SetX($x + $indention);
            $this->Cell($colwidths[2], $cell_height, "Name & Signature of Hospital Representative", 0, 1, "L");
            $this->Rect($x, $y, $colwidths[2] + $colwidths[3], $cell_height + ($cell_height + 8));

            $objhosp = new Hospital_Admin();
            $hosp = $objhosp->getHospitalInfo();

            $this->Ln();

            //				$this->SetFontSize(14);
            $this->SetFont("", "B", 12);        // per request ... font size is changed from 11 to 14 --- by LST -- 06.30.2010

            $this->SetX($this->lMargin);

            $length = $this->GetStringWidth(strtoupper($hosp["hosp_name"]));
            if ($length > $colwidths[0]) {
                $xpos = $this->GetX();
                $ypos = $this->GetY();
                $this->SetXY($xpos, $ypos - 4);
                $this->MultiCell($colwidths[0], $cell_height, strtoupper($hosp["hosp_name"]), 0, "L");
                $this->SetXY($xpos, $ypos);
                $this->Cell($colwidths[0], $cell_height + 2, "", 0, 0, "C");
            } else
                $this->Cell($colwidths[0], $cell_height + 2, strtoupper($hosp["hosp_name"]), 0, 0, "C");

            $length = $this->GetStringWidth(strtoupper(trim($hosp["addr_no_street"]) . ", " . trim($hosp["brgy_name"]) . ", " . trim($hosp["mun_name"])));
            if ($length > $colwidths[1]) {
                $xpos = $this->GetX();
                $ypos = $this->GetY();
                $this->SetXY($xpos, $ypos - 4);
                $this->MultiCell($colwidths[1], $cell_height, strtoupper(trim($hosp["addr_no_street"]) . ", " . trim($hosp["brgy_name"]) . ", " . trim($hosp["mun_name"])), 0, "L");
                $this->SetXY($xpos, $ypos);
                $this->Cell($colwidths[1], $cell_height + 2, "", 0, 0, "C");
            } else {
                $this->Cell($colwidths[1], $cell_height + 2, strtoupper(trim($hosp["addr_no_street"]) . ", " . trim($hosp["brgy_name"]) . ", " . trim($hosp["mun_name"])), 0, 0, "C");
            }

            $this->Cell($colwidths[2], $cell_height + 2, strtoupper($hosp["authrep"]), 0, 0, "C");
            $this->Cell($colwidths[3], $cell_height + 2, $hosp["designation"], 0, 1, "C");
            //				$this->SetFontSize($this->DEFAULT_FONTSIZE);

            $this->SetFont($this->DEFAULT_FONTFAMILY, $this->DEFAULT_FONTSTYLE, $this->DEFAULT_FONTSIZE);

            // Second line of header ...
            if ($this->is_detailed)
                $colwidths = array($this->ColumnWidth[0] + $this->ColumnWidth[1], $this->ColumnWidth[2] + $this->ColumnWidth[3], $this->ColumnWidth[4] + $this->ColumnWidth[5] + $this->ColumnWidth[6],
                    $this->ColumnWidth[7] + $this->ColumnWidth[8] + $this->ColumnWidth[9] + $this->ColumnWidth[10] + $this->ColumnWidth[11], $this->ColumnWidth[12] + $this->ColumnWidth[13] + $this->ColumnWidth[14] + $this->ColumnWidth[15]);
            else
                $colwidths = array($this->ColumnWidth[0] + ($this->ColumnWidth[1] / 2), ($this->ColumnWidth[1] / 2) + ($this->ColumnWidth[2] / 2), ($this->ColumnWidth[2] / 2) + $this->ColumnWidth[3], $this->ColumnWidth[4] + $this->ColumnWidth[5], $this->ColumnWidth[6] + $this->ColumnWidth[7] + $this->ColumnWidth[8]);

            $this->Ln(1);

            $x = $this->GetX();
            $y = $this->GetY() - 1;

            $this->SetY($this->GetY());
            $this->Cell($colwidths[0], $cell_height, "PHIC Accreditation No.", 0, 0, "C");
            $this->Rect($x, $y, $colwidths[0], $cell_height + ($cell_height + 4));

            $x = $x + $colwidths[0];
            $this->Cell($colwidths[1], $cell_height, "Hospital Category", 0, 0, "C");
            $this->Rect($x, $y, $colwidths[1], $cell_height + ($cell_height + 4));

            $x = $x + $colwidths[1];
            $this->Cell($colwidths[2], $cell_height, "Authorized Bed Capacity", 0, 0, "C");
            $this->Rect($x, $y, $colwidths[2], $cell_height + ($cell_height + 4));

            $x = $x + $colwidths[2];
            $this->Cell($colwidths[3], $cell_height, "PHIC Employer's Number", 0, 0, "C");
            $this->Rect($x, $y, $colwidths[3], $cell_height + ($cell_height + 4));

            $x = $x + $colwidths[3];
            $this->Cell($colwidths[4], $cell_height, "Tax Account No.", 0, 0, "C");
            $this->Rect($x, $y, $colwidths[4], $cell_height + ($cell_height + 4));

            $this->Ln();

            //				$this->SetFontSize(10);
            $this->SetFont("", "B", 12);

            switch ($hosp["hosp_type"]) {
                case "TH":
                    $hosptype = "Tertiary";
                    break;

                case "SH":
                    $hosptype = "Secondary";
                    break;

                default:
                    $hosptype = "Primary";
                    break;
            }

            $insurance = new Insurance();
            $this->Cell($colwidths[0], $cell_height + 2, $insurance->getAccreditationNo(DEFAULT_HCAREID), 0, 0, "C");   // PHIC Accreditation No.
            $this->Cell($colwidths[1], $cell_height + 2, $hosptype, 0, 0, "C");                                                                    // Hospital Category
            $this->Cell($colwidths[2], $cell_height + 2, ($hosp["bed_capacity"] == 0) ? " " : $hosp["bed_capacity"], 0, 0, "C");                                                    // Authorized Bed Capacity
            $this->Cell($colwidths[3], $cell_height + 2, $insurance->getHospitalEmployerNo(DEFAULT_HCAREID), 0, 0, "C");// PHIC Employer's No.
            $this->Cell($colwidths[4], $cell_height + 2, $hosp["tax_acctno"], 0, 1, "C");                                                        // Tax Account No.
            //				$this->SetFontSize($this->DEFAULT_FONTSIZE);
        } else {
            // ... if summary report of transmittals ...
            $this->Cell(0, $cell_height, "SUMMARY OF TRANSMITTALS", 0, 1, 'C');
            $this->Ln(2);
            $this->SetFontSize($this->DEFAULT_FONTSIZE);

            $this->SetFont($this->DEFAULT_FONTFAMILY, $this->DEFAULT_FONTSTYLE, 11);

            $this->SetFontSize(12);
            $sTmp = "Covered Period: " . $this->from_date . " to " . $this->to_date;
            $cwidth = ($this->rMargin - $this->lMargin) / 2;
            $this->Cell($cwidth, $cell_height, $sTmp, 0, 1, "C");
            $this->Ln(1);
        }

        $this->SetFont($this->DEFAULT_FONTFAMILY, $this->DEFAULT_FONTSTYLE, $this->DEFAULT_FONTSIZE);

        if ($this->is_detailed) {
            $this->Ln(1.2);
            $this->SetX($this->GetX());
            $this->Cell($this->ColumnWidth[0], $this->RowHeight + 1, " ", "TLR", 0, "C");
            $this->Cell($this->ColumnWidth[1], $this->RowHeight + 1, " ", "TLR", 0, "C");
            $this->Cell($this->ColumnWidth[2], $this->RowHeight + 1, " ", "TLR", 0, "C");
            $this->Cell($this->ColumnWidth[3] + $this->ColumnWidth[4], $this->RowHeight + 1, "Confinement Period", 1, 0, "C");                                                                             // Confinement Period
            $this->Cell($this->ColumnWidth[5] + $this->ColumnWidth[6] + $this->ColumnWidth[7] + $this->ColumnWidth[8] + $this->ColumnWidth[9], $this->RowHeight + 1, "Hospital Charges", 1, 0, "C");     // Hospital Charges
            $this->Cell($this->ColumnWidth[10] + $this->ColumnWidth[11] + $this->ColumnWidth[12] + $this->ColumnWidth[13], $this->RowHeight + 1, "Professional Fee", 1, 0, "C");                           // Professional Fee
            $this->Cell($this->ColumnWidth[14], $this->RowHeight + 1, " ", "TLR", 0, "C");
            $this->Cell($this->ColumnWidth[15], $this->RowHeight + 1, "Patient's", "TLR", 1, "C");
        }

        if ($this->is_detailed)
            $this->SetFontSize($this->DEFAULT_FONTSIZE);
        else {
            $this->SetFontSize(10);
            if ($this->transmit_no == '') $this->SetFont($this->DEFAULT_FONTFAMILY, "B", 10);
        }

        if ($this->is_detailed) {
            if ($this->colored) $this->SetFillColor(255);
            $this->SetTextColor(0);
            for ($i = 0; $i < $this->Columns; $i++) {
                $this->Cell($this->ColumnWidth[$i], $this->RowHeight, $this->ColumnLabels[$i], $this->ColumnBorders[$i], 0, 'C', 1);
            }
            $this->Ln();
        } else
            parent::Header();

        $this->bEndOfReport = false;
    }

    function Header()
    {
        if ($this->PageNo() > 1) {
            if ($this->is_detailed) {
                $this->SetX($this->GetX());
                $this->Cell($this->ColumnWidth[0], $this->RowHeight, " ", "TLR", 0, "C");
                $this->Cell($this->ColumnWidth[1], $this->RowHeight, " ", "TLR", 0, "C");
                $this->Cell($this->ColumnWidth[2], $this->RowHeight, " ", "TLR", 0, "C");
                $this->Cell($this->ColumnWidth[3] + $this->ColumnWidth[4], $this->RowHeight, "Confinement Period", 1, 0, "C");                                                                             // Confinement Period
                $this->Cell($this->ColumnWidth[5] + $this->ColumnWidth[6] + $this->ColumnWidth[7] + $this->ColumnWidth[8] + $this->ColumnWidth[9], $this->RowHeight, "Hospital Charges", 1, 0, "C");     // Hospital Charges
                $this->Cell($this->ColumnWidth[10] + $this->ColumnWidth[11] + $this->ColumnWidth[12] + $this->ColumnWidth[13], $this->RowHeight, "Professional Fee", 1, 0, "C");                           // Professional Fee
                $this->Cell($this->ColumnWidth[14], $this->RowHeight, " ", "TLR", 0, "C");
                $this->Cell($this->ColumnWidth[15], $this->RowHeight, "Patient's", "TLR", 1, "C");

                $this->SetFontSize($this->DEFAULT_FONTSIZE);
            } else {
                $this->SetFontSize(10);
                if ($this->transmit_no == '') $this->SetFont($this->DEFAULT_FONTFAMILY, "B", 10);
            }

            if ($this->is_detailed) {
                if ($this->colored) $this->SetFillColor(255);
                $this->SetTextColor(0);
                for ($i = 0; $i < $this->Columns; $i++) {
                    $this->Cell($this->ColumnWidth[$i], $this->RowHeight, $this->ColumnLabels[$i], $this->ColumnBorders[$i], 0, 'C', 1);
                }
                $this->Ln();
            } else
                parent::Header();
        }
    }

		function getClassificationDesc() {
        if (($this->classification != '') && ($this->classification != '0')) {
            $strSQL = "select memcategory_desc from seg_memcategory
												where memcategory_id = $this->classification";
            $sDesc = '';
            if ($result = $this->Conn->Execute($strSQL)) {
                $this->Conn->SetFetchMode(ADODB_FETCH_ASSOC);
                if ($row = $result->FetchRow()) {
                    $sDesc = (is_null($row["memcategory_desc"])) ? "" : $row["memcategory_desc"];
                }
            }
        } else
            $sDesc = "ALL MEMBER CLASSIFICATIONS";
        return $sDesc;
    }

        // updated by carriane 08/30/18; added suffix 
		function concatname($slast, $sfirst, $smid, $ssuffix='') {
        $stmp = "";

        if (!empty($slast)) $stmp .= $slast;
        if (!empty($sfirst)) {
            if (!empty($stmp)) $stmp .= ", ";
            $stmp .= $sfirst.' '.$ssuffix;
        }
        if (!empty($smid)) {
            if (!empty($stmp)) $stmp .= " ";
            $stmp .= $smid;
        }
        return ($stmp);
    }

		function getPrincipalHolder($s_pid, $nhcareid, $ins_nr) {
        global $db;

        $sprincipal = "";
        $pinsure_obj = new PersonInsurance($s_pid);
				$row = $pinsure_obj->is_member_info_editableENC($s_pid, $nhcareid, $ins_nr);
        if (!$row) {
            $strSQL = "select cp.pid, cp.name_last, cp.name_first, cp.name_middle \n
													from care_person_insurance as cpi0 inner join care_person as cp on cpi0.pid = cp.pid \n
													where exists (select * from care_person_insurance as cpi1 \n
																					 where cpi1.pid = '$s_pid' and cpi1.hcare_id = $nhcareid \n
																							and cpi1.pid <> cpi0.pid and cpi1.hcare_id = cpi0.hcare_id \n
														 and cpi1.insurance_nr = cpi0.insurance_nr) \n
														 and cpi0.is_principal <> 0";

            if ($result = $db->Execute($strSQL)) {
                if ($result->RecordCount()) {
                    while ($row = $result->FetchRow())
                        $sprincipal = $this->concatname((is_null($row['name_last']) ? '' : $row['name_last']),
                            (is_null($row['name_first']) ? '' : $row['name_first']),
                            (is_null($row['name_middle']) ? '' : $row['name_middle']));
                }
            }
        } else {
            $sprincipal = $this->concatname((is_null($row['last_name']) ? '' : $row['last_name']),
                (is_null($row['first_name']) ? '' : $row['first_name']),
                (is_null($row['middle_name']) ? '' : $row['middle_name']));
        }

        return ($sprincipal);
    }

		function GetNewPrincipalHolder($pid, $encounter_nr){
				global $db;
				$principalname = "";
				$pid = $db->qstr($pid);
				$encounter_nr = $db->qstr($encounter_nr);
				$sql = "SELECT member_lname,
								member_fname,
								member_mname,
								suffix
						FROM seg_encounter_insurance_memberinfo 
						WHERE pid = $pid
						AND encounter_nr = $encounter_nr";

				if($result = $db->GetRow($sql)){
					 $principalname = $this->concatname($result['member_lname'], $result['member_fname'], $result['member_mname'],$result['suffix']);

                     // commented by carriane 08/30/18
					 // if($result['suffix']){
					 // 	return $principalname." ".$result['suffix'].".";
					 // }else{
					 	return $principalname;
					 //}
				}else{	
					return false;
				}
		}

		function AfterRowRender() {
//			if ($this->RENDERCOLNUM == ($this->MAXCOLS-1)) {
        $nlimit = ($this->is_detailed) ? 11 : 4;

        if ($this->transmit_no != '')
            $indx = 5;
        else
            $indx = 6;

        for ($i = 0; $i < $nlimit; $i++) {
            $this->PgTotals[$i] += str_replace(',', '', $this->Data[$this->RENDERROWNUM - 1][$i + $indx]);
            $this->GrTotals[$i] += str_replace(',', '', $this->Data[$this->RENDERROWNUM - 1][$i + $indx]);
        }
//			}
    }

		function BeforeCell() {
        if ($this->COLNUM == 0) {
            if ($this->transmit_no == '') {
                if (strcmp($this->prev_date, $this->DATA) != 0) {
                    $this->prev_date = $this->DATA;
                    $this->DATA = strftime("%m-%d-%Y", strtotime($this->DATA));
                } else
                    $this->DATA = " ";
            }
        }
    }

		function BeforeCellRender() {
        if ($this->RENDERCOLNUM == 0) {
            $this->SetXY($this->RENDERROWX - 5, $this->RENDERROWY);
            $this->Cell(4, $this->RowHeight, $this->RENDERROWNUM + 1, 0, 0, "R");
            $this->SetXY($this->RENDERROWX, $this->RENDERROWY);
        }
    }

		function PrintPageTotals() {
        $nlimit = ($this->is_detailed) ? 11 : 4;

        if ($this->transmit_no != '') {
            $this->SetX($this->GetX() + $this->ColumnWidth[0] + $this->ColumnWidth[1] + $this->ColumnWidth[2]);
            $this->Cell($this->ColumnWidth[3] + $this->ColumnWidth[4], $this->RowHeight, "PAGE TOTAL", 1, 0, "C");
            $indx = 5;
        } else {
            $this->SetX($this->GetX() + $this->ColumnWidth[0] + $this->ColumnWidth[1] + $this->ColumnWidth[2] + $this->ColumnWidth[3]);
            $this->Cell($this->ColumnWidth[4] + $this->ColumnWidth[5], $this->RowHeight, "PAGE TOTAL", 1, 0, "C");
            $indx = 6;
        }

        for ($i = 0; $i < $nlimit; $i++) {
            $this->Cell($this->ColumnWidth[$i + $indx], $this->RowHeight, number_format($this->PgTotals[$i], 2, '.', ','), 1, 0, "R");
            $this->PgTotals[$i] = 0.00;
        }
        $this->Ln();
    }

		function PrintGrandTotals() {
        $nlimit = ($this->is_detailed) ? 11 : 4;

        if ($this->transmit_no != '') {
            $this->SetX($this->GetX() + $this->ColumnWidth[0] + $this->ColumnWidth[1] + $this->ColumnWidth[2]);
            $this->Cell($this->ColumnWidth[3] + $this->ColumnWidth[4], $this->RowHeight, "GRAND TOTAL", 1, 0, "C");
            $indx = 5;
        } else {
            $this->SetX($this->GetX() + $this->ColumnWidth[0] + $this->ColumnWidth[1] + $this->ColumnWidth[2] + $this->ColumnWidth[3]);
            $this->Cell($this->ColumnWidth[4] + $this->ColumnWidth[5], $this->RowHeight, "GRAND TOTAL", 1, 0, "C");
            $indx = 6;
        }
        for ($i = 0; $i < $nlimit; $i++) {
            $this->Cell($this->ColumnWidth[$i + $indx], $this->RowHeight, number_format($this->GrTotals[$i], 2, '.', ','), 1, 0, "R");
        }
        $this->Ln();
    }

		function isWellBaby($enc) {
        global $db;

        $enc_type = 0;
        $strSQL = "select encounter_type " .
            "   from care_encounter " .
            "   where encounter_nr = '" . $enc . "'";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $enc_type = $row['encounter_type'];
            }
        }

        return ($enc_type == WELLBABY);
    }

        //added by Nick 08-04-2014
        function getDischargeName($encounter_nr){
            global $db;
            $this->sql = "SELECT * FROM seg_encounter_name WHERE encounter_nr = ? LIMIT 1";
            $rs = $db->Execute($this->sql,$encounter_nr);
            if($rs){
                if($rs->RecordCount()){
                    return $rs->FetchRow();
                }
            }
            return false;
        }

        //added by Nick 08-04-2014
        function setPatientName(&$row){
            $dischargeInfo = $this->getDischargeName($row['encounter_nr']);
            if(!empty($dischargeInfo)){
                $row['name_first'] = $dischargeInfo['name_first'];
                $row['name_last'] = $dischargeInfo['name_last'];
                $row['name_middle'] = $dischargeInfo['name_middle'];
            }
        }

    /**
     * Updated By Jarel
     * Updated On 09/25/2014
     * Combined Old Circular And New Circular as Requested by Billing
     * NEEDS TO REFACTOR
     **/
		function FetchData() {
        global $db;
//        $strSQL = "select cpi.insurance_nr, is_principal, cp.name_last, cp.name_first, cp.name_middle, date_format((case when admission_dt is null or admission_dt = '' then encounter_date else admission_dt end), '%b %e, %Y %l:%i%p') as date_admission, \n
//                         date_format(str_to_date(ce.modify_time, '%Y-%m-%d %H:%i:%s'), '%b %e, %Y %l:%i%p') as date_discharge, acc_coverage, \n
//                         med_coverage, xlo_coverage, or_fee, pf_visit, surgeon_coverage, anesth_coverage \n
//                      from (((((seg_transmittal as h inner join seg_transmittal_details as d on h.transmit_no = d.transmit_no) \n
//                         inner join care_encounter as ce on d.encounter_nr = ce.encounter_nr) inner join care_person as cp on ce.pid = cp.pid) \n
//                         inner join care_person_insurance as cpi on cpi.pid = ce.pid and cpi.hcare_id = h.hcare_id) \n
//                         inner join (select encounter_nr, hcare_id, sum(total_acc_coverage) as acc_coverage,sum(total_med_coverage) as med_coverage, sum(total_srv_coverage + total_msc_coverage) as xlo_coverage, \n
//                                        sum(total_ops_coverage) as or_fee, sum(total_d1_coverage + total_d2_coverage) as pf_visit, sum(total_d3_coverage) as surgeon_coverage, sum(total_d4_coverage) as anesth_coverage \n
//                                        from seg_billing_coverage as sbc inner join seg_billing_encounter as sbe on sbc.bill_nr = sbe.bill_nr \n
//                                        group by encounter_nr, hcare_id) as t on \n
//                            t.encounter_nr = d.encounter_nr and t.hcare_id = h.hcare_id) \n
//                         inner join seg_encounter_memcategory as sem on sem.encounter_nr = d.encounter_nr \n
//                      where h.transmit_no = '$this->transmit_no' and memcategory_id = $this->classification \n
//                      order by cp.name_last, cp.name_first, cp.name_middle";

				if ($this->transmit_no != '') {								# remove cpi.insurance_nr couz per encounter ang insurance
					$strSQL = "select cp.pid, h.hcare_id, t.encounter_nr, cpi.insurance_nr as oldInsurance, IF(sei.`remarks` = '1' OR sei.`remarks` IS NULL, simi.`insurance_nr`, siro.`title`) AS  newInsurance, is_principal, memcategory_desc, cp.name_last, cp.name_first, cp.name_middle, date_format((case when admission_dt is null or admission_dt = '' then encounter_date else admission_dt end), '%b %e, %Y %l:%i%p') as date_admission, \n
													 IFNULL(DATE_FORMAT(STR_TO_DATE(ce.mgh_setdte,'%Y-%m-%d %H:%i:%s'),'%b %e, %Y %l:%i%p'),DATE_FORMAT(STR_TO_DATE( bill_dte,'%Y-%m-%d %H:%i:%s'),'%b %e, %Y %l:%i%p')) AS date_discharge, acc_coverage, \n
													 med_coverage, xlo_coverage, hci_coverage, or_fee, pf_visit, surgeon_coverage, anesth_coverage, patient_claim \n
												from ((((((seg_transmittal as h inner join seg_transmittal_details as d on h.transmit_no = d.transmit_no) \n
													 inner join care_encounter as ce on d.encounter_nr = ce.encounter_nr) inner join care_person as cp on ce.pid = cp.pid) \n
													 inner join care_person_insurance as cpi on cpi.pid = ce.pid and cpi.hcare_id = h.hcare_id) \n
													 left join seg_encounter_insurance_memberinfo AS simi ON simi.`encounter_nr` = ce.encounter_nr AND simi.hcare_id= h.hcare_id) \n
													 inner join (select bill_dte, encounter_nr, hcare_id, sum(total_acc_coverage) as acc_coverage,sum(total_med_coverage) as med_coverage, sum(total_srv_coverage + total_msc_coverage) as xlo_coverage, sum(total_services_coverage) as hci_coverage, \n
																					sum(total_ops_coverage) as or_fee, sum(total_d1_coverage + total_d2_coverage) as pf_visit, sum(total_d3_coverage) as surgeon_coverage, sum(total_d4_coverage) as anesth_coverage \n
																					from seg_billing_coverage as sbc inner join seg_billing_encounter as sbe on (sbc.bill_nr = sbe.bill_nr AND sbe.is_deleted IS NULL) \n
																					group by encounter_nr, hcare_id) as t on \n
															t.encounter_nr = d.encounter_nr and t.hcare_id = h.hcare_id) \n
													 left join (seg_encounter_memcategory as sem inner join seg_memcategory as sm on sem.memcategory_id = sm.memcategory_id) \n
															on sem.encounter_nr = d.encounter_nr \n
                                                            INNER JOIN seg_encounter_insurance sei
                                                              ON sei.`encounter_nr` = simi.`encounter_nr`
                                                              LEFT JOIN seg_insurance_remarks_options siro
                                                              ON sei.`remarks` = siro.`id`

												where h.transmit_no = '$this->transmit_no' " . (($this->classification == '') || ($this->classification == '0') ? '' : "and sem.memcategory_id = $this->classification") . " \n
												order by cp.name_last, cp.name_first, cp.name_middle";

        } else {
            $strSQL = "SELECT DATE(h.transmit_dte) AS transmitdate, cp.pid, h.hcare_id, t.encounter_nr, cpi.insurance_nr, is_principal, memcategory_desc, cp.name_last, cp.name_first, cp.name_middle, date_format((CASE WHEN admission_dt IS NULL OR admission_dt = '' THEN encounter_date ELSE admission_dt END), '%b %e, %Y %l:%i%p') AS date_admission, \n
													 IFNULL(DATE_FORMAT(STR_TO_DATE(ce.mgh_setdte,'%Y-%m-%d %H:%i:%s'),'%b %e, %Y %l:%i%p'),DATE_FORMAT(STR_TO_DATE( bill_dte,'%Y-%m-%d %H:%i:%s'),'%b %e, %Y %l:%i%p')) AS date_discharge, acc_coverage, \n
													 med_coverage, xlo_coverage, or_fee, pf_visit, surgeon_coverage, anesth_coverage, patient_claim \n
												from ((((((seg_transmittal as h inner join seg_transmittal_details as d on h.transmit_no = d.transmit_no) \n
													 inner join care_encounter as ce on d.encounter_nr = ce.encounter_nr) inner join care_person as cp on ce.pid = cp.pid) \n
													 inner join care_person_insurance as cpi on cpi.pid = ce.pid and cpi.hcare_id = h.hcare_id) \n
 													 left join seg_encounter_insurance_memberinfo AS simi ON simi.`encounter_nr` = ce.encounter_nr AND simi.hcare_id= h.hcare_id) \n
													 inner join (select bill_dte, encounter_nr, hcare_id, sum(total_acc_coverage) as acc_coverage,sum(total_med_coverage) as med_coverage, sum(total_srv_coverage + total_msc_coverage) as xlo_coverage, \n
																					sum(total_ops_coverage) as or_fee, sum(total_d1_coverage + total_d2_coverage) as pf_visit, sum(total_d3_coverage) as surgeon_coverage, sum(total_d4_coverage) as anesth_coverage \n
																					from seg_billing_coverage as sbc inner join seg_billing_encounter as sbe on (sbc.bill_nr = sbe.bill_nr AND sbe.is_deleted IS NULL) \n
																					group by encounter_nr, hcare_id) as t on \n
															t.encounter_nr = d.encounter_nr and t.hcare_id = h.hcare_id) \n
													 left join (seg_encounter_memcategory as sem inner join seg_memcategory as sm on sem.memcategory_id = sm.memcategory_id) \n
															on sem.encounter_nr = d.encounter_nr \n
												where DATE(h.transmit_dte) >= DATE('".$this->frmdte."') and DATE(h.transmit_dte) <= DATE('".date('Y-m-d',strtotime($this->todte))."')
												order by DATE(h.transmit_dte), cp.name_last, cp.name_first, cp.name_middle";
        }
        #echo $strSQL; exit();
        $result = $this->Conn->Execute($strSQL);
        $this->_count = $result->RecordCount();
        $this->Conn->SetFetchMode(ADODB_FETCH_ASSOC);

        if ($result) {

            $this->Data = array();
            if ($this->caseType) {

                if ($this->caseType == "Surgical") {
                    $ct = '1';
                    $type = 'p';
                }

                if ($this->caseType == "Medical") {
                    $ct = '0';
                    $type = 'm';
                }


                while ($row = $result->FetchRow()) {

                            $this->setPatientName($row);
                            
                    $encNr = $row["encounter_nr"];

                    $encounterDate = date('Y-m-d', strtotime(Billing::getEncounterDate($encNr)));

                    $ctSQL = "SELECT ce.pid,
												'' AS case_type,
												'' as hf,
												'' as pf
									FROM care_encounter `ce`
									INNER JOIN seg_billing_encounter `sbe`
									ON ce.`encounter_nr` = sbe.`encounter_nr`
									INNER JOIN seg_billing_pkg `sbp`
									ON sbe.`bill_nr` = sbp.`ref_no`
									INNER JOIN seg_packages `sp`
									ON sbp.`package_id` = sp.`package_id`
									WHERE ce.encounter_nr = " . $db->qstr($encNr) . "\n
									AND sp.is_surgical= " . $db->qstr($ct) . "\n
									UNION ALL
									SELECT pid , case_type , hf, pf FROM 
							        (
							        SELECT
							          ce.`pid`,
							          p.`case_type`,
							          SUM(sbc.`hci_amount`) AS  hf,
							          SUM(sbc.`pf_amount`) AS pf 
							        FROM
							          care_encounter ce 
							          INNER JOIN seg_billing_encounter sbe 
							            ON ce.`encounter_nr` = sbe.`encounter_nr` 
							          INNER JOIN seg_billing_caserate sbc 
							            ON sbe.`bill_nr` = sbc.`bill_nr` 
							            AND sbe.`is_deleted` IS NULL 
							            AND sbe.`is_final` = '1' 
							          INNER JOIN seg_case_rate_packages p 
							            ON p.`code` = sbc.`package_id` AND  p.`package` <> 0 AND
							            (
											STR_TO_DATE(p.date_from,'%Y-%m-%d') <= STR_TO_DATE('{$encounterDate}','%Y-%m-%d') AND
											STR_TO_DATE(p.date_to,'%Y-%m-%d') >= STR_TO_DATE('{$encounterDate}','%Y-%m-%d')
										)
							        WHERE ce.encounter_nr = " . $db->qstr($encNr) . "\n
							        GROUP BY ce.`encounter_nr`
							         ) AS t WHERE t.case_type = " . $db->qstr($type);
                    #print_r($ctSQL);
                    $ctResult = $this->Conn->Execute($ctSQL);
                    $ctCount = $ctResult->RecordCount();

                    if ($ctCount) {

                        $patient = $this->concatname((is_null($row['name_last']) ? '' : $row['name_last']),
                            (is_null($row['name_first']) ? '' : $row['name_first']),
                            (is_null($row['name_middle']) ? '' : $row['name_middle']));
								$simiInsurance = $row["newInsurance"];

								if ($simiInsurance == '')
										$oldInsurance = $row["oldInsurance"];
								else
										$oldInsurance = $simiInsurance;

                        if ($row["is_principal"] == 0)
                            $member = $this->getPrincipalHolder($row["pid"], $row["hcare_id"], $row["insurance_nr"]);
                        else
                            $member = $patient;

                        #Do the Work around for transmittal Letter Billed Using ACR NEED TO REFACTOR
                        if ($pkg1 = $ctResult->FetchRow()) {
                            $total_2 = (is_null($pkg1['pf']) ? '' : $pkg1['pf']);
                            $total_1 = (is_null($pkg1['hf']) ? '' : $pkg1['hf']);
                        }

                        if ($total_1 == '') {
                            $total_1 = (is_null($row["acc_coverage"]) ? 0 : $row["acc_coverage"]) +
                                (is_null($row["med_coverage"]) ? 0 : $row["med_coverage"]) +
                                (is_null($row["xlo_coverage"]) ? 0 : $row["xlo_coverage"]) +
                                (is_null($row["or_fee"]) ? 0 : $row["or_fee"]) +
                                (is_null($row["hci_coverage"]) ? 0 : $row["hci_coverage"]);


                            $total_2 = (is_null($row["pf_visit"]) ? 0 : $row["pf_visit"]) +
                                (is_null($row["surgeon_coverage"]) ? 0 : $row["surgeon_coverage"]) +
                                (is_null($row["anesth_coverage"]) ? 0 : $row["anesth_coverage"]);


                            $sqlPkg = "SELECT pkg.package_id FROM seg_billing_pkg as pkg
																 inner join seg_billing_encounter as sbe on pkg.ref_no = sbe.bill_nr 
																 WHERE sbe.encounter_nr=$db->qstr($encNr)";

                            $pkgTmp = $this->Conn->Execute($sqlPkg);
                            $pkg = $pkgTmp->FetchRow();

                            if ($pkg) {
                                $pkgId = $pkg['package_id'];
                                $sqlPkgPrc = "SELECT hp.amountlimit,sp.is_surgical,sp.package_name FROM seg_hcare_packages as hp
																		inner join seg_packages as sp on sp.package_id=hp.package_id 
																		WHERE hp.package_id=$db->qstr($pkgId)";

                                $pkgPrc = $this->Conn->Execute($sqlPkgPrc);
                                $pkgPrcTmp = $pkgPrc->FetchRow();
                                $packagePrice = $pkgPrcTmp['amountlimit'];
                                $isSurgical = $pkgPrcTmp['is_surgical'];
                                $pkg_name = $pkgPrcTmp['package_name'];

                                if ($isSurgical) {
                                    $total_2 = (!(strpos(trim(strtoupper($pkg_name)), DEFAULT_NBPKG_NAME, 0) === false) ? DEFAULT_NBPKG_RATE : $packagePrice) * (.4);
                                } else {
                                    $total_2 = (!(strpos(trim(strtoupper($pkg_name)), DEFAULT_NBPKG_NAME, 0) === false) ? DEFAULT_NBPKG_RATE : $packagePrice) * (.3);
                                }
                                $total_1 = $packagePrice - $total_2;
                            }
                        }

								$simiInsurance = $row["newInsurance"];

								if ($simiInsurance == '')
										$oldInsurance = $row["oldInsurance"];
								else
										$oldInsurance = $simiInsurance;
                        $enc_obj=new Encounter;
                        $encInfo=$enc_obj->getEncounterInfo($row["encounter_nr"]);
                        if($encInfo['encounter_type'] == DIALYSIS_PATIENT){
                            $admits_date_ = $enc_obj->getEncounterOldTrans($row["encounter_nr"]);
                            $dis_dates_ = $enc_obj->getEncounterNewTrans($row["encounter_nr"]);
                            $admits_date_ = date('M d, Y h:iA', strtotime($admits_date_));
                            $dis_dates_ = date('M d, Y h:iA', strtotime($dis_dates_));
                        }
                        if ($this->is_detailed) {
                            $this->Data[] = array(
												$oldInsurance,
                                $patient,
                                $member,
                                ($encInfo['encounter_type'] == DIALYSIS_PATIENT)?$admits_date_:$row["date_admission"],
                                ($encInfo['encounter_type'] == DIALYSIS_PATIENT)?$dis_dates_:$row["date_discharge"],
                                number_format($row["acc_coverage"], 2, '.', ','),
                                number_format($row["med_coverage"], 2, '.', ','),
                                number_format($row["xlo_coverage"], 2, '.', ','),
                                number_format($row["or_fee"], 2, '.', ','),
                                number_format($total_1, 2, '.', ','),
                                number_format($row["pf_visit"], 2, '.', ','),
                                number_format($row["surgeon_coverage"], 2, '.', ','),
                                number_format($row["anesth_coverage"], 2, '.', ','),
                                number_format($total_2, 2, '.', ','),
                                number_format((!(strpos(trim(strtoupper($pkg_name)), DEFAULT_NBPKG_NAME, 0) === false) ? DEFAULT_NBPKG_RATE : $packagePrice), 2, '.', ','),
                                number_format($row["patient_claim"], 2, '.', ',')
                            );
                        } else {
                            if ($this->transmit_no != '')
                                $this->Data[] = array(
												$oldInsurance,
                                    strtoupper($patient),                            // made uppercase .... per request by billing .... 06.30.2010 by LST
                                    strtoupper($member),
                                    ($encInfo['encounter_type'] == DIALYSIS_PATIENT)?$admits_date_:$row["date_admission"],
                                    ($encInfo['encounter_type'] == DIALYSIS_PATIENT)?$dis_dates_:$row["date_discharge"],
                                    number_format($total_1, 2, '.', ','),
                                    number_format($total_2, 2, '.', ','),
                                    number_format($total_1 + $total_2, 2, '.', ','),
                                    number_format($row["patient_claim"], 2, '.', ',')
                                );
                            else
                                $this->Data[] = array(
                                    $row["transmitdate"],
												$oldInsurance,
                                    strtoupper($patient),
                                    strtoupper($member),
                                    ($encInfo['encounter_type'] == DIALYSIS_PATIENT)?$admits_date_:$row["date_admission"],
                                    ($encInfo['encounter_type'] == DIALYSIS_PATIENT)?$dis_dates_:$row["date_discharge"],
                                    number_format($total_1, 2, '.', ','),
                                    number_format($total_2, 2, '.', ','),
                                    number_format($total_1 + $total_2, 2, '.', ','),
                                    number_format($row["patient_claim"], 2, '.', ',')
                                );
                        }
                    }
                }

            } else {

							

                while ($row = $result->FetchRow()) {
                    $encNr = $row["encounter_nr"];
                    $encounterDate = date('Y-m-d', strtotime(Billing::getEncounterDate($encNr)));
							        $BillDate = $db->GetOne("SELECT bill_frmdte FROM seg_billing_encounter WHERE is_final = '1' AND ISNULL(is_deleted) AND encounter_nr = ".$db->qstr($encNr));
									
								$BillDate = date("Ymd", strtotime($BillDate));

							        if($BillDate == '19700101'){
							    	    $BillDate = 20140801;
							        }

                                                                $this->setPatientName($row);
                    $sqlPkg2 = "SELECT pid , case_type , hf, pf FROM
										        (
										        SELECT
										          ce.`pid`,
										          p.`case_type`,
										          SUM(sbc.`hci_amount`) AS  hf,
							          			  SUM(sbc.`pf_amount`) AS pf 
										        FROM
										          care_encounter ce 
										          INNER JOIN seg_billing_encounter sbe 
										            ON ce.`encounter_nr` = sbe.`encounter_nr` 
										          INNER JOIN seg_billing_caserate sbc 
										            ON sbe.`bill_nr` = sbc.`bill_nr` 
										            AND sbe.`is_deleted` IS NULL 
										            AND sbe.`is_final` = '1' 
										          INNER JOIN seg_case_rate_packages p 
										            ON p.`code` = sbc.`package_id` AND  p.`package` <> 0 AND
										            (
														STR_TO_DATE(p.date_from,'%Y-%m-%d') <= STR_TO_DATE('{$encounterDate}','%Y-%m-%d') AND
														STR_TO_DATE(p.date_to,'%Y-%m-%d') >= STR_TO_DATE('{$encounterDate}','%Y-%m-%d')
													)
										        WHERE ce.encounter_nr = " . $db->qstr($encNr) . "\n
										       
										         ) AS t";
                                                 /*Remove  GROUP BY ce.`encounter_nr` by Mark 09-27-16*/
                    //echo $sqlPkg2;
                    $flag = false;
                    if ($result2 = $this->Conn->Execute($sqlPkg2)) {
                        if ($result2->RecordCount()) {
                            if ($row1 = $result2->FetchRow()) {
                                $total_2 = (is_null($row1['pf']) ? '' : $row1['pf']);
                                $total_1 = (is_null($row1['hf']) ? '' : $row1['hf']);
                                $flag = true;
                            }
                        }
                    }

                    $patient = $this->concatname((is_null($row['name_last']) ? '' : $row['name_last']),
                        (is_null($row['name_first']) ? '' : $row['name_first']),
                        (is_null($row['name_middle']) ? '' : $row['name_middle']));

								$simiInsurance = $row["newInsurance"];

								if ($simiInsurance == '')
										#$oldInsurance =($row["oldInsurance"]);
										$oldInsurance = $row["oldInsurance"];
								else
										$oldInsurance = $simiInsurance;

								#echo "ins->".$simiInsurance;
								#echo "ins2->".$oldInsurance;
							


                    if ($row["is_principal"] == 0)
										$member = $this->getPrincipalHolder($row["pid"], $row["hcare_id"], $oldInsurance);
                    else
                        $member = $patient;
									#echo "ins2->".$oldInsurance;

                    if (!$flag) {
                        $total_1 = (is_null($row["acc_coverage"]) ? 0 : $row["acc_coverage"]) +
                            (is_null($row["med_coverage"]) ? 0 : $row["med_coverage"]) +
                            (is_null($row["xlo_coverage"]) ? 0 : $row["xlo_coverage"]) +
                            (is_null($row["or_fee"]) ? 0 : $row["or_fee"]) +
                            (is_null($row["hci_coverage"]) ? 0 : $row["hci_coverage"]);;

                        // $total_2 = (is_null($row["pf_visit"]) ? 0 : $row["pf_visit"]) +
                        // 					 (is_null($row["surgeon_coverage"]) ? 0 : $row["surgeon_coverage"]) +
                        // 					 (is_null($row["anesth_coverage"]) ? 0 : $row["anesth_coverage"]);

                        $total_2 = (is_null($row["pf_visit"]) ? 0 : $row["pf_visit"]) +
                            (is_null($row["surgeon_coverage"]) ? 0 : $row["surgeon_coverage"]) +
                            (is_null($row["anesth_coverage"]) ? 0 : $row["anesth_coverage"]);


                        $sqlPkg = "SELECT pkg.package_id FROM seg_billing_pkg as pkg
																 inner join seg_billing_encounter as sbe on pkg.ref_no = sbe.bill_nr 
																 WHERE sbe.encounter_nr='$encNr'";

                        $pkgTmp = $this->Conn->Execute($sqlPkg);
                        $pkg = $pkgTmp->FetchRow();

                        if ($pkg) {
                            $pkgId = $pkg['package_id'];
                            $sqlPkgPrc = "SELECT hp.amountlimit,sp.is_surgical,sp.package_name FROM seg_hcare_packages as hp
																		inner join seg_packages as sp on sp.package_id=hp.package_id 
																		WHERE hp.package_id=$pkgId";

                            $pkgPrc = $this->Conn->Execute($sqlPkgPrc);
                            $pkgPrcTmp = $pkgPrc->FetchRow();
                            $packagePrice = $pkgPrcTmp['amountlimit'];
                            $isSurgical = $pkgPrcTmp['is_surgical'];
                            $pkg_name = $pkgPrcTmp['package_name'];
                            if ($isSurgical) {
                                $total_2 = (!(strpos(trim(strtoupper($pkg_name)), DEFAULT_NBPKG_NAME, 0) === false) ? DEFAULT_NBPKG_RATE : $packagePrice) * (.4);
                            } else {
                                $total_2 = (!(strpos(trim(strtoupper($pkg_name)), DEFAULT_NBPKG_NAME, 0) === false) ? DEFAULT_NBPKG_RATE : $packagePrice) * (.3);
                            }
                            $total_1 = $packagePrice - $total_2;
                        }

                    }


								$simiInsurance = $row["newInsurance"];
								
								if ($row["insurance_nr" == ''])
										$oldInsurance = $row["oldInsurance"];
								else
										$oldInsurance = $simiInsurance;
									
								if(InsuranceCutOff < $BillDate){
									$oldInsurance = $simiInsurance;
									$member = $this->GetNewPrincipalHolder($row['pid'],$encNr);
									echo $Member;
									
								}else{
									if ($simiInsurance == '')
										$oldInsurance = $row["oldInsurance"];
									else
										$oldInsurance = $simiInsurance;
								}
					$enc_obj=new Encounter;
                    $encInfo=$enc_obj->getEncounterInfo($row["encounter_nr"]);
                    if($encInfo['encounter_type'] == DIALYSIS_PATIENT){
                        $admits_date_ = $enc_obj->getEncounterOldTrans($row["encounter_nr"]);
                        $dis_dates_ = $enc_obj->getEncounterNewTrans($row["encounter_nr"]);
                        $admits_date_ = date('M d, Y h:iA', strtotime($admits_date_));
                        $dis_dates_ = date('M d, Y h:iA', strtotime($dis_dates_));
                    }
                    if ($this->is_detailed) {
                        $this->Data[] = array(
												$oldInsurance,
                            $patient,
                            $member,
                            ($encInfo['encounter_type'] == DIALYSIS_PATIENT)?$admits_date_:$row["date_admission"],
                            ($encInfo['encounter_type'] == DIALYSIS_PATIENT)?$dis_dates_:$row["date_discharge"],
                            number_format($row["acc_coverage"], 2, '.', ','),
                            number_format($row["med_coverage"], 2, '.', ','),
                            number_format($row["xlo_coverage"], 2, '.', ','),
                            number_format($row["or_fee"], 2, '.', ','),
                            number_format($total_1, 2, '.', ','),
                            number_format($row["pf_visit"], 2, '.', ','),
                            number_format($row["surgeon_coverage"], 2, '.', ','),
                            number_format($row["anesth_coverage"], 2, '.', ','),
                            number_format($total_2, 2, '.', ','),
                            number_format((!(strpos(trim(strtoupper($pkg_name)), DEFAULT_NBPKG_NAME, 0) === false) ? DEFAULT_NBPKG_RATE : $packagePrice), 2, '.', ','),
                            number_format($row["patient_claim"], 2, '.', ',')
                        );
                    } else {
                        if ($this->transmit_no != '')

                            $this->Data[] = array(

												$oldInsurance,
                                strtoupper($patient),                            // made uppercase .... per request by billing .... 06.30.2010 by LST
                                strtoupper($member),
                                ($encInfo['encounter_type'] == DIALYSIS_PATIENT)?$admits_date_:$row["date_admission"],
                                ($encInfo['encounter_type'] == DIALYSIS_PATIENT)?$dis_dates_:$row["date_discharge"],
                                number_format($total_1, 2, '.', ','),
                                number_format($total_2, 2, '.', ','),
                                number_format($total_1 + $total_2, 2, '.', ','),
                                number_format($row["patient_claim"], 2, '.', ',')
                            );
                        else
                            $this->Data[] = array(
                                $row["transmitdate"],
												$oldInsurance,
                                strtoupper($patient),
                                strtoupper($member),
                                ($encInfo['encounter_type'] == DIALYSIS_PATIENT)?$admits_date_:$row["date_admission"],
                                ($encInfo['encounter_type'] == DIALYSIS_PATIENT)?$dis_dates_:$row["date_discharge"],
                                number_format($total_1, 2, '.', ','),
                                number_format($total_2, 2, '.', ','),
                                number_format($total_1 + $total_2, 2, '.', ','),
                                number_format($row["patient_claim"], 2, '.', ',')
                            );
                    }
                }
            }

        } else
            echo $this->Conn->ErrorMsg();
    }
}

if (isset($_GET['nr']) && $_GET['nr']) {
    $rep = new RegGen_TransmittalLetter('Philippine Health Insurance Corporation', ($_GET['detailed'] == '1'));
    $rep->transmit_no = $_GET['nr'];
    $rep->caseType = $_GET['caseType'];
} else {
    $rep = new RegGen_TransmittalLetter('Southern Philippines Medical Center', ($_GET['detailed'] == '1'), true);
    $rep->transmit_no = "";
    $rep->caseType = $_GET['caseType'];
}

if (isset($_GET['class']) && $_GET['class'])
    $rep->classification = $_GET['class'];
else
    $rep->classification = "";

if (isset($_GET['trdte']) && $_GET['trdte'])
    $rep->transmit_date = strftime("%B %d, %Y", $_GET['trdte']);
else
    $rep->transmit_date = strftime("%B %d, %Y");

if (isset($_GET['fromdte']) && $_GET['fromdte']) {
    $rep->from_date = strftime("%B %d, %Y", $_GET['fromdte']);
    $rep->frmdte = strftime("%Y-%m-%d", $_GET['fromdte']);
} else
    $rep->from_date = strftime("%B %d, %Y");

if (isset($_GET['todte']) && $_GET['todte']) {
    $rep->to_date = strftime("%B %d, %Y", $_GET['todte']);
    $rep->todte = strftime("%Y-%m-%d", $_GET['todte']);
} else {
    $rep->to_date = strftime("%B %d, %Y");
    $rep->todte = strftime("%B %d, %Y");
}


//$rep->TransmittalHeader();
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();