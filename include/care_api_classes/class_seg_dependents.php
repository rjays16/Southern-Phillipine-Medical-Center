<?php
// Class for updating `seg_pharma_retail` and `seg_pharma_rdetails` tables.
// Created: 4-10-2006 (Lemuel S. Trazo)

require('./roots.php');	
require_once($root_path.'include/care_api_classes/class_core.php');

class SegDependents extends Core {

	/**
	* Database table for the discount data
	* @var string
	*/
	var $tb_dependent='seg_dependents';

	/**
	* SQL query result. Resulting ADODB record object.
	* @var object
	*/
	var $result;
	
	/**
	* Resulting record count
	* @var int
	*/
	var $count;
	var $saveOk;

	/**
	* Fieldnames of the care_appointment table.
	* @var array
	*/	
	var $fld_dependent=array(
		"parent_pid", 
		"dependent_pid", 
		"relationship", 
		"status",
		"history", 
		"modify_id",
		"modify_dt",      
		"create_id",  
		"create_dt"
		);
		
	
	/**
	* Constructor
	* @param string refno
        	*/
	function SegDependents(){
		$this->setTable($this->tb_dependent);
		$this->setRefArray($this->fld_dependent);
	}
	
	/**
	* Sets the core object to point to seg_discount and corresponding field names.
	*/
	function useSegDependents(){
		$this->coretable=$this->tb_dependent;
		$this->ref_array=$this->fld_dependent;
	}
	
	function clearDependentList($parent_id){
		global $db;
		
		$this->sql = "DELETE FROM $this->tb_dependent WHERE parent_pid='$parent_id'";
		return $this->Transact();
	}
	
	function addDependent($data, $dep_list){
		global $db,$HTTP_SESSION_VARS;
		
$parent_pid = $db->qstr($data['parent_pid']);
		$dependent_pid = $db->qstr($data['dependent_pid']);
		$relationship = $db->qstr($data['relationship']);
		$status = $db->qstr('member');
		$create_dt = $db->qstr(date('Y-m-d H:i:s'));
		$create_id = $db->qstr($HTTP_SESSION_VARS['sess_user_name']);
		$history = $this->ConcatHistory("Create " . date('Y-m-d H:i:s') . " " . $HTTP_SESSION_VARS['sess_user_name'] . "\n");
		$this->sql = "INSERT INTO seg_dependents (parent_pid, dependent_pid, relationship, status, modify_id, modify_dt, create_id, create_dt, history)
					 VALUES ($parent_pid, $dependent_pid, $relationship, $status, $create_id, $create_dt, $create_id, $create_dt, $history)";

/*		$parent_id = $db->qstr($data['pid']);
		$history = $db->qstr($data['history']);
		$modify_id = $db->qstr($data['modify_id']);
		$modify_dt = $db->qstr($data['modify_dt']);
		$create_id = $db->qstr($data['create_id']);
		$create_dt = $db->qstr($data['create_dt']);
		
		$this->sql = "INSERT INTO $this->tb_dependent(parent_pid,dependent_pid,relationship,status,history,modify_id,modify_dt,create_id,create_dt) VALUES($parent_id ,?,?,?,$history,$modify_id,$modify_dt,$create_id,$create_dt)";
		#echo "sql = ".$this->sql;*/
		if($buf=$db->Execute($this->sql,$dep_list)) {
			$this->saveOK = true;
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { $this->saveOK = false; return false; }
	}
	
	function getAllDependents($parent_id){
		global $db;
		
		$this->sql="SELECT d.*,IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS person_age, p.* 
					FROM seg_dependents AS d
					INNER JOIN care_person AS p ON p.pid=d.dependent_pid
					WHERE parent_pid='".$parent_id."'
					AND d.status NOT IN ('cancelled','deleted','expired') ";
		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
	    if ($this->count=$this->result->RecordCount()){
			return $this->result;
		 }else{
				return FALSE;
		 }
	 	}else{
			return FALSE;
    	}
	}

	// added by: syboy 12/06/2015 : meow
	function indexDependentsRemarks(){
		global $db;
		$sql = "SELECT id, remarks FROM seg_dependents_remarks WHERE status IN (0) AND pid = ?";
		return $db->GetAll($sql, $_GET['pid']);
	}

	function addDependentRemarks($pid, $remarks, $sess_username){
		global $db;
		date_default_timezone_get('Asia/Manila');
		$dateTime = date('Y/m/d H:i:s');

		$sql = "INSERT INTO seg_dependents_remarks
				(pid, remarks, create_id, create_dt)
				VALUES
				(?,?,?,?)";
		$arr = array(
				$pid,
				$remarks,
				utf8_decode($sess_username),
				$dateTime
			);

		if ($db->Execute($sql, $arr)) {
			return true;
		}else{
			return false;
		}
	}

	function getLastestDependentRemarks(){
		global $db;
		$sql = "SELECT id, remarks FROM seg_dependents_remarks WHERE status IN (0) AND pid = ? ORDER BY id DESC LIMIT 1";
		return $db->GetRow($sql, $_GET['pid']);
	}

	function deleteDependentRemarks($id, $sess_username){
		global $db;
		$dT_modfied = date('Y/m/d H:i:s');
		$sql = "UPDATE seg_dependents_remarks SET status = ?, midify_id = ?, modify_dt = ? WHERE id = ?";
		$arr = array(
					1,
					utf8_decode($sess_username),
					$dT_modfied,
					$id
			);

		if ($db->Execute($sql, $arr)) {
			return true;
		} else {
			return false;
		}
	}

	function DataDependentRemarks($id){
		global $db;
		$sql = "SELECT id, remarks FROM seg_dependents_remarks WHERE status IN (0) AND id = ?";
		return $db->GetRow($sql, $id);
	}

	function updateDependentRemarks($id, $pid, $remarks, $sess_user){
		global $db;
		$dT_modfied = date('Y/m/d H:i:s');
		$sql = "UPDATE seg_dependents_remarks SET remarks = ?, midify_id = ?, modify_dt = ? WHERE id = ? AND pid = ?";
		$arr = array(
				$remarks,
				utf8_decode($sess_user),
				$dT_modfied,
				$id,
				$pid
			);

		if ($db->Execute($sql, $arr)) {
			return true;
		}else{
			return false;
		}
	}


	// ended syboy

	function deleteDependent($parent_pid, $dependent_pid){
		global $db, $HTTP_SESSION_VARS;

		$this->sql = "UPDATE seg_dependents
						SET status='deleted',
							modify_id=".$db->qstr($HTTP_SESSION_VARS['sess_user_name']).",
							modify_dt=".$db->qstr(date('Y-m-d H:i:s')).",
						    history=".$this->ConcatHistory("Delete " . date('Y-m-d H:i:s'). " " . $HTTP_SESSION_VARS['sess_user_name'] . "\n" ) ."
					  WHERE parent_pid = " . $db->qstr($parent_pid) . "
					  AND dependent_pid = " . $db->qstr($dependent_pid);

		return $this->Transact($this->sql);
	}

	# Added by: JEFF
	# Date: 08-18-17
	# Purpose: To update relationship of dependents
	function changeRelation($rel,$id,$pid){
		global $db, $HTTP_SESSION_VARS;

		// $rel = utf8_decode(utf8_decode(utf8_encode($rel)));  <-- in case need og ñ issues pero dli mn need sa relation.

		$this->sql = "UPDATE seg_dependents
						SET relationship = " . $db->qstr($rel) . ",
							modify_id=".$db->qstr($HTTP_SESSION_VARS['sess_user_name']).",
							modify_dt=".$db->qstr(date('Y-m-d H:i:s')).",
						    history=".$this->ConcatHistory("Updated " . date('Y-m-d H:i:s'). " " . $HTTP_SESSION_VARS['sess_user_name'] . "\n" ) ."
					  WHERE parent_pid = " . $db->qstr($pid) . "
					  AND dependent_pid = " . $db->qstr($id);

		return $this->Transact($this->sql);
	}
	#Ended by: JEFF 08-18-17
	
	/**
	 * @author Gervie 01/26/2016
	 *
	 * Adding dependent upon selecting the patient in search UI.
	 */
	function addDependentNew($data){
		global $db, $HTTP_SESSION_VARS;

		$parent_pid = $db->qstr($data['parent_pid']);
		$dependent_pid = $db->qstr($data['dependent_pid']);
		$relationship = $db->qstr($data['relationship']);
		$status = $db->qstr('member');
		$create_dt = $db->qstr(date('Y-m-d H:i:s'));
		$create_id = $db->qstr($HTTP_SESSION_VARS['sess_user_name']);
		$history = $this->ConcatHistory("Create " . date('Y-m-d H:i:s') . " " . $HTTP_SESSION_VARS['sess_user_name'] . "\n");
		$this->sql = "INSERT INTO seg_dependents (parent_pid, dependent_pid, relationship, status,  modify_id, modify_dt, create_id, create_dt, history)
					 VALUES ($parent_pid, $dependent_pid, $relationship, $status, $create_id, $create_dt, $create_id, $create_dt, $history)";

		if($res=$db->Execute($this->sql))
			return true;
		else
			return false;
	}

	/**
	 * @author Gervie 01/26/2016
	 *
	 * Updating dependent that exist on the database with the same parent_pid.
	 */
	function updateExistingDependent($data){
		global $db, $HTTP_SESSION_VARS;

		$fldArray = array('parent_pid' => $db->qstr($data['parent_pid']),
						  'dependent_pid' => $db->qstr($data['dependent_pid']),
						  'relationship' => $db->qstr($data['relationship']),
						  'status' => $db->qstr('member'),
						  'modify_id' => $db->qstr($HTTP_SESSION_VARS['sess_user_name']),
						  'modify_dt' => $db->qstr(date('Y-m-d H:i:s')),
						  'history' => $this->ConcatHistory("Create " . date('Y-m-d H:i:s') . " " . $HTTP_SESSION_VARS['sess_user_name'] . " \n"));

		$res = $db->Replace('seg_dependents', $fldArray, array('parent_pid', 'dependent_pid','relationship'));

		if($res)
			return true;
		else
			return false;
	}

	/**
	 * @author Gervie 01/26/2016
	 *
	 * Deleting all dependents of the employee.
	 */
	function deleteAllDependent($parent_pid){
		global $db, $HTTP_SESSION_VARS;

		$this->sql = "UPDATE seg_dependents
						SET status='deleted',
							modify_id=".$db->qstr($HTTP_SESSION_VARS['sess_user_name']).",
							modify_dt=".$db->qstr(date('Y-m-d H:i:s')).",
						    history=".$this->ConcatHistory("Delete " . date('Y-m-d H:i:s'). " " . $HTTP_SESSION_VARS['sess_user_name'] . "\n" ) ."
					  WHERE parent_pid = " . $db->qstr($parent_pid);

		if($res=$db->Execute($this->sql))
			return true;
		else
			return false;
	}

	/**
	 * @author Gervie 04/13/2016
	 *
	 * Dependent Monitoring
	 */
	function dependentMonitoring($data, $action){
		global $db, $HTTP_SESSION_VARS;

		$user = ($data['create_id']) ? $data['create_id'] : $HTTP_SESSION_VARS['sess_user_name'];

		$parent_pid = $db->qstr($data['parent_pid']);
		$dependent_pid = $db->qstr($data['dependent_pid']);
		$relationship = $db->qstr($data['relationship']);
		$action_taken = $db->qstr($action);
		$action_date = $db->qstr(date('Y-m-d H:i:s'));
		$action_personnel = $db->qstr($user);
		$action_id = $db->qstr($_SESSION['sess_login_userid']);
		$this->sql = "INSERT INTO seg_dependents_monitoring (parent_pid, dependent_pid, relationship, action_taken, action_personnel, action_date,action_id)
					 VALUES ($parent_pid, $dependent_pid, $relationship, $action_taken, $action_personnel, $action_date,$action_id)";

		if($res=$db->Execute($this->sql))
			return true;
		else
			return false;
	}

	/**
	 * added by rnel 08-16-2016
	 * get all the monitoring for every dependents
	 */

	function getDependent($depid) {
		global $db;

		 $this->sql = "SELECT sdm.parent_pid, sdm.action_taken, sdm.action_personnel, sdm.action_date
		 				FROM seg_dependents_monitoring AS sdm
		 				WHERE sdm.dependent_pid = {$db->qstr($depid)}";

		 if($this->result=$db->Execute($this->sql)) {
		 	return $this->result;
		 } else {
		 	return false;
		 }

	}

	function getDependent2($depid, $ppid) {
		global $db;
		$this->sql = "SELECT sm.history
						FROM seg_dependents AS sm
						WHERE sm.dependent_pid = {$db->qstr($depid)}
						AND sm.parent_pid = {$db->qstr($ppid)}";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else {
			return false;
		}
	}

	/**
	 * end rnel
	 */

	#rnel
	public function personellStatus($pid) {

		global $db;
		$this->sql = "SELECT a.*
						FROM care_personell AS a
						WHERE a.status = 'deleted'
						AND a.pid = {$db->qstr($pid)}";

		$this->result = $db->Execute($this->sql);
		// var_dump($this->result->RecordCount()); die;
		if($this->result->RecordCount() > 0) {

			return true;
		} else {

			return false;
		}
	}

	// function dependentHistory($dependent_pid=0) {
	// 	global $db;

	// 	$sql = "SELECT history FROM seg_dependents WHERE dependent_pid = {$dependent_pid}";
	// 	$history = $db->GetRow($sql, $id);
	// 	return $history;
	// }
}
?>