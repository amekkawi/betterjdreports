<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

class mMetadata extends Model {
	
	static function &Get($clientid = null) {
		return DB::QueryAll('SELECT * FROM metadata ' . (!is_null($clientid) ? ' WHERE clientid  = ' . DB::Quote($clientid) : '') . ' ORDER BY clientid, type');
	}
	
	static function Add($clientid, $type, $data) {
		$result = DB::ValidatedInsert('metadata', array(
			'clientid' => $clientid,
			'type' => $type,
			'data' => $data
		));
		
		if (!is_string($result)) {
			Model::Load('history');
			$history = mHistory::Add('Client Metadata', $clientid, $type, 'Add', 'Added \'' . $type . '\' to ' . $clientid, AC::GetLoggedInUser());
			if (is_string($history)) Controller::ShowError('db_error', array('errorin' => 'mMetadata::Add (History)', 'errormessage' => $history));
		}
		
		return $result;
	}
	
	static function Update($clientid, $type, $data) {
		$result = DB::ValidatedUpdate('metadata', array('clientid', 'type'), array(
			'clientid' => $clientid,
			'type' => $type,
			'data' => $data
		));
		
		if (!is_string($result)) {
			Model::Load('history');
			$history = mHistory::Add('Client Metadata', $clientid, $type, 'Update', 'Updated \'' . $type . '\' for ' . $clientid, AC::GetLoggedInUser());
			if (is_string($history)) Controller::ShowError('db_error', array('errorin' => 'mMetadata::Update (History)', 'errormessage' => $history));
		}
		
		return $result;
	}
	
	static function Remove($clientid, $type) {
		$result = DB::Update('DELETE FROM metadata WHERE clientid = ' . DB::Quote($clientid) . ' AND type = ' . DB::Quote($type));
		
		if (!is_string($result)) {
			Model::Load('history');
			$history = mHistory::Add('Client Metadata', $clientid, $type, 'Remove', 'Removed \'' . $type . '\' from ' . $clientid, AC::GetLoggedInUser());
			if (is_string($history)) Controller::ShowError('db_error', array('errorin' => 'mMetadata::Remove (History)', 'errormessage' => $history));
		}
		
		return $result;
	}
}
?>