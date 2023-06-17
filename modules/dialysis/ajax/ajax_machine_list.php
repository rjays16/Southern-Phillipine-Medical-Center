<?php

require('./roots.php');
require($root_path . 'include/inc_environment_global.php');
require_once($root_path . "include/care_api_classes/dialysis/class_dialysis.php");

$query = isset($_GET['term']) ? $_GET['term'] : '';
getMachineList($query);

function getMachineList($query) {
    $objDialysis = new SegDialysis();
    $machineList = $objDialysis->getAllMachineNumbers($query);
    if ($machineList) {
        echo json_encode($machineList);
    }else {
    	echo json_encode(array());
    }
}

?>
