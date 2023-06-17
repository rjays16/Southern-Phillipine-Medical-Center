<?php
$cs = Yii::app()->clientScript;
$baseUrl = Yii::app()->request->baseUrl;

$js = <<<JS

function addItem(){
    var template = $('#new_item').html();
    var list = $('#list');
    var newItemId = $('#add_code');

    if(newItemId.val() == '')
        return false;

    if(list.find('input[value='+newItemId.val()+']').length > 0){
		alert("Item is already in the list!");
		return false;
	}

    var templateValues = {
		serv_code : newItemId.val(),
		serv_desc : $('#add_desc').val(),
		serv_loc : $('#add_type').val(),
		serv_cash : number_format($('#add_cash').val(), 2, '.', ','),
		serv_charge : number_format($('#add_charge').val(), 2, '.', ','),
		serv_qty : '',
        is_fs : $('#is_fs').val(),
    };

    $('#no_items').remove();
	list.append(Mustache.render(template,templateValues));
	listSetConfig();
}

function listSetConfig(){
	var list = $('#list');

	list.find('.cell').css({padding:0});
	list.find('.cell-input').css({
		'box-sizing':'border-box',
		'-moz-box-sizing':'border-box',
		'-webkit-box-sizing':'border-box',
		width:'100%',
		height:'30px',
		position:'relative',
		top:'5px'
	});
}

function totalPrice(){

        var cash = 0;
        var charge = 0;

        $('.price_cash').each(function(){
            var price_cash = $(this);
            var qty = price_cash.closest('tr').find('.quantity').val();
            cash += parseInt(price_cash.val().replace(/,/g, '')) * parseInt(qty);
        });

        $('.price_charge').each(function(){
            var price_charge = $(this);
            var qty = price_charge.closest('tr').find('.quantity').val();
            charge += parseInt(price_charge.val().replace(/,/g, '')) * parseInt(qty);
        });

        $('#total_cash').html(number_format(cash, 2, '.', ','));
        $('#total_charge').html(number_format(charge, 2, '.', ','));
}


function deleteRow(e){
    $(e).parent().parent().animate({opacity:0.25},300,'linear',function(){
        $(this).remove();
        totalPrice();
    });
}

function deleteRowAjax(id,pid,code,e){
	if(confirm('Are you sure you want to delete this item?')){
		$.ajax({
			url : '{$baseUrl}/index.php?r=packageManager/manage/delete&id='+id+'&pid='+pid+'&code='+code,
			dataType : 'json',
			success : function(data){
				if(data.result == true){
					alert('Item deleted.');
					window.location.href = window.location.href;
					deleteRow(e);
				}
			},
			error : function(x,y){
				alert('Error deleting item.');
			}
		});
	}
}

function number_format(number, decimals, dec_point, thousands_sep){
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
      var k = Math.pow(10, prec);
      return '' + (Math.round(n * k) / k).toFixed(prec);
    };

    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }

    return s.join(dec);
}

JS;

$cs->registerScript('js',$js,CClientScript::POS_HEAD);

?>

<div class="form">
    <?php
    $item = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'itemsForm',
        'enableAjaxValidation' => false,
    ));

    echo $item->errorSummary($model);
    ?>
    <div class="form-inline" style="padding: 10px; border-radius: 5px; background-color: #F2F2F2; border: 1px solid #dadada;">
        <label for="item-search" style="margin-right: 10px;">Item to add: </label>
        <?php
        echo CHtml::hiddenField('add_code', '');
        echo CHtml::hiddenField('add_desc', '');
        echo CHtml::hiddenField('add_cash', '');
        echo CHtml::hiddenField('add_charge', '');
        echo CHtml::hiddenField('add_type', '');
        echo CHtml::hiddenField('is_fs', '');

        $url = Yii::app()->createUrl('packageManager/manage/items');
        $this->widget('bootstrap.widgets.TbSelect2', array(
            'asDropDownList' => false,
            'name' => 'item-search',
            'options' => array(
                'width' => 400,
                'placeholder' => 'Search Items',
                'dataType' => 'json',
                'id' => 'js:function(data){return data.serv_code;}',
                'ajax' => array(
                    'url' => $url,
                    'data' => 'js:function(term) {
                            return {
                                t: term
                            };
                        }',
                    'results' => 'js:function(data,page) { return {results: data}; }',
                ),
                'allowClear' => false,
                'escapeMarkup' => 'js:function (markup) { return markup; }',
                'minimumInputLength' => 3,
                'initSelection' => 'js:function(element, callback){
                        var id = $(element).val();
                        if(id !== "") {
                            $.ajax("' . $url . '", {
                                data: {id: id},
                                dataType: "json"
                            }).done(function(data) {
                                callback(data);
                            });
                        }
                    }',
                'formatResult' => 'js:function(data, container, query){
                        return "<span class=\'label label-success\'>Code: "+ data.serv_code +"</span>&nbsp;" +
                               "<span class=\'label label-info\'><i class=\'fa fa-tag\'></i> " + data.serv_loc + "</span><br/>" +
                               "<span>"+data.serv_desc+"</span>";
                    }',
                'formatSelection' => 'js:function(data, container){
                    $("#add_code").val(data.serv_code);
                    $("#add_desc").val(data.serv_desc);
                    $("#add_cash").val(data.serv_cash);
                    $("#add_charge").val(data.serv_charge);
                    $("#add_type").val(data.serv_type);
                    $("#is_fs").val(data.is_fs);
                    return data.serv_desc;
                }'
            ),
        ));

        $this->widget('bootstrap.widgets.TbButton',array(
            'buttonType' => 'button', 'type' => 'info',
            'icon' => 'fa fa-plus', 'label' => 'Add Item',
            'htmlOptions' => array(
                'style' => 'margin-left:20px;',
                'onclick' => 'addItem()'
            )
        ));

        $this->widget('bootstrap.widgets.TbButton',array(
            'buttonType' => 'submit', 'type' => 'success',
            'icon' => 'fa fa-save', 'label' => 'Save',
            'htmlOptions' => array(
                'class' => 'pull-right'
            )
        ));
        ?>
    </div>
    <table class="table table-bordered table-hovered" style="margin-top: 5px;">
        <thead>
           <tr>
               <th width="2%"><i class="fa fa-gear"></i></th>
               <th width="10%" style="text-align: center;">Service Code</th>
               <th width="*" style="text-align: center;">Description</th>
               <th width="5%" style="text-align: center;">Location</th>
               <th width="10%" style="text-align: center;">Cash Price</th>
               <th width="10%" style="text-align: center;">Charge Price</th>
               <th width="5%" style="text-align: center;">Quantity</th>
           </tr>
        </thead>
        <tbody id="list">
            <?php
                if(!empty($d_model)):
                foreach($d_model as $key):
            ?>
            <tr>
                <td><i class="fa fa-times" onclick="deleteRowAjax('<?=$key->item_id?>','<?=$key->package_id?>','<?=$key->item_code?>',this)"></i></td>
                <td style="text-align: center;">
                    <?=$key->item_code?>
                    <input type="hidden" name="items[<?=$key->item_id?>][item_code]" value="<?=$key->item_code?>">
                </td>
                <td>
                    <?=$key->item_name?>
                    <input type="hidden" name="items[<?=$key->item_id?>][item_name]" value="<?=$key->item_name?>">
                </td>
                <td style="text-align: center;">
                    <?=$key->item_purpose?>
                    <input type="hidden" name="items[<?=$key->item_id?>][item_purpose]" value="<?=$key->item_purpose?>">
                </td>
                <td style="text-align: right;">
                    <input type="text" class="price_cash" name="items[<?=$key->item_id?>][price_cash]" value="<?=number_format($key->price_cash, 2, '.', ',')?>" style="width: 100%; box-sizing: border-box; height: 30px; margin: 0px;" onkeyup="totalPrice();">
                </td>
                <td style="text-align: right;">
                    <input type="text" class="price_charge" name="items[<?=$key->item_id?>][price_charge]" value="<?=number_format($key->price_charge, 2, '.', ',')?>" style="width: 100%; box-sizing: border-box; height: 30px; margin: 0px;" onkeyup="totalPrice();">
                </td>
                <td>
                    <input type="text" class="quantity" name="items[<?=$key->item_id?>][quantity]" value="<?=$key->quantity?>" style="width: 100%; box-sizing: border-box; height: 30px; margin: 0px;" onkeyup="totalPrice();">
                </td>
            </tr>
            <?php
                endforeach;
                else:
            ?>
            <tr>
                <td id="no_items" colspan="7" style="color: #880000; text-align: center;"><strong>No items added for this package.</strong></td>
            </tr>
            <?php
                endif;
            ?>
        </tbody>
    </table>
    <div class="pull-right">
        <table class="table-condensed">
            <tr>
                <td style="text-align: right">Total Package Cash Price:</td>
                <td style="text-align: right; font-weight: bold;"><span id="total_cash"><?php echo number_format($totalCash, 2) ?></span></td>
            </tr>
            <tr>
                <td style="text-align: right">Total Package Charge Price:</td>
                <td style="text-align: right; font-weight: bold;"><span id="total_charge"><?php echo number_format($totalCharge, 2) ?></span></td>
            </tr>
        </table>
    </div>
    <?php
    $this->endWidget();
    ?>
</div>

<script id="new_item" type="mustache-template">
<tr>
    <td><i class="fa fa-times" onclick="deleteRow(this);"></i></td>
    <td style="text-align: center;">
        {{serv_code}}
        <input type="hidden" name="serv_code[]" value="{{serv_code}}">
    </td>
    <td>
        {{serv_desc}}
        <input type="hidden" name="serv_desc[]" value="{{serv_desc}}">
    </td>
    <td style="text-align: center;">
        {{serv_loc}}
        <input type="hidden" name="serv_loc[]" value="{{serv_loc}}">
    </td>
    <td style="text-align: right;">
        <input class="price_cash" type="text" name="serv_cash[]" value="{{serv_cash}}" title="Cash Price" style="width: 100%; box-sizing: border-box; height: 30px; margin: 0px;" onkeyup="totalPrice();">
    </td>
    <td style="text-align: right;">
        <input class="price_charge" type="text" name="serv_charge[]" value="{{serv_charge}}" title="Charge Price" style="width: 100%; box-sizing: border-box; height: 30px; margin: 0px;" onkeyup="totalPrice();">
    </td>
    <td>
        <input type="text" class="quantity" name="serv_qty[]" title="Quantity" style="width: 100%; box-sizing: border-box; height: 30px; margin: 0px;" onkeyup="totalPrice();">
    </td>
</tr>

</script>