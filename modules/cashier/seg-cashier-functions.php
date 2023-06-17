<?php

require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('LANG_FILE','radio.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');

// reset all 2nd level lock cookies
require($root_path.'include/inc_2level_reset.php');

$thisfile=basename(__FILE__);
$breakfile=$root_path.'main/startframe.php'.URL_APPEND;
$_SESSION['sess_path_referer']=$top_dir.$thisfile;

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# Added for the common header top block
$smarty->assign('sToolbarTitle',"Cashier");

# Added for the common header top block
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDRadio')");

$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('title',"Cashier");

$url = $root_path . 'modules/cashier/seg-cashier-pass.php' .
	URL_APPEND . '&target={target}&userck=' . $userck;

#$url_search_emp = $root_path .'modules/personell_admin/personell_search.php?from=medocs&department=Cashier'; # Added by: syboy 12/18/2015 : meow

$menu = array(
	'Process Payments' => array(
		array(
			'href' => strtr($url, array('{target}' => 'requestlist')),
			'label' => 'Process requests',
			'description' => 'Process payments for hospital cost center requests',
			'icon' => createComIcon($root_path,'cart_go.png','0'),
		),
		array(
			'href' => strtr($url, array('{target}' => 'services')),
			'label' => 'Payments',
			'description' => 'Process payments/collections,',
			'icon' => createComIcon($root_path,'page_green.png','0'),
		),
		array(
			'href' => strtr($url, array('{target}' => 'billinglist')),
			'label' => 'Payward',
			'description' => 'Process hospital billing accounts',
			'icon' => createComIcon($root_path,'door_in.png','0'),
		),
		array(
			'href' => strtr($url, array('{target}' => 'deposit')),
			'label' => 'Deposit',
			'description' => 'Process patient deposits/partial payments',
			'icon' => createComIcon($root_path,'money_add.png','0'),
		),
		array(
			'href' => strtr($url, array('{target}' => 'recent')),
			'label' => 'Recent payments',
			'description' => 'Review your recently processed payments',
			'icon' => createComIcon($root_path,'time.png','0'),
		)
	),

	'Credit Memos' => array(
		array(
			'href' => strtr($url, array('{target}' => 'memonew')),
			'label' => 'Issue credit memo',
			'description' => 'Issue cash voucher for refund',
			'icon' => createComIcon($root_path,'note_edit.png','0'),
		),
		array(
			'href' => strtr($url, array('{target}' => 'memoarchives')),
			'label' => 'Credit memo archives',
			'description' => 'List of issued cash vouchers',
			'icon' => createComIcon($root_path,'folder_find.png','0'),
		),
	),

	'Reports' => array(
		array(
			'href' => strtr($url, array('{target}' => 'reports')),
			'label' => 'Cashier reports',
			'description' => 'Generate cashier reports',
			'icon' => createComIcon($root_path,'report.png','0'),
		),
//added by shandy 08-04-2014
		array(
			//'href' => strtr($url, array('{target}' => 'manual')),
			'href'=>$root_path.'modules/cashier/pdf/CASHIER.pdf'. URL_APPEND."&userck=&target=",
			'label' => 'Users Manual',
			'description' => "PDF Copy of User's Manual",
			'icon' => createComIcon($root_path,'pdf-icon.png','0'),
		),
//added by borj 10-30-2014
		array(
			'href' => strtr($url, array('{target}' => 'JasperReport')),
			'label' => 'Cashier Report Launcher',
			'description' => 'Generate Reports',
			'icon' => createComIcon($root_path,'icon-reports.png','0'),
		),		
	),

	'Administration' => array(
		//Added by: syboy 12/18/2015 : meow
        'sSearchEmp' => array(
            'href' => strtr($url, array('{target}' => 'cas_searchdoctor')),
            'label' => "Search employee",
            'description' => 'Search Active and Inactive employee',
            'icon' => createComIcon($root_path,'lockfolder.gif','0'),
        ),
        // Ended syboy
		array(
			'href' => strtr($url, array('{target}' => 'archives')),
			'label' => 'Payment archives',
			'description' => 'Masterlist of processed cashier payments',
			'icon' => createComIcon($root_path,'folder_page.png','0'),
		),
		array(
			'href' => strtr($url, array('{target}' => 'databank')),
			'label' => 'Payment items manager',
			'description' => 'Databank for miscellaneous payments/hospital services',
			'icon' => createComIcon($root_path,'wrench.png','0'),
		),
		array(
			'href' => strtr($url, array('{target}' => 'assignorno')),
			'label' => 'Assign OR series',
			'description' => 'Assign range of OR numbers to specific encoders',
			'icon' => createComIcon($root_path,'table_go.png','0'),
		),
		array(
			'href' => strtr($url, array('{target}' => 'editorno')),
			'label' => 'Edit OR numbers',
			'description' => 'Bulk editing of OR numbers',
			'icon' => createComIcon($root_path,'page_edit.png','0'),
		),
		array(
			'href' => strtr($url, array('{target}' => 'setupprinter')),
			'label' => 'Setup printer',
			'description' => 'Add, update, delete printers',
			'icon' => createComIcon($root_path,'printer.png','0'),
		),
	)
);
$smarty->assign('aMenu', $menu);

# Assign the submenu to the mainframe center block
$smarty->assign('sMainBlockIncludeFile','common/basemenu.tpl');

$smarty->display('common/mainframe.tpl');

