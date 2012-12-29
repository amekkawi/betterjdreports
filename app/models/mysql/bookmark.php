<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

class mBookmark extends Model {
	
	static function Get($userid, $name = null) {
		$results =& DB::QueryAll('SELECT * FROM bookmarks WHERE userid = ' . DB::Quote($userid) . (!is_null($name) ? ' AND name = ' . DB::Quote($name) : ' ORDER BY name'));
		foreach ($results as $key => $result) {
			$results[$key]['arguments'] = unserialize($results[$key]['arguments']);
			$results[$key]['querystring'] = unserialize($results[$key]['querystring']);
		}
		return $results;
	}
	
	static function Create($userid, $name, $controller, $action, $arguments = array(), $querystring = array()) {
		Model::Load('history');
		
		$result = DB::ValidatedInsert('bookmarks', array(
			'userid' => AC::GetLoggedInUser(),
			'name' => $name,
			'controller' => $controller,
			'action' => $action,
			'arguments' => serialize($arguments),
			'querystring' => serialize($querystring)
		));
		
		if (!is_string($result)) {
			$history = mHistory::Add('Bookmarks', $userid, $name, 'Add', 'Added \'' . $name . '\'', AC::GetLoggedInUser());
			if (is_string($history)) Controller::ShowError('db_error', array('errorin' => 'mBookmark::Create (History)', 'errormessage' => $history));
		}
		
		return $result;
	}
	
	static function Remove($userid, $name) {
		Model::Load('history');
		
		$result = DB::Update('DELETE FROM bookmarks WHERE userid = ' . DB::Quote($userid) . ' AND name = ' . DB::Quote($name));
		
		if (!is_string($result) && $result > 0) {
			$history = mHistory::Add('Bookmark', array($userid, $name), 'Remove', AC::GetLoggedInUser());
			if (is_string($history)) Controller::ShowError('db_error', array('errorin' => 'mBookmark::Remove (History)', 'errormessage' => $history));
		}
		
		return $result;
	}
	
}