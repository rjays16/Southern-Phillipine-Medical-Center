<?php

require_once($root_path . 'include/care_api_classes/inventory/ServiceContext.php');
require_once($root_path . 'include/care_api_classes/class_inventory.php');
require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');

class InventoryService {
    protected $requestStatus;

    function __construct() {
        $this->baseDirectory = Hospital_Admin::get('inv_directory');
    }

    public function getItemByDept($data){
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/my_department_total_items.asp',
            'method' => 'GET',
            'params' => $data
        );
        $service = new INVServiceContext($options);
        $result = $service->execute();
        $this->requestStatus = $service->status;
        return $result;
    }

    public function sendTransactItem($data){
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/transact_item2.asp',
            'method' => 'GET',
            'params' => $data
        );
        $service = new INVServiceContext($options);
        $result = $service->execute();
        $this->requestStatus = $service->status;
        return $result;
    }

    public function sendReturnItem($data){
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/return_item.asp',
            'method' => 'GET',
            'params' => $data
        );
        $service = new INVServiceContext($options);
        $result = $service->execute();
        $this->requestStatus = $service->status;
        return $result;
    }

    /*
        reference_number (&reference_number=<value>)
        barcode (&barcode=<value>)
        item_code (&item_code =<value>)
        item_name (&item_name=<value>)
        unit (&unit=<value>)
    */
    public function getItemInfo($area, $data) {
        $invClass = new Inventory();

        //GET INVENTORY KEY PER AREA
        $key = $invClass->getAPIKeyByAreaCode($area);
        $data['SEGAPIK'] = $key['inv_api_key'];
        
        //GET STOCKCARD
        $result = $this->getItemByDept($data);
        
        //CHECK REQUEST IF OK
        if($result == "Unauthorized Access" || $this->requestStatus != "200"){
            return 0;
        }

        //convert xml to array
        $xml = simplexml_load_string($result);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);

        if(!empty($array)){
            if(is_array($array['iteminfo'][0])){
                foreach ($array['iteminfo'] as $key => $value) {
                    if($value['quantity'] != 0){
                        $itemDetail = $value;
                        break;
                    }
                }
                if(empty($itemDetail))
                    $itemDetail = $array['iteminfo'][0];
            }
            else
                $itemDetail = $array['iteminfo'];

            return $itemDetail;
        }
        return 1;
    }

    /*
        item_code (&item_code=<value>)
        barcode (&barcode=<value>)
        quantity (&quantity =<value>)
        hnumber (&hnumber=<value>)
        cnumber (&cnumber=<value>)
        fname (&fname=<value>)
        lname (&lname=<value>)
        reference_number (&referance_number=<value>)
    */
    public function transactItem($area, $data) {
        $invClass = new Inventory();

        //GET INVENTORY KEY PER AREA
        $key = $invClass->getAPIKeyByAreaCode($area);
        $data['SEGAPIK'] = $key['inv_api_key'];

        //send transaction to inventory API
        $result = $this->sendTransactItem($data);

        //CHECK REQUEST IF OK
        $successMsg = "Transaction has been completed";
        if(strcmp($result,$successMsg) < 0 || $result == "Unauthorized Access" || $this->requestStatus != 200){
            return FALSE;
        }

        return $result;
    }

    /*
        item_code (&item_code=<value>)
        barcode (&barcode=<value>)
        quantity (&quantity =<value>)
        uid (&uid=<value>)
    */
    public function returnItem($area, $data){
        $invClass = new Inventory();

        //GET INVENTORY KEY PER AREA
        $key = $invClass->getAPIKeyByAreaCode($area);
        $data['SEGAPIK'] = $key['inv_api_key'];

        //send transaction to inventory API
        $result = $this->sendReturnItem($data);

        //CHECK REQUEST IF OK
        $successMsg = "Transaction has been completed";
        if(strcmp($result,$successMsg) < 0 || $result == "Unauthorized Access" || $this->requestStatus != 200){
            return FALSE;
        }

        return $result;
    }
}

?>