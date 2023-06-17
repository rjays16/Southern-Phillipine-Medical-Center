<?php

require("./roots.php");
require_once($root_path . 'include/care_api_classes/class_core.php');
require_once($root_path . 'include/care_api_classes/class_globalconfig.php');
require_once($root_path . 'include/care_api_classes/class_personell.php');

class Rest_ehr{

	function getResponse($url, $data, $headers = null, $method = 'POST'){
		date_default_timezone_set('Asia/Manila');	
		$newData = null;
		$this->http_build_query_for_curl($data, $newData);
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_HEADER, 0);
		curl_setopt($handle, CURLOPT_HTTPHEADER, array('Accept: application/json'));
		curl_setopt($handle, CURLOPT_URL, $url);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_USERPWD, "go:segworks");
		curl_setopt($handle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

		ob_start();
		$response= curl_exec($handle);
		$content = ob_get_contents();
		ob_end_clean();
		$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		return $response;
	}

	function http_build_query_for_curl( $arrays, &$new = array(), $prefix = null ) {

		if ( is_object( $arrays ) ) {
			$arrays = get_object_vars( $arrays );
		}

		foreach ( $arrays AS $key => $value ) {
			$k = isset( $prefix ) ? $prefix . '[' . $key . ']' : $key;
			if ( is_array( $value ) OR is_object( $value )  ) {
				$this->http_build_query_for_curl( $value, $new, $k );
			} else {
				$new[$k] = $value;
			}
		}
	}

	function addPatient($patientData){ 
		$urlLocal = "http://10.1.80.36/ehrservice/patient/AddPatient/";
		$data_string = json_encode($patientData);   

		$result = $this->getResponse($urlLocal, $data_string);
		return $result;
	}

	function addDoctor($doctorData){ 
		$urlLocal = "http://10.1.80.36/ehrservice/doctor/AddDoctor/";
		$data_string = json_encode($doctorData);   

		$result = $this->getResponse($urlLocal, $data_string);
		return $result;
	}


	function assignDoctorDepartment($department){ 
		$urlLocal = "http://10.1.80.36/ehrservice/doctor/AssignDepartment/";
		$data_string = json_encode($department);   

		$result = $this->getResponse($urlLocal, $data_string);
		return $result;
	}

	function assignPatientDepartment($patientInfo){ 
		$urlLocal = "http://10.1.80.36/ehrservice/patient/AssignPatient/";
		$data_string = json_encode($patientInfo);   

		$result = $this->getResponse($urlLocal, $data_string);
		return $result;
	}

	function saveSOAP($data, $type){ 
		$urlLocal = "http://10.1.80.36/ehrservice/doctor/SaveDrNote/";

		$soapData = array(
			"data"		=>	$data,
			"type"		=>	$type[0]['name']
		);

		$data_string = json_encode($soapData);   
		$result = $this->getResponse($urlLocal, $data_string);
		return $result;
	}

	function saveAssessmentDiagnosis($data){ 
		$urlLocal = "http://10.1.80.36/ehrservice/doctor/SaveFinalDiagnosis/";

		$data_string = json_encode($data);   
		$result = $this->getResponse($urlLocal, $data_string);
		return $result;
	}

	function createLabRequest($requests){ 
		$urlLocal = "http://10.1.80.36/ehrservice/laboratory/LaboratoryRequest/";

		$data_string = json_encode($requests);   
		$result = $this->getResponse($urlLocal, $data_string);
		return $result;
	}

	function createRadRequest($requests){ 
		$urlLocal = "http://10.1.80.36/ehrservice/radiology/RadRequest/";

		$data_string = json_encode($requests);   
		$result = $this->getResponse($urlLocal, $data_string);
		return $result;
	}

	function createPharmaRequest($requests){ 
		$urlLocal = "http://10.1.80.36/ehrservice/radiology/RadRequest/";

		$data_string = json_encode($requests);   
		$result = $this->getResponse($urlLocal, $data_string);
		return $result;
	}

	function createBloodBankRequest($requests){ 
		$urlLocal = "http://10.1.80.36/ehrservice/laboratory/BloodBankRequest/";

		$data_string = json_encode($requests);   
		$result = $this->getResponse($urlLocal, $data_string);
		return $result;
	}


}






?>