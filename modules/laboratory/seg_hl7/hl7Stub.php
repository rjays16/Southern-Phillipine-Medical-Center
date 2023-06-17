<?php
//edited by justinttan 11/10/2014
/**
 * Send HL7 Data to X2Wave Lab POST
 */
require('./roots.php');
require($root_path . 'include/inc_environment_global.php');
require_once($root_path . 'include/care_api_classes/emr/services/LaboratoryEmrService.php');
require_once($root_path . 'include/care_api_classes/emr/services/PatientEmrService.php');
require_once($root_path . 'include/care_api_classes/emr/services/DoctorEmrService.php');
require_once($root_path . 'include/care_api_classes/emr/services/EncounterEmrService.php');
require_once($root_path . 'include/care_api_classes/emr/class_emr.php');
require_once($root_path . 'include/care_api_classes/seg_hl7/seg_class_hl7.php');
require_once($root_path . 'include/care_api_classes/class_personell.php');
require_once($root_path . 'include/care_api_classes/class_person.php');
require_once($root_path . 'include/care_api_classes/class_department.php');
require_once($root_path . 'include/care_api_classes/class_encounter.php');

if (isset($_GET['pid']) && isset($_GET['did'])) {

    $pid = $_GET['pid'];
    $personell = new Personell();
    $did = $personell->getPathologistId();
    $patientService = new PatientEmrService();
    $doctorService = new DoctorEmrService;
    $encService = new EncounterEmrService();
   
//     try {
//         $res = $encService->saveEncounter($pid, '2014500075');
// //        if ($patientService->savePatient($pid)) {
// //            echo 'save patient <br>';
// //        }
//     } catch (Exception $exc) {
//         die('error in exception');
//     }
//     // exit;
//     if ($doctorService->saveDoctor($did)) {
//         echo 'save doctor <br>';
//     }

    $hl7fxnObj = new seg_HL7();
    $service = new LaboratoryEmrService();
    try {
        $lab = $service->sendLabHl7(array(
            'HISPatientId' => $pid,
            'HL7Id' => 'HL7WNFS_00680018.hl7',
            'HL7Content' => base64_encode('MSH|^~\\&|HCLAB||HIS||20130610172825||ORU^R01|HCL00037934821|P|2.3||||||8859
PID|1||2520221||^BUHAT BAGO||201306100000|M
OBR|1|11495705|130107327|CBCPLT^CBC + PLT|R|20130610162316||||||||20130610161208||102613^CAROL J. BUAYA||21^CHARITY WARD (PEDIA)||||20130610172825||||||21^CHARITY WARD (PEDIA)
OBX|1|ST|HGB^Hemoglobin||196.0|g/L|135 - 175|H|||F|||20130610172824|CTRLAB^CENTRAL LAB|DEL^JUNADEL LARGO KATO,RMT
OBX|2|ST|HCT^Hematocrit||0.55||0.40 - 0.52|H|||F|||20130610172824|CTRLAB^CENTRAL LAB|DEL^JUNADEL LARGO KATO,RMT
OBX|3|ST|RBC^RBC Count||5.39|x10\\S\\6/uL|4.20 - 6.10|N|||F|||20130610172824|CTRLAB^CENTRAL LAB|DEL^JUNADEL LARGO KATO,RMT
OBX|4|ST|WBC^WBC Count||16.83|x10\\S\\3/uL|5.0 - 10.0|H|||F|||20130610172824|CTRLAB^CENTRAL LAB|DEL^JUNADEL LARGO KATO,RMT
OBX|5|ST|DIFF^Differential Count||\"\"||||||F
OBX|6|ST|NEUT^Neutrophil||69|%|55 - 75|N|||F|||20130610172824|CTRLAB^CENTRAL LAB|DEL^JUNADEL LARGO KATO,RMT
OBX|7|ST|LYMPH^Lymphocytes||18|%|20 - 35|L|||F|||20130610172824|CTRLAB^CENTRAL LAB|DEL^JUNADEL LARGO KATO,RMT
OBX|8|ST|MONO^Monocytes||10|%|2 - 10|N|||F|||20130610172824|CTRLAB^CENTRAL LAB|DEL^JUNADEL LARGO KATO,RMT
OBX|9|ST|EO^Eosinophil||2|%|1 - 8|N|||F|||20130610172824|CTRLAB^CENTRAL LAB|DEL^JUNADEL LARGO KATO,RMT
OBX|10|ST|BASO^Basophil||1.000|%|0 - 1|N|||F|||20130610172824|CTRLAB^CENTRAL LAB|DEL^JUNADEL LARGO KATO,RMT
OBX|11|ST|PLT^Platelet Count||280|x10\\S\\3/uL|150 - 400|N|||F|||20130610172824|CTRLAB^CENTRAL LAB|DEL^JUNADEL LARGO KATO,RMT
OBX|12|ST|MCH^MCH||36.4|pg|25.70 - 32.20|H|||F|||20130610172824|CTRLAB^CENTRAL LAB|DEL^JUNADEL LARGO KATO,RMT
OBX|13|ST|MCHC^MCHC||35.9|g/dL|32.30 - 36.50|N|||F|||20130610172824|CTRLAB^CENTRAL LAB|DEL^JUNADEL LARGO KATO,RMT
OBX|14|ST|MCV^MCV||101.4|fl|79.00 - 92.20|H|||F|||20130610172824|CTRLAB^CENTRAL LAB|DEL^JUNADEL LARGO KATO,RMT
OBX|15|ST|RETIC^Reticulocyte||!|%|4.0 - 8.0|N|||D|||20130610172824|CTRLAB^CENTRAL LAB|DEL^JUNADEL LARGO KATO,RMT'),
            'DateReleased' => date('Y-m-d'),
            'Pathologist' => $did,
                ));
        echo $lab;
        $hl7fxnObj->updatePostedToEmrStatus('HL7WNFS_00680018.hl7');
    } catch (Exception $exc) {
        
    }
} else {
    die('no pid or doctor id :(');
}
?>
