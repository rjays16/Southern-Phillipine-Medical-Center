<!-- Created by Nick 07-01-2014 -->
<head>
    <style>
        tr {
            text-align: left;
        }
    </style>
    {{foreach from=$javascripts item=script}}
        {{$script}}
    {{/foreach}}
    <script type="text/javascript">var $j = jQuery.noConflict();</script>
    <script>
        $j(function () {
            preset();
            $j('#miscSearchKey').keypress(function(e){
                if(e.which == 13) miscSearch();
            });
            $j('#deptSearchKey').keypress(function(e){
                if(e.which == 13) deptSearch();
            });
        });
    </script>
</head>

<div align="center">
    <span>Search:</span><input id="miscSearchKey" value="" />
    <button id="search" onclick="miscSearch()">Search</button>
    <div id="misc_container" style="width: 80%;">

    </div>
</div>

<div id="deptContainer" style="display: none; margin-top: 5px;">
    <table cellspacing="5px">
        <tr>
            <td>Code</td>
            <td>:</td>
            <td id="selected_code"></td>
        </tr>
        <tr>
            <td>Description</td>
            <td>:</td>
            <td id="selected_description"></td>
        </tr>
        <!--
        <tr>
            <td colspan="3">
                <label><input id="visibleToClinic" type="checkbox"/>&nbsp;&nbsp;Visible to clinic only</label>
            </td>
        </tr>
        -->
    </table>
    <div align="center" height="100%">
        <span>Search:</span><input class="segInput" id="deptSearchKey" value="" />
        <button id="search" onclick="deptSearch()">Search</button>
        <button id="search" onclick="getAddedDepts()">Show added departments</button>
        <div id="dept_container" widtd="100%" style="height: inherit;">

        </div>
    </div>
</div>