<?php 
	/*include('db_connection.php'); */
	require_once ('functions.php');

	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require "{$root_path}include/care_api_classes/class_notice.php";
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	$setHospitalInfo=true;
    $objInfo = new Hospital_Admin($setHospitalInfo);
 ?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo 'SegHIS - '.$objInfo->hosp_info['hosp_name'];?></title>

    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css">

</head>
<body>

    <div class="all-content-wrapper">
	
		<section style="width:100%;" class="container">
			<div class="form-group custom-input-space has-feedback">
				<br>
				<div class="page-body clearfix">
					<div class="panel panel-default">

						<div class="panel-heading"><a href="javascript:history.back()"><span class="glyphicon glyphicon-backward"></span></a>&nbsp;&nbsp;<strong>Acknowledged By:</strong>
							<a style="float: right;" href="generatePdf.php?id=<?php echo urlencode($_GET['note_id']); ?>" title="Preview" "><span class="glyphicon glyphicon-print"></span> Print</a>
						</div>

						<div class="panel-body">
							<!-- My Documents start -->
							<div id="print-data" class="table-responsive">
								<table class="table table-striped table-hover js-basic-example dataTable">
									<thead>
										<tr>
											<th width="200px"><center>Name</center></th>
											<th width="350px"><center>Department</center></th>
											<th width="350px"><center>Date/Time</center></th> 
										</tr>
									</thead>
									<tbody>
									<?php
									$noticeId = $_GET['note_id'];
									$query = $db->Execute("SELECT * FROM seg_notice_acknledgmnts WHERE notice_id = '$noticeId' ");
									
										while($row = $query->FetchRow()){
										?>
										<tr>
											<td><center><?php echo $row['sess_user']; ?></center></td>
											<td><center><?php echo $row['departmnt']; ?></center></td>
											<td><center><?php echo date('F d, Y h:i A',strtotime($row['date_ack'])); ?></center></td>
										</tr>
										<?php
											}
										?>
									</tbody>
								</table>
							</div>
						</div>
					</div>

			</div>
		</section>
    </div>

</body>
</html>