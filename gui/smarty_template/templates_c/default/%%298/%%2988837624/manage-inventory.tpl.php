<?php /* Smarty version 2.6.0, created on 2020-02-11 13:18:14
         compiled from pharmacy/manage-inventory.tpl */ ?>
<?php echo $this->_tpl_vars['sFormStart']; ?>

<div style="width:99%; padding:5px 0px">
			     <dl id="system-message" style="display: none;">
						<dt>Information</dt>
						<dd>
							<label id="msgInfo" style="color:#0055bb;font-size: 18px;"></label> &nbsp;&nbsp;<a id="closeDD" style="cursor: pointer;">Close</a>
						</dd>
					</dl>
					 
					<dl id="error-message" style="display: none;">
						<dt>System error</dt>
						<dd>
							<label id="msgInfoError" style="color:#0055bb;font-size: 15px;"></label> &nbsp;&nbsp;<a id="closeError" style="cursor: pointer;">Close</a>
					
						</dd>
					</dl>
					

	<table border="0" cellspacing="1" cellpadding="2" width="99%" align="center" style="color:black">
	<tbody>
		
			<tr>
				<td class="segPanel" align="right" valign="middle" width="18%"><strong>Area Code</strong></td>
				<td class="segPanel2" align="left" valign="middle" width="35%" style="">
					<?php echo $this->_tpl_vars['sAreaCode']; ?>

				</td>
				<td class="segPanel2" align="left" valign="middle" width="*" style="">
					<strong>Unique identification code</strong>
				</td>
			</tr>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="18%"><strong>Area Name</strong></td>
				<td class="segPanel2" align="left" valign="middle" width="35%" style="">
					<?php echo $this->_tpl_vars['sAreaName']; ?>

				</td>
				<td class="segPanel2" align="left" valign="middle" width="*" style="">
					<strong>Identification name</strong>
				</td>
			</tr>
				<tr>
						<td class="segPanel" align="right" valign="middle"><strong>Allow socialized</strong></td>
						<td class="segPanel2" align="left" valign="middle" style="border-right:0">
							<?php echo $this->_tpl_vars['sIsSocialized']; ?>

						</td>
						<td class="segPanel2" align="left" valign="middle" style="border-left:0">
							<strong>This area is covered by charity/socialized discounts</strong>
						</td>
					</tr>

				 <!--  <tr>
						<td class="segPanel" align="right" valign="middle"><strong>Lock Flag</strong></td>
						<td class="segPanel2" align="left" valign="middle" style="border-right:0">
							<?php echo $this->_tpl_vars['sLockFlag']; ?>

						</td>
						<td class="segPanel2" align="left" valign="middle" style="border-left:0">
							<strong>Lock In Inventory</strong>
						</td>
					</tr>
					<tr>
						<td class="segPanel" align="right" valign="middle"><strong>Show Area</strong></td>
						<td class="segPanel2" align="left" valign="middle" style="border-right:0">
							<?php echo $this->_tpl_vars['sShowArea']; ?>

						</td>
						<td class="segPanel2" align="left" valign="middle" style="border-left:0">
							<strong>Show Area In Inventory</strong>
						</td>
					</tr> -->

			<tr>
				<td class="segPanel" align="right" valign="middle" width="18%"><strong>Inventory Area Code</strong></td>
				<td class="segPanel2" align="left" valign="middle" width="30%" style="border-right:0">
						<?php echo $this->_tpl_vars['sIntventoryAreaCode']; ?>

				</td>
				<td class="segPanel2" align="left" valign="middle" width="*" style="border-left:0">
					<strong>Unique area code for inventory</strong>
				</td>
			</tr>


					<tr>
						<td class="segPanel" align="right" valign="middle" width="18%"><strong>Inventory API key</strong></td>
						<td class="segPanel2" align="left" valign="middle" width="30%" style="border-right:0">
								<?php echo $this->_tpl_vars['sIntventoryAPIkey']; ?>

						</td>
						<td class="segPanel2" align="left" valign="middle" width="*" style="border-left:0">
							<strong>Unique API Key for the Inventory</strong>
						</td>
					</tr>

			<tr>
				<td class="segPanel" align="right" valign="middle"><strong>Created by</strong></td>
				<td class="segPanel2" align="left" valign="middle" style="border-right:0">
						<?php echo $this->_tpl_vars['sCreadtedBy']; ?>

				</td>
				<td class="segPanel2" align="left" valign="middle" style="border-left:0">
					<strong>Full name of user</strong>
				</td>
			</tr>

			<tr>
				<td class="segPanel" align="right" valign="middle"><strong>Created Date</strong></td>
				<td class="segPanel2" align="left" valign="middle" style="border-right:0">
						<?php echo $this->_tpl_vars['sCreadtedDT']; ?>

				</td>
				<td class="segPanel2" align="left" valign="middle" style="border-left:0">
					<strong>Date Created</strong>
				</td>
			</tr>
				<tr>
				<td  class="segPanel"align="right" valign="middle"  width="18%">
					<strong>Action</strong>
				</td>
				<td class="segPanel2" align="left" valign="middle" style="border-right:0">
				<?php echo $this->_tpl_vars['sBtnModify']; ?>

				<?php echo $this->_tpl_vars['sBtnClose']; ?>

				</td>
				
			</tr>
			
		</tbody>
	</table>

	<?php echo $this->_tpl_vars['shiddenActions']; ?>

	<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>


	<span id="tdShowWarnings" style="font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:normal;"></span>
	<div style="width:80%">
		<?php echo $this->_tpl_vars['sUpdateControlsHorizRule']; ?>

		<?php echo $this->_tpl_vars['sUpdateOrder']; ?>

		<?php echo $this->_tpl_vars['sCancelUpdate']; ?>

	</div>

	<!-- incase lng mabalik -->
		<div style="display: none;">
		<?php echo $this->_tpl_vars['sShowArea']; ?>

		<?php echo $this->_tpl_vars['sLockFlag']; ?>

		</div>
	<!--  -->

</div>
<span style="font:bold 15px Arial"><?php echo $this->_tpl_vars['sDebug']; ?>
</span>
<?php echo $this->_tpl_vars['sFormEnd']; ?>

<?php echo $this->_tpl_vars['sTailScripts']; ?>
