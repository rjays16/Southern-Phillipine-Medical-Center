<?
# ============================================
#
# Added by James 3/4/2014
# For Generating Bill - Industrial Clinic
#
# ============================================

error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('NO_2LEVEL_CHK',1);
$local_user='ck_pflege_user';

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'include/care_api_classes/industrial_clinic/class_ic_transactions.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
require_once($root_path.'modules/industrial_clinic/ajax/seg-ic-transactions.common.php');
require_once($root_path.'include/inc_date_format_functions.php');

# Import Classes here ====================================================

$svrObj = new SegICTransaction();

# ========================================================================

global $db;

$smarty = new Smarty_Care('common');
$smarty->assign('sToolbarTitle',"Industrial Clinic :: Generate Bill");
$smarty->assign('sWindowTitle',"Industrial Clinic :: Generate Bill");

$breakfile = 'javascript:window.parent.cClick();';
$smarty->assign('breakfile', $breakfile);

ob_start();
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

<script type="text/javascript" src="js/seg-ic-billing.js"></script>
<script type="text/javascript">
var $J = jQuery.noConflict();

// Put you scripts here ================================================

	var myFrame = $J('<div></div>');

	$J(function(){

		var agency_id = "<?= $_GET['comp_id'] ?>";
		var count = "<?= $_GET['count'] ?>";
		var by_id = "<?= $_GET['by_id'] ?>";

		if(by_id == 1)
				cutoff = "<?= $_GET['sel_date'] ?>";
			else
				cutoff = "Today";

		$J('#selectall').click(function(){

			var agency_id = "<?= $_GET['comp_id'] ?>";
			var count = "<?= $_GET['count'] ?>";
			var by_id = "<?= $_GET['by_id'] ?>";

			if(by_id == 1)
					cutoff = "<?= $_GET['sel_date'] ?>";
				else
					cutoff = "Today";

			xajax_getDiscount(count, agency_id, cutoff, 0);
		});

		$J('#btnadd_discount').click(function(){

			var agency_id = "<?= $_GET['comp_id'] ?>";
			var count = "<?= $_GET['count'] ?>";
			var by_id = "<?= $_GET['by_id'] ?>";
			var subtotal = $J('#show-sub-total').html();

			if(by_id == 1)
					cutoff = "<?= $_GET['sel_date'] ?>";
				else
					cutoff = "Today";

			var params = "count="+count+"&agency_id="+agency_id+"&cutoff="+cutoff+"&subtotal="+subtotal;

			myFrame.html('<iframe src="seg-ic-discount.php?'+params+'" style="width:100%; height:100%;"></iframe>')
			.dialog({
				autoOpen:true,
				width:"70%",
				height:280,
				modal:true,
				title:"Discount Information"
			});
		});

		xajax_getDiscount(count, agency_id, cutoff, 0);
	});

	// For viewing billed reports
	function printReport(count, comp_id, cutoff_date){
		
		if(cutoff_date == "Today")
		{
			mydate = new Date();
		}
		else
		{
			mydate = new Date(cutoff_date*1000);

			d = mydate.getDate();
			m = mydate.getMonth()+1;
			y = mydate.getFullYear();

			cutoff = y+"-"+m+"-"+d;
		}

		//var url="<?=$root_path?>"+"modules/industrial_clinic/seg-ic-billing-statement-report-unbilled.php";
		var url="<?=$root_path?>"+"modules/industrial_clinic/reports/billstatement.php";
		var params = "";
		var report = "";
		//var employeeList = new Array(); //commented by art 06/10/2014
		var employeeList = [];//added by art 06/10/2014

		for(var counter=0; counter<=count; counter++)
		{
			employeeEncNo = $J('#empno_'+counter).val();
			employeeCheckbox = $J('#check_empno_'+counter+':checked').val();

			if(employeeCheckbox == "on")
			{
				//employeeList[] = employeeEncNo;//commented by art 06/10/2014
				employeeList.push(employeeEncNo);//added by art 06/10/2014
			}
		};
		//added by art 06/10/2014
		if (employeeList != '') {
			params = "comp_id="+comp_id+"&encounter_nr="+employeeList+"&cutoff="+cutoff;
			report = "seg-ic-billing-statement-report-unbilled.php";
			window.open(url+"?"+params,report,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
		}else{
			alert('No Selected Item/s');
		};
		//end 
		
	}

	// For generating billed reports
	function generateBill(count, acct_id, cutoff_date){

		var total = $J('#show-net-total').html();
		var discount_amount = $J('#show-discount-total').html();//added by art 06/08/2014

		if(cutoff_date == "Today")
		{
			mydate = new Date();
		}
		else
		{
			mydate = new Date(cutoff_date*1000);
		}

		d = mydate.getDate();
		m = mydate.getMonth()+1;
		y = mydate.getFullYear();

		cutoff = y+"-"+m+"-"+d;


		var employeeList = new Array();

		for(var counter=0; counter<=count; counter++)
		{
			var employeeEncNo = $J('#empno_'+counter).val();
			var employeeCheckbox = $J('#check_empno_'+counter+':checked').val();

			if(employeeCheckbox == "on")
			{
				employeeList[counter] = employeeEncNo;
			}
		};

		if(employeeList.length != 0)
			/*xajax_generateBill(employeeList, acct_id, cutoff, parseInt(total.replace(/,/g, ''))); commented by art 06/08/2014*/
			xajax_generateBill(employeeList, acct_id, cutoff, parseInt(total.replace(/,/g, '')),parseInt(discount_amount.replace(/,/g, ''))); //added by art 06/08/2014
		else
			alert("Cannot process this bill. No employees selected.");

	}

	// Populate Total Charges
	function populateCalcutations(subtotal, discount, nettotal) {
	
		$J('#show-sub-total').html(subtotal);
		$J('#show-discount-total').html(discount);
		$J('#show-net-total').html(nettotal);

	}

	// Calculate Total Charges
	function calculateSubTotal(count, acct_id, discount, type) {

		var discounts = $J('#show-discount-total').val();
		var discount_amount;
		var employeeList = new Array();

		for(var counter=0; counter<=count; counter++)
		{
			employeeEncNo = $J('#empno_'+counter).val();
			employeeCheckbox = $J('#check_empno_'+counter+':checked').val();

			if(employeeCheckbox == "on")
			{
				employeeList[counter] = employeeEncNo;
			}
		};

		if(type == 0)
		{
			if(discount != 0)
				discount_amount = discount;
			else
			{
				if(discounts == 0.00) discount_amount = 0;
				else discount_amount = discounts;
			}
		}
		else
		{
			discount_amount = discount;
		}

		xajax_getTotalCharges(employeeList, parseFloat(discount_amount), type);
	}

	// For closing the frame
	function refreshFrame() {
		window.parent.location.href = "seg-ic-pass.php?ntid=false&lang=en&userck=&target=ic_billing";
	}

// =====================================================================

</script>
<?

# Put your conditions here =============================================

	# Assign values
	$by_date = $_GET['by_date'];

	if($by_date == 1)
	{
		$cutoff = DATE("Y-m-d", strtotime($_GET['sel_date']));
	}
	else
	{
		$cutoff = "Today";
	}

	$agency_id = $_GET['comp_id'];
	$agency_name = $_GET['comp_name'];

	# Get Unbilled Employees
	$result = $svrObj->getUnbilledEmployees($agency_id, $cutoff);

	# Count Unbilled Employees
	$count_result = $svrObj->countUnbilledEmployees($agency_id);

	# Assign count value
	$count_row = $count_result->FetchRow();
	if($count_row)
	{
		$count = $count_row['count'];
	}
	else
	{
		$count = 0;
	}

	# Initialize counter
	$counter=0;

	while ($row = $result->FetchRow())
	{

		$transdate = $row['trxn_date'];
		$employeename = $row['name_first']." ".$row['name_last'];

		$srcRows .= '<tr>
						<td width="30%">'.$transdate.'</td>
						<td width="50%">'.$employeename.'</td>
						<td hidden>
							<input 
								type="text" 
								id="empno_'.$counter.'" 
								value="'.$row['refno'].'"/>
						</td>
						<td align="center">
							<input checked 
								class="checkall" 
								id="check_empno_'.$counter.'" 
								type="checkbox" 
								name="select-employee" 
								onchange="xajax_getDiscount('.$count.','.$agency_id.','.strtotime($cutoff).', 0);" 
								style="valign:bottom"/>
						</td>
					</tr>';

		$counter++; # Counter value + 1
	}

	# Assign Buttons
	// $previewButton = '<input class="jedButton" type="button" value="Preview" onclick="printReport('.$count.', '.$agency_id.','.strtotime($cutoff).'); return false;" style="cursor:pointer">';
	// $generateBillButton = '<input class="jedButton" type="button" value="Generate Bill" onclick="generateBill('.$count.','.$agency_id.','.strtotime($cutoff).');" style="cursor:pointer">';

	$previewButton = '<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary"
					   onclick="printReport('.$count.', '.$agency_id.','.strtotime($cutoff).');"
					   style="font:bold 12px Arial; cursor:pointer" role="button">
							<span class="ui-button-icon-primary ui-icon ui-icon-print"></span>
					  		<span class="ui-button-text">Preview</span>
					  </button>';

	global $allowedarea;
		$allowedarea = array('_a_2_BillGen');
		if (validarea($_SESSION['sess_permission'],1)) {

				$generateBillButton = '<button title="Generate Bill" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary"
								   onclick="generateBill('.$count.','.$agency_id.','.strtotime($cutoff).');"
								   style="font:bold 12px Arial; cursor:pointer" role="button">
										<span class="ui-button-icon-primary ui-icon ui-icon-disk"></span>
								  		<span class="ui-button-text">Generate Bill</span>
								  </button>';
		} else {
				$generateBillButton = '<button title="No Permission" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary"
								   style="font:bold 12px Arial; cursor:pointer" role="button" disabled>
										<span class="ui-button-icon-primary ui-icon ui-icon-disk"></span>
								  		<span class="ui-button-text">Generate Bill</span>
								  </button>';

		}

# ======================================================================

$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# assign your elements here ============================================

$smarty->assign('sAgency',$agency_name);
$smarty->assign('sCutOff',$cutoff);
$smarty->assign('sListRows',$srcRows);

# Buttons (Preview, Generate Bill)
$smarty->assign('sPreview',$previewButton);
$smarty->assign('sGenerateBill',$generateBillButton);

# ======================================================================

$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);
$smarty->assign('sMainBlockIncludeFile','industrial_clinic/generate-bill.tpl'); //Assign the industrial_clinic template to the frameset
$smarty->display('common/mainframe.tpl'); //Display the contents of the frame

?>