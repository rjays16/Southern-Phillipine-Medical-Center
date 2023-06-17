<?php 
$this->setPageTitle('');
$this->showfooter = false;

$cs = Yii::app()->clientScript;
Yii::import('bootstrap.components.Bootstrap');
Yii::import('bootstrap.widgets.TbActiveForm');
Yii::app()->clientscript->scriptMap['*.js'] = false;

$cs->registerScript('requests-modal', <<<JAVASCRIPT

var loc = window.location;
var deletedrows = new Array();
var now = new Date();
var month = (now.getMonth() + 1);               
var day = now.getDate();
if (month < 10) 
    month = "0" + month;
if (day < 10) 
    day = "0" + day;
var today = now.getFullYear() + '-' + month + '-' + day;

function numberWithCommas(x) {
    return parseFloat(x).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
function updateAccountDetails(id){
    var res = id.split("_");
    var acc_changed = 0;
    var tempid = '';

    if(res[0] == 'gdaccount'){
        acc_changed = 1;
    }
    if(res[1] != null){
        tempid = '_'+res[1];
    }
    if(acc_changed == 1){
        $("#gdsubaccount"+tempid+" option").remove();
        $('#gdsubaccount'+tempid).append($("<option></option>")
                        .attr("value",'')
                        .text('-- Select Sub Category --'));
    }
    
    var enc = $('#encounter_nr').val();
    var account_id = $('#gdaccount'+tempid).val();
    var id = $('#gdsubaccount'+tempid).val();
    var baseUrl = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=creditCollection/cashCreditCollection/getAccountSubCategory&encounter_nr='+enc;

    if(account_id != ''){
        $.ajax({
            url: baseUrl,
            type: 'GET',
            data: {'type_id' : account_id, 'id': id},
            success: function(result){
                // console.log(result);
                var obj = $.parseJSON(result);
                if(acc_changed == 1){
                    if(obj.subcategoryref.length > 0){
                        $('#gdsubaccount'+tempid).css("display", '');
                        $.each(obj.subcategoryref, function(key, value) {
                            $('#gdsubaccount'+tempid).append($("<option></option>")
                                        .attr("value",value.subAccountid)
                                        .attr("data-accountname",value.sub_account.toUpperCase())
                                        .text(value.sub_account.toUpperCase())); 
                        });
                    }else{
                        $('#gdsubaccount'+tempid).css("display", 'none');
                    }
                }

                $("#gdbalance"+tempid).val("₱ "+ numberWithCommas(obj.totalFund));
                $("#gdrefbal"+tempid).val(obj.totalFund);

            },
            error: function(log){
                console.log(log);
            }
        });
    }else{
        $("#gdbalance"+tempid).val("₱ "+ numberWithCommas(0.00));
    } 
}

$("#btnaddgrant").on("click", function(){
    var rowCount = $('#tbl_grantdetails >tbody >tr').length + 1;
    var enc = $('#encounter_nr').val();

    var baseUrl = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=creditCollection/cashCreditCollection/openGrantRequestModal&encounter_nr='+enc;
    var is_view = $('#is_view').val();
    var grantrow_id = '';

    if(is_view == 1) grantrow_id = 'new';

    if(!$(this).attr('disabled')){
        $.ajax({
            type:'GET',
            url: baseUrl,
            data: {'isOpened': 1},
            success: function(data){
                var obj = $.parseJSON(data);
    
                $("#tbl_grantdetails").find('tbody')
                    .append($('<tr>', {id: grantrow_id})
                        .append($('<td>', {style: 'vertical-align:center'})
                            .append($('<i>',{
                                        class: 'fa fa-times grantDel',
                                        style: 'font-size:18px;color:red;cursor:pointer'
                                    }))
                            )
                        .append($('<td>')
                            .append(
                                $('<select>',{
                                    id: 'gdaccount_'+rowCount,
                                    name: 'scaccount[]',
                                    class: 'grantaccountdd',
                                    onchange: 'updateAccountDetails(this.id)'
                                }).append(
                                    $('<option>', {
                                        value: '',
                                        text: '-- Select Account --'
                                    })
                                ).append(
                                    $.map(obj, function(value, key) {
                                        if(value['has_permission'] == 1){
                                            return $('<option>', {
                                                value: key,
                                                "data-accountname" : value['alt_name'].toUpperCase(),
                                                text: value['alt_name'].toUpperCase()
                                            })
                                        }
                                    })
                                )
                            )
                            .append('<br>')
                            .append(
                                $('<select>',{
                                    id: 'gdsubaccount_'+rowCount,
                                    name: 'scsubaccount[]',
                                    class: 'grantaccountdd',
                                    onchange: 'updateAccountDetails(this.id)',
                                    style: 'display:none'
                                }).append(
                                    $('<option>', {
                                        value: '',
                                        text: '-- Select Sub Category --'
                                    })
                                )
                            )
                        )
                        .append($('<td>')
                            .append(
                                $('<input>', {
                                    id: 'gdbalance_'+rowCount,
                                    name: 'gdbalance',
                                    style: 'width:90px',
                                    readonly: '',
                                    value: '₱ 0.00',
                                    type: 'text'
                                })
                            ).append(
                                $('<input>', {
                                    id: 'gdrefbal_'+rowCount,
                                    name: 'gdrefbal',
                                    type: 'hidden',
                                    class: 'saminputs'
                                })
                            )
                        )
                        .append($('<td>')
                            .append(
                                $('<input>', {
                                    id: 'gdamount_'+rowCount,
                                    name: 'gdamount',
                                    style: 'width:90px',
                                    type: 'text',
                                    class: 'saminputs'
                                })
                            )
                        )
                        .append($('<td>')
                            .append(
                                $('<input>', {
                                    id: 'gdcontrolno_'+rowCount,
                                    name: 'gdcontrolno',
                                    style: 'width:100px',
                                    type: 'text',
                                    class: 'saminputs'
                                })
                            )
                        )
                        .append($('<td>')
                            .append(
                                $('<input>', {
                                    id: 'gddategrant_'+rowCount,
                                    name: 'gddategrant',
                                    style: 'width:110px',
                                    type: 'date',
                                    class: 'saminputs',
                                    value: today
                                })
                            )
                        )
                    );  
                $('#gdamount_'+rowCount).mask("000,000,000,000.00", {reverse: true});
            }
        });
    }
    
});

$(".grantDel").live('click', function(){
    var is_view = $("#is_view").val();

    if(is_view == 1){
        var del_row = $(this).parent().closest('tr');
        var grant_id = del_row.attr('id');
        if(grant_id != 'new'){
            var del_amnt = del_row.find(".clm_amount").html();
            var totalbal = $('#totalbal').val();
            var deleted = grant_id.split("_");

            $('#totalbal').val(parseFloat(totalbal) + parseFloat(del_amnt));
            $('#head_bal').html(parseFloat($('#totalbal').val()).toFixed(2));

            deletedrows.push(deleted[1]);
        }
        
        $('#btnaddgrant').attr("disabled", false);
    } 

    $(this).parent().closest('tr').remove();
});

$('#grant-request-modal').on('hidden.bs.modal', function () {
    $('.grantDel').die('click');
});

$("#btnsavegrant").on("click", function(){
    var arrayDetails = new Array;
    var hasEmptyfields = 0;
    var success = 1;
    var is_view = $("#is_view").val();

    $('#tbl_grantdetails tbody tr').each(function() {
        var arrayTemp = [];
        var select_field = $(this).find("select[name^=scaccount]");

        /*if(is_view == 1){
            var row_id = $(this).attr('id');

            if(row_id != 'new'){
                arrdeleted = row_id.split("_");
                arrayTemp['row_id'] = arrdeleted[1];
            }else{
                arrdeleted = row_id
                arrayTemp['row_id'] = arrdeleted;
            }
        }else*/ arrayTemp['row_id'] = '';
        if(select_field.length > 0){
            select_field.each(function(){
                if(this.value == ''){
                    Alerts.error({
                        title: "Error",
                        content: "Account cannot be empty",
                        callback: function (result) {
                            Alerts.close();
                        }
                    });
                    hasEmptyfields = 1;
                    return false;
                }else{
                    arrayTemp['account'] = this.value;
                    arrayTemp['accountname'] = $(this).find(':selected').attr('data-accountname');
                }
            });
                console.log();

            $(this).find("select[name^=scsubaccount]").each(function(){
                arrayTemp['subaccount'] = this.value;
                if(this.value != '')
                    arrayTemp['accountname'] = $(this).find(':selected').attr('data-accountname');
            });

            $(this).find("input.saminputs").each(function(){
                if((this.value == ''|| !this.value.replace(/\s/g, '').length || this.value == 0) && this.name != 'gdrefbal'){
                    Alerts.error({
                        title: "Error",
                        content:"Please fill in the empty fields. Fields must not be empty or 0.",
                        callback: function (result) {
                            Alerts.close();
                        }
                    });
                    hasEmptyfields = 1;
                    return false;
                }else{
                    if(this.name == 'gdamount'){
                        arrayTemp['gdamounts'] = this.value.replace(/,/g , '');
                    }
                    else arrayTemp[this.name] = this.value;
                }
            });

            // console.log(arrayTemp);
            arrayDetails.push(arrayTemp);
        }
    });
    console.log(deletedrows);
    console.log(arrayDetails);
    var tempGrantArr = [];

    if(!arrayDetails.length && !deletedrows.length){
        Alerts.error({
            title: "Error",
            content:"No changes made. There's nothing to save",
            callback: function (result) {
                Alerts.close();
            }
        });
        return false;
    }else{
        if(hasEmptyfields == 0){
            $.each(arrayDetails, function(data, value){
                console.log('here ako inside arrayDetails');
                var exists = 0;
                if(tempGrantArr.length != 0){
                    $.each(tempGrantArr, function(a,b){
                        var exceeded = 0;
                        if(tempGrantArr[a]['account'] == value['account'] && tempGrantArr[a]['subaccount'] == value['subaccount']){
                            tempGrantArr[a]['gdamount'] = parseFloat(tempGrantArr[a]['gdamount']) +parseFloat(value['gdamounts']);
                            exists = 1;
                            if(parseFloat(tempGrantArr[a]['gdamount']) >  parseFloat(tempGrantArr[a]['gdrefbal'])){
                                exceeded = 1;
                            }
                        }else{
                            if(parseFloat(value['gdamounts']) > parseFloat(value['gdrefbal'])){
                                exceeded = 1;
                            }
                        }

                        if(exceeded == 1){
                            Alerts.error({
                                title: "Error",
                                content:"Amount entered to "+value['accountname']+" has exceeded to its available balance",
                                callback: function (result) {
                                    Alerts.close();
                                }
                            });
                            success = 0;
                            return false;
                        }
                    });

                    if(exists == 0)
                        tempGrantArr.push(value);
                }else{
                    if(parseFloat(value['gdamounts']) > parseFloat(value['gdrefbal'])){
                        Alerts.error({
                            title: "Error",
                            content:"Amount entered to "+value['accountname']+" has exceeded to its available balance",
                            callback: function (result) {
                                Alerts.close();
                            }
                        });
                        success = 0;
                        return false;
                    }else{
                        tempGrantArr.push(value);
                    }
                }
            });

            if(success == 1){
                // console.log("success");
                var refno = $("#refno").val();
                var costcenter = $("#costcenter").val();
                var itemcode = $("#itemcode").val();
                var encounter_nr = $("#encounter_nr").val();
                var baseUrl = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=creditCollection/cashCreditCollection/saveGrantDetails&refno='+refno+'&costcenter='+costcenter+'&itemcode='+itemcode+'&encounter_nr='+encounter_nr;
                var totalbal = $("#totalbal").val();
                var data = new Object;
                var totalamount = 0;
                var isfull = 0;

                $.each(arrayDetails, function(i, array){
                    totalamount = parseFloat(totalamount) + parseFloat(array['gdamounts']);

                    data[i] = { 
                        row_id: array['row_id'],
                        account: array['account'],
                        accountname: array['accountname'],
                        gdamount: array['gdamounts'],
                        gdcontrolno: array['gdcontrolno'],
                        gddategrant: array['gddategrant'],
                        gdrefbal: array['gdrefbal'],
                        subaccount: array['subaccount'],
                    };
                });

                var json = JSON.stringify(data);

                // if(is_view == 1) totalbal = $("#totaldue").val();

                if(parseFloat(totalamount) == parseFloat(totalbal) && parseFloat(totalamount) != 0){
                    isfull = 1;
                }
                console.log(deletedrows);
                console.log(totalamount);
                console.log(totalbal);

                if(parseFloat(totalamount) > parseFloat(totalbal)){
                    Alerts.error({
                        title: "Error",
                        content:"Total grant amount exceeded to request's balance",
                        callback: function (result) {
                            Alerts.close();
                        }
                    });
                    return false;
                }else{
                    Alerts.confirm({
                        title: "Are you sure you want to save changes?",
                        content: "This will update the grant details of this request",
                        callback: function(result) {
                            if(result) {
                                deletedrows = JSON.stringify(deletedrows);
                                $.ajax({
                                    url: baseUrl,
                                    type: 'GET',
                                    dataType: "json",
                                    data: {'deleteRows':deletedrows,'details' : json, 'isfull' : isfull, 'update': is_view},
                                    success: function(data){
                                        
                                        if(data.result == 'success'){
                                            actualBal = data.actualBal;
                                            remainBal = data.remainBal;
                                            Alerts.alert({
                                                icon: 'fa fa-check',
                                                title: "Success",
                                                content: "Grant Details Successfully Saved",
                                                callback: function (result) {
                                                    Alerts.close();
                                                    $("#grant-request-modal").modal('toggle');
                                                    $.fn.yiiGridView.update('patient-request-list-grid', {
                                                        type:'GET',
                                                        data: {'encounter_nr':encounter_nr}
                                                    });

                                                    $('#actualBalance').val("₱ "+ numberWithCommas(actualBal));
                                                    $('#remainBalance').val("₱ "+ numberWithCommas(remainBal));
                                                }
                                            });
                                        }
                                        console.log(result);

                                    },
                                    error: function(log){
                                        console.log(log);
                                    }
                                });
                            }
                        }
                    });
                }
            }
        }
    }
});

$("input[name^=gdamount]").mask("000,000,000,000.00", {reverse: true});


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
<div class="container" style="width: 100%;">
    <label style="font-size: 14px;cursor:text;">
        Ref No.: <b style="margin-right: 50px;"><?php echo $details['refno'] ?></b>

        Balance: <b id='head_bal'><?php echo number_format($balance, 2) ?></b>
    </label> 
    <label style="font-size: 14px;cursor:text;">Item Name: <b><?php echo $details['item_name'] ?></b> </label>
    <br>
    <?php 
        $disabled = '';

        $this->widget(
            'bootstrap.widgets.TbButton',
            array(
                'label' => 'Add',
                'type' => 'info',
                'size' => 'small',
                'url' => '#',
                'icon' => 'fa fa-plus',
                'id' => 'btnaddgrant',
                'htmlOptions' => array(
                    'title' => 'Add Grant',
                    'disabled' => $disabled,
                ),
            )
        );
        

        $this->widget(
            'bootstrap.widgets.TbButton',
            array(
                'label' => 'Save',
                'type' => 'info',
                'size' => 'small',
                'url' => '#',
                'disabled' => '',
                'icon' => 'fa fa-floppy-o',
                'id' => 'btnsavegrant',
                'htmlOptions' => array(
                    'title' => 'Save Grant Details'
                ), 
            )
        );
    ?>         
  <table class="table table-striped" id="tbl_grantdetails">
    <thead>
      <tr>
        <th></th>
        <th>Account Category</th>
        <th>Balance</th>
        <th>Amount</th>
        <th>Control No.</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
        <?php 
            $row_id = '';
            $count = 1;
            if(count($grants) > 0){
                foreach($grants as $grant){
        ?>
                    <tr id="grant_<?=$grant['id']?>">
                        <td>
                            <?php 
                            if($referralAccount[$grant['account']]['has_permission']){
                            ?>
                                <i class="fa fa-times grantDel" style="font-size:18px;color:red;cursor:pointer"></i>
                            <?php
                            }
                            ?>
                        </td>
                        <td>
                            <!-- <select id="gdaccount<?=$row_id?>" class="grantaccountdd saminputs" name="scaccount[]" onchange="updateAccountDetails(this.id)"> -->
                                <?php 
                                    echo $referralAccount[$grant['account']]['alt_name'];
                                /*echo "<option value=''>-- Select Account --</option>";
                                foreach ($referralAccount as $key => $value) {
                                    if($key == $grant['account']){
                                        $accountbal = $grant['amount'];
                                        $selected = 'selected';
                                    }
                                    else $selected = '';

                                    echo "<option value='".$key."' data-accountname='".$value."' $selected>".$value."</option>";
                                }*/
                                ?>
                            <!-- </select> -->
                            
                            <?php 
                            /*$hasSubcategory = 0;
                            $subOptions = "<option value=''>-- Select Sub Category --</option>";*/
                            foreach ($subaccountcat as $subaccount) {
                                if($subaccount['id'] == $grant['sub_account']){
                                    /*$accountbal = $subaccount['amount'];
                                    $selected = 'selected';*/
                                    echo "<br>".$subaccount['subaccountname'];
                                }
                                /*else $selected = '';

                                if($subaccount['account'] == $grant['account']){
                                    $subOptions .= "<option value='".$subaccount['subaccountid']."' data-accountname='".$subaccount['subaccountname']."' $selected>".$subaccount['subaccountname']."</option>";
                                    $hasSubcategory = 1;
                                }*/
                            }


                            /*if($hasSubcategory) $hidesubaccount = '';
                            else $hidesubaccount = "style='display:none'";*/
                            
                            ?>
                            <!-- <select id="gdsubaccount<?=$row_id?>" class="grantaccountdd saminputs" name="scsubaccount[]" onchange="updateAccountDetails(this.id)" <?=$hidesubaccount?>>
                                     <?=$subOptions?>
                            </select> -->
                        </td>
                        <td>
                            <!-- <input type="text" style="width:90px" id="gdbalance<?=$row_id?>" name="gdbalance" value="<?php echo '₱ '.number_format($grant['balance'], 2); ?>" readonly>
                            <input type="hidden" name="gdrefbal" id="gdrefbal<?=$row_id?>" class="saminputs" value="<?=$grant['balance']?>"> -->
                            <?php echo '₱ '.number_format($grant['balance'], 2); ?>
                        </td>
                        <td class="clm_amount"><?=$grant['amount'] ?></td>
                        <!-- <td>
                            <input type="text" style="width:90px" id="gdamount<?=$row_id?>" name="gdamount" class="saminputs" value="<?=$grant['amount'] ?>">
                        </td> -->
                        <td>
                            <!-- <input type="text" style="width:100px" id="gdcontrolno<?=$row_id?>" name="gdcontrolno" class="saminputs" value="<?=$grant['control_no'] ?>" > -->
                            <?=$grant['control_no'] ?>
                        </td>
                        <td>
                            <!-- <input type="date" style="width:110px" id="gddategrant<?=$row_id?>" name="gddategrant" class="saminputs" value="<?=$grant['date'] ?>"> -->
                            <?php echo date("m/d/Y",strtotime($grant['date'])) ?>
                        </td>
                    </tr>
        <?php   
                    $count++;
                    $row_id = '_'.$count;
                }// end foreach($grants) loop
            }else{
        ?>
                <tr>
                    <td>
                    </td>
                    <td>
                        <?php //var_dump($referralAccount);die; ?>
                        <select id="gdaccount" class="grantaccountdd saminputs" name="scaccount[]" onchange="updateAccountDetails(this.id)">
                            <?php 
                                echo "<option value=''>-- Select Account --</option>";
                                foreach ($referralAccount as $key => $value) {
                                    if($value['has_permission'])
                                        echo "<option value='".$key."' data-accountname='".$value['alt_name']."'>".$value['alt_name']."</option>";
                                }
                            ?>
                        </select>
                        <br>
                        <select id="gdsubaccount" class="grantaccountdd saminputs" name="scsubaccount[]" onchange="updateAccountDetails(this.id)" style="display: none">
                            <option value="">-- Select Sub Category --</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" style="width:90px" id="gdbalance" name="gdbalance" value="<?php echo '₱ '.number_format(0, 2); ?>" readonly>
                        <input type="hidden" name="gdrefbal" id="gdrefbal" class="saminputs">
                    </td>
                    <td>
                        <input type="text" style="width:90px" id="gdamount" name="gdamount" class="saminputs">
                    </td>
                    <td>
                        <input type="text" style="width:100px" id="gdcontrolno" name="gdcontrolno" class="saminputs">
                    </td>
                    <td>
                        <input type="date" style="width:110px" id="gddategrant" name="gddategrant" class="saminputs" value="<?php echo date('Y-m-d')?>">
                    </td>
                </tr>
        <?php
            }
         ?>
    </tbody>
  </table>
  <input type="hidden" name="gdaccountchanged" id="gdaccountchanged">
  <input type="hidden" name="costcenter" id="costcenter" value="<?php echo $costcenter ?>">
  <input type="hidden" name="refno" id="refno" value="<?php echo $details['refno'] ?>">
  <input type="hidden" name="itemcode" id="itemcode" value="<?php echo $itemcode ?>">
  <input type="hidden" name="totalbal" id="totalbal" value="<?php echo $balance; ?>">
  <input type="hidden" name="totaldue" id="totaldue" value="<?php echo isset($totaldue) ? $totaldue : '' ?>">
  <input type="hidden" name="is_view" id="is_view" value="<?php echo count($grants) > 0 ? 1 : 0 ?>">
</div>
<?php

$this->endWidget();
?>
