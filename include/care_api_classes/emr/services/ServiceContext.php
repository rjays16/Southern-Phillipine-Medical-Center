<?php

/**
 * 
 * ServiceContext.php
 * @author Marc Lua
 * This class was made by Alvin Quinones
 * 
 * Description of ServiceContext
 *
 * @package
 */
require_once($root_path . 'include/care_api_classes/class_curl.php');
require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');
require_once($root_path . 'include/care_api_classes/emr/class_emr.php');
require_once($root_path . 'include/care_api_classes/emr/class_staging.php');

class ServiceContext{

    protected static $baseUrl;
    protected $timestamp;
    protected $endpoint = '';
    protected $method = 'GET';
    protected $params = array();
    // protected $data = array();
    protected $content = null;
    protected $stagingType = null;
    protected $stagingId = null;

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
            // 'data' => array(),
            'content' => null
        );

        $options = array_merge($defaults, $options);
        $this->endpoint = $options['endpoint'];
        $this->method = strtoupper($options['method']);
        //$this->params = $this->sortParams($options['params']);
        //$this->data = $this->sortParams($options['data']);
        $this->params = $options['params'];
        // $this->data = $options['data'];
        $this->content = $options['content'];
        $this->timestamp = time();

        $this->api = strtoupper($options['api']);
        $this->pid = $options['pid'];
        $this->encounter_nr = $options['encounter_nr'];
        $this->personell_nr = $options['personell_nr'];
        $this->refno = $options['refno'];

        $this->stagingType = $options['stagingType'];
        $this->stagingId = $options['stagingId'];

        // parent::__construct();
    }


    /**
     * 
     */
    public function execute() {
        $emr = new EMR();

        switch ($this->method) {
            case 'PUT':
                $staging = new Staging(
                    array(
                        'parameter' => $this->stagingId,
                        'type' => $this->stagingType,
                        'status' => 'UPDATE'
                    )
                );
                $this->message = $staging->exec();
                break;
            case 'POST':
                $staging = new Staging(
                    array(
                        'parameter' => $this->stagingId,
                        'type' => $this->stagingType,
                        'status' => 'NEW'
                    )
                );
                $this->message = $staging->exec();
                break;
            case 'DELETE':
                $this->api = strtoupper("PUT");
                $staging = new Staging(
                    array(
                        'parameter' => $this->stagingId,
                        'type' => $this->stagingType,
                        'status' => 'DELETE'
                    )
                );
                $this->message = $staging->exec();
                break;
        }

        $dataarr = array
                (
                    'api'=>$this->api,
                    'url'=>$url,
                    'method'=>$this->method,
                    'message' =>$this->message,
                    'pid'=>$this->pid,
                    'encounter_nr'=>$this->encounter_nr,
                    'personell_nr'=>$this->personell_nr,
                    'refno'=>$this->refno,
                    'http_code' => $this->status,
                );

        $success = $emr->storeAuditLog($dataarr);
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
        $baseUrl = Hospital_Admin::get('EMR_address');
        
        if (isset($baseUrl)) {
            //allows ip address only as base url
            self::$baseUrl = preg_replace('/[^0-9.]/', '', $baseUrl);
            return self::$baseUrl;
        }
        return null;
    }
}