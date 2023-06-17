function jsAccOptionsChange(obj, value, text){

    if(obj.id == 'wardlist') {
    	$J("#input_greater_twelve").hide();
        if(Number(value)>0){
            xajax_setWardRooms(value);
        }
        else{
            js_ClearOptions('roomlist');
            js_AddOptions('roomlist','- Select Room -', 0);
        }
    }
}

function js_ClearOptions(tagId){
    var id = '#'+tagId;
    $J(id).empty();
}

function js_AddOptions(tagId, text, value, info,is_per_hour){
    var elTarget = '#'+tagId;
    console.log(is_per_hour);
    $J(elTarget).append($J("<option></option>").val(value).text(text).attr('title', info).attr('data-hour', is_per_hour));
}

function reloadWindow(){
	location.reload();
}

function validateAdd(overlap){
	var adm_dt = new Date($J("#admission_dt").val());
	var now = new Date();

	if($J('#date_from').is(":visible")){
		var date_from = new Date($J("#date_from").val());
		var date_to = new Date($J("#date_to").val());
		var date_from_value = $J("#date_from").val();
		var date_to_value = $J("#date_to").val();
	}else{
		var date_from = new Date($J("#date_from_time").val());
		var date_to = new Date($J("#date_to_time").val());
		var date_from_value = $J("#date_from_time").val();
		var date_to_value = $J("#date_to_time").val();
	}

	var adt_month = '' + (adm_dt.getMonth() + 1),
        adt_day = '' + adm_dt.getDate(),
        adt_year = adm_dt.getFullYear();
    var adm_dt_parse = Date.parse(adt_month+'/'+adt_day+'/'+adt_year);

    var df_month = '' + (date_from.getMonth() + 1),
        df_day = '' + date_from.getDate(),
        df_year = date_from.getFullYear();
    var date_from_parse = Date.parse(df_month+'/'+df_day+'/'+df_year);

    var dt_month = '' + (date_to.getMonth() + 1),
        dt_day = '' + date_to.getDate(),
        dt_year = date_to.getFullYear();
    var date_to_parse = Date.parse(dt_month+'/'+dt_day+'/'+dt_year);

    if(!$J('#date_from').is(":visible")){
    	var adt_hours = '' + (adm_dt.getHours()),
	        adt_minutes = '' + adm_dt.getMinutes();
    	var adm_dt_parse = Date.parse(adt_month+'/'+adt_day+'/'+adt_year+" "+adt_hours+":"+adt_minutes);

	 	var df_hours = '' + (date_from.getHours()),
	        df_minutes = '' + date_from.getMinutes();
    	var date_from_parse = Date.parse(df_month+'/'+df_day+'/'+df_year+" "+df_hours+":"+df_minutes);

	    var dt_hours = '' + (date_to.getHours()),
	        dt_minutes = '' + date_to.getMinutes();
	    var date_to_parse = Date.parse(dt_month+'/'+dt_day+'/'+dt_year+" "+dt_hours+":"+dt_minutes);
    }

	if($J('#wardlist').val() == 0){
		alert("Please select ward");
	}else if($J('#roomlist').val() == 0){
		alert("Please select room");
	}else if(($J("#date_from").val() == '' && $J('#date_from').is(":visible"))||($J("#date_from_time").val() == '' && !$J('#date_from').is(":visible"))){
		alert("Please Indicate Start Date");
	}else if(($J("#date_to").val() == '' && $J('#date_to').is(":visible"))||($J("#date_to_time").val() == '' && !$J('#date_to').is(":visible"))){
		alert("Please Indicate End Date");
	}else if(overlap == 1){
		alert("Date selected has an accommodation");
	}else if(date_to_parse < date_from_parse){
		alert("Date to should not be earlier than from date");
	}else if(date_from_parse < adm_dt_parse || date_to_parse < adm_dt_parse){
		alert("Date selected should not be earlier than admission date");
	}else{
		var data = new Object();

	    data.ward_nr = Number($J('#wardlist').val().trim());
	    data.room_nr = Number($J('#roomlist').val().trim());
	    data.encounter_nr =  Number($J('#enc_nr').val().trim());
	    data.datefrom = date_from_value;
	    data.dateto = date_to_value;
	    data.ward_name = $J('#wardlist option:selected').text().trim();
	    var per_hour = $J('#roomlist option:selected').attr('data-hour');

	    if(per_hour != null)
	    	data.is_per_hour = per_hour;
	    else data.is_per_hour = null;

	    var bill_dt = $J('#bill_date').val();
	    xajax_saveAccommodation(data, bill_dt);
	}
}

function assignValue(id,val){
    $J('#'+id).val(val);
}

function deleteAccommodation(id){
	var answer = confirm("Are you sure you want to delete this accommodation?");
	var enc = $J("#enc_nr").val();

	if(answer){
		xajax_deleteAccommodation(id,enc);
	}
}

	jQuery(document).ready(function(){
		let dbDate = $J('#server_date').val();
		let dbAdmissionDate = $J('#server_admission_date').val();

		if($J("#enableTodayDateTo").val() == 1){
			datetoMax = '0';
		}else {
			datetoMax = -1;
		}
		$J('#addNewRow').click(function(){
			var enc = $J("#enc_nr").val();
			if($J('#date_from').is(":visible")){
				var date_from = $J("#date_from").val();
				var date_to = $J("#date_to").val();
			}else{
				var date_from = $J("#date_from_time").val();
				var date_to = $J("#date_to_time").val();
			}

			xajax_checkifOverlaps(enc, date_from, date_to);

		});


		if($J('#overlaps').val() == 1 || $J('#lack_of_date').val() == 1){
			$J("#opening-message" ).dialog({
		    	closeOnEscape: false,
		      	position: ['center',20],
		      	modal: true,
		      	buttons: {
		        	Ok: function() {
		         		$J( this ).dialog( "close" );
		        	}
		      	}
		    });
		}

		$J('#date_from').datepicker({
	        dateFormat: 'MM dd, yy',
	        beforeShow: function (input, inst) {
		        setTimeout(function () {
		            inst.dpDiv.css({
		                top: $J("#date_from").offset().top + 25,
		                left: $J("#date_from").offset().left
		            });
		        }, 0);
		    },
			defaultDate: new Date(dbDate),
			minDate: new Date(dbAdmissionDate),
		    maxDate: new Date(dbDate)
	    });

	    $J("#date_from").datepicker( "option", "disabled", true );

		$J('#ui-datepicker-div').hide();

		$J('#date_to').datepicker({
	        dateFormat: 'MM dd, yy',
	        beforeShow: function (input, inst) {
		        setTimeout(function () {
		            inst.dpDiv.css({
		                top: $J("#date_to").offset().top + 25,
		                left: $J("#date_to").offset().left
		            });
		        }, 0);
		    },
			defaultDate: new Date(dbDate),
			minDate: new Date(dbAdmissionDate),
		    maxDate: new Date(dbDate)
	    });

	    $J("#date_to").datepicker( "option", "disabled", true );

		$J('#ui-datepicker-div').hide();

		$J('#date_from_time').datetimepicker({
	        dateFormat: 'MM dd, yy',
	        timeFormat: "hh:mm TT",
	        beforeShow: function (input, inst) {
		        setTimeout(function () {
		            inst.dpDiv.css({
		                top: $J("#date_from_time").offset().top + 25,
		                left: $J("#date_from_time").offset().left
		            });
		        }, 0);
		    },
			defaultDate: new Date(dbDate),
			minDate: new Date(dbAdmissionDate),
			maxDate: new Date(dbDate)
	    });

	    $J("#date_from_time").datetimepicker( "option", "disabled", true );

		$J('#ui-datepicker-div').hide();

		$J('#date_to_time').datetimepicker({
	        dateFormat: 'MM dd, yy',
			timeFormat: "hh:mm TT",
	        beforeShow: function (input, inst) {
		        setTimeout(function () {
		            inst.dpDiv.css({
		                top: $J("#date_to_time").offset().top + 25,
		                left: $J("#date_to_time").offset().left
		            });
		        }, 0);
		    },
			defaultDate: new Date(dbDate),
			minDate: new Date(dbAdmissionDate),
			maxDate: new Date(dbDate)
	    });

	    $J("#date_to_time").datetimepicker( "option", "disabled", true );

		$J('#ui-datepicker-div').hide();
		

		$J('#audittrailbtn').click(function () {
	        var enc = $J('#enc_nr').val();

	        var auditTrailLink = '../../modules/nursing/nursing-accommodation-trail.php?encounter_nr='+enc;
	        var auditTrailDialog = $J('<div></div>')
	            .html('<iframe style="border: none;" src="' + auditTrailLink + '" width="100%" height="100%"></iframe>')
	            .dialog({
	                autoOpen: true,
	                modal: true,
	                show: 'fade',
	                hide: 'fade',
	                height: 450,
	                width: '85%',
	                title: 'Audit Trail',
	                close: function () {
	                    location.reload();
	                }
	            });

	        return false;
	    });

	    $J("#roomlist").change(function(){
    	    var option = $J('option:selected', this).attr('data-hour');
    	    console.log(option);
    	    if(option == 1){
    	    	$J('#date_from').hide();
    	    	$J('#date_to').hide();
    	    	$J('#date_from_time').show();
    	    	$J('#date_to_time').show();
    	    	$J('#date_from_time').val('');
    	    	$J('#date_to_time').val('');
    	    }else{
    	    	$J('#date_from_time').hide();
    	    	$J('#date_to_time').hide();
    	    	$J('#date_from').show();
    	    	$J('#date_to').show();
    	    	$J('#date_from').val('');
    	    	$J('#date_from').val('');
    	    }
	    });
	});