<?php
class InventoryServiceYii extends Pest {
	private $_url;
    public $hasErrorConnecting, $inventoryAddress;

	public function __construct() {
    	$this->hospitalInfoModel = HospitalInfo::model()->find();

		if($this->hospitalInfoModel){
			$this->_url = "http://" . $this->hospitalInfoModel->INV_address.
				"/". $this->hospitalInfoModel->INV_directory."/";
    	}
        //Checks connection to API
        exec(sprintf('ping -c 1 -W 1 %s', escapeshellarg($this->hospitalInfoModel->INV_address)), $res, $rval);
        if($rval !== 0)
            $this->hasErrorConnecting = TRUE;
        $this->inventoryAddress=$this->hospitalInfoModel->INV_address."";
        parent::__construct($this->_url);
        $this->setAuth();
    }

    public function setAuth() {
    	parent::setupAuth("", "");
    }

    /*
        reference_number (&reference_number=<value>)
        barcode (&barcode=<value>)
        item_code (&item_code =<value>)
        item_name (&item_name=<value>)
        unit (&unit=<value>)
    */
    public function getItemInfo($area, $data) {
        $pharmacyAreaModel = new PharmacyArea();

        //GET INVENTORY KEY PER AREA
        $key = $pharmacyAreaModel->getAPIKeyByAreaCode($area);
        $data['SEGAPIK'] = $key;
        
        //GET STOCKCARD
        $result = $this->get('/my_department_total_items.asp', $data);
        //CHECK REQUEST IF OK
        if($result == "Unauthorized Access" || $this->status != "200"){
            return 0;
        }

        //convert xml to array
        $xml = simplexml_load_string($result);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);

        if(!empty($array)){
            if(is_array($array['iteminfo'][0])){
                foreach ($array['iteminfo'] as $key => $value) {
                    if($value['quantity'] != 0 && ($data['barcode']==$value['barcode'])){
                        $itemDetail = $value;
                        break;
                    }
                }
                if(empty($itemDetail))
                    $itemDetail = null;
            }
            else
                $itemDetail = $array['iteminfo'];

            return $itemDetail;
        }
        return 0;
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
        $pharmacyAreaModel = new PharmacyArea();

        //GET INVENTORY KEY PER AREA
        $key = $pharmacyAreaModel->getAPIKeyByAreaCode($area);
        $data['SEGAPIK'] = $key;

        //send transaction to inventory API
        $result = $this->get('/transact_item2.asp', $data);
        unset($save_transaction);
        $save_transaction = $data;
        $save_transaction = array_merge($save_transaction, array('url' => http_build_query($data)));
        $this->insertInvUID(serialize($save_transaction));
        //CHECK REQUEST IF OK
        $successMsg = "Transaction has been completed";
        if(strcmp($result,$successMsg) < 0 || $result == "Unauthorized Access" || $this->status != 200){
            return FALSE;
        }

        return $result;
    }

    public function insertInvUID($dataINV){
        global $db;
        $sql_insert ="INSERT INTO `seg_inventory_logs` SET
        `data_log`=".$db->qstr($dataINV)."";

        if($db->Execute($sql_insert))
            return TRUE;
        else return FALSE;              
    }

}