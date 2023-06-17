<?php
# updaed by VAS 06/15/2019
# using HL7 approach
# parse a HL7 message for bloodbank crossmatching result that fetch from LIS

error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require_once('roots.php');
// require_once($root_path . 'include/inc_jasperReporting.php');
require($root_path.'classes/fpdf/fpdf.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path . 'include/inc_environment_global.php');
include_once($root_path.'include/care_api_classes/class_globalconfig.php');

#added by VAS 06/15/2019
require_once($root_path.'include/care_api_classes/class_blood_bank.php');
require_once($root_path.'frontend/bootstrap.php');
require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_parse_hl7_message.php');
require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_hl7.php');
#---------------- ended here added by VAS 06/15/2019

global $db;
define('FONT_INCREMENT', 1);
define(BLUE, 3);
define(revM, 2);
define(revN, 0);
define(rev, 1);

class blood_pdf extends FPDF {
    var $serials,
        $refno, 
        $id, $unitLast,
        $datafsize, 
        $baseDir,
        $effectivity, 
        $footer, 
        $revision, 
        $color,
        $generic_effectivity,
        $generic_footer,
        $generic_rev,
        $copy,
        $count = 1,
        $putHeader = true,
        $putFooter = true,
        $totalUnitResult,
        $pagecount = 1,
        $lis_order_no,
        $resultLabelData = array(),
        $temporaryY,
        $hosp_info,
        $logoX1 = 8,
        $logoY1 = 8,
        $logoX2 = 83,
        $logoY2 = 4;

    function Results(){
        global $db;
        #added by VAS 06/15/2019
        $bloodObj = new SegBloodBank();
        $parseObj = new seg_parse_msg_HL7();
        $hl7fxnObj = new seg_HL7();
        #$units = $this->id;
        #$this->unitLast = substr($units, -1);
        #$this->unitLast = preg_replace("/[^0-9,.]/", "", $units);
        #$refno = $this->refno;
        
        $testcode = Config::model()->findByPk('bloodbank_default_testcode');
        $pxblood_res = $bloodObj->getPatientBloodResult($this->refno, $testcode);

        if (is_object($pxblood_res)){
            $row = $pxblood_res->FetchRow();
            extract($row);

            #parse result starts here for the ABORH ORU
            #PART 1 : PATIENT ABORH

            #PID INFO
            $order_message = $row['order_hl7_msg'];
            $segments_order = explode($parseObj->delimiter, trim($order_message));
            $details_part1 = $parseObj->bloodparseHL7($segments_order);

            $obr_p1 = $details_part1->obr;

            $arr_physician = explode($parseObj->COMPONENT_SEPARATOR, trim($obr_p1['physician']));
            $physician = $arr_physician[1];

            $arr_loc = explode($parseObj->COMPONENT_SEPARATOR, trim($obr_p1['location']));
            $loc1 = explode("ROOM", $arr_loc[1], 2);
            $location = $loc1[0];
            $clinical_info = $obr_p1['clinical_info'];

            #------------PID INFO

            #OBR SEGMENT
            #OBR for ABORH
            $message = $row['hl7_msg'];
            $segments = explode($parseObj->delimiter, trim($message));
            $details_part2 = $parseObj->bloodparseHL7($segments);
            $obr_p2 = $details_part2->obr;

            $lab_no = $obr_p2['lab_no'];
            $date_received = $obr_p2['date_received'];
            $date_crossmatched = $obr_p2['date_crossmatched'];
            #---OBR SEGMENT for ABORH
        } #end if (is_object($pxblood_res)) 

        #Get age color and result footer
        $glob_obj = new GlobalConfig($GLOBAL_CONFIG);
        $glob_obj->getConfig('adult_age');
        $adult_age = (int)$GLOBAL_CONFIG['adult_age'];

        // added by carriane 02/20/18
        $glob_obj->getConfig('ENovember');
        $ENovember = $GLOBAL_CONFIG['ENovember'];
        $glob_obj->getConfig('EMarch');
        $EMarch = $GLOBAL_CONFIG['EMarch'];

        $getAge = explode(" ", $age);

        $sql_getcompatibility = $db->Prepare("SELECT sbc.adult,sbc.pedia,sbc.child, sbc.generic, sbc.generic_effectivity, sbc.generic_rev FROM seg_blood_compatibility sbc");

        $getcompatibility= $db->Execute($sql_getcompatibility);
        $compatibility = $db->Execute($sql);
        while ($rows = $getcompatibility->FetchRow()) {
            $pedia = $rows['pedia'];
            $adult = $rows['adult'];
            $child = $rows['child'];

            #added by VAS 07/16/2019
            $this->generic_footer = $rows['generic'];
            $this->generic_effectivity = $rows['generic_effectivity'];
            $this->generic_rev = $rows['generic_rev'];
        }

        if((int)$getAge[0]>=$adult_age && $getAge[1]=='years') {
            $this->footer = $adult;
            $this->effectivity = $EMarch;
            $this->revision = rev;
        }
        elseif (((int)$getAge[0]>=5 && $getAge[1]=='months') || ((int)$getAge[0]<19 && ($getAge[1]=='year' || $getAge[1]=='years') ) ){
            $this->footer = $child;
            $this->effectivity = $EMarch;
            $this->revision = rev;
        }
        else {
            $this->footer = $pedia;
            $this->color = BLUE;
            $this->effectivity = $ENovember;
            $this->revision = revN;
        }

        $sql = $db->Prepare("SELECT value FROM seg_define_config WHERE id =".$db->qstr($this->color));

        $ageBracket = $db->Execute($sql);
        $row3 = $ageBracket->FetchRow();

        $this->color = $row3['value'];
        // $this->lis_order_no = $lis_order_no;
        $this->lis_order_no = $lab_no;

        $bloodunits = $db->GetAll("SELECT * FROM seg_blood_received_details WHERE refno =".$db->qstr($this->refno)." AND result NOT IN ('retype')");

        #PART 2 : PATIENT BLOOD PREPARED PRODUCTS
        if(count($bloodunits)){
            $this->totalUnitResult = count($bloodunits);
            $a = 1;
            $countLabel = 0;
            $nofound=0;
           // $printbloodlabel = 0;
            $errorcount = 0;
            $blood_seq = $db->GetAll("SELECT result_code FROM seg_blood_result_seq WHERE is_included = 1 ORDER BY lis_ordering DESC");

            foreach($bloodunits as $units){
                foreach($blood_seq as $seq){
                    $bloodprod = $bloodObj->getPatientPreparedBloodProd($lis_order_no,$pid,$units['serial_no'],$seq['result_code']);

                    if($bloodprod)
                        break;
                }

                if (is_object($bloodprod)){
                    while($bps_row = $bloodprod->FetchRow()){
                        $bps_message = $bps_row['hl7_msg'];
                        $segments_bps = explode($parseObj->delimiter, trim($bps_message));
                        $details_bps = $parseObj->bloodparseHL7($segments_bps);
                        
                        $bpo = $details_bps->bpo;
                        $component = $bpo['blood_component'];
                        $total_no_bags = $bpo['no_units'];

                        $bpx = $details_bps->bpx;
                        
                        $date_done = $bpx['date_crossmatched'];
                        $patient_blood_type = $bpx['patient_blood_type'];

                        $patient_blood_type = explode(" ", $patient_blood_type);
                        if(strpos($patient_blood_type[0], '+') !== false)
                            $patient_blood_type = str_replace("+", " POS", $patient_blood_type[0]);
                        else
                            $patient_blood_type = str_replace("-", " NEG", $patient_blood_type[0]);

                        $expiry_date = $bpx['date_expiry'];
                        $serial_no = $bpx['serial_no'];

                        $volume = $bpx['volume'];

                        $result_compatibility = $bloodObj->getBloodCrossmatchResultDesc($bpx['crossmatching_result']);
                        
                                /*if(stristr(mb_strtolower($result_compatibility), 'incompatible') === FALSE){
                            $printbloodlabel = 1;
                        }else{
                            $printbloodlabel = 0;
                                }*/

                        
                        $arr_source = explode($parseObj->COMPONENT_SEPARATOR, trim($bpx['blood_source']));
                        $sources = $arr_source[0];

                        #DONOR  
                                if (preg_match("/[a-z]/i", strtolower($serial_no)) && strpos($serial_no, '-') !== false) {
                                    $serial_no_temp = substr($serial_no,0,strpos($serial_no, '-'));
                                    $donor = $bloodObj->getDonorInfo($serial_no_temp, $testcode,$date_crossmatched,$bpx['end_date']);
                                }else $donor = $bloodObj->getDonorInfo($serial_no, $testcode);


                        if (is_object($donor)){
                            $donor_row = $donor->FetchRow();

                            $donor_message = $donor_row['hl7_msg'];
                            $segments_donor = explode($parseObj->delimiter, trim($donor_message));
                            $details_donor = $parseObj->bloodparseHL7($segments_donor);
                            $blood_donor = $details_donor->obx[1];

                            $donor_blood_type = $blood_donor['result'];

                            $donor_blood_type = explode(" ", $donor_blood_type);
                            if(strpos($donor_blood_type[0], '+') !== false)
                                $donor_blood_type = str_replace("+", " POS", $donor_blood_type[0]);
                            else
                                $donor_blood_type = str_replace("-", " NEG", $donor_blood_type[0]);

                            $arr_medtech = explode($parseObj->COMPONENT_SEPARATOR, trim($blood_donor['medtech']));
                            $medtech = str_replace('~', '', $arr_medtech[1]);
                        }

                        if ((!trim($result_compatibility))&&(!$nofound)){
                            $result_compatibility = 'No Result Yet';
                        }

                        $patient_info = array('patient_name' => utf8_decode(trim($pat_name)),
                                          'physician' => utf8_decode(trim($physician)),
                                          'ward' => $location,
                                          'diagnosis' => $clinical_info,
                                          'date_birth' => (($birth!='')&&($birth!='0000-00-00'))?date('m/d/Y',strtotime($birth)):'UNKNOWN',
                                          'age' => $age,
                                          'gender' => ($sex=='m')?'Male':'Female',
                                          'hrn' => $pid,
                                          'units' => $a,
                                                  'qtys' => count($bloodunits),
                                          'type_request' => ($urgents == 1) ? 'STAT' : 'ROUTINE',
                                          'lab_no'  => ''
                                     );

                        $result_info = array(
                                        'date_encoded' => ($date_received)?date('m/d/Y h:i A',strtotime($date_received)):'',
                                        'date_crossmatched' => ($date_crossmatched)?date('m/d/Y h:i A',strtotime($date_crossmatched)):'',
                                        'date_done' => ($date_done)?date('m/d/Y h:i A',strtotime($date_done)):'Not Yet Dispensed',
                                        'patient_blood_type' => $patient_blood_type,
                                        'donor_blood_type' => $donor_blood_type,
                                        'result_compatibility' => $result_compatibility,
                                        'serial_no' => ($serial_no)?$serial_no:$this->serials,
                                        'component' => $component,
                                        'source' => $sources,
                                        'expiry_date' => ($expiry_date)?date('m/d/Y h:i A',strtotime($expiry_date)):'',
                                        'medtech' => $medtech,
                                        'volume'  => $volume
                                    );

                        $this->resultLabelData[$countLabel] = array("patient_name" => $pat_name,
                                                    "hrn" => $pid,
                                                    'age' => $age,
                                                    "date_birth" => (($birth!='')&&($birth!='0000-00-00'))?date('m/d/Y',strtotime($birth)):'UNKNOWN',
                                                    "gender" => ($sex=='m')?'Male':'Female',
                                                    "ward" => $location,
                                                    "patient_blood_type" => $patient_blood_type,
                                                    "donor_blood_type"=> $donor_blood_type,
                                                    "result_compatibility"=> $result_compatibility,
                                                    "serial_no"=> ($serial_no)?$serial_no:$this->serials,
                                                    "component"=> $component,
                                                    "expiry_date"=> ($expiry_date)?date('m/d/Y h:i A',strtotime($expiry_date)):'',
                                                            "date_crossmatched"=> ($date_crossmatched)?date('m/d/Y',strtotime($date_crossmatched)):'Not Yet Dispensed',
                                                    "medtech"=> $medtech );
                        $this->PatientData($result_info, $patient_info);
                    }
                    $a++;
                    $countLabel++;
                }else{
                    $errorcount++;
                }
                        
            }

            if($errorcount == count($bloodunits)){
                $result_compatibility = 'No matching Serial Number found. Please double check LIS for results. Thank you';
                $text2 = "Error : ".$result_compatibility;
                echo "<html><head></head><body>".$text2."</body></html>";die;
            }else
                $this->requestLabel();
        }else{
            $result_compatibility = 'No matching Serial Number found.';
            $text2 = "Error : ".$result_compatibility;
            echo "<html><head></head><body>".$text2."</body></html>";
        } #------ if (is_object($bloodprod))   

    }
    
    function Header(){
        if($this->putHeader){
            $objInfo = new Hospital_Admin();
            if ($row_hosp = $objInfo->getAllHospitalInfo()) {
                $this->hosp_info['hosp_agency'] = ucwords($row_hosp['hosp_agency']);
                $this->hosp_info['hosp_name']   = strtoupper($row_hosp['hosp_name']);
                $this->hosp_info['hosp_addr2']   = ucwords($row_hosp['hosp_addr2']);
                $this->hosp_info['hosp_country']   = ucwords($row_hosp['hosp_country']);
            }else {
                $this->hosp_info['hosp_country'] = "Republic of the Philippines";
                $this->hosp_info['hosp_agency']  = "DEPARTMENT OF HEALTH";
                $this->hosp_info['hosp_name']    = "DAVAO MEDICAL CENTER";
                $this->hosp_info['hosp_addr1']   = "JICA Bldg., JP Laurel Avenue, Davao City";
            }

            $this->baseDir = dirname(dirname(dirname(dirname(__FILE__)))).'/';
            if($this->count == 1){
                $this->AddFont('dejavusans', '',"dejavusans.php");
                $this->AddFont('dejavusansb', '',"dejavusansb.php");
                $this->AddFont('dejavusansbi', '',"dejavusansbi.php");
                $this->AddFont('dejavusansi', '',"dejavusansi.php");
            }

            $this->Image($this->baseDir."img/doh.jpg", 32, 11, 21, 21);
            $this->Image($this->baseDir."gui/img/logos/dmc_logo.jpg", 162.5, 6, 27, 27);

            $this->SetFont("dejavusans",'', 9+FONT_INCREMENT);
            $this->Cell(0,4.45,$row_hosp['hosp_country'], 0, 1,'C');
            $this->Cell(0,4.45,$row_hosp['hosp_agency'], 0, 1,'C');
            $this->Cell(0,4.45,"Center for Health Development", 0, 1,'C');
            $this->SetFont("dejavusansb",'', 9+FONT_INCREMENT);

            $this->Cell(0,4.7,ucwords(strtolower($row_hosp['hosp_name'])), 0, 1,'C');
            $this->SetFont("dejavusans",'', 9+FONT_INCREMENT);
            $this->Cell(0,4.7,$row_hosp['hosp_addr2'], 0, 1,'C');
            $current_y = $this->GetY();
            $this->SetY($current_y+2.5);

            $this->addCell(167,4,"Date",date('m/d/Y h:i A'),0,0.07,0.85,8);
            $this->addCell(100,4,"Lab. no.",$this->ptInfo['lab_no'],1,0.3,0.7,8);
            $this->SetFont("dejavusansb",'', 10+FONT_INCREMENT);
            $this->addCell(91.5,4,"","COMPATIBILITY TEST RESULT",0,0.79,0.85, 9, 'B');
            $this->SetFont("dejavusans",'', 8+FONT_INCREMENT);
            $current_y = $this->GetY()+3.7;
            $current_x = 55;
            $this->SetXY($current_x, $current_y);
            $this->addCell(50,4,"",$this->color,0,0.1,0.1, 8);
            $current_y = $this->GetY()-3;
            $current_x = 158.7;
            $this->SetXY($current_x, $current_y);
            $this->addCell(100,6,"LIS Order No.",$this->lis_order_no,0,0.24,0.86,8);
            
        }
        
    }

    function DocumentDetails(){
        $this->Ln();
        $this->SetFont("dejavusansb",'', 10);
        $this->Cell(0,4,"PATIENT'S DATA", 0, 1,'L');
        $this->Rect(5,51, 205, 29,'');

        $this->SetFont("dejavusans",'', 10);
        $current_y = $this->GetY();
        $current_y += 2.5;
        $this->SetY($current_y);
        $this->MultiCell(20,4,"Patient: ");
        $current_x = $this->GetX();
        $current_x += 15;
        $this->SetXY($current_x,$current_y);
        $this->SetFont("dejavusansb",'', 10);

        $patName = $this->ptInfo['patient_name'];
        if(strlen($patName) > 21){
            $tempPatName = substr($this->ptInfo['patient_name'], 22,1);
            if($tempPatName == ' '){
                $patName = substr($this->ptInfo['patient_name'], 0,22);
            }else{
                $patName = substr($this->ptInfo['patient_name'], 0,22);
                $lastSpace = strrpos($patName," ");
                $patName = substr($patName, 0,$lastSpace);
            }
            
        }

        $this->MultiCell(61,4,$patName);
        $current_x = $this->GetX();
        $current_x += 75;
        $this->SetXY($current_x,$current_y);
        $this->SetFont("dejavusans",'', 10);
        $this->MultiCell(61,4,"Date of Birth: ");
        $current_x = $this->GetX();
        $current_x += 99;
        $this->SetXY($current_x,$current_y);
        $this->MultiCell(50,4,$this->ptInfo['date_birth']);

        $current_x = $this->GetX();
        $current_x += 147;
        $this->SetXY($current_x,$current_y);
        $this->MultiCell(50,4,"HRN: ");
        $current_x = $this->GetX();
        $current_x += 160;
        $this->SetXY($current_x,$current_y);
        $this->SetFont("dejavusansb",'', 13);
        $this->MultiCell(50,4,$this->ptInfo['hrn']);

        $phyName = $this->ptInfo['physician'];
        if(strlen($phyName) > 26){
            $tempPhyName = substr($this->ptInfo['physician'], 26,1);
    
            if($tempPhyName == ' '){
                $phyName = substr($this->ptInfo['physician'], 0,26);
            }else{
                $phyName = substr($this->ptInfo['physician'], 0,26);
                $lastSpace = strrpos($phyName," ");
                $phyName = substr($phyName, 0,$lastSpace);
            }
            
        }

        $current_x = $this->GetX();
        $current_x = 5;
        $current_y = $this->GetY();
        $current_y += .5;
        $this->SetXY($current_x,$current_y);
        $this->SetFont("dejavusans",'', 10);
        $this->MultiCell(50,4,"Physician: ");
        $current_x = $this->GetX();
        $current_x += 19;
        $this->SetXY($current_x,$current_y);
        $this->MultiCell(70,4,$phyName);

        $current_x = $this->GetX();
        $current_x += 75;
        $this->SetXY($current_x,$current_y);
        $this->MultiCell(80,4,"Age/Gender:".$this->ptInfo['age']." / ".$this->ptInfo['gender']);
        $current_x = $this->GetX();
        $current_x += 147;
        $this->SetXY($current_x,$current_y);
        $this->MultiCell(50,4,"# of Units: ".$this->ptInfo['units']."  of  ".$this->ptInfo['qtys']);

        $current_x = $this->GetX();
        $current_x = 5;
        $current_y = $this->GetY();
        $current_y += 1;
        $this->SetXY($current_x,$current_y);
        $this->MultiCell(140,4,"Ward: ".$this->ptInfo['ward']);
        $current_x = $this->GetX();
        $current_x += 147;
        $this->SetXY($current_x,$current_y);
        $this->MultiCell(50,4,"Type of Request: ".$this->ptInfo['type_request']);
        
        $current_x = $this->GetX();
        $current_x = 5;
        $current_y = $this->GetY();
        $current_y += 1;
        $this->SetXY($current_x,$current_y);
        $this->MultiCell(80,4,"Diagnosis: ");
        $current_x += 19;
        $this->SetXY($current_x,$current_y);

        if(strlen($this->ptInfo['diagnosis']) <= 165)
            $fontsize = 10;
        elseif(strlen($this->ptInfo['diagnosis']) > 165 && strlen($this->ptInfo['diagnosis']) < 180)
            $fontsize = 9;
        elseif(strlen($this->ptInfo['diagnosis']) >= 180 && strlen($this->ptInfo['diagnosis']) <= 220) $fontsize = 8;
        elseif(strlen($this->ptInfo['diagnosis']) > 220) $fontsize = 6;

        $this->SetFont("dejavusans",'', $fontsize);
        $this->MultiCell(126,4,$this->ptInfo['diagnosis']);
        $current_x = $this->GetX();
        $current_x += 147;
        $this->SetXY($current_x,$current_y);
        $this->MultiCell(75,4,"Indication: _________________");

        $this->OBR_ABORH();
    }

    function OBR_ABORH(){
        
        $this->SetY(85);

        $current_y = $this->GetY();
        $current_x = $this->GetX();
        $this->SetFont("dejavusans",'', 9);
        $this->MultiCell(38,3,"Date Time Encoded: ");
        $current_x+=36;
        $this->SetXY($current_x, $current_y);
        $this->MultiCell(23,4,$this->resInfo['date_encoded']);
        $current_x+=32;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(48,4,"Date Time Crossmatched: ");
        $current_x+=47;
        $this->SetXY($current_x, $current_y);
        $this->MultiCell(23,4,$this->resInfo['date_crossmatched']);
        $current_x+=30;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(32,4,"Date Time Done: ");
        $current_x+=31;
        $this->SetXY($current_x, $current_y);
        $this->MultiCell(23,4,$this->resInfo['date_done']);
        $current_x+=20;
        $this->SetXY($current_x, $current_y+6);

        $this->CompatResults();
    }

    function CompatResults(){
        global $db;

        $this->Ln(3);

        $current_y = $this->GetY();
        $current_x = $this->GetX();

        $this->MultiCell(27,6,"",1);
        $current_x+=27;
        $this->SetXY($current_x, $current_y);
        $this->MultiCell(82,6,"Blood Type",1,"C");
        $current_x+=82;
        $this->SetXY($current_x, $current_y);
        $this->MultiCell(96,6,"Result of Compatibility",1,"C");
        $current_x=5;
        $current_y+=6;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(27,6,"PATIENT",1,"C");
        $current_x+=27;
        $this->SetXY($current_x, $current_y);

        $this->SetFont("dejavusansb",'', 9);
        $this->MultiCell(82,6,$this->resInfo['patient_blood_type'],1,"C");
        $current_x+=82;
        $this->SetXY($current_x, $current_y);
        $this->MultiCell(96,12,$this->resInfo['result_compatibility'],1,"C");
        $current_x=5;
        $current_y+=6;
        $this->SetXY($current_x, $current_y);
        $this->SetFont("dejavusans",'', 9);

        $this->MultiCell(27,6,"DONOR",1,"C");
        $current_x+=27;
        $this->SetXY($current_x, $current_y);
        $this->SetFont("dejavusansb",'', 9);
        $this->MultiCell(82,6,$this->resInfo['donor_blood_type'],1,"C");
        $current_x=5;
        $current_y+=11;
        $this->SetXY($current_x, $current_y);

        $this->SetFont("dejavusans",'', 9);
        $this->MultiCell(19,4,"Serial No.:",0,"L");
        $current_x+=18;
        $this->SetXY($current_x, $current_y);
        $this->SetFont("dejavusansb",'', 9);
        $this->MultiCell(43,4,$this->resInfo['serial_no'],0,"L");
        $current_x+=48;
        $this->SetXY($current_x, $current_y);

        $this->SetFont("dejavusans",'', 9);
        $this->MultiCell(23,4,"Component:",0,"L");
        $current_x+=21;
        $this->SetXY($current_x, $current_y);
        $this->SetFont("dejavusansb",'', 9);
        $this->MultiCell(20,4,$this->resInfo['component'],0,"L");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->SetFont("dejavusans",'', 9);
        $this->MultiCell(17,4,"Volume:",0,"L");
        $current_x+=14;
        $this->SetXY($current_x, $current_y);
        $this->SetFont("dejavusansb",'', 9);
        $this->MultiCell(20,4,$this->resInfo['volume'],0,"L");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->SetFont("dejavusans",'', 9);
        $this->MultiCell(17,4,"Source:",0,"L");
        $current_x+=13;
        $this->SetXY($current_x, $current_y);
        $this->SetFont("dejavusansb",'', 9);
        $this->MultiCell(15,4,$this->resInfo['source'],0,"L");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->SetFont("dejavusans",'', 9);
        $this->MultiCell(24,4,"Expiry Date:",0,"L");
        $current_x+=21;
        $this->SetXY($current_x, $current_y);
        $this->SetFont("dejavusansb",'', 9);
        $this->MultiCell(24,4,$this->resInfo['expiry_date'],0,"L");
        $current_x=9;
        $current_y+=15;
        $this->SetXY($current_x, $current_y);

        $strdatedone = date('Y-m-d H:i:s',strtotime($this->resInfo['date_done']));

        $pathologist = $db->GetAll("SELECT `fn_get_personell_name` (s.personell_nr) fullname, s.* FROM seg_signatory s WHERE s.document_code='pathologist' AND s.end_date > ".$db->qstr($strdatedone));

        $sp = explode("-", $pathologist[0]['section']);

        $this->Image($this->baseDir.$pathologist[0]['seg_signatory'], $sp[0], $sp[1], $sp[2], $sp[3]);
        $this->SetFont("dejavusansb",'', 9);
        $patFullname = mb_strtoupper($pathologist[0]['fullname']).", ".mb_strtoupper($pathologist[0]['title']);
        // $pathologist = Config::model()->findByPk('bloodbank_pathologist');
        $this->MultiCell(85,4,$patFullname,0,"C");
        $current_x+=120;
        $this->SetXY($current_x, $current_y);
        // $this->MultiCell(60,4,$this->resInfo['medtech'],"B","C");
        $this->MultiCell(60,4,'',"B","C");
        //$this->Line(125,132,195,132);
        $current_x=13;
        $current_y+=5;
        $this->SetXY($current_x, $current_y);

        $this->SetFont("dejavusans",'', 10);
        $this->MultiCell(75,4,"Anatomic & Clinical Pathologist",0,"C");
        $current_x+=115;
        $this->SetXY($current_x, $current_y);
        $this->MultiCell(60,4,"Medical Technologist",0,"C");
        
        $this->SetDash(1,1);
        $this->Line(5,145,210,145);
        $this->Ln(1);
        $this->bloodTransfusionRecord();

    }

    function bloodTransfusionRecord(){
        $this->Ln();
        $this->SetFont("dejavusansb",'', 10);
        $this->Cell(0,2,"BLOOD TRANSFUSION RECORD", 0, 1,'C');

        $current_y = $this->GetY();
        $current_x = $this->GetX();
        
        $this->SetFont("dejavusans",'', 9);
        $this->MultiCell(79,6,"[  ] Consent for Transfusion Secured & Signed",0);
        $current_x+=102;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(95,6,"Transfusion Ordered by:____________________________,MD",0);
        $this->Cell(0,4,"Blood Withdrawn from Blood Bank: (For Blood Bank Use )", 0, 1,'L');
        $current_y = $this->GetY();
        $current_x = $this->GetX();
        $this->SetXY($current_x, $current_y);
        $this->SetDash(0,0);

        $this->MultiCell(65,7,"Blood Unit Issued:",1);
        $current_x+=65;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(72,7,"Date:",1);
        $current_x+=72;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(68,7,"Time:",1);
        $current_x=5;
        $current_y+=7;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(65,7,"Blood Unit Issued & Checked by:",1);
        $current_x+=65;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(61,7,"1.","LTB");
        $current_x+=61;
        $this->SetXY($current_x, $current_y);
        $this->SetFont("dejavusansb",'', 9);
        $this->MultiCell(11,7,",RMT","RTB");
        $current_x+=11;
        $this->SetXY($current_x, $current_y);

        $this->SetFont("dejavusans",'', 9);
        $this->MultiCell(57,7,"2. Verified by:",'LTB');
        $current_x+=57;
        $this->SetXY($current_x, $current_y);
        $this->SetFont("dejavusansb",'', 9);
        $this->MultiCell(11,7,",RMT",'RTB');
        $current_x=5;
        $current_y+=7;
        $this->SetXY($current_x, $current_y);

        $this->SetFont("dejavusans",'', 9);
        $this->MultiCell(65,7,"Blood Unit Received & Checked by:",1);
        $current_x+=65;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(50,7,"Name:",1);
        $current_x+=50;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(50,7,"Signature:",1);
        $current_x+=50;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(40,7,"Date & Time:",1);
        $this->SetFont("dejavusans",'U', 9);
        $this->Cell(0,7,"THIS PORTION TO BE ACCOMPLISHED BY THE STAFF ADMINISTERING THE BLOOD UNIT", 0, 1,'L');
        $this->SetFont("dejavusans",'', 9);
        $this->Cell(0,2,"Patient armband, blood bag, compatibility label and information on this slip corresponds?", 0, 1,'L');

        $current_y = $this->GetY()+1;
        $current_x = $this->GetX();
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(55,7,"Patient's Name:",1);
        $current_x+=55;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(65,7,"",1);
        $current_x+=65;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(85,7,"Patient's Bld. Group:",1);
        $current_x=5;
        $current_y+=7;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(55,7,"Blood Unit Number:",1);
        $current_x+=55;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(65,7,"",1);
        $current_x+=65;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(50,7,"Unit Bld. Group:",1);
        $current_x+=50;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(35,7,"Expiry:",1);
        $current_x=5;
        $current_y+=7;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(55,7,"Checked by:",1);
        $current_x+=55;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(65,7,"1.",1);
        $current_x+=65;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(85,7,"2.",1);
        $current_x=5;
        $current_y+=7;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(55,11,"Blood Unit Transfused by:",1);
        $current_x+=55;
        $this->SetXY($current_x, $current_y);

        $this->SetFont("dejavusans",'', 7);
        $this->drawTextBox('Name & Signature', 65, 11, 'C', 'B');
        $current_x+=65;
        $this->SetXY($current_x, $current_y);

        $this->SetFont("dejavusans",'', 9);
        $this->MultiCell(85,7.3,"Date & Time Started:",1, "L");
        $current_y+=7.3;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(85,7.3,"Date & Time Terminated:",1, "L");
        $current_x=5;
        $current_y+=3.6;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(55,11,"Transfusion Terminated by:",1, "L");
        $current_x+=55;
        $this->SetXY($current_x, $current_y);
        $this->SetFont("dejavusans",'', 7);

        $this->drawTextBox('Name & Signature', 65, 11, 'C', 'B');
        $current_x+=65;
        $current_y+=3.7;
        $this->SetXY($current_x, $current_y);

        $this->SetFont("dejavusans",'', 9);
        $this->MultiCell(53,7.2,"No. of Hours Consumed:",1, "L");
        $current_x+=53;
        $this->SetXY($current_x, $current_y);
        $this->MultiCell(32,7.2,"Amount Given:",1, "L");

        $this->vitalSignsMonitoring();
    }

    function vitalSignsMonitoring(){
        $this->SetFont("dejavusansb",'', 10);
        $this->Cell(0,7,"VITAL SIGNS MONITORING", 0, 1,'C');

        $this->SetFont("dejavusans",'', 9);
        $this->drawTextBox('VITAL SIGNS', 62, 11, 'C', 'B');
        $current_x+=67;
        $current_y = $this->GetY()-10.5;
        $this->SetXY($current_x, $current_y);

        $this->SetFont("dejavusans",'', 9);
        $this->MultiCell(26,5.5,"Prior to Blood Transfusion",1, "C");
        $current_x+=26;
        $this->SetXY($current_x, $current_y);

        $this->SetFont("dejavusans",'', 9);
        $this->drawTextBox('During Blood Transfusion', 75, 11, 'C', 'B');
        $current_x+=75;
        $current_y = $this->GetY()-10.5;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(18,11,"",1, "C");
        $current_x+=18;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(24,5.5,"After Transfusion",1, "C");
        $current_x=5;
        $current_y+=11;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(62,6,"",1, "C");
        $current_x+=62;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(26,6,"",1, "C");
        $current_x+=26;
        $this->SetXY($current_x, $current_y);

        $this->SetFont("dejavusansi",'', 9);
        $this->MultiCell(15,6,"15 mins",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(15,6,"30 mins",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(15,6,"1 Hr",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(15,6,"2 Hrs",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(15,6,"3 Hrs",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(18,6,"4 Hrs",1, "C");
        $current_x+=18;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(24,6,"",1, "C");
        $current_x=5;
        $current_y+=6;
        $this->SetXY($current_x, $current_y);

        $this->SetFont("dejavusans",'', 9);
        $this->MultiCell(62,5,"Temperature",1, "L");
        $current_x+=62;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(26,5,"",1, "C");
        $current_x+=26;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(15,5,"",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(15,5,"",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(15,5,"",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(15,5,"",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(15,5,"",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(18,5,"",1, "C");
        $current_x+=18;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(24,5,"",1, "C");
        $current_x=5;
        $current_y+=5;
        $this->SetXY($current_x, $current_y);

        $this->SetFont("dejavusans",'', 9);
        $this->MultiCell(62,5,"Blood Pressure",1, "L");
        $current_x+=62;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(26,5,"",1, "C");
        $current_x+=26;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(15,5,"",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(15,5,"",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(15,5,"",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(15,5,"",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(15,5,"",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(18,5,"",1, "C");
        $current_x+=18;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(24,5,"",1, "C");
        $current_x=5;
        $current_y+=5;
        $this->SetXY($current_x, $current_y);

        $this->SetFont("dejavusans",'', 9);
        $this->MultiCell(62,6,"RR",1, "L");
        $current_x+=62;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(26,6,"",1, "C");
        $current_x+=26;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(15,6,"",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(15,6,"",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(15,6,"",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(15,6,"",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(15,6,"",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(18,6,"",1, "C");
        $current_x+=18;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(24,6,"",1, "C");
        $current_x=5;
        $current_y+=6;
        $this->SetXY($current_x, $current_y);

        $this->SetFont("dejavusans",'', 9);
        $this->MultiCell(62,6,"PR",1, "L");
        $current_x+=62;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(26,6,"",1, "C");
        $current_x+=26;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(15,6,"",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(15,6,"",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(15,6,"",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(15,6,"",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(15,6,"",1, "C");
        $current_x+=15;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(18,6,"",1, "C");
        $current_x+=18;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(24,6,"",1, "C");
        $current_x=5;
        $current_y+=6;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(79,6,"Transfusion Reaction Noted:    [   ] YES    [   ] NO",0, "L");
        $current_x+=125;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(100,6,"Signs and Symptoms: _________________________",0, "L");
        $current_x=10;
        $current_y+=6;
        $this->SetXY($current_x, $current_y);
        
        $this->SetFont("dejavusansb",'', 9);
        #$this->Line(13,283,63,283);
        $this->MultiCell(60,6,"MD","B", "R");
        $current_x+=140;
        $this->SetXY($current_x, $current_y);

        #$this->Line(138,283,198,283);
        $this->MultiCell(55,6,"RN","B", "R");
        $current_x=11;
        $current_y+=7;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(58,3,"Signature of Physician",0, "C");
        $current_x+=137;
        $this->SetXY($current_x, $current_y);

        $this->MultiCell(58,3,"Signature of Registered Nurse",0, "C");
        $current_y+=5;
        $this->SetY($current_y);
        $this->Cell(0,5,"IMPORTANT:", 0, 1,'l');

        $this->SetFont("dejavusans",'', 9);
        $this->SetX(8);
        $this->Cell(0,3,"*   To be accomplished in DUPLICATE. Please attach the ORIGINAL copy in chart & return DUPLICATE COPY to Blood Bank", 0, 1,'l');
        $this->SetX(13);
        $this->Cell(0,7.5," as soon as transfusion is finished.", 0, 1,'l');
        $current_y= $this->GetY();
        $this->SetXY(8,$current_y);
        $this->Cell(0,3,"*   IN CASE OF TRANSFUSION REACTION, INFORM BLOOD BANK &  ACCOMPLISH BLOOD TRANSFUSION REACTION REGISTRY", 0, 1,'l');

        $this->SetDash(1,1);
        $this->Line(5,318,210,318);

        $this->SetFont("dejavusansb",'', 9);
        $this->SetX(16);
        $this->Cell(0,11,"TO BE ACCOMPLISHED BY THE BLOOD TRANSFUSION PERSONNEL ON RETURN OF CONSUMED UNIT", 0, 1,'l');


    }

    function Footer()
    {
        if($this->putFooter){
            $current_y = $this->GetY()-1;
            $current_x = $this->GetX();
            $this->SetFont("dejavusans",'', 9);
            $this->SetY($current_y);
            $this->MultiCell(40,1,"Consumed bag: [    ]",0, "L");
            $current_x += 113;
            $this->SetXY($current_x,$current_y);

            $current_y = $this->GetY();
            $this->MultiCell(87,1,"Take note of amount of remaining blood:_________cc",0, "L");
            $current_x = $this->GetX();
            $current_y = $this->GetY()-1.5;
            $this->SetXY($current_x,$current_y);
            $current_y = $this->GetY();
            $this->Cell(80,11,"Received by: ______________________________________", 0, 1,'l');
            $current_x +=113;
            $this->SetXY($current_x,$current_y);
            $current_y = $this->GetY();
            $this->Cell(40,11,"Date:____________________", 0, 1,'l');
            $current_x += 56.3;
            $this->SetXY($current_x,$current_y);
            $this->Cell(35,11,"Time:_________", 0, 1,'l');

            $this->SetFont("dejavusans",'U', 9);
            $current_y = $this->GetY()-1.5;
            $this->SetY($current_y);
            $this->Cell(35,1,"Retention time: 5 yrs.", 0, 1,'l');
            $current_y = $this->GetY()+6.5;
            $this->SetY($current_y);
            $this->SetFont("dejavusansb",'', 10);
            $current_y = $this->GetY();
            $this->Cell(35,1,$this->footer, 0, 1,'l');

            $this->SetFont("dejavusans",'', 9);
            $current_x = $this->GetX() + 78;
            $this->SetXY($current_x,$current_y);
            $this->Cell(35,1,"Effectivity: ".$this->effectivity, 0, 1,'l');

            $current_x = $this->GetX() + 140;
            $this->SetXY($current_x,$current_y);
            $this->Cell(35,1,"Rev. ".$this->revision, 0, 1,'l');

            $current_x = $this->GetX() + 166;
            $this->SetXY($current_x,$current_y);
            $this->Cell(35,1,"Page ".$this->pagecount." of ".$this->totalUnitResult , 0, 1,'l');

            if($this->count == 2){
                $tempx = 166.5;  
                $this->copy = "Duplicate";
            }else {
                $this->copy = "Original";
                $tempx = 166;
            }

            $current_x = $this->GetX() + $tempx;
            $current_y += 4;
            $this->SetXY($current_x,$current_y);
            $this->Cell(35,1,"(".$this->copy.")", 0, 1,'l');
            $this->count++;
            if($this->count > 2)
                $this->pagecount++;
            
        }

    }

    function PatientData($resultinfo, $patientinfo){
        $this->AddPage();

        $this->ptInfo = $patientinfo;
        $this->resInfo = $resultinfo;
        $this->count = 1;

        while($this->count < 2){
            
            $this->DocumentDetails($patientinfo);

            if($this->count == 1){
                $this->AddPage();
                $this->DocumentDetails($patientinfo);
            }
        }
    }

    function LabelHeader(){

        $this->Image($this->baseDir."img/doh.jpg", $this->logoX1, $this->logoY1, 14, 14);
        $this->Image($this->baseDir."gui/img/logos/dmc_logo.jpg", $this->logoX2, $this->logoY2, 18, 18);
        $this->SetFont("times",'', 9);
        $current_y = $this->GetY();
        $current_x = $this->GetX();
        $this->SetXY($current_x,$current_y);
        $this->Cell(95,4.45,$this->hosp_info['hosp_country'], 0, 1,'C');
        $this->SetX($current_x);
        $this->Cell(95,4.45,$this->hosp_info['hosp_agency'], 0, 1,'C');
        $this->SetX($current_x);
        $this->Cell(95,4.45,"Center for Health Development", 0, 1,'C');
        $this->SetX($current_x);
        $this->SetFont("times",'B', 8);
        $this->Cell(95,4.45,$this->hosp_info['hosp_name'], 0, 1,'C');
        $this->SetX($current_x);
        $this->SetFont("times",'', 9);
        $this->Cell(95,4.45,$this->hosp_info['hosp_addr2'], 0, 1,'C');
        $this->SetX($current_x);
        $this->SetFont("times",'B', 11);
        $current_y = $this->GetY()+3;
        $this->SetXY($current_x,$current_y);
        $this->Cell(95,6,"COMPATIBILITY LABEL", 0, 1,'C');
        $current_y = $this->GetY()+5;
        $this->SetXY($current_x,$current_y);
        $this->logoX1 += 110;
        $this->logoX2 += 110;
    }

    function requestLabel(){
        $this->putHeader = false;
        $this->AddPage();

        $b = 0;
        $countlabel = 1;
        $this->SetY(5);
        $thisX = $this->GetX();
        $thisY = 0;
        $this->Line(108,5,108,130);
        while($b < $this->totalUnitResult){
            $this->LabelHeader();

            $current_x = $thisX;
            $current_y = $this->GetY();
            $this->SetFont("times",'B', 10);
            $this->MultiCell(40,3,"Patient's Name :" ,0, "L");
            $current_x += 38;
            $this->SetXY($current_x,$current_y);

            if(strlen($this->resultLabelData[$b]['patient_name']) > 24)
                $patName_y = 4;
            else $patName_y = 0;

            $this->SetFont("times",'B', 12);
            $this->MultiCell(63,4,$this->resultLabelData[$b]['patient_name'],0, "L");

            $current_x = $thisX;
            $current_y += 5+$patName_y;
            $this->SetXY($current_x,$current_y);

            $this->SetFont("times",'B', 10);
            $this->MultiCell(40,3,"HRN :",0, "L");
            $current_x += 38;
            $this->SetXY($current_x,$current_y);

            $this->SetFont("times",'B', 12);
            $this->MultiCell(100,3,$this->resultLabelData[$b]['hrn'],0, "L");
            $current_x = $thisX;
            $current_y += 5;
            $this->SetXY($current_x,$current_y);

            $this->SetFont("times",'B', 10);
            $this->MultiCell(40,3,"Birth Date :",0, "L");
            $current_x += 38;
            $this->SetXY($current_x,$current_y);

            $this->SetFont("times",'B', 12);
            $this->MultiCell(100,3,$this->resultLabelData[$b]['date_birth'],0, "L");
            $current_x = $thisX;
            $current_y += 5;
            $this->SetXY($current_x,$current_y);

            $this->SetFont("times",'B', 10);
            $this->MultiCell(40,3,"Age/Gender :",0, "L");
            $current_x += 38;
            $this->SetXY($current_x,$current_y);

            $this->SetFont("times",'B', 12);
            $this->MultiCell(100,3,$this->resultLabelData[$b]['age'].' / '.$this->resultLabelData[$b]['gender'],0, "L");
            $current_x = $thisX;
            $current_y += 5;
            $this->SetXY($current_x,$current_y);

            $this->SetFont("times",'B', 10);
            $this->MultiCell(40,3,"Ward :",0, "L");
            $current_x += 38;
            $this->SetXY($current_x,$current_y);

            $this->SetFont("times",'B', 10);
            $this->MultiCell(60,3,$this->resultLabelData[$b]['ward'],0, "L");
            $current_x = $thisX;
            $current_y =$this->GetY() + 1;
            $this->SetXY($current_x,$current_y);

            $this->SetFont("times",'B', 10);
            $this->MultiCell(46,3,"Patient's Blood Type :",0, "L");
            $current_x += 38;
            $this->SetXY($current_x,$current_y);

            $this->SetFont("times",'B', 10);
            $pat_blood = explode(" ",$this->resultLabelData[$b]['patient_blood_type']);
            $this->SetFont("times",'B', 12);

            if(strlen($pat_blood[0]) < 2)
                $cellLen = 5;
            else $cellLen = 7;

            $this->Cell($cellLen,3,$pat_blood[0],0, "L");
            $this->SetFont("times",'B', 10);
            $this->Cell(100,3,$pat_blood[1],0, "L");
            $current_x = $thisX;
            $current_y += 5;
            $this->SetXY($current_x,$current_y);

            $this->SetFont("times",'B', 10);
            $this->MultiCell(46,3,"Donor's Blood Type :",0, "L");
            $current_x += 38;
            $this->SetXY($current_x,$current_y);

            $this->SetFont("times",'B', 10);
            $donor_blood = explode(" ",$this->resultLabelData[$b]['donor_blood_type']);
            $this->SetFont("times",'B', 12);

            if(strlen($donor_blood[0]) < 2)
                $cellLen = 5;
            else $cellLen = 7;

            $this->Cell($cellLen,3,$donor_blood[0],0, "L");
            $this->SetFont("times",'B', 10);
            $this->Cell(100,3,$donor_blood[1],0, "L");
            $current_x = $thisX;
            $current_y += 5;
            $this->SetXY($current_x,$current_y);

            $this->SetFont("times",'B', 10);
            $this->MultiCell(40,3,"Result :",0, "L");
            $current_x += 38;
            $this->SetXY($current_x,$current_y);

            $this->SetFont("times",'B', 10);
            $this->MultiCell(100,3,$this->resultLabelData[$b]['result_compatibility'],0, "L");
            $current_x = $thisX;
            $current_y += 5;
            $this->SetXY($current_x,$current_y);

            $this->SetFont("times",'B', 10);
            $this->MultiCell(40,3,"Serial Number :",0, "L");
            $current_x += 38;
            $this->SetXY($current_x,$current_y);

            $this->SetFont("times",'B', 13);
            $this->MultiCell(100,3,$this->resultLabelData[$b]['serial_no'],0, "L");
            $current_x = $thisX;
            $current_y += 5;
            $this->SetXY($current_x,$current_y);

            $this->SetFont("times",'B', 10);
            $this->MultiCell(40,3,"Component :",0, "L");
            $current_x += 38;
            $this->SetXY($current_x,$current_y);

            $this->SetFont("times",'B', 10);
            $this->MultiCell(100,3,$this->resultLabelData[$b]['component'],0, "L");
            $current_x = $thisX;
            $current_y += 5;
            $this->SetXY($current_x,$current_y);

            $this->SetFont("times",'B', 10);
            $this->MultiCell(40,3,"Expiry Date :",0, "L");
            $current_x += 38;
            $this->SetXY($current_x,$current_y);

            $this->SetFont("times",'B', 13);
            $this->MultiCell(100,3,$this->resultLabelData[$b]['expiry_date'],0, "L");
            $current_x = $thisX;
            $current_y += 5;
            $this->SetXY($current_x,$current_y);

            $this->SetFont("times",'B', 10);
            $this->MultiCell(46,3,"Crossmatched Date :",0, "L");
            $current_x += 38;
            $this->SetXY($current_x,$current_y);

            $this->SetFont("times",'B', 10);
            $this->MultiCell(100,3,$this->resultLabelData[$b]['date_crossmatched'],0, "L");
            $current_x = $thisX;
            $current_y += 5;
            $this->SetXY($current_x,$current_y);

            $current_x = $thisX;
            $current_y += 4;
            $this->SetDash(0,0);
            $this->SetXY($current_x,$current_y);
            // $this->Cell(80,3,strtoupper($this->resultLabelData[$b]['medtech']), 'B', 1,'C');
            $this->Cell(80,3,'', 'B', 1,'C');
            #$current_y = $this->GetY() + 2;
            #$this->Line(5,$current_y,85,$current_y);

            $this->Ln(2);
            $current_x = $thisX;
            $current_y += 4;
            $this->SetFont("times",'B', 10);
            $this->SetXY($current_x,$current_y);
            $this->Cell(80,3,'Medical Technologist', 0, 1,'C');

            $current_x = $thisX;
            $current_y += 15;
            $this->SetXY($current_x,$current_y);
            $this->SetFont("times",'B', 9);
            $current_y = $this->GetY();
            $this->Cell(35,3,$this->generic_footer, 0, 1,'l');

            $current_x += 30;
            $this->SetXY($current_x,$current_y);
            $this->SetFont("times",'B', 9);
            $this->Cell(35,3,"Effectivity: ".$this->generic_effectivity, 0, 1,'l');

            $current_x += 50;
            $this->SetXY($current_x,$current_y);
            $this->SetFont("times",'B', 9);
            $this->Cell(35,3,"Rev. ".$this->generic_rev, 0, 1,'l');

            $this->SetDash(1,1);
            $current_y = $this->GetY() + 5;
            $this->Line($thisX,$current_y,$thisX+90,$current_y);
            $b++;

            $thisX = 115;
            $this->SetXY($thisX,5+$thisY); 
            
            if($countlabel == 4 && $b < $this->totalUnitResult){
                $thisX = 5;
                $this->putFooter = false;
                $this->logoX1 = 8;
                $this->logoY1 = 8;
                $this->logoX2 = 83;
                $this->logoY2 = 4;
                $this->AddPage();
                $this->Line(109,5,109,130);
                $countlabel = 0;
                $this->SetY(5);
                $thisY = 0;
            }

            if($countlabel == 2 && $b < $this->totalUnitResult){
                $this->SetDash(0,0);
                $this->Line(108,135,108,260);
                $thisX = 5;
                $thisY = 132.4;
                $this->putFooter = false;
                $this->logoY1 += 132;
                $this->logoY2 += 132;
                $this->logoX1 = 8;
                $this->logoX2 = 83;
                $current_y += 5;
                $this->SetY($current_y);
            }

            $countlabel++;

            
        }
        
        $this->putFooter = false;
    }

    function addCell($w,$h,$label,$text,$ln=0,$d1=0.3,$d2=0.7,$fsize=9,$b){
        $this->SetFont('dejavusans','',$fsize+FONT_INCREMENT);

        if($label != "")
            $colon = ":";
        else $colon = "";

        if($this->datafsize != '') $datasize = $this->datafsize;
        else $datasize = $fsize;

        $this->Cell($w * $d1,$h,$label.$colon, 0, 0,'L');
        $this->SetFont('dejavusans'.strtolower($b),'',$datasize+FONT_INCREMENT);
        $this->Cell($w * $d2,$h,$text, 0, $ln,'L');
        $this->SetFont('dejavusans','',$fsize+FONT_INCREMENT);
    }

    function blood_pdf($id,$refno){
        $this->FPDF('p','mm','legal');
        $this->SetTitle("Results", true);
        $this->refno = $refno;
        $this->id = $id;
        $this->SetMargins(5,10,5);
    }

    function outputFile($mode=false){
        $this->AliasNbPages();
        $this->Results();
        if($mode){
            return $this->Output('','s');
        }else{
            $this->Output();
        }
    }
}

if(isset($_GET['id']) && isset($_GET['refno'])){
    $fpdf = new blood_pdf($_GET['id'],$_GET['refno']);
    $fpdf->outputFile();
}

?>