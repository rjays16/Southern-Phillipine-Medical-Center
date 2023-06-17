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
    .btn-submit {
        float:right;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        font-weight: normal;
        margin:0;
        outline: 0;
    }

</style>

<script type="text/javascript">
    window.onload = function() {
    lingapBills.reload();
}
</script>

<form enctype = 'multipart/form-data' action="{{$formAction}}" method = 'POST' id='dialysisLingapForm'>
    <div id="mainContent" style="width:98%">
        <div class = 'searchBlock'>
            <span class = 'searchLabel' >Classification: {{$classification}} </span>
            <input class = 'btn-submit' type = 'submit' {{$disableApply}} value='Apply Discount'>
        </div>
        <div>
            {{$lstRequest}}
        </div>
    </div>
    <div id="hidden-inputs" style="display:none">
        <input type="hidden" id="pid" name="pid" value={{$pid}}>
    </div>
    {{$jsCalendarSetup}}
</form>
{{$sTailScripts}}