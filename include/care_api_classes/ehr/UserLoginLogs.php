<?php 


#require_once($root_path . 'include/care_api_classes/ehr/ServiceContext.php');
require_once($root_path . 'include/care_api_classes/doctors_mobility/class_mobility.php');
require_once($root_path . 'include/care_api_classes/doctors_mobility/ServiceContext.php');
require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');

class UserLoginLogs {

	
	
	public function __construct() {
        $this->baseDirectory = Hospital_Admin::get('EHR_directory');
    }


    public function UserLoginLogs($from, $to) {
    	$options = array(
            'endpoint' => '/'.$this->baseDirectory.'/userLoginLogs/userLoginLogs?from='.$from.'&to='.$to,    // uncomment in production. rnel
            // 'endpoint' => '/ehrservice4dev/userLoginLogs/userLoginLogs?from='.$from.'&to='.$to,  // for local development, comment in  production and test server. rnel
            
            'method' => 'GET',
        );

        // try {
            
        // } catch (Exception $e) {
            
        // }

        #$service = new ServiceContext($options);
        $service = new EHRServiceContext($options);
        // echo "<pre>";
        // var_dump($service->execute()); die;
        return $service->execute();
    }
}