<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

class ClientsController extends AppController {
	
	function search() {
		Model::Load('tag');
		Model::Load('report');
		
		$this->setData('selectednavi', $ref = 'Clients');
		$this->includeJavascript('js/searchforms.js');
		
		// Validated page.
		V::Set($_GET['page'], $_GET['page'], 'posinteger', 1);
		V::Set($_GET['sortcol'], $_GET['sortcol'], 'clientssearchsort', 'clientid');
		V::Set($_GET['sortdir'], $_GET['sortdir'], 'sortdir', preg_match('/^(totalreports)$/', $_GET['sortcol']) ? 'desc' : 'asc');
		V::Set($_GET['filters'], $_GET['filters'], 'array', array());
		
		// Remove a filter.
		if (isset($_GET['removefilter'])) {
			if (V::Check('clientssearch', $_GET['filters'][intval($_GET['removefilter'])])) {
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
		
		// Validate the filters and reformat for mClient::Search().
		$searchFilters = array();
		foreach ($_GET['filters'] as $key => $filter) {
			if (V::Check('clientssearch', $filter)) {
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
		
		$clients =& mClient::Search(array(
			'filters' => $searchFilters,
			'userid' => AC::InGroup('admin') || AC::InGroup('viewall_clients') ? null : AC::GetLoggedInUser(),
			'page' => 1,
			'limit' => 100,
			'order' => array(
				array('column' => $_GET['sortcol'], 'direction' => $_GET['sortdir']),
				array('column' => 'clientid', 'direction' => 'asc')
			)
		));
		
		if (is_string($clients)) {
			Controller::ShowError('db_error', array('errorin' => 'clients/search', 'errormessage' => $clients));
		}
		else {
			
			// Get a list of the client IDs.
			$clientids = array();
			$clientref = array();
			foreach ($clients['records'] as $key => $client) {
				array_push($clientids, $client['clientid']);
				$clientref[$client['clientid']] =& $clients['records'][$key];
				
				$clients['records'][$key]['tags'] = array();
				$clients['records'][$key]['jobs'] = array();
			}
			
			if (count($clientids) > 0) {
				
				// Get tags grouped by client.
				$tags =& mTag::ByClients($clientids);
				if (is_string($tags)) Controller::ShowError('db_error', array('errorin' => 'clients/search', 'errormessage' => $tags));
				
				// Reformat the list of tags.
				foreach ($tags as $tag) {
					//array_push($tagsByClient[$tag['clientid']], $tag['tag']);
					array_push($clientref[$tag['clientid']]['tags'], $tag['tag']);
				}
				
				// Get job data grouped by client/job.
				$jobs =& mReport::JobTotals($clientids);
				if (is_string($jobs)) Controller::ShowError('db_error', array('errorin' => 'clients/search', 'errormessage' => $jobs));
				
				// Reformat the list of jobs.
				foreach ($jobs as $job) {
					//array_push($jobsByClient[$job['clientid']], array('job' => $job['job'], 'total' => $job['jobcount']));
					array_push($clientref[$job['clientid']]['jobs'], array('job' => $job['job'], 'total' => $job['jobcount']));
				}
			}
			
			$this->setData('clients', $clients);
		}
	}
	
	function show($clientid = NULL) {
		if (is_null($clientid)) {
			Controller::ShowError('missing_arguments', array('argumentNames' => array('clientid')));
		}
		
		Model::Load('tag');
		Model::Load('report');
		Model::Load('metadata');
		Model::Load('history');
		
		$this->setData('selectednavi', $ref = 'Clients');
		
		$client =& mClient::Search(array(
			'userid' => AC::InGroup('admin') || AC::InGroup('viewall_clients') ? null : AC::GetLoggedInUser(),
			'filters' => array(
				array('column' => 'clientid', 'flag' => 'x', 'text' => $clientid)
			)
		));
		
		if (is_string($client)) {
			Controller::ShowError('db_error', array('errorin' => 'clients/show (Client Search)', 'errormessage' => $client));
		}
		elseif (count($client) == 0) {
			Controller::ShowError('access_denied', array('errormessage' => 'No client was found using the ID specified or you do not have access to view it.'));
		}
		
		if (AC::InGroup('admin') || AC::InGroup('updateall_clients')) {
			$this->setData('update', $update = TRUE);
			$this->setData('delete', $delete = TRUE);
			$this->setData('purge', $purge = TRUE);
		}
		else {
			$access =& mClient::GetAccess(AC::GetLoggedInUser(), $client[0]['clientid']);
			if (is_string($client)) Controller::ShowError('db_error', array('errorin' => 'clients/show (GetAccess)', 'errormessage' => $access));
			
			$this->setData('update', $update = count($access) == 1 && $access[0]['update'] == TRUE);
			$this->setData('delete', $delete = count($access) == 1 && $access[0]['delete'] == TRUE);
			$this->setData('purge', $purge = count($access) == 1 && $access[0]['purgereports'] == TRUE);
		}
		
		$this->pageTitle = 'Client Detail: ' . $client[0]['clientid'];
		$this->setData('client', $client[0]);
		
		// Get tags grouped by client.
		$this->setData('tags', $tags =& mTag::ByClients(array($client[0]['clientid'])));
		if (is_string($tags)) Controller::ShowError('db_error', array('errorin' => 'clients/search (tags)', 'errormessage' => $tags));
		
		// Get job data grouped by client/job.
		$this->setData('jobs', $jobs =& mReport::JobTotals(array($client[0]['clientid'])));
		if (is_string($jobs)) Controller::ShowError('db_error', array('errorin' => 'clients/search (jobs)', 'errormessage' => $jobs));
		
		// Get metadata.
		$this->setData('metadata', $metadata =& mMetadata::Get($client[0]['clientid']));
		if (is_string($metadata)) Controller::ShowError('db_error', array('errorin' => 'clients/search (metadata)', 'errormessage' => $metadata));
		
		// Get history.
		$this->setData('history', $history =& mHistory::Search(array(
			'relatedto' => 'clients',
			'filters' => array(
				array('column' => 'id1', 'flag' => 'x', 'text' => $clientid)
			),
			'page' => 1,
			'limit' => 25
		)));
		if (is_string($history)) Controller::ShowError('db_error', array('errorin' => 'clients/search (history)', 'errormessage' => $history));
	}
}
?>