{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />

{{$sFormStart}}

<style type="text/css">
<!--
    .tabFrame {
        padding:5px;
        min-height:150px;
    }

-->
</style>
<script language="javascript" type="text/javascript">
<!--
    function editProduct(nr) { 
        return overlib(
        OLiframeContent('seg-inventory-product-edit.php?nr='+nr, 670, 400, 'fProduct', 0, 'auto'),
        WIDTH,670, TEXTPADDING,0, BORDER,0, 
                STICKY, SCROLL, CLOSECLICK, MODAL,
                CLOSETEXT, '<img src={{$sRootPath}}/images/close_red.gif border=0 >',
        CAPTIONPADDING,2, 
                CAPTION,'Product Editor',
        MIDX,0, MIDY,0, 
        STATUS,'Product editor');
    }

    function search() {
        plst.fetcherParams = [$('codename').value, $('generic').value, $('prodclass').value];
        plst.reload();
    }

    function tabClick(listID, index) {
        var dList = $(listID);
        if (dList) {
            var listItems = dList.getElementsByTagName("LI");
            if (listItems[index]) {
                for (var i=0;i<listItems.length;i++) {
                    if (i!=index) {
                        listItems[i].className = "";
                        if ($("tab"+i)) $("tab"+i).style.display = "none";
                    }
                }
                if ($("tab"+index)) $("tab"+index).style.display = "block";
                listItems[index].className = "segActiveTab";
            }
        }
    }
    
    function toggleTBody(list) {
        var dTable = $(list);
        if (dTable) {
            var dBody = dTable.getElementsByTagName("TBODY")[0];
            if (dBody) dBody.style.display = (dBody.style.display=="none") ? "" : "none";
        }
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
-->
</script>

<div style="width:100%">
    <table border="0" cellspacing="0" cellpadding="2" width="60%" align="center" style="">
        <tbody>
            <tr>
                <td class="segPanelHeader">
                    Search pharmacy product
                </td>
            </tr>
      <tr>
        <td class="segPanel" style="padding:2px">
          <table border="0" cellspacing="0" cellpadding="2" width="100%" align="center" style="font:normal 12px Arial">
                  <tr><td colspan="3" style="height:10px"></td><tr>
                  <tr>
                      <td align="right" valign="middle" width="20%"><strong>Code/Name</strong></td>
                      <td align="left" valign="middle" width="30%" style="">
                          {{$sCodeName}}
                      </td>
                      <td align="left" valign="middle" width="*" style="">
                          <strong>Search products by code or name</strong>
                      </td>
                  </tr>
                  <tr>
                      <td align="right" valign="middle"><strong>Generic name</strong></td>
                      <td align="left" valign="middle" style="border-right:0">
                          {{$sGenericName}}
                      </td>
                      <td align="left" valign="middle" style="border-left:0">
                          <strong>Search products by generic name</strong>
                      </td>
                  </tr>
                  <!--
                  <tr>
                      <td align="right" valign="middle"><strong>Classfication</strong></td>
                      <td align="left" valign="middle" style="border-right:0">
                          {{$sSelectClassification}}
                      </td>
                      <td align="left" valign="middle" style="border-left:0">
                          <strong>Search by product category</strong>
                      </td>
                  </tr>
                  -->
                  <tr>
                      <td align="right" valign="middle"><strong>Type</strong></td>
                      <td align="left" valign="middle" style="border-right:0">
                          {{$sProdClass}}
                      </td>
                      <td align="left" valign="middle" style="border-left:0">
                          <strong>Filter products by type</strong>
                      </td>
                  </tr>
                  <tr><td></td><td colspan="2" style="height:2px"><input class="segButton" type="button" value="Search" onclick="search()"/></td><tr>
          </table>
      </tr>
        </tbody>
    </table>
    <br />

    <div align="left" style="width:85%">
        <div style="padding:2px 0px">
            {{$sCreateProduct}}{{$sCreateClassification}}<input class="segButton" type="button" value="Refresh!" onclick="plst.reload()" />
        </div>
        {{$sProductList}}

        <div style="margin-top:2px">
            <span style="font:bold 11px Arial">Legend:</span>
            <span style="margin-left:5px; color:#000066">
                Medicine
                <img src="{{$sRootPath}}gui/img/common/default/pharma_meds.png" align="absmiddle" />
            </span>
            <span style="margin-left:5px; color:#006600">
                Supplies
                <img src="{{$sRootPath}}gui/img/common/default/pharma_supplies.png" align="absmiddle" />
            </span>
            <span style="margin-left:5px; color:#000066">
                Non-Med Supplies
                <img src="{{$sRootPath}}gui/img/common/default/pharma_nonmeds.png" align="absmiddle" />
            </span>
            <span style="margin-left:5px; color:#000066">
                Equipment
                <img src="{{$sRootPath}}gui/img/common/default/pharma_equip.png" align="absmiddle" />
            </span>
            <span style="margin-left:5px; color:#000066">
                Blood
                <img src="{{$sRootPath}}gui/img/common/default/pharma_blood.png" align="absmiddle" />
            </span>
            <span style="margin-left:5px; color:#000066">
                Housekeeping
                <img src="{{$sRootPath}}gui/img/common/default/pharma_housekeeping.png" align="absmiddle" />
            </span>
        </div>
    </div>


{{$sHiddenInputs}}
{{$jsCalendarSetup}}
</div>
{{$sFormEnd}}
{{$sTailScripts}}     
