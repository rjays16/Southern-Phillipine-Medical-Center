<?php /* Smarty version 2.6.0, created on 2020-07-11 08:30:56
         compiled from or/or_asu_request.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'or/or_asu_request.tpl', 30, false),array('function', 'html_radios', 'or/or_asu_request.tpl', 62, false),array('function', 'html_checkboxes', 'or/or_asu_request.tpl', 217, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title></title>
<?php echo $this->_tpl_vars['check_date_string']; ?>

<?php echo $this->_tpl_vars['or_main_css']; ?>

<?php if (count($_from = (array)$this->_tpl_vars['javascript_array'])):
    foreach ($_from as $this->_tpl_vars['js']):
?>
		<?php echo $this->_tpl_vars['js']; ?>

<?php endforeach; unset($_from); endif; ?>
<script>


</script>
</head>
<body onload="preset();">

<div id="or_main_request" align="left">
	<?php echo $this->_tpl_vars['form_start']; ?>

	<span id="reminder">Required fields are marked with <?php echo $this->_tpl_vars['required_mark']; ?>
</span>

	<fieldset>
		<legend>Request Details</legend>
	<table>

		<!--Added by Cherry 04-28-10-
		<tr>
			<td><label>Requesting Doctor:</label> <?php echo $this->_tpl_vars['required_mark']; ?>
 </td>
				<td>
					<select name="or_doctor">
						<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['or_doctor'],'selected' => $this->_tpl_vars['or_doctor_selected']), $this);?>

					</select>
				</td>
				 <td valign="middle"><span id="or_doctor_msg"><?php echo $this->_tpl_vars['error_input']; ?>
</span></td>
		</tr>
	-->

		<tr>
			<td><label>Department</label> <?php echo $this->_tpl_vars['required_mark']; ?>
 </td>
				<td>
					<select name="or_request_department" id="or_request_department">
						<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['or_request_department'],'selected' => $this->_tpl_vars['or_request_department_selected']), $this);?>

					</select>
				</td>
					<td valign="middle"><span id="or_request_department_msg"><?php echo $this->_tpl_vars['error_input']; ?>
</span></td>
		</tr>

		<!--<tr>
			<td width="210px"><label>Department:</label></td>
			<td width="160px"><?php echo $this->_tpl_vars['or_request_department']; ?>
</td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td><label>Operating Room:</label></td>
			<td><?php echo $this->_tpl_vars['or_op_room']; ?>
</td>
			<td></td>
			<td></td>
		</tr>-->

		 <tr>
			<td valign="middle"><label>Transaction:</label> <?php echo $this->_tpl_vars['required_mark']; ?>
 </td>
			<td> <?php echo smarty_function_html_radios(array('name' => 'or_transaction_type','options' => $this->_tpl_vars['or_transaction_type'],'selected' => $this->_tpl_vars['or_transaction_type_selected'],'id' => 'or_transaction_type'), $this);?>
</td>
			<td valign="middle"><span id="transaction_type_msg"><?php echo $this->_tpl_vars['error_input']; ?>
</span></td>
			<td></td>
		</tr>



		 <tr>
			<td width="210px"><label>Date Requested:</label></td>
			<td width="160px"><?php echo $this->_tpl_vars['or_request_date_display'];  echo $this->_tpl_vars['or_request_date']; ?>
</td>
			<td><?php echo $this->_tpl_vars['or_request_dt_picker']; ?>
</td>
			<td><?php echo $this->_tpl_vars['or_request_calendar_script']; ?>
</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
		</tr>

		<!--<tr>
			<td valign="middle"><label>OR Type:</label> <?php echo $this->_tpl_vars['required_mark']; ?>
</td>
			<td>
				<select name="or_type">
					<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['or_type'],'selected' => $this->_tpl_vars['or_type_selected']), $this);?>

				</select>
			</td>
			<td valign="middle"><span id="transaction_type_msg"><?php echo $this->_tpl_vars['error_input']; ?>
</span></td>
			<td></td>
		</tr>-
		<tr>
			<td valign="middle"><label>Priority:</label> <?php echo $this->_tpl_vars['required_mark']; ?>
</td>
			<td><?php echo smarty_function_html_radios(array('name' => 'or_request_priority','options' => $this->_tpl_vars['or_request_priority'],'separator' => "<br/>",'selected' => $this->_tpl_vars['or_request_priority_selected']), $this);?>
</td>
			<td valign="middle"><span id="priority_msg"><?php echo $this->_tpl_vars['error_input']; ?>
</span></td>
			<td></td>
		</tr>-->


		<tr>
			<td><label>Procedure:</label> <?php echo $this->_tpl_vars['required_mark']; ?>
 </td>
			<td><?php echo $this->_tpl_vars['package_name']; ?>
</td>
			<td><?php echo $this->_tpl_vars['procedure_select']; ?>
</td>
			<td><span id="procedure_name_msg"><?php echo $this->_tpl_vars['error_input']; ?>
</span></td>
		</tr>

		<tr>
			<td>&nbsp;</td>
		 </tr>

			<!--Added by Cherry 04-28-10-->
		<tr>
			<td><label>Requesting Doctor:</label> <?php echo $this->_tpl_vars['required_mark']; ?>
 </td>
				<td>
					<select name="or_doctor">
						<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['or_doctor'],'selected' => $this->_tpl_vars['or_doctor_selected']), $this);?>

					</select>
				</td>
				 <td valign="middle"><span id="or_doctor_msg"><?php echo $this->_tpl_vars['error_input']; ?>
</span></td>
		</tr>

		<!--<tr>
			<td valign="middle"><label>Consent Signed: <?php echo $this->_tpl_vars['required_mark']; ?>
</label></td>
			<td><?php echo smarty_function_html_radios(array('name' => 'or_consent_signed','options' => $this->_tpl_vars['or_consent_signed'],'selected' => $this->_tpl_vars['or_consent_signed_selected']), $this);?>
</td>
			<td valign="middle"><span id="or_consent_signed_msg"><?php echo $this->_tpl_vars['error_input']; ?>
</span></td>
			<td></td>
		</tr>

		<tr>
			<td valign="middle"><label>Case: <?php echo $this->_tpl_vars['required_mark']; ?>
</label></td>
			<td>
				<table cellpadding="5" cellspacing="5">
					<tr>

						<td width="90px">Service:<br/><?php echo smarty_function_html_radios(array('name' => 'or_request_case','options' => $this->_tpl_vars['or_request_case_service'],'separator' => "<br/>",'selected' => $this->_tpl_vars['or_request_case_selected']), $this);?>
</td>

						<td>Pay:<br/><?php echo smarty_function_html_radios(array('name' => 'or_request_case','options' => $this->_tpl_vars['or_request_case_pay'],'separator' => "<br/>",'selected' => $this->_tpl_vars['or_request_case_selected']), $this);?>
</td>
					</tr>
				</table>
			</td>
			<td valign="middle"><span id="or_request_case_msg"><?php echo $this->_tpl_vars['error_input']; ?>
</span></td>
			<td></td>


		</tr> -->


	</table>
	</fieldset>

	<fieldset>
	<legend>Patient Information</legend>
	<table>
		<tr>
			<td width="210px"><label>Patient Name:</label> <?php echo $this->_tpl_vars['required_mark']; ?>
 </td>
			<td width="160px"><strong><?php echo $this->_tpl_vars['patient_name']; ?>
</strong></td>
			<td><?php echo $this->_tpl_vars['patient_select_button']; ?>
</td>
			<td><span id="patient_name_msg"><?php echo $this->_tpl_vars['error_input']; ?>
</span></td>
		</tr>
		<tr>
			<td><label>Patient Gender:</label></td>
			<td><?php echo $this->_tpl_vars['patient_gender']; ?>
</td>
			<td><?php echo $this->_tpl_vars['error_input']; ?>
</td>
		</tr>

		<tr>
			<td><label>Patient Age:</label></td>
			<td><?php echo $this->_tpl_vars['patient_age']; ?>
</td>
			<td><?php echo $this->_tpl_vars['error_input']; ?>
</td>
		</tr>
		<tr>
			<td><label>Patient Address:</label></td>
			<td><?php echo $this->_tpl_vars['patient_address']; ?>
</td>
			<td><?php echo $this->_tpl_vars['error_input']; ?>
</td>
		</tr>
	</table>
	</fieldset>

	<!--<fieldset>
		<legend>Pre-operation Details</legend>
		<table>
			<tr>
				<td width="210px"><label>Date and time of operation:</label></td>
				<td width="160px"><?php echo $this->_tpl_vars['or_operation_date_display'];  echo $this->_tpl_vars['or_operation_date']; ?>
</td>
				<td><?php echo $this->_tpl_vars['or_operation_dt_picker']; ?>
</td>
				<td><?php echo $this->_tpl_vars['or_operation_calendar_script']; ?>
</td>
			</tr>

			<tr>
				<td><label>Estimated length of operation:</label></td>
				<td><?php echo $this->_tpl_vars['or_est_op_length']; ?>
</td>
				<td></td>
			</tr>

			<tr>
				<td valign="middle"><label>Case classification:</label> <?php echo $this->_tpl_vars['required_mark']; ?>
</td>
				<td><?php echo smarty_function_html_radios(array('name' => 'or_case_classification','options' => $this->_tpl_vars['or_case_classification'],'separator' => "<br/>",'selected' => $this->_tpl_vars['or_case_classification_selected']), $this);?>
</td>
				<td valign="middle"><span id="or_case_classification_msg"><?php echo $this->_tpl_vars['error_input']; ?>
</span></td>
			</tr>

			<tr>
				<td valign="middle"><label>Pre-operative diagnosis:</label></td>
				<td><?php echo $this->_tpl_vars['pre_operative_diagnosis']; ?>
</td>
				<td></td>
			</tr>
			<tr>
				<td valign="middle"><label>Operation procedure:</label></td>
				<td><?php echo $this->_tpl_vars['operation_procedure']; ?>
</td>
				<td></td>
			</tr>
		</table>
	</fieldset>-->

	<fieldset>
		<legend>Work-Ups Done</legend>
		<table>
			<tr>
				<td width="210px" valign="middle"><label>Special requirements:</label> <?php echo $this->_tpl_vars['required_mark']; ?>
</td>
				<td width="180px"><?php echo smarty_function_html_checkboxes(array('name' => 'or_special_requirements','options' => $this->_tpl_vars['or_special_requirements'],'separator' => "<br/>",'selected' => $this->_tpl_vars['or_special_requirements_selected'],'id' => 'or_special_requirements'), $this);?>
</td>
				<td valign="middle"><span id="special_req_msg"><?php echo $this->_tpl_vars['error_input']; ?>
</span></td>
			</tr>
		</table>
	</fieldset>

	<!--Added by Cherry 02-17-10-->
	<fieldset>
		<legend>Schedule</legend>
		<table>
			<tr>
				<td width="210px"><label>Date of Operation:</label> <?php echo $this->_tpl_vars['required_mark']; ?>
 </td>
				<td width="160px"><!--<?php echo $this->_tpl_vars['or_operation_date_display']; ?>
--><?php echo $this->_tpl_vars['or_operation_date']; ?>
</td>
				<td><?php echo $this->_tpl_vars['or_operation_dt_picker']; ?>
</td>
				<td><?php echo $this->_tpl_vars['or_operation_calendar_script']; ?>
</td>
				<!--<td valign="middle"><span id="or_operation_date_msg"><?php echo $this->_tpl_vars['error_input']; ?>
</span></td> -->
			</tr>
			<!--
			<tr id="op_time">
				<td width="210px" valign="middle"><label>Time of Operation:</label> <?php echo $this->_tpl_vars['required_mark']; ?>
 </td>
				<td width="180px"><?php echo smarty_function_html_radios(array('name' => 'time_of_operation','options' => $this->_tpl_vars['time_of_operation'],'separator' => "<br/>",'selected' => $this->_tpl_vars['time_of_operation_selected'],'onClick' => "checkType(this.value);"), $this);?>
</td>
				<!--<td valign="middle"><span id="time_of_operation_msg"><?php echo $this->_tpl_vars['error_input']; ?>
</span></td>
			</tr>-->
		</table>

	</fieldset>

	<!-- End Cherry-->

	<?php echo $this->_tpl_vars['package_id']; ?>


	<?php echo $this->_tpl_vars['or_main_submit']; ?>

	<?php echo $this->_tpl_vars['or_main_cancel']; ?>

	<?php echo $this->_tpl_vars['or_main_print']; ?>

	<?php echo $this->_tpl_vars['patient_pid']; ?>

	<?php echo $this->_tpl_vars['encounter_nr']; ?>

	<?php echo $this->_tpl_vars['hospital_number']; ?>

	<?php echo $this->_tpl_vars['mode']; ?>

	<?php echo $this->_tpl_vars['submitted']; ?>

	<?php echo $this->_tpl_vars['dept_nr']; ?>

	<?php echo $this->_tpl_vars['op_room']; ?>

	<?php echo $this->_tpl_vars['op_nr']; ?>

	<?php echo $this->_tpl_vars['refno']; ?>

	<?php echo $this->_tpl_vars['or_request_nr']; ?>

	<?php echo $this->_tpl_vars['form_end']; ?>

</div>

</body>
</html>