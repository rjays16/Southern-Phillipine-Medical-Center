<?php 
/**
 * protocol.php
 *
 * @author Mark Gocela <alecogkram@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 */
/**
* 
*/
namespace SegHis\modules\InventoryAPIServices\components\urlconfig;
abstract class protocol 
{
	protected $http;
	public function __construct($http = 'http://'){
		$this->http = $http;
	}
	public function getProxy(){
		return $this->http; 
	}
	abstract protected function getLink();
	
}