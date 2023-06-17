<?php /* Smarty version 2.6.0, created on 2020-02-21 08:07:43
         compiled from cashier/cashier_databank_form.tpl */ ?>
<div align="center" style="font:bold 12px Tahoma; color:#990000; "><?php echo $this->_tpl_vars['sWarning']; ?>
</div>

<?php echo $this->_tpl_vars['sFormStart']; ?>


<script language="javascript" type="text/javascript">
<!--
	var discountItems = 0;

	function tabClick(listID, index) {
		var dList = $(listID);
		if (dList) {
			var listItems = dList.getElementsByTagName("LI");
			if (listItems[index]) {
				for (var i=0;i<listItems.length;i++) {
					if (i!=index) {
						listItems[i].className = "";
						if ($("tab"+i)) $("tab"+i).style.display = "none";
					}
				}
				if ($("tab"+index)) $("tab"+index).style.display = "block";
				listItems[index].className = "segActiveTab";
			}
		}
	}
	
	function toggleTBody(list) {
		var dTable = $(list);
		if (dTable) {
			var dBody = dTable.getElementsByTagName("TBODY")[0];
			if (dBody) dBody.style.display = (dBody.style.display=="none") ? "" : "none";
		}
	}
	
	function toggleCheckboxesByName(name, val) {
		var chk = document.getElementsByName(name);
		if (chk) {
			for (var i=0; i<chk.length; i++) {
				chk[i].checked = val;
			}
			return false;
		}
		return false;
	}
	
	function enableInputChildren(id, enable) {
		var el=$(id);
		if (el) {
			var children = el.getElementsByTagName("INPUT");
			if (children) {
				for (i=0;i<children.length;i++) {
					children[i].disabled = !enable;
				}
				return true;
			}
		}
		return false;
	}

	function formatNumber(num,dec) {
		var nf = new NumberFormat(num);
		if (isNaN(dec)) dec = nf.NO_ROUNDING;
		nf.setPlaces(dec);
		return nf.toFormatted();
	}

-->
</script>
<?php echo $this->_tpl_vars['sUpdateParentScript']; ?>

<div style="width:98%; padding:5px 0px">
	<table border="0" cellspacing="0" cellpadding="2" width="99%" align="center" style="border:1px solid #888888">
		<tbody>
			<tr>
				<td class="jedPanel" align="right" valign="middle" width="18%"><strong>Item code<span class="required">*</span></strong></td>
				<td class="jedPanel2" align="left" valign="middle" width="30%" style="border-right:0">
					<?php echo $this->_tpl_vars['sServiceCode']; ?>

				</td>
				<td class="jedPanel2" align="left" valign="middle" width="*" style="border-left:0">
					<strong>System generated code for this service/item</strong>
				</td>
			</tr>
			<tr>
				<td class="jedPanel" align="right" valign="middle" width="18%"><strong>Item name<span class="required">*</span></strong></td>
				<td class="jedPanel2" align="left" valign="middle" width="30%" style="border-right:0">
					<?php echo $this->_tpl_vars['sServiceName']; ?>

				</td>
				<td class="jedPanel2" align="left" valign="middle" width="*" style="border-left:0">
					<strong>Name/Identifier for the service/item</strong>
				</td>
			</tr>
<!--			<tr>
				<td class="jedPanel" align="right" valign="middle" width="18%"><strong>Description</strong></td>
				<td class="jedPanel2" align="left" valign="middle" width="30%" style="border-right:0">
					<?php echo $this->_tpl_vars['sDescription']; ?>

				</td>
				<td class="jedPanel2" align="left" valign="middle" width="*" style="border-left:0">
					<strong>Brief description for the item</strong>
				</td>
			</tr> -->
			<tr>
				<td class="jedPanel" align="right" valign="middle" width="18%"><strong>Short name<span class="required">*</span></strong></td>
				<td class="jedPanel2" align="left" valign="middle" width="30%" style="border-right:0">
					<?php echo $this->_tpl_vars['sShortName']; ?>

				</td>
				<td class="jedPanel2" align="left" valign="middle" width="*" style="border-left:0">
					<strong>Shorthand name for the item (10 characters max)</strong>
				</td>
			</tr>
			<tr>
				<td class="jedPanel" align="right" valign="middle" width="18%"><strong>Price<span class="required">*</span></strong></td>
				<td class="jedPanel2" align="left" valign="middle" style="border-right:0;" width="30%">
					<?php echo $this->_tpl_vars['sPrice']; ?>
					
				</td>
				<td class="jedPanel2" align="left" valign="middle" style="border-left:0">
					<strong>Item price (Set to 0 for arbitrary pricing)</strong>
				</td>
			</tr>
			<tr>
				<td class="jedPanel" align="right" valign="middle"><strong>Account type<span class="required">*</span></strong></td>
				<td class="jedPanel2" align="left" valign="middle" style="border-right:0">
					<?php echo $this->_tpl_vars['sAccountType']; ?>

				</td>
				<td class="jedPanel2" align="left" valign="middle" style="border-left:0">
					<strong>Categorize the item into a specific account type</strong>
				</td>
			</tr>
			<tr>
				<td class="jedPanel" align="right" valign="middle"><strong>Department</strong></td>
				<td class="jedPanel2" align="left" valign="middle" style="border-right:0">
					<?php echo $this->_tpl_vars['sDepartment']; ?>

				</td>
				<td class="jedPanel2" align="left" valign="middle" style="border-left:0">
					<strong>Assign the item to department</strong>
				</td>
			</tr>
<?php if ($this->_tpl_vars['bEditMode']): ?>
			<tr>
				<td class="jedPanel" align="right" valign="middle"><strong>Locked</strong></td>
				<td class="jedPanel2" align="left" valign="middle" style="border-right:0">
					<?php echo $this->_tpl_vars['sIsLocked']; ?>

				</td>
				<td class="jedPanel2" align="left" valign="middle" style="border-left:0">
					<strong>Lock/unlock this item from the databank</strong>
				</td>
			</tr>
<?php endif; ?>
		</tbody>
	</table>

	<div align="left" style="width:99%;padding:4px">
		<input class="jedButton" type="submit" value="Save"/>
		<input class="jedButton" type="button" value="Cancel" onclick="parent.cClick()"/>
	</div>




<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>


<span id="tdShowWarnings" style="font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:normal;"></span>
<div style="width:80%">
<?php echo $this->_tpl_vars['sUpdateControlsHorizRule']; ?>

<?php echo $this->_tpl_vars['sUpdateOrder']; ?>

<?php echo $this->_tpl_vars['sCancelUpdate']; ?>

</div>


</div>
<span style="font:bold 15px Arial"><?php echo $this->_tpl_vars['sDebug']; ?>
</span>
<?php echo $this->_tpl_vars['sFormEnd']; ?>

<?php echo $this->_tpl_vars['sTailScripts']; ?>
 	