<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

/**
 * Items controller
 *
 */
class Income extends Authenticated{
	
	/**
	 * Adding new income
	 *
	 * @return void
	 */
	 public function newAction(){
		 View::renderTemplate('Income/new.html');
	 }
}