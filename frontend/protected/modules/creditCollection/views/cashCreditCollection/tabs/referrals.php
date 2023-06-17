<?php 
$this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'patient-referral-list-grid',
	'enableSorting' => false,
	'ajaxUpdate' => true,
	'dataProvider'=> $referrals->getEncounterReferrals(),
	'columns'=>array(
		array('name'=> 'entry_date', 'header' => 'Date'),
		array('name'=> 'alt_name', 'header' => 'Account'),
		array('name'=> 'sub_account', 'header' => 'Sub Account'),
		array('name'=> 'control_no', 'header' => 'Control No.'),
		array('name'=> 'amount', 'header' => 'Amount'),
		array('name'=> 'create_id', 'header' => 'Encoder'),
		array('name'=> 'remarks', 'header' => 'Remarks'),
		array(
			'class' => 'bootstrap.widgets.TbButtonColumn',
			'template' => '{update} {delete}',
			'buttons' => array(
					'update' => array(
						'visible' => 'true',
						'type' => '',
						'click'=>"function(){
							if($('#encounter_nr').val() == ''){
								Alerts.alert({
						            icon: 'fa fa-times',
						            title: 'Error',
						            content: 'Please select a patient first',
						            callback: function (result) {
						                Alerts.close();
						            }
						        });
								return false;
							}else{
								$.ajax({
									type:'GET',
									url: $(this).attr('href'),
									success: function(data){
										var obj = JSON.parse(data);
										
										if(obj.result == 'failed'){
											Alerts.alert({
									            icon: 'fa fa-times',
									            title: 'Error',
									            content: obj.errormsg,
									            callback: function (result) {
									                Alerts.close();
									            }
									        });
										}else{
											console.log(obj);
											$('#referral-entry-modal .row-fluid').html(obj.modal);
								  			$('#referral-entry-modal').modal();
								  			$('#referral-entry-header').html('Process Referral Entry');
											
											if(obj.model.length > 0){
											    $.each(obj.model, function(key, value) {
											     	$('#sub_account').append($('<option></option>')
									                    .attr('value', value.id)
									                    .text(value.name.toUpperCase())); 
												});
											}

											$('#entry_date').val(obj.details.entry_date);
											$('#account').val(obj.details.account);
											$('#sub_account').val(obj.details.sub_account);
											$('#control_no').val(obj.details.control_no);
											$('#amount').val(obj.details.amount);
											$('#remarks').val(obj.details.remarks);
											$('#referral_id').val(obj.details.id);

											if(obj.details.balance){
												$('#account_fund').val(obj.details.balance);
												$('#fund').val('₱ '+ numberWithCommas(obj.details.balance));
											}
											else{
												$('#account_fund').val(-1);
												$('#fund').val('N/A');
											} 

											return false;

										}
									}
								});
								return false;
							}
						}"
				),
					'delete' => array(
						'visible' => 'true',
						'type' => '',
						'click' => "function(){
							if($('#encounter_nr').val() == ''){
								Alerts.alert({
						            icon: 'fa fa-times',
						            title: 'Error',
						            content: 'Please select a patient first',
						            callback: function (result) {
						                Alerts.close();
						            }
						        });
								return false;
							}else{
								var thisurl = $(this).attr('href');
								
								Alerts.confirm({
							        title: 'Are you sure you want to delete this referral?',
							        content: 'This will update patient\'s referral',
							        callback: function(result) {
							        	if(result){

											$.ajax({
												type:'GET',
												url: thisurl,
												success: function(data){
													var obj = JSON.parse(data);

													if(obj.result == 'success'){
														$.fn.yiiGridView.update('patient-referral-list-grid');
													  	$('#actualBalance').val('₱ '+ numberWithCommass(obj.actualBal));
														$('#remainBalance').val('₱ '+ numberWithCommass(obj.remainBal));
														Alerts.alert({
												            icon: 'fa fa-check',
												            title: 'Success',
												            content: 'Successfully Deleted Referral',
												            callback: function (result) {
												                Alerts.close();
												            }
												        });

													}else if(obj.result == 'failed'){
														Alerts.alert({
												            icon: 'fa fa-times',
												            title: 'Error',
												            content: obj.errormsg,
												            callback: function (result) {
												                Alerts.close();
												            }
												        });
													}
													
													return false;
												},
												error : function(data){
													console.log(data);
													return false;
												}
											});
							        	}
							        }
							    });
								
								return false;
							}
						}"

				)
			),
		),
	),
));
 ?>