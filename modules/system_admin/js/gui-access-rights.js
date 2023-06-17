
//<!-- Created by Arvin 04/17/2018 -->
//Display Textfield if 'others' is selected
function deleteReason() {
	var select = $('#select-reason').val();
	var reason = $('#'+select).text();
	//var otherReason = $('#text_reason').val();

	if(select == 'OT'){
        $('#other_reason').show();
        $('#other_reason').val('');
        $('#delete_reason').val('');
        $('#delete_other_reason').val('');
        $('#delete_reason').val(reason);
    }
    else{
        $('#other_reason').hide();
        $('#other_reason').val('');
        $('#delete_reason').val('');
        $('#delete_other_reason').val('');
        $('#delete_reason').val(reason);
    }
}

//Update hidden input everytime user types
function getOtherReason(){

	$("#other_reason").keyup(function(key){
		var val = $(this).val();
		if(this.keyCode == 8){
		    // user has pressed backspace
		    array.pop(val);
		}
	  	$("#delete_other_reason").val(val);
	});
}

//Hide Table Row if the user is locked
function checkLock(lockTrigger) {
	var value = $('#lock_value').val();

	if (lockTrigger != 1){
		$("#text_reason").show();
	} else{
		$("#text_reason").hide();
	}
	deleteReason();

}

function validateForm() {
	var otherReason = $('#delete_other_reason').val();
	var select = $('#select-reason').val();
	
	if(select=='OT'){
		if(otherReason==''){
			alert('Reason must be filled out');
			return false;
		}
	}
}