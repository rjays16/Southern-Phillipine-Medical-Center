<?php
//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','startframe.php');
define('NO_CHAIN',1);
require_once($root_path.'include/inc_front_chain_lang.php');
global $db;
$curdate = date('Y-m-d h:i A');
$update_db = $db->Execute("UPDATE seg_notice_tbl AS snt
	SET snt.status='0'
		WHERE CONCAT(snt.`note_date`,' ',snt.`time_to`) < NOW();");

$cksid='ck_sid'.$sid;
#added by VAN 120707
/*
if ($_GET['device']!=1){
#q20AAA==#3N1yuyekL7mtZpSXHZDH9wRsenPemLSVbnQ9n9zrk2E=
	$HTTP_COOKIE_VARS[$cksid] = "q20AAA==#3N1yuyekL7mtZpSXHZDH9wRsenPemLSVbnQ9n9zrk2E=";
	$cookie = "1";
}
*/

if ($_GET['device']!=1){
	if(!$HTTP_COOKIE_VARS[$cksid] && !$cookie) { header("location:".$root_path."cookies.php?lang=$lang&startframe=1"); exit;}
}

if(!session_is_registered('sess_news_nr')) session_register('sess_news_nr');

# added by VAN 
$readerpath='headline-read.php?sid='.$sid.'&lang='.$lang;	
# reset all 2nd level lock cookies
require($root_path.'include/inc_2level_reset.php');
		
$dept_nr=1; # 1 = press relations

# Get the maximum number of headlines to be displayed
$config_type='news_headline_max_display';
include($root_path.'include/inc_get_global_config.php');

if(!isset($news_headline_max_display)||!$news_headline_max_display) $news_num_stop=3; # default is 3 
    else $news_num_stop=$news_headline_max_display;  # The maximum number of news article to be displayed
	
//include($root_path.'include/inc_news_get.php'); // now get the current news
$thisfile=basename(__FILE__);
require_once($root_path.'include/care_api_classes/class_news.php');
$newsobj=new News;
$news=&$newsobj->getHeadlinesPreview($dept_nr,$news_num_stop);

# Set initial session environment for this module

if(!session_is_registered('sess_file_editor')) session_register('sess_file_editor');
if(!session_is_registered('sess_file_reader')) session_register('sess_file_reader');

$HTTP_SESSION_VARS['sess_file_break']=$top_dir.$thisfile;
$HTTP_SESSION_VARS['sess_file_return']=$top_dir.$thisfile;
$HTTP_SESSION_VARS['sess_file_editor']='headline-edit-select-art.php';
$HTTP_SESSION_VARS['sess_file_reader']='headline-read.php';
$HTTP_SESSION_VARS['sess_dept_nr']='1'; // 1= press relations dept
$HTTP_SESSION_VARS['sess_title']=$LDEditTitle.'::'.$LDSubmitNews;
$HTTP_SESSION_VARS['sess_user_origin']='main_start';
$HTTP_SESSION_VARS['sess_path_referer']=$top_dir.$thisfile;

# added by VAN 
if ($_GET['device']==1)
	$readerpath='headline-read.php'.URL_APPEND.'&device=1';
else
	$readerpath='headline-read.php'.URL_APPEND;	
# Load the news display configs
require_once($root_path.'include/inc_news_display_config.php');

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Hide the title bar
 $smarty->assign('bHideTitleBar',TRUE);

 # added by VAN
 if ($_GET['device']==1)	
		$LDPageTitle = "SegHIS Doctor Dashboard";

 # Window title
 $smarty->assign('title',$LDPageTitle);
 
   if ($_GET['device']==1){
  	?>
		<link media="all, handheld" rel="stylesheet" href="../../doctor/default.css" type="text/css">	
		<!--<img src="../../doctor/images/seghis_logo.jpg">-->
		<!--<img src="../../doctor/images/seghis_logo.jpg" height="30">-->
		<img src="../../doctor/images/seghis_logo.jpg" height="30" width="100" align="absmiddle" alt="seghis logo">
		<!--<img src="../../doctor/images/segworks.gif" height="30" width="50" align="absmiddle" alt="seg logo">-->
		<br>
		<!--&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-->
		<span class="reg3link"><img src="../../doctor/images/back.gif">&nbsp;<a href="../../doctor/welcome.php">MAIN PAGE</a></span>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<span class="reg3link"><img src="../../doctor/images/redflag.gif">&nbsp;<a href="../../doctor/index.php?logout=1">LOGOUT</a></span>

		<h4><font color="blue">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NEWS</font></h4>
	<?php
  }


 $smarty->assign('news_normal_display_width',$news_normal_display_width);

 # Headline title
 $smarty->assign('LDHeadline',$LDHeadline);

#added by VAN
#echo "<br>start page <br>";
#print_r($HTTP_SESSION_VARS);

 #Collect html code

  /**
 * Routine to display the headlines
 */
for($j=1;$j<=$news_num_stop;$j++){

	$picalign=($j==2)? 'right' : 'left';

	 ob_start();
		include($root_path.'include/inc_news_preview.php');
		($j==2)? $smarty->display('news/headline_newslist_item2.tpl') : $smarty->display('news/headline_newslist_item.tpl');
		$sTemp = ob_get_contents();
	ob_end_clean();
	
	$smarty->assign('sNews_'.$j,$sTemp);
}

# Collect html for the submenu blocks

ob_start();

	#added by VAN 12-06-07
	if ($_GET['device']!=1)
		include($root_path.'include/inc_rightcolumn_menu.php');

	# Stop buffering, get contents

	$sTemp = ob_get_contents();
ob_end_clean();

# assign contents to subframe

$smarty->assign('sSubMenuBlock',$sTemp);

# Assign the subframe template file name to mainframe

$smarty->assign('sMainBlockIncludeFile','news/headline.tpl');

  /**
 * show Template
 */

 $smarty->display('common/mainframe.tpl');
 
// require($root_path.'js/floatscroll.js');
 
?>
