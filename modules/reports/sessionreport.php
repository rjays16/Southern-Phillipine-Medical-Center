<?php
session_start();
if(isset($_POST['link'])){
	$_SESSION['loading_report_link'] = $_POST['link'];
}else{
	$_SESSION['loading_report_link'] = 'wla sulod';
}
?>