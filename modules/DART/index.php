<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $current_user, $currentModule, $theme, $app_strings, $mod_strings, $site_URL;

include_once 'Smarty_setup.php';
include_once dirname(__FILE__) . '/DART.php';

$daySelected = isset($_REQUEST['_date']) ? vtlib_purify($_REQUEST['_date']) : '';
if (empty($daySelected)) {
	$daySelected = date('Y-m-d');
}

$today = date('Y-m-d');

$dart = new DART();
$dart->setActiveUser($current_user);

if (isset($_REQUEST['_refresh']) && $_REQUEST['_refresh'] == 'true') {
	$dart->record_ChangesForTheDay($today);
}

$changes = $dart->report_GatherChangedRecordDetails($daySelected);

$smarty = new vtigerCRM_Smarty();
$smarty->assign('BASEURL', '');
$smarty->assign('CHANGES', $changes);
$smarty->assign('DAY', $daySelected);
$smarty->assign('TODAY', $today);

$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
include 'modules/cbupdater/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('SITE_URL', $site_URL);

$smarty->display(vtlib_getModuleTemplate($currentModule, 'index.tpl'));
?>
