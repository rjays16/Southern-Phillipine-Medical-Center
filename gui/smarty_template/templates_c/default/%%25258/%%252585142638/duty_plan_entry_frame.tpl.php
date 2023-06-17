<?php /* Smarty version 2.6.0, created on 2021-03-09 15:40:17
         compiled from radiology/duty_plan_entry_frame.tpl */ ?>

<form name="dienstplan" <?php echo $this->_tpl_vars['sFormAction']; ?>
 method="post">

<ul>

<font size=4>
<?php echo $this->_tpl_vars['LDMonth']; ?>
 <?php echo $this->_tpl_vars['sMonthSelect']; ?>
 &nbsp; <?php echo $this->_tpl_vars['LDYear']; ?>
 <?php echo $this->_tpl_vars['sYearSelect']; ?>

</font>

<table border="0">
  <tbody>
    <tr>
      <td colspan="3" valign="top">
        
		<table border=0 cellpadding=0 cellspacing=1 width="100%" class="frame">
        <tbody>
<?php if (! $this->_tpl_vars['segDutyPlanRadiologyMode']): ?>
          <tr class="submenu2_titlebar" style="font-size:16px">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td colspan="2"><?php echo $this->_tpl_vars['LDStandbyPerson']; ?>
</td>
			 <td colspan="2"><?php echo $this->_tpl_vars['LDOnCall']; ?>
</td>
          </tr>
<?php endif; ?>
		  <?php echo $this->_tpl_vars['sDutyRows']; ?>


        </tbody>
        </table>

	  </td>
	   <!--commented by VAN 03-24-08 -->
		<!--
      <td valign="top">
        <?php echo $this->_tpl_vars['sSave']; ?>

		<p>
		<?php echo $this->_tpl_vars['sClose']; ?>

      </td>
		-->
    </tr>
    <tr>
      <td colspan="3"><?php echo $this->_tpl_vars['sSave']; ?>
&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['sClose']; ?>
</td>
      <td>&nbsp;</td>
    </tr>  
  </tbody>
</table>
</ul>

<?php echo $this->_tpl_vars['sHiddenInputs']; ?>


</form>