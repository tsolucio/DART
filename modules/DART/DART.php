<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once dirname(__FILE__) . '/DART.Config.php';

/**
 * DART class extending the Core class
 */
class DART extends DART_Core {

	public $moduleIcon = array('library' => 'utility', 'containerClass' => 'slds-icon_container slds-icon-standard-account', 'class' => 'slds-icon', 'icon'=>'activity');

	/**
	 * Determine user's identify having not acted for the day.
	 */
	public function users_noactivityForTheDay($fordate) {
		global $adb;
		$result = $adb->pquery(
			"SELECT id, user_name as name, email1, email2
				FROM vtiger_users
				WHERE deleted=0 AND status='Active' AND id not IN (SELECT distinct smownerid FROM vtiger_dart_recordchanges WHERE modifiedon=?)",
			array($fordate)
		);
		$rows = array();
		if ($adb->num_rows($result)) {
			while ($resultrow = $adb->fetch_array($result)) {
				$row = array('name' => decode_html($resultrow['name']));
				$row['email'] = empty($resultrow['email1'])? $resultrow['email2'] : $resultrow['email1'];
				$rows[] = $row;
			}
		}
		return $rows;
	}

	/**
	 * Gather the changes of record by-user-by-module
	 */
	public function report_GatherChangedRecordDetails($fordate, $foruser = '') {
		return $this->report_GatherChangesByUserAndModuleForTheDay($fordate, $foruser);
	}

	/**
	 * Gather the changes of record by-user-by-module-for the day
	 */
	public function report_GatherChangesByUserAndModuleForTheDay($date, $user = '') {
		global $adb;

		$group = array();

		$moduleinfo = $adb->pquery('SELECT * FROM vtiger_entityname', array());

		if (!$adb->num_rows($moduleinfo)) {
			return $group;
		}

		while ($row = $adb->fetch_array($moduleinfo)) {
			$query = $this->report_PrepareQueryForTheModule($row['modulename'], $row['tablename'], $row['entityidfield'], $row['fieldname'], $user != '');
			if ($user == '') {
				$records = $adb->pquery($query, array($date));
			} else {
				$records = $adb->pquery($query, array($date, $user));
			}

			if (!$adb->num_rows($records)) {
				continue;
			}

			while ($record = $adb->fetch_array($records)) {
				$module = $row['modulename'];
				$changeType = $record['haschanged'] ? 'UPDATED' : 'CREATED';
				$changeOwner = empty($record['modifier'])? $record['owner'] : $record['modifier'];

				if ($this->permittedToView($module, $record['id']) === false) {
					continue;
				}

				$username = $this->username($changeOwner);
				if (!isset($group[$username])) {
					$group[$username] = array();
				}
				if (!isset($group[$username][$module])) {
					$group[$username][$module] = array();
				}

				$group[$username][$module][] = array(
					'id' => $record['id'], 'title' => $record['title'], 'module' => $record['module'], 'changetype' => $changeType
				);
			}
			unset($records);
		}
		return $group;
	}

	/**
	 * Query to pickup the changes across all the module.
	 */
	public function report_PrepareQueryForTheModule($module, $table, $idcolumn, $titlecolumn, $filter_user = false) {
		if (strpos($titlecolumn, ',') > 0) {
			$titlecolumn = 'concat('.str_replace(',', ",' ',", $titlecolumn).')';
		}

		$idcolumnalias = $idcolumn;
		if ($idcolumnalias != 'id') {
			$idcolumnalias = "$idcolumn as id";
		} else {
			$idcolumnalias = "$table.$idcolumn";
		}

		$selectcolumn = $titlecolumn;
		if ($selectcolumn != $idcolumn) {
			$selectcolumn .= ' as title, ' . $idcolumnalias;
		}
		return "SELECT $selectcolumn, vtiger_dart_recordchanges.modifiedby as modifier, vtiger_dart_recordchanges.smownerid as owner, module, createdtime!=modifiedtime as haschanged
			FROM $table
			INNER JOIN vtiger_dart_recordchanges ON $table.$idcolumn = vtiger_dart_recordchanges.crmid AND modifiedon=?
			INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid=vtiger_dart_recordchanges.crmid".($filter_user ? ' AND modifiedby=?' : '');
	}
}

/**
 * Core DART class
 */
class DART_Core {
	/**
	 * Helper function to retrieve username
	 */
	public static $usernameCache = false;
	public function username($id) {
		global $adb;
		if (self::$usernameCache === false) {
			self::$usernameCache = array();
			$result = $adb->pquery('SELECT user_name, id FROM vtiger_users', array());
			while ($row = $adb->fetch_array($result)) {
				self::$usernameCache[$row['id']] = $row['user_name'];
			}
		}
		return self::$usernameCache[$id];
	}

	/**
	 * Helper function to retrieve configuration parameter
	 */
	public function config($key, $defvalue = false) {
		global $__DART_CONFIG;
		if (isset($__DART_CONFIG[$key])) {
			return $__DART_CONFIG[$key];
		}
		return $defvalue;
	}

	// Access restriction user context
	protected $activeUser = false;

	/**
	 * Set active user context.
	 */
	public function setActiveUser($user) {
		$this->activeUser = $user;
	}

	/**
	 * Is record view permitted?
	 */
	public function permittedToView($module, $crmid) {
		if ($this->activeUser !== false) {
			return (isPermitted($module, 'DetailView', $crmid) == 'yes');
		}
		return true;
	}

	/**
	 * Record the changes for the day
	 */
	public function record_ChangesForTheDay($date, $user = '') {
		global $adb;

		if ($user == '') {
			$sql = 'SELECT setype, crmid, smownerid, modifiedby FROM vtiger_crmentity WHERE DATE(modifiedtime)=?';
			$result = $adb->pquery($sql, array($date));
		} else {
			$sql = 'SELECT setype, crmid, smownerid, modifiedby FROM vtiger_crmentity WHERE DATE(modifiedtime)=? AND modifiedby = ?';
			$result = $adb->pquery($sql, array($date, $user));
		}

		if (!$adb->num_rows($result)) {
			return;
		}

		while ($row = $adb->fetch_array($result)) {
			$this->record_ChangesForTheModule($row['setype'], $row['crmid'], $row['smownerid'], $row['modifiedby'], $date);
		}
	}

	/**
	 * Record the changes for the module for the day
	 */
	public function record_ChangesForTheModule($module, $crmid, $smownerid, $modifiedby, $date) {
		global $adb;
		$sql = 'INSERT IGNORE INTO vtiger_dart_recordchanges(module, crmid, smownerid, modifiedby, modifiedon) VALUES (?, ?, ?, ?, ?)';
		$adb->pquery($sql, array($module, $crmid, $smownerid, $modifiedby, $date));
	}
}
?>
