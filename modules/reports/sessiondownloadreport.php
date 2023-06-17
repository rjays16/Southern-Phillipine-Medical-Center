<?php
	session_start();
	//public function getSessionFinishDownload(){
    	$retVal = "downloading";
    	if(isset($_SESSION['doneloadingreport'])){
    		if(trim($_SESSION['doneloadingreport']) == "done"){
    			$retVal = "done";
    		}
    	}
    	die($retVal);
    	
    //}
?>