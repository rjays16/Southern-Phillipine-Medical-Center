{{* gui_totals.tpl  Template for cashier module totals display *}}

<style type="text/css">
.displayTotals {
	text-align:right;
	font-family:Arial;
	font-size:16px;
	font-weight:bold;
}

.displayTotalsLink {
	font-family:Arial;
	font-size:16px;
	font-weight:bold;
	cursor:pointer;
	color:#000066;
}

span.displayTotalsLink:hover {
	text-decoration:underline;
	color:#660000;
	background: #cccccc;
}
</style>

<script type="text/javascript">

function clickAmountTendered() {
	$('show-amt-tendered').style.display = 'none';
	$('amount_tendered').style.display = '';
	$('amount_tendered').focus();
}

function saveAmountTendered() {
	$('show-amt-tendered').style.display = '';
	$('amount_tendered').style.display = 'none';
	$('amount_tendered').blur();
	//amtTenderedOnBlurFocusHandle($('amount_tendered'));
	return false;
}
</script>
<table width="100%" style="font-size: 12px;" border="0" cellspacing="2" cellpadding="1">
	<tbody>
		<tr>
			<td width="20%" align="left" class="segPanelHeader" ><strong>Sub-Total</strong></td>
		</tr>
		<tr>
			<td style="background-color:#e0e0e0;margin:1px 10px;text-align:right"><span id="show-sub-total"	class="displayTotals" style="color:#000000;" {{if $sGUIvSubTotal}}value="{{$sGUIvSubTotal}}"{{else}}value="0"{{/if}}>{{$sGUISubTotal}}</span></td>
		</tr>

		<tr>
			<td width="20%" align="left" class="segPanelHeader" ><strong>Discount</strong></td>
		</tr>
		<tr>
			<td style="background-color:#d0d0d0;margin:1px 10px;text-align:right"><span id="show-discount-total" class="displayTotals" style="color:#006600;" {{if $sGUIvDiscountTotal}}value="{{$sGUIvDiscountTotal}}"{{else}}value="0"{{/if}}>{{$sGUIDiscountTotal}}</span></td>
		</tr>

		<tr>
			<td width="20%" align="left" class="segPanelHeader"><strong>Net Total</strong></td>
		</tr>
		<tr>
			<td style="background-color:#c0c0c0;margin:1px 10px;text-align:right"><span id="show-net-total" class="displayTotals" style="color:#000066" {{if $sGUIvNetTotal}}value="{{$sGUIvNetTotal}}"{{else}}value="0"{{/if}}>{{$sGUINetTotal}}</span></td>
		</tr>

		<tr>
			<td width="20%" align="left" class="segPanelHeader"><strong>Amt Tendered</strong></td>
		</tr>
		<tr>
			<td style="background-color:#b0b0b0;margin:1px 10px;text-align:right; border:1px solid #808080">
				<span id="show-amt-tendered" class="displayTotalsLink" style="color:#0000ff;display:block" {{if $sGUIvAmtTendered}}value="{{$sGUIvAmtTendered}}"{{else}}value="0"{{/if}} onclick="clickAmountTendered()">{{$sGUIAmtTendered}}</span>
				<input class="displayTotals" id="amount_tendered" name="amount_tendered" type="text" value="{{$sAmtTendered}}"
					onfocus="amtTenderedOnBlurFocusHandle(this);this.select();"
					onblur="saveAmountTendered();amtTenderedOnBlurFocusHandle(this);$('process-btn').onclick();"
					onkeyup="if (event.keyCode==13) this.blur(); return false;"
					style="margin:0;padding:0;width:100%;display:none;"/>
			</td>
		</tr>

		<tr>
			<td width="19%" align="left" class="segPanelHeader"><strong>Change</strong></td>
		</tr>
		<tr>
			<td style="background-color:#ffffff;margin:1px 10px;text-align:right;border:1px solid #cccccc"><span id="show-change" class="displayTotals" style="color:#000066;" {{if $sGUIvChange}}value="{{$sGUIvChange}}"{{else}}value="0"{{/if}}>{{$sGUIChange}}</span></td>
		</tr>

	</tbody>
</table>
