<style type="text/css">
    .searchBlock {
        background-color: #e5e5e5;
        color: #2d2d2d;
        text-align:left;
        padding: 10px 10px 10px 10px;
        margin: 5px 0px 5px 0px;
    }

    .searchLabel {
        font: bold 12px Arial;
    }

</style>

<script type="text/javascript">
    window.onload = function() {
    document.getElementById('search').focus();
    searchBills();
}
</script>

{{$sFormStart}}
<div id="mainContent" style="width:98%">
    <div class="searchBlock">
        <label class = "searchLabel" for = "search">Search Bill No: </label>
        <input id="search" class = "segInput searchLabel" type = "text" name="search">
        <button class="segButton" id="searchBtn" onclick="searchBills()"><img src="../../gui/img/common/default/magnifier.png" />Search</button>
    </div>
    <div>
        {{$lstRequest}}
    </div>
</div>
<div id="hidden-inputs" style="display:none">
    <input type="hidden" id="pid" name="pid" value={{$pid}}>
</div>
{{$jsCalendarSetup}}
{{$sFormEnd}}
{{$sTailScripts}}