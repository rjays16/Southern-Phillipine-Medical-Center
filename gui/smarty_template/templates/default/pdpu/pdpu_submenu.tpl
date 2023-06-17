<TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
    <TBODY>
    <TR>
        <TD>
            <TABLE class="submenu_group" cellSpacing=0 cellPadding=0 width=600>
                <TBODY >
                <TR>
                    <TD class="submenu_title" colspan=3>Patient Discharge Planning Unit</TD>
                </tr>
                <!-- Added by Gervie 11/02/2015 -->
                <TR>
                    <TD width="1%">{{$sLabServicesRequestIcon}}</TD>
                    <TD class="submenu_item" width=35%><nobr>{{$LDAssessment}}</nobr></TD>
                    <TD>Encode, view, and print Assessment and Referral Form</TD>
                </TR>
                <TR>
                    <TD width="1%">{{$LDComprehensiveIcon}}</TD>
                    <TD class="submenu_item" width=35%><nobr>{{$LDComprehensive}}</nobr></TD>
                    <TD>Comprehensive patient information</TD>
                </TR>
                {{include file="common/submenu_row_spacer.tpl"}}
                </TBODY>
            </TABLE>
        </TD>
    </TR>
</TABLE>

<br/>
<a href="{{$breakfile}}"><img {{$gifClose2}} alt="{{$LDCloseAlt}}" {{$dhtml}}></a>