<?php /* Smarty version 2.6.0, created on 2020-05-12 12:00:43
         compiled from laboratory/test_manager/test_gui_main2.tpl */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title></title>

<?php if (count($_from = (array)$this->_tpl_vars['css_and_js'])):
    foreach ($_from as $this->_tpl_vars['script']):
?>
		<?php echo $this->_tpl_vars['script']; ?>

<?php endforeach; unset($_from); endif; ?>

</head>

<body>
<?php echo $this->_tpl_vars['formstart']; ?>

<div id="lab_test" align="center" style="width:90%;">
	<ul>
		<li><a href="#test_group"><span>Services with groups</span></a></li>
		<li><a href="#test_service"><span>Services without groups</span></a></li>
	</ul>
	<div id="test_group">
		<div>
		 <table align="center" cellpadding="2" cellspacing="2" border="0" width="82%" style="border-collapse: collapse; border: 1px solid rgb(204, 204, 204);">
				<tbody>
						<tr>
							<td class="segPanelHeader" colspan="2"><strong>Search service with test group</strong></td>
						</tr>
						<tr>
							<td class="segPanel">
								<table align="center" width="82%" style="font:bold 12px Arial;">
									<tbody>
										<tr>
											<td style="width:90px" nowrap="nowrap" align="right"><b>Section</b></td>
											<td style="width:400px" nowrap="nowrap"><?php echo $this->_tpl_vars['sectionsWith']; ?>
</td>
										</tr>
										<tr>
											<td style="width:90px" nowrap="nowrap" align="right"><b>Service Name</b></td>
											<td style="width:400px" nowrap="nowrap"><?php echo $this->_tpl_vars['testGroupSearch']; ?>
&nbsp;<?php echo $this->_tpl_vars['groupSearchBtn']; ?>
&nbsp;<?php echo $this->_tpl_vars['toolsBtn']; ?>
</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
				</tbody>
			</table>
			<br/>
			<div id="test_grp_list" align="center"></div>
		 </div>
	</div>
	<div class="blues" id="test_service">
		<div>
		 <table align="center" cellpadding="2" cellspacing="2" border="0" width="82%" style="border-collapse: collapse; border: 1px solid rgb(204, 204, 204);">
				<tbody>
						<tr>
							<td class="segPanelHeader" colspan="2"><strong>Search service without test group</strong></td>
						</tr>
						<tr>
							<td class="segPanel">
								<table align="center" width="82%" style="font:bold 12px Arial;">
									<tbody>
										<tr>
											<td style="width:90px" nowrap="nowrap" align="right"><b>Section</b></td>
											<td style="width:400px" nowrap="nowrap"><?php echo $this->_tpl_vars['sectionsWitho']; ?>
</td>
										</tr>
										<tr>
											<td style="width:90px" nowrap="nowrap" align="right"><b>Service Name</b></td>
											<td style="width:400px" nowrap="nowrap"><?php echo $this->_tpl_vars['testServiceSearch']; ?>
&nbsp;<?php echo $this->_tpl_vars['serviceSearchBtn']; ?>
</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
				</tbody>
			</table>
			<br/>
			<div id="test_srv_list" align="center"></div>
		 </div>
	</div>
</div>
<br/>
<?php echo $this->_tpl_vars['formend']; ?>


</body>

</html>