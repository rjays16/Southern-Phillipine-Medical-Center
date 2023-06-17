<?php /* Smarty version 2.6.0, created on 2020-02-07 08:54:39
         compiled from nursing/ward_create_form.tpl */ ?>

<p>

<ul>
<?php echo $this->_tpl_vars['sMascotImg']; ?>
 <?php echo $this->_tpl_vars['sStationExists']; ?>
 <?php echo $this->_tpl_vars['LDEnterAllFields']; ?>

<p>
</p>
<!--
<form action="nursing-station-new.php" method="post" name="newstat" onSubmit="return check(this)">
<form action="nursing-station-new.php" method="post" name="newstat" id="newstat" onSubmit="return false;">
-->
<form action="nursing-station-new.php" method="post" name="newstat" id="newstat" onSubmit="return checkWardForm();">
<table width="70%">
  <tbody>
    <tr>
      <td class="adm_item"><?php echo $this->_tpl_vars['LDAccomodationType']; ?>
</td>
      <td class="adm_input"><?php echo $this->_tpl_vars['sAccTypeRadio']; ?>
</td>
    </tr>
    <tr>
      <td class="adm_item"><?php echo $this->_tpl_vars['LDStation']; ?>
</td>
      <td class="adm_input"><?php echo $this->_tpl_vars['segName']; ?>
</td>
    </tr>
    <tr>
      <td class="adm_item"><?php echo $this->_tpl_vars['LDWard_ID']; ?>
</td>
      <td class="adm_input"><?php echo $this->_tpl_vars['segWardID']; ?>
 <?php echo $this->_tpl_vars['LDNoSpecChars']; ?>
</td>
    </tr>
    <tr class="charityOnly">
      <td class="adm_item"><?php echo $this->_tpl_vars['LDDept']; ?>
</td>
      <td class="adm_input"><?php echo $this->_tpl_vars['sDeptSelectBox']; ?>
 <?php echo $this->_tpl_vars['sSelectIcon']; ?>
 <?php echo $this->_tpl_vars['LDPlsSelect']; ?>
</td>
    </tr>        
    <tr>
      <td class="adm_item"><?php echo $this->_tpl_vars['LDDescription']; ?>
</td>
      <td class="adm_input"><?php echo $this->_tpl_vars['segDescription']; ?>
</td>
    </tr>
    <!--
    <tr>
        <td class="adm_item"><?php echo $this->_tpl_vars['LDWardRate']; ?>
</td>
        <td class="adm_input"><?php echo $this->_tpl_vars['segWardRate']; ?>
 <?php echo $this->_tpl_vars['segRoomNxtNr']; ?>
 <?php echo $this->_tpl_vars['segRoomStartNr']; ?>
 <?php echo $this->_tpl_vars['segRoomEndNr']; ?>
</td>
    </tr>
    -->
    <!--edited by pol-->
    <!--<tr class="charityOnly">
        <td class="adm_item"><?php echo $this->_tpl_vars['LDRoomNr']; ?>
</td>
        <td class="adm_input"></td>  
    </tr> -->
    <?php echo $this->_tpl_vars['segRoomNr']; ?>
 <?php echo $this->_tpl_vars['segRoomNxtNr']; ?>
 <?php echo $this->_tpl_vars['segRoomStartNr']; ?>
 <?php echo $this->_tpl_vars['segRoomEndNr']; ?>
                          
    <!--added by VAN 04-10-08 --->
    <!--
    <tr>
        <td class="adm_item"><?php echo $this->_tpl_vars['LDRoom1Nr']; ?>
</td>
        <td class="adm_input"><?php echo $this->_tpl_vars['segRoomStartNr']; ?>
</td>
    </tr>
    <tr>
        <td class="adm_item"><?php echo $this->_tpl_vars['LDRoom2Nr']; ?>
</td>
        <td class="adm_input"><?php echo $this->_tpl_vars['segRoomEndNr']; ?>
</td>
    </tr>
    -->
    <!-------------------->
    <!-----edited by VAN 04-11-08 --------->
    <!--
    <tr>
        <td class="adm_item"><?php echo $this->_tpl_vars['LDRoomInfo']; ?>
</td>
        <td class="adm_input"><?php echo $this->_tpl_vars['segRoomInfo']; ?>
</td>
    </tr>
    <tr>
        <td class="adm_item"><?php echo $this->_tpl_vars['LDNoOfBeds']; ?>
</td>
        <td class="adm_input"><?php echo $this->_tpl_vars['segNrOfBeds']; ?>
</td>
    </tr>
    -->
    <!--<tr class="charityOnly">
        <td class="adm_item"><?php echo $this->_tpl_vars['LDRoomInfo']; ?>
</td>
        <td class="adm_input"><?php echo $this->_tpl_vars['segRoomInfo']; ?>
</td>
    </tr>    -->
    <!--
    <tr class="charityOnly">
        <td class="adm_item"><?php echo $this->_tpl_vars['LDRoomRate']; ?>
</td>
        <td class="adm_input"><?php echo $this->_tpl_vars['segRoomRate']; ?>
</td>
    </tr>
    -->
   <!-- <tr class="charityOnly">
        <td class="adm_item"><?php echo $this->_tpl_vars['LDRoomType']; ?>
</td>
        <td class="adm_input"><?php echo $this->_tpl_vars['segRoomType']; ?>
</td>
    </tr>
    <tr class="charityOnly">
        <td class="adm_item"><?php echo $this->_tpl_vars['LDNoOfBeds']; ?>
</td>
        <td class="adm_input"><?php echo $this->_tpl_vars['segNrOfBeds']; ?>
</td>
    </tr>          -->
    <!--end edited by pol-->
<!--
    <tr>
      <td class="adm_item"><?php echo $this->_tpl_vars['LDRoom1Nr']; ?>
</td>
      <td class="adm_input"><input type="text" name="room_nr_start" size=4 maxlength=4 value="<?php echo $this->_tpl_vars['room_nr_start']; ?>
" /></td>
    </tr>
    <tr>
      <td class="adm_item"><?php echo $this->_tpl_vars['LDRoom2Nr']; ?>
</td>
      <td class="adm_input"><input type="text" name="room_nr_end" size=4 maxlength=4 value="<?php echo $this->_tpl_vars['room_nr_end']; ?>
" /></td>
    </tr>
-->
    <tr>
      <td class="adm_item"><?php echo $this->_tpl_vars['LDRoomPrefix']; ?>
</td>
      <td class="adm_input"><?php echo $this->_tpl_vars['segRoomPrefix']; ?>
</td>
    </tr>
<!--  edited by shand for mandatory excess---->
     <tr class="charityOnly">
        <td class="adm_item"><?php echo $this->_tpl_vars['LDMandatory']; ?>
</td>
        <td class="adm_input"><?php echo $this->_tpl_vars['segMandatory']; ?>
</td>
    </tr>
<!-- end    -->
<!--edited by pol-->
    <tr class="">
        <td colspan="2"><?php echo $this->_tpl_vars['segAddRoom']; ?>
</td>
    </tr>
    <tr class="">
        <td colspan="2">
            <table id="room-list" class="segList" border="0" width="90%" cellpadding="1" cellspacing="1" style="border:1px solid #666666;border-bottom:0px;">
                <thead>
                    <tr class="reg_list_titlebar">
                        <td width="12%"><font face="verdana,arial" size="2" >&nbsp;<b> Room No. </b></font></td>
                        <td width="15%"><font face="verdana,arial" size="2" >&nbsp;<b> No. of Beds </b></font></td>
                        <td width="25%"><font face="verdana,arial" size="2" > <b>&nbsp; Room's short description &nbsp;</b></font></td>
                        <!--<td><font face="verdana,arial" size="2" > <b>&nbsp; Room Rate &nbsp;</b></font></td>-->
                        <td width="5%"><font face="verdana,arial" size="2" > <b>&nbsp; Room Type &nbsp;</b></font></td>
                        <td width="2%"><font face="verdana,arial" size="2" > <b>&nbsp; &nbsp;</b></font></td>
                        <td width="2%"><font face="verdana,arial" size="2" > <b>&nbsp; &nbsp;</b></font></td>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $this->_tpl_vars['sRoomItems']; ?>

                </tbody>
            </table>        
        </td>
    </tr>
    <!--end edited by pol-->
    <!---pol-->
   
  </tbody>
</table>
<br>
<!--<?php echo $this->_tpl_vars['sSaveButton']; ?>
-->
<table>
    <tr>
        <td><?php echo $this->_tpl_vars['sSaveButton']; ?>
</td>
        <td><?php echo $this->_tpl_vars['sCancel']; ?>
</td>
    </tr>
</table>
<?php echo $this->_tpl_vars['segInitialization']; ?>

</form>
<form action="nursing-station-new.php?mode=update" method="post" name="viewstat" id="viewstat" onSubmit="">
    <?php echo $this->_tpl_vars['sFormModeUpdate']; ?>

</form>
<p>
<!--<?php echo $this->_tpl_vars['sCancel']; ?>
-->
</p>
</ul>