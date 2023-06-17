<?php


namespace SegHis\modules\medRec\services;

use Curl;
use ServiceCallException;
use Config;


\Yii::import('eclaims.services.Curl');
\Yii::import('eclaims.services.ServiceCallException');

class NotificationService
{
    protected static $baseUrl;

    public function __construct()
    {
        $this->curl = new Curl;
        $this->curl->setHeaders(
            array(
                'Authorization' => 'Bearer ' . env('ONESIGNAL_API_TOKEN')
            )
        );
    }

    /**
     * @param array $payload
     * @return mixed
     */
    public function sendNotification($apiUrl, $payload)
    {
        $url = env('ONESIGNAL_API_URL') . $apiUrl;
        $this->curl->post($url, $payload);
        return $this->processResult();
    }


    /**
     * Parses the results and/or generate the necessary errors
     * @throws ServiceCallException When the method detects that the last
     * service call has indeed produced an error
     */
    protected function processResult()
    {

        $info    = $this->curl->getInfo();
        $result  = $this->curl->getResult();
        $decoded = \CJSON::decode($result);
        if ($info['http_code'] == 200) {
            if ($decoded) {
                return $decoded;
            } else {
                throw new ServiceCallException(500, 'The service call response contains invalid data', $result);
            }
        } else {

            switch ($info['http_code']) {
                case 404:
                    throw new ServiceCallException(404, 'Invalid service location( ' . $this->getBaseUrl() . $this->endpoint . ' )');
            }

            $message = '';
            $data    = null;

            if ($decoded) {
                $message = @$decoded['message'];
                $data    = @$decoded['data'];
            }

            if (empty($message)) {
                $message = $this->curl->getError() ? $this->curl->getError() : 'Unexpected error encountered in service call';
            }

            throw new ServiceCallException($info['http_code'], $message, $data);
        }
    }

    /**
     * Normalizes the Base URL of the service endpoint
     * @throws CException When the base_url configuration is not found
     */
    protected function getBaseUrl() {
        if (empty(self::$baseUrl)) {
            self::$baseUrl = (string) Config::get('hie_service_base_url');
            if (substr(self::$baseUrl, -1) !== '/') {
                self::$baseUrl .= '/';
            }
        }
        return self::$baseUrl;
    }    

}

