<?php

namespace SegHis\components\notification;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use SegHis\components\pest\Pest;
use \Yii;

class NotificationActiveResource
{
    /**
     * @var NotificationActiveResource
     */
    private static $inst = null;
    private $url = '';
    private $pest = null;
    private $header = null;
    private $error;
    private $_cookies = array();
    private $_token;


    /**
     * Call this method to get singleton
     *
     * @return NotificationActiveResource
     */
    public static function instance()
    {
        if (self::$inst == null) {
            self::$inst = new NotificationActiveResource(
                self::_getInstanceParam('NOTIFICATION_URL'), self::_getInstanceParam('NOTIFICATION_TOKEN')
            );
        }

        return self::$inst;
    }

    public function getCookies()
    {
        return $this->_cookies;
    }

    private static function _getInstanceParam($var)
    {
        if (isset(Yii::app()->params)) {
            return Yii::app()->params[$var];
        } else {
            $params = include __DIR__ . '/../../../protected/config/params.php';
            return $params[$var];
        }
    }

    private function __construct($url, $token)
    {
        $this->pest = new Pest($url);
        $this->_token = $token;        
    }

    private function _getPersonelID()
    {
        if (isset(Yii::app()->user->personell_nr)) {
            return Yii::app()->user->personell_nr;
        } else {
            return "";
        }
    }

    private function _getPersonelUname()
    {
        if (isset(Yii::app()->user->username)) {
            return Yii::app()->user->username;
        } else {
            return "";
        }
    }

    public function setPersonellId($id)
    {
        $this->_addCookie('PERSONEL', $id);
    }

    public function setPersonellUname($uname)
    {
        $this->_addCookie('UNAME', $uname);
    }


    private function _addCookie($name, $value)
    {
        $this->_cookies[$name] = $value;
    }  

    private function _getheader()
    {
        $cookies = "Cookie: ";
        foreach ($this->_cookies as $key => $value) {
            $cookies .= "{$key}={$value};";
        }

        return array(
            array($cookies),
            "Authorization" => "Bearer ".$this->_token
        );
    }

    private function _get($url, $get)
    {
        try {
            return $this->pest->get($url, $get, $this->_getheader());
        } catch (\Pest_ClientError $e) {
            $responseDta = $this->getResponseData();
            return $responseDta['body'];
        } catch (\Exception $e) {
            return json_encode(
                array(
                    'code'      => $e->getCode(),
                    'message'   => $e->getMessage(),
                    'full_resp' => $this->getResponseData(),
                    'trace'     => $e->getFile() . " ({$e->getLine()})"
                )
            );
        }
    }

    private function _post($url, $post)
    {
        try {
            return $this->pest->post($url, $post, $this->_getheader());
        } catch (\Pest_ClientError $e) {
            $responseDta = $this->getResponseData();
            return $responseDta['body'];
        } catch (\Exception $e) {
            return json_encode(
                array(
                    'code'      => $e->getCode(),
                    'message'   => $e->getMessage(),
                    'full_resp' => $this->getResponseData(),
                    'trace'     => $e->getFile() . " ({$e->getLine()})"
                )
            );
        }
    }

    public function getResponseData()
    {
        return $this->pest->last_response;
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

        $result = $this->_post(
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

        $result = $this->_post(
            "/event/spmc/user",
            array(
                'receiver' => $events
            )
        );
        $tmpdata = json_decode($result, true);

        return empty($tmpdata) ? false : $tmpdata;
    }

    public function registerUser($user_id, $player_id=null)
    {
        $result = $this->_post(
            "/telemed/register/ehr",
            array(
                'user_id'   => $user_id,
                'player_id' => $player_id,
            )
        );

        $tmpdata = json_decode($result, true);

        return empty($tmpdata) ? false : $tmpdata;
    }

    /***
     * 
     */
    public function isOngoingTriage($consult_id)
    {
        $result = $this->_post(
            "/telemed/ongoing/triage",
            array(
                'consult_id' => $consult_id,
            )
        );

        $tmpdata = json_decode($result, true);

        return empty($tmpdata) ? false : $tmpdata;
    }


    /***
     * 
     */
    public function isOngoingMedRec($consult_id)
    {
        $result = $this->_post(
            "/telemed/ongoing/medrec",
            array(
                'consult_id' => $consult_id,
            )
        );

        $tmpdata = json_decode($result, true);

        return empty($tmpdata) ? false : $tmpdata;
    }    
}
