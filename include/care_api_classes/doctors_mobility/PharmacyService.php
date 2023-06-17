<?php
require_once($root_path . 'include/care_api_classes/doctors_mobility/ServiceContext.php');
require_once($root_path . 'include/care_api_classes/doctors_mobility/class_mobility.php');
require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');

class PharmacyService {
    protected static $baseDirectory;

    function __construct() {
       $this->baseDirectory = Hospital_Admin::get('EHR_directory');
    }
    
    public function createPharmaRequest($data) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/pharmacy/create',
            'method' => 'POST',
            'data' => $data,
        );
        $service = new EHRServiceContext($options);
        return $service->execute();
    }

    public function removePharmaRequest($data) {
            $options = array(
                'endpoint' => '/'.$this->baseDirectory.'/pharmacy/deleteRequest',
                'method' => 'POST',
                'data' => $data,
            );
            $service = new EHRServiceContext($options);
            return $service->execute();
        }

    //@ modules\pharmacy\seg-pharma-order-new.php
    //@ modules\pharmacy\seg-pharma-order-edit.php
    public function savePharmaRequest($refno) {
        $mobility_obj = new DoctorsMobility();
        $pharmaInfo = $mobility_obj->getPharmacyInfo($refno);
        
        if (!$pharmaInfo) {
            return false;
        }

        $pharmaArr = $mobility_obj->getPharmaDataArray($pharmaInfo);
        $res = $this->createPharmaRequest($pharmaArr);

        //TODO: check if POST successful
        return true;
    }

    //@ modules\clinics\ajax\clinic-requests.server.php
    //@ modules\pharmacy\ajax\order.server.php
    public function deletePharmaRequest($refno) {
        $details = array(
            'ref_no' => $refno,
        );
            
        $res = $this->removePharmaRequest($details);
        return TRUE;
    }
}

?>