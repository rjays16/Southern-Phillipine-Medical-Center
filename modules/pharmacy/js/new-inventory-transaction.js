/*author MLJ  date creadted 0016-9-29?*/
	
		$(document).ready(function(){
			if (!canCreate) 
 			$('#ADD').hide();
			if (!canUpdate) 
 			$('#MODIFY').hide();
		    if (!canDelete) 
 			$('#DELETE').hide();
		});
			
  

		 $("button").click(function(e){
		 	var area_code =$('#area_code').val();
			var area_name =$('#area_name').val();
			var allow_socialized = $("#allow_socialized").is(':checked') ? 1 : 0;
			var show_area = $("#show_area").is(':checked') ? 1 : 0;
			var lockflag = $("#lockflag").is(':checked') ?  1 : 0;
			var inv_area_code =$('#inv_area_code').val();
			var inv_api_key =$('#inv_api_key').val();
		    var idClicked = e.target.id;
		    if (area_code !="" && area_name !="" && inv_area_code!="" && inv_api_key !="") {
		     if(idClicked =="ADD")
		        xajax_insertPharmaArea('INSERT INTO',area_code,area_name,allow_socialized,lockflag,show_area,inv_area_code,inv_api_key);
		     if(idClicked =="MODIFY")
		       xajax_insertPharmaArea('UPDATE',area_code,area_name,allow_socialized,lockflag,show_area,inv_area_code,inv_api_key);
		     if(idClicked =="DELETE")
		        xajax_insertPharmaArea('DELETE',area_code,area_name,allow_socialized,lockflag,show_area,inv_area_code,inv_api_key);
		     if(idClicked =="UNDO")
		        xajax_insertPharmaArea('UNDO',area_code,area_name,allow_socialized,lockflag,show_area,inv_area_code,inv_api_key);
		   }else{
		   	
		   	 $('#msgInfoError').html("An error occured while proccessing your request:<br>* field required <br>Check corresponding fields.");
		   	 ErrorMsg();
		   }
		    
		});
		 function afterSave(message){
		 
		 	if (message === "Deleted") {
		 	  var area_code =$('#area_code').val();
		 		$('#msgInfo').html("Area: "+area_code+" Successfully Deactivated.");
		 		$('#hiddenActions').val('2');
		 		MessInfo(area_code,"Deleted");
		 	}
			else if (message === "ERROR"){
				  var area_code =$('#area_code').val();
			    $('#msgInfoError').html("An error occured while proccessing your request:<br> -Inventory area <b>'"+area_code+"'</b> already exist.<br> -Check corresponding fields.");
				ErrorMsg();
			}
			else if (message === "ERRORARCODE") {
				  var inv_area_code =$('#inv_area_code').val();
			    $('#msgInfoError').html("An error occured while proccessing your request:<br>-Inventory Area Code: must be unique <b>'"+inv_area_code+"'</b> already exist.<br> -Check corresponding fields.");
				ErrorMsg();
			}
			else if (message == "ERRORAPICODE") {
				 var inv_api_key =$('#inv_api_key').val();
			    $('#msgInfoError').html("An error occured while proccessing your request:<br>-API  Key: must be unique <b>'"+inv_api_key+"'</b> already exist.<br> -Check corresponding fields.");
				ErrorMsg();
			}			 	
		 	else{
			 	var area_code =$('#area_code').val();
				MessInfo(area_code,"");
				if (message !="Deleted") {
					$('#hiddenActions').val('2');
				$('#msgInfo').html(message);
					setTimeout(function(){
					    window.location.href = "seg-inventory-edit.php?area_code="+area_code+"&isDeleted=2";
						
					},2000);
				}
 	
		 	}
							         		
		 }
		     function MessInfo(area_code,actionS){
    	    var ErrorConnection = $('#system-message')
                    .dialog({
                    	dialogClass: 'transparent-dialog',
                        autoOpen: true,
                        modal: true,
                        height: "auto",
                        width: "50%",
                        show: 'bounce',
                        hide: 'explode',
                        resizable: false,
                        draggable: true,
                        title: ' ',
                        position: "center",
                         open: function(event, ui) {
            				$(".transparent-dialog").css({background: "transparent",border:"none"});	
			            }
					
                    });
                     	 $(".ui-dialog-titlebar").hide();
                             $('#closeDD').click(function() {
							     if (actionS == "Deleted") {
							     	$('#system-message').dialog("close");
							     	 window.parent.jQuery('#InventoryAreaDialog').dialog('close');
			 		 			     window.parent.location.reload(true);
							     }else{
							     	$('#system-message').dialog("close");
							     	 window.location.href = "seg-inventory-edit.php?area_code="+area_code+"&isDeleted=2"; 
						     	}	
						     
						}); 

		    }

		         function ErrorMsg(){
    	    var ErrorMsgDialog = $('#error-message')
                    .dialog({
                    	dialogClass: 'transparent-dialog',
                        autoOpen: true,
                        modal: true,
                        height: "auto",
                        width: "50%",
                        show: 'bounce',
                        hide: 'explode',
                        resizable: false,
                        draggable: true,
                        title: 'Error Connection ',
                        position: "center",
                         open: function(event, ui) {
            				$(".transparent-dialog").css({background: "transparent",border:"none"});	
			            }
					
                    });
                     	 $(".ui-dialog-titlebar").hide();
                             $('#closeError').click(function() {
							     	$('#error-message').dialog("close");
			 		 			     
						}); 

		    }
