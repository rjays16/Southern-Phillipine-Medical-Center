/*function preset(){
  
    $J('#Search').bind('keyup', function() {
      //if ((event.keyCode == 13)&&(isValidSearch($J('#Search').val()))) getReports();
      getReports();
    });
    
}*/

function isValidSearch(key) {

        if (typeof(key)=='undefined') return false;
        var s=key.toUpperCase();

        //update the regular expression to enable the search box, limit to pid, name and date with format mm/dd/YYYY
        /*return (
                        /^[A-ZÑ\-\.]{2}[A-ZÑ\-\. ]*\s*,\s*[A-ZÑ\-\.]{2}[A-ZÑ\-\. ]*$/.test(s) ||
                        /^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
                        /^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
                        /^(0[1-9]|1[012])\/(0[1-9]|[12]\d|3[01])\/(2\d{2})/.test(s) ||
                        /^\d+$/.test(s)
        );*/
        return (
                        /^[A-ZÑ\-\.]{2}[A-ZÑ\-\. ]*\s*,\s*[A-ZÑ\-\.]{2}[A-ZÑ\-\. ]*$/.test(s) ||
                        /^(0?[1-9]|1[012])[\/](0?[1-9]|[12][0-9]|3[01])[\/]\d{4}$/.test(s) ||
                        /^\d+$/.test(s)
        );
}

function DisabledSearch(){
        var b=isValidSearch($('Search').value);
        $("searchButton").style.cursor=(b?"pointer":"default");
        $("searchButton").disabled = !b;
}

function viewResult(filename){
    window.open("seg-lab-result-view.php?filename="+filename+"&showBrowser=1","viewPatientResult","left=150, top=100, width=800,height=600,menubar=no,resizable=yes,scrollbars=yes");
}

function viewParsedResult(pid, lis_order_no){
    warn(function(){
        jQuery('<div></div>')
            .html('<iframe style="width:100%;height:100%;border:none;" src="seg-lab-parsedresult-view.php?pid='+pid+'&lis_order_no='+lis_order_no+'"></iframe>')
            .dialog({
                modal: true,
                width: '80%',
                height: 500,
                title: 'Result',
                position: 'top',
                buttons: {
                    Close: function(){ jQuery(this).dialog('close'); }
                }
            });
    });
}

//added by Nick 3/14/2014
function viewParsedResult2(permission, pid, lis_order_no) {
    permission = 1; // temporary open to all
    if (permission != 1) {
        alert("You have no permission to access this feature.");
        return;
    }
    warn(function(){
        var urlholder = "seg-lab-report-hl7.php?pid=" + pid + "&lis_order_no=" + lis_order_no + "&showBrowser=1";
        window.open(urlholder, "viewPatientResult", "left=150, top=100, width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
    });
}

function warn(callback) {
    callback(); //updated by nick 1-15-2016, remove prompt
    //jQuery('<div></div>')
    //    .html('<strong style="color: #f00; font-size: 14pt;">To verify the result, please contact the laboratory department.</strong>')
    //    .dialog({
    //        modal: true,
    //        title: 'Warning',
    //        position: 'top',
    //        buttons: {
    //            Ok: function () {
    //                callback();
    //                jQuery(this).dialog('close');
    //            }
    //        }
    //    });
}

function getReports() {
    searchSource();
}
