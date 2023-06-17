<div align="left" style="width:100%">
    <table width="100%" border="0" cellspacing="5" cellpadding="0">
        <tr>
            <td width="50%" valign="top">
                <table border="0" cellpadding="2" cellspacing="2" width="100%">
                    <tbody>
                    <tr>
                        <td class="segPanelHeader" colspan="3">Patient Information</td>
                    </tr>
                    <tr>
                        <td class="segPanel" width="1%" nowrap="nowrap"><strong>Patient's Name:</strong></td>
                        <td class="jedPanel3" id="spname" width="50%">{{$sPName}}</td>
                    </tr>
                    <!--tr>
                        <td class="segPanel" width="1%" nowrap="nowrap"><strong>Address</strong></td>
                        <td class="jedPanel3" id="spadd" width="50%">{{$sAddress}}</td>
                    </tr-->
                    <tr>
                        <td class="segPanel" width="1%" nowrap="nowrap"><strong>HRN</strong></td>
                        <td class="jedPanel3" id="spid" width="50%">{{$sPid}}</td>
                    </tr>
                    <tr>
                        <td class="segPanel" width="1%" nowrap="nowrap"><strong>Case No:</strong></td>
                        <td class="jedPanel3" id="spid" width="50%">{{$sCase}}</td>
                    </tr>
                    <tr>
                        <td class="segPanel" width="1%" nowrap="nowrap"><strong>Total Gross Amount:</strong></td>
                        <td class="jedPanel3" id="sgross" width="50%">{{$sGross}}</td>
                    </tr>
                    <tr>
                        <td class="segPanel" width="1%" nowrap="nowrap"><strong>Total Insurance Coverage:</strong></td>
                        <td class="jedPanel3" id="scoverage" width="50%">{{$sCoverage}}</td>
                    </tr>
                    <tr>
                        <td class="segPanel" width="1%" nowrap="nowrap"><strong>Total Discount:</strong></td>
                        <td class="jedPanel3" id="sdiscount" width="50%">{{$sDiscount}}</td>
                    </tr>
                    <tr>
                        <td class="segPanel" width="1%" nowrap="nowrap"><strong>Total Deposit:</strong></td>
                        <td class="jedPanel3" id="sdeposit" width="50%">{{$sDeposit}}</td>
                    </tr>
                    <tr>
                        <td class="segPanel" width="1%" nowrap="nowrap"><strong>Total Net Amount:</strong></td>
                        <td class="jedPanel3" id="snet" width="50%">{{$sNet}}</td>
                    </tr>
                    <tr>
                        <td class="segPanel" width="1%" nowrap="nowrap"><strong>Less Collection Grants:</strong></td>
                        <td class="jedPanel3" id="stotalgrants" width="50%">{{$sLess}}</td>
                    </tr>
                    <tr>
                        <td class="segPanel" width="1%" nowrap="nowrap"><strong>Running Balance:</strong></td>
                        <td class="jedPanel3" id="sbalance" width="50%">{{$sBalance}}</td>
                    </tr>
                    <tr>
                        <td class="segPanelHeader" colspan="3">Collection Grants</td>
                    </tr>
                    <tr>
                        <td id="admitting_diagnosis" class="jedPanel3" colspan="3"></td>
                    </tr>
                    <tr>
                        <table id="collectionsTable">

                            <tbody></tbody>
                        </table>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>


</div>