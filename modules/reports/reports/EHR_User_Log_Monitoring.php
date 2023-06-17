<?php 
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);

require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path . 'include/care_api_classes/ehr/UserLoginLogs.php');
include('parameters.php');

$from = date('Y-m-d', $_GET['from_date']);
$to = date('Y-m-d' ,$_GET['to_date']);
// var_dump($from); die;

try {
	$logger = new UserLoginLogs;
	$logs = $logger->UserLoginLogs($from, $to);
	
	$logsfix = urldecode($logs);
	#$logsfix = preg_replace('/("(.*?)"|(\w+))(\s*:\s*)\+?(0+(?=\d))?(".*?"|.)/s', '"$2$3"$4$6', $logs);
	$logsfix = stripslashes($logsfix);
	$logs = json_decode($logsfix, true);
	/*echo "<pre>";
	print_r($logsfix);
	echo "</pre>"; 
	die();*/
} catch (Exception $e) { 
    echo $e->getTraceAsString();
}	


$baseurl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['HTTP_HOST'],
    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
);


$img = $baseurl . "gui/img/logos/dmc_logo.jpg";
$params->put('img_spmc', $img);

if(count($logs) > 0) {

	#added by raymond - convert unicode value to actual character
	 $unicode = array('u00d1','u00f1');
	 $actual = array('Ñ','ñ'); 
	
	foreach ($logs as $key => $log) {

		$date = new DateTime(@$log['login_dt']);
		$fullname = @$log['person']['name_first'].' '.@$log['person']['name_middle'].' '.@$log['person']['name_last'];
		$login_dt = $date->format('F j, Y h:i:s A');
		
		#raymond - replace unicode value to its actual character
		$fullname = str_replace($unicode, $actual, $fullname);
		
		#utf8_decode($fullname) also works
		$data[$key]['user_fullname'] = strtoupper(utf8_decode(utf8_decode(utf8_encode($fullname))));
		$data[$key]['dt_login'] = $login_dt;
		$data[$key]['empty_result'] = " ";

	}
} else {
	$empty_text = 'no found data base on the given date/s.';
	$data[0]['user_fullname'] = " ";
	$data[0]['dt_login'] = " ";
	$data[0]['empty_result'] = ucwords($empty_text);
}

$params->put('from_date', date('F j, Y', $_GET['from_date']));
$params->put('to_date', date('F j, Y', $_GET['to_date']));

?>