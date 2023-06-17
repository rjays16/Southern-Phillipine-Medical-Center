
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

    
function emptyTray() {
    clearOrder($('order-list'));
    appendOrder($('order-list'),null);
    refreshDiscount();
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

function appendOrder(list, details, disabled) {
    if (!list) list = $('order-list');
    if (list) {
        var dBody=list.getElementsByTagName("tbody")[0];
        
        if (dBody) {
            var src;
            var lastRowNum = null,
                    items = document.getElementsByName('items[]');
                    dRows = dBody.getElementsByTagName("tr");
            if (details) {
            
                var id = details.id,
                    name = details.name,
                    desc = details.desc,
                    pending = details.pending,
                    d = details.d,
                    soc = details.soc,
                    unitid = details.unitid,
                    unitdesc = details.unitdesc,
                    perpc = details.perpc,
                    type = details.type,
                    expdate = details.expdate,
                    serial = details.serial;
                    
                    
                
                if (items) {
                    if ($('rowID'+id)) {
 
                        var itemRow = $('row'+id),
                                itemQty = $('rowQty'+id)
                        itemQty.value = parseFloatEx(itemQty.value) + parseFloatEx(details.qty)
                        itemQty.setAttribute('prevValue',itemQty.value)
                        qty = parseFloatEx(itemQty.value)
                        tot = netPrice*qty
                        //$('rowid'+id).value     =   details.id
                        $('rowname'+id).value    = details.name
                        $('rowdesc'+id).value            = details.desc
                        $('rowpending'+id).value        = details.pending
                        $('rowunitid'+id).value        = details.unitid
                        $('rowunitdesc'+id).value        = details.unitdesc
                        $('rowperpc'+id).value        = details.perpc
                        $('rowexpdate'+id).value        = details.expdate
                        $('rowserial'+id).value        = details.serial
                        $('rowd'+id).value        = details.d
                        $('rowsoc'+id).value        = details.soc
                        
                        return true                        
                        return true
                    }
                    if (items.length == 0) clearOrder(list)
                }
                
                alt = (dRows.length%2)+1         
                
                var disabledAttrib = disabled ? 'disabled="disabled"' : ""
                
                src = 
                    '<tr class="wardlistrow'+alt+'" id="row'+id+'">' +
                    '<input type="hidden" name="soc[]" id="rowsoc'+id+'" value="'+details.soc+'" />'+
                    '<input type="hidden" name="d[]" id="rows'+id+'" value="'+details.d+'" />'+
                    '<input type="hidden" name="pending[]" id="rowpending'+id+'" value="'+details.pending+'" />'+
                    '<input type="hidden" name="desc[]" id="rowdesc'+id+'" value="'+details.desc+'" />'+
                    '<input type="hidden" name="name[]" id="rowname'+id+'" value="'+details.name+'" />'+
                    '<input type="hidden" name="unitid[]" id="rowunitid'+id+'" value="'+details.unitid+'" />'+
                    '<input type="hidden" name="unitdesc[]" id="rowunitdesc'+id+'" value="'+details.unitdesc+'" />'+
                    '<input type="hidden" name="perpc[]" id="rowperpc'+id+'" value="'+details.perpc+'" />'+
                    '<input type="hidden" name="expdate[]" id="rowexpdate'+id+'" value="'+details.expdate+'" />'+ 
                    '<input type="hidden" name="serial[]" id="rowserial'+id+'" value="'+details.serial+'" />'+ 
                    '<input type="hidden" name="items[]" id="rowis'+id+'" value="'+details.id+'" />';
                
                if (disabled)
                    src+='<td></td>'
                else
                    src+='<td class="centerAlign" width="5%"><img class="segSimulatedLink" src="../../images/close_small.gif" border="0" onclick="removeItem(\''+id+'\')"/></td>'
                

                src+=
                    '<td>'+details.id+'</td>'+
                    '<td width="30%"><span style="color:#660000">'+details.name+'</span></td>'+
                    '<td width="35%"><span style="color:#660000">'+details.desc+'</span></td>'+
                    '<td align="center"><span style="color:#660000">'+details.pending+'</span></td>'+
                    '<td align="center"><span style="color:#660000">'+details.unitdesc+'</span></td></tr>';
                
                trayItems++;
            }
            else {
                src = "<tr><td colspan=\"8\">Issue list is currently empty...</td></tr>";    
            }
            dBody.innerHTML += src;
            return true;
        }
    }
    return false;
}

function refreshDiscount() {
    var nodes;
    var nr = $('encounter_nr').value;
    if (nr)
        nodes = document.getElementsByName("charity[]");
    else
        nodes=document.getElementsByName("discount[]");
    totalDiscount = 0;
    if (nodes) {
        for (var i=0;i<nodes.length;i++) {
            if (nodes[i].value) totalDiscount += parseFloatEx(nodes[i].getAttribute('discount'));
        }
    }
    var dItem = $("show-discount");
    if (dItem) {
        dItem.value = parseFloatEx(totalDiscount * 100).toFixed(2);
    }
    refreshTotal();
}

function refreshTotal() {
    
}

function formatNumber(num,dec) {
    var nf = new NumberFormat(num);
    if (isNaN(dec)) dec = nf.NO_ROUNDING;
    nf.setPlaces(dec);
    return nf.toFormatted();
}

function pSearchClose() {
    var nr = $('encounter_nr').value;

    cClick();
}

function removeItem(id) {
    var destTable, destRows;
    var table = $('order-list');
    var rmvRow=document.getElementById("row"+id);
    if (table && rmvRow) {
        var rndx = rmvRow.rowIndex-1;
        table.deleteRow(rmvRow.rowIndex);
        if (!document.getElementsByName("items[]") || document.getElementsByName("items[]").length <= 0)
            appendOrder(table, null);
        reclassRows(table,rndx);
    }
    refreshTotal();
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
