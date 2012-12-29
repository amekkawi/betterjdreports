<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

class AppController extends Controller
{
	function PreAction() {
		
		// General Javascript Includes
		Controller::includeJavascript(array(
			// jQuery
			'js/jquery/jquery.js',
		
			// General extensions
			'js/general.js',
		
			// jQuery UI
			'js/jquery/ui/jquery.ui.core.js',
			'js/jquery/ui/jquery.ui.widget.js',
			'js/jquery/ui/jquery.ui.mouse.js',
			'js/jquery/ui/jquery.ui.position.js'
		));
		
		// General CSS Includes
		Controller::includeCSS(array(
			
		));
		
		if (is_null(AC::GetLoggedInUser())) {
			if (isset($_POST['ac_userid']) && isset($_POST['ac_password'])) {
				
				Model::Load('user');
				if (mUser::Authorize(strtolower($_POST['ac_userid']), $_POST['ac_password'])) {
					AC::Login(strtolower($_POST['ac_userid']), mUser::GetGroupIDs(strtolower($_POST['ac_userid'])));
					
					$userinfo =& mUser::Get(AC::GetLoggedInUser());
					
					if (is_string($userinfo)) {
						Controller::ShowError('db_error', array('errorin' => 'AppController->PreAction', 'errormessage' => $userinfo));
					}
					
					Session::Set('userinfo', array('name' => $userinfo[0]['name'], 'timezone' => $userinfo[0]['timezone']));
					
					// Redirect the user to avoid POSTing when clicking the back button.
					HTML::Redirect(
						$this->action == "logout"
							? HTML::URL('home', 'index')
							: HTML::URL());
				}
				else {
					Controller::SetData('loginmessage', $msg = 'Invalid username and/or password.');
					Controller::ShowPage('login');
				}
			}
			else {
				Controller::ShowPage('login');
			}
			
			return FALSE;
		}
		else {
			Session::RefreshExpiration();
		}
	}
	
	function FormatBytes($number) {
		$number = intval($number);
		if ($number > 1024 * 1024 * 1024) {
			return round($number / 1024 / 1024 / 1024, 2) . " GB";
		}
		elseif ($number > 1024 * 1024) {
			return round($number / 1024 / 1024, 2) . " MB";
		}
		elseif ($number > 1024) {
			return round($number / 1024, 2) . " KB";
		}
		else {
			return $number . " bytes";
		}
	}
	
	function Pager(&$result) {
		$totalrecords = $result['total'];
		$totalpages = $result['totalpages'];
		
		if ($totalpages > 1) {
			echo '<table align="center" width="100%" class="pager" border="0" cellpadding="0" cellspacing="0"><tr>';
			echo '<td width="1%" nowrap="nowrap" class="pager-prev">' . ($result['page'] <= 1 ? '<span>&lt;&lt; Prev Page</span>' : '<a href="'.HTML::URL(array_merge($_GET, array('page'=>$result['page'] - 1))).'">&lt;&lt; Prev Page</a>') . '</td>';
			echo '<td width="98%"><table align="center" border="0" cellpadding="0" cellspacing="0"><tr>';
			
			echo '<td><span>Showing ' . number_format(intval($result['rowfrom'])) . ' to ' . number_format(intval($result['rowto'])) . ' of ' . number_format(intval($result['total'])) . ':</span></td>';
			
			if (max(1, $result['page'] - 5) > 1) {
				echo '<td><a href="'.HTML::URL(array_merge($_GET, array('page'=>1))).'">1</a></td><td style="padding: 4px">...</td>';
			}
			
			for ($i = max(1, $result['page'] - 5); $i <= min($totalpages, $result['page'] + 5); $i++) {
				if ($i == $result['page']) {
					echo '<td><b>'.$i.'</b></td>';
				}
				else {
					echo '<td><a href="'.HTML::URL(array_merge($_GET, array('page'=>$i))).'">'.$i.'</a></td>';
				}
			}
			
			if (min($totalpages, $result['page'] + 5) < $totalpages) {
				echo '<td style="padding: 4px">...</td><td><a href="'.HTML::URL(array_merge($_GET, array('page'=>$totalpages))).'">'.$totalpages.'</a></td>';
			}
			
			echo '</tr></table></td>';
			echo '<td width="1%" nowrap="nowrap" align="right" class="pager-next">' . ($result['page'] >= $totalpages ? '<span>Next Page &gt;&gt;</span>' : '<a href="'.HTML::URL(array_merge($_GET, array('page'=>$result['page']+1))).'">Next Page &gt;&gt;</a>') . '</td>';
			echo '</tr></table>';
		}
	}
	
	var $operations = array(
		'Write',
		'DeleteFile',
		'DeleteDir',
		'CreateDir',
		'Read',
		'GetDirectoryListing',
		'CopyDir',
		'SetAttr',
		'ChkDsk',
		'ArchiveCleanup',
		'WriteCache',
		'StartUpdate',
		'WriteUpdate',
		'MoveFile',
		'MoveDir',
		'None',
		'BackupSearch',
		'Backup',
		'BackupCleanup',
		'Restore',
		'DeleteBucket',
		'UpgradeBucket',
		'OpenBackupDB',
		'UploadBackupDB',
		'CompressBackupDB',
		'SyncFiles',
		'DeleteVault',
		'BlockBackup',
		'BlockBackupUpload',
		'VerifyDB'
	);
	
	function FormatOperation($type) {
		return array_key_exists(intval($type) - 1, $this->operations) ? $this->operations[intval($type) - 1] : $type;
	}
	
	function FormatDuration($seconds) {
		$duration = intval($seconds);
		if ($duration > 60 * 60 * 24) {
			return '~' . number_format(round($duration / (60*60), 1), 1) . ' hrs';
		}
		else {
			$duration -= ($hours = floor($duration / (60*60))) * 60 * 60;
			$duration -= ($minutes = floor($duration / 60)) * 60;
			return ($hours > 0 ? $hours . ':' . ($minutes < 10 ? '0' : '') : '') . $minutes . ':' . ($duration < 10 ? '0' : '') . $duration;
		}
	}
	
	function BookmarkLink() {
		return HTML::URL('bookmarks', 'add', array_merge(array(strtolower($this->name), $this->action), $this->arguments), array('qs' => $_GET));
	}
	
	function OutputHiddenInputs($ignore = array(), $arr = NULL) {
		if (is_null($arr)) $arr = $_GET;
		if (!is_array($ignore)) $ignore = array($ignore);
		
		// Change all the ignores to lowercase.
		foreach ($ignore as $key => $value) {
			$ignore[$key] = strtolower($value);
		}
		
		// Flip the array for lookup.
		$ignore = array_flip($ignore);
		
		if (is_array($arr) && count($arr) > 0) {
			$exploded = explode('&', http_build_query($arr));
			for ($i = 0; $i < count($exploded); $i++) {
				$pair = explode('=', $exploded[$i]);
				if (count($pair) == 2 && !array_key_exists(strtolower($pair[0]), $ignore)) {
					echo '<input type="hidden" name="'.HTML::Encode(urldecode($pair[0])).'" value="'.HTML::Encode(urldecode($pair[1])).'" />';
				}
			}
		}
	}
	
	function FormatFilter($filter) {
		$parts = explode('-', $filter, 3);
		switch ($parts[0]) {
			case 'clientid': $column = 'Client ID'; break;
			case 'job': $column = 'Job'; break;
			case 'reporttime': $column = 'Report Time'; break;
			case 'duration': $column = 'Duration'; break;
			case 'uploadsize': $column = 'Uploaded Bytes'; break;
			case 'changedfiles': $column = 'Uploaded Files'; break;
			case 'result': $column = 'Result'; break;
			case 'errors': $column = 'Errors'; break;
			default: $column = $parts[0];
		}
		switch ($parts[1]) {
			case 'x': $flag = 'is exactly'; break;
			case 'c': $flag = 'contains'; break;
			case 's': $flag = 'starts with'; break;
			case 'e': $flag = 'ends with'; break;
			case 'g': $flag = 'is greater than'; break;
			case 'l': $flag = 'is less than'; break;
			case 'i': $flag = 'is not'; break;
			default: $flag = $parts[1];
		}
		
		return $column . ' ' . $flag . ' "' . $parts[2] . '"';
	}
}

?>