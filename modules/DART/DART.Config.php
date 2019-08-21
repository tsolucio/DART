<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

global $current_user,$currentModule;
$mailto = getUserEmail(1);

$__DART_CONFIG = array(

	/** Configuration for mailing, make sure Outgoing Server is setup */
	'mail.subject' => getTranslatedString('ActivityUpdate', 'DART'),
	'mail.subject.noactivity' => getTranslatedString('NoActivity', 'DART'),
	//'mail.from'    => 'noreply@company.com',
	'mail.fromname'=> getTranslatedString('DART', 'DART'),

	'mail.to'      => array( $mailto ),

	//'mail.cc'      => array( 'services@company.com' ),
	//'mail.replyto' => 'replyto@company.com',

	// Is outgoing server setup with sendmail?
	'mail.sendmail' => false,
);
?>
