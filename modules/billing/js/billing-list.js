function showDetailsSection() {
	window.parent.js_showDetailsSection();
}

function addClaim(insurance_nr, categ_id, categ_desc, prd, enc_nr, patient, claim, pclaim, newform, smod_opscode, indicator, cat_code_indicator) {
		var details = new Object();

		details.insurance_nr = insurance_nr;
		details.categ_id = categ_id;
		details.categ_desc = categ_desc;
		details.prd = prd;
		details.enc_nr = enc_nr;
		details.patient = patient;
		details.claim = claim;
		details.pclaim = pclaim;
		details.newform = newform;
		details.smod_opscode = smod_opscode; // added by: syboy 06/27/2015
		// details.deff_code = deff_code; // added by: syboy 07/31/2015
		details.indicator = indicator; // added by: syboy 10/12/2015
		details.cat_code_indicator = cat_code_indicator; // added by: syboy 11/13/2015 : meow

	window.parent.js_addClaim(details);
}

function fillupTransmittalDetails(go_fillup, hcare_id) {
	if (go_fillup == '1') {
				$('cases_list').innerHTML = window.parent.$('cases').innerHTML;
		xajax_showTransmittalDetails(hcare_id, $('cases_list').innerHTML);
	}
}

function assignEncNrsBilled() {
		var s = '';
		var elems = document.getElementsByName("cases_added[]");

		for(var i=0;i<elems.length;i++) {
				if(elems[i].name=='cases_added[]') {
						//alert(elems[i].value);
						if (i > 0) s += ",";
						s += elems[i].value;
				}
		}

		//alert($('cases_list').innerHTML);
		window.parent.$('cases').innerHTML = s;
}

function getEncounterNosBilled() {
		if (window.parent.$('cases')) {
				$('cases_list').innerHTML = window.parent.$('cases').innerHTML;
				xajax_assignToSessionVar($('cases_list').innerHTML);
		}
}