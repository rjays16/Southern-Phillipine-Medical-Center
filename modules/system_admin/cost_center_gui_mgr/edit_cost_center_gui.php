<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require('./roots.php'); //traverse the root= directory
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
$local_user='ck_op_pflegelogbuch_user'; //I don't get this, but it has something to do with page authorization access
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php'); //load the extended smarty template
require_once($root_path . 'modules/system_admin/ajax/cost-center-gui-mgr.common.php');
require_once($root_path.'include/care_api_classes/class_gui_cost_center_mgr.php'); //load the CostCenterGuiMgr class
global $db;
$target = $_GET['target'];
$guiObj = new CostCenterGuiMgr();
$smarty = new Smarty_Care('common');
$mgr_id = ($_POST['edit_id'] ? $_POST['edit_id']:$_GET['id']);


if (isset($_POST['is_submitted']))
{

	if($guiObj->updateGuiMgr($_POST))
	{
		$smarty->assign('sysInfoMessage','GUI details successfully updated.');
	}
	else {
		 $smarty->assign('sysErrorMessage','Error in saving the GUI details.');
	}
	$mgr_id = $_POST['edit_id'];
}

/******---LOAD GUI ITEMS---*******/
$result = $guiObj->getGuiDetailItems($mgr_id);
// var_dump($guiObj->sql);exit();
while($row=$result->FetchRow())
{

	if($row['name_type']=="H")
	{
		$details->data[] = $row['header_data'];
	}
	else if($row['name_type']=="D")
	{
		$details->data[] = $row['service_code'];
	}
	$details->datatype[] = $row['name_type'];
	$details->row_no[] = $row['row_order_no'];
	$details->col_no[] = $row['col_order_no'];

	$details->id = $row['nr'];
	$details->cost_center = $row['ref_source'];
	$details->section = $row['section'];
	$details->num_rows = $row['no_rows'];
	$details->num_cols = $row['no_cols'];
}
$rad_select="";
$lab_select="";
// die($details->cost_center);
if($details->cost_center=="RD")
{
	$sql = "SELECT d.nr FROM seg_radio_service_groups AS r LEFT JOIN care_department AS d".
	" ON r.department_nr=d.nr WHERE d.parent_dept_nr='158' AND r.group_code=".$db->qstr($details->section);
	$details->radio_area = $db->GetOne($sql);
	$rad_select="selected='selected'";
}else if($details->cost_center=="OB"){
	$sql = "SELECT d.nr FROM seg_radio_service_groups AS r LEFT JOIN care_department AS d".
	" ON r.department_nr=d.nr WHERE r.group_code=".$db->qstr($details->section);
	// var_dump($sql);exit();
	$details->radio_area = $db->GetOne($sql);
	$ob_select="selected='selected'";
}
else if($details->cost_center=="LD")
{
	$lab_select="selected='selected'";
}

$datatype = implode(",",$details->datatype);
$data = implode(",",$details->data);
$rows = implode(",",$details->row_no);
$cols = implode(",",$details->col_no);
ob_start();
?>
<script type="text/javascript" src="<?= $root_path ?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/jquery/jquery.js"></script>
<script type="text/javascript">var J = jQuery.noConflict();</script>
<link rel="stylesheet" href="<?= $root_path ?>modules/system_admin/cost_center_gui_mgr/cost_center_mgr.css" type="text/css" />
<script type="text/javascript" src="<?= $root_path ?>modules/or/js/jquery.tabs/jquery.tabs.pack.js"></script>
<link rel="stylesheet" type="text/css" href="<?= $root_path ?>modules/or/js/jquery.tabs/jquery.tabs.css" />
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/jquery/ui/jquery.ui.core.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/jquery/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/jquery/ui/jquery.ui.mouse.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/jquery/ui/jquery.ui.sortable.js"></script>
<script type="text/javascript" src="<?= $root_path ?>modules/system_admin/js/gui-mgr-functions.js"></script>


<style type="text/css">
/*	.header ul, .data ul, .hiddenData ul { display: none; }*/
</style>


<script type="text/javascript">

var numRows;
var deletedRows;
var nextRowID;

J().ready(function() {

	if($('cost_center').value=="LD")
	{
		$('lab_section_row').style.display="";
		$('radio_section_row').style.display="none";
		$('radio_specific_row').style.display="none";
		$('obgyne_section_row').style.display="none";

	}
	else if($('cost_center').value=="RD")
	{
		$('lab_section_row').style.display="none";
		$('radio_section_row').style.display="";
		$('radio_specific_row').style.display="";
		$('obgyne_section_row').style.display="none";

	}else if ($('cost_center').value=="OBGYNE"){
		$('obgyne_section_row').style.display="";
		$('radio_specific_row').style.display="none";
		$('radio_section_row').style.display="none";
		$('lab_section_row').style.display="none";

	}

	//generating the data grid
	var rows = $('num_rows').value;
	var cols = $('num_cols').value;
	var table_body = '';

	fn_init_rowValues(rows);

	table_body+='<ul id="sortable">';
	for(i=0;i<rows;i++)
	{
		//table_body+='<tr class="wardlistrow" id="row'+i+'">';
		table_body+='<li class="ui-state-highlight" id="row'+i+'">';
		for(j=0;j<cols;j++)
		{
			table_body+=
				'<div>'+
				'<table width="100%" border="0" cellpadding="2" cellspacing="0">'+
					'<tr>'+
						'<td width="15%">'+
							'<select class="segInput" id="data_type'+i+j+'" name="data_type'+i+j+'" onchange="set_datatype(this.value,\''+i+'\',\''+j+'\')">'+
								'<option value="0">-Select-</option>'+
								'<option value="header">Header</option>'+
								'<option value="data">Data</option>'+
							'</select>'+
						'</td>'+
						'<td width="70%">'+
							'<span id="header'+i+j+'" style="display:none"></span>'+
							'<span id="data'+i+j+'" style="display:none"></span>'+
							'<span id="hidden_data'+i+j+'">'+
								'<input type="hidden" id="datatype'+i+j+'[]" name="datatype[]" value=""/>'+
								'<input type="hidden" id="cell_id[]" name="cell_id[]" value="'+i+'/'+j+'"/>'+
							'</span>'+
						'</td>'+
						'<td width="*">'+
							'<img src="../../../images/cost_center_insert.png" title="Insert Below" onclick="fn_InsertBelow(this,\''+i+'\','+j+');"/>&nbsp;'+
							'<img src="../../../images/cashier_delete_small.gif" title="Delete"  onclick="fn_Delete(this,\''+i+'\','+j+');"/>&nbsp;'+
						'</td>'+
					'</tr>'+
				'</table>'
				'</div>';
		}
			table_body+='</li>';
	}
	table_body+='</ul>';

	$('service_list').innerHTML = table_body;
	J("#sortable").sortable({
		containment: 'parent'
	})
		.disableSelection();
	$('control_buttons').style.display='';
	var dlen = parseInt("<? echo count($details->datatype);?>");
	var datatype = "<?echo $datatype;?>".split(',');
	var datavalue = "<?echo $data;?>".split(',');
	var row_no = "<?echo $rows;?>".split(',');
	var col_no = "<?echo $cols;?>".split(',');

	for(i=0;i<dlen;i++)
	{
		if(datatype[i]=="H")
		{
			$('datatype'+row_no[i]+col_no[i]+'[]').value="header";
			//$('header'+row_no[i]+col_no[i]).innerHTML='<span><input type="text" size="30" id="data_values'+row_no[i]+col_no[i]+'" name="data_values[]" class="segInput"/></span>';
			$('header'+row_no[i]+col_no[i]).update(
				new Element('input', {
					className: 'segInput',
					id: 'data_values'+row_no[i]+col_no[i],
					name: 'data_values[]',
					type: 'text',
					size: '30'
				})
			);

			$('header'+row_no[i]+col_no[i]).style.display='';
			$('data'+row_no[i]+col_no[i]).style.display='none';
			$('data'+row_no[i]+col_no[i]).innerHTML='';

			var dtype_form = document.guimgr_form.elements["data_type"+row_no[i]+col_no[i]];
			for(j=0;j<dtype_form.length;j++)
			{
				if(dtype_form[j].value=="header")
					dtype_form.selectedIndex = j;
			}
			$('data_values'+row_no[i]+col_no[i]).value = datavalue[i];
		}
		else if(datatype[i]=="D")
		{
			$('datatype'+row_no[i]+col_no[i]+'[]').value="data";
			$('hidden_data'+row_no[i]+col_no[i]).innerHTML+='<input type="hidden" id="dataservices'+row_no[i]+col_no[i]+'[]" name="dataservices[]" value=""/>';
			$('header'+row_no[i]+col_no[i]).style.display='none';
			$('header'+row_no[i]+col_no[i]).innerHTML='';
			$('data'+row_no[i]+col_no[i]).style.display='';
		//ajax
<?php
if($details->cost_center=="LD") {
	$sql_serv = "SELECT service_code, name FROM seg_lab_services WHERE group_code=".$db->qstr($details->section)." ORDER BY name ASC";
}
else if($details->cost_center=="RD") {
	$sql_serv = "SELECT service_code, name FROM seg_radio_services WHERE group_code=".$db->qstr($details->section)." ORDER BY name ASC";
} else if($details->cost_center=="OB"){
	$sql_serv = "SELECT service_code, name FROM seg_radio_services WHERE group_code=".$db->qstr($details->section)." ORDER BY name ASC";
}
$result_serv = $db->Execute($sql_serv);
?>
	//var options_serv = '<select class="segInput" id="data_values'+row_no[i]+col_no[i]+'" name="data_values[]" onchange="check_datavalues(this.value)"><option value="0" style="display">-Select Service-</option>';

	//-commented
	var options = new Element('SELECT', {
		id: 'data_values'+row_no[i]+col_no[i],
		name: 'data_values[]',
		value:'angelo',
		className: 'segInput'
	}).observe('change', function() {
		check_datavalues(this.value);
	});


	options.update(new Element('option', {value:datavalue[i]}).update(datavalue[i]));

<?php
		while($row=$result_serv->FetchRow())
		{
?>
			options.insert(new Element('option', {value:"<?= addslashes($row['service_code']); ?>"}).update("<?= addslashes($row['name']) ?>"));

<?php
		}
?>
		//options_serv+="</select>";
		//alert(options_serv);
		//$('data'+row_no[i]+col_no[i]).innerHTML = options_serv;
			//options_serv+='<option value=""><?echo $row['name'];?></option>';
		$('data'+row_no[i]+col_no[i]).update(options);

		var dtype_form = document.guimgr_form.elements["data_type"+row_no[i]+col_no[i]];
		for(j=0;j<dtype_form.length;j++)
		{
			if(dtype_form[j].value=="data")
				dtype_form.selectedIndex = j;
		}

		//set data values
		var dform = document.guimgr_form.elements["data_values"+row_no[i]+col_no[i]];
		var len2 = dform.length;
		for(x=0;x<len2;x++)
		{
			if(dform[x].value==datavalue[i])
				dform.selectedIndex = x;
		}
	}
}

});

function fn_init_rowValues(size){
	var i;

	numRows=size;
	nextRowID=numRows;
}


function fn_Delete(obj,id,col){
	var i;
	var current = "row"+id;
	$(current).remove();
	numRows--;
	$('num_rows').value=numRows;
}


</script>
<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$javascript = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript', $javascript);

//$smarty->assign('css_and_js', $css_and_js);
$breakfile=$root_path."main/spediens.php".URL_APPEND;

$smarty->assign('form_start', '<form name="guimgr_form" method="POST" action="'.$_SERVER['PHP_SELF'].'">');
$smarty->assign('form_end', '</form>');

 require_once($root_path . 'include/care_api_classes/class_acl.php');
	$objAcl = new Acl($_SESSION['sess_temp_userid']);

	$_a_1_sysad_gui = $objAcl->checkPermissionRaw('_a_1_sysad_gui');
	$_a_2_sysad_manage_lab = $objAcl->checkPermissionRaw('_a_2_sysad_manage_lab');
	$_a_2_sysad_manage_rad = $objAcl->checkPermissionRaw('_a_2_sysad_manage_rad');
	$_a_2_sysad_manage_spl = $objAcl->checkPermissionRaw('_a_2_sysad_manage_spl');
	$_a_2_sysad_manage_bb = $objAcl->checkPermissionRaw('_a_2_sysad_manage_bb');
	$_a_2_sysad_manage_obgyne = $objAcl->checkPermissionRaw('_a_2_sysad_manage_obgyne');

    $all_gui = ($_a_1_sysad_gui && !($_a_2_sysad_manage_lab || $_a_2_sysad_manage_rad  || $_a_2_sysad_manage_spl || $_a_2_sysad_manage_bb || $_a_2_sysad_manage_obgyne));
	$dep_list ="";
	$cond ="";
	if($all_gui){
		$dep_list .= '<option '.$lab_select.' value="LD">Laboratory</option>
					<option '.$rad_select.' value="RD">Radiology</option>
					<option '.$ob_select.' value="OBGYNE">OB-GYN USD</option>';
	}else{	
		if($_a_2_sysad_manage_lab || $_a_2_sysad_manage_spl ||  $_a_2_sysad_manage_bb){
					$dep_list .= '<option '.$lab_select.' value="LD">Laboratory</option>';
			$lab = $_a_2_sysad_manage_lab ? "'LB'," : '';
			$spl = $_a_2_sysad_manage_spl ? "'SPL'," : '';
			$bb = $_a_2_sysad_manage_bb ? "'BB'" : '';

			$cond = "WHERE category IN(".$lab.$spl.$bb;
			// var_dump($cond);exit;
			$cond=rtrim($cond, ",");
			$cond .= ")";
		}

		if($_a_2_sysad_manage_rad){
					$dep_list .= '<option '.$rad_select.' value="RD">Radiology</option>';
		}
		if($_a_2_sysad_manage_obgyne){
			$dep_list .= '<option '.$ob_select.' value="OBGYNE">OB-GYN USD</option>';
		}
}
$smarty->assign('sCostCenters', '<select class="segInput" id="cost_center" name="cost_center" onchange="list_sections(this.value);">
					<option value="0">-Select Department-</option>
				'.$dep_list.'
					</select>');
$sql = "SELECT group_code, name FROM seg_lab_service_groups ".$cond." ORDER BY name ASC";
		$result = $db->Execute($sql);
		$lab_options = '<option value="0">-Select Section-</option>';
		while($row=$result->FetchRow())
		{
			if($row['group_code']==$details->section)
				$lab_options.='<option value="'.$row['group_code'].'" selected="selected">'.$row['name'].'</option>';
			else
			$lab_options.='<option value="'.$row['group_code'].'">'.$row['name'].'</option>';
		}
$smarty->assign('sLabSections', '<select class="segInput" id="lab_section" name="lab_section">'.$lab_options.'</select>');
$sql = "SELECT group_code, name FROM seg_radio_service_groups WHERE department_nr=".$db->qstr($details->radio_area)." ORDER BY name ASC";
	$result = $db->Execute($sql);
	$options = '<option value="0">-Select Section-</option>';
	while($row=$result->FetchRow())
	{
		if($row['group_code']==$details->section)
			$rad_options.='<option value="'.$row['group_code'].'" selected="selected">'.$row['name'].'</option>';
		else
		$rad_options.='<option value="'.$row['group_code'].'">'.$row['name'].'</option>';
	}
$smarty->assign('sRadioSections', '<select class="segInput" id="radio_section" name="radio_section">'.$rad_options.'</select>');
$sql = "SELECT name_formal, nr FROM care_department WHERE parent_dept_nr='158' ORDER BY name_formal ASC";
		$result = $db->Execute($sql);
		$area_options = '<option value="0">-Select Area-</option>';
		while($row=$result->FetchRow())
		{
			if($row['nr']==$details->radio_area)
				$area_options.='<option value="'.$row['nr'].'" selected="selected">'.$row['name_formal'].'</option>';
			else
				$area_options.='<option value="'.$row['nr'].'">'.$row['name_formal'].'</option>';
		}
$smarty->assign('sRadioArea', '<select class="segInput" id="radio_area" name="radio_area" onchange="list_radio_sections(this.value)">'.$area_options.'</select>');
$sql = "SELECT group_code, name FROM seg_radio_service_groups WHERE fromdept='OB' AND status <> 'deleted' ORDER BY name ASC";
		$result = $db->Execute($sql);
		$obgyne_options = '<option value="0">-Select Section-</option>';
		while($row=$result->FetchRow())
		{
			if($row['group_code'] == $details->section)
				$selected = 'selected="selected"';
			else $selected = '';

			$obgyne_options.='<option value="'.$row['group_code'].'" '.$selected.'>'.$row['name'].'</option>';
		}
$smarty->assign('sOBGyneSections', '<select class="segInput" id="obgyne_section" name="obgyne_section">'.$obgyne_options.'</select>');
$smarty->assign('sRow', '<input type="text" id="num_rows" name="num_rows" size="5" class="segInput" value="'.$details->num_rows.'" onkeydown="return key_check(event, this.value)"/>');
$smarty->assign('sColumn', '<input type="text" id="num_cols" name="num_cols" size="5" class="segInput" value="'.$details->num_cols.'" onkeydown="return key_check(event, this.value)"/>');

$smarty->assign('package_submit', '<input type="submit" id="package_submit" value="" />');
$smarty->assign('package_cancel', '<a href="'.$breakfile.'" id="package_cancel"></a>');
$smarty->assign('is_submitted', '<input type="hidden" name="is_submitted" value="TRUE" />');
$smarty->assign('edit_nr', '<input type="hidden" name="edit_id" id="edit_id" value="'.$mgr_id.'" />');
//$smarty->assign('sOnLoadJs','onLoad="preSetEdit(\''.$_GET['id'].'\'); "');

ob_start();
?>

<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">
<input type="hidden" name="key" id="key">
<input type="hidden" name="pagekey" id="pagekey">
<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('bHideCopyright', true);
$smarty->assign('bHideTitleBar', true);
$smarty->assign('breakfile',$breakfile); //Close button
$smarty->assign('sMainBlockIncludeFile','system_admin/cost_center_gui_mgr/edit_tray.tpl'); //Assign the new_package template to the frameset
//$smarty->assign('sMainFrameBlockData',$sTemp);
$smarty->display('common/mainframe.tpl'); //Display he contents of the frame

?>