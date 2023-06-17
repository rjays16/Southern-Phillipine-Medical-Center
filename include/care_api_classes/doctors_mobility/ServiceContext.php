<?php

/**
 * 
 * ServiceContext.php
 * @author Marc Lua
 * edited justin
 * This class was made by Alvin Quinones
 * 
 * Description of ServiceContext
 *
 * @package
 */
require_once($root_path . 'include/care_api_classes/class_curl.php');
require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');

class EHRServiceContext extends Curl {

    protected static $baseUrl;
    protected $timestamp;
    protected $endpoint = '';
    protected $method = 'GET';
    protected $params = array();
    protected $data = array();
    protected $content = null;

    /**
     * Keeps track if the service context has already executed
     * @var boolean
     */
    protected $hasExecuted = false;

    /**
     * Constructor
     */
    public function __construct($options = array()) {
        $defaults = array(
            'endpoint' => '',
            'method' => 'GET',
            'params' => array(),
            'data' => array(),
            'content' => null
        );

        $options = array_merge($defaults, $options);
        $this->endpoint = $options['endpoint'];
        $this->method = strtoupper($options['method']);
        //$this->params = $this->sortParams($options['params']);
        //$this->data = $this->sortParams($options['data']);
        $this->params = $options['params'];
        $this->data = $options['data'];
        $this->content = $options['content'];
        $this->timestamp = time();
        parent::__construct();
    }

    /**
     * 
     */
    public function execute() {

        $url = $this->getBaseUrl() . $this->endpoint;
        
        if (!fsockopen(self::$baseUrl, 80, $errNo, $errStr, 1)) {
            header('HTTP/1.0 404');
            return false;
        }

       $username = Hospital_Admin::get('EHR_username');
       $password = Hospital_Admin::get('EHR_password');

       $this->setOptions(array(
           CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
           CURLOPT_USERPWD => $username . ':' . $password,
       ));

        switch ($this->method) {
            case 'PUT':
                $this->put($url, $this->data, $this->params);
                break;
            case 'POST':
                $this->post($url, $this->data);
                break;
            case 'GET':
            default:
                $this->get($url, $this->params);
                break;
        }
        return $this->processResult();
    }

    /**
     * 
     */
    protected function processResult() {
        $info = $this->getCurlInfo();
        if ($info['http_code'] !== 200 && $info['http_code'] !== 201) {
            switch ($info['http_code']) {
                default:
                    header('HTTP/1.0 ' . $info['http_code']);
                    break;
                // Might not reach this... (already handled by Curl::_exec)
                //throw new CHttpException($info['http_code'], curl_error($this->_ch));
            }
        }

        switch ($info['content_type']) {
            case 'application/json':
                return json_decode($this->output);
            case 'application/xml':
                return new SimpleXMLElement($this->output);
            case 'text/plain':
            default:
                return $this->output;
        }
    }

    /**
     * Normalizes the Base URL of the service endpoint
     * @throws CException When the base_url configuration is not found
     */
    protected function getBaseUrl() {
        $baseUrl = Hospital_Admin::get('EHR_address');
        
        if (isset($baseUrl)) {
            //allows ip address only as base url
            self::$baseUrl = preg_replace('/[^0-9.]/', '', $baseUrl);
            return self::$baseUrl;
        }
        return null;
    }
}