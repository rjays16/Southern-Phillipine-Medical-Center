  
function pSearchClose() {
	cClick(); 
}


function reclassRows(list,startIndex) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var dRows = dBody.getElementsByTagName("tr");
			if (dRows) {
				for (i=startIndex;i<dRows.length;i++) {
					dRows[i].className = "wardlistrow"+(i%2+1);
				}
			}
		}
	}
}

function clearDependents(list) {	
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			trayItems = 0;
			dBody.innerHTML = "";
			return true;
		}
	}
	return false;
}

function appendDependents(list,details) {
	console.log(details);
	var allow_searchEmp = $('allow_searchEmp').value;
	var allow_depmanager = $('allow_depmanager').value;
	
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		//alert('dBody = '+dBody.innerHTML);
		
		if (dBody) {
				
			var lastRowNum = null, 
					toolTipText,
					deps = document.getElementsByName('deps[]');
					dRows = dBody.getElementsByTagName("tr");
			
			if (details.id) {
            //if (details) {
				var id = details.id,
					name = details.name,
					sex = details.sex,
					pid = details.pid;
//alert('details = '+details.sex);
				alt = (dRows.length%2)+1;


				// added by rnel 08-16-2016
				// format the data to be feed to tooltip
				var toolTipText = '';
				if(details.date === undefined && details.personnel === undefined && details.taken === undefined) {
					console.log('error');
				} else {
	details.old.forEach(function(item, i) {
						toolTipText += details.old[i] + '<br/>';
					});
					details.date.forEach(function(item, i) {
						if(details.taken[i] == 'activated') {
							details.taken[i] = 'added';
						}
						toolTipText += 'Date/Time: '+item.toUpperCase()+' | Personnel: '+details.personnel[i].toUpperCase()+'  ('+'<b>'+details.taken[i].toUpperCase()+'</b>'+')' + '<br/>';
					});

				}



				if (deps) {
					for (var i=0;i<deps.length;i++) {
						if (deps[i].value == details.id) {
							$('toolTipText'+id).value = toolTipText;
							document.getElementById('relationship'+id).innerHTML = details.relationship.toUpperCase();
							document.getElementById('relation'+id).value = details.relationship.toUpperCase();
							alert('"'+details.name.toUpperCase()+'" is already in the list & has been UPDATED!');
							return true;
						}
					}
					if (deps.length == 0)
	 					clearDependents(list);
				}
			// added by: syboy 03/02/2016 : meow
			if (allow_depmanager == 0 && allow_searchEmp == 1) {
				delitemImg = '';
			}else{
			delitemImg = '<a href="javascript: nd(); deleteDependent(\''+pid+'\''+',\''+id+'\''+');">'+
							'<img src="../../images/btn_delitem.gif" border="0" title="DELETE"/></a>';	

			// Added by: JEFF
			// Date: 08-18-17
			// Purpose: To use img onClick function for updating dependents relation.
			var rel_get;
			rel_get = details.relationship.toUpperCase();
			relation_change = '<img src="../../gui/img/common/default/layout_edit.png" border="0" onClick="relEditPrompt()"/>';
			// End by: JEFF @ 08-18-17

			}
			// ended syboy

			if (sex=='m')
				sexImg = '<img src="../../gui/img/common/default/spm.gif" border="0" />';
			else if (sex=='f')
				sexImg = '<img src="../../gui/img/common/default/spf.gif" border="0" />';
			else
				sexImg = '';

			toolTipTextHandler = ' onMouseOver="return overlib($(\'toolTipText'+id+'\').value, CAPTION,\'Update Details\',  '+
							'  TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, \'oltxt\', CAPTIONFONTCLASS, \'olcap\', '+
							'  WIDTH, 600, FGCLASS,\'olfgjustify\',FGCOLOR, \'#bbddff\');" onmouseout="nd();"';				 
			
			src = 
					'<tr class="wardlistrow'+alt+'" id="row'+id+'"> '+
					'<input type="hidden" name="toolTipText'+id+'" id="toolTipText'+id+'" value="'+toolTipText+'" />'+
					'<input type="hidden" name="dep_id[]" id="depid'+id+'" value="'+details.id+'" />'+
					'<input type="hidden" name="relation[]" id="relation'+id+'" value="'+details.relationship+'" />'+
					'<td width="4%" style="text-align: center;"><input type="hidden" name="deps[]" id="rowID'+id+'" value="'+id+'" />'+delitemImg+
					'</td>'+
					// '<td width="2%" style="text-align: center; cursor: pointer;"><img src="../../gui/img/common/default/layout_edit.png" border="0" onClick=" getRelDetail(\''+details.relationship.toUpperCase()+'\',\''+details.id+'\',\''+pid+'\'); relEditPrompt();" title="Update Relationship"/></td>'+
					'<td width="2%" style="text-align: center; cursor: pointer;"><img src="../../images/cashier_edit_3.gif" border="0" onClick=" getRelDetail(\''+details.relationship.toUpperCase()+'\',\''+details.id+'\',\''+pid+'\'); relEditPrompt();" title="Update Relationship"/></td>'+
					'<td width="10%"><span id="id'+id+'"'+toolTipTextHandler+'>'+details.id+'</span></td>'+
					'<td width="*"><span id="name'+id+'"'+toolTipTextHandler+'>'+details.name.toUpperCase()+'</span></td>'+
					'<td width="15%"><span id="relationship'+id+'"'+toolTipTextHandler+'>'+details.relationship.toUpperCase()+'</span></td>'+
					'<td width="10%"><span id="bdate'+id+'"'+toolTipTextHandler+'>'+details.bdate+'</span></td>'+
					'<td width="10%"><span id="age'+id+'"'+toolTipTextHandler+'>'+details.age+'</span></td>'+
					'<td width="4%"><span id="sex'+id+'"'+toolTipTextHandler+'>'+sexImg+'</span></td>'+
					'<td width="10%"><span id="status'+id+'"'+toolTipTextHandler+'>'+details.status.toUpperCase()+'</span></td>'+
					/*'<td width="5%" align="center" >'+delitemImg+'</td>'+*/
					'</tr>';
				trayItems++;
				
			}else {
				src = "<tr><td colspan=\"11\">Dependent's list is currently empty...</td></tr>";	
				
			}

			dBody.innerHTML += src;
			document.getElementById('counter').innerHTML = deps.length;
			return true;
		}
	}
	return false;
}
// <img src="../../images/cashier_edit_3.gif" border="0">
// <input type="button" id="update_relation" value="Update" style="background-color: #3ff334; color:#000; font-size:10; font-weight: bold;font-style: oblique;"/>
function addDependent(details){
	var data = new Object();
	var parent_pid = $('pid').value;

	data.parent_pid = parent_pid;
	data.dependent_pid = details.id;
	data.relationship = details.relationship;

	xajax_addDependent(data);

}

function deleteDependent(pid, id){
	var conf = confirm('Are you sure to remove this person to this list?');

	if(conf)
		xajax_deleteDependent(pid, id);
}

function removeItem(id) {
	var destTable, destRows;
	var table = $('dep-list');
	var rmvRow=document.getElementById("row"+id);
	if (table && rmvRow) {
		$('id'+id).parentNode.removeChild($('id'+id));
		var rndx = rmvRow.rowIndex-1;
		table.deleteRow(rmvRow.rowIndex);
		reclassRows(table,rndx);
	}
		//burn added : September 13, 2007
	var deps = document.getElementsByName('deps[]');
	if (deps.length == 0){
		emptyIntialRequestList();
	}
	document.getElementById('counter').innerHTML = deps.length;
}

function emptyTray() {
	var parent_pid = $('pid').value;
	var conf = confirm("Are you sure to remove all the dependents of this employee?");

	if(conf)
		xajax_deleteAllDependents(parent_pid);

	/*var deps = document.getElementsByName('deps[]');
	var id, i;
    var details = new Object();
		
	for (i=deps.length-1; i>=0;i--){	
		id = deps[i].value;
		$('row'+id).parentNode.removeChild($('row'+id));
	}
	
	clearDependents($('dep-list'));
	details.id=null; 
    appendDependents($('dep-list'),details);*/
}

function emptyIntialRequestList(){
    var details = new Object(); 
    clearDependents($('dep-list'));
    details.id=null; 
    appendDependents($('dep-list'),details);
}


//modify by rnel
function initialDependentList(objHistory) {
	objInfo = JSON.parse(objHistory);
	console.log(objInfo);
	var details = new Object();

		details.id= objInfo.dependent_pid;
		details.pid = objInfo.pid;
		details.name = objInfo.dependent_name;
		details.relationship = objInfo.relationship;
		details.bdate = objInfo.date_birth;
		details.age = objInfo.age;
		// details.dep_history = dep_history;
		details.date = objInfo.new_date;
		details.personnel = objInfo.action_personnel;
		details.taken = objInfo.action_taken;

		details.status = objInfo.civil_status;
		details.sex = objInfo.sex;

		details.old = objInfo.oldV;
		
		var list = document.getElementById('dep-list');
		
		//alert('details = '+details);
		if (details.id)
			$('mode').value = "update";
		else
			$('mode').value = "save";

		result = appendDependents(list,details);
}
		/*	
				This will trim the string i.e. no whitespaces in the
				beginning and end of a string AND only a single
				whitespace appears in between tokens/words 
				input: object
				output: object (string) value is trimmed
		*/
function trimString(objct){
	objct.value = objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g," "); 
}/* end of function trimString */


function checkRequestForm(){
	var deps = document.getElementsByName('deps[]');
		/*
        if (deps.length==0){
			alert("Please add a dependents first.");
			$('btnAdd').focus();
			return false;	
		}
       */
		$('inputform').submit();
		return true;
	}

function warnClear() {
	if ($('pid').value == "") return true;
	else return confirm('Performing this action will clear the request. Do you wish to continue?');
	
}

function dependentHistory(id){
	
	return overlib('')
}

// function testx(){
// 	alert("x!");
// }

