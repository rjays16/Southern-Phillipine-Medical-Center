<?php
/*
Created: Deboner Dulos  <deboner.dulos.wise@gmail.com>
3-3-19 11:19 pm
*/

class TelemedCurl{
    public static $_ehrinstance;
	private  $_localhost;
    private  $_token;
    private $_response;

	function __construct()
	{
//	    $this->_initEhrCurl();
        $config = include __DIR__ . '/telemed-config.php';
        static::$_ehrinstance = $this;
        static::$_ehrinstance->_localhost = $config['HOST'];
        static::$_ehrinstance->_token = $config['EHR_TOKEN'];
	}


    public function __call($method, $args)
    {
        if (isset($this->$method)) {
            $func = $this->$method;
            return call_user_func_array($func, $args);
        }
    }


	public function rest_post($path, $post, $get = array())
	{
		$url = "http://".$this->_localhost.$path;

		// convert data properly since there's a problem with curl's json encoding
		$newData = null;
		$this->_http_build_query_for_curl($post, $newData);
        return $this->_initCurl($url, $get, $newData);
	}

    public function rest_get($path, $get = array())
    {
        $url = "http://".$this->_localhost.$path;
        return $this->_initCurl($url, $get);
    }


	private function  _initCurl($url, $get, $post = array()){
        // convert data properly since there's a problem with curl's json encoding
        $url = $url .'?'. $this->_http_get_params($get);
        $handle = curl_init($url);
        $ckfile = tempnam ("/tmp", 'cookiename');
        curl_setopt ($handle, CURLOPT_COOKIEJAR, $ckfile);
        curl_setopt ($handle, CURLOPT_COOKIEFILE, $ckfile);
//        curl_setopt($handle, CURLOPT_HTTPHEADER, $get);
        curl_setopt($handle, CURLOPT_HTTPHEADER, array("Cookie: TOKEN=".$this->_token));
        curl_setopt($handle, CURLOPT_HEADER, 0);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $post);
        curl_setopt($handle, CURLOPT_FAILONERROR, true);


        ob_start();
        $res = curl_exec($handle);
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $content = ob_get_contents();

        curl_close($handle);
        ob_end_clean();

        if($this->_isValidResponse($content)){
            $content = $this->_response;
        }
        else{
            $code = 500;
//            $content = $this->_response;
            if(empty($content))
                $content = 'Something is wrong with EHR code execution.';
        }

        // close cURL resource, and free up system resources
        return array('code' => $code, 'content' => $content);
    }


    private function _isValidResponse($respoonse){
        $this->_response = json_decode($respoonse);
        if (is_object($this->_response))
            return true;
        return false;
    }





	private function _http_build_query_for_curl( $arrays, &$new = array(), $prefix = null ) {

		if ( is_object( $arrays ) ) {
			$arrays = get_object_vars( $arrays );
		}

		foreach ( $arrays AS $key => $value ) {
			$k = isset( $prefix ) ? $prefix . '[' . $key . ']' : $key;
			if ( is_array( $value ) OR is_object( $value )  ) {
				$this->_http_build_query_for_curl( $value, $new, $k );
			} else {
				$new[$k] = $value;
			}
		}
	}


    private function _http_get_params( $get = array() ) {
        return http_build_query($get);
    }

}