<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

class TagsController extends AppController {
	
	function add() {
		if (!V::Set($_GET['clientid'], $_GET['clientid'], 'table:clients:clientid')) {
			Controller::ShowError('invalid_value', array('value' => $_GET['clientid'], 'variable' => 'clientid'));
		}
		if (!V::Set($_GET['tag'], $_GET['tag'], 'table:tags:tag')) {
			Controller::ShowError('invalid_value', array('value' => $_GET['tag'], 'variable' => 'tag'));
		}
		
		if (!AC::InGroup('updateall_clients')) {
			Model::Load('client');
			$access =& mClient::GetAccess(AC::GetLoggedInUser(), $_GET['clientid']);
			if (is_string($access)) Controller::ShowError('db_error', array('errorin' => 'tags/add (access)', 'errormessage' => $access));
		}
		
		if (!AC::InGroup('updateall_clients') && (count($access) == 0 || $access[0]['update'] == FALSE)) {
			Controller::ShowError('access_denied', array('errormessage' => 'You do not have access to add tags to the client \''.$_GET['clientid'].'\'.'));
		}
		else {
			$result = mTag::Add($_GET['clientid'], $_GET['tag']);
			if (is_string($result)) Controller::ShowError('db_error', array('errorin' => 'tags/add (tabs)', 'errormessage' => $result));
			
			HTML::Redirect(isset($_GET['returnto']) ? $_GET['returnto'] : HTML::URL('clients', 'show', array($_GET['clientid'])));
		}
	}
	
	function remove() {
		if (!V::Set($_GET['clientid'], $_GET['clientid'], 'table:clients:clientid')) {
			Controller::ShowError('invalid_value', array('value' => $_GET['clientid'], 'variable' => 'clientid'));
		}
		if (!V::Set($_GET['tag'], $_GET['tag'], 'table:tags:tag')) {
			Controller::ShowError('invalid_value', array('value' => $_GET['tag'], 'variable' => 'tag'));
		}
		
		$this->pageTitle = 'Remove Tag';
		
		if (isset($_GET['verify'])) {
			if (!AC::InGroup('updateall_clients')) {
				Model::Load('client');
				$access =& mClient::GetAccess(AC::GetLoggedInUser(), $_GET['clientid']);
				if (is_string($access)) Controller::ShowError('db_error', array('errorin' => 'tags/add (access)', 'errormessage' => $access));
			}
			
			if (!AC::InGroup('updateall_clients') && (count($access) == 0 || $access[0]['update'] == FALSE)) {
				Controller::ShowError('access_denied', array('errormessage' => 'You do not have access to remove tags from the client \''.$_GET['clientid'].'\'.'));
			}
			else {
				$result = mTag::Remove($_GET['clientid'], $_GET['tag']);
				if (is_string($result)) Controller::ShowError('db_error', array('errorin' => 'tags/add', 'errormessage' => $result));
				
				HTML::Redirect(isset($_GET['returnto']) ? $_GET['returnto'] : HTML::URL('clients', 'show', array($_GET['clientid'])));
			}
		}
	}
	
}