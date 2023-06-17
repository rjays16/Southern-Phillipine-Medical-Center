<?php
require("./roots.php");
require_once($root_path . 'include/care_api_classes/class_core.php');
require_once($root_path . 'include/care_api_classes/class_globalconfig.php');
require_once($root_path . 'include/care_api_classes/class_personell.php');
require_once($root_path . '/frontend/bootstrap.php');


class Rest_Curl
{


	function sendAPI($url, $data, $headers = null, $method = 'POST')
	{
		date_default_timezone_set('Asia/Manila');
		$tstamp = strtotime("now");
		$username = 'spmc';
		$apikey = "BKGAJ90FKK98362P2";
		$hmac = hash_hmac('sha1', $username . $tstamp . $apikey, $apikey);

		$newData = null;
		#$this->http_build_query_for_curl($data, $newData);
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_HEADER, 0);
		curl_setopt($handle, CURLOPT_HTTPHEADER, array(
			'X-Authorization: Basic ' . base64_encode($username . ":" . $hmac),
			'Timestamp: ' . $tstamp,
			'Content-Type: application/json'
		));

		curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, ($data));
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

		ob_start();
		$response = curl_exec($handle);
		$data = json_decode($response);
		// echo $var->access_token;
		// die;
		// $content = ob_get_contents();
		// ob_end_clean();
		// $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		// echo "<pre>$response</pre>";
		// var_dump('asdasdasdas');
	
		return $data;
		// if (!$response) {
		// 	die("No response");
		// }
		// $content = ob_get_contents();
		// ob_end_clean();
		// $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

		// return array('code' => $code, 'content' => $data);
	}

	function http_build_query_for_curl($arrays, &$new = array(), $prefix = null)
	{

		if (is_object($arrays)) {
			$arrays = get_object_vars($arrays);
		}

		foreach ($arrays as $key => $value) {
			$k = isset($prefix) ? $prefix . '[' . $key . ']' : $key;
			if (is_array($value) or is_object($value)) {
				$this->http_build_query_for_curl($value, $new, $k);
			} else {
				$new[$k] = $value;
			}
		}
	}

	function save_users($username, $userid, $pass, $personell_nr, $createID, $job, $p_areas)
	{
		$url = "http://10.1.80.34:8000/api/createUser";

		$data = array(
			'name' => $username,
			'personnel_nr' => $personell_nr,
			'username' => $userid,
			'password' => $pass,
			'job_title' => $job,
			'createID' => $createID,
			'area' => $p_areas,
		);

		$data_string = json_encode($data);
		$result = $this->sendAPI($url, $data_string);
		// var_dump($result);
		// die;
		return $result;
	}

	function change_password($username, $password, $personell_nr)
	{
		$url = "http://10.1.80.34:8000/api/changePassword";

		$data = array(
			'personnel_nr' => $personell_nr,
			'username' => $username,
			'password' => $password,
		);

		$data_string = json_encode($data);
		$result = $this->sendAPI($url, $data_string);
		// var_dump($result);
		// die;
		return $result;
	}

	function updateNameInfo($name_last, $name_first, $personell_nr)
	{
		$name = $name_first . ' ' . $name_last;
		$url = "http://10.1.80.34:8000/api/updateNameInfo";

		$data = array(
			'personnel_nr' => $personell_nr,
			'full_name' => $name,
		);

		$data_string = json_encode($data);
		$result = $this->sendAPI($url, $data_string);
		// var_dump($result);
		// die;
		return $result;
	}

	function storeLoginInfo($username, $password)
	{
		$url = "http://10.1.80.34:8081/#/LoginHis/sijuneroyugbenderugmatsugarielugjayveeugcarlaraangnakabaloaningaurl/$username/$password";

		$data = array(
			'username' => $username,
			'password' => $password,
		);

		$data_string = json_encode($data);
		$result = $this->sendAPI($url, $data_string);
		// var_dump($result);
		// die;
		return $result;
	}
	function storeLoginDietary($username, $password)
	{
		$public_ip = Config::get('spmc_public_ip');
		$dietary_public_ip = Config::get('dietary_public_ip');
		$public_ip_spmc = explode(',', $public_ip->value);
		 if(in_array($_SERVER['HTTP_HOST'], $public_ip_spmc)){
                      $dietary_url = DIETARY_PUBLIC_API;
                    }else{
                          $dietary_url = DIETARY_API;
                    }
                    
		$url = $dietary_url."/api/oauth/token";
		$data = array(
			'client_id' => 2,
			'client_secret' => 'pvW7wa5zBxSMIFtX8Yzj0xEOLkHJiUGTjsRKw2nJ',
			'grant_type'=> "password",
			'username' => $username,
			'password' => $password,
		);

		$data_string = json_encode($data);
		$result = $this->sendAPI($url, $data_string);
		// var_dump($result);
		// die;
		return $result;
	}
	
}
