<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

class Session extends SessionCore {
	static function _RetrieveData() {
		Model::Load('sessionstorage');
		if (is_string(Session::$_data = mSessionstorage::Get(Session::ID()))) {
			Controller::ShowError('db_error', array('errormessage' => Session::$_data, 'errorin' => 'Session::_RetrieveData()'));
		}
		elseif (Session::$_data === FALSE) {
			AC::Logout();
			Controller::ShowError('invalid_session_data');
		}
	}
	
	static function _StoreData() {
		Model::Load('sessionstorage');
		if (is_string($result = mSessionstorage::Set(Session::ID(), Session::$_data, DB::Now(30 * 60)))) {
			Controller::ShowError('db_error', array('errormessage' => $result, 'errorin' => 'Session::_StoreData()'));
		}
	}
	
	static function RefreshExpiration() {
		Model::Load('sessionstorage');
		if (is_string($result = mSessionstorage::RefreshExpiration(Session::ID(), DB::Now(30 * 60)))) {
			Controller::ShowError('db_error', array('errormessage' => $result, 'errorin' => 'Session::_RefreshExpiration()'));
		}
	}
}
?>