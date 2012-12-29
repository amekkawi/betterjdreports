<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

class mSessionstorage extends Model {
	static function Get($sessionid) {
		$record =& DB::QueryRow('SELECT * FROM session_storage WHERE sessionid = ' . DB::Quote($sessionid) . ' AND expiration > ' . DB::Quote(DB::Now()));
		
		if (is_string($record)) {
			return $record;
		}
		else if (is_null($record)) {
			return array();
		}
		else {
			return unserialize($record['data1'] . $record['data2'] . $record['data3'] . $record['data4']);
		}
	}
	
	static function Set($sessionid, $data, $expiration) {
		$serialized = serialize($data);
		$data1 = substr($serialized, 0, 255);
		$data2 = substr($serialized, 255, 255);
		$data3 = substr($serialized, 255*2, 255);
		$data4 = substr($serialized, 255*3, 255);
		$result = DB::Update('INSERT INTO session_storage (sessionid, data1, data2, data3, data4, expiration)'
			. ' VALUES ('.DB::Quote($sessionid).', '.DB::Quote($data1).', '.DB::Quote($data2).', '.DB::Quote($data3).', '.DB::Quote($data4).', '.DB::Quote($expiration).')'
			. ' ON DUPLICATE KEY UPDATE data1 = '.DB::Quote($data1).', data2 = '.DB::Quote($data2).', data3 = '.DB::Quote($data3).', data4 = '.DB::Quote($data4).', expiration = '.DB::Quote($expiration));
		return is_string($result) ? $result : TRUE;
	}
	
	static function RefreshExpiration($sessionid, $expiration) {
		$result = DB::Update('UPDATE session_storage SET expiration = ' . DB::Quote($expiration) . ' WHERE sessionid = ' . DB::Quote($sessionid));
		return is_string($result) ? $result : TRUE;
	}
}
?>