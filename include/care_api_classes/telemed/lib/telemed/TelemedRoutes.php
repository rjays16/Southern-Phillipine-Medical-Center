<?php
/**
 * Created by PhpStorm.
 * User: Deboner Dulos <deboner.dulos.wise@gmail.com>
 * Date: 3/7/2019
 * Time: 1:49 PM
 */

class TelemedRoutes
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
        $conf = require __DIR__.'/../telemed-config.php';
        $this->_URL = $conf['HOST'];
        $this->_URS = $conf['URS'];
        $this->_TOKEN = $conf['TELEMED_TOKEN'];
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
		if (!class_exists('Pest')) {
			require_once __DIR__.'/../pest/Pest.php';
		}        
        return new Pest($this->_URL);
    }

    public function getResponseData(){
        return $this->_pest->last_response;
    }

    public function get($url, $get = array()){
        try{
            return $this->_pest->get($url, $get, $this->_header);
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
            return $this->_pest->post($url, $post, $this->_header);
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



    public function sendConsRegNotif($title, $message, $receiver_player_ids, $sender_player_id = '', $param = array(), $consult_id = '', $buttons = array()){
        return  $this->post('/notification/telemed',array(
            'consult_id' => $consult_id,
            'sender' => $sender_player_id,
            'title' => $title,
            'message' => $message,
            'users' => $receiver_player_ids,
            'param_data' => $param,
            'buttons' => $buttons
        ));
    }


    public function sendConsNotifSPMCEmployee($title, $message, $emp_user_username, $sender_player_id, $data = array()){
        return  $this->post('/notification/telemed',array(
            'sender' => $sender_player_id,
            'title' => $title,
            'message' => $message,
            'receiver_username' => $emp_user_username, 
            'param_data' => $data,
        ));
    }
    


    public function sendConsNotifPatientAssignedDoctor($doctorName,$doctor_id,$encounter_no, $receiver_player_ids, $sender_player_id, $user_username, $consult_id, $token, $param = array()){
        
        return  $this->post('/notification/telemed/patient/assigned/doctor',array(
            'consult_id' => $consult_id,
            'sender' => $sender_player_id,
            'title' => 'Consultation Status',
            'message' => "Dr. $doctorName was assigned to you. Please wait for your schedule. We will send you your room address later.",
            'users' => array($receiver_player_ids),
            'param_data' => array_merge(
                array(
                    'token' => $token,
                    'doctor_name' => $doctorName,
                    'doctor_id' => $doctor_id,
                    'user_username' => $user_username,
                    'encounter_no' => $encounter_no,
                ),
                $param
            ),
        ));
    }

    public function registerPlayer($username, $player_id)
    {
        return $this->post("/telemed/register/his",array(
                'user_id' => $username,
                'player_id' => $player_id,
            )
        );
        
    }

    public function unRegisterPlayer($player_id)
    {
        return $this->post("/telemed/unregister/his",array(
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


    public function FunctionName(Type $var = null)
    {
        # code...
    }



}