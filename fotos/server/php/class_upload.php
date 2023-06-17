<?php
// Created: Vanessa A. Saren

class ECSController {
    
    #added by VAN 11-13-2012
    /**
     * @var WSPolicy $plicy
     */
    protected $policy;
    /**
     * @var WSSecurityToken $securityToken
     */
    protected $securityToken;
    
    protected $key_hie = '/srv/www/certificates/segworks.key';
    protected $cert_hie = '/srv/www/certificates/segworks.cert';
    protected $cert_hosp = '/srv/www/certificates/alice_cert.cert';
    protected $key_hosp = '/srv/www/certificates/alice_key.pem';
    protected $policy_url = '/srv/www/policies/policy.xml';
    protected $wsdl = 'https://125.5.106.4/hitp/hie/ecs/wsdl';
    #-----------------
    
    /**
     *
     * @return string
     */
    public function getWsdlPath()
    {
        #return '../../../protected/hie/config/ECSService.wsdl';
        #return 'http://192.168.1.185/segtdd/hie/ecs/wsdl';
        return $this->wsdl;
    }
    
    public function getPolicy()
    {
        if (empty($this->policy)) {
            #$policyXml = file_get_contents('../../../protected/hie/config/policy.xml');
            $policyXml = file_get_contents($this->policy_url);
            $this->policy = new WSPolicy($policyXml);
        }

        return $this->policy;
    }

    /**
     * Creates the WSSecurityToken instance
     * @return WSSecurityToken
     */
    public function getSecurityToken()
    {
        if (empty($this->securityToken)) {
            #$cert = ws_get_cert_from_file('../../../protected/hie/certificates/segworks.cert');
            #$key = ws_get_key_from_file('../../../protected/hie/certificates/segworks.key');
            $cert = ws_get_cert_from_file($this->cert_hie);
            $key = ws_get_key_from_file($this->key_hie);

            $this->securityToken = new WSSecurityToken(array(
                'certificate' => $cert,
                'privateKey' => $key,
            ));
        }

        return $this->securityToken;
    }
    
    public function uploadClaimDocument($params){
        #extract($testData);

        #$cert = ws_get_cert_from_file("../../../protected/hie/certificates/alice_cert.cert");
        #$key = ws_get_key_from_file("../../../protected/hie/certificates/alice_key.pem");
        #$rootCert = ws_get_cert_from_file("../../../protected/hie/certificates/segworks.cert");
        $cert = ws_get_cert_from_file($this->cert_hosp);
        $key = ws_get_key_from_file($this->key_hosp);
        $rootCert = ws_get_cert_from_file($this->cert_hie);
        
        $securityToken = new WSSecurityToken(array(
            "privateKey" => $key,
            "certificate" => $cert,
            "receiverCertificate" => $rootCert
        ));
        
        // segworkstechcert.pem
        // clientcert.pem
        $client = new WSClient(array(
            "useWSA" => true,
            'wsdl' =>  $this->getWsdlPath(),
            "policy" => $this->getPolicy(),
            "securityToken" => $securityToken,
			  "CACert" => '/usr/local/nginx/conf/server.crt',
			  "clientCert" => '/srv/wwww/certificates/segworkscertkey.pem',
			  'passphrase' => 's3gw0rx'
        ));

        $proxy = $client->getProxy();
        #print_r($proxy,1);
        try {    
            $result = $proxy->uploadClaimDocument($params);
            #print_r($result,1);
            return $result;
        
        } catch (Exception $e) {
            if ($e instanceof WSFault) {
                return $e->Reason;
            } else {
                #printf("Message = %s\n",$e->getMessage());
                return $e->getMessage();
            }
        }    
            
    }
    
    public function deleteClaimDocument($params){
        
        $cert = ws_get_cert_from_file($this->cert_hosp);
        $key = ws_get_key_from_file($this->key_hosp);
        $rootCert = ws_get_cert_from_file($this->cert_hie);
        
        $securityToken = new WSSecurityToken(array(
            "privateKey" => $key,
            "certificate" => $cert,
            "receiverCertificate" => $rootCert
        ));
        
        // segworkstechcert.pem
        // clientcert.pem
        $client = new WSClient(array(
            "useWSA" => true,
            'wsdl' =>  $this->getWsdlPath(),
            "policy" => $this->getPolicy(),
            "securityToken" => $securityToken,
            "CACert" => '/usr/local/nginx/conf/server.crt',
            "clientCert" => '/srv/wwww/certificates/segworkscertkey.pem',
            'passphrase' => 's3gw0rx'
        ));

        $proxy = $client->getProxy();
        #print_r($proxy,1);
        try {    
            $result = $proxy->deleteClaimDocument($params);
            #print_r($result,1);
            return $result;
        
        } catch (Exception $e) {
            if ($e instanceof WSFault) {
                return $e->Reason;
            } else {
                return $e->getMessage();
            }
        }    
            
    }

}
?>