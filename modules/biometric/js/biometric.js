function getRegisteredFingerprint(pid) {
	xajax_populateRegisteredFingerprint(pid);
}

function setRegisteredFingerprint(fingerPrint, isExist) {	
	fingerPrint.forEach(fingerPrintFn);
	function fingerPrintFn(item, index) {
		let isHidden='';
		if(isExist[index]==0) {
			isHidden = 'hidden';
		}
		$J("#fingerPrintDisplay").append('<img '+isHidden+' class="fingers" src="../../modules/registration_admission/image/fingerprint/'+item+'.png" >');
	}
}
