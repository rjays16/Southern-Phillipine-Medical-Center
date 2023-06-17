<?php /* Smarty version 2.6.0, created on 2020-02-05 13:13:46
         compiled from cashier/edit-or-mainblock.tpl */ ?>
<?php echo $this->_tpl_vars['sFormStart']; ?>

    <div style="padding:10px;width:95%;border:0px solid black">
    
        <font class="warnprompt"><br></font>
    <table border="0" width="30%" class="Search">
      <tbody>
        <tr>
          <td class="segPanelHeader">Search existing OR series</td>
        </tr>
        <tr>
          <td class="segPanel" align="left" style="white-space:nowrap"><?php echo $this->_tpl_vars['sFromOrNo']; ?>
 <?php echo $this->_tpl_vars['sToOrNo']; ?>
</td>
        </tr>
                    <tr>
            <td class="segPanel" align="center">
              <img class="segSimulatedLink" id="search" name="search" src="../../gui/img/control/default/en/en_searchbtn.gif" border=0 alt="Search data" align="absmiddle"  onclick="startAJAXSearch(this.id,0); return false;" />
            </td>
          </tr>   
                     
      </tbody>
    </table>
</div>
<?php echo $this->_tpl_vars['sTable']; ?>

<?php echo $this->_tpl_vars['sFormEnd']; ?>
 
<?php echo $this->_tpl_vars['sTailScripts']; ?>