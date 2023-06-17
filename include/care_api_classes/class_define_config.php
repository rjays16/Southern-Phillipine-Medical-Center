<?php 

/**
* @author : art 03/05/2015 
* class to get values from db tbl seg_define_config
*/
require_once($root_path.'include/care_api_classes/class_core.php');

class Define_Config extends Core
{
	var $value;

	function __construct($param) {     
		global $db;     
		$this->value = $db->GetOne("SELECT value FROM seg_define_config WHERE param = ".$db->qstr($param));  
	}              
	
	
	function get_value() {          
		return $this->value;
	}              
}


 ?>