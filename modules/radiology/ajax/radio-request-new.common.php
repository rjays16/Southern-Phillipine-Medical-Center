<?php
  #edited by VAS 03-21-2012
  #update the ajax version

  require('./roots.php');
  require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
  $xajax = new xajax($root_path."modules/radiology/ajax/radio-request-new.server.php");
    
  $xajax->setCharEncoding("ISO-8859-1");
  $xajax->register(XAJAX_FUNCTION, "populateRequestListByRefNo");
  $xajax->register(XAJAX_FUNCTION, "updateRequest");
  $xajax->register(XAJAX_FUNCTION, "existSegOverrideAmount");
  
  $xajax->register(XAJAX_FUNCTION, "updateCoverage");

  #added by Francis L.G 02-05-2013
  $xajax->register(XAJAX_FUNCTION, "chkCLhis");

  #added by Gervie 04/22/2016
  $xajax->register(XAJAX_FUNCTION, "updatePrintStatus");
  $xajax->register(XAJAX_FUNCTION, "updatePHIC"); 

?>