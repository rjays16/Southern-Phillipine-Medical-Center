<?php
/*------begin------ This protection code was suggested by Luki R. luki@karet.org ---- */
if (eregi('save_immunization.inc.php',$PHP_SELF)) 
	die('<meta http-equiv="refresh" content="0; url=../">');

require_once($root_path.'include/care_api_classes/class_immunization.php');
if(!isset($imm_obj)) $imm_obj=new Immunization;

require_once($root_path.'include/inc_date_format_functions.php');
# Check date, default is today
if($HTTP_POST_VARS['date']) $HTTP_POST_VARS['date']=@formatDate2STD($HTTP_POST_VARS['date'],$date_format);
	else $HTTP_POST_VARS['date']=date('Y-m-d');
if($HTTP_POST_VARS['refresh_date']) $HTTP_POST_VARS['refresh_date']=@formatDate2STD($HTTP_POST_VARS['refresh_date'],$date_format);

$imm_obj->setDataArray($HTTP_POST_VARS);

if($type&&$medicine&&$dosage&&$application_type_nr&&$application_by){

	switch($mode){	
		case 'create': 
								//if($HTTP_POST_VARS['date') $HTTP_POST_VARS['date']=@formatDate2STD($HTTP_POST_VARS['date'],$date_format);
								//if($HTTP_POST_VARS['refresh_date') $HTTP_POST_VARS['date']=@formatDate2STD($HTTP_POST_VARS['refresh_date'],$date_format);
								
								if($imm_obj->insertDataFromInternalArray()) 
									{
										header("location:".$thisfile.URL_REDIRECT_APPEND."&mode=show&target=$target&pid=".$HTTP_SESSION_VARS['sess_pid']);
										exit;
									}
									else echo "<br>$LDDbNoSave";
								break;
		case 'update': 
								//$HTTP_POST_VARS['date']=@formatDate2STD($HTTP_POST_VARS['date'],$date_format);
								//$imm_obj->setDataArray($HTTP_POST_VARS);
								$imm_obj->setWhereCond("nr=$imm_nr");
								if($imm_obj->updateDataFromInternalArray($dept_nr)) 
									{
										header("location:".$thisfile.URL_REDIRECT_APPEND."&target=$target&pid=".$HTTP_SESSION_VARS['sess_pid']);
										exit;
									}
									else echo "$sql<br>$LDDbNoUpdate";
								break;
					
	}// end of switch
} # end of if()

?>
