<?php 
	/*include('db_connection.php'); */
	require_once ('functions.php');

	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require($root_path .'include/inc_special_functions_permission.php');
	require "{$root_path}include/care_api_classes/class_notice.php";
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	$setHospitalInfo=true;
    $objInfo = new Hospital_Admin($setHospitalInfo);

 ?>

<?php session_start(); ?>
<?php 
	$active_note = display_all_active_orientation(); 

	function time_name($time){
		$time = explode(':', $time);
		if ($time[0] >= 0 && $time[0] < 12) {
			
			if($time[0] == '00'){		

				$time[0] = '12';

				}
				$tfn = $time[0] . ':' . $time[1] . " AM";

			} else {
					if($time[0] !='12'){		

					$time[0] = $time[0] - '12';

				}
					
					$tfn = $time[0] . ':' . $time[1] . " PM";
						}
		return $tfn;
	}
?>

<?php 
if (!$_SESSION['sess_login_username']) {
         $errMSG = "Please Login your Credentials via Main Page in order to acknowledge the Notices below ...";
    } 
    else {

    }

 ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo 'SegHIS - '.$objInfo->hosp_info['hosp_name'];?></title>
    
    <script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css">

</head>
<body>
    <div class="all-content-wrapper">
	
		<section class="container">
			<div class="form-group custom-input-space has-feedback">
				<div class="page-heading">
					<h3>List of Orientations</h3>
				</div>
				<div class="page-body clearfix">
					

					<div class="panel panel-default">
						<div class="panel-heading"> List of Active Orientation(s)</div>
						<div class="panel-body">

							<?php
								if(isset($errMSG)){
										?>
							            <div class="alert alert-danger">
							            	<span class="glyphicon glyphicon-warning-sign"></span> <strong><?php echo $errMSG; ?></strong>
							            </div>
							            <?php
								}
								else if(isset($successMSG)){
									?>
							        <div class="alert alert-success">
							              <strong><span class="glyphicon glyphicon-check"></span> <?php echo $successMSG; ?></strong>
							        </div>
							        <?php
								}
							?>  

							<!-- My Documents start -->
							<div class="table-responsive">
								<table class="table table-striped table-hover js-basic-example dataTable">
									<?php 

									global $db;
									$curdate = date('Y-m-d h:i A');

									$db->Execute("UPDATE seg_notice_tbl SET status='0' WHERE concat(note_date,' ',time_to) < '$curdate' ");

									$rows=$db->GetOne("SELECT count(note_id) FROM `seg_notice_tbl` WHERE status = 1 AND is_deleted = 1 AND category = 'Orientation'  ");
										/*$row = mysqli_fetch_row($query);*/

											
										$page_rows = 10;
										$last = ceil($rows/$page_rows);

										if($last < 1){
											$last = 1;
										}
										$pagenum = 1;
										if(isset($_GET['pn'])){
											$pagenum = preg_replace('#[^0-9]#', '', $_GET['pn']);
										}
										if ($pagenum < 1) { 
											$pagenum = 1; 
										} 
										else if ($pagenum > $last) { 
											$pagenum = $last; 
										}
										$limit = 'LIMIT ' .($pagenum - 1) * $page_rows .',' .$page_rows;

										$nquery=$db->Execute("SELECT * FROM `seg_notice_tbl` WHERE status = 1 AND is_deleted = 1 AND category = 'Orientation' ORDER BY note_date DESC $limit");

										$paginationCtrls = '';

										if($rows <= 10) {

										} else {

										if($last != 1){
											
										if ($pagenum > 1) {
									        $previous = $pagenum - 1;
											$paginationCtrls .= '<a href="'.$_SERVER['PHP_SELF'].'?pn='.$previous.'" class="btn btn-primary">Previous</a> &nbsp; &nbsp; ';
											
											for($i = $pagenum-4; $i < $pagenum; $i++){
												if($i > 0){
											        $paginationCtrls .= '<a href="'.$_SERVER['PHP_SELF'].'?pn='.$i.'" class="btn btn-info">'.$i.'</a> &nbsp; ';
												}
										    }
									    }
										$paginationCtrls .= ''.$pagenum.' &nbsp; ';
										
										for($i = $pagenum+1; $i <= $last; $i++){
											$paginationCtrls .= '<a href="'.$_SERVER['PHP_SELF'].'?pn='.$i.'" class="btn btn-info">'.$i.'</a> &nbsp; ';
											if($i >= $pagenum+4){
												break;
											}
										}
									    if ($pagenum != $last) {
									        $next = $pagenum + 1;
									        $paginationCtrls .= ' &nbsp; &nbsp; <a href="'.$_SERVER['PHP_SELF'].'?pn='.$next.'" class="btn btn-primary">Next</a> ';
									    	}
										}
									}

									?>
									<thead>
										<tr>
											<th width="200px">Date Published</th>
											<th>Date Scheduled</th>
											<th>Venue</th>
											<th>Subject</th>
											<th>Attachment</th>
											<th>Acknowledgement</th>
										</tr>
									</thead>
									<tbody>
									
											<?php
											/*$name =$row['notice_attchmnt'];*/
											$s_user = $_SESSION['sess_login_username'];
											$status  = 1;

											
											while($row = $nquery->FetchRow()) {
											$name =$row['notice_attchmnt'];
											?>
											<tr>
											<td><?php echo $row['date_published']; ?></td>
											<td><?php echo date('F d, Y',strtotime($row['note_date'])).' '.time_name($row['time_from']).' - '.time_name($row['time_to']) ?></td>
											<td><?php echo $row['venue']; ?></td>
											<td><?php echo $row['subject']; ?></td>
											<td>
											<a href="download.php?filename=<?php echo $name;?>">
												<?php echo $row['notice_attchmnt']; ?>
											</a>
											</td>
											<td style="text-align: center;">
											<?php 
											$value = $row['note_id'];
											$sess_user = $_SESSION['sess_login_username'];
											$check = $db->Execute("SELECT * FROM seg_notice_acknledgmnts WHERE notice_id ='$value' AND sess_user = '$sess_user'");
											

											if(!$_SESSION['sess_login_username']) {
													echo "<button disabled type=\"button\" class=\"btn btn-danger edit\">
												        <span class=\"glyphicon glyphicon-check\"></span>
												        </button>";
											} 
											else {
													 if($check->FetchRow() > 0 ){
													echo "<button disabled value=\"$value\" type=\"button\" class=\"btn btn-danger edit\">
												        <span class=\"glyphicon glyphicon-check\"></span>
												        </button>";
												    } else{
												    echo "<button value=\"$value\" type=\"button\" class=\"btn btn-danger edit\">
												        <span class=\"glyphicon glyphicon-check\"></span>
												        </button>";
												    }
											}

											?>
											</td>
											</tr>
									<?php
									}
									?>
									</tbody>
								</table>
							</div>
							<!-- #END# My Documents -->
							<div style="float:right;" id="pagination_controls"><?php echo $paginationCtrls; ?></div>
						</div>
					</div>

				</div>
			</div>
		</section>
    </div>
    <?php include('modal_ntc.php'); ?>
	<script src="custom.js"></script>

</body>
</html>
