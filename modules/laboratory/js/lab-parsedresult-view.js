//restrict to copy, cut and paste the whole document

var curr_table = 0;

function preset(){
  $J(document).ready(function(){
      $J(document).bind("cut copy paste",function(e) {
          e.preventDefault();
      });

      //restrict the right click
      block = setInterval("window.clipboardData.setData('text','')",2);  
      clearInterval(block);
      //restrict the print screen
      window.addEventListener("keyup",kPress,false);
    });
}

function kPress(e){ 
  var c=e.keyCode||e.charCode; 
  if (c==44) alert("print screen");
}

function Ln(){
  $J('#parseresult').append('<br>');
}

function hideLoading(){
  $J("#imgLoading").hide();
}

function header(data){
  $J('#parseresult').append('<hr><div align="center">'
                                   +data['hosp_country']+'<br>'
                                   +data['hosp_agency']+'<br>'
                                   +data['hosp_name']+'<br>'
                                   +data['hosp_addr1']
                           +'</div><br>');
}

function documentDetails(details){
  curr_table++;
  $J('#parseresult').append('<table border="0" cellspacing="2" cellpadding="2" width="99%" align="center">'
                                +'<tr>'
                                    +'<td width="10%">Name</td>'
                                    +'<td width="40%"><b>:'+details['name']+'</b></td>'
                                    +'<td width="10%">Lab no</td>'
                                    +'<td width="40%"><b>:'+details['lab_no']+'</b></td>'
                                +'</tr>'
                                +'<tr>'
                                    +'<td width="10%">Pid</td>'
                                    +'<td width="40%"><b>:'+details['pid']+'</b></td>'
                                    +'<td width="10%">Location</td>'
                                    +'<td width="40%">:<b>'+details['location']+'</b></td>'
                                +'</tr>'
                                +'<tr>'
                                    +'<td colspan="2" width="50%">'
                                        +'<table border="0" width="100%">'
                                            +'<td width="20%">Age</td>'
                                            +'<td width="30%">:<b>'+details['age']+'</b></td>'
                                            +'<td width="20%" align="right">Sex</td>'
                                            +'<td width="30%">:<b>&nbsp;'+details['gender']+'</b></td>'
                                        +'</table>'
                                    +'</td>'
                                    +'<td width="10%">Physician</td>'
                                    +'<td width="40%">:<b>'+details['physician']+'</b></td>'
                                +'</tr>'
                            +'</table>'
                            +'<table width="100%">'
                                +'<tr>'
                                    +'<td colspan="4" width="100%">'
                                        +'<table border="0" width="100%" style="font-size:10pt; font-family:tahoma">'
                                            +'<td width=5%>Date received</td><td width="10%">:<b>'+details['received_dt']+'</b></td>'
                                            +'<td width=5%>Date reported</td><td width="10%">:<b>'+details['reported_dt']+'</b></td>'
                                            +'<td width=5%>Date released</td><td width="10%">:<b>'+details['released_dt']+'</b></td>'
                                        +'</table>'
                                    +'<td>'
                                +'</tr>'
                            +'</table>'
                            +'<table id="tests'+curr_table+'" width="100%">'
                                +'<tr style="border-top:dashed 1px; border-bottom:dashed 1px">'
                                    +'<td align="center" width="33%" style="padding: 5px; border-top: 2px dashed #585E66; border-bottom: 2px dashed #585E66;">&nbsp;&nbsp;&nbsp;<strong>TEST</strong></td>'
                                    +'<td align="center" width="33%" style="padding: 5px; border-top: 2px dashed #585E66; border-bottom: 2px dashed #585E66;">&nbsp;&nbsp;&nbsp;<strong>RESULT</strong></td>'
                                    +'<td align="center" width="33%" style="padding: 5px; border-top: 2px dashed #585E66; border-bottom: 2px dashed #585E66;">&nbsp;&nbsp;&nbsp;<strong>REFERENCE RANGE</strong></td>'
                                +'</tr>'
                            +'</table>');
}

function createGroup(data){
  $J('#tests'+curr_table).append('<tr><td><b>'+data+'<b></td></tr>');
}

function createTestName(data){
  $J('#tests'+curr_table).append('<tr><td><b>'+data+'<b></td></tr>');
}

function createDetails(data){
  var space = '';
  var details = '';

  for(i=0; i<=data.length; i++){
      if(data[i][0]==0){
        space = '&nbsp;&nbsp;&nbsp;&nbsp;';
      }else{
        space = '';
      }

      if (data[i][4]!=''){
          td = '<td width="30%">'+data[i][3]+'</td>'
              +'<td width="50%">'+data[i][4]+'</td>';
      }else{
          td = '<td width="80%">'+data[i][3]+'</td>';           
      } 

     details = '<tr>'
                    +'<td>'+space+data[i][1]+'</td>'
                    +'<td><table border="0" cellspacing="2" cellpadding="2" width="99%" align="center">'
                            +'<tr>'
                                +'<td width="20%">'+data[i][2]+'</td>'
                                +td
                            +'</tr>'
                        +'</table></td>'
                    +'<td>'+data[i][5]+'</td>'
               +'</tr>';

      var note = data[i][6].trim();

      if(note.length > 0) {
          details += '<tr>'
                          +'<td colspan="3">'+space+note+'</td>'
                    +'</tr>';
      }

      $J('#tests'+curr_table).append(details);
  }
}

function createSignatories(medtech,pathologist){

  $J('#parseresult').append('<table border="0" cellspacing="2" cellpadding="2" width="99%" align="center">'
                           +'<tr>'
                                +'<td width="40%" align="center"><strong><u>'+medtech+'</u></strong></td>'
                                +'<td width="20%">&nbsp;</td>'
                                +'<td width="40%" align="center"><strong><u>'+pathologist+'</u></strong></td>'
                           +'</tr>'
                           +'<tr>'
                                +'<td width="40%" align="center">Medical Technologist</td>'
                                +'<td width="20%">&nbsp;</td>'
                                +'<td width="40%" align="center">Pathologist</td>'
                            +'</tr>'
                           +'</table><br>');
}

function debug(data){
  $J('<div></div>')
  .html('<textarea style="width:100%; height:100%">'+data+'</textarea>')
  .dialog({
      width:"98%",
      height:400
  });
}