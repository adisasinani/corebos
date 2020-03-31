<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $app_strings, $mod_strings, $current_language, $currentModule, $theme;

include_once __DIR__ . '/core/ModTracker_Basic.php';

$reqid = vtlib_purify($_REQUEST['id']);
$atpoint = vtlib_purify($_REQUEST['atpoint']);

// Calculate the paging before hand
$prevAtPoint = ($atpoint + 1);
$nextAtPoint = ($atpoint - 1);

$trackrecord = false;
if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'history') {
	// Retrieve the track record at required point
	$trackrecord = ModTracker_Basic::getByCRMId($reqid, $atpoint);
	if ($trackrecord === false && $atpoint > 0) {
		$atpoint = $atpoint - 1;
		$prevAtPoint = $atpoint; // Signal no more previous
	}
} else {
	$trackrecord = ModTracker_Basic::getById($reqid);
}

if ($trackrecord === false || !$trackrecord->exists() || !$trackrecord->isViewPermitted()) {
	echo 'NOTRACKRECORD';
} else {
	if ($trackrecord) {
		$details = array();
		foreach ($trackrecord->getDetails() as $detail) {
			$details[] = array(
				'displayname' => $detail->getDisplayName(),
				'labelforpreval' => $detail->getDisplayLabelForPreValue(),
				'labelforpostval' => $detail->diff(),
				'labelhighlight' => $detail->diffHighlight(),
			);
		}
		echo json_encode(array(
			'trackrecord' => array(
				'raw' => $trackrecord,
				'displayname' => $trackrecord->getDisplayName(),
				'latest' => array(
					'modifiedbylabel' => $trackrecord->getModifiedByLabel(),
					'modifiedon' => $trackrecord->getModifiedOn(),
					'details' => $details,
				),
			),
			'atpoint' => $atpoint,
			'atpoint_prev' => $prevAtPoint,
			'atpoint_next' => $nextAtPoint,
		));
	}
}
?>