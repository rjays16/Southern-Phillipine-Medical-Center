$(document).ready(function() {
	var printRoundsClass = $(".printRoundsClass");
    var printEndorsementList = $(".printEndorsementList");
	var chooseTimeDiv = $(".chooseTime");
	var blackBackground = $(".blackBackground");
	var okShift = $(".okShift");
	var cancelShift = $(".cancelShift");
	var dietlist = $(".dietList");
	var vsmon = $(".vsmon");
	var mms = $(".mms");
    var cbgClass = $(".cbgClass");

	blackBackground.hide();
	chooseTimeDiv.hide();

	printRoundsClass.click(function(){
		        $('#formclicked').val('nursingrounds');
	  		if(chooseTimeDiv.is(':visible')){
	  			chooseTimeDiv.fadeOut(300);
	  			blackBackground.fadeOut(300);
			}
			else
			{
				chooseTimeDiv.fadeIn(300);
	  			blackBackground.fadeIn(300);
			}
	});

	printEndorsementList.click(function () {
		$('#formclicked').val('printEndorsementList');
		if(chooseTimeDiv.is(':visible')){
            chooseTimeDiv.fadeOut(300);
            blackBackground.fadeOut(300);
      }
      else
      {
          chooseTimeDiv.fadeIn(300);
            blackBackground.fadeIn(300);
      }
	});

    dietlist.click(function () {
        $('#formclicked').val('dietlist');

        okShiftClicked();
    });

	vsmon.click(function () {
		$('#formclicked').val('vsmon');

		okShiftClicked();
	});
		
	mms.click(function () {
		$('#formclicked').val('mms');

		okShiftClicked();
	});
        
    cbgClass.click(function () {
		$('#formclicked').val('cbgClass');
		getCBGIssuanceParams($('#ward_id').val(), $('#ward_name').val());
	});

	okShift.click(function(){
		blackBackground.fadeOut(300);
		chooseTimeDiv.fadeOut(300);
	});

});/*end of document ready function*/

function okShiftClicked()
{ 	
    var ward_name = $_GET('station');
    var ward_nr = $_GET('ward_nr');
    var timeChosen = $("#timeChosenPrintNursing option:selected").val();
    var formclicked = $('#formclicked').val();
    var url;

    if(formclicked == 'nursingrounds'){
            url = "reports/nursing-print-rounds.php?time=" + timeChosen + "&ward_name=" + ward_name + "&ward_nr="+ward_nr;
    }else if(formclicked == 'dietlist'){
            url = "reports/diet-list.php?ward_name=" + ward_name + "&ward_nr="+ward_nr;
    }else if(formclicked == 'vsmon'){
            url = "reports/vsmonitoring.php?ward_name=" + ward_name + "&ward_nr="+ward_nr;
    }else if(formclicked == 'mms'){
            url = "reports/medicinemonitoring.php?ward_name=" + ward_name + "&ward_nr="+ward_nr;
    }else if(formclicked=='printEndorsementList'){
         url = "reports/nursing-endorsement-sheet.php?time=" + timeChosen +"&ward_name=" + ward_name + "&ward_nr="+ward_nr;
    }

    var win = window.open(url, '_blank');
    if (win)
        win.focus();

}

function closeNurseRoundSelection() {
    var chooseTimeDiv = $(".chooseTime");
    var blackBackground = $(".blackBackground");    
    
    chooseTimeDiv.fadeOut(300);
    blackBackground.fadeOut(300);    
}

function closePatientClassification() {
    var updatePatientClassification = $(".updatePatientClassification");
    var pClassBackground = $(".pClassBackground");    
    
    updatePatientClassification.fadeOut(300);
    pClassBackground.fadeOut(300);    
}

/*AJAX FOR PRINTING NURSE ROUNDS*/
// $("#checkTimePrintNursing").submit(function(e){
// 										e.preventDefault();

// 										$.post
// 										(
// 											'../nursing-print-rounds.php',
// 											{
// 												timeChosenPrintNursing: $('input[orientationChoice]:checked').val();
// 											},
// 											function(result)
// 											{
												
// 											}

// 										);
// 									});

function getCBGIssuanceParams(wardId, wardName) 
{
    async function f() {    
        const { value: formValues } = await Swal.fire({
            title: 'CBG Issuance Report Filter',
            html:
              '<span>PERIOD FROM: </span><p><input id="fromDatetimePicker"></p>'+ 
              '<span>TO: </span><p><input id="toDatetimePicker"></p>'+
              '<span>WARD: </span><p><input id="current_ward" value="'+wardName+'" disabled="disabled"></p>',
            customClass: 'swal2-overflow',
            showCancelButton: true,
            onOpen: function() {
                $('#fromDatetimePicker').datetimepicker({
                    timepicker:false,
                    format:'Y/m/d'
                }).on('change', function () {
                    $('.xdsoft_datetimepicker').hide();
                });
                
                $('#toDatetimePicker').datetimepicker({
                    timepicker:false,
                    format:'Y/m/d'
                }).on('change', function () {
                    $('.xdsoft_datetimepicker').hide();
                });
            },
            preConfirm: () => {
              return [
                document.getElementById('fromDatetimePicker').value,
                document.getElementById('toDatetimePicker').value,
                wardId, 
                wardName
              ]
            }            
        })

        if (formValues) {
//            Swal.fire(JSON.stringify(formValues))                      
            var rawUrlData = { reportid: 'cbgstrips-issuance', 
                               repformat: 'pdf',
                               param:{fromDtTm:formValues[0],
                                      toDtTm:formValues[1],
                                      wardId:formValues[2],
                                      wardName:formValues[3]} };
            var urlParams = jQuery.param(rawUrlData);
            window.open('../../modules/reports/show_report.php?'+urlParams, '_blank');
        }      
    }
    
    f();        
}