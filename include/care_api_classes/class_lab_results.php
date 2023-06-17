<?php

/**
* Class for adding, editing, deleting and retrieving lab results.
* Created 11-11-2008 by Lorraine Raissa T. Yu
*/

require('./roots.php');

require_once($root_path.'include/care_api_classes/class_core.php');

Class Lab_Results extends Core {

		/**
		* SQL query result. Resulting ADODB record object.
		* @var object
		*/
		var $result;

		/**
		* table name
		* @var string
		*/
		var $tb_name="seg_lab_result";

		/**
		* Status items used in sql queries "IN (???)"
		* @var string
		* @access private
		*/
		var $dead_stat="'deleted','hidden','inactive','void'";

		/**
		* Constructor
		* @param
		*/
		function Lab_Results()
		{
		}

		/*
		* Generic Function for executing sql query
		*
		* @param $sql
		*/
		function exec_query($sql)
		{
				global $db;
				if($this->result = $db->Execute($sql))
				{
						if($this->result->RecordCount())
								return $this->result;
						else
								return FALSE;
				}
				else
						return FALSE;
		}

		/*
		* Function for retrieving system
		*
		* @param
		*/
		function get_system()
		{
				global $db;
				$this->sql = "SELECT value FROM care_config_global WHERE type='lab_result_default_unit';";
																if($this->result = $db->Execute($this->sql))
																{
																	 if($val=$this->result->FetchRow())
																			return $val["value"];
																	 else
																			return FALSE;
																}
																else
																	return FALSE;
		}

		/*
		* Function for retrieving service name
		*
		* @param $service_code
		*/
		function get_group_name($group_id)
		{
				global $db;
				$this->sql = "SELECT name FROM seg_lab_result_groupname WHERE group_id='$group_id'";
				if($this->result = $db->Execute($this->sql))
				{
						if($val=$this->result->FetchRow())
								return $val["name"];
						else
								return FALSE;
				}
				else
						return FALSE;
		}

		 function get_service_name($service_code)
		 {

				global $db;
				$this->sql = "SELECT name FROM seg_lab_services WHERE service_code='".$service_code."'";
				if($this->result = $db->Execute($this->sql))
				{
						/*if($this->result->RecordCount())
								return $this->result;
						else
								return FALSE;*/
						if($val=$this->result->FetchRow())
								return $val["name"];
						else
								return FALSE;
				}
				else{
					echo "error:".$db->ErrorMsg();
					$this->error_msg = $db->ErrorMsg();
						return FALSE;
				}
		}

		/*
		* Function for retrieving lab result data
		*
		* @param $refNo, $service_code
		*/
		function get_lab_results($refno, $service_code, $param_id)
		{
				global $db;
				$this->sql = "SELECT result_value, name, unit, seg_lab_result.si_unit, seg_lab_result.si_lo_normal, \n".
							"seg_lab_result.si_hi_normal, seg_lab_result.cu_unit, seg_lab_result.cu_lo_normal, seg_lab_result.cu_hi_normal \n".
							" FROM $this->tb_name, seg_lab_result_params WHERE seg_lab_result.refno='$refno' AND \n".
							"seg_lab_result.service_code='$service_code' AND seg_lab_result_params.param_id=$this->tb_name.param_id \n".
							"AND seg_lab_result_params.param_id='$param_id' AND (ISNULL($this->tb_name.`status`) \n".
							"OR $this->tb_name.`status`!='deleted');";

				if($this->result = $db->Execute($this->sql))
				{
						if($this->result->RecordCount())
								return $this->result;
						else
								return FALSE;
				}
				else
						return FALSE;
		}

		/*
		* Function for retrieving group lab result data
		*
		* @param $refNo, $service_code
		*/
		function get_group_lab_results($refno, $service_code, $param_id)
		{
				global $db;
				//revised by cha, july 17, 2010
				$this->sql = "SELECT result_value, name, unit
												FROM $this->tb_name as r
												LEFT JOIN seg_lab_result_param_assignment pa ON pa.param_id=r.param_id
												LEFT JOIN seg_lab_result_params AS p ON p.param_id = pa.param_id
												LEFT JOIN seg_lab_result_groupparams as gp ON gp.service_code=pa.service_code
												WHERE r.refno='$refno'
												AND pa.param_id='$param_id'
												AND p.status <> 'deleted';";
				if($this->result = $db->Execute($this->sql))
				{
						if($this->count = $this->result->RecordCount())
								return $this->result;
						else
								return FALSE;
				}
				else
						return FALSE;
		}

		/*
		* Function for retrieving parameter ID
		*
		* @param $param_Name
		*/
		function get_param_id($param_Name='', $scode='')
		{
				global $db;
				//revised by cha, july 17, 2010
				$this->sql = "SELECT pa.param_id FROM seg_lab_result_param_assignment pa LEFT JOIN seg_lab_result_params \n".
				"AS p ON pa.param_id=p.param_id WHERE p.name='$param_Name' AND pa.service_code='$scode' AND p.status <> 'deleted';";
				if($this->result && $this->result = $db->Execute($this->sql))
				{
						//echo $this->sql;
						if($this->count = $this->result->RecordCount())
								return $this->result->FetchRow();
						else
								return FALSE;
				}
				else
						return FALSE;
		}

		/*
		* Function for retrieving group parameter ID
		*
		* @param $param_Name
		*/
		function get_group_param_id($param_Name='', $scode='')
		{
				global $db;
				//revised by cha, july 17, 2010
				$this->sql = "SELECT p.param_id
												FROM seg_lab_result_groupparams as gp
												LEFT JOIN seg_lab_result_param_assignment pa ON gp.service_code=pa.service_code
												LEFT JOIN seg_lab_result_params as p ON pa.param_id=p.param_id
												WHERE p.name='$param_Name' AND pa.service_code='$scode' AND p.status <> 'deleted');";
				if($this->result = $db->Execute($this->sql))
				{
						//echo $this->sql;
						if($this->count = $this->result->RecordCount())
								return $this->result->FetchRow();
						else
								return FALSE;
				}
				else
						return FALSE;
		}

		/*
		* Function for retrieving patient data
		*
		* @param $refNo, $service_code
		*/
		#commented and edited by VAN 12-09-2008
		/*
		function get_patient_data($pid, $refno, $service_code)
		{
				global $db;

				$ret_array = array("name" => "", "address" => "", "sex" => "", "age" => "", "ward" => "", "physician" => "");

				$this->sql = "SELECT encounter_nr FROM seg_lab_serv WHERE refno='$refno';";
				$result = $db->Execute($this->sql);
				if($result!=NULL && $val = $result->FetchRow())
				{
						$encounter_nr = $val["encounter_nr"];
				}

				$this->sql = "SELECT CONCAT(IF(ISNULL(name_first), '', CONCAT(name_first, ' ')), IF(ISNULL(name_middle), '', CONCAT(name_middle, '. ')), IF(ISNULL(name_last), '', name_last)) as name, CONCAT(IF(ISNULL(street_name), '', CONCAT(street_name, ', ')), IF(ISNULL(brgy_name), '', CONCAT(brgy_name, ', ')), IF(ISNULL(mun_name), '', CONCAT(mun_name, ' ')), IF(ISNULL(zipcode), '', zipcode)) as address, sex, age FROM care_person, seg_barangays, seg_municity WHERE pid='$pid' AND (care_person.mun_nr = seg_barangays.mun_nr AND care_person.brgy_nr = seg_barangays.brgy_nr) AND (care_person.mun_nr = seg_municity.mun_nr);";
				$result = $db->Execute($this->sql);
				if($val = $result->FetchRow())
				{
						$ret_array["name"] = $val["name"];
						$ret_array["address"] = $val["address"];
						$ret_array["sex"] = $val["sex"];
						$ret_array["age"] = $val["age"];
				}

				$this->sql = "SELECT name as ward FROM care_encounter, care_ward WHERE encounter_nr='$encounter_nr' AND care_ward.nr=care_encounter.current_ward_nr;";
				$result = $db->Execute($this->sql);
				if($val = $result->FetchRow())
				{
						$ret_array["ward"] = $val["ward"];
				}

				$this->sql = "SELECT CONCAT(IF(ISNULL(name_first), '', CONCAT(name_first, ' ')), IF(ISNULL(name_middle), '', CONCAT(name_middle, '. ')), IF(ISNULL(name_last), '', name_last)) as physician FROM seg_lab_servdetails, care_personell, care_person WHERE refno='$refno' AND service_code='$service_code' AND seg_lab_servdetails.request_doctor=care_personell.nr AND care_personell.pid=care_person.pid;";
				$result = $db->Execute($this->sql);
				if($val = $result->FetchRow())
				{
						$ret_array["physician"] = $val["physician"];
				}
				return $ret_array;
		}
		 */

		 #edited by VAN 04-14-2010
		 function get_patient_data($refno, $group_id){
				global $db;

				$this->sql="SELECT ls.*, cp.*, ld.*,
												sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name,
												e.current_ward_nr, e.current_room_nr, e.current_dept_nr, e.encounter_type,
												IF(fn_calculate_age(NOW(),cp.date_birth),fn_get_age(NOW(),cp.date_birth),age) AS age
												FROM seg_lab_serv AS ls
												INNER JOIN seg_lab_servdetails AS ld ON ld.refno=ls.refno
												INNER JOIN care_person AS cp ON cp.pid=ls.pid
												LEFT JOIN care_encounter AS e ON e.encounter_nr=ls.encounter_nr
												LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=cp.brgy_nr
												LEFT JOIN seg_municity AS sm ON sm.mun_nr=cp.mun_nr
												LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
												LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
												WHERE ld.refno='$refno'
												AND (ISNULL(ls.status) OR ls.status!='deleted')
												AND (ISNULL(ld.status) OR ld.status!='deleted')";
				#echo $this->sql;
				if ($this->result=$db->Execute($this->sql)) {
						$this->count=$this->result->RecordCount();
						return $this->result->FetchRow();
				} else{
					 return FALSE;
				}
		}
		function getLabResult($refno, $group_id) {
				global $db;

				$this->sql="select refno, group_id, is_confidential from seg_lab_resultdata where refno='$refno' AND group_id='$group_id' AND (ISNULL(status) OR status!='deleted');";
								#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
				if ($this->count=$this->result->RecordCount()){
								# $this->rec_count=$this->dept_count;
					return $this->result->FetchRow();
						}else{
								return FALSE;
						}
				}else{
					return FALSE;
				}
		}
		/*
		* Function for adding lab result data
		*
		* @param $refNo, $service_code, $data
		*/
								#modified by cha, july 6, 2010
		function add_lab_results ($data, $refno, $gender, $group_id, $service_code='')
		{
				global $db;
				$lo_normal="";
				$hi_normal="";
				$unit ="";

				$bSuccess=TRUE;
				$db->StartTrans();

				if($group_id=="")
						$group_id=0;

				if(!empty($data))
				{
					for($ctr= 0; !empty($data[0][$ctr]) && $bSuccess; $ctr++)
					{
							//modified by ch, july 6, 2010
						/*if($service_code)
									$this->sql = "SELECT param_id FROM seg_lab_result_params WHERE name='" .$data[0][$ctr] ."' AND group_id=0 AND $gender=1 AND (ISNULL(`status`) OR `status`!='deleted') AND service_code='$service_code'";
							else
									$this->sql = "SELECT param_id FROM seg_lab_result_params WHERE name='" .$data[0][$ctr] ."' AND group_id=$group_id AND $gender=1 AND (ISNULL(`status`) OR `status`!='deleted')";*/
							//$this->sql = "SELECT param_id FROM seg_lab_result_params WHERE name='" .$data[0][$ctr] ."' AND $gender=1 AND (ISNULL(`status`) OR `status`!='deleted')";

							//revised by cha, july 17, 2010
						$this->sql = "SELECT pa.param_id FROM seg_lab_result_param_assignment pa LEFT JOIN seg_lab_result_params p \n".
						"ON pa.param_id=p.param_id WHERE p.name='".$data[0][$ctr]."' AND $gender=1 AND p.status <> 'deleted' ";

							if($service_code){
								$this->sql.=" AND service_code='$service_code'";
							}
							if($group_id){
								$this->sql.=" AND group_id=$group_id ";
							}

							//$result = $db->Execute($sql);
							//if($result!=NULL && $var = $result->FetchRow()){
							if($param_id = $db->GetOne($this->sql)){
									$history = "Added ".date('Y-m-d H:i:s')." ".$_SESSION['sess_user_name']."\n";
									$this->sql = "INSERT INTO $this->tb_name(refno, param_id, result_value, unit, status)
													VALUES('$refno', '". $param_id ."', '". $data[1][$ctr] ."', '". $data[2][$ctr] ."', '')";
									if ($this->result=$db->Execute($this->sql)) {
										if($db->Affected_Rows()>0)
										{
											$bSuccess = TRUE;
										}
									}else
									{
										//$error = $db->ErrorMsg();
										$this->error_msg = $db->ErrorMsg();
										$bSuccess = FALSE;
									}

							}
							else{
									$bSuccess = FALSE;
							}
					}
				}
				if (!$bSuccess) $db->FailTrans();
				$db->CompleteTrans();

				return $bSuccess;
		}

		/*
		* Function for adding date, med tech and pathologist
		*
		* @param $refNo, $service_code, $date, $med_tech, $pathologist
		*/
								#modified by cha, july 6, 2010
		function add_lab_resultdata ($refno, $group_id=0, $date, $med_tech, $pathologist, $is_confidential, $service_code='')
		{
				if($service_code=='')
						$service_code=0;
				global $db;
				//$history = "Added ".date('Y-m-d H:i:s')." ".$_SESSION['sess_user_name']."\n";
				$history = "CONCAT('Added ', NOW(), '".$_SESSION['sess_user_name']."')";
				//$this->sql = "INSERT INTO seg_lab_resultdata(refno, group_id, service_code, service_date, pathologist_pid, med_tech_pid, history, modify_id, modify_dt, create_id, create_dt, status, is_confidential) VALUES('$refno', '$group_id', '$service_code', '".date('Y-m-d H:i:s')."', '$pathologist', '$med_tech', '$history', '".$_SESSION['sess_user_name']."', '".date('Y-m-d H:i:s')."', '".$_SESSION['sess_user_name']."', '".date('Y-m-d H:i:s')."', '', $is_confidential);";
				$this->sql = "INSERT INTO seg_lab_resultdata(refno, group_id, service_code, service_date, pathologist_pid, med_tech_pid, history, modify_id, modify_dt, create_id, create_dt, status, is_confidential) VALUES('$refno', '$group_id', '$service_code', NOW(), '$pathologist', '$med_tech', ".$history.", '".$_SESSION['sess_user_name']."', NOW(), '".$_SESSION['sess_user_name']."', NOW(), '', $is_confidential);";
				//$error = $db->ErrorMsg();

				if($result = $db->Execute($this->sql))
				{
						if($db->Affected_Rows()>0)
						{
							return TRUE;
						}
				}
				else
				{
					$this->error_msg = $db->ErrorMsg();
					return FALSE;
				}
		}

		/*
		* Function for updating lab result data
		*
		* @param $refNo, $service_code, $data
		*/
								#modified by cha, july 6, 2010
		function update_lab_results ($data, $refno, $gender,$group_id,$service_code='')
		{
				global $db;
				$bSuccess=TRUE;
				$db->StartTrans();

				for($ctr= 0; !empty($data[0][$ctr]) && $bSuccess; $ctr++)
				{
						/*if($service_code)
								$this->sql = "SELECT param_id FROM seg_lab_result_params WHERE name='" .$data[0][$ctr] ."' AND group_id=0 AND $gender=1 AND (ISNULL(`status`) OR `status`!='deleted') AND service_code='$service_code'";
						else
								$this->sql = "SELECT param_id FROM seg_lab_result_params WHERE name='" .$data[0][$ctr] ."' AND group_id=$group_id AND $gender=1 AND (ISNULL(`status`) OR `status`!='deleted')";*/
						//$this->sql = "SELECT param_id FROM seg_lab_result_params WHERE name='" .$data[0][$ctr] ."' AND $gender=1 AND (ISNULL(`status`) OR `status`!='deleted')";

						//revised by cha, july 17, 2010
						$this->sql = "SELECT pa.param_id FROM seg_lab_result_param_assignment pa LEFT JOIN seg_lab_result_params p \n".
						"ON pa.param_id=p.param_id WHERE p.name='".$data[0][$ctr]."' AND $gender=1 AND p.status <> 'deleted' ";

						if($service_code){
							$this->sql.=" AND pa.service_code='$service_code'";
						}
						if($group_id){
							$this->sql.=" AND p.group_id=$group_id ";
						}

						$result = $this->exec_query($this->sql);
						if($result && $var = $result->FetchRow()){
								#echo $sql;
								$this->sql="UPDATE $this->tb_name ".
												" SET result_value='". $data[1][$ctr] ."', unit='".$data[2][$ctr]."' ".
												" WHERE refno = '$refno' AND param_id='". $var["param_id"] .
												"' AND (ISNULL(status) OR status!='deleted')";
								#echo "<br>".$sql;
								$result = $db->Execute($this->sql);
								if($result){
										$bSuccess = TRUE;
								}
						}
				}

				if (!$bSuccess) $db->FailTrans();
				$db->CompleteTrans();

				return $bSuccess;
		}

		/*
		* Function for updating date, med tech and pathologist
		*
		* @param $refNo, $service_code, $date, $med_tech, $pathologist
		*/
		#modified by cha, july 6, 2010
		function update_lab_resultdata ($refno, $group_id, $date, $med_tech, $pathologist, $is_confidential, $service_code='')
		{
				if($service_code=='')
						$service_code=0;
				global $db;
				$this->sql = "SELECT history FROM seg_lab_resultdata WHERE refno='$refno' AND group_id='". $group_id ."' AND service_code='". $service_code ."' AND (ISNULL(status) OR status!='deleted')";
				$result = $db->Execute($this->sql);
				if($result!="" && $var2 = $result->FetchRow())
						$history = $var2["history"] ."Updated ".date('Y-m-d H:i:s')." ".$_SESSION['sess_user_name']."\n";
				else
						$history = "Updated ".date('Y-m-d H:i:s')." ".$_SESSION['sess_user_name']."\n";
				$this->sql = "UPDATE seg_lab_resultdata SET service_date='$date', pathologist_pid='$pathologist', med_tech_pid='$med_tech', history='$history', modify_id='".$_SESSION['sess_user_name']."', modify_dt='".date('Y-m-d H:i:s')."', is_confidential=$is_confidential WHERE refno='$refno' AND group_id='$group_id' AND service_code='". $service_code ."' AND (ISNULL(status) OR status!='deleted')";
				if($result = $db->Execute($this->sql))
				{
						return TRUE;
				}else
				{
					$this->error_msg = $db->ErrorMsg();
					return FALSE;
				}
		}

		/*
		* Function for logically deleting lab result data
		*
		* @param $refNo, $service_code
		*/
		function delete_lab_results ($refno, $group_id, $gender)
		{
				global $db;
				$bSuccess=TRUE;
				$db->StartTrans();

				//revised by cha, july 17, 2010
				$sql = "SELECT pa.param_id
										FROM seg_lab_result_groupparams as gp
										LEFT JOIN seg_lab_result_param_assignment pa ON pa.service_code=gp.service_code
										LEFT JOIN seg_lab_result_params as p ON pa.param_id=p.param_id
										WHERE gp.group_id=$group_id AND $gender=1
										ORDER BY gp.order_nr, pa.order_nr ASC";
				$rs = $db->Execute($sql);
				while($rs!=NULL && $v = $rs->FetchRow()){
						$sql = "UPDATE seg_lab_result SET `status`='deleted' WHERE refno='$refno' AND param_id='".$v["param_id"]."' AND (ISNULL(status) OR status!='deleted')";
						$result = $db->Execute($sql);
						$error = $db->ErrorMsg();
						if($result)
								$bSuccess = TRUE;
						else{
								$bSuccess = FALSE;
								break;
						}
				}

				if (!$bSuccess) $db->FailTrans();
				$db->CompleteTrans();

				return $bSuccess;
		}

		function delete_lab_resultdata ($refno, $group_id, $reason)
		{
				global $db;

				$sql = "SELECT history FROM seg_lab_resultdata WHERE refno='$refno' AND group_id='$group_id' AND (ISNULL(status) OR status!='deleted')";
				$result = $db->Execute($sql);
				if($result!="" && $var2 = $result->FetchRow())
						$history = $var2["history"] ."Deleted ".date('Y-m-d H:i:s')." ".$_SESSION['sess_user_name']."\n";
				else
						$history = "Deleted ".date('Y-m-d H:i:s')." ".$_SESSION['sess_user_name']."\n";
				$sql = "UPDATE seg_lab_resultdata SET `status`='deleted', history='$history', modify_id='".$_SESSION['sess_user_name']."', modify_dt='".date('Y-m-d H:i:s')."', cancel_reason='".$reason."' WHERE refno='$refno' AND group_id='$group_id' AND (ISNULL(status) OR status!='deleted')";
				$result = $db->Execute($sql);
				$error = $db->ErrorMsg();
				if($result)
						return TRUE;
				else
						return FALSE;
		}

		function deleteTestGroup($group_id){
				global $db;

				$this->sql = "UPDATE seg_lab_result_groupparams SET status='deleted' WHERE group_id='$group_id'";
				$rs = $db->Execute($this->sql);
				if($rs){
						$this->sql = "UPDATE seg_lab_result_groupname SET status='deleted' WHERE group_id='$group_id'";
						$rs = $db->Execute($this->sql);
						if($rs)
								return TRUE;
				}
				return false;
		}

		function countSearchTestGroups($searchkey='',$maxcount=100,$offset=0) {
				global $db, $sql_LIKE, $root_path, $date_format;
				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;

				# convert * and ? to % and &
				$searchkey=strtr($searchkey,'*?','%_');
				$searchkey=trim($searchkey);
				#$suchwort=$searchkey;
				$searchkey = str_replace("^","'",$searchkey);
				$keyword=addslashes($searchkey);

				$this->sql = "SELECT group_id, name
												FROM seg_lab_result_groupname AS gn
												WHERE (ISNULL(gn.status) OR gn.status!='deleted')";
				if($searchkey!='')
						$this->sql .= " AND (gn.group_id LIKE '%$searchkey%' OR gn.name LIKE '%$searchkey%')";
				$this->sql .= " AND (ISNULL(status) OR status!='deleted')";

				#echo "sql = ".$this->sql;
				if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()) {
								return $this->result;
						}
						else{return FALSE;}
				}else{return FALSE;}
		}

		function SearchTestGroups($searchkey='',$maxcount=100,$offset=0){
				global $db, $sql_LIKE, $root_path, $date_format;
				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;

				# convert * and ? to % and &
				$searchkey=strtr($searchkey,'*?','%_');
				$searchkey=trim($searchkey);
				#$suchwort=$searchkey;
				$searchkey = str_replace("^","'",$searchkey);
				$keyword=addslashes($searchkey);

				$this->sql = "SELECT group_id, name
												FROM seg_lab_result_groupname AS gn
												WHERE (ISNULL(gn.status) OR gn.status!='deleted')";
				if($searchkey!='')
						$this->sql .= " AND (gn.group_id LIKE '%$searchkey%' OR gn.name LIKE '%$searchkey%')";
				$this->sql .= " AND (ISNULL(status) OR status!='deleted')";

				if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
						if($this->rec_count=$this->res['ssl']->RecordCount()) {
								return $this->res['ssl'];
						}else{return false;}
				}else{return false;}
		}

		//function addGroup($group_id, $name){ #comment out by cha, june 23, 2010
		function addGroup($name){
				global $db;

				#comment out by cha, june 23, 2010
				//$this->sql = "INSERT INTO seg_lab_result_groupname (group_id, name, create_id, create_time) VALUES($group_id, '$name', '".$_SESSION['sess_user_name']."', '".date('Y-m-d H:i:s')."')";
				$this->sql = "INSERT INTO seg_lab_result_groupname (name, create_id, create_time) VALUES('$name', '".$_SESSION['sess_user_name']."', '".date('Y-m-d H:i:s')."')";
				if ($db->Execute($this->sql)) {
					if ($db->Affected_Rows()) {
						return $db->Insert_ID();
					}
				}
				return FALSE;
		}

		function addParamToGroup($group_id, $service_code, $order_nr){
				global $db;

				$this->sql = "INSERT INTO seg_lab_result_groupparams (group_id, service_code, order_nr, create_id, create_time) VALUES($group_id, '$service_code', $order_nr, '".$_SESSION['sess_user_name']."', '".date('Y-m-d H:i:s')."')";
				$result = $db->Execute($this->sql);
				if($result)
						return true;
				else
						return false;
		}

		function getGroupName($group_id){
				global $db;

				$this->sql = "SELECT gn.name FROM seg_lab_result_groupname AS gn WHERE gn.group_id=".$group_id;
				$result = $db->Execute($this->sql);
				if($result && $row = $result->FetchRow())
						return $row['name'];
				else
						return false;
		}

		function getServiceName($service_code){
				global $db;

				$this->sql = "SELECT name FROM seg_lab_services WHERE service_code='".$service_code."'";
				$result = $db->Execute($this->sql);
				if($result && $row = $result->FetchRow())
						return $row['name'];
				else
						return false;
		}

		#revised by cha, july 13, 2010
		function getGroupServices($group_id){
				global $db;

				$this->sql = "SELECT gp.service_code, s.name, gp.order_nr \n".
							",IF(EXISTS(SELECT pa.param_id FROM seg_lab_result_param_assignment pa \n".
								"WHERE pa.service_code=gp.service_code AND pa.is_copied='0'),1,0) AS `has_params` \n".
							"FROM seg_lab_result_groupparams AS gp LEFT JOIN seg_lab_services AS s ON \n".
							"s.service_code = gp.service_code WHERE gp.group_id=$group_id AND \n".
							"(ISNULL(gp.status) OR gp.status!='deleted') ORDER BY gp.order_nr ASC";
				$result = $db->Execute($this->sql);
				return $result;
		}

		function editGroup($group_id, $name){
				global $db;

				$this->sql = "UPDATE seg_lab_result_groupname SET name='$name', modify_id='".$_SESSION['sess_user_name']."', modify_time='".date('Y-m-d H:i:s')."' WHERE group_id=$group_id AND (ISNULL(status) OR status!='deleted')";
				$result = $db->Execute($this->sql);
				if($result)
						return true;
				else
						return false;
		}

		function deleteParamsFromGroup($group_id){
				global $db;

				$this->sql = "DELETE FROM seg_lab_result_groupparams WHERE group_id=$group_id AND (ISNULL(status) OR status!='deleted')";
				$result = $db->Execute($this->sql);
				if($result)
						return true;
				else
						return false;
		}

		function deleteGroup($group_id){
				global $db;

				$this->sql = "UPDATE seg_lab_result_groupparams SET status = 'deleted' WHERE group_id=$group_id";
				$result = $db->Execute($this->sql);
				if($result){
						$this->sql = "UPDATE seg_lab_result_groupname SET status = 'deleted' WHERE group_id=$group_id";
						$result = $db->Execute($this->sql);
						if($result)
								return true;
						else
								return false;
				}
				else
						return false;
		}

		function getLabTests(){
				global $db;

				$this->sql = "SELECT group_id, name FROM seg_lab_result_groupname WHERE status IS NULL OR status<>'deleted'
												UNION
												SELECT service_code AS group_id, name FROM seg_lab_services
												WHERE service_code NOT IN (SELECT service_code FROM seg_lab_result_groupparams)
												AND service_code NOT IN (SELECT service_code_child FROM seg_lab_result_group)
												AND (status IS NULL OR status<>'deleted')";
				$result = $db->Execute($this->sql);
				if($result){
						return $result;
				}
				else
						return false;
		}

		function getTestParams($group_id='', $service_code=''){
				global $db;

				//revised by cha, july 17, 2010
				if($group_id!=''){
					$cond = "AND p.group_id='$group_id' ";
				}
				if($service_code!="") {
					$cond.= " AND d.service_code='$service_code' ";
				}
				$this->sql = "SELECT * FROM \n".
											"(SELECT d.service_code,pa.param_id,gp.order_nr AS `group_order`, pa.order_nr AS `param_order`, \n".
											"p.name, p.param_group_id,pg.name AS `group_name`, p.is_numeric, p.is_boolean, p.is_longtext, p.SI_unit, p.SI_lo_normal, \n".
											"p.SI_hi_normal, p.CU_unit, p.CU_lo_normal, p.CU_hi_normal, p.is_female, p.is_male, p.is_time, p.is_multiple_choice, p.is_table, \n".
											"r.result_value, r.unit \n".
											"FROM seg_lab_result_params AS p \n".
											"LEFT JOIN seg_lab_result_param_assignment AS pa ON p.param_id=pa.param_id \n".
											"LEFT JOIN seg_lab_result_groupparams AS gp ON p.group_id=gp.group_id AND pa.service_code=gp.service_code \n".
											"LEFT JOIN seg_lab_result_paramgroups AS pg ON pg.param_group_id=p.param_group_id \n".
											"LEFT JOIN seg_lab_result AS r ON r.param_id=pa.param_id AND r.status <> 'deleted' \n".
											"LEFT JOIN seg_lab_servdetails AS d ON d.refno=r.refno AND d.service_code=pa.service_code \n".
											"WHERE p.status <> 'deleted' ".$cond." \n".
											" ORDER BY gp.order_nr, pa.order_nr) a \n".
									"GROUP BY a.param_id, a.param_group_id \n".
									"ORDER BY param_order,group_order";
				/*if($group_id==0 || $group_id==''){
						$this->sql = "SELECT p.*, s.name as group_name
														FROM seg_lab_result_params AS p
														LEFT JOIN seg_lab_services AS s ON s.service_code = p.service_code
														WHERE s.service_code='$service_code' ORDER BY p.order_nr ASC";
				}
				else{
						$this->sql = "SELECT p.group_id, p.param_id, p.service_code, p.short_name AS name, p.is_numeric, p.is_boolean, p.is_longtext, p.order_nr, p.param_group_id, p.is_female, p.is_male, pg.name as group_name, gp.order_nr as order2
														FROM seg_lab_result_groupparams as gp
														LEFT JOIN seg_lab_result_params as p ON p.service_code = gp.service_code
														LEFT JOIN seg_lab_result_paramgroups as pg ON pg.param_group_id = p.param_group_id
														WHERE gp.group_id=$group_id AND (ISNULL(p.status) OR p.status NOT IN ('deleted'))
														UNION SELECT p.group_id, p.param_id, p.service_code, p.short_name As name, p.is_numeric, p.is_boolean, p.is_longtext, p.order_nr, p.param_group_id, p.is_female, p.is_male, pg.name as group_name, gp.order_nr as order2
														FROM seg_lab_result_groupparams as gp
														INNER JOIN seg_lab_result_group as g ON g.service_code = gp.service_code
														LEFT JOIN seg_lab_result_params as p ON p.service_code = g.service_code_child
														LEFT JOIN seg_lab_result_paramgroups as pg ON pg.param_group_id = p.param_group_id
														WHERE gp.group_id=$group_id AND (ISNULL(p.status) OR p.status NOT IN ('deleted'))
														ORDER BY order2, order_nr ASC";
				}*/
				$result = $db->Execute($this->sql);
				if($result){
						return $result;
				}
				else
						return false;
		}

		function getAllResultsByTest($group_id='', $service_code='', $from_date='', $to_date='', $is_IPD=0){
				global $db;

				if($from_date=='')
						$from_date="NOW()";
				else
						$from_date="'$from_date 00:00:00'";

				if($to_date=='')
						$to_date="NOW()";
				else
						$to_date="'$to_date 23:59:59'";

				if($group_id==0 || $group_id==''){
						$this->sql = "SELECT DISTINCT rd.refno, rd.group_id, rd.service_code, rd.pathologist_pid, rd.med_tech_pid, rd.service_date, d.clinical_info, d.request_dept, d.is_in_house, s.encounter_nr, s.pid, s.ordername, s.orderaddress, s.loc_code, p.date_birth, p.sex, fn_calculate_age(p.date_birth, NOW()) as age, w.name AS location
														FROM seg_lab_resultdata AS rd
														RIGHT JOIN seg_lab_servdetails AS d ON d.refno = rd.refno AND d.status<>'deleted' AND d.is_in_house=$is_IPD
														INNER JOIN seg_lab_serv AS s ON s.refno = d.refno
														LEFT JOIN care_person AS p ON p.pid = s.pid
														LEFT JOIN care_ward As w ON w.nr = s.loc_code
														WHERE service_date <=$to_date AND service_date>=$from_date
														AND (rd.status IS NULL OR rd.status<>'deleted') AND rd.service_code='$service_code'
														ORDER BY service_date ASC";
				}
				else{
						if($is_IPD){
								$this->sql = "SELECT DISTINCT rd.refno, rd.group_id, rd.service_code, rd.pathologist_pid, rd.med_tech_pid, rd.service_date, d.clinical_info, d.request_dept, d.is_in_house, s.encounter_nr, s.pid, s.ordername, s.orderaddress, s.loc_code, p.date_birth, p.sex, fn_calculate_age(p.date_birth, NOW()) as age, w.name AS location
																FROM seg_lab_resultdata AS rd
																RIGHT JOIN seg_lab_servdetails AS d ON d.refno = rd.refno AND d.status<>'deleted' AND d.is_in_house=$is_IPD
																INNER JOIN seg_lab_serv AS s ON s.refno = d.refno
																LEFT JOIN care_person AS p ON p.pid = s.pid
																LEFT JOIN care_ward As w ON w.nr = s.loc_code
																WHERE service_date <=$to_date AND service_date>=$from_date
																AND (rd.status IS NULL OR rd.status<>'deleted') AND rd.group_id=$group_id
																ORDER BY service_date ASC";
						}
						else{
								$this->sql = "SELECT DISTINCT rd.refno, rd.group_id, rd.service_code, rd.pathologist_pid, rd.med_tech_pid, rd.service_date, d.clinical_info, d.request_dept, d.is_in_house, s.encounter_nr, s.pid, s.ordername, s.orderaddress, s.loc_code, p.date_birth, p.sex, fn_calculate_age(p.date_birth, NOW()) as age, IF(ISNULL(w.name), loc_code, w.name) AS location
																FROM seg_lab_resultdata AS rd
																RIGHT JOIN seg_lab_servdetails AS d ON d.refno = rd.refno AND d.status<>'deleted' AND d.is_in_house=$is_IPD
																INNER JOIN seg_lab_serv AS s ON s.refno = d.refno
																LEFT JOIN care_person AS p ON p.pid = s.pid
																LEFT JOIN care_ward As w ON w.nr = s.loc_code
																WHERE service_date <=$to_date AND service_date>=$from_date
																AND (rd.status IS NULL OR rd.status<>'deleted')
																AND rd.group_id=$group_id
																UNION
																SELECT DISTINCT rd.refno, rd.group_id, rd.service_code, rd.pathologist_pid, rd.med_tech_pid, rd.service_date, d.clinical_info, d.request_dept, d.is_in_house, s.encounter_nr, s.pid, s.ordername, s.orderaddress, s.loc_code, p.date_birth, p.sex, fn_calculate_age(p.date_birth, NOW()) as age, 'Walk-in' AS location
																FROM seg_lab_resultdata AS rd
																RIGHT JOIN seg_lab_servdetails AS d ON d.refno = rd.refno AND d.status<>'deleted' AND d.is_in_house=$is_IPD
																INNER JOIN seg_lab_serv AS s ON s.refno = d.refno
																LEFT JOIN seg_walkin AS p ON p.pid = s.pid
																WHERE service_date <=$to_date AND service_date>=$from_date
																AND (rd.status IS NULL OR rd.status<>'deleted') AND rd.group_id=$group_id AND loc_code='WIN'
																ORDER BY service_date ASC";
						}
				}
				$result = $db->Execute($this->sql);
				if($result){
						return $result;
				}
				else
						return false;
		}

		function getResultsByRefno($refno, $group_id='', $service_code=''){
				global $db;

				//revised by cha, july 17, 2010
				if($service_code==''){
						$this->sql = "SELECT r.param_id, r.refno, r.result_value FROM seg_lab_result AS r
														LEFT JOIN seg_lab_result_param_assignment pa ON r.param_id=pa.param_id
														LEFT JOIN seg_lab_result_params AS p ON p.param_id = pa.param_id
														WHERE r.refno='$refno' AND (r.status IS NULL OR r.status<>'deleted')
														AND p.group_id=$group_id";
				}
				elseif($group_id==''){
						$this->sql = "SELECT r.param_id, r.refno, r.result_value FROM seg_lab_result AS r
														LEFT JOIN seg_lab_result_param_assignment pa ON r.param_id=pa.param_id
														LEFT JOIN seg_lab_result_params AS p ON p.param_id = pa.param_id
														WHERE r.refno='$refno' AND (r.status IS NULL OR r.status<>'deleted')
														AND pa.service_code='$service_code'";
				}

				$result = $db->Execute($this->sql);

				if($result){
						return $result;
				}
				else
						return false;
		}
}
