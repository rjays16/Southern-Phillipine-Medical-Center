<?php 
    /*include('db_connection.php'); */
    require_once ('functions.php');
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require "{$root_path}include/care_api_classes/class_notice.php";
    $objnotice = new Notice;
 ?>

<?php 
if (!$_SESSION['sess_login_username']) {
        redirect_to("notice_orientation.php");
    } 
    else {
if (isset($_POST['acknwledge'])) {
	
            $ack = $objnotice->getAcknowledge($_POST);

            if ($ack) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            $successMSG = "Notice Successfully Acknowledged ...";
            } else {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            $errMSG = "Notice Acknowledgement Failed ...";
            }
       }
   }


 ?>