<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

$GLOBALS['DBLOG'] = array();

class DB extends DBCore {
	
	static function Query($sql) {
		$start = microtime(true);
		$result =& DBCore::Query($sql);
		if (Config::Get('debugqueries')) {
			array_push($GLOBALS['DBLOG'], array(
				'type' => 'Query',
				'sql' => $sql,
				'time' => microtime(true) - $start,
				'records' => !is_string($result) ? DB::RowCount($result) : null,
				'error' => is_string($result) ? $result : null,
				'errorinfo' => is_string($result) ? $GLOBALS['DebugInfo'] : null
			));
		}
		return $result;
	}
	
	static function &QueryAll($sql) {
		$start = microtime(true);
		$result =& DBCore::QueryAll($sql);
		if (Config::Get('debugqueries')) {
			array_push($GLOBALS['DBLOG'], array(
				'type' => 'QueryAll',
				'sql' => $sql,
				'time' => microtime(true) - $start,
				'records' => !is_string($result) ? count($result) : null,
				'error' => is_string($result) ? $result : null,
				'errorinfo' => is_string($result) ? $GLOBALS['DebugInfo'] : null
			));
		}
		return $result;
	}
	
	static function QueryRow($sql) {
		$start = microtime(true);
		$result =& DBCore::QueryRow($sql);
		if (Config::Get('debugqueries')) {
			array_push($GLOBALS['DBLOG'], array(
				'type' => 'QueryRow',
				'sql' => $sql,
				'time' => microtime(true) - $start,
				'records' => !is_string($result) ? (is_null($result) ? '0' : '1') : null,
				'error' => is_string($result) ? $result : null,
				'errorinfo' => is_string($result) ? $GLOBALS['DebugInfo'] : null
			));
		}
		return $result;
	}
	
	static function Update($sql) {
		$start = microtime(true);
		$result = DBCore::Update($sql);
		if (Config::Get('debugqueries')) {
		array_push($GLOBALS['DBLOG'], array(
			'type' => 'Update',
			'sql' => $sql,
			'time' => microtime(true) - $start,
			'affected' => !is_string($result) ? $result : null,
			'error' => is_string($result) ? $result : null,
			'errorinfo' => is_string($result) ? $GLOBALS['DebugInfo'] : null
		));
		}
		return $result;
	}
	
}

?>