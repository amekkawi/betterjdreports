<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

class mUser extends Model {

	static function Create($userid, $name, $arguments = array()) {
		// Allow the arguments to be passed as an array.
		if (is_array($userid)) {
			$arguments = $userid;
		}
		else {
			$arguments['userid'] = $userid;
			$arguments['name'] = $name;
		}
		
		// Crypt the password if being set.
		if (isset($arguments['password']) && $arguments['password'] != '') {
			$arguments['password'] = crypt($arguments['password']);
		}
		
		$now = DB::Now();
		$arguments['created'] = $now;
		$arguments['modified'] = $now;
		
		return DB::ValidatedInsert('users', $arguments, array(
			'userid' => NULL,
			'name' => NULL,
			'password' => '',
			'email' => '',
			'timezone' => '',
			'created' => NULL,
			'modified' => NULL
		));
	}
	
	static function Update($userid, $arguments = array()) {
		$arguments['userid'] = $userid;
		$arguments['modified'] = DB::Now();
		
		// Created should never be updated.
		if (isset($arguments['created'])) unset($arguments['created']);
		
		// Crypt the password if being updated.
		if (isset($arguments['password']) && $arguments['password'] != '') {
			$arguments['password'] = crypt($arguments['password']);
		}
		
		return DB::ValidatedUpdate('users', array('userid'), $arguments);
	}
	
	static function Remove($userid) {
		$result = DB::Update('DELETE FROM users WHERE userid = ' . DB::Quote($userid));
		return (is_string($result) ? $result : TRUE);
	}
	
	static function Authorize($userid, $password) {
		$result =& DB::QueryRow('SELECT password FROM users WHERE userid = ' . DB::Quote($userid));
		
		if (is_string($result)) return $result;
		elseif (is_null($result)) return FALSE;
		else return crypt($password, $result['password']) === $result['password'];
	}
	
	static function Get($userid) {
		return DB::QueryAll('SELECT * FROM users WHERE userid = ' . DB::Quote($userid));
	}
	
	static function GetAll($order = array( 'name', 'userid' )) {
		$orderBySQL = "";
		
		for ($i = 0; $i < count($order); $i++) {
			if (is_string($order[$i]) && V::Check('table:users', $tmp = $order[$i])) {
				$orderBySQL .= ($orderBySQL != "" ? ", " : "") . $order[$i];
			}
			elseif (V::Check('table:users', $tmp = $order[$i]['column']) && preg_match('/^(asc|desc)$/i', $order[$i]['direction'])) {
				$orderBySQL .= ($orderBySQL != "" ? ", " : "") . $order[$i]['column'] . " " . $order[$i]['direction'];
			}
		}
		
		return DB::QueryAll('SELECT * FROM users ORDER BY ' . $orderBySQL);
	}
	
	static function GetGroupIDs($userid) {
		$result =& DB::QueryAll('SELECT groupid FROM user_groups WHERE userid = ' . DB::Quote($userid));
		
		if (is_array($result)) {
			$arr = array();
			for($i = 0; $i < count($result); $i++) {
				$arr[$result[$i]['groupid']] = true;
			}
			return $arr;
		}
		else {
			return $result;
		}
	}
}
?>