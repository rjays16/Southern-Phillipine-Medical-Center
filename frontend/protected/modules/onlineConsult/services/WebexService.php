<?php


namespace SegHis\modules\onlineConsult\services;


use DoctorMeeting;

class WebexService
{
    private $siteName;
    private $webExID;
    private $password;

    private $username;
    private $siteID;
    private $send_mode;
    private $action;

    const SEND_CURL = 'curl';
    const SEND_FSOCKS = 'fsocks';
    const PREFIX_HTTPS = 'https';
    const SUFIX_XML_API = 'WBXService/XMLService';
    const WEBEX_DOMAIN = 'webex.com';
    const XML_VERSION = '1.0';
    const XML_ENCODING = 'UTF-8';
    const ACCEPT = 'Accept: application/xml';
    const CONTENT_TYPE = 'Content-Type: text/xml';
    const API_SCHEMA_SERVICE = 'http://www.webex.com/schemas/2002/06/service';
    const API_SCHEMA_MEETING = 'http://www.webex.com/schemas/2002/06/service/meeting';

    public function __construct($doctorId)
    {
        $doctorMeeting = DoctorMeeting::model()->findByAttributes(array(
            'doctor_id' => $doctorId
        ));
        $this->WebExAccount($doctorMeeting);

        $this->siteName;
        $this->webExID;
        $this->password;

        $this->action    = 0;
        $this->response  = array();
        $this->send_mode = in_array(self::SEND_CURL, get_loaded_extensions())
            ? self::SEND_CURL : self::SEND_FSOCKS;
    }

    public function WebExAccount($doctorMeeting)
    {
        $this->siteName = $doctorMeeting->site_name;
        $this->webExID  = $doctorMeeting->webex_id;
        $this->password = $doctorMeeting->password;
    }

    public function set_sendmode($mode)
    {
        if (!in_array($mode, self::get_sendmode())) {
            exit(__CLASS__ . ' error report: Wrong send mode');
        }
        $this->send_mode = $mode;
    }

    public function set_auth($username, $password, $siteID)
    {
        $this->username = $username;
        $this->password = $password;
        $this->siteID   = $siteID;
    }

    private function get_xml($data)
    {
        $xml   = array();
        $xml[] = '<?xml version="' . self::XML_VERSION . '" encoding="'
            . self::XML_ENCODING . '"?>';
        $xml[] = '<serv:message xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
        $xml[] = '<header>';
        $xml[] = '<securityContext>';
        $xml[] = '<siteName>' . $this->siteName . '</siteName>';
        $xml[] = '<webExID>' . $this->webExID . '</webExID>';
        $xml[] = '<password>' . $this->password . '</password>';
        $xml[] = '</securityContext>';
        $xml[] = '</header>';
        $xml[] = '<body>';
        $xml[] = '<bodyContent xsi:type="java:com.webex.service.binding.'
            . $data['service'] . '">';
        $xml[] = $data['xml_body'];
        $xml[] = '</bodyContent>';
        $xml[] = '</body>';
        $xml[] = '</serv:message>';

        return implode('', $xml);
    }

    private function send($data)
    {
        extract($data);

        $ch = curl_init(self::PREFIX_HTTPS . '://' . $this->siteName . '.' . self::WEBEX_DOMAIN . '/'
            . self::SUFIX_XML_API
        );

        curl_setopt(CURLOPT_HTTPHEADER, array(self::ACCEPT, self::CONTENT_TYPE, 'SOAPAction', 'MySoapAction'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        if ($response === false) {
            exit(__CLASS__ . ' error report: Curl error - ' . curl_error($ch));
        }

        curl_close($ch);

        return $response;
    }

    public function getMeeting($key)
    {
        $xml_body   = array();
        $xml_body[] = '<meetingKey>';
        $xml_body[] = $key;
        $xml_body[] = '</meetingKey>';

        $data['xml_body'] = implode('', $xml_body);
        $data['service']  = 'meeting.GetMeeting';

        $xml      = $this->get_xml($data);
        $response = $this->send($xml);

        $xml = simplexml_load_string($response);

        $Data                   = new stdClass;
        $Data->header           = new stdClass;
        $Data->header->response = new stdClass;
        $Data->bodyContent      = array();

        $node                           = $xml->children(self::API_SCHEMA_SERVICE);
        $Data->header->response->result = (string)$node[0]->response->result;
        $Data->header->response->gsbStatus
                                        = (string)$node[0]->response->gsbStatus;
        $node_meeting                   = $node[1]->bodyContent;

        return (string)$node_meeting->children(self::API_SCHEMA_MEETING)->meetingLink;
    }

    public function createMeeting()
    {
        $xml_body   = array();
        $xml_body[] = '<accessControl>';
        $xml_body[] = '<meetingPassword>';
        $xml_body[] = 'password';
        $xml_body[] = '</meetingPassword>';
        $xml_body[] = '</accessControl>';
        $xml_body[] = '<metaData>';
        $xml_body[] = '<confName>';
        $xml_body[] = 'Meeting with ' . $this->username;
        $xml_body[] = '</confName>';
        $xml_body[] = '</metaData>';
        $xml_body[] = '<schedule>';
        $xml_body[] = '<startDate>';
        $xml_body[] = 'Today date';
        $xml_body[] = '</startDate>';
        $xml_body[] = '<duration>';
        $xml_body[] = '30';
        $xml_body[] = '</duration>';
        $xml_body[] = '</schedule>';

        $data['xml_body'] = implode('', $xml_body);
        $data['service']  = 'meeting.CreateMeeting';

        $xml      = $this->get_xml($data);
        $response = $this->send($xml);
    }


}
