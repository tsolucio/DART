{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
<p>{$SITE_URL}</p>
<p style="font: 12px Arial, sans-serif;">
{if $CHANGES}
		<i>{'LBL_UPDATES_FOR'|@getTranslatedString:$MODULE} <b>{$DAY}</b></i>
{else}
		<i>{'LBL_NO_UPDATES'|@getTranslatedString:$MODULE}<b>{$DAY}</b></i>
{/if}
{if $DAY == $TODAY}
	<img src='{$BASEURL}themes/images/reload.gif' border=0> <a href='{$BASEURL}index.php?module={$MODULE}&action=index&parenttab={$CATEGORY}&_refresh=true'>{'LBL_REFRESH_NOW'|@getTranslatedString:$MODULE}</a>
{/if}
</p>
{if $CHANGES}
	{include file='modules/DART/htmlreport.tpl'}
{/if}
