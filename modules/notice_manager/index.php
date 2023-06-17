<?php 
	/*include('connect.php'); */
	require_once ('functions.php');
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require($root_path .'include/inc_special_functions_permission.php');
	require "{$root_path}include/care_api_classes/class_notice.php";
	$objnotice = new Notice;
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	$setHospitalInfo=true;
    $objInfo = new Hospital_Admin($setHospitalInfo);

  ?>
  
<?php 
if(isset($_POST['btnsave']))
	{
	
	date_default_timezone_set("Asia/Calcutta");
	$date = date('Y-m-d H:i:s');

	date("Y-m-d", strtotime($_POST['date1']));
	$category 		= $_POST['category'];
	$date_published = $_POST['date_pub'];
	$met_date	 	= date('Y-m-d',strtotime($_POST['fr_date']));
	$from_time 		= $_POST['fr_time'];
	$to_time		= $_POST['to_time'];
	$venue 	    	= $_POST['venue'];
	$subject 		= $_POST['subject'];
	$status 	 	= $_POST['status'];

	$deleted = 1;
		
	$file 	 = $_FILES['attchmnt']['name'];
	$tmp_dir = $_FILES['attchmnt']['tmp_name'];
	$imgSize = $_FILES['attchmnt']['size'];

	move_uploaded_file($tmp_dir,"files/".$file);

	$f_time = strtotime($from_time);
	$t_time = strtotime($to_time);
	$check  = ($f_time - $t_time) / 60;

	if($check >= 0 || $status == 0) {
		$errMSG = "Invalid date/time data detected";
		echo "<script>
				setTimeout(function() {
		 			$('#error').fadeOut();
				}, 3000 );
			  </script>";
	} else {
		$fro_time = date('H:i',strtotime($from_time));
		$too_time = date('H:i',strtotime($to_time));
		$_POST['fro_time'] = $fro_time;
		$_POST['too_time'] = $too_time;
		$_POST['fr_date'] = $met_date;
		$_POST['file_name'] = $file;
		$_POST['is_deleted'] = $deleted;
		$_POST['is_date'] = $date;
		$stmt = $objnotice->insertNotice($_POST);
							
			if($stmt)
			{
				$successMSG = "Notice Succesfully Inserted ...";
				header("refresh:2;notice_manager.php");
			}
			else
			{
				$errMSG = "Notice Insertion Failed ...";
			} 
		}
	}

 ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo 'SegHIS - '.$objInfo->hosp_info['hosp_name'];?></title>
   	
	<link href="bootstrap/css/datepicker.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css">

    <link href="bootstrap/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
	<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
	<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>
    <script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.maskedinput.js"></script>
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/datepicker.js"></script>
</head>
<body>

    <div class="all-content-wrapper">
	
		<section class="container">
			<div class="form-group custom-input-space has-feedback">
				<div class="page-heading">
					<h3>Create Notice</h3>
				</div>
				<div class="page-body clearfix">
					<!-- form -->
					<form enctype="multipart/form-data" method="post" id="notice_form">
					<div class="row">
						<div class="col-md-offset-1 col-md-10">
							<div class="panel panel-default">

								<div class="panel-body">

							<?php
								if(isset($errMSG)){
										?>
							            <div id="error" class="alert alert-danger">
							            	<span class="glyphicon glyphicon-info-sign"></span> <strong><?php echo $errMSG; ?></strong>
							            </div>
							            <?php
								}
								else if(isset($successMSG)){
									?>
							        <div class="alert alert-success">
							              <strong><span class="glyphicon glyphicon-ok-sign"></span> <?php echo $successMSG; ?></strong>
							        </div>
							        <?php
								}
							?>  

									<div class="col-md-12">


										<label for="category" class="col-md-2" >Category :</label>
										<div class="form-group">
										<div class="col-md-10">
											<select class="form-control select picker show-tick" name="category" required >
												<option disabled selected>-- Select Category --</option>
												<option value="Meeting">Meeting</option>
												<option value="Orientation">Orientation</option>
											</select>
										</div>
										</div>
										<br><br>

										<label for="date_pub" class="col-md-2" >Date Published:</label>
										<div class="form-group">
										<div class="col-md-4">
										<div  class="input-group">
											<input readonly type="text" class="form-control" name="date_pub" required value="<?php echo date("F d, Y"); ?>"  id="date1" />
											<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
										</div>
										</div>
										</div>
										<br><br>

										<label for="date_of_meeting" class="col-md-2" >Date Scheduled:</label>
										<div class="form-group">
										   <div class="col-md-4" >
										    	<div class="input-group add-on date">
										    		<input readonly id ="date2" required type="text" placeholder="MM-DD-YYYY" name="fr_date" class="datepicker-here form-control">
										          	<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
										   		</div>
										    </div>

										    <div class="col-md-3">
											<div class="input-group add-on">
												<input id="time" type="text" class="form-control" placeholder="00:00" data-mask="00:00" maxlength="5" required />
												<span class="input-group-addon">
												<select id="suffixes" class="time_notice">
													<option value="AM" name="suffix">A.M</option>
													<option value="PM" name="suffix">P.M</option>
												</select></span>
											</div>
											</div>

											 <div class="col-md-2">
											<div class="form-group">
												<input type="hidden" class="form-control" name="fr_time" id="display_time">
											</div>
											</div>

										</div>
										<br><br>

										<label for="date_of_meeting" class="col-md-2" >To:</label>
										<div class="form-group">
										   <div class="col-md-4" >
										    	<div style="display:none;" class="input-group add-on">
										    		<input type="text" name="to_date" class="datepicker-here form-control" id="date2">
										          	<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
										   		</div>
										    </div>

										    <div class="col-md-3">
											<div class="input-group add-on">
												<input id="time2" type="text" class="form-control" placeholder="00:00" maxlength="5" data-mask="00:00" required />
												<span class="input-group-addon">
													<select id="suffixes2" class="time_notice2">
													<option value="AM">A.M</option>
													<option value="PM">P.M</option>
												</select></span>
											</div>
											</div>

											<div class="col-md-2">
											<div class="form-group">
												<input type="hidden" class="form-control" name="to_time" id="display_time2">
											</div>
											</div>
										</div>
										<br><br>

										<label for="venue" class="col-md-2 required" >Venue:</label>
										<div class="form-group">
										<div class="col-md-10">
											<input type="text" class="form-control" name="venue" required />
										</div>
										</div>
										<br><br>

										<label for="subject" class="col-md-2 required" >Subject:</label>
										<div class="form-group">
										<div class="col-md-10">
											<input type="text" class="form-control" name="subject" required />
										</div>
										</div>
										<br><br>

										<label for="attchmnt" class="col-md-2 required" >Attachment:</label>
										<div class="form-group">
										<div class="col-md-6">
											<input type="file" class="form-control" name="attchmnt" />
										</div>
										</div>
										<br><br>

										<label for="active" class="col-md-2 required" >Active:</label>
										<div class="form-group">
										<div class="col-md-6">
											<input disabled checked type="checkbox" id="chk_active" />
									        <input type="hidden" name="status" id="status" value="">
										</div>
									   </div>
									</div>	

									<div class="clearfix"></div>
									<div style="float: right;" class="form-group">
										<button type="submit" name="btnsave" class="btn btn-primary">
								        	<span class="glyphicon glyphicon-save"></span>&nbsp; Save
								        </button>

										<a class="btn btn-secondary  btn-danger" href="notice_manager.php"><span class="glyphicon glyphicon-remove"></span>&nbsp;Cancel</a>
									</div>
									<br>
								</div>
							</div>
						</div>
					</div>
					</form>
					<!-- #END# form -->

				</div>
			</div>
		</section>
    </div>

    <script src="js/jquerym.js"></script>
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

<script type="text/javascript">

$(".glyphicon-calendar").click(function() {
  $("#date2").focus();
});

//     function checkStatus()
// {

// 	var date1 = $('#date1');
// 	var date2 = $('#date2');

//    if (Date.parse(date1.val()) <= Date.parse(date2.val())) {
//    	$('#chk_active').prop('checked', true);
//    	$('#status').val('1');
//    }else {
//    	$('#chk_active').prop('checked', false);
//    	$('#status').val('0');
//    }
// }

$(document).on('change',function(e){
		var today = Date.parse(new Date());
		var date = $('#date2').val();
		var suffix = $('#suffixes').val();	
		var time = $('#time').val();

		var timez = time + " " + suffix;
		
		if (time.value !== "") {
			
		    $('#display_time').val(timez);
		  }
	
		  var suffix2 = $('#suffixes2').val();	
		  var time2 = $('#time2').val();	

		 var timez2 = time2 + " " + suffix2;
		if (time2.value !== "") {
			
		    $('#display_time2').val(timez2);
		  }
		 console.log(timez2);
		 var date2 = Date.parse(date + " " + timez2);
		 if (today <= date2) {
		   	$('#chk_active').prop('checked', true);
		   	$('#status').val('1');
		   }else {
		   	$('#chk_active').prop('checked', false);
		   	$('#status').val('0');
		   }
	});
	
</script>
</body>
</html>
