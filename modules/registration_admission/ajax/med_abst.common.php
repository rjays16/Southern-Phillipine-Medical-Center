<?php

		require('./roots.php');
		
		require_once($root_path.'classes/xajax/xajax.inc.php');
	
		$xajax = new xajax($root_path."modules/registration_admission/ajax/med_abst.server.php");
		
		
		$xajax->setCharEncoding("ISO-8859-1");
		$xajax->registerFunction("populateMedAbstEncRef");
		$xajax->registerFunction("SearchEncRefMedAbstList");
		$xajax->registerFunction("deleteAbstract");
        // $xajax->registerFunction("deleteCertificateConf");
?>