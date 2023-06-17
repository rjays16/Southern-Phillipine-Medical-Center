
function addBills(details) {
    list = $('lingapBills');
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
                discountid=details.discountid;

                var dRows = dBody.select("tr");
                var alt = (dRows.length%2>0) ? 'alt':'';
                var options;
                var index = dRows.length;
                switch(status) {
                    case "":
                        options = new Element('img', {
                            align : 'absmiddle', 
                            src:'../../images/btn_unpaiditem.gif'
                        });
                        break;
                    default:
                        options = new Element('img', {
                            align : 'absmiddle', 
                            src:'../../images/flag_'+ status + '.gif'
                        });
                        break;
                }
                var hiddenCheckbox = new Element('input', {
                    type: 'hidden',
                    name : 'box[' + index + ']',
                    value: 0
                });
                var checkbox = new Element('input', {
                    id: 'box_' + index,
                    type: 'checkbox',
                    class:'segInput',
                    'data-pos': index,
                    name : 'box[' + index + ']'
                });
                if (discountid == 'lingap') {
                    checkbox.disabled = true;
                    checkbox.checked = true;
                }

                var row = new Element('tr', {
                    class: alt,
                    style: 'height:26px',

                }).insert(new Element('td', {
                        class: 'centerAlign',
                    }).update(hiddenCheckbox)
                        .insert(checkbox)
                        .insert(new Element('input', {
                            type: 'hidden',
                            value: bill_nr,
                            name: 'bnr[' + index + ']'
                        })))
                .insert(new Element('td', {
                    class:'centerAlign',
                    id:'status_' + index 
                } ).update(options))
                .insert(new Element('td', {
                    class:'centerAlign',
                    id:'bnr_' + index 
                } ).update(bill_nr))
                .insert(new Element('td', {
                    class:'centerAlign',
                    id:'type_' + index 
                } ).update(bill_type))
                .insert(new Element('td', {
                    class:'centerAlign',
                    id:'amt_' + index 
                } )
                .update(amount))
                .insert(new Element('td', {
                    class:'centerAlign',
                    id:'net_' + index 
                })
                .update(amount));
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