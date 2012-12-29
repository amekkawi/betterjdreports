<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

class mHistory extends Model {
	
	static function Add($target, $id1, $id2, $action, $description, $userid = NULL) {
		return DB::ValidatedInsert('history', array(
			'target' => $target,
			'id1' => $id1,
			'id2' => $id2,
			'eventtime' => time(),
			'action' => $action,
			'description' => $description,
			'userid' => $userid
		));
	}
	
	static function Search($arguments = array()) {
		$defaultArguments = array(
			"relatedto" => null,
			"columns" => array("*"),
			"filters" => array(),
			"limit" => NULL, //TODO: Needs to be validated
			"page" => NULL, //TODO: Needs to be validated
			"order" => array(
				array('column' => 'eventtime', 'direction' => 'desc')
			)
		);
		
		$defaultFilter = array(
			'column' => null,
			'flag' => null,
			'text' => null
		);
		
		$arguments = array_merge($defaultArguments, $arguments);
		
		$where = array();
		
		if (!is_null($arguments['relatedto'])) {
			switch ($arguments['relatedto']) {
				case 'clients':
					array_push($where, '(h.target = ' . DB::Quote('Client') . ' OR h.target = ' . DB::Quote('Client Access') . ' OR h.target = ' . DB::Quote('Tag') . ' OR h.target = ' . DB::Quote('Alert') . ' OR h.target = ' . DB::Quote('Report') . ')');
					break;
			}
		}
		
		if (is_array($arguments['filters'])) {
			foreach ($arguments['filters'] as $i => $filter) {
				array_merge($defaultFilter, $filter);
						
				switch ($filter['column']) {
					case 'target':
					case 'id1':
					case 'id2':
					case 'action':
					case 'userid':
						$op = $filter['flag'] == 'i' ? ' != ' : ' = ';
						array_push($where, 'h.' . $filter['column'] . $op . DB::Quote($filter['text']));
						break;
						
					case 'eventtime':
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
									array_push($where, '(r.' . $filter['column'] . ' > ' . DB::Quote($upperutime) . ' OR '
										.'r.' . $filter['column'] . ' < ' . DB::Quote($lowerutime) . ')');
								}
								else {
									array_push($where, '(r.' . $filter['column'] . ' >= ' . DB::Quote($lowerutime) . ' AND '
										.'r.' . $filter['column'] . ' <= ' . DB::Quote($upperutime) . ')');
								}
								
								$utime = FALSE;
							}
						}
						
						if ($utime !== FALSE) {
							array_push($where, 'r.' . $filter['column'] . $op . DB::Quote($utime));
						}
						break;
				}
			}
		}
		
		// Parse the column list.
		$columns = '';
		for ($i = 0; $i < count($arguments['columns']); $i++) {
			if (V::Check('table:history', $tmp = $arguments['columns'][$i])) {
				$columns .= ( $i > 0 ? ", " : '') . "h." . $arguments['columns'][$i];
			}
		}
		
		$sql = 'SELECT ' . $columns . ' FROM history h '."\n";
		
		if (count($where) > 0) $sql .= "\n\n".' WHERE ' . implode("\n".' AND ', $where);
		
		// Parse the ORDER BY list.
		$order = array();
		for ($i = 0; $i < count($arguments['order']); $i++) {
			if (V::Check('table:history', $temp = $arguments['order'][$i]['column']) && (empty($arguments['order'][$i]['direction']) || preg_match("/^(asc|desc)$/i", $arguments['order'][$i]['direction']))) {
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
	
}