<?php
//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path . 'include/inc_environment_global.php');
require($root_path . 'include/care_api_classes/class_personell.php');
/**
 * CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
 * GNU General Public License
 * Copyright 2002,2003,2004,2005 Elpidio Latorilla
 * elpidio@care2x.org,
 *
 * See the file "copy_notice.txt" for the licence notice
 */
define('LANG_FILE', 'doctors.php');
define('NO_2LEVEL_CHK', 1);
require_once($root_path . 'include/inc_front_chain_lang.php');
require($root_path . 'include/inc_2level_reset.php');

if (!session_is_registered('sess_path_referer')) session_register('sess_path_referer');
$breakfile = $root_path . 'main/startframe.php' . URL_APPEND;

$HTTP_SESSION_VARS['sess_path_referer'] = $top_dir . basename(__FILE__);
# Erase the cookie 
if (isset($HTTP_COOKIE_VARS['ck_doctors_dienstplan_user' . $sid])) setcookie('ck_doctors_dienstplan_user' . $sid, '', 0, '/');
# erase the user_origin 
if (isset($HTTP_SESSION_VARS['sess_user_origin'])) $HTTP_SESSION_VARS['sess_user_origin'] = '';

# Start Smarty templating here
/**
 * LOAD Smarty
 */

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once($root_path . 'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# Create a helper smarty object without reinitializing the GUI
$smarty2 = new smarty_care('common', FALSE);

# Added for the common header top block
$smarty->assign('sToolbarTitle', $LDDoctors);

//-----added 2007-10-03 FDP
# Hide the return button
$smarty->assign('pbBack', FALSE);
//-------------------------

# href for the help button
$smarty->assign('pbHelp', "javascript:gethelp('submenu1.php','$LDDoctors')");

# href for the close button
$smarty->assign('breakfile', $breakfile);

# Window bar title
$smarty->assign('title', $LDDoctors);

//added by Nick 3-27-2015
$url = $root_path . 'modules/doctors/doctors-main-pass.php' .
    URL_APPEND . '&target={target}&retpath={retpath}&userck=' . $userck;

//added by carriane 07/13/17
require_once($root_path.'include/care_api_classes/class_acl.php');
$objAcl = new Acl($_SESSION['sess_temp_userid']);

$showList = $objAcl->checkPermissionRaw('_a_1_showalldept');
//end carriane
$has_all_persmission = $objAcl->checkPermissionRaw('_a_0_all');

$with_permission = $objAcl->checkPermissionRaw('_a_1_doctorsreportlauncher');

#$url_search_emp = $root_path .'modules/personell_admin/personell_search.php?from=medocs&department=Doctors'; # Added by: syboy 12/18/2015 : meow
//modified by Nick 3-27-2015
//added by art 10/13/2014
$personnel = new Personell();
$is_doctor = $personnel->isDoctor($_SESSION['sess_login_personell_nr']);
#udpated by carriane 07/13/17
if ($is_doctor || $showList || $with_permission || $has_all_persmission) {
    $personnelAssignment = $personnel->get_Dept_name($_SESSION['sess_login_personell_nr']);

    if($is_doctor || $has_all_persmission){
        $dashboardLink = $root_path.'modules/dashboard/dashboard.php'.URL_APPEND;
    }
    else{
        $dashboardLink = 'javascript:alert(\'Dashboard is only accessible for doctors account only!\')';
    }
    if($with_permission || $personnelAssignment){
        $reportsLink = strtr($url,array('{target}'=>'report-launcher','{retpath}'=>'menu'));
    }
    else
        $reportsLink = 'javascript:alert(\'Reports is only accessible for doctors with department only!\')';
} else {
    $dashboardLink = 'javascript:alert(\'Dashboard is only accessible for doctors account only!\')';
    $reportsLink = 'javascript:alert(\'Reports is only accessible for doctors account only!\')';
}
#end raymond
//end art

//added by Nick 3-27-2015
$menu = array(
    'Doctor\'s Module' => array(
        array(
            'href' => $dashboardLink,
            'target' => '_new',
            'label' => 'Doctor\'s Dashboard',
            'description' => 'Doctor\'s Dashboard',
            'icon' => createComIcon($root_path,'bug.png','0'),
        ),
        array(
            'href' => 'doctors-dienst-schnellsicht.php' . URL_APPEND . '&retpath=docs',
            'label' => $LDQView,
            'description' => $LDDOCSTxt,
            'icon' => createComIcon($root_path,'eye_s.gif','0'),
        ),
        array(
            'href' => strtr($url,array('{target}'=>'dutyplan','{retpath}'=>'menu')),
            'label' => $LDDOCS,
            'description' => $LDDocsForumTxt,
            'icon' => createComIcon($root_path,'post_discussion.gif','0'),
        ),
        array(
            'href' => strtr($url,array('{target}'=>'setpersonal','{retpath}'=>'menu')),
            'label' => $LDDocsList,
            'description' => 'View or Modify the list of doctors in a department',
            'icon' => createComIcon($root_path,'forums.gif','0'),
        ),
        # Added by: syboy 12/18/2015 : meow
        // array(
        //     'href' => strtr($url,array('{target}'=>'setsearchdoctor','{retpath}'=>'menu')),
        //     'label' => "Search employee",
        //     'description' => 'Search Active and Inactive employee',
        //     'icon' => createComIcon($root_path,'lockfolder.gif','0'),
        // ),
        # Ended syboy
        array(
            'href' => $root_path . 'modules/news/newscolumns.php' . URL_APPEND . '&dept_nr=37&user_origin=dept',
            'label' => $LDNews,
            'description' => $LDNewsTxt,
            'icon' => createComIcon($root_path,'bubble.gif','0'),
        ),
        array(
            'href' => $reportsLink,
            'label' => 'Doctors Report Launcher',
            'description' => 'View reports',
            'icon' => createComIcon($root_path,'icon-reports.png','0'),
        ),
        array(
            'href' => $root_path . 'forms/DDManual2020.pdf' . URL_APPEND . '&dept_nr=37&user_origin=dept',
            'label' => $LDUser,
            'description' => $LDPdfTxt,
            'icon' => createComIcon($root_path,'pdf-icon.png','0'),
        ),
        array(
            'href' => $root_path . 'forms/EHR.pdf' . URL_APPEND . '&dept_nr=37&user_origin=dept',
            'label' => 'EHR Quick Guide',
            'description' => 'PDF Copy of EHR Quick Guide',
            'icon' => createComIcon($root_path, 'pdf-icon.png', '0'),
        ),
    )
);
$smarty->assign('aMenu', $menu);
$smarty->assign('sMainBlockIncludeFile','common/basemenu.tpl');
//end Nick

//commented by Nick 3-27-2015
//$aSubMenuIcon = array(
//    createComIcon($root_path, 'bug.png', '0'),
//    createComIcon($root_path, 'eye_s.gif', '0'),
//    createComIcon($root_path, 'post_discussion.gif', '0'),
//    createComIcon($root_path, 'forums.gif', '0'),
//    createComIcon($root_path, 'bubble.gif', '0'),
//    createComIcon($root_path, 'pdf-icon.png', '0')
//);
//
//$aSubMenuText = array(
//    'Doctor\'s Dashboard',
//    $LDDOCSTxt,
//    $LDDocsForumTxt,
//    $LDNewsTxt,
//    $LDNewsTxt,
//    $LDPdfTxt
//);
//
//#added by art 10/13/2014
//$personell = new Personell();
//$is_doctor = $personell->isDoctor($_SESSION['sess_login_personell_nr']);
//if ($is_doctor) {
//    $dash = '<a target="_new" href="../../modules/dashboard/dashboard.php' . URL_APPEND . '">Doctor\'s Dashboard</a>';
//} else {
//    $dash = '<span style="color:#000066;cursor: pointer;" onclick="alert(\'Dashboard is only accessible for doctors account only!\');">Doctor\'s Dashboard</a>';
//}
//#end art
//
//$aSubMenuItem = array(
//    #'LDDD' => '<a target="_new" href="../../modules/dashboard/dashboard.php'.URL_APPEND.'">Doctor\'s Dashboard</a>',
//    'LDDD' => $dash,
//    'LDQViewTxt' => '<a href="doctors-dienst-schnellsicht.php' . URL_APPEND . '&retpath=docs">' . $LDQView . '</a>',
//    'LDDutyPlanTxt' => '<a href="doctors-main-pass.php' . URL_APPEND . '&target=dutyplan&retpath=menu">' . $LDDOCS . '</a>',
//    'LDDocsForumTxt' => '<a href="doctors-main-pass.php' . URL_APPEND . '&target=setpersonal&retpath=menu">' . $LDDocsList . '</a>',
//    'LDNewsTxt' => '<a href="' . $root_path . 'modules/news/newscolumns.php' . URL_APPEND . '&dept_nr=37&user_origin=dept">' . $LDNews . '</a>',
//    'LDPdfTxt' => '<a href="' . $root_path . 'forms/DOCTORS.pdf' . URL_APPEND . '&dept_nr=37&user_origin=dept">' . $LDUser . '</a>',
//);
//
//$iRunner = 0;
//
//while (list($x, $v) = each($aSubMenuItem)) {
//    $sTemp = '';
//    ob_start();
//    if ($cfg['icons'] != 'no_icon') $smarty2->assign('sIconImg', '<img ' . $aSubMenuIcon[$iRunner] . '>');
//    $smarty2->assign('sSubMenuItem', $v);
//    $smarty2->assign('sSubMenuText', $aSubMenuText[$iRunner]);
//    $smarty2->display('common/submenu_row.tpl');
//    $sTemp = ob_get_contents();
//    ob_end_clean();
//    $iRunner++;
//    $smarty->assign($x, $sTemp);
//}
//
//$smarty->assign('sMainBlockIncludeFile', 'doctors/submenu_doctors.tpl');
$smarty->display('common/mainframe.tpl');