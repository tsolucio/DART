{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}

{include file="Buttons_List.tpl"}

<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher">
<div class="slds-modal__container slds-p-around_none slds-card" style="min-height: 350px;display:block;">
	<div class="slds-grid slds-gutters">
		<div class="slds-col slds-size_4-of-6 slds-page-header__meta-text">
			<span class="slds-m-left_small">{'LBL_SELECT_TEXT'|@getTranslatedString:$MODULE}</span>
			<!-- Calendar setup -->
			<div id='_cal' style="max-width: 310px;" class="slds-m-left_small"></div>
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
			<div width="50%" max-width="50%" style="padding-left: 10px;">
				{include file="modules/DART/scrreport.tpl"}
			</div>
		</div>
		<div class="slds-col slds-size_2-of-6">
			<div style="padding-left:2rem;padding-top:1.95rem;position:relative">
				<a href="javascript:void(0)" aria-describedby="help">
					<span class="slds-icon_container slds-icon-utility-info">
					<svg class="slds-icon slds-icon slds-icon_xx-small slds-icon-text-default" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#info"></use>
					</svg>
					<span class="slds-assistive-text">{'LBL_NOTE_TEXT'|@getTranslatedString:$MODULE}</span>
					</span>
				</a>
				<div class="slds-popover slds-popover_tooltip slds-nubbin_left-top" role="tooltip" id="help" style="position:absolute;top:14px;left:60px">
					<div class="slds-popover__body"><b>{'NOTE'|@getTranslatedString:$MODULE}:</b> {'LBL_NOTE_TEXT'|@getTranslatedString:$MODULE}</div>
				</div>
			</div>
		</div>
	</div>
</div>
</section>