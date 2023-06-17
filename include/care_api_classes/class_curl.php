<?php

/**
 * Curl wrapper for Yii
 * v - 1.2
 * @author hackerone
 */
class Curl {

    protected $_ch;
    // config from config.php
    public $options;
    public $status;
    // default config
    protected $_config = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_AUTOREFERER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
//        CURLOPT_HEADER => true,
//        CURLOPT_NOBODY => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:5.0) Gecko/20110619 Firefox/5.0',
    );
    protected $curlInfo = array();
    protected $output;

    public function __construct() {
        $this->init();
        $this->output = '';
    }

    private function _exec($url) {
        $this->setOption(CURLOPT_URL, $url);
        $c = curl_exec($this->_ch);
        $this->curlInfo = array_merge(curl_getinfo($this->_ch));
        $this->output = $c;
        $this->status = curl_getinfo($this->_ch, CURLINFO_HTTP_CODE); // find HTTP status
        curl_close($this->_ch);
    }

    public function get($url, $params = array()) {
        $this->setOption(CURLOPT_HTTPGET, true);
        $this->_exec($this->buildUrl($url, $params));
    }

    /**
     * Encodes Post $data to JSON string.
     * @param type $url
     * @param type $data
     */
    public function post($url, $data = array()) {
        $data = $this->array_map_recursive("utf8_encode", $data);
        $jsonData = json_encode($data);
        $this->setOption(CURLOPT_POSTFIELDS, $jsonData);
        $this->setHeaders(array(
            'Content-Type' => 'application/json',
            'Content-Length' => strlen($jsonData))
        );
        $this->_exec($url);
    }

    public function put($url, $data, $params = array()) {
        $data = $this->array_map_recursive("utf8_encode", $data);
        $jsonData = json_encode($data);
        $this->setHeaders(array(
            'Content-Type' => 'application/json',
            'Content-Length' => strlen($jsonData))
        );
        // write to memory/temp
        $f = fopen('php://temp', 'rw+');
        fwrite($f, $jsonData);
        rewind($f);
        $this->setOption(CURLOPT_PUT, true);
        $this->setOption(CURLOPT_INFILE, $f);
        $this->setOption(CURLOPT_INFILESIZE, strlen($jsonData));
        return $this->_exec($this->buildUrl($url, $params));
    }

    public function buildUrl($url, $data = array()) {

        $parsed = parse_url($url);
        isset($parsed['query']) ? parse_str($parsed['query'], $parsed['query']) : $parsed['query'] = array();
        $params = isset($parsed['query']) ? array_merge($parsed['query'], $data) : $data;
        $parsed['query'] = ($params) ? '?' . http_build_query($params) : '';
        if (!isset($parsed['path']))
            $parsed['path'] = '/';

        $port = '';
        if (isset($parsed['port'])) {
            $port = ':' . $parsed['port'];
        }
        if (!isset($parsed['scheme'])) {
            $parsed['scheme'] = 'http';
        }

        return $parsed['scheme'] . '://' . $parsed['host'] . $port . $parsed['path'] . $parsed['query'];
    }

    public function setOptions($options = array()) {
        curl_setopt_array($this->_ch, $options);
        return $this;
    }

    public function setOption($option, $value) {
        curl_setopt($this->_ch, $option, $value);
        return $this;
    }

    /**
     * 
     * @param Array $header
     * Set Header Parameters
     */
    public function setHeaders($header) {
        if (!is_array($header))
            throw new CException('Array Required');
        $out = array();
        foreach ($header as $k => $v) {
            $out[] = $k . ': ' . $v;
        }
        $header = $out;
        $this->setOption(CURLOPT_HTTPHEADER, $header);
    }

    // initialize curl
    public function init() {
        try {
            $this->_ch = curl_init();
            $options = is_array($this->options) ? ($this->options + $this->_config) : $this->_config;
            $this->setOptions($options);
        } catch (Exception $e) {
            throw new Exception('Curl not installed');
        }
    }

    /**
     * return information of the http request.
     * @return type
     */
    protected function getCurlInfo() {
        return isset($this->curlInfo) ? $this->curlInfo : false;
    }

    //output of the service
    public function getResult() {
        return $this->output;
    }

    function array_map_recursive($callback, $array) {
        foreach ($array as $key => $value) {
            if (is_array($array[$key])) {
                $array[$key] = $this->array_map_recursive($callback, $array[$key]);
            }
            else {
                $array[$key] = call_user_func($callback, $array[$key]);
            }
        }
        return $array;
    }
}