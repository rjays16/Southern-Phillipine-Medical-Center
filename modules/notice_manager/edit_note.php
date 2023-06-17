<?php 
	/*include('db_connection.php');*/ 
	require_once ('functions.php');
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require($root_path .'include/inc_special_functions_permission.php');
	require "{$root_path}include/care_api_classes/class_personell.php";
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	$setHospitalInfo=true;
    $objInfo = new Hospital_Admin($setHospitalInfo);
	
 ?>

<?php 
 
   $note = update_note_by_id($_GET["id"]);
   $timefr= date('h:i a',strtotime($note['time_from']));
   $timeto= date('h:i a',strtotime($note['time_to']));
   $split1=explode(' ', $timefr);
   $split2=explode(' ', $timeto);
   // var_dump($note);die;
   if (!$note) {
    redirect_to("notice_manager.php");
          }
	function time_names($time){

		$time1 = explode(':', $time);
		$time2 = $time1[0] . ':' . $time1[1];
		$time3= date('h:i',strtotime($time2));
		return $time3;
	}
 ?>

<?php 
	global $db;
	if(isset($_POST['btnupdate']))
	{

	$file 	 = $_FILES['attchmnt']['name'];
	$tmp_dir = $_FILES['attchmnt']['tmp_name'];
	$imgSize = $_FILES['attchmnt']['size'];
	
	move_uploaded_file($tmp_dir,"files/".$file);
	
		$_POST['file_name'] = $file;
		$status 		= $_POST["status"];
		$id 			= $_POST["note_id"];
		$category 		= $_POST['category'];
		$date_published = $_POST['date_pub'];
		$note_date		= date('Y-m-d',strtotime($_POST['fr_date']));
		$time_from 		= $_POST['fr_time'];
		$time_to 		= $_POST['to_time'];
		$file 			= $_POST['file_name'];
		$venue 			= $_POST['venue'];
		$subject 		= $_POST['subject'];
		$f_time = strtotime($time_from);
		$t_time = strtotime($time_to);
		$check  = ($f_time - $t_time) / 60;
	    
	if($check >= 0 || $status == 0) {
		$errMSG = "Invalid date/time data detected";
		echo "<script>
				setTimeout(function() {
		 			$('#error').fadeOut();
				}, 3000 );
			  </script>";
	} else {
		$fro_time = date('H:i',strtotime($time_from));
		$too_time = date('H:i',strtotime($time_to));
		
		$result = $db->Execute("UPDATE seg_notice_tbl SET category = ". $db->qstr($category) .", date_published = ". $db->qstr($date_published) .", note_date = ". $db->qstr($note_date) .", time_from = ". $db->qstr($fro_time) .", time_to = ". $db->qstr($too_time) .", venue = ". $db->qstr($venue) .", status = ". $db->qstr($status) .", subject = ". $db->qstr($subject) .",notice_attchmnt = ".$db->qstr($file)." WHERE note_id = ". $db->qstr($id) ." LIMIT 1");        
    	   
        if ($result) {
        	$successMSG = "Notice Edition Successful";
			echo "<script>
				setTimeout(function() {
		 			$('#error').fadeOut();
				}, 3000 );
			  </script>";
	      	header('location: notice_manager.php');
	    	} else {
	      header('location: edit_note.php');
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

    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="bootstrap/css/datepicker.css" type="text/css"> 	
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/datepicker.js"></script>

</head>
<body>

    <div class="all-content-wrapper">
	
		<section class="container">
			<div class="form-group custom-input-space has-feedback">
				<div class="page-heading">
					<h3>Update Notice</h3>
				</div>
				<div class="page-body clearfix">
					<!-- form -->
					<form enctype="multipart/form-data" method="post" action="edit_note.php?id=<?php echo urlencode($note["note_id"]); ?>">
					<div class="row">
						<div class="col-md-offset-1 col-md-10">
							<div class="panel panel-default">

								<div class="panel-body">


							<?php
								if(isset($errMSG)){
										?>
							            <div class="alert alert-danger">
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
										<input type="hidden" name="note_id" value="<?php echo $note["note_id"]; ?>">

										<label for="category" class="col-md-2 required" >Category :</label>
										<div class="form-group">
										<div class="col-md-10">
											<select class="form-control select picker show-tick" name="category" required >
												<option value="<?php echo htmlentities($note["category"]); ?>"><?php echo $note["category"]; ?></option>
												<?php 
												$category = $note["category"];

												if ($category == "Meeting") {
													echo "<option value=\"Orientation\">Orientation</option>";
												} else {
													echo "<option value=\"Meeting\">Meeting</option>";
												} ?>
											</select>
										</div>
										</div>
										<br><br><br>

										<?php 
										/*$today = date("m/d/Y");*/
										// var_dump($root_path.'modules/notice_manager/files/'.$note['notice_attchmnt']);die;
										$names = $note['notice_attchmnt'];

										?>
										<label for="date_pub" class="col-md-2 required" >Date Published:</label>
										<div class="form-group">
										<div class="col-md-4">
										<div  class="input-group">
											<input type="text" readonly class="form-control" name="date_pub" value="<?php echo htmlentities($note["date_published"]); ?>" id="date1" />
											<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
										</div>
										</div>
										</div>
										<br><br><br>

										<label for="date_of_meeting" class="col-md-2" >Date Scheduled:</label>
										<div class="form-group">
										   <div class="col-md-4" >
										    	<div class="input-group add-on">
										    		<input readonly required type="text" placeholder="DD-MM-YYYY" name="fr_date" class="datepicker-here form-control" value="<?php echo htmlentities(date('F d, Y',strtotime($note["note_date"])));?>" id="date2">
										          	<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
										   		</div>
										    </div>

										    <div class="col-md-3">
											<div class="input-group add-on">
												<input id="time" class="form-control" maxlength="5" placeholder="00:00" data-mask="00:00" value="<?= htmlentities(time_names($note['time_from']))?>" type="text" required />
												<span class="input-group-addon">
													<select id="suffixes" class="time_notice">
													<option value="AM" <?=$split1[1] == 'am' ? ' selected="selected"' : '';?>>A.M</option>
													<option value="PM" <?=$split1[1] == 'pm' ? ' selected="selected"' : '';?>>P.M</option>
												</select></span>
											</div>
											</div>

											 <div class="col-md-2">
											<div class="form-group">
												<input type="hidden" class="form-control" value="<?= htmlentities(time_names($note['time_from']))?>" name="fr_time" id="display_time">
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
												<input id="time2" class="form-control" maxlength="5" placeholder="00:00" data-mask="00:00" value="<?= htmlentities(time_names($note['time_to']))?>" type="text" required />
												<span class="input-group-addon"><select id="suffixes2" class="time_notice2">
													<option value="AM" <?=$split2[1] == 'am' ? ' selected="selected"' : '';?> >A.M</option>
													<option value="PM" <?=$split2[1] == 'pm' ? ' selected="selected"' : '';?> >P.M</option>
												</select></span>
											</div>
											</div>

											<div class="col-md-2">
											<div class="form-group">
												<input type="hidden" class="form-control" value="<?= htmlentities(time_names($note['time_to']))?>" name="to_time" id="display_time2">
											</div>
											</div>
										</div>
										<br><br>

										<label for="venue" class="col-md-2 required" >Venue:</label>
										<div class="form-group">
										<div class="col-md-10">
											<input type="text" class="form-control" name="venue" value="<?php echo htmlspecialchars($note["venue"]); ?>" required />
										</div>
										</div>
										<br><br><br>

										<label for="subject" class="col-md-2 required" >Subject:</label>
										<div class="form-group">
										<div class="col-md-10">
											<input type="text" class="form-control" name="subject" value="<?php echo htmlspecialchars($note["subject"]); ?>" required />
										</div>
										</div>
										<br><br><br>
										<label for="attchmnt" class="col-md-2 required" >New Attachment:</label>
										<div class="form-group">
										<div class="col-md-6">
											<input type="file" class="form-control" name="attchmnt" value="<?php $root_path.'modules/notice_manager/files/'.$note['notice_attchmnt'] ?>" />
										</div>
										</div>
										<br><br>
										
										<div class="form-group">
										<div class="col-md-6">
											<label>Current Attachment: <?php echo "<a href='download.php?filename=$name'</a>". $names ?></a></label>
										</div>
										</div>

										<br><br>
										<label for="active" class="col-md-2 required" >Active:</label>
										<div class="form-group">
										<div class="col-md-6">

										<?php if($note['status'] == 0 ) {

												echo '<input type="hidden" name="status" value="0" />';
												echo '<input disabled type="checkbox" name="status" value="0" id="chk_active" />';
												

											} else {
												echo '<input type="hidden" name="status" value="1" />';

												echo '<input disabled checked type="checkbox" name="status" value="1" id="chk_active" />';
												
											}
										?>	
									        <!-- <input type="hidden" name="status" id="status" value=""> -->
										</div>
									   </div>
									</div>	

									<div class="clearfix"></div>
									<div style="float: right;" class="form-group">
										<input type="submit" name="btnupdate" value="Update" class="btn btn-primary">

										<a class="btn btn-secondary  btn-danger" href="notice_manager.php"><i class="glyphicon glyphicon-remove-sign" aria-hidden="true"></i> &nbsp; Cancel</a>
									</div>
									<br>
								</div>
							</div>
						</div>
					</div>
					</form>
					<!-- #END# form -->
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

	// function checkStatus(){

	// 	var date1 	= $('#date1');
	// 	var date2 	= $('#date2');
		
	//    	if (Date.parse(date1.val()) <= Date.parse(date2.val())) {
	//    		$('#chk_active').prop('checked', true);
	//    		$('#status').val('1');
	//    	}else {
	//    		$('#chk_active').prop('checked', false);
	//    		('#status').val('0');
	//    		}
	// }

	$(document).on('ready',function(e){
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

		 var date2 = Date.parse(date + " " + timez2);
		 if (today <= date2) {
		   	$('#chk_active').prop('checked', true);
		   	$('#status').val('1');
		   }else {
		   	$('#chk_active').prop('checked', false);
		   	$('#status').val('0');
		   }
	});

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