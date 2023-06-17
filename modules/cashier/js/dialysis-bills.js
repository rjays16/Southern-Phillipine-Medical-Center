function searchBills() {
    var o = new Object;
    o['query'] = $('search').value;
    o['pid'] = $('pid').value;
    bills.fetcherParams = o;
    bills.reload();
    return false;
}

function addBills(details) {
    list = $('bills');
    if (list) {
        var dBody=list.select("tbody")[0];
        if (dBody) {
            if (typeof(details)=='object') {
                var bill_nr=details.bill_nr,
                request_date=details.request_date,
                fullname=details.fullname,
                bill_type=details.bill_type,
                amount=details.amount,
                status=details.status;
                    
                var dRows = dBody.select("tr");
                var alt = (dRows.length%2>0) ? 'alt':'';
                var options;
                var index = dRows.length;
                switch(status) {
                    case "":
                        options = new Element('input', {
                            class : 'segButton',
                            type : 'button',
                            value : '>',
                            onclick : 'prepareAdd("' + index + '")'
                        });
                        break;
                    default:
                        options = new Element('img', {
                            align : 'absmiddle', 
                            src:'../../images/flag_'+ status + '.gif'
                        });
                        break;
                }
                  
                var row = new Element('tr', {
                    class: alt, 
                    style:'height:26px',
                  
                } )
                .insert(new Element('td', {
                    class:'centerAlign',
                    id:'bnr_' + index 
                } ).update(bill_nr))
                .insert(new Element('td', {
                    class:'centerAlign',
                    id:'req_' + index 
                } ).update(request_date))
                .insert(new Element('td', {
                    class:'centerAlign',
                    id:'name_' + index 
                } ).update(fullname))
                //                .insert(new Element('td', {
                //                    class:'centerAlign',
                //                    id:'btype_' + index 
                //                })
                //                 .update(bill_type))
                .insert(new Element('td', {
                    class:'centerAlign',
                    id:'amt_' + index 
                } )
                .update(amount))
                .insert(new Element('td', {
                    id:'status_' + index,
                    class:'centerAlign'
                } ).update(options));
                dBody.insert(row);
            }
            else {
                dBody.update('<tr><td colspan="4">List is currently empty...</td></tr>');
                return true;
            }
        }
        return false;
    }
}
        
function prepareAdd(index) {
    var billNr = document.getElementById('bnr_'+index).innerHTML;
    $j = jQuery.noConflict();
    var pid = $j('#pid').val();
    $j.getJSON('ajax/ajax_bill_details.php', {
        bill_nr:billNr, 
        pid: pid
    }, function(data) {
        var res = data;
        for(var i=0;i<res.size();i++) {
            addToTray(res[i]);
        }
    }).fail(function(data){
        return false;
    });
    return true;
}

function addToTray(d) {
    var details = new Object();
    details.id = d.bill_nr;
    details.name = d.bill_type == 'PH' ? 'Dialysis Pre-Bill PH' : 'Dialysis Pre-Bill NPH';
    details.desc = d.bill_type;
    details.qty = 1;
    details.origprice = d.amount;
    details.price =  d.amount;
    details.ispaid = 0;
    details.checked= 1;
    details.showdel= 1;
    details.calculate= 1;
    details.doreplace = 1;
    details.limit= -1;
    details.src = 'db';
    details.ref = '0000000000';
    result = window.parent.addServiceToList(details);
    return true;
}
   
    




