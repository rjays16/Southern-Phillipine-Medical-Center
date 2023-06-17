/* 
 * 
 * 
 */

function saveFingerprintTemplate(cpid, fptemplate) {      
    console.log(fptemplate);    
    if (fptemplate) {
        $.ajax({
            type: 'POST',
            url: '../../../index.php?r=biometric/biometric/saveFingerprint',
            data: { pid: JSON.stringify(cpid), template: JSON.stringify(fptemplate) },          
            success: function(data) {
                        swal.fire({
                          position: 'top-end',
                          type: 'success',
                          title: 'Fingerprint saved!',
                          showConfirmButton: false,
                          timer: 1500
                        })
                    },
            error: function(jqXHR, exception) {
                        console.log(jqXHR.responseText)
                        swal.fire({
                          position: 'top-end',
                          type: 'error',
                          title: jqXHR.responseText,
                          showConfirmButton: false,
                          timer: 1500
                        })
                    },
            dataType: 'json'                  
        });      
    }    
} 
