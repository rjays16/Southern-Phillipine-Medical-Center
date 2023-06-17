 
	<div class="modal fade" id="edit" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content modal-col-danger">
				<div class="modal-header">
					<h4 class="modal-title" id="smallModalLabel">Confirmation:</h4>
				</div>
				<div class="modal-body">
					&nbsp;&nbsp;&nbsp;&nbsp;<strong>Are you sure you want to Delete this Notice ?</strong>


				<div class="container-fluid">
					<form method="post" action="update_del.php">
						<input type="hidden" name="noteID" style="width:300px;" class="form-control" id="noticeID">
					<div class="modal-footer">
						<input type="submit" name="update" class="btn btn-primary" value="Delete"><!-- <span class="fa fa-check"></span> &nbsp; --></button>
						<button type="button" class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove-sign"></span> Cancel</button>
					</div>
					</form>
				</div>
				</div>
				
			</div>
		</div>
	</div>