<?php
/**
* SegHIS (Billing Module)
*/
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('NO_2LEVEL_CHK',1);

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'modules/industrial_clinic/ajax/seg-ic-transactions.common.php');

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);

$xajax->printJavascript($root_path.'classes/xajax');

?>
<!-- Core module and plugins: -->
<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.maskedinput.js"></script>
<script type="text/javascript">
var $J = jQuery.noConflict();

// Put you scripts here ================================================

	$J(function(){

		var count = "<?= $_GET['count'] ?>";
		var agency_id = "<?= $_GET['agency_id'] ?>";
		var cutoff = "<?= $_GET['cutoff'] ?>";

		/*$J('#save').click(function(){

			var subtotal = "<?= $_GET['subtotal'] ?>";
			var remarks = $J('#remarks').val();
			var discountfixed = $J('#discountamnt').val();
			var discountpercent = $J('#discount').val();
			var discount;

			if(discountfixed != "" && discountpercent != "")
				alert("Cannot undergo discount with fixed and percentage amounts both filled up.");
			else if(discountfixed == "" && discountpercent == "")
				alert("No discount amount entered.");
			else
			{

				if(discountfixed > parseInt(subtotal.replace(/,/g, '')))
					alert("You cannot enter a discount that is greater than the Sub Total.");
				else if(discountpercent > 100)
					alert("You cannot enter an amount greater than 100%.");
				else
				{
					if(discountfixed)
						discount = discountfixed;
					else
						discount = parseInt(subtotal.replace(/,/g, '')*(discountpercent/100));

					xajax_saveDiscount(agency_id, cutoff, discount, remarks);
					parent.calculateSubTotal(count, agency_id, discount, 1);
					parent.myFrame.dialog("close");
				}
			}
	
		});*/

		//added by art 05/27/2014
		$J('#save').click(function(){

			var subtotal = "<?= $_GET['subtotal'] ?>";
			var remarks = $J('#remarks').val();
			var discountpercent = $J('#discount').val();

			if(discountpercent != "")
				if(discountpercent > 100)
					alert("You cannot enter an amount greater than 100%.");
				else
				{
					xajax_saveDiscount(agency_id, cutoff, discountpercent, remarks);
					parent.calculateSubTotal(count, agency_id, discountpercent, 1);
					
					parent.myFrame.dialog("close");
				}
			else
			{
				alert("No discount amount entered.");
			}
	
		});
		//end art

		$J('#cancel').click(function(){
			parent.myFrame.dialog("close");
		});

		xajax_getDiscount(count, agency_id, cutoff, 1);

	});

	function populateList(data){

		var remarks = data.remarks;
		var discount = data.discount;

		$J('#remarks').val(remarks);
		// $J('#discountamnt').val(discount);
		$J('#discount').val(discount); //added by art 05/27/2014
	
	}

	function isNumberKey(evt, type){
    
	    var charCode = (evt.which) ? evt.which : event.keyCode

	    if (charCode > 31 && (charCode < 48 || charCode > 57))
	    	if(charCode != 46)
		        return false;
	    return true;

	}

</script>
<?php

$smarty->assign('sMainBlockIncludeFile','industrial_clinic/discount.tpl');
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>
