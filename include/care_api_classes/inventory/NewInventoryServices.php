<?php 

require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');
require_once($root_path . 'include/care_api_classes/class_inventory.php');
/*author MARK: 2016-10-07
	@for INVENTORY DAI integration

*/
/**
* 
*/

class InventoryServiceNew {
	
	function __construct() {
        $this->baseDirectory = Hospital_Admin::get('INV_directory');
        $this->baseAddress = Hospital_Admin::get('INV_address');
        $this->baseChecKDai = 'my_department_total_items';
        $this->baseCheckPrice = 'my_department_items';
        $this->baseTransactItem = 'transact_item2';
        $this->baseViewTransactItem = 'transactions';
        $this->file_extension = '.asp';
        $this->protocol = 'http://';
        $this->methodRequest = 'SEGAPIK';
        $this->placeHolder = '?';

    }

    public function cURLConfig($http_Datas){
    		  $ch = curl_init();
		        curl_setopt($ch, CURLOPT_URL,$http_Datas);
		        curl_setopt($ch, CURLOPT_FAILONERROR,1);
		        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

		  $retValue = curl_exec($ch);      
		  curl_close($ch);
		  if (simplexml_load_string($retValue))
			  return $retValue;
		  else 
			  return FALSE;

    }

    /*fetchData FROM DAI*/
	public function fetchDataInventoryByAPIkey($API,$url_data2){
		$url_data = array('item_list' => $this->baseChecKDai,
						  'item_price' => $this->baseCheckPrice,
						  'item_transact' => $this->baseTransactItem,
						  'item_transactView' =>$this->baseViewTransactItem);
		$http_Data = $this->protocol.$this->baseAddress.'/'.$this->baseDirectory.'/'.$url_data[$url_data2].$this->file_extension.$this->placeHolder.$this->methodRequest.'='.$API;
		// return $http_Data;
		return $this->cURLConfig($http_Data);
   }
  
   public function GetItemListFromDai($API_key,$inv_directory){
   		$connection = $this->PingConnectionToDAI();
   		if ($connection) {
   		$result = $this->fetchDataInventoryByAPIkey($API_key,$inv_directory);
   		// return $result;
   		try {
				$oXMLData = new SimpleXMLElement($result) or die("Invalid API.");
	    	    $json = json_encode($oXMLData);
	        	$array = json_decode($json,TRUE);
	        	
	        } catch (Exception $exc) {
	        	return 404;
	        }
	       return $array;
	   }else{
	   		return 0;
	   }
   }
   public function transactItemToDai($dataFromHIS = array(),$inv_directory,$area){
   	     $invClass = new Inventory();
   	     $connection = $this->PingConnectionToDAI();
   	     $key = $invClass->getAPIKeyByAreaCode($area);

   		 			$data ='&item_code='.$dataFromHIS['&item_code'].
                            '&barcode='.$dataFromHIS['&barcode'].
                            '&quantity='.$dataFromHIS['&quantity'].
                            '&hnumber='.$dataFromHIS['&hnumber'].
                            '&cnumber='.$dataFromHIS['&cnumber'].
                            '&fname='.str_replace(' ', '-',$dataFromHIS['&fname']).
                            '&lname='.str_replace(' ', '-',$dataFromHIS['&lname']);
         $appendURL =$key['inv_api_key'].$data;
         // var_dump($appendURL); die();
         
       if ($connection) {
       		$uid = "";
       		// $this->fetchDataInventoryByAPIkey($appendURL,$inv_directory);
	       	$msg_request  =$this->CheckTransaction($appendURL);
	       					/*Get UID Transaction*/
	       				if($msg_request){
		                    $start = stripos($msg_request, "[") + 1;
		                    if($start !== false){
		                        $length = stripos($msg_request, "]") - $start;
		                        $uid = substr($msg_request, $start, $length);
		                    }
		                }
	       
	       	if (!empty($uid)) /*Success Transaction FROM DAI*/
	       		return $uid;
	       	else		/*Failed*/
	       		return $uid = "Failed";
	       	
   		  	
	   }else{
	   		return 0;
	   }
   }
   public function CheckTransaction($API_KEY){
   		  $host = $this->baseAddress;
   		  $inv_url = $this->protocol.$host.'/'.$this->baseDirectory.'/'.$this->baseTransactItem.$this->file_extension.$this->placeHolder.$this->methodRequest.'='.$API_KEY;
   		  #if success uuid is not null
   		  // var_dump($inv_url); die();
   		try {
				$invsiteTrans = @file_get_contents(preg_replace('/\s+/', '',$inv_url));

	        } catch (Exception $exc) {
	        	return 404;
	        }
	     return $invsiteTrans;
   }

     public function PingConnectionToDAI(){
     	  try {
	     		$host = $this->baseAddress;
	            if($fp = @fsockopen($host,80,$errno,$errstr,0.5)){   
	                    $inv_url = $this->protocol.$host.'/'.$this->baseDirectory;
	                    $invsite = @file_get_contents($inv_url);
	                    if (!empty($invsite))return 1;
	                    else return 0;
	            } else return 0;
	            fclose($fp);
            	} catch (Exception $e) {
     				return 0;
     	    }
   }
   public function getIPsource(){

		    $pharma_client  = @$_SERVER['HTTP_CLIENT_IP'];
		    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		    $remote  = $_SERVER['REMOTE_ADDR'];
		    if(filter_var($pharma_client, FILTER_VALIDATE_IP)) $ip = $pharma_client;
		    elseif(filter_var($forward, FILTER_VALIDATE_IP)) $ip = $forward;
		    else $ip = $remote;
		    return $ip;
	}
	public function RequestURI(){
		return $_SERVER['SERVER_NAME'];
	}
	
}
?>