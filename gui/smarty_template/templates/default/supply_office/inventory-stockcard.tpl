{{$sFormStart}}
    <div style="width:500px">
    {{* NOTE:::  The following table  block must be inside the $sFormStart and $sFormEnd tags !!! *}}

    <!-- <font class="prompt">{{$sDeleteOK}}{{$sSaveFeedBack}}</font> -->
    <font class="warnprompt">{{$sMascotImg}} {{$sDeleteFailed}} {{$LDOrderNrExists}} <br> {{$sNoSave}}</font>
    <table width="100%" border="0" style="font-size:12px; margin-top:5px" cellspacing="0" cellpadding="2">
        <tbody>
            <tr>
                <td align="left" class="jedPanelHeader" colspan="2" width="300"><strong>Stock card options</strong></td>
            </tr>
            <tr>
                <td align="right" width="30%" nowrap="nowrap" align="right" class="jedPanel"> </td>
                <td width="70%" nowrap="nowrap" class="jedPanel"> </td>
            </tr>
            <tr>
                <td colspan=2 align="center" class="jedPanel"><hr width="90%" size="1" /></td>
            </tr>
            <tr id="area_row" style="display:">
                <td align="right" width="30%" nowrap="nowrap" align="right" class="jedPanel"><b>Select Area</b></td>
                <td width="70%" nowrap="nowrap" class="jedPanel">{{$sSCSelectArea}}</td>
            </tr>
            <!--
            <tr id="date_row" style="display:">
                <td align="right" width="30%" nowrap="nowrap" align="right" class="jedPanel"><b>Date</b></td>
                <td width="70%" nowrap="nowrap" class="jedPanel">{{$sSCDateHidden}}{{$sSCDateInput}}{{$sSCDateIcon}}</td>
            </tr>            6
            -->                                                              
            <tr id="expdate_row" style="display:">
                <td align="right" width="30%" nowrap="nowrap" align="right" class="jedPanel"><b>Item</b></td>
                <td width="70%" nowrap="nowrap" class="jedPanel">{{$sSCItemHidden}}{{$sSCItemInput}}{{$sSCItemIcon}}
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center" class="jedPanel">
                    {{$sGenerateButton}}
                </td>
            </tr>
            <tr>
                <td align="right" width="30%" nowrap="nowrap" align="right" class="jedPanel"> </td>
                <td width="70%" nowrap="nowrap" class="jedPanel"> </td>
            </tr>
        </tbody>
    </table>
    
    {{$sHiddenInputs}}
    
{{$jsCalendarSetup}}
{{$sTransactionDetailsControls}}
<br/>
<div style="float:left;">
<table border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="1%">{{$sContinueButton}}</td>
    </tr>
</table>
</div>


</div>
<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}}
