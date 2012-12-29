<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

class MetadataController extends AppController {
	
	protected $loadControllerModel = false;
	
	protected $modelNames = array('metadata');
	
	function add($clientid = NULL) {
		if (!V::Set($clientid, $clientid, 'table:metadata:clientid')) {
			Controller::ShowError('missing_arguments', array('argumentNames' => array('clientid')));
		}
		
		$this->pageTitle = 'Add Metadata';
		
		V::Set($type, $_GET['type'], 'table:metadata:type', NULL);
		V::Set($data, $_GET['data'], 'table:metadata:data', NULL);
		
		if (is_null($type) || is_null($data)) {
			$this->setData('clientid', $clientid);
		}
		else {
			if (!AC::InGroup('updateall_clients')) {
				Model::Load('client');
				$access =& mClient::GetAccess(AC::GetLoggedInUser(), $clientid);
				if (is_string($access)) Controller::ShowError('db_error', array('errorin' => 'metadata/add (access)', 'errormessage' => $access));
				
				if (count($access) == 0 || $access[0]['update'] == FALSE) {
					Controller::ShowError('access_denied', array('errormessage' => 'You do not have access to add metadata to the client \''.$clientid.'\'.'));
				}
			}
			
			$result = mMetadata::Add($clientid, $type, $data);
			if (is_string($result)) Controller::ShowError('db_error', array('errorin' => 'metadata/add (metadata)', 'errormessage' => $result));
			
			HTML::Redirect(isset($_GET['returnto']) ? $_GET['returnto'] : HTML::URL('clients', 'show', array($clientid)));
		}
	}
	
	function update($clientid = NULL, $oldtype = NULL) {
		if (!V::Set($clientid, $clientid, 'table:metadata:clientid') || !V::Set($oldtype, $oldtype, 'table:metadata:type')) {
			Controller::ShowError('missing_arguments');
		}
		
		$this->pageTitle = 'Update Metadata';
		
		V::Set($type, $_GET['type'], 'table:metadata:type', NULL);
		V::Set($data, $_GET['data'], 'table:metadata:data', NULL);
		
		if (is_null($_GET['type'])) {
			$_GET['type'] = $oldtype;
		}
		
		$this->view = 'metadata/add';
		
		if (!AC::InGroup('updateall_clients')) {
			Model::Load('client');
			$access =& mClient::GetAccess(AC::GetLoggedInUser(), $clientid);
			if (is_string($access)) Controller::ShowError('db_error', array('errorin' => 'metadata/update (access)', 'errormessage' => $access));
			
			if (count($access) == 0 || $access[0]['update'] == FALSE) {
				Controller::ShowError('access_denied', array('errormessage' => 'You do not have access to update metadata for the client \''.$clientid.'\'.'));
			}
		}
		
		$this->setData('clientid', $clientid);
		$this->setData('type', $type);
	}
}
?>