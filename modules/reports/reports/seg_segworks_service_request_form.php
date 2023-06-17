 <?php
#created by Borj, 04/10/2014 Jasper in Segworks and IHOMP Service Request Form
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');

$baseurl = sprintf(
"%s://%s%s",
isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
$_SERVER['SERVER_ADDR'],
substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"],$top_dir))
);

$data[0]['image_01'] = $baseurl."gui/img/logos/dmc_logo.jpg";

showReport('seg_segworks_service_request_form',$params,$data,'PDF'); 
?>
