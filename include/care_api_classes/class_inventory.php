<?php

require("./roots.php");
require_once($root_path . 'include/care_api_classes/class_core.php');
require_once($root_path . 'include/care_api_classes/class_globalconfig.php');

/**
 * @author Justin Tan
 */
class Inventory extends Core{

	var $tb_inv_area 		= 'seg_pharma_areas';
	var $tb_personell_inv 	= 'seg_personell_inv_area';

	var $fld_inv = array('area_code',
						'area_name',
						'allow_socialized',
						'lockflag',
						'inv_area_code',
						'inv_api_key',
						'history',
						'create_id',
						'create_dt',
						'modify_id',
						'modify_dt');

	var $fld_personell_inv = array('personell_nr',
						'area_code');

    function getAPIKeyByAreaCode($area){
        global $db;

        $this->sql = "SELECT inv_api_key 
        			FROM $this->tb_inv_area 
        			WHERE area_code = ".$db->qstr($area);
        $row = $db->GetRow($this->sql);

        return $row;
    }

    //get all inventory area
    function getInventoryArea(){
    	$this->sql = "SELECT area_code, area_name,is_deleted FROM $this->tb_inv_area WHERE inv_api_key IS NOT NULL OR show_area = 1 ORDER BY area_name";

    	return $this->executeQuery();
    }
    /*added by MARK April 19, 2017*/
    function getPharmaAreaByuserDefault($personell_nr){
        global $db;
        $this->sql = "SELECT pda.area_code,spa.area_name 
              FROM pharma_default_areas AS pda
              INNER JOIN seg_pharma_areas AS spa
              ON spa.area_code = pda.area_code
              WHERE pda.default_area='1' AND pda.personell_nr = ".$db->qstr($personell_nr);
        $row = $db->GetRow($this->sql);
        return $row;
    }

	//get all inventory area of personnel
	function getInventoryAreaByPersonnel($personnelNr){
    	global $db;

		$this->sql = "SELECT b.`area_code`, b.`area_name`,b.`is_deleted` 
					FROM $this->tb_personell_inv a 
					LEFT JOIN $this->tb_inv_area b ON b.`area_code` = a.`area_code`
					WHERE a.`personell_nr` = ".$db->qstr($personnelNr);

    	return $this->executeQuery();
	}
    function getInventoryAreaAll(){
      global $db;
      $this->sql = "SELECT * FROM  $this->tb_inv_area ";
      return $this->executeQuery();
  }
	function clearAssignedInvArea($personell_nr) {
    	global $db;
    	
		$this->sql = "DELETE FROM $this->tb_personell_inv WHERE personell_nr = ".$db->qstr($personell_nr);

		return $this->Transact();
	}

	function addInvAreaToPersonnel($personell_nr, $invArray) {
		global $db;

		$this->sql = "INSERT INTO $this->tb_personell_inv (personell_nr,area_code) 
			VALUES(".$db->qstr($personell_nr).",?)";

    	return $this->executeQuery($invArray);
	}

    private function executeQuery($params = null){
    	global $db;

    	if(empty($params))
    		$this->result = $db->Execute($this->sql);
    	else
    		$this->result = $db->Execute($this->sql, $params);

    	if($this->result){
    		if($this->count = $this->result->RecordCount()) {
    			return $this->result;
    		}else{
    			return false;
    		}
		}else{
			return false;
		}
    }

    /*added By MARK 10-01-2016*/
    function getAreasPharma(){
    	global $db;
    	$this->sql ="SELECT  `area_code`,
                              `area_name`,
                              `allow_socialized`,
                              `lockflag`,
                              `show_area`,
                              `inv_area_code`,
                              `inv_api_key`,
                              `history`,
                              `create_id`,
                              `create_dt`,
                              `modify_id`,
                              `modify_dt`,
                              `is_deleted` FROM ".$this->tb_inv_area;
        if($this->result = $db->GetAll($this->sql))
        return $this->result;
        else return $this->result;
        
    }
     function getAreasPharmaBy($area_code){
        global $db;
        $this->sql ="SELECT * FROM ".$this->tb_inv_area." WHERE area_code=".$db->qstr($area_code);
       if ($this->result=$db->Execute($this->sql)) {
            if ($this->count=$this->result->RecordCount())
                return $this->result;
            else return FALSE;
        }else return FALSE;
        
    }
      function getRcodefromArea($rcodes){
        global $db;
        $this->sql="SELECT inv_area_code FROM seg_pharma_areas WHERE inv_area_code=".$db->qstr($rcodes);
         if ($result=$db->Execute($this->sql)) {
                if ($result->RecordCount()) {
                    if ($row = $result->FetchRow()) {
                        if (!is_null($row["inv_area_code"])) {
                            $RCODES = $row["inv_area_code"];
                        }
                    }
                }
            }
            return $RCODES;
    }
        function getAPIfromArea($area_code,$API){
        global $db;
        $this->sql="SELECT inv_api_key FROM seg_pharma_areas WHERE area_code !=".$db->qstr($area_code)." AND inv_api_key=".$db->qstr($API);
         if ($result=$db->Execute($this->sql)) {
                if ($result->RecordCount()) {
                    if ($row = $result->FetchRow()) {
                        if (!is_null($row["inv_api_key"])) {
                            $APICODE = $row["inv_api_key"];
                        }
                    }
                }
            }
            return $APICODE;
    }

     function savePharmaAreas($sql_statement,$area_code,$area_name, $allow_socialized,$lockflag,$show_area,$inv_area_code,$api_key){
        global $db, $HTTP_SESSION_VARS;
        $area_codex = $db->qstr($area_code);
        $area_namex = $db->qstr($area_name);
        $allow_socializedx = $db->qstr($allow_socialized);
        $lockflagx = $db->qstr($lockflag);
        $show_areax = $db->qstr($show_area);
        $inv_area_codex = $db->qstr($inv_area_code);
        $inv_api_keyx = $db->qstr($api_key);
        $create_id = $db->qstr($HTTP_SESSION_VARS['sess_user_name']);
        $create_dt = $db->qstr(date('Y-m-d'));
        $history_create = $db->qstr("Created by ".$HTTP_SESSION_VARS['sess_user_name']." | " . date('Y-m-d H:i:s') . "\n");
        $history_modify = $this->ConcatHistory("Modified by ".$HTTP_SESSION_VARS['sess_user_name']." | ".date('Y-m-d H:i:s')."\n");
        $history_delete = $this->ConcatHistory("Deactivated by ".$HTTP_SESSION_VARS['sess_user_name']." | ".date('Y-m-d H:i:s')."\n");
        $history4_undo_delete = $this->ConcatHistory("Undo Delete by ".$HTTP_SESSION_VARS['sess_user_name']." | ".date('Y-m-d H:i:s')."\n");
        if ($sql_statement == "UPDATE") {
             $where = "history = ".$history_modify.",
                     modify_id = $create_id
                     WHERE area_code= $area_codex";
        }
        if ($sql_statement=="INSERT INTO") {
           $where = "history = $history_create,
                     create_id = $create_id,
                     create_dt = $create_dt";
        }if($sql_statement=="DELETE"){
            $sql_statement = "UPDATE";
             $where = "history = ".$history_delete.",
                     modify_id = $create_id,
                     is_deleted = 1
                     WHERE area_code= $area_codex";
        }if($sql_statement=="UNDO"){
            $sql_statement = "UPDATE";
             $where = "history = ".$history4_undo_delete.",
                     modify_id = $create_id,
                     is_deleted = 0
                     WHERE area_code= $area_codex";
        }

        $this->sql ="$sql_statement seg_pharma_areas SET   area_code= $area_codex,
                                                        area_name = $area_namex,
                                                        allow_socialized = $allow_socializedx,
                                                        lockflag = $lockflagx,
                                                        show_area = $show_areax,
                                                        inv_area_code = $inv_area_codex,
                                                        inv_api_key = $inv_api_keyx,".$where;
                                           
        if($res=$db->Execute($this->sql))
            return true;
        else
            return false;
    }


    /*End By MARK 10-01-2016*/

}