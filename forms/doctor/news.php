<?php
		
//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('roots.php');
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

$cksid='ck_sid'.$sid;
if(!$HTTP_COOKIE_VARS[$cksid] && !$cookie) { header("location:".$root_path."cookies.php?lang=$lang&startframe=1"); exit;}

if(!session_is_registered('sess_news_nr')) session_register('sess_news_nr');

$readerpath='headline-read.php?sid='.$sid.'&lang='.$lang;
# reset all 2nd level lock cookies
require($root_path.'include/inc_2level_reset.php');
?>
<img src="images/seghis_logo.jpg">
<?php
	if (isset($_SESSION['sid'])){
?>
<br>
<span class="reglink"><a href="index.php?logout=1">LOGOUT</a></span>
<h3><font color="blue">NEWS</font></h3>

<?php		
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

 # Window title
 $smarty->assign('title',$LDPageTitle);

 $smarty->assign('news_normal_display_width',$news_normal_display_width);

 # Headline title
 $smarty->assign('LDHeadline',$LDHeadline);

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

	#include($root_path.'include/inc_rightcolumn_menu.php');

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
 }else{
		echo "<p id=\"screen\">Session time out.";
		?>
		&nbsp;&nbsp;<img src="images/lockfolder.gif">
		<?php
		echo "</p>";
		echo "<span class=\"reg3link\"><a href=\"index.php\">LOGIN</a></span>";
 }
?>