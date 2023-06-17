<?php /* Smarty version 2.6.0, created on 2020-02-05 12:23:58
         compiled from nursing/nursing-accommodation.tpl */ ?>

<div class="container-fluid col-md-4" style="margin: 3px;">
	<div class="panel panel-primary">
      	<div class="panel-heading">Patient Details 
		<?php if ($this->_tpl_vars['hasSavedBilling']): ?>
	      	<div style="background-color: white;
					color: red;
					font-style: italic;
					font-weight: bold;
					margin-top: -20px;
					width: 430px;
					text-align: center;
					margin-left: 230px">
				<?php echo $this->_tpl_vars['hasSavedBilling']; ?>

			</div>
		<?php endif; ?>
		</div>
      	
      <div class="panel-body">
      	<table class="table table-hover" style="font-family: calibri;margin-bottom: -5px;">
      		<tr>
      			<td>HRN:&nbsp;&nbsp;<strong><span id="panelhrn"><?php echo $this->_tpl_vars['hrn']; ?>
</span></strong></td>
      			<td>Case No.:&nbsp;&nbsp;<strong><span id="panelencounter"><?php echo $this->_tpl_vars['encounter_nr']; ?>
</span></strong></td>
      		</tr>
      		<tr>
      			<td>Name:&nbsp;&nbsp;<strong><span id="panelname"><?php echo $this->_tpl_vars['fullname']; ?>
</span></strong></td>
      			<td>Admission Date and Time:&nbsp;&nbsp;<strong><span id="paneladmissiondt"><?php echo $this->_tpl_vars['admission_dt']; ?>
</span></strong> </td>
      		</tr>
      	</table>
      </div>
    </div>
  	<div class="panel panel-info">
      <div class="panel-body">
      	<table class="table table-striped table-responsive" id="addNewAcc" style="font-family: calibri;margin-bottom: -5px;border-radius: 3px;">
      		<?php if (! $this->_tpl_vars['isfinal'] && $this->_tpl_vars['hasPermissionAdd']): ?>
	  			<tr>
					<th style="padding:4px;">
						<select style="width:100%;padding:2px;height: 26px" id="wardlist" onchange="jsAccOptionsChange(this, this.options[this.selectedIndex].value, this.options[this.selectedIndex].text)">
							<option value="0" >-Select Ward-</option>
							<?php if (isset($this->_foreach['wards'])) unset($this->_foreach['wards']);
$this->_foreach['wards']['name'] = 'wards';
$this->_foreach['wards']['total'] = count($_from = (array)$this->_tpl_vars['wardlist']);
$this->_foreach['wards']['show'] = $this->_foreach['wards']['total'] > 0;
if ($this->_foreach['wards']['show']):
$this->_foreach['wards']['iteration'] = 0;
    foreach ($_from as $this->_tpl_vars['ward']):
        $this->_foreach['wards']['iteration']++;
        $this->_foreach['wards']['first'] = ($this->_foreach['wards']['iteration'] == 1);
        $this->_foreach['wards']['last']  = ($this->_foreach['wards']['iteration'] == $this->_foreach['wards']['total']);
?>
								<option value="<?php echo $this->_tpl_vars['ward']['nr']; ?>
" datavalue="<?php echo $this->_tpl_vars['ward']['name']; ?>
">
									<?php echo $this->_tpl_vars['ward']['name']; ?>

								</option>
								</br>
							<?php endforeach; unset($_from); endif; ?>
							
						</select>
					</th>
					<th style="padding:3px;">
						<select style="width:100%;padding:2px;height: 26px" id="roomlist">
							<option value="0" >-Select Room-</option>
						</select>
					</th>
					<th style="padding:3px;">
						<input type="text" style="width:100%;height:26px;padding:2px" class="date_from" id="date_from" placeholder="Select Start Date">
						<input type="text" style="width:100%;height:26px;padding:2px;display:none" class="date_from_time" id="date_from_time" placeholder="Select Start Date">
					</th>
					<th style="padding:3px;" >
						<input type="text" style="width:100%;height:26px;padding:2px" class="date_to" id="date_to" placeholder="Select End Date">
						<input type="text" style="width:100%;height:26px;padding:2px;display:none" class="date_to_time" id="date_to_time" placeholder="Select End Date">
					</th>
					<th style="padding:3px;">
						<button type="button" style="font-family:calibri;height: 26px;" class="btn btn-primary" title="Add" id="addNewRow" <?php echo $this->_tpl_vars['disabled']; ?>
><label style="margin-top: -2px">ADD</label></button>
					</th>
				</tr>
			<?php endif; ?>
			<tr><th colspan="7" style="background:#d9edf7">Accommodation History</th></tr>
      	</table>

      	<table class="table table-striped table-responsive" id="accommodationinfo" style="font-family: calibri;margin-bottom: -5px;border-radius: 3px;width: 100%">
			<thead>
				<tr>
					<th></th>
					<th>Ward Name</th>
					<th>No. of Day(s)</th>
					<th>Encoder</th>
					<th>Date & Time Encoded</th>
					<th></th>
				</tr>
			</thead>
		
			<tbody id="acccom_list" style="background: white">
				<?php if (isset($this->_foreach['accommodations'])) unset($this->_foreach['accommodations']);
$this->_foreach['accommodations']['name'] = 'accommodations';
$this->_foreach['accommodations']['total'] = count($_from = (array)$this->_tpl_vars['accommodations']);
$this->_foreach['accommodations']['show'] = $this->_foreach['accommodations']['total'] > 0;
if ($this->_foreach['accommodations']['show']):
$this->_foreach['accommodations']['iteration'] = 0;
    foreach ($_from as $this->_tpl_vars['accommodation']):
        $this->_foreach['accommodations']['iteration']++;
        $this->_foreach['accommodations']['first'] = ($this->_foreach['accommodations']['iteration'] == 1);
        $this->_foreach['accommodations']['last']  = ($this->_foreach['accommodations']['iteration'] == $this->_foreach['accommodations']['total']);
?>
					<tr id="row_<?php echo $this->_tpl_vars['accommodation']['ward_id']; ?>
">
						<td>
							<?php if (! $this->_tpl_vars['isfinal'] && ! $this->_tpl_vars['accommodation']['today'] && $this->_tpl_vars['hasPermissionDelete']): ?>
								<img src="../../images/btn_delitem.gif" id="<?php echo $this->_tpl_vars['accommodation']['ward_id']; ?>
" class="imgdelete" style="border-right:hidden; cursor:pointer;" onclick="deleteAccommodation(this.id)">
							<?php else: ?>
								<img src="../../images/btn_delitem.gif" class="imgdelete" style="border-right:hidden;opacity:0.5;" title="Unable to delete current accommodation">
							<?php endif; ?>
						</td>
						<td><?php echo $this->_tpl_vars['accommodation']['ward_name']; ?>
</td>
						<td><?php echo $this->_tpl_vars['accommodation']['nofdays']; ?>
</td>
						<td><?php echo $this->_tpl_vars['accommodation']['create_id']; ?>
</td>
						<td><?php echo $this->_tpl_vars['accommodation']['create_dt']; ?>
</td>
						<td></td>
					</tr>
				<?php endforeach; unset($_from); endif; ?>
			</tbody>

			<tfoot>
				<tr>
					<th colspan="6">
						<button type="button" style="font-family:calibri;height: 30px" class="btn btn-info" title="Audit Trail" id="audittrailbtn"><label style="margin-top: -2px">AUDIT TRAIL</label></button>
					</th>
				</tr>
			</tfoot>
		</table>
      </div>
</div>
<div id="opening-message" title="" style="display: none">
  <p style="color:red;font-weight: bold;font-size: 12px; font-style: Tahoma">
    <?php echo $this->_tpl_vars['message']; ?>

  </p>
</div>