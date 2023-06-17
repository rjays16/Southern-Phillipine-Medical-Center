var data='';
$(document).ready(function(){

 NotesDate();
     });
 function Again(){
 NotesDate();
 }
  var dietorder_list = [];
  var update_dietlist = false;
  function NotesDate(){
  
  $j('#NotesDate').val(toDate(new Date(), "yyyy-mm-dd") );
 
  $j('#pNotes_display').datetimepicker({
         dateFormat: 'M d, yy',
         timeFormat: 'hh:mm tt',
         onSelect: function (selectedDate) {
   
            $j('#NotesDate').val(toDate(new Date(selectedDate), "yyyy-mm-dd") );    

         },
         onClose: function (selectedDate) {  
      document.getElementById('NotesDate').innerHTML = toDate(new Date(selectedDate), "mm-dd-yyyy ") ;  

        
         },
     });
 
 
 }
  function toDate(epoch, format, locale) {
     var date = new Date(epoch),
         format = format || 'mm/dd/YY',
         locale = locale || 'en'
         dow = {};
 
     dow.en = [
         'Sunday',
         'Monday',
         'Tuesday',
         'Wednesday',
         'Thursday',
         'Friday',
         'Saturday'
     ];
 
     var formatted = format
         .replace('D', dow[locale][date.getDay()])
         .replace('dd', ("0" + date.getDate()).slice(-2))
         .replace('mm', ("0" + (date.getMonth() + 1)).slice(-2))
         .replace('yyyy', date.getFullYear())
         .replace('yy', (''+date.getFullYear()).slice(-2))
         .replace('hh', ("0" + date.getHours()).slice(-2))
         .replace('mn', ("0" + date.getMinutes()).slice(-2));
 
     return formatted;
 }

    function AddFn(){
        var diet_code = $j('#nDiet :selected').val();
        var diet_name = $j('#nDiet :selected').text();
        var already_exists=false;
        var select_diet = true;
        var allDiet = $j('[name="diet[]"]');
        $j.each(allDiet, function (index, value) {
                if(value.value==diet_code){
                    already_exists=true;
                }
                if(diet_code==0){
                    select_diet = false;
                }
        });
            if(diet_code==0){
                select_diet = false;
            }
        if(select_diet){
            if(already_exists){
                alert("Diet already exists!");
            }
            else{
                var markup = '<tr id=row'+diet_code+'>'+
                            '<td></td><td><input type="hidden" name="diet[]" id="code" value='+diet_code+'>'+
                            '<button type="button"  class="removebutton" title="Remove this row" style="width:10px; background-color: Transparent; border: none;"><img src="../../images/close_small.gif" style="margin-left:-5px;"></button><label style="font: 14px Arial; margin-left: 15px;">'+diet_name+'</label></td>'+
                        '</tr>';
                $j("#table_notes #tb_notes").append(markup);
                dietorder_list.push(diet_code);
                update_dietlist = true;
            }
        }

     }
     function cutOff(){
        $j('#withincutoff').val(true);
        // alert("Please contact Dietary to update Patient's Diet.");
       
     }

    $(document).on('click','button.removebutton', function() {
    var allDiet = $j('[name="diet[]"]');
    var diet = $j(this).siblings('[name="diet[]"]');
    var index = allDiet.index(diet.get(0));
    dietorder_list.splice(index,1);
    $(this).closest('tr').remove();
    update_dietlist = true;
    return false;
    });

     $(document).on('click','button.disablebtn', function() {
         var allDiet = $j('[name="diet[]"]');
    var diet = $j(this).siblings('[name="diet[]"]');
    var index = allDiet.index(diet.get(0));
    dietorder_list.splice(index,1);
    $(this).closest('tr').remove();
    update_dietlist = true;
     $j('#withincutoff').val(true);
    return false;
    // cutOff();
    });

    function cutOffAdding(){
        $j('#withincutoff').val(true);
        // alert("Please contact Dietary to update Patient's Diet.");
       AddFn();
     }
     
     function validationForm(val) {
        var height = $j('#height');
        var weight = $j('#weight');
        var encounterno_bmi = $j('#encounterno_bmi');

        var allDiet = $j('[name="diet[]"]');
        var cutofftime = '';
        var time = ("0" + new Date().getHours()).slice(-2)+":"+("0" + new Date().getMinutes()).slice(-2);
        /*if(height.val().trim()==''  ||  height.val() == '0.00'){
                alert("Please input height.");
            height.focus();
            return false;
        }
        if(weight.val().trim()==''  ||  weight.val() == '0.00'){
             alert("Please input weight.");
             weight.focus();
             return false;
         }*/

        if(encounterno_bmi.val().trim()=='')  {
            alert('The height and weight are not yet encoded.');
            return false;
        }


        if(allDiet.length == 0){
            alert("Please input diet.");
            return false;
        }



         if( time >= "05:01" && time <= "10:30"){
             cutofftime = 'Lunch';
         }else if(time >= "10:31" && time <= "15:30"){
             cutofftime = 'Dinner';
         }else{
             cutofftime = 'Breakfast';
         }
         if(update_dietlist){
             alert ("This diet is for "+cutofftime+".");
         }



         // alert(document.getElementsByName("Submit").value);

     }

      
//End 