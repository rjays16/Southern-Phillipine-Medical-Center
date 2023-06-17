<?php 
use \SegHis\models\Hospital;
require_once '../../frontend/bootstrap.php';
$hospitalInfo = Hospital::info();

try{

		$context = stream_context_create(array("http"=>array(
	    "method" => "GET",
	    "header" => "Accept: xml/*, text/*, */*\r\n",
	    "ignore_errors" => false,
	    "timeout" => 3,
	)));

	$offline = 0;
	$fp = @fsockopen($hospitalInfo->INV_address, 80, $errno, $errstr, 0.5);
	if (!$fp) {
	  	$offline = 0;
	}
	else{
	 	$offline = 1;
	 	$inv_url = "http://".$hospitalInfo->INV_address.'/'.$hospitalInfo->INV_directory;
	 	$invsite = @file_get_contents($inv_url,false, $context, 0, 1000);

	    if (empty($invsite)) $offline = 0;
	}
}catch (Exception $e){
	$offline = 0;
}
echo "<input type='hidden' id='DAIcon' name='DAIcon' value='".$offline."'>
	 <input type='hidden' id='INV_address' name='INV_address' value='".$hospitalInfo->INV_address."'>";
?>