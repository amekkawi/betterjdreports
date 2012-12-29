<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

class mTag extends Model {
	
	static function ByClients($clientids = null) {
		if (is_string($clientids)) {
			$clientids = array($clientids);
		}
		elseif (!is_array($clientids) || count($clientids) == 0) {
			// TODO: Use Controller::ShowError()
			echo "clients must be an array with at least one item in mTag::ByClients()"; exit;
		}
		
		$where = array();
		foreach ($clientids as $clientid) {
			array_push($where, 'clientid = ' . DB::Quote($clientid));
		}
		
		return DB::QueryAll('SELECT * FROM tags WHERE ' . implode(' OR ', $where) . ' ORDER BY clientid');
	}
	
	static function Add($clientids, $tag) {
		if (!is_array($clientids)) $clientids = array($clientids);
		
		if (is_string($error = V::Check('table:tags:tag', $tag, true))) return $error;
		
		$values = array();
		foreach ($clientids as $clientid) {
			if (is_string($error = V::Check('table:clients:clientid', $clientid, true))) return $error;
			array_push($values, '(' . DB::Quote($clientid) . ', ' . DB::Quote($tag) . ')');
		}
		
		$result = DB::Update('INSERT INTO tags (clientid, tag) VALUES ' . implode(',', $values) . ' ON DUPLICATE KEY UPDATE tag = tag');
		
		if (!is_string($result)) {
			Model::Load('history');
			foreach ($clientids as $clientid) {
				$history = mHistory::Add('Tag', $clientid, $tag, 'Add', 'Added \'' . $tag . '\' to ' . $clientid . '.', AC::GetLoggedInUser());
				if (is_string($history)) Controller::ShowError('db_error', array('errorin' => 'mTag::Add (History)', 'errormessage' => $history));
			}
		}
		
		return $result;
	}
	
	static function Remove($clientids, $tag) {
		if (!is_array($clientids)) $clientids = array($clientids);
		
		if (is_string($error = V::Check('table:tags:tag', $tag, true))) return $error;
	
		$where = array();
		foreach ($clientids as $clientid) {
			array_push($where, 'clientid = ' . DB::Quote($clientid));
		}
		
		$result = DB::Update('DELETE FROM tags WHERE tag = ' . DB::Quote($tag) . ' AND (' . implode(' OR ', $where) . ')');
		
		if (!is_string($result)) {
			Model::Load('history');
			foreach ($clientids as $clientid) {
				$history = mHistory::Add('Tag', $clientid, $tag, 'Remove', 'Removed \'' . $tag . '\' from ' . $clientid . '.', AC::GetLoggedInUser());
				if (is_string($history)) Controller::ShowError('db_error', array('errorin' => 'mTag::Remove (History)', 'errormessage' => $history));
			}
		}
		
		return $result;
	}
	
}