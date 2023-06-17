<?php /* Smarty version 2.6.0, created on 2020-12-01 16:25:30
         compiled from common/header_topblock.tpl */ ?>
 
<table cellspacing="0"  class="titlebar" style="border:1px solid #cfcfcf;margin-bottom:10px" cellpadding="0">    
 <tr valign=middle  class="titlebar" >
  <td bgcolor="<?php echo $this->_tpl_vars['top_bgcolor']; ?>
" valign="middle" width="1">
    &nbsp;<?php echo $this->_tpl_vars['sTitleImage']; ?>
&nbsp;<font size="3" color="<?php echo $this->_tpl_vars['top_txtcolor']; ?>
" style="white-space:nowrap"><?php echo $this->_tpl_vars['sToolbarTitle']; ?>
</font>
     <?php if ($this->_tpl_vars['Subtitle']): ?>
      - <?php echo $this->_tpl_vars['Subtitle']; ?>

     <?php endif; ?>
  </td>
<?php if ($this->_tpl_vars['QuickMenu']): ?>
	<td class="quickmenu" bgcolor="<?php echo $this->_tpl_vars['top_bgcolor']; ?>
" align=right valign="middle">
		<ul>
<?php if (count($_from = (array)$this->_tpl_vars['QuickMenu'])):
    foreach ($_from as $this->_tpl_vars['qmId'] => $this->_tpl_vars['qItem']):
?>
	<?php if ($this->_tpl_vars['qItem']['label'] != "|"): ?>
		  <li>
				<a href="<?php echo $this->_tpl_vars['qItem']['url']; ?>
">
					<span><img <?php echo $this->_tpl_vars['qItem']['icon']; ?>
 align="absmiddle"/></span>
					<?php echo $this->_tpl_vars['qItem']['label']; ?>

				</a>
			</li>
	<?php else: ?>
		  <li class="separator"></li>
	<?php endif; ?>
<?php endforeach; unset($_from); endif; ?>
		</ul>
	</td>
<?php else: ?>
  <td bgcolor="<?php echo $this->_tpl_vars['top_bgcolor']; ?>
" align=right valign="middle" style="">
  	<?php if ($this->_tpl_vars['pbAux2']): ?>
		<a href="<?php echo $this->_tpl_vars['pbAux2']; ?>
"><img <?php echo $this->_tpl_vars['gifAux2']; ?>
 alt="" <?php echo $this->_tpl_vars['dhtml']; ?>
 /></a>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['pbAux1']): ?>
		<a href="<?php echo $this->_tpl_vars['pbAux1']; ?>
"><img <?php echo $this->_tpl_vars['gifAux1']; ?>
 alt="" <?php echo $this->_tpl_vars['dhtml']; ?>
 /></a>
	<?php endif; ?>
	<!-- Hide Back buttons =)  AJMQ/Oct 03 2007
	<?php if ($this->_tpl_vars['pbBack']): ?>
		<a href="<?php echo $this->_tpl_vars['pbBack']; ?>
">
			<img <?php echo $this->_tpl_vars['gifBack2']; ?>
 alt="" <?php echo $this->_tpl_vars['dhtml']; ?>
 />
		</a>
	<?php endif; ?>
	-->
	<!---hide for the meantime...pet, apr22,2008-----
	<?php if ($this->_tpl_vars['pbHelp']): ?>
		<a href="<?php echo $this->_tpl_vars['pbHelp']; ?>
">
			<img <?php echo $this->_tpl_vars['gifHilfeR']; ?>
 alt="" <?php echo $this->_tpl_vars['dhtml']; ?>
 />
		</a>
	<?php endif; ?>
	---pet---------------------til here only-------->
	

	<!--
	created by JOHN PAUL SARGENTO
	date: march 28, 2017
	for printing nursing rounds
	 -->
	<!--button for printing nursing rounds -->
	<?php if ($this->_tpl_vars['gnrtRounds']): ?>
		<a href="<?php echo $this->_tpl_vars['breakfile']; ?>
" <?php echo $this->_tpl_vars['sCloseTarget']; ?>
>
			<img <?php echo $this->_tpl_vars['gifClose2']; ?>
 alt="<?php echo $this->_tpl_vars['LDCloseAlt']; ?>
" <?php echo $this->_tpl_vars['dhtml']; ?>
 />
		</a>
	<?php endif; ?>
	<!-- end for printing nursing rounds-->

	<?php if ($this->_tpl_vars['breakfile']): ?>
		<!-- <a style="color: black; font-size: 1em;" href="#">Print Nursing Rounds</a> -->

		<?php if ($this->_tpl_vars['breakfile'] == "nursing-wardList.php?ntid=false&lang=en&key=*&pagekey=*"): ?>

		<form method="post" action="#" id="checkTimePrintNursing">
			<div class="blackBackground"></div> 
			<div class="chooseTime">
				<div class="rowShift">
					<label class="shiftLabel">Shift: </label>
					<select name="timeChosenPrintNursing" id="timeChosenPrintNursing">
						<?php echo $this->_tpl_vars['getShift']; ?>

					</select>
				</div>
				<div class="rowShift">
					<input class="okShift" type="button" name="submitShift" value="OK" onclick="okShiftClicked();">
					<button class="cancelShift" onclick="closeNurseRoundSelection();">Cancel</button>
				</div>
			</div>
			
			
		<div class="pClassBackground"></div>   
		<?php if (isset ( $this->_tpl_vars['ward_dept'] ) && $this->_tpl_vars['ward_dept'] == $this->_tpl_vars['IPBM_DEPT']): ?>	
		<div class="updatePatientClassification">
			<div>
				<a class="pull-right" id="mod_hist" style="border: 1px solid black;padding: 5px;">View History</a>
			</div>
			<br><br>
			<table>
				<tr>
					<td>
						<label class="classLabel">Patient Name: </label>
					</td>
					<td>
						<div id="pName" class="p-info"></div>
					</td>
				</tr>
				<tr>
					<td>
						<label class="classLabel">Admission Date: </label>
					</td>
					<td>
						<div id="admission_date" class="p-info"></div>
					</td>
				</tr>
				<tr>
					<td>
						<label class="classLabel">Confinement Days: </label>
					</td>
					<td>
						<div id="confinement_days" class="p-info" style="vertical-align:middle;"></div>
					</td>
				</tr>
			</table>
			<hr>
			<table>
				<tr>
					<td>
						<label class="classLabel">Classification: </label>
					</td>
					<td>
						<select name="patient_class" id="p_class-input">
					
						</select>
					</td>
					<td>
						<input class="okShift" type="button" name="" value="OK" onclick="updateClassification();">
					</td>
					<td>
						<button class="cancelShift" onclick="closePatientClassification();return false;">Cancel</button>
					</td>
				</tr>
				
			</table>
			<div id="pclass-warning" style="text-align: center;color: black;"></div>
		</div>
		<?php endif; ?>
		

		</form>
	
		<input type="hidden" name="formclicked" id="formclicked" value="">
		<?php if ($this->_tpl_vars['hidden_rounds']): ?>
		<div class="<?php echo $this->_tpl_vars['printEndorsementList']; ?>
">Endorsement Sheet</div>
		<?php else: ?>
		<div class="<?php echo $this->_tpl_vars['printRoundsClass']; ?>
">Nursing Rounds Forms</div>
		<?php endif; ?>
		<div class="<?php echo $this->_tpl_vars['dietList']; ?>
">Diet List</div>
		<div class="<?php echo $this->_tpl_vars['vsmon']; ?>
">VS Monitoring</div>
		<div class="<?php echo $this->_tpl_vars['mms']; ?>
">Medicine Monitoring Sheet</div>
        <div class="<?php echo $this->_tpl_vars['cbgClass']; ?>
">CBG Strips Issuance</div>                
		<?php endif; ?>

		<a href="<?php echo $this->_tpl_vars['breakfile']; ?>
" <?php echo $this->_tpl_vars['sCloseTarget']; ?>
>
			<img <?php echo $this->_tpl_vars['gifClose2']; ?>
 alt="<?php echo $this->_tpl_vars['LDCloseAlt']; ?>
" <?php echo $this->_tpl_vars['dhtml']; ?>
 />
		</a>
	<?php endif; ?>
  </td>
<?php endif; ?>
 </tr>
 </table>
 <script type="text/javascript">
 	var ctpn = $("#checkTimePrintNursing");

 	ctpn.onSubmit(function(){
 		alert("text/javascript");

 	});

 	function $_GET(param)
 	{
		var vars = {};
		window.location.href.replace( location.hash, '' ).replace( 
			/[?&]+([^=&]+)=?([^&]*)?/gi, // regexp
			function( m, key, value ) { // callback
				vars[key] = value !== undefined ? value : '';
			}
		);

		if ( param ) {
		return vars[param] ? vars[param] : null;	
	}
	return vars;
	}

 	// function okShiftClicked()
 	// { 	
 	// 	var ward_name = $_GET('station');
 	// 	var ward_nr = $_GET('ward_nr');
 	// 	var timeChosen = $("#timeChosenPrintNursing option:selected").val();
	 // 	var url = "reports/nursing-print-rounds.php?time=" + timeChosen + "&ward_name=" + ward_name + "&ward_nr="+ward_nr;
	 // 	var win = window.open(url, '_blank');
		// if (win)
		//     win.focus();
		
		    
		
 	// }

 // 	$J( "#print_report").click(function() {
	// 	var HRN= $J("#hrn").text();  
	// 	var enc_nr = $J("#encounter_nr").val();
	// 	var age = $J("#age").text();
	// 	if (HRN == "") {
	// 		alert("Select a patient");
	// 	}else{
	//  	var url = "reports/waiver_report.php?pid="+HRN+"&enc="+enc_nr+"&ages="+age;	
	//  	var win = window.open(url, '_blank');
	// 	if (win)
	// 	    win.focus();
	// 	else 
	// 	    alert('Please allow popups for this website');
	// 	}
	// });
 </script>