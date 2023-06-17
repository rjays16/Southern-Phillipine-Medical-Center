<?php
require('./roots.php');

class UserToken{

    public $personnel_nr;

    const USERS_TOKEN_TBL = 'seg_user_token';
    const GLOBAL_CONFIG_TBL = 'care_config_global';
    const REP_PORTAL_TXT = "report_portal";
    const REP_REDIRECT_REPORT = "redirect_report";
    const PUBLIC_IP_VAR = "spmc_public_ip";
    const PUBLIC_IP_FORWARD_PORT = 'rep_public_ip_port_forward';

    private $token;
    /**
     * constructor
     * @param int $personnel_nr
     * @param string $token
     */
    public function __construct($personnel_nr=false,$token=false)
    {
        if ($personnel_nr)
            $this->personnel_nr = $personnel_nr;
        else
            $this->personnel_nr = $_SESSION['sess_login_personell_nr'];
        if($token)
            $this->token = $token;
    }

    /**
     * Get User token
     * return @string
     */
    public function getUserToken()
    {
        return $this->token;
    }

    /**
     * Get User token
     * return @string
     */
    public function setToken($token)
    {
        return $this->token = $token;
    }

    /**
     * verify user token; personnel_nr, token
     * return @boolean
     */
    public function verifyUserToken()
    {
        global $db;
        $this->sql="SELECT * FROM ".(self::USERS_TOKEN_TBL)." WHERE personnel_nr=".$db->qstr($this->personnel_nr)." AND token=".$db->qstr($this->token)." LIMIT 1";
        if($db->GetRow($this->sql))
            return true;
        else
            return false;
    }

    /**
     * Create Random Token
     * return @string
     */
    public function createRandToken()
    {
        global $db;
        $this->token = substr(md5(uniqid($_SESSION['sess_login_personell_nr'], true)), 0, 15);
        $result = $db->Replace(self::USERS_TOKEN_TBL, array("personnel_nr" => $db->qstr($this->personnel_nr),"token"=>$db->qstr($this->token)), array('personnel_nr'));

        if($result) return $this->token;
        else return false;
    }

    /**
     * Report User login
     * If login is false redirect to login.php
     * redirect to login.php if no login else return @boolean
     */
    function repUserLogin(){
        global $root_path;
        $connect_to_instance = $this->connectToInstance();
        if ($connect_to_instance){
            $report_portal = $this->getReportPortal();

            if ($_GET['repfile'] == "show_report")
                $_SESSION['loading_report_link'] = $report_portal."/modules/reports/".$_GET['repfile'].".php?".$_SERVER['QUERY_STRING'];
            else{
                $arrFile = explode("/", trim($_SERVER['REQUEST_URI'],'/'), 2) + array( 1 => NULL);

                $_SESSION['loading_report_link'] = $report_portal."/".$arrFile['1'];
            }

            if (!$this->personnel_nr){

                $this->personnel_nr = $_GET['personnel_nr'];
                $this->token = $_GET['ptoken'];

                if($this->token && $this->personnel_nr){
                    $_url_append = "personnel_nr=".$this->personnel_nr."&ptoken=".$this->token;
                    header("location: ".$root_path."main/login.php?".$_url_append);
                }
            }
            return false;
        }

    }

    function connectToInstance(){
        global $db;
        return $db->GetOne("SELECT value FROM ".(self::GLOBAL_CONFIG_TBL)." WHERE type=".$db->qstr(self::REP_REDIRECT_REPORT));
    }

    function getPublicIp(){
        global $db;
        return $db->GetOne("SELECT value FROM ".(self::GLOBAL_CONFIG_TBL)." WHERE type=".$db->qstr(self::PUBLIC_IP_VAR));
    }


    function getPortForward()
    {
        global $db;
        return $db->GetOne("SELECT value FROM ".(self::GLOBAL_CONFIG_TBL)." WHERE type=".$db->qstr(self::PUBLIC_IP_FORWARD_PORT));
    }


    function getReportPortalUrl(){
        global $db;
        return $db->GetOne("SELECT value FROM ".(self::GLOBAL_CONFIG_TBL)." WHERE type=".$db->qstr(self::REP_PORTAL_TXT));
    }

    function getReportPortal(){
        $server_host = $_SERVER['HTTP_HOST'];

        $report_portal = "";
        $port_forward = $this->getPortForward();
        $parse_url = parse_url($port_forward);
        $tmpUrl = $parse_url["port"] ? $parse_url["host"].":".$parse_url["port"] : $parse_url["host"];

        if ($server_host == $tmpUrl)
            $report_portal = $port_forward;

        if (!$report_portal){
            $report_portal = $this->getReportPortalUrl();
        }

        return $report_portal;
    }

}