<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************/

class addlinksforcalendar extends cbupdaterWorker {
	public function applyChange() {
		global $adb;
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$query=$adb->query("select tabid from vtiger_tab where name='Calendar4You'");
			$tabid=$adb->query_result($query, 0, 0);
			$link = Vtiger_Link::addlink($tabid, 'HEADERSCRIPT', 'Calendar4You_HeaderScript3', 'modules/Calendar4You/fullcalendar/lib/moment.min.js', '', 1, null, true);
			$link2 = Vtiger_Link::addlink($tabid, 'HEADERCSS', 'Calendar4You_HeaderStyle2', 'modules/Calendar4You/fullcalendar/themes/cupertino/jquery-ui.min.css', '', 1, null, true);
			$adb->query("UPDATE vtiger_links SET sequence = '2' WHERE  linkurl='modules/Calendar4You/fullcalendar/fullcalendar.js'");
			$adb->query("UPDATE vtiger_links SET sequence = '3' WHERE  linkurl='modules/Calendar4You/Calendar4You.js'");
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}

?>
