<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/industrial_clinic/ajax/agency_mgr.common.php');
require_once($root_path.'include/care_api_classes/industrial_clinic/class_agency_mgr.php');

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','lab.php');

$local_user='ck_dialysis_user';
require_once $root_path.'include/inc_front_chain_lang.php';

# Create products object
$GLOBAL_CONFIG=array();

# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

if (!$_GET['from'])
	$breakfile=$root_path."modules/industrial_clinic/seg-industrial_clinic-functions.php".URL_APPEND;
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile=$root_path."modules/industrial_clinic/seg-industrial_clinic-functions.php".URL_APPEND;
}

$thisfile='seg-ic-agency-details.php';

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once $root_path.'gui/smarty_template/smarty_care.class.php';
$smarty = new smarty_care('common');

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

# href for the close button
$smarty->assign('breakfile',$breakfile);
$title = "Industrial Clinic :: Agency Manager";

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);

#save data here
/*if(isset($_POST['submitted'])){
	$data = array(
		'name'=>$_POST['agency_name'],
		'address'=>$_POST['agency_address'],
		'contact_number'=>$_POST['agency_contact'],
		'short_name'=>$_POST['agency_sname'],
		'president'=>$_POST['agency_president'],
		'hr_manager'=>$_POST['agency_hr'],
		'account_no'=>$_POST['agency_account']
	);
	if()
}*/

$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);
# Collect javascript code
ob_start();
	 # Load the javascript code
?>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?=$root_path?>js/jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>


<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/listgen/listgen.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css"/>
<script type="text/javascript" src="<?= $root_path ?>modules/industrial_clinic/js/seg-ic-company-details.js" ></script>

<script type="text/javascript">
var $J = jQuery.noConflict();

function initialize()
{
	load_employee_list();
	load_company_employees();  // #for billing added by angelo
	load_service_list();
	load_company_items_list();
	load_package_list();
	load_package_service_list();
	load_other_company_packages();
}

function load_employee_list()
{
	ListGen.create($('member-list'),{
		id: 'member',
		url: '<?=$root_path?>modules/industrial_clinic/ajax/ajax_list_agency_members.php',
		params: {'agency_id':$('agency_id').value, 'search_person':$('psearch').value},
		width: 'auto',
		height: 'auto',
		columnModel: [
			{
				name: 'patient_id',
				label: 'Patient ID',
				width: 70,
				sortable: true,
				sorting: ListGen.SORTING.asc,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '11',
					fontWeight: 'bold'
				}
			},
			{
				name: 'patient_name',
				label: 'Name',
				width: 135,
				sortable: true,
				sorting: ListGen.SORTING.asc,
				styles: {
					color: '#660000',
					font: 'Tahoma',
					fontSize: '11'
				}
			},
			{
				name: 'patient_bdate',
				label: 'Birthdate',
				width: 80,
				sortable: false,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '11'
				}
			},
			{
				name: 'patient_age',
				label: 'Age',
				width: 60,
				sortable: false,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '11',
					textAlign: 'center'
				}
			},
			{
				name: 'patient_sex',
				label: 'Sex',
				width: 70,
				sortable: true,
				sorting: ListGen.SORTING.asc,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '11',
					textAlign: 'center'
				}
			},
			{
				name: 'patient_status',
				label: 'Civil Status',
				width: 80,
				sortable: false,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '11',
					textAlign: 'center'
				}
			},
			{
				name: 'options',
				label: 'Options',
				width: 200,
				sortable: false
			}
		]
	});
}

function load_service_list()
{
	ListGen.create($('service-list'),{
		id: 'service',
		url: '<?=$root_path?>modules/industrial_clinic/ajax/ajax_list_services.php',
		params: {'cost_center':$('service_area').value, 'search_key':$('search_service').value, 'mode':'package'},
		width: 'auto',
		height: 100,
		columnModel: [
			{
				name: 'item_code',
				label: 'Item Code',
				width: 100,
				sortable: true,
				sorting: ListGen.SORTING.none,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '11',
					fontWeight: 'bold'
				}
			},
			{
				name: 'item_name',
				label: 'Item Name',
				width: 250,
				sortable: true,
				sorting: ListGen.SORTING.asc,
				styles: {
					color: '#660000',
					font: 'Tahoma',
					fontSize: '12'
				}
			},
			{
				name: 'item_price',
				label: 'Item Price',
				width: 150,
				sortable: false,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '12',
					fontWeight: 'bold'
				}
			},
			{
				name: 'options',
				label: 'Options',
				width: 100,
				sortable: false
			}
		]
	});
}

function load_company_items_list()
{
	 ListGen.create($('company-items-list'),{
		id: 'company_items',
		url: '<?=$root_path?>modules/industrial_clinic/ajax/ajax_list_company_prices.php',
		params: {'company_id':$('company_id').value, 'search_key':$('search_comp_item').value},
		width: 'auto',
		height: 100,
		columnModel: [
			{
				name: 'item_code',
				label: 'Item Code',
				width: 100,
				sortable: true,
				sorting: ListGen.SORTING.none,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '11',
					fontWeight: 'bold'
				}
			},
			{
				name: 'item_name',
				label: 'Item Name',
				width: 250,
				sortable: true,
				sorting: ListGen.SORTING.asc,
				styles: {
					color: '#660000',
					font: 'Tahoma',
					fontSize: '12'
				}
			},
			{
				name: 'item_area',
				label: 'Area',
				width: 70,
				sortable: true,
				sorting: ListGen.SORTING.none,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '12',
					textAlign: 'center'
				}
			},
			{
				name: 'item_price',
				label: 'Item Price',
				width: 150,
				sortable: false,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '12',
					fontWeight: 'bold'
				}
			},
			{
				name: 'options',
				label: 'Options',
				width: 70,
				sortable: false,
				styles: {
					textAlign: 'center'
				}
			}
		]
	});
}

function load_package_list()
{
	ListGen.create( $('packages-list'), {
		id: 'addpackage',
		url: '<?=$root_path?>modules/industrial_clinic/ajax/ajax_list_company_packages.php',
		params: {'company_id':$('company_id').value, 'search_key':$('package_search').value, 'mode':'editpackage'},
		width: 'auto',
		height: 'auto',
		autoLoad: true,
		effects: true,
		rowHeight: 30,
		columnModel: [
			{
				name: 'package_name',
				label: 'Name',
				width: 250,
				sortable: true,
				sorting: ListGen.SORTING.asc
			},
			{
				name: 'package_price',
				label: 'Price',
				width: 100,
				sortable: false
			},
			{
				name: 'options',
				label: 'Options',
				width: 250,
				sortable: false
			}
		]
	});
}

function load_package_service_list()
{
	ListGen.create($('service-package-list'),{
		id: 'servicepackage',
		url: '<?=$root_path?>modules/industrial_clinic/ajax/ajax_list_services.php',
		params: {'cost_center':$('service_area').value, 'search_key':$('search_service').value, 'mode':'package'},
		autoLoad: true,
		effects: true,
		width: 'auto',
		height: 150,
		columnModel: [
			{
				name: 'item_code',
				label: 'Item Code',
				width: 100,
				sortable: true,
				sorting: ListGen.SORTING.none,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '11',
					fontWeight: 'bold',
					textAlign: 'center'
				}
			},
			{
				name: 'item_name',
				label: 'Item Name',
				width: 200,
				sortable: true,
				sorting: ListGen.SORTING.asc,
				styles: {
					color: '#660000',
					font: 'Tahoma',
					fontSize: '12'
				}
			},
			{
				name: 'item_price',
				label: 'Price',
				width: 100,
				sortable: false,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '12',
					fontWeight: 'bold',
					textAlign: 'right'
				}
			},
			{
				name: 'options',
				label: 'Options',
				width: 80,
				sortable: false,
				styles: {
					textAlign: 'center'
				}
			}
		]
	});
}

function load_other_company_packages()
{
	ListGen.create( $('other-package-list'), {
		id: 'otherpackage',
		url: '<?=$root_path?>modules/industrial_clinic/ajax/ajax_list_company_packages.php',
		params: {'company_id':$('company_list').value, 'search_key':$('search_package_service').value, 'mode':'otherpackage'},
		width: 'auto',
		height: 'auto',
		autoLoad: true,
		effects: true,
		rowHeight: 30,
		columnModel: [
			{
				name: 'package_name',
				label: 'Name',
				width: 250,
				sortable: true,
				sorting: ListGen.SORTING.asc
			},
			{
				name: 'package_price',
				label: 'Price',
				width: 100,
				sortable: false
			},
			{
				name: 'options',
				label: 'Options',
				width: 250,
				sortable: false
			}
		]
	});
}

function load_company_employees()
{

	ListGen.create($('employee-list'),{
		id: 'member',
		url: '<?=$root_path?>modules/industrial_clinic/seg-ic-billing-employee-list.php',
		params: {'agency_id':$('agency_id').value, 'search_person':$('psearch').value},
		width: 625,
		height: 200,
		columnModel: [
			{
				name: 'patient_id',
				label: 'Patient ID',
				width: 70,
				sortable: true,
				sorting: ListGen.SORTING.asc,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '11',
					fontWeight: 'bold'
				}
			},
			{
				name: 'patient_name',
				label: 'Employee Name',
				width: 225,
				sortable: true,
				sorting: ListGen.SORTING.asc,
				styles: {
					color: '#660000',
					font: 'Tahoma',
					fontSize: '11'
				}
			},
			{
				name: 'patient_bdate',
				label: 'Birthdate',
				width: 110,
				sortable: false,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '11'
				}
			},
			{
				name: 'patient_age',
				label: 'Age',
				width: 60,
				sortable: false,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '11',
					textAlign: 'center'
				}
			},
			{
				name: 'patient_sex',
				label: 'Sex',
				width: 70,
				sortable: true,
				sorting: ListGen.SORTING.asc,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '11',
					textAlign: 'center'
				}
			},
			{
				name: 'patient_status',
				label: 'Civil Status',
				width: 90,
				sortable: false,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '11',
					textAlign: 'center'
				}
			}
		]
	});
}


function listServices()
{
	$('service-list').list.params = {'cost_center':$('service_area').value, 'search_key':$('search_service').value, 'mode':'service'};
	$('service-list').list.refresh();
}

function listCompanyServices()
{
	$('company-items-list').list.params = {'company_id':$('company_id').value, 'search_key':$('search_comp_item').value};
	$('company-items-list').list.refresh();
}

function listPackageServices()
{
	$('service-package-list').list.params = {'cost_center':$('package_service_area').value, 'search_key':$('search_package_service').value, 'mode':'package'};
	$('service-package-list').list.refresh();
}

function listCompanyPackages()
{
	$('packages-list').list.params = {'company_id':$('company_id').value, 'search_key':$('package_search').value,'mode':'editpackage'};
	$('packages-list').list.refresh();
}

function listOtherCompanyPackages()
{
	$('other-package-list').list.params = {'company_id':$('company_list').value, 'search_key':$('search_other_package').value, 'mode':'otherpackage'};
	$('other-package-list').list.refresh();
}

function saveServicePriceToCompany(code, area)
{
	if(!key_check($('item_price'+code).value, 'item_price'+code)) {
		return false;
	} else {
		var rep = confirm("Save new price for this item?")
		if(rep) {
			var new_price = $('item_price'+code).value;
			//alert($('company_id').value+" | "+code+" | "+area+" | "+new_price)
			xajax_saveServicePriceToCompany($('company_id').value, code, area, new_price);
		}
	}
}

function editServicePriceToCompany(code, area)
{
	if(!key_check($('comp_item_price'+code).value, 'comp_item_price'+code)) {
		return false;
	} else {
		var rep = confirm("Edit price for this item?")
		if(rep) {
			var new_price = $('comp_item_price'+code).value;
			xajax_saveServicePriceToCompany($('company_id').value, code, area, new_price);
		}
	}
}

function deleteServicePriceToCompany(code, area)
{
	var rep = confirm("Delete item from list?")
	if(rep) {
		xajax_deleteServicePriceToCompany($('company_id').value, code, area);
	}
}

function validate() {

	if($('agency_name').value=="")
	{
		alert("Please provide the agency name.");
		$('agency_name').focus();
		return false;
	}
	return true;
}

function outputResponse(rep)
{
	alert(rep)
	window.parent.$('agency-list').list.refresh();
	window.parent.cClick();
}

function openPatientSelect() {
	if ($('select-enc').hasClassName('disabled')) return false;
<?php
$var_arr = array(
	"var_pid"=>"pid",
	"var_encounter_nr"=>"encounter_nr",
	"var_include_walkin"=>"0",
	"var_reg_walkin"=>"0",
);
$vas = array();
foreach($var_arr as $i=>$v) {
	$vars[] = "$i=$v";
}
$var_qry = implode("&",$vars);
?>
	overlib(
			OLiframeContent('seg-ic-assign-member.php?<?=$var_qry?>&var_include_enc=0&agency_id='+$('agency_id').value,
			750, 450, 'fSelEnc', 0, 'no'),
			WIDTH,750, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?=$root_path?>/images/close_red.gif border=0 >',
			CAPTIONPADDING,2,
			CAPTION,'Select registered person',
			MIDX,0, MIDY,0,
			STATUS,'Select registered person');
	return false;
}

function deleteAgencyMember(pid, nr)
{
	var rep = confirm("Delete patient membership from this agency?")
	if(rep) {
		xajax_deleteAgencyMember(pid, nr);
	}
}

function editEmployeeDetails(pid, nr)
{
	return overlib(
	OLiframeContent('seg-ic-assign-member-gui.php?pid='+pid+'&company_id='+nr+'&mode=update',
								400, 200, 'fGroupTray', 0, 'no'),
								WIDTH,400, TEXTPADDING,0, BORDER,0,
									STICKY, SCROLL, CLOSECLICK, MODAL,
									CLOSETEXT, '<img src=../../images/close_red.gif border=0>',
							 CAPTIONPADDING,2, CAPTION,'Update Employee Details',
							 MIDX,0, MIDY,0,
							 STATUS,'Update Employee Details');
}

function refreshlist(rep)
{
	alert(rep)
	$('member-list').list.refresh();
}

function searchEmployee()
{
	$('member-list').list.params={'agency_id':$('agency_id').value, 'search_person':$('psearch').value};
	$('member-list').list.refresh();
}

function updateAgency()
{
	var rep = confirm("Update agency details?");
	if(rep) {
		if(validate()) {
			var data = [];
			data['name'] = $('agency_name').value;
			data['address'] = $('agency_address').value;
			data['contact_no'] = $('agency_contact').value;
			data['short_id'] = $('agency_sname').value;
			data['president'] = $('agency_president').value;
			data['hr_manager'] = $('agency_hr').value;
			data['hosp_acct_no'] = $('agency_account').value;
			xajax_updateAgency(data,$('agency_id').value);
		}
	}
	return false;
}

//search wizard
function SearchName(){
		return overlib(
			OLiframeContent('../../modules/industrial_clinic/seg-ic-billing-name-select.php?company_id='+$('company_id').value, 600, 410, 'fOrderTray', 0, 'auto'),
				WIDTH,600, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, '<img src=../..//images/close_red.gif border=0 >',
				CAPTIONPADDING,2,
				CAPTION,'Person Name',
				MIDX,0, MIDY,0,
				STATUS,'Run Person Name');

}

//added code by angelo m. 08.24.2010
//start
function openReport(){

	var url="<?=$root_path?>"+"modules/industrial_clinic/";
	var params="";

	url =url+ "seg-ic-consolidated-print-out.php";
	params ="pid="+$('txtPid').value+"&company_id="+$('company_id').value+"&date_from="+$('searchDteStart').value+"&date_to="+$('searchDteEnd').value;
	window.open(url+"?"+params,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
}
//end

function addPackageItems()
{
	$J('#add-package').dialog('open');
}

function otherCompanyPackages()
{
	$J('#other-packages').dialog('open');
}

function addItemToList(code,name,area)
{
	var tableId = $('packagelist');

	if($('package_item_area'+code)) {
	 alert("Item already added to package list.")
	 return false;
	} else {
	 if(tableId)
	 {
			var dBody=tableId.select("tbody")[0];
			if(dBody){
				var table1 = $('packagelist').getElementsByTagName('tbody').item(0);
				if ($('row_empty')) {
					table1.removeChild($('row_empty'));
				}
				var dRows = dBody.getElementsByTagName("tr");
				if(code)
				{
					alt = (dRows.length%2>0) ? ' class="alt"':''

					rowSrc = '<tr class="'+alt+'" id="package'+code+'">'+
							'<td width="20%" align="center">'+
								'<span style="color:#000000;font-weight:bold">'+code+'</span>'+
								'<input type="hidden" id="package_item_code'+code+'" name="package_items_code[]" value="'+code+'"/>'+
							'</td>'+
							'<td width="*">'+
								'<span style="color:#660000;font-weight:bold">'+name.toUpperCase()+'</span>'+
							'</td>'+
							'<td width="20%" align="center">'+
								'<span style="color:#000000;font-weight:bold">'+area.toUpperCase()+'</span>'+
								'<input type="hidden" id="package_item_area'+code+'" name="package_items_area[]" value="'+area+'"/>'+
							'</td>'+
							'<td width="5%" align="center">'+
								'<img class="link" src="<?=$root_path?>images/cashier_delete.gif" border="0" onclick="remove_item(\''+code+'\');return false;"/>'+
							'</td>'+
						'</tr>';
				}else
				{
					rowSrc = '<tr id="row_empty"><td colspan="6">No items added...</td></tr>';
				}
				dBody.insert(rowSrc);
			}
	 }
 }
}

function remove_item(id)
{
	var rep = confirm("Delete this item from package list?");
	if(rep) {
		var table = $('packagelist').getElementsByTagName('tbody').item(0);
		table.removeChild($('package'+id));

		if (!document.getElementsByName('package_items_code[]') || document.getElementsByName('package_items_code[]').length <= 0) {
			append_empty_list();
		}
	}
	return false;
}

function append_empty_list()
{
	var table = $('packagelist').getElementsByTagName('tbody').item(0);
	var row = document.createElement("tr");
	var cell = document.createElement("td");
	row.id = "row_empty";
	cell.appendChild(document.createTextNode('No items added...'));

	cell.colSpan = "6";
	row.appendChild(cell);
	$('packagelist').getElementsByTagName('tbody').item(0).appendChild(row);
}

function clearList(listID)
{
	var list=$(listID),dRows, dBody;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			dBody.innerHTML = "";
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

function key_check(val, id)
{
	if(isNaN(parseFloatEx(val)) || parseFloatEx(val)<0) {
		alert("Invalid input.");
		$(id).focus();
		return false;
	}
	$(id).value = parseFloatEx(val);
	return true;
}

function parseFloatEx(x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)
}

function savePackage(mode)
{
	var items = document.getElementsByName('package_items_code[]');
	if($('package_name').value=="") {
		alert("Please provide the package name.");
		$('package_name').focus();
		return false;
	}
	else if ($('package_price').value=="") {
		alert("Please provide the package price.");
		$('package_price').focus();
		return false;
	}
	else if(!key_check($('package_price').value, 'package_price')) {
		return false;
	}
	else if(items.length <= 0) {
		alert("Please add items to this package.");
		return false;
	}
	else {
		var rep = confirm("Save this package?")
		if(rep) {
			var areas = document.getElementsByName('package_items_area[]');
			var item_code = new Array();
			var item_area = new Array();
			for(i=0;i<items.length;i++)
			{
				item_code[i] = items[i].value;
				item_area[i] = areas[i].value;
			}
			if(mode=='new') {
				xajax_saveCompanyPackage($('company_id').value, $('package_name').value, $('package_price').value, item_code, item_area);
			} else if(mode=='edit') {
				xajax_editCompanyPackage($('company_id').value, $('package_id').value, $('package_name').value, $('package_price').value, item_code, item_area);
			}
		}
		return false;
	}
	return false;
}

function deleteCompanyPackage(package_id)
{
	var rep = confirm("Delete this package from list?")
	if(rep) {
		xajax_deleteCompanyPackage(package_id);
	}
}

function editCompanyPackage(package_id)
{
	//var rep = confirm("Edit this package from list?")
//	if(rep) {
		xajax_showCompanyPackageDetails($('company_id').value, package_id, 'edit');
//	}
}

function copyCompanyPackage(package_id)
{
	xajax_showCompanyPackageDetails($('company_list').value, package_id, 'copy');
	$J('#other-packages').dialog('close');
}

function clearPackageList()
{
	var rep = confirm("Performing this action will clear the tray. Continue?")
	if(rep) {
		$('package_name').value="";
		$('package_id').value="";
		$('package_price').value="";
		clearList('packagelist');
		append_empty_list();
		$('update_package').style.display="none";
		$('save_package').style.display="";
	}
}


//load jquery dom
$J(function() {
		$J("#tabs").tabs({
			selected:0,
		});

		$J('#add-package').dialog({
			title: 'Add Items',
			autoOpen: false,
			width: 560,
			height: 300,
			modal: true,
			show: 'fade',
			hide: 'fade',
			resizable: false,
			closeOnEscape: true,
			close: function() {
			}
		});

		$J('#other-packages').dialog({
			title: 'Packages from other companies',
			autoOpen: false,
			width: 560,
			height: 300,
			modal: true,
			show: 'fade',
			hide: 'fade',
			resizable: false,
			closeOnEscape: true,
			close: function() {
			}
		});

});

document.observe('dom:loaded', initialize);
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$amgr_obj = new SegAgencyManager();
$id = $_POST['agency_id'] ? $_POST['agency_id'] : $_GET['agency_id'];
$data = $amgr_obj->getCompanyDetails($id);

$smarty->assign('agency_name', '<input type="text" class="segInput" id="agency_name" name="agency_name" style="width:100%" value="'.$data['name'].'"/>');
$smarty->assign('agency_address', '<textarea class="segInput" id="agency_address" name="agency_address" style="width:100%;overflow-y:scroll;">'.$data['address'].'</textarea>');
$smarty->assign('agency_contactnum', '<input type="text" class="segInput" id="agency_contact" name="agency_contact" style="width:100%" value="'.$data['contact_no'].'"/>');
$smarty->assign('agency_sname', '<input type="text" class="segInput" id="agency_sname" name="agency_sname" style="width:100%" value="'.$data['short_id'].'"/>');
$smarty->assign('agency_president', '<input type="text" class="segInput" id="agency_president" name="agency_president" style="width:100%" value="'.$data['president'].'"/>');
$smarty->assign('agency_hr', '<input type="text" class="segInput" id="agency_hr" name="agency_hr" style="width:100%" value="'.$data['hr_manager'].'"/>');
$smarty->assign('agency_accountnum', '<input type="text" class="segInput" id="agency_account" name="agency_account" style="width:100%" value="'.$data['hosp_acct_no'].'"/>');

$smarty->assign('save_btn', '<button class="segButton" onclick="updateAgency();return false;"><img src="../../gui/img/common/default/note_go.png"/>Update</button>');
$smarty->assign('addperson_btn', '<button class="segButton" id="select-enc" onclick="openPatientSelect();return false;"><img src="../../gui/img/common/default/user_add.png"/>Add Person</button>');
$smarty->assign('close_btn','<button class="segButton" onclick="parent.cClick(); return false;"><img src="../../gui/img/common/default/cancel.png"/>Close</button>');
$smarty->assign('search_btn', '<button class="segButton" onclick="searchEmployee();return false;"><img src="'.$root_path.'gui/img/common/default/zoom.png"/>Search</button>');
$smarty->assign('search_fld', '<input type="text" class="segInput" id="psearch" name="psearch" style="width:250px" onkeyup="if(this.value.length>=3){searchEmployee();}return false;"/>');

//added by cha, august 25, 2010
//forms for services manager
$smarty->assign('companyName', '<input type="text" class="segInput" id="company_name" name="company_name" value="'.$data['name'].'" style="width:60%" readonly="readonly"/>');
$smarty->assign('companyId', '<input type="hidden" class="segInput" id="company_id" name="company_id" value="'.$id.'"/>');
$smarty->assign('serviceArea', '<select class="segInput" id="service_area" name="service_area" style="width:60%" onchange="listServices();$(\'search_service\').disabled=false;">
					<option value="0">-Select an area-</option>
					<option value="LD">Laboratory</option>
					<option value="RD">Radiology</option>
					<option value="PH">Pharmacy</option>
					<option value="MISC">Miscellaneous</option>
					</select>');
$smarty->assign('serviceSearch','<input type="text" class="segInput" id="search_service" name="search_service" style="width:60%" onkeyup="if(this.value.length>=3){listServices();}" disabled="disabled"/>');
$smarty->assign('serviceBtn','<button class="segButton" onclick="listServices();return false;"><img src="'.$root_path.'gui/img/common/default/zoom.png"/>Search</button>');
$smarty->assign('companyItemSearch','<input type="text" class="segInput" id="search_comp_item" name="search_comp_item" style="width:60%" onkeyup="if(this.value.length>=3){listCompanyServices();}"/>');
$smarty->assign('companyItemSearchBtn','<button class="segButton" onclick="listCompanyServices();return false;"><img src="'.$root_path.'gui/img/common/default/zoom.png"/>Search</button>');
//end cha

//added by cha, august 26, 2010
//forms for package manager
$smarty->assign('packageId','<input id="package_id" type="hidden"/>');
$smarty->assign('packageName','<input id="package_name" type="text" class="segInput" style="width:60%;" value=""/>');
$smarty->assign('packagePrice','<input id="package_price" type="text" class="segInput" style="width:60%;" value=""/>');
$smarty->assign('updatePackage', '<button class="segButton" id="update_package" onclick="savePackage(\'edit\');return false;" style="cursor:pointer;display:none"><img src="'.$root_path.'gui/img/common/default/folder_edit.png"/>Update</button>');
$smarty->assign('savePackage', '<button class="segButton" id="save_package" onclick="savePackage(\'new\');return false;" style="cursor:pointer;display"><img src="'.$root_path.'gui/img/common/default/disk.png"/>Save</button>');
$smarty->assign('clearPackageList', '<button class="segButton" onclick="clearPackageList();return false;" style="cursor:pointer"><img src="'.$root_path.'gui/img/common/default/bin_empty.png"/>Clear</button>');
$smarty->assign('addPackageItems', '<button class="segButton" onclick="addPackageItems();return false;" style="cursor:pointer"><img src="'.$root_path.'gui/img/common/default/cart_add.png"/>Add Items</button>');
$smarty->assign('addPackageFromOtherCompany', '<button class="segButton" onclick="otherCompanyPackages();return false;" style="cursor:pointer"><img src="'.$root_path.'gui/img/common/default/folder_explore.png"/>Other Packages</button>');
$smarty->assign('packageServiceArea', '<select class="segInput" id="package_service_area" name="package_service_area" style="width:85%" onchange="listPackageServices();$(\'search_package_service\').disabled=false;">
					<option value="0">-Select an area-</option>
					<option value="LD">Laboratory</option>
					<option value="RD">Radiology</option>
					<option value="PH">Pharmacy</option>
					<option value="MISC">Miscellaneous</option>
					</select>');
$smarty->assign('packageServiceSearch','<input type="text" class="segInput" id="search_package_service" name="search_package_service" style="width:65%" onkeyup="if(this.value.length>=3){listPackageServices();}" disabled="disabled"/>');
$smarty->assign('packageServiceBtn','<button class="segButton" onclick="listPackageServices();return false;"><img src="'.$root_path.'gui/img/common/default/zoom.png"/>Search</button>');
$smarty->assign('packageItemSearch', '<input type="text" id="package_search" name="package_search" class="segInput" style="width:60%" onkeyup="if(this.value.length>=3){listCompanyPackages();}return false;"/>');
$smarty->assign('packageItemSearchBtn', '<button class="segButton" onclick="listCompanyPackages();return false;" style="cursor:pointer"><img src="'.$root_path.'gui/img/common/default/zoom.png"/>Search</button>');

$list = $amgr_obj->getCompanyList($id);
$options="<option value='0'>-Select company-</option>";
while($row=$list->FetchRow())
{
	$options.="<option value='".$row["company_id"]."'>".$row["name"]."</option>";
}
$smarty->assign('companyList', '<select class="segInput" id="company_list" name="company_list" onchange="listOtherCompanyPackages();">'.$options.'</select>');
$smarty->assign('otherPackageSearch', '<input type="text" id="search_other_package" name="search_other_package" class="segInput" style="width:60%" onkeyup="if(this.value.length>=3){listOtherCompanyPackages();}return false;"/>');
$smarty->assign('otherPackageSearchBtn', '<button class="segButton" onclick="listOtherCompanyPackages();return false;" style="cursor:pointer"><img src="'.$root_path.'gui/img/common/default/zoom.png"/>Search</button>');

//end cha

$smarty->assign('form_start','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND.'" method="POST" id="agency_form" name="agency_form">');
$smarty->assign('form_end','</form>');
$smarty->assign('submitted', '<input type="hidden" value="TRUE" name="submitted" />');
$smarty->assign('agency_id', '<input type="hidden" value="'.$_GET['agency_id'].'" name="agency_id" id="agency_id"/>');

#added by angelo m. 08.23.2010
#start here
#for billing form


$forCompany='<label>
							<input type="checkbox" id="chkCompany" name="chkCompany" onclick="showCompany();"/>
							All
						</label>';
$forCompanyDetails='
<div id="frmCompany" style="display:none">
</div>';
$forCompany=$forCompany.$forCompanyDetails;

$forEmployee='
	<div id="frmEmployee" style="display:block">
	Employee:&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="hidden" id="txtPid" name="txtPid" class="segInput" size="50"/>
	<input type="text" id="txtsearchName" name="txtsearchName" class="segInput" size="50"/>
		<img border="0" align="absmiddle" onclick="SearchName()" alt="Search" src="../../images/his_searchbtn.gif" class="segSimulatedLink"/>
	</div>
';
$searchDteStart='
<input type="text"
 id="searchDteStart"
 value=""
 maxlength="10"
 size="10"
 name="searchDteStart"/>
<img height="22" border="0" align="absmiddle" width="26" style="cursor: pointer;" id="searchDteStart_trigger"
src="'.$root_path.'gui/img/common/default/show-calendar.gif">'.
'<script type="text/javascript">
							Calendar.setup ({
								inputField : "searchDteStart", ifFormat : "%m/%d/%Y",
								 showsTime : false,
								 button : "searchDteStart_trigger",
								 singleClick : true,
								 step : 1
							});
</script>';
$searchDteEnd='
<input type="text"
 id="searchDteEnd"
 value=""
 maxlength="10"
 size="10"
 name="searchDteEnd"/>
<img height="22" border="0" align="absmiddle" width="26" style="cursor: pointer;" id="searchDteEnd_trigger"
src="'.$root_path.'gui/img/common/default/show-calendar.gif">'.
'<script type="text/javascript">
							Calendar.setup ({
								inputField : "searchDteEnd", ifFormat : "%m/%d/%Y",
								 showsTime : false,
								 button : "searchDteEnd_trigger",
								 singleClick : true,
								 step : 1
							});
</script>';

$viewReportBtn='<button class="segButton" onclick="openReport();return false;" style="cursor:pointer">
<img src="'.$root_path.'gui/img/common/default/page_white_acrobat.png"/>View Report</button>';

$smarty->assign('forCompany',$forCompany);
$smarty->assign('forEmployee',$forEmployee);
$smarty->assign('searchDteStart',$searchDteStart);
$smarty->assign('searchDteEnd',$searchDteEnd);
$smarty->assign('viewReportBtn',$viewReportBtn);
#end here




ob_start();
$sTemp='';

?>
<input type="hidden" name="submitted" value="1" />
<input type="hidden" name="sid" value="<?php echo $sid?>">
<input type="hidden" name="lang" value="<?php echo $lang?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck?>">
<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
<input type="hidden" name="lockflag" value= "<?php echo  $lockflag?>">

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';
#$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
if (!$viewonly) {
	$smarty->assign('sContinueButton','<input type="image" class="segSimulatedLink" src="'.$root_path.'images/btn_submitorder.gif" align="absmiddle" alt="Submit">');
	$smarty->assign('sBreakButton','<img class="segSimulatedLink" src="'.$root_path.'images/btn_cancelorder.gif" alt="'.$LDBack2Menu.'" align="absmiddle" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;">');
}

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','industrial_clinic/agency_details.tpl');
$smarty->display('common/mainframe.tpl');

