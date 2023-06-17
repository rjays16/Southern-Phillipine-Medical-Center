<?php /* Smarty version 2.6.0, created on 2020-02-07 08:53:24
         compiled from nursing/ward_profile.tpl */ ?>

<ul>
<table width="90%">
  <tbody>
    <!---- added by VAN 04-11-08-->
	 <tr>
	 	<td colspan="2"><?php echo $this->_tpl_vars['LDEditWard']; ?>
</td>
	 </tr>
	 <tr>
      <td class="adm_item"><?php echo $this->_tpl_vars['LDAccommodation']; ?>
</td>
      <td class="adm_input" colspan="4"><?php echo $this->_tpl_vars['accommodation']; ?>
</td>
    </tr>
	 <!------------------------------->
	 <tr>
      <td class="adm_item"><?php echo $this->_tpl_vars['LDStation']; ?>
</td>
      <td class="adm_input" colspan="4"><?php echo $this->_tpl_vars['name']; ?>
</td>
    </tr>
    <tr>
      <td class="adm_item"><?php echo $this->_tpl_vars['LDWard_ID']; ?>
</td>
      <td class="adm_input" colspan="4"><?php echo $this->_tpl_vars['ward_id']; ?>
</td>
    </tr>
    <tr>
      <td class="adm_item"><?php echo $this->_tpl_vars['LDDept']; ?>
</td>
      <td class="adm_input" colspan="4"><?php echo $this->_tpl_vars['dept_name']; ?>
</td>
    </tr>
    <tr>
      <td class="adm_item"><?php echo $this->_tpl_vars['LDDescription']; ?>
</td>
      <td class="adm_input" colspan="4"><?php echo $this->_tpl_vars['description']; ?>
</td>
    </tr>
    <tr>
      <td class="adm_item"><?php echo $this->_tpl_vars['LDRoom1Nr']; ?>
</td>
      <td class="adm_input" colspan="4"><?php echo $this->_tpl_vars['room_nr_start']; ?>
</td>
    </tr>
    <tr>
      <td class="adm_item"><?php echo $this->_tpl_vars['LDRoom2Nr']; ?>
</td>
      <td class="adm_input" colspan="4"><?php echo $this->_tpl_vars['room_nr_end']; ?>
</td>
    </tr>
    <tr>
      <td class="adm_item"><?php echo $this->_tpl_vars['LDRoomPrefix']; ?>
</td>
      <td class="adm_input" colspan="4"><?php echo $this->_tpl_vars['roomprefix']; ?>
</td>
    </tr>
<!-- edited by shan---->
     <?php if ($this->_tpl_vars['isViewMandatory']): ?>
         <tr>
            <td class="adm_item"><?php echo $this->_tpl_vars['LDMandatory']; ?>
</td>
            <td class="adm_input" colspan="4"><?php echo $this->_tpl_vars['segMandatory']; ?>
</td>
        </tr>
     <?php endif; ?>   
<!-- <?php echo $this->_tpl_vars['LDMandatory']; ?>
 end by: shan----->
	 <!---added by VAN 04-12-08-->
	 <!--
	 <tr>
      <td class="adm_item"><?php echo $this->_tpl_vars['LDWardRate']; ?>
</td>
      <td class="adm_input" colspan="3"><?php echo $this->_tpl_vars['ward_rate']; ?>
</td>
    </tr>
	-->
	 <!-------------------->
   <tr> 
      <td class="adm_item"><?php echo $this->_tpl_vars['LDCreatedOn']; ?>
</td>
      <td class="adm_input" colspan="4"><?php echo $this->_tpl_vars['date_create']; ?>
</td>
    </tr>
   <tr>
      <td class="adm_item"><?php echo $this->_tpl_vars['LDCreatedBy']; ?>
</td>
      <td class="adm_input" colspan="4"><?php echo $this->_tpl_vars['create_id']; ?>
</td>
    </tr></tbody>

  <?php if ($this->_tpl_vars['bShowRooms']): ?>
  	<!--
    <tr>
      <td class="adm_item" colspan="3">&nbsp;</td>
    </tr>
	 <tr>
	 -->
	 	<td colspan="2"><?php echo $this->_tpl_vars['LDEditRoom']; ?>
</td>
	 </tr>
   <tr  class="wardlisttitlerow">
      <td><?php echo $this->_tpl_vars['LDRoom']; ?>
</td>
      <td width="7%"><?php echo $this->_tpl_vars['LDBedNr']; ?>
</td>
      <td><?php echo $this->_tpl_vars['LDRoomShortDescription']; ?>
</td>
	 <!-- <td width="15%"><?php echo $this->_tpl_vars['LDRoomRate']; ?>
</td>-->
	 <td width="19%"><?php echo $this->_tpl_vars['LDRoomType']; ?>
</td>
	 <td width="13%"><?php echo $this->_tpl_vars['LDRoomRate']; ?>
</td>
    </tr>
	
	<?php echo $this->_tpl_vars['sRoomRows']; ?>

  
  <?php endif; ?>

  </tbody>
</table>
<table width="100%">
  <tbody>
    <tr valign="top">
      <td><?php echo $this->_tpl_vars['sClose']; ?>
</td>
      <td align="right"><?php echo $this->_tpl_vars['sWardClosure']; ?>
</td>
    </tr>
  </tbody>
</table>
</ul>