function isValidSearch(key) {

        if (typeof(key)=='undefined') return false;
        var s=key.toUpperCase();

        return (
                        ///^[A-ZÑ\-\.]{2}[A-ZÑ\-\. ]*\s*,\s*[A-ZÑ\-\.]{2}[A-ZÑ\-\. ]*$/.test(s) ||
                        /^(0?[1-9]|1[012])[\/](0?[1-9]|[12][0-9]|3[01])[\/]\d{4}$/.test(s) ||
                        /^\d+$/.test(s)
        );
        //return 1;
}

function DisabledSearch(){
        var b=isValidSearch($('Search').value);
        $("searchButton").style.cursor=(b?"pointer":"default");
        $("searchButton").disabled = !b;
}

function viewResult(id,serials,refno,test_code){
    var urls = '../../modules/reports/reports/BB_Compatibility_Report_fpdf.php?id='+id+'&refno='+refno+'';

    window.open(urls, "viewBloodCompatibilityResult", "left=150, top=100, width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
}

function warn(callback) {
    callback();
}

function getReports() {
    searchSource();
}
