<?php
/**
 * @author Gervie 03/23/2016
 */
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');

$breakfile=$root_path.'main/startframe.php'.URL_APPEND;
// reset all 2nd level lock cookies
require($root_path.'include/inc_2level_reset.php');

# Start Smarty templating here
/**
 * LOAD Smarty
 */

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# Module title in the toolbar
$smarty->assign('sToolbarTitle',"PDPU");

# Hide the return button
$smarty->assign('pbBack',FALSE);
//-------------------------

# Help button href
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

# href for close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('title',"Social Service");


// Added by Gervie 11/02/2015
$smarty->assign('sLabServicesRequestIcon','<img ' . createComIcon($root_path,'statbel2.gif','0') . ' align="absmiddle">');
$smarty->assign('LDAssessment', "<a href=\"pdpu_pass.php?sid=$sid&lang=$lang&target=referral\">Assessment and Referral Form</a>");
$smarty->assign('LDComprehensiveIcon','<img ' . createComIcon($root_path,'search_plus.gif','0') . ' align="absmiddle">');
$smarty->assign('LDComprehensive', "<a href='javascript:CompreSearchFxn();'>Comprehensive</a>");

$smarty->assign('sMainBlockIncludeFile','pdpu/pdpu_submenu.tpl');
$smarty->display('common/mainframe.tpl');
?>
<script type="text/javascript">

    ShortcutKeys();
    function ShortcutKeys() {
        //new person
        shortcut.add('Alt+P', NewRegFxn,
            {
                'type': 'keydown',
                'propagate': false,
            }
        );

        //new born registration
        shortcut.add('Alt+N', NewbornFxn,
            {
                'type': 'keydown',
                'propagate': false,
            }
        );

        //search
        shortcut.add('Alt+Z', SearchFxn,
            {
                'type': 'keydown',
                'propagate': false,
            }
        );

        // added by: syboy 12/18/2015 : meow; search employee
        shortcut.add('Alt+D', SearchDoc,
                                {
                                    'type':'keydown',
                                    'propagate':false,
                                }
                        );
        // ended

        //advance search
        shortcut.add('Alt+X', AdvanceSearchFxn,
            {
                'type': 'keydown',
                'propagate': false,
            }
        );

        //comprehensive search
        shortcut.add('Alt+C', CompreSearchFxn,
            {
                'type': 'keydown',
                'propagate': false,
            }
        );

        //consultation
        shortcut.add('Alt+A', ConsultationFxn,
            {
                'type': 'keydown',
                'propagate': false,
            }
        );

        //icd, icpm
        shortcut.add('Alt+M', MedocsFxn,
            {
                'type': 'keydown',
                'propagate': false,
            }
        );

        //reports
        shortcut.add('Alt+R', ReportsFxn,
            {
                'type': 'keydown',
                'propagate': false,
            }
        );

        //Added by Borj 2014-08-04 ISO
        //IpdUserManual
        shortcut.add('Alt+I', IpdUserManualFxn,
            {
                'type': 'keydown',
                'propagate': false,
            }
        );
    }

    //new person
    function NewRegFxn() {
        urlholder = "<?=$root_path?>modules/ipd/seg-ipd-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipd_reg&from=ipd";
        window.location.href = urlholder;
    }

    //new born registration
    function NewbornFxn() {
        urlholder = "<?=$root_path?>modules/ipd/seg-ipd-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipd_newbornreg&from=ipd";
        window.location.href = urlholder;
    }

    //search
    function SearchFxn() {
        urlholder = "<?=$root_path?>modules/ipd/seg-ipd-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipd_searchpatient&from=ipd";
        window.location.href = urlholder;
    }

    // added by: syboy 12/18/2015 : meow; Search Employee
    function SearchDoc(){
        // urlholder="<?=$root_path?>modules/personell_admin/personell_search.php?from=medocs&department=Admitting";
        urlholder = "<?=$root_path?>modules/ipd/seg-ipd-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipd_searchdoctor&from=ipd";
        window.location.href=urlholder;
    }

    //advance search
    function AdvanceSearchFxn() {
        urlholder = "<?=$root_path?>modules/ipd/seg-ipd-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipd_searchadv&from=ipd";
        window.location.href = urlholder;
    }

    //comprehensive search
    function CompreSearchFxn() {
        urlholder = "<?=$root_path?>modules/ipd/seg-ipd-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipd_searchcomprePDPU";
        window.location.href = urlholder;
    }

    //consultation
    function ConsultationFxn() {
        urlholder = "<?=$root_path?>modules/ipd/seg-ipd-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipd_consultation&from=ipd";
        window.location.href = urlholder;
    }

    //icd, icpm
    function MedocsFxn() {
        urlholder = "<?=$root_path?>modules/ipd/seg-ipd-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipd_icdicpm&from=ipd";
        window.location.href = urlholder;
    }

    //Medical Certificate
    function MedCertFxn() {
        //urlholder="<?=$root_path?>modules/registration_admission/cert_med_search.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=er_icdicpm&from=er";
        urlholder = "<?=$root_path?>modules/ipd/seg-ipd-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipd_medcert&from=ipd";
        window.location.href = urlholder;
    }

    //reports
    function ReportsFxn() {
        urlholder = "<?=$root_path?>modules/ipd/seg-ipd-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=reports&from=ipd";
        window.location.href = urlholder;
    }

    //reports
    function ReportsFxn2() {
        window.location.href = "<?=$root_path?>modules/ipd/seg-ipd-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ireports&from=ipd";
    }

    //Added by Borj 2014-08-04 ISO
    //IpdUserManual
    function IpdUserManualFxn() {
        urlholder = "<?=$root_path?>forms/ADMISSION.pdf?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=reports&from=ipd";
        window.location.href = urlholder;
    }
</script>