<?php 

  function redirect_to ($new_location) {
	header("Location: " . $new_location);
	exit;
  }


 //  function  mysql_prep($string) {
	// global $db;

	// $escaped_string = mysqli_real_escape_string ($db, $string);
	// return $escaped_string;
 //  }


  function confirm_query($result_set) {
		if (!$result_set) {
			die ("Database query failed.");
		}
  }


  function display_all_note() {
	global $db;
	$note_set=$db->Execute("SELECT * FROM seg_notice_tbl WHERE is_deleted = 1 ORDER BY note_date DESC ");
	confirm_query($note_set);
	return $note_set;
  }

  function display_all_active_meeting() {
	global $db;
	$note_type = "Meeting";

	$active_note=$db->Execute("SELECT * FROM seg_notice_tbl WHERE status = 1 AND is_deleted = 1 AND category = '$note_type' ORDER BY note_date DESC");
	confirm_query($active_note);
	return $active_note;
  }

  function display_all_active_orientation() {
	global $db;
	$note_type = "Orientation";

	$active_note=$db->Execute("SELECT * FROM seg_notice_tbl WHERE status = 1 AND is_deleted = 1 AND category = '$note_type' ORDER BY note_date DESC");
	confirm_query($active_note);
	return $active_note;
  }
  
  //Code Need to change
  function update_note_by_id($note_id) {
	global $db;

	/*$safe_note_id = mysqli_real_escape_string($connection,$note_id);*/


	/*$note_set = mysqli_query($connection,"SELECT * FROM seg_notice_tbl WHERE note_id = {$safe_note_id} LIMIT 1");*/
	$sql = "SELECT * FROM seg_notice_tbl WHERE note_id = ". $db->qstr($note_id) ." LIMIT 1";
	/*var_dump($sql);die;*/
	/*$query .= "FROM seg_notice_tbl ";
	$query .= "WHERE note_id = {$safe_note_id} ";
	$query .= "LIMIT 1";
	$note_set = mysqli_query($connection, $query);*/
	/*confirm_query($note_set);*/
	if ($result=$db->Execute($sql)){
						#$this->count=$this->result->RecordCount();

						if ($result->RecordCount())
								return $result->FetchRow();

						else
								return FALSE;
				}else{
						return FALSE;
				}
  }

 function select_note_by_id($note_id) {
	global $db;

	/*$safe_note_id = mysqli_real_escape_string($connection,$note_id);*/


	/*$note_set = mysqli_query($connection,"SELECT * FROM seg_notice_tbl WHERE note_id = {$safe_note_id} LIMIT 1");*/
	$sql = "SELECT * FROM seg_notice_tbl WHERE note_id = ". $db->qstr($note_id) ." LIMIT 1";
	/*var_dump($sql);die;*/
	/*$query .= "FROM seg_notice_tbl ";
	$query .= "WHERE note_id = {$safe_note_id} ";
	$query .= "LIMIT 1";
	$note_set = mysqli_query($connection, $query);*/
	/*confirm_query($note_set);*/
	if ($result=$db->Execute($sql)){
						#$this->count=$this->result->RecordCount();

						if ($result->RecordCount())
								return $result->FetchRow();

						else
								return FALSE;
				}else{
						return FALSE;
				}
  }


  function print_note_by_id($note_id) {
	global $db;

	/*$safe_note_id = mysqli_real_escape_string($db,$note_id);*/

	/*$note_set=$db->Execute("SELECT * FROM seg_notice_tbl WHERE note_id = {$safe_note_id} LIMIT 1 ");*/
	$sql = "SELECT * FROM seg_notice_tbl WHERE note_id = ". $db->qstr($note_id) ." LIMIT 1";
	/*$query .= "FROM seg_notice_tbl ";
	$query .= "WHERE note_id = {$safe_note_id} ";
	$query .= "LIMIT 1";*/
	/*$note_set = mysqli_query($db, $query);
	confirm_query($note_set);*/
	if ($result=$db->Execute($sql)){
						#$this->count=$this->result->RecordCount();

						if ($result->RecordCount())
								return $result->FetchRow();

						else
								return FALSE;
				}else{
						return FALSE;
				}
  }





?>





