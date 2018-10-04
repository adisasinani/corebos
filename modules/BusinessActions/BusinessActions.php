<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'data/CRMEntity.php';
require_once 'data/Tracker.php';
include_once 'vtlib/Vtiger/Utils/StringTemplate.php';
include_once 'vtlib/Vtiger/LinkData.php';

class BusinessActions extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_businessactions';
	public $table_index = 'businessactionsid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_businessactionscf', 'businessactionsid');
	// related_tables variable should define the association (relation) between dependent tables
	// FORMAT: related_tablename => array(related_tablename_column[, base_tablename, base_tablename_column[, related_module]] )
	// Here base_tablename_column should establish relation with related_tablename_column
	// NOTE: If base_tablename and base_tablename_column are not specified, it will default to modules (table_name, related_tablename_column)
	// Uncomment the line below to support custom field columns on related lists
	// var $related_tables = array('vtiger_MODULE_NAME_LOWERCASEcf' => array('MODULE_NAME_LOWERCASEid', 'vtiger_MODULE_NAME_LOWERCASE',
	// 'MODULE_NAME_LOWERCASEid', 'MODULE_NAME_LOWERCASE'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_businessactions', 'vtiger_businessactionscf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_businessactions' => 'businessactionsid',
		'vtiger_businessactionscf' => 'businessactionsid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'businessactions_no' => array('businessactions' => 'businessactions_no'),
		'linklabel' => array('businessactions' => 'linklabel'),
		'linktype' => array('businessactions' => 'elementtype_action'),
		'module_list' => array('businessactions' => 'module_list'),
		'active' => array('businessactions' => 'active'),
		'Assigned To' => array('crmentity' => 'smownerid'),
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'businessactions_no' => 'businessactions_no',
		'linklabel' => 'linklabel',
		'linktype' => 'elementtype_action',
		'module_list' => 'module_list',
		'active' => 'active',
		'Assigned To' => 'assigned_user_id',
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'businessactions_no';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'businessactions_no' => array('businessactions' => 'businessactions_no'),
		'linklabel' => array('businessactions' => 'linklabel'),
		'linktype' => array('businessactions' => 'elementtype_action'),
		'module_list' => array('businessactions' => 'module_list'),
		'active' => array('businessactions' => 'active'),
		'Assigned To' => array('crmentity' => 'smownerid'),
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'businessactions_no' => 'businessactions_no',
		'linklabel' => 'linklabel',
		'linktype' => 'elementtype_action',
		'module_list' => 'module_list',
		'active' => 'active',
		'Assigned To' => 'assigned_user_id',
	);

	// For Popup window record selection
	public $popup_fields = array('businessactions_no');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'businessactions_no';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'businessactions_no';

	// Required Information for enabling Import feature
	public $required_fields = array('businessactions_no' => 1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'businessactions_no';
	public $default_sort_order = 'ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'businessactions_no');

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
	}

	private static function convertToObject($tabid, $valuemap) {

		$link_obj = new Vtiger_Link();

		$link_obj->tabid = $tabid;
		$link_obj->linkid = $valuemap['businessactionsid'];
		$link_obj->linktype       = $valuemap['elementtype_action'];
		$link_obj->linklabel      = $valuemap['linklabel'];
		$link_obj->linkurl        = decode_html($valuemap['linkurl']);
		$link_obj->linkicon       = decode_html($valuemap['linkicon']);
		$link_obj->sequence       = $valuemap['sequence'];
		$link_obj->status         = (isset($valuemap['status']) ? $valuemap['status'] : false);
		$link_obj->handler_path   = $valuemap['handler_path'];
		$link_obj->handler_class  = $valuemap['handler_class'];
		$link_obj->handler        = $valuemap['handler'];
		$link_obj->onlyonmymodule = $valuemap['onlyonmymodule'];

		return $link_obj;
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		if ($event_type == 'module.postinstall') {
			// TODO Handle post installation actions
			$this->setModuleSeqNumber('configure', $modulename, 'bact-', '0000001');
		} elseif ($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
		} elseif ($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled.
		} elseif ($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} elseif ($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} elseif ($event_type == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
		}
	}

	public static function getAllByType($tabid, $type = false, $parameters = false, $userid = null, $recordid = null) {
		global $adb, $current_user;

		$accumulator = array();
		$module_name = getTabModuleName($tabid);
		$multitype = false;

		if ($userid == null) {
			$userid = $current_user->id;
		}

		$type_sql = "";

		if ($type) {
			// Multiple link type selection
			if (is_array($type)) {
				$multitype = true;
				$type_sql = $adb->convert2Sql(' AND elementtype_action IN ('.Vtiger_Utils::implodestr('?', count($type), ',') .') ', $adb->flatten_array($type));
			} else {
				$type_sql = $adb->convert2Sql(' AND elementtype_action = ?', array($type));
			}
		}

		$query = 'SELECT businessactionsid, 
                         elementtype_action,
                         linklabel,
                         linkurl,
                         linkicon,
                         sequence,
                         handler_path,
                         handler_class,
                         handler,
                         onlyonmymodule,
                         brmap,
                         mandatory
                    FROM vtiger_businessactions INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_businessactions.businessactionsid
                   WHERE vtiger_crmentity.deleted = 0 
                     AND active = 1 
                     AND module_list LIKE "%'.$module_name.'%" '.$type_sql;

		$orderby = ' ORDER BY elementtype_action, sequence';

		$role_condition = "EXISTS(SELECT 1 
                                    FROM vtiger_user2role 
                                   WHERE vtiger_user2role.userid=? 
                                     AND vtiger_businessactions.acrole LIKE CONCAT('%', vtiger_user2role.roleid, '%')
                           )";
		$role_condition = $adb->convert2Sql($role_condition, array($userid));

		$user_condition = $adb->convert2sql('vtiger_crmentity.smownerid = ?', array($userid));

		require_once 'include/utils/GetUserGroups.php';
		$UserGroups = new GetUserGroups();
		$UserGroups->getAllUserGroups($userid);

		$group_condition = "";
		if (count($UserGroups->user_groups)>0) {
			$groups = implode(',', $UserGroups->user_groups);
			$group_condition = 'OR vtiger_crmentity.smownerid IN ('.$groups.') ';
		}

		$where_ext = "AND (".$role_condition." OR ".$user_condition. " ".$group_condition.")";
		$sql = $query.$where_ext.$orderby;

		$business_actions = $adb->query($sql);

		while ($row = $adb->fetch_array($business_actions)) {
			$accumulator[] = $row;
		}

		$strtemplate = new Vtiger_StringTemplate();
		if ($parameters) {
			foreach ($parameters as $key => $value) {
				$strtemplate->assign($key, $value);
			}
		}

		$result = array();
		if ($multitype) {
			foreach ($type as $t) {
				$result[$t] = array();
			}
		}

		foreach ($accumulator as $row) {

			/** Should the widget be shown */
			$return = cbEventHandler::do_filter('corebos.filter.link.show', array($row, $type, $parameters));
			if ($return == false) {
				continue;
			}

			//Get Vtiger_Link object
			$link = self::convertToObject($tabid, $row);

			if (!empty($row['handler_path']) && isInsideApplication($row['handler_path'])) {
				checkFileAccessForInclusion($row['handler_path']);
				require_once $row['handler_path'];
				$linkData = new Vtiger_LinkData($link, $current_user);
				$ignore = call_user_func(array($row['handler_class'], $row['handler']), $linkData);
				if (!$ignore) {
					self::log("Ignoring Link ... ".var_export($row, true));
					continue;
				}
			}

			if (isset($recordid) && $row['brmap'] > 0 && $row['mandatory'] == 0 && !coreBOS_Rule::evaluate($row['brmap'], $recordid)) {
				continue;
			}

			if ($parameters) {
				$link->linkurl = $strtemplate->merge($link->linkurl);
				$link->linkicon= $strtemplate->merge($link->linkicon);
			}

			if ($multitype) {
				$result[$link->linktype][] = $link;
			} else {
				$result[$link->linktype] = $link;
			}
		}

		return $result;
	}

	/**
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	// public function save_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle deleting related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//public function delete_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle getting related list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//public function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	/**
	 * Handle getting dependents list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//public function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }
}
