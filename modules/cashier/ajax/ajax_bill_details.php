<?php

require('./roots.php');
require($root_path . 'include/inc_environment_global.php');
require_once($root_path . "include/care_api_classes/dialysis/class_dialysis.php");

$pid = isset($_GET['pid']) ? $_GET['pid'] : '';
$billNr = isset($_GET['bill_nr']) ? $_GET['bill_nr'] : '';
getBillDetails($billNr, $pid);

function getBillDetails($billNr, $pid) {
    $objDialysis = new SegDialysis();
    $billDetails = $objDialysis->getBillDetails($billNr, $pid);
    if ($billDetails) {
        echo json_encode($billDetails->getRows());
    }
}

?>
