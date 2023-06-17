
<?php 

	  include('functions.php'); 
	  include('./roots.php');
	  include($root_path.'include/inc_environment_global.php');

?>


<?php 
   $note = print_note_by_id($_GET["id"]);
?>

<html>
<head>
	<title>SegHIS - Southern Philippines Medical Center Hospital Information System</title>
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css">

<style>

@page {
    size: auto;
    margin: 0;
  }
	
body {
	background-color: #f9f9f9;
}

.container {
	width:90%;
	margin:auto;
}
		
.table {
    width: 100%;
    margin-bottom: 1px;
}

.table-striped tbody > tr:nth-child(odd) > td,
.table-striped tbody > tr:nth-child(odd) > th {
}

@media print{
#print {
	display:none;
	}
@page
   {
    size: 8.5in 13in;
    size: portrait;
  }
}
	
</style>

<script>
function printPage() {
    window.print();
}
</script>
		
</head>


<body>
	<div class="container">
	
		<br/>
		<div style="margin-top:-30px;">
			<br>

			<center><p>Republic of the Philippines</p></center>
			<center><p style="margin-top:-10px;">Department of Health</p></center>
			<center><p style="margin-top:-10px;"><b>Center of Health Development of Davao Region</b></p></center>
			<center><p style="margin-top:-10px;"><b>SOUTHERN PHILIPPINES MEDICAL CENTER</p></b></center>
			<center><p style="margin-top:-10px;"><b>HOSPITAL INFORMATION SYSTEM</p></b></center>
		</div>
		

		<?php 
		  $id = $_GET['id'];
			$query = $db->Execute("SELECT * FROM seg_notice_tbl WHERE note_id= '$id' ");
			// $result = mysql_query($query) or die(mysql_error());
			$row = FetchRow($result);

		 ?>
		 <br>
		 <br>
		<div>
		<b>Date Published: <?php echo date('F d, Y',strtotime($row['date_published'])); ?></b>
        </div>

		<center><h3>NOTICE OF <?php echo strtoupper($row['category']) ?> </h3></center>
		<div>
			<br/>
			<p style="margin-top:-10px;"><b>Subject : </b><?php echo $row['subject']; ?></p>
			<p style="margin-top:-10px;"><b>Date  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </b><?php echo date('F d, Y', strtotime($row['note_date'])); ?></p>
			<p style="margin-top:-10px;"><b>Venue  &nbsp;&nbsp;&nbsp;: </b><?php echo $row['venue']; ?></p>
		</div>

		<br/>

		<div>
			<p style="margin-top:-10px;"><b>Acknowledged By : </b></p>
		</div>
        			
		<br/>
						<table class="table table-striped">
						  <thead>
								<tr>
									<?php $get = $row['sess_user']; ?>
									<th width="300"><center>Name</center></th>
									<th width="300"><center>Department</center></th>
									<th width="300"><center>Date / Time</center></th>
								</tr>
						  </thead>   
						  <tbody>
						  	<?php
								$noticeId = $_GET['id'];
								$query = $db->Execute("SELECT * FROM `seg_notice_acknledgmnts` WHERE notice_id = '$noticeId' ");
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
		<div id="print_dt" style="position:fixed; font-size:12px;margin-top:55px;left:20;width:97%;">
			<p>Date Generated : <?php echo 	date('F d, Y h:i A'); ?></p>

			<button style="margin-top:-50px;float:right;" class="btn btn-primary" type="submit" id="print" onclick="printPage()"><span class="glyphicon glyphicon-print"></span> Print</button>

		</div>
</body>
</html>