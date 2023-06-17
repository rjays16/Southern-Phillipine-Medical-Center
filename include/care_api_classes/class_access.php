<?php
/**
* @package care_api
*/

/**
*/
require_once($root_path.'include/care_api_classes/class_core.php');
/**
* Class for access authentication rout�nes.
* Extends the class "Core".
* Note this class should be instantiated only after a "$db" adodb  connector object has been established by an adodb instance
* @package care_api
*/
class Access extends Core {
	/**
	* Users table name
	* @var string
	*/
	var $tb_user='care_users';
	var $tb_care_session3='care_session3';
	var $tb_personell = 'care_personell';
	var $session_id = 0;
	/**
	* Holder for user data in associative array
	* @var array
	*/
	var $user=array();
	/**
	* Allowed areas in hieararchical order
	* @var array
	*/
	var $allowedareas=array();
	/**
	* User's registration status.
	* FALSE = unknown.
	* TRUE = known.
	* @var boolean
	*/
	var $usr_status=FALSE;
	/**
	* Flags if the "all" permission type is permitted.
	* Default is TRUE.
	* @var boolean
	*/
	var $permit_type_all=TRUE;
	/**
	* Password status.
	* FALSE = wrong password.
	* TRUE = correct password.
	* @var boolean
	*/
	var $pw_status=FALSE;
	/**
	* The access permission status.
	* FALSE = locked.
	* TRUE = access allowed.
	* @var boolean
	*/
	var $lock_status=FALSE;
	/**
	* Internal buffer for the login id (username)
	*/
	var $login_id;
	/**
	* Constructor. If login and password are passed as parameters, the access data are immediately loaded.
	*
	* For example:
	*
	* <code>
	* $user =  & new Access('Smith','Cocapabana');
	* if( $user->isKnown() && $user->hasValidPassword && $user->isNotLocked()){
	* ...
	* }
	* </code>
	*
	* @param string Login name
	* @param string Password
	* @access public
	* @return boolean
	*/
	function Access($login='',$pw='',$no_encryption=false){
		$this->coretable=$tb_user;
		$this->login_id =$login;
		if(!empty($login)&&!empty($pw)){
			return $this->loadAccess($login,$pw,$no_encryption);
		}
	}
	
	/**
	* Loads the user data and checks its access status. 
	* Use if login and password were not passed during construction OR if a new access data is to be loaded using the same object instance.
	*
	* For example:
	*
	* <code>
	* $user =  & new Access;
	* ....
	* $user->loadAccess('Smith','Cocapabana');
	* if( $user->isKnown() && $user->hasValidPassword && $user->isNotLocked()){
	* ...
	* }
	* </code>
	*
	* @param string Login name
	* @param string Password
	* @access public
	* @return boolean
	*/
	function loadAccess($login='',$pw='',$no_encryption=false){
		/**
		* @global ADODB-db-link
		*/
		global $db;
		# Reset all status
		$this->pw_status=FALSE;
		$this->lock_status=FALSE;
		if(empty($login)){
			if(!empty($this->login)) $login=$this->login;
				else return FALSE;
		}
		if(empty($pw)){
			if(!empty($this->pw)) $pw=$this->pw;
				else return FALSE;
		}
		#Modifed by Matsuu for SPMC-1032
		// $this->sql="SELECT name,login_id,password, personell_nr, permission, lockflag FROM $this->tb_user WHERE login_id ='".addslashes($login)."'";
		$this->sql="SELECT cu.name,cu.login_id,cu.password,cu.personell_nr,cu.permission,cu.lockflag FROM $this->tb_user as cu LEFT JOIN $this->tb_personell as cp ON cp.nr = cu.personell_nr WHERE login_id = ".$db->qstr($login)." AND cp.status NOT IN('deleted','expired')";
		if ($result=$db->Execute($this->sql)) {
		    if ($this->rec_count=$result->RecordCount()) {
		       $this->user=$result->FetchRow();
			   $this->usr_status=TRUE; # User is known

				if ($no_encryption){
					if($this->user['password']==$pw) $this->pw_status=TRUE; # Password is valid
				}else{
					if($this->user['password']==md5($pw)) $this->pw_status=TRUE; # Password is valid
				}
			   if((int)$this->user['lockflag'])  $this->lock_status=TRUE; # Access is locked
			   return TRUE;
			}else{
				$usr_status=FALSE;
				return FALSE;
			}
		}else{
			$usr_status=FALSE;
			return FALSE;
		}
	}
	/**
	* Returns the password status of the user
	* @access public
	* @return boolean  TRUE = password valid, else FALSE = invalid password
	*/
	function hasValidPassword(){
		return $this->pw_status;
	}
	/**
	* Returns the user  status of the user whether he is registered user or not.
	* @access public
	* @return boolean  TRUE = is registered as user, else FALSE = invalid user
	*/
	function isKnown(){
		return $this->usr_status;
	}
	/**
	* Returns the user permission "is locked?" status.
	* Use only after the access data was loaded by the constructor or loadAccess() method.
	* @access public
	* @return boolean TRUE = User permissionis locked, else FALSE = user unknown or unregisted
	*/
	function isLocked(){
		return $this->lock_status;
	}
	/**
	* Returns the permission "is not locked?" status. A negation of isLocked() method.
	* Use only after the access data was loaded by the constructor or loadAccess() method.
	* @access public
	* @return boolean FALSE = User permission is locked, else TRUE = permission is locked
	*/
	function isNotLocked(){
		return !$this->lock_status;
	}
	/**
	* Returns the user's registered name.
	* Use only after the access data was loaded by the constructor or loadAccess() method.
	* @access public
	* @return string
	*/
	function Name(){
		return $this->user['name'];
	}
	/**
	* Returns the user's login name ( login username ).
	* Use only after the access data was loaded by the constructor or loadAccess() method.
	* @access public
	* @return string
	*/
	function LoginName(){
		return $this->user['login_id'];
	}
	/**
	* Returns the permission areas of the user. No interpretation is returned.
	* Use only after the access data was loaded by the constructor or loadAccess() method.
	* @access public
	* @return string
	*/
	function PermissionAreas(){
		return $this->user['permission'];
	}

	function personellNr(){
		return $this->user['personell_nr'];
	}

	function notification(){
		return $this->user['notification'];
	}

	/**
	* Checks if the user is permitted in a given protected area.
	*
	* Use only after the access data was loaded by the constructor or loadAccess() method.
	* @access public
	* @param string The area to be checked.
	* @return string
	*/
	function isPermitted($area=''){
		if(empty($area)) return false;
		return (stristr($this->user['permission'],$area));
	}
	/**
	* Sets the allowed hierarchical areas.
	*
	* @param array The allowed areas in hierarchy.
	* @access public
	* @return string
	*/
	function setAllowedAreas($areas=''){
		if($areas){
			$this->allowedareas=$areas;
			return TRUE;
		}else{
			return FALSE;
		}
	}
	/**
	* Checks if the user is permitted in the group of protected areas.
	*
	* This checks also whether the user is permitted in the area due to its role or position in the privilege hierarchy.
	* The group of areas must be set first with the "setAllowedAreas()" method.
	* Use only after the access data was loaded by the constructor or loadAccess() method.
	* @access public
	* @param string The area to be checked.
	* @return string
	*/
	function isPermittedInGroup($user_area=''){
		if(empty($user_area)){
			return FALSE;
		}else{
			if(ereg('System_Admin', $user_area)){  // if System_admin return true
	   			return TRUE;
			}elseif(in_array('no_allow_type_all', $this->allowedareas)){ // check if the type "all" is blocked, if so return false
	     			return FALSE;
			}elseif($this->permit_type_all && ereg('_a_0_all', $user_area)){ // if type "all" , return true
				return TRUE;
			}else{                                                                  // else scan the permission
				for($j=0;$j<sizeof($this->allowedareas);$j++){
					if(ereg($this->allowedareas[$j],$user_area)) return TRUE;
				}
			}
			return FALSE;           // otherwise the user has no access permission in the area, return false
		}
	}
	/**
	*  Checks the  data if user exists based on his username (login id)
	*
	* @public
	* @param string Username or login id
	* @return mixed adodb record or boolean FALSE
	*/
	function UserExists($login_id){
		global $db;
		if(!empty($login_id)) $this->login_id=$login_id;
			elseif(empty($this->login_id)) return FALSE;

		$this->sql="SELECT * FROM care_users WHERE login_id='".addslashes($this->login_id)."'";

		if ($this->res['_ud']=$db->Execute($this->sql)) {
			if ($this->res['_ud']->RecordCount()) {
				$this->user = $this->res['_ud']->FetchRow();
				$this->lock_status = $this->user['lockflag'];
				return TRUE;
			} else {
				$this->usr_status=FALSE;
				return false;
			}
		} else {
			$this->usr_status=FALSE;
			return false;
		}
	}
	/**
	*  Changes the lock status of the user
	*
	* @private
	* @param boolean
	* @return boolean
	*/
	//Edited by Arvin 04/17/2018
	function _changelock($newlockflag=0, $reason=null, $otherReason=null){
		global $db;
		$start_time = $_SESSION['DEACTIVATION_TIME_IN'];
		$interval = $start_time->diff(new DateTime());
		$duration = $interval->format('%H:%I:%S');
		$locker_mode=($newlockflag?"LOCK":"UNLOCK");
		$finalReason = $reason;

		//Checks user lock status
		if($newlockflag == 1) {
			//check if there is other reason
			if( $reason == "Others" ) {
				$finalReason = $otherReason;
			} 
		} else {
			$finalReason = null;
		}

		$lock_audit="INSERT INTO 
								seg_areas_duration_time ( pid, duration, mode, create_id, create_dt, reason ) 
					SELECT 
								cp.pid,
								".$db->qstr($duration).",
								".$db->qstr($locker_mode).",
								".$db->qstr($_SESSION['sess_user_name']).",
								".$db->qstr(date('Y-m-d H:i:s')).",
								".$db->qstr($finalReason)."
								FROM care_personell cp LEFT JOIN care_users cu on cp.nr=cu.personell_nr 
								WHERE cu.login_id=".$db->qstr($this->login_id);

		$db->Execute($lock_audit);
		$this->sql="UPDATE $this->tb_user SET lockflag=".$db->qstr($newlockflag).",lock_duration=".$db->qstr($duration).",modify_time=NOW(),modify_id=".$db->qstr($_SESSION['sess_user_name'])." WHERE login_id=".$db->qstr($this->login_id);
      	$sql_areas = "INSERT INTO seg_areas_duration_time (" .
	      			"pid,".
	      			"areas,".
	      			"old_areas,".
	      			"duration,".
	      			"mode,".
	      			"create_id,".
	      			"create_dt".
	      			") SELECT ".
	      			"cu.personell_nr," .
	      			"cu.permission," .
	      			"cu.permission," .
	      			$db->qstr($duration) . "," .
	      			$db->qstr(($newlockflag?"LOCK":"UNLOCK")). "," .
	      			$db->qstr($_SESSION['sess_user_name']) . "," .
	      			$db->qstr(date('YmdHis')) .
      			" FROM care_users cu WHERE cu.login_id=".$db->qstr($this->login_id);
      			// die($sql_areas);
      	$db->Execute($sql_areas);
		//end added by Mark Guerra 3/12/2018
		return $this->Transact($this->sql);
	}
	/**
	*  Locks access permission of the user
	*
	* @public
	* @return boolean
	*/
	function Lock($reason='', $otherReason=''){
		return $this->_changelock(1, $reason, $otherReason);
	}
	/**
	*  UNlocks access permission of the user
	*
	* @public
	* @return boolean
	*/
	function UnLock(){
		return $this->_changelock(0, null, null);
	}
	/**
	*  Deletes the user if exists based on his username (login id)
	*
	* @public
	* @param string Username or login id
	* @return mixed adodb record or boolean FALSE
	*/
	function Delete($login_id){
		global $db;
		if(!empty($login_id)) $this->login_id=$login_id;
			elseif(empty($this->login_id)) return FALSE;

		$this->sql="DELETE FROM $this->tb_user  WHERE login_id='$this->login_id'";

		if ($this->Transact($this->sql)) {
			$this->user = NULL;
			$this->allowedareas = NULL;
			$this->usr_status=FALSE;
			$this->pw_status=FALSE;
			$this->lock_status=FALSE;
			return TRUE;
		} else {
			return FALSE;
		}
	}
    
    // Added by LST to be able to get the department where login id is assigned ...
    // 12.08.2008 
    function getDeptNr($slogin_id) {
        global $db;
        
        $this->sql = "select location_nr from care_personell_assignment as cpa ".
                     "   where exists(select login_id from care_users as cu ".
                     "   where cu.personell_nr = cpa.personell_nr and cu.login_id = '$slogin_id') ".
                     "   order by nr desc limit 1";
        if ($this->result=$db->Execute($this->sql)){
            if ($this->result->RecordCount()) {
                $row = $this->result->FetchRow();
                return $row["location_nr"];
            }                
            else
                return FALSE;
        }else{
            return FALSE;
        }                                     
    }    
    
    function getPersonellNr($slogin_id) {
        global $db;
        
        $this->sql = "select personell_nr from care_users as cu ".
                     "   where cu.login_id = '$slogin_id')";
        if ($this->result=$db->Execute($this->sql)){
            if ($this->result->RecordCount()) {
                $row = $this->result->FetchRow();
                return $row["personell_nr"];
            }                
            else
                return FALSE;
        }else{
            return FALSE;
        }   
    }

    //Added by Arvin 04/17/2018
    function getLockReason() {
        global $db;
     
        return $db->GetAll("SELECT * FROM care_lock_access_reason");
    }

    function insertSession($personnel_nr, $action, $is_deleted = 0){
    	global $db;
    	// $checkLogout = "SELECT "
    	// var_dump($this->checkActiveLogout($personnel_nr));die;
    	if($action == 'login'){
    		if ($this->checkActiveLogout($personnel_nr) > 0) {
    			$this->updateLogoutSession($personnel_nr);
    		}
    	}
    	$sql_session = "INSERT INTO care_session3 (action, personnel_nr, created_at, updated_at, is_deleted)
				VALUES (".$db->qstr($action).", ".$db->qstr($personnel_nr) .", ". $db->qstr(date('YmdHis')) .", ".$db->qstr(date('YmdHis')).", ".$db->qstr($is_deleted).")";
		$db->Execute($sql_session);

    }

    function updateLogoutSession($personnel_nr){
    	global $db;

    	$sql_session="UPDATE care_session3 SET is_deleted = '1' WHERE personnel_nr=".$db->qstr($personnel_nr)."AND id = ".$db->qstr($this->session_id);
		$db->Execute($sql_session);

    }
    function checkActiveLogout($personnel_nr){
		global $db;
	    $sql_checkActiveLogout ="SELECT * FROM care_session3  WHERE personnel_nr = ".$db->qstr($personnel_nr)." AND is_deleted = '0' AND action = 'logout' ORDER BY id DESC LIMIT 1";
		$result=$db->Execute($sql_checkActiveLogout);
		$count = $result->RecordCount();

		if ($count > 0) {
			$row = $result->FetchRow();
			$this->session_id = $row['id'];
			return $count;
		}
		return 0;
		
    }

	public function modifyAccessArray( array $array, $key, array $new ) {
		$keys = array_keys( $array );
		$index = array_search( $key, $keys );
		$pos = false === $index ? count( $array ) : $index + 1;
		
		return array_merge( array_slice( $array, 0, $pos ), $new, array_slice( $array, $pos ) );
	}
}

?>
