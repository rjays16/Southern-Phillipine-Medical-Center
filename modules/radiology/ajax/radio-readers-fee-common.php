<?php
 //added by: Borj Radiology Readers Fee 2014-10-17
require('./roots.php');
require_once($root_path.'classes/xajax/xajax.inc.php');
$xajax= new xajax($root_path."modules/radiology/ajax/radio-readers-fee-server.php");
$xajax->setCharEncoding("iso-8859-1");
$xajax->registerFunction("savereaders");
$xajax->registerFunction("savedoctorspf");
$xajax->registerFunction("savereadersOB");

    
?>