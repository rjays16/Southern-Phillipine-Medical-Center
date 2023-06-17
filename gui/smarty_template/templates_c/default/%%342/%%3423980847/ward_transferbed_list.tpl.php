<?php /* Smarty version 2.6.0, created on 2020-07-22 13:07:12
         compiled from nursing/ward_transferbed_list.tpl */ ?>

<!-- <?php if (! $this->_tpl_vars['hidedatetime']): ?> -->
<table>
		<tr>
			<td class="adm_item">
				<b style="color:red; font-size: 14px">Date and Time transferred:</b>
			</td>
			<td colspan=2 class="adm_input">
				<?php echo $this->_tpl_vars['sLDDateFrom']; ?>

				<?php echo $this->_tpl_vars['sDateMiniCalendar']; ?>

				<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

				<?php echo $this->_tpl_vars['sLDTimeFrom']; ?>

			</td>
		</tr>
</table>
<!-- <?php endif; ?> -->
&nbsp;&nbsp;
<table cellspacing="0" width="100%">
<tbody>
	<tr>
		<td class="adm_item"><?php echo $this->_tpl_vars['LDRoom']; ?>
</td>
		<!-- added by Mats 07262016 -->
		<td class="adm_item"><?php echo $this->_tpl_vars['LDDescription']; ?>
</td>
		
		<td class="adm_item"><?php echo $this->_tpl_vars['LDBed']; ?>
</td>
		<td class="adm_item">&nbsp;</td>
		<td class="adm_item"><?php echo $this->_tpl_vars['LDFamilyName']; ?>
, <?php echo $this->_tpl_vars['LDName']; ?>
</td>
		<td class="adm_item"><?php echo $this->_tpl_vars['LDBirthDate']; ?>
</td>
		<td class="adm_item"><?php echo $this->_tpl_vars['LDBillType']; ?>
</td>
		<td class="adm_item">&nbsp;</td>
	</tr>

	<?php echo $this->_tpl_vars['sOccListRows']; ?>


 </tbody>
</table>