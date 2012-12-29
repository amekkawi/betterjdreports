<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

class mClient extends Model {
	
	static function Update($clientid, $arguments = array()) {
		$arguments['clientid'] = $clientid;
		$result = DB::ValidatedUpdate('clients', array('clientid'), $arguments);
		
		if (!is_string($result)) {
			$history = mHistory::Add('Client', $clientid, '', 'Update', 'Updated \'' . $name . '\'', AC::GetLoggedInUser());
			if (is_string($history)) Controller::ShowError('db_error', array('errorin' => 'mClient::Update (History)', 'errormessage' => $history));
		}
		
		return $result;
	}
	
	static function Search($arguments = array()) {
		$defaultArguments = array(
			"columns" => array("*"),
			"filters" => array(),
			"userid" => NULL,
			"limit" => NULL, //TODO: Needs to be validated
			"page" => NULL, //TODO: Needs to be validated
			"order" => array(
				array('column' => 'clientid', 'direction' => 'asc')
			)
		);
		
		$defaultFilter = array(
			'column' => null,
			'flag' => null,
			'text' => null
		);
		
		$arguments = array_merge($defaultArguments, $arguments);
		
		$where = array();
		$having = array();
		$inner_joins = array();
		$left_joins = array();
		
		if (is_array($arguments['filters'])) {
			foreach ($arguments['filters'] as $i => $filter) {
				array_merge($defaultFilter, $filter);
						
				switch ($filter['column']) {
					case 'clientid':
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
						array_push($where, 'c.' . $filter['column'] . $op . $val);
						break;
					
					case 'tags':
						if ($filter['text'] == '') {
							array_push($where, 'te' . count($left_joins) . '.tag IS ' . ($filter['flag'] == 'i' ? ' NOT ' : '') . ' NULL');
							$left_joins[count($left_joins)] = ' LEFT JOIN tags te' . count($left_joins) . ' ON c.clientid = te' . count($left_joins) . '.clientid';
						}
						elseif ($filter['flag'] == 'i') {
							array_push($where, 'te' . count($left_joins) . '.tag IS NULL');
							$left_joins[count($left_joins)] = ' LEFT JOIN tags te' . count($left_joins) . ' ON c.clientid = te' . count($left_joins) . '.clientid AND te' . count($left_joins) . '.tag = ' . DB::Quote($filter['text']);
						}
						else {
							$inner_joins[count($inner_joins)] = ' JOIN tags ti' . count($inner_joins) . ' ON c.clientid = ti' . count($inner_joins) . '.clientid AND ti' . count($inner_joins) . '.tag = ' . DB::Quote($filter['text']);
						}
						break;
						
					// Number
					case 'totalreports':
						$op = $filter['flag'] == 'g' ? ' > ' : ($filter['flag'] == 'l' ? ' < ' : ($filter['flag'] == 'i' ? ' != ' : ' = '));
						array_push($having, $filter['column'] . $op . DB::Quote($filter['text']));
						break;
				}
			}
		}
		
		// Parse the column list.
		$columns = '';
		for ($i = 0; $i < count($arguments['columns']); $i++) {
			if (V::Check('table:clients', $tmp = $arguments['columns'][$i])) {
				$columns .= ( $i > 0 ? ", " : '') . "c." . $arguments['columns'][$i];
			}
		}
		
		$sql = 'SELECT count(r.clientid) as totalreports, ' . $columns . "\n".' FROM (clients c '.implode("\n".' ', $inner_joins).') '."\n".' LEFT JOIN reports r ON c.clientid = r.clientid';
		
		if (count($left_joins)) {
			$sql .= "\n\n".implode("\n".' ', $left_joins);
		}
		
		if (!is_null($arguments['userid'])) {
			$sql .= "\n\n".' JOIN client_access ca on ca.clientid = c.clientid AND ca.userid = ' . DB::Quote($arguments['userid']);
		}
		
		if (count($where) > 0) {
			$sql .= "\n\n".' WHERE ' . implode("\n".' AND ', $where);
		}
		
		$sql .= "\n\n".' GROUP BY c.clientid ';
		
		if (count($having) > 0) {
			$sql .= "\n".' HAVING ' . implode("\n".' AND ', $having);
		}
		
		// Parse the ORDER BY list.
		$order = array();
		for ($i = 0; $i < count($arguments['order']); $i++) {
			if (V::Check('clientssearchsort', $temp = $arguments['order'][$i]['column']) && (empty($arguments['order'][$i]['direction']) || preg_match("/^(asc|desc)$/i", $arguments['order'][$i]['direction']))) {
				array_push($order, $arguments['order'][$i]['column'] . (!empty($arguments['order'][$i]['direction']) ? " " . $arguments['order'][$i]['direction'] : " asc"));
			}
		}
		if (count($order) > 0) {
			$sql .= "\n".' ORDER BY ' . implode(', ', $order);
		}
		
		// Do a paged query if a valid 'page' and 'limit' are set.
		if (is_int($arguments['page']) && $arguments['page'] > 0 && is_int($arguments['limit']) && $arguments['limit'] > 0) {
			return DB::QueryPaged($sql, intval($arguments['page']), intval($arguments['limit']));
		}
		
		// Otherwise, do a normal query.
		else {
			
			// Limit the results if a valid 'limit' is set.
			if (is_int($arguments['limit']) && $arguments['limit'] > 0) {
				$sql .= 'LIMIT 0, ' . $arguments['limit'];
			}
			
			return DB::QueryAll($sql);
		}
	}
	
	static function &GetAccess($userid, $clientids = NULL) {
		
		if (is_string($clientids)) $clientids = array($clientids);
		
		$where = array();
		if (is_array($clientids)) {
			foreach ($clientids as $clientid) {
				array_push($where, 'clientid = ' . DB::Quote($clientid));
			}
		}
		
		return DB::QueryAll('SELECT * FROM client_access WHERE userid = ' . DB::Quote($userid) . (count($where) > 0 ? ' AND (' . implode(' OR ', $where) . ')' : ''));
	}
	
}
?>