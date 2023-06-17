<?php
// require($root_path.'include/inc_environment_global.php');
//get the id of the session
$sess_log_user = $_SESSION['sess_login_personell_nr'];
//and find the department if name_first is equal to the current session user
require "{$root_path}include/care_api_classes/class_personell.php";

$objpersonell = new Personell;
		
		$row1 = $objpersonell->getPersonellInfo($sess_log_user);

		if ($row1) {
			$department = $row1['dept_name'];
		}
?>

	<div class="modal fade" id="edit" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content modal-col-danger">
				<div class="modal-header">
					<h4 class="modal-title" id="smallModalLabel">Acknowledgement:</h4>
				</div>
				<div class="modal-body">
					&nbsp;&nbsp;&nbsp;&nbsp;<strong>Do you want to acknowledge this Notice ?</strong>


				<div class="container-fluid">
					<form method="post" action="acknowledgement.php">
						<input type="hidden" name="noteID" style="width:300px;" class="form-control" id="noticeID">
						<input type="hidden" style="width:350px;" value="<?php echo $department; ?>" name="depart" style="width:300px;" class="form-control">
						<input type="hidden" name="sess_user" style="width:200px;" class="form-control" value="<?php echo  $_SESSION['sess_login_username']; ?>">

					<div class="modal-footer">
						<input type="submit" name="acknwledge" class="btn btn-primary" value="Yes"><!-- <span class="fa fa-check"></span> &nbsp; --></button>
						<button type="button" class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> No</button>
					</div>
					</form>
				</div>
				</div>
				
			</div>
		</div>
	</div>