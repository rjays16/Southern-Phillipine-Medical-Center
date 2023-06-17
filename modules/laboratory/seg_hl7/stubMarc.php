<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path . 'include/care_api_classes/emr/services/EncounterEmrService.php');

$encService = new EncounterEmrService();

try {
    $response = $encService->getPatientEncounter('123', '456');
    print_r($response);
    exit;
} catch (Exception $exc) {
    //echo $exc->getTraceAsString();
}
