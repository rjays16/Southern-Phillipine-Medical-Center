<?php
/* @var $this CashCreditCollectionController */
$this->setPageTitle('');
$this->showfooter = false;
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->clientScript;

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery/jquery.mask.js');

Yii::import('bootstrap.components.Bootstrap');
Yii::import('bootstrap.widgets.TbSelect2');
Yii::import('bootstrap.widgets.TbButton');
Yii::import('bootstrap.widgets.TbGridView');
Yii::import('bootstrap.widgets.TbActiveForm');
$css = <<<CSS
body { padding-top: 0;}
#btn_search:hover{ background: #800080;}
.patient-encounter-list{float: right;}
.case-number-link, 
.case-option{cursor: pointer;}
div .indextbox{
	height: 20px;
	border-style:none;
	font-weight:bold;
	background-color: #fff;
	cursor:text;
}
.box_label{
	text-align: center;
	font-weight: bold;
	color: green;
}
.btn_grant{
	text-align: center;
}
#btn_reqsearch{
	margin-top: -50px;
	margin-left: 825px;
	position: absolute;
}
#btnsavegrant{
	margin-left: 10px;
}
.focused{
	border: solid 1px red;
}
CSS;

$cs->registerCss('css',$css);

$this->breadcrumbs = array(
	'Billing Main Menu' => $baseUrl . '/modules/billing/bill-main-menu.php',
	'Cash Credit and Collection'
);

$cs->registerScript('credit', <<<JAVASCRIPT

var loc = window.location;
var today = $("#date_today").val();

function numberWithCommass(x) {
	return x.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

$('#btn_modal_search').on('click', function(){
	var hrn = $('#search-name-hrn').val();
    var caseNum = $('#search-case-number').val();
    var isnum = /^\d+$/;

    if((hrn.length > 2 && ((hrn.indexOf(',') !== -1 && caseNum == '') || (isnum.test(hrn)))) || (caseNum.length > 4 && hrn == '')){
    	$.fn.yiiGridView.update('people-grid-view', {
	       type:'GET',
	       data: {'search-name-hrn':hrn, 'search-case-number': caseNum, 'credit_collection' : 1}
	  	});
	  	return false;
    }
});

/* SEARCH MODAL JS */

$('.search_box').on('keypress', function(e){
	var keycode = (e.keyCode ? e.keyCode : e.which);
	if (keycode == '13') {
	  	var hrn = $('#search-name-hrn').val();
	    var caseNum = $('#search-case-number').val();
	    var isnum = /^\d+$/;

	    if((hrn.length > 2 && ((hrn.indexOf(',') !== -1 && caseNum == '') || (isnum.test(hrn)))) || (caseNum.length > 4 && hrn == '')){
	    	$.fn.yiiGridView.update('people-grid-view', {
		       type:'GET',
		       data: {'search-name-hrn':hrn, 'search-case-number': caseNum, 'credit_collection' : 1}
		  	});
		  	return false;
	    }
	}
});

$('#search-patient-modal').on('hidden.bs.modal', function () {
	if($('#collapseclicked').val() != 1){
		$("#search-name-hrn").val('');
	 	$("#search-case-number").val('');

	  	$.fn.yiiGridView.update('people-grid-view', {
	  		data: {'search-name-hrn':'hrn', 'search-case-number': '', 'credit_collection' : 1}
	  	});

	  	return false;
	}
	$('#collapseclicked').val(0);
});

function initCollapse() {
    var collapsibleElements = $(".collapse");
    collapsibleElements.on('shown.bs.collapse', function(){
        var _this = $(this);
        var hrn = _this.data('hrn');
        var list = $('.encounter_list_' + hrn);

        var template = '<tr>' +
            '<td>{{{case_nr}}}</td>' +
            '<td>{{case_date}}</td>' +
            '<td style="width: 140px;">{{department}}</td>' +
            '<td>{{case_type}}</td>' +
        '</tr>';
        $.ajax({
	  		url: '{$baseUrl}/index.php?r=person/search/caseNumbers/pid/'+hrn,
	  		type: 'GET',
	  		success: function(response){
	  			$('#loading_'+hrn).hide();
	  			if(response.length > 0) {
	                var table = list.find('#encounter_table_' + hrn);
	                for(var i=0; i < response.length; i++) {
	                    table.find('tbody').append(Mustache.render(template, {
	                        case_nr: '<a class="case-option" data-hrn="'+response[i].pid+'" data-case_number="'+response[i].encounter_nr+'">'+response[i].encounter_nr+'</a>',
	                        case_date: response[i].encounter_date,
	                        department: response[i].department,
	                        case_type: response[i].encounter_type
	                    }));
	                }
	                caseOptionsClick();
	                list.show();
	                table.show();
	            }
	  		},
	  		error: function(error){
	  			console.log(error);
	  		}
	  	});
    });
    collapsibleElements.on('show.bs.collapse', function(){
        var _this = $(this);
        var hrn = _this.data('hrn');
        var list = $('.encounter_list_' + hrn);
        var table = list.find('#encounter_table_' + hrn);
        table.find('tbody').empty();
        table.hide();
        $('#loading_'+hrn).show();
    }).on('hidden.bs.collapse', function (e) {
		$('#collapseclicked').val(1);
	});
}
function caseOptionsClick(){
	$('.case-option').on('click', function(e){
        e.preventDefault();
        var _this = $(this);
       
        $.getJSON('{$baseUrl}/index.php?r=creditCollection/cashCreditCollection/search/hrn/'+_this.data('hrn')+'/encounter_nr/'+_this.data('case_number'),
        {},
        function(response){
        	console.log(response);
            loadPerson(response);
        });
    });
}

function initCaseNumberClick(){
    $('.case-number-link').on('click', function(e){
        e.preventDefault();
        var _this = $(this);
       
        $.getJSON('{$baseUrl}/index.php?r=creditCollection/cashCreditCollection/search/hrn/'+_this.data('hrn')+'/encounter_nr/'+_this.data('case_number'),
        {},
        function(response){
        	console.log(response);
            loadPerson(response);
        });
    });
}

function loadPerson(data){
	$('#pid').val(data.pid);
	$('#encounter_nr').val(data.encounter_nr);
	$('#encounter_type').val(data.encounter_type);
	$('#patient_name').val(data.fullName);
	$('#classification').val(data.classification);
	$('#actualBalance').val("₱ "+ numberWithCommass(data.actualBal));
	$('#remainBalance').val("₱ "+ numberWithCommass(data.remainBal));

	// clear search fields in request tab
	$('#request_source').val('');
	$('#status').val('pending');
	$('#date_from').val('');
	$('#date_to').val('');

	$('#search-patient-modal').modal('toggle');

	$.fn.yiiGridView.update('patient-referral-list-grid', {
		type:'GET',
  		data: {'encounter_nr':data.encounter_nr}
  	});

	$.fn.yiiGridView.update('patient-request-list-grid', {
		type:'GET',
  		data: {'encounter_nr':data.encounter_nr,'request_source' : '', 'status' : 'pending', 'date_from' : '', 'date_to' : ''}
  	});
  	
}
/* END SEARCH MODAL JS */

$("#btn_referral").on('click', function(e){
	var _button = $(this);

    e.preventDefault();

    if ($("#encounter_nr").val() == '') {
    	Alerts.alert({
            icon: 'fa fa-times',
            title: "Error",
            content: _button.data('alert-message'),
            callback: function (result) {
                Alerts.close();
            }
        });
    }else{
    	var baseUrl = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=creditCollection/cashCreditCollection/openReferralModal';

		var encounter_nr = $("#encounter_nr").val();


	  	$.ajax({
	  		url: baseUrl,
	  		type: 'GET',
	  		data: {'encounter_nr' : encounter_nr},
	  		success: function(data){
	  			$("#referral-entry-modal .row-fluid").html(data);
	  			$("#referral-entry-modal").modal();
	  			$('#referral-entry-header').html('Process Referral Entry');
	  		},
	  		error: function(error){
	  			console.log(error);
	  		}
	  	});
    }
});

$("#btnSaveReferral").on('click', function(){

	var entry_date = $("#entry_date").val();
	var type_id = $('#account').val();
	var id = $('#sub_account').val();
	var control_no = $('#control_no').val();
	var amount = $("#amount").val();
	var remarks = $("#remarks").val();
	var encounter_nr = $("#encounter_nr").val();
	var account_fund = $("#account_fund").val();
	var referral_id = $("#referral_id").val();

	var data = new Object();
	
	if(entry_date == '' || type_id=='' || control_no == '' || (amount == '' || amount == 0)){
		Alerts.alert({
            icon: 'fa fa-times',
            title: "Error",
            content: "Please fill in required fields",
            callback: function (result) {
                Alerts.close();
            }
        });
	}else if(parseFloat(amount) > parseFloat(account_fund) && account_fund != -1){
		Alerts.alert({
            icon: 'fa fa-times',
            title: "Error",
            content: "Amount cannot be greater than the account fund",
            callback: function (result) {
                Alerts.close();
            }
        });
	}else if(account_fund == 0){
		Alerts.alert({
            icon: 'fa fa-times',
            title: "Error",
            content: "Sorry cannot save referral. Account fund is exhausted!",
            callback: function (result) {
                Alerts.close();
            }
        });
	}else{
		Alerts.confirm({
	        title: "Are you sure you want to save changes?",
	        content: "This will update patient's refferal entries",
	        callback: function(result) {
	            if(result) {
	               	data.encounter_nr = encounter_nr;
					data.entry_date = entry_date;
					data.type_id = type_id;
					data.id = id;
					data.control_no = control_no;
					data.amount = amount;
					data.balance = account_fund;
					data.remarks = remarks;
					data.referral_id = referral_id;

					var json = JSON.stringify(data);

					var baseUrl1 = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=creditCollection/cashCreditCollection/save';

					$.ajax({
						url: baseUrl1,
						type:'GET',
						data: {'data': json},
						success: function(data){
							var obj = JSON.parse(data);

							if(obj.result == 'success'){
								$.fn.yiiGridView.update('patient-referral-list-grid', {
									type:'GET',
							  		data: {'encounter_nr':encounter_nr}
							  	});

							  	if(referral_id != ''){
									Alerts.alert({
							            icon: 'fa fa-check',
							            title: "Success",
							            content: "Successfully Updated Allotment",
							            callback: function (result) {
							                Alerts.close();
							            }
							        });
							        $("#referral-entry-modal").modal("toggle");
								}else{
									Alerts.alert({
							            icon: 'fa fa-check',
							            title: "Success",
							            content: "Successfully Added New Allotment",
							            callback: function (result) {
							                Alerts.close();
							            }
							        });
								}

								$("#entry_date").val(today);
								$("#account").val('');
								$("#sub_account").val('');
								$("#control_no").val('');
								$("#amount").val('');
								$("#fund").val('');
								$("#remarks").val('');
								$("#actualBalance").val("₱ "+ numberWithCommass(obj.actualBal));
								$("#remainBalance").val("₱ "+ numberWithCommass(obj.remainBal));
							  	return false;
							}else if(obj.result == 'failed'){
								Alerts.alert({
						            icon: 'fa fa-times',
						            title: "Error",
						            content: obj.message,
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
	}

});

$('#btn_reqsearch').on('click', function(e){
	var request_source = $('#request_source').val();
    var status = $('#status').val();
    var date_from = $('#date_from').val();
    var date_to = $('#date_to').val();
	$.fn.yiiGridView.update('patient-request-list-grid', {
       type:'GET',
       data: {'request_source' : request_source, 'status' : status, 'date_from' : date_from, 'date_to' : date_to}
  	});
});



JAVASCRIPT
    , CClientScript::POS_READY);
?>

<h2 align="center">Cash Credit and Collection</h2>

<?php 

$this->renderPartial('_cashcreditcollection', array('model'=>$model, 'referrals'=>$referrals, 'costCentersList' => $costCentersList, 'requestStatus' => $requestStatusList, 'modelGrants' => $modelGrants)); ?>


