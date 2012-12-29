<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

class mItem extends Model {
	
	function Search($arguments = array()) {
		$defaultArguments = array(
			"columns" => array("*"),
			"filters" => array(),
			"offset" => 0,
			"limit" => NULL, //TODO: Needs to be validated
			"page" => NULL, //TODO: Needs to be validated
			"order" => array(
				array('column' => 'logorder', 'direction' => 'asc'),
				array('column' => 'logorder', 'direction' => 'desc')
			)
		);
		
		$defaultFilter = array(
			'column' => null,
			'flag' => null,
			'text' => null
		);
		
		$arguments = array_merge($defaultArguments, $arguments);
		
		$where = array();
		
		if (is_array($arguments['filters'])) {
			foreach ($arguments['filters'] as $i => $filter) {
				array_merge($defaultFilter, $filter);
						
				switch ($filter['column']) {
					case 'path':
						$pathParts = array(substr($filter['text'], 0, 255),
							strlen($filter['text']) > 255 ? substr($filter['text'], 255, 255) : '',
							strlen($filter['text']) > 255 * 2 ? substr($filter['text'], 255 * 2, 255) : '');
						
						switch ($filter['flag']) {
							case 'c':
								array_push($where, 'concat(i.path1, i.path2, i.path3) LIKE \'%' . DB::Escape($filter['text'], true) . '%\'');
								break;
							case 'e':
								$val = '\'%' . DB::Escape($filter['text'], true) . '\'';
								array_push($where, 'concat(i.path1, i.path2, i.path3) LIKE \'%' . DB::Escape($filter['text'], true) . '\'');
								break;
							case 's':
								if (strlen($filter['text']) < 255) {
									array_push($where, 'i.path1 LIKE \'' . DB::Escape($filter['text'], true) . '%\'');
								}
								elseif (strlen($filter['text']) == 255) {
									array_push($where, 'i.path1 = ' . DB::Quote($filter['text']));
								}
								elseif (strlen($filter['text']) < 255 * 2) {
									array_push($where, 'i.path1 = ' . DB::Quote($pathParts[0]) . ' AND i.path2 LIKE \'' . DB::Escape($pathParts[1], true) . '%\'');
								}
								elseif (strlen($filter['text']) == 255 * 2) {
									array_push($where, 'i.path1 = ' . DB::Quote($pathParts[0]) . ' AND i.path2 = ' . DB::Quote($pathParts[1]));
								}
								elseif (strlen($filter['text']) < 255 * 3) {
									array_push($where, 'i.path1 = ' . DB::Quote($pathParts[0]) . ' AND i.path2 = ' . DB::Quote($pathParts[1]) . ' AND i.path3 LIKE \'' . DB::Escape($pathParts[2], true) . '%\'');
								}
								else {
									array_push($where, 'i.path1 = ' . DB::Quote($pathParts[0])
										. ' AND i.path2 = ' . DB::Quote($pathParts[1])
										. ' AND i.path3 = ' . DB::Quote($pathParts[2]));
								}
								break;
							default:
								array_push($where, 'i.path1 = ' . DB::Quote($pathParts[0])
									. ' AND i.path2 = ' . DB::Quote($pathParts[1])
									. ' AND i.path3 = ' . DB::Quote($pathParts[2]));
						}
						
						break;
						
					case 'clientid':
					case 'job':
					case 'result':
					case 'detail':
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
						array_push($where, 'i.' . $filter['column'] . $op . $val);
						break;
					
					case 'starttime':
						$op = $filter['flag'] == 'g' ? ' > ' : ($filter['flag'] == 'l' ? ' < ' : ($filter['flag'] == 'i' ? ' != ' : ' = '));
						if (preg_match('/^[0-9]+$/', $filter['text'])) {
							array_push($where, 'i.' . $filter['column'] . $op . DB::Quote($filter['text']));
						}
						else {
							// TODO
						}
						break;
						
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
									array_push($where, '(i.' . $filter['column'] . ' > ' . DB::Quote($upperutime) . ' OR '
										.'i.' . $filter['column'] . ' < ' . DB::Quote($lowerutime) . ')');
								}
								else {
									array_push($where, '(i.' . $filter['column'] . ' >= ' . DB::Quote($lowerutime) . ' AND '
										.'i.' . $filter['column'] . ' <= ' . DB::Quote($upperutime) . ')');
								}
								
								$utime = FALSE;
							}
						}
						
						if ($utime !== FALSE) {
							array_push($where, 'i.' . $filter['column'] . $op . DB::Quote($utime));
						}
						break;
					
					case 'duration':
						$op = $filter['flag'] == 'g' ? ' > ' : ($filter['flag'] == 'l' ? ' < ' : ($filter['flag'] == 'i' ? ' != ' : ' = '));
						$parts = explode(':', $filter['text']);
						$seconds = 0;
						for ($i = count($parts) - 1; $i >= 0; $i--) {
							$seconds += $parts[$i] * pow(60, count($parts) - 1 - $i);
						}
						array_push($where, 'i.' . $filter['column'] . $op . DB::Quote($seconds));
						break;
						
					// Number
					case 'size1':
					case 'size2':
						preg_match('/^([0-9]+)(?:[ ]?([KMG])(?:[B]?))?$/i', $filter['text'], $matches);
						if (isset($matches[2])) {
							switch (strtoupper($matches[2])) {
								case 'K': $filter['text'] = floatval($filter['text']) * 1024; break;
								case 'M': $filter['text'] = floatval($filter['text']) * 1024 * 1024; break;
								case 'G': $filter['text'] = floatval($filter['text']) * 1024 * 1024 * 1024; break;
							}
						}
					case 'type':
					case 'logorder':
						$op = $filter['flag'] == 'g' ? ' > ' : ($filter['flag'] == 'l' ? ' < ' : ($filter['flag'] == 'i' ? ' != ' : ' = '));
						array_push($where, 'i.' . $filter['column'] . $op . DB::Quote($filter['text']));
						break;
				}
			}
		}
		
		// Parse the column list.
		$columns = "";
		if (is_string($arguments['columns']) && strtoupper($arguments['columns']) == 'COUNT') {
			$columns = "count(*) as itemcount";
		}
		else {
			for ($i = 0; $i < count($arguments['columns']); $i++) {
				if (V::Check('table:items', $tmp = $arguments['columns'][$i])) {
					$columns .= ( $i > 0 ? ", " : "") . "i." . $arguments['columns'][$i];
				}
			}
		}
		
		// Parse the ORDER BY list.
		$orderBySQL = "";
		for ($i = 0; $i < count($arguments['order']); $i++) {
			if (V::Check('table:items', $temp = $arguments['order'][$i]['column']) && (empty($arguments['order'][$i]['direction']) || preg_match("/^(asc|desc)$/i", $arguments['order'][$i]['direction']))) {
				$orderBySQL .= ($orderBySQL != "" ? ", " : "") . $arguments['order'][$i]['column'] . (!empty($arguments['order'][$i]['direction']) ? " " . $arguments['order'][$i]['direction'] : " asc");
			}
		}
		if ($orderBySQL != "") $orderBySQL = "\nORDER BY " . $orderBySQL . " ";
		
		$sql = 'SELECT ' . $columns . ' FROM items i ' . (count($where) > 0 ? " \n\nWHERE\n " . implode("\n AND ", $where) . "\n" : '');
			
		$sql .= $orderBySQL;
		
		// Do a paged query if a valid 'page' and 'limit' are set.
		if (is_int($arguments['page']) && $arguments['page'] > 0 && is_int($arguments['limit']) && $arguments['limit'] > 0) {
			return DB::QueryPaged($sql, intval($arguments['page']), intval($arguments['limit']));
		}
		
		// Otherwise, do a normal query.
		else {
			
			// Limit the results if a valid 'limit' is set.
			if (is_int($arguments['limit']) && $arguments['limit'] > 0) {
				$sql .= "\nLIMIT " . (is_int($arguments['offset']) && $arguments['offset'] >= 0 ? $arguments['offset'] : 0) . ", " . $arguments['limit'];
			}
			
			return DB::QueryAll($sql);
		}
	}
}
?>