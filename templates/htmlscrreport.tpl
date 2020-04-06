{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
<table class="slds-table slds-table_cell-buffer slds-table_bordered slds-table_striped">
<thead>
	<tr class="slds-line-height_reset">
		<th scope="col">
			<div class="slds-truncate" title="{'LBL_USER'|@getTranslatedString:$MODULE}">{'LBL_USER'|@getTranslatedString:$MODULE}</div>
		</th>
		<th scope="col">
			<div class="slds-truncate" title="{'LBL_ACTION_PERFORMED'|@getTranslatedString:$MODULE}">{'LBL_ACTION_PERFORMED'|@getTranslatedString:$MODULE}</div>
		</th>
	</tr>
</thead>
<tbody>
{foreach item=GROUP key=USER from=$CHANGES}
	<tr class="slds-hint-parent">
		<td>
			<div class="slds-truncate" title="{$USER}"><strong>{$USER}</strong></div>
		</td>
		<td>
			<table class="slds-table slds-table_cell-buffer slds-table_bordered slds-table_striped">
			{foreach item=RECORDS key=MODULE from=$GROUP}
				<tr>
					<td>{$MODULE|@getTranslatedString:$MODULE}</td>
					<td>
						<table class="slds-table slds-table_cell-buffer slds-table_bordered slds-table_striped">
						{foreach item=RECORD from=$RECORDS}
						<tr>
							<td>
								{$RECORD.changetype|@getTranslatedString:'DART'}</>: <a href="{$BASEURL}index.php?module={$RECORD.module}&amp;action=DetailView&amp;record={$RECORD.id}">{$RECORD.title}</a>
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
</tbody>
</table>