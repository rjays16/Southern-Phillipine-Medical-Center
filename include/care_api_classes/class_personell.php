<?php
/**
* @package care_api
*/
/**
*/
require('roots.php');
require_once($root_path.'include/care_api_classes/class_core.php');
include_once($root_path.'include/care_api_classes/class_department.php');   # burn added: July 19, 2007
include_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/care_api_classes/class_seg_dependents.php');
/**
*  Personnel methods.
*  Note this class should be instantiated only after a "$db" adodb  connector object  has been established by an adodb instance
* @author Elpidio Latorilla
* @version beta 2.0.1
* @copyright 2002,2003,2004,2005,2005 Elpidio Latorilla
* @package care_api
*/
class Personell extends Core {
		/**#@+
		* @access private
		*/
		/**
		* Table name for personnel data
		* @var string
		*/
		var $tb='care_personell';
		/**
		* Table name for personnel data
		* @var string
		*/
		var $tb_role_person='care_role_person';
		/**
		* Table name for personnel assignments
		* @var string
		*/
		var $tb_assign='care_personell_assignment';
		/**
		* Table name for departments
		* @var string
		*/
		var $tb_dept='care_department';
		/**
		* Table name for person registration data.
		* @var string
		*/
		var $tb_person='care_person';
		/**
		* Table name for person registration data.
		* @var string
		*/
		var $tb_users='care_users';


		/**
		* Table name for on-call duty plans
		* @var string
		*/
		var $tb_dpoc='care_dutyplan_oncall';
		/**
		* Table name for phone and contact information
		* @var string
		*/
		var $tb_cphone='care_phone';
		/**
		* Table name for city-town names
		* @var string
		*/
		var $tb_citytown='care_address_citytown';
		/**
		* Database table for the region address data.
		* @var string
		* burn added: May 28, 2007
		*/
		var $tb_regions='seg_regions';
		/**
		* Database table for the province address data.
		* @var string
		* burn added: May 28, 2007
		*/
		var $tb_provinces='seg_provinces';
		/**
		* Database table for the municipality/city address data.
		* @var string
		* burn added: May 28, 2007
		*/
		var $tb_municity='seg_municity';
		/**
		* Database table for the barangay address data.
		* @var string
		* burn added: May 28, 2007
		*/
		var $tb_barangays='seg_barangays';

        //added by jasper 02/06/13
        var $tb_doctor_role='seg_doctor_role';

		/**#@-*/
		/**
		* SQL query result buffer
		* @var adodb record object
		*/
		var $result;
		/**
		* Loaded data flag
		* @var boolean
		*/
		var $is_loaded='FALSE';
		/**
		* Resulting row buffer
		* @var array
		*/
		var $row;
		/**
		* Depatments data buffer
		* @var adodb record object
		*/
		var $depts;
		/**
		* Resulting rows count buffer
		* @var int
		*/
		var $record_count;
		/**
		* Personnel data buffer
		* @var adodb record object
		*/
		var $personell_data;

		/**
		* IPBM nr 
		* @var string
		*/
		
		var $IPBMdept_nr = '182';

		/**
		* Field names of care_dutyplan_oncall
		* @var array
		*/
		var $dpoc_fields=array('nr',
																		'dept_nr',
																		'role_nr',
																		'year',
																		'month',
																		'duty_1_txt',
																		'duty_2_txt',
																		'duty_1_pnr',
																		'duty_2_pnr',
																		'status',
																		'history',
																		'modify_id',
																		'modify_time',
																		'create_id',
																		'create_time');
		/**
		* Field names of care_personell_assignment
		* @var array
		*/
		var $assign_fields=array('nr',
																		'personell_nr',
																		'role_nr',
																		'location_type_nr',
																		'location_nr',
																		'date_start',
																		'date_end',
																		'is_temporary',
																		'list_frequency',
																		'status',
																		'history',
																		'modify_id',
																		'modify_time',
																		'create_id',
																		'create_time');
		/**
		* Field names of care_personell
		* @var array
		*/
		var $personell_fields=array('nr',
																		'short_id',
																		'pid',
																		'ris_id',
																		'job_type_nr',
																		'job_function_title',
																		'job_position',
																		'date_join',
																		'date_exit',
																		'contract_class',
																		'contract_start',
																		'contract_end',
																		'pay_class',
																		'pay_class_sub',
																		'local_premium_id',
																		'tax_account_nr',
																		'ir_code',
																		'nr_workday',
																		'nr_weekhour',
																		'nr_vacation_day',
																		'multiple_employer',
																		'nr_dependent',
																		'status',
																		'history',
																		'modify_id',
																		'modify_time',
																		'create_id',
																		'create_time',
																		'license_nr',
																		'prescription_license_nr',	//added by cha, august 17, 2010
																		'tin',
																		'is_resident_dr',
																		'tier_nr',
																		'id_nr', // added by: syboy; 05/14/2015; 5:20pm
																		'category', // added by: syboy; 05/15/2015
																		'job_status', // added by: syboy; 05/15/2015
																		'other_title',
																		'is_reliever',
                                                                        'ptr_nr',
                                                                        's2_nr',
                                                                        'doctor_role',//added by jasper 02/06/13
                                                                        'doctor_level', //added by Jarel 04/06/13
                                                                        'bio_nr'); # added by: syboy 01/18/2016 : meow
		/**
		* Constructor
		*/

		function getDepartmentGroup($dr_nr){
	    	global $db;
	    	$this->sql = "SELECT cd.name_formal as dr_department from care_department cd
	    					INNER JOIN care_personell_assignment cpa
	    						ON cpa.location_nr = cd.nr
	    					WHERE cpa.personell_nr = '$dr_nr'";
	    					// echo $this->sql; 
	   		if ($this->result = $db->Execute($this->sql)) {
					if ($this->result->RecordCount()){
							$row = $this->result->FetchRow();
							return $row['dr_department'];
					}
			}
	    	return false;
	    }

	    function getDepartmentGroupId($group_nr){
	    	global $db;
	    	$this->sql = "SELECT location_nr from care_personell_assignment WHERE personell_nr = '$group_nr'";
	   		if ($this->result = $db->Execute($this->sql)) {
					if ($this->result->RecordCount()){
							$row = $this->result->FetchRow();
							return $row['location_nr'];
					}
			}
	    	return false;
	    }


		function Personell(){
				$this->setTable($this->tb);
				$this->setRefArray($this->personell_fields);
		}
		/**
		* Sets the core object to point to the care_dutyplan_oncall table and field names.
		* @access public
		*/
		function useDutyplanTable(){
				$this->setTable($this->tb_dpoc);
				$this->setRefArray($this->dpoc_fields);
		}
		/**
		* Sets the core object to point to the care_personell_assignment table and field names.
		* @access public
		*/
		function useAssignmentTable(){
				$this->setTable($this->tb_assign);
				$this->setRefArray($this->assign_fields);
		}
		/**
		* Sets the core object to point to the care_personell table and field names.
		* @access public
		*/
		function usePersonellTable(){
				$this->setTable($this->tb);
				$this->setRefArray($this->personell_fields);
		}
		/**
		* Checks if the personnel (employee) number exists in the database.
		* @access public
		* @param int Personnel number
		* @return boolean
		*/
		function InitPersonellNrExists($init_nr){
				global $db;
				$this->sql="SELECT nr FROM $this->tb WHERE nr=$init_nr";
				if($this->result=$db->Execute($this->sql)){
						if($this->result->RecordCount()){
								return TRUE;
						} else { return FALSE; }
				} else { return FALSE; }
		}



		/**#@+
		*
		* The returned adodb record object contains rows of arrays.
		* Each array contains the personnel data with the following index keys:
		* - nr = record's primary key number
		* - personell_nr = personnel or employee number
		* - job_function_title = job function title or name
		* - name_last = employee's last or family name
		* - name_first = employee's first or given name
		* - date_birth = date of birth
		* - sex = sex
		* @return mixed adodb record object or boolean
		*/
		/**
		* Returns information of all nurses of a department.
		*
		* @access public
		* @param int Department number
		*/
		function getDoctorsOfDept($dept_nr=0){
				if(!$dept_nr) return FALSE;
						else return $this->_getAllPersonell(1,$dept_nr,"D"); // 1= dept (medical), D = doctor (role)
						#else return $this->_getAllPersonell(1,17,$dept_nr); // 1= dept (location), 17 = doctor (role)
		}
		/**
		* Returns information of all nurses of a department.
		*
		* @access public
		* @param int Department number
		*/
		function getNursesOfDept($dept_nr=0){

				if(!$dept_nr) return FALSE;
				else return $this->_getAllPersonell(1,$dept_nr,"N"); // 1= dept (location); N = nurse (role)
				#else return $this->_getAllPersonell(1,16,$dept_nr); // 1= dept (location); 16 = nurse (role)
		}



		/**
		* Returns  information of all personnel (employee) based on location type, role number and department number keys
		*
		* @access private
		* @param int Location type number
		* @param int Role number
		* @param int Department number
		*/
		#function _getAllPersonell($loc_type_nr,$role_nr=0,$dept_nr){
		function _getAllPersonell($loc_type_nr,$dept_nr,$role){
				global $db, $dbf_nodate;



#        include_once($root_path.'include/care_api_classes/class_department.php');
				$dept_obj=new Department;

				 $row=array();

				if (($loc_type_nr == 1) && ($role=="D")){
						#$cond = "%doctor%";
						 #$cond2 = "%surgeon%";
						 #$cond3 = "%anesthesiologist%";

#            $list = $this->getAncestorChildrenDept($dept_nr);   # burn added : May 31, 2007
						$list = $dept_obj->getAncestorChildrenDept($dept_nr);   # burn added : July 19, 2007
#echo "class_personell.php : _getAllPersonell : list = '".$list."'<br> \n";
#echo "class_personell.php : _getAllPersonell : list : <br> \n"; print_r($list); echo"<br> \n";
						$add_where="";
						if (!empty($list)){
								$add_where = " OR a.location_nr IN ($list) ";
						}
						#exit();
						$this->sql="SELECT a.nr, a.personell_nr, ps.job_type_nr, ps.job_function_title,ps.job_position,ps.license_nr,ps.tin, p.name_last, p.name_first, p.name_2, p.name_middle, p.date_birth, p.sex
										FROM     $this->tb_assign AS a,
														$this->tb AS ps,
														$this->tb_person AS p
										WHERE
												a.location_type_nr=$loc_type_nr
												AND (ps.short_id LIKE 'D%')
												AND (a.location_nr=$dept_nr $add_where)
												AND (a.date_end='$dbf_nodate' OR a.date_end>='".date('Y-m-d')."')
												AND a.status NOT IN ($this->dead_stat)
												AND a.personell_nr=ps.nr
												AND ps.pid=p.pid
										ORDER BY p.name_last, p.name_first, p.name_middle";
#                    ORDER BY a.list_frequency DESC";
				}elseif (($loc_type_nr == 1) && ($role=="N")){
						$cond = "%nurse%";

						$this->sql="SELECT a.nr, a.personell_nr, ps.job_function_title,ps.job_position,ps.license_nr,ps.tin, p.name_last, p.name_first, p.name_middle, p.date_birth, p.sex
										FROM     $this->tb_assign AS a,
														$this->tb AS ps,
														$this->tb_person AS p
										WHERE
												a.location_type_nr=$loc_type_nr
												AND (ps.short_id LIKE 'N%')
												AND a.location_nr=$dept_nr
												AND (a.date_end='$dbf_nodate' OR a.date_end>='".date('Y-m-d')."')
												AND a.status NOT IN ($this->dead_stat)
												AND a.personell_nr=ps.nr
												AND ps.pid=p.pid
										ORDER BY p.name_last, p.name_first, p.name_middle";

				}else {
						$cond = "%staff%";
						#edited by VAN 03-07-08
						/*
						$this->sql="SELECT a.nr, a.personell_nr, ps.job_function_title, p.name_last, p.name_first, p.name_middle, p.date_birth, p.sex
										FROM     $this->tb_assign AS a,
														$this->tb AS ps,
														$this->tb_person AS p
										WHERE
												a.location_type_nr != 1
												AND (ps.short_id LIKE 'G%')
												AND a.location_nr=$dept_nr
												AND (a.date_end='$dbf_nodate' OR a.date_end>='".date('Y-m-d')."')
												AND a.status NOT IN ($this->dead_stat)
												AND a.personell_nr=ps.nr
												AND ps.pid=p.pid
										ORDER BY a.list_frequency DESC";
						*/
						$this->sql="SELECT a.nr, a.personell_nr, ps.job_function_title,ps.job_position,ps.license_nr,ps.tin, p.name_last, p.name_first, p.name_middle, p.date_birth, p.sex
										FROM     $this->tb_assign AS a,
														$this->tb AS ps,
														$this->tb_person AS p
										WHERE
												(ps.short_id LIKE 'G%')
												AND a.location_nr=$dept_nr
												AND (a.date_end='$dbf_nodate' OR a.date_end>='".date('Y-m-d')."')
												AND a.status NOT IN ($this->dead_stat)
												AND a.personell_nr=ps.nr
												AND ps.pid=p.pid
										ORDER BY p.name_last, p.name_first, p.name_middle";
				}
				/*
						$this->sql="SELECT a.nr, a.personell_nr, ps.job_function_title, p.name_last, p.name_first, p.name_middle, p.date_birth, p.sex
								FROM     $this->tb_assign AS a,
														$this->tb AS ps,
														$this->tb_person AS p
								WHERE a.role_nr=$role_nr
										AND a.location_type_nr=$loc_type_nr
										AND a.location_nr=$dept_nr
										AND (a.date_end='$dbf_nodate' OR a.date_end>='".date('Y-m-d')."')
										AND a.status NOT IN ($this->dead_stat)
										AND a.personell_nr=ps.nr
										AND ps.pid=p.pid
								ORDER BY a.list_frequency DESC";
				*/

//echo "class_personell.php : _getAllPersonell : sql = '".$this->sql."'<br> \n";
//die(print_r("class_personell.php : _getAllPersonell : sql = '".$this->sql."'<br> \n"));
				if ($this->result=$db->Execute($this->sql)) {
						if ($this->record_count=$this->result->RecordCount()) {
								return $this->result;

						} else {
								return FALSE;
						}
				}
				else {
						return FALSE;
				}
		}
		/**#@-*/

		/**#@+
		*
		* If the on-call duty plan exists, its record primary key number will be returned, else FALSE
		* @return mixed adodb record object or boolean
		*/
		/**
		* Checks if the on-call duty plan of a given role number, department number, year and month exists in the databank.
		* @access private
		* @param int Role number
		* @param int Department number
		* @param int Year
		* @param int Month
		*/
		function _OCDutyplanExists($role_nr,$dept_nr=0,$year=0,$month=0){
				global $db;
				if(!$role_nr||!$dept_nr||!$year||!$month){
						return FALSE;
				}else{
						if ($this->row= $this->_getOCDutyPlan($role_nr,$dept_nr,$year,$month,'nr')) {
								return $this->row['nr'];
						}else {
								return FALSE;
						}
				}
		}
		/**
		* Checks if the  doctors' on-call duty plan of a given department number, year and month exists in the databank.
		*
		* If the on-call duty plan exists, its record primary key number will be returned, else FALSE
		* @access public
		* @param int Department number
		* @param int Year
		* @param int Month
		*/
		function DOCDutyplanExists($dept_nr,$year,$month){
				return $this->_OCDutyplanExists(15,$dept_nr,$year,$month); // 15 = doctor_on_call (role)
		}
		/**
		* Checks if the  nurses' on-call duty plan of a given department number, year and month exists in the databank.
		*
		* If the on-call duty plan exists, its record primary key number will be returned, else FALSE
		* @access public
		* @param int Department number
		* @param int Year
		* @param int Month
		*/
		function NOCDutyplanExists($dept_nr,$year,$month){
				return $this->_OCDutyplanExists(14,$dept_nr,$year,$month); // 14 = nurse_on_call (role)
		}
		/**#@-*/

		/**#@+
		*
		* The returned items are based on the field names passed as string to the method.
		* To see the allowed field names to be passed, see the <var>$fld_dpoc</var> array.
		* @return mixed adodb record object or boolean
		*/
		/**
		* Gets the on-call duty plan of a given role number, department number, year and month.
		*
		* @access private
		* @param int Role number
		* @param int Department number
		* @param int Year
		* @param int Month
		* @param string Field names of items to be fetched
		*/
		function _getOCDutyplan($role_nr,$dept_nr=0,$year=0,$month=0,$elems='*'){
				global $db;

				if(!$role_nr||!$dept_nr||!$year||!$month){
						return FALSE;
				}else{
						$this->sql="SELECT $elems FROM $this->tb_dpoc WHERE role_nr=$role_nr AND dept_nr=$dept_nr AND year=$year AND month  IN ('$month','".(int)$month."',".(int)$month.")" ;
						if ($this->res['_godp']=$db->Execute($this->sql)) {
								if ($this->rec_count=$this->res['_godp']->RecordCount()) {
										return $this->res['_godp']->FetchRow();
								}else{return FALSE;}
						}else{return FALSE;}
				}
		}
		/**
		* Gets the  doctors' on-call duty plan of a  department number, year and month.
		*
		* @access public
		* @param int Department number
		* @param int Year
		* @param int Month
		* @param string Field names of items to be fetched
		*/
		function getDOCDutyplan($dept_nr,$year,$month,$elems='*'){
				return $this->_getOCDutyplan(15,$dept_nr,$year,$month,$elems);
		}
		/**
		* Gets the  Nurses' on-call duty plan of a  department number, year and month.
		*
		* @access public
		* @param int Department number
		* @param int Year
		* @param int Month
		* @param string Field names of items to be fetched
		*/
		function getNOCDutyplan($dept_nr,$year,$month,$elems='*'){
				return $this->_getOCDutyplan(14,$dept_nr,$year,$month,$elems);
		}
		/**#@-*/

		/**
		* Gets the personnel information based on its personnel number key.
		*
		* The returned  array contains the personnel data with the following index keys:
		* - all index keys as outlined in the <var>$personell_fields</var> array
		* - all index keys as outlined in the <var>Person::$elems_array</var> array
		* - funk1 = first pager number
		* - inphone1 = first internal phone number
		* - inphone2 = second internal phone number
		* - inphone3 = third internal phone number
		* @access public
		* @param int Personnel number
		* @return mixed adodb record object or boolean
		*/
		function getPersonellInfo($nr){
				global $db;
				$this->sql="SELECT cpa.location_nr, dept.name_formal AS dept_name,
														ps.*,p.*,
														c.funk1,
														c.funk2,
														c.inphone1,
														c.inphone2,
														c.inphone3
								FROM $this->tb AS ps
														LEFT JOIN care_personell_assignment AS cpa ON cpa.personell_nr=ps.nr
																LEFT JOIN care_department AS dept ON dept.nr=cpa.location_nr,
												$this->tb_person AS p LEFT JOIN
												$this->tb_cphone AS c ON c.personell_nr=$nr
								WHERE ps.nr='$nr'
								 AND ps.pid=p.pid";
/*            # burn commmented :' November 20, 2007
				$sql="SELECT ps.*,p.*,
														c.funk1,
														c.funk2,
														c.inphone1,
														c.inphone2,
														c.inphone3
								FROM $this->tb AS ps,
												$this->tb_person AS p LEFT JOIN
												$this->tb_cphone AS c ON c.personell_nr=$nr
								WHERE ps.nr='$nr'
								 AND ps.pid=p.pid";
*/
#echo "class_personell.php : getPersonellInfo :: sql ='".$sql."'<br> \n";   #burn: November 14, 2007
				if ($this->result=$db->Execute($this->sql)) {
							 if ($this->record_count=$this->result->RecordCount()) {
								return $this->result->FetchRow();
						} else {
								return FALSE;
						}
				}else {
						return FALSE;
				}
		}
		/**
		* Gets a list of departments with on-call duty plan of a given role number, year and month.
		*
		* The returned array contains the department numbers with availabe on-call plan.
		* @access private
		* @param int Role number
		* @param int Year
		* @param int Month
		* @return mixed array  or boolean
		*/
		function _getOCQuicklist($role_nr,$year=0,$month=0){
				global $db;
				$x='';
				$v='';
				$d=$this->depts;
				$row;
				$buffer=array();
				if(!$role_nr||!$year||!$month){
						return FALSE;
				}else{
						list($x,$v)=each($d);
						$dept_list=$v['nr'];
						while(list($x,$v)=each($d)){
								$dept_list.=','.$v['nr'];
						}

						$sql="SELECT dept_nr FROM $this->tb_dpoc WHERE role_nr=$role_nr AND dept_nr IN ($dept_list) AND year='$year' AND month='$month'";

						if ($this->result=$db->Execute($sql)) {
								if ($this->record_count=$this->result->RecordCount()) {
										$row=$this->result->GetArray();
										while(list($x,$v)=each($row)) {
												$buffer[]=$v['dept_nr'];
										}
										return $buffer;
								} else {
										return FALSE;
								}
						}else {
								return FALSE;
						}
				}
		}

		/**
		* Gets a list of departments with doctors' on-call duty plan of a given  year and month.
		*
		* An array to hold the department numbers must be passed as reference.
		* @access public
		* @param array Department numbers. Associative, reference.
		* @param int Year
		* @param int Month
		* @return mixed array  or boolean
		*/
		function getDOCQuicklist(&$depts,$year,$month){
				$this->depts=$depts;
				return $this->_getOCQuicklist(15,$year,$month);
		}
		/**
		* Gets a list of departments with Nurses' on-call duty plan of a given  year and month.
		*
		* An array to hold the department numbers must be passed as reference.
		* @access public
		* @param array Department numbers. Associative, reference.
		* @param int Year
		* @param int Month
		* @return mixed array  or boolean
		*/
		function getNOCQuicklist(&$depts,$year,$month){
				$this->depts=$depts;
				return $this->_getOCQuicklist(14,$year,$month);
		}
		/**
		* Searches and returns basic personnel information.
		*
		* The returned adodb record object contains rows of arrays.
		* Each array contains the personnel data with the following index keys:
		* - nr = record's primary key number
		* - job_function_title = job function title or name
		* - name_last = employee's last or family name
		* - name_first = employee's first or given name
		* - date_birth = date of birth
		* - sex = sex
		* @param string Search key words
		* @param string Field name to sort, default = 'name_last'
		* @param string Sort direction, default = ASC
		* @param boolean Flags whether the return is limited or not, default FALSE
		* @param int Maximum number of rows returned, default 30 rows
		* @param int Index of the first returned row default 0 = start
		* @return mixed adodb record object  or boolean
		*/
		function searchPersonellBasicInfo($key,$oitem='name_last',$odir='ASC',$limit=FALSE,$len=30,$so=0){
				global $db, $sql_LIKE;

				if(empty($key)) return FALSE;
				$this->sql="SELECT ps.nr, ps.job_function_title, p.pid, p.name_last, p.name_first, p.date_birth, p.sex
								FROM $this->tb AS ps, $this->tb_person AS p";
				if(is_numeric($key)){
						$key=(int)$key;
						$this->sql.=" WHERE ps.nr = $key AND ps.pid=p.pid";
				}else{
						$this->sql.=" WHERE (ps.nr $sql_LIKE '$key%'
												OR ps.job_function_title $sql_LIKE '$key%'
												Or p.pid $sql_LIKE '$key%'
												OR p.name_last $sql_LIKE '$key%'
												OR p.name_first $sql_LIKE '$key%'
												OR p.date_birth $sql_LIKE '$key%')
												AND p.pid=ps.pid";
				}
				#echo "sql = ".$this->sql;
				if(!empty($oitem)){
						if($oitem=='nr'||$oitem=='job_function_title') $this->sql.=" ORDER BY ps.$oitem $odir";
								else  $this->sql.=" ORDER BY p.$oitem $odir";
				}
				if($limit){
						$this->res['spbi']=$db->SelectLimit($this->sql,$len,$so);
				}else{
						$this->res['spbi']=$db->Execute($this->sql);
				}
				if ($this->res['spbi']) {
							 if ($this->record_count=$this->res['spbi']->RecordCount()) {
								$this->rec_count=$this->record_count; # Work around
								return $this->res['spbi'];
						}else{return FALSE;}
				}else{return FALSE;}
		}
		/**
		* Search similar to searchPersonellBasicInfo but returns a limited number of rows.
		*
		* For detailed structure of returned data, see <var>searchPersonellBasicInfo()</var> method.
		* @access public
		* @param string Search key word
		* @param int Maximum number of rows returned, default 30 rows
		* @param int Index of the first returned row, default 0 = start
		* @param string Field name to sort, default = 'name_last'
		* @param string Sort direction, default = ASC
		* @return mixed adodb record object  or boolean
		*/
		function searchLimitPersonellBasicInfo($key,$len,$so,$oitem,$odir){
				return $this->searchPersonellBasicInfo($key,$oitem,$odir,TRUE,$len,$so);
		}
		/**
		* Checks if the PID number (the person) exists as employee in the database.
		*
		* If person exists as employee, its record primary number key will be returned, else FALSE.
		* @access public
		* @param int PID number
		* @return mixed integer  or boolean
		*/
		function Exists($pid=0){
				global $db;
				if(!$pid){
						return FALSE;
				}else{
						$sql="SELECT nr FROM $this->tb WHERE pid=$pid";
						#echo "Exists : ".$sql;
						if ($this->result=$db->Execute($sql)) {
										if ($this->result->RecordCount()) {
										$this->row=$this->result->FetchRow();
												return $this->row['nr'];
								} else {
										return FALSE;
								}
						}else {
									 return FALSE;
						}
				}
		}

		#------------- add 02-22-07------------------
		function is_already_assigned($personell_nr=0){
				global $db;

				$rs = $db->Execute("SELECT * FROM $this->tb_assign WHERE personell_nr = '$personell_nr'");

				$field = $rs->FetchRow();
				if ($field['personell_nr'] != NULL)
						return true;
				else
						return false;

}

		function get_Dept_name($personell_nr=0){
		global $db;

				$this->sql ="SELECT pa.personell_nr, pa.location_nr, d.nr, d.name_formal
												 FROM $this->tb_assign as pa, $this->tb_dept as d
												 WHERE pa.location_nr=d.nr and pa.personell_nr='$personell_nr'";

				if ($this->result=$db->Execute($this->sql)){
						if ($this->result->RecordCount())
								return $this->result->FetchRow();
						else
								return FALSE;
				}else{
						return FALSE;
				}
				/*
				$rs =$db->Execute("SELECT pa.personell_nr, pa.location_nr, d.nr, d.name_formal
																	FROM $this->tb_assign as pa, $this->tb_dept as d
																	WHERE pa.location_nr=d.nr and pa.personell_nr='$personell_nr'");

				$field = $rs->FetchRow();
				return $field['name_formal'];
				*/

		}

		function get_Person_name($personell_nr=0){
		global $db;

				$this->sql ="SELECT
												 /*if(job_function_title='Doctor','MD',if(job_function_title='Consulting doctor','MD,FPCR','')) as drtitle,*/
												 IF (p.`other_title` <> '' OR NOT ISNULL(p.`other_title`),other_title,if(job_function_title='Doctor','MD',if(job_function_title='Consulting doctor','MD,FPCR',''))) as drtitle, p.doctor_role as drRole, p.doctor_level,
												 CONCAT(ifnull(name_first,''),' ',IF (LENGTH(CONCAT(SUBSTR(name_middle,1,1),'.'))=2,CONCAT(SUBSTR(name_middle,1,1),'. '),' '),ifnull(name_last,'')) AS dr_name,
												 job_function_title, job_position,p.license_nr,p.prescription_license_nr,p.tin,pa.nr, pa.personell_nr, p.pid,pr.title, pr.pid, pr.name_first, pr.name_2, pr.name_middle, pr.name_last, pr.title
												 FROM $this->tb_assign as pa, $this->tb as p, $this->tb_person as pr
												 WHERE pa.personell_nr=p.nr and p.pid=pr.pid and pa.personell_nr='$personell_nr'";
				
				if ($this->result=$db->Execute($this->sql)){
						if ($this->result->RecordCount())
								return $this->result->FetchRow();
						else
								return FALSE;
				}else{
						return FALSE;
				}
				/*
				$rs =$db->Execute("SELECT pa.nr, pa.personell_nr, p.nr, p.pid, pr.pid, pr.name_first, pr.name_2, pr.name_last, pr.title
																	FROM $this->tb_assign as pa, $this->tb as p, $this->tb_person as pr
																	WHERE pa.personell_nr=p.nr and p.pid=pr.pid and pa.personell_nr='$personell_nr'");

				$field = $rs->FetchRow();
				return $field;
				*/
		}

		// added by carriane 10/20/17
		function getUserFullName($encoder){
			global $db;

			$encoder = $db->GetOne("SELECT `name` FROM `care_users` WHERE `login_id`=".$db->qstr($encoder));

			return $encoder;

		}
		// end carriane

        #Added by Jarel 04/09/2013
        function get_Person_name3($personell_nr=0,$isServedBy=0){
    		global $db;

    		if($isServedBy){
    			$this->sql ="SELECT
    							IF (p.`other_title` <> '' OR NOT ISNULL(p.`other_title`),other_title,
        							IF(job_function_title = 'Doctor','MD',
	        							IF(job_function_title = 'Consulting doctor','MD,FPCR',''
								     	)
					    			)
						  		) AS drtitle, p.doctor_role AS drRole, p.doctor_level, CONCAT(IFNULL(name_first, ''), ' ', IF (LENGTH(CONCAT(SUBSTR(name_middle, 1, 1), '.')) = 2,CONCAT(SUBSTR(name_middle, 1, 1), '. '),' '),IFNULL(name_last, '')) AS dr_name,name_last,job_function_title,job_position,p.license_nr,p.tin,p.nr,p.pid,pr.title,pr.pid,pr.name_first,pr.name_2,pr.name_middle,pr.name_last,pr.title
						  	FROM care_personell AS p,care_person AS pr 
							WHERE p.pid = pr.pid AND p.nr IN (".$db->qstr($personell_nr).") 
							ORDER BY p.doctor_level DESC ";
    		}else{
    			$this->sql ="SELECT
                                 IF (p.`other_title` <> '' OR NOT ISNULL(p.`other_title`),other_title,if(job_function_title='Doctor','MD',if(job_function_title='Consulting doctor','MD,FPCR',''))) as drtitle, p.doctor_role as drRole, p.doctor_level,
                                 CONCAT(ifnull(name_first,''),' ',IF (LENGTH(CONCAT(SUBSTR(name_middle,1,1),'.'))=2,CONCAT(SUBSTR(name_middle,1,1),'. '),' '),ifnull(name_last,'')) AS dr_name,name_last,
                                 job_function_title, job_position,p.license_nr,p.tin,pa.nr, pa.personell_nr, p.pid,pr.title, pr.pid, pr.name_first, pr.name_2, pr.name_middle, pr.name_last, pr.title
                                 FROM $this->tb_assign as pa, $this->tb as p, $this->tb_person as pr
                                 WHERE pa.personell_nr=p.nr and p.pid=pr.pid and pa.personell_nr IN (".$db->qstr($personell_nr).") ORDER BY p.doctor_level DESC";
    		}

            if ($this->result=$db->Execute($this->sql)){
                    if ($this->result->RecordCount()){
                            return $this->result;
                    }
                    else
                            return FALSE;
            }else{
                    return FALSE;
            }

        }

		#-----------added by van 04-30-07-----------

		function get_Dr_Dept_nr($fname, $fname2, $lname){
		global $db;

				if ($fname2){
						$cond = " (((pr.name_first LIKE '$fname%')
												 OR (pr.name_2 LIKE '$fname2%') )
											AND (pr.name_last LIKE '$lname%'))";
				}else{
						$cond = " (pr.name_first LIKE '$fname%'
												AND (pr.name_last LIKE '$lname%'))";
				}

				$this->sql ="SELECT pa.nr, pa.personell_nr, pa.location_nr, p.nr, p.pid,
														pr.pid, pr.name_first, pr.name_2, pr.name_last, pr.name_middle, pr.title,
														d.name_formal, d.nr
												FROM $this->tb_assign AS pa,
																 $this->tb AS p,
															 $this->tb_person AS pr,
																 $this->tb_dept AS d
												 WHERE pa.personell_nr=p.nr
												 AND p.pid=pr.pid
												 AND d.nr = pa.location_nr
												 AND $cond";

				#echo "this->sql = ".$this->sql;
				if ($this->result=$db->Execute($this->sql)){
						if ($this->result->RecordCount())
								return $this->result->FetchRow();
						else
								return FALSE;
				}else{
						return FALSE;
				}
		}

		#------------------------------------------


		function getDeptInfo($dept_nr=0){
		global $db;

				$this->sql ="SELECT * FROM $this->tb_dept WHERE nr='$dept_nr'";

				if ($this->result=$db->Execute($this->sql)){
						if ($this->result->RecordCount())
								return $this->result->FetchRow();
						else
								return FALSE;
				}else{
						return FALSE;
				}
				/*
				$rs =$db->Execute("SELECT * FROM $this->tb_dept WHERE nr='$dept_nr'");

				$field = $rs->FetchRow();
				return $field['name_formal'];
				*/
		}

		function getStaffOfDept($dept_nr=0){
				if(!$dept_nr) return FALSE;
				else return $this->_getAllPersonell(0,$dept_nr,"S"); // 1= dept (location); S = staff (role)
		}

		function getRole_type($personell_nr=0, $job_fxn){
		global $db;

				$this->sql ="SELECT p.nr, p.pid, p.job_function_title, rp.nr, rp.job_type_nr, rp.name
																	FROM $this->tb as p, $this->tb_role_person as rp
																	WHERE p.job_function_title=rp.name
																	AND p.job_function_title = '$job_fxn'
																	AND p.nr = '$personell_nr'";

				if ($this->result=$db->Execute($this->sql)){
						if ($this->result->RecordCount())
								return $this->result->FetchRow();
						else
								return FALSE;
				}else{
						return FALSE;
				}
				/*
				$rs = $db->Execute("SELECT p.nr, p.pid, p.job_function_title, rp.nr, rp.job_type_nr, rp.name
																	FROM $this->tb as p, $this->tb_role_person as rp
																	WHERE p.job_function_title=rp.name
																	AND p.job_function_title = '$job_fxn'
																	AND p.nr = '$personell_nr'");

				$field = $rs->FetchRow();
				return $field['nr'];
				*/
		}

		/*
		function getLocation_type($dept_nr=0){
		global $db;

				$rs = $db->Execute("SELECT * FROM $this->tb_dept WHERE nr='$dept_nr'");

				$field = $rs->FetchRow();
				return $field['type'];

		}
		*/

		function getPersonellData($nr = 0){
		global $db;

				$this->sql ="SELECT nr, personell_nr FROM $this->tb_assign WHERE nr = '$nr'";

				if ($this->result=$db->Execute($this->sql)){
						if ($this->result->RecordCount())
								return $this->result->FetchRow();
						else
								return FALSE;
				}else{
						return FALSE;
				}
				/*
				$rs = $db->Execute("SELECT nr, personell_nr FROM $this->tb_assign WHERE nr = '$nr'");

				$field = $rs->FetchRow();
				return $field['personell_nr'];
				*/
		}

		//edited fucntion by Macoy July 12,2014
		function _getAllDoctor($loc_type_nr,$admit_patient,$cond=''){
				global $db, $dbf_nodate;
				 $row=array();

# $admit_patient=0, Outpatient
# $admit_patient=1, Inpatient/ER
# $admit_patient NOT IN (0,1), BOTH Inpatient/ER & Outpatient

						# burn modified : August 1, 2007
				if ($admit_patient=="1"){
						$where = " AND d.admit_inpatient = $admit_patient ";
				}elseif ($admit_patient=="0") {
						$where = " AND d.admit_outpatient = 1 ";
				}

		$this->sql = "SELECT
						fn_get_personell_name(a.personell_nr) AS doctor_name,
						fn_get_personellname_lastfirstmi(a.personell_nr) AS doctor_name2,
						a.nr, a.personell_nr, a.location_type_nr, ps.job_function_title,
											p.custom_middle_initial AS cmid, p.name_last, p.name_middle, p.name_first, p.name_2, p.date_birth, p.sex,
											a.location_nr,d.nr,d.type,d.name_formal,d.admit_inpatient,ps.license_nr
									 FROM     $this->tb_assign AS a,
														$this->tb AS ps,
														$this->tb_person AS p,
														$this->tb_dept as d
										WHERE
												a.location_type_nr = $loc_type_nr
												$where $cond
												AND (ps.short_id LIKE 'D%')
												AND (a.date_end='$dbf_nodate' OR a.date_end>='".date('Y-m-d')."')
												AND a.status NOT IN ('hidden','inactive','void')
												AND a.personell_nr = ps.nr
												AND ps.pid = p.pid
												AND a.location_nr = d.nr
						$cond
										ORDER BY p.name_last";

				if ($this->result=$db->Execute($this->sql)) {
					 if ($this->count=$this->result->RecordCount()) {
								return $this->result;
						} else {
								return FALSE;
						}
		} else {
						return FALSE;
				 }
		}
		function _getAllDoctor2($loc_type_nr,$admit_patient,$cond=''){
				global $db, $dbf_nodate;
				 $row=array();

# $admit_patient=0, Outpatient
# $admit_patient=1, Inpatient/ER
# $admit_patient NOT IN (0,1), BOTH Inpatient/ER & Outpatient

						# burn modified : August 1, 2007
				if ($admit_patient=="0") {
						$where = " AND d.admit_outpatient = 1 AND a.status NOT IN ('hidden','void') ";
				}
				elseif ($admit_patient=="1"){
						$where = " AND d.admit_inpatient = $admit_patient AND a.status NOT IN ('hidden','void') ";
				}

		$this->sql = "SELECT
						fn_get_personell_name(a.personell_nr) AS doctor_name,
						fn_get_personellname_lastfirstmi(a.personell_nr) AS doctor_name2,
						a.nr, a.personell_nr, a.location_type_nr, ps.job_function_title,
											p.custom_middle_initial AS cmid, p.name_last, p.name_middle, p.name_first, p.name_2, p.date_birth, p.sex,
											a.location_nr,d.nr,d.type,d.name_formal,d.admit_inpatient,ps.license_nr
									 FROM     $this->tb_assign AS a,
														$this->tb AS ps,
														$this->tb_person AS p,
														$this->tb_dept as d
										WHERE
												a.location_type_nr = $loc_type_nr
												$where $cond
												AND (ps.short_id LIKE 'D%')
												AND a.personell_nr = ps.nr
												AND ps.pid = p.pid
												AND a.location_nr = d.nr
										ORDER BY p.name_last";

				if ($this->result=$db->Execute($this->sql)) {
					 if ($this->count=$this->result->RecordCount()) {
								return $this->result;
						} else {
								return FALSE;
						}
		} else {
						return FALSE;
				 }
		}

		function getDoctors2($admit_patient=0,$cond=''){
				return $this->_getAllDoctor2(1,$admit_patient,$cond);
		}
		function _getAllDoctorBilling($loc_type_nr,$admit_patient,$cond=''){
				$GLOBAL_CONFIG = array();
				$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
				$glob_obj->getConfig('personell_filter%');
				if(substr_count($GLOBAL_CONFIG['personell_filter_doctors'], ',')>0) {
				  $filter_doctors = explode(",",$GLOBAL_CONFIG['personell_filter_doctors']);
				  $filter_doctors_n = "'".implode("','",$filter_doctors)."'";
				}
				else {
				  $filter_doctors_n = "'".$GLOBAL_CONFIG['personell_filter_doctors']."'";
				}

				if(substr_count($GLOBAL_CONFIG['personell_filter_positions'], ',')>0) {
				  $filter_positions = explode(",",$GLOBAL_CONFIG['personell_filter_positions']);
				  $filter_positions_n = "cpl.job_position LIKE '%".implode("%' OR cpl.job_position LIKE '%",$filter_positions)."%'";
				}
				else {
				  $filter_positions_n = "cpl.job_position LIKE '%".$GLOBAL_CONFIG['personell_filter_positions']."%'";
				}



				global $db, $dbf_nodate;
				 $row=array();

# $admit_patient=0, Outpatient
# $admit_patient=1, Inpatient/ER
# $admit_patient NOT IN (0,1), BOTH Inpatient/ER & Outpatient

						# burn modified : August 1, 2007
				if ($admit_patient=="1"){
						$where = " AND d.admit_inpatient = $admit_patient ";
				}elseif ($admit_patient=="0") {
						$where = " AND d.admit_outpatient = 1 ";
				}

		$this->sql = "SELECT DISTINCT
					  fn_get_personell_name (cpl.nr) AS doctor_name,
					  fn_get_personellname_lastfirstmi (cpl.nr) AS doctor_name2,
					  cpl.nr as personell_nr,
					  cpl.short_id,
					  cpa.location_type_nr,
					  cpl.job_function_title,
					  cpl.job_position,
					  cpa.role_nr,
					  cpn.custom_middle_initial AS cmid,
					  cpn.name_last,
					  cpn.name_middle,
					  cpn.name_first,
					  cpn.name_2,
					  cpn.date_birth,
					  cpn.sex,
					  cpa.location_nr,
					  cd.nr dnr,
					  cd.type,
					  cd.name_formal,
					  cd.admit_inpatient,
					  cpl.license_nr 
					FROM
					  care_personell cpl 
					  LEFT JOIN care_person cpn 
					    ON cpl.pid = cpn.pid 
					  LEFT JOIN care_personell_assignment cpa 
					    ON cpl.nr = cpa.personell_nr 
					    AND cpa.status NOT IN ('deleted') 
					  LEFT JOIN care_department cd 
					    ON cpa.location_nr = cd.nr 
					    WHERE
					    (cpl.short_id LIKE 'D%')
					    AND cpl.job_function_title = 'Consulting doctor'
						AND (".$filter_positions_n.") OR cpl.nr IN (".$filter_doctors_n.")
						ORDER BY cpn.name_last";
												// var_dump($sql);die();

				if ($this->result=$db->Execute($this->sql)) {
					 if ($this->count=$this->result->RecordCount()) {
								return $this->result;
						} else {
								return FALSE;
						}
		} else {
						return FALSE;
				 }
		}

		function _getAllDoctorIPBM($loc_type_nr,$admit_patient,$cond=''){
				global $db, $dbf_nodate;
				define('IPBMdept_nr', 182);
				 $row=array();
				if ($admit_patient=="1"){
						$where = " AND d.admit_inpatient = $admit_patient ";
				}elseif ($admit_patient=="0") {
						$where = " AND d.admit_outpatient = 1 ";
				}

				$this->sql = "SELECT
						fn_get_personell_name(a.personell_nr) AS doctor_name,
						fn_get_personellname_lastfirstmi(a.personell_nr) AS doctor_name2,
						a.nr, a.personell_nr, a.location_type_nr, ps.job_function_title,
											p.custom_middle_initial AS cmid, p.name_last, p.name_middle, p.name_first, p.name_2, p.date_birth, p.sex,
											a.location_nr,d.nr,d.type,d.name_formal,d.admit_inpatient,ps.license_nr
									 FROM     $this->tb_assign AS a,
														$this->tb AS ps,
														$this->tb_person AS p,
														$this->tb_dept as d
										WHERE
												a.location_type_nr = $loc_type_nr
												$where $cond
												AND (ps.short_id LIKE 'D%')
												AND (a.date_end='$dbf_nodate' OR a.date_end>='".date('Y-m-d')."')
												AND a.status NOT IN ('hidden','inactive','void')
												AND a.personell_nr = ps.nr
												AND ps.pid = p.pid
												AND a.location_nr = d.nr
												AND a.location_nr = ".IPBMdept_nr."
						$cond
										ORDER BY p.name_last";

				if ($this->result=$db->Execute($this->sql)) {
					 if ($this->count=$this->result->RecordCount()) {
								return $this->result;
						} else {
								return FALSE;
						}
		} else {
						return FALSE;
				 }
		}

		function _getAllDoctor2IPBM($loc_type_nr,$admit_patient,$cond=''){
				global $db, $dbf_nodate;
				define('IPBMdept_nr', 182);
				 $row=array();

# $admit_patient=0, Outpatient
# $admit_patient=1, Inpatient/ER
# $admit_patient NOT IN (0,1), BOTH Inpatient/ER & Outpatient

						# burn modified : August 1, 2007
				if ($admit_patient=="0") {
						$where = " AND d.admit_outpatient = 1 AND a.status NOT IN ('hidden','void') ";
				}
				elseif ($admit_patient=="1"){
						$where = " AND d.admit_inpatient = $admit_patient AND a.status NOT IN ('hidden','void') ";
				}

				$this->sql = "SELECT
						fn_get_personell_name(a.personell_nr) AS doctor_name,
						fn_get_personellname_lastfirstmi(a.personell_nr) AS doctor_name2,
						a.nr, a.personell_nr, a.location_type_nr, ps.job_function_title,
											p.custom_middle_initial AS cmid, p.name_last, p.name_middle, p.name_first, p.name_2, p.date_birth, p.sex,
											a.location_nr,d.nr,d.type,d.name_formal,d.admit_inpatient,ps.license_nr
									 FROM     $this->tb_assign AS a,
														$this->tb AS ps,
														$this->tb_person AS p,
														$this->tb_dept as d
										WHERE
												a.location_type_nr = $loc_type_nr
												$where $cond
												AND (ps.short_id LIKE 'D%')
												AND a.personell_nr = ps.nr
												AND ps.pid = p.pid
												AND a.location_nr = d.nr
												and a.location_nr = ".IPBMdept_nr."
										ORDER BY p.name_last";

				if ($this->result=$db->Execute($this->sql)) {
					 if ($this->count=$this->result->RecordCount()) {
								return $this->result;
						} else {
								return FALSE;
						}
				} else {
						return FALSE;
				}
		}

		function getDoctors2IPBM($admit_patient=0,$cond=''){
				return $this->_getAllDoctor2IPBM(1,$admit_patient,$cond);
		}

		function getDoctorsIPBM($admit_patient=0,$cond=''){
				return $this->_getAllDoctorIPBM(1,$admit_patient,$cond);
		}

		//added by carriane 7/10/17
		function getServiceWardDoctors(){
			global $db;

			$list = Config::get('care_personell_filter_doctors');

			$arrayList = explode(',', $list);

			$arrayList2 = "('".implode("','", $arrayList)."')";

			$this->sql = "SELECT 
							  cpa.`nr`,
							  cp.`name_last`,
							  cp.`name_first`,
							  cp.`name_middle` 
							FROM
							  `care_personell` cpa LEFT JOIN `care_person` cp ON cpa.pid = cp.pid
							WHERE cpa.pid IN ".$arrayList2."
							ORDER BY cp.name_last";

			if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
						return $this->result;
			} else{
				 return FALSE;
			}
		}
		//end of getServiceWardDoctors()

		function getDoctors($admit_patient=0,$cond=''){
				return $this->_getAllDoctor(1,$admit_patient,$cond);
		}
		function getDoctorsBilling($admit_patient=0,$cond=''){
				return $this->_getAllDoctorBilling(1,$admit_patient,$cond);
		}
		function getDoctorByDept($dept_nr=0, $admit_patient=0, $cond=''){
				global $db, $dbf_nodate;

#        include_once($root_path.'include/care_api_classes/class_department.php');
				$dept_obj=new Department;

# $admit_patient=0, Outpatient
# $admit_patient=1, Inpatient/ER
# $admit_patient NOT IN (0,1), BOTH Inpatient/ER & Outpatient


						# burn modified : August 1, 2007
				if ($admit_patient=="1"){
						$where = " AND d.admit_inpatient = $admit_patient ";
				}elseif ($admit_patient=="0") {
						$where = " AND d.admit_outpatient = 1 ";
				}

#            $list = $this->getAncestorChildrenDept($dept_nr);   # burn added : May 31, 2007
						$list = $dept_obj->getAncestorChildrenDept($dept_nr);   # burn added : July 19, 2007
#echo "class_personell.php : _getAllPersonell : list = '".$list."'<br> \n";
#echo "class_personell.php : _getAllPersonell : list : <br> \n"; print_r($list); echo"<br> \n";
						$add_where="";
						if (!empty($list)){
								$add_where = " OR pa.location_nr IN ($list) ";
						}

				$this->sql ="SELECT pa.personell_nr, pa.location_nr,
														p.name_last, p.name_middle, p.name_first, p.name_2
												FROM $this->tb_assign AS pa,
																$this->tb AS pr,
																$this->tb_person AS p,
																$this->tb_dept as d
												 WHERE
																pa.personell_nr = pr.nr
																AND pa.location_nr = d.nr
																AND pr.pid = p.pid
																AND pr.short_id LIKE 'D%'
																AND pa.location_type_nr = 1
																AND (pa.date_end='$dbf_nodate' OR pa.date_end>='".date('Y-m-d')."')
																AND pa.status NOT IN ($this->dead_stat)
																$where
																AND (pa.location_nr = '$dept_nr' $add_where)
								$cond
																ORDER BY p.name_last";
#                                ORDER BY pa.list_frequency DESC";
#echo "class_personell.php : getDoctorByDept : this->sql = '".$this->sql."' <br> \n";

				if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
						return $this->result;
			} else{
				 return FALSE;
			}
		}


		function getIPBMDoctorByNr($nr){
			global $db;
			$this->sql ="SELECT pa.personell_nr, pa.location_nr,pr.license_nr,
								IF(p.name_first IS NOT NULL OR p.name_first != '',CONCAT(p.name_first,' ', p.name_middle, ' ', name_last), u.name) AS name
						FROM $this->tb_assign AS pa,
										$this->tb AS pr,
										$this->tb_person AS p,
										$this->tb_dept AS d,
										$this->tb_users AS u
						 where
										pa.personell_nr = ". $db->qstr($nr) ."
										AND pa.personell_nr = pr.nr
										AND pa.location_nr = d.nr
										AND pr.pid = p.pid
										AND pr.short_id LIKE 'D%'
										AND pa.location_type_nr = '1'
										AND pa.status NOT IN ($this->dead_stat)
										AND (pa.location_nr = " . $db->qstr($this->IPBMdept_nr) . ")
										LIMIT 1";
										// print_r($this->sql);die;
			if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
						return $this->result->FetchRow();
			} else{
				 return FALSE;
			}
		}
		#--------------------------------------------

		/**
		* Loads the personnel data in the internal buffer <var>$personell_data</var>. based on its personnel number key.
		*
		* The data is stored in the internal buffer array <var> $personell_data</var> .
		* This method returns only TRUE or FALSE. The load success status is also stored in the <var>$is_loaded</var> variable.
		* @access public
		* @param int Personnel number
		* @return boolean
		*/
		function loadPersonellData($nr=0){
				global $db;

				if(!$nr) return FALSE;
				/*
				$this->sql="SELECT ps.*, p.title, p.name_last, p.name_first, p.date_birth, p.sex,
														p.street_name, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name,
														p.photo_filename,
														c.item_nr AS phone_pk,
														c.beruf,
														c.bereich1,
														c.bereich2,
														c.exphone1,
														c.exphone2,
														c.funk1,
														c.funk2,
														c.inphone1,
														c.inphone2,
														c.inphone3,
														c.roomnr,
														t.name AS citytown_name
								FROM $this->tb AS ps,
												$this->tb_person AS p
												LEFT JOIN $this->tb_cphone AS c ON c.personell_nr=$nr
												LEFT JOIN $this->tb_citytown AS t ON p.addr_citytown_nr=t.nr,
												$this->tb_barangays AS sb, $this->tb_municity AS sm,
												$this->tb_provinces AS sp, $this->tb_regions AS sr
								WHERE ps.nr=$nr AND ps.pid=p.pid
														AND sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr
														AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=p.brgy_nr
												";
						*/
						$this->sql="SELECT ps.*, p.create_id AS pcreated_id, spc.description AS jobcatdes, p.title, p.name_last, p.name_first, p.name_middle, p.date_birth, p.sex,
														p.street_name, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name,
														p.photo_filename,
														c.item_nr AS phone_pk,
														c.beruf,
														c.bereich1,
														c.bereich2,
														c.exphone1,
														c.exphone2,
														c.funk1,
														c.funk2,
														c.inphone1,
														c.inphone2,
														c.inphone3,
														c.roomnr,
														t.name AS citytown_name
								FROM $this->tb AS ps
								INNER JOIN $this->tb_person AS p ON ps.pid=p.pid
								LEFT JOIN $this->tb_cphone AS c ON c.personell_nr=$nr
								LEFT JOIN $this->tb_citytown AS t ON p.addr_citytown_nr=t.nr
								LEFT JOIN $this->tb_barangays AS sb ON sb.brgy_nr=p.brgy_nr
								LEFT JOIN $this->tb_municity AS sm ON sm.mun_nr=sb.mun_nr
								LEFT JOIN $this->tb_provinces AS sp ON sp.prov_nr=sm.prov_nr
								LEFT JOIN $this->tb_regions AS sr ON sr.region_nr=sp.region_nr
								LEFT JOIN seg_phs_category AS spc ON ps.category = spc.id
								WHERE ps.nr=$nr";
#echo "class personell.php : loadPersonellData : this->sql = '".$this->sql."' <br> \n";
				if($this->result=$db->Execute($this->sql)) {
						if($this->record_count=$this->result->RecordCount()) {
								$this->personell_data=$this->result->FetchRow();
								$this->result=NULL;
								$this->is_loaded=TRUE;
								$this->is_preloaded=TRUE;
								//echo $this->sql;
								return TRUE;
						} else {
								//echo $this->sql;
								return FALSE;
						}
				} else {return FALSE;}
		}# end of function loadPersonellData


		function getPersonData($pid){
			global $db;

				$this->sql = "SELECT cp.*, seg_b.brgy_name, seg_m.`mun_name`, seg_m.`zipcode`, provinceName.`prov_name` FROM care_person cp
					LEFT JOIN `seg_barangays` AS seg_b
						ON cp.`brgy_nr` = seg_b.`brgy_nr`
					LEFT JOIN `seg_municity` AS seg_m
						ON cp.`mun_nr` = seg_m.`mun_nr`
					LEFT JOIN (
						SELECT seg_p.`prov_name`, segm.`mun_nr` FROM `seg_municity` segm
							INNER JOIN `seg_provinces` seg_p
								ON segm.`prov_nr`=seg_p.`prov_nr`
					) AS provinceName
						ON provinceName.`mun_nr` = seg_m.`mun_nr`
					WHERE cp.`pid`=".$db->qstr($pid);
				if ($this->result=$db->Execute($this->sql)) {
					if ($this->count=$this->result->RecordCount()){
						return $this->result->FetchRow();
				 }else{
						return FALSE;
				 }
				}else{
					return FALSE;
				}
		}

		function loadPersonellData_ORIG($nr=0){
				global $db;

				if(!$nr) return FALSE;

				$this->sql="SELECT ps.*, p.title, p.name_last, p.name_first, p.date_birth, p.sex,
														p.addr_str,p.addr_str_nr,p.addr_zip,
														p.photo_filename,
														c.item_nr AS phone_pk,
														c.beruf,
														c.bereich1,
														c.bereich2,
														c.exphone1,
														c.exphone2,
														c.funk1,
														c.funk2,
														c.inphone1,
														c.inphone2,
														c.inphone3,
														c.roomnr,
														t.name AS citytown_name
								FROM $this->tb AS ps,
												$this->tb_person AS p
												LEFT JOIN $this->tb_cphone AS c ON c.personell_nr=$nr
												LEFT JOIN $this->tb_citytown AS t ON p.addr_citytown_nr=t.nr
								WHERE ps.nr=$nr AND ps.pid=p.pid";
				if($this->result=$db->Execute($this->sql)) {
						if($this->record_count=$this->result->RecordCount()) {
								$this->personell_data=$this->result->FetchRow();
								$this->result=NULL;
								$this->is_loaded=TRUE;
								$this->is_preloaded=TRUE;
								//echo $this->sql;
								return TRUE;
						} else {
								//echo $this->sql;
								return FALSE;
						}
				} else {return FALSE;}
		}

		/**#@+
		*
		* Use this methode only after the personnell data was successfully loaded with the <var>loadPersonellData()</var> method.
		* @access public
		* @return string
		*/
		/**
		* Returns the title
		*/
		function Title(){
				//if(!$this->is_loaded) return FALSE;
				return $this->personell_data['title'];
		}
		/**
		* Returns the employee's last/family name
		*/
		function LastName(){
				//if(!$this->is_loaded) return FALSE;
				return $this->personell_data['name_last'];
		}
		/**
		* Returns the employee's first/given name
		*/
		function FirstName(){
				//if(!$this->is_loaded) return FALSE;
				return $this->personell_data['name_first'];
		}
		/**
		* Returns date of birth
		*/
		function BirthDate(){
				//if(!$this->is_loaded) return FALSE;
				return $this->personell_data['date_birth'];
		}
		/**
		* Returns profession info
		*/
		function Profession(){
				//if(!$this->is_loaded) return FALSE;
				return $this->personell_data['beruf'];
		}
		/**
		* Returns room nr.
		*/
		function RoomNr(){
				//if(!$this->is_loaded) return FALSE;
				return $this->personell_data['beruf'];
		}
		/**
		* Returns the primary key of the phone record
		*/
		function PhoneKey(){
				//if(!$this->is_loaded) return FALSE;
				return $this->personell_data['phone_pk'];
		}
		/**
		* Returns first internal phone number
		*/
		function InPhone1(){
				//if(!$this->is_loaded) return FALSE;
				return $this->personell_data['inphone1'];
		}
		/**
		* Returns second internal phone number
		*/
		function InPhone2(){
				//if(!$this->is_loaded) return FALSE;
				return $this->personell_data['inphone2'];
		}
		/**
		* Returns third internal phone number
		*/
		function InPhone3(){
				//if(!$this->is_loaded) return FALSE;
				return $this->personell_data['inphone3'];
		}
		/**
		* Returns first external phone number
		*/
		function ExPhone1(){
				//if(!$this->is_loaded) return FALSE;
				return $this->personell_data['exphone1'];
		}
		/**
		* Returns second external phone number
		*/
		function ExPhone2(){
				//if(!$this->is_loaded) return FALSE;
				return $this->personell_data['exphone2'];
		}
		/**
		* Returns third external phone number
		*/
		function ExPhone3(){
				//if(!$this->is_loaded) return FALSE;
				return $this->personell_data['exphone3'];
		}
		/**
		* Returns first dept
		*/
		function Dept1(){
				//if(!$this->is_loaded) return FALSE;
				return $this->personell_data['bereich1'];
		}
		/**
		* Returns second dept
		*/
		function Dept2(){
				//if(!$this->is_loaded) return FALSE;
				return $this->personell_data['bereich2'];
		}
		/**
		* Returns first pager number
		*/
		function Beeper1(){
				//if(!$this->is_loaded) return FALSE;
				return $this->personell_data['funk1'];
		}
		/**
		* Returns second pager number
		*/
		function Beeper2(){
				//if(!$this->is_loaded) return FALSE;
				return $this->personell_data['funk2'];
		}
		/**
		* Returns full address in german format
		*/
		function formattedAddress_DE(){
				//if(!$this->is_loaded) return FALSE;
#        return $this->personell_data['addr_str'].' '.$this->personell_data['addr_str_nr'].'<br>'.$this->personell_data['addr_str_zip'].' '.$this->personell_data['citytown_name'];   # burn commented : May 28, 2007
				return $this->personell_data['street_name'].' '.$this->personell_data['mun_name'].' '.
								$this->personell_data['zipcode'].'<br>'.$this->personell_data['prov_name'];   # burn added : May 28, 2007
		}
		/**#@-*/
		/**
		* Returns person's PID number.
		*
		* Use this methode only after the personnell data was successfully loaded with the <var>loadPersonellData()</var> method.
		* @access public
		* @return string
		*/
		function PID(){
				//if(!$this->is_loaded) return FALSE;
				return $this->personell_data['pid'];
		}

				/**  where to put?      class_personell.php
				*   Gets the doctors' on-call duty plan of a department number, year, month and day
				*
				*   @access public
				*   @param int Department number
				*   @param int Year
				*   @param int Month
				*   @param int Day
				*   @return boolean:
				*         TRUE, the list (array) of doctors on-call for the day (includes name, pnr, frequency)
				*         FALSE, there is no doctor on-call (DOC) for the day
				*   created burn: Sept 19, 2006
				*/
				function getDOCDutyplanForTheDay($dept_nr,$pyear,$pmonth,$pday){
					 global $db;
					 $doc = array();

					 $this->useDutyplanTable();

					 if($dutyplan=&$this->getDOCDutyplan($dept_nr,$pyear,$pmonth)){
							/* at this point, $dutyplan holds the doctors' on-call duty for
								 a particular department, year, & month
							*/
					$aelems=unserialize($dutyplan['duty_1_txt']);
					$relems=unserialize($dutyplan['duty_2_txt']);
					$a_pnr=unserialize($dutyplan['duty_1_pnr']);
					$r_pnr=unserialize($dutyplan['duty_2_pnr']);

						 /*  $aelems['a'.$pday];  name of the 1st doctor-on-call */
						 /* $a_pnr['ha'.$pday];  'personell_nr' (care_personell_assignment table) = 'nr' (care_personell table)
																				of the 1st doctor-on-call */
						 /* $a_pnr['fa'.$pday];  number of xray films interpreted for the day by 1st doctor-on-call */
						 /* $relems['r'.$pday];  name of the 2nd doctor-on-call */
						 /* $r_pnr['hr'.$pday];  'personell_nr' (care_personell_assignment table) = 'nr' (care_personell table)
																				of the 2nd doctor-on-call */
						 /* $r_pnr['fa'.$pday];  number of xray films interpreted for the day by 2nd doctor-on-call */

							$doc['count']="";   /* initializes as empty */
							if ((!empty($aelems['a'.$pday])) && (!empty($relems['r'.$pday]))){
									$doc['count']="ar";   /* there are 2 assigned DOCs for the day */
							}else if (!empty($aelems['a'.$pday])){
									$doc['count']="a";    /* ONLY attending DOC is assigned for the day */
							}else if (!empty($relems['r'.$pday])){
									$doc['count']="r";    /* ONLY resident DOC is assigned for the day */
							}

							$doc['a'] = $aelems['a'.$pday];
							$doc['ha'] = $a_pnr['ha'.$pday];
							$doc['fa'] = $a_pnr['fa'.$pday];
							$doc['r'] = $relems['r'.$pday];
							$doc['hr'] = $r_pnr['hr'.$pday];
							$doc['fr'] = $r_pnr['fr'.$pday];
							echo "getDOCDutyplanOnTheDay TRUE <br>";
							return $doc;  /* returns the list of on-call duty doctors of a given day */
					 }else{
							echo "getDOCDutyplanOnTheDay FALSE <br>";
							return FALSE;   /* there is NO existing duty plan for the Month */
					 }
				}/* end of function getDOCDutyplanOnTheDay */

				/**  where to put?      class_personell.php ???
				*   Updates the 'frequency' (of 'duty_1_pnr' and 'duty_2_pnr' fields) in 'care_dutyplan_oncall' table
				*
				*   @access public
				*   @param array of DOC information
				*   @param int Department number
				*   @param int Year
				*   @param int Month
				*   @param int Day
				*   @return boolean:
				*         TRUE, successfully updated the 'frequency' (of 'duty_1_pnr' and 'duty_2_pnr' fields)
				*               in 'care_dutyplan_oncall' table;
				*         FALSE, there is no doctor on-call (DOC) for the day
				*   created burn: Sept 19, 2006
				*/
				function updateFrequencyDOC($DOCDuty, $dept_nr, $pyear, $pmonth, $pday, $encoder_id){
					 echo "entering function 'updateFrequencyDOC'  <br> ";

					 $this->useDutyplanTable();

					 $dutyplan=&$this->getDOCDutyplan($dept_nr,$pyear,$pmonth);

					 $a_pnr=unserialize($dutyplan['duty_1_pnr']);
					 $r_pnr=unserialize($dutyplan['duty_2_pnr']);

							/* updates the frequency of the DOC */
					 $a_pnr['fa'.$pday] = $DOCDuty['fa'];
					 $r_pnr['fr'.$pday] = $DOCDuty['fr'];

					 $ref_buffer=array();
							// Serialize the data
					 $ref_buffer['duty_1_pnr']=serialize($a_pnr);
					 $ref_buffer['duty_2_pnr']=serialize($r_pnr);

					 $ref_buffer['modify_id']=$encoder_id;
					 $ref_buffer['modify_time']=date('YmdHis');
					 $ref_buffer['history']=$this->ConcatHistory("Update frequency: ".date('Y-m-d H:i:s')." = ".$encoder_id."\n");

							// Point to the internal data array
					 $this->setDataArray($ref_buffer);

					 $dpoc_nr=$this->DOCDutyplanExists($dept_nr,$pyear,$pmonth);

					 echo "exiting function 'updateFrequencyDOC'  <br> ";

					 return $this->updateDataFromInternalArray($dpoc_nr);

				}/* end of function updateFrequencyDOC */

                //added by jasper 02/06/13
                function fnGetDoctorRoleName($id) {
                    global $db;

                    //edited by VAN 02-12-2013
                    //add $db->qstr to avoid SQL injection and follow coding standard (all keywords must be in CAPITAL LETTER)
                    //$sql = "select name from " . $this->tb_doctor_role . " where id = '" . $id . "'";
                    $sql = "SELECT name FROM " . $this->tb_doctor_role . " WHERE id = " . $db->qstr($id) . "";
                    if($this->result=$db->Execute($sql)){
                            if($this->record_count=$this->result->RecordCount()){
                                 return $this->result;
                            }else{
                                 return FALSE;
                            }
                    }
                }

                function fnGetDoctorRole(){
                    global $db;
                    $this->record_count = 0;

                     $sql="SELECT name, id FROM ".$this->tb_doctor_role." ORDER BY name ASC";

                     #echo "(getRoleNameOfPerson) sql = $sql <br> ";
                     if($this->result=$db->Execute($sql)){
                            if($this->record_count=$this->result->RecordCount()){
                                 #echo "getRoleNameOfPerson TRUE <br>";
                                 return $this->result;
                            }else{
                                # echo "getRoleNameOfPerson FALSE 01 <br>";
                                 return FALSE;
                            }
                     }else{
                            #echo "getRoleNameOfPerson FALSE 02 <br>";
                            return FALSE;
                     }
                }
                //added by jasper 02/06/13

                #Added by Jarel 04/03/2013
                function getDoctorLevel(){
                    global $db;

                     $sql="SELECT * FROM seg_doctor_level ORDER BY id";

                     if($this->result=$db->Execute($sql)){
                            if($this->record_count=$this->result->RecordCount()){
                                 return $this->result;
                            }else{
                                 return FALSE;
                            }
                     }else{
                            return FALSE;
                     }
                }

                function getDoctorLevelDesc($id){
                    global $db;

                     $sql = "SELECT * FROM seg_doctor_level WHERE id = " . $db->qstr($id) . "";

                     if($this->result=$db->Execute($sql)){
                            if($this->record_count=$this->result->RecordCount()){
                                 return $this->result;
                            }else{
                                 return FALSE;
                            }
                     }else{
                            return FALSE;
                     }
                }

                /**
				*   Gets all the seg_phs_job_status
				*   @access public
				*   @return boolean:
				*   created by: syboy; 05/15/2015
				*/
				function getCategoryLevel(){
					global $db;

					 $sql = "SELECT description, id, category FROM seg_phs_job_status WHERE is_deleted = 0 ORDER BY description ASC";

					 if($this->result=$db->Execute($sql)){
							if($this->record_count=$this->result->RecordCount()){
								 return $this->result;
							}else{
								 return FALSE;
							}
					 }else{
							return FALSE;
					 }
				}/* end of function getCategoryLevel */

				/*
				 * Author : syboy
				 */ 
				function getCatJob($personell_nr = 0){
					global $db;
					$sql2 = "SELECT 
							  cp.nr,
							  cp.category,
							  spjs.description AS spjsD,
							  spjs.category AS spjsc,
							  spc.description AS spcd
							FROM
							  care_personell AS cp 
							  INNER JOIN seg_phs_job_status AS spjs 
							    ON cp.category = spjs.id 
							  INNER JOIN seg_phs_category AS spc
							  	ON spjs.category = spc.id
							WHERE spjs.is_deleted = 0 
								AND cp.nr = '".$personell_nr."' ";

					if ($this->result = $db->Execute($sql2)){
						if ($this->count = $this->result->RecordCount())
								return $this->result;
						else
								return FALSE;
					}else{
							return FALSE;
					}
				}

				/**
				*   Gets all the possible role names of a person in 'care_role_person' table and 'name' fieldname
				*   @access public
				*   @return boolean:
				*         TRUE, list of role names from 'care_role_person' table;
				*         FALSE, there is no role name entry
				*   created burn: Sept 27, 2006
				*/
				function getRoleNameOfPerson(){
				global $db;

					 $this->record_count = 0;

					 $sql="SELECT name, job_type_nr FROM ".$this->tb_role_person." ORDER BY name ASC";

					 #echo "(getRoleNameOfPerson) sql = $sql <br> ";
					 if($this->result=$db->Execute($sql)){
							if($this->record_count=$this->result->RecordCount()){
								 #echo "getRoleNameOfPerson TRUE <br>";
								 return $this->result;
							}else{
								# echo "getRoleNameOfPerson FALSE 01 <br>";
								 return FALSE;
							}
					 }else{
							#echo "getRoleNameOfPerson FALSE 02 <br>";
							return FALSE;
					 }
				}/* end of function getRoleNameOfPerson */

				/**
				*   Checks if the role name of a person exists in the database based on its role name ONLY.
				*      - uses 'care_role_person' table and 'name' fieldname
				*
				*   @access public
				*   @param string Role name
				*   @return boolean:
				*         TRUE, the role name exist in 'care_role_person' table;
				*         FALSE, the role name does not exist
				*   created burn: Sept 27, 2006
				*/
				function roleName_exists($role_name=''){
				global $db;

					 $this->record_count = 0;

					 $sql="SELECT name FROM ".$this->tb_role_person." WHERE name='".trim($role_name)."'";

					 echo "(roleName_exists) sql = $sql <br> ";
					 if($this->result=$db->Execute($sql)){
							if($this->record_count=$this->result->RecordCount()){
								 echo "roleName_exists TRUE <br>";
								 return $this->result;
							}else{
								 echo "roleName_exists FALSE 01 <br>";
								 return FALSE;
							}
					 }else{
							echo "roleName_exists FALSE 02 <br>";
							return FALSE;
					 }
				}/* end of function roleName_exists */
				/**
				*   Checks if the role name of a person exists in the database based on its role name ONLY.
				*      - uses 'care_role_person' table and 'name' fieldname
				*
				*   @access public
				*   @param string Role name
				*   @return boolean:
				*         TRUE, the role name exist in 'care_role_person' table;
				*         FALSE, the role name does not exist
				*   created burn: Sept 27, 2006
				*/
				function getJobTypeNumber($role_name=''){
				global $db;
					 $this->record_count = 0;

					 $sql="SELECT job_type_nr FROM ".$this->tb_role_person." WHERE name='".trim($role_name)."'";

					 #echo "(getJobTypeNumber) sql = $sql <br> ";
					 if($this->result=$db->Execute($sql)){
							if($this->record_count=$this->result->RecordCount()){
								 $this->row=$this->result->FetchRow();
								 #echo "getJobTypeNumber TRUE this->row['job_type_nr']=".$this->row['job_type_nr']." <br>";
								 return $this->row['job_type_nr'];
								 # return $this->result;
							}else{
								 #echo "getJobTypeNumber FALSE 01 <br>";
								 return FALSE;
							}
					 }else{
							#echo "getJobTypeNumber FALSE 02 <br>";
							return FALSE;
					 }
				}/* end of function getJobTypeNumber */

		/**
		* Searches and returns basic personnel information.
		*
		* The returned adodb record object contains rows of arrays.
		* Each array contains the personnel data with the following index keys:
		* - nr = record's primary key number
		* - job_function_title = job function title or name
		* - name_last = employee's last or family name
		* - name_first = employee's first or given name
		* - date_birth = date of birth
		* - sex = sex
		* @param string Search key words
		* @param string Field name to sort, default = 'name_last'
		* @param string Sort direction, default = ASC
		* @param boolean Flags whether the return is limited or not, default FALSE
		* @param int Maximum number of rows returned, default 30 rows
		* @param int Index of the first returned row default 0 = start
		* @return mixed adodb record object  or boolean
		*   created/modified burn: Sept 28, 2006
		*/
		#edited by VAN 04-10-08
		function searchPersonellBasicInfoByJobType($key,$oitem='name_last',$odir='ASC',$limit=FALSE,$len=30,$so=0,$jobType=0){
				global $db, $sql_LIKE;
				#if(empty($key)) return FALSE;
				$this->sql="SELECT ps.nr, ps.job_function_title, p.pid, p.name_last, p.name_first, p.date_birth, p.sex
								FROM $this->tb AS ps, $this->tb_person AS p";
				if(is_numeric($key)){
						$key=(int)$key;
						$this->sql.=" WHERE ps.nr = $key AND ps.pid=p.pid";
				}else{
						/*
						$this->sql.=" WHERE (ps.nr $sql_LIKE '$key%'
												OR ps.job_function_title $sql_LIKE '$key%'
												Or p.pid $sql_LIKE '$key%'
												OR p.name_last $sql_LIKE '$key%'
												OR p.name_first $sql_LIKE '$key%'
												OR p.date_birth $sql_LIKE '$key%')
												AND p.pid=ps.pid
												AND ps.job_type_nr=$jobType";   # similar to searchPersonellBasicInfo except this line
						*/
						$this->sql.=" WHERE (ps.nr $sql_LIKE '%$key%'
												OR ps.job_function_title $sql_LIKE '%$key%'
												Or p.pid $sql_LIKE '%$key%'
												OR p.name_last $sql_LIKE '%$key%'
												OR p.name_first $sql_LIKE '%$key%'
												OR p.date_birth $sql_LIKE '%$key%')
												AND p.pid=ps.pid
												AND ps.job_type_nr=$jobType";   # similar to searchPersonellBasicInfo except this line

				}
				if(!empty($oitem)){
						if($oitem=='nr'||$oitem=='job_function_title') $this->sql.=" ORDER BY ps.$oitem $odir";
								else  $this->sql.=" ORDER BY p.$oitem $odir";
				}

#echo "searchPersonellBasicInfoByJobType : this->sql = '$this->sql' <br> \n";



				if($limit){
						$this->res['spbi']=$db->SelectLimit($this->sql,$len,$so);
				}else{
						$this->res['spbi']=$db->Execute($this->sql);
				}
		if ($this->res['spbi']) {
					 if ($this->record_count=$this->res['spbi']->RecordCount()) {
								$this->rec_count=$this->record_count; # Work around
								return $this->res['spbi'];
						}else{return FALSE;}
				}else{return FALSE;}
		}
		/**
		* Search similar to searchPersonellBasicInfo but returns a limited number of rows.
		*
		* For detailed structure of returned data, see <var>searchPersonellBasicInfo()</var> method.
		* @access public
		* @param string Search key word
		* @param int Maximum number of rows returned, default 30 rows
		* @param int Index of the first returned row, default 0 = start
		* @param string Field name to sort, default = 'name_last'
		* @param string Sort direction, default = ASC
		* @return mixed adodb record object  or boolean
		*   created/modified burn: Sept 28, 2006
		*/
		function searchLimitPersonellBasicInfoByJobType($key,$len,$so,$oitem,$odir,$jobType=0){
				#echo "searchLimitPersonellBasicInfoByJobType job type=$jobType <br> ";
				#return $this->searchPersonellBasicInfo($key,$oitem,$odir,TRUE,$len,$so);
				return $this->searchPersonellBasicInfoByJobType($key,$oitem,$odir,TRUE,$len,$so,$jobType);
		}

		#-----------added by VAN 03-27-08
				function getAllNurse($loc_type_nr){
				global $db, $dbf_nodate;

				$dept_obj=new Department;

				 $row=array();

						$cond = "%nurse%";
						$this->sql="SELECT a.nr, a.personell_nr, ps.job_function_title, p.name_last, p.name_first, p.name_middle, p.date_birth, p.sex
										FROM     $this->tb_assign AS a,
														$this->tb AS ps,
														$this->tb_person AS p
										WHERE
												a.location_type_nr=$loc_type_nr
												AND (ps.short_id LIKE 'N%')
												AND (a.date_end='$dbf_nodate' OR a.date_end>='".date('Y-m-d')."')
												AND a.status NOT IN ($this->dead_stat)
												AND a.personell_nr=ps.nr
												AND ps.pid=p.pid
										ORDER BY name_last, a.list_frequency DESC";

#echo "class_personell.php : _getAllPersonell : sql = '".$this->sql."'<br> \n";
				if ($this->result=$db->Execute($this->sql)) {
						if ($this->record_count=$this->result->RecordCount()) {
								return $this->result;
						} else {
								return FALSE;
						}
				}
				else {
						return FALSE;
				}
		}

		#----------------------------------------

		#added by VAN 04-28-08
		function LastInsertPKPersonell(){
				global $db;

				$this->sql="SELECT max(nr) AS nr FROM $this->tb";
# echo "LastInsertPKAddress : this->sql = '".$this->sql."' <br> \n";
				if ($this->row=$db->Execute($this->sql)) {
						if ($this->row->RecordCount()){
								$temp = $this->row->FetchRow();
								return $temp['nr'];
						} else {
								return 0;
						}
				}else{
						return 0;
				}
		}

		#-----added by VAN 06-14-08
		function get_Officer_Dept($position, $title, $dept){
				global $db;

				$this->sql ="SELECT p.*, pa.location_nr,pa.personell_nr, d.id, d.name_formal
										 FROM care_personell AS p
										 INNER JOIN care_personell_assignment AS pa ON pa.personell_nr=p.nr
										 LEFT JOIN care_department AS d ON d.nr = pa.location_nr
										 WHERE job_function_title = '$position'
										 AND job_position LIKE '$title%'
										 AND pa.location_nr='$dept'";

				#echo "this->sql = ".$this->sql;
				if ($this->result=$db->Execute($this->sql)){
						if ($this->result->RecordCount())
								return $this->result->FetchRow();
						else
								return FALSE;
				}else{
						return FALSE;
				}
		}

		function get_AllOfficer_Dept($position, $dept){
				global $db;

				$this->sql ="SELECT cp.*, p.*, pa.location_nr,pa.personell_nr, d.id, d.name_formal
										 FROM care_personell AS p
										 INNER JOIN care_person AS cp ON cp.pid=p.pid
										 INNER JOIN care_personell_assignment AS pa ON pa.personell_nr=p.nr
										 LEFT JOIN care_department AS d ON d.nr = pa.location_nr
										 WHERE job_function_title = '$position'
										 AND pa.location_nr='$dept'
										 AND job_position IS NOT NULL";

				#echo "this->sql = ".$this->sql;
				if ($this->result=$db->Execute($this->sql)){
						if ($this->result->RecordCount())
								return $this->result;
						else
								return FALSE;
				}else{
						return FALSE;
				}
		}

		function get_Officer_Head($title){
				global $db;

				$this->sql ="SELECT p.*, p.nr AS personell_nr
										 FROM care_personell AS p
										 WHERE job_position LIKE '$title%'";

				#echo "this->sql = ".$this->sql;
				if ($this->result=$db->Execute($this->sql)){
						if ($this->result->RecordCount())
								return $this->result->FetchRow();
						else
								return FALSE;
				}else{
						return FALSE;
				}
		}

		function get_Person_name2($personell_nr=0){
		global $db;

				$this->sql ="SELECT pa.nr, pa.personell_nr, p.nr, p.pid,pr.title,
										 pr.pid, pr.name_first, pr.name_2, pr.name_middle, pr.name_last, pr.title, pa.location_nr
										 FROM $this->tb as p
										 INNER JOIN $this->tb_person as pr ON p.pid=pr.pid
										 LEFT JOIN $this->tb_assign as pa ON pa.personell_nr=p.nr
										 WHERE p.nr='$personell_nr'";

				if ($this->result=$db->Execute($this->sql)){
						if ($this->result->RecordCount())
								return $this->result->FetchRow();
						else
								return FALSE;
				}else{
						return FALSE;
				}
		}

		function get_Personell_info($personell_nr=0){
				global $db;

				$this->sql ="SELECT * FROM care_personell where nr='".$personell_nr."'";

				if ($this->result=$db->Execute($this->sql)){
						if ($this->result->RecordCount())
								return $this->result->FetchRow();
						else
								return FALSE;
				}else{
						return FALSE;
				}
		}

		function getPathologist(){
				global $db;

				$this->sql = "SELECT CONCAT(IF(ISNULL(name_first), '', CONCAT(name_first, ' ')),
												IF(ISNULL(name_middle), '', substring(name_middle,1,1)), '. ',
												IF(ISNULL(name_last), '', name_last),
                                                IF(ISNULL(other_title), '', CONCAT(', ',other_title))) as name
												FROM care_personell AS pr
												INNER JOIN care_person AS p ON pr.pid=p.pid
												WHERE ((pr.job_function_title LIKE '%pathologist%')
												OR (pr.job_position LIKE '%pathologist%'))";

				 if ($this->result=$db->Execute($this->sql)){
						if ($this->result->RecordCount())
								return $this->result->FetchRow();
						else
								return FALSE;
				}else{
						return FALSE;
				}
		}

		//added by justinttan 11/10/2014
		function getPathologistId() {
			global $db;

			$this->sql = "select nr FROM care_personell AS pr
               INNER JOIN care_person AS p ON pr.pid=p.pid
               WHERE ((pr.job_function_title LIKE '%pathologist%')
               OR (pr.job_position LIKE '%pathologist%'))";

			if ($this->result = $db->Execute($this->sql)) {
				if ($this->result->RecordCount()) {
				    $row = $this->result->FetchRow();
				    return $row['nr'];
				}
				else
				    return FALSE;
			}else {
				return FALSE;
			}
		}

		// Added by LST - 03.31.2009 ---------------------------
		/*
		function concatname($slast, $sfirst, $smid) {
				$stmp = "";

				if (!empty($slast)) $stmp .= $slast;
				if (!empty($sfirst)) {
						if (!empty($stmp)) $stmp .= ", ";
						$stmp .= $sfirst;
				}
				if (!empty($smid)) {
						if (!empty($stmp)) $stmp .= " ";
						$stmp .= $smid;
				}
				return($stmp);
		}
		*/
		#---------------------------

		/**
		* @internal     get recordset of authorized representative.
		* @access       public
		* @author       Bong S. Trazo
		* @package      include
		* @subpackage   care_api_classes
		* @global       db - database object
		*
		* @return       recordset of authorized representative.
		*/
		function getAuthorizedRep() {
				global $db;

				$this->sql = "select name_last, name_first, name_middle, job_position\n".
										 "   from care_person as cp \n".
										 "      inner join care_personell as cpl on cp.pid = cpl.pid \n".
										 "   where cpl.status <> 'deleted' \n".
										 "      and is_authorized_rep = 1";
				if ($this->result = $db->Execute($this->sql)) {
						if ($this->result->RecordCount())
								return $this->result->FetchRow();
						else
								return false;
				}
				else
						return false;
		}

		// Added by LST - 03.31.2009 ---------------------------
		function concatname($slast, $sfirst, $smid) {
				$stmp = "";

				if (!empty($slast)) $stmp .= $slast;
				if (!empty($sfirst)) {
						if (!empty($stmp)) $stmp .= ", ";
						$stmp .= $sfirst;
				}
				if (!empty($smid)) {
						if (!empty($stmp)) $stmp .= " ";
						$stmp .= $smid;
				}
				return($stmp);
		}
		#---------------------------

		#added by VAN 05-22-09
		function clearAccreditationList($personell_nr) {
				$this->sql = "DELETE FROM seg_dr_accreditation WHERE dr_nr='$personell_nr'";
				#echo "<br>delete sql = ".$this->sql;
				return $this->Transact();
		}

		function addAccreditation($personell_nr, $orderArray) {
				global $HTTP_SESSION_VARS;
				global $db;

				$userid = $HTTP_SESSION_VARS['sess_temp_userid'];
				
				$this->sql = "INSERT INTO seg_dr_accreditation(dr_nr,hcare_id,accreditation_nr,expiration,status,history,modify_id,modify_dt,create_id,create_dt)
																		VALUES('$personell_nr',?,?,?,'',CONCAT('Create: ',NOW(),' [$userid]\\n'),'$userid',NOW(),'$userid',NOW())";

				
				if($buf=$db->Execute($this->sql,$orderArray)) {
						if($buf->RecordCount()) {
								return true;
						} else { return false; }
				} else { return false; }

		}

        function relieveDependents($personell_nr) {
            global $HTTP_SESSION_VARS;
            global $db;

            $encoder = $_SESSION['sess_temp_userid'];
            $sql_over_21 = "SELECT 
                                      sdp.* 
                                    FROM
                                      seg_dependents sdp 
                                      LEFT JOIN `care_personell` cpl 
                                        ON sdp.`parent_pid` = cpl.`pid` 
                                      LEFT JOIN `care_personell_assignment` cpa 
                                        ON cpl.`nr` = cpa.`personell_nr` 
                                      LEFT JOIN care_person cp 
                                        ON sdp.`dependent_pid` = cp.`pid` 
                                      LEFT JOIN seg_phs_job_status spjs 
                                        ON cpl.`category` = spjs.`id` 
                                      LEFT JOIN `seg_phs_category` spc 
                                        ON spjs.`category` = spc.`id` 
                                    WHERE sdp.`status` = 'member' AND cpl.`nr`=".$db->qstr($personell_nr);
            $dependent_over_21 = $db->Execute($sql_over_21);
            $objDependent2 = new SegDependents();

            while($dep_over_21 = $dependent_over_21->FetchRow()) {
                unset($data2);
                $data2['parent_pid'] = $dep_over_21['parent_pid'];
                $data2['dependent_pid'] = $dep_over_21['dependent_pid'];
                $data2['relationship'] = $dep_over_21['relationship'];
                $data2['create_id'] = 'System';

                $objDependent2->dependentMonitoring($data2, 'Locked');
                echo "<br/> Deactivated Dependent - ".$dep_over_21['dependent_pid'];
                $history_over_21 = "CONCAT(history,'Locked Personnel: " . date('Y-m-d H:i:s') . " [System]\n')";
                $db->Execute("UPDATE
                                          seg_dependents
                                        SET status='expired',
                                            history = {$history_over_21},
                                            modify_id = ?,
                                            modify_dt = NOW()
                                        WHERE dependent_pid=?", array($encoder, $dep_over_21['dependent_pid']));
            }

        }

		function get_Doctor_Accreditation($personell_nr=0){
				global $db;

				$this->sql ="SELECT ac.*, i.firm_id
												FROM seg_dr_accreditation AS ac
												INNER JOIN care_insurance_firm AS i ON i.hcare_id=ac.hcare_id
												WHERE dr_nr='$personell_nr'";

				if ($this->result=$db->Execute($this->sql)){
						if ($this->count = $this->result->RecordCount())
								return $this->result;
						else
								return FALSE;
				}else{
						return FALSE;
				}
		}
		#---------------------------
		/**
		* @internal     Return the role type levels applicable to doctors.
		* @access       public
		* @author       Bong S. Trazo
		* @package      include
		* @subpackage   care_api_classes
		* @global       db - database object
		*
		* @return       resultset of extracted records.
		*/
		function getRoleTypeLevels() {
				global $db;

				$this->sql = "select * from seg_role_tier order by tier_nr";
				if ($this->result = $db->Execute($this->sql)) {
						return $this->result;
				}
				else
						return false;
		}

		#added by VAN 12-18-09
		function getAllDoctorAffiliates($personell_nr){
				global $db;

				$this->sql="SELECT affiliates
										FROM seg_personell_affiliates
										WHERE personell_nr='".$personell_nr."'";

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
		#-------------------------

		function getNameWPosition($pid){
				global $db;

				$this->sql="SELECT CONCAT(TRIM(p.name_first),' ', IF(TRIM(p.name_middle)<>'',CONCAT(LEFT(TRIM(p.name_middle),1),'. '),''),
											TRIM(p.name_last),
														IF(substr(short_id,1,1)='D',CONCAT(', MD',IF(ISNULL(other_title),'',CONCAT(', ',other_title))),
												IF(ISNULL(other_title),'',CONCAT(', ',other_title)) )) as name
										from care_person AS p
										INNER JOIN care_personell AS pr ON pr.pid=p.pid
										WHERE p.pid = '".$pid."'";

				if ($this->result=$db->Execute($this->sql)) {
					if ($this->count=$this->result->RecordCount()){
						return $this->result->FetchRow();
				 }else{
						return FALSE;
				 }
				}else{
					return FALSE;
				}

		}

		#added by VAN 05-04-2010
		 function clearWardList($personell_nr) {
				$this->sql = "DELETE FROM seg_nurse_ward_area WHERE personell_nr='$personell_nr'";
				#echo "<br>delete sql = ".$this->sql;
				return $this->Transact();
		}

		function addWard($personell_nr, $wardArray) {
				global $HTTP_SESSION_VARS;
				global $db;

				$this->sql = "INSERT INTO seg_nurse_ward_area(personell_nr,ward_nr)
																		VALUES('$personell_nr',?)";

				if($buf=$db->Execute($this->sql,$wardArray)) {
						if($buf->RecordCount()) {
								return true;
						} else { return false; }
				} else { return false; }

		}

		function get_Nurse_Ward_Area($personell_nr=0){
				global $db;

				$this->sql ="SELECT n.ward_nr, w.ward_id, w.name
											FROM seg_nurse_ward_area AS n
											INNER JOIN care_ward AS w ON w.nr=n.ward_nr
											WHERE personell_nr='$personell_nr'";

				if ($this->result=$db->Execute($this->sql)){
						if ($this->count = $this->result->RecordCount())
								return $this->result;
						else
								return FALSE;
				}else{
						return FALSE;
				}
		}

		function get_Nurse_Ward_Area_Assign($personell_nr=0, $ward_nr=0){
				global $db;

				$this->sql ="SELECT n.ward_nr, w.ward_id, w.name
											FROM seg_nurse_ward_area AS n
											INNER JOIN care_ward AS w ON w.nr=n.ward_nr
											WHERE personell_nr='$personell_nr' AND ward_nr='$ward_nr'";

				if ($this->result=$db->Execute($this->sql)){
						if ($this->count = $this->result->RecordCount())
								return $this->result->FetchRow();
						else
								return FALSE;
				}else{
						return FALSE;
				}
		}
		function get_specific_signatory($personell_nr){
			global $db;

		    $this->sql ="SELECT s.*, upper(fn_get_personell_name2(personell_nr)) AS name, signatory_position, signatory_title
						 FROM seg_signatory AS s
			             WHERE personell_nr='$personell_nr'";
            $this->result=$db->Execute($this->sql);
			return $this->result->FetchRow();
		}
		#-----------------------

		#added by VAN 05-27-2010, edited by jasper 03-18-2013
		function get_Signatory($document_code, $errorBirth=false){
		    global $db;

		    $select_default = "SELECT s.`end_date` FROM seg_signatory AS s WHERE document_code = 'birthcert' AND s.`is_default` = 1";
            $select_inactive = "SELECT s.`end_date` FROM seg_signatory AS s WHERE document_code = 'birthcert' AND s.`is_active` = 0";
		    $this->result=$db->Execute($select_default);
            $result_inactive=$db->Execute($select_inactive);
            $dt = $this->result->FetchRow();
            $inactive = $result_inactive->FetchRow();
            $default = date('Y-m-d H:i:s');

            if ($dt['end_date'] <= $default) {
                $orderBy = 'ORDER BY is_default DESC';
            } else {
                $orderBy = 'ORDER BY NAME ASC ';
            }

            if($inactive['end_date'] <= $default) {
                $active = 'AND is_active = 1';
            }

		    $this->sql ="SELECT s.*, upper(fn_get_personell_name2(personell_nr)) AS name, signatory_position, signatory_title
						 FROM seg_signatory AS s
			             WHERE document_code=".$db->qstr($document_code)."
			             $active
                         $orderBy";

                         //ORDER BY UPPER(fn_get_personell_name(personell_nr))";
           /* $this->sql ="SELECT s.*, upper(fn_get_personell_name(personell_nr)) AS name, signatory_position, signatory_title
                                        FROM seg_signatory AS s
                                        WHERE document_code='$document_code'";*/

		    // echo "this->sql = ".$this->sql;
		    if ($this->result=$db->Execute($this->sql)){
			    if ($this->result->RecordCount()){
                    if ($errorBirth || ($document_code == 'confcert-ipbm') || ($document_code == 'vacc_cert2') || ($document_code == 'vacc_cert'))
                        return $this->result;
                    else
                        return $this->result->FetchRow();
			    }else
				    return FALSE;
		    }else{
			    return FALSE;
		    }
	    }
		#------------------------

		/**
		* added by VAN 03-01-2011
		* Checks if the PID number (the person) exists as employee in the database.
		* If person exists as employee, its record primary number key will be returned, else FALSE.
		* @access public
		* @param int PID number
		* @return mixed integer  or boolean
		*/
		function is_personnel($pid=0){
				global $db;
				if(!$pid){
						return FALSE;
				}else{
						$this->sql="SELECT ps.*
												FROM care_personell AS ps
												WHERE ((date_exit NOT IN (DATE(NOW())) AND date_exit > DATE(NOW()))
												OR date_exit='0000-00-00' OR date_exit IS NULL)
												AND ((contract_end NOT IN (DATE(NOW())) AND contract_end > DATE(NOW()))
												OR contract_end='0000-00-00' OR contract_end IS NULL)
												AND ps.pid='$pid'";
						if ($this->result=$db->Execute($this->sql)){
							if ($this->count = $this->result->RecordCount())
								return $this->result->FetchRow();
							else
								return FALSE;
						}else{
								return FALSE;
						}
				}
		}

        function getRadTech(){
                global $db;

                $this->sql = "SELECT nr, pr.pid, fn_get_personellname_lastfirstmi(pr.nr) as name
                                                FROM care_personell AS pr
                                                INNER JOIN care_person AS p ON pr.pid=p.pid
                                                WHERE ((pr.job_function_title LIKE '%Radiologic Technologist%')
                                                OR (pr.job_position LIKE '%Radiologic Technologist%')) AND pr.status != 'deleted'
                                                ORDER BY name_last, name_first, name_middle";

                 if ($this->result=$db->Execute($this->sql)){
                    if ($this->result->RecordCount())
                        return $this->result;
                    else
                        return FALSE;
                }else{
                        return FALSE;
                }
        }

        function get_Signatory_Outside($document_code, $role){
            global $db;

            $this->sql ="SELECT * FROM seg_signatory_outside WHERE document_code='$document_code' AND role='$role' LIMIT 1";

            #echo "this->sql = ".$this->sql;
            if ($this->result=$db->Execute($this->sql)){
                if ($this->result->RecordCount()){
                    return $this->result->FetchRow();
                }else
                    return FALSE;
            }else{
                return FALSE;
            }
        }

       //added by jasper 11/18/12
       function getDoctorAccreditation($doctor_nr) {
          global $db;
          $this->sql = "SELECT accreditation_nr FROM seg_dr_accreditation WHERE dr_nr='$doctor_nr'";
          if ($this->result=$db->Execute($this->sql)){
                if ($this->result->RecordCount()){
                    return $this->result->FetchRow();
                }else
                    return FALSE;
            }else{
                return FALSE;
            }
        }
        //added by jasper 11/18/12

        function updatePersonnelNameUsers($personell_nr, $name_person){
            global $db;

            $history = $this->ConcatHistory("Updated the Name ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_userid']."\n");
            $encoder = $_SESSION['sess_temp_userid'];

            #added by fritz 02/12/2020
            $name = $db->GetOne("SELECT name FROM care_users WHERE personell_nr =".$db->qstr($personell_nr)." AND old_name IS NULL");
            $old_name_sql = "";
            if ($name) {
            	$old_name_sql = ",old_name =".$db->qstr($name);
            }
            
            $this->sql = "UPDATE care_users SET
                                name = ".$db->qstr($name_person).",
                                history = ".$history.",
                                modify_id = ".$db->qstr($encoder).",
                                modify_time = NOW() $old_name_sql
                          WHERE personell_nr=".$db->qstr($personell_nr);

            if ($db->Execute($this->sql)) {
                if ($db->Affected_Rows()) {
                    $ret=TRUE;
                }
            }
            if ($ret)    return TRUE;
            else return FALSE;

         }
         function getUser($user){
	global $db;
	$this->sql = "SELECT c.name FROM care_users c WHERE c.login_id = ".$db->qstr($user)."";
			if ($this->result=$db->Execute($this->sql)){
						if ($this->result->RecordCount())
								return $this->result->FetchRow();
						else
								return FALSE;
				}else{
						return FALSE;
				}
}

         # Added by James 4/24/2014
         function getDoctorName($encounter_nr)
         {
         	global $db;

         	$this->sql = "SELECT 
							  fn_get_person_name (cp.pid) AS name
							FROM
							  seg_industrial_transaction sit 
							  INNER JOIN seg_industrial_cert_med sicm 
							    ON sicm.refno = sit.refno 
							  INNER JOIN care_personell cpl 
							    ON cpl.nr = sicm.dr_nr_med 
							  INNER JOIN care_person cp 
							    ON cp.pid = cpl.pid 
							WHERE sit.encounter_nr = ".$db->qstr($encounter_nr);

         	if ($this->result=$db->Execute($this->sql)){
                if ($this->result->RecordCount()){
                    return $this->result->FetchRow();
                }else
                    return FALSE;
            }else{
                return FALSE;
            }
         }

        #added by art 10/13/2014
//comment out by Nick 5-20-2015
//        function isDoctor($personell_nr){
//        	global $db;
//        	$this->sql = "SELECT a.`job_type_nr` FROM care_personell a WHERE a.`nr` = ".$db->qstr($personell_nr)." AND a.`job_type_nr` = 1";
//         	if ($this->result=$db->Execute($this->sql)){
//                if ($this->result->RecordCount()){
//                    return TRUE;
//                }else{
//                	return FALSE;
//                }
//            }else{
//                return FALSE;
//            }
//         }
        #end art

	/**
	 * @author Nick 5-29-2015
	 * @param $personnelNr
	 * @return bool
	 */
	public function isDoctor($personnelNr){
        	global $db;
		$isDoctor = $db->GetOne("SELECT
								   IF(SUBSTRING(short_id, 1, 1) = 'D', 1, 0) AS is_doctor
								 FROM
								   care_personell
								 WHERE nr = ?",$personnelNr);
		return $isDoctor == 1;
    }

    public function isNurse($nr){
        	global $db;
		$is_nurse = $db->GetOne("SELECT
								   IF(SUBSTRING(short_id, 1, 1) = 'N', 1, 0) AS is_nurse
								 FROM
								   care_personell
								 WHERE nr = ?",$nr);
		return $is_nurse == 1;
    }

	public static function getPersonnelSignature($personnelId,$root_path){
		global $db;
		$rs = $db->GetOne("SELECT signature_filename FROM care_personell WHERE nr=?",$personnelId);
		if(!$rs)
			return '';
		return $root_path."fotos/signatures/".$rs;
	}

        function getPersonellByRisId($ris_id){
         	global $db;

         	$this->sql = "SELECT cpl.`nr`, cp.`name_last`, cp.`name_first`, cpl.`doctor_role`
         		FROM care_personell cpl 
         		LEFT JOIN care_person cp ON cp.`pid` = cpl.`pid` 
         		WHERE cpl.`ris_id` = ".$db->qstr($ris_id);

         	if ($this->result=$db->Execute($this->sql)){
                if ($this->result->RecordCount()){
                    return $this->result->FetchRow();
                }else
                    return FALSE;
            }else{
                return FALSE;
            }
         }

    function deleteRisId($personell_nr){
    	global $db;

     	$this->sql = "UPDATE care_personell p
			SET ris_id = NULL
			WHERE p.`nr` = ".$db->qstr($personell_nr);

		if($db->Execute($this->sql)){
            if($db->Affected_Rows()){
                $ret=TRUE;
            }
        }

        if ($ret) return TRUE;
        else return FALSE;
    }

	/**
	 * Hotfix hahayz
	 * @param $personnelNr
	 * @return mixed
	 */
	public static function getDoctorInfo($personnelNr)
	{
		global $db;
		$sql = "SELECT
					  IF (
						p.`other_title` <> ''
						OR NOT ISNULL(p.`other_title`),
						other_title,
						IF(
						  job_function_title = 'Doctor',
						  'MD',
						  IF(
							job_function_title = 'Consulting doctor',
							'MD,FPCR',
							''
						  )
						)
					  ) AS drtitle,
					  p.doctor_role AS drRole,
					  p.doctor_level,
					  CONCAT(
						IFNULL(name_first, ''),
						' ',
						IF (
						  LENGTH(
							CONCAT(SUBSTR(name_middle, 1, 1), '.')
						  ) = 2,
						  CONCAT(SUBSTR(name_middle, 1, 1), '. '),
						  ' '
						),
						IFNULL(name_last, '')
					  ) AS dr_name,
					  job_function_title,
					  job_position,
					  p.license_nr,
					  p.tin,
					  pa.nr,
					  pa.personell_nr,
					  p.pid,
					  pr.title,
					  pr.pid,
					  pr.name_first,
					  pr.name_2,
					  pr.name_middle,
					  pr.name_last,
					  pr.title
					FROM
					  care_personell AS p
					  INNER JOIN care_person AS pr
						ON p.pid = pr.pid
					  LEFT JOIN care_personell_assignment AS pa
						ON pa.personell_nr = p.nr
					WHERE p.nr IN ($personnelNr)
					ORDER BY p.doctor_level DESC ";
		return $db->Execute($sql);
	}
 
   /**
	*   Gets all the seg_dependents
	*   @access public
	*   @return number of dependents:
	*   created by: syboy 02/04/2016 : meow
	*/
	
	function getEmployeeDependents($pid){
    	global $db;
    	$dependent = $db->GetOne("SELECT COUNT(parent_pid) AS dependent FROM seg_dependents WHERE parent_pid = ? AND STATUS IN ('member') ", $pid);
    	return $dependent;
    }

      function getRemarks($pid){
    	global $db;
    	$sql = "SELECT remarks FROM care_personell_remarks WHERE pid = ? ORDER BY create_date DESC LIMIT 1";
    	
     if($this->result=$db->Execute($sql,$pid)){
                            if($this->record_count=$this->result->RecordCount()){
                                 return $this->result;
                            }else{
                                 return FALSE;
                            }
                     }else{
                            return FALSE;
                     }
    }

    /**
	 * @author Arco - 06/02/2016
	 *
	 * Employee Monitoring
	 */

	function employeeMonitoring($data, $action){
		global $db, $HTTP_SESSION_VARS;

		$user = ($data['create_id']) ? $data['create_id'] : $HTTP_SESSION_VARS['sess_user_name'];

		$employee_nr = $db->qstr($data['employee_nr']);
		$employee_pid = $db->qstr($data['employee_pid']);
		$remarks = $db->qstr($data['remarks']);
		$action_taken = $db->qstr($action);
		$action_date = $db->qstr(date('Y-m-d H:i:s'));
		if ($data['checker_for_new_employee'] == 1) {
			$is_new = 1;
		} else {
			$is_new = 0;
		}
		if ($data['remarks'] == "Auto-Locked") {
			$action_personnel = $db->qstr("System");
		} else {
			$action_personnel = $db->qstr($user);
		}

		$this->sql = "INSERT INTO seg_employees_monitoring (employee_nr, employee_pid, remarks, action_taken, action_personnel, action_date, is_new)
					 VALUES ($employee_nr, $employee_pid, $remarks, $action_taken, $action_personnel, $action_date, $is_new)";

		if($res=$db->Execute($this->sql))
			return true;
		else
			return false;
	}
	#Added by Matsuu 12232018
	function getStatusPersonnel($personell_nr=0)
	{
		global $db;
		$this->sql = "SELECT 
							ser.`frombilling` 
							FROM
							`seg_encounter_result` AS ser 
							INNER JOIN `care_encounter` AS ce 
							ON ce.`encounter_nr` = ser.`encounter_nr` 
							INNER JOIN `care_personell` AS cp 
							ON cp.`pid` = ce.`pid` 
							WHERE cp.`nr` = ".$db->qstr($personell_nr)." AND ser.`frombilling` = '1'";
		if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result->FetchRow();
			} else{
				 return FALSE;
			}
	
	}

	function getPersonellTitle($nr){
		global $db;
	
		$this->sql = "SELECT other_title FROM care_personell WHERE nr = " . $db->qstr($nr) . "";
		$title = $db->GetOne($this->sql);
		return $title;

	}

   /*@date 02/12/2020
	*@author fritz
	*@params string name
    */
	function getPersonnelDeptByName($name){
		global $db;

		$this->sql = "SELECT 
						  name_formal 
						FROM
						  care_department AS d 
						  INNER JOIN care_personell_assignment AS a 
						    ON d.nr = a.`location_nr` 
						  INNER JOIN care_users AS u 
						    ON a.`personell_nr` = u.`personell_nr` 
						WHERE IF(
						    NAME = ".$db->qstr($name)." 
						    AND old_name IS NULL,
						    1,
						    IF(old_name = ".$db->qstr($name).", 1, 0)
						  )";
						
		return $db->GetOne($this->sql);
	}
	#END function getPersonnelDeptByName

}/* end of class Personell */
