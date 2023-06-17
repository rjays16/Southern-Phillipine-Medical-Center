<?php
/**
* @package care_api
*/

/**
*/
//require_once($root_path.'include/care_api_classes/class_core.php');
/**
*  GUI person search methods.
* Dependencies:
* assumes the following files are in the given path
* /include/care_api_classes/class_person.php
* /include/care_api_classes/class_paginator.php
* /include/care_api_classes/class_globalconfig.php
* /include/inc_date_format_functions.php
*  Note this class should be instantiated only after a "$db" adodb  connector object  has been established by an adodb instance
* @author Elpidio Latorilla
* @version beta 2.0.1
* @copyright 2002,2003,2004,2005,2005 Elpidio Latorilla
* @package care_api
*/

$thisfile = basename($HTTP_SERVER_VARS['PHP_SELF']);

class GuiSearchPerson {

	# Default value for the maximum nr of rows per block displayed, define this to the value you wish
	# In normal cases the value is derived from the db table "care_config_global" using the "pagin_insurance_list_max_block_rows" element.
	var $max_block_rows =30 ;

	# Set to TRUE if you want to show the option to select  inclusion of the first name in universal searches
	# This would give the user a chance to shut the search for first names and makes the search faster, but the user has one element more to consider
	# If set to FALSE the option will be hidden and both last name and first names will be searched, resulting to slower search
	var $show_firstname_controller = TRUE;

	# Set to TRUE if you want the sql query to be displayed
	# Useful for debugging or optimizing the query
	var $show_sqlquery = FALSE;

	# Set to TRUE to automatically show data if result count is only 1
	var $auto_show_bynumeric = FALSE;
	var $auto_show_byalphanumeric = FALSE;

	# The language tables
	var $langfile = array( 'aufnahme.php', 'personell.php');

	# Initialize some flags
	var $toggle = 0;
	var $mode = '';


	# Set color values for the search mask
	# Default search mask background color
	var $searchmask_bgcolor='#f3f3f3';

	# Default block background color
	var $entry_block_bgcolor='#fff3f3';

	# Default border color
	var $entry_border_bgcolor='#66ee66';

	# Defaut body border color
	var $entry_body_bgcolor='#ffffff';

	# Search key buffer
	var $searchkey='';

	# Optional url parameter to append to target url
	var $targetappend ='';

	# The text holder in front of output block
	var $pretext='';

	# The text holder after the output block
	var $posttext='';

	# script parameters buffer
	var $script_vars = array();

	# Tipps tricks flag
	var $showtips = TRUE;

	# Segworks Addon : sendtoinput
	# added by AJMQ - April 24, 2006
	var $seg_send_to_input = FALSE;
	var $seg_sti_target_window = '';
	var $seg_sti_control_id = '';
	var $seg_sti_close_onclick = TRUE;

	# the type of search (person or personnel)
	# burn added: March 16, 2007
	var $seg_search_type;

	var $closefile='main/startframe.php';
	var $thisfile ='' ;
	var $cancelfile = 'main/startframe.php';
	var $targetfile = '';
	var $searchfile = '';


	# smarty template
	var $smarty;

	# Flag for output or returning form data
	var $bReturnOnly = FALSE;

	/**
	* Constructor
	*/
	function GuiSearchPerson($target='',$filename='',$cancelfile=''){
		global $thisfile, $root_path;
		if(empty($filename)) $this->thisfile = $thisfile;
			else $this->thisfile = $filename;
		if(!empty($cancelfile)) $this->cancelfile = $cancelfile;
			else $this->cancelfile =$root_path.$this->cancelfile;
		if(!empty($target)){
			$this->targetfile = $target;
			$this->withtarget=TRUE;
		}
	}

	/**
	*	SendToInput Addon
	*/
	function issetSendToInput() {
		return $seg_send_to_input == TRUE;
	}

	function prepareSendToInput($target='', $control='') {
		$this->seg_send_to_input = TRUE;
		$this->seg_sti_target_window = $target;
		$this->seg_sti_control_id = $control;
	}

	/**
	* Sets the target file of each listed item
	*/
	function setTargetFile($target){
		$this->targetfile = $target;
	}
	/**
	* Sets the file name of the script where this gui is  being displayed
	*/
	function setThisFile($target){
		$this->targetfile = $target;
	}
	/**
	* Sets the file name of the script to run when the search button is pressed
	*/
	function setSearchFile($target){
		$this->searchfile = $target;
	}
	/**
	* Sets the file name of the script to run when the cancel button is pressed
	*/
	function setCancelFile($target){
		$this->cancelfile = $target;
	}
	/**
	* Appends a string of url parameters to the target url
	*/
	function appendTargetUrl($str){
		$this->targetappend = $this->targetappend.$str;
	}
	/**
	* Sets the prompt text string
	*/
	function setPrompt($str){
		$this->prompt = $str;
	}

	/**
	* Sets the type of search (person or personnel)
	* burn added: March 16, 2007
	*/
	function setSearchType($str){
		$this->seg_search_type = $str;
	}

	/**
	* Displaying the GUI
	*/

	function display($skey=''){
		global 	$db, $searchkey, $root_path,  $firstname_too, $HTTP_POST_VARS, $HTTP_GET_VARS,
				$sid, $lang, $mode,$totalcount, $pgx, $odir, $oitem, $HTTP_SESSION_VARS,
				$dbf_nodate,  $user_origin, $parent_admit, $status, $target, $origin;

		include($root_path . 'include/inc_ipbm_permissions.php');
		
		#added by VAN 05-06-08
		include_once($root_path.'include/care_api_classes/class_encounter.php');
		# Create encounter object
		$encounter_obj=new Encounter();

		#added by VAN 05-13-08
		require_once($root_path.'include/care_api_classes/class_social_service.php');
		$objSS = new SocialService;

		require_once($root_path.'include/care_api_classes/class_department.php');
		$dept_obj=new Department;

		global $ptype, $allow_patient_register, $allow_newborn_register, $allow_er_user, $allow_opd_user, $allow_ipd_user, $allow_phs_user, $allow_medocs_user, $allow_UpdatePatientD, $allow_update;

		if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
			$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
		else
			$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
		$user_dept_info = $dept_obj->getUserDeptInfo($seg_user_name);

		$HTTP_SESSION_VARS['dept'] = $user_dept_info['id'];

		#echo "<br>search ptype = ".$ptype;

#echo "class_gui_search_person.php : A searchkey = '".$searchkey."' <br> \n";
#echo "class_gui_search_person.php : basename(HTTP_SERVER_VARS['PHP_SELF']) <br> ";
#echo basename($HTTP_SERVER_VARS['PHP_SELF'])." <br> \n";
#echo "class_gui_search_person.php : HTTP_SERVER_VARS['PHP_SELF'] = '".$HTTP_SERVER_VARS['PHP_SELF']."' <br> \n";
#echo "class_gui_search_person.php : thisfile = '".$thisfile."' <br> \n";
#echo "class_gui_search_person.php : filename = '".$filename."' <br> \n";
		$this->thisfile = $filename;
		$this->searchkey = $skey;
		$this->mode = $mode;
#echo "class_gui_search_person.php : this->thisfile = '".$this->thisfile."' <br> \n";
		if(empty($this->targetfile)){
			$withtarget = FALSE;
			$navcolspan = 5;
		}else{
			$withtarget = TRUE;
			$navcolspan = 6;
		}

		if(!empty($skey)) $searchkey = $skey;

		# Load the language tables
		$lang_tables =$this->langfile;
		include($root_path.'include/inc_load_lang_tables.php');

		if ($_GET['ptype'])
			$ptype = $_GET['ptype'];
		elseif ($HTTP_SESSION_VARS['ptype'])
			$ptype = $HTTP_SESSION_VARS['ptype'];

		$HTTP_SESSION_VARS['ptype'] = $ptype;

	#echo "<br>search 2 ptype = ".$ptype;

		# Initialize pages control variables
		if($mode=='paginate'){
			$searchkey=$HTTP_SESSION_VARS['sess_searchkey'];

			if (!empty($HTTP_SESSION_VARS['ptype']))
				$ptype = $HTTP_SESSION_VARS['ptype'];
			//$searchkey='USE_SESSION_SEARCHKEY';
			//$mode='search';
		}else{
			# Reset paginator variables
			$pgx=0;
			$totalcount=0;
			$odir='';
			$oitem='';
		}
#echo "search = ".$searchkey;
		# Create an array to hold the config values
		$GLOBAL_CONFIG=array();


		#Load and create paginator object
		include_once($root_path.'include/care_api_classes/class_paginator.php');
		#echo "<br>pgx = ".$pgx;
		#echo "<br>thisfile = ".$this->thisfile;
		#echo "<br>key = ".$HTTP_SESSION_VARS['sess_searchkey'];
		$pagen=new Paginator($pgx,$this->thisfile,$HTTP_SESSION_VARS['sess_searchkey'],$root_path);
		

		include_once($root_path.'include/care_api_classes/class_globalconfig.php');
		$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('person_id_%');

		# Get the max nr of rows from global config
		$glob_obj->getConfig('pagin_person_search_max_block_rows');
		if(empty($GLOBAL_CONFIG['pagin_person_search_max_block_rows'])){
			# Last resort, use the default defined at the start of this page
			$pagen->setMaxCount($max_block_rows);
		}else{
			$pagen->setMaxCount($GLOBAL_CONFIG['pagin_person_search_max_block_rows']);
		}

		//$db->debug=true;

		if(!defined('SHOW_FIRSTNAME_CONTROLLER')) define('SHOW_FIRSTNAME_CONTROLLER',$this->show_firstname_controller);

		if(SHOW_FIRSTNAME_CONTROLLER){
			if(isset($HTTP_POST_VARS['firstname_too'])){
				if($HTTP_POST_VARS['firstname_too']){
					$firstname_too=1;
				}elseif($mode=='paginate'&&isset($HTTP_GET_VARS['firstname_too'])&&$HTTP_GET_VARS['firstname_too']){
					$firstname_too=1;
				}
			}elseif($mode!='search'){
				$firstname_too=TRUE;
			}
		}
		#echo "id = ".$user_dept_info['id'];
		#added by VAN 05-06-08

		#if ($user_dept_info['id']=='OPD-Triage'){
		/*
		if ($allow_opd_user){
			if (empty($this->mode))
				$this->mode='search';

			if (empty($mode))
				$mode ='search';
		}
		*/
#echo "class_gui_search_person.php : 1 searchkey = '".$searchkey."'; mode = '".$mode."' <br> \n";
		#if(($this->mode=='search' || $this->mode=='paginate') && !empty($searchkey)){
		if($this->mode=='search' || $this->mode=='paginate'){
#echo "<br>key = ".$searchkey;
			# Translate *? wildcards
			$searchkey=strtr($searchkey,'*?','%_');
#echo "<br>key = ".$searchkey;
			include_once($root_path.'include/inc_date_format_functions.php');
#echo "<br>class_gui_search_person : C date_format = '".$date_format."' <br><br> \n";
#echo "<br>class_gui_search_person : C getDateFormat() = '".getDateFormat()."' <br><br> \n";

			include_once($root_path.'include/care_api_classes/class_person.php');
			$person=& new Person();

			#added by VAN 02-21-08
			#echo "<br>oitem = ".$oitem."<br>";
			#echo "<br>odir = ".$odir."<br>";

			if (empty($oitem))
				$oitem = 'date_reg';
			if (empty($odir))
				$odir = 'DESC';

			# Set the sorting directive
			#if(isset($oitem)&&!empty($oitem)) $sql3 =" ORDER BY $oitem $odir";
			#if(isset($oitem)&&!empty($oitem))

			$sql3 =" ORDER BY name_last ASC, name_first ASC";

			#edited by VAN 04-16-08
			#if(isset($oitem)&&!empty($oitem)) $sql3 =" ORDER BY cp.pid DESC, cp.name_last ASC, $oitem $odir";

			if($mode=='paginate'){
				$fromwhere=$HTTP_SESSION_VARS['sess_searchkey'];
					#commented by VAN 06-26-08
					/*
					$sql="SELECT cp.senior_ID, cp.pid, cp.name_last, cp.name_first, cp.date_birth, cp.addr_zip, cp.sex, cp.death_date, cp.fromtemp,cp.status ".
						" , sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name ".
						" FROM ".$fromwhere.$sql3;   # burn added: March 8, 2007
					*/

					$sql="SELECT SQL_CALC_FOUND_ROWS dep.dependent_pid, ps.nr AS personnelID, cp.civil_status, cp.senior_ID, cp.fromtemp, cp.pid,cp.name_last,cp.name_first,cp.name_middle,cp.date_birth,cp.addr_zip, cp.sex,cp.death_date,cp.death_encounter_nr, cp.status,cp.street_name,
														sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name,IF(fn_calculate_age(NOW(),cp.date_birth),fn_get_age(NOW(),cp.date_birth),age) AS age, cp.sex,
														SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_nr)),20) AS encounter_nr,
														SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.er_opd_diagnosis)),20) AS er_opd_diagnosis,
														SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_type)),20) AS encounter_type,
														SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.current_ward_nr)),20) AS current_ward_nr,
														SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.current_room_nr)),20) AS current_room_nr,
														SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.current_dept_nr)),20) AS current_dept_nr,
														SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.is_medico)),20) AS is_medico,
														SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.admission_dt)),20) AS admission_dt,
														SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.discharge_date)),20) AS discharge_date,

														IF (((dep.dependent_pid IS NOT NULL) OR (ps.nr IS NOT NULL)),'PHS',
																	IF((SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_type)),20) IS NULL),'',
																		(IF((SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_type)),20)=2),
																			SUBSTRING(MAX(CONCAT(scp.grant_dte,scp.discountid)),20),
																			SUBSTRING(MAX(CONCAT(se.grant_dte,se.discountid)),20))))) AS discountid,
														IF (((dep.dependent_pid IS NOT NULL) OR (ps.nr IS NOT NULL)),'1',
																	IF((SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_type)),20) IS NULL),'',
																		(IF((SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_type)),20)=2),
																		SUBSTRING(MAX(CONCAT(scp.grant_dte,scp.discount)),20),
																		SUBSTRING(MAX(CONCAT(se.grant_dte,se.discount)),20))))) AS discount ".
														" FROM ".$fromwhere.$sql3;
				#echo "sql = ".$sql;
				$ergebnis=$db->SelectLimit($sql,$pagen->MaxCount(),$pagen->BlockStartIndex());
				$linecount=$ergebnis->RecordCount();

			}else{

#echo "class_gui_search_person.php : 2 searchkey = '".$searchkey."'; mode = '".$mode."' <br> \n";

				$ergebnis=$person->SearchSelect($searchkey,$pagen->MaxCount(),$pagen->BlockStartIndex(),$oitem,$odir,$firstname_too,$ptype);
#echo "sql2 = ".$person->sql;
#echo "<br>rec_count= '".$person->rec_count."'";
#echo "class_gui_search_person.php : ergebnis : <br> "; print_r($ergebnis); echo " <br> \n";
				#Retrieve the sql fromwhere portion
				$fromwhere=$person->buffer;

				#added by VAN 05-07-08
				$fromwhere2=$person->buffer2;

#echo "<br>class_gui_search_person.php : fromwhere = '".$fromwhere."' <br> \n";

				$HTTP_SESSION_VARS['sess_searchkey']=$fromwhere;

				$sql=$person->getLastQuery();
#echo "sql = ".$sql3;
#echo "<br>class_gui_search_person.php : sql = '".$sql."' <br> \n";
				$linecount=$person->LastRecordCount();
			}

			if($ergebnis){
/*					# burn comment; March 13, 2007
				if($linecount==1){
					if(( $this->auto_show_bynumeric && $person->is_nr) || $this->auto_show_byalphanumeric  ){
						$zeile=$ergebnis->FetchRow();
						header("location:".$this->targetfile."?sid=".$sid."&lang=".$lang."&pid=".$zeile['pid']."&edit=1&status=".$status."&user_origin=".$user_origin."&noresize=1&mode=");
						exit;
					}
				}
*/
				$pagen->setTotalBlockCount($linecount);

				# If more than one count all available
				if(isset($totalcount)&&$totalcount){
					$pagen->setTotalDataCount($totalcount);
				}else{
					# Count total available data
					$sql='SELECT COUNT(cp.pid) AS maxnr FROM '.$fromwhere;
					#echo "sql = ".$sql;
					#edited by VAN
					/*
					if ($fromwhere2){
						$sql='SELECT COUNT(cp.pid) AS maxnr FROM '.$fromwhere.
								 ' UNION ALL SELECT COUNT(cp.pid) AS maxnr FROM '.$fromwhere2;
					}else{
						$sql='SELECT COUNT(cp.pid) AS maxnr FROM '.$fromwhere;
					}
					*/
#echo "<br>sql= '".$sql."'";
					if($result=$db->Execute($sql)){
						$totalcount=$result->RecordCount();   #burn added, October 18, 2007
/*							   #burn commented, October 18, 2007
						if ($result->RecordCount()) {
							$rescount=$result->FetchRow();   #burn commented, October 18, 2007
							$totalcount=$rescount['maxnr'];   #burn commented, October 18, 2007
						}
*/
					}
					$pagen->setTotalDataCount($totalcount);
				}
#echo "<br>totalcount= '".$totalcount."'<br>linecount= '".$linecount."'";
				# Set the sort parameters
				$pagen->setSortItem($oitem);
				$pagen->setSortDirection($odir);
			}else{
				if($show_sqlquery) echo $sql;
			}

		} else {
			$mode='';
		}

		$entry_block_bgcolor=$this->entry_block_bgcolor;
		$entry_border_bgcolor=$this->entry_border_bgcolor;
		$entry_body_bgcolor=$this->entry_body_bgcolor;
		$searchmask_bgcolor= $this->searchmask_bgcolor;


		##############  Here starts the html output

		# Start Smarty templating here
		# Create smarty object without initiliazing the GUI (2nd param = FALSE)

		include_once($root_path.'gui/smarty_template/smarty_care.class.php');
		$this->smarty = new smarty_care('common',FALSE);

		# Output any existing text before the search block
		if(!empty($this->pretext)) $this->smarty->assign('sPretext',$this->pretext);

		# Show tips and tricks link and the javascript
		if($this->showtips){
			ob_start();
				include_once($root_path.'include/inc_js_gethelp.php');
				$sTemp = ob_get_contents();
				$this->smarty->assign('sJSGetHelp',$sTemp);
			ob_end_clean();

			$this->smarty->assign('LDTipsTricks','<a href="javascript:gethelp(\'person_search_tips.php\')">'.$LDTipsTricks.'</a>');

		}

		$uri = $_SERVER['REQUEST_URI'];
		$i = stripos($uri, "/", 1);
		$uri = substr($uri, 0, $i+1);
		$url = $_SERVER['SERVER_ADDR'].$uri;

        $this->smarty->assign('sJSBiometricSearch', '
                    <script type="text/javascript" src="'.$root_path.'js/socket.io.js"></script>
                    <script type="text/javascript" src="'.$root_path.'modules/biometric/js/searchFingerprint.js">
                    </script><script language="javascript">
                        function launchFPSearch(clientid) {
                            initFPSocket(clientid, \''.BIOMETRIC_SOCKET_SERVER.'\');
                            window.location.href="https://'.$url.'modules/biometric/launchSearchFingerprint.php?&clientId="+clientid;
                        }
                        
                        function showFoundPatientProfile() {
                            var fpid = document.getElementById(\'foundPid\').value;
                            window.location.replace("'.$this->targetfile.'?&pid="+fpid+"&edit=1&status='.$status.'&target='.$target.'&user_origin='.$user_origin.'&noresize=1&ptype='.$ptype.'&mode='.$IPBMextend.'");
                        }
                    </script>');
                                
        #
        # Prepare the javascript validator
        #                                

	#if ($user_dept_info['id']=='OPD-Triage'){
	if ($allow_opd_user){
		if(!isset($searchform_count) || !$searchform_count){
			$this->smarty->assign('sJSFormCheck','<script language="javascript">

				function isValidSearch(key) {

					if (typeof(key)==\'undefined\') return false;
					var s=key.toUpperCase();
					return (
						/^[A-ZÑ\-\.]{2}[A-ZÑ\-\. ]*\s*,\s*[A-ZÑ\-\.]{2}[A-ZÑ\-\. ]*$/.test(s) ||
						/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
						/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
						/^\d+$/.test(s)
					);
				}

				function chkSearch(d){
					//if((d.searchkey.value=="") || (d.searchkey.value==" ") || (d.searchkey.value.length < 3)){
					if (!isValidSearch(d.searchkey.value)) {
						d.searchkey.focus();
						return false;
					}else	{
						return true;
					}
				}

				function DisabledSearch(){
					/*
					if (document.getElementById("searchkey").value.length < 3){
						document.getElementById("searchButton").style.cursor="default";
						document.getElementById("searchButton").disabled = true;
					}else{
						document.getElementById("searchButton").style.cursor="pointer";
						document.getElementById("searchButton").disabled = false;
					}
					*/

					var b=isValidSearch(document.getElementById(\'searchkey\').value);
					document.getElementById("searchButton").style.cursor=(b?"pointer":"default");
					document.getElementById("searchButton").disabled = !b;
				}

			</script>');
		}
	}else{
		if(!isset($searchform_count) || !$searchform_count){
			$this->smarty->assign('sJSFormCheck','<script language="javascript">

				function isValidSearch(key) {
					if (typeof(key)==\'undefined\') return false;
					var s=key.toUpperCase();
					return (
						/^[A-ZÑ\-\.]{2}[A-ZÑ\-\. ]*\s*,\s*[A-ZÑ\-\.]{2}[A-ZÑ\-\. ]*$/.test(s) ||
						/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
						/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
						/^\d+$/.test(s)
					);
				}

				function chkSearch(d){
					//if((d.searchkey.value=="") || (d.searchkey.value==" ") || (d.searchkey.value.length < 3)){
					if (!isValidSearch(d.searchkey.value)) {
						d.searchkey.focus();
						return false;
					}else  {
						return true;
					}
				}

				function DisabledSearch(){
					/*
					if (document.getElementById("searchkey").value.length < 3){
						document.getElementById("searchButton").style.cursor="default";
						document.getElementById("searchButton").disabled = true;
					}else{
						document.getElementById("searchButton").style.cursor="pointer";
						document.getElementById("searchButton").disabled = false;
					}
					*/

					var b=isValidSearch(document.getElementById(\'searchkey\').value);
					document.getElementById("searchButton").style.cursor=(b?"pointer":"default");
					document.getElementById("searchButton").disabled = !b;
				}

			</script>');
		}
	}

		#
		# Prepare the search file
		#
		if(empty($this->searchfile)) $search_script = $this->thisfile;
			else $search_script = $this->searchfile;

		#
		# Prepare the form params
		#
		$sTemp = 'method="post" name="searchform';
		if($searchform_count) $sTemp = $sTemp."_".$searchform_count;
		$sTemp = $sTemp.'" onSubmit="return chkSearch(this)"';
		 if(isset($search_script) && $search_script!='') $sTemp = $sTemp.' action="'.$search_script.'"';
		$this->smarty->assign('sFormParams',$sTemp);
#print_r($user_dept_info);
#echo "id = ".$user_dept_info['id'];
		#if ($user_dept_info['id']=='OPD-Triage'){
		if ($allow_opd_user){
			if(empty($this->prompt)) $searchprompt=$LDEntryPrompt." <br>To search all paid patients as of today, just leave the search textbox blank.";
			else $searchprompt=$this->prompt." <br>To search all paid patients as of today, just leave the search textbox blank.";
		}else{
			if(empty($this->prompt)) $searchprompt=$LDEntryPrompt;
			else $searchprompt=$this->prompt;
		}
		/*
		if(empty($this->prompt)) $searchprompt=$LDEntryPrompt." <br>To search all paid patients as of today, just leave the search textbox blank";
			else $searchprompt=$this->prompt." <br>To search all paid patients as of today, just leave the search textbox blank";
		*/
		//$searchprompt=$LDEnterEmployeeSearchKey;
		$this->smarty->assign('searchprompt',$searchprompt);

		#
		# Prepare the checkbox input
		#
		if(defined('SHOW_FIRSTNAME_CONTROLLER')&&SHOW_FIRSTNAME_CONTROLLER){
			if(isset($firstname_too)&&$firstname_too) $sTemp= 'checked';
			$this->smarty->assign('sCheckBoxFirstName','<input type="checkbox" name="firstname_too" '.$sTemp.'>');
			$this->smarty->assign('LDIncludeFirstName',$LDIncludeFirstName);
		}
                
                require_once($root_path."include/care_api_classes/biometric/class_biometric.php");
                $cClientId = Biometric::uniqidReal();                 

		if (!isset($target) || $target != 'personell_reg') {
			$searchFPButton = '<input name="searchFPButton" id="searchFPButton" type="image" onclick="launchFPSearch(\''.$cClientId.'\');" '.createLDImgSrc($root_path,'fingerprintsearch.gif','0','absmiddle').'>';
		}
		else {
			$searchFPButton = '';
		}
		
		#
		# Prepare the hidden inputs
		#
		$this->smarty->assign('sHiddenInputs','<input name="searchButton" id="searchButton" type="image" '.createLDImgSrc($root_path,'searchlamp.gif','0','absmiddle').'>
                                &nbsp;'.$searchFPButton.'
				<input type="hidden" name="sid" value="'.$sid.'">
				<input type="hidden" name="lang" value="'.$lang.'">
				<input type="hidden" name="noresize" value="'.$noresize.'">
				<input type="hidden" name="target" value="'.$target.'">
				<input type="hidden" name="user_origin" value="'.$user_origin.'">
				<input type="hidden" name="origin" value="'.$origin.'">
				<input type="hidden" name="retpath" value="'.$retpath.'">
				<input type="hidden" name="aux1" value="'.$aux1.'">
				<input type="hidden" name="ipath" value="'.$ipath.'">
				<input type="hidden" name="mode" value="search">
                                <input type="hidden" id="foundPid" name="foundPid" value="">');

		#commented by VAN 04-17-08
		#$this->smarty->assign('sCancelButton','<a href="'.$this->cancelfile.URL_APPEND.'"><img '.createLDImgSrc($root_path,'cancel.gif','0').'></a>');

		//include($root_path.'include/inc_patient_searchmask.php');
		#
		# Create append data for previous and next page links
		#
#echo "class_gui_search_person.php : this->targetappend 1 = '".$this->targetappend."' <br> \n";
		$this->targetappend.="&firstname_too=$firstname_too&origin=$origin".$IPBMextend;
#echo "class_gui_search_person.php : this->targetappend 2 = '".$this->targetappend."' <br> \n";
		//echo $mode;
		if($parent_admit) $bgimg='tableHeaderbg3.gif';
			else $bgimg='tableHeader_gr.gif';
		$tbg= 'background="'.$root_path.'gui/img/common/'.$theme_com_icon.'/'.$bgimg.'"';

		if($mode=='search'||$mode=='paginate'){
			if ($linecount) $this->smarty->assign('LDSearchFound',str_replace("~no.~",$totalcount,$LDSearchFound).' '.$LDShowing.' '.$pagen->BlockStartNr().' '.$LDTo.' '.$pagen->BlockEndNr().'.');
				else $this->smarty->assign('LDSearchFound',str_replace('~no.~','0',$LDSearchFound));
		}

		if ($linecount){

			$this->smarty->assign('bShowResult',TRUE);

			$img_male=createComIcon($root_path,'spm.gif','0');
			$img_female=createComIcon($root_path,'spf.gif','0');
            /*
			$this->smarty->assign('LDRegistryNr',$pagen->makeSortLink($LDRegistryNr,'pid',$oitem,$odir,$this->targetappend));
			$this->smarty->assign('LDSex',$pagen->makeSortLink($LDSex,'sex',$oitem,$odir,$this->targetappend));
			$this->smarty->assign('LDLastName',$pagen->makeSortLink($LDLastName,'name_last',$oitem,$odir,$this->targetappend));
			$this->smarty->assign('LDFirstName',$pagen->makeSortLink($LDFirstName,'name_first',$oitem,$odir,$this->targetappend));
			$this->smarty->assign('LDMiddleName',$pagen->makeSortLink("Middle Name",'name_middle',$oitem,$odir,$this->targetappend));
			$this->smarty->assign('LDBday',$pagen->makeSortLink($LDBday,'date_birth',$oitem,$odir,$this->targetappend));
			$this->smarty->assign('segBrgy',$pagen->makeSortLink("Barangay",'brgy_name',$oitem,$odir,$this->targetappend));   # burn added: March 8, 2007
			$this->smarty->assign('segMuni',$pagen->makeSortLink("Muni/City",'mun_name',$oitem,$odir,$this->targetappend));   # burn added: March 8, 2007
#			$this->smarty->assign('LDZipCode',$pagen->makeSortLink($LDZipCode,'addr_zip',$oitem,$odir,$this->targetappend));   # burn commented: March 8, 2007
			$this->smarty->assign('LDZipCode',$pagen->makeSortLink($LDZipCode,'zipcode',$oitem,$odir,$this->targetappend));   # burn added: March 8, 2007
			*/
            
            #edited by VAN 12-20-2011
            $this->smarty->assign('LDRegistryNr',$LDRegistryNr);
            $this->smarty->assign('LDSex',$LDSex);
            $this->smarty->assign('LDLastName',$LDLastName);
            $this->smarty->assign('LDFirstName',$LDFirstName);
            $this->smarty->assign('LDMiddleName','Middle Name');
            $this->smarty->assign('LDBday',$LDBday);
            $this->smarty->assign('segBrgy','Barangay');   # burn added: March 8, 2007
            $this->smarty->assign('segMuni','Muni/City');   # burn added: March 8, 2007
#            $this->smarty->assign('LDZipCode',$pagen->makeSortLink($LDZipCode,'addr_zip',$oitem,$odir,$this->targetappend));   # burn commented: March 8, 2007
            $this->smarty->assign('LDZipCode',$LDZipCode);   # burn added: March 8, 2007
            
			if(!empty($this->targetfile)){
				$this->smarty->assign('LDOptions',$LDOptions.'');
			}

			$sTemp = '';
			$toggle=0;
				# sets the date in 'MM/dd/yyyy' format
			$date_format = getDateFormat();   # burn added: May 19, 2007
			while($zeile=$ergebnis->FetchRow()){
					#print_r($zeile);
				#if($zeile['status']=='' || $zeile['status']=='normal' || $zeile['status']=='migrated'){
				if($zeile['status']=='' || $zeile['status']=='normal'){

					$this->smarty->assign('toggle',$toggle);
					$toggle = !$toggle;

#	echo " zeile['pid'] = '".$zeile['pid']."' ; admitted = '".$encounter_obj->isCurrentlyAdmitted($zeile['pid'],'_PID')."' <br> \n";
#echo " encounter_obj->sql = '".$encounter_obj->sql."' <br> \n";
						# burn added: March 15, 2007
					$label='';
					#echo "<br>type = ".$zeile['encounter_type'];
					if ( $encounter_obj->isCurrentlyAdmitted($zeile['pid'],'_PID') &&
							($enc_row = $encounter_obj->getLastestEncounter($zeile['pid'])) ){
							#echo " encounter_obj->sql = '".$encounter_obj->sql."' <br> \n";
							#echo "<br>type = ".$encounter_type;
#					if ($enc_row = $encounter_obj->getLastestEncounter($zeile['pid'])){
						#if($enc_row['encounter_type']==1){
						if($zeile['encounter_type']==1){
							$label =	'<img '.createComIcon($root_path,'flag_red.gif').'>'.
										'<font size=1 color="red">ER</font>';
						}elseif($zeile['encounter_type']==2){
							$label =	'<img '.createComIcon($root_path,'flag_blue.gif').'>'.
										'<font size=1 color="blue">OPD</font>';
						}elseif(($zeile['encounter_type']==3)||($zeile['encounter_type']==4)){
							$label =	'<img '.createComIcon($root_path,'flag_green.gif').'>'.
										'<font size=1 color="green">IPD</font>';
						}
					#added by VAN 12-30-08
					}elseif($enc_row = $encounter_obj->getLastestEncounter($zeile['pid'])){
						if($zeile['encounter_type']==1){
							$label =	'<img '.createComIcon($root_path,'flag_red.gif').'>'.
										'<font size=1 color="red">ER</font>';
						}elseif($zeile['encounter_type']==2){
							$label =	'<img '.createComIcon($root_path,'flag_blue.gif').'>'.
										'<font size=1 color="blue">OPD</font>';
						}elseif($zeile['encounter_type'] == IPBMIPD_enc){
							$label =	'<img '.createComIcon($root_path,'flag_green.gif').'>'.
										'<font size=1 color="green">IPBM - IPD</font>';
						}elseif($zeile['encounter_type']==IPBMOPD_enc){
							$label =	'<img '.createComIcon($root_path,'flag_blue.gif').'>'.
										'<font size=1 color="blue">IPBM - OPD</font>';
						}elseif(($zeile['encounter_type']==3)||($zeile['encounter_type']==4)){
							$label =	'<img '.createComIcon($root_path,'flag_green.gif').'>'.
										'<font size=1 color="green">IPD</font>';
						}elseif($zeile['encounter_type']==6){ #added by art 02/12/2014
							$label =	'<img '.createComIcon($root_path,'flag_blue.gif').'>'.
										'<font size=1 color="blue">IC</font>';				
						}

					}else{
						$enc_row['encounter_type']=0;   # no ACTIVE encounter
					}

					$this->smarty->assign('sRegistryNr',$zeile['pid']." ".$label);

					switch(strtolower($zeile['sex'])){
						case 'f': $this->smarty->assign('sSex','<img '.$img_female.'>'); break;
						case 'm': $this->smarty->assign('sSex','<img '.$img_male.'>'); break;
						default: $this->smarty->assign('sSex','&nbsp;'); break;
					}
					#echo $zeile['name_middle'];
					$this->smarty->assign('sLastName',ucfirst($zeile['name_last']));
					$this->smarty->assign('sFirstName',ucfirst($zeile['name_first']));

					$this->smarty->assign('sMiddleName',ucfirst($zeile['name_middle']));
					#
					# If person is dead show a black cross
					#
					if($zeile['death_date']&&$zeile['death_date']!=$dbf_nodate) $this->smarty->assign('sCrossIcon','<img '.createComIcon($root_path,'blackcross_sm.gif','0','absmiddle').'>');
						else $this->smarty->assign('sCrossIcon','');
					#edited by VAN 05-26-08
					#if(($zeile['death_date']&&$zeile['death_date']!=$dbf_nodate) || $zeile['is_DOA'])
					#	$this->smarty->assign('sCrossIcon','<img '.createComIcon($root_path,'blackcross_sm.gif','0','absmiddle').'>');
					#else
					#	$this->smarty->assign('sCrossIcon','');

					$date_birth = @formatDate2Local($zeile['date_birth'],$date_format);
					$bdateMonth = substr($date_birth,0,2);
					$bdateDay = substr($date_birth,3,2);
					$bdateYear = substr($date_birth,6,4);
					if (!checkdate($bdateMonth, $bdateDay, $bdateYear)){
						//echo "invalid birthdate! <br> \n";
						$date_birth='';
					}
#					$this->smarty->assign('sBday',formatDate2Local($zeile['date_birth'],$date_format));   # burn commented: March 26, 2007
					$this->smarty->assign('sBday',$date_birth);   # burn added: March 26, 2007
					$this->smarty->assign('sBrgy',$zeile['brgy_name']);   # burn added: March 8, 2007
					$this->smarty->assign('sMuni',$zeile['mun_name']);   # burn added: March 8, 2007

#					$this->smarty->assign('sZipCode',$zeile['addr_zip']);   # burn commented: March 8, 2007
					$this->smarty->assign('sZipCode',$zeile['zipcode']);   # burn added: March 8, 2007

						# burn added: March 16, 2007
					#if ( ($user_dept_info['dept_nr']==150) &&
					/*
					if ( ((($allow_opd_user) && ($ptype=='opd'))||(($allow_phs_user) && ($ptype=='phs'))) &&
							(($enc_row['encounter_type']==0) || (($enc_row['encounter_type']==2))) || ($enc_row['is_discharged'])
						){

						#added by VAN 05-06-08
						$name = trim($zeile['name_first'])." ".trim($zeile['name_last']);
						$encounter_obj->getPatientOPDORNoforADay($zeile['pid'],$name);

						$patSS = $objSS->getPatientSocialClass($zeile['pid']);
						#edited by VAN 06-25-08
						$allow_show_details=TRUE;
					#}elseif( ($user_dept_info['dept_nr']==149) &&
					}elseif( ($allow_er_user) && ($ptype=='er') &&
								#(($enc_row['encounter_type']==0) || $enc_row['encounter_type']==1)
								#edited by VAN 12-18-08
								(($enc_row['encounter_type']==0) || ($enc_row['encounter_type']==1) || ($enc_row['encounter_type']==2) || ($enc_row['is_discharged']))
							 ){
						$allow_show_details=TRUE;   # search under ER Triage
					#}elseif(($user_dept_info['dept_nr']==148)||($user_dept_info['dept_nr']==151)){
					}elseif((($allow_ipd_user)||($allow_medocs_user))&& ($ptype=='ipd') || (($enc_row['encounter_type']==0) || ($enc_row['encounter_type']==2) || ($enc_row['is_discharged']))){
						$allow_show_details=TRUE;   # search under Admitting section or Medical Records

					}#added by VAN 06-25-08
					#elseif($user_dept_info['dept_nr']==174){
					elseif(($allow_newborn_register)&& ($ptype=='ipd')){
						#echo "fromtemp = ".$zeile['fromtemp'];
						if ($zeile['fromtemp'])
							$allow_show_details=TRUE;   # search under Birth section or Medical Records
						else
							$allow_show_details=FALSE;
					}else{
						$allow_show_details=FALSE;   # User has no permission to VIEW person's details
					}
					*/

					if ( ($allow_opd_user) &&
							(($enc_row['encounter_type']==0) || $enc_row['encounter_type']==2)
						){

						$name = trim($zeile['name_first'])." ".trim($zeile['name_last']);
						$encounter_obj->getPatientOPDORNoforADay($zeile['pid'],$name);

						$patSS = $objSS->getPatientSocialClass($zeile['pid']);
						$allow_show_details=TRUE;
                    }elseif ($allow_phs_user){
                        $allow_show_details=TRUE;    
					}elseif($allow_er_user){
						$allow_show_details=TRUE;   # search under ER Triage
						#for IC Clinic
					}elseif($allow_UpdatePatientD){
						$allow_show_details=TRUE;
					}elseif(($allow_ipd_user)||($allow_medocs_user)){
						$allow_show_details=TRUE;   # search under Admitting section or Medical Records
					}elseif($allow_newborn_register){
						if ($zeile['fromtemp'])
							$allow_show_details=TRUE;   # search under Birth section or Medical Records
						else
							$allow_show_details=FALSE;
					}else{
						$allow_show_details=FALSE;   # User has no permission to VIEW person's details
					}

					if ($this->seg_search_type == 'personnel'){
						$allow_show_details=TRUE;   # search under Personnel Management
					}

					if ($this->seg_send_to_input) {
						$control_id = $this->seg_sti_control_id;
						if ($this->seg_sti_target_window == "parent")
							$docTarget = "window.parent.document.";
						elseif ($this->seg_sti_target_window == "opener")
							$docTarget = "window.opener.document.";
						elseif ($this->seg_sti_target_window == "")
							$docTarget = "document.";
						else
							$docTarget = $this->seg_sti_target_window.".document.";
						$sTarget = "<a href=\"#\" onclick=\"" . $docTarget . "getElementById('".$control_id."_text').value='".$zeile['name_first']." ".$zeile['name_last']."';";
						$sTarget .= $docTarget . "getElementById('".$control_id."_id').value='".$zeile['pid'] . "';";
						if ($this->seg_sti_close_onclick)	$sTarget .= "window.close();";

						$sTarget .= "\">";

						//$sTarget = "<a href=\"$this->targetfile".URL_APPEND."&pid=".$zeile['pid']."&edit=1&status=".$status."&target=".$target."&user_origin=".$user_origin."&noresize=1&mode=\">";
						$sTarget=$sTarget.'<img '.createLDImgSrc($root_path,'ok_small.gif','0').' title="'.$LDShowDetails.'"></a>';
						$this->smarty->assign('sOptions',$sTarget);
					}
					elseif ($withtarget) {
#echo "enc_row['encounter_type'] = '".$enc_row['encounter_type']."' allow_show_details = '$allow_show_details' <br> \n";
						#echo "wait = ".$allow_opd_user;

						$sTarget='';
						if ($allow_show_details||($isIPBM&&($ipbmcanUpdatePatient||$ipbmcanViewPatient))) {
							$sTarget = "<a href=\"$this->targetfile".URL_APPEND."&pid=".$zeile['pid']."&edit=1&status=".$status."&target=".$target."&user_origin=".$user_origin."&noresize=1".$IPBMextend."&ptype=".$ptype."&mode=\">";
							$sTarget=$sTarget.'<img '.createLDImgSrc($root_path,'ok_small.gif','0').' title="'.$LDShowDetails.'"></a>';
						// var_dump($ipbmcanViewPatient);
						}
						$this->smarty->assign('sOptions',$sTarget);
					}
					if(!file_exists($root_path.'cache/barcodes/pn_'.$zeile['pid'].'.png')){
						$this->smarty->assign('sHiddenBarcode',"<img src='".$root_path."classes/barcode/image.php?code=".$zeile['pid']."&style=68&type=I25&width=180&height=50&xres=2&font=5&label=2' border=0 width=0 height=0>");
					}

					# Added by Alvin for Bukidnon Hospital Inauguration with PGMA
					if (($totalcount==1)&&($allow_show_details||($isIPBM&&($ipbmcanUpdatePatient||$ipbmcanViewPatient)))) {
						#require_once($root_path."include/care_api_classes/alerts/class_alert.php");
						#$ac = new SegAlert();

						$this->smarty->assign('sLastName',ucfirst($zeile['name_last']));
						$this->smarty->assign('sFirstName',ucfirst($zeile['name_first']));

						$this->smarty->assign('sMiddleName',ucfirst($zeile['name_middle']));
						header("Location:".$this->targetfile.URL_APPEND."&pid=".$zeile['pid']."&edit=1&status=".$status."&target=".$target."&user_origin=".$user_origin."&noresize=1&ptype=".$ptype."&mode=".$IPBMextend);
						exit();
					}

					#
					# Generate the row in buffer and append as string
					#
					ob_start();
						$this->smarty->display('registration_admission/reg_search_list_row.tpl');
						$sTemp = $sTemp.ob_get_contents();
					ob_end_clean();
				}
			}
			#
			# Assign the rows string to template
			#
			$this->smarty->assign('sResultListRows',$sTemp);
			$this->smarty->assign('sPreviousPage',$pagen->makePrevLink($LDPrevious,$this->targetappend));
				$yhPrev = $this->thisfile.$pagen->yhPgPrev;
			$this->smarty->assign('sNextPage',$pagen->makeNextLink($LDNext,$this->targetappend));
				$yhNext = $this->thisfile.$pagen->yhPgNext;
		}

		ob_start();
			include_once($root_path.'modules/registration_admission/include/yh_page.php');
			$tmp1 = ob_get_contents();
		ob_end_clean();
		$this->smarty->assign('yhPrevNext',$tmp1);
		#
		# Add eventual appending text block
		#
		if(!empty($this->posttext)) $this->smarty->assign('sPostText',$this->posttext);

		#
		# Displays the search page
		#
		if($this->bReturnOnly){
			ob_start();
				$this->smarty->display('registration_admission/reg_search_main.tpl');
				$sTemp=ob_get_contents();
			ob_end_clean();
			return $sTemp;
		}else{
			# show Template
			$this->smarty->display('registration_admission/reg_search_main.tpl');
		}
	} // end of function display()

	/**
	* Generates the search page contents but will not output it. Instead it will buffer the output and return it as a string.
	*/
	function create($skey=''){
		$this->bReturnOnly = TRUE;
		return $this->display($skey);
	}
} // end of class
?>