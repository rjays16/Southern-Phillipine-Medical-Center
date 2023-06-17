<?php
    require_once($root_path . 'include/care_api_classes/doctors_mobility/ServiceContext.php');
    require_once($root_path . 'include/care_api_classes/doctors_mobility/class_mobility.php');
    require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');

    class MiscellaneousService {
        protected static $baseDirectory;

        function __construct() {
           $this->baseDirectory = Hospital_Admin::get('EHR_directory');
        }
        
        public function createMiscRequest($data) {
            $options = array(
                'endpoint' => '/'.$this->baseDirectory.'/miscellaneous/create',
                'method' => 'POST',
                'data' => $data,
            );
            $service = new EHRServiceContext($options);
            return $service->execute();
        }

        public function removeMiscRequest($data) {
            $options = array(
                'endpoint' => '/'.$this->baseDirectory.'/miscellaneous/deleteRequest',
                'method' => 'POST',
                'data' => $data,
            );
            $service = new EHRServiceContext($options);
            return $service->execute();
        }

        //@ modules\dialysis\seg-misc-request-new.php
        public function saveMiscRequest($refno) {
            $mobility_obj = new DoctorsMobility();
            $miscInfo = $mobility_obj->getMiscInfo($refno);
           
            // if (!$miscInfo) {
            //     return false;
            // }
            
            $miscArr = $mobility_obj->getMiscDataArray($miscInfo, $refno);
            $res = $this->createMiscRequest($miscArr);

            //TODO: check if POST successful
            return true;
        }

        //@ modules\clinics\ajax\clinic-requests.server.php
        public function deleteMiscRequest($refno) {
            $details = array(
                'ref_no' => $refno,
            );
            
            $res = $this->removeMiscRequest($details);
            return TRUE;
        }
    }
?>