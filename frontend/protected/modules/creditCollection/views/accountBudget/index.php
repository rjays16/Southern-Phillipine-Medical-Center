<?php
/* @var $this AccountBudgetController */
$this->setPageTitle('');
$this->showfooter = false;
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->clientScript;
Yii::import('bootstrap.components.Bootstrap');
Yii::import('bootstrap.widgets.TbSelect2');
Yii::import('bootstrap.widgets.TbButton');
Yii::import('bootstrap.widgets.TbGridView');
Yii::import('bootstrap.widgets.TbActiveForm');
$css = <<<CSS
body { padding-top: 0;}
CSS;

$cs->registerCss('css',$css);

$this->breadcrumbs = array(
	'Billing Main Menu' => $baseUrl . '/modules/billing/bill-main-menu.php',
	'Account Budget Allocation'
);

Yii::app()->clientScript->registerScript('credit-form', <<<JAVASCRIPT

var loc = window.location;
var now = new Date();
var month = (now.getMonth() + 1);               
var day = now.getDate();
if (month < 10) 
    month = "0" + month;
if (day < 10) 
    day = "0" + day;
var today = month + '/' + day + '/' + now.getFullYear();

$('#grant_account').on('change', function(){
	$('#acc_changed').val(1);
});

$('.accounts-drop-down').on('change', function(){
	var account_id = $('#grant_account').val();
	var sub_account_id = $('#sub_account').val();
	
	var baseUrl = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=creditCollection/accountBudget/getGrantAccounts';

	var baseUrl1 = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=creditCollection/accountBudget/displayBudget';

	var acc_changed = $('#acc_changed').val();

	$.ajax({
		url: baseUrl,
		type: 'GET',
		data: {'account_id' : account_id, 'sub_account': sub_account_id},
		success: function(result){
			var obj = $.parseJSON(result);
			console.log(obj);

			if(acc_changed == 1){
				$("#sub_account option").remove();
				$('#sub_account').append($("<option></option>")
			                    .attr("value",'')
			                    .text('-- Select Sub Category --'));
				
				if(obj.model.length > 0){
				    $.each(obj.model, function(key, value) {
				     	$('#sub_account').append($("<option></option>")
				                    .attr("value",value.id)
				                    .text(value.name.toUpperCase())); 
					});
				}
				$('#acc_changed').val(0);
			}
			// console.log(obj);
			$("#actualBalance").val("₱ "+ numberWithCommas(obj.actual));
			$("#remainBalance").val("₱ "+ numberWithCommas(obj.remaining));

			updateGridView('account-budget-list-grid', baseUrl1, {type_id:account_id,id:sub_account_id});
		},
		error: function(log){
			console.log(log);
		}
	});
});

$("#newAllotment").on('click', function(){
	if($("#grant_account").val() == ''){
		alert("Please select an account first");
		return false;
	}else{
		$('#allotment-header').html('New Allotment');
	}
});

$('#amount').on('keypress', function(e){
  var regex = new RegExp("^[0-9.]+$");
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    }
    e.preventDefault();
    return false;
});

$('#new-allotment-modal').on('hidden.bs.modal', function () {
	$("#allotmentDate").val(today);
  	$("#amount").val('');
  	$("#remarks").val('');
  	$('#allotment_id').val('');
});

$('#btnSaveAllotment').on('click', function(){
	var type_id = $('#grant_account').val();
	var id = $('#sub_account').val();
	var allotmentDate = $('#allotmentDate').val();
	var amount = $('#amount').val();
	var remarks = $('#remarks').val();
	var allotment_id = $('#allotment_id').val();

	var data = new Object();
    
    if(allotmentDate == '' || amount == ''){
    	Alerts.alert({
            icon: 'fa fa-times',
            title: "Error",
            content: "Please fill in empty fields",
            callback: function (result) {
                Alerts.close();
            }
        });
    	return false;
    }else{
    	var msgsuccess = '';

    	Alerts.confirm({
	        title: "Are you sure you want to save changes?",
	        content: "This will update account's allotment",
	        callback: function(result) {
	            if(result) {
	            	data.id = id;
		    		data.type_id = type_id;
					data.allotmentDate = allotmentDate;
					data.amount = amount;
					data.remarks = remarks;
					data.allotment_id = allotment_id;

					var json = JSON.stringify(data);

					var baseUrl1 = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=creditCollection/accountBudget/save';
					
					var baseUrl = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=creditCollection/accountBudget/displayBudget';

					$.ajax({
						url: baseUrl1,
						type: 'GET',
						data: {'data': json},
						success: function(result){
				       		var obj = JSON.parse(result);

				       		if(obj.result == 'success'){
				       			updateGridView('account-budget-list-grid', baseUrl, {type_id:type_id,id:id});

				       			$("#actualBalance").val("₱ "+ numberWithCommas(obj.actual));
								$("#remainBalance").val("₱ "+ numberWithCommas(obj.remaining));

				       			if(allotment_id != ''){
									msgsuccess = "Successfully Updated Allotment";
									$('#new-allotment-modal').modal('toggle');
								}else{
									msgsuccess = "Successfully Added New Allotment";
								}

								Alerts.alert({
						            icon: 'fa fa-check',
						            title: "Success",
						            content: msgsuccess,
						            callback: function (result) {
						                Alerts.close();
						            }
						        });

								

						       	$("#allotmentDate").val(today);
								$("#amount").val('');
								$("#remarks").val('');
				       		}else if(obj.result == 'failed'){
				       			Alerts.alert({
						            icon: 'fa fa-times',
						            title: 'Error',
						            content: obj.message,
						            callback: function (result) {
						                Alerts.close();
						            }
						        });
				       		}
						}
					});

					
				  	return false;
	            }
	        }
	    });
    }
});

function updateGridView(gridviewid, baseUrl, data){
	$.fn.yiiGridView.update(gridviewid, {
       type:'GET',
       url: baseUrl,
       data: data
  	});
  	return false;
}

function numberWithCommas(x) {
    	return x.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

JAVASCRIPT
    , CClientScript::POS_READY);
?>

<h2 align="center">Account Budget Allocation</h2>

<?php 
echo CHtml::tag('div');
	$this->beginWidget(
		'bootstrap.widgets.TbBox',
		array(
			'title' => 'Budget Allocation'
		)	
	);

	$model2 = new FreeFormModel();
	$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'walkin-form',
        'type' => 'horizontal',
        'enableAjaxValidation' => false,
        'htmlOptions' => array(
            'class' => 'service-form'
        )
    ));

		echo $form->errorSummary($model);
		echo 'Account: ';
		?>
		<select id="grant_account" name="grant_account" class="accounts-drop-down" style="margin-left: 31px">
            <?php 
                
            echo "<option value=''>-- Select Account --</option>";

            foreach ($grantAccountTypes as $key => $value) {

                echo "<option value='".$key."' data-accountname='".$value."'>".strtoupper($value)."</option>";
            }
            ?>
        </select>
        <?php
	    echo CHtml::tag('div', array('style' => 'margin-top: -30px;margin-left: 450px;'));
		    echo 'Actual Balance: ';
		    echo $form->textField($model2, '', array(
		        'class' => 'input-medium',
		        'id' => 'actualBalance',
		        'style' => 'height: 20px;border-style: dashed; text-align: right;',
		        'readonly' => 'readonly'

		    ));
	    echo CHtml::closeTag('div');

	    echo CHtml::tag('div', array('style' => 'margin-top: -30px;margin-left: 750px;'));
		    echo 'Remaining Balance: ';
		    echo $form->textField($model2, '', array(
		        'class' => 'input-medium',
		        'id' => 'remainBalance',
		        'style' => 'height:20px; border-style: dashed; text-align: right;',
		        'readonly' => 'readonly'

		    ));
	    echo CHtml::closeTag('div');

	    echo 'Sub Category: ';
	    echo $form->dropDownList($model, 'id', $grantAccountTypeList, 
	    	array('prompt'=>'-- Select Sub Category --', 
	    		'id' => 'sub_account',
	    		'name' => 'sub_account',
	    		'class' => 'accounts-drop-down'
	    	)
	    );

	    echo $form->hiddenField($model2, '',
	        array(
	            'id' => "acc_changed",
	            'name' => 'acc_changed'
	        )
	    );
	$this->endWidget();

    $this->widget(
	    'bootstrap.widgets.TbButton',
	    array(
	        'label' => 'New Allotment',
	        'type' => 'primary',
	        'url' => '#',
	        'disabled' => '',
	        'icon' => 'fa fa-plus-circle',
	        'id' => 'newAllotment',
	        'htmlOptions' => array(
                'data-toggle' => 'modal',
                'data-target' => '#new-allotment-modal',
                'data-tooltip' => 'tooltip',
                'title' => 'Add New Allotment',
            ),
	    )
	);

	$this->widget('bootstrap.widgets.TbGridView', array(
		'id'=>'account-budget-list-grid',
		'dataProvider'=>$allotModel,
		'columns'=>array(
			array('name'=> 'date', 'header' => 'Date'),
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
							if($('#grant_account').val() == ''){
								Alerts.alert({
						            icon: 'fa fa-times',
						            title: 'Error',
						            content: 'Please select an account first',
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
										console.log(data);
										var obj = JSON.parse(data);
										$('#allotmentDate').val(obj.allotmentDate);
										$('#amount').val(obj.amount);
										$('#remarks').val(obj.remarks);
										$('#allotment_id').val(obj.id);
										$('#allotment-header').html('Update Allotment');
										$('#new-allotment-modal').modal('show');
										return false;
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
							if($('#grant_account').val() == ''){
								Alerts.alert({
						            icon: 'fa fa-times',
						            title: 'Error',
						            content: 'Please select an account first',
						            callback: function (result) {
						                Alerts.close();
						            }
						        });
								return false;
							}else{
								var thisurl = $(this).attr('href');
								Alerts.confirm({
							        title: 'Are you sure you want to delete this allotment?',
							        content: 'This will update account\'s allotment',
							        callback: function(result) {
							        	if(result){
							        		var type_id = $('#grant_account').val();
											var id = $('#sub_account').val();
											var baseUrl = loc.protocol + '//'+ loc.host + '/' + loc.pathname.split('/')[1]+'/index.php?r=creditCollection/accountBudget/displayBudget';

											$.ajax({
												type:'GET',
												url: thisurl+'&typeid='+type_id+'&subid='+id,
												success: function(data){
													var obj = JSON.parse(data);

													if(obj.result == 'success'){
														Alerts.alert({
												            icon: 'fa fa-check',
												            title: 'Success',
												            content: 'Successfully Deleted Allotment',
												            callback: function (result) {
												                Alerts.close();
												            }
												        });
														updateGridView('account-budget-list-grid', baseUrl, {type_id:type_id,id:id});

														$('#actualBalance').val('₱ '+ numberWithCommas(obj.actual));
														$('#remainBalance').val('₱ '+ numberWithCommas(obj.remaining));

													}else if(obj.result == 'failed'){
														Alerts.alert({
												            icon: 'fa fa-times',
												            title: 'Error',
												            content: obj.message,
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
					),
				),
			),
		),
	)); 

	$this->endWidget();


echo CHtml::closeTag('div');

echo CHtml::tag('div');

$this->beginWidget(
    'bootstrap.widgets.TbModal',
    array(
        'id' => 'new-allotment-modal',
        'fade' => false,
        'htmlOptions' => array(
            'data-backdrop' => 'static',
            'style' => 'height:430px;width:450px;',
        )
    )
); 
?>
	<div class="modal-header">
        <a class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></a>
        <h5 id="allotment-header"></h5>
    </div>

    <div class="modal-body" style="height:1000px;">
    	<div class="gg" style="margin-left: 30px;">
            <div class="row-fluid">
                <?php 
                	echo $form->labelEx($model,'Allotment Date <font color="#ff0000">*</font>');
                	echo $form->datepickerRow($model2, '',
				        array(
				            'options' =>
				                array(
				                    'format' => 'mm/dd/yyyy',
				                    'autoclose' => true,
				                    'showButtonPanel' => true,

				                ),
				            'htmlOptions'=>array(
				                'style' => 'width:350px;height:30px',
				                'id' => "allotmentDate",
				                'name' => 'allotmentDate',
				                'placeholder'=>'mm/dd/yyyy',
				                'value' => date('m/d/Y')
				            )
			        ));

			        echo $form->labelEx($model,'Amount <font color="#ff0000">*</font>');
                	echo $form->textField($model2, '',
			            array(
			                'id' => "amount",
			                'name' => 'amount',
			                'style' => 'width:350px;height:30px;'
			            )
			        );

			        echo $form->labelEx($model,'Remarks');
                	echo $form->textArea($model2, '',
			            array(
			                'id' => "remarks",
			                'name' => 'remarks',
			                'style' => 'width:350px',
			                'rows'=>4,
			                'cols'=>50,
			                'size' => 200,
			                'maxlength' => 600
			            )
			        );

			        echo $form->hiddenField($model2, '',
			            array(
			                'id' => "allotment_id",
			                'name' => 'allotment_id'
			            )
			        );
                ?>
            </div>

            <div class="row-fluid">       
                  
            </div>
    	</div>
	</div>
	<div class="modal-footer">
        <?php 
        $this->widget(
            'bootstrap.widgets.TbButton',
            array(
                'type' => 'primary',
                'label' => 'Save',
                'url' => '',
                'htmlOptions' => array(
                    'id' => 'btnSaveAllotment'
                ),
            )
        ); ?>
    </div>

<?php
$this->endWidget();
echo CHtml::closeTag('div');
?>


