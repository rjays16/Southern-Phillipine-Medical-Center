<?php 
require_once($root_path.'include/care_api_classes/class_core.php');
/**
 * 
 * @author Marc Francis Alhambra
 */
class Permission extends Core
{
	 function getPermssionId($id){
			 global $db;
			 $this->id  = $db->GetOne("SELECT login_id from care_users WHERE personell_nr=".$db->qstr($id));
			 return $this->id;
	}
}
?>
