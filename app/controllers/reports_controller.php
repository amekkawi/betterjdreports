<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

class ReportsController extends AppController {
	
	function index() {
		HTML::Redirect('reports', 'search');
	}
	
	function latest() {
		Model::Load('report');
		Model::Load('user');
		
		$this->pageTitle = 'Latest Reports';
		$this->setData('selectednavi', $ref = 'Latest');
		$this->includeJavascript('js/searchforms.js');
		
		// Validated page.
		V::Set($_GET['page'], $_GET['page'], 'posinteger', 1);
		V::Set($_GET['sortcol'], $_GET['sortcol'], 'table:reports', 'reporttime');
		V::Set($_GET['sortdir'], $_GET['sortdir'], 'sortdir', preg_match('/^(reporttime|errors)$/', $_GET['sortcol']) ? 'desc' : 'asc');
		V::Set($_GET['filters'], $_GET['filters'], 'array', array());
		
		// Remove a filter.
		if (isset($_GET['removefilter'])) {
			if (V::Check('latestsearch', $_GET['filters'][intval($_GET['removefilter'])])) {
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
		
		// Validate the filters and reformat for mReport::Search().
		$searchFilters = array();
		foreach ($_GET['filters'] as $key => $filter) {
			if (V::Check('latestsearch', $filter)) {
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
		
		$this->setData('latest', $latest =& mReport::Search(array(
			'latest' => true,
			'filters' => $searchFilters,
			'userid' => AC::InGroup('admin') || AC::InGroup('viewall_reports') ? null : AC::GetLoggedInUser(),
			'page' => intval($_GET['page']),
			'limit' => 50,
			'order' => array(
				array('column' => $_GET['sortcol'], 'direction' => $_GET['sortdir']),
				array('column' => 'clientid', 'direction' => 'asc'),
				array('column' => 'job', 'direction' => 'asc')
			)
		)));
		
		if (is_string($latest)) {
			Controller::ShowError('db_error', array('errorin' => 'reports/latest', 'errormessage' => $latest));
		}
	}
	
	function search() {
		
		$this->setData('selectednavi', $ref = 'Reports');
		$this->includeJavascript('js/searchforms.js');
		$this->includeJavascript('js/jquery.button.js');
		$this->includeCSS('css/buttons.css');
		
		// Validated page.
		V::Set($_GET['page'], $_GET['page'], 'posinteger', 1);
		V::Set($_GET['sortcol'], $_GET['sortcol'], 'table:reports', 'reporttime');
		V::Set($_GET['sortdir'], $_GET['sortdir'], 'sortdir', preg_match('/^(reporttime|duration|uploadsize|changedfiles|errors)$/', $_GET['sortcol']) ? 'desc' : 'asc');
		V::Set($_GET['filters'], $_GET['filters'], 'stringarray', array());
		V::Set($_GET['purge'], $_GET['purge'], 'table:clients:clientid', null);
		
		// Remove a filter.
		if (isset($_GET['removefilter'])) {
			if (V::Check('reportssearch', $_GET['filters'][intval($_GET['removefilter'])])) {
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
		
		// Validate the filters and reformat for mReport::Search().
		$searchFilters = array();
		$foundPurge = FALSE;
		foreach ($_GET['filters'] as $key => $filter) {
			if (V::Check('reportssearch', $filter)) {
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
		
		if (!is_null($_GET['purge'])) {
			
			$this->pageTitle = 'Purge Reports for ' . $_GET['purge'];
			
			// Add an additional filter if purge is specified.
			array_push($searchFilters, array(
				'column' => 'clientid',
				'flag' => 'x',
				'text' => $_GET['purge']
			));
		}
		
		// Clean up the filter indexes (in case some were removed) and remove duplicates.
		$_GET['filters'] = array_keys(array_flip($_GET['filters']));
		
		$this->setData('results', $results =& mReport::Search(array(
			'filters' => $searchFilters,
			'userid' => AC::InGroup('admin') || AC::InGroup('viewall_reports') ? null : AC::GetLoggedInUser(),
			'page' => intval($_GET['page']),
			'limit' => 50,
			'order' => array(
				array('column' => $_GET['sortcol'], 'direction' => $_GET['sortdir']),
				array('column' => 'clientid', 'direction' => 'asc'),
				array('column' => 'job', 'direction' => 'asc')
			)
		)));
		
		if (is_string($results)) {
			Controller::ShowError('db_error', array('errorin' => 'reports/search', 'errormessage' => $results));
		}
	}
	
	function purge($clientid = NULL) {
		if (!V::Check('table:clients:clientid', $clientid)) {
			Controller::ShowError('missing_arguments', array('value' => $clientid, 'variable' => 'clientid'));
		}
		
		V::Set($_GET['filters'], $_GET['filters'], 'array', array());
		V::Set($_GET['verify'], $_GET['verify'], 'boolean');
		$this->setData('clientid', $clientid);
		
		$this->pageTitle = 'Purge Reports';
		
		// Validate the filters and reformat for mReport::Purge().
		$searchFilters = array();
		foreach ($_GET['filters'] as $key => $filter) {
			if (V::Check('reportssearch', $filter)) {
				$parts = explode('-', $filter, 3);
				array_push($searchFilters, array(
					'column' => $parts[0],
					'flag' => $parts[1],
					'text' => $parts[2]
				));
			}
		}
		
		if (isset($_GET['verify']) && $_GET['verify'] == '1') {
			$result = mReport::Purge($clientid, $searchFilters, AC::InGroup('admin') || AC::InGroup('viewall_reports') ? null : AC::GetLoggedInUser());
			
			if (is_string($result)) {
				Controller::ShowError('db_error', array('errorin' => 'reports/purge', 'errormessage' => $result));
			}
			
			HTML::Redirect('clients', 'show', array($clientid));
		}
	}
	
	function show($clientid = NULL, $job = NULL, $reporttime = NULL) {
		
		Model::Load('item');
		$this->pageTitle = 'Report Detail';
		
		$this->setData('selectednavi', $ref = 'Reports');
		$this->includeJavascript('js/searchforms.js');
		
		// Validated page.
		V::Set($_GET['page'], $_GET['page'], 'posinteger', 1);
		V::Set($_GET['sortcol'], $_GET['sortcol'], 'table:items', 'logorder');
		V::Set($_GET['sortdir'], $_GET['sortdir'], 'sortdir', preg_match('/^(duration|size2)$/', $_GET['sortcol']) ? 'desc' : 'asc');
		V::Set($_GET['filters'], $_GET['filters'], 'array', array());
		
		// Validate arguments.
		if (($msg = V::Check('table:clients:clientid', $clientid, TRUE)) !== TRUE) {
			Controller::ShowError('invalid_value', array('value' => $clientid, 'variable' => 'first argument', 'message' => $msg));
		}
		if (($msg = V::Check('table:reports:job', $job, TRUE)) !== TRUE) {
			Controller::ShowError('invalid_value', array('value' => $job, 'variable' => 'second argument', 'message' => $msg));
		}
		if (($msg = V::Check('table:reports:reporttime', $reporttime, TRUE)) !== TRUE) {
			Controller::ShowError('invalid_value', array('value' => $reporttime, 'variable' => 'third argument', 'message' => $msg));
		}
		
		// Remove a filter.
		if (isset($_GET['removefilter'])) {
			if (V::Check('itemssearch', $_GET['filters'][intval($_GET['removefilter'])])) {
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
			if (V::Check('itemssearch', $filter)) {
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
		
		$this->setData('report', $report =& mReport::Search(array(
			'filters' => array(
				array('column' => 'clientid', 'flag' => 'x', 'text' => $clientid),
				array('column' => 'job', 'flag' => 'x', 'text' => $job),
				array('column' => 'reporttime', 'flag' => 'x', 'text' => $reporttime)
			),
			'userid' => AC::InGroup('admin') || AC::InGroup('viewall_reports') ? null : AC::GetLoggedInUser()
			)));
		
		if (is_string($report)) {
			Controller::ShowError('db_error', array('errorin' => 'reports/search', 'errormessage' => $report));
		}
		elseif (count($report) > 0) {
			$this->setData('items', $items =& mItem::Search(array(
				'filters' => array_merge($searchFilters, array(
					array('column' => 'clientid', 'flag' => 'x', 'text' => $clientid),
					array('column' => 'job', 'flag' => 'x', 'text' => $job),
					array('column' => 'reporttime', 'flag' => 'x', 'text' => $reporttime)
				)),
				'page' => intval($_GET['page']),
				'limit' => 100,
				'order' =>
					$_GET['sortcol'] == 'path1' ? array(
						array('column' => 'path1', 'direction' => $_GET['sortdir']),
						array('column' => 'path2', 'direction' => $_GET['sortdir']),
						array('column' => 'path3', 'direction' => $_GET['sortdir']),
						array('column' => 'logorder', 'direction' => 'asc')
					) :
					array(
						array('column' => $_GET['sortcol'], 'direction' => $_GET['sortdir']),
						array('column' => 'logorder', 'direction' => 'asc')
					)
			)));
			
			if (is_string($items)) {
				Controller::ShowError('db_error', array('errorin' => 'reports/search', 'errormessage' => $items));
			}
		
		}
	}
	
}
?>