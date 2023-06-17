<?php
/**
 * Created by PhpStorm.
 * User: Deboner Dulos <deboner.dulos.wise@gmail.com>
 * Date: 3/7/2019
 * Time: 12:58 AM
 */

include __DIR__ . '/../controller/Database.php';
include __DIR__ . '/../TelemedEventmanager.php';
class Route
{
    private static $eventObj ;
    public $db ;
    private $_routeFound = false;
    private $_executed_fn = false;
    private $_routes = array();
    private $_TOKEN_VAR = 'TOKEN';
    private $_INPUTS = array();
    private $_COOKIE = array();
    private $_HEADER = array();
    private $_uri;
    private $_CONFIG;
    private $_METHOD;
    private $_IS_EHR = false;
    private $_IS_USER_VALID;


    function __construct()
    {
        $this->_CONFIG = include __DIR__ . '/telemed-config.php';
        $this->_extractURI();
        $this->_extractHeader();
        $this->_IS_TELEMED = $this->isTelemedReq();
        $this->_initInputs();
        $this->initDB();
    }

    private function initDB(){
        try {
            $this->db = Database::getInstance();
        }catch(PDOException $e){
            echo $this->response($e->getMessage(), $e->getCode(),array(
                'status' => false,
                'code' => $e->getCode(),
            ),$e);
            die();
        }
    }

    private function _extractURI(){
        $path = explode($this->_CONFIG['TELEMED_PATH'],$_SERVER['REQUEST_URI']); // get URI
        $path = explode('?',$path[1]); // remove get params on URL
        $this->_uri = $path[0];

        $this->_METHOD = $_SERVER['REQUEST_METHOD'];
    }

    public function isTelemedReq(){
        return  $this->getHeader('REQ') == 'TELEMED';
    }


    private function _initInputs(){
        if(file_get_contents('php://input'))
            $json = json_decode(file_get_contents('php://input'), true);

        foreach (array_merge($_GET,$_POST) as $key => $input){
            $this->_INPUTS[$key] = $input;
        }

        foreach ($json['data'] as $key => $input){
            $this->_INPUTS[$key] = $input;
            $_POST[$key] = $input;
        }
    }


    public function input($var){
        return isset($this->_INPUTS[$var]) ? $this->_INPUTS[$var] : null;
    }

    public function postD($var, $default = null){
        return isset($_POST[$var]) ? $_POST[$var] : $default;
    }


    public function getD($var, $default = null){
        return isset($_GET[$var]) ? $_GET[$var] : $default;
    }

    public function inputs(){
        return $this->_INPUTS;
    }

    public function foundRoute($route = '', $verb = "GET"){
        if(!$this->_routeFound)
            $this->_routeFound = strtolower(trim($route)) == ''.strtolower(trim($this->_uri)) && $this->_METHOD == $verb;

        return $this->_routeFound;
    }

    public  function onExit(){
        if(!$this->foundRoute()){
            try {
                throw new Exception('URL not found.',404);
            } catch (Exception $e) {
                echo $this->response($e->getMessage(), $e->getCode(),array(
                    'status' => false,
                    'code' => $e->getCode(),
                ),$e);
            }
        }
    }

    public function getEvent(){
        if(self::$eventObj == null){
            self::$eventObj = new TelemedEventmanager();
        }
        return self::$eventObj;
    }

    public  function execute($fn_name){
        if(!$this->_executed_fn){
            $this->_executed_fn = true;
            $Event = $this->getEvent();
            $response = $Event->{$fn_name}($this);

            return $response ? $response : json_encode(true);
        }
    }

    public function response($message, $code = 200,$data = array(), Exception $e = null){
        header('Content-Type: application/json');
        if(in_array($code, array(200,400,404,401,500)))
            header('X-PHP-Response-Code: '.$code, true, $code);
        else
            header('X-PHP-Response-Code: 500', true, 500);
        $data['message'] = $message;

        if($e)
            $data['trace'] = $e->getTrace();
        
        return json_encode($data);
    }

    private function _extractHeader(){
        if (!function_exists('getallheaders'))
        {
            function getallheaders()
            {
                $headers = array();
                foreach ($_SERVER as $name => $value)
                {
                    if (substr($name, 0, 5) == 'HTTP_')
                    {
                        $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                    }
                }
                return $headers;
            }
        }
        
        $headerCookies = getallheaders();
        foreach(explode(';', $headerCookies['Cookie']) as $itm) {
            list($key, $val) = explode('=', $itm,2  );
            $this->_COOKIE[$key] = $val;
        }

        foreach (apache_request_headers() as $key => $value) {

            $this->_HEADER[$key] = $value;
            if($key == 'Authorization'){
                $token = explode('Bearer ',$value);
                $this->_HEADER[$this->_TOKEN_VAR] = $token[1];
            }
        }
    }




    public function isAuthorized(){
        try {
            $authorized = $this->_validateRequest();
            if($authorized != true){
                throw new Exception('Unauthorized',401);
            }
            return true;;
        } catch (Exception $e) {
            echo $this->response($e->getMessage(), $e->getCode(),array(
                'status' => false,
                'code' => $e->getCode(),
            ),$e);
            return false;
        }
    }

    private function _validateRequest(){
        $authorized = $this->_HEADER[$this->_TOKEN_VAR] == $this->_CONFIG['HIS_PRIVATE_KEY'];
        if(!$authorized)
            return false;
        return true;
    }

    // private function _validateUser(){
    //     $Event = $this->getEvent();
    //     $response = $Event->{'validateRequstUser'}($this->getHeader('PERSONEL'));
    //     return $response === true ? $response : false;
    // }

    public function getRequestPersonelID(){
        return $this->getHeader('PERSONEL');
    }

    public function getRequestPersonelUname(){
        return $this->getHeader('UNAME');
    }

    public function getAuthorization(){

        // return isset($this->_COOKIE[$var])? $this->_COOKIE[$var] : false;
    }

    public function getHeader($var){
        return isset($this->_COOKIE[$var])? $this->_COOKIE[$var] : false;
    }

    public function getHeaders(){
        return $this->_COOKIE;
    }





}