<?php /* Smarty version 2.6.0, created on 2020-02-05 12:14:10
         compiled from radiology/radio-html-unified.tpl */ ?>
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
                        <span><i><?php echo $this->_tpl_vars['hosp_country']; ?>
</i></span><br>
                        <span><i><?php echo $this->_tpl_vars['hosp_agency']; ?>
</i></span><br>
                        <span><?php echo $this->_tpl_vars['hosp_name']; ?>
</span><br>
                        <span><?php echo $this->_tpl_vars['hosp_addr1']; ?>
</span><br>
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
                                    <td>Patient:&nbsp;<?php echo $this->_tpl_vars['patient_name']; ?>
</td>
                                </tr>
                                <tr>
                                    <td>Address:&nbsp;<?php echo $this->_tpl_vars['address']; ?>
</td>
                                </tr>
                                <tr>
                                    <td>
                                        Gender:&nbsp;<?php echo $this->_tpl_vars['gender']; ?>
&nbsp;&nbsp;
                                        Birthdate:&nbsp;<?php echo $this->_tpl_vars['birth_date']; ?>
&nbsp;&nbsp;
                                        Age:&nbsp;<?php echo $this->_tpl_vars['age']; ?>

                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td>HRN:&nbsp;<?php echo $this->_tpl_vars['hrn']; ?>
</td>
                                </tr>
                                <tr>
                                    <td>RID:&nbsp;<?php echo $this->_tpl_vars['rid']; ?>
</td>
                                </tr>
                                <tr>
                                    <td>BN:&nbsp;<?php echo $this->_tpl_vars['batch_no']; ?>
</td>
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
                                    <td>Requesting Doctor:&nbsp;<?php echo $this->_tpl_vars['requesting_doctor']; ?>
</td>
                                </tr>
                                <tr>
                                    <td>Clinical Indication/Impression:&nbsp;<?php echo $this->_tpl_vars['clinical_impression']; ?>
</td>
                                </tr>
                                <tr>
                                    <td>Date/Time of Examination:&nbsp;<?php echo $this->_tpl_vars['date_examination']; ?>
</td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td>Exam Taken:&nbsp;<?php echo $this->_tpl_vars['exam']; ?>
</td>
                                </tr>
                                <tr>
                                    <td>Dept:&nbsp;<?php echo $this->_tpl_vars['department']; ?>
</td>
                                </tr>
                                <tr>
                                    <td>Area:&nbsp;<?php echo $this->_tpl_vars['area']; ?>
</td>
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
                        <td align="center"><?php echo $this->_tpl_vars['docu_title']; ?>
</td>
                    </tr>
                    <tr>
                        <td align="center"><?php echo $this->_tpl_vars['status']; ?>
</td>
                    </tr>
                    <tr>
                        <td align="center"><?php echo $this->_tpl_vars['note']; ?>
</td>
                    </tr>
                </table>
            </div>

            <div>
                <table>
                    <?php echo $this->_tpl_vars['findings_info']; ?>

                </table>
            </div>
            <div id="footer">
                <div id="encoder_info">
                    <table>
                        <tr>
                            <td>Served by:&nbsp;<?php echo $this->_tpl_vars['served_by']; ?>
</td>
                        </tr>
                        <tr>
                            <td>Result Encoded by:&nbsp;<?php echo $this->_tpl_vars['encoded_by']; ?>
</td>
                        </tr>
                        <tr>
                            <td>Date Encoded<?php echo $this->_tpl_vars['foot_result']; ?>
:&nbsp;<?php echo $this->_tpl_vars['date_encoded']; ?>
</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>