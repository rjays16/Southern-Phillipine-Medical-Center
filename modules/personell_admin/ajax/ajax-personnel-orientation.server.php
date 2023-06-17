<?php
//Added By John
        function saveOrientation($personell_nr,$starttime,$endtime,$date,$module,$title,$venue){
            global $db, $_SESSION, $root_path;
            $objResponse = new xajaxResponse();
            $VAL1 = utf8_encode(utf8_decode(utf8_encode($title)));
            $sql="INSERT INTO `seg_orientation_list` (`employee_number`,`starting_time_of_orientation`,`end_time_of_orientation`,
            `date_of_orientation`,`module_orientation`,`title_orientation`,`venue`,`added_by`,`created_at`,`history`) VALUES('".$personell_nr."','".$starttime."','".$endtime."','".$date."','".$module."','".addslashes($title)."','".$venue."','".$_SESSION['sess_user_name']."','".date('Y-m-d H:i:s')."','"."Created: ".date('Y-m-d H:i:s')." = ".$_SESSION['sess_user_name']."')"; 
            $db->Execute($sql);
            //die($sql);
            return $objResponse;
        }

        function removeFromList($orientation_list_id){
            global $db, $_SESSION, $root_path;
                 $objResponse = new xajaxResponse();
                 $sql="UPDATE seg_orientation_list SET is_deleted = 1 , modify_id = '".$_SESSION['sess_user_name']."' , modify_date = '".date('Y-m-d H:i:s')."', history = CONCAT(history,'\n','Deleted: ".date('Y-m-d H:i:s')." = ".$_SESSION['sess_user_name']."') WHERE orientation_list_id='".$orientation_list_id."'";
            $db->Execute($sql); 
            //die($sql);
            return $objResponse;
        }

        function updateFromOrientation($orientation_list_id,$starting_time_of_orientation,$end_time_of_orientation,$date_of_orientation,$module_orientation,$title_orientation,$venue){
            global $db, $_SESSION, $root_path;
            $objResponse = new xajaxResponse();
            $VAL1 = utf8_decode(utf8_encode(utf8_decode($title_orientation)));
            $sql="UPDATE seg_orientation_list SET starting_time_of_orientation='".$starting_time_of_orientation."',end_time_of_orientation='".$end_time_of_orientation."',date_of_orientation='".$date_of_orientation."', module_orientation='".$module_orientation."', title_orientation='".addslashes($title_orientation)."', venue='".$venue."' , modify_id = '".$_SESSION['sess_user_name']."' , modify_date = '".date('Y-m-d H:i:s')."', history = CONCAT(history,'\n','Updated: ".date('Y-m-d H:i:s')." = ".$_SESSION['sess_user_name']."') WHERE orientation_list_id='".$orientation_list_id."'";
             $db->Execute($sql);
             // die($sql);
	         return $objResponse;

        }

    require_once('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require($root_path."modules/personell_admin/ajax/ajax-personnel-orientation.common.php");
    $xajax->processRequests();
?>