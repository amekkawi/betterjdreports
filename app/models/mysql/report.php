<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

class mReport extends Model {
	
	private static function ProcessFilter(&$filter) {
		
		$defaultFilter = array(
			'column' => null,
			'flag' => null,
			'text' => null
		);
		
		$filter = array_merge($defaultFilter, $filter);
		
		switch ($filter['column']) {
			case 'clientid':
			case 'job':
			case 'result':
				$op = $filter['flag'] == 'c' || $filter['flag'] == 's' || $filter['flag'] == 'e' ? ' LIKE ' : ($filter['flag'] == 'i' ? ' != ' : ' = ');
				switch ($filter['flag']) {
					case 'c':
						$val = '\'%' . DB::Escape($filter['text'], true) . '%\'';
						break;
					case 's':
						$val = '\'' . DB::Escape($filter['text'], true) . '%\'';
						break;
					case 'e':
						$val = '\'%' . DB::Escape($filter['text'], true) . '\'';
						break;
					default:
						$val = DB::Quote($filter['text']);
				}
				
				return 'r.' . $filter['column'] . $op . $val;
				
			case 'reporttime':
				$op = $filter['flag'] == 'g' ? ' > ' : ($filter['flag'] == 'l' ? ' < ' : ($filter['flag'] == 'i' ? ' != ' : ' = '));
				if (preg_match('/^[0-9]+$/', $filter['text'])) {
					$utime = $filter['text'];
				}
				else {
					$date_time = explode(' ', $filter['text']);
					$date = explode('/', $date_time[0]);
					
					if (count($date_time) == 2) {
						$time = explode(':', $date_time[1]);
						$utime = mktime(intval($time[0]), intval($time[1]), intval($time[2]), intval($date[1]), intval($date[2]), intval($date[0]));
					}
					elseif ($op == ' > ') {
						$utime = mktime(23, 59, 59, intval($date[1]), intval($date[2]), intval($date[0]));
					}
					elseif ($op == ' < ') {
						$utime = mktime(0, 0, 0, intval($date[1]), intval($date[2]), intval($date[0]));
					}
					else {
						$lowerutime = mktime(0, 0, 0, intval($date[1]), intval($date[2]), intval($date[0]));
						$upperutime = mktime(23, 59, 59, intval($date[1]), intval($date[2]), intval($date[0]));
						
						if ($op == ' != ') {
							return '(r.' . $filter['column'] . ' > ' . DB::Quote($upperutime) . ' OR '
								. 'r.' . $filter['column'] . ' < ' . DB::Quote($lowerutime) . ')';
						}
						else {
							return '(r.' . $filter['column'] . ' >= ' . DB::Quote($lowerutime) . ' AND '
								. 'r.' . $filter['column'] . ' <= ' . DB::Quote($upperutime) . ')';
						}
					}
				}
				
				return 'r.' . $filter['column'] . $op . DB::Quote($utime);
				
			case 'duration':
				$op = $filter['flag'] == 'g' ? ' > ' : ($filter['flag'] == 'l' ? ' < ' : ($filter['flag'] == 'i' ? ' != ' : ' = '));
				$parts = explode(':', $filter['text']);
				$seconds = 0;
				for ($i = count($parts) - 1; $i >= 0; $i--) {
					$seconds += $parts[$i] * pow(60, count($parts) - 1 - $i);
				}
				return 'r.' . $filter['column'] . $op . DB::Quote($seconds);
				
			// Number
			case 'uploadsize':
				preg_match('/^([0-9]+)(?:[ ]?([KMG])(?:[B]?))?$/i', $filter['text'], $matches);
				if (isset($matches[2])) {
					switch (strtoupper($matches[2])) {
						case 'K': $filter['text'] = floatval($filter['text']) * 1024; break;
						case 'M': $filter['text'] = floatval($filter['text']) * 1024 * 1024; break;
						case 'G': $filter['text'] = floatval($filter['text']) * 1024 * 1024 * 1024; break;
					}
				}
			case 'changedfiles':
			case 'errors':
				$op = $filter['flag'] == 'g' ? ' > ' : ($filter['flag'] == 'l' ? ' < ' : ($filter['flag'] == 'i' ? ' != ' : ' = '));
				return 'r.' . $filter['column'] . $op . DB::Quote($filter['text']);
		}
		
		return NULL;
	}
	
	static function Search($arguments = array()) {
		$defaultArguments = array(
			"columns" => array("*"),
			"latest" => false,
			"filters" => array(),
			"userid" => NULL,
			"limit" => NULL, //TODO: Needs to be validated
			"page" => NULL, //TODO: Needs to be validated
			"order" => array(
				array('column' => 'reporttime', 'direction' => 'desc')
			)
		);
		
		$arguments = array_merge($defaultArguments, $arguments);
		
		$where = array();
		$innerWhere = array();
		
		// Filter out '*' jobs if showing latest.
		if ($arguments['latest']) {
			array_push($innerWhere, 'r.job != ' . DB::Quote('*'));
		}
		
		if (is_array($arguments['filters'])) {
			foreach ($arguments['filters'] as $filter) {
				
				// Only continue if the filter was processed.
				if (!is_null($filterStr = mReport::ProcessFilter($filter))) {
					
					// Allow for some special handling.
					switch ($filter['column']) {
						case 'clientid':
						case 'job':
							if ($arguments['latest']) {
								array_push($innerWhere, $filterStr);
							}
							else {
								array_push($where, $filterStr);
							}
							break;
							
						default:
							array_push($where, $filterStr);
					}
				}
			}
		}
		
		// Parse the column list.
		$columns = "";
		for ($i = 0; $i < count($arguments['columns']); $i++) {
			if (V::Check('table:reports', $tmp = $arguments['columns'][$i])) {
				$columns .= ( $i > 0 ? ", " : "") . "r." . $arguments['columns'][$i];
			}
		}
		
		// Parse the ORDER BY list.
		$orderBySQL = "";
		for ($i = 0; $i < count($arguments['order']); $i++) {
			if (V::Check('table:reports', $temp = $arguments['order'][$i]['column']) && (empty($arguments['order'][$i]['direction']) || preg_match("/^(asc|desc)$/i", $arguments['order'][$i]['direction']))) {
				$orderBySQL .= ($orderBySQL != "" ? ", " : "") . $arguments['order'][$i]['column'] . (!empty($arguments['order'][$i]['direction']) ? " " . $arguments['order'][$i]['direction'] : " asc");
			}
		}
		if ($orderBySQL != "") $orderBySQL = "\nORDER BY " . $orderBySQL . " ";
		
		$sql = 'SELECT ' . $columns . ' FROM';
		
		// Create a sub-query that only gets the most recent report for each client/job.
		if ($arguments['latest']) {
			$sql .= '( SELECT r.clientid, r.job, MAX(r.reporttime) as reporttime FROM reports r';
			
			if (!is_null($arguments['userid'])) {
				$sql .=
					 ' JOIN clients c on r.clientid = c.clientid'
					.' JOIN client_access ca on ca.clientid = c.clientid AND ca.userid = ' . DB::Quote($arguments['userid']);
			}
			
			$sql .= ' WHERE ' .  implode(' AND ', $innerWhere)
				. ' GROUP BY r.clientid, r.job'
				. ' ) as ri JOIN reports r ON ri.clientid = r.clientid AND ri.job = r.job AND ri.reporttime = r.reporttime';
		}
		
		// Normal search.
		else {
			$sql .= ' reports r ' . (is_null($arguments['userid']) ? '' :
					 ' JOIN clients c on r.clientid = c.clientid'
					.' JOIN client_access ca on ca.clientid = c.clientid AND ca.userid = ' . DB::Quote($arguments['userid']));
		}
		
		if (count($where) > 0) $sql .= ' WHERE ' . implode(' AND ', $where);
			
		$sql .= $orderBySQL;
		
		// Do a paged query if a valid 'page' and 'limit' are set.
		if (is_int($arguments['page']) && $arguments['page'] > 0 && is_int($arguments['limit']) && $arguments['limit'] > 0) {
			return DB::QueryPaged($sql, intval($arguments['page']), intval($arguments['limit']));
		}
		
		// Otherwise, do a normal query.
		else {
			
			// Limit the results if a valid 'limit' is set.
			if (is_int($arguments['limit']) && $arguments['limit'] > 0) {
				$sql .= "\nLIMIT 0, " . $arguments['limit'];
			}
			
			return DB::QueryAll($sql);
		}
	}
	
	static function Purge($clientid, $filters = array(), $userid = NULL) {
		
		Model::Load('history');
		
		$where = array();
		foreach ($filters as $filter) {
			// Only continue if the filter was processed.
			if (!is_null($filterStr = mReport::ProcessFilter($filter))) {
				array_push($where, $filterStr);
			}
		}
		
		array_push($where, 'r.clientid = ' . DB::Quote($clientid));
		
		$sql = 'DELETE FROM r USING reports r ';
		
		if (!is_null($userid)) {
			$sql .= ' JOIN clients c on r.clientid = c.clientid'
				. ' JOIN client_access ca on ca.clientid = c.clientid AND ca.purgereports = 1 AND ca.userid = ' . DB::Quote($userid);
		}
		
		$sql .= ' WHERE ' . implode(' AND ', $where);
		
		$result = DB::Update($sql);
		
		if (!is_string($result) && $result > 0) {
			$filterArr = array();
			foreach ($_GET['filters'] as $filter) {
				array_push($filterArr, AppController::FormatFilter($filter));
			}
			$history = mHistory::Add('Report', $clientid, '', 'Purge', 'Purged ' . number_format($result) . ' reports from '. $clientid . '. Filters: ' . implode('; ', $filterArr) . '.', AC::GetLoggedInUser());
			if (is_string($history)) Controller::ShowError('db_error', array('errorin' => 'mReport::Purge (History)', 'errormessage' => $history));
		}
		
		return $result;
	}
	
	static function JobTotals($clientids = array()) {
		if (is_string($clientids)) {
			$clientids = array($clientids);
		}
		elseif (!is_array($clientids)) {
			// TODO: Use Controller::ShowError()
			echo "clients must be an array in mTag::ByClients()"; exit;
		}
		
		$where = array();
		foreach ($clientids as $clientid) {
			array_push($where, 'clientid = ' . DB::Quote($clientid));
		}
		
		return DB::QueryAll('SELECT count(*) as jobcount, clientid, job FROM reports ' . (count($where) > 0 ? ' WHERE ' . implode(' OR ', $where) : '') . ' GROUP BY clientid, job ORDER BY clientid, job');
	}
}
?>