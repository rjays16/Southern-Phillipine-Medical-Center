<?php
/**
* @package care_api
*/

/**
*/
//require_once($root_path.'include/care_api_classes/class_core.php');
/**
*  GUI input form for person registration methods.
*
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
* @KB by Kurt Brauchli
* @package care_api
*/

//require_once($root_path.'include/care_api_classes/class_gui_input_person.php');
//$cominfo = new GuiInputPerson();
//
//require_once($root_path.'modules/registration_admission/address.common.php');
//if ($xajax) {
//		$xajax->printJavascript($root_path.'classes/xajax');
//	}



use Intervention\Image\Image as InterventionImage;

require_once $root_path.'frontend/protected/vendor/autoload.php';

$thisfile = basename($HTTP_SERVER_VARS['PHP_SELF']);

class GuiInputPerson {

	# Language tables
	var $langfiles= array('emr.php', 'person.php', 'date_time.php', 'aufnahme.php');

	# Default path for fotos. Make sure that this directory exists!
	var $default_photo_path='fotos/registration';
		var $default_fpimage_path = 'fotos/fingerprints';

	# Filename of file running this gui
	var $thisfile = '';

	# PID number
	var $pid=0;

	# Toggler var
	var $toggle=0;

	# Color of error text
	var $error_fontcolor='#ff0000';

	# Text block above form
	var $pretext='';
	# Text block below the form
	var $posttext='';

	# filename for displaying the data after saving
	var $displayfile='';

	# Boolean for error
	# burn added: March 15, 2007
	var $seg_error_person_exists=FALSE;

	# smarty template
	var $smarty;

	# Flag for output or returning form data
	var $bReturnOnly = FALSE;

	/**
	*	Department number of the encoder
	*	burn added: March 14, 2007
	*/
	var $segUserDept;
	var $mod;

	/**
	* Constructor
	*/
	function GuiInputPerson($filename = ''){
		global $thisfile, $root_path;
		if(empty($filename)) $this->thisfile = $thisfile;
			else $this->thisfile = $filename;
	}
	/**
	* Sets the PID number
	*/
	function setPID($pid=0){
		if(!empty($pid)) $this->pid = $pid;
	}
	/**
	* Sets the PID number
	*/
	function setDisplayFile($fn=''){
		if(!empty($fn)) $this->displayfile = $fn;
	}

	/**
	*	Sets the department number of the encoder
	*	burn added: March 14, 2007
	*/
	function setUserDept($user_dept=''){
		if(!empty($user_dept)) $this->segUserDept = $user_dept;
	}

	/**
	* Create a row of select element in the form
	* burn added: March 14, 2007
	*/
	function createTRselect($rs_obj, $error_handler, $label_text, $input_name,
					$val_selected, $input_val, $input_text, $segClassName='', $colspan = 1, $input_size = 35,$red=FALSE){

		ob_start();
			if ($error_handler || $red) $sBuffer="<font color=\"$this->error_fontcolor\">* $label_text</font>";
				else $sBuffer=$label_text;
			$this->smarty->assign('sItem',$sBuffer);
			if (!empty($segClassName))
				$segClassName= 'class="'.$segClassName.'"';
			$this->smarty->assign('segClassName',$segClassName);
			$this->smarty->assign('sColSpan2',"colspan=$colspan");
			$sInput="\n		<select name=\"".$input_name."\" id=\"".$input_name."\"> \n";
			while ($result=$rs_obj->FetchRow()){
				$selected='';
				if ($val_selected==$result[$input_val])
					$selected='selected';
				$sInput.='			<option value="'.$result[$input_val].'" '.$selected.'>'.$result[$input_text]."</option> \n";
			}
			$sInput.="		</select> \n";
			$this->smarty->assign('sInput',$sInput);
			$this->smarty->display('registration_admission/reg_row.tpl');
			$sBuffer = ob_get_contents();
		ob_end_clean();

		//$this->toggle=!$this->toggle;

		return $sBuffer;
	}

	/**
	* Create a row of input element in the form
	*/
#	function createTR($error_handler, $input_name, $ld_text, $input_val, $colspan = 1, $input_size = 35,$red=FALSE){   # burn commented: March 16, 2007
	function createTR($error_handler, $input_name, $ld_text, $input_val, $segClassName='', $colspan = 1,
				$input_size = 35,$red=FALSE){   # burn added: March 16, 2007

		ob_start();
			if ($error_handler || $red) $sBuffer="<font color=\"$this->error_fontcolor\">* $ld_text</font>";
				else $sBuffer=$ld_text;
			$this->smarty->assign('sItem',$sBuffer);
			if (!empty($segClassName))
				$segClassName= 'class="'.$segClassName.'"';
			$this->smarty->assign('segClassName',$segClassName);
			$this->smarty->assign('sColSpan2',"colspan=$colspan");
			$this->smarty->assign('sInput','<input name="'.$input_name.'" id="'.$input_name.'" type="text" onBlur="trimString(this)" size="'.$input_size.'" value="'.$input_val.'" >');   # burn added : March 10, 2007
			$this->smarty->display('registration_admission/reg_row.tpl');
			$sBuffer = ob_get_contents();
		ob_end_clean();

		//$this->toggle=!$this->toggle;

		return $sBuffer;
	}
	//added by borj 2014-23-01
	function createTR2($error_handler, $input_name, $ld_text, $input_val, $segClassName='', $colspan = 1,
				$input_size = 9,$red=FALSE){   # burn added: March 16, 2007

		ob_start();
			if ($error_handler || $red) $sBuffer="<font color=\"$this->error_fontcolor\">* $ld_text</font>";
				else $sBuffer=$ld_text;
			$this->smarty->assign('sItem',$sBuffer);
			if (!empty($segClassName))
				$segClassName= 'class="'.$segClassName.'"';
			$this->smarty->assign('segClassName',$segClassName);
			$this->smarty->assign('sColSpan2',"colspan=$colspan");
			$this->smarty->assign('sInput','<input name="'.$input_name.'" id="'.$input_name.'" type="text" onBlur="trimString(this)" size="'.$input_size.'" value="'.$input_val.'" >');   # burn added : March 10, 2007
			$this->smarty->display('registration_admission/reg_row.tpl');
			$sBuffer = ob_get_contents();
		ob_end_clean();

		//$this->toggle=!$this->toggle;

		return $sBuffer;
	}
	/**
	* Checks if the ethnic origin name exists in the database based on its name ONLY.
	*
	* @access public
	* @param string Ethnic Origin name
	* @return boolean
	* burn added: August 30, 2006
	*/
	function EthnicOriginNameExists($ethnic_name=''){
		global $db;
		//if(!$this->_internResolveFirmID($firm_id)) return FALSE;
		if($this->result=$db->Execute("SELECT name FROM care_type_ethnic_orig WHERE name='$ethnic_name'")) {
			if($this->result->RecordCount()) {
				return TRUE;
			} else {
				return FALSE;
			}
		 } else {
			return FALSE;
		}
	}
	
	//added by Nick 05-21-2014
	function getWellBabyEncounter($pid){
		global $db;
		$this->sql = $db->Prepare("SELECT 
									  encounter_nr 
									FROM
									  care_encounter 
									WHERE encounter_type = '12' 
									  AND pid = ?");
		$rs = $db->Execute($this->sql,$pid);
		if($rs){
			if($rs->RecordCount()){
				$row = $rs->FetchRow();
				return $row['encounter_nr'];
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	//added by Nick 05-21-2014
	function updateWellBabyEncounter($data){
		global $db;
		$pk = array('encounter_nr');

		foreach ($data as $key => &$val) {
			$val = $db->qstr($val);
		}

		$rs = $db->Replace('care_encounter',$data,$pk);
		if($rs){
			return true;
		}else{
			return false;
		}
	}

	/**
	* Displays the GUI input form
	*/
	function display(){
		
		global $db, $sid, $lang, $root_path, $pid, $insurance_show, $user_id, $mode, $dbtype, $breakfile, $cfg, $seg_thisfile,
				$update, $photo_filename, $HTTP_POST_VARS,  $HTTP_POST_FILES, $HTTP_SESSION_VARS, $target;

				global $fpimage_filename;           // Added by LST - 08.31.2009 -- for fingerprint image.

		extract($HTTP_POST_VARS);
		# Load the language tables
		$lang_tables =$this->langfiles;
		include($root_path.'include/inc_load_lang_tables.php');
		
		# Load the other hospitals array
		include_once($root_path.'global_conf/other_hospitals.php');
		include_once($root_path.'include/inc_date_format_functions.php');
		include_once($root_path.'include/care_api_classes/class_insurance.php');
		include_once($root_path.'include/care_api_classes/class_person.php');

		#----------added by VAN-----------------
		require_once($root_path.'include/care_api_classes/class_encounter.php');
		$encounter_obj=new Encounter();
		$patient = $encounter_obj->getLastestEncounter($pid);
		$encounter_nr = $patient['encounter_nr'];
		

		require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
		$objInfo = new Hospital_Admin();
        
        require_once($root_path.'include/care_api_classes/class_personell.php');
        $pers_obj=new Personell;

		#------------------------------------
        #added by VAN 11/19/2013
        require_once($root_path . 'include/care_api_classes/emr/class_emr.php');
        $emr_obj = new EMR;

        $row_hosp = $objInfo->getAllHospitalInfo();
        $EMR_address = $row_hosp['EMR_address'];
        $EMR_directory = $row_hosp['EMR_directory'];
        #======================

		#added by VAN 11-05-09
		#$fromdep = $_GET['fromdep'];

		#added by VAS 11-09-08
		#determine user permission
		global $ptype, $allow_patient_register, $allow_newborn_register, $allow_er_user, $allow_opd_user, $allow_phs_user, $allow_ipd_user, $allow_medocs_user, $allow_update, $allow_updateNameData;
		
		include_once $root_path . 'include/inc_ipbm_permissions.php';

		#echo "new ptype = ".$ptype;

		#if ($ptype=='medocs')
		#		$ptype = 'newborn';

		# Create the new person object
		$person_obj= new Person($pid);

		# Create a new person insurance object
		$pinsure_obj= new PersonInsurance($pid);

				# Create a new person insurance object
		#commented by VAN
		#$insure_obj=& new Insurance($insurance_firm_id);   # burn added: August 29, 2006
		$insure_obj= new Insurance($hcare_id);	#van added : 09-05-07

		# Create the new address object
		include_once($root_path.'include/care_api_classes/class_address.php');   # burn added:' August 22, 2006
		$address_obj= new Address($addr_citytown_nr);   # burn added: August 29, 2006

		if ($_GET['pid'])
			$pid =   $_GET['pid'];
		#echo "p = ".$pid;
		if(!isset($insurance_show)) $insurance_show=TRUE;

		$newdata=1;

		$error=0;

		$dbtable='care_person';

		if(!isset($photo_filename)||empty($photo_filename))
            $photo_filename='';
        if(!isset($fpimage_filename) || empty($fpimage_filename))
             $fpimage_filename = '';
		# Assume first that image is not uploaded
		$valid_image=FALSE;

		//* Get the global config for person's registration form*/
		include_once($root_path.'include/care_api_classes/class_globalconfig.php');
		$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('person_%');

		//extract($GLOBAL_CONFIG);

		# Check whether config foto path exists, else use default path
		$photo_path = (is_dir($root_path.$GLOBAL_CONFIG['person_foto_path'])) ? $GLOBAL_CONFIG['person_foto_path'] : $this->default_photo_path;
				$fpimage_path = $this->default_fpimage_path;

		if (($mode=='save') || ($mode=='forcesave')) {
			# If saving is not forced, validate important elements
			if($mode!='forcesave') {
				# clean and check input data variables
				if(trim($encoder)=='') $encoder=$aufnahme_user;
			}


			# If the validation produced no error, save the data
			if(!$error) {
				# Save the old filename for testing
				$old_fn=$photo_filename;

				# Create image object
				include_once($root_path.'include/care_api_classes/class_image.php');
				$img_obj= new Image;

				# Check the uploaded image file if exists and valid
				if($img_obj->isValidUploadedImage($_FILES['photo_filename'])){
					$valid_image=TRUE;
					# Get the file extension
					$picext=$img_obj->UploadedImageMimeType();
				} elseif (!empty($HTTP_POST_VARS['photo_data'])) {
                    // Photo data from media device (like camera)
                    // Simulate $_FILES item
                    require_once $root_path.'frontend/protected/vendors/DataUri.php';
                    if (DataUri::tryParse($HTTP_POST_VARS['photo_data'], $dataUri)) {

                        $data = false;
                        if ($dataUri->tryDecodeData($data)) {
                            $tmpFile = tempnam(sys_get_temp_dir(), 'photo');
                            file_put_contents($tmpFile, $data);
                            $_FILES['photo'] = array(
                                'name' => uniqid($pid.'-') . '.jpg',
                                'type' => 'image/jpeg',
                                'tmp_name' => $tmpFile,
                                'error' => UPLOAD_ERR_OK,
                                'size' => strlen($data)
                            );
                        }
                    }
				}

				if ($update) {
					#-------added by VAN 07-02-08
					if (((($fromtemp)||($ptype=='newborn'))&&(($allow_ipd_user)||($allow_newborn_register)))){
					#if (($ptype=='newborn')||($fromtemp)){
						 #$HTTP_POST_VARS['fromtemp'] = 1;
						 $HTTP_POST_VARS['admitted_baby'] = $HTTP_POST_VARS['profileType'];
					}else{
						 #$HTTP_POST_VARS['fromtemp'] = 0;
						 $HTTP_POST_VARS['admitted_baby'] = 0;
						 #echo "<br>false";
					}

					#------------------------

					#edited by VAN 01-21-09
					$date = date("Y-m-d",strtotime($HTTP_POST_VARS['reg_date']));
					$time = $HTTP_POST_VARS['reg_time'].":00 ".$HTTP_POST_VARS['selAMPM2'];
					$time = date("H:i:s",strtotime($time));
					$date_reg = $date." ".$time;
					$date_reg = date("Y-m-d H:i:s",strtotime($date_reg));

					#$date_reg=date('Y-m-d H:i:s', strtotime($date_reg));
					#--------------------

                    #birth time
                    if (($fromtemp)||($ptype=='newborn')){
                        $birth_time = $HTTP_POST_VARS['birth_time'].":00 ".$HTTP_POST_VARS['birth_time_selAMPM'];
                    }
                    
					#added by VAN 06-17-09
					$uploaddir = "../../fotos/registration/";
					$filename = trim($_FILES['photo']['name']);
					#$filename = substr($filename, -20);
					#$filename = ereg_replace(" ", "", $filename);

					$validExt = preg_match("/\.jpg$/", $filename) || preg_match("/\.jpeg$/", $filename) || preg_match("/\.gif$/",$filename);
                    if ($validExt){
							$uploadfile = $uploaddir . $filename;

							if (file_exists("../../fotos/registration/".$filename)){
									foreach (glob("../../fotos/registration/".$filename) as $filename){
										unlink($filename);
									} // end of loop
							} // if (file_exists("../../fotos/registration/".$filename))
						if (is_uploaded_file($_FILES['photo']['tmp_name'])) {	
							if (move_uploaded_file($_FILES['photo']['tmp_name'],$uploadfile)){
								 chmod($uploadfile, 0644);
							}
						} else {
                            // Manually move the temporary file to the fotos/registration directory
                            @rename($_FILES['photo']['tmp_name'], $uploadfile);
                            @chmod($uploadfile, 0644);
	                        }
	                    }

					if ($filename != null && $validExt){
						 $filename = trim($_FILES['photo']['name']);
					}else{
						 $filename = trim($photo_filename);
						 $uploadfile = trim($photo);
					}

					$photo_filename = $filename;
					if ($photo_filename == '') {
						$photo_filename = $photo_data;
					}
					#----------------------

                    if (($birth_time!='00:00:00') && ($birth_time!=''))
                        $birth_time = date('H:i:s',strtotime($birth_time));
                    
                    #edited by art 11/29/14 added qstr to solve error saving data  with '

                    // added by carriane 08/09/18
					if(strpos(addslashes($HTTP_POST_VARS['name_first']), ', ') !== false){
						$commaPos = strpos(addslashes($HTTP_POST_VARS['name_first']), ', ');

						$suffixFromName = substr($HTTP_POST_VARS['name_first'], $commaPos+2);

						$suffix = $suffixFromName;
						$name_first = str_replace(', ', ' ', $HTTP_POST_VARS['name_first']);
					}
					// end carriane

					$sql="UPDATE $dbtable SET
							 title='$title',
							 date_reg=".$db->qstr($date_reg).",
							 name_last=".$db->qstr($name_last).",
							 name_first=".$db->qstr($name_first).",
                             suffix=".$db->qstr($suffix).",
							 name_2=".$db->qstr($name_2).",
							 name_3=".$db->qstr($name_3).",
							 name_middle=".$db->qstr($name_middle).",
							 name_maiden=".$db->qstr($name_maiden).",
							 name_others=".$db->qstr($name_others).",
							 date_birth=".$db->qstr(formatDate2STD($date_birth,$date_format)).",
                             birth_time = ".$db->qstr($birth_time).",
							 place_birth=".$db->qstr($place_birth).",
							 blood_group=".$db->qstr(trim($blood_group)).",
							 sex=".$db->qstr($sex).",
							 addr_str=".$db->qstr($addr_str).",
							 addr_str_nr=".$db->qstr($addr_str_nr).",
							 addr_zip=".$db->qstr($addr_zip).",
							 addr_citytown_nr=".$db->qstr($addr_citytown_nr).",
							 street_name=".$db->qstr(stripslashes($street_name)).",
							 brgy_nr=".$db->qstr($brgy_nr).",
							 mun_nr=".$db->qstr($mun_nr).",
							 citizenship=".$db->qstr($citizenship).",
							 occupation=".$db->qstr($occupation).",
							 employer=".$db->qstr($employer).",
							 phone_1_nr=".$db->qstr($phone_1_nr).",
							 phone_2_nr=".$db->qstr($phone_2_nr).",
							 cellphone_1_nr=".$db->qstr($cellphone_1_nr).",
							 cellphone_2_nr=".$db->qstr($cellphone_2_nr).",
							 fax=".$db->qstr($fax).",
							 email=".$db->qstr($email).",
							 civil_status=".$db->qstr($civil_status).",
							 sss_nr=".$db->qstr($sss_nr).",
							 nat_id_nr=".$db->qstr($nat_id_nr).",
							 religion=".$db->qstr($religion).",
							 mother_fname=".$db->qstr($mother_fname).",
							 mother_maidenname=".$db->qstr($mother_maidenname).",
							 mother_mname=".$db->qstr($mother_mname).",
							 mother_lname=".$db->qstr($mother_lname).",
							 mother_pid=".$db->qstr($mother_pid).",
							 father_fname=".$db->qstr($father_fname).",
							 father_mname=".$db->qstr($father_mname).",
							 father_lname=".$db->qstr($father_lname).",
							 spouse_name=".$db->qstr($spouse_name).",
							 guardian_name=".$db->qstr($guardian_name).",
							 ethnic_orig=".$db->qstr($ethnic_orig).",
							 senior_ID=".$db->qstr($senior_ID).",
							 veteran_ID=".$db->qstr($veteran_ID).",
							 /*is_indigent='$is_indigent',*/
							 /*DOH_ID = '$DOH_ID',*/
							 age='$age',
							 admitted_baby = ".$db->qstr($HTTP_POST_VARS['admitted_baby']).",
							 fromtemp = ".$db->qstr($HTTP_POST_VARS['fromtemp']).",
							 date_update=".$db->qstr(date('Y-m-d H:i:s')).",
                             is_temp_bdate = ".$db->qstr($HTTP_POST_VARS['is_temp_bdate']).",
                             homis_id = ".$db->qstr($HTTP_POST_VARS['homis_id']).",";
							# add to the sql query
						$sql.=" photo_filename='$photo_filename',";
						$sql.=" fpimage_filename='$fpimage_filename',";     # added by LST ------ 09.01.2009 ----- for fingerprint image.
//					}								------------- commented out by Bong 5/17/2007
						$sql.=$changePID;   # burn added : July 25, 2007

					# complete the sql query
					$sql.=" history=".$person_obj->ConcatHistory("Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n").", modify_id='".$HTTP_SESSION_VARS['sess_user_name']."' WHERE pid='$pid'";

                    //added by devon - update foreign citizenship in deathcert
                    $sqlcheckDeathcert = $db->GetAll("SELECT * FROM seg_cert_death WHERE pid = ". $db->qstr($pid)." ");
                    if(count($sqlcheckDeathcert) == 1) {
                        $sqlforDeathcertUpdate = "UPDATE seg_cert_death SET dcitizenship = ".$db->qstr($citizenship)." WHERE pid = ".$db->qstr($pid)." ";
                        $db->Execute($sqlforDeathcertUpdate);
                    }

					if (!empty($changePID)){
						$pid = $new_permanent_pid;
					}

					#$db->debug=true;
					$db->BeginTrans();
					$ok=$db->Execute($sql);


					$name_first_suffix = explode(", ", $name_first); //get name without suffix
					$getPersonData = $pers_obj->getPersonData($pid);
					$patientData = array(
						'pid'					=> $pid,
						'firstName'				=> $name_first_suffix[0],
						'middleName'			=> $name_middle,
						'lastName'				=> $name_last,
						'gender'				=> $sex,
						'dateOfBirth'			=> (formatDate2STD($date_birth,$date_format)),
						'street'				=> $addr_str,
						'barangay'				=> $getPersonData['brgy_nr'],
						'city'					=> $getPersonData['mun_nr'],
						'custom_middle_initial'	=> $getPersonData['custom_middle_initial'],
						'suffix'				=> $getPersonData['suffix'],
						'name_maiden'			=> $getPersonData['name_maiden'],
						'name_others'			=> $getPersonData['name_others'],
						'place_birth'			=> $getPersonData['place_birth'],
						'blood_group'			=> $getPersonData['blood_group'],
						'addr_str'				=> $getPersonData['addr_str'],
						'addr_str_nr'			=> $getPersonData['addr_str_nr'],
						'addr_zip'				=> $getPersonData['addr_zip'],
						'addr_citytown_nr'		=> $getPersonData['addr_citytown_nr'],
						'addr_is_valid'			=> $getPersonData['addr_is_valid'],
						'street_name'			=> $getPersonData['street_name'],
						'citizenship'			=> $getPersonData['citizenship'],
						'occupation'			=> $getPersonData['occupation'],
						'employer'				=> $getPersonData['employer'],
						'phone_1_code'			=> $getPersonData['phone_1_code'],
						'phone_2_code'			=> $getPersonData['phone_2_code'],
						'phone_2_nr'			=> $getPersonData['phone_2_nr'],
						'cellphone_1_nr'		=> $getPersonData['cellphone_1_nr'],
						'cellphone_2_nr'		=> $getPersonData['cellphone_2_nr'],
						'fax'					=> $getPersonData['fax'],
						'email'					=> $getPersonData['email'],
						'civil_status'			=> $getPersonData['civil_status'],
						'religion'				=> $getPersonData['religion'],
						'mother_fname'			=> $getPersonData['mother_fname'],
						'mother_maidenname'		=> $getPersonData['mother_maidenname'],
						'mother_mname'			=> $getPersonData['mother_mname'],
						'mother_lname'			=> $getPersonData['mother_lname'],
						'father_fname'			=> $getPersonData['father_fname'],
						'spouse_name'			=> $getPersonData['spouse_name'],
						'guardian_name'			=> $getPersonData['guardian_name'],
						'contact_person'		=> $getPersonData['contact_person'],
						'contact_relation'		=> $getPersonData['contact_relation'],
						'death_date'			=> $getPersonData['death_date'],
						'death_encounter_nr'	=> $getPersonData['death_encounter_nr'],
						'death_cause'			=> $getPersonData['death_cause'],
						'death_cause_code'		=> $getPersonData['death_cause_code'],
						'date_update'			=> $getPersonData['date_update'],
						'status'				=> $getPersonData['status'],
						'history'				=> $getPersonData['history'],
						'senior_ID'				=> $getPersonData['senior_ID'],
						'veteran_ID'			=> $getPersonData['veteran_ID'],
						'is_indigent'			=> $getPersonData['is_indigent'],
						'DOH_ID'				=> $getPersonData['DOH_ID'],
						'age'					=> $getPersonData['age'],
						'name_search'			=> $getPersonData['name_search'],
						'create_id'				=> $HTTP_POST_VARS['user_id']
					);

					// UPDATE PATIENT INFO
					require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
					$ehr = Ehr::instance();
					$utfEncodedArray = array_map("utf8_encode", $patientData);
					$patient = $ehr->postAddPerson($utfEncodedArray);
					$asd = $ehr->getResponseData();
					$EHRstatus = $patient->status;
					if(!$EHRstatus){

					}
//
//					// for other api calls e.g. dietary
//					require_once($root_path . 'include/care_api_classes/API/curl_api.php');
//					$sql_personnel = "SELECT nr FROM care_personell WHERE pid=".$db->qstr($pid);
//					$personell_nr = $db->GetOne($sql_personnel);
//					$curl_obj = new Rest_Curl;
//					$curl_obj->updateNameInfo($name_last,$name_first,$personell_nr);

					//added by borj 2014-22-01
					$sql1 = "UPDATE seg_person_vaccination SET vac_details='".$HTTP_POST_VARS['vac_details']."', vac_date='".$HTTP_POST_VARS['vac_date']."' where pid = '".$HTTP_POST_VARS['pid']."'";
					$ok5 = $db->Execute($sql1);
					
                                        // Removed by LST ..... 12/05/2019
					# Added by LST --- 09.01.2009 ----- for saving fingerprint image.
//					if ($ok) {
//							$fldArray = array('pid'=>"'{$HTTP_POST_VARS['pid']}'", 'fpimage'=>"cast(unhex('".$HTTP_POST_VARS['fpcode']."') as BINARY)");
//							$ok1 = $db->Replace('seg_fingerprint', $fldArray, 'pid');
//					}

					if($ok) {
                        
                        #added by VAN 06-19-2013
                        #update name in care_users if the patient or person is a personnel
                        $sql_personnel = "SELECT nr FROM care_personell WHERE pid=".$db->qstr($pid);
                        $personell_nr = $db->GetOne($sql_personnel);
                        $sql_name = "SELECT fn_get_person_name_first_mi_last(".$db->qstr($pid).") AS NAME FROM care_person WHERE pid=".$db->qstr($pid);
                        $name_person = $db->GetOne($sql_name);
                        
                        if ($personell_nr) {
                            $pers_obj->updatePersonnelNameUsers($personell_nr, $name_person);
                        
                            # added by VAS 11/19/2013
                            # integration to EMR starts here
                            # Post corresponding Doctor information in EMR
                            if ($emr_obj->isDoctor($personell_nr)) {
                                require_once($root_path . 'include/care_api_classes/emr/services/DoctorEmrService.php');
                                $doctorService = new DoctorEmrService();
                                try {
                                    $doctorService->saveDoctor($personell_nr, 1);
                                } catch (Exception $exc) {
//                                    echo $exc->getTraceAsString();
                                }
                            }
                            #=======================
                        }

						$db->CommitTrans();

                        # added by VAS 11/19/2013
                        # integration to EMR starts here
                        # for doctor only
                        # Post corresponding Patient/Person demographic in EMR

                        $_POST['date_reg'] = $date_reg;

                        try {
                        	//update patient record
//                            require_once($root_path . 'include/care_api_classes/emr/services/PatientEmrService.php');
//                            $patientService = new PatientEmrService();
//                            #add new argument to detect if to update patient demographic or not
//                            $patientService->savePatient($pid, 1);
                        } catch (Exception $exc) {
                            //echo $exc->getTraceAsString();
                        }

                        #===============================
						# Update the insurance data
						# Lets detect if the data is already existing

						#---------added by VAN ------------------

						if($insurance_show) {
							#if ($insurance_class_nr!=3){
							if (($insurance_class_nr!=3)&&($_POST["items"]!=NULL)){
								#-----with insurance---
								#if ($_POST["items"]!=NULL){
									$insurance_cur = array();
									foreach (array_unique($_POST["items"]) as $i=>$v) {
										#------------------hcare_id, insurance_nr, is principal holder-----
										$insurance_cur[] = array($_POST["items"][$i],$_POST["nr"][$i],$_POST["is_principal"][$i]);
										$insurance_array_cur .= $_POST["items"][$i].",";
									}

									#from the hidden field of bulk_array_prev : previous array - detailed
									$insurance_prev = unserialize(stripslashes($bulk_array_prev));

									#from the hidden field of insurance_array_prev : previous array - only hcareid
									$insurance_array_prev = explode(",",$insurance_array_prev);

									#current array - only hcareid
									$insurance_array_cur = substr($insurance_array_cur,0,strlen($insurance_array_cur)-1);
									$insurance_array_cur = explode(",",$insurance_array_cur);

									#return the elements present in previous array and not present in the current
									#the insurance to be deleted in the table : only hcareid
									$not_existing_cur = array_diff($insurance_array_prev, $insurance_array_cur);

									#insurance to be deleted
									$not_existing_cur_implode = implode(",",$not_existing_cur);

									#current insurance
									$existing_cur_implode = implode(",",$insurance_array_cur);

									#echo "<br>current = ".implode(",",$insurance_array_cur);
									#return the elements present in current array and not present in the previous
									#the insurance to be added in the table
									$not_existing_prev = array_diff($insurance_array_cur, $insurance_array_prev);
									$not_existing_prev_implode = implode(",",$not_existing_prev);
									$not_existing_prev_explode = explode(",",$not_existing_prev_implode);

									$delete_result_nc="DELETE FROM care_person_insurance WHERE hcare_id IN (".$not_existing_cur_implode.") AND pid = '".$pid."'";
									$ok = $db->Execute($delete_result_nc);

									$delete_result="DELETE FROM care_person_insurance WHERE hcare_id IN (".$existing_cur_implode.") AND pid = '".$pid."'";
									#echo "<br>sql delete = ".$delete_result;
									$ok = $db->Execute($delete_result);

									for ($i=0; $i<sizeof($not_existing_prev_explode); $i++){
										for ($j=0; $j<sizeof($insurance_cur); $j++){
											$arr_cur = $insurance_cur[$j][0];
											$arr_cur = explode(",",$arr_cur);
											#change 10-09-07
											/*
											if (in_array($not_existing_prev_explode[$i], $arr_cur)) {
												$NEP_detailed_array = $insurance_cur[$j];
												$pinsure_obj->addInsurance_reg($pid,$NEP_detailed_array,$HTTP_SESSION_VARS['sess_user_name'],date('YmdHis'),$insurance_class_nr);
											}
											*/
											$NEP_detailed_array = $insurance_cur[$j];
											#print_r($NEP_detailed_array);
											$pinsure_obj->addInsurance_reg($pid,$NEP_detailed_array,$HTTP_SESSION_VARS['sess_user_name'],date('YmdHis'),$insurance_class_nr);
										}
									}

									$update_result="UPDATE care_person_insurance SET class_nr = '".$insurance_class_nr."' WHERE pid = '".$pid."'";
									#echo "sql update = ".$update_result;
									$ok = $db->Execute($update_result);

							}elseif (($insurance_class_nr==3)||($_POST["items"]==NULL)){
								#add script here if it is self-pay or insurance tray is empty
								$from_person_insurance = $pinsure_obj->getPersonInsuranceItems($pid);
								$row_from_person_insurance=$from_person_insurance->RecordCount();

								$sql2 = "SELECT * FROM care_encounter WHERE pid ='".$pid."'";
								$from_encounter=$db->Execute($sql2);
								$row_from_encounter=$from_encounter->RecordCount();
								if ($row_from_encounter!=0){
									while($rsObj=$from_encounter->FetchRow()) {
										$enc_cr = $rsObj["encounter_nr"];
										$sql3 = "SELECT * from seg_encounter_insurance WHERE encounter_nr='".$rsObj["encounter_nr"]."'";
										$from_seg_insurance=$db->Execute($sql3);
										$row_from_seg_insurance=$from_seg_insurance->RecordCount();

										if ($row_from_seg_insurance!=0){
											while ($row=$from_seg_insurance->FetchRow()) {
												$from_seg .= $row["hcare_id"].",";
											}
										}
									}
								}
								$from_seg = substr($from_seg, 0, strlen($from_seg)-1);
								$from_seg_list = explode(",",$from_seg);

								if ($from_seg_list[0]==NULL){
									$pinsure_obj->clearInsuranceList_reg($pid);         #clear care_person_insurance table
								}else{
									if ($row_from_person_insurance!=0){
										while ($row=$from_person_insurance->FetchRow()) {
											if (in_array($row['hcare_id'], $from_seg_list)){
												#$in_seg_firmname .= $row["firm_id"]." ,";
											}else{
												$not_in_seg .= $row["hcare_id"].",";
												#$not_in_seg_firmname .= $row["firm_id"]." ,";
											}
										}
										$not_in_seg = substr($not_in_seg, 0, strlen($not_in_seg)-1);
										$delete_result="DELETE FROM care_person_insurance WHERE hcare_id IN (".$not_in_seg.") AND pid = '".$pid."'";
										$ok = $db->Execute($delete_result);
										/*
										if ($ok){
											$not_in_seg_firmname = substr($not_in_seg_firmname, 0, strlen($not_in_seg_firmname)-1);
											$in_seg_firmname = substr($in_seg_firmname, 0, strlen($in_seg_firmname)-1);

											echo " \n <script type=\"text/javascript\">alert(\"Only ".$not_in_seg_firmname.
													 " can be deleted and ".$in_seg_firmname." cannot be deleted because they are already been used!\")</script>";

										}
										*/
									}
								}
							}
						}
						 #-----------------------------------------
						/*
						if($insurance_show) {
							if($insurance_item_nr) {

									$insure_data=array('insurance_nr'=>$insurance_nr,
											'firm_id'=>$insurance_firm_id,
											'class_nr'=>$insurance_class_nr,
											'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
											'modify_time'=>date('YmdHis')
											);
									$insure_data['history'] = "CONCAT(history,'Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n')";
									$pinsure_obj->updateDataFromArray($insure_data,$insurance_item_nr);

								if(!empty($insurance_nr) && !empty($insurance_firm_name) && $insurance_firm_id) {

														# Increases usage frequency of an insurance company.
														# burn added:' August 23, 2006

									 if (($insurance_firm_name) && ($insure_obj->FirmName_exists($insurance_firm_name)) ){
																							#and in the list in care_insurance_firm table
												$insure_obj->updateUseFrequency($insurance_firm_id,1);
											 }
								}

							} elseif ($insurance_nr && $insurance_firm_name  && $insurance_class_nr) {

								#     There's NO EXISTING item_nr in care_person_insurance table
								#     Add an entry/row in care_person_insurance table
								#     burn modified: August 24, 2006

								$insure_data=array('insurance_nr'=>$insurance_nr,
											'firm_id'=>$insurance_firm_id,
											'pid'=>$pid,
											'class_nr'=>$insurance_class_nr,
											'history'=>"Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n",
											'create_id'=>$HTTP_SESSION_VARS['sess_user_name'],
											'create_time'=>date('YmdHis')
										);
								$pinsure_obj->insertDataFromArray($insure_data);

#echo "class_gui_input_person.php : insert mode : pinsure_obj->sql='".$pinsure_obj->sql."' <br> \n";
							}
						} */
						$newdata=1;
						//$db->debug=1;
						// KB: save other_his_no
						if( isset($_POST['other_his_org']) && !empty($_POST['other_his_org'])){
							$person_obj->OtherHospNrSet($_POST['other_his_org'], $_POST['other_his_no'], $_SESSION['sess_user_name'] );
						}
						/*
						* Increases usage frequency of a city/town.
						* burn added:' August 23, 2006
						*/
							if ($address_obj->CityTownNameExists($addr_citytown_name)){
																				//and in the list in care_address_citytown table
										$address_obj->updateUseFrequency($addr_citytown_nr,1);
									}
						/*
						* Increases usage frequency of an ethnic origin.
						* burn added:' August 23, 2006
						*/
						#commented by VAN
						/*
						if ($ethnic_orig_txt && $this->EthnicOriginNameExists($ethnic_orig_txt)){
															 //and in the list in care_type_ethnic_orig table
								$sql = "UPDATE care_type_ethnic_orig SET use_frequency=(use_frequency + 1) WHERE nr=$ethnic_orig";
								$db->BeginTrans();
									$ok=$db->Execute($sql);
						}
						*/
						#Added by Jarel Create Well Baby transaction on Update Only if there's no Well Baby Transaction
						$hasWellBaby = $encounter_obj->hasWellBabyTransaction($pid);
						if ((!$hasWellBaby)&&($fromtemp)){
                            $glob_obj->getConfig('encounter_%');
                            if($GLOBAL_CONFIG['encounter_nr_fullyear_prepend'])
                                $ref_nr=(int)date('Y').$GLOBAL_CONFIG['encounter_nr_init'];
                            else 
                                $ref_nr=$GLOBAL_CONFIG['encounter_nr_init'];
                                
                            $triage = 'wellbaby';
                            $last_enc_nr = $encounter_obj->getLastEncounterNr($triage);

                            if ($last_enc_nr)
                                $ref_nr = $last_enc_nr;
                            $wellbaby['encounter_nr']=$encounter_obj->getNewEncounterNr($ref_nr,12);
                            $wellbaby['pid']=$pid;
                            $wellbaby['current_dept_nr']=191;
                            // $wellbaby['encounter_date']=date('Y-m-d H:i:s', strtotime($date_reg));//commented by Nick 05-21-2014
                            $wellbaby['encounter_date'] = formatDate2STD($date_birth,$date_format) . ' ' . $birth_time;//added by Nick 05-21-2014
                            #care_type_encounter
                            $wellbaby['encounter_class_nr']=12; 
                            $wellbaby['encounter_type']=12;
                            $encoder = $HTTP_SESSION_VARS['sess_login_username'];
                            $wellbaby['modify_id']=$encoder;
                            $wellbaby['modify_time']=date('YmdHis');
                            $wellbaby['create_id']=$encoder;
                            $wellbaby['create_time']=date('YmdHis');
                            $wellbaby['history']='Create: '.date('Y-m-d H:i:s').' = '.$encoder;
                            $encounter_obj->setDataArray($wellbaby);
                            
							$db->StartTrans();                                
                            if($encounter_obj->insertDataFromInternalArray()){
                                $bSuccess = $encounter_obj->update_Encounter_Tracker($wellbaby['encounter_nr'],$triage);
                                #echo "<br>up = ".$encounter_obj->sql;
                            }
                            
                            if (!$bSuccess) $db->FailTrans();
                            $db->CompleteTrans();
                            #exit();
                        }else{//condition block added by Nick 05-21-2014
                        	if(isset($HTTP_POST_VARS['profileType']) && $HTTP_POST_VARS['profileType'] == 0){
								$wellbaby['encounter_nr'] = $this->getWellBabyEncounter($pid);
								$wellbaby['pid']=$pid;
								$wellbaby['current_dept_nr']=191;
								$wellbaby['encounter_date'] = formatDate2STD($date_birth,$date_format) . ' ' . $birth_time;
								$wellbaby['encounter_class_nr']=12; 
								$wellbaby['encounter_type']=12;
								$encoder = $HTTP_SESSION_VARS['sess_login_username'];
								$wellbaby['modify_id']=$encoder;
								$wellbaby['modify_time']=date('YmdHis');
								$wellbaby['create_id']=$encoder;
								$wellbaby['create_time']=date('YmdHis');
								$wellbaby['history']='Create: '.date('Y-m-d H:i:s').' = '.$encoder;
								$db->StartTrans();
								$rs = $this->updateWellBabyEncounter($wellbaby);
								if(!$rs){
									$db->FailTrans();
								}
								$db->CompleteTrans();
                        	}
                        }#end of wellbaby condition
                        if(isset($HTTP_POST_VARS['homis_id'])) $from='ipbm';
                                            if(file_exists($this->displayfile)) {
                                                require_once($root_path."include/care_api_classes/biometric/class_biometric.php");
												try {
                                                $bsuccess = Biometric::saveFingerprint($pid, $_POST["fptemplate"]);
												}
												catch (Exception $ex) {
													echo "ERROR: ".$ex->getMessage();
												}
                                                if ($bsuccess) {
                                                    header("Location: $this->displayfile".URL_REDIRECT_APPEND."&pid=$pid&from=$from&newdata=1&target=entry&ptype=$ptype");                                                
                                                    exit;
                                                }
                                            }else{
                                                echo "Error! Target display file not defined!!";
                                            }
					} else {
						$db->RollbackTrans();
					}

									// below is the code for saving a new person/patient's entry. # burn added: August 22, 2006
					} else {

					$from='entry';
					if(isset($HTTP_POST_VARS['homis_id'])) $from='ipbm';
					$HTTP_POST_VARS['date_birth']=@formatDate2Std($date_birth,$date_format);
					#$HTTP_POST_VARS['date_reg']=date('Y-m-d H:i:s');
					#edited by VAN 01-21-09
					$date = date("Y-m-d",strtotime($HTTP_POST_VARS['reg_date']));
					$time = $HTTP_POST_VARS['reg_time'].":00 ".$HTTP_POST_VARS['selAMPM2'];
					$time = date("H:i:s",strtotime($time));
					$date_reg = $date." ".$time;
					$date_reg = date("Y-m-d H:i:s",strtotime($date_reg));

					$HTTP_POST_VARS['date_reg']=date('Y-m-d H:i:s', strtotime($date_reg));
					#--------------------
                    
					$HTTP_POST_VARS['blood_group']=trim($HTTP_POST_VARS['blood_group']);
					$HTTP_POST_VARS['status']='normal';
					$HTTP_POST_VARS['history']="Init.reg. ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n";
					$HTTP_POST_VARS['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
					$HTTP_POST_VARS['create_time']=date('Y-m-d H:i:s');

					#if (((($fromtemp)||($ptype=='newborn'))&&(($allow_ipd_user)||($allow_newborn_register)))){
					if (($ptype=='newborn')||($fromtemp)){
						 #$HTTP_POST_VARS['fromtemp'] = 1;
						 $HTTP_POST_VARS['admitted_baby'] = $HTTP_POST_VARS['profileType'];
                         
                         #birth time
                         $birth_time = $HTTP_POST_VARS['birth_time'].":00 ".$HTTP_POST_VARS['birth_time_selAMPM'];
                         
                         if (($birth_time!='00:00:00') && ($birth_time!=''))
                            $birth_time = date('H:i:s',strtotime($birth_time));
                         
                         $HTTP_POST_VARS['birth_time'] = $birth_time;   
					}else{
						 #$HTTP_POST_VARS['fromtemp'] = 0;
						 $HTTP_POST_VARS['admitted_baby'] = 0;
						 #echo "<br>false";
					}

					# Prepare internal data to be stored together with the user input data
					if(!$person_obj->InitPIDExists($GLOBAL_CONFIG['person_id_nr_init'])){
						# If db is mysql, insert the initial pid value  from global config
						# else let the dbms make an initial value via the sequence generator e.g. postgres
						# However, the sequence generator must be configured during db creation to start at
						# the initial value set in the global config
						if($dbtype=='mysql'){
							$HTTP_POST_VARS['pid']=$GLOBAL_CONFIG['person_id_nr_init'];
						}
					}/*else{
						# Persons are existing. Check if duplicate might exist
						if(is_object($duperson=$person_obj->PIDbyData($HTTP_POST_VARS))){
						#echo "sql = ".$person_obj->sql;
							$error_person_exists=TRUE;
						}
					}*/

					$brgy_nr = $HTTP_POST_VARS['brgy_nr'] ? $HTTP_POST_VARS['brgy_nr'] : $HTTP_SESSION_VARS['seg_post']['brgy_nr'];
					$name_last  = $HTTP_POST_VARS['name_last'] ? $HTTP_POST_VARS['name_last'] : $HTTP_SESSION_VARS['seg_post']['name_last'];
					$name_first = $HTTP_POST_VARS['name_first'] ? $HTTP_POST_VARS['name_first'] : $HTTP_SESSION_VARS['seg_post']['name_first'];
					
                    if(is_object($duperson = $person_obj->isPersonExists($name_first,$name_last))){
                        $error_person_exists = true;
					}
					
					if(!$error_person_exists||$mode=='forcesave'){

						#edited by VAN 07-02-08
						$ref_nr=$GLOBAL_CONFIG['person_id_nr_init'];

						$permission_val = explode(" ",$HTTP_SESSION_VARS['sess_permission']);
						$manual_hrn = false;
						/*foreach($permission_val as $val){
							echo "value: ".$val."<br/>";
						}*/
						if(in_array("_a_1_ipdpatientmanualhrn", $permission_val)){
							if(trim($HTTP_POST_VARS['pid']) == ""){
								$HTTP_POST_VARS['pid']=$person_obj->getNewPIDNr($ref_nr+$GLOBAL_CONFIG['person_id_nr_init_adder']);
							}else{
								$manual_hrn = true;
								$pid = $HTTP_POST_VARS['pid'];
							}
						}else{
							$HTTP_POST_VARS['pid']=$person_obj->getNewPIDNr($ref_nr+$GLOBAL_CONFIG['person_id_nr_init_adder']);
						}
						

						#added by VAN 06-17-09
						$uploaddir = "../../fotos/registration/";
						$filename = trim($_FILES['photo']['name']);
						#$filename = substr($filename, -20);
						#$filename = ereg_replace(" ", "", $filename);

						$validExt = preg_match("/\.jpg$/", $filename) || preg_match("/\.jpeg$/", $filename) || preg_match("/\.gif$/",$filename);
                        if ($validExt){
							 $uploadfile = $uploaddir . $filename;

							 if (file_exists("../../fotos/registration/".$filename)){
									foreach (glob("../../fotos/registration/".$filename) as $filename){
											unlink($filename);
									} // end of loop
							 } // if (file_exists("../../fotos/registration/".$filename))

							 if (is_uploaded_file($_FILES['photo']['tmp_name'])) {
							 if (move_uploaded_file($_FILES['photo']['tmp_name'],$uploadfile)){
									chmod($uploadfile, 0644);
							 }
						 } else {
                                // Manually move the temporary file to the fotos/registration directory
                                @rename($_FILES['photo']['tmp_name'], $uploadfile);
                                @chmod($uploadfile, 0644);
                            }
                        }

						if (($filename!=NULL)&&((ereg(".jpg", $filename)) || (ereg(".jpeg", $filename)) || (ereg(".gif",$filename)))){
							$filename = trim($_FILES['photo']['name']);
						}else{
							$filename = trim($photo_filename);
							$uploadfile = trim($photo);
						}

						$HTTP_POST_VARS['photo_filename'] = $filename;
						#----------------------

						// added by carriane 08/09/18
						if(strpos(addslashes($HTTP_POST_VARS['name_first']), ', ') !== false){
							$commaPos = strpos(addslashes($HTTP_POST_VARS['name_first']), ', ');

							$suffixFromName = substr($HTTP_POST_VARS['name_first'], $commaPos+2);

							$HTTP_POST_VARS['suffix'] = $suffixFromName;
							$HTTP_POST_VARS['name_first'] = str_replace(', ', ' ', $HTTP_POST_VARS['name_first']);
						}
						// end carriane

						#added by VAN 09-29-2016
						#To escapes special characters in a string for use in an SQL statement
						#sample : O'Reilly
						$HTTP_POST_VARS['name_last'] = addslashes($HTTP_POST_VARS['name_last']);
						$HTTP_POST_VARS['name_first'] = addslashes($HTTP_POST_VARS['name_first']);

						#added by VAN 10/24/2016
						#for ipbm updates
						$HTTP_POST_VARS['source'] = $_GET['from'];

						$HTTP_POST_VARS['street_name'] = addslashes($HTTP_POST_VARS['street_name']);
						$insertSuccess=false;
						$sqlExistor="SELECT pid FROM care_person WHERE name_first=".$db->qstr($HTTP_POST_VARS['name_first'])." AND name_last=".$db->qstr($HTTP_POST_VARS['name_last'])." AND name_middle=".$db->qstr($HTTP_POST_VARS['name_middle'])." AND date_birth=".$db->qstr($HTTP_POST_VARS['date_birth']);
						$alreadyExist = $db->GetOne($sqlExistor);
						if($alreadyExist) $insertSuccess=false;
						else{
							$get_lock = $db->GetOne("SELECT GET_LOCK('INSERT_INTO_CARE_PERSON',5)");
							if($get_lock){
								$insertSuccess = $person_obj->insertDataFromInternalArray();
								$db->GetOne("SELECT RELEASE_LOCK('INSERT_INTO_CARE_PERSON')");
							}
						}
						if($insertSuccess){
							
							
						// if($person_obj->insertDataFromInternalArray()){

							# If data was newly inserted, get the insert id if mysql,
							# else get the pid number from the latest primary key

							if(!$update){
								$oid = $db->Insert_ID();
								$pid=$person_obj->LastInsertPK('pid',$oid);

								if (!$pid){   # burn added March 6, 2007
									$pid = $HTTP_POST_VARS['pid'];
								}

								//EL: set the new pid
								$person_obj->setPID($pid);
								$gnder = '';
								if($sex == "f") {
									$gnder = 'female';
								}else {
									$gnder = 'male';
								}

								$name_first_suffix = explode(", ", $name_first); //get name without suffix
								$getPersonData = $pers_obj->getPersonData($pid);
								$patientData = array(
										'pid'					=> $pid,
										'firstName'				=> $name_first_suffix[0],
										'middleName'			=> $name_middle,
										'lastName'				=> $name_last,
										'gender'				=> $gnder,
										'dateOfBirth'			=> (formatDate2STD($date_birth,$date_format)),
										'street'				=> $addr_str,
										'barangay'				=> $getPersonData['brgy_nr'],
										'city'					=> $getPersonData['mun_nr'],
										'custom_middle_initial'	=> $getPersonData['custom_middle_initial'],
										'suffix'				=> $getPersonData['suffix'],
										'name_maiden'			=> $getPersonData['name_maiden'],
										'name_others'			=> $getPersonData['name_others'],
										'place_birth'			=> $getPersonData['place_birth'],
										'blood_group'			=> $getPersonData['blood_group'],
										'addr_str'				=> $getPersonData['addr_str'],
										'addr_str_nr'			=> $getPersonData['addr_str_nr'],
										'addr_zip'				=> $getPersonData['addr_zip'],
										'addr_citytown_nr'		=> $getPersonData['addr_citytown_nr'],
										'addr_is_valid'			=> $getPersonData['addr_is_valid'],
										'street_name'			=> $getPersonData['street_name'],
										'citizenship'			=> $getPersonData['citizenship'],
										'occupation'			=> $getPersonData['occupation'],
										'employer'				=> $getPersonData['employer'],
										'phone_1_code'			=> $getPersonData['phone_1_code'],
										'phone_2_code'			=> $getPersonData['phone_2_code'],
										'phone_2_nr'			=> $getPersonData['phone_2_nr'],
										'cellphone_1_nr'		=> $getPersonData['cellphone_1_nr'],
										'cellphone_2_nr'		=> $getPersonData['cellphone_2_nr'],
										'fax'					=> $getPersonData['fax'],
										'email'					=> $getPersonData['email'],
										'civil_status'			=> $getPersonData['civil_status'],
										'religion'				=> $getPersonData['religion'],
										'mother_fname'			=> $getPersonData['mother_fname'],
										'mother_maidenname'		=> $getPersonData['mother_maidenname'],
										'mother_mname'			=> $getPersonData['mother_mname'],
										'mother_lname'			=> $getPersonData['mother_lname'],
										'father_fname'			=> $getPersonData['father_fname'],
										'spouse_name'			=> $getPersonData['spouse_name'],
										'guardian_name'			=> $getPersonData['guardian_name'],
										'contact_person'		=> $getPersonData['contact_person'],
										'contact_relation'		=> $getPersonData['contact_relation'],
										'death_date'			=> $getPersonData['death_date'],
										'death_encounter_nr'	=> $getPersonData['death_encounter_nr'],
										'death_cause'			=> $getPersonData['death_cause'],
										'death_cause_code'		=> $getPersonData['death_cause_code'],
										'date_update'			=> $getPersonData['date_update'],
										'status'				=> $getPersonData['status'],
										'history'				=> $getPersonData['history'],
										'senior_ID'				=> $getPersonData['senior_ID'],
										'veteran_ID'			=> $getPersonData['veteran_ID'],
										'is_indigent'			=> $getPersonData['is_indigent'],
										'DOH_ID'				=> $getPersonData['DOH_ID'],
										'age'					=> $getPersonData['age'],
										'name_search'			=> $getPersonData['name_search'],
										'create_id'				=> $HTTP_POST_VARS['user_id']
									);
									//SAVE PATIENT INFO
//	        						require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
//									$ehr = Ehr::instance();
//									$utf8_encode_data = array_map("utf8_encode", $patientData);
//									$patient = $ehr->postAddPerson($utf8_encode_data);
//									$asd = $ehr->getResponseData();
//									$EHRstatus = $patient->status;
//
//									if(!$EHRstatus){
//										// echo "<pre>";
//										// var_dump($patient->status);
//										// var_dump($patient->msg);
//										// var_dump($asd);
//										// die();
//									}
								
								// if($response = $curl_ehr->addPatient($patientData)){
								// 	// var_dump($response);die();
								// }else{
								// 	var_dump($response);die();
								// }
							}

                            # added by VAS 11/19/2013
                            # integration to EMR starts here
                            # for doctor only
                            # Post corresponding Patient/Person demographic in EMR

                            $_POST['date_reg'] = $date_reg;
                            $_POST['pid'] = $pid;

                            #edited by VAN 10-24-2016
                            #commented this EMR function
                            /*try {
                            	//save new record
                                require_once($root_path . 'include/care_api_classes/emr/services/PatientEmrService.php');
                                $patientService = new PatientEmrService();
                                #add new argument to detect if to update patient demographic or not
                                $patientService->savePatient($pid);
                            } catch (Exception $exc) {
                                //echo $exc->getTraceAsString();
                            }*/

                            #===============================
							// KB: save other_his_no
							if( isset($_POST['other_his_org']) && !empty($_POST['other_his_org'])){
								$person_obj->OtherHospNrSet($_POST['other_his_org'], $_POST['other_his_no'], $_SESSION['sess_user_name'] );
							}

							# Save the valid uploaded photo
							if($valid_image){
								# Compose the new filename by joining the pid number and the file extension with "."
								$photo_filename=$pid.'.'.$picext;
								# Save the file
								if($img_obj->saveUploadedImage($_FILES['photo_filename'],$root_path.$photo_path.'/',$photo_filename)){
									# Update the filename to the databank
									$person_obj->setPhotoFilename($pid,$photo_filename);
								}
							} elseif (!empty($HTTP_POST_VARS['photo_data'])) {
                                //
							}

                                                        // Removed by LST ..... 12/05/2019
							# Added by LST --- 09.01.2009 ----- for saving fingerprint image.
//							$fldArray = array('pid'=>"'{$HTTP_POST_VARS['pid']}'", 'fpimage'=>"cast(unhex('".$HTTP_POST_VARS['fpcode']."') as BINARY)");
//							$db->Replace('seg_fingerprint', $fldArray, 'pid');

							# Update the insurance data
							# Lets detect if the data is already existing
							/*
														if($insurance_show) {

								#----------added by VAN-------------
								#if ($insurance_class_nr!=3){
								if (($insurance_class_nr!=3)&&($_POST["items"]!=NULL)){

									$insurance_cur = array();

									foreach (array_unique($_POST["items"]) as $i=>$v) {
										#------------------hcare_id, insurance_nr, is principal holder-----
										$insurance_cur[] = array($_POST["items"][$i],$_POST["nr"][$i],$_POST["is_principal"][$i]);
										$insurance_array_cur .= $_POST["items"][$i].",";
									}

									#from the hidden field of bulk_array_prev : previous array - detailed
									$insurance_prev = unserialize(stripslashes($bulk_array_prev));

									#from the hidden field of insurance_array_prev : previous array - only hcareid
									$insurance_array_prev = explode(",",$insurance_array_prev);

									#current array - only hcareid
									$insurance_array_cur = substr($insurance_array_cur,0,strlen($insurance_array_cur)-1);
									$insurance_array_cur = explode(",",$insurance_array_cur);

									#return the elements present in previous array and not present in the current
									#the insurance to be deleted in the table
									$not_existing_cur = array_diff($insurance_array_prev, $insurance_array_cur);

									#insurance to be deleted
									$not_existing_cur_implode = implode(",",$not_existing_cur);

									#return the elements present in current array and not present in the previous
									#the insurance to be added in the table
									$not_existing_prev = array_diff($insurance_array_cur, $insurance_array_prev);
									$not_existing_prev_implode = implode(",",$not_existing_prev);
									$not_existing_prev_explode = explode(",",$not_existing_prev_implode);

									$delete_result="DELETE FROM care_person_insurance WHERE hcare_id IN (".$not_existing_cur_implode.") AND pid = '".$pid."'";
									$ok = $db->Execute($delete_result);

									for ($i=0; $i<sizeof($not_existing_prev_explode); $i++){
										for ($j=0; $j<sizeof($insurance_cur); $j++){
											$arr_cur = $insurance_cur[$j][0];
											$arr_cur = explode(",",$arr_cur);
											if (in_array($not_existing_prev_explode[$i], $arr_cur)) {
												$NEP_detailed_array = $insurance_cur[$j];
												$pinsure_obj->addInsurance_reg($pid,$NEP_detailed_array,$HTTP_SESSION_VARS['sess_user_name'],date('YmdHis'),$insurance_class_nr);
											}
										}
									}

								}elseif (($insurance_class_nr==3)||($_POST["items"]==NULL)){
									#add script here if it is self-pay or insurance tray is empty
									#----
									$from_person_insurance = $pinsure_obj->getPersonInsuranceItems($pid);
									$row_from_person_insurance=$from_person_insurance->RecordCount();

									$sql2 = "SELECT * FROM care_encounter WHERE pid ='".$pid."'";
									$from_encounter=$db->Execute($sql2);
									$row_from_encounter=$from_encounter->RecordCount();
									if ($row_from_encounter!=0){
										while($rsObj=$from_encounter->FetchRow()) {
											$enc_cr = $rsObj["encounter_nr"];
											$sql3 = "SELECT * from seg_encounter_insurance WHERE encounter_nr='".$rsObj["encounter_nr"]."'";
											$from_seg_insurance=$db->Execute($sql3);
											$row_from_seg_insurance=$from_seg_insurance->RecordCount();

											if ($row_from_seg_insurance!=0){
												while ($row=$from_seg_insurance->FetchRow()) {
													$from_seg .= $row["hcare_id"].",";
												}
											}
										}
									}
									$from_seg = substr($from_seg, 0, strlen($from_seg)-1);
									$from_seg_list = explode(",",$from_seg);

									if ($from_seg_list[0]==NULL){
										$pinsure_obj->clearInsuranceList_reg($pid);         #clear care_person_insurance table
									}else{
										if ($row_from_person_insurance!=0){
											while ($row=$from_person_insurance->FetchRow()) {
												if (in_array($row['hcare_id'], $from_seg_list)){
													#$in_seg_firmname .= $row["firm_id"]." ,";
												}else{
													$not_in_seg .= $row["hcare_id"].",";
													#$not_in_seg_firmname .= $row["firm_id"]." ,";
												}
											}
											$not_in_seg = substr($not_in_seg, 0, strlen($not_in_seg)-1);

											$delete_result="DELETE FROM care_person_insurance WHERE hcare_id IN (".$not_in_seg.") AND pid = '".$pid."'";
											$ok = $db->Execute($delete_result);

									}
								}
									#----
								}
								#-----------------------------------
							}      */
							$newdata=1;
							if(file_exists($this->displayfile)){
								//added by VAN 07-29-2013
                                //add a encounter number for well-baby
                                //$HTTP_POST_VARS['admitted_baby'] == 0
                                # Edited by: syboy 12/11/2015 : meow; || ($ptype=='medocs') 
                                # Create well baby transactions using medical records module
                                if (($ptype=='newborn') || ($ptype=='medocs')&&($fromtemp)){
                                    /*$glob_obj->getConfig('encounter_nr_init_wellbaby');
                                    $wb_enc_nr = $GLOBAL_CONFIG['encounter_nr_init_wellbaby'];
                                    $wb_encounter_nr = (int)date('Y').$wb_enc_nr;
                                    
                                    $wellbaby->wb_encounter_nr = $wb_encounter_nr;
                                    $wellbaby->pid = $pid;
                                    $wellbaby->encounter_date = date('Y-m-d H:i:s', strtotime($date_reg));
                                    $wellbaby->encounter_class_nr = '';
                                    $wellbaby->encounter_type = '';
                                    $wellbaby->encounter_status = '';
                                    $wellbaby->is_discharged = 0;
                                    $wellbaby->discharged_date= NULL;
                                    $wellbaby->discharged_time = NULL;
                                    $wellbaby->is_maygohome = 0;
                                    $wellbaby->mgh_setdte = NULL;
                                    $wellbaby->modify_id = $HTTP_SESSION_VARS['sess_user_name'];
                                    $wellbaby->modify_time = date('Y-m-d H:i:s');
                                    $wellbaby->create_id = $HTTP_SESSION_VARS['sess_user_name'];
                                    $wellbaby->create_time = date('Y-m-d H:i:s');
                                    
                                    $encounter_obj->saveWellBabyEncounterNr($wellbaby);*/
                                    
                                    $glob_obj->getConfig('encounter_%');
                                    if($GLOBAL_CONFIG['encounter_nr_fullyear_prepend'])
                                        $ref_nr=(int)date('Y').$GLOBAL_CONFIG['encounter_nr_init'];
                                    else 
                                        $ref_nr=$GLOBAL_CONFIG['encounter_nr_init'];
                                        
                                    $triage = 'wellbaby';
                                    $last_enc_nr = $encounter_obj->getLastEncounterNr($triage);
                                                        
                                    if ($last_enc_nr)
                                        $ref_nr = $last_enc_nr;
                                    #echo "<br>last_enc_nr = ".$last_enc_nr;                            
                                    $wellbaby['encounter_nr']=$encounter_obj->getNewEncounterNr($ref_nr,12);     
                                    #echo "<br> sql = ".$encounter_obj->sql;
                                    $wellbaby['pid']=$pid;
                                    $wellbaby['current_dept_nr']=191;
                                    
                                    $birth_time = $HTTP_POST_VARS['birth_time'].":00 ".$HTTP_POST_VARS['birth_time_selAMPM'];
									if (($birth_time!=''))
				                        $birth_time = date('H:i:s',strtotime($birth_time));

                                    // $wellbaby['encounter_date']=date('Y-m-d H:i:s', strtotime($date_reg));//commented by Nick 05-21-2014
                                    $wellbaby['encounter_date'] = $HTTP_POST_VARS['date_birth'] . ' ' . $HTTP_POST_VARS['birth_time'];//added by Nick 05-21-2014
                                    
                                    #care_type_encounter
                                    $wellbaby['encounter_class_nr']=12; 
                                    $wellbaby['encounter_type']=12;
                                    $encoder = $HTTP_SESSION_VARS['sess_login_username'];
                                    $wellbaby['modify_id']=$encoder;
                                    $wellbaby['modify_time']=date('YmdHis');
                                    $wellbaby['create_id']=$encoder;
                                    $wellbaby['create_time']=date('YmdHis');
                                    $wellbaby['history']='Create: '.date('Y-m-d H:i:s').' = '.$encoder;
                                    
                                    $encounter_obj->setDataArray($wellbaby);

                                    $db->StartTrans();                                
                                    if($encounter_obj->insertDataFromInternalArray()){
                                        $bSuccess = $encounter_obj->update_Encounter_Tracker($wellbaby['encounter_nr'],$triage);
                                        #echo "<br>up = ".$encounter_obj->sql;
                                    }
                                    
                                    if (!$bSuccess){
                                    	$db->FailTrans();
                                    }
                                    $db->CompleteTrans();
                                    #exit();
                                }#end of wellbaby condition                                                             
                                        require_once($root_path."include/care_api_classes/biometric/class_biometric.php");                                                
										try {										
                                        $bsuccess = Biometric::saveFingerprint($pid, $_POST["fptemplate"]);                                            
										}
										catch (Exception $ex) {													
											echo "ERROR: ".$ex->getMessage();
										}
                                        if ($ERSave){                                            
                                            if ($bsuccess) {
                                                $encounter_date = date('Y-m-d H:i:s', strtotime($date_reg));
                                                header("Location: patient_er_encounter.php".URL_REDIRECT_APPEND."&pid=$pid&encounter_date=$encounter_date&current_dept_nr=$current_dept_nr&category=$category&from=$from&newdata=1&target=entry&ptype=$ptype");                                            
                                                exit;
                                            }
                                        } else {
                                            if ($bsuccess) {                                                                                        
                                                header("Location: $this->displayfile".URL_REDIRECT_APPEND."&pid=$pid&from=$from&newdata=1&target=entry&ptype=$ptype");
                                                exit;
                                            }
                                        }
                                }else{
                                        echo "Error! Target display file not defined!!";
                                }
                                    // }
                                    }else {
                                            //"<p>$person_obj->getErrorMsg()<p>$LDDbNoSave";
                                            $permission_val = explode(" ",$HTTP_SESSION_VARS['sess_permission']);
                                            $manual_hrn = false;
                                            /*foreach($permission_val as $val){
                                                    echo "value: ".$val."<br/>";
                                            }*/
                                            if(in_array("_a_1_ipdpatientmanualhrn", $permission_val)){
                                                    echo "<script>alert('HRN Exists!');</script>";
                                            }else{
                                                    echo "<p>Person already exists.<p>$LDDbNoSave";
                                            }

                        //echo "<p>HRN Exists! $person_obj->getErrorMsg()<p>$LDDbNoSave";
                            //echo "<p>$person_obj->getErrorMsg()<p>$LDDbNoSave";
                                    }
                                }
                        }                                                                
                                        
                    } // end of if(!$error)
		}elseif(!empty($this->pid)){
			 # Get the person?s data
			if($data_obj=&$person_obj->getAllInfoObject()){

				$zeile=$data_obj->FetchRow();
				extract($zeile);
				$dbfromtemp = $fromtemp;
				# Get the related insurance data
				#$p_insurance=&$pinsure_obj->getPersonInsuranceObject($pid);
				$p_insurance=$pinsure_obj->getPersonInsuranceItems($pid);
				#echo "sql = ".$pinsure_obj->sql;
				#echo "count = ".$p_insurance->RecordCount()."<br>";
				#$row=$p_insurance->FetchRow();
				#print_r($row);
				if($p_insurance==FALSE) {
					$insurance_show=TRUE;
				} else {
					if(!$p_insurance->RecordCount()) {
						$insurance_show=TRUE;
					} elseif ($p_insurance->RecordCount()>=1){
						#echo "elseif";
						$buffer= $p_insurance->FetchRow();
						#print_r($buffer);
						extract($buffer);
						#-----------
						$insurance_show=TRUE;

						#$insurance_firm_name=$pinsure_obj->getFirmName($insurance_firm_id);

					#}elseif ($p_insurance->RecordCount()>=2){
						#$insurance_show=TRUE;
					} else {
						$insurance_show=FALSE;
					}
				}

//								if ($fpcode = $person_obj->getFPImage($this->pid))
//										$fpimage = $fpcode;
//								else
//										$fpimage = '';
			}
		} else {
			#$date_reg=date('Y-m-d H:i:s');
			#edited by VAN 01-21-09
			if (isset($HTTP_POST_VARS['reg_date'])){
				$date = date("Y-m-d",strtotime($HTTP_POST_VARS['reg_date']));
				$time = $HTTP_POST_VARS['reg_time'].":00 ".$HTTP_POST_VARS['selAMPM2'];
				$time = date("H:i:s",strtotime($time));
				$date_reg = $date." ".$time;
				$date_reg=date('Y-m-d H:i:s',strtotime($date_reg));
			}else
				$date_reg=date('Y-m-d H:i:s');
			#------------------
		}
		# Get the insurance classes
		$insurance_classes=&$pinsure_obj->getInsuranceClassInfoObject('class_nr,name,LD_var AS "LD_var"');
		//echo "getInsuranceClassInfoObject = ".$pinsure_obj->sql;

		#added by VAS 03/21/2017
		if ((empty($photo_filename)) && ($photo_data))
			$photo_filename = $photo_data;

		include_once($root_path.'include/inc_photo_filename_resolve.php');

		#
		#
		########  Here starts the GUI output #######################################################
		#
		#

		# Start Smarty templating here
		# Create smarty object without initiliazing the GUI (2nd param = FALSE)

		include_once($root_path.'gui/smarty_template/smarty_care.class.php');
		$this->smarty = new smarty_care('common',FALSE);

		$img_male=createComIcon($root_path,'spm.gif','0');
		$img_female=createComIcon($root_path,'spf.gif','0');

		if(!empty($this->pretext)) $this->smarty->assign('pretext',$this->pretext);

		# Collect extay javascript code
		$sTemp='';
		ob_start();

		#---------add a query here using the $HTTP_SESSION_VARS['sess_user_name']
		require_once($root_path.'include/care_api_classes/class_department.php');
		$dept_obj=new Department;
		if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
			$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
		else
			$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
		$dept_belong = $dept_obj->getUserDeptInfo($seg_user_name);

		#echo "here = ".$dept_belong['name_formal'];
		$this->smarty->assign('LDDept',$LDDepartment);
		$this->smarty->assign("sDeptInput",'<input  name="dept" type="text" value="'.$dept_belong['name_formal'].'"  size="35" readonly>');
		#-----------------------------------------------

		#added by VAN 06-25-08
		#if (stristr($dept_belong['id'],'Birth'))
		#echo "allow = ".$allow_newborn_register;
		#if ($dept_belong['dept_nr']==151)
		if (($allow_newborn_register)&&(($ptype=='newborn')||($ptype=='medocs'))){
			$birth_section = true;
			$fromtemp = 1;
	 }else{
			$birth_section = false;
			$fromtemp = 0;
	 }
		echo '<script type="text/javascript" src="'.$root_path.'js/shortcuts.js"></script>';

		//added By Mats	
		$encount_nr = $_GET['encounter_nr'];


		$sql1 = "SELECT is_final,ce.encounter_nr,pid FROM seg_billing_encounter AS sbe 
				 INNER JOIN care_encounter AS ce ON ce.encounter_nr = sbe.encounter_nr 
				 WHERE ce.encounter_nr = ".$db->qstr($encount_nr)." AND sbe.is_deleted != 1 ORDER BY sbe.`bill_dte` DESC";
				
		$result = $db->GetRow($sql1);
		
		if($result){
			$ins_nr = $result['encounter_nr'];
			$is_final = $result['is_final'];
			$is_pid = $result['pid'];
			
		}
		// var_dump($is_final);exit();

		$sql2 = "SELECT sei.encounter_nr as nrs ,seim.encounter_nr as nrs_info FROM seg_encounter_insurance AS sei 
 				 INNER JOIN seg_encounter_insurance_memberinfo AS seim ON sei.encounter_nr = seim.encounter_nr where sei.encounter_nr=".$db->qstr($encount_nr)."AND seim.encounter_nr=".$db->qstr($encount_nr);
		$result_nr = $db->GetRow($sql2);
		if($result_nr){
			$nrs = $result_nr['nrs'];
			$nrs_info=$result_nr['nrs_info'];
		}
		// var_dump($sql2);die;
		//Add ended here by Mats
                
    require_once($root_path."include/care_api_classes/biometric/class_biometric.php");
    $cClientId = isset($_POST["clientId"]) ? $_POST["clientId"] : Biometric::uniqidReal();

	$uri = $_SERVER['REQUEST_URI'];
	$i = stripos($uri, "/", 1);
	$uri = substr($uri, 0, $i+1);
	$fpurl = $_SERVER['SERVER_ADDR'].$uri;		
?>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/reg-insurance-gui.js?t=<?=time()?>"></script>
<script type="text/javascript" src="<?=$root_path?>modules/biometric/js/biometric.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>modules/registration_admission/css/fpbiometric.css" type="text/css" />

<script type="text/javascript" src="<?=$root_path?>js/socket.io.js"></script>
<script language="javascript">            
	var outputClientId = '<?php echo $cClientId; ?>';
	var socketServerHost = '<?php echo BIOMETRIC_SOCKET_SERVER; ?>';
</script>
<script src="<?=$root_path?>modules/biometric/js/registerFingerprint.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script language="javascript">
    // Needed to remove conflict between socket.io.js and prototype.js
    if(window.Prototype) {
        delete Object.prototype.toJSON;
        delete Array.prototype.toJSON;
        delete Hash.prototype.toJSON;
        delete String.prototype.toJSON;
    }
</script>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.maskedinput.js"></script>

<!-------------------------------------------->
<script  language="javascript">
<!--
        var $J = jQuery.noConflict();
        
        jQuery(function($){
             $J("#date_text").mask("99/99/9999");
             $J("#vac_date").mask("99/99/9999");
        });
        
        jQuery(function($){
             $J("#birth_time").mask("99:99");
        });
                
		//added by VAN------
		$( document ).ready(function() {
	    console.log( "ready!" );
		});



		var trayItems = 0;

		function openOrderTray() {
			window.open("seg-reg-insurance-tray.php<?=URL_APPEND?>&clear_ck_sid=<?=$clear_ck_sid?>","patient_select","width=720,height=500,menubar=no,resizable=no,scrollbars=yes");
		}

/*
		function BMP() {
			return overlib(
				OLiframeContent('<?= $root_path ?>modules/registration_admission/brgy_muni_prov.php?',
					700, 400, 'fSelEnc', 0, 'auto'),
				WIDTH,700, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, '<img src=<?= $root_path?>/images/close.gif border=0 >',
				CAPTIONPADDING,4,
				CAPTION,'Select registered person',
				MIDX,0, MIDY,0,
				STATUS,'Select registered person')
		}
*/
		// added by: syboy 03/16/2016 : meow
		function searchMother(){
			return overlib(
				OLiframeContent('<?= $root_path?>modules/registration_admission/seg-select-enc-mothersearch.php?search=searchmother', 700, 400, 'fOrderTray', 0, 'auto'),
					WIDTH,700, TEXTPADDING,0, BORDER,0,
					STICKY, SCROLL, CLOSECLICK, MODAL,
					CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
					CAPTIONPADDING,2,
					CAPTION,'Select registered person',
					MIDX,0, MIDY,0,
					STATUS,'Select registered person');
		}
		//  ended syboy

		//added by VAN 05-06-08
		function checkPatient(){

			var ans = confirm("Does the patient is a senior citizen with SC ID, \n DMC personnel or belong to a minority group?");

			if (ans){
				var pid = "charity";
				window.location.href = "patient_register.php?pid="+pid;
			}else{

				var response = prompt("Please enter the fullname of the patient \n and follow this format 'Surname Suffix Name, Given Name' : ");

				if (response){
					//alert('hello = '+response);
					if (response.match(","))
						xajax_checkinDBperson(response);
					else{
						alert('Please follow the format in entering a name.');
						checkPatient();
					}
				}else{
					checkPatient();
				}
			}
		}

		function setPatientPID(pid){
			//alert(pid);
			if ((pid!=0)&&(pid!='paid')){
				//if data is already exist and already paid
				window.location.href = "patient_register_show.php?pid="+pid;
			}else if (pid=='paid'){
				window.location.href = "patient_register.php?pid="+pid;
			}else{
				alert("Please pay first at the cashier for consulation fee.");
				//window.location.href = "patient_register.php";
				window.location.href = "patient_register_search.php?target=search";
			}
		}
		//-------------------------------

		function jsShowDetails(showDetails){
			//added by VAN
			var d = document.aufnahmeform;
			//alert('showDetails = '+showDetails);
			var personDetails = getElementsByClass("personDetails");
			for (var i=0; i< personDetails.length; i++){
				personDetails[i].style.display = (showDetails) ? '' : 'none';
			}
			blocking("arrow_up", showDetails);
			blocking("arrow_down", !showDetails);

			//------------added by VAN-----------
			// if (d.insurance_class_nr[2].checked == true){
			// 	document.getElementById('iconIns').style.display = 'none';
			// }else if((d.insurance_class_nr[0].checked == true)||(d.insurance_class_nr[1].checked == true)){
			// 	document.getElementById('iconIns').style.display = '';
			// }else{
			// 	d.insurance_class_nr[2].checked = true;
			// 	document.getElementById('iconIns').style.display = 'none';
			// }

			//-------------------------------------
		}

		function forceSave() {
			if(aufnahmeform.religion.value==1){
				alert("<?php echo $LDPlsEnterReligion; ?>");
				aufnahmeform.religion.focus();
				return false;
			}else{
			$('forceSaver').disabled = true;
			document.aufnahmeform.mode.value="forcesave";
			document.aufnahmeform.submit();
			}
		}

		function showpic(d) {
                    if(d.value) document.images.headpic.src="<?php echo $root_path.$photo_path.'/' ?>" + d.value;
/*			if(d.value) document.images.headpic.src=d.value;     ---- commented by Bong - 5/10/2007 */
		}

                function showFPImage(d) {
                    if (d.value) document.images.fpimage.src="<?php echo $root_path.$fpphoto_path.'/' ?>" + d.value;
                }
                
                function launchFPReg(clientid) {
                    window.location.href="https://<?php echo $fpurl ?>modules/biometric/launchRegisterFingerprint.php<?php echo URL_REDIRECT_APPEND ?>&clientId="+clientid;
                }

		function takepic() {
			var url = '<?= $root_path.'index.php?r=devices/camera' ?>';
            var $dialog = $J('<div></div>');
            $dialog.html('<iframe width="100%" height="100%" src="' + url + '" allow="camera"></iframe>').dialog({
                autoOpen: true,
                modal: true,
                resizable: false,
                title: "Take a photo!",
                width: 445,
                height: 451,
                close: function () {
                    var iframe = $dialog.find('iframe').get(0);
                    var contentWindow = iframe.contentWindow;
                    contentWindow.postMessage('closeDevice', '*');
                },
                buttons: {
                    'Take Picture!': function() {
                        var iframe = $dialog.find('iframe').get(0);
                        var contentWindow = iframe.contentWindow;
                        var webcam = contentWindow.Webcam;
                        webcam.snap( function(dataUri) {
                            $J('#photo_data').val(dataUri);
                            $J('#photo-img')
                                .attr('width', 200)
                                .attr('src', dataUri);
                        });
                        contentWindow.postMessage('closeDevice', '*');
                        $J(this).dialog('close');
                    }
                }
            });
		}

				function enrollFP() {
						urlholder = "<?php echo $root_path ?>include/inc_fingerprint_enrollment.php";
						nleft = (screen.width - 640) / 2;
						ntop  = (screen.height - 480) / 2;
						printwin = window.open(urlholder, "Fingerprint", "toolbar=no,status=no,menubar=no,width=640,height=480,location=no,directories=no,resizable=no,scrollbars=no,top=" + ntop + ",left=" + nleft);
				}

		function popSearchWin(target,obj_val,obj_name){
			//urlholder="./data_search.php<?php echo URL_REDIRECT_APPEND; ?>&target="+target+"&obj_val="+obj_val+"&obj_name="+obj_name;
			//edited by VAN 03-19-08
			urlholder="<?php echo $root_path ?>/modules/registration_admission/data_search.php<?php echo URL_REDIRECT_APPEND; ?>&target="+target+"&obj_val="+obj_val+"&obj_name="+obj_name;
			DSWIN<?php echo $sid ?>=window.open(urlholder,"wblabel<?php echo $sid ?>","menubar=no,width=400,height=550,resizable=yes,scrollbars=yes");
		}

				function emailValidation(thisEmail)
				{
				/*   checks the email addess entry using a regular expression
				 bun added: August 29, 2006
			*/
			 var pattern =/^[a-zA-Z][\w\.-]*[a-zA-Z0-9]@[a-zA-Z0-9][\w\.-]*[a-zA-Z0-9]\.[a-zA-Z][a-zA-Z\.]*[a-zA-Z]$/;
					 if (thisEmail.match(pattern))

					return true; /* the email address entry is valid */
			 else
					return false;
				}

		function getBirthdate(obj){
			var age = parseInt(obj.value);
			var bdate = document.aufnahmeform.date_birth.value;
			var dateNow = new Date();
			if (!isNaN(age)){
				document.aufnahmeform.date_birth.value=(dateNow.getMonth()+1)+"/"+dateNow.getDate()+"/"+(dateNow.getFullYear()-age);
				document.aufnahmeform.age.value=age;
			}else{
				if (obj.value){
					if (bdate){// retain the old value if existing
						getAge(document.aufnahmeform.date_birth);
					}else{//reset birthdate and age
						document.aufnahmeform.age.value='';
					}
				}else{
						document.aufnahmeform.date_birth.value='';
						document.aufnahmeform.age.value='';
						
				}
			}
			

		}

//		function getAge(){
//			var dob = document.aufnahmeform.date_birth.value;
		/*
		function getAge(obj){
			var dob;
			var dateNow = new Date();
			var valid;
			//  mm/dd/yyyy
			try{
				valid = IsValidDate(obj,'MM/dd/yyyy');
				dob = obj.value;
				var dobMonth = dob.substring(0,2);
				var dobDay = dob.substring(3,5);
				var dobYear = dob.substring(6,10);
				var pastDate = new Date(2000,dobMonth-1,dobDay);
				var presentDate = new Date(2000,dateNow.getMonth(),dateNow.getDate());
				var age = dateNow.getFullYear() - parseInt(dobYear);
				var ageYear = (presentDate.getTime()-pastDate.getTime())/31536000000;///86400000;
				age = age + ageYear;
				var msg = " dob = '"+dob+"' \n dateNow = '"+dateNow+"' \n dobMonth = '"+dobMonth+"' \n"+
				" dobDay = '"+dobDay+"' \n dobYear = '"+dobYear+"' \n pastDate = '"+pastDate+"' \n"+
				" presentDate = '"+presentDate+"' \n age = '"+age+"' \n ageYear = '"+ageYear+"'";
				if ((isNaN(age)) ||(dobMonth==0)||(dobDay==0)||(dobYear==0)){
					document.aufnahmeform.age.value='';
				}else{
					document.aufnahmeform.age.value=age;
					document.aufnahmeform.place_birth.focus();
				}
			}catch(e){
				document.aufnahmeform.age.value='';
			}
		}
		*/
		function getAge(){
			var dob;
			var dateNow = new Date();
			var valid;
			var obj = document.aufnahmeform.date_text;
			//  mm/dd/yyyy
			try{
				valid = IsValidDate(obj,'MM/dd/yyyy');
				dob = obj.value;
				var dobMonth = dob.substring(0,2);
				var dobDay = dob.substring(3,5);
				var dobYear = dob.substring(6,10);
				var pastDate = new Date(2000,dobMonth-1,dobDay);
				var presentDate = new Date(2000,dateNow.getMonth(),dateNow.getDate());
				var age = dateNow.getFullYear() - parseInt(dobYear);
				var ageYear = (presentDate.getTime()-pastDate.getTime())/31536000000;///86400000;
				age = age + ageYear;
				var msg = " dob = '"+dob+"' \n dateNow = '"+dateNow+"' \n dobMonth = '"+dobMonth+"' \n"+
				" dobDay = '"+dobDay+"' \n dobYear = '"+dobYear+"' \n pastDate = '"+pastDate+"' \n"+
				" presentDate = '"+presentDate+"' \n age = '"+age+"' \n ageYear = '"+ageYear+"'";
				if ((isNaN(age)) ||(dobMonth==0)||(dobDay==0)||(dobYear==0)){
					document.aufnahmeform.age.value='';
				}else{
					document.aufnahmeform.age.value=age;
					document.aufnahmeform.place_birth.focus();
				}
			}catch(e){
				document.aufnahmeform.age.value='';
			}
		}

		//added by VAN 04-26-08
		function checkCivilStat(){
			var d = document.aufnahmeform;
			//alert(d.age.value);
			//if (d.age.value<=15){

			if (d.age.value<18){
				d.civil_status[0].checked = true;
			}else{
				d.civil_status[0].checked = false;
			}
		}
		/**
		 * added by rnel 08-13-2016
		 */
		function birthTimeFocus() {
        	var d = document.aufnahmeform;
        	d.birth_time.focus();
        }

		function checkIfSenior(){
			var d = document.aufnahmeform;
			
			//if (d.age.value<=15){
			if (d.age.value>=60){
				document.getElementById('senior_row').style.display ='';
				document.getElementById('veteran_row').style.display ='';
			}else{
				document.getElementById('senior_row').style.display ='none';
				document.getElementById('senior_ID').value='';
				document.getElementById('veteran_row').style.display ='none';
				if(d.age.value>=20&&d.age.value!=""){
					var sex;
					var age = $('age').value;
					var dept_nr = $J('current_dept_nr').value;

					if (document.aufnahmeform.sex[0].checked==true)
						sex = 'm';
					else if (document.aufnahmeform.sex[1].checked==true)
						sex = 'f';
					else
						sex = 'm';

					if (age=='')
						age = 0;

					//alert(sex+" - "+age+" - "+dept_nr);
					xajax_validateDept(sex,age,dept_nr);
				}
			}
		}

		function checkIfVeteran(){
			if (document.getElementById('isVeteran').checked==true){
				document.getElementById('vetID').style.display ='';
			}else{
				document.getElementById('vetID').style.display ='none';
			}
		}

		function checkIfIndigent(){
			if (document.getElementById('is_indigent').checked==true){
				document.getElementById('span_indigent').style.display ='';
				document.getElementById('td_indigent').color = 'red';
			}else{
				document.getElementById('span_indigent').style.display ='none';
				document.getElementById('td_indigent').color = '';
			}
		}

		function chkform(d) {
				/*   This will trim the string i.e. no whitespaces in the beginning and end of a string
									AND only a single whitespace appears in between tokens/words
				 bun added: August 28, 2006
			*/
			var ERswitch = document.aufnahmeform.ERSave.value;
			var x=d.getElementsByTagName("input");

			for(var i=0;i<x.length;i++){
				if ((x[i].id=='photo_filename') || (x[i].id=='photo') || (x[i].id=='photo1')){
					//nothing
				}else{
					x[i].value = x[i].value.replace(/^\s+|\s+$/g,"");
					x[i].value = x[i].value.replace(/\s+/g," ");
				}
			}

			// added by carriane 08/09/18
			var first_name_temp = d.name_first.value;

			if(first_name_temp.indexOf(',') > -1){
				if(first_name_temp.indexOf(', ') == -1){
					alert("<?php echo $LDPlsSuffixProperFormat; ?>");
					d.name_first.focus();
					return false;
				}
			}
			// end carriane

			//alert('here');
			if(d.name_last.value==""){
				alert("<?php echo $LDPlsEnterLastName; ?>");
				d.name_last.focus();
				return false;
			}
	

			if(d.name_first.value==""){
				alert("<?php echo $LDPlsEnterFirstName; ?>");
				d.name_first.focus();
				return false;
			//}else if ((d.date_birth.value=="")&&(!ERswitch)){
			//commented by VAN 04-30-08

			}else if ((d.date_birth.value=="")&&(d.age.value=="")){
				alert("<?php echo $LDPlsEnterDateBirth; ?>");
				d.date_birth.focus();
				return false;
			//}else if((d.sex[0]&&d.sex[1]&&!d.sex[0].checked&&!d.sex[1].checked)&&(!ERswitch)){

			}

			if(d.sex[0]&&d.sex[1]&&!d.sex[0].checked&&!d.sex[1].checked){
				alert("<?php echo $LDPlsSelectSex; ?>");
				return false;
			//}else if((d.street_name.value=="")&&(!ERswitch)){

			//commented by VAN 04-28-08
			/*
			}else if(d.street_name.value==""){
				alert("Please enter the house number or street address!");
				d.street_name.focus();
				return false;
			//}else if((d.brgy_nr.value==0)&&(!ERswitch)){
			*/
			}

			/* commented by janken for disabling the mandatory field of barangay and municipality 11/14/2014
			if(d.brgy_nr.value== 'NULL'){
				alert("Please select a barangay");
				d.brgy_nr.focus();
				return false;
			//added by VAN 05-09-08
			/*
			}else if ((document.getElementById('is_indigent').checked==true)&&(d.ethnic_orig.value==1)){
				alert("Please enter the ethnic group");
				d.ethnic_orig.focus();
				return false;
			
			/*}

			if(d.brgy_nr.value=='NULL' && d.mun_nr.value==-1) {
				alert("Please select a municipality");
				d.mun_nr.focus();
				return false;
			//added by VAN 05-09-08
			/*
			}else if ((document.getElementById('is_indigent').checked==true)&&(d.ethnic_orig.value==1)){
				alert("Please enter the ethnic group");
				d.ethnic_orig.focus();
				return false;
			*/
			//}

			//if ((d.birth_section.value==1)||(d.showBirth.value==1)){
			if (d.showBirth.value==1){
                //edited by aiza
				if (($('birth_time').value=='')||($('birth_time').value=='__:__') && d.is_temp_bdate.value == 0){
                    alert("Please enter the birth time");
                    $('birth_time').focus();
                    return false;
                }else if((d.mother_fname.value=="")||(d.mother_fname.value==" ")){
					alert("Please enter the mother's first name");
					d.mother_fname.focus();
					return false;
				}else if((d.mother_lname.value=="")||(d.mother_lname.value==" ")){
					alert("Please enter the mother's last name");
					d.mother_lname.focus();
					return false;
				}
			}

			var c_date = new Date();
			var b_date = new Date(d.date_birth.value);

			if(isValidDate2(d.date_birth.value)){
				if(b_date > c_date){
					alert("Date of Birth must be earlier or the same with the current date.");
					d.date_birth.focus();
					d.date_birth.value = '';
					return false;
				}
			} 
			else {
				if(d.is_temp_bdate.value == 0){ 
					alert("Invalid date entered.");
					d.date_birth.focus();
					d.date_birth.value = '';
					return false;	
				}					
			}

		// Added by Matsuu
		
			if(d.religion.value==1){
				alert("<?php echo $LDPlsEnterReligion; ?>");
				d.religion.focus();
				return false;
			}
	// Ended here..

			return true;
		}/* end of function chkform */

		function validateDept(){
			var sex;
			var age = $('age').value;
			var dept_nr = $('current_dept_nr').value;

			if (document.aufnahmeform.sex[0].checked==true)
				sex = 'm';
			else if (document.aufnahmeform.sex[1].checked==true)
				sex = 'f';
			else
				sex = 'm';

			if (age=='')
				age = 0;

			//alert(sex+" - "+age+" - "+dept_nr);
			xajax_validateDept(sex,age,dept_nr);
		}

		function ajxSetDepartment(dept_nr) {
				document.aufnahmeform.current_dept_nr.value = dept_nr;

		}

		function preset(){
			var d = document.aufnahmeform;
			var ptype = '<?=$ptype?>';
            var allow_updateNameData = '<?=($allow_updateNameData||($isIPBM&&$ipbmcanUpdatePatient))?>';
            var update = '<?=$_GET['update']?>';

			d.name_last.focus();
			//alert(ptype);
			if (ptype=='er'){
				$('current_dept_nr').setAttribute("onchange","validateDept()");
				//$('bsaveER').style.display = '';
				//$('bsave').style.display = 'none';
			}/*else{
				$('bsaveER').style.display = 'none';
				$('bsave').style.display = '';
			}*/
            if (update==1){
                if (allow_updateNameData==1){
                    $('name_last').readOnly  = false;
                    $('name_first').readOnly  = false;
                    $('name_middle').readOnly  = false;
                }else{
                    $('name_last').readOnly  = true;
                    $('name_first').readOnly  = true;
                    $('name_middle').readOnly  = true;
                }
            } 
		}

		function jsSetERSave(){
			document.aufnahmeform.ERSave.value="1";
		}

		function getPHICrequirement(){
			var src1 ="<div style='background:red;' id='dialog'><center><h1 style='color:#ffffff;'>Please select document/s attached</h1></center><h2 style='color:#ffffff;'><input type='checkbox' value='Birth Certificate' id='documents' name='documents'>Birth Certificate</h2><br>"+
												// "<h2 style='color:#ffffff;'><input type='checkbox' id='documents' value=' Valid ID' name='documents'>Valid ID/s except PHIC</h2><br>"+
												"<h2 style='color:#ffffff;'><input type='checkbox' id='documents' value='Marriage Contract' name='documents'>Marriage Contract</h2><br>"+
												"<h2 style='color:#ffffff;'><input type='checkbox' id='documents' value='Voters ID/Certificate' name='documents'>Voters ID/Certificate</h2><br>"+
												"<h2 style='color:#ffffff;'><input type='checkbox' id='documents' value='Others' name='documents' onclick ='resetData();'>Others: <input type='text' id='others'></h2>"+
												"<h7 style='color:#ffffff;'>{NOTE: Special Characters (\\,$,#,!,@,%,^,*,{,},(,|,),[],~,+,=,>,<,:,;) are not allowed}</h7>"
												"</div>";
								$J(src1).dialog({
				                        autoOpen: true,
				                        modal:true,
				                        height: "auto",
				                        width: "70%",
				                        resizable: false,
				                        show: "fade",
				                        hide: "explode",
				                        title: "Updated Data.",
				                        position: "top", 
				                        buttons: {
				                            "OK": function() 
				                            {
				                            	var otherInfo = $J('#others').val();
				                               if($J('input[name="documents"]:checked').val()){
				                               	if($J('input[type="checkbox"][value="Others"]:checked').val() &&  $J.trim(otherInfo)!='' || !$J('input[type="checkbox"][value="Others"]:checked').val() &&  $J.trim(otherInfo)==''  ){
				                               		getPHICDetails();
				                               		
				                               	}else{
				                               		if(!$J('input[type="checkbox"][value="Others"]:checked').val()){
					                               		alert("When putting a Requirement, Others\' needs to be checked.");
					                               		return;
					                               	}else{
					                               		alert("You need to put some Requirement if you choose Others.");
					                               		return;
					                               	}
				                          				                               		
				                               	}
				                               // $J( this ).dialog( "close" );
				                           }else{
				                           	alert("You need to choose requirement to proceed.");
				                           }
				                            } ,
							                  "Cancel": function () 
							                {
							                	$J('#dialog').empty();
							                    $J(this).dialog("destroy");
							                }
				                        },
				                        close: function(ev, ui) { $J(this).remove(); }
				                    });

		}

		function getPHICDetails(){
			var inform = [];
			var info = $J('input[type="checkbox"]:checked');
				for (var i = 0; i < info.length; i++) {
					if(info[i].value=="Others"){
						// if($J.inArray($J('#others').val(),inform) < 0)

						inform.push($J('#others').val());
						// alert(inform);
						if(/^[\w\-\s\u00f1\u00d1.,_/&`']*$/.test(inform) == false){
						alert('Special Characters are not allowed');
						$J(src1).reload();
								}else{

								}
						

					}
					else{
						//if wala sa array i-push, to avoid double entry
						// alert(info[i].value);
						if($J.inArray(info[i].value,inform) < 0)
							inform.push(info[i].value);
					}
				}
			
 			var is_pid = '<?=$pid?>';
			var otherInfo = $J('#others').val();
			var encoder='<?=$_SESSION[sess_login_username]?>';
			var name_first_data_value=document.getElementById("name_first").value;
			var DOB_data = document.getElementById("date_text").value;
			var status_data_value = document.aufnahmeform.civil_status.value;
		    var name_last_data_value=document.getElementById("name_last").value;
			var info = $J('input[name="documents"]:checked').val();
			if(info!='Others'){
			var dataReq = inform;
			// alert(dataReq);
			
				 // $J('#others').val("");
			}
			else{
			var dataReq = otherInfo;

			// alert(dataReq);
			}
			if(document.aufnahmeform.sex.value == "f"){
							gender = "Female";
						}
						else
						{
							gender = "Male";
						}
				var src = "<div style='background:red;'><center><h1 style='color:#ffffff;'>Patient has already submitted PHIC requirement in Billing</h1></center><h1 style='color:#ffffff;'><br><br>Last Name: "+name_last_data_value+" </h1>"+
												"<h1 style='color:#ffffff;'>First Name: "+name_first_data_value+" </h1>"+
												"<h1  style='color:#ffffff;'>Gender: "+gender+"</h1>"+
												"<h1  style='color:#ffffff;'>Date of Birth: "+DOB_data+" </h1>"+
												"<h1  style='color:#ffffff;'>Civil Status: "+status_data_value+" </h1>"+
												"<h1  style='color:#ffffff;'>Requirement/s Submitted: "+dataReq+" </h1>"+
												"</div>";

									$J(src).dialog({
				                        autoOpen: true,
				                        modal:true,
				                        height: "550",
				                        width: "70%",
				                        resizable: false,
				                        show: "fade",
				                        hide: "explode",
				                        title: "Data Updated",
				                        position: "top", 
				                        buttons: {
				                            "OK": function() 
				                            {
				                            
				                                xajax_addDocuments(is_pid,dataReq.toString(),encoder);
				                                $J(".ui-dialog-content").button("disable");
				                            	$J(".ui-dialog-content").dialog( "destroy" );
				                              
				                            } 
				                        }
				                    });

		}
		function formSubmitter(){
			document.aufnahmeform.submit();
		}
		
		function resetData () {
			// body...
			if(!$J('input[name="documents"][value="Others"]:checked').val()){
					$J('#others').val('');
			} 
		}

		function gotoCheck(){
				var ptype = '<?=$ptype?>';
				var update = '<?=$_GET['update']?>';
				var namebutton, nameimage,namespan;

				var encount_nr = '<?=$encount_nr?>';
				var is_final = '<?=$is_final?>';

				var is_pid = '<?=$is_pid?>';
				var nrs_info ='<?=$nrs_info?>';
				var nrs ='<?=$nrs?>';
				var ins_nr = '<?=$ins_nr?>';
			   
             	
             	var selectedVal = "";
				var selected = $J("input[type='radio'][name='civil_status']:checked");
				selectedVal = selected.val();
             	var selectdatebirth = "";
                selectdatebirth=document.getElementById("date_text").value;
                selectdatebirth=new Date(selectdatebirth);
                selectdatebirth = selectdatebirth.getFullYear()+'-'+(selectdatebirth.getMonth()+1)+'-'+selectdatebirth.getDate();

				if (ptype=='er'){
						jsSetERSave();	
				}

				if ((ptype=='er')&&(update!=1)){
						namebutton = "ERsubmit";
						nameimage = "../../gui/img/control/default/en/en_er_savedisc.gif";
						namespan = "bsaveER";
				}else{
						namebutton = "saveButton";
						nameimage = "../../gui/img/control/default/en/en_savedisc.gif";
						namespan = "bsave";
				}
				//alert(namespan);
				//$(namespan).innerHTML = '<img id="'+namebutton+'" name="'+namebutton+'" src="'+nameimage+'"  title="<?=$LDSaveData?>" border=0 align="absmiddle">';
				var ans = confirm('Process this transaction?');
				if (ans){
					//document.aufnahmeform.onsubmit = chkform(aufnahmeform);
					if (chkform(document.aufnahmeform)){
						
							//added and edit by Mmats 07192016
						var name_last_temp_value = document.getElementById("name_last_data").value;
						var name_last_data_value=document.getElementById("name_last").value;
						var name_first_temp_value = document.getElementById("name_first_data").value;
						var name_first_data_value=document.getElementById("name_first").value;
						var sex_data = document.aufnahmeform.sex.value;
						var sex_temp = document.getElementById("sex_data").value;
						var DOB_data = document.getElementById("date_text").value;
						var DOB_temp = document.getElementById("birth_data").value;
						var name_middle_temp_value = document.getElementById("name_middle_data").value;
						var name_middle_data_value=document.getElementById("name_middle").value;
						var pid_temp = document.getElementById("pid_data").value;
						var status_data_value = document.aufnahmeform.civil_status.value;
						var status_temp_value = document.getElementById("status_data").value;
						var mun_nr = document.getElementById('mun_nr').value;
						var street_name = document.aufnahmeform.street_name.value;
						var zip_code = document.getElementById('zipcode').value;
						var brgy_nr = document.getElementById('brgy_nr').value;
						// added by Christian 06-08-2020
						var firstNameOnly='',suffixOnly='',commaLoc='',fNameLength='';
						commaLoc = name_first_data_value.indexOf(",");
						fNameLength = name_first_data_value.length;

						if(commaLoc != "-1"){
							firstNameOnly = name_first_data_value.substring(0,commaLoc);
							suffixOnly = name_first_data_value.substr(commaLoc+1, fNameLength).replace(/\s/g,'');
						}else{
							firstNameOnly = name_first_data_value;
						}
						// end Christian 06-08-2020
							if(document.aufnahmeform.sex.value == "f"){
							gender = "Female";
						}
						else
						{
							gender = "Male";
						}
							
							if (pid_temp == ""){
								document.aufnahmeform.submit();
							}
							else if(name_last_temp_value != name_last_data_value || name_middle_temp_value != name_middle_data_value 
																			|| name_first_temp_value != name_first_data_value 
																			|| sex_data != sex_temp
																			|| DOB_data != DOB_temp
																			|| status_data_value != status_temp_value){
							
								// alert(ins_nr);
        //      					alert(nrs_info);
        //      					alert(nrs);
        //      					alert(is_final);
								if(is_final!='1' && encount_nr==nrs && nrs_info !='' ){
									var src3 = "<div style='background:red;'><br><br><br><br><br><br><br><br><h1 style='color:#ffffff;font-size:37px;'><center>Patient has already submitted PHIC requirement</h1>"+
												"<center><h1 style='color:#ffffff;font-size:40px;'> Do you want to continue?  </h1></center>"+
												"</div>";
									$J(src3).dialog({
											closeOnEscape: false,
					                        autoOpen: true,
					                        modal:true,
					                        height: "550",
					                        width: "70%",
					                        resizable: false,
					                        show: "fade",
					                        hide: "explode",
					                        title: "Notice:",
					                        position: "top", 
					                        buttons: {
					                            "OK": function() 
					                            { 
					                              getPHICrequirement();      	
					                              $J( this ).dialog( "destroy" );
					                            } ,
								                  "Cancel": function () {
								                 $J('#documents').attr('checked',false);
								                    $J(this).dialog("destroy");
								                    }
					                        		  }
					                    });
									
								}
								else{

									insertEmptyAudit();
									alert("Update Success");
									document.aufnahmeform.submit();
								}
								
								xajax_updateProfileEncounter(pid_temp,encount_nr,selectedVal,selectdatebirth,mun_nr,street_name,zip_code,brgy_nr,sex_data);
								xajax_updatePhicInfo(pid_temp, encount_nr,name_last_data_value,firstNameOnly,name_middle_data_value,suffixOnly,sex_data,DOB_data); //Added by Christian 06-08-20
							}
							else{
								xajax_updateProfileEncounter(pid_temp,encount_nr,selectedVal,selectdatebirth,mun_nr,street_name,zip_code,brgy_nr,sex_data);
								alert("Update Success");
							document.aufnahmeform.submit();
							//ended by mats
						}
						
					}


					
					else{
						//$('saveButton').disabled = false;
						$(namespan).innerHTML = '<a href="javascript:void(0);"><img id="'+namebutton+'" name="'+namebutton+'" src="'+nameimage+'" border=0  title="<?=$LDSaveData?>"  align="absmiddle" onclick="gotoCheck();"></a>';
					}
				}//else
					//$(namespan).innerHTML = '<a href="javascript:void(0);"><img id="'+namebutton+'" name="'+namebutton+'" src="'+nameimage+'" border=0  title="<?=$LDSaveData?>"  align="absmiddle" onclick="gotoCheck();"></a>';
					
		 }

		function insertEmptyAudit(){
    	var is_pid = '<?=$pid?>';
    	var encoder='<?=$_SESSION[sess_login_username]?>';                           
		xajax_addauditPHIC(is_pid,"",encoder);        
    }
			// Added by Gervie
		 function chkValidDate(){
		 	//edited by aiza
		 			
			var current_date = '<?= date("m/d/Y H:i:s")?>';
			var current_time = '<?= date("h:i")?>';

			var reg_dt = $('reg_date').value;
			var reg_tm = $('reg_time').value;
			var reg_meridian = $('selAMPM2').value;
		
			var reg_time_text_hr = reg_tm.substr(0, 2);
			var reg_time_text_min = reg_tm.substr(3);

			if (reg_meridian == 'PM') {
				if (reg_time_text_hr != '12')
					reg_time_text_hr = parseInt(reg_time_text_hr) + 12;

			} else if (reg_meridian == 'AM') {
				if (reg_time_text_hr == '12')
					reg_time_text_hr = '00';
			}

			reg_tm = reg_time_text_hr + ":" + reg_time_text_min + ":00";
		
			var r_dt;
			r_dt = reg_dt + " " + reg_tm;


			if(isValidDate2($('reg_date').value)) {
		 		if (r_dt > current_date){
					alert('Registration Date/Time must be earlier or the same with the current date/time.');
			 		$('reg_date').value = '<?= date("m/d/Y")?>';
			 		$('reg_time').value = current_time;

				}

		 	} else {
		 		alert('Invalid date entered.');
		 		$('reg_date').value = '<?= date("m/d/Y")?>';
		 		$('reg_time').value = current_time;
		 	}

		 	console.log();
		 	document.getElementById('selAMPM2').style.visibility = 'visible';
		 }

		function isValidDate2(s) {
			var bits = s.split('/');
		  	var y = bits[2], d  = bits[1], m = bits[0];
		  	// Assume not leap year by default (note zero index for Jan)
		  	var daysInMonth = [31,28,31,30,31,30,31,31,30,31,30,31];

		  	// If evenly divisible by 4 and not evenly divisible by 100,
		  	// or is evenly divisible by 400, then a leap year
		  	if ( (!(y % 4) && y % 100) || !(y % 400)) {
		    	daysInMonth[1] = 29;
		  	}
		  	return d <= daysInMonth[--m]
		}

	//save
	var ptype = '<?=$ptype?>';
	//When F2 is Pressed
	shortcut("F2",
		function(){
				gotoCheck();
				
		}
	);

<?php
		require($root_path.'include/inc_checkdate_lang.php');
?>
		-->
		</script>
		<script language="javascript" src="<?php echo $root_path; ?>js/setdatetime.js"></script>
		<script language="javascript" src="<?php echo $root_path; ?>js/checkdate.js"></script>
		<script language="javascript" src="<?php echo $root_path; ?>js/dtpick_care2x.js"></script>
<?php

		echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">';
		echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>';
		echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>';
		echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>';
			# burn added : March 1, 2007
//		echo '<script type="text/javascript" language="javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>';
#	require("address.common.php");
	require($root_path.'modules/registration_admission/address.common.php');
	if ($xajax) {
		$xajax->printJavascript('../../classes/xajax');
	}

?>

<script language="javascript">
<!--

	function AddressWizard() {
		return overlib(
			OLiframeContent('<?= $root_path?>/modules/registration_admission/seg-address-select.php', 600, 410, 'fOrderTray', 0, 'auto'),
				WIDTH,600, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
				CAPTIONPADDING,2,
				CAPTION,'Address Wizard',
				MIDX,0, MIDY,0,
				STATUS,'Run address wizard');
	}


		/*
				This will trim the string i.e. no whitespaces in the
				beginning and end of a string AND only a single
				whitespace appears in between tokens/words
				input: object
				output: object (string) value is trimmed
		*/
	function trimString(objct){
		objct.value = objct.value.replace(/^\s+|\s+$/g,"");
		objct.value = objct.value.replace(/\s+/g," ");
	}/* end of function trimString */

	function blocking(objectName, flag){
		if (document.layers) {
			document.layers[objectName].display = (flag) ? '' : 'none';
		} else if (document.all) {
			document.all[objectName].style.display = (flag) ? '' : 'none';
		} else if (document.getElementById) {
			document.getElementById(objectName).style.display = (flag) ? '' : 'none';
		}
	}/* end of function blocking*/

	function getElementsByClass(searchClass,node,tag) {
		var classElements = new Array();
		if ( node == null )
			node = document;
		if ( tag == null )
			tag = '*';
		var els = node.getElementsByTagName(tag);
		var elsLen = els.length;
		var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
		for (i = 0, j = 0; i < elsLen; i++) {
			if ( pattern.test(els[i].className) ) {
				classElements[j] = els[i];
				j++;
			}
		}
		return classElements;
	}

	//-----------added by VAN 09-04-07---------------
	function DisableInsurance(){
		var d = document.aufnahmeform;
		var rowSrc;
		var list = document.getElementById('order-list');
		var dBody=list.getElementsByTagName("tbody")[0];

		if(d.insurance_class_nr[2].checked==true){
			document.getElementById('iconIns').style.display = 'none';
			rowSrc = '<tr><td colspan="10" style="">No such insurance firm exists...</td></tr>'
			dBody.innerHTML = rowSrc;
		}else{
			document.getElementById('iconIns').style.display = '';
			rowSrc = " ";
			<?php
				$result = $pinsure_obj->getPersonInsuranceItems($pid);
				$rows=array();
				if (is_object($result)){
				while ($row=$result->FetchRow()) {
					$rows[] = $row;
				}

				$sql2 = "SELECT * FROM care_encounter WHERE pid ='".$pid."'";
					$from_encounter=$db->Execute($sql2);
					$row_from_encounter=$from_encounter->RecordCount();
					if ($row_from_encounter!=0){
						while($rsObj=$from_encounter->FetchRow()) {
							$enc_cr = $rsObj["encounter_nr"];
							$sql3 = "SELECT * from seg_encounter_insurance WHERE encounter_nr='".$rsObj["encounter_nr"]."'";
							$from_seg_insurance=$db->Execute($sql3);
							$row_from_seg_insurance=$from_seg_insurance->RecordCount();

							if ($row_from_seg_insurance!=0){
								while ($row=$from_seg_insurance->FetchRow()) {
									$from_seg .= $row["hcare_id"].",";
								}
							}
						}
					}
				}

				$from_seg = substr($from_seg, 0, strlen($from_seg)-1);
				$from_seg_list = explode(",",$from_seg);

				foreach ($rows as $i=>$row) {
					if ($row) {

					$count++;
					$alt = ($count%2)+1;

						if (in_array($row['hcare_id'], $from_seg_list)){
			?>
							rowSrc +='<tr class="wardlistrow<?= $alt; ?>" id="row<?= $row['hcare_id'];?>">' +
									'<input type="hidden" name="items[]" id="rowID<?=$row['hcare_id'];?>" value="<?=$row['hcare_id'];?>" />'+
									'<input type="hidden" name="nr[]" id="rowNr<?=$row['hcare_id'];?>" value="<?=$row['insurance_nr'];?>" />'+
									'<input type="hidden" name="is_principal[]" id="rowis_principal<?=$row['hcare_id'];?>" value="<?=$row['is_principal'];?>" />'+
									'<td class="centerAlign"><img src="../../images/insurance.gif" border="0"/>&nbsp;</td>'+
									'<td width="*" id="name<?= $row['hcare_id'];?>"><?= $row['firm_id'];?></td>'+
									'<td width="25%" align="right" id="inspin<?= $row['hcare_id'];?>"><?= $row['insurance_nr']; ?></td>'+
									'<td width="18%" class="centerAlign" id="insprincipal<?= $row['hcare_id'];?>"><?= (($row["is_principal"])?'YES':'NO'); ?></td>'+
									'<td></td>'+
							'</tr>';
			<?php 	}else{
			?>
							rowSrc +='<tr class="wardlistrow<?= $alt; ?>" id="row<?= $row['hcare_id'];?>">' +
									'<input type="hidden" name="items[]" id="rowID<?=$row['hcare_id'];?>" value="<?=$row['hcare_id'];?>" />'+
									'<input type="hidden" name="nr[]" id="rowNr<?=$row['hcare_id'];?>" value="<?=$row['insurance_nr'];?>" />'+
									'<input type="hidden" name="is_principal[]" id="rowis_principal<?=$row['hcare_id'];?>" value="<?=$row['is_principal'];?>" />'+
									'<td class="centerAlign"><a href="javascript:removeItem(\'<?= $row['hcare_id'];?>\')"><img src="../../images/btn_delitem.gif" border="0"/></a></td>'+
									'<td width="*" id="name<?= $row['hcare_id'];?>"><?= $row['firm_id'];?></td>'+
									'<td width="25%" align="right" id="inspin<?= $row['hcare_id'];?>"><?= $row['insurance_nr']; ?></td>'+
									'<td width="18%" class="centerAlign" id="insprincipal<?= $row['hcare_id'];?>"><?= (($row["is_principal"])?'YES':'NO'); ?></td>'+
									'<td></td>'+
							'</tr>';
			<?php
						}
					}
				}
			?>
			if (rowSrc==" "){
				rowSrc = '<tr><td colspan="10" style="">No such insurance firm exists...</td></tr>'
			}
			dBody.innerHTML = rowSrc;
		}
	}
	//---------------------------------------------

	function jsEnableAddresses(en) {
		var enable = en=="1";
		$('region_nr').style.visibility = enable ? "visible" : "hidden";
		$('prov_nr').style.visibility = enable ? "visible" : "hidden";
		$('mun_nr').style.visibility = enable ? "visible" : "hidden";
		$('zipcode').style.visibility = enable ? "visible" : "hidden";
		$('brgy_nr').style.visibility = enable ? "visible" : "hidden";

		$('region_nr').disabled = !enable;
		$('prov_nr').disabled = !enable;
		$('mun_nr').disabled = !enable;
		$('zipcode').disabled = !enable;
		$('brgy_nr').disabled = !enable;
	}

	function ajxClearAddress(objName) {
		var optionsList;
		var el=$(objName);
		if (el) {
			optionsList = el.getElementsByTagName('OPTION');
			for (var i=optionsList.length-1;i>=0;i--) {
				optionsList[i].parentNode.removeChild(optionsList[i]);
			}
		}
	}/* end of function ajxClearAddress */

	function ajxAddAddress(objName, text, value) {
		var grpEl = $(objName);
		if (grpEl) {
			var opt = new Option( text, value );
			if (value == "NULL") opt.style.color = "#0000c0";
			grpEl.appendChild(opt);
		}
	}/* end of function ajxAddAddress */
		/*
				Resets the province's name, municipality/city's and
				barangay's default name and zip code after selecting a region.
				input: region's ID
		*/
	function setByRegion(regionID) {
		$('region_nr').value = regionID;
		$('prov_nr').value = -1;
		$('mun_nr').value = -1;
		$('zipcode').value = -1;
		$('brgy_nr').value = -1;
	}

	function jsSetRegion() {
		var aRegion=$('region_nr');

		var aRegionID = aRegion.options[aRegion.selectedIndex].value;
		if (aRegionID==-1){
			/*
			xajax_setAll('province'); // resets the list of provinces
			xajax_setAll('municity'); // resets the list of municipalities/cities
			xajax_setAll('zipcode'); // resets the list of zipcodes
			xajax_setAll('barangay'); // resets the list of barangays
			*/

			$('brgy_nr').disabled = true;
			ajxClearAddress('brgy_nr');
			ajxAddAddress('brgy_nr', '-No Baranggay Available-', -1);

			$('mun_nr').disabled = true;
			ajxClearAddress('mun_nr');
			ajxAddAddress('mun_nr', '-No Municipality/City Available-', -1);

			$('zipcode').disabled = true;
			ajxClearAddress('zipcode');
			ajxAddAddress('zipcode', '-No Zipcode Available-', -1);

			$('prov_nr').disabled = true;
			ajxClearAddress('prov_nr');
			ajxAddAddress('prov_nr', '-No Province Available-', -1);

		} else {
			$('region_nr').disabled = true;
			$('prov_nr').style.visibility = "hidden";
			$('mun_nr').style.visibility = "hidden";
			$('zipcode').style.visibility = "hidden";
			$('brgy_nr').style.visibility = "hidden";
			xajax_setRegion(aRegionID);
		}
	}
		/*
				Sets the region's name, province's name; and
				resets barangay's and municipality/city's default name
				after selecting a province.
				input: region's ID, province's ID
		*/
	function setByProvince(regionID, provID) {
		$('region_nr').value = regionID;
		$('prov_nr').value = provID;
		$('mun_nr').value = -1;
		$('zipcode').value = -1;
		$('brgy_nr').value = -1;
	}

	function jsSetProvince() {
		var aRegion=$('region_nr');
		var aRegionID = aRegion.options[aRegion.selectedIndex].value;
		var aProvince=$('prov_nr');
		var aProvinceID = aProvince.options[aProvince.selectedIndex].value;

		if (aProvinceID==-1) {
			/*
			xajax_setAll('municity',aRegionID); // resets the list of municipalities/cities
			xajax_setAll('zipcode',aRegionID);  // resets the list of zipcodes
			xajax_setAll('barangay',aRegionID); // resets the list of barangays
			*/
			$('brgy_nr').disabled = true;
			ajxClearAddress('brgy_nr');
			ajxAddAddress('brgy_nr', '-No Baranggay Available-', -1);

			$('mun_nr').disabled = true;
			ajxClearAddress('mun_nr');
			ajxAddAddress('mun_nr', '-No Municipality/City Available-', -1);

			$('zipcode').disabled = true;
			ajxClearAddress('zipcode');
			ajxAddAddress('zipcode', '-No Zipcode Available-', -1);

		} else {
			$('region_nr').disabled = true;
			$('prov_nr').disabled = true;

			$('mun_nr').style.visibility = "hidden";
			$('zipcode').style.visibility = "hidden";
			$('brgy_nr').style.visibility = "hidden";
			xajax_setProvince(aProvinceID);
		}
	}
		/*
				Sets the region's name, province's name, municipality/city's name,
				zipcode; and resets barangay's default name after selecting a municipality/city.
				input: region's ID, province's ID, zipcode
		*/
	function setByMuniCity(regionID, provID, munID, zipcode) {
		$('region_nr').value = regionID;
		$('prov_nr').value = provID;
		$('mun_nr').value = munID;
		$('zipcode').value = zipcode;
		$('brgy_nr').value = 'NULL';
	}

	function jsSetMuniCity() {
		var aRegion=$('region_nr');
		var aRegionID = aRegion.options[aRegion.selectedIndex].value;
		var aProvince=$('prov_nr');
		var aProvinceID = aProvince.options[aProvince.selectedIndex].value;
		var aMuniCity=$('mun_nr');
		var aMuniCityID = aMuniCity.options[aMuniCity.selectedIndex].value;

		if (aMuniCityID==-1){
			/*
			xajax_setAll('municity',-1,aProvinceID); // resets the list of municipalities/cities
			xajax_setAll('zipcode',-1,aProvinceID); // resets the list of zipcodes
			xajax_setAll('barangay',-1,aProvinceID); // resets the list of barangays
			*/
			$('zipcode').value = -1;

			$('brgy_nr').disabled = true;
			ajxClearAddress('brgy_nr');
			ajxAddAddress('brgy_nr', '-No Baranggay Available-', -1);
		} else {
			$('region_nr').disabled = true;
			$('prov_nr').disabled = true;
			$('mun_nr').disabled = true;
			$('zipcode').disabled = true;
			$('brgy_nr').style.visibility = "hidden";
			xajax_setMuniCity(aMuniCityID);
		}
	}

		/*
				Sets the region's name, province's name, municipality/city's name;
				and resets barangay's default name after selecting a zipcode.
				input: region's ID, province's ID, municipality/city ID
		*/
	function setByZipcode(regionID, provID, munID, zipcode) {
		$('region_nr').value = regionID;
		$('prov_nr').value = provID;
		$('mun_nr').value = munID;
		$('zipcode').value = zipcode;
		$('brgy_nr').value = 0;
	}

	function jsSetZipcode() {
		var aRegion=$('region_nr');
		var aRegionID = aRegion.options[aRegion.selectedIndex].value;
		var aProvince=$('prov_nr');
		var aProvinceID = aProvince.options[aProvince.selectedIndex].value;
		var aZipcode=$('zipcode');
		var aZipcodeID = aZipcode.options[aZipcode.selectedIndex].value;

		if (aZipcodeID==-1){
			/*
			xajax_setAll('municity',-1,aProvinceID); // resets the list of municipalities/cities
			xajax_setAll('zipcode',-1,aProvinceID); // resets the list of zipcodes
			xajax_setAll('barangay',-1,aProvinceID); // resets the list of barangays
			*/

			$('mun_nr').value = -1;
			$('brgy_nr').disabled = true;
			ajxClearAddress('brgy_nr');
			ajxAddAddress('brgy_nr', '-No Baranggay Available-', -1);
		} else {
			$('region_nr').disabled = true;
			$('prov_nr').disabled = true;
			$('mun_nr').disabled = true;
			$('zipcode').disabled = true;

			$('brgy_nr').style.visibility = "hidden";
			xajax_setZipcode(aZipcodeID);
		}
	}
		/*
				This will set the region's name, province's name, municipality/city's name,
				zipcode, and barangay's name after selecting a barangay.
				input: region's ID, province's ID, municipality/city ID, zipcode, brgyID
		*/
	function setByBarangay(regionID, provID, munID, zipcode, brgyID) {
		$('region_nr').value = regionID;
		$('prov_nr').value = provID;
		$('mun_nr').value = munID;
		$('zipcode').value = zipcode;
		$('brgy_nr').value = brgyID;
	}

	function jsSetBarangay() {
		/*
		var aRegion=$('region_nr');
		var aRegionID = aRegion.options[aRegion.selectedIndex].value;
		var aProvince=$('prov_nr');
		var aProvinceID = aProvince.options[aProvince.selectedIndex].value;
		var aMuniCity=$('mun_nr');
		var aMuniCityID = aMuniCity.options[aMuniCity.selectedIndex].value;
		var aBrgy=$('brgy_nr');
		var aBrgyID = aBrgy.options[aBrgy.selectedIndex].value;


		$('region_nr').disabled = true;
		$('prov_nr').disabled = true;
		$('mun_nr').disabled = true;
		$('zipcode').disabled = true;
		$('brgy_nr').disabled = true;

		if (aBrgyID==-1){
			xajax_setAll('barangay',-1,-1,aMuniCityID); // resets the list of barangays
		}else{
			xajax_setBarangay(aBrgyID);
		}
		*/
	}

	//added by VAN 01-21-09
	var js_time = "";
	function js_setTime(jstime){
		js_time = jstime;
	}

	function js_getTime(){
		return js_time;
	}

	function validateTime(S) {
		return /^([01]?[0-9])(:[0-5][0-9])?$/.test(S);
	}

	var seg_validDate=true;
	//var seg_validTime=false;

	function seg_setValidDate(bol){
		seg_validDate=bol;
	//	alert("seg_setValidDate : seg_validDate ='"+seg_validDate+"'");
	}

	var seg_validTime=false;
	function setFormatTime(thisTime,AMPM){
	//	var time = $('time_text_d');
	//alert(AMPM);
		var stime = thisTime.value;
		var hour, minute;
		var ftime ="";
		var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
		var f2 = /^[0-9]\:[0-5][0-9]$/;
		var jtime = "";

		trimString(thisTime);

		if (thisTime.value==''){
			seg_validTime=false;
			return;
		}

		stime = stime.replace(':', '');

		if (stime.length == 3){
			hour = stime.substring(0,1);
			minute = stime.substring(1,3);
		} else if (stime.length == 4){
			hour = stime.substring(0,2);
			minute = stime.substring(2,4);
		}else{
			alert("Invalid time format.");
			thisTime.value = "";
			seg_validTime=false;
			thisTime.focus();
			return;
		}

		jtime = hour + ":" + minute;
		js_setTime(jtime);

		if (hour==0){
			 hour = 12;
			 document.getElementById(AMPM).value = "AM";
		}else	if((hour > 12)&&(hour < 24)){
			 hour -= 12;
			 document.getElementById(AMPM).value = "PM";
		}

		ftime =  hour + ":" + minute;

        if (ftime.length==4)
            ftime = '0'+ftime;
                
		if(!ftime.match(f1) && !ftime.match(f2)){
			thisTime.value = "";
			alert("Invalid time format.");
			seg_validTime=false;
			thisTime.focus();
		}else{
			thisTime.value = ftime;
			seg_validTime=true;
		}
	}// end of function setFormatTime

		//added by VAN 06-17-09
		function setPhoto(objct){
			var imgName = objct.value;
			var dotElement = imgName.lastIndexOf(".");
			var stringLength = imgName.length;
			var imgFormat = imgName.substring(dotElement+1, stringLength);
			imgFormat = imgFormat.toLowerCase();
			if(!(imgFormat == "jpg" || imgFormat == "jpeg"))
				alert("The specific image could not be uploaded.\nOnly images with the following format are allowed: jpeg and jpg");
			$('photo1').value = objct.value;
		}/* end of function setPhoto */
		//-------------------------

	//-------------------------

	//added by VAN 02-22-08
	function EnableButton(val){
		var userid = '<?php echo $this->segUserDept; ?>';
		var allow_er_user = '<?php echo $allow_er_user; ?>';
		var allow_medocs_user = '<?php echo $allow_medocs_user; ?>';
		var allow_newborn_register = '<?php echo $allow_newborn_register; ?>';
		var ptype = '<?php echo $ptype?>';
		var update = '<?php echo $_GET["update"]; ?>';

		// for ER only & new born baby
		if ((allow_er_user==1)&&(ptype=='er')){

		if (val==1){
				// Temporary
				//$('submit').style.display = '';
				$('saveButton').style.display = '';
				//$('ERsubmit').style.display = 'none';

				//added by VAN 07-02-08
				//document.getElementById('ERdept').style.value = 'none';
		}else{

				// Permanent
				//$('submit').style.display = 'none';
				$('saveButton').style.display = 'none';
				//$('ERsubmit').style.display = '';

				//added by VAN 07-02-08
				//document.getElementById('ERdept').style.value = '';
			}
		}
	}        

<?php

		if ($row_mun = $objInfo->getAllHospitalInfo())
			$default_mun_nr = $row_mun['default_city'];
		else
			#Davao City
			$default_mun_nr = 24;

		if ($brgy_nr && $brgy_nr!=='NULL'){
			global $theBarangay;
			$theBarangay = $brgy_nr;
?>
			xajax_setBarangay(<?= $brgy_nr ?>);
<?php
		} else {
			global $theMunicity;

			if (!$mun_nr) $mun_nr = $default_mun_nr;
			$theMunicity = $mun_nr;
?>
			xajax_setMuniCity(<?= $mun_nr ?>); // sets Davao City as default city
<?php
		}
?>

// -->
</script>
<?php
		$sTemp = ob_get_contents();
		ob_end_clean();

		$this->smarty->assign('sRegFormJavaScript',$sTemp);

		$this->smarty->assign('thisfile',$thisfile);

		if($error) {
			$this->smarty->assign('error',TRUE);
			$this->smarty->assign('sErrorImg','<img '.createMascot($root_path,'mascot1_r.gif','0','bottom').' align="absmiddle">');
			if ($error>1) $this->smarty->assign('sErrorText',$LDErrorS);
				else $this->smarty->assign('sErrorText',$LDError);

		}elseif($error_person_exists){

			$this->smarty->assign('errorDupPerson',TRUE);
			$this->smarty->assign('sErrorImg','<img '.createMascot($root_path,'mascot1_r.gif','0','bottom').' align="absmiddle">');
			$this->smarty->assign('LDPersonDuplicate',$LDPersonDuplicate);
			if($duperson->RecordCount()>1) $this->smarty->assign('sErrorText',"$LDSimilarData2 $LDPlsCheckFirst2");
				else $this->smarty->assign('sErrorText',"$LDSimilarData $LDPlsCheckFirst");

	$mode_orig = $mode;
	$mode = 'search';

 #
 # Create the search object
 #
 require_once($root_path.'include/care_api_classes/class_gui_duplicate_person.php');
 $psearch = new GuiDuplicatePerson;

# sets the type of search (person or personnel)
# burn added: March 16, 2007
$psearch->setSearchType("person");

$psearch->setTargetFile('patient_register_show.php');

if ($_GET['dup_mode']=='paginate'){
    $temp__SESSION = $HTTP_SESSION_VARS['seg_post'];
    extract($temp__SESSION);
}else{
	$HTTP_SESSION_VARS['seg_post'] = $HTTP_POST_VARS;
	$duplicate_sql = $HTTP_POST_VARS['name_last'].", ".$HTTP_POST_VARS['name_first'];
}

if (empty($HTTP_POST_VARS['name_first'])&&empty($name_first))
	$HTTP_POST_VARS['firstname_too'] = FALSE;
else
	$HTTP_POST_VARS['firstname_too'] = TRUE;

$temp = $psearch->create($duplicate_sql);

$this->smarty->assign('segDupDataRows',$temp);

$this->smarty->assign('thisfile',$seg_thisfile);

$mode = $mode_orig;

#added by VAN 08-08-08
$this->mod = $mode;

$_GET=NULL;


		}# end of " }elseif($error_person_exists){ "

		if ($_GET['pid'])
			$pid = $_GET['pid'];
		if (empty($photo_filename))
			$photo_filename = $photo_data;

		$permission_val = explode(" ",$HTTP_SESSION_VARS['sess_permission']);
		$manual_hrn = false;
		/*foreach($permission_val as $val){
			echo "value: ".$val."<br/>";
		}*/
		if(in_array("_a_1_ipdpatientmanualhrn", $permission_val)){
			$manual_hrn = true;
		}

		if($manual_hrn){
			$valpid = "<input type='text' id='pid' name='pid' value='".$pid."'/>";
			$LDRegistryNr = "PID";
			if($pid) $this->smarty->assign('LDRegistryNr',$LDRegistryNr);
			$this->smarty->assign('pid',$valpid);
		}else{
			if($pid) $this->smarty->assign('LDRegistryNr',$LDRegistryNr);
			$this->smarty->assign('pid',$pid);
		}
	
		$this->smarty->assign('img_source',$img_source);
		$this->smarty->assign('fpimg_source',$fpimg_source);
		$this->smarty->assign('LDPhoto',$LDPhoto);
		if(isset($photo_filename)) $pfile= $photo_filename;
			else $pfile='';

		$fpfile = (isset($fpimage_filename)) ? $fpimage_filename : '';
// -------------- modified by Bong - 5/10/2007 -----------------------------------
//		$this->smarty->assign('sFileBrowserInput','<input name="photo_filename" type="file" size="15"   onChange="showpic(this)" value="'.$pfile.'">');

		$this->smarty->assign('sFileBrowserInput','<input name="photo_filename" id="photo_filename" type="hidden" value="" /><input name="photo_data" id="photo_data" type="hidden" value="'.trim($photo_filename).'" /><button class="button button-large" onClick="takepic()"><i class="fa fa-camera"></i> Take a picture!</button><br><b>-or-</b>');
		$this->smarty->assign('sFPImageEnrollment','<input name="fpimage_filename" type="hidden" size="25" onChange="showFPImage(this)" value="'.$fpfile.'"><input class="jedButton" style="cursor:pointer" name="fp_enroll" type="button" size="18" onClick="enrollFP()" value="Take Fingerprint!">');
// ------------------------------------------------------------------------------                
		#added by Christian                     
		if (!isset($target) || $target != 'personell_reg') {		
		$this->smarty->assign('sFingerPrintDisplay',
						'<div id="fingerPrintDisplay">
							<img class="frame" src="../../modules/registration_admission/image/fingerprint/frame.png">
							<img class="handsOutline" src="../../modules/registration_admission/image/fingerprint/hands.png" onload="getRegisteredFingerprint('.$pid.')" width="157" height="80">
						</div><br><br><br><br><br><br><br><br><br><br><br><br><br>');           
// -------------- modified by Bong for fingerprint biometric- 08/28/2019 -----------------------------------
		require_once($root_path.'include/care_api_classes/biometric/class_person_fingerprint.php');
		$hasRecord = PersonFingerprint::getPersonFingerprintOnly($pid);
		$isRegistered = $hasRecord ? 'Update fingerprint':'Register fingerprint';
		$this->smarty->assign('sFingerPrintReg',
                        '<div>
                            <button class="favorite styled fpbutton" type="button" onclick="launchFPReg(\''.$cClientId.'\');" value="Register" name="Register"><span class="iconunknown">&nbsp;</span><span class="text">'.$isRegistered.'</span></button>
                        </div>
                        <br>
                        <div id="fingerprint-window">
                            <div style="display:none" id="fingerprint"></div>
                            <div id="clientid"></div>
                            <textarea id="fptemplate" name="fptemplate" style="display:none">'.(isset($_POST["fptemplate"]) ? $_POST["fptemplate"] : '').'</textarea>
                            <input type="hidden" name="ptntId" id="ptnt" value="'.$pid.'" />
                            <input type="hidden" name="clientId" id="clientId" value="'.$cClientId.'" />
                        </div>');
		}
		else {
			$this->smarty->assign('sFingerPrintDisplay', '');
			$this->smarty->assign('sFingerPrintReg', '');
		}											
// -------------- modified by Bong - 08/28/2019 -----------------------------------           

		#added by VAN 06-17-09
		$this->smarty->assign('sFileBrowserUpload','<input type="file"  size="25" onChange="setPhoto(this)" name="photo" id="photo" value="'.trim($photo_filename).'"><input type="hidden" name="photo1" id="photo1" value="'.trim($photo_filename).'">');

		# iRowSpanCount counts the rows on the left of the photo image. Begin with 5 because there are 5 static rows.
		$iRowSpanCount = 5;

		$this->smarty->assign('LDRegDate',$LDRegDate);
		#commented by VAN 01-21-09
		#$this->smarty->assign('sRegDate',formatDate2Local($date_reg,$date_format).'<input name="date_reg" type="hidden" value="'.$date_reg.'">');

		//$iRowSpanCount++;
		$this->smarty->assign('LDRegTime',$LDRegTime);

		#added by VAN 01-21-09

		$phpfd=$date_format;
		$phpfd=str_replace("dd", "%d", strtolower($phpfd));
		$phpfd=str_replace("mm", "%m", strtolower($phpfd));
		$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

		if ($date_reg)
			$segRegDateTime = date("m/d/Y H:i:s",strtotime($date_reg));
		else{
			$segRegDateTime = date("m/d/Y H:i:s");

		}

		$registration_date = date("Y-m-d",strtotime($segRegDateTime));
		if ($registration_date=='0000-00-00'){
			$reg_date = "";
		}else{
			$reg_date = date("m/d/Y",strtotime($registration_date));

		}
		$this->smarty->assign('sRegDate','<input type="text" name="reg_date" size=10 maxlength=10 value="'.$reg_date.'" id="reg_date" onChange="if (IsValidDate(this,\''.$date_format.'\')){ seg_setValidDate(true); chkValidDate();}else{ seg_setValidDate(false); }" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')" />');

		$TP_href_date="javascript:show_calendar('aufnahmeform.date','".$date_format."')";
		$dfbuffer="LD_".strtr($date_format,".-/","phs");
		$TP_date_format=$$dfbuffer;

		$jsCalScript = "<script type=\"text/javascript\">
							Calendar.setup ({
								inputField : \"reg_date\", ifFormat : \"$phpfd\", showsTime : false, button : \"reg_date_trigger\", singleClick : true, step : 1
							});
						</script>
						";
		$this->smarty->assign('jsCalendarSetup', $jsCalScript);
		$this->smarty->assign('sDateMiniCalendar','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="reg_date_trigger" align="absmiddle" style="cursor:pointer"> <font size=1>['.$TP_date_format.']</font>');

		$meridian = date("A",strtotime($segRegDateTime));
		if ($meridian=='PM'){
			$selected1 = "";
			$selected2 = "selected";
		}else{
			$selected1 = "selected";
			$selected2 = "";
		}

		$registration_time = date("H:i:s",strtotime($segRegDateTime));
		if ($registration_time=='00:00:00'){
			$reg_time = date("h:i s");
			$meridian = date("A");

			if ($meridian=='AM'){
				$selected1 = 'selected';
				$selected2 = '';
			}else{
				$selected1 = '';
				$selected2 = 'selected';
			}
			#echo "meridia - ".$meridian;
		}else{
			$reg_time = date("h:i",strtotime($registration_time));
		}

		$regtime = '<input type="text" id="reg_time" name="reg_time" size="4" maxlength="5" value="'.$reg_time.'" onChange="setFormatTime(this,\'selAMPM2\'); chkValidDate();" />
						<select id="selAMPM2" name="selAMPM2" onchange="chkValidDate()">
							<option value="AM" '.$selected1.'>A.M.</option>
							<option value="PM" '.$selected2.'>P.M.</option>
						</select>&nbsp;<font size=1>[hh:mm]</font>';

		$this->smarty->assign('sRegTime',$regtime);
		#----------------------------

		#if ((($allow_newborn_register)&&(($ptype=='newborn')||($ptype=='medocs')))||(($allow_newborn_register)||($fromtemp))){ #174
		if ($pid)
			$fromtemp = $dbfromtemp;
		#echo $fromtemp;
		#echo "<br>s =".$allow_newborn_register;
		if (($fromtemp)&&($allow_newborn_register)){
			$t_profile_type ="
					<tr>
						<td  class='reg_item'>
							Profile Type
						</td>
						<td class='reg_input'>
							<FONT color='#800000'>";

					$t_profile_type = $t_profile_type."
									<input name='profileType' id='profileType' type='radio' value='1' ".(($admitted_baby==1)? "checked":"")." onClick='EnableButton(this.value);'>Admitted Baby
									<input name='profileType' id='profileType' type='radio' value='0' ".(($admitted_baby==0)? "checked":"")." onClick='EnableButton(this.value);'>Well-baby
					";
			$t_profile_type = $t_profile_type."
						</td>
					</tr>
			";

			$this->smarty->assign('segProfileType',$t_profile_type);   # burn added: July 24, 2007
		}

		#added by VAN 02-20-08
		if (empty($profileType))
			$profileType = 0;
#echo "class_gui_input_person.php : pid = '".$pid."';   profileType = '".$profileType."' <br> \n";
		// Made hideable as suggested by Kurt brauchli
		if (!$GLOBAL_CONFIG['person_title_hide']){
			$this->smarty->assign('sPersonTitle',$this->createTR( $errortitle, 'title', $LDTitle, $title,'','', 14 ));
			$iRowSpanCount++;
		}

		$this->smarty->assign('sNameLast',$this->createTR($errornamelast, 'name_last', $LDLastName,$name_last,'','',35,TRUE));
		//$iRowSpanCount++;

		// added by carriane 08/09/18
		if($suffix)
			$name_first = str_replace(' '.$suffix, ', '.$suffix, $name_first);
		// end carriane

		$this->smarty->assign('sNameFirst',$this->createTR($errornamefirst, 'name_first', $LDFirstName,$name_first,'','',35,TRUE));
		//$iRowSpanCount++;

        #added by VAN
        $this->smarty->assign('sSuffix',$this->createTR($errorsuffix, 'suffix', 'Suffix',$suffix));

		if (!$GLOBAL_CONFIG['person_name_2_hide']){
			$this->smarty->assign('sName2',$this->createTR($errorname2, 'name_2', $LDName2,$name_2));
			$iRowSpanCount++;
		}

		if (!$GLOBAL_CONFIG['person_name_3_hide']){
			$this->smarty->assign('sName3',$this->createTR($errorname3, 'name_3', $LDName3,$name_3));
			$iRowSpanCount++;
		}

		if (!$GLOBAL_CONFIG['person_name_middle_hide']){
			$this->smarty->assign('sNameMiddle',$this->createTR($errornamemid, 'name_middle', $LDNameMid,$name_middle));
			$iRowSpanCount++;
		}

		if (!$GLOBAL_CONFIG['person_name_maiden_hide']){
			$this->smarty->assign('sNameMaiden',$this->createTR($errornamemaiden, 'name_maiden', $LDNameMaiden,$name_maiden));
			$iRowSpanCount++;
		}

		if (!$GLOBAL_CONFIG['person_name_others_hide']){
			$this->smarty->assign('sNameOthers',$this->createTR($errornameothers, 'name_others', $LDNameOthers,$name_others));
			$iRowSpanCount++;
		}

		# Set the rowspan value for the photo image <td>
		$this->smarty->assign('sPicTdRowSpan',"rowspan=$iRowSpanCount");


		#if ($errordatebirth) $this->smarty->assign('LDBday',"<font color=red>* $LDBday</font>");
		#	else $this->smarty->assign('LDBday',"<font color=red>* $LDBday</font>");
		#edited by VAN 04-30-08
		if ($errordatebirth) $this->smarty->assign('LDBday',"<font color=#ff0000>* $LDBday</font>");
			else $this->smarty->assign('LDBday',"<font color=#ff0000>* $LDBday</font>");

		$agewobday = $age;
		if($date_birth){
			if($mode=='save'||$error||$error_person_exists) $sBdayBuffer = $date_birth;
				else $sBdayBuffer = formatDate2Local($date_birth,$date_format);
			if (strstr($date_birth,'-'))
				$sBdayBuffer = formatDate2Local($date_birth,$date_format);
			# burn added : March 26, 2007
			if (!($age = $person_obj->getAge($sBdayBuffer))){
				#$age = '';
				$age = $agewobday;
				$sBdayBuffer = '';
			}
		}

		# Uncomment the following when the current date must be inserted
		# automatically at the start of each document

		$phpfd=$date_format;
		$phpfd=str_replace("dd", "%d", strtolower($phpfd));
		$phpfd=str_replace("mm", "%m", strtolower($phpfd));
		$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
	/*
	$sDateJS= 'onFocus="this.select();"
				id = "date_text"
				onBlur="getAge(this); checkCivilStat(); checkIfSenior();
				IsValidDate(this,\''.$date_format.'\'); "
				onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
				<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="date_trigger" style="cursor:pointer" >
				<font size=1>[';
	*/
	/*$sDateJS= 'onFocus="this.select();"
				id = "date_text"
				onBlur="getAge(); checkCivilStat(); checkIfSenior();
				IsValidDate(this,\''.$date_format.'\'); "
				onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
				<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="date_trigger" style="cursor:pointer" >
				[';
			ob_start();*/
            
    $sDateJS= 'onFocus="this.select();"
                id = "date_text"
                onBlur="getAge(); checkCivilStat(); checkIfSenior(); birthTimeFocus();"
                >
                <img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="date_trigger" style="cursor:pointer" >
                [';
			ob_start();
	?>


			<!--EDITED: SEGWORKS -->
			<script type="text/javascript">
			Calendar.setup ({
					//inputField : "date_text", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "date_trigger", singleClick : true, step : 1
					//edited by VAN 05-06-08
					inputField : "date_text", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "date_trigger", singleClick : true, onClose: function(cal) { cal.hide(); getAge(); checkCivilStat(); checkIfSenior(); birthTimeFocus(); }, step : 1

			});
			</script>
	<?
			$calendarSetup = ob_get_contents();
			ob_end_clean();

		$sDateJS .= $calendarSetup;
		/**/
		$dfbuffer="LD_".strtr($date_format,".-/","phs");
		$sDateJS = $sDateJS.$$dfbuffer.']';

		$this->smarty->assign('sBdayInput','<input name="date_birth" type="text" size="15" maxlength=10 value="'.$sBdayBuffer.'" '.$sDateJS);

        #added by VAN 06-06-2013
        #requested by Ma'am Lani for those patients esp. Bajao who don't know their birthday
        $this->smarty->assign('sTempBday','<input type="checkbox" name="is_temp_bdate" id="is_temp_bdate" value="1" '.(($is_temp_bdate)?'checked="checked" ':'').'>&nbsp;Temp Birthday**');
        
        #added by VAS 08-17-2012
        if ($errordatebirth) $this->smarty->assign('LDBirthTime',"<font color=#ff0000>* Birth TIme</font>");
            else $this->smarty->assign('LDBirthTime',"<font color=#ff0000>* Birth TIme</font>");
        
        if (($fromtemp)||($ptype=='newborn')){
            if (($birth_time!='')){
                $birthtime = date("h:i", strtotime($birth_time));
                
                $meridian = date("A", strtotime($birth_time));
                
                if ($meridian=='AM'){
                    $selected1_btime = 'selected';
                    $selected2_btime = '';
                }else{
                    $selected1_btime = '';
                    $selected2_btime = 'selected';
                }
            }else{
                $birthtime = '';
            }   
        }
        
        $btime = '';
        $btime = $btime.'<input value="'.$birthtime.'" type="text" id="birth_time" name="birth_time" size="4" maxlength="5" onChange="setFormatTime(this,\'birth_time_selAMPM\')" />&nbsp;';
        $btime = $btime.'<select id="birth_time_selAMPM" name="birth_time_selAMPM">
                            <option value="AM" '.$selected1_btime.'>A.M.</option>
                            <option value="PM" '.$selected2_btime.'>P.M.</option>';
        $btime = $btime.'</select>&nbsp;<font size=1>[hh:mm]</font>';
    
        $this->smarty->assign('sBirthTime',$btime );    
        #======================
        
        
		# burn added : March 23, 2007
		#$this->smarty->assign('segAge','<input name="age" id="age" type="text" size="3" maxlength=5 value="'.$age.'" onBlur="trimString(this); getBirthdate(this);"> year(s) old');
		#edited by VAN
		$this->smarty->assign('segAge','<input name="age" id="age" type="text" size="3" maxlength=5 value="'.$age.'" onBlur="trimString(this);checkCivilStat();checkIfSenior();"> year(s) old');
		#echo "senior_ID = ".$senior_ID;
		#added by VAN 05-07-08
		#$this->smarty->assign('LDSenior','Is senior citizen?');
		$this->smarty->assign('LDSenior','Senior Citizen ID No.');
		#$this->smarty->assign('sSenior','<input type="checkbox" name="isSenior" id="isSenior" value="1" >&nbsp;&nbsp;<span id="scID">ID No. :&nbsp;&nbsp;<input type="text" name="isSeniorID" id="isSeniorID" value="'.$isSeniorID.'"></span>');
		$this->smarty->assign('sSenior','<input type="text" name="senior_ID" id="senior_ID" value="'.$senior_ID.'">');

		$this->smarty->assign('LDVeterans','Is a veteran?');
		$this->smarty->assign('sVeterans','<input type="checkbox" name="isVeteran" id="isVeteran" value="1" onclick="checkIfVeteran();">&nbsp;&nbsp;<span id="vetID" style="display:none">ID No. :&nbsp;&nbsp;<input type="text" name="veteran_ID" id="veteran_ID" value="'.$isSeniorID.'" ></span>');
		#----------------------------

		# burn added : March 10, 2007
		# make place of birth hideable
		if (!$GLOBAL_CONFIG['person_place_birth_hide']){
			$this->smarty->assign('LDBirthplace',"$segBirthplace"); #CDE
			$this->smarty->assign('sBirthplace','<input name="place_birth" type="text" size="35" onBlur="trimString(this);" value="'.$place_birth.'" >');
		}

		if ($errorsex) $this->smarty->assign('LDSex', "<font color=#ff0000>* $LDSex</font>:");
			else $this->smarty->assign('LDSex', "<font color=#ff0000>* $LDSex</font>");

		$sSexMBuffer='<input name="sex" type="radio" value="m"  ';
		if($sex=="m") $sSexMBuffer.=' checked>';
			else $sSexMBuffer.='>';
		$this->smarty->assign('sSexM',$sSexMBuffer);
		$this->smarty->assign('LDMale',$LDMale);

		$sSexFBuffer ='<input name="sex" type="radio" value="f"  ';
		if($sex=="f") $sSexFBuffer.='checked>';
			else $sSexFBuffer.='>';
		$this->smarty->assign('sSexF',$sSexFBuffer);
		$this->smarty->assign('LDFemale',$LDFemale);

		# But patch 2004-03-10
		# Clean blood group
		$blood_group = trim($blood_group);

		//  Made hideable as suggested by Kurt Brauchli
		if (!$GLOBAL_CONFIG['person_bloodgroup_hide'] ){
			$this->smarty->assign('LDBloodGroup',$LDBloodGroup);
			$sBGBuffer='
				<input name="blood_group" type="radio" value="A" ';
			if($blood_group=='A') $sBGBuffer.='checked';
			$sBGBuffer.='>';
			$this->smarty->assign('sBGAInput',$sBGBuffer);
			$this->smarty->assign('LDA',$LDA);

			$sBGBuffer='
				<input name="blood_group" type="radio" value="B" ';
			if($blood_group=='B') $sBGBuffer.='checked';
			$sBGBuffer.='>';
			$this->smarty->assign('sBGBInput',$sBGBuffer);
			$this->smarty->assign('LDB',$LDB);

			$sBGBuffer='
				<input name="blood_group" type="radio" value="AB" ';
			if($blood_group=='AB') $sBGBuffer.='checked';
			$sBGBuffer.='>';
			$this->smarty->assign('sBGABInput',$sBGBuffer);
			$this->smarty->assign('LDAB',$LDAB);

			$sBGBuffer='
				<input name="blood_group" type="radio" value="O" ';
			if($blood_group=='O') $sBGBuffer.='checked';
			$sBGBuffer.='>';
			$this->smarty->assign('sBGOInput',$sBGBuffer);
			$this->smarty->assign('LDO',$LDO);
		}
		// KB: make civil status hideable
		if (!$GLOBAL_CONFIG['person_civilstatus_hide']){
			$this->smarty->assign('LDCivilStatus',$LDCivilStatus);
			$sCSInput='<input name="civil_status" type="radio" ';

			#added by VAN 04-26-2008
			$sCSBuffer = $sCSInput.'value="child" ';
			if($civil_status=="child") $sCSBuffer.='checked';
			$this->smarty->assign('sCSChildInput',$sCSBuffer.'>');
			#-----------------

			$sCSBuffer = $sCSInput.'value="single" ';
			if($civil_status=="single") $sCSBuffer.='checked';
			$this->smarty->assign('sCSSingleInput',$sCSBuffer.'>');

			$sCSBuffer = $sCSInput.'value="married" ';
			if($civil_status=="married") $sCSBuffer.='checked';
			$this->smarty->assign('sCSMarriedInput',$sCSBuffer.'>');


			$sCSBuffer = $sCSInput.'value="divorced" ';
			if($civil_status=="divorced") $sCSBuffer.='checked';
			$this->smarty->assign('sCSDivorcedInput',$sCSBuffer.'>');


			$sCSBuffer = $sCSInput.'value="widowed" ';
			if($civil_status=="widowed") $sCSBuffer.='checked';
			$this->smarty->assign('sCSWidowedInput',$sCSBuffer.'>');

			$sCSBuffer = $sCSInput.'value="separated" ';
			if($civil_status=="separated") $sCSBuffer.='checked';
			$this->smarty->assign('sCSSeparatedInput',$sCSBuffer.'>');

			// added by carriane 01/26/18
			$sCSBuffer = $sCSInput.'value="annulled" ';
			if($civil_status=="annulled") $sCSBuffer.='checked';
			$this->smarty->assign('sCSAnnulledInput',$sCSBuffer.'>');
			// end carriane

			#added by VAN 04-26-08
			$this->smarty->assign('LDChild',$LDChild);

			$this->smarty->assign('LDSingle',$LDSingle);
			$this->smarty->assign('LDMarried',$LDMarried);
			$this->smarty->assign('LDDivorced',$LDDivorced);
			$this->smarty->assign('LDWidowed',$LDWidowed);
			$this->smarty->assign('LDSeparated',$LDSeparated);
			$this->smarty->assign('LDAnnulled',$LDAnnulled); // added by carriane 01/26/18
		}

		$img_hidedetails=createComIcon($root_path,'hidedetails.gif','0');
		$img_showdetails=createComIcon($root_path,'showdetails.gif','0');

		$sArrows='<span id="arrow_up" onClick="jsShowDetails(false);" style="display:none; cursor:pointer"><img '.$img_hidedetails."></span> \n";
		$sArrows.='<span id="arrow_down" onClick="jsShowDetails(true);" style="display:\'\'; cursor:pointer"><img '.$img_showdetails."></span> \n";

		$this->smarty->assign('sArrows',$sArrows);   # burn added: March 14, 2007

		$sERDepartments='';
		#if (isset($this->segUserDept)&&($this->segUserDept==149)&&(!$update)){
		if (($allow_er_user)&&($ptype=='er')&&(!$update)){
		#edited by VAN 07-02-08
		#if (isset($this->segUserDept)&&(($this->segUserDept==149)||($this->segUserDept==151))&&(!$update)){
			include_once($root_path.'include/care_api_classes/class_department.php');
			$dept_obj = new Department;
			$dept_rs = $dept_obj->getAllOPDMedicalObject(1);   # get all the departments in IPD
			if ((empty($current_dept_nr))||(!($current_dept_nr)))
				$current_dept_nr = 133;
			$sERDepartments= $this->createTRselect($dept_rs,$errordept,"Admitting Department", 'current_dept_nr', $current_dept_nr, 'nr', 'name_formal','', 2,'',TRUE);
			#echo "type = ".$profileType;
			$this->smarty->assign('sERDepartments',$sERDepartments);   # burn added: March 14, 2007

			#added by VAN 12-16-08
			$list_category = $encounter_obj->getTriageCategory();
			#echo "ca r = ".$encounter_obj->sql;
			if (empty($category))
				$category = 3;

			#$category =  $roman_id." - ".$category;
			$sERCategory= $this->createTRselect($list_category,$errordept,"Triage Category", 'category',$category, 'category_id', 'category','', 2,'',TRUE);
			$this->smarty->assign('sERCategory',$sERCategory);
			#------------------------
		}

		# Retrieve record set of all occupations
		$occupation_obj = $person_obj->getOccupation();
		# burn added : March 10, 2007
		# make occupation hideable
		if (!$GLOBAL_CONFIG['person_occupation_hide']){
			if (empty($occupation))
				$occupation=1;   # default religion, 'Not Indicated'

			$this->smarty->assign('sOccupation',$this->createTRselect($occupation_obj,$erroroccupation,$LDOccupation, 'occupation', $occupation, 'occupation_nr', 'occupation_name','', 2));   # burn added: March 14, 2007
			#added by VAN 05-01-08
			#$this->smarty->assign('sEmployer',$this->createTR(FALSE, 'employer', $segEmployer,$employer,'personDetails',2));
		}

		#added by VAN 05-01-08
		if (!$GLOBAL_CONFIG['person_employer_hide']){
			$this->smarty->assign('sEmployer',$this->createTR(FALSE, 'employer', $segEmployer,$employer,'personDetails',2));
		}

		# burn added : March 10, 2007
		# make Family Background label hideable
		if ((!$GLOBAL_CONFIG['person_mother_name_hide']) && (!$GLOBAL_CONFIG['person_father_name_hide']) &&
			 (!$GLOBAL_CONFIG['person_spouse_name_hide']) && (!$GLOBAL_CONFIG['person_guardian_name_hide'])){
			$this->smarty->assign('sFamilyBackground',$segFamilyBackground);
		}

		# burn added : March 10, 2007
		# make hideable
		#added by VAN 05-19-08

		#edited by VAN 07-24-08
		$this->smarty->assign('segPersonInput',"true");
		#if (($allow_newborn_register)&&(($ptype=='newborn')||($ptype=='medocs'))){
		if (($fromtemp)&&($allow_newborn_register)){
			#$this->smarty->assign('segPersonInput',"true");
			$showBirth=1;
			#uncommented by VAN 07-04-08
			$sERDepartments = " ";
			$this->smarty->assign('sERDepartments',$sERDepartments);
		}else{
			$showBirth=0;
		}

		$this->smarty->assign('sIsNewborn',$showBirth);

		if (!$GLOBAL_CONFIG['person_mother_name_hide']){
			if ($sERDepartments){
				if ($showBirth){
					$this->smarty->assign('sMother','<font color=red>*'.$segMotherName.'</font>');
					$this->smarty->assign('sMother_fname','<input type="text" name="mother_fname" id="mother_fname" size="30" value="'.$mother_fname.'">');
					$this->smarty->assign('sMother_mdname','<input type="text" name="mother_maidenname" id="mother_maidenname" value="'.$mother_maidenname.'">');
					$this->smarty->assign('sMother_mname','<input type="text" name="mother_mname" id="mother_mname" value="'.$mother_mname.'">');
					$this->smarty->assign('sMother_lname','<input type="text" name="mother_lname" id="mother_lname" size="25" value="'.$mother_lname.'">');
					# added by: syboy 03/16/2016 : meow
					$this->smarty->assign('sMother_search','<input type="image" align="absmiddle" src="../../gui/img/control/default/en/en_searchlamp.gif" id="searchButton" name="searchButton" style="cursor: pointer;" onClick="searchMother();"');
					$this->smarty->assign('sMother_pid','<input type="text" name="mother_pid" id="mother_pid" size="25" value="'.$mother_pid.'">');
					# ended syboy
				}else{
					#$this->smarty->assign('sMother',$segMotherName);
					$this->smarty->assign('sMother',$this->createTR(FALSE, 'mother_fname', $segMotherName,$mother_fname,'',2));
				}
			}else{
				#edited by VAN 06-25-08
				if ($showBirth){
					#edited by VAN 05-19-08
					#$this->smarty->assign('sMother',$this->createTR(FALSE, 'mother_name', $segMotherName,$mother_name,'personDetails',2));
					$this->smarty->assign('sMother','<font color=red>*'.$segMotherName.'</font>');
					$this->smarty->assign('sMother_fname','<input type="text" name="mother_fname" id="mother_fname" size="30" value="'.$mother_fname.'">');
					$this->smarty->assign('sMother_mdname','<input type="text" name="mother_maidenname" id="mother_maidenname" value="'.$mother_maidenname.'">');
					$this->smarty->assign('sMother_mname','<input type="text" name="mother_mname" id="mother_mname" value="'.$mother_mname.'">');
					$this->smarty->assign('sMother_lname','<input type="text" name="mother_lname" id="mother_lname" size="25" value="'.$mother_lname.'">');
				}else{
					#$this->smarty->assign('sMother',$segMotherName);  #personDetails
					$this->smarty->assign('sMother',$this->createTR(FALSE, 'mother_fname', $segMotherName,$mother_fname ." ".$mother_maidenname." ". $mother_mname ." ".$mother_lname,'',2));
					
				}

			}
		}
		# burn added : March 10, 2007
		# make hideable
		if (!$GLOBAL_CONFIG['person_father_name_hide']){
			if ($sERDepartments){
				if ($showBirth){
					$this->smarty->assign('sFather',$segFatherName);
					$this->smarty->assign('sFather_fname','<input type="text" name="father_fname" id="father_fname" size="30" value="'.$father_fname.'">');
					$this->smarty->assign('sFather_mname','<input type="text" name="father_mname" id="father_mname" value="'.$father_mname.'">');
					$this->smarty->assign('sFather_lname','<input type="text" name="father_lname" id="father_lname" size="25" value="'.$father_lname.'">');
				}else{
					$this->smarty->assign('sFather',$this->createTR(FALSE, 'father_fname', $segFatherName,$father_fname,'',2));
				}
			}else{
				#$this->smarty->assign('sFather',$this->createTR(FALSE, 'father_name', $segFatherName,$father_name,'personDetails',2));
				if ($showBirth){
					$this->smarty->assign('sFather',$segFatherName);
					$this->smarty->assign('sFather_fname','<input type="text" name="father_fname" id="father_fname" size="30" value="'.$father_fname.'">');
					$this->smarty->assign('sFather_mname','<input type="text" name="father_mname" id="father_mname" value="'.$father_mname.'">');
					$this->smarty->assign('sFather_lname','<input type="text" name="father_lname" id="father_lname" size="25" value="'.$father_lname.'">');
				}else{
					$this->smarty->assign('sFather',$this->createTR(FALSE, 'father_fname', $segFatherName,$father_fname,'',2));
				}
			}
		}
		# burn added : March 10, 2007
		# make hideable
		if (!$GLOBAL_CONFIG['person_spouse_name_hide']){
			if ($sERDepartments){
					# ER-triage user, do not include family background in the Hide/Show Details
				$this->smarty->assign('sSpouse',$this->createTR(FALSE, 'spouse_name', $segSpouseName,$spouse_name,'',2));
			}else{
				$this->smarty->assign('sSpouse',$this->createTR(FALSE, 'spouse_name', $segSpouseName,$spouse_name,'',2));
			}
		}
		# burn added : March 10, 2007
		# make hideable
		if (!$GLOBAL_CONFIG['person_guardian_name_hide']){
			if ($sERDepartments){
					# ER-triage user, do not include family background in the Hide/Show Details
				$this->smarty->assign('sGuardian',$this->createTR(FALSE, 'guardian_name', $segGuardianName,$guardian_name,'',2));
			}else{
				$this->smarty->assign('sGuardian',$this->createTR(FALSE, 'guardian_name', $segGuardianName,$guardian_name,'',2));
			}
		}
		#edited by Borj 2014-17-01
		if ($ptype!='newborn' && $ptype!='medocs' && $ptype!='ipd' && $ptype!=''){

		}else{
			$this->smarty->assign('sVacHeader',$LDVaccinationsDet);
			$this->smarty->assign('sVacDetails',$this->createTR($errorvaddetails, 'vac_details', $LDVaccinationInfo,$vac_details));
			$this->smarty->assign('sVacDate',$this->createTR2($errorvacdate, 'vac_date', $LDVaccinationDate,$vac_date));
		}
		
		#END


		if ($erroraddress) $this->smarty->assign('LDAddress',"<font color=red>$LDAddress</font>:");
			else $this->smarty->assign('LDAddress',"$LDAddress:");

		if ($errorstreet) $this->smarty->assign('LDStreet',"<font color=red>* $LDStreet</font>:");
			else $this->smarty->assign('LDStreet',"<font color=#ff0000>* $LDStreet</font>");

		$this->smarty->assign('sStreetInput','<input name="addr_str" type="text" size="35" value="'.$addr_str.'">');

		if ($errorstreetnr) $this->smarty->assign('LDStreetNr',"<font color=red>* $LDStreetNr</font>:");
				else $this->smarty->assign('LDStreetNr',"<font color=#ff0000>* $LDStreetNr</font>");

		$this->smarty->assign('sStreetNrInput','<input name="addr_str_nr" type="text" size="10" value="'.$addr_str_nr.'">');

		if ($errortown) $this->smarty->assign('LDTownCity',"<font color=red>* $LDTownCity</font>:");   # burn modified: August 25, 2006
			else $this->smarty->assign('LDTownCity',"<font color=#ff0000>* $LDTownCity</font>");   # burn modified: August 25, 2006
		$this->smarty->assign('sTownCityInput','<input name="addr_citytown_name" type="text" size="35" value="'.$addr_citytown_name.'">');
		$this->smarty->assign('sTownCityMiniCalendar',"<a href=\"javascript:popSearchWin('citytown','aufnahmeform.addr_citytown_nr','aufnahmeform.addr_citytown_name')\"><img ".createComIcon($root_path,'b-write_addr.gif','0')."></a>");

		 if ($errorzip) $this->smarty->assign('LDZipCode',"<font color=red>*$LDZipCode</font> :");
			else  $this->smarty->assign('LDZipCode',"<font color=#ff0000>* $LDZipCode </font>");
		 $this->smarty->assign('sZipCodeInput','<input name="addr_zip" type="text" size="10" value="'.$addr_zip.'">');

				$this->smarty->assign('LDInsuranceBurn',"$LDInsuranceList:");   # burn added: August 28, 2006
				$this->smarty->assign('LDInsuranceClass',"$LDInsuranceClass");   # burn added: August 28, 2006
		// KB: make insurance completely hideable
		#------------commented by justin 03-17-15----------
		// if (!$GLOBAL_CONFIG['person_insurance_hide']){
		// 	//echo "insurance_show =".$insurance_show;
		// 	if($insurance_show) {
		// 		if (!$person_insurance_1_nr_hide) {

		// 			$this->smarty->assign('bShowInsurance',TRUE);

		// 			#$this->smarty->assign('sInsuranceNr',$this->createTR($errorinsurancenr, 'insurance_nr', $LDInsuranceNr.' ',$insurance_nr,'personDetails',2));

		// 			#-----------------added by VAN 09-04-07-------
		// 			if (empty($pid))
		// 				$pid = 0;

		// 			$this->smarty->assign('sBtnAddItem','<a href="javascript:void(0);"
		// 								 onclick="return overlib(
		// 											 OLiframeContent(\''.$root_path.'/modules/registration_admission/seg-reg-insurance-tray.php?pid='.$pid.'\', 600, 410, \'fOrderTray\', 0, \'auto\'),
		// 											 WIDTH,600, TEXTPADDING,0, BORDER,0,
		// 										 STICKY, SCROLL, CLOSECLICK, MODAL,
		// 										 CLOSETEXT, \'<img src='.$root_path.'/images/close_red.gif border=0 >\',
		// 																 CAPTIONPADDING,2,
		// 														 CAPTION,\'Add insurance from Admission Insurance tray\',
		// 												 MIDX,0, MIDY,0,
		// 											 STATUS,\'Add insurance from Admission Insurance tray\');"
		// 							 onmouseout="nd();">
		// 					<img name="btninsurance" id="btninsurance" src="'.$root_path.'images/his_additems_button.gif" border="0"></a>');

		// 			/* $this->smarty->assign('sOrderItems',"
		// 											<tr>
		// 												<td colspan=\"10\">Insurance list is currently empty...</td>
		// 											</tr>");
		// 			*/

		// 			$this->smarty->assign('sOrderItems',"
		// 					<tr>
		// 						<td colspan=\"10\">Insurance list is currently empty...</td>
		// 					</tr>");

		// 			# Note: make a class function for this part later
		// 			$result = $pinsure_obj->getPersonInsuranceItems($pid);
		// 			$count = $pinsure_obj->count;
		// 			#echo "sql = ".$pinsure_obj->sql;
		// 			#echo $count;
		// 			#echo "ss = ".is_object($result);
		// 			#echo "record count = ".$result->RecordCount();
		// 			$rows=array();
		// 			if (is_object($result)){
		// 				while ($row=$result->FetchRow()) {
		// 					$rows[] = $row;
		// 				}
		// 			}

		// 			$sql2 = "SELECT * FROM care_encounter WHERE pid ='".$pid."'";
		// 			$from_encounter=$db->Execute($sql2);
		// 			$row_from_encounter=$from_encounter->RecordCount();
		// 			if ($row_from_encounter!=0){
		// 				while($rsObj=$from_encounter->FetchRow()) {
		// 					$enc_cr = $rsObj["encounter_nr"];
		// 					$sql3 = "SELECT * from seg_encounter_insurance WHERE encounter_nr='".$rsObj["encounter_nr"]."'";
		// 					$from_seg_insurance=$db->Execute($sql3);
		// 					$row_from_seg_insurance=$from_seg_insurance->RecordCount();

		// 					if ($row_from_seg_insurance!=0){
		// 						while ($row=$from_seg_insurance->FetchRow()) {
		// 							$from_seg .= $row["hcare_id"].",";
		// 						}
		// 					}
		// 				}
		// 			}

		// 			#$from_care = substr($from_care, 0, strlen($from_care)-1);
		// 			#$from_care_list = explode(",",$from_care);
		// 			$from_seg = substr($from_seg, 0, strlen($from_seg)-1);
		// 			$from_seg_list = explode(",",$from_seg);
		// 			#-------------------------

		// 			$bulk_array_prev = array();
		// 			foreach ($rows as $i=>$row) {
		// 				if ($row) {
		// 					$count++;
		// 					$alt = ($count%2)+1;

		// 					$bulk_array_prev[] = array($row['hcare_id'],$row['insurance_nr'],$row["is_principal"]);
		// 					$insurance_array_prev .= $row['hcare_id'].",";

		// 					if (in_array($row['hcare_id'], $from_seg_list)){
		// 						$src .= '
		// 							<tr class="wardlistrow'.$alt.'" id="row'.$row['hcare_id'].'">
		// 								<input type="hidden" name="items[]" id="rowID'.$row['hcare_id'].'" value="'.$row['hcare_id'].'" />
		// 								<input type="hidden" name="nr[]" id="rowNr'.$row['hcare_id'].'" value="'.$row['insurance_nr'].'" />
		// 								<input type="hidden" name="is_principal[]" id="rowis_principal'.$row['hcare_id'].'" value="'.$row['is_principal'].'" />
		// 								<td class="centerAlign"><img src="../../images/insurance.gif" border="0"/>&nbsp;</td>
		// 								<td id="name'.$row['hcare_id'].'">'.$row['firm_id'].'</td>
		// 								<td width="25%" align="right" id="inspin'.$row['hcare_id'].'">'.$row['insurance_nr'].'</td>
		// 								<td width="18%" class="centerAlign" id="insprincipal'.$row['hcare_id'].'">'.(($row["is_principal"])?'YES':'NO').'</td>
		// 								<td></td>
		// 							</tr>
		// 						';
		// 					}else{
		// 						$src .= '
		// 							<tr class="wardlistrow'.$alt.'" id="row'.$row['hcare_id'].'">
		// 								<input type="hidden" name="items[]" id="rowID'.$row['hcare_id'].'" value="'.$row['hcare_id'].'" />
		// 								<input type="hidden" name="nr[]" id="rowNr'.$row['hcare_id'].'" value="'.$row['insurance_nr'].'" />
		// 								<input type="hidden" name="is_principal[]" id="rowis_principal'.$row['hcare_id'].'" value="'.$row["is_principal"].'" />
		// 								<td class="centerAlign"><a href="javascript:removeItem(\''.$row['hcare_id'].'\')"><img src="../../images/btn_delitem.gif" border="0"/></a></td>
		// 								<td id="name'.$row['hcare_id'].'">'.$row['firm_id'].'</td>
		// 								<td width="25%" align="right" id="inspin'.$row['hcare_id'].'">'.$row['insurance_nr'].'</td>
		// 								<td width="18%" class="centerAlign" id="insprincipal'.$row['hcare_id'].'">'.(($row["is_principal"])?'YES':'NO').'</td>
		// 								<td></td>
		// 							</tr>
		// 						';
		// 					}
		// 				}
		// 			}

		// 			$insurance_array_prev = substr($insurance_array_prev,0,strlen($insurance_array_prev)-1);

		// 			if (($src) && ($insurance_class_nr!=3))
		// 				$this->smarty->assign('sOrderItems',$src);
		// 			#--------------------------------------------
		// 			#edited by VAN
		// 			#if ($errorinsuranceclass) $this->smarty->assign('sErrorInsClass',"<font color=\"$error_fontcolor\">");
		// 			if ($errorinsuranceclass) $this->smarty->assign('LDInsuranceNr',"<font color=red>$LDInsuranceList</font>");
		// 			else  $this->smarty->assign('LDInsuranceNr',$LDInsuranceList);

		// 			if($insurance_classes!=false){
		// 				$sInsClassBuffer='';

		// 				#---------added by VAN-------------
		// 				$sql = "SELECT ci.* FROM care_person_insurance AS ci
		// 						WHERE ci.pid ='".$pid."' LIMIT 1";
		// 				$res=$db->Execute($sql);
		// 				$rsObj=$res->FetchRow();
		// 				$insurance_class_nr = $rsObj['class_nr'];
		// 				if ($insurance_class_nr==NULL)
		// 					$insurance_class_nr = 3;
		// 				#---------------------------------
		// 				while($result=$insurance_classes->FetchRow()) {
		// 					$sInsClassBuffer.='<input name="insurance_class_nr" id="insurance_class_nr" type="radio" onChange="DisableInsurance();" value="'.$result['class_nr'].'" ';
		// 					if($insurance_class_nr==$result['class_nr']) $sInsClassBuffer.='checked';
		// 					$sInsClassBuffer.='>';

		// 					$LD=$result['LD_var'];
		// 					if(isset($$LD)&&!empty($$LD)) $sInsClassBuffer.=$$LD; else $sInsClassBuffer.=$result['name'];
		// 					$sInsClassBuffer.='&nbsp;';
		// 				}

		// 				$this->smarty->append('sInsClasses',$sInsClassBuffer);

		// 			} else {
		// 				#echo "else";
		// 				$this->smarty->assign('sInsClasses','no insurance class');
		// 				/*
		// 				if ($errorinsurancecoid) $this->smarty->assign('LDInsuranceCo',"<font color=red>$LDInsuranceCo</font> :");
		// 				else  $this->smarty->assign('LDInsuranceCo',"$LDInsuranceCo :");

		// 				$this->smarty->assign('sInsCoNameInput','<input name="insurance_firm_name" onBlur="trimString(this)" type="text" size="35" value="'.$insurance_firm_name.'">');
		// 				$this->smarty->assign('sInsCoMiniCalendar',"<a href=\"javascript:popSearchWin('insurance','aufnahmeform.insurance_firm_id','aufnahmeform.insurance_firm_name')\"><img ".createComIcon($root_path,'b-write_addr.gif','0')."></a>");
		// 				*/
		// 			} #end of if($insurance_classes!=false){
		// 		} #end of if (!$person_insurance_1_nr_hide)
		// 	} else {

		// 		$this->smarty->assign('bNoInsurance',TRUE);
		// 		$this->smarty->assign('LDSeveralInsurances','<a href="#">$LDSeveralInsurances <img '.createComIcon($root_path,'frage.gif','0').'></a>');
		// 	}
		// }


		if (!$GLOBAL_CONFIG['person_phone_1_nr_hide']){
			$this->smarty->assign('sPhone1',$this->createTR($errorphone1, 'phone_1_nr', $LDPhone.' 1',$phone_1_nr,'',2));
		}

		#edited by KENTOOT 09-17-2014
		if ($GLOBAL_CONFIG['person_cellphone_1_nr_hide']){
			$this->smarty->assign('sCellPhone1',$this->createTR($errorcell1, 'cellphone_1_nr', $LDCellPhone.' No.',$cellphone_1_nr,2));
		}

		if (!$GLOBAL_CONFIG['person_phone_2_nr_hide']){
			$this->smarty->assign('sPhone2',$this->createTR($errorphone2, 'phone_2_nr', $LDPhone.' 2',$phone_2_nr,'personDetails',2));
		}

		if (!$GLOBAL_CONFIG['person_cellphone_2_nr_hide']){
			$this->smarty->assign('sCellPhone2',$this->createTR($errorcell2, 'cellphone_2_nr', $LDCellPhone.' 2',$cellphone_2_nr,'personDetails',2));
		}
		if (!$GLOBAL_CONFIG['person_fax_hide']){
			$this->smarty->assign('sFax',$this->createTR($errorfax, 'fax', $LDFax,$fax,'personDetails',2));
		}
		if (!$GLOBAL_CONFIG['person_email_hide']){
			$this->smarty->assign('sEmail',$this->createTR($erroremail, 'email', $LDEmail,$email,'personDetails',2));
		}

		#added by VAN 10/24/2016
		#for IPBM, HOMIS id
		$show_homisid = 0;
		if (($_GET['from']=='ipbm')||($_GET['ptype']=='ipbm')||($homis_id))
			$show_homisid = 1;

		$this->smarty->assign('sForIPBM',$show_homisid);
		
		$this->smarty->assign('LDIDHOMIS','HOMIS ID');

		if (($homis_id) && !(($_GET['from']=='ipbm')||($_GET['ptype']=='ipbm')))
			$this->smarty->assign('sIDHOMIS',$homis_id.'<input type="hidden" name="homis_id" id="homis_id" value="'.$homis_id.'">');
		else	
			$this->smarty->assign('sIDHOMIS','<input type="text" name="homis_id" id="homis_id" value="'.$homis_id.'">');
		

		/* Create the helper class for the country table */
		include_once($root_path.'include/care_api_classes/class_address.php');
		$address_country = new Address('country');
		$country_obj = $address_country->getAllAddress();

		if (!$GLOBAL_CONFIG['person_citizenship_hide']){
			if (empty($citizenship))
				$citizenship="PH";   # default country of citizenship
			$this->smarty->assign('sCitizenship',$this->createTRselect($country_obj, $errorcitizen, $segCitizenship, 'citizenship', $citizenship, 'country_code', 'country_name','', 2));   # burn added: March 14, 2007
		}
		if (!$GLOBAL_CONFIG['person_sss_nr_hide']){
			$this->smarty->assign('sSSSNr',$this->createTR($errorsss, 'sss_nr', $LDSSSNr,$sss_nr,'personDetails',2));
		}
		if (!$GLOBAL_CONFIG['person_nat_id_nr_hide']){
			$this->smarty->assign('sNatIdNr',$this->createTR($errornatid, 'nat_id_nr', $LDNatIdNr,$nat_id_nr,'personDetails',2));
		}
			# Retrieve record set of all religions
		$religion_obj = $person_obj->getReligion();

		if (!$GLOBAL_CONFIG['person_religion_hide']){
			if (empty($religion))
				$religion=1;   # default religion, 'Not Indicated'
			$this->smarty->assign('sReligion',$this->createTRselect($religion_obj,$errorreligion,"<font color=#ff0000>*$LDReligion</font>", 'religion', $religion, 'religion_nr', 'religion_name','', 2));   # burn added: March 14, 2007
		}

		#added by VAN 05-09-08
		$ethnic_obj = $person_obj->getEthnic_orig();

		if (!$GLOBAL_CONFIG['person_ethnic_orig_hide']){


			if (empty($ethnic_orig))
				$ethnic_orig=1;   # default ethnic, 'Not Indicated'
			$this->smarty->assign('sEthnicOrig',$this->createTRselect($ethnic_obj,$errorethnic,$LDEthnicOrigin, 'ethnic_orig', $ethnic_orig, 'nr', 'name','', 2));

		}
		// KB: add a field for other HIS nr
		if (!$GLOBAL_CONFIG['person_other_his_nr_hide']){
			$this->smarty->assign('bShowOtherHospNr',TRUE);

			$this->smarty->assign('LDOtherHospitalNr',$LDOtherHospitalNr);

			$other_hosp_list = $person_obj->OtherHospNrList();
			$sOtherNrBuffer='';
			foreach( $other_hosp_list as $k=>$v ){
				$sOtherNrBuffer.="<b>".$kb_other_his_array[$k].":</b> ".$v."<br />\n";
			}

			$this->smarty->assign('sOtherNr',$sOtherNrBuffer);

			$sOtherNrBuffer='';
			$sOtherNrBuffer.="<SELECT name=\"other_his_org\">".
						"<OPTION value=\"\">--</OPTION>";
			foreach( $kb_other_his_array as $k=>$v ){
				$sOtherNrBuffer.="<OPTION value=\"$k\" $check>$v</OPTION>";
			}
			$sOtherNrBuffer.="</SELECT>\n".
					"&nbsp;&nbsp;".
					"$LDNr:<INPUT name=\"other_his_no\" size=20><br />\n";

			$sOtherNrBuffer.="($LDSelectOtherHospital - $LDNoNrNoDelete)".
						"<br />\n";
			$sOtherNrBuffer.="</TD></TR>\n\n";

			$this->smarty->assign('sOtherNrSelect',$sOtherNrBuffer);
		}
		$this->smarty->assign('LDRegBy','Modify By' );
		if(isset($user_id) && $user_id) $buffer=$user_id; else  $buffer = $HTTP_SESSION_VARS['sess_user_name'];
		$this->smarty->assign('sRegByInput','<input  name="user_id" type="text" value="'.$buffer.'"  size="35" readonly>');

		# Collect the hidden inputs

		ob_start();
			echo "			<input type='hidden' name='ERSave' value='$ERSave'>";   # burn added: March 15, 2007

				  $birth_days1 = strtotime($date_birth);
				  $birth_days2 = date('m/d/Y',$birth_days1);
?>
			<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="1000000">
			<input type="hidden" name="itemname" value="<?php echo $itemname; ?>">
			<input type="hidden" name="sid" value="<?php echo $sid; ?>">
			<input type="hidden" name="lang" value="<?php echo $lang; ?>">
			<input type="hidden" name="linecount" value="<?php echo $linecount; ?>">
			<input type="hidden" name="mode" id="mode" value="save">
			<input type="hidden" name="addr_citytown_nr" value="<?php echo $addr_citytown_nr; ?>">
			<input type="hidden" name="insurance_item_nr" value="<?php echo $insurance_item_nr; ?>">
			<input type="hidden" name="insurance_firm_id" value="<?php echo $insurance_firm_id; ?>">
			<input type="hidden" name="insurance_show" value="<?php echo $insurance_show; ?>">
			<!--<input type="hidden" name="ethnic_orig" value="<?php echo $ethnic_orig; ?>">-->

			<!----added by VAN 09-07-07---------->
			<input type="hidden" name="bulk_array_prev" id="bulk_array_prev" value='<?php echo serialize($bulk_array_prev); ?>' size="100">
			<input type="hidden" name="insurance_array_prev" id="insurance_array_prev" value='<?php echo $insurance_array_prev; ?>' size="100">
			<!-- -->
			<input type="hidden" name="ptype" id="ptype" value="<?=$ptype?>" />
			<!-- added by VAN 06-25-08-->

			<input type="hidden" name="showBirth" id="showBirth" value="<?=($showBirth)?$showBirth:0?>">
			<input type="hidden" name="birth_section" id="birth_section" value="<?=$birth_section?>">
			<input type="hidden" name="fromtemp" id="fromtemp" value="<?=($fromtemp)?$fromtemp:0?>">
			<!-- added by LST 09-01-2009 -->
			<!--<textarea  id="fpcode" NAME="fpcode" COLS=50 ROWS=20 style="display:none"><?= $fpimage ?></textarea>-->			
			<!-- -->
			<!-- added by Matsuu 12132016 -->
			<input type="hidden" name="patientTtype" id="patientType" size="25" value="<?=$_GET['ptype']?>">
			<input type="hidden" name="is_update" id="is_update" size="25" value="<?=$_GET['update']?>">
			<!-- ended Matsuu -->

			<!-- Added by Matsu 11132016 -->
			<input type="hidden" name="name_last_data" id="name_last_data" value="<?=$name_last?>">
			<input type="hidden" name="name_first_data" id="name_first_data" value="<?=$name_first?>">
			<input type="hidden" name="name_middle_data" id="name_middle_data" value="<?=$name_middle?>">
			<input type="hidden" name="sex_data" id="sex_data" value="<?= $sex?>">
			<input type="hidden" name="birth_data" id="birth_data" value="<?= $birth_days2 ?>">
			<input type="hidden" name="pid_data" id="pid_data" value="<?=$pid?>">
			<input type="hidden" name="status_data" id="status_data" value="<?=$civil_status?>">


<?php
		if($update){
			$this->smarty->assign('sUpdateHiddenInputs','<input type="hidden" name="update" value=1><input type="hidden" name="pid" id="pid" value="'.$pid.'">');
		}

		$sTemp= ob_get_contents();
		ob_end_clean();
		$this->smarty->assign('sHiddenInputs',$sTemp);

		#$this->smarty->assign('pbSubmit','<input id="submit" name="submit" type="image" '.createLDImgSrc($root_path,'savedisc.gif','0').'  alt="'.$LDSaveData.'" align="absmiddle">');
		#commented by VAN 01-09-2015
		#$this->smarty->assign('pbSubmit','<span id="bsave"><a href="javascript:void(0);"><img id="saveButton5" name="saveButton" '.createLDImgSrc($root_path,'savedisc.gif','0').'  title="'.$LDSaveData.'"  align="absmiddle" onclick="gotoCheck();"></a></span>');
        //edited by jasper 08/24/2013 - FIX FOR BUGZILLA 262
        if($isIPBM){
        	$this->smarty->assign('pbReset','<a href="'. URL_APPEND . '&pid=' . $pid .$IPBMextend.'"><img '.createLDImgSrc($root_path,'reset.gif','0').' alt="'.$LDResetData.'"   align="absmiddle"></a>');
        }
        else{
        $this->smarty->assign('pbReset','<a href="'. URL_APPEND . '&pid=' . $pid .'&update=1"><img '.createLDImgSrc($root_path,'reset.gif','0').' alt="'.$LDResetData.'"   align="absmiddle"></a>');
        }
        //removed by jasper 08/24/2013
        //$this->smarty->assign('pbReset','<a href="javascript:document.aufnahmeform.reset()"><img '.createLDImgSrc($root_path,'reset.gif','0').' alt="'.$LDResetData.'"   align="absmiddle"></a>');

		if ((($allow_er_user)||($allow_medocs_user)) && ($ptype=='er') && (!$update)){
			# visible only if the user is from ER
			#$this->smarty->assign('pbERSubmit','<input id="ERsubmit" name="ERsubmit"  type="image" '.createLDImgSrc($root_path,'er_savedisc.gif','0').'  alt="Save data & Admit" align="absmiddle" onClick="jsSetERSave();">');
			$this->smarty->assign('pbERSubmit','<span id="bsaveER"><a href="javascript:void(0);"><img id="ERsubmit" name="ERsubmit" '.createLDImgSrc($root_path,'er_savedisc.gif','0').' alt="Save data & Admit"  title="'.$LDSaveData.'"  align="absmiddle" onclick="gotoCheck();"></a></span>');

		}elseif (($allow_er_user) && ($ptype=='er') && ($update)){	#added by VAN 11-09-2008
			#$this->smarty->assign('pbERSubmit','<input id="submit" name="submit" type="image" '.createLDImgSrc($root_path,'savedisc.gif','0').'  alt="'.$LDSaveData.'" align="absmiddle">');
			$this->smarty->assign('pbERSubmit','<span id="bsave"><a href="javascript:void(0);"><img id="saveButton" name="saveButton" '.createLDImgSrc($root_path,'savedisc.gif','0').'  alt="Save data & Admit"  title="'.$LDSaveData.'"  align="absmiddle" onclick="gotoCheck();"></a></span>');
		}else{
			$this->smarty->assign('pbSubmit','<span id="bsave"><a href="javascript:void(0);"><img id="saveButton5" name="saveButton" '.createLDImgSrc($root_path,'savedisc.gif','0').'  title="'.$LDSaveData.'"  align="absmiddle" onclick="gotoCheck();"></a></span>');
		}

		if($error||$error_person_exists)		$this->smarty->assign('pbForceSave','<input id="forceSaver" type="button" value="'.$LDForceSave.'" onClick="forceSave()">');

		if (!$newdata){
			ob_start();
?>
			<form action=<?php echo $thisfile; ?> method=post>
				<input type=hidden name=sid value=<?php echo $sid; ?>>
				<input type=hidden name=patnum value="">
				<input type=hidden name="lang" value="<?php echo $lang; ?>">
				<input type=hidden name="date_format" value="<?php echo $date_format; ?>">
				<input type=submit value="<?php echo $LDNewForm ?>" >
			</form>
<?php
			$sTemp= ob_get_contents();
			ob_end_clean();
			$this->smarty->assign('sNewDataForm',$sTemp);
		}

		# Set the form template as form
		$this->smarty->assign('bSetAsForm',TRUE);

require_once($root_path.'modules/registration_admission/address_new.php');
$this->smarty->assign('segAddressNew',"$segAddressNew");

		if($this->bReturnOnly){
			ob_start();
				$this->smarty->display('registration_admission/reg_form.tpl');
				$sTemp=ob_get_contents();
			ob_end_clean();
			return $sTemp;
		}else{
			# show Template
			$this->smarty->display('registration_admission/reg_form.tpl');
		}

	} // end of function

	function create(){
		$this->bReturnOnly = TRUE;
		return $this->display();
	}
} // end of class
?>