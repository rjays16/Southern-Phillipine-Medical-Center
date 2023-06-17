<?php
//added by Nick 1/24/2014
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'classes/fpdf/fpdf.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/laboratory/seg-lab-report-hl7.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
$objInfo = new Hospital_Admin();
$row_hosp = $objInfo->getAllHospitalInfo();

global $db;


/**
    Make a nested path , creating directories down the path
    Recursion !!
*/
function make_path($path){
    $dir = pathinfo($path , PATHINFO_DIRNAME);
     
    if( is_dir($dir) ){
        
        return true;

    }else{
        
        if( make_path($dir) ){

            if( mkdir($dir) ){
                chmod($dir , 0775);
                return true;
            }

        }
    }
     
    return false;
}


$dirpath = $row_hosp['LIS_folder_path_pdf_dms'];

$limit = 100;


#for inpatients' record
#static
$date_start = '2014-08-01';


$sql = "SELECT DISTINCT s.encounter_nr, e.encounter_type, h.pid, 
        o.refno, h.lis_order_no, e.parent_encounter_nr
        FROM seg_hl7_hclab_msg_receipt h
        LEFT JOIN seg_lab_hclab_orderno o ON o.lis_order_no=h.lis_order_no
        LEFT JOIN seg_lab_serv s ON s.refno=o.refno 
        LEFT JOIN care_encounter e ON e.encounter_nr=s.encounter_nr
        WHERE h.msg_type_id = 'ORU'
        AND h.event_id = 'R01'
        AND h.is_stored = 0
        AND e.encounter_type IN ('3','4')
        AND DATE(e.admission_dt) BETWEEN ".$db->qstr($date_start)." AND DATE(NOW())
        GROUP BY h.pid, h.lis_order_no
        LIMIT $limit";

$rs=$db->Execute($sql);


if($rs){
    echo "<br>RETRIEVING INPATIENTS starts here.... <br>";

    if($rs->RecordCount()){
        while($row=$rs->FetchRow()){
            $pid = $row['pid'];
            $lis = $row['lis_order_no'];

            #get ER encounter, parent encounter of admission
            $arrayERenc = array();
            if (trim($row['parent_encounter_nr'])<>'')
                array_push($arrayERenc, $row['parent_encounter_nr']);

            $newArrER = array_unique($arrayERenc);

            #for inpatient's record
            if ($lis){
                $enc = $row['encounter_nr'];
                $refno = $row['refno'];

                #edited by VAN 09-16-2014
                $location = $dirpath;
                if ($enc){
                    #for hrn
                    $location .= $pid;

                    #for encounter
                    $location .= '/'.$enc; 
                    
                    $pdf_filename = $pid . "_" . $enc . "_" . $lis .".pdf";

                    $fullpath = $location. '/' .$pdf_filename;
                    
                    make_path( $fullpath );

                    ob_start();
                    $pdf = new lab_pdf($pid,$lis);
                    $labresult = $pdf->outputFile(true);
                    ob_clean();
                    file_put_contents($fullpath, $labresult);

                    if(file_exists(($fullpath))){
                        $pdf->setIsStored2(1);
                        echo "<span style='color: #007722'>Created :: $fullpath</span><br>";
                    }else{
                        echo "<span style='color: #FF0000'>Failed :: $fullpath</span><br>";
                    }
                }
            }

        }
    }else{
        echo "<span style='color: #007722'>No records found</span><br>";
    }
}
#========== end for inpatients

#parent er encounter of patient admitted the whole month of aug 2014
$sql_er = "SELECT DISTINCT s.encounter_nr, e.encounter_type, h.pid, 
        o.refno, h.lis_order_no, e.parent_encounter_nr
        FROM seg_hl7_hclab_msg_receipt h
        LEFT JOIN seg_lab_hclab_orderno o ON o.lis_order_no=h.lis_order_no
        LEFT JOIN seg_lab_serv s ON s.refno=o.refno 
        LEFT JOIN care_encounter e ON e.encounter_nr=s.encounter_nr
        WHERE h.msg_type_id = 'ORU'
        AND h.event_id = 'R01'
        AND h.is_stored = 0
        AND e.encounter_nr IN ('".implode("' \n   , '",$newArrER)."')
        GROUP BY h.pid, h.lis_order_no";

$rs_er=$db->Execute($sql_er);

if($rs_er){
    echo "<br>RETRIEVING ER PATIENTS starts here.... <br>";
    
    if($rs_er->RecordCount()){
        while($row=$rs_er->FetchRow()){
            $pid = $row['pid'];
            $lis = $row['lis_order_no'];

            #for inpatient's record
            if ($lis){
                $enc = $row['encounter_nr'];
                $refno = $row['refno'];

                #edited by VAN 09-16-2014
                $location = $dirpath;
                if ($enc){
                    #for hrn
                    $location .= $pid;

                    #for encounter
                    $location .= '/'.$enc; 
                    
                    $pdf_filename = $pid . "_" . $enc . "_" . $lis .".pdf";

                    $fullpath = $location. '/' .$pdf_filename;
                    
                    make_path( $fullpath );

                    ob_start();
                    $pdf = new lab_pdf($pid,$lis);
                    $labresult = $pdf->outputFile(true);
                    ob_clean();
                    file_put_contents($fullpath, $labresult);

                    if(file_exists(($fullpath))){
                        $pdf->setIsStored2(1);
                        echo "<span style='color: #007722'>Created :: $fullpath</span><br>";
                    }else{
                        echo "<span style='color: #FF0000'>Failed :: $fullpath</span><br>";
                    }
                }
            }

        }
    }else{
        echo "<span style='color: #007722'>No records found</span><br>";
    }
}
#========== end for er patients
