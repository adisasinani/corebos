/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

function verifyConvertLeadData(form) {
	var convertForm=document.ConvertLead;
	var no_ele=convertForm.length;

	if ((form.select_account!=null)&&(form.select_contact!=null)) {
		if (!(form.select_account.checked || form.select_contact.checked)) {
			ldsPrompt.show(alert_arr['ERROR'], alert_arr['ERR_SELECT_EITHER']);
			return false;
		}
	} else if (form.select_account!=null) {
		if (!form.select_account.checked) {
			ldsPrompt.show(alert_arr['ERROR'], alert_arr['ERR_SELECT_ACCOUNT']);
			return false;
		}
	} else if (form.select_contact!=null) {
		if (!form.select_contact.checked) {
			ldsPrompt.show(alert_arr['ERROR'], alert_arr['ERR_SELECT_CONTACT']);
			return false;
		}
	}
	var i=0;
	if (form.select_account!=null && form.select_account.checked) {
		for (i=0; i<no_ele; i++) {
			if ((convertForm[i].getAttribute('module')=='Accounts') && (convertForm[i].getAttribute('record')=='true')) {
				if (convertForm[i].value=='') {
					ldsPrompt.show(alert_arr['ERROR'], alert_arr['ERR_MANDATORY_FIELD_VALUE']);
					return false;
				}
			}
		}
	}
	if (form.select_potential!=null && form.select_potential.checked) {
		for (i=0; i<no_ele; i++) {
			if ((convertForm[i].getAttribute('module')=='Potentials') && (convertForm[i].getAttribute('record')=='true')) {
				if (convertForm[i].value=='') {
					ldsPrompt.show(alert_arr['ERROR'], alert_arr['ERR_MANDATORY_FIELD_VALUE']);
					return false;
				}
			}
		}
		if (form.jscal_field_closedate!=null && form.jscal_field_closedate.value!='') {
			if (!dateValidate('closingdate', alert_arr['LBL_CLOSE_DATE'], 'date')) {
				return false;
			}
		}
		var val = form.amount.value;
		if (typeof userCurrencySeparator != 'undefined' && userCurrencySeparator != '') {
			while (val.indexOf(userCurrencySeparator) != -1) {
				val = val.replace(userCurrencySeparator, '');
			}
		}
		if (typeof userDecimalSeparator != 'undefined' && userDecimalSeparator != '') {
			if (val.indexOf(userDecimalSeparator) != -1) {
				val = val.replace(userDecimalSeparator, '.');
			}
		}
		if (form.amount.value!=null && isNaN(val)) {
			ldsPrompt.show(alert_arr['ERROR'], alert_arr['ERR_POTENTIAL_AMOUNT']);
			return false;
		}
	}
	if (form.select_contact!=null && form.select_contact.checked) {
		for (i=0; i<no_ele; i++) {
			if ((convertForm[i].getAttribute('module')=='Contacts') && (convertForm[i].getAttribute('record')=='true')) {
				if (convertForm[i].value=='') {
					ldsPrompt.show(alert_arr['ERROR'], alert_arr['ERR_MANDATORY_FIELD_VALUE']);
					return false;
				}
			}
		}
		if (form.email.value!='') {
			if (!patternValidateObject(form.email, alert_arr['LBL_EMAIL'], 'EMAIL')) {
				return false;
			}
		}
	}

	if (document.getElementById('transfertoacc') && document.getElementById('transfertoacc').checked && !form.select_account.checked) {
		ldsPrompt.show(alert_arr['ERROR'], alert_arr['ERR_TRANSFER_TO_ACC']);
		return false;
	}
	if (document.getElementById('transfertocon') && document.getElementById('transfertocon').checked && !form.select_contact.checked) {
		ldsPrompt.show(alert_arr['ERROR'], alert_arr['ERR_TRANSFER_TO_CON']);
		return false;
	}
	return true;
}

function togglePotFields(form) {
	if (form.createpotential.checked == true) {
		form.potential_name.disabled = true;
		form.closedate.disabled = true;
		form.potential_amount.disabled = true;
		form.potential_sales_stage.disabled = true;
	} else {
		form.potential_name.disabled = false;
		form.closedate.disabled = false;
		form.potential_amount.disabled = false;
		form.potential_sales_stage.disabled = false;
		form.potential_sales_stage.value='';
	}
}

function set_return(product_id, product_name) {
	if (document.getElementById('from_link').value != '') {
		window.opener.document.QcEditView.parent_name.value = product_name;
		window.opener.document.QcEditView.parent_id.value = product_id;
	} else {
		window.opener.document.EditView.parent_name.value = product_name;
		window.opener.document.EditView.parent_id.value = product_id;
	}
}

function set_return_todo(product_id, product_name) {
	if (document.getElementById('from_link').value != '') {
		window.opener.document.QcEditView.task_parent_name.value = product_name;
		window.opener.document.QcEditView.task_parent_id.value = product_id;
	} else {
		window.opener.document.createTodo.task_parent_name.value = product_name;
		window.opener.document.createTodo.task_parent_id.value = product_id;
	}
}

function set_return_specific(product_id, product_name) {
	//Used for DetailView, Removed 'EditView' formname hardcoding
	var fldName = getOpenerObj('lead_name');
	var fldId = getOpenerObj('lead_id');
	fldName.value = product_name;
	fldId.value = product_id;
}

function add_data_to_relatedlist(entity_id, recordid) {
	opener.document.location.href='index.php?module=Emails&action=updateRelations&destination_module=leads&entityid='+entity_id+'&parentid='+recordid;
}

function searchMapLocation(addressType) {
	var mapParameter = '';
	if (addressType == 'Main') {
		if (fieldname.indexOf('lane') > -1) {
			if (document.getElementById('dtlview_lane')) {
				mapParameter = document.getElementById('dtlview_lane').innerHTML+' ';
			}
		}
		if (fieldname.indexOf('city') > -1) {
			if (document.getElementById('dtlview_city')) {
				mapParameter = mapParameter + document.getElementById('dtlview_city').innerHTML+' ';
			}
		}
		if (fieldname.indexOf('state') > -1) {
			if (document.getElementById('dtlview_state')) {
				mapParameter = mapParameter + document.getElementById('dtlview_state').innerHTML+' ';
			}
		}
		if (fieldname.indexOf('country') > -1) {
			if (document.getElementById('dtlview_country')) {
				mapParameter = mapParameter + document.getElementById('dtlview_country').innerHTML+' ';
			}
		}
		if (fieldname.indexOf('code') > -1) {
			if (document.getElementById('dtlview_code')) {
				mapParameter = mapParameter + document.getElementById('dtlview_code').innerHTML+' ';
			}
		}
	}
	mapParameter = removeHTMLFormatting(mapParameter);
	window.open('http://maps.google.com/maps?q='+mapParameter, 'goolemap', 'height=450,width=700,resizable=no,titlebar,location,top=200,left=250');
}

function selectTransferTo(module) {
	if (module=='Accounts') {
		if (document.getElementById('transfertoacc').checked) {
			document.getElementById('account_block').style.display='block';
			document.getElementById('select_account').checked='checked';
		}
	}
	if (module=='Contacts') {
		if (document.getElementById('transfertocon').checked) {
			document.getElementById('contact_block').style.display='block';
			document.getElementById('select_contact').checked='checked';
		}
	}
}

function setCookie(c_name, value, exdays) {
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? '' : '; expires='+exdate.toUTCString());
	document.cookie=c_name + '=' + c_value;
}

function getCookie(c_name) {
	var c_value = document.cookie;
	var c_start = c_value.indexOf(' ' + c_name + '=');
	if (c_start == -1) {
		c_start = c_value.indexOf(c_name + '=');
	}
	if (c_start == -1) {
		c_value = null;
	} else {
		c_start = c_value.indexOf('=', c_start) + 1;
		var c_end = c_value.indexOf(';', c_start);
		if (c_end == -1) {
			c_end = c_value.length;
		}
		c_value = unescape(c_value.substring(c_start, c_end));
	}
	return c_value;
}

function toggle_converted() {
	if (getCookie('LeadConv') == 'true') {
		setCookie('LeadConv', 'false');
	} else {
		setCookie('LeadConv', 'true');
	}
	document.location.reload(true);
}

function LeadssetValueFromCapture(recordid, value, target_fieldname) {
	if (target_fieldname=='accountname') {
		document.getElementById('accountname').value = value;
	}
}

function callConvertLeadDiv(id) {
	VtigerJS_DialogBox.block();
	var params = `&${csrfMagicName}=${csrfMagicToken}`;
	fetch(
		'index.php?module=Leads&action=LeadsAjax&file=ConvertLead&record='+id,
		{
			method: 'post',
			headers: {
				'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			credentials: 'same-origin',
			body: params
		}
	).then(response => response.text().then(response => {
		VtigerJS_DialogBox.unblock();
		ldsModal.show('modalTitle', response, 'medium', "document.getElementById('ConvertLead').action.value='LeadConvertToEntities'; if (verifyConvertLeadData(ConvertLead)) {VtigerJS_DialogBox.block();document.getElementById('ConvertLead').submit();}");
		ldsModal.updateTitle(document.getElementById('convertLeadHeaderTitle').innerHTML);
		vtlib_executeJavascriptInElement(document.getElementById('ConvertLead'));
	}));
}
