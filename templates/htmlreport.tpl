{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
<table cellpadding=0 cellspacing=1 style="font: 12px Arial, sans-serif; background: #DDDDFF;">
	<tr>
		<td style="background: #FFFFFF; border: 1px solid #DDDDFF; border-right: 0; padding: 5px;"><strong>{'LBL_USER'|@getTranslatedString:$MODULE}</strong></td>
		<td style="background: #FFFFFF; border: 1px solid #DDDDFF; padding: 1px;"><strong>{'LBL_ACTION_PERFORMED'|@getTranslatedString:$MODULE}</strong></td>
	</tr>
	
	{foreach item=GROUP key=USER from=$CHANGES}
		<tr valign=top>
			<td width="120px" nowrap="nowrap" style="background: #FFFFFF; border: 1px solid #DDDDFF; border-right: 0; padding: 0 5px;"><strong>{$USER}<strong></td>

			<td style="background: #FFFFFF;">
				<table width="100%" cellpadding=1 cellspacing=1 border=0 style="font: inherit; background: #DDDDFF;">
				{foreach item=RECORDS key=MODULE from=$GROUP}
					<tr valign=top>
						<td width="120px" nowrap="nowrap" style="background: #FFFFFF;">{$MODULE|@getTranslatedString:$MODULE}</td>
				
						<td style="background: #FFFFFF;">
							<table cellpadding=3 cellspacing=0 border=0 style="font: inherit;">
							{foreach item=RECORD from=$RECORDS}
							<tr valign=top>
								<td>
									<small>{$RECORD.changetype|@getTranslatedString:'DART'}</small>: <a href="{$BASEURL}index.php?module={$RECORD.module}&amp;action=DetailView&amp;record={$RECORD.id}">{$RECORD.title}</a>
								</td>
							</tr>
							{/foreach}
							</table>
						</td>
					</tr>
				{/foreach}
				</table>
			</td>
		</tr>
	{/foreach}
</table>
