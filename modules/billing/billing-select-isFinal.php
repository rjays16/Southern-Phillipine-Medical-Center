<?php require('./roots.php'); ?>
<!--
This is the view for popup `Select Registered Patient`
@author michelle 03-03-15
-->

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css"/>
<link rel="stylesheet" href="<?= $root_path ?>css/table.css" type="text/css"/>

<!-- prototype -->
<script type="text/javascript" src="<?= $root_path ?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/fat/fat.js"></script>
<script type="text/javascript" src="<?= $root_path ?>/js/shortcut.js"></script>

<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins: -->
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_modal.js"></script>



<script type='text/javascript' src="<?= $root_path ?>js/jquery/jquery-1.8.2.js"></script>
<script type='text/javascript' src="<?= $root_path ?>js/jquery/ui/jquery-ui-1.9.1.js"></script>

<!--<script type="text/javascript" src="--><?//= $root_path ?><!--modules/billing/js/billing-collection.js"></script>-->
<!--<script type="text/javascript" language="javascript" src="--><?//= $root_path ?><!--js/jquery.datatables/resources/demo.js"></script>-->

<script type="text/javascript">
        
    /**
     * This will render patient information.
     * template is on seg_credit_collection.tpl
     * helper seg_credit_collection.php
     * @param data
     */
    function displayPatientInfo(data)
    {
        var d = $(data);
        window.parent.$('hrnInput').value = d.data('pid');
        window.parent.$('pNameInput').value = d.data('name');
        window.parent.$('pAddressInput').value = d.data('address');
        window.parent.$('pEncrNoInput').value = d.data('encounter');
        window.parent.$('pBillNrInput').value = d.data('billnr');
        window.parent.$('pBillDateInput').value = d.data('billdte');
        window.parent.$('pBillFrmDateInput').value = d.data('billfrmdte');
        window.parent.$('pInsuranceInput').value = d.data('insurancenr');
        window.parent.closeSelEncCollectionDialog();

       // window.parent.$('collectionDialog').style.display = "";
    }

    /**
     * Searching functionality. search by patient name, pid and
     * case no.
     * @author michelle 03-11-15
     */
    function search()
    {
        var searchPName = $('#search').val();
        var searchCase = $('#searchCase').val();

        var params = '';
        var url = '';

        if (searchPName != '') {
            params = {q: searchPName};
            url = '../../index.php?r=collections/index/search';
        } else {
            params = {q: searchCase};
            url = '../../index.php?r=collections/index/searchByEncounter';
        }

        var res = '';
        var row = '';
        var header = '<tr>' +
            '<td>HRN</td>' +
            '<td>Sex</td>' +
            '<td>Patient\'s Name</td>' +
            '<td>Confinement</td>' +
            '<td>PHIC?</td>' +
            '<td>Confinement Type</td>' +
            '<td>Case No.</td>' +
            '<td></td>' +
            '</tr>';

        $.ajax({
           url: url,
           type: 'GET',
           data: params,
           dataType: 'json',
           success: function (data) {
                res = data;
           },
           complete: function (item) {
               $('#collectionSearch tbody').empty();
               $('#collectionSearch tbody').append(header);

               if (res.persons.length > 0) {
                   $.each(res.persons, function(i,v) {
                       row = '<tr>' +
                           '<td align="center">' + res.persons[i].hrn + '</td>' +
                           '<td align="center">' + res.persons[i].sex + '</td>' +
                           '<td align="center">' + res.persons[i].name + '</td>' +
                           '<td align="center">' + res.persons[i].confinement + '</td>' +
                           '<td align="center">' + res.persons[i].phic + '</td>' +
                           '<td align="center">' + res.persons[i].type + '</td>' +
                           '<td align="center">' + res.persons[i].caseNo  + '</td>' +
                           '<td align="center">' + res.persons[i].select + '</td>' +
                           '</tr>';
                       $('#collectionSearch tbody').append(row);
                   });
               } else {
                    row = '<tr>' +
                       '<td></td>' +
                       '<td></td>' +
                       '<td></td>' +
                       '<td>No results found.</td>' +
                       '<td></td>' +
                       '<td></td>' +
                       '<td></td>' +
                       '<td></td>' +
                   '</tr>';
                   $('#collectionSearch tbody').append(row);
               }
           },
           error: function (e) {
               //Search query is not in the recommended format
               $('#collectionSearch tbody').empty();
               $('#collectionSearch tbody').append(header);
               row = '<tr>' +
                   '<td></td>' +
                   '<td></td>' +
                   '<td></td>' +
                   '<td>Search query may not be in the recommended format.</td>' +
                   '<td></td>' +
                   '<td></td>' +
                   '<td></td>' +
                   '<td></td>' +
                   '</tr>';

               $('#collectionSearch tbody').append(row);
               e.preventDefault();
           }
        });
    }

</script>

<div style="display: inline; margin-bottom: 3px; margin-top: 5px">
   <label style="font: 12px Arial">Search Patient Name / HRN:</label> <input id="search" type="text" onchange="search()" value="" size="40" />
   <label style="font: 12px Arial">Case No:</label> <input id="searchCase" type="text" value="" onchange="search()"/>
</div>
<table id="collectionSearch" class="CSSTableGenerator" width="98%" cellspacing="2" cellpadding="2">
    <tbody>
        <tr>
            <td>HRN</td>
            <td>Sex</td>
            <td>Patient's Name</td>
            <td>Confinement</td>
            <td>PHIC?</td>
            <td>Confinement Type</td>
            <td>Case No.</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td>No data available.</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>

<input type="hidden" value="" id="hidPid" />