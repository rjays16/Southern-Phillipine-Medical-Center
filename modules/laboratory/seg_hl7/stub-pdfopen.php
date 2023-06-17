<?php
    #for cron schedule
    #per minute
	# created by VAN 01-12-2012
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
    $objInfo = new Hospital_Admin();
    
    require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
    $srvObj=new SegLab();
    
    ##getting the binary file and viewing
    global $db;
    
    $filename = "HL7WNFS_01519621_cbc2.HL7";

    $sql = "SELECT * FROM seg_hl7_hclab_msg_receipt WHERE filename=".$db->qstr($filename);
    
    $rs = $db->execute($sql);
    if ($db->Affected_Rows()) {
        $row = $rs->FetchRow();
        $message = $row['result_pdf'];
        #echo "print = ".$message;

        file_get_contents($message);
        
        header("Content-type: application/pdf");  
        
        echo $message;
        
    }else{
        $text2 = "Error : ".$sql;
        echo "<html><head></head><body>".$text2."</body></html>";
    }    
    
?>
