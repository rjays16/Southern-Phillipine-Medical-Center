<?php
/**
 * SEgHIS Integrated Information System for Hospitals and Health Care Organizations and Services
 */

/**
 * Front controller (Yii framework)
 */

if (isset($_GET['r'])) {
    // Load inc_environment_global to use care2x sessions
    require './roots.php';
    require_once './include/inc_environment_global.php';

    // change the following paths if necessary
    $yii=dirname(__FILE__).'/classes/yii/yii.php';
    $config=dirname(__FILE__).'/frontend/protected/config/main.php';

    // remove the following lines when in production mode
    defined('YII_DEBUG') or define('YII_DEBUG',true);
    // specify how many levels of call stack should be shown in each log message
    defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

    require './frontend/protected/vendor/autoload.php';    
    require_once($yii);
    
    require_once dirname(__FILE__).'/frontend/SegHis.php';
    $application = new SegHis($config);    
    $application->run();
//    Yii::createWebApplication($config)->run();
    exit;
}


error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
define('FROM_ROOT',1);

if(!isset($mask)) $mask=false;
if(!isset($cookie)) $cookie=false;
if(!isset($_chg_lang_)) $_chg_lang_=false;
if(!isset($boot)) $boot=false;
if(!isset($sid)) $sid='';

require('./roots.php');
require('./include/inc_environment_global.php');

//$db->debug=1;

# Register global session variables
if(!session_is_registered('sess_user_name')) session_register('sess_user_name');
if(!session_is_registered('sess_user_origin')) session_register('sess_user_origin');
if(!session_is_registered('sess_file_forward')) session_register('sess_file_forward');
if(!session_is_registered('sess_file_return')) session_register('sess_file_return');
if(!session_is_registered('sess_file_break')) session_register('sess_file_break');
if(!session_is_registered('sess_path_referer')) session_register('sess_path_referer');
if(!session_is_registered('sess_dept_nr')) session_register('sess_dept_nr');
if(!session_is_registered('sess_title')) session_register('sess_title');
if(!session_is_registered('sess_lang')) session_register('sess_lang');
if(!session_is_registered('sess_user_id')) session_register('sess_user_id');
if(!session_is_registered('sess_cur_page')) session_register('sess_cur_page');
if(!session_is_registered('sess_searchkey')) session_register('sess_searchkey');
if(!session_is_registered('sess_tos')) session_register('sess_tos'); # the session time out start time

$bname='';
$bversion='';
$user_id='';
$ip='';
$cfgid='';
$config_exists=false;

$GLOBALCONFIG=array();
$USERCONFIG=array();

/****************************************************************************
 phpSniff: HTTP_USER_AGENT Client Sniffer for PHP
 Copyright (C) 2001 Roger Raymond ~ epsilon7@users.sourceforge.net

* Check environment : Browser, OS
* @param string $bn  name of browser
* @param string $bv  version of browser
* @param string $f   CFG filename
* @param string $i   IP adress
* @param string $uid new guid (session var)
* @return all parameter using &
* @access public
*
* 02.02.2003 Thomas Wiedmann
****************************************************************************
*/

require_once('./classes/phpSniff/phpSniff.class.php'); # Sniffer for PHP

function configNew(&$bn,&$bv,&$f,$i,&$uid)
{
    global $HTTP_USER_AGENT;
    global $REMOTE_ADDR;

    # We disable the error reporting, because Konqueror 3.0.3 causes a  runtime error output that stops the program.
    #  could be a bug in phpsniff .. hmmm?
    $old_err_rep= error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);

    # Function rewritten by Thomas Wiedmann to use phpSniff class

    # initialize some vars
    if(!isset($UA)) $UA = '';
    if(!isset($cc)) $cc = '';
    if(!isset($dl)) $dl = '';
    if(!isset($am)) $am = '';

    //$timer = new phpTimer();
    //$timer->start('main');
    //$timer->start('client1');
    $sniffer_settings = array('check_cookies'=>$cc,'default_language'=>$dl,'allow_masquerading'=>$am);
    $client = new phpSniff($UA,$sniffer_settings);

    # get phpSniff result
    $i=$client->get_property('ip');
    $bv=$client->get_property('version');
    $bn=$client->get_property('browser');

    # translate some browsernames for "segHIS"
    if ($bn == 'moz') { $bn='mozilla';}
    else if ($bn == 'op') { $bn='opera';}
    else if ($bn == 'ns') { $bn='netscape';}
    else if ($bn == 'ie') { $bn='msie';}

    $uid=uniqid('');
    $f='CFG'.$uid.microtime().'.cfg';

     # Return previous error reporting
     error_reporting($old_err_rep);
}

/**
* Create simple session id (sid), save a encrpyted  sid to a cookie with a dynamic name
* consisting of concatenating "ck_sid" and the sid itself.
* For more information about the encryption class, see the proper docs of the pear's "hcemd5.php" class.
*/
//$sid=uniqid('');
$sid=session_id();
$ck_sid_buffer='ck_sid'.$sid;

include('include/inc_init_crypt.php'); // initialize crypt
$ciphersid=$enc_hcemd5->encodeMimeSelfRand($sid);
setcookie($ck_sid_buffer,$ciphersid);
$HTTP_COOKIE_VARS[$ck_sid_buffer]=$ciphersid;

#
# Simple counter, counts all hits including revisits
# Uncomment the following line  if you  like to count the hits, then make sure
# that the path /counter/hits/ and the file /counter/hitcount.txt  are system writeable
#
// include('./counter/count.php');


if((isset($boot)&&$boot)||!isset($HTTP_COOKIE_VARS['ck_config'])||empty($HTTP_COOKIE_VARS['ck_config'])) {
        configNew($bname,$bversion,$user_id,$ip,$cfgid);
} else {
        $user_id=$HTTP_COOKIE_VARS['ck_config'];
}

#
# Load user config API. Get the user config data from db
#
require_once('include/care_api_classes/class_userconfig.php');
$cfg_obj=new UserConfig;

if($cfg_obj->exists($user_id)) {
    $cfg_obj->getConfig($user_id);
    $USERCONFIG=$cfg_obj->buffer;
        $config_exists=true;  // Flag that user config is existing
}else{
    $cfg_obj->_getDefault();
    $USERCONFIG=$cfg_obj->buffer;
}

# Load global configurations API
require_once('include/care_api_classes/class_globalconfig.php');
$glob_cfg=new GlobalConfig($GLOBALCONFIG);

# Get the global config for language usage
$glob_cfg->getConfig('language_%');
# Get the global config for frames
$glob_cfg->getConfig('gui_frame_left_nav_width');
# Get the global config for lev nav border
$glob_cfg->getConfig('gui_frame_left_nav_border');

$public_ip=$glob_cfg->getConfigValue('spmc_public_ip');
$dietary_public_ip=$glob_cfg->getConfigValue('dietary_public_ip');

$public_ip_spmc = explode(',', $public_ip);


$test = $glob_cfg->getConfig('test_server%');

if (!empty($test)) {
    function url(){
      return sprintf(
        "%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['REQUEST_URI']
      );
    }
    $repoUrl = $glob_cfg->getConfigValue('test_repo_url');
    
    $repoUrl = explode(',', $repoUrl);
    
    $img = $glob_cfg->getConfigValue('test_img_url');
    
    $testImgUrl = url() .$img;

    $repoName = trim($_SERVER['REQUEST_URI'], '/');

    if (in_array($repoName , $repoUrl)) {

        echo '<div style="background:#ED1C24;text-align:center;"> <img src = '.$img.' > </div>';
    }
}

$savelang=0;
/*echo $GLOBALCONFIG['language_non_single'];
while (list($x,$v)=each($GLOBALCONFIG)) echo $x.'==>'.$v.'<br>';
*/
# Start checking language properties
if(!$GLOBALCONFIG['language_single']) {
        # We get the language code
        if($_chg_lang_&&!empty($lang)) {
                $savelang=1;
    }else{
        //echo $lang=$USERCONFIG['lang'];
                if($USERCONFIG['lang']) $lang=$USERCONFIG['lang'];
                    else  include('chklang.php');
     }
}else{

        # If single language is configured, we get the user configured lang
    if(!empty($USERCONFIG['lang']) && file_exists('language/'.$USERCONFIG['lang'].'/lang_'.$USERCONFIG['lang'].'_startframe.php')) {
            $lang=$USERCONFIG['lang'];
    } else {
            # If user config lang is not available, we get the global system lang configuration
            if(!empty($GLOBALCONFIG['language_default']) && file_exists('language/'.$GLOBALCONFIG['language_default'].'/lang_'.$GLOBALCONFIG['language_default'].'_startframe.php')) {
                        $lang=$GLOBALCONFIG['language_default'];
        } else {
                    $lang=LANG_DEFAULT; # Comes from inc_environment_global.php, the last chance, usually set to "en"
            }
    }
}

#
# After having a language code check if the critical scripts exist and set warning
#
$createwarn=file_exists('create_admin.php');
$initwarn=file_exists('./install/initialize.php');
$md5warn=file_exists('./install/encode_pw_md5.php');
$installwarn=file_exists('./install/install.php');
if($createwarn||$installwarn||$md5warn){
    #
    # Load necessary language tables
    #
    $lang_tables[]='create_admin.php';
    include_once('./include/inc_load_lang_tables.php');
    include_once('include/inc_charset_fx.php');
    if($createwarn){
        include('./include/inc_create_admin_warning.php');
    }
    if($initwarn){
        include('./include/inc_init_warning.php');
    }
    if($md5warn){
        include('./include/inc_md5_warning.php');
    }
    if($installwarn){
        include('./include/inc_install_warning.php');
    }
    #
    # exit to avoid running the program
    #
    exit;
}

#
# Prepare language file path
#
$lang_file='language/'.$lang.'/lang_'.$lang.'_startframe.php';

#
# We check if language table exists, if not, english is used
#
if(file_exists($lang_file)) {
        include($lang_file);
} else {
        include('language/en/lang_en_startframe.php');  # en = english is the default language table
    $lang='en';
}

#
# The language detection is finished, we save it to session
#
$HTTP_SESSION_VARS['sess_lang']=$lang;

/*$ck_lang_buffer='ck_lang'.$sid;
setcookie($ck_lang_buffer,$lang);*/

/*$HTTP_COOKIE_VARS[$ck_lang_buffer]=$lang;*/
     //echo $mask;
if((isset($mask)&&$mask)||!$config_exists||$savelang) {
    if(!$config_exists) {

        //$cfg_obj->getConfig('default');
        //$USERCONFIG=&$cfg_obj->buffer;

        configNew($bname,$bversion,$user_id,$ip,$cfgid);

        $USERCONFIG['bname']=$bname;
        $USERCONFIG['bversion']=$bversion;
        $USERCONFIG['cid']=$cfgid;
    }
    // *****************************
    //save browser info to user config array
    // *****************************
    if(empty($ip)) $USERCONFIG['ip']=$REMOTE_ADDR;
    $USERCONFIG['mask']=$mask;
    $USERCONFIG['lang']=$lang;
    if(((($bname=='msie') ||($bname=='opera')) &&($bversion>4)) ||(($bname=='netscape')&&($bversion>3.5)) ||($bname=='mozilla')) {
        $USERCONFIG['dhtml']=1;
    }
    // *****************************
    // Save config to db
    // *****************************
    $mask=$USERCONFIG['mask']; # save mask before serializing
    $cfg_obj->saveConfig($user_id,$USERCONFIG);
    setcookie('ck_config',$user_id,time()+(3600*24*365)); # expires after 1 year
}

#
# save user_id to session
#
$HTTP_SESSION_VARS['sess_user_id']=$user_id;
if(empty($HTTP_SESSION_VARS['sess_user_name'])) $HTTP_SESSION_VARS['sess_user_name']='default';
#
# set the initial session timeout start value
#
$HTTP_SESSION_VARS['sess_tos']=date('His');

#
# Load character set fx
#
include_once('include/inc_charset_fx.php');

#
# Load image fx
#
require_once('include/inc_img_fx.php');

#
# Start smarty templating
#
# Workaround for user config array to work inside the smarty class
#
$cfg = $USERCONFIG;
#print_r($_SESSION);
//while(list($x,$v)=each($cfg)) echo "$x => $v<br>";
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

#
# Window bar title
#
$smarty->assign('sWindowTitle',$LDMainTitle);

#
# Assign the contents frame source
#
$smarty->assign('sContentsFrameSource',"src = \"blank.php?lang=$lang&sid=$sid\"");

#
# Load the gui template
#
//require('gui/html_template/default/tp_index.php');
#
# If the floating menu window is selected
#
if($mask == 2){

    if($lang=='ar'||$lang=='fa') $smarty->assign('sBaseFramesetTemplate','common/frameset_floatingmenu_rtl.tpl');
        else $smarty->assign('sBaseFramesetTemplate','common/frameset_floatingmenu_ltr.tpl');

        $smarty->assign('sMenuFrameSource','src="main/menubar2.php"');
    $smarty->assign('sStartFrameSource',"src=\"main/indexframe.php?boot=1&lang=$lang&egal=$egal&cookie=$cookie&sid=$sid&mask=2\"");

}else{
    $smarty->assign('sStartFrameSource',"src = \"main/indexframe.php?boot=1&mask=$mask&lang=$lang&cookie=$cookie&sid=$sid\"");

    #
    # Assign frame dimensions
    #
    $smarty->assign('gui_frame_left_nav_width',$GLOBALCONFIG['gui_frame_left_nav_width']);
    $smarty->assign('gui_frame_left_nav_border',$GLOBALCONFIG['gui_frame_left_nav_border']);

    if($lang=='ar'||$lang=='fa') {
        $smarty->assign('sBaseFramesetTemplate','common/frameset_rtl.tpl');
        //require('gui/html_template/righttoliftdefault/tp_index.php');
    } else{
        #
        # Else use normal frameset design
        #
        $smarty->assign('sBaseFramesetTemplate','common/frameset_ltr.tpl');
    }
}


$sql="SELECT nr,sort_nr,name,LD_var AS \"LD_var\",url,is_visible FROM care_menu_main WHERE is_visible=1 OR LD_var='LDEDP' OR LD_var='LDLogin' ORDER by sort_nr";

$result=$db->Execute($sql);

if($result){
    #echo '<table CELLPADDING=0 CELLSPACING=0 border=0>';
    $main_menu = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" class=\"moduletable\">
                                                            <tbody>
                                                                <tr align=\"left\">
                                                                    <td height=\"1\">&nbsp;</td>
                                                                </tr>";

    $gui='';
    #$TP_img1= '<img '.createComIcon($root_path,'blue_bullet.gif','0','middle').'>';
    #$TP_com_img_path=$root_path.'gui/img/common';
    $buf='';
    # Load the menu item template
    #$tp =&$TP_obj->load('tp_main_index_menu_item.htm');

    while($menu=$result->FetchRow()){
        if (eregi('LDLogin',$menu['LD_var'])){
            if ($HTTP_COOKIE_VARS['ck_login_logged'.$sid]=='true'){
                $menu['url']='main/logout_confirm.php';
                $menu['LD_var']='LDLogout';
            }
        }
    #   $TP_menu_item='<a href="'.$root_path.$menu['url'].URL_APPEND.'" TARGET="CONTENTS" REL="child">';
        if(isset($$menu['LD_var'])&&!empty($$menu['LD_var'])) $TP_menu_item=$$menu['LD_var'];
            else $TP_menu_item=$menu['name'];
    #   $TP_menu_item='</A>';
        #eval("echo $tp;");

        if ($menu['LD_var'] != 'LDLogin' && $menu['LD_var'] !='LDLogout'){
                /*
                $seg_user = $HTTP_SESSION_VARS['sess_login_userid'];
                include_once($root_path.'include/care_api_classes/class_department.php');
                $dept_obj=new Department;
                $dept_belong = $dept_obj->getUserDeptInfo($seg_user);
                $dept=$dept_belong['id'];
                */

                /* Temporary menu visibility settings */
                $menu_items_allowed = array(
                    "OPD-Triage"=>array("LDHome","LDPatient","LDConsultation","LDDirectory","LDReports","LDSupport","LDSpecials"),
                    "Medocs"=>array("LDHome","LDPatient","LDAdmission","LDDirectory","LDReports","LDSupport","LDSpecials","LDMedocs"),
                    "Admission"=>array("LDHome","LDPatient","LDAdmission","LDDirectory","LDReports","LDSupport","LDSpecials","LDMedocs"),
                    "ER"=>array("LDHome","LDPatient","LDConsultation","LDDirectory","LDReports","LDSupport","LDSpecials","LDMedocs")
                );
                $allow = $menu_items_allowed[$dept];
                # Edited by AJMQ (OCt 19, 2007) - Disallow displaying of Med Depot menu item
                # if ((empty($allow) || (in_array($menu['LD_var'],$allow)))  && $menu['LD_var']!='LDMedDepot')

                $urlAppend = URL_APPEND;
                if ($urlAppend && strpos($menu['url'], '?') !== false) {
                    $urlAppend[0] = '&';
                }

                if($menu['name']=='Dietary'){
                    if(in_array($_SERVER['HTTP_HOST'], $public_ip_spmc)){
                      $dietary_url = $dietary_public_ip;
                    }else{
                          $dietary_url = DIETARY_URL;
                    }

                    if($HTTP_SESSION_VARS['sess_access_token'] != NULL){
                        $main_menu  .= "<tr align=\"left\">
    <td><a href=\"".$dietary_url."/LoginHis/".$HTTP_SESSION_VARS['sess_access_token']."/".$HTTP_SESSION_VARS['sess_token_type']."/".$HTTP_SESSION_VARS['sess_expires_in']."/".$HTTP_SESSION_VARS['sess_login_userid']."\" class=\"mainlevel\" target=\"_blank\">$TP_menu_item</a></td>
            </tr>";
                    }else{
                        #echo $dietary_url;
                      $main_menu  .= "<tr align=\"left\">
    <td><a href=\"".$dietary_url."\" class=\"mainlevel\" target=\"_blank\">$TP_menu_item</a></td>
                    </tr>";
                    }     
                }else{
                       $main_menu  .= "<tr align=\"left\">
                                                                    <td><a href=\"".$root_path.$menu['url'].$urlAppend."\" class=\"mainlevel\" target=\"contframe\">$TP_menu_item</a></td>
                                                                </tr>";
                }
             // print_r($HTTP_SESSION_VARS['sess_access_token']);

        }
    }

$main_menu .= "</tbody>
                                             </table>";
#   echo $gui;

#   echo '</table>';
}
$smarty->assign("appID", onesignal);
$smarty->assign("sMainMenu", $main_menu);
$smarty->assign("notification_token", $_SESSION['token']);
$smarty->assign("notification_socket", $notification_socket);
$smarty->assign("username", $_SESSION['sess_login_userid']);
$smarty->assign("ehr_mobile_host", $ehr_mobile_host);

if ($_REQUEST['start'])
    $smarty->assign('startPage', $_REQUEST['start']);

#
# Display the frame page
#
#$smarty->display('common/baseframe.tpl');
$smarty->display('main/main_index.tpl');
?>