<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

class AdminController extends AppController {
	
	protected $loadControllerModel = false;
	
	function index() {
		$this->setData('selectednavi', $ref = 'Admin');
	}
	
}
?>