 <?php
#created by Borj, 4/10/2014 Jasper in Segworks and IHOMP Service Request Form
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');

$data[0] = "0";
showReport('seg_ihomp_service_request_form',$params,$data,'PDF'); 
?>
