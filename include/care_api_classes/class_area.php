<?php

require_once('./roots.php'); 

class SegArea extends Core{

    var $tb_issue = "seg_areas";
    
    function getAreaName($area){
        global $db;
        
        $this->sql = "SELECT area_name FROM seg_areas WHERE area_code='$area'";
        $this->result = $db->Execute($this->sql);
        $row = $this->result->FetchRow();
        
        return $row['area_name'];     
    } 

}
?>
