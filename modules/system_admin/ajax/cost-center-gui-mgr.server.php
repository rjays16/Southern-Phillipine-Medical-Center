<?php
require('./roots.php');
require $root_path . 'include/care_api_classes/class_acl.php';
function populateRadioSections($dept_nr)
{
	global $db;
	$objResponse = new xajaxResponse();
	$sql = "SELECT group_code, name FROM seg_radio_service_groups WHERE department_nr=".$db->qstr($dept_nr)." AND fromdept='RD' ORDER BY name ASC";
	$result = $db->Execute($sql);
	$options = '<option value="0">-Select Section-</option>';
	while($row=$result->FetchRow())
	{
		$options.='<option value="'.$row['group_code'].'">'.$row['name'].'</option>';
	}
	$objResponse->assign("radio_section", "innerHTML", $options);
	$objResponse->assign("radio_specific_row", "style.display", "");
	return $objResponse;
}

function populateServices($cost_center, $section, $row, $col)
{
	global $db;
	$objResponse = new xajaxResponse();
	$cell_id="data".$row.$col;
	if($cost_center=="LD")
	{
		$sql = "SELECT service_code, name FROM seg_lab_services WHERE group_code=".$db->qstr($section)." AND status <> 'deleted' ORDER BY name ASC";
	}
	else if($cost_center=='RD')
	{
		$sql = "SELECT service_code, name FROM seg_radio_services WHERE group_code=".$db->qstr($section)." AND status <> 'deleted' ORDER BY name ASC";
	}else if($cost_center=='OBGYNE'){
		$sql = "SELECT service_code, name FROM seg_radio_services WHERE group_code=".$db->qstr($section)." AND status <> 'deleted' ORDER BY name ASC";
	}
	$result = $db->Execute($sql);
	$options = '<select class="segInput" id="data_values'.$row.$col.'" name="data_values[]" onchange="check_datavalues(this.value)"><option value="0" style="display">-Select Service-</option>';
	#$options = '<select id="data_values[]" name="data_values[]" onchange="check_data(this.value,\''.$row.'\',\''.$col.'\')"><option value="0" style="display">-Select Service-</option>';
	while($row=$result->FetchRow())
	{
		$options.='<option value="'.$row['service_code'].'">'.$row['name'].'</option>';
	}
	$options.="</select>";
	$objResponse->assign($cell_id, "innerHTML", $options);
	#$objResponse->assign($cell_id, "style.display", "");
	#$objResponse->assign("header".$row.$col, "style.display", "none");
	return $objResponse;
}

function populateGuiList($page)
{
	global $db;
	$objResponse = new xajaxResponse();
	$guiObj = new CostCenterGuiMgr();

	$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
	$glob_obj->getConfig('pagin_patient_search_max_block_rows');
	$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];


 	$objAcl = new Acl($_SESSION['sess_temp_userid']);

	$_a_1_sysad_gui = $objAcl->checkPermissionRaw('_a_1_sysad_gui');
	$_a_2_sysad_manage_lab = $objAcl->checkPermissionRaw('_a_2_sysad_manage_lab');
	$_a_2_sysad_manage_rad = $objAcl->checkPermissionRaw('_a_2_sysad_manage_rad');
	$_a_2_sysad_manage_spl = $objAcl->checkPermissionRaw('_a_2_sysad_manage_spl');
	$_a_2_sysad_manage_bb = $objAcl->checkPermissionRaw('_a_2_sysad_manage_bb');
	$_a_2_sysad_manage_obgyne = $objAcl->checkPermissionRaw('_a_2_sysad_manage_obgyne');
    $all_gui = ($_a_1_sysad_gui && !($_a_2_sysad_manage_lab || $_a_2_sysad_manage_rad   || $_a_2_sysad_manage_spl || $_a_2_sysad_manage_bb || $_a_2_sysad_manage_obgyne));
	$dep_list ="";
	$cond ="";
	$cond2 ="";

	if($all_gui){
		$cond = "";
		$cond2 = "";
	}else{	
		if($_a_2_sysad_manage_lab || $_a_2_sysad_manage_spl ||  $_a_2_sysad_manage_bb){
			$lab = $_a_2_sysad_manage_lab ? "'LB'," : '';
			$spl = $_a_2_sysad_manage_spl ? "'SPL'," : '';
			$bb = $_a_2_sysad_manage_bb ? "'BB'" : '';

			$cond = "WHERE category IN(".$lab.$spl.$bb;
			// var_dump($cond);exit;
			$cond=rtrim($cond, ",");
			$cond .= ")";
		}
		if($_a_2_sysad_manage_rad || $_a_2_sysad_manage_obgyne){
			// $objResponse->alert($cond);
					$rd = $_a_2_sysad_manage_rad ? "'RD'," : '';
					$ob = $_a_2_sysad_manage_obgyne ? "'OB'" : '';
					if($cond ==""){
						$cond2 .= " WHERE";
					}
					$cond2 .= " ref_source IN(".$rd.$ob;
			// var_dump($cond);exit;
			$cond2=rtrim($cond2, ",");
			$cond2 .= ")";
		}
	
}

	$offset = $page * $maxRows;
	$total_items = $guiObj->countGuiItems(0,$maxRows,$offset,$cond,$cond2);
	#$objResponse->alert($guiObj->sql);
	$total = $guiObj->count;
	$lastPage = floor($total/$maxRows);

	if ((floor($total%10))==0)
		$lastPage = $lastPage-1;


	if ($page > $lastPage) $page=$lastPage;
		$dataRow = $guiObj->getGuiItems(0,$maxRows,$offset,$cond,$cond2);

	$rows=0;
	$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
	$objResponse->call("clearList","guilist");
	if ($dataRow) {
		$rows=$dataRow->RecordCount();
		while($result=$dataRow->FetchRow())
		{
			if($result["ref_source"]=="LD")
			{
				$section_label = $db->GetOne("SELECT name FROM seg_lab_service_groups WHERE group_code=".$db->qstr($result["section"]));
				$source_label = "Laboratory";
			}else if($result["ref_source"]=="RD")
			{
				$section_label = $db->GetOne("SELECT name FROM seg_radio_service_groups WHERE group_code=".$db->qstr($result["section"])." AND fromdept='RD'");
				$source_label = "Radiology";
			}else if($result["ref_source"]=="OB"){
				$section_label = $db->GetOne("SELECT name FROM seg_radio_service_groups WHERE group_code=".$db->qstr($result["section"])." AND fromdept='OB'");
				$source_label = "OB-GYN USD";
			}
			$objResponse->call("viewGuiList","guilist",trim($result["nr"]),trim($source_label),trim($section_label));
		}#end of while
	} #end of if
	if (!$rows) $objResponse->call("viewGuiList","guilist",NULL);
	$objResponse->call("endAJAXList",$sElem);

	return $objResponse;
}

function deleteGuiItem($id)
{
	global $db;
	$objResponse = new xajaxResponse();
	$guiObj = new CostCenterGuiMgr();
	$result = $guiObj->deleteGuiItem($id);
	if($result)
	{
		$objResponse->call("refreshFrame","Delete successful!");
	}
	else
	{
		$objResponse->call("refreshFrame","Delete not successful!");
	}
	return $objResponse;
}

function getGuiItems($id)
{
	global $db;
	$objResponse = new xajaxResponse();
	$guiObj = new CostCenterGuiMgr();
	$result = $guiObj->getGuiDetailItems($id);

	while($row=$result->FetchRow())
	{
		if($row['name_type']=="H")
		{
			$dataObj->data[] = $row['header_data'];
		}
		else if($row['name_type']=="D")
		{
			$dataObj->data[] = $row['service_code'];
		}
		$dataObj->datatype[] = $row['name_type'];
		$dataObj->row_no[] = $row['row_order_no'];
		$dataObj->col_no[] = $row['col_order_no'];

		$details->id = $row['nr'];
		$details->cost_center = $row['ref_source'];
		$details->section = $row['section'];
		$details->num_rows = $row['no_rows'];
		$details->num_cols = $row['no_cols'];
	}
	$details->dataObj = $dataObj;

	if($details->cost_center=="RD")
	{
		$sql = "SELECT d.nr FROM seg_radio_service_groups AS r LEFT JOIN care_department AS d".
		" ON r.department_nr=d.nr WHERE d.parent_dept_nr='158' AND r.group_code=".$db->qstr($details->section);
		$details->radio_area = $db->GetOne($sql);
		#$objResponse->alert($details->radio_area);
	}

	$objResponse->call("initialize_gui",$details);
	return $objResponse;
}

function setSection($i)
{
	$objResponse = new xajaxResponse();
	$objResponse->assign("document.guimgr_form.cost_center","selectedIndex",$i);
	return $objResponse;
}

require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'include/care_api_classes/class_gui_cost_center_mgr.php');
require_once($root_path.'modules/system_admin/ajax/cost-center-gui-mgr.common.php');
include_once($root_path.'include/care_api_classes/class_globalconfig.php');
$xajax->processRequest();
?>
