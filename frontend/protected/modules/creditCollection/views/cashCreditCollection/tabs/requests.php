<?php 

$model2 = new FreeFormModel();
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'referral-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => false,
    'htmlOptions' => array(
        'class' => 'service-form'
    )
));	

echo 'Request Source: ';
echo $form->dropDownList($model2, '', $costCentersList, 
    array(
    	'prompt'=>'- Select Request Source -',
        'id' => "request_source",
        'name' => 'request_source',
        'style' => 'width:200px;height:30px;margin-right:15px'
    )
);

echo 'Status: ';
echo $form->dropDownList($model2, '', $requestStatus,
    array(
    	'prompt'=>'- Select Status -',
        'id' => "status",
        'name' => 'status',
        'style' => 'width:140px;height:30px;margin-right:15px'
    )
);
?>

<!-- NOTE: Synchronous XMLHttpRequest error if datePickerRow from TbActiveForm will be used due to jQuery bug -->
Date: <input type="date" name="date_from" id="date_from" style="width:105px;height:20px;margin-right:5px"> to <input type="date" name="date_to" id="date_to" style="width:105px;height:20px;margin-left:5px">
<!-- end NOTE -->

<?php

$this->endWidget();
?>
<button type="button" class="btn btn-info btn-sm" id="btn_reqsearch" title="Search Patient">	<i class="fa fa-search"></i>
</button>
<?php

$that = $this;
$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'patient-request-list-grid',
    'enableSorting' => false,
	'ajaxUpdate' => true,
    'dataProvider' => $modelGrants->getAllEncounterRequests(),
    'columns' => array(
        array('name' => 'date_requested', 'header' => 'Date Requested'),
        array('name' => 'refno', 'header' => 'Reference'),
        array('name' => 'itemname', 'header' => 'Item Name'),
        array('name' => 'quantity', 'header' => 'Qty'),
        array('name' => 'orig_price', 'header' => 'Amount'),
        array('name' => 'adjstd_price', 'header' => 'Total Due'),
        array(
            'header' => 'Options',
            'class' => 'person.widgets.PersonCustomColumn',
            'value' => function ($row, $data) use ($row, $value, $that) {
                if ($data['is_served']) {
                    return "<div class='box_label'>SERVED</div>";
                } elseif($data['request_flag'] == 'crcu') {
                	$refno = $data['refno'];
                	$costcenter = $data['costcenter'];
                	$itemcode = $data['service_code'];
                	$baseUrl = Yii::app()->getController()->createUrl('openGrantRequestModal');
                	$baseUrl1 = Yii::app()->getController()->createUrl('voidGrantDetails');

                   	return "<div class='col text-center'>".$that->widget('bootstrap.widgets.TbButtonGroup',
                		array('buttonType' => 'button',
                			  'buttons'    => array(
                				array(
	                				'tooltip'     => '',
	                				'icon'		  => 'fa fa-eye',
	                                'label'       => '',
	                                'url'         => '#',
	                                'context'     => 'success',
	                                'htmlOptions' => array(
	                                	'title' 	=> 'View Details',
	                                	'style'		=> '',
	                                	'onclick'	=> "(function(refno='$refno',costcenter='$costcenter',itemcode='$itemcode',baseUrl='$baseUrl'){
	                                		var enc = $('#encounter_nr').val();
	                                		var url = baseUrl+'&refno='+refno+'&costcenter='+costcenter+'&itemcode='+itemcode+'&encounter_nr='+enc+'&view=1';
	                                		$.ajax({
												type:'GET',
												url: url,
												success: function(data){
													var obj = JSON.parse(data);
													$('#grant-request-modal .row-fluid').html(obj.modal);
										  			$('#grant-request-modal').modal();
										  			$('#grant-request-header').html('Grant Details');
													
												}
											});
	                                		
	                                	})()"
	                                )
	                            ),
                            ),
                        ),
                        true
                	)/*.$that->widget('bootstrap.widgets.TbButtonGroup',
                		array('buttonType' => 'button',
                			  'buttons'    => array(
                				array(
	                				'tooltip'     => '',
	                				'icon'		  => 'fa fa-undo',
	                                'label'       => '',
	                                'url'         => '#',
	                                'context'     => 'success',
	                                'htmlOptions' => array(
	                                	'title' 	=> 'Void Grant',
	                                	'style'		=>  '',
	                                	'onclick'   => "(function(refno='$refno',costcenter='$costcenter',itemcode='$itemcode',baseUrl='$baseUrl1'){
	                                		var enc = $('#encounter_nr').val();
	                                		var url = baseUrl+'&refno='+refno+'&costcenter='+costcenter+'&itemcode='+itemcode+'&encounter_nr='+enc+'&void=1';

	                                		Alerts.confirm({
	                                			title: 'Are you sure you want to void the grant?',
	                                			content: 'This will delete the grant details of the request',
	                                			callback: function(result){
	                                				if(result){
	                                					$.ajax({
															type:'GET',
															url: url,
															success: function(data){
																var enc = $('encounter_nr').val();
																var obj = $.parseJSON(data);
																console.log(obj);
																if(obj.result == 'success'){
																	actualBal = obj.actualBal;
																	remainBal = obj.remainBal;

																	Alerts.alert({
																		icon: 'fa fa-check',
																		title: 'Success',
																		content: 'Successfully voided request grant details',
																		callback: function(result){
																			$('patient-request-list-grid').yiiGridView.update('patient-request-list-grid', {
							                                                    type:'GET'
							                                                });
																			
																			$('#actualBalance').val('₱ '+ actualBal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
                                                							$('#remainBalance').val('₱ '+ remainBal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));

																			Alerts.close();
																			
																		}
																	});
																}else{
																	Alerts.error({
													                    title: 'Error',
													                    content: 'An error occured while voiding grant details',
													                    callback: function (result) {
													                        Alerts.close();
													                    }
													                });
																}

															}
														});
	                                				}
	                                			}
	                                		});
	                                	})()"
	                                )
	                            ),
                            ),
                        ),
                        true
                	)*/."</div>";

                }elseif($data['request_flag'] != NULL){
                	switch (strtoupper($data['request_flag'])) {
                		case 'CMAP':
                			$request_flag = 'MAP';
                			break;
                		default:
                			$request_flag = strtoupper($data['request_flag']);
                			break;
                	}
					return "<div class='box_label'>".$request_flag."</div>";;
                }else{
                	$refno = $data['refno'];
                	$costcenter = $data['costcenter'];
                	$itemcode = $data['service_code'];
                	$baseUrl = Yii::app()->getController()->createUrl('openGrantRequestModal');

                	return "<div class='col text-center'>".$that->widget('bootstrap.widgets.TbButtonGroup',
                		array('buttonType' => 'button',
                			  'buttons'    => array(
                				array(
	                				'tooltip'     => 'Grant',
	                				'icon'		  => 'fa fa-plus',
	                                'label'       => 'Grant',
	                                'url'         => '#',
	                                'context'     => 'success',
	                                'htmlOptions' => array(
	                                	'title'            	=> 'Grant',
	                                	'class'     		=> 'btn-circle bg-green-jungle',
	                                	'onclick'   => "(function(refno='$refno',costcenter='$costcenter',itemcode='$itemcode',baseUrl='$baseUrl'){
	                                		var enc = $('#encounter_nr').val();
	                                		var url = baseUrl+'&refno='+refno+'&costcenter='+costcenter+'&itemcode='+itemcode+'&encounter_nr='+enc;
	                                		$.ajax({
												type:'GET',
												url: url,
												success: function(data){
													var obj = JSON.parse(data);
													$('#grant-request-modal .row-fluid').html(obj.modal);
										  			$('#grant-request-modal').modal();
										  			$('#grant-request-header').html('Grant Details');
													
												}
											});
	                                		
	                                	})()"
	                                )
	                            ),
                            ),
                        ),
                        true
                	)."</div>";
                }
            },
            'headerHtmlOptions' => array('style' => 'text-align:center') 
        )
    )
));


 ?>