
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

<?php

	global $db;
	$curdate = $db->qstr(date('Y-m-d g:i A'));

	$db->Execute("UPDATE seg_notice_tbl SET status='0' WHERE date(concat(note_date,' ',time_to)) < '$curdate' ");
	
	if(isset($_GET['delete_id']))
	{	
	$id = $_GET['delete_id'];
	$delete = 0;

		$stmt = $db->prepare('UPDATE seg_notice_tbl 
									SET is_deleted =?, 
								    WHERE note_id=?');
			/*$stmt->bindParam(':del',$pName);
			$stmt->bindParam(':nid',$id);*/

		    $cons = array($pName,$id);
		    $rs = $db->Execute($stmt, $cons);


			if($rs)
			{
				$successMSG = "Notice Succesfully Deleted ...";
				header("refresh:2;notice_manager.php");
			}
			else
			{
				$errMSG = "Notice Deletion Failed ...";
			} 
	}

?>

<?php $note_set = display_all_note(); ?>

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

<style>

input {
  border: 0;
  background: transparent;
  -webkit-appearance: none;
  -moz-appearance: none;
  -ms-appearance: none;
  -o-appearance: none;
  appearance: none;
}

input:focus{
  outline: 0;
}
.search-placeholder{
  background-color: #A9A9A9;
  display: inline-block;
  padding: 5px; 
  border-radius: 10px;
  font-size:11px;
}
</style>

</head>
<body>

    <div class="all-content-wrapper">
	
		<section style="width:100%;" class="container">
			<div class="form-group custom-input-space has-feedback">
				<div class="page-heading">
					<h3>Notice Manager</h3>
				</div>
				<div class="page-body clearfix">
					

					<div class="panel panel-default">

						<div class="panel-heading">
						<div class="search-placeholder">
							<span class="glyphicon glyphicon-search "></span> &nbsp;
							<input type="text" id="myInput" placeholder=" Search Date or Subjects..">
						</div>
							<div style="float: right;">
							<a class="btn btn-sm btn-primary btn-plus" title="Create Notice" onclick="return checkPerm()" id="creates" href="index.php" >
								<i class="glyphicon glyphicon-plus" aria-hidden="true"></i> &nbsp; Add New Notice
							</a>
							</div>
						</div>
							
						<div class="panel-body">
							<!-- My Documents start -->
							<div class="table-responsive">
								<link href="../billing_new/css/dataTables/bootstrap.min.css" rel="stylesheet">   
								<script src="../billing_new/js/dataTables/jquery.min.js"></script>
								<link rel="stylesheet" href="../billing_new/css/dataTables/jquery.dataTables.min.css"></style>
								<script type="text/javascript" src="../billing_new/js/dataTables/jquery.dataTables.min.js"></script>
								<script type="text/javascript" src="../billing_new/js/dataTables/bootstrap.min.js"></script>
							<table id="noteTable" class="table table-striped table-hover js-basic-example dataTable">
									<thead>
										<tr>
											<th style="font-size:12px;">Category</th>
											<th style="font-size:12px;">Date Published</th>
											<th style="font-size:12px;">Date Scheduled</th>
											<th style="font-size:12px;">Time</th>
											<th style="font-size:12px;"><center>Venue</center></th>
											<th style="font-size:12px;"><center>Subject</center></th>
											<th style="font-size:12px;">Attachment</th>
											<th style="font-size:12px;">Active</th>
											<th style="font-size:12px;">Action</th>
										</tr>
									</thead>
									<?php 

										global $db;
										$sql = "SELECT snt.`note_id` AS id, snt.`category` AS Category, snt.`date_published` AS date_pub, snt.`note_date` AS Dates, snt.`time_from` AS time_f, snt.`time_to` AS time_t, snt.`venue` AS venue, snt.`subject` AS subject, snt.`status` AS status, snt.`notice_attchmnt` AS attachment, snt.`is_deleted` AS deleted FROM seg_notice_tbl AS snt WHERE snt.`is_deleted`= '1' ORDER BY snt.`note_date` DESC";
										/*var_dump($sql);die();*/
										$result = $result = $db->Execute($sql);
										// Data Table 
												while($row = $result->FetchRow()){
													
													$today = strtotime(date('F d,Y g:i a'));

													$date= strtotime(date('F d, Y',strtotime($row['Dates'])).' '.date('g:i a',strtotime($row['time_t'])));
													
													$name =$row['attachment'];
													$encode = urlencode($row['id']);
													$id= $row['id'];		    

													if(!empty($row['medsocwork'])) {
														$soc_name = $row['medsocwork'];
													}else{
														$soc_name = $row['medsocname'];
													}	
													echo "<tr>";
										            // echo "<td align = 'center'>" . $row['id'] . "</td>";
										            // echo "<td align = 'center'>" . $row['ward']. "</td>"; 
										            
										            echo "<td align = 'center' style='font-size:12px;'><a href=list_of_ack.php?note_id=$id</a>" . $row['Category']. "</td>";  
										            echo "<td align = 'center' style='font-size:12px;'>" . $row['date_pub']. "</td>";  
										            echo "<td align = 'center' style='font-size:12px;'>" . date('F d, Y',strtotime($row['Dates'])). "</td>";  
										            echo "<td align = 'center' style='font-size:12px;'>" . date('h:i a',strtotime($row['time_f'])).' - '.date('h:i a',strtotime($row['time_t'])). "</td>";  
										            // echo "<td align = 'center'>" . $row['time_t']. "</td>";  
										            echo "<td align = 'center' style='font-size:12px;'>" . $row['venue']. "</td>";
										            echo "<td align = 'center' style='font-size:12px;'>" . $row['subject']. "</td>";
										            // echo "<td align = 'center'>" . $row['status']. "</td>";   
										            echo "<td align = 'center' style='font-size:12px;'><a href='download.php?filename=$name'</a>"  . $row['attachment']. "</td>";
										            if($today <= $date){
										            	echo "<td align = 'center' style='font-size:12px;'>" .'<span class="glyphicon glyphicon-ok"></span>'. "</td>";
										            }else{
										            	echo "<td align = 'center' style='font-size:12px;'>" .'<span class="glyphicon glyphicon-remove"></span>'. "</td>";
										            	 
										            }
										            echo "<td align = 'center' style='font-size:12px;'><div class='form-group'><a href=edit_note.php?id=$encode <span class='glyphicon glyphicon-edit edit2'></span></a><button style='background-color: transparent;border:none;overflow:hidden;' type='button' class='edit' value='$id'><span class='glyphicon glyphicon-remove-sign'></span>
												</button></div></td>";
										          
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

	<?php include('modal_del.php');  ?>
	<script src="custom.js"></script>

    <script>
    	$('#creates').click(function()
			{	
				var canCreate = '<?php echo $cancreateManager ?>';
			    if(!canCreate){
    			alert("You don\'t have permission to Create this notice");
    			return false;
    		}else{
    			return true;
    			}
			});

    	$('.edit2').click(function()
			{		
				var canEdit = '<?php echo $caneditManager ?>';

			    if(!canEdit){
    			alert("You don\'t have permission to Edit this notice");
    			return false;
    		}else{
				var result = confirm("Are you sure you want to Edit this notice?");
    			if(result){
    				return true;
    			}else{
    				return false;
    				}
    			}
			});

    	$('.edit').click(function()
			{	
				var canDel = '<?php echo $candeleteManager ?>';

				if(!canDel){
					alert("You don\'t have permission to Delete this notice");
    				return false;
				}else{
					return true;
				}
			});
    		
    /*Permission Checker*/
    	
	/*Call Data Table*/
    	$(document).ready(function(){
	   oTable = $('#noteTable').DataTable( {
	   			"order": [[ 2, 'desc' ]],
	   				language: {
			        search: "_INPUT_",
			        searchPlaceholder: "Search..."
			    },	"dom": '<<t>ip>',			
				  "columns": [
				    { "searchable": false },
				    { "searchable": false },
				    null,
				    { "searchable": false },
				    { "searchable": false },
				    null,
				    { "searchable": false },
				    { "searchable": false },
				    { "searchable": false }
				  ]
				
				} ); 
	   $('.dataTables_filter input').attr("placeholder", "Search Date or Subjects...");
	   $('#myInput').keyup(function(){
      oTable.search($(this).val()).draw() ;
})
	});
    	
   		
    </script>

</body>
</html>
