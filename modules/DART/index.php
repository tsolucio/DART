<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $current_user, $currentModule, $theme, $app_strings, $mod_strings, $site_URL, $adb;

include_once 'Smarty_setup.php';
include_once dirname(__FILE__) . '/DART.php';

$firsttime = true;
$daySelected = isset($_REQUEST['_date']) ? vtlib_purify($_REQUEST['_date']) : '';
if (empty($daySelected)) {
	$daySelected = date('Y-m-d');
}
$userSelected = isset($_REQUEST['_user']) ? vtlib_purify($_REQUEST['_user']) : '';
if (empty($userSelected)) {
	$userSelected = $current_user->id;
} else {
	$firsttime = false;
}

//Start loading user picklist values
$result = $adb->pquery("SELECT * FROM vtiger_field WHERE tabid='6' AND uitype='53'", array());
$uitype = $adb->query_result($result, '0', 'uitype');
$fieldname = $adb->query_result($result, '0', 'fieldname');
$fieldlabel = $adb->query_result($result, '0', 'fieldlabel');
$maxlength = $adb->query_result($result, '0', 'maximumlength');
$generatedtype = $adb->query_result($result, '0', 'generatedtype');
$typeofdata = $adb->query_result($result, '0', 'typeofdata');
$userfld = getOutputHtml($uitype, $fieldname, $fieldlabel, $maxlength, array('assigned_user_id'=>$current_user->id), $generatedtype, $module, '', $typeofdata);
//End loading user picklist values
$today = date('Y-m-d');

$dart = new DART();
$dart->setActiveUser($current_user);

if ((isset($_REQUEST['_refresh']) && $_REQUEST['_refresh'] == 'true')  && (isset($_REQUEST['_todayUser']) && !empty($_REQUEST['_todayUser']))) {
	$todayUser = vtlib_purify($_REQUEST['_todayUser']);
	$dart->record_ChangesForTheDay($today, $todayUser);
	$userSelected = $todayUser;
	$firsttime = false;
}
if ($firsttime) {
	$changes = array();
} else {
	$changes = $dart->report_GatherChangedRecordDetails($daySelected, $userSelected);
}

$smarty = new vtigerCRM_Smarty();
$smarty->assign('BASEURL', '');
$smarty->assign('CHANGES', $changes);
$smarty->assign('DAY', $daySelected);
$smarty->assign('USERSELECTED', $userSelected);
$smarty->assign('USERPICKLIST', $userfld);
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
