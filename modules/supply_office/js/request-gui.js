var ViewMode = false;

function display(str) {
    document.write(str);
}

var totalDiscount = 0;

function parseFloatEx(x) {
    var str = x.toString().replace(/\,|\s/,'')          
    return parseFloat(str)
} 

function warnClear() {
    var items = document.getElementsByName('items[]');
    if (items.length == 0) return true;
    else return confirm('Performing this action will clear the order tray. Do you wish to continue?');
}

function formatNumber(num,dec) {
    var nf = new NumberFormat(num);
    if (isNaN(dec)) dec = nf.NO_ROUNDING;
    nf.setPlaces(dec);
    return nf.toFormatted();
}

function resetRefNo(newRefNo,error) {
    $("refno").style.color = error ? "#ff0000" : "";
    $("refno").value=newRefNo;
}

function pSearchClose() {
    var nr = $('encounter_nr').value;
    cClick();
}
    
function emptyTray() {
    warnClear();
    clearOrder($('order-list'));
    addItemToRequest($('order-list'),null);
    //refreshDiscount();
}

function reclassRows(list,startIndex) {
    if (list) {
        var dBody=list.getElementsByTagName("tbody")[0];
        if (dBody) {
            var dRows = dBody.getElementsByTagName("tr");
            if (dRows) {
                for (i=startIndex;i<dRows.length;i++) {
                    dRows[i].className = "wardlistrow"+(i%2+1);
                }
            }
        }
    }
}

function clearOrder(list) {    
    if (!list) list = $('order-list')
    if (list) {
        var dBody=list.getElementsByTagName("tbody")[0]
        if (dBody) {
            trayItems = 0
            dBody.innerHTML = ""
            return true
        }
    }
    return false
}

function jsRqstngAreaOptionChng(obj, value){
    if(obj.id == 'ori_area') {        
        //$('opw_nr').value  = value;   
        js_ClearOptions('des_area');
        xajax_getRequestedAreas(value);
    }
}

function showRequestedAreas(options) {
    $('requested_area').innerHTML = options;
}

function addItemToRequest(list, details) {
    if (!list) list = $('order-list');
    if (list) {
        var dBody=list.getElementsByTagName("tbody")[0];
        if (dBody) {
            var src;            
            var dRows = dBody.getElementsByTagName("tr");
            
            var items = document.getElementsByName('items[]');
            if (items.length == 0) {
                clearOrder(list);
            }                           
            alt = (dRows.length%2)+1   
                              
            if (details) {       
                //alert('Row is '+lastRow);
                                                  
                src = '<tr class="wardlistrow'+alt+'" id="row_'+details.id+'">' +                        
                          '<input type="hidden" name="items[]" id="item_'+details.id+'" value="'+details.id+'" />'+
                          '<input type="hidden" name="descs[]" id="desc_'+details.id+'" value="'+details.desc+'" />'+
                          '<input type="hidden" name="unit_ids[]" id="unit_'+details.id+'" value="'+details.unit+'" />'+
                          '<input type="hidden" name="is_unitpcs[]" id="is_pc_'+details.id+'" value="'+details.is_perpc+'" />'+
                          '<input type="hidden" name="qtys[]" id="qty_'+details.id+'" value="'+details.qty+'" />'+
                          '<td width="4%"><img class="segSimulatedLink" src="../../images/close_small.gif" border="0" onclick="removeItem(\''+details.id+'\')"/></td>'+
                          '<td width="10%">'+details.id+'</td>'+
                          '<td width="25%">'+details.name+'</td>'+
                          '<td width="*">'+details.desc+'</td>'+
                          '<td width="4%">'+formatNumber(Number(details.qty),3)+'</td>'+
                          '<td width="4%">'+details.unit_name+'</td>'+
                      '</tr>';
            }
            else {
                src = "<tr><td colspan=\"6\">Request list is currently empty ...</td></tr>";
            }
                
            dBody.innerHTML += src;
            return true;
        }
    }   
    return false
}

function removeItem(id) {
    var destTable, destRows;
    var table = $('order-list');
    var rmvRow=document.getElementById("row_"+id);
    if (table && rmvRow) {        
        var rndx = rmvRow.rowIndex-1;
        table.deleteRow(rmvRow.rowIndex);        
        if (!document.getElementsByName("items[]") || document.getElementsByName("items[]").length <= 0)
            addItemToRequest(table, null);           
        reclassRows(table,rndx);
    }
    else
        alert(table+' and '+rmvRow);
//    refreshTotal();
}

