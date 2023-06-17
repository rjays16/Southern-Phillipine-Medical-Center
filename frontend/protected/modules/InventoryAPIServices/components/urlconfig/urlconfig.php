<?php 
/**
 * urlconfig.php
 *
 * @author Mark Gocela <alecogkram@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 */
/**
* 
*/
namespace SegHis\modules\InventoryAPIServices\components\urlconfig;
use \SegHis\modules\InventoryAPIServices\components\urlconfig\protocol;
use \SegHis\models\HospitalInfo;
use CHttpException;

class urlconfig extends protocol
{
	protected $rawLink = null;
	/**
     * @return string
     * @throws FailedRequestException
     */
	public function getLink(){
		 $this->baseModel =  HospitalInfo::model()->find();
		 if(!$this->baseModel){
		 	throw new CHttpException(500, 'return an empty model of HospitalInfo');
		 }
		return $this->rawLink =$this->getProxy().$this->baseModel->INV_address."/". $this->baseModel->INV_directory."/";
	}
}