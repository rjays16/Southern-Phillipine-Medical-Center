<?php /* Smarty version 2.6.0, created on 2020-07-10 15:56:53
         compiled from system_admin/cost_center_gui_mgr/main_menu.tpl */ ?>
<?php echo $this->_tpl_vars['form_start']; ?>

<div id="new_package">
	<ul>
		<li><a href="#add_gui"><span>Add GUI</span></a></li>
		<li><a href="#view_list"><span>View List</span></a></li>
	</ul>
	<div id="add_gui">
		 <table border="0" cellspacing="2" cellpadding="1" width="60%" align="center">
			<tr>
				<td class="segPanelHeader" colspan="2">Details</td>
			</tr>
			<tr>
				<td colspan="2">
					<table class="segPanel" border="0" cellspacing="2" cellpadding="1" width="100%" align="center">
						<tr>
							<td><strong>Cost Center:</strong></td>
							<td><?php echo $this->_tpl_vars['sCostCenters']; ?>
</td>
						</tr>
						<tr id="lab_section_row" style="display:none">
							<td><strong>Sections:</strong></td>
							<td><?php echo $this->_tpl_vars['sLabSections']; ?>
</td>
						</tr>
						<tr id="radio_section_row" style="display:none">
							<td><strong>Area:</strong></td>
							<td><?php echo $this->_tpl_vars['sRadioArea']; ?>
</td>
						</tr>
						<tr id="radio_specific_row" style="display:none">
							<td><strong>Sections:</strong></td>
							<td><?php echo $this->_tpl_vars['sRadioSections']; ?>
</td>
						</tr>

						<tr id="obgyne_section_row" style="display:none">
							<td><strong>Sections:</strong></td>
							<td><?php echo $this->_tpl_vars['sOBGyneSections']; ?>
</td>
						</tr>

						<tr>
							<td><strong>No of Rows:</strong></td>
							<td><?php echo $this->_tpl_vars['sRow']; ?>
</td>
						</tr>
						<tr>
							<td><strong>No of Columns:</strong></td>
							<td><?php echo $this->_tpl_vars['sColumn']; ?>
</td>
						</tr>
					</table>
				</td>
			</tr>
		 </table>
		 <div style="width:80%; text-align:right; padding:2px 4px">
			<img src="../../../images/btn_add.gif" style="cursor:pointer" align="middle" id="add_gui" onclick="add_rows_cols()">
		 </div>

		 <div class="segPanel" id="service_list" align="center" style="width:90%; "></div>


		 <div id="control_buttons" style="display:none;width:165px;height:30px">
		 <?php echo $this->_tpl_vars['package_submit']; ?>

		 <?php echo $this->_tpl_vars['package_cancel']; ?>

		 <?php echo $this->_tpl_vars['is_submitted']; ?>

		 </div>
	</div>
	<div class="blues" id="view_list">
		<div style="padding:4px;height:280px;overflow-y:auto" align="center">
					<table class="segList" width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
					<thead>
						<tr class="nav">
							<th colspan="10">
								<div id="pageFirst" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,FIRST_PAGE)">
									<img title="First" src="../../../images/start.gif" border="0" align="absmiddle"/>
									<span title="First">First</span>
								</div>
								<div id="pagePrev" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,PREV_PAGE)">
									<img title="Previous" src="../../../images/previous.gif" border="0" align="absmiddle"/>
									<span title="Previous">Previous</span>
								</div>
								<div id="pageShow" style="float:left; margin-left:10px">
									<span></span>
								</div>
								<div id="pageLast" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,LAST_PAGE)">
									<span title="Last">Last</span>
									<img title="Last" src="../../../images/end.gif" border="0" align="absmiddle"/>
								</div>
								<div id="pageNext" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,NEXT_PAGE)">
									<span title="Next">Next</span>
									<img title="Next" src="../../../images/next.gif" border="0" align="absmiddle"/>
								</div>
							</th>
						</tr>
					</thead>
					</table>
					<table id="guilist" class="jedList" width="*" border="0" cellpadding="0" cellspacing="0" align="center">
						<thead>
								<tr>
									<th width="2%%" nowrap="nowrap">GUI nr</th>
									<th width="10%">Cost Center</th>
									<th width="10%">Section</th>
									<th width="3%">Options</th>
								</tr>
						</thead>
						<tbody id="guilist-body">
								<tr><td colspan="5" style="">No GUI added..</td></tr>
						</tbody>
					</table>
					</div>
	</div>
</div>
<br/>
<?php echo $this->_tpl_vars['form_end']; ?>
