<?php /* Smarty version 2.6.0, created on 2020-02-05 13:18:18
         compiled from billing/billing_discounts.tpl */ ?>
<div align="center" style="font:bold 12px Tahoma; color:#990000; "><?php echo $this->_tpl_vars['sWarning']; ?>
</div><br />
<?php echo $this->_tpl_vars['sFormStart']; ?>

<style type="text/css">
	#discount_details tr td {
		font:normal 12px Arial, Helvetica, sans-serif;
	}				
</style>
<div align="center" id="mainSection">
	<table width="94%" id="progress_indicator" name="progress_indicator" cellpadding="0" cellspacing="1" style="display:none">
		<tr>
			<td align="left" width="*"><?php echo $this->_tpl_vars['sProgressBar']; ?>
</td>
		</tr>	
	</table>	
	<table class="segList" width="94%" id="discounts_tbl" name="discounts_tbl" cellpadding="0" cellspacing="1" style="display:none">
		<thead>
			<tr>
				<th align="left" width="27%" style="font-size:12px;">Description</th>
				<th width="12%"><span style="font-size:12px;">Bill Areas</span></th>
				<th width="30%"><span style="font-size:12px;">Remarks</span></th>
				<th width="14%"><span style="font-size:12px;">Discount (%)</span></th>
                <th width="14%"><span style="font-size:12px;">Discount<br>Amount</span></th>
				<th width="*"><span style="font-size:12px;">&nbsp;</span></th>
			</tr>
		</thead>
		<tbody id="discount_details">
		</tbody>	
	</table>
	<br>
	<table width="94%" id="footer" name="footer" cellpadding="0" cellspacing="1" style="display:none">
		<tr>
			<td align="left" width="*"><?php echo $this->_tpl_vars['sAddButton']; ?>
</td>
		</tr>	
	</table>
</div>
<div id="discountInfoBox">
<div class="hd" align="left">Discount Information</div>
<div class="bd">
	<form id="fprof" method="post" action="document.location.href">
		<table width="100%" class="segPanel">
			<tbody>
				<tr>
					<td width="31%" align="right"><b>Select Discount :</b></td>
					<td width="69%">
						<select id="discount_list" name="discount_list" onchange="jsDiscountOptionChange(this, this.options[this.selectedIndex].value)">
							<option value="">- Select Discount -</option>
						</select>
				  </td>
				</tr>
				<tr>
					<td align="right"><b>Bill Areas :</b></td>
					<td>
						<input type="hidden" name="areas_id" id="areas_id" value="">
						<TEXTAREA disabled="disabled" id="areas_desc" name="areas_desc" COLS=30 ROWS=4></TEXTAREA>
					</td>
				</tr>				
				<tr>
					<td align="right"><b>Remarks :</b></td>
					<td>
						<TEXTAREA id="remarks" name="remarks" COLS=30 ROWS=3></TEXTAREA>
					</td>
				</tr>
				<tr>
					<td align="right"><b>Discount (%):</b></td>
					<td><input style="text-align:right" onblur="trimString(this); genChkDecimal(this, 4); clearOtherField(this, 'discountamnt');" onFocus="this.select();" id="discount" name="discount" value="" /></td>
				</tr>
				<tr>
					<td align="right"><b>Discount (Fixed):</b></td>
					<td><input style="text-align:right" onblur="trimString(this); genChkDecimal(this, 2); clearOtherField(this, 'discount');" onFocus="this.select();" id="discountamnt" name="discountamnt" value="" /></td>
				</tr>                
			</tbody>
		</table>
		<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

	</form>
</div>
</div>
<span style="font:bold 15px Arial"><?php echo $this->_tpl_vars['sDebug']; ?>
</span>
<?php echo $this->_tpl_vars['sMainHiddenInputs']; ?>

<?php echo $this->_tpl_vars['sFormEnd']; ?>

<?php echo $this->_tpl_vars['sTailScripts']; ?>
