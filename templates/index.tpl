{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}

{include file="Buttons_List.tpl"}

<div style="min-height: 350px;">

<table width="100%" cellpadding=3 cellspacing=0 border=0>
<tr valign=top>
	<td width="50%" style="padding-left: 10px;">
		<p style="font: 12px Arial, sans-serif; clear: both;">
			<b>{'NOTE'|@getTranslatedString:$MODULE}:</b> {'LBL_NOTE_TEXT'|@getTranslatedString:$MODULE}
		</p>
		
		<!-- Calendar setup -->
		<div id='_cal' style="float: left;"></div>
		
		<script type="text/javascript">
		Calendar.setup({ldelim}
			date: "{'-'|@str_replace:'/':$DAY}",
			flat: "_cal",
			flatCallback : function(cal) {ldelim}
				var yyyy = cal.date.getFullYear(), mm = cal.date.getMonth()+1, dd = cal.date.getDate();
				if (mm < 10) mm = "0" + mm;
				if (dd < 10) dd = "0" + dd;
				var selectedDate = yyyy + '-' + mm + '-' + dd;
				VtigerJS_DialogBox.block();
				location.href = 'index.php?module=DART&action=index&_date=' + encodeURIComponent(selectedDate)
			{rdelim}
		{rdelim});
		</script>
		<p style="clear: both;"></p>
	</td>
</tr>
<tr valign=top>
	<td width="50%" max-width="50%" style="padding-left: 10px;">
		{include file="modules/DART/emailreport.tpl"}
	</td>
</tr>
</table>
</div>
