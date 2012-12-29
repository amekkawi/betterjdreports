<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

class ItemsController extends AppController {
	
	function search() {
		$this->pageTitle = 'File Search';
		$this->setData('selectednavi', $ref = 'Files');
		$this->includeJavascript('js/searchforms.js');
		
		V::Set($clientid, $_GET['clientid'], 'table:clients:clientid', FALSE);
		V::Set($path, $_GET['path'], 'filesearchpath', FALSE);
		V::Set($size, $_GET['size'], 'bytes', FALSE);
		V::Set($_GET['sizeflag'], $_GET['sizeflag'], 'string', '');
		
		$validBaseCriteria = TRUE;
		$errors = array();
		
		// Force changing the base criteria.
		if (isset($_GET['change'])) {
			$validBaseCriteria = FALSE;
			unset($_GET['change']);
		}
		
		// Handle invalid base criteria.
		if ($clientid === FALSE) {
			$validBaseCriteria = FALSE;
			if (isset($_GET['clientid']) && strlen($_GET['clientid']) > 0) array_push($errors, 'Invalid Client ID.');
		}
		
		if ($path === FALSE && $size === FALSE) {
			$validBaseCriteria = FALSE;
		}
			
		if ($path === FALSE && isset($_GET['path']) && strlen($_GET['path']) > 0) {
			$validBaseCriteria = FALSE;
			array_push($errors, 'Invalid path. Must be at least 4 characters long.');
		}
		
		if ($size === FALSE && isset($_GET['size']) && strlen($_GET['size']) > 0) {
			$validBaseCriteria = FALSE;
			array_push($errors, 'Invalid size. Must be an integer.');
		}
		
		// Check if the user has access to the client ID.
		if ($validBaseCriteria && !AC::InGroup('admin') && !AC::InGroup('viewall_reports')) {
			Model::Load('client');
			$access = mClient::GetAccess(AC::GetLoggedInUser(), $clientid);
			
			if (is_string($access)) {
				Controller::ShowError('db_error', array('errorin' => 'items/search', 'errormessage' => $validBaseCriteria));
			}
			
			$validBaseCriteria = count($access) == 1;
			
			if (!$validBaseCriteria) array_push($errors, 'Client ID does not exist or you do not have access to it.');
		}
		
		// Check that the results are not too large.
		if ($validBaseCriteria) {
			V::Set($_GET['page'], $_GET['page'], 'posinteger', 1);
			V::Set($_GET['sortcol'], $_GET['sortcol'], 'table:items', 'logorder');
			V::Set($_GET['sortdir'], $_GET['sortdir'], 'sortdir', preg_match('/^(duration|size2)$/', $_GET['sortcol']) ? 'desc' : 'asc');
			V::Set($_GET['filters'], $_GET['filters'], 'array', array());
			
			$countFilters = array( array('column' => 'clientid', 'flag' => 'x', 'text' => $clientid) );
			if ($path !== FALSE) array_push($countFilters, array('column' => 'path', 'flag' => 's', 'text' => $path));
			if ($size !== FALSE) array_push($countFilters, array('column' => 'size1', 'flag' => $_GET['sizeflag'], 'text' => $size));
			
			$count =& mItem::Search(array(
				'columns' => array('clientid'),
				'filters' => $countFilters,
				'offset' => 10000,
				'limit' => 1,
				'order' => array()
			));
			
			if (is_string($count)) {
				Controller::ShowError('db_error', array('errorin' => 'items/search', 'errormessage' => $count));
			}
			
			if (count($count) > 0) { //($count[0]['itemcount'] >= 10000) {
				$validBaseCriteria = FALSE;
				array_push($errors, 'Too many results (> 10,000). Refine your search by changing your criteria.');
			}
		}
			
		if (!$validBaseCriteria) {
			$this->setData('errors', $errors);
			$this->view = 'items/search-selectclient';
		}
		else {
			// Remove a filter.
			if (isset($_GET['removefilter'])) {
				if (V::Check('filesearch', $_GET['filters'][intval($_GET['removefilter'])])) {
					$parts = explode('-', $_GET['filters'][intval($_GET['removefilter'])]);
					$this->setData('initialSearchValues', $tmp = array(
						'column' => $parts[0],
						'flag' => $parts[1],
						'text' => $parts[2]
					));
				}
				unset($_GET['filters'][intval($_GET['removefilter'])]);
				unset($_GET['removefilter']);
			}
			
			// Add a new search filter.
			if (isset($_GET['search']) && is_array($_GET['search'])) {
				array_push($_GET['filters'], $_GET['search']['field'] . '-' . $_GET['search']['filter'] . '-' . $_GET['search']['text']);
				unset($_GET['search']);
			}
			
			// Validate the filters and reformat for mItem::Search().
			$searchFilters = array();
			foreach ($_GET['filters'] as $key => $filter) {
				if (V::Check('filesearch', $filter)) {
					$parts = explode('-', $filter, 3);
					array_push($searchFilters, array(
						'column' => $parts[0],
						'flag' => $parts[1],
						'text' => $parts[2]
					));
				}
				else {
					// Removed invalid filters.
					unset($_GET['filters'][$key]);
				}
			}
			
			// Clean up the indexes for the filters, in case some where removed.
			$_GET['filters'] = array_values($_GET['filters']);
			
			array_push($searchFilters, array('column' => 'clientid', 'flag' => 'x', 'text' => $clientid));
			if ($path !== FALSE) array_push($searchFilters, array('column' => 'path', 'flag' => 's', 'text' => $path));
			if ($size !== FALSE) array_push($searchFilters, array('column' => 'size1', 'flag' => $_GET['sizeflag'], 'text' => $size));
			
			$this->setData('path', $path);
			$this->setData('size', $size);
			
			//if (count($_GET['filters']) > 0) {
				$this->setData('items', $items =& mItem::Search(array(
					'filters' => array_merge($searchFilters, array(
						array('column' => 'clientid', 'flag' => 'x', 'text' => $clientid)
					)),
					'page' => intval($_GET['page']),
					'limit' => 100,
					'order' =>
						$_GET['sortcol'] == 'path1' ? array(
							array('column' => 'path1', 'direction' => $_GET['sortdir']),
							array('column' => 'path2', 'direction' => $_GET['sortdir']),
							array('column' => 'path3', 'direction' => $_GET['sortdir'])
						) :
						array(
							array('column' => $_GET['sortcol'], 'direction' => $_GET['sortdir']),
							array('column' => 'path1', 'direction' => $_GET['sortdir']),
							array('column' => 'path2', 'direction' => $_GET['sortdir']),
							array('column' => 'path3', 'direction' => $_GET['sortdir'])
						)
				)));
				
				if (is_string($items)) {
					Controller::ShowError('db_error', array('errorin' => 'reports/search', 'errormessage' => $items));
				}
			//}
		}
	}
	
}