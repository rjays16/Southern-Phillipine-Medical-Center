<html>
<!--
Created by Nick 06-18-2014
-->
<style>
    body {
        padding: 10px;
        font-family: tahoma;
        /* disable selecting texts */
        user-select: none;
        -o-user-select:none;
        -moz-user-select: none;
        -khtml-user-select: none;
        -webkit-user-select: none;
        margin:0px;
        padding:0px;
    }

    #container {
        overflow: auto;
        height: 100%;
    }

    #wrapper {
        width: 90%;
        margin-left: 5%;
    }

    #header tr td {
        text-align: center;
    }

    img {
        width: 50%;
    }

    .panel {
        border: solid 1px #000000;
    }

    #encoder_info {
        float: right;
    }
</style>
<head>
    <script type="text/javascript" src="../../js/shortcut.js"></script>
    <script type='text/javascript' src="../../js/jquery/jquery-1.8.2.js"></script>
    <script language="javascript">
        //added by Nick - show data on cancel print
        function afterPrint(){
            $("#container").show();
        }
        //added by Nick - hide data on print
        function beforePrint(){
            $("#container").hide();
        }
        //added by Nick - detect print action
        $(function(){
            if ('matchMedia' in window) {
                window.matchMedia('print').addListener(function(media) {
                    if (media.matches) {
                        beforePrint();
                    } else {
                        $(document).one('mouseover', afterPrint);
                    }
                });
            } else {
                $(window).on('beforeprint', beforePrint);
                $(window).on('afterprint', afterPrint);
            }
        });
        //disable mouse right-click
        document.onmousedown= function disableclick(event){
            if(event.button==2){
                return false;
            }
        }
    </script>
</head>
<body oncontextmenu="return false;">
<div id="container">
    <div id="wrapper">
        <div>
            <table id="header" width="100%">
                <tr>
                    <td width="20%"><img src="../../gui/img/logos/dmc_logo.jpg"/></td>
                    <td width="60%">
                        <span><i>{{$hosp_country}}</i></span><br>
                        <span><i>{{$hosp_agency}}</i></span><br>
                        <span>{{$hosp_name}}</span><br>
                        <span>{{$hosp_addr1}}</span><br>
                        <span>Department of Radiological & Imaging Sciences</span>
                    </td>
                    <td width="20%"><img src="../../modules/radiology/images/rad_logo.jpg"/></td>
                </tr>
            </table>
            <div class="panel">
                <table id="patient_info" width="100%">
                    <thead>
                    <tr>
                        <th width="60%"></th>
                        <th width="30%"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <table>
                                <tr>
                                    <td>Patient:&nbsp;{{$patient_name}}</td>
                                </tr>
                                <tr>
                                    <td>Address:&nbsp;{{$address}}</td>
                                </tr>
                                <tr>
                                    <td>
                                        Gender:&nbsp;{{$gender}}&nbsp;&nbsp;
                                        Birthdate:&nbsp;{{$birth_date}}&nbsp;&nbsp;
                                        Age:&nbsp;{{$age}}
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td>HRN:&nbsp;{{$hrn}}</td>
                                </tr>
                                <tr>
                                    <td>RID:&nbsp;{{$rid}}</td>
                                </tr>
                                <tr>
                                    <td>BN:&nbsp;{{$batch_no}}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <table id="patient_info" width="100%">
                    <thead>
                    <tr>
                        <th width="60%"></th>
                        <th width="30%"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <table>
                                <tr>
                                    <td>Requesting Doctor:&nbsp;{{$requesting_doctor}}</td>
                                </tr>
                                <tr>
                                    <td>Clinical Indication/Impression:&nbsp;{{$clinical_impression}}</td>
                                </tr>
                                <tr>
                                    <td>Date/Time of Examination:&nbsp;{{$date_examination}}</td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td>Exam Taken:&nbsp;{{$exam}}</td>
                                </tr>
                                <tr>
                                    <td>Dept:&nbsp;{{$department}}</td>
                                </tr>
                                <tr>
                                    <td>Area:&nbsp;{{$area}}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <table width="100%">
                    <tr>
                        <td align="center">{{$docu_title}}</td>
                    </tr>
                    <tr>
                        <td align="center">{{$status}}</td>
                    </tr>
                    <tr>
                        <td align="center">{{$note}}</td>
                    </tr>
                </table>
            </div>

            <div>
                <table>
                    {{$findings_info}}
                </table>
            </div>
            <div id="footer">
                <div id="encoder_info">
                    <table>
                        <tr>
                            <td>Served by:&nbsp;{{$served_by}}</td>
                        </tr>
                        <tr>
                            <td>Result Encoded by:&nbsp;{{$encoded_by}}</td>
                        </tr>
                        <tr>
                            <td>Date Encoded{{$foot_result}}:&nbsp;{{$date_encoded}}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>