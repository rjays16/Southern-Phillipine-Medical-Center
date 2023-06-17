<?php 
/**
 * APIservices.php
 *
 * @author Mark Gocela <alecogkram@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 */
/**
* 
*/
namespace SegHis\modules\InventoryAPIServices\components\api;
use SegHis\modules\InventoryAPIServices\components\urlconfig\urlconfig;
use PestXML;
use Pest;
use Yii;
use CHttpException;
class APIservices 
{
	protected $pest = null;
    /**
     * DAI constructor.
     * @param $url
     */
    public function __construct()
    {
    	Yii::import('application.components.pest.*');
    	/*addHeader by default*/
    	/*setupAuth by default*/
		$this->pest = new Pest($this->setUpURL());
    }

    public function setUpURL(){
    	/*HTTP default to 'http://'
		 you may change by instance of the class in given arguments that passed to the class constructor
		 e.g: urlconfig('https://')*/
		 $DaiUrl =  new urlconfig();
    	return $DaiUrl->getLink();
    }
    /**
     * enhancement later after testing all API's
     * @param String $api,String $LinkDir as  array_key
     * @return array
     */
    public function getItemsFromDAI($api,$LinkDir)
    {
    	$data['SEGAPIK'] =$api;
    	$source = include(\Yii::getPathOfAlias('InventoryAPIServices.components.data.directory').'.php');
    	if (array_key_exists($LinkDir,$source)) {
    			$directories = $source[$LinkDir];
		        $result =  $this->pest->get(DIRECTORY_SEPARATOR.$directories,$data);
		        $xml    =  simplexml_load_string($result);
				$json   =  json_encode($xml);
				$array  =  json_decode($json,TRUE);
		        return  $array;
    	}else{
    		 throw new CHttpException(500, 'The system has detected an invalid request URL');
    		
    	}
    }
    /**
     * enhancement later after testing all API's
     * @param array $data must contain 
	    	item_code (&item_code=<value>)
	        barcode (&barcode=<value>)
	        quantity (&quantity =<value>)
	        hnumber (&hnumber=<value>)
	        cnumber (&cnumber=<value>)
	        fname (&fname=<value>)
	        lname (&lname=<value>)
	        reference_number (&referance_number=<value>)
     * @return String
     */

    public function transactItemToDai($data =array(),$api){
    	$source = include(\Yii::getPathOfAlias('InventoryAPIServices.components.data.directory').'.php');
    	if (array_key_exists($LinkDir,$source)) {
    		$directories = $source[$LinkDir];
    		$result =  $this->pest->get(DIRECTORY_SEPARATOR.$directories,$data);
    		$uid    = "";
    		if(!empty($result)){
		                    $start = stripos($result, "[") + 1;
		                    if($start !== false){
		                        $length = stripos($result, "]") - $start;
		                        $uid = substr($result, $start, $length);
		                    }
		     }else{
		     	throw new CHttpException(500, 'Empty result request from DAI');
		     }
    		return  $uid;
    	}else{
    		 throw new CHttpException(500, 'The system has detected an invalid request URL');
    		
    	}

    }

    
   
}