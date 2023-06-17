
function preset() {
	if($('#is_other').is(':checked')) {
		affiantsPurpose();
	}
	$('.alert').delay(3000).fadeOut(1000);
}

function submitForm() {	
	
}

function processForm() {

	var i, empty = 0, affirmative, fields = new Array();

	affirmative = window.confirm('Process this transaction?');
	// Commented by JEFF 07-15-17 for fields that aren't needed as request by the user
	fields = [
		$('#affiant_fname'),
		$('#affiant_lname'),
		$('#affiant_mname'),
		$('#affiant_citizenship'),
		$('#affiant_status'),
		$('#affiant_age'),
		$('#affiant_address_barangay'),
		$('#affiant_address_city'),
		// $('#affiant_address_prob'),
		// $('#affiant_address_country'),
		// $('#father_surename'),
		$('#child_birth_date'),
		$('#child_birth_mun_cty'),
		// $('#child_birth_pro'),
		$('#child_birth_country')
		// $('#city_mun_lcro_cert'),
		// $('#province_lcro_cert'),
		// $('#country_lcro_cert'),
		// $('#administer_personell'),
		// $('#place_ausf_cert')
	];

	if(affirmative == true){
		if ($('#affiant_fname').val() == '') {
			window.alert('Affiant first name is needed!');
			$('#affiant_fname').focus();
			return false;
		}

		if ($('#affiant_mname').val() == '') {
			window.alert('Affiant middle name is needed!');
			$('#affiant_mname').focus();
			return false;
		}

		if ($('#affiant_lname').val() == '') {
			window.alert('Affiant last name is needed!');
			$('#affiant_lname').focus();
			return false;
		}

		if ($('#affiant_citizenship').val() == '') {
			window.alert('Citizenship is empty!');
			$('#affiant_citizenship').focus();
			return false;
		}

		if ($('#affiant_age').val() == 0) {
			window.alert('affiant age is invalid!');
			$('.aff_age').css('color', '#e24212');
			$('#affiant_age').focus();
			return false;
		}

		if ($('#affiant_address_basic').val() == '') {
			window.alert('Incomplete Address!');
			$('#affiant_address_basic').focus();
			return false;
		}

		if ($('#affiant_address_barangay').val() == '') { 
			window.alert('Incomplete Address!');
			$('#affiant_address_barangay').focus();
			return false;
		}

		if ($('#affiant_address_city').val() == '') {
			window.alert('Incomplete Address!');
			$('#affiant_address_city').focus();
			return false;
		}

		if ($('#paternity_reg_num').val() == '') {
			window.alert('ID Number is needed!');
			$('#paternity_reg_num').focus();
			return false;
		}

		if ($('#administer_place').val() == '') {
			window.alert('Exhibiting City is needed!');
			$('#administer_place').focus();
			return false;
		}

		if(!$('#is_self').is(':checked') && !$('#is_other').is(':checked')) {
			window.alert('Please check atleast one of the radio button purposes!');
			$('.checkbox').css('color', '#e24212');
			$('#is_self').focus();

			return false;
		}
	}

	if(affirmative == true) {
		fields.forEach( function(field, index) {
			if(fields[index].val() == null || fields[index].val() == ' ') {
				console.log(fields[index]);
				fields[index].css('borderColor', '#e24212');
				// fields[index].focus();
				empty++;
			} else {
				fields[index].css('borderColor', '#ccc');
			}
		});

		if(empty == 0) {
			return true;
		} else {
			window.alert('You have '+empty+' field/s left empty!');
			return false;
		}
	} else {
		return false;
	}
}

var chck = false;
function affiantsPurpose(pid) {
	xajax_getChildFullname(pid);
	
	if($('#is_other').is(':checked')) {
		$('#other').css('display', 'block');
		chck = true;
	} else {
		$('#other').css('display', 'none');
		// $('#child_fullname').val(' ');
		// $('#affiant_fname').val(' ');
		// $('#affiant_lname').val(' ');
		// $('#affiant_mname').val(' ');
		// $('#affiant_age').val(' ');
	}

	if($('#is_self').is(':not(:checked)')&&chck==false) {

	// } else {
	
	}

	$('.checkbox').css('color', '#333');
}


function placeFillingAUSF() {
	var place = $('#city_mun_lcro_cert :selected').text();
	$('#place_ausf_cert').val(place);
}


function personInfo(obj) {
	if($('#is_other').is(':checked') && $('#is_self').is(':checked')) {
		window.alert('Please Select Only One Option!');
		$('#is_other').attr('checked', false);
		$('#is_self').attr('checked', false);
		$('#child_birth_date').val(' ');

		setTimeout(function(){$('#other').css('display', 'none');},500);
		// $('#affiant_fname').val(' ');
		// $('#affiant_lname').val(' ');
		// $('#affiant_mname').val(' ');
		// $('#affiant_age').val(' ');
		return false
	}
	var date, dateNow, age, person = JSON.parse(obj);
	console.log(person);
	date = new Date(person.info.date_birth);
	dateNow = new Date;
	age = (dateNow.getFullYear() - date.getFullYear());

	if($('#is_other').is(':checked')) {
		// if(person.fullname != null)
		// 	$('#child_fullname').val(person.fullname);
		// else
		$('#child_fullname').val(decodeURI(name_full));
		$('#child_birth_date').val(person.info.date_birth);
	}

	// This JS is for fetching affiant name when is_self is checked.
	// if($('#is_self').is(':checked')) {
	// 	 if(person.info.mother_fname != ''){
	// 	 	$('#affiant_fname').val(person.info.mother_fname);
	// 	 	}
	// 	 else{
	// 	 	$('#affiant_fname').val(n_f);
	// 		}
	// 	 if(person.info.mother_lname != null){
	// 	 	$('#affiant_lname').val(person.info.mother_lname);
	// 	 }
	// 	 else{
	// 	 	$('#affiant_lname').val(n_l);
	// 		}
	// 	 if(person.info.mother_mname != null){
	// 	 	$('#affiant_mname').val(person.info.mother_mname);
	// 	 }
	// 	 else{
	// 	 	$('#affiant_mname').val(n_m);
	// 	 }
	// 	 // $('#affiant_age').val(person.info.mother_fname);
	// }

	// return false;
}

function numberOnly() {
	var result, validInput = /^\d*\.?\d*$/, input = $('#affiant_age').val();
	result = validInput.test($.trim(input));
	if(!result) {

		window.alert('Only Numbers Are Allowed!');
		$('#affiant_age').val(' ');
		// $('#affiant_age').focus();	
	}
}
 $(document).ready(function(){
 regDate();
 patRegDate();
 childBday();
 ausfDate();
 adminDate();
     });
 function Again(){
 regDate();
 patRegDate();
 childBday();
 ausfDate();
 adminDate();
 }
  function regDate(){
 

 //$j('#PBEFdate').val(toDate(new Date(date), "yyyy-mm-dd") );    
  $j('#reg_date').val(toDate(new Date(), "yyyy-mm-dd") );
 
  $j('#child_birth_reg_date').datetimepicker({
         dateFormat: 'M d, yy',
         timeFormat: 'hh:mm tt',
         onSelect: function (selectedDate) {
   
            $j('#reg_date').val(toDate(new Date(selectedDate), "yyyy-mm-dd") );    
           // document.getElementById('fcker').innerHTML = 'fcker' ;     
           //regDate();
          
         },
         onClose: function (selectedDate) {  
      document.getElementById('reg_date').innerHTML = toDate(new Date(selectedDate), "mm-dd-yyyy ") ;  
           // regDate();
        
         },
     });
 

 }
   function ausfDate(){
 

 //$j('#PBEFdate').val(toDate(new Date(date), "yyyy-mm-dd") );    
  $j('#display_date_ausf_cert').val(toDate(new Date(), "yyyy-mm-dd") );
 
  $j('#date_ausf_cert').datetimepicker({
         dateFormat: 'M d, yy',
         timeFormat: 'hh:mm tt',
         onSelect: function (selectedDate) {
   
            $j('#display_date_ausf_cert').val(toDate(new Date(selectedDate), "yyyy-mm-dd") );    
           // document.getElementById('fcker').innerHTML = 'fcker' ;     
           //regDate();
          
         },
         onClose: function (selectedDate) {  
      document.getElementById('display_date_ausf_cert').innerHTML = toDate(new Date(selectedDate), "mm-dd-yyyy ") ;  
           // regDate();
        
         },
     });

 }
   function patRegDate(){
 

 //$j('#PBEFdate').val(toDate(new Date(date), "yyyy-mm-dd") );    
  $j('#paternity_reg').val(toDate(new Date(), "yyyy-mm-dd") );
 
  $j('#paternity_reg_date').datetimepicker({
         dateFormat: 'M d, yy',
         timeFormat: 'hh:mm tt',
         onSelect: function (selectedDate) {
   
            $j('#paternity_reg').val(toDate(new Date(selectedDate), "yyyy-mm-dd") );    
           // document.getElementById('fcker').innerHTML = 'fcker' ;     
           //regDate();
          
         },
         onClose: function (selectedDate) {  
      document.getElementById('paternity_reg').innerHTML = toDate(new Date(selectedDate), "mm-dd-yyyy ") ;  
           // regDate();
        
         },
     });
 

 }
    function childBday(){
 

 //$j('#PBEFdate').val(toDate(new Date(date), "yyyy-mm-dd") );    
  $j('#child_bdate').val(toDate(new Date(), "yyyy-mm-dd") );
 
 // updated by carriane 03/26/2018; chenge datetimepicker to datepicker
  $j('#child_birth_date').datepicker({
         dateFormat: 'yy-mm-dd',
         onSelect: function (selectedDate) {
   
            $j('#child_bdate').val(toDate(new Date(selectedDate), "yyyy-mm-dd") );    
           // document.getElementById('fcker').innerHTML = 'fcker' ;     
           //regDate();
          
         },
         onClose: function (selectedDate) {  
      document.getElementById('child_bdate').innerHTML = toDate(new Date(selectedDate), "mm-dd-yyyy ") ;  
           // regDate();
        
         },
     });
 

 }

 //Jamen


    function adminDate(){
 
 //$j('#PBEFdate').val(toDate(new Date(date), "yyyy-mm-dd") );    
  $j('#admin_date').val(toDate(new Date(), "yyyy-mm-dd") );
 
  $j('#administer_date').datetimepicker({
         dateFormat: 'M d, yy',
         timeFormat: 'hh:mm tt',
         onSelect: function (selectedDate) {
   
            $j('#admin_date').val(toDate(new Date(selectedDate), "yyyy-mm-dd") );    
           // document.getElementById('fcker').innerHTML = 'fcker' ;     
           //regDate();
          
         },
         onClose: function (selectedDate) {  
      document.getElementById('admin_date').innerHTML = toDate(new Date(selectedDate), "mm-dd-yyyy ") ;  
           // regDate();
        
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

function print_affidavit(pid) {
	if(!pid) {
		window.alert('Unable to print the document!');
		return false;
	} else {
		window.open(p_url+"/affidavit_father_surename.php?pid="+pid,"Affidavit_Father_Surename","modal, width=900,height=700,menubar=no,resizable=yes,scrollbars=no");
	}
}
