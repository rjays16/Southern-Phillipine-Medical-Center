<?php
/**
 * Created by PhpStorm.
 * User: Deboner Dulos <deboner.dulos.wise@gmail.com>
 * Date: 3/7/2019
 * Time: 1:49 PM
 */

require_once __DIR__.'/../pest/Pest.php';

class NotificationRoutes
{

    private $_URL = '';
    private $_TOKEN = '';
    private $_pest = null;
    private $_header = null;
    private $_cookies = array();
    private $_error;
    private $_URS ;
    private $_PERSONEL ;


    public function __construct($personelID,$personelUname) {
        $conf = require __DIR__.'/../notification-config.php';
        $this->_URL = $conf['HOST'];
        $this->_URS = $conf['URS'];
        $this->_TOKEN = $conf['Notification_TOKEN'];
        $this->_addCookie('PERSONEL', $personelID);
        $this->_addCookie('UNAME', $personelUname);
        $this->setRequestOwner($personelID);
        $this->_pest = $this->getPest();
    }

    public function setRequestOwner($personelID){
        $this->_PERSONEL = $personelID;
    }

    private function getPest(){
        $this->_setheader();
        return new Pest($this->_URL);
    }

    public function getResponseData(){
        return $this->_pest->last_response;
    }

    public function get($url, $get = array()){
        try{
            return json_decode($this->_pest->get($url, $get, $this->_header), true);
        }
        catch (Pest_ClientError $e){
            $resp = $this->getResponseData();
            return json_decode($resp['body'],true);
        }catch (\Exception $e){
            return array(
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'full_resp' => $this->getResponseData(),
                'trace' => $e->getFile() . " ({$e->getLine()})"
            );
        }
    }

    

    public function post($url, $post = array()){
        try{
            return json_decode($this->_pest->post($url, $post, $this->_header), true);
        }
        catch (Pest_ClientError $e){
            $resp = $this->getResponseData();
            return json_decode($resp['body'],true);
        }catch (\Exception $e){
            return array(
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'trace' => $e->getFile() . " ({$e->getLine()})"
            );
        }

    }


    /**
     * [
        * [
            * @param string $title # Notification title
            * @param string $message # Notification message
            * @param string $receiver_username # Array of user username<string> whom to receive
            * @param string $sender_username # Sender username
            * @param array $buttons # Notification buttons details
            * @param array $param_data # Notification payload data details
            * @param string $url # Notification click action to redirect FOR BROWSER
            * @param string $url # Notification click action to redirect
            * @param string $consult_id # Receiver consultation ID for Telemed
            * @param string $eventClass # Socket event to trigger
            * @param array $otherPayload # payload other data such as Socket Event class parameters
        * ]
     * ]
     */
    public function sendSpmcNotificationList($notifications = array()) {

        $result = $this->post(
            "/notification/spmc/user",
            array(
                'receiver' => $notifications
            )
        );
        $tmpdata = json_decode($result, true);

        return empty($tmpdata) ? false : $tmpdata;
    }


    /**
     * [
        * [
            * @param string $eventClass # Socket event to trigger
            * @param array $otherPayload # payload other data such as Socket Event class parameters
        * ]
     * ]
     */
    public function sendSpmcSocketEvent(array $events) {

        $result = $this->post(
            "/event/spmc/user",
            array(
                'receiver'   => $events,
            )
        );
        $tmpdata = json_decode($result, true);

        return empty($tmpdata) ? false : $tmpdata;
    }
    


    public function registerUser($username, $player_id = null)
    {
        return $this->post("/telemed/register/his",array(
                'user_id' => $username,
                'player_id' => $player_id,
            )
        );
        
    }


    private function _setheader(){
        $cookies = "Cookie: ";
        foreach ($this->_cookies as $key => $value){
            $cookies .= "{$key}={$value};";
        }
        $this->_header = array($cookies);
        $this->_header['Authorization'] = "Bearer ".$this->_TOKEN;
    }

    private function _addCookie($name, $value){
        $this->_cookies[$name] = $value;
    }





}