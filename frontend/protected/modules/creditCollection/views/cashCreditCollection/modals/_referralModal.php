<?php
$this->setPageTitle('');
$this->showfooter = false;

$cs = Yii::app()->clientScript;

$cs->registerScript('referral-modal', <<<JAVASCRIPT

var loc = window.location;

function numberWithCommas(x) {
    var re = /^-?[0-9]+$/;
    
    if (re.test(x) == true) {
        return x.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }else return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

$('#amount').on('keypress', function(e){
  var regex = new RegExp("^[0-9.]+$");
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    }
    e.preventDefault();
    return false;
});

$('#account').on('change', function(){
	$('#acc_changed').val(1);
});

$('.accounts_list').on('change', function(){
	var account_id = $('#account').val();
	var id = $('#sub_account').val();
	var baseUrl = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=creditCollection/cashCreditCollection/getGrantAccount';
	var acc_changed = $('#acc_changed').val();

	$.ajax({
		url: baseUrl,
		type: 'GET',
		data: {'type_id' : account_id, 'id': id},
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

            if(obj.actualAccountFund >= 0)
                $("#fund").val("₱ "+ numberWithCommas(obj.actualAccountFund));
            else $("#fund").val("N/A");
            
			$("#account_fund").val(obj.actualAccountFund);

		},
		error: function(log){
			console.log(log);
		}
	});
});

$('#referral-entry-modal').on('hidden.bs.modal', function () {
	$("#entry_date").val('');
	$("#account").val('');
	$("#sub_account").val('');
	$("#control_no").val('');
	$("#amount").val('');
	$("#fund").val('');
	$("#remarks").val('');

  	return false;
});

JAVASCRIPT
    , CClientScript::POS_END);

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - */

$model2 = new FreeFormModel();
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'referral-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => false,
    'htmlOptions' => array(
        'class' => 'service-form'
    )
));	

?>

	<!-- echo $form->datePickerRow($model2, '',
        array(
            'options' =>
                array(
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                    'showButtonPanel' => true
                ),
            'htmlOptions'=>array(
                'style' => 'width:250px;height:25px',
                'id' => "entry_date",
                'name' => 'entry_date',
                'placeholder'=>'yyyy-mm-dd',
            )
    	), 
    	array('label' => 'Entry Date <font color="#ff0000">*</font>', 
    		'labelOptions' => array('style' => 'text-align:left')
    	)
	); -->

	<!-- NOTE: Synchronous XMLHttpRequest error if datePickerRow from TbActiveForm will be used due to jQuery bug-->
	<div>Entry Date<font color="#ff0000">*</font>
		<input type="date" name="entry_date" id="entry_date" style="width:245px;height:25px;margin-left:105px;margin-bottom:10px" value="<?=date("Y-m-d")?>">
 	</div>
	<!-- end NOTE -->
<?php
	/*echo $form->dropDownListRow($model2, '', $grantAccountTypes, array('prompt'=>'-- Select Account --',
		'id' => 'account',
		'name' => 'account',
		'class' => 'accounts_list',
		'style' => 'width:260px;height:30px'
		),
    	array('label' => 'Account <font color="#ff0000">*</font>', 
    		'labelOptions' => array('style' => 'text-align:left')
    	)
	);*/
    echo 'Account <font color="#ff0000">*</font>';
?>
    <select id="account" name="account" class="accounts_list" style="width:260px;height:30px;margin-left: 117px;margin-bottom: 10px">
        <?php 
            
        echo "<option value=''>-- Select Account --</option>";

        foreach ($grantAccountTypes as $key => $value) {

            echo "<option value='".$key."' data-accountname='".$value."'>".strtoupper($value)."</option>";
        }
        ?>
    </select>
<?php
	echo $form->dropDownListRow($model2, '', '', array('prompt'=>'-- Select Sub Category --',
		'id' => 'sub_account',
		'name' => 'sub_account',
		'class' => 'accounts_list',
		'style' => 'width:260px;height:30px;'
		),
		array('label' => 'Sub Category', 
			'labelOptions' => array('style' => 'text-align:left')
		)
    );

	echo $form->textFieldRow($model2, '',
        array(
            'id' => "control_no",
            'name' => 'control_no',
            'style' => 'width:250px;height:25px;'
        ),
		array('label' => 'Control No <font color="#ff0000">*</font>', 
			'labelOptions' => array('style' => 'text-align:left')
		)
    );

	echo $form->textFieldRow($model2, '',
        array(
            'id' => "fund",
            'name' => 'fund',
            'style' => 'width:250px;height:25px;',
            'readonly' => 'readonly'
        ),
		array('label' => 'Fund', 
			'labelOptions' => array('style' => 'text-align:left')
		)
    );

	echo $form->textFieldRow($model2, '',
        array(
            'id' => "amount",
            'name' => 'amount',
            'style' => 'width:250px;height:25px;'
        ),
		array('label' => 'Amount <font color="#ff0000">*</font>', 
			'labelOptions' => array('style' => 'text-align:left')
		)
    );

	echo $form->textAreaRow($model2, '',
        array(
            'id' => "remarks",
            'name' => 'remarks',
            'style' => 'width:250px',
            'rows'=>4,
            'cols'=>50,
            'size' => 200,
            'maxlength' => 300
        ),
		array('label' => 'Remarks', 
			'labelOptions' => array('style' => 'text-align:left')
		)
    );

	echo $form->hiddenField($model2, '',
        array(
            'id' => "referral_id",
            'name' => 'referral_id'
        )
    );

    echo $form->hiddenField($model2, '',
        array(
            'id' => "account_fund",
            'name' => 'account_fund'
        )
    );

    echo $form->hiddenField($model2, '',
        array(
            'id' => "acc_changed",
            'name' => 'acc_changed'
        )
    );

    echo $form->hiddenField($model2, '',
        array(
            'id' => "ref_enc_nr",
            'name' => 'ref_enc_nr',
            'value' => $encounter_nr
        )
    );
$this->endWidget();
?>