{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}

<p class=" slds-page-header__meta-text">
{if $CHANGES}
	<i>{'LBL_UPDATES_FOR'|@getTranslatedString:$MODULE} <b>{$DAY}</b></i>
{else}
	<i>{'LBL_NO_UPDATES'|@getTranslatedString:$MODULE}<b>{$DAY}</b></i>
{/if}
{if $DAY == $TODAY}
	<img src='{$BASEURL}themes/images/reload.gif' border=0>
	<button type="button" class="slds-button slds-button_brand slds-m-bottom_medium" style="min-width: fit-content;" onclick="indexRefresh('{$BASEURL}','{$MODULE}');">
		{'LBL_REFRESH_NOW'|@getTranslatedString:$MODULE}
	</button>
{/if}
</p>
{if $CHANGES}
	{include file='modules/DART/htmlscrreport.tpl'}
{/if}
<script type="text/javascript">
	function indexRefresh(baseurl,module) {
		var todayUser = $('#assigned_user_id').val();
		location.href = baseurl+'index.php?module='+module+'&action=index&_refresh=true&_todayUser='+encodeURIComponent(todayUser);
	}
</script>
