{{$sFormStart}}
<div style="width:99%; padding:5px 0px">
	<table border="0" cellspacing="1" cellpadding="2" width="99%" align="center" style="color:black">
		<tbody>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="18%"><strong>Product Code</strong></td>
				<td class="segPanel2" align="left" valign="middle" width="35%" style="">
					{{$sProductCode}}
				</td>
				<td class="segPanel2" align="left" valign="middle" width="*" style="">
					<strong>Unique product identification code</strong>
				</td>
			</tr>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="18%"><strong>Item Code</strong></td>
				<td class="segPanel2" align="left" valign="middle" width="35%" style="">
					{{$sItemCode}}
				</td>
				<td class="segPanel2" align="left" valign="middle" width="*" style="">
					<strong>Item code from inventory</strong>
				</td>
			</tr>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="18%"><strong>Barcode</strong></td>
				<td class="segPanel2" align="left" valign="middle" width="35%" style="">
					{{$sBarcode}}
				</td>
				<td class="segPanel2" align="left" valign="middle" width="*" style="">
					<strong>Barcode from inventory</strong>
				</td>
			</tr>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="18%"><strong>Generic name</strong></td>
				<td class="segPanel2" align="left" valign="middle" width="30%" style="border-right:0">
						{{$sGenericName}}
				</td>
				<td class="segPanel2" align="left" valign="middle" width="*" style="border-left:0">
					<strong>International Nonproprietary Name for the product</strong>
				</td>
			</tr>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="18%"><strong>Drug Description</strong></td>
				<td class="segPanel2" align="left" valign="middle" width="30%" style="border-right:0">
					{{$sDrugCode}}

				</td>
				<td class="segPanel2" align="left" valign="middle" width="*" style="border-left:0">
					<!-- <strong id="drug_desc">{{$sDrugDesc}}</strong>-->
					<strong id="drug_desc">This is drug description from Phil. Health</strong>

					<input class="segInput" type="hidden" name="drug_description" id="drug_description" size="30" value="">
				</td>
			</tr>
			<tr>
				<td class="segPanel" align="right" valign="middle"><strong>Product name</strong></td>
				<td class="segPanel2" align="left" valign="middle" style="border-right:0">
					{{$sProductName}}
				</td>
				<td class="segPanel2" align="left" valign="middle" style="border-left:0">
					<strong>Full name of the product</strong>
				</td>
			</tr>
<!--			<tr>
				<td class="segPanel" align="right" valign="middle"><strong>Description</strong></td>
				<td class="segPanel2" align="left" valign="middle" style="border-right:0">
					{{$sDescription}}
				</td>
				<td class="segPanel2" align="left" valign="middle" style="border-left:0">
					<strong>Detailed product information</strong>
				</td>
			</tr>
-->
			<tr>
                <td class="segPanel" align="right" valign="middle"><strong>Is FS</strong></td>
                <td class="segPanel2" align="left" valign="middle" style="border-right:0">
                    {{$sIsFs}}
                </td>
                <td class="segPanel2" align="left" valign="middle" style="border-left:0">
                    <strong>This product is a forward stocking</strong>
                </td>
			</tr>

            <tr>
				<td class="segPanel" align="right" valign="middle"><strong>Is socialized</strong></td>
				<td class="segPanel2" align="left" valign="middle" style="border-right:0">
					{{$sIsSocialized}}
				</td>
				<td class="segPanel2" align="left" valign="middle" style="border-left:0">
					<strong>This product is covered by charity/socialized discounts</strong>
				</td>
			</tr>
			<tr>
				<td class="segPanel" align="right" valign="middle"><strong>Is in Inventory</strong></td>
				<td class="segPanel2" align="left" valign="middle" style="border-right:0">
					{{$sIsInInventory}}
				</td>
				<td class="segPanel2" align="left" valign="middle" style="border-left:0">
					<strong>This product is in the inventory system</strong>
				</td>
			</tr>
			{{if $sAvailability_True}}
			<tr>
				<td class="segPanel" align="right" valign="middle">
					<div {{$styles}}>
						<strong>Availability</strong><br />
						<a href="javascript:void" onclick="toggleCheckboxesByName('availability[]',true); return false">Check all</a>
					</div>
				</td>
				<td class="segPanel2" align="left" valign="middle" style="border-right:0">
					{{$sAvailability}}
				</td>
				<td class="segPanel2" align="left" valign="middle" style="border-left:0">
					<strong>Product availability in selected hospital areas</strong>
				</td>
			</tr>
			{{/if}}
			<tr>
				<td class="segPanel" align="right" valign="middle"><strong>Type</strong></td>
				<td class="segPanel2" align="left" valign="middle" style="border-right:0">
					{{$sProductType}}
				</td>
				<td class="segPanel2" align="left" valign="middle" style="border-left:0">
					<strong>Product type</strong>
				</td>
			</tr>
			<tr>
				<td class="segPanel" align="right" valign="middle"><strong>Product Category</strong></td>
				<td class="segPanel2" align="left" valign="middle" style="border-right:0">
					{{$sProductCategory}}
				</td>
				<td class="segPanel2" align="left" valign="middle" style="border-left:0">
					<strong>Product Type Category</strong>
				</td>
			</tr>
		</tbody>
	</table>
<!--
	<table border="0" cellspacing="0" cellpadding="2" width="99%" align="center" style="border-collapse:collapse; border:1px solid #cccccc; margin-top:5px; color:black">
		<tbody>
			<tr><td class="segPanel" colspan="3" style="height:5px"></td><tr>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="18%"><strong>Classfication</strong></td>
				<td class="segPanel2" align="left" valign="middle" style="border-right:0;	 padding:5px" colspan="2">
					<table border="0" cellpadding="0" cellspacing="0" style="font:normal 12px Arial">
						<tr>
							<td><strong><em>Select classification</em></strong></td>
							<td>&nbsp;</td>
							<td><strong><em>Product classification</em></strong></td>
						</tr>
						<tr>
							<td>{{$sSelectClassification}}</td>
							<td style="padding:0px 2px">
								<input type="button" class="segButton" value=">" style="font:bold 11px Arial;padding:0px 1px" onclick="optTransfer.transferRight()" /><br />
								<input type="button" class="segButton" value="<" style="font:bold 11px Arial;padding:0px 1px" onclick="optTransfer.transferLeft()" />
							</td>
							<td>
								{{$sSelectClassification2}}
								<br />
								<input id="classification" name="classification" type="hidden" value="">
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr><td class="segPanel" colspan="3" style="height:5px"></td></tr>
		</tbody>
	</table>
-->
	<table border="0" cellspacing="1" cellpadding="2" width="99%" align="center" style="margin-top:2px; color:black">
		<tbody>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="18%"><strong>Landed Cost</strong></td>
				<td class="segPanel2" align="left" valign="middle" style="border-right:0;" width="30%">
					{{$sCostPrice}}
				</td>
				<td class="segPanel2" align="left" valign="middle" style="border-left:0">
					<strong>Inventory System Landed Cost</strong>
				</td>
			</tr>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="18%"><strong>Cash price</strong></td>
				<td class="segPanel2" align="left" valign="middle" style="border-right:0;" width="30%">
					{{$sCashPrice}}
				</td>
				<td class="segPanel2" align="left" valign="middle" style="border-left:0">
					<strong>Default retail price (cash)</strong>
				</td>
			</tr>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="18%"><strong>Charge price</strong></td>
				<td class="segPanel2" align="left" valign="middle" style="border-right:0;" width="35%">
					{{$sChargePrice}}
				</td>
				<td class="segPanel2" align="left" valign="middle" style="border-left:0">
					<strong>Default retail price (charged)</strong>
				</td>
			</tr>
			<tr>
				<td class="segPanel" align="right" valign="middle"><strong>Discounted prices</strong></td>
				<td class="segPanel2" align="left" valign="middle" style="border-right:0; padding:5px" colspan="2">
					{{$sSelectDiscount}}
					<input id="inp-discount" type="hidden" value="" />
					<button class="segButton" id="add-discount" onclick="prepareAdd(); return false" disabled="disabled"><img src="{{$sRootPath}}gui/img/common/default/tag_blue_add.png"/>Add price</button>
					<div style="width:60%; margin-top:2px; height:120px;overflow-x:hidden; overflow-y:scroll; border:1px solid #4470b1; background-color: #ccc">
					<table id="discountprices" class="segList compact" border="0" cellpadding="0" cellspacing="0" style="font:normal 10px Arial; width:100%;">
						<thead>
							<tr>
								<th width="60%">Discount type</th>
								<th width="*" class="rightAlign">Price</th>
								<th width="10%">&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							{{$sDiscounts}}
						</tbody>
					</table>
					</div>
				</td>
			</tr>
			<!-- Added by: Macoy, May 20, 2014 -->
			<tr>
				<td class="segPanel" align="right" valign="middle" width="18%"><strong>Remarks</strong></td>
				<td><textarea id="remarks" class="segInput" style="font:12px Arial; resize: none" rows="5" cols="42" name="remarks">{{$sRemarks}}</textarea></td>
			</tr>
			<!--  -->
		</tbody>
	</table>

	<div align="left" style="width:99%;padding:4px">
	<button class="segButton"><img src="{{$sRootPath}}gui/img/common/default/disk.png" />Save</button>
	<button class="segButton" onclick="parent.cClick(); return false"><img src="{{$sRootPath}}gui/img/common/default/cancel.png" />Close</button>
	</div>

	{{$sHiddenInputs}}
	{{$jsCalendarSetup}}

	<span id="tdShowWarnings" style="font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:normal;"></span>
	<div style="width:80%">
		{{$sUpdateControlsHorizRule}}
		{{$sUpdateOrder}}
		{{$sCancelUpdate}}
	</div>


</div>
<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}}
