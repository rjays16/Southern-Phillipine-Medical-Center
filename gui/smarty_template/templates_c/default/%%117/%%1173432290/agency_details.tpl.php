<?php /* Smarty version 2.6.0, created on 2020-07-06 13:44:52
         compiled from industrial_clinic/agency_details.tpl */ ?>
<div>
<div id="tabs" style="width:99%;margin-top:5px" class="ui-tabs">
	<ul>
		<li><a href="#tab-profile">Profile</a></li>
		<li><a href="#tab-employees">Employees</a></li>
		<li><a href="#tab-servicemgr">Services Manager</a></li>
		<li><a href="#tab-packages">Packages</a></li>
<!--		<li><a href="#tab-requests">Requests</a></li>
		<li><a href="#tab-billing">Billing</a></li>       -->
	</ul>

	<div id="tab-profile" class="ui-tabs-hide">
		<?php echo $this->_tpl_vars['form_start']; ?>

		<div style="width:100%;margin-top:5px">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" align="center" style="font:12px Arial bold">
					<tbody>
						<tr>
							<td class="segPanel" align="right" valign="middle" width="30%"><strong>Agency Name</strong></td>
							<td class="segPanel2" align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['agency_name']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel" align="right" valign="middle" width="30%"><strong>Address</strong></td>
							<td class="segPanel2" align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['agency_address']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel" align="right" valign="middle" width="30%"><strong>Contact No.</strong></td>
							<td class="segPanel2" align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['agency_contactnum']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel" align="right" valign="middle" width="30%"><strong>Short Name</strong></td>
							<td class="segPanel2" align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['agency_sname']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel" align="right" valign="middle" width="30%"><strong>CEO/President</strong></td>
							<td class="segPanel2" align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['agency_president']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel" align="right" valign="middle" width="30%"><strong>HR Manager</strong></td>
							<td class="segPanel2" align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['agency_hr']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel" align="right" valign="middle" width="30%"><strong>Account No.</strong></td>
							<td class="segPanel2" align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['agency_accountnum']; ?>
</td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:5px"><?php echo $this->_tpl_vars['save_btn'];  echo $this->_tpl_vars['close_btn']; ?>
</div>
			</div>
			<?php echo $this->_tpl_vars['form_end']; ?>

	</div>

	<div id="tab-employees" class="ui-tabs-hide">
		<div style="width:100%;margin-top:5px">
			<table width="100%" style="font:12px Arial bold">
				<tbody>
					<tr>
						<td colspan="2" align="left"><b>Search Employee</b>&nbsp;<?php echo $this->_tpl_vars['search_fld']; ?>
&nbsp;<?php echo $this->_tpl_vars['search_btn']; ?>
&nbsp;<?php echo $this->_tpl_vars['addperson_btn']; ?>
</td>
					</tr>
				</tbody>
			</table>
			<div class="dashlet">
				<div id="member-list" style="margin-top:10px"></div>
			</div>
		</div>
	</div>

	<div id="tab-servicemgr" class="ui-tabs-hide">
		<div style="width:100%;margin-top5px">
			<table width="100%" cellpadding="0" cellspacing="2" style="font:12px Arial bold">
				<tbody>
					<tr>
						<td class="segPanel">
							<table width="100%" cellpadding="0" cellspacing="2" style="font:12px Arial bold">
								<tr>
									<td width="30%" align="right"><strong>Company Name :</strong>&nbsp;</td>
									<td align="left"><?php echo $this->_tpl_vars['companyName'];  echo $this->_tpl_vars['companyId']; ?>
</td>
								</tr>
								<tr>
									<td width="30%" align="right"><strong>Cost Center Area :</strong>&nbsp;</td>
									<td align="left"><?php echo $this->_tpl_vars['serviceArea']; ?>
</td>
								</tr>
								<tr>
									<td width="30%" align="right"><strong>Search Service :</strong>&nbsp;</td>
									<td align="left"><?php echo $this->_tpl_vars['serviceSearch']; ?>
&nbsp;<?php echo $this->_tpl_vars['serviceBtn']; ?>
</td>
								</tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="service-list" style="margin-top:5px;">
		</div>
		<div class="dashlet" style="margin-top:10px">
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
					<tbody>
						<tr>
							<td width="15%" valign="top"><h1 style="white-space:nowrap">Search saved items</h1></td>
							<td align="left" valign="top">&nbsp;&nbsp;<?php echo $this->_tpl_vars['companyItemSearch']; ?>
&nbsp;<?php echo $this->_tpl_vars['companyItemSearchBtn']; ?>
</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div id="company-items-list" align="center">
			</div>
	</div>

	<div id="tab-packages" class="ui-tabs-hide">
		<div style="width:100%;margin-top5px">
			<table width="100%" cellpadding="0" cellspacing="2" style="font:12px Arial bold">
				<tbody>
					<tr>
						<td class="segPanelHeader"><strong>New Package details</strong></td>
					</tr>
					<tr>
						<td class="segPanel">
							<table width="100%" cellpadding="0" cellspacing="2" style="font:12px Arial bold">
								<tr>
									<td width="20%" align="right"><strong>Company Name :</strong>&nbsp;</td>
									<td align="left"><?php echo $this->_tpl_vars['companyName'];  echo $this->_tpl_vars['companyId']; ?>
</td>
								</tr>
								<tr>
									<td width="20%" align="right"><strong>Package Name :</strong>&nbsp;</td>
									<td align="left"><?php echo $this->_tpl_vars['packageName'];  echo $this->_tpl_vars['packageId']; ?>
</td>
								</tr>
								<tr>
									<td width="20%" align="right"><strong>Price :</strong>&nbsp;</td>
									<td align="left"><?php echo $this->_tpl_vars['packagePrice']; ?>
</td>
								</tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="dashlet" style="margin-top:10px">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
				<tbody>
					<tr>
						<td width="15%" valign="top"><h1 style="white-space:nowrap">List of items for this package</h1></td>
						<td align="center"><?php echo $this->_tpl_vars['addPackageItems'];  echo $this->_tpl_vars['addPackageFromOtherCompany']; ?>
</td>
						<td align="right"><?php echo $this->_tpl_vars['savePackage'];  echo $this->_tpl_vars['updatePackage'];  echo $this->_tpl_vars['clearPackageList']; ?>
</td>
					</tr>
				</tbody>
			</table>
			<table class="segList" width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top:5px;border-bottom:0">
				<thead>
					<tr>
						<th width="15%" nowrap="nowrap">Item Code</th>
						<th width="*">Item Name</th>
						<th width="15%">Area</th>
						<th width="10%"></th>
					</tr>
				</thead>
			</table>
			<div style="height:100px; overflow: auto;">
				<table id="packagelist" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
					<tbody id="packagelist-body">
						<tr id="row_empty"><td colspan="6">No items added...</td></tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="dashlet" style="margin-top:10px">
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
					<tbody>
						<tr>
							<td width="15%" valign="top"><h1 style="white-space:nowrap">Search saved packages</h1></td>
							<td align="left" valign="middle">&nbsp;&nbsp;<?php echo $this->_tpl_vars['packageItemSearch']; ?>
&nbsp;<?php echo $this->_tpl_vars['packageItemSearchBtn']; ?>
</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div id="packages-list" align="center">
			</div>
	</div>
	<div id="add-package" style="display:none">
		<table border="0" cellspacing="1" cellpadding="2" width="99%" align="center" style="font:12px Arial">
			<tbody>
				<tr>
					<td class="segPanel" align="right" valign="middle" width="30%"><strong>Cost Center Area</strong></td>
					<td align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['packageServiceArea']; ?>
</td>
				</tr>
				<tr>
					<td class="segPanel" align="right" valign="middle" width="30%"><strong>Search Item</strong></td>
					<td align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['packageServiceSearch']; ?>
&nbsp;<?php echo $this->_tpl_vars['packageServiceBtn']; ?>
</td>
				</tr>
			</tbody>
		</table>
		<div id="service-package-list" style="margin-top:5px;">
		</div>
	</div>
	<div id="other-packages" style="display:none">
		<table border="0" cellspacing="1" cellpadding="2" width="99%" align="center" style="font:12px Arial">
			<tbody>
				<tr>
					<td class="segPanel" align="right" valign="middle" width="30%"><strong>Company</strong></td>
					<td align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['companyList']; ?>
</td>
				</tr>
				<tr>
					<td class="segPanel" align="right" valign="middle" width="30%"><strong>Search package</strong></td>
					<td align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['otherPackageSearch']; ?>
&nbsp;<?php echo $this->_tpl_vars['otherPackageSearchBtn']; ?>
</td>
				</tr>
			</tbody>
		</table>
		<div id="other-package-list" style="margin-top:5px;">
		</div>
	</div>

<!--	<div id="tab-requests" class="ui-tabs-hide">
	</div> -->

<!--	<div id="tab-billing" class="ui-tabs-hide">
		<div style="width:100%;margin-top:5px">
			<table width="100%" style="font:12px Arial bold" border="0">
				<tbody>
					<tr>
						<td colspan="2" align="left" class="segPanelHeader">Search Options
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center" class="segPanel">
								<b>
									<table width="100%" style="font:12px Arial bold" border="0" class="">
										<tbody>
											<tr>
												<td colspan="2"><?php echo $this->_tpl_vars['forCompany']; ?>

												<div id="employee-list" style="margin-top:10px;display:none"></div>
												</td>
											</tr>
											<tr>
													<td colspan="2" align="left"><?php echo $this->_tpl_vars['forEmployee']; ?>
</td>
											</tr>
											<tr>
												<td width="20%">Start:</td><td width="*"><?php echo $this->_tpl_vars['searchDteStart']; ?>
</td>
											</tr>
											<tr>
												<td width="20%">End:</td><td><?php echo $this->_tpl_vars['searchDteEnd']; ?>
</td>
											</tr>
											<tr>
												<td colspan="2" align="center" class=""><?php echo $this->_tpl_vars['viewReportBtn']; ?>

												</td>
											</tr>
										</tbody>
									</table>
								</b>
							</td>
					</tr>

				</tbody>
			</table>
		</div>
	</div>-->

</div>
<?php echo $this->_tpl_vars['agency_id']; ?>

</div>

