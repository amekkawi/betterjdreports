<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

class HomeController extends AppController {
	function index() {
		
		Model::Load('bookmark');
		Model::Load('history');
		
		$this->pageTitle = null;
		$this->setData('selectednavi', $ref = 'Home');
		
		$this->setData('bookmarks', $bookmarks =& mBookmark::Get(AC::GetLoggedInUser()));
		if (is_string($bookmarks)) {
			Controller::ShowError('db_error', array('errorin' => 'home/index', 'errormessage' => $result));
		}
		
		$this->setData('history', $history =& mHistory::Search(array(
			'filters' => array(
				array('column' => 'userid', 'flag' => 'x', 'text' => AC::GetLoggedInUser())
			),
			'page' => 1,
			'limit' => 25
		)));
		if (is_string($history)) {
			Controller::ShowError('db_error', array('errorin' => 'home/index', 'errormessage' => $result));
		}
	}
}
?>