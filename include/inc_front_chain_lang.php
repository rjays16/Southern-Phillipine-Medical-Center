<?php
# To see how the script locking is implemented in this script see /development/dev_docs/script_locking.txt 

#------begin------ This protection code was suggested by Luki R. luki@karet.org ----
if (eregi('inc_front_chain_lang.php',$PHP_SELF)) 
	die('<meta http-equiv="refresh" content="0; url=../">');
#------end-----

# Set  to TRUE if you want to disable the time-out feature,
# Once this value is taken from the database, this will be used as the last resort default value
$TIME_OUT_INACTIVE=FALSE;
# Set for  the time out value. The format is MinutesSeconds, e.g.  530 = 5 minutes, 20 seconds or e.g. 2000 = 20 minutes, 00 seconds
# Once the time-out value is taken from the database, this will be used as the last resort default value
$TIME_OUT_TIME=100;

# Establish db connection	
require_once($root_path.'include/inc_db_makelink.php');

# The function getLang gets the language code and stores it to the lang variable
# The ck_language variable is a cookie which holds the language code stored at the beginning of
# browser�s session. After acquiring the language code, the existence of the language table is
# checked. If language table does not exist, function returns 0.
#
# param chk_file =  filename of the language table
# return = 1 if language table exists 

function getLang($chk_file) 
{
   global $lang, $root_path, $sid;
   
   if(!isset($lang)||empty($lang))
   {
	  $ck_lang_buffer='ck_lang'.$sid;
      if(!isset($HTTP_COOKIE_VARS[$ck_lang_buffer])||empty($HTTP_COOKIE_VARS[$ck_lang_buffer])) include($root_path.'chklang.php');
         else $lang=$HTTP_COOKIE_VARS[$ck_lang_buffer];
   }
   
   if(file_exists($root_path.'language/'.$lang.'/lang_'.$lang.'_'.$chk_file)) return 1;
      else return 0;
}

# Load charset function
require_once($root_path.'include/inc_charset_fx.php'); // charset functions

# The following lines of code is the script chaining detector. It compares the sid values propagated via
# the relative url with the ck_sid+sid (decrypted) cookie values. If the two don�t match, a warning message will apear and
# the script exits stopping the execution. If the caller script does not require chaining, it must set the
# constant NO_CHAIN to 1 before including this script.

if(!defined('NO_CHAIN')||NO_CHAIN!=1){
   $no_valid=0;
   
   if(!isset($sid)) $sid=NULL;
   
   $ck_sid_buffer='ck_sid'.$sid;
	 
   define('INIT_DECODE',1); # set flag to decrypt
	
   include($root_path.'include/inc_init_crypt.php'); # initialize crypt 

   $clear_ck_sid = $dec_hcemd5->DecodeMimeSelfRand($HTTP_COOKIE_VARS[$ck_sid_buffer]);

	$tnow=date('His');
   // echo $tnow."<p>";
	$time_out=FALSE;

   if(!defined('NO_2LEVEL_CHK')||NO_2LEVEL_CHK!=1){
		
		# Let us check if the calling script is the time-out configuration script, if yes, then we skip the time out
		if (!eregi('edv_system_timeout.php',$PHP_SELF)) {
			# Load the global time out configs
			include_once($root_path.'include/care_api_classes/class_globalconfig.php');
			if(!isset($GLOBAL_CONFIG)) $GLOBAL_CONFIG=array();
			$gc_obj=& new GlobalConfig($GLOBAL_CONFIG);
			$gc_obj->getConfig('timeout_%');
			# If config data available, use it
			if($GLOBAL_CONFIG['timeout_inactive']) $TIME_OUT_INACTIVE=$GLOBAL_CONFIG['timeout_inactive'];
			if((int)$GLOBAL_CONFIG['timeout_time']) $TIME_OUT_TIME=(int)$GLOBAL_CONFIG['timeout_time'];
		
			if(!$TIME_OUT_INACTIVE){
    				//echo $tnow."<br>";
					//echo $HTTP_SESSION_VARS['sess_tos']."<br>";
					//echo ($tnow-$HTTP_SESSION_VARS['sess_tos'])."<br>";
	  			# Check if session is still valid 
	  			if(isset($HTTP_SESSION_VARS['sess_tos'])||session_is_registered('sess_tos')){
					# Check if time out value is positive or not zero
					# current time minus start time
				if(($tnow - $HTTP_SESSION_VARS['sess_tos']) >= $TIME_OUT_TIME) $time_out=TRUE;
						else $HTTP_SESSION_VARS['sess_tos']=$tnow;
				}else{
					$time_out=TRUE;
				}
			
    			//if(!isset($HTTP_SESSION_VARS['sess_tos'])||!session_is_registered('sess_tos')||!$HTTP_SESSION_VARS['sess_tos']||$time_out){
    			if($time_out||!$HTTP_SESSION_VARS['sess_tos']){
    				//echo $tnow."<br>";
					//echo $HTTP_SESSION_VARS['sess_tos']."<br>";
					//echo ($tnow-$HTTP_SESSION_VARS['sess_tos'])."<br>";
	  				//echo "session expired<br>";
    				//echo $TIME_OUT_TIME."<br>";
					//echo $HTTP_SESSION_VARS['sess_user_id'];
					
					/*
					echo "ALVIN DEBUGGER: TIME_OUT_INACTIVE=[";
					var_dump($TIME_OUT_INACTIVE);
					echo "]<hr>";

					echo "ALVIN DEBUGGER: TIME_OUT_TIME=[";
					var_dump($TIME_OUT_TIME);
					echo "]<hr>";

					echo "ALVIN DEBUGGER: TNOW=[";
					print_r($tnow);
					echo "]<hr>";
					
					echo "ALVIN DEBUGGER: SESS_TOS=[";
					print_r($HTTP_SESSION_VARS['sess_tos']);
					echo "]<hr>";					
					
					*/
					# Show session time out warning and exit the script to stop the module
					include($root_path."include/inc_session_timeout_warning.php");
					exit;
				}else{
					# Reset the time-out start time
					$HTTP_SESSION_VARS['sess_tos']=$tnow;
					//echo $tnow;
				}
			}
		}
	  # Decrypt the second level cookie sid and compare to sid
       $dec_2level = new Crypt_HCEMD5($key_2level, '');
       $clear_2sid = $dec_2level->DecodeMimeSelfRand($HTTP_COOKIE_VARS[('ck_2level_sid'.$sid)]);
   
       if(!$sid||($sid!=$clear_ck_sid)||($sid!=$clear_2sid)||!isset($HTTP_COOKIE_VARS[$local_user.$sid])||empty($HTTP_COOKIE_VARS[$local_user.$sid])) $no_valid=1;
	# if(!$sid||($sid!=$clear_ck_sid)||($sid!=$clear_2sid)) $no_valid=1; 
   }elseif (!$sid||($sid!=$clear_ck_sid)){
   		$no_valid=1;
			 #die("novalid:$no_valid, sid:$sid, clear_ck_sid:$clear_sk_sid");
	}else{
		# Reset the time-out start time
		$HTTP_SESSION_VARS['sess_tos']=$tnow;
	}

    $REP_PORTAL_TXT = "report_portal";
    $GLOBAL_CONFIG_TBL = 'care_config_global';
    $connect_to_instance = $db->GetOne("SELECT notes FROM ".($GLOBAL_CONFIG_TBL)." WHERE type=".$db->qstr($REP_PORTAL_TXT));
    if ($connect_to_instance){
        $no_valid = 0;
    }

    if ($no_valid) {
       if(getLang('invalid-access-warning.php')) {
					die("Unauthorized Page Access");
					exit;
	       header('Location:'.$root_path.'language/'.$lang.'/lang_'.$lang.'_invalid-access-warning.php'); 
	   }
	     else {
					die("Unauthorized Page Access");
					exit;
		     header('Location:'.$root_path.'language/'.LANG_DEFAULT.'/lang_'.LANG_DEFAULT.'_invalid-access-warning.php');
		 } 
       
	   exit;
   }
}; 


# The constant LANG_FILE contains the file name of the language table without the lang_xx_ component.
# This constant  must be set by the script that calls this file.
# If the calling script does not need a language table, the constant LANG_FILE must be set to empty string '' 
// commented for causing errors by kenneth 08/14/2017
// if(defined('LANG_FILE')&&LANG_FILE!='') {
// 	include_once($root_path.'language/'.$lang.'/lang_'.$lang.'_globals.php');
// }
// else include_once ($root_path.'language/'.LANG_DEFAULT.'/lang_'.LANG_DEFAULT.'_globals.php');

if(defined('LANG_FILE')&&LANG_FILE!='') {
    if(getLang(LANG_FILE)) include($root_path.'language/'.$lang.'/lang_'.$lang.'_'.LANG_FILE);
	    else include ($root_path.'language/'.LANG_DEFAULT.'/lang_'.LANG_DEFAULT.'_'.LANG_FILE);
}

	# Load additional language tables
if(isset($lang_tables)&&is_array($lang_tables)&&sizeof($lang_tables)) include_once($root_path.'include/inc_load_lang_tables.php');

#  Load additional environment files 
require_once($root_path.'include/inc_config_color.php'); # load user configurations
require_once($root_path.'include/inc_img_fx.php'); # image functions

# Resolve the template theme
if(isset($cfg['template_theme'])&&!empty($cfg['template_theme'])) $template_theme=$cfg['template_theme'];
	else $template_theme = 'default';

# Load template class by default
if(!defined('NO_TEMPLATE')||!NO_TEMPLATE){
	require_once($root_path.'include/care_api_classes/class_template.php'); // template class
	# Template object
	$TP_obj=new Template($root_path,$template_path,$template_theme);
}

# define environment constants
//define('MODERATE_NEWS',0);  // define to 1 if news is moderated
//define('LANG_DEPENDENT',0); // define to 1 if the news contents are language dependent

?>