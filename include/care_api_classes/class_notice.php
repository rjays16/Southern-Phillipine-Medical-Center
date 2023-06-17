<?php
/**
* @package care_api
*/
/**
*/
require_once($root_path.'include/care_api_classes/class_core.php');

class Notice extends Core {

var $tb_notice='seg_notice_tbl';
var $tb_acknow='seg_notice_acknledgmnts';
var $sql;

function insertNotice($noticeinfo){
		global $db;
		
		$this->sql="INSERT INTO $this->tb_notice (category, date_published, note_date, time_from, time_to, venue, subject, status, notice_attchmnt, is_deleted, date_created) VALUES (".$db->qstr($noticeinfo['category']).",".$db->qstr($noticeinfo['date_pub']).",".$db->qstr($noticeinfo['fr_date']).",".$db->qstr($noticeinfo['fro_time']).",".$db->qstr($noticeinfo['too_time']).",".$db->qstr($noticeinfo['venue']).",".$db->qstr($noticeinfo['subject']).",".$db->qstr($noticeinfo['status']).",".$db->qstr($noticeinfo['file_name']).",".$db->qstr($noticeinfo['is_deleted']).",".$db->qstr($noticeinfo['is_date']).") ";
		
		$stmt=$db->Execute($this->sql);

		return $stmt;
	}


function getAcknowledge($ackinfo){

global $db;
		$datenow = date('Y-m-d H:i:s');
	 	$this->sql="INSERT INTO $this->tb_acknow (sess_user, notice_id, date_ack, departmnt) 
	 			VALUES (".$db->qstr($ackinfo['sess_user']).",".$db->qstr($ackinfo['noteID']).",".$db->qstr($datenow).",".$db->qstr($ackinfo['depart']).")";

       $ack=$db->Execute($this->sql);

       return $ack;



}
	

}



?>