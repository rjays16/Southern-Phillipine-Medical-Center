<?php /* Smarty version 2.6.0, created on 2020-02-05 12:42:54
         compiled from pdpu/pdpu_submenu.tpl */ ?>
<TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
    <TBODY>
    <TR>
        <TD>
            <TABLE class="submenu_group" cellSpacing=0 cellPadding=0 width=600>
                <TBODY >
                <TR>
                    <TD class="submenu_title" colspan=3>Patient Discharge Planning Unit</TD>
                </tr>
                <!-- Added by Gervie 11/02/2015 -->
                <TR>
                    <TD width="1%"><?php echo $this->_tpl_vars['sLabServicesRequestIcon']; ?>
</TD>
                    <TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDAssessment']; ?>
</nobr></TD>
                    <TD>Encode, view, and print Assessment and Referral Form</TD>
                </TR>
                <TR>
                    <TD width="1%"><?php echo $this->_tpl_vars['LDComprehensiveIcon']; ?>
</TD>
                    <TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDComprehensive']; ?>
</nobr></TD>
                    <TD>Comprehensive patient information</TD>
                </TR>
                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                </TBODY>
            </TABLE>
        </TD>
    </TR>
</TABLE>

<br/>
<a href="<?php echo $this->_tpl_vars['breakfile']; ?>
"><img <?php echo $this->_tpl_vars['gifClose2']; ?>
 alt="<?php echo $this->_tpl_vars['LDCloseAlt']; ?>
" <?php echo $this->_tpl_vars['dhtml']; ?>
></a>