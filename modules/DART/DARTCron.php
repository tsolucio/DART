<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

/**
 * Turn-off PHP error reporting & request max timeout
 */
try {
	error_reporting(0);
	set_time_limit(0);
} catch(Exception $e) { }
$Vtiger_Utils_Log = false;
include_once 'vtlib/Vtiger/Module.php';
require_once 'modules/Emails/mail.php';
require_once 'modules/Emails/Emails.php';
include_once 'vtlib/Vtiger/Mailer.php';
include_once 'modules/DART/DART.php';
include_once 'Smarty_setup.php';

/**
 * Global configuration
 */
global $site_URL, $default_language, $app_strings, $adb, $current_user;
if (empty($current_user)) {
	$current_user = Users::getActiveAdminUser();
}
if (empty($current_language)) {
	$current_language = $default_language;
}
if (empty($app_strings)) {
	$app_strings = return_application_language($current_language);
}

class DART_Cron {
	protected $dart;
	protected $today;
	protected $recorded = false;

	public function __construct() {
		$this->today = date('Y-m-d');
		$this->dart = new DART();
	}

	public function viewer() {
		global $site_URL;

		$smarty = new vtigerCRM_Smarty();
		$smarty->assign('BASEURL', "{$site_URL}/");
		$smarty->assign('DAY', $this->today);
		$smarty->assign('MODULE', 'DART');
		return $smarty;
	}

	public function mailer() {
		$mailer = new Vtiger_Mailer();
		$mailer->IsHTML(true);

		$mailer->From = $mailer->Username;

		$mailsendmail = $this->dart->config('mail.sendmail');
		if ($mailsendmail === true || $mailsendmail == 'true') {
			$mailer->IsSendMail();
		}

		$mailfrom = $this->dart->config('mail.from');
		if (!empty($mailfrom)) {
			$mailer->From = $mailfrom;
		}

		$mailfromname = $this->dart->config('mail.fromname');
		if (!empty($mailfromname)) {
			$mailer->FromName = $mailfromname;
		}

		$mailreplyto = $this->dart->config('mail.replyto');
		if (!empty($mailreplyto)) {
			$mailer->AddReplyTo($mailreplyto);
		}

		return $mailer;
	}

	public function recordNow() {
		if ($this->recorded) {
			return;
		}

		// Trigger record changes
		$this->dart->record_ChangesForTheDay($this->today);
		$changes = $this->dart->report_GatherChangedRecordDetails($this->today);
		$this->recorded = true;

		return $changes;
	}

	public function recordAndEmailActivityReport() {
		global $site_URL;

		$changes = $this->recordNow();

		$viewer = $this->viewer();
		$viewer->assign('CHANGES', $changes);
		$viewer->assign('SITE_URL', $site_URL);

		$mailcontent = $viewer->fetch(vtlib_getModuleTemplate('DART', 'emailreport.tpl'));
		$mailsubject = str_replace('$date$', date('Y-m-d'), $this->dart->config('mail.subject'));

		$mailto = $this->dart->config('mail.to');
		if (!empty($mailto)) {
			$tomail = '';
			foreach ($mailto as $to) {
				$tomail .= $to.',';
			}
			$mailto = substr($tomail, 0, -1);
		} else {
			$mailto = '';
		}

		$mailcc = $this->dart->config('mail.cc');
		if (!empty($mailcc)) {
			$ccmail = '';
			foreach ($mailcc as $cc) {
				$ccmail .= $cc.',';
			}
			$mailcc = substr($ccmail, 0, -1);
		} else {
			$mailcc = '';
		}

		$HELPDESK_SUPPORT_EMAIL_ID = GlobalVariable::getVariable('HelpDesk_Support_EMail', 'support@your_support_domain.tld', 'HelpDesk');
		$HELPDESK_SUPPORT_NAME = GlobalVariable::getVariable('HelpDesk_Support_Name', 'your-support name', 'HelpDesk');
		$from_name = $this->dart->config('mail.fromname');
		if (empty($from_name)) {
			$from_name = $HELPDESK_SUPPORT_NAME;
		}

		$from_email = $this->dart->config('mail.from');
		if (empty($from_email)) {
			$from_email = $HELPDESK_SUPPORT_EMAIL_ID;
		}

		send_mail('Emails', $mailto, $from_name, $from_email, $mailsubject, $mailcontent, $mailcc, '');
		unset($changes);
		unset($viewer);
	}

	public function sendNoActivityUpdateEmail() {
		$this->recordNow(); // Making sure the changes has been recorded already.

		$users = $this->dart->users_noactivityForTheDay($this->today);
		if (!empty($users)) {
			$viewer = $this->viewer();

			$mailcontent = $viewer->fetch(vtlib_getModuleTemplate('DART', 'noactivity.tpl'));
			$mailsubject = str_replace('$date$', $this->today, $this->dart->config('mail.subject.noactivity'));

			$to = '';
			foreach ($users as $user) {
				if (!in_array($user['email'], (array)$to)) {
					/* Put all users on TO */
					$to[] = $user['email'];
					$to .= $user['email'].',';
				}
			}
			$to = substr($to, 0, -1);
			$bcc = '';

			$HELPDESK_SUPPORT_EMAIL_ID = GlobalVariable::getVariable('HelpDesk_Support_EMail', 'support@your_support_domain.tld', 'HelpDesk');
			$HELPDESK_SUPPORT_NAME = GlobalVariable::getVariable('HelpDesk_Support_Name', 'your-support name', 'HelpDesk');
			$from_name = $this->dart->config('mail.fromname');
			if (empty($from_name)) {
				$from_name = $HELPDESK_SUPPORT_NAME;
			}

			$from_email = $this->dart->config('mail.from');
			if (empty($from_email)) {
				$from_email = $HELPDESK_SUPPORT_EMAIL_ID;
			}

			send_mail('Emails', $to, $from_name, $from_email, $mailsubject, $mailcontent, '', $bcc);
		}

		unset($users);
		unset($mailer);
		unset($viewer);
	}
}

$dartCron = new DART_Cron();
$dartCron->recordAndEmailActivityReport();

// Uncomment the line below to send reminder to user with no activity
//$dartCron->sendNoActivityUpdateEmail();
?>
