var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;

function display(str) {
    if($('ajax_display')) $('ajax_display').innerHTML = str.replace('\n','<br>');
}

function formatNumber(num,dec) {
    var nf = new NumberFormat(num);
    if (isNaN(dec)) dec = nf.NO_ROUNDING;
    nf.setPlaces(dec);
    return nf.toFormatted();
}

function parseFloatEx(x) {
    var str = x.toString().replace(/\,|\s/,'')
    return parseFloat(str)    
}

function setPagination(pageno, lastpage, pagen, total) {
    currentPage=parseInt(pageno);
    lastPage=parseInt(lastpage);    
    firstRec = (parseInt(pageno)*pagen)+1;
    if (currentPage==lastPage)
        lastRec = total;
    else
        lastRec = (parseInt(pageno)+1)*pagen;
    if (parseInt(total))
        $("pageShow").innerHTML = '<span>Showing '+(formatNumber(firstRec))+'-'+(formatNumber(lastRec))+' out of '+(formatNumber(parseInt(total)))+' record(s)</span>';
    else
        $("pageShow").innerHTML = '';
    $("pageFirst").className = (currentPage>0 && lastPage>0) ? "segSimulatedLink" : "segDisabledLink";
    $("pagePrev").className = (currentPage>0 && lastPage>0) ? "segSimulatedLink" : "segDisabledLink";
    $("pageNext").className = (currentPage<lastPage) ? "segSimulatedLink" : "segDisabledLink";
    $("pageLast").className = (currentPage<lastPage) ? "segSimulatedLink" : "segDisabledLink";
}

function jumpToPage(el, jumpType, set) {
    if (el.className=="segDisabledLink") return false;
    if (lastPage==0) return false;
    switch(jumpType) {
        case FIRST_PAGE:
            if (currentPage==0) return false;
            startAJAXSearch('search',0);
        break;
        case PREV_PAGE:
            if (currentPage==0) return false;
            startAJAXSearch('search',currentPage-1);
        break;
        case NEXT_PAGE:
            if (currentPage >= lastPage) return false;
            startAJAXSearch('search',parseInt(currentPage)+1);
        break;
        case LAST_PAGE:
            if (currentPage >= lastPage) return false;
            startAJAXSearch('search',lastPage);
        break;
    }
}

function prepareAdd(id) {
    var details = new Object();
    
     var    qty=0;
    
    qty = prompt("Enter quantity of package to be issued:")
            if (qty === null) return false;
 
    
    details.id = $('id'+id).innerHTML;
    details.name = $('name'+id).innerHTML;
    details.desc = $('desc'+id).innerHTML;
    //details.pending = $('pending'+id).value;
    details.pending = qty;
    details.unitid = $('unitid'+id).value;
    details.unitdesc = $('unitdesc'+id).value;
    details.perpc = $('perpc'+id).value;

    var list = window.parent.document.getElementById('order-list');
    result = window.parent.appendOrder(list,details);
    if (result) 
        alert('Item added to order list...');
    else
        alert('Failed to add item...');
    if (window.parent && window.parent.refreshDiscount) window.parent.refreshDiscount()
    
    
}

function prepareAddPc(id) {
    var details = new Object();
    
     var    qty=0;
    
    qty = prompt("Enter the number of pieces to be issued:")
            if (qty === null) return false;
    
    details.id = $('id'+id).innerHTML;
    details.name = $('name'+id).innerHTML;
    details.desc = $('desc'+id).innerHTML;
    //details.pending = $('pending'+id).value;
    details.pending = qty;
    details.unitid = 2;
    details.unitdesc = "piece";
    details.perpc = 1;


    var list = window.parent.document.getElementById('order-list');
    result = window.parent.appendOrder(list,details);
    if (result) 
        alert('Item added to order list...');
    else
        alert('Failed to add item...');
    if (window.parent && window.parent.refreshDiscount) window.parent.refreshDiscount()
}

function clearList(listID) {
    // Search for the source row table element
    var list=$(listID),dRows, dBody;
    if (list) {
        dBody=list.getElementsByTagName("tbody")[0];
        if (dBody) {
            dBody.innerHTML = "";
            return true;    // success
        }
        else return false;    // fail
    }
    else return false;    // fail
}

function clearExp(listID) {
    // Search for the source row table element
    var list=$(listID),dRows, dBody;
    if (list) {
        dBody=list.getElementsByTagName("select")[0];
        if (dBody) {
            dBody.innerHTML = "<option value=''>Select Expiry</option>";
            return true;    // success
        }
        else return false;    // fail
    }
    else return false;    // fail
    
}

function addProductToList(listID, details ) {
    // ,id, name, desc, cash, charge, cashsc, chargesc, d, soc
    var list=$(listID), dRows, dBody, rowSrc;
    var i,val;
    /*
    for( i = 0; i < document.forms['radioButtonForm'].elements['item_type'].length; i++ )
    {
        if(document.forms['radioButtonForm'].elements['item_type'][i].checked) {
            val = document.forms['radioButtonForm'].elements['item_type'][i].value;
        }
    }
    */
    val = $('item_type_list').value;
    //area_destination = $('area_dest').value; 
    
    /*
    munit_expdate_hidden
    mpc_expdate_hidden
    */
    /*
    var select = $('med_pck'), sBody, sOption;
    var select1 = $('med_pc'), sBody1, sOption1;
    
    if(details.exparray == "") {
        clearExp(select);
        clearExp(select1);
    }
     
    
    if(details.exparray != "")
    {   
        sBody=select.getElementsByTagName("select")[0];
        
        var option = details.exparray;
        
        sOption = option; 
        sBody.innerHTML += sOption; 
    }
    
    if(details.exparray != "")
    {   
        sBody1=select1.getElementsByTagName("select")[0];
        
        var option = details.exparray;
        
        sOption1 = option; 
        sBody1.innerHTML += sOption1; 
    }
    */
    
    var filter = val;

    if (list) {
        dBody=list.getElementsByTagName("tbody")[0];
        dRows=dBody.getElementsByTagName("tr");

        // get the last row id and extract the current row no.
            
        if (typeof(details)=="object") {
            var id = details.id,
                name = details.name,
                desc = details.desc,
                pending = details.pending,
                unitid = details.unitid,
                perpc = details.perpc,
                d = details.d,
                unitdesc = details.unitdesc,
                exparray = details.exparray,
                qtyperpack = details.qtyperpack,
                soc = details.soc;
            
                
            var cashHTML, chargeHTML;
            var cashSeniorHTML, chargeSeniorHTML;
            var pugee,pangit;
            
            //if (d>=0)
            //$('temporaryid').value = id;
                rowSrc = "<tr>"+
                                    '<td>'+
                                        '<span id="name'+id+'" style="font:bold 12px Arial;color:#000066">'+name+'</span><br />'+
                                        '<div style=""><div id="desc'+id+'" style="font:normal 11px Arial; color:#404040">'+desc+'</div></div>'+
                                    '</td>'+
                                    '<td align="center">'+
                                        '<input id="soc'+id+'" type="hidden" value="'+soc+'"/>'+
                                        '<span id="id'+id+'" style="font:bold 11px Arial;color:#660000">'+id+'</span></td>'+
                                         '<input id="idpo" type="hidden" value="'+id+'"/>'+
                                    '<td align="right" colspan="2">'+
                                        '<input id="pending'+id+'" type="hidden" value="'+pending+'"/>'+ 
                                        '<input id="unitid'+id+'" type="hidden" value="'+unitid+'"/>'+
                                        '<input id="unitdesc'+id+'" type="hidden" value="'+unitdesc+'"/>'+
                                        '<input id="qtyperpack'+id+'" type="hidden" value="'+qtyperpack+'"/>'+
                                        '<input id="perpc'+id+'" type="hidden" value="'+perpc+'"/><span style="color:#008000">'+pending+'</span></td>'+
                                    '<td colspan="2"></td>'+
                                    '<td>'+
                                        '<input type="button" id="packadd'+id+'" value="Pck" style="color:#000066; font-weight:bold; padding:0px 2px" ';
                                            //'onclick="prepareAdd(\''+id+'\')" '+
                                            
                                        if(qtyperpack=='') {
                                            alert("yo");
                                            if(pending<10) {
                                            rowSrc += 'disabled';
                                            }
                                        } 
                                        else {
                                            if(parseInt(qtyperpack) > parseInt(pending) ) {
                                            alert("abot ko");
                                            rowSrc += 'disabled';
                                            }
                                        }
                                        
                    rowSrc +=       '/>'+
                                    '</td>'+
                                    '<td>'+
                                        '<input type="button" id="pcadd'+id+'" value="Pc" style="color:#000066; font-weight:bold; padding:0px 2px" '+
                                            //'onclick="prepareAddPc(\''+id+'\')" '+
                                        '/>'+
                                    '</td>'+
                                '</tr>';       
              

             
         if(filter=='M') {
            pugee1 = "packadd"+id;
            pangit1 = "pcadd"+id;
            //YAHOO.util.Event for per pack of medicine
             
            YAHOO.util.Event.addListener(pugee1, "click", function showMedicinePrompt(e, id) {
            
                var select = $('med_pck'), sBody, sOption;
                
                if(exparray == "") {
                    clearExp(select);
                }
                 
                
                if(exparray != "")
                {   
                    sBody=select.getElementsByTagName("select")[0];
                    
                    var option = exparray;
                    
                    sOption = option; 
                    sBody.innerHTML += sOption; 
                }
                
                var handleSubmit = function() {
                
                var details = new Object();
                var qty,extra,perpack;
                                
                qty = $('munit_qty').value;
                perpack = $('qtyperpack'+id).value;
                extra = $('munit_expdate_hidden').value;
                
                qty = qty*perpack;
                
                max = $('pending'+id).value;
                
                while ((max - qty) < 0){
                    qty = prompt("Enter a valid number of pieces to be issued:");
                        if (qty === null) return false;
                }
                
                qty = $('munit_qty').value;
                
                details.type = 'M';
                details.expdate = extra;
                details.serial = "-";

                details.id = $('id'+id).innerHTML;
                details.name = $('name'+id).innerHTML;
                details.desc = $('desc'+id).innerHTML;
                details.pending = qty;
                details.unitid = $('unitid'+id).value;
                details.unitdesc = $('unitdesc'+id).value;
                details.perpc = $('perpc'+id).value;

                var list = window.parent.document.getElementById('order-list');
                result = window.parent.appendOrder(list,details);
                if (result) 
                    alert('Item added to order list...');
                else
                    alert('Failed to add item...');
                if (window.parent && window.parent.refreshDiscount) window.parent.refreshDiscount();
                
                this.submit();
                };
                var handleCancel = function() {
                    this.cancel();
                };
            
                // Instantiate the Dialog
                YAHOO.equipprompt.container.mdialog = new YAHOO.widget.Dialog("medicineBox", 
                                                                             { width : "560px",
                                                                              fixedcenter : true,
                                                                              visible : false, 
                                                                              constraintoviewport : true,
                                                                              buttons : [ { text:"Save", handler:handleSubmit, isDefault:true },
                                                                                          { text:"Cancel", handler:handleCancel } ]
                                                                             } );
                                                                             
                YAHOO.equipprompt.container.mdialog.render();
                YAHOO.equipprompt.container.mdialog.show();    

                    
            }, id);
            //YAHOO.util.Event for per piece of medicine 
            YAHOO.util.Event.addListener(pangit1, "click", function showMedicinePcPrompt(e, id) {
            
                var select1 = $('med_pc'), sBody1, sOption1;
                  
                 if(exparray == "") {
                    clearExp(select1);
                }
                
                if(exparray != "")
                {   
                    sBody1=select1.getElementsByTagName("select")[0];
                    
                    var option = exparray;
                    
                    sOption1 = option; 
                    sBody1.innerHTML += sOption1; 
                }
                // Define various event handlers for Dialog
                var handleSubmit = function() {
                    var details = new Object();
                    var qty,mexpiry;
                                    
                    qty = $('mpc_qty').value;                                                   
                    mexpiry = $('mpc_expdate_hidden').value;
                    
                     max = $('pending'+id).value;
                
                    while ((max - qty) < 0){
                        qty = prompt("Enter a valid number of pieces to be issued:");
                            if (qty === null) return false;
                    }
                    
                    details.type = 'M';
                    details.expdate = mexpiry;
                    details.serial = "-";   

                    details.id = $('id'+id).innerHTML;
                    details.name = $('name'+id).innerHTML;
                    details.desc = $('desc'+id).innerHTML;
                    details.pending = qty;
                    details.unitid = 2;
                    details.unitdesc = "piece";
                    details.perpc = 1;

                    var list = window.parent.document.getElementById('order-list');
                    result = window.parent.appendOrder(list,details);
                    if (result) 
                        alert('Item added to order list...');
                    else
                        alert('Failed to add item...');
                    if (window.parent && window.parent.refreshDiscount) window.parent.refreshDiscount();
                    
                    this.submit();
                };
                var handleCancel = function() {
                    this.cancel();
                };        
                
                // Instantiate the Dialog
                YAHOO.equipprompt.container.mdialogPc = new YAHOO.widget.Dialog("medicineBoxPc", 
                                                                             { width : "560px",
                                                                              fixedcenter : true,
                                                                              visible : false, 
                                                                              constraintoviewport : true,
                                                                              buttons : [ { text:"Save", handler:handleSubmit, isDefault:true },
                                                                                          { text:"Cancel", handler:handleCancel } ]
                                                                             } );
                            
            
                YAHOO.equipprompt.container.mdialogPc.render();
                YAHOO.equipprompt.container.mdialogPc.show();    

                    
            }, id);
        }    
             
        if(filter=='E') {
             pugee = "packadd"+id;
             pangit = "pcadd"+id;
            //YAHOO.util.Event for per pack of equipment
            alert(pending);
            YAHOO.util.Event.addListener(pugee, "click", function showEquipPrompt(e, id) {    

            // Define various event handlers for Dialog
            var handleSubmit = function() {
                
                var details = new Object();
                var qty,extra;
                var max;
                                
                qty = $('eunit_qty').value;
                perpack = $('qtyperpack'+id).value;
                extra = $('eunit_serial').value; 
                
                qty = qty*perpack;
                
                max = $('pending'+id).value;
                
                while ((max - qty) < 0){
                    qty = prompt("Enter a valid number of pieces to be issued:");
                        if (qty === null) return false;
                }
                
                details.type = 'E';
                details.serial = extra;
                details.expdate = "-";

                details.id = $('id'+id).innerHTML;
                details.name = $('name'+id).innerHTML;
                details.desc = $('desc'+id).innerHTML;
                details.pending = qty;
                details.unitid = $('unitid'+id).value;
                details.unitdesc = $('unitdesc'+id).value;
                details.perpc = $('perpc'+id).value;

                var list = window.parent.document.getElementById('order-list');
                result = window.parent.appendOrder(list,details);
                if (result) 
                    alert('Item added to order list...');
                else
                    alert('Failed to add item...');
                if (window.parent && window.parent.refreshDiscount) window.parent.refreshDiscount();
                
                this.submit();
            };
            var handleCancel = function() {
                this.cancel();
            };
            
            // Instantiate the Dialog
            YAHOO.equipprompt.container.edialog = new YAHOO.widget.Dialog("equipmentBox", 
                                                                                     { width : "560px",
                                                                                      fixedcenter : true,
                                                                                      visible : false, 
                                                                                      constraintoviewport : true,
                                                                                      buttons : [ { text:"Save", handler:handleSubmit, isDefault:true },
                                                                                                  { text:"Cancel", handler:handleCancel } ]
                                                                                     } );


            YAHOO.equipprompt.container.edialog.render();
            YAHOO.equipprompt.container.edialog.show();    

                
            }, id);
            
            //YAHOO.util.Event for per piece of equipment
            YAHOO.util.Event.addListener(pangit, "click", function showEquipPcPrompt(e, id) {    

            // Define various event handlers for Dialog
            var handleSubmit = function() {
                var details = new Object();
                var qty,extra;
                                
                qty = $('epc_qty').value;
                extra = $('epc_serial').value;
                
                max = $('pending'+id).value;
                
                while ((max - qty) < 0){
                    qty = prompt("Enter a valid number of pieces to be issued:");
                        if (qty === null) return false;
                }
                
                details.type = 'E';
                details.serial = extra;
                details.expdate = "-"; 

                details.id = $('id'+id).innerHTML;
                details.name = $('name'+id).innerHTML;
                details.desc = $('desc'+id).innerHTML;
                details.pending = qty;
                details.unitid = 2;
                details.unitdesc = "piece";
                details.perpc = 1;

                var list = window.parent.document.getElementById('order-list');
                result = window.parent.appendOrder(list,details);
                if (result) 
                    alert('Item added to order list...');
                else
                    alert('Failed to add item...');
                if (window.parent && window.parent.refreshDiscount) window.parent.refreshDiscount();
                
                this.submit();
            };
            var handleCancel = function() {
                this.cancel();
            };        
            
            // Instantiate the Dialog
            YAHOO.equipprompt.container.edialogPc = new YAHOO.widget.Dialog("equipmentBoxPc", 
                                                                                     { width : "560px",
                                                                                      fixedcenter : true,
                                                                                      visible : false, 
                                                                                      constraintoviewport : true,
                                                                                      buttons : [ { text:"Save", handler:handleSubmit, isDefault:true },
                                                                                                  { text:"Cancel", handler:handleCancel } ]
                                                                                     } );
            
            YAHOO.equipprompt.container.edialogPc.render();
            YAHOO.equipprompt.container.edialogPc.show();    

        
            }, id);
        }
        

           
              
        }
        else {
            rowSrc = '<tr><td colspan="8" style="">No such product exists...</td></tr>';
        }
        dBody.innerHTML += rowSrc;
    }
}

//----------

function init(e){
    
    YAHOO.equipprompt.container.bBody = new YAHOO.widget.Module("bBody", {visible:true});
    YAHOO.equipprompt.container.bBody.render();
    
    
}//end function init

function initEquipmentPrompt(){

    var handleSubmit = function() {
        this.submit();
    };
    var handleCancel = function() {
        this.cancel();
    };
    
    // Instantiate the Dialog
    YAHOO.equipprompt.container.edialog = new YAHOO.widget.Dialog("equipmentBox", 
                                                                             { width : "560px",
                                                                              fixedcenter : true,
                                                                              visible : false, 
                                                                              constraintoviewport : true,
                                                                              buttons : [ { text:"Save", handler:handleSubmit, isDefault:true },
                                                                                          { text:"Cancel", handler:handleCancel } ]
                                                                             } );
    
    
    //YAHOO.util.Event.addListener("packadd", "click", showEquipPrompt); 
    //YAHOO.util.Event.addListener("searchbuttonpogz", "click", showEquipPrompt);   
}
/*
function showEquipPrompt() {    

  // Define various event handlers for Dialog
    var handleSubmit = function() {
        
        var details = new Object();
        var qty;
        
        //id = $('temporaryid').value;
        //qty = prompt("Enter the number of pieces to be issued:")
        //    if (qty === null) return false;
        
        qty = $('eunit_qty').value;
        
        details.id = $('id'+id).innerHTML;
        details.name = $('name'+id).innerHTML;
        details.desc = $('desc'+id).innerHTML;
        //details.pending = $('pending'+id).value;
        details.pending = qty;
        details.unitid = 2;
        details.unitdesc = "piece";
        details.perpc = 1;

        var list = window.parent.document.getElementById('order-list');
        result = window.parent.appendOrder(list,details);
        if (result) 
            alert('Item added to order list...');
        else
            alert('Failed to add item...');
        if (window.parent && window.parent.refreshDiscount) window.parent.refreshDiscount();
        
        this.submit();
    };
    var handleCancel = function() {
        this.cancel();
    };
    
    // Instantiate the Dialog
    YAHOO.equipprompt.container.edialog = new YAHOO.widget.Dialog("equipmentBox", 
                                                                             { width : "560px",
                                                                              fixedcenter : true,
                                                                              visible : false, 
                                                                              constraintoviewport : true,
                                                                              buttons : [ { text:"Save", handler:handleSubmit, isDefault:true },
                                                                                          { text:"Cancel", handler:handleCancel } ]
                                                                             } );


    YAHOO.equipprompt.container.edialog.render();
    YAHOO.equipprompt.container.edialog.show();    

        
}
*/

function initEquipmentPcPrompt(){
    // Define various event handlers for Dialog
    var handleSubmit = function() {
        this.submit();
    };
    var handleCancel = function() {
        this.cancel();
    };        
    
    // Instantiate the Dialog
    YAHOO.equipprompt.container.edialogPc = new YAHOO.widget.Dialog("equipmentBoxPc", 
                                                                             { width : "560px",
                                                                              fixedcenter : true,
                                                                              visible : false, 
                                                                              constraintoviewport : true,
                                                                              buttons : [ { text:"Save", handler:handleSubmit, isDefault:true },
                                                                                          { text:"Cancel", handler:handleCancel } ]
                                                                             } );
    
    
    //YAHOO.util.Event.addListener("packadd", "click", showEquipPrompt); 
    //YAHOO.util.Event.addListener("searchbuttonpogz", "click", showEquipPrompt);   
}
/*
function showEquipPcPrompt() {    

    YAHOO.equipprompt.container.edialogPc.render();
    YAHOO.equipprompt.container.edialogPc.show();    

        
}
*/
function initMedicinePrompt(){
    // Define various event handlers for Dialog
    var handleSubmit = function() {
        this.submit();
    };
    var handleCancel = function() {
        this.cancel();
    };        
    
    // Instantiate the Dialog
    YAHOO.equipprompt.container.mdialog = new YAHOO.widget.Dialog("medicineBox", 
                                                                             { width : "560px",
                                                                              fixedcenter : true,
                                                                              visible : false, 
                                                                              constraintoviewport : true,
                                                                              buttons : [ { text:"Save", handler:handleSubmit, isDefault:true },
                                                                                          { text:"Cancel", handler:handleCancel } ]
                                                                             } );
    
    
    //YAHOO.util.Event.addListener("packadd", "click", showEquipPrompt); 
    //YAHOO.util.Event.addListener("searchbuttonpogz", "click", showEquipPrompt);   
}
/*
function showMedicinePrompt() {    

    YAHOO.equipprompt.container.mdialog.render();
    YAHOO.equipprompt.container.mdialog.show();    

        
}
*/
function initMedicinePcPrompt(){
    // Define various event handlers for Dialog
    var handleSubmit = function() {
        this.submit();
    };
    var handleCancel = function() {
        this.cancel();
    };        
    
    // Instantiate the Dialog
    YAHOO.equipprompt.container.mdialogPc = new YAHOO.widget.Dialog("medicineBoxPc", 
                                                                             { width : "560px",
                                                                              fixedcenter : true,
                                                                              visible : false, 
                                                                              constraintoviewport : true,
                                                                              buttons : [ { text:"Save", handler:handleSubmit, isDefault:true },
                                                                                          { text:"Cancel", handler:handleCancel } ]
                                                                             } );
    
    
    //YAHOO.util.Event.addListener("packadd", "click", showEquipPrompt); 
    //YAHOO.util.Event.addListener("searchbuttonpogz", "click", showEquipPrompt);   
}
/*
function showMedicinePcPrompt() {  
  

    YAHOO.equipprompt.container.mdialogPc.render();
    YAHOO.equipprompt.container.mdialogPc.show();    

        
}
*/
