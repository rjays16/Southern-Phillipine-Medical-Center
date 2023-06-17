{{$sFormStart}}

<script language="javascript" type="text/javascript">
<!--
    var discountItems = 0;

  function tabClick(obj) {
    if (obj.className=='segActiveTab') return false;
    var prodclass = document.getElementById('prod_class').value;    
    var dList = obj.parentNode;
    var tab;
    
    if (dList) {
      var listItems = dList.select("LI");
      if (obj) {
        for (var i=0;i<listItems.length;i++) {
          if (obj!=listItems[i]) {
            listItems[i].className = "";
            tab = listItems[i].getAttribute('segTab');
            if ($(tab))
              $(tab).style.display = "none";
          }
        }
        tab = obj.getAttribute('segTab');
        if ($(tab))  $(tab).style.display = "block";
        obj.className = "segActiveTab";
      }
    }
    onChangeProdClass(prodclass);
  }

    
    function toggleTBody(list) {
        var dTable = $(list);
        if (dTable) {
            var dBody = dTable.getElementsByTagName("TBODY")[0];
            if (dBody) dBody.style.display = (dBody.style.display=="none") ? "" : "none";
        }
    }
    
    function toggleCheckboxesByName(name, val) {
        var chk = document.getElementsByName(name);
        if (chk) {
            for (var i=0; i<chk.length; i++) {
                chk[i].checked = val;
            }
            return false;
        }
        return false;
    }
    
    function enableInputChildren(id, enable) {
        var el=$(id);
        if (el) {
            var children = el.getElementsByTagName("INPUT");
            if (children) {
                for (i=0;i<children.length;i++) {
                    children[i].disabled = !enable;
                }
                return true;
            }
        }
        return false;
    }

function formatNumber(num,dec) {
    var nf = new NumberFormat(num);
    if (isNaN(dec)) dec = nf.NO_ROUNDING;
    nf.setPlaces(dec);
    return nf.toFormatted();
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

function clearDiscount(list) {
    if (!list) list = $('discountprices');
    if (list) {
        var dBody=list.getElementsByTagName("tbody")[0];
        if (dBody) {
            discountItems = 0;
            dBody.innerHTML = "";
            return true;
        }
    }
    return false;
}

function removeDiscount(id) {
    var destTable, destRows;
    var table = $('discountprices');
    var rmvRow=document.getElementById("row"+id);
    if (table && rmvRow) {
        var rndx = rmvRow.rowIndex-1;
        table.deleteRow(rmvRow.rowIndex);
        if (!document.getElementsByName("discounts[]") || document.getElementsByName("discounts[]").length <= 0)
            addDiscount(false, null);
        reclassRows(table,rndx);
    }
}

function addDiscount(list,details) {
    if (!list) list = $('discountprices');
    if (list) {
        var dBody=list.getElementsByTagName("tbody")[0];
        if (dBody) {
            var src;
            var lastRowNum = null,
                    items = document.getElementsByName('discounts[]');
                    dRows = dBody.getElementsByTagName("tr");
            if (details) {
                var id = details.id;
                var showPrice = (isNaN(details.price) || details.price==0) ? 'Arbitrary' : formatNumber(details.price,2);
                if (items) {
                    if ($('id'+id)) {
                        //alert($('qty'+id).innerHTML);
                        $('price'+id).value    = details.price;
                        $('show-price'+id).innerHTML     = showPrice;
                        return true;
                    }
                    if (items.length == 0) clearDiscount(list);
                }

                alt = (dRows.length%2)+1;
                src = 
                    '<tr class="wardlistrow'+alt+'" id="row'+id+'">' +
                    '<input type="hidden" name="discounts[]" id="id'+id+'" value="'+details.id+'" />'+
                    '<input type="hidden" name="price[]" id="price'+id+'" value="'+details.price+'" />'+
                    '<td><span style="color:#660000">'+details.name+'</span></td>'+
                    '<td class="rightAlign">'+
                        '<span id="show-price'+id+'">'+showPrice+'</span>'+
                    '</td>'+
                    '<td class="centerAlign"><img class="segSimulatedLink" src="../../images/close_small.gif" border="0" onclick="removeDiscount(\''+id+'\')"/></td>'+
                '</tr>';
                discountItems++;
            }
            else {
                src = "<tr><td colspan=\"3\">No discounts added...</td></tr>";    
            }
            dBody.innerHTML += src;
            return true;
        }
    }
    return false;
}

function prepareAdd() {
    var details = new Object();
    details.id = $("sel-discount").options[$("sel-discount").selectedIndex].value;
    details.name = $("sel-discount").options[$("sel-discount").selectedIndex].text;
    details.price = $("inp-discount").value;
    addDiscount($('discountprices'),details);
}
    
-->
</script>
<ul id="request-tabs" class="segTab" style="padding-left:20px;">
  <li class="segActiveTab" onclick="tabClick(this)" segTab="info">
    <h2 class="segTabText">Information</h2>
  </li>
  <li onclick="tabClick(this)" segTab="price">
    <h2 class="segTabText">Pricing</h2>
  </li>
    <li onclick="tabClick(this)" segTab="class">
    <h2 class="segTabText">Extra</h2>
  </li>
  &nbsp;
</ul>
<div style="width:95%; border-top:2px solid #4e8ccf;margin:4px; margin-top:0; padding-top:10px; min-height:300px">
  <div id="info" style="padding:2px;padding-top:3px;width:95%">
      <table border="0" cellspacing="0" cellpadding="2" width="99%" align="center" style="border-collapse:collapse; color:black">
          <tbody>
              <tr>
                  <td class="segPanel" align="right" valign="middle" width="18%"><strong>Product Code</strong></td>
                  <td class="segPanel2" align="left" valign="middle" width="30%" style="">
                      {{$sProductCode}}
                  </td>
                  <td class="segPanel2" align="left" valign="middle" width="*" style="">
                      <strong>Unique product identification code</strong>
                  </td>
              </tr>
        <tr>
          <td class="segPanel" align="right" valign="middle"><strong>Type</strong></td>
          <td class="segPanel2" align="left" valign="middle" style="border-right:0">
            {{$sProductType}}
          </td>
          <td class="segPanel2" align="left" valign="middle" style="border-left:0">
            <strong>Product type</strong>
          </td>
        </tr>
              <tr>
                  <td class="segPanel" align="right" valign="middle" width="18%"><strong>Generic name</strong></td>
                  <td class="segPanel2" align="left" valign="middle" width="30%" style="border-right:0">
                      {{$sGenericName}}
                  </td>
                  <td class="segPanel2" align="left" valign="middle" width="*" style="border-left:0">
                      <strong>International Nonproprietary Name for the product</strong>
                  </td>
              </tr>
              <tr>
                  <td class="segPanel" align="right" valign="middle"><strong>Product name</strong></td>
                  <td class="segPanel2" align="left" valign="middle" style="border-right:0">
                      {{$sProductName}}                    
                  </td>
                  <td class="segPanel2" align="left" valign="middle" style="border-left:0">
                      <strong>Full name of the product</strong>
                  </td>
              </tr>
  <!--            <tr>
                  <td class="segPanel" align="right" valign="middle"><strong>Description</strong></td>
                  <td class="segPanel2" align="left" valign="middle" style="border-right:0">
                      {{$sDescription}}
                  </td>
                  <td class="segPanel2" align="left" valign="middle" style="border-left:0">
                      <strong>Detailed product information</strong>
                  </td>
              </tr>
  -->
              <tr>
                  <td class="segPanel" align="right" valign="middle"><strong>Is socialized</strong></td>
                  <td class="segPanel2" align="left" valign="middle" style="border-right:0">
                      {{$sIsSocialized}}
                  </td>
                  <td class="segPanel2" align="left" valign="middle" style="border-left:0">
                      <strong>This product is covered by charity/socialized discounts</strong>
                  </td>
              </tr>
        <tr>
          <td class="segPanel" align="right" valign="middle"><strong>Is restricted</strong></td>
          <td class="segPanel2" align="left" valign="middle" style="border-right:0">
            {{$sIsRestricted}}
          </td>
          <td class="segPanel2" align="left" valign="middle" style="border-left:0">
            <strong>This item is in the list of restricted medicines/drugs</strong>
          </td>
        </tr>
              <tr>
                  <td class="segPanel" align="right" valign="middle">
                      <strong>Availability</strong><br />
                      <a href="javascript:void" onclick="toggleCheckboxesByName('availability[]',true); return false">Check all</a>
                  </td>
                  <td class="segPanel2" align="left" valign="middle" style="border-right:0">
                      {{$sAvailability}}
                  </td>
                  <td class="segPanel2" align="left" valign="middle" style="border-left:0">
                      <strong>Product availability in selected hospital areas</strong>
                  </td>
              </tr>
          </tbody>
      </table>
  </div>
  <div id="price" style="padding:2px;padding-top:3px;width:95%; display:none">
      <table border="0" cellspacing="0" cellpadding="2" width="99%" align="center" style="border-collapse:collapse; margin-top:2px; color:black">
          <tbody>        
              <tr>
                  <td class="segPanel" align="right" valign="middle" width="18%"><strong>Cash price</strong></td>
                  <td class="segPanel2" align="left" valign="middle" style="border-right:0;" width="30%">
                      {{$sCashPrice}}                    
                  </td>
                  <td class="segPanel2" align="left" valign="middle" style="border-left:0">
                      <strong>Default retail price (cash)</strong>
                  </td>
              </tr>
              <tr>
                  <td class="segPanel" align="right" valign="middle" width="18%"><strong>Charge price</strong></td>
                  <td class="segPanel2" align="left" valign="middle" style="border-right:0;" width="30%">
                      {{$sChargePrice}}
                  </td>
                  <td class="segPanel2" align="left" valign="middle" style="border-left:0">
                      <strong>Default retail price (charged)</strong>
                  </td>
              </tr>
              <tr>
                  <td class="segPanel" align="right" valign="middle"><strong>Discounted prices</strong></td>
                  <td class="segPanel2" align="left" valign="middle" style="border-right:0; padding:5px" colspan="2">
                      {{$sSelectDiscount}}
                      <input class="segInput" id="inp-discount" type="text" size="10" style="text-align:right" />
                      <input class="segButton" type="button" value="Add" onclick="prepareAdd()" />
                      <br />
                      <div style="width:90%; height:160px;overflow-x:hidden; overflow-y:scroll; border:1px solid #4470b1">
                      <table id="discountprices" class="segList compact" border="0" cellpadding="0" cellspacing="0" style="font:normal 10px Arial; width:100%">
                          <thead>
                              <tr>
                                  <th width="80%">Discount type</th>
                                  <th width="*" class="rightAlign">Price</th>
                                  <th width="1">&nbsp;</th>
                              </tr>
                          </thead>
                          <tbody>
                              {{$sDiscounts}}
                          </tbody>
                      </table>
                      </div>
                  </td>
              </tr>
          </tbody>
      </table>
  </div>
  <div id="class" style="padding:2px;padding-top:3px;width:95%;display:none">
      <div id="class_meds" style="display:"> 
      <table border="0" cellspacing="0" cellpadding="2" width="99%" align="center" style="margin-top:5px; color:black">
          <tbody>
              <tr>
                  <td class="segPanel" align="right" valign="middle" width="18%"><strong>Classfication</strong></td>
                  <td class="segPanel2" align="left" valign="middle" style="border-right:0; padding:5px" colspan="2">
                      <table border="0" cellpadding="0" cellspacing="0" style="font:normal 12px Arial">
                          <tr>
                              <td><strong><em>Select classification</em></strong></td>
                              <td>&nbsp;</td>
                              <td><strong><em>Product classification</em></strong></td>
                          </tr>
                          <tr>
                              <td>{{$sSelectClassification}}</td>
                              <td style="padding:0px 2px">
                                  <input type="button" class="segButton" value=">" style="font:bold 11px Arial;padding:0px 1px" onclick="optTransfer.transferRight()" /><br />
                                  <input type="button" class="segButton" value="<" style="font:bold 11px Arial;padding:0px 1px" onclick="optTransfer.transferLeft()" />
                              </td>
                              <td>
                                  {{$sSelectClassification2}}
                                  <br />
                                  <input id="classification" name="classification" type="hidden" value="">
                              </td>
                          </tr>                
                      </table>
                  </td>
              </tr>
          </tbody>
      </table>
      </div>
      <div id="class_equip" style="display:none;"> 
      </div>
      <div id="class_nms" style="display:none;">  
      </div>
      <div id="class_blood" style="display:none;"> 
      </div>
      <br>
      <table border="0" cellspacing="0" cellpadding="2" width="99%" align="center" style="border-collapse:collapse; color:black">
        <tbody>
        <tr>
          <td class="segPanel" align="right" valign="middle"><strong>Small unit of Issue </strong></td>
          <td class="segPanel2" align="left" valign="middle" style="border-right:0">
            {{$sSmallUnit}}
          </td>
          <td class="segPanel2" align="left" valign="middle" style="border-left:0">
            <strong>Select small unit of issue</strong>
          </td>
        </tr>
        <tr>
          <td class="segPanel" align="right" valign="middle"><strong>Big unit of Issue</strong></td>
          <td class="segPanel2" align="left" valign="middle" style="border-right:0">
            {{$sBigUnit}}
          </td>
          <td class="segPanel2" align="left" valign="middle" style="border-left:0">
            <strong>Select big unit of issue</strong>
          </td>
        </tr>
        <tr>
          <td class="segPanel" align="right" valign="middle"><strong>Quantity per packing</strong></td>
          <td class="segPanel2" align="left" valign="middle" style="border-right:0">
            {{$sPerPack}}
          </td>
          <td class="segPanel2" align="left" valign="middle" style="border-left:0">
            <strong>Quantity per packing</strong>
          </td>
        </tr>
        <tr>
          <td class="segPanel" align="right" valign="middle"><strong>Reorder Point</strong></td>
          <td class="segPanel2" align="left" valign="middle" style="border-right:0">
            {{$sMinQty}}
          </td>
          <td class="segPanel2" align="left" valign="middle" style="border-left:0">
            <strong>Reorder Point</strong>
          </td>
        </tr>
        </tbody>
      </table>
  </div>
    
    {{$sHiddenInputs}}
    {{$jsCalendarSetup}}

</div>
<div align="left" style="width:95%;padding:4px; border-top:2px solid #4e8ccf; ">
  <input class="segButton" type="submit" value="Save"/>
  <input class="segButton" type="button" value="Cancel" onclick="parent.cClick()"/>
</div>
{{$sFormEnd}}
{{$sTailScripts}}     
