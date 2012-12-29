<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

class BookmarksController extends AppController {
	
	function add() {
		if (count($this->arguments) < 2) {
			Controller::ShowError('missing_arguments');
		}
		
		$this->pageTitle = 'Add Bookmark';
		
		$bookmarkArguments = $this->arguments;
		$this->setData('bookmarkController', $bookmarkController = array_shift($bookmarkArguments));
		$this->setData('bookmarkAction', $bookmarkAction = array_shift($bookmarkArguments));
		$this->setData('bookmarkArguments', $bookmarkArguments);
		
		$errors = array();
		
		V::Set($_GET['qs'], $_GET['qs'], 'array', array());
		V::Set($_GET['name'], $_GET['name'], 'table:bookmarks:name', '');
		
		$this->setData('testlink', $testlink = HTML::URL($bookmarkController, $bookmarkAction, $bookmarkArguments, $_GET['qs']));
		$this->setData('buttontext', $buttontext = 'Add Bookmark');
		
		if ($_GET['name'] != '') {
			$result =& mBookmark::Get(AC::GetLoggedInUser(), $_GET['name']);
			if (is_string($result)) {
				Controller::ShowError('db_error', array('errorin' => 'bookmarks/add', 'errormessage' => $result));
			}
			elseif (count($result) > 0) {
				array_push($errors, 'A bookmark with that name already exists.');
			}
			else {
				$result = mBookmark::Create(AC::GetLoggedInUser(), $_GET['name'], $bookmarkController, $bookmarkAction, $bookmarkArguments, $_GET['qs']);
				if (is_string($result)) {
					Controller::ShowError('db_error', array('errorin' => 'bookmarks/add', 'errormessage' => $result));
				}
				
				HTML::Redirect('home', 'index');
			}
		}
		
		$this->setData('errors', $errors);
	}
	
}