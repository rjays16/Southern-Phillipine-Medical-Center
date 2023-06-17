

<?php 
    /*include('db_connection.php'); */
    require_once ('functions.php');

    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require($root_path .'include/inc_special_functions_permission.php');
               

 ?>


<?php 
if (isset($_POST['update'])) {

    $noticeID   = $_POST['noteID'];

    $ack = $db->Execute("UPDATE seg_notice_tbl SET is_deleted = 0, status = 0 WHERE note_id = '$noticeID' ");
            
            if ($ack) {
            redirect_to("notice_manager.php");
            $successMSG = "Notice Succesfully Acknowledged ...";
            } else {
            redirect_to("notice_manager.php");
            $errMSG = "Notice Acknowledgement Failed ...";
            }



	
       }

 ?>
